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

<select class="chzn-select {if $OCCUPY_COMPLETE_WIDTH} row-fluid {/if}" name="contract_type" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo='{$FIELD_INFO|escape}' {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if} data-selected-value='{$FIELD_MODEL->get('fieldvalue')}'>
		{if $FIELD_MODEL->isEmptyPicklistOptionAllowed()}<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>{/if}
	{foreach item=value key=key from=$RECORD_CONTRACT_TYPE_LIST}
        <option value="{$value['contract_typeid']}" {if $value['contract_typeid'] eq $RECORD_CONTRACTTYPE_ID } selected {/if}>{$value['contract_type']}</option>
    {/foreach}
</select>

{/strip}