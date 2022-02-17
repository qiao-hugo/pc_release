<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Schoolemploy_Detail_View extends Vtiger_Detail_View {


    function process(Vtiger_Request $request) {
        $mode = $request->getMode();   

        /*//根据关联参数执行
        if(!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);
            return;
        }
        $currentUserModel = Users_Record_Model::getCurrentUserModel();*/
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

        // 获取录用人员信息
        $schooladoptpeople = $this->getSchoolemploy($recordId);
        
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

        $viewer->assign('IS_ADDUSER', $this->is_addUsers());
        //echo 111;die;
        // 上面的都是在 vtiger_detail_view 的 showModuleDetailView方法copy的
        // 取出增值模块的详细信息
        $viewer->assign('MOREINVOICES', Recharge_Record_Model::getRechargeDetails($recordId));
        //$viewer->assign('MOREINVOICES', Invoice_Record_Model::getMoreinvoice($recordId));
        //$viewer->assign('MOREINVOICES', array('a'=>'1', 'b'=>'2'));
        return $viewer->view('DetailViewFullContents.tpl', $moduleName, true);
    }

    // 判断是否有 添加用户的权限
    function is_addUsers () {
        global $current_user, $adb;
        if ($current_user->id == '1') {
            return '1';
        }
        if ($current_user->is_admin == 'on') { // 1. 必须是管理员
            $sql = "SELECT id FROM vtiger_user2setting WHERE FIND_IN_SET(?, userid) AND FIND_IN_SET(1, setting)"; // 必须有添加用户的模块
            $sel_result = $adb->pquery($sql, array($current_user->id));
            $res_cnt = $adb->num_rows($sel_result);
            if($res_cnt > 0) {
                return '1';
            }
        }
        return '0';
    }
    
    // 录用人员详情
    function getSchoolemploy($id) {
        $sql = "SELECT vtiger_schoolresume.schoolresumeid, vtiger_schoolresume.`name`, IF ( vtiger_schoolresume.gendertype = 'MALE', '男', '女') AS gendertype, vtiger_schoolresume.telephone, vtiger_schoolresume.email FROM vtiger_schoolemploypeople INNER JOIN vtiger_schoolresume ON vtiger_schoolresume.schoolresumeid=vtiger_schoolemploypeople.schoolresumeid WHERE vtiger_schoolemploypeople.schoolemployid=?";
        global $adb;
        $sel_result = $adb->pquery($sql, array($id));
        $res_cnt = $adb->num_rows($sel_result);
        $data = array();
        if($res_cnt > 0) {
            while($rawData=$adb->fetch_array($sel_result)) {
                // schoolresumeid 加密
                $rawData['t_schoolresumeid'] = base64_encode($rawData['schoolresumeid'] + 99999);
                $data[] = $rawData;
            }
        }

        return $data;
    }

}
