<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/**
 * User Field Model Class
 */
class Users_Field_Model extends Vtiger_Field_Model {

    /**
	 * Function to check whether the current field is read-only
	 * @return <Boolean> - true/false
	 */
	public function isReadOnly() {
        /*$currentUserModel = Users_Record_Model::getCurrentUserModel();*/
        if(/*($currentUserModel->isAdminUser() == false && $this->get('uitype') == 98) || */$this->get('uitype') == 106 || $this->get('uitype') == 156) {
            return true;
        }
	}


	/**
	 * Function to check if the field is shown in detail view
	 * @return <Boolean> - true/false
	 */
	public function isViewEnabled() {
		if($this->getDisplayType() == '4' || in_array($this->get('presence'), array(1,3))) {
			return false;
		}
		return true;
	}


    /**
	 * Function to get the Webservice Field data type
	 * @return <String> Data type of the field
	 */
	public function getFieldDataType() {
		if($this->get('uitype') == 99){
			return 'password';
		}else if(in_array($this->get('uitype'), array(32, 115))) {
            return 'picklist';
        } else if($this->get('uitype') == 101) {
            return 'userReference';
        } else if($this->get('uitype') == 98) {
            return 'userRole';
        } elseif($this->get('uitype') == 105) {
			return 'image';
		} else if($this->get('uitype') == 31) {
			return 'theme';
		} else if($this->get('uitype') == 102) {
			return 'userDepartment';
		} 
        return parent::getFieldDataType();
    }

    /**
	 * Function to check whether field is ajax editable'
	 * @return <Boolean>
	 */
	public function isAjaxEditable() {
		if(!$this->isEditable() || $this->get('uitype') == 105 || $this->get('uitype') == 106 || $this->get('uitype') == 98 || $this->get('uitype') == 101 || $this->get('uitype') == 102) {
			return false;
		}
		return true;
	}

	/**
	 * Function to get all the available picklist values for the current field
	 * @return <Array> List of picklist values if the field is of type picklist or multipicklist, null otherwise.
	 */
	public function getPicklistValues() {
		if($this->get('uitype') == 32) {
			return Vtiger_Language_Handler::getAllLanguages();
		}
        else if ($this->get('uitype') == '115') {
            $db = PearDatabase::getInstance();
            
            $query = 'SELECT '.$this->getFieldName().' FROM vtiger_'.$this->getFieldName();
            $result = $db->pquery($query, array());
            $num_rows = $db->num_rows($result);
            $fieldPickListValues = array();
            for($i=0; $i<$num_rows; $i++) {
                $picklistValue = $db->query_result($result,$i,$this->getFieldName());
                $fieldPickListValues[$picklistValue] = vtranslate($picklistValue,$this->getModuleName());
            }
            return $fieldPickListValues;
        }
		return parent::getPicklistValues();
	}

    /**
     * Function to returns all skins(themes)
     * @return <Array>
     */
    public function getAllSkins(){
        return Vtiger_Theme::getAllSkins();
    }

    /**
	 * Function to retieve display value for a value
	 * @param <String> $value - value which need to be converted to display value
	 * @return <String> - converted display value
	 */
    public function getDisplayValue($value,$recordId = false,$recordInstance = false) {
        
		 if($this->get('uitype') == 32){
			return Vtiger_Language_Handler::getLanguageLabel($value);
		 }
        return parent::getDisplayValue($value, $recordId);
    }

	/**
	 * Function returns all the User Roles
	 * @return
	 */
     public function getAllRoles($true=true){
		
		if($true){
			require_once 'crmcache/role.php';
		
			return array_flip($roles);
		
		
		}
	 
	 
        $roleModels = Settings_Roles_Record_Model::getAll();
		$roles = array();
		foreach ($roleModels as $roleId=>$roleModel) {
			$roleName = $roleModel->getName();
			$roles[$roleName] = $roleId;
		}
		return $roles;
    }
	/**
	 * 返回所有的部门
	 * @return multitype:Ambigous <<Array>, multitype:Ambigous <Settings_Departments_Record_Model, Vtiger_Base_Model, Settings_Departments_Record_Model> >
	 */
    public function getAllDepartments($true=true){
		if($true){
			require 'crmcache/departmentanduserinfo.php';
			return array_flip($departlevel);
		}
    	$deparmentModels=Settings_Departments_Record_Model::getAll();
    	$departments=array();
    	foreach($deparmentModels as $departmentId=>$deparmentModel){
    		$departmentName=$deparmentModel->getName();
    		$departments[$departmentName]=$departmentId;
    	}
    	
    	return $departments;
    }
	/**
	 * Function to check whether this field editable or not
	 * return <boolen> true/false
	 */
	public function isEditable() {
		$isEditable = $this->get('editable');
		if (!$isEditable) {
			$this->set('editable', parent::isEditable());
		}
		return $this->get('editable');
	}
	
	/**
     * Function which will check if empty piclist option should be given
     */
    public function isEmptyPicklistOptionAllowed() {
		if($this->getFieldName() == 'reminder_interval') {
			return true;
		}
        return false;
    }
    //获取体系缓存信息
    public function getTissueLists() {
    	$TissueLists=Vtiger_Cache::get('zdcrm_','TissueLists');
    	if(empty($TissueLists)){
    		require 'crmcache/TissueEditor.php';
    	}
    	return $TissueLists;	
    }
    public function getspecialPicklistValuesnouse() {
    	if($this->getName() == 'secondroleid'){
    		require 'crmcache/role.php';
    		return $roles;
    	}else{
    		return parent::getPicklistValues();
    	}
    }
}
