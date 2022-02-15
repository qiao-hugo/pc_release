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

/**
 * URL Verfication - Required to overcome Apache mis-configuration and leading to shared setup mode.
 */
include_once 'config.php';
if (file_exists('config_override.php')) {
	include_once 'config_override.php';
}

include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';

include_once('libraries/nusoap/nusoap.php');
include_once('modules/HelpDesk/HelpDesk.php');
include_once('modules/Emails/mail.php');
include_once 'modules/Users/Users.php';

ob_end_clean();
/** Configure language for server response translation */
global $default_language, $current_language;
if(!isset($current_language)) $current_language = $default_language;
/*
$userid = getPortalUserid();
$user = new Users();
$current_user = $user->retrieveCurrentUserInfoFromFile(458);
*/

$log = LoggerManager::getLogger('customerportal');

error_reporting(0);

$NAMESPACE = 'www.appcrm.com';
$server = new soap_server;

$server->configureWSDL('customerportal');

$server->wsdl->addComplexType(
	'common_array',
	'complexType',
	'array',
	'',
	array(
		'fieldname' => array('name'=>'fieldname','type'=>'xsd:string'),
	)
);

$server->wsdl->addComplexType(
	'common_array1',
	'complexType',
	'array',
	'',
	'SOAP-ENC:Array',
	array(),
	array(
		array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:common_array[]')
	),
	'tns:common_array'
);

$server->wsdl->addComplexType(
	'field_details_array',
	'complexType',
    'array',
    '',
	array(
    	'fieldlabel' => array('name'=>'fieldlabel','type'=>'xsd:string'),
        'fieldvalue' => array('name'=>'fieldvalue','type'=>'xsd:string'),
	)
);

$server->register(
	'authenticate_user',
	array('fieldname'=>'tns:common_array'),
	array('return'	 =>'tns:common_array'),
	$NAMESPACE);

$server->register(
	'change_password',
	array('fieldname'=>'tns:common_array'),
	array('return'=>'tns:common_array'),
	$NAMESPACE);

$server->register(
	'get_account_name',
	array('accountid'=>'xsd:string'),
	array('return'=>'tns:common_array'),
	$NAMESPACE);

$server->register(
        'get_check_account_id',
	array('id'=>'xsd:string'),
	array('return'=>'xsd:string'),
	$NAMESPACE);

$server->register(
		'get_modules',
		array(),
		array('return'=>'tns:field_details_array'),
		$NAMESPACE);


$server->register(
	'get_my_account',
	array('fieldname'=>'tns:common_array'),
	array('return'=>'tns:field_details_array'),
	$NAMESPACE);
$server->register(
	'get_VisitingOrder',
	array('fieldname'=>'tns:common_array'),
	array('return'=>'tns:field_details_array'),
	$NAMESPACE);

// 学校拜访单
$server->register(
	'get_Schoolvisit',
	array('fieldname'=>'tns:common_array'),
	array('return'=>'tns:field_details_array'),
	$NAMESPACE);

$server->register(
    'mobile_upload',
    array('fieldname'=>'tns:common_array'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
$server->register(
    'mobile_download',
    array('fieldname'=>'tns:common_array'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
$server->register(
    'contracts_photograph',
    array('fieldname'=>'tns:common_array'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
$server->register(
	'com_search_list',
	array('searchModule'=>'xsd:string','searchValue'=>'xsd:string','relatedModule'=>'xsd:string','userid'=>'xsd:string','isPermission'=>'xsd:boolean'),
	array('return'=>'tns:field_details_array'),
	$NAMESPACE);

$server->register(
	'get_account_msg',
	array('id'=>'xsd:string'),
	array('return'=>'tns:field_details_array'),
	$NAMESPACE);
$server->register(
    'get_address_msg',
    array('id'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
/**
 * 获取用户登陆信息
 */
$server->register(
    'getCompanySaleServiceInfo',
    array('fieldname'=>'tns:common_array'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
$server->register(
	'add_VisitingOrder',
	array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
	array('return'=>'tns:field_details_array'),
	$NAMESPACE);
/**
 * 保存的通用方法
 */
$server->register(
	'saveRecord',
	array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
	array('return'=>'tns:field_details_array'),
	$NAMESPACE);
$server->register(
	'tyunWebGetAccount',
	array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
	array('return'=>'tns:field_details_array'),
	$NAMESPACE);
/**
 * 二维码登陆
 */
$server->register(
    'qrcodelogin',
    array('fieldname'=>'tns:common_array'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
$server->register(
	'add_Schoolvisit',
	array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
	array('return'=>'tns:field_details_array'),
	$NAMESPACE);

$server->register(
	'add_contact',
	array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
	array('return'=>'tns:field_details_array'),
	$NAMESPACE);

$server->register(
	'add_accounts',
	array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
	array('return'=>'tns:field_details_array'),
	$NAMESPACE);
$server->register(
    'do_ExtensionTrialWorkflow',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
$server->register(
	'getAccountsDetail',
	array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
	array('return'=>'tns:field_details_array'),
	$NAMESPACE);
$server->register(
	'getList',
	array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
	array('return'=>'tns:field_details_array'),
	$NAMESPACE);
$server->register(
	'getUserDetail',
	array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
	array('return'=>'tns:field_details_array'),
	$NAMESPACE);
// 添加跟进提醒
$server->register(
    'addAlertData',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
// 添加评论
$server->register(
    'followComment',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
//得到用户组数据
$server->register(
    'getGroupUsers',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
$server->register(
	'addFollowInfo',
	array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
	array('return'=>'tns:field_details_array'),
	$NAMESPACE);
$server->register(
	'getAccounts',
	array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
	array('return'=>'tns:field_details_array'),
	$NAMESPACE);
$server->register(
    'get_ExtensionTrial',
	array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
	array('return'=>'tns:field_details_array'),
	$NAMESPACE);

$server->register(
		'findActivecode',
		array('fieldname'=>'tns:common_array'),
		array('return'=>'tns:field_details_array'),
		$NAMESPACE);

$server->register(
    'tyunContractChange',
    array('fieldname'=>'tns:common_array'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);

$server->register(
	'check_accountname',
	array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
	array('return'=>'tns:field_details_array'),
	$NAMESPACE);

$server->register(
	'addMod',
	array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
	array('return'=>'tns:field_details_array'),
	$NAMESPACE);

$server->register(
	'getContact',
	array('id'=>'xsd:string','userid'=>'xsd:string'),
	array('return'=>'tns:field_details_array'),
	$NAMESPACE);
/**
 * 由拜访单Id取客户id
 */
$server->register(
    'visitidgetContact',
    array('id'=>'xsd:string','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
$server->register(
    'doVisitingOrderRevoke',
    array('fieldname'=>'tns:common_array'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
$server->register(
    'dosign',
    array('fieldname'=>'tns:common_array'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);

$server->register(
    'dosignYz',
    array('fieldname'=>'tns:common_array'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
$server->register(
    'schoolvisitDosign',
    array('fieldname'=>'tns:common_array'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);

$server->register(
    'uppicture',
    array('fieldname'=>'tns:common_array','basic'=>'tns:common_array'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
$server->register(
    'add_WorkSummarize',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
$server->register(
    'get_WorkSummarize',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
$server->register(
	'getWorkFlowCheck',
	array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
	array('return'=>'tns:field_details_array'),
	$NAMESPACE);
// 移动端正在使用的待审核列表
$server->register(
    'getWorkFlowChecks',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
$server->register(
    'checkWorkSummarize',
    array('userid'=>'xsd:string'),
    array('return'=>'xsd:string'),
    $NAMESPACE);
$server->register(
    'get_record_detail',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
/**
 *延期审核详情
 */
$server->register(
    'oneExtensionTrial',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
$server->register(
    'do_VisitingOrderWorkflow',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);

//通知模块
$server->register(
    'get_NewList',
    array('fieldname'=>'tns:common_array'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);

//获取我的回款信息
$server->register(
    'get_my_receivepayment',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);

// 获取添加客户前的准备信息，例如客户属性等下拉的数据
$server->register(
    'getAddAccountReadyData',
    array('fieldname'=>'tns:common_array'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);


$server->register(
	'login_from_weixin',
	array('fieldname'=>'tns:common_array'),
	array('return'	 =>'tns:common_array'),
	$NAMESPACE);

$server->register(
    'login_from_cs',
    array('fieldname'=>'tns:common_array'),
    array('return'	 =>'tns:common_array'),
    $NAMESPACE);
//获取我部门的人（陪同人）
$server->register(
    'getDepartmentsUserByUserId',
    array('userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);

$server->register(
    'getTyunProductDownUp',
    array('userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
//获取客户账号信息
$server->register(
    'search_refillapplication_accountzh',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'	 =>'tns:common_array'),
	$NAMESPACE);
//获取客户对应客服的的信息
$server->register(
	'getCustomerService',
	array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'	 =>'tns:common_array'),
    $NAMESPACE);

//周报信息
$server->register(
    'getSalestagetInfo',
    array('userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
//当前负责人的客户及产品信息
$server->register(
    'getAccountsAndProduct',
    array('userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
//当前用户可成交的客户
$server->register(
    'getCandealAccounts',
    array('userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
//添加销售日报
$server->register(
    'addSalesDaily',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
//获取当日收款的客户
$server->register(

	'salesdailydaydeal',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
//添加 充值申请单
$server->register(
    'addRefillApplication',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
/*充值申请单详情*/
$server->register(
    'oneRefillApplication',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
/*供应商详情*/
$server->register(
    'oneVendors',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
/* 发票（新）详情 */
$server->register(
    'oneNewinvoice',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
/* 工单详情 */
$server->register(
    'oneSalesOrder',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
/*  超期录入详情审核  */
$server->register(
    'oneRefundTimeoutAudit',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
/*分成单详情*/
$server->register(
    'oneSeparateInto',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
/* 销售业绩汇总表 */
$server->register(
    'oneAchievementSummary',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
$server->register(
    'oneAchievementallotStatistic',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
/*  业绩日期设置  */
$server->register(
    'oneClosingDate',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
/*退款申请详情*/
$server->register(
    'oneOrderChargeback',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);

$server->register(
    'oneAccountPlatform',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
$server->register(
    'getApplicationAuthority',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
$server->register(
    'oneProductProvider',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
$server->register(
	'oneContractGuarantee',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
/**验证担保金几级审核**/
$server->register(
    'checkAuditInformation',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
/*查找所属部门*/
$server->register(
		'findParentDepartment',
		array('userid'=>'xsd:string'),
		array('return'=>'tns:field_details_array'),
		$NAMESPACE);


$server->register(
	'getagentid',
	array('userid'=>'xsd:string'),
	array('return'=>'tns:field_details_array'),
	$NAMESPACE);


$server->register(
		'sendMailAgent',
		array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
		array('return'=>'tns:field_details_array'),
		$NAMESPACE);
/**另购产品**/
$server->register(
    'getExtraProduct',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
//保存T云接口返回数据
$server->register(
    'saveTyunResposeData',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
/**合同超期提醒**/
$server->register(
    'getExtendedReminder',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);

/*回款*/
$server->register(
		'receiveReceivedPayments',
		array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
		array('return'=>'tns:field_details_array'),
		$NAMESPACE);

/*查找合同编号*/
$server->register(
		'findContractNo',
		array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
		array('return'=>'tns:field_details_array'),
		$NAMESPACE);

/*合同搜索*/
$server->register(
		'contractSearch',
		array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
		array('return'=>'tns:field_details_array'),
		$NAMESPACE);
/**搜索服务购买合同*/
$server->register(
    'searchTyunBuyServiceContract',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
$server->register(
    'checkUpgradeAndRenew',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
/*合同搜索根据客户ID*/
$server->register(
    'contractWithAccountid',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);

/*保存激活码信息*/
$server->register(
		'saveSecreCodeInfo',
		array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
		array('return'=>'tns:field_details_array'),
		$NAMESPACE);

/*保存激活码信息*/
$server->register(
    'upgradeAndRenew',
		array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
		array('return'=>'tns:field_details_array'),
		$NAMESPACE);
/*  申请编辑信息  */
$server->register(
    'applicationToModify',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
/*工作流审核*/
$server->register(
    'salesorderWorkflowStagesExamine',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
/*微信发送提醒*/
$server->register(
    'salesorderWorkflowStagesWx',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
/*工作流打回*/
$server->register(
    'salesorderWorkflowStagesRepulse',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
// 校招简历添加
$server->register(
    'addSchoolresumeInfo',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);

// 销售日报列表
$server->register(
    'get_SalesDailyList',
	array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
	array('return'=>'tns:field_details_array'),
$NAMESPACE);

$server->register(
    'getRefillApplication',
	array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
//获取媒体账户的信息
$server->register(
    'getAccountPlatformList',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
//获取媒体账号外采信息
$server->register(
    'getProductProviderList',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);



$server->register(
    'getUserRelativeUserList',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);

$server->register(
    'searchSchool',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
$server->register(
    'getSchoolMsg',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);


$server->register(
    'get_SalesDailyOtherData',
	array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
	array('return'=>'tns:field_details_array'),
$NAMESPACE);
$server->register(
    'addApproval',
	array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
	array('return'=>'tns:field_details_array'),
$NAMESPACE);

$server->register(
    'search_servicecontracts',
	array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
	array('return'=>'tns:field_details_array'),
$NAMESPACE);
$server->register(
    'refill_application_topplatform',
	array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
	array('return'=>'tns:field_details_array'),
$NAMESPACE);

$server->register(
    'getOneSalesDaily',
	array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
	array('return'=>'tns:field_details_array'),
$NAMESPACE);


// 服务合同
$server->register(
    'getServiceContracts',
	array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
	array('return'=>'tns:field_details_array'),
$NAMESPACE);
//发票新
$server->register(
    'getNewinvoice',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
$server->register(
    'oneServiceContracts',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
//服务合同补充协议
$server->register(
    'getContractsAgreement',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
/**
 * 通用列表获取接口
 */
$server->register(
    'getComListImplements',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
//通用的recordModule调用
$server->register(
    'getComRecordModule',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
$server->register(
    'getAccountContent',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
$server->register(
    'oneContractsAgreement',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
//采购合同
$server->register(
    'getSupplierContracts',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
$server->register(
    'oneSupplierContracts',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);

//采购合同补充
$server->register(
    'getSuppContractsAgreement',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
//通用方法调用执行
$server->register(
	'do_com_recordmodel',
	array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
	array('return'=>'tns:field_details_array'),
	$NAMESPACE);
$server->register(
    'oneSuppContractsAgreement',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
//建站合同状态回传 接口  gaocl add 2018/04/08
$server->register(
    'updateTyunStationSale',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
//建站合同服务进度 接口  gaocl add 2018/05/03
$server->register(
    'saveStationServiceProgress',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
//建站服务购买数据保存  gaocl add 2018/04/09
$server->register(
    'saveTyunStationSale',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
//建站服务购发送邮件  gaocl add 2018/04/23
$server->register(
    'sendMailTyunStationSale',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);

//搜索供应商 gaocl add 2019/04/19
$server->register(
    'search_vendors',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
//搜索供应商合同 gaocl add 2019/04/19
$server->register(
    'search_vendors_servicecontracts',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
//搜索平台账户 gaocl add 2019/04/19
$server->register(
    'search_accountplatform',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
$server->register(
    'search_vendor_productservice',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);

//搜索回款 gaocl add 2019/04/21
$server->register(
    'search_receivedpayments',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
/**
 * 建站购买续费
 */
$server->register(
	'saveStationRenew',
	array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
	array('return'=>'tns:field_details_array'),
	$NAMESPACE);
//查询T云购买信息
$server->register(
    'searchTyunBuyServiceInfo',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
//查询T云升级产品
$server->register(
    'searchTyunUpgradeProduct',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
//查询T云另购产品
$server->register(
    'getTyunServiceItem',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);

$server->register(
    'checkTyunExistBuy',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);

$server->register(
    'getUserRole',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
//下单员工发送需要审核提醒
$server->register(
    'sendElecContractVerifyEmail',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);

//获取主体公司要替换的信息
$server->register(
    'getMainPartInfo',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);

$server->register(
    'checkTyunIsPay',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
$server->register(
    'getIntentionality',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
$server->register(
    'salesDailyAccountStatistics',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);

$server->register(
    'doVisitingOrderCancel',
    array('fieldname'=>'tns:common_array'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);

/**
 * Helper class to provide functionality like caching etc...
 */
class Vtiger_Soap_CustomerPortal {

	/** Preference value caching */
	static $_prefs_cache = array();
	static function lookupPrefValue($key) {
		if(self::$_prefs_cache[$key]) {
			return self::$_prefs_cache[$key];
		}
		return false;
	}
	static function updatePrefValue($key, $value) {
		self::$_prefs_cache[$key] = $value;
	}

	/** Sessionid caching for re-use */
	static $_sessionid = array();
	static function lookupSessionId($key) {
		if(isset(self::$_sessionid[$key])) {
			return self::$_sessionid[$key];
		}
		return false;
	}
	static function updateSessionId($key, $value) {
		self::$_sessionid[$key] = $value;
	}

	/** Store available module information */
	static $_modules = false;
	static function lookupAllowedModules() {
		return self::$_modules;
	}
	static function updateAllowedModules($modules) {
		self::$_modules = $modules;
	}

}
/***微信登录***/
function login_from_weixin($email,$sessionid,$login='true'){
	    global $adb,$log;
		//$sql  = "SELECT *  FROM `vtiger_users` WHERE status='Active' AND email1='$email'";
	    //$query = $adb->pquery("SELECT *  FROM `vtiger_users` WHERE `status`='Active' AND email1='$email' limit 1");
	    $query_sql = "SELECT vtiger_users.*,vtiger_user2role.roleid,vtiger_role.rolename,
            	vtiger_user2department.departmentid,vtiger_departments.departmentname from vtiger_users
                LEFT JOIN vtiger_user2department ON(vtiger_users.id=vtiger_user2department.userid)
                LEFT JOIN vtiger_user2role ON(vtiger_users.id=vtiger_user2role.userid)
                LEFT JOIN vtiger_role ON(vtiger_user2role.roleid=vtiger_role.roleid)
                LEFT JOIN vtiger_departments ON(vtiger_user2department.departmentid=vtiger_departments.departmentid)
                WHERE vtiger_users.`status`='Active' AND vtiger_users.isdimission=0 AND vtiger_users.email1=? limit 1";
        $result_users=$adb->pquery($query_sql,array($email));
		$norows = $adb->num_rows($result_users);
		$result = array();

		$err[0]['err1'] = "MORE_THAN_ONE_USER";
		$err[1]['err1'] = "INVALID_USERNAME_OR_PASSWORD";

		if($norows) {
			while($resultrow = $adb->fetchByAssoc($result_users)) {
				$result = $resultrow;
			}
		}else{
			return $err[1];
		}


		$list[0]['id'] 					= $result['id'];
		$list[0]['user_name'] 			= $result['user_name'];
		$list[0]['user_password'] 		= $result['user_password'];
		$list[0]['last_name'] 			= $result['last_name'];
        $list[0]['phone_mobile'] 			= $result['phone_mobile'];
        $list[0]['reports_to_id'] 			= $result['reports_to_id'];
		$list[0]['roleid'] 			= $result['roleid'];
        $list[0]['departmentname'] 			= $result['departmentname'];
		$list[0]['rolename'] 			= $result['rolename'];
        $list[0]['departmentid'] 			= $result['departmentid'];
        $list[0]['watertext']=$result['last_name'].($result['usercode']+0);
		insertWeixinSessionID($result['id'],$sessionid);
		if($login != 'false')
		{
			$sessionid = makeRandomPassword();
			$sql="insert into vtiger_soapservice values(?,?,?)";
			$result = $adb->pquery($sql, array($result['id'],'customer' ,$sessionid));
			$list[0]['sessionid'] = $sessionid;
		}
		return $list;
}

/***客服系统登录***/
function login_from_cs($userId,$login='true'){
    global $adb,$log;
    //$sql  = "SELECT *  FROM `vtiger_users` WHERE status='Active' AND email1='$email'";
    //$query = $adb->pquery("SELECT *  FROM `vtiger_users` WHERE `status`='Active' AND email1='$email' limit 1");
    $query_sql = "SELECT vtiger_users.*,vtiger_user2role.roleid,vtiger_role.rolename,
            	vtiger_user2department.departmentid,vtiger_departments.departmentname from vtiger_users
                LEFT JOIN vtiger_user2department ON(vtiger_users.id=vtiger_user2department.userid)
                LEFT JOIN vtiger_user2role ON(vtiger_users.id=vtiger_user2role.userid)
		LEFT JOIN vtiger_role ON(vtiger_role.roleid=vtiger_user2role.roleid)
                LEFT JOIN vtiger_departments ON(vtiger_user2department.departmentid=vtiger_departments.departmentid)
                WHERE vtiger_users.`status`='Active' AND vtiger_users.isdimission=0 AND vtiger_users.id=? limit 1";
    $result_users=$adb->pquery($query_sql,array($userId));
    $norows = $adb->num_rows($result_users);
    $result = array();

    $err[0]['err1'] = "MORE_THAN_ONE_USER";
    $err[1]['err1'] = "INVALID_USERNAME_OR_PASSWORD";

    if($norows) {
        while($resultrow = $adb->fetchByAssoc($result_users)) {
            $result = $resultrow;
        }
    }else{
        return $err[1];
    }


    $list[0]['id'] 					= $result['id'];
    $list[0]['user_name'] 			= $result['user_name'];
    $list[0]['user_password'] 		= $result['user_password'];
    $list[0]['last_name'] 			= $result['last_name'];
    $list[0]['phone_mobile'] 			= $result['phone_mobile'];
	$list[0]['reports_to_id'] 			= $result['reports_to_id'];
    $list[0]['roleid'] 			= $result['roleid'];
    $list[0]['departmentname'] 			= $result['departmentname'];
	$list[0]['rolename'] 			= $result['rolename'];
    $list[0]['departmentid'] 			= $result['departmentid'];

    if($login != 'false')
    {
        $sessionid = makeRandomPassword();
        $sql="insert into vtiger_soapservice values(?,?,?)";
        $result = $adb->pquery($sql, array($result['id'],'customer' ,$sessionid));
        $list[0]['sessionid'] = $sessionid;
    }
    return $list;
}

/**	function used to authenticate whether the customer has access or not
 *	@param string $username - customer name for the customer portal
 *	@param string $password - password for the customer portal
 *	@param string $login - true or false. If true means function has been called for login process and we have to clear the session if any, false means not called during login and we should not unset the previous sessions
 *	return array $list - returns array with all the customer details
 */

function authenticate_user($username,$password,$sessionid,$version,$login = 'true')
{
	$tt = $password;
	global $adb,$log;
	//$adb->println("Inside customer portal function authenticate_user($username, $password, $login).");
	include('vtigerversion.php');
	if(version_compare($version,'5.1.0','>=') == 0){
		$list[0] = "NOT COMPATIBLE";
  		return $list;
	}

		$username 	= $adb->sql_escape_string($username);
		$password 	= $adb->sql_escape_string($password);
		$old_password = $password;
		//$password 	= str_replace(md5(getip()),'',$password);
        $password 	= substr($password,32,strlen($password)-32);
		$password 	= str_split($password,2);

		$password 	= '%'.implode('%',$password);

		$password 	= urldecode($password);

		$user = new Users();
		$user->column_fields['user_name'] = $username;

		$err[0]['err1'] = "MORE_THAN_ONE_USER";
		$err[1]['err1'] = "INVALID_USERNAME_OR_PASSWORD";

		if ($user->doLogin($password)) {

			$userinfo  	= $user->retrieve_user_info($username);
			$userid 	= $userinfo['id'];
			$list[0]['id'] 					= $userid;
			$list[0]['user_name'] 			= $username;
			$list[0]['user_password'] 		= $old_password;
			$list[0]['last_name'] 			= $userinfo['last_name'];
            $list[0]['email1'] 			= $userinfo['email1'];
			$list[0]['reports_to_id'] 			= $userinfo['reports_to_id'];
			$list[0]['departmentid'] 			= $userinfo['departmentid'];
			$list[0]['phone_mobile']		= $userinfo['phone_mobile'];//在移动端使用，获取手机号码
			$list[0]['roleid']		= $userinfo['roleid'];
            $list[0]['departmentname']		= $userinfo['departmentname'];
            $list[0]['departmentid']		= $userinfo['departmentid'];
            $list[0]['watertext']=$userinfo['last_name'].($userinfo['usercode']+0);
		}else{
			return $err[1];
		}
		insertWeixinSessionID($userid,$sessionid);
		if($login != 'false')
		{

			$sessionid = makeRandomPassword();
			$sql="insert into vtiger_soapservice values(?,?,?)";
			$result = $adb->pquery($sql, array($userid,'customer' ,$sessionid));
			$list[0]['sessionid'] = $sessionid;
		}

	return $list;
}
function insertWeixinSessionID($userid,$sessionid){
	global $adb;
	$sql="DELETE FROM vtiger_weixinclientsession WHERE userid=?";
	$adb->pquery($sql, array($userid));
	$sql='INSERT INTO vtiger_weixinclientsession (userid,sessionid) VALUES(?,?)';
	$adb->pquery($sql, array($userid,$sessionid));
}




/**	Function used to get the session id which was set during login time
 *	@param int $id - contact id to which we want the session id
 *	return string $sessionid - return the session id for the customer which is a random alphanumeric character string
 **/
function getServerSessionId($id)
{
	global $adb;
	$adb->println("Inside the function getServerSessionId($id)");

	//To avoid SQL injection we are type casting as well as bound the id variable. In each and every function we will call this function
	$id = (int) $id;

	$sessionid = Vtiger_Soap_CustomerPortal::lookupSessionId($id);
	if($sessionid === false) {
		$query = "select * from vtiger_soapservice where type='customer' and id=?";
		$result = $adb->pquery($query, array($id));
		if($adb->num_rows($result) > 0) {
			$sessionid = $adb->query_result($result,0,'sessionid');
			Vtiger_Soap_CustomerPortal::updateSessionId($id, $sessionid);
		}
	}
	return $sessionid;
}

/**	function used to get the Account name
 *	@param int $id - Account id
 *	return string $message - Account name returned
 */
function get_account_name($accountid)
{
	global $adb,$log;



	//$log->debug("Entering customer portal function get_account_name");
	$res = $adb->pquery("select * from vtiger_account where accountid=?", array($accountid));
	$accountname=$adb->query_result($res,0,'accountname');
    return array($accountname);
	//$log->debug("Exiting customer portal function get_account_name");


	$user = new Users();
	$userid = getPortalUserid();
	$current_user = $user->retrieveCurrentUserInfoFromFile($userid);

	//return $user->getColumnNames_User();

	return array($accountname);
}


#搜索通用方法
function com_search_list($searchModule,$searchValue,$relatedModule,$userid,$isPermission=false){

	global $current_user;
    global $currentView;
    global $currentModule;
    $currentModule=$searchModule;
    $currentView='List';
	//$userid = getPortalUserid();
	$user = new Users();
	$current_user = $user->retrieveCurrentUserInfoFromFile($userid);
	include_once('modules/Vtiger/Models/Module.php');
	$searchModuleModel = Vtiger_Module_Model::getInstance($searchModule);
	//$records = $searchModuleModel->searchRecord($searchValue, '', '', $relatedModule);
	$records = $searchModuleModel->searchRecordAPP($searchValue, '', '', $relatedModule,$isPermission);

	$result = array();
	foreach($records as $moduleName=>$recordModels) {
		foreach($recordModels as $recordModel) {
			$result[] = array('label'=>decode_html($recordModel->getName()), 'value'=>decode_html($recordModel->getName()), 'id'=>$recordModel->getId());
		}
	}

	return $result;
}
#获取单个用户的资料
function get_account_msg($id){
		global  $adb;
		$query = $adb->pquery('SELECT vtiger_account.*,IFNULL((SELECT CONCAT(last_name,\'[\',IFNULL((SELECT departmentname	FROM vtiger_departments	WHERE departmentid =(SELECT departmentid FROM vtiger_user2department WHERE userid =vtiger_users.id LIMIT 1)),\'\'),\']\',(IF (`status` = \'Active\',\'\',\'[离职]\'))) AS last_name FROM vtiger_users WHERE vtiger_crmentity.smownerid = vtiger_users.id	), \'--\') AS username,vtiger_crmentity.smownerid as userid  FROM `vtiger_account` LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_account.accountid WHERE accountid=? limit 1',array($id));
		$norows = $adb->num_rows($query);
		$result = array();
		if($norows) {
			while($resultrow = $adb->fetch_array($query)) {
				$result[] = $resultrow;
			}
		}
		return $result;
}
#获取单个用户的资料
function get_address_msg($id){
    include_once('includes/http/Request.php');
    include_once('modules/VisitingOrder/actions/SaveAjax.php');
    $visitingOrderSaveAjax = new VisitingOrder_SaveAjax_Action();
    $fieldname['accountid']=$id;
    $res  = $visitingOrderSaveAjax->getSignedAddress(new Vtiger_Request($fieldname, $fieldname));
    return array($res);

}
#获取客户联系人
function getContact($id,$userid){

		global $current_user,$currentAction;

		$user = new Users();
		$current_user = $user->retrieveCurrentUserInfoFromFile($userid);
		$currentAction = 'DetailView';
		$result = array();
		include_once('modules/Vtiger/Models/Record.php');
		$recordModel = Vtiger_Record_Model::getInstanceById($id, 'Accounts');
		$moduleModel = $recordModel->getModule();
		$entity		 = $recordModel->entity->column_fields;

		$result[] = array('contactid'=>$id,'name'=>$entity['linkname']);
		include_once('modules/Accounts/Models/Record.php');
		$allcontacts = Accounts_Record_Model::getContactsToIndex($id);
		if(!empty($allcontacts)){
			foreach ($allcontacts as $key => $value) {

				$result[] = array('contactid'=>$value['contactid'],'name'=>$value['name']);
			}

		}
		return $result;
}

/**
 * 根据拜访单ID找联系人
 * @param $id
 * @param $userid
 * @return array
 */
function visitidgetContact($id,$userid){
    global $current_user,$currentAction;

    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
    $currentAction = 'DetailView';
    $result = array();
    include_once('modules/Vtiger/Models/Record.php');
    $recordModel = Vtiger_Record_Model::getInstanceById($id, 'VisitingOrder');
    $moduleModel = $recordModel->getModule();
    $entity		 = $recordModel->entity->column_fields;
	if(!empty($entity['related_to'])){
		$result['id']=$entity['related_to'];
		$result['data']=getContact($entity['related_to'],$userid);
	}else{
		$result=array('noaccount');
	}
    return $result;
}
function do_com_recordmodel($fieldname){
	global $current_user;
	$module = $fieldname['module'];
	$action=$fieldname['action'];
	$user = new Users();
	$current_user = $user->retrieveCurrentUserInfoFromFile($fieldname['userid']);
	$recordModel=Vtiger_Record_Model::getCleanInstance($module);
	return array($recordModel->$action($fieldname));
}
#获取通用列表数据
function get_workFlowCheck_list($module,$fieldname){
    file_put_contents('files.txt',$fieldname['public']);
    define('TABLEPIX','vtiger_');
    include('modules/WorkFlowCheck/Models/ListView.php');
    include('modules/Vtiger/Models/Paging.php');
    global $current_user,$currentModule, $currentAction;
    $_REQUEST['public']=$fieldname['public'];
    $currentAction = 'DetailView';
    $currentModule = $module;
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($fieldname['userid']);
    $listViewModel 	= Vtiger_ListView_Model::getInstance($module, 0);
    $pagingModel 	= new Vtiger_Paging_Model();   	//分页
    $pagenum 	= 1;
    if (! empty($fieldname['pagenum'])) {
        $pagenum = $fieldname['pagenum'];
    }

    $pagecount 	= 10;
    if(isset($fieldname['pagenum'])){
        $pagenum 	= $fieldname['pagenum'];
    }
    if(isset($fieldname['pagecount'])){
        $pagecount 	= $fieldname['pagecount'];
    }
    $searchField='';
    if(isset($fieldname['searchField'])){
        $searchField['BugFreeQuery'] = $fieldname['searchField'];
        $_REQUEST['BugFreeQuery'] =  $fieldname['searchField'];
    }
    if(isset($fieldname['filter'])){
        $searchField['filter'] = $fieldname['filter'];
    }
    if(isset($fieldname['public'])){
        $searchField['public'] = $fieldname['public'];
    }
    if(isset($fieldname['src_module'])){
        $_REQUEST['src_module'] = $fieldname['src_module'];
        $listViewModel->set('src_module',$fieldname['src_module']);
    }



    $pagingModel->set('page', $pagenum);
    $pagingModel->set('limit', $pagecount);			//每页显示数量
    //ob_start();

    if(isset($fieldname['search_key'])){
        $listViewModel->set('search_key', $fieldname['search_key']);
        $listViewModel->set('search_value', $fieldname['search_value']);
    }
	$listViewModel->isAllCount=1;
    $entries 		= $listViewModel->getListViewEntries($pagingModel,$searchField);
    $counts 		= $listViewModel->getListViewCount($searchField);

    /*$tt  = ob_get_contents();
    ob_end_clean();
    return array($tt);*/
    $pageTotal = ceil($counts / $pagecount);
    return array('sum'=>$pageTotal, 'list'=>$entries,'count'=>count($entries));
}
#获取通用列表数据
function get_com_list($module,$fieldname){
    define('TABLEPIX','vtiger_');
    include('modules/Vtiger/Models/ListView.php');
    include('modules/Vtiger/Models/Paging.php');
	global $current_user,$currentModule, $currentAction;
	$currentAction = 'DetailView';
	$currentModule = $module;
	$user = new Users();
	$current_user = $user->retrieveCurrentUserInfoFromFile($fieldname['userid']);
    $listViewModel 	= Vtiger_ListView_Model::getInstance($module, 0);
    $pagingModel 	= new Vtiger_Paging_Model();   	//分页
	$pagenum 	= 1;
	if (! empty($fieldname['pagenum'])) {
		$pagenum = $fieldname['pagenum'];
	}

	$pagecount 	= 10;
	if(isset($fieldname['pagenum'])){
		$pagenum 	= $fieldname['pagenum'];
	}
	if(isset($fieldname['pagecount'])){
		$pagecount 	= $fieldname['pagecount'];
	}
	$searchField='';
	if(isset($fieldname['searchField'])){
		$searchField['BugFreeQuery'] = $fieldname['searchField'];
                $_REQUEST['BugFreeQuery'] =  $fieldname['searchField'];
	}
	if(isset($fieldname['filter'])){
		$searchField['filter'] = $fieldname['filter'];
	}
	if(isset($fieldname['public'])){
		$searchField['public'] = $fieldname['public'];
	}
	if(isset($fieldname['modulestatus'])){
		if($fieldname['modulestatus'] == 'check'){
			$searchField['vtiger_refillapplication.modulestatus'] = 'checking';
		}
	}
    if(isset($fieldname['src_module'])){
        $_REQUEST['src_module'] = $fieldname['src_module'];
        $listViewModel->set('src_module',$fieldname['src_module']);
	}

    if(isset($fieldname['orderby'])){
        $_REQUEST['orderby'] = $fieldname['orderby'];
        $listViewModel->set('orderby',$fieldname['orderby']);
    }

    if(isset($fieldname['sortorder'])){
        $_REQUEST['sortorder'] = $fieldname['sortorder'];
        $listViewModel->set('sortorder',$fieldname['sortorder']);
    }

    $pagingModel->set('page', $pagenum);
    $pagingModel->set('limit', $pagecount);			//每页显示数量
    //ob_start();

	if(isset($fieldname['search_key'])){
		$listViewModel->set('search_key', $fieldname['search_key']);
		$listViewModel->set('search_value', $fieldname['search_value']);
	}
	$listViewModel->isFromMobile=1;//来自移动端
	$entries 		= $listViewModel->getListViewEntries($pagingModel,$searchField);
	$counts=0;
	if(!in_array($module,array('VisitingOrder','RefillApplication','Accounts')) || $fieldname['showTotal']==1){
		$counts 		= $listViewModel->getListViewCount($searchField);
	}
	/*$tt  = ob_get_contents();
	ob_end_clean();
	return array($tt);*/

	$pageTotal = ceil($counts / $pagecount);
	return array('sum'=>$pageTotal, 'list'=>$entries,'total'=>$counts);
}

#获取到款通知的列表
function get_my_receivepayment($fieldname, $userid){
    global $adb;


    $date=date('Y-m-d');

    $total = 0;
    $pageCount = 10;

    $sql = "SELECT count(*) AS count from  vtiger_receivedpayments LEFT JOIN vtiger_servicecontracts ON maybe_account = sc_related_to LEFT JOIN vtiger_crmentity ON maybe_account = crmid WHERE relatetoid =0 AND vtiger_servicecontracts.modulestatus='c_complete' AND receiveid>1 AND receiveid = ? AND maybe_account>0 AND receivedpaymentsid NOT IN ( SELECT DISTINCT receivepaymentid FROM `vtiger_receivedpayments_throw` WHERE userid>1 AND userid = ? )";
    $res = $adb->pquery($sql, array($userid,$userid));
    $row = $adb->query_result_rowdata($res, 0);
    $total = $row['count'];
    $pageTotal = ceil($total / $pageCount);

    $pagenum = $fieldname['pagenum'];
    $start = ($pagenum - 1) * $pageCount;

    $sql = "SELECT label, reality_date, CONCAT( currencytype, ':', unit_price ) AS price FROM vtiger_receivedpayments LEFT JOIN vtiger_servicecontracts ON maybe_account = sc_related_to LEFT JOIN vtiger_crmentity ON maybe_account = crmid WHERE relatetoid =0 AND vtiger_servicecontracts.modulestatus='c_complete' AND receiveid>1 AND receiveid = ? AND maybe_account>0 AND receivedpaymentsid NOT IN ( SELECT DISTINCT receivepaymentid FROM `vtiger_receivedpayments_throw` WHERE userid>1 AND userid = ? ) LIMIT $start, $pageCount";
    $res = $adb->pquery($sql, array($userid,$userid));
    $data = array();
    if($adb->num_rows($res)>0){
        for($i=0;$i<$adb->num_rows($res);$i++){
            $data[] = $adb->fetchByAssoc($res,$i);
        }
    }


    /*
	// 测试用的代码，sql里面没有 receiveid = ?
    $date=date('Y-m-d');
    $total = 0;
    $pageCount = 2;

    $sql = "SELECT count(*) AS count from vtiger_receivedpayments LEFT JOIN vtiger_servicecontracts ON maybe_account = sc_related_to LEFT JOIN vtiger_crmentity ON maybe_account = crmid WHERE relatetoid = '' AND maybe_account != '' AND receivedpaymentsid NOT IN ( SELECT DISTINCT receivepaymentid FROM `vtiger_ReceivedPayments_throw` WHERE userid = ? )";
    $res = $adb->pquery($sql, array($userid));
    $row = $adb->query_result_rowdata($res, 0);
    $total = $row['count'];
    $pageTotal = ceil($total / $pageCount);

    $pagenum = $fieldname['pagenum'];
    $start = ($pagenum - 1) * $pageCount;

    $sql = "SELECT label, reality_date, CONCAT( currencytype, ':', unit_price ) AS price FROM vtiger_receivedpayments LEFT JOIN vtiger_servicecontracts ON maybe_account = sc_related_to LEFT JOIN vtiger_crmentity ON maybe_account = crmid WHERE relatetoid = '' AND maybe_account != '' AND receivedpaymentsid NOT IN ( SELECT DISTINCT receivepaymentid FROM `vtiger_ReceivedPayments_throw` WHERE userid = ? ) LIMIT $start, $pageCount";
    $res = $adb->pquery($sql, array($userid,$userid));
    $data = array();
    if($adb->num_rows($res)>0){
        for($i=0;$i<$adb->num_rows($res);$i++){
            $data[] = $adb->fetchByAssoc($res,$i);
        }
    }*/

    return array($pageTotal, $data);
}
#获取我的客户列表
function get_my_account($fieldname){
	return get_com_list('Accounts',$fieldname);

}

#获取拜访客户列表
function get_VisitingOrder($fieldname){
	return get_com_list('VisitingOrder',$fieldname);
}

#获取学校拜访单
function get_Schoolvisit($fieldname){
	return get_com_list('Schoolvisit',$fieldname);
}

#添加联系人
function add_contact($fieldname,$userid){

	global $adb,$log,$current_user;

	$user = new Users();
	$current_user = $user->retrieveCurrentUserInfoFromFile($userid);
	include_once('includes/http/Request.php');
	include_once('modules/Vtiger/actions/Save.php');
	$save = new Vtiger_Save_Action();
	$res  = $save->saveRecord(new Vtiger_Request($fieldname, $fieldname));
	$order_id = $res->getId();
	return array($order_id);
}
#添加跟进
function addMod($fieldname,$userid){
	global $adb,$log,$current_user,$currentModule;
	$currentModule = 'ModComments';

	$user = new Users();
	$current_user = $user->retrieveCurrentUserInfoFromFile($userid);
	include_once('includes/http/Request.php');
	include_once('modules/ModComments/actions/SaveAjax.php');
	$save = new ModComments_SaveAjax_Action();
	$res  = $save->process(new Vtiger_Request($fieldname, $fieldname),true);
	return $res;
}


#添加签到信息
function schoolvisitDosign($fieldname){
    global $adb,$log,$currentModule;
    $arr =  array_values($fieldname);
    $arr[] = $fieldname['userid'];
	//只有提单人签到才算签到
    /*$sql = 'UPDATE vtiger_schoolvisitsign SET signid = ? , signaddress = ?,signaddcode = ?,signtime=?,issign=? WHERE visitingorderid=? AND extractid=?';
    $adb->pquery($sql, array($arr));
    //不管是提单人或是陪同人只要有签到就算该拜访单已签到
    $sql = 'UPDATE vtiger_schoolvisitsign SET issign=1 WHERE visitingorderid=?';
    $adb->pquery($sql, array($fieldname['visid']));
*/

    // 更改签到详情表的数据
    // 判断是第几次签到
    $signnum = 1;
    $sql = "select * from vtiger_schoolvisitsign WHERE visitingorderid=? AND userid=? AND signnum=1 AND issign=1";
    $sel_result = $adb->pquery($sql, array($fieldname['visid'], $fieldname['userid']));
    $res_cnt = $adb->num_rows($sel_result);
    if ($res_cnt > 0)  {
    	$signnum = 2;
    } else {
    	$signnum = 1;
    }
    $sql = "UPDATE vtiger_schoolvisitsign set coordinate=?, signtime=?, signaddress=?, issign=? WHERE visitingorderid=? AND userid=? AND signnum=?";
    $adb->pquery($sql, array($fieldname['adcode'], $fieldname['time'], $fieldname['adname'], '1',$fieldname['visid'], $fieldname['userid'], $signnum));

}
//验证是否是签到人
function dosignYz($fieldname){
    global $adb;
    // 查询是否存在
    $ishas = "select * from vtiger_visitsign WHERE visitingorderid=? AND userid=? ";
    $ishas_result = $adb->pquery($ishas, array($fieldname['visid'], $fieldname['userid']));
    $ishas_result = $adb->num_rows($ishas_result);
    $data = array();
    if($ishas_result>0){
        $sql2 = "select signnum from vtiger_visitingorder WHERE visitingorderid=?";
        $res = $adb->pquery($sql2,array($fieldname['visid']));
        if($adb->num_rows($res)){
            $signnum = 0;
            while($row=$adb->fetch_array($res)){
                $signnum = $row['signnum'];
            }
            $sql = 'UPDATE vtiger_visitingorder SET issign=1,signnum=? WHERE visitingorderid=?';
            $adb->pquery($sql, array((1+intval($signnum)),$fieldname['visid']));
        }else{
            $sql = 'UPDATE vtiger_visitingorder SET issign=1,signnum=1 WHERE visitingorderid=?';
            $adb->pquery($sql, array($fieldname['visid']));
        }

        $data['result']['success']=true;
	}else{
        $data['result']['success']=false;
	}
    return array($data);
}
#添加签到信息
function dosign($fieldname){
    global $adb,$log,$currentModule;
    $arr =  array_values($fieldname);
    $arr[] = $fieldname['userid'];
    // 更改签到详情表的数据
    // 判断是第几次签到
    $signnum = 1;
    $sql = "select * from vtiger_visitsign WHERE visitingorderid=? AND userid=? AND signnum=1 AND issign=1";
    $sel_result = $adb->pquery($sql, array($fieldname['visid'], $fieldname['userid']));
    $res_cnt = $adb->num_rows($sel_result);
    if ($res_cnt > 0)  {
    	$signnum = 2;
    } else {
    	$signnum = 1;
    }
    $sql = "UPDATE vtiger_visitsign set coordinate=?, signtime=?, signaddress=?, issign=?,unusualsign=?,unusualremark=?,file=? WHERE visitingorderid=? AND userid=? AND signnum=?";
    $adb->pquery($sql, array($fieldname['adcode'], $fieldname['time'], $fieldname['adname'], '1',$fieldname['unusualsign'],$fieldname['unusualremark'],$fieldname['file'],$fieldname['visid'], $fieldname['userid'], $signnum));
	$RecordModel=Vtiger_Record_Model::getCleanInstance('VisitingOrder');
    if($signnum==2){
		$query='SELECT visitsignid,visitsigntype,signnum FROM vtiger_visitsign_mulit WHERE visitingorderid=? AND userid=? AND issign=1 ORDER BY signnum DESC LIMIT 1';
		$result=$adb->pquery($query,array($fieldname['visid'], $fieldname['userid']));
		if($adb->num_rows($result)){
			$returndata=$adb->query_result_rowdata($result,0);
			$Tsignnum=$returndata['signnum'];
			if($Tsignnum==1){
				$sql = "UPDATE vtiger_visitsign_mulit set coordinate=?, signtime=?, signaddress=?, issign=?,zhsignnum=?,unusualsign=?,unusualremark=?,file=? WHERE visitingorderid=? AND userid=? AND signnum=?";
				$adb->pquery($sql, array($fieldname['adcode'], $fieldname['time'], $fieldname['adname'], '1','二',$fieldname['unusualsign'],$fieldname['unusualremark'],$fieldname['file'],$fieldname['visid'], $fieldname['userid'], $signnum));
			}else{
				$Tsignnum+=1;
				$signnuZh=$RecordModel->number2chinese((int)$Tsignnum);
				$sql='INSERT INTO vtiger_visitsign_mulit(coordinate,visitingorderid,userid,visitsigntype,signtime,signaddress,issign,signnum,zhsignnum,unusualsign,unusualremark,file) values(?,?,?,?,?,?,?,?,?,?,?,?)';
				$adb->pquery($sql, array($fieldname['adcode'],$fieldname['visid'],$fieldname['userid'],$returndata['visitsigntype'], $fieldname['time'], $fieldname['adname'], 1,$Tsignnum,$signnuZh,$fieldname['unusualsign'],$fieldname['unusualremark'],$fieldname['file'],));
			}
		}
	}else{
		$sql = "UPDATE vtiger_visitsign_mulit set coordinate=?, signtime=?, signaddress=?, issign=?,zhsignnum=?,unusualsign=?,unusualremark=?,file=? WHERE visitingorderid=? AND userid=? AND signnum=?";
		$adb->pquery($sql, array($fieldname['adcode'], $fieldname['time'], $fieldname['adname'], '1','一',$fieldname['unusualsign'],$fieldname['unusualremark'],$fieldname['file'],$fieldname['visid'], $fieldname['userid'], $signnum));
	}

	//只有提单人签到才算签到
	$sql = 'UPDATE vtiger_visitingorder SET signid = ? , signaddress = ?,signaddcode = ?,signtime=?,issign=? WHERE visitingorderid=? AND extractid=?';
	$adb->pquery($sql, array($fieldname['userid'],$fieldname['adname'],$fieldname['adcode'],date("Y-m-d H:i:s"),1,$fieldname['visid'],$fieldname['userid']));
	//不管是提单人或是陪同人只要有签到就算该拜访单已签到

	//拜访单中增加记录签到次数
//    $sql2 = "select signnum from vtiger_visitingorder WHERE visitingorderid=?";
//    $res = $adb->pquery($sql2,array($fieldname['visid']));
//    if($adb->num_rows($res)){
//        $signnum = 0;
//        while($row=$adb->fetch_array($res)){
//        	$signnum = $row['signnum'];
//        }
//        $sql = 'UPDATE vtiger_visitingorder SET issign=1 and signnum=? WHERE visitingorderid=?';
//        $adb->pquery($sql, array($fieldname['visid'],$signnum+1));
//    }else{
//        $sql = 'UPDATE vtiger_visitingorder SET issign=1 and signnum=1 WHERE visitingorderid=?';
//        $adb->pquery($sql, array($fieldname['visid']));
//    }
    	$RecordModel->setEffectiveVisits($fieldname['visid'],$fieldname['userid']);

}

/**
 * 拜访单撤销
 * @param $fieldname
 * @return array
 */
function doVisitingOrderRevoke($fieldname){
    $user = new Users();
    global $current_user;
    $current_user = $user->retrieveCurrentUserInfoFromFile($fieldname['userid']);
    include_once('includes/http/Request.php');
    include_once('modules/VisitingOrder/actions/ChangeAjax.php');
    $save = new VisitingOrder_ChangeAjax_Action();
    $fieldname['action']='ChangeAjax';
    $fieldname['module']='VisitingOrder';
    $fieldname['mode']='doRevoke';
    $res  = $save->doRevoke(new Vtiger_Request($fieldname, $fieldname));
    return array($res);
}
#获取本部门的人
function getDepartmentsUserByUserId($userid) {
	global $adb;
	// 1. 获取用户及部门
	$sql = "SELECT id, CONCAT( '(',IFNULL(brevitycode,''),')',last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', IF ( `status` = 'Active', '', '[离职]' )) AS last_name FROM vtiger_users WHERE status='Active'";
	$res = $adb->pquery($sql, array());
	$temp_user = array();
	if($adb->num_rows($res) > 0){
        for($i=0; $i<$adb->num_rows($res); $i++){
            $temp_user[] = $adb->fetchByAssoc($res, $i);
        }
	}
	return array($temp_user);
}

/**客户账户检索
 * @param $fieldname
 * @param $userid
 * @return array
 */
function search_refillapplication_accountzh($fieldname,$userid) {
    global $adb;
    //获取账户信息
    $sql = "SELECT DISTINCT b.accountid,a.topplatform,a.accountzh,a.did FROM vtiger_rechargesheet a
                LEFT JOIN vtiger_refillapplication b ON(a.refillapplicationid=b.refillapplicationid)
                WHERE b.accountid=? AND a.topplatform=?";
    $res = $adb->pquery($sql, array($fieldname["accountid"],$fieldname["topplatform"]));
    $temp_actountzh = array();
    if($adb->num_rows($res) > 0){
        for($i=0; $i<$adb->num_rows($res); $i++){
            $temp_actountzh[] = $adb->fetchByAssoc($res, $i);
        }
    }
    return array($temp_actountzh);
}

/**
 * 获取客户对应的客服列表
 * @param $fieldname
 * @param $userid
 * @return array
 */
function getCustomerService($fieldname,$userid){
	global $adb;
	$accountName=str_replace("'",'',$fieldname['accountname']);
	$accountName=str_replace(',',"','",$accountName);
	$query="SELECT vtiger_account.accountname,vtiger_users.email1 AS email,vtiger_users.last_name AS username,vtiger_account.serviceid AS userid FROM vtiger_account LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_account.accountid LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_account.serviceid WHERE vtiger_crmentity.deleted=0
		AND vtiger_account.accountname in('{$accountName}')";
	$result=$adb->pquery($query,array());
	if($adb->num_rows($result)){
		$data=array();
		$userid=array();
		while($row=$adb->fetch_array($result)){
			if(!in_array($row["userid"],$userid)){
				$userid[]=$row["userid"];
				$data[$row["userid"]]=array("accountname"=>array($row["accountname"]),
					"email"=>$row["email"],
					"username"=>$row["username"],
				);
			}else{
				$data[$row["userid"]]["accountname"][]=$row["accountname"];
			}

		}
		return array(array('success'=>2,'data'=>array_values($data)));
	}
	return array(array('success'=>1,'data'=>'没有相关的数据'));
}


#添加拜访单
function add_Schoolvisit($fieldname,$userid){
	global $adb,$log,$current_user,$currentModule;
	global $isallow;
	$isallow=array('SalesOrder','Invoice','Quotes','VisitingOrder','Vacate','OrderChargeback','RefillApplication',
		'ExtensionTrial', 'Suppcontractsextension','PurchaseInvoice','Schoolvisit');

	$currentModule = 'Schoolvisit';

	$user = new Users();
	$current_user = $user->retrieveCurrentUserInfoFromFile($userid);
	include_once('includes/http/Request.php');
	include_once('modules/Vtiger/actions/Save.php');
	$save = new Vtiger_Save_Action();

	$res  = $save->saveRecord(new Vtiger_Request($fieldname, $fieldname));

	$order_id = $res->getId();
	if($order_id){
		// 更新客户地址
		//$sql = "update vtiger_visitingorder set customeraddress=? where visitingorderid=?";
		//$adb->pquery($sql, array($fieldname['customeraddress'], $order_id));

		// 添加到 拜访单签单详情表
		/*$sql = "INSERT INTO `vtiger_schoolvisitsign` (`visitsignid`, `visitingorderid`, `userid`, `visitsigntype`, `signtime`, `signaddress`, `issign`) VALUES (null, ?, ?, ?, ?, ?, ?);";
		$adb->pquery($sql, array($order_id, $current_user->id, '提单人', '', '', '0'));*/
		for($i=1; $i<=2; $i++) {
			// 添加到 拜访单签单详情表
			$sql = "INSERT INTO `vtiger_schoolvisitsign` (`visitsignid`, `visitingorderid`, `userid`, `visitsigntype`, `signtime`, `signaddress`, `issign`, `signnum`, `coordinate`) VALUES (null, ?, ?, ?, ?, ?, ?, ?, ?);";
			$adb->pquery($sql, array($order_id, $current_user->id, '提单人', '', '', '0', $i, ''));
		}

		if (!empty($fieldname['accompany'])) {   //陪同人
			$accomptyArr = explode(' |##| ', $fieldname['accompany']);
			$t_arr = array();
			foreach ($accomptyArr as $key => $value) {
				$t_arr[] = "(null, '$order_id', '$value', '陪同人', '', '', '0', '1', '')";
				$t_arr[] = "(null, '$order_id', '$value', '陪同人', '', '', '0', '2', '')";
			}
			$sql = "INSERT INTO `vtiger_schoolvisitsign` (`visitsignid`, `visitingorderid`, `userid`, `visitsigntype`, `signtime`, `signaddress`, `issign`, `signnum`, `coordinate`) VALUES " . implode(',', $t_arr);
			$adb->pquery($sql, array());
		}

		// 添加工作流
    	//调用申请单的处理，此处注释掉 2017/03/07 gaocl
	    include_once('data/CRMEntity.php');
	    $on_focus = CRMEntity::getInstance('Schoolvisit');
	    $on_focus->makeWorkflows('Schoolvisit', 398372, $order_id, 'edit');

	}
	return array($order_id);
}

/**
 * @param $fieldname
 * @param $userid
 * @return array
 * @author: steel.liu
 * @Date:xxx
 * 通用的保存方法
 */
function saveRecord($fieldname,$userid){
    global $adb,$log,$current_user,$currentModule;
    global $isallow;
    $workflowsid=0;
    if(!empty($fieldname['workflowsid']) && $fieldname['workflowsid']>0){
        $isallow=array($fieldname['module']);
        $workflowsid=$fieldname['workflowsid'];
	}
	if(!empty($fieldname['checkAction'])){//通用的验证方法
        $actionReturnMsg=checkAction($fieldname);
        if($actionReturnMsg['success']){
            $actionReturnMsg['record']=0;
        	return array($actionReturnMsg);
		}
	}
	$_POST=$fieldname;
	$_REQUEST=$fieldname;
    $createdworkflows=1;
	if(!empty($fieldname['createdworkflows'])){
        $createdworkflows=0;
    }
    $currentModule = $fieldname['module'];
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
    $_POST=$fieldname;
    include_once('includes/http/Request.php');
    include_once('modules/Vtiger/actions/Save.php');
    $className=$currentModule.'_Save_Action';
    if(class_exists($className)){
        $save = new $className();
	}else{
        $save = new Vtiger_Save_Action();
	}
    $res  = $save->saveRecord(new Vtiger_Request($fieldname, $fieldname));
	$returnID= $res->getId();
    if($returnID){
		if($workflowsid>0 && $createdworkflows==1){
            include_once('data/CRMEntity.php');
            $on_focus = CRMEntity::getInstance($currentModule);
            $on_focus->makeWorkflows($currentModule, $workflowsid, $returnID, 'edit');
		}
    }else{
        return array(array('success'=>false,'record'=>0,'msg'=>'无法保存!'));
	}
    if(!empty($fieldname['doAction'])){//后置事件
    	$fieldname['record']=$returnID;
        doAction($fieldname);
    }
    return array(array('success'=>true,'record'=>$returnID,'msg'=>''));

}
function checkAction($fieldname){
    include_once('includes/http/Request.php');
    $module=$fieldname['module'];
	$recordModel=Vtiger_Record_Model::getCleanInstance($module);
	return $recordModel->$fieldname['checkAction'](new Vtiger_Request($fieldname, $fieldname));
}
function doAction($fieldname){
    include_once('includes/http/Request.php');
    $module=$fieldname['module'];
    $recordModel=Vtiger_Record_Model::getCleanInstance($module);
    return $recordModel->$fieldname['doAction'](new Vtiger_Request($fieldname, $fieldname));
}
#添加拜访单
function add_VisitingOrder($fieldname,$userid){
	global $adb,$log,$current_user,$currentModule;
	$currentModule = 'VisitingOrder';

	$user = new Users();
	$current_user = $user->retrieveCurrentUserInfoFromFile($userid);
	include_once('includes/http/Request.php');
	include_once('modules/Vtiger/actions/Save.php');
	$save = new Vtiger_Save_Action();
    $_REQUEST['related_to']=$fieldname['related_to'];
	$res  = $save->saveRecord(new Vtiger_Request($fieldname, $fieldname));

	$order_id = $res->getId();
	if($order_id){
		// 更新客户地址和位置定位
		$sql = "update vtiger_visitingorder set customeraddress=?,destinationcode=? where visitingorderid=?";
		$adb->pquery($sql, array($fieldname['customeraddress'], $fieldname['destinationcode'], $order_id));

		/*// 添加到 拜访单签单详情表
		$sql = "INSERT INTO `vtiger_visitsign` (`visitsignid`, `visitingorderid`, `userid`, `visitsigntype`, `signtime`, `signaddress`, `issign`) VALUES (null, ?, ?, ?, ?, ?, ?);";
		$adb->pquery($sql, array($order_id, $current_user->id, '提单人', '', '', '0'));*/
		for($i=1; $i<=2; $i++) {
			// 添加到 拜访单签单详情表
			$sql = "INSERT INTO `vtiger_visitsign` (`visitsignid`, `visitingorderid`, `userid`, `visitsigntype`, `signtime`, `signaddress`, `issign`, `signnum`, `coordinate`) VALUES (null, ?, ?, ?, ?, ?, ?, ?, ?);";
			$adb->pquery($sql, array($order_id, $current_user->id, '提单人', '', '', '0', $i, ''));
		}
		$accompany='';
		if ( ! empty($fieldname['accompany'])) {   //陪同人
			$accomptyArr = explode(' |##| ', $fieldname['accompany']);
			$t_arr = array();
			foreach ($accomptyArr as $key => $value) {
				$t_arr[] = "(null, '$order_id', '$value', '陪同人', '', '', '0', '1', '')";
				$t_arr[] = "(null, '$order_id', '$value', '陪同人', '', '', '0', '2', '')";
			}
			$sql = "INSERT INTO `vtiger_visitsign` (`visitsignid`, `visitingorderid`, `userid`, `visitsigntype`, `signtime`, `signaddress`, `issign`, `signnum`, `coordinate`) VALUES " . implode(',', $t_arr);
			$adb->pquery($sql, array());
			$query='SELECT GROUP_CONCAT(last_name) FROM vtiger_users WHERE id in('.generateQuestionMarks($accomptyArr).')';
			$result=$adb->pquery($query,$accomptyArr);
			if($adb->num_rows($result)) {
				$accomptyArrdata = $adb->raw_query_result_rowdata($result, 0);
				$accompany=$accomptyArrdata[0];
			}
		}
		$adb->pquery("DELETE FROM vtiger_visitsign_mulit WHERE visitingorderid=?",array($order_id));
		$sql='INSERT INTO vtiger_visitsign_mulit(visitingorderid,userid,visitsigntype,signtime,signaddress,issign,signnum,zhsignnum) SELECT visitingorderid,userid,visitsigntype,signtime,signaddress,issign,signnum,if(signnum=1,\'一\',\'二\') FROM vtiger_visitsign WHERE visitingorderid=?';
		$adb->pquery($sql,array($order_id));
		$isaction=1;
		/*if($current_user->reports_to_id==38){
			$isaction=2;
			$sql='UPDATE vtiger_visitingorder SET modulestatus=\'c_complete\' WHERE visitingorderid=?';
			$adb->pquery($sql,array($order_id));
		}*/
		#添加审批流程
		$time = date('Y-m-d H:i:s');
        $workflow = array(
            'salesorderid'			=>$order_id,
            'workflowsid'				=>400,
            'modulename'				=>'VisitingOrder',
            'smcreatorid'				=>$userid,
            'createdtime'				=>$time,
            'ishigher'				=>1,
            'addtime'					=>$time
        );

        $reports_to_id=$current_user->reports_to_id;
        $query='SELECT vtiger_users.*,vtiger_role.*,vtiger_departments.* FROM vtiger_users LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_users.id LEFT JOIN vtiger_user2role ON vtiger_user2role.userid=vtiger_users.id
			LEFT JOIN vtiger_role ON vtiger_user2role.roleid=vtiger_role.roleid LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_user2department.departmentid WHERE vtiger_users.id=?';
        $result2=$adb->pquery($query,array($userid));
        $userInfoResult=$adb->fetchByAssoc($result2,0);
        if($userInfoResult){
            $reports_to_id=$userInfoResult['reports_to_id'];
        }

		$workflow['sequence'] = 1;
		$workflow['workflowstagesname'] = '提单人上级审批';
		$workflow['workflowstagesid'] = 470;
		$workflow['isaction'] =$isaction;
		$workflow['actiontime'] = $time;
		$workflow['higherid'] = $reports_to_id;

        /*
		for($i=1;$i<=3;$i++){
			$workflow['sequence'] = $i;
			if($i==1){
				$workflow['workflowstagesname'] = '提单人上级审批';
				$workflow['workflowstagesid'] = 470;
				$workflow['isaction'] = 1;
				$workflow['actiontime'] = $time;
				$workflow['higherid'] = 0;
			}elseif($i==2){
				$workflow['workflowstagesname'] = '提单人确认';
				$workflow['workflowstagesid'] = 520;
				$workflow['isaction'] = 0;
				$workflow['actiontime'] = '';
				$workflow['higherid'] = 1;
			}elseif($i==3){
				$workflow['workflowstagesname'] = '提单人上级审批';
				$workflow['workflowstagesid'] = 1869;
				$workflow['isaction'] = 0;
				$workflow['actiontime'] = '';
				$workflow['higherid'] = 0;
			}
        }
        */
			$res = $adb->pquery("insert into vtiger_salesorderworkflowstages (salesorderid,workflowsid,
				modulename,smcreatorid,createdtime,ishigher,addtime,sequence,workflowstagesname,workflowstagesid,
				isaction,actiontime,higherid) values(?,?,?,?,?,?,?,?,?,?,?,?,?)",
				$workflow);
		//添加拜访跟进中
        $visitaccountcontractid = $adb->getUniqueId('vtiger_visitaccountcontract');
        $adb->pquery('INSERT INTO `vtiger_visitaccountcontract`(visitaccountcontractid,accountid,visitingorderid,vextractid,vaccompany,vstartdate) SELECT ?,vtiger_visitingorder.related_to,vtiger_visitingorder.visitingorderid,vtiger_visitingorder.extractid,vtiger_visitingorder.accompany,vtiger_visitingorder.startdate FROM vtiger_visitingorder WHERE vtiger_visitingorder.related_to>0 AND visitingorderid=?',array($visitaccountcontractid,$order_id));
        $adb->pquery('UPDATE vtiger_crmentity,vtiger_visitingorder SET vtiger_visitingorder.accountnamer=vtiger_crmentity.label,vtiger_visitingorder.modulename=vtiger_crmentity.setype WHERE vtiger_visitingorder.related_to=vtiger_crmentity.crmid AND vtiger_visitingorder.visitingorderid=?',array($order_id));
        $adb->pquery('UPDATE vtiger_salesorderworkflowstages,vtiger_visitingorder SET vtiger_salesorderworkflowstages.accountid=vtiger_visitingorder.related_to,vtiger_salesorderworkflowstages.accountname=(SELECT vtiger_crmentity.label FROM vtiger_crmentity WHERE vtiger_crmentity.crmid=vtiger_visitingorder.related_to) WHERE vtiger_visitingorder.visitingorderid=vtiger_salesorderworkflowstages.salesorderid AND vtiger_salesorderworkflowstages.salesorderid=?',array($order_id));

        $recordModel=Vtiger_Record_Model::getCleanInstance('VisitingOrder');
		$fieldname['visitingorderid']=$order_id;
		if ( ! empty($fieldname['accompany'])) {
			$fieldname['accompany']=explode(' |##| ', $fieldname['accompany']);
		}
		$recordModel->addEffectiveVisits(new Vtiger_Request($fieldname, $fieldname),$userid);
        $recordModel->getSendWinXinUser($order_id);

		$user = new Users();
		if($current_user->reports_to_id){
		$report_users = $user->retrieveCurrentUserInfoFromFile($current_user->reports_to_id);
		global $site_URL;
		$body= '与您相关的拜访单需要审核<br>';
		$body.='<table style="border-collapse: collapse;border:solid 1px #000;color:#666;font-size:12px;">
                    <tr><td style="border:solid 1px #000;font-size:12px;text-align: right;" nowrap="nowrap">主题</td><td style="border:solid 1px #000;text-align:left;color:#000;font-size:14px;" nowrap="nowrap"><a href="'.$site_URL.'index.php?module=VisitingOrder&view=Detail&record='.$order_id.'" target="_blank" style="text-decoration:none;">'.$fieldname['subject'].'</a></td></tr>
                    <tr><td style="border:solid 1px #000;font-size:12px;text-align: right;" nowrap="nowrap">客户</td><td style="border:solid 1px #000;text-align:left;color:#000;font-size:14px;" nowrap="nowrap">'.$fieldname['related_to_display'].'</td></tr>
                    <tr><td style="border:solid 1px #000;font-size:12px;text-align: right;" nowrap="nowrap">拜访地址</td><td style="border:solid 1px #000;text-align:left;color:#000;font-size:14px;" nowrap="nowrap">'.$fieldname['destination'].'</td></tr>
                    <tr><td style="border:solid 1px #000;font-size:12px;text-align: right;" nowrap="nowrap">联系人</td><td style="border:solid 1px #000;text-align:left;color:#000;font-size:14px;" nowrap="nowrap">'.$fieldname['contacts'].'</td></tr>
                    <tr><td style="border:solid 1px #000;font-size:12px;text-align: right;" nowrap="nowrap">拜访目的</td><td style="border:solid 1px #000;text-align:left;color:#000;font-size:14px;" nowrap="nowrap">'.$fieldname['purpose'].'</td></tr>
                    <tr><td style="border:solid 1px #000;font-size:12px;text-align: right;" nowrap="nowrap">提单人</td><td style="border:solid 1px #000;text-align:left;color:#000;font-size:14px;" nowrap="nowrap">'.$current_user->last_name.'</td></tr>
                    <tr><td style="border:solid 1px #000;font-size:12px;text-align: right;" nowrap="nowrap">陪同人</td><td style="border:solid 1px #000;text-align:left;color:#000;font-size:14px;" nowrap="nowrap">'.$accompany.'</td></tr>
                    <tr><td style="border:solid 1px #000;font-size:12px;text-align: right;" nowrap="nowrap">开始日期</td><td style="border:solid 1px #000;text-align:left;color:#000;font-size:14px;" nowrap="nowrap">'.$fieldname['startdate'].'</td></tr>
                    <tr><td style="border:solid 1px #000;font-size:12px;text-align: right;" nowrap="nowrap">结束日期</td><td style="border:solid 1px #000;text-align:left;color:#000;font-size:14px;" nowrap="nowrap">'.$fieldname['enddate'].'</td></tr>
                    <tr><td style="border:solid 1px #000;font-size:12px;text-align: right;" nowrap="nowrap">外出类型</td><td style="border:solid 1px #000text-align:left;color:#000;font-size:14px;" nowrap="nowrap">'.$fieldname['outobjective'].'</td></tr>
                    <tr><td style="border:solid 1px #000;font-size:12px;text-align: right;" nowrap="nowrap">备注</td><td style="border:solid 1px #000;text-align:left;color:#000;font-size:14px;" nowrap="nowrap">'.$fieldname['remark'].'</td></tr>
                    </table>';
            include_once('modules/SalesorderWorkflowStages/actions/SaveAjax.php');
            $object = new SalesorderWorkflowStages_SaveAjax_Action();
            $object->sendWxRemind(array('salesorderid'=>$order_id,'salesorderworkflowstagesid'=>0));
            $Subject = '拜访单审核';
            //$body = '与您相关的拜访单需要审核,<br> 目的地为:<a href="http://192.168.1.3/index.php?module=VisitingOrder&view=Detail&record='.$order_id.'" target="_blank">'. $fieldname['destination'] . '</a><br>请及时处理';
            $address = array(array('mail' => $report_users->column_fields['email1'], 'name' => $report_users->column_fields['last_name']));
            //$address=array(array('mail'=>'steel.liu@71360.com','name'=>$report_users->column_fields['last_name']));
            Vtiger_Record_Model::sendMail($Subject, $body, $address,'ERP系统');
		}
	}
	return array($order_id);
}



function getPortalUserid() {
	global $adb,$log;



	//$log->debug("Entering customer portal function getPortalUserid");

	$userid = Vtiger_Soap_CustomerPortal::lookupPrefValue('userid');
	if($userid === false) {
		$res = $adb->pquery("SELECT prefvalue FROM vtiger_customerportal_prefs WHERE prefkey = 'userid' AND tabid = 0", array());
		$norows = $adb->num_rows($res);
		if($norows > 0) {
			$userid = $adb->query_result($res,0,'prefvalue');
			// Update the cache information now.
			Vtiger_Soap_CustomerPortal::updatePrefValue('userid', $userid);
		}
	}
	return $userid;
	//$log->debug("Exiting customerportal function getPortalUserid");
}


function unsetServerSessionId($id)
{
	global $adb,$log;
	//$log->debug("Entering customer portal function unsetServerSessionId");
	//$adb->println("Inside the function unsetServerSessionId");

	$id = (int) $id;
	Vtiger_Soap_CustomerPortal::updateSessionId($id, false);

	$adb->pquery("delete from vtiger_soapservice where type='customer' and id=?", array($id));
	//$log->debug("Exiting customer portal function unsetServerSessionId");
	return;
}




/**	Function used to validate the session
 *	@param int $id - contact id to which we want the session id
 *	@param string $sessionid - session id which will be passed from customerportal
 *	return true/false - return true if valid session otherwise return false
 **/
function validateSession($id, $sessionid)
{
	global $adb;
	//$adb->println("Inside function validateSession($id, $sessionid)");

	if(empty($sessionid)) return false;

	$server_sessionid = getServerSessionId($id);

	$adb->println("Checking Server session id and customer input session id ==> $server_sessionid == $sessionid");

	if($server_sessionid == $sessionid) {
		$adb->println("Session id match. Authenticated to do the current operation.");
		return true;
	} else {
		$adb->println("Session id does not match. Not authenticated to do the current operation.");
		return false;
	}
}
function uppicture($fieldname,$basic){
    //return($basic);
    //include_once('modules/VisitingOrder/actions/uppicture.php');
    //$obj_up = new VisitingOrder_uppicture_Action();
    //return $obj_up->uppicture($fieldname,$basic);
    global $adb,$log;
    $files=$fieldname;
    $uid = $basic['userid'];
    $model='VisitingOrder';
    $record=$basic['record'];
    if($files['name'] != '' && $files['size'] > 0){
       global $upload_badext ;
        $current_id = $adb->getUniqueID("vtiger_files");
        $file_name = $files['name'];
        $binFile = sanitizeUploadFileName($file_name, $upload_badext);
        $filename = ltrim(basename(" " . $binFile)); //allowed filename like UTF-8 characters
        $filetype = $files['type'];
        $filesize = $files['size'];
        $filetmp_name = $files['tmp_name'];
        $upload_file_path = decideFilePath();
        $newfilename=time();
        $upload_status = rename($filetmp_name, $upload_file_path . $current_id . "_" .$newfilename);
        if($upload_status){
            $sql2 = "insert into vtiger_files(attachmentsid, name,description, type, path,uploader,uploadtime,newfilename) values(?, ?,?, ?, ?,?,?,)";
            $params2 = array($current_id, $filename, $model,$filetype, $upload_file_path,$uid,date('Y-m-d H:i:s'),$newfilename);
            $result = $adb->pquery($sql2, $params2);
        }
        if(!empty($record)&& $record>0){
            $sql="UPDATE vtiger_visitingorder SET picture=? WHERE visitingorderid=?";
            $adb->pquery($sql,array($filename.'##'.$current_id,$record));
        }
        return array('success'=>true,'result'=>array('id'=>$current_id,'name'=>$filename));
    }
    exit;
}
/**
 * 工作总结列表
 * @param $fieldname
 * @return array
 */
function get_WorkSummarize($fieldname){
    return get_com_list('WorkSummarize',$fieldname);
}
/**
 * 获取待审核信息（通用）
 * @param $fieldname
 * @return array
 */
function getWorkFlowCheck($fieldname){
	return get_com_list('WorkFlowCheck',$fieldname);
}
/**
 * 获取待审核列表信息（移动端待审核列表）
 * @param $fieldname
 * @return array
 */
function getWorkFlowChecks($fieldname){
    return get_workflowCheck_list('WorkFlowCheck',$fieldname);
}
/**
 * 二维码登陆
 * @param $fieldname
 * @return array
 */
function qrcodelogin($fieldname){
    global $adb;
    if($fieldname['flag']==2) {
		if ($fieldname['status'] == 'scan') {
			$adb->pquery("UPDATE vtiger_qrcodelogin SET `status`=1 WHERE ercode=?", array($fieldname['loginid']));
		} elseif ($fieldname['status'] == 'confirm') {
			$adb->pquery("UPDATE vtiger_qrcodelogin SET `status`=2,userid=? WHERE ercode=?", array($fieldname['userid'], $fieldname['loginid']));

		} elseif ($fieldname['status'] == 'cancel') {
			$adb->pquery("delete from vtiger_qrcodelogin WHERE ercode=?", array($fieldname['loginid']));
		}
		return array(1);
	}else{
		if($fieldname['status']=='scan'){
			//$adb->pquery("UPDATE vtiger_qrcodelogin SET `status`=1 WHERE ercode=?",array($fieldname['loginid']));
		}elseif($fieldname['status']=='confirm'){
			$userid=$fieldname['userid'];
			$query="SELECT
					vtiger_users.user_name,
					vtiger_users.is_admin,
					vtiger_users.last_name,
					vtiger_user2role.roleid,
					vtiger_users.email1,
					vtiger_users.`status`,
					vtiger_users.title,
					vtiger_users.phone_work,
					vtiger_users.department,
					vtiger_users.phone_mobile,
					vtiger_users.reports_to_id,
					vtiger_users.phone_other,
					vtiger_users.email2,
					vtiger_users.phone_fax,
					vtiger_users.secondaryemail,
					vtiger_users.phone_home,
					vtiger_users.address_city,
					vtiger_users.address_state,
					vtiger_users.address_postalcode,
					vtiger_users.user_sys,
					vtiger_user2department.departmentid,
					vtiger_user2role.secondroleid,
					vtiger_users.usermodifiedtime,
					vtiger_users.usercode,
					vtiger_users.user_entered,
					vtiger_users.fillinsales,
					vtiger_users.brevitycode,
					vtiger_users.leavedate,
					vtiger_users.isdimission,
					vtiger_role.rolename,
					vtiger_users.id
				FROM
					vtiger_users
				INNER JOIN vtiger_user2role ON vtiger_users.id = vtiger_user2role.userid
				inner JOIN vtiger_role ON vtiger_role.roleid=vtiger_user2role.roleid
				INNER JOIN vtiger_user2department ON vtiger_users.id = vtiger_user2department.userid
				WHERE
					vtiger_users.id >0
				# AND departmentid != ''
				AND `status`='Active'
				AND id=? limit 1";
			$sales = $adb->pquery($query, array($userid));
			$rows = $adb->num_rows($sales);
			$ret_lists = array();
			if ($rows>0) {
				while ($row = $adb->fetchByAssoc($sales)) {
					$lists = array();
					$lists['username'] = $row['user_name'];
					$lists['is_admin'] = $row['is_admin'];
					$lists['last_name'] = $row['last_name'];
					$lists['email'] = $row['email1'];
					$lists['status'] = $row['status'];
					$lists['title'] = $row['title'];
					$lists['phone_work'] = $row['phone_work'];
					$lists['department'] = $row['department'];
					$lists['phone_mobile'] = $row['phone_mobile'];
					$lists['department'] = $row['department'];
					$lists['departmentname'] = $row['department'];
					$lists['reports_to_id'] = $row['reports_to_id'];
					$lists['phone_other'] = $row['phone_other'];
					$lists['usercode'] = $row['usercode'];
					$lists['user_entered'] = $row['user_entered'];
					$lists['brevitycode'] = $row['brevitycode'];
					$lists['leavedate'] = $row['leavedate'];
					$lists['leavedate'] = $row['leavedate'];
					$lists['isdimission'] = $row['isdimission'];
					$lists['roleid'] = $row['roleid'];
					$lists['rolename'] = $row['rolename'];
					$lists['fullname'] = $row['last_name'];
					$lists['reportstoid'] = $row['reports_to_id'];
					$lists['id'] = $row['id'];
					$lists['userid'] = $row['id'];
					$lists['departmentid'] = $row['departmentid'];
					$ret_lists[] = $lists;
				}
				$data = json_encode($ret_lists);
				return array(array("success" => true, "data" => $data));
			}
			return array(array("success" => false));
		}elseif($fieldname['status']=='cancel'){
			//$adb->pquery("delete from vtiger_qrcodelogin WHERE ercode=?",array($fieldname['loginid']));
		}
		//return array(1);
	}

}

/**
 * 工作总结列表
 * @param $fieldname
 * @return array
 */
function get_SalesDailyList($fieldname,$userid){
    $list = get_com_list('SalesDaily', $fieldname);
    //return array();
    $data = $list['list'];
    if (count($data) > 1) {
    	$data = salesDailyIsLook($data,$userid);
    	$list['list'] = $data;
    }

    //$list['list'] = count($data);
    return $list;
}


function getUserNameByid($userid) {
	global $adb;
	$sql  = "SELECT CONCAT( last_name,'[',IFNULL((SELECT departmentname FROM vtiger_departments WHERE
									departmentid = (SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1)
							),''),']',(IF (`status` = 'Active','','[离职]')
						)
					) AS last_name
				FROM
					vtiger_users
				WHERE
					? = vtiger_users.id";
	$result = $adb->pquery($sql, array($userid));
	$res_cnt = $adb->num_rows($result);

	if ($res_cnt > 0) {
		$row = $adb->query_result_rowdata($result, 0);
		return $row['last_name'];
	}

	return '';
}

/*
	获取的日报 判断一下是否有新的 批复
*/
function salesDailyIsLook($data,$userid) {
	global $current_user,$adb;
	$ids = array();

	$username = getUserNameByid($userid);
	foreach ($data as $key => $value) {
            if ($value['smownerid'] == $username) {
                    $ids[] = $value['salesdailybasicid'];
            }
	}
//	$sql = "select relationid,islook from vtiger_approval where relationid IN (". implode(',', $ids) .")";
	$sql = "select moduleid as relationid,islook from vtiger_modcomments where moduleid IN (". implode(',', $ids) .") and modulename='SalesDaily'";

	$result = $adb->pquery($sql, array());
	$res_cnt = $adb->num_rows($result);
	$approvalArr = null;
	if ($res_cnt > 0) {
            $approvalArr = array();
            for($i=0; $i<$res_cnt; $i++ ) {
                    $row = $adb->fetch_row($result,$i);
                    $approvalArr[] = $row;
            }
	}

	$approvalKeyValue = array();
	foreach ($approvalArr as $key=>$value) {
		$approvalKeyValue[$value['relationid']] = $value['islook'];
	}

	foreach ($data as $key => $value) {
            if ($value['smownerid'] == $username) {
                    if ($approvalKeyValue[$value['salesdailybasicid']] === '0') {
                            $data[$key]['islook'] = 1;
                    }
            }
	}
	return $data;
}


function get_SalesDailyOtherData($fieldname, $userid) {
	global $current_user;
	$users = getUserById(array($userid));
	if (! empty($users)) {
		return array('last_name'=>$users[0], 'nowtime'=>date('Y-m-d'));
	}
	return array('last_name'=>'', 'nowtime'=>date('Y-m-d'));
}
/**
 * 添加工作总结
 * @param $fieldname
 * @param $userid
 * @return array
 */
function add_WorkSummarize($fieldname,$userid){
    global $current_user;
    $user = new Users();
    $current_user =$user->retrieveCurrentUserInfoFromFile($userid);
    include_once('includes/http/Request.php');
    include_once('modules/Vtiger/actions/Save.php');
    $save = new Vtiger_Save_Action();
    $res  = $save->saveRecord(new Vtiger_Request($fieldname, $fieldname));
    $order_id = $res->getId();
    return array($order_id);
}

/**
 * 验证是否已经写了工作总结
 * @param $userid
 * @return array
 * @throws Exception
 */
function checkWorkSummarize($userid) {
    global $adb;
    $date=date('Y-m-d');
    $res = $adb->pquery("SELECT count(1) as counts FROM `vtiger_worksummarize` WHERE left(createdtime,10)=? AND smownerid=?", array($date,$userid));
    return $adb->query_result($res,0,'counts');
}


function getSchoolInfo($id) {
	if (!empty($id)) {
		$sql = " select schoolname,address from vtiger_school where schoolid=?";
		global $adb;
		$res = $adb->pquery($sql, array($id));
		$count = $adb->num_rows($res);
		if($count > 0){
			$row = $adb->query_result_rowdata($res, 0);
			return $row;
	    }
	}
	return '';
}

/**
 *  延期申请审核
 */
function oneExtensionTrial($fieldname,$userid){
    global $current_user,$currentModule, $currentAction, $adb;
    $currentAction = 'DetailView';
    $currentModule = $fieldname['module'];
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
    include_once('includes/http/Request.php');
    include_once('modules/Vtiger/Models/Record.php');
    $recordModel=Vtiger_Record_Model::getInstanceById($fieldname['record'],$fieldname['module']);
    $arr=$recordModel->entity->column_fields;
    //取出合同编号
    if(!empty($arr['servicecontractsid'])){
        $recordModelU=Vtiger_Record_Model::getInstanceById($arr['servicecontractsid'] ,'ServiceContracts');
        $arr['servicecontractsid']=$recordModelU->entity->column_fields['contract_no'];
    }
    //取出负责人
    if(!empty($arr['assigned_user_id'])){
        $recordModelU=Vtiger_Record_Model::getInstanceById($arr['assigned_user_id'] ,'Users');
        $arr['assigned_user_id']=$recordModelU->entity->column_fields['last_name'];
    }
    // 获取工作流
    $workflows = $recordModel->getWorkflowsMobile(new Vtiger_Request($fieldname, $fieldname));
    return array('list'=>$arr,'Workflows'=>$workflows);
}
/**
 * 拜访单详情
 * @param $fieldname
 * @param $userid
 * @return array
 */
function get_record_detail($fieldname,$userid){
    global $current_user,$currentModule, $currentAction, $adb;
    $currentAction = 'DetailView';
    $currentModule = $fieldname['module'];
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
    include_once('includes/http/Request.php');
    include_once('modules/Vtiger/Models/Record.php');
    $recordModel=Vtiger_Record_Model::getInstanceById($fieldname['record'],$fieldname['module']);
    $arr=$recordModel->entity->column_fields;
    //不管完成不完成拜访单都返回工作流
    $arr['Workflows']=$recordModel->getWorkflowsMobile(new Vtiger_Request($fieldname, $fieldname));

	$outobjective=$recordModel->get('outobjective')=='出差'?0:1;
    $arr['accountname']='';
    if(!empty($arr['related_to'])){
        //$recordModelA=Vtiger_Record_Model::getInstanceById($arr['related_to'] ,'Accounts');
        $temp=get_account_name($arr['related_to']);
        $arr['accountname']=$temp[0];
    }
    $arr['usersname']='';
    if(!empty($arr['extractid'])){
        $recordModelU=Vtiger_Record_Model::getInstanceById($arr['extractid'] ,'Users');
        $arr['usersname']=$recordModelU->entity->column_fields['last_name'];
    }
    if(!empty($arr['auditorid'])){
        $recordModelU=Vtiger_Record_Model::getInstanceById($arr['auditorid'] ,'Users');
        $arr['auditorid']=$recordModelU->entity->column_fields['last_name'];
	}else{
        $arr['auditorid']='';
	}
    if(!empty($arr['signid'])){
        $recordModelU=Vtiger_Record_Model::getInstanceById($arr['signid'] ,'Users');
        $arr['signid']=$recordModelU->entity->column_fields['last_name'];
    }
    if(!empty($arr['followid'])){
        $recordModelU=Vtiger_Record_Model::getInstanceById($arr['followid'] ,'Users');
        $arr['followid']=$recordModelU->entity->column_fields['last_name'];
    }
    if(!empty($arr['schoolid'])) {
    	$schoolInfo = getSchoolInfo($arr['schoolid']);
    	$arr['schoolname'] = $schoolInfo['schoolname'];
    	$arr['schooladdress'] = $schoolInfo['address'];
    }
    if(!empty($arr['picture'])){
        $picture = $arr['picture'];
        $newvalue=explode('*|*',$picture);
        $returnpicture = array_pop($newvalue);
        if(!empty($returnpicture)) {
            include_once('modules/VisitingOrder/actions/DownloadFile.php');
            $download = new VisitingOrder_DownloadFile_Action();
            $picture_res =  $download->app_parse($returnpicture);
        }
    }
    if (!empty($arr['accompany'])) {
    	$userIdArr = explode(' |##| ', $arr['accompany']);
    	$userArr = getUserById($userIdArr);
    	$arr['accompanyuser'] = implode(',', $userArr);
    }
    $arr['pictures']=$picture_res;

    $visitsingArr = array();

    if (! empty($arr['accompany'])  && !empty($arr['schoolid'])) {
    	// 获取拜访单的签到信息
		if($outobjective==1) {
			$sql = "SELECT
					u.last_name,
					v.signaddress,
					v.visitsigntype,
					if(v.signnum=1, '一', '二') as signnum 
				FROM
					vtiger_schoolvisitsign v
				LEFT JOIN vtiger_users u ON v.userid = u.id
				WHERE
					v.visitingorderid = ?";
		}else{
			$sql = "SELECT
				u.last_name,
				v.signtime,
				v.signaddress,
				v.visitsigntype,
				v.zhsignnum as signnum,
				v.userid,
				IF(v.issign=1, '是', '否')  AS issign,
              	unusualsign,
	       		unusualremark,
	       		file
			FROM
				vtiger_visitsign_mulit v
			LEFT JOIN vtiger_users u ON v.userid = u.id
			WHERE
				v.visitingorderid = ?  order by v.userid,signnum";
		}
		$t_result = $adb->pquery($sql, array($fieldname['record']));
		while($rawData = $adb->fetch_array($t_result)) {
			$visitsingArr[] = $rawData;
		}
    }else{
		//if (! empty($arr['accompany'])) {
    	// 获取拜访单的签到信息
		if($outobjective==1) {
			$sql = "SELECT
					u.last_name,
					v.signaddress,
					v.visitsigntype,
					if(v.signnum=1, '一', '二') as signnum ,
              		unusualsign,
	       			unusualremark,
       				v.userid
	       			file
				FROM
					vtiger_visitsign v
				LEFT JOIN vtiger_users u ON v.userid = u.id
				WHERE
					v.visitingorderid = ?";
		}else{
			$sql = "SELECT
				u.last_name,
				v.signtime,
				v.signaddress,
				v.visitsigntype,
				v.zhsignnum as signnum,
				v.userid,
				IF(v.issign=1, '是', '否')  AS issign,
              	unusualsign,
	       		unusualremark,
	       		file
			FROM
				vtiger_visitsign_mulit v
			LEFT JOIN vtiger_users u ON v.userid = u.id
			WHERE
				v.visitingorderid = ?";
		}
		$t_result = $adb->pquery($sql, array($fieldname['record']));
		while($rawData = $adb->fetch_array($t_result)) {
			$visitsingArr[] = $rawData;
		}
    //}
	}
    $signRecord = array();
    foreach ($visitsingArr as $key=>$value) {
    	$recordKey = $value['userid'];
        $signRecord[$recordKey]['last_name'] = $value['last_name'];
        $signRecord[$recordKey]['visitsigntype'] = $value['visitsigntype'];
        $signRecord[$recordKey]['userid'] = $value['userid'];
        $fileData = array();
        if($value['unusualsign']){
            $AllFileArr = explode("*|*",$value['file']);
            foreach ($AllFileArr as $file){
                $fileData[] = explode("##",$file)[1];
            }
        }
        $value['file'] = $fileData;
        $signRecord[$recordKey]['data'][] = $value;
    }



    $signRecordArray=array();
    if($outobjective==1){
        // 获取拜访单的签到信息
        $sql = "SELECT
				u.last_name,
				v.signtime,
				v.signaddress,
				v.visitsigntype,
				v.signnum,
				v.userid,
				IF(v.issign=1, '是', '否')  AS issign,
				IF(v.signnum=1, '一', '二')  AS signnum,
	       			unusualsign,
	       			unusualremark,
	       			file

			FROM
				vtiger_visitsign v
			LEFT JOIN vtiger_users u ON v.userid = u.id
			WHERE
				v.visitingorderid = ?
			";
	}else{
        // 获取拜访单的签到信息
        $sql = "SELECT
				u.last_name,
				v.signtime,
				v.signaddress,
				v.visitsigntype,
				v.zhsignnum as signnum,
				v.userid,
				IF(v.issign=1, '是', '否')  AS issign,
	       			unusualsign,
	       			unusualremark,
	       			file
			FROM
				vtiger_visitsign_mulit v
			LEFT JOIN vtiger_users u ON v.userid = u.id
			WHERE
				v.visitingorderid = ?";
	}
    $signRecordArray = array();
    $recordResult = $adb->pquery($sql, array($fieldname['record']));
    while($rawData = $adb->fetch_array($recordResult)) {
        $signRecordArray[] = $rawData;
    }
    $signRecord = array();
    foreach ($signRecordArray as $key=>$value) {
    	$signRecordKey = $value['userid'];
        $signRecord[$signRecordKey]['last_name'] = $value['last_name'];
        $signRecord[$signRecordKey]['visitsigntype'] = $value['visitsigntype'];
        $signRecord[$signRecordKey]['userid'] = $value['userid'];
        $fileData = array();
        if($value['unusualsign']){
            $AllFileArr = explode("*|*",$value['file']);
            foreach ($AllFileArr as $file){
                $fileData[] = explode("##",$file)[1];
            }
        }
        $value['file'] = $fileData;
        $signRecord[$signRecordKey]['data'][] = $value;
    }
    $isCanCancel = false;
    if($fieldname['module']=='VisitingOrder'){
    	$ModuleModel = VisitingOrder_Module_Model::getCleanInstance($fieldname['module']);
        $isCanCancel = ($ModuleModel->exportGrouprt('VisitingOrder','specialcancel',$userid) && ($arr['modulestatus']=='c_complete'));
	}

    $arr['t_accompany'] = $visitsingArr;
    $arr['signrecord']=$signRecord;
    $arr['cancancel']=$isCanCancel;
    $arr['stragevisithistories']=$recordModel->strangeVisitHistory($fieldname['record'],$fieldname['host']);
    return array($arr);
}


// 获取添加客户中下拉字段的选择的数据
function getAddAccountReadyData($fieldname) {
	global $adb;

	// 获取公司属性
	$sql = "SELECT customerpropertyid,customerproperty FROM vtiger_customerproperty WHERE picklist_valueid = '0' ORDER BY sortorderid";
	$t_result = $adb->pquery($sql, array());
	$customerpropertyArr = array();
	while($rawData = $adb->fetch_array($t_result)) {
		$customerpropertyArr[] = $rawData;
	}

	// 获取公司来源
	$sql = "select leadsource from vtiger_leadsource order by sortorderid";
	$t_result = $adb->pquery($sql, array());
	$leadsource = array();
	while($rawData = $adb->fetch_array($t_result)) {
		$leadsource[] = $rawData['leadsource'];
	}
	$cnLeadsourceArr = array(
		'Cold Call'=>'电话营销',
		'TelNum'=>'电话号码',
		'Existing Customer'=>'老客户推荐',
		'Self Generated'=>'自动生成',
		'Employee'=>'员工推荐',
		'Partner'=>'合作伙伴',
		'Public Relations'=>'公共关系',
		'Direct Mail'=>'邮件推销',
		'Conference'=>'论坛/会议',
		'Trade Show'=>'展会',
		'Web Site'=>'网站',
		'Word of mouth'=>'口碑',
		'Other'=>'其它'
	);
	foreach($leadsource as $k=>$v) {
		if ($cnLeadsourceArr[$v]) {
			$leadsource[$k] = $cnLeadsourceArr[$v];
		}
	}
	return array($customerpropertyArr, $leadsource);
}
//客户列表
function getList($fieldname, $userid){
//    return array(array($fieldname));
    $list = get_com_list('Accounts', $fieldname);
    return $list;
}
function getGroupUsers($fieldname,$userid){

    $groupUsers=Users_Record_Model::getAccessibleUsers(54);
    $groupUsers=json_encode($groupUsers);
    return array($groupUsers);
}

function addAlertData($fieldname,$userid){
    global $adb;
    $modulename='ModComments';
    $modcommentsid=$fieldname['modcommentsid'];
    $subject = $fieldname['subject'];
    $alertcontent = $fieldname['alertcontent'];
    $alerttime = $fieldname['alerttime'];
    $alertid =$fieldname['alertid'];
    $alertid=implode(' |##| ', $alertid);
    $accountid=$fieldname['accountid'];
    $activitytype = $fieldname['activitytype'];
    $taskpriority = $fieldname['taskpriority'];
    $ownerid=$fieldname['ownerid'];
    $alertstatus = 'wait';
    $remark = '';
    $creatorid=$fieldname['creatorid'];

    $account_record_model=Vtiger_Record_Model::getCleanInstance('Accounts');
    //当前负责人可以领取的最大用户
    $limitm=$account_record_model->getRankLimitm($fieldname['creatorid']);
    $sql=" SELECT * FROM vtiger_account WHERE  accountid=?  ";
    $accountInfo=$adb->pquery($sql,array($fieldname['accountid']));
    $accountInfo=$adb->query_result_rowdata($accountInfo,0);
    /*if(empty($limitm[$accountInfo['accountrank']])||$limitm[$accountInfo['accountrank']]<=0) {
        return array("success"=>array('success'=>1,'message'=> vtranslate($accountInfo['accountrank'],'','zh_cn').'等级客户保护数量'.$limitm['rankProtectNum'][$accountInfo['accountrank']].'个，您当前已有'.vtranslate($accountInfo['accountrank'],'','zh_cn').'等级客户'.$limitm['havingRankProtectNum'][$accountInfo['accountrank']].'个，已达保护数量'));
    }*/
    try {
        $id = $adb->getUniqueID("vtiger_jobalerts");
        $insertSql = "insert into vtiger_jobalerts(
						jobalertsid,subject,alerttime,modulename,moduleid,alertcontent,alertid,alertstatus,alertcount,activitytype,taskpriority,remark,ownerid,creatorid,accountid,createdtime)
						 values(?,?,?,?,?,?,?,?,0,?,?,?,?,?,?,sysdate())";
        $insertparams[] = $id;
        $insertparams[] = $subject;
        $insertparams[] = $alerttime;
        $insertparams[] = $modulename;
        $insertparams[] = $modcommentsid;
        $insertparams[] = $alertcontent;
        $insertparams[] = $alertid;
        $insertparams[] = $alertstatus;
        $insertparams[] = $activitytype;
        $insertparams[] = $taskpriority;
        $insertparams[] = $remark;
        $insertparams[] = $ownerid;
        $insertparams[] = $creatorid;
        $insertparams[] = $accountid;
        $adb->pquery($insertSql, $insertparams);
        //更新提醒人表
        $arrAlertid = $fieldname['alertid'];
        if (!empty($arrAlertid)) {
            //更新提醒人表(插入)
            foreach ($arrAlertid as $alertid) {
                $insert_query = "insert into vtiger_jobalertsreminder(jobalertsid,alertid)values(?,?)";
                $adb->pquery($insert_query, array($id, $alertid));
            }
        }
    }catch (Exception $e){
        return array("success"=>array("success"=>0,'message'=>'添加失败请重试！'));
	}
    return array("success"=>array("success"=>1,"message"=>"添加成功"));
}

function followComment($fieldname){
    global $adb;
    try{
        $sql="insert into vtiger_submodcomments(modcommentsid,creatorid,createdtime,modcommenthistory,accountintentionality) values(?,?,?,?,?)";
        $adb->pquery($sql,array($fieldname['modcommentsid'],$fieldname['userId'],date("Y-m-d H:i:s"),$fieldname['modcommenthistory'],$fieldname['accountintentionality']));
        $result = $adb->pquery("select moduleid,modulename from vtiger_modcomments where modcommentsid=?",array($fieldname['modcommentsid']));
        if($adb->num_rows($result)){
            $row = $adb->fetchByAssoc($result,0);
            if($row['modulename']=='Accounts'){
                $adb->pquery("update vtiger_account set intentionality=? where accountid=?",array($fieldname['accountintentionality'],$row['moduleid']));
            }
        }
	}catch (Exception $e){
        return array("success"=>array("success"=>0));
	}
    return array("success"=>array("success"=>1));
};

function getIntentionality($fieldname,$userid){
	$arr = array();
    $arr['ACCOUNTINTENTIONALITY'] = ModComments_Record_Model::getAccountIntentionality(true);
    return $arr;
}

//客户详情
function getUserDetail($fieldname,$userid){
        $parentId = $fieldname['record'];
        $pageNumber =(int)$fieldname['page'];
        $limit = $fieldname['limit'];
        if(empty($pageNumber)){
            $pageNumber = 1;
        }else{
//            intval($pageNumber
        }

//        //计算总页数
//
//$pages=intval($numrows/$pagesize);
//
////判断页数设置
//
//if (isset($_GET['page'])){
//　$page=intval($_GET['page']);
//}
//else{
//　$page=1; //否则，设置为第一页
//}
    global $adb,$current_user;

    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);

        $pagingModel = new Vtiger_Paging_Model();
        $pagingModel->set('page', $pageNumber);
        if(!empty($limit)) {
            $pagingModel->set('limit', $limit);
        }
        $recentComments = ModComments_Record_Model::getRecentComments($parentId, $pagingModel,'Accounts',1);
        $pagingModel->calculatePageRange($recentComments);
        $currentUserModel = Users_Record_Model::getCurrentUserModel();

        foreach ($recentComments as $key => $val){
            $recentComments[$key]['valueMap']['accountintentionality'] = vtranslate($val['valueMap']['accountintentionality'],'Accounts','zh_cn');
            $dataComments = dataComments($val['valueMap']['modcommentsid']);
//            return  ($val);
                $recentComments[$key]['commentor'] = $dataComments;
//            }
        }
//           return $recentComments;
        //获取客户id
        $accountid=$parentId;
        require_once 'crmcache/role.php';

        //wangbin 2015-9-7 客服回访的任务包
//        global $current_user;
//        global $adb;
        $userid = $current_user->id;
        $accountInfo=$adb->pquery(" SELECT accountname FROM vtiger_account WHERE accountid=? ",array($accountid));
        $accountInfo=$adb->query_result_rowdata($accountInfo,0);
         //$current_user->is_admin;
        $if_updateTask1 = false;
        $task_name = "";
        $sel_serviceidsql = "SELECT serviceid,servicecommentsid FROM `vtiger_servicecomments` WHERE assigntype = 'accountby' AND related_to = ? limit 1";
        $serviceid_result = $adb->pquery($sel_serviceidsql,array($accountid));
        $serviceid = $adb->fetchByAssoc($serviceid_result, 0);//当前客户分配的客服;
        if($userid == $serviceid['serviceid']){//如果当前登录用户是该客户的客服，就去查找任务包
            $if_updateTask1 = true;
            $qiantai_taskArr = AutoTask_BasicAjax_Action::service_follow($accountid);
            $task_name = $qiantai_taskArr['autoworkflowtaskname'];

            $taskid = $qiantai_taskArr['autoworkflowtaskid'];
            $remarkArray = $adb->pquery("SELECT * FROM `vtiger_autoworkflowtasks` WHERE autoworkflowtaskid = ?",array($taskid));
			$remarkname = $adb->fetch_array($remarkArray,0);
            $remarkname = $remarkname['remark'];
        }
        //end

        $double_type =  ServiceContracts_Record_Model::search_double($accountid);  //双推产品的类型
        //wangbin 判断当前客户购买的产品类型
        $arr = array();
        if($userid == $serviceid['serviceid']){
            $arr['double_type'] = $double_type;
            $arr['servicecomment'] = $serviceid['servicecommentsid'];//判断客服跟进把最近的跟进记录添加到客服分配表中去
        }
        $arr['COMMENTSCOUNTS'] =  Accounts_Record_Model::getModcommentCount($accountid);
        $arr['COMMENTS'] = ($recentComments);
        $arr['ROLE'] = $roles;
        $arr['ACCOUNTID'] = $accountid;
        $arr['COMMENTSMODE'] = ModComments_Record_Model::getModcommentmode();
        $COMMENTSTYPE = ModComments_Record_Model::getModcommenttype();
//        foreach ($COMMENTSTYPE as $val){
//            vtranslate($val,'ModComments');
//        }
        $arr['COMMENTSTYPE'] = $COMMENTSTYPE;
        $arr['MODCOMMENTCONTACTS'] = ModComments_Record_Model::getModcommentContacts($accountid);
    	$arr['ACCOUNTINTENTIONALITY'] = ModComments_Record_Model::getAccountIntentionality(true);
        //$arr['CURRENTUSER'] = $currentUserModel;
        $arr['MODULE_NAME'] = 'Accounts';

        $arr['PAGING_MODEL'] = $pagingModel;

        if($if_updateTask1){
            $arr['TASKNAME'] = $task_name;
            $arr['REMARK'] = $remarkname;
        }
        return array($arr,$accountInfo['accountname']);
}
/**
 * 客户添加
 */
function addFollowInfo($fieldname,$userid){
	 include_once('modules/Vtiger/models/Record.php');
       global $adb;
       include_once('modules/Vtiger/models/Record.php');
        $creatorid = $fieldname['creatorid'];
//        $request->set('creatorid',$currentUserModel->getId());
//        $request->set('addtime',date('Y-m-d H:i:s' , time()));
        $time_now = date('Y-m-d H:i:s' , time());
        //客户id设置 gaocl add
        $modulename=$fieldname['modulename'];
        $accountid=$fieldname['accountid'];
    	$accountintentionality = $fieldname['accountintentionality'];
    if($modulename != 'Accounts' && !empty($accountid)){
//                $request->set('related_to',$_REQUEST['accountid']);
        }
		$commentcontentAccount=$fieldname['commentcontent'];
		$modcommenttype=$fieldname['modcommenttype'];
		if($modcommenttype=='首次客户录入系统跟进' || $modcommenttype=='首次拜访客户后跟进'){
			$followupdata=$fieldname['followupdata'];
			$followupdata=array_map(function($v){$noendl=str_replace('#endl#','',$v);$explode=explode('**#**',$noendl);$keynum=$explode[0]+1;return $keynum.'*#*'.$explode[1];},$followupdata);
			$commentcontent=implode('#endl#',$followupdata);
			$followupdataAccount=array_map(function($v){ $explode=explode('*#*',$v);$trimexplode1=trim($explode[1]);if(!empty($trimexplode1)){return $explode[0].','.$explode[1];}else{return '';}},$followupdata);
			$commentcontentAccount=implode(';',$followupdataAccount);
			$commentcontentAccount=$modcommenttype.':'.trim($commentcontentAccount,';');
			$fieldname['commentcontent']=$commentcontent;
		}

    //首次拜访客户后跟进 记录面谈的是KP还是非KP
    if($modulename=='Accounts' && $modcommenttype=='首次拜访客户后跟进'){
        $followupdata=$fieldname['followupdata'];
        $followupdata2=array_map(function($v){$noendl=str_replace('#endl#','',$v);$explode=explode('**#**',$noendl);return $explode[1];},$followupdata);
        if(in_array("非KP",$followupdata2)){
            $fieldname['iskp']=0;
        }else{
            $fieldname['iskp']=1;
        }
    }

        //2015年9月6日10:25:12 wangbin 如果是客服跟进客户信息,关联客服任务任务包
        $ifupdateservice = $fieldname['ifupdateservice'];
        $commentcontent = $fieldname['commentcontent'];
        $location_url = "";
        if($modulename=='Accounts' && !empty($accountid) && ($ifupdateservice === 'true'||$ifupdateservice === 'false')){//从前台接受一个参数；
            $qiantai_taskArr = AutoTask_BasicAjax_Action::service_follow($accountid);
            if(!empty($qiantai_taskArr)){
                $taskname = $qiantai_taskArr['autoworkflowtaskname'];
                $aftercomment = $commentcontent."<".$taskname.">";
                $taskid = $qiantai_taskArr['autoworkflowtaskentityid'];
                $record = $qiantai_taskArr['autoworkflowentityid'];
                $source_record = $qiantai_taskArr['autoworkflowid'];
                $clidkid = $qiantai_taskArr['autoworkflowtaskid'];
//                $request->set('commentcontent',$aftercomment);
//                $request->set('autoworkflowtaskentityid',$taskid);
                if($ifupdateservice === 'true'){
                    $location_url = "index.php?module=AutoTask&view=Detail&record="."$record"."&source_record=".$source_record."&clickid=".$clidkid."&remarkcommen=".$commentcontent;
                  //  header("Location:$location_url");
                    //AutoTask_BasicAjax_Action::closeCurrent_openNext($qiantai_taskArr,$commentcontent);
                }
            }
        }
            //die('客服跟进到此结束');
            //end

        if($modulename == 'ServiceComments') {
            //更新客服跟进天数 adatian/2015-07-01 add
            ServiceComments_Record_Model::updateServiceNofollowDay($accountid);
            //客服跟进客户如果有回访任务就要把当前任务添加到跟进表中 wangbin
            $followreturnplainid = $fieldname['isfollowplain'];//本次回访跟进id;
            if($followreturnplainid>0){
                ServiceComments_Record_Model::updatefollow($followreturnplainid);
//                $request->set('commentreturnplanid',$followreturnplainid);
            }
        }


//        $recordModel  = $this->saveRecord($request); //保存

        $sql = "INSERT INTO vtiger_modcomments  (modcommentsid,`commentcontent`, `related_to`, `addtime`, `creatorid`, `modcommenttype`, `modcommentmode`, `modcommenthistory`, `contact_id`, `modulename`, `moduleid`, `modcommentpurpose`, `commentreturnplanid`,`accountintentionality`) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
//        return array($commentcontent,$fieldname['moduleid'],$time_now,$creatorid,$fieldname['modcommenttype'],$fieldname['modcommentmode'],'',$fieldname['moduleid'],$modulename,$fieldname['moduleid'],$fieldname['modcommentpurpose'],0);
        $db=PearDatabase::getInstance();
		$modcommentsid=$db->getUniqueID('vtiger_modcomments');
        $remarkArray = $db->pquery($sql,array($modcommentsid,$commentcontent,$fieldname['moduleid'],$time_now,$creatorid,$fieldname['modcommenttype'],$fieldname['modcommentmode'],'',$fieldname['contact_id'],$modulename,$fieldname['moduleid'],$fieldname['modcommentpurpose'],0,$accountintentionality));
        $is_service =$fieldname['is_service'];//是否是当前客服添加的跟进；
        $modcommentid = $fieldname['id'];  //跟进id



        if(!empty($is_service)){
            $update_servicecommnets_sql = "UPDATE vtiger_servicecomments SET modcommentsid=? ,modcomment=?,modcommtnt_time=? WHERE servicecommentsid = 2001";
            $db->pquery($update_servicecommnets_sql,array($modcommentid,$commentcontent,$time_now,$is_service));
        }
        //更新客户保护天数 gaocl add
		$id = $fieldname['related_to'];
		//更新客服跟进天数 gaocl/2015-01-16 add
		$moduleid=$fieldname['moduleid'];//$servicecommentsid

        //更新拜访单跟进 steel /2015-03-04
		if($modulename == 'VisitingOrder'){
			VisitingOrder_Record_Model::updateVisitingOrderFollowstatus($moduleid);
		}
        //更新客跟进后修改CRM表中的修改时间
        if($modulename == 'Accounts'){
            if(!empty($commentcontentAccount)){
            	$accountModel = Vtiger_Record_Model::getInstanceById($moduleid,'Accounts');
                $oldintentionality = $accountModel->get("intentionality");
                if($oldintentionality==$accountintentionality){
                    $sql='UPDATE vtiger_account SET commentcontent=?,intentionality=? WHERE accountid=?';
		}else{
                    $sql='UPDATE vtiger_account SET commentcontent=?,intentionality=?,intentionalitydate=\''.date("Y-m-d").'\' WHERE accountid=?';
                }
                $db->pquery($sql,array($commentcontentAccount,$accountintentionality,$moduleid));
            }

            $accounts_model = Vtiger_Record_Model::getCleanInstance('Accounts');
            $tt = $accounts_model::updateAccountsStatus($moduleid,$creatorid);
            // 更新保护天数
            $salerank=$accounts_model->getSaleRank($creatorid);
            $results = $db->pquery("SELECT accountrank FROM vtiger_account WHERE accountid = ?", array($id));
            $accountrank = $db->query_result($results, 0,'accountrank');
            $userinfo = $db->pquery("SELECT u.user_entered,ud.departmentid FROM vtiger_users as u LEFT JOIN vtiger_user2department as ud ON ud.userid=u.id WHERE id = ?", array($creatorid));
            $departmentid = $db->query_result($userinfo, 0,'departmentid');
            $user_entered = $db->query_result($userinfo, 0,'user_entered');
            $result=$accounts_model->getRankDays(array($salerank,$accountrank,$departmentid,$user_entered));
            $recordModels = Vtiger_Record_Model::getInstanceById($id, 'Accounts');
            $entity = $recordModels->entity->column_fields;
            if($result['isupdate']=='ryes' && $entity['assigned_user_id']==$creatorid && !in_array($entity['accountcategory'],array(1,2))) {
                $update_query = "update vtiger_account set protectday=?,effectivedays=? where accountid=? ";
                $db->pquery($update_query, array($result['protectday'], $result['protectday'], $id));
            }
//                $tt = Accounts_Record_Model::updateAccountsStatus($moduleid,$creatorid);
//                return array($tt);

            $ServiceComments_model = Vtiger_Record_Model::getCleanInstance('ServiceComments');
            $ServiceComments_model::updateServiceNofollowDay($accountid);
//                ServiceComments_Record_Model::updateServiceNofollowDay($accountid);

                //                Leads_Record_Model::leadUpdateFllowup($request);
                $query = "UPDATE vtiger_leaddetails,vtiger_account SET vtiger_leaddetails.followuptime=?,vtiger_leaddetails.followupcontents=? WHERE vtiger_account.accountid=vtiger_leaddetails.accountid AND vtiger_account.accountid=? AND vtiger_account.frommarketing=1";

                $db->pquery($query, array($time_now,$commentcontentAccount,$accountid));

            //拜访单记录最新跟进时间 和跟进内容
            $sql2 =  "select visitingorderid from vtiger_visitingorder where related_to = ?  order by starttime desc";
            $res = $db->pquery($sql2,array($accountid));
            if($db->num_rows($res)){
                while ($row = $db->fetch_array($res)){
                    $visitingorderid[] = $row['visitingorderid'];
                }
                $sql = "update vtiger_visitingorder set addtime = ?,commentcontent=? where visitingorderid in (".implode(',',$visitingorderid).")";

                $db->pquery($sql,array(date('Y-m-d H:i:s' , time()),$commentcontentAccount));
            }

        }

        //对商机跟进后,修改商机的最近跟进时间，和跟进人;
        if($modulename == 'Leads'){
            //这里处理商对商机的一些跟进
            $currentid = $currentUserModel->getId();
            $leadsSmowner_sql = "SELECT smownerid FROM vtiger_crmentity INNER JOIN vtiger_leaddetails ON leadid=crmid WHERE crmid=? AND deleted = ?";
            $adb = PearDatabase::getInstance();
            if($moduleid){
                $LeadsmoResult = $adb->pquery($leadsSmowner_sql,array($moduleid,0));
                $smowneridss = $adb->query_result_rowdata($LeadsmoResult,0);
                $smownerid = $smowneridss['smownerid'];
                if($currentid==$smownerid){
                    $update_comment_sql = 'UPDATE vtiger_leaddetails SET  commenttime = NOW() WHERE leadid = ?';
                    $adb->pquery($update_comment_sql,array($moduleid));
                }
            }
        }

        $result = array();
        $result['data'] = $remarkArray;
        return array($fieldname);
        #移动app功能用 罗志坚 add
//        if($service){
//                $result['modcommentid'] = $modcommentid;
//                return $result;
//        }
//        $response = new Vtiger_Response();
//        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
//        //$response->setResult($result);
//        $response->setResult($location_url);
//
//        $response->emit();
}
 function dataComments($commentedBy){
        global $adb;
        $sql = "SELECT vm.*,vu.* FROM vtiger_modcomments as vm  INNER JOIN vtiger_users as vu on vu.id=vm.creatorid where vm.modcommentsid=?";
        $res = $adb->pquery($sql, array($commentedBy));
        $res_data = $adb->fetch_row($res);
        return $res_data;
}



// 根据用户id返回用户名
function getUserById($userArr) {
	global $adb;
	$user = array();
	if (! empty($userArr)) {
		$userid = implode(',', $userArr);
		$sql = "select last_name from vtiger_users where id IN (".$userid.")";
		$res = $adb->pquery($sql, array());
		if($adb->num_rows($res) > 0){
	        for($i=0; $i<$adb->num_rows($res); $i++){
	            $t = $adb->fetchByAssoc($res, $i);
	            $user[] = $t['last_name'];
	        }
		}
	}
	return $user;
}

/*
	添加客户
*/
function add_accounts($fieldname, $userid) {
	global $adb,$log,$current_user;

	$user = new Users();
	$current_user = $user->retrieveCurrentUserInfoFromFile($userid);

	include_once('includes/http/Request.php');
	$accountname=$fieldname['accountname'];
    	$accountid = $fieldname['accountid'];
	$accountname=trim($accountname);
	$label=preg_replace('/\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;|&quot;|　|&apos;|&amp;|&lt;|&gt;|&#039;|&ldquo;|&rdquo;|&lsquo;|&rsquo;|&hellip;|\“|\”|\‘|\〉|\〈|\’|\〖|\〗|\【|\】|\、|\·|\…|\——|\＋|\－|\＝|\,|\<|\.|\>|\/|\?|\;|\:|\\\'|\"|\[|\{|\]|\}|\\|\||\`|\~|\!|\@|\#|\$|\%|\^|\\&|\*|\(|\)|\-|\_|\=|\+|\，|\＜|\．|\＞|\／|\？|\；|\：|\＇|\＂|\［|\{|\］|\}|\＼|\｜|\｀|\￣|\！|\＠|\#|\＄|\％|\＾|\＆|\＊|\（|\）|\－|\＿|\＝|\＋|\，|\《|\．|\》|\、|\？|\；|\：|\’|\”|\【|\｛|\】|\｝|\＼|\｜|\·|\～|\！|\＠|\＃|\￥|\％|\…|\＆|\×|\（|\）|\－|\——|\—|\＝|\＋|\，|\《|\。|\》|\、|\？|\；|\：|\‘|\“|\【|\｛|\】|\｝|\、|\||\·|\~|\！|\@|\#|\￥|\%|\&|\*|\（|\）|\-|\——|\=|\+/u','',$accountname);
	$labelname=strtoupper($label);
	$sql = "SELECT
				vtiger_crmentity.label,
				vtiger_crmentity.crmid
			FROM
				vtiger_uniqueaccountname
			LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_uniqueaccountname.accountid
			WHERE
				deleted = 0
			AND vtiger_uniqueaccountname.accountname =?
			LIMIT 1";
	$listResult = $adb->pquery($sql, array($labelname));
	if($adb->num_rows($listResult) && !$accountid){
		$res=array("result"=>'error','status'=>false,"message"=>"该客户已存在,不允许重复创建!");
	}else{
		include_once('includes/http/Request.php');
		include_once('modules/Vtiger/actions/Save.php');
		include_once('modules/Accounts/actions/Save.php');
		$_REQUEST['record']=$fieldname['accountid']?$fieldname['accountid']:'';//save_modules模块中要用到
		$accountname=preg_replace('/^(\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;)+|(\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;)+$/u','',$accountname);
		$_REQUEST['accountname']=$labelname;
		$fieldname['accountname']=$accountname;
		$ressorder=new Accounts_Save_Action();
		$res=$ressorder->saveRecord(new Vtiger_Request($fieldname, $fieldname));
	}
	return array($res);
}
/*
	判断客户是否重复
*/
function check_accountname($fieldname, $userid) {
	global $adb;
	$query="SELECT 1 FROM vtiger_uniqueaccountname
                        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_uniqueaccountname.accountid
                        WHERE vtiger_uniqueaccountname.accountname=? AND vtiger_crmentity.deleted =0 ";
	$label=str_replace('\\','',$fieldname['accountname']);
	$label=preg_replace('/\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;|&quot;|　|&apos;|&amp;|&lt;|&gt;|&#039;|&ldquo;|&rdquo;|&lsquo;|&rsquo;|&hellip;|\“|\”|\‘|\〉|\〈|\’|\〖|\〗|\【|\】|\、|\·|\……|\…|\——|\＋|\－|\＝|\,|\<|\.|\>|\/|\?|\;|\:|\\\'|\"|\[|\{|\]|\}|\\|\||\`|\~|\!|\@|\#|\$|\%|\^|\\&|\*|\(|\)|\-|\_|\=|\+|\，|\＜|\．|\＞|\／|\？|\；|\：|\＇|\＂|\［|\{|\］|\}|\＼|\｜|\｀|\￣|\！|\＠|\#|\＄|\％|\＾|\＆|\＊|\（|\）|\－|\＿|\＝|\＋|\，|\《|\．|\》|\、|\？|\；|\：|\’|\”|\【|\｛|\】|\｝|\＼|\｜|\·|\～|\！|\＠|\＃|\￥|\％|\……|\…|\＆|\×|\（|\）|\－|\——|\—|\＝|\＋|\，|\《|\。|\》|\、|\？|\；|\：|\‘|\“|\【|\｛|\】|\｝|\、|\||\·|\~|\！|\@|\#|\￥|\%|\……|\…|\&|\*|\（|\）|\-|\——|\=|\+/u','',$label);
	$label=strtoupper($label);
	$query = $adb->pquery($query, array($label));
	$norows = $adb->num_rows($query);
	if ($norows > 0) {
		return array('1');
	} else {
		return array('0');
	}
}




/*
	返回我的客户
*/
function getAccounts($fieldname, $userid) {
	global $adb;

	$pagenum = $fieldname['pagenum'];
	// 获取共有多少页
	$sql = "SELECT
				count(*) AS count
			FROM
				vtiger_account
			LEFT JOIN vtiger_crmentity ON vtiger_account.accountid = vtiger_crmentity.crmid
			WHERE
				vtiger_crmentity.smownerid = ?";
	$query = $adb->pquery($sql, array($userid));
	$norows = $adb->num_rows($query);

	$pageTotal = 0;
	$resData = array();
	if ($norows > 0) {
		$row = $adb->query_result_rowdata($query, 0);
		$total = $row['count'];
		$page = 20;
		$pageTotal = ceil($total / $page);
		if ($pagenum <= $pageTotal) {
			$start = ($pagenum - 1) * $page;
			$limit = $page;
			$sql = "SELECT
				vtiger_account.*
			FROM
				vtiger_account
			LEFT JOIN vtiger_crmentity ON vtiger_account.accountid = vtiger_crmentity.crmid
			WHERE
				vtiger_crmentity.smownerid = ?
			ORDER BY vtiger_account.accountid DESC
			LIMIT $start,$limit";
			$listResult = $adb->pquery($sql, array($userid));
			while ($rawData = $adb->fetch_array($listResult)) {
				$resData[] = $rawData;
			}
        }
	}
	return array($resData, $pageTotal);
}

/**
 * 更新激活码信息
 * @param unknown $fieldname
 * @return s[]|unknown[]|unknown[]
 */
function findActivecode($fieldname){
	global $adb;
	$sql = "SELECT * FROM `vtiger_activationcode` WHERE activecode=?";
	$listResult = $adb->pquery($sql, array($fieldname['activecode']));
	$row = $adb->query_result_rowdata($listResult, 0);
	if(!empty($row)){
		$nowDate = date("Y-m-d H:i:s", time());
        $loginname=!empty($fieldname['loginname'])?$fieldname['loginname']:'';
		//if($fieldname['activetype'] == 'first_activate' && empty($row['activedate'])){
		if($fieldname['activetype'] == 'first_activate'){
			$sql = "UPDATE `vtiger_activationcode` SET `activedate`=?, `expiredate`=?,`companyname`=?,`usercode`=?, `status`=? WHERE `activecode`=?";
			$res = $adb->pquery($sql, array($fieldname['startdate'], $fieldname['enddate'],$fieldname['companyname'],$loginname, 1, $fieldname['activecode']));
            $sql="UPDATE vtiger_servicecontracts,vtiger_activationcode SET vtiger_servicecontracts.due_date=IF(vtiger_servicecontracts.due_date IS NULL,?,if(vtiger_servicecontracts.due_date<?,?,vtiger_servicecontracts.due_date)) WHERE vtiger_activationcode.contractid=vtiger_servicecontracts.servicecontractsid
			AND vtiger_activationcode.activecode=?";
            $adb->pquery($sql,array($fieldname['enddate'],$fieldname['enddate'],$fieldname['enddate'],$fieldname['activecode']));
        }elseif($fieldname['activetype'] == 'cancel'){//取消激活
			$sql = "UPDATE `vtiger_activationcode` SET `activedate`=?,`expiredate`=?,`companyname`=?,`usercode`=?, `status`=? WHERE `activecode`=?";
			$res = $adb->pquery($sql, array($fieldname['startdate'],$fieldname['enddate'], $fieldname['companyname'],$loginname,0, $fieldname['activecode']));
		}else{
        	$online=$fieldname['activetype']=='online'?"`onlinetime`='{$fieldname['startdate']}',":'';
			$sql = "UPDATE `vtiger_activationcode` SET {$online}`expiredate`=?,`companyname`=?,`usercode`=?, `status`=? WHERE `activecode`=?";
			$res = $adb->pquery($sql, array($fieldname['enddate'],$fieldname['companyname'], $loginname,1, $fieldname['activecode']));
            $sql="UPDATE vtiger_servicecontracts,vtiger_activationcode SET vtiger_servicecontracts.due_date=IF(vtiger_servicecontracts.due_date IS NULL,?,if(vtiger_servicecontracts.due_date<?,?,vtiger_servicecontracts.due_date)) WHERE vtiger_activationcode.contractid=vtiger_servicecontracts.servicecontractsid
			AND vtiger_activationcode.activecode=?";
            $adb->pquery($sql,array($fieldname['enddate'],$fieldname['enddate'],$fieldname['enddate'],$fieldname['activecode']));
		}
		$sql="INSERT INTO `vtiger_activationcode_detail` (`activationcodeid`, `activecode`, `activetype`, `startdate`, `enddate`, `remark`, `cdate`) VALUES (?, ?, ?, ?, ?, ?, ?)";
		$result = $adb->pquery($sql, array($row['activationcodeid'], $fieldname['activecode'], $fieldname['activetype'], $fieldname['startdate'], $fieldname['enddate'], $fieldname['remark'], $nowDate));
		return array(array('success'=>1));//操作成功
	}else{
		return array(array('success'=>0));//激活码不存在
	}
}
function tyunContractChange($fieldname){
    global $adb;
    $sql = "SELECT * FROM `vtiger_activationcode` WHERE contractname!='' AND contractname=?";
    $listResult = $adb->pquery($sql, array($fieldname['ContractCode']));
    //$row = $adb->query_result_rowdata($listResult, 0);
    $num = $adb->num_rows($listResult);
    if($num){
        $nowDate = date("Y-m-d H:i:s", time());
        $sql = "SELECT 1 FROM `vtiger_productid` WHERE productid!='' AND productid=?";
        $listResult = $adb->pquery($sql, array($fieldname['ProductID']));
        if($adb->num_rows($listResult))
		{
            $sql = "UPDATE `vtiger_activationcode` SET `productlife`=?, `productid`=? WHERE `contractname`=?";
            $res = $adb->pquery($sql, array($fieldname['ProductLife'], $fieldname['ProductID'], $fieldname['ContractCode']));
            return array(array('success'=>1));//操作成功
		}
		return array(array('success'=>2));
	}else{
		return array(array('success'=>0));//激活码不存在
	}
}

/*
	返回单个客户的详细信息
*/
function getAccountsDetail($fieldname, $userid) {
	global $adb;
	$sql = "select * from vtiger_account where accountid=?";
	$listResult = $adb->pquery($sql, array($fieldname['accountid']));
	$row = $adb->query_result_rowdata($listResult, 0);
	return array($row);
}

/**
 * 拜访单审核&打回
 *
 */
function do_VisitingOrderWorkflow($fieldname,$userid){
    global $current_user,$currentModule, $currentAction;
    $currentAction = 'DetailView';
    $currentModule = $fieldname['module'];
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
    include_once('includes/http/Request.php');
    //require_once('modules/Vtiger/actions/SaveAjax.php');
    include_once('modules/SalesorderWorkflowStages/actions/SaveAjax.php');
    $save_ajax=new SalesorderWorkflowStages_SaveAjax_Action();
    $mode=$fieldname['mode'];
    $save_ajax->$mode(new Vtiger_Request($fieldname, $fieldname));
    return array(array('OK'));
}
/**
 * 合同超期提醒
 */
function getExtendedReminder($fieldname,$userid){

    global $current_user,$adb;
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
    $where = getAccessibleUsers('ServiceContracts', 'List', true);
    $sql='';
    if ($where != '1=1') {
        $sql .= " AND vtiger_crmentity.smownerid in (" . implode(',', $where) . ")";
    }
    $confirmdate= date("Y-m-d",strtotime("-25 days"));
    $overdue=date("Y-m-d",strtotime("-31 days"));
    $querys="SELECT (SELECT COUNT(vtiger_extensiontrial.extensiontrialid) FROM vtiger_extensiontrial WHERE vtiger_extensiontrial.servicecontractsid=vtiger_servicecontracts.servicecontractsid) AS extensionnum,(SELECT email1 FROM vtiger_users WHERE vtiger_users.id = vtiger_crmentity.smownerid LIMIT 1) AS email,vtiger_servicecontracts.contract_no, vtiger_servicecontracts.isconfirm AS isconfirm, vtiger_servicecontracts.servicecontractsid, vtiger_servicecontracts.servicecontractsid, vtiger_servicecontracts.receivedate, vtiger_servicecontracts.delayuserid, vtiger_servicecontracts.confirmlasttime, IF ( vtiger_servicecontracts.isconfirm > 0, DATEDIFF( ?, vtiger_servicecontracts.confirmlasttime ), DATEDIFF( ?, vtiger_servicecontracts.receivedate )) AS diffdate, ( SELECT CONCAT( last_name, '[', IFNULL( ( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ) ) ) AS last_name FROM vtiger_users WHERE vtiger_crmentity.smownerid = vtiger_users.id ) AS userid, vtiger_crmentity.smownerid FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_servicecontracts.servicecontractsid WHERE deleted = 0 AND vtiger_servicecontracts.modulestatus = '已发放' AND ( ( vtiger_servicecontracts.isconfirm = 1 AND vtiger_servicecontracts.confirmlasttime <? ) OR ( vtiger_servicecontracts.isconfirm = 0 AND vtiger_servicecontracts.receivedate <? ))".$sql.' order by diffdate';
    $confirmResult=$adb->pquery($querys,array($overdue,$overdue,$confirmdate,$confirmdate));
    $confirmNum=$adb->num_rows($confirmResult);
    if($confirmNum>0){
        for($i=0;$i<$confirmNum;$i++){
            $row=$adb->fetch_array($confirmResult);
            //$row['diffdate']=$row['diffdate']=='-5'?'<span class="label label-success">'.$row['diffdate'].'</span>':($row['diffdate']=='-4'?'<span class="label label-b_check">'.$row['diffdate'].'</span>':($row['diffdate']=='-3'?'<span class="label label-a_normal">'.$row['diffdate'].'</span>':($row['diffdate']=='-2'?'<span class="label label-warning">'.$row['diffdate'].'</span>':($row['diffdate']=='-1'?'<span class="label label-a_exception">'.$row['diffdate'].'</span>':'<span class="label label-inverse">'.$row['diffdate'].'</span>'))));
            /**/
            // $row['add']=$row['delayuserid']==0?($row['smownerid']==$current_user->id?($row['extensiontrialid']==0?1:0):0):0;
            //是否可以延期申请
            if (in_array($row['isconfirm'], array('0', '1')) && ($row['extensionnum'] < 2) && $row['smownerid']==$current_user->id) {
                $row['add'] = '1';
            }
            if ($row['isconfirm'] == 0 && $row['extensionnum'] == 1) {
                $row['add'] = '0';
            }

            $row['type'] = '服务合同';

            $confirmlist[]=$row;
        }
    }
	return array($confirmlist);
}
/**
 * 移动端简历录入
 *
 */
function addSchoolresumeInfo($fieldname,$userid){

    global $current_user,$adb;
    $user = new Users();
    $query='SELECT 1 FROM vtiger_schoolresume 
					LEFT JOIN vtiger_crmentity 
						ON vtiger_crmentity.crmid=vtiger_schoolresume.schoolresumeid 
					WHERE vtiger_crmentity.deleted=0
						AND telephone=?';
    $result=$adb->pquery($query,array($fieldname['telephone']));

    if(!empty($fieldname['schoolrecruitid']) && $adb->num_rows($result)==0) {
        include_once('modules/Vtiger/models/Record.php');
    	$recordModel=Vtiger_Record_Model::getInstanceById($fieldname['schoolrecruitid'],'Schoolrecruit');
        $entity=$recordModel->getEntity();
        $column_fields=$entity->column_fields;
        $createuserid=$column_fields['createuserid'];
        $current_user = $user->retrieveCurrentUserInfoFromFile($createuserid);
        $arr = array(
            "schoolrecruitid" => $fieldname['schoolrecruitid'],
            "name" => $fieldname['name'],
            "gendertype" => $fieldname['gendertype'],
            "highestdegree" => $fieldname['highestdegree'],
            "schoolid" => $fieldname['schoolid'],
            "graduatemajor" => $fieldname['graduatemajor'],
            "email" => $fieldname['email'],
            "shool_resume_source" => "school_recruit",
            "comeform" => "手机端",
            "schoolname" => $fieldname['schoolname'],
            "telephone" => $fieldname['telephone'],
            "record" => '',
            "action" => "SaveAjax",
            "module" => 'Schoolresume',
            'view'=>'Edit'
        );
        include_once('includes/http/Request.php');
        include_once('modules/Vtiger/actions/Save.php');
        $save = new Vtiger_Save_Action();
        $res = $save->saveRecord(new Vtiger_Request($arr, $arr));
        $id=$res->getId();
        $adb->pquery('UPDATE vtiger_schoolresume SET schoolname=?,comeform=? WHERE schoolresumeid=?',
					array($fieldname['schoolname'],"手机端",$id));
        $adb->pquery('UPDATE vtiger_crmentity SET smcreatorid=?,smownerid=?,modifiedby=? WHERE crmid=?',array($createuserid,$createuserid,$createuserid,$id));
        return array(1);
    }else{
        return array(2);
	}
}

function getSalestagetInfo($userid) {
	global $adb;


	// 这里还有判断该用户 是部门还是个人
	$sql = "SELECT * FROM vtiger_user2role WHERE roleid=? AND userid=?";
	$sel_result = $adb->pquery($sql, array('H80', $userid));
	$res_cnt = $adb->num_rows($sel_result);

	$data = array();
	$data['weekData'] = array(1);
	$data['monthData'] = array(1);

	$nowYear = date('Y');
	$nowMonth = date('m');
	$now = date('Y-m-d');

	if($res_cnt > 0) {
		$data['is_depa'] ='1';

    	//$row = $adb->query_result_rowdata($sel_result, 0);
    	// 部门周报
		$sql = "select * from vtiger_depasalestargetdetail where createid=? AND  year=? AND month=? AND 
				'$now' BETWEEN  str_to_date(startdate,'%Y-%m-%d') AND str_to_date(enddate,'%Y-%m-%d') LIMIT 1";
		$sel_result = $adb->pquery($sql, array($userid, $nowYear, $nowMonth));
		$res_cnt = $adb->num_rows($sel_result);
		$data['weekData'] = $sql;
		if($res_cnt > 0) {
	    	$row = $adb->query_result_rowdata($sel_result, 0);
	    	$data['weekData'] = $row;
		}
		// 部门月报
		$sql = "select * from vtiger_depasalestarget where createid='$userid' AND  year='$nowYear' AND month='$nowMonth' LIMIT 1";
		$sel_result = $adb->pquery($sql, array());
		$res_cnt = $adb->num_rows($sel_result);
		if($res_cnt > 0) {
	    	$row = $adb->query_result_rowdata($sel_result, 0);
	    	$data['monthData'] = $row;
		}
	} else {
		// 周报
		$sql = "select * from vtiger_salestargetdetail where businessid=? AND  year=? AND month=? AND 
				'$now' BETWEEN  str_to_date(startdate,'%Y-%m-%d') AND str_to_date(enddate,'%Y-%m-%d') LIMIT 1";


		$sel_result = $adb->pquery($sql, array($userid, $nowYear, $nowMonth));
		$res_cnt = $adb->num_rows($sel_result);
		if($res_cnt > 0) {
	    	$row = $adb->query_result_rowdata($sel_result, 0);
	    	$data['weekData'] = $row;
		}
		// 月报
		$sql = "select * from vtiger_salestarget where businessid='$userid' AND  year='$nowYear' AND month='$nowMonth' LIMIT 1";
		$sel_result = $adb->pquery($sql, array());
		$res_cnt = $adb->num_rows($sel_result);
		if($res_cnt > 0) {
	    	$row = $adb->query_result_rowdata($sel_result, 0);
	    	$data['monthData'] = $row;
		}
	}
	return array($data);
}


/**
 * 获取负责人的客户的及产品的信息
 * @return array
 */
function getAccountsAndProduct($userid){
    global $adb,$current_user;

    $datetime=date("Y-m-d");
    $query = 'SELECT 1 FROM vtiger_salesdaily_basic WHERE vtiger_salesdaily_basic.dailydatetime=? AND vtiger_salesdaily_basic.smownerid=?';
    $result = $adb->pquery($query, array($datetime, $userid));
    $arrtemp['isadd']=0;
    if ($adb->num_rows($result)) {
        $arrtemp['isadd']=1;
    }
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
    $query='SELECT vtiger_account.accountid,vtiger_account.accountname FROM vtiger_account LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_account.accountid WHERE vtiger_crmentity.deleted=0 AND vtiger_account.accountcategory=0 AND vtiger_crmentity.smownerid=?';
    $result=$adb->pquery($query,array($current_user->id));
    while($rawData=$adb->fetch_array($result)){
        $arrtemp['account'][$rawData['accountid']]['name']=$rawData['accountname'];
        $arrtemp['account'][$rawData['accountid']]['id']=$rawData['accountid'];
    }
    $query='SELECT parentdepartment FROM `vtiger_departments` WHERE departmentid=?';
    $result=$adb->pquery($query,array($current_user->column_fields['departmentid']));
    $resultdata=$adb->query_result_rowdata($result,0);
    $otherpriceflag=1;
    $marketpricefalst=$resultdata['parentdepartment'].'::';
    //是否是上海,深圳的商务
    if(strpos($marketpricefalst,'H72::')===false && strpos($marketpricefalst,'H246::')===false){
        $otherpriceflag=2;
    }

    $query='SELECT vtiger_products.productid,vtiger_products.productname,vtiger_products.unit_price,vtiger_products.tranperformance,vtiger_products.otherunit_price FROM vtiger_products LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_products.productid WHERE vtiger_crmentity.deleted=0 AND vtiger_products.salesdailyshow=1 ORDER BY salesdailysort';
    $result=$adb->pquery($query,array());
    while($rawData=$adb->fetch_array($result)){
        $arrtemp['product'][$rawData['productid']]['id']=$rawData['productid'];
        $arrtemp['product'][$rawData['productid']]['name']=$rawData['productname'];
        $arrtemp['product'][$rawData['productid']]['marketprice']=$otherpriceflag==1?$rawData['unit_price']:$rawData['otherunit_price'];
        $arrtemp['product'][$rawData['productid']]['performance']=$rawData['tranperformance'];
    }
    return array($arrtemp);
}

/**
 * 获取负责人的客户的及产品的信息
 * @return array
 */
function getCandealAccounts($userid){

    global $adb,$current_user;
    //$userid=$userid['userid'];
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
    $query='SELECT salesdailybasicid FROM vtiger_salesdaily_basic WHERE smownerid=? ORDER BY salesdailybasicid DESC LIMIT 1';
    $result=$adb->pquery($query,array($userid));
    $arrtemp=array();
    do{
        if($adb->num_rows($result)==0){
            break;
        }
        $rowdata=$adb->query_result_rowdata($result,0);
        $query='SELECT vtiger_salesdailycandeal.*,vtiger_account.accountname FROM vtiger_salesdailycandeal LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_salesdailycandeal.accountid WHERE vtiger_salesdailycandeal.deleted=0 AND vtiger_salesdailycandeal.issigncontract=0 AND vtiger_salesdailycandeal.salesdailybasicid=?';
        $result=$adb->pquery($query,array($rowdata['salesdailybasicid']));
        while($rawData=$adb->fetch_array($result)){
            $arrtemp[]=$rawData;
            //$arrtemp[]=$rawData;
        }

    }while(0);
    return $arrtemp;
}
/*
添加批复
*/
function addApproval($fieldname, $userid){
	global $adb,$log,$current_user;

	$user = new Users();
	$current_user = $user->retrieveCurrentUserInfoFromFile($userid);
	include_once('includes/http/Request.php');
	include_once('modules/Approval/actions/Save.php');
	$save = new Approval_Save_Action();

	$save->saveRecord(new Vtiger_Request($fieldname, $fieldname));
	return array(array('flag'=>true));
}

/*
搜索服务合同
*/
function search_servicecontracts($fieldname, $userid) {
	global $current_user;
    global $currentView;
    global $currentModule;

    $currentModule='ServiceContracts';
    $currentView='List';
	//$userid = getPortalUserid();
	$user = new Users();
	$current_user = $user->retrieveCurrentUserInfoFromFile($userid);
	include_once('modules/ServiceContracts/Models/ServiceContracts_Module_Model.php');
	$searchModuleModel = new ServiceContracts_Module_Model();
	//return array($fieldname['searchValue']);die;
	return array($searchModuleModel->getSearchResultApp($fieldname['searchValue'] ,1));
}

/*
搜索供应商 gaocl add 2018/04/19
*/
function search_vendors($fieldname, $userid) {
    global $adb,$current_user;
	$user = new Users();
	$current_user = $user->retrieveCurrentUserInfoFromFile($userid);
    $searchKey = $fieldname['searchValue'];


	$query = "SELECT vtiger_vendor.vendorid, vtiger_vendor.vendor_no, vtiger_vendor.vendorname,vtiger_vendor.bankaccount,vtiger_vendor.bankname,vtiger_vendor.banknumber
			FROM vtiger_vendor 
			LEFT JOIN vtiger_crmentity ON vtiger_vendor.vendorid = vtiger_crmentity.crmid 
			WHERE vtiger_crmentity.deleted=0 AND vtiger_vendor.vendorstate='al_approval'";

    //$where = getAccessibleUsers('Vendors', 'List', false);
    $where = getAccessibleUsers('Vendors', 'Popup', false);
	if(!empty($where) && $where!='1=1'){
		$query .= ' AND (vtiger_crmentity.smownerid '.$where." OR EXISTS(SELECT 1 FROM vtiger_sharevendors WHERE vtiger_sharevendors.sharestatus=1 AND vtiger_sharevendors.vendorsid=vtiger_vendor.vendorid AND vtiger_sharevendors.userid={$current_user->id}))";
	}
    $query .= " AND ( vtiger_vendor.vendor_no LIKE '%$searchKey%' OR vtiger_vendor.vendorname LIKE '%$searchKey%' ) LIMIT 50";

	//return array($query);

	$result = $adb->pquery($query, array());
	$noOfRows = $adb->num_rows($result);

	$data = array();
	for($i=0; $i<$noOfRows; ++$i) {
		$row = $adb->query_result_rowdata($result, $i);
		$data[] = $row;
		if ($i >= 50) {
			break;
		}
	}
    return array($data);
}
/*
搜索供应商合同 gaocl add 2018/04/19
*/
function search_vendors_servicecontracts($fieldname, $userid) {
    global $adb;
    $searchKey = $fieldname['searchValue'];
    $query = "SELECT vtiger_suppliercontracts.signdate,IF(vtiger_suppliercontracts.modulestatus='c_complete','on','off') AS iscontracted,vtiger_suppliercontracts.suppliercontractsid,vtiger_suppliercontracts.contract_no,vtiger_suppliercontracts.vendorid,vtiger_vendor.vendorname
			FROM vtiger_suppliercontracts 
			LEFT JOIN vtiger_crmentity ON vtiger_suppliercontracts.suppliercontractsid = vtiger_crmentity.crmid 
			LEFT JOIN vtiger_vendor ON vtiger_vendor.vendorid = vtiger_suppliercontracts.vendorid
			WHERE ( vtiger_suppliercontracts.contract_no LIKE '%$searchKey%' OR vtiger_vendor.vendorname LIKE '%$searchKey%' )";

    $query .= " AND EXISTS
			(SELECT 1 FROM vtiger_accountplatform
			LEFT JOIN vtiger_crmentity M ON vtiger_accountplatform.accountplatformid = M.crmid
			WHERE M.deleted = 0 AND vtiger_accountplatform.modulestatus='c_complete' AND vtiger_accountplatform.vendorid=vtiger_suppliercontracts.vendorid AND vtiger_accountplatform.effectivestartaccount<=CURDATE()
			AND vtiger_accountplatform.effectiveendaccount>=CURDATE())";

    $where = getAccessibleUsers('SupplierContracts', 'List', true);
    if(!empty($where) && $where!='1=1'){
        $query .= ' and vtiger_crmentity.smownerid '.$where;
    }
    //担保和已签收的合同
    $query .= " AND vtiger_suppliercontracts.modulestatus in('c_complete') AND vtiger_suppliercontracts.isguarantee=1 AND vtiger_suppliercontracts.effectivetime>=CURRENT_DATE()";
    $query .= " LIMIT 50";

    //return array($query);
    $result = $adb->pquery($query, array());
    $noOfRows = $adb->num_rows($result);

    $data = array();
    for($i=0; $i<$noOfRows; ++$i) {
        $row = $adb->query_result_rowdata($result, $i);
        $data[] = $row;
        if ($i >= 50) {
            break;
        }
    }
    return array($data);
}

/*
	充值申请单 充值平台获取
*/
function refill_application_topplatform($fieldname, $userid) {
	global $adb;
	$sql = "select topplatform from vtiger_topplatform";
	$listResult = $adb->pquery($sql, array());
	$res = array();
	while($rawData=$adb->fetch_array($listResult)) {
   		$res[] =  $rawData;
    }
    return array($res);
}
//服务合同
function getNewinvoice($fieldname,$userid){
    $list = get_com_list('Newinvoice', $fieldname);
    return $list;
}
/*
发票（新）详情
*/
function oneNewinvoice($fieldname, $userid) {
    global $adb,$current_user;
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
    include_once('modules/Vtiger/Models/Vtiger_DetailView_Model.php');
    $operate=setoperate($fieldname['id'],'Newinvoice');
    $_REQUEST['realoperate']=$operate;
    $SalesOrderModel = Vtiger_DetailView_Model::getInstance('Newinvoice', $fieldname['id']);
    $recordModel = $SalesOrderModel->getRecord();
    //工单详情信息
    $sql = "SELECT vtiger_servicecontracts.contract_no as contractid ,
       vtiger_newinvoice.taxtype ,
       vtiger_newinvoice.accountid ,
       vtiger_newinvoice.invoicestatus ,
       vtiger_newinvoice.businessnamesone,
       vtiger_newinvoice.workflowsid ,
       vtiger_newinvoice.invoicecompany,
       vtiger_newinvoice.taxtotal ,
       vtiger_newinvoice.receiveid,
      (SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS label FROM vtiger_users WHERE id = vtiger_newinvoice.receiveid limit 1 ) as invoicer,
       vtiger_newinvoice.receivedate ,
       vtiger_newinvoice.businesscontent ,
       vtiger_newinvoice.billingcontent ,
       vtiger_newinvoice.billingid ,
       vtiger_newinvoice.file ,
       vtiger_newinvoice.taxpayers_no,
       vtiger_newinvoice.registeraddress,
       vtiger_newinvoice.telephone,
       vtiger_newinvoice.depositbank ,
       vtiger_newinvoice.accountnumber ,
       vtiger_newinvoice.isformtable ,
       vtiger_newinvoice.workflowstime ,
       vtiger_newinvoice.workflowsnode,
       vtiger_newinvoice.billingtime ,
       vtiger_newinvoice.drawer,
       vtiger_newinvoice.modulestatus ,
       vtiger_newinvoice.taxrate ,
       vtiger_newinvoice.invoicecode ,
       vtiger_newinvoice.businessnames ,
       vtiger_newinvoice.tax ,
       vtiger_newinvoice.totalandtax ,
       vtiger_newinvoice.amountofmoney,
       vtiger_newinvoice.commodityname,
       vtiger_newinvoice.remark,
       vtiger_newinvoice.salesorderid ,
       vtiger_newinvoice.customerno,
       vtiger_newinvoice.purchaseorder,
       vtiger_newinvoice.salescommission,
       vtiger_newinvoice.exciseduty,
       vtiger_newinvoice.s_h_amount,
       vtiger_crmentity.smownerid,
       (SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS label FROM vtiger_users WHERE id = vtiger_crmentity.smownerid limit 1 ) as personincharge,	
       vtiger_crmentity.modifiedtime,
       vtiger_newinvoice.invoicetype,
       vtiger_newinvoice.actualtotal,
       vtiger_newinvoice.invoiceno,
       vtiger_newinvoice.matchover,
       vtiger_newinvoice.voidreason,
       vtiger_newinvoice.voiduserid,
       vtiger_newinvoice.voiddatetime,
       vtiger_newinvoice.isaccountinvoice,
       vtiger_newinvoice.modulename,
       vtiger_newinvoice.havasigned ,
       vtiger_newinvoice.companycode,
       vtiger_newinvoice.customer_name,
       vtiger_workflows.workflowsname,
       vtiger_crmentity.deleted FROM  vtiger_crmentity 
       LEFT JOIN vtiger_newinvoice ON vtiger_newinvoice.invoiceid=vtiger_crmentity.crmid 
       LEFT JOIN vtiger_newinvoicebillads ON vtiger_newinvoicebillads.invoicebilladdressid=vtiger_crmentity.crmid 
       LEFT JOIN vtiger_newinvoiceshipads ON vtiger_newinvoiceshipads.invoiceshipaddressid=vtiger_crmentity.crmid 
       LEFT JOIN vtiger_newinvoicecf ON vtiger_newinvoicecf.invoiceid=vtiger_crmentity.crmid 
       LEFT JOIN vtiger_newinventoryproductrel ON vtiger_newinventoryproductrel.id=vtiger_crmentity.crmid 
       LEFT JOIN vtiger_workflows ON vtiger_workflows.workflowsid = vtiger_newinvoice.workflowsid
       LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid = vtiger_newinvoice.contractid
       WHERE  vtiger_crmentity.crmid=? LIMIT 1 ";

    $sel_result = $adb->pquery($sql, array($fieldname['id']) );
    $res_cnt = $adb->num_rows($sel_result);
    $row = array();
    if($res_cnt > 0) {
        $row = $adb->query_result_rowdata($sel_result, 0);
    }
    // 工作流
    include_once('includes/http/Request.php');
    $tt = $recordModel->getWorkflowsMobile(new Vtiger_Request($fieldname, $fieldname));

    //关联回款信息
    $sql = "select invoiceid,modulename from vtiger_newinvoice where invoiceid=?";
    $sel_result = $adb->pquery($sql, array($fieldname['id']));
    $resultdata=$adb->query_result_rowdata($sel_result,0);
    $sql = "select invoiceid,modulename from vtiger_newinvoice where invoiceid=? AND modulestatus=?";
    $sel_result = $adb->pquery($sql, array($fieldname['id'], 'a_exception'));
    $res_cnt = $adb->num_rows($sel_result);
    if($res_cnt > 0) {
        if($resultdata['modulename']=='ServiceContracts'){
            $invoicecompany="SELECT vtiger_servicecontracts.invoicecompany FROM vtiger_servicecontracts WHERE vtiger_servicecontracts.servicecontractsid = vtiger_receivedpayments.relatetoid";
        }else{
            $invoicecompany="SELECT vtiger_suppliercontracts.invoicecompany FROM vtiger_suppliercontracts WHERE vtiger_suppliercontracts.suppliercontractsid = vtiger_receivedpayments.relatetoid";
        }
        $sql = "SELECT  '1' AS data_flag,vtiger_newinvoicerayment.surpluinvoicetotal, vtiger_newinvoicerayment.newinvoiceraymentid, vtiger_newinvoicerayment.servicecontractsid, vtiger_newinvoicerayment.receivedpaymentsid, vtiger_newinvoicerayment.total , vtiger_newinvoicerayment.arrivaldate, vtiger_newinvoicerayment.invoicetotal, ( SELECT vtiger_receivedpayments.allowinvoicetotal FROM vtiger_receivedpayments WHERE vtiger_receivedpayments.receivedpaymentsid = vtiger_newinvoicerayment.receivedpaymentsid ) AS allowinvoicetotal, vtiger_newinvoicerayment.invoicecontent, vtiger_newinvoicerayment.remarks , vtiger_newinvoicerayment.invoiceid, vtiger_newinvoicerayment.contract_no, vtiger_newinvoicerayment.paytitle AS t_paytitle, vtiger_newinvoicerayment.paytitle AS t_paytitle, CONCAT(vtiger_receivedpayments.paytitle, '[', vtiger_receivedpayments.unit_price, ']') AS paytitle , ({$invoicecompany}) AS invoicecompany FROM vtiger_newinvoicerayment LEFT JOIN vtiger_receivedpayments ON vtiger_newinvoicerayment.receivedpaymentsid = vtiger_receivedpayments.receivedpaymentsid WHERE invoiceid = ? AND vtiger_newinvoicerayment.deleted = 0";
    } else {
        if($resultdata['modulename']=='ServiceContracts'){
            $invoicecompany="SELECT vtiger_servicecontracts.invoicecompany FROM vtiger_servicecontracts WHERE vtiger_servicecontracts.servicecontractsid = vtiger_receivedpayments.relatetoid";
        }else{
            $invoicecompany="SELECT vtiger_suppliercontracts.invoicecompany FROM vtiger_suppliercontracts WHERE vtiger_suppliercontracts.suppliercontractsid = vtiger_receivedpayments.relatetoid";
        }
        $sql = "SELECT  '1' AS data_flag,vtiger_newinvoicerayment.surpluinvoicetotal, vtiger_newinvoicerayment.newinvoiceraymentid, vtiger_newinvoicerayment.servicecontractsid, vtiger_newinvoicerayment.receivedpaymentsid, vtiger_newinvoicerayment.total , vtiger_newinvoicerayment.arrivaldate, vtiger_newinvoicerayment.invoicetotal, vtiger_newinvoicerayment.allowinvoicetotal, vtiger_newinvoicerayment.invoicecontent, vtiger_newinvoicerayment.remarks , vtiger_newinvoicerayment.invoiceid, vtiger_newinvoicerayment.contract_no, vtiger_newinvoicerayment.paytitle AS t_paytitle, vtiger_newinvoicerayment.paytitle AS t_paytitle, CONCAT(vtiger_receivedpayments.paytitle, '[', vtiger_receivedpayments.unit_price, ']') AS paytitle , ({$invoicecompany}) AS invoicecompany FROM vtiger_newinvoicerayment LEFT JOIN vtiger_receivedpayments ON vtiger_newinvoicerayment.receivedpaymentsid = vtiger_receivedpayments.receivedpaymentsid WHERE invoiceid = ? AND vtiger_newinvoicerayment.deleted = 0";
    }
    $sel_result = $adb->pquery($sql, array($fieldname['id']));
    $res_cnt = $adb->num_rows($sel_result);
    $RelevantPaymentInformation = array();
    if($res_cnt > 0){
        while($rawData=$adb->fetch_array($sel_result)) {
            $RelevantPaymentInformation[] = $rawData;
        }
    }
    $recordid=$fieldname['id'];
    //财务数据
    $sql = "SELECT *,(SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS label FROM vtiger_users WHERE id = vtiger_newinvoiceextend.drawerextend limit 1 ) as drawer FROM vtiger_newinvoiceextend WHERE vtiger_newinvoiceextend.deleted = 0
                AND vtiger_newinvoiceextend.invoiceid ='{$recordid}'";
    $newinvoiceextend = $adb->run_query_allrecords($sql);  // 获取发票信息
    foreach ($newinvoiceextend as $key=>$value) {
        $sql = "SELECT * FROM vtiger_newnegativeinvoice WHERE vtiger_newnegativeinvoice.deleted = 0 AND vtiger_newnegativeinvoice.invoiceextendid='{$value['invoiceextendid']}'";
        $newnegativeinvoice =  $adb->run_query_allrecords($sql);  // 获取红冲信息
        // 计算红冲的和
        $negativetotalandtaxextend = 0;
        if(count($newnegativeinvoice) > 0) {
            foreach($newnegativeinvoice as $v) {
                $negativetotalandtaxextend += $v['negativetotalandtaxextend'];
            }
        }
        $newinvoiceextend[$key]['newnegativeinvoice'] = $newnegativeinvoice;
        $newinvoiceextend[$key]['surplusnewnegativeinvoice'] = $value['totalandtaxextend'] - $negativetotalandtaxextend;
    }
    //获取作废数据
    $sql = "select *,(select surpluinvoicetotal from vtiger_newinvoicerayment where vtiger_newinvoicerayment.newinvoiceraymentid=vtiger_newinvoicetovoid.newinvoiceraymentid) AS surpluinvoicetotal from vtiger_newinvoicetovoid where type=? AND invoiceextendid IN (select invoiceextendid from vtiger_newinvoiceextend where invoiceid =?)";
    $sel_result = $adb->pquery($sql, array(1, $recordid));
    $res_cnt = $adb->num_rows($sel_result);
    $abandonedData = array();
    if($res_cnt > 0) {
        while($rawData=$adb->fetch_array($sel_result)) {
            $abandonedData[] = $rawData;
        }
    }
    //   作废数据和红冲作废数据 用 type区分  作废数据1红冲作废数据2
    //获取红冲作废记录
    $sql = "select *,(select surpluinvoicetotal from vtiger_newinvoicerayment where vtiger_newinvoicerayment.newinvoiceraymentid=vtiger_newinvoicetovoid.newinvoiceraymentid) AS surpluinvoicetotal from vtiger_newinvoicetovoid where type=? AND invoiceextendid IN (select invoiceextendid from vtiger_newinvoiceextend where invoiceid =?)";
    $sel_result = $adb->pquery($sql, array(2, $recordid));
    $res_cnt = $adb->num_rows($sel_result);
    $redAbandonedData = array();
    if($res_cnt > 0) {
        while($rawData=$adb->fetch_array($sel_result)) {
            $redAbandonedData[$rawData['invoiceextendid']][] = $rawData;
        }
    }
    //合同回款记录
    $sql="SELECT ( SELECT last_name FROM vtiger_users WHERE id = vtiger_receivedpayments.createid ) AS createid, vtiger_servicecontracts.contract_no, IFNULL( vtiger_servicecontracts.currencytype, '--' ) AS currencytype, vtiger_receivedpayments.relmodule, TRUNCATE ( vtiger_receivedpayments.unit_price, 2 ) AS unit_price, IFNULL( vtiger_receivedpayments.reality_date, '--' ) AS reality_date, IF ( vtiger_receivedpayments.standardmoney, TRUNCATE ( vtiger_receivedpayments.standardmoney, 2 ), '--') AS standardmoney, IF ( vtiger_receivedpayments.exchangerate, TRUNCATE ( vtiger_receivedpayments.exchangerate, 2 ), '--') AS exchangerate, IFNULL( ( SELECT vtiger_newinvoice.invoice_no FROM vtiger_newinvoice WHERE vtiger_newinvoice.invoiceid = vtiger_newinvoicerelatedreceive.invoiceid ), '--') AS invoice_no, IFNULL( ( SELECT vtiger_newinvoice.modulestatus FROM vtiger_newinvoice WHERE vtiger_newinvoice.invoiceid = vtiger_newinvoicerelatedreceive.invoiceid ), '--') AS modulestatus, IFNULL( vtiger_receivedpayments.paytitle, '--') AS paytitle, vtiger_receivedpayments.receivedpaymentsid AS receivedid, vtiger_receivedpayments.overdue FROM vtiger_receivedpayments LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid = vtiger_receivedpayments.relatetoid LEFT JOIN vtiger_newinvoicerelatedreceive ON vtiger_newinvoicerelatedreceive.receivedpaymentsid = vtiger_receivedpayments.receivedpaymentsid LEFT JOIN vtiger_newinvoice ON vtiger_newinvoice.invoiceid = vtiger_newinvoicerelatedreceive.invoiceid WHERE vtiger_newinvoice.invoiceid = {$recordid}";
    $contractReturnRecord = $adb->run_query_allrecords($sql);

    return array('Newinvoice'=>$row, 'workflows'=>$tt,'RelevantPaymentInformation'=>$RelevantPaymentInformation,'newinvoiceextend'=>$newinvoiceextend,'abandonedData'=>$abandonedData,'redAbandonedData'=>$redAbandonedData,'contractReturnRecord'=>$contractReturnRecord);
}

/**
 * @param  $record   type=1 指的是 获取作废数据，type=2 红冲作废数据
 * @param  string $type
 * @return array
 */
function getNewinvoicetovoid($record, $type='1') {
    global $adb;
    $sql = "select *,(select surpluinvoicetotal from vtiger_newinvoicerayment where vtiger_newinvoicerayment.newinvoiceraymentid=vtiger_newinvoicetovoid.newinvoiceraymentid) AS surpluinvoicetotal from vtiger_newinvoicetovoid where type=? AND invoiceextendid IN (select invoiceextendid from vtiger_newinvoiceextend where invoiceid =?)";
    $sel_result = $adb->pquery($sql, array($type, $record));
    $res_cnt = $adb->num_rows($sel_result);
    $res = array();
    if($res_cnt > 0) {
        while($rawData=$adb->fetch_array($sel_result)) {
            $res[$rawData['invoiceextendid']][] = $rawData;
        }
    }
    return $res;
}
/*
 退款申请详情
*/
function oneOrderChargeback($fieldname, $userid) {
    global $adb,$current_user;
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
    $operate=setoperate($fieldname['id'],'OrderChargeback');
    $_REQUEST['realoperate']=$operate;
    include_once('modules/Vtiger/Models/Vtiger_DetailView_Model.php');
    $SalesOrderModel = Vtiger_DetailView_Model::getInstance('OrderChargeback', $fieldname['id']);
    $recordModel = $SalesOrderModel->getRecord();
    //退款申请详情信息
    $sql = "SELECT vtiger_crmentity.smownerid ,
       (SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS label FROM vtiger_users WHERE id = vtiger_crmentity.smownerid limit 1 ) as personincharge,	
       vtiger_crmentity.createdtime ,
       vtiger_crmentity.modifiedtime ,
       vtiger_crmentity.modifiedby ,
       vtiger_crmentity.description ,
       vtiger_orderchargeback.orderchargeback_no ,
       vtiger_orderchargeback.servicecontractsid ,
       vtiger_orderchargeback.accountid ,
       vtiger_orderchargeback.workflowsid ,
       vtiger_orderchargeback.modulestatus ,
       vtiger_orderchargeback.workflowstime,
       vtiger_orderchargeback.workflowsnode ,
       vtiger_orderchargeback.contractamount ,
       vtiger_orderchargeback.receivingmoney,
       vtiger_orderchargeback.refundamount ,
       vtiger_orderchargeback.issubmit ,
       vtiger_orderchargeback.refundreason ,
       vtiger_orderchargeback.applytime ,
       vtiger_orderchargeback.executedcost ,
       vtiger_orderchargeback.processingresult,
       vtiger_orderchargeback.originalcontractprocessing ,
       vtiger_orderchargeback.changebackdescribe ,
       vtiger_orderchargeback.salesorderid ,
       vtiger_orderchargeback.inoivenoid ,
       vtiger_crmentity.deleted FROM  vtiger_crmentity 
       LEFT JOIN vtiger_orderchargeback ON vtiger_orderchargeback.orderchargebackid=vtiger_crmentity.crmid 
       WHERE  vtiger_crmentity.crmid=? LIMIT 1 ";

    $sel_result = $adb->pquery($sql, array($fieldname['id']) );
    $res_cnt = $adb->num_rows($sel_result);
    $row = array();
    if($res_cnt > 0) {
        $row = $adb->query_result_rowdata($sel_result, 0);
    }
    // 工作流
    include_once('includes/http/Request.php');
    $tt = $recordModel->getWorkflowsMobile(new Vtiger_Request($fieldname, $fieldname));
    //回款明细
    /*$sql = " SELECT vtiger_receivedpayments.*,IFNULL((SELECT sum(vtiger_receivedpayments_extra.extra_price) FROM `vtiger_receivedpayments_extra` WHERE vtiger_receivedpayments_extra.receivementid=vtiger_receivedpayments.receivedpaymentsid),0) AS sumextra_price FROM `vtiger_receivedpayments` where relatetoid =? ";
    $listResult = $adb->pquery($sql, array($row['servicecontractsid']));
    $detailsOfRepayment = array();
    while($rawData=$adb->fetch_array($listResult)) {
        $detailsOfRepayment[] =  $rawData;
    }*/
    // select vtiger_salesorderproductsrel.*,vtiger_products.productname,vtiger_products.realprice,vtiger_crmentity.createdtime as crmentitycreatedtime,vtiger_users.last_name,IFNULL(( SELECT vtiger_products.productname FROM vtiger_products WHERE vtiger_products.productid = vtiger_salesorderproductsrel.productcomboid ), '--' ) AS productcomboname from vtiger_salesorderproductsrel left join vtiger_products on vtiger_salesorderproductsrel.productid=vtiger_products.productid left join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_products.productid left join vtiger_users on vtiger_crmentity.smownerid=vtiger_users.id where vtiger_salesorderproductsrel.salesorderid=2180008 AND vtiger_salesorderproductsrel.multistatus=5
    // 退款产品明细
    /*$sql = " SELECT vtiger_receivedpayments.*,IFNULL((SELECT sum(vtiger_receivedpayments_extra.extra_price) FROM `vtiger_receivedpayments_extra` WHERE vtiger_receivedpayments_extra.receivementid=vtiger_receivedpayments.receivedpaymentsid),0) AS sumextra_price FROM `vtiger_receivedpayments` where relatetoid =? ";
    $listResult = $adb->pquery($sql, array($fieldname['id']));
    $DetailsOfRefundProducts = array();
    $CostSynthesisInformation=array();
    while($rawData=$adb->fetch_array($listResult)) {
        $DetailsOfRefundProducts[] =  $rawData;
        $CostSynthesisInformation['humanResourcesTotal']+=$rawData['costing'];// 人力成本
        $CostSynthesisInformation['externalMiningTotal']+=$rawData['purchasemount'];// 外采成本
    }*/
    //退款申请发票明细
    /*$sql = "SELECT vtiger_invoice.invoiceid,vtiger_invoiceextend.invoiceextendid,vtiger_invoiceextend.billingtimeextend,vtiger_invoiceextend.invoicecodeextend,vtiger_invoiceextend.invoice_noextend,vtiger_invoiceextend.commoditynameextend,vtiger_invoiceextend.totalandtaxextend,vtiger_invoiceextend.processstatus,vtiger_invoiceextend.invoicestatus,(SELECT CONCAT(last_name,'[',IFNULL((SELECT departmentname FROM vtiger_departments WHERE departmentid = (SELECT	departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 ) ),''),']',(IF (`status` = 'Active','','[离职]'))) AS last_name FROM vtiger_users WHERE vtiger_invoiceextend.operator = vtiger_users.id) AS operator,vtiger_invoiceextend.operatortime FROM vtiger_invoiceextend LEFT JOIN vtiger_invoice ON vtiger_invoice.invoiceid=vtiger_invoiceextend.invoiceid WHERE vtiger_invoiceextend.deleted=0 AND vtiger_invoice.contractid=? ";
    $listResult = $adb->pquery($sql, array($row['servicecontractsid']));
    $DetailsOfApplicationInvoiceForRefund = array();
    while($rawData=$adb->fetch_array($listResult)) {
        $DetailsOfApplicationInvoiceForRefund[] =  $rawData;
    }*/
    return array('SalesOrder'=>$row, 'workflows'=>$tt);
    /*    return array('SalesOrder'=>$row, 'workflows'=>$tt,'detailsOfRepayment'=>$detailsOfRepayment,'DetailsOfRefundProducts'=>$DetailsOfRefundProducts,'CostSynthesisInformation'=>$CostSynthesisInformation,'DetailsOfApplicationInvoiceForRefund'=>$DetailsOfApplicationInvoiceForRefund);*/
}
/*
 * 业绩明细表
 */
function oneAchievementallotStatistic($fieldname, $userid) {
    global $adb,$current_user;
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
    $operate=setoperate($fieldname['id'],'AchievementallotStatistic');
    $_REQUEST['realoperate']=$operate;
    include_once('modules/Vtiger/Models/Vtiger_DetailView_Model.php');
    $SalesOrderModel = Vtiger_DetailView_Model::getInstance('AchievementallotStatistic', $fieldname['id']);
    $recordModel = $SalesOrderModel->getRecord();
    $sql = "SELECT
		 aas.owncompany,
		 aas.createtime,
		 aas.reality_date,
		 aas.matchdate,
		 aas.paytitle,
		 aas.unit_price,
		 aas.unit_prices,
		 aas.department,
		 aas.groupname,
		 aas.departmentname,
		 aas.receivedpaymentownid,
		 aas.achievementtype,
		 aas.accountname,
		 aas.signdate,
		 aas.contract_no,
		 aas.total ,
		 aas.costing,
		 aas.purchasemount,
		 aas.worksheetcost,
		 aas.productname ,
		 aas.productlife ,
		 aas.marketprice ,
		 aas.dividemarketprice,
		 aas.costdeduction ,
		 aas.dividecostdeduction,
		 aas.other ,
		 aas.effectiverefund,
		 aas.arriveachievement,
		 aas.achievementmonth,
		 aas.modulestatus,
		 aas.dividetotal,
		 aas.extracost,
		 aas.isover,
		 aas.adjustachievement,
		 aas.adjustremarks,
		 aas.adjustbeforearriveachievement,
		 aas.achievementallotid,
         aas.adjustachievementrecord
	FROM
		 vtiger_achievementallot_statistic as aas
	WHERE
		aas.achievementallotid = ?
	LIMIT 1";
    $sel_result = $adb->pquery($sql, array($fieldname['id']) );
    $res_cnt = $adb->num_rows($sel_result);
    $row = array();
    if($res_cnt > 0) {
        $row = $adb->query_result_rowdata($sel_result, 0);
    }
    // 工作流
    include_once('includes/http/Request.php');
    $tt = $recordModel->getWorkflowsMobile(new Vtiger_Request($fieldname, $fieldname));
    return array('AchievementallotStatistic'=>$row, 'workflows'=>$tt);
}
/*
 * 业绩汇总表
 */
function oneAchievementSummary($fieldname, $userid) {
    global $adb,$current_user;
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
    $operate=setoperate($fieldname['id'],'AchievementSummary');
    $_REQUEST['realoperate']=$operate;
    include_once('modules/Vtiger/Models/Vtiger_DetailView_Model.php');
    $SalesOrderModel = Vtiger_DetailView_Model::getInstance('AchievementSummary', $fieldname['id']);
    $recordModel = $SalesOrderModel->getRecord();
    $sql = " SELECT a.remarks,a.adjustachievementrecord,a.confirmstatus,a.unit_price,a.arriveachievement,a.realarriveachievement,a.effectiverefund,adjustachievement,a.createtime,a.achievementmonth,a.invoicecompany,a.modulestatus,d.departmentname as departmentid,u.last_name as userid FROM `vtiger_achievementsummary` as a LEFT JOIN vtiger_departments as d ON a.departmentid=d.departmentid LEFT JOIN vtiger_users as u ON u.id=a.userid  WHERE  `achievementid` = ? LIMIT 1 ";
    $sel_result = $adb->pquery($sql, array($fieldname['id']) );
    $res_cnt = $adb->num_rows($sel_result);
    $row = array();
    if($res_cnt > 0) {
        $row = $adb->query_result_rowdata($sel_result, 0);
    }


    // 工作流
    include_once('includes/http/Request.php');
    $tt = $recordModel->getWorkflowsMobile(new Vtiger_Request($fieldname, $fieldname));
    return array('AchievementSummary'=>$row, 'workflows'=>$tt);
}
/*
 * 业绩日期设置
 */
function oneClosingDate($fieldname, $userid) {
    global $adb,$current_user;
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
    $operate=setoperate($fieldname['id'],'ClosingDate');
    $_REQUEST['realoperate']=$operate;
    include_once('modules/Vtiger/Models/Vtiger_DetailView_Model.php');
    $SalesOrderModel = Vtiger_DetailView_Model::getInstance('ClosingDate', $fieldname['id']);
    $recordModel = $SalesOrderModel->getRecord();
    $sql = " SELECT date ,modulestatus,recorddate,remarks  FROM `vtiger_closingdate`  WHERE  `id` = ? LIMIT 1 ";
    $sel_result = $adb->pquery($sql, array($fieldname['id']) );
    $res_cnt = $adb->num_rows($sel_result);
    $row = array();
    if($res_cnt > 0){
        $row = $adb->query_result_rowdata($sel_result, 0);
    }


    // 工作流
    include_once('includes/http/Request.php');
    $tt = $recordModel->getWorkflowsMobile(new Vtiger_Request($fieldname, $fieldname));
    return array('ClosingDate'=>$row, 'workflows'=>$tt);
}

/*
 * 分成单详情
 */
function oneSeparateInto($fieldname, $userid) {
    global $adb,$current_user;
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
    $operate=setoperate($fieldname['id'],'SeparateInto');
    $_REQUEST['realoperate']=$operate;
    include_once('modules/Vtiger/Models/Vtiger_DetailView_Model.php');
    $SalesOrderModel = Vtiger_DetailView_Model::getInstance('SeparateInto', $fieldname['id']);
    $recordModel = $SalesOrderModel->getRecord();
    //分成单详情信息
    $sql = "SELECT vtiger_separateinto.servicecontractsid,
       vtiger_separateinto.accountid ,
       vtiger_separateinto.total ,
       vtiger_separateinto.signdate ,
       vtiger_crmentity.smownerid ,
       vtiger_crmentity.createdtime ,
       vtiger_crmentity.modifiedtime ,
       vtiger_crmentity.modifiedby ,
       vtiger_separateinto.workflowstime ,
       vtiger_separateinto.workflowsnode ,
       vtiger_separateinto.modulestatus ,
       vtiger_separateinto.workflowsid ,
       vtiger_separateinto.contract_type ,
       vtiger_crmentity.deleted,
       (SELECT label FROM vtiger_crmentity WHERE crmid=vtiger_separateinto.servicecontractsid limit 1) as contract_no,
       (SELECT label FROM vtiger_crmentity WHERE crmid=vtiger_separateinto.accountid limit 1) as accountname,
       (SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS label FROM vtiger_users WHERE id = vtiger_crmentity.smownerid limit 1 ) as username
       FROM  vtiger_crmentity 
       LEFT JOIN vtiger_separateinto ON vtiger_separateinto.separateintoid=vtiger_crmentity.crmid 
       WHERE  vtiger_crmentity.crmid=? LIMIT 1 ";
    $sel_result = $adb->pquery($sql, array($fieldname['id']) );
    $res_cnt = $adb->num_rows($sel_result);
    $row = array();
    if($res_cnt > 0) {
        $row = $adb->query_result_rowdata($sel_result, 0);
    }
    // 工作流
    include_once('includes/http/Request.php');
    $tt = $recordModel->getWorkflowsMobile(new Vtiger_Request($fieldname, $fieldname));
    //分成成员列表
    $sql = " SELECT *,( SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS last_name FROM vtiger_users WHERE vtiger_servicecontracts_separate.receivedpaymentownid = vtiger_users.id ) AS receivedpaymentownname FROM `vtiger_servicecontracts_separate` WHERE separateintoid =?";
    $listResult = $adb->pquery($sql, array($fieldname['id']));
    $divideInto = array();
    while($rawData=$adb->fetch_array($listResult)) {
        $divideInto[] =  $rawData;
    }
    return array('SeparateInto'=>$row, 'workflows'=>$tt,'divideInto'=>$divideInto);
}
/*
*  超期录入审核  详情
*/
function oneRefundTimeoutAudit($fieldname, $userid) {
    global $adb,$current_user;
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
    $operate=setoperate($fieldname['id'],'RefundTimeoutAudit');
    $_REQUEST['realoperate']=$operate;
    include_once('modules/Vtiger/Models/Vtiger_DetailView_Model.php');
    $SalesOrderModel = Vtiger_DetailView_Model::getInstance('RefundTimeoutAudit', $fieldname['id']);
    $recordModel = $SalesOrderModel->getRecord();
    //超期录入信息详情
    $sql = "SELECT vtiger_refundtimeoutaudit.unit_price ,
       vtiger_refundtimeoutaudit.owncompany ,
       vtiger_refundtimeoutaudit.paytitle ,
       vtiger_refundtimeoutaudit.reality_date ,
       vtiger_crmentity.smownerid ,
       (SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS label FROM vtiger_users WHERE id = vtiger_crmentity.smownerid limit 1 ) as personincharge,
       vtiger_crmentity.createdtime ,
       vtiger_crmentity.modifiedtime ,
       vtiger_crmentity.modifiedby ,
       vtiger_refundtimeoutaudit.workflowstime ,
       vtiger_refundtimeoutaudit.workflowsnode ,
       vtiger_refundtimeoutaudit.modulestatus ,
       vtiger_refundtimeoutaudit.workflowsid ,
       vtiger_refundtimeoutaudit.receivedpaymentsid ,
       vtiger_refundtimeoutaudit.overdue ,
       vtiger_crmentity.deleted FROM  vtiger_crmentity 
       LEFT JOIN vtiger_refundtimeoutaudit ON vtiger_refundtimeoutaudit.refundtimeoutauditid=vtiger_crmentity.crmid 
       WHERE  vtiger_crmentity.crmid=?  LIMIT 1";
    $sel_result = $adb->pquery($sql, array($fieldname['id']) );
    $res_cnt = $adb->num_rows($sel_result);
    $row = array();
    if($res_cnt > 0) {
        $row = $adb->query_result_rowdata($sel_result, 0);
    }
    // 工作流
    include_once('includes/http/Request.php');
    $tt = $recordModel->getWorkflowsMobile(new Vtiger_Request($fieldname, $fieldname));
    return array('SalesOrder'=>$row, 'workflows'=>$tt);
}
/*
工单详情
*/
function oneSalesOrder($fieldname, $userid) {
    global $adb,$current_user;
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
    $operate=setoperate($fieldname['id'],'SalesOrder');
    $_REQUEST['realoperate']=$operate;
    include_once('modules/Vtiger/Models/Vtiger_DetailView_Model.php');
    $SalesOrderModel = Vtiger_DetailView_Model::getInstance('SalesOrder', $fieldname['id']);
    $recordModel = $SalesOrderModel->getRecord();
    //工单详情信息
    $sql = " SELECT vtiger_salesorder.salesorder_no ,vtiger_salesorder.subject ,vtiger_salesorder.potentialid,
       vtiger_salesorder.customerno,vtiger_salesorder.pending ,vtiger_salesorder.salescommission ,
       vtiger_salesorder.accountid,vtiger_crmentity.smownerid,vtiger_crmentity.createdtime,
       (SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS label FROM vtiger_users WHERE id = vtiger_crmentity.smownerid limit 1 ) as personincharge,	 
       vtiger_crmentity.modifiedtime,vtiger_crmentity.modifiedby ,vtiger_crmentity.description ,
       vtiger_salesorder.workflowsid,vtiger_salesorder.servicecontractsid ,
       vtiger_salesorder.modulestatus,vtiger_salesorder.workflowstime ,vtiger_salesorder.workflowsnode ,
       vtiger_salesorder.file ,vtiger_salesorder.productid ,vtiger_salesorder.issubmit ,
       vtiger_salesorder.isfrommarkets ,vtiger_salesorder.iseditproductlist ,
       vtiger_salesorder.voidreason ,vtiger_salesorder.voiduserid ,vtiger_salesorder.voiddatetime ,
       vtiger_salesorder.occupationamount ,vtiger_salesorder.customer_name ,
       vtiger_crmentity.deleted 
       FROM  vtiger_crmentity LEFT JOIN vtiger_salesorder ON vtiger_salesorder.salesorderid=vtiger_crmentity.crmid  WHERE  vtiger_crmentity.crmid=?  LIMIT 1 ";

    $sel_result = $adb->pquery($sql, array($fieldname['id']) );
    $res_cnt = $adb->num_rows($sel_result);
    $row = array();
    if($res_cnt > 0) {
        $row = $adb->query_result_rowdata($sel_result, 0);
    }
    // 工作流
    include_once('includes/http/Request.php');
    $tt = $recordModel->getWorkflowsMobile(new Vtiger_Request($fieldname, $fieldname));
    //关联回款信息
    $sql = "SELECT 
			vtiger_receivedpayments.*,
			IFNULL(vtiger_salesorderrayment.laborcost,'0.00') AS laborcost,
			IFNULL(vtiger_salesorderrayment.purchasecost,'0.00') AS purchasecost,
			vtiger_salesorderrayment.remarks as rremarks,
			1 AS israyment
			FROM vtiger_salesorderrayment
			JOIN vtiger_receivedpayments ON(
			  vtiger_salesorderrayment.receivedpaymentsid=vtiger_receivedpayments.receivedpaymentsid
			)
			WHERE  vtiger_salesorderrayment.deleted=0 AND vtiger_salesorderrayment.salesorderid=? ORDER BY vtiger_receivedpayments.receivedpaymentsid";
    $listResult = $adb->pquery($sql, array($fieldname['id']));
    $relevantPaymentInformation = array();
    while($rawData=$adb->fetch_array($listResult)) {
        $relevantPaymentInformation[] =  $rawData;
    }
    //编辑信息列表
    $sql = " SELECT vtiger_formdesign.field, vtiger_salesorder_productdetail_history.*, vtiger_products.productname, vtiger_users.last_name FROM `vtiger_salesorder_productdetail_history` LEFT JOIN vtiger_formdesign ON vtiger_salesorder_productdetail_history.TplId = vtiger_formdesign.formid LEFT JOIN vtiger_products ON vtiger_salesorder_productdetail_history.Productid = vtiger_products.productid LEFT JOIN vtiger_users ON vtiger_salesorder_productdetail_history.EditId = vtiger_users.id WHERE SalesOrderId = ? ";
    $listResult = $adb->pquery($sql, array($fieldname['id']));
    $editInfo = array();
    while($rawData=$adb->fetch_array($listResult)) {
        $editInfo[] =  $rawData;
    }
    /*$result = mysql_escape_string(strip_tags($fieldname['id']));*/
    //工单产品明细
    $sql = " SELECT vtiger_salesorderproductsrel.*, vtiger_products.productname as productnames, vtiger_products.realprice, vtiger_crmentity.createdtime AS crmentitycreatedtime, vtiger_users.last_name , IFNULL(( SELECT vtiger_products.productname FROM vtiger_products WHERE vtiger_products.productid = vtiger_salesorderproductsrel.productcomboid ), '--') AS productcomboname, ( SELECT vtiger_vendor.vendorname FROM vtiger_vendor WHERE vtiger_vendor.vendorid = vtiger_salesorderproductsrel.vendorid ) AS vendorname, ( SELECT vtiger_suppliercontracts.contract_no FROM vtiger_suppliercontracts WHERE vtiger_suppliercontracts.suppliercontractsid = vtiger_salesorderproductsrel.suppliercontractsid ) AS supplier_contract_no FROM vtiger_salesorderproductsrel LEFT JOIN vtiger_products ON vtiger_salesorderproductsrel.productid = vtiger_products.productid LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_products.productid LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id WHERE vtiger_salesorderproductsrel.salesorderid =? ";
    $listResult = $adb->pquery($sql, array($fieldname['id']));
    $productDetail = array();
    $CostSynthesisInformation = array();
    while($rawData=$adb->fetch_array($listResult)) {
        $productDetail[] =  $rawData;
        $CostSynthesisInformation['humanResourcesTotal']+=$rawData['costing'];// 人力成本总和
        $CostSynthesisInformation['externalMiningTotal']+=$rawData['purchasemount'];// 外采成本总和
    }
    // 工单产品循环查询语句
    /* SELECT vtiger_salesorderproductsrel.productid AS vtiger_salesorderproductsrelproductid,vtiger_salesorderproductsrel.productcomboid AS vtiger_salesorderproductsrelproductcomboid,vtiger_salesorderproductsrel.producttype AS vtiger_salesorderproductsrelproducttype,vtiger_salesorderproductsrel.marketprice AS vtiger_salesorderproductsrelmarketprice,vtiger_salesorderproductsrel.costing AS vtiger_salesorderproductsrelcosting,vtiger_salesorderproductsrel.productform AS vtiger_salesorderproductsrelproductform,vtiger_salesorderproductsrel.auditorid AS vtiger_salesorderproductsrelauditorid,vtiger_salesorderproductsrel.audittime AS vtiger_salesorderproductsrelaudittime,vtiger_salesorderproductsrel.createtime AS vtiger_salesorderproductsrelcreatetime,vtiger_salesorderproductsrel.creatorid AS vtiger_salesorderproductsrelcreatorid,vtiger_salesorderproductsrel.salesorderproductsrelstatus AS vtiger_salesorderproductsrelsalesorderproductsrelstatus,vtiger_salesorderproductsrel.remark AS vtiger_salesorderproductsrelremark,vtiger_salesorderproductsrel.relatorids AS vtiger_salesorderproductsrelrelatorids,vtiger_salesorderproductsrel.ownerid AS vtiger_salesorderproductsrelownerid,vtiger_salesorderproductsrel.backerid AS vtiger_salesorderproductsrelbackerid,vtiger_salesorderproductsrel.backtime AS vtiger_salesorderproductsrelbacktime,vtiger_salesorderproductsrel.backwhy AS vtiger_salesorderproductsrelbackwhy,vtiger_salesorderproductsrel.servicecount AS vtiger_salesorderproductsrelservicecount,vtiger_salesorderproductsrel.serviceamount AS vtiger_salesorderproductsrelserviceamount,vtiger_salesorderproductsrel.schedule AS vtiger_salesorderproductsrelschedule,vtiger_salesorderproductsrel.starttime AS vtiger_salesorderproductsrelstarttime,vtiger_salesorderproductsrel.endtime AS vtiger_salesorderproductsrelendtime,vtiger_salesorderproductsrel.servicecontractsid AS vtiger_salesorderproductsrelservicecontractsid,vtiger_salesorderproductsrel.salesorderid AS vtiger_salesorderproductsrelsalesorderid,vtiger_salesorderproductsrel.accountid AS vtiger_salesorderproductsrelaccount_id,vtiger_salesorderproductsrel.purchasemount AS vtiger_salesorderproductsrelpurchasemount,vtiger_salesorderproductsrel.ip AS vtiger_salesorderproductsrelip,vtiger_salesorderproductsrel.domain AS vtiger_salesorderproductsreldomain,vtiger_salesorderproductsrel.space AS vtiger_salesorderproductsrelspace,vtiger_salesorderproductsrel.Tsite AS vtiger_salesorderproductsreltsite,vtiger_salesorderproductsrel.TsiteNew AS vtiger_salesorderproductsreltsitenew,vtiger_salesorderproductsrel.suppliercontractsid AS vtiger_salesorderproductsrelsuppliercontractsid,vtiger_salesorderproductsrel.vendorid AS vtiger_salesorderproductsrelvendorid,vtiger_salesorderproductsrel.salesorderproductsrelid FROM vtiger_salesorderproductsrel WHERE  vtiger_salesorderproductsrel.salesorderproductsrelid=83914  LIMIT 1*/
    return array('SalesOrder'=>$row, 'workflows'=>$tt,'relevantPaymentInformation'=>$relevantPaymentInformation,'editInfo'=>$editInfo,'productDetail'=>$productDetail,'CostSynthesisInformation'=>$CostSynthesisInformation);
}
/*
供应商详情
*/
function oneVendors($fieldname, $userid) {
    global $adb,$current_user;
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
    $operate=setoperate($fieldname['id'],'Vendors');
    $_REQUEST['realoperate']=$operate;
    include_once('modules/Vtiger/Models/Vtiger_DetailView_Model.php');
    $refillApplicationModel = Vtiger_DetailView_Model::getInstance('Vendors', $fieldname['id']);
    $recordModel = $refillApplicationModel->getRecord();
    //供应商详情信息
    $sql = "SELECT
			vtiger_vendor.vendorname AS vtiger_vendorvendorname,
			vtiger_vendor.vendor_no AS vtiger_vendorvendor_no,
			vtiger_vendor.phone AS vtiger_vendorphone,
			vtiger_vendor.email AS vtiger_vendoremail,
			vtiger_vendor.website AS vtiger_vendorwebsite,
			vtiger_vendor.glacct AS vtiger_vendorglacct,
			vtiger_vendor.category AS vtiger_vendorcategory,
			vtiger_crmentity.createdtime AS vtiger_crmentitycreatedtime,
			vtiger_crmentity.modifiedtime AS vtiger_crmentitymodifiedtime,
			vtiger_crmentity.modifiedby AS vtiger_crmentitymodifiedby,
			vtiger_vendor.street AS vtiger_vendorstreet,
			vtiger_vendor.pobox AS vtiger_vendorpobox,
			vtiger_vendor.city AS vtiger_vendorcity,
			vtiger_vendor.state AS vtiger_vendorstate,
			vtiger_vendor.postalcode AS vtiger_vendorpostalcode,
			vtiger_vendor.allowtransaction AS vtiger_vendorallowtransaction,
			vtiger_vendor.country AS vtiger_vendorcountry,
			vtiger_crmentity.description AS vtiger_crmentitydescription,
			vtiger_crmentity.smownerid AS vtiger_crmentityassigned_user_id,
            (SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS label FROM vtiger_users WHERE id = vtiger_crmentity.smownerid limit 1 ) as personincharge,	 
			vtiger_vendor.industry AS vtiger_vendorindustry,
			vtiger_vendor.abbreviation AS vtiger_vendorabbreviation,
			vtiger_vendor.linkman AS vtiger_vendorlinkman,
			vtiger_vendor.linkphone AS vtiger_vendorlinkphone,
			vtiger_vendor.fax AS vtiger_vendorfax,
			vtiger_vendor.qq AS vtiger_vendorqq,
			vtiger_vendor.codenumber AS vtiger_vendorcodenumber,
			vtiger_vendor.thearea AS vtiger_vendorthearea,
			vtiger_vendor.address AS vtiger_vendoraddress,
			vtiger_vendor.depositbank AS vtiger_vendordepositbank,
			vtiger_vendor.accountnumber AS vtiger_vendoraccountnumber,
			vtiger_vendor.taxpayers_no AS vtiger_vendortaxpayers_no,
			vtiger_vendor.registeraddress AS vtiger_vendorregisteraddress,
			vtiger_vendor.telephone AS vtiger_vendortelephone,
			vtiger_vendor.file AS vtiger_vendorfile,
			vtiger_vendor.vendortype AS vtiger_vendorvendortype,
			vtiger_vendor.vendorscore AS vtiger_vendorvendorscore,
			vtiger_vendor.vendorstate AS vtiger_vendorvendorstate,
			vtiger_vendor.vendorscoredate AS vtiger_vendorvendorscoredate,
			vtiger_vendor.mainplatform AS vtiger_vendormainplatform,
			vtiger_vendor.workflowsid AS vtiger_vendorworkflowsid,
			vtiger_vendor.modulestatus AS vtiger_vendormodulestatus,
			vtiger_vendor.workflowstime AS vtiger_vendorworkflowstime,
			vtiger_vendor.workflowsnode AS vtiger_vendorworkflowsnode,
			vtiger_vendor.bankaccount AS vtiger_vendorbankaccount,
			vtiger_vendor.bankname AS vtiger_vendorbankname,
			vtiger_vendor.banknumber AS vtiger_vendorbanknumber,
			vtiger_vendor.invoicemode AS vtiger_vendorinvoicemode,
			vtiger_vendor.vendorrank AS vtiger_vendorvendorrank,
			vtiger_vendor.parentid AS vtiger_vendorparentid,
			vtiger_vendor.bankcode AS vtiger_vendorbankcode,
			vtiger_crmentity.deleted
		FROM
			vtiger_crmentity
		LEFT JOIN vtiger_vendor ON vtiger_vendor.vendorid = vtiger_crmentity.crmid
		WHERE  vtiger_crmentity.crmid =? ";

    $sel_result = $adb->pquery($sql, array($fieldname['id']) );
    $res_cnt = $adb->num_rows($sel_result);
    $row = array();
    if($res_cnt > 0) {
        $row = $adb->query_result_rowdata($sel_result, 0);
    }
    //回款明细
    $sql = "SELECT * FROM vtiger_refillapprayment WHERE deleted=0 AND refillapplicationid=?";
    $listResult = $adb->pquery($sql, array($fieldname['id']));
    $res_rf = array();
    while($rawData=$adb->fetch_array($listResult)) {
        $res_rf[] =  $rawData;
    }
//  银行账户列表
    $backInfoSQL = " SELECT vendorbankid,vendorid,bankaccount,bankname,banknumber,bankcode,allowtransaction,deleted,createdtime,createdid,deletedid,deletedtime FROM vtiger_vendorbank WHERE deleted=0 and vendorid=? ";
    $bankList = $listResult = $adb->pquery($backInfoSQL, array($fieldname['id']));
    $bankInfoList=array();
    while($rawData=$adb->fetch_array($bankList)) {
        $bankInfoList[] =  $rawData;
    }
    // 产品返点列表
    $sql = " select * from vtiger_vendorsrebate LEFT JOIN vtiger_products ON vtiger_products.productid=vtiger_vendorsrebate.productid where vtiger_vendorsrebate.vendorid=? AND vtiger_vendorsrebate.deleted=0 ";
    $listResult = $adb->pquery($sql, array($fieldname['id']));
    $res = array();
    while($rawData=$adb->fetch_array($listResult)) {
        $res[] =  $rawData;
    }
    // 工作流
    include_once('includes/http/Request.php');
    $tt = $recordModel->getWorkflowsMobile(new Vtiger_Request($fieldname, $fieldname));
    $refundlist=array();
    if(in_array($recordModel->get('rechargesource'),array('Vendors','Accounts'))){
        $refundlists=$recordModel->getRubricreChargesheet($fieldname['id']);
        foreach($refundlists as $key=>$value){
            $refundlist['item'.$key]=$value;
        }
    }
    $vendorlist=array();
    if($recordModel->get('rechargesource')=='PACKVENDORS'){
        $vendorlist=$recordModel->getDetailVendorList($fieldname['id']);
    }
    return array('Vendors'=>$row,'productReturnPoint'=>$res, 'workflows'=>$tt,'bankInfoList'=>$bankInfoList,'refundlist'=>$refundlist,'vendorlist'=>$vendorlist);
}
/*
充值申请单详情
*/
function oneRefillApplication($fieldname, $userid) {
	global $adb,$current_user;
	$user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
    $operate=setoperate($fieldname['id'],'RefillApplication');
    $_REQUEST['realoperate']=$operate;
    include_once('modules/Vtiger/Models/Vtiger_DetailView_Model.php');
	$refillApplicationModel = Vtiger_DetailView_Model::getInstance('RefillApplication', $fieldname['id']);
	$recordModel = $refillApplicationModel->getRecord();

	/*$sql = "SELECT vtiger_refillapplication.refillapplicationno,vtiger_refillapplication.servicecontractsid,
    (SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id = vtiger_crmentity.smcreatorid) as last_name,
    vtiger_refillapplication.accountid,vtiger_refillapplication.file, ( SELECT vtiger_servicecontracts.contract_no FROM vtiger_servicecontracts WHERE vtiger_servicecontracts.servicecontractsid = vtiger_refillapplication.servicecontractsid ) AS contract_no,
    ( SELECT vtiger_account.accountname FROM vtiger_account WHERE vtiger_account.accountid = vtiger_refillapplication.accountid ) AS accountname,
    vtiger_refillapplication.remarks,vtiger_crmentity.createdtime AS refillcreatedtime, IF(vtiger_rechargesheet.rechargetype='c_recharge','充值','退款') AS t_rechargetype,IF(vtiger_refillapplication.customertype='ChannelCustomers','渠道','直客') AS t_customertype,IF(vtiger_rechargesheet.rechargetypedetail='renew','续费',IF(vtiger_rechargesheet.rechargetypedetail='OpenAnAccount','开户','')) AS t_rechargetypedetail,vtiger_refillapplication.customertype,
    vtiger_refillapplication.rechargesource,IF(vtiger_refillapplication.customeroriginattr='free','自有','非自有') AS t_customeroriginattr,vtiger_refillapplication.totalrecharge,vtiger_refillapplication.actualtotalrecharge,
    IF(vtiger_refillapplication.iscontracted='on','是','否') AS t_iscontracted,vtiger_refillapplication.grossadvances,
    vtiger_rechargesheet.* FROM vtiger_refillapplication
    LEFT JOIN vtiger_rechargesheet ON vtiger_refillapplication.refillapplicationid = vtiger_rechargesheet.refillapplicationid
    LEFT JOIN vtiger_crmentity ON(vtiger_refillapplication.refillapplicationid=vtiger_crmentity.crmid)
    WHERE vtiger_refillapplication.refillapplicationid =? AND vtiger_rechargesheet.isentity = 1 LIMIT 1";*/
    $sql = "SELECT vtiger_refillapplication.refillapplicationno,vtiger_refillapplication.servicecontractsid,
    (SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id = vtiger_crmentity.smcreatorid) as last_name,
    vtiger_refillapplication.accountid,vtiger_refillapplication.file, ( SELECT vtiger_servicecontracts.contract_no FROM vtiger_servicecontracts WHERE vtiger_servicecontracts.servicecontractsid = vtiger_refillapplication.servicecontractsid ) AS contract_no,
    ( SELECT vtiger_account.accountname FROM vtiger_account WHERE vtiger_account.accountid = vtiger_refillapplication.accountid ) AS accountname,
    vtiger_refillapplication.remarks,vtiger_crmentity.createdtime AS refillcreatedtime, IF(vtiger_refillapplication.customertype='ChannelCustomers','渠道','直客') AS t_customertype,vtiger_refillapplication.customertype,
    vtiger_refillapplication.rechargesource,IF(vtiger_refillapplication.customeroriginattr='free','自有','非自有') AS t_customeroriginattr,vtiger_refillapplication.totalrecharge,vtiger_refillapplication.actualtotalrecharge,
    IF(vtiger_refillapplication.iscontracted='alreadySigned','已签订','未签订') AS t_iscontracted,vtiger_refillapplication.grossadvances,vtiger_refillapplication.servicesigndate,
    IF(vtiger_refillapplication.paymentperiod='payfirst','现付款','后付款') AS t_paymentperiod,
    vtiger_refillapplication.humancost,vtiger_refillapplication.purchasecost,
    vtiger_refillapplication.expecteddatepayment,
    vtiger_refillapplication.expectedpaymentdeadline,
    vtiger_refillapplication.cashconsumptiontotal,
    vtiger_refillapplication.cashincreasetotal,
    vtiger_refillapplication.bankcode,
    vtiger_refillapplication.modulestatus,
    vtiger_refillapplication.totalcashin,
	vtiger_refillapplication.totalcashtransfer,
	vtiger_refillapplication.totalturnoverofaccount,
	vtiger_refillapplication.totaltransfertoaccount,
    vtiger_vendor.vendorname,vtiger_refillapplication.bankaccount,vtiger_refillapplication.bankname,vtiger_refillapplication.banknumber,vtiger_refillapplication.totalreceivables,vtiger_refillapplication.expcashadvances,
    vtiger_refillapplication.remarks,vtiger_refillapplication.contractamountrecharged,vtiger_refillapplication.changecontracttype,vtiger_refillapplication.oldrechargesource,vtiger_refillapplication.changesnumber,
    vtiger_refillapplication.contractamount,vtiger_refillapplication.newcontract_no,vtiger_refillapplication.newaccount_name,vtiger_refillapplication.newcustomertype,vtiger_refillapplication.newiscontracted,vtiger_refillapplication.newservicesigndate,vtiger_refillapplication.newcontractamount,vtiger_refillapplication.refillapplicationid, 
    (SELECT vendorname FROM `vtiger_vendor` WHERE `vendorid` =vtiger_refillapplication.vendorid ) as vendorname ,
    vtiger_refillapplication.conversiontype
    FROM vtiger_refillapplication
    LEFT JOIN vtiger_vendor ON(vtiger_vendor.vendorid=vtiger_refillapplication.vendorid)
    LEFT JOIN vtiger_crmentity ON(vtiger_refillapplication.refillapplicationid=vtiger_crmentity.crmid)
    WHERE vtiger_refillapplication.refillapplicationid =?";

	$sel_result = $adb->pquery($sql, array($fieldname['id']) );
	$res_cnt = $adb->num_rows($sel_result);
	$row = array();
	if($res_cnt > 0) {
	    $row = $adb->query_result_rowdata($sel_result, 0);
	}
    $refillapplicationList=array();
    // 如果是合同变更申请
	if($row['rechargesource']=='contractChanges'){
        $result = $adb->pquery('SELECT r.refillapplicationid,r.refillapplicationno,r.rechargesource,IFNULL((select CONCAT(last_name,\'[\',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),\'\'),\']\',(if(`status`=\'Active\',\'\',\'[离职]\'))) as last_name from vtiger_users where c.smownerid=vtiger_users.id),\'--\') as smownerid,c.createdtime,r.grossadvances,r.actualtotalrecharge,r.totalreceivables  FROM vtiger_refillapplication as r  LEFT JOIN vtiger_crmentity as c ON r.refillapplicationid = c.crmid WHERE r.refillapplicationid IN( SELECT detail_refillapplicationid FROM vtiger_changecontract_detail WHERE vtiger_changecontract_detail.refillapplicationid=? )', array($row['refillapplicationid']));
        while ($rowData=$adb->fetch_array($result)){
            $refillapplicationList[]= $rowData;
        }
	}


	/*获得垫款*/
	/*$accountid =  $row['accountid'];
	$result = $adb->pquery("SELECT advancesmoney  FROM vtiger_account WHERE accountid =? ",array($accountid));
	$advancesmoney = 0;
	if ($result && $adb->num_rows($result) > 0) {
		$rowData = $adb->fetch_array($result);
		$advancesmoney =  $rowData['advancesmoney'];
	}*/

	//回款明细
    $sql = "SELECT * FROM vtiger_refillapprayment WHERE deleted=0 AND refillapplicationid=?";
    $listResult = $adb->pquery($sql, array($fieldname['id']));
    $res_rf = array();
    while($rawData=$adb->fetch_array($listResult)) {
        $res_rf[] =  $rawData;
    }
	// 充值明细
	/*$sql = "SELECT vtiger_rechargesheet.*, IF(vtiger_rechargesheet.rechargetypedetail='renew','续费',IF(vtiger_rechargesheet.rechargetypedetail='OpenAnAccount','开户','')) AS t_rechargetypedetail,
    IF ( rechargetypedetail = 'renew', '续费', '开户') AS t_rechargetypedetail,
    IF ( havesignedcontract = 'on', '是', '否') AS t_havesignedcontract, IF ( isprovideservice = 'on', '是', '否') AS t_isprovideservice
 FROM vtiger_rechargesheet WHERE deleted=0 AND refillapplicationid =? AND isentity = 0";*/
    $sql = "SELECT vtiger_rechargesheet.*, IF(vtiger_rechargesheet.rechargetypedetail='renew','续费',IF(vtiger_rechargesheet.rechargetypedetail='OpenAnAccount','开户','')) AS t_rechargetypedetail,
    IF ( rechargetypedetail = 'renew', '续费', '开户') AS t_rechargetypedetail,
    IF ( havesignedcontract = 'alreadySigned', '已签订', '未签订') AS t_havesignedcontract, IF ( isprovideservice = '1', '是', '否') AS t_isprovideservice,IF ( customeroriginattr = 'free', '自有', '非自有') AS t_customeroriginattr,
    IF ( rebatetype = 'CashBack', '返现', '返货') AS t_rebatetype,
    IF ( accountrebatetype = 'CashBack', '返现', '返货') AS t_accountrebatetype,
    vtiger_suppliercontracts.contract_no,vtiger_products.productname AS topplatform
  FROM vtiger_rechargesheet
  LEFT JOIN vtiger_suppliercontracts ON(vtiger_suppliercontracts.suppliercontractsid=vtiger_rechargesheet.suppliercontractsid)
  LEFT JOIN vtiger_products ON(vtiger_products.productid=vtiger_rechargesheet.productid)
  WHERE vtiger_rechargesheet.deleted=0 AND vtiger_rechargesheet.refillapplicationid =?";
	$listResult = $adb->pquery($sql, array($fieldname['id']));
	$res = array();
	while($rawData=$adb->fetch_array($listResult)) {
   		$res[] =  $rawData;
    }

    // 工作流
    include_once('includes/http/Request.php');
    $tt = $recordModel->getWorkflowsMobile(new Vtiger_Request($fieldname, $fieldname));
    $refundlist=array();
    if(in_array($recordModel->get('rechargesource'),array('Vendors','Accounts'))){
        $refundlists=$recordModel->getRubricreChargesheet($fieldname['id']);
        foreach($refundlists as $key=>$value){
            $refundlist['item'.$key]=$value;
		}
    }

    $vendorlist=array();
    if($recordModel->get('rechargesource')=='PACKVENDORS'){
        $vendorlist=$recordModel->getDetailVendorList($fieldname['id']);
	}
	//return array('refillApplication'=>$row,'rechargesheet'=>$res, 'workflows'=>$tt, 'advancesmoney'=>$advancesmone);
    return array('refillApplication'=>$row,'rechargesheet'=>$res, 'workflows'=>$tt,'refillapprayment'=>$res_rf,'refundlist'=>$refundlist,'vendorlist'=>$vendorlist,'refillapplicationList'=>$refillapplicationList);
}
//得是否有审核权限（媒体账户管理和媒体外采账户管理的 申请编辑）点编辑时 提示是否可以编辑
function getApplicationAuthority($fieldname, $userid){
    global $adb,$current_user;

    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
    $operate=setoperate($fieldname['id'],$fieldname['module']);
    $_REQUEST['realoperate']=$operate;
    include_once('modules/Vtiger/Models/Vtiger_DetailView_Model.php');
    $accountPlatformModel = Vtiger_DetailView_Model::getInstance($fieldname['module'], $fieldname['id']);
    $recordModel = $accountPlatformModel->getRecord();
    // 判断是否可以显示’申请编辑‘ 按钮  根据返回的$isPower
    if($recordModel->entity->column_fields['modulestatus']=='c_complete'){
    	// 如果该记录中的accountid 存在 判断 account 的负责人是否是当前用户id  如果是 则可以申请编辑
        if($recordModel->get('accountid')>0){
            $accountRecordModel = Vtiger_Record_Model::getInstanceById($recordModel->get('accountid'), "Accounts");
            $accountidflag = $accountRecordModel->get('assigned_user_id') == $current_user->id ? true : false;
        }
        if ($recordModel->entity->column_fields['assigned_user_id'] == $userid || $accountidflag || $recordModel->personalAuthority($fieldname['module'],"doedit")) {
            $isPower = true;
        }else{
            $isPower = false;
		}
	}else{
        $isPower = false;
	}

    //下面判断点击编辑时否可以去编辑
    $isPosibbleToEdit = 'true';
    $message='';
	$accountPlatformRecordModels=Vtiger_Record_Model::getInstanceById($fieldname['id'],$fieldname['module']);
	if(!empty($accountPlatformRecordModels)&&$accountPlatformRecordModels){
		$module=$accountPlatformRecordModels->getData();
		$moduleStatus=$module['modulestatus'];
		// 判断是否是不可以编辑的状态 根据 $isPosibbleToEdit 状态
		if(!getIsEditOrDel('edit',$moduleStatus)){
            include_once('languages/zh_cn/Vtiger.php');
			$isPosibbleToEdit='false';
			$message ='状态 '.$languageStrings[$moduleStatus].' 不允许当前的操作';
		}
		//$languageStrings[$moduleStatus]
	}
    return array(0=>$isPower,1=>array('status'=>$isPosibbleToEdit,'message'=>$message));
}
function oneAccountPlatform($fieldname, $userid) {
    global $adb;
    include_once('languages/zh_cn/AccountPlatform.php');
    $result=getDetail('AccountPlatform',$fieldname,$userid);
    $result['row']['accountrebatetype']=$languageStrings[$result['row']['accountrebatetype']];
    $result['row']['rebatetype']=$languageStrings[$result['row']['rebatetype']];
    $result['row']['customeroriginattr']=$languageStrings[$result['row']['customeroriginattr']];
    $result['row']['isprovideservice']=$languageStrings[$result['row']['isprovideservice']];
    $query="SELECT * FROM vtiger_crmentity WHERE crmid in(?,?,?,?)";
    $resultData=$adb->pquery($query,array($result['row']['accountid'],
        $result['row']['vendorid'],
        $result['row']['productid'],
        $result['row']['suppliercontractsid']));
    $arr=array();
    while($row=$adb->fetch_array($resultData)){
        $arr[$row['crmid']]=$row['label'];
    }
    $result['row']['accountname']=$arr[$result['row']['accountid']];
    $result['row']['vendorname']=$arr[$result['row']['vendorid']];
    $result['row']['productname']=$arr[$result['row']['productid']];
    $result['row']['suppliercontractsname']=$arr[$result['row']['suppliercontractsid']];
    // 查询负责人名称
    $sql = "SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS label FROM vtiger_users WHERE id = ? limit 1 ";
    $recharge =  $adb->pquery($sql,array($result['row']['assigned_user_id']));
    $recharge =  $adb->query_result_rowdata($recharge,0);
    $result['row']['assigned_user_id']=$recharge['label'];
    return $result;
}
function oneProductProvider($fieldname, $userid) {
    global $adb;
    include_once('languages/zh_cn/ProductProvider.php');
    $result=getDetail('ProductProvider',$fieldname,$userid);
    $result['row']['accountrebatetype']=$languageStrings[$result['row']['accountrebatetype']];
    $result['row']['rebatetype']=$languageStrings[$result['row']['rebatetype']];
    $result['row']['customeroriginattr']=$languageStrings[$result['row']['customeroriginattr']];
    $result['row']['isprovideservice']=$languageStrings[$result['row']['isprovideservice']];
    $query="SELECT * FROM vtiger_crmentity WHERE crmid in(?,?,?,?)";
    $resultData=$adb->pquery($query,array($result['row']['accountid'],
        $result['row']['vendorid'],
        $result['row']['productid'],
        $result['row']['suppliercontractsid']));
    $arr=array();
    while($row=$adb->fetch_array($resultData)){
        $arr[$row['crmid']]=$row['label'];
    }
    $result['row']['accountname']=$arr[$result['row']['accountid']];
    $result['row']['vendorname']=$arr[$result['row']['vendorid']];
    $result['row']['productname']=$arr[$result['row']['productid']];
    $result['row']['suppliercontractsname']=$arr[$result['row']['suppliercontractsid']];
    // 查询负责人名称
    $sql = "SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS label FROM vtiger_users WHERE id = ? limit 1 ";
    $recharge =  $adb->pquery($sql,array($result['row']['assigned_user_id']));
    $recharge =  $adb->query_result_rowdata($recharge,0);
    $result['row']['assigned_user_id']=$recharge['label'];
    return $result;
}
function oneContractGuarantee($fieldname, $userid) {
    global $adb;
    include_once('languages/zh_cn/ContractGuarantee.php');
    $result=getDetail('ContractGuarantee',$fieldname,$userid);
    $query="SELECT label FROM vtiger_crmentity WHERE crmid=? LIMIT 1";
    $resultData=$adb->pquery($query,array($result['row']['contractid']));
    $resultDatad=$adb->raw_query_result_rowdata($resultData,0);
	$result['row']['contractname']=$resultDatad['label'];
    // 查询负责人名称
    $sql = "SELECT CONCAT( last_name, '[', IFNULL(( SELECT departmentname FROM vtiger_departments WHERE departmentid = ( SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1 )), '' ), ']', ( IF ( `status` = 'Active', '', '[离职]' ))) AS label FROM vtiger_users WHERE id = ? limit 1 ";
    $recharge =  $adb->pquery($sql,array($result['row']['assigned_user_id']));
    $recharge =  $adb->query_result_rowdata($recharge,0);
    $result['row']['assigned_user_id']=$recharge['label'];
    return $result;
}
function getDetail($moduleName,$fieldname, $userid) {
    global $adb,$current_user;
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);

    include_once('modules/Vtiger/Models/Vtiger_DetailView_Model.php');
    $refillApplicationModel = Vtiger_DetailView_Model::getInstance($moduleName, $fieldname['id']);
    $recordModel = $refillApplicationModel->getRecord();
    $row = $recordModel->getEntity()->column_fields;
    // 工作流
    include_once('includes/http/Request.php');
    $tt = $recordModel->getWorkflowsMobile(new Vtiger_Request($fieldname, $fieldname));
    return array('row'=>$row, 'workflows'=>$tt);
}
function findParentDepartment($userid){
	global $adb,$current_user;
	$user = new Users();
	$current_user = $user->retrieveCurrentUserInfoFromFile($userid);

	$sql = "SELECT parentdepartment FROM `vtiger_departments` WHERE departmentid = (SELECT departmentid FROM `vtiger_user2department` WHERE userid=? LIMIT 1)";
	$listResult = $adb->pquery($sql, array($current_user->id));
	$res_cnt = $adb->num_rows($listResult);
	$row = array();
	if($res_cnt > 0) {
	    $row = $adb->query_result_rowdata($listResult, 0);
	}
	return array($row);

}

function sendMailAgent($fieldname, $userid) {
	global $adb,$current_user;
	$user = new Users();
	$current_user = $user->retrieveCurrentUserInfoFromFile($userid);
	$current_user->reports_to_id;

	include_once('modules/Vtiger/models/Record.php');
	$sql = "SELECT * FROM `vtiger_users` WHERE id=? LIMIT 1";
	$sel_result = $adb->pquery($sql, array($current_user->reports_to_id));
	$res_cnt = $adb->num_rows($sel_result);
	$agent_email = '';
	$agent_last_name = '';
	if($res_cnt > 0) {
		$row = $adb->query_result_rowdata($sel_result, 0);
		$agent_email = $row['email1'];
		$agent_last_name = $row['last_name'];
	}
	/*
	$sql = "SELECT * FROM `vtiger_users` WHERE id=? LIMIT 1";
	$sel_result = $adb->pquery($sql, array($current_user->id));
	$res_cnt = $adb->num_rows($sel_result);
	$email = '';
	$last_name = '';
	$department = '';
	if($res_cnt > 0) {
		$row = $adb->query_result_rowdata($sel_result, 0);
		$email = $row['email1'];
		$last_name = $row['last_name'];
		$department = $row['department'];
	}*/
    $email = $current_user->email1;
    $last_name = $current_user->last_name;
    $department = $current_user->department;

	$Subject = !empty($fieldname['Subject'])?$fieldname['Subject']:'T云激活码领取通知';

	$productArr = array(
		'c5f54cfc-36b5-11e7-a335-5254003c6d38'=>'T云X1双推(首购)',
		'caa9b301-36b5-11e7-a335-5254003c6d38'=>'T云X2双推(首购)',
		'c83cce8e-4993-11e7-a335-5254003c6d38'=>'T云V2双推版',
        '512cb5c8-7609-11e7-a335-5254003c6d38'=>'T云系列S1(首购)',
        '512cb5e6-7609-11e7-a335-5254003c6d38'=>'T云系列S1Plus(首购)',
        '512cb609-7609-11e7-a335-5254003c6d38'=>'T云系列S2(首购)',
        'fb01732e-4296-11e6-ad98-00155d069461' => 'T云系列V(首购)',
        'fafdc07c-4296-11e6-ad98-00155d069461' => 'T云系列V1(首购)',
        'fb016797-4296-11e6-ad98-00155d069461' => 'T云系列V2(首购)',
        'fb016866-4296-11e6-ad98-00155d069461' => 'T云系列V3(首购)',
        'eb472d25-f1b1-11e6-a335-5254003c6d38' => 'T云系列V3Plus(首购)',
        'fb0174bf-4296-11e6-ad98-00155d069461' => 'T云系列V5(首购)',
        'b96c4ad7-27f3-4526-ab43-609d8dbd1170' => 'T云系列V5Plus(首购)',
        'ad0bee9e-516f-11e6-a2ff-52540013dadb' => 'T云系列V6(首购)',
        'eb480f94-f1b1-11e6-a335-5254003c6d38' => 'T云系列V8(首购)',
        'a36a9cac-516f-11e6-a2ff-52540013dadb' => 'T云系列发布宝(首购)',
        '512cb5c8-7609-11e7-a335-5254003c6d38'=>'T云系列S1(首购)',
        '512cb5e6-7609-11e7-a335-5254003c6d38'=>'T云系列S1Plus(首购)',
        '512cb609-7609-11e7-a335-5254003c6d38'=>'T云系列S2(首购)',
        'da1832bc-bc86-459f-a14c-285b2f69e1d3'=>'T云系列S3小程序建站（首购）',
        '9bb55818-37ba-49cc-9c5b-493b68a19c21'=>'小程序电商标准版',
        'b9345acf-452d-4746-8533-4c59b6b02df8'=>'小程序电商旗舰版',
        '0fea4ea4-78e3-438b-9b4f-1792f60bea06'=>'T云系列旗舰版',
		"b8d18d40-2b9d-42da-9096-b2d780a2c49b"=>"T云臻推宝（首购）",
		"b9feca81-98cf-4210-9168-405d81c3e66b"=>"T云词霸（首购）",
		"5d1736e9-6b26-4932-8e74-0377374fa7ce"=>"T云随推（首购）",
		"1c72a0e4-530b-4c46-b186-60b1a9251876"=>"T云宝盟（首购）",
		"7aa589be-548d-48b5-a75f-e09cb0f06156"=>"T云系列F（首购)",
		"cde37624-c07f-49e2-ad8c-621da73e9566"=>"T云系列F1（首购)",
		"81c8a558-f03d-475c-9430-da74c6e25d38"=>"T云系列F2（首购)",
		"8d6d07f7-cd42-4df4-94cd-cf6a312a6d80"=>"T云系列F3（首购)",
		"b95fdcf3-e59f-47d9-8d4e-b1b3d0d93f15"=>"T云系列F5（首购)",
	);
	$productid = $fieldname['productid'];
	$productName = $productArr[$productid];

    $oldproductid = $fieldname['oldproductid'];
    $oldProductName = $productArr[$oldproductid];
	/*$search=array('last_name',
		'department',
		'customername',
        'contractno',
		'productName',
		'productlife',
		'nowTime',
        'expiredate',
        'oldProductName',
		'mobile');
	$replce=array($last_name,
			$department,
			$fieldname['customername'],
            $fieldname['contractno'],
			$productName,
			$fieldname['productlife'],
			$fieldname['nowTime'],
            $fieldname['expiredate'],
            $oldProductName,
			$fieldname['mobile']);*/

    $type = $fieldname['type'];
    if($type == '1'){
    	//升级
        $body =  !empty($fieldname['body'])?$fieldname['body']:"员工：{$last_name}<br>部门：{$department}<br>客户：{$fieldname['customername']}<br>合同编号：{$fieldname['contractno']}<br>原版本：{$oldProductName}<br>升级版本：{$productName}<br>升级年限：{$fieldname['productlife']} 年<br>升级时间：{$fieldname['nowTime']}<br>到期时间：{$fieldname['expiredate']}";
	}else if($type == '2'){
    	//续费
        $body =  !empty($fieldname['body'])?$fieldname['body']:"员工：{$last_name}<br>部门：{$department}<br>客户：{$fieldname['customername']}<br>合同编号：{$fieldname['contractno']}<br>续费版本：{$oldProductName}<br>续费年限：{$fieldname['productlife']} 	年<br>续费时间：{$fieldname['nowTime']}<br>到期时间：{$fieldname['expiredate']}";
    }else if($type == '3') {
        //另购
        $body = !empty($fieldname['body']) ? $fieldname['body'] : "员工：{$last_name}<br>部门：{$department}<br>客户：{$fieldname['customername']}<br>合同编号：{$fieldname['contractno']}<br>另购版本：{$oldProductName} <br>另购时间：nowTime";
    }else if($type == '4'){
		//降级
		$body =  !empty($fieldname['body'])?$fieldname['body']:"员工：{$last_name}<br>部门：{$department}<br>客户：{$fieldname['customername']}<br>合同编号：{$fieldname['contractno']}<br>原版本：{$oldProductName}<br>降级版本：{$productName}<br>降级年限：{$fieldname['productlife']} 年<br>降级时间：{$fieldname['nowTime']}<br>到期时间：{$fieldname['expiredate']}";
    }else{
        $body =  !empty($fieldname['body'])?$fieldname['body']:"员工：{$last_name}<br>部门：{$department}<br>客户：{$fieldname['customername']}<br>合同编号：{$fieldname['contractno']}<br>购买版本：{$productName}<br>购买年限：{$fieldname['productlife']} 	年<br>购买时间：{$fieldname['nowTime']}<br>客户手机：{$fieldname['mobile']}";
	}
	//$body=str_replace($search,$replce,$body);

    //另购服务
    $buyserviceinfo = $fieldname['buyserviceinfo'];
    $s_buyserviceinfo = "另购服务<br><table style='width:30%;border-spacing: 0;border-collapse: collapse;'><tr style='height: 35px;'><th style='background-color: #eee;text-align: center;border: 1px solid #ccc;width: 50%;font-size: 14px;'>服务名称</th><th style='background-color: #eee;text-align: center;border: 1px solid #ccc;width: 25%;font-size: 14px;'>数量</th></tr><tbody>";
	if($buyserviceinfo && count($buyserviceinfo)>0){
		for($i=0;$i<count($buyserviceinfo);$i++){
            $s_buyserviceinfo .= "<tr style='height: 35px;'><td style='text-align: center;border-radius: 4px;border: 1px solid #ccc;height: 34px;padding: 5px 2px;font-size: 14px;'>".$buyserviceinfo[$i]["servicename_display"]."</td><td style='text-align: center;border-radius: 4px;border: 1px solid #ccc;height: 34px;padding: 5px 2px;font-size: 14px;'>".$buyserviceinfo[$i]["buycount_display"]."</td></tr>";
		}
	}else{
        $s_buyserviceinfo .= "<tr style='height: 35px;'><td style='text-align: center;border-radius: 4px;border: 1px solid #ccc;height: 34px;padding: 5px 2px;font-size: 14px;'>无</td><td style='text-align: center;border-radius: 4px;border: 1px solid #ccc;height: 34px;padding: 5px 2px;font-size: 14px;'>无</td></tr>";
	}
    $s_buyserviceinfo .= "</tbody></table>";
    $body.="<br><br>".$s_buyserviceinfo;

	$upmail=!empty($fieldname['agent_mail'])?$fieldname['agent_mail']:$email;
	$address = array(
			array('mail'=>$upmail, 'name'=>''),//营总监
			array('mail'=>$email, 'name'=>$last_name),//负责人
			array('mail'=>$agent_email, 'name'=>$agent_last_name),//负责人上级
	);
	if($userid==1179){
		unset($address[2]);
	}
	//=====测试==========================
    $address = array(
        array('mail'=>'chunli.gao@71360.com', 'name'=>$last_name),//负责人
        array('mail'=>$agent_email, 'name'=>$agent_last_name),//负责人上级
    );
	//===================================
    _logs(array('发送邮件地址：', $address));
    _logs(array('邮件内容：'.$body));
	$tt = Vtiger_Record_Model::sendMail($Subject,$body,$address);
    _logs(array('发送邮件完成：', $tt));

    //保存调用T云接口返回数据
    $recordModel=Vtiger_Record_Model::getCleanInstance('ActivationCode');
    $arr_data = array(
        'contractno'=>$fieldname['contractno'],
        "classtype"=>$fieldname['classtype'],
        "tyunurl"=>'发送邮件',
        "crminput"=>json_encode($address),
        "tyunoutput"=>json_encode($tt),
        "success"=>1);
    $recordModel->saveTyunResposeData($arr_data);

	return array($tt);
}

/*回款*/
function receiveReceivedPayments($fieldname, $userid){
	global $adb;


    $date=date('Y-m-d');

    $total = 0;
    $pageCount = 10;

    $sql = "SELECT paytitle,owncompany,reality_date,unit_price,createdtime FROM `vtiger_receivedpayments` WHERE relatetoid=? ORDER BY createdtime DESC";
    $listResult = $adb->pquery($sql, array($fieldname['id']));
    $res = array();
    while($rawData=$adb->fetch_array($listResult)) {
    	$res[] =  $rawData;
    }
	return array('ReceivedPayments'=>$res);

}
function tyunWebGetAccount($fieldname, $userid){
	global $adb;
	$accountname=$fieldname['accountname'];
	$accountid=$fieldname['accountid'];
	$accountname=trim($accountname);
	$label=preg_replace('/\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;|&quot;|　|&apos;|&amp;|&lt;|&gt;|&#039;|&ldquo;|&rdquo;|&lsquo;|&rsquo;|&hellip;|\“|\”|\‘|\〉|\〈|\’|\〖|\〗|\【|\】|\、|\·|\…|\——|\＋|\－|\＝|\,|\<|\.|\>|\/|\?|\;|\:|\\\'|\"|\[|\{|\]|\}|\\|\||\`|\~|\!|\@|\#|\$|\%|\^|\\&|\*|\(|\)|\-|\_|\=|\+|\，|\＜|\．|\＞|\／|\？|\；|\：|\＇|\＂|\［|\{|\］|\}|\＼|\｜|\｀|\￣|\！|\＠|\#|\＄|\％|\＾|\＆|\＊|\（|\）|\－|\＿|\＝|\＋|\，|\《|\．|\》|\、|\？|\；|\：|\’|\”|\【|\｛|\】|\｝|\＼|\｜|\·|\～|\！|\＠|\＃|\￥|\％|\…|\＆|\×|\（|\）|\－|\——|\—|\＝|\＋|\，|\《|\。|\》|\、|\？|\；|\：|\‘|\“|\【|\｛|\】|\｝|\、|\||\·|\~|\！|\@|\#|\￥|\%|\&|\*|\（|\）|\-|\——|\=|\+/u','',$accountname);
	$labelname=strtoupper($label);
	$sql = "SELECT
				vtiger_crmentity.label,
				vtiger_crmentity.crmid
			FROM
				vtiger_uniqueaccountname
			LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_uniqueaccountname.accountid
			WHERE
				deleted = 0
			AND vtiger_uniqueaccountname.accountname =?
			LIMIT 1";
	$listResult = $adb->pquery($sql, array($labelname));
	if($adb->num_rows($listResult)){
		$resultData=$adb->query_result_rowdata($listResult,0);
		$res=array("cid"=>$resultData['crmid'],'accountname'=>$resultData['label']);
	}else{

		include_once('includes/http/Request.php');
		include_once('modules/Vtiger/actions/Save.php');
		$request=new Vtiger_Request(array(), array());
		$_REQUEST['record']='';//save_modules模块中要用到
		$accountname=preg_replace('/^(\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;)+|(\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;)+$/u','',$accountname);
		global $current_user;
		$user = new Users();

		//$current_user = $user->retrieveCurrentUserInfoFromFile(6934);;
		$current_user = $user->retrieveCurrentUserInfoFromFile(1);
		$address=$fieldname['province'].'#';
		$address.=$fieldname['city'].'#';
		$address.=$fieldname['area'].'#';
		$address.=$fieldname['address'];
		$accountname=trim($accountname);
		$_REQUEST['accountname']=$labelname;
		$request->set('accountname',$accountname);
		$request->set('module','Accounts');
		$request->set('view','Edit');
		$request->set('action','Save');
		$request->set('makedecisiontype','Decisionmakers');
		$request->set('address',$address);
		$request->set('phone',$fieldname['phone']);
		$request->set('linkname',$fieldname['linkname']);
		$request->set('title',$fieldname['title']);
		$request->set('email1',$fieldname['email1']);
		$request->set('mobile',$fieldname['mobile']);
		$request->set('weixin',$fieldname['weixin']);
		$request->set('customertype',$fieldname['customertype']);
		$request->set('website',$fieldname['website']);
		$request->set('gendertype',$fieldname['gendertype']);
		$ressorder=new Vtiger_Save_Action();
		$recordModel=$ressorder->saveRecord($request);
		$crmid=$recordModel->getId();
		$sql='REPLACE INTO vtiger_uniqueaccountname(accountid,accountname) VALUES(?,?)';
		$adb->pquery($sql,array($crmid,$labelname));
		$sql='UPDATE vtiger_account SET protectday=30,effectivedays=30 WHERE accountid=?';
		$adb->pquery($sql,array($crmid));
		$res=array("cid"=>$crmid,'accountname'=>$accountname);
	}
	if($accountid>0){
		$adb->pquery("UPDATE vtiger_activationcode SET customerid=? WHERE usercodeid=? AND (customerid IS NULL OR customerid='')", array($res["cid"],$accountid));
	}
	return array($res);
}

/*查找合同编号*/
function findContractNo($fieldname, $userid){
	global $adb,$current_user;
	$user = new Users();
	$current_user = $user->retrieveCurrentUserInfoFromFile($userid);


	$date=date('Y-m-d');

	$total = 0;
	$pageCount = 10;

	//$sql = "SELECT servicecontractsid,contract_no FROM `vtiger_servicecontracts` as a left join vtiger_crmentity as b on (a.servicecontractsid=b.crmid) WHERE b.deleted=0 AND b.smownerid='" . $current_user->id . "' AND a.contract_no IN (SELECT servicecontracts_no FROM `vtiger_servicecontracts_print` WHERE servicecontractsprintid=? AND constractsstatus IN ('c_receive'))";
	$sql = "SELECT servicecontractsid,contract_no FROM `vtiger_servicecontracts` as a left join vtiger_crmentity as b on (a.servicecontractsid=b.crmid) WHERE b.deleted=0 AND b.smownerid='" . $current_user->id . "' AND a.contract_no like ? AND a.modulestatus='已发放'";
	$listResult = $adb->pquery($sql, array('%'.$fieldname['id'].'%'));
	$res = '';
	while($rawData=$adb->fetch_array($listResult)) {
		$res['contract_no'] =  $rawData['contract_no'];
		$res['servicecontractsid'] =  $rawData['servicecontractsid'];
	}
	return array('ReceivedPayments'=>$res);

}

/**
 *根据客户Id找合同
 *
 */
function contractWithAccountid($fieldname, $userid){
    global $adb,$current_user;
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);

    $accountid = $fieldname['accountid'];
    $sql = "SELECT servicecontractsid,vtiger_activationcode.productid,contract_no,vtiger_activationcode.activecode,IFNULL((SELECT CONCAT(last_name,'[',IFNULL((SELECT departmentname FROM vtiger_departments WHERE departmentid =(SELECT departmentid FROM vtiger_user2department WHERE userid =vtiger_users.id LIMIT 1)),''),']',(IF (`status` = 'Active','','[离职]'))) AS last_name FROM vtiger_users WHERE a.signid= vtiger_users.id	), '--') AS username,a.signid as userid FROM `vtiger_servicecontracts` as a left join vtiger_crmentity as b on (a.servicecontractsid=b.crmid) LEFT JOIN vtiger_activationcode ON vtiger_activationcode.contractid=a.servicecontractsid WHERE b.deleted=0 AND a.parent_contracttypeid=2 AND sc_related_to=? AND a.modulestatus='c_complete' LIMIT 50";
    $listResult = $adb->pquery($sql, array($accountid));
    $res = array();
    while($rawData=$adb->fetch_array($listResult)) {
        $res[] =  $rawData;
    }
    return array('contractList'=>$res);

}
/*合同搜索*/
function contractSearch($fieldname, $userid){
	global $adb,$current_user;
	$user = new Users();
	$current_user = $user->retrieveCurrentUserInfoFromFile($userid);


	$date=date('Y-m-d');

	$total = 0;
	$pageCount = 10;
    include_once('include/utils/UserInfoUtil.php');
    $where=getAccessibleUsers('Accounts','List',false);
    $query='';
    if($where!='1=1'){
        $query = ' AND b.smownerid '.$where;
    }
	$contract_no = $fieldname['contract_no'];
	//$sql = "SELECT servicecontractsid,contract_no FROM `vtiger_servicecontracts` as a left join vtiger_crmentity as b on (a.servicecontractsid=b.crmid) WHERE b.deleted=0 AND b.smownerid='" . $current_user->id . "' AND a.contract_no IN (SELECT servicecontracts_no FROM `vtiger_servicecontracts_print` WHERE servicecontracts_no LIKE ? AND  constractsstatus IN ('c_receive')) LIMIT 50";
    $sql = "SELECT servicecontractsid,contract_no,IFNULL((SELECT CONCAT(last_name,'[',IFNULL((SELECT departmentname	FROM vtiger_departments	WHERE departmentid =(SELECT departmentid FROM vtiger_user2department WHERE userid =vtiger_users.id LIMIT 1)),''),']',(IF (`status` = 'Active','','[离职]'))) AS last_name FROM vtiger_users WHERE b.smownerid = vtiger_users.id	), '--') AS username,b.smownerid as userid FROM `vtiger_servicecontracts` as a left join vtiger_crmentity as b on (a.servicecontractsid=b.crmid) WHERE b.deleted=0 AND a.signaturetype='papercontract' AND a.parent_contracttypeid=2" .$query. " AND a.contract_no like ? AND a.modulestatus IN('已发放','c_recovered')
     AND NOT EXISTS(SELECT 1 FROM vtiger_activationcode WHERE vtiger_activationcode.contractid=a.servicecontractsid AND vtiger_activationcode.status IN(0,1)) 
	 AND NOT EXISTS(SELECT 1 FROM vtiger_tyunstationsale WHERE vtiger_tyunstationsale.contractid=a.servicecontractsid AND vtiger_tyunstationsale.stationsalestatus=0) 
     LIMIT 50";

	$listResult = $adb->pquery($sql, array("%$contract_no%"));
	$res = array();
	while($rawData=$adb->fetch_array($listResult)) {
		$res[] =  $rawData;
	}
	return array('contractList'=>$res);

}

/*合同搜索*/
function searchTyunBuyServiceContract($fieldname, $userid){
    global $adb,$current_user;
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);

    $date=date('Y-m-d');

    $total = 0;
    $pageCount = 10;
    include_once('include/utils/UserInfoUtil.php');

    $where=getAccessibleUsers('Accounts','List',false);
    $query='';
	$is_cs_admin = $fieldname['is_cs_admin'];
    if($where!='1=1' && $is_cs_admin !=1){
        $query = ' AND b.smownerid '.$where;
    }
    $contract_no = $fieldname['contract_no'];
    /*$customerid = $fieldname['customerid'];
    $sql = "SELECT servicecontractsid,contract_no,IFNULL((SELECT CONCAT(last_name,'[',IFNULL((SELECT departmentname	FROM vtiger_departments	WHERE departmentid =(SELECT departmentid FROM vtiger_user2department WHERE userid =vtiger_users.id LIMIT 1)),''),']',(IF (`status` = 'Active','','[离职]'))) AS last_name FROM vtiger_users WHERE b.smownerid = vtiger_users.id	), '--') AS username,b.smownerid as userid FROM `vtiger_servicecontracts` as a left join vtiger_crmentity as b on (a.servicecontractsid=b.crmid) WHERE b.deleted=0  AND a.parent_contracttypeid=2" .$query. " AND a.sc_related_to=? AND a.contract_no like ? AND a.modulestatus IN('已发放','c_recovered')
     AND NOT EXISTS(SELECT 1 FROM vtiger_activationcode WHERE vtiger_activationcode.contractid=a.servicecontractsid AND vtiger_activationcode.status IN(0,1))
	 AND NOT EXISTS(SELECT 1 FROM vtiger_tyunstationsale WHERE vtiger_tyunstationsale.contractid=a.servicecontractsid)
     LIMIT 50";
    $listResult = $adb->pquery($sql, array($customerid,"%$contract_no%"));*/
    $sql = "SELECT servicecontractsid,contract_no,IFNULL((SELECT CONCAT(last_name,'[',IFNULL((SELECT departmentname	FROM vtiger_departments	WHERE departmentid =(SELECT departmentid FROM vtiger_user2department WHERE userid =vtiger_users.id LIMIT 1)),''),']',(IF (`status` = 'Active','','[离职]'))) AS last_name FROM vtiger_users WHERE b.smownerid = vtiger_users.id	), '--') AS username,b.smownerid as userid FROM `vtiger_servicecontracts` as a left join vtiger_crmentity as b on (a.servicecontractsid=b.crmid) WHERE b.deleted=0  AND a.parent_contracttypeid=2" .$query. " AND a.contract_no like ? AND a.modulestatus IN('已发放','c_recovered')
     AND NOT EXISTS(SELECT 1 FROM vtiger_activationcode WHERE vtiger_activationcode.contractid=a.servicecontractsid AND vtiger_activationcode.status IN(0,1)) 
	 AND NOT EXISTS(SELECT 1 FROM vtiger_tyunstationsale WHERE vtiger_tyunstationsale.contractid=a.servicecontractsid) 
     LIMIT 50";

    $listResult = $adb->pquery($sql, array("%$contract_no%"));

    $res = array();
    while($rawData=$adb->fetch_array($listResult)) {
        $res[] =  $rawData;
    }
    return array('contractList'=>$res);

}
/**另购产品**/
function getExtraProduct($fieldname, $userid){
    include_once('includes/http/Request.php');
    include_once('modules/ServiceContracts/models/Record.php');
    return ServiceContracts_Record_Model::getextraproduct(1);
}
//保存T云接口返回数据
function saveTyunResposeData($fieldname, $userid){
    $recordModel=Vtiger_Record_Model::getCleanInstance('ActivationCode');
    $recordModel->saveTyunResposeData($fieldname);
    return array(array(1));
}

/**
 * 保存激活码信息
 */
function saveSecreCodeInfo($fieldname, $userid){
	global $adb,$current_user;
	$user = new Users();
	$current_user = $user->retrieveCurrentUserInfoFromFile($userid);
	include_once('includes/http/Request.php');
	include_once('modules/Vtiger/actions/Save.php');
	$save = new Vtiger_Save_Action();
    //$fieldname['ActivationCodeData']['receivetime']=date("Y-m-d H:i:s");
	$res  = $save->saveRecord(new Vtiger_Request($fieldname['ActivationCodeData'], $fieldname['ActivationCodeData']));
	$activationcodeid = $res->getId();
    $buyserviceinfo = $fieldname['ActivationCodeData']['t_buyserviceinfo'];
    $buydate = $fieldname['ActivationCodeData']['buydate'];
    if(empty($buydate)){
        $buydate = date("Y-m-d H:i:s");
	}
    //$crm_buyserviceinfo = $fieldname['ActivationCodeData']['crm_buyserviceinfo'];
    $adb->pquery("UPDATE vtiger_activationcode SET pushstatus=1,receivetime=?,buyserviceinfo=?,creator=? WHERE activationcodeid=?",array($buydate,$buyserviceinfo,$userid,$activationcodeid));
    //更新合同金额
    $contractamount = $fieldname['ActivationCodeData']['t_contractamount'];
    $contractid  = $fieldname['ActivationCodeData']['contractid'];
    if(!empty($contractamount) && bccomp($contractamount,0)>0){
        $adb->pquery("UPDATE vtiger_servicecontracts SET total=?,signid=? WHERE servicecontractsid=?",array($contractamount,$userid,$contractid));
    }
	//customerid
	//return array(array($fieldname));
    //$productidold=array(
        /*'512cb5c8-7609-11e7-a335-5254003c6d38'=>'',
		'512cb5e6-7609-11e7-a335-5254003c6d38'=>'',
		'512cb609-7609-11e7-a335-5254003c6d38'=>'',*/
       /* 'fafdc07c-4296-11e6-ad98-00155d069461'=>"426335",
        'fb016797-4296-11e6-ad98-00155d069461'=>"426337",
        'eb472d25-f1b1-11e6-a335-5254003c6d38'=>"565988",
        'fb016866-4296-11e6-ad98-00155d069461'=>"426340",
        'fb0174bf-4296-11e6-ad98-00155d069461'=>"426342",
        'ad0bee9e-516f-11e6-a2ff-52540013dadb'=>"566004",
        'eb480f94-f1b1-11e6-a335-5254003c6d38'=>"474817",
        'fb01732e-4296-11e6-ad98-00155d069461'=>"426322",
        'a36a9cac-516f-11e6-a2ff-52540013dadb'=>"837");*/
    //updateContractInfo($fieldname,$userid,$productidold);
    return array(array($activationcodeid));

}

/**
 * 添加合同信息
 */
function updateContractInfo($fieldname,$userid,$productidold){
	return ;
    global $adb,$current_user;

    $productidnew=$productidold[$fieldname['ActivationCodeData']['productid']];

    $resuProducts=$adb->pquery('SELECT vtiger_products.productname, vtiger_products.product_no, vtiger_products.realprice, vtiger_products.unit_price, vtiger_products.minmarketprice,vtiger_products.productid FROM vtiger_products LEFT JOIN vtiger_seproductsrel ON vtiger_seproductsrel.crmid = vtiger_products.productid AND vtiger_seproductsrel.setype=\'Products\' WHERE  (vtiger_seproductsrel.productid = ? or vtiger_products.productid=?)',array($productidnew,$productidnew));
    $listProducts=array();
    $prealprices=0;
    while($row=$adb->fetch_row($resuProducts)){
        if($row['productid']==$productidnew){
            $productname=$row['productname'];
            $pmarketprice=$row['unit_price'];

        }else{
            $listProducts[$row['productid']]['productid']=$row['productid'];
            $listProducts[$row['productid']]['realprice']=$row['realprice'];
            $listProducts[$row['productid']]['purchasemount']=$row['minmarketprice'];
            $prealprices+=$row['realprice'];

        }
    }

    $productlife=$fieldname['ActivationCodeData']['productlife'];
    $agelife=$productlife*12;
    $QUEST['record']=$fieldname['ActivationCodeData']['contractid'];
    include_once('includes/http/Request.php');
    include_once('modules/Vtiger/actions/Save.php');
    $request=new Vtiger_Request($QUEST, $QUEST);
    foreach($listProducts as $productd){
        $_REQUEST['productids'][$productd['productid']]=$productd['productid'];//套餐ID
        $_REQUEST['productcomboid'][$productd['productid']]=$productidnew;//套餐ID
        $_REQUEST['productsolution'][$productd['productid']]='';//备注为空
        $_REQUEST['producttext'][$productd['productid']]='';//产品信息为空
        $_REQUEST['productnumber'][$productd['productid']]=1;//数量
        $_REQUEST['agelife'][$productd['productid']]=$agelife;//年限
        $_REQUEST['standard'][$productd['productid']]='';//是否标标准
        $_REQUEST['thepackage'][$productd['productid']]=$productname;//套餐名称
        $_REQUEST['isextra'][$productd['productid']]=0;//是否额外产品
        $_REQUEST['prealprice'][$productd['productid']]=$prealprices*$productlife;//套餐单价
        $_REQUEST['punit_price'][$productd['productid']]=$pmarketprice*$productlife;//套餐市场价
        $_REQUEST['pmarketprice'][$productd['productid']]='';//套餐合同价
        $_REQUEST['realprice'][$productd['productid']]=$productd['realprice']*$productlife;//成本
        $_REQUEST['purchasemount'][$productd['productid']]=$productd['purchasemount']*$productlife;//额外成本
        $_REQUEST['vendorid'][$productd['productid']]='';//供产商
        $_REQUEST['suppliercontractsid'][$productd['productid']]='';//供应商合同
    }
    $request->set('productid',$productidnew);//合同产品
    $request->set('sc_related_to', $fieldname['ActivationCodeData']['customerid']);//对应的客户
    $_REQUEST['productid'][]=$productidnew;//save_modules模块中要用到
    $_REQUEST['record']=$fieldname['ActivationCodeData']['contractid'];//save_modules模块中要用到
    $_REQUEST['currentid']=$fieldname['ActivationCodeData']['contractid'];
    $_REQUEST['sc_related_to']=$fieldname['ActivationCodeData']['customerid'];//客户
    $request->set('module','ServiceContracts');
    $request->set('view','Edit');
    $request->set('action','Save');
    $ressorder=new Vtiger_Save_Action();
    $ressorder->saveRecord($request);
}
/*合同搜索*/
function checkUpgradeAndRenew($fieldname, $userid){
    global $adb,$current_user;
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);

    $contract_no = $fieldname['contractid'];
    $classid = $fieldname['classid'];
    //$sql = "SELECT servicecontractsid,contract_no FROM `vtiger_servicecontracts` as a left join vtiger_crmentity as b on (a.servicecontractsid=b.crmid) WHERE b.deleted=0 AND b.smownerid='" . $current_user->id . "' AND a.contract_no IN (SELECT servicecontracts_no FROM `vtiger_servicecontracts_print` WHERE servicecontracts_no LIKE ? AND  constractsstatus IN ('c_receive')) LIMIT 50";
    $sql = "SELECT 1 FROM `vtiger_activationcode` WHERE contractid=? AND classtype=?";
    $listResult = $adb->pquery($sql, array($contract_no,$classid));
    $res= 0;
    if($adb->num_rows($listResult)){
        $res= 1;
	}
    return array('contract'=>$res);

}
/**
 * tyun升级续费
 */
function upgradeAndRenew($fieldname, $userid){
	//return array($fieldname);
    global $adb,$current_user;
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
    $tableid=$adb->getUniqueID('vtiger_activationcode');
    $divideNames['activationcodeid']=$tableid;
    //$divideNames['activecode']=$fieldname['activeCode'];//激活码
    $divideNames['contractid']=$fieldname['acData']['contractid'];//客户Id
    $divideNames['customername']=$fieldname['acData']['CompanyName'];//客户名称
    $divideNames['productlife']=$fieldname['acData']['productlife'];//年限
    $divideNames['productid']=$fieldname['acData']['productid'];//产品
    $divideNames['salesname']=$fieldname['acData']['SalesName'];//销售员
    $divideNames['customerid']=$fieldname['acData']['customerid'];//销售员
    $divideNames['originalcontractname']=$fieldname['acData']['originalcontractcode'];//原合同编号
    //$divideNames['originalcontractid']=$fieldname['originalcontractid'];//原合同ID
    $divideNames['oldproductid']=$fieldname['acData']['oldproductid'];//原合同ID
    $divideNames['resultmsg']=$fieldname['acData']['resultmsg'];//客户端返回的信息
    $divideNames['status']=0;//
    $divideNames['classtype']=$fieldname['acData']['classid'];//类型升级,续费
    $divideNames['contractname']=$fieldname['acData']['contractcode'];//类型升级,续费
    $divideNames['usercode']=$fieldname['acData']['usercode'];//用户名
    $divideNames['upgradeDate']=$fieldname['acData']['upgradeDate'];//升级日期
    $sql='INSERT INTO `vtiger_activationcode`('. implode(',', array_keys($divideNames)).') VALUES ('. generateQuestionMarks($divideNames) .')';
    $adb->pquery($sql,$divideNames);
    $productidold=array(
        /*'512cb5c8-7609-11e7-a335-5254003c6d38'=>'',
        '512cb5e6-7609-11e7-a335-5254003c6d38'=>'',
        '512cb609-7609-11e7-a335-5254003c6d38'=>'',*/
        'fafdc07c-4296-11e6-ad98-00155d069461'=>"393333",//v1
        'fb016797-4296-11e6-ad98-00155d069461'=>"426337",//v2
        'eb472d25-f1b1-11e6-a335-5254003c6d38'=>"565988",
        'fb016866-4296-11e6-ad98-00155d069461'=>"430156",//v3
        'fb0174bf-4296-11e6-ad98-00155d069461'=>"522819",//v5
        'ad0bee9e-516f-11e6-a2ff-52540013dadb'=>"566004",
        'eb480f94-f1b1-11e6-a335-5254003c6d38'=>"474817",//
        'fb01732e-4296-11e6-ad98-00155d069461'=>"405279",//标准板版
        'a36a9cac-516f-11e6-a2ff-52540013dadb'=>"433858");//发布宝经续费
    updateContractInfo($fieldname,$userid,$productidold);
    return array('flag'=>1);
}
function getCompanySaleServiceInfo($fieldname){

    $db=PearDatabase::getInstance();
    $query='';
    $requdata=array($fieldname["errorcode"]);
    if(!empty($fieldname["companyname"])){
    	$query=" UNION
				SELECT
					saleuser.last_name AS salename,
					saleuser.email1 AS saleemail,
					saleuser.phone_mobile AS salemobile,
					(SELECT  vtiger_departments.departmentname FROM vtiger_user2department LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_user2department.departmentid WHERE vtiger_user2department.userid=saleuser.id) AS salesdepartment ,
					serviceuser.last_name AS servicename,
					serviceuser.email1 AS servicename,
					serviceuser.phone_mobile AS servicename,
					(SELECT  vtiger_departments.departmentname FROM vtiger_user2department LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_user2department.departmentid WHERE vtiger_user2department.userid=serviceuser.id) AS servicedepartment
				
				FROM
					vtiger_account
				LEFT JOIN vtiger_activationcode ON vtiger_activationcode.customerid = vtiger_account.accountid
				LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_account.accountid
				LEFT JOIN vtiger_users AS saleuser ON saleuser.id=vtiger_crmentity.smownerid
				LEFT JOIN vtiger_users AS serviceuser ON serviceuser.id=vtiger_account.serviceid
				
				WHERE
					vtiger_crmentity.deleted=0
				AND vtiger_activationcode.companyname=?
				
				UNION
				SELECT
					saleuser.last_name AS salename,
					saleuser.email1 AS saleemail,
					saleuser.phone_mobile AS salemobile,
					(SELECT  vtiger_departments.departmentname FROM vtiger_user2department LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_user2department.departmentid WHERE vtiger_user2department.userid=saleuser.id) AS salesdepartment ,
					serviceuser.last_name AS servicename,
					serviceuser.email1 AS servicename,
					serviceuser.phone_mobile AS servicename,
					(SELECT  vtiger_departments.departmentname FROM vtiger_user2department LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_user2department.departmentid WHERE vtiger_user2department.userid=serviceuser.id) AS servicedepartment
				
				FROM
					vtiger_account
				LEFT JOIN vtiger_activationcode ON vtiger_activationcode.customerid = vtiger_account.accountid
				LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_account.accountid
				LEFT JOIN vtiger_users AS saleuser ON saleuser.id=vtiger_crmentity.smownerid
				LEFT JOIN vtiger_users AS serviceuser ON serviceuser.id=vtiger_account.serviceid
				
				WHERE
					vtiger_crmentity.deleted=0
				AND vtiger_activationcode.customername=?";
        $requdata=array($fieldname["errorcode"],$fieldname["companyname"],$fieldname["companyname"]);
	}
	$listQuery="SELECT
				saleuser.last_name AS salename,
				saleuser.email1 AS saleemail,
				saleuser.phone_mobile AS salemobile,
				(SELECT  vtiger_departments.departmentname FROM vtiger_user2department LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_user2department.departmentid WHERE vtiger_user2department.userid=saleuser.id) AS salesdepartment ,
				serviceuser.last_name AS servicename,
				serviceuser.email1 AS serviceemail,
				serviceuser.phone_mobile AS servicemobile,
				(SELECT  vtiger_departments.departmentname FROM vtiger_user2department LEFT JOIN vtiger_departments ON vtiger_departments.departmentid=vtiger_user2department.departmentid WHERE vtiger_user2department.userid=serviceuser.id) AS servicedepartment
			
			FROM
				vtiger_account
			LEFT JOIN vtiger_activationcode ON vtiger_activationcode.customerid = vtiger_account.accountid
			LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_account.accountid
			LEFT JOIN vtiger_users AS saleuser ON saleuser.id=vtiger_crmentity.smownerid
			LEFT JOIN vtiger_users AS serviceuser ON serviceuser.id=vtiger_account.serviceid
			WHERE
				vtiger_crmentity.deleted=0
			AND vtiger_activationcode.usercode=?".$query;
	$result=$db->pquery($listQuery,$requdata);
    $array=array();
    while($row=$db->fetch_array($result))
    {
        $array["data"]["sale"]=array("name"=>$row["salename"],
        "mobile"=>$row["saleemail"],
        "email "=>$row["salemobile"],
        "department"=>$row["salesdepartment"]);
        $array["data"]["service"]=array("name"=>$row["servicename"],
            "mobile"=>$row["serviceemail"],
            "email "=>$row["servicemobile"],
            "department"=>$row["servicedepartment"]);
    }
    return array($array);
}

/**
 * 获取T云对应产品的可升级或降级的产品
 */
function getTyunProductDownUp($userid){

    global $adb,$current_user;
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
    $db=PearDatabase::getInstance();
    $query='SELECT dproduct,sproduct,dlabel,tyundownup FROM `vtiger_productdownupgrade` WHERE deleted=0';
    $result=$db->pquery($query,array());
    $array=array();

    while($row=$db->fetch_array($result))
    {
		$array[]=$row;
		//$array[$row['tyundownup']][$row['sproduct']][$i]=array('product'=>$row['dproduct'],'name'=>$row['dlabel']);
    }
    return array($array);
}
function applicationToModify($fieldname, $userid){
    global $adb,$current_user;
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
    //Vtiger_Response
    include_once('modules/'.$fieldname['module'].'/actions/ChangeAjax.php');
    include_once('includes/http/Request.php');
    include_once('includes/http/Response.php');
    // 这个地方目前两个地方用到 AccountPlatform  和  ProductProvider 模块
    /*$changeAjax = new AccountPlatform_ChangeAjax_Action();*/
    $changeAjaxAction= $fieldname['changeAjaxAction'];
    $changeAjax = new $changeAjaxAction();
    $_REQUEST['isMobileCheck']=1;
    ob_start();
    $results = $changeAjax->Resubmit(new Vtiger_Request($fieldname, $fieldname));
    $result = ob_get_contents();
    ob_end_clean();
    return array('flag'=>1,'result'=>$result);
}
/*
 * 工作流审核
*/
function salesorderWorkflowStagesExamine($fieldname, $userid) {
	global $adb,$current_user;
	$user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
    //Vtiger_Response
    $operate=setoperate($fieldname['record'],$fieldname['src_module']);
    $_REQUEST['realoperate']=$operate;
    include_once('modules/SalesorderWorkflowStages/actions/SaveAjax.php');
    include_once('includes/http/Request.php');
    include_once('includes/http/Response.php');
    $saveAjax = new SalesorderWorkflowStages_SaveAjax_Action();
    $_REQUEST['isMobileCheck']=1;
    ob_start();
    $results = $saveAjax->updateSalseorderWorkflowStages(new Vtiger_Request($fieldname, $fieldname));
    $result = ob_get_contents();
    ob_end_clean();
    if(is_array($results)){
        $result=$results;
    }
    return array('flag'=>1,'result'=>$result);
}
/**
 * 微信发送提醒
 */
function salesorderWorkflowStagesWx($stagerecord){
    global $adb,$current_user;
    include_once('modules/SalesorderWorkflowStages/actions/SaveAjax.php');
    $saveAjax = new SalesorderWorkflowStages_SaveAjax_Action();
    $saveAjax->sendSns($stagerecord);
    return;
}
/*
	工作流打回
*/
function salesorderWorkflowStagesRepulse($fieldname, $userid) {
	global $adb,$current_user;
	$user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
    //Vtiger_Response

    include_once('modules/SalesorderWorkflowStages/actions/SaveAjax.php');
    include_once('includes/http/Request.php');
    include_once('includes/http/Response.php');

    $saveAjax = new SalesorderWorkflowStages_SaveAjax_Action();
    $_REQUEST['isMobileCheck']=1;
    if(method_exists($saveAjax,$fieldname['mode'])){
        ob_start();
        $results = $saveAjax->$fieldname['mode'](new Vtiger_Request($fieldname, $fieldname));
        $result = ob_get_contents();
        ob_end_clean();
        if(is_array($results)){
            $result=$results;
		}
	}
    //$data = json_decode($res);
    return  array($result);
}
function checkAuditInformation($fieldname, $userid){
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
    $_SESSION['userdepartmentid']=$current_user->departmentid;
    $recordModel=Vtiger_Record_Model::getCleanInstance('RefillApplication');
    $accountid=$fieldname['accountid'];
    $advancesmoney=$fieldname['advancesmoney'];
    return array($recordModel->checkAuditInformation($accountid,$advancesmoney));
}

/*
	添加充值申请单
*/
function addRefillApplication($fieldname, $userid) {
	global $adb,$current_user;
	global $isallow;
		$isallow=array('SalesOrder','Invoice','Quotes','VisitingOrder','Vacate','OrderChargeback','RefillApplication',
		'ExtensionTrial', 'Suppcontractsextension','PurchaseInvoice');
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
    $_SESSION['userdepartmentid']=$current_user->departmentid;
    $rechargesource = $fieldname["refillApplicationData"]["rechargesource"];
    $accountid=$fieldname["refillApplicationData"]['accountid'];
    $actualtotalrecharge=$fieldname["refillApplicationData"]['actualtotalrecharge'];
    $totalrecharge=$fieldname["refillApplicationData"]['totalrecharge'];
    $amountAvailable=$actualtotalrecharge-$totalrecharge;
    $rechargesource=$fieldname["refillApplicationData"]['rechargesource'];
    $rechargesourceArray=array('Accounts','Vendors');

    $recordModel=Vtiger_Record_Model::getCleanInstance('RefillApplication');
    if(in_array($rechargesource,$rechargesourceArray)) {
        $auditInformation = $recordModel->setAuditInformation($accountid, $amountAvailable);
        if (!$auditInformation['flag']) {
            $result_up['success']= 2;
            $result_up['msg']= "客户垫款大于担保金额,不允许提交!!";
            return array($result_up);
        };
    }
	//获取合同总的充值金额
    $servicecontractsid=$fieldname["refillApplicationData"]['servicecontractsid'];
    $contractamount=$fieldname["refillApplicationData"]['contractamount'];
    $sumrefilltotal=$recordModel->getSumActualtotalrecharge($servicecontractsid);
    if(!empty($contractamount) && bccomp($contractamount,0) >0){
        if(bccomp($sumrefilltotal,$contractamount)>0){
            $result_up['success']= 2;
            $result_up['msg']= "已充值合同金额(".$sumrefilltotal.")不能大于合同金额(".$contractamount.")!";
            return array($result_up);
        }
    }
    // 非程序化充值申请流程工作流id 线上 555702   90crm的工作流id  397103
	// 程序化充值申请流程工作流id   线上 558406   90crm的工作流id  397496
	// 判断使用哪个工作流 程序化事业部->程序化充值申请流程
	// 不是程序化事业部->非程序化充值申请流程
    //调用申请单的处理，此处注释掉 2017/03/07 gaocl
    /*$workflowsid = '555702';
    $fieldname['refillApplicationData']['workflowsid'] = $workflowsid;
    if ($current_user->departmentid == 'H249') { // 程序化广告事业部
           $workflowsid = '558406';
           $fieldname['refillApplicationData']['workflowsid'] = $workflowsid;
       }*/

    //return array($current_user->departmentid);die;
    include_once('includes/http/Request.php');
    include_once('modules/Vtiger/actions/Vtiger_Save_Action.php');
    //$save = new Vtiger_Save_Action();
    $save = new RefillApplication_Save_Action();

    $res  = $save->saveRecord(new Vtiger_Request($fieldname['refillApplicationData'], $fieldname['refillApplicationData']));
    $recordid = $res->getId();
    // 添加明细
   /* $rechargesheetData = $fieldname['rechargesheetData'];
    if (count($rechargesheetData) > 0) {
    	$tarray = array();
    	$insertistr = '';
    	foreach ($rechargesheetData as $rechargesheet) {
    			$value = $rechargesheet;
    			$tarray[]=$recordid;
                $tarray[]=$value['topplatform'];
                $tarray[]=$value['accountzh'];
                $tarray[]=$value['did'];
                $tarray[]=$value['rechargetype'];
		  		$tarray[]=$value['receivementcurrencytype'];
                $tarray[]=$value['exchangerate'];
                $tarray[]=$value['rechargeamount'];
                $tarray[]=$value['prestoreadrate'];
                $tarray[]=$value['discount'];
                $tarray[]=$value['tax'];
                $tarray[]=$value['factorage'];
                $tarray[]=$value['activationfee'];
                $tarray[]=$value['totalcost'];
                $tarray[]=$value['dailybudget'];
                $tarray[]=$value['transferamount'];
                $tarray[]=$value['rebateamount'];
                $tarray[]=$value['totalgrossprofit'];
                $tarray[]=$value['servicecost'];
                $tarray[]=$value['mstatus'];
                $tarray[]=$userid;
                $tarray[]=date('Y-m-d H:i:s');
                $insertistr.='(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?),';
    	}
    	$insertistr=rtrim($insertistr,',');
    	$sql="INSERT INTO vtiger_rechargesheet(`refillapplicationid`,`topplatform`,`accountzh`,`did`,`rechargetype`,receivementcurrencytype,exchangerate,`rechargeamount`,`prestoreadrate`,`discount`,`tax`,`factorage`,`activationfee`,`totalcost`,`dailybudget`,`transferamount`,`rebateamount`,`totalgrossprofit`,`servicecost`,`mstatus`,`createdid`,`createdtime`) VALUES{$insertistr}";
        $adb->pquery($sql, $tarray);
    }*/

    /*$accountid =  $fieldname['refillApplicationData']['accountid'];
    $result = $adb->pquery("SELECT advancesmoney  FROM vtiger_account WHERE accountid =? ",array($accountid));
    $advancesmoney = 0;
    if ($result && $adb->num_rows($result) > 0) {
        $row = $adb->fetch_array($result);
        $advancesmoney =  $row['advancesmoney'];
    }*/

    // 添加工作流
    //调用申请单的处理，此处注释掉 2017/03/07 gaocl
   /* include_once('data/CRMEntity.php');
    $on_focus = CRMEntity::getInstance('RefillApplication');
    $on_focus->makeWorkflows('RefillApplication', $workflowsid, $recordid, 'edit');*/

    $result_up['success']= 1;
    $result_up['msg']= $recordid;
    return array($result_up);
    //return array($recordid, 0);
    //return array($recordid);
}


/**
 * 充值申请单列表
 * @param $fieldname
 * @return array
 */
function getRefillApplication($fieldname,$userid){
    $list = get_com_list('RefillApplication', $fieldname);
    return $list;
}
function getAccountPlatformList($fieldname,$userid){
    global $current_user;
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
    $list = get_com_list('AccountPlatform', $fieldname);
    return $list;
}
function getProductProviderList($fieldname,$userid){
    global $current_user;
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
    $list = get_com_list('ProductProvider', $fieldname);
    return $list;
}

function getOneSalesDaily($fieldname, $userid) {
	global $current_user,$currentAction,$adb;

	// 看了属于自己的批复 改变状态
//	$sql = "UPDATE vtiger_approval,vtiger_salesdaily_basic SET islook=1
//	WHERE vtiger_approval.relationid=vtiger_salesdaily_basic.salesdailybasicid
//	AND vtiger_approval.relationid=?
//	AND model='SalesDaily' AND vtiger_salesdaily_basic.smownerid=?";
//	$adb->pquery($sql, array($fieldname['id'], $userid));
	$sql = "UPDATE vtiger_modcomments,vtiger_salesdaily_basic set islook=1 where vtiger_modcomments.moduleid=vtiger_salesdaily_basic.salesdailybasicid and 
	vtiger_modcomments.moduleid=? and vtiger_modcomments.modulename='SalesDaily' and  vtiger_salesdaily_basic.smownerid=?";
    $adb->pquery($sql, array($fieldname['id'], $userid));
	$user = new Users();
	$current_user = $user->retrieveCurrentUserInfoFromFile($userid);
	$currentAction = 'DetailView';
	$result = array();
	//include_once('modules/SalesDaily/Models/Record.php');
	//$recordModel = SalesDaily_Record_Model::getInstanceById($fieldname['id'], 'Approval');

	include_once('modules/alesDaily/Models/Record.php');
	$recordModel = new SalesDaily_Record_Model();
	$detailList = $recordModel->getDetailList($fieldname['id']);

	// 获取基本信息
	$sql = "SELECT
			vtiger_salesdaily_basic.*,
			(
				SELECT
					CONCAT(
						last_name,
						'[',
						IFNULL(
							(
								SELECT
									departmentname
								FROM
									vtiger_departments
								WHERE
									departmentid = (
										SELECT
											departmentid
										FROM
											vtiger_user2department
										WHERE
											userid = vtiger_users.id
										LIMIT 1
									)
							),
							''
						),
						']',
						(

							IF (
								`status` = 'Active',
								'',
								'[离职]'
							)
						)
					) AS last_name
				FROM
					vtiger_users
				WHERE
					vtiger_salesdaily_basic.smownerid = vtiger_users.id
			) AS last_name
		FROM
		vtiger_salesdaily_basic where vtiger_salesdaily_basic.salesdailybasicid=?";
	$result = $adb->pquery($sql, array($fieldname['id']));
	$res_cnt = $adb->num_rows($result);
	$row = array();
	if($res_cnt > 0) {
	    $row = $adb->query_result_rowdata($result, 0);
	}
	$detailList['basic'] = $row;

	// 获取批复内容
//	$sql = "SELECT description,relationid,createid,createtime,
//(SELECT CONCAT(last_name,'[',IFNULL(
//							(SELECT departmentname FROM vtiger_departments WHERE
//									departmentid = (
//										SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_salesdaily_basic.smownerid LIMIT 1
//									)),''),']',(IF (`status` = 'Active','','[离职]'))) AS last_name FROM vtiger_users WHERE vtiger_salesdaily_basic.smownerid = vtiger_users.id) AS smownerid_last_name,
//(SELECT CONCAT(last_name,'[',IFNULL(
//							(SELECT departmentname FROM vtiger_departments WHERE
//									departmentid = (
//										SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_salesdaily_basic.smownerid LIMIT 1
//									)),''),']',(IF (`status` = 'Active','','[离职]'))) AS last_name FROM vtiger_users WHERE vtiger_approval.createid = vtiger_users.id) AS create_last_name
//
//FROM vtiger_approval
//				 LEFT JOIN vtiger_salesdaily_basic ON vtiger_salesdaily_basic.salesdailybasicid=vtiger_approval.relationid
//				 WHERE relationid=? AND delflag=0 AND
//(vtiger_approval.createid=?
//OR vtiger_salesdaily_basic.smownerid=?)  ";

	$sql = "select a.commentcontent as description,a.moduleid as relationid,a.creatorid as createid,a.addtime as createtime,
       (SELECT CONCAT(last_name,'[',IFNULL(
							(SELECT departmentname FROM vtiger_departments WHERE
									departmentid = (
										SELECT departmentid FROM vtiger_user2department WHERE userid =  a.creatorid LIMIT 1
									)),''),']',(IF (`status` = 'Active','','[离职]'))) AS last_name FROM vtiger_users WHERE  a.creatorid = vtiger_users.id) AS smownerid_last_name,
(SELECT CONCAT(last_name,'[',IFNULL(
							(SELECT departmentname FROM vtiger_departments WHERE
									departmentid = (
										SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_salesdaily_basic.smownerid LIMIT 1
									)),''),']',(IF (`status` = 'Active','','[离职]'))) AS last_name FROM vtiger_users WHERE a.creatorid = vtiger_users.id) AS create_last_name
       from vtiger_modcomments a 
				 LEFT JOIN vtiger_salesdaily_basic ON vtiger_salesdaily_basic.salesdailybasicid=a.moduleid 
	where a.moduleid=? and a.modulename='SalesDaily' order by createtime desc
";


	$result = $adb->pquery($sql, array($fieldname['id']));
	$res_cnt = $adb->num_rows($result);

	$approvalArr = null;
	if ($res_cnt > 0) {
		$approvalArr = array();
		for($i=0; $i<$res_cnt; $i++ ) {
			$row = $adb->fetch_row($result,$i);
			$approvalArr[] = $row;
		}
	}

	$detailList['approvalData'] = $approvalArr;
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
    $detailList['canreply'] = $recordModel->canReply($recordModel->get('smownerid'),$userid);
	return array($detailList);
}

/**
 * 获取负责人的客户的及产品的信息
 * @return array
 */
function addSalesDaily($fieldname,$userid){
    global $adb,$current_user;
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
    $datetime=date("Y-m-d");
    $datetimenow=date("Y-m-d H:i:s");
    $query = 'SELECT 1 FROM vtiger_salesdaily_basic WHERE vtiger_salesdaily_basic.dailydatetime=? AND vtiger_salesdaily_basic.smownerid=?';
    $result = $adb->pquery($query, array($datetime, $userid));
    if ($adb->num_rows($result)) {
        return array(0);
    }
    $request_a=array( "module"	=>'SalesDaily',
        "action"	=>'Save',
        'record'	=>'',
        "defaultCallDuration"		=>5,
        "defaultOtherEventDuration"	=>5,
        "createdtime"			=>$datetimenow,
        "dailydatetime"				=>$datetime,
        "smownerid"				=>$userid,
        "departmentid"				=>array($current_user->departmentid),
        "content"				=>$fieldname[0]['content'],
        "todaycontent"				=>$fieldname[0]['todaycontent'],
        "tommorrowcontent"			=>$fieldname[0]['tommorrowcontent'],
        "todayquestion"         	=>$fieldname[0]['todayquestion'],
        "todayfeel"         	=>$fieldname[0]['todayfeel'],
        "todayvisitnum"         	=>$fieldname[0]['todayvisitnum'],
        "total_telnumber"         	=>$fieldname[0]['total_telnumber'],
        "tel_connect_rate"         	=>$fieldname[0]['tel_connect_rate'],
        "wxnumber"         	=>$fieldname[0]['wxnumber'],
        "wxnewlyaddnumber"         	=>$fieldname[0]['wxnewlyaddnumber'],
        "wxnumberweek"         	=>$fieldname[0]['wxnumberweek'],
        "wxnumberweekaddnumber"         	=>$fieldname[0]['wxnumberweekaddnumber'],
        "wxnumbermonth"         	=>$fieldname[0]['wxnumbermonth'],
        "wxnumbermonthaddnumber"         	=>$fieldname[0]['wxnumbermonthaddnumber'],
		"telnumber"      			=>$fieldname[0]['telnumber'],
    );
    include_once('includes/http/Request.php');
    include_once('modules/Vtiger/actions/Save.php');
//    $save = new Vtiger_Save_Action();
    $save = new SalesDaily_Save_Action();
    $res  = $save->saveRecord(new Vtiger_Request($request_a, $request_a));
    $recordid = $res->getId();
        //$recordid = 21;
    //上个工作日的可成交客户*******START********
    if(!empty($fieldname[0]['prevcandealrecordid'])){
        foreach($fieldname[0]['prevcandealrecordid'] as $key=>$value){
            $prevcandealdeleted=$fieldname[0]['prevcandealdeleted'][$key]==1?1:0;
            if($prevcandealdeleted){
                $sql='INSERT INTO `vtiger_salesdailycandeal`(salesdailybasicid,accountid,contactname,mobile,title,accountcontent,productname,quote,firstpayment,issigncontract,deleted,dailydatetime,deleteddate) select ?,accountid,contactname,mobile,title,accountcontent,productname,quote,firstpayment,?,?,?,? from vtiger_salesdailycandeal where salesdailycandealid=?';
                $tempcanddeal=array($recordid,$fieldname[0]['prevcandealissigncontract'][$key],$fieldname[0]['prevcandealdeleted'][$key],$datetime,$datetimenow,$value);
            }else{
                $sql='INSERT INTO `vtiger_salesdailycandeal`(salesdailybasicid,accountid,contactname,mobile,title,accountcontent,productname,quote,firstpayment,issigncontract,dailydatetime) select ?,accountid,contactname,mobile,title,accountcontent,productname,quote,firstpayment,?,? from vtiger_salesdailycandeal where salesdailycandealid=?';
                $tempcanddeal=array($recordid,$fieldname[0]['prevcandealissigncontract'][$key],$datetime,$value);
            }
            $adb->pquery($sql,$tempcanddeal);
        }
    }
    //上个工作日的可成交客户*******end********


    //当天可成交的客户*******START********
    if(!empty($fieldname[0]['candealaccountmsg'])){
        foreach($fieldname[0]['candealaccountmsg'] as $key=>$value){
            $sql = "INSERT INTO `vtiger_salesdailycandeal`(salesdailybasicid,accountid,contactname,mobile,title,accountcontent,productname,quote,firstpayment,issigncontract,dailydatetime)
                SELECT ".$recordid.",vtiger_account.accountid,vtiger_account.linkname,vtiger_account.mobile,vtiger_account.title,'{$fieldname[0]['candealaccountcontent'][$key]}','{$fieldname[0]['candealproduct'][$key]}','{$fieldname[0]['candealquote'][$key]}','{$fieldname[0]['candealfirstpayment'][$key]}',0,'{$datetime}' FROM vtiger_account WHERE accountid=?";
            $adb->pquery($sql,array($value));
        }
    }

    //当天可成交的客户*******end********
    global $root_directory;
    include_once($root_directory.'languages/zh_cn/Vtiger.php');
    //当天成交的客户*******START********
    if(!empty($fieldname[0]['dayaccountmsg'])){
        foreach($fieldname[0]['dayaccountmsg'] as $key=>$value){
            $daydealmarketprice=$fieldname[0]['daydealmarketprice'][$key];//市场价
            $daydealdealamount=$fieldname[0]['daydealamount'][$key];//成交价
            $daydealfirstpayment=$fieldname[0]['daydealfirstpayment'][$key];//首付款
            $daydealstepprice=$fieldname[0]['daydealstepprice'][$key];//成本价
            if($daydealmarketprice <=0 || $daydealdealamount <=0 || $daydealfirstpayment<=0){
                //防止意外小于0的情况
                $daydealarrivalamounts=0;
            }else {
                //成交价大于市场价
                if ($daydealdealamount >= $daydealmarketprice) {
                    if ($daydealstepprice > 1) {
                        $daydealarrivalamount = $daydealfirstpayment - $daydealfirstpayment / $daydealdealamount * $daydealstepprice;
                    } else {
                        $daydealarrivalamount = $daydealfirstpayment;
                    }
                    $daydealarrivalamounts = round($daydealarrivalamount, 2);
                } else {
                    $discount = $daydealdealamount / $daydealmarketprice;
                    if ($discount >= 0.75) {
                        if ($daydealstepprice > 1) {
                            $daydealarrivalamount = $daydealfirstpayment * $discount - $daydealfirstpayment / $daydealdealamount * $daydealstepprice;
                        } else {
                            $daydealarrivalamount = $daydealfirstpayment * $discount;
                        }
                        $daydealarrivalamounts = round($daydealarrivalamount, 2);
                    } else {
                        $daydealarrivalamounts = 0;
                    }

                }
            }
            $query='SELECT vtiger_account.industry,(SELECT count(1) FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_servicecontracts.servicecontractsid WHERE vtiger_crmentity.deleted=0 AND vtiger_servicecontracts.modulestatus=\'c_complete\' AND vtiger_servicecontracts.sc_related_to=vtiger_account.accountid) AS oldcust,(SELECT count(1) FROM vtiger_visitingorder LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_visitingorder.visitingorderid WHERE vtiger_crmentity.deleted=0 AND vtiger_visitingorder.visitingorderid AND vtiger_visitingorder.modulestatus=\'c_complete\' AND vtiger_visitingorder.related_to=vtiger_account.accountid) AS visitingordernum,(SELECT vtiger_visitingorder.contacts FROM vtiger_visitingorder LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_visitingorder.visitingorderid WHERE vtiger_crmentity.deleted=0 AND vtiger_visitingorder.visitingorderid AND vtiger_visitingorder.modulestatus=\'c_complete\' AND vtiger_visitingorder.related_to=vtiger_account.accountid ORDER BY vtiger_visitingorder.visitingorderid DESC limit 1) AS visitingordercontacts,(SELECT (SELECT GROUP_CONCAT(vtiger_users.last_name) FROM vtiger_users WHERE FIND_IN_SET(vtiger_users.id,replace(vtiger_visitingorder.accompany,\' |##| \',\',\'))) FROM vtiger_visitingorder LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_visitingorder.visitingorderid WHERE vtiger_crmentity.deleted=0 AND vtiger_visitingorder.visitingorderid AND vtiger_visitingorder.modulestatus=\'c_complete\' AND vtiger_visitingorder.related_to=vtiger_account.accountid ORDER BY vtiger_visitingorder.visitingorderid DESC limit 1) AS visitingorderwithvisitor FROM vtiger_account WHERE accountid=?';
            $result=$adb->pquery($query,array($value));
            while($rawData=$adb->fetch_array($result)){
                //$industry=vtranslate($rawData['industry']);
                $industry=empty($languageStrings[$rawData['industry']])?$rawData['industry']:$languageStrings[$rawData['industry']];
                $visitingorderwithvisitor=$rawData['visitingorderwithvisitor']==null?"":$rawData['visitingorderwithvisitor'];
                $visitingordercontacts=$rawData['visitingordercontacts']==null?"":$rawData['visitingordercontacts'];
                $oldcust=$rawData['oldcust']>=1?1:0;

                $sql = "INSERT INTO
                        vtiger_salesdailydaydeal(salesdailybasicid,accountid,productid,marketprice,dealamount,paymentnature,allamount,firstpayment,visitingordercount,oldcustomers,industry,visitingobj,withvisitor,productname,arrivalamount,arrivalamountc,costprice) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                $adb->pquery($sql,array($recordid,$value,$fieldname[0]['daydealproduct'][$key],$fieldname[0]['daydealmarketprice'][$key],$fieldname[0]['daydealamount'][$key],$fieldname[0]['daydealpaymentnature'][$key],$fieldname[0]['daydealallamount'][$key],$fieldname[0]['daydealfirstpayment'][$key],
                    $rawData['visitingordernum'],$oldcust,$industry,$visitingordercontacts,$visitingorderwithvisitor,$fieldname[0]['daydealproductname'][$key],$daydealarrivalamounts,$fieldname[0]['daydealarrivalamount'][$key],$fieldname[0]['daydealstepprice'][$key]));
            }
        }
    }

    //当天成交的客户*******end********

    //40%客户开始*******START********
    $query="SELECT
                vtiger_account.accountid,
                vtiger_account.accountname,
                vtiger_visitingorder.visitingorderid,
                (SELECT vtiger_crmentity.smownerid FROM vtiger_crmentity WHERE crmid=vtiger_account.accountid AND vtiger_crmentity.setype='Accounts') as smownerid,
                vtiger_account.accountname,
                vtiger_account.newleadsource,
                vtiger_visitingorder.contacts,
                if(vtiger_account.linkname=vtiger_visitingorder.contacts,vtiger_account.title,(SELECT vtiger_contactdetails.`title` FROM vtiger_contactdetails WHERE vtiger_account.accountid=vtiger_contactdetails.accountid AND vtiger_contactdetails.`name`=vtiger_visitingorder.contacts)) AS title,
                if(vtiger_account.linkname=vtiger_visitingorder.contacts,vtiger_account.mobile,(SELECT vtiger_contactdetails.`mobile` FROM vtiger_contactdetails WHERE vtiger_account.accountid=vtiger_contactdetails.accountid AND vtiger_contactdetails.`name`=vtiger_visitingorder.contacts)) AS mobile,
                vtiger_visitingorder.startdate
                FROM `vtiger_accountrankhistory`
                LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_accountrankhistory.accountid LEFT JOIN vtiger_crmentity ON vtiger_account.accountid=vtiger_crmentity.crmid LEFT JOIN vtiger_visitingorder ON vtiger_account.accountid=vtiger_visitingorder.related_to WHERE vtiger_accountrankhistory.newaccountrank='forp_notv' AND vtiger_visitingorder.modulestatus='c_complete' AND vtiger_crmentity.smownerid=? AND vtiger_crmentity.deleted=0 AND left(vtiger_accountrankhistory.createdtime,10)=? GROUP BY vtiger_accountrankhistory.accountid";
    $result=$adb->pquery($query,array($current_user->id,$datetime));

    while($rawData=$adb->fetch_array($result)){
        //$leadsource=vtranslate($rawData['leadsource']);
        $leadsource=empty($languageStrings[$rawData['newleadsource']])?$rawData['newleadsource']:$languageStrings[$rawData['newleadsource']];
        $leadsource = vtranslate($leadsource,'Accounts');
        $mangerreturnendtime = date("Y-m-d H:00:00",(strtotime("+ 2 day",strtotime(substr($rawData['startdate'],0,10)))+12*60*60));
        $sql = 'INSERT INTO vtiger_salesdailyfournotv
                            (salesdailybasicid,accountid,accountsmownerid,visitingorderid,leadsource,linkname,mobile,title,accountname,startdatetime,mangereturnendtime) VALUES(?,?,?,?,?,?,?,?,?,?,?)';
        $adb->pquery($sql, array($recordid,$rawData['accountid'],$rawData['smownerid'],$rawData['visitingorderid'],$leadsource,$rawData['contacts'],$rawData['mobile'],$rawData['title'],$rawData['accountname'],$rawData['startdate'],$mangerreturnendtime));
    }
    //40%客户开始*******END********

    //次日拜访的客户列表*******START********
    $nextdatetime=date('Y-m-d',strtotime("+1 day"));
    $sql = 'INSERT INTO
                    vtiger_salesdailynextdayvisit
                    (salesdailybasicid,visitingorderid,visitingorderstartdate,contacts,title,visitingordernum,accountid,accountname,purpose,withvisitor)
                    SELECT
                    '.$recordid.',
                    vtiger_visitingorder.visitingorderid,
                    \''.$nextdatetime.'\',
                    vtiger_visitingorder.contacts,
                    if(vtiger_account.linkname=vtiger_visitingorder.contacts,vtiger_account.title,(SELECT vtiger_contactdetails.`title` FROM vtiger_contactdetails WHERE vtiger_account.accountid=vtiger_contactdetails.accountid AND vtiger_contactdetails.`name`=vtiger_visitingorder.contacts)) AS title,
                    (SELECT count(1) FROM vtiger_visitingorder LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_visitingorder.visitingorderid WHERE vtiger_crmentity.deleted=0 AND vtiger_visitingorder.visitingorderid AND vtiger_visitingorder.modulestatus!=\'a_exception\' AND vtiger_visitingorder.related_to=vtiger_account.accountid) AS visitingordernum,
                    vtiger_account.accountid,
                    vtiger_account.accountname,
                    vtiger_visitingorder.purpose,
                    IFNULL((SELECT GROUP_CONCAT(vtiger_users.last_name) FROM vtiger_users WHERE FIND_IN_SET(vtiger_users.id,replace(vtiger_visitingorder.accompany,\' |##| \',\',\'))),\'\') AS visitingorderwithvisitor
                    FROM vtiger_visitingorder
                LEFT JOIN vtiger_crmentity ON vtiger_visitingorder.visitingorderid=vtiger_crmentity.crmid
                LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_visitingorder.related_to
                WHERE vtiger_crmentity.deleted=0
                    AND vtiger_visitingorder.modulestatus!=\'a_exception\'
                    AND vtiger_crmentity.smownerid=? AND left(vtiger_visitingorder.startdate,10)=?';

    $adb->pquery($sql,array($current_user->id,$nextdatetime));
    return array($recordid);

    //次日拜访的客户列表*******END********



}
function salesdailydaydeal($fieldname,$userid){
    global $adb,$current_user;
    //$user = new Users();
    //$current_user = $user->retrieveCurrentUserInfoFromFile($userid);
    $datetime=date("Y-m-d");
    $query="SELECT
                    (SELECT last_name FROM vtiger_users WHERE vtiger_users.id=vtiger_achievementallot.receivedpaymentownid) AS user_name,
                    vtiger_achievementallot.receivedpaymentownid AS receiveid,
										vtiger_account.accountid,
                    vtiger_achievementallot.businessunit AS unit_price,
                    vtiger_receivedpayments.reality_date,
                    vtiger_account.accountname,
                    vtiger_servicecontracts.contract_no,
                    vtiger_account.industry,
                        (
                            SELECT
                                count(1)
                            FROM
                                vtiger_servicecontracts
                            LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_servicecontracts.servicecontractsid
                            WHERE
                                vtiger_crmentity.deleted = 0
                            AND vtiger_servicecontracts.modulestatus = 'c_complete'
                            AND vtiger_servicecontracts.sc_related_to = vtiger_account.accountid
                        ) AS oldcust,
                        (
                            SELECT
                                count(1)
                            FROM
                                vtiger_visitingorder
                            LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_visitingorder.visitingorderid
                            WHERE
                                vtiger_crmentity.deleted = 0
                            AND vtiger_visitingorder.visitingorderid
                            AND vtiger_visitingorder.modulestatus = 'c_complete'
                            AND vtiger_visitingorder.related_to = vtiger_account.accountid
                        ) AS visitingordernum,
                        (
                            SELECT
                                vtiger_visitingorder.contacts
                            FROM
                                vtiger_visitingorder
                            LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_visitingorder.visitingorderid
                            WHERE
                                vtiger_crmentity.deleted = 0
                            AND vtiger_visitingorder.visitingorderid
                            AND vtiger_visitingorder.modulestatus = 'c_complete'
                            AND vtiger_visitingorder.related_to = vtiger_account.accountid
                            ORDER BY
                                vtiger_visitingorder.visitingorderid DESC
                            LIMIT 1
                        ) AS visitingordercontacts,
                        (
                            SELECT
                                (
                                    SELECT
                                        GROUP_CONCAT(vtiger_users.last_name)
                                    FROM
                                        vtiger_users
                                    WHERE
                                        FIND_IN_SET(
                                            vtiger_users.id,
                                            REPLACE (
                                                vtiger_visitingorder.accompany,
                                                ' |##| ',
                                                ','
                                            )
                                        )
                                )
                            FROM
                                vtiger_visitingorder
                            LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_visitingorder.visitingorderid
                            WHERE
                                vtiger_crmentity.deleted = 0
                            AND vtiger_visitingorder.visitingorderid
                            AND vtiger_visitingorder.modulestatus = 'c_complete'
                            AND vtiger_visitingorder.related_to = vtiger_account.accountid
                            ORDER BY
                                vtiger_visitingorder.visitingorderid DESC
                            LIMIT 1
                        ) AS visitingorderwithvisitor,

                    vtiger_servicecontracts.total,
                    vtiger_servicecontracts.productid,
                    (SELECT CONCAT(vtiger_products.productname) FROM vtiger_products WHERE vtiger_products.productid IN(vtiger_servicecontracts.productid)) AS productname,
                    (SELECT sum(IFNULL(vtiger_products.unit_price,0)) FROM vtiger_products WHERE vtiger_products.productid IN(vtiger_servicecontracts.productid)) AS marketprice,
                    (SELECT sum(IFNULL(vtiger_products.tranperformance,0)) FROM vtiger_products WHERE vtiger_products.productid IN(vtiger_servicecontracts.productid)) AS allcost,
              
                    vtiger_achievementallot.matchdate as matchdate                FROM
                    vtiger_achievementallot
                LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid = vtiger_achievementallot.servicecontractid
                LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_servicecontracts.sc_related_to
                LEFT JOIN vtiger_receivedpayments ON vtiger_receivedpayments.receivedpaymentsid=vtiger_achievementallot.receivedpaymentsid
                LEFT JOIN vtiger_user2department ON vtiger_user2department.userid=vtiger_achievementallot.receivedpaymentownid
                LEFT JOIN vtiger_departments ON vtiger_user2department.departmentid=vtiger_departments.departmentid
                WHERE
                vtiger_receivedpayments.receivedpaymentsid>0
                AND vtiger_achievementallot.matchdate=?
                AND vtiger_achievementallot.matchdate!=''
                AND vtiger_achievementallot.matchdate IS NOT NULL 
                AND vtiger_achievementallot.receivedpaymentownid=?";
    $result = $adb->pquery($query, array($datetime, $userid));
    //$result = $adb->pquery($query, array('2017-07-07', 5455));
    if ($adb->num_rows($result)==0) {
        return array(0);
    }
    $array=array();
    while($row=$adb->fetch_array($result)){
        $array[]=$row;
	}
	return $array;

}
/**合同延期审核列表**/
function get_ExtensionTrial($fieldname,$userid)
{
    $result = array();
    if (!empty($userid)) {
        $sql = " SELECT vtiger_extensiontrial.extensionfrequency,vtiger_salesorderworkflowstages.salesorderworkflowstagesid,IFNULL((SELECT CONCAT(last_name,'[',IFNULL((SELECT departmentname FROM vtiger_departments WHERE departmentid = (SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_users.id LIMIT 1)),''),']',(IF(vtiger_users.`status` = 'Active','','[离职]'))) AS last_name FROM vtiger_users,vtiger_crmentity as crmtable WHERE crmtable.crmid=vtiger_extensiontrial.servicecontractsid AND crmtable.smownerid = vtiger_users.id),'--') AS username,
						(SELECT vtiger_users.email1 FROM vtiger_users,vtiger_crmentity as crmtable WHERE crmtable.crmid=vtiger_extensiontrial.servicecontractsid AND crmtable.smownerid = vtiger_users.id) as email,vtiger_crmentity.createdtime,
						vtiger_servicecontracts.contract_no,vtiger_extensiontrial.servicecontractsid AS servicecontractsid,
							vtiger_extensiontrial.content,
							vtiger_extensiontrial.extensiontrialid
						FROM
							vtiger_extensiontrial
						LEFT JOIN vtiger_crmentity ON vtiger_extensiontrial.extensiontrialid = vtiger_crmentity.crmid
						LEFT JOIN vtiger_servicecontracts ON vtiger_servicecontracts.servicecontractsid = vtiger_extensiontrial.servicecontractsid
						LEFT JOIN vtiger_salesorderworkflowstages ON  ( vtiger_salesorderworkflowstages.workflowsid=vtiger_extensiontrial.workflowsid AND vtiger_salesorderworkflowstages.salesorderid =vtiger_extensiontrial.extensiontrialid )
						WHERE
							1 = 1
						AND vtiger_crmentity.deleted = 0
						AND vtiger_extensiontrial.modulestatus='a_normal'
						AND vtiger_extensiontrial.auditor=?
                        GROUP BY vtiger_extensiontrial.extensiontrialid
						ORDER BY
							vtiger_extensiontrial.extensiontrialid DESC
						LIMIT 0,
						 20";
        global $adb;
        $res = $adb->pquery($sql, array($userid));
        $count = $adb->num_rows($res);
        if($count > 0){
            $result = array();
            for($i=0; $i<$count; $i++){
                $result[] = $adb->fetchByAssoc($res, $i);
            }

        }
    }
    return array($result);
}

/**
 * 合同延期审核处理
 * @param $fieldname
 * @param $userid
 */
function do_ExtensionTrialWorkflow($fieldname,$userid)
{
	global $adb;
	$datetime=date('Y-m-d H:i:s');
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
	$adb->pquery('UPDATE `vtiger_extensiontrial` SET modulestatus=\'c_complete\',workflowstime=?,workflowsnode=\'已完成\',extensionfrequency=if(extensionfrequency<2,extensionfrequency+1,extensionfrequency) WHERE extensiontrialid=?',array($datetime,$fieldname['record']));
	$adb->pquery("UPDATE vtiger_salesorderworkflowstages SET `schedule`=100,isaction=2,auditorid=?,auditortime='{$datetime}' WHERE salesorderid=? AND modulename='ExtensionTrial'",array($userid,$fieldname['record']));
    $sql = "UPDATE vtiger_servicecontracts SET vtiger_servicecontracts.confirmvalue=TRIM(TRAILING '##' FROM CONCAT('" . $current_user->last_name. "," . date('Y-m-d H:i:s') . "##',IFNULL(confirmvalue,''))),isconfirm=1,delayuserid=?,confirmlasttime='" . date('Y-m-d H:i:s') . "' WHERE servicecontractsid=?";
    $adb->pquery($sql,array($userid,$fieldname['contractid']));
}
/**合同拍照上传*/
function contracts_photograph($fieldname,$userid){
    global $adb,$log,$current_user;
	$sql2 = "UPDATE vtiger_files SET relationid=?,style=?,filestate=? WHERE attachmentsid=?";
	$params2 = array($fieldname['relationid'],$fieldname['style'],$fieldname['filestate'],$fieldname['attachmentsid']);
	$result = $adb->pquery($sql2, $params2);
	return array(array('success'=>true,'result'=>array()));


}
#移动端附件下载
function mobile_upload($fieldname,$userid){

    global $adb,$log,$current_user;
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
    if($fieldname['filename'] != '' && $fieldname['filesize'] > 0){
        global $upload_badext;
		//return array($fieldname['filename'],$fieldname['filesize']);
        $current_id = $adb->getUniqueID("vtiger_files");
        //$date_var = date("Y-m-d H:i:s");

        $file_name = $fieldname['filename'];
        $file_name=preg_replace('/(\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;)+|(\s|\x{3000}|\x{00a0}|\x{0020}|&nbsp;)+/u','',$file_name);
        $binFile = sanitizeUploadFileName($file_name, $upload_badext);
        $uploadfile=str_replace('/','',base64_encode($binFile));//去掉因base64l加密后成的/在误解析成路径引起的问题
        $newfileName=time();
		$filename = ltrim(basename(" " . $binFile)); //allowed filename like UTF-8 characters
        $filetype = $fieldname['filetype'];
        $filesize = $fieldname['filesize'];
        $upload_file_path = decideFilePath();
        $upload_status=file_put_contents($upload_file_path . $current_id . "_" .$newfileName,base64_decode($fieldname['filecontents']));

        if(!$upload_status){

            return array(array('success'=>false,'result'=>array('msg'=>'文件上传失败')));
            //exit;
        }

        $save_file = 'true';
        $sql2 = "insert into vtiger_files(attachmentsid, name,description, type, path,uploader,uploadtime,newfilename) values(?, ?,?, ?, ?,?,?,?)";
        $params2 = array($current_id, $filename, $fieldname['module'],$filetype, $upload_file_path,$userid,date('Y-m-d H:i:s'),$newfileName);
        $result = $adb->pquery($sql2, $params2);
        return array(array('success'=>true,'result'=>array('id'=>$current_id,'filename'=>$filename)));

    }else{
        return array(array('success'=>false,'result'=>array('msg'=>'文件上传失败')));
	}
}
function mobile_download($fieldname,$userid){
    $fileid=(int)base64_decode($fieldname['fileid']);
    if($fileid>0){
        global $adb;
        $result = $adb->pquery("SELECT * FROM vtiger_files WHERE attachmentsid=?", array($fileid));
        if($adb->num_rows($result)) {
            $fileDetails = $adb->query_result_rowdata($result);
            $filePath = $fileDetails['path'];
            if($fileDetails['newfilename']>0){
				$fileName=$fileDetails['newfilename'];
				$savedFile = $fileDetails['attachmentsid']."_".$fileName;
			}else{
				$fileName = html_entity_decode($fileDetails['name'], ENT_QUOTES, vglobal('default_charset'));
				$t_fileName = base64_encode($fileName);
				$t_fileName = str_replace('/', '', $t_fileName);
				$savedFile = $fileDetails['attachmentsid']."_".$t_fileName;
				if(!file_exists($filePath.$savedFile)){
					$savedFile = $fileDetails['attachmentsid']."_".$fileName;
				}
			}
            $fileSize = filesize($filePath.$savedFile);
            $fileSize = $fileSize + ($fileSize % 1024);

            if (fopen($filePath.$savedFile, "r")) {
                $fileContent = fread(fopen($filePath.$savedFile, "r"), $fileSize);

                return array(array(1,$fileDetails['type'],$fileDetails['name'],base64_encode($fileContent)));
//                return array(array(1,$fileDetails['type'],$fileName,base64_encode($fileContent)));
            }

            return array(array(0,'文件不存在'));
        }else{
            return array(array(0,'文件不存在'));
        }
    }
    return array(array(0,'文件不存在'));
    exit;
}
//通知模块
function get_NewList($fieldname){
    $_REQUEST['filter'] = 'NewList';
    return get_com_list('Knowledge',$fieldname);
}

function getSchoolMsg($fieldname,$userid) {
	$schoolid = $fieldname['schoolid'];
	$result = array();
	if (!empty($schoolid)) {
		$sql = " select schoolid,schoolname,address,contactsuser from vtiger_school where schoolid=?";
		global $adb;
		$res = $adb->pquery($sql, array($schoolid));
		$count = $adb->num_rows($res);
		if($count > 0){
			$temp_user = array();
	        $result = $adb->query_result_rowdata($res, 0);

	        $ttt = explode('#', $result['address']);
	        if(count($ttt) > 0) {
	        	$result['destination'] = $ttt[count($ttt) - 1];
	        } else {
	        	$result['destination'] = '';
	        }
	        $result['address'] = implode('', $ttt);
	    }
	}
	return array($result);
}

function searchSchool($fieldname,$userid) {
	$schoolname = $fieldname['schoolname'];
	$result = array();
	if (!empty($schoolname)) {
		$sql = " select * from vtiger_school where schoolname like '%$schoolname%' ";
		global $adb;
		$res = $adb->pquery($sql, array());
		$count = $adb->num_rows($res);
		if($count > 0){
			$temp_user = array();
	        for($i=0; $i<$count; $i++){
	            $temp_user[] = $adb->fetchByAssoc($res, $i);
	        }

	        foreach ($temp_user as $value) {
	        	$result[] = array('label'=>$value['schoolname'], 'value'=>$value['schoolname'], 'id'=>$value['schoolid']);
	        }


	    }
	}
	return $result;
}

/**
 * 取当前用户可查看的模块对应人的列表可能通
 * @param $fieldname
 * @param $userid
 * @return array
 */
function getUserRelativeUserList($fieldname,$userid){
    global $adb,$current_user;
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
    $where=getAccessibleUsers($fieldname['module'],'List',true);
	if($where!='1=1'){
        $query=' and id in ('.implode(',',$where).')';
    }else{
		$query='';
	}
    $sql = "SELECT
					id,brevitycode,last_name
				FROM
					vtiger_users WHERE  vtiger_users.status='Active'{$query}
				";
	if($fieldname['search_value']){
		$sql .= " and last_name like '%".$fieldname['search_value']."%'";
	}
    $sql .= " ORDER BY user_name";

	if($fieldname['pagecount']){
        $sql .= " limit ".$fieldname['pagenum'].','.$fieldname['pagecount'];
    }

    $res = $adb->pquery($sql, array());
    $temp_user = array();
    if($adb->num_rows($res) > 0){
        for($i=0; $i<$adb->num_rows($res); $i++){
            $temp_user[] = $adb->fetchByAssoc($res, $i);
        }
    }
    $sql1 = "SELECT
					count(1) as total
				FROM
					vtiger_users WHERE  vtiger_users.status='Active'{$query}";
    if($fieldname['search_value']){
        $sql1 .= " and last_name like '%".$fieldname['search_value']."%'";
    }
    $result1 = $adb->query($sql1,array());
    $total = $adb->query_result($result1,0,'total');

    return array($temp_user,$total);
}

//服务合同
function getServiceContracts($fieldname,$userid){
    $list = get_com_list('ServiceContracts', $fieldname);
    return $list;
}
/*服务合同详情*/
function oneServiceContracts($fieldname, $userid) {
	global $adb,$current_user;
	$user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);

    /*include_once('modules/Vtiger/Models/Vtiger_DetailView_Model.php');
	$serviceContractsModel = Vtiger_DetailView_Model::getInstance('ServiceContracts', $fieldname['id']);
	$recordModel = $serviceContractsModel->getRecord();
	$tt = $recordModel->entity->column_fields;*/
	$sql = "SELECT vtiger_servicecontracts.signaturetype,vtiger_servicecontracts.modulestatus,vtiger_servicecontracts.contract_no,vtiger_servicecontracts.total,vtiger_servicecontracts.bussinesstype,vtiger_servicecontracts.frameworkcontract, CASE vtiger_servicecontracts.modulestatus WHEN 'c_complete' THEN '已签收' WHEN 'c_cancel' THEN '作废' ELSE vtiger_servicecontracts.modulestatus END AS 't_modulestatus', vtiger_servicecontracts.sc_related_to, ( SELECT vtiger_account.accountname FROM vtiger_account WHERE vtiger_account.accountid = vtiger_servicecontracts.sc_related_to ) AS accountname, vtiger_servicecontracts.contract_type , vtiger_crmentity.smownerid, vtiger_servicecontracts.receivedate, vtiger_servicecontracts.signid, vtiger_servicecontracts.signdate, vtiger_servicecontracts.receiveid ,vtiger_servicecontracts.invoicecompany, vtiger_servicecontracts.returndate,vtiger_crmentity.smownerid, IF(vtiger_servicecontracts.multitype = '1', '是', '否') AS 'multitype', ( SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id = vtiger_crmentity.smownerid ) AS smownerid_last_name, ( SELECT vtiger_users.email1 FROM vtiger_users WHERE vtiger_users.id = vtiger_crmentity.smownerid ) AS email, ( SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id = vtiger_servicecontracts.receiveid ) AS receiveid_last_name , ( SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id = vtiger_servicecontracts.signid ) AS signid_last_name FROM vtiger_servicecontracts LEFT JOIN vtiger_crmentity ON vtiger_servicecontracts.servicecontractsid = vtiger_crmentity.crmid WHERE vtiger_servicecontracts.servicecontractsid = ? LIMIT 1";
	$sel_result = $adb->pquery($sql, array($fieldname['id']) );
	$res_cnt = $adb->num_rows($sel_result);
	$row = array();
	if($res_cnt > 0) {
		$rowData = $adb->query_result_rowdata($sel_result, 0);
	    $row[] = $rowData;
		$query="SELECT attachmentsid,`name` FROM vtiger_files WHERE delflag=0 AND relationid=? ";
        $dataresult = $adb->pquery($query,array($fieldname['id']));
        $norows = $adb->num_rows($dataresult);
        $result = array();
        if($norows){
            while($resultrow = $adb->fetch_array($dataresult)) {
                $result[]=$resultrow;
            }
        }
        $row['atta']=$result;
        $operate=setoperate($fieldname['id'],'ServiceContracts');
        $_REQUEST['realoperate']=$operate;
        include_once('modules/Vtiger/Models/Vtiger_DetailView_Model.php');
        $refillApplicationModel = Vtiger_DetailView_Model::getInstance('ServiceContracts', $fieldname['id']);
        $recordModel = $refillApplicationModel->getRecord();
        // 工作流
        include_once('includes/http/Request.php');
        $tt = $recordModel->getWorkflowsMobile(new Vtiger_Request($fieldname, $fieldname));
        $row['workflows']=$tt;
        $row['canCancelOrder']=0;

        if($recordModel->isOrderCancel($fieldname['id']) &&
			in_array($rowData['modulestatus'],array('已发放','c_recovered','c_complete')) && $rowData['signaturetype']!='eleccontract' && $rowData['smownerid']==$userid || $current_user->is_admin=='on'){
            $row['canCancelOrder']=1;
        }
        $row['MAXHANDLECONTRACTNUM']=ServiceContracts_Record_Model::servicecontracts_reviced($current_user->id);
    }

	return array(json_encode($row));
}
function getSupplierContracts($fieldname,$userid){
    $list = get_com_list('SupplierContracts', $fieldname);
    return $list;
}
/*服务合同详情*/
function oneSupplierContracts($fieldname, $userid) {
    global $adb,$current_user;
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);

    /*include_once('modules/Vtiger/Models/Vtiger_DetailView_Model.php');
	$serviceContractsModel = Vtiger_DetailView_Model::getInstance('ServiceContracts', $fieldname['id']);
	$recordModel = $serviceContractsModel->getRecord();
	$tt = $recordModel->entity->column_fields;*/
    $sql = "SELECT
				vtiger_suppliercontracts.contract_name AS vtiger_suppliercontractscontract_name,
                vtiger_suppliercontracts.paymentclause AS vtiger_suppliercontractspaymentclause,
                vtiger_suppliercontracts.total AS vtiger_suppliercontractstotal,
				vtiger_suppliercontracts.suppliercontractsstatus AS vtiger_suppliercontractssuppliercontractsstatus,
				vtiger_suppliercontracts.contract_no AS vtiger_suppliercontractscontract_no,
				vtiger_suppliercontracts.file AS vtiger_suppliercontractsfile,
				vtiger_suppliercontracts.modulestatus AS vtiger_suppliercontractsmodulestatus,
				vtiger_suppliercontracts.remark AS vtiger_suppliercontractsremark,
				vtiger_suppliercontracts.receivedate AS vtiger_suppliercontractsreceivedate,
				(select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_suppliercontracts.signid=vtiger_users.id) AS vtiger_suppliercontractssignid,
				vtiger_suppliercontracts.returndate AS vtiger_suppliercontractsreturndate,
				vtiger_suppliercontracts.currencytype AS vtiger_suppliercontractscurrencytype,
				vtiger_suppliercontracts.signdate AS vtiger_suppliercontractssigndate,
				(SELECT vtiger_vendor.vendorname FROM vtiger_vendor WHERE vtiger_vendor.vendorid=vtiger_suppliercontracts.vendorid) AS vtiger_suppliercontractsvendorid,
				vtiger_crmentity.smownerid AS vtiger_crmentityassigned_user_id,
				vtiger_suppliercontracts.paymethed AS vtiger_suppliercontractspaymethed,
				vtiger_suppliercontracts.effectivetime AS vtiger_suppliercontractseffectivetime,
				vtiger_suppliercontracts.suppliercontracts_no AS vtiger_suppliercontractssuppliercontracts_no,
				vtiger_suppliercontracts.sideagreement AS vtiger_suppliercontractssideagreement,
				vtiger_suppliercontracts.workflowsid AS vtiger_suppliercontractsworkflowsid,
				vtiger_suppliercontracts.workflowstime AS vtiger_suppliercontractsworkflowstime,
				vtiger_suppliercontracts.workflowsnode AS vtiger_suppliercontractsworkflowsnode,
				vtiger_suppliercontracts.invoicecompany AS vtiger_suppliercontractsinvoicecompany,
				vtiger_suppliercontracts.receiptorid AS vtiger_suppliercontractsreceiptorid,
				vtiger_suppliercontracts.iscomplete AS vtiger_suppliercontractsiscomplete,
				(select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_crmentity.smownerid=vtiger_users.id) AS smowner_owner,
				vtiger_crmentity.deleted
			FROM
				vtiger_crmentity
			LEFT JOIN vtiger_suppliercontracts ON vtiger_suppliercontracts.suppliercontractsid = vtiger_crmentity.crmid
			WHERE
				vtiger_crmentity.crmid =?
			LIMIT 1";
    $sel_result = $adb->pquery($sql, array($fieldname['id']) );
    $res_cnt = $adb->num_rows($sel_result);
    $row = array();

    if($res_cnt > 0) {

        $row[] = $adb->query_result_rowdata($sel_result, 0);
        $query="SELECT attachmentsid,`name` FROM vtiger_files WHERE delflag=0 AND relationid=?";
        $dataresult = $adb->pquery($query,array($fieldname['id']));
        $norows = $adb->num_rows($dataresult);
        $result = array();
        if($norows){
            while($resultrow = $adb->fetch_array($dataresult)) {
                $result[]=$resultrow;
            }
        }
        $row['atta']=$result;
        $operate=setoperate($fieldname['id'],'SupplierContracts');
        $_REQUEST['realoperate']=$operate;
        include_once('modules/Vtiger/Models/Vtiger_DetailView_Model.php');
        $refillApplicationModel = Vtiger_DetailView_Model::getInstance('SupplierContracts', $fieldname['id']);
        $recordModel = $refillApplicationModel->getRecord();
        // 工作流
        include_once('includes/http/Request.php');
        $tt = $recordModel->getWorkflowsMobile(new Vtiger_Request($fieldname, $fieldname));
        $row['workflows']=$tt;

    }
    return array(json_encode($row));
}
/*服务合同补充协议*/
function getContractsAgreement($fieldname,$userid){
    $list = get_com_list('ContractsAgreement', $fieldname);
    return $list;
}

/**
 * @param $fieldname
 * @param $userid
 * @return array
 * @author: steel.liu
 * @Date:xxx
 * 通用列表接口
 */
function getComListImplements($fieldname,$userid){
	$module=$fieldname['module'];
    $list = get_com_list($module, $fieldname);
    return $list;
}

/**
 * @param $fieldname
 * @param $userid
 * @return array
 * @author: steel.liu
 * @Date:xxx
 * 通用的RecordModule接口
 */
function getComRecordModule($fieldname,$userid){
	global $isallow,$current_user;
    $isallow=array('SalesOrder','Invoice','Quotes','VisitingOrder','Vacate','OrderChargeback','RefillApplication',
        'ExtensionTrial', 'Suppcontractsextension','PurchaseInvoice','OrderChargeback', 'Newinvoice', 'Vendors', 'Schoolvisit','ServiceContracts','ContractsAgreement','SupplierContracts','SuppContractsAgreement','AccountPlatform','ProductProvider','ContractGuarantee'
    ,'RefundTimeoutAudit','SeparateInto','UserManger','AchievementallotStatistic','AchievementSummary','ClosingDate','Staypayment','EmployeeAbility','PreInvoiceDeferral','InputInvoice', 'CustomerStatement','SupplierStatement','ContractDelaySign');//'ServiceContracts',
    if($userid){	
	$user = new Users();
    	$current_user = $user->retrieveCurrentUserInfoFromFile($userid);
    }
	$moduleName=$fieldname['module'];
    $recordModel=Vtiger_Record_Model::getCleanInstance($moduleName);
    $record = $fieldname['record'];
    $actionName=$fieldname['action'];
    $request = new Vtiger_Request($fieldname,$fieldname);
    $request->set("record",$record);
    $return = $recordModel->$actionName($request);
    return array($return);
}
function getAccountContent($fieldname,$userid){
    global $adb;
    $recordId = $fieldname['accountid'];
    $accountInfo=$adb->pquery(" SELECT  *  FROM vtiger_account WHERE accountid=? limit 1 ",array($recordId));
    $accountInfo = $adb->query_result_rowdata($accountInfo, 0);
    return array('result'=>$accountInfo);
}

/*服务合同补充协议*/
function oneContractsAgreement($fieldname, $userid) {
    global $adb,$current_user;
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);

    /*include_once('modules/Vtiger/Models/Vtiger_DetailView_Model.php');
	$serviceContractsModel = Vtiger_DetailView_Model::getInstance('ServiceContracts', $fieldname['id']);
	$recordModel = $serviceContractsModel->getRecord();
	$tt = $recordModel->entity->column_fields;*/
    $sql = "SELECT
				vtiger_contractsagreement.file AS agreementfile,
				vtiger_crmentity.smownerid AS vtiger_crmentityassigned_user_id,
			(select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_crmentity.smownerid=vtiger_users.id) AS smownerid_ref,
			vtiger_crmentity.createdtime AS vtiger_crmentitycreatedtime,
				vtiger_crmentity.modifiedtime AS vtiger_crmentitymodifiedtime,
				vtiger_crmentity.modifiedby AS vtiger_crmentitymodifiedby,
				vtiger_crmentity.description AS vtiger_crmentitydescription,
				vtiger_contractsagreement.workflowsid AS vtiger_contractsagreementworkflowsid,
				vtiger_contractsagreement.servicecontractsid AS vtiger_contractsagreementservicecontractsid,
				(SELECT vtiger_servicecontracts.contract_no FROM vtiger_servicecontracts WHERE vtiger_servicecontracts.servicecontractsid=vtiger_contractsagreement.servicecontractsid) AS contract_no,
				vtiger_contractsagreement.modulestatus AS vtiger_contractsagreementmodulestatus,
				vtiger_contractsagreement.workflowstime AS vtiger_contractsagreementworkflowstime,
				vtiger_contractsagreement.workflowsnode AS vtiger_contractsagreementworkflowsnode,
				vtiger_contractsagreement.remarks AS vtiger_contractsagreementremarks,
				vtiger_contractsagreement.accountid AS vtiger_contractsagreementaccount_id,
				(SELECT vtiger_account.accountname FROM vtiger_account WHERE vtiger_account.accountid=vtiger_contractsagreement.accountid) AS accountname,
				vtiger_contractsagreement.dateofapp AS vtiger_contractsagreementdateofapp,
				vtiger_contractsagreement.receiptorid AS vtiger_contractsagreementreceiptorid,
				(select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_contractsagreement.receiptorid=vtiger_users.id) AS receiptorid,
				vtiger_contractsagreement.newservicecontractsno AS vtiger_contractsagreementnewservicecontractsno,
				vtiger_crmentity.deleted
			FROM
				vtiger_crmentity
			LEFT JOIN vtiger_contractsagreement ON vtiger_contractsagreement.contractsagreementid = vtiger_crmentity.crmid
			WHERE
				vtiger_crmentity.crmid=?
			LIMIT 1";
    $sel_result = $adb->pquery($sql, array($fieldname['id']) );
    $res_cnt = $adb->num_rows($sel_result);
    $row = array();
    if($res_cnt > 0) {
        $row[] = $adb->query_result_rowdata($sel_result, 0);
        //参照格式:QQ图片20160511143121.jpg##3638*|*公司资料.jpg##3639
        $attachmentsidDatas=explode("*|*",$row[0]['agreementfile']);
        $result = array();
        foreach($attachmentsidDatas as $attachmentsidData){
        	$tempFileName=explode("##",$attachmentsidData);
            $result[]=array('attachmentsid'=>$tempFileName[1],"name"=>$tempFileName[0]);
		}
        $row['atta']=$result;
        $operate=setoperate($fieldname['id'],'ContractsAgreement');
        $_REQUEST['realoperate']=$operate;
        include_once('modules/Vtiger/Models/Vtiger_DetailView_Model.php');
        $refillApplicationModel = Vtiger_DetailView_Model::getInstance('ContractsAgreement', $fieldname['id']);
        $recordModel = $refillApplicationModel->getRecord();
        // 工作流
        include_once('includes/http/Request.php');
        $tt = $recordModel->getWorkflowsMobile(new Vtiger_Request($fieldname, $fieldname));
        $row['workflows']=$tt;
    }
    return array(json_encode($row));
}

function getSuppContractsAgreement($fieldname,$userid){
    $list = get_com_list('SuppContractsAgreement', $fieldname);
    return $list;
}
/*服务合同详情*/
function oneSuppContractsAgreement($fieldname, $userid) {
    global $adb,$current_user;
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);

    /*include_once('modules/Vtiger/Models/Vtiger_DetailView_Model.php');
	$serviceContractsModel = Vtiger_DetailView_Model::getInstance('ServiceContracts', $fieldname['id']);
	$recordModel = $serviceContractsModel->getRecord();
	$tt = $recordModel->entity->column_fields;*/
    $sql = "SELECT
	vtiger_suppcontractsagreement.file AS agreementfile,
    vtiger_suppcontractsagreement.contract_name AS vtiger_suppliercontractscontract_name,
    vtiger_suppcontractsagreement.paymentclause AS vtiger_suppliercontractspaymentclause,
    vtiger_suppcontractsagreement.total AS vtiger_suppliercontractstotal,
	vtiger_crmentity.smownerid AS vtiger_crmentityassigned_user_id,
	(select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_crmentity.smownerid=vtiger_users.id) AS smownerid_ref,
	vtiger_crmentity.createdtime AS vtiger_crmentitycreatedtime,
	vtiger_crmentity.modifiedtime AS vtiger_crmentitymodifiedtime,
	vtiger_crmentity.modifiedby AS vtiger_crmentitymodifiedby,
	vtiger_crmentity.description AS vtiger_crmentitydescription,
	vtiger_suppcontractsagreement.workflowsid AS vtiger_suppcontractsagreementworkflowsid,
	vtiger_suppcontractsagreement.suppliercontractsid AS vtiger_suppcontractsagreementsuppliercontractsid,
	(SELECT vtiger_suppliercontracts.contract_no FROM vtiger_suppliercontracts WHERE vtiger_suppliercontracts.suppliercontractsid=vtiger_suppcontractsagreement.suppliercontractsid) AS contract_no,
	vtiger_suppcontractsagreement.modulestatus AS vtiger_suppcontractsagreementmodulestatus,
	vtiger_suppcontractsagreement.workflowstime AS vtiger_suppcontractsagreementworkflowstime,
	vtiger_suppcontractsagreement.workflowsnode AS vtiger_suppcontractsagreementworkflowsnode,
	vtiger_suppcontractsagreement.remarks AS vtiger_suppcontractsagreementremarks,
	vtiger_suppcontractsagreement.vendorid AS vtiger_suppcontractsagreementvendorid,
	(SELECT vtiger_vendor.vendorname FROM vtiger_vendor WHERE vtiger_vendor.vendorid=vtiger_suppcontractsagreement.vendorid) AS accountname,
	vtiger_suppcontractsagreement.dateofapp AS vtiger_suppcontractsagreementdateofapp,
	vtiger_suppcontractsagreement.receiptorid AS vtiger_suppcontractsagreementreceiptorid,
	(select CONCAT(last_name,'[',IFNULL((select departmentname from vtiger_departments where departmentid = (select departmentid FROM vtiger_user2department where userid=vtiger_users.id LIMIT 1)),''),']',(if(`status`='Active','','[离职]'))) as last_name from vtiger_users where vtiger_suppcontractsagreement.receiptorid=vtiger_users.id) AS receiptorid,
	vtiger_suppcontractsagreement.newservicecontractsno AS vtiger_suppcontractsagreementnewservicecontractsno,
	vtiger_crmentity.deleted
	FROM
		vtiger_crmentity
	LEFT JOIN vtiger_suppcontractsagreement ON vtiger_suppcontractsagreement.contractsagreementid = vtiger_crmentity.crmid
	WHERE
		vtiger_crmentity.crmid =?
	LIMIT 1";
    $sel_result = $adb->pquery($sql, array($fieldname['id']) );
    $res_cnt = $adb->num_rows($sel_result);
    $row = array();
    if($res_cnt > 0) {
        $row[] = $adb->query_result_rowdata($sel_result, 0);
        //参照格式:QQ图片20160511143121.jpg##3638*|*公司资料.jpg##3639
        $attachmentsidDatas=explode("*|*",$row[0]['agreementfile']);
        $result = array();
        foreach($attachmentsidDatas as $attachmentsidData){
            $tempFileName=explode("##",$attachmentsidData);
            $result[]=array('attachmentsid'=>$tempFileName[1],"name"=>$tempFileName[0]);
        }
        $operate=setoperate($fieldname['id'],'SuppContractsAgreement');
        $_REQUEST['realoperate']=$operate;
        $row['atta']=$result;
        include_once('modules/Vtiger/Models/Vtiger_DetailView_Model.php');
        $refillApplicationModel = Vtiger_DetailView_Model::getInstance('SuppContractsAgreement', $fieldname['id']);
        $recordModel = $refillApplicationModel->getRecord();
        // 工作流
        include_once('includes/http/Request.php');
        $tt = $recordModel->getWorkflowsMobile(new Vtiger_Request($fieldname, $fieldname));
        $row['workflows']=$tt;
    }
    return array(json_encode($row));
}

//T云独立建站售卖表更新  gaocl add 2018/04/08
function updateTyunStationSale($fieldname,$userid){
    global $adb,$log;
    //$user = new Users();
    //$current_user = $user->retrieveCurrentUserInfoFromFile($userid);
    //'ActionType'=>'动作类型 1 开通账号 2 完成',
    $actionType = $fieldname['ActionType'];
    $contractCode = $fieldname['ContractCode'];

    $sql="SELECT 1 FROM vtiger_tyunstationsale WHERE contractcode=?";
    $sel_result = $adb->pquery($sql, array($contractCode) );
    $res_cnt = $adb->num_rows($sel_result);

	if($res_cnt <=0){
        $result_up['success']= 3;
        return array($result_up);
	}

	if($actionType == '1'){
        $update_sql = "UPDATE vtiger_tyunstationsale SET loginname=?,opendate=? WHERE contractcode=?";
	}else if($actionType == '2'){
        $update_sql = "UPDATE vtiger_tyunstationsale SET loginname=?,finnishdate=? WHERE contractcode=?";
    }else{
        $result_up['success']= -1;
        return array($result_up);
	}

	//'LoginName'=>'客户账号',
	//'ExecDate'=>'执行时间'
	try{
        $adb->pquery($update_sql, array($fieldname['LoginName'],$fieldname['ExecDate'],$fieldname['ContractCode']) );
	}catch(WebServiceException $exception){
        //$log->debug($exception->getMessage());
        $result_up['success']= 2;
        return array($result_up);
    }
    $result_up['success']= 1;
    return array($result_up);
}
//T云独立建站售卖数据保存  gaocl add 2018/04/09
function saveTyunStationSale($fieldname,$userid){
    global $adb,$current_user,$log;
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
    include_once('includes/http/Request.php');
    include_once('modules/Vtiger/actions/Save.php');

    $result_up['success']= 1;
    $result_up['msg']= "购买成功";

	try{
		//判断合同编号是否重复
		$query_sql = "SELECT 1 FROM vtiger_tyunstationsale WHERE contractcode=?";
		$sel_result = $adb->pquery($query_sql, array($fieldname['contractcode']) );
		$res_cnt = $adb->num_rows($sel_result);
		if($res_cnt > 0){
			$result_up['success']= 2;
			$result_up['msg']= "合同编号重复";
			return array($result_up);
		}

        //判断是否已经购买过(判断激活码表中是否存在)
        $query_sql = "SELECT 1 FROM vtiger_activationcode WHERE status IN(0,1) AND contractid=?";
        $sel_result = $adb->pquery($query_sql, array($fieldname['contractid']) );
        $res_cnt = $adb->num_rows($sel_result);
        if($res_cnt > 0){
            $result_up['success']= 2;
            $result_up['msg']= "该合同已经领取过激活码,不能购买";
            return array($result_up);
        }

		//获取客户登录账户名
		$service_sql = "SELECT vtiger_users.user_name FROM vtiger_servicecomments
						INNER JOIN vtiger_crmentity ON(vtiger_crmentity.crmid=vtiger_servicecomments.servicecommentsid)
						LEFT JOIN vtiger_users ON(vtiger_users.id=vtiger_servicecomments.serviceid AND vtiger_users.status='Active')
						WHERE assigntype='accountby' AND related_to=? AND vtiger_crmentity.deleted=0";
		$sel_result = $adb->pquery($service_sql, array($fieldname['accountid']) );
		$res_cnt = $adb->num_rows($sel_result);
		if($res_cnt > 0){
			$row[] = $adb->query_result_rowdata($sel_result, 0);
			$fieldname['serviceloginname'] = $row[0]['user_name'];
		}

		//保存处理
		/*$insert_sql = "INSERT INTO vtiger_tyunstationsale(	`contractid`,`accountid`,`companyname`,`agentcode`,	`servicetype`,`buycount`,`buyyear`,`signaddress`,`signdate`,`custphone`,`status`,`contractcode`,`salesname`,`salesphone`,`serviceloginname`,`createdid`,`createdtime`)
					VALUES(	?,?,?,?,?,?,?,?,NOW(),?,1,?,?,?,?,?,NOW())";
		$adb->pquery($insert_sql,array($fieldname['contractid'],$fieldname['accountid'],$fieldname['companyname'],$fieldname['agentcode'],$fieldname['servicetype'],$fieldname['buycount'],$fieldname['buyyear'],$fieldname['signaddress'],
			$fieldname['custphone'],$fieldname['contractcode'],$fieldname['salesname'],$fieldname['salesphone'],$fieldname['serviceloginname'],$fieldname['createdid']));*/
        $insert_sql = "INSERT INTO vtiger_tyunstationsale(	`contractid`,`accountid`,`companyname`,`agentcode`,	`serviceinfo`,servicecount,`signaddress`,`signdate`,`custphone`,`status`,`contractcode`,`salesname`,`salesphone`,`serviceloginname`,`createdid`,`createdtime`,`classtype`)
					VALUES(	?,?,?,?,?,?,?,NOW(),?,1,?,?,?,?,?,NOW(),'buy')";
        $adb->pquery($insert_sql,array($fieldname['contractid'],$fieldname['accountid'],$fieldname['companyname'],$fieldname['agentcode'],$fieldname['serviceinfo'],$fieldname['servicecount'],$fieldname['signaddress'],
            $fieldname['custphone'],$fieldname['contractcode'],$fieldname['salesname'],$fieldname['salesphone'],$fieldname['serviceloginname'],$fieldname['createdid']));
	}catch(Exception $exception){
		//$log->debug($exception->getMessage());
		$result_up['success']= -1;
        $result_up['msg']= $exception->getMessage();
		return array($result_up);
	}
    return array($result_up);
}

//T云建站合同服务进度接口  gaocl add 2018/05/03
function saveStationServiceProgress($fieldname,$userid){
    global $adb,$log;
    //'ServiceType'=>'服务类型', 1小程序 2 PC 3 移动
    $serviceType = $fieldname['ServiceType'];
    $contractCode = $fieldname['ContractCode'];
    $serviceID = $fieldname['ServiceID'];
    $loginName = $fieldname['LoginName'];
    $execDate = $fieldname['ExecDate'];

    $sql="SELECT 1 FROM vtiger_tyunstationsale_detail WHERE contractcode=? AND serviceid=?";
    $sel_result = $adb->pquery($sql, array($contractCode,$serviceID) );
    $res_cnt = $adb->num_rows($sel_result);

    try{
        if($res_cnt > 0){
            $update_sql = "UPDATE vtiger_tyunstationsale_detail SET execdate=?,updatetime=NOW() WHERE contractcode=? AND serviceid=?";
            $adb->pquery($update_sql, array($loginName,$serviceType,$execDate,$contractCode,$serviceID) );
        }else{
            $insert_sql = "INSERT INTO vtiger_tyunstationsale_detail(contractcode,serviceid,loginname,servicetype,execdate,createdtime) VALUES(?,?,?,?,?,NOW())";
            $adb->pquery($insert_sql, array($contractCode,$serviceID,$loginName,$serviceType,$execDate) );
        }

        //更新完成时间(所有服务都完成才能更新)
        $update_sql = "UPDATE vtiger_tyunstationsale SET finnishdate=(SELECT MAX(execdate) FROM vtiger_tyunstationsale_detail WHERE vtiger_tyunstationsale_detail.contractcode=?)
					WHERE servicecount=(SELECT COUNT(1) FROM vtiger_tyunstationsale_detail WHERE vtiger_tyunstationsale_detail.contractcode=?);";
        $adb->pquery($update_sql, array($contractCode,$contractCode));
    }catch(WebServiceException $exception){
        //$log->debug($exception->getMessage());
        $result_up['success']= -1;
        return array($result_up);
    }
    $result_up['success']= 1;
    return array($result_up);
}

/**
 * 获取用户平台信息
 * @param $fieldname
 * @param $userid
 * @return array
 */
 function search_accountplatform($fieldname,$userid){
	$recordModel=Vtiger_Record_Model::getCleanInstance('RefillApplication');
	$accountid = $fieldname['searchValue'];
	$request = new Vtiger_Request();
	$request->set("record",$accountid);
	if(!empty($fieldname['searchdid'])&& $fieldname['searchdid']!=''){
        $request->set("searchdid",$fieldname['searchdid']);
    }
	$request->set("pageNum",$fieldname['pageNum']);
	$return = $recordModel->getAccountPlatform($request);
	return array($return);
}
/**
 * 获取供应商产品服务信息
 * @param $fieldname
 * @param $userid
 * @return array
 */
function search_vendor_productservice($fieldname,$userid){
    $recordModel=Vtiger_Record_Model::getCleanInstance('RefillApplication');
    $vendorid = $fieldname['searchValue'];
    $accountid = $fieldname['accountid'];
	$searchDid = $fieldname['searchDid'];
    $type = $fieldname['type'];
    $request = new Vtiger_Request($fieldname,$fieldname);
    $request->set("record",$vendorid);
    $request->set("accountid",$accountid);
	$request->set("searchDid",$searchDid);
	$request->set("pageNum",$fieldname['pageNum']);
    // 退币转冲 type=1
    if($type==1){
        $request->set("rechargesource",'COINRETURN');
	}else{
        $request->set("rechargesource",'Vendors');
	}
	$return = $recordModel->getVendorBankInfo($request);
	return array($return);
}

/**
 * 获取用户平台信息
 * @param $fieldname
 * @param $userid
 * @return array
 */
function search_receivedpayments($fieldname,$userid){
    $recordModel=Vtiger_Record_Model::getCleanInstance('RefillApplication');
    $servicecontractid = $fieldname['searchValue'];

    $request = new Vtiger_Request();
    $request->set("record",$servicecontractid);
    $return = $recordModel->getReceivedPaymentsData($request);
    return array($return);
}

/**
 * 建站购买邮件发送 gaocl add 2018/04/24
 * @param $fieldname
 * @param $userid
 * @return array
 * @throws Exception
 */
function sendMailTyunStationSale($fieldname, $userid) {
    global $adb,$current_user,$log;
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);

    include_once('modules/Vtiger/models/Record.php');
    $sql = "SELECT * FROM `vtiger_users` WHERE id=? LIMIT 1";
    $sel_result = $adb->pquery($sql, array($current_user->reports_to_id));
    $res_cnt = $adb->num_rows($sel_result);
    $agent_email = '';
    $agent_last_name = '';
    if($res_cnt > 0) {
        $row = $adb->query_result_rowdata($sel_result, 0);
        $agent_email = $row['email1'];
        $agent_last_name = $row['last_name'];
    }

    $email = $current_user->email1;
    $last_name = $current_user->last_name;
    $department = $current_user->department;

    $Subject = 'T云建站购买成功通知';
   /* $arr_servicetype = array(
        '1'=>'小程序标准建站',
        '2'=>'PC端标准建站',
        '3'=>'移动端标准建站'
    );
	$servicetype = $fieldname['servicetype'];
    $servicetype_name = $arr_servicetype[$servicetype];*/
    $nowTime = date("Y-m-d H:i", time());
    $body =  "员工：{$last_name}<br>部门：{$department}<br>客户：{$fieldname['companyname']}<br>客户手机：{$fieldname['custphone']}{$fieldname['buyContent']}<br><br>购买时间：{$nowTime}<br>";

    $address = array(
        array('mail'=>$email, 'name'=>$last_name),//销售
        array('mail'=>$agent_email, 'name'=>$agent_last_name)//销售上级
    );
    $log->info("T云建站购买成功通知邮件内容：".$body);
    $log->info("T云建站购买成功通知邮件地址：".$address);

    //测试
   /*$address = array(
       array('mail'=>$agent_email, 'name'=>$agent_last_name)//销售上级
    );*/

    //return array($address);

    $tt = Vtiger_Record_Model::sendMail($Subject,$body,$address);
    return array($tt);
}

/**
 *建站续费
 * @param $fieldname
 * @param $userid
 * @return array
 */
function saveStationRenew($fieldname, $userid){
	$recordModel=Vtiger_Record_Model::getCleanInstance('ServiceContracts');
	$res=$recordModel->RenewCloudSiteUser($fieldname);
	return array($res);
}

/**
 * 获取代理商ID
 */
function getagentid($userid){
	global $adb,$current_user;
	$query='SELECT * FROM vtiger_departmentragentid WHERE FIND_IN_SET(?,userids) limit 1';
	$result=$adb->pquery($query,array($userid));
	if($adb->num_rows($result)){
		$row = $adb->query_result_rowdata($result, 0);
		return array($row['agentid']);
	}
	$user = new Users();
	$current_user = $user->retrieveCurrentUserInfoFromFile($userid);
	$query = "SELECT parentdepartment FROM `vtiger_departments` WHERE departmentid = (SELECT departmentid FROM `vtiger_user2department` WHERE userid=? LIMIT 1)";
	$listResult = $adb->pquery($query, array($current_user->id));
	$res_cnt = $adb->num_rows($listResult);
	if($res_cnt > 0) {
		$row = $adb->query_result_rowdata($listResult, 0);
		$parentdepartment=$row['parentdepartment'].'::';
		$agents=0;
		$query='SELECT * FROM vtiger_departmentragentid WHERE LENGTH(pdepartmentid)>0 ORDER BY LENGTH(pdepartmentid) DESC';
		$result=$adb->pquery($query,array());
		while($rowdepart=$adb->fetch_array($result)){
			$pdepartmentid=$rowdepart['pdepartmentid'];
			if(empty($pdepartmentid)){
				continue;
			}
			$pdepartmentid=$rowdepart['pdepartmentid'].'::';
			//if(false !== strpos($row['parentdepartment'], $rowdepart['pdepartmentid'])){
			if(false !== strpos($parentdepartment, $pdepartmentid)){
				$agents = $rowdepart['agentid'];
				break;
			}
		}
		return array($agents);
	}

	return array(0);
}
/**
 * 查询T云购买信息
 * @param $fieldname
 * @param $userid
 * @return array
 */
function searchTyunBuyServiceInfo($fieldname, $userid){
    global $adb;
    $tyun_account = $fieldname['tyun_account'];
    $tyun_type = $fieldname['tyun_type'];
    $query="SELECT 
			M.contractid,
			M.contractname,
			M.productid,
			(SELECT vtiger_products.productname FROM vtiger_products WHERE vtiger_products.tyunproductid=M.productid LIMIT 1) AS productname,
			M.classtype,
			(SELECT MAX(str_to_date(REPLACE(MM.expiredate,'/','-'),'%Y-%m-%d')) FROM vtiger_activationcode MM WHERE MM.status IN(0,1) AND MM.usercode=M.usercode) AS expiredate,
			IF(IFNULL(P.receivetime,M.receivetime)>'2017-11-04',1,0) as receivetimeflag,
			IFNULL(P.customerid,M.customerid) AS customerid,
			IFNULL(P.customername,M.customername) AS customername,
			IFNULL(P.companyname,M.companyname) AS companyname,
			IFNULL(P.activecode,M.activecode) AS activecode,
			IFNULL(P.activationcodeid,M.activationcodeid) AS activationcodeid,
			(SELECT CONCAT('合同:',contractname,' / 下单时间:',receivetime) FROM vtiger_activationcode WHERE classtype=? AND usercode=M.usercode AND `status` IN(0,1) ORDER BY receivetime desc LIMIT 1) AS latelyadd,
			IFNULL(P.agents,M.agents) AS agents,
            M.startdate
			FROM vtiger_activationcode M
			LEFT JOIN vtiger_activationcode P ON(M.buyid=P.activationcodeid)
			WHERE M.status IN(0,1) AND M.classtype IN('buy','upgrade','degrade')
			AND M.usercode=? ORDER BY M.receivetime DESC LIMIT 1";
    try{
		//$listResult = $adb->pquery($query, array("%$tyun_account%"));
        $listResult = $adb->pquery($query, array($tyun_type,$tyun_account));
		$res = array();
		$rowdata='';
		while($rawData=$adb->fetch_array($listResult)) {
			$res[] =  $rawData;
            $rowdata=$rowdata;
		}
		if(count($res) == 0){
            return array(array('success'=>false,'message'=>'未查询到购买信息','buyList'=>null));
		}
		if($tyun_type=='degrade'){
            if(strtotime($rowdata['expiredate'])>strtotime(date('Y-m-d'))){
                return array(array('success'=>false,'message'=>'未到期,不允许降级','buyList'=>null));
			}
		}
    }catch(WebServiceException $exception){
        return array(array('success'=>false,'message'=>'查询数据错误','buyList'=>null));
    }
    return array(array('success'=>true,'message'=>'','buyList'=>$res));

}

/**
 * 查询T云升级产品
 * @param $fieldname
 * @param $userid
 * @return array
 */
function searchTyunUpgradeProduct($fieldname,$userid){
    $recordModel=Vtiger_Record_Model::getCleanInstance('ActivationCode');
    $p_productid = $fieldname['p_productid'];
    $is_degrade = $fieldname['is_degrade'];
    $request = new Vtiger_Request();
    $request->set("p_productid",$p_productid);
    $request->set("is_degrade",$is_degrade);
    $return = $recordModel->searchTyunUpgradeProduct($request);
    return array($return);
}

/**
 * 查询T云另购产品
 * @param $fieldname
 * @param $userid
 * @return array
 */
function getTyunServiceItem($fieldname,$userid){
    $recordModel=Vtiger_Record_Model::getCleanInstance('ActivationCode');
    $request = new Vtiger_Request();
    $return = $recordModel->getTyunServiceItem($request);
    return array($return);
}

/**
 * 验证该用户是否存在未签收的合同
 * @param $fieldname
 * @param $userid
 */
function checkTyunExistBuy($fieldname,$userid){
    global $adb;
    $tyun_account = $fieldname['tyun_account'];
    $query="SELECT 1 FROM vtiger_activationcode WHERE status IN(0,1) AND usercode=? AND contractstatus=0 AND orderstatus!='orderstop' AND LEFT(createdtime,10)!='".date('Y-m-d')."'";
    $listResult = $adb->pquery($query, array($tyun_account));
    return array($adb->num_rows($listResult));
}

function checkTyunIsPay($fieldname,$userid){
    global $adb;
    $tyun_account = $fieldname['tyun_account'];
    $query="SELECT 1 FROM vtiger_activationcode a left join vtiger_servicecontracts b on a.contractid=b.servicecontractsid WHERE a.status IN(0,1) AND a.usercode=? AND ((b.ispay = 0 AND a.signaturetype = 'eleccontract') or (a.signaturetype = 'papercontract' and a.createdtime>'2021-11-01 18:30:00' and b.ispay=0))  AND b.modulestatus !='c_stop'";
    $listResult = $adb->pquery($query, array($tyun_account));
    return array($adb->num_rows($listResult));
}

function getUserRole($fieldname,$userid){
    global $adb;
    $query="SELECT roleid FROM vtiger_user2role WHERE userid=?";
    $listResult = $adb->pquery($query, array($userid));
    return array($adb->raw_query_result_rowdata($listResult,0));
}

/**
 * 写日志，用于测试,可以开启关闭
 * @param data mixed
 */
function _logs($data, $file = 'logs_'){
	$year	= date("Y");
	$month	= date("m");
	$dir	= './Logs/' . $year . '/' . $month . '/';
	if(!is_dir($dir)) {
		mkdir($dir,0777,true);
	}
	$file = $dir . $file . date('Y-m-d').'.txt';
	@file_put_contents($file, '----------------' . date('H:i:s') . '--------------------'.PHP_EOL.var_export($data,true).PHP_EOL, FILE_APPEND);
}


/**
 * 电子合同审核邮件发送 niejunwei 2020-04-09
 * @param $fieldname
 * @param $userid
 * @return array
 * @throws Exception
 */
function sendElecContractVerifyEmail($fieldname, $userid) {
    global $adb,$current_user,$log;
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);

    include_once('modules/Vtiger/models/Record.php');
    $sql = "SELECT * FROM `vtiger_users` WHERE id=? LIMIT 1";
    $sel_result = $adb->pquery($sql, array($current_user->reports_to_id));
    $res_cnt = $adb->num_rows($sel_result);
    $agent_email = '';
    $agent_last_name = '';
    if($res_cnt > 0) {
        $row = $adb->query_result_rowdata($sel_result, 0);
        $agent_email = $row['email1'];
        $agent_last_name = $row['last_name'];
    }

    $email = $current_user->email1;
    $last_name = $current_user->last_name;
    $department = $current_user->department;

    $Subject = '电子合同需要审核';
    $nowTime = date("Y-m-d H:i", time());
    $body =  "员工：{$last_name}<br>部门：{$department}<br>客户：{$fieldname['companyname']}<br>客户手机：{$fieldname['custphone']}{$fieldname['buyContent']}<br><br>购买时间：{$nowTime}<br>";
    $address = array(
        array('mail'=>$email, 'name'=>$last_name),//销售
        array('mail'=>$agent_email, 'name'=>$agent_last_name)//销售上级
    );
    $log->info("电子合同购买审核通知邮件内容：".$body);
    $log->info("电子合同购买审核通知邮件地址：".$address);

    $tt = Vtiger_Record_Model::sendMail($Subject,$body,$address);
    return array($tt);
}

/**
 * 日报中客户统计信息
 *
 */
function salesDailyAccountStatistics($fieldname,$userid){
    global $current_user,$currentModule, $currentAction;
    $currentAction = 'BasicAjax';
    $currentModule = $fieldname['module'];
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
    include_once('includes/http/Request.php');
    //require_once('modules/Vtiger/actions/SaveAjax.php');
    include_once('modules/SalesDaily/actions/BasicAjax.php');
    $save_ajax=new SalesDaily_BasicAjax_Action();
    $mode=$fieldname['mode'];
    return $save_ajax->$mode(new Vtiger_Request($fieldname, $fieldname));
}

/**
 * 拜访单撤销
 * @param $fieldname
 * @return array
 */
function doVisitingOrderCancel($fieldname){
    $user = new Users();
    global $current_user;
    $current_user = $user->retrieveCurrentUserInfoFromFile($fieldname['userid']);
    include_once('includes/http/Request.php');
    include_once('modules/VisitingOrder/actions/ChangeAjax.php');
    $save = new VisitingOrder_ChangeAjax_Action();
    $fieldname['action']='ChangeAjax';
    $fieldname['module']='VisitingOrder';
    $fieldname['mode']='doSpecialCancel';
    $res  = $save->doSpecialCancel(new Vtiger_Request($fieldname, $fieldname));
    return array($res);
}
/* Begin the HTTP listener service and exit. */
if (!isset($HTTP_RAW_POST_DATA)){
	$HTTP_RAW_POST_DATA = file_get_contents('php://input');
}
$server->service($HTTP_RAW_POST_DATA);

exit();

?>
