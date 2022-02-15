<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Schoolvisit_Detail_View extends Vtiger_Detail_View {


    function preProcess(Vtiger_Request $request) {
        $viewer = $this->getViewer($request);
        $viewer->assign('NO_SUMMARY', true);
        parent::preProcess($request);
    }

    function process(Vtiger_Request $request) {
        $mode = $request->getMode();   

        //根据关联参数执行
        if(!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);
            return;
        }
        
       
        /*$dd = Users_Record_Model::getCurrentUserModel();
        $pp = $dd->getAccessibleUsers();
        print_r($pp);die;*/
        echo $this->showModuleDetailView($request);  
    }

    function showDetailViewByMode($request) {
        $requestMode = $request->get('requestMode');
        if($requestMode == 'full') {
            return $this->showModuleDetailView($request);
        }
        return $this->showModuleBasicView($request);
    }


    /**
     * Function returns Inventory details
     * @param Vtiger_Request $request
     */
    function showModuleDetailView(Vtiger_Request $request) {
        //echo parent::showModuleDetailView($request);
        //echo $this->getWorkflowsM($request);
        global $current_user;
        global $adb;
        $recordId = $request->get('record');
        $moduleName = $request->getModule();
    
        if(!$this->record){
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }
    
    
        $recordModel = $this->record->getRecord();
    

        //print_r($recordModel->getId());die;
        echo $this->getWorkflowsM($request, $recordModel);
        
        $recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
        $structuredValues = $recordStrucure->getStructure();
        

        // 获取拜访单的签到信息
        $sql = "SELECT
                u.last_name,
                v.signtime,
                v.signaddress,
                v.visitsigntype,
                v.signnum,
                v.userid,
                IF(v.issign=1, '是', '否')  AS issign,
                IF(v.signnum=1, '一', '二')  AS signnum

            FROM
                vtiger_schoolvisitsign v
            LEFT JOIN vtiger_users u ON v.userid = u.id
            WHERE
                v.visitingorderid = ?
            ORDER BY visitsigntype,signnum
            ";
        $visitsingArr = array();
        $t_result = $adb->pquery($sql, array($recordId, $current_user->id));
        while($rawData = $adb->fetch_array($t_result)) {
            $visitsingArr[] = $rawData;
        }

        $t_data = array();
        foreach ($visitsingArr as $key=>$value) {
            $t_data[$value['userid']]['last_name'] = $value['last_name'];
            $t_data[$value['userid']]['visitsigntype'] = $value['visitsigntype'];
            $t_data[$value['userid']]['data'][] = $value;
        }
        // 计算两次签到的时间
        foreach ($t_data as $key=>$value) {
            $signData = $value['data'];
            if (count($signData) == 2) {
                $signTime1 = $signData[0]['signtime']; //第一次签到时间
                $signTime2 = $signData[1]['signtime']; //第二次签到时间
                $diffTime = strtotime($signTime2) - strtotime($signTime1);
                
                $day    = floor($diffTime / 60 / 60 / 24);
                $diffTime  -= $day * 60 * 60 * 24;
                $hour   = floor($diffTime / 60 / 60);
                $diffTime  -= $hour * 60 * 60;
                $minute = floor($diffTime / 60);
                
                if($day > 0) {
                    $t_data[$key]['diffTime'] = $day.'天'.$hour.'小时'.$minute.'分钟';
                } else {
                    $t_data[$key]['diffTime'] = $hour.'小时'.$minute.'分钟';
                }
            }
        }

        //

        $moduleModel = $recordModel->getModule();
        $viewer = $this->getViewer($request);
        $viewer->assign('followdata', $followdata);
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        
        $viewer->assign('VISITSINGS', $t_data);
        return $viewer->view('DetailViewFullContents.tpl',$moduleName,true);
    }





    function getWorkflowsM(Vtiger_Request $request, $recordModel){
        $moduleName = $request->getModule();
        $viewer = $this->getViewer($request);
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('ModuleName',$moduleName); //工作流stagesid
        return $viewer->view('LineItemsWorkflowsM.tpl', $moduleName,true);
    }
    


}
