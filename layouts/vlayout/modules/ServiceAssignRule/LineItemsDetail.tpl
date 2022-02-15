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
{*<table id="tbl_ServiceAssignRule_Service_Detail" class="table table-bordered blockContainer showInlineTable">
    <tr>
	<th colspan="4">
	    <b>客服转移</b>
	</th>
    </tr>
    <tr>
    <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">原客服</label></td>
    <td class="fieldValue medium">
    	<div class="row-fluid">
		<span class="span10">
			{assign var=ALL_ACTIVEUSER_LIST value=$USER_MODEL->getAccessibleUsers()}
			{assign var=ASSIGNED_USER_ID value='oldserviceid'}
		    {assign var=CURRENT_USER_ID value=$USER_MODEL->get('id')}
			{assign var=FIELD_VALUE value=$FIELD_MODEL->get('fieldvalue')}
			{if $FIELD_VALUE eq ''}
				{assign var=FIELD_VALUE value=$CURRENT_USER_ID}
			{/if}
			<select class="chzn-select {$ASSIGNED_USER_ID}" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-name="{$ASSIGNED_USER_ID}" name="{$ASSIGNED_USER_ID}" data-fieldinfo='{$FIELD_INFO}' {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if}>
				<optgroup label="{vtranslate('LBL_USERS')}">
                    <option value="" data-picklistvalue= '' data-userId="0" selected>请选择客服 </option>
					{foreach key=OWNER_ID item=OWNER_NAME from=$ALL_ACTIVEUSER_LIST}
		                    <option value="{$OWNER_ID}" data-picklistvalue= '{$OWNER_NAME}'
								
								data-userId="{$CURRENT_USER_ID}">
		                    {$OWNER_NAME}
		                    </option>
					{/foreach}
				</optgroup>
				 
			</select>
    	</span>
		</div>
    </td>
    <td class="fieldLabel medium"><label class="muted pull-right marginRight10px"></label></td>
    <td class="fieldValue medium"></td>
    </tr>
</table>*}
		<table style="width:100%;text-align:right;">
	    <tr>
	    <td style="text-align:left;">
	    {*<font color=red><div class="divserviceinfo">客服：,可分配：个客户,已分配：个客户</div></font>*}
	    </td>
	    <td>
	    <input type="radio" name="list" id="allAssignRadio" value="1"/>全部分配
	    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	    <input type="radio" name="list" checked id="selectAssignRadio" value="0"/>选择分配
	    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	    <input type="checkbox" checked id="notAssignCheckBox" />不包含已分配客服客户
	    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	 	<button id="btnGetAccounts" type="button" class="btn btn-primary">获取客户</button>
	 	&nbsp;&nbsp;
	 	<button id="btnAssign" type="button" class="btn btn-primary">分配客服</button>
	 	
	    </td></tr>
	    </table>
<div class="msg"></div>
<div  style="margin:0;width:100%; " id="div_account_detail">

<table id="tbl_ServiceAssignRule_Account_Detail" class="table listViewEntriesTable" >
    <thead>
    <tr>
	<th class="{$WIDTHTYPE}">
		<input type="checkbox" id="listViewEntriesMainCheckBox" />
	</th>
	<th>
	    <b>客户</b>
	</th>
	<th>
	    <b>客户等级</b>
	</th>
	<th>
	    <b>部门</b>
	</th>
    <th>
	    <b>负责人</b>
	</th>
	<th>
	    <b>客服</b>
	</th>
    </tr></thead>
    <tbody>	
    
    </tbody>	
</table>
</div>

