<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Staffcapacity_List_View extends Vtiger_KList_View {
	protected $listViewEntries = false;
	protected $listViewCount = false;
	protected $listViewLinks = false;
	protected $listViewHeaders = false;
	
	function __construct() {
		parent::__construct();
	}


	function process (Vtiger_Request $request) {
        $report = $request->get('report');
        $changeHistory = $request->get('filter');
        if($report==1){
            $moduleName = $request->getModule();
            $report1 = Accounts_Record_Model::getReport();
            $viewer = $this->getViewer ($request);
            $viewer->assign('REPORT', $report1);
            $viewer->view('report.tpl', $moduleName);
        }else {
            $viewer = $this->getViewer($request);
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);//module相关的数据
            $this->viewName = $request->get('viewname');
            $viewer->assign('VIEWNAME', $this->viewName);

            if ($request->isAjax()) {
                $this->initializeListViewContents($request, $viewer);//竟然调用两次，
                $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
            }


            $viewer->assign('VIEW', $request->get('view'));
            $viewer->assign('MODULE_MODEL', $moduleModel);


            if($changeHistory=='changeHistory'){
                $viewer->view('reportchangeowner.tpl', $moduleName);
            }else{
                $viewer->view('ListViewContents.tpl', $moduleName);
            }
        }
	}
}