<?php
/**
 * B类客户
 */
set_time_limit(0);
$dir=trim(__DIR__,DIRECTORY_SEPARATOR);
$dir=trim(__DIR__,'cron');
ini_set("include_path", $dir);
require_once('config.php');
require_once('include/utils/utils.php');
require_once('include/logging.php');
header("Content-type:text/html;charset=utf-8");
error_reporting(0);
//ini_set('display_errors','on'); error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';


vglobal('default_language', $default_language);
$currentLanguage = 'zh_cn';
vglobal('current_language', $currentLanguage);
$dsn='mysql:host=172.31.255.83;dbname=T_Cloud_Promote2;charset=utf8';
$tempdbuser='erp_select';
$tempdbpass='SGwshu76_SGWjhuty23SGjh_SG';
echo '----------start------------<br>',"\n";
try {
    $pdodb = new PDO($dsn, $tempdbuser, $tempdbpass);
    $step=1500;
    $startnum=0;
    echo '<pre>';
    while(1){
        $query='SELECT ContractCode FROM `T_Cloud_User_Register` WHERE ContractCode IS NOT NULL AND ContractCode!=\'\' limit '.($startnum*$step).','.$step;
        echo $query;
        $smt=$pdodb->query($query);
        $data=$smt->fetchAll(PDO::FETCH_ASSOC);
        if(empty($data)){
            break;
        }
        $str='';
        foreach($data as $value){
            $str.="'".$value['ContractCode']."',";
        }
        $str=trim($str,',');
        $sql='UPDATE vtiger_activationcode SET isbcustomer=1 WHERE contractname in('.$str.')';
        $adb->pquery($sql,array());
        $startnum++;
    }
}catch(Exception $e){
    die();
}
echo "\n",'-----------ends-----------';
exit;
/*
$url='https://tyapi.71360.com/api/app/aggregateservice-api/v1.0.0/api/Speed/GetNoRegUserID';
$time = time() . '123';
$sault1 = $time . $sault;
$token = md5($sault1);
$curlset = array(CURLOPT_HTTPHEADER => array(
    "Content-Type:application/json",
    "S-Request-Token:" . $token,
    "S-Request-Time:" . $time));
$jdata = "{}";
$jsonData = https_requestcomm($url, $jdata, $curlset);
$data=json_decode($jsonData,true);
if($data['success']){
    global $adb;
    $sql='UPDATE vtiger_activationcode SET isbcustomer=1 WHERE usercodeid in('.implode(',',$data['data']).')';
    $adb->pquery($sql,array());
    $query='SELECT DISTINCT customerid FROM vtiger_activationcode WHERE customerid>0 AND `status` IN(0,1) AND usercodeid IN('.implode(',',$data['data']).')';
    $result=$adb->pquery($query);
    $nums=$adb->num_rows($result);;
    if($nums){
        $i=0;
        $sql='REPLACE INTO `vtiger_b_account` (`accountid`) VALUES';
        $strstring='';
        while($row=$adb->fetch_array($result)){
            $i++;
            $strstring.='('.$row['customerid'].'),';
            if($i%5000==0 || $i==$nums){
                $strstring=trim($strstring,',');
                $insertSql=$sql.$strstring;
                $adb->pquery($insertSql);
                echo $insertSql,"<br>";
                $strstring='';
            }
        }
    }
}

function https_requestcomm($url,$data=null,$curlset=null,$islog=false){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    if(!empty($curlset)){
        foreach($curlset as $key=>$value){
            curl_setopt($curl, $key, $value);
        }
    }
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
*/