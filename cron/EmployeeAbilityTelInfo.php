<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/11/6
 * Time: 15:09
 */

$dir=trim(__DIR__,DIRECTORY_SEPARATOR);
$dir=trim(__DIR__,'cron');
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
$sql = "select userid,content,employeeabilityid from vtiger_employee_ability_detail where stafflevel='junior' and status = 0";
$result = $adb->pquery($sql,array());
$BeginDate=date('Y-m-01', strtotime(date("Y-m-d")));
$EndDate=date('Y-m-d', strtotime("$BeginDate +1 month -1 day"));
while ($row = $adb->fetchByAssoc($result)){
    $content = json_decode(str_replace("&quot;", '"', $row['content']), true);
    $sql = "select sum(telnumber) as totaltelnumber,sum(telduration) as totaltelduration from vtiger_telstatistics where useid=? and telnumberdate>=? and telnumberdate<=?";
    $result2 = $adb->pquery($sql,array($row['userid'],$BeginDate,$EndDate));
    $row2 = $adb->fetchByAssoc($result2,0);
    if($content['telnumber']['status']!='completed'){
        $content['telnumber']['wordsub'] = $row2['totaltelnumber']?$row2['totaltelnumber']:0;
        if($row2['totaltelnumber']>=6000){
            $content['telnumber']['status']='completed';
        }
    }

    if($content['telduration']['status']!='completed'){
        $content['telduration']['wordsub'] = $row2['totaltelduration']?$row2['totaltelduration']:0;
        if($row2['totaltelduration']>=1500){
            $content['telduration']['status']='completed';
        }
    }

    $updateSql = "update vtiger_employee_ability_detail set content = ? where userid=? and stafflevel='junior'";
    $updateListSql = "update vtiger_employee_ability set telnumber = ?,telduration=? where employeeabilityid=?";
    $recordModel = EmployeeAbility_Record_Model::getCleanInstance("EmployeeAbility");
    if($recordModel->isFinishCurrentLevelTask($content)){
        $updateSql = "update vtiger_employee_ability_detail set content=?,status=1 where  userid=? and stafflevel='junior'";
        $updateListSql = "update vtiger_employee_ability set telnumber =?,telduration=?,stafflevel='".$recordModel->upgradeLevel('junior',$row['employeeabilityid'])."' where employeeabilityid=?";
    }
    $adb->pquery($updateSql,array(json_encode($content),$row['userid']));
    $adb->pquery($updateListSql,array($content['telnumber']['wordsub'],$content['telduration']['wordsub'],$row['employeeabilityid']));
}
