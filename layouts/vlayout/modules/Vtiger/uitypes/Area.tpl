{strip}
{assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($FIELD_MODEL->getFieldInfo()))}
{assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
{assign var="FIELD_NAME" value=$FIELD_MODEL->get('name')}
{assign var=FIELD_VALUE value=$FIELD_MODEL->get('fieldvalue')}
<select style="width:80px;" name="province" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"></select>
<select style="width:80px;" name="city"  data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"></select>
<select style="width:80px;" name="area"  data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"></select>
<input id="{$MODULE}_editView_fieldName_{$FIELD_NAME}" type="text" class="input-large" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  name="{$FIELD_MODEL->getFieldName()}" data-fieldinfo='{$FIELD_INFO}' {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if} />
<span id="areadata" data="{$FIELD_VALUE}" style="display:none;"></span>
{/strip}