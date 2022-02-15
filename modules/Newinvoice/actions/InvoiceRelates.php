<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Newinvoice_InvoiceRelates_Action extends Vtiger_Action_Controller {

	
	function checkPermission(Vtiger_Request $request) {
		return;
	}
    /**
     * 2015-4-30 steel 发票关联尾款
     * index.php?module=ServiceContracts&action=BasicAjax&record=合同id&mode=receivepay
     * @param Vtiger_Request $request
     * @throws Exception
     */
	public function receivepay(Vtiger_Request $request){
		$productid  = $request->get('record');
		$db=PearDatabase::getInstance();
		$result = $db->pquery("SELECT * from vtiger_receivedpayments where relatetoid=? ",array($productid));
		$row=$db->num_rows($result);
		$lis=array();
		if($row>1){
			for ($i=0; $i<$row; ++$i) {
				$li = $db->fetchByAssoc($result);
				$lis[]=$li;
			}
		}elseif($row==1){
			$lis[] = $db->query_result_rowdata($result);
		}
		$response = new Vtiger_Response();
		$response->setEmitType(Vtiger_Response::$EMIT_JSON);
		$response->setResult($lis);
		$response->emit();
	}

	/**
     * index.php?module=ServiceContracts&action=BasicAjax&record=合同id&mode=getsmownerid
	 * @author wangbin
	 * @see Vtiger_Action_Controller::getViewer()
	 * @param int accountid 客户id
	 * @return string 客户负责人id
	 */
	public function getsmownerid(Vtiger_Request $request){
	    $accountid = $request->get('record');
	    $db=PearDatabase::getInstance();//young 20150427 优化代码
	    $sql = 'SELECT (SELECT id FROM vtiger_users where id=vtiger_crmentity.smownerid) as id FROM vtiger_crmentity WHERE crmid = ?';
	    $smownerid = $db->pquery($sql,array($accountid));
	    if ($db->num_rows($smownerid) > 0) {
	        $data = $smownerid->fields['id'];
	    }

        if(empty($data)){
	        $data = Users_Record_Model::getCurrentUserModel()->column_fields['currency_id'];
	    }
	    $response = new Vtiger_Response();
	    $response->setResult($data);
	    $response->emit();
	}

    /**
     * ajax请求返回josn格式数据货币类型
     * @param Vtiger_Request $request
     */
    public function getcurrencytype(Vtiger_Request $request){
        $recordId = $request->get('record');//合同的id
        $db=PearDatabase::getInstance();
        $sql = 'SELECT currencytype FROM `vtiger_servicecontracts` WHERE servicecontractsid=?';
        $currencytype = $db->pquery($sql,array($recordId));

        if ($db->num_rows($currencytype) > 0) {
            $data = $currencytype->fields['currencytype'];
        }else{
            $data="";
        }
        //var_dump($currencytype);
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**读取当前合的所有的回款列表
     * @ruthor steel
     * @time 2015-05-04
     * @param Vtiger_Request $request
     * @throws Exception
     */
	public function process(Vtiger_Request $request) {

	    $recordId = $request->get('record');//合同的id
        global $current_user;
	    $db=PearDatabase::getInstance();

           /* $sql = "SELECT
                        (
                            SELECT
                                vtiger_account.accountname
                            FROM
                                vtiger_account
                            WHERE
                                vtiger_account.accountid = vtiger_receivedpayments.accountid
                        ) AS accountname,
                        IFNULL((
                            SELECT
                                last_name
                            FROM
                                vtiger_users
                            WHERE
                                id = vtiger_receivedpayments.createid
                        ),'--') AS createid,
                        IFNULL(
                            vtiger_servicecontracts.total,
                            '--'
                        ) AS total,
                        IFNULL(
                            vtiger_receivedpayments.standardmoney,
                            '--'
                        ) AS standardmoney,
                        IFNULL(
                            vtiger_receivedpayments.exchangerate,
                            '--'
                        ) AS exchangerate,
                        vtiger_servicecontracts.contract_no,
                        IFNULL(vtiger_servicecontracts.currencytype,'--') AS currencytype,
                        vtiger_receivedpayments.relmodule,
                        TRUNCATE(vtiger_receivedpayments.unit_price,2) AS unit_price,
                        IFNULL(vtiger_receivedpayments.reality_date,'--') AS reality_date,
                        IFNULL(
                            (
                                SELECT
                                    vtiger_invoice.invoice_no
                                FROM
                                    vtiger_invoice
                                WHERE
                                    vtiger_invoice.invoiceid = vtiger_newinvoicerelatedreceive.invoiceid
                            ),
                            '--'
                        ) AS invoice_no,
                        IFNULL(
                            (
                                SELECT
                                    vtiger_invoice.modulestatus
                                FROM
                                    vtiger_invoice
                                WHERE
                                    vtiger_invoice.invoiceid = vtiger_newinvoicerelatedreceive.invoiceid
                            ),
                            '--'
                        ) AS modulestatus,
                        vtiger_receivedpayments.accountid,
                        IFNULL(vtiger_receivedpayments.paytitle,'--') AS paytitle,
                        IFNULL(vtiger_newinvoicerelatedreceive.invoiceid,'--') AS invoiceid,
                        vtiger_receivedpayments.receivedpaymentsid AS receivedid
                    FROM
                        vtiger_receivedpayments
                    LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid = vtiger_receivedpayments.relatetoid
                    LEFT JOIN vtiger_newinvoicerelatedreceive ON vtiger_newinvoicerelatedreceive.receivedpaymentsid = vtiger_receivedpayments.receivedpaymentsid
                    WHERE
                        vtiger_receivedpayments.relatetoid = {$recordId}
                         ORDER BY invoice_no DESC,receivedid ASC";

	    $receivepaylist = $db->run_query_allrecords($sql);*/
        $query="SELECT vtiger_crmentity.* FROM vtiger_crmentity WHERE crmid=?";
        $result=$db->pquery($query,array($recordId));
        $resultdata=$db->query_result_rowdata($result);
        if($resultdata['setype']=='SupplierContracts'){
            $querylist="SELECT
                            vtiger_vendor.vendorname AS accountname,
                            vtiger_vendor.vendorid AS accountid,
                            vtiger_suppliercontracts.invoicecompany,
                            IFNULL(vtiger_suppliercontracts. billcontent,'暂无') AS billingcontent
                        FROM
                            vtiger_vendor
                        LEFT JOIN vtiger_suppliercontracts ON vtiger_vendor.vendorid = vtiger_suppliercontracts.vendorid
                        WHERE
                            vtiger_suppliercontracts.suppliercontractsid ={$recordId} ";
        }else{
            $querylist="SELECT
                            vtiger_account.accountname,
                            vtiger_account.accountid,
                            vtiger_servicecontracts.invoicecompany,
                            IFNULL(vtiger_servicecontracts. billcontent,'暂无') AS billingcontent
                        FROM
                            vtiger_account
                        LEFT JOIN vtiger_servicecontracts ON vtiger_account.accountid = vtiger_servicecontracts.sc_related_to
                        WHERE
                            vtiger_servicecontracts.servicecontractsid ={$recordId} ";
        }
        $accountname = $db->run_query_allrecords($querylist);
        /*$querybillcontent="SELECT
                                IFNULL(vtiger_contractsproductsrel.billingcontent,'') AS billingcontent
                            FROM
                                vtiger_contractsproductsrel
                            LEFT JOIN vtiger_contract_type ON vtiger_contract_type.contract_typeid=vtiger_contractsproductsrel.contract_type
                            LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.contract_type = vtiger_contract_type.contract_type
                            WHERE vtiger_servicecontracts.servicecontractsid={$recordId}";
        $billcontent = $db->run_query_allrecords($querybillcontent);*/

        /*$query="SELECT
                    IFNULL(vtiger_newinvoice.billingtime,'--') AS billingtime,
                    IFNULL(
                        vtiger_servicecontracts.contract_no,
                        '--'
                    ) AS contract_no,
                    IFNULL(vtiger_newinvoice.invoice_no,'') as invoice_no,
                    IFNULL(vtiger_newinvoice.companyname,'') AS companyname,
                    vtiger_crmentity.createdtime,
                    IFNULL(vtiger_newinvoice.accountnumber,'') AS accountnumber,
                    crmid,
                    vtiger_newinvoice.taxtotal,
                    IFNULL(
                        vtiger_receivedpayments.unit_price,
                        '--'
                    ) AS unit_price
                FROM
                    vtiger_newinvoice
                LEFT JOIN vtiger_crmentity ON vtiger_newinvoice.invoiceid = vtiger_crmentity.crmid
                LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid = vtiger_newinvoice.contractid
                LEFT JOIN vtiger_invoicerelatedreceive ON vtiger_invoicerelatedreceive.invoiceid = vtiger_newinvoice.invoiceid
                LEFT JOIN vtiger_receivedpayments ON vtiger_receivedpayments.receivedpaymentsid = vtiger_invoicerelatedreceive.receivedpaymentsid
                WHERE
                    (
                        vtiger_receivedpayments.unit_price IS NULL
                        OR vtiger_newinvoice.contractid IS NULL
                    )
                AND vtiger_crmentity.smownerid = {$current_user->id}";

        $receivepaynolist = $db->run_query_allrecords($query);*/
        $return=array('invoicecompany'=>$accountname[0]['invoicecompany'],'accountname'=>$accountname[0]['accountname'],'id'=>$accountname[0]['accountid'],'billcontent'=>$accountname[0]['billingcontent']);
        /*if(!empty($receivepaylist) || !empty($receivepaynolist)){
            $return=array_merge($return,array('resultlist'=>'yes'));
            if(!empty($receivepaylist)){
                $return=array_merge($return,array('markl'=>'yes','receivepaylist'=>$receivepaylist));
            }else{
                $return=array_merge($return,array('markl'=>'no'));
            }
            if(!empty($receivepaynolist)){
                $return=array_merge($return,array('marknl'=>'yes','receivepaynolist'=>$receivepaynolist));
            }else{
                $return=array_merge($return,array('marknl'=>'no'));
            }

        }else{
            $return=array_merge($return,array('resultlist'=>'no'));
        }*/

		$response = new Vtiger_Response();
		$response->setResult($return);
		$response->emit();
	}
}
