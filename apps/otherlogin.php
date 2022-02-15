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

require_once("PortalConfig.php");
include("version.php");
include_once('include/utils/utils.php');

@session_start();
$loginid=$_GET['loginid'];
$backurl=$_GET['backurl'];
if(empty($loginid)){
    echo "无效的请求!";
    exit;
}
if(isset($_SESSION['customer_id']) && $_SESSION['customer_id']>0 && isset($_SESSION['customer_name']))
{
	header("Location: index.php?module=QrcodeLogin&action=index&loginid=".$loginid."&backurl=".$backurl);
	exit;
}

global $default_charset;
header('Content-Type: text/html; charset='.$default_charset);
//获取企业微信的AccessToken
$corpid = "wx4d2151259aa58eba";
$Secret ="9n5ih34K5fFxuwAUJRiLhGY_HPvtA9p79VPfA4ltIgdsjTCGQOTWMCF6FEANlg_d";


/*获取token*/
$cache_token=@file_get_contents('./wtoken.txt');
$flag = false;
if(!empty($cache_token)){
	$tokens = json_decode($cache_token,true);
	if(!empty($tokens)&&isset($tokens['timeout'])&&$tokens['timeout']>time()){
		$flag = true;
	}
}

if(false===$flag){
	$url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=".$corpid."&corpsecret=".$Secret;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
	curl_setopt($ch, CURLOPT_TIMEOUT, 15);
	$output = curl_exec($ch);
	curl_close($ch);
	$data = json_decode($output,true);
	$data['timeout'] = time()+$data['expires_in']-600;
	file_put_contents('./wtoken.txt', json_encode($data));
}

//登录授权
$url =  "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$corpid"."&redirect_uri=".urlencode("http://m.crm.71360.com/otheraccess_token.php?loginid={$loginid}&backurl={$backurl}")."&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect";
header('location: '.$url);




?>
