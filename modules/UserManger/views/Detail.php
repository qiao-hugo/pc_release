<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class UserManger_Detail_View extends Vtiger_Detail_View {
    function preProcess(Vtiger_Request $request, $display = true) {
		$viewer = $this->getViewer($request);
		$viewer->assign('NO_SUMMARY', true);
		parent::preProcess($request);
	}
	/**
	 * Function returns Inventory details
	 * @param Vtiger_Request $request
	 */
	function showModuleDetailView(Vtiger_Request $request) {
		$recordId = $request->get('record');
		$moduleName = $request->getModule();
	
		if(!$this->record){
			$this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
		}
	
	
		$recordModel = $this->record->getRecord();

		echo $this->getWorkflowsM($request,$recordModel);
		
		$recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
		$structuredValues = $recordStrucure->getStructure();
		$moduleModel = $recordModel->getModule();
		$viewer = $this->getViewer($request);
		$viewer->assign('RECORD', $recordModel);
		$viewer->assign('RECORD_STRUCTURE', $structuredValues);
		$viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
		$viewer->assign('MODULE_NAME', $moduleName);
		$viewer->assign('EMAILD', $recordModel->get('email1'));
        $viewer->assign('MYUSER',array());
		if($recordModel->get('userid')>0){
            $viewer->assign('MYUSER',getMyuser($recordModel->get('userid')));
        }
		$viewer->assign('MODULESTATUS', $recordModel->get('modulestatus'));
		$viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));

		return $viewer->view('DetailViewFullContents.tpl',$moduleName,true);
	}

	/**
	 * Function returns Inventory details
	 * @param Vtiger_Request $request
	 * @return type
	 */
	function showDetailViewByMode($request) {
		return $this->showModuleDetailView($request);
	}

	function showModuleBasicView($request) {
		return $this->showModuleDetailView($request);
	}
    function getWorkflowsM(Vtiger_Request $request){
        $moduleName = $request->getModule();
        $viewer = $this->getViewer($request);
        $viewer->assign('ModuleName',$moduleName); //工作流stagesid
        return  $viewer->view('LineItemsWorkflowsM.tpl', 'UserManger',true);
    }
}
	