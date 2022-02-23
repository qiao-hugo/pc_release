<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class SuppContractsAgreement_BasicAjax_Action extends Vtiger_Action_Controller {
	function __construct() {
		parent::__construct();
		$this->exposeMethod('getAccount');

	}

	function checkPermission(Vtiger_Request $request) {
		return;
	}

	function getAccount(Vtiger_Request $request){
	    $record = $request->get('record');
        $dataResult=array('flag'=>false);
        do {
            global $adb;
            $sql="select type from vtiger_suppliercontracts where suppliercontractsid=?";
            $result=$adb->pquery($sql,array($record));
            $type=$adb->query_result($result,0,'type');
            /*if($type=='cost'){
                $query="SELECT vtiger_suppliercontracts.type,vtiger_suppliercontracts.total,vtiger_suppliercontracts.contract_name,vtiger_suppliercontracts.paymentclause,vtiger_suppliercontracts.bankaccount,vtiger_suppliercontracts.bankname,vtiger_suppliercontracts.bankcode,vtiger_suppliercontracts.banknumber,vtiger_suppliercontracts.banklist FROM vtiger_suppliercontracts WHERE vtiger_suppliercontracts.suppliercontractsid=?";
            }else {
                $query="SELECT vtiger_suppliercontracts.type,vtiger_vendor.vendorid,vtiger_vendor.vendorname,vtiger_suppliercontracts.total,vtiger_suppliercontracts.contract_name,vtiger_suppliercontracts.paymentclause,vtiger_suppliercontracts.bankaccount,vtiger_suppliercontracts.bankname,vtiger_suppliercontracts.bankcode,vtiger_suppliercontracts.banknumber,vtiger_suppliercontracts.banklist FROM vtiger_vendor LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_vendor.vendorid
                    LEFT JOIN vtiger_suppliercontracts ON vtiger_suppliercontracts.vendorid=vtiger_vendor.vendorid
                    WHERE vtiger_crmentity.deleted=0  AND vtiger_suppliercontracts.suppliercontractsid=?";
            }*/

            $query="SELECT vtiger_suppliercontracts.type,vtiger_vendor.vendorid,vtiger_vendor.vendorname,vtiger_suppliercontracts.total,vtiger_suppliercontracts.contract_name,vtiger_suppliercontracts.paymentclause,
                           vtiger_suppliercontracts.bankaccount,vtiger_suppliercontracts.bankname,vtiger_suppliercontracts.bankcode,vtiger_suppliercontracts.banknumber,vtiger_suppliercontracts.banklist,
                           vtiger_suppliercontracts.invoicecompany
                    FROM vtiger_vendor 
                    LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_vendor.vendorid
                    LEFT JOIN vtiger_suppliercontracts ON vtiger_suppliercontracts.vendorid=vtiger_vendor.vendorid
                    WHERE vtiger_crmentity.deleted=0  AND vtiger_suppliercontracts.suppliercontractsid=?";

            $result=$adb->pquery($query,array($record));
            if(!$adb->num_rows($result)){
                break;
            }
            $row = $adb->query_result_rowdata($result, 0);
            $query="SELECT * FROM vtiger_vendorsrebate  WHERE  vtiger_vendorsrebate.deleted=0  AND vtiger_vendorsrebate.suppliercontractsid=? ";
            $result=$adb->pquery($query,array($record));
            $rebateList=array();
            while($rowData=$adb->fetch_array($result)){
                $rebateList[]=$rowData;
            }
            $dataResult=array('flag'=>true,'type'=>$row['type'],'rebateList'=>$rebateList,'accountid'=>$row['vendorid'],'accountname'=>$row['vendorname'],'total'=>$row['total'],'contract_name'=>$row['contract_name'],'paymentclause'=>$row['paymentclause'],
                'bankaccount'=>$row['bankaccount'],'bankname'=>$row['bankname'],'bankcode'=>$row['bankcode'],'banknumber'=>$row['banknumber'],'banklist'=>$row['banklist'],'invoicecompany'=>$row['invoicecompany']);
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
