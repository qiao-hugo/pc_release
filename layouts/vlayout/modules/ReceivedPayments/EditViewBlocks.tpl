{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *$('#fallintotable thead').after(aaaaa);
 ********************************************************************************/
-->*}
{strip}
<div class='container-fluid editViewContainer'>
	<form class="form-horizontal recordEditView" id="EditView" name="EditView" method="post" action="index.php" enctype="multipart/form-data">
		{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
		{if !empty($PICKIST_DEPENDENCY_DATASOURCE)}
			<input type="hidden" name="picklistDependency" value='{Vtiger_Util_Helper::toSafeHTML($PICKIST_DEPENDENCY_DATASOURCE)}' />
		{/if}
		{assign var=QUALIFIED_MODULE_NAME value={$MODULE}}
		{assign var=IS_PARENT_EXISTS value=strpos($MODULE,":")}
		{if $IS_PARENT_EXISTS}
			{assign var=SPLITTED_MODULE value=":"|explode:$MODULE}
			<input type="hidden" name="module" value="{$SPLITTED_MODULE[1]}" />
			<input type="hidden" name="parent" value="{$SPLITTED_MODULE[0]}" />
		{else}
			<input type="hidden" name="module" value="{$MODULE}" />
		{/if}
		<input type="hidden" name="action" value="Save" />
		<input type="hidden" name="maybe_account_display" value="" />
		<input type="hidden" name="maybe_account" value="" />
		<input type="hidden" name="record" value="{$RECORD_ID}" />
		<input type="hidden" name="defaultCallDuration" value="{$USER_MODEL->get('callduration')}" />
		<input type="hidden" name="defaultOtherEventDuration" value="{$USER_MODEL->get('othereventduration')}" />
		{if $IS_RELATION_OPERATION }
			<input type="hidden" name="sourceModule" value="{$SOURCE_MODULE}" />
			<input type="hidden" name="sourceRecord" value="{$SOURCE_RECORD}" />
			<input type="hidden" name="relationOperation" value="{$IS_RELATION_OPERATION}" />
		{/if}
		<div class="contentHeader row-fluid">
		{assign var=SINGLE_MODULE_NAME value='SINGLE_'|cat:$MODULE}
		{if $RECORD_ID neq ''}
			<h3 class="span8 textOverflowEllipsis" title="{vtranslate('LBL_EDITING', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)} {$RECORD_STRUCTURE_MODEL->getRecordName()}">{vtranslate('LBL_EDITING', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)} - {$RECORD_STRUCTURE_MODEL->getRecordName()}</h3>
		{else}
			<h3 class="span8 textOverflowEllipsis">{vtranslate('LBL_CREATING_NEW', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)}</h3>
		{/if}
			<span class="pull-right">
				<button class="btn btn-success" type="submit"><strong>{vtranslate('LBL_SAVE', $MODULE)}</strong></button>
				<a class="cancelLink" type="reset" onclick="javascript:window.history.back();">{vtranslate('LBL_CANCEL', $MODULE)}</a>
			</span>
		</div>
		{foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE name="EditViewBlockLevelLoop"}
			{if $BLOCK_FIELDS|@count lte 0}{continue}{/if}
            {if $BLOCK_LABEL eq 'LBL_ACCOUNT_RECEIVABLE' && empty($RECORD_ID)}{continue}{/if}
			<table class="table table-bordered blockContainer showInlineTable">
			<tr>
				<th class="blockHeader" colspan="4">{vtranslate($BLOCK_LABEL, $MODULE)}</th>
			</tr>
			<tr>
			{assign var=COUNTER value=0}
			{foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}

				{assign var="isReferenceField" value=$FIELD_MODEL->getFieldDataType()}
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
				<td class="fieldLabel {$WIDTHTYPE}">
					{if $isReferenceField neq "reference"}<label class="muted pull-right marginRight10px">{/if}
						{if $FIELD_MODEL->isMandatory() eq true && $isReferenceField neq "reference"} <span class="redColor">*</span> {/if}
						{if $FIELD_MODEL->getName() eq 'paytitle' }<span class="redColor" {if !$FIELD_MODEL->get('fieldvalue')} style="display:none " {/if} id="paytitleMust">*</span>{/if}
						{if $isReferenceField eq "reference"}
							{assign var="REFERENCE_LIST" value=$FIELD_MODEL->getReferenceList()}
							{assign var="REFERENCE_LIST_COUNT" value=count($REFERENCE_LIST)}
							{if $REFERENCE_LIST_COUNT > 1}
								{assign var="DISPLAYID" value=$FIELD_MODEL->get('fieldvalue')}
								{assign var="REFERENCED_MODULE_STRUCT" value=$FIELD_MODEL->getUITypeModel()->getReferenceModule($DISPLAYID)}
								{if !empty($REFERENCED_MODULE_STRUCT)}
									{assign var="REFERENCED_MODULE_NAME" value=$REFERENCED_MODULE_STRUCT->get('name')}
								{/if}
								<span class="pull-right">
									{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}
									<select id="{$MODULE}_editView_fieldName_{$FIELD_MODEL->getName()}_dropDown" class="chzn-select referenceModulesList streched" style="width:140px;">
										<optgroup>
											{foreach key=index item=value from=$REFERENCE_LIST}
												<option value="{$value}" {if $value eq $REFERENCED_MODULE_NAME} selected {/if}>{vtranslate($value, $MODULE)}</option>
											{/foreach}
										</optgroup>
									</select>
								</span>
							{else}
								{*<!--Related to 合同弹出框label-->*}
								<label class="muted pull-right marginRight10px">{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}{vtranslate($FIELD_MODEL->get('label'), $MODULE)}</label>
							{/if}
						{else if $FIELD_MODEL->get('uitype') eq "83"}
							{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) COUNTER=$COUNTER MODULE=$MODULE}
						{else}
							{vtranslate($FIELD_MODEL->get('label'), $MODULE)}
						{/if}
					{if $isReferenceField neq "reference"}</label>{/if}
				</td>
				{if $FIELD_MODEL->get('uitype') neq "83"}
					<td class="fieldValue {$WIDTHTYPE}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
							{if $FIELD_MODEL->get('editread') || ($FIELD_MODEL->get('readonly') eq '0' and !empty($RECORD_ID))}
							
							{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName(),$MODULE) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE RECORD=$RECORD}<input disabled="disabled" name="{$FIELD_MODEL->get('column')}" type="hidden" value="{$FIELD_MODEL->get('fieldvalue')}">
						{else}
						<div class="row-fluid">
							<span class="span10">
								{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) BLOCK_FIELDS=$BLOCK_FIELDS}
							</span>
						</div>
						{/if}
						
					</td>
				{/if}
				{if $BLOCK_FIELDS|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype"}
					<td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
				{/if}
				{if $MODULE eq 'Events' && $BLOCK_LABEL eq 'LBL_EVENT_INFORMATION' && $smarty.foreach.blockfields.last }
					{include file=vtemplate_path('uitypes/FollowUp.tpl',$MODULE) COUNTER=$COUNTER}
				{/if}
			{/foreach}
			</tr>
			</table>
			<br>
		{/foreach}
		<div>
		</div>

		<script>
		var aaaaa="<tr ><td><select  class=\"chzn-select\" name=\"suoshugongsi\[\]\"> <optgroup label=\"{vtranslate('LBL_USERS')}\">{foreach key=OWNER_ID item=OWNER_NAME from=$OWNCOMPANY}<option value=\"{$OWNER_ID}\" data-picklistvalue= '{$OWNER_NAME}' {if $FIELD_VALUE eq $OWNER_ID} selected {elseif $OWNER_ID eq "上海珍岛信息技术有限公司"}selected{/if}  	data-userId=\"{$CURRENT_USER_ID}\">{$OWNER_NAME} </option> {/foreach} </optgroup> </select> </td><td> {if $FIELD_VALUE eq ''}{assign var=FIELD_VALUE value=$USER_MODEL->get('id')}{/if}	<select class=\"chzn-select\" name=\"suoshuren\[\]\"> <optgroup label=\"{vtranslate('LBL_USERS')}\"> <option value=''>选择一个选项</option>	{foreach key=OWNER_ID item=OWNER_NAME from=$ACCESSIBLE_USERS} <option value=\"{$OWNER_ID}\" data-picklistvalue= '{$OWNER_ID}' {if $FIELD_VALUE eq $OWNER_ID} selected {/if} data-userId=\"{$CURRENT_USER_ID}\">{$OWNER_NAME}</option> {/foreach} </optgroup>	</select></td><td>	<div class=\"input-append\"> <input name=\"bili\[\]\" type=\"text\" placeholder = \"请输入比例\" class=\"scaling\" ><span class=\"add-on\">%</i></span></div></td><td> <input type=\"text\" name=\"fenchengjine\[\]\" placeholder = \"请输入分成金额\" class=\"fallintoprice\" readonly=\"readonly\"></td><td class=\"muted pull-right marginRight10px\"> <b><button class=\"btn btn-small deletefallinto\" type=\"button\"><i class=\" icon-trash\"></i></button></b> </td> </tr>";
        var bbbbb='<tr> <td> <select class=\"chzn-select\" name=\"extra_type\[\]\"> <option value="沙龙">沙龙</option> <option value="外采">外采</option><option value="手续费">手续费</option> <option value="其他">其他</option> </select> </td> <td>  <span class=\"redColor\">*</span><input data-validation-engine=\"validate\[required\]\" type=\"text\" name=\"extra_price\[\]\" placeholder=\"请输入金额\"> </td> <td> <textarea style="width:100%" type=\"textarea\" name=\"extra_remark\[\]\" placeholder=\"备注\"></textarea> </td> <td class=\"muted pull-right marginRight10px\"><b><button class=\"btn btn-small del_extra\" type=\"button\" ><i class=\"icon-trash\"></i></button></b></td> </tr>';
        </script>
		<div class="widgetContainer_receivehistory"></div>
        {if !empty($RECORD_ID)}
		<table class="table table-bordered table-striped blockContainer showInlineTable" id = "fallintotable">
			<thead>
			<tr><th class="blockHeader" colspan="5"><span >分成明细</span></th></tr>
			<tr>
				<td><b>所属公司</b></td>
				<td><b>业绩所属人</b></td>
				<td><b>比例</b></td>
				<td><b>分成金额</b></td>
				<td class="muted pull-right marginRight10px"><b><button class="btn btn-small" type="button" id="addfallinto"><i class=" icon-plus"></i></button></b></td>
			</tr>
			</thead>
			<tbody id="body">
			
			</tbody>
		</table>
        {/if}
    <table class="table table-bordered table-striped blockContainer showInlineTable" id = "">
        <thead>
        <tr><th class="blockHeader" colspan="4"><span >额外成本</span></th></tr>
        <tr>
            <td><b>费用类型</b></td>
            <td><b>金额(人民币)</b></td>
            <td><b>备注</b></td>
            <td class="muted pull-right marginRight10px"><b><button class="btn btn-small" type="button" id="add_extra"><i class=" icon-plus"></i></button></b></td>
        </tr>
        </thead>
        <tbody id="extra_body">
            {if $EXTRA_DATA neq ""}
                {foreach item="extra_data" from="$EXTRA_DATA"}
                    <tr>
                        <td>
                            <select class="chzn-select" name="extra_type[]">
                                <option value="沙龙" {if "沙龙" eq $extra_data['extra_type']}selected{/if}>沙龙</option>
                                <option value="外采" {if "外采" eq $extra_data['extra_type']}selected{/if}>外采</option>
								<option value="手续费" {if "手续费" eq $extra_data['extra_type']}selected{/if}>手续费</option>
                                <option value="媒介充值" {if "媒介充值" eq $extra_data['extra_type']}selected{/if}>媒介充值</option>
                                <option value="其他" {if "其他" eq $extra_data['extra_type']}selected{/if}>其他</option>
                            </select>
                        </td>
                        <td>
                            <span class="redColor">*</span><input type="text" name="extra_price[]" data-validation-engine="validate[required]" value="{$extra_data['extra_price']}" placeholder="请输入金额">
                        </td>
                        <td>
                            <textarea style="width:100%" type="textarea"   name="extra_remark[]" placeholder="备注">{$extra_data['extra_remark']}</textarea>
                        </td>
                        <td class="muted pull-right marginRight10px">
                            <b><button class="btn btn-small del_extra" type="button" ><i class="icon-trash"></i></button></b>
                        </td>
                    </tr>
                {/foreach}
            {/if}
        </tbody>
    </table>
{/strip}