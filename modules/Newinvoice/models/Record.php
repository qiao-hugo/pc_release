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
class Newinvoice_Record_Model extends Inventory_Record_Model {
    public static $ELECTRONINVOICECOMPANY=array(
        '珍岛信息技术（上海）股份有限公司',
        '无锡珍岛数字生态服务平台技术有限公司',
        '凯丽隆（上海）软件信息科技有限公司',
        '广东珍岛信息技术有限公司',
        '成都珍岛信息技术有限公司',
        '上海珍岛智能技术集团有限公司佛山分公司',
        '上海珍岛智能技术集团有限公司广州分公司',
        '上海珍岛网络科技有限公司',
        '苏州珍岛信息技术有限公司',
        '杭州珍岛信息技术有限公司',
        '台州珍岛信息技术有限公司',
        '上海珍岛智能技术集团有限公司东莞分公司',
        '金华市珍岛信息技术有限公司');

    static public function getAllBillingcontent() {
        $query = "SELECT DISTINCT billingcontent FROM vtiger_contractsproductsrel";
        $db=PearDatabase::getInstance();
        return $db->run_query_allrecords($query);
    }


    /**
     *
     * @param $userId
     * @param $companyCode
     * @return mixed
     */
    static function isFpAdmin($userId,$companyCode){
        $sql="select 1 from vtiger_invoicecompanyuser where invoicecompany=? and modulename='fp' and userid=?";
        $db=PearDatabase::getInstance();
        $result = $db->pquery($sql,array($companyCode,$userId));
        return $db->num_rows($result);
    }

    /**
     * 取得发票对应的回票
     * @return arrayw
     */
    static public function inventoryList(){

        $db=PearDatabase::getInstance();
        $invoiceid=abs((int)$_REQUEST['record']);

        $sql="SELECT ( SELECT last_name FROM vtiger_users WHERE id = vtiger_receivedpayments.createid ) AS createid, vtiger_servicecontracts.contract_no, IFNULL( vtiger_servicecontracts.currencytype, '--' ) AS currencytype, vtiger_receivedpayments.relmodule, TRUNCATE ( vtiger_receivedpayments.unit_price, 2 ) AS unit_price, IFNULL( vtiger_receivedpayments.reality_date, '--' ) AS reality_date, IF ( vtiger_receivedpayments.standardmoney, TRUNCATE ( vtiger_receivedpayments.standardmoney, 2 ), '--') AS standardmoney, IF ( vtiger_receivedpayments.exchangerate, TRUNCATE ( vtiger_receivedpayments.exchangerate, 2 ), '--') AS exchangerate, IFNULL( ( SELECT vtiger_newinvoice.invoice_no FROM vtiger_newinvoice WHERE vtiger_newinvoice.invoiceid = vtiger_newinvoicerelatedreceive.invoiceid ), '--') AS invoice_no, IFNULL( ( SELECT vtiger_newinvoice.modulestatus FROM vtiger_newinvoice WHERE vtiger_newinvoice.invoiceid = vtiger_newinvoicerelatedreceive.invoiceid ), '--') AS modulestatus, IFNULL( vtiger_receivedpayments.paytitle, '--') AS paytitle, vtiger_receivedpayments.receivedpaymentsid AS receivedid, vtiger_receivedpayments.overdue FROM vtiger_receivedpayments LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid = vtiger_receivedpayments.relatetoid LEFT JOIN vtiger_newinvoicerelatedreceive ON vtiger_newinvoicerelatedreceive.receivedpaymentsid = vtiger_receivedpayments.receivedpaymentsid LEFT JOIN vtiger_newinvoice ON vtiger_newinvoice.invoiceid = vtiger_newinvoicerelatedreceive.invoiceid WHERE vtiger_newinvoice.invoiceid = {$invoiceid}";
        return $db->run_query_allrecords($sql);
    }
    static public function editInoviceList($contactid=''){
        if(empty($contactid)){
            return ;
        }
        $db=PearDatabase::getInstance();
        $query="SELECT
                IFNULL(
                    (
                        SELECT
                            last_name
                        FROM
                            vtiger_users
                        WHERE
                            id = vtiger_receivedpayments.createid
                    ),
                    '--'
                ) AS createid,
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
                IFNULL(
                    (
                            vtiger_newinvoice.invoice_no
                    ),
                    '--'
                ) AS invoice_no,
                IFNULL(
                    vtiger_newinvoice.invoiceid,
                    '--'
                ) AS invoicesid,
                vtiger_receivedpayments.accountid,
                IFNULL(
                    vtiger_receivedpayments.paytitle,
                    '--'
                ) AS paytitle,
                IFNULL(
                    (
                            vtiger_newinvoice.modulestatus
                    ),
                    '--'
                ) AS modulestatus,
                vtiger_receivedpayments.receivedpaymentsid AS receivedid,
                vtiger_receivedpayments.overdue
            FROM
                vtiger_receivedpayments
            INNER JOIN vtiger_newinvoicerayment ON(vtiger_receivedpayments.receivedpaymentsid=vtiger_newinvoicerayment.receivedpaymentsid)
            LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid = vtiger_receivedpayments.relatetoid
            LEFT JOIN vtiger_newinvoice ON vtiger_servicecontracts.servicecontractsid = vtiger_newinvoice.contractid
            WHERE
                vtiger_newinvoice.invoiceid=vtiger_newinvoicerayment.invoiceid
                AND vtiger_receivedpayments.relatetoid = { $contactid }
            ORDER BY
                invoice_no DESC,
                receivedid ASC";
        return $db->run_query_allrecords($query);
    }


    /*
        获取回款关联
    */
    static public function getNewinvoicerayment($invoiceid) {
        if (empty($invoiceid)) {
            return array();
        }
        $db=PearDatabase::getInstance();

        // 如果是 打回状态

        $sql = "select invoiceid,modulename from vtiger_newinvoice where invoiceid=?";
        $sel_result = $db->pquery($sql, array($invoiceid));
        $resultdata=$db->query_result_rowdata($sel_result,0);
        $sql = "select invoiceid,modulename from vtiger_newinvoice where invoiceid=? AND modulestatus=?";
        $sel_result = $db->pquery($sql, array($invoiceid, 'a_exception'));
        $res_cnt = $db->num_rows($sel_result);
        if($res_cnt > 0) {

            if($resultdata['modulename']=='ServiceContracts'){
                $invoicecompany="SELECT vtiger_servicecontracts.invoicecompany FROM vtiger_servicecontracts WHERE vtiger_servicecontracts.servicecontractsid = vtiger_receivedpayments.relatetoid";
            }else{
                $invoicecompany="SELECT vtiger_suppliercontracts.invoicecompany FROM vtiger_suppliercontracts WHERE vtiger_suppliercontracts.suppliercontractsid = vtiger_receivedpayments.relatetoid";
            }
            $sql = "SELECT  '1' AS data_flag,vtiger_newinvoicerayment.voidorredtotal,vtiger_newinvoicerayment.surpluinvoicetotal, vtiger_newinvoicerayment.newinvoiceraymentid, vtiger_newinvoicerayment.servicecontractsid, vtiger_newinvoicerayment.receivedpaymentsid, vtiger_newinvoicerayment.total , vtiger_newinvoicerayment.arrivaldate, vtiger_newinvoicerayment.invoicetotal, ( SELECT vtiger_receivedpayments.allowinvoicetotal FROM vtiger_receivedpayments WHERE vtiger_receivedpayments.receivedpaymentsid = vtiger_newinvoicerayment.receivedpaymentsid ) AS allowinvoicetotal, vtiger_newinvoicerayment.invoicecontent, vtiger_newinvoicerayment.remarks , vtiger_newinvoicerayment.invoiceid, vtiger_newinvoicerayment.contract_no, vtiger_newinvoicerayment.paytitle AS t_paytitle, vtiger_newinvoicerayment.paytitle AS t_paytitle, CONCAT(vtiger_receivedpayments.paytitle, '[', vtiger_receivedpayments.unit_price, ']') AS paytitle , ({$invoicecompany}) AS invoicecompany FROM vtiger_newinvoicerayment LEFT JOIN vtiger_receivedpayments ON vtiger_newinvoicerayment.receivedpaymentsid = vtiger_receivedpayments.receivedpaymentsid WHERE invoiceid = ? AND vtiger_newinvoicerayment.deleted = 0";
        } else {
            if($resultdata['modulename']=='ServiceContracts'){
                $invoicecompany="SELECT vtiger_servicecontracts.invoicecompany FROM vtiger_servicecontracts WHERE vtiger_servicecontracts.servicecontractsid = vtiger_receivedpayments.relatetoid";
            }else{
                $invoicecompany="SELECT vtiger_suppliercontracts.invoicecompany FROM vtiger_suppliercontracts WHERE vtiger_suppliercontracts.suppliercontractsid = vtiger_receivedpayments.relatetoid";
            }
            $sql = "SELECT  '1' AS data_flag,vtiger_newinvoicerayment.voidorredtotal,vtiger_newinvoicerayment.surpluinvoicetotal, vtiger_newinvoicerayment.newinvoiceraymentid, vtiger_newinvoicerayment.servicecontractsid, vtiger_newinvoicerayment.receivedpaymentsid, vtiger_newinvoicerayment.total , vtiger_newinvoicerayment.arrivaldate, vtiger_newinvoicerayment.invoicetotal, vtiger_newinvoicerayment.allowinvoicetotal, vtiger_newinvoicerayment.invoicecontent, vtiger_newinvoicerayment.remarks , vtiger_newinvoicerayment.invoiceid, vtiger_newinvoicerayment.contract_no, vtiger_newinvoicerayment.paytitle AS t_paytitle, vtiger_newinvoicerayment.paytitle AS t_paytitle, CONCAT( IF(vtiger_receivedpayments.paytitle!='',vtiger_receivedpayments.paytitle,vtiger_staypayment.payer) , '[', vtiger_receivedpayments.unit_price, ']' ) AS paytitle , ({$invoicecompany}) AS invoicecompany FROM vtiger_newinvoicerayment LEFT JOIN vtiger_receivedpayments ON vtiger_newinvoicerayment.receivedpaymentsid = vtiger_receivedpayments.receivedpaymentsid left join vtiger_staypayment on vtiger_staypayment.staypaymentid=vtiger_receivedpayments.staypaymentid WHERE invoiceid = ? AND vtiger_newinvoicerayment.deleted = 0";
        }

        $sel_result = $db->pquery($sql, array($invoiceid));
        $res_cnt = $db->num_rows($sel_result);

        $data = array();
        if ($res_cnt > 0) {
            while($rawData=$db->fetch_array($sel_result)) {
                $data[] = $rawData;
            }
        }

        return $data;
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
        $result=$db->pquery("select 1 from vtiger_newinvoicesign where vtiger_newinvoicesign.setype='Invoice' AND vtiger_newinvoicesign.invoiceid=?",array($recordid));
        if($db->num_rows($result)>0){
            return false;
        }
        return true;
    }

