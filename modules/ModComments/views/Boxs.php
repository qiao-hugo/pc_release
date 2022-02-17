<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ModComments_Boxs_View extends Vtiger_IndexAjax_View {

	function __construct() {
		parent::__construct();
		$this->exposeMethod('setSubModComments');
	}
	public function process(Vtiger_Request $request) {
		$record = $request->get('record');
		$mode=$request->getMode();
		if(!empty($mode)){
			$this->invokeExposedMethod($mode,$request);
			return;
		}
		
		$moduleName = $request->getModule();
		$recordModel = ModComments_Record_Model::getInstanceById($record);
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		
		//print_r($recordModel->getData());die();
		
		$viewer = $this->getViewer($request);
		$viewer->assign('CURRENTUSER', $currentUserModel);
		$viewer->assign('COMMENT', $recordModel);
		echo $viewer->view('Comment.tpl', $moduleName, true);
	}
	//设置评论
	public function setSubModComments(Vtiger_Request $request){
		
		//跟进id
		$modcommentsid = $request->get('src_record');
		//评论id
		$record= $request->get('record');
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		
		//$srcRecordModel=ModComments_Record_Model::getInstanceById($modcommentsid);
		
		$recordModel = ModComments_Record_Model::getSubModcommentsById($record);
		$viewer = $this->getViewer($request);
		$viewer->assign('CURRENTUSER', $currentUserModel);
		$viewer->assign('COMMENT', $recordModel);
		$viewer->assign('RECORD', $record);
		$viewer->assign('Modcommentsid', $modcommentsid);
		//$viewer->assign('SCRIPTS', $this->getHeaderScripts($request));
        $relateModeule = $request->get('relateModule');
        $viewer->assign("ACCOUNTINTENTIONALITY",'');
        if($relateModeule=='Accounts'){
            $viewer->assign("ACCOUNTINTENTIONALITY",ModComments_Record_Model::getAccountIntentionality());
        }
		
		echo $viewer->view('SubComments.tpl', 'ModComments', true);
	}
	public function getHeaderScripts(Vtiger_Request $request){
		$jsFileNames = array(
				"modules.products.resources.Edit"
		);
		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		return $jsScriptInstances;
	}
	
	
	
}