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
$server->register(
	'com_search_list',
	array('searchModule'=>'xsd:string','searchValue'=>'xsd:string','relatedModule'=>'xsd:string','userid'=>'xsd:string'),
	array('return'=>'tns:field_details_array'),
	$NAMESPACE);

$server->register(
	'get_account_msg',
	array('id'=>'xsd:string'),
	array('return'=>'tns:field_details_array'),
	$NAMESPACE);

$server->register(
	'add_VisitingOrder',
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
	'getAccountsDetail',
	array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
	array('return'=>'tns:field_details_array'),
	$NAMESPACE);


$server->register(
	'getAccounts',
	array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
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
$server->register(
    'dosign',
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
    'checkWorkSummarize',
    array('userid'=>'xsd:string'),
    array('return'=>'xsd:string'),
    $NAMESPACE);
$server->register(
    'get_record_detail',
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

//获取我部门的人（陪同人）
$server->register(
    'getDepartmentsUserByUserId',
    array('userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);

//获取客户账号信息
$server->register(
    'search_refillapplication_accountzh',
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

/*回款*/
$server->register(
		'receiveReceivedPayments',
		array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
		array('return'=>'tns:field_details_array'),
		$NAMESPACE);

/*查找合同编号*/
$server->register(
		'findContractNo',
		array('fieldname'=>'tns:common_array'),
		array('return'=>'tns:field_details_array'),
		$NAMESPACE);

/*合同搜索*/
$server->register(
		'contractSearch',
		array('fieldname'=>'tns:common_array'),
		array('return'=>'tns:field_details_array'),
		$NAMESPACE);

/*工作流审核*/
$server->register(
    'salesorderWorkflowStagesExamine',
    array('fieldname'=>'tns:common_array','userid'=>'xsd:string'),
    array('return'=>'tns:field_details_array'),
    $NAMESPACE);
/*工作流打回*/
$server->register(
    'salesorderWorkflowStagesRepulse',
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
function login_from_weixin($email,$login='true'){
	    global $adb,$log;
		$sql  = "SELECT *  FROM `vtiger_users` WHERE status='Active' AND email1='$email'";
	    $query = $adb->pquery("SELECT *  FROM `vtiger_users` WHERE `status`='Active' AND email1='$email' limit 1");
		$norows = $adb->num_rows($query);
		$result = array();
		
		$err[0]['err1'] = "MORE_THAN_ONE_USER";
		$err[1]['err1'] = "INVALID_USERNAME_OR_PASSWORD";
		
		if($norows) {
			while($resultrow = $adb->fetchByAssoc($query)) {
				$result = $resultrow;
			}
		}else{
			return $err[1];
		}

	
		$list[0]['id'] 					= $result['id'];
		$list[0]['user_name'] 			= $result['user_name'];
		$list[0]['user_password'] 		= $result['user_password'];
		$list[0]['last_name'] 			= $result['last_name'];
			
		
		
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

function authenticate_user($username,$password,$version,$login = 'true')
{	
	$tt = $password;
	global $adb,$log;
	$adb->println("Inside customer portal function authenticate_user($username, $password, $login).");
	include('vtigerversion.php');
	if(version_compare($version,'5.1.0','>=') == 0){
		$list[0] = "NOT COMPATIBLE";
  		return $list;
	}

		$username 	= $adb->sql_escape_string($username);
		$password 	= $adb->sql_escape_string($password);
		$old_password = $password;
		$password 	= str_replace(md5(getip()),'',$password);
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
		}else{
			return $err[1];
		}
		
		if($login != 'false')
		{

			$sessionid = makeRandomPassword();
			$sql="insert into vtiger_soapservice values(?,?,?)";
			$result = $adb->pquery($sql, array($userid,'customer' ,$sessionid));
			$list[0]['sessionid'] = $sessionid;
		}
		
	return $list;
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

	

	$log->debug("Entering customer portal function get_account_name");
	$res = $adb->pquery("select * from vtiger_account where accountid=?", array($accountid));
	$accountname=$adb->query_result($res,0,'accountname');
    return array($accountname);
	$log->debug("Exiting customer portal function get_account_name");
   

	$user = new Users();
	$userid = getPortalUserid();
	$current_user = $user->retrieveCurrentUserInfoFromFile($userid);

	//return $user->getColumnNames_User();

	return array($accountname);
}


#搜索通用方法
function com_search_list($searchModule,$searchValue,$relatedModule,$userid){

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
	$records = $searchModuleModel->searchRecordAPP($searchValue, '', '', $relatedModule);

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
		$query = $adb->pquery('SELECT *  FROM `vtiger_account` WHERE accountid=? limit 1',array($id));
		$norows = $adb->num_rows($query);
		$result = array();
		if($norows) {
			while($resultrow = $adb->fetch_array($query)) {
				$result[] = $resultrow;
			}
		}
		return $result;
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

#获取通用列表数据
function get_com_list($module,$fieldname){
	global $current_user,$currentModule, $currentAction;
	$currentAction = 'DetailView';
	$currentModule = $module;
	$user = new Users();
	$current_user = $user->retrieveCurrentUserInfoFromFile($fieldname['userid']);
	
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

	include_once('modules/Vtiger/Models/ListView.php');
	include_once('modules/Vtiger/Models/Paging.php');
	$listViewModel 	= Vtiger_ListView_Model::getInstance($module, 0);
	$pagingModel 	= new Vtiger_Paging_Model();   	//分页
    $pagingModel->set('page', $pagenum);
    $pagingModel->set('limit', $pagecount);			//每页显示数量
    

	$entries 		= $listViewModel->getListViewEntries($pagingModel,$searchField);
	$counts 		= $listViewModel->getListViewCount($searchField); 
	$pageTotal = ceil($counts / $pagecount);
	return array('sum'=>$pageTotal, 'list'=>$entries);
}

#获取到款通知的列表
function get_my_receivepayment($fieldname, $userid){
    global $adb;


    $date=date('Y-m-d');
    
    $total = 0;
    $pageCount = 10;

    $sql = "SELECT count(*) AS count from  vtiger_receivedpayments LEFT JOIN vtiger_servicecontracts ON maybe_account = sc_related_to LEFT JOIN vtiger_crmentity ON maybe_account = crmid WHERE relatetoid = '' AND receiveid = ? AND maybe_account != '' AND receivedpaymentsid NOT IN ( SELECT DISTINCT receivepaymentid FROM `vtiger_ReceivedPayments_throw` WHERE userid = ? )";
    $res = $adb->pquery($sql, array($userid,$userid));
    $row = $adb->query_result_rowdata($res, 0);
    $total = $row['count'];
    $pageTotal = ceil($total / $pageCount);

    $pagenum = $fieldname['pagenum'];
    $start = ($pagenum - 1) * $pageCount;

    $sql = "SELECT label, reality_date, CONCAT( currencytype, ':', unit_price ) AS price FROM vtiger_receivedpayments LEFT JOIN vtiger_servicecontracts ON maybe_account = sc_related_to LEFT JOIN vtiger_crmentity ON maybe_account = crmid WHERE relatetoid = '' AND receiveid = ? AND maybe_account != '' AND receivedpaymentsid NOT IN ( SELECT DISTINCT receivepaymentid FROM `vtiger_ReceivedPayments_throw` WHERE userid = ? ) LIMIT $start, $pageCount";
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
function dosign($fieldname){
    global $adb,$log,$currentModule;
    $arr =  array_values($fieldname);
    $arr[] = $fieldname['userid'];

    $sql = 'UPDATE vtiger_visitingorder SET signid = ? , signaddress = ?,signaddcode = ?,signtime=?,issign=? WHERE visitingorderid=? AND extractid=?';
    $adb->pquery($sql, array($arr));


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
    $sql = "UPDATE vtiger_visitsign set coordinate=?, signtime=?, signaddress=?, issign=? WHERE visitingorderid=? AND userid=? AND signnum=?";
    $adb->pquery($sql, array($fieldname['adcode'], $fieldname['time'], $fieldname['adname'], '1',$fieldname['visid'], $fieldname['userid'], $signnum));
    
}

#获取本部门的人
function getDepartmentsUserByUserId($userid) {
	global $adb;
	// 1. 获取当前用户的部门
	$sql = "SELECT
					u.id,u.last_name
				FROM
					vtiger_users u WHERE  u.status='Active' 
				ORDER BY u.user_name";
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
#添加拜访单
function add_VisitingOrder($fieldname,$userid){
	global $adb,$log,$current_user,$currentModule;
	$currentModule = 'VisitingOrder';
	
	$user = new Users();
	$current_user = $user->retrieveCurrentUserInfoFromFile($userid);
	include_once('includes/http/Request.php');
	include_once('modules/Vtiger/actions/Save.php');
	$save = new Vtiger_Save_Action();
	
	$res  = $save->saveRecord(new Vtiger_Request($fieldname, $fieldname));

	$order_id = $res->getId();
	if($order_id){
		// 更新客户地址
		$sql = "update vtiger_visitingorder set customeraddress=? where visitingorderid=?";
		$adb->pquery($sql, array($fieldname['customeraddress'], $order_id));

		/*// 添加到 拜访单签单详情表
		$sql = "INSERT INTO `vtiger_visitsign` (`visitsignid`, `visitingorderid`, `userid`, `visitsigntype`, `signtime`, `signaddress`, `issign`) VALUES (null, ?, ?, ?, ?, ?, ?);";
		$adb->pquery($sql, array($order_id, $current_user->id, '提单人', '', '', '0'));*/
		for($i=1; $i<=2; $i++) {
			// 添加到 拜访单签单详情表
			$sql = "INSERT INTO `vtiger_visitsign` (`visitsignid`, `visitingorderid`, `userid`, `visitsigntype`, `signtime`, `signaddress`, `issign`, `signnum`, `coordinate`) VALUES (null, ?, ?, ?, ?, ?, ?, ?, ?);";
			$adb->pquery($sql, array($order_id, $current_user->id, '提单人', '', '', '0', $i, ''));
		}
		
		if ( ! empty($fieldname['accompany'])) {   //陪同人
			$accomptyArr = explode(' |##| ', $fieldname['accompany']);
			$t_arr = array();
			foreach ($accomptyArr as $key => $value) {
				$t_arr[] = "(null, '$order_id', '$value', '陪同人', '', '', '0', '1', '')";
				$t_arr[] = "(null, '$order_id', '$value', '陪同人', '', '', '0', '2', '')";
			}
			$sql = "INSERT INTO `vtiger_visitsign` (`visitsignid`, `visitingorderid`, `userid`, `visitsigntype`, `signtime`, `signaddress`, `issign`, `signnum`, `coordinate`) VALUES " . implode(',', $t_arr);
			$adb->pquery($sql, array());
		}

		#添加审批流程
		$time = date('Y-m-d H:i:s');
		$workflow = array(	  
						  'salesorderid'			=>$order_id,
						  'workflowsid'				=>400,
						  'modulename'				=> 'VisitingOrder',
						  'smcreatorid'				=>$userid,
						  'createdtime'				=>$time,
						  'ishigher'				=>1,
						  'addtime'					=>$time
		);
        $workflow['sequence'] = 1;
        $workflow['workflowstagesname'] = '提单人上级审批';
        $workflow['workflowstagesid'] = 470;
        $workflow['isaction'] = 1;
        $workflow['actiontime'] = $time;
        $workflow['higherid'] = $current_user->reports_to_id;
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
			
	}
	return array($order_id);
}



function getPortalUserid() {
	global $adb,$log;



	$log->debug("Entering customer portal function getPortalUserid");

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
	$log->debug("Exiting customerportal function getPortalUserid");
}


function unsetServerSessionId($id)
{
	global $adb,$log;
	$log->debug("Entering customer portal function unsetServerSessionId");
	$adb->println("Inside the function unsetServerSessionId");

	$id = (int) $id;
	Vtiger_Soap_CustomerPortal::updateSessionId($id, false);

	$adb->pquery("delete from vtiger_soapservice where type='customer' and id=?", array($id));
	$log->debug("Exiting customer portal function unsetServerSessionId");
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
	$adb->println("Inside function validateSession($id, $sessionid)");

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
    $model= 'VisitingOrder';
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
        $upload_status = rename($filetmp_name, $upload_file_path . $current_id . "_" . base64_encode($binFile));
        if($upload_status){
            $sql2 = "insert into vtiger_files(attachmentsid, name,description, type, path,uploader,uploadtime) values(?, ?,?, ?, ?,?,?)";
            $params2 = array($current_id, $filename, $model,$filetype, $upload_file_path,$uid,date('Y-m-d H:i:s'));
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
	$sql = "select relationid,islook from vtiger_approval where relationid IN (". implode(',', $ids) .")";
	
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
    if($arr['modulestatus']!='c_complete'){
        $arr['Workflows']=$recordModel->getWorkflowsMobile(new Vtiger_Request($fieldname, $fieldname));
    }
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
    if (! empty($arr['accompany'])) {
    	// 获取拜访单的签到信息
		$sql = "SELECT
					u.last_name,
					v.signaddress,
					v.visitsigntype,
					if(v.signnum=1, '一', '二') as signnum 
				FROM
					vtiger_visitsign v
				LEFT JOIN vtiger_users u ON v.userid = u.id
				WHERE
					v.visitingorderid = ?";
		$t_result = $adb->pquery($sql, array($fieldname['record']));
		while($rawData = $adb->fetch_array($t_result)) {
			$visitsingArr[] = $rawData;
		}
    }
    $arr['t_accompany'] = $visitsingArr;

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
	include_once('modules/Accounts/actions/Save.php');
	$save = new Accounts_Save_Action();
	$res  = $save->saveRecord(new Vtiger_Request($fieldname, $fieldname));
	$order_id = $res->getId();
	return array($order_id);
}
/*
	判断客户是否重复
*/
function check_accountname($fieldname, $userid) {
	global $adb;

	$sql = "select accountname from vtiger_account where accountname=? ";
	$query = $adb->pquery($sql, array($fieldname['accountname']));
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
/*
充值申请单详情
*/
function oneRefillApplication($fieldname, $userid) {
	global $adb,$current_user;
	$user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
	
    include_once('modules/Vtiger/Models/Vtiger_DetailView_Model.php');
	$refillApplicationModel = Vtiger_DetailView_Model::getInstance('RefillApplication', $fieldname['id']);
	$recordModel = $refillApplicationModel->getRecord();

	$sql = "SELECT vtiger_refillapplication.refillapplicationno,vtiger_refillapplication.servicecontractsid,
    (SELECT vtiger_users.last_name FROM vtiger_users WHERE vtiger_users.id = vtiger_crmentity.smcreatorid) as last_name,
    vtiger_refillapplication.accountid, ( SELECT vtiger_servicecontracts.contract_no FROM vtiger_servicecontracts WHERE vtiger_servicecontracts.servicecontractsid = vtiger_refillapplication.servicecontractsid ) AS contract_no,
    ( SELECT vtiger_account.accountname FROM vtiger_account WHERE vtiger_account.accountid = vtiger_refillapplication.accountid ) AS accountname,
    vtiger_refillapplication.remarks, IF(vtiger_rechargesheet.rechargetype='c_recharge','充值','退款') AS t_rechargetype, vtiger_rechargesheet.* FROM vtiger_refillapplication
    LEFT JOIN vtiger_rechargesheet ON vtiger_refillapplication.refillapplicationid = vtiger_rechargesheet.refillapplicationid
    LEFT JOIN vtiger_crmentity ON(vtiger_refillapplication.refillapplicationid=vtiger_crmentity.crmid)
    WHERE vtiger_refillapplication.refillapplicationid =? AND vtiger_rechargesheet.isentity = 1 LIMIT 1";
	$sel_result = $adb->pquery($sql, array($fieldname['id']) );
	$res_cnt = $adb->num_rows($sel_result);
	$row = array();
	if($res_cnt > 0) {
	    $row = $adb->query_result_rowdata($sel_result, 0);
	}
	
	/*获得垫款*/
	$accountid =  $row['accountid'];
	$result = $adb->pquery("SELECT advancesmoney  FROM vtiger_account WHERE accountid =? ",array($accountid));
	$advancesmoney = 0;
	if ($result && $adb->num_rows($result) > 0) {
		$rowData = $adb->fetch_array($result);
		$advancesmoney =  $rowData['advancesmoney'];
	}

	// 充值明细
	$sql = "SELECT vtiger_rechargesheet.*, IF ( rechargetype = 'c_recharge', '充值', '退款') AS t_rechargetype FROM vtiger_rechargesheet WHERE refillapplicationid =? AND isentity = 0";
	$listResult = $adb->pquery($sql, array($fieldname['id']));
	$res = array();
	while($rawData=$adb->fetch_array($listResult)) {
   		$res[] =  $rawData;
    }

    // 工作流
    include_once('includes/http/Request.php');
    $tt = $recordModel->getWorkflowsMobile(new Vtiger_Request($fieldname, $fieldname));

	return array('refillApplication'=>$row, 'rechargesheet'=>$res, 'workflows'=>$tt, 'advancesmoney'=>$advancesmoney);
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

/*查找合同编号*/
function findContractNo($fieldname){
	global $adb;


	$date=date('Y-m-d');

	$total = 0;
	$pageCount = 10;

	$sql = "SELECT servicecontracts_no FROM `vtiger_servicecontracts_print` WHERE servicecontractsprintid=? AND constractsstatus in ('c_receive', 'c_print')";
	$listResult = $adb->pquery($sql, array($fieldname['id']));
	$res = '';
	while($rawData=$adb->fetch_array($listResult)) {
		$res =  $rawData['servicecontracts_no'];
	}
	return array('ReceivedPayments'=>$res);

}

/*合同搜索*/
function contractSearch($fieldname){
	global $adb;


	$date=date('Y-m-d');

	$total = 0;
	$pageCount = 10;
	$contract_no = $fieldname['contract_no'];
	$sql = "SELECT servicecontractsprintid,servicecontracts_no FROM `vtiger_servicecontracts_print` WHERE servicecontracts_no LIKE ?";
	
	$listResult = $adb->pquery($sql, array("%$contract_no%")); 
	$res = array();
	while($rawData=$adb->fetch_array($listResult)) {
		$res[] =  $rawData;
	}
	return array('contractList'=>$res);

}



/*工作流审核
*/
function salesorderWorkflowStagesExamine($fieldname, $userid) {
	global $adb,$current_user;
	$user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
    //Vtiger_Response

    include_once('modules/SalesorderWorkflowStages/actions/SaveAjax.php');
    include_once('includes/http/Request.php');
    include_once('includes/http/Response.php');

    $saveAjax = new SalesorderWorkflowStages_SaveAjax_Action();
    $saveAjax->updateSalseorderWorkflowStages(new Vtiger_Request($fieldname, $fieldname));
    //$data = json_decode($res);
    return array('flag'=>1);
}

/*
	工作流打回
*/
function salesorderWorkflowStagesRepulse($fieldname, $userid) {
return $fieldname;
	global $adb,$current_user,$log;
	$log->debug("salesorderWorkflowStagesRepulse start");
	$log->debug("fieldname:".var_export($fieldname,true)."");
	$user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
    //Vtiger_Response
	$log->debug("current_user:".var_export($current_user,true)."");
    include_once('modules/SalesorderWorkflowStages/actions/SaveAjax.php');
    include_once('includes/http/Request.php');
    include_once('includes/http/Response.php');

    $saveAjax = new SalesorderWorkflowStages_SaveAjax_Action();
	$log->debug("saveAjax:".var_export($saveAjax,true)."");
	$log->debug("salesorderWorkflowStagesRepulse end");
    ob_start();
    $saveAjax->backall(new Vtiger_Request($fieldname, $fieldname));
    $data = ob_get_contents();
    ob_end_clean();
    //$data = json_decode($res);
    return array($data);
}


/*
	添加充值申请单
*/
function addRefillApplication($fieldname, $userid) {
	global $adb,$current_user;
	global $isallow;
		$isallow=array('SalesOrder','Invoice','Quotes', 'VisitingOrder','Vacate','OrderChargeback','RefillApplication',
		'ExtensionTrial', 'Suppcontractsextension','PurchaseInvoice');
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile($userid);
    	
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
    $save = new Vtiger_Save_Action();

    $res  = $save->saveRecord(new Vtiger_Request($fieldname['refillApplicationData'], $fieldname['refillApplicationData']));
    $recordid = $res->getId();
	
    // 添加明细
    $rechargesheetData = $fieldname['rechargesheetData'];
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
    }
    $accountid =  $fieldname['refillApplicationData']['accountid'];
    $result = $adb->pquery("SELECT advancesmoney  FROM vtiger_account WHERE accountid =? ",array($accountid));
    $advancesmoney = 0;
    if ($result && $adb->num_rows($result) > 0) {
    	$row = $adb->fetch_array($result);
    	$advancesmoney =  $row['advancesmoney'];
    }
    // 添加工作流
    //调用申请单的处理，此处注释掉 2017/03/07 gaocl
   /* include_once('data/CRMEntity.php');
    $on_focus = CRMEntity::getInstance('RefillApplication');
    $on_focus->makeWorkflows('RefillApplication', $workflowsid, $recordid, 'edit');*/
   
    return array($recordid, $advancesmoney);
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

function getOneSalesDaily($fieldname, $userid) {
	global $current_user,$currentAction,$adb;

	// 看了属于自己的批复 改变状态
	$sql = "UPDATE vtiger_approval,vtiger_salesdaily_basic SET islook=1
	WHERE vtiger_approval.relationid=vtiger_salesdaily_basic.salesdailybasicid
	AND vtiger_approval.relationid=?
	AND model='SalesDaily' AND vtiger_salesdaily_basic.smownerid=?";
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
	$sql = "SELECT description,relationid,createid,createtime,
(SELECT CONCAT(last_name,'[',IFNULL(
							(SELECT departmentname FROM vtiger_departments WHERE
									departmentid = (
										SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_salesdaily_basic.smownerid LIMIT 1
									)),''),']',(IF (`status` = 'Active','','[离职]'))) AS last_name FROM vtiger_users WHERE vtiger_salesdaily_basic.smownerid = vtiger_users.id) AS smownerid_last_name,
(SELECT CONCAT(last_name,'[',IFNULL(
							(SELECT departmentname FROM vtiger_departments WHERE
									departmentid = (
										SELECT departmentid FROM vtiger_user2department WHERE userid = vtiger_salesdaily_basic.smownerid LIMIT 1
									)),''),']',(IF (`status` = 'Active','','[离职]'))) AS last_name FROM vtiger_users WHERE vtiger_approval.createid = vtiger_users.id) AS create_last_name

FROM vtiger_approval
				 LEFT JOIN vtiger_salesdaily_basic ON vtiger_salesdaily_basic.salesdailybasicid=vtiger_approval.relationid
				 WHERE relationid=? AND delflag=0 AND 
(vtiger_approval.createid=?
OR vtiger_salesdaily_basic.smownerid=?)  ";

	$result = $adb->pquery($sql, array($fieldname['id'], $userid, $userid));
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
        "tommorrowcontent"			=>$fieldname[0]['tommorrowcontent']
    );
    include_once('includes/http/Request.php');
    include_once('modules/Vtiger/actions/Save.php');
    $save = new Vtiger_Save_Action();
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
                vtiger_account.leadsource,
                vtiger_visitingorder.contacts,
                if(vtiger_account.linkname=vtiger_visitingorder.contacts,vtiger_account.title,(SELECT vtiger_contactdetails.`title` FROM vtiger_contactdetails WHERE vtiger_account.accountid=vtiger_contactdetails.accountid AND vtiger_contactdetails.`name`=vtiger_visitingorder.contacts)) AS title,
                if(vtiger_account.linkname=vtiger_visitingorder.contacts,vtiger_account.mobile,(SELECT vtiger_contactdetails.`mobile` FROM vtiger_contactdetails WHERE vtiger_account.accountid=vtiger_contactdetails.accountid AND vtiger_contactdetails.`name`=vtiger_visitingorder.contacts)) AS mobile,
                vtiger_visitingorder.startdate
                FROM `vtiger_accountrankhistory`
                LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_accountrankhistory.accountid LEFT JOIN vtiger_crmentity ON vtiger_account.accountid=vtiger_crmentity.crmid LEFT JOIN vtiger_visitingorder ON vtiger_account.accountid=vtiger_visitingorder.related_to WHERE vtiger_accountrankhistory.newaccountrank='forp_notv' AND vtiger_visitingorder.modulestatus='c_complete' AND vtiger_crmentity.smownerid=? AND vtiger_crmentity.deleted=0 AND left(vtiger_accountrankhistory.createdtime,10)=? GROUP BY vtiger_accountrankhistory.accountid";
    $result=$adb->pquery($query,array($current_user->id,$datetime));

    while($rawData=$adb->fetch_array($result)){
        //$leadsource=vtranslate($rawData['leadsource']);
        $leadsource=empty($languageStrings[$rawData['leadsource']])?$rawData['leadsource']:$languageStrings[$rawData['leadsource']];
        $sql = 'INSERT INTO vtiger_salesdailyfournotv
                            (salesdailybasicid,accountid,accountsmownerid,visitingorderid,leadsource,linkname,mobile,title,accountname,startdatetime) VALUES(?,?,?,?,?,?,?,?,?,?)';
        $adb->pquery($sql, array($recordid,$rawData['accountid'],$rawData['smownerid'],$rawData['visitingorderid'],$leadsource,$rawData['contacts'],$rawData['mobile'],$rawData['title'],$rawData['accountname'],$rawData['startdate']));
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
//通知模块
function get_NewList($fieldname){
    $_REQUEST['filter'] = 'NewList';
    return get_com_list('Knowledge',$fieldname);
}

/* Begin the HTTP listener service and exit. */
if (!isset($HTTP_RAW_POST_DATA)){
	$HTTP_RAW_POST_DATA = file_get_contents('php://input');
}
$server->service($HTTP_RAW_POST_DATA);

exit();

?>
