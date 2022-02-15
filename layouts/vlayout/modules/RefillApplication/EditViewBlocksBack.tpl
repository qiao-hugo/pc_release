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
		{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
		<input type="hidden" name="msupprebate" value="{$DATARESULT['supprebate']}" />
		{assign var="ADDM" value=array('prestoreadrate','rechargeamount','discount','refundamount')}
		{assign var="OTHERDATAFIELD" value=array('transferamount')}
		<input type="hidden" name="record" value="{$RECORD_ID}" />
		<input type="hidden" name="rechargesheetid" value="{$DATARESULT['rechargesheetid']}" />
		<input type="hidden" name="rechargesource" value="{$RECHARGESOURCE}" />
		{*{assign var="NACCOUNTREADFIELD" value=array('exchangerate','prestoreadrate','rechargeamount','factorage','activationfee','taxation')}
		{assign var=TECHPROCUREMENT value=array('productservice','suppliercontractsid','havesignedcontract','havesignedcontract','transferamount','signdate','productid')}
    	{assign var=ACCOUNTVENDORS value=array('Accounts','Vendors')}
    	{assign var=ACCOUNTVENDORSFIELD value=array('iscontracted','servicesigndate','grossadvances')}
		{assign var=SALESORDERLIST value=array('salesorderid','humancost','purchasecost','contractamount')}
		{assign var=PRERECHARGE value=array('productservice','suppliercontractsid','havesignedcontract','havesignedcontract','signdate','productid','rechargeamount','discount','prestoreadrate','mstatus','rebates')}
		{assign var=MODULEFLAG value=array('Vendors','TECHPROCUREMENT','PreRecharge')}*}
    {assign var=ACCOUNTS value=array('servicecontractsid','accountid','totalrecharge','expcashadvances','iscontracted','servicesigndate','grossadvances','file','remarks','prestoreadrate','rechargeamount','discount','mstatus','rebatetype','accountrebatetype','activationfee','factorage','taxation')}
    {assign var=VENDORS value=array('servicecontractsid','accountid','customertype','totalrecharge','expcashadvances','iscontracted','servicesigndate','grossadvances','file','remarks','vendorid','bankaccount','bankname','banknumber','rebatetype','prestoreadrate','rechargeamount','discount','mstatus','actualtotalrecharge','accountrebatetype','activationfee','factorage','taxation')}
    {assign var=TECHPROCUREMENT value=array('salesorderid','servicecontractsid','accountid','totalrecharge','humancost','purchasecost','contractamount','file','remarks','vendorid','bankaccount','bankname','banknumber','suppliercontractsid','havesignedcontract','signdate','productid','transferamount')}
    {assign var=PRERECHARGE value=array('vendorid','bankaccount','bankname','banknumber','productservice','suppliercontractsid','havesignedcontract','signdate','productid','prestoreadrate','rechargeamount','discount','rebates','mstatus')}
    {assign var=OTHERPROCUREMENT value=array('vendorid','bankaccount','bankname','banknumber','expecteddatepayment','expectedpaymentdeadline','beardepartment','bearratio','productservice','suppliercontractsid','havesignedcontract','signdate','productid','purchaseamount','purchaseprice','purchasequantity')}
    {assign var=NONMEDIAEXTRACTION value=array('servicecontractsid','accountid','totalrecharge','expcashadvances','iscontracted','servicesigndate','grossadvances','file','remarks','vendorid','bankaccount','bankname','banknumber','productservice','suppliercontractsid','havesignedcontract','signdate','productid','purchaseamount')}
    {assign var=NACCOUNTSREADONLY value=array('servicecontractsid','accountid','customertype','totalrecharge','expcashadvances','iscontracted','servicesigndate','grossadvances','file','remarks','receivementcurrencytype','prestoreadrate','mstatus','activationfee','factorage','taxation')}
    {assign var=NVENDORSREADONLY value=array('servicecontractsid','accountid','customertype','totalrecharge','expcashadvances','iscontracted','servicesigndate','grossadvances','file','remarks','vendorid','bankaccount','bankname','banknumber','productservice','suppliercontractsid','signdate','productid','isprovideservice','rechargetypedetail','receivementcurrencytype','prestoreadrate','totalcost','servicecost','mstatus','activationfee','factorage','taxation')}
	{foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE name="EditViewBlockLevelLoop"}
			{if $BLOCK_FIELDS|@count lte 0}{continue}{/if}
            {if $BLOCK_LABEL neq 'LBL_CUSTOM_INFORMATION'}{continue}{/if}
			<table class="table table-bordered blockContainer showInlineTable detailview-table {$BLOCK_LABEL}">
			<thead>
			<tr>
				<th class="blockHeader" colspan="4">
				&nbsp;&nbsp;{vtranslate($BLOCK_LABEL, $MODULE)}</th>
			</tr>
			</thead>
			<tbody>
			<tr><td colspan="4"><span style="color:red;">此页面为充值单红冲退款申请页面，请注意修改页面默认值，充值账户币及现金充值均可互推最终退款金额（只能设置其一），最终算出的退款金额不能大于可退款金额，要退出回款需在关联回款信息栏对应设置</span></td></tr>
			<tr>

			{assign var=COUNTER value=0}
			{foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}
                {*{if in_array($FIELD_MODEL->getFieldName(),$ACCOUNTVENDORSFIELD) && !in_array($RECHARGESOURCE,$ACCOUNTVENDORS)}{continue}{/if}
				{if $FIELD_MODEL->getFieldName() eq 'did' && $RECHARGESOURCE neq 'Accounts'}{continue}{/if}
                {if $FIELD_MODEL->getFieldName() eq 'accountzh' && $RECHARGESOURCE neq 'Accounts'}{continue}{/if}
                {if $FIELD_MODEL->getFieldName() eq 'topplatform' && $RECHARGESOURCE neq 'Accounts'}{continue}{/if}
                {if $FIELD_MODEL->getFieldName() eq 'productservice' && !in_array($RECHARGESOURCE,$MODULEFLAG)}{continue}{/if}
                {if $FIELD_MODEL->getFieldName() eq 'suppliercontractsid' && !in_array($RECHARGESOURCE,$MODULEFLAG)}{continue}{/if}
                {if $FIELD_MODEL->getFieldName() eq 'havesignedcontract' && !in_array($RECHARGESOURCE,$MODULEFLAG)}{continue}{/if}
                {if $FIELD_MODEL->getFieldName() eq 'signdate' && !in_array($RECHARGESOURCE,$MODULEFLAG)}{continue}{/if}
                {if $FIELD_MODEL->getFieldName() eq 'customeroriginattr' && $RECHARGESOURCE eq 'TECHPROCUREMENT'}{continue}{/if}
                {if $FIELD_MODEL->getFieldName() eq 'customertype' && $RECHARGESOURCE eq 'TECHPROCUREMENT'}{continue}{/if}
                {if $FIELD_MODEL->getFieldName() eq 'expcashadvances' && $RECHARGESOURCE eq 'TECHPROCUREMENT'}{continue}{/if}
                {if !in_array($FIELD_MODEL->getFieldName(),$TECHPROCUREMENT) && $RECHARGESOURCE eq 'TECHPROCUREMENT' && $BLOCK_LABEL eq 'LBL_CUSTOM_INFORMATION'}{continue}{/if}
                {if in_array($FIELD_MODEL->getFieldName(),$SALESORDERLIST) && $RECHARGESOURCE neq 'TECHPROCUREMENT'}{continue}{/if}
                {if !in_array($FIELD_MODEL->getFieldName(),$PRERECHARGE) && $RECHARGESOURCE eq 'PreRecharge' && $BLOCK_LABEL eq 'LBL_CUSTOM_INFORMATION'}{continue}{/if}
                {if $FIELD_MODEL->getFieldName() eq 'rebates' && $RECHARGESOURCE neq 'PreRecharge'}{continue}{/if}
		*}
                {if !in_array($FIELD_MODEL->getFieldName(),$ACCOUNTS) && $RECHARGESOURCE eq 'Accounts'}{continue}{/if}
                {if !in_array($FIELD_MODEL->getFieldName(),$VENDORS) && $RECHARGESOURCE eq 'Vendors'}{continue}{/if}
                {if !in_array($FIELD_MODEL->getFieldName(),$TECHPROCUREMENT) && $RECHARGESOURCE eq 'TECHPROCUREMENT'}{continue}{/if}
                {if !in_array($FIELD_MODEL->getFieldName(),$PRERECHARGE) && $RECHARGESOURCE eq 'PreRecharge'}{continue}{/if}
                {if !in_array($FIELD_MODEL->getFieldName(),$OTHERPROCUREMENT) && $RECHARGESOURCE eq 'OtherProcurement'}{continue}{/if}
                {if !in_array($FIELD_MODEL->getFieldName(),$NONMEDIAEXTRACTION) && $RECHARGESOURCE eq 'NonMediaExtraction'}{continue}{/if}


                {assign var="isReferenceField" value=$FIELD_MODEL->getFieldDataType()}
				{if $FIELD_MODEL->get('uitype') eq "20" or $FIELD_MODEL->get('uitype') eq "19"}
					{if $COUNTER eq '1'}
						<td class="{$WIDTHTYPE}"></td><td class="{$WIDTH_TYPE_CLASSSES[$WIDTHTYPE]}"></td></tr><tr>
						{assign var=COUNTER value=0}
					{/if}
				{/if}
				{if $COUNTER eq 2}
					</tr><tr>
					{assign var=COUNTER value=1}
				{else}
					{assign var=COUNTER value=$COUNTER+1}
				{/if}
				<td class="fieldLabel {$WIDTHTYPE}">
					{if $isReferenceField neq "reference"}<label class="muted pull-right marginRight10px">{/if}
						{if $FIELD_MODEL->isMandatory() eq true && $isReferenceField neq "reference"} <span class="redColor">*</span> {/if}
						{if $isReferenceField eq "reference"}
							{assign var="REFERENCE_LIST" value=$FIELD_MODEL->getReferenceList()}
							{assign var="REFERENCE_LIST_COUNT" value=count($REFERENCE_LIST)}
							{if $REFERENCE_LIST_COUNT > 1}
								{assign var="DISPLAYID" value=$FIELD_MODEL->get('fieldvalue')}
								{assign var="REFERENCED_MODULE_STRUCT" value=$FIELD_MODEL->getUITypeModel()->getReferenceModule($DISPLAYID)}
								{if !empty($REFERENCED_MODULE_STRUCT)}
									{assign var="REFERENCED_MODULE_NAME" value=$REFERENCED_MODULE_STRUCT->get('name')}
								{/if}
								<span class="pull-right">
									{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}
									<select class="chzn-select referenceModulesList streched" style="width:140px;">
										<optgroup>
											{foreach key=index item=value from=$REFERENCE_LIST}
												<option value="{$value}" {if $value eq $REFERENCED_MODULE_NAME} selected {/if}>{vtranslate($value, $MODULE)}</option>
											{/foreach}
										</optgroup>
									</select>
								</span>
							{else}
								<label class="muted pull-right marginRight10px">{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}<span class="redColor">[退]</span>{vtranslate($FIELD_MODEL->get('label'), $MODULE)}</label>
							{/if}
						{else}
							<span class="redColor">[退]</span>{vtranslate($FIELD_MODEL->get('label'), $MODULE)}
						{/if}
					{if $isReferenceField neq "reference"}</label>{/if}
				</td>
				{if $FIELD_MODEL->get('uitype') neq "83"}
					<td class="fieldValue {$WIDTHTYPE}" {if $FIELD_MODEL->get('uitype') eq '19'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if} {if $FIELD_MODEL->get('uitype') eq '20'} colspan="3"{/if}>
						{if $ISENTITY eq 1 && $FIELD_MODEL->get('uitype') neq "1" && $FIELD_MODEL->get('uitype') neq "15" && $FIELD_MODEL->get('uitype') neq "16" && !in_array($FIELD_MODEL->getFieldName(),$ADDM) && !in_array($FIELD_MODEL->getFieldName(),$OTHERDATAFIELD)}
							{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) BLOCK_FIELDS=$BLOCK_FIELDS}

						{else}
                            {assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($FIELD_MODEL->getFieldInfo()))}
                            {assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
                            {assign var="FIELD_NAME" value=$FIELD_MODEL->get('name')}
                            {assign var="DISCOUNT" value=array('PreRecharge','Accounts')}
                            {assign var="READONLYDATA" value=array("totalrecharge","actualtotalrecharge","topplatform","accountzh","servicecost","prestoreadrate",'totalcost','totalgrossprofit','accountnumber','depositbank','bankname','invoicecode','invoicenumber','businessnames','billingcontent','amountofmoney','contractamount','humancost','purchasecost')}
                            {if $FIELD_MODEL->getFieldName() eq 'rechargeamount' && $RECHARGESOURCE eq 'PreRecharge'}
                                {assign var="READONLYFLAG" value=false}
                            {else}
                                {assign var="READONLYFLAG" value=true}
                            {/if}
							<input id="{$MODULE}_editView_fieldName_{$FIELD_NAME}" type="text"
								   class="input-large {if $FIELD_MODEL->isNameField()}nameField{/if}"
								   data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
								   name="{if in_array($FIELD_MODEL->getFieldName(),$ADDM)}m{/if}{$FIELD_MODEL->getFieldName()}"
								   data-type='m{$FIELD_MODEL->getFieldName()}'
								   data-value="{$DATARESULT[$FIELD_MODEL->getFieldName()]}"
								   value="{if !in_array($FIELD_MODEL->getFieldName(),$DISPLAYFIELD)}
                            {if $FIELD_MODEL->get('uitype') eq 56}
                             {if $DATARESULT[$FIELD_MODEL->getFieldName()] eq 1}有{else}无{/if}
                            {elseif in_array($FIELD_MODEL->get('uitype'),array(15,16))}
                             {vtranslate($DATARESULT[$FIELD_MODEL->getFieldName()],"RefillApplication")}
                                {else}
                                {$DATARESULT[$FIELD_MODEL->getFieldName()]}
                            {/if}
                         {else}
                             {$DATARESULT[$DISPLAYVALUE[$FIELD_MODEL->getFieldName()]]}
                         {/if}"
                                    {if $FIELD_MODEL->get('uitype') eq '3'
                                    || $FIELD_MODEL->get('uitype') eq '4'|| $FIELD_MODEL->isReadOnly() || (!in_array($FIELD_MODEL->getFieldName(),$NACCOUNTSREADONLY) && $RECHARGESOURCE eq 'Accounts') || (!in_array($FIELD_MODEL->getFieldName(),$NVENDORSREADONLY) && $RECHARGESOURCE eq 'Vendors')}
										readonly="readonly"
                                    {/if}
								   data-fieldinfo='{$FIELD_INFO}' {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if} />
                            {if $FIELD_MODEL->getFieldName() eq 'rebatetype'}
								<input type="hidden" name="rebatetypevalue" value="{$DATARESULT[$FIELD_MODEL->getFieldName()]}" >
                            {/if}
                            {if $FIELD_MODEL->getFieldName() eq 'accountrebatetype'}
								<input type="hidden" name="accountrebatetypevalue" value="{$DATARESULT[$FIELD_MODEL->getFieldName()]}" >
                            {/if}
						{/if}

					</td>
				{/if}
				{if $BLOCK_FIELDS|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype"}
					<td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
				{/if}
			{/foreach}
			</tr></tbody>
			</table>
			<br>
		{/foreach}
	<table class="table table-bordered blockContainer showInlineTable detailview-table LBL_CUSTOM_INFORMATION">
		<thead>
		<tr><th class="blockHeader" colspan="4">
				 &nbsp;&nbsp;</th></tr>
		</thead>
		<tbody>
		<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">最高可退款金额</label></td>{*'activationfee','factorage','taxation'*}
			<td class="fieldValue medium"><input type="text" class="input-large" id="high_refund" value="{$DATARESULT['transferamount']-$DATARESULT['refundamount']}" readonly="readonly"/></td>
{*                        <td class="fieldValue medium"><input type="text" class="input-large" value="{$DATARESULT['transferamount']-$DATARESULT['refundamount']-$DATARESULT['factorage']-$DATARESULT['taxation']-$DATARESULT['activationfee']}" readonly="readonly"/></td>*}
			<td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{vtranslate('refundamount','RefillApplication')} </label></td>
			<td class="fieldValue medium" ><input type="text" class="input-large" name="mrefundamount" value="{$DATARESULT['transferamount']-$DATARESULT['refundamount']}" readonly="readonly" /><input type="hidden" class="input-large" name="trefundamount" value="{$DATARESULT['transferamount']-$DATARESULT['refundamount']}" /></td>
		<tr>
		{if $RECHARGESOURCE eq 'Vendors'}
		<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">供应商退款金额</label></td>
			<td class="fieldValue medium" colspan="3"><input type="text" class="input-large" name="amountpayable" value="" readonly="readonly"/></td>
		<tr>
		{/if}
		</tbody>
	</table>

{/strip}