<?php
/**
 * 注：12月份业绩提成激励数据导出,脚本只使用一次
 * 
 * */
ini_set("include_path", "../");
require_once('config.php');
require_once('include/utils/utils.php');
require_once('include/logging.php');
header("Content-type:text/html;charset=utf-8");
error_reporting(0);
include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';

set_time_limit(0);

global $root_directory,$adb;
$current_language = 'zh_cn';
$listViewModel = Vtiger_ListView_Model::getInstance('AchievementSummary');
$listViewHeaders = getExcelHeaders();

include_once $root_directory.'libraries/PHPExcel/PHPExcel.php';
$phpexecl=new PHPExcel();
$phpexecl->getProperties()->setCreator("liu ganglin")
    ->setLastModifiedBy("liu ganglin")
    ->setTitle("Office 2007 XLSX servicecontracts Document")
    ->setSubject("Office 2007 XLSX servicecontracts Document")
    ->setDescription("Test document for Office 2007 XLSX, generated using classes.")
    ->setKeywords("office 2007 openxml php")
    ->setCategory("AccountPlatform");

$headerCodes = getExcelHeaderCode(count($listViewHeaders)+10);
$headerArray = [];
foreach ($listViewHeaders as $key => $val) {
    if($listViewHeaders[$key]['ishidden']){
        continue;
    }
    $headerArray[$key] = $listViewHeaders[$key];
}

if(empty($headerArray)){
    $headerArray = $listViewHeaders;
}
$step = 0;
$lastStep = 0;
foreach($headerArray as $key => $val){
    if($val['ishidden']){
        continue;
    }
    $headerTitle = vtranslate($key,'AchievementSummary');
    $phpexecl->setActiveSheetIndex(0)->setCellValue($headerCodes[$step].'1',$headerTitle);
    $step++;
    //导出表格在“区域”后增加体系
    if($key == 'invoicecompany'){
        $phpexecl->setActiveSheetIndex(0)->setCellValue($headerCodes[$step].'1','一级部门');
        $phpexecl->setActiveSheetIndex(0)->setCellValue($headerCodes[$step+1].'1','二级部门');
        $phpexecl->setActiveSheetIndex(0)->setCellValue($headerCodes[$step+2].'1','三级部门');
        $phpexecl->setActiveSheetIndex(0)->setCellValue($headerCodes[$step+3].'1','四级部门');
        $phpexecl->setActiveSheetIndex(0)->setCellValue($headerCodes[$step+4].'1','五级部门');
        $step += 5;
        $lastStep += 5;
    }
    $lastStep++;
}

$phpexecl->setActiveSheetIndex(0)->setCellValue($headerCodes[$lastStep].'1','激励提成A');
$phpexecl->setActiveSheetIndex(0)->setCellValue($headerCodes[$lastStep+1].'1','激励提成B');
$phpexecl->setActiveSheetIndex(0)->setCellValue($headerCodes[$lastStep+2].'1','总激励提成');
$phpexecl->setActiveSheetIndex(0)->setCellValue($headerCodes[$lastStep+3].'1','11月份最高提成点');
$phpexecl->setActiveSheetIndex(0)->setCellValue($headerCodes[$lastStep+4].'1','最终总提成');

