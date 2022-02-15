<?php
ini_set("memory_limit","2048M");
set_time_limit(0);
ini_set("include_path", "../");
require_once('config.php');
require_once('include/utils/utils.php');
require_once('include/logging.php');
header("Content-type:text/html;charset=utf-8");
//error_reporting(-1);
//ini_set("display_errors",1);
include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';

updateTable();

function updateTable(){
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://in-kq.71360.com/application/listMonthReportStatisticForJixiao?searchMonth='.date('Y-m',strtotime('-1 month')),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    $resJsonArray=json_decode($response,true);
    $retrunArray=array();
    foreach ($resJsonArray['result'] as $result){
        $retrunArray[]=array('userId'=>$result['userId'],'shouldWorkday'=>$result['shouldWorkday']);
    }
    global $adb;
    $adb->connect();
    foreach ($retrunArray as $retrun){
        $sql="update vtiger_useractivemonthnew set shouldworkday=? where subordinateid=? and activedate=?";
        $adb->pquery($sql,array($retrun['shouldWorkday'],$retrun['userId'],date('Y-m',strtotime('-1 month'))));
    }
    exit();
}


