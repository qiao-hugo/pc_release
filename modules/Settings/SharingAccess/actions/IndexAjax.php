<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Class Settings_SharingAccess_IndexAjax_Action extends Settings_Vtiger_IndexAjax_View {

	function __construct() {
		parent::__construct();
		$this->exposeMethod('saveRule');
        $this->exposeMethod('saveMultiRule');
		$this->exposeMethod('deleteRule');
	}

	public function process(Vtiger_Request $request) {
		$mode = $request->get('mode');
		if(!empty($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}
	}

	public function saveRule(Vtiger_Request $request) {
		$forModule = $request->get('for_module');
		$ruleId = $request->get('record');
		$companyid = $request->get('companyid');

		$moduleModel = Settings_SharingAccess_Module_Model::getInstance($forModule);
		if(empty($ruleId)) {
			$ruleModel = new Settings_SharingAccess_Rule_Model();
			$ruleModel->setModuleFromInstance($moduleModel);
		}else {
			$ruleModel = Settings_SharingAccess_Rule_Model::getInstance($moduleModel, $ruleId);
		}

		$ruleModel->set('companyid', implode(',',$companyid));
		$ruleModel->set('source_id', $request->get('source_id'));
		$ruleModel->set('target_id', $request->get('target_id'));
		$ruleModel->set('permission', $request->get('permission'));

		$response = new Vtiger_Response();
		$response->setEmitType(Vtiger_Response::$EMIT_JSON);
		try {
			$ruleModel->save();
		} catch (AppException $e) {
			$response->setError('Saving Sharing Access Rule failed');
		}
		$response->emit();
	}

    public function saveMultiRule(Vtiger_Request $request)
    {

        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $target_ids = $request->get('target_id');
        $source_id = $request->get('source_id');
        $permission = $request->get('permission');
        $forModules = $request->get('for_module');
        $company_id = $request->get('compay_id');
        $company_id=implode(',',$company_id);
        $target_ids=!empty($target_ids)?$target_ids:array('H372');
        try {

            foreach ($forModules as $forModule) {
                foreach ($target_ids as $target_id) {
                    $moduleModel = Settings_SharingAccess_Module_Model::getInstance($forModule);
                    $ruleModel = Settings_SharingAccess_Rule_Model::getRuleByShareRoleIdAndToDepartmentId($moduleModel,$source_id, $target_id);
                    if (!$ruleModel) {
                        $ruleModel = new Settings_SharingAccess_Rule_Model();
                        $ruleModel->setModuleFromInstance($moduleModel);
                    }

                    $ruleModel->set('source_id', $source_id);
                    $ruleModel->set('target_id', $target_id);
                    $ruleModel->set('companyid', $company_id);
                    $ruleModel->set('permission', $permission);
                    $ruleModel->save();
                }
            }
        } catch (AppException $e) {
            $response->setError('Saving Sharing Access Rule failed');
        }
        header('Location:index.php?module=SharingAccess&parent=Settings&view=Index&block=1&fieldid=5');
        $response->emit();
        exit();
    }

    public function deleteRule(Vtiger_Request $request)
    {
        $forModule = $request->get('for_module');
        $ruleId = $request->get('record');

		$moduleModel = Settings_SharingAccess_Module_Model::getInstance($forModule);
		$ruleModel = Settings_SharingAccess_Rule_Model::getInstance($moduleModel, $ruleId);

		$response = new Vtiger_Response();
		$response->setEmitType(Vtiger_Response::$EMIT_JSON);
		try {
			$ruleModel->delete();
		} catch (AppException $e) {
			$response->setError('Deleting Sharing Access Rule failed');
		}
		$response->emit();
	}
}