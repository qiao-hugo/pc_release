<?php
class Users_Detail_View extends Users_PreferenceDetail_View {

	public function preProcess(Vtiger_Request $request) {
		//非后台访问跳转 By Joe @20150506
		$parent=$request->get('parent');
		if(empty($parent)){
			header('Location:'.$_SERVER['REQUEST_URI'].'&parent=Settings');
			exit;
		}
		parent::preProcess($request, false);
		$this->preProcessSettings($request);
	}

	public function preProcessSettings(Vtiger_Request $request) {
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
		
		$viewer->assign('SELECTED_FIELDID',$fieldId);
		$viewer->assign('SELECTED_MENU', $selectedMenu);
		$viewer->assign('SETTINGS_MENUS', $menuModels);
		$viewer->assign('MODULE', $moduleName);
		$viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
        $viewer->assign('LOAD_OLD', Settings_Vtiger_Index_View::$loadOlderSettingUi);
		$viewer->view('SettingsMenuStart.tpl', $qualifiedModuleName);
	}

	public function postProcessSettings(Vtiger_Request $request) {
		$viewer = $this->getViewer($request);
		$qualifiedModuleName = $request->getModule(false);
		$viewer->view('SettingsMenuEnd.tpl', $qualifiedModuleName);
	}

	public function postProcess(Vtiger_Request $request) {
		$this->postProcessSettings($request);
		parent::postProcess($request);
	}
	//详细显示直属员工 By Joe@20150417
	public function process(Vtiger_Request $request) {
        $view=$request->get("view");
        if($view=='Detail') {

            $parentRecordId = $request->get('record');
            $pageNumber = $request->get('page');
            $limit = $request->get('limit');
            $moduleName = $request->getModule();

            if (empty($pageNumber) || $pageNumber == 'undefined') {
                $pageNumber = 1;
            }

            $pagingModel = new Vtiger_Paging_Model();
            $pagingModel->set('page', $pageNumber);
            if (!empty($limit)) {
                $pagingModel->set('limit', $limit);
            }
	    if(!$this->record){
                $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $parentRecordId);
            }
            $recordModel = $this->record->getRecord();

            $recentActivities = ModTracker_Record_Model::getUpdates($parentRecordId, $pagingModel);
            $pagingModel->calculatePageRange($recentActivities);

            $viewer = $this->getViewer($request);
            $viewer->assign('RECENT_ACTIVITIES', $recentActivities);
	    $viewer->assign('EMAILD', $recordModel->entity->column_fields['email1']);
            $viewer->assign('MODULE_NAME', $moduleName);
            $viewer->assign('PAGING_MODEL', $pagingModel);
        }else{
            $viewer = $this->getViewer($request);
        }
        $viewer->assign('VIEWUS',$view);
		$viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
		$viewer->assign('MYUSER',getMyuser($request->get('record')));
		$viewer->view('UserViewHeader.tpl', $request->getModule());
		parent::process($request);
		
	}
	
	public function getHeaderScripts(Vtiger_Request $request) {
		$headerScriptInstances = parent::getHeaderScripts($request);
		$moduleName = $request->getModule();

		$jsFileNames = array(
			'modules.Settings.Vtiger.resources.Index'
		);

		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
	}
	
}
