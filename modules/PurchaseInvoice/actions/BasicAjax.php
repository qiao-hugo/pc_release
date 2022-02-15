<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class PurchaseInvoice_BasicAjax_Action extends Vtiger_Action_Controller {
	function __construct() {
		parent::__construct();
		$this->exposeMethod('getvendorid');


	}
	
	
	function checkPermission(Vtiger_Request $request) {
		return;
	}

	/**
     * @param Vtiger_Request $request
     * @throws Exception
     */
	public function process(Vtiger_Request $request) {
		$mode = $request->getMode();
		if(!empty($mode)) {
			echo $this->invokeExposedMethod($mode, $request);
			return;
		}
	}

    /**
     * 合同的归还
     * @param Vtiger_Request $request
     * @throws Exception
     */
    public function getvendorid(Vtiger_Request $request){
        $record = $request->get('record');
        $sql = "SELECT vtiger_suppliercontracts.vendorid, vtiger_vendor.vendorname FROM vtiger_suppliercontracts LEFT JOIN vtiger_vendor ON vtiger_vendor.vendorid = vtiger_suppliercontracts.vendorid WHERE vtiger_suppliercontracts.suppliercontractsid = ? LIMIT 1";
        $db = PearDatabase::getInstance();
        $sel_result = $db->pquery($sql, array($record));
        $res_cnt = $db->num_rows($sel_result);
        $row = array();
        if($res_cnt > 0) {
            $row = $db->query_result_rowdata($sel_result, 0);
        }

        $response = new Vtiger_Response();
        $response->setResult($row);
        $response->emit();
        exit;
    }
    /***************END扫描枪识别不了带字母的合同编号暂时停用*************************/
}
