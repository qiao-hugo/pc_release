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
						 {if $FIELD_MODEL->get('label')=='contractoverduebalance' OR $FIELD_MODEL->get('label')=='receivestatus'}
							<span style="color: red">{vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}</span>
						 {else}
							 {vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}
						 {/if}
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
						 {elseif $FIELD_MODEL->getName() =='receivestatus'}
							 <span style="color:red">{vtranslate($RECORD->get('receivestatus'),'AccountReceivable')}</span>
						{elseif $FIELD_MODEL->getName() =='contractoverduebalance'}
							 <span style="color:red">{$RECORD->get('contractoverduebalance')}</span>
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

	{foreach item=ITEM key=KEY from=$CONTRACTDATA}
		<table class="table table-bordered equalSplit detailview-table" style="margin-top: 20px;text-align: center;margin-bottom: 0px">
		<tr style="background-color: #f1f1f1;font-size:14px;color:#333;">
			<td colspan="4" style="font-weight: bold;color: black">客户合同{$ITEM['contract_no']}</td>
		</tr>
			<tr>
				<td class="fieldLabel medium" >
					<label class="muted pull-right marginRight10px">合同编号</label>
				</td>
				<td class="fieldValue medium">{$ITEM['contract_no']}</td>
				<td class="fieldLabel medium">
					<label class="muted pull-right marginRight10px">客户</label>
				</td>
				<td  class="fieldValue medium">{$ITEM['accountname']}</td>
			</tr>
			<tr>
				<td  class="fieldLabel medium" ><label class="muted pull-right marginRight10px">合同主体</label></td>
				<td class="fieldValue medium">{$ITEM['invoicecompany']}</td>
				<td   class="fieldLabel medium" ><label class="muted pull-right marginRight10px">业务类型</label></td>
				<td  class="fieldValue medium">{vtranslate($ITEM['bussinesstype'],'ContractExecution')}</td>
			</tr>
			<tr>
				<td  class="fieldLabel medium" ><label class="muted pull-right marginRight10px">产品类型</label></td>
				<td  class="fieldValue medium">{$ITEM['productname']}</td>
				<td   class="fieldLabel medium" ><label class="muted pull-right marginRight10px">签订人</label></td>
				<td   class="fieldValue medium">{$ITEM['signid']}</td>
			</tr>
			<tr>
				<td  class="fieldLabel medium" ><label class="muted pull-right marginRight10px">签订日期</label></td>
				<td  class="fieldValue medium">{$ITEM['signdate']}</td>
				<td   class="fieldLabel medium" ><label class="muted pull-right marginRight10px">合同额</label></td>
				<td   class="fieldValue medium">{$ITEM['total']}</td>
			</tr>
			<tr>
				<td  class="fieldLabel medium" ><label class="muted pull-right marginRight10px">合同开票总额</label></td>
				<td  class="fieldValue medium">{$ITEM['contractinvoiceamount']}</td>
				<td   class="fieldLabel medium" ><label class="muted pull-right marginRight10px">合同收款金额</label></td>
				<td   class="fieldValue medium">{$ITEM['contractpaidamount']}</td>
			</tr>
			<tr>
				<td  class="fieldLabel medium" "><label class="muted pull-right marginRight10px">框架合同</label></td>
				<td  class="fieldValue medium">{if $ITEM['frameworkcontract']=='yes'}是{else}否{/if}</td>
					<td   class="fieldLabel medium" ><label class="muted pull-right marginRight10px">合同状态</label></td>
				<td   class="fieldValue medium">{vtranslate($ITEM['modulestatus'],'ServiceContracts')}</td>
			</tr>
		</table>

		<table  class="table table-bordered blockContainer showInlineTable" style="text-align: center">
			<tr style="background-color: #f1f1f1;font-size:14px;color:#333;">
				<td colspan="9" style="font-weight: bold;color: black">合同应收明细（运营）</td>
			</tr>
			<tr>
				<th>收款阶段</th>
				<th>应收金额</th>
				<th>应收时间</th>
				<th style="width: 150px;">收款说明</th>
				<th>状态</th>
				<th>应收余额</th>
				<th>收款情况</th>
				<th>逾期天数</th>
				<th>凭证</th>
			</tr>
			{foreach from=$ITEM['executionDetailData'] key=KEY2 item=$CONTRACT_EXECUTION_DETAIL}
				<tr>
					<td>{$ITEM['executionDetailData'][$KEY2]['stageshow']}</td>
					<td>{$ITEM['executionDetailData'][$KEY2]['receiveableamount']}</td>
					<td>{$ITEM['executionDetailData'][$KEY2]['receiverabledate']}</td>
					<td>{$ITEM['executionDetailData'][$KEY2]['collectiondescription']}</td>
					<td>{vtranslate($ITEM['executionDetailData'][$KEY2]['executestatus'],'ContractExecution')}</td>
					<td>{$ITEM['executionDetailData'][$KEY2]['contractreceivable']}</td>
					<td {if $ITEM['executionDetailData'][$KEY2]['collection']=='overdue'}style="color: red" {/if}>{vtranslate($ITEM['executionDetailData'][$KEY2]['collection'],'ContractExecution')}</td>
					<td {if $ITEM['executionDetailData'][$KEY2]['collection']=='overdue'}style="color: red" {/if}>{$ITEM['executionDetailData'][$KEY2]['overduedays']}</td>
					<td><a href="{$ITEM['executionDetailData'][$KEY2]['voucherdownloadurl']}">{$ITEM['executionDetailData'][$KEY2]['voucher']}</a></td>
				</tr>
			{/foreach}
			<tr style="color: red">
				<td style="text-align: right">合计:</td>
				<td>{$ITEM['totalreceiveableamount']}</td>
				<td></td>
				<td></td>
				<td></td>
				<td>{$ITEM['totalcontractreceivablebalance']}</td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
		</table>
	{/foreach}
{/strip}