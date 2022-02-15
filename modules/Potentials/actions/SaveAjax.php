<?php
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Potentials_SaveAjax_Action extends Vtiger_SaveAjax_Action {
	
	function __construct() {
		parent::__construct();
		$this->exposeMethod('autofillpotentials');
	}
	public function process(Vtiger_Request $request) {
		$mode = $request->getMode();
		
		if(!empty($mode)) {
			echo $this->invokeExposedMethod($mode, $request);
			return;
		}
		if(method_exists($this,$mode)){
			$this->$mode($request);
		}
		//Restrict to store indirect relationship from Potentials to Contacts
		$sourceModule = $request->get('sourceModule');
		$relationOperation = $request->get('relationOperation');

		if ($relationOperation && $sourceModule === 'Contacts') {
			$request->set('relationOperation', false);
		}

		parent::process($request);
	}
	public function autofillpotentials (Vtiger_Request $request){
		
		$accountid = $request->get(accountid);
		$db=PearDatabase::getInstance();
//此文件的来源:拜访单
// 		查询公司联系人
		$contactlist=ModComments_Record_Model::getModcommentContacts($accountid);
		
		$datalist=$contactlist;
		$response = new Vtiger_Response();
		$response->setEmitType(Vtiger_Response::$EMIT_JSON);
		$response->setResult($datalist);
		$response->emit();
	}

	
}
