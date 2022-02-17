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
$date = date("Y-m-d");
if(Workday_Record_Model::get_daytype($date)=='holiday'){
    echo '今天非工作日';
    return;
}
$sql = "select vtiger_users.email1,vtiger_newinvoice.invoiceno from vtiger_newinvoice 
  left join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_newinvoice.invoiceid  
  left join vtiger_users on vtiger_users.id = vtiger_crmentity.smownerid
where invoicetype='c_billing' and matchover=0";

$result = $adb->pquery($sql,array());
if(!$adb->num_rows($result)){
    return;
}

$Subject = '预开票待匹配提醒！！！';
while ($row = $adb->fetchByAssoc($result)){
    if(!$row['email1']){
        continue;
    }
    $str = '您好!<br>';
    $str .= "    你于".date("Y-m-d H:i:s")."，在  “ERP系统---财务模块---发票（新）”中，还有未匹配回款的数据，请及时处理。谢谢！<br>
            数据详情为：<br>
           1，发票编号：".$row['invoiceno'];
    Vtiger_Record_Model::sendMail($Subject, $str,  array(array('mail' => $row['email1'], 'name' => '')));
}
echo '发送完成';