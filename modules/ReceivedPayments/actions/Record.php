<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ReceivedPayments_Record_Action extends Vtiger_Action_Controller {
    function checkPermission(Vtiger_Request $request) {
        return;
    }

    public function process(Vtiger_Request $request) {
        //$paytitle = '上海沃福林汽车音响改装店';
        $paytitle = $request->get('paytitle');
        $accountdata = ReceivedPayments_Record_Model::match_account($paytitle);
        $result = array($accountdata['crmid'],$accountdata['label']);
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }

    /**
     * 拆分回款公共方法
     * @param Vtiger_Request $request
     * @return array|bool[]
     * @throws Exception
     */
    public function splitReceive(Vtiger_Request $request) {
        $recordId = $request->get('record');  // 回款id
        $contract_no = $request->get('contract_no'); //合同编号 id
        $split_money = $request->get('split_money');   //拆分金额
        $t_split_money = $request->get('t_split_money');   //拆分后的原始金额
        $unit_price = $request->get('unit_price');   //原款金额

        $recordModel=Vtiger_Record_Model::getInstanceById($recordId,'ReceivedPayments');
        $retrun=array('flag'=>false);

        do {
            if (!is_numeric($split_money) || $split_money <= 0) {
                $retrun['msg']='拆分金额必需大于0';
                break;
            }
            global $current_user;
            $db = PearDatabase::getInstance();
            $allowinvoicetotal=$recordModel->get('allowinvoicetotal');//可开票金额
            $rechargeableamount=$recordModel->get('rechargeableamount');//可使用金额
            $occupationcost=$recordModel->get('occupationcost');//已占用工单成本
            $unit_price=$recordModel->get('unit_price');//入账金额
            $chargebacks=$recordModel->get('chargebacks');//扣款金额
            $relatetoid=$recordModel->get('relatetoid');//合同ＩＤ
            $standardmoney=$recordModel->get('standardmoney');//原币金额
            $exchangerate=$recordModel->get('exchangerate');//原币金额
            $t_split_money=bcsub($unit_price,$split_money,2);
            //原币金额
            $standardmoneynew=bcdiv($split_money,$exchangerate,2);
            if ($t_split_money<=0) {
                $retrun['msg']='拆分金额必需小于入账金额!';
                break;
            }
            $relatetoid=$recordModel->get('relatetoid');
            if($relatetoid){
                if(empty($contract_no)){
                    $retrun['msg']='已匹配的回款合同编号必需存在!';
                    break;
                }
            }
            $receivedstatus=$recordModel->get('receivedstatus');
            if($receivedstatus!='virtualrefund' && bccomp($split_money,$allowinvoicetotal,2)>0){
                $retrun['msg']='拆分金额,大于可开票金额!';
                break;
            }
            if(bccomp($standardmoneynew,$rechargeableamount,2)>0){//原币金额与可使用金额对比
                $retrun['msg']='拆分金额,大于可使用金额!';
                break;
            }
            if($receivedstatus=='virtualrefund'){
                if($contract_no<=0) {
                    $retrun['msg']='虚拟回款拆分,合同必需存在!';
                    break;
                }else{
                    $oldcontractrecordModel=Vtiger_Record_Model::getInstanceById($recordModel->get('relatetoid'),'ServiceContracts');
                    $contractRecordModel=Vtiger_Record_Model::getInstanceById($contract_no,'ServiceContracts');
                    if($oldcontractrecordModel->get('sc_related_to')!=$contractRecordModel->get('sc_related_to')){
                        $retrun['msg']='虚拟回款拆分,必需和原合同是同一客户!';
                        break;
                    }
                }
            }
            if ($contract_no<=0) {
                $contract_no = '';
            }else{
                $contractRecordModel=Vtiger_Record_Model::getInstanceById($contract_no,'ServiceContracts');
                if($contract_no!=$relatetoid && $contractRecordModel->get('multitype')!=1 && $contractRecordModel->get('total')!=0){
                    $query="SELECT sum(unit_price) as sumunitprice FROM vtiger_receivedpayments WHERE relatetoid=? AND receivedstatus='normal' AND deleted=0";
                    $dataResult=$db->pquery($query,array($contract_no));
                    $contaunitprice=0;//合同已匹配的回款总额
                    if($db->num_rows($dataResult)){
                        $data=$db->query_result_rowdata($dataResult,0);
                        $contaunitprice=$data['sumunitprice'];
                    }
                    $diffprice=bcsub($contractRecordModel->get('total'),$contaunitprice,2);
                    if($diffprice<=0 || bccomp($diffprice,$split_money,2)<0){
                        $retrun['msg']='回款金额之合大于合同金额！';
                        break;
                    }
                }

            }
            // 1. 获取 回款

            $standardmoney=$standardmoney-$standardmoneynew;
            $sql = "SELECT *  FROM vtiger_receivedpayments WHERE receivedpaymentsid=? limit 1";
            $sel_result = $db->pquery($sql, array($recordId));
            $oldRow = $db->query_result_rowdata($sel_result, 0);
            $row = $oldRow;
            $receivedpaymentsid = $db->getUniqueID('vtiger_receivedpayments');
            $row['old_receivedpaymentsid'] = $row['receivedpaymentsid'];
            $row['receivedpaymentsid'] = $receivedpaymentsid;
            $row['unit_price'] = $split_money;
            $row['standardmoney'] = $standardmoneynew;
            $row['createtime'] = date('Y-m-d H:i:s');
            $row['relatetoid'] = $contract_no;
            $row['ancestor_receivedpaymentsid'] = $oldRow['ancestor_receivedpaymentsid'];
            $row['old_receivedpaymentsid'] = trim($oldRow['old_receivedpaymentsid'].','.$recordId,',');
//            $row['old_receivedpaymentsid'] = $recordId;
            $row['overdue'] = $row['overdue'] . ' | 拆分回款';
            $row['allowinvoicetotal'] = ($receivedstatus!='virtualrefund'?$split_money:0);
            $row['rechargeableamount'] = $standardmoneynew;
            $row['chargebacks'] = 0;
            $row['occupationcost'] = 0;
            $row['chargebacksremak'] = '';
            $row['artificialclassfication'] = $oldRow['artificialclassfication'];
            $row['systemclassfication'] = $oldRow['systemclassfication'];
            if ($row['relatetoid'] > 0) {
                $row['ismatchdepart'] = 1;
            } else {
                $row['ismatchdepart'] = 0;
                $row['departmentid'] = '';
                $row['newdepartmentid'] = '';

            }
            foreach ($row as $key => $value) {
                if (is_numeric($key)) {
                    unset($row[$key]);
                }
            }
            //$current_id = $db->getUniqueID("vtiger_receivedpayments");
            $current_id = $receivedpaymentsid;
            $row['receivedpaymentsid'] = $current_id;
            // 添加数据
            $divideNames = array_keys($row);
            $divideValues = array_values($row);
            /*echo 'INSERT INTO `vtiger_receivedpayments` ('. implode(',', $divideNames).') VALUES ('. generateQuestionMarks($divideValues) .')';
            exit;*/
            foreach ($divideValues as $k => $v) {
                if (empty($v)) {
                    $divideValues[$k] = '';
                }
            }
            /*$sql = 'INSERT INTO `vtiger_receivedpayments` ('. implode(',', $divideNames).') VALUES (' . implode(',', $divideValues) . ')';
            echo $sql;
            exit;*/

            $db->pquery('INSERT INTO `vtiger_receivedpayments` (' . implode(',', $divideNames) . ') VALUES (' . generateQuestionMarks($divideValues) . ')', $divideValues);
            // cxh 2020-08-01 拆分回款的成本
            $result =$db->pquery(" SELECT * FROM vtiger_receivedpayments_extra WHERE receivementid=? ",array($recordId));
            $strValues='';
            $deleteid='';
            while ($rowData=$db->fetch_array($result)){
                $oldvalues[]=$rowData['receivementid'];
                $newvalues[]=$receivedpaymentsid;
                $oldvalues[]=$rowData['extra_type'];
                $newvalues[]=$rowData['extra_type'];
                $oldvalues[]=$rowData['extra_price']-$rowData['extra_price']*($row['unit_price']/$oldRow['unit_price']);
                $newvalues[]=$rowData['extra_price']*($row['unit_price']/$oldRow['unit_price']);
                $oldvalues[]=$rowData['extra_remark'];
                $newvalues[]=$rowData['extra_remark'];
                $strValues.='(?,?,?,?),';
                $deleteid.=$rowData['id'].',';
            }
            /*var_dump($oldvalues);
            var_dump($newvalues);exit();*/
            $strValues=trim($strValues,',');
            $deleteid=trim($deleteid,',');
            $db->pquery('DELETE FROM vtiger_receivedpayments_extra WHERE id IN('.$deleteid.')',array());
            // 插入拆分回款的成本
            $db->pquery('INSERT INTO vtiger_receivedpayments_extra (receivementid,extra_type,extra_price,extra_remark) VALUES '.$strValues,array($newvalues));
            // 插入原回款的新成本
            $db->pquery('INSERT INTO vtiger_receivedpayments_extra (receivementid,extra_type,extra_price,extra_remark) VALUES '.$strValues,array($oldvalues));
            // cxh 2020-08-01 拆分回款的成本 end

            $id = $db->getUniqueId('vtiger_modtracker_basic');
            $db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
                array($id, $current_id, 'ReceivedPayments', $current_user->id, date('Y-m-d H:i:s'), 0));
            $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
                Array($id, 'overdue', '', "回款拆分" . $t_split_money . '=>' . $split_money));

            // 更新原始回款
            $sql = "UPDATE vtiger_receivedpayments set unit_price=?, standardmoney=?, old_receivedpaymentsid=?, allowinvoicetotal=if(allowinvoicetotal-{$split_money}>0,(allowinvoicetotal-{$split_money}),0),rechargeableamount=if(rechargeableamount-{$standardmoneynew}>0,(rechargeableamount-{$standardmoneynew}),0) WHERE receivedpaymentsid=?";
            $db->pquery($sql, array($t_split_money, $standardmoney,  trim($oldRow['old_receivedpaymentsid'].','.$recordId,','), $recordId));

            //$Matchreceivements_BasicAjax_Action=new Matchreceivements_BasicAjax_Action();
            // 更新回款中的分成比例
            if ($row['relatetoid'] > 0) { //如果回款已经匹配
                // 新生成的回款
                $divide_arr = ServiceContracts_Record_Model::servicecontracts_divide($row['relatetoid']);
                for ($i = 0; $i < count($divide_arr); ++$i) {
                    $divide_temp = $divide_arr[$i];
                    $divide_data['owncompanys'] = $divide_temp['owncompanys'];
                    $divide_data['receivedpaymentsid'] = $current_id;
                    $divide_data['receivedpaymentownid'] = $divide_temp['receivedpaymentownid'];
                    $divide_data['scalling'] = $divide_temp['scalling'];
                    $divide_data['servicecontractid'] = $row['relatetoid'];
                    if (!empty($row['unit_price'])) {
                        $divide_data['businessunit'] = ($divide_temp['scalling'] * $row['unit_price']) / 100;
                        $divideNames = array_keys($divide_data);
                        $divideValues = array_values($divide_data);
                        $db->pquery('INSERT INTO `vtiger_achievementallot` (' . implode(',', $divideNames) . ') VALUES (' . generateQuestionMarks($divideValues) . ')', $divideValues);
                    }
                }
                $receiveRecordModel = Vtiger_Record_Model::getCleanInstance("ReceivedPayments");
                $receiveRecordModel->setUpdateSalesorder($row['relatetoid']);
                //$Matchreceivements_BasicAjax_Action->commonInsertAchievementallotStatistic($current_id,$row['unit_price'],0,0,$row['relatetoid']);
            }

            // 老的回款 更新回款记录
            // 删除老的回款分成比例 在重新添加
            if ($oldRow['relatetoid'] > 0) { //老的回款已经匹配
                $sql = "delete from vtiger_achievementallot where receivedpaymentsid=?";
                $db->pquery($sql, array($oldRow['receivedpaymentsid']));

                //重新添加
                $divide_arr = ServiceContracts_Record_Model::servicecontracts_divide($oldRow['relatetoid']);
                for ($i = 0; $i < count($divide_arr); ++$i) {
                    $divide_temp = $divide_arr[$i];
                    $divide_data['owncompanys'] = $divide_temp['owncompanys'];
                    $divide_data['receivedpaymentsid'] = $oldRow['receivedpaymentsid'];
                    $divide_data['receivedpaymentownid'] = $divide_temp['receivedpaymentownid'];
                    $divide_data['scalling'] = $divide_temp['scalling'];
                    $divide_data['servicecontractid'] = $oldRow['relatetoid'];
                    if (!empty($oldRow['unit_price'])) {
                        $divide_data['businessunit'] = ($divide_temp['scalling'] * $t_split_money) / 100;
                        $divideNames = array_keys($divide_data);
                        $divideValues = array_values($divide_data);
                        $db->pquery('INSERT INTO `vtiger_achievementallot` (' . implode(',', $divideNames) . ') VALUES (' . generateQuestionMarks($divideValues) . ')', $divideValues);

                    }
                }
                //$Matchreceivements_BasicAjax_Action->commonInsertAchievementallotStatistic($oldRow['receivedpaymentsid'],$t_split_money,0,0,$oldRow['relatetoid']);
            }

            // 如果回款已经匹配 新拆分的回款添加回款匹配记录
            if ($row['relatetoid'] > 0) { //如果回款已经匹配
                $t_id = $db->getUniqueID("vtiger_receivedpayments_notes");
                $sql = "INSERT INTO `vtiger_receivedpayments_notes` (`receivedpaymentsnotesid`, `createtime`, `smownerid`, `receivedpaymentsid`, `notestype`) VALUES ('{$t_id}', '" . date('Y-m-d H:i:s') . "', '{$current_user->id}', '{$current_id}', 'notestype2')";
                $db->pquery($sql, array());
            }
            $retrun=array('flag'=>true,'msg'=>$receivedpaymentsid);

        }while(0);
        return $retrun;
    }
}
