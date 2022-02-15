<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
class VisitingOrder_SaveAjax_Action extends Vtiger_Save_Action {
	function __construct() {
		parent::__construct();
		$this->exposeMethod('autofillvisitingorder');
	}
	
	public function checkPermission(Vtiger_Request $request) {
		return ;
// 		$moduleName = $request->getModule();
// 		$record = $request->get('record');
	
// 		if(!Users_Privileges_Model::isPermitted($moduleName, 'Save', $record)) {
// 			throw new AppException('LBL_PERMISSION_DENIED');
// 		}
	}
/**
 * 2014-12-26 14:25:12 wangbin 
 * */

	public function autofillvisitingorderback (Vtiger_Request $request){
		$accountid = $request->get(accountid);
		$db=PearDatabase::getInstance();
        //查询公司地址
		$address = $db->pquery('SELECT address  FROM `vtiger_account` WHERE accountid=?',array($accountid));
		$li1 = $db->num_rows($address);
		for ($i=0; $i<$li1; ++$i) {
			$num = $db->fetchByAssoc($address);
			$addresslist = $num;
		}
  		/* //查询公司联系人
		$contact = $db->pquery('SELECT name  FROM `vtiger_contactdetails` WHERE accountid=?',array($accountid));
		$li2 = $db->num_rows($contact);
		$contactlist=array();
		for ($i=0; $i<$li2; ++$i) {
			$num = $db->fetchByAssoc($contact);
			$contactlist[$i] = $num;
		} */
		
		$contactlist=ModComments_Record_Model::getModcommentContacts($accountid);
		$datalist=array($addresslist,$contactlist);
		$response = new Vtiger_Response();
		$response->setEmitType(Vtiger_Response::$EMIT_JSON);
		$response->setResult($datalist);
		$response->emit();
	}
	
	public function process(Vtiger_Request $request) {
		$type = $request->get('type');
		$id = $request->get('record');
		$mode = $request->getMode();
		if(!empty($mode)) {
			echo $this->invokeExposedMethod($mode, $request);
			return;
		}
		//获取登录用户信息
		$currentUser = Users_Record_Model::getCurrentUserModel();
		$userid = $currentUser->get('id');
		$db = PearDatabase::getInstance();
		$select_query = "select * from vtiger_visitingorder where visitingorderid=?";
		$result = $db->pquery($select_query, array($id));
		//审核状态
		$examinestatus = $db->query_result($result, 0,'examinestatus');
		//跟进状态
		$followstatus = $db->query_result($result, 0,'followstatus');
	
		//echo $followstatus;
		if ($followstatus=="followup"){
			$response = new Vtiger_Response();
			$response->setResult(array($followstatus));
			$response->emit();
			return;
		}

		//更新处理
		if ($type == "followup"){
// 			if ($examinestatus =="pass"){
// 				//跟进
// 				$update_query = "update vtiger_visitingorder set followid=?,followstatus='followup',followtime=sysdate() where visitingorderid=?";
// 				$update_params = array($userid, $id);
// 			}else{
// 				$response = new Vtiger_Response();
// 				$response->setResult(array($examinestatus));
// 				$response->emit();
// 			    return;
// 			}
		}else if ($type == "audit"){
			//审核
			$update_query = "update vtiger_visitingorder set examineid=?,examinestatus='pass',examinetime=sysdate() where visitingorderid=?";
			$update_params = array($userid, $id);
		}else if ($type == "reject"){
			//拒绝
			$update_query = "update vtiger_visitingorder set examineid=?,examinestatus='reject',examinetime=sysdate() where visitingorderid=?";
			$update_params = array($userid,$id);
		}else{
			exit;
		}
		
		$db->pquery($update_query, $update_params);
		$result = array('label'=>decode_html(''));
		$response = new Vtiger_Response();
		$response->setResult($result);
		$response->emit();
	    return;
	}
	public function autofillvisitingorder(Vtiger_Request $request){

        $datalist=$this->getSignedAddress($request);
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($datalist);
        $response->emit();
    }
    public function getSignedAddress(Vtiger_Request $request){
        $recordId=$request->get('accountid');
        $db=PearDatabase::getInstance();
        $query="SELECT vtiger_crmentity.setype FROM vtiger_crmentity
        WHERE vtiger_crmentity.deleted=0 AND vtiger_crmentity.setype IN ('School','Vendors','Accounts') AND vtiger_crmentity.crmid=? limit 1";
        $result=$db->pquery($query,array($recordId));
        if($db->num_rows($result)){
            $info=$db->query_result($result);
            $info="getInfo".$info;
            $datalist=$this->$info($request);
        }else{
            $datalist=array();
        }
        return $datalist;
    }

    /**
     * 取客户的信息
     * @param Vtiger_Request $request
     * @return array
     */
    private function getInfoAccounts(Vtiger_Request $request){
        $accountid = $request->get('accountid');
        $db=PearDatabase::getInstance();
        //查询公司地址
        $address = $db->pquery('SELECT address  FROM `vtiger_account` WHERE accountid=?',array($accountid));
        $li1 = $db->num_rows($address);
        for ($i=0; $i<$li1; ++$i) {
            $num = $db->fetchByAssoc($address);
            $addresslist = $num;
        }
        $contactlist=ModComments_Record_Model::getModcommentContacts($accountid);
        return array($addresslist,$contactlist);
    }

    /**
     * 取供应商的信息
     */
    private function getInfoVendors(Vtiger_Request $request){
        $accountid = $request->get('accountid');
        $db=PearDatabase::getInstance();
        //查询公司地址
        $address = $db->pquery('SELECT vtiger_vendor.address,vtiger_vendor.linkman FROM `vtiger_vendor` WHERE vendorid=?',array($accountid));
        $li1 = $db->num_rows($address);
        for ($i=0; $i<$li1; ++$i) {
            $addresslists = $db->fetchByAssoc($address);
        }
        $addresslist=array('address'=>$addresslists['address']);
        $contactlist=array(array('name'=>$addresslists['linkman']));
        return array($addresslist,$contactlist);
    }

    /**
     * 学校的相关相信息
     * @param Vtiger_Request $request
     * @return array
     */
    private function getInfoSchool(Vtiger_Request $request){
        $accountid = $request->get('accountid');
        $db=PearDatabase::getInstance();
        //查询公司地址
        $address = $db->pquery('SELECT address,contactsuser as linkman FROM `vtiger_school` WHERE schoolid=?',array($accountid));
        $li1 = $db->num_rows($address);
        for ($i=0; $i<$li1; ++$i) {
            $addresslists = $db->fetchByAssoc($address);
        }
        $addresslist=array('address'=>$addresslists['address']);
        $contactlist=array(array('name'=>$addresslists['linkman']));
        return array($addresslist,$contactlist);
    }
}
