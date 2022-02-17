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
	<div class='editViewContainer container-fluid'>

	<form class="form-horizontal recordEditView" id="EditView" name="EditView" method="post"  action="index.php" enctype="multipart/form-data">
	{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
	{if !empty($PICKIST_DEPENDENCY_DATASOURCE)}
		<input type="hidden" name="picklistDependency" value='{Vtiger_Util_Helper::toSafeHTML($PICKIST_DEPENDENCY_DATASOURCE)}' />
	{/if}
	<input type="hidden" name="module" value="{$MODULE}" />
	<input type="hidden" name="supprebate" value="{$RECORD->get('supprebate')}" />
	<input type="hidden" name="rechargesource" value="{$RECHARGESOURCE}" />
	<input type="hidden" name="occupationamount" value="0" />
	<input type="hidden" name="action" value="Save" />
	<input type="hidden" name="record" value="{$RECORD_ID}" />
	<input type="hidden" name="srcterminal" value="2" />
	<input type="hidden" name="isautoclose" value="" />
	<input type="hidden" id="whichContract"  value="0" />
	{if $IS_RELATION_OPERATION }
		<input type="hidden" name="sourceModule" value="{$SOURCE_MODULE}" />
		<input type="hidden" name="sourceRecord" value="{$SOURCE_RECORD}" />
		<input type="hidden" name="relationOperation" value="{$IS_RELATION_OPERATION}" />
	{/if}
	<div class="contentHeader row-fluid">
		{assign var=SINGLE_MODULE_NAME value='SINGLE_'|cat:$MODULE}
		{if $RECORD_ID neq ''}
			<h3 title="{vtranslate('LBL_EDITING', $MODULE)} {vtranslate($RECHARGESOURCE, $MODULE)}{*vtranslate($SINGLE_MODULE_NAME, $MODULE)*} {$RECORD_STRUCTURE_MODEL->getRecordName()}">{vtranslate('LBL_EDITING', $MODULE)} {*vtranslate($SINGLE_MODULE_NAME, $MODULE)*}{vtranslate($RECHARGESOURCE, $MODULE)} - {$RECORD_STRUCTURE_MODEL->getRecordName()}</h3>
			<hr>
		{else}
			<h3>{vtranslate('LBL_CREATING_NEW', $MODULE)} {vtranslate($RECHARGESOURCE, $MODULE)}{*vtranslate($SINGLE_MODULE_NAME, $MODULE)*}</h3>
			<hr>
		{/if}
		<span class="pull-right">
				<button class="btn btn-success" type="submit"><strong>{vtranslate('LBL_SAVE', $MODULE)}</strong></button>
				<a class="cancelLink" type="reset" onclick="javascript:window.history.back();">{vtranslate('LBL_CANCEL', $MODULE)}</a>
		</span>
	</div>
	{assign var=CONTRACTCHANGES value=array('oldrechargesource','contractamountrecharged','actualtotalrecharge','grossadvances','totalreceivables','remarks','changecontracttype','changesnumber','newcustomertype','newaccountid','newiscontracted','newservicesigndate','newcontractamount','newcontractsid')}
	{assign var=CONTRACTCHANGES_OLD value=array('servicecontractsid','accountid','customertype','contractamount','iscontracted','servicesigndate')}

	{foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE name="EditViewBlockLevelLoop"}
		{if $BLOCK_LABEL eq 'VENDOR_LBL_INFO'}{continue}{/if}
		{if $BLOCK_FIELDS|@count lte 0}{continue}{/if}
		{*{if $BLOCK_LABEL eq 'LBL_INFO' && ($RECHARGESOURCE eq 'contractChanges')}{else}{continue}{/if}*}
		<table class="table table-bordered blockContainer showInlineTable detailview-table {$BLOCK_LABEL}"{if $BLOCK_LABEL eq 'VENDOR_LBL_INFO' && $RECHARGESOURCE eq 'INCREASE' && $RECORD->get('granttype') eq 'virtrefund'} style="display:none;"{/if}>
			<thead>
			<tr>
				<th class="blockHeader" colspan="4">
					<img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;"><img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">&nbsp;&nbsp;
					{if $BLOCK_LABEL eq 'LBL_CUSTOM_INFORMATION'}
						目标合同信息
					{else}
						{vtranslate($BLOCK_LABEL, $MODULE)}
					{/if}
				</th>
			</tr>
			</thead>
			<tbody>
			<tr>
				{assign var=COUNTER value=0}
				{foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}
				{if !in_array($FIELD_MODEL->getFieldName(),$CONTRACTCHANGES) && $RECHARGESOURCE eq 'contractChanges'}{continue}{/if}
				{assign var="isReferenceField" value=$FIELD_MODEL->getFieldDataType()}
				{if $FIELD_MODEL->get('uitype') eq "20" or $FIELD_MODEL->get('uitype') eq "19"}
				{if $COUNTER eq '1'}
				<td class="{$WIDTHTYPE}"></td><td class="{$WIDTH_TYPE_CLASSSES[$WIDTHTYPE]}"></td></tr><tr>
				{assign var=COUNTER value=0}
				{/if}
				{/if}
				{if $COUNTER eq 2}
			</tr><tr>
				{assign var=COUNTER value=1}
				{else}
				{assign var=COUNTER value=$COUNTER+1}
				{/if}
				<td class="fieldLabel {$WIDTHTYPE}" title="{$FIELD_MODEL->get('prompt')}">

					{if $FIELD_MODEL->get('name') eq 'newcustomertype' && $changecontracttype eq 'SupplierContracts'}<div class="blockOrNone" style="display: none;" >{elseif $FIELD_MODEL->get('name') eq 'newcustomertype'}<div class="blockOrNone"  >{/if}
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
												<select class="chzn-select referenceModulesList streched" style="width:140px;">
													<optgroup>
														{foreach key=index item=value from=$REFERENCE_LIST}
															<option value="{$value}" {if $value eq $REFERENCED_MODULE_NAME} selected {/if}>{vtranslate($value, $MODULE)}</option>
														{/foreach}
													</optgroup>
												</select>
											</span>
								{else}
									<label class="muted pull-right marginRight10px">{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}{vtranslate($FIELD_MODEL->get('label'), $MODULE)}{if $FIELD_MODEL->get('prompt') neq ''}<span class="icon-question-sign"></span></label>{/if}
								{/if}
							{else if $FIELD_MODEL->get('uitype') eq "83"}
								{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) COUNTER=$COUNTER}
							{else}
								{vtranslate($FIELD_MODEL->get('label'), $MODULE)}{if $FIELD_MODEL->get('prompt') neq ''}<span class="icon-question-sign"></span>{/if}
							{/if}
							{if $isReferenceField neq "reference"}</label>{/if}
					{if $FIELD_MODEL->get('name') eq 'newcustomertype'}</div>{/if}
				</td>
				{if $FIELD_MODEL->get('uitype') neq "83"}
					<td class="fieldValue {$WIDTHTYPE}" {if $FIELD_MODEL->get('uitype') eq '19'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if} {if $FIELD_MODEL->get('uitype') eq '20'} colspan="3"{/if}>
						{if $FIELD_MODEL->get('name') eq 'newcustomertype'}<div class="blockOrNone">{/if}
					        {if in_array($FIELD_MODEL->get('name'),array('contractamountrecharged','changesnumber','actualtotalrecharge','grossadvances','totalreceivables','newservicesigndate','newcontractamount'))}
								<input type="text" class="input-large" {if $FIELD_MODEL->get('name') eq 'newservicesigndate'}{else}data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"{/if} name="{$FIELD_MODEL->get('name')}" data-cid="yesreplace" value="{vtranslate($FIELD_MODEL->get('fieldvalue'),'RefillApplication')}"  readonly="readonly"  />
							{elseif $FIELD_MODEL->get('name') eq 'newaccountid'}
								{assign var="displayId" value=$FIELD_MODEL->get('fieldvalue')}
								<input type="hidden"  class="input-large"  name="{$FIELD_MODEL->get('name')}" data-cid="yesreplace" value="{$FIELD_MODEL->get('fieldvalue')}"  data-displayvalue='{$FIELD_MODEL->getEditViewDisplayValue($FIELD_MODEL->get('fieldvalue'))}'  />
								<input id="newaccountid_display" name="newaccountid_display" type="text"  value="{$newaccount_name}" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"   readonly="readonly" autocomplete="off">
							{elseif $FIELD_MODEL->get('name') eq 'oldrechargesource'}
								<select class="chzn-select" name="{$FIELD_MODEL->getFieldName()}"  data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  >
									<option value="">选择一个选项</option>
									<option value="Accounts" {if $FIELD_MODEL->get('fieldvalue') eq 'Accounts'}selected{/if } >媒体充值</option>
									<option value="COINRETURN" {if $FIELD_MODEL->get('fieldvalue') eq 'COINRETURN'}selected{/if }>退币转充</option>
									<option value="Vendors" {if $FIELD_MODEL->get('fieldvalue') eq 'Vendors'}selected{/if }>媒体充值(外采)</option>
									<option value="NonMediaExtraction" {if $FIELD_MODEL->get('fieldvalue') eq 'NonMediaExtraction'}selected{/if }>非媒体类外采</option>
									<option value="PreRecharge" {if $FIELD_MODEL->get('fieldvalue') eq 'PreRecharge'}selected{/if }>预充值</option>
									<option value="TECHPROCUREMENT" {if $FIELD_MODEL->get('fieldvalue') eq 'TECHPROCUREMENT'}selected{/if }>工单外采</option>
								</select>
							{elseif $FIELD_MODEL->get('name') eq 'newcustomertype' && $changecontracttype eq 'SupplierContracts'}
							{else}
							{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) BLOCK_FIELDS=$BLOCK_FIELDS}
							{/if}
					    {if $FIELD_MODEL->get('name') eq 'newcustomertype'}</div>{/if}
					</td>
				{/if}
				{if $BLOCK_FIELDS|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype"}
					<td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
				{/if}
				{/foreach}
			</tr>
			</tbody>
		</table>
		<br>
		{*如果当前lbl是基本信息那么就执行在执行下 block 循环 从基本信息中把原合同信息显示出来 其他的字段不显示*}
		{if $BLOCK_LABEL eq 'LBL_INFO'}
			{foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE name="EditViewBlockLevelLoop"}
				{if $BLOCK_LABEL neq 'LBL_INFO'}{continue}{/if}
				{if $BLOCK_FIELDS|@count lte 0}{continue}{/if}
				<table class="table table-bordered blockContainer showInlineTable detailview-table {$BLOCK_LABEL}"{if $BLOCK_LABEL eq 'VENDOR_LBL_INFO' && $RECHARGESOURCE eq 'INCREASE' && $RECORD->get('granttype') eq 'virtrefund'} style="display:none;"{/if}>
					<thead>
					<tr>
						<th class="blockHeader" colspan="4">
							<img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;"><img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">&nbsp;&nbsp;
							原合同信息
						</th>
					</tr>
					</thead>
					<tbody>
					<tr>
						{assign var=COUNTER value=0}
						{foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}
						{if !in_array($FIELD_MODEL->getFieldName(),$CONTRACTCHANGES_OLD) }{continue}{/if}
						{assign var="isReferenceField" value=$FIELD_MODEL->getFieldDataType()}
						{if $FIELD_MODEL->get('uitype') eq "20" or $FIELD_MODEL->get('uitype') eq "19"}
						{if $COUNTER eq '1'}
						<td class="{$WIDTHTYPE}"></td><td class="{$WIDTH_TYPE_CLASSSES[$WIDTHTYPE]}"></td></tr><tr>
						{assign var=COUNTER value=0}
						{/if}
						{/if}
						{if $COUNTER eq 2}
					</tr><tr>
						{assign var=COUNTER value=1}
						{else}
						{assign var=COUNTER value=$COUNTER+1}
						{/if}
						<td class="fieldLabel {$WIDTHTYPE}" title="{$FIELD_MODEL->get('prompt')}">
							{if $FIELD_MODEL->get('name') eq 'customertype' && $changecontracttype eq 'SupplierContracts'}<div class="blockOrNone" style="display: none;">{elseif $FIELD_MODEL->get('name') eq 'customertype'}<div class="blockOrNone">{/if}
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
													<select class="chzn-select referenceModulesList streched" style="width:140px;">
														<optgroup>
															{foreach key=index item=value from=$REFERENCE_LIST}
																<option value="{$value}" {if $value eq $REFERENCED_MODULE_NAME} selected {/if}>{vtranslate($value, $MODULE)}</option>
															{/foreach}
														</optgroup>
													</select>
												</span>
											{else}
												<label class="muted pull-right marginRight10px">{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}{vtranslate($FIELD_MODEL->get('label'), $MODULE)}{if $FIELD_MODEL->get('prompt') neq ''}<span class="icon-question-sign"></span></label>{/if}
											{/if}
										{else if $FIELD_MODEL->get('uitype') eq "83"}
											{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) COUNTER=$COUNTER}
										{else}
											{vtranslate($FIELD_MODEL->get('label'), $MODULE)}{if $FIELD_MODEL->get('prompt') neq ''}<span class="icon-question-sign"></span>{/if}
										{/if}
										{if $isReferenceField neq "reference"}</label>{/if}
								{if $FIELD_MODEL->get('name') eq 'customertype'}</div>{/if}
						</td>
						{if $FIELD_MODEL->get('uitype') neq "83"}
							<td class="fieldValue {$WIDTHTYPE}" {if $FIELD_MODEL->get('uitype') eq '19'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if} {if $FIELD_MODEL->get('uitype') eq '20'} colspan="3"{/if}>
								{if $FIELD_MODEL->get('name') eq 'customertype'}<div class="blockOrNone">{/if}
									{if in_array($FIELD_MODEL->get('name'),array('servicesigndate','contractamount'))}
										<input type="text" class="input-large" {if $FIELD_MODEL->get('name') eq 'servicesigndate'}{else} data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" {/if} name="{$FIELD_MODEL->get('name')}" data-cid="yesreplace" value="{$FIELD_MODEL->get('fieldvalue')}"  readonly="readonly"  />
									{elseif $FIELD_MODEL->get('name') eq 'accountid'}
										{assign var="displayId" value=$FIELD_MODEL->get('fieldvalue')}
										<input type="hidden"  class="input-large"  name="{$FIELD_MODEL->get('name')}" data-cid="yesreplace" value="{$FIELD_MODEL->get('fieldvalue')}"   />
										<input id="accountid_display" name="accountid_display" type="text"  value="{$account_name}" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"   readonly="readonly" autocomplete="off">
									{elseif $FIELD_MODEL->get('name') eq 'customertype' && $changecontracttype eq 'SupplierContracts'}
									{else}
										{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) BLOCK_FIELDS=$BLOCK_FIELDS}
									{/if}
							    {if $FIELD_MODEL->get('name') eq 'customertype'}</div>{/if}
							</td>
						{/if}
						{if $BLOCK_FIELDS|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype"}
							<td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
						{/if}
						{/foreach}
					</tr>
					</tbody>
				</table>
				<br>
				{if $BLOCK_LABEL eq 'LBL_INFO'}

				{/if}
			{/foreach}
		{/if}
	{/foreach}
	<br/>
	<table class="table table-bordered blockContainer showInlineTable  detailview-table" id = "fallintotable">
		<thead>
		<tr>
			<th class="blockHeader" colspan="8" >
				充值单信息
			</th>
		</tr>
		</thead>
		<tbody  id="refillApplicationList">
		<tr>
			<td><b><input type="checkbox" {if $REFILLAPPLICATION_LIST } checked="checked" {/if} class="contractChange" name="DetaPayments"  title="合同变更申请" />全选</b></td>
			<td><b>申请单编号</b></td>
			<td><b>充值来源</b></td>
			<td><b>申请人</b></td>
			<td><b>申请时间</b></td>
			<td><b>合计垫款金额</b></td>
			<td><b>应收款总额</b></td>
			<td><b>应付款总额</b></td>
		</tr>
		{if $REFILLAPPLICATION_LIST}
			{foreach from=$REFILLAPPLICATION_LIST item="item" key="key" }
                    <tr {if $item['error'] eq 1 }style="color: red;"{/if}>
                        <td><input type="checkbox" checked="checked" data-error="{$item['error']}" value="{$item['refillapplicationid']}"  data-grossadvances="{$item['grossadvances']}" data-actualtotalrecharge="{$item['actualtotalrecharge']}" data-totalreceivables="{$item['totalreceivables']}" class="entryCheckBox"  name="contractChangeApplication[]" title="合同变更申请"></td>
                        <td>{$item['refillapplicationno']}</td>
                        <td>{vtranslate($item['rechargesource'])}</td>
                        <td>{$item['smownerid']}</td>
                        <td>{$item['createdtime']}</td>
                        <td>{$item['grossadvances']}</td>
                        <td>{$item['actualtotalrecharge']}</td>
                        <td>{$item['totalreceivables']}</td>
                    </tr>
            {/foreach}
		{/if}

		</tbody>
	</table>
{/strip}