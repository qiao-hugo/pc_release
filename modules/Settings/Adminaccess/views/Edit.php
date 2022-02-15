<?php
/*+**********
 * 后台权限编辑
 * 
 *********/

Class Settings_Adminaccess_Edit_View extends Settings_Vtiger_Index_View {

	public function process(Vtiger_Request $request) {

		$viewer = $this->getViewer ($request);
		$moduleName = $request->getModule();
		$qualifiedModuleName = $request->getModule(false);
		$record = $request->get('record');
		if(!empty($record)) {
			$recordModel = Settings_Adminaccess_Record_Model::getInstance($record);
			$viewer->assign('MODE', 'edit');
		} else {
			$recordModel = new Settings_Adminaccess_Record_Model();
			$viewer->assign('MODE', '');
		}
		$viewer->assign('RECORD_MODEL', $recordModel);
		$viewer->assign('RECORD_ID', $record);
		$viewer->assign('MODULE', $moduleName);
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
		$viewer->view('EditView.tpl', $qualifiedModuleName);
	}
}