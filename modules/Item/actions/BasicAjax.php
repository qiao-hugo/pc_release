<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Item_BasicAjax_Action extends Vtiger_Action_Controller {
	function __construct() {
		parent::__construct();
        $this->exposeMethod('hasRepeatSubItem');
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

	public function hasRepeatSubItem(Vtiger_Request $request){
	    global $adb;
        $return['flag']=true;
        $parentcate=trim($request->get('parentcate'));
        $soncate=trim($request->get('soncate'));
        $sql="SELECT vtiger_soncate.soncateid FROM vtiger_soncate LEFT JOIN vtiger_parentcate ON vtiger_soncate.parentcate=vtiger_parentcate.parentcateid WHERE 1=1 AND vtiger_soncate.deleted=0 AND vtiger_parentcate.parentcate=? AND vtiger_soncate.soncate=?";
        $result=$adb->pquery($sql,array($parentcate,$soncate));
        if($adb->num_rows($result)>0){
            $return['flag']=false;
        }
        $response = new Vtiger_Response();
        $response->setResult($return);
        $response->emit();
    }

}
