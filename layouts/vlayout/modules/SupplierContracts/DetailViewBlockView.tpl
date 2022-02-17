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
	{if $BLOCK_LABEL_KEY eq 'LBL_EXTRAPRODUCT' or $BLOCK_LABEL_KEY eq 'LBL_CUSTOM_INFORMATION'}{continue}{/if}
	{assign var=IS_HIDDEN value=$BLOCK->isHidden()}
	{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
	<input type=hidden name="timeFormatOptions" data-value='{$DAY_STARTS}' />
	<table class="table table-bordered equalSplit detailview-table {if ($BLOCK_LABEL_KEY=='LBL_COMPARE_INFO' and !$SHOWCOMPAREFILE)}hide{/if}">
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
			{if !$FIELD_MODEL->isViewableInDetailView() or $FIELD_MODEL->getName() eq 'workflowsid' or $FIELD_MODEL->getName() eq 'banklist'}
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
							({$BASE_CURRENCY_SYMBOL})
						{/if}
					 </label>
				 </td>
				 <td class="fieldValue {$WIDTHTYPE}" id="{$MODULE}_detailView_fieldValue_{$FIELD_MODEL->getName()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
					 <span class="value" data-field-type="{$FIELD_MODEL->getFieldDataType()}">
{if $FIELD_MODEL->getName() neq 'isconfirm' or ($FIELD_MODEL->getName() eq 'isconfirm' and $FIELD_MODEL->get('fieldvalue') eq 0)}
                        {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME RECORD=$RECORD}
{else}
                             {assign var=CONFIRM value=explode('##',$RECORD->entity->column_fields['confirmvalue'])}
                             {assign var=TEMP value=[]}
                             {foreach item=TEMPV from=$CONFIRM}
                                 {assign var=TEMPVE value=explode(',',$TEMPV)}
                                {$TEMP[]='<span style="width:100px;display:inline-block;overflow:hidden;"><i class="icon-user"></i>'|CAT:$TEMPVE[0]:'</span><i class="icon-time"></i>':$TEMPVE[1]}
                             {/foreach}
                             <i class="icon-th-list alignMiddle" title="审查详情" data-container="body" data-toggle="popover" data-placement="right" data-content='{implode('<br>',$TEMP)}'></i>
                         {/if}
					 </span>
                     {assign var=FIELDAJAX value=array('effectivetime','remark','paymentclause')}
					  {if $FIELD_MODEL->isEditable() eq 'true' && ($FIELD_MODEL->getFieldDataType()!=Vtiger_Field_Model::REFERENCE_TYPE) && $FIELD_MODEL->isAjaxEditable() eq 'true' && in_array($FIELD_MODEL->getName(),$FIELDAJAX)}
						 <span class="hide edit">
							 {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME}
                             {if $FIELD_MODEL->getFieldDataType() eq 'multipicklist'}
                                <input type="hidden" class="fieldname" value='{$FIELD_MODEL->get('name')}[]' data-prev-value='{$FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue'))}' />
                             {else}
                                 <input type="hidden" class="fieldname" value='{$FIELD_MODEL->get('name')}' data-prev-value='{Vtiger_Util_Helper::toSafeHTML($FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue')))}' />
                             {/if}
						 </span>
					 {/if}

				 </td>
			 {/if}

		{if $FIELD_MODEL_LIST|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype" and $FIELD_MODEL->get('uitype') neq "69" and $FIELD_MODEL->get('uitype') neq "105"}
			<td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
		{/if}
		{/foreach}
		</tr>
		{if $BLOCK_LABEL_KEY=='LBL_CATE_INFO'}
			<tr>
				<td class="fieldLabel medium" id="SupplierContracts_detailView_fieldLabel_payapplyid">
					<label class="muted pull-right marginRight10px">支出申请名称</label>
				</td>
				<td class="fieldValue" colspan="3" id="SupplierContracts_detailView_fieldValue_payapplyid">
					<span  class="value" data-field-type="string">
						{foreach item=PAYAPPLY key=PAYAPPLYKEY from=$PAYAPPLYLIST}
							<div>支出申请单单号-{$PAYAPPLY['payapply_no']} 申请名称-{$PAYAPPLY['payapply_name']} 申请人-{$PAYAPPLY['last_name']} 申请主体-{$PAYAPPLY['invoicecompany']} </div>
						{/foreach}
					</span>
				</td>
			</tr>
		{/if}
		</tbody>
	</table>
	<br>
	{/foreach}
    {foreach from=$VENDORSREBATEDATA item=value key=INDEX}
		<table class="table table-bordered equalSplit detailview-table">
			<thead>
			<tr>
				<th class="blockHeader" colspan="4">
					<img class="cursorPointer alignMiddle blockToggle hide " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="44"><img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="44">&nbsp;&nbsp;产品返点[{$INDEX + 1}]
				</th>
			</tr>
			</thead>
			<tbody>
			<tr>
				<td class="fieldLabel medium" id="Vendors_detailView_fieldLabel_depositbank">
					<label class="muted pull-right marginRight10px">产品</label>
				</td>
				<td class="fieldValue medium" id="Vendors_detailView_fieldValue_depositbank">
				<span class="value" data-field-type="string">
					{$value['productname']}
				</span>
				</td>
				<td class="fieldLabel medium" id="Vendors_detailView_fieldLabel_accountnumber">
					<label class="muted pull-right marginRight10px">返点比例</label>
				</td>
				<td class="fieldValue medium" id="Vendors_detailView_fieldValue_accountnumber">
				<span class="value" data-field-type="string">
					{$value['rebate']}
				</span>
				</td>
			</tr>
			<tr>
				<td class="fieldLabel medium" id="Vendors_detailView_fieldLabel_taxpayers_no">
					<label class="muted pull-right marginRight10px">生效时间</label>
				</td>
				<td class="fieldValue medium" id="Vendors_detailView_fieldValue_taxpayers_no">
				<span class="value" data-field-type="string">
					{$value['effectdate']}
				</span>
				</td>
				<td class="fieldLabel medium" id="Vendors_detailView_fieldLabel_registeraddress">
					<label class="muted pull-right marginRight10px">失效时间</label>
				</td>
				<td class="fieldValue medium" id="Vendors_detailView_fieldValue_registeraddress">
				<span class="value" data-field-type="string">
					{$value['enddate']}
				</span>
				</td>
			</tr>
			<tr>
				<td class="fieldLabel medium">
					<label class="muted pull-right marginRight10px"><span class="redColor"></span> 返点类型</label>
				</td>
				<td class="fieldValue medium" colspan="3">
					<div class="row-fluid">
						<span class="span10">{vtranslate($value['rebatetype'], $MODULE)}</span>
					</div>
				</td>
			</tr>
			<tr>
				<td class="fieldLabel medium">
					<label class="muted pull-right marginRight10px"><span class="redColor"></span> 返点说明</label>
				</td>
				<td class="fieldValue medium" colspan="3">
					<div class="row-fluid">
						<span class="span10">{$value['vexplain']}</span>
					</div>
				</td>
			</tr>
			</tbody>
		</table>

    {/foreach}
<br/>
	{if !empty($RSTATEMENT) || $ADDRSTATEMENT}
	<table class="table table-bordered detailview-table">
		<thead>
		<tr>
			<th class="blockHeader" colspan="4">
				<img class="cursorPointer alignMiddle blockToggle hide " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="44"><img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="44">&nbsp;&nbsp;付款明细{if $ADDRSTATEMENT}<b class="pull-right"><button class="btn btn-small" type="button" id="ADDRSTATEMENT">添加付款记录</button></b>{/if}
			</th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td class="fieldLabel medium">
				<label class="muted pull-left marginRight10px">付款单流水号</label>
			</td>
			<td class="fieldLabel medium" >
				<label class="muted pull-left marginRight10px">付款单金额</label>
			</td>
			<td class="fieldLabel medium" id="Vendors_detailView_fieldLabel_accountnumber">
				<label class="muted pull-left marginRight10px">付款单日期</label>
			</td>
			<td class="fieldLabel medium" id="Vendors_detailView_fieldValue_accountnumber">
				<label class="muted pull-left marginRight10px">数据来源</label>
			</td>
		</tr>
		{foreach from=$RSTATEMENT item=value}
		<tr>
			<td class="fieldValue medium">
				<label class="muted pull-left marginRight10px">{$value.flownumberofpaymentform}</label>
			</td>
			<td class="fieldValue medium" >
				<label class="muted pull-left marginRight10px">{$value.paymentamount}</label>
			</td>
			<td class="fieldValue medium" id="Vendors_detailView_fieldLabel_accountnumber">
				<label class="muted pull-left marginRight10px">{$value.paymentdate}</label>
			</td>
			<td class="fieldValue medium" id="Vendors_detailView_fieldValue_accountnumber">
				<label class="muted pull-left marginRight10px">{$value.sourcedata}</label>
			</td>
		</tr>
		{/foreach}

		</tbody>
	</table>
	{/if}
{literal}
    <script>$(function ()
        { $("[data-toggle='popover']").popover();
        });
    </script>
    {/literal}
	<script src="/libraries/jSignature/jSignature.min.noconflict.js"></script>
{/strip}
