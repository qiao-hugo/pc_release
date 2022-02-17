<?php
/*+********
 *客户信息管理
 **********/

class Staypayment_Record_Model extends Vtiger_Record_Model {
    static function createStaypayment($fake_request){
        $db=PearDatabase::getInstance();
        //$result=$db->run_query_allrecords('');
        $ressorder=new Vtiger_Save_Action();
        $ressorder->saveRecord($fake_request);
        //$crmid=$db->getUniqueID('vtiger_crmentity');求表ID当前最大的
    }

    static function getaccinfoBYcontractid($contractid){
        $adb=PearDatabase::getInstance();
        $query="SELECT * FROM vtiger_crmentity WHERE crmid=? limit 1";
        $result=$adb->pquery($query,array($contractid));
        $resultdata=$adb->query_result_rowdata($result,0);
        if($resultdata['setype']=='ServiceContracts'){
            $sql = "SELECT accountid, accountname,effectivetime,companycode FROM vtiger_servicecontracts INNER JOIN vtiger_account ON sc_related_to = accountid WHERE servicecontractsid =? limit 1";
        }else{
            $sql = "SELECT vtiger_suppliercontracts.vendorid AS  accountid,vtiger_vendor.vendorname AS accountname,effectivetime,companycode FROM vtiger_suppliercontracts INNER JOIN vtiger_vendor ON vtiger_suppliercontracts.vendorid = vtiger_vendor.vendorid WHERE vtiger_suppliercontracts.suppliercontractsid =? limit 1";
        }

        $result = $adb->pquery($sql,array($contractid));
        if($adb->num_rows($result)>0){
            $temp = $adb->query_result_rowdata($result,0);
        }else{
            $temp = array();
        }
        return $temp;
    }

    /**
     * steel 2015-11-26
     * 是否已经签名
     * @throws
     */
    static public function checksign($recordid){
        $db=PearDatabase::getInstance();
        $result=$db->pquery("select 1 from vtiger_staypaymentsign where vtiger_staypaymentsign.setype='Staypayment' AND vtiger_staypaymentsign.staypaymentid=?",array($recordid));
        if($db->num_rows($result)>0){
            return false;
        }
        return true;
    }

    /**
     * 获取附件链接和附件
     */
    public function getFile($recordid){
        global $adb;
        $sql = "select file from vtiger_staypayment where staypaymentid=?";
        $result = $adb->pquery($sql,array($recordid));
        $file = '';
        if($adb->num_rows($result)){
            $row = $adb->query_result_rowdata($result,0);
            $file = $row['file'];
        }
        if(!$file){
            return array();
        }

        $files = explode("##",$file);
        $sql2 = "select name,path,type from vtiger_files where attachmentsid = ? ";
        $result2 = $adb->pquery($sql2,array($files[1]));
        if($adb->num_rows($result2)){
            $row2 = $adb->query_result_rowdata($result2,0);
            $path = $row2['path'];
            $name = $row2['name'];
            $type = explode('/',$row2['type']);
        }
        return array(
            '/'.$path.$name,
            strtolower($type[1])=='pdf'?'pdf':strtolower($type[0])
        );

    }

