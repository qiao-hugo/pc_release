<?php
/*+*******************
 * 异步加载设置记录
 ********************/

Class Settings_SharingAccess_IndexAjax_View extends Settings_Vtiger_IndexAjax_View {
	function __construct() {
		parent::__construct();
		$this->exposeMethod('showRules');
		$this->exposeMethod('editRule');
		$this->exposeMethod('newRule');
		$this->exposeMethod('createRule');
		$this->exposeMethod('searchRules');
	}

	public function process(Vtiger_Request $request) {
		$mode = $request->get('mode');
		if(!empty($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}
	}

	public function searchRules(Vtiger_Request $request){
        $viewer = $this->getViewer ($request);
        $moduleName = $request->getModule();
        $qualifiedModuleName = $request->getModule(false);
        $share_roleid = $request->get('share_roleid');
        $ruleModelList = Settings_SharingAccess_Rule_Model::getAllByShareRole($share_roleid);
        list($group_module_name,$group_id) = explode(':',$share_roleid);
        $group = Settings_Groups_Record_Model::getInstance($group_id);
        $viewer->assign('GROUP', $group);
        $viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
        $viewer->assign('RULE_MODEL_LIST',$ruleModelList);
        echo $viewer->view('SearchRules.tpl', $qualifiedModuleName, true);
    }

	public function showRules(Vtiger_Request $request) {
		$viewer = $this->getViewer ($request);
		$moduleName = $request->getModule();
		$qualifiedModuleName = $request->getModule(false);
		$forModule = $request->get('for_module');
		//获取选中模块信息
		$moduleModel = Settings_SharingAccess_Module_Model::getInstance($forModule);
		//vtiger_datashare_module_rel中读取已有配置
		$ruleModelList = Settings_SharingAccess_Rule_Model::getAllByModule($moduleModel);
		//$viewer->assign('ALL_RULE_MEMBERS', Settings_SharingAccess_RuleMember_Model::getAll());
		$viewer->assign('MODULE_MODEL', $moduleModel);
		$viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
		$viewer->assign('MODULE', $moduleName);
		$viewer->assign('FOR_MODULE', $forModule);
		$viewer->assign('RULE_MODEL_LIST', $ruleModelList);
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());

		echo $viewer->view('ListRules.tpl', $qualifiedModuleName, true);
	}

	public function newRule(Vtiger_Request $request){
        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();
        $qualifiedModuleName = $request->getModule(false);
        $forModule = $request->get('for_module');

        $moduleModel = Settings_SharingAccess_Module_Model::getInstance($forModule);

        $ruleModel = new Settings_SharingAccess_Rule_Model();
        $ruleModel->setModuleFromInstance($moduleModel);
        require 'crmcache/role.php';
        require 'crmcache/departmentanduserinfo.php';

        $viewer->assign('ALL_MODULES', Settings_SharingAccess_Module_Model::getAll(true));
        $viewer->assign('ALL_RULE_MEMBERS', Settings_SharingAccess_RuleMember_Model::getAll());
        //$viewer->assign('ALL_RULE_MEMBERS', $roles);
        $viewer->assign('DEPARTMENT',$departlevel);
        $viewer->assign('ALL_PERMISSIONS', Settings_SharingAccess_Rule_Model::$allPermissions);
        $viewer->assign('MODULE_MODEL', $moduleModel);
        $viewer->assign('RULE_MODEL', $ruleModel);
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('ALLCOMPLANY', Settings_SharingAccess_Module_Model::getCompayId());
        $viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        echo $viewer->view('NewRule.tpl',$qualifiedModuleName,true);
    }

	public function editRule(Vtiger_Request $request) {

		$viewer = $this->getViewer ($request);
		$moduleName = $request->getModule();
		$qualifiedModuleName = $request->getModule(false);
		$forModule = $request->get('for_module');
		$ruleId = $request->get('record');

		$moduleModel = Settings_SharingAccess_Module_Model::getInstance($forModule);
		if($ruleId) {
			$ruleModel = Settings_SharingAccess_Rule_Model::getInstance($moduleModel, $ruleId);
		} else {
			$ruleModel = new Settings_SharingAccess_Rule_Model();
			$ruleModel->setModuleFromInstance($moduleModel);
		}
		require 'crmcache/role.php';
		require 'crmcache/departmentanduserinfo.php';
		$viewer->assign('ALL_RULE_MEMBERS', Settings_SharingAccess_RuleMember_Model::getAll());
		//$viewer->assign('ALL_RULE_MEMBERS', $roles);
		$viewer->assign('DEPARTMENT',$departlevel);
		$viewer->assign('ALL_PERMISSIONS', Settings_SharingAccess_Rule_Model::$allPermissions);
		$viewer->assign('MODULE_MODEL', $moduleModel);
		$viewer->assign('RULE_MODEL', $ruleModel);
		$viewer->assign('MODULE', $moduleName);
        $viewer->assign('ALLCOMPLANY', Settings_SharingAccess_Module_Model::getCompayId());
		$viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());

		echo $viewer->view('EditRule.tpl', $qualifiedModuleName, true);
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