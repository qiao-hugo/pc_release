<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Leads_Detail_View extends Vtiger_Detail_View {
	function __construct() {
        	parent::__construct();
        	$this->exposeMethod('showAccountComments');
    	}
	/*public function process(Vtiger_Request $request){
		$recordId = $request->get('record');
		$moduleName = $request->getModule();
		//young.yang 2014-12-26 工作流
		global $isallow;
		if(in_array($moduleName, $isallow)){
			//echo $this->getWorkflowsM($request);
		}
		//end
		if(!$this->record){
			$this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
		}
		$recordModel = $this->record->getRecord();
		$recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_WORKFLOW);
		$structuredValues = $recordStrucure->getStructure();
		
		
		$moduleModel = $recordModel->getModule();
		
		$viewer = $this->getViewer($request);
		$viewer->assign('RECORD', $recordModel);
		$viewer->assign('RECORD_STRUCTURE', $structuredValues);
		$viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
		$viewer->assign('MODULE_NAME', $moduleName);
		$viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
		print_r($structuredValues);
		
		echo $viewer->view('DetailViewFullContents.tpl',$moduleName,true);
	}*/
    function preProcess(Vtiger_Request $request, $display=true) {
        parent::preProcess($request, false);
        $recordId = $request->get('record');
        $moduleName = $request->getModule();
        if(!$this->record){
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }

        $recordModel = $this->record->getRecord();
        $recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
        $summaryInfo = array();

        // Take first block information as summary information
        $stucturedValues = $recordStrucure->getStructure();
        foreach($stucturedValues as $blockLabel=>$fieldList) {
            $summaryInfo[$blockLabel] = $fieldList;
            break;
        }

        $detailViewLinkParams = array('MODULE'=>$moduleName,'RECORD'=>$recordId);

        $detailViewLinks = $this->record->getDetailViewLinks($detailViewLinkParams);
        $navigationInfo = null;//ListViewSession::getListViewNavigation($recordId);  // 20150611 young 这里造成详细页面错误，暂时为null

        $viewer = $this->getViewer($request);
        $viewer->assign('RECORD', $recordModel);
        //	$viewer->assign('NAVIGATION', $navigationInfo);
        //Intially make the prev and next records as null
        $prevRecordId = null;
        $nextRecordId = null;
        $found = false;
        if ($navigationInfo) {
            foreach($navigationInfo as $page=>$pageInfo) {
                foreach($pageInfo as $index=>$record) {
                    //If record found then next record in the interation
                    //will be next record
                    if($found) {
                        $nextRecordId = $record;
                        break;
                    }
                    if($record == $recordId) {
                        $found = true;
                    }
                    //If record not found then we are assiging previousRecordId
                    //assuming next record will get matched
                    if(!$found) {
                        $prevRecordId = $record;
                    }
                }
                //if record is found and next record is not calculated we need to perform iteration
                if($found && !empty($nextRecordId)) {
                    break;
                }
            }
        }

        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        if(!empty($prevRecordId)) {
            $viewer->assign('PREVIOUS_RECORD_URL', $moduleModel->getDetailViewUrl($prevRecordId));
        }
        if(!empty($nextRecordId)) {
            $viewer->assign('NEXT_RECORD_URL', $moduleModel->getDetailViewUrl($nextRecordId));
        }

        $viewer->assign('MODULE_MODEL', $this->record->getModule());

        $viewer->assign('DETAILVIEW_LINKS', $detailViewLinks);

        $viewer->assign('IS_EDITABLE', $this->record->getRecord()->isEditable($moduleName));
        $viewer->assign('IS_DELETABLE', $this->record->getRecord()->isDeletable($moduleName));

        $linkParams = array('MODULE'=>$moduleName, 'ACTION'=>$request->get('view'));
        $linkModels = $this->record->getSideBarLinks($linkParams);
        $viewer->assign('QUICK_LINKS', $linkModels);

        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $viewer->assign('DEFAULT_RECORD_VIEW', $currentUserModel->get('default_record_view'));
        $viewer->assign('CLUEFOLLOWSTATUS', $recordModel->getClueFollowStatus($recordId));
        $viewer->assign('LEADSOURCE', $recordModel->get("leadsource"));
        $viewer->assign('LOCATIONPROVINCE', $recordModel->get("locationprovince"));



        if($display) {
            $this->preProcessDisplay($request);
        }

    }

    function showModuleSummaryView($request) {
        $recordId = $request->get('record');
        $moduleName = $request->getModule();

        if(!$this->record){
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }
        $recordModel = $this->record->getRecord();
        $recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_SUMMARY);

        $moduleModel = $recordModel->getModule();
        $viewer = $this->getViewer($request);
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());

        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        $viewer->assign('SUMMARY_RECORD_STRUCTURE', $recordStrucure->getStructure());
        $viewer->assign('RELATED_ACTIVITIES', $this->getActivities($request));
        $viewer->assign('LEADSOURCE', $recordModel->get("leadsource"));
        $viewer->assign('LOCATIONPROVINCE', $recordModel->get("locationprovince"));
        return $viewer->view('ModuleSummaryView.tpl', $moduleName, true);
    }
    /**
     * 跟进信息
     */
    function showRecentComments(Vtiger_Request $request) {
        $parentId = $request->get('record');
        $pageNumber =(int)$request->get('page');
        $limit = $request->get('limit');
        $moduleName = $request->getModule();
        if(empty($pageNumber)){
            $pageNumber = 1;
        }

        $pagingModel = new Vtiger_Paging_Model();
        $pagingModel->set('page', $pageNumber);
        if(!empty($limit)) {
            $pagingModel->set('limit', $limit);
        }

        $recentComments = ModComments_Record_Model::getRecentComments($parentId, $pagingModel,$moduleName);
        $recentComments = Leads_Record_Model::getRecentComments($parentId, $pagingModel,$moduleName);
        $pagingModel->calculatePageRange($recentComments);
        $currentUserModel = Users_Record_Model::getCurrentUserModel();

        //获取客户id
        $accountid="";
        if ($moduleName !="Accounts"){
            if(!$this->record){
                $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $parentId);
            }
            $recordModel = $this->record->getRecord();
            $accountid=$recordModel->get('related_to');
        }else{
            $accountid=$parentId;
        }

        require_once 'crmcache/role.php';
        $viewer = $this->getViewer($request);
        $viewer->assign('COMMENTS', $recentComments);
        $viewer->assign('ROLE', $roles);
        $viewer->assign('ACCOUNTID', $accountid);
        $viewer->assign('COMMENTSMODE', ModComments_Record_Model::getModcommentmode());
        $viewer->assign('COMMENTSTYPE',ModComments_Record_Model::getModcommenttype());
        //$viewer->assign('MODCOMMENTCONTACTS',ModComments_Record_Model::getModcommentContacts($accountid));
        $viewer->assign('MODCOMMENTCONTACTS',Leads_Record_Model::getModcommentContacts($parentId));
        $viewer->assign('CURRENTUSER', $currentUserModel);
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('PAGING_MODEL', $pagingModel);

        return $viewer->view('RecentComments.tpl', $moduleName, 'true');
    }
    function showAccountComments(Vtiger_Request $request) {
        $parentId = $request->get('record');
        $pageNumber =(int)$request->get('page');
        $limit = $request->get('limit');
        $moduleName = $request->getModule();
        if(empty($pageNumber)){
            $pageNumber = 1;
        }

        $pagingModel = new Vtiger_Paging_Model();
        $pagingModel->set('page', $pageNumber);
        if(!empty($limit)) {
            $pagingModel->set('limit', $limit);
        }

        $recentComments = ModComments_Record_Model::getRecentComments($parentId, $pagingModel,$moduleName);
        $recentComments = Leads_Record_Model::getRecentComments($parentId, $pagingModel,$moduleName);
        $pagingModel->calculatePageRange($recentComments);
        $currentUserModel = Users_Record_Model::getCurrentUserModel();

        //获取客户id
        $accountid="";
        if ($moduleName !="Accounts"){
            if(!$this->record){
                $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $parentId);
            }
            $recordModel = $this->record->getRecord();
            $accountid=$recordModel->get('related_to');
        }else{
            $accountid=$parentId;
        }

        require_once 'crmcache/role.php';
        $viewer = $this->getViewer($request);
        $viewer->assign('COMMENTS', $recentComments);
        $viewer->assign('ROLE', $roles);
        $viewer->assign('ACCOUNTID', $accountid);
        $viewer->assign('COMMENTSMODE', ModComments_Record_Model::getModcommentmode());
        $viewer->assign('COMMENTSTYPE',ModComments_Record_Model::getModcommenttype());
        //$viewer->assign('MODCOMMENTCONTACTS',ModComments_Record_Model::getModcommentContacts($accountid));
        $viewer->assign('MODCOMMENTCONTACTS',Leads_Record_Model::getModcommentContacts($parentId));
        $viewer->assign('CURRENTUSER', $currentUserModel);
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('PAGING_MODEL', $pagingModel);
        $viewer->assign('LEADSOURCE', $recordModel->get("leadsource"));
        $viewer->assign('LOCATIONPROVINCE', $recordModel->get("locationprovince"));
        return $viewer->view('SaccountComments.tpl', 'Leads', 'true');
    }
}
