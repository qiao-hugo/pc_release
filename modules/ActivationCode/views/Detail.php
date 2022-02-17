<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class ActivationCode_Detail_View extends Vtiger_Detail_View {
    function preProcess(Vtiger_Request $request) {
        $viewer = $this->getViewer($request);
        $viewer->assign('NO_SUMMARY', true);
        parent::preProcess($request);
    }
    /**
     * Function returns Inventory details
     * @param Vtiger_Request $request
     */
    function showModuleDetailView(Vtiger_Request $request) {
        //echo parent::showModuleDetailView($request);
        //echo $this->getWorkflowsM($request);
        $recordId = $request->get('record');
        $moduleName = $request->getModule();

        if(!$this->record){
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }


        $recordModel = $this->record->getRecord();

        //获取跟进信息  gaocl add
        global $adb;
        $recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
        $structuredValues = $recordStrucure->getStructure();
        $moduleModel = $recordModel->getModule();
        $viewer = $this->getViewer($request);
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));

        $arr_tyun = $recordModel->searchProductList($recordId);
        $viewer->assign('TYUN_BUY_SERVICE_LIST', $arr_tyun['tyun_buy_service']);
        $viewer->assign('TYUN_ALL_BUY_SERVICE_LIST', $arr_tyun['tyun_all_buy_service']);
        return $viewer->view('DetailViewFullContents.tpl',$moduleName,true);
    }

    /**
     * Function returns Inventory details
     * @param Vtiger_Request $request
     * @return type
     */
    function showDetailViewByMode(Vtiger_Request $request) {
        return $this->showModuleDetailView($request);
    }

    function showModuleBasicView($request) {
        return $this->showModuleDetailView($request);
    }

    /**
     * Function returns latest comments
     * @param Vtiger_Request $request
     * @return <type>
     */
    function showRecentComments(Vtiger_Request $request) {
        $parentId = $request->get('record');

        $pageNumber = $request->get('page');
        $limit = $request->get('limit');
        $moduleName = $request->getModule();

        if(empty($pageNumber) || $pageNumber=='undefined') {
            $pageNumber = 1;
        }

        $pagingModel = new Vtiger_Paging_Model();
        $pagingModel->set('page', $pageNumber);
        if(!empty($limit)) {
            $pagingModel->set('limit', $limit);
        }

        $recentComments = ModComments_Record_Model::getRecentComments($parentId, $pagingModel,$moduleName);
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
        $viewer = $this->getViewer($request);
        $viewer->assign('COMMENTS', $recentComments);
        $viewer->assign('ACCOUNTID', $accountid);
        $viewer->assign('COMMENTSMODE', ModComments_Record_Model::getModcommentmode());
        $viewer->assign('COMMENTSTYPE',ModComments_Record_Model::getModcommenttype());
        $viewer->assign('MODCOMMENTCONTACTS',ModComments_Record_Model::getModcommentContacts($accountid));
        $viewer->assign('CURRENTUSER', $currentUserModel);
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('PAGING_MODEL', $pagingModel);

        return $viewer->view('RecentComments.tpl', $moduleName, 'true');
    }

    /**
     * Function sends all the comments for a parent(Accounts, Contacts etc)
     * @param Vtiger_Request $request
     * @return <type>
     */
    function showAllComments(Vtiger_Request $request) {
        $parentRecordId = $request->get('record');
        $commentRecordId = $request->get('commentid');
        $moduleName = $request->getModule();
        $currentUserModel = Users_Record_Model::getCurrentUserModel();

        $parentCommentModels = ModComments_Record_Model::getAllParentComments($parentRecordId);

        if(!empty($commentRecordId)) {
            //$currentCommentModel = ModComments_Record_Model::getInstanceById($commentRecordId);
        }

        //获取客户id
        $accountid="";
        if ($moduleName !="Accounts"){
            if(!$this->record){
                $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $parentRecordId);
            }
            $recordModel = $this->record->getRecord();
            $accountid=$recordModel->get('related_to');
        }else{
            $accountid=$parentRecordId;
        }

        $viewer = $this->getViewer($request);
        $viewer->assign('CURRENTUSER', $currentUserModel);
        $viewer->assign('ACCOUNTID', $accountid);
        $viewer->assign('PARENT_COMMENTS', $parentCommentModels);
        $viewer->assign('CURRENT_COMMENT', $currentCommentModel);
        $viewer->assign('COMMENTSMODE', array('拜访'));
        $viewer->assign('COMMENTSTYPE',ModComments_Record_Model::getModcommenttype());
        $viewer->assign('MODCOMMENTCONTACTS',ModComments_Record_Model::getModcommentContacts($accountid));
        return $viewer->view('ShowAllComments.tpl', $moduleName, 'true');
    }
}
