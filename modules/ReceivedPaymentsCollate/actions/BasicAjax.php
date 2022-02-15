<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ReceivedPaymentsCollate_BasicAjax_Action extends Vtiger_Action_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->exposeMethod('checkPaymentCode');
    }

    function checkPermission(Vtiger_Request $request)
    {
        return;
    }

    /**
     * @param Vtiger_Request $request
     * @throws Exception
     */
    public function process(Vtiger_Request $request)
    {
        $mode = $request->getMode();
        if (!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);
            return;
        }
    }

    /**
     * 删除回款
     * @param Vtiger_Request $request
     */
    public function checkPaymentCode(Vtiger_Request $request)
    {
        global $adb;
        $paymentcode = $request->get('paymentcode');
        $recordId = $request->get('record');
        $data = array('flag' => false, 'msg' => '');
        if ($paymentcode) {
            $sql = "select 1 from vtiger_receivedpayments where paymentcode=?";
            $result = $adb->pquery($sql, array($paymentcode));
            if ($adb->num_rows($result) > 0) {
                $data = array('flag' => true, 'paymentnum' => $adb->num_rows($result));
            } else {
                $data = array('flag' => false);
            }
        }
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

}
