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
global $adb;

$sql = " select c.accountid,c.accountname,b.subject,b.alertcontent,d.department,d.email1,d.last_name,b.jobalertsid,b.alerttime from 
   vtiger_jobalertsreminder a 
   left join vtiger_jobalerts b on a.jobalertsid=b.jobalertsid 
   left join vtiger_account c on b.accountid=c.accountid
   left join vtiger_users d on a.alertid = d.id
   where a.wxstatus = 0";

$res = $adb->pquery($sql);
$datas = array();
while ($row = $adb->fetch_array($res)) {
    $alerttime = strtotime($row['alerttime']);
//    if ($alerttime < time() - 30 * 60 || $alerttime > time() + 30 * 60) {
//        continue;
//    }
    $email = $row['email1'] . '|';
    $accountname = $row['accountname'];
    $last_name = $row['last_name'];
    $department = $row['department'];
    $subject = $row['subject'];
    $alertcontent = $row['alertcontent'];
    $content = '<div class=\"gray\">' . date('Y-m-d H:i') . '</div><div class=\"normal\">' . '客户名称:' . $accountname . '<br>提醒人:' . $last_name . '[' . $department . ']<br>主题:' . $subject . '<br>提醒内容:' . $alertcontent . '</div><div class=\"highlight\"></div>请及时处理';
    $datas[$row['jobalertsid']] = array(
        'email' => $datas[$row['jobalertsid']] ? $datas[$row['jobalertsid']]['email'] . $email : $email,
        'description' => $content,
        'title' => '跟进提醒：您有客户需要跟进',
        'record'=>$row['accountid']
    );
}


if (count($datas)) {
    foreach ($datas as $key => $data) {
        $recordModel = Vtiger_Record_Model::getCleanInstance('JobAlerts');
        $recordModel->sendWechatMessage(array(
            'email' => trim(trim($data['email'], '|')),
            'description' => $data['description'],
            'title' => $data['title'],
            'dataurl' => 'http://mtest.crm.71360.com/index.php?module=Accounts&action=userDetail&record='.$data['record'],
            'flag' => 7
        ));
        $jobalertsid[] = $key;
    }

    $sql = "update vtiger_jobalertsreminder set wxstatus=1 where jobalertsid in(" . implode(',', $jobalertsid) . ")";
    $adb->pquery($sql, array());
}


