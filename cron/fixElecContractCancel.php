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
error_reporting(1);


include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';


vglobal('default_language', $default_language);
$currentLanguage = 'zh_cn';
vglobal('current_language', $currentLanguage);

global $adb;
$result = $adb->pquery("SELECT
	attachmentsid ,
	contract_no,
servicecontractsid
	from 
	vtiger_files a 
	left join vtiger_servicecontracts b on a.relationid=b.servicecontractsid
WHERE
	b.contract_no in('ZD-SYH2021010001',
	'ZD-0SEO-2020010001',
	'ZD-0SEO-2020010001',
	'ZD-2020020004',
	'ZD-2020010004',
	'ZD-2020040007',
	'ZD-2020080004',
	'ZD-2020010006',
	'ZD-2020010004',
	'ZD-2020010006',
	'ZD-0FWQ-2020010002',
	'ZD-0WJD-2020030002',
	'ZD-2019120011',
	'ZD-2020040006',
	'ZD-2020040006',
	'ZD-0FWQ-2020010006',
	'ZD-CRM2020090001',
	'ZD-KF2020090001',
	'ZD-2020080006',
	'ZD-2020080005',
	'ZD-XCXSHOP2020100001',
	'ZD-2020080006',
	'ZD-YQ2020120004',
	'ZD-0YK-2020090002',
	'ZD-2020030005',
	'ZD-2019100002',
	'ZD-2020040005',
	'ZD-CRM2020100006',
	'ZD-XCXSHOP2020100004',
	'ZD-XCXSHOP2020120001',
	'ZD-YQ2020100003',
	'ZDYUN2017070002',
	'ZD-0FWQ-2020010004',
	'ZDYUN2017070002',
	'ZD-2018010014',
	'ZDWX-2019030001',
	'ZD-FW2017120002',
	'ZDWX-0WJD-2019100001',
	'ZDWX-0WJD-201910001',
	'ZDWX-0WJD-2019100001',
	'ZDWX-0WJD-201910001',
	'ZDWX-0WJD-2019100001',
	'ZD-CRM2020120002',
	'ZD-SHJ-2020120002',
	'ZD-YQ2020120002',
	'ZD-0YK-2020120003',
	'ZD-2020030004',
	'ZD-0WJD-2020070003',
	'ZD-2020080009',
	'ZD-2020030004',
	'ZD-2020080008',
	'ZD-0WJD-2020070003',
	'ZD-2020080009',
	'ZD-CRM2020120003',
	'ZD-SHJ-2020120003',
	'ZD-YQ2020120001',
	'ZD-0YK-2020120005',
	'ZD-CRM2020100004',
	'ZD-KF2020110001',
	'ZD-XCXSHOP2020100002',
	'ZD-XCXSHOP2020110002',
	'ZD-YQ2020100001',
	'ZD-0YK-2020100009',
	'ZDWX-2018020001',
	'ZDWX-0WJD-2018090001',
	'ZDWX-0WJD-2018090001',
	'ZD-YQ2021010002',
	'ZDWL-YUN2017080001',
	'ZD-CRM2020100003',
	'ZD-XCXSHOP2020090003',
	'ZD-XCXSHOP2020100006',
	'ZD-YQ2020090003',
	'ZD-0YK-2020090001',
	'ZD-CRM2020100005',
	'ZD-KF2020110002',
	'ZD-XCXSHOP2020100003',
	'ZD-YQ2020100002',
	'ZD-0YK-2020100008',
	'ZD-CRM2020110001',
	'ZD-SHJ-2020110001',
	'ZD-SHJ-2020120004',
	'ZD-YQ2020110001',
	'ZDWX-0SEO-2018080001',
	'ZD-0FWQ-2020010005',
	'ZD-0WJD-2018060002',
	'ZD-0WJD-2018070002',
	'ZDWL-0FWQ-2018080001',
	'ZD-0YK-2020120004',
	'ZDWL-0FWQ-2018080001',
	'ZD-KF2020100002',
	'ZD-XCXSHOP2020090002',
	'ZD-YQ2020090002',
	'ZD-YQ2020100009',
	'ZD-0FWQ-2020010009',
	'ZD-KF2020100001',
	'ZD-XCXSHOP2020090001',
	'ZD-YQ2020090001',
	'ZD-YQ2020100008',
	'ZD-CRM2020110003',
	'ZD-SHJ-2020110003',
	'ZD-YQ2020110002',
	'ZD-YQ2020120003',
	'ZD-CRM2020110002',
	'ZD-SHJ-2020110002',
	'ZD-SHJ-2020120005',
	'ZD-0YK-2020090004',
	'ZD-0YK-2020090005',
	'ZD-2020070005',
	'ZD-CRM2020100002',
	'ZD-XCXSHOP2020100007',
	'ZD-YQ2020100006',
	'ZD-0YK-2020090006',
	'ZD-CRM2020070001',
	'ZD-CRM2020090002',
	'ZD-2020070006',
	'ZD-CRM2020120001',
	'ZD-SHJ-2020120001',
	'ZDWL-0FWQ-2018050003',
	'ZDWL-0FWQ-2018050003-1',
	'ZDWL-0FWQ-2018050003',
	'ZD-0YK-2020090003',
	'ZDWL-SZWK-2021020041',
	'ZDWL-YUN2017080002',
	'ZD-2020080011',
	'ZD-XCXSHOP2020110001',
	'ZD-XCXSHOP2020120002',
	'ZD-KF2021020001',
	'ZD-SHJ-2021010001',
	'ZD-YQ2021010001',
	'ZD-WKYX2020070001',
	'ZD-0YK-2020090007',
	'ZD-XLSMPP2020000041',
	'ZDWL-WK2021000016',
	'ZD-XCXSHOP2020090004',
	'ZD-0WJD-2020040001',
	'ZD-2019110005',
	'ZD-XCXJZ2019000060',
	'ZD-0KB-2020070002',
	'ZD-CRM2020120001',
	'ZD-SHJ-2020120001' ) 
	AND a.style = 'files_style4' 
	AND a.description = 'ServiceContracts'
and a.delflag=0
	",array());
while ($row = $adb->fetchByAssoc($result)){
    down($row['attachmentsid'],$row['contract_no'],$row['servicecontractsid']);
}

function down($fileid,$contract_no,$servicecontractsid)
{
    global $current_user,$adb;
    $result = $adb->pquery("SELECT * FROM vtiger_files WHERE attachmentsid=?", array($fileid));
    if($adb->num_rows($result)) {
        $fileDetails = $adb->query_result_rowdata($result);
        $filePath = '/data/httpd/crm/'.$fileDetails['path'];
        $fileName = html_entity_decode($fileDetails['name'], ENT_QUOTES, vglobal('default_charset'));
        if($fileDetails['newfilename']>0){
            $savedFile = $fileDetails['attachmentsid'] . "_" . $fileDetails['newfilename'];
        }else{
            $t_fileName = base64_encode($fileName);
            $t_fileName = str_replace('/', '', $t_fileName);
            $savedFile = $fileDetails['attachmentsid'] . "_" . $t_fileName;
        }
        if(!file_exists($filePath.$savedFile)){
            $savedFile = $fileDetails['attachmentsid']."_".$fileName;
        }
        $fileSize = filesize($filePath.$savedFile);
        $fileSize = $fileSize + ($fileSize % 1024);
        echo $filePath.$savedFile.'<br>';
        if (fopen($filePath.$savedFile, "r")) {
            $fileContent = fread(fopen($filePath.$savedFile, "r"), $fileSize);
            $dir = '/data/httpd/crm/temp/hetong/';
            if(!is_dir($dir)) {
                mkdir($dir,0755,true);
            }
            $fileName = $contract_no.'_'.$servicecontractsid.'_'.$fileName;
            file_put_contents($dir.$fileName,$fileContent);
        }
    }
}


//global $adb;
//$result = $adb->pquery("select file from vtiger_contract_template",array());
//while ($row = $adb->fetchByAssoc($result)){
//    $fileData = explode("##",$row['file']);
//    if(!$fileData[1]){
//        continue;
//    }
//    $data[] = $fileData[1];
//    down($fileData[1]);
//}
//
//function down($fileid)
//{
//    global $current_user,$adb;
//    $result = $adb->pquery("SELECT * FROM vtiger_files WHERE attachmentsid=?", array($fileid));
//    if($adb->num_rows($result)) {
//        $fileDetails = $adb->query_result_rowdata($result);
//        $filePath = '/data/httpd/crm/'.$fileDetails['path'];
//        $fileName = html_entity_decode($fileDetails['name'], ENT_QUOTES, vglobal('default_charset'));
//        if($fileDetails['newfilename']>0){
//            $savedFile = $fileDetails['attachmentsid'] . "_" . $fileDetails['newfilename'];
//        }else{
//            $t_fileName = base64_encode($fileName);
//            $t_fileName = str_replace('/', '', $t_fileName);
//            $savedFile = $fileDetails['attachmentsid'] . "_" . $t_fileName;
//        }
//        if(!file_exists($filePath.$savedFile)){
//            $savedFile = $fileDetails['attachmentsid']."_".$fileName;
//        }
//        $fileSize = filesize($filePath.$savedFile);
//        $fileSize = $fileSize + ($fileSize % 1024);
//        echo $filePath.$savedFile.'<br>';
//        if (fopen($filePath.$savedFile, "r")) {
//            $fileContent = fread(fopen($filePath.$savedFile, "r"), $fileSize);
//            $dir = '/data/httpd/crm/temp/moban/';
//            if(!is_dir($dir)) {
//                mkdir($dir,0755,true);
//            }
//            file_put_contents($dir.$fileName,$fileContent);
//        }
//    }
//}



//$record=2887727;
//$recordModel = ServiceContracts_Record_Model::getCleanInstance( 'ServiceContracts');
//$sql2 = "update vtiger_servicecontracts set modulestatus=?,receivedate=?,eleccontractstatus='b_elec_actioning' where servicecontractsid=?";
//$adb->pquery($sql2,array('已发放',date("Y-m-d"),$record));
////发送电子合同给客户
//$res = $recordModel->sendElecContract($record);
//if(!$res){
//    $sql3 = "update vtiger_servicecontracts set eleccontractstatus='a_elec_actioning_fail' where servicecontractsid=?";
//    $adb->pquery($sql3,array($record));
//    echo '发送电子合同失败';
//    exit();
//}
//echo '发送电子合同成功';

//
//
//$record=2888341;
//$contract_no='ZDDG-TYUNXF2021000001';
//$query = "SELECT * FROM vtiger_activationcode WHERE contractname=? AND `status`!=2";
//
//$type_result = $adb->pquery($query, array($contract_no));
//if ($adb->num_rows($type_result)) {
//    $rowData = $adb->query_result_rowdata($type_result,0);
//    $tyunWebRecordModel = TyunWebBuyService_Record_Model::getCleanInstance("TyunWebBuyService");
//    $tyunWebRecordModel->doOrderCancelByContractNo($contract_no,$rowData['usercodeid'],$rowData['usercode']);
//}
//$adb->pquery("UPDATE vtiger_servicecontracts SET workflowsnode='已作废',modulestatus='c_cancel',eleccontractstatus='a_elec_sending' WHERE servicecontractsid=?", array($record));
//$data = array(
//    'recordid'=>  $record,
//    'reason'=>'提单人要求打回',
//    'isPass'=>0,
//);
//$recordModel = ServiceContracts_Record_Model::getCleanInstance( 'ServiceContracts');
////审核不通过同步到放心签平台
//$res = $recordModel->auditStatus($data);
//if($res){
//    echo '执行完成';
//    return;
//}
//echo '通知放心签失败';