    /**
     *
     */
    public function sendWarningEmail($record,$reason=''){
        global $adb,$current_user;
        $recordModel = Vtiger_Record_Model::getInstanceById($record, 'Staypayment', TRUE);
        $contractType=$recordModel->entity->column_fields['modulename'];
        $sql = "select a.modulestatus,c.contract_no,d.accountname,a.staypaymentname,a.currencytype,b.createdtime,a.staypaymentjine,e.email1,a.staypaymenttype from vtiger_staypayment a 
  left join vtiger_crmentity b on a.staypaymentid=b.crmid 
  left join vtiger_servicecontracts c on a.contractid = c.servicecontractsid
  left join vtiger_account d on a.accountid = d.accountid
  left join vtiger_users e on e.id = b.smcreatorid
where a.staypaymentid = ?";
        if($contractType!='ServiceContracts'){
            $sql = "select a.modulestatus,c.contract_no,d.vendorname as accountname,a.staypaymentname,a.currencytype,b.createdtime,a.staypaymentjine,e.email1,a.staypaymenttype from vtiger_staypayment a 
  left join vtiger_crmentity b on a.staypaymentid=b.crmid 
  left join vtiger_suppliercontracts c on a.contractid = c.suppliercontractsid
  left join vtiger_vendor d on a.accountid = d.vendorid
  left join vtiger_users e on e.id = b.smcreatorid
where a.staypaymentid = ?";
        }
        $result = $adb->pquery($sql,array($record));
        if(!$adb->num_rows($result)){
            return;
        }
        $row = $adb->fetchByAssoc($result,0);


        $Subject = '代付款审核！！！';
        $str = '';
        $currentDate = date("Y-m-d H:i:s");
        switch ($row['modulestatus']){
            case 'c_complete':
                $str .= '同事你好！你于 '.$row['createdtime'].' 创建的代付款信息，已经于 '.$currentDate.' 被 '.$current_user->last_name.' 审核通过';
                $str .= '<br><br><br>';
                $str .= "<table style='border: 1px solid black;border-collapse: collapse'><tr><th style='border-right: 1px solid black'>合同编号</th>
                    <th  style='border-right: 1px solid black'>合同客户名称</th>
                    <th style='border-right: 1px solid black'>代付款客户</th>";
                if($row['staypaymenttype']=='fixation'){
                    $str .= "    <th style='border-right: 1px solid black'>币种</th>
                    <th style='border-right: 1px solid black'>代付款金额</th></tr>";
                }
                $str .= '<tr style=\'border: 1px solid black\'>
                        <td style=\'border-right: 1px solid black\'>' . $row['contract_no'] . '</td>
                        <td style=\'border-right: 1px solid black\'>' . $row['accountname'] . '</td>
                        <td style=\'border-right: 1px solid black\'>' . $row['staypaymentname'] . '</td>';
                if($row['staypaymenttype']=='fixation') {
                    $str .= '<td style=\'border-right: 1px solid black\'>' . $row['currencytype'] . '</td>
                        <td style=\'border-right: 1px solid black\'>' . $row['staypaymentjine'] . '</td>
                </tr>';
                }
                $str .= "</table><br>";
                break;
            case 'a_exception':
                $str .= '同事你好！你于 '.$row['createdtime'].' 创建的代付款信息，已经于 '.$currentDate.' 被 '.$current_user->last_name.' 打回,打回原因为:'.$reason;
                break;
        }
        if(!$str){
            return;
        }

        $address = $row['email1'];
        Vtiger_Record_Model::sendMail($Subject, $str, array(array('mail'=>$address, 'name'=>'')));
    }

    /**
     * 代付款审核微信消息
     *
     * @param $overdueDatas
     */
    public function sendWarningWx($record,$reason='')
    {
        $recordModel = Vtiger_Record_Model::getInstanceById($record, 'Staypayment', TRUE);
        $contractType=$recordModel->entity->column_fields['modulename'];
        $sql = "select a.modulestatus,c.contract_no,d.accountname,a.staypaymentname,a.currencytype,b.createdtime,a.staypaymentjine,e.email1,a.staypaymenttype from vtiger_staypayment a 
  left join vtiger_crmentity b on a.staypaymentid=b.crmid 
  left join vtiger_servicecontracts c on a.contractid = c.servicecontractsid
  left join vtiger_account d on a.accountid = d.accountid
  left join vtiger_users e on e.id = b.smcreatorid
where a.staypaymentid = ?";

        if($contractType!='ServiceContracts'){
            $sql = "select e.wechatid,a.modulestatus,c.contract_no,d.vendorname as accountname,a.staypaymentname,a.currencytype,b.createdtime,a.staypaymentjine,e.email1,a.staypaymenttype from vtiger_staypayment a 
  left join vtiger_crmentity b on a.staypaymentid=b.crmid 
  left join vtiger_suppliercontracts c on a.contractid = c.suppliercontractsid
  left join vtiger_vendor d on a.accountid = d.vendorid
  left join vtiger_users e on e.id = b.smcreatorid
where a.staypaymentid = ?";
        }

        global $adb,$current_user;

        $result = $adb->pquery($sql,array($record));
        if(!$adb->num_rows($result)){
            return;
        }
        $row = $adb->fetchByAssoc($result,0);
        $currentDate = date("Y-m-d H:i:s");
        $content = '';
        switch ($row['modulestatus']){
            case 'c_complete':
                $content .= '同事你好！你于 '.$row['createdtime'].' 创建的代付款信息，已经于 '.$currentDate.' 被 '.$current_user->last_name.' 审核通过<br>';
                $content .= '合同编号:'.$row['contract_no'].'<br>合同客户名称:'.$row['accountname'].'<br>代付款客户:'.$row['staypaymentname'];
                if($row['staypaymenttype']=='fixation'){
                    $content  .= '<br>代付款金额:'.$row['staypaymentjine'].'<br>币种:'.$row['currencytype'].'<br>';
                }
                break;
            case 'a_exception':
                $content .= '同事你好！你于 '.$row['createdtime'].' 创建的代付款(合同编号:'.$row['contract_no'].')，已经于 '.$currentDate.' 被 '.$current_user->last_name.' 打回<br>';
                $content .= '合同编号:'.$row['contract_no'].'<br>合同客户名称:'.$row['accountname'].'<br>打回原因:'.$reason.'<br>';
                break;
        }

        $this->sendWechatMessage(array('email'=>trim($row['email1']),'description'=>$content,'dataurl'=>'#','title'=>'【代付款消息提醒】','flag'=>7));
    }


