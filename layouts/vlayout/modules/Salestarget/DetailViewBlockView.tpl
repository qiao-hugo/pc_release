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


	{*{if $WEEKDATE.show neq '1'}style="display:none;"{/if}*}
	{foreach key=KEY item=WEEKDATE from=$DATE_ARR}
		<table  class="{$KEY} weektable fallintotable1 table table-bordered blockContainer showInlineTable  detailview-table Duplicates">
	        <thead>
	        <tr>
	            <th class="blockHeader" colspan="4" >
	            	{if $WEEKDATE.salestargetdetailid eq ''}
	            	<input type="hidden" name="inserti[{$WEEKDATE.weekNum}]" value="{$WEEKDATE.weekNum}">
	            	{else}
	            	<input type="hidden" name="updatei[{$WEEKDATE.weekNum}]" value="{$WEEKDATE.weekNum}">
	            	<input type="hidden" name="salestargetdetailid[{$WEEKDATE.weekNum}]" value="{$WEEKDATE.salestargetdetailid}">
	            	{/if}
	                <img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">&nbsp;&nbsp;
	                销售周报 <span class="label label-success">{vtranslate({$KEY},{$MODULE_NAME})}</span>
	            </th>
	        </tr>
	        </thead>
	        <tbody >
	        <!-- <tr>
	            <td><b></b></td>
	            <td><b></b></td>
	            <td><b></b></td>
	            <td style="text-align: right;">
	            <b><button class="btn btn-lg" type="button" >驳回</button></b>&nbsp;&nbsp;&nbsp;
	            <b><button class="btn btn-lg" type="button" >提交</button></b>&nbsp;&nbsp;&nbsp;
	            <b><button class="btn btn-lg deleteTable" type="button" ><i class="icon-trash"></i></button></b></td>
	        </tr> -->
	        <tr>
	        	<td>　　　　周次&nbsp;&nbsp;&nbsp;{$WEEKDATE.weekNum}
	        	<td>周开始日期&nbsp;&nbsp;&nbsp;{$WEEKDATE.startdate}</td>
	        	<td>　　周结束日期&nbsp;&nbsp;&nbsp;{$WEEKDATE.enddate}</td>
	        	<td></td>
	        </tr>
	        <tr>
	        	<td>计划邀约目标&nbsp;&nbsp;&nbsp;{$WEEKDATE.weekinvitationtarget}</td>
	        	<td>实际邀约数&nbsp;&nbsp;&nbsp;{$WEEKDATE.weekinvitation}</td>
	        	<td>邀约目标完成率&nbsp;&nbsp;&nbsp;{$WEEKDATE.weekinvitationrate}</td>
	        	<td>原因&nbsp;&nbsp;&nbsp;{$WEEKDATE.weekinvitationremarks}</td>
	        </tr>
	        <tr>
	        	<td>计划拜访目标&nbsp;&nbsp;&nbsp;{$WEEKDATE.weekvisittarget}</td>
	        	<td>实际拜访数&nbsp;&nbsp;&nbsp;{$WEEKDATE.weekvisit}</td>
	        	<td>拜访目标完成率&nbsp;&nbsp;&nbsp;{$WEEKDATE.weekvisitrate}</td>
	        	<td>原因&nbsp;&nbsp;&nbsp;{$WEEKDATE.weekvisitrateremarks}</td>
	        </tr>
	        <tr>
	        	<td>计划业绩目标&nbsp;&nbsp;&nbsp;{$WEEKDATE.weekachievementtargt}</td>
	        	<td>实际业绩数&nbsp;&nbsp;&nbsp;{$WEEKDATE.weekachievement}</td>
	        	<td>业绩目标完成率&nbsp;&nbsp;&nbsp;{$WEEKDATE.weekachievementrate}</td>
	        	<td>原因&nbsp;&nbsp;&nbsp;{$WEEKDATE.weekachievementremarks}</td>
	        </tr>
	        <tr>
	        	<td  style="text-align: center;">行动方案&nbsp;&nbsp;&nbsp;{$WEEKDATE.programme}</td>
	        	<td colspan="3" style="text-align: center;"></td>
	        </tr>
	        </tbody>
	    </table>
    {/foreach}

   	<style type="text/css">
   		.weektable td{
   			text-align: center;
   		}
   	</style>
{/strip}