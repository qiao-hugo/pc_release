<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the 
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
********************************************************************************/

// 关闭所有运行错误
//error_reporting(E_WARNING & ~E_NOTICE & ~E_DEPRECATED); // PRODUCTION

#header("Location: http://222.73.140.168");

ini_set('display_errors','off');
// ini_set('display_errors','on'); error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);   // DEBUGGING

if($_SESSION['uid']==1){
	//ini_set('display_errors','on');
}
//版本号 检测系统函数支持和更新css js
include('vtigerversion.php');


// 脚本运行最大内存
//ini_set('memory_limit','64M');
// 日历时钟计算器聊天室功能
$CALENDAR_DISPLAY = 'true';
$WORLD_CLOCK_DISPLAY = 'true';
$CALCULATOR_DISPLAY = 'true';
$CHAT_DISPLAY = 'true'; 
$USE_RTE = 'true';

// 外部访问接口
$PORTAL_URL = 'http://vtiger.com/customerportal';

//系统支持信息
$HELPDESK_SUPPORT_EMAIL_ID = 'young.yang@trueland.net';
$HELPDESK_SUPPORT_NAME = 'your-support name';
$HELPDESK_SUPPORT_EMAIL_REPLY_ID = $HELPDESK_SUPPORT_EMAIL_ID;

/*数据库连接配置 */
if(isset($_REQUEST['testbak'])&& $_REQUEST['testbak']==11){
	//拉线上数据
	$dbconfig['db_server'] = '127.0.0.1';
	$dbconfig['db_port'] = ':3306';
	$dbconfig['db_username'] = 'yufabu_crmuser';
	$dbconfig['db_password'] = 'Ag2529gfhosgWtse3';
	$dbconfig['db_name'] = 'test';
	$dbconfig['db_type'] = 'mysql';
	$dbconfig['db_status'] = 'true';
}else{
	// 本地数据库
//	$dbconfig['db_server'] = '127.0.0.1';
//	$dbconfig['db_port'] = ':3306';
//	$dbconfig['db_username'] = 'root';
//	$dbconfig['db_password'] = '123456';
//	$dbconfig['db_name'] = 'vtigercrm600new';
//	$dbconfig['db_type'] = 'mysql';
//	$dbconfig['db_status'] = 'true';

	// 测试数据库
	$dbconfig['db_server'] = '192.168.7.147';
	$dbconfig['db_port'] = ':3306';
	$dbconfig['db_username'] = 'vtigercrm600new';
	$dbconfig['db_password'] = 'vtigercrm600new';
	$dbconfig['db_name'] = 'vtigercrm600new';
	$dbconfig['db_type'] = 'mysql';
	$dbconfig['db_status'] = 'true';
}

// TODO: test if port is empty
// TODO: set db_hostname dependending on db_type
$dbconfig['db_hostname'] = $dbconfig['db_server'].$dbconfig['db_port'];
// sql日志
$dbconfig['log_sql'] = false;

// 持久化
$dbconfigoption['persistent'] = true;
// 自动释放
$dbconfigoption['autofree'] = false;
// debug
$dbconfigoption['debug'] = 0;

//默认关联表命名
$dbconfigoption['seqname_format'] = '%s_seq';
// 兼容？
$dbconfigoption['portability'] = 0;
// 默认不开启ssl
$dbconfigoption['ssl'] = false;
$host_name = $dbconfig['db_hostname'];

//变更服务器需要修改
//系统访问地址
$site_URL = 'http://www.pcRelease.io/';

//$sso_URL='http://192.168.44.157:8080/';
$sso_URL = 'http://prein-sso.71360.com/';

// root directory path
//$root_directory = 'E:\vtigercrm600\apache\htdocs\vtigerCRM/';
$root_directory = 'D:\phpstudy_pro\WWW\pc_release/';

//缓存目录
$cache_dir = 'cache/';
// tmp_dir default value prepended by cache_dir = images/
$tmp_dir = 'cache/images/';
// import_dir default value prepended by cache_dir = import/
$import_dir = 'cache/import/';
// upload_dir default value prepended by cache_dir = upload/
$upload_dir = 'cache/upload/';

// 上传文件大小限制
$upload_maxsize = 3000000;

//是否允许使用导出'all' 'admin'  'none' 
$allow_exports = 'all';

//上传文件后缀检查，禁止的文件会加上.txt后缀 Edit by Joe @20150518
$upload_badext = array('php', 'php3', 'php4', 'php5', 'pl', 'cgi', 'py', 'asp', 'cfm', 'js', 'vbs', 'html', 'htm', 'exe', 'bin', 'bat', 'sh', 'dll', 'phps', 'phtml', 'xhtml', 'rb', 'msi', 'jsp', 'shtml', 'sth', 'shtm');

//完整的加载路径
$includeDirectory = $root_directory.'include/';

//分页数据条数
$list_max_entries_per_page = '20';

//导航默认翻页？没有用吧 Edit by Joe @20150518
$limitpage_navigation = '5';
//最大历史显示 后台配置已关闭 
$history_max_viewed = '5';

//默认的模块和操作
$default_module = 'Home';
$default_action = 'index';

//默认使用的主题样式 部分有修改 切换需谨慎 Edit by Joe @20150518
$default_theme = 'softed';

//显示页面执行耗时
$calculate_response_time = true;

// 登录页面默认账户？好像不用了 囧 // 登录页面默认密码？好像不用了 too  Edit by Joe @20150518
$default_user_name = '';
$default_password = '';

//是否创建默认用户 never! //并设置为管理员
$create_default_user = false;
$default_user_is_admin = false;

//默认允许开启持久化数据库连接
$disable_persistent_connections = false;

//首要货币名称
$currency_name = 'China, Yuan Renminbi';

//编码和语言
$default_charset = 'UTF-8';
$default_language = 'en_us';

//默认不增加语言类型前缀
$translation_string_prefix = false;

//默认开启权限缓存来加速
$cache_tab_perms = true;

//隐藏首页空数据的区块？ never
$display_empty_home_blocks = false;

//代码跟踪
$disable_stats_tracking = false;

// 应用app id
$application_unique_key = '1886c36eb5559f69909b62929bc0644e';

// 列表的描述 标题等字段截取长度
$listview_max_textlength = 40;

//后台配置数据或生成报表时需要关闭脚本运行超时限制 By set_time_limit Edit by Joe @20150518
$php_max_execution_time = 0;

$default_timezone = 'Asia/Shanghai';
/** 如果需要配置系统默认时区 无需检测函数 function_exists('date_default_timezone_set') Edit by Joe @20150518 */
if(isset($default_timezone)){
	date_default_timezone_set($default_timezone);
}
?>
