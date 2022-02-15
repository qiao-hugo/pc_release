<?php

class Settings_Adminaccess_Save_Action extends Settings_Vtiger_Index_Action {

	public function process(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$qualifiedModuleName = $request->getModule(false);
		$recordId = $request->get('record');

		//$moduleModel = Settings_Vtiger_Module_Model::getInstance($qualifiedModuleName);
		if(!empty($recordId)) {
			$recordModel = Settings_Adminaccess_Record_Model::getInstance($recordId);
		} else {
			$recordModel = new Settings_Adminaccess_Record_Model();
		}
		if($recordModel) {
			$recordModel->set('groupname', decode_html($request->get('groupname')));
			$recordModel->set('description', $request->get('description'));
			$recordModel->set('userid', $request->get('members'));
			$recordModel->set('setting', $request->get('actions'));
			$recordModel->save();
		}

		$redirectUrl = $recordModel->getDetailViewUrl();
		header("Location: $redirectUrl");
	}
}
