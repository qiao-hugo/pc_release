{strip}
<div class='container-fluid editViewContainer'>
	<input type="hidden" name="defa_year" value="{$DEFA_YEAR}">
	<input type="hidden" name="defa_month" value="{$DEFA_MONTH}">
	<input type="hidden" name="last_name" value="{$LAST_NAME}">
	<input type="hidden" name="main_ismodify" value="{$MAIN_ISMODIFY}">
	
	<form class="form-horizontal recordEditView" id="EditView" name="EditView" method="post" action="index.php" enctype="multipart/form-data">
		{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
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
												<option value="{$value}" {if $USERID eq $index}selected{/if} {if $value eq $REFERENCED_MODULE_NAME} selected {/if}>{vtranslate($value, $MODULE)}</option>
											{/foreach}
										</optgroup>
									</select>
								</span>
							{else}
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
	            	<input type="hidden" t-name="salestargetdetailid" name="salestargetdetailid[{$WEEKDATE.weekNum}]" value="{$WEEKDATE.salestargetdetailid}">
	            	{/if}
	            	<input type="hidden"  t-name="weekismodify" name="weekismodify" value="{$WEEKDATE.ismodify}">

	                <img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">&nbsp;&nbsp;
	                销售周报&nbsp;&nbsp;<span class="label label-success">{vtranslate({$KEY},{$MODULE_NAME})}</span>
	            </th>
	        </tr>
	        </thead>
	        <tbody >
	        <tr>
	            <td><b></b></td>
	            <td><b></b></td>
	            <td><b></b></td>
	            <td style="text-align: right;">
	            {if $IS_SHOW_RETURN_BUTTON eq '1'}
	            	<b><button class="btn btn-lg weebSalesReturn" type="button" >驳回</button></b>&nbsp;&nbsp;&nbsp;
	            {/if}
	            
	            {if $WEEKDATE.ismodify neq '1'}
	            	<b><button class="btn btn-lg weebSalesSubmit" type="button" >提交</button></b>&nbsp;&nbsp;&nbsp;
	            {/if}

	            <b><button class="btn btn-lg deleteTable" type="button" ><i class="icon-trash"></i></button></b></td>
	        </tr>
	        <tr>
	        	<td>　　　　周次&nbsp;&nbsp;&nbsp;<input t-name="weekNum" type="text" readonly="readonly" value="{$WEEKDATE.weekNum}" name="weeknum[{$WEEKDATE.weekNum}]"></td>
	        	<td>周开始日期&nbsp;&nbsp;&nbsp;<input  t-name="startdate" type="text"  readonly="readonly" value="{$WEEKDATE.startdate}" name="startdate[{$WEEKDATE.weekNum}]"></td>
	        	<td>　　周结束日期&nbsp;&nbsp;&nbsp;<input t-name="enddate"  type="text" readonly="readonly" value="{$WEEKDATE.enddate}" name="enddate[{$WEEKDATE.weekNum}]"></td>
	        	<td></td>
	        </tr>
	        <tr>
	        	<td>计划邀约目标&nbsp;&nbsp;&nbsp;<input t-name="weekinvitationtarget" type="number"  value="{$WEEKDATE.weekinvitationtarget}"  name="weekinvitationtarget[{$WEEKDATE.weekNum}]"></td>
	        	<td>实际邀约数&nbsp;&nbsp;&nbsp;<input t-name="weekinvitation" type="number"  value="{$WEEKDATE.weekinvitation}" readonly="readonly" name="weekinvitation[{$WEEKDATE.weekNum}]"></td>
	        	<td>邀约目标完成率&nbsp;&nbsp;&nbsp;<input t-name="weekinvitationrate" type="text" value="{$WEEKDATE.weekinvitationrate}" readonly="readonly" name="weekinvitationrate[{$WEEKDATE.weekNum}]"></td>
	        	<td>原因&nbsp;&nbsp;&nbsp;<input type="text" t-name="weekinvitationremarks" value="{$WEEKDATE.weekinvitationremarks}" name="weekinvitationremarks[{$WEEKDATE.weekNum}]"></td>
	        </tr>
	        <tr>
	        	<td>计划拜访目标&nbsp;&nbsp;&nbsp;<input t-name="weekvisittarget" type="number" value="{$WEEKDATE.weekvisittarget}" name="weekvisittarget[{$WEEKDATE.weekNum}]"></td>
	        	<td>实际拜访数&nbsp;&nbsp;&nbsp;<input t-name="weekvisit" type="number"  value="{$WEEKDATE.weekvisit}" readonly="readonly" name="weekvisit[{$WEEKDATE.weekNum}]"></td>
	        	<td>拜访目标完成率&nbsp;&nbsp;&nbsp;<input t-name="weekvisitrate" type="text" value="{$WEEKDATE.weekvisitrate}" readonly="readonly" name="weekvisitrate"[{$WEEKDATE.weekNum}]></td>
	        	<td>原因&nbsp;&nbsp;&nbsp;<input t-name="weekvisitrateremarks" type="text" value="{$WEEKDATE.weekvisitrateremarks}" name="weekvisitrateremarks[{$WEEKDATE.weekNum}]"></td>
	        </tr>
	        <tr>
	        	<td>计划业绩目标&nbsp;&nbsp;&nbsp;<input t-name="weekachievementtargt" type="number" value="{$WEEKDATE.weekachievementtargt}" name="weekachievementtargt[{$WEEKDATE.weekNum}]"></td>
	        	<td>实际业绩数&nbsp;&nbsp;&nbsp;<input  t-name="weekachievement" type="number"   value="{$WEEKDATE.weekachievement}" readonly="readonly" name="weekachievement[{$WEEKDATE.weekNum}]"></td>
	        	<td>业绩目标完成率&nbsp;&nbsp;&nbsp;<input t-name="weekachievementrate" type="text" value="{$WEEKDATE.weekachievementrate}" readonly="readonly" name="weekachievementrate[{$WEEKDATE.weekNum}]"></td>
	        	<td>原因&nbsp;&nbsp;&nbsp;<input t-name="weekachievementremarks" type="text" value="{$WEEKDATE.weekachievementremarks}" name="weekachievementremarks[{$WEEKDATE.weekNum}]"></td>
	        </tr>
	        <tr>
	        	<td colspan="2" style="text-align: center;">行动方案&nbsp;&nbsp;&nbsp;<textarea t-name="programme" name="programme[{$WEEKDATE.weekNum}]" style="width:75%;">{$WEEKDATE.programme}</textarea></td>
	        	<td  colspan="2" style="text-align: left;"></td>
	        </tr>
	        </tbody>
	    </table>
    {/foreach}


    <!-- <div style="position:fixed;right: 5%; bottom:30%;" lang="1" class="insertbefore"><b class="pull-right"><button class="btn btn-small" type="button" id="addfallinto" style="border:1px dashed #178fdd;">添加周 <i class="icon-plus" title="添加周销售目标"></i></button></b></div> -->

    <style type="text/css">
   		.weektable td{
   			text-align: center;
   		}
   	</style>
{/strip}