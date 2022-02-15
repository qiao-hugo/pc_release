<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Invoice_Detail_View extends Inventory_Detail_View {
    function process(Vtiger_Request $request) {

        $mode = $request->getMode();
        if(!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);
            return;
        }

        $currentUserModel = Users_Record_Model::getCurrentUserModel();

        if ($currentUserModel->get('default_record_view') === 'Summary') {
            echo $this->showModuleBasicView($request);
        } else {
            echo $this->showModuleDetailView($request);
        }

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
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        $viewer->assign('INVOICE_LIST',Invoice_Record_Model::inventoryList());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
	    $accessibleUsers = get_username_array('');
        $viewer->assign('IS_TOVOID', Users_Privileges_Model::isPermitted('Invoice', 'ToVoid', $recordId));
        $IS_FINANCE=Invoice_Record_Model::exportGroupri()&&$recordModel->entity->column_fields['modulestatus']=='c_complete';
        $viewer->assign('IS_FINANCE', $IS_FINANCE);
        $viewer->assign('IS_NEGATIVEEDIT', Users_Privileges_Model::isPermitted('Invoice', 'NegativeEdit', $recordId));
        $viewer->assign('ACCESSIBLE_USERS',$accessibleUsers);
        $viewer->assign('MOREINVOICES',Invoice_Record_Model::getMoreinvoice($recordId));


        return $viewer->view('DetailViewFullContents.tpl',$moduleName,true);
    }
}
