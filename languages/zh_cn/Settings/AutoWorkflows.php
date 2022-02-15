<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * 简体中文语言包 - 工作流设置
 * 版本: 6.0.0RC 
 * 作者: Maie | www.maie.name
 * 更新日期: 2013-12-13
 * All Rights Reserved.
 *************************************************************************************/
$languageStrings = array(
	//Basic Field Names
	'LBL_NEW' => '新建',
	'LBL_WORKFLOW' => '工作流',
	'LBL_CREATING_WORKFLOW' => '正在创建工作流',
	'LBL_EDITING_WORKFLOW' => '正在编辑工作流',
	'LBL_NEXT' => '下一步',
    'autoworkflowname'=>'工作流名称',
    'Module'=>'所属名称',
    'Execution Condition'=>'触发条件',
    'AutoWorkflows'=>'工作流',
    'LBL_ADD_RECORD'=>'新建工作流',

	//Edit view
	'LBL_STEP_1' => '第一步',
	'LBL_ENTER_BASIC_DETAILS_OF_THE_WORKFLOW' => '输入工作流的基本细节',
	'LBL_SPECIFY_WHEN_TO_EXECUTE' => '指定何时执行此工作流程',
	'ON_FIRST_SAVE' => '当记录第一次保存时',
	'ONCE' => '当条件第一次满足时',
	'ON_EVERY_SAVE' => '每次保存记录时',
	'ON_MODIFY' => '每次记录被修改时',
    
    'EDITwangbb'=>'每次编辑时',
    'CREATwangbb'=>'每次保存',
	'MANUAL' => '系统',
	'SCHEDULE_WORKFLOW' => '工作流调度计划',
	'ADD_CONDITIONS' => '添加条件',
	'ADD_TASKS' => '添加任务',
	
	//Step2 edit view
	'LBL_EXPRESSION' => '表达式',
	'LBL_FIELD_NAME' => '字段',
	'LBL_SET_VALUE' => '设置值',
	'LBL_USE_FIELD' => '使用字段',
	'LBL_USE_FUNCTION' => '使用功能',
	'LBL_RAW_TEXT' => '原始文本',
	'LBL_ENABLE_TO_CREATE_FILTERS' => '启用创建筛选器',
	'LBL_CREATED_IN_OLD_LOOK_CANNOT_BE_EDITED' => '此工作流基于旧的样式创建，不能编辑。您可以选择重新创建，或者使用现有的筛选条件。',
	'LBL_USE_EXISTING_CONDITIONS' => '使用现有的条件',
	'LBL_RECREATE_CONDITIONS' => '重新创建条件',
	'LBL_SAVE_AND_CONTINUE' => '保存 & 继续',
	
	//Step3 edit view
	'LBL_ACTIVE' => '激活',
	'LBL_TASK_TYPE' => '任务类型',
	'LBL_TASK_TITLE' => '任务标题',
	'LBL_ADD_TASKS_FOR_WORKFLOW' => '添加工作流任务',
	'LBL_TASK_TYPE' => '任务类型',
	'LBL_EXECUTE_TASK' => '执行任务',
	'LBL_SELECT_OPTIONS' => '选择选项',
	'LBL_ADD_FIELD' => '添加字段',
	'LBL_ADD_TIME' => '添加时间',
	'LBL_TITLE' => '标题',
	'LBL_PRIORITY' => '优先级',
	'LBL_ASSIGNED_TO' => '分配给',
	'LBL_TIME' => '时间',
	'LBL_DUE_DATE' => '截止日期',
	'LBL_THE_SAME_VALUE_IS_USED_FOR_START_DATE' => '开始日期也使用相同的值',
	'LBL_EVENT_NAME' => '事件名称',
	'LBL_TYPE' => '类型',
	'LBL_METHOD_NAME' => '方法名称',
	'LBL_RECEPIENTS' => '接收人',
	'LBL_ADD_FIELDS' => '添加字段',
	'LBL_SMS_TEXT' => 'Sms文本',
	'LBL_SET_FIELD_VALUES' => '设置字段值',
	'LBL_ADD_FIELD' => '添加字段',
	'LBL_IN_ACTIVE' => '无效',
	'LBL_SEND_NOTIFICATION' => '发送通知',
	'LBL_START_TIME' => '开始时间',
	'LBL_START_DATE' => '开始日期',
	'LBL_END_TIME' => '结束时间',
	'LBL_END_DATE' => '结束日期',
	'LBL_ENABLE_REPEAT' => '启用重复',
	'LBL_NO_METHOD_IS_AVAILABLE_FOR_THIS_MODULE' => '没有可用于该模块的方法',
	'LBL_FINISH' => '完成',
	'LBL_NO_TASKS_ADDED' => '没有任务',
	'LBL_CANNOT_DELETE_DEFAULT_WORKFLOW' => '你不能删除默认的工作流',
	'LBL_MODULES_TO_CREATE_RECORD' => '模块创建记录',
	'LBL_EXAMPLE_EXPRESSION' => '表达式',
	'LBL_EXAMPLE_RAWTEXT' => '原始文本',
	'LBL_VTIGER' => 'Vtiger',
	'LBL_EXAMPLE_FIELD_NAME' => '字段',
	'LBL_NOTIFY_OWNER' => '通知所有者',
	'LBL_ANNUAL_REVENUE' => '年度营收',
	'LBL_EXPRESSION_EXAMPLE2' => "if mailingcountry == 'India' then concat(firstname,' ',lastname) else concat(lastname,' ',firstname) end"

);

$jsLanguageStrings = array(
	'JS_STATUS_CHANGED_SUCCESSFULLY' => '状态更改成功',
	'JS_TASK_DELETED_SUCCESSFULLY' => '任务删除成功',
	'JS_SAME_FIELDS_SELECTED_MORE_THAN_ONCE' => '多次选择了相同的字段',
	'JS_WORKFLOW_SAVED_SUCCESSFULLY' => '工作流保存成功'
);