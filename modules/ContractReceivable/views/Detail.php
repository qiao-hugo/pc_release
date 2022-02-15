<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ContractReceivable_Detail_View extends Vtiger_Detail_View {
    function process(Vtiger_Request $request) {
        if($request->get('mode')=='showRecentComments'){
            echo $this->showRecentComments($request);
            return;
        }
        if($request->get('mode')=='showRelatedList'){
            echo $this->showRelatedList($request);
            return;
        }
        echo $this->showModuleDetailView($request);
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
        //var_dump($recordStrucure);die;
        $structuredValues = $recordStrucure->getStructure();

        $moduleModel = $recordModel->getModule();

        $viewer = $this->getViewer($request);
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        $executionDetailData = $recordModel->getExecutionDetailData();
        $viewer->assign('CONTRACT_EXECUTION_DETAILS', $executionDetailData['detaildata']);
        $viewer->assign('TOTAL_RECEIVABLE_AMOUNT', $executionDetailData['totalreceiveableamount']);
        $viewer->assign('TOTAL_CONTRACT_RECEIVABLE_BALANCE', $executionDetailData['totalcontractreceivablebalance']);
        return $viewer->view('DetailViewFullContents.tpl',$moduleName,true);
    }

    /**
     * 跟进信息
     */
    function showRecentComments(Vtiger_Request $request) {
        $contractRecordModel = Vtiger_Record_Model::getInstanceById($request->get('record'),$request->getModule());
//        $parentId = $request->get('record');
        $parentId = $contractRecordModel->getContractId();

        $pageNumber =(int)$request->get('page');
        $limit = $request->get('limit');
//        $moduleName = $request->getModule();
        $moduleName = 'ServiceContracts';

        if(empty($pageNumber)){
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
        $viewer->assign('CONTRACTID', $parentId);
        $viewer->assign('STAGE_LIST',$contractRecordModel->getStageList($parentId));

        return $viewer->view('RecentComments.tpl', $moduleName, 'true');
    }
}
