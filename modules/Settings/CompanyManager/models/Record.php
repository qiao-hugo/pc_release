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
 * Departments Record Model Class
 */
class Settings_CompanyManager_Record_Model extends Settings_Vtiger_Record_Model {
	/**
	 * Function to get the Id
	 * @return <Number> department Id
	 */
	public function getId() {
		return $this->get('companyid');
	}
	/**
	 * Function to get the department Name
	 * @return <String>
	 */
	public function getName() {
		return $this->get('companyfullname');
	}
    /**
     * cxh 2019-09-02 add
     * @return Vtiger_Base_Model
     */
    public static function getAllCompanyList(){
          $db = PearDatabase::getInstance();
          $sql = " SELECT companyid,invoicecompany as companyfullname FROM  vtiger_invoicecompany WHERE  1=1   ";
          $params = array();
          $result = $db->pquery($sql, $params);
          $data =array();
          while($row = $db->fetch_array($result)){
                  $data[]=$row;
          }
          return $data;
    }



}