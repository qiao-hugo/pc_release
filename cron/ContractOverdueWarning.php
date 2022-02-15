<?php
$dir=trim(__DIR__,DIRECTORY_SEPARATOR);
$dir=trim(__DIR__,'cron');
ini_set("include_path", $dir);
require_once('config.php');
require_once('include/utils/utils.php');
require_once('include/logging.php');
header("Content-type:text/html;charset=utf-8");
//error_reporting(0);


include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';


vglobal('default_language', $default_language);
$currentLanguage = 'zh_cn';
vglobal('current_language', $currentLanguage);
global $adb;

//逾期
$sql = "select * from vtiger_earlywarningsetting where isclose=0 and remindertype='Overduewarning' limit 1";
$result = $adb->pquery($sql, array());

if ($adb->num_rows($result)) {
    //逾期的
    $sql2 = "SELECT
                            ( vtiger_servicecontracts.contract_no ) AS contract_no,
                            vtiger_receivable_overdue.bussinesstype,
                            vtiger_receivable_overdue.contracttotal,
                            vtiger_receivable_overdue.stageshow,
                            IFNULL(vtiger_receivable_overdue.receiveableamount,0) as receiveableamount,
                            ifnull(vtiger_receivable_overdue.contractreceivable,0) as contractreceivable,
                            ifnull(vtiger_receivable_overdue.overduedays,0) as overduedays,
                            concat(vtiger_users.last_name,'[',vtiger_departments.departmentname,']') as signname,
                            vtiger_receivable_overdue.signdate,
                            vtiger_receivable_overdue.receiverabledate,
                            ifnull( vtiger_products.productname,vtiger_servicecontracts.contract_type ) AS productid,
                            ( vtiger_account.accountname ) AS accountid,
                            vtiger_receivable_overdue.signid
                        FROM
                            vtiger_receivable_overdue
                            LEFT JOIN vtiger_modcomments ON vtiger_modcomments.moduleid = vtiger_receivable_overdue.contractid
                            LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid = vtiger_receivable_overdue.contractid
                            LEFT JOIN vtiger_products ON vtiger_products.productid = vtiger_receivable_overdue.productid
                            LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_receivable_overdue.accountid 
                            LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_receivable_overdue.signid 
                            LEFT JOIN vtiger_user2department ON vtiger_user2department.userid = vtiger_users.id 
                            LEFT JOIN vtiger_departments ON vtiger_departments.departmentid = vtiger_user2department.departmentid 
                        WHERE
                            1 = 1 and vtiger_receivable_overdue.iscancel=0
                            group by contract_no,signid,stageshow";
    $result2 = $adb->pquery($sql2,array());
    $data = array();
    if($adb->num_rows($result2)){
        while ($row2 = $adb->fetchByAssoc($result2)){
            $data[$row2['signid']][] = $row2;
        }
    }
    echo '<pre>';
    var_dump($data);
    if(!empty($data)){
        $recordModel = Vtiger_Record_Model::getCleanInstance("ReceivableOverdue");
        $row = $adb->fetchByAssoc($result,0);
        $alertChannels = explode(',',$row['alertchannels']);
        foreach ($alertChannels as $alertChannel){
            switch ($alertChannel) {
                case 'email':
                    echo 'warnningemailstart';
                    $recordModel->sendWarningEmail($data);
                    echo 'warnningemailend';
                    break;
                case 'assistant':
                    echo 'assistantstart';
                    $recordModel->sendWarningWx($data);
                    echo 'assistantend';
                    break;
            }
        }
    }

}


$sql3 = "select * from vtiger_earlywarningsetting where isclose=0 and remindertype='rbexp' limit 1";
$result3 = $adb->pquery($sql3, array());
if($adb->num_rows($result3)) {
    $row3 = $adb->fetchByAssoc($result3, 0);
    $days = $row3['forwardday'];
    $date = date('Y-m-d', (time() + $days * 24 * 60 * 60));
    $sql4 = "select 
                c.contract_no,
                d.accountname,
                c.bussinesstype,
                c.total as contracttotal,
                ifnull(e.productname,c.contract_type) as productname,
                a.stageshow,
                concat(f.last_name,'[',h.departmentname,']') as signname,
                c.signdate,
                ifnull(a.receiveableamount,0) as receiveableamount,
                a.receiverabledate,
                c.signid
                from vtiger_contracts_execution_detail a 
                left join vtiger_contracts_execution b on a.contractexecutionid = b.contractexecutionid
                left join vtiger_servicecontracts c on a.contractid = c.servicecontractsid
                left join vtiger_account d on d.accountid = a.accountid
                LEFT JOIN vtiger_products e ON e.productid = c.productid
                LEFT JOIN vtiger_users f ON f.id = c.signid 
                LEFT JOIN vtiger_user2department g ON g.userid = f.id 
                LEFT JOIN vtiger_departments h ON h.departmentid = g.departmentid 
                where a.contractreceivable>0 and a.receiverabledate=? and a.iscancel=0";
    $result4 = $adb->pquery($sql4, array($date));
    $data = array();
    if ($adb->num_rows($result4)) {
        while ($row4 = $adb->fetchByAssoc($result4)) {
            $data[$row4['signid']][] = $row4;
        }
    }
    if (!empty($data)) {
        $recordModel = Vtiger_Record_Model::getCleanInstance("ContractExecution");
        $alertChannels = explode(',', $row3['alertchannels']);
        foreach ($alertChannels as $alertChannel) {
            switch ($alertChannel) {
                case 'email':
                    $recordModel->sendWarningEmail($data);
                    break;
                case 'assistant':
                    $recordModel->sendWarningWx($data);
                    break;
            }
        }
    }
}


