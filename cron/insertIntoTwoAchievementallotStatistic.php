<?php
ini_set("include_path", "../");
require_once('config.php');
require_once('include/utils/utils.php');
require_once('include/logging.php');
header("Content-type:text/html;charset=utf-8");
ini_set('display_errors','on'); error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);   // DEBUGGING
include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';
set_time_limit(0);
if(($_REQUEST['all']!=1 || empty($_REQUEST['matchdate'])) && empty($_REQUEST['rpid'])){
    header("HTTP/1.1 404 Not Found");
    header("Status: 404 Not Found");
    exit;
}
global $adb;
$isTrue=true;
$start=0;
$end=1000;
$isall=$_REQUEST['all'];
do{

    if($isall==1){
        $matchdate=$_REQUEST['matchdate'];
        $parent_contracttypeid=empty($_REQUEST['contracttypeid'])?2:$_REQUEST['contracttypeid'];
        $sql="a.matchdate >'$matchdate' AND  s.parent_contracttypeid=".$parent_contracttypeid." and  aas.servicecontractid IS NULL ";
    }else{
        $rpid=$_REQUEST['rpid'];
        $sql="a.receivedpaymentsid in(".$rpid.")";
    }
    $query="SELECT
	a.*, r.unit_price 
FROM
	vtiger_achievementallot AS a
LEFT JOIN vtiger_achievementallot_statistic AS aas ON aas.receivedpaymentsid = a.receivedpaymentsid
INNER JOIN vtiger_servicecontracts AS s ON s.servicecontractsid = a.servicecontractid
INNER JOIN vtiger_receivedpayments AS r ON r.receivedpaymentsid = a.receivedpaymentsid
WHERE 
    ".$sql."
GROUP BY
	a.receivedpaymentsid
ORDER BY
	a.achievementallotid ASC LIMIT $start,$end ";
    $result = $adb->run_query_allrecords($query);
    echo $query;echo "\n";
    if(!empty($result)){
        $Matchreceivements_BasicAjax_Action=new Matchreceivements_BasicAjax_Action();
        foreach($result as $row){
            $Matchreceivements_BasicAjax_Action->commonInsertAchievementallotStatisticjioaben($row['receivedpaymentsid'],$row['unit_price'],0,0,$row['servicecontractid'],0,$row['matchdate']);
        }
        $start=$start+1000;
        $end=$end+1000;
    }else{
        $isTrue=false;
    }
    sleep(1);
}while($isTrue);







