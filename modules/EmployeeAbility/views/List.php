<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class EmployeeAbility_List_View extends Vtiger_KList_View {
	protected $listViewEntries = false;
	protected $listViewCount = false;
	protected $listViewLinks = false;
	protected $listViewHeaders = false;
	
	function __construct() {
		parent::__construct();
	}


	function process (Vtiger_Request $request) {
	    global $adb,$current_user;
        $moduleName = $request->getModule();
	    $moduleModel = EmployeeAbility_Module_Model::getCleanInstance($moduleName);
	    if($request->get("public")=='seteduurl'  && $moduleModel->exportGrouprt($moduleName,'seteduurl')){
            $viewer = $this->getViewer ($request);
            $sql = "select * from vtiger_employee_ability_column order by rank asc";
            $result = $adb->pquery($sql,array());
            while ($row = $adb->fetchByAssoc($result)){
                $data[] = $row;
            }
            $viewer->assign('DATAS', $data);
            $viewer->view('seteduurl.tpl', $moduleName);
            return;
        }

	    if(in_array($current_user->roleid,array('H82','H81')) && !$moduleModel->exportGrouprt($moduleName,'seteduurl')
            && !$moduleModel->exportGrouprt($moduleName,'employeeAbility')){
	        $result = $adb->pquery("select employeeabilityid,stafflevel from vtiger_employee_ability where userid=? limit 1",array($current_user->id));
            $row=array(
                'stafflevel'=>'junior'
            );
            if($adb->num_rows($result)){
                $row = $adb->fetchByAssoc($result,0);
            }
            $redirectUrl = 'index.php?module=EmployeeAbility&view=Detail&record='.$row['employeeabilityid'].'&stafflevel='.$row['stafflevel'];
            header("Location: $redirectUrl");
            exit();
        }
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

            $EmployeeRecordModel = EmployeeAbility_Record_Model::getCleanInstance("EmployeeAbility");
            $columns = $EmployeeRecordModel->getColumns();
            $viewer->assign('VIEW', $request->get('view'));
            $viewer->assign('MODULE_MODEL', $moduleModel);
            $viewer->assign('JUNIOR', $columns['junior']);
            $viewer->assign('INTERMEDIAE', $columns['intermediate']);
            $viewer->assign('SENIOR', $columns['senior']);

            if($changeHistory=='changeHistory'){
                $viewer->view('reportchangeowner.tpl', $moduleName);
            }else{
                $viewer->view('ListViewContents.tpl', $moduleName);
            }
        }
	}
}