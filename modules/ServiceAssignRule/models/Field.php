<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
include_once 'vtlib/Vtiger/Field.php';

/**
 * Vtiger Field Model Class
 */
class ServiceAssignRule_Field_Model extends Vtiger_Field_Model {
	/**
	 * Function which will check if empty piclist option should be given
	 */
	public function isEmptyPicklistOptionAllowed() {
		return false;
	}
	
	/**
	 * Function to retieve display value for a value
	 * @param <String> $value - value which need to be converted to display value
	 * @return <String> - converted display value
	 */
	public function getDisplayValue($value, $record=false, $recordInstance = false) {
		//客户名称取得
// 		if($this->getName() == 'related_to'){
// 			return ServiceAssignRule_Record_Model::getAccountsName($value);
// 		}
		//产品名称取得
		if($this->getName() == 'productid'){
			return ServiceAssignRule_Record_Model::getProductName($value);
		}
		
		//负责人
		if($this->getName() == 'ownerid' && empty($value)){
			return '--';
		}
		
		if(!$this->uitype_instance) {
			$this->uitype_instance = Vtiger_Base_UIType::getInstanceFromField($this);
		}
		$uiTypeInstance = $this->uitype_instance;
		return $uiTypeInstance->getDisplayValue($value, $record, $recordInstance);
	}
	
	/**
	 * Function to get all the available picklist values for the current field
	 * @return <Array> List of picklist values if the field is of type picklist or multipicklist, null otherwise.
	 */
	public function getPicklistValues() {
        $fieldDataType = $this->getFieldDataType();
		if($this->getName() == 'hdnTaxType') return null;

        if($fieldDataType == 'picklist' || $fieldDataType == 'multipicklist') {
            $currentUser = Users_Record_Model::getCurrentUserModel();
            
            if($this->getName() == 'related_to')
            {
            	//客户数据取得
            	$fieldPickListValues=ServiceAssignRule_Record_Model::getAccountsListValues();
            }else if($this->getName() == 'departmentid')
            {
            	//部门数据取得
            	//$fieldPickListValues=ServiceAssignRule_Record_Model::getDepartmentListValues();
            }else  if($this->getName() == 'productid')
            {
            	//产品数据取得
            	$fieldPickListValues=ServiceAssignRule_Record_Model::getProductListValues();
            }else{
            	if($this->isRoleBased() && !$currentUser->isAdminUser()) {
            		$userModel = Users_Record_Model::getCurrentUserModel();
            		$picklistValues = Vtiger_Util_Helper::getRoleBasedPicklistValues($this->getName(), $userModel->get('roleid'));
            	}else{
            		$picklistValues = Vtiger_Util_Helper::getPickListValues($this->getName());
            	}
            	foreach($picklistValues as $value) {
            		$fieldPickListValues[$value] = vtranslate($value,$this->getModuleName());
            	}
            }
			return $fieldPickListValues;
			}
		return null;
    }
}