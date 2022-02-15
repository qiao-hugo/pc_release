<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Accounts_List_View extends Vtiger_KList_View {
	protected $listViewEntries = false;
	protected $listViewCount = false;
	protected $listViewLinks = false;
	protected $listViewHeaders = false;
	
	function __construct() {
		parent::__construct();
	}


	function process (Vtiger_Request $request) {
        /*ini_set('display_errors','on'); error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
        $record=new Matchreceivements_BasicAjax_Action();
        $record->commonInsertAchievementallotStatistic(76394,$total=1580,$shareuser=0,$currentid=0,$contractid=2676867,$params=0);
        exit();// 测试代码内容
		*/
        $report = $request->get('report');
        $changeHistory = $request->get('filter');


        if($report==1){
            $moduleName = $request->getModule();
            $report1 = Accounts_Record_Model::getReport();
            $viewer = $this->getViewer ($request);
            $viewer->assign('REPORT', $report1);
            $viewer->view('report.tpl', $moduleName);
        }elseif($report==2){
            $moduleName = $request->getModule();
            $recordModel=Vtiger_Record_Model::getCleanInstance('Accounts');
            if(!$recordModel->personalAuthority('Accounts','highseasetting')){
                return ;
            }
            $viewer = $this->getViewer ($request);
            $viewer->view('CalendarView.tpl', $moduleName);
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
            global $current_user;
            //判断是否渠道用户
            /*$departmentdData = getChannelDepart();
            if (! in_array($current_user->departmentid, $departmentdData) ) {
                $viewer->assign('ISCHANNELDEPART', '1');
            }*/

            // 是否有垫款权限
            $viewer->assign('IS_ADVANCEMONY', Users_Privileges_Model::isPermitted('Accounts', 'ConvertLead'));
            if($changeHistory=='changeHistory'){
                $viewer->view('reportchangeowner.tpl', $moduleName);
            }else{
                $viewer->view('ListViewContents.tpl', $moduleName);
            }
        }
		
	}
}