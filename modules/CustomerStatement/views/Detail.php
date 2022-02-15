<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class CustomerStatement_Detail_View extends Vtiger_Detail_View {
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
//        parent::showModuleDetailView($request);
        $moduleName = $request->getModule();
        global $isallow;
//        if(in_array($moduleName, $isallow)){
//            echo $this->getWorkflowsM($request);
//
//        }
        echo $this->getWorkflows($request);

        $recordId = $request->get('record');
        $moduleName = $request->getModule();

        if(!$this->record){
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }

        $recordModel = $this->record->getRecord();
        $recod = Vtiger_Record_Model::getInstanceById($recordId,$moduleName);
        //获取跟进信息  gaocl add
        global $adb,$current_user;
        $recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
        $structuredValues = $recordStrucure->getStructure();
        $moduleModel = $recordModel->getModule();
        $viewer = $this->getViewer($request);

        $viewer->assign('MODULE_SUMMARY', $this->showModuleSummaryView($request));

        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));

        return $viewer->view('DetailViewFullContents.tpl', $moduleName, true);

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


}
