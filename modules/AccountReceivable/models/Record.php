<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/



class AccountReceivable_Record_Model extends Vtiger_Record_Model
{
    public function getContractsAndExecutionByAccountid(){
        $accountid = $this->get('accountid');
        $db = PearDatabase::getInstance();
        $sql = "select 
       a.contract_no,
       a.contractid,
       d.accountname,
       b.invoicecompany,
       h.bussinesstype,
       b.productname as productname,
       concat(f.last_name,'[',g.departmentname,']') as signid,
       b.total,
       a.contractinvoiceamount,
       a.contractpaidamount,
       a.collectionstatus,
       b.signdate,
       b.frameworkcontract,
       b.modulestatus
  from vtiger_contract_receivable a 
  left join vtiger_servicecontracts b on a.contractid=b.servicecontractsid
  left join vtiger_account d on d.accountid=a.accountid
  left join vtiger_products e on e.productid=a.productid
  left join vtiger_users f on f.id=a.signid
  left join vtiger_departments g on g.departmentid=a.signdempart
  left join vtiger_contract_type h on h.contract_type=b.contract_type
where a.accountid=? and b.modulestatus!='c_cancel' and a.iscancel=0 group by contract_no";

        $result = $db->pquery($sql,array($accountid));
        $contractData=array();
        if($db->num_rows($result)){
            while ($row=$db->fetchByAssoc($result)){
                $ExecutionDetail = $this->getExecutionDetail( $row['contractid']);
                $row['executionDetailData'] = $ExecutionDetail['detaildata'];
                $row['totalreceiveableamount'] = $ExecutionDetail['totalreceiveableamount'];
                $row['totalcontractreceivablebalance'] = $ExecutionDetail['totalcontractreceivablebalance'];
                $contractData[$row['contractid']] = $row;
            }
        }
        return $contractData;
    }

    public function getExecutionDetail($contractid){
        if(empty($contractid)){
            return array();
        }
        $executionDetailData = array();
        global $adb;
        $sql = 'select *,vtiger_contracts_execution.contractid from vtiger_contracts_execution_detail left join vtiger_contracts_execution 
  on vtiger_contracts_execution_detail.contractexecutionid=vtiger_contracts_execution.contractexecutionid 
where vtiger_contracts_execution_detail.contractid=? and vtiger_contracts_execution_detail.iscancel=0 order by vtiger_contracts_execution_detail.stage asc';
        $result = $adb->pquery($sql,array($contractid));
        $totalreceiveableamount = 0;
        $totalcontractreceivablebalance = 0;
        if($adb->num_rows($result)){
            while ($row=$adb->fetchByAssoc($result)){
                $voucher = explode('##',$row['voucher']);
                $row['voucher'] = $voucher[0];
                $row['voucherdownloadurl'] = "index.php?module=ContractExecution&action=DownloadFile&filename=".base64_encode($voucher[1]);
                $executionDetailData[] = $row;
                $executionDetailData['detaildata'][] = $row;
                $totalreceiveableamount +=$row['receiveableamount'];
                $totalcontractreceivablebalance +=$row['contractreceivable'];
            }
        }
        $executionDetailData['totalreceiveableamount'] = number_format($totalreceiveableamount,2);
        $executionDetailData['totalcontractreceivablebalance'] = number_format($totalcontractreceivablebalance,2);
        return $executionDetailData;
    }

    public function getAccountReceivedPayments(){
        global $adb;
        $accountid = $this->get('accountid');

        $sql = "select a.*,b.contract_no from vtiger_receivedpayments a 
inner join vtiger_contract_receivable c on c.contractid = a.relatetoid
left join vtiger_servicecontracts b on a.relatetoid=b.servicecontractsid 
where a.receivedstatus='normal' and b.sc_related_to=? and b.modulestatus !='c_cancel'";
        $result = $adb->pquery($sql,array($accountid));
        $total_standard_money =0;
        $total_unit_price =0;
        if($adb->num_rows($result)){
            while ($row = $adb->fetchByAssoc($result)){
                $payments['contracts'][] = $row;
                $total_standard_money += $row['standardmoney'];
                $total_unit_price += $row['unit_price'];
            }
        }
        $payments['total_standard_money'] = number_format($total_standard_money,2);
        $payments['total_unit_price'] = number_format($total_unit_price,2);
        return $payments;
    }
}