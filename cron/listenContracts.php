<?php
ini_set("include_path", "../");

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


//global $adb;
//$date = date("Y-m-d", strtotime("+30 day"));
//$currentDate = date("Y-m-d");
//$recordModel = Vtiger_Record_Model::getCleanInstance("ServiceContracts");
//
//
////提前一个月发送订单即将消耗完毕提醒
//$sql = "select servicecontractsid from vtiger_listenorder where enddate=? and deleted=0";
//$result = $adb->pquery($sql, array($date));
//$num = $adb->num_rows($result);
//echo '30天后到期的数量:'.$num.'<br>';
//if ($num) {
//    while ($row = $adb->fetchByAssoc($result)) {
//        $contractIds[] = $row['servicecontractsid'];
//        echo '30天后到期的合同id:'.$row['servicecontractsid'].'<br>';
//    }
//    $recordModel->batchSendWarnSaleWx($contractIds);
//    echo '批量发送30天后到期的消息结束';
//}


$dateArray = array(
    date("Y-m-d", strtotime("+30 day")),
    date("Y-m-d", strtotime("+25 day")),
    date("Y-m-d", strtotime("+20 day")),
    date("Y-m-d", strtotime("+15 day")),
    date("Y-m-d", strtotime("+10 day")),
    date("Y-m-d", strtotime("+5 day")),
    date("Y-m-d", strtotime("+4 day")),
    date("Y-m-d", strtotime("+3 day")),
    date("Y-m-d", strtotime("+2 day")),
    date("Y-m-d", strtotime("+1 day")),
);

global $adb;
$recordModel = Vtiger_Record_Model::getCleanInstance("ServiceContracts");

//提前一个月发送订单即将消耗完毕提醒
$sql = "select servicecontractsid from vtiger_listenorder where enddate in ('".implode("','",$dateArray)."') and deleted=0";
$result = $adb->pquery($sql, array());
$num = $adb->num_rows($result);
echo '到期的数量:'.$num.'<br>';
if ($num) {
    while ($row = $adb->fetchByAssoc($result)) {
        $contractIds[] = $row['servicecontractsid'];
        echo '到期的合同id:'.$row['servicecontractsid'].'<br>';
    }
    $recordModel->batchSendWarnSaleWx($contractIds);
    echo '批量发送到期的消息结束';
}

/**
 * 记录发票日志
 * @param $data
 * @param string $file
 */
function _logs($data, $file = 'listen_logs_'){
    global $root_directory;
    $year	= date("Y");
    $month	= date("m");
    $day	= date("d");
    $dir	= $root_directory.'logs/listenContracts/' . $year . '/' . $month . '/';
    if(!is_dir($dir)) {
        mkdir($dir,0755,true);
    }
    $file = $dir . $file . date('Y-m-d').'.txt';
    @file_put_contents($file, '----' . date('H:i:s') .PHP_EOL.var_export($data,true).PHP_EOL, FILE_APPEND);
}

//消耗完毕当天通知财务已消耗完成的合同
//$sql2 = "select a.servicecontractsid,b.contract_no from vtiger_listenorder a left join vtiger_servicecontracts b on a.servicecontractsid=b.servicecontractsid where enddate=? and deleted=0";
//$result2 = $adb->pquery($sql2, array($currentDate));
//$num2 = $adb->num_rows($result2);
//echo '当天消耗完的合同数量:'.$num2.'<br>';
//if ($num2) {
//    $servicecontractsids = array();
//    while ($row =$adb->fetchByAssoc($result2)){
////        $servicecontractsids[] = $row['servicecontractsid'];
//        echo '当天消耗完的合同id:'. $row['contract_no'].'<br>';
//        $contractNos[] = $row['contract_no'];
//
//    }
////    $recordModel->batchSendWarnFinanceWx($servicecontractsids);
//    $resultData = $recordModel->batchStopOrder($contractNos);
//    echo '批量发送今天到期的消息结束';
//    if(!empty($resultData)) {
//        _logs(array($resultData));
//    }
//}

//function _logs($data, $file = 'logs_listencontract'){
//    $year	= date("Y");
//    $month	= date("m");
//    $dir=trim(__DIR__,'cron');
//    $dir.= 'logs/tyun/' . $year . '/' . $month . '/';
//    if(!is_dir($dir)) {
//        mkdir($dir,0755,true);
//    }
//    $file = $dir . $file . date('Y-m-d').'.txt';
//    @file_put_contents($file, '----------------' . date('H:i:s') . '--------------------'.PHP_EOL.var_export($data,true).PHP_EOL, FILE_APPEND);
//}

