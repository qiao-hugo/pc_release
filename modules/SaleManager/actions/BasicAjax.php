<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class SaleManager_BasicAjax_Action extends Vtiger_Action_Controller {
	function __construct() {
		parent::__construct();
        $this->exposeMethod('saveaccountroleid');
        $this->exposeMethod('deleteRole');
	}
	
	function checkPermission(Vtiger_Request $request) {
		return;
	}



    /**
     * @param Vtiger_Request $request
     * @throws Exception
     */
	public function process(Vtiger_Request $request) {
		$mode = $request->getMode();
		if(!empty($mode)) {
			echo $this->invokeExposedMethod($mode, $request);
			return;
		}
	}
    public function saveaccountroleid(Vtiger_Request $request){
        $recordid=$request->get('roleid');
        global $adb;
        $query='SELECT 1 FROM vtiger_accountrole WHERE roleid=?';
        $result=$adb->pquery($query,array($recordid));
        if(!$adb->num_rows($result)){
            $sql="INSERT INTO  `vtiger_accountrole`(roleid,rolename) SELECT roleid,rolename FROM vtiger_role WHERE roleid=?";
            $adb->pquery($sql,array($recordid));
        }

        $response = new Vtiger_Response();
        $response->setResult(array());
        $response->emit();
    }
    public function deleteRole(Vtiger_Request $request){
        $recordid=$request->get('roleid');
        global $adb;
        $sql="delete from `vtiger_accountrole` WHERE roleid=?";
        $adb->pquery($sql,array($recordid));
        $response = new Vtiger_Response();
        $response->setResult(array());
        $response->emit();
    }

}
