<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class InputInvoice_List_View extends Vtiger_KList_View {

    function process (Vtiger_Request $request) {
        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
        $this->viewName = $request->get('viewname');
        $viewer->assign('VIEWNAME', $this->viewName);

        if ($request->isAjax()) {
            $this->initializeListViewContents($request, $viewer);//竟然调用两次，这边其实是ajax调用的，哈哈！！
            $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
        }

        $viewer->assign('VIEW', $request->get('view'));
        $viewer->assign('MODULE_MODEL', $moduleModel);

        $viewer->assign("OFFSETAMOUNT",0);
        $recordModel = InputInvoice_Record_Model::getCleanInstance($moduleName);
        global $current_user;
        if($recordModel->exportGrouprt($moduleName,'offsetamount',$current_user->id)){
            $viewer->assign("OFFSETAMOUNT",1);
        }
        $viewer->view('ListViewContents.tpl', $moduleName);

    }
}