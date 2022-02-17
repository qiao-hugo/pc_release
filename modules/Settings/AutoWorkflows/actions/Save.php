<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_AutoWorkflows_Save_Action extends Settings_Vtiger_Basic_Action {

	public function process(Vtiger_Request $request) {
	    $db = PearDatabase::getInstance();
		$recordId = $request->get('record');
		$summary = $request->get('summary');
		$moduleName = $request->get('module_name');
		$conditions = $request->get('conditions');
		$filterSavedInNew = $request->get('filtersavedinnew');
		$executionCondition = $request->get('execution_condition');
		$jsoncondition = $request->get('submitcondition'); // wangbin;		
		$date_var = date("Y-m-d H:i");
		$modifiedtime =  $db->formatDate($date_var, true);
		if(!empty($jsoncondition)){
		    $strJson = "[";
		    for ($i=0;$i<count($jsoncondition);++$i){
		        $strJson .= $jsoncondition[$i].",";
		    }
		    $strJson =  trim($strJson,',')."]"; //第二步触发条件json数据条件
		}
		if($recordId){
		    $updatesql = "UPDATE `vtiger_autoworkflows`  set autoworkflowname= ?, modulename= ?, execution_condition= ?, modifiedtime = ?, json_TriggerCondition = ? WHERE autoworkflowid =? ";
		    $db->pquery($updatesql,array($summary,$moduleName,$executionCondition,$modifiedtime,$strJson,$recordId));
		    $insertid = $recordId;    
		}else{
		    $insertsql = "INSERT INTO `vtiger_autoworkflows` ( autoworkflowname, modulename, execution_condition, modifiedtime, json_TriggerCondition) VALUES (?, ?, ?, ?, ?)";
		    //$current_id = $db->getUniqueID("vtiger_autoworkflows");
		    $db->pquery($insertsql,array($summary,$moduleName,$executionCondition,$modifiedtime,$strJson));
		    $insertid =  $db->getLastInsertID();
		  }
		 $response = new Vtiger_Response();
		$response->setResult(array('id' => $insertid));
		$response->emit();
	}
} 