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
		.draggable-element {
			display: block;
			width: 140px;
			height: 50px;
			background: white;
			border: 1px solid rgb(196, 196, 196);
			line-height: 50px;
			text-align: center;
			color: rgb(51, 51, 51);
			font-size: 12px;
			cursor: move;
			float: left;
			word-break: break-all;
			word-wrap: break-word;
		}
		.draggable-element:active{
			background-color:#e3e3e3;
		}

		.draggable-element::selection{
			color:#000000;
		}
		.equalSplit td{
			width: 0%;
		}
		/*.chzn-choices{
			width: 250%;
		}*/
		.modal-body{
			max-height: 1000px;
		}
        .marginRight10px{
            width: 90px;
        }
		.customclass::-webkit-scrollbar {
			width: 2px;
			height: 1px;
		}
		.customclass::-webkit-scrollbar-thumb {
			border-radius: 10px;
			background-color: #F90;
			background-image: -webkit-linear-gradient(45deg, rgba(255, 255, 255, .2) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .2) 50%, rgba(255, 255, 255, .2) 75%, transparent 75%, transparent);
		}
		.customclass::-webkit-scrollbar-track {
			-webkit-box-shadow: inset 0 0 5px rgba(0,0,0,0.2);
			background: #EDEDED;
		}
	</style>
	{foreach key=BLOCK_LABEL_KEY item=FIELD_MODEL_LIST from=$RECORD_STRUCTURE}
	{assign var=BLOCK value=$BLOCK_LIST[$BLOCK_LABEL_KEY]}
	{if $BLOCK eq null or $FIELD_MODEL_LIST|@count lte 0}{continue}{/if}
	{assign var=IS_HIDDEN value=$BLOCK->isHidden()}
	{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
	<input type=hidden name="timeFormatOptions" data-value='{$DAY_STARTS}' />
	<input type=hidden name="recordId" data-value='{$RECORDID}' />
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
			{if $FIELD_MODEL->getName() eq 'parentcate'}
				<td class="fieldValue {$WIDTHTYPE}" id="{$MODULE}_detailView_fieldValue_{$FIELD_MODEL->getName()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
					 <span class="value" data-field-type="{$FIELD_MODEL->getFieldDataType()}">
                        {$PARENTCATE}
					 </span>
				</td>
			{elseif $FIELD_MODEL->getName() eq 'soncate'}
				<td class="fieldValue {$WIDTHTYPE}" id="{$MODULE}_detailView_fieldValue_{$FIELD_MODEL->getName()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
					 <span class="value" data-field-type="{$FIELD_MODEL->getFieldDataType()}">
                        {$SONCATE}
					 </span>
				</td>
			{elseif $FIELD_MODEL->getName() eq 'special'}
				<td class="fieldValue {$WIDTHTYPE}" id="{$MODULE}_detailView_fieldValue_{$FIELD_MODEL->getName()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
					 <span class="value" data-field-type="{$FIELD_MODEL->getFieldDataType()}">
                        {$SPECIAL}
					 </span>
				</td>
			{else}
				<td class="fieldValue {$WIDTHTYPE}" id="{$MODULE}_detailView_fieldValue_{$FIELD_MODEL->getName()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
					 <span class="value" data-field-type="{$FIELD_MODEL->getFieldDataType()}">
                        {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME RECORD=$RECORD}{if $FIELD_MODEL->name eq 'probability'}%{/if}
					 </span>
				</td>
			{/if}

			 {/if}

		{if $FIELD_MODEL_LIST|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype" and $FIELD_MODEL->get('uitype') neq "69" and $FIELD_MODEL->get('uitype') neq "105"}
			<td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
		{/if}
		{/foreach}
		</tr>
		</tbody>
	</table>
	{/foreach}

	<div class="relatedContents contents-bottomscroll">
		<div class="bottomscroll-div">
			<table class="table table-bordered listViewEntriesTable">
				<thead>
				<tr class="listViewHeaders">
					<th nowrap="" class="medium"><a href="javascript:void(0);">适用部门</a></th>
					<th nowrap="" class="medium" colspan="3"><a href="javascript:void(0);">审核流程</a></th>
					<th nowrap="" class="medium"><a href="javascript:void(0);">修改</a>  <div class="btn addButton" data-soncateid="{$SONCATEID}" data-parentcate="{$PARENTCATE}" data-soncate="{$SONCATE}" id="newworkflow" style="float:right;cursor:pointer;">新增</div></th>
				</tr>
				</thead>
				<tbody>
				{foreach key=SONCATEWORKFLOWSKEY item=SONCATEWORKFLOW from=$SONCATEWORKFLOWS}
					<tr class="listViewEntries">
						<td class="medium" nowrap="">{$SONCATEWORKFLOW['department']}</td>
						<td class="medium" nowrap="" colspan="3">
							{foreach key=WORKFLOWSKEY item=WORKFLOW from=$SONCATEWORKFLOW['workflowstages']}
								<a target="_blank" class="workflowstagesid workflowstagesid{$SONCATEWORKFLOW['filterworkflowstageid']}" data-workflowstagesname="{$WORKFLOW['workflowstagesname']}" data-workflowstagesid="{$WORKFLOW['workflowstagesid']}" href="index.php?module=WorkflowStages&view=Detail&record={$WORKFLOW['workflowstagesid']}">{$WORKFLOW['workflowstagesname']}</a> ->
							{/foreach}
							结束
						</td>
						<td>
							<a class="updateRecordButton"   data-soncateid="{$SONCATEID}" data-departmentid="{$SONCATEWORKFLOW['departmentid']}" data-parentcate="{$PARENTCATE}" data-soncate="{$SONCATE}" data-filterworkflowstageid="{$SONCATEWORKFLOW['filterworkflowstageid']}" data-ceocheck="{$SONCATEWORKFLOW['ceocheck']}" data-companycode="{$SONCATEWORKFLOW['companycode']}" data-soncateworkflowid="{$SONCATEWORKFLOW['filterworkflowid']}"><i title="编辑" class="icon-pencil alignMiddle"></i></a>  &nbsp;&nbsp;
							<a class="deleteRecordButton" data-soncateworkflowid="{$SONCATEWORKFLOW['filterworkflowstageid']}"><i title="删除" class="icon-trash alignMiddle"></i></a>
						</td>
					</tr>
				{/foreach}
				</tbody>
			</table>
		</div>
	</div>
	{*    {include file=vtemplate_path('RecentComments.tpl','Channels') MODULE='Channels'}*}
	<script type="text/javascript" src="resources/drag-arrange.js"></script>
{/strip}