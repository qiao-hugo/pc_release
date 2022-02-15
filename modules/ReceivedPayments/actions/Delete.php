<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ReceivedPayments_Delete_Action extends Vtiger_Delete_Action {

	function checkPermission(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$record = $request->get('record');

		$currentUserPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		if(!$currentUserPrivilegesModel->isPermitted($moduleName, 'Delete', $record)) {
			throw new AppException('LBL_PERMISSION_DENIED');
		}
		//young.yang 2015-1-3 增加编辑页面，对于流程状态的控制，某些状态不允许编辑
		global $isallow;
		if(in_array($moduleName, $isallow)){
			$module=SalesorderWorkflowStages_Record_Model::getInstanceById(0);
			$result=$module->getWorkflowsStatus($moduleName, $record);
			if(!empty($result)){
				if(!$result['success']){
					throw new AppException($result['msg']);
					/*$response = new Vtiger_Response();
					$response->setResult($result);
					$response->emit();
					exit; */
				}
				
			}
		}
		//end
	}

	public function process(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$recordId = $request->get('record');
		$ajaxDelete = $request->get('ajaxDelete');
		
		$recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
		$moduleModel = $recordModel->getModule();
		$db=PearDatabase::getInstance();
		$isreceived=$db->pquery('select discontinued from vtiger_receivedpayments where receivedpaymentsid=?',array($recordId));
		if($isreceived->fields['0']==1){
		    throw new AppException(vtranslate($moduleName).' '.vtranslate('LBL_NOT_ACCESS'));
		    exit;
		}
		$recordModel->delete();

		$listViewUrl = $moduleModel->getListViewUrl();
		if($ajaxDelete) {
			$response = new Vtiger_Response();
			$response->setResult($listViewUrl);
			return $response;
		} else {
			header("Location: $listViewUrl");
		}
	}
}