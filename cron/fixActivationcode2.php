<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/11/6
 * Time: 15:09
 */

$dir=trim(__DIR__,DIRECTORY_SEPARATOR);
$dir=trim(__DIR__,'cron');
ini_set("include_path", $dir);
require_once('config.php');
require_once('include/utils/utils.php');
require_once('include/logging.php');
header("Content-type:text/html;charset=utf-8");
//error_reporting(0);


include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';


vglobal('default_language', $default_language);
$currentLanguage = 'zh_cn';
vglobal('current_language', $currentLanguage);
ini_set('display_errors','on'); error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);   // DEBUGGING
$orderCodes=array(
//    'T20200525120058067652','T20200831144257484580','T20201104140226714038','T20210106190532580586','T20210106191409640514',
//    'T20210108162055075371','T20210222125800571768','T20210318102102201506','T20210402164333261242','T20210407102541757607',
//    'S20210421172846514208','S20210508164536282475'
    'N20200519133625710683','N20200519155702645065','N20200519163528064062','OD2020120113521948147',
    'R20210611092911213270','R20210611092911702621','R20210611092911016517','R20210611092911630213'
);

foreach ($orderCodes as $orderCode){
    $recordModel = ServiceContracts_Record_Model::getCleanInstance("ServiceContracts");
    $request = new Vtiger_Request(array(),array());
    //调用合同里创建T云电子合同的方法
    $request->set('ordercode', $orderCode);
    $data[] = $recordModel->createTyunServiceContracts($request);
}
echo "<pre>";
var_dump($data);

