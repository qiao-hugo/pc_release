<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class SalesDaily_List_View extends Vtiger_KList_View {
    function process (Vtiger_Request $request) {
        $report=$request->get('report');
        if($report=='NoDaily'){
            $moduleName = $request->getModule();
            $viewer = $this->getViewer($request);
            $viewer->view('nodaily.tpl', $moduleName);
        }elseif($report=='MonthDaily'){
            $moduleName = $request->getModule();
            $recordModule=new SalesDaily_Record_Model();
            $departmentList=$recordModule->getDepartmentMonthList($request);

            $viewer = $this->getViewer($request);
            $viewer->assign('DEPARTMENT',getDepartment());
            $viewer->assign("DETAILLIST",$departmentList);
            $viewer->view('MonthDaily.tpl', $moduleName);
        }else{
            parent::process($request);
        }

    }
}