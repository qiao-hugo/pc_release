<?php


define('DEVELAPOR',true);
if(substr(php_uname(),0,7)=='Windows'){
	define('DEBUG_LINE_SEP', "\r\n");
}else{
	define('DEBUG_LINE_SEP', "\n");
}

$ROOTWEB=dirname(__FILE__);
$sql_trace_all_array=array();
$develapor_trace=array();
$begin_page_time=microtime(true);
$begin_page_mem=memory_get_usage();
$class_loader_all=array();
$custom_function_counts=array();
$log_htmls="";

$log_array=array();
//$files_include=array();
//调试信息
function trace($value,$label){
	//$name=empty($name)?get_variable_name($arr):$name;
	//ob_clean();
	$backtrace    =	debug_backtrace();
	$info   =   ($label?$label.':':'').'<pre>'.print_r($value,true).'</pre><p>文件 '.$backtrace[0]['file'].' 函数 '.$backtrace[1]['function'].' 行 '.$backtrace[0]['line'].'</p>';
	$GLOBALS['develapor_trace'][$backtrace[1]['function'].$backtrace[0]['line']]=$info;
	
	//ob_end_clean();
}
//获取文件大小，同时判断是否存在
function getsize($file){return;
	if(file_exists($file)){
		return str_ireplace('C:\Program Files\vtigercrm600\apache\htdocs\vtigerCRM', '', $file).' '.round(filesize($file)/1024,4).'kb';
	}else{
		return '文件不存在';
	}
}
//替换一次,主要是解析sql
function defined_str_replace($sql,$arr){return;
	if(!empty($arr)&&is_array($arr)){
		foreach($arr as $v){
			$pos=strpos($sql, '?');
			$sql=substr_replace($sql, $v, $pos, strlen('?'));
		}
	}
	return $sql;
}
//页面内存使用情况
function page_inf(){
	$exectime=round((microtime(true)-$GLOBALS['begin_page_time']),2).'s';
	$unit=array('b','kb','mb','gb','tb','pb');
	$execmem=round((memory_get_usage()-$GLOBALS['begin_page_mem'])/pow(1024,($i=floor(log((memory_get_usage()-$GLOBALS['begin_page_mem']),1024)))),2).' '.$unit[$i];
	
	return array('a'=>$exectime,'b'=>$execmem);
}


