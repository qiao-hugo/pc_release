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
$end=1000;
$strRceivedpaymentsids=$_REQUEST['receivedPayments'];
$flag=$_REQUEST['flag'];
do{
    if($flag=='tyun'){//T云
        $query="SELECT
	a.*, r.unit_price
FROM
	vtiger_achievementallot AS a
LEFT JOIN vtiger_achievementallot_statistic AS aas ON aas.receivedpaymentsid = a.receivedpaymentsid
INNER JOIN vtiger_servicecontracts AS s ON s.servicecontractsid = a.servicecontractid
INNER JOIN vtiger_receivedpayments AS r ON r.receivedpaymentsid = a.receivedpaymentsid
WHERE 
 a.receivedpaymentsid IN(".$strRceivedpaymentsids.")
GROUP BY
	a.receivedpaymentsid
ORDER BY
	a.achievementallotid ASC LIMIT $start,$end ";
        $result = $adb->run_query_allrecords($query);
        echo $end;echo "\n";
        if(!empty($result)){
            foreach($result as $row){
                Matchreceivements_BasicAjax_Action::commonInsertAchievementallotStatistics($row['receivedpaymentsid'],$row['unit_price'],1,$row['receivedpaymentownid'],$row['servicecontractid'],0,$row['matchdate']);
            }
            $start=$start+1000;
            $end=$end+1000;
        }else{
            $isTrue=false;
        }
        sleep(1);
    }
    if($flag=='tsite'){//Tsite
        $isTrue=false;
    }
    if($flag=='refill'){//充值申请单
        $recordid=$strRceivedpaymentsids;
        $productQuery='SELECT * FROM vtiger_rechargesheet WHERE refillapplicationid=? AND productid in(2137321,2138055)';
        $productResult=$adb->pquery($productQuery,array($recordid));
        if($adb->num_rows($productResult)){
            $matchreceivements_BasicAjax_Action= new Matchreceivements_BasicAjax_Action();
            $params['refillapplicationid']=$recordid;
            $receivedpayments=$adb->pquery("SELECT  *  FROM vtiger_refillapprayment WHERE refillapplicationid=?  LIMIT 1 ",array($recordid) );
            if($adb->num_rows($receivedpayments)) {
                $receivedpayments = $adb->query_result_rowdata($receivedpayments, 0);
                $rp=$matchreceivements_BasicAjax_Action->getReceivedpaymentsInfo($receivedpayments['receivedpaymentsid']);
                $remark='';
                $matchdate=$receivedpayments['completedatetime'];
                $shareuser=0;
                $currentid=1;
                $total=$rp['unit_price'];
                $matchreceivements_BasicAjax_Action->rechargeCalculation($rp,$rp['servicecontractsid'],$receivedpayments['receivedpaymentsid'],$shareuser,$total,$currentid,$remark,$adb,$params,$matchdate);
                echo 232232323;
            }
        }
        $isTrue=false;
    }
    if(!in_array($flag,array('refill','tsite','tyun'))){
        $isTrue=false;
    }
}while($isTrue);







