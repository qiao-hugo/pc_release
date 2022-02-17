<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ContractActivaCode_BasicAjax_Action extends Vtiger_Action_Controller {
	function __construct() {
		parent::__construct();

		$this->exposeMethod('CAExportData');
	}
	
	function checkPermission(Vtiger_Request $request) {
		return;
	}
    /**
     * @param Vtiger_Request $request
     * @throws Exception
     */
	public function process(Vtiger_Request $request) {
		$mode = $request->getMode();
		if(!empty($mode)) {
			echo $this->invokeExposedMethod($mode, $request);
			return;
		}

	}
    /**
     * T云表格数据
     * @param Vtiger_Request $request
     */
    public function CAExportData(Vtiger_Request $request){
        $moduleModel=Vtiger_Module_Model::getInstance('ContractActivaCode');
        $moduleModel->CAExportDataExcel($request);
    }

}
