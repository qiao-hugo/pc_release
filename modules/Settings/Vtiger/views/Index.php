<?php
/*+***************
 * 系统后台访问控制
 *
 *****************/

class Settings_Vtiger_Index_View extends Vtiger_Basic_View {

    //Variables which decalres whether the older setting need to be loaded or new one
    public static $loadOlderSettingUi = false;

	function __construct() {
		parent::__construct();
	}
	
	function checkPermission(Vtiger_Request $request) {
		//管理员可访问
		global $current_user,$adb;
        $userid= $current_user->id;
        $fieldid= $request->get('fieldid');
        if($fieldid==0){
            $modulename=$request->get('module');
            $settingsModel = Settings_Vtiger_Module_Model::getInstance();
            $fieldid=$settingsModel->getFieldId($modulename);
        }
        $result=$adb->pquery('SELECT id FROM `vtiger_user2setting` WHERE FIND_IN_SET(?,userid) AND FIND_IN_SET(?,setting)',array($userid,$fieldid));
        if($adb->num_rows($result)==0 && $current_user->is_admin!='on') {
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED', 'Vtiger'));
        }
		/*if($current_user->is_admin!='on') {
			throw new AppException('管理员才能访问！');
		}*/
		if(!$current_user->superadmin) {
			global $root_directory;
			$moduleName = $request->getModule(); 

			if($moduleName =='Vtiger'){
				return true;
			}
			include $root_directory.'crmcache/module2access.php';
			if(isset($module2access[$current_user->id]) && in_array($moduleName, $module2access[$current_user->id])){
				return true;
			}else{
				throw new AppException('没有访问权限！');
			}
		}



		

	}

	protected function transformToUI5URL(Vtiger_Request $request) {
		$params = 'module=Settings&action=index';

		if ($request->has('item')) {
			switch ($request->get('item')) {
				case 'LayoutEditor':
					$params = 'module=Settings&action=LayoutBlockList&parenttab=Settings&formodule='.$request->get('source_module');
					break;
				case 'EditWorkflows':
					$params = 'module=com_vtiger_workflow&action=workflowlist&list_module='.$request->get('source_module');
					break;
				case 'PicklistEditor':
					$params = 'module=PickList&action=PickList&parenttab=Settings&moduleName='.$request->get('source_module');
					break;
				case 'SMSServerConfig':
					$params = 'module='. $request->get('source_module').'&action=SMSConfigServer&parenttab=Settings&formodule='.$request->get('source_module');
					break;
				case 'CustomFieldList':
					$params = 'module=Settings&action=CustomFieldList&parenttab=Settings&formodule='.$request->get('source_module');
					break;
				case 'GroupDetailView':
					$params = 'module=Settings&action=GroupDetailView&groupId='.$request->get('groupId');
					break;
				case 'ModuleManager' :
					$params = 'module=Settings&action=ModuleManager&parenttab=Settings';
					break;
				case 'MailScanner':
					$params = 'module=Settings&action=MailScanner&parenttab=Settings';
					break;
				case 'WebForms':
					$params = 'module=Webforms&action=index&parenttab=Settings';
					break;
				case 'CustomFields' :
					$params = 'module=Settings&action=CustomFieldList&parenttab=Settings&formodule='.$request->get('source_module');
					break;
			}
		}
		return '../index.php?' . $params;
	}

	public function preProcess (Vtiger_Request $request) {
		parent::preProcess($request, false);
		$this->preProcessSettings($request);
	}

