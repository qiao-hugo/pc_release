<?php
ini_set("include_path", "../");
require_once('config.php');
require_once('include/utils/utils.php');
require_once('include/logging.php');
header("Content-type:text/html;charset=utf-8");
//error_reporting(0);
include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';
set_time_limit(0);
global $adb;
$isTrue=true;
$start=0;
$end=100;
do{
    // 四月份回复数据  分成比列等于100
    $query="SELECT
	a.*, r.unit_price
FROM
	vtiger_achievementallot AS a
INNER JOIN vtiger_receivedpayments AS r ON r.receivedpaymentsid = a.receivedpaymentsid
WHERE 
a.scalling = 100 
AND  r.receivedpaymentsid IN(64614,65742,65817,65577,63965,65358,64657,65804) 
ORDER BY
	a.achievementallotid ASC LIMIT $start,$end ";
    $result = $adb->run_query_allrecords($query);
    echo $start;echo "\n";
    if(!empty($result)){
        foreach($result as $row){
            Matchreceivements_BasicAjax_Action::commonInsertAchievementallotStatisticsAboutBakMarketing($row['receivedpaymentsid'],$row['unit_price'],1,$row['receivedpaymentownid'],$row['servicecontractid'],0,$row['matchdate']);
        }
        $start=$start+100;
    }else{
        $isTrue=false;
    }
    sleep(1);
}while($isTrue);
