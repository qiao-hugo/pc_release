<?php
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

//两天内不作废恢复状态
$sql="SELECT
	vtiger_servicecontracts.servicecontractsid,
	vtiger_servicecontracts.backstatus,
	vtiger_servicecontracts.modulestatus,
	vtiger_salesorderworkflowstages.workflowsid
FROM
	vtiger_servicecontracts
	LEFT JOIN vtiger_salesorderworkflowstages ON vtiger_servicecontracts.servicecontractsid = vtiger_salesorderworkflowstages.salesorderid 
WHERE
	vtiger_salesorderworkflowstages.workflowstagesflag = 'DO_RETURN_TCLOUD' 
	AND vtiger_salesorderworkflowstages.isaction = '1' 
	AND vtiger_servicecontracts.modulestatus in ( 'c_cancelings', 'c_canceling' )
	and vtiger_servicecontracts.backstatus in ('c_complete','已发放')
	and vtiger_servicecontracts.signaturetype !='eleccontract'
	and now() > date_add(vtiger_servicecontracts.canceltime,interval + 2 day)";

$result = $adb->pquery($sql, array());
if ($adb->num_rows($result)) {
    //有值排队一个个来
    while ($row = $adb->fetchByAssoc($result)){
        $adb->pquery('UPDATE vtiger_servicecontracts SET cancelid=null,canceltime=null,cancelvoid=null,pagenumber=null,cancelmoney=null,cancelremark=null,modulestatus=?,backstatus=? WHERE servicecontractsid=?',array($row['backstatus'],$row['modulestatus'],$row['servicecontractsid']));
        $adb->pquery("DELETE FROM vtiger_salesorderworkflowstages WHERE salesorderid=? AND vtiger_salesorderworkflowstages.workflowsid=?  AND vtiger_salesorderworkflowstages.modulename='ServiceContracts'",array($row['servicecontractsid'],$row['workflowsid']));
    }
}



