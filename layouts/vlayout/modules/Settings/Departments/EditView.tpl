{*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************}
{strip}
<div class="container-fluid">

<label class="themeTextColor font-x-x-large">{vtranslate($MODULE, $QUALIFIED_MODULE)}</label>
		<hr>
	<form name="EditRole" action="index.php" method="post" id="EditView" class="form-horizontal">
			<input type="hidden" name="module" value="Departments">
			<input type="hidden" name="action" value="Save">
			<input type="hidden" name="parent" value="Settings">
			{assign var=RECORD_ID value=$RECORD_MODEL->getId()}
			<input type="hidden" name="record" value="{$RECORD_ID}" />
			<input type="hidden" name="mode" value="{$MODE}">
			
			{assign var=HAS_PARENT value="{if $RECORD_MODEL->getParent()}true{/if}"}
			{if $HAS_PARENT}
				<input type="hidden" name="parent_departmentid" value="{$RECORD_MODEL->getParent()->getId()}">
			{/if}
		
		<div class="row-fluid">
				<div class="row-fluid">
					<label class="fieldLabel span3"><strong>{vtranslate('LBL_NAME', $QUALIFIED_MODULE)}<span class="redColor">*</span>: </strong></label>
					<input type="text" class="fieldValue span6" name="rolename" id="profilename" value="{$RECORD_MODEL->getName()}" data-validation-engine='validate[required]'  />
				</div><br>
				<div class="row-fluid">
					<label class="fieldLabel span3"><strong>{vtranslate('LBL_DEPARTMENT_CODE', $QUALIFIED_MODULE)}: </strong></label>
					<input type="text" class="fieldValue span6" name="code" id="profilecode" value="{$RECORD_MODEL->getCode()}"  />
				</div><br>
				<div class="row-fluid">
					<label class="fieldLabel span3"><strong>{vtranslate('LBL_ISJURIDICALPERSON', $QUALIFIED_MODULE)}: </strong></label>
					<select class="chzn-select fieldValue span6" name="isjuridicalperson" id="profileisjuridicalperson">
						<option value="0" {if $RECORD_MODEL->getIsjuridicalPerson() eq 0}selected{/if}>否  </option>
						<option value="1" {if $RECORD_MODEL->getIsjuridicalPerson() eq 1}selected{/if}>是  </option>
					</select>
				</div><br>
				<div class="row-fluid">
					<label class="fieldLabel span3"><strong>{vtranslate('LBL_ERPACCOUNT', $QUALIFIED_MODULE)}: </strong></label>
					<input type="text" class="fieldValue span6" name="erpaccount" id="profilecode" value="{$RECORD_MODEL->getErpAccount()}"  />
    				{*{assign var=ERPACCOUT value=$RECORD_MODEL->getErpAccount()}
					<select class="chzn-select fieldValue span6" id="profileerpaccount" name="erpaccount" >
						<option value="">请选择</option>
                        {foreach key=USERID item=USERNAME from=$RECORD_MODEL->getUserDepartmentInfo()}
							<option value="{$USERID}" {if $ERPACCOUT eq $USERID} selected {/if}>{$USERNAME}</option>
						{/foreach}
					</select>*}
				</div><br>
				<div class="row-fluid">
					<label class="fieldLabel span3"><strong>{vtranslate('LBL_REPORTS_TO', $QUALIFIED_MODULE)}: </strong></label>
					<div class="span8 fieldValue">
						<input type="hidden" name="parent_departmentid" {if $HAS_PARENT}value="{$RECORD_MODEL->getParent()->getId()}"{/if}>
						<input type="text" class="input-large" name="parent_deparmentid_display" {if $HAS_PARENT}value="{$RECORD_MODEL->getParent()->getName()}"{/if} readonly>
					</div>
				</div><br>
				<div class="row-fluid">
					<label class="fieldLabel span3"><strong>部门负责人<span class="redColor">*</span>: </strong></label>
					{assign var=ERPPEOPLEID value=$RECORD_MODEL->getPeopleID()}
					<select class="chzn-select fieldValue span6" id="peopleid" name="peopleid"  data-validation-engine='validate[required]' >
						<option value="">请选择</option>
						{foreach key=USERID item=USERNAME from=$RECORD_MODEL->getUserDepartmentInfo()}
							<option value="{$USERID}" {if $ERPPEOPLEID eq $USERID} selected {/if}>{$USERNAME}</option>
						{/foreach}
					</select>
				</div><br>
               
				
				
				
                
			</div><div class="pull-right">
				<button class="btn btn-success" type="submit">{vtranslate('LBL_SAVE',$MODULE)}</button>
				<a class="cancelLink" onclick="javascript:window.history.back();" type="reset">Cancel</a>
			</div>
	</form>
</div>
{/strip}