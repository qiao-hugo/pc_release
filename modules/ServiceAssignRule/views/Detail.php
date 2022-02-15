<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ServiceAssignRule_Detail_View extends Vtiger_Detail_View {
	protected function preProcessDisplay(Vtiger_Request $request) {
		$viewer = $this->getViewer($request);
		$recordId = $request->get('record');
		$viewer = $this->getViewer($request);
		//分配类型
		$viewer->assign('ASSIGN_TYPE', ServiceAssignRule_Record_Model::getAssignType($recordId));
		
		$displayed = $viewer->view($this->preProcessTplName($request), $request->getModule());
	}
	
	function showModuleBasicView($request) {
		return $this->showModuleDetailView($request);
	}
	
	/**
	 * Function returns Inventory details
	 * @param Vtiger_Request $request
	 */
	function showModuleDetailView(Vtiger_Request $request) {
		echo $this->getWorkflowsM($request);
		echo parent::showModuleDetailView($request);
	}

	function getWorkflowsM(Vtiger_Request $request){
		$moduleName = $request->getModule();
		$viewer = $this->getViewer($request);
		//return $viewer->view('FunctionOperation.tpl', $moduleName,true);          //在详细页面隐藏按钮“分配客服”
	}
}
