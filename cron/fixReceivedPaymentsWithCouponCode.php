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

//处理以前的老数据
global $adb;
$contractIds = array(
    2908149,2964912,2993551,3004746,3050274,3055501,3055510,3059704,3080695,3081981,3083331,3103717,3103720,3103721,3103727,3103730,3103735,3106734,3110353,3119646,3129521,3129524,3139471,3139472,3149613,3159334,3162017,3165942,3168064,3168077,3182417,3184815,3187603,3187768,3202655,3205332,3206140,3208546,3217117,3218518,3218897,3218924,3222707,3222723,3222732,3225749,3225750,3225989,3225991,3227229,3227235,3228142,3229169,3230084,3230122,3230124,3231767,3232813,3232817,3233004,3233275,3233280,3233282,3235059,3235945,3236040,3236775,3236777,3238184,3238554,3238640,3238658,3240887,3240955,3241019,3241538,3242219,3242229,3243717,3244055,3244598,3246811
);

$successContractIds=array();
$failContractIds=array();
$recordModel = ServiceContracts_Record_Model::getCleanInstance("ServiceContracts");
foreach ($contractIds as $contractId){
    $returnData = $recordModel->payAfterMatch($contractId,0,false);
    if($returnData['success']){
        $successContractIds[]=$contractId;
    }else{
        $failContractIds[]=array($contractId,'msg'=>$returnData['msg']);
    }
    sleep(2);
}

_logs(array('successContractIds',$successContractIds));
_logs(array('failContractIds',$failContractIds));
echo '执行完毕';
function _logs($data, $file = 'logs_couponcodematch'){
    $year	= date("Y");
    $month	= date("m");
    $dir=trim(__DIR__,'cron');
    $dir.= 'logs/tyun/' . $year . '/' . $month . '/';
    if(!is_dir($dir)) {
        mkdir($dir,0755,true);
    }
    $file = $dir . $file . date('Y-m-d').'.txt';
    @file_put_contents($file, '----------------' . date('H:i:s') . '--------------------'.PHP_EOL.var_export($data,true).PHP_EOL, FILE_APPEND);
}
