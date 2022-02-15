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

    {assign var=ACCOUNTS value=array('refillapplicationno','workflowsid','modifiedtime','modifiedby','modulestatus','createdtime','smownerid','assigned_user_id','servicecontractsid','accountid','customertype','customeroriginattr','totalrecharge','actualtotalrecharge','expcashadvances','iscontracted','servicesigndate','grossadvances','file','remarks','did','accountzh','productid','isprovideservice','rechargetypedetail','receivementcurrencytype','exchangerate','prestoreadrate','rechargeamount','discount','tax','factorage','activationfee','taxation','totalcost','transferamount','servicecost','totalgrossprofit','mstatus','rebatetype','accountrebatetype','supprebate','flow_state','receivedstatus')}
    {assign var=VENDORS value=array('totalreceivables','refillapplicationno','workflowsid','modifiedtime','modifiedby','modulestatus','createdtime','smownerid','assigned_user_id','servicecontractsid','accountid','customertype','customeroriginattr','totalrecharge','actualtotalrecharge','expcashadvances','iscontracted','servicesigndate','grossadvances','file','remarks','vendorid','bankaccount','bankname','banknumber','bankcode','suppliercontractsid','havesignedcontract','signdate','did','accountzh','productid','isprovideservice','rechargetypedetail','receivementcurrencytype','exchangerate','prestoreadrate','rechargeamount','discount','tax','factorage','rebatetype','activationfee','taxation','totalcost','transferamount','servicecost','totalgrossprofit','mstatus','accountrebatetype','paymentperiod','ispayment','supprebate')}
    {assign var=TECHPROCUREMENT value=array('refillapplicationno','workflowsid','modifiedtime','modifiedby','modulestatus','createdtime','smownerid','assigned_user_id','salesorderid','servicecontractsid','accountid','totalrecharge','totalreceivables','humancost','purchasecost','contractamount','file','remarks','vendorid','bankaccount','bankname','banknumber','bankcode','suppliercontractsid','havesignedcontract','signdate','productid','amountpayable')}
    {assign var=PRERECHARGE value=array('totalreceivables','refillapplicationno','workflowsid','modifiedtime','modifiedby','modulestatus','createdtime','smownerid','assigned_user_id','vendorid','bankaccount','bankname','banknumber','bankcode','suppliercontractsid','havesignedcontract','signdate','productid','prestoreadrate','rechargeamount','discount','rebates','mstatus','rebatetype','remarks','paymentperiod','ispayment')}
    {assign var=OTHERPROCUREMENT value=array('refillapplicationno','workflowsid','modifiedtime','modifiedby','modulestatus','createdtime','smownerid','assigned_user_id','vendorid','bankaccount','bankname','banknumber','bankcode','expecteddatepayment','expectedpaymentdeadline','beardepartment','bearratio','suppliercontractsid','havesignedcontract','signdate','productid','purchaseamount','purchaseprice','purchasequantity')}
    {assign var=NONMEDIAEXTRACTION value=array('refillapplicationno','workflowsid','modifiedtime','modifiedby','modulestatus','createdtime','smownerid','assigned_user_id','servicecontractsid','accountid','totalrecharge','totalreceivables','expcashadvances','iscontracted','servicesigndate','grossadvances','file','remarks','vendorid','bankaccount','bankname','banknumber','bankcode','suppliercontractsid','havesignedcontract','signdate','productid','purchaseamount','totalgrossprofit','actualtotalrecharge','paymentperiod','ispayment')}
    {assign var=PACKVENDORS value=array('refillapplicationno','workflowsid','modifiedtime','modifiedby','modulestatus','createdtime','smownerid','assigned_user_id','totalreceivables','vendorid','bankaccount','bankname','banknumber','bankcode','expecteddatepayment','expectedpaymentdeadline','remarks')}
    {assign var=COINRETURNFIELD value=array('servicecontractsid','refillapplicationno','workflowsid','modifiedtime','modifiedby','accountid','file','remarks','modulestatus','createdtime','smownerid','assigned_user_id','totalcashtransfer','totalcashin','totalturnoverofaccount','totaltransfertoaccount','did','productid','topplatform','accountzh','accountrebatetype','isprovideservice','discount','cashtransfer','accounttransfer','turninorout','conversiontype','vendorid')}
    {assign var=INCREASE value=array('refillapplicationno','workflowsid','modifiedtime','modifiedby','modulestatus','createdtime','smownerid','assigned_user_id','mservicecontractsid','maccountid','file','remarks','cashconsumptiontotal','cashincreasetotal','mservicecontractsid','maccountid','mservicecontractsid_name','maccountid_name','cashgift','taxrefund','cashconsumption','cashincrease','grantquarter','mstatus','discount','accountrebatetype','granttype','bankname','bankaccount','banknumber','bankcode')}
    {foreach key=BLOCK_LABEL_KEY item=FIELD_MODEL_LIST from=$RECORD_STRUCTURE}
        {if $BLOCK_LABEL_KEY eq 'LBL_CUSTOM_INFORMATION'}{assign var=OTHERFIELD_MODEL_LIST value=$FIELD_MODEL_LIST}{/if}
	{assign var=BLOCK value=$BLOCK_LIST[$BLOCK_LABEL_KEY]}
    {if $BLOCK_LABEL_KEY eq 'VENDOR_LBL_INFO' && in_array($RECHARGESOURCE,array('Accounts','COINRETURN'))}{continue}{/if}
    {if $BLOCK_LABEL_KEY eq 'VENDOR_LBL_INFO' && $RECHARGESOURCE eq 'INCREASE' && $RECORD->get('granttype') eq 'virtrefund'}{continue}{/if}
	{if $BLOCK eq null or $FIELD_MODEL_LIST|@count lte 0}{continue}{/if}
	{assign var=IS_HIDDEN value=$BLOCK->isHidden()}
	{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
	<table border="1" align="center" cellpadding="0" cellspacing="0">
            <tr>
                    <td colspan="4" style="text-align: left;line-height:200%;font-size: 14px;font-weight:bold;">
                            &nbsp;&nbsp;{if $BLOCK_LABEL_KEY eq 'LBL_CUSTOM_INFORMATION' && $RECHARGESOURCE eq 'INCREASE'}虚拟回款
                        {elseif $BLOCK_LABEL_KEY eq 'VENDOR_LBL_INFO' && $RECHARGESOURCE eq 'INCREASE'}
                        收款账户信息
                        {else}{vtranslate({$BLOCK_LABEL_KEY},{$MODULE_NAME})}{/if}
                        {if $BLOCK_LABEL_KEY eq 'LBL_CUSTOM_INFORMATION'}
                            1/{$RECHARGESHEETCOUNT}{if $RECHARGESOURCE eq 'COINRETURN'}&nbsp;转出{/if}
                        {/if}
                    </td>
            </tr>
		{assign var=COUNTER value=0}
		<tr>
		{foreach item=FIELD_MODEL key=FIELD_NAME from=$FIELD_MODEL_LIST}
            {if !$FIELD_MODEL->isViewableInDetailView()}
                {continue}
            {/if}
            {if !in_array($FIELD_MODEL->getFieldName(),$ACCOUNTS) && $RECHARGESOURCE eq 'Accounts'}{continue}{/if}
            {if !in_array($FIELD_MODEL->getFieldName(),$VENDORS) && $RECHARGESOURCE eq 'Vendors'}{continue}{/if}
            {if !in_array($FIELD_MODEL->getFieldName(),$TECHPROCUREMENT) && $RECHARGESOURCE eq 'TECHPROCUREMENT'}{continue}{/if}
            {if !in_array($FIELD_MODEL->getFieldName(),$PRERECHARGE) && $RECHARGESOURCE eq 'PreRecharge'}{continue}{/if}
            {if !in_array($FIELD_MODEL->getFieldName(),$OTHERPROCUREMENT) && $RECHARGESOURCE eq 'OtherProcurement'}{continue}{/if}
            {if !in_array($FIELD_MODEL->getFieldName(),$NONMEDIAEXTRACTION) && $RECHARGESOURCE eq 'NonMediaExtraction'}{continue}{/if}
            {if !in_array($FIELD_MODEL->getFieldName(),$PACKVENDORS) && $RECHARGESOURCE eq 'PACKVENDORS'}{continue}{/if}
            {if !in_array($FIELD_MODEL->getFieldName(),$COINRETURNFIELD) && $RECHARGESOURCE eq 'COINRETURN'}{continue}{/if}
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
            <td class="fieldLabel {$WIDTHTYPE}" width="20%"  style="text-align: left;line-height: 150%;font-size: 12px;">
                <label class='muted pull-right marginRight10px'>{vtranslate($tax.taxlabel, $MODULE)}(%)</label>
            </td>
            <td class="fieldValue {$WIDTHTYPE}"  style="text-align: left;line-height: 150%;font-size: 12px;">
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
            <td class="fieldLabel {$WIDTHTYPE}" width="30%" style="text-align: left;line-height: 150%;font-size: 12px;"><label class="muted pull-right marginRight10px">{vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}</label></td>
            <td class="fieldValue {$WIDTHTYPE}"  style="text-align: left;line-height: 150%;font-size: 12px;">
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
            <td class="{$WIDTHTYPE}"   style="text-align: left;line-height: 150%;font-size: 12px;"></td><td class="{$WIDTHTYPE}"  style="text-align: left;line-height: 150%;font-size: 12px;"></td></tr><tr>
            {assign var=COUNTER value=0}
            {/if}
            {/if}
            {if $COUNTER eq 2}
        </tr><tr>
            {assign var=COUNTER value=1}
            {else}
            {assign var=COUNTER value=$COUNTER+1}
            {/if}
            <td class="fieldLabel" width="16%" style="text-align: right;line-height: 200%;font-size: 10px;">
                <label class="muted pull-right marginRight10px">
                    {vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}
                </label>
            </td>
            <td class="fieldValue {$WIDTHTYPE}" width="34%"   style="text-align: left;line-height: 200%;font-size: 10px;" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
					 <span class="value" data-field-type="{$FIELD_MODEL->getFieldDataType()}">
                        {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME RECORD=$RECORD}{if $FIELD_MODEL->name eq 'probability'}%{/if}
					 </span>

            </td>
            {/if}

            {if $FIELD_MODEL_LIST|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype" and $FIELD_MODEL->get('uitype') neq "69" and $FIELD_MODEL->get('uitype') neq "105"}
                <td class="{$WIDTHTYPE}"  style="text-align: left;line-height: 150%;font-size: 12px;"></td><td class="{$WIDTHTYPE}"></td>
            {/if}
		{/foreach}
		</tr>
	</table>
	<table><tr><td>&nbsp;</td></tr></table>
        <br>
	{/foreach}
        {assign var=FIELDADDNAME value=array('mservicecontractsid','maccountid')}
        {foreach key=row_no item=data from=$C_RECHARGESHEET}
                {assign var=BLOCK_LABEL_KEY value='LBL_CUSTOM_INFORMATION'}
            {assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
                <table  border="1" align="center" cellpadding="0" cellspacing="0">
                    <tr>
                        <td colspan="4" style="text-align: left;line-height:200%;font-size: 14px;font-weight:bold;">
                            &nbsp;&nbsp;{if $RECHARGESOURCE eq 'INCREASE'}虚拟回款{else}{vtranslate({$BLOCK_LABEL_KEY},{$MODULE_NAME})}{/if}
                            {if $RECHARGESOURCE neq 'COINRETURN'}
                            {$row_no+2}/{$RECHARGESHEETCOUNT}
                            {else}
                            {if $data['turninorout'] eq 'in'}
                            {$data['seqnum']}/{$INCOUNT}&nbsp;转入
                            {else}
                                {$data['seqnum']}/{$RECHARGESHEETCOUNT}&nbsp;转出
                            {/if}
                            {/if}&nbsp;
                        </td>
                    </tr>
                    {assign var=COUNTER value=0}
                    <tr>
                        {foreach item=FIELD_MODEL key=FIELD_NAME from=$OTHERFIELD_MODEL_LIST}
                        {if !$FIELD_MODEL->isViewableInDetailView()}
                            {continue}
                        {/if}
                        {if !in_array($FIELD_MODEL->getFieldName(),$ACCOUNTS) && $RECHARGESOURCE eq 'Accounts'}{continue}{/if}
                        {if !in_array($FIELD_MODEL->getFieldName(),$VENDORS) && $RECHARGESOURCE eq 'Vendors'}{continue}{/if}
                        {if !in_array($FIELD_MODEL->getFieldName(),$TECHPROCUREMENT) && $RECHARGESOURCE eq 'TECHPROCUREMENT'}{continue}{/if}
                        {if !in_array($FIELD_MODEL->getFieldName(),$PRERECHARGE) && $RECHARGESOURCE eq 'PreRecharge'}{continue}{/if}
                        {if !in_array($FIELD_MODEL->getFieldName(),$OTHERPROCUREMENT) && $RECHARGESOURCE eq 'OtherProcurement'}{continue}{/if}
                        {if !in_array($FIELD_MODEL->getFieldName(),$NONMEDIAEXTRACTION) && $RECHARGESOURCE eq 'NonMediaExtraction'}{continue}{/if}
                        {if !in_array($FIELD_MODEL->getFieldName(),$COINRETURNFIELD) && $RECHARGESOURCE eq 'COINRETURN'}{continue}{/if}
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
                        <td class="fieldLabel {$WIDTHTYPE}">
                            {vtranslate($tax.taxlabel, $MODULE)}
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
                        <td class="fieldLabel {$WIDTHTYPE}" style="text-align: left;line-height: 150%;font-size: 12px;"><label class="muted pull-right marginRight10px">{vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}</label></td>
                        <td class="fieldValue {$WIDTHTYPE}" style="text-align: left;line-height: 150%;font-size: 12px;">
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
                        <td class="fieldLabel" width="16%"  style="text-align: right;line-height: 200%;font-size: 10px;">
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
                            &nbsp;
                        </td>
                        <td class="fieldValue {$WIDTHTYPE}" width="34%"  style="text-align: left;line-height:200%;font-size: 10px;" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
					 <span class="value" data-field-type="{$FIELD_MODEL->getFieldDataType()}">
                         &nbsp;
                        {if !in_array($FIELD_MODEL->getFieldName(),$DISPLAYFIELD)}
                            {if $FIELD_MODEL->get('uitype') eq 56}
                             {if $data[$FIELD_MODEL->getFieldName()] eq 1}是{else}无{/if}
                            {elseif in_array($FIELD_MODEL->get('uitype'),array(15,16))}
                             {vtranslate($data[$FIELD_MODEL->getFieldName()],$MODULE_NAME)}
                            {elseif in_array($FIELD_MODEL->getFieldName(),$FIELDADDNAME)}
                                {$data[$FIELD_MODEL->getFieldName()|cat:'_name']}
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
                </table>
            <table><tr><td>&nbsp;</td></tr></table>
        {/foreach}
    {if !empty($PAYMENTSLIST)}
        {foreach key=KEYINDEX item=D from=$PAYMENTSLIST}
            <table border="1" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td colspan="4" style="text-align: left;line-height:200%;font-size: 14px;font-weight:bold;">
                        &nbsp;&nbsp;关联回款信息(申请人录入)
                    </td>
                </tr>

                <tr>
                    <td width="16%"  style="text-align: right;line-height: 200%;font-size: 10px;"><label class="muted pull-right marginRight10px">回款信息 </label>
                    </td>
                    <td class="fieldValue medium" width="34%"  style="text-align: left;line-height:150%;font-size: 10px;">{$D['paytitle']}</td>
                    <td width="16%"  style="text-align: right;line-height: 200%;font-size: 10px;"><label class="muted pull-right marginRight10px">入账金额 </label>
                    </td>
                    <td class="fieldValue medium" width="34%"  style="text-align: left;line-height:200%;font-size: 10px;">&nbsp;{$D['total']}</td>
                </tr>
                <tr>
                    <td class="fieldLabel medium" width="16%"  style="text-align: right;line-height: 200%;font-size: 10px;"><label class="muted pull-right marginRight10px">入账日期 </label>
                    </td>
                    <td class="fieldValue medium"width="34%"  style="text-align: left;line-height:200%;font-size: 10px;">&nbsp;{$D['arrivaldate']}</td>
                    <td class="fieldLabel medium" width="16%"  style="text-align: right;line-height: 200%;font-size: 10px;"><label class="muted pull-right marginRight10px">可使用充值金额 </label>
                    </td>
                    <td class="fieldValue medium" width="34%"  style="text-align: left;line-height:200%;font-size: 10px;">&nbsp;{$D['allowrefillapptotal']}</td>
                </tr>
                <tr>
                    <td class="fieldLabel medium" width="16%"  style="text-align: right;line-height: 200%;font-size: 10px;"><label class="muted pull-right marginRight10px">充值现金 </label>
                    </td>
                    <td class="fieldValue medium" width="34%"  style="text-align: left;line-height:200%;font-size: 10px;">&nbsp;{$D['refillapptotal']}</td>
                    <td class="fieldLabel medium" width="16%"  style="text-align: right;line-height: 200%;font-size: 10px;"><label class="muted pull-right marginRight10px">退款金额 </label>
                    </td>
                    <td class="fieldValue medium" width="34%"  style="text-align: left;line-height:200%;font-size: 10px;">&nbsp;{$D['refundamount']}</td>
                </tr>
                <tr>
                    <td class="fieldLabel medium" width="16%"  style="text-align: right;line-height: 200%;font-size: 10px;"><label class="muted pull-right marginRight10px">备注 </label>
                    </td>
                    <td class="fieldValue medium" colspan="3"  style="text-align: left;line-height:200%;font-size: 10px;">&nbsp;{$D['remarks']}</td>

                </tr>
            </table>
            &nbsp;
            <br>
        {/foreach}
    {/if}
    {if $RECHARGESOURCE eq 'PACKVENDORS' && !empty($VENDORLIST)}
        <table border="1" align="center" cellpadding="0" cellspacing="0">
            <thead>
            <tr style="text-align: left;line-height:150%;font-size: 12px;font-weight:bold;">
                <th><label class="muted">申请单编号</label></th>
                <th><label class="muted"><span class="redColor"></span> 服务合同</label></th>
                <th><label class="muted"><span class="redColor"></span> 客户</label></th>
                <th><label class="muted"><span class="redColor"></span> 申请人</label></th>
                <th><label class="muted"><span class="redColor"></span> 应收款总额</label></th>
                <th><label class="muted"><span class="redColor"></span> 应付款金额</label></th>
                <th><label class="muted"><span class="redColor"></span> 申请时间</label></th>
                <th><label class="muted"><span class="redColor"></span> 备注</label></th></tr></thead><tbody>
            {foreach key=KEYINDEX item=DA from=$VENDORLIST}

                <tr style="text-align: center;line-height:150%;font-size: 10px;font-weight:bold;">
                    <td><div class="row-fluid"><span class="span10">{$DA.refillapplicationno}</span></div></td>
                    <td><div class="row-fluid"><span class="span10">{$DA['contract_no']}</span></div></td>
                    <td><div class="row-fluid"><span class="span10">{$DA['accountname']}</span></div></td>
                    <td><div class="row-fluid"><span class="span10">{$DA['username']}</span></div></td>
                    <td><div class="row-fluid"><span class="span10">{$DA['actualtotalrecharge']}</span></div></td>
                    <td><div class="row-fluid"><span class="span10">{$DA['totalreceivables']}</span></div></td>
                    <td><div class="row-fluid"><span class="span10">{$DA['createdtime']}</span></div></td>
                    <td><div class="row-fluid"><span class="span10">{$DA['remarks']}</span></div></td></tr>
            {/foreach}
            </tbody></table>
    {/if}
    <table><tr><td>&nbsp;</td></tr></table>
{/strip}