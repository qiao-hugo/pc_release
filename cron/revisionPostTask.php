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

$query="SELECT * FROM vtiger_transfer_history  WHERE status = 0 ORDER BY id";
$result = $adb->run_query_allrecords($query);
foreach($result as $row){
    $cur_time = time();
    $effectivetime = strtotime($row['effectivetime']);
    if($cur_time>=$effectivetime){
        $recorderModel2 = Vtiger_Record_Model::getInstanceById($row['userid'],'Users');
        $recorderModel2->set('id',$row['userid']);
        $fieldsarray = array(
            'reports_to_id',
            'invoicecompany',
            'departmentid',
            'roleid',
            'department',
            'title',
            'employeelevel',
            'companyid'
        );
        foreach ($fieldsarray as $fielddata){
            if($row[$fielddata]){
                $recorderModel2->set($fielddata,$row[$fielddata]);
            }
        }
        $recorderModel2->set('transfertype',$row['type']);
        if($row['type']=='barrack'){
            $recorderModel2->set('barracksintime',date('Y-m-d',$effectivetime));
        }else{
            $recorderModel2->set('postintime',date('Y-m-d',$effectivetime));
        }
        $recorderModel2->set('mode','edit');
        $recorderModel2->save();

        $usermanagersql = "update vtiger_usermanger set `modulestatus`= ? where usermangerid = ?";
        $adb->pquery($usermanagersql,array('c_complete',$row['usermangerid']));

        $sql = 'update vtiger_transfer_history set status = 1 where id = ?';
        $adb->pquery($sql,array($row['id']));
    }
}

