<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Schoolrecruit_Detail_View extends Vtiger_Detail_View {


    function process(Vtiger_Request $request) {
        $recordId = $request->get('record');

        $viewer = $this->getViewer($request);
        $ttt = $this->isMakeSchoolqualifiedpeople($request);
        $viewer->assign('IS_MAKE_QUALIFIED',$ttt);//人员

        $mode = $request->getMode();   

        //根据关联参数执行
        if(!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);
            return;
        }

       
        $currentUserModel = Users_Record_Model::getCurrentUserModel();

        echo $this->showModuleDetailView($request);  
    }

    

    function showModuleBasicView($request) {
        $recordId = $request->get('record');
        $moduleName = $request->getModule();
        
        if(!$this->record){
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }
        $recordModel = $this->record->getRecord();
        
        $detailViewLinkParams = array('MODULE'=>$moduleName,'RECORD'=>$recordId);
        $detailViewLinks = $this->record->getDetailViewLinks($detailViewLinkParams);

        $viewer = $this->getViewer($request);
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('MODULE_SUMMARY', $this->showModuleSummaryView($request));

        $viewer->assign('DETAILVIEW_LINKS', $detailViewLinks);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        $viewer->assign('MODULE_NAME', $moduleName);

        $viewer->assign('recordId', $recordId);

        $accessibleUsers = get_username_array($where);  //人员信息
        $viewer->assign('ACCESSIBLE_USERS',$accessibleUsers);//人员
        
        $recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
        $structuredValues = $recordStrucure->getStructure();

        $moduleModel = $recordModel->getModule();
        
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        echo $viewer->view('DetailViewSummaryContents.tpl', $moduleName, true);
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
        
        $this->getSchoolrecruitsign($request);  // 获取签到

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
        // 
        //$viewer->assign('MOREINVOICES', Recharge_Record_Model::getRechargeDetails($recordId));
        //$viewer->assign('MOREINVOICES', Invoice_Record_Model::getMoreinvoice($recordId));
        //$viewer->assign('MOREINVOICES', array('a'=>'1', 'b'=>'2'));
        return $viewer->view('DetailViewFullContents.tpl', $moduleName, true);
    }


    // 判断是否生成了 合格简历名单
    public function isMakeSchoolqualifiedpeople(Vtiger_Request $request) {
        $recordid = $request->get('record');
        $sql = "select schoolqualifiedid from vtiger_schoolqualified where schoolrecruitid=?";
        global $adb;
        $sel_result = $adb->pquery($sql, array($recordid));
        $res_cnt = $adb->num_rows($sel_result);
        if ($res_cnt > 0) {
            return 1;
        }
        return 0;
    }


    // 获取签到
    public function getSchoolrecruitsign(Vtiger_Request $request) {
        $recordid = $request->get('record');
        global $adb;
        $sql = "SELECT *, (SELECT CONCAT( last_name, '[', IFNULL( ( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ) ) ) AS last_name FROM vtiger_users WHERE vtiger_schoolrecruitsign.userid = vtiger_users.id ) AS t_userid FROM vtiger_schoolrecruitsign WHERE schoolrecruitid=?";
        $sel_result = $adb->pquery($sql, array($recordid));
        $res_cnt = $adb->num_rows($sel_result);
        if ($res_cnt > 0) {

            $signdata1 = array(); // 负责人
            $signdata2 = array(); // 陪同人
            while($rawData=$adb->fetch_array($sel_result)) {
                if ($rawData['signtype'] == '负责人') {
                    $signdata1[] = $rawData;
                } else {
                    $signdata2[] = $rawData;
                }
            }

            $viewer = $this->getViewer($request);
            $viewer->assign('SIGNDATA1', $signdata1);
            $viewer->assign('SIGNDATA2', $signdata2);
        }
    }
}
