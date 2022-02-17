<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Workflows_Detail_View extends Vtiger_Detail_View {
	
	public function __construct(){
		parent::__construct();
		$this->exposeMethod('getWorkflowStageHistory');//注册函数
	}
	function checkPermission(Vtiger_Request $request) {
		return true;
	}
	
	public function getWorkflowStageHistory(){
		//db
		
		
		echo '111';
		
		
		
	}
	
	
	
	public function getWorkflowsContent(Vtiger_Request $request){
		$moduleName = $request->get('module');
		$recordId = $request->get('record');
		
		
		$module = Vtiger_Record_Model::getInstanceById($recordId,$moduleName);
		$column=$module->getData();
		$notecontent=$column['notecontent'];
		
		$relateRecord = $request->get('relaterecord');
		if(!empty($relateRecord)){
			$Smodule = Vtiger_Record_Model::getInstanceById($relateRecord,'SalesOrder');
			$Scolumn=$Smodule->getData();
			$notecontent = $Scolumn['notecontent'];
		}
		
		$viewer = $this->getViewer($request);
		$viewer->assign('RECORD', $recordId);
		$viewer->assign('NOTECONTENT', $notecontent);
		$viewer->assign('ISCONTRACT', $column['iscontract']);
        $viewer->assign('ISCONTENT', $column['iscontent']);
		
		return $viewer->view('LineItemsEditNote.tpl', 'Workflows',true);
	}
}
?>
