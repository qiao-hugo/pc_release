<?php
/*+****
 *显示权限组的访问控制
 * 
 ******************/

class Settings_Profiles_Detail_View extends Settings_Vtiger_Index_View {

	public function process(Vtiger_Request $request) {
		$recordId = $request->get('record');
		$moduleName = $request->getModule();
		$qualifiedModuleName = $request->getModule(false);

		$recordModel = Settings_Profiles_Record_Model::getInstanceById($recordId);
		
		
		//$Profilemode=new Settings_Profiles_Record_Model; 
		
		
		
		$viewer = $this->getViewer($request);
		$viewer->assign('MODULE', $moduleName);
		$viewer->assign('RECORD_ID', $recordId);
		$viewer->assign('RECORD_MODEL', $recordModel);
		$viewer->assign('ALL_BASIC_ACTIONS', Vtiger_Action_Model::getAllBasic(true));
		$viewer->assign('PERMISSION_LIST', Vtiger_Action_Model::$authlist);
		//print_r(Vtiger_Action_Model::getAllBasic(true));
		//exit;
		$viewer->assign('ALL_UTILITY_ACTIONS', Vtiger_Action_Model::getAllUtility(true));
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());

		$viewer->view('DetailView.tpl', $qualifiedModuleName);
	}

}
