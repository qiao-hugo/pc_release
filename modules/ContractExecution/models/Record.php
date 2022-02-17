<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/



class ContractExecution_Record_Model extends Vtiger_Record_Model
{

    public $contractWorkFlowSid = 2429366;  //合同执行审核id
    public function isLastExecuted($contractid){
        global $adb;

        $sql = "select b.executestatus from vtiger_contracts_execution a left join vtiger_contracts_execution_detail b on a.executiondetailid=b.executiondetailid where a.contractid=? limit 1";
        $result = $adb->pquery($sql,array($contractid));
        if($adb->num_rows($result)){
            $row = $adb->fetchByAssoc($result,0);
            if($row['executestatus']==''){
                return true;
            }
            if($row['executestatus']!='c_executed'){
                return false;
            }
        }
        return true;
    }


    public function getExecutionId($contractid){
        global $adb;
        $sql = "select contractexecutionid from vtiger_contracts_execution  where contractid=?";
        $result = $adb->pquery($sql,array($contractid));
        if($adb->num_rows($result)){
            $row = $adb->query_result_rowdata($result,0);
            return $row['contractexecutionid'];
        }
        return 0;
    }

    /**
     * 生成执行阶段和审核流
     *
     * @param $contractexecutionid
     * @param $contractid
     * @param $data
     * @return mixed
     */
    public function executionDetail($contractexecutionid,$contractid,$data,$isPass){
        global $adb,$isallow;
        $sql = "insert into vtiger_contracts_execution_detail(`stage`,`stageshow`,`receiveableamount`,`collectiondescription`,`executestatus`,`stagetype`,`contractexecutionid`,`collection`,`accountid`,`contractid`,`contractreceivable`) values (?,?,?,?,?,?,?,'normal',?,?,?)";
        $result = $adb->pquery($sql,array_values($data));
        $executionDetailId = $adb->getLastInsertID();

        $sql2 = "update vtiger_contracts_execution set executiondetailid=?,status=? where contractid=?";
        $status = 'b_execution_actioning';
        if($isPass){
            $status = 'c_execution_complete';
        }
        $result2 = $adb->pquery($sql2,array($executionDetailId,$status,$contractid));


        //生成工作流
        $isallow=array('ContractExecution');
        $focus = CRMEntity::getInstance('ContractExecution');
        $_POST['workflowsid'] =  $this->contractWorkFlowSid;
        $focus->makeWorkflows('ContractExecution', $this->contractWorkFlowSid, $contractexecutionid,'edit');
        $departmentid=$_SESSION['userdepartmentid'];
        $focus->setAudituid('ContractsAuditset',$departmentid,$contractexecutionid,$this->contractWorkFlowSid);

        //新建时 消息提醒第一审核人进行审核
        $object = new SalesorderWorkflowStages_SaveAjax_Action();
        $object->sendWxRemind(array('salesorderid'=>$contractexecutionid,'salesorderworkflowstagesid'=>0));


        $sql = 'delete from vtiger_contract_receivable where contractid=?';
        $adb->pquery($sql,array($contractid));
        $this->insertintoContractReceivable($adb,$contractexecutionid);

        return $executionDetailId;
    }

