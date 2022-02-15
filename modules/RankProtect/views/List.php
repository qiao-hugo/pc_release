<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class RankProtect_List_View extends Vtiger_List_View {
    function process (Vtiger_Request $request)
    {

        $filter = $request->get('filter');

        if ($filter == 'protected') {               //导出

            $moduleName = $request->getModule();
            $moduleModel=Vtiger_Module_Model::getInstance($moduleName);
            $viewer = $this->getViewer($request);
            $viewer->assign('USER',$moduleModel->getUserInfo());
            $viewer->assign('RECOEDS',$moduleModel->getProtectData());
            $viewer->view('protectedsetting.tpl', $moduleName);
            exit;
        }
        $moduleName = $request->getModule();
        $viewer = $this->getViewer($request);
        $viewer->assign('DEPARTMENT',getDepartment());
        $db=PearDatabase::getInstance();
        $staff_stage=$db->run_query_allrecords("SELECT * FROM  vtiger_staff_stage");
        $transformation=array('1'=>'duration_1_month','2'=>'duration_1_to_3_month','3'=>'duration_3_to_6_month','4'=>'6~12个月','5'=>'12个月以上');
        $viewer->assign("STAFFSTAGE",$staff_stage);
        $viewer->assign("TRANSFORMATION",$transformation);
        $accountrank=$db->run_query_allrecords("SELECT * FROM vtiger_accountrank");
        $viewer->assign("ACCOUNTRANK",$accountrank);
        $performancerank=$db->run_query_allrecords("SELECT * FROM vtiger_performancerank");
        $viewer->assign("PERFORMANCERANK",$performancerank);
        $viewer->assign('RECOEDS',RankProtect_Module_Model::getRankProtect());
        $viewer->view('rankprotectlist.tpl', $moduleName);
    }

}