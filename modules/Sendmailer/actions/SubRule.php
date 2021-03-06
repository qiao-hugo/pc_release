<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Sendmailer_SubRule_Action extends Vtiger_Action_Controller{

	function __construct() {

		$this->exposeMethod('getAccountInfos');

	}

	public function checkPermission(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);

		$userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		$permission = $userPrivilegesModel->hasModulePermission($moduleModel->getId());

		if(!$permission) {
			throw new AppException('LBL_PERMISSION_DENIED');
		}
	}

	public function process(Vtiger_Request $request) {
		$mode = $request->getMode();
		if(!empty($mode) && $this->isMethodExposed($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}

	}



	/**
	 * 获取分配客户信息
	 * @param Vtiger_Request $request
	 */
	function getAccountInfos(Vtiger_Request $request) {

		//获取客户信息

		echo json_encode(Sendmailer_Record_Model::getAccountInfos($request));

		/*$response = new Vtiger_Response();
		$response->setResult($result);
		$response->emit();*/
	}
	

}