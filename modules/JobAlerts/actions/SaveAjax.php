<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
class JobAlerts_SaveAjax_Action extends Vtiger_Save_Action {
	
	public function checkPermission(Vtiger_Request $request) {
		return ;
// 		$moduleName = $request->getModule();
// 		$record = $request->get('record');
	
// 		if(!Users_Privileges_Model::isPermitted($moduleName, 'Save', $record)) {
// 			throw new AppException('LBL_PERMISSION_DENIED');
// 		}
	}

	public function process(Vtiger_Request $request) {
		$id = $request->get('record');
		//获取登录用户信息
// 		$currentUser = Users_Record_Model::getCurrentUserModel();
// 		$userid = $currentUser->get('id');
		$db = PearDatabase::getInstance();

		//完成
		$update_query = "update vtiger_jobalerts set alertstatus='finish',finishtime=sysdate() where jobalertsid=?";
		$update_params = array($id);
		$db->pquery($update_query, $update_params);
		
		$result = array('label'=>decode_html(''));
		$response = new Vtiger_Response();
		$response->setResult($result);
		$response->emit();
	    return;
	}
}
