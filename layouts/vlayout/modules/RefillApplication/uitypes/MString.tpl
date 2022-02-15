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
{assign var="FIELD_NAME" value=$FIELD_MODEL->get('name')}
{assign var=ACCOUNTS value=array('servicecontractsid','usecontractamount','contractamount','accountid','customertype','customeroriginattr','totalrecharge','actualtotalrecharge','expcashadvances','iscontracted','servicesigndate','file','did','accountzh','productid','isprovideservice','rechargetypedetail','receivementcurrencytype','prestoreadrate','rechargeamount','discount','tax','totalcost','servicecost','totalgrossprofit')}
{assign var=VENDORS value=array('totalreceivables','usecontractamount','contractamount','discount','servicecontractsid','accountid','customertype','customeroriginattr','totalrecharge','actualtotalrecharge','expcashadvances','iscontracted','servicesigndate','file','did','accountzh','vendorid','bankaccount','bankname','banknumber','productservice','suppliercontractsid','havesignedcontract','signdate','productid','isprovideservice','rechargetypedetail','receivementcurrencytype','prestoreadrate','rechargeamount','tax','totalcost','servicecost','totalgrossprofit','bankcode')}
{assign var=TECHPROCUREMENT value=array('salesorderid','servicecontractsid','accountid','totalrecharge','totalreceivables','actualtotalrecharge','humancost','purchasecost','contractamount','file','remarks','vendorid','bankaccount','bankname','banknumber','bankcode','productservice','suppliercontractsid','havesignedcontract','signdate','productid')}
{assign var=PRERECHARGE value=array('totalreceivables','vendorid','bankaccount','bankname','banknumber','bankcode','productservice','suppliercontractsid','havesignedcontract','signdate','productid','prestoreadrate','discount')}
{assign var=OTHERPROCUREMENT value=array('vendorid','bankaccount','bankname','banknumber','bankcode','expecteddatepayment','expectedpaymentdeadline','beardepartment','bearratio','productservice','suppliercontractsid','havesignedcontract','signdate','productid')}
{assign var=NONMEDIAEXTRACTION value=array('servicecontractsid','accountid','totalrecharge','actualtotalrecharge','totalreceivables','expcashadvances','iscontracted','servicesigndate','file','remarks','vendorid','bankaccount','bankname','banknumber','bankcode','productservice','suppliercontractsid','havesignedcontract','signdate','productid','contractamount','usecontractamount')}
{assign var=PACKVENDORSSTRING value=array('vendorid','bankaccount','bankname','banknumber','bankcode','totalreceivables')}
    {assign var=COINRETURN value=array('accountid','file','remarks','did','accountzh','productid','isprovideservice','accountrebatetype','discount','totalcashtransfer','totalcashin','totalturnoverofaccount','totaltransfertoaccount','cashtransfer')}
    {assign var=INCREASE value=array('cashconsumptiontotal','cashincreasetotal','cashincrease')}
	<input id="{$MODULE}_editView_fieldName_m{$FIELD_NAME}{$DATANUM}" type="text"
	   class="input-large {if $FIELD_MODEL->isNameField()}nameField{/if}"
	   data-num="{$DATANUM}"
	   data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" 
	   name="m{$FIELD_MODEL->getFieldName()}[{$DATANUM}]"
	   value="{$FIELD_MODEL->get('fieldvalue')}"
	   {if $FIELD_MODEL->getFieldName() eq 'grantquarter'}placeholder="{$FIELD_MODEL->get('prompt')}"{/if}
		{if ($FIELD_MODEL->get('uitype') eq '106' && $MODE neq '') || $FIELD_MODEL->get('uitype') eq '3'
				|| $FIELD_MODEL->get('uitype') eq '4'|| $FIELD_MODEL->isReadOnly() || ($RECHARGESOURCE eq 'Accounts' && in_array($FIELD_MODEL->getFieldName(),$ACCOUNTS)) || ($RECHARGESOURCE eq 'Vendors' && in_array($FIELD_MODEL->getFieldName(),$VENDORS)) || ($RECHARGESOURCE eq 'TECHPROCUREMENT' && in_array($FIELD_MODEL->getFieldName(),$TECHPROCUREMENT)) || ($RECHARGESOURCE eq 'PreRecharge' && in_array($FIELD_MODEL->getFieldName(),$PRERECHARGE)) || ($RECHARGESOURCE eq 'OtherProcurement' && in_array($FIELD_MODEL->getFieldName(),$OTHERPROCUREMENT)) || ($RECHARGESOURCE eq 'NonMediaExtraction' && in_array($FIELD_MODEL->getFieldName(),$NONMEDIAEXTRACTION)) || ($RECHARGESOURCE eq 'PACKVENDORS' && in_array($FIELD_MODEL->getFieldName(),$PACKVENDORSSTRING)) || ($RECHARGESOURCE eq 'COINRETURN' && in_array($FIELD_MODEL->getFieldName(),$COINRETURN)) || ($RECHARGESOURCE eq 'INCREASE' && in_array($FIELD_MODEL->getFieldName(),$INCREASE))}
				readonly="readonly"
		{/if}
data-fieldinfo='{$FIELD_INFO}' {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if} />
{* TODO - Handler Ticker Symbol field  ($FIELD_MODEL->get('uitype') eq '106' && $MODE eq 'edit') ||*}
{/strip}