    /**
     * 移动端根据用户id获取合同号
     * @param $request
     * @return array
     */
    public function getServiceNoById($request){
        global $current_user;
        $searchValue=$request->get('searchValue');
        $current_user->id=$request->get('userid');
        $userId=$request->get('userid');
        $listQuery=$this->getWhereQuery($userId);
        $adb=PearDatabase::getInstance();
        $sql="SELECT
	vtiger_crmentity.crmid AS suppliercontractsid,
	vtiger_crmentity.label AS contract_no
FROM
	vtiger_crmentity
	LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid = vtiger_crmentity.crmid 
WHERE
	vtiger_crmentity.deleted = 0 
	AND vtiger_crmentity.setype = 'ServiceContracts' 
	AND vtiger_servicecontracts.modulestatus = 'c_complete' 
	AND vtiger_crmentity.label LIKE '%".$searchValue."%' 
	".$listQuery."
	LIMIT 500 UNION ALL
SELECT
	vtiger_crmentity.crmid AS suppliercontractsid,
	vtiger_crmentity.label AS contract_no
FROM
	vtiger_crmentity
	LEFT JOIN vtiger_suppliercontracts ON vtiger_suppliercontracts.suppliercontractsid = vtiger_crmentity.crmid 
WHERE
	vtiger_crmentity.deleted = 0 
	AND vtiger_crmentity.setype = 'SupplierContracts' 
	AND vtiger_suppliercontracts.modulestatus = 'c_complete' 
	AND vtiger_crmentity.label LIKE '%".$searchValue."%' 
	LIMIT 0,
	20";
    $result = $adb->pquery($sql,array());
    $rowNo = $adb->num_rows($result);
    $serviceNo=array();
    if($rowNo) {
        while($row = $adb->fetch_array($result)) {
            $serviceNo[] = $row;
        }
    }
    return $serviceNo;
    }

    /**
     * 移动端根据id获取信息
     * @param $request
     */
    public function getServiceInfo($request){
        $contractid = $request->get('id');
        return Staypayment_Record_Model::getaccinfoBYcontractid($contractid);
    }

    /**
     * 移动端add
     * @param $request
     * @return mixed
     */
//    public function mobileSaveAdd($request){
//        $saveObject=new Staypayment_Save_Action();
//        $stayPaymentWorkFlowSid=$saveObject->stayPaymentWorkFlowSid;
//        $request->set('workFlowSid',$stayPaymentWorkFlowSid);
//        $id=$this->getMobileSaveAddModel($request);
//        if($id){
//            return  array('res'=>'success');
//        }
//        return  array('res'=>'fail');
//    }

