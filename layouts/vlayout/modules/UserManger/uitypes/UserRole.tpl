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
{assign var=PICKLIST_VALUES value=$FIELD_MODEL->getAllRoles()}
{assign var=OLDTEMPJOB value=""}
{assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
<select class="chzn-select" name="{$FIELD_MODEL->getFieldName()}" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo='{$FIELD_INFO}' {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if} >
    <option value="">请选择角色</option>
    {foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$PICKLIST_VALUES}
        {if $PICKLIST_VALUE@first}
            <optgroup label="{vtranslate($PICKLIST_VALUE['jobcategory'],'UserManger')}">
        {/if}
        {if !$PICKLIST_VALUE@first &&  $PICKLIST_VALUE['jobcategory'] neq $OLDTEMPJOB}
            </optgroup>
            <optgroup label="{vtranslate($PICKLIST_VALUE['jobcategory'],'UserManger')}">
            {$OLDTEMPJOB=$PICKLIST_VALUE['jobcategory']}
        {/if}
        <option value="{$PICKLIST_VALUE['id']}" {if $FIELD_MODEL->get('fieldvalue') eq $PICKLIST_VALUE['id']} selected {/if}>
            {vtranslate($PICKLIST_NAME, $MODULE)}
        </option>
            {if $PICKLIST_VALUE@last}
        </optgroup>
        {/if}
{/foreach}
</select>
{/strip}