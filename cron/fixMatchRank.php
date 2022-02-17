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
//error_reporting(0);


include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';


vglobal('default_language', $default_language);
$currentLanguage = 'zh_cn';
vglobal('current_language', $currentLanguage);


global $adb;
$adb->pquery("truncate vtiger_receivedpaymentstopten",array());
$adb->pquery("truncate vtiger_receivedpaymentsranking",array());
$sql = "select distinct receivedpaymentsid from vtiger_achievementallot_statistic where achievementmonth=?  and is_deduction=0   and achievementtype='newadd'";
$result = $adb->pquery($sql, array('2021-05'));
if (!$adb->num_rows($result)) {
    return;
}

$recordModel = Matchreceivements_Record_Model::getCleanInstance("Matchreceivements");
while ($row = $adb->fetchByAssoc($result)) {
    $recordModel->matchToRanking($row['receivedpaymentsid'], true);
}



