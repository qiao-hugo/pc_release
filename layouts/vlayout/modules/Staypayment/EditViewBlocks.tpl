{strip}
<div class='container-fluid editViewContainer'>
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
		<input type="hidden" name="isauto" value="{$IS_AUTO}" />
		<input type="hidden" name="stay_type" value="{$STAY_TYPE}" />
		<input type="hidden" name="defaultCallDuration" value="{$USER_MODEL->get('callduration')}" />
		<input type="hidden" name="defaultOtherEventDuration" value="{$USER_MODEL->get('othereventduration')}" />
		<input type="hidden" name="companycode" value=""/>
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
						{if $FIELD_MODEL->getName() eq 'idcard' }<span class="redColor" id="idcardMust">*</span>{/if}
						{if $FIELD_MODEL->getName() eq 'taxpayers_no' }<span class="redColor"  id="taxpayersnoMust">*</span>{/if}
						{if $FIELD_MODEL->getName() eq 'explainfile' }<span class="redColor" {if !$FIELD_MODEL->get('fieldvalue')} style="display:none " {/if} id="explainfileMust">*</span>{/if}
						{if $FIELD_MODEL->getName() eq 'explaintext' }<span class="redColor" {if !$FIELD_MODEL->get('fieldvalue')} style="display:none " {/if} id="explaintextMust">*</span>{/if}
						{if $FIELD_MODEL->getName() eq 'enddate' }<span class="redColor" {if !$FIELD_MODEL->get('fieldvalue')} style="display:none " {/if} id="enddateMust">*</span>{/if}
						{if $FIELD_MODEL->getName() eq 'startdate' }<span class="redColor" {if !$FIELD_MODEL->get('fieldvalue')} style="display:none " {/if} id="startdateMust">*</span>{/if}
						{if $FIELD_MODEL->getName() eq 'staypaymentjine' }<span class="redColor" {if !$FIELD_MODEL->get('fieldvalue')} style="display:none " {/if} id="staypaymentjineMust">*</span>{/if}
						{if $FIELD_MODEL->getName() eq 'currencytype' }<span class="redColor" {if !$FIELD_MODEL->get('fieldvalue')} style="display:none " {/if} id="currencytypeMust">*</span>{/if}
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
								{if $FIELD_MODEL->get('name') eq 'explainfile'}
									{include file=vtemplate_path('uitypes/ExplainFileUpload.tpl', $MODULE) BLOCK_FIELDS=$BLOCK_FIELDS}
								{else}
									{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE)}
								{/if}
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
		<br>
		<span style="color: red;">
			特别说明：
			<br>
			1、流程：代付款信息录入后，需携带“代付款证明原件”，移交财务部合同管理员处。
			<br>
			2、注意：代付款证明原件对方敲章必须为原件红章。
			<br>
			3、所有空白处（包括日期）均需填写清晰、完整。
			<br>
			4、（个人主体）甲方需加盖合同专用章或公章，乙方需亲笔签名，（企业主体）甲、乙双方需加盖合同专用章或公章。
			<br>
			5、提供本代付款证明时，请同时提供乙方代付凭证，并由甲方加盖合同专用章或公章。
			<br>
			6、甲方未按前述要求提供代付款证明时，珍岛信息技术（上海）股份有限公司财务部有权拒收，视为甲方未履行主合同下的付款义务。
			<br>
			7、同一代付款人为不同客户付款时，需额外加以附件证明两家公司有关联关系（如证明是同一个法人主体，或均与代付人有关联关系）。
		</span>
		<br>
		<br>
		<br>

		{/strip}
