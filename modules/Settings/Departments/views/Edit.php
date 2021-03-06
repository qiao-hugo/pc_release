<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Class Settings_Departments_Edit_View extends Settings_Departments_Index_View {

	public function process(Vtiger_Request $request) {
		$viewer = $this->getViewer ($request);
		$moduleName = $request->getModule();
		$qualifiedModuleName = $request->getModule(false);
		$record = $request->get('record');
		
		$parentRoleId = $request->get('parent_departmentid');
		$roleDirectlyRelated = false;
		
		
		if(!empty($record)) {
			
			$recordModel = Settings_Departments_Record_Model::getInstanceById($record);
			$viewer->assign('MODE', 'edit');
		} else {
			$recordModel = new Settings_Departments_Record_Model();
			$viewer->assign('MODE', '');
			$recordModel->setParent(Settings_Departments_Record_Model::getInstanceById($parentRoleId));
		}

		//$viewer->assign('MEMBER_GROUPS', Settings_Groups_Member_Model::getAll(false));
		$viewer->assign('RECORD_MODEL', $recordModel);
		
		$viewer->assign('RECORD_ID', $record);
		$viewer->assign('MODULE', $moduleName);
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());

		$viewer->view('EditView.tpl', $qualifiedModuleName);
	}
}