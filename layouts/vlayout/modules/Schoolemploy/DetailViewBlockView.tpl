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
	{if $followdata|@count neq 0}
	<table class="table table-bordered equalSplit detailview-table">
		<thead>
			<th class="blockHeader" colspan="2"><img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="144"><img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="144">&nbsp;&nbsp;跟进详细</th>
		</thead>
		<tr><td>跟进状态</td><td>跟进时间</td></tr>
		<tr>{foreach item=da from=$followdata}<td>{$da}</td>{/foreach}</tr>
	</table>
	{/if}
	
	{if $ACHIEVEMENTALLOTDATA neq ""}
	<table class="table table-bordered blockContainer showInlineTable" id="achievementallottable">
			<tr><thead><th class="blockHeader" colspan="6"><span>回款业绩分配</span></th></thead></tr>
				{foreach key=BLOCK_LABEL  item=BLOCK_FIELDS from=$ACHIEVEMENTALLOTDATA name="EditViewBlockLevelLoop"}
					<tr>
					<td><label class="muted pull-right marginRight10px">业绩所属人</label></td>
					<td> <input type='hidden' name="achievementallotdata[{$smarty.foreach.EditViewBlockLevelLoop.index}][]" value="{$BLOCK_FIELDS['receivedpaymentownid']}">{$BLOCK_FIELDS['receivedpaymentownid']}</td>
					<td><label class="muted pull-right marginRight10px">事业部</label></td>
					<td> <input type='hidden' name="achievementallotdata[{$smarty.foreach.EditViewBlockLevelLoop.index}][]" value="{$BLOCK_FIELDS['businessunit']}">{$BLOCK_FIELDS['businessunit']}</td>
					<td><label class="muted pull-right marginRight10px">所属公司</label></td>
					<td> <input type='hidden' name="achievementallotdata[{$smarty.foreach.EditViewBlockLevelLoop.index}][]" value="{$BLOCK_FIELDS['owncompanys']}">{$BLOCK_FIELDS['owncompanys']}</td>
					</tr>	
				{/foreach}
	</table>
	{/if}

	<script type="text/javascript">
		
		/*var accessible_users = '<select class="chzn-select" name=""><optgroup label={vtranslate("LBL_USERS")}>{foreach key=OWNER_ID item=OWNER_NAME from=$ACCESSIBLE_USERS}<option value="{$OWNER_ID}" data-picklistvalue="{$OWNER_ID}">{$OWNER_NAME}</option>{/foreach}</optgroup></select>' ;*/

		var accessible_users = "<select id=\"ddddd\" class=\"chzn-select\" name=\"reportsower\"> <optgroup label=\"{vtranslate('LBL_USERS')}\"> 	{foreach key=OWNER_ID item=OWNER_NAME from=$ACCESSIBLE_USERS} <option value=\"{$OWNER_ID}\" data-picklistvalue= '{$OWNER_ID}' {if $FIELD_VALUE eq $OWNER_ID} selected {/if} data-userId=\"{$CURRENT_USER_ID}\">{$OWNER_NAME}</option> {/foreach} </optgroup>	</select>";
	</script>
	<style type="text/css">
		.select2-drop{
			z-index: 1000043;
		}
	</style>
	<input type="hidden" name="recordId" value="{$recordId}">



	{*简历合格人员信息*}
	{if $SCHOOLQUALIFIEDPEOPLE|@count neq 0}
	<table class="table table-bordered blockContainer showInlineTable  detailview-table" id = "fallintotable">
        <thead>
        <tr>
            <th class="blockHeader" colspan="8" >
                简历合格人员信息
            </th>
        </tr>
        </thead>
        <tbody>
        <tr>
        	<td><b>姓名</b></td>
        	<td><b>性别</b></td>
        	<td><b>联系电话</b></td>
        	<td><b>邮箱</b></td>

        	<td><b>用户名</b></td>
        	<td><b>部门</b></td>
        	<td><b>职务</b></td>

        	<td><b>操作</b></td>
        </tr>
        {foreach from=$SCHOOLQUALIFIEDPEOPLE item="VO" key="divide_key"}
        
        <tr >
            <td>{$VO['name']}</td>
            <td>{$VO['gendertype']}</td>
            <td>{$VO['telephone']}</td>
            <td>{$VO['email']}</td>

            <td>{$VO['assessmentresult']}</td>
            <td>{$VO['assessmentdate']}</td>
            <td>{$VO['instructor']}</td>
            <td>
            	{if $IS_ADDUSER eq 1}
				<a target="_blank"  href="index.php?module=Users&parent=Settings&view=Edit&sid={$VO['t_schoolresumeid']}" ><i title="正式录用" class="icon-shopping-cart alignMiddle"></i></a>&nbsp;
				{/if}
            	<a target="_blank"  href="index.php?module=Schoolresume&view=Detail&record={$VO['schoolresumeid']}"><i title="详细信息" class="icon-th-list alignMiddle"></i></a>&nbsp;
            	
            </td>
        </tr>
        {/foreach}
        </tbody>
    </table>
    {/if}
{/strip}