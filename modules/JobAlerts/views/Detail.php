<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class JobAlerts_Detail_View extends Vtiger_Detail_View {
	function preProcess(Vtiger_Request $request) {
		$viewer = $this->getViewer($request);
		$viewer->assign('NO_SUMMARY', true);
        $List = $request->get('List');  //判断是否是从列表页面进入详情的
        if(isset($List) && $request->get('List') == 'List'){
            //添加提醒状态 1：已读:/0：未读  20150719/adatian
            $recordId = $request->get('record');
            $moduleName = $request->getModule();
            if(!$this->record){
                $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
            }

            $db = PearDatabase::getInstance();
            //先查询是否已读
            $sql="SELECT ownerid,state FROM `vtiger_jobalerts` WHERE jobalertsid =".$recordId."";
            $result = $db->pquery($sql);
            $state = $db->query_result($result,0,'state');
            $ownerid = $db->query_result($result,0,'ownerid');  //处理人

            global $current_user; //登录人
            if($state != 1 && $current_user->id == $ownerid ){
                $update_query="UPDATE vtiger_jobalerts SET state = 1 WHERE  jobalertsid =? and vtiger_jobalerts.ownerid=".$ownerid."";
                $db->pquery($update_query, array($recordId));
            }
        }


        parent::preProcess($request);
	}
	/**
	 * Function returns Inventory details
	 * @param Vtiger_Request $request
	 */
	function showModuleDetailView(Vtiger_Request $request) {
		$checkResult = JobAlerts_Record_Model::checkDisposePermission($request);
		if ($checkResult){
			echo $this->getWorkflowsM($request);
		}
		echo parent::showModuleDetailView($request);
	}
	
	/**
	 * Function returns Inventory details
	 * @param Vtiger_Request $request
	 * @return type
	 */
	function showDetailViewByMode(Vtiger_Request $request) {
		return $this->showModuleDetailView($request);
	}
	
	function showModuleBasicView($request) {
		return $this->showModuleDetailView($request);
	}
	
	function getWorkflowsM(Vtiger_Request $request){
		$recordModel = $this->record->getRecord();
		$moduleName = $request->getModule();
		$viewer = $this->getViewer($request);
		$viewer->assign('ALERT_STATUS', $recordModel->get("alertstatus"));
		$viewer->assign('MODULEID', $recordModel->get("moduleid"));
		$viewer->assign('MODULENAME', $recordModel->get("modulename"));
		return $viewer->view('FunctionOperation.tpl', $moduleName,true);
	}
}
	