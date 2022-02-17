<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Schooleligibility_Detail_View extends Vtiger_Detail_View {


    function process(Vtiger_Request $request) {
        $mode = $request->getMode();   

        //根据关联参数执行
        if(!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);
            return;
        }

       
        $currentUserModel = Users_Record_Model::getCurrentUserModel();

        echo $this->showModuleDetailView($request);  
    }

    function showDetailViewByMode($request) {
        $requestMode = $request->get('requestMode');
        if($requestMode == 'full') {
            return $this->showModuleDetailView($request);
        }
        return $this->showModuleBasicView($request);
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

        // 获取简历合格人员信息
        $schooladoptpeople = $this->getSchooladoptpeople($recordId);
        

        

        $viewer = $this->getViewer($request);
        $viewer->assign('ACCESSIBLE_USERS',get_username_array());//人员
        $viewer->assign('recordId', $recordId);
        $viewer->assign('SCHOOLQUALIFIEDPEOPLE', $schooladoptpeople);
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        //echo 111;die;
        // 上面的都是在 vtiger_detail_view 的 showModuleDetailView方法copy的
        // 取出增值模块的详细信息
        $viewer->assign('MOREINVOICES', Recharge_Record_Model::getRechargeDetails($recordId));
        //$viewer->assign('MOREINVOICES', Invoice_Record_Model::getMoreinvoice($recordId));
        //$viewer->assign('MOREINVOICES', array('a'=>'1', 'b'=>'2'));
        return $viewer->view('DetailViewFullContents.tpl', $moduleName, true);
    }

    
    // 考核通过人员详情
    function getSchooladoptpeople($id) {
        $sql = "SELECT vtiger_schooladoptpeople.schooladoptpeopleid,vtiger_schooladoptpeople.schoolresumeid, vtiger_schoolresume.`name`, IF(vtiger_schoolresume.gendertype='MALE','男','女') AS gendertype, vtiger_schoolresume.telephone, vtiger_schoolresume.email, IF(vtiger_schooladoptpeople.assessmentresult='assessmentresult_yes','通过', IF( vtiger_schooladoptpeople.assessmentresult='assessmentresult_no','不通过','')) AS assessmentresult, vtiger_schooladoptpeople.assessmentdate, ( SELECT CONCAT( last_name, '[', IFNULL( ( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ) ) ) AS last_name FROM vtiger_users WHERE vtiger_schooladoptpeople.instructor = vtiger_users.id ) AS instructor FROM vtiger_schooladoptpeople INNER JOIN vtiger_schoolresume ON vtiger_schooladoptpeople.schoolresumeid=vtiger_schoolresume.schoolresumeid WHERE vtiger_schooladoptpeople.schooladoptid=?";
        global $adb;
        $sel_result = $adb->pquery($sql, array($id));
        $res_cnt = $adb->num_rows($sel_result);
        $data = array();
        if($res_cnt > 0) {
            while($rawData=$adb->fetch_array($sel_result)) {
                $data[] = $rawData;
            }
        }

        return $data;
    }

}
