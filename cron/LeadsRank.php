<?php
error_reporting(1);
set_time_limit(0);
ini_set("include_path", "../");
require_once('include/utils/utils.php');
require_once('include/logging.php');

if(time()<strtotime('2017-10-09')){
return;
}
global $dbconfig;
$db_port=ltrim($dbconfig['db_port'],":");

$mysql = mysqli_connect($dbconfig['db_server'],$dbconfig['db_username'],$dbconfig['db_password'],$dbconfig['db_name'],$db_port);
//按设定来走客户掉公海
$highseasdate=date('Ymd');
$result=$mysql->query("SELECT 1 FROM `vtiger_workdayhighseas` WHERE datetype='holiday' AND workdayhighseasid={$highseasdate} limit 1");
$numRow=$result->num_rows;
$result->close();
if($numRow>0){
    return ;
}
echo '开始';
date_default_timezone_set("PRC");
$datetime=date('Y-m-d H:i:s');

//更新商机调公海
$result2=$mysql->query("select allocationaftertracking,longesttracking from vtiger_sendmail_lead_setting limit 1");
while($row=$result2->fetch_assoc()){
    $leadSettings=$row;
}
var_dump($leadSettings);
if($leadSettings){
    $allocationaftertracking=$leadSettings['allocationaftertracking'];
    $longesttracking=$leadSettings['longesttracking'];
    $allocationStart=date('Y-m-d',strtotime("-{$allocationaftertracking} day"));
    $allocationEnd=date('Y-m-d 23:59:59',strtotime("-{$allocationaftertracking} day"));
    echo $allocationaftertracking.'<br>';
    echo $longesttracking.'<br>';
    echo $allocationStart.'<br>';
    echo $allocationEnd.'<br>';
    $result3 = $mysql->query("select leadid from  vtiger_leaddetails where allocatetime>'".$allocationStart."' and allocatetime<='".$allocationEnd."' and cluefollowstatus='tobecontact'");
    while($row3=$result3->fetch_assoc()){
        $leadids[]=$row3['leadid'];
        intoModTracker($row3['leadid']);
    }

    $longStart=date('Y-m-d',strtotime("-{$longesttracking} day"));
    $longEnd=date('Y-m-d 23:59:59',strtotime("-{$longesttracking} day"));
    $result4 = $mysql->query("select leadid from vtiger_leaddetails where commenttime>'".$longStart."' and commenttime<='".$longEnd."' and cluefollowstatus='bependding'");
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
?>
