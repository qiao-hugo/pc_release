<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Invoice_BasicAjax_Action extends Vtiger_Action_Controller {
	function __construct() {
		parent::__construct();
		$this->exposeMethod('savesignimage');
        $this->exposeMethod('relatebilling');
        $this->exposeMethod('addRedInvoice');
        $this->exposeMethod('addCancel');
        $this->exposeMethod('addCancelFlag');
	}
	
	function checkPermission(Vtiger_Request $request) {
		return;
	}


    /**
     * 在线签名的保存
     * @param Vtiger_Request $request
     */
    public function savesignimage(Vtiger_Request $request){
        $imgstring=$request->get('image');
        $recordId = $request->get('record');//合同的id
        $recordModel=Vtiger_Record_Model::getInstanceById($recordId,'Invoice');
        //一张单只充许一个签名防止通过其它手段来提交数据
        if(!Users_Privileges_Model::isPermitted('Invoice', 'DuplicatesHandling', $recordId)||$recordModel->entity->column_fields['modulestatus']!='c_complete' || !Invoice_Record_Model::checksign($recordId)){
            $data='';
            $response = new Vtiger_Response();
            $response->setResult($data);
            $response->emit();
            exit;
        }
        $newrecordid=base64_encode($recordId);
        global $invoiceimagepath,$root_directory,$current_user;
        $imagepath=$invoiceimagepath.date('Y').'/'.date('F').'/'.date('d').'/';
        //是否是目录不是则循环创建
        is_dir($root_directory.$imagepath) || mkdir($root_directory.$imagepath,0777,true);
        //文件相对保存的路径
        $newimagepath= $imagepath.$newrecordid.'.png';
        //以文档流方式创建文件
        $img=imagecreatefromstring(base64_decode(str_replace('data:image/png;base64,','',$imgstring)));
        //取得图片的宽和高
        $invoiceimagewidth=imagesx($img);
        $invoiceimageheight=imagesy($img);
        //写入相对应的日期
        $textcolor = imagecolorallocate($img, 255, 0, 0);
        //$img若直接保存的话背影为黑色新建一个真彩图片背景为白色让两张图片合并$img为带a的通道
        $other=imagecreatetruecolor($invoiceimagewidth,$invoiceimageheight);
        $white=imagecolorallocate($img, 255, 255, 255);
        //$other 填充为白色
        imagefill($other,0,0,$white);
        $datetime=date('Y-m-d H:i');
        //将日期写入$img中
        imagestring($img,5,$invoiceimagewidth-200,$invoiceimageheight-60,$datetime,$textcolor);
        //合并图片
        imagecopy($other,$img,0,0,0,0,$invoiceimagewidth,$invoiceimageheight);
        //保存图片
        imagepng($other,$root_directory.$newimagepath);
        //释放资源
        imagedestroy($img);
        imagedestroy($other);
        $db=PearDatabase::getInstance();
        $sql = 'INSERT INTO `vtiger_invoicesign`(invoiceid,path,`name`,deleted,setype,createdtime,smcreatorid) VALUES(?,?,?,0,?,?,?)';
        $db->pquery($sql,array($recordId,$newimagepath,$newrecordid,'Invoice',$datetime,$current_user->id));
        if ($db->getLastInsertID()<1) {
            //如果不成功则删除添加的图片
            unlink($root_directory.$newimagepath);
        }
        $data='';
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * @param Vtiger_Request $request
     * @throws Exception
     */
	public function process(Vtiger_Request $request) {
		$mode = $request->getMode();
		if(!empty($mode)) {
			echo $this->invokeExposedMethod($mode, $request);
			return;
		}
	}
    /**
     * 关联开票信息
     * @param Vtiger_Request $request
     */
    public function relatebilling(Vtiger_Request $request){
        $recordid=$request->get('record');
        $db=PearDatabase::getInstance();
        $sql="UPDATE vtiger_invoice,
                 vtiger_billing
                SET vtiger_invoice.taxpayers_no = vtiger_billing.taxpayers_no,
                 vtiger_invoice.registeraddress = vtiger_billing.registeraddress,
                 vtiger_invoice.depositbank = vtiger_billing.depositbank,
                 vtiger_invoice.telephone = vtiger_billing.telephone,
                 vtiger_invoice.accountnumber = vtiger_billing.accountnumber
                WHERE vtiger_invoice.billingid=vtiger_billing.billingid AND vtiger_invoice.invoiceid=?";
        $db->pquery($sql,array($recordid));
        $data='';
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }
    /**
     *  添加红冲发问
     */
    public function addRedInvoice(Vtiger_Request $request){
        $datasave=$request->get('savedata');
        $recordId = $request->get('record');//合同的id
        $recordModel=Vtiger_Record_Model::getInstanceById($recordId,'Invoice');
        $db=PearDatabase::getInstance();
        $array=array('negativeinvoiceextendid','invoiceextendid','negativedrawerextend','negativebillingtimerextend','negativeinvoicecodeextend','negativeinvoice_noextend','negativebusinessnamesextend','negativetaxrateextend','negativecommoditynameextend','negativetotalandtaxextend','negativeremarkextend','negativeamountofmoneyextend','negativetaxextend');
        if(empty($datasave)){
            $data='数据不能为空';
            $response = new Vtiger_Response();
            $response->setResult($data);
            $response->emit();
            exit;
        }
        $temparr=array('negativetotalandtaxextend','negativetaxextend','negativeamountofmoneyextend');
        $insertname='';
        $insertvalue='';
        foreach($datasave as $value){
            if(in_array($value['name'],$array)){
                $insertname.='`'.$value['name'].'`,';
                if(in_array($value['name'],$temparr)){
                    $insertvalue.="'-".$value['value']."',";
                }else{
                    $insertvalue.="'".$value['value']."',";
                }
                if($value['name']=='invoiceextendid'){
                    $invoiceextendid=$value['value'];
                }
            }
        }

        global $current_user;
        $insertname.='`negativedrawerextend`';
        $insertvalue.=$current_user->id;

        if(!Invoice_Record_Model::exportGroupri()||!Users_Privileges_Model::isPermitted('Invoice', 'NegativeEdit', $recordId)||$recordModel->entity->column_fields['modulestatus']!='c_complete' || !Invoice_Record_Model::checkNegativeInvoice(array($recordId,$invoiceextendid,1))){
            $data='错误的操作';
            $response = new Vtiger_Response();
            $response->setResult($data);
            $response->emit();
            exit;
        }
        $sql="INSERT INTO vtiger_negativeinvoice({$insertname}) VALUES({$insertvalue})";
        $db->pquery($sql,array());
        $datetime=date('Y-m-d H:i:s');
        $sql="UPDATE vtiger_invoiceextend SET invoicestatus='redinvoice',processstatus=2,operator=?,operatortime=? WHERE invoiceid=? AND invoiceextendid=?";
        $db->pquery($sql,array($current_user->id,$datetime,$recordId,$invoiceextendid));
        $data='';
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
        exit;
    }
    public function addCancel(Vtiger_Request $request){
        $invoiceextendid=$request->get('invoiceextendid');
        $recordId = $request->get('record');//合同的id
        $recordModel=Vtiger_Record_Model::getInstanceById($recordId,'Invoice');
        $db=PearDatabase::getInstance();
        global $current_user;
        do{
            if(!Invoice_Record_Model::exportGroupri()||!Users_Privileges_Model::isPermitted('Invoice', 'ToVoid', $recordId)||$recordModel->entity->column_fields['modulestatus']!='c_complete' || !Invoice_Record_Model::checkNegativeInvoice(array($recordId,$invoiceextendid,1))){
                break;
            }
            $datetime=date('Y-m-d H:i:s');
            $sql="UPDATE vtiger_invoiceextend SET invoicestatus='tovoid',processstatus=2,operator=?,operatortime=? WHERE invoiceid=? AND invoiceextendid=?";
            $db->pquery($sql,array($current_user->id,$datetime,$recordId,$invoiceextendid));
        }while(0);

        $data='';
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
        exit;
    }
    //取消标记退款过程 对发票做一个标记如果标记为1则无法向下审核
    public function addCancelFlag(Vtiger_Request $request){
        $invoiceextendid=$request->get('invoiceextendid');
        $recordId = $request->get('record');//合同的id
        $recordModel=Vtiger_Record_Model::getInstanceById($recordId,'Invoice');
        $db=PearDatabase::getInstance();
        global $current_user;
        do{
            if(!Invoice_Record_Model::exportGroupri()||!Users_Privileges_Model::isPermitted('Invoice', 'ToVoid', $recordId)||!Users_Privileges_Model::isPermitted('Invoice', 'NegativeEdit', $recordId)||$recordModel->entity->column_fields['modulestatus']!='c_complete' || !Invoice_Record_Model::checkNegativeInvoice(array($recordId,$invoiceextendid,1))){
                break;
            }
            $sql="UPDATE vtiger_invoiceextend SET invoicestatus='normal',processstatus=0 WHERE invoiceid=? AND invoiceextendid=?";
            $db->pquery($sql,array($recordId,$invoiceextendid));
        }while(0);

        $data='';
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
        exit;
    }

}
