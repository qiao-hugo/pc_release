<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * 简体中文语言包 - 商机线索
 * 版本: 6.0.0RC 
 * 作者: Maie | www.maie.name
 * 更新日期: 2013-12-10
 * All Rights Reserved.
 *************************************************************************************/
$languageStrings = array(
	// Basic Strings
	'Leads' => '商机',
	'SINGLE_Leads' => '商机线索',
	'LBL_RECORDS_LIST' => '商机列表',
	'LBL_ADD_RECORD' => '添加商机',

	// Blocks
	'LBL_LEAD_INFORMATION' => '商机信息',

	//Field Labels
	'Lead No' => '编号',
	'Company' => '线索客户名称',
	'Account Name'=>'转化后客户名称',
	'Designation' => '描述',
	'Website' => '网站',
	'Industry' => '行业',
	'Lead Status' => '状态',
	'No Of Employees' => '雇员数量',
	'Phone' => '常用电话',
	'Secondary Email' => '备用Email',
	'Email' => '常用Email',

	//Added for Existing Picklist Entries

	'--None--'=>'--无--',
	'Mr.'=>'先生',
	'Ms.'=>'小姐',
	'Mrs.'=>'女士',
	'Dr.'=>'博士',
	'Prof.'=>'教授',

	//Lead Status Picklist values
	'Attempted to Contact'=>'尝试联系',
	'Cold'=>'关系冷淡',
	'Contact in Future'=>'即将联系',
	'Contacted'=>'联系中',
	'Hot'=>'交往',
	'Junk Lead'=>'没有线索',
	'Lost Lead'=>'失去线索',
	'Not Contacted'=>'没有联系',
	'Pre Qualified'=>'预审合格',
	'Qualified'=>'合格',
	'Warm'=>'熟络',

	// Mass Action
	'LBL_CONVERT_LEAD' => '转换商机为客户',
    'Address'=>'公司地址',
    'AssignerStatus'=>'处理状态',
    'Assigner'=>'分配者',
    'a_not_allocated'=>'未分配',
    'c_allocated'=>'已分配',
    'c_transformation'=>'已转化',
    'c_cancelled'=>'已作废',
    'Last Name'=>'联系人',

	//Convert Lead
	'LBL_TRANSFER_RELATED_RECORD' => '相关记录转移到',
	'LBL_CONVERT_LEAD_ERROR' => '必须勾选公司或联系人才能转换',
	'LBL_CONVERT_LEAD_ERROR_TITLE' => '模块禁用',
	'CANNOT_CONVERT' => '不能转换',
	'LBL_FOLLOWING_ARE_POSSIBLE_REASONS' => '可能的原因包括：',
	'LBL_LEADS_FIELD_MAPPING_INCOMPLETE' => '商机字段映射不完整（设置 > 模块管理 > 商机 > 商机字段管理)',
	'LBL_MANDATORY_FIELDS_ARE_EMPTY' => '必填字段为空',
	'LBL_LEADS_FIELD_MAPPING' => '商机字段映射',

	//Leads Custom Field Mapping
	'LBL_CUSTOM_FIELD_MAPPING'=> '编辑字段映射',
	'LBL_WEBFORMS' => '安装web表单',
	'LBL_LEAD_SOURCE' => '商机来源',
    'ConversionTime'=>'转化时间',
    'AllocateTime'=>'分配时间',
    'Lead is already converted'=>'商机已转换为客户',
    'LeadsourceTnum'=>'来源号码',
    'LBL_cancelled_LEAD'=>'作废',
    'VoidReason'=>'作废原因',
    'c_Related'=>'已关联',
    'LBL_RELATED_LEAD'=>'强制关联',
    'c_Forced_Related'=>'强制关联'
);
$jsLanguageStrings = array(
	'JS_SELECT_CONTACTS' => '选择联系人继续',
	'JS_SELECT_ORGANIZATION' => '选择客户继续',
	'JS_SELECT_ORGANIZATION_OR_CONTACT_TO_CONVERT_LEAD' => '转换需要选择联系人或客户',
    'a_not_allocated'=>'未分配',
    'c_allocated'=>'已分配',
    'c_transformation'=>'已转化',
    'c_cancelled'=>'已作废',
    'c_Related'=>'已关联',
    'c_Forced_Related'=>'强制关联',
    'c_complete'=>'已成交','purproduct'=>'业务划分',
    'youshitong'=>'优视通',
    'zhihvtong'=>'智汇通',
    'wangzhanjiamhse'=>'网站建设',
    'SEOyouhua'=>'SEO优化',
    'bingtuiguang'=>'必应推广',
    'waimaotuiguang'=>'外贸推广',
    'sousuoynqingyingxiao'=>'搜索引擎营销',
    'DSPguanggao'=>'DSP',
    'tiefentong'=>'粉丝通',
    'xinlangfuyi'=>'新浪扶翼',
    'xinlanglongyuan'=>'新浪龙渊',
    'zhihuitui'=>'智汇推',
    'yandextuiguang'=>'yandex推广',
    'guangdiantong'=>'广点通',
    'zhengheyingxiao'=>'整合营销',
    'shuziyingxiao'=>'数字营销',
    'gugetuiguang'=>'谷歌推广',
    'yahutuiguang'=>'雅虎推广',
    'wnagluotuiguang'=>'网络推广',
    'baiduVrenzheng'=>'百度V认证',
    'Facebooltuiguang'=>'Facebook推广',
    'Apptuiguang'=>'App',
    'shemeiyingxiao'=>'社媒营销',
    'dailihezuo'=>'代理合作',
    'yuqingjiance'=>'舆情监测',
    'jingzhunyingxiao'=>'精准营销',
    'pinpaiyingxiao'=>'品牌营销',
    'zhengzhanyouhua'=>'整站优化',
    'souhuhuisuan'=>'搜狐汇算',
    '360tuiguang'=>'360推广 ',
    'sougoutuiguang'=>'搜狗推广',

    'leadsystem'=>'体系划分',
    'zhongxiaodiyicenter'=>'中小客户部一中心',
    'zhongxiaodiercenter'=>'中小客户部二中心',
    'zhongxiaodisancenter'=>'中小客户部三中心',
    'zhongxiaodisicenter'=>'中小客户部四中心',
    'zhongxiaowuxi'=>'中小客户部无锡公司',
    'zhongxiaosuzhou'=>'中小客户部苏州公司',
    'zhongxiaochengdu'=>'中小客户部成都公司',
    'zhongxiaoningbo'=>'中小客户部宁波公司',
    'zhongxiaohangzhou'=>'中小客户部杭州公司',
    'zhongxiaowenzhou'=>'中小客户部温州公司',
    'haiwaishiye'=>'海外事业部',
    'hdwaiwuxi'=>'海外事业部无锡公司',
    'haiwaishenzhen'=>'海外事业部深圳公司',
    'pinpaikehu'=>'品牌客户部',
    'pinpaikehubeijing'=>'品牌客户部北京公司',
    'baiduVrenzheng'=>'百度V认证',
    'jingzhunyingxiao'=>'精准营销事业部',
    'fabubao'=>'发布宝事业部',
    'KAkehu'=>'KA客户部',
    'KAshenzhen'=>'KA客户部深圳公司',
    'dianshang'=>'电商事业部',
    'jishufuwu'=>'技术服务部'
);