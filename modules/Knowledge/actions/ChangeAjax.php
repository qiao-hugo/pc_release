<?php

class Knowledge_ChangeAjax_Action extends Vtiger_Action_Controller {

    function __construct(){
        parent::__construct();
        $this->exposeMethod('shelf');
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

    public function shelf(Vtiger_Request $request){
        $recordId = $request->get('id');
        $status = $request->get('status');
        global $adb;
        $sql = "select 1 from vtiger_knowledge where  knowledgeid= ? ";
        $result = $adb->pquery($sql,array($recordId));
        $response = new Vtiger_Response();

        if($adb->num_rows($result)){
            $adb->pquery("update vtiger_knowledge set status = ? where knowledgeid=?",array($status,$recordId));
            $response->setResult(array('msg'=>'下架成功'));
            $response->emit();
            exit();
        }
        $response->setError(-1, '下架失败');
        $response->emit();
    }
}
