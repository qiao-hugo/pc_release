<?php

class SalesorderWorkflowStages_ChangeAjax_Action extends Vtiger_Action_Controller {

    function __construct(){
        parent::__construct();
        $this->exposeMethod('updateAuditorId');
    }

    function checkPermission(Vtiger_Request $request) {
        return true;
    }

    public function process(Vtiger_Request $request) {
        $mode=$request->getMode();
        if(!empty($mode)){
            echo $this->invokeExposedMethod($mode, $request);
            return;
        }
    }

    public function updateAuditorId(Vtiger_Request $request){
        $db = PearDatabase::getInstance();
        $db->pquery("update vtiger_salesorderworkflowstages set ishigher=1,higherid=? where salesorderworkflowstagesid=?",array($request->get('auditorid'),$request->get("salesorderworkflowstagesid")));
        echo json_encode(array("success"=>true,'msg'=>'修改成功'));
    }

}
