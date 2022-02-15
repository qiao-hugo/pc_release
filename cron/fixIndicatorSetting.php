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
global $root_directory,$adb;
$code = "array(";
$result = $adb->pquery("select * from vtiger_indicatorsetting where deleted=0");
while ($row = $adb->fetchByAssoc($result)){
    $key= $row['staff_stage'].$row['departmentid'];
    $code .= "'".$key."'=>array('telnumber'=>'".$row['telnumber']."','telduration'=>'".$row['telduration']."','intended_number'=>'".$row['intended_number']."','invite_number'=>'".
        $row['invite_number']."','visit_number'=>'".$row['visit_number']."','returned_money'=>'".$row['returned_money']."','relationship_or'=>'".$row['relationship_or']."'),";

}

$code2 = "array(";
$result = $adb->pquery("select a.*,b.departmentid,b.staff_stage from vtiger_special_operation a left join vtiger_indicatorsetting b on a.indicatorsettingid=b.id where b.deleted=0");
while ($row = $adb->fetchByAssoc($result)){
    $key= $row['staff_stage'].$row['departmentid'];
    $specialOperations[$key][] = "array('basics_column'=>'".$row['basics_column']."','basics_operator'=>'".$row['basics_operator'].
        "','basics_value'=>'".$row['basics_value']."','operate_column'=>'".$row['operate_column']."','operate_operator'=>'".$row['operate_operator']."','indicatorsettingid'=>'".$row['indicatorsettingid'].
        "','operate_value'=>'".$row['operate_value']."')";
}
foreach ($specialOperations as $key=>$specialOperation){
    $code2.="'".$key."'=>array(".implode(',',$specialOperation)."),";
}

$handle=@fopen($root_directory.'crmcache/indicatorsetting.php',"w+");
if($handle){
    $newbuf ="<?php\n\n";
    $newbuf .= '$indicatorsetting='.rtrim($code,',').");\n";
    $newbuf .= '$specialoperation='.rtrim($code2,',').");\n";
    $newbuf .= "?>";
    fputs($handle, $newbuf);
    fclose($handle);
}



