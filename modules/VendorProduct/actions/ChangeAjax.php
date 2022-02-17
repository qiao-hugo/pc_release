<?php
class VendorProduct_ChangeAjax_Action extends Vtiger_Action_Controller {
    function __construct() {
        parent::__construct();
        $this->exposeMethod('checkIdAndProductProvider');
        $this->exposeMethod('Resubmit');
    }



	function checkPermission(Vtiger_Request $request) {
		return true;
	}

	public function process(Vtiger_Request $request) {
        $mode = $request->getMode();
        if(!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);
            return;
        }
	}
	public function checkIdAndProductProvider(Vtiger_Request $request){
        $recordModel=Vtiger_Record_Model::getCleanInstance('ProductProvider');
        $res['flag']=$recordModel->checkIdAndProductProvider($request);
        $response = new Vtiger_Response();
        $response->setResult($res);
        $response->emit();
    }
    public function Resubmit(Vtiger_Request $request){
	    $recordModel=Vtiger_Record_Model::getInstanceById($request->get('record'),'ProductProvider');
        $column_fields=$recordModel->entity->column_fields;
        global $current_user;
        if($column_fields['modulestatus']=='c_complete' && ($column_fields['assigned_user_id'] == $current_user->id ||$current_user->is_admin=='on')){
            $recordModel->doResubmit($request);
        }
    }
}
