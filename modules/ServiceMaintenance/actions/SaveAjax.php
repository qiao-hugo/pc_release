<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
class ServiceMaintenance_SaveAjax_Action extends Vtiger_Save_Action {
	public function process(Vtiger_Request $request) {
		$db = PearDatabase::getInstance();
		$id = $request->get('record');
		
		$updateSql = "update vtiger_servicemaintenance SET processstate='cancellation',finishtime=sysdate() where servicemaintenanceid=?";
		$db->pquery($updateSql, array($id));
		
		$result = array(0);
		$response = new Vtiger_Response();
		$response->setResult($result);
		$response->emit();
	    return;
	}
}
