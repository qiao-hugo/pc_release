<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Newinvoice_Detail_View extends Inventory_Detail_View {
    function __construct() {
        parent::__construct();
    }

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

    /*function tovoid(Vtiger_Request $request) {  // 作废
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
        $viewer->assign('INVOICE_LIST',Newinvoice_Record_Model::inventoryList());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        $accessibleUsers = get_username_array('');
        $viewer->assign('IS_TOVOID', Users_Privileges_Model::isPermitted('Invoice', 'ToVoid', $recordId));
        $IS_FINANCE=Newinvoice_Record_Model::exportGroupri()&&$recordModel->entity->column_fields['modulestatus']=='c_complete';
        $viewer->assign('IS_FINANCE', $IS_FINANCE);
        $viewer->assign('IS_NEGATIVEEDIT', Users_Privileges_Model::isPermitted('Invoice', 'NegativeEdit', $recordId));
        $viewer->assign('ACCESSIBLE_USERS',$accessibleUsers);
        $viewer->assign('MOREINVOICES', Newinvoice_Record_Model::getMoreinvoice($recordId));
        $invoicetype = $recordModel->entity->column_fields['invoicetype']; 

        // 发票的状态
        $invoicestatus = $recordModel->entity->column_fields['invoicestatus']; 

        if ($invoicestatus != 'tovoid') {
            $newinvoiceData = Newinvoice_Record_Model::getNewinvoicerayment($recordId);
            $viewer->assign('NEWINVOICEDATA', $newinvoiceData);
        }
        return $viewer->view('TovoidViewBlockView.tpl',$moduleName,true);
    }
*/

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
        $viewer->assign('INVOICE_LIST',Newinvoice_Record_Model::inventoryList());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        $viewer->assign('UNLINKPAYMENT', $moduleModel->exportGrouprt("Newinvoice","unlinkPayment"));
	    $accessibleUsers = get_username_array('');

        // 获取红冲作废记录
        $newinvoicetovoid = $this->getNewinvoicetovoid($recordId, '2');
        $viewer->assign('NEWINVOICETOVOID', $newinvoicetovoid);
        // 获取直接作废的记录
        $newinvoicetovoid = $this->getNewinvoicetovoid($recordId, '1');
        $viewer->assign('NEWINVOICETOVOID_DIRECT', $newinvoicetovoid);

        //获取订单发票作废记录
        $newinvoiceordertovoid = $this->getNewinvoiceordertovoid($recordId, '1');
        $viewer->assign('NEWINVOICEORDERTOVOID_DIRECT', $newinvoiceordertovoid);
        // 作废权限
        $viewer->assign('IS_TOVOID', Users_Privileges_Model::isPermitted('Newinvoice', 'ToVoid', $recordId));

        // 工作流必须审核完成
        $IS_FINANCE=Newinvoice_Record_Model::exportGroupri()&&$recordModel->entity->column_fields['modulestatus']=='c_complete';
        $viewer->assign('IS_FINANCE', $IS_FINANCE);
        // 红冲权限
        $viewer->assign('IS_NEGATIVEEDIT', Users_Privileges_Model::isPermitted('Newinvoice', 'NegativeEdit', $recordId));

        $viewer->assign('ACCESSIBLE_USERS',$accessibleUsers);
        $viewer->assign('MOREINVOICES', Newinvoice_Record_Model::getMoreinvoice($recordId));


        $invoicetype = $recordModel->entity->column_fields['invoicetype']; 
        $account_id = $recordModel->entity->column_fields['account_id']; 
        $viewer->assign('INVOICETYPE', $invoicetype);
        $viewer->assign('t_invoicecompany', $recordModel->entity->column_fields['invoicecompany']);
        $viewer->assign('t_contractid', $recordModel->entity->column_fields['contractid']);
        $viewer->assign('t_modulestatus', $recordModel->entity->column_fields['modulestatus']);

        $newinvoiceData = Newinvoice_Record_Model::getNewinvoicerayment($recordId);
        $viewer->assign('NEWINVOICEDATA', $newinvoiceData);

        //获取订单渠道信息
        $dongchaliList=Newinvoice_Record_Model::getDongchaliListWithDeleted($recordId);
        $viewer->assign('DONGCHALILIST', $dongchaliList);
        $viewer->assign('billingsourcedata', $recordModel->entity->column_fields['billingsourcedata']);

        $viewer->assign('ACCOUNTID', $account_id);

        $tt = $recordModel->getWorkflowstagesflag($recordId);
        if($tt) {
            $viewer->assign('IS_ADD_NEWINVOICEAYMENT', 1);
        }
        // 
        global $current_user;
        $is_admin = $current_user->is_admin == 'on' ? 1 : 0;
        $viewer->assign('IS_ADMIN', $is_admin);

        $isVoidFlow=Newinvoice_Record_Model::isVoidFlow($recordId);
        $viewer->assign('IS_VOID_FLOW', $isVoidFlow);

        return $viewer->view('DetailViewFullContents.tpl',$moduleName,true);
    }


    public function getNewinvoicetovoid($record, $type='1') {
        global $adb; 
        $sql = "select *,(select surpluinvoicetotal from vtiger_newinvoicerayment where vtiger_newinvoicerayment.newinvoiceraymentid=vtiger_newinvoicetovoid.newinvoiceraymentid) AS surpluinvoicetotal from vtiger_newinvoicetovoid where type=? AND invoiceextendid IN (select invoiceextendid from vtiger_newinvoiceextend where invoiceid =?)";
        $sel_result = $adb->pquery($sql, array($type, $record));
        $res_cnt = $adb->num_rows($sel_result);
        $res = array();
        if($res_cnt > 0) {
            while($rawData=$adb->fetch_array($sel_result)) {
                $res[$rawData['invoiceextendid']][] = $rawData;
            }
        }
        return $res;
    }


    public function getNewinvoiceordertovoid($record, $type='1'){
        global $adb;
        $sql = "select ordercode,total,allowinvoicetotal,invoicetotal,tovoidtotal,invoiceextendid from vtiger_newinvoiceordertovoid where type=? AND invoiceextendid IN (select invoiceextendid from vtiger_newinvoiceextend where invoiceid =?)";
        $sel_result = $adb->pquery($sql, array($type, $record));
        $res_cnt = $adb->num_rows($sel_result);
        $res = array();
        if($res_cnt > 0) {
            while($rawData=$adb->fetch_array($sel_result)) {
                $res[$rawData['invoiceextendid']][] = $rawData;
            }
        }
        return $res;
    }
}
