<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/


class ReceivableOverdue_Record_Model extends Vtiger_Record_Model
{
    public function getContractId()
    {
        $receivableoverdueid = $this->getId();
        global $adb;
        $sql = "select contractid from vtiger_receivable_overdue where receivableoverdueid=? ";
        $result = $adb->pquery($sql, array($receivableoverdueid));
        if ($adb->num_rows($result)) {
            $data = $adb->query_result_rowdata($result, 0);
            return $data['contractid'];
        }
        return 0;
    }

    public function getStageList($contractid)
    {
        global $adb;
        $sql = "select a.stageshow from vtiger_contracts_execution_detail a  where a.contractid=? order by stage asc";
        $result = $adb->pquery($sql, array($contractid));
        $stageList = array();
        if ($adb->num_rows($result)) {
            while ($row = $adb->fetchByAssoc($result)) {
                $stageList[] = $row['stageshow'];
            }
        }
        return $stageList;

    }

    public function sendWarningEmail($overdueDatas)
    {
        if (empty($overdueDatas)) {
            return;
        }
        $Subject = '合同阶段收款已逾期！！！';
        $str = '';
        foreach ($overdueDatas as $signId => $overdueData) {
            $str .= "<table style='border: 1px solid black;border-collapse: collapse'><tr><th style='border-right: 1px solid black'>合同编号</th>
                    <th  style='border-right: 1px solid black'>客户名称</th>
                    <th style='border-right: 1px solid black'>业务类型</th>
                    <th style='border-right: 1px solid black'>合同额</th>
                    <th style='border-right: 1px solid black'>产品类型</th>
                    <th style='border-right: 1px solid black'>合同阶段</th>
                    <th style='border-right: 1px solid black'>合同签订人</th>
                    <th style='border-right: 1px solid black'>签订日期</th>
                    <th style='border-right: 1px solid black'>应收金额</th>
                    <th style='border-right: 1px solid black'>应收时间</th>
                    <th style='border-right: 1px solid black'>逾期天数</th>
                    <th>逾期应收余额</th></tr>";
            foreach ($overdueData as $value) {
                $str .= '<tr style=\'border: 1px solid black\'>
                        <td style=\'border-right: 1px solid black\'>' . $value['contract_no'] . '</td>
                        <td style=\'border-right: 1px solid black\'>' . $value['accountid'] . '</td>
                        <td style=\'border-right: 1px solid black\'>' . vtranslate($value['bussinesstype'],'ServiceContracts') . '</td>
                        <td style=\'border-right: 1px solid black\'>' . $value['contracttotal'] . '</td>
                        <td style=\'border-right: 1px solid black\'>' . $value['productid'] . '</td>
                        <td style=\'border-right: 1px solid black\'>' . $value['stageshow'] . '</td>
                        <td style=\'border-right: 1px solid black\'>' . $value['signname'] . '</td>
                        <td style=\'border-right: 1px solid black\'>' . $value['signdate'] . '</td>
                        <td style=\'border-right: 1px solid black\'>' . $value['receiveableamount'] . '</td>
                        <td style=\'border-right: 1px solid black\'>' . $value['receiverabledate'] . '</td>
                        <td style=\'border-right: 1px solid black\'>' . $value['overduedays'] . '</td>
                        <td>' . $value['contractreceivable'] . '</td>
                </tr>';
            }
            $str .= "</table><br>";
            $lastBody = '<span style="color: red">以下合同阶段收款已逾期，请及时跟进回款，可至ERP系统财务应收模块“逾期应收明细表”中填写跟进内容</span><br><br>' . $str;
            $address = $this->getAllEmail($signId);
            $this->_logs(array('address'=>$address,'text'=>$lastBody));
            Vtiger_Record_Model::sendMail($Subject, $lastBody, $address);
        }
    }


    public function sendWarningWx($overdueDatas)
    {
        if (empty($overdueDatas)) {
            return;
        }
        foreach ($overdueDatas as $signId => $overdueData) {
            foreach ($overdueData as $value) {
                $content = '合同编号:'.$value['contract_no'].'<br>客户:'.$value['accountid'].'<br>签订人:'.$value['signname'].'<br>应收金额:'.$value['receiveableamount'].'<br>逾期天数:'.$value['overduedays'].'<br>逾期应收:'.$value['contractreceivable'];
                $allEmail = $this->getAllEmail($signId);
                $email = '';
                foreach ($allEmail as $all){
                    $email .= $all['mail'].'|';
                }
                $this->_logs(array('wechatMessage','email'=>$email,'description'=>$content));
                $this->sendWechatMessage(array('email'=>trim($email),'description'=>$content,'dataurl'=>'#','title'=>'合同阶段收款已逾期！！！','flag'=>7));
            }
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

    public function getAllEmail($signId)
    {
        $allSuperiorIds = getAllSuperiorIds($signId);
        array_push($allSuperiorIds, $signId);
        global $adb;
        $sql = "select email1,reports_to_id,last_name,wechatid from vtiger_users where id in (" . implode(',', $allSuperiorIds) . ")";
        $result = $adb->pquery($sql, array());
        $address = array();
        while ($row = $adb->fetchByAssoc($result)) {
            $address[] = array('mail' => $row['wechatid'], 'name' => $row['last_name']);
        }
        return $address;
    }

}
