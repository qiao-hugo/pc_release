<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class SeparateInto_BasicAjax_Action extends Vtiger_BasicAjax_Action {

	function __construct() {
		parent::__construct();
		$this->exposeMethod('getServicecontractInfo');
		$this->exposeMethod('getShareInfo');


	}
	
	
	function checkPermission(Vtiger_Request $request) {
		return;
	}

	public function getShareInfo(Vtiger_Request $request){
	    $accountId=$request->get("record");
	    $recordModel = SeparateInto_Record_Model::getCleanInstance("SeparateInto");
        $shareInfo = $recordModel->getMarketingShareInfo($accountId);
        $dataResult=array('flag'=>false);
        if(count($shareInfo)){
            $dataResult['flag']=true;
            $dataResult['data']=$shareInfo;
        }
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($dataResult);
        $response->emit();
    }


    function getServicecontractInfo(Vtiger_Request $request){

        $record = $request->get('record');
        $dataResult=array('flag'=>false);
        do {
            $recordModel = Vtiger_Record_Model::getInstanceById($record, "ServiceContracts");
            $accountid=$recordModel->get('sc_related_to');
            $total=$recordModel->get('total');
            $signdate=$recordModel->get('signdate');
            $contract_type=$recordModel->get('contract_type');
            $accountname='';
            if($accountid){
                global  $adb;
                $query='SELECT accountname,frommarketing FROM vtiger_account WHERE accountid=? LIMIT 1';
                $dataResult=$adb->pquery($query,array($accountid));
                $data=$adb->raw_query_result_rowdata($dataResult,0);
                $accountname=$data['accountname'];

            }
            $dataResult=array('flag'=>true,'accountid'=>$accountid,'accountname'=>$accountname,"total"=>$total,'signdate'=>$signdate,'contract_type'=>$contract_type);
        }while(0);
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($dataResult);
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
