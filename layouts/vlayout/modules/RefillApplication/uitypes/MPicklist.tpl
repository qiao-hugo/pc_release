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
<select class="chzn-select" name="m{$FIELDNAME}[{$DATANUM}]" data-num="{$DATANUM}" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo='{$FIELD_INFO|escape}' {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if} data-selected-value='{$FIELD_MODEL->get('fieldvalue')}'>
		{if $FIELD_MODEL->isEmptyPicklistOptionAllowed()}<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>{/if}
    {if $FIELD_MODEL->getFieldName() eq 'did'}<option value="{$FIELD_MODEL->get('fieldvalue')}" selected>{$FIELD_MODEL->get('fieldvalue')}</option>{/if}
    {if $FIELD_MODEL->getFieldName() eq 'productservice'}<option value="{$FIELD_MODEL->get('fieldvalue')}" selected>{$PRODUCTSERVICEVALUE}</option>{/if}
    {foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$PICKLIST_VALUES}
        <option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}" {if trim(decode_html($FIELD_MODEL->get('fieldvalue'))) eq trim($PICKLIST_NAME)} selected {/if}     {if $FIELDNAME eq 'accountrank' && isset($RANKLIMIT) }{if !$RANKLIMIT[$PICKLIST_NAME]} disabled="true"{/if}{/if}>{$PICKLIST_VALUE}</option>
    {/foreach}
</select>
{/strip}