<?php
/*
 * 通用接口
 * @Author gaochunli
 * @Version V1.0.0
 * @Date 2019/6/27
 */
header("Content-type:text/html;charset=utf-8");
//error_reporting(0);
require('include/utils/UserInfoUtil.php');
include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';
include_once  'languages/zh_cn/Accounts.php';
vglobal('default_language', $default_language);
$currentLanguage = 'zh_cn';
vglobal('current_language',$currentLanguage);
function _logs($data, $file = 'logs_'){
    $year	= date("Y");
    $month	= date("m");
    $dir	= './logs/tyun/' . $year . '/' . $month . '/';
    if(!is_dir($dir)) {
        mkdir($dir,0755,true);
    }
    $file = $dir . $file . date('Y-m-d').'.txt';
    @file_put_contents($file, '----------------' . date('H:i:s') . '--------------------'.PHP_EOL.var_export($data,true).PHP_EOL, FILE_APPEND);
}
if(isset($_REQUEST['module'])&& isset($_REQUEST['action'])){
    $module=$_REQUEST['module'];
    $action=$_REQUEST['action'];
    _logs(array($module,$_REQUEST));
    $moduleArray=array('SupplierContracts','TyunWebBuyService','Users','AchievementallotStatistic', 'VisitingOrder','Accounts','ServiceContracts','Vendors','AchievementSummary','ReceivedPayments');
    $return=array('success'=>false,'msg'=>'无效参数');
    do{
        if(false && !in_array($module,$moduleArray)){//测试不验证
            break;
        }
        $recordModel=Vtiger_Record_Model::getCleanInstance($module);
        if(!method_exists($recordModel,$action)){
            break;
        }
        $return=$recordModel->$action(new Vtiger_Request($_REQUEST, $_REQUEST));
    }while(0);
    echo json_encode($return,JSON_UNESCAPED_UNICODE);
}else{
    echo json_encode(array('msg'=>'无效参数'),JSON_UNESCAPED_UNICODE);
}
exit;
?>