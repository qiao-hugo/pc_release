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
{assign var=FIELD_NAME value=$FIELD_MODEL->get('name')}
{assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($FIELD_MODEL->getFieldInfo()))}
{assign var=PICKLIST_VALUES value=$FIELD_MODEL->getAllDepartments(true)}
{assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
{if $FIELD_MODEL->getFieldName() eq 'user_sys'}
{assign var=TissueLists value=$FIELD_MODEL->getTissueLists()}
<select class="chzn-select" name="{$FIELD_MODEL->getFieldName()}" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo='{$FIELD_INFO}' {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if} >
    <option value="">所属体系</option>
    {foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$PICKLIST_VALUES}
    {if in_array($PICKLIST_VALUE,$TissueLists) }
	<option value="{$PICKLIST_VALUE}" {if $FIELD_MODEL->get('fieldvalue') eq $PICKLIST_VALUE} selected {/if}>{vtranslate($PICKLIST_NAME, $MODULE)}</option>
	{/if}
{/foreach}
{elseif $FIELD_MODEL->get('uitype') eq '103'}
    {assign var="FIELD_VALUE_LIST" value=explode(' |##| ',$FIELD_MODEL->get('fieldvalue'))}
    {if $FIELD_VALUE eq ''}
        {assign var=FIELD_VALUE value=$CURRENT_USER_ID}
    {/if}
    <select class="chzn-select" name="{$FIELD_MODEL->getFieldName()}[]" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo='{$FIELD_INFO}' {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if} {if $FIELD_MODEL->get('uitype') eq '103'}multiple{/if}>
        {foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$PICKLIST_VALUES}
            <option value="{$PICKLIST_VALUE}" {if in_array($PICKLIST_VALUE,$FIELD_VALUE_LIST)} selected {/if}>{str_replace(array('|','—'),'',vtranslate($PICKLIST_NAME, $MODULE))}</option>
        {/foreach}
    </select>
{else}
<select class="chzn-select" name="{$FIELD_MODEL->getFieldName()}" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo='{$FIELD_INFO}' {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if} >
    {if $FIELD_MODEL->getFieldName() == 'departmentid'}
        <option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
    {/if}
    {foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$PICKLIST_VALUES}
	<option value="{$PICKLIST_VALUE}" {if $FIELD_MODEL->get('fieldvalue') eq $PICKLIST_VALUE} selected {/if}>{vtranslate($PICKLIST_NAME, $MODULE)}</option>
{/foreach}
</select>
{/if}
{/strip}