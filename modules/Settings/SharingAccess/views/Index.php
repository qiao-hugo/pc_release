<?php
/*+************
 *Edit By Joe
 * 模块共享规则
 *********/

Class Settings_SharingAccess_Index_View extends Settings_Vtiger_Index_View {

	public function process(Vtiger_Request $request) {
		$viewer = $this->getViewer ($request);
		$moduleName = $request->getModule();
		$qualifiedModuleName = $request->getModule(false);
        $viewer->assign('ALL_RULE_MEMBERS', Settings_SharingAccess_RuleMember_Model::getAll());
		//获取所有模块信息
		//vtiger_def_org_share中控制
		$viewer->assign('ALL_MODULES', Settings_SharingAccess_Module_Model::getAll(true));
		//获取模块操作规则
		$viewer->assign('ALL_ACTIONS', Settings_SharingAccess_Action_Model::getAll());
		$viewer->assign('MODULE', $moduleName);
		//$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
		$viewer->assign('DEPENDENT_MODULES', Settings_SharingAccess_Module_Model::getDependentModules());

		$viewer->view('Index.tpl', $qualifiedModuleName);
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
			'modules.Settings.Vtiger.resources.Index',
			"modules.Settings.$moduleName.resources.Index"
		);

		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
	}
}