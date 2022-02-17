<?php
$apps_m_domain_url = "http://m.71360.com/";
$apps_m_crm_url = 'http://m.crm.71360.com';

//目录:/apps/login.php
$apps_login_access_url = $apps_m_crm_url."/access_token.php";
//目录:/apps/access_token.php
$apps_login_access_index_url = $apps_m_crm_url."/index.php";
//目录:/apps/otherlogin.php
$apps_other_login_access_url = $apps_m_crm_url.'/otheraccess_token.php';
//T云web端地址
//$tyunweburl='http://121.46.194.176/';//web端地址
$tyunweburl='http://pretyapi.71360.com/';//web端地址 测试pre
//$tyunClienturl='http://tyunapi.71360.com/';//客户端地址
$tyunClienturl='http://apityun.71360.com/';//客户端测试地址
//忽略校验合同领取人和客服负责人是否一致
$arr_ignore_check = array(1179,2824,7871,5921,199,15520);
//客服管理操作 (1179=>黄玉琴 1=>admin 1793=>高 2110=>柳 2824=李季,15520=>渠道郭楚乔) 1、显示购买时间 2、显示降级功能
$arr_cs_admin = array(1179,1,1793,2110,2824,199);
//客服角色
$arr_service_role = array('H83','H84','H85','H113','H117','H124','H129','H136','H140');
//特殊客服 1=>admin 1793=>高 2110=>柳
$arr_cs_special = array(1,1793,2110,199);

//中小部门
$zhongxiaodepartment = "H3";
//中小员工下单最高折扣
$zhongxiaominrate = 0.95;
//中小员工下单需审核的工作流ID
$eleContractWorkflowsid=2427241;

//放心签的相关信息
$fangxinqianurl = 'httpsss://zhenxinqian.71360.com/';  //放心签baseurl
$fangxinqiangettokenurl = $fangxinqianurl."common/token"; //获取放心签token
$fangxinqiangetformidurl = $fangxinqianurl."common/formId"; //获取放心签formId
$fangxinqianAppKey = "62af61e";
$fangxinqianAppSecrect = "ee6b5cb32ebb418aabfe361195ca6efb";

//电子合同第三方相关接口

$fangxinqian_get_templates = $fangxinqianurl."tyun/get_template"; //根据产品编号获取合同模板
$fangxinqian_get_enclosures = $fangxinqianurl."open/contract/get_enclosures"; // 根据产品编号获取附件(关联附件数据接口)
$fangxinqian_new_contract = $fangxinqianurl."tyun/save_and_replace"; // ERP新建合同(推送合同表单数据接口)
$fangxinqian_send = $fangxinqianurl."common/send"; // 发送电子合同接口
$fangxinqianview = $fangxinqianurl."common/view"; // 合同预览
$fangxinqianauditstatus = $fangxinqianurl."common/audit_status"; // 接收审核状态接口(审核通过时自动签署)
