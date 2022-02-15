<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Receipt_Detail_View extends Vtiger_Detail_View {

    function __construct(){
        parent::__construct();
    }
    function showModuleBasicView($request) {
        return $this->showModuleDetailView($request);
    }

    function showModuleDetailView(Vtiger_Request $request) {
        $recordId = $request->get('record');
        $moduleName = $request->getModule();

        global $isallow;
        if(in_array($moduleName, $isallow)){
            echo $this->getWorkflowsM($request);
        }
        if(!$this->record){
        $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }
        $recordModel = $this->record->getRecord();
        $recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
        $structuredValues = $recordStrucure->getStructure();
        $moduleModel = $recordModel->getModule();

        $adb =PearDatabase::getInstance();
        $sql = "select * FROM vtiger_receipt where receiptid=?";
        $sel_result = $adb->pquery($sql, array($recordId));
        $rawData = $adb->query_result_rowdata($sel_result, 0);
        $receivedpaymentsid = $rawData['receivedpaymentsid'];

        $sql = "select * FROM vtiger_receivedpayments where receivedpaymentsid=?";
        $sel_result = $adb->pquery($sql, array($receivedpaymentsid));
        $rawData = $adb->query_result_rowdata($sel_result, 0);
        $paytitle = $rawData['paytitle'];
        $owncompany = $rawData['owncompany'];
        $unit_price = $rawData['unit_price'];
        $reality_date = $rawData['reality_date'];

        $viewer = $this->getViewer($request);

        $viewer->assign('paytitle', $paytitle);
        $viewer->assign('owncompany', $owncompany);
        $viewer->assign('unit_price', $unit_price);
        $viewer->assign('reality_date', $reality_date);
        //获取回款关联
        if (!empty($recordId)) {
            $newinvoiceData = Receipt_Record_Model::getNewinvoicerayment($recordId);
            $viewer->assign('NEWINVOICEDATA', $newinvoiceData);
            $viewer->assign('IS_UPDATE_NEWINVOICERAYMENT', 1);
        }

        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));

        // 上面的都是在 vtiger_detail_view 的 showModuleDetailView方法copy的

        //$viewer->assign('MOREINVOICES', Invoice_Record_Model::getMoreinvoice($recordId));
        //$viewer->assign('MOREINVOICES', array('a'=>'1', 'b'=>'2'));
        return $viewer->view('DetailViewFullContents.tpl', $moduleName, true);
    }

}
