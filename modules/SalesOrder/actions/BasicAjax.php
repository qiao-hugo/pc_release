<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class SalesOrder_BasicAjax_Action extends Vtiger_Action_Controller {

    function __construct() {
        parent::__construct();
        $this->exposeMethod('docancel');
        $this->exposeMethod('saveSalesorderRayment');
        $this->exposeMethod('activeRepairOrder');
        $this->exposeMethod('getReceivedPaymentsHistory');
        $this->exposeMethod('changesApplicant');
        $this->exposeMethod('changesUpdate');
        $this->exposeMethod('submitSaveSalesorderRayment');
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
        if (!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);
            return;
        }
    }

    /**
     * 关联开票信息
     * @param Vtiger_Request $request
     */
    public function docancel(Vtiger_Request $request) {
        $recordid = $request->get('record');
        $voidreason = $request->get('voidreason');
        global $current_user, $adb;
        $recordModel = Vtiger_Record_Model::getInstanceById($recordid, 'SalesOrder');
        $ret_result = array("success" => true, "message" => "");

        //设置工单作废权限 gaocl add 2018/05/16
        $moduleModel = Vtiger_Module_Model::getCleanInstance('SalesOrder');
        $is_salesorder_tovoid = $moduleModel->exportGrouprt('SalesOrder', 'orderCancel');
        //正常、打回、回款不足的工单可作废
        if (!$is_salesorder_tovoid) {
            $ret_result = array("success" => false, "message" => "没有工单作废的权限");
            $response = new Vtiger_Response();
            $response->setResult($ret_result);
            $response->emit();
            return;
        }
        if ($recordModel->isSalesorderProductsRel($recordid)) {
            $arr_modulestatus = array("a_normal", "a_exception", "c_lackpayment");
            if (!in_array($recordModel->entity->column_fields['modulestatus'], $arr_modulestatus)) {
                $ret_result = array("success" => false, "message" => "只能作废正常、打回、回款不足的工单");
                $response = new Vtiger_Response();
                $response->setResult($ret_result);
                $response->emit();
                return;
            }
        }

        //$modulestatus=array('c_cancel','c_complete');
        //$modulestatus=array('c_cancel');
        //if($current_user->is_admin=='on'&&!in_array($recordModel->entity->column_fields['modulestatus'],$modulestatus)){
        //更新记录
        $currentTime = date('Y-m-d H:i:s');
        $adb->pquery("UPDATE vtiger_salesorder SET iscancel=1,modulestatus=?,voidreason=?,voiduserid=?,voiddatetime=? WHERE salesorderid=?", array('c_cancel', $voidreason, $current_user->id, $currentTime, $recordid));
        // 删除保证金
        $adb->pquery("UPDATE vtiger_guarantee SET deleted=1 WHERE salesorderid=?", array($recordid));
        $adb->pquery("UPDATE vtiger_salesorderworkflowstages SET isaction=0 WHERE modulename='SalesOrder' AND isaction=1 AND salesorderid=?", array($recordid));
        $adb->pquery("UPDATE vtiger_salesorderproductsrel SET multistatus=4 WHERE salesorderid=?", array($recordid));

        //更新当前工单所属合同下其他工单状态和节点状态 gaocl add 2018/03/20
        $rel_salesorder_sql = "SELECT salesorderid,modulestatus,servicecontractsid FROM vtiger_salesorder WHERE servicecontractsid  IN (SELECT servicecontractsid FROM vtiger_salesorder WHERE salesorderid=?)
                        AND salesorderid !=?
                        AND modulestatus != 'c_cancel'
                        AND EXISTS (SELECT 1 FROM vtiger_crmentity WHERE vtiger_crmentity.crmid=salesorderid AND vtiger_crmentity.deleted=0)";

        $result_salesorder = $adb->pquery($rel_salesorder_sql, array($recordid, $recordid));
        $num_salesorder = $adb->num_rows($result_salesorder);
        if ($num_salesorder > 0) {
            for ($i = 0; $i < $num_salesorder;  ++$i) {
                $salesorderid = $adb->query_result($result_salesorder, $i, 'salesorderid');
                $servicecontractsid = $adb->query_result($result_salesorder, $i, 'servicecontractsid');
                $modulestatus = $adb->query_result($result_salesorder, $i, 'modulestatus');
                if ($modulestatus == 'c_lackpayment') {
                    //回款+ 担保金是否大于成本
                    if (ServiceContracts_Record_Model::receiveDayprice($servicecontractsid, $salesorderid, false)) {
                        //更新状态
                        $adb->pquery("UPDATE vtiger_salesorder SET modulestatus = ? WHERE salesorderid=?", array('b_check', $salesorderid));
                        //激活审核节点
                        $result_worktagesid = $adb->pquery("SELECT salesorderworkflowstagesid FROM vtiger_salesorderworkflowstages WHERE salesorderid=? AND isaction=0 AND modulename='SalesOrder' ORDER BY sequence LIMIT 1", array($salesorderid));
                        $salesorderworkflowstagesid = $adb->query_result($result_worktagesid, 0, 'salesorderworkflowstagesid');
                        $adb->pquery("UPDATE vtiger_salesorderworkflowstages SET isaction=1 WHERE salesorderworkflowstagesid=?", array($salesorderworkflowstagesid));
                    }
                }
            }
        }
        //}

        $response = new Vtiger_Response();
        $response->setResult($ret_result);
        $response->emit();
    }

    /**
     * 保存工单回款数据
     * @param Vtiger_Request $request
     */
    function saveSalesorderRayment(Vtiger_Request $request) {
        $recordModel = Vtiger_Record_Model::getCleanInstance('SalesOrder');
        $response = new Vtiger_Response();
        $response->setResult($recordModel->saveSalesorderRayment($request));
        $response->emit();
    }

    /**
     * 回款成本的使用明细
     * @param Vtiger_Request $request
     * @author: steel.liu
     * @Date:xxx
     *
     */
    public function getReceivedPaymentsHistory(Vtiger_Request $request) {
        $receivedPaymentsRecordModel = Vtiger_Record_Model::getCleanInstance('ReceivedPayments');
        $data = $receivedPaymentsRecordModel->getReceivedPaymentsUseDetail($request);
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
    }

    /**
     *  工单回款不足流程冻结情况下可以重新激活上一个节点
     */
    public function activeRepairOrder(Vtiger_Request $request) {
        global $current_user;
        $recordid = $request->get('record');
        $db = PearDatabase::getInstance();
//        echo $recordid;die;
        $salesorderworkflowstages = $db->pquery("update vtiger_salesorderworkflowstages set isaction=1  where sequence=1 and salesorderid= ?", array($recordid));
        $salemanager = $db->pquery("update vtiger_salesorder set modulestatus = 'b_check' where salesorderid= ?", array($recordid));
        $response = new Vtiger_Response();
        $response->setResult($salemanager);
        $response->emit();
    }
    /**
     * 变更申请人按钮权限控制
     */
    public function changesApplicant(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName); //module相关的数据
        if (!$moduleModel->exportGrouprt('SalesOrder', 'changesApplicant')) {   //权限验证
            $response = new Vtiger_Response();
            $ret_result = array("success" => false, "message" => "没有权限");
            $response->setResult($ret_result);
            $response->emit();
            exit;
        }
        $data = getAccessibleUsers('SalesOrder','List');
        $db = PearDatabase::getInstance();
        if($data=='1=1'){
            $data = '';
        }else{
            $data=' and id  '.$data;
        }
        $res = $db->pquery("SELECT brevitycode,user_name,last_name,id,department,title from vtiger_users WHERE isdimission=0 AND status='active' $data ");
        //$res = $db->pquery("SELECT user_name,last_name,id,department,title from vtiger_users WHERE  id > 1 AND  status='active'");
        $array_data = array();
        if ($db->num_rows($res)) {
            while ($row = $db->fetch_array($res)) {
                $array_data[] = $row;
            }
        }
        $response = new Vtiger_Response();
        $ret_result = array("success" => true, "message" => "成功", "data" => $array_data,'where'=>$data);
        $response->setResult($ret_result);
        $response->emit();
        exit;
    }

    /**
     * 更新变更申请人数据
     */
    public function changesUpdate(Vtiger_Request $request) {
        global $current_user;
        $db = PearDatabase::getInstance();
        $salesorderid = $request->get('record');
        $smcreatorid = $request->get('smcreatorid');
        $db->pquery("update vtiger_crmentity set smownerid=? WHERE crmid =?", array($smcreatorid, $salesorderid));
        $salesorderworkflowstages = $db->pquery("SELECT * from vtiger_salesorderworkflowstages WHERE (isaction=1 or isaction=0) and ishigher=1 and salesorderid=? and (workflowstagesflag='BILL_CONFIRM' or workflowstagesflag='RAYMENT_MATCH' or workflowstagesflag='DO_REFUND' )", array($salesorderid));
        if ($db->num_rows($salesorderworkflowstages)) {
            while ($row = $db->fetch_array($salesorderworkflowstages)) {
                if (!empty($salesorderworkflowstages)) {
                    $db->pquery("update vtiger_salesorderworkflowstages set higherid=? WHERE salesorderworkflowstagesid =?", array($smcreatorid, $row['salesorderworkflowstagesid']));
                }
            }
        }
        //日志插入 cby
        $id = $db->getUniqueId('vtiger_modtracker_basic');
        $currentTime = date('Y-m-d H:i:s');

        $res = $db->pquery("SELECT user_name,last_name,id,department,title from vtiger_users WHERE id=$smcreatorid");
        $row='';
        if ($db->num_rows($res)) {
            $row = $db->fetch_row($res);
        }
        $db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)', array($id, $salesorderid, 'SalesOrder', $current_user->id, $currentTime, 0));
        $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)', Array($id, 'pending', '', '变更负责人为' . $row['last_name'] ));

        $response = new Vtiger_Response();
        $ret_result = array("success" => true, "message" => "成功");
        $response->setResult($ret_result);
        $response->emit();
        exit();
    }

    /**
     * 工单回款匹配
     * @param Vtiger_Request $request
     */
    public function submitSaveSalesorderRayment(Vtiger_Request $request){
        global $adb,$current_user;
        $salesorderid=$request->get('record');
        $receivedpaymentsid=$request->get('raymentid');
        $purchasecost=$request->get('purchasecost');
        $rremarks=$request->get('rremarks');
        $returnData=array("flag" =>false, "msg" => "成功");
        do{
            if($salesorderid<=0){
                $returnData['msg']= "提交工单信息有误";
                break;
            }
            if($receivedpaymentsid<=0){
                $returnData['msg']= "回款信息有误";
                break;
            }
            if($purchasecost<=0){
                $returnData['msg']= "工单使用金额有误";
                break;
            }
            $recordModel=Vtiger_Record_Model::getInstanceById($salesorderid,'SalesOrder');

            if($recordModel->get('assigned_user_id')!=$current_user->id){
                $returnData['msg']= "非操作人";
                break;
            }
            $query='SELECT 1 FROM vtiger_salesorderrayment WHERE salesorderid=? AND receivedpaymentsid=?';
            $result=$adb->pquery($query,array($salesorderid,$receivedpaymentsid));
            if($adb->num_rows($result)){
                $returnData['msg']= "回款已被使用不可重复匹配！";
                break;
            }
            $query='SELECT 
                vtiger_receivedpayments.*,
                IFNULL(vtiger_salesorderrayment.laborcost,\'0.00\') AS laborcost,
                IFNULL(vtiger_salesorderrayment.purchasecost,\'0.00\') AS purchasecost,
                vtiger_salesorderrayment.remarks as rremarks,
                0 AS israyment
                FROM vtiger_receivedpayments
                LEFT JOIN vtiger_salesorderrayment ON(
                 vtiger_salesorderrayment.deleted=0 
                 AND vtiger_salesorderrayment.receivedpaymentsid=vtiger_receivedpayments.receivedpaymentsid
                 AND vtiger_salesorderrayment.salesorderid=?
                )
                WHERE vtiger_receivedpayments.receivedstatus=\'normal\' AND vtiger_receivedpayments.deleted=0 AND rechargeableamount>0 AND vtiger_receivedpayments.receivedpaymentsid=?';
            $result=$adb->pquery($query,array($salesorderid,$receivedpaymentsid));
            $rechargeableamount=$result->fields['rechargeableamount'];//可使用金额
            if(bccomp($rechargeableamount,$purchasecost,4)<0){
                $returnData['msg']= "工单可使用金额与实际金额不符";
                break;
            }
            $laborcost=0;
            $occupationcost=0;
            $totalcost=0;
            //减回款
            $update_sql = "INSERT INTO vtiger_salesorderrayment(salesorderid,receivedpaymentsid,availableamount,occupationcost,laborcost,purchasecost,totalcost,modifiedby,modifiedtime,deleted,remarks) VALUES(?,?,?,?,?,?,?,?,NOW(),0,?)";
            $adb->pquery($update_sql,array($salesorderid,$receivedpaymentsid,$rechargeableamount,$occupationcost,$laborcost,$purchasecost,$totalcost,$current_user->id,$rremarks));
            $adb->pquery("UPDATE `vtiger_receivedpayments` SET occupationcost=(occupationcost+{$purchasecost}),rechargeableamount=if((rechargeableamount-{$purchasecost})>0,(rechargeableamount-{$purchasecost}),0) WHERE receivedpaymentsid=?",array($receivedpaymentsid));
            //算业绩匹配多少就给你算多少
            $recordModel->calcSalesorderAchievement(array(
                'receivedpaymentsid'=>$receivedpaymentsid,
                'salesorderid'=>$salesorderid,
                'purchasecost'=>$purchasecost,
            ));
            $returnData['flag']=true;
        }while(0);
        $response = new Vtiger_Response();
        $response->setResult($returnData);
        $response->emit();

    }

}
