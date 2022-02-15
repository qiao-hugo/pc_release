<?php
class SupplierStatement_BasicAjax_Action extends Vtiger_BasicAjax_Action {
    public $stayPaymentWorkFlowSid = 2426467;  //代付款在线签收id
    function __construct() {
        parent::__construct();
        $this->exposeMethod('getservicecontractsinfo');
    }

    function checkPermission(Vtiger_Request $request) {
        return;
    }

    function getservicecontractsinfo(Vtiger_Request $request){
        $contractid = $request->get('record');
        $return = SupplierStatement_Record_Model::getaccinfoBYcontractid($contractid);
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
