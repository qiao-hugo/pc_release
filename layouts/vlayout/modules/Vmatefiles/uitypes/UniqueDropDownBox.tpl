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
    <input type="hidden" name={$FIELDNAME} value="{Vtiger_Util_Helper::toSafeHTML('files_style9')}" />
    <select disabled="ture" class="chzn-select" name="{$FIELDNAME}-select" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo='{$FIELD_INFO|escape}' {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if} data-selected-value='{$FIELD_MODEL->get('fieldvalue')}'>
        <option value="{Vtiger_Util_Helper::toSafeHTML('files_style9')}">vmate合同</option>
    </select>
{/strip}