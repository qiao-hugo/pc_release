<?php
/*+*********************
 * 保存权限分组前验证重复
 ************/

Class Settings_Profiles_EditAjax_Action extends Settings_Vtiger_IndexAjax_View {

	function __construct() {
		parent::__construct();
		$this->exposeMethod('checkDuplicate');
	}

	public function process(Vtiger_Request $request) {
		$mode = $request->get('mode');
		if(!empty($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}
	}

	public function checkDuplicate(Vtiger_Request $request) {
		$profileName = $request->get('profilename');
		$recordId = $request->get('record');
		$recordModel = Settings_Profiles_Record_Model::getInstanceByName($profileName, false, array($recordId));
		$response = new Vtiger_Response();
		if(!empty($recordModel)) {
			$response->setResult(array('success' => true,'message'=> '名称不能重复'));
		}else{
			$response->setResult(array('success' => false));
		}
		$response->emit();
	}

}