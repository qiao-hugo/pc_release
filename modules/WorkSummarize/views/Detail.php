<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class WorkSummarize_Detail_View extends Vtiger_Detail_View {
	function __construct() {
		parent::__construct();
		$recordModel = Vtiger_Record_Model::getInstanceById($_REQUEST['record'], 'WorkSummarize');
		$moduleModel = $recordModel->getModule();
		$entity=$recordModel->entity->column_fields;
		global $current_user;
		$where=getAccessibleUsers('WorkSummarize','List',true);
		if($where!='1=1' && !(in_array($entity['assigned_user_id'],$where) || in_array($current_user->id, explode(' |##| ', $entity['touser'])))){
			throw new AppException('你不能查看该用户工作总结');
			exit;
		}
	}
	function showDetailViewByMode($request) {
		$requestMode = $request->get('requestMode');
		if($requestMode == 'full') {
			return $this->showModuleDetailView($request);
		}
		return $this->showModuleBasicView($request);
	}
	function showModuleDetailView(Vtiger_Request $request) {
		
		
		
		$recordId = $request->get('record');
		$moduleName = $request->getModule();
		//young.yang 2014-12-26 工作流
		global $isallow;
		if(in_array($moduleName, $isallow)){
			echo $this->getWorkflowsM($request);
		}
		//end	
		if(!$this->record){
		$this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
		}
		$recordModel = $this->record->getRecord();
		$recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
		$structuredValues = $recordStrucure->getStructure();
		
		
        $moduleModel = $recordModel->getModule();
		
		$viewer = $this->getViewer($request);
		$viewer->assign('RECORD', $recordModel);
		$viewer->assign('PREV_CONTENT', WorkSummarize_Record_Model::getLastRecord());
		$viewer->assign('REPLY_CONTENT', WorkSummarize_Record_Model::getReply());
		$viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
		$viewer->assign('MODULE_NAME', $moduleName);
		$viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
		
		
		return $viewer->view('DetailViewFullContents.tpl',$moduleName,true);
	}
	function showModuleBasicView($request) {
	
		$recordId = $request->get('record');
		$moduleName = $request->getModule();
	
		if(!$this->record){
			$this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
		}
		$recordModel = $this->record->getRecord();
	
		$detailViewLinkParams = array('MODULE'=>$moduleName,'RECORD'=>$recordId);
		$detailViewLinks = $this->record->getDetailViewLinks($detailViewLinkParams);
	
		$viewer = $this->getViewer($request);
		$viewer->assign('RECORD', $recordModel);
		$viewer->assign('MODULE_SUMMARY', $this->showModuleSummaryView($request));
	
		$viewer->assign('DETAILVIEW_LINKS', $detailViewLinks);
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
		$viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
		$viewer->assign('MODULE_NAME', $moduleName);
	
		$recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
		$structuredValues = $recordStrucure->getStructure();
	
		$moduleModel = $recordModel->getModule();
		$viewer->assign('REPLY_CONTENT', WorkSummarize_Record_Model::getReply());
		$viewer->assign('PREV_CONTENT', WorkSummarize_Record_Model::getLastRecord());
		$viewer->assign('RECORD_STRUCTURE', $structuredValues);
		$viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
	
		echo $viewer->view('DetailViewSummaryContents.tpl', $moduleName, true);
	}
	
}
