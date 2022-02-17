<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class VisitDepartment_Detail_View extends Vtiger_Detail_View {
	/* function preProcess(Vtiger_Request $request) {
		$viewer = $this->getViewer($request);
		$viewer->assign('NO_SUMMARY', true);
		parent::preProcess($request);
	} */
    function __construct(){
        parent::__construct();
        $this->exposeMethod('updateVdepartinfo');
        $this->exposeMethod('getVisitDImprovement');
    }
	/**
	 * Function returns Inventory details
	 * @param Vtiger_Request $request
	 * @return type
	 */
	/* function showDetailViewByMode(Vtiger_Request $request) {
		return $this->showModuleDetailView($request);
	} */
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
        $record = $request->get('record');

        $viewer = $this->getViewer($request);
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('RECORDCOMMENTANALYSIS', $recordModel->getVisitCommentanalysis($record));
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        return $viewer->view('DetailViewFullContents.tpl',$moduleName,true);
    }
	function showModuleBasicView($request) {
		return $this->showModuleDetailView($request);
	}
	public function updateVdepartinfo(Vtiger_Request $request){
        $recordId = $request->get('record');
        if($recordId>0){
            $moduleName = $request->getModule();

            if(!$this->record){
                $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
            }
            $recordModel = $this->record->getRecord();

            $record = $request->get('record');

            $viewer = $this->getViewer($request);
            $viewer->assign('RECORDCOMMENTANALYSIS', $recordModel->getVisitCommentanalysis($record));

            return $viewer->view('LineItemsDetail.tpl',$moduleName,true);
        }else{

            $moduleModel = Vtiger_Module_Model::getInstance('VisitDepartment');
            $viewer = $this->getViewer($request);
            $viewer->assign('RECORDCOMMENTANALYSIS', $moduleModel->getaddVCommentanalysis($request));

            return $viewer->view('LineItemsDetail.tpl','VisitDepartment',true);
        }
    }
    public function  getVisitDImprovement(Vtiger_Request $request){

        $recordId=$request->get('record');
        if(!$this->record){
            $this->record = Vtiger_DetailView_Model::getInstance('VisitDepartment', $recordId);
        }
        $result=$this->record->getRecord()->getVisitDImprovement($request);
        $viewer = $this->getViewer($request);
        $viewer->assign('CURRENTCOMMENT', $result);

        return $viewer->view('LineItemsComment.tpl', 'VisitDepartment', 'true');

    }
}
