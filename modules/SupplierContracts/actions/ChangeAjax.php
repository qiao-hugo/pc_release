<?php

class SupplierContracts_ChangeAjax_Action extends Vtiger_Action_Controller {
    private $nostandworkflowsid=793975;
    private $cancelworkflowsid=812777;
    public $purchaseWorkFlowSid;  //采购合同审核流
    public $costWorkFlowSid;     //费用合同审核流
    private $Kllcompanycode=array('KLL','WXKLL','KLLDSHJKJJT','GZKLL');//凯丽隆审核的合同主体公司
    public $TREASURER_TWO=array('ZD','DCL','ZDWL','YJSKJ','HKKLLGJ');//上海财务审核的合同主体公司
    private $Kllneedle='H283::';//凯丽隆部节门点
    private $WXKLLneedle='H349::';//无锡凯丽隆部节门点
    function __construct(){
        $recordModel = SupplierContracts_Record_Model::getCleanInstance("SupplierContracts");
        $this->costWorkFlowSid=$recordModel->costWorkFlowSid;
        $this->purchaseWorkFlowSid=$recordModel->purchaseWorkFlowSid;
        parent::__construct();
        $this->exposeMethod('makeWorkflowStages');
        $this->exposeMethod('savesignimage');
        $this->exposeMethod('getuserlist');
        $this->exposeMethod('changereceived');
        $this->exposeMethod('assignreceiptor');
        $this->exposeMethod('ContractCancel');
        $this->exposeMethod('CheckContractCancel');
        $this->exposeMethod('getSuppContractsAgreement');
        $this->exposeMethod('chuNaDoContractCancel');
        $this->exposeMethod('getCancelInfo');
        $this->exposeMethod('getSonCate');
        $this->exposeMethod('getPayApply');
    }

	function checkPermission(Vtiger_Request $request) {
		return true;
	}

	public function process(Vtiger_Request $request) {
       $mode=$request->getMode();
        if(!empty($mode)){
            echo $this->invokeExposedMethod($mode, $request);
            return;
        }
	}