ini_set('memory_limit','512M');
$limit = 1000;
$i=0;
include 'crmcache/departmentanduserinfo.php';
$current = 2;
$depar = array(
    ['value'=>'','start'=>'','end'=>''],
    ['value'=>'','start'=>'','end'=>''],
    ['value'=>'','start'=>'','end'=>''],
    ['value'=>'','start'=>'','end'=>''],
    ['value'=>'','start'=>'','end'=>''],
);
while (1) {
    $listQuery = selectOctSummarySql($i * $limit, $limit);
    $i++;
    $result = $adb->pquery($listQuery, array());
    $num=$adb->num_rows($result);
    if(0==$num){
        break;
    }
    while ($value = $adb->fetch_array($result)) {
        $departmentid = $value['departmentid_reference'];
        $step = 0;
        foreach ($headerArray as $keyheader => $valueheader) {
            if($valueheader['ishidden']) {
                continue;
            }
            $currnetValue = uitypeformat($valueheader, $value[$valueheader['columnname']], 'AchievementSummary');
            $phpexecl->setActiveSheetIndex(0)->setCellValueExplicit($headerCodes[$step].$current, $currnetValue);
            $step++;
            //导出表格增加体系
            if($keyheader == 'invoicecompany'){
                $parentdepartment = $departmenttoparent[$departmentid];
                $parentDepartmentArr = explode('::', $parentdepartment);
                $parentDepartmentArr = array_values(array_diff($parentDepartmentArr, ['H1']));
                for ($j=0; $j < 5; $j++) {
                    if(!isset($parentDepartmentArr[$j])){
                        $departmentsName = '';
                    }else{
                        $departmentsName = $cachedepartment[$parentDepartmentArr[$j]];
                    }
                    $phpexecl->setActiveSheetIndex(0)->setCellValueExplicit($headerCodes[$step+$j].$current, $departmentsName);

                    if($departmentsName != $depar[$j]['value'] && $depar[$j]['start'] != $depar[$j]['end']){
                        $phpexecl->setActiveSheetIndex(0)->mergeCells($depar[$j]['start'].':'.$depar[$j]['end']); 
                        $phpexecl->setActiveSheetIndex(0)->getStyle($depar[$j]['start'])->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    }

                    if($departmentsName == ''){
                        $depar[$j]['value'] = '';
                        $depar[$j]['end'] = $headerCodes[$step+$j].$current;
                        $depar[$j]['start'] = $headerCodes[$step+$j].$current;
                    }else{
                        if($departmentsName == $depar[$j]['value']){
                            $depar[$j]['end'] = $headerCodes[$step+$j].$current;
                        }else{
                            $depar[$j]['value'] = $departmentsName;
                            $depar[$j]['start'] = $headerCodes[$step+$j].$current;
                            $depar[$j]['end'] = $headerCodes[$step+$j].$current;
                        }
                    }
                }
                $step += 5;
            }
        }
        $value['excitation_a'] = empty($value['excitation_a']) ? 0 : $value['excitation_a'];
        $value['excitation_b'] = empty($value['excitation_b']) ? 0 : $value['excitation_b'];
        $value['totalroyalty'] = empty($value['totalroyalty']) ? 0 : $value['totalroyalty'];
        $value['lastheightratio'] = empty($value['lastheightratio']) ? 0 : $value['lastheightratio'];
        $phpexecl->setActiveSheetIndex(0)->setCellValueExplicit($headerCodes[$lastStep].$current, $value['excitation_a']);
        $phpexecl->setActiveSheetIndex(0)->setCellValueExplicit($headerCodes[$lastStep+1].$current, $value['excitation_b']);
        $phpexecl->setActiveSheetIndex(0)->setCellValueExplicit($headerCodes[$lastStep+2].$current, $value['totalroyalty']);
        $phpexecl->setActiveSheetIndex(0)->setCellValueExplicit($headerCodes[$lastStep+3].$current, $value['lastheightratio']);
        $phpexecl->setActiveSheetIndex(0)->setCellValueExplicit($headerCodes[$lastStep+4].$current, $value['totalroyalty']+$value['actualroyalty']);

        $current++;
    }
    if($num!=$limit){break;}
}
//合并最后的相同部门
foreach($depar as $val){
    if($val['start'] != $val['end']){
        $phpexecl->setActiveSheetIndex(0)->mergeCells($val['start'].':'.$val['end']); 
        $phpexecl->setActiveSheetIndex(0)->getStyle($val['start'])->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    }
}

// 设置工作表的名称
$phpexecl->getActiveSheet()->setTitle('销售业绩激励提成汇总表');
$phpexecl->setActiveSheetIndex(0);

