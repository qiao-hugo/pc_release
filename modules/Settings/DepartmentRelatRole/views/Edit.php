<?php
/*+**********
 * 用户群组设置
 * 
 *********/

Class Settings_DepartmentRelatRole_Edit_View extends Settings_Vtiger_Index_View {

	public function process(Vtiger_Request $request) {
		$viewer = $this->getViewer ($request);
		$moduleName = $request->getModule();
		$qualifiedModuleName = $request->getModule(false);
		$record = $request->get('record');

		if(!empty($record)) {
			$recordModel = Settings_DepartmentRelatRole_Record_Model::getInstance($record);
			$viewer->assign('MODE', 'edit');
		} else {
			$recordModel = new Settings_DepartmentRelatRole_Record_Model();
			$viewer->assign('MODE', '');
		}
		//目前只使用用户分组
		//$viewer->assign('MEMBER_GROUPS', Settings_Groups_Member_Model::getAll(false));
		$viewer->assign('RECORD_MODEL', $recordModel);
		$viewer->assign('RECORD_ID', $record);
		$viewer->assign('MODULE', $moduleName);
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());

		$viewer->view('EditView.tpl', $qualifiedModuleName);
	}

	/**
	 * Function to get the list of Script models to be included
	 * @param Vtiger_Request $request
	 * @return <Array> - List of Vtiger_JsScript_Model instances
	 */
	function getHeaderScripts(Vtiger_Request $request) {
		$headerScriptInstances = parent::getHeaderScripts($request);
		$moduleName = $request->getModule();

		$jsFileNames = array(
			"modules.Settings.$moduleName.resources.Edit"
		);

		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
	}
}