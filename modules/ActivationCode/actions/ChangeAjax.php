<?php
class ActivationCode_ChangeAjax_Action extends Vtiger_Action_Controller {

	function checkPermission(Vtiger_Request $request) {
		return true;
	}

	public function process(Vtiger_Request $request) {
		$contractid = $request->get('contractid');
        $activecode = $request->get('activecode');

        global $adb;
		$sql = 'select contractid from vtiger_activationcode where activecode=? AND contractid=?';
        $sel_result = $adb->pquery($sql, array($activecode, $contractid));
        $res_cnt = $adb->num_rows($sel_result);
        $flag = 1;
        if ($res_cnt > 0) {
            $flag = 0;
        }
        $response = new Vtiger_Response();
        $response->setResult(array('success'=>$flag, 'message'=>'激活码合同编号已存在'));
        $response->emit();
	}
}