header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream");
header("Content-Type: application/download");
header('Content-Disposition: attachment;filename="销售业绩激励提成汇总表.xlsx"');
header("Content-Transfer-Encoding: binary ");
header ('Expires: Mon, 14 Jul 2015 08:18:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified


$objWriter = PHPExcel_IOFactory::createWriter($phpexecl, 'Excel2007');
$objWriter->save('php://output');


//查询12月份业绩汇总数据
function selectOctSummarySql($start, $limit = 1000){
    //获取“中小体系”部门
    $departmentids = getChildDepartment('H3');
    $departmentids[] = 'H3';

    $sql = "SELECT vtiger_achievementsummary.achievementmonth,vtiger_achievementsummary.invoicecompany, (vtiger_departments.departmentname) as departmentid,vtiger_achievementsummary.departmentid as departmentid_reference,(select last_name from vtiger_users where vtiger_achievementsummary.userid=vtiger_users.id) as userid,vtiger_achievementsummary.employeelevel,vtiger_achievementsummary.achievementtype,vtiger_achievementsummary.unit_price,vtiger_achievementsummary.effectiverefund,vtiger_achievementsummary.mrenewarriveachievement,vtiger_achievementsupdate.uroyalty,vtiger_achievementsupdate.uroyaltyremark,vtiger_achievementsummary.realarriveachievement,vtiger_achievementsummary.royalty,vtiger_achievementsummary.annualdiscount,vtiger_achievementsummary.deliverdetain,vtiger_achievementsummary.annualpayment,vtiger_achievementsummary.grantdetain,vtiger_achievementsummary.actualroyalty,vtiger_achievementsummary.userfullname,vtiger_achievementsummary.quarterlyaward,vtiger_achievementsummary.withholdroyaltyratio,vtiger_achievementsummary.halfyearlyaward,vtiger_achievementsummary.confirmstatus,IF(vtiger_achievementsummary.proportionofyears=1,'是','否') as proportionofyears,vtiger_achievementsummary.performancetype,IF(vtiger_achievementsummary.achievementtype='newadd',vtiger_achievementexcitation.excitation_a,0) as excitation_a,IF(vtiger_achievementsummary.achievementtype='newadd',vtiger_achievementexcitation.excitation_b,0) as excitation_b,IF(vtiger_achievementsummary.achievementtype='newadd',vtiger_achievementexcitation.totalroyalty,0) as totalroyalty,IF(vtiger_achievementsummary.achievementtype='newadd',vtiger_achievementexcitation.lastheightratio,0) as lastheightratio,vtiger_achievementsummary.achievementid FROM vtiger_achievementsummary LEFT JOIN vtiger_achievementexcitation ON vtiger_achievementexcitation.userid=vtiger_achievementsummary.userid LEFT JOIN vtiger_achievementsupdate ON (vtiger_achievementsupdate.uuserid=vtiger_achievementsummary.userid AND vtiger_achievementsupdate.uachievementmonth=vtiger_achievementsummary.achievementmonth AND vtiger_achievementsupdate.uachievementtype=vtiger_achievementsummary.achievementtype AND vtiger_achievementsupdate.uperformancetype=vtiger_achievementsummary.performancetype AND vtiger_achievementsupdate.deleted=0)  LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_achievementsummary.departmentid  WHERE 1=1 AND vtiger_achievementsummary.departmentid in ('".implode("','",$departmentids)."') and vtiger_achievementsummary.achievementmonth = '2021-12'  ORDER BY vtiger_departments.parentdepartment DESC,vtiger_achievementsummary.achievementmonth DESC,vtiger_achievementsummary.createtime DESC limit {$start}, {$limit}";
    return $sql;
}


function  getExcelHeaders(){
    global $adb; 
    $temp = [];
    $sql="SELECT vtiger_field.* FROM vtiger_field join vtiger_tab on vtiger_field.tabid=vtiger_tab.tabid join vtiger_blocks on vtiger_blocks.blockid = vtiger_field.block and vtiger_blocks.tabid=vtiger_tab.tabid WHERE vtiger_tab.name ='AchievementSummary' and vtiger_blocks.visible=0 AND vtiger_field.isshowfield=0 AND vtiger_field.displaytype != 4 AND vtiger_field.displaytype != 0 ORDER BY vtiger_blocks.sequence,vtiger_field.listpresence";

    $result = $adb->run_query_allrecords($sql);
    foreach ($result as  $val) {
        $temp[$val['fieldlabel']]=$val;
    }
    return $temp;
}


exit;