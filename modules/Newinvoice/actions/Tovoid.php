<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Newinvoice_Tovoid_Action extends Vtiger_Save_Action {

	function __construct() {
        parent::__construct();
        $this->exposeMethod('addRedInvoice');
    }

	function checkPermission(Vtiger_Request $request) {

        return;
	}

    public function addRedInvoice(Vtiger_Request $request) {
        /*
            添加 vtiger_negativeinvoice 这个表
        */
        global $current_user;

        $recordId = $request->get('record');

        $invoiceextendid = $request->get('invoiceextendid');
        $negative_data = array();
        $negative_data['negativeinvoicecodeextend'] = $request->get('negativeinvoicecodeextend'); // 
        $negative_data['negativeinvoice_noextend'] = $request->get('negativeinvoice_noextend');   //
        $negative_data['negativebusinessnamesextend'] = $request->get('negativebusinessnamesextend');
        $negative_data['negativebillingtimerextend'] = $request->get('negativebillingtimerextend');
        $negative_data['negativecommoditynameextend'] = $request->get('negativecommoditynameextend'); //不必填
        $negative_data['negativeamountofmoneyextend'] = $request->get('negativeamountofmoneyextend');
        $negative_data['negativetaxrateextend'] = $request->get('negativetaxrateextend');
        $negative_data['negativetaxextend'] = $request->get('negativetaxextend');
        $negative_data['negativetotalandtaxextend'] = $request->get('negativetotalandtaxextend');
        $negative_data['negativeremarkextend'] = $request->get('negativeremarkextend');    ////不必填
        $negative_data['invoiceextendid'] = $request->get('invoiceextendid');
        $negative_data['invoiceid'] = $recordId;
        do{

            // 判断是否有权限
            // 红冲权限
            $privileges = Users_Privileges_Model::isPermitted('Newinvoice', 'NegativeEdit', $recordId);
            $is_admin = $current_user->is_admin == 'on' ? 1 : 0;
            if ( ! ($privileges || $is_admin) ) {
                break;
            }
            $flag = false;
            foreach ($negative_data as $key => $value) {
                if ($key != 'negativecommoditynameextend' && $key != 'negativeremarkextend') {
                    $value = trim($value);
                    if (empty($value)) {
                        $flag = true;
                    }
                }
            }
            if ($flag) {
                break;
            }

            $tovoidform = $request->get('tovoidform');

            if (!empty($tovoidform)) {
                $flag = $this->tt_add_redInvoice($request);
                if (!$flag) {
                    break;
                }
            }

             
            // 开票人
            $negative_data['negativedrawerextend'] = $current_user->id;
            $negative_data['negativeinvoiceextendid'] = '';

            $divideNames = array_keys($negative_data);
            $divideValues = array_values($negative_data);
            
            $db = PearDatabase::getInstance();

            $db->pquery('INSERT INTO `vtiger_newnegativeinvoice` ('. implode(',', $divideNames).') VALUES ('. generateQuestionMarks($divideValues) .')',$divideValues);

            // 重新计算实际开票金额
            Newinvoice_Record_Model::calcActualtotal($recordId, true);

            $datetime=date('Y-m-d H:i:s');
            $sql="UPDATE vtiger_newinvoiceextend SET invoicestatus='redinvoice',processstatus=2,operator=?,operatortime=? WHERE invoiceid=? AND invoiceextendid=?";
            $db->pquery($sql,array($current_user->id,$datetime,$recordId, $invoiceextendid));

            // 这里判断 对应的所有发票是否都已经作废，
            $sql = "select invoiceextendid from vtiger_newinvoiceextend where invoicestatus!='redinvoice' AND invoiceid=?";
            $sel_result = $db->pquery($sql, array($recordId));
            $res_cnt    = $db->num_rows($sel_result);
            if ($res_cnt == 0) {  // 没有其它的状态 
                $sql = "update vtiger_newinvoice set invoicestatus=? where invoiceid=?";
                $db->pquery($sql, array('redinvoice', $recordId));
                $sql = "update vtiger_newinvoice set modulestatus='c_cancel' where invoiceid=?";
                $db->pquery($sql, array($recordId));
                //只有当发票全废除时解锁
                Newinvoice_Record_Model::updateInvoiceWithOutPayment($recordId);
            }
        }while(0);
        $record = $request->get('record');
        $recordModel = Vtiger_Record_Model::getInstanceById($record, 'Newinvoice');
        $loadUrl = $recordModel->getDetailViewUrl();
        if(empty($loadUrl)){
            $loadUrl="index.php";
        }
        header("Location: $loadUrl");
    }

    // 红冲操作
    public function tt_add_redInvoice(Vtiger_Request $request) {
        $invoiceextendid = $request->get('invoiceextendid'); // 发票id
        $newinvoiceraymentid = $request->get('newinvoiceraymentid');
        $tovoie_total = $request->get('tovoie_total');
        $record = $request->get('record');
        $tovoidform = $request->get('tovoidform'); // 发票id 

        $recordModel = Vtiger_DetailView_Model::getInstance('Newinvoice', $record)->getRecord();
        $entityData = $recordModel ->entity->column_fields;


        $totalandtaxextend = 0;
        $set_sql = "";
        $insert_sql = "";
        $t_time = date('Y-m-d H:i:s');
        // 每个回款作废的金额为多少
        $setNewinvoicerayment = array();
        $db = PearDatabase::getInstance();
        global $current_user;
        $flag = true;
        if (is_array($tovoidform)) {
            $newinvoicerayment = array();
            foreach ($tovoidform as $value) {
                $newinvoicerayment[$newinvoiceraymentid[$value]] = $tovoie_total[$value];

                //$totalandtaxextend += $tovoie_total[$value];
                $totalandtaxextend =bcadd($tovoie_total[$value],$totalandtaxextend,2);

                $set_sql .= " when newinvoiceraymentid={$newinvoiceraymentid[$value]} then {$tovoie_total[$value]} ";

                $insert_sql .= "(NULL, '{$_REQUEST['servicecontractsid'][$value]}', '{$_REQUEST['receivedpaymentsid'][$value]}', '{$_REQUEST['total'][$value]}', '{$_REQUEST['invoicetotal'][$value]}', '{$_REQUEST['allowinvoicetotal'][$value]}', '{$tovoie_total[$value]}', '{$_REQUEST['contract_no'][$value]}', '{$current_user->id}', '{$t_time}', '{$invoiceextendid}', '2', '{$newinvoiceraymentid[$value]}'),";
                
                $setNewinvoicerayment[] = array('receivedpaymentsid'=>$_REQUEST['receivedpaymentsid'][$value], 'tovoie_total'=>$_REQUEST['tovoie_total'][$value], 'newinvoiceraymentid'=>$newinvoiceraymentid[$value]);

                 // 判断每个回款的作废金额 是否 大于 此次开票金额
                $sql = "select newinvoiceraymentid from vtiger_newinvoicerayment where surpluinvoicetotal<{$_REQUEST['tovoie_total'][$value]} AND newinvoiceraymentid=?";
                $sel_result = $db->pquery($sql, array($newinvoiceraymentid[$value]));
                $res_cnt = $db->num_rows($sel_result);
                if($res_cnt > 0) {
                    $flag = false;
                }
            }
        }
                // 判断 作废发票的金额是否 小于 原始发票的价税合计 是否一致 totalandtaxextend
        $sql = " SELECT invoiceextendid, totalandtaxextend - IFNULL(( SELECT SUM(negativetotalandtaxextend) FROM vtiger_newnegativeinvoice WHERE invoiceextendid = ? and deleted = 0), 0) AS 'totalandtaxextend' FROM vtiger_newinvoiceextend WHERE invoiceextendid = ? AND deleted = 0";
        $sel_result = $db->pquery($sql, array($invoiceextendid, $invoiceextendid));

        $res_cnt = $db->num_rows($sel_result);
        if ($res_cnt > 0 &&  $flag) {
            $row = $db->query_result_rowdata($sel_result, 0);
            if(bccomp($row['totalandtaxextend'] , $totalandtaxextend,2)<0) {
                return false;
            } 

            //$sql = " update vtiger_newinvoicerayment set allowinvoicetotal= allowinvoicetotal + case {$set_sql} end where invoiceid=? ";
            //$db->pquery($sql, array($record));

            // 添加作废记录
            $sql = "INSERT INTO `vtiger_newinvoicetovoid` (`newinvoicetovoidid`, `servicecontractsid`, `receivedpaymentsid`, `total`, `invoicetotal`, `allowinvoicetotal`, `tovoidtotal`, `contract_no`, `createid`, `createtime`, `invoiceextendid`, `type`, `newinvoiceraymentid`) VALUES ";
            $insert_sql = trim($insert_sql, ',');
            $sql .= $insert_sql;
            $db->pquery($sql, array());
            foreach ($setNewinvoicerayment as $value) {

                Newinvoice_Record_Model::setAllowinvoicetotalLog($value['receivedpaymentsid'], $value['tovoie_total'], '（发票红冲）'. ' 发票编号:'.$entityData['invoiceno'].'）');

                $t = $value['tovoie_total'];
                $sql = "update vtiger_receivedpayments set allowinvoicetotal=allowinvoicetotal+{$t} where receivedpaymentsid=? LIMIT 1";
                $db->pquery($sql, array($value['receivedpaymentsid']));

                $sql = "update vtiger_newinvoicerayment set surpluinvoicetotal=surpluinvoicetotal-{$t},invoicetotal=invoicetotal-{$t} where receivedpaymentsid=? AND newinvoiceraymentid=?";
                $db->pquery($sql, array($value['receivedpaymentsid'], $value['newinvoiceraymentid']));
            }
        } else {
            return false;
        }

        return true;
    }



    // 作废操作
    public function tt_tovoid(Vtiger_Request $request, $type = 'tovoid') {
        $invoiceextendid = $request->get('invoiceextendid'); // 发票id
        $newinvoiceraymentid = $request->get('newinvoiceraymentid');
        $tovoie_total = $request->get('tovoie_total');
        $record = $request->get('record');
        $tovoidform = $request->get('tovoidform'); // 发票id 

        $recordModel = Vtiger_DetailView_Model::getInstance('Newinvoice', $record)->getRecord();
        $entityData = $recordModel ->entity->column_fields;

        $totalandtaxextend = 0;
        $set_sql = "";
        $insert_sql = "";
        $t_time = date('Y-m-d H:i:s');

        // 每个回款作废的金额为多少
        $setNewinvoicerayment = array();
        global $current_user;
        $db = PearDatabase::getInstance();
        $flag = true;
        if (is_array($tovoidform)) {
            $newinvoicerayment = array();
            foreach ($tovoidform as $value) {
                $newinvoicerayment[$newinvoiceraymentid[$value]] = $tovoie_total[$value];

                $totalandtaxextend += $tovoie_total[$value];

                $set_sql .= " when newinvoiceraymentid={$newinvoiceraymentid[$value]} then {$tovoie_total[$value]} ";

                $insert_sql .= "(NULL, '{$_REQUEST['servicecontractsid'][$value]}', '{$_REQUEST['receivedpaymentsid'][$value]}', '{$_REQUEST['total'][$value]}', '{$_REQUEST['invoicetotal'][$value]}', '{$_REQUEST['allowinvoicetotal'][$value]}', '{$tovoie_total[$value]}', '{$_REQUEST['contract_no'][$value]}', '{$current_user->id}', '{$t_time}', '{$invoiceextendid}', '1', '{$newinvoiceraymentid[$value]}'),";
                
                $setNewinvoicerayment[] = array('receivedpaymentsid'=>$_REQUEST['receivedpaymentsid'][$value], 'tovoie_total'=>$_REQUEST['tovoie_total'][$value], 'newinvoiceraymentid'=>$newinvoiceraymentid[$value]);

                // 判断每个回款的作废金额 是否 大于 此次开票金额
                $sql = "select newinvoiceraymentid from vtiger_newinvoicerayment where surpluinvoicetotal<{$_REQUEST['tovoie_total'][$value]} AND newinvoiceraymentid=?";
                $sel_result = $db->pquery($sql, array($newinvoiceraymentid[$value]));
                $res_cnt = $db->num_rows($sel_result);
                if($res_cnt > 0) {
                    $flag = false;
                }
            }
        }
        
        // 判断 作废发票的金额是否 和 原始发票的价税合计 是否一致
        $sql = " select invoiceextendid from vtiger_newinvoiceextend where invoiceextendid=? AND totalandtaxextend=? AND deleted=0";
        $sel_result = $db->pquery($sql, array($invoiceextendid, $totalandtaxextend));
        $res_cnt = $db->num_rows($sel_result);
        if ($res_cnt > 0 && $flag) {
            //$sql = " update vtiger_newinvoicerayment set allowinvoicetotal= allowinvoicetotal + case {$set_sql} end where invoiceid=? ";
            //$db->pquery($sql, array($record));

            if ($type == 'tovoid') {
                // 发票改为作废
                $sql = " update vtiger_newinvoiceextend set  invoicestatus='tovoid',processstatus=2 where invoiceextendid=?";
                $db->pquery($sql, array($invoiceextendid));
            }

            // 重新计算实际开票金额
            Newinvoice_Record_Model::calcActualtotal($record, true);
            
            // 添加作废记录
            $sql = "INSERT INTO `vtiger_newinvoicetovoid` (`newinvoicetovoidid`, `servicecontractsid`, `receivedpaymentsid`, `total`, `invoicetotal`, `allowinvoicetotal`, `tovoidtotal`, `contract_no`, `createid`, `createtime`, `invoiceextendid`, `type`,`newinvoiceraymentid`) VALUES ";
            $insert_sql = trim($insert_sql, ',');
            $sql .= $insert_sql;
            $db->pquery($sql, array());
            foreach ($setNewinvoicerayment as $value) {

                Newinvoice_Record_Model::setAllowinvoicetotalLog($value['receivedpaymentsid'], $value['tovoie_total'], '（发票作废）'. ' 发票编号:'.$entityData['invoiceno'].'）');

                $t = $value['tovoie_total'];
                $sql = "update vtiger_receivedpayments set allowinvoicetotal=allowinvoicetotal+{$t} where receivedpaymentsid=? LIMIT 1";
                $db->pquery($sql, array($value['receivedpaymentsid']));

                // 减去 剩余此次开票金额
                $sql = "update vtiger_newinvoicerayment set surpluinvoicetotal=surpluinvoicetotal-{$t} where receivedpaymentsid=? AND newinvoiceraymentid=?";
                $db->pquery($sql, array($value['receivedpaymentsid'], $value['newinvoiceraymentid']));
                
            }

            // 这里判断 对应的所有发票是否都已经作废，
            $sql = "select invoiceextendid from vtiger_newinvoiceextend where invoicestatus!='tovoid' AND invoiceid=?";
            $sel_result = $db->pquery($sql, array($record));
            $res_cnt    = $db->num_rows($sel_result);
            if ($res_cnt == 0) {  // 没有其它的状态 
                $sql = "update vtiger_newinvoice set invoicestatus=? where invoiceid=?";
                $db->pquery($sql, array('tovoid', $record));
                $sql = "update vtiger_newinvoice set modulestatus='c_cancel' where invoiceid=?";
                $db->pquery($sql, array($record));
                //只有当发票全废除时解锁
                Newinvoice_Record_Model::updateInvoiceWithOutPayment($record);
            }
        }
    }


    /**设当前发票的状态是作废还是退票
     * @ruthor steel
     * @time 2015-05-04
     * @param Vtiger_Request $request
     * @throws Exception
     */
	public function process(Vtiger_Request $request) {
        $mode = $request->getMode();
        if(!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);
            return;
        }
        global $current_user;
        $record = $request->get('record');

        //echo 11;die;
        //$recordModel = $this->saveRecord($request);
        // 判断是否有权限
        $privileges = Users_Privileges_Model::isPermitted('Newinvoice', 'ToVoid', $record);
        $is_admin = $current_user->is_admin == 'on' ? 1 : 0;

        if ($privileges || $is_admin) {
            $this->tt_tovoid($request);
        }

        $recordModel = Vtiger_Record_Model::getInstanceById($record, 'Newinvoice');
        $loadUrl = $recordModel->getDetailViewUrl();
        if(empty($loadUrl)){
            $loadUrl="index.php";
        }
        header("Location: $loadUrl");
		/*$response = new Vtiger_Response();
		$response->setResult($return);
		$response->emit();*/
	}
}
