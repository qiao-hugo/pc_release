<?php
class Staypayment_BasicAjax_Action extends Vtiger_Action_Controller {
    function __construct() {
        parent::__construct();
        $this->exposeMethod('getservicecontractsinfo');
    }

    function checkPermission(Vtiger_Request $request) {
        return;
    }

    function getservicecontractsinfo(Vtiger_Request $request){
        $contractid = $request->get('record');
        $return = Staypayment_Record_Model::getaccinfoBYcontractid($contractid);
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($return);
        $response->emit();
    }

    public function process(Vtiger_Request $request) {
        $mode = $request->getMode();
        if(!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);
            return;
        }
    }
}
