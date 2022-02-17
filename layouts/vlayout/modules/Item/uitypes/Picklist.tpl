{*<!--
/**
  *下拉，保护数量增加限制
 ******/
-->*}
{strip}
    {assign var="FIELD_INFO" value=Zend_Json::encode($FIELD_MODEL->getFieldInfo())}
    {assign var=PICKLIST_VALUES value=$FIELD_MODEL->getPicklistValues()}
    {assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
    {assign var="FIELDNAME" value={$FIELD_MODEL->getFieldName()}}
    {assign var="NOCHZN" value=['eleccontracttplid']}
    {if $FIELDNAME eq 'special'}

        {if $RECORD_ID>0}
            <select class="chzn-select" name="special" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"   data-selected-value='{$FIELD_MODEL->get('fieldvalue')}'>
                <option value="0" {if $RECORD_PRODUCTSCATEGORY['nspecial'] eq 0} selected {/if}>否</option>
                <option value="1" {if $RECORD_PRODUCTSCATEGORY['nspecial'] eq 1} selected {/if}>是</option>
            </select>
        {else}
            <select class="chzn-select" name="special" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"   data-selected-value='{$FIELD_MODEL->get('fieldvalue')}'>
                <option value="0">否</option>
                <option value="1">是</option>
            </select>
        {/if}

    {else if $FIELDNAME eq 'parentcate'}
        {if $RECORD_ID>0}
            <select class="chzn-select" name="parentcate" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"   data-selected-value='{$FIELD_MODEL->get('fieldvalue')}'>
                {if $FIELD_MODEL->isEmptyPicklistOptionAllowed()}<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>{/if}
                {foreach item=PICKLIST_P  from=$RECORD_PRODUCTSCATEGORY['parent']}
                    <option value="{$PICKLIST_P['parentcateid']}" {if $RECORD_PRODUCTSCATEGORY['nparentcate'] eq $PICKLIST_P['parentcateid']} selected {/if}>{$PICKLIST_P['parentcate']}</option>
                {/foreach}
            </select>
        {else}
            <input id="{$MODULE}_editView_fieldName_{$FIELD_NAME}" type="text" class="input-large" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  name="{$FIELD_MODEL->getFieldName()}" data-fieldinfo='{$FIELD_INFO}' {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if} />
        {/if}
    {else}
    {if $RECORD_ID>0}
        <input id="{$MODULE}_editView_fieldName_{$FIELD_NAME}" type="text" class="input-large" value="{$RECORD_PRODUCTSCATEGORY['nsoncate']}" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  name="{$FIELD_MODEL->getFieldName()}" data-fieldinfo='{$FIELD_INFO}' {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if} />
    {else}
        <input id="{$MODULE}_editView_fieldName_{$FIELD_NAME}" type="text" class="input-large" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  name="{$FIELD_MODEL->getFieldName()}" data-fieldinfo='{$FIELD_INFO}' {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if} />
    {/if}
    {/if}
{/strip}