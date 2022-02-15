<?php
define("AUTO_TOKEN",md5(date('Y-m-d H:i:s')));
$dir=trim(__DIR__,DIRECTORY_SEPARATOR);
$dir=trim(__DIR__,'cron');
ini_set("include_path", $dir);
require_once('config.php');
require_once('include/utils/utils.php');
require_once('include/logging.php');
header("Content-type:text/html;charset=utf-8");
//error_reporting(-1);
//ini_set("display_errors",1);
include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';
vglobal('default_language', $default_language);
$currentLanguage = 'zh_cn';
vglobal('current_language', $currentLanguage);


findUnMatchRecedpayment();

/**
 * 获取未匹配的回款
 */
function findUnMatchRecedpayment(){
    global $adb;
    $sql="select * from vtiger_receivedpayments where (relatetoid=0 or relatetoid is null) and createtime >'2021-11-01 18:30:00' and receivedstatus='normal' AND deleted=0 ";
    $unMatchPayment=$adb->run_query_allrecords($sql);
    if($unMatchPayment){
        $recordModel=new ReceivedPayments_Record_Model();
        foreach ($unMatchPayment as $receivedPayment){
            //判断回款入账时间是否当月
            $nowMonth=date('Y-m',strtotime('-1 day'));
            $old_reality_date=$recordModel->getCompareTime($receivedPayment['receivedpaymentsid'],$receivedPayment['reality_date']);
            if($old_reality_date){
                $realityDateArray=explode('-',$old_reality_date);
                $monthRealityDate=$realityDateArray[0].'-'.$realityDateArray[1];
                //当月不一样
                if($nowMonth!=$monthRealityDate){
                    $sql="update vtiger_receivedpayments set iscrossmonthmatch=1 where receivedpaymentsid=?";
                    $adb->pquery($sql,array($receivedPayment['receivedpaymentsid']));
                }else{
                    //判断是否超时匹配
                    $recordModel->matchingWithTimeOut($receivedPayment['receivedpaymentsid'],0,0);
                }
            }
        }
        //开始匹配
        exit();
    }else{
        echo '没未匹配的回款';
        exit();
    }
}

