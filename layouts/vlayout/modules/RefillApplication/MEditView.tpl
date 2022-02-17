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

		{assign var=ACCOUNTS value=array('servicecontractsid','usecontractamount','contractamount','accountid','customertype','customeroriginattr','totalrecharge','actualtotalrecharge','expcashadvances','iscontracted','servicesigndate','grossadvances','file','remarks','did','accountzh','productid','rebatetype','isprovideservice','rechargetypedetail','receivementcurrencytype','exchangerate','prestoreadrate','rechargeamount','discount','tax','factorage','activationfee','taxation','totalcost','transferamount','servicecost','totalgrossprofit','mstatus','accountrebatetype','flow_state')}
		{assign var=VENDORS value=array('totalreceivables','usecontractamount','contractamount','servicecontractsid','accountid','customertype','customeroriginattr','totalrecharge','actualtotalrecharge','expcashadvances','iscontracted','servicesigndate','grossadvances','file','remarks','did','rebatetype','accountzh','remarks','vendorid','bankaccount','bankname','banknumber','banklist','bankcode','productservice','suppliercontractsid','havesignedcontract','signdate','productid','isprovideservice','rechargetypedetail','receivementcurrencytype','exchangerate','prestoreadrate','rechargeamount','discount','tax','factorage','activationfee','taxation','totalcost','transferamount','servicecost','totalgrossprofit','mstatus','accountrebatetype','paymentperiod','ispayment')}
		{assign var=TECHPROCUREMENT value=array('salesorderid','servicecontractsid','accountid','totalrecharge','totalreceivables','humancost','purchasecost','contractamount','file','remarks','vendorid','bankaccount','bankname','banknumber','banklist','bankcode','productservice','suppliercontractsid','havesignedcontract','signdate','productid','amountpayable')}
		{assign var=PRERECHARGE value=array('totalreceivables','vendorid','bankaccount','bankname','banknumber','banklist','bankcode','productservice','suppliercontractsid','havesignedcontract','signdate','productid','prestoreadrate','rechargeamount','discount','rebates','mstatus','rebatetype','remarks','paymentperiod','banklist','bankcode')}
		{assign var=OTHERPROCUREMENT value=array('vendorid','bankaccount','bankname','banknumber','banklist','bankcode','expecteddatepayment','expectedpaymentdeadline','beardepartment','bearratio','productservice','suppliercontractsid','havesignedcontract','signdate','productid','purchaseamount','purchaseprice','purchasequantity')}
		{assign var=NONMEDIAEXTRACTION value=array('servicecontractsid','accountid','totalrecharge','totalreceivables','expcashadvances','iscontracted','servicesigndate','grossadvances','file','remarks','vendorid','bankaccount','bankname','banknumber','banklist','bankcode','productservice','suppliercontractsid','havesignedcontract','signdate','productid','purchaseamount','contractamount','usecontractamount','totalgrossprofit','actualtotalrecharge','banklist','bankcode','paymentperiod')}
		{assign var=PACKVENDORS value=array('totalreceivables','vendorid','bankaccount','bankname','banknumber','banklist','bankcode','expecteddatepayment','expectedpaymentdeadline','remarks')}
    	{assign var=COINRETURN value=array('servicecontractsid','accountid','file','remarks','did','accountzh','productid','isprovideservice','accountrebatetype','discount','totalcashtransfer','totalcashin','totalturnoverofaccount','totaltransfertoaccount','cashtransfer','accounttransfer')}
    	{assign var=INCREASE value=array('mservicecontractsid','maccountid','file','remarks','cashconsumptiontotal','cashincreasetotal','mservicecontractsid','maccountid','mservicecontractsid_name','maccountid_name','cashgift','taxrefund','cashconsumption','cashincrease','grantquarter','mstatus','discount','accountrebatetype','receivementcurrencytype')}
	{foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE name="EditViewBlockLevelLoop"}
			{if $BLOCK_FIELDS|@count lte 0}{continue}{/if}
			{if $BLOCK_LABEL eq 'VENDOR_LBL_INFO'}{continue}{/if}
			{if $BLOCK_LABEL eq 'LBL_INFO'}{continue}{/if}
			<table class="table table-bordered blockContainer showInlineTable detailview-table {$BLOCK_LABEL} increase{$DATANUM}">
			<thead>
			<tr>
				<th class="blockHeader" colspan="4">
				<img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;"><img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">&nbsp;&nbsp;
                    {if $BLOCK_LABEL eq 'LBL_CUSTOM_INFORMATION' && $RECHARGESOURCE eq 'COINRETURN'}
						转出明细
						<b class="pull-right">
							<button class="btn btn-small turncashin" type="button" data-type="out">
								<span style="color:red;"><i class="icon-plus" title=""></i>转出</span>
							</button></b>
                    {elseif $BLOCK_LABEL eq 'LBL_CUSTOM_INFORMATION' && $RECHARGESOURCE eq 'INCREASE'}
						虚拟增款[{$DATANUM}]
						<b class="pull-right">
							<button class="btn btn-small delincrease" type="button" data-num="{$DATANUM}">
								<span style="color:red;"><i class="icon-minus" title=""></i>增款</span>
							</button></b>
                    {else}
                        {vtranslate($BLOCK_LABEL, $MODULE)}
                    {/if}
				</th>
			</tr>
			</thead>
			<tbody>
			<tr>

			{assign var=COUNTER value=0}
			{foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}
				{if !in_array($FIELD_MODEL->getFieldName(),$ACCOUNTS) && $RECHARGESOURCE eq 'Accounts'}{continue}{/if}
				{if !in_array($FIELD_MODEL->getFieldName(),$VENDORS) && $RECHARGESOURCE eq 'Vendors'}{continue}{/if}
				{if !in_array($FIELD_MODEL->getFieldName(),$TECHPROCUREMENT) && $RECHARGESOURCE eq 'TECHPROCUREMENT'}{continue}{/if}
				{if !in_array($FIELD_MODEL->getFieldName(),$PRERECHARGE) && $RECHARGESOURCE eq 'PreRecharge'}{continue}{/if}
				{if !in_array($FIELD_MODEL->getFieldName(),$OTHERPROCUREMENT) && $RECHARGESOURCE eq 'OtherProcurement'}{continue}{/if}
				{if !in_array($FIELD_MODEL->getFieldName(),$NONMEDIAEXTRACTION) && $RECHARGESOURCE eq 'NonMediaExtraction'}{continue}{/if}
                {if !in_array($FIELD_MODEL->getFieldName(),$PACKVENDORS) && $RECHARGESOURCE eq 'PACKVENDORS'}{continue}{/if}
                {if !in_array($FIELD_MODEL->getFieldName(),$COINRETURN) && $RECHARGESOURCE eq 'COINRETURN'}{continue}{/if}
                {if !in_array($FIELD_MODEL->getFieldName(),$INCREASE) && $RECHARGESOURCE eq 'INCREASE'}{continue}{/if}
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
				<td class="fieldLabel {$WIDTHTYPE}" title="{$FIELD_MODEL->get('prompt')}">
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
								<label class="muted pull-right marginRight10px">{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}{vtranslate($FIELD_MODEL->get('label'), $MODULE)}{if $FIELD_MODEL->get('prompt') neq ''}<span class="icon-question-sign"></span></label>{/if}
							{/if}
						{else if $FIELD_MODEL->get('uitype') eq "83"}
							{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) COUNTER=$COUNTER}
						{else}
							{vtranslate($FIELD_MODEL->get('label'), $MODULE)}{if $FIELD_MODEL->get('prompt') neq ''}<span class="icon-question-sign"></span>{/if}
						{/if}
					{if $isReferenceField neq "reference"}</label>{/if}
				</td>
				{if $FIELD_MODEL->get('uitype') neq "83"}
					<td class="fieldValue {$WIDTHTYPE}" {if $FIELD_MODEL->get('uitype') eq '19'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if} {if $FIELD_MODEL->get('uitype') eq '20'} colspan="3"{/if}>
						{if $FIELD_MODEL->getFieldName() eq 'did' && ($RECHARGESOURCE eq 'Accounts'||$RECHARGESOURCE eq 'Vendors'||$RECHARGESOURCE eq 'COINRETURN')}
                            {include file=vtemplate_path('../RefillApplication/uitypes/Picklist.tpl','RefillApplication') BLOCK_FIELDS=$BLOCK_FIELDS}
						{else}
                            {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateNameM(),$MODULE) BLOCK_FIELDS=$BLOCK_FIELDS}
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

{/strip}