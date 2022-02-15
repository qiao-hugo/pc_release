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
    {if $RECHARGESOURCE eq 'contractChanges'}
        {include file="DetailViewContractChanges.tpl"|vtemplate_path:$MODULE}
    {else}
        {if $RECEIVESTATUS}
            <div id="addrepayment">
                <table class="table table-bordered blockContainer  detailview-table">
                    <thead>
                    <tr>
                        <th class="blockHeader" colspan="4">
                            <img class="cursorPointer alignMiddle blockToggle hide " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;"><img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">&nbsp;&nbsp;关联回款信息<b class="pull-right"><button class="btn btn-small" type="button" id="add_newinvoicerayment"><i class="icon-plus" title="点击添加关联回款信息"></i></button></b>
                        </th>
                    </tr>
                    </thead>
                </table>
            </div>
        {/if}
    {assign var=ACCOUNTS value=array('refillapplicationno','workflowsid','modifiedtime','modifiedby','modulestatus','createdtime','smownerid','assigned_user_id','servicecontractsid','accountid','customertype','customeroriginattr','totalrecharge','actualtotalrecharge','expcashadvances','iscontracted','servicesigndate','grossadvances','file','remarks','did','accountzh','productid','isprovideservice','rechargetypedetail','receivementcurrencytype','exchangerate','prestoreadrate','rechargeamount','discount','tax','factorage','activationfee','taxation','totalcost','transferamount','servicecost','totalgrossprofit','mstatus','rebatetype','accountrebatetype','supprebate','flow_state','receivedstatus','invoicecircumstance')}
    {assign var=VENDORS value=array('paymentdate','totalreceivables','refillapplicationno','workflowsid','modifiedtime','modifiedby','modulestatus','createdtime','smownerid','assigned_user_id','servicecontractsid','accountid','customertype','customeroriginattr','totalrecharge','actualtotalrecharge','expcashadvances','iscontracted','servicesigndate','grossadvances','file','remarks','vendorid','bankaccount','bankname','banknumber','bankcode','suppliercontractsid','havesignedcontract','signdate','did','accountzh','productid','isprovideservice','rechargetypedetail','receivementcurrencytype','exchangerate','prestoreadrate','rechargeamount','discount','tax','factorage','activationfee','taxation','totalcost','transferamount','servicecost','totalgrossprofit','mstatus','rebatetype','accountrebatetype','paymentperiod','ispayment','supprebate','invoicecircumstance')}
    {assign var=TECHPROCUREMENT value=array('paymentdate','refillapplicationno','workflowsid','modifiedtime','modifiedby','modulestatus','createdtime','smownerid','assigned_user_id','salesorderid','servicecontractsid','accountid','totalrecharge','totalreceivables','humancost','purchasecost','contractamount','file','remarks','vendorid','bankaccount','bankname','banknumber','bankcode','suppliercontractsid','havesignedcontract','signdate','productid','amountpayable','invoicecircumstance')}
    {assign var=PRERECHARGE value=array('paymentdate','totalreceivables','refillapplicationno','workflowsid','modifiedtime','modifiedby','modulestatus','createdtime','smownerid','assigned_user_id','vendorid','bankaccount','bankname','banknumber','bankcode','suppliercontractsid','havesignedcontract','signdate','productid','prestoreadrate','rechargeamount','discount','rebates','mstatus','rebatetype','remarks','paymentperiod','ispayment','invoicecircumstance')}
    {assign var=OTHERPROCUREMENT value=array('refillapplicationno','workflowsid','modifiedtime','modifiedby','modulestatus','createdtime','smownerid','assigned_user_id','vendorid','bankaccount','bankname','banknumber','bankcode','expecteddatepayment','expectedpaymentdeadline','beardepartment','bearratio','suppliercontractsid','havesignedcontract','signdate','productid','purchaseamount','purchaseprice','purchasequantity','invoicecircumstance')}
    {assign var=NONMEDIAEXTRACTION value=array('paymentdate','refillapplicationno','workflowsid','modifiedtime','modifiedby','modulestatus','createdtime','smownerid','assigned_user_id','servicecontractsid','accountid','totalrecharge','totalreceivables','expcashadvances','iscontracted','servicesigndate','grossadvances','file','remarks','vendorid','bankaccount','bankname','banknumber','bankcode','suppliercontractsid','havesignedcontract','signdate','productid','purchaseamount','totalgrossprofit','actualtotalrecharge','paymentperiod','ispayment','nonaccountrebatetype','nonaccountrebate','invoicecircumstance','isthrowtime','throwtime')}
    {assign var=PACKVENDORS value=array('refillapplicationno','workflowsid','modifiedtime','modifiedby','modulestatus','createdtime','smownerid','assigned_user_id','totalreceivables','vendorid','bankaccount','bankname','banknumber','bankcode','expecteddatepayment','expectedpaymentdeadline','invoicecompany','remarks','actualtotalreceivables','invoicecircumstance')}
    {assign var=COINRETURN value=array('servicecontractsid','refillapplicationno','workflowsid','modifiedtime','modifiedby','accountid','file','modulestatus','createdtime','smownerid','assigned_user_id','totalcashtransfer','totalcashin','totalturnoverofaccount','totaltransfertoaccount','did','productid','remarks','topplatform','accountzh','accountrebatetype','isprovideservice','discount','cashtransfer','accounttransfer','turninorout','conversiontype','vendorid','invoicecircumstance')}
    {assign var=INCREASE value=array('refillapplicationno','workflowsid','modifiedtime','modifiedby','modulestatus','createdtime','smownerid','assigned_user_id','mservicecontractsid','maccountid','file','remarks','cashconsumptiontotal','cashincreasetotal','mservicecontractsid','maccountid','mservicecontractsid_name','maccountid_name','cashgift','taxrefund','cashconsumption','cashincrease','grantquarter','mstatus','discount','accountrebatetype','granttype','bankname','bankaccount','banknumber','bankcode','receivementcurrencytype','invoicecircumstance','invoicecircumstance')}

    {assign var=MODULEREFUNDFIELD value=array('Accounts','Vendors','TECHPROCUREMENT','NonMediaExtraction')}

    {assign var=ACCOUNTSREDBACK value=array('prestoreadrate','rechargeamount','refundamount','transferamount','servicecost','totalgrossprofit','mstatus','factorage','activationfee','taxation')}
    {assign var=VENDORSREDBACK value=array('prestoreadrate','rechargeamount','refundamount','transferamount','servicecost','totalgrossprofit','accountzh','mstatus','factorage','activationfee','taxation')}
    {assign var=NONMEDIAEXTRACTIONREDBACK value=array('prestoreadrate','rechargeamount','refundamount','transferamount','servicecost','totalgrossprofit','mstatus')}
    {*
    {assign var=SALESORDERLIST value=array('salesorderid','humancost','purchasecost','contractamount')}
    {assign var=TECHPROCUREMENT value=array('productservice','suppliercontractsid','havesignedcontract','havesignedcontract','transferamount','signdate','productid')}
    {assign var=PRERECHARGE value=array('productservice','suppliercontractsid','havesignedcontract','havesignedcontract','signdate','productid','rechargeamount','discount','prestoreadrate','mstatus','rebates')}
    {assign var=MODULEFLAG value=array('Vendors','TECHPROCUREMENT','PreRecharge')}
    {assign var=PRERECHARGELBL_BASIC value=array('servicecontractsid','accountid','customertype','customeroriginattr','totalrecharge','actualtotalrecharge','expcashadvances','remarks','file','servicesigndate','grossadvances','iscontracted')}
    {assign var=ACCOUNTSRETURN value=array('did','accountzh')}
    {assign var=MODULEFLAGFIELD value=array('productservice','suppliercontractsid','havesignedcontract','signdate')}
    {assign var=TECHPROCUREMENTFIELD value=array('customeroriginattr','customertype','expcashadvances')}
    {assign var=ACCOUNTVENDORS value=array('Accounts','Vendors')}
    {assign var=ACCOUNTVENDORSFIELD value=array('iscontracted','servicesigndate','grossadvances')}
    *}
    {assign var=TOTALGATHERI value=0}
    {assign var=TOTALADVANCES value=0}
    {assign var=TOTALRECHARGEAMOUNT value=0}
    {assign var=TOTALACCOUNTCURRENCY value=0}
    {assign var=TOTALCOST value=0}
    {assign var=TOTALMAORI value=0}

    {foreach key=BLOCK_LABEL_KEY item=FIELD_MODEL_LIST from=$RECORD_STRUCTURE}
        {if $BLOCK_LABEL_KEY eq 'LBL_CUSTOM_INFORMATION'}{assign var=OTHERFIELD_MODEL_LIST value=$FIELD_MODEL_LIST}{/if}
	{assign var=BLOCK value=$BLOCK_LIST[$BLOCK_LABEL_KEY]}
    {if $BLOCK_LABEL_KEY eq 'VENDOR_LBL_INFO' && in_array($RECHARGESOURCE,array('Accounts','COINRETURN'))}{continue}{/if}
    {if $BLOCK_LABEL_KEY eq 'VENDOR_LBL_INFO' && $RECHARGESOURCE eq 'INCREASE' && $RECORD->get('granttype') eq 'virtrefund'}{continue}{/if}
    {if $BLOCK_LABEL_KEY eq 'LBL_CUSTOM_INFORMATION' && $RECHARGESOURCE eq 'PACKVENDORS'}{continue}{/if}
	{if $BLOCK eq null or $FIELD_MODEL_LIST|@count lte 0}{continue}{/if}
	{assign var=IS_HIDDEN value=$BLOCK->isHidden()}
	{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
        	<input type=hidden name="timeFormatOptions" data-value='{$DAY_STARTS}' />
        <input type="hidden" name="rechargesource" value="{$RECORD->get('rechargesource')}" />
	<table class="table table-bordered equalSplit detailview-table {if (($WORKFLOWS['iscontract'] eq 0) && ($BLOCK_LABEL_KEY eq 'LBL_ADV')) || (($BLOCK_LABEL_KEY eq 'LBL_CUSTOM_INFORMATION') && ($WORKFLOWS['iscontract'] eq 1))} hide{/if}">
		<thead>
		<tr>
				<th class="blockHeader" colspan="4">
						<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id={$BLOCK_LIST[$BLOCK_LABEL_KEY]->get('id')}>
						<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id={$BLOCK_LIST[$BLOCK_LABEL_KEY]->get('id')}>
						&nbsp;&nbsp;{if $BLOCK_LABEL_KEY eq 'LBL_CUSTOM_INFORMATION' && $RECHARGESOURCE eq 'INCREASE'}
                            虚拟回款
                    {elseif $BLOCK_LABEL_KEY eq 'VENDOR_LBL_INFO' && $RECHARGESOURCE eq 'INCREASE'}
                            收款账户信息
                    {else}{vtranslate({$BLOCK_LABEL_KEY},{$MODULE_NAME})}{/if}
                    {if $BLOCK_LABEL_KEY eq 'LBL_CUSTOM_INFORMATION' && $REFUNDSORTRANSFERSTUTAS && $RECORD->getisbackwash($RECORD->get('rechargesheetid'))}
                        <b class="pull-right">
                        <button class="btn btn-small refundsOrTransfers" type="button" data-id="{$RECORD->get('rechargesheetid')}">
                            <span style="color:red;">红冲</span>
                        </button></b>{/if}
                    {if $BLOCK_LABEL_KEY eq 'LBL_CUSTOM_INFORMATION'}
                    <span class="label label-a_normal">1/{$RECHARGESHEETCOUNT}{if $RECHARGESOURCE eq 'COINRETURN'}&nbsp;转出{/if}</span>
                    {/if}&nbsp;&nbsp;{vtranslate($RECHARGESOURCE,$MODULE)}
				</th>
		</tr>
		</thead>
		 <tbody {if $IS_HIDDEN} class="hide" {/if}>
		{assign var=COUNTER value=0}
		<tr>
		{foreach item=FIELD_MODEL key=FIELD_NAME from=$FIELD_MODEL_LIST}
            {*{if in_array($FIELD_MODEL->getFieldName(),$ACCOUNTVENDORSFIELD) && !in_array($RECHARGESOURCE,$ACCOUNTVENDORS)}{continue}{/if}*}
            {if !$FIELD_MODEL->isViewableInDetailView()}
                {continue}
            {/if}
            {if $FIELD_MODEL->getFieldName() eq 'totalrecharge'}
                {$TOTALGATHERI=$FIELD_MODEL->get('fieldvalue')}
            {/if}
            {if $FIELD_MODEL->getFieldName() eq 'actualtotalrecharge'}
                {$TOTALRECHARGEAMOUNT=$FIELD_MODEL->get('fieldvalue')}
            {/if}

            {if $FIELD_MODEL->getFieldName() eq 'prestoreadrate'}
                {$TOTALACCOUNTCURRENCY=$TOTALACCOUNTCURRENCY+$FIELD_MODEL->get('fieldvalue')}
            {/if}
            {if $FIELD_MODEL->getFieldName() eq 'servicecost'}
                {$TOTALCOST=$TOTALCOST+$FIELD_MODEL->get('fieldvalue')}
            {/if}
            {if $FIELD_MODEL->getFieldName() eq 'totalgrossprofit'}
                {$TOTALMAORI=$TOTALMAORI+$FIELD_MODEL->get('fieldvalue')}
            {/if}
            {if !in_array($FIELD_MODEL->getFieldName(),$ACCOUNTS) && $RECHARGESOURCE eq 'Accounts'}{continue}{/if}
            {if !in_array($FIELD_MODEL->getFieldName(),$VENDORS) && $RECHARGESOURCE eq 'Vendors'}{continue}{/if}
            {if !in_array($FIELD_MODEL->getFieldName(),$TECHPROCUREMENT) && $RECHARGESOURCE eq 'TECHPROCUREMENT'}{continue}{/if}
            {if !in_array($FIELD_MODEL->getFieldName(),$PRERECHARGE) && $RECHARGESOURCE eq 'PreRecharge'}{continue}{/if}
            {if !in_array($FIELD_MODEL->getFieldName(),$OTHERPROCUREMENT) && $RECHARGESOURCE eq 'OtherProcurement'}{continue}{/if}
            {if !in_array($FIELD_MODEL->getFieldName(),$NONMEDIAEXTRACTION) && $RECHARGESOURCE eq 'NonMediaExtraction'}{continue}{/if}
            {if !in_array($FIELD_MODEL->getFieldName(),$PACKVENDORS) && $RECHARGESOURCE eq 'PACKVENDORS'}{continue}{/if}
            {if !in_array($FIELD_MODEL->getFieldName(),$COINRETURN) && $RECHARGESOURCE eq 'COINRETURN'}{continue}{/if}
            {if !in_array($FIELD_MODEL->getFieldName(),$INCREASE) && $RECHARGESOURCE eq 'INCREASE'}{continue}{/if}

            {*
            {if in_array($FIELD_MODEL->getFieldName(),$PRERECHARGELBL_BASIC) && $RECHARGESOURCE eq 'PreRecharge'}{continue}{/if}
            {if in_array($FIELD_MODEL->getFieldName(),$ACCOUNTSRETURN) && $RECHARGESOURCE neq 'Accounts'}{continue}{/if}
            {if $FIELD_MODEL->getFieldName() eq 'productservice'}{continue}{/if}
            {if $FIELD_MODEL->getFieldName() eq 'purchaseinvoiceid'}{continue}{/if}
            {if in_array($FIELD_MODEL->getFieldName(),$MODULEFLAGFIELD) && !in_array($RECHARGESOURCE,$MODULEFLAG)}{continue}{/if}
            {if in_array($FIELD_MODEL->getFieldName(),$TECHPROCUREMENTFIELD) && $RECHARGESOURCE eq 'TECHPROCUREMENT'}{continue}{/if}
            {if !in_array($FIELD_MODEL->getFieldName(),$TECHPROCUREMENT) && $RECHARGESOURCE eq 'TECHPROCUREMENT' && $BLOCK_LABEL_KEY eq 'LBL_CUSTOM_INFORMATION'}{continue}{/if}
            {if !in_array($FIELD_MODEL->getFieldName(),$PRERECHARGE) && $RECHARGESOURCE eq 'PreRecharge' && $BLOCK_LABEL_KEY eq 'LBL_CUSTOM_INFORMATION'}{continue}{/if}
            {if in_array($FIELD_MODEL->getFieldName(),$SALESORDERLIST) && $RECHARGESOURCE neq 'TECHPROCUREMENT'}{continue}{/if}
            {if $FIELD_MODEL->getFieldName() eq 'rebates' && $RECHARGESOURCE neq 'PreRecharge'}{continue}{/if}
            *}
            {if $FIELD_MODEL->get('uitype') eq "83"}
            {foreach item=tax key=count from=$TAXCLASS_DETAILS}
            {if $tax.check_value eq 1}
            {if $COUNTER eq 2}
        </tr><tr>
            {assign var="COUNTER" value=1}
            {else}
            {assign var="COUNTER" value=$COUNTER+1}
            {/if}
            <td class="fieldLabel {$WIDTHTYPE}">
                <label class='muted pull-right marginRight10px'  title="{$FIELD_MODEL->get('prompt')}">{vtranslate($tax.taxlabel, $MODULE)}(%)</label>
            </td>
            <td class="fieldValue {$WIDTHTYPE}">
						 <span class="value">
							 {$tax.percentage}
						 </span>
            </td>
            {/if}
            {/foreach}
            {else if $FIELD_MODEL->get('uitype') eq "69" || $FIELD_MODEL->get('uitype') eq "105"}
            {if $COUNTER neq 0}
            {if $COUNTER eq 2}
        </tr><tr>
            {assign var=COUNTER value=0}
            {/if}
            {/if}
            <td class="fieldLabel {$WIDTHTYPE}"  title="{$FIELD_MODEL->get('prompt')}"><label class="muted pull-right marginRight10px">{vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}{if $FIELD_MODEL->get('prompt') neq ''}<span class="icon-question-sign"></span></label>{/if}</label></td>
            <td class="fieldValue {$WIDTHTYPE}">
                <div id="imageContainer" width="300" height="200">
                    {foreach key=ITER item=IMAGE_INFO from=$IMAGE_DETAILS}
                        {if !empty($IMAGE_INFO.path) && !empty({$IMAGE_INFO.orgname})}
                            <img src="{$IMAGE_INFO.path}_{$IMAGE_INFO.orgname}" width="300" height="200">
                        {/if}
                    {/foreach}
                </div>
            </td>
            {assign var=COUNTER value=$COUNTER+1}
            {else}
            {if $FIELD_MODEL->get('uitype') eq "20" or $FIELD_MODEL->get('uitype') eq "19"}
            {if $COUNTER eq '1'}
            <td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td></tr><tr>
            {assign var=COUNTER value=0}
            {/if}
            {/if}
            {if $COUNTER eq 2}
        </tr><tr>
            {assign var=COUNTER value=1}
            {else}
            {assign var=COUNTER value=$COUNTER+1}
            {/if}
            <td class="fieldLabel {$WIDTHTYPE}" id="{$MODULE}_detailView_fieldLabel_{$FIELD_MODEL->getName()}"  title="{$FIELD_MODEL->get('prompt')}">
                <label class="muted pull-right marginRight10px">
                    {vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}
                    {if ($FIELD_MODEL->get('uitype') eq '72') && ($FIELD_MODEL->getName() eq 'unit_price')}
                        {$BASE_CURRENCY_SYMBOL}
                    {/if}{if $FIELD_MODEL->get('prompt') neq ''}<span class="icon-question-sign"></span></label>{/if}
                </label>
            </td>
            <td class="fieldValue {$WIDTHTYPE}" id="{$MODULE}_detailView_fieldValue_{$FIELD_MODEL->getName()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
					 <span class="value" data-field-type="{$FIELD_MODEL->getFieldDataType()}">
                        {if in_array($FIELD_MODEL->getFieldName(), ['actualtotalrecharge', 'totalrecharge', 'cashconsumptiontotal', 'cashincreasetotal', 'actualtotalreceivables', 'totalreceivables', 'contractamount', 'humancost', 'purchasecost', 'grossadvances', 'prestoreadrate', 'rechargeamount', 'totalcost', 'transferamount', 'servicecost', 'totalgrossprofit', 'amountpayable', 'purchaseamount', 'taxrefund', 'cashconsumption', 'cashincrease'])}
                            {number_format($FIELD_MODEL->get('fieldvalue'), 2)}
                        {else}
                            {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME RECORD=$RECORD}
                        {/if}
                        {if $FIELD_MODEL->name eq 'probability'}%{/if}
					 </span>

            </td>
            {/if}

            {if $FIELD_MODEL_LIST|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype" and $FIELD_MODEL->get('uitype') neq "69" and $FIELD_MODEL->get('uitype') neq "105"}
                <td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
            {/if}
		{/foreach}
		</tr>
         {if $BLOCK_LABEL_KEY eq 'LBL_CUSTOM_INFORMATION' && in_array($RECHARGESOURCE,$MODULEREFUNDFIELD)}
             {if !empty($REFUNDLIST)}
                   {assign var=FACTORAGE_COUNT value=0}
                     {assign var=ACTIVATION_COUNT value=0}
                       {assign var=TAXATION_COUNT value=0}
                        <input type="hidden" name="f1_tmp" value=""/>
                        <input type="hidden" name="a1_tmp" value=""/>
                        <input type="hidden" name="t1_tmp" value=""/>

                 {foreach item=REFUNDSIGN from=$REFUNDLIST[$RECORD->get('rechargesheetid')]}
                     <tr><th class="blockHeader" colspan="4">
                                {assign var="f_count" value=$f_count+$REFUNDSIGN['factorage']}
                     {assign var="a_count" value=$a_count+$REFUNDSIGN['activationfee']}
                       {assign var="t_count" value= $t_count+$REFUNDSIGN['taxation']}
                                <input type="hidden" name="factorage_tmp" value="{$f_count}"/>
                                 <input type="hidden" name="activationfee_tmp" value="{$a_count}"/>
                                  <input type="hidden" name="taxation_tmp" value="{$t_count}"/>
                             <span style="color:red;">红冲,退款</span>
                             {if $REFUNDSIGN['isbackwash'] eq 1 && $REFUNDSIGN['backwashstatus'] eq 1}
                                <input type="hidden" value="2" class="isbackwash"/>
                                {* <div style="position:relative;">
                                     <div style=" position:absolute;top:30%;right:50%;border:1px solid black;width:120px;line-height:1.3;text-align:center;color:red;border-radius:5px;font-size:24px;
            transform: rotate(40deg);
            -o-transform: rotate(40deg);
            -webkit-transform: rotate(40deg);
            -moz-transform: rotate(40deg);
            filter:progid:DXImageTransform.Microsoft.BasicImage(Rotation=2);">红冲,退款待提交</div> *}
                                 </div>
                             {elseif $REFUNDSIGN['isbackwash'] eq 1}
                                <input type="hidden" value="1" class="isbackwash"/>
                                {* <div style="position:relative;">
                                     <div style=" position:absolute;top:30%;right:50%;border:1px solid red;width:120px;line-height:1.3;text-align:center;color:red;border-radius:5px;font-size:24px;
            transform: rotate(40deg);
            -o-transform: rotate(40deg);
            -webkit-transform: rotate(40deg);
            -moz-transform: rotate(40deg);
            filter:progid:DXImageTransform.Microsoft.BasicImage(Rotation=2);">红冲,退款审核中</div>
                                 </div> *}
                             {/if}

                         </th>
                     </tr>

                     {assign var=COUNTER value=0}
                     <tr>

                     {foreach item=FIELD_MODEL key=FIELD_NAME from=$OTHERFIELD_MODEL_LIST}
                         {if !in_array($FIELD_MODEL->getFieldName(),$ACCOUNTSREDBACK) && $RECHARGESOURCE eq 'Accounts'}{continue}{/if}
                         {if !in_array($FIELD_MODEL->getFieldName(),$VENDORSREDBACK) && $RECHARGESOURCE eq 'Vendors'}{continue}{/if}
                         {if $FIELD_MODEL->get('uitype') eq "83"}
                             {foreach item=tax key=count from=$TAXCLASS_DETAILS}
                                 {if $tax.check_value eq 1}
                                     {if $COUNTER eq 2}
                                         </tr><tr>
                                         {assign var="COUNTER" value=1}
                                     {else}
                                         {assign var="COUNTER" value=$COUNTER+1}
                                     {/if}
                                     <td class="fieldLabel {$WIDTHTYPE}"  title="{$FIELD_MODEL->get('prompt')}">
                                         <label class='muted pull-right marginRight10px'>{vtranslate($tax.taxlabel, $MODULE)}(%){if $FIELD_MODEL->get('prompt') neq ''}<span class="icon-question-sign"></span></label>{/if}</label>
                                     </td>
                                     <td class="fieldValue {$WIDTHTYPE}">
						 <span class="value">
							 {$tax.percentage}
						 </span>
                                     </td>
                                 {/if}
                             {/foreach}
                         {else if $FIELD_MODEL->get('uitype') eq "69" || $FIELD_MODEL->get('uitype') eq "105"}
                             {if $COUNTER neq 0}
                                 {if $COUNTER eq 2}
                                     </tr><tr>
                                     {assign var=COUNTER value=0}
                                 {/if}
                             {/if}
                             <td class="fieldLabel {$WIDTHTYPE}" title="{$FIELD_MODEL->get('prompt')}"><label class="muted pull-right marginRight10px">{vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}{if $FIELD_MODEL->get('prompt') neq ''}<span class="icon-question-sign"></span></label>{/if}</label></td>
                             <td class="fieldValue {$WIDTHTYPE}">

                             </td>
                             {assign var=COUNTER value=$COUNTER+1}
                         {else}
                             {if $FIELD_MODEL->get('uitype') eq "20" or $FIELD_MODEL->get('uitype') eq "19"}
                                 {if $COUNTER eq '1'}
                                     <td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td></tr><tr>
                                     {assign var=COUNTER value=0}
                                 {/if}
                             {/if}
                             {if $COUNTER eq 2}
                                 </tr><tr>
                                 {assign var=COUNTER value=1}
                             {else}
                                 {assign var=COUNTER value=$COUNTER+1}
                             {/if}
                             <td class="fieldLabel {$WIDTHTYPE}" id="{$MODULE}_detailView_fieldLabel_{$FIELD_MODEL->getName()}" title="{$FIELD_MODEL->get('prompt')}">
                                 <label class="muted pull-right marginRight10px">
                                     {if $REFUNDSIGN['mstatus'] eq 'SupplierRefund'}[供应商退款]{/if}
                                     {if $FIELD_MODEL->getName() eq 'servicecost'}
                                     申请日期
                                     {elseif $FIELD_MODEL->getName() eq 'totalgrossprofit'}
                                     退款日期
                                     {elseif $FIELD_MODEL->getName() eq 'accountzh'}
                                     供应商退款金额
                                     {elseif $FIELD_MODEL->getName() eq 'transferamount'}
                                     退款金额
                                     {else}
                                         {vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}
                                         {if ($FIELD_MODEL->get('uitype') eq '72') && ($FIELD_MODEL->getName() eq 'unit_price')}
                                             {$BASE_CURRENCY_SYMBOL}
                                         {/if}
                                         {if $FIELD_MODEL->get('prompt') neq ''}<span class="icon-question-sign"></span></label>{/if}
                                     {/if}
                                 </label>
                             </td>
                             <td class="fieldValue {$WIDTHTYPE}" id="{$MODULE}_detailView_fieldValue_{$FIELD_MODEL->getName()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
					 <span class="value" data-field-type="{$FIELD_MODEL->getFieldDataType()}">
                            {if $FIELD_MODEL->get('uitype') eq 56}
                                {if $REFUNDSIGN[$FIELD_MODEL->getFieldName()] eq 1}是{else}无{/if}
                            {elseif in_array($FIELD_MODEL->get('uitype'),array(15,16))}
                                {vtranslate($REFUNDSIGN[$FIELD_MODEL->getFieldName()],$MODULE_NAME)}
                            {else}
                                {if $FIELD_MODEL->get('uitype') eq 56}
                                    {if $REFUNDSIGN[$FIELD_MODEL->getFieldName()] eq 1}是{else}无{/if}
                                 {elseif in_array($FIELD_MODEL->get('uitype'),array(15,16))}
                                     {vtranslate($REFUNDSIGN[$FIELD_MODEL->getFieldName()],$MODULE_NAME)}
                                 {elseif $FIELD_MODEL->getFieldName() eq 'servicecost'}
                                    <span style="color:red;">{$REFUNDSIGN['createdtime']}</span>
                                 {elseif $FIELD_MODEL->getFieldName() eq 'mstatus'}
                                    <span style="color:red;">{$REFUNDSIGN['remark']}</span>
                                 {elseif $FIELD_MODEL->getFieldName() eq 'totalgrossprofit'}
                                    <span style="color:red;">{$REFUNDSIGN['refundtime']}</span>
                                {else}
                                    {if $FIELD_MODEL->getFieldName() eq 'transferamount'}
                                        <span style="color:red;">-{$REFUNDSIGN['refundamount']}</span>
                                    {elseif $FIELD_MODEL->getFieldName() eq 'accountzh'}
                                        <span style="color:red;">-{$REFUNDSIGN['amountpayable']}</span>
                                    {else}
                                        <span style="color:red;">-{$REFUNDSIGN[$FIELD_MODEL->getFieldName()]}</span>
                                    {/if}
                                {/if}
                            {/if}

					 </span>

                             </td>
                         {/if}
                         {if $FIELD_MODEL_LIST|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype" and $FIELD_MODEL->get('uitype') neq "69" and $FIELD_MODEL->get('uitype') neq "105"}
                             <td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
                         {/if}
                     {/foreach}
                     </tr>
                 {/foreach}
             {/if}
             {if !empty($REFUNDLIST['refillredrefund'])}
                 {foreach item=REFUNDSIGNSUB from=$REFUNDLIST['refillredrefund'][$RECORD->get('rechargesheetid')]}
                     <tr><th class="blockHeader" colspan="4">
                             <span style="color:red;">退款明细</span>
                         </th>
                     </tr>
                     <tr>
                         <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">回款信息</label>
                         </td>
                         <td class="fieldValue medium"><span style="color:red;">{$REFUNDSIGNSUB['paytitle']}-*-{$REFUNDSIGNSUB['owncompany']}</span></td>
                         <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">入账金额</label>
                         </td>
                         <td class="fieldValue medium"><span style="color:red;">{number_format($REFUNDSIGNSUB['total'], 2)}</span></td>
                     </tr>
                     <tr>
                         <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">入账日期 </label>
                         </td>
                         <td class="fieldValue medium"><span style="color:red;">{$REFUNDSIGNSUB['arrivaldate']}</span></td>
                         <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">可使用金额</label>
                         </td>
                         <td class="fieldValue medium"><span style="color:red;">{number_format($REFUNDSIGNSUB['allowrefillapptotal'], 2)}</span></td>
                     </tr>
                     <tr>
                         <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">使用金额</label>
                         </td>
                         <td class="fieldValue medium"><span style="color:red;">{number_format($REFUNDSIGNSUB['refillapptotal'], 2)}</span></td>
                         <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">退款金额</label>
                         </td>
                         <td class="fieldValue medium"><span style="color:red;">{number_format($REFUNDSIGNSUB['tempbackwashtotal'], 2)}</span></td>
                     </tr>
                 {/foreach}
             {/if}


            {/if}
		</tbody>
	</table>
	<br>
	{/foreach}
    {$TOTALADVANCES=$TOTALRECHARGEAMOUNT-$TOTALGATHERI}

        {foreach key=row_no item=data from=$C_RECHARGESHEET}
                {assign var=BLOCK_LABEL_KEY value='LBL_CUSTOM_INFORMATION'}
                <table class="table table-bordered equalSplit detailview-table {if (($WORKFLOWS['iscontract'] eq 0) && ($BLOCK_LABEL_KEY eq 'LBL_ADV')) || (($BLOCK_LABEL_KEY eq 'LBL_CUSTOM_INFORMATION') && ($WORKFLOWS['iscontract'] eq 1))} hide{/if}">
                    <thead>
                    <tr>
                        <th class="blockHeader" colspan="4">
                            <img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id={$BLOCK_LIST[$BLOCK_LABEL_KEY]->get('id')}>
                            <img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id={$BLOCK_LIST[$BLOCK_LABEL_KEY]->get('id')}>
                            &nbsp;&nbsp;{if $RECHARGESOURCE neq 'INCREASE'}{vtranslate({$BLOCK_LABEL_KEY},{$MODULE_NAME})}{else}虚拟回款{/if}{if $RECHARGESOURCE neq 'COINRETURN'}<span class="label label-a_normal">{$row_no+2}/{$RECHARGESHEETCOUNT}</span>
                                {else}
                                    {if $data['turninorout'] eq 'in'}
                                        <span class="label label-success">{$data['seqnum']}/{$INCOUNT}&nbsp;转入</span>
                                        {if $REFUNDSORTRANSFERSTUTAS && $RECORD->getisbackwash($data['rechargesheetid'])}
                                        <b class="pull-right">
                                            <button class="btn btn-small refundsOrTransfers" type="button" data-id="{$data['rechargesheetid']}">
                                                <span style="color:red;">红冲</span>
                                            </button></b>
                                        {/if}
                                    {else}

                                <span class="label label-a_normal">{$data['seqnum']}/{$RECHARGESHEETCOUNT}&nbsp;转出</span>
                                    {/if}
                                {/if}&nbsp;{vtranslate($RECHARGESOURCE,$MODULE)}
                            {if $REFUNDSORTRANSFERSTUTAS && $RECORD->getisbackwash($data['rechargesheetid']) && $RECHARGESOURCE neq 'COINRETURN'}
                            <b class="pull-right">
                                <button class="btn btn-small refundsOrTransfers" type="button" data-id="{$data['rechargesheetid']}">
                                    <span style="color:red;">红冲</span>
                                </button></b>{/if}&nbsp;

                        </th>
                    </tr>
                    </thead>
                    <tbody {if $IS_HIDDEN} class="hide" {/if}>
                    {assign var=COUNTER value=0}
                    <tr>

                        {assign var=SALESORDERLIST value=array('salesorderid','humancost','purchasecost','contractamount')}
                        {*{assign var=TECHPROCUREMENT value=array('productservice','suppliercontractsid','havesignedcontract','havesignedcontract','transferamount','signdate','productid')}
                        {assign var=PRERECHARGE value=array('productservice','suppliercontractsid','havesignedcontract','havesignedcontract','signdate','productid','rechargeamount','discount','prestoreadrate','mstatus','rebates')}*}
                        {assign var=MODULEFLAG value=array('Vendors','TECHPROCUREMENT','PreRecharge')}
                        {assign var=FIELDADDNAME value=array('mservicecontractsid','maccountid')}
                        {assign var=PRERECHARGELBL_BASIC value=array('servicecontractsid','accountid','customertype','customeroriginattr','totalrecharge','actualtotalrecharge','expcashadvances','remarks','file')}
                        {foreach item=FIELD_MODEL key=FIELD_NAME from=$OTHERFIELD_MODEL_LIST}
                        {if !$FIELD_MODEL->isViewableInDetailView()}
                            {continue}
                        {/if}
                        {if $FIELD_MODEL->getFieldName() eq 'prestoreadrate'}
                            {$TOTALACCOUNTCURRENCY=$TOTALACCOUNTCURRENCY+$data[$FIELD_MODEL->getFieldName()]}
                        {/if}
                        {if $FIELD_MODEL->getFieldName() eq 'servicecost'}
                            {$TOTALCOST=$TOTALCOST+$data[$FIELD_MODEL->getFieldName()]}
                        {/if}
                        {if $FIELD_MODEL->getFieldName() eq 'totalgrossprofit'}
                            {$TOTALMAORI=$TOTALMAORI+$data[$FIELD_MODEL->getFieldName()]}
                        {/if}
                        {*
                        {if in_array($FIELD_MODEL->getFieldName(),$PRERECHARGELBL_BASIC) && $RECHARGESOURCE eq 'PreRecharge'}{continue}{/if}
                        {if $FIELD_MODEL->getFieldName() eq 'did' && $RECHARGESOURCE neq 'Accounts'}{continue}{/if}
                        {if $FIELD_MODEL->getFieldName() eq 'accountzh' && $RECHARGESOURCE neq 'Accounts'}{continue}{/if}
                        {if $FIELD_MODEL->getFieldName() eq 'productservice'}{continue}{/if}
                        {if $FIELD_MODEL->getFieldName() eq 'purchaseinvoiceid'}{continue}{/if}
                        {if $FIELD_MODEL->getFieldName() eq 'productservice' && !in_array($RECHARGESOURCE,$MODULEFLAG)}{continue}{/if}
                        {if $FIELD_MODEL->getFieldName() eq 'suppliercontractsid' && !in_array($RECHARGESOURCE,$MODULEFLAG)}{continue}{/if}
                        {if $FIELD_MODEL->getFieldName() eq 'havesignedcontract' && !in_array($RECHARGESOURCE,$MODULEFLAG)}{continue}{/if}
                        {if $FIELD_MODEL->getFieldName() eq 'signdate' && !in_array($RECHARGESOURCE,$MODULEFLAG)}{continue}{/if}
                        {if $FIELD_MODEL->getFieldName() eq 'customeroriginattr' && $RECHARGESOURCE eq 'TECHPROCUREMENT'}{continue}{/if}
                        {if $FIELD_MODEL->getFieldName() eq 'customertype' && $RECHARGESOURCE eq 'TECHPROCUREMENT'}{continue}{/if}
                        {if $FIELD_MODEL->getFieldName() eq 'expcashadvances' && $RECHARGESOURCE eq 'TECHPROCUREMENT'}{continue}{/if}
                        {if !in_array($FIELD_MODEL->getFieldName(),$TECHPROCUREMENT) && $RECHARGESOURCE eq 'TECHPROCUREMENT' && $BLOCK_LABEL_KEY eq 'LBL_CUSTOM_INFORMATION'}{continue}{/if}
                        {if !in_array($FIELD_MODEL->getFieldName(),$PRERECHARGE) && $RECHARGESOURCE eq 'PreRecharge' && $BLOCK_LABEL_KEY eq 'LBL_CUSTOM_INFORMATION'}{continue}{/if}
                        {if in_array($FIELD_MODEL->getFieldName(),$SALESORDERLIST) && $RECHARGESOURCE neq 'TECHPROCUREMENT'}{continue}{/if}
                        {if $FIELD_MODEL->getFieldName() eq 'rebates' && $RECHARGESOURCE neq 'PreRecharge'}{continue}{/if}
                        *}
                        {if !in_array($FIELD_MODEL->getFieldName(),$ACCOUNTS) && $RECHARGESOURCE eq 'Accounts'}{continue}{/if}
                        {if !in_array($FIELD_MODEL->getFieldName(),$VENDORS) && $RECHARGESOURCE eq 'Vendors'}{continue}{/if}
                        {if !in_array($FIELD_MODEL->getFieldName(),$TECHPROCUREMENT) && $RECHARGESOURCE eq 'TECHPROCUREMENT'}{continue}{/if}
                        {if !in_array($FIELD_MODEL->getFieldName(),$PRERECHARGE) && $RECHARGESOURCE eq 'PreRecharge'}{continue}{/if}
                        {if !in_array($FIELD_MODEL->getFieldName(),$OTHERPROCUREMENT) && $RECHARGESOURCE eq 'OtherProcurement'}{continue}{/if}
                        {if !in_array($FIELD_MODEL->getFieldName(),$NONMEDIAEXTRACTION) && $RECHARGESOURCE eq 'NonMediaExtraction'}{continue}{/if}
                        {if !in_array($FIELD_MODEL->getFieldName(),$COINRETURN) && $RECHARGESOURCE eq 'COINRETURN'}{continue}{/if}
                        {if !in_array($FIELD_MODEL->getFieldName(),$INCREASE) && $RECHARGESOURCE eq 'INCREASE'}{continue}{/if}
                        {if $FIELD_MODEL->get('uitype') eq "83"}
                        {foreach item=tax key=count from=$TAXCLASS_DETAILS}
                        {if $tax.check_value eq 1}
                        {if $COUNTER eq 2}
                    </tr><tr>
                        {assign var="COUNTER" value=1}
                        {else}
                        {assign var="COUNTER" value=$COUNTER+1}
                        {/if}
                        <td class="fieldLabel {$WIDTHTYPE}"  title="{$FIELD_MODEL->get('prompt')}">
                            <label class='muted pull-right marginRight10px'>{vtranslate($tax.taxlabel, $MODULE)}(%)</label>
                        </td>
                        <td class="fieldValue {$WIDTHTYPE}">
						 <span class="value">
							 {$tax.percentage}
						 </span>
                        </td>
                        {/if}
                        {/foreach}
                        {else if $FIELD_MODEL->get('uitype') eq "69" || $FIELD_MODEL->get('uitype') eq "105"}
                        {if $COUNTER neq 0}
                        {if $COUNTER eq 2}
                    </tr><tr>
                        {assign var=COUNTER value=0}
                        {/if}
                        {/if}
                        <td class="fieldLabel {$WIDTHTYPE}"><label class="muted pull-right marginRight10px">{vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}</label></td>
                        <td class="fieldValue {$WIDTHTYPE}">
                            <div id="imageContainer" width="300" height="200">
                                {foreach key=ITER item=IMAGE_INFO from=$IMAGE_DETAILS}
                                    {if !empty($IMAGE_INFO.path) && !empty({$IMAGE_INFO.orgname})}
                                        <img src="{$IMAGE_INFO.path}_{$IMAGE_INFO.orgname}" width="300" height="200">
                                    {/if}
                                {/foreach}
                            </div>
                        </td>
                        {assign var=COUNTER value=$COUNTER+1}
                        {else}
                        {if $FIELD_MODEL->get('uitype') eq "20" or $FIELD_MODEL->get('uitype') eq "19"}
                        {if $COUNTER eq '1'}
                        <td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td></tr><tr>
                        {assign var=COUNTER value=0}
                        {/if}
                        {/if}
                        {if $COUNTER eq 2}
                    </tr><tr>
                        {assign var=COUNTER value=1}
                        {else}
                        {assign var=COUNTER value=$COUNTER+1}
                        {/if}
                        <td class="fieldLabel {$WIDTHTYPE}" id="{$MODULE}_detailView_fieldLabel_{$FIELD_MODEL->getName()}">
                            <label class="muted pull-right marginRight10px">
                                {if $RECHARGESOURCE neq 'COINRETURN'}
                                    {vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}
                                {else}
                                    {if in_array($FIELD_MODEL->get('label'),array('cashtransfer','accounttransfer'))}
                                        {assign var=LABELNAME value=$FIELD_MODEL->get('label')|cat:$data['turninorout']}
                                        {vtranslate($LABELNAME,{$MODULE_NAME})}
                                    {else}
                                        {vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}
                                    {/if}
                                {/if}
                                {if ($FIELD_MODEL->get('uitype') eq '72') && ($FIELD_MODEL->getName() eq 'unit_price')}
                                    {$BASE_CURRENCY_SYMBOL}
                                {/if}
                            </label>
                        </td>
                        <td class="fieldValue {$WIDTHTYPE}" id="{$MODULE}_detailView_fieldValue_{$FIELD_MODEL->getName()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
					 <span class="value" data-field-type="{$FIELD_MODEL->getFieldDataType()}">
                        {if !in_array($FIELD_MODEL->getFieldName(),$DISPLAYFIELD)}
                            {if $FIELD_MODEL->get('uitype') eq 56}
                             {if $data[$FIELD_MODEL->getFieldName()] eq 1}是{else}无{/if}
                            {elseif in_array($FIELD_MODEL->get('uitype'),array(15,16))}
                             {vtranslate($data[$FIELD_MODEL->getFieldName()],$MODULE_NAME)}
                            {elseif in_array($FIELD_MODEL->getFieldName(),$FIELDADDNAME)}
                                {$data[$FIELD_MODEL->getFieldName()|cat:'_name']}
                            {elseif in_array($FIELD_MODEL->getFieldName(), ['prestoreadrate', 'rechargeamount', 'totalcost', 'transferamount', 'servicecost', 'totalgrossprofit', 'amountpayable', 'purchaseamount', 'taxrefund', 'cashconsumption', 'cashincrease'])}
                                {number_format($data[$FIELD_MODEL->getFieldName()], 2)}
                            {else}
                                {$data[$FIELD_MODEL->getFieldName()]}
                            {/if}
                        {else}
                             {$data[$DISPLAYVALUE[$FIELD_MODEL->getFieldName()]]}
                        {/if}
					 </span>

                        </td>
                        {/if}

                        {if $FIELD_MODEL_LIST|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype" and $FIELD_MODEL->get('uitype') neq "69" and $FIELD_MODEL->get('uitype') neq "105"}
                            <td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
                        {/if}
                        {/foreach}
                    </tr>
                    {if !empty($REFUNDLIST)}
                    {foreach item=REFUNDSIGN from=$REFUNDLIST[$data['rechargesheetid']]}
                    <tr><th class="blockHeader" colspan="4">
                            <span style="color:red;">红冲,退款</span>
                            {if $REFUNDSIGN['isbackwash'] eq 1 && $REFUNDSIGN['backwashstatus'] eq 1}
                                <input type="hidden" value="2" class="isbackwash"/>
                                {* <div style="position:relative;">
                                    <div style=" position:absolute;top:30%;right:50%;border:1px solid black;width:120px;line-height:1.3;text-align:center;color:red;border-radius:5px;font-size:24px;
            transform: rotate(40deg);
            -o-transform: rotate(40deg);
            -webkit-transform: rotate(40deg);
            -moz-transform: rotate(40deg);
            filter:progid:DXImageTransform.Microsoft.BasicImage(Rotation=2);">红冲,退款待提交</div> *}
                                </div>
                            {elseif $REFUNDSIGN['isbackwash'] eq 1}
                                <input type="hidden" value="1" class="isbackwash"/>
                                {* <div style="position:relative;">
                                    <div style=" position:absolute;top:30%;right:50%;border:1px solid red;width:120px;line-height:1.3;text-align:center;color:red;border-radius:5px;font-size:24px;
            transform: rotate(40deg);
            -o-transform: rotate(40deg);
            -webkit-transform: rotate(40deg);
            -moz-transform: rotate(40deg);
            filter:progid:DXImageTransform.Microsoft.BasicImage(Rotation=2);">红冲,退款审核中</div>
                                </div> *}
                            {/if}
                        </th>
                    </tr>

                    {assign var=COUNTER value=0}
                    <tr>

                        {foreach item=FIELD_MODEL key=FIELD_NAME from=$OTHERFIELD_MODEL_LIST}
                        {if !in_array($FIELD_MODEL->getFieldName(),$ACCOUNTSREDBACK) && $RECHARGESOURCE eq 'Accounts'}{continue}{/if}
                        {if !in_array($FIELD_MODEL->getFieldName(),$VENDORSREDBACK) && $RECHARGESOURCE eq 'Vendors'}{continue}{/if}
                        {if $FIELD_MODEL->get('uitype') eq "83"}
                        {foreach item=tax key=count from=$TAXCLASS_DETAILS}
                        {if $tax.check_value eq 1}
                        {if $COUNTER eq 2}
                    </tr><tr>
                        {assign var="COUNTER" value=1}
                        {else}
                        {assign var="COUNTER" value=$COUNTER+1}
                        {/if}
                        <td class="fieldLabel {$WIDTHTYPE}">
                            <label class='muted pull-right marginRight10px'>{vtranslate($tax.taxlabel, $MODULE)}(%)</label>
                        </td>
                        <td class="fieldValue {$WIDTHTYPE}">
						 <span class="value">
							 {$tax.percentage}
						 </span>
                        </td>
                        {/if}
                        {/foreach}
                        {else if $FIELD_MODEL->get('uitype') eq "69" || $FIELD_MODEL->get('uitype') eq "105"}
                        {if $COUNTER neq 0}
                        {if $COUNTER eq 2}
                    </tr><tr>
                        {assign var=COUNTER value=0}
                        {/if}
                        {/if}
                        <td class="fieldLabel {$WIDTHTYPE}"><label class="muted pull-right marginRight10px">{vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}</label></td>
                        <td class="fieldValue {$WIDTHTYPE}">

                        </td>
                        {assign var=COUNTER value=$COUNTER+1}
                        {else}
                        {if $FIELD_MODEL->get('uitype') eq "20" or $FIELD_MODEL->get('uitype') eq "19"}
                        {if $COUNTER eq '1'}
                        <td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td></tr><tr>
                        {assign var=COUNTER value=0}
                        {/if}
                        {/if}
                        {if $COUNTER eq 2}
                    </tr><tr>
                        {assign var=COUNTER value=1}
                        {else}
                        {assign var=COUNTER value=$COUNTER+1}
                        {/if}
                        <td class="fieldLabel {$WIDTHTYPE}" id="{$MODULE}_detailView_fieldLabel_{$FIELD_MODEL->getName()}">
                            <label class="muted pull-right marginRight10px">
                            {if $FIELD_MODEL->getName() eq 'servicecost'}
                                申请日期
                            {elseif $FIELD_MODEL->getName() eq 'totalgrossprofit'}
                                退款日期
                            {elseif $FIELD_MODEL->getName() eq 'accountzh'}
                                供应商退款金额
                            {elseif $FIELD_MODEL->getName() eq 'transferamount'}
                                退款金额
                            {else}
                                {vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}
                                {if ($FIELD_MODEL->get('uitype') eq '72') && ($FIELD_MODEL->getName() eq 'unit_price')}
                                    {$BASE_CURRENCY_SYMBOL}
                                {/if}
                            {/if}
                            </label>
                        </td>
                        <td class="fieldValue {$WIDTHTYPE}" id="{$MODULE}_detailView_fieldValue_{$FIELD_MODEL->getName()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
					 <span class="value" data-field-type="{$FIELD_MODEL->getFieldDataType()}">

                            {if $FIELD_MODEL->get('uitype') eq 56}
                                {if $REFUNDSIGN[$FIELD_MODEL->getFieldName()] eq 1}是{else}无{/if}
                            {elseif in_array($FIELD_MODEL->get('uitype'),array(15,16))}
                                {vtranslate($REFUNDSIGN[$FIELD_MODEL->getFieldName()],$MODULE_NAME)}
                            {elseif $FIELD_MODEL->getFieldName() eq 'servicecost'}
                                <span style="color:red;">{$REFUNDSIGN['createdtime']}</span>
                            {elseif $FIELD_MODEL->getFieldName() eq 'mstatus'}
                                <span style="color:red;">{$REFUNDSIGN['remark']}</span>
                            {elseif $FIELD_MODEL->getFieldName() eq 'totalgrossprofit'}
                                <span style="color:red;">{$REFUNDSIGN['refundtime']}</span>
                            {else}
                                {if $FIELD_MODEL->getFieldName() eq 'transferamount'}
                                <span style="color:red;">-{$REFUNDSIGN['refundamount']}</span>
                                {elseif $FIELD_MODEL->getFieldName() eq 'accountzh'}
                                <span style="color:red;">-{$REFUNDSIGN['amountpayable']}</span>
                                {else}
                                <span style="color:red;">-{$REFUNDSIGN[$FIELD_MODEL->getFieldName()]}</span>
                                    {/if}
                            {/if}

					 </span>

                            </td>
                            {/if}
                            {if $FIELD_MODEL_LIST|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype" and $FIELD_MODEL->get('uitype') neq "69" and $FIELD_MODEL->get('uitype') neq "105"}
                                <td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
                            {/if}
                            {/foreach}
                        </tr>
                            {/foreach}
                        {/if}
                        {if !empty($REFUNDLIST['refillredrefund'])}
                            {foreach item=REFUNDSIGNSUB from=$REFUNDLIST['refillredrefund'][$data['rechargesheetid']]}
                                <tr><th class="blockHeader" colspan="4">
                                        <span style="color:red;">退款明细</span>
                                    </th>
                                </tr>
                                <tr>
                                    <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">回款信息</label>
                                    </td>
                                    <td class="fieldValue medium"><span style="color:red;">{$REFUNDSIGNSUB['paytitle']}-*-{$REFUNDSIGNSUB['owncompany']}</span></td>
                                    <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">入账金额</label>
                                    </td>
                                    <td class="fieldValue medium"><span style="color:red;">{number_format($REFUNDSIGNSUB['total'], 2)}</span></td>
                                </tr>
                                <tr>
                                    <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">入账日期 </label>
                                    </td>
                                    <td class="fieldValue medium"><span style="color:red;">{$REFUNDSIGNSUB['arrivaldate']}</span></td>
                                    <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">可使用金额</label>
                                    </td>
                                    <td class="fieldValue medium"><span style="color:red;">{number_format($REFUNDSIGNSUB['allowrefillapptotal'], 2)}</span></td>
                                </tr>
                                <tr>
                                    <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">使用金额</label>
                                    </td>
                                    <td class="fieldValue medium"><span style="color:red;">{number_format($REFUNDSIGNSUB['refillapptotal'], 2)}</span></td>
                                    <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">退款金额</label>
                                    </td>
                                    <td class="fieldValue medium"><span style="color:red;">{number_format($REFUNDSIGNSUB['tempbackwashtotal'], 2)}</span></td>
                                </tr>
                            {/foreach}
                        {/if}
                        </tbody>
                    </table>
                    <br>
            {/foreach}
        {*{/if}*}
        {if in_array($RECHARGESOURCE,$MODULEREFUNDFIELD) &&!empty($PAYMENTSLIST)}
            {if $RECHARGESOURCE neq 'TECHPROCUREMENT' && $USERID eq 10710 }{else}
            {foreach key=KEYINDEX item=D from=$PAYMENTSLIST}
                <table class="table table-bordered equalSplit detailview-table " data-num="{$KEYINDEX + 1}">
                    <thead>
                    <tr>
                        <th class="blockHeader" colspan="4">
                                {if $D['receivedstatus'] eq 'SupplierRefund'}
                                    供应商退款关联
                                {else}
                                    {if $RECHARGESOURCE eq 'TECHPROCUREMENT'}工单产品{else}关联回款信息(申请人录入){/if}[{$KEYINDEX + 1}]
                                    {if $REVOKERELATION}
                                        <b class="pull-right">
                                        <button class="btn btn-small doRevokeRelation" type="button" data-refillappraymentid="{$D['refillappraymentid']}" data-record="{$D['refillapplicationid']}" title="点击解除关联回款信息">
                                            <span style="color:red;">解除关联</span>
                                        </button></b>
                                    {/if}
                                {/if}
                            {if $D['receivedstatus'] eq 'revokerelation'}
                                <div style="position:relative;">
                                    <div style=" position:absolute;top:75px;right:50%;border:1px solid red;width:90px;line-height:1.3;text-align:center;color:red;border-radius:5px;font-size:24px;
                transform: rotate(40deg);
                -o-transform: rotate(40deg);
                -webkit-transform: rotate(40deg);
                -moz-transform: rotate(40deg);
                filter:progid:DXImageTransform.Microsoft.BasicImage(Rotation=2);">撤销中</div>
                                </div>
                            {/if}

                        &nbsp;&nbsp;
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{if $RECHARGESOURCE eq 'TECHPROCUREMENT'}产品信息{else}回款信息{/if} </label>
                    </td>
                    <td class="fieldValue medium">{$D['paytitle']}-*-{$D['owncompany']}</td>
                    <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{if $RECHARGESOURCE eq 'TECHPROCUREMENT'}外采成本{else}入账金额{/if}</label>
                    </td>
                    <td class="fieldValue medium">{number_format($D['total'], 2)}</td>
                </tr>
                <tr>
                    <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">入账日期 </label>
                    </td>
                    <td class="fieldValue medium">{$D['arrivaldate']}</td>
                    <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{if $RECHARGESOURCE eq 'TECHPROCUREMENT'}可使用外采金额{else}可使用金额{/if}</label>
                    </td>
                    <td class="fieldValue medium">{number_format($D['allowrefillapptotal'], 2)}</td>
                </tr>
                <tr>
                    <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">{if $RECHARGESOURCE eq 'TECHPROCUREMENT'}使用外采金额{else}使用金额{/if} </label>
                    </td>
                    <td class="fieldValue medium">{number_format($D['refillapptotal'], 2)}</td>
                    <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">退款金额</label>
                    </td>
                    <td class="fieldValue medium">{number_format($D['refundamount'], 2)}</td>
                </tr>
                <tr>
                    <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">回款类型 </label>
                    </td>
                    <td class="fieldValue medium" colspan="3">{vtranslate($D['rreceivedstatus'],"ReceivedPayments")}</td>

                </tr>
                <tr>
                    <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">备注 </label>
                    </td>
                    <td class="fieldValue medium" colspan="3">{$D['remarks']}</td>

                    </tr>
                    <!--<tr>
                        <td class="fieldLabel medium">
                            <label class="muted pull-right marginRight10px"> 回款信息</label>
                            <input type="hidden" name="updateii[]" value="{$D['newinvoiceraymentid']}">
                            <input class="t_tab_newinvoicerayment_id" type="hidden" value="{$D['newinvoiceraymentid']}">
                        </td>
                        <td class="fieldValue medium">
                            <div class="row-fluid">
                        <span class="span10">
                        {$D['paytitle']}
                        </span>
                            </div>
                        </td>
                        <td class="fieldLabel medium">
                            <label class="muted pull-right marginRight10px"> 入账金额</label>
                        </td>
                        <td class="fieldValue medium">
                            <div class="row-fluid">
                                <span class="span10">{$D['total']}</span>
                            </div>
                        </td>
                    </tr>-->
                    </tbody>
                </table>
            {/foreach}
            {/if}
        {/if}
        {assign var="RECHARGEARR" value=array('Accounts','Vendors')}
        {if false && in_array($RECHARGESOURCE,$RECHARGEARR)}
        <table class="table table-bordered equalSplit detailview-table " data-num="{$row_no+1}">
            <thead>
            <tr>
                <th class="blockHeader" colspan="4"><img class="cursorPointer alignMiddle blockToggle  hide" src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;"> <img class="cursorPointer alignMiddle blockToggle" src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;"> &nbsp;&nbsp; 合计信息&nbsp;&nbsp;<b class="pull-right">

                    </b></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">
                        合计收款金额
                    </label>
                </td>
                <td class="fieldValue medium">
                    <input type="text" class="input-large" name="totalgatheri" readonly="readonly" value="{$TOTALGATHERI}"/>
                </td>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">
                        合计垫款金额
                    </label>
                </td>
                <td class="fieldValue medium">
                    <input type="text" class="input-large" name="totaladvances" readonly="readonly" value="{$TOTALADVANCES}"/>
                </td>
            </tr>
            <tr>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">
                        合计账户充值金额
                    </label>
                </td>
                <td class="fieldValue medium">
                    <input type="text" class="input-large" name="totalrechargeamount" readonly="readonly" value="{$TOTALRECHARGEAMOUNT}"/>
                </td>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">
                        合计充值账户币
                    </label>
                </td>
                <td class="fieldValue medium">
                    <input type="text" class="input-large" name="totalaccountcurrency" readonly="readonly" value="{$TOTALACCOUNTCURRENCY}"/>
                </td>
            </tr>
            <tr>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">
                        合计成本
                    </label>
                </td>
                <td class="fieldValue medium">
                    <input type="text" class="input-large" name="totalcost" readonly="readonly" value="{$TOTALCOST}"/>
                </td>
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">
                        合计毛利
                    </label>
                </td>
                <td class="fieldValue medium">
                    <input type="text" class="input-large" name="totalmaori" readonly="readonly" value="{$TOTALMAORI}"/>
                </td>
            </tr>
            </tbody>
        </table>
        {/if}
        {if $RECHARGESOURCE eq 'PACKVENDORS' && !empty($VENDORLIST)}
            <table class="table table-bordered detailview-table">
            <thead>
            <tr>
                <th><label class="muted">申请单编号</label></th>
                <th><label class="muted"><span class="redColor"></span> 服务合同</label></th>
                <th><label class="muted"><span class="redColor"></span> 客户</label></th>
                <th><label class="muted"><span class="redColor"></span> 申请人</label></th>
                <th><label class="muted"><span class="redColor"></span> 充值来源</label></th>
                <th><label class="muted"><span class="redColor"></span> 应收款总额</label></th>
                <th><label class="muted"><span class="redColor"></span> 应付款金额</label></th>
                <th><label class="muted"><span class="redColor"></span> 申请时间</label></th>
                <th><label class="muted"><span class="redColor"></span> 备注</label></th></tr></thead><tbody>
                {foreach key=KEYINDEX item=DA from=$VENDORLIST}
                <tr>
                <td><div class="row-fluid"><span class="span10"><a href="/index.php?module=RefillApplication&view=Detail&record={$DA.refillapplicationid}" target="_blank">{$DA.refillapplicationno}</a></span></div></td>
                <td><div class="row-fluid"><span class="span10">{$DA['contract_no']}</span></div></td>
                <td><div class="row-fluid"><span class="span10">{$DA['accountname']}</span></div></td>
                <td><div class="row-fluid"><span class="span10">{$DA['username']}</span></div></td>
                <td><div class="row-fluid"><span class="span10">{$DA['rechargesource']}</span></div></td>
                <td><div class="row-fluid"><span class="span10">{number_format($DA['actualtotalrecharge'], 2)}</span></div></td>
                <td><div class="row-fluid"><span class="span10">{number_format($DA['totalreceivables'], 2)}</span></div></td>
                <td><div class="row-fluid"><span class="span10">{$DA['createdtime']}</span></div></td>
                <td><div class="row-fluid"><span class="span10">{$DA['remarks']}</span></div></td></tr>
                {/foreach}
            </tbody></table>
        {/if}
    {/if}
    {* 用于标记是否存在“红冲，退款审核中”状态 *}
    <input type="hidden" value="0" class="isbackwash"/>
    <script>
    {if $RECORD->get('modulestatus') neq 'a_exception'}
    $('.isbackwash').each(function(n,k){
        if($(k).val() == 1){
            revieWatermark('红冲,退款审核中','red');
            return false;
        }else if($(k).val() == 2){
            revieWatermark('红冲,退款待提交','black');
            return false;
        }
    });
    {/if}
    function revieWatermark(watermarkText,clour) {
        var screenHeight = window.screen.height;
        var screenWidth  = window.screen.width;
        var watermarkText =  watermarkText;
        if (navigator.appName == "Microsoft Internet Explorer" && navigator.appVersion.match(/9./i) == "9.") {
            var stepH = 0.1 * screenHeight;
            var stepW = 0.1 * screenWidth;
            for (var i = 0; i <= 5; i++) {
                if(i%2==0){
                    continue;
                }
                for(var j = 0; j <= 7; j++){
                    if(j%2!=0){
                        continue;
                    }
                    $('body').append('<div style="position:relative;" class="backwashwatermark"><div style=" z-index:9999999;position:fixed;top:' + (150 * (i)+20) + 'px;left:'+ (200 * (j)+100) +'px;pointer-events: none;border:1px solid '+clour+';width:120px;line-height:1.3;text-align:center;color:red;border-radius:5px;font-size:24px;transform: rotate(40deg);-o-transform: rotate(40deg);-webkit-transform: rotate(40deg);-moz-transform: rotate(40deg);filter:progid:DXImageTransform.Microsoft.BasicImage(Rotation=2);">' + watermarkText + '</div></div>');
                }
            }
        } else {
            var stepH = 0.13 * screenHeight;
            var stepW = 0.1 * screenWidth;
            for (var i = 0; i <= 5; i++) {
                if(i%2==0){
                    continue;
                }
                for(var j = 0; j <= 35; j++){
                    if(j%4!=0){
                        continue;
                    }
            
                    $('body').append('<div style="position:relative;" class="backwashwatermark"><div style=" z-index:9999999;position:fixed;top:' + (150 * (i)+20) + 'px;left:'+ (200 * (j)+100) +'px;pointer-events: none;border:1px solid '+clour+';width:120px;line-height:1.3;text-align:center;color:red;border-radius:5px;font-size:24px;transform: rotate(40deg);-o-transform: rotate(40deg);-webkit-transform: rotate(40deg);-moz-transform: rotate(40deg);filter:progid:DXImageTransform.Microsoft.BasicImage(Rotation=2);">' + watermarkText + '</div></div>');
                        
                    
                }
            }
        }
    }
    </script>
{/strip}