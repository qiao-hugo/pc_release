<?php
/*******************************************************
 * 全局变量定义规范
 * 1.变量名称不能使用没有任何意义的字符,多个单词组成的第一个单词首字符小写
 * 2.注释规范 //描述 by young 2015-11-20
 *******************************************************/
//发票签名保存的路径
$invoiceimagepath = '/storage/invoice/';

$t_yun_url = 'http://apityun.71360.com';
$t_yun_api_url = 'http://tyunapi.71360.com';
$t_yun_agent_url = 'http://tyunagent.71360.com';
$m_crm_url = 'http://mtest.crm.71360.com';
$serivce_crm_url = 'http://192.168.7.44:83';//客服营销系统
//新网关配置
$new_gateway_url='http://preoapi.71360.com';
$new_gateway_appId='sT8fqb7JVP1XvA5PL73bUqoyhuxhJOrl';
$new_gateway_appSecret='xQIIoG81zGXpZD2E32mdslZyGqaMeLA4LIQeX24qQuxGuME3CgfQY2Tj9VWoJ6F2';

//目录: service_contracts/models/record
//获取建站购买续费用户订单信息
$service_contracts_renew_cloud_site = $t_yun_url."/api/CRM/GetCloudSiteUser";
////T云合同签收通知
$service_contracts_tyun_contractconfirm_url = $t_yun_url."/api/CRM/ContractConfirm";
////建站购买地址
$service_contracts_tyun_sitecontract_url = $t_yun_url."/api/cms/CrmSiteContract";
////创建建站购买续费订单
$service_contracts_renew_cloud_site_user = $t_yun_url."/api/CRM/RenewCloudSiteUser";
//获取手机验证码
$service_contracts_send_mobile_code = $t_yun_url."/api/SMS/SendMobileCaptcha";
//$service_contracts_send_mobile_code = $t_yun_url."/api/SMS/SendMobileCode";
$service_contracts_get_mobile_code = $t_yun_url."/api/cms/GetMobileCode";
$service_contracts_search_doubles = array('common' => 396796, 'yellow_glod' => 396798, 'white_gold' => 396797);
$service_contracts_t_cloud_package = array(631612, 631769, 631761, 2115444, 426322, 426335, 426337, 426340, 565988, 787685,
    2113422, 603314, 474817, 783750, 830604, 2115445, 2116274, 2122361, 837, 781569, 781572, 781575, 2115457, 2115461,
    393333, 403863, 430156, 781577, 522819, 2115463, 781580, 781582, 783753, 2115459, 2115460, 2116276, 2122366,
    2123633, 2123636, 2123638, 2123496, 360689, 506127, 506129, 506141, 2115819, 2115477, 565678, 565692, 565694, 565696,
    565697, 565699, 565700, 565701, 570132, 584350, 2115470, 2115472, 2115467, 2115468, 2115476, 506131, 506134, 506135,
    2115478, 2115474, 2115479, 2115480, 2140134, 2140136, 2190148, 2190144, 2177131, 2192735, 2192729, 374, 2200833,
    2226507, 2200833, 2226485, 2226488, 2226492, 2226496, 2226499, 2226501, 2226503, 2226504,
    2226506, 2226507, 2278672, 2278612);
$service_contracts_keep_node = array(378331, 430156, 483000);
$service_contracts_select_workfows = 361027;
$service_contracts_check_paryments_node = 483000;
$service_contracts_arr_productids = array("2115445", "2115460", "2116274", "2116276", "2122361", "2122366", "2271588", "2271586","2317553","2317558");
$service_contracts_arr_productNames = array("2115445" => "云建站3.0微信小程序标准建站（首购）",
    "2115460" => "T云系列微信小程序标准建站（续费）",
    "2116274" => "云建站3.0PC标准建站 （首购）",
    "2116276" => "T云系列PC标准建站（续费）",
    "2122361" => "云建站3.0移动标准建站（首购）",
    "2122366" => "T云系列移动标准建站（续费）",
    "2271588" => "云建站3.0百度小程序标准建站（续费）",
    "2271586" => "云建站3.0百度小程序标准建站（首购）",
    "2317553"=>"T云建站独立IP（首购）",
    "2317558"=>"T云建站独立IP（续费）",
);
$service_contracts_check_tyun_product_tempproductid = array(830604, 783750, 783753, 783753, 2133564, 2133565, 2271588, 2271586,2317553,2317551,2317558,2115445,);
$service_contracts_check_tyun_product_and_year = array(426335, 426337, 565988, 426340, 426342, 566004, 474817, 426322, 837, 631769, 631612, 631761, 2115444, 787685, 2113422, 603314, 2140134);
$service_contracts_is_tyun_product = array(426335, 426337, 565988, 426340, 426342, 566004, 474817, 426322, 837, 631769, 631612, 631761, 2116274, 787685, 2113422, 603314, 2116274, 2122361, 2140134, 2115444);


//目录:Service_contracts/actions/change_ajax
$url_getactive_status = $t_yun_api_url."/api/cms/UserServerState";

//目录:SchoolRecruit/actions/basicAjax
$school_recruit_basic_ajax_qrcode_url = $m_crm_url.'/studentinput.php';

//目录:TelStatistics/actions/save
$tel_statistics_actions_save_url = $m_crm_url."/api.php";

