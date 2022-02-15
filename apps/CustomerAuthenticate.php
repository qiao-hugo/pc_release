<?php
/*********************************************************************************
 ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 ********************************************************************************/
include("include.php");
include("version.php");
require_once("PortalConfig.php");
require_once("include/utils/utils.php");

global $version,$default_language,$result;
$username = trim($_REQUEST['username']);
$password = trim($_REQUEST['pw']);

session_start();
setPortalCurrentLanguage();
if(!empty($_REQUEST['typed']) && $_REQUEST['typed']==2){
    $password 	= substr($password,32,strlen($password)-32);
    $password 	= str_split($password,2);
    $password 	= '%'.implode('%',$password);
    $password 	= urldecode($password);
    $reData=https_requestcomm('http://192.168.44.157:8080/login/userLogin',json_encode(array('account'=>$username,'backUrl'=>'http://192.168.7.26/','password'=>$password)),array(CURLOPT_HTTPHEADER=>array(
        "Content-Type:application/json")));
    $reData=json_decode($reData,true);
    if($reData['resultCode']==200){
        $__vt_param__=explode('=',$reData['result']['backUrl']);
        $url="http://192.168.44.157:8080/validate/findAuthInfo?token=".$__vt_param__[1];
        $reData=https_requestcomm($url);
        $reData=json_decode($reData,true);
        $_SESSION['customer_id'] 			= $reData['result']['userid'];
        $_SESSION['customer_name'] 			= $reData['result']['fullname'];
        $_SESSION['phone_mobile'] 			= 0;
        $_SESSION['last_name'] 				= $reData['result']['fullname'];
        $_SESSION['reports_to_id'] 				= $reData['result']['reportstoid'];
        $_SESSION['roleid'] 				= $reData['result']['roleid'];
        $_SESSION['departmentname'] 				= $reData['result']['departmentname'];
        $_SESSION['rolename'] 				= $reData['result']['rolename'];
        $_SESSION['departmentid'] 				= $reData['result']['departmentid'];
        $customerid 						= $_SESSION['customer_id'];
        $sessionid 							= $_SESSION['customer_sessionid'];
        header("Location: index.php?module=VisitingOrder&action=vlist");
    }else{
        $login_error_msg = getTranslatedString("LBL_CANNOT_CONNECT_SERVER");
        $login_error_msg = base64_encode('<font color=red size=1px;> ' . $login_error_msg . ' </font>');
        header("Location: login.php?login_error=$login_error_msg");
    }
    exit;
}else {

    $clientsessionid = session_id();
    $params = array('user_name' => "$username",
        'user_password' => "$password",
        "sessionid" => $clientsessionid,
        'version' => "$version",
    );

    $result = $client->call('authenticate_user', $params, $Server_Path, $Server_Path);
//The following are the debug informations
    $err = $client->getError();
    if ($err) {
        $login_error_msg = getTranslatedString("LBL_CANNOT_CONNECT_SERVER");
        $login_error_msg = base64_encode('<font color=red size=1px;> ' . $login_error_msg . ' </font>');
        header("Location: login.php?login_error=$login_error_msg");
        exit;
    }
}

if(strtolower($result[0]['user_name']) == strtolower($username) && strtolower($result[0]['user_password']) == strtolower($password))
{
	session_start();
	$_SESSION['customer_id'] 			= $result[0]['id'];
	$_SESSION['customer_sessionid'] 	= $result[0]['sessionid'];
	$_SESSION['customer_name'] 			= $result[0]['user_name'];
    $_SESSION['phone_mobile'] 			= $result[0]['phone_mobile'];
	$_SESSION['last_name'] 				= $result[0]['last_name'];
	$_SESSION['reports_to_id'] 				= $result[0]['reports_to_id'];
    $_SESSION['roleid'] 				= $result[0]['roleid'];
    $_SESSION['departmentname'] 				= $result[0]['departmentname'];
    $_SESSION['rolename'] 				= $result[0]['rolename'];
    $_SESSION['departmentid'] 				= $result[0]['departmentid'];
    $_SESSION['waterText'] 				= $result[0]['watertext'];
	$customerid 						= $_SESSION['customer_id'];
	$sessionid 							= $_SESSION['customer_sessionid'];
	header("Location: index.php?module=VisitingOrder&action=vlist");
}
else
{
	if($result[0] == 'NOT COMPATIBLE'){
		$error_msg = getTranslatedString("LBL_VERSION_INCOMPATIBLE");
	}elseif($result[0] == 'INVALID_USERNAME_OR_PASSWORD') {
		$error_msg = getTranslatedString("LBL_ENTER_VALID_USER");	
	}elseif($result[0] == 'MORE_THAN_ONE_USER'){
		$error_msg = getTranslatedString("MORE_THAN_ONE_USER");
	}
	else
		$error_msg = getTranslatedString("LBL_CANNOT_CONNECT_SERVER");

	$login_error_msg = base64_encode('<font color=red size=1px;> '.$error_msg.' </font>');
	header("Location: login.php?login_error=$login_error_msg");
}
function https_requestcomm($url,$data=null,$curlset=null){
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
    $info = curl_getinfo($curl);
    curl_close($curl);

    return $output;
}