    /**
     * 合同生成工作流
     * @param Vtiger_Request $request
     */
    public function makeWorkflowStages(Vtiger_Request $request)
    {
        $recordId=$request->get('recordid');
        $detailModel=Vtiger_DetailView_Model::getInstance('SupplierContracts',$recordId);
        global $current_user,$adb;
        $recordModel=$detailModel->getRecord();
        if($recordModel->entity->column_fields['modulestatus']=='a_normal'
            && $recordModel->entity->column_fields['assigned_user_id']== $current_user->id
        ){
//            $workFlowSid=$recordModel->entity->column_fields['workflowsid'];
            $soncateid=$recordModel->entity->column_fields['soncateid'];
            $frameworkcontract=$recordModel->entity->column_fields['frameworkcontract'];
            $total=$recordModel->entity->column_fields['total'];
            $filterWorkFlow = $recordModel->getFilterWorkFlow($soncateid,$total,$frameworkcontract);
            $userInfo=getUserInfo($current_user->id);
            $parentdepartment=$userInfo['parentdepartment'];
            $parentdepartment=explode('::',$parentdepartment);
            $parentdepartment=array_reverse($parentdepartment);
            $strtemp=' AND (';
            foreach($parentdepartment as $tempvalue){
                $strtemp.="FIND_IN_SET('".$tempvalue."',departmentid) OR ";
            }
            $strtemp=trim($strtemp,' OR ');
            $strtemp.=')';
            $query='SELECT 1 FROM `vtiger_filterworkflowstage` where sourceid=? AND deleted=0 '.$strtemp;
            if(!$adb->num_rows($adb->pquery($query,array($soncateid)))){
                $response = new Vtiger_Response();
                $response->setResult(array('falg'=>false,'msg'=>'请确认是否能够提相关项目的采购，如果可以，请联系相关人员设置审批流程'));
                $response->emit();
                exit;
            }
            $workFlowSid=$filterWorkFlow['workflowsid'];
            $_REQUEST['workflowsid']=$workFlowSid;

            $_POST['suppliercontractsstatus']=$recordModel->get('suppliercontractsstatus');
//            $_REQUEST['workflowsid']=$workFlowSid;
//            $_REQUEST['workflowsid']=$this->nostandworkflowsid;
            $focus = CRMEntity::getInstance('SupplierContracts');
            $result = $focus->db->pquery("SELECT vtiger_products.productname,vtiger_products.productid,vtiger_products.productman FROM vtiger_vendorsrebate LEFT JOIN vtiger_products ON vtiger_products.productid=vtiger_vendorsrebate.productid WHERE vtiger_vendorsrebate.suppliercontractsid=? AND vtiger_vendorsrebate.deleted=0", array($recordId));
            if($focus->db->num_rows($result)>0){
                $temproductid=array();
                while ($product = $focus->db->fetch_row($result)) {
                    if(!in_array($product['productid'],$temproductid)) {
                        $temproductid[] = $product['productid'];
                        $checkarray[] = array('workflowstagesname' => $product['productname'] . '审核', 'smcreatorid' => 0, 'productid' => $product['productid'], 'productman' => $product['productman']);
                    }
                }
                vglobal('checkproducts', $checkarray);
            }
            $focus->makeWorkflows('SupplierContracts', $_REQUEST['workflowsid'], $recordId,'edit',$filterWorkFlow['type'],$soncateid,$filterWorkFlow['ceocheck']);
            //$departmentid=empty($current_user->departmentid)?'H1':$current_user->departmentid;
            $departmentid=$_SESSION['userdepartmentid'];
            $focus->setAudituid('SupplierCAuditset',$departmentid,$recordId,$_REQUEST['workflowsid']);
            $focus->db->pquery("UPDATE vtiger_salesorderworkflowstages SET accountid=? WHERE salesorderid=?",array($recordModel->entity->column_fields['vendorid'],$recordId));
            //$focus->db->pquery("UPDATE vtiger_salesorderworkflowstages SET ishigher=1,higherid=(SELECT vtiger_auditsettings.oneaudituid FROM`vtiger_auditsettings` INNER JOIN (SELECT vtiger_departments.parentdepartment,vtiger_departments.departmentid FROM vtiger_departments WHERE vtiger_departments.departmentid='{$current_user->departmentid}') AS tempdepart ON FIND_IN_SET(vtiger_auditsettings.department,REPLACE(tempdepart.parentdepartment,'::',',')) LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_auditsettings.department  WHERE vtiger_auditsettings.auditsettingtype='SupplierCAuditset' ORDER BY abs(LENGTH(IFNULL(tempdepart.parentdepartment,0))-LENGTH(IFNULL(vtiger_departments.parentdepartment,0))) LIMIT 1) WHERE vtiger_salesorderworkflowstages.sequence=1 AND vtiger_salesorderworkflowstages.salesorderid=? AND isaction=1 AND vtiger_salesorderworkflowstages.modulename='SupplierContracts'",array($recordId));
            $focus->db->pquery("UPDATE vtiger_suppliercontracts SET modulestatus='b_check',workflowsid=? WHERE suppliercontractsid=?",array($workFlowSid,$recordId));
            /*$query='SELECT vtiger_invoicecompanyuser.userid FROM `vtiger_invoicecompanyuser` WHERE modulename=\'ht\' AND invoicecompany=?';
            $datacode=$focus->db->pquery($query,array($recordModel->entity->column_fields['companycode']));
            if($focus->db->num_rows($datacode)>0){
                $row = $focus->db->query_result_rowdata($datacode, 0);
                $focus->db->pquery("UPDATE vtiger_salesorderworkflowstages SET ishigher=1,higherid=? WHERE vtiger_salesorderworkflowstages.workflowstagesflag IN('DO_PRINT','CLOSE_WORKSTREAM') AND vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename='SupplierContracts'",array($row['userid'],$recordId));
            }*/
            if(in_array($recordModel->get('companycode'),$recordModel->Kllcompanycode)){
                $query='SELECT vtiger_departments.parentdepartment FROM vtiger_departments WHERE departmentid=? limit 1';
                $result=$focus->db->pquery($query,array($departmentid));
                $data=$focus->db->raw_query_result_rowdata($result,0);
                $parentdepartment=$data['parentdepartment'];
                $parentdepartment.='::';
                //$needle='H283::';
                if(strpos($parentdepartment,$recordModel->Kllneedle)!==false || strpos($parentdepartment,$recordModel->WXKLLneedle)!==false){
                    $updateSql="UPDATE vtiger_salesorderworkflowstages SET workflowstagesname='审核生成合同编号',ishigher = 1,higherid =? WHERE salesorderid =? AND workflowstagesflag='CREATE_CODE' AND workflowsid =?";
                    $focus->db->pquery($updateSql,array(11505,$recordId,$_REQUEST['workflowsid']));
                }
                //2021.06.03 把初级财务主管删掉
//                $deleteSql="DELETE FROM vtiger_salesorderworkflowstages WHERE salesorderid=? AND workflowstagesflag IN('TREASURER_ONE','TREASURER_TWO')";
                $deleteSql="DELETE FROM vtiger_salesorderworkflowstages WHERE salesorderid=? AND workflowstagesflag IN('TREASURER_ONE')";
                $focus->db->pquery($deleteSql,array($recordId));//删除财务主管的节点
            }elseif(in_array($recordModel->get('companycode'),$recordModel->TREASURER_TWO)){
                $deleteSql="DELETE FROM vtiger_salesorderworkflowstages WHERE salesorderid=? AND workflowstagesflag IN('TREASURER_ONE')";
                $focus->db->pquery($deleteSql,array($recordId));//删除财务主管的节点
            }else{
//                $deleteSql="DELETE FROM vtiger_salesorderworkflowstages WHERE salesorderid=? AND workflowstagesflag IN('TREASURER_TWO')";
//                $focus->db->pquery($deleteSql,array($recordId));//删除财务主管的节点
            }
            //新建时 消息提醒第一审核人进行审核
            $object = new SalesorderWorkflowStages_SaveAjax_Action();
            $object->sendWxRemind(array('salesorderid'=>$recordId,'salesorderworkflowstagesid'=>0));
	    if(!$recordModel->entity->column_fields['contract_no']){
                $this->createContractNo($recordModel->entity->column_fields,$recordId);
            }
            $response = new Vtiger_Response();
            $response->setResult(array('falg'=>true));
            $response->emit();
        }

    }
    /**
     * 在线签名的保存
     * @param Vtiger_Request $request
     */
    public function savesignimage(Vtiger_Request $request){
        $imgstring=$request->get('image');
        $recordId = $request->get('record');//合同的id
        $recordModel=Vtiger_Record_Model::getInstanceById($recordId,'SupplierContracts');
        $setype=$recordModel->checksign($recordId)?'SupplierContractOne':'SupplierContractTwo';
        $newrecordid=base64_encode($recordId);

        global $root_directory,$current_user;
        $invoiceimagepath='/storage/suppliercontracts/';
        include $root_directory.'modules'.DIRECTORY_SEPARATOR.'ServiceContracts'.DIRECTORY_SEPARATOR.'actions'.DIRECTORY_SEPARATOR.'circleSeal.class.php';

        $imagepath=$invoiceimagepath.date('Y').'/'.date('F').'/'.date('d').'/';
        //是否是目录不是则循环创建
        is_dir($root_directory.$imagepath) || mkdir($root_directory.$imagepath,0777,true);
        //文件相对保存的路径
        $newimagepath= $imagepath.$newrecordid.$setype.'.png';
        //以文档流方式创建文件

        $title='';
        if($setype=='SupplierContractOne'){
            $title='财务采购合同领用';
        }else{
            $title='财务采购合同归还';
        }
        $img=imagecreatefromstring(base64_decode(str_replace('data:image/png;base64,','',$imgstring)));
        $seal = new circleSeal($title,75,9,24,0,0,20,0);
        $img2=$seal->doImgNOut();
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
        imagecopy($other,$img2,$invoiceimagewidth-$invoiceimagewidth/4,$invoiceimageheight/3,0,0,150,150);
        //保存图片
        imagepng($other,$root_directory.$newimagepath);
        //释放资源
        imagedestroy($img);
        imagedestroy($img2);
        imagedestroy($other);

        $db=PearDatabase::getInstance();
        $sql = 'INSERT INTO `vtiger_invoicesign`(invoiceid,path,`name`,deleted,setype,createdtime,smcreatorid) VALUES(?,?,?,0,?,?,?)';
        $db->pquery($sql,array($recordId,$newimagepath,$newrecordid,$setype,$datetime,$current_user->id));
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
     * 指定代领人
     * @param Vtiger_Request $request
     */
    public function assignreceiptor(Vtiger_Request $request)
    {
        $recordId = $request->get('recordid');
        $userid = $request->get('userid');
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, 'SupplierContracts');
        $entity=$recordModel->entity->column_fields;
        $arr = array();
        global $current_user;
        if (($entity['modulestatus']=='a_normal' && $current_user->id==$entity['assigned_user_id']) || ($entity['modulestatus']=='c_stamp' && ($recordModel->checkCreator($recordId) || $current_user->is_admin=='on' || ($recordModel->get('assigned_user_id')== $current_user->id && $recordModel->get('sideagreement')==1)))) {
            $db = PearDatabase::getInstance();
            $datetime=date("Y-m-d H:i:s");
            $id = $db->getUniqueId('vtiger_modtracker_basic');
            $db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                array($id, $recordId, 'SupplierContracts', $current_user->id, $datetime, 0));
            $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                Array($id, 'receiptorid', $entity['receiptorid'], $userid));
            //$isstandard=$entity['modulestatus']=='a_normal'?"isstandard=1,":'';
            $isstandard='';
            $query = "UPDATE vtiger_suppliercontracts SET {$isstandard}receiptorid=? WHERE suppliercontractsid=?";
            $db->pquery($query, array($userid,$recordId));

        }

        $response = new Vtiger_Response();
        $response->setResult($arr);
        $response->emit();
    }
    /**
     * 验证合同是否可以作废
     */
    public function CheckContractCancel(Vtiger_Request $request){
        $recordId = $request->get('recordid');
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, 'SupplierContracts');
        $canContractCancel = $recordModel->canContractCancel();
        $response = new Vtiger_Response();
        $response->setResult($canContractCancel);
        $response->emit();
    }

    /**
     * 合同作废
     * @param Vtiger_Request $request
     *
     */
    public function ContractCancel(Vtiger_Request $request){
        $recordId = $request->get('recordid');
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, 'SupplierContracts');
        $entity=$recordModel->entity->column_fields;
        //$isFinance=$recordModel->getModule()->exportGrouprt('ServiceContracts','concancel');
        $arr=true;
        if(($entity['modulestatus'] == 'c_complete'|| $entity['modulestatus'] == 'c_receive')/* && $isFinance*/){
            global $current_user;
            $userid = $current_user->id;
            $remark = $request->get('remark');
            $pagenumber = $request->get('pagenumber');
            $reasoncan = $request->get('reasoncan');
            $yjmoney=$reasoncan=='losevoid'?300:$pagenumber;
            $db=PearDatabase::getInstance();
            $query='UPDATE vtiger_suppliercontracts SET cancelid=?,canceltime=?,cancelvoid=?,pagenumber=?,cancelmoney=?,cancelremark=?,modulestatus=?,backstatus=? WHERE suppliercontractsid=?';
            $db->pquery($query,array($userid,date('Y-m-d H:i:s'),$reasoncan,$pagenumber,$yjmoney,$remark,'c_cancelings',$entity['modulestatus'],$recordId));
            $moduleInstance=CRMEntity::getInstance('SupplierContracts');
            $_REQUEST['workflowsid']=$this->cancelworkflowsid;
            //$_REQUEST['workflowsid']=614638;//线上的ID
            $moduleInstance->makeWorkflows('SupplierContracts', $_REQUEST['workflowsid'], $recordId,false);
            //$db->pquery("UPDATE vtiger_salesorderworkflowstages SET ishigher=1,higherid=16 WHERE vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.workflowsid=? AND sequence=1 AND vtiger_salesorderworkflowstages.modulename='ServiceContracts'",array($recordId,$_REQUEST['workflowsid']));//更新作废审核人
        }
        $response = new Vtiger_Response();
        $response->setResult($arr);
        $response->emit();
    }
    /**
     * 取得当前的采购合同的补充协议
     * @param Vtiger_Request $request
     */
    public function getSuppContractsAgreement(Vtiger_Request $request)
    {
        $recordid=$request->get('recordid');
        $db=PearDatabase::getInstance();
        $query='SELECT vtiger_contractsagreement.newservicecontractsno FROM vtiger_contractsagreement LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_contractsagreement.contractsagreementid
                WHERE vtiger_crmentity.deleted=0
                AND vtiger_contractsagreement.servicecontractsid=?';
        $result=$db->pquery($query,array($recordid));
        $array=array();
        while($row=$db->fetch_array($result))
        {
            $array[]=array('contractsno'=>$row['newservicecontractsno']);
        }
        $num=count($array);
        echo json_encode(array('num'=>$num,'result'=>$array));
    }
    /**
     * 出纳审核
     * @param Vtiger_Request $request
     *
     */
    public function chuNaDoContractCancel(Vtiger_Request $request){
        $recordId = $request->get('recordid');
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, 'SupplierContracts');
        $entity=$recordModel->entity->column_fields;
        $isFinance=$recordModel->getModule()->exportGrouprt('SupplierContracts','concancel');
        $arr=true;
        if(($entity['modulestatus'] == 'c_cancelings') && $isFinance){
            global $current_user;
            $userid = $current_user->id;
            $remark = $request->get('remark');
            $pagenumber = $request->get('pagenumber');
            $souje = $request->get('souje');
            $soujbanhao = $request->get('soujbanhao');
            $reasoncan = $request->get('reasoncan');
            $yjmoney=$reasoncan=='losevoid'?300:$pagenumber;
            $db=PearDatabase::getInstance();
            $query='UPDATE vtiger_suppliercontracts SET cancelfeeid=?,docanceltime=?,cancelvoid=?,pagenumber=?,cancelmoney=?,cancelremark=?,modulestatus=?,accountsdue=?,receiptnumber=? WHERE suppliercontractsid=?';
            $db->pquery($query,array($userid,date('Y-m-d H:i:s'),$reasoncan,$pagenumber,$yjmoney,$remark,'c_canceling',$souje,$soujbanhao,$recordId));
            $db->pquery("UPDATE vtiger_salesorderworkflowstages SET ishigher=1,higherid=? WHERE vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.workflowsid=? AND sequence=1 AND vtiger_salesorderworkflowstages.modulename='SupplierContracts'",array($userid,$recordId,$this->cancelworkflowsid));//更新作废审核人
        }
        $response = new Vtiger_Response();
        $response->setResult($arr);
        $response->emit();
    }
    //获取用户名称,ID
    public function getCancelInfo(Vtiger_Request $request){
        $recordid=$request->get('recordid');
        $db=PearDatabase::getInstance();
        $query="SELECT
                    (SELECT CONCAT( last_name, '[', IFNULL( ( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ) ) ) AS last_name FROM vtiger_users WHERE cancelid = vtiger_users.id ) AS cancelname,
                    ifnull(canceltime,'') as canceltime,
                    ifnull(docanceltime,'') as docanceltime,
                    ifnull(cancelvoid,'') as cancelvoid,
                    ifnull(cancelfeeid,'') as cancelfeeid,
                    ifnull(cancelmoney,'') as cancelmoney,
                    ifnull(cancelremark,'') as cancelremark,
                    ifnull(pagenumber,'') as pagenumber,
                    ifnull(accountsdue,'') as accountsdue,
                    ifnull(receiptnumber,'') AS receiptnumber

                FROM
                    `vtiger_suppliercontracts`
                WHERE
                    suppliercontractsid =?";
        $result = $db->pquery($query, array($recordid));
        $response = new Vtiger_Response();
        $response->setResult($db->query_result_rowdata($result));
        $response->emit();

    }
    //获取用户名称,ID
    public function getSonCate(Vtiger_Request $request){
        $parentcate=$request->get('parentcate');
        $RecordModel = SupplierContracts_Record_Model::getCleanInstance("SupplierContracts");
        $data1 = $RecordModel->getSonCate($parentcate);
        $data['success']=true;
        $data['list']=$data1;
        echo json_encode($data);
    }

    //获取用户名称,ID
    public function getPayApply(Vtiger_Request $request){
        $parentcate=$request->get('parentcate');
        $soncate=$request->get('soncate');
        $record=$request->get('record');

        $payApplyRecordModel = PayApply_Record_Model::getCleanInstance("PayApply");
        $data1 = $payApplyRecordModel->getPayApply($record,$parentcate,$soncate);
        $data['success']=true;
        $data['list']=$data1;
        echo json_encode($data);
    }


    public function createContractNo($entity,$record){
        /*
    编码原则：
    业务类采购合同编码规则：GY(业务类供应商合同的简称）+各公司拼音简称(如无锡珍岛为WX)+ZD+签订年月+序列号（4位数）
    行政类采购合同编码规则：GX(行政类供应商合同的简称）+各公司拼音简称(如无锡珍岛为WX)+ZD+签订年月+序列号（4位数）
    框架合作协议：GC(框架合作协议的简称）+各公司拼音简称(如无锡珍岛为WX)+ZD+签订年月+序列号（4位数）
    保存时自动生成采购合同编号；修改采购公司或者合同类型不会修改采购合同编号。
    */
        $suppliercontractsstatus=$entity['suppliercontractsstatus'];
        $invoicecompany=$entity['invoicecompany'];
        $db=PearDatabase::getInstance();
        //生成合同编号
        $year=date('Y');
        $monthn=date('m');
        $day=date('d');
        //求合同主体的编码
        $query="SELECT company_codeno FROM `vtiger_company_code` WHERE companyfullname=? limit 1";
        $result=$db->pquery($query,array($invoicecompany));
        $company_codeno=$db->query_result($result,0,'company_codeno');
        $company_codeno=!empty($company_codeno)?$company_codeno:'ZD';
        $splitcontNO=explode('-',$entity['contract_no']);
        if(empty($entity['contract_no']) || $splitcontNO[0]!=$suppliercontractsstatus || $splitcontNO[1]!=$company_codeno) {
            $query = "SELECT suppliercontractsstatus,invoicecompany,meter FROM vtiger_suppliercontractsnodefect WHERE suppliercontractsstatus=? AND invoicecompany=? LIMIT 1";
            $result = $db->pquery($query, array($suppliercontractsstatus, $company_codeno));
            if ($db->num_rows($result)) {

                $meter = $db->query_result($result, 0, "meter");
                $db->pquery("DELETE FROM vtiger_suppliercontractsnodefect WHERE suppliercontractsstatus=? AND invoicecompany=? AND meter=?", array($suppliercontractsstatus, $company_codeno, $meter));
            } else {
                $query = "SELECT suppliercontractsstatus,invoicecompany,meter FROM vtiger_suppliercontractsnometer WHERE suppliercontractsstatus=? AND invoicecompany=? LIMIT 1";
                $result = $db->pquery($query, array($suppliercontractsstatus, $company_codeno));
                if ($db->num_rows($result)) {
                    $meter = $db->query_result($result, 0, "meter");
                    $meter = 1 + $meter;
                    $meter = str_pad($meter, 4, '0', STR_PAD_LEFT);
                } else {
                    $meter = '0001';
                }
                $db->pquery('REPLACE INTO vtiger_suppliercontractsnometer(suppliercontractsstatus,invoicecompany,meter) VALUES(?,?,?)', array($suppliercontractsstatus, $company_codeno, $meter));
            }
            $contract_no = $suppliercontractsstatus . '-' . $company_codeno . '-' . $year . $monthn . $day . $meter;
            if (!empty($entity['contract_no'])) {
                $db->pquery("INSERT INTO vtiger_suppliercontractsnodefect(suppliercontractsstatus,invoicecompany,meter) SELECT '{$splitcontNO[0]}','{$splitcontNO[1]}',meter FROM vtiger_suppliercontracts WHERE suppliercontractsid=?", array($record));
            }
            //取供应商的类型是行政采购GX还是业务采购GY
            //按供应商类型+合同主体+年份+月份+日期+序号生成合同编号
            $sql = "UPDATE vtiger_suppliercontracts SET contract_no=?,meter=? WHERE suppliercontractsid=?";
            $db->pquery($sql, array($contract_no,$meter, $record));
            $sql = "UPDATE vtiger_crmentity SET label=? WHERE crmid=?";
            $db->pquery($sql, array($contract_no, $record));
            $db->pquery(" UPDATE vtiger_salesorderworkflowstages SET vtiger_salesorderworkflowstages.salesorder_nono=? WHERE  vtiger_salesorderworkflowstages.salesorderid=? ",array($contract_no,$record));
        }
    }
}
