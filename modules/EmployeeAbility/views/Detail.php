<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class EmployeeAbility_Detail_View extends Vtiger_Detail_View {

    function preProcess(Vtiger_Request $request) {
        $viewer = $this->getViewer($request);
        $viewer->assign('NO_SUMMARY', true);
        parent::preProcess($request);
        if(!$request->get('record')){
            $moduleName = $request->getModule();
            $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
            global $current_user;
            $userid = $current_user->id;
            $departmentid = $current_user->departmentid;
            $employeeabilityid =  $recordModel->insertAbility($userid,$departmentid);
            $recordModel->insertAbilityDetail($employeeabilityid, $userid);
            $redirectUrl = 'index.php?module=EmployeeAbility&view=Detail&record='.$employeeabilityid;
            header("Location: $redirectUrl");
            exit();
        }
    }



    function process(Vtiger_Request $request) {
        $mode = $request->getMode();
        echo $this->showModuleDetailView($request);
    }
    /**
     * Function returns Inventory details
     * @param Vtiger_Request $request
     */
    function showModuleDetailView(Vtiger_Request $request) {
        $recordId = $request->get('record');
        $stafflevel = $request->get("stafflevel");
        $moduleName = $request->getModule();
        $moduleModel = EmployeeAbility_Module_Model::getCleanInstance($moduleName);


        $recordModel = Vtiger_Record_Model::getInstanceById($recordId,$moduleName);
        $a = $recordModel->defaultJson('junior');
        if(!$stafflevel){
            $stafflevel = $recordModel->staffLevel();
        }
        global $adb;
        $result = $adb->pquery("select userid from vtiger_employee_ability where employeeabilityid=? limit 1",array($recordId));
        $userid = 0;
        if($adb->num_rows($result)){
            $row = $adb->fetchByAssoc($result,0);
            $userid = $row['userid'];
        }
//        $userid= $recordModel->get('userid');
        $departmentid = $recordModel->get("departmentid");

        $contents = $recordModel->staffAbilityContentByLevel($recordId,$stafflevel);
        $viewer = $this->getViewer($request);
        $viewer->assign("STAFFLEVEL",$stafflevel);
        $viewer->assign('DATAS',$contents );
        $viewer->assign('RECORDID',$recordId );
        global $current_user;
        $viewer->assign('ISADMIN',0);
        $viewer->assign("COLLEAGECOLUMNS",$recordModel->getColleageColumns());
        $viewer->assign("SPECIALCOLLEAGECOLUMNS",$recordModel->getSpecialColumns());
        $viewer->assign('JOBHOLDER',$recordModel->getUserName($recordId));
        $viewer->assign('COLUMNDATA',$recordModel->getColumnInfo());

        $managerIds = array();
        //商务经理H80  商务总监H79
        if(in_array($current_user->id,getAllSuperiorIds($userid))){
            if($current_user->roleid=='H80'){
                array_push($managerIds,1);
            }
            if($current_user->roleid=='H79'){
                array_push($managerIds,2);

            }
        }

        if ($moduleModel->exportGrouprt('EmployeeAbility','employeeAbility')){
            array_push($managerIds,3);
        }
        $viewer->assign('ISMANAGER',$managerIds);

        return $viewer->view('DetailViewFullContents.tpl',$moduleName,true);
    }
}
