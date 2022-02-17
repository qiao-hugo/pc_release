<?php
class TyunUpgradeRule_ChangeAjax_Action extends Vtiger_Action_Controller {
    function __construct() {
        parent::__construct();
        $this->exposeMethod('checkProduct');
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

    /**
     * 检查当前产品当前的类型是否已经添加
     * @param Vtiger_Request $request
     */
    public function checkProduct(Vtiger_Request $request){
        $recordId = $request->get('record');//合同的id
        $data=false;
        if(empty($recordId)) {
            $recordModule = Vtiger_Record_Model::getCleanInstance('TyunUpgradeRule');
            $data = $recordModule->isRepeatTyunProduct($request);
        }
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }
}