//自定义错误页面和显示数据,错误信息
function define_error($errno, $errstr, $errfile, $errline){
	//header("Content-type:text/html;charset=utf-8;");
	return false;
	global $log_array;
	
$adasdasd=Array ('808c4cf78b163e4795e8c39e6dc2fc9d','b665033a31123df37c6a6370f6f02708','70e5d4967c2f2a9e521f73d38d0070d9','b81a394261de30466b290a3837fd3d89','39f1c5b8c9f914ce2e15d26636a3e840','687d612c683fba44f6561cc9fc86ce05','38c4ac64d219be5a2a9c66207b24514f','343399c55f2e155d7264bc1c4168e2fa','67e9db3c8a4243ed4b1b3fe1a68e4e28','3118ebf442df20feea12a555f43ea8af','3ee60c335390939a201dc66e14a9987c','60a5459fd96f410711a8a30dddb78dbe','be9d795d27d13ad2f321461b627b1749','97ac29e20a1e0d74b66bb29ac37ad993','29a2bcb0f719583208c172e6dc8b459f','8d43888529c52e0e7e3d0c9f711a2187','ab66a393dfc2350e1a92b2476c1bd272','f919a2681c8daec19ca3d7d7d577ccc9','6c60ed3e078ce79206d3c8c02151a246','685428892479914886204bdb970c3699','8c10e32bd0c96854d9eac99b4defd5c8','656c3e849142a30ead3ae383c0d0130f','43075f69019e0e7e783dc0306b3c6f74','7043d3c93ccbec8c2c7bcc28ba5be630','21b005e86d8be80e61264d36bfffaa6c','d3cd022ce799ab58fe7342338b0bdf7e','0cf799c92478ac80ace467f88c4e55d0','a6fbda2b634d449076e4a8a9b466310c','d1de71e34ab398e0b3adc88f008712bc','98bf9b5f74909b22f935efdb0e8cb804','1e97a046cdb25031b2eb520cfb8cc229','279205f3fa043b895a40825b50a80b98','91afd56babd99d6a262e6d24a897da28','ad3cee6d3e6667ee03337e5039e65b6c','cad6bb1155ef182cafa62461d0b2c8bd','718a011635559ef76a16279c0b07c6fe','63229ce9bd7d9134cffebc37b284da47','7ac28942961cb575e1dd998bf2b74f19','d4467e26d9c6b817e4d6e141db8c0038','e0aa43f3b8d16645e5ad4acd0dbba521','6b7850a9e37a40a98bbd11e2666e5456','5e96c7708e45d20aa9167559723d814a','b6363174063a77817eb857dfe7f5694b','399caca00895f0a50ddb108c4b3d5266','e5b6cceb35db55fd008f8ad634d51840','c4cdaa146925c175805f5784b0446d53','80e3f912597a5d3ea0b5725893f25e81','ea335d7685e186882ed5f2673e678279','98c7285680ce587a54e77d3d3b3c19ae','a55c154df5e08de2d1eab19bae10e39f','86ad82a031bd0ef354e7ccc365b63e49','82a2225a3e756381749ceecfcf465d32','66c046cd4d3254ed3977bf55c18501f0','1a2ed9ba30e8f58f90f1c04ecf170948','c3248c0217d04480ebce1682326f28ff','a5f38b9c21984833fe7ee2bbad6bcdfa','a7bf98f2e0790af434bae93ecd6969c2','f865375bf496385fef79ad195405fa78','1616709516c52b5a07076d6aba5c0bc1','a03a28b633b66f9b637b2de3db4cc251','d5551915b84faa2adaa42f98188891a1','04cdba4884043c51481516dda9ef4fa4','1c15be6c59b38e867d7914fd8101b0e2','817cc7a81a263f35bb7c3c9602fe4400','4ae4e39a00da339ace5dc298f7a3aed9','58d7c24217e5e49099c4b25f260e484b','1cdf577b81267c984b6ebac9b3de85aa','ad635344f9e54e57927a9ad5b5612739','c6dcfbe09a82a754aa06ae1b45e61c96','0156ac075892b5fd78dac8a389bf259c','6f5d2cc17fca1e5dedc6c7a2cf18ffd3');
//屏蔽的目录
//$nofold=array('utils','database','debug','adodb','libraries','vtlib','test');
	/*	if($errno<>2048&&strpos($errfile,'libraries')==false&&strpos($errfile,'include')==false&&strpos($errfile,'vlayout')==false&&strpos($errfile,'Vtiger')==false&&strpos($errfile,'Users')==false&&strpos($errfile,'includes')==false){
	$info= "errno：$errno,$errstr\n";
	$info.= "errstr：$errfile,$errline\n";
	
	//$info.= 'errtime：'.date('Y-m-d H:i:s');
	//$info.=print_r(debug_backtrace(),true);
	echo $info;}
	*/
	$uniqueid=md5($errno.$errstr.$errfile.$errline);//唯一识别码
	if(in_array($uniqueid,$adasdasd)){
		return false;
	}elseif(strpos($errfile,'utils')>0||strpos($errfile,'database')>0||strpos($errfile,'debug')>0||strpos($errfile,'adodb')>0||strpos($errfile,'libraries')>0||strpos($errfile,'vtlib')>0||strpos($errfile,'test')>0){
		return false;
	}else{
		
		$errfile=str_ireplace($GLOBALS['ROOTWEB'],'',$errfile);
		
		//echo $errno.'<br>';
		if(!array_key_exists($uniqueid,$log_array)){
		$log_array[$uniqueid]=array('errno'=>$errno,'errstr'=>$errstr,'errfile'=>$errfile,'errline'=>$errline,'uniqueid'=>$uniqueid,'time'=>date('i:s'));
		}
		//$sql="insert into vtiger_logs(uniqueid,errno,errstr,errfile,errline,addtime,status) values('$uniqueid','$errno','$errstr','$errfile','$errline','".date('Y-m-d')."',0) ";
		//$db->pquery($sql,null);
		return false;
	}
}
set_error_handler('define_error');

function writelog($val){
	$file=fopen('C:\Program Files\vtigercrm600\apache\htdocs\vtigerCRM\logs\test1.txt', 'a');
	if(is_array($val)){
		$str=var_export($val);
	}else{
		$str=$val;
	}
	fwrite($file,$str.'\r');
	fclose($file);
}

?>