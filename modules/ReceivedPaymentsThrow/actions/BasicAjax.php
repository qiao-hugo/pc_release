<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ReceivedPaymentsThrow_BasicAjax_Action extends Vtiger_Action_Controller {
	function __construct() {
		parent::__construct();
	}
	
	function checkPermission(Vtiger_Request $request) {
		return true;
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

		$record = $request->get('record');

		$t = array('success'=>'1');
		if (!empty($record)) {
			$sql = "update vtiger_receivedpayments_throw set deleted=1 where id=?";
			$db = PearDatabase::getInstance();
			$db->pquery($sql, array($record));
		} else {
			$t['success'] = '0';
		}

		$response = new Vtiger_Response();
		$response->setResult($t);
		$response->emit();

	    //2015-1-23 wangbin 合并两次请求ajax
	    /*$recordId = $request->get('record');//合同的id
		$salesorderid = $request->get('salesorderid');//工单的id 编辑模式
	    $db=PearDatabase::getInstance();
		//查询合同下产品信息
		$sql = 'SELECT `productid`, `productform`, salesorderid, IFNULL(( SELECT vtiger_products.productname FROM vtiger_products WHERE vtiger_products.productid = vtiger_salesorderproductsrel.productcomboid ), \'--\' ) AS productcomboid FROM vtiger_salesorderproductsrel WHERE servicecontractsid =  ? AND (vtiger_salesorderproductsrel.multistatus=0 OR vtiger_salesorderproductsrel.multistatus=1)';
        $product = $db->pquery($sql,array($recordId));
	    $productids = $db->num_rows($product);

		$datas=array($productidlist,$return,$package,$isEditForm);
		$response = new Vtiger_Response();
		$response->setResult($datas);
		$response->emit();*/
	}
}
