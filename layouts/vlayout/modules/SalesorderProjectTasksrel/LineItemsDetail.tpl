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
{assign var=FINAL_DETAILS value=$RELATED_PRODUCTS.1.final_details}
<table class="table table-bordered mergeTables">
    <thead>
    <th colspan="2" class="detailViewBlockHeader">
	{vtranslate('LBL_ITEM_DETAILS', $MODULE_NAME)}
    </th>
    <th colspan="1" class="detailViewBlockHeader">
	{assign var=CURRENCY_INFO value=$RECORD->getCurrencyInfo()}
	{vtranslate('LBL_CURRENCY', $MODULE_NAME)} : {vtranslate($CURRENCY_INFO['currency_name'],$MODULE_NAME)}({$CURRENCY_INFO['currency_symbol']})
    </th>
    <th colspan="2" class="detailViewBlockHeader">
	{vtranslate('LBL_TAX_MODE', $MODULE_NAME)} : {vtranslate($FINAL_DETAILS.taxtype, $MODULE_NAME)}
    </th>
	</thead>
	<tbody>
    <tr>
	<td>
	    <span class="redColor">*</span><b>{vtranslate('LBL_ITEM_NAME',$MODULE_NAME)}</b>
	</td>
        <td>
	    <b>{vtranslate('LBL_QTY',$MODULE_NAME)}</b>
	</td>
        <td>
	    <b>{vtranslate('LBL_LIST_PRICE',$MODULE_NAME)}</b>
	</td>
        <td>
	    <b>{vtranslate('LBL_TOTAL',$MODULE_NAME)}</b>
	</td>
        <td>
	    <b class="pull-right">{vtranslate('LBL_NET_PRICE',$MODULE_NAME)}</b>
	</td>
    </tr>
    {foreach key=INDEX item=LINE_ITEM_DETAIL from=$RELATED_PRODUCTS}
	<tr>
	    <td>
		<div class="row-fluid">
		    {$LINE_ITEM_DETAIL["productName$INDEX"]}
		</div>
		{if $LINE_ITEM_DETAIL["productDeleted$INDEX"]}
			<div class="row-fluid redColor deletedItem">
				{if empty($LINE_ITEM_DETAIL["productName$INDEX"])}
					{vtranslate('LBL_THIS_LINE_ITEM_IS_DELETED_FROM_THE_SYSTEM_PLEASE_REMOVE_THIS_LINE_ITEM',$MODULE)}
				{else}
					{vtranslate('LBL_THIS',$MODULE)} {$LINE_ITEM_DETAIL["entityType$INDEX"]} {vtranslate('LBL_IS_DELETED_FROM_THE_SYSTEM_PLEASE_REMOVE_OR_REPLACE_THIS_ITEM',$MODULE)}
				{/if}
			</div>
		{/if}
		{if !empty($LINE_ITEM_DETAIL["subProductArray$INDEX"])}
		    <div>
			{foreach item=SUB_PRODUCT_NAME from=$LINE_ITEM_DETAIL["subProductArray$INDEX"]}
			    <div>
				{if !empty($SUB_PRODUCT_NAME)}
					- &nbsp; <em>{$SUB_PRODUCT_NAME}</em>
				{/if}
			    </div>
			{/foreach}
		    </div>
		{/if}
		{if !empty($LINE_ITEM_DETAIL["productName$INDEX"])}
			<div>
				{$LINE_ITEM_DETAIL["comment$INDEX"]|nl2br}
			</div>
		{/if}
	    </td>
	    <td>
		{$LINE_ITEM_DETAIL["qty$INDEX"]}
	    </td>
	    <td>
		<div>
		    {$LINE_ITEM_DETAIL["listPrice$INDEX"]}
		</div>
		</td>
		<td>
		    <div>
			{$LINE_ITEM_DETAIL["productTotal$INDEX"]}
		    </div>
		</td>
		<td>
		    <span class="pull-right">
			{$LINE_ITEM_DETAIL["netPrice$INDEX"]}
		    </span>
		</td>
	    </tr>
	    {/foreach}
	    </tbody>
	</table>

	<table class="table table-bordered">
	    <tr>
		<td width="83%">
		    <div class="pull-right">
			<b>{vtranslate('LBL_ITEMS_TOTAL',$MODULE_NAME)}</b>
		    </div>
		</td>
		<td>
		    <span class="pull-right">
			<b>{$FINAL_DETAILS["hdnSubTotal"]}</b>
		    </span>
		</td>
	    </tr>
	   
	</table>