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
 {if $FIELDNAME eq 'contract_type'}
    {if $RECORD_ID>0}
        <select class="chzn-select" name="parent_contracttypeid" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"   data-selected-value='{$FIELD_MODEL->get('fieldvalue')}' style="width:110px;">
            {if $FIELD_MODEL->isEmptyPicklistOptionAllowed()}<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>{/if}
            {foreach item=PICKLIST_P  from=$RECORD_PRODUCTSCATEGORY['parent']}
                <option value="{$PICKLIST_P['parent_contracttypeid']}" {if $PICKLIST_P['parent_contracttypeid'] eq $RECORD_PRODUCTSCATEGORY['nparentid']} selected {/if}>{$PICKLIST_P['parent_contracttype']}</option>
            {/foreach}
        </select>
        <select class="chzn-select" name="{$FIELDNAME}" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo='{$FIELD_INFO|escape}' {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if} data-selected-value='{$FIELD_MODEL->get('fieldvalue')}' style="width:110px;">
                {if $FIELD_MODEL->isEmptyPicklistOptionAllowed()}<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>{/if}
            {foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$PICKLIST_VALUES}
                {if in_array($PICKLIST_VALUE,$RECORD_PRODUCTSCATEGORY['ischild'])}
                <option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}" {if trim(decode_html($FIELD_MODEL->get('fieldvalue'))) eq trim($PICKLIST_NAME)} selected {/if}     {if $FIELDNAME eq 'accountrank' && isset($RANKLIMIT) }{if !$RANKLIMIT[$PICKLIST_NAME]} disabled="true"{/if}{/if}>{$PICKLIST_VALUE}</option>
                {/if}
            {/foreach}
        </select>

    {else}
        <select class="chzn-select" name="parent_contracttypeid" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"   data-selected-value='{$FIELD_MODEL->get('fieldvalue')}' style="width:110px;">
            {if $FIELD_MODEL->isEmptyPicklistOptionAllowed()}<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>{/if}
            {foreach item=PICKLIST_P  from=$RECORD_PRODUCTSCATEGORY['parent']}
                <option value="{$PICKLIST_P['parent_contracttypeid']}">{$PICKLIST_P['parent_contracttype']}   </option>
            {/foreach}
        </select>
    {/if}
 {else if $FIELDNAME eq 'currencytype'}
     <select class="chzn-select" name="{$FIELDNAME}" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo='{$FIELD_INFO|escape}' {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if} data-selected-value='{$FIELD_MODEL->get('fieldvalue')}'>
         <option value="人民币" {if trim(decode_html($FIELD_MODEL->get('fieldvalue'))) eq '人民币'} selected {/if}>人民币</option>
         <option value="美金" {if trim(decode_html($FIELD_MODEL->get('fieldvalue'))) eq '美金'} selected {/if}>美金</option>
     </select>
 {else}
     <select class="chzn-select" name="{$FIELDNAME}" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo='{$FIELD_INFO|escape}' {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if} data-selected-value='{$FIELD_MODEL->get('fieldvalue')}'>
         {if $FIELD_MODEL->isEmptyPicklistOptionAllowed()}<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>{/if}
         {foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$PICKLIST_VALUES}
             <option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}" {if trim(decode_html($FIELD_MODEL->get('fieldvalue'))) eq trim($PICKLIST_NAME)} selected {/if}     {if $FIELDNAME eq 'accountrank' && isset($RANKLIMIT) }{if !$RANKLIMIT[$PICKLIST_NAME]} disabled="true"{/if}{/if}>{$PICKLIST_VALUE}</option>
         {/foreach}
     </select>
 {/if}
{/strip}