//目录:ActivationCode/model/record
$activation_code_tyun_buy_url = $t_yun_url."/api/CRM/UpdateSecretkey";
$activation_code_tyun_upgrade_url = $t_yun_url."/api/CRM/UpdateSecretkeyUpgrade";
$activation_code_tyun_degrade_url = $t_yun_url."/api/CRM/UpdateSecretkeyDegrade";
$activation_code_tyun_renew_url = $t_yun_url."/api/CRM/UpdateSecretkeyRenewal";
$activation_code_tyun_againbuy_url = $t_yun_url."/api/CRM/UpdateSecretkeyBuyService";
$activation_code_tyun_serviceitem_url = $t_yun_url."/api/CRM/GetServiceItem";
$activation_code_tyun_upgrade_product_url = $t_yun_agent_url."/Base.xml";

//目录:/Users/view/qrlogin
$users_view_qrlogin_other_login_url = $m_crm_url.'/otherlogin.php';
//目录:/VisitingOrder/models/record
$visiting_order_weixin_contract_url = $m_crm_url."/api.php";

$m_crm_domain_index_url = $m_crm_url."/index.php";
$m_crm_domian_api_url = $m_crm_url."/api.php";
//用户管理添加审核工作流ID
$UserMangerWorkflowsid=2142553;
//总部角色
$UserMangerHeadPersonnelRole=array('H91','H148','H149','H93');
//分公司角色
$UserMangerPersonnelRole=array('H146','H147','H93');
//T云WEB版接口
/*$tyunweburl='http://121.46.193.163:8222/';*/
$tyunweburl='http://pretyapi.71360.com/';
//T云web端发短信接口
$tyunweburlsms='http://tyapi.71360.com/';
//$tyunweburlsms='http://121.46.193.163:8222/';
//客户端地址
$tyunClienturl='http://apityun.71360.com/';
//TWEB版接口sault
$sault='multiModuleProjectDirectoryasdafdgfdhggijfgfdsadfggiytudstlllkjkgff';
//TWEB版接口productid

$configcontracttypeName='T云WEB版';
$configcontracttypeNameYUN='T云院校版';
$configcontracttypeNameJT='T云集团版';
$configcontracttypeNameYQ='园区版';
$configcontracttypeNameTYUN=array('T云WEB版','T云院校版','T云集团版','园区版');

$kefu_url = 'http://192.168.44.156:9002';
$kefu_updatecontract_result = $kefu_url.'/api/updateContractProcessRssult';
$testtyunweburl='http://121.46.194.155/api/micro/order-basic/v1.0.0/';
//$orderpaymenturl=' http://121.46.194.176/api/micro/order-basic/v1.0.0/api/Order/PaymentCompletedOrder';
$orderpaymenturl='http://pretyapi.71360.com/api/app/tcloud-agent/v1.0.0/api/crmPayment';

//移动端电子合同审核工作流id
$createEleContractWorkflowsid=2427241;
//绑定电子合同CODE
$BindContractCode= $tyunweburl.'api/micro/order-basic/v1.0.0/api/Order/BindContractCode';
$GetProductPageData = $tyunweburl.'api/micro/order-basic/v1.0.0/api/Product/GetPageData';
$GetPackagePageData = $tyunweburl.'api/micro/order-basic/v1.0.0/api/Package/GetPageData';

//电子合同第三方相关接口

$fangxinqian_url = 'http://testzhenxinqian.71360.com/';

$fangxinqian_get_templates = $fangxinqian_url."tyun/get_template"; //根据产品编号获取合同模板
$fangxinqian_add_product = $fangxinqian_url."common/add_product"; //添加产品
$fangxinqian_appKey='62af61e';
$fangxinqian_appSecret='ee6b5cb32ebb418aabfe361195ca6efb';
$fangxinqianOcrKey='ad918e706c264ef3be700ab59708613a';
$fangxinqianOcrSecrect='154199b5e0dc4f4896dbf7770f03e87936ddeaa7d0c5491e902c0984e67255f9';
$fangxinqian_new_contract = $fangxinqian_url."tyun/save_and_replace"; // ERP新建合同(推送合同表单数据接口)
$fangxinqian_send = $fangxinqian_url."common/send";
//上海珍岛公司id
$SHZD_companyid = 3;
$SHZDWL_companyid = 15;
// Tyun 验证弱密码
$curlTyunWeakPasswordCheck='http://pretyapi.71360.com/api/app/aggregateservice-api/v1.0.0/api/Login/CheckPassword';
$rabbitmqConfig=array('config'=>array(
    'host' => '127.0.0.1',
    'port' => '5672',
    'login' => 'crmmquse',
    'password' => 'Awhj82SGjissGr',
    'vhost'=>'/'),
    'exchangeName'=>'e_xchange',
    'queryName'=>'e_query',
    'routeName'=>'e_route',
    'deadExchangeName'=>'delay_exchange',
    'deadRouteName'=>'delay_route',
    'deadQueryName'=>'delay_query',
);
//腾讯地图key
$mapKey = "YQSBZ-DN7WP-NWGDE-L7OWN-4ZYU2-GCBJU";

$zhongxiaojingli = 'H80';
$zhongxiaozongjian = 'H79';
$channelDepartmentId = 'H39';
//中小部门
$zhongxiaodepartment = "H3";
$kaililongdepartment = "H283";

$zhongxiaoshangwuroleid='H82';
$zhongxiaoshangwuzhuguanroleid='H81';
$zhongxiaoshangwujingliroleid='H80';
$isDev=1;
$FANGXINQIANURL='https://api.fangxinqian.cn/';

$limitDate="2021-09-01 23:59:59";

$weikeurl='https://preweike.71360.com/';
$LEADDEFAULTHOLDER=14;
$marketingShareUserId=14;
?>
