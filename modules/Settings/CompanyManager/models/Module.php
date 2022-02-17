<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/*
 * Settings Module Model Class
 */
class Settings_CompanyManager_Module_Model extends Settings_Vtiger_Module_Model {

	var $baseTable = 'vtiger_company_code';
	var $baseIndex = 'companyid';
	var $listFields = array('departmentid'=>'company id','companyfullname' => 'Name');
	var $name = 'CompanyManager';
}
