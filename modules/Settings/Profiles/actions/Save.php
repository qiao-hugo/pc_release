<?php
/*+********
 * 权限配置新增与编辑
 * Edit By Joe @20150418
 *********/

class Settings_Profiles_Save_Action extends Vtiger_Action_Controller {
	
	public function checkPermission(Vtiger_Request $request) {
		$currentUser = Users_Record_Model::getCurrentUserModel();
		if(!$currentUser->isAdminUser()) {
			throw new AppException('LBL_PERMISSION_DENIED');
		}
	}

	public function process(Vtiger_Request $request) {
		$recordId = $request->get('record');
		if(!empty($recordId)) {
			$recordModel = Settings_Profiles_Record_Model::getInstanceById($recordId);
		} else {
			$recordModel = new Settings_Profiles_Record_Model();
		}
		if($recordModel) {
			$recordModel->set('profilename', $request->get('profilename'));
			$recordModel->set('description', $request->get('description'));
			//全局的编辑和查看权限[暂未开放]
			$recordModel->set('viewall', $request->get('viewall'));
			$recordModel->set('editall', $request->get('editall'));
			//模块权限数组[注意此处提交表单量会超过php默认配置max_input_vars需修改才能完成]
			$recordModel->set('profile_permissions', $request->get('permissions'));
			$recordModel->save();
		}
		
		$redirectUrl = $recordModel->getDetailViewUrl();
		header("Location: $redirectUrl");
	}
}
