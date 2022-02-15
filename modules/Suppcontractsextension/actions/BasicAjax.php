<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Suppcontractsextension_BasicAjax_Action extends Vtiger_Action_Controller {
	function __construct() {
		parent::__construct();
		$this->exposeMethod('addContractsExtension');

	}
	
	function checkPermission(Vtiger_Request $request) {
		return;
	}
	

	function addContractsExtension(Vtiger_Request $request){
	    $userid = $request->get('suserid');
	    $record = $request->get('srecord');
        $workflowsid=397296;

        $_REQUES['record']='';
        global $current_user;
        do {
            // 这块有点没搞懂 20161209
            /*if ($userid == $current_user->id) {
                break;
            }*/
            global $adb;
      
            $query="SELECT 1 FROM vtiger_suppliercontracts LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_suppliercontracts.suppliercontractsid WHERE vtiger_crmentity.deleted=0 AND vtiger_suppliercontracts.suppliercontractsid=? AND vtiger_crmentity.smownerid=?";
            $result=$adb->pquery($query,array($record,$userid));
            if($adb->num_rows($result)!=1){
                break;
            }

            $query="SELECT 1 FROM vtiger_suppcontractsextension LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_suppcontractsextension.suppcontractsextensionid WHERE vtiger_crmentity.deleted=0 AND vtiger_suppcontractsextension.suppliercontractsid=?";
            $result=$adb->pquery($query,array($record));
            if($adb->num_rows($result)!=0){
                break;
            }

            $request = new Vtiger_Request($_REQUES, $_REQUES);
            $request->set('assigned_user_id', $current_user->id);
            $request->set('suppliercontractsid', $record);
            $request->set('workflowsid', $workflowsid);
            $request->set('module', 'Suppcontractsextension');
            $request->set('view', 'Edit');
            $request->set('action', 'Save');
            $ressorder = new SupplierContracts_Save_Action();
            $ressorderecord = $ressorder->saveRecord($request);

            $on_focus = CRMEntity::getInstance('Suppcontractsextension');
            $on_focus->makeWorkflows('Suppcontractsextension', $workflowsid, $ressorderecord->getId(), 'edit');

        }while(0);

        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array());
        $response->emit();

	}

	public function process(Vtiger_Request $request) {
		$mode = $request->getMode();
		if(!empty($mode)) {
			echo $this->invokeExposedMethod($mode, $request);
			return;
		}

	}

}
