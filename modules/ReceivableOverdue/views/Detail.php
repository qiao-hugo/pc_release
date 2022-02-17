<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ReceivableOverdue_Detail_View extends Vtiger_Detail_View {
    function showModuleDetailView(Vtiger_Request $request) {
        return parent::showModuleDetailView($request);
    }

    /**
     * 跟进信息
     */
    function showRecentComments(Vtiger_Request $request) {
        $contractOverdueRecordModel = Vtiger_Record_Model::getInstanceById($request->get('record'),$request->getModule());
//        $parentId = $request->get('record');
        $parentId = $contractOverdueRecordModel->getContractId();
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
//        if ($moduleName !="Accounts"){
//
////            if(!$this->record){
////                $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $parentId);
////            }
//
//            $recordModel = $this->record->getRecord();
//            $accountid=$recordModel->get('related_to');
//
//        }else{
//            $accountid=$parentId;
//        }
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
        $viewer->assign('STAGE_LIST',$contractOverdueRecordModel->getStageList($parentId));
        return $viewer->view('RecentComments.tpl', $moduleName, 'true');
    }

}
