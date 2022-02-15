<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/**
 * Inventory Record Model Class
 */
class ContractActivaCode_Record_Model extends Vtiger_Record_Model {
    /**
     * 取得发票列表
     * @param $servicecontractsid
     */
    public function getInvoiceList($servicecontractsid){
        $query="SELECT
                    vtiger_newinvoice.invoiceid,vtiger_newinvoiceextend.invoiceextendid,vtiger_newinvoiceextend.billingtimeextend,vtiger_newinvoiceextend.invoicecodeextend,vtiger_newinvoiceextend.invoice_noextend,vtiger_newinvoiceextend.commoditynameextend,vtiger_newinvoiceextend.totalandtaxextend,vtiger_newinvoiceextend.processstatus,vtiger_newinvoiceextend.invoicestatus,(SELECT CONCAT(last_name,'[',IFNULL((SELECT departmentname FROM vtiger_departments WHERE departmentid = (SELECT	departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ),''),']',(IF (`status` = 'Active','','[离职]'))) AS last_name FROM vtiger_users WHERE vtiger_newinvoiceextend.operator = vtiger_users.id) AS operator,vtiger_newinvoiceextend.operatortime
                FROM
                    vtiger_newinvoiceextend
                LEFT JOIN vtiger_newinvoice ON vtiger_newinvoice.invoiceid = vtiger_newinvoiceextend.invoiceid
                LEFT JOIN vtiger_crmentity ON vtiger_newinvoiceextend.invoiceid = vtiger_crmentity.crmid
                LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.sc_related_to=vtiger_newinvoice.accountid
                WHERE
                vtiger_crmentity.deleted = 0
                AND vtiger_newinvoice.modulestatus = 'c_complete'
                AND vtiger_newinvoiceextend.invoicestatus = 'normal'
                AND 
                vtiger_servicecontracts.servicecontractsid=?";
        $db = PearDatabase::getInstance();
        $result=$db->pquery($query,array($servicecontractsid));
        $temp=array();
        for($i=0; $i<$db->num_rows($result); $i++) {
            $invoiceid= $db->query_result($result, $i,'invoiceid');
            $invoiceextendid = $db->query_result($result, $i, 'invoiceextendid');
            $billingtimeextend =	$db->query_result($result, $i, 'billingtimeextend');
            $invoicecodeextend =	$db->query_result($result, $i, 'invoicecodeextend');
            $invoice_noextend =	$db->query_result($result, $i, 'invoice_noextend');
            $commoditynameextend =	$db->query_result($result, $i, 'commoditynameextend');
            $invoicestatus =$db->query_result($result, $i, 'invoicestatus');
            $operatortime =$db->query_result($result, $i, 'operatortime');
            $processstatus =$db->query_result($result, $i, 'processstatus');
            $operator =$db->query_result($result, $i, 'operator');
            $totalandtaxextend =$db->query_result($result, $i, 'totalandtaxextend');

            $temp[]=array('invoiceid'=>$invoiceid,'invoiceextendid'=>$invoiceextendid,'billingtimeextend'=>$billingtimeextend,'invoicecodeextend'=>$invoicecodeextend,'invoice_noextend'=>$invoice_noextend,'commoditynameextend'=>$commoditynameextend,'totalandtaxextend'=>$totalandtaxextend,'operatortime' =>$operatortime,'processstatus' =>$processstatus,'operator' =>$operator,'invoicestatus'=>$invoicestatus);
        }
        return $temp;
    }

    /**
     * 取得回款列表
     * @param $servicecontractsid
     */
    public function getReceivedPaymentsList($servicecontractsid){
        $db = PearDatabase::getInstance();
        $query="SELECT vtiger_receivedpayments.*,IFNULL((SELECT sum(vtiger_receivedpayments_extra.extra_price) FROM `vtiger_receivedpayments_extra` WHERE vtiger_receivedpayments_extra.receivementid=vtiger_receivedpayments.receivedpaymentsid),0) AS sumextra_price FROM `vtiger_receivedpayments` where receivedstatus='normal' AND vtiger_receivedpayments.deleted=0 AND relatetoid=?";
        $result = $db->pquery($query,array($servicecontractsid));
        $stages=array();
        $receivedpaymentsid=array();
        for($i=0; $i<$db->num_rows($result); $i++) {
            $row=$db->query_result_rowdata($result, $i);
            $stages[]=$row;
            $receivedpaymentsid[]=$row['receivedpaymentsid'];
        }
        $receivedpaymentsid=empty($receivedpaymentsid)?array(0):$receivedpaymentsid;
        $sql = "SELECT achievementallotid, owncompanys, receivedpaymentsid, ( SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE vtiger_achievementallot.receivedpaymentownid = vtiger_users.id ) AS receivedpaymentownid, businessunit,scalling FROM `vtiger_achievementallot` WHERE receivedpaymentsid in(".implode(',',$receivedpaymentsid).")";
        $achievementallot = $db->pquery("$sql",array());
        $nums = $db->num_rows($achievementallot);
        $achievementallotdata = array();
        if($nums > 0) {
            for($i=0; $i<$nums; ++$i) {
                $row = $db->query_result_rowdata($achievementallot, $i);
                $achievementallotdata[$row['receivedpaymentsid']][] = $row;
            }
        }
        return  array('receivedpaymentlist'=>$stages,'achievementallotdata'=>$achievementallotdata);
    }
}