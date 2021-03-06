{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
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
	<input type="hidden" name="record" value="{$RECORD_ID}" />
	<input type="hidden" name="defaultCallDuration" value="{$USER_MODEL->get('callduration')}" />
	<input type="hidden" name="defaultOtherEventDuration" value="{$USER_MODEL->get('othereventduration')}" />
	<input type="hidden" name="countDetail" value="{$COUNTD_DETAIL}" />
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
						{elseif $FIELD_MODEL->get('uitype') eq "83"}
							{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) COUNTER=$COUNTER MODULE=$MODULE}
						{else}
							{vtranslate($FIELD_MODEL->get('label'), $MODULE)}
						{/if}
						{if $isReferenceField neq "reference"}</label>{/if}
				</td>
				{if $FIELD_MODEL->get('uitype') neq "83"}
					<td class="fieldValue {$WIDTHTYPE}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
						<div class="row-fluid">
							<span class="span10">
								{*START STEEL 2015???2???12?????????*}
								{if $RECORD_ID gt 0}
									{*include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) BLOCK_FIELDS=$BLOCK_FIELDS*}
									{*START STEEL 2015???2???12?????????*}
									{assign var=EDIT_FIELD_NAMES value=['rejectiontype']}
									{if in_array($FIELD_MODEL->get('name'),$EDIT_FIELD_NAMES)}
										{$FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue'), $record, $RECORD)}
									{else}
										{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) BLOCK_FIELDS=$BLOCK_FIELDS}
									{/if}
								{else}
									{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) BLOCK_FIELDS=$BLOCK_FIELDS}
								{/if}
								{*END*}
							</span>
						</div>
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
	{*<table class="table table-bordered blockContainer showInlineTable  detailview-table Duplicates" data-num="yesreplace">*}
	{if $RECORD_ID > 0 }
		{foreach  key=key item=FIELD  from=$DETAIL_INFO_LIST  }
			<table class="table table-bordered blockContainer showInlineTable plusTableContent" data-num="yesreplace">
				<input  type="hidden" name="id[]"  value="{$FIELD['potentialdetailid']}"  />
				<input  type="hidden" name="pid[]"  value="{$FIELD['potentialid']}"  />
				<thead>
				<tr>
					<th class="blockHeader" colspan="4">
						<img class="cursorPointer alignMiddle blockToggle  hide" src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;"> <img class="cursorPointer alignMiddle blockToggle" src="layouts/vlayout/skins/softed/images/arrowDown.png"data-mode="show" data-id="141" style="display: inline;"> &nbsp;&nbsp;??????/??????????????????&nbsp;&nbsp;<spanclass="label label-success">{if $key>0}{$key}{else}{/if}</span><b class="pull-right"><button {if $key>0} class="btn btn-small delbutton  delDetailInfo" {else}class="btn btn-small delbutton  addfallinto"{/if} type="button"   data-potentialdetailid="{$FIELD['potentialdetailid']}"  data-id="yesreplace"><i  {if $key>0}  class="icon-trash" {else}  class="icon-plus"    {/if} title="????????????/??????????????????" ></i></button></b>
					</th>
				</tr>
				</thead>
				<tbody>
				<tr>
					<td class="fieldLabel medium">
						<label class="muted pull-right marginRight10px"> <span class="redColor">*</span>??????/??????????????????</label>
					</td>
					<td class="fieldValue medium">
						<input type="text" class="input-large" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="potentialnames[]" value="{$FIELD['potentialnames']}" data-cid="yesreplace"/>
						{*<input type="text" class="input-la	rge  checknumber" data-cid="yesreplace" name="mexchangerate[]" value="1.00" />*}
					</td>
					<td class="fieldLabel medium">
						<label class="muted pull-right marginRight10px"><span class="redColor">*</span>?????????</label>
					</td>
					<td class="fieldValue medium">
						<div class="row-fluid">
						   <span class="span10">
                              <div class="input-append">
								   <input  type="number" class="input-large checknumber" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  name="probabilitys[]" value="{$FIELD['probabilitys']}"  step="any"><span class="add-on">%</span>
							  </div>
						   </span>
						</div>
					</td>
				</tr>
				<tr>
					<td class="fieldLabel medium">
						<label class="muted pull-right marginRight10px"><span class="redColor">*</span>????????????</label>
					</td>
					<td class="fieldValue medium">
						<select class="chzn-select" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="salesstages[]"  data-cid="yesreplace">
							<option value="" selected="">??????????????????</option>
							<option value="??????????????????" {if $FIELD['salesstages']=="??????????????????"}selected{/if}>??????????????????</option>
							<option value="?????????????????????" {if $FIELD['salesstages']=="?????????????????????"}selected{/if}>?????????????????????</option>
							<option value="?????????????????????" {if $FIELD['salesstages']=="?????????????????????"}selected{/if}>?????????????????????</option>
							<option value="???????????????????????????" {if $FIELD['salesstages']=="???????????????????????????"}selected{/if}>???????????????????????????</option>
							<option value="?????????????????????" {if $FIELD['salesstages']=="?????????????????????"}selected{/if}>?????????????????????</option>
							<option value="?????????????????????" {if $FIELD['salesstages']=="?????????????????????"}selected{/if}>?????????????????????</option>
							<option value="??????????????????????????????" {if $FIELD['salesstages']=="??????????????????????????????"}selected{/if}>??????????????????????????????</option>
							<option value="?????????????????????" {if $FIELD['salesstages']=="?????????????????????"}selected{/if}>?????????????????????</option>
							<option value="???????????????" {if $FIELD['salesstages']=="???????????????"}selected{/if}>???????????????</option>
							<option value="?????????????????????" {if $FIELD['salesstages']=="?????????????????????"}selected{/if}>?????????????????????</option>
						</select>
					</td>
					<td class="fieldLabel medium">
						<label class="muted pull-right marginRight10px"><span class="redColor">*</span>????????????</label>
					</td>
					<td class="fieldValue medium">
						<select class="chzn-select" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="budgetinterval[]"  data-cid="yesreplace">
							<option value="" selected="">??????????????????</option>
							<option value="0-1???" {if $FIELD['budgetinterval']=="0-1???"}selected{/if} >0-1???</option>
							<option value="1-30???" {if $FIELD['budgetinterval']=="1-30???"}selected{/if}>1-30???</option>
							<option value="30-50???"{if $FIELD['budgetinterval']=="30-50???"}selected{/if}>30-50???</option>
							<option value="50-100???" {if $FIELD['budgetinterval']=="50-100???"}selected{/if}>50-100???</option>
							<option value="100?????????" {if $FIELD['budgetinterval']=="100?????????"}selected{/if}>100?????????</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="fieldLabel medium">
						<label class="muted pull-right marginRight10px">??????????????????</label>
					</td>
					<td class="fieldValue medium">
						{*<input type="text" class="input-large" name="budgetlocktime[]" value="" data-cid="yesreplace"/>*}
						{*<input type="text" class="input-la	rge  checknumber" data-cid="yesreplace" name="mexchangerate[]" value="1.00" />*}
						<div class="input-append row-fluid">
							<div class="span10 row-fluid date form_datetime">
							   <span class="add-on clearDate cursorPointer">
								<i id="RefillApplication_editView_fieldName_expcashadvances_clear" class="icon-remove-sign" title="??????"></i>
							   </span>
								<input  type="text" id="budgetlockstart{$key}"  name="budgetlockstart[]"   data-date-format="yyyy-mm-dd" readonly="" value="{$FIELD['budgetlockstart']}" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  autocomplete="off">
								<span class="add-on"><i class="icon-calendar"></i></span>
							</div>
						</div>
						<div class="input-append row-fluid">
							<div class="span10 row-fluid date form_datetime">
							   <span class="add-on clearDate cursorPointer">
								<i id="RefillApplication_editView_fieldName_expcashadvances_clear" class="icon-remove-sign" title="??????"></i>
							   </span>
								<input  type="text" id="budgetlockend{$key}"  name="budgetlockend[]"   data-date-format="yyyy-mm-dd" readonly="" value="{$FIELD['budgetlockend']}" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  autocomplete="off">
								<span class="add-on"><i class="icon-calendar"></i></span>
							</div>
						</div>
					</td>
					<td class="fieldLabel medium">
						<label class="muted pull-right marginRight10px">??????????????????</label>
					</td>
					<td class="fieldValue medium">
						<input  type="checkbox"  class="isannuallypay"  data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  {if $FIELD['isannuallypay']==1}checked{/if}  value="1"/><input  type="hidden"  name="isannuallypay[]" value="{$FIELD['isannuallypay']}"/>
					</td>
				</tr>
				<tr>
					<td class="fieldLabel medium">
						<label class="muted pull-right marginRight10px"><span class="redColor">*</span>????????????</label>
					</td>
					<td class="fieldValue medium">
						<select class="chzn-select" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="dockingrole[]"  data-cid="yesreplace">
							<option value="" >??????????????????</option>
							<option value="??????" {if $FIELD['dockingrole']=="??????"}selected{/if} >??????</option>
							<option value="?????????" {if $FIELD['dockingrole']=="?????????"}selected{/if}>?????????</option>
							<option value="?????????" {if $FIELD['dockingrole']=="?????????"}selected{/if}>?????????</option>
							<option value="???????????????" {if $FIELD['dockingrole']=="???????????????"}selected{/if}>???????????????</option>
							<option value="??????????????????" {if $FIELD['dockingrole']=="??????????????????"}selected{/if}>??????????????????</option>
							<option value="??????" {if $FIELD['dockingrole']=="??????"}selected{/if}>??????</option>
							<option value="?????????" {if $FIELD['dockingrole']=="?????????"}selected{/if}>?????????</option>
							<option value="??????" {if $FIELD['dockingrole']=="??????"}selected{/if}>??????</option>
							<option value="??????" {if $FIELD['dockingrole']=="??????"}selected{/if}>??????</option>
							<option value="??????" {if $FIELD['dockingrole']=="??????"}selected{/if}>??????</option>
							<option value="??????" {if $FIELD['dockingrole']=="??????"}selected{/if}>??????</option>
						</select>
					</td>
					<td class="fieldLabel medium">
						<label class="muted pull-right marginRight10px">?????????</label>
					</td>
					<td class="fieldValue medium">
						<input type="text" class="input-large" name="docker[]" value="{$FIELD['docker']}" data-cid="yesreplace"/>
					</td>
				</tr>
				<tr>
					<td class="fieldLabel medium">
						<label class="muted pull-right marginRight10px"><span class="redColor">*</span>??????????????????</label>
					</td>
					<td colspan="3" class="fieldValue medium">
						{*
                         <input class="span9 dateField form_datetime" id="BugFreeQuery_value1" name="BugFreeQuery[value1]" size="16" type="text" value="">
                        *}
						<textarea  style="width: 80%;" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="projectdetails[]">{$FIELD['projectdetails']}</textarea>
					</td>
				</tr>
				</tbody>
			</table>
		{/foreach}
	{else}
		<table class="table table-bordered  blockContainer showInlineTable plusTableContent" data-num="yesreplace">
			<input  type="hidden" name="id[]"  value="0"  />
			<input  type="hidden" name="pid[]"  value="0"  />
			<thead>
			<tr>
				<th class="blockHeader" colspan="4">
					<img class="cursorPointer alignMiddle blockToggle  hide" src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;"> <img class="cursorPointer alignMiddle blockToggle" src="layouts/vlayout/skins/softed/images/arrowDown.png"data-mode="show" data-id="141" style="display: inline;"> &nbsp;&nbsp;??????/??????????????????&nbsp;&nbsp;<spanclass="label label-success"></span><b class="pull-right"><button class="btn btn-small delbutton addfallinto" type="button"    data-id="yesreplace"><i class="icon-plus" title="????????????/??????????????????"></i></button></b>
				</th>
			</tr>
			</thead>
			<tbody>
			<tr>
				<td class="fieldLabel medium">
					<label class="muted pull-right marginRight10px"> <span class="redColor">*</span>??????/??????????????????</label>
				</td>
				<td class="fieldValue medium">
					<input type="text" class="input-large" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="potentialnames[]" value="" data-cid="yesreplace"/>
					{*<input type="text" class="input-la	rge  checknumber" data-cid="yesreplace" name="mexchangerate[]" value="1.00" />*}
				</td>
				<td class="fieldLabel medium">
					<label class="muted pull-right marginRight10px"><span class="redColor">*</span>?????????</label>
				</td>
				<td class="fieldValue medium">
					<div class="row-fluid">
						   <span class="span10">
                              <div class="input-append">
								   <input  type="number"  class="input-large checknumber"  data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  name="probabilitys[]" value=""  step="any"><span class="add-on">%</span>
							  </div>
						   </span>
					</div>
				</td>
			</tr>
			<tr>
				<td class="fieldLabel medium">
					<label class="muted pull-right marginRight10px"><span class="redColor">*</span>????????????</label>
				</td>
				<td class="fieldValue medium">
					<select class="chzn-select" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="salesstages[]"  data-cid="yesreplace">
						<option value="" selected="">??????????????????</option>
						<option value="??????????????????">??????????????????</option>
						<option value="?????????????????????">?????????????????????</option>
						<option value="?????????????????????">?????????????????????</option>
						<option value="???????????????????????????">???????????????????????????</option>
						<option value="?????????????????????">?????????????????????</option>
						<option value="?????????????????????">?????????????????????</option>
						<option value="??????????????????????????????">??????????????????????????????</option>
						<option value="?????????????????????">?????????????????????</option>
						<option value="???????????????">???????????????</option>
						<option value="?????????????????????">?????????????????????</option>
					</select>
				</td>
				<td class="fieldLabel medium">
					<label class="muted pull-right marginRight10px"><span class="redColor">*</span>????????????</label>
				</td>
				<td class="fieldValue medium">
					<select class="chzn-select" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="budgetinterval[]"  data-cid="yesreplace">
						<option value="" selected="">??????????????????</option>
						<option value="0-1???">0-1???</option>
						<option value="1-30???">1-30???</option>
						<option value="30-50???">30-50???</option>
						<option value="50-100???">50-100???</option>
						<option value="100?????????">100?????????</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="fieldLabel medium">
					<label class="muted pull-right marginRight10px">??????????????????</label>
				</td>
				<td class="fieldValue medium">
					{*<input type="text" class="input-large" name="budgetlocktime[]" value="" data-cid="yesreplace"/>*}
					{*<input type="text" class="input-la	rge  checknumber" data-cid="yesreplace" name="mexchangerate[]" value="1.00" />*}
					<div class="input-append row-fluid">
						<div class="span10 row-fluid date form_datetime">
							   <span class="add-on clearDate cursorPointer">
								<i id="RefillApplication_editView_fieldName_expcashadvances_clear" class="icon-remove-sign" title="??????"></i>
							   </span>
							<input  type="text" id="budgetlockstart0"  name="budgetlockstart[]"   data-date-format="yyyy-mm-dd" readonly="" value="" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  autocomplete="off">
							<span class="add-on"><i class="icon-calendar"></i></span>
						</div>
					</div>

					<div class="input-append row-fluid">
						<div class="span10 row-fluid date form_datetime">
							   <span class="add-on clearDate cursorPointer">
								<i id="RefillApplication_editView_fieldName_expcashadvances_clear" class="icon-remove-sign" title="??????"></i>
							   </span>
							<input  type="text" id="budgetlockend0"  name="budgetlockend[]"   data-date-format="yyyy-mm-dd" readonly="" value="" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  autocomplete="off">
							<span class="add-on"><i class="icon-calendar"></i></span>
						</div>
					</div>
				</td>
				<td class="fieldLabel medium">
					<label class="muted pull-right marginRight10px">??????????????????</label>
				</td>
				<td class="fieldValue medium">
					<input  type="checkbox"  class="isannuallypay"  data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  value="1"/><input  type="hidden"  name="isannuallypay[]" value="0"/>
				</td>
			</tr>
			<tr>
				<td class="fieldLabel medium">
					<label class="muted pull-right marginRight10px"><span class="redColor">*</span>????????????</label>
				</td>
				<td class="fieldValue medium">
					<select class="chzn-select" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="dockingrole[]"  data-cid="yesreplace">
						<option value="" >??????????????????</option>
						<option value="??????" >??????</option>
						<option value="?????????">?????????</option>
						<option value="?????????">?????????</option>
						<option value="???????????????">???????????????</option>
						<option value="??????????????????">??????????????????</option>
						<option value="??????">??????</option>
						<option value="?????????">?????????</option>
						<option value="??????">??????</option>
						<option value="??????">??????</option>
						<option value="??????">??????</option>
						<option value="??????">??????</option>
					</select>
				</td>
				<td class="fieldLabel medium">
					<label class="muted pull-right marginRight10px">?????????</label>
				</td>
				<td class="fieldValue medium">
					<input type="text" class="input-large" name="docker[]" value="" data-cid="yesreplace"/>
				</td>
			</tr>
			<tr>
				<td class="fieldLabel medium">
					<label class="muted pull-right marginRight10px"><span class="redColor">*</span>??????????????????</label>
				</td>
				<td colspan="3" class="fieldValue medium">
					{*
                                      <input class="span9 dateField form_datetime" id="BugFreeQuery_value1" name="BugFreeQuery[value1]" size="16" type="text" value="">
                    *}
					<textarea style="width: 80%;" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="projectdetails[]"> </textarea>
				</td>
			</tr>
			</tbody>
		</table>
	{/if}
	<div id="insertbefore"></div>
	<input type="hidden" id="detatilNumber" {if $COUNTD_DETAIL>0} value="{$COUNTD_DETAIL-1}"{else}value="0"{/if}/>
{literal}
	<script>
        var appendDetailInfo ='<table class="table table-bordered blockContainer showInlineTable plusTableContent" data-num="yesreplace"><input  type="hidden" name="id[]"  value="0"  />' +
            '<input  type="hidden" name="pid[]"  value="0"  />'+
            '<thead>'+
            '<tr>'+
            '<th class="blockHeader" colspan="4">'+
            '<img class="cursorPointer alignMiddle blockToggle  hide" src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;"> <img class="cursorPointer alignMiddle blockToggle" src="layouts/vlayout/skins/softed/images/arrowDown.png"data-mode="show" data-id="141" style="display: inline;"> &nbsp;&nbsp;??????/??????????????????&nbsp;&nbsp;<spanclass="label label-success">detailNumbers</span><b class="pull-right"><button class="btn btn-small delbutton addfallinto" type="button" data-potentialdetailid="0" data-id="yesreplace"><i class="icon-plus" title="????????????/??????????????????"></i></button></b>'+
            '</th>'+
            '</tr>'+
            '</thead>'+
            '<tbody>'+
            '<tr>'+
            '<td class="fieldLabel medium">'+
            '<label class="muted pull-right marginRight10px"> <span class="redColor">*</span>??????/??????????????????</label>'+
            '</td>'+
            '<td class="fieldValue medium">'+
            '<input type="text" class="input-large" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="potentialnames[]" value="" data-cid="yesreplace"/>'+
            '</td>'+
            '<td class="fieldLabel medium">'+
            '<label class="muted pull-right marginRight10px"><span class="redColor">*</span>?????????</label>'+
            '</td>'+
            '<td class="fieldValue medium">'+
            '<div class="row-fluid">'+
            '<span class="span10">'+
            '<div class="input-append">'+
            '<input  type="number" class="input-large checknumber" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  name="probabilitys[]" value=""  step="any"><span class="add-on">%</span>'+
            '</div>'+
            '</span>'+
            '</div>'+
            '</td>'+
            '</tr>'+
            '<tr>'+
            '<td class="fieldLabel medium">'+
            '<label class="muted pull-right marginRight10px"><span class="redColor">*</span>????????????</label>'+
            '</td>'+
            '<td class="fieldValue medium">'+
            '<select class="chzn-select" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="salesstages[]"  data-cid="yesreplace">'+
            '<option value="" selected="">??????????????????</option>'+
            '<option value="??????????????????">??????????????????</option>'+
            '<option value="?????????????????????">?????????????????????</option>'+
            '<option value="?????????????????????">?????????????????????</option>'+
            '<option value="???????????????????????????">???????????????????????????</option>' +
            '<option value="?????????????????????">?????????????????????</option>' +
            '<option value="?????????????????????">?????????????????????</option>'+
            '<option value="??????????????????????????????">??????????????????????????????</option>'+
            '<option value="?????????????????????">?????????????????????</option>' +
            '<option value="???????????????">???????????????</option>' +
            '<option value="?????????????????????">?????????????????????</option>' +
            '</select>'+
            '</td>'+
            '<td class="fieldLabel medium">'+
            '<label class="muted pull-right marginRight10px"><span class="redColor">*</span>????????????</label>'+
            '</td>'+
            '<td class="fieldValue medium">'+
            '<select class="chzn-select" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="budgetinterval[]"  data-cid="yesreplace">'+
            '<option value="" selected="">??????????????????</option>'+
            '<option value="0-1???">0-1???</option>'+
            '<option value="1-30???">1-30???</option>'+
            '<option value="30-50???">30-50???</option>'+
            '<option value="50-100???">50-100???</option>'+
            '<option value="100?????????">100?????????</option>'+
            ' </select>'+
            '</td>'+
            '</tr>'+
            '<tr>'+
            '<td class="fieldLabel medium">'+
            '<label class="muted pull-right marginRight10px">??????????????????</label>'+
            '</td>'+
            '<td class="fieldValue medium">'+
            ' <div class="input-append row-fluid">' +
            '<div class="span10 row-fluid date form_datetime">' +
            '<span class="add-on clearDate cursorPointer">' +
            '<i id="RefillApplication_editView_fieldName_expcashadvances_clear" class="icon-remove-sign" title="??????"></i>' +
            '</span>' +
            '<input  type="text"  id="budgetlockstart" class="dateField" name="budgetlockstart[]" readonly="" value="" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  autocomplete="off">' +
            '<span class="add-on"><i class="icon-calendar"></i></span>' +
            '</div>' +
            '</div>' +
            '<div class="input-append row-fluid">' +
            '<div class="span10 row-fluid date form_datetime">' +
            '<span class="add-on clearDate cursorPointer">' +
            '<i id="RefillApplication_editView_fieldName_expcashadvances_clear" class="icon-remove-sign" title="??????"></i>' +
            '</span>' +
            '<input  type="text" id="budgetlockend" class="dateField" name="budgetlockend[]"   data-date-format="yyyy-mm-dd" readonly="" value="" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  autocomplete="off">' +
            '<span class="add-on"><i class="icon-calendar"></i></span>' +
            '</div>' +
            '</div>'+
            '</td>'+
            '<td class="fieldLabel medium">'+
            '<label class="muted pull-right marginRight10px">??????????????????</label>'+
            '</td>'+
            ' <td class="fieldValue medium">'+
            ' <input  type="checkbox" class="isannuallypay" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  value="1"/><input  type="hidden"  name="isannuallypay[]" value="0"/>'+
            ' </td>'+
            ' </tr>'+
            '<tr>'+
            '<td class="fieldLabel medium">'+
            '<label class="muted pull-right marginRight10px"><span class="redColor">*</span>????????????</label>'+
            '</td>'+
            '<td class="fieldValue medium">'+
            '<select class="chzn-select" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="dockingrole[]"  data-cid="yesreplace">'+
            '<option value="" >??????????????????</option>'+
            '<option value="??????" >??????</option>'+
            '<option value="?????????">?????????</option>'+
            '<option value="?????????">?????????</option>'+
            '<option value="???????????????">???????????????</option>'+
            '<option value="??????????????????">??????????????????</option>'+
            '<option value="??????">??????</option>'+
            '<option value="?????????">?????????</option>'+
            '<option value="??????">??????</option>'+
            '<option value="??????">??????</option>'+
            '<option value="??????">??????</option>'+
            '<option value="??????">??????</option>'+
            '</select>'+
            '</td>'+
            '<td class="fieldLabel medium">'+
            '<label class="muted pull-right marginRight10px">?????????</label>'+
            '</td>'+
            ' <td class="fieldValue medium">'+
            '<input type="text" class="input-large" name="docker[]" value="" data-cid="yesreplace"/>'+
            '</td>'+
            ' </tr>'+
            '<tr>'+
            '<td class="fieldLabel medium">'+
            '<label class="muted pull-right marginRight10px"><span class="redColor">*</span>??????????????????</label>'+
            '</td>'+
            '<td colspan="3" class="fieldValue medium">'+
            '<textarea style="width: 80%;" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="projectdetails[]"> </textarea>'+
            '</td>'+
            '</tr>'+
            '</tbody>'+
            '</table>';
	</script>
{/literal}


{/strip}