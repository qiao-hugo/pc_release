<?php
class Authentication_BasicAjax_Action extends Vtiger_BasicAjax_Action {
    function __construct() {
        parent::__construct();
        $this->exposeMethod('deleteAuth');
    }

    function checkPermission(Vtiger_Request $request) {
        return;
    }

    public function process(Vtiger_Request $request) {
        $mode = $request->getMode();
        if(!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);
            return;
        }
    }


    public function deleteAuth(Vtiger_Request $request){
        global $adb;
        $recordId=$request->get('record');
        $sql="update vtiger_authentication set isdelete=1 where authenticationid=?";
        $adb->pquery($sql,array($recordId));
        $response = new Vtiger_Response();
        $response->setResult(array());
        $response->emit();
    }

}
