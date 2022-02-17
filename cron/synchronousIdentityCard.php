<?php
set_time_limit(0);
$dir=trim(__DIR__,DIRECTORY_SEPARATOR);
$dir=trim(__DIR__,'cron');
ini_set("include_path", $dir);
require_once('config.php');
require_once('include/utils/utils.php');
require_once('include/logging.php');
header("Content-type:text/html;charset=utf-8");
error_reporting(E_ALL);
include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';
vglobal('default_language', $default_language);
$currentLanguage = 'zh_cn';
vglobal('current_language', $currentLanguage);
global $adb;

$sql="select id from vtiger_users where idcard is null";
$idArrays=$adb->run_query_allrecords($sql);
foreach ($idArrays as $id){
    $sault='aSae23ios@d!45d1aB84aWe';
    $sign=md5($sault.$id['id']);
    $curlset=array(CURLOPT_HTTPHEADER=>array(
        "Content-Type:application/json"));
    $postData = array(
        "employeeId"=>(int)$id['id'],
        'sign'=>$sign
    );
    $url ='https://xxhoa.71360.com/cache/employeeAllInfo/getEmployeeAllInfoByCrmId';
    $res = json_decode(https_request($url, json_encode($postData),$curlset),true);
    if($res['success']){
        $data=json_decode(cbc_decrypt($res['data'],'f4k9f5w7f8g4er26', '5e8y6w45juem1234'),true);
        $sql="update vtiger_users set idcard=?,employeenumber=? where id=?";
        $adb->pquery($sql,array($data['idNumber'],$data['employeeNumber'],$id['id']));
    }
}
if(!empty($_GET['update'])){
    $sql="select id from vtiger_users  where employeenumber is null";
    $idArrays=$adb->run_query_allrecords($sql);
    foreach ($idArrays as $id){
        $sault='aSae23ios@d!45d1aB84aWe';
        $sign=md5($sault.$id['id']);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json"));
        $postData = array(
            "employeeId"=>(int)$id['id'],
            'sign'=>$sign
        );
        $url ='https://xxhoa.71360.com/cache/employeeAllInfo/getEmployeeAllInfoByCrmId';
        echo $url;
        echo '<hr>';
        $res = json_decode(https_request($url, json_encode($postData),$curlset),true);
		print_r($res);
        if($res['success']){
            $data=json_decode(cbc_decrypt($res['data'],'f4k9f5w7f8g4er26', '5e8y6w45juem1234'),true);
            $sql="update vtiger_users set employeenumber=? where id=?";
            echo $id['employeeNumber'],'====',$id['id'];
            echo '<hr>';
            $adb->pquery($sql,array($data['employeeNumber'],$id['id']));
            $sql="UPDATE `vtiger_usermanger` SET employeenumber=? WHERE userid=?";
            $adb->pquery($sql,array($data['employeeNumber'],$id['id']));
        }
    }
}

/**
 * 请求
 * @param $url
 * @param null $data
 * @param array $curlset
 * @return bool|string
 */
function https_request($url, $data = null,$curlset=array()){
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

/**
 * 解密
 * @param $data
 * @param $key
 * @param $iv
 * @return false|string
 */
function cbc_decrypt($data,$key,$iv) {
    $data = base64_decode($data);
    $data = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, $iv);
    $padding = ord($data[strlen($data) - 1]);
    return substr($data, 0, -$padding);
}

