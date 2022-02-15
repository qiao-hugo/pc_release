<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ContractGuarantee_BasicAjax_Action extends Vtiger_Action_Controller {
	function __construct() {
		parent::__construct();
		$this->exposeMethod('getAccountName');
        $this->exposeMethod('cancelContract');
        $this->exposeMethod('checkIsCanCancel');
        $this->exposeMethod('noNeedToExport');
        $this->exposeMethod('needToExport');
    }

    function checkPermission(Vtiger_Request $request) {
        return;
    }

	function getAccountName(Vtiger_Request $request){

	    $record = $request->get('record');
        $dataResult=array('flag'=>false);
        do {
            global $adb;
            $query='SELECT * FROM vtiger_crmentity WHERE deleted=0 AND crmid=?';
            $dataResult=$adb->pquery($query,array($record));
            if($adb->num_rows($dataResult)){
                $data=$adb->raw_query_result_rowdata($dataResult,0);
                $moduleName=$data['setype'];
                $recordModel = Vtiger_Record_Model::getInstanceById($record, $moduleName);
                if($moduleName=='ServiceContracts'){
                    $accountid=$recordModel->get('sc_related_to');
                }else{
                    $accountid=$recordModel->get('vendorid');
                }
                if($accountid){
                    $dataResult=$adb->pquery($query,array($accountid));
                    $data=$adb->raw_query_result_rowdata($dataResult,0);
                    $accountname=$data['label'];
                    $dataResult=array('flag'=>true,'accountid'=>$accountid,'accountname'=>$accountname);
                }

            }
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

    public function checkIsCanCancel(Vtiger_Request $request){

        $recordModel=Vtiger_Record_Model::getCleanInstance('ContractGuarantee');
        $data=$recordModel->checkIsCanCancel($request);
        //return $data;
        $response=new Vtiger_Response();
        $response->setResult($data);
        $response->emit();

    }
    /**
     * 消除服务合同
     * @param Vtiger_Request $request
     * @author  cxh
     */
    public function cancelContract(Vtiger_Request $request){

        $recordModel=Vtiger_Record_Model::getCleanInstance('ContractGuarantee');
        $data=$recordModel->cancelContract($request);
        //return $data;
        $response=new Vtiger_Response();
        $response->setResult($data);
        $response->emit();

    }
    /**
     * 设置不需要导出内容
     * @param Vtiger_Request $request
     */
    public function noNeedToExport(Vtiger_Request $request){
        $recordid=$request->get('record');
        $voidreason=$request->get('voidreason');
        global $current_user,$adb;
        //更新
        $currentTime = date('Y-m-d H:i:s');
        $adb->pquery("UPDATE vtiger_contractguarantee SET is_exportable='unable_export',unable_export_reason=? WHERE contractguaranteeid=?",array($voidreason,$recordid));
        //更新记录
        $id = $adb->getUniqueId('vtiger_modtracker_basic');
        $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
            array($id, $recordid, 'ContractGuarantee', $current_user->id,$currentTime, 0));
        $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?),(?,?,?,?)',
            Array($id, 'is_exportable','able_toexport','unable_export',$id, 'unable_export_reason','无',$voidreason));
        $data='';
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }
    /**
     * 设置需要导出内容
     * @param Vtiger_Request $request
     */
    public function needToExport(Vtiger_Request $request){
        $recordid=$request->get('record');
        global $current_user,$adb;
        $oldReason=$adb->pquery("SELECT *  FROM vtiger_contractguarantee WHERE contractguaranteeid=? ",array($recordid));
        $oldReason = $adb->query_result_rowdata($oldReason, 0);
        //更新
        $currentTime = date('Y-m-d H:i:s');
        $adb->pquery("UPDATE vtiger_contractguarantee SET is_exportable='able_toexport',unable_export_reason='' WHERE contractguaranteeid=?",array($recordid));
        //更新记录
        $id = $adb->getUniqueId('vtiger_modtracker_basic');
        $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
            array($id, $recordid, 'ContractGuarantee', $current_user->id,$currentTime, 0));
        $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?),(?,?,?,?)',
            Array($id, 'is_exportable','unable_export','able_toexport',$id, 'unable_export_reason',$oldReason['unable_export_reason'],'无'));
        $data='';
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

}