    /**
     * 生成应收数据
     * @param $adb
     * @param $contractexecutionid
     */
    public function insertintoContractReceivable($adb,$contractexecutionid){
        //插入合同应收表
        $sql3="insert into vtiger_contract_receivable(`accountid`,`contract_no`,`bussinesstype`,`productid`,`signid`,
                                         `contracttotal`,`contractpaidamount`,`contractreceivableamount`,`contractreceivablebalance`,
                                         `contractinvoiceamount`,`collectionstatus`,`signdempart`,`contractid`)                                     
                  select b.accountid,c.contract_no,
                       d.bussinesstype,
                       c.productid ,
                       c.signid,
                       sum(c.total) as contracttotal,
                       ifnull((select sum(unit_price) from vtiger_receivedpayments where relatetoid=c.servicecontractsid and receivedstatus='normal' and deleted=0),0) as contractpaidamount,
                       ifnull((select sum(receiveableamount) from vtiger_contracts_execution_detail where vtiger_contracts_execution_detail.contractexecutionid=a.contractexecutionid),0) as contractreceivableamount,
                       ifnull((select sum(contractreceivable) from vtiger_contracts_execution_detail where vtiger_contracts_execution_detail.contractexecutionid=a.contractexecutionid),0) as contractreceivablebalance,
                       ifnull((select sum(actualtotal) from vtiger_newinvoice where vtiger_newinvoice.contractid=a.contractid and vtiger_newinvoice.modulestatus='c_complete'),0) as contractinvoiceamount,
                         if((select count(1) from vtiger_contracts_execution_detail where vtiger_contracts_execution_detail.contractexecutionid=a.contractexecutionid and vtiger_contracts_execution_detail.receiverabledate is not null and vtiger_contracts_execution_detail.receiverabledate != '0000-00-00 00:00:00' and  vtiger_contracts_execution_detail.receiverabledate<CURRENT_DATE and executestatus='a_no_execute')>0,'hasoverdue','normal') as status,
                         c.signdempart,
                        c.servicecontractsid
                  from vtiger_contracts_execution a
                  left join vtiger_account b on a.accountid=b.accountid
                  left join vtiger_servicecontracts c on a.contractid=c.servicecontractsid
                  left join vtiger_contract_type d on d.contract_type = c.contract_type
                  where c.modulestatus='c_complete' and a.contractexecutionid=?
                group by a.contractid";
        $adb->pquery($sql3,array($contractexecutionid));
    }

    /**
     * 生成应收数据
     * @param $adb
     * @param $contractexecutionid
     */
    public function insertintoContractReceivableFrameContract($adb,$contractid){
        //插入合同应收表
        $sql3 = "insert into vtiger_contract_receivable(`accountid`,`contract_no`,`bussinesstype`,`productid`,`signid`,
                                         `contracttotal`,`contractpaidamount`,`contractreceivableamount`,`contractreceivablebalance`,
                                         `contractinvoiceamount`,`collectionstatus`,`signdempart`,`contractid`)            
         select b.accountid,c.contract_no,
                       d.bussinesstype,
                       c.productid ,
                       c.signid,
                       sum(c.total) as contracttotal,
                       ifnull((select sum(unit_price) from vtiger_receivedpayments where relatetoid=c.servicecontractsid and receivedstatus='normal' and deleted=0),0) as contractpaidamount,
                       ifnull((select sum(receiveableamount) from vtiger_contracts_execution_detail where vtiger_contracts_execution_detail.contractid=c.servicecontractsid),0) as contractreceivableamount,
                       ifnull((select sum(contractreceivable) from vtiger_contracts_execution_detail where vtiger_contracts_execution_detail.contractid=c.servicecontractsid),0) as contractreceivablebalance,
                       ifnull((select sum(actualtotal) from vtiger_newinvoice where vtiger_newinvoice.contractid=c.servicecontractsid and vtiger_newinvoice.modulestatus='c_complete'),0) as contractinvoiceamount,
                       'normal',
                         c.signdempart,
                        c.servicecontractsid
                  from vtiger_servicecontracts c 
                  left join vtiger_account b on b.accountid=c.sc_related_to
                  left join vtiger_contract_type d on d.contract_type=c.contract_type
                  where c.modulestatus='c_complete' and c.servicecontractsid=?
                group by c.servicecontractsid";

        $adb->pquery($sql3,array($contractid));
    }
    /**
     * 生成执行单编号
     */
    public function setContractExecutionNo($contractExecutionId=0){
        global $adb;
        $result = $adb->pquery("select 1 as num from vtiger_contracts_execution",array());
        $num = $adb->num_rows($result);
        $contractExecutionNo = str_pad($num,6,0,STR_PAD_LEFT );
        return 'EX'.$contractExecutionNo;
    }

    public function newContractExecution($data){
        global $adb;
        $recorid = $adb->getUniqueID('vtiger_crmentity');
        $userid = $data['userid'];
        $datetime = date('Y-m-d H:i:s');

        //插入主表
        $crmdata['crmid']=$recorid;
        $crmdata['smcreatorid']=$userid;
        $crmdata['smownerid']=$userid;
        $crmdata['modifiedby']=$userid;
        $crmdata['setype']= 'ContractExecution';
        $crmdata['createdtime']=$datetime;
        $crmdata['modifiedtime']='';
        $crmdata['version']=0;
        $crmdata['deleted']=0;
        $crmdata['label']=$data['contractno'].'应收';
        $crmdataNames = array_keys($crmdata);
        $crmdataValues = array_values($crmdata);
        $adb->pquery('INSERT INTO vtiger_crmentity ('. implode(',', $crmdataNames).') VALUES ('. generateQuestionMarks($crmdataValues) .')', $crmdataValues);

        $fieldData['contractexecutionid'] = $recorid;
        $fieldData['contractexecutionno'] = $this->setContractExecutionNo($recorid);
        $fieldData['contractid'] = $data['contractid'];
        $fieldData['modulestatus'] = 'a_no_execute';
        $fieldData['accountid'] = $data['accountid'];
        $fieldData['sc_related_to'] = $data['accountid'];
        $fieldData['createdate'] = $datetime;
        $fieldData['workflowsid']=$this->contractWorkFlowSid;
        $fieldNames = array_keys($fieldData);
        $fieldValues = array_values($fieldData);
        $adb->pquery('INSERT INTO  vtiger_contracts_execution ('.implode(',', $fieldNames).') VALUES ('.generateQuestionMarks($fieldValues).')',$fieldValues);

        return $recorid;
    }


    public function autoExecute($contractexecutionid,$data){
        global $adb;
        $sql = 'update vtiger_contracts_execution_detail set executor=?,executedate=?,executestatus=?,receiverabledate=?,voucher=? where executiondetailid=?';
        $adb->pquery($sql,array($data['executor'],date('Y-m-d H:i:s'),'c_executed',date('Y-m-d'),$data['voucher'],$data['executiondetailid']));
        //将对应的总表修订
        $sql2 = "update vtiger_contracts_execution set updatedate=?,modulestatus=?,processdate=? where contractexecutionid=?";
        $adb->pquery($sql2,array(date('Y-m-d H:i:s'),'c_complete',date('Y-m-d H:i:s'),$data['contractexecutionid']));

        if($data['fileid']){
            $adb->pquery('update vtiger_files set relationid=? where attachmentsid=?',array($data['contractexecutionid'],$data['fileid']));
        }


        //将工作流更新为已完成
        //节点自动审批
        $adb->pquery("UPDATE vtiger_contracts_execution SET workflowsnode=(SELECT vtiger_salesorderworkflowstages.workflowstagesname FROM `vtiger_salesorderworkflowstages` WHERE vtiger_salesorderworkflowstages.isaction=1 AND vtiger_salesorderworkflowstages.salesorderid=? AND vtiger_salesorderworkflowstages.modulename='ContractExecution' LIMIT 1)  WHERE contractexecutionid=?", array($data['contractexecutionid'], $data['contractexecutionid']));
        $params['salesorderid'] = $data['contractexecutionid'];


        $updateSql = " UPDATE  vtiger_salesorderworkflowstages SET modulestatus=?,isaction=2,auditorid=?,auditortime=?,schedule=100 WHERE salesorderid = ?  AND workflowsid =?";
        $adb->pquery($updateSql,array('c_complete',$data['executor'],date("Y-m-d H:i:s"),$data['contractexecutionid'],$this->contractWorkFlowSid));

        $object = new SalesorderWorkflowStages_SaveAjax_Action();
        $object->sendWxRemind(array('salesorderid'=>$data['contractexecutionid'],'salesorderworkflowstagesid'=>0));

    }


    public function getExecutionDetails()
    {
        $contractExecutionId = $this->getId();
        global $adb;
        $sql = "select a.executiondetailid,a.stageshow,a.receiveableamount,a.collectiondescription,a.receiverabledate,CONCAT(b.last_name,'[',IFNULL(  b.department, '' ),']',( IF ( b.status = 'Active', '', '[离职]' ) ) ) last_name,a.executedate,a.executestatus,a.stagetype,a.voucher from vtiger_contracts_execution_detail a left join vtiger_users b on a.executor=b.id where a.contractexecutionid=? order by a.stage asc  ";
        $result = $adb->pquery($sql,array($contractExecutionId));
        while ($row = $adb->fetchByAssoc($result)) {
            $row['executestatus'] = vtranslate($row['executestatus'],'ContractExecution');
            $voucher = explode('##',$row['voucher']);
            $row['voucher'] = $voucher[0];
            $row['voucherdownloadurl'] = "index.php?module=ContractExecution&action=DownloadFile&filename=".base64_encode($voucher[1]);
            $ret_lists[] = $row;
        }
        return $ret_lists;
    }

    public function sendWarningEmail($overdueDatas)
    {
        if (empty($overdueDatas)) {
            return;
        }
        $Subject = '提醒：合同阶段收款即将逾期';
        $str = '';
        foreach ($overdueDatas as $signId => $overdueData) {
            $str .= "<table style='border: 1px solid black;border-collapse: collapse'><tr><th style='border-right: 1px solid black'>合同编号</th>
                    <th  style='border-right: 1px solid black'>客户名称</th>
                    <th  style='border-right: 1px solid black'>业务类型</th>
                    <th  style='border-right: 1px solid black'>合同额</th>
                    <th style='border-right: 1px solid black'>产品类型</th>
                    <th style='border-right: 1px solid black'>合同阶段</th>
                    <th style='border-right: 1px solid black'>合同签订人</th>
                    <th style='border-right: 1px solid black'>签订日期</th>
                    <th style='border-right: 1px solid black'>应收金额</th>
                    <th>应收时间</th></tr>";
            foreach ($overdueData as $value) {
                $str .= '<tr  style=\'border: 1px solid black\'>
                        <td  style=\'border-right: 1px solid black\'>' . $value['contract_no'] . '</td>
                        <td style=\'border-right: 1px solid black\'>' . $value['accountname'] . '</td>
                        <td style=\'border-right: 1px solid black\'>' . vtranslate($value['bussinesstype'],'ServiceContracts') . '</td>
                        <td style=\'border-right: 1px solid black\'>' . $value['contracttotal'] . '</td>
                        <td style=\'border-right: 1px solid black\'>' . $value['productname'] . '</td>
                        <td style=\'border-right: 1px solid black\'>' . $value['stageshow'] . '</td>
                        <td style=\'border-right: 1px solid black\'>' . $value['signname'] . '</td>
                        <td style=\'border-right: 1px solid black\'>' . $value['signdate'] . '</td>
                        <td style=\'border-right: 1px solid black\'>' . $value['receiveableamount'] . '</td>
                        <td>' . $value['receiverabledate'] . '</td>
                </tr>';
            }
            $str .= "</table><br>";
            $lastBody = '<span style="color: orange">以下合同阶段收款即将逾期，请及时跟进客户回款并完成回款合同匹配！！！</span><br><br>' . $str;
            $address = $this->getEmail($signId);
            $this->_logs(array('address'=>$address,'text'=>$lastBody));
            Vtiger_Record_Model::sendMail($Subject, $lastBody, $address);
        }
    }

    /**
     * 写日志，用于测试,可以开启关闭
     * @param data mixed
     */
    public function _logs($data, $file = 'logs_'){
        $year	= date("Y");
        $month	= date("m");
        $dir	= './logs/tyun/' . $year . '/' . $month . '/';
        if(!is_dir($dir)) {
            mkdir($dir,0755,true);
        }
        $file = $dir . $file . date('Y-m-d').'.txt';
        @file_put_contents($file, '----------------' . date('H:i:s') . '--------------------'.PHP_EOL.var_export($data,true).PHP_EOL, FILE_APPEND);
    }

    public function sendWarningWx($overdueDatas)
    {
        if (empty($overdueDatas)) {
            return;
        }
        foreach ($overdueDatas as $signId => $overdueData) {
            foreach ($overdueData as $value) {
                $content = '合同编号:'.$value['contract_no'].'<br>客户:'.$value['accountname'].'<br>签订人:'.$value['signname'].'<br>应收金额:'.$value['receiveableamount'].'<br>应收时间:'.$value['receiverabledate'];
                $allEmail = $this->getEmail($signId);
                $email = '';
                foreach ($allEmail as $all){
                    $email .= $all['mail'].'|';
                }
                $this->sendWechatMessage(array('email'=>trim($email),'description'=>$content,'dataurl'=>'#','title'=>'提醒：合同阶段收款即将逾期','flag'=>7));
            }
        }
    }

    public function getEmail($signId)
    {
        global $adb;
        $sql = "select email1,reports_to_id,last_name from vtiger_users where id =?";
        $result = $adb->pquery($sql, array($signId));
        $address = array();
        while ($row = $adb->fetchByAssoc($result)) {
            $address[] = array('mail' => $row['email1'], 'name' => $row['last_name']);
        }
        return $address;
    }


    public function getContractIdById($record){
        global $adb;
        $sql = "select contractid from vtiger_contracts_execution where contractexecutionid=? limit 1";
        $result = $adb->pquery($sql,array($record));
        if($adb->num_rows($result)){
            $row = $adb->fetchByAssoc($result,0);
            return $row['contractid'];
        }
        return 0;
    }

    public function getContractNoById($record){
        global $adb;
        $sql = "select vtiger_servicecontracts.contract_no from vtiger_contracts_execution left join vtiger_servicecontracts on vtiger_contracts_execution.contractid=vtiger_servicecontracts.servicecontractsid where vtiger_contracts_execution.contractexecutionid=? limit 1";
        $result = $adb->pquery($sql,array($record));
        if($adb->num_rows($result)){
            $row = $adb->fetchByAssoc($result,0);
            return $row['contract_no'];
        }
        return 0;
    }

}