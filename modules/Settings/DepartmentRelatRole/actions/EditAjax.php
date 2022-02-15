<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Class Settings_DepartmentRelatRole_EditAjax_Action extends Settings_Vtiger_IndexAjax_View {

	function __construct() {
		parent::__construct();
		$this->exposeMethod('checkDuplicate');
		$this->exposeMethod('dataChange');
		$this->exposeMethod('deletedData');
	}

	public function process(Vtiger_Request $request) {
		$mode = $request->get('mode');
		if(!empty($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}
	}

	public function checkDuplicate(Vtiger_Request $request) {
		$department = $request->get('department');
		$recordModel =new Settings_DepartmentRelatRole_Record_Model();
        $returndata=$recordModel->getDepartmentRelatRoleByDepartmentID($department);
		$response = new Vtiger_Response();
        $response->setResult(array('success' => $returndata));
		$response->emit();
	}
	public function dataChange(Vtiger_Request $request){
        $recordModel =new Settings_DepartmentRelatRole_Record_Model();
        $department = $request->get('department');
        if(!$recordModel->getDepartmentRelatRoleByDepartmentID($department)){
            $returndata=$recordModel->dataChange($request);
        }
        $response = new Vtiger_Response();
        $response->setResult(array('success' => $returndata));
        $response->emit();
    }
    public function deletedData(Vtiger_Request $request){
        $recordModel =new Settings_DepartmentRelatRole_Record_Model();
        $returndata=$recordModel->deletedData($request);
        $response = new Vtiger_Response();
        $response->setResult(array('success' => $returndata));
        $response->emit();
    }

}