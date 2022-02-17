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
    // 查询 业绩月份错误的已生成明细所有数据。
    $query="SELECT  achievementallotid FROM vtiger_achievementallot_statistic WHERE  achievementmonth='2020-05'  AND reality_date <'2020-05-01' AND  matchdate IN('2020-05-06','2020-05-07') LIMIT $start,$end";
    $result = $adb->run_query_allrecords($query);
    echo $start;
    if(!empty($result)){
        $deleteArray=[];
        foreach($result as $row){
            $deleteArray[]=$row['achievementallotid'];
        }
        //如果本来有有效数据 则删除汇总表数据
        if(!empty($deleteArray)){
            AchievementSummary_Record_Model::delAchievementSummary($deleteArray);
        }
        $deleteArrayStr=implode(",",$deleteArray);
        $adb->pquery("DELETE FROM vtiger_achievementallot_statistic WHERE achievementallotid IN(".$deleteArrayStr.") ");
        $start=$start+100;
    }else{
        $isTrue=false;
    }
    sleep(1);
}while($isTrue);







