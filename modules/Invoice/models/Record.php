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
class Invoice_Record_Model extends Inventory_Record_Model {

    /**
     * 取得发票对应的回票
     * @return arrayw
     */
    static public function inventoryList(){

        $db=PearDatabase::getInstance();
        $invoiceid=abs((int)$_REQUEST['record']);

        $sql="SELECT
                (
                    SELECT
                        last_name
                    FROM
                        vtiger_users
                    WHERE
                        id = vtiger_receivedpayments.createid
                ) AS createid,
                vtiger_servicecontracts.contract_no,
                IFNULL(
                    vtiger_servicecontracts.currencytype,
                    '--'
                ) AS currencytype,
                vtiger_receivedpayments.relmodule,
                TRUNCATE (
                    vtiger_receivedpayments.unit_price,
                    2
                ) AS unit_price,
                IFNULL(
                    vtiger_receivedpayments.reality_date,
                    '--'
                ) AS reality_date,
                IF(vtiger_receivedpayments.standardmoney,TRUNCATE(vtiger_receivedpayments.standardmoney,2),'--') AS standardmoney,
                IF(vtiger_receivedpayments.exchangerate,TRUNCATE(vtiger_receivedpayments.exchangerate,2),'--') AS exchangerate,
                IFNULL(
                    (
                        SELECT
                            vtiger_invoice.invoice_no
                        FROM
                            vtiger_invoice
                        WHERE
                            vtiger_invoice.invoiceid = vtiger_invoicerelatedreceive.invoiceid
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
                            vtiger_invoice.invoiceid = vtiger_invoicerelatedreceive.invoiceid
                    ),
                    '--'
                ) AS modulestatus,
                IFNULL(vtiger_receivedpayments.paytitle,'--') AS paytitle,
                vtiger_receivedpayments.receivedpaymentsid AS receivedid,
                 vtiger_receivedpayments.overdue
            FROM
                vtiger_receivedpayments
            LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid = vtiger_receivedpayments.relatetoid
            LEFT JOIN vtiger_invoicerelatedreceive ON vtiger_invoicerelatedreceive.receivedpaymentsid = vtiger_receivedpayments.receivedpaymentsid
            LEFT JOIN vtiger_invoice ON vtiger_invoice.invoiceid = vtiger_invoicerelatedreceive.invoiceid
            WHERE
                vtiger_invoice.invoiceid = {$invoiceid}";
        return $db->run_query_allrecords($sql);
    }
    static public function editInoviceList($contactid=''){
        if(empty($contactid)){
            return ;
        }
        $db=PearDatabase::getInstance();


        $query="SELECT
                    IFNULL((SELECT last_name	FROM vtiger_users	WHERE	id = vtiger_receivedpayments.createid),'--') AS createid,
                    IFNULL(vtiger_servicecontracts.total,'--') AS total,
                    IFNULL(vtiger_receivedpayments.standardmoney,'--') AS standardmoney,
                    IFNULL(vtiger_receivedpayments.exchangerate,'--') AS exchangerate,
                    vtiger_servicecontracts.contract_no,
                    IFNULL(vtiger_servicecontracts.currencytype,'--') AS currencytype,
                    vtiger_receivedpayments.relmodule,
                    TRUNCATE (vtiger_receivedpayments.unit_price,	2) AS unit_price,
                    IFNULL(vtiger_receivedpayments.reality_date,'--') AS reality_date,
                    IFNULL((SELECT	vtiger_invoice.invoice_no	FROM	vtiger_invoice WHERE vtiger_invoice.invoiceid = vtiger_invoicerelatedreceive.invoiceid),'--') AS invoice_no,
                    IFNULL(vtiger_invoicerelatedreceive.invoiceid,'--') as invoicesid,
                    vtiger_receivedpayments.accountid,
                    IFNULL(vtiger_receivedpayments.paytitle,'--') AS paytitle,
                     IFNULL(
                    (
                        SELECT
                            vtiger_invoice.modulestatus
                        FROM
                            vtiger_invoice
                        WHERE
                            vtiger_invoice.invoiceid = vtiger_invoicerelatedreceive.invoiceid
                    ),
                    '--'
                ) AS modulestatus,
                    vtiger_receivedpayments.receivedpaymentsid AS receivedid,
                    vtiger_receivedpayments.overdue
                FROM
                    vtiger_receivedpayments
                LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid = vtiger_receivedpayments.relatetoid
                LEFT JOIN vtiger_invoicerelatedreceive ON vtiger_invoicerelatedreceive.receivedpaymentsid = vtiger_receivedpayments.receivedpaymentsid
                WHERE
                    vtiger_receivedpayments.relatetoid = {$contactid}
                ORDER BY
                    invoice_no DESC,
                    receivedid ASC";
        return $db->run_query_allrecords($query);
    }

