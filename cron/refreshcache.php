<?php
//更新系统缓存定时任务每天是晚上11：30执行
//error_reporting(0);
set_time_limit(0);
header("Content-type:text/html;charset=utf-8");
$dir=trim(__DIR__,DIRECTORY_SEPARATOR);
$dir=trim(__DIR__,'cron');
ini_set("include_path", $dir);
require_once('config.php');
require_once('include/utils/utils.php');
require_once('include/logging.php');

include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';
ini_set('display_errors','on'); error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
$reqest=new Vtiger_Request($_REQUEST,$_REQUEST);
global $root_directory,$currentModule;
include $root_directory.'modules/Settings/Cacheinfo/views/Index.php';
$currentModule='Cacheinfo';
echo "cache....start....\n";
$serttingsCache=new Settings_Cacheinfo_Index_View();
$serttingsCache->process($reqest);

echo "cache....endl....\n";
