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
	{* 注 CONTRACTCHANGES 中的newcontract_no名称对应新建时的newcontractsid ，newaccount_name对应 newaccountid *}
	{assign var=CONTRACTCHANGES value=array('smownerid','refillapplicationno','createdtime','smownerid','assigned_user_id','modifiedtime','workflowsid','workflowsnode','modulestatus','oldrechargesource','contractamountrecharged','actualtotalrecharge','grossadvances','totalreceivables','remarks','changecontracttype','changesnumber','newcustomertype','newaccount_name','newiscontracted','newservicesigndate','newcontractamount','newcontract_no')}
	{* 注 CONTRACTCHANGES_OLD 中的oldcontract_no名称对应新建时的servicecontractsid ，account_name 对应  accountid *}
	{assign var=CONTRACTCHANGES_OLD value=array('oldcontract_no','account_name','customertype','contractamount','iscontracted','servicesigndate')}
	{foreach key=BLOCK_LABEL_KEY item=FIELD_MODEL_LIST from=$RECORD_STRUCTURE}
		{if $BLOCK_LABEL_KEY eq 'VENDOR_LBL_INFO'}{continue}{/if}
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
						{if $BLOCK_LABEL_KEY eq 'LBL_CUSTOM_INFORMATION'}
							目标合同信息
						{else}
							&nbsp;&nbsp;{vtranslate({$BLOCK_LABEL_KEY},{$MODULE_NAME})}
						{/if}

					</th>
			</tr>
			</thead>
			 <tbody {if $IS_HIDDEN} class="hide" {/if}>
			{assign var=COUNTER value=0}
			<tr>
			{foreach item=FIELD_MODEL key=FIELD_NAME from=$FIELD_MODEL_LIST}
				{if !in_array($FIELD_MODEL->getFieldName(),$CONTRACTCHANGES)}{continue}{/if}
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
							 {if  $FIELD_MODEL->get('name') eq 'oldrechargesource' }
								 {vtranslate($FIELD_MODEL->get('fieldvalue'),'RefillApplication')}
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
		{if  $BLOCK_LABEL_KEY eq 'LBL_INFO'}
			{foreach key=BLOCK_LABEL_KEY item=FIELD_MODEL_LIST from=$RECORD_STRUCTURE}
				{if $BLOCK_LABEL_KEY neq 'LBL_INFO'}{continue}{/if}
				{if $BLOCK_LABEL_KEY eq 'VENDOR_LBL_INFO'}{continue}{/if}
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
							原合同信息

						</th>
					</tr>
					</thead>
					<tbody {if $IS_HIDDEN} class="hide" {/if}>
					{assign var=COUNTER value=0}
					<tr>
						{foreach item=FIELD_MODEL key=FIELD_NAME from=$FIELD_MODEL_LIST}
						{if !in_array($FIELD_MODEL->getFieldName(),$CONTRACTCHANGES_OLD)}{continue}{/if}
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
							{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME RECORD=$RECORD}{if $FIELD_MODEL->name eq 'probability'}%{/if}
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
				{if  $BLOCK_LABEL eq 'LBL_INFO'}

				{/if}
			{/foreach}
		{/if}
	{/foreach}
	<table class="table table-bordered blockContainer showInlineTable  detailview-table" id = "fallintotable">
		<thead>
		<tr>
			<th class="blockHeader" colspan="8" >
				充值单信息
			</th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td><b>申请单编号</b></td>
			<td><b>充值来源</b></td>
			<td><b>申请人</b></td>
			<td><b>申请时间</b></td>
			<td><b>合计垫款金额</b></td>
			<td><b>应收款总额</b></td>
			<td><b>应付款总额</b></td>
			{foreach from=$REFILLAPPLICATION_LIST item="item" key="key"}
		<tr>
			<td>{$item['refillapplicationno']}</td>
			<td>{vtranslate($item['rechargesource'],'RefillApplication')}</td>
			<td>{$item['smownerid']}</td>
			<td>{$item['createdtime']}</td>
			<td>{$item['grossadvances']}</td>
			<td>{$item['actualtotalrecharge']}</td>
			<td>{$item['totalreceivables']}</td>
		</tr>
		{/foreach}
		</tbody>
	</table>
{/strip}