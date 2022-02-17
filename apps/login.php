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
if(isset($_SESSION['customer_id']) && isset($_SESSION['customer_name']))
{
	header("Location: index.php");
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
$params='';
if(!empty($_REQUEST['backurl'])){
    $params='?backurl='.$_REQUEST['backurl'];
}
//登录授权
//$url =  "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$corpid"."&redirect_uri=".urlencode("http://m.crm.71360.com/access_token.php")."&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect";
$url =  "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$corpid"."&redirect_uri=".urlencode("http://m.crm.71360.com/access_token.php".$params)."&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect";
//header('location: '.$url);
if($_REQUEST['loginType'] != 'cs'){
    header('location: '.$url);eixt();
}




?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
		<title>登录</title>
		
	    
	    <meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=0,width=device-width" />
	    <meta content="yes" name="apple-mobile-web-app-capable" />
	    <meta content="yes" name="apple-touch-fullscreen" />
	    <meta content="telephone=no" name="format-detection" />
	   
	    <link href="static/css/bootstrap.min.css" rel="stylesheet" />
	    <link href="static/css/font-awesome.min.css" rel="stylesheet" />
	    <link href="static/css/login.css" rel="stylesheet" />
	    <link href="static/css/common.css" rel="stylesheet" />
	    <script src="static/js/jquery-2.1.0.min.js"></script>
	    <script src="static/js/bootstrap.min.js"></script>

</head>

<body>


<div class="container-fluid w">
        <div class="row">
            <div class="header tc">
                <i class="icon-chevron-left"></i>
                登录
            </div>
            <form  name="login" onsubmit="return rsa()"  action="CustomerAuthenticate.php" method="POST">
            <div class="logo"></div>
            <div class="loginbar">
                <div class="form-group">
                    <label for="exampleInputEmail1">登陆选项</label>
                    <select type="text" id='typed' name='typed' placeholder="" class="form-control">
                        <option value="1">ERP账号</option>
                        <option value="2">其他系统账号</option>
                    </select>
                </div>
            	<div class="form-group">
                    <label class="controls">
						<?php
						    
						   if(isset($_REQUEST['login_error'])&&$_REQUEST['login_error'] != '')
							echo '用户名或密码错误!'; 
						?>
					</label>
                </div>
                <div class="form-group">
                    <label for="exampleInputEmail1">账号</label>
                    <input type="text" id='username' name='username' placeholder="" class="form-control">
                </div>
                <div class="form-group">
                    <label for="exampleInputPassword1">密码</label>
                    <input type="password" id="pw" name='pw' placeholder="" class="form-control">
                    <input type="hidden" id="crmkey" value="<?=md5(getip())?>">
                </div>
            </div>
            <div class="login">
                <div class="form-group">
                    <button class="btn" type="submit">登录</button>
                </div>
            </div>
            
        </form>
        </div>
    </div>



</body>
</html>

<script language="javascript">

	function rsa(){
		var pwd=jQuery("input[name='pw']");
		if(pwd.val().length<1){
		alert('请输入完整的信息!');return false;
		}
		var es = [],c='',ec='';s = pwd.val().split('');
		for(var i=0,length=s.length;i<length;i++){
			c = s[i];ec = encodeURIComponent(c);
			if(ec==c){
				ec = c.charCodeAt().toString(16);ec = ('00' + ec).slice(-2);
			}
			es.push(ec);
		}
		var crmkey=jQuery("#crmkey").val();
		pwd.val(crmkey+es.join('').replace(/%/g,'').toUpperCase());
		return true;
	}

</script>

<?php
?>
