{*<!--
/******
  *货币模版
  *72类型带汇率计算
 ******/
-->*}
{strip}
{assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($FIELD_MODEL->getFieldInfo()))}
{assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
{assign var="FIELD_NAME" value=$FIELD_MODEL->get('name')}

{if $FIELD_MODEL->get('uitype') eq '71'}
<div class="input-prepend">
	<span class="add-on">&#165;</span>
	<input id="{$MODULE}_editView_fieldName_{$FIELD_NAME}" type="text" class="input-medium" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="{$FIELD_MODEL->getFieldName()}"
	data-fieldinfo='{$FIELD_INFO}' value="{$FIELD_MODEL->get('fieldvalue')}" {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if}
	data-decimal-seperator='{$USER_MODEL->get('currency_decimal_separator')}' data-group-seperator='{$USER_MODEL->get('currency_grouping_separator')}' data-number-of-decimal-places='{$USER_MODEL->get('no_of_currency_decimals')}'/>
</div>
{else if ($FIELD_MODEL->get('uitype') eq '72') && ($FIELD_MODEL->getName() eq 'unit_price')}
	{if $BASE_CURRENCY_SYMBOL eq ''}
		{$BASE_CURRENCY_SYMBOL='¥'}
	{/if}
	<div class="input-prepend">
		<div class="row-fluid">
			<span class="span1">
				<span class="add-on row-fluid">{$BASE_CURRENCY_SYMBOL}</span>
			</span>
			<span class="span10 row-fluid">
				<input id="{$MODULE}-editview-fieldname-{$FIELD_NAME}" type="text" class="span6 unitPrice" name="{$FIELD_MODEL->getFieldName()}" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
			data-fieldinfo='{$FIELD_INFO}'  value="{$FIELD_MODEL->get('fieldvalue')}" {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if}
			data-decimal-seperator='{$USER_MODEL->get('currency_decimal_separator')}' data-group-seperator='{$USER_MODEL->get('currency_grouping_separator')}' data-number-of-decimal-places='{$USER_MODEL->get('no_of_currency_decimals')}'/>
				{if $smarty.request.view eq 'Edit'}
					<!--<a id="moreCurrencies" class="span cursorPointer">{vtranslate('LBL_MORE_CURRENCIES', $MODULE)}>></a>
					<span id="moreCurrenciesContainer" class="hide"></span>-->
				{/if}
				<input type="hidden" name="base_currency" value="{$BASE_CURRENCY_NAME}">
				<input type="hidden" name="cur_{$BASE_CURRENCY_ID}_check" value="on">
				<input type="hidden" id="requstedUnitPrice" name="{$BASE_CURRENCY_NAME}" value="">
			</span>
		</div>
	</div>
{else}
<div class="input-prepend">
	<div class="row-fluid">
		<span class="span1"><span class="add-on row-fluid">{$USER_MODEL->get('currency_symbol')}</span></span>
		<span class="span7"><input type="text" class="row-fluid" name="{$FIELD_MODEL->getFieldName()}" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
		data-fieldinfo='{$FIELD_INFO}' value="{$FIELD_MODEL->get('fieldvalue')}" {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if} data-decimal-seperator='{$USER_MODEL->get('currency_decimal_separator')}' data-group-seperator='{$USER_MODEL->get('currency_grouping_separator')}' /></span>
	</div>
</div>
{/if}
{* TODO - UI Type 72 needs to be handled. Multi-currency support also needs to be handled *}
{/strip}