    /**
     * steel 2015-05-27
     * 编辑处理指定节点可以进行编辑操作
     * edit_financial//财务编辑节点
     * edit_receive//领取人编辑节点
     * @throws
     */
    static public function nodeCheck(){
        $db=PearDatabase::getInstance();
        $invoiceid=abs((int)$_REQUEST['record']);
        $query="SELECT
                    vtiger_workflowstages.workflowstagesflag,
                    vtiger_workflowstages.iseditdata
                FROM
                    `vtiger_salesorderworkflowstages`
                LEFT JOIN vtiger_workflowstages ON vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid
                WHERE
                    vtiger_salesorderworkflowstages.salesorderid =?
                AND isaction = 1
                AND vtiger_salesorderworkflowstages.modulename = 'Invoice'";
        $result=$db->pquery($query,array($invoiceid));
        $currentflag=$db->query_result($result,0,'workflowstagesflag');
        $currentedit=$db->query_result($result,0,'iseditdata');
        if($currentedit==1){
            global $current_user;
            $userid=getDepartmentUser('H25');
            //财务审核节点编辑
            if($currentflag=='edit_financial' && in_array($current_user->id,$userid)){
                return true;
            }
            $where=getAccessibleUsers('','',true);
            $recordModel = Vtiger_Record_Model::getInstanceById($invoiceid, 'Invoice');
            $entity=$recordModel->entity->column_fields;
            //领取人完善资料节点编辑
            if($currentflag=='edit_receive' && in_array($entity['assigned_user_id'],$where)){
                return true;
            }
            return false;
        }else{
            return false;
        }
    }
    /**
     * steel 2015-11-26
     * 是否已经签名
     * @throws
     */
    static public function checksign($recordid){
        $db=PearDatabase::getInstance();
        $result=$db->pquery("select 1 from vtiger_invoicesign where vtiger_invoicesign.setype='Invoice' AND vtiger_invoicesign.invoiceid=?",array($recordid));
        if($db->num_rows($result)>0){
            return false;
        }
        return true;
    }
    static public function getMoreinvoice($recordid){
        $db=PearDatabase::getInstance();
        if($recordid<1){return ;}
        return $db->run_query_allrecords("SELECT vtiger_invoiceextend.*,vtiger_negativeinvoice.negativedrawerextend,vtiger_negativeinvoice.negativebillingtimerextend,vtiger_negativeinvoice.negativeinvoicecodeextend,vtiger_negativeinvoice.negativeinvoice_noextend,vtiger_negativeinvoice.negativebusinessnamesextend,vtiger_negativeinvoice.negativetaxrateextend,vtiger_negativeinvoice.negativecommoditynameextend,vtiger_negativeinvoice.negativetotalandtaxextend,vtiger_negativeinvoice.negativeremarkextend,vtiger_negativeinvoice.negativeamountofmoneyextend,vtiger_negativeinvoice.negativetaxextend FROM vtiger_invoiceextend LEFT JOIN vtiger_negativeinvoice ON vtiger_negativeinvoice.invoiceextendid=vtiger_invoiceextend.invoiceextendid WHERE vtiger_invoiceextend.deleted=0 AND vtiger_invoiceextend.invoiceid={$recordid}");
    }
    static public function checkNegativeInvoice($arr){
        $db=PearDatabase::getInstance();
        $sql="SELECT * FROM vtiger_invoiceextend WHERE vtiger_invoiceextend.invoiceid=? AND vtiger_invoiceextend.invoiceextendid=?";
        if($arr[2]==2){
            $sql.=' AND vtiger_invoiceextend.operator=1';
        }else{
            $sql.=' AND vtiger_invoiceextend.operator!=2';
        }
        $result=$db->pquery($sql,array($arr[0],$arr[1]));
        if($db->num_rows($result)>0){
            return false;
        }
        return true;
    }

    /**
     * 财务人员的部门
     * @return bool
     */
    static public function exportGroupri(){
        global $current_user;
        $id=$current_user->id;
        $db=PearDatabase::getInstance();
        //不必过滤是否在职因为离职的根本就登陆不了系统
        $query="select vtiger_user2department.userid from vtiger_user2department LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid WHERE CONCAT(vtiger_departments.parentdepartment,'::') REGEXP 'H25::'";
        $result=$db->run_query_allrecords($query);
        $userids=array();
        foreach($result as $values){
            $userids[]=$values['userid'];
        }
        $userids[]=1;
        //$userids=array(1,2155,323,1923);//有访问权限的
        if(in_array($id,$userids)){
            return true;
        }
        return false;
    }
}
