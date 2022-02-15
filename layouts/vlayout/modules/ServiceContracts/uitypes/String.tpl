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
{assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($FIELD_MODEL->getFieldInfo()))}
{assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
{assign var="FIEDLONLY" value=['elereceiver','originator','','originatormobile','elereceivermobile']}
{assign var="FIELD_NAME" value=$FIELD_MODEL->get('name')}
<input id="{$MODULE}_editView_fieldName_{$FIELD_NAME}" type="text"
	   class="input-large {if $FIELD_MODEL->isNameField()}nameField{/if}"
	   data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
	   name="{$FIELD_MODEL->getFieldName()}"  {if $FIELD_NAME eq 'agentname'} list="agentlist" onchange="inputSelect()"  autocomplete="off" {/if}

		  value="{$FIELD_MODEL->get('fieldvalue')}"
		{if ($FIELD_MODEL->get('uitype') eq '106' && $MODE neq '') || $FIELD_MODEL->get('uitype') eq '3'
				|| $FIELD_MODEL->get('uitype') eq '4'|| $FIELD_MODEL->isReadOnly() || in_array($FIELD_NAME,$FIEDLONLY)}
				readonly="readonly"
		{/if}
	   {if $FIELD_MODEL->get('prompt') neq ''} placeholder="{$FIELD_MODEL->get('prompt')}"{/if}
	    data-fieldinfo='{$FIELD_INFO}' {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if} />
{* TODO - Handler Ticker Symbol field  ($FIELD_MODEL->get('uitype') eq '106' && $MODE eq 'edit') ||*}
	{if $FIELD_NAME eq 'agentname'}
		<datalist id="agentlist">

		</datalist>
		<input type="hidden" name="agentid" value="{$AGENTID}"/>
	{/if}
{/strip}