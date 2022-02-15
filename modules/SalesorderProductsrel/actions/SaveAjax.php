<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
class SalesorderProductsrel_SaveAjax_Action extends Vtiger_Save_Action {
	
	public function checkPermission(Vtiger_Request $request) {
		return ;
// 		$moduleName = $request->getModule();
// 		$record = $request->get('record');
	
// 		if(!Users_Privileges_Model::isPermitted($moduleName, 'Save', $record)) {
// 			throw new AppException('LBL_PERMISSION_DENIED');
// 		}
	}

	public function process(Vtiger_Request $request) {
// 		$type = $request->get('type');
// 		$id = $request->get('record');
// 		//获取登录用户信息
// 		$currentUser = Users_Record_Model::getCurrentUserModel();
// 		$userid = $currentUser->get('id');

// 		//更新
// 		if ($type == "audit"){
// 			//审核
// 			$update_query = "update vtiger_salesorderproductsrel set backerid=null,isvisible=0,backtime='',backwhy='',auditorid=?,salesorderproductsrelstatus='pass',audittime=sysdate(),finishstatus=1 where salesorderproductsrelid=?";
// 			$update_params = array($userid, $id);
// 		}else if ($type == "reject"){
// 			//打回
// 			$backwhy =$request->get('backwhy');
// 			$update_query = "update vtiger_salesorderproductsrel set backerid=?,backtime=sysdate(),backwhy=?,auditorid=null,salesorderproductsrelstatus='reject',audittime='',isvisible=0,nodestatus=0,finishstatus=0 where salesorderproductsrelid=?";
// 			$update_params = array($userid, $backwhy,$id);
// 		}else{
// 			exit;
// 		}
		
// 		$db = PearDatabase::getInstance();
// 		$db->pquery($update_query, $update_params);
// 		return null;
	}
}
