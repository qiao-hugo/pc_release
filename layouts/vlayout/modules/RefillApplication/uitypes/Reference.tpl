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
{assign var="REFERENCE_LIST" value=$FIELD_MODEL->getReferenceList()}
{if $FIELD_NAME eq 'did'}
	{assign var="REFERENCE_LIST" value=array('RefillApplication')}
{/if}
{assign var="REFERENCE_LIST_COUNT" value=count($REFERENCE_LIST)}
{assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($FIELD_MODEL->getFieldInfo()))}
{assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
{if {$REFERENCE_LIST_COUNT} eq 1}
		<input name="popupReferenceModule" type="hidden" value="{$REFERENCE_LIST[0]}" />
{/if}
{if {$REFERENCE_LIST_COUNT} gt 1}
	{assign var="DISPLAYID" value=$FIELD_MODEL->get('fieldvalue')}
	{assign var="REFERENCED_MODULE_STRUCT" value=$FIELD_MODEL->getUITypeModel()->getReferenceModule($DISPLAYID)}
	{if !empty($REFERENCED_MODULE_STRUCT)}
		{assign var="REFERENCED_MODULE_NAME" value=$REFERENCED_MODULE_STRUCT->get('name')}
	{/if}
	{if in_array($REFERENCED_MODULE_NAME, $REFERENCE_LIST)}
		<input name="popupReferenceModule" type="hidden" value="{$REFERENCED_MODULE_NAME}" />
	{else}
		<input name="popupReferenceModule" type="hidden" value="{$REFERENCE_LIST[0]}" />
	{/if}
{/if}
	{assign var="FIELDSNO" value=array('productid','purchaseinvoiceid','accountid','suppliercontractsid','maccountid','mservicecontractsid')}
	{assign var="TECHFIELDSNO" value=array('salesorderid','vendorid')}
	{assign var="ACCOUNTSFIELDSNO" value=array('purchaseinvoiceid','servicecontractsid','suppliercontractsid')}
	{assign var="VendorsFIELDSNO" value=array('servicecontractsid','vendorid')}
	{assign var="PreRechargeFIELDSNO" value=array('vendorid')}
	{assign var="OtherProcurementFIELDSNO" value=array('productid','purchaseinvoiceid','accountid','servicecontractsid','suppliercontractsid')}
	{assign var="NonMediaExtractionFIELDSNO" value=array('vendorid','servicecontractsid')}
	{assign var="PACKVENDORSFIELDSNO" value=array('vendorid')}
	{assign var="COINRETURNFIELDSNO" value=array('servicecontractsid','vendorid')}
	{assign var="INCREASE" value=array('mservicecontractsid')}
	{assign var="CONTRACTCHANGES" value=array('accountid','rechargesource','customertype','actualtotalrecharge','contractamount','iscontracted','servicesigndate','grossadvances','totalreceivables','remarks','changesnumber','newcustomertype','newaccountid','newiscontracted','newservicesigndate','newcontractamount','newcontractsid','servicecontractsid')}
<input name="{$FIELD_MODEL->getFieldName()}"  type="hidden" value="{$FIELD_MODEL->get('fieldvalue')}" data-multiple="{$FIELD_MODEL->get('ismultiple')}" class="sourceField" data-displayvalue='{$FIELD_MODEL->getEditViewDisplayValue($FIELD_MODEL->get('fieldvalue'))}' data-fieldinfo='{$FIELD_INFO}' />
{assign var="displayId" value=$FIELD_MODEL->get('fieldvalue')}
<div class="row-fluid input-prepend input-append">
	{*{if (!in_array($FIELD_NAME,$FIELDSNO) && $RECHARGESOURCE neq 'COINRETURN') || (!in_array($FIELD_NAME,$TECHFIELDSNO) && $RECHARGESOURCE eq 'COINRETURN')}*}
	{if (empty($RECORD_ID) && ((in_array($FIELD_NAME,$ACCOUNTSFIELDSNO) && $RECHARGESOURCE eq 'Accounts')
		|| (in_array($FIELD_NAME,$VendorsFIELDSNO) && $RECHARGESOURCE eq 'Vendors')
		|| (in_array($FIELD_NAME,$PreRechargeFIELDSNO) && $RECHARGESOURCE eq 'PreRecharge')
		|| (in_array($FIELD_NAME,$OtherProcurementFIELDSNO) && $RECHARGESOURCE eq 'OtherProcurement')
		|| (in_array($FIELD_NAME,$NonMediaExtractionFIELDSNO) && $RECHARGESOURCE eq 'NonMediaExtraction')
		|| (in_array($FIELD_NAME,$PACKVENDORSFIELDSNO) && $RECHARGESOURCE eq 'PACKVENDORS')
    	|| (in_array($FIELD_NAME,$COINRETURNFIELDSNO) && $RECHARGESOURCE eq 'COINRETURN')
    	|| (in_array($FIELD_NAME,$TECHFIELDSNO) && $RECHARGESOURCE eq 'TECHPROCUREMENT')))
    	|| (in_array($FIELD_NAME,$INCREASE) && $RECHARGESOURCE eq 'INCREASE')
	    || ((in_array($FIELD_NAME,$CONTRACTCHANGES) && $RECHARGESOURCE eq 'contractChanges' && !($RECORD_ID gt 0) ))
	}
<span class="add-on clearReferenceSelection cursorPointer">
	<i id="{$MODULE}_editView_fieldName_{$FIELD_NAME}_clear" class='icon-remove-sign' title="{vtranslate('LBL_CLEAR', $MODULE)}"></i>
</span>
	{/if}
{if $FIELD_MODEL->getFieldName() eq 'did'}
	<span class="add-on clearReferenceSelection cursorPointer">
	<i id="{$MODULE}_editView_fieldName_{$FIELD_NAME}_clear" class='icon-remove-sign' title="{vtranslate('LBL_CLEAR', $MODULE)}"></i>
</span>
{/if}
<input id="{$FIELD_NAME}_display" readonly="readonly" name="{$FIELD_MODEL->getFieldName()}_display" type="text" class="{if $smarty.request.view eq 'Edit'} span7 {else} span8 {/if}	marginLeftZero autoComplete" {if !empty($displayId)}readonly="true"{/if}
 {if $RECHARGESOURCE eq 'contractChanges' && $smarty.request.view eq 'Edit' && $FIELD_MODEL->getFieldName() eq 'servicecontractsid' }value="{$oldcontract_no}"{elseif $RECHARGESOURCE eq 'contractChanges' && $smarty.request.view eq 'Edit' && $FIELD_MODEL->getFieldName() eq 'newcontractsid' } value="{$newcontract_no}" {else}value="{$FIELD_MODEL->getEditViewDisplayValue($displayId)}"{/if} data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
 data-fieldinfo='{$FIELD_INFO}' placeholder="{vtranslate('LBL_TYPE_SEARCH',$MODULE)}" {if in_array($FIELD_NAME,$FIELDSNO)} readonly="readonly"{/if}
 {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if}/>
    {if (empty($RECORD_ID) && ((in_array($FIELD_NAME,$ACCOUNTSFIELDSNO) && $RECHARGESOURCE eq 'Accounts')
    || (in_array($FIELD_NAME,$VendorsFIELDSNO) && $RECHARGESOURCE eq 'Vendors')
    || (in_array($FIELD_NAME,$PreRechargeFIELDSNO) && $RECHARGESOURCE eq 'PreRecharge')
    || (in_array($FIELD_NAME,$OtherProcurementFIELDSNO) && $RECHARGESOURCE eq 'OtherProcurement')
    || (in_array($FIELD_NAME,$NonMediaExtractionFIELDSNO) && $RECHARGESOURCE eq 'NonMediaExtraction')
    || (in_array($FIELD_NAME,$PACKVENDORSFIELDSNO) && $RECHARGESOURCE eq 'PACKVENDORS')
    || (in_array($FIELD_NAME,$COINRETURNFIELDSNO) && $RECHARGESOURCE eq 'COINRETURN')
    || (in_array($FIELD_NAME,$TECHFIELDSNO) && $RECHARGESOURCE eq 'TECHPROCUREMENT')))
    || (in_array($FIELD_NAME,$INCREASE) && $RECHARGESOURCE eq 'INCREASE')
	|| ((in_array($FIELD_NAME,$CONTRACTCHANGES) && $RECHARGESOURCE eq 'contractChanges'  && !($RECORD_ID gt 0) ))}
<span data-id="{$MODULE}_editView_fieldName_{$FIELD_NAME}_select" class="add-on relatedPopup cursorPointer">
	<i id="{$MODULE}_editView_fieldName_{$FIELD_NAME}_select" data-id="{$MODULE}_editView_fieldName_{$FIELD_NAME}_select" class="icon-search relatedPopup" title="{vtranslate('LBL_SELECT', $MODULE)}" ></i>
</span>
	{/if}
{if $FIELD_MODEL->getFieldName() eq 'did'}
	<span data-id="{$MODULE}_editView_fieldName_{$FIELD_NAME}_select" class="add-on relatedPopupDid cursorPointer">
		<i id="{$MODULE}_editView_fieldName_{$FIELD_NAME}_select" data-id="{$MODULE}_editView_fieldName_{$FIELD_NAME}_select" class="icon-search relatedPopupDid" title="{vtranslate('LBL_SELECT', $MODULE)}" ></i>
	</span>
{/if}
{assign var=QUICKCREATE_RESTRICTED_MODULES value=['SalesOrder','Quotes','Invoice','PurchaseOrder']}
<!-- Show the add button only if it is edit view  
{if $smarty.request.view eq 'Edit' && !in_array($REFERENCE_LIST[0],$QUICKCREATE_RESTRICTED_MODULES)}
<span class="add-on cursorPointer createReferenceRecord">
	<i id="{$MODULE}_editView_fieldName_{$FIELD_NAME}_create" class='icon-plus' title="{vtranslate('LBL_CREATE', $MODULE)}"></i>
</span>
{/if}



-->
</div>
{/strip}
