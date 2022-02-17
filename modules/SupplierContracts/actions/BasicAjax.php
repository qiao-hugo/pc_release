<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class SupplierContracts_BasicAjax_Action extends Vtiger_BasicAjax_Action {

    private $create_sign_one="CREATE_SIGN_ONE";
    private $create_sign_two="CREATE_SIGN_TWO";
	function __construct() {
		parent::__construct();
		$this->exposeMethod('inquireproduct');
		$this->exposeMethod('receivepay');
		$this->exposeMethod('getcurrencytype');
		$this->exposeMethod('getsmownerid');
		$this->exposeMethod('Received');
		$this->exposeMethod('Returned');
		$this->exposeMethod('NoSignReturned');
		$this->exposeMethod('NotSignInvalid');
		$this->exposeMethod('getservicecontractsinfo');
		$this->exposeMethod('add');
		$this->exposeMethod('addAuditsettings');
		$this->exposeMethod('deleted');
		$this->exposeMethod('setContractsStates');
		$this->exposeMethod('setContractsClose');
		$this->exposeMethod('files_deliver');
		$this->exposeMethod('files_delete');
		$this->exposeMethod('deletedAuditsettings');
		$this->exposeMethod('getSuppliercontracts');
		$this->exposeMethod('savesignimage');
		$this->exposeMethod('changeLead');//更换合同领取人
		$this->exposeMethod('saveChangeLeadSign');//更换合同领取人
		$this->exposeMethod('ContractCancel');//合同作废审请
		$this->exposeMethod('getQrcode');//获取2维码
		$this->exposeMethod('addsupplierstatus');//获取2维码
		$this->exposeMethod('deletedsupplierstatus');//获取2维码
		$this->exposeMethod('getVendorBankInfo');//获取供应商银行账户信息
		$this->exposeMethod('createVirtualNumber');//生成虚拟合同编号
		$this->exposeMethod('AddRStatement');//添加付款记录

	}


	function checkPermission(Vtiger_Request $request) {
		return;
	}

	// 根据供应商获取采购合同
	function getSuppliercontracts(Vtiger_Request $request) {
		$vendorid = $request->get('vendorid');
		$adb = PearDatabase::getInstance();
		$sql = "select suppliercontractsid,contract_no from vtiger_suppliercontracts where vendorid=?";
		$result = $adb->pquery($sql, array($vendorid));
		$row = $adb->num_rows($result);
		$suppliercontracts = array();
		if ($row > 0) {
			while($rawData=$adb->fetch_array($result)) {
				$suppliercontracts[] = $rawData;
        	}
		}
		$response = new Vtiger_Response();
	    $response->setResult($suppliercontracts);
	    $response->emit();
	}

	// 删除附件
	function files_delete(Vtiger_Request $request) {
		$fileid = $request->get('record');
		$suppliercontractsid = $request->get('srecorId');
		global $current_user;
        $recordModel=Vtiger_Record_Model::getInstanceById($suppliercontractsid,'SupplierContracts');
        $column_fields=$recordModel->entity->column_fields;
        $modulestatus=array('c_receive','c_complete','c_recovered');
        $dostatus=array('a_normal','a_exception');
        if(in_array($column_fields["modulestatus"],array('b_check','c_complete','b_actioning'))){
            $response = new Vtiger_Response();
            $response->setError(-1, '流程审核中或已完成，不允许删除附件');
            $response->emit();
            exit;
        }

//        if(in_array($column_fields['modulestatus'],$dostatus) || (in_array($column_fields['modulestatus'],$modulestatus)&& is_custompowers('scontractsFilesDelete')) )
//        {
            $sql = "update vtiger_files set deleter=?,delflag=1 where attachmentsid=?";
            $adb = PearDatabase::getInstance();
            $adb->pquery($sql, array($current_user->id, $fileid));
//        }
		$data = array();
		$response = new Vtiger_Response();
	    $response->setResult($data);
	    $response->emit();
	}

	// 附件签收
	function files_deliver(Vtiger_Request $request) {
		$servicecontractsid = $request->get('record');
		global $current_user;

		$sql = "update vtiger_files set deliversuserid=?,filestate=?, delivertime=? where attachmentsid=?";
		$adb = PearDatabase::getInstance();

		$data = array($current_user->id,'filestate2', date('Y-m-d H:i:s'), $servicecontractsid);
		$adb->pquery($sql, $data);

		$data = array();
		$data['last_name'] = $current_user->last_name."[". $current_user->department ."]";
		$data['delivertime'] = date('Y-m-d H:i:s');

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
	    //2015-1-23 wangbin 合并两次请求ajax
	    $recordId = $request->get('record');//合同的id
		$salesorderid = $request->get('salesorderid');//工单的id 编辑模式
	    $db=PearDatabase::getInstance();
		//查询合同下产品信息
		$sql = 'SELECT `productid`, `productform`, salesorderid, IFNULL(( SELECT vtiger_products.productname FROM vtiger_products WHERE vtiger_products.productid = vtiger_salesorderproductsrel.productcomboid ), \'--\' ) AS productcomboid FROM vtiger_salesorderproductsrel WHERE servicecontractsid =  ? AND (vtiger_salesorderproductsrel.multistatus=0 OR vtiger_salesorderproductsrel.multistatus=1)';
        $product = $db->pquery($sql,array($recordId));
	    $productids = $db->num_rows($product);
		//合同下得有产品//没有无产品的合同提工单
		$productidlist = array();
		if($productids>0){
		    while($row=$db->fetchByAssoc($product)){
				$productidlist[$row['productid']] = $row;
		    	//$module = Vtiger_Record_Model::getInstanceById($row['productid'],'Products');$productidlist[$i] = $module->getData();
		    	//$productidlist[$i]['solutions']= empty($row['salesorderid'])?$productidlist[$i]['notecontent']:$row['notecontent'];
				//$productidlist[$i]['productcomboid']= empty($row['productcomboid'])?'--':$row['productcomboid'];
		    }
		}
		$isEditForm=true;
		//编辑工单
		if($salesorderid){
			$productlist=$db->pquery('SELECT vtiger_salesorder_productdetail.FormInput,vtiger_salesorder_productdetail.TplId, vtiger_products.productname,vtiger_products.product_no, vtiger_products.productid, vtiger_formdesign.content_parse,vtiger_formdesign.field FROM vtiger_salesorder_productdetail LEFT JOIN vtiger_products ON vtiger_products.productid = vtiger_salesorder_productdetail.Productid LEFT JOIN vtiger_formdesign ON vtiger_formdesign.formid = vtiger_salesorder_productdetail.TplId WHERE vtiger_salesorder_productdetail.SalesOrderId =?',array($salesorderid));
			$rows=$db->num_rows($productlist);
			//旧数据只有模版
			if(!$rows){
				$isEditForm=false;
				$productlist=$db->pquery('SELECT vtiger_products.productname,vtiger_products.product_no, vtiger_products.productid from vtiger_products WHERE vtiger_products.productid in('.implode(',',array_keys($productidlist)).')');
				$rows=$db->num_rows($productlist);
			}
		}else{
			$productlist=$db->pquery('SELECT vtiger_customer_modulefields.formid as tplid,vtiger_products.productname,vtiger_products.product_no,vtiger_products.productid,vtiger_formdesign.content_parse,vtiger_formdesign.field FROM vtiger_customer_modulefields LEFT JOIN vtiger_products ON vtiger_products.productid = vtiger_customer_modulefields.relateid LEFT JOIN vtiger_formdesign ON vtiger_formdesign.formid = vtiger_customer_modulefields.formid WHERE vtiger_customer_modulefields.relateid IN ('.implode(',',array_keys($productidlist)).')');
			$rows=$db->num_rows($productlist);
			if(!$rows){
				//产品无表单
			}
		}
		//使用产品表单
		if($rows){
			$products=array();
			require 'include/utils/formparse.php';
			while($row=$db->fetchByAssoc($productlist)){
				if($isEditForm){
					$datas=json_decode(str_replace('&quot;','"',$row['field']),true);
					$values=(empty($row['forminput']))?array():json_decode(str_replace('&quot;','"',$row['forminput']),true);
					$productidlist[$row['productid']]['productform']=parse_toform($datas,$row['content_parse'],$values,$row['productid']);
					$productidlist[$row['productid']]['product_tplid']=$row['tplid'];
				}
				$productidlist[$row['productid']]['productname']=$row['productname'];
				$productidlist[$row['productid']]['product_no']=$row['product_no'];

			}
		}

	    //$product = $db->pquery('select `productid`,(select notecontent from vtiger_productcf where vtiger_productcf.productid=vtiger_salesorderproductsrel.productid) as notecontent from vtiger_salesorderproductsrel where servicecontractsid = ?',array($recordId));
	   /* $sql1="SELECT productname FROM vtiger_products WHERE productid=(select productcomboid FROM vtiger_servicecontracts WHERE servicecontractsid=?)";
        $package = $db->pquery($sql1,array($recordId))->fields['0'];//读取产品套餐*/
      /*   $package = array();
        $sql = 'SELECT `productid`, `productform` AS notecontent, salesorderid, IFNULL(( SELECT vtiger_products.productname FROM vtiger_products WHERE vtiger_products.productid = vtiger_salesorderproductsrel.productcomboid ), \'--\' ) AS productcomboid FROM vtiger_salesorderproductsrel WHERE servicecontractsid =  ?';
	    $product = $db->pquery($sql,array($recordId));
	    $li1 = $db->num_rows($product);
	    $productidlist = array();
		//echo $sql;
		//exit;
	    //young.yang 2015-1-29 修改原代码，简化
		if($li1>0){
			$i=0;
		    while($row=$db->fetch_row($product)){
		    	$module = Vtiger_Record_Model::getInstanceById($row['productid'],'Products');
		    	$productidlist[$i] = $module->getData();
		    	$productidlist[$i]['solutions']= empty($row['salesorderid'])?$productidlist[$i]['notecontent']:$row['notecontent'];
                $productidlist[$i]['productcomboid']= empty($row['productcomboid'])?'--':$row['productcomboid'];
		    	$i=$i+1;
		    }
		} */
	    //end
		//var_dump($productidlist);die;

		$moduleName = $request->get('module');
		$module = Vtiger_Record_Model::getInstanceById($recordId,'ServiceContracts');
		$result=array();
		$result=$module->getData();
		$accountsId=$result['sc_related_to'];
		//$smownerid=$result['assigned_user_id'];//合同领取人
		$smownerid=$result['Receiveid'];//2015年8月5日  wangbin  #1023 工单编辑负责人经常会变化
		$total=$result['total'];
        $remark = $result['remark'];
		$accountsModule=Vtiger_Record_Model::getInstanceById($accountsId,'Accounts');
		//合同数据
		$accounts=$accountsModule->getData();
		//合同回款记录
		$rp=ReceivedPayments_Record_Model::getAllReceivedPayments($recordId);
		$return=array('accountname'=>$accounts['accountname'],'id'=>$accounts['record_id'],'userid'=>$smownerid,'total'=>$total,'customerno'=>$accounts['account_no'],'rp'=>$rp,'remark'=>$remark);
		$datas=array($productidlist,$return,$package,$isEditForm);
		$response = new Vtiger_Response();
		$response->setResult($datas);
		$response->emit();
	}


    /**
     *
     * 合同领用
     * @param Vtiger_Request $request
     */
    public function Received(Vtiger_Request $request){
        $userid=$request->get('userid');
        $codenumber=$request->get('inputcode');
        $codenumber=trim($codenumber);
        $superconllar=$request->get('superconllar');
        $userqrcodeid=$request->get('userqrcodeid');
        $supermanid=$request->get('supermanid');
        $mod=Vtiger_Module_Model::getCleanInstance("SupplierContracts");
        $datas=array();
        do{
            //有没有权限操作
            if(!$mod->exportGrouprt('SupplierContracts','Received')) {
                $datas['msg']='没有权限';
                $datas['rstatus']='no_status';
                break;
            }
            $db=PearDatabase::getInstance();
            //根据公司员工编号0001-9999四位数字这里只判断为数字，长度没有做处理，合同编号不能为全数字
            if($userid==0||is_numeric($codenumber)){
                $datas=$this->checkuser($codenumber);
                break;
            }
            if($userid!=0 && ($supermanid==0 || is_numeric($codenumber)) && $superconllar==1){
                $datas=$this->checkuser($codenumber);
                if($datas['rstatus']!='no_status'){
                    $datas['rstatus']='superman_status';
                }else{
                    $datas['msg']='超领人--'.$datas['msg'];
                }
                break;
            }
            if(empty($codenumber)||empty($userid)){
                $datas['msg']='不正确的输入';
                $datas['rstatus']='no_status';
                break;
            }
            /*if(empty($userqrcodeid) || $userid!=$_SESSION['confirmSupplierReceived']){
                $datas['msg']='请先扫二维码确认!或领取人和扫码人不是同一人';
                $datas['rstatus']='no_status';
                break;
            }*/
            $codenumbertemp=$codenumber;
            $status=$this->checkcontractsno(array($codenumbertemp,1));
            if($status['rstatus']=='no_status'){
                $datas=$status;
                break;
            }
            $servicenum=$mod->servicecontracts_reviced($userid);
            //指定的用户不限合同份数
            if($servicenum && !$mod->exportGrouprt('ServiceContracts','Received',$userid)){
                $datas['msg']=$servicenum;
                $datas['rstatus']='super_status';
                break;
            }
            $tempfield='servicecontractsprintid';

            $query="SELECT vtiger_suppliercontracts.suppliercontractsid,modulestatus,vtiger_suppliercontracts.contract_no,vtiger_suppliercontracts.contractattribute FROM vtiger_suppliercontracts LEFT JOIN vtiger_crmentity ON vtiger_suppliercontracts.suppliercontractsid = vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted = 0 AND vtiger_suppliercontracts.contract_no=?";
            $result=$db->pquery($query,array($codenumbertemp));
            $tempfield='servicecontracts_no';

            $num=$db->num_rows($result);
            if($num==0){
                $datas['msg']='合同已领取,生成错误请手工录入';
                $datas['rstatus']='no_status';
                break;
            }elseif($num>1){
                $datas['msg']='多个重复的合同编号';
                $datas['rstatus']='no_status';
                break;
            }
            $rows = $db->raw_query_result_rowdata($result);
            if($rows['modulestatus']=='c_complete'){
                $datas['msg']='合同已签收';
                $datas['rstatus']='no_status';
                break;
            }
            if ($rows['modulestatus'] == 'c_cancel') {
                $datas['msg'] = '合同已作废';
                $datas['rstatus'] = 'no_status';
                break;
            }
            if ($rows['modulestatus'] != 'c_stamp') {
                $datas['msg'] = '只有状态为已盖章的合同才能进行操作';
                $datas['rstatus'] = 'no_status';
                break;
            }
            if($this->checkNostand($userid,$codenumbertemp))
            {
                $datas['msg'] = '合同领取人不是指定的代领人!';
                $datas['rstatus'] = 'no_status';
                break;
            }
            $signpath=$request->get('signpath');
            unset($_REQUEST);//防止信息干扰
            $_REQUEST['record']=$rows['suppliercontractsid'];
            $request=new Vtiger_Request($_REQUEST, $_REQUEST);
            $_REQUEST['action']='SaveAjax';
            if($rows['contractattribute']=='standard'){
                $request->set('receiptorid',$userid);
            }
            $request->set('assigned_user_id',$userid);
            $request->set('modulestatus', 'c_receive');
            $request->set('receivedate', date('Y-m-d'));
            $request->set('module','SupplierContracts');
            $request->set('view','Edit');
            $request->set('action','SaveAjax');
            $ressorder=new SupplierContracts_Save_Action();
            $ressorderecord=$ressorder->saveRecord($request);
            $this->dosignContracts(array('controllerType'=>'Received','userid'=>$userid,'inputcode'=>$codenumber,'signpath'=>$signpath,'userid'=>$userid,"recordId"=>$rows['suppliercontractsid'],'workflowstagesflag'=>$this->create_sign_one));
            $datetime=date('Y-m-d H:i:s');
            global $current_user;
            //$sql="UPDATE vtiger_servicecontracts_print SET constractsstatus='c_receive',receivedtime=?,receivedid=?,doreceivedid=? WHERE {$tempfield}=?";
            $db->pquery('UPDATE vtiger_suppliercontracts SET modulestatus=? WHERE suppliercontractsid=?',array('c_receive',$rows['suppliercontractsid']));
            //$db->pquery($sql,array($datetime,$userid,$current_user->id,$codenumbertemp));
            $datas['userid']=$userid;
            $datas['contractno']=$rows['contract_no'];
            $datas['rstatus']='contractok';
        }while(0);
        $response = new Vtiger_Response();
        $response->setResult($datas);
        $response->emit();
        exit;
    }

    /**
     * 查看工号信息
     * @param $codenumber int用户工号
     * @return mixed
     * @throws Exception
     */
    public function checkuser($codenumber){
        $db=PearDatabase::getInstance();
        //修改合同领用的工号比对,用户编号改为6码，原有的用户编号不足6码的，前面补0 2017/03/01 gaocl edit
        $new_codenumber=str_pad($codenumber, 6, '0', STR_PAD_LEFT);
        $query="SELECT vtiger_users.id,vtiger_users.usercode,vtiger_users.last_name,vtiger_users.`status`,vtiger_departments.departmentname FROM vtiger_users LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid WHERE vtiger_users.`status`='Active' AND (usercode=? OR usercode=?)";
        $result=$db->pquery($query,array($codenumber,$new_codenumber));
        $num=$db->num_rows($result);

        if($num<=0){
            $datas['msg']='不存在的工号';
            $datas['rstatus']='no_status';
        }else if($num==1){
            $rows=$db->query_result_rowdata($result);


            if($rows['status']!='Active'){
                $datas['msg']='该工号已离职!';
                $datas['rstatus']='no_status';
            }else{
                $userstatus=$rows['status']=='Active'?'':'[离职]';
                $datas['userid']=$rows['id'];
                $datas['ucode']=$rows['usercode'];
                $datas['username']=$rows['last_name'].'['.$rows['departmentname'].']'.$userstatus;
                $datas['rstatus']='userset';
            }
        }else{
            $datas['msg']='该工号有多个人使用';
            $datas['rstatus']='no_status';
        }
        return $datas;
    }

    /**
     * 该合同是否是非标合同如果是则判断当前领用是否是合同的申请人
     * @param $userid
     * @param $contractsql
     * @return bool
     *
     */
    public function checkNostand($userid,$codenumbertemp)
    {
        $db=PearDatabase::getInstance();
        //$query="SELECT smownerid FROM `vtiger_servicecontracts` WHERE nostand=1".$contractsql;
        $query="SELECT vtiger_suppliercontracts.receiptorid FROM vtiger_suppliercontracts LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_suppliercontracts.suppliercontractsid WHERE vtiger_crmentity.deleted=0 AND vtiger_suppliercontracts.contract_no=? AND vtiger_suppliercontracts.contractattribute='custommade'";
        $result=$db->pquery($query,array($codenumbertemp));
        $num=$db->num_rows($result);
        if($num){
            $row=$db->query_result_rowdata($result);
            if($row['receiptorid']==$userid)
            {
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * 查看合同编号表中是否可以使用
     * @param $codenumber array ,合同编号,状态1为领取,2归还
     * @return array
     * @throws Exception
     */
    public function checkcontractsno($codenumber){
        $db=PearDatabase::getInstance();
        $query="SELECT
                    vtiger_suppliercontracts.*
                FROM
                    vtiger_suppliercontracts
                LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_suppliercontracts.suppliercontractsid
                WHERE
                    vtiger_crmentity.deleted = 0
                AND vtiger_suppliercontracts.contract_no =?";
        $result=$db->pquery($query,array($codenumber[0]));
        $num=$db->num_rows($result);
        $datas=array('msg'=> '未知错误','rstatus'=>'no_status');
        do {
            if ($num <= 0) {
                $datas['msg'] = '不存在的合同编号';
                $datas['rstatus'] = 'no_status';
                break;
            }if ($num >1) {
                $datas['msg'] = '合同编号重复';
                $datas['rstatus'] = 'no_status';
                break;
            }
            $rows = $db->raw_query_result_rowdata($result);
            if ($rows['modulestatus'] == 'c_receive' && $codenumber[1]==1) {
                $datas['msg'] = '合同已领取';
                $datas['rstatus'] = 'no_status';
                break;
            }
            $status=array('c_recovered'=>'合同已收回',
                'c_generated'=>'合同未打印',
                'c_complete'=>'合同已签收',
                'c_complete'=>'合同已签收',
                'c_cancel'=>'合同已作废',
                'a_normal'=>'合同未审核',
                'a_exception'=>'合同打回中',
                'c_print'=>'合同未盖章',
                'b_check'=>'合同执行中',
                'b_actioning'=>'合同执行中',
                'c_canceling'=>'合同作废中',

                );
            if (in_array($rows['modulestatus'],array_keys($status))) {
                $datas['msg'] = $status[$rows['modulestatus']];
                $datas['rstatus'] = 'no_status';
                break;
            }
            if ($rows['modulestatus'] == 'c_stamp' && $codenumber[1]==2) {
                $datas['msg'] = '合同未领取';
                $datas['rstatus'] = 'no_status';
                break;
            }
            if ($rows['modulestatus'] == 'c_stamp') {
                $datas['sno'] = $rows['servicecontracts_no'];
                $datas['rstatus'] = 'OK';
                break;
            }
            if ($rows['modulestatus'] == 'c_receive' && $codenumber[1]==2) {
                $datas['sno'] = $rows['servicecontracts_no'];
                $datas['rstatus'] = 'OK';
                break;
            }
        }while(0);
        return $datas;
    }

    /**
     * 合同的归还
     * @param Vtiger_Request $request
     * @throws Exception
     */
    public function Returned(Vtiger_Request $request){
        $userid=$request->get('userid');
        $codenumber=$request->get('inputcode');
        $userqrcodeid=$request->get('userqrcodeid');
        $codenumber=trim($codenumber);
        $mod=Vtiger_Module_Model::getCleanInstance('SupplierContracts');
        $datas=array();
        do{
            if(!$mod->exportGrouprt('SupplierContracts','Received')) {
                $datas['msg']='没有权限';
                $datas['rstatus']='no_status';
                break;
            }
            $db=PearDatabase::getInstance();
            if($userid==0||is_numeric($codenumber)){
                $datas=$this->checkuser($codenumber);
                break;
            }
            if(empty($codenumber)||empty($userid)){
                $datas['msg']='不正确的输入';
                $datas['rstatus']='no_status';
                break;
            }
            /*if(empty($userqrcodeid) || $userid!=$_SESSION['confirmSupplierReturned']){
                $datas['msg']='请先扫二维码确认!或领取人和扫码人不是同一人';
                $datas['rstatus']='no_status';
                break;
            }*/

            $codenumbertemp=$codenumber;
            $status=$this->checkcontractsno(array($codenumbertemp,2));
            if($status['rstatus']=='no_status'){
                $datas=$status;
                break;
            }
            $query="SELECT vtiger_suppliercontracts.suppliercontractsid,modulestatus,vtiger_suppliercontracts.contract_no FROM vtiger_suppliercontracts LEFT JOIN vtiger_crmentity ON vtiger_suppliercontracts.suppliercontractsid = vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted = 0 AND vtiger_suppliercontracts.contract_no=?";
            $result=$db->pquery($query,array($codenumbertemp));
            $num=$db->num_rows($result);
            if($num==0){
                $datas['msg']='合同已领取,生成错误请手工录入';
                $datas['rstatus']='no_status';
                break;
            }elseif($num>1){
                $datas['msg']='多个重复的合同编号';
                $datas['rstatus']='no_status';
                break;
            }
            $rows = $db->raw_query_result_rowdata($result);
            if($rows['modulestatus']=='c_complete'){
                $datas['msg']='合同已签收';
                $datas['rstatus']='no_status';
                break;
            }
            if ($rows['modulestatus'] == 'c_cancel') {
                $datas['msg'] = '合同已作废';
                $datas['rstatus'] = 'no_status';
                break;
            }
            if ($rows['modulestatus'] != 'c_receive') {
                $datas['msg'] = '只有状态为已领取的采购合同才能进行操作';
                $datas['rstatus'] = 'no_status';
                break;
            }
            $signpath=$request->get('signpath');
            unset($_REQUEST);//防止信息干扰
            $_REQUES['record']=$rows['suppliercontractsid'];
            $request=new Vtiger_Request($_REQUES, $_REQUES);
            $request->set('modulestatus', 'c_recovered');
            $request->set('Returndate', date('Y-m-d'));
            $_REQUEST['action']='SaveAjax';
            $request->set('module','SupplierContracts');
            $request->set('view','Edit');
            $request->set('action','SaveAjax');
            $ressorder=new SupplierContracts_Save_Action();
            $ressorder->saveRecord($request);
            $datetime=date('Y-m-d H:i:s');
            global $current_user;
            $db->pquery('UPDATE vtiger_suppliercontracts SET modulestatus=? WHERE suppliercontractsid=?',array('c_recovered',$rows['suppliercontractsid']));
            $this->dosignContracts(array('controllerType'=>'Returned','userid'=>$userid,'inputcode'=>$codenumber,'signpath'=>$signpath,'userid'=>$userid,"recordId"=>$rows['suppliercontractsid'],'workflowstagesflag'=>$this->create_sign_two));

            $datas['userid']=$userid;
            $datas['contractno']=$rows['contract_no'];
            $datas['contractid']=$rows['suppliercontractsid'];
            $datas['rstatus']='contractok';
        }while(0);
        $response = new Vtiger_Response();
        $response->setResult($datas);
        $response->emit();
        exit;
    }
    /**
     * 合同的(未签合同)归还
     * @param Vtiger_Request $request
     * @throws Exception
     */
    public function NoSignReturned(Vtiger_Request $request){
        /**
         * 采购合同不能走
         */
        return ;
        $datas=array();
        do{
            $userid=$request->get('userid');
            $codenumber=$request->get('inputcode');
            if($userid==0||is_numeric($codenumber)){
                $datas=$this->checkuser($codenumber);
                break;
            }
            $receiveddata=$this->noSignCommon($request);
            if($receiveddata['rstatus']=='no_status'){
                $datas=$receiveddata;
                break;
            }

            $codenumbertemp=$receiveddata['codenumbertemp'];
            $contract_no=$receiveddata['rows']['contract_no'];
            $recordId=$receiveddata['rows']['suppliercontractsid'];
            //$moduleName = $request->getModule();
            //$recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
            //$recordModel->delete();
            $datetime=date('Y-m-d H:i:s');
            global $current_user;
            $db = PearDatabase::getInstance();
            $query = "UPDATE vtiger_crmentity set deleted=1,modifiedtime=?,modifiedby=? where crmid=?";
            $db->pquery($query,array($datetime,$current_user->id,$recordId));
            $signpath=$request->get('signpath');
            $this->dosignContracts(array('signpath'=>$signpath,'userid'=>$userid,"recordId"=>$recordId,'workflowstagesflag'=>$this->sequencefive));
            $datas['msg']='合同编号'.$contract_no.'(未签)归还完成';
            $datas['rstatus']='do_ok';
        }while(0);
        $response = new Vtiger_Response();
        $response->setResult($datas);
        $response->emit();
        exit;
    }

    public function noSignCommon(Vtiger_Request $request){
        $codenumber=$request->get('inputcode');
        $codenumber=trim($codenumber);
        $mod=Vtiger_Module_Model::getCleanInstance("SupplierContracts");
        $datas=array();
        do {
            if (!$mod->exportGrouprt('SupplierContracts', 'Received')) {
                $datas['msg'] = '没有权限';
                $datas['rstatus'] = 'no_status';
                break;
            }
            $db = PearDatabase::getInstance();
            if (is_numeric($codenumber)) {
                $datas['msg'] = '不存在的合同编号或工号';
                $datas['rstatus'] = 'no_status';
                break;
            }
            if (empty($codenumber)) {
                $datas['msg'] = '不正确的输入';
                $datas['rstatus'] = 'no_status';
                break;
            }
            $codenumbertemp=$codenumber;
            $status = $this->checkcontractsno(array($codenumbertemp, 2));
            if ($status['rstatus'] == 'no_status') {
                $datas = $status;
                break;
            }
            $query = "SELECT vtiger_suppliercontracts.suppliercontractsid,modulestatus,vtiger_suppliercontracts.contract_no FROM vtiger_suppliercontracts LEFT JOIN vtiger_crmentity ON vtiger_suppliercontracts.suppliercontractsid = vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted = 0 AND vtiger_suppliercontracts.contract_no=?";
            $result = $db->pquery($query, array($codenumbertemp));

            $num = $db->num_rows($result);

            if ($num == 0) {
                $datas['msg'] = '没有相关记录';
                $datas['rstatus'] = 'no_status';
                break;
            } elseif ($num > 1) {
                $datas['msg'] = '多个重复的合同编号';
                $datas['rstatus'] = 'no_status';
                break;
            }
            $rows = $db->raw_query_result_rowdata($result);
            if ($rows['modulestatus'] == 'c_complete') {
                $datas['msg'] = '合同已签收';
                $datas['rstatus'] = 'no_status';
                break;
            }
            if ($rows['modulestatus'] == 'c_cancel') {
                $datas['msg'] = '合同已作废';
                $datas['rstatus'] = 'no_status';
                break;
            }
            if ($rows['modulestatus'] != 'c_receive') {
                $datas['msg'] = '只有状态为已领取的合同才能进行操作';
                $datas['rstatus'] = 'no_status';
                break;
            }

            $datas['codenumbertemp']=$codenumbertemp;
            $datas['rows']=$rows;
            $datas['rstatus']='ok';
        }while(0);

        return $datas;
    }

    function deletedAuditsettings(Vtiger_Request $request) {
    	$moduleModel = Vtiger_Module_Model::getInstance('ServiceContracts');
        if($moduleModel->exportGrouprt('SupplierContracts','dempartConfirm')){   //权限验证
            global $current_user;
            $id=$request->get("id");
            $delsql="delete from vtiger_auditsettings where auditsettingsid=?";
            $db=PearDatabase::getInstance();
            $datetime=date('Y-m-d H:i:s');
            $db->pquery($delsql,array($id));
        }
        $data='更新成功';
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    // 合同延期审核权限添加
    function addAuditsettings(Vtiger_Request $request) {
    	$auditsettingtype = $request->get("auditsettingtype");
    	$department = $request->get("department");
    	$oneaudituid = $request->get("oneaudituid");
    	$towaudituid = $request->get("towaudituid");
    	$threeaudituid = $request->get("threeaudituid");
    	$data = array('flag'=>'0', 'msg'=>'添加失败');
    	do {
    		$moduleModel = Vtiger_Module_Model::getInstance('SupplierContracts');
            if(!$moduleModel->exportGrouprt('SupplierContracts','dempartConfirm')){   //权限验证
                break;
            }
            if (empty($auditsettingtype)) {
            	break;
            }
            if (empty($department)) {
            	break;
            }
            if (empty($oneaudituid)) {
            	break;
            }
            if (empty($towaudituid)) {
            	break;
            }
            /*if (empty($threeaudituid)) {
            	//break;
                $threeaudituid='';
            }*/
            $sql = "delete from vtiger_auditsettings where auditsettingtype=? AND department=? AND oneaudituid=? AND towaudituid=?";
            $sql2 = "INSERT INTO `vtiger_auditsettings` (`auditsettingsid`, `auditsettingtype`, `department`, `oneaudituid`, `towaudituid`,audituid3, `createtime`, `createid`) VALUES (NULL, ?, ?,?, ?, ?, ?, ?);";
            global $current_user;
            $db=PearDatabase::getInstance();
            $db->pquery($sql, array($auditsettingtype, $department, $oneaudituid, $towaudituid));
            $db->pquery($sql2, array($auditsettingtype, $department, $oneaudituid, $towaudituid,$threeaudituid, date('Y-m-d H:i:s'), $current_user->id));
    		$data = array('flag'=>'1', 'msg'=>'添加成功');
    	} while (0);
    	$response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }




    /**
     * 在线签名的保存
     * @param Vtiger_Request $request
     */
    public function savesignimage(Vtiger_Request $request){
        $data=$this->createStamp($request);
        $data=base64_encode($data);
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * 生成签名
     * @param Vtiger_Request $request
     * @return string
     */
    private function createStamp(Vtiger_Request $request){
        $imgstring=$request->get('image');
        $recordId=microtime(true);
        $newrecordid=md5($recordId);
        global $invoiceimagepath,$root_directory;
        include $root_directory.'modules'.DIRECTORY_SEPARATOR.'ServiceContracts'.DIRECTORY_SEPARATOR.'actions'.DIRECTORY_SEPARATOR.'circleSeal.class.php';

        $imagepath=$invoiceimagepath.date('Y').'/'.date('F').'/'.date('d').'/';
        //是否是目录不是则循环创建
        is_dir($root_directory.$imagepath) || mkdir($root_directory.$imagepath,0777,true);
        //文件相对保存的路径
        $newimagepath= $imagepath.$newrecordid.'.png';
        //以文档流方式创建文件
        $stats=$request->get('stats');
        $title='';
        if($stats=='Received'){
            $title='采购服务合同领用';
        }elseif($stats=='Returned'){
            $title='采购服务合同归还';
        }elseif($stats=='NoSignReturned'){
            $title='采购合同未签归还';
        }elseif($stats=='NotSignInvalid'){
            $title='采购合同作废归还';
        }elseif($stats=='srcuser'){
            $title='采购变更原领用人';
        }elseif($stats=='dstuser'){
            $title='采购变更新领用人';
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
        return $newimagepath;
    }
    /**
     * 写入签名
     * @param Vtiger_Request $request
     * @param $arr
     */
    private function dosignContracts($arr){
        global $current_user,$adb;
        $recordId=$arr['recordId'];
        $userid=$arr['userid'];
        $workflowstagesflag=$arr['workflowstagesflag'];
        $newimagepath=base64_decode($arr['signpath']);
        $newrecordid=base64_encode($recordId);
        $datetime=date('Y-m-d H:i:s');
        $sql = 'INSERT INTO `vtiger_invoicesign`(invoiceid,path,`name`,deleted,setype,createdtime,smcreatorid) VALUES(?,?,?,0,?,?,?)';
        $adb->pquery($sql,array($recordId,$newimagepath,$newrecordid,'SupplierContracts',$datetime,$current_user->id));
        $adb->pquery('update vtiger_salesorderworkflowstages set auditorid=?,auditortime=?,schedule=?,isaction=?,isrejecting=? where salesorderid =? and workflowstagesflag=? and modulename=?',array($userid,$datetime,100,2,0,$recordId,$workflowstagesflag,'SupplierContracts'));
        // cxh 2019-08-17 start
        if($arr['controllerType']=='Received' || $arr['controllerType']=='Returned'){
            if($arr['controllerType']=='Returned'){
                $adb->pquery('update vtiger_salesorderworkflowstages set modulestatus=? where salesorderid =? and modulename=? AND isaction=2 ', array('c_complete',$recordId,'SupplierContracts'));
            }
            //获取发起人信息并提示发起人
            $stageSql = " SELECT s.salesorderworkflowstagesid,s.productid,s.higherid,s.ishigher,s.salesorderid,w.isrole,s.workflowstagesname,s.salesorder_nono,s.accountname,s.modulename,actiontime,(SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS label FROM vtiger_users WHERE id=s.smcreatorid limit 1 ) as smcreator  FROM  vtiger_salesorderworkflowstages as s LEFT JOIN vtiger_workflowstages as w ON s.workflowstagesid = w.workflowstagesid LEFT JOIN vtiger_users as u ON u.id=s.smcreatorid WHERE  1=1  and  s.workflowstagesflag=?  AND   s.modulename='SupplierContracts'  AND  s.salesorderid = ? LIMIT 1  ";
            $result = $adb->pquery($stageSql, array($workflowstagesflag,$recordId));
            if($adb->num_rows($result)){
                $userEmailStr = '';
                $email = $adb->query_result($result,0,'email1');
                $actiontime = $adb->query_result($result,0,'actiontime');
                $module = $adb->query_result($result,0,'modulename');
                $modulename = vtranslate($module,'Vtiger','zh_cn');
                $smcreator  = $adb->query_result($result,0,'smcreator');
                $salesorder_nono = $adb->query_result($result,0,'salesorder_nono');
                $accountname = $adb->query_result($result,0,'accountname');
                $workflowstagesname = $adb->query_result($result,0,'workflowstagesname');
                //邮箱验证
                if($this->checkEmail(trim($email))){
                    $userEmailStr = trim($email);
                    $title="您的申请审核更新啦";
                    // 公共审核消息发送模板
                    $object = new SalesorderWorkflowStages_SaveAjax_Action();
                    $object->sendMsgToUser($title,$userEmailStr,$actiontime,$modulename,$smcreator,$salesorder_nono,$accountname,$workflowstagesname,$module,$recordId);
                }
                //如果是领取合同人 或者归还合同人的工号 给这个工号的人发送消息提醒
                $inputcode = $arr['inputcode'];
                $query = "SELECT vtiger_users.id,vtiger_users.email1,vtiger_users.usercode,vtiger_users.last_name,vtiger_users.`status`,vtiger_departments.departmentname FROM vtiger_users LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid WHERE vtiger_users.`status`='Active' AND id=? ";
                $result = $adb->pquery($query, array($userid));
                $email = $adb->query_result($result,0,'email1');
                //邮箱验证
                if($this->checkEmail(trim($email))){
                    $userEmailStr = trim($email);
                    //公共审核消息发送模板
                    $title = $arr['controllerType']=='Received' ? '您已领取合同':'您已归还合同';
                    $object = new SalesorderWorkflowStages_SaveAjax_Action();
                    // 下面参数共用 上面提单人参数除了 $title,$userEmailStr
                    $object->sendMsgToUser($title,$userEmailStr,$actiontime,$modulename,$smcreator,$salesorder_nono,$accountname,$workflowstagesname,$module,$recordId);
                }
            }

        }
        // cxh 2019-08-17 end
    }

    /**
     * 邮件格式验证
     * @param $str
     * @return bool
     */
    public function checkEmail($str){
        $str=trim($str);
        $regex = '/^[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*@(?:[-_a-z0-9][-_a-z0-9]*\.)*(?:[a-z0-9][-a-z0-9]{0,62})\.(?:(?:[a-z]{2}\.)?[a-z]{2,})$/i';
        if (preg_match($regex, $str)) {
            return true;
        }
        return false;
    }

    public function changeLead(Vtiger_Request $request){
        $inputcode=$request->get('inputcode');
        $recordid=$request->get('recordid');
        $srcuserid=$request->get('srcuserid');
        $dstuserid=$request->get('dstuserid');
        $inputcode=trim($inputcode);
        do{
            if(empty($inputcode)){
                $data['rstatus']='empty';
                $data['msg']='无效的输入';
                break;
            }
            global $adb;
            $codenumber=preg_replace('/-8$/','',$inputcode);
            if($recordid<=0){
                if(is_numeric($codenumber)){
                    $consql=' AND servicecontractsprintid=?';
                }else{
                    $consql=' AND contract_no=?';
                }
                $query="SELECT vtiger_servicecontracts.servicecontractsid FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_servicecontracts.servicecontractsid WHERE vtiger_crmentity.deleted=0 AND vtiger_servicecontracts.modulestatus='已发放'".$consql;
                $result=$adb->pquery($query,array($codenumber));
                $num=$adb->num_rows($result);
                if($num==1){
                    $data['rstatus']='contOK';
                    $servicecontractsid=$adb->fetch_row($result);
                    $data['msg']=$servicecontractsid['servicecontractsid'];
                }elseif($num==0){
                    $data['rstatus']='msgerr';
                    $data['msg']='只有已发放的合同才能更改领用人';
                }else{
                    $data['rstatus']='msgerr';
                    $data['msg']='合同编号重复';
                }
                break;
            }
            $new_codenumber=str_pad($codenumber, 6, '0', STR_PAD_LEFT);
            if($srcuserid<=0){
                $query="SELECT
                            vtiger_crmentity.smownerid
                        FROM
                            `vtiger_servicecontracts`
                        LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_servicecontracts.servicecontractsid
                        LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
                        WHERE
                            vtiger_crmentity.deleted = 0
                        AND vtiger_servicecontracts.modulestatus='已发放'
                        AND vtiger_servicecontracts.servicecontractsid =?
                        AND vtiger_users.usercode =?";
                $result=$adb->pquery($query,array($recordid,$new_codenumber));
                $num=$adb->num_rows($result);
                if($num==1){
                    $data['rstatus']='srcuserOK';
                    $smownerid=$adb->fetch_row($result);
                    $data['msg']=$smownerid['smownerid'];
                }elseif($num==0){
                    $data['rstatus']='msgerr';
                    $data['msg']='当前用户不是合同领取人';
                }else{
                    $data['rstatus']='msgerr';
                    $data['msg']='合同编号重复';
                }
                break;
            }
            if($dstuserid<=0){
                $query="SELECT
                            vtiger_users.id
                        FROM
                            `vtiger_servicecontracts`
                        LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_servicecontracts.servicecontractsid
                        LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
                        WHERE
                            vtiger_crmentity.deleted = 0
                        AND vtiger_servicecontracts.modulestatus='已发放'
                        AND vtiger_users.usercode =?";
                $result=$adb->pquery($query,array($new_codenumber));
                $num=$adb->num_rows($result);
                if($num<5){
                    $query="SELECT id FROM vtiger_users WHERE `status`='Active' AND usercode=?";
                    $resultUser=$adb->pquery($query,array($new_codenumber));
                    $numUser=$adb->num_rows($resultUser);
                    if($numUser<=0){
                        $data['rstatus']='msgerr';
                        $data['msg']='工号不存在或账号禁用!';
                    }elseif($numUser==1){
                        $data['rstatus']='dstuserOK';
                        $smownerid=$adb->fetch_row($resultUser);
                        $data['msg']=$smownerid['id'];
                    }else{
                        $data['rstatus']='msgerr';
                        $data['msg']='工号重复!';
                    }
                }else{
                    $data['rstatus']='msgerr';
                    $data['msg']='新领用人有'.$num.'份合同没有归还,不允许操作';
                }
                break;
            }
        }while(0);
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }
    public function saveChangeLeadSign(Vtiger_Request $request){
        global $current_user,$adb;
        $recordId=$request->get('recordid');
        $newimagepath=$this->createStamp($request);
        $newrecordid=base64_encode($recordId);
        $datetime=date('Y-m-d H:i:s');
        $stats=$request->get('stats');
        if($stats=='dstuser'){
            $dstuserid=$request->get('dstuserid');
            $_REQUEST['record']=$recordId;
            $request=new Vtiger_Request($_REQUEST, $_REQUEST);
            $request->set('assigned_user_id',$dstuserid);
            $request->set('module','SupplierContracts');
            $request->set('Receivedate', date('Y-m-d'));
            $_REQUEST['action']='SaveAjax';
            $request->set('action','SaveAjax');
            $request->set('view','Edit');
            $ressorder=new ServiceContracts_Save_Action();
            $ressorder->saveRecord($request);
        }
        $sql = 'INSERT INTO `vtiger_invoicesign`(invoiceid,path,`name`,deleted,setype,createdtime,smcreatorid) VALUES(?,?,?,0,?,?,?)';
        $adb->pquery($sql,array($recordId,$newimagepath,$newrecordid,'SupplierContracts',$datetime,$current_user->id));
        $data=$stats;
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * @param Vtiger_Request $request
     * @author: steel.liu
     * @Date:xxx
     * 添加对应的合同权限
     */
    public function addsupplierstatus(Vtiger_Request $request){
        $userid = $request->get("userid");
        $suppliercontractsstatus = $request->get("suppliercontractsstatus");
        $data = array('flag'=>'0', 'msg'=>'添加失败');
        do {
            $moduleModel = Vtiger_Module_Model::getInstance('SupplierContracts');
            if(!$moduleModel->exportGrouprt('SupplierContracts','supplierstatus')){   //权限验证
                break;
            }
            if (empty($userid)) {
                break;
            }
            if (empty($suppliercontractsstatus)) {
                break;
            }
            $sql = "delete from vtiger_supplierstatus where userid=? ";
            $sql2 = "INSERT INTO `vtiger_supplierstatus` (`suppliercontractsstatus`, `userid`) VALUES (?,?)";
            $db=PearDatabase::getInstance();
            $db->pquery($sql, array($userid));
            $db->pquery($sql2, array($suppliercontractsstatus, $userid));
            $data = array('flag'=>'1', 'msg'=>'添加成功');
        } while (0);
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * @param Vtiger_Request $request
     * @author: steel.liu
     * @Date:xxx
     * 删除对应的合同权限
     */
    function deletedsupplierstatus(Vtiger_Request $request) {
        $moduleModel = Vtiger_Module_Model::getInstance('ServiceContracts');
        if($moduleModel->exportGrouprt('SupplierContracts','supplierstatus')){   //权限验证
            $id=$request->get("id");
            $delsql="delete from vtiger_supplierstatus where supplierstatusid=?";
            $db=PearDatabase::getInstance();
            $db->pquery($delsql,array($id));
        }
        $data='更新成功';
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     * 获取供应商的银行信息
     * @param $request
     */
    public function getVendorBankInfo($request){
        $record=$request->get('accountid');
        $isneed=$request->get('isneed');
        $recordModel=Vtiger_Record_Model::getInstanceById($record,'Vendors');
        $entity=$recordModel->getEntity();
        $column_fields=$entity->column_fields;
        if($isneed){
            //是否已上传
            $isUpload = $recordModel->isUploadQualificationFiles($record);
            if(!$isUpload){
                $response = new Vtiger_Response();
                $response->setError(-1, '请先在供应商页上传供应商资质！');
                $response->emit();
                exit();
            }
        }

        $bankinfo[]=array(
            'bankaccount'=>$column_fields['bankaccount'],
            'bankcode'=>$column_fields['bankcode'],
            'bankname'=>$column_fields['bankname'],
            'banknumber'=>$column_fields['banknumber'],
        );
        $bankinfodata=$recordModel->getVendorBank($record);
        $bankinfo=array_merge($bankinfo,$bankinfodata);
        $response = new Vtiger_Response();
        $response->setResult($bankinfo);
        $response->emit();
    }

    /**
     * 生成虚拟编号
     * @param Vtiger_Request $request
     * @throws Exception
     */
    public function createVirtualNumber(Vtiger_Request $request){
        global $current_user,$adb;
        $record=$request->get('record');
        try{
            $recordModel=Vtiger_Record_Model::getInstanceById($record,'SupplierContracts');
            $flag=false;
        }catch (Exception $e){
            $flag=true;
        }
        $returnArray=array('flag'=>false);
        do{
            if($flag){
                $returnArray['msg']='编号生成错误';
                break;
            }
            if($recordModel->get('modulestatus')!='a_normal'){
                $returnArray['msg']='只有正常状态下才能生成虚拟编号';
                break;
            }
            if($recordModel->get('assigned_user_id')!=$current_user->id){
                $returnArray['msg']='只有领取人才能进行此操作!';
                break;
            }
            if($recordModel->get('isvirtualnumber')==1){
                $returnArray['msg']='生成错误!';
                break;
            }
            $contract_no=$recordModel->get('contract_no');
            if(!empty($contract_no)){
                $returnArray['msg']='合同编号已生成不允许再生成';
                break;
            }
            $query = "SELECT suppliercontractsstatus,invoicecompany,meter FROM vtiger_suppliercontractsnometer WHERE suppliercontractsstatus='XN' AND invoicecompany='CGHT' LIMIT 1";
            $result = $adb->pquery($query, array());
            if ($adb->num_rows($result)) {
                $meter = $adb->query_result($result, 0, "meter");
                $meter = 1 + $meter;
                $meter = str_pad($meter, 4, '0', STR_PAD_LEFT);
            } else {
                $meter = '0001';
            }
            $adb->pquery('REPLACE INTO vtiger_suppliercontractsnometer(suppliercontractsstatus,invoicecompany,meter) VALUES(\'XN\',\'CGHT\',?)', array($meter));
            $contract_no = 'XNCGHT' . $meter;
            $sql = "UPDATE vtiger_suppliercontracts SET contract_no=?,meter=?,isvirtualnumber=1 WHERE suppliercontractsid=?";
            $adb->pquery($sql, array($contract_no,$meter, $record));
            $sql = "UPDATE vtiger_crmentity SET label=? WHERE crmid=?";
            $adb->pquery($sql, array($contract_no, $record));
            $returnArray['flag']=true;
        }while(0);
        $response = new Vtiger_Response();
        $response->setResult($returnArray);
        $response->emit();
    }

    /**
     * 手工添加报销单
     * @param $request
     */
    public function AddRStatement($request){
        global $current_user,$adb;
        $flownumberofpaymentform=$request->get('flownumberofpaymentform');
        $paymentamount=$request->get('paymentamount');
        $paymentdate=$request->get('paymentdate');
        $recordid=$request->get("recordid");
        try{
            $recordModel=Vtiger_Record_Model::getInstanceById($recordid,'SupplierContracts');
            $flag=false;
        }catch (Exception $e){
            $flag=true;
        }
        $returnArray=array('flag'=>false);
        do{
            if($flag){
                $returnArray['msg']='数据错误!';
                break;
            }
            if(!$recordModel->personalAuthority('SupplierContracts','ADDRSTATEMENT')){
                $returnArray['msg']='没有此权限!';
                break;
            }
            $total=$recordModel->get('total');
            $amountpaid=$recordModel->get('amountpaid');
            $Addpaymentamount=bcadd($paymentamount,$amountpaid,2);
            if($total>0 && bccomp($total,$Addpaymentamount,2)==-1){
                $returnArray['msg']='付款金额之和大于合同金额!';
                break;
            }
            $_REQUEST['action']='SaveAjax';
            $residualamount=$total>0?bcsub($total,$Addpaymentamount,2):0;
            $recordModel->set('amountpaid',$Addpaymentamount);
            $recordModel->set('residualamount',$residualamount);
            $recordModel->set('mode','edit');
            $recordModel->save();
            $query='INSERT INTO `vtiger_reimbursementstatement` (`suppliercontractsid`, `flownumberofpaymentform`, `paymentamount`, `sourcedata`, `paymentdate`, `createdtime`, `smowneerid`) VALUES (?,?,?,\'手动维护\',?,?,?)';
            $adb->pquery($query,array($recordid,$flownumberofpaymentform,$paymentamount,$paymentdate,date('Y-m-d H:i:s'),$current_user->id));
            $returnArray['flag']=true;
        }while(0);
        $response = new Vtiger_Response();
        $response->setResult($returnArray);
        $response->emit();
    }
}
