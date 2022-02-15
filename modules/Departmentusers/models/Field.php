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
class Departmentusers_Field_Model extends Users_Field_Model {

  
    public function getspecialPicklistValuesnouse() {
    	if($this->getName() == 'department'){
    		require 'crmcache/departmentanduserinfo.php';
    		return $cachedepartment;
    	}else{
    		return parent::getPicklistValues();
    	}
    }
}
