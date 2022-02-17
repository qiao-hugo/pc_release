<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class RechargePlatform_BasicAjax_Action extends Vtiger_Action_Controller {
	function __construct() {
		parent::__construct();
		$this->exposeMethod('checkplatformname');
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

    // 验证充值平台名称重复
    /**
     * @param Vtiger_Request $request
     */
    function checkplatformname(Vtiger_Request $request) {
        $platformname = $request->get("platformname");
        $db=PearDatabase::getInstance();

        $sql='SELECT 1 FROM vtiger_topplatform WHERE topplatform=? LIMIT 1';
        $result = $db->pquery($sql,array($platformname));
        $count = $db->num_rows($result);

        $response = new Vtiger_Response();
        $response->setResult($count);
        $response->emit();
    }
}