    public function mobileSaveAdd($request){
        global $current_user,$isallow,$adb;
        $user = new Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile($request->get('userid'));
        $isallow=array('Staypayment');
        $saveObject=new Staypayment_Save_Action();
        $stayPaymentWorkFlowSid=$saveObject->stayPaymentWorkFlowSid;
        $request->set('workFlowSid',$stayPaymentWorkFlowSid);
        $fileStr=$this->getFileStr($request->get('attachmentsid'));
        $request->set('file',$fileStr);
        $sql="select companycode from vtiger_servicecontracts where servicecontractsid=?";
        $result=$adb->pquery($sql,array($request->get('contractid')));
        $request->set('companycode',$adb->query_result($result, 0, 'companycode'));
        $recordModel=$saveObject->saveRecord($request);
        $id=$recordModel->getId();
        if($id){
            return  array('res'=>'success');
        }
        return  array('res'=>$isallow);
    }


    /**
     * 获取model数据
     * @param $request
     */
    public function getMobileSaveAddModel($request){
        global $current_user;
        $adb=PearDatabase::getInstance();
        $current_user->id=$request->get('userid');
        $stayPaymentObject=new Staypayment();
        $stayPaymentObject->insertIntoCrmEntity('Staypayment','');
        $fileStr=$this->getFileStr($request->get('attachmentsid'));
        $id=$_REQUEST['currentid'];
        $contractType=$this->getContractType($request->get('contractid'));
        $sql="INSERT INTO `vtiger_staypayment` (
	`staypaymentid`,
	`contractid`,
	`accountid`,
	`staypaymenttype`,
	`createtime`,
	`createid`,
	`overdute`,
	`modulename`,
	`modulestatus`,
	`file`,
	`remark`,
	`workflowsid`,
	`workflowstime`,
	`workflowsnode`
)
VALUES
	(
		'".$id."',
		'".$request->get('contractid')."',
		'".$request->get('accountid')."',
		'".$request->get('staypaymenttype')."',
		'".date('Y-m-d H:i:s')."',
		'".$request->get('userid')."',
		'".$request->get('overdute')."',
		'".$contractType."',
		'a_normal',
		'".$fileStr."',
		'".$request->get('remark')."',
		'".$request->get('workFlowSid')."',
		'".date('Y-m-d H:i:s')."',
		'代付款线上签收'
	)
";
        $adb->query($sql,array());


