<?php
$dir= __DIR__;
$dir=rtrim($dir,'/cron');
ini_set("include_path", $dir);

require_once('config.php');
require_once('include/utils/utils.php');
require_once('include/logging.php');

header("Content-type:text/html;charset=utf-8");
//error_reporting(0);
include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';
set_time_limit(0);
/**
 *  下面是调取员工的级别的处理
 */
function http_request($url, $data = null,$curlset=array()){
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

function cbc_decrypt($data,$key,$iv) {
    $data = base64_decode($data);
    $data = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, $iv);
    $padding = ord($data[strlen($data) - 1]);
    return substr($data, 0, -$padding);
}

updateUserGradeRoyalty();
function updateUserGradeRoyalty(){
    global $adb;
    $url='https://xxhoa.71360.com/cache/department/getAllDepartment?sign=453c02843968f87c41cd963a3d4a8bf8';
    $DataJson=http_request($url);
    $data=json_decode($DataJson,true);
    if($data['success']==1) {
        $jsonData = cbc_decrypt($data['data'], 'f4k9f5w7f8g4er26', '5e8y6w45juem1234');
        $arrayData = json_decode($jsonData, true);
        $SqlSplitJoin = '';
        foreach ($arrayData as $value) {
            $SqlSplitJoin .= "('" . $value['departmentId'] . "','" . $value['departmentName'] . "','" . $value['path'] . "','" . $value['crmPrincipalId'] . "'," . substr_count($value['path'], "::") . "),";
        }
        $SqlSplitJoin = trim($SqlSplitJoin, ',');
        $adb->pquery('truncate table vtiger_departments', array());
        $SqlSplitJoin = 'INSERT INTO vtiger_departments(departmentid,departmentname,parentdepartment,peopleid,depth) VALUES' . $SqlSplitJoin;
        $adb->pquery($SqlSplitJoin, array());
    }
}






