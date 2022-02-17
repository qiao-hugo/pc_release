<?php

class InputInvoice_Detail_View extends Vtiger_Detail_View
{
    protected $record = false;
    function __construct() {
        parent::__construct();
        $this->exposeMethod('showModuleBasicView');
    }


//    function process(Vtiger_Request $request)
//    {
//        echo $this->showModuleBasicView($request);
//    }

    /**
     * Function shows basic detail for the record
     * @param <type> $request
     */
    function showModuleBasicView(Vtiger_Request $request)
    {
        $recordId = $request->get('record');
        $moduleName = $request->getModule();
        if (!$this->record) {
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }

        echo $this->getWorkflowsM($request);

        $recordModel = $this->record->getRecord();

        $detailViewLinkParams = array('MODULE' => $moduleName, 'RECORD' => $recordId);
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
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        $billproperty = $recordModel->get("billproperty");
        $viewer->assign('BILLPROPERTY',$billproperty);
        $discountCoupon = $recordModel->discountCouponInfo($recordId);
//        var_dump($discountCoupon);die;
        $viewer->assign('DISCOUNTCOUPON',$discountCoupon);
        $viewer->assign('DISCOUNTCOUPONBOOL',empty($discountCoupon)?0:1);
        echo $viewer->view('DetailViewSummaryContents.tpl', $moduleName, true);
    }


}