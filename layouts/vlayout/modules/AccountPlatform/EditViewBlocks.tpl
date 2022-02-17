{*<!--
/*******************************
   * 编辑或新增通用字段显示模版
  *编辑下不可编辑字段也要显示
  *增加只读字段 可新增不可编辑
  *文本域展一行 uitype 19/20
  {if !empty($PICKIST_DEPENDENCY_DATASOURCE)}<input type="hidden" name="picklistDependency" value='{Vtiger_Util_Helper::toSafeHTML($PICKIST_DEPENDENCY_DATASOURCE)}' />{/if}
 ********/
-->*}
{strip}
<style>
	.file {
		position: relative;
		display: inline-block;
		background: #08c;
		border: 1px solid #08c;
		padding: 4px 12px;
		overflow: hidden;
		color: white !important;
		text-decoration: none;
		text-indent: 0;
		line-height: 20px;
	}
</style>
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
	{if $RECORD_ID > 0 }
		<table class="table table-bordered blockContainer showInlineTable plusTableContent" data-num="yesreplace">
			<thead>
			<tr>
				<th class="blockHeader" colspan="4">
					<img class="cursorPointer alignMiddle blockToggle  hide" src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;"> <img class="cursorPointer alignMiddle blockToggle" src="layouts/vlayout/skins/softed/images/arrowDown.png"data-mode="show" data-id="141" style="display: inline;">&nbsp;&nbsp;账户列表&nbsp;&nbsp;
				</th>

			</tr>
			</thead>
			<tbody id="accountDetail" >
			<tr>
				<td class="fieldLabel medium">
				</td>
				<td class="fieldValue medium">
					账户ID
				</td>
				<td class="fieldLabel medium">
				</td>
				<td class="fieldValue medium">
					账户名称
				</td>
			</tr>
			{foreach  key=key item=FIELD  from=$DETAIL_INFO_LIST  }
				<tr  data-id="{$FIELD['accountplatform_detail_id']}"  data-idaccount="{$FIELD['idaccount']}" data-accountplatform="{$FIELD['accountplatform']}"  ><input type="hidden"  name="accountplatform_detail_id[]"  value="{$FIELD['accountplatform_detail_id']}" /><input type="hidden"  name="updateStatus[]" id="updateStatus"  value="0" />
					<td class="fieldLabel medium">
						<label class="muted pull-right marginRight10px"><span class="redColor">*</span></label>
					</td>
					<td class="fieldValue medium">
						<input id="oldidaccount" type="hidden" class="input-large"  name="oldidaccount[]" value="{$FIELD['idaccount']}" >
						<input id="idaccount" type="text" class="input-large idaccount" onkeyup="this.value=this.value.replace(/\s+/g,'')" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="idaccount[]" value="{$FIELD['idaccount']}" >
					</td>
					<td class="fieldLabel medium">
					</td>
					<td class="fieldValue medium">
						<input id="oldaccountplatform" type="hidden" class="input-large"  name="oldaccountplatform[]" value="{$FIELD['accountplatform']}" >
						<input id="accountplatform" type="text" class="input-large accountplatform" data-validation-engine="validate[ funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="accountplatform[]" value="{$FIELD['accountplatform']}" ><a class="deleteRecordButton"><i title="删除" class="icon-trash alignMiddle"></i></a>
					</td>
				</tr>
			{/foreach}
			</tbody>
		</table>
	{else}
		<table class="table table-bordered  blockContainer showInlineTable plusTableContent" data-num="yesreplace">
			<thead>
			<tr>
				<th class="blockHeader" colspan="3">
					<img class="cursorPointer alignMiddle blockToggle  hide" src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;"> <img class="cursorPointer alignMiddle blockToggle" src="layouts/vlayout/skins/softed/images/arrowDown.png"data-mode="show" data-id="141" style="display: inline;"> &nbsp;&nbsp;账户列表&nbsp;&nbsp;
				</th>
				<th class="blockHeader">
					<input type="button" id="uploadImport" value="批量导入" title="文件名请勿包含空格" style="display: none;">
					<input type="button" id="importExplain" value="（导入说明）" class="file" title="导入说明" style="margin-left:30px; cursor: pointer;">
				</th>
			</tr>
			</thead>
			<tbody id="accountDetail">
			<tr>
				<td class="fieldLabel medium">
				</td>
				<td class="fieldValue medium">
					账户ID
				</td>
				<td class="fieldLabel medium">
				</td>
				<td class="fieldValue medium">
					账户名称
				</td>
			</tr>
			<tr>
				<input type="hidden"  name="accountplatform_detail_id[]"  value="0" /><input type="hidden"  name="updateStatus[]" id="updateStatus" value="0" />
				<td class="fieldLabel medium">
					<label class="muted pull-right marginRight10px"><span class="redColor">*</span></label>
					</td>
				<td class="fieldValue medium">
					<input id="oldidaccount" type="hidden" class="input-large"  name="oldidaccount[]" value="" >
					<input id="idaccount" type="text" class="input-large idaccount" onkeyup="this.value=this.value.replace(/\s+/g,'')" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="idaccount[]" placeholder="请输入账户ID" value="" >
					</td>
				<td class="fieldLabel medium">
					</td>
				<td class="fieldValue medium">
					<input id="accountplatform" type="text" class="input-large accountplatform" data-validation-engine="validate[ funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="accountplatform[]"  placeholder="请输入账户名称" value="" ><a class="deleteRecordButton"><i title="删除" class="icon-trash alignMiddle"></i></a>
					</td>
			</tr>
			</tbody>
		</table>
	{/if}
	<div id="inserttable"></div>
	<div style="position:fixed;right: 5%;bottom:45%;" ><b class="pull-right"><button class="btn btn-small" type="button" id="appendAccountDetail" style="border:1px dashed #178fdd;border-radius:20px;width:40px;height:40px;" autocomplete="off"><i class="icon-plus" title="点击添加账户明细"></i></button></b></div>
	<literal>
		<script>
            var appendAccountDetail ='<tr><input type="hidden"  name="accountplatform_detail_id[]"  value="0" /><input type="hidden"  name="updateStatus[]" id="updateStatus" value="0" />' +
                '<td class="fieldLabel medium">' +
                '<label class="muted pull-right marginRight10px"><span class="redColor">*</span></label>' +
                '</td>' +
                '<td class="fieldValue medium">' +
                '<input id="oldidaccount" type="hidden" class="input-large"  name="oldidaccount[]" value="" >'+
                '<input id="idaccount" type="text" class="input-large idaccount" onkeyup="this.value=this.value.replace(/\\s+/g,\'\')" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="idaccount[]"  placeholder="请输入账户ID" value="" >' +
                '</td>' +
                '<td class="fieldLabel medium">' +
                '</td>' +
                '<td class="fieldValue medium">' +
                '<input id="accountplatform" type="text" class="input-large accountplatform" data-validation-engine="validate[ funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="accountplatform[]"  placeholder="请输入账户名称" value="" ><a class="deleteRecordButton"><i title="删除" class="icon-trash alignMiddle"></i></a>' +
                '</td>' +
                '</tr>';
		</script>
	</literal>
{/strip}