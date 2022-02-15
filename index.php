<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

//Overrides GetRelatedList : used to get related query
//TODO : Eliminate below hacking solution
//error_reporting(E_ALL); // PRODUCTION
//ini_set('display_errors','on'); error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);   // DEBUGGING
//ini_set('display_errors','on');
//自定义调试信息
//peardatabase.php244 行调用


header("Content-type:text/html;charset=utf-8");

define('TABLEPIX','vtiger_');

include_once 'debuger.php';
include_once('includes/runtime/Cache.php');
include_once('includes/Loader.php');
include_once('include/Webservices/Relation.php');
include_once('vtlib/Vtiger/Module.php');
include_once('includes/main/WebUI.php');
$webUI = new Vtiger_WebUI();
$webUI->process(new Vtiger_Request($_REQUEST, $_REQUEST));
