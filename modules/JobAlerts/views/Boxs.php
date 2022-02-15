<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class JobAlerts_Boxs_View extends Vtiger_IndexAjax_View {

	function __construct() {
		parent::__construct();
		$this->exposeMethod('setJobAlerts');
	}
	public function process(Vtiger_Request $request) {
		$record = $request->get('record');
		$mode=$request->getMode();
		if(!empty($mode)){
			$this->invokeExposedMethod($mode,$request);
			return;
		}
		
		$moduleName = $request->getModule();
		$recordModel = ModComments_Record_Model::getInstanceById($record);
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		
		//print_r($recordModel->getData());die();
		
		$viewer = $this->getViewer($request);
		$viewer->assign('CURRENTUSER', $currentUserModel);
		$viewer->assign('COMMENT', $recordModel);
		echo $viewer->view('Comment.tpl', $moduleName, true);
	}
	//设置提醒
	public function setJobAlerts(Vtiger_Request $request){
		//客户id
		$accountid=$request->get('accountid');
		$moduleName = $request->getModule();
		//跟进id
		$modcommentsid = $request->get('src_record');
		//评论id
		$record= $request->get('record');
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		
// 		 if(!empty($record) && $request->get('isDuplicate') == true) {
//             $recordModel = $this->record?$this->record:Vtiger_Record_Model::getInstanceById($record, $moduleName);
//             $viewer->assign('MODE', '');
//         }else if(!empty($record)) {
//             $recordModel = $this->record?$this->record:Vtiger_Record_Model::getInstanceById($record, $moduleName);
//             $viewer->assign('RECORD_ID', $record);
//             $viewer->assign('MODE', 'edit');
//         } else {
//             $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
//             $viewer->assign('MODE', '');
//         }
        
        $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
        if(!$this->record){
            $this->record = $recordModel;
        }
        
		$moduleModel = $recordModel->getModule();
		$fieldList = $moduleModel->getFields();
		$requestFieldList = array_intersect_key($request->getAll(), $fieldList);

		foreach($requestFieldList as $fieldName=>$fieldValue){
			$fieldModel = $fieldList[$fieldName];
			$specialField = false;
			// We collate date and time part together in the EditView UI handling 
			// so a bit of special treatment is required if we come from QuickCreate 
			if ($moduleName == 'Calendar' && empty($record) && $fieldName == 'time_start' && !empty($fieldValue)) { 
				$specialField = true; 
				// Convert the incoming user-picked time to GMT time 
				// which will get re-translated based on user-time zone on EditForm 
				$fieldValue = DateTimeField::convertToDBTimeZone($fieldValue)->format("H:i"); 
                
			}
            
            if ($moduleName == 'Calendar' && empty($record) && $fieldName == 'date_start' && !empty($fieldValue)) { 
                $startTime = Vtiger_Time_UIType::getTimeValueWithSeconds($requestFieldList['time_start']);
                $startDateTime = Vtiger_Datetime_UIType::getDBDateTimeValue($fieldValue." ".$startTime);
                list($startDate, $startTime) = explode(' ', $startDateTime);
                $fieldValue = Vtiger_Date_UIType::getDisplayDateValue($startDate);
            }
			if($fieldModel->isEditable() || $specialField) {
				$recordModel->set($fieldName, $fieldModel->getDBInsertValue($fieldValue));
			}
		}
		$recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_EDIT);
		
		$viewer = $this->getViewer($request);
		$viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());//UI字段生成位置
		$viewer->assign('CURRENTUSER', $currentUserModel);
		$viewer->assign('COMMENT', $recordModel);
		$viewer->assign('RECORD', $record);
		$viewer->assign('ACCOUNT_ID', $accountid);
		$viewer->assign('ACCOUNT_NAME', JobAlerts_Record_Model::getAccountName($accountid));
		$viewer->assign('MODULE', $moduleName);
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
		$viewer->assign('Modcommentsid', $modcommentsid);
		//$viewer->assign('SCRIPTS', $this->getHeaderScripts($request));
		
		echo $viewer->view('SubJobAlerts.tpl', 'JobAlerts', true);
	}
	public function getHeaderScripts(Vtiger_Request $request){
		$jsFileNames = array(
				"modules.products.resources.Edit"
		);
		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		return $jsScriptInstances;
	}
	
	
	
}