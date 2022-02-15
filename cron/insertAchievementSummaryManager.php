<?php
ini_set("include_path", "../");
require_once('config.php');
require_once('include/utils/utils.php');
require_once('include/logging.php');
header("Content-type:text/html;charset=utf-8");
ini_set('display_errors','on'); error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);   // DEBUGGING
include_once 'vtlib/Vtiger/Module.php';
error_reporting(0);
include_once 'includes/main/WebUI.php';
set_time_limit(0);
global $adb;
$isTrue=true;
$start=0;
$end=1000;
$achievementMonth='2020-09';
// 四级部门业绩占比
$query=" SELECT SUM(IF(more_years_renew=1,1,0)) as allfourgrademoreyears,count(1) as allnumber,GROUP_CONCAT(groupname) as groupnamestr,groupname FROM (SELECT * FROM vtiger_achievementallot_statistic WHERE  achievementmonth='".$achievementMonth."' GROUP BY  servicecontractid,receivedpaymentownid) as a GROUP BY groupname ";
$result = $adb->run_query_allrecords($query);
$params=[];
$str='';
if(!empty($result)){
    foreach($result as $row){
        if($row['allfourgrademoreyears']/$row['allnumber']>=30){
            $percentthirty=1;
        }else{
            $percentthirty=0;
        }
        $params[]=$achievementMonth;
        $params[]=$row['groupname'];
        $params[]='fourgrade';// percenttype
        $params[]=$percentthirty;
        $params[]=date("Y-m-d H:i:s");
        $str.="(?,?,?,?,?),";
    }
}

// 五级部门业绩占比
$query=" SELECT SUM(IF(more_years_renew=1,1,0)) as allfivegrademoreyears,count(1) as allnumber,departmentname FROM (SELECT * FROM vtiger_achievementallot_statistic WHERE  achievementmonth='".$achievementMonth."' GROUP BY  servicecontractid,receivedpaymentownid) as a GROUP BY departmentname ";
$result = $adb->run_query_allrecords($query);
if(!empty($result)){
    foreach($result as $row){
        if($row['allfivegrademoreyears']/$row['allnumber']>=30){
            $percentthirty=1;
        }else{
            $percentthirty=0;
        }
        $params[]=$achievementMonth;
        $params[]=$row['departmentname'];
        $params[]='fivegrade';// percenttype
        $params[]=$percentthirty;
        $params[]=date("Y-m-d H:i:s");
        $str.="(?,?,?,?,?),";
    }
}


$str=trim($str,",");
$sql="INSERT INTO vtiger_achievementsummary_managerpercent (achievementmonth,departmentname,percenttype,percentthirty,createtime) VALUES ".$str;
$adb->pquery($sql,array($params));
exit();








