<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ActivationCode_BasicAjax_Action extends Vtiger_Action_Controller {
	function __construct() {
		parent::__construct();
		$this->exposeMethod('getTyunServiceItem');
        $this->exposeMethod('checkBuyInput');
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

    public function getTyunServiceItem(Vtiger_Request $request) {
        $recordModel=Vtiger_Record_Model::getCleanInstance('ActivationCode');
        $response = new Vtiger_Response();
        $response->setResult($recordModel->getTyunServiceItem($request));
        $response->emit();
    }

    public function checkBuyInput(Vtiger_Request $request) {
        $recordModel=Vtiger_Record_Model::getCleanInstance('ActivationCode');

        $result = $recordModel->checkBuyInput($request);
        /*if($result['success']){
            $result_check = $recordModel->checkBuyServiceChange($request,false);
            if(!$result_check['changeFlag']){
                $result = array('success'=>false, 'message'=>'您没有做任何修改');
            }
        }*/
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }

}
