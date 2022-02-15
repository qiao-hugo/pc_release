<?php 
session_start();
header("Content-Type:text/html;charset=utf-8");
//echo "正在验证.....";
function check_login($email){
	
	include("include.php");
	include("version.php");
	require_once("PortalConfig.php");
	require_once("include/utils/utils.php");
	global $version,$default_language,$result;

	$params = array('email' => $email,'version' => "$version");
	$result = $client->call('login_from_weixin', $params, $Server_Path, $Server_Path);
	$err = $client->getError();
	if ($err)
	{
		echo '非法登录';
		exit;
	}
	//print_r($result);
	if(strtolower($result[0]['user_name']))
	{
		#session_start();
		$_SESSION['email'] 					= $email;
		$_SESSION['customer_id'] 			= $result[0]['id'];
		$_SESSION['customer_sessionid'] 	= $result[0]['sessionid'];
		$_SESSION['customer_name'] 			= $result[0]['user_name'];
		$_SESSION['last_name'] 				= $result[0]['last_name'];
		$_SESSION['phone_mobile'] 			= $result[0]['phone_mobile'];
		$_SESSION['departmentname'] 				= $result[0]['departmentname'];
		$_SESSION['reports_to_id'] 				= $result[0]['reports_to_id'];
		$_SESSION['roleid'] 				= $result[0]['roleid'];
		$_SESSION['rolename'] 				= $result[0]['rolename'];
		$_SESSION['departmentid'] 				= $result[0]['departmentid'];
                $_SESSION['waterText'] 				= $result[0]['watertext'];
		$customerid 						= $_SESSION['customer_id'];
		$sessionid 							= $_SESSION['customer_sessionid'];
		header("Location:/index.php?module=QrcodeLogin&action=index&loginid=".$_GET['loginid']."&backurl=".$_GET['backurl']);
	}
}
if(isset($_REQUEST['code'])&&$_REQUEST['code']){

	$cache_token=@file_get_contents('./wtoken.txt');
	$tokens = json_decode($cache_token,true);
	$access_token = $tokens['access_token'];
	$code 		  = $_REQUEST['code'];
	$url = "https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?access_token={$access_token}&code={$code}";
	$ch  = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_HEADER, 0);
	//curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
	curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
	curl_setopt($ch, CURLOPT_TIMEOUT, 15);
	$output = curl_exec($ch);
	curl_close($ch);
	$userinfo = json_decode($output,true);
	if(isset($userinfo['UserId'])&&$userinfo['UserId']){
		#echo $userinfo['UserId'];
		check_login($userinfo['UserId']);
	}else{
		echo "非法登录";
	}
}else{
	echo "非法登录";
}


?>
