<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/11/6
 * Time: 15:09
 */

$dir = trim(__DIR__, DIRECTORY_SEPARATOR);
$dir = trim(__DIR__, 'cron');
ini_set("include_path", $dir);
require_once('config.php');
require_once('include/utils/utils.php');
require_once('include/logging.php');
header("Content-type:text/html;charset=utf-8");
error_reporting(0);


include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';


vglobal('default_language', $default_language);
$currentLanguage = 'zh_cn';
vglobal('current_language', $currentLanguage);

require('crmcache/departmentanduserinfo.php');
global $channelDepartmentId,$current_user;
$deparr=$departmentinfo[$channelDepartmentId];
global $adb;



$quDaoAccountIds = array();
//打标签---start
$sql3 = 'SELECT
	a.accountid
FROM
	vtiger_account a
LEFT JOIN vtiger_crmentity b ON a.accountid = b.crmid
LEFT JOIN vtiger_users c ON c.id = b.smownerid
LEFT JOIN vtiger_user2department d ON c.id = d.userid
WHERE
	b.smownerid > 0
    AND d.departmentid IN (\''.implode("','",$deparr).'\')
AND b.deleted = 0';

$result4 = $adb->pquery($sql3,array());
while ($row4 = $adb->fetchByAssoc($result4)){
    $quDaoAccountIds[] = $row4['accountid'];
}

//打标签-----渠道部
if(count($quDaoAccountIds)){
    $adb->query("update vtiger_account set accountlabeling='qudaobu' where accountid in (".implode(",",$quDaoAccountIds).")",array());
}


//打标签-----其他
$adb->pquery("update vtiger_account set accountlabeling='otherbu' where accountlabeling is null",array());
//打标签---end




//释放到公海---start
$beforeFiveDays = date("Y-m-d",strtotime("-5 day"));

//查找所有的没有跟进过的客户
$sql = 'SELECT
	a.accountid
FROM
	vtiger_account a
LEFT JOIN vtiger_crmentity b ON a.accountid = b.crmid
LEFT JOIN vtiger_users c ON c.id = b.smownerid
LEFT JOIN vtiger_user2department d ON c.id = d.userid
WHERE
	b.smownerid > 0
    AND d.departmentid IN (\''.implode("','",$deparr).'\')
AND b.deleted = 0
and a.accountrank in ("chan_notv","forp_notv")
and (a.lastfollowuptime =\'\' or a.lastfollowuptime<"'.$beforeFiveDays.'")'
;
echo "第一条sql ".$sql.'<br>';
$data = array();
$result = $adb->query($sql,array());
while ($row = $adb->fetchByAssoc($result)){
    $data[]= $row['accountid'];
}


$data2 = array();
$sql2 = 'SELECT
	a.accountid
FROM
	vtiger_account a
LEFT JOIN vtiger_crmentity b ON a.accountid = b.crmid
LEFT JOIN vtiger_users c ON c.id = b.smownerid
LEFT JOIN vtiger_user2department d ON c.id = d.userid
WHERE
	b.smownerid > 0
    AND d.departmentid IN (\''.implode("','",$deparr).'\')
AND b.deleted = 0
and a.accountrank in ("chan_notv","forp_notv")
and a.lastfollowuptime>="'.$beforeFiveDays.'"'
;
echo "第二条sql ".$sql2.'<br>';

$result2 = $adb->query($sql2,array());
while ($row2 = $adb->fetchByAssoc($result2)){
    $data2[] = $row2['accountid'];
}

if(count($data2)){
    $sql3 = "select commentcontent,related_to from (select commentcontent,related_to,addtime from vtiger_modcomments where modulename='Accounts' and related_to in(".implode(',',$data2).") order by modcommentsid desc) t GROUP BY t.related_to";
    echo "长度sql  ".$sql3;
    echo '<br>';
    $result3 = $adb->query($sql3,array());
    while ($row3 = $adb->fetchByAssoc($result3)){
        echo '<br>';

        if(mb_strlen($row3['commentcontent'],'utf-8')<10){
            echo $row3['commentcontent'].'   长度'.strlen($row3['commentcontent']);
            echo "<br>";
            $data[] = $row3['related_to'];
        }
    }
}

//释放客户到公海
if(count($data)){
    $str2 = "update vtiger_account set accountcategory=2 where accountid in (".implode(',',$data).")";
    echo 'str2 '.$str2;
    $adb->pquery("update vtiger_account set accountcategory=2 where accountid in (".implode(',',$data).")",array());
}
//释放到公海---end





