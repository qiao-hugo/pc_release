<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Accounts_WhiteList_Action extends Vtiger_SaveAjax_Action {

	

	public function process(Vtiger_Request $request) {
		exit;
		
		$selectedIds = $request->get('selectedId');
		$db = PearDatabase::getInstance();
		foreach($selectedIds as $recordId) {
			if(Users_Privileges_Model::isPermitted($moduleName, 'White_List', $recordId)) {
				
				$recordId=abs((int)$recordId);
				$query = "update vtiger_account set protected=1 WHERE accountid=".$recordId;
				echo $recordId;
				$db->pquery($query);
			}
		}
		
		$cvId = $request->get('viewname');
		$response = new Vtiger_Response();
		$response->setResult(array('viewname'=>$cvId, 'module'=>$moduleName));
		$response->emit();
	}
}