    static public function checkWorkflows($stagerecordid) {
        $db=PearDatabase::getInstance();

        $query="SELECT
                    vtiger_workflowstages.workflowstagesflag
                FROM
                    `vtiger_salesorderworkflowstages`
                LEFT JOIN vtiger_workflowstages ON vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid
                WHERE
                    vtiger_salesorderworkflowstages.salesorderid = ?
                AND vtiger_salesorderworkflowstages.modulename = 'Newinvoice'
                AND vtiger_salesorderworkflowstages.isaction=1";
        $result=$db->pquery($query,array($stagerecordid));
        $currentflag=$db->query_result($result, 0, 'workflowstagesflag');
        if ($currentflag == 'receive_invoice') {  //发票领取阶段
            return true;
        }
        return false;
    }

    static public function getMoreinvoice($recordid) {
        $db=PearDatabase::getInstance();
        $sql = "SELECT * FROM vtiger_newinvoiceextend WHERE vtiger_newinvoiceextend.deleted = 0
                AND vtiger_newinvoiceextend.invoiceid ='{$recordid}'";
        $newinvoiceextend = $db->run_query_allrecords($sql);  // 获取发票信息
        foreach ($newinvoiceextend as $key=>$value) {
            $sql = "SELECT * FROM vtiger_newnegativeinvoice WHERE vtiger_newnegativeinvoice.deleted = 0 AND vtiger_newnegativeinvoice.invoiceextendid='{$value['invoiceextendid']}'";
            $newnegativeinvoice =  $db->run_query_allrecords($sql);  // 获取红冲信息
            // 计算红冲的和
            $negativetotalandtaxextend = 0;
            if(count($newnegativeinvoice) > 0) {
                foreach($newnegativeinvoice as $v) {
                    $negativetotalandtaxextend += $v['negativetotalandtaxextend'];
                }
            }
            $newinvoiceextend[$key]['newnegativeinvoice'] = $newnegativeinvoice;
            $newinvoiceextend[$key]['surplusnewnegativeinvoice'] = $value['totalandtaxextend'] - $negativetotalandtaxextend;
        }
        return $newinvoiceextend;
    }

