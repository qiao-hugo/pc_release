<?php 

	$myData['activecode'] = 'c4aee8cf-8640-4249-99d6-6d4abe2934882211';//激活码
	$myData['activetype'] = 'first_activate';//动作
	$myData['startdate'] = '2017-03-16';//开始时间
	$myData['enddate'] = '2018-03-16';//结束时间
	$myData['remark'] = '厅李厚霖sdf';//备注
	//$url = "http://m.crm.dev/indexapi.php?module=ActivationCode&action=updateActiveCodeInfo";
	//$url = "http://m.crm.dev/indexapi.php?module=ActivationCode&action=test";
	$url = "http://m.crm.71360.com/indexapi.php?module=ActivationCode&action=test";
	
	$tempData['data'] = encrypt(json_encode($myData));
	//$tempData['sign'] = md5('sdsdf');
	$postData = http_build_query($tempData);//传参数
	$res = https_request($url, $postData);
	print_r($res);

	
	/**
	 * des加密
	 * @param unknown $encrypt 原文
	 * @return string
	 */
	function encrypt($encrypt, $key='sdfesdcf') {
		$mcrypt = MCRYPT_TRIPLEDES;
		$iv = mcrypt_create_iv(mcrypt_get_iv_size($mcrypt, MCRYPT_MODE_ECB), MCRYPT_RAND);
		$passcrypt = mcrypt_encrypt($mcrypt, $key, $encrypt, MCRYPT_MODE_ECB, $iv);
		$encode = base64_encode($passcrypt);
		return $encode;
	}
	
	/**
	 * des解密
	 * @param unknown $decrypt
	 * @return string
	 */
	function decrypt($decrypt, $key='sdfesdcf'){
		$decoded = str_replace(' ','%20',$decrypt);
		$decoded = base64_decode($decrypt);
		$mcrypt = MCRYPT_TRIPLEDES;
		$iv = mcrypt_create_iv(mcrypt_get_iv_size($mcrypt, MCRYPT_MODE_ECB), MCRYPT_RAND);
		$decrypted = mcrypt_decrypt($mcrypt, $key, $decoded, MCRYPT_MODE_ECB, $iv);
		return $decrypted;
	}
	
	function https_request($url, $data = null){
		$curl = curl_init();
		//curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type:application/x-www-form-urlencoded"));
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
