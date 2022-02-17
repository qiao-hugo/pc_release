<?php
class Leads_Checkmob_Action extends Vtiger_Action_Controller {

	function checkPermission(Vtiger_Request $request) {
		return true;
	}

	public function process(Vtiger_Request $request) {
        $mobile=$request->get('mobile');
        $mobile = trim($mobile);
        $leadid=$request->get('leadId');
        $adb = PearDatabase::getInstance();
        $sql = "SELECT mobile FROM vtiger_leaddetails INNER JOIN vtiger_crmentity ON crmid = leadid INNER JOIN vtiger_leadaddress ON leadid = leadaddressid WHERE deleted = '0' AND mobile = ?";
        if($leadid>0){
            $sql .= ' AND leadid != ?';
           $result =  $adb->pquery($sql,array($mobile,$leadid));
        }else{
            $result = $adb->pquery($sql,array($mobile));
        }
        $num = $adb->num_rows($result);
        $pass=true;
        if($num>0){
            $pass=false;
        }
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($pass);
        $response->emit();
    }
}
