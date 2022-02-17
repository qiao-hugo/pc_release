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

    <div id="pagehtml">
<input type="hidden" id="view" value="{$VIEW}" />
<input type="hidden" id="pageStartRange" value="" />
<input type="hidden" id="pageEndRange" value="" />
<input type="hidden" id="previousPageExist" value="" />
<input type="hidden" id="nextPageExist" value="" />
<input type="hidden" id="alphabetSearchKey" value= "" />
<input type="hidden" id="Operator" value="{$OPERATOR}" />
<input type="hidden" id="alphabetValue" value="{$ALPHABET_VALUE}" />
<input type="hidden" id="totalCount" value="{$PAGE_COUNT}" />

<input type='hidden' value="{$PAGE_NUMBER}" id='pageNumber'>
<input type='hidden' value="{$PAGING_MODEL->getPageLimit()}" id='pageLimit'>
<input type="hidden" value="{$LISTVIEW_ENTIRES_COUNT}" id="noOfEntries">

{assign var = ALPHABETS_LABEL value = vtranslate('LBL_ALPHABETS', 'Vtiger')}
{assign var = ALPHABETS value = ','|explode:$ALPHABETS_LABEL}



<div class="listViewEntriesDiv contents-bottomscroll" style="overflow:auto;">
	<div class="bottomscroll-div" >
	<input type="hidden" value="{$ORDER_BY}" id="orderBy">
	<input type="hidden" value="{$SORT_ORDER}" id="sortOrder">

	{assign var=WIDTHTYPE value=$CURRENT_USER_MODEL->get('rowheight')}
	<table class="table listViewEntriesTable">
		<thead>
			<tr class="listViewHeaders">
				<th nowrap>公司账号</th>
				<th nowrap>汇款抬头</th>
				<th nowrap>支付渠道</th>
				<th nowrap>交易单号</th>
				<th nowrap>入账日期</th>
				<th nowrap>回款金额</th>
				<th nowrap>是否匹配</th>
				<th nowrap>是否代付款</th>
				<th nowrap>匹配合同</th>
				<th nowrap>匹配时间</th>
				<th nowrap>匹配人</th>
				<th nowrap>是否超时匹配</th>
				<th nowrap>是否跨月匹配</th>
			</tr>
		</thead>
		{foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=listview}
		<tr class="listViewEntries"  data-id='{$LISTVIEW_ENTRY['receivedpaymentsid']}' data-recordUrl='index.php?module=ReceivedPayments&view=Detail&record={$LISTVIEW_ENTRY['receivedpaymentsid']}' id="{$MODULE}_listView_row_{$smarty.foreach.listview.index+1}">
			<td class="listViewEntryValue" nowrap="">{$LISTVIEW_ENTRY['owncompany']}</td>
			<td class="listViewEntryValue" nowrap="">{$LISTVIEW_ENTRY['paytitle']}</td>
			<td class="listViewEntryValue" nowrap="">{$LISTVIEW_ENTRY['paymentchannel']}</td>
			<td class="listViewEntryValue" nowrap="">{$LISTVIEW_ENTRY['paymentcode']}</td>
			<td class="listViewEntryValue" nowrap="">{$LISTVIEW_ENTRY['reality_date']}</td>
			<td class="listViewEntryValue" nowrap="">{$LISTVIEW_ENTRY['unit_price']}</td>
			<td class="listViewEntryValue" nowrap="">{$LISTVIEW_ENTRY['ismatch']}</td>
			<td class="listViewEntryValue" nowrap="">{$LISTVIEW_ENTRY['isstaypayment']}</td>
			<td class="listViewEntryValue" nowrap="">{$LISTVIEW_ENTRY['contract_no']}</td>
			<td class="listViewEntryValue" nowrap="">{$LISTVIEW_ENTRY['matchdate']}</td>
			<td class="listViewEntryValue" nowrap="">{$LISTVIEW_ENTRY['username']}</td>
			<td class="listViewEntryValue" nowrap="">{$LISTVIEW_ENTRY['istimeoutmatch']}</td>
			<td class="listViewEntryValue" nowrap="">{$LISTVIEW_ENTRY['iscrossmonthmatch']}</td>
            <td class="listViewEntryValue"></td>
		</tr>
		{/foreach}
		
	</table>

</div>
</div>
    </div>
{/strip}
