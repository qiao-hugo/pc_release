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


session_start();

error_reporting(0);
/** Function to  return a string with backslashes stripped off
 * @param $value -- value:: Type string
 * @returns $value -- value:: Type string array
 */
include_once('include/utils/utils.php');
require_once('libs/smarty/Smarty.class.php');

$root_directory = dirname(__FILE__).'/';

$smarty       			= new smarty();

$smarty->setTemplateDir($root_directory.'views/'); 
$smarty->setCompileDir($root_directory.'views_cache/'); 
require_once('baseapp.php');

function stripslashes_checkstrings($value){
	if(is_string($value)){
		return stripslashes($value);
	}
	return $value;
}

if(get_magic_quotes_gpc() == 1){
	$_REQUEST 	= array_map("stripslashes_checkstrings", $_REQUEST);
	$_POST 		= array_map("stripslashes_checkstrings", $_POST);
	$_GET 		= array_map("stripslashes_checkstrings", $_GET);
}

include("include.php");
include("version.php");

global $client;

$params = array(
		'fieldname'=>array('pagenum' 	=> 2,
						   'pagecount'  => 10)
	);


if(isset($_REQUEST['param'])&&$_REQUEST['param'] == 'forgot_password')
{
	global $client;

	$email = $_REQUEST['email_id'];
	$params = array('email' => "$email");
	$result = $client->call('send_mail_for_password', $params);
	$_REQUEST['mail_send_message'] = $result;
	require_once("supportpage.php");
}
elseif(isset($_REQUEST['logout'])&&$_REQUEST['logout'] == 'true')
{
	$customerid = $_SESSION['customer_id'];
	$sessionid 	= $_SESSION['customer_sessionid'];

	#$params = Array(Array('id' => "$customerid", 'sessionid'=>"$sessionid", 'flag'=>"logout"));
	#$result = $client->call('update_login_details', $params);
	unset($_SESSION['customer_id']);
	unset($_SESSION['customer_name']);
	unset($_SESSION['last_login']);
	unset($_SESSION['__permitted_modules']);
	header("Location: login.php");

}
else
{
	$module = 'main';
	$action = 'index';

	$isAjax = (isset($_REQUEST['ajax'])&&$_REQUEST['ajax'] == 'true')?true:false;
	
	if(true || $_SESSION['customer_id'] != '')
	{
		$customerid = $_SESSION['customer_id'];
		$sessionid 	= $_SESSION['customer_sessionid'];

		if(isset($_REQUEST['module'])&&$_REQUEST['module'] != '')
		{
			$module = $_REQUEST['module'];
		}
		if(isset($_REQUEST['action'])&&$_REQUEST['action'] != '')
		{
			$action = $_REQUEST['action'];
		}

		$filename = 'controller/'.$module.'.class.php';
		include("libs/Utils.php");
		global $default_charset;
		$smarty->assign('charset',$default_charset);
		$smarty->assign('title','珍岛app-erp系统');
		$smarty->assign('versionjs','1.0.1');
        $issendmsg=0;
        if(isset($_GET['issendmsg']) && !empty($_GET['issendmsg'])){
            $issendmsg=1;
		}
        
        $smarty->assign('issendmsg',$issendmsg);
		header('Content-Type: text/html; charset='.$default_charset);

		if(is_file($filename)) {
			include($filename);
			$module_class 	= new $module($client,$Server_Path,$_REQUEST,$smarty);
			$module_class->run($action);
		}else{
			echo $filename." file no found";
		}		
	}else{
		header("Location: login.php?backurl=".base64_encode($_SERVER['REQUEST_URI']));
	}
}

?>