    /*static public function getMoreinvoice($recordid){
        $db=PearDatabase::getInstance();
        if($recordid<1){return ;}
        return $db->run_query_allrecords("SELECT vtiger_newinvoiceextend.*, vtiger_newnegativeinvoice.negativedrawerextend, vtiger_newnegativeinvoice.negativebillingtimerextend, vtiger_newnegativeinvoice.negativeinvoicecodeextend, vtiger_newnegativeinvoice.negativeinvoice_noextend, vtiger_newnegativeinvoice.negativebusinessnamesextend, vtiger_newnegativeinvoice.negativetaxrateextend, vtiger_newnegativeinvoice.negativecommoditynameextend, vtiger_newnegativeinvoice.negativetotalandtaxextend, vtiger_newnegativeinvoice.negativeremarkextend, vtiger_newnegativeinvoice.negativeamountofmoneyextend, vtiger_newnegativeinvoice.negativetaxextend FROM vtiger_newinvoiceextend LEFT JOIN vtiger_newnegativeinvoice ON vtiger_newnegativeinvoice.invoiceextendid = vtiger_newinvoiceextend.invoiceextendid WHERE vtiger_newinvoiceextend.deleted = 0 AND vtiger_newinvoiceextend.invoiceid ={$recordid}");
    }*/
    static public function checkNegativeInvoice($arr){
        $db=PearDatabase::getInstance();
        $sql="SELECT * FROM vtiger_newinvoiceextend WHERE vtiger_newinvoiceextend.invoiceid=? AND vtiger_newinvoiceextend.invoiceextendid=? AND vtiger_newinvoiceextend.deleted = 0";
        if($arr[2]==2){
            $sql.=' AND vtiger_newinvoiceextend.operator=1';
        }else{
            $sql.=' AND vtiger_newinvoiceextend.operator!=2';
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


    static public function getNewinvoiceraymentInfo($account_id, $invoicecompany='', $is_edit=true) {
        $where = '';
        if(!empty($invoicecompany)) {
            $where = " AND vtiger_servicecontracts.invoicecompany='{$invoicecompany}' ";
        }

        $addWhere = '';
        if(!$is_edit) {
            $addWhere = " AND vtiger_receivedpayments.allowinvoicetotal>0 ";
        }

        /*$sql = "SELECT  vtiger_servicecontracts.invoicecompany,vtiger_receivedpayments.paytitle AS t_paytitle, vtiger_receivedpayments.receivedpaymentsid, CONCAT(vtiger_receivedpayments.reality_date, '【', vtiger_receivedpayments.receivedpaymentsid ,'】',
            ' ￥',vtiger_receivedpayments.unit_price,' ',
            vtiger_receivedpayments.paytitle, ' [', vtiger_servicecontracts.contract_no, ']') AS paytitle, vtiger_receivedpayments.unit_price, vtiger_receivedpayments.reality_date , vtiger_servicecontracts.servicecontractsid, vtiger_servicecontracts.contract_no, vtiger_contractsproductsrel.billingcontent, vtiger_receivedpayments.allowinvoicetotal FROM vtiger_servicecontracts LEFT JOIN vtiger_receivedpayments ON vtiger_servicecontracts.servicecontractsid = vtiger_receivedpayments.relatetoid LEFT JOIN vtiger_contract_type ON vtiger_servicecontracts.contract_type = vtiger_contract_type.contract_type LEFT JOIN vtiger_contractsproductsrel ON vtiger_contractsproductsrel.contract_type = vtiger_contract_type.contract_typeid WHERE vtiger_servicecontracts.sc_related_to = ? AND vtiger_receivedpayments.relatetoid != 0 AND vtiger_receivedpayments.relatetoid IS NOT NULL {$where} {$addWhere}";
        */
        $sql = "SELECT  vtiger_servicecontracts.invoicecompany,vtiger_receivedpayments.paytitle AS t_paytitle, vtiger_receivedpayments.receivedpaymentsid, CONCAT(vtiger_receivedpayments.reality_date, '【', vtiger_receivedpayments.receivedpaymentsid ,'】', 
            ' ￥',vtiger_receivedpayments.unit_price,' ',
            vtiger_receivedpayments.paytitle, ' [', vtiger_servicecontracts.contract_no, ']') AS paytitle, vtiger_receivedpayments.unit_price, vtiger_receivedpayments.reality_date , vtiger_servicecontracts.servicecontractsid, vtiger_servicecontracts.contract_no, vtiger_servicecontracts.billcontent as billingcontent, vtiger_receivedpayments.allowinvoicetotal FROM vtiger_servicecontracts LEFT JOIN vtiger_receivedpayments ON vtiger_servicecontracts.servicecontractsid = vtiger_receivedpayments.relatetoid  WHERE vtiger_servicecontracts.sc_related_to = ? AND vtiger_receivedpayments.relatetoid != 0 AND vtiger_receivedpayments.relatetoid IS NOT NULL {$where} {$addWhere}";

        $db=PearDatabase::getInstance();
        $sel_result = $db->pquery($sql, array($account_id));
        $res_cnt = $db->num_rows($sel_result);
        $invoicerayment = array();

        if($res_cnt > 0) {
            while($rawData=$db->fetch_array($sel_result)) {
                $invoicerayment[$rawData['receivedpaymentsid']] = $rawData;
            }
        }
        return $invoicerayment;
    }

    static public function setAllowinvoicetotalLog($id, $value, $msg='') {
        $db=PearDatabase::getInstance();
        global $current_user;

        $sql = "SELECT allowinvoicetotal FROM vtiger_receivedpayments WHERE receivedpaymentsid=? LIMIT 1";
        $sel_result = $db->pquery($sql, array($id));
        $res_cnt = $db->num_rows($sel_result);
        if($res_cnt > 0) {
            $row = $db->query_result_rowdata($sel_result, 0);

            // 做更新记录
            $did = $db->getUniqueId('vtiger_modtracker_basic');
            $db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                            array($did, $id, 'ReceivedPayments', $current_user->id, date('Y-m-d H:i:s'), 0));

            $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                Array($did, 'allowinvoicetotal', $row['allowinvoicetotal'], ($row['allowinvoicetotal'] + $value) . $msg ));
        }
    }

    // 计算发票的 开票金额
    static public function calcTaxtotal($invoiceId, $isUpdateData = false) {
        $db=PearDatabase::getInstance();
        $sql = "select sum(invoicetotal) AS invoicetotal from vtiger_newinvoicerayment where invoiceid=? AND deleted=0";
        $sel_result = $db->pquery($sql, array($invoiceId));
        $res_cnt    = $db->num_rows($sel_result);

        $invoicetotal = 0;
        if($res_cnt > 0) {
            $row = $db->query_result_rowdata($sel_result, 0);
            $invoicetotal = $row['invoicetotal'];
            // 是否需要更新到 vtiger_invoice 表的开票金额
            if ($isUpdateData) {
                $sql = "update vtiger_newinvoice set taxtotal=? where invoiceid=?";
                $db->pquery($sql, array($invoicetotal, $invoiceId));
            }
        }
        return $invoicetotal;
    }

    // 计算发票的 实际开票金额
    static public function calcActualtotal($invoiceId, $isUpdateData = false) {
        global $current_user;
        $db=PearDatabase::getInstance();
        $sql = "select sum(totalandtaxextend) AS totalandtaxextend from vtiger_newinvoiceextend where  invoiceid=? AND deleted=0 AND invoicestatus!='tovoid' ";
        $sel_result = $db->pquery($sql, array($invoiceId));
        $res_cnt    = $db->num_rows($sel_result);

        // 红冲金额
        $redinvoiceTotal = 0;
        $sql = "select SUM(negativetotalandtaxextend) AS negativetotalandtaxextend from vtiger_newnegativeinvoice where invoiceid=? AND deleted=0";
        $sel_result2 = $db->pquery($sql, array($invoiceId));
        $res_cnt2    = $db->num_rows($sel_result2);
        if ($res_cnt2  > 0) {
            $row = $db->query_result_rowdata($sel_result2, 0);
            $redinvoiceTotal = $row['negativetotalandtaxextend'];
        }

        $totalandtaxextend = 0;
        if($res_cnt > 0) {
            $row = $db->query_result_rowdata($sel_result, 0);
            $totalandtaxextend = $row['totalandtaxextend'];
            // 是否需要更新到 vtiger_invoice 表的 实际开票金额
            if ($isUpdateData) {
                $sql = "update vtiger_newinvoice set actualtotal=? where invoiceid=?";
                $db->pquery($sql, array($totalandtaxextend - $redinvoiceTotal, $invoiceId));

                //如果实际开票金额为0，则可作废该发票 gaocl add 2018-05-29
                $currentTime = date('Y-m-d H:i:s');
                $db->pquery("UPDATE vtiger_newinvoice SET iscancel=1,modulestatus=?,voidreason=?,voiduserid=?,voiddatetime=? WHERE actualtotal=0 AND invoiceid=?",array('c_cancel','因实际开票金额为0,系统自动作废',$current_user->id,$currentTime,$invoiceId));
                // 工作流置非激活状态
                $db->pquery("UPDATE vtiger_salesorderworkflowstages SET isaction=0 WHERE workflowstagesflag!='INVOICE_ADMIN_THROUGH' and modulename='Newinvoice' AND isaction=1 AND salesorderid=(SELECT invoiceid FROM vtiger_newinvoice WHERE modulestatus='c_cancel' AND invoiceid=? LIMIT 1)",array($invoiceId));
            }
        }

        return $totalandtaxextend;
    }


    // 计算发票的 实际开票金额
    static public function calcActualtotal2($invoiceId, $isUpdateData = false) {
        $db=PearDatabase::getInstance();
        $sql = "select sum(totalandtaxextend) AS totalandtaxextend from vtiger_newinvoiceextend where  invoiceid=? AND deleted=0 AND invoicestatus!='tovoid'";
        $sel_result = $db->pquery($sql, array($invoiceId));
        $res_cnt    = $db->num_rows($sel_result);

        $totalandtaxextend = 0;
        if($res_cnt > 0) {
            $row = $db->query_result_rowdata($sel_result, 0);
            $totalandtaxextend = $row['totalandtaxextend'];
            // 是否需要更新到 vtiger_invoice 表的 实际开票金额
            if ($isUpdateData) {
                $sql = "update vtiger_newinvoice set actualtotal=? where invoiceid=?";
                $db->pquery($sql, array($totalandtaxextend, $invoiceId));
            }
        }

        return $totalandtaxextend;
    }

    // 计算 预开票 的 需要匹配的回款金额
    static public function caclNeedTotal($invoiceId) {
        $db=PearDatabase::getInstance();
        $sql = "select sum(invoicetotal) AS invoicetotal from vtiger_newinvoicerayment where invoiceid=? AND deleted=0";
        $sel_result = $db->pquery($sql, array($invoiceId));
        $res_cnt    = $db->num_rows($sel_result);

        $invoicetotal = 0; // 回款关联的开票金额
        if($res_cnt > 0) {
            $row = $db->query_result_rowdata($sel_result, 0);
            $invoicetotal = $row['invoicetotal'];
        }

        // 计算发票的 总金额
        $totalandtaxextend = self::calcActualtotal2($invoiceId);
        // 红冲的情况
        $sql = "select sum(negativetotalandtaxextend) AS negativetotalandtaxextend from vtiger_newnegativeinvoice where invoiceid=? AND deleted=0";
        $sel_result = $db->pquery($sql, array($invoiceId));
        $res_cnt    = $db->num_rows($sel_result);
        $negativetotalandtaxextend = 0;
        if($res_cnt > 0) {
            $row = $db->query_result_rowdata($sel_result, 0);
            $negativetotalandtaxextend = $row['negativetotalandtaxextend'];
        }
        $totalandtaxextend -= $negativetotalandtaxextend; //减去红冲
        $totalandtaxextend -= $invoicetotal; // 减去已经匹配的回款

        return $totalandtaxextend;
    }

    // 返回当前的审核的节点 标示
    public function getWorkflowstagesflag($invoiceId) {
        $db=PearDatabase::getInstance();
        $query="SELECT
                    vtiger_salesorderworkflowstages.isaction
                FROM
                    `vtiger_salesorderworkflowstages`
                LEFT JOIN vtiger_workflowstages ON vtiger_workflowstages.workflowstagesid = vtiger_salesorderworkflowstages.workflowstagesid
                WHERE
                    vtiger_salesorderworkflowstages.salesorderid= ?
                AND vtiger_salesorderworkflowstages.modulename='Newinvoice'
                AND vtiger_workflowstages.workflowstagesflag IN ('receive_invoice')
                AND vtiger_salesorderworkflowstages.isaction IN ('1', '2')
                ";
        $result = $db->pquery($query, array($invoiceId));
        $res_cnt = $db->num_rows($result);
        if ($res_cnt > 0) {
            return true;
        }
        return false;
    }

    /**
     * 是否可以发票重新匹配合同
     * @param Vtiger_Request $request
     */
    static  public  function hasRepeatServiceContracts($record)
    {
        //可编辑条件：
        //发票管理员/系统管理员(在合同权限中设置)
        //发票申请类型为预开票
        //已匹配金额=0
        //发票流程状态已完成

        global $adb;
        global $current_user;
        $query="SELECT 1 FROM vtiger_exportmanage WHERE deleted=0 AND userid=? AND module=? AND classname=?";//加入特殊角色
        $result=$adb->pquery($query,array($current_user->id,'Newinvoice','pre_invoice_audit'));
        $num=$adb->num_rows($result);
        if($num){
            $query='';
        }else{
            $query='AND NOT EXISTS(SELECT 1 FROM vtiger_newinvoicerayment WHERE vtiger_newinvoicerayment.deleted=0 AND vtiger_newinvoicerayment.allowinvoicetotal>0 AND vtiger_newinvoicerayment.invoiceid='.$record.')';
        }
        $query_sql = "SELECT 1 FROM vtiger_newinvoice
                    INNER JOIN vtiger_crmentity ON(vtiger_newinvoice.invoiceid=vtiger_crmentity.crmid)
                    LEFT JOIN vtiger_servicecontracts ON (vtiger_servicecontracts.servicecontractsid=vtiger_newinvoice.contractid)
                    WHERE vtiger_crmentity.deleted =0 
                    AND vtiger_newinvoice.invoicetype='c_billing' 
                    ".$query."
                    AND vtiger_newinvoice.modulestatus = 'c_complete'
                    AND vtiger_newinvoice.invoiceid=?";

        /*if(!$current_user->is_admin){
            $query_sql .="AND vtiger_crmentity.smownerid={$current_user->id}";
        }*/

        //echo $query_sql;die();
        $sel_result = $adb->pquery($query_sql, array($record));
        $res_cnt = $adb->num_rows($sel_result);

        if($res_cnt > 0){
            return true;
        }
        return false;
    }

    public function sendWarningEmail($warningDatas)
    {
        if (empty($warningDatas)) {
            return;
        }
        $Subject = '预开票待匹配提醒！！！';
        $str = '';
        foreach ($warningDatas as $warningData) {
            $str .= '您好!<br>';
            $str .= "    你于".date("Y-m-d H:i:s")."，在  “ERP系统---财务模块---发票（新）”中，还有未匹配回款的数据，请及时处理。谢谢！<br>
            数据详情为：<br>
           1，发票编号：".$warningData['invoiceno'];
            $this->_logs(array('预开发票匹配提醒','email'=>$warningData['email1'],'content'=>$str));
            Vtiger_Record_Model::sendMail($Subject, $str,  array(array('mail' => $warningData['email1'], 'name' => '')));
        }
    }

    /**
     * 获得预开票提醒设置
     * @param string $auditsettingtype
     * @return mixed
     */
    public function getPreInvoiceRemindSettings($auditsettingtype="PreInvoiceRemindSetting"){
        $db=PearDatabase::getInstance();
        $sql = "SELECT
	remindid,
	'预开票提醒设置' AS remindtype,
	( SELECT vtiger_departments.departmentname FROM vtiger_departments WHERE vtiger_departments.departmentid = vtiger_newinvoiceremind.department ) AS department,
	days,
	over_days
FROM
	vtiger_newinvoiceremind 
WHERE
	remindtype =?
ORDER BY
	remindid DESC";
        return $db->pquery($sql,array($auditsettingtype));
    }


    /**
     * 获得记录
     * @param string $auditsettingtype
     * @return mixed
     */
    public function getPreInvoiceAuditSettings($auditsettingtype="PreInvoiceAuditSetting"){
        $db=PearDatabase::getInstance();
        $sql = "SELECT
	auditsettingsid,
	'预开票审核设置' AS auditsettingtype,
	( SELECT vtiger_departments.departmentname FROM vtiger_departments WHERE vtiger_departments.departmentid = vtiger_auditsettings.department ) AS department,
	( SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id = vtiger_auditsettings.oneaudituid ) AS oneaudituid,
	IFNULL( ( SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id = vtiger_auditsettings.towaudituid ), '--' ) AS towaudituid,
	IFNULL( ( SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id = vtiger_auditsettings.audituid3 ), '--' ) AS audituid3,
	IFNULL( ( SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id = vtiger_auditsettings.audituid4 ), '--' ) AS audituid4,
	IFNULL( ( SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id = vtiger_auditsettings.audituid5 ), '--' ) AS audituid5 
FROM
	vtiger_auditsettings 
WHERE
	auditsettingtype =?
ORDER BY
	auditsettingsid DESC";
        //return $db->run_query_allrecords($sql,array($auditsettingtype));
        return $db->pquery($sql,array($auditsettingtype));
    }


    /**
     * 所有预开票的进来，有回款的废除如果金额相等,解锁
     * @param $invoiceId
     */
    static public function updateInvoiceRemind($invoiceId){
        $db=PearDatabase::getInstance();
        $sql='SELECT
IF
	( actualtotal = ( SELECT sum( invoicetotal ) FROM `vtiger_newinvoicerayment` WHERE deleted = 0 AND invoiceid = ? ), 1, 0 )  as matchover
FROM
	vtiger_newinvoice 
WHERE
	invoiceid = ?';
        $result=$db->pquery($sql,array($invoiceId,$invoiceId));
        $row=$db->fetch_row($result);
        if($row['matchover']){
            self::updateInvoiceWithOutPayment($invoiceId);
        }
    }


    /**
     * 废除了无锁
     * @param $invoiceId
     */
    static public function updateInvoiceWithOutPayment($invoiceId){
        $db=PearDatabase::getInstance();
        $sql = "update vtiger_newinvoice set matchtimeover=0,lockstatus=0 where invoiceid=?";
        $db->pquery($sql, array($invoiceId));
    }

    /**
     *
     * 将matchtimeover改为0,解除关联回款使用,并对该发票枷锁
     * @param $invoiceId
     */
    static public function updateInvoiceLock($invoiceId){
        $db=PearDatabase::getInstance();
        $sql = "update vtiger_newinvoice set matchtimeover=0 where invoiceid=?";
        $db->pquery($sql, array($invoiceId));
    }

    /**
     * 获取需要延期的数据
     * @return mixed
     */
    static public function getPreInvoiceDelaySettings($auditsettingtype="PreInvoiceDelay"){
        $db=PearDatabase::getInstance();
        $sql = "SELECT
	auditsettingsid,
	'预开票回款延期' AS auditsettingtype,
	( SELECT vtiger_departments.departmentname FROM vtiger_departments WHERE vtiger_departments.departmentid = vtiger_auditsettings.department ) AS department,
	( SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id = vtiger_auditsettings.oneaudituid ) AS oneaudituid
FROM
	vtiger_auditsettings 
WHERE
	auditsettingtype =?
ORDER BY
	auditsettingsid DESC";
        return $db->pquery($sql,array($auditsettingtype));
    }

    /**
     * 通过流程废除发票
     */
    static public function voidInvoiceByWorkFlow($request){
        global $current_user;
        $db=PearDatabase::getInstance();
        //获取本地需要废除的发票
        $record=$request->get('record');
        $recordModel = Vtiger_DetailView_Model::getInstance('Newinvoice', $record)->getRecord();
        $billingsourcedata = $recordModel->entity->column_fields['billingsourcedata'];
        if($billingsourcedata=='ordersource'){
            self::voidInvoiceByWorkFlowWithOrder($record);
            return false;
        }
        $needVoidExtendSql="select * from vtiger_newinvoiceextend where invoiceid=".$record." and invoicestatus='normal' and processstatus=1";
        $extendArray=$db->run_query_allrecords($needVoidExtendSql);
        $needPaymentArray=static::getNewinvoicerayment($record);
        //循环开始废除发票
        $privileges = Users_Privileges_Model::isPermitted('Newinvoice', 'ToVoid', $record);
        $is_admin = $current_user->is_admin == 'on' ? 1 : 0;
        //先判断权限问题
        if($privileges || $is_admin){
            foreach ($extendArray as $key => $extend){
                $flag=Newinvoice_Record_Model::isClearVoidOrRed($record);
                //没有回款或者需要清空关联回款直接废除
                if(!$needPaymentArray||$flag){
                    if($flag){
                        //需要先清空关联回款
                        Newinvoice_Record_Model::emptyInvoiceRey($record);
                    }
                    //没有回款直接废除
                    $invoiceextendid = $extend['invoiceextendid']; // 发票id
                    // 发票改为作废
                    $sql = " update vtiger_newinvoiceextend set  invoicestatus='tovoid',processstatus=2 where invoiceextendid=?";
                    $db->pquery($sql, array($invoiceextendid));
                    Newinvoice_Record_Model::calcActualtotal($extend['invoiceid'], true);
                }else{
                    //获取需要废除的回款
                    $value['invoiceextendid']=$extend['invoiceextendid'];
                    $value['record']=$record;
                    foreach ($needPaymentArray as $paymentkey=>$needPayment){
                        //拼接金额参数
                        $value['tovoidform'][$paymentkey]=$paymentkey+1;
                        $value['newinvoiceraymentid'][$paymentkey+1]=$needPayment['newinvoiceraymentid'];
                        //重新封装一下参数
                        $_REQUEST['servicecontractsid'][$paymentkey+1]=$needPayment['servicecontractsid'];
                        $_REQUEST['receivedpaymentsid'][$paymentkey+1]=$needPayment['receivedpaymentsid'];
                        $_REQUEST['total'][$paymentkey+1]=$needPayment['total'];
                        $_REQUEST['invoicetotal'][$paymentkey+1]=$needPayment['invoicetotal'];
                        $_REQUEST['allowinvoicetotal'][$paymentkey+1]=$needPayment['allowinvoicetotal'];
                        $_REQUEST['contract_no'][$paymentkey+1]=$needPayment['contract_no'];
                        if($extend['totalandtaxextend']>$needPayment['voidorredtotal']){
                            $_REQUEST['tovoie_total'][$paymentkey+1]=$value['tovoie_total'][$paymentkey+1]=floatval($needPayment['voidorredtotal']);
                            $extend['totalandtaxextend']=$extend['totalandtaxextend']-$needPayment['voidorredtotal'];
                            $needPaymentArray[$paymentkey]['voidorredtotal']=0;
                        }else{
                            $_REQUEST['tovoie_total'][$paymentkey+1]=$value['tovoie_total'][$paymentkey+1]=$extend['totalandtaxextend'];
                            $needPaymentArray[$paymentkey]['voidorredtotal']=$needPaymentArray[$paymentkey]['voidorredtotal']-$extend['totalandtaxextend'];
                            $extend['totalandtaxextend']=0;
                        }
                    }
                    $voidRequest=new Vtiger_Request($value);
                    $voidObject=new Newinvoice_Tovoid_Action();
                    //调用作废方法
                    $voidObject->tt_tovoid($voidRequest);
                }
            }
        }
    }

    /**
     * 订单渠道的废除发票
     * @param $record
     */
    static public function voidInvoiceByWorkFlowWithOrder($record){
        global $current_user;
        $db=PearDatabase::getInstance();
        $needVoidExtendSql="select * from vtiger_newinvoiceextend where invoiceid=".$record." and invoicestatus='normal' and processstatus=1";
        $extendArray=$db->run_query_allrecords($needVoidExtendSql);
        $orderArray=self::getDongchaliListVoid($record);
        $privileges = Users_Privileges_Model::isPermitted('Newinvoice', 'ToVoid', $record);
        $is_admin = $current_user->is_admin == 'on' ? 1 : 0;
        //先判断权限问题
        if($privileges || $is_admin){
            //再次计算废弃发票金额是否和废除开票金额相等
            $sumVoidMoney=array_sum(array_column($orderArray, 'voidmoney'));
            $sumExtendMoney=array_sum(array_column($extendArray, 'totalandtaxextend'));
            //等于继续
            if(bccomp($sumVoidMoney,$sumExtendMoney)==0){
                foreach ($extendArray as $extend){
                    $sql = "update vtiger_newinvoiceextend set  invoicestatus='tovoid',processstatus=2 where invoiceextendid=?";
                    $db->pquery($sql, array($extend['invoiceextendid']));
                }
                foreach ($orderArray as $order){
                    $sql = "update vtiger_dongchaliorder set  remainingmoney=invoicemoney-voidmoney,deleted=1 where dongchaliorderid=?";
                    $db->pquery($sql, array($order['dongchaliorderid']));
                    //记录日志
                    $voidLog['ordercode']=$order['ordercode'];
                    $voidLog['total']=$order['money'];
                    $voidLog['allowinvoicetotal']=$order['invoicemoney'];
                    $voidLog['invoicetotal']=$order['invoicemoney'];
                    $voidLog['tovoidtotal']=$order['voidmoney'];
                    $voidLog['remainingtotal']=$order['invoicemoney']-$order['voidmoney'];
                    $voidLog['createid']=$current_user->id;
                    $voidLog['createtime']=date('Y-m-d H:i:s');
                    $voidLog['invoiceextendid']=$extendArray[0]['invoiceextendid'];
                    $voidLog['type']=1;
                    $voidLog['dongchaliorderid']=$order['dongchaliorderid'];
                    $db->run_insert_data('vtiger_newinvoiceordertovoid',$voidLog);
                }

                //重新计算实际开票金额
                Newinvoice_Record_Model::calcActualtotalByOrder($extend['invoiceid'],'tovoid');

                // 这里判断 对应的所有发票是否都已经作废，
                $sql = "select invoiceextendid from vtiger_newinvoiceextend where invoicestatus!='tovoid' AND invoiceid=?";
                $sel_result = $db->pquery($sql, array($record));
                $res_cnt    = $db->num_rows($sel_result);
                if ($res_cnt == 0) {  // 没有其它的状态
                    $sql = "update vtiger_newinvoice set invoicestatus=? where invoiceid=?";
                    $db->pquery($sql, array('tovoid', $record));
                    $sql = "update vtiger_newinvoice set modulestatus='c_cancel' where invoiceid=?";
                    $db->pquery($sql, array($record));
                }
            }
        }
    }

    /**
     * 订单作废后计算金额
     * @param $invoiceId
     */
    static function calcActualtotalByOrder($invoiceId,$type){
        $db=PearDatabase::getInstance();
        $sql = "select sum(totalandtaxextend) AS totalandtaxextend from vtiger_newinvoiceextend where  invoiceid=? AND deleted=0 AND invoicestatus!='tovoid' ";
        $sel_result = $db->pquery($sql, array($invoiceId));
        $res_cnt    = $db->num_rows($sel_result);

        // 红冲金额
        $redinvoiceTotal = 0;
        $sql = "select SUM(negativetotalandtaxextend) AS negativetotalandtaxextend from vtiger_newnegativeinvoice where invoiceid=? AND deleted=0";
        $sel_result2 = $db->pquery($sql, array($invoiceId));
        $res_cnt2    = $db->num_rows($sel_result2);
        if ($res_cnt2  > 0) {
            $row = $db->query_result_rowdata($sel_result2, 0);
            $redinvoiceTotal = $row['negativetotalandtaxextend'];
        }
        $totalandtaxextend = 0;
        if($res_cnt > 0) {
            $row = $db->query_result_rowdata($sel_result, 0);
            $totalandtaxextend = $row['totalandtaxextend'];
            $sql = "update vtiger_newinvoice set actualtotal=? where invoiceid=?";
            $db->pquery($sql, array($totalandtaxextend - $redinvoiceTotal, $invoiceId));
        }
    }

    /**
     * 订单红冲处理
     * @param $record
     */
    static public function redInvoiceByWorkFlowWithOrder($record){
        global $current_user;
        $db=PearDatabase::getInstance();
        $needVoidExtendSql="select * from vtiger_newinvoiceextend where invoiceid=".$record." and invoicestatus='normal' and processstatus=1";
        $extendArray=$db->run_query_allrecords($needVoidExtendSql);
        $orderArray=self::getDongchaliListWithDeleted($record);
        $privileges = Users_Privileges_Model::isPermitted('Newinvoice', 'NegativeEdit', $record);
        //先判断权限问题
        $is_admin = $current_user->is_admin == 'on' ? 1 : 0;
        if ($privileges || $is_admin) {
            //判断作废金额是否和发票金额一样
            $sumVoidMoney=array_sum(array_column($orderArray, 'voidmoney'));
            $sumExtendMoney=0;
            foreach ($extendArray as $extend){
                $negativeInfo=json_decode($extend['negativeinfo'],true);
                $sumExtendMoney+=$negativeInfo['negativeTotalAndTax'];
            }
            //等于继续
            if(bccomp($sumVoidMoney,$sumExtendMoney)==0){
                foreach ($extendArray as $key => $extend){
                    $negativeInfo=json_decode($extend['negativeinfo'],true);
                    $redInvoiceAmount=$negativeInfo['negativeTotalAndTax'];
                    //红冲废除
                    foreach ($orderArray as $orderKey=>$orderValue){
                        //当需要废除的金额
                        if($redInvoiceAmount!=0){
                            if($orderArray[$orderKey]['voidmoney']==0){
                                //当这个订单扣完了，扣下面那个
                                continue;
                            }
                            if($redInvoiceAmount>$orderValue['voidmoney']){
                                //当目前需要红冲的金额大于这一张的订单金额
                                $redInvoiceAmount=$redInvoiceAmount-$orderValue['voidorredtotal'];//剩余发票继续红冲
                                $orderArray[$orderKey]['voidmoney']=0;//需要废除的金额归零
                            }else{
                                $orderArray[$orderKey]['voidmoney']=$orderArray[$orderKey]['voidmoney']-$redInvoiceAmount;
                                $oldInvoiceAmount=$redInvoiceAmount;
                                $redInvoiceAmount=0;//红冲金额归0
                            }
                        }else{
                            //当此发票已经开完了，那就直接跳出订单扣款循环
                            break;
                        }
                        //发票没废除一次记一次
                        $voidLog['ordercode']=$orderValue['ordercode'];
                        $voidLog['total']=$orderValue['money'];
                        $voidLog['allowinvoicetotal']=$orderValue['invoicemoney'];
                        $voidLog['invoicetotal']=$orderValue['invoicemoney'];
                        $voidLog['tovoidtotal']=$orderArray[$orderKey]['voidmoney']==0?$orderValue['voidmoney']:$oldInvoiceAmount;
                        $voidLog['remainingtotal']=$orderValue['invoicemoney']-$orderValue['voidmoney'];
                        $voidLog['createid']=$current_user->id;
                        $voidLog['createtime']=date('Y-m-d H:i:s');
                        $voidLog['invoiceextendid']=$extend['invoiceextendid'];
                        $voidLog['type']=2;
                        $voidLog['dongchaliorderid']=$orderValue['dongchaliorderid'];
                        $db->run_insert_data('vtiger_newinvoiceordertovoid',$voidLog);
                        //处理订单金额
                        $where=" where dongchaliorderid=?";
                        if($voidLog['remainingtotal']==0){
                            $where=",deleted=1".$where;
                        }
                        $sql="update vtiger_dongchaliorder set remainingmoney=remainingmoney-".$voidLog['tovoidtotal'].",voidmoney=voidmoney-".$voidLog['tovoidtotal'].$where;
                        $db->pquery($sql, array($orderValue['dongchaliorderid']));
                    }
                    //加废除发票
                    $negative_data = array();
                    $negative_data['negativeinvoicecodeextend'] = $negativeInfo['negativeInvoiceCode']; //
                    $negative_data['negativeinvoice_noextend'] = $negativeInfo['negativeInvoiceNo'];   //
                    $negative_data['negativebusinessnamesextend'] = $negativeInfo['negativeBusinessNames'];
                    $negative_data['negativebillingtimerextend'] = $negativeInfo['negativeBillingTime'];
                    $negative_data['negativecommoditynameextend'] = $negativeInfo['negativeCommodityName']; //不必填
                    $negative_data['negativeamountofmoneyextend'] = $negativeInfo['negativeAmountOfMoney'];
                    $negative_data['negativetaxrateextend'] = $negativeInfo['negativeTaxRate'];
                    $negative_data['negativetaxextend'] = $negativeInfo['negativeTax'];
                    $negative_data['negativetotalandtaxextend'] = $negativeInfo['negativeTotalAndTax'];
                    $negative_data['negativeremarkextend'] = $negativeInfo['negativeRemark'];    ////不必填
                    $negative_data['invoiceextendid'] =$extend['invoiceextendid'];
                    $negative_data['invoiceid'] = $record;
                    $negative_data['negativedrawerextend'] = $current_user->id;
                    $divideNames = array_keys($negative_data);
                    $divideValues = array_values($negative_data);
                    $db->pquery('INSERT INTO `vtiger_newnegativeinvoice` ('. implode(',', $divideNames).') VALUES ('. generateQuestionMarks($divideValues) .')',$divideValues);
                    $datetime=date('Y-m-d H:i:s');
                    //对发票加红冲状态
                    $sql="UPDATE vtiger_newinvoiceextend SET invoicestatus='redinvoice',processstatus=2,operator=?,operatortime=? WHERE invoiceid=? AND invoiceextendid=?";
                    $db->pquery($sql,array($current_user->id,$datetime,$record, $extend['invoiceextendid']));
                    //重新计算实际开票金额
                    Newinvoice_Record_Model::calcActualtotalByOrder($extend['invoiceid'],'redinvoice');
                    // 这里判断 对应的所有发票是否都已经作废，
                    $sql = "select invoiceextendid from vtiger_newinvoiceextend where invoicestatus!='redinvoice' AND invoiceid=?";
                    $sel_result = $db->pquery($sql, array($record));
                    $res_cnt    = $db->num_rows($sel_result);
                    if ($res_cnt == 0) {  // 没有其它的状态
                        $sql = "update vtiger_newinvoice set invoicestatus=? where invoiceid=?";
                        $db->pquery($sql, array('redinvoice', $record));
                        $sql = "update vtiger_newinvoice set modulestatus='c_cancel' where invoiceid=?";
                        $db->pquery($sql, array($record));
                    }
                }
            }
        }
    }

    /**
     * 通过流程红冲发票
     * @param $request
     */
    static public function redInvoiceByWorkFlow($request){
        global $current_user;
        $db=PearDatabase::getInstance();
        $record=$request->get('record');
        $recordModel = Vtiger_DetailView_Model::getInstance('Newinvoice', $record)->getRecord();
        $billingsourcedata = $recordModel->entity->column_fields['billingsourcedata'];
        if($billingsourcedata=='ordersource'){
            //订单红冲
            self::redInvoiceByWorkFlowWithOrder($record);
            return false;
        }
        $needVoidExtendSql="select * from vtiger_newinvoiceextend where invoiceid=".$record." and invoicestatus='normal' and processstatus=1";
        $extendArray=$db->run_query_allrecords($needVoidExtendSql);
        $needPaymentArray=static::getNewinvoicerayment($record);
        $privileges = Users_Privileges_Model::isPermitted('Newinvoice', 'NegativeEdit', $record);
        //先判断权限问题
        $is_admin = $current_user->is_admin == 'on' ? 1 : 0;
        if ($privileges || $is_admin) {
            //先判断开票金额和废除金额是否相等
            $isAmountEqual=Newinvoice_Record_Model::isAmountEqual($needPaymentArray,$extendArray,$record);
            //金额必须一样才能下一步
            if($isAmountEqual){
                foreach ($extendArray as $key => $extend){
                    $negativeInfo=json_decode($extend['negativeinfo'],true);
                    $redInvoiceAmount=$negativeInfo['negativeTotalAndTax'];
                    $flag=Newinvoice_Record_Model::isClearVoidOrRed($record);
                    if(!$needPaymentArray||$flag){
                        if($flag){
                            //需要先清空关联回款
                            Newinvoice_Record_Model::emptyInvoiceRey($record);
                        }
                    }else{
                        //红冲废除
                        $value['invoiceextendid']=$extend['invoiceextendid'];
                        $value['record']=$record;
                        foreach ($needPaymentArray as $paymentkey=>$needPayment){
                            //拼接金额参数
                            $value['tovoidform'][$paymentkey]=$paymentkey+1;
                            $value['newinvoiceraymentid'][$paymentkey+1]=$needPayment['newinvoiceraymentid'];
                            //重新封装一下参数
                            $_REQUEST['servicecontractsid'][$paymentkey+1]=$needPayment['servicecontractsid'];
                            $_REQUEST['receivedpaymentsid'][$paymentkey+1]=$needPayment['receivedpaymentsid'];
                            $_REQUEST['total'][$paymentkey+1]=$needPayment['total'];
                            $_REQUEST['invoicetotal'][$paymentkey+1]=$needPayment['invoicetotal'];
                            $_REQUEST['allowinvoicetotal'][$paymentkey+1]=$needPayment['allowinvoicetotal'];
                            $_REQUEST['contract_no'][$paymentkey+1]=$needPayment['contract_no'];
                            if($redInvoiceAmount>$needPayment['voidorredtotal']){
                                $_REQUEST['tovoie_total'][$paymentkey+1]=$value['tovoie_total'][$paymentkey+1]=floatval($needPayment['voidorredtotal']);
                                $redInvoiceAmount=$redInvoiceAmount-$needPayment['voidorredtotal'];
                                $needPaymentArray[$paymentkey]['voidorredtotal']=0;
                            }else{
                                $_REQUEST['tovoie_total'][$paymentkey+1]=$value['tovoie_total'][$paymentkey+1]=$redInvoiceAmount;
                                $needPaymentArray[$paymentkey]['voidorredtotal']=$needPaymentArray[$paymentkey]['voidorredtotal']-$redInvoiceAmount;
                                $redInvoiceAmount=0;
                            }
                        }
                        $voidRequest=new Vtiger_Request($value);
                        $voidObject=new Newinvoice_Tovoid_Action();
                        //调用作废方法
                        $voidObject->tt_add_redInvoice($voidRequest);
                    }
                    //回款废除完了之后废除
                    $negative_data = array();
                    $negative_data['negativeinvoicecodeextend'] = $negativeInfo['negativeInvoiceCode']; //
                    $negative_data['negativeinvoice_noextend'] = $negativeInfo['negativeInvoiceNo'];   //
                    $negative_data['negativebusinessnamesextend'] = $negativeInfo['negativeBusinessNames'];
                    $negative_data['negativebillingtimerextend'] = $negativeInfo['negativeBillingTime'];
                    $negative_data['negativecommoditynameextend'] = $negativeInfo['negativeCommodityName']; //不必填
                    $negative_data['negativeamountofmoneyextend'] = $negativeInfo['negativeAmountOfMoney'];
                    $negative_data['negativetaxrateextend'] = $negativeInfo['negativeTaxRate'];
                    $negative_data['negativetaxextend'] = $negativeInfo['negativeTax'];
                    $negative_data['negativetotalandtaxextend'] = $negativeInfo['negativeTotalAndTax'];
                    $negative_data['negativeremarkextend'] = $negativeInfo['negativeRemark'];    ////不必填
                    $negative_data['invoiceextendid'] =$extend['invoiceextendid'];
                    $negative_data['invoiceid'] = $record;
                    $negative_data['negativedrawerextend'] = $current_user->id;
                    $negative_data['negativeinvoiceextendid'] = '';
                    $divideNames = array_keys($negative_data);
                    $divideValues = array_values($negative_data);
                    $db->pquery('INSERT INTO `vtiger_newnegativeinvoice` ('. implode(',', $divideNames).') VALUES ('. generateQuestionMarks($divideValues) .')',$divideValues);
                    // 重新计算实际开票金额
                    Newinvoice_Record_Model::calcActualtotal($record, true);
                    $datetime=date('Y-m-d H:i:s');
                    $sql="UPDATE vtiger_newinvoiceextend SET invoicestatus='redinvoice',processstatus=2,operator=?,operatortime=? WHERE invoiceid=? AND invoiceextendid=?";
                    $db->pquery($sql,array($current_user->id,$datetime,$record, $extend['invoiceextendid']));
                    // 这里判断 对应的所有发票是否都已经作废，
                    $sql = "select invoiceextendid from vtiger_newinvoiceextend where invoicestatus!='redinvoice' AND invoiceid=?";
                    $sel_result = $db->pquery($sql, array($record));
                    $res_cnt    = $db->num_rows($sel_result);
                    if ($res_cnt == 0) {  // 没有其它的状态
                        $sql = "update vtiger_newinvoice set invoicestatus=? where invoiceid=?";
                        $db->pquery($sql, array('redinvoice', $record));
                        $sql = "update vtiger_newinvoice set modulestatus='c_cancel' where invoiceid=?";
                        $db->pquery($sql, array($record));
                    }
                }
            }else{
                $resultaa['success'] = 'false';
                $resultaa['error']['message'] = "开票金额和废除/红冲金额不相等";
                echo json_encode($resultaa);
                exit;
            }
        }
    }

    /**
     * 判断废弃金额是否和红冲金额一致
     * @param $needPaymentArray
     * @param $extendArray
     * @param $record
     * @return bool
     * @throws Exception
     */
    static public function isAmountEqual($needPaymentArray,$extendArray,$record){
        $flag=Newinvoice_Record_Model::isClearVoidOrRed($record);
        //有回款或者不是预开票需要清空的
        if($needPaymentArray&&!$flag){
            $needPaymentAmount=0;
            foreach ($needPaymentArray as $needPayment){
                $needPaymentAmount=bcadd($needPaymentAmount,$needPayment['voidorredtotal'],2);
            }
            $negativeMoney=0;
            foreach ($extendArray as $extend){
                $negativeInfo=json_decode($extend['negativeinfo'],true);
                $negativeMoney=bcadd($negativeMoney,$negativeInfo['negativeTotalAndTax'],2);
            }
            if(bccomp($needPaymentAmount,$negativeMoney,2)!=0){
                //废弃金额和红冲金额不一样
                return false;
            }
        }
        return true;
    }

    /**
     * 在流程中获取发票
     * @param $recordid
     * @return array
     */
    static public function getMoreinvoiceWithStatus($recordid) {
        $db=PearDatabase::getInstance();
        $sql = "SELECT * FROM vtiger_newinvoiceextend WHERE vtiger_newinvoiceextend.deleted = 0
                AND vtiger_newinvoiceextend.invoiceid ='{$recordid}' and invoicestatus='normal' and processstatus=0";
        $newinvoiceextend = $db->run_query_allrecords($sql);  // 获取发票信息
        foreach ($newinvoiceextend as $key=>$value) {
            $sql = "SELECT * FROM vtiger_newnegativeinvoice WHERE vtiger_newnegativeinvoice.deleted = 0 AND vtiger_newnegativeinvoice.invoiceextendid='{$value['invoiceextendid']}'";
            $newnegativeinvoice =  $db->run_query_allrecords($sql);  // 获取红冲信息
            // 计算红冲的和
            $negativetotalandtaxextend = 0;
            if(count($newnegativeinvoice) > 0) {
                foreach($newnegativeinvoice as $v) {
                    $negativetotalandtaxextend += $v['negativetotalandtaxextend'];
                }
            }
            $newinvoiceextend[$key]['newnegativeinvoice'] = $newnegativeinvoice;
            $newinvoiceextend[$key]['surplusnewnegativeinvoice'] = $value['totalandtaxextend'] - $negativetotalandtaxextend;
        }
        return $newinvoiceextend;
    }


    /**
     * 判断是否需要清空发票关联回款
     * @param $invoiceid
     * @throws Exception
     */
    static public function isClearVoidOrRed($invoiceid){
        $db = PearDatabase::getInstance();
        $sql = "select invoicetype from vtiger_newinvoice where invoiceid=? limit 1";
        $sel_result = $db->pquery($sql, array($invoiceid));
        $res_cnt = $db->num_rows($sel_result);
        if ($res_cnt > 0) {
            $row = $db->query_result_rowdata($sel_result, 0);
            $invoicetype = $row['invoicetype'];
            $sql = "select invoiceid from vtiger_newinvoicerayment where invoiceid=? AND deleted=0 LIMIT 1";
            $sel_result = $db->pquery($sql, array($invoiceid));
            $res_cnt = $db->num_rows($sel_result);

            if ($invoicetype == 'c_billing' && $res_cnt > 0) { // 如果是预开票
                $needTotal = Newinvoice_Record_Model::caclNeedTotal($invoiceid);
                if ($needTotal != 0) {  // 需要的匹配回款金额为0
                    return true;
                }
            }
        }
        return false;
    }


    /**
     * 清空回款
     * @param $invoiceid
     */
    static public function emptyInvoiceRey($invoiceid) {
        // 清空 关联回款
        $db = PearDatabase::getInstance();
        $sql = "select newinvoiceraymentid,receivedpaymentsid,invoicetotal from vtiger_newinvoicerayment where invoiceid=? AND deleted=0";
        $sel_result = $db->pquery($sql, array($invoiceid));
        $res_cnt = $db->num_rows($sel_result);
        if ($res_cnt > 0) {
            $recordModel = Vtiger_DetailView_Model::getInstance('Newinvoice', $invoiceid)->getRecord();
            $entityData = $recordModel ->entity->column_fields;
            while($rawData=$db->fetch_array($sel_result)) {
                $sql = "update vtiger_newinvoicerayment set deleted=1  where newinvoiceraymentid=? ";
                $db->pquery($sql, array($rawData['newinvoiceraymentid']));
                Newinvoice_Record_Model::setAllowinvoicetotalLog($rawData['receivedpaymentsid'], $rawData['invoicetotal'], '（预发票清空关联回款处理）'. ' 发票编号:'.$entityData['invoiceno'].'）');
                $sql = " UPDATE vtiger_receivedpayments SET allowinvoicetotal=allowinvoicetotal+{$rawData['invoicetotal']} WHERE receivedpaymentsid=? ";
                $db->pquery($sql, array($rawData['receivedpaymentsid']));
            }
        }
    }

    /**判断有没有作废或者红冲流程
     * @param $recordId
     * @return int
     * @throws Exception
     */
    static public function isVoidFlow($recordId){
        $db = PearDatabase::getInstance();
        $sql = "select * from vtiger_salesorderworkflowstages where salesorderid=? AND modulename=? and workflowstagesflag in ('APPLICATION_VOID','APPLICATION_RED')";
        $sel_result = $db->pquery($sql, array($recordId,'Newinvoice'));
        $res_cnt = $db->num_rows($sel_result);
        if ($res_cnt > 0) {
            $workflowstagesflag=$db->query_result($sel_result,0,'workflowstagesflag');
            if($workflowstagesflag=='APPLICATION_VOID'){
                return 1;
            }
            return 2;
        }
        return 0;
    }

    /**
     * 获取相应发票申请单下的洞察力订单
     * @param $invoiceid
     * @return array
     */
    static public function getDongchaliList($invoiceid){
        $db = PearDatabase::getInstance();
        $sql="select * from  vtiger_dongchaliorder where invoiceid='".$invoiceid."' and isused=1";
        return $db->run_query_allrecords($sql);
    }

    static public function getDongchaliListWithDeleted($invoiceid){
        $db = PearDatabase::getInstance();
        $sql="select * from  vtiger_dongchaliorder where invoiceid='".$invoiceid."' and deleted=0";
        return $db->run_query_allrecords($sql);
    }

    /**
     * 获取需要废弃的洞察力订单
     * @param $invoiceid
     * @return array
     */
    static public function getDongchaliListVoid($invoiceid){
        $db = PearDatabase::getInstance();
        $sql="select * from  vtiger_dongchaliorder where invoiceid='".$invoiceid."' and deleted=0 and invoicemoney=voidmoney";
        return $db->run_query_allrecords($sql);
    }

    public function getInvoiceCompanyByContractId($contractid){
        $db = PearDatabase::getInstance();
        $result = $db->pquery("select invoicecompany from vtiger_newinvoice where contractid=? and modulestatus!='c_cancel'",array($contractid));
        if(!$db->num_rows($result)){
            return '';
        }
        $resData = $db->fetchByAssoc($result,0);
        return $resData['invoicecompany'];
    }

    public function submitInvoiceVerify(Vtiger_Request $request){
        $verifyinvoiceids = $request->get("verifyinvoiceid");
        if(empty($verifyinvoiceids)){
            return;
        }
        $verifyinvoiceids = explode(",",$verifyinvoiceids);
        foreach ($verifyinvoiceids as $verifyinvoiceid){
            $this->makeWorkFlow($verifyinvoiceid);
        }
    }

    public function makeWorkFlow($recordId){
        $detailModel=Vtiger_DetailView_Model::getInstance('Newinvoice',$recordId);
        global $current_user;
        $recordModel=$detailModel->getRecord();
        $ncolumn_fields=$recordModel->entity->column_fields;
        $db = $recordModel->entity->db;
        $preInvoiceFlag=in_array($ncolumn_fields['invoicecompany'],array('凯丽隆（广州）信息科技有限公司','凯丽隆（上海）信息科技有限公司','凯丽隆（上海）软件信息科技有限公司','上海凯丽隆大数据科技集团有限公司'));

        if ($ncolumn_fields['invoicetype'] == 'c_normal') {
            $query="SELECT sum(invoicetotal) AS invoicetotal FROM vtiger_newinvoicerayment WHERE deleted=0 AND invoiceid=?";
            $invoiceTotalResult=$db->pquery($query,array($recordId));
            $query="SELECT sum(vtiger_receivedpayments.allowinvoicetotal) AS allowinvoicetotal FROM vtiger_receivedpayments LEFT JOIN vtiger_newinvoicerayment ON vtiger_newinvoicerayment.receivedpaymentsid=vtiger_receivedpayments.receivedpaymentsid WHERE  vtiger_newinvoicerayment.deleted=0 AND vtiger_newinvoicerayment.invoiceid=?";
            $allowInvoiceTotalResult=$db->pquery($query,array($recordId));
            $invoiceTotalData = $db->query_result_rowdata($invoiceTotalResult, 0);
            $allowInvoiceTotalData = $db->query_result_rowdata($allowInvoiceTotalResult, 0);
            if($allowInvoiceTotalData['allowinvoicetotal']<=0 || ($allowInvoiceTotalData['allowinvoicetotal']-$invoiceTotalData['invoicetotal'])<0){
//                $data['msg']="可用的开票金额已被占用!";
//                break;
            }
        }
        $newinvoiceWordflows=array('1'=>'599627', '2'=>'599631', '3'=>'599639','4'=>778075,'5'=>'2690719'); //线上的

        if($ncolumn_fields['modulename']=='ServiceContracts'){

            $accountRecordModel=Accounts_Record_Model::getInstanceById($recordModel->entity->column_fields['account_id'],"Accounts");
            $accountcolumn_fields=$accountRecordModel->entity->column_fields;
            $accountname=$accountcolumn_fields['accountname'];

        }else{
            $accountRecordModel=Vtiger_Record_Model::getInstanceById($recordModel->entity->column_fields['account_id'],"Vendors");
            $accountcolumn_fields=$accountRecordModel->entity->column_fields;
            $accountname=$accountcolumn_fields['vendorname'];
        }

        $accountname=preg_replace('/\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;|&quot;|　|&apos;|&amp;|&lt;|&gt;|&#039;|&ldquo;|&rdquo;|&lsquo;|&rsquo;|&hellip;|\“|\”|\‘|\〉|\〈|\’|\〖|\〗|\【|\】|\、|\·|\……|\…|\——|\＋|\－|\＝|\,|\<|\.|\>|\/|\?|\;|\:|\\\'|\"|\[|\{|\]|\}|\\|\||\`|\~|\!|\@|\#|\$|\%|\^|\\&|\*|\(|\)|\-|\_|\=|\+|\，|\＜|\．|\＞|\／|\？|\；|\：|\＇|\＂|\［|\{|\］|\}|\＼|\｜|\｀|\￣|\！|\＠|\#|\＄|\％|\＾|\＆|\＊|\（|\）|\－|\＿|\＝|\＋|\，|\《|\．|\》|\、|\？|\；|\：|\’|\”|\【|\｛|\】|\｝|\＼|\｜|\·|\～|\！|\＠|\＃|\￥|\％|\……|\…|\＆|\×|\（|\）|\－|\——|\—|\＝|\＋|\，|\《|\。|\》|\、|\？|\；|\：|\‘|\“|\【|\｛|\】|\｝|\、|\||\·|\~|\！|\@|\#|\￥|\%|\……|\…|\&|\*|\（|\）|\-|\——|\=|\+/u','',$accountname);
        $accountname=strtoupper($accountname);
        $businessnamesone=$ncolumn_fields['businessnamesone'];
        $businessnamesone=preg_replace('/\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;|&quot;|　|&apos;|&amp;|&lt;|&gt;|&#039;|&ldquo;|&rdquo;|&lsquo;|&rsquo;|&hellip;|\“|\”|\‘|\〉|\〈|\’|\〖|\〗|\【|\】|\、|\·|\……|\…|\——|\＋|\－|\＝|\,|\<|\.|\>|\/|\?|\;|\:|\\\'|\"|\[|\{|\]|\}|\\|\||\`|\~|\!|\@|\#|\$|\%|\^|\\&|\*|\(|\)|\-|\_|\=|\+|\，|\＜|\．|\＞|\／|\？|\；|\：|\＇|\＂|\［|\{|\］|\}|\＼|\｜|\｀|\￣|\！|\＠|\#|\＄|\％|\＾|\＆|\＊|\（|\）|\－|\＿|\＝|\＋|\，|\《|\．|\》|\、|\？|\；|\：|\’|\”|\【|\｛|\】|\｝|\＼|\｜|\·|\～|\！|\＠|\＃|\￥|\％|\……|\…|\＆|\×|\（|\）|\－|\——|\—|\＝|\＋|\，|\《|\。|\》|\、|\？|\；|\：|\‘|\“|\【|\｛|\】|\｝|\、|\||\·|\~|\！|\@|\#|\￥|\%|\……|\…|\&|\*|\（|\）|\-|\——|\=|\+/u','',$businessnamesone);
        $businessnamesone=strtoupper($businessnamesone);
        $matchover='';
        if ($ncolumn_fields['invoicetype']=='c_normal'){
            if ($accountname==$businessnamesone) {
                $workflowsid=$newinvoiceWordflows['1'];
            } else {
                $workflowsid=$newinvoiceWordflows['2'];
            }
            $matchover='matchover=1,matchtimeover=0,';
        } else {
            if ($accountname==$businessnamesone) {
                if($preInvoiceFlag){
                    $workflowsid=$newinvoiceWordflows['5'];
                }else{
                    $workflowsid=$newinvoiceWordflows['3'];
                }
            } else {
                $workflowsid=$newinvoiceWordflows['4'];
            }
        }

        $_REQUEST['workflowsid']=$workflowsid;
        $focus=CRMEntity::getInstance('Newinvoice');
        $focus->makeWorkflows('Newinvoice',$_REQUEST['workflowsid'],$recordId,'edit');
        // 2019-08-20 cxh start
        $query="UPDATE vtiger_salesorderworkflowstages,
				 vtiger_newinvoice
				SET vtiger_salesorderworkflowstages.accountid=vtiger_newinvoice.accountid, vtiger_salesorderworkflowstages.salesorder_nono = vtiger_newinvoice.invoiceno,
				  vtiger_salesorderworkflowstages.modulestatus='p_process',
				 vtiger_salesorderworkflowstages.accountname=(SELECT vtiger_account.accountname FROM vtiger_account WHERE vtiger_account.accountid=vtiger_newinvoice.accountid)
				WHERE vtiger_newinvoice.invoiceid=vtiger_salesorderworkflowstages.salesorderid
				AND vtiger_salesorderworkflowstages.salesorderid=?";
        $focus->db->pquery($query,array($recordId));
        //更改工作流节点指定审核人
        $departmentid=$_SESSION['userdepartmentid'];
        if($preInvoiceFlag&&$ncolumn_fields['invoicetype']!='c_normal'&&$accountname==$businessnamesone){
            $focus->setAudituid('PreInvoiceAuditSetting',$departmentid,$recordId,$workflowsid);
        }else{
            $focus->setAudituid('ContractsAuditset',$departmentid,$recordId,$workflowsid);
        }
        //新建时 消息提醒第一审核人进行审核
        $object = new SalesorderWorkflowStages_SaveAjax_Action();
        $object->sendWxRemind(array('salesorderid'=>$recordId,'salesorderworkflowstagesid'=>0));
        // 2019-08-20 cxh end
        $sql = "select workflowstagesname from vtiger_workflowstages where workflowsid=? order by sequence LIMIT 1";
        $sel_result=$focus->db->pquery($sql, array($workflowsid));
        $res_cnt=$db->num_rows($sel_result);
        $workflowsnode='';
        if ($res_cnt > 0) {
            $row = $db->query_result_rowdata($sel_result, 0);
            $workflowsnode = $row['workflowstagesname'];
        }
        $focus->db->pquery("UPDATE `vtiger_newinvoice` SET {$matchover}modulestatus='b_check',workflowsid=?,workflowsnode=? WHERE invoiceid=?", array($workflowsid, $workflowsnode, $recordId));
        if ($ncolumn_fields['invoicetype'] == 'c_normal') {
            $query="SELECT receivedpaymentsid,invoicetotal FROM vtiger_newinvoicerayment WHERE deleted=0 AND invoiceid=?";
            $receResult=$db->pquery($query,array($recordId));
            while($row=$db->fetch_array($receResult)){
                Newinvoice_Record_Model::setAllowinvoicetotalLog($row['receivedpaymentsid'], -$row['invoicetotal'], '（发票回款关联信息'. ' 发票编号:'.$ncolumn_fields['invoiceno'].'）');
                $sql = " UPDATE vtiger_receivedpayments SET allowinvoicetotal=if(allowinvoicetotal-{$row['invoicetotal']}<0,0,allowinvoicetotal-{$row['invoicetotal']}) WHERE receivedpaymentsid=? ";
                $db->pquery($sql, array($row['receivedpaymentsid']));
            }
            Newinvoice_Record_Model::calcTaxtotal($recordId, true);
        }
    }

    public function canSubmitVerify(Vtiger_Request $request){
        $newinvoiceids=$request->get("verifyinvoiceid");
        foreach ($newinvoiceids as $recordId){
            $detailModel=Vtiger_DetailView_Model::getInstance('Newinvoice',$recordId);
            $db = PearDatabase::getInstance();
            $recordModel=$detailModel->getRecord();
            $ncolumn_fields=$recordModel->entity->column_fields;
            if ($ncolumn_fields['invoicetype'] == 'c_normal') {
                $query="SELECT sum(invoicetotal) AS invoicetotal FROM vtiger_newinvoicerayment WHERE deleted=0 AND invoiceid=?";
                $invoiceTotalResult=$db->pquery($query,array($recordId));
                $query="SELECT sum(vtiger_receivedpayments.allowinvoicetotal) AS allowinvoicetotal FROM vtiger_receivedpayments LEFT JOIN vtiger_newinvoicerayment ON vtiger_newinvoicerayment.receivedpaymentsid=vtiger_receivedpayments.receivedpaymentsid WHERE  vtiger_newinvoicerayment.deleted=0 AND vtiger_newinvoicerayment.invoiceid=?";
                $allowInvoiceTotalResult=$db->pquery($query,array($recordId));
                $invoiceTotalData = $db->query_result_rowdata($invoiceTotalResult, 0);
                $allowInvoiceTotalData = $db->query_result_rowdata($allowInvoiceTotalResult, 0);
                if($allowInvoiceTotalData['allowinvoicetotal']<=0 || ($allowInvoiceTotalData['allowinvoicetotal']-$invoiceTotalData['invoicetotal'])<0){
                    return array('success'=>0,'msg'=>'可用的开票金额已被占用');
                }

                $newInvoiceBasicAction = new Newinvoice_BasicAjax_Action();
                if(!$newInvoiceBasicAction->isSignedWithContractAndStayPayment($ncolumn_fields['contractid'])){
                    return array('success'=>0,'msg'=>'正常开票需合同和代付款签收后方可提交审批');
                }
            }
        }

        return array('success'=>1,'msg'=>'');
    }

    public function removeFile(Vtiger_Request $request){
        $fileid=$request->get("fileid");
        $userid = $request->get("userid");
        $db=PearDatabase::getInstance();
        $db->pquery("update vtiger_files set delflag=1,deletertime=?,deleter=? where attachmentsid=?",array(date("Y-m-d H:i:s"),$userid,$fileid));
        return array('success'=>1,'msg'=>'删除成功');
    }

    public function canSubmitVerifyInvoice($contractid,$saveTotal=0){
        $db = PearDatabase::getInstance();
        $result = $db->pquery("select frameworkcontract,total from vtiger_servicecontracts where servicecontractsid=?",array($contractid));
        if(!$db->num_rows($result)){
            return false;
        }
        $data = $db->fetchByAssoc($result,0);
        if($data['frameworkcontract']=='yes'){
            return true;
        }
        $total=$data['total'];
//        $result2 = $db->pquery("select 1 from vtiger_newinvoice where contractid=? and modulestatus='b_check'",array($contractid));
//        if (!$db->num_rows($result2)){
//            return true;
//        }
        if(floatval($saveTotal) && floatval($saveTotal)>floatval($total)&&$total){
            return false;
        }
        $result3 = $db->pquery("select sum(taxtotal) as total from vtiger_newinvoice where contractid=? and modulestatus !='c_cancel'",array($contractid));
        $data2=$db->fetchByAssoc($result3,0);
        if(($data2['total']>$total || (floatval($saveTotal)&&(floatval($data2['total'])+floatval($saveTotal))>floatval($total)))&& $total>0){
            return false;
        }
        return true;
    }

    public function checkBusinessnamesAndCustomerNameIsSame($contractid,$accountname){
        $db =PearDatabase::getInstance();
        $result = $db->pquery("select c.accountname,a.businessnamesone,a.invoiceno from vtiger_newinvoice a left join vtiger_account c on a.accountid=c.accountid
where a.contractid=? and a.modulestatus in ('b_check','c_complete')",array($contractid));
        if(!$db->num_rows($result)){
            return array('success'=>false,'msg'=>'不存在不一致');
        }
        while ($row=$db->fetchByAssoc($result)){
            if($row['accountname']!=$accountname){
                return array('success'=>true,'msg'=>'该合同编号下已开具抬头为'.$row['accountname'].'的发票（发票编号'.$row['invoiceno'].'），与订单的合同公司名称不一致，请前往PC端进行发票作废或者红冲后再完成下单；');
            }
        }
        return array('success'=>false,'msg'=>'不存在不一致');
    }

    public function getSumTaxTotal($contractid){
        $db = PearDatabase::getInstance();
        $result = $db->pquery("SELECT
	sum(taxtotal) as sumtaxtotal
FROM
	vtiger_newinvoice a
	LEFT JOIN vtiger_account b ON a.accountid = b.accountid 
WHERE
	a.contractid =? 
	AND a.modulestatus in ('c_complete','b_check') 
    AND a.iscancel!=1",array($contractid));
        $row = $db->fetchByAssoc($result,0);
        return $row['sumtaxtotal'];
    }

         /* 记录发票日志
     * @param $data
     * @param string $file
     */
    static public function recordLog($data, $file = 'logs_'){
        global $root_directory;
        $year	= date("Y");
        $month	= date("m");
        $dir	= $root_directory.'logs/invoice/' . $year . '/' . $month . '/';
        if(!is_dir($dir)) {
            mkdir($dir,0755,true);
        }
        $file = $dir . $file . date('Y-m-d').'.txt';
        @file_put_contents($file, '----------------' . date('H:i:s') . '--------------------'.PHP_EOL.var_export($data,true).PHP_EOL, FILE_APPEND);
    }
}
