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
//ini_set('display_errors','on'); error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);   // DEBUGGING

//$oldDatas = array(
//    array(
//        'old_contract_no'=>'WK-16296867069704201',
//        'accountid'=>'3111153',
//        'firstcompany'=>'梁丰',
//        'total'=>'588'
//    ),
//    array(
//        'old_contract_no'=>'WK-16297044867618398',
//        'accountid'=>'3111153',
//        'firstcompany'=>'梁丰',
//        'total'=>'588'
//    ),
//    array(
//        'old_contract_no'=>'WK-16303782962650318',
//        'accountid'=>'3111127',
//        'firstcompany'=>'崔文博',
//        'total'=>'588'
//    ),
//    array(
//        'old_contract_no'=>'WK-16303805456868013',
//        'accountid'=>'3111084',
//        'firstcompany'=>'徐泽峰',
//        'total'=>'1628'
//    ),
//
//    array(
//        'old_contract_no'=>'WK-16306738155010296',
//        'accountid'=>'3129732',
//        'firstcompany'=>'陈皇男',
//        'total'=>'698'
//    ),
//    array(
//        'old_contract_no'=>'WK-16308267844348314',
//        'accountid'=>'3129748',
//        'firstcompany'=>'严丽',
//        'total'=>'588'
//    ),
//    array(
//        'old_contract_no'=>'WK-16312582555056959',
//        'accountid'=>'3129795',
//        'firstcompany'=>'杨小江',
//        'total'=>'588'
//    ),
//    array(
//        'old_contract_no'=>'WK-16299747467475392',
//        'accountid'=>'3111137',
//        'firstcompany'=>'丁军华',
//        'total'=>'588'
//    ),
//
//    array(
//        'old_contract_no'=>'WK-16294322595751192',
//        'accountid'=>'3111166',
//        'firstcompany'=>'戴均良',
//        'total'=>'588'
//    ),
//    array(
//        'old_contract_no'=>'WK-16294489408023146',
//        'accountid'=>'3111137',
//        'firstcompany'=>'刘丽蓉',
//        'total'=>'588'
//    ),
//);
$oldDatas = array(
//    array(
//        'old_contract_no'=>'WK-16308992816281268',
//        'accountid'=>'3147752',
//        'firstcompany'=>'李钢',
//        'total'=>'2280'
//    )  ,
//    array(
//        'old_contract_no'=>'WK-16322746336788379',
//        'accountid'=>'3147452',
//        'firstcompany'=>'王立恒',
//        'total'=>'588'
//    )  ,
//    array(
//        'old_contract_no'=>'WK-16316135723087528',
//        'accountid'=>'3147845',
//        'firstcompany'=>'位志',
//        'total'=>'588'
//    )  ,
//    array(
//        'old_contract_no'=>'WK-16316149738491899',
//        'accountid'=>'3111153',
//        'firstcompany'=>'杨圆',
//        'total'=>'2388'
//    )  ,
//    array(
//        'old_contract_no'=>'WK-16318679122586460',
//        'accountid'=>'3147870',
//        'firstcompany'=>'杨圆',
//        'total'=>'100'
//    )  ,
    array(
        'old_contract_no'=>'WK-16323775781182612',
        'accountid'=>'3149769',
        'firstcompany'=>'代华',
        'total'=>'588'
    )  ,
    array(
        'old_contract_no'=>'WK-16337445522724816',
        'accountid'=>'3147870',
        'firstcompany'=>'杨圆',
        'total'=>'550'
    )  ,
    array(
        'old_contract_no'=>'WK-16339209852963528',
        'accountid'=>'3178907',
        'firstcompany'=>'林传芳',
        'total'=>'1296'
    )
);
$companyInfo=array(
    "companyfullname"=>"上海珍岛网络科技有限公司",
    "company_code"=>"ZDWL",
);
$serviceYear=1;

$recordModel = ServiceContracts_Record_Model::getCleanInstance("ServiceContracts");
foreach ($oldDatas as $oldData){
    $result = $recordModel->createWkServiceContracts($companyInfo,$serviceYear,$oldData['firstcompany'],$oldData['total'],'wk001','新增','电子合同平台合同编号:'.$oldData['old_contract_no'],'papercontract');
    echo $result['contracts_no'].'----'.$oldData['old_contract_no'].'<br>';
}
