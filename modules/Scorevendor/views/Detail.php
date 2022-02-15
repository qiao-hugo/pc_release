<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Scorevendor_Detail_View extends Vtiger_Detail_View {
    function __construct() {
        parent::__construct();
        $this->exposeMethod('show_paper');
    }

    function process(Vtiger_Request $request) {
        $mode = $request->getMode();   

        //根据关联参数执行
        if(!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);
        }

        $public = $request->get('public');  
        if ($public == 'show_paper') {
            $this->show_paper($request);
            return;
        } else if($public == 'show_paper_log') {
            $this->show_paper_log($request);
            return;
        }
       
        $currentUserModel = Users_Record_Model::getCurrentUserModel();

        echo $this->showModuleDetailView($request);  
    }

    public function show_paper(Vtiger_Request $request) {
        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();
        $recordId = $request->get('record');

        if(!$this->record){
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }
        $recordModel = $this->record->getRecord();
        $entity = $recordModel->entity->column_fields;

        $scoreModelEntity = Scoremodel_Record_Model::getScoremodel($entity['scoremodelid']);
        //print_r($scoreModelEntity);die;
        $viewer->assign('ENTITY', $entity);
        $viewer->assign('SCOREMODELENTITY', $scoreModelEntity);
        //print_r($scoreModelEntity);die;

        echo $viewer->view('show_paper.tpl', $moduleName, true);
    }

    public function show_paper_log(Vtiger_Request $request) {
        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();
        $recordId = $request->get('record');

        $sql = "SELECT 
                vtiger_scoremodel.scoremodel_name,
                vtiger_scorevendor.scoretotal,
                vtiger_scorevendor.scorecontent
                FROM vtiger_scorevendor INNER JOIN vtiger_scoremodel
                ON vtiger_scorevendor.scoremodelid=vtiger_scoremodel.scoremodelid
                WHERE vtiger_scorevendor.scorevendorid=?";
        $db = PearDatabase::getInstance();
        $sel_result = $db->pquery($sql, array($recordId));
        $res_cnt = $db->num_rows($sel_result);
        if($res_cnt > 0) {
            $row = $db->query_result_rowdata($sel_result, 0);

            $scorecontent = Scoremodel_Record_Model::scoremodelContentDecode($row['scorecontent']);
            $row['scoremodel_content'] = $scorecontent;
            $viewer->assign('SCOREMODELENTITY', $row);
            $viewer->assign('IS_LOG', 1);
        }
        
        echo $viewer->view('show_paper.tpl', $moduleName, true);
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


        // 上面的都是在 vtiger_detail_view 的 showModuleDetailView方法copy的
        // 取出增值模块的详细信息
        $viewer->assign('MOREINVOICES', Recharge_Record_Model::getRechargeDetails($recordId));
        //$viewer->assign('MOREINVOICES', Invoice_Record_Model::getMoreinvoice($recordId));
        //$viewer->assign('MOREINVOICES', array('a'=>'1', 'b'=>'2'));
        return $viewer->view('DetailViewFullContents.tpl', $moduleName, true);
    }

    
}
