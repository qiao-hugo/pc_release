<?php
error_reporting(1);
set_time_limit(0);
/* ini_set("include_path", "../");
require_once('include/utils/utils.php');
require_once('include/logging.php'); */
require_once "../config.inc.php";
if(time()<strtotime('2017-10-09')){
return;
}
//global $dbconfig;
$db_port=ltrim($dbconfig['db_port'],":");

//file_put_contents("/data/httpd/vtigerCRM/cron/account-rank-log",date('Y-m-d H:i:s'));

//$mysql = mysqli_connect('192.168.1.3','crmuser','crmdbpasswd123','vtigercrm600new',3306);
$mysql = mysqli_connect($dbconfig['db_server'],$dbconfig['db_username'],$dbconfig['db_password'],$dbconfig['db_name'],$db_port);
//2016-08-31 关联可能客户
$mysql->query("UPDATE vtiger_receivedpayments,vtiger_account SET vtiger_receivedpayments.maybe_account=vtiger_account.accountid WHERE vtiger_account.accountname=vtiger_receivedpayments.paytitle AND (vtiger_receivedpayments.maybe_account IS NULL OR vtiger_receivedpayments.maybe_account='' OR vtiger_receivedpayments.maybe_account=0) AND (vtiger_receivedpayments.relatetoid='' OR vtiger_receivedpayments.relatetoid=0 OR vtiger_receivedpayments.relatetoid IS NULL)");
//清空扫码登陆临时表
$mysql->query("truncate table vtiger_qrcodelogin");
//按设定来走客户掉公海
$highseasdate=date('Ymd');
$datetimecut=strtotime('-30 day');
$result=$mysql->query("SELECT accountid FROM vtiger_account WHERE visitingorderlastfollowtime<".$datetimecut." AND accountrank='forp_notv'");
$accountids=array();
$datetime=date('Y-m-d H:i:s');
while($row=$result->fetch_assoc()){
    $mysql->query("INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) SELECT id,".$row['accountid'].",'Accounts',6934,'".$datetime."',0 FROM vtiger_modtracker_basic_seq limit 1");
    $mysql->query("INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) SELECT id,'accountrank', 'forp_notv','chan_notv' FROM vtiger_modtracker_basic_seq limit 1");
    $mysql->query("UPDATE vtiger_modtracker_basic_seq SET id=id+1");
}
$mysql->query("UPDATE vtiger_account SET accountrank='chan_notv' WHERE visitingorderlastfollowtime<".$datetimecut." AND accountrank='forp_notv'");
$result=$mysql->query("SELECT 1 FROM `vtiger_workdayhighseas` WHERE datetype='holiday' AND workdayhighseasid={$highseasdate} limit 1");
$numRow=$result->num_rows;
$result->close();
if($numRow>0){
    return ;
}
$result=$mysql->query("SELECT accountid,accountcategory FROM vtiger_account WHERE accountcategory<2 and protected=0 and protectday=0");
$accountids=array();
while($row=$result->fetch_assoc()){
    $accountids[]=$row;
}
date_default_timezone_set("PRC");
$datetime=date('Y-m-d H:i:s');
foreach($accountids as $account){
    $mysql->query("INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) SELECT id,{$account['accountid']},'Accounts',6934,'{$datetime}',0 FROM vtiger_modtracker_basic_seq limit 1");
    $mysql->query("INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) SELECT id,'accountcategory', {$account['accountcategory']}, 2 FROM vtiger_modtracker_basic_seq limit 1");
    $mysql->query("UPDATE vtiger_modtracker_basic_seq SET id=id+1");
}

//追加更新掉入公海时间 gaocl edit 2018/02/28
//$mysql->query('update vtiger_account set accountcategory=2  where accountcategory<2 and protected=0 and protectday=0');
$mysql->query('update vtiger_account set accountcategory=2,intentionality=\'zeropercentage\'  where accountcategory=0 and protected=0 and protectday=0');
$mysql->query('update vtiger_account set accountcategory=2,fall_toovert_time=NOW(),intentionality=\'zeropercentage\'  where accountcategory=1 and protected=0 and protectday=0');

