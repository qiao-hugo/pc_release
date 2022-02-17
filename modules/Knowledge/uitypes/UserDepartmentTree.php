<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Knowledge_UserDepartmentTree_UIType extends Vtiger_UserDepartmentTree_UIType {
	public function getDisplayValue($value, $recordId) {
		$displayValue = $this->getEditViewDisplayValue($value);
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		if ($currentUserModel->isAdminUser()) {
			$roleRecordModel = new Settings_Roles_Record_Model();
			$roleRecordModel->set('roleid', $value);
			//return '<a href="'. $roleRecordModel->getEditViewUrl() .'">'. textlength_check($displayValue) .'</a>';
		}
		return $displayValue;
	}
}