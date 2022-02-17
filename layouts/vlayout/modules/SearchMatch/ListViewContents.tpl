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
				<th nowrap>支付渠道</th>
				<th nowrap>交易单号</th>
				<th nowrap>回款抬头</th>
				<th nowrap>入账日期</th>
				<th nowrap>本位币</th>
				<th nowrap>汇率</th>
				<th nowrap>回款金额</th>
				<th nowrap>回款类型</th>
				<th nowrap>创建时间</th>
				<th nowrap style="width:90px">操作</th>
			</tr>
		</thead>
		{foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=listview}
		<tr class="listViewEntries"  data-id='{$LISTVIEW_ENTRY['receivedpaymentsid']}' data-channel="{$LISTVIEW_ENTRY['paymentchannel']}"  id="{$MODULE}_listView_row_{$smarty.foreach.listview.index+1}">
			<td class="listViewEntryValue" nowrap="">{$LISTVIEW_ENTRY['owncompany']}</td>
			<td class="listViewEntryValue" nowrap="">{$LISTVIEW_ENTRY['paymentchannel']}</td>
			<td class="listViewEntryValue" nowrap="">{$LISTVIEW_ENTRY['paymentcode']}</td>
			<td class="listViewEntryValue" nowrap="">{$LISTVIEW_ENTRY['paytitle']}</td>
			<td class="listViewEntryValue" nowrap="">{$LISTVIEW_ENTRY['reality_date']}</td>
			<td class="listViewEntryValue" nowrap="">{$LISTVIEW_ENTRY['standardmoney']}</td>
			<td class="listViewEntryValue" nowrap="">{$LISTVIEW_ENTRY['exchangerate']}</td>
			<td class="listViewEntryValue" nowrap="">{$LISTVIEW_ENTRY['unit_price']}</td>
			<td class="listViewEntryValue" nowrap="">{vtranslate($LISTVIEW_ENTRY['receivedstatus'], 'ReceivedPayments')}</td>
			<td class="listViewEntryValue" nowrap="">{$LISTVIEW_ENTRY['createtime']}</td>
            <td class="listViewEntryValue">
					{if $LISTVIEW_ENTRY['isShow'] eq 1 }
						<div  style="width:120px">
							<a class="matchReceive">匹配回款</a>&nbsp;&nbsp;&nbsp;
							<a class="splitReceive">拆分回款</a>
						</div>
					{else}
						<div  style="width:120px;color: #DDDDDD" >
							匹配回款&nbsp;&nbsp;&nbsp;
								拆分回款
						</div>
					{/if}
            </td>
		</tr>
		{/foreach}
		
	</table>

</div>
</div>
    </div>
{/strip}
