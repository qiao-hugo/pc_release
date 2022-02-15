<?php

class IndicatorSetting_Detail_View extends Vtiger_Detail_View
{
    protected $record = false;
    function __construct() {
        parent::__construct();
        $this->exposeMethod('showModuleBasicView');
        $this->exposeMethod('showRecentActivities');
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
        $operations = IndicatorSetting_Module_Model::$operations;
        $viewer->assign('OPERATIONS',$operations);
        $recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
        $structuredValues = $recordStrucure->getStructure();
        $moduleModel = $recordModel->getModule();
        $viewer->assign('SPECIAL_OPERATORS', IndicatorSetting_Record_Model::getSpecialOperationByIndicatorSettingId($recordId));

        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        echo $viewer->view('DetailViewSummaryContents.tpl', $moduleName, true);
    }

    /**
     * Function returns recent changes made on the record
     * @param Vtiger_Request $request
     */
    function showRecentActivities (Vtiger_Request $request) {
        $parentRecordId = $request->get('record');
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


        $recentActivities = IndicatorSetting_Record_Model::getUpdates($parentRecordId, $pagingModel);
        $pagingModel->calculatePageRange($recentActivities);

        $viewer = $this->getViewer($request);
        $viewer->assign('RECENT_ACTIVITIES', $recentActivities);
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('PAGING_MODEL', $pagingModel);

        echo $viewer->view('RecentActivities.tpl', $moduleName, 'true');
    }
}