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
$sql = "select a.receivedpaymentsid,c.accountname from vtiger_receivedpayments a 
       left join vtiger_servicecontracts b on a.relatetoid=b.servicecontractsid
       left join vtiger_account c on b.sc_related_to = c.accountid
        where  a.modulename='ServiceContracts' and a.ismatchdepart=1 and (a.accountname='' or a.accountname is NULL) and relatetoid!=0 and relatetoid is not null";

$res = $adb->query($sql,array());
if(!$adb->num_rows($res)){
    echo '无数据';
    return;
}

while ($row = $adb->fetchByAssoc($res)){
    $sql2 = "update vtiger_receivedpayments set accountname = ? where receivedpaymentsid=?";
    $adb->pquery($sql2,array($row['accountname'],$row['receivedpaymentsid']));
}
echo '数据处理完毕';