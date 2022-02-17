<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Settings_Departments_Save_Action extends Vtiger_Action_Controller {
	
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

	public function process(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$qualifiedModuleName = $request->getModule(false);
		$recordId = $request->get('record');
		$roleName = $request->get('rolename');
		$departmentcode = $request->get('code');
		$isjuridicalperson = $request->get('isjuridicalperson');
		$erpaccount = $request->get('erpaccount');
		$peopleid = $request->get('peopleid');
		//$allowassignedrecordsto = $request->get('allowassignedrecordsto');

		$moduleModel = Settings_Vtiger_Module_Model::getInstance($qualifiedModuleName);
		if(!empty($recordId)) {
			$recordModel = Settings_Departments_Record_Model::getInstanceById($recordId);
		} else {
			$recordModel = new Settings_Departments_Record_Model();
		}

		$parentRoleId = $request->get('parent_departmentid');

		
		if($recordModel && !empty($parentRoleId)) {
			$parentRole = Settings_Departments_Record_Model::getInstanceById($parentRoleId);
            //检查code是否重复
            $checkdepartmentcode=Settings_Departments_Record_Model::getIsRepeatField('departmentcode',$departmentcode,$recordId);
			//if(!empty($allowassignedrecordsto)) $recordModel->set('allowassignedrecordsto', $allowassignedrecordsto); // set the value of assigned records to
			if($parentRole && !empty($roleName) && empty($checkdepartmentcode)) {
				$recordModel->set('departmentname', $roleName);
				$recordModel->set('departmentcode', $departmentcode);
                $recordModel->set('isjuridicalperson', $isjuridicalperson);
                $recordModel->set('peopleid', $peopleid);
				if($isjuridicalperson==1){
                    $recordModel->set('erpaccount', $erpaccount);
                }else{
                    $recordModel->set('erpaccount', '');
                }


				//$recordModel->set('profileIds', $roleProfiles);
				$parentRole->addChildDepartment($recordModel);
			}
		}

		$redirectUrl = $moduleModel->getIndexViewUrl();
		header("Location: $redirectUrl");
	}
}
