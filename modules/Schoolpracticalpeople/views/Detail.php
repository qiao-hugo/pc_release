<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Schoolpracticalpeople_Detail_View extends Vtiger_Detail_View {

    function __construct(){
        parent::__construct();
    }

    function process(Vtiger_Request $request) {;
        $this->getSchoolresumeInfo($request);
        $mode = $request->getMode();   
        //根据关联参数执行
        if(!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);
            return;
        }

       
        $currentUserModel = Users_Record_Model::getCurrentUserModel();

        echo $this->showModuleDetailView($request);  
    }

    function getSchoolresumeInfo(Vtiger_Request $request) {
        $recordId = $request->get('record');
        global $adb;
        $sql = "SELECT schoolresumeid FROM vtiger_schoolpracticalpeople WHERE schoolpracticalpeopleid=?";
        $sel_result = $adb->pquery($sql, array($recordId));
        $res_cnt    = $adb->num_rows($sel_result);
        if ($res_cnt > 0) {
            $row = $adb->query_result_rowdata($sel_result, 0);
            $schoolresume = Schoolresume_Record_Model::getSchoolresumeInfo($row['schoolresumeid']);
            $viewer = $this->getViewer($request);
            $viewer->assign('SCHOOL_RESUME', $schoolresume);
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
        //var_dump($recordStrucure);die;
        $structuredValues = $recordStrucure->getStructure();
        
        $accessibleUsers = get_username_array($where);  //人员信息

        $moduleModel = $recordModel->getModule();
        
        $viewer = $this->getViewer($request);
        $viewer->assign('ACCESSIBLE_USERS',$accessibleUsers);//人员
        
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));

        $viewer->assign('recordId', $recordId);
        // 上面的都是在 vtiger_detail_view 的 showModuleDetailView方法copy的
        // 取出增值模块的详细信息
        $viewer->assign('MOREINVOICES', Recharge_Record_Model::getRechargeDetails($recordId));
        //$viewer->assign('MOREINVOICES', Invoice_Record_Model::getMoreinvoice($recordId));
        //$viewer->assign('MOREINVOICES', array('a'=>'1', 'b'=>'2'));
        return $viewer->view('DetailViewFullContents.tpl', $moduleName, true);
    }
}
