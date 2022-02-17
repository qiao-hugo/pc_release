<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/



class ContractReceivable_Record_Model extends Vtiger_Record_Model
{
    public function getExecutionDetailData(){
        global $adb;
        $contractreceivableid = $this->getId();
        if(empty($contractreceivableid)){
            return array();
        }
        $executionDetailData = array();
        $sql = 'select *,vtiger_contracts_execution.contractid from vtiger_contracts_execution_detail 
  left join vtiger_contracts_execution  on vtiger_contracts_execution_detail.contractexecutionid=vtiger_contracts_execution.contractexecutionid 
  left join vtiger_contract_receivable on vtiger_contract_receivable.contractid=vtiger_contracts_execution_detail.contractid
where vtiger_contract_receivable.contractreceivableid=? and vtiger_contracts_execution_detail.iscancel=0 order by vtiger_contracts_execution_detail.stage asc';
        $result = $adb->pquery($sql,array($contractreceivableid));
        $totalreceiveableamount = 0;
        $totalcontractreceivablebalance = 0;
        if($adb->num_rows($result)){
            while ($row=$adb->fetchByAssoc($result)){
                $voucher = explode('##',$row['voucher']);
                $row['voucher'] = $voucher[0];
                $row['voucherdownloadurl'] = "index.php?module=ContractExecution&action=DownloadFile&filename=".base64_encode($voucher[1]);
                $executionDetailData['detaildata'][] = $row;
                $totalreceiveableamount +=$row['receiveableamount'];
                $totalcontractreceivablebalance +=$row['contractreceivable'];

            }
        }
        $executionDetailData['totalreceiveableamount'] = number_format($totalreceiveableamount,2);
        $executionDetailData['totalcontractreceivablebalance'] = number_format($totalcontractreceivablebalance,2);
        return $executionDetailData;
    }

    public function getContractId(){
        $contractreceivableid = $this->getId();
        global $adb;
        $sql = "select contractid from vtiger_contract_receivable where contractreceivableid=? ";
        $result = $adb->pquery($sql,array($contractreceivableid));
        if($adb->num_rows($result)){
            $data = $adb->query_result_rowdata($result,0);
            return $data['contractid'];
        }
        return 0;
    }

    public function getStageList($contractid){
        global $adb;
        $sql = "select stageshow from vtiger_contracts_execution_detail where contractid=? and iscancel=0 order by stage asc";
        $result = $adb->pquery($sql,array($contractid));
        $stageList = array();
        if($adb->num_rows($result)){
            while ($row = $adb->fetchByAssoc($result)){
                $stageList[] = $row['stageshow'];
            }
        }
        return $stageList;

    }


    public function getAccountReceivedPayments(){
        global $adb;
        $contractid = $this->get('contractid');
        $sql = "select a.*,b.contract_no from vtiger_receivedpayments a left join vtiger_servicecontracts b on a.relatetoid=b.servicecontractsid where a.relatetoid=? and a.receivedstatus='normal'";
        $result = $adb->pquery($sql,array($contractid));
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

    public function isExist($contractid){
        global $adb;
        $sql = "select 1 from vtiger_contract_receivable where contractid = ? and iscancel=0";
        $result = $adb->pquery($sql,array($contractid));
        if($adb->num_rows($result)){
            return true;
        }
        return false;
    }
    /**
     * Ô¤¾¯ÉèÖÃ
     *
     */
    public function getEarlyWarningSettingData(){
        global $adb;
        $query='SELECT * FROM `vtiger_earlywarningsetting`';
        $result=$adb->pquery($query);
        $returnData=array();
        if($adb->num_rows($result)){
            while($row=$adb->fetchByAssoc($result)){
                $alertchannels=explode(',',$row['alertchannels']);
                $row['alertchannelsarr']=is_array($alertchannels)?$alertchannels:array();
                $returnData[$row['remindertype']]=$row;
            }
        }
        return $returnData;
    }

}