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
<div class='editViewContainer container-fluid'>

	<form class="form-horizontal recordEditView" id="EditView" name="EditView" method="post" action="index.php" enctype="multipart/form-data">
		{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
		{if !empty($PICKIST_DEPENDENCY_DATASOURCE)}
			<input type="hidden" name="picklistDependency" value='{Vtiger_Util_Helper::toSafeHTML($PICKIST_DEPENDENCY_DATASOURCE)}' />
		{/if}
		<input type="hidden" name="module" value="{$MODULE}" />
		<input type="hidden" name="supprebate" value="{$RECORD->get('supprebate')}" />
		<input type="hidden" name="rechargesource" value="{$RECHARGESOURCE}" />
		<input type="hidden" name="occupationamount" value="0" />
		<input type="hidden" name="action" value="Save" />
		<input type="hidden" name="record" value="{$RECORD_ID}" />
		<input type="hidden" name="srcterminal" value="2" />
		{if $IS_RELATION_OPERATION }
			<input type="hidden" name="sourceModule" value="{$SOURCE_MODULE}" />
			<input type="hidden" name="sourceRecord" value="{$SOURCE_RECORD}" />
			<input type="hidden" name="relationOperation" value="{$IS_RELATION_OPERATION}" />
		{/if}
		<div class="contentHeader row-fluid">
		{assign var=SINGLE_MODULE_NAME value='SINGLE_'|cat:$MODULE}
		{if $RECORD_ID neq ''}
			<h3 title="{vtranslate('LBL_EDITING', $MODULE)} {vtranslate($RECHARGESOURCE, $MODULE)}{*vtranslate($SINGLE_MODULE_NAME, $MODULE)*} {$RECORD_STRUCTURE_MODEL->getRecordName()}">{vtranslate('LBL_EDITING', $MODULE)} {*vtranslate($SINGLE_MODULE_NAME, $MODULE)*}{vtranslate($RECHARGESOURCE, $MODULE)} - {$RECORD_STRUCTURE_MODEL->getRecordName()}</h3>
            <hr>
        {else}
			<h3>{vtranslate('LBL_CREATING_NEW', $MODULE)} {vtranslate($RECHARGESOURCE, $MODULE)}{*vtranslate($SINGLE_MODULE_NAME, $MODULE)*}</h3>
            <hr>
		{/if}
			{*<span class="pull-left">
			<i style="color: #ff0000;font-size:20px;font-style: normal;font-weight:bold;">25号之前完成的退款申请,月底最后一个工作日退款;25号之后完成的退款申请,顺延至下个月处理</i>
			</span>*}
			<span style="color:red;font-size:22px;font-weight:bold;">鼠标移至问号图标可查看对应字段注释</span>
			<span class="pull-right">
				<button class="btn btn-success" type="submit"><strong>{vtranslate('LBL_SAVE', $MODULE)}</strong></button>
				<a class="cancelLink" type="reset" onclick="javascript:window.history.back();">{vtranslate('LBL_CANCEL', $MODULE)}</a>
			</span>
		</div>
		{*保留的显示*}
		{*
		{assign var=TECHPROCUREMENT value=array('productservice','suppliercontractsid','havesignedcontract','havesignedcontract','transferamount','signdate','productid')}
    	{assign var=ACCOUNTVENDORS value=array('Accounts','Vendors')}
    	{assign var=ACCOUNTVENDORSFIELD value=array('iscontracted','servicesigndate','grossadvances')}
		{assign var=SALESORDERLIST value=array('salesorderid','humancost','purchasecost','contractamount')}
		{assign var=PRERECHARGE value=array('productservice','suppliercontractsid','havesignedcontract','havesignedcontract','signdate','productid','rechargeamount','discount','prestoreadrate','mstatus','rebates')}
		{assign var=MODULEFLAG value=array('Vendors','TECHPROCUREMENT','PreRecharge')}
		*}
		{assign var=ACCOUNTS value=array('servicecontractsid','usecontractamount','contractamount','accountid','customertype','customeroriginattr','totalrecharge','actualtotalrecharge','expcashadvances','iscontracted','servicesigndate','grossadvances','file','remarks','did','accountzh','productid','rebatetype','isprovideservice','rechargetypedetail','receivementcurrencytype','exchangerate','prestoreadrate','rechargeamount','discount','tax','factorage','activationfee','taxation','totalcost','transferamount','servicecost','totalgrossprofit','mstatus','accountrebatetype','flow_state','receivedstatus','invoicecircumstance')}
		{assign var=VENDORS value=array('totalreceivables','usecontractamount','contractamount','servicecontractsid','accountid','customertype','customeroriginattr','totalrecharge','actualtotalrecharge','expcashadvances','iscontracted','servicesigndate','grossadvances','file','remarks','did','rebatetype','accountzh','remarks','vendorid','bankaccount','bankname','banknumber','banklist','bankcode','productservice','suppliercontractsid','havesignedcontract','signdate','productid','isprovideservice','rechargetypedetail','receivementcurrencytype','exchangerate','prestoreadrate','rechargeamount','discount','tax','factorage','activationfee','taxation','totalcost','transferamount','servicecost','totalgrossprofit','mstatus','accountrebatetype','paymentperiod','ispayment','invoicecircumstance')}
		{assign var=TECHPROCUREMENT value=array('salesorderid','servicecontractsid','accountid','totalrecharge','totalreceivables','humancost','purchasecost','contractamount','file','remarks','vendorid','bankaccount','bankname','banknumber','banklist','bankcode','productservice','suppliercontractsid','havesignedcontract','signdate','productid','amountpayable','invoicecircumstance')}
		{assign var=PRERECHARGE value=array('totalreceivables','vendorid','bankaccount','bankname','banknumber','banklist','bankcode','productservice','suppliercontractsid','havesignedcontract','signdate','productid','prestoreadrate','rechargeamount','discount','rebates','mstatus','rebatetype','remarks','paymentperiod','banklist','bankcode','invoicecircumstance')}
		{assign var=OTHERPROCUREMENT value=array('vendorid','bankaccount','bankname','banknumber','banklist','bankcode','expecteddatepayment','expectedpaymentdeadline','beardepartment','bearratio','productservice','suppliercontractsid','havesignedcontract','signdate','productid','purchaseamount','purchaseprice','purchasequantity','invoicecircumstance')}
		{assign var=NONMEDIAEXTRACTION value=array('servicecontractsid','accountid','totalrecharge','totalreceivables','expcashadvances','iscontracted','servicesigndate','grossadvances','file','remarks','vendorid','bankaccount','bankname','banknumber','banklist','bankcode','productservice','suppliercontractsid','havesignedcontract','signdate','productid','purchaseamount','contractamount','usecontractamount','totalgrossprofit','actualtotalrecharge','banklist','bankcode','paymentperiod','nonaccountrebatetype','nonaccountrebate','invoicecircumstance','isthrowtime','throwtime')}
		{assign var=PACKVENDORS value=array('totalreceivables','vendorid','bankaccount','bankname','banknumber','banklist','bankcode','expecteddatepayment','expectedpaymentdeadline','remarks','actualtotalreceivables','invoicecompany','invoicecircumstance')}
    	{assign var=COINRETURN value=array('servicecontractsid','accountid','file','remarks','did','accountzh','productid','isprovideservice','accountrebatetype','discount','totalcashtransfer','totalcashin','totalturnoverofaccount','totaltransfertoaccount','cashtransfer','accounttransfer','conversiontype','vendorid','invoicecircumstance')}
    	{assign var=INCREASE value=array('mservicecontractsid','maccountid','file','remarks','cashconsumptiontotal','cashincreasetotal','mservicecontractsid','maccountid','mservicecontractsid_name','maccountid_name','cashgift','taxrefund','cashconsumption','cashincrease','grantquarter','mstatus','discount','accountrebatetype','granttype','bankname','bankaccount','banknumber','bankcode','receivementcurrencytype','invoicecircumstance')}
	{foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE name="EditViewBlockLevelLoop"}
			{if $BLOCK_FIELDS|@count lte 0}{continue}{/if}
			{if $BLOCK_LABEL eq 'VENDOR_LBL_INFO' && ($RECHARGESOURCE eq 'Accounts' OR $RECHARGESOURCE eq 'COINRETURN')}{continue}{/if}
			{if $BLOCK_LABEL eq 'LBL_INFO' && ($RECHARGESOURCE eq 'OtherProcurement')}{continue}{/if}

		{if $BLOCK_LABEL eq 'LBL_CUSTOM_INFORMATION' && $RECHARGESOURCE eq 'COINRETURN'}
			<div style="float: right;height: 20px;" >
				<a class="button btn-info btn-primary" style="margin: 5px;" id="batchoutput"><span style="padding: 5px;font-size: 14px;">明细批量导入(单次最多20条)</span></a>   <a href="/转出明细批量导入模板.xls" style="color: red;">批量导入模板下载</a>
			</div>
			<div style="display:none;">
				<input type="file" name="batchoutput" accept="application/vnd.ms-excel" id="outputrefill" />
			</div>
		{/if}
		<div {if $BLOCK_LABEL=="LBL_CUSTOM_INFORMATION"}id="batchImport"{/if}>
			<table class="table table-bordered blockContainer showInlineTable detailview-table  {$BLOCK_LABEL}"{if $BLOCK_LABEL eq 'VENDOR_LBL_INFO' && $RECHARGESOURCE eq 'INCREASE' && $RECORD->get('granttype') eq 'virtrefund'} style="display:none;"{/if}>
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
					虚拟回款
					<b class="pull-right">
						<button class="btn btn-small addincrease" type="button" data-num="{$RECHARGESHEETCOUNT}">
							<span style="color:red;"><i class="icon-plus" title=""></i>增款</span>
						</button></b>
					{elseif $BLOCK_LABEL eq 'VENDOR_LBL_INFO' && $RECHARGESOURCE eq 'INCREASE'}
					收款账户信息
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
				{*{if !in_array($FIELD_MODEL->getFieldName(),$OtherProcurement) && $RECHARGESOURCE eq 'OtherProcurement' && $BLOCK_LABEL eq 'LBL_CUSTOM_INFORMATION'}{continue}{/if}
                {if in_array($FIELD_MODEL->getFieldName(),$ACCOUNTVENDORSFIELD) && !in_array($RECHARGESOURCE,$ACCOUNTVENDORS)}{continue}{/if}
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
                {if $FIELD_MODEL->getFieldName() eq 'rebates' && $RECHARGESOURCE neq 'PreRecharge'}{continue}{/if}*}
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
                            {include file=vtemplate_path('../RefillApplication/uitypes/Reference.tpl','RefillApplication') BLOCK_FIELDS=$BLOCK_FIELDS}
						{else}
                            {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) BLOCK_FIELDS=$BLOCK_FIELDS}
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
		</div>
		{if $BLOCK_LABEL eq 'LBL_INFO' && $RECHARGESOURCE eq 'Accounts'}
		<div style="float: right;height: 20px;" >
			<a class="button btn-info btn-primary" style="margin: 5px;" id="batchimport"><span style="padding: 5px;font-size: 14px;">明细批量导入(单次最多20条)</span></a>   <a href="/充值申请单批量导入模板.xls" style="color: red;">批量导入模板下载</a>
		</div>
			<div style="display:none;">
				<input type="file" name="batchimport" accept="application/vnd.ms-excel" id="importrefill" />
			</div>
		{/if}
{/foreach}
{if $RECHARGESOURCE eq 'PACKVENDORS'}
    <div id="vendoridslist"></div>
    <link href="libraries/icheck/blue.css" rel="stylesheet">
    <script src="libraries/icheck/icheck.min.js"></script>
{/if}


<div class="modal-dialog" style="width: 1000px;display: none;left:0; right:0; top:60px; bottom:0;position:fixed;height: 500px;" id="show_data2" >
    <div class="modal-content" style="height: 500px;">
        <div class="modal-body" style="overflow: hidden;overflow-y: scroll;">
            <button type="button" class="bootbox-close-button close" data-dismiss="modal" aria-hidden="true" style="margin-top: -10px;" id="">×</button>
            <table class="table table-bordered blockContainer showInlineTable detailview-table LBL_CUSTOM_INFORMATION">
                <thead>
                    <tr>
                        <th class="blockHeader" colspan="5">
                            <img class="cursorPointer alignMiddle blockToggle  hide" src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;">
                            <img class="cursorPointer alignMiddle blockToggle" src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">
                            &nbsp;&nbsp; 客户垫款详情&nbsp;&nbsp;<b class="pull-right"></b></th>
                    </tr>
                </thead>
                <tbody id='show_data1'>



                </tbody>
            </table>
        </div>
    </div>
</div>
{/strip}
