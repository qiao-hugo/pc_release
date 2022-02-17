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
vglobal('current_language',$currentLanguage);
global $adb;
$achievementmonth = date('Y-m',time());
$query="SELECT
    a.owncompanys AS invoicecompany,
	a.receivedpaymentownid AS userid,
	sum( a.unit_price ) AS unit_price,
	sum( a.arriveachievement ) AS arriveachievement,
	sum( a.effectiverefund ) AS effectiverefund,
	a.achievementmonth,
	b.departmentid AS departmentid,
	a.achievementtype AS achievementtype 
FROM
	vtiger_achievementallot_statistic a
	LEFT JOIN vtiger_usermanger b ON a.receivedpaymentownid = b.userid 
WHERE
	a.isover = 0 
	AND a.producttype != 10
	AND a.achievementmonth='".$achievementmonth."'
GROUP BY
	a.receivedpaymentownid,
	a.achievementmonth,
	a.achievementtype";
$result = $adb->run_query_allrecords($query);
foreach($result as $row){
    $recorderModel2 = Vtiger_Record_Model::getCleanInstance('AchievementSummary');
    $fieldsarray = array(
        'userid',
        'unit_price',
        'arriveachievement',
        'effectiverefund',
        'achievementmonth',
        'departmentid',
        'invoicecompany',
        'achievementtype'
    );
    foreach ($fieldsarray as $fielddata){
        if($row[$fielddata]){
            $recorderModel2->set($fielddata,$row[$fielddata]);
        }
    }
    $recorderModel2->set('confirmstatus','tobeconfirm');
    $recorderModel2->set('createtime',date('Y-m-d H:i:s'));
    $recorderModel2->save();
}