	public function preProcessSettings (Vtiger_Request $request) {

		$viewer = $this->getViewer($request);

		$moduleName = $request->getModule();
		$qualifiedModuleName = $request->getModule(false);
		$selectedMenuId = $request->get('block');
		$fieldId = $request->get('fieldid');
		$settingsModel = Settings_Vtiger_Module_Model::getInstance();
		
		$menuModels = $settingsModel->getMenus();
		
		if(!empty($selectedMenuId)) {
			$selectedMenu = Settings_Vtiger_Menu_Model::getInstanceById($selectedMenuId);
		} elseif(!empty($moduleName) && $moduleName != 'Vtiger') {
			$fieldItem = Settings_Vtiger_Index_View::getSelectedFieldFromModule($menuModels,$moduleName);
			if($fieldItem){
				$selectedMenu = Settings_Vtiger_Menu_Model::getInstanceById($fieldItem->get('blockid'));
				$fieldId = $fieldItem->get('fieldid');
			} else {
				reset($menuModels);
				$firstKey = key($menuModels);
				$selectedMenu = $menuModels[$firstKey];
			}
		} else {
			reset($menuModels);
			$firstKey = key($menuModels);
			$selectedMenu = $menuModels[$firstKey];
		}

		if(Settings_Vtiger_Index_View::$loadOlderSettingUi) {
            // Customization
            $viewer->assign('UI5_URL', $this->transformToUI5URL($request));
            // END
        }

		$viewer->assign('SELECTED_FIELDID',$fieldId);
		$viewer->assign('SELECTED_MENU', $selectedMenu);
		$viewer->assign('SETTINGS_MENUS', $menuModels);
		$viewer->assign('MODULE', $moduleName);
		$viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
        $viewer->assign('LOAD_OLD', Settings_Vtiger_Index_View::$loadOlderSettingUi);
		$viewer->view('SettingsMenuStart.tpl', $qualifiedModuleName);
	}

	public function postProcessSettings (Vtiger_Request $request) {
	
		$viewer = $this->getViewer($request);
		$qualifiedModuleName = $request->getModule(false);
		$viewer->view('SettingsMenuEnd.tpl', $qualifiedModuleName);
	}

	public function postProcess (Vtiger_Request $request) {
		
		$this->postProcessSettings($request);
		parent::postProcess($request);
	}

	public function process(Vtiger_Request $request) {
        if( !Settings_Vtiger_Index_View::$loadOlderSettingUi) {
            //NOTE: We plan to embed UI5 Settings until we are complete.
            $viewer = $this->getViewer($request);
            $qualifiedModuleName = $request->getModule(false);
			$usersCount = Users_Record_Model::getCount(true);
			//$activeWorkFlows = Settings_Workflows_Record_Model::getActiveCount();
			//$activeModules = Settings_ModuleManager_Module_Model::getModulesCount(true);
			$pinnedSettingsShortcuts = Settings_Vtiger_MenuItem_Model::getPinnedItems();

			$viewer->assign('USERS_COUNT',$usersCount);
			//$viewer->assign('ACTIVE_WORKFLOWS',$activeWorkFlows);
			//$viewer->assign('ACTIVE_MODULES',$activeModules);
			$viewer->assign('SETTINGS_SHORTCUTS',$pinnedSettingsShortcuts);
			$viewer->assign('MODULE',$qualifiedModuleName);
            $viewer->view('Index.tpl', $qualifiedModuleName);
        }
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
			"libraries/jquery/posabsolute-jQuery-Validation-Engine/$moduleName",
			'modules.Vtiger.resources.Vtiger',
			'modules.Settings.Vtiger.resources.Vtiger',
			'modules.Settings.Vtiger.resources.Edit',
			"modules.Settings.$moduleName.resources.$moduleName",
			'modules.Settings.Vtiger.resources.Index',
			"modules.Settings.$moduleName.resources.Index",
		);
		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
	}
	
	public static function getSelectedFieldFromModule($menuModels,$moduleName) {
		if($menuModels) {
			foreach($menuModels  as $menuModel) {
				$menuItems = $menuModel->getMenuItems();
				foreach($menuItems as $item) {
					$linkTo = $item->getUrl();
					if(stripos($linkTo, '&module='.$moduleName) !== false || stripos($linkTo, '?module='.$moduleName) !== false) {
						return $item;
					}
				}
			}
		}
		return false;
	}
}
