<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Schoolassessmentpeople_List_View extends Vtiger_KList_View {


	// Users_Privileges_Model::isPermitted('Newinvoice', 'NegativeEdit', $recordId)

	function __construct() {
        parent::__construct();
    }


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

        $viewer->assign('ACCESSIBLE_USERS',get_username_array());//人员

        $viewer->assign('VIEW', $request->get('view'));
        $viewer->assign('MODULE_MODEL', $moduleModel);

        // 是否有通过的权限
        //$flag = Users_Privileges_Model::isPermitted('Schoolassessmentpeople', 'NegativeEdit');
        $flag = true;
        global $current_user;
        $is_admin = $current_user->is_admin == 'on' ? 1 : 0;
        if ($flag || $is_admin) {
        	$viewer->assign('IS_CHECK', '1');
        }
        

        $viewer->view('ListViewContents.tpl', $moduleName);

    }

}