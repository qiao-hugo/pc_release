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
$sql = "SELECT
	a.servicecontractsid,
       a.contract_no
FROM
	vtiger_servicecontracts a
LEFT JOIN vtiger_activationcode b ON a.servicecontractsid = b.contractid
WHERE
	a.modulestatus = 'c_complete'
AND a.eleccontractstatus = 'c_elec_complete'
AND a.signaturetype = 'eleccontract'
AND b.contractstatus = 1
AND b.`status` IN (0, 1)
AND b.pushstatus = 0
and b.comeformtyun=1
GROUP BY
	servicecontractsid";
$result = $adb->pquery($sql,array());
if(!$adb->num_rows($result)){
    return;
}

while ($row = $adb->fetchByAssoc($result)){
    $datas[] = $row;
}
$sault='multiModuleProjectDirectoryasdafdgfdhggijfgfdsadfggiytudstlllkjkgff';


foreach ($datas as $data){
    $recordModel = ServiceContracts_Record_Model::getCleanInstance("ServiceContracts");
    $query='SELECT usercodeid FROM vtiger_activationcode WHERE contractid=? AND `status` in(0,1) AND usercodeid>0 AND pushstatus=0 AND comeformtyun=1 LIMIT 1';
    $result=$adb->pquery($query,array($data['servicecontractsid']));
    if($adb->num_rows($result)) {
        $usercodeid = $result->fields['usercodeid'];
        $array = array("userID" => $usercodeid, "ContractCode" => $data['contract_no']);
        $postData = json_encode($array);
        $ContractConfirm = "http://pretyapi.71360.com/api/micro/order-basic/v1.0.0/api/Order/ContractConfirm";
        $time = time() . '123';
        $sault1 = $time . $sault;
        $token = md5($sault1);
        $curlset = array(CURLOPT_HTTPHEADER => array(
            "Content-Type:application/json",
            "S-Request-Token:" . $token,
            "S-Request-Time:" . $time));
        $res = https_requestTweb($ContractConfirm, $postData, $curlset);
        $resultData = json_decode($res, true);
        var_dump($resultData);
        if ($resultData['code'] == 200) {
            $adb->pquery('UPDATE vtiger_activationcode SET pushstatus=1 WHERE contractid=? AND `status` in(1,0)', array($data['servicecontractsid']));
        }
    }
}

function https_requestTweb($url, $data = null,$curlset=array()){
    $curl = curl_init();
    if(!empty($curlset)){
        foreach($curlset as $key=>$value){
            curl_setopt($curl, $key, $value);
        }
    }
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if (!empty($data)){
        curl_setopt($curl, CURLOPT_POST, 1);//post提交方式
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}