$mysql->query('update vtiger_account set protectday=protectday-1,effectivedays=effectivedays-1 where accountcategory<2 and protected=0 and protectday>0');
$now = strtotime('-30 day');
$mysql->query("update vtiger_account set accountrank='chan_notv' WHERE protected=0 AND accountrank='forp_notv' AND visitingorderlastfollowtime<{$now}");

//新零售跟进天数过期掉公海
$mysql->query("update vtiger_account set followday=followday-1 where isfollow='ryes' and followday>0 ");
$mysql->query("update vtiger_account set accountcategory=2  where isfollow='ryes' and followday=0 and accountcategory<2 ");

//更新商机调公海
$result2=$mysql->query("select allocationaftertracking,longesttracking from vtiger_sendmail_lead_setting limit 1");
while($row=$result2->fetch_assoc()){
    $leadSettings=$row;
}
if($leadSettings){
    $allocationaftertracking=$leadSettings['allocationaftertracking'];
    $longesttracking=$leadSettings['longesttracking'];
    $allocationStart=date('Y-m-d',strtotime("-{$allocationaftertracking} day"));
    $allocationEnd=date('Y-m-d 23:59:59',strtotime("-{$allocationaftertracking} day"));
    $result3 = $mysql->query("select leadid from  vtiger_leaddetails where allocatetime>? and allocatetime<=? and cluefollowstatus='tobecontact'",array($allocationStart,$allocationEnd));
    while($row3=$result3->fetch_assoc()){
        $leadids[]=$row3['leadid'];
        intoModTracker($row3['leadid']);
    }

    $longStart=date('Y-m-d',strtotime("-{$longesttracking} day"));
    $longEnd=date('Y-m-d 23:59:59',strtotime("-{$longesttracking} day"));
    $result4 = $mysql->query("select leadid from vtiger_leaddetails where commenttime>? and commenttime<=? and cluefollowstatus='bependding'",array($allocationStart,$allocationEnd));
    while($row4=$result4->fetch_assoc()){
        $leadids[]=$row4['leadid'];
        intoModTracker($row4['leadid']);
    }

    $mysql->query("Update vtiger_crmentity set smownerid='' where crmid in(".implode(",",$leadids).')');
    $mysql->query('UPDATE `vtiger_leaddetails` SET leadcategroy=2,cluefollowstatus="nostatus" WHERE leadid in('.implode(",",$leadids).')');
}


$mysql->close();



function intoModTracker($recordId){
    $db=PearDatabase::getInstance();
    $datetime=date('Y-m-d H:i:s');
    $id = $db->getUniqueId('vtiger_modtracker_basic');
    $db->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
        array($id , $recordId, 'Leads', 6934, $datetime, 0));

    $db->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
        Array($id, 'leadcategroy',0, 2));
}

exit;
error_reporting(0);

exit;

ini_set("include_path", "../");
require_once('include/utils/utils.php');
require_once('include/logging.php');
$start=date('Y-m-d H:i:s');
global $adb, $log;
//$log =& LoggerManager::getLogger('RecurringInvoice');
//$log->debug("invoked RecurringInvoice");

/*客户等级管理  未保护的不跟进每日减一，为0的进入公海*/
$ids=$adb->pquery('update vtiger_account set protectday=protectday-1 where accountcategory<2 and protected=0 and protectday>0', array());
echo $adb->num_rows($ids);
$adb->pquery('update vtiger_account set accountcategory=2  where accountcategory<2 and protected=0 and protectday=0', array());

$now=strtotime("-30 day");
$adb->pquery("update vtiger_account set accountrank='chan_notv' WHERE protected=0 AND accountrank='forp_notv' AND visitingorderlastfollowtime<{$now}", array());
file_put_contents("/data/httpd/vtigerCRM/cron/account-rank-log",'start='.$start.'--end='.date('Y-m-d H:i:s'));



?>
