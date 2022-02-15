<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class PotentialScalesrel_BasicAjax_Action extends Vtiger_BasicAjax_Action {
	
	
	function checkPermission(Vtiger_Request $request) {
		return true;
	}

	public function process(Vtiger_Request $request) {
		$moduleName = $request->get('module');
		$recordId = $request->get('record');
		$mode = $request->getMode();
		
		$result=array();
		if(!empty($mode)){
			$result=$this->invokeExposedMethod($mode, $request);
			$response = new Vtiger_Response();
			$response->setResult($result);
			$response->emit();
		}else{
			parent::process($request);
			return;
		}
	}
	
	public function getAllPotentialScales(Vtiger_Request $request){
		$recordId=$request->get('record');
		
		$result=PotentialScalesrel_Record_Model::getRelateScales($recordId);
		return $result;
	}
}
