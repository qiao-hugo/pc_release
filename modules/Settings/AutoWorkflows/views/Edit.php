<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_AutoWorkflows_Edit_View extends Settings_Vtiger_Index_View {

	public function process(Vtiger_Request $request) {
		$mode = $request->getMode();
		global $adb;
		$recordid = $request->get("record");
 		if(!empty($recordid)){
 		    $sql = "SELECT * FROM vtiger_autoworkflows WHERE autoworkflowid = ?";
 		    $result = $adb->pquery($sql,array($recordid));
 		    if ($adb->num_rows($result)<1){
 		        throw new Exception("你所查询的记录记录不存在");
 		    }else{
 		        $flowli = $adb->fetchByAssoc($result);
 		        $flowli['json_triggercondition'] = json_decode(str_replace('&quot;','"',$flowli['json_triggercondition']));
 		        //$data = (json_decode(str_replace('&quot;','"',$row)));
 		        $viewer = $this->getViewer($request);
 		        $viewer->assign("WORKFLOWDETAIL",$flowli);
 		    }
 		}
		//var_dump($request);die;
		if ($mode) {
			$this->$mode($request);
		} else {
			$this->step1($request);
		}
	}

	public function preProcess(Vtiger_Request $request) {
		parent::preProcess($request);
		$viewer = $this->getViewer($request);

		$recordId = $request->get('record');
		$viewer->assign('RECORDID', $recordId);
		if($recordId) {
			$workflowModel = Settings_AutoWorkflows_Record_Model::getInstance($recordId);
			$viewer->assign('WORKFLOW_MODEL', $workflowModel);
		}
		$viewer->assign('RECORD_MODE', $request->getMode());
		$viewer->view('EditHeader.tpl', $request->getModule(false));
	}

    /**
     * 第一步设置基本的信息
     * @param Vtiger_Request $request
     */
	public function step1(Vtiger_Request $request) {
		$currentUser = Users_Record_Model::getCurrentUserModel();
		$viewer = $this->getViewer($request);
		$moduleName = $request->getModule();
		$qualifiedModuleName = $request->getModule(false);

		$recordId = $request->get('record');
		if ($recordId) {
			$workflowModel = Settings_AutoWorkflows_Record_Model::getInstance($recordId);
			$viewer->assign('RECORDID', $recordId);
			$viewer->assign('MODULE_MODEL', $workflowModel->getModule());  //所有模块筛选
			$viewer->assign('MODE', 'edit');
		} else {
			$workflowModel = Settings_AutoWorkflows_Record_Model::getCleanInstance($moduleName);
            $selectedModule = $request->get('source_module');
            if(!empty($selectedModule)) {
                $viewer->assign('SELECTED_MODULE', $selectedModule);
            }
		}

		$viewer->assign('WORKFLOW_MODEL', $workflowModel);
		$viewer->assign('ALL_MODULES', Settings_AutoWorkflows_Module_Model::getSupportedModules());
		$viewer->assign('TRIGGER_TYPES', Settings_AutoWorkflows_Module_Model::getTriggerTypes());

		$viewer->assign('MODULE', $moduleName);
		$viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
		$viewer->assign('CURRENT_USER', $currentUser);
		$admin = Users::getActiveAdminUser();
		$viewer->assign('ACTIVE_ADMIN', $admin);
		$viewer->view('Step1.tpl', $qualifiedModuleName);
	}

	//第二步操作步骤
	public function step2 (Vtiger_Request $request) {
	    $viewer = $this->getViewer($request);
		$moduleName = $request->getModule();
		$qualifiedModuleName = $request->getModule(false);

		$recordId = $request->get('record');

		/* if ($recordId) {
			$workFlowModel = Settings_AutoWorkflows_Record_Model::getInstance($recordId);
			$selectedModule = $workFlowModel->getModule();
			$selectedModuleName = $selectedModule->getName();
		} else { */
		    
			$workFlowModel = Settings_AutoWorkflows_Record_Model::getCleanInstance($selectedModuleName);
			$selectedModule = Vtiger_Module_Model::getInstance($selectedModuleName);
			$selectedModuleName = $request->get('module_name');
		//}

		$requestData = $request->getAll();
		foreach($requestData as $name=>$value) {
			$workFlowModel->set($name,$value);
		}
		//Added to support advance filters
		//$recordStructureInstance = Settings_AutoWorkflows_RecordStructure_Model::getInstanceForWorkFlowModule($workFlowModel,Settings_AutoWorkflows_RecordStructure_Model::RECORD_STRUCTURE_MODE_FILTER);

		//$viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
		//$viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());

		$viewer->assign('WORKFLOW_MODEL',$workFlowModel);

		$viewer->assign('MODULE_MODEL', $selectedModule);
		$viewer->assign('SELECTED_MODULE_NAME', $selectedModuleName);

		$dateFilters = Vtiger_Field_Model::getDateFilterTypes();
        foreach($dateFilters as $comparatorKey => $comparatorInfo) {
            $comparatorInfo['startdate'] = DateTimeField::convertToUserFormat($comparatorInfo['startdate']);
            $comparatorInfo['enddate'] = DateTimeField::convertToUserFormat($comparatorInfo['enddate']);
            $comparatorInfo['label'] = vtranslate($comparatorInfo['label'], $qualifiedModuleName);
            $dateFilters[$comparatorKey] = $comparatorInfo;
        }
        $viewer->assign('DATE_FILTERS', $dateFilters);
		//$viewer->assign('ADVANCED_FILTER_OPTIONS', Settings_AutoWorkflows_Field_Model::getAdvancedFilterOptions());
		//$viewer->assign('ADVANCED_FILTER_OPTIONS_BY_TYPE', Settings_AutoWorkflows_Field_Model::getAdvancedFilterOpsByFieldType());
		$viewer->assign('COLUMNNAME_API', 'getWorkFlowFilterColumnName');

		$viewer->assign('FIELD_EXPRESSIONS', Settings_AutoWorkflows_Module_Model::getExpressions());
		$viewer->assign('META_VARIABLES', Settings_AutoWorkflows_Module_Model::getMetaVariables());

		// Added to show filters only when saved from vtiger6
		if($workFlowModel->isFilterSavedInNew()) {
			$viewer->assign('ADVANCE_CRITERIA', $workFlowModel->transformToAdvancedFilterCondition());
		} else {
			$viewer->assign('ADVANCE_CRITERIA', "");
		}

		$viewer->assign('IS_FILTER_SAVED_NEW',$workFlowModel->isFilterSavedInNew());
		$viewer->assign('MODULE', $moduleName);
		$viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
		
		$viewer->view('Step2.tpl', $qualifiedModuleName);

		$selectmodulename = $request->get('module_name');
		$moduleModel = Vtiger_Module_Model::getInstance($selectmodulename);
		$viewer = $this->getViewer($request);
		$viewer->assign('SEARCHRECORD_STRUCTURE', $moduleModel->getSearchFields());
		$viewer->assign('SOURCE_MODULE',$selectmodulename);
		$viewer->view('Condition.tpl', $qualifiedModuleName);
	}

	function Step3(Vtiger_Request $request) {
		$viewer = $this->getViewer($request);
		$moduleName = $request->getModule();
		$qualifiedModuleName = $request->getModule(false);

		$recordId = $request->get('record');

		$moduleModel = $workFlowModel->getModule();
		$viewer->assign('TASK_TYPES', Settings_AutoWorkflows_TaskType_Model::getAllForModule($moduleModel));
		$viewer->assign('SOURCE_MODULE',$selectedModuleName);
		$viewer->assign('RECORD',$recordId);
		$viewer->assign('MODULE', $moduleName);
		$viewer->assign('WORKFLOW_MODEL',$workFlowModel);
		$viewer->assign('TASK_LIST', $workFlowModel->getTasks());
		$viewer->assign('QUALIFIED_MODULE',$qualifiedModuleName);

		$viewer->view('Step3.tpl', $qualifiedModuleName);
	}

	public function getHeaderScripts(Vtiger_Request $request) {
		$headerScriptInstances = parent::getHeaderScripts($request);
		$moduleName = $request->getModule();

		$jsFileNames = array(
			'modules.Settings.Vtiger.resources.Edit',
			"modules.Settings.$moduleName.resources.Edit",
			"modules.Settings.$moduleName.resources.Edit1",
			"modules.Settings.$moduleName.resources.Edit2",
			"modules.Settings.$moduleName.resources.Edit3",
			//"modules.Settings.$moduleName.resources.AdvanceFilter",
			"modules.Settings.$moduleName.resources.EditTask",
			'~libraries/jquery/ckeditor/ckeditor.js',
			"modules.Vtiger.resources.CkEditor",
		);

		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
	}

	
	
	//wangbin 获取条件规则；
	public function getcondition(Vtiger_Request $request){
	    $qualifiedModuleName = $request->getModule(false);
        $record = $request->get('record');
        $selectmodulename = $request->get('selectmodulename');
        $moduleModel = Vtiger_Module_Model::getInstance($selectmodulename);
        $viewer = $this->getViewer($request);
        $viewer->assign('SEARCHRECORD_STRUCTURE', $moduleModel->getSearchFields());
        $viewer->view('Condition.tpl', $qualifiedModuleName);
	}


    /**
     * 流程设计页面,默认需要读取本页面上的默认的流程id
     * @param Vtiger_Request $request
     */
    public function designFlow(Vtiger_Request $request){
        global $adb;
        $viewer = $this->getViewer($request);
        $record=$request->get('source_record');
        //读取当前流程下的所有的节点
        $sql = 'SELECT * FROM  vtiger_autoworkflowtasks WHERE autoworkflowid=?';
        $result = $adb->pquery($sql,array($record));
        $arrModel=array();
        if($adb->num_rows($result)>0){
           // $strTasks='{"total":'.$adb->num_rows($result).',"list":[';
            while($row=$adb->fetch_array($result)){
                $arrModel[]=$row;
            }
        }
        $qualifiedModuleName = $request->getModule(false);
        $viewer->assign('MODULE_MODLE',$arrModel);
        $viewer->assign('FLOWID',$record);
        $viewer->view('flowdesign.tpl',$qualifiedModuleName);
    }
}