        $sql="SELECT * FROM vtiger_workflowstages WHERE workflowsid =? ORDER BY sequence ASC";
        $wresult=$adb->pquery($sql,array($request->get('workFlowSid')));
        $serviceid=$adb->query_result($wresult,0,'workflowstagesid');
        $servicename=$adb->query_result($wresult,0,'workflowstagesname');
        $handleaction=$adb->query_result($wresult,0,'handleaction');
        $user=Users_Privileges_Model::getInstanceById($current_user->id);
        $sql="INSERT INTO `vtiger_salesorderworkflowstages` (
	`workflowstagesname`,
	`workflowstagesid`,
	`sequence`,
	`addtime`,
	`salesorderid`,
	`isaction`,
	`actiontime`,
	`workflowsid`,
	`modulename`,
	`smcreatorid`,
	`createdtime`,
	`productid`,
	`departmentid`,
	`higherid`,
	`modulestatus`,
	`accountid`,
	`accountname`,
	`salesorder_nono`,
	`handleaction` 	
)
VALUES
	(
		'".$servicename."',
		'".$serviceid."',
		1,
		'".date('Y-m-d H:i:s')."',
		'".$id."',
		1,
		'".date('Y-m-d H:i:s')."',
		'".$request->get('workFlowSid')."',
		'Staypayment',
		'".$request->get('userid')."',
		'".date('Y-m-d H:i:s')."',
		0,
		'".$user->get('current_user_parent_departments')."',
		0,
		'p_process',
		'".$request->get('accountid')."',
		'".$request->get('accountid_display')."',
		'".rtrim($request->get('contractid_display'),'-->[服务合同]')."',
		'".$handleaction."' 
	);";
        $adb->query($sql,array());

        $companyCode=$stayPaymentObject->getContractsCompanyCode('Staypayment',$request->get('contractid'));
        $query = "UPDATE vtiger_salesorderworkflowstages,
				 vtiger_staypayment
				SET vtiger_salesorderworkflowstages.accountid=vtiger_staypayment.accountid,
				     vtiger_salesorderworkflowstages.modulestatus='p_process',
				    vtiger_staypayment.modulestatus='b_check',
				    vtiger_salesorderworkflowstages.companycode=?
				WHERE vtiger_staypayment.staypaymentid=vtiger_salesorderworkflowstages.salesorderid
				AND vtiger_salesorderworkflowstages.salesorderid=? AND  vtiger_salesorderworkflowstages.workflowsid=? ";
        $adb->pquery($query, array($companyCode,$id,$request->get('workFlowSid')));
        //新建时 消息提醒第一审核人进行审核
        $object = new SalesorderWorkflowStages_SaveAjax_Action();
        $object->sendWxRemind(array('salesorderid'=>$id,'salesorderworkflowstagesid'=>0));
        return $id;
    }

    /*
     * 获取合同类型
     */
    public function getContractType($contractId){
        $contractType='ServiceContracts';
        $adb=PearDatabase::getInstance();
        $query="SELECT * FROM vtiger_crmentity WHERE crmid=? limit 1";
        $result=$adb->pquery($query,array($contractId));
        $resultdata=$adb->query_result_rowdata($result,0);
        if($resultdata['setype']=='SupplierContracts'){
            $contractType='SupplierContracts';
        }
        return $contractType;
    }

    /**
     *移动端图片上传
     * @param $request
     * @return array
     */
    public function doUploadImg($request){
        $model=$request->get('module');
        $file = $request->get('file');
        $name = $request->get('filename');
        $files = explode("base64,",$file);
        $filestream = $files[1];
        $size = strlen($filestream);
        if($name != '' && $size > 0){
            global $current_user;
            global $upload_badext;
            global $adb;
            $current_id = $adb->getUniqueID("vtiger_files");
            $file_name=preg_replace('/(\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;)+|(\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;)+/u','',$name);
            $binFile = sanitizeUploadFileName($file_name, $upload_badext);
            $uploadfile=time();
            $filename = ltrim(basename(" " . $binFile)); //allowed filename like UTF-8 characters
            $filetype = 'image/png';
            $upload_file_path = decideFilePath();
            file_put_contents($upload_file_path . $current_id . "_" .$uploadfile,base64_decode($filestream));
            if(!file_exists($upload_file_path . $current_id . "_" .$uploadfile)){
                return array('success'=>false,'result'=>array('id'=>$current_id,'name'=>$filename));
            }
            $sizeArray = getimagesize($upload_file_path . $current_id . "_" .$uploadfile);
            $sql2 = "insert into vtiger_files(attachmentsid, name,description, type, path,uploader,uploadtime,newfilename) values(?, ?,?, ?, ?,?,?,?)";
            $params2 = array($current_id, $filename, $model,$filetype, $upload_file_path,$current_user->id,date('Y-m-d H:i:s'),$uploadfile);
            $adb->pquery($sql2, $params2);
            return array('success'=>true,'result'=>array('id'=>$current_id,'name'=>$filename,'file'=>$upload_file_path . $current_id . "_" .$uploadfile,'width'=>$sizeArray[0],'hight'=>$sizeArray[1]));
        }else{
            return array('success'=>false,'msg'=>'上传失败','name'=>$files,'size'=>$filestream);
        }
    }

    /**
     *获得文件字符串
     * @param $fileStr
     * @return string
     */
    function  getFileStr($fileStr){
        $adb=PearDatabase::getInstance();
        $sql="select * from vtiger_files where attachmentsid in (".$fileStr.")";
        $result=$adb->pquery($sql,array());
        $fileArray=array();
        for ($i=0; $i<$adb->num_rows($result); ++$i) {
            $fileArray[]=$adb->fetchByAssoc($result);
        }
        $files='';
        foreach ($fileArray as $file){
            $fix=$file['name'].'##'.$file['attachmentsid'].'*|*';
            $files.=$fix;
        }
        $files=rtrim($files,'*|*');
        return $files;
    }


    function  getWhereQuery($userId){
		if(in_array($userId,array(1179,1734,7871,14758))){//黄玉琴，刘光翠,黄绥、张叶
            return '';
        }
        global $current_user,$adb;
        $user = new Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile($userId);
        $user=Users_Privileges_Model::getInstanceById($userId);
        $searchDepartment =$user->get('current_user_parent_departments');
        $listQuery='';
        $query='SELECT invoicecompany FROM vtiger_invoicecompanyuser WHERE modulename=\'ht\' AND  userid=?';
        $result=$adb->pquery($query,array($current_user->id));
        $companySql = '';
        if($adb->num_rows($result)){
            while ($row = $adb->fetchByAssoc($result)){
                $data[] = $row['invoicecompany'];
            }
            $companySql = " OR  vtiger_servicecontracts.companycode in('".implode("','",$data)."') ";
        }
        if(!empty($searchDepartment)&&$searchDepartment!='H1'){  //20150525 柳林刚 加入
            $userid=getDepartmentUser($searchDepartment);
            $where=getAccessibleUsers('ServiceContracts','List',true);
            if($where!='1=1'){
                $where=array_intersect($where,$userid);
            }else{
                $where=$userid;
            }
            $where=!empty($where)?$where:array(-1);
            $listQuery= ' AND (vtiger_crmentity.smownerid in ('.implode(',',$where).')'.$companySql.') ';
        }else{
            $where=getAccessibleUsers();
            if($where!='1=1'){
                $listQuery= ' AND (vtiger_crmentity.smownerid '.$where.$companySql.')';
            }
        }
//        file_put_contents(__DIR__.DIRECTORY_SEPARATOR.'aa.sql',$listQuery);
        return $listQuery;
    }

    public function getIdCardCheck($request){
        $idcard=$request->get('idcard');
        $payer=$request->get('payer');
        $userId=$request->get('userid');
        //先查我们的erp user数据库里有没有
        global $adb;
        $result=$adb->pquery('select id,last_name from vtiger_users where idcard=?',array($idcard));
        if($adb->num_rows($result)){
            //身份证号是内部员工的，不允许
            $data['flag']=false;
            $data['msg']='打款人身份证号存在于人员系统，无法提交';
        }else{
            //在user里查不到，去新的身份证库里查
            $result=$adb->pquery('select name from vtiger_idcard where idcard=?',array($idcard));
            $userName=$adb->query_result($result, 0, 'name');
            if($userName){
                if($payer!=$userName){
                    //库里所存的打款人姓名和身份证不符
                    $data['flag']=false;
                    $data['msg']='打款人姓名和身份证不符';
                }else{
                    //通过验证
                    $data['flag']=true;
                }
            }else{
                //我们库里没有存,先查库看有没有失败的再调用外来接口
                $sql="select id from vtiger_idcardlog where idcard=? and name=? and successorfail='fail'";
                $result=$adb->pquery($sql,array($idcard,$payer));
                if($adb->num_rows($result)){
                    //已经有失败的了，直接返回
                    $this->juHeLog(null,$idcard,$payer,$userId);
                    $data['flag']=false;
                    $data['msg']='打款人的信息不真实，无法提交，请提供真实的打款人姓名和身份证号';
                }else {
                    $serviceRecord = new ServiceContracts_Record_Model();
                    $verificationArray = $serviceRecord->realNameCheck(array('name' => $payer, 'identityNumber' => $idcard));
                    //接口调用日志
                    $responseArray = json_decode($verificationArray['response'], true);
                    $this->juHeLog($verificationArray, $idcard, $payer, $userId);
                    if ($responseArray['code'] && $responseArray['code'] == '10000') {
                        if ($responseArray['data']['result'] == 1) {
                            //通过验证,把数据整到数据库
                            $idcardArray['idcard'] = $idcard;
                            $idcardArray['name'] = $payer;
                            $adb->run_insert_data('vtiger_idcard', $idcardArray);
                            $data['flag'] = true;
                        } else {
                            $data['flag'] = false;
                            $data['msg'] = '打款人信息不真实，无法提交，请提供真实的打款人姓名和身份证号';
                        }
                    } else {
                        $data['flag'] = false;
                        $data['msg'] = '验证失败';
                    }
                }
            }
        }
        return $data;
    }

    /**
     * 记录调用接口日志
     * @param $verificationArray
     * @param $idcard
     * @param $payer
     * @param $record
     */
    public function juHeLog($verificationArray,$idcard,$payer,$userId){
        global $adb;
        $insert['creatid']=$userId;
        $insert['createdtime']=date('Y-m-d H:i:s');
        $insert['requestjson']=$verificationArray['request'];
        $insert['responsejson']=$verificationArray['response'];
        $responseArray=json_decode($verificationArray['response'],true);
        $insert['successorfail']='fail';
        $responseArray['code']&&$responseArray['code']=='10000'&&$responseArray['data']['result']==1&&$insert['successorfail']='success';
        $insert['idcard']=$idcard;
        $insert['name']=$payer;
        $insert['source']='app';
        $adb->run_insert_data('vtiger_idcardlog',$insert);
    }

    /**
     * 获取最晚签收时间
     * @return false|string
            */
    static function getLastSignTime(){
        date_default_timezone_set('Asia/Shanghai');
        $month=date('n');
        $isThird=$month/3;
        $quarter=ceil($month/3);
        if($month>=10){
            $last_sign_time=date('Y-12-31 23:59:59');
        }else{
            $last_sign_time=date('Y-m-d 23:59:59',strtotime("-1 day",strtotime(date('Y-'.($quarter*3+1).'-01'))));
        }
        if(is_int($isThird)){
            //是每个季度末尾月
            $last_sign_time=date('Y-m-d 23:59:59',strtotime("+4 months -1 day",strtotime(date('Y-m'))));
        }
        Matchreceivements_Record_Model::recordLog($last_sign_time.'最晚签收时间');
        return $last_sign_time;
    }

    function mobileDetail(Vtiger_Request $request) {
        $userid = $request->get("userid");
        $id = $request->get("id");
        global $adb,$current_user;
        $user = new Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
        //工单详情信息
        $sql = "SELECT
  a.staypaymentid,
	b.contract_no ,
	c.accountname,
	a.staypaymenttype,
	a.startdate,
	a.enddate,
	a.currencytype,
	a.staypaymentjine,
	a.surplusmoney,
	a.payertype,
	a.taxpayers_no,
	a.overdute,
	a.payer,
	a.idcard,
	a.explaintext,
	a.explainfile,
	a.file
FROM
	vtiger_staypayment a
	LEFT JOIN vtiger_servicecontracts b ON a.contractid = b.servicecontractsid
	LEFT JOIN vtiger_account c ON c.accountid = a.accountid 
	where a.staypaymentid=?";
        $sel_result = $adb->pquery($sql, array($id) );
        $res_cnt = $adb->num_rows($sel_result);
        $row = array();
        if($res_cnt > 0) {
            $lng = translateLng("Staypayment");
            $row=$adb->fetchByAssoc($sel_result,0);
            $row['staypaymenttype']=$lng[$row['staypaymenttype']];
            $row['payertype']=$lng[$row['payertype']];
            $rowfiles = explode("*|*",$row['file']);
            foreach ($rowfiles as $file){
                $filedata=explode("##",$file);
                $files[]=$filedata[1];
            }
            $rowexplainfiles = explode("*|*",$row['explainfile']);
            foreach ($rowexplainfiles as $rowexplainfile){
                $filedata=explode("##",$rowexplainfile);
                $explainfile[]=$filedata[1];
            }
        }
        $row['atta1']=array();
        $row['atta2']=array();
        if(count($files)){
            $query="SELECT attachmentsid,`name` FROM vtiger_files WHERE delflag=0 AND attachmentsid in ( ".implode(",",$files).")";
            $dataresult = $adb->pquery($query,array());
            $norows = $adb->num_rows($dataresult);
            $result = array();
            if($norows){
                while($resultrow = $adb->fetch_array($dataresult)) {
                    $result[]=$resultrow;
                }
            }
            $row['atta1']=$result;
        }
        if(count($explainfile)){
            $query="SELECT attachmentsid,`name` FROM vtiger_files WHERE delflag=0 AND attachmentsid in ( ".implode(",",$explainfile).")";
            $dataresult = $adb->pquery($query,array());
            $norows = $adb->num_rows($dataresult);
            $result = array();
            if($norows){
                while($resultrow = $adb->fetch_array($dataresult)) {
                    $result[]=$resultrow;
                }
            }
            $row['atta2']=$result;
        }


        $fieldname=array(
            'id' => $id,
            'module' => 'Staypayment',
            'record' => $id,
        );
        // 工作流
        $tt = $this->getWorkflowsMobile(new Vtiger_Request($fieldname, $fieldname));
        return array('Staypayment'=>$row, 'workflows'=>$tt);
    }
}
