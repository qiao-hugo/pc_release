<?php
/**
 * 暂扣发放发放
 */
$dir=trim(__DIR__,DIRECTORY_SEPARATOR);
$dir=trim(__DIR__,'cron');
ini_set("include_path", $dir);
require_once('config.php');
require_once('include/utils/utils.php');
require_once('include/logging.php');
header("Content-type:text/html;charset=utf-8");
//error_reporting(0);
ini_set('display_errors','on'); error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';


vglobal('default_language', $default_language);
$currentLanguage = 'zh_cn';
vglobal('current_language', $currentLanguage);
global $adb,$tyunweburl;
$url=$tyunweburl.'api/micro/order-basic/v1.0.0/api/Product/GetWebSiteProducts';
$time=time().'123';
$sault1=$time.$sault;
$token=md5($sault1);
$curlset=array(CURLOPT_HTTPHEADER=>array(
    "Content-Type:application/json",
    "S-Request-Token:".$token,
    "S-Request-Time:".$time));
$data=https_requestcomm($url,NULL,$curlset);
$jsonData=json_decode($data,true);
echo $data;
$productIds=array();
$packageIds=array();
if($jsonData['success']){
    $productIds=$jsonData['data']['productIds'];
    $packageIds=$jsonData['data']['packageIds'];
}else{
    exit;
}
if(!empty($packageIds) || !empty($productIds)){
    $productIds=empty($productIds)?array(-1):$productIds;
    $packageIds=empty($packageIds)?array(-1):$packageIds;
    $query="SELECT DISTINCT servicecontractsid,contract_no,usercodeid 
        FROM vtiger_activationcode 
        LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid = vtiger_activationcode.contractid
        WHERE
         vtiger_servicecontracts.isfulldelivery=0 
         AND vtiger_servicecontracts.firstfulldelivery=0
        AND vtiger_activationcode.`status` in(0,1)
        AND (vtiger_activationcode.productid in(".implode(',',$packageIds).") OR (vtiger_activationcode.productid=0 AND vtiger_activationcode.buyseparately IN(" . implode(',', $productIds) . ")))";
    echo $query;
    $result=$adb->pquery($query);
    $array=array();
    $recordModel=Vtiger_Record_Model::getCleanInstance('AchievementSummary');
    $url=$tyunweburl.'api/micro/order-basic/v1.0.0/api/Order/GetWebSiteInfo';
    echo $url;
    while($row=$adb->fetch_array($result)){
        $record=$row['servicecontractsid'];
        $time=time().'123';
        $sault1=$time.$sault;
        $token=md5($sault1);
        $curlset=array(CURLOPT_HTTPHEADER=>array(
            "Content-Type:application/json",
            "S-Request-Token:".$token,
            "S-Request-Time:".$time));
        $jdata=json_encode(array('contractCode'=>$row['contract_no']));
        $data=https_requestcomm($url,$jdata,$curlset);
        echo $data;
        echo '<hr>';
        $jsonData=json_decode($data,true);
        if ($jsonData['success']) {
            echo '<hr>';
            $packageWeb=$jsonData['data']['packageWeb'];//套餐是否包含网站
            $packageWebOnline=$jsonData['data']['packageWebOnline'];//套餐网站是否上线
            $otherWeb=$jsonData['data']['otherWeb'];//另购是否包网站
            $otherWebOnline=$jsonData['data']['otherWebOnline'];//另购是网站是否上线
            //1套餐另购都包含网站且都已上线
            //2套餐含网站，另购不含网站，套餐网站上线
            //3套餐不含网站，另购含网站，另购网站上线
            //4套餐另购都不含网站，
            if(($packageWeb && $packageWebOnline && $otherWeb && $otherWebOnline) ||
                ($packageWeb && $packageWebOnline && !$otherWeb) ||
                (!$packageWeb && $otherWeb && $otherWebOnline) ||
                (!$packageWeb && !$otherWeb)
            ){
                try{
                    $db=PearDatabase::getInstance();
                    $query = 'SELECT 1 ';
                    $dataResult = $db->pquery($query, array());
                    $db->query_result_rowdata($dataResult, 0);

                }catch (Exception $exception){
                    $db=new PearDatabase();
                    $db->connect();
                }
                echo '成功匹配';
                echo '<hr>';
                echo '<hr>';
                echo '<hr>';
                $sql='UPDATE vtiger_servicecontracts SET isfulldelivery=1,fulldeliverytime=?,fulldeliveryid=? WHERE servicecontractsid=?';
                echo $sql;
                print_r(array(date('Y-m-d H:i:s'),6934,$record));
                echo '<hr>';
                echo '<hr>';
                $adb->pquery($sql,array(date('Y-m-d H:i:s'),6934,$record));
                $recordModel->setModTracker('ServiceContracts',$record,array('remark'=>array('currentValue'=>'已确认','oldValue'=>'确认产品完全交付')));
                echo
                $recordModel->customerServiceConfirmDelivery($record);
            }
        }
    }
    echo 'complete';
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
