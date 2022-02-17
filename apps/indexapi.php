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
require_once('baseapi.php');

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

session_start();
$_SESSION['customer_id_api'] 			= '1';

$_SESSION['customer_name_api'] 			= 'Jeff';
//$_SESSION['last_name'] 				= 'jeff';
//$_SESSION['phone_mobile'] 				= '13641833211';//获取T云数据
//$customerid 						= $_SESSION['customer_id'];
//$sessionid 							= $_SESSION['customer_sessionid'];


//$moduleArr = array('Api');
$module = $_REQUEST['module']?$_REQUEST['module']:'main';
$action = $_REQUEST['action']?$_REQUEST['action']:'index';
/* if(!in_array($module, $moduleArr)){
	exit();
} */

//$isAjax = (isset($_REQUEST['ajax'])&&$_REQUEST['ajax'] == 'true')?true:false;

if($_SESSION['customer_id_api'] != '')
{
	//$customerid = $_SESSION['customer_id'];
	//$sessionid 	= $_SESSION['customer_sessionid'];
	
	if(isset($_REQUEST['module'])&&$_REQUEST['module'] != '')
	{
		$module = $_REQUEST['module'];
	}
	if(isset($_REQUEST['action'])&&$_REQUEST['action'] != '')
	{
		$action = $_REQUEST['action'];
	}

	$filename = 'api/'.$module.'.class.php';
	include("libs/Utils.php");
	global $default_charset;
	$smarty->assign('charset',$default_charset);
	$smarty->assign('title','珍岛app-erp系统');

	header('Content-Type: text/html; charset='.$default_charset);
	
	if(is_file($filename)) {
		include($filename);
		//print_r($client);
		/* print_r($Server_Path);
		print_r($_REQUEST);
		print_r($smarty); */
		$module_class 	= new $module($client,$Server_Path,$_REQUEST,$smarty);
		$module_class->run($action);
	}else{
		echo $filename." file no found";
	}
}else{
	//header("Location: login.php");
	exit();
}


?>
