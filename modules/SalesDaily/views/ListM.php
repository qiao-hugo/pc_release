<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class SalesDaily_ListM_View extends Vtiger_List_View {
    function process (Vtiger_Request $request) {
        $recordModel=new SalesDaily_Record_Model();
        $departmentList=$recordModel->getDepartmentMonthList($request);
        $moduleName = $request->getModule();
        $viewer = $this->getViewer($request);
        $viewer->assign("DETAILLIST",$departmentList);
        $viewer->view('LineItemsDetailM.tpl', $moduleName);
        exit;

    }
}