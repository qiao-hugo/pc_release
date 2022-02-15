<?php
class CustomerStatement_BasicAjax_Action extends Vtiger_BasicAjax_Action {
    public $stayPaymentWorkFlowSid = 2426467;  //代付款在线签收id
    function __construct() {
        parent::__construct();
        $this->exposeMethod('getservicecontractsinfo');
        $this->exposeMethod('getaccountnocheck');
        $this->exposeMethod('savesignimage');
        $this->exposeMethod('getImage');
        $this->exposeMethod('supplydata');

    }

    function checkPermission(Vtiger_Request $request) {
        return;
    }

    function getservicecontractsinfo(Vtiger_Request $request){
        $contractid = $request->get('record');
        $return = Staypayment_Record_Model::getaccinfoBYcontractid($contractid);
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($return);
        $response->emit();
    }

    public function process(Vtiger_Request $request) {
        $mode = $request->getMode();
        if(!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);
            return;
        }
    }

    function getaccountnocheck(Vtiger_Request $request){
        $staypaymentaccountno = $request->get('staypaymentaccountno');
        $staypaymentname =$request->get('staypaymentname');
        $return = ReceivedPayments_Record_Model::checkNoMatchReceivedPayment($staypaymentaccountno,$staypaymentname);
        $return = array('success'=>true,'msg'=>'','data'=>array(array(
            'paytitle'=>'xxxx',
            'paymentaccountno'=>"23232333",
            'unit_price'=>80,
            'reality_date'=>'2018-12-23'
        )));
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($return);
        $response->emit();
    }

    public function getImage(Vtiger_Request $request){
        $fileid=(int)base64_decode($request->get('filename'));
        if($fileid>0){
            global $adb;
            $result = $adb->pquery("SELECT * FROM vtiger_files WHERE attachmentsid=?", array($fileid));
            if($adb->num_rows($result)) {
                $fileDetails = $adb->query_result_rowdata($result);
                $filePath = $fileDetails['path'];
                $fileName = html_entity_decode($fileDetails['name'], ENT_QUOTES, vglobal('default_charset'));
                $t_fileName = base64_encode($fileName);
                $t_fileName = str_replace('/', '', $t_fileName);
                $savedFile = $fileDetails['attachmentsid']."_".$t_fileName;
                if(!file_exists($filePath.$savedFile)){
                    $savedFile = $fileDetails['attachmentsid']."_".$fileName;
                }
                $fileSize = filesize($filePath.$savedFile);
                $fileSize = $fileSize + ($fileSize % 1024);
                if (fopen($filePath.$savedFile, "r")) {
                    $fileContent = fread(fopen($filePath.$savedFile, "r"), $fileSize);
                    header("Content-type: ".$fileDetails['type']);
                    header("Pragma: public");
                    header("Cache-Control: private");
//					header("Content-Disposition: attachment; filename=$fileName");
                    header('Content-Disposition: attachment; filename="'.$fileName.'"');
                    header("Content-Description: PHP Generated Data");
                }

                echo $fileContent;
            }else{
                echo 'no file exist';
            }
        }
    }

    public function savesignimage(Vtiger_Request $request){
        $imgstring=$request->get('image');
        $recordId = $request->get('record');//合同的id
        $recordModel=Vtiger_Record_Model::getInstanceById($recordId,'Staypayment');
        $id = $request->get('id');
        $newrecordid=base64_encode($recordId);
        global $root_directory,$current_user;

        $invoiceimagepath = $invoiceimagepath='/storage/staypayment/';
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
        $sql = 'INSERT INTO `vtiger_newinvoicesign`(invoiceid,path,`name`,deleted,setype,createdtime,smcreatorid) VALUES(?,?,?,0,?,?,?)';
        $db->pquery($sql,array($recordId,$newimagepath,$newrecordid,'Staypayment',$datetime,$id));
        if ($db->getLastInsertID()<1) {
            //如果不成功则删除添加的图片
            unlink($root_directory.$newimagepath);
        }

        //签完名更改代付款状态为完成
        $recordModel->set('id', $recordId);
        $recordModel->set('mode','edit');
        $recordModel->set('modulestatus','c_complete');
        $recordModel->save();

        //同步到合同附件列表
        $servicecontractsid = $recordModel->get('contractid');

        $fileId = $recordModel->get('file');
        $newFile = explode('##',$fileId);
        $sql2 = "update vtiger_files set description='ServiceContracts',relationid=?,style='files_style1',deliversuserid=?,delivertime=?,filestate='filestate2' where attachmentsid=?";
        $db->pquery($sql2,array($servicecontractsid,$current_user->id,date('Y-m-d H:i:s'),$newFile[1]));

        $serviceRecordModel = Vtiger_Record_Model::getInstanceById($servicecontractsid,'ServiceContracts');
        $comparesql = '';
        if($serviceRecordModel->get('total')>=$recordModel->get('staypaymentjine')){
            $comparesql = ',isclose=1';
        }
        //节点自动审批
        $db->pquery("UPDATE vtiger_staypayment SET workflowsnode=(SELECT vtiger_salesorderworkflowstages.workflowstagesname FROM `vtiger_salesorderworkflowstages` WHERE vtiger_salesorderworkflowstages.isaction=1 AND vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename='Staypayment' LIMIT 1),returnid=?".$comparesql." WHERE staypaymentid=?", array($recordId,$id, $recordId));
        $params['salesorderid'] = $request->get('record');


        $updateSql = " UPDATE  vtiger_salesorderworkflowstages SET modulestatus=?,isaction=2,auditorid=?,auditortime=?,schedule=100 WHERE salesorderid = ?  AND workflowsid =?";
        $db->pquery($updateSql,array('c_complete',$current_user->id,date("Y-m-d H:i:s"),$recordId,$this->stayPaymentWorkFlowSid));

        $object = new SalesorderWorkflowStages_SaveAjax_Action();
        $object->sendWxRemind(array('salesorderid'=>$recordId,'salesorderworkflowstagesid'=>0));

        $data='';
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    function supplydata(Vtiger_Request $request){
        $record = $request->get('record');
        $field = $request->get("field");
        $value = trim($request->get("value"));
        $value = str_replace("　",' ',$value);
        $value = preg_replace("/^[\s\v".chr(227).chr(128)."]+/","", $value); //替换开头空字符
        $value = rtrim($value);
        $return = array('success'=>false);
        $response = new Vtiger_Response();
        if(!$record || !in_array($field,array('currencytype','staypaymentname','staypaymentjine','startdate','enddate')) || !$value){
            $response->setEmitType(Vtiger_Response::$EMIT_JSON);
            $response->setResult($return);
            $response->emit();
            return;
        }
        global $adb;
        $recordModel = Staypayment_Record_Model::getInstanceById($record,'Staypayment');
        if($recordModel->get('staypaymenttype')=='fixation' && $field=='staypaymentjine'){
            $sql = "update vtiger_staypayment set {$field} = ?,surplusmoney=? where staypaymentid=?";
            $res = $adb->pquery($sql,array($value,$value,$record));
        }else{
            $sql = "update vtiger_staypayment set {$field} = ? where staypaymentid=?";
            $res = $adb->pquery($sql,array($value,$record));
        }
        $return = array('success'=>true);
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($return);
        $response->emit();
    }
}
