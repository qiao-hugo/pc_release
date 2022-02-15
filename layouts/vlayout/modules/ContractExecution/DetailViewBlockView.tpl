{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*<!--去除双击编辑-->
 ********************************************************************************/
-->*}
{strip}
	<style>
		.add-execution{
			padding: 10px;
			height: 40px;
		}

		.add-execution-tip{
			float:left;
			width: 10%;
			text-align: right;
		}

		.add-execution-node{
			float: left;
			width:12%;
			text-align: right;
		}
		.add-execution-info{
			float:left;
			width: 70%;
			margin-left: 10px;
		}
	</style>
	{foreach key=BLOCK_LABEL_KEY item=FIELD_MODEL_LIST from=$RECORD_STRUCTURE}
	{assign var=BLOCK value=$BLOCK_LIST[$BLOCK_LABEL_KEY]}
	{if $BLOCK eq null or $FIELD_MODEL_LIST|@count lte 0}{continue}{/if}
	{assign var=IS_HIDDEN value=$BLOCK->isHidden()}
	{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
	<input type=hidden name="timeFormatOptions" data-value='{$DAY_STARTS}' />
	<table class="table table-bordered equalSplit detailview-table">
		<thead>
		<tr>
				<th class="blockHeader" colspan="4">
						<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id={$BLOCK_LIST[$BLOCK_LABEL_KEY]->get('id')}>
						<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id={$BLOCK_LIST[$BLOCK_LABEL_KEY]->get('id')}>
						&nbsp;&nbsp;{vtranslate({$BLOCK_LABEL_KEY},{$MODULE_NAME})}
				</th>
		</tr>
		</thead>
		 <tbody {if $IS_HIDDEN} class="hide" {/if}>
		{assign var=COUNTER value=0}
		<tr>
		{foreach item=FIELD_MODEL key=FIELD_NAME from=$FIELD_MODEL_LIST}
			{if !$FIELD_MODEL->isViewableInDetailView()}
				 {continue}
			 {/if}
			 {if $FIELD_MODEL->get('uitype') eq "83"}
				{foreach item=tax key=count from=$TAXCLASS_DETAILS}
				{if $tax.check_value eq 1}
					{if $COUNTER eq 2}
						</tr><tr>
						{assign var="COUNTER" value=1}
					{else}
						{assign var="COUNTER" value=$COUNTER+1}
					{/if}
					<td class="fieldLabel {$WIDTHTYPE}">
					<label class='muted pull-right marginRight10px'>{vtranslate($tax.taxlabel, $MODULE)}(%)</label>
					</td>
					 <td class="fieldValue {$WIDTHTYPE}">
						 <span class="value">
							 {$tax.percentage}
						 </span>
					 </td>
				{/if}
				{/foreach}
			{else if $FIELD_MODEL->get('uitype') eq "69" || $FIELD_MODEL->get('uitype') eq "105"}
				{if $COUNTER neq 0}
					{if $COUNTER eq 2}
						</tr><tr>
						{assign var=COUNTER value=0}
					{/if}
				{/if}
				<td class="fieldLabel {$WIDTHTYPE}"><label class="muted pull-right marginRight10px">{vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}</label></td>
				<td class="fieldValue {$WIDTHTYPE}">
					<div id="imageContainer" width="300" height="200">
						{foreach key=ITER item=IMAGE_INFO from=$IMAGE_DETAILS}
							{if !empty($IMAGE_INFO.path) && !empty({$IMAGE_INFO.orgname})}
								<img src="{$IMAGE_INFO.path}_{$IMAGE_INFO.orgname}" width="300" height="200">
							{/if}
						{/foreach}
					</div>
				</td>
				{assign var=COUNTER value=$COUNTER+1}
			{else}
				{if $FIELD_MODEL->get('uitype') eq "20" or $FIELD_MODEL->get('uitype') eq "19"}
					{if $COUNTER eq '1'}
						<td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td></tr><tr>
						{assign var=COUNTER value=0}
					{/if}
				{/if}
				 {if $COUNTER eq 2}
					 </tr><tr>
					{assign var=COUNTER value=1}
				{else}
					{assign var=COUNTER value=$COUNTER+1}
				 {/if}
				 <td class="fieldLabel {$WIDTHTYPE}" id="{$MODULE}_detailView_fieldLabel_{$FIELD_MODEL->getName()}">
					 <label class="muted pull-right marginRight10px">
						 {vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}
						 {if ($FIELD_MODEL->get('uitype') eq '72') && ($FIELD_MODEL->getName() eq 'unit_price')}
							{$BASE_CURRENCY_SYMBOL}
						{/if}
					 </label>
				 </td>
				 <td class="fieldValue {$WIDTHTYPE}" id="{$MODULE}_detailView_fieldValue_{$FIELD_MODEL->getName()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
					 <span class="value" data-field-type="{$FIELD_MODEL->getFieldDataType()}">
						 {if $FIELD_MODEL->getName() == 'createdid'}
						 	{getUserName($RECORD->get('createdid'))}
						 {elseif $FIELD_MODEL->getName() == 'staff_stage'}
							 {IndicatorSetting_Module_Model::$staff_stages[$RECORD->get('staff_stage')]}
						 {elseif $FIELD_MODEL->getName() == 'departmentid'}
							 {assign var=DEPARTMENTNAME value=getDepartmentName()}
							 {$DEPARTMENTNAME[$RECORD->get('departmentid')]}
						 {elseif $FIELD_MODEL->getName() =='relationship_or'}
							 {IndicatorSetting_Module_Model::vtranslateRelation($RECORD->get($FIELD_MODEL->getName()),'IndicatorSetting')}
						 {elseif $FIELD_MODEL->getName() =='status'}
							 {vtranslate($RECORD->get('status'),$MODULE)}
						 {else}
	                        {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME RECORD=$RECORD}{if $FIELD_MODEL->name eq 'probability'}%{/if}
						 {/if}
					 </span>
				 </td>
			 {/if}

		{if $FIELD_MODEL_LIST|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype" and $FIELD_MODEL->get('uitype') neq "69" and $FIELD_MODEL->get('uitype') neq "105"}
			<td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
		{/if}
		{/foreach}
		</tr>
		</tbody>
	</table>
	{/foreach}


	<table  class="table table-bordered blockContainer showInlineTable" style="margin-top: 20px;text-align: center">
		<tr style="background-color: #f1f1f1;font-size:14px;color:#333;">
			<td colspan="9" style="font-weight: bold;color: black">合同阶段拆分</td>
		</tr>
		<tr>
			<th>收款阶段</th>
			<th>应收金额</th>
			<th style="width: 150px;">收款说明</th>
			<th>应收时间</th>
			<th>执行人</th>
			<th>执行时间</th>
			<th>状态</th>
			<th>阶段类型</th>
			<th>凭证</th>
		</tr>
		{foreach from=$CONTRACT_EXECUTION_DETAILS item=CONTRACT_EXECUTION_DETAIL}
			<tr>
				<td>{$CONTRACT_EXECUTION_DETAIL['stageshow']}</td>
				<td>{$CONTRACT_EXECUTION_DETAIL['receiveableamount']}</td>
				<td>{$CONTRACT_EXECUTION_DETAIL['collectiondescription']}</td>
				<td>{$CONTRACT_EXECUTION_DETAIL['receiverabledate']}</td>
				<td>{$CONTRACT_EXECUTION_DETAIL['last_name']}</td>
				<td>{$CONTRACT_EXECUTION_DETAIL['executedate']}</td>
				<td>{$CONTRACT_EXECUTION_DETAIL['executestatus']}</td>
				<td>{$CONTRACT_EXECUTION_DETAIL['stagetype']}</td>
				<td><a href="{$CONTRACT_EXECUTION_DETAIL['voucherdownloadurl']}">{$CONTRACT_EXECUTION_DETAIL['voucher']}</a></td>
			</tr>
		{/foreach}
	</table>
{/strip}