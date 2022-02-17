<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_Departments_Popup_View extends Vtiger_Footer_View {
	
	public function checkPermission(Vtiger_Request $request) {
		$currentUser = Users_Record_Model::getCurrentUserModel();
        $modulename=$request->get('module');
        $settingsModel = Settings_Vtiger_Module_Model::getInstance();
        $fieldid=$settingsModel->getFieldId($modulename);
        global $adb,$current_user;
        $userid=$current_user->id;
        $result=$adb->pquery('SELECT id FROM `vtiger_user2setting` WHERE FIND_IN_SET(?,userid) AND FIND_IN_SET(?,setting)',array($userid,$fieldid));
        if(!$currentUser->isAdminUser() && $adb->num_rows($result)==0) {
			throw new AppException('LBL_PERMISSION_DENIED');
		}
	}

	function process (Vtiger_Request $request) {
		
		
		$viewer = $this->getViewer($request);
		$moduleName = $request->getModule();
		$qualifiedModuleName = $request->getModule(false);

		$sourceRecord = $request->get('src_record');
		
		$companyDetails = Vtiger_CompanyDetails_Model::getInstanceById();
		$companyLogo = $companyDetails->getLogo();

		$sourceRole = Settings_Departments_Record_Model::getInstanceById($sourceRecord);
		$rootRole = Settings_Departments_Record_Model::getBaseDepartment();
		$allRoles = Settings_Departments_Record_Model::getAll();
		
		$viewer->assign('SOURCE_ROLE', $sourceRole);
		$viewer->assign('ROOT_ROLE', $rootRole);
		$viewer->assign('ROLES', $allRoles);
		
		$viewer->assign('MODULE_NAME',$moduleName);
		$viewer->assign('COMPANY_LOGO',$companyLogo);
		
		$viewer->view('Popup.tpl', $qualifiedModuleName);
	}

	/**
	 * Function to get the list of Script models to be included
	 * @param Vtiger_Request $request
	 * @return <Array> - List of Vtiger_JsScript_Model instances
	 */
	function getHeaderScripts(Vtiger_Request $request) {
		$headerScriptInstances = parent::getHeaderScripts($request);
		$moduleName = $request->getModule();

		$jsFileNames = array(
			'modules.Settings.Vtiger.resources.Popup',
			"modules.Settings.$moduleName.resources.Popup",
			"modules.Settings.$moduleName.resources.$moduleName",
			'libraries.jquery.jquery_windowmsg',
		);

		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
	}
}