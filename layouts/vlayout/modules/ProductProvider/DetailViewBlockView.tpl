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
			{if !$FIELD_MODEL->isViewableInDetailView() || $FIELD_MODEL->get('name') eq 'idaccount' || $FIELD_MODEL->get("name") eq 'accountzh'}
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
	{/foreach}


	<table class="table table-bordered equalSplit detailview-table">
			<thead>
			<tr>
				<th class="blockHeader" colspan="4">
					<img class="cursorPointer alignMiddle blockToggle  hide" src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;"> <img class="cursorPointer alignMiddle blockToggle" src="layouts/vlayout/skins/softed/images/arrowDown.png"data-mode="show" data-id="141" style="display: inline;"> &nbsp;&nbsp;账户列表&nbsp;&nbsp;
				</th>
			</tr>
			</thead>
			<tbody  id="accountDetail">
				<tr>
					<td class="fieldLabel medium">
					</td>
					<td class="fieldValue medium">
						账户ID
					</td>
					<td class="fieldLabel medium">
						账户名称
					</td>
					<td class="fieldValue medium">
                        操作
					</td>
				</tr>
				{foreach  key=key item=FIELD from=$DETAIL_INFO_LIST }
				<tr  data-id="{$FIELD['productprovide_detail_id']}" >
					<td class="fieldLabel {$WIDTHTYPE}" >
						<label class="muted pull-right marginRight10px"><span class="redColor">*</span></label>
					</td>
					<td class="fieldValue {$WIDTHTYPE}"  >
						<span class="value" data-field-type="string" id="textidaccount" >{$FIELD['idaccount']}</span>
						<input id="idaccount" style="display: none;" type="text" class="input-large" onkeyup="this.value=this.value.replace(/\s+/g,'')" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="idaccount[]"  value="{$FIELD['idaccount']}" >
					</td>
					<td class="fieldLabel {$WIDTHTYPE}" >
						<span class="value" data-field-type="string" id="textaccountzh">{$FIELD['accountzh']}</span>
						<input id="accountzh" style="display: none;" type="text" class="input-large" data-validation-engine="validate[ funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="accountzh[]" value="{$FIELD['accountzh']}" >
					</td>
					<td class="fieldValue {$WIDTHTYPE}" >
						<span class="clickSave" style="display:none;"><i class="icon-ok alignMiddle" title="点击保存账户明细"></i></span>&nbsp;&nbsp;<a class="clickEdit"><i class="icon-pencil alignMiddle" title="点击编辑账户明细"></i></a>&nbsp;&nbsp;<a class="deleteRecordButton"><i title="删除" class="icon-trash alignMiddle"></i></a>
					</td>
				</tr>
			   {/foreach}
			</tbody>
	</table>
	<div style="position:fixed;right: 15%;bottom:15%;" ><b class="pull-right"><button class="btn btn-small" type="button" id="appendAccountDetail" style="border:1px dashed #178fdd;border-radius:20px;width:40px;height:40px;" autocomplete="off"><i class="icon-plus" title="点击添加账户明细"></i></button></b></div>
	<literal>
		<script>
            var appendAccountDetail ='<tr>' +
                '<td class="fieldLabel medium">' +
                '<label class="muted pull-right marginRight10px"><span class="redColor">*</span></label>' +
                '</td>' +
                '<td class="fieldValue medium">' +
				'<span class="value" data-field-type="string" id="textidaccount" ></span>'+
                '<input id="idaccount" type="text" class="input-large" onkeyup="this.value=this.value.replace(/\\s+/g,\'\')" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="idaccount[]" placeholder="请输入账户ID" value="" >' +
                '</td>' +
                '<td class="fieldLabel medium">' +
				'<span class="value" data-field-type="string" id="textaccountzh"></span>'+
				'<input id="accountzh" type="text" class="input-large" data-validation-engine="validate[ funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="accountzh[]"  placeholder="请输入账户名称" value="" >'+
                '</td>' +
                '<td class="fieldValue medium">' +
                '<a class="clickSave"><i class="icon-ok alignMiddle" title="点击保存账户明细"></i></a>&nbsp;&nbsp;<a class="clickEdit" style="display:none"><i class="icon-pencil alignMiddle" title="点击编辑账户明细"></i></a>&nbsp;&nbsp;<a class="deleteRecordButton"><i title="删除" class="icon-trash alignMiddle"></i></a>' +
                '</td>' +
                '</tr>';
		</script>
	</literal>
{/strip}