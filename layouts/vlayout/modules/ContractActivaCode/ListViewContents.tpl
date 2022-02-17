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
				{if $LISTVIEW_FIELDS}
					{foreach key=KEY item=LISTVIEW_HEADER from=$LISTVIEW_FIELDS}
				<th nowrap data-field="{$LISTVIEW_HEADERS[$KEY]['columnname']}">
					{vtranslate($KEY, $MODULE)}
				</th>
				{/foreach}
				{else}
				{foreach key=KEY item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
				<th nowrap data-field="{$LISTVIEW_HEADER['columnname']}">
					{vtranslate($KEY, $MODULE)}
				</th>
				{/foreach}
				{/if}
				<th nowrap>未开票金额</th>
				<th nowrap>未收款金额</th>
				<th nowrap>每月确认收入</th>
				<th nowrap>累计确认收入</th>
				<th nowrap>本月确认收入</th>
				<th nowrap>是否到期</th>
				<th nowrap>合同应收账款</th>
				<th nowrap style="width:90px">操作</th>
			</tr>
		</thead>
		{assign var=UNPAIDAMOUNT value=0}
		{assign var=NOTICKETAMOUNT value=0}
		{assign var=SERVICECONTRACTSTOTAL value=0}
		{assign var=PAYMENTTOTAL value=0}
		{assign var=INVOICETOTAL value=0}
		{foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=listview}
		<tr class="listViewEntries"  data-id='{$LISTVIEW_ENTRY['id']}' data-recordUrl='index.php?module={$MODULE}&view=Detail&record={$LISTVIEW_ENTRY['id']}' id="{$MODULE}_listView_row_{$smarty.foreach.listview.index+1}">
            {foreach key=fkey item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS name=fieldview}
                <td class="listViewEntryValue"  nowrap>
                    {if $LISTVIEW_HEADER['columnname'] eq 'subject'}
                        <a class="btn-link" href='index.php?module={$MODULE}&view=Detail&record={$LISTVIEW_ENTRY['id']}' target="_block">{vtranslate( $LISTVIEW_ENTRY[$LISTVIEW_HEADER['columnname']],$MODULE)}</a>
                    {elseif $LISTVIEW_HEADER['columnname'] eq 'related_to'}
						<a class="btn-link" href=index.php?module=Accounts&view=Detail&record={$LISTVIEW_ENTRY['related_to_reference']} target=_blank>{$LISTVIEW_ENTRY['related_to']}</a>
                    {elseif $LISTVIEW_HEADER['columnname'] eq 'refund'}
                        {if $LISTVIEW_ENTRY['orderchargebackid'] neq 0}
							<a class="btn-link" href=index.php?module=OrderChargeback&view=Detail&record={$LISTVIEW_ENTRY['orderchargebackid']} target=_blank>{$LISTVIEW_ENTRY['contract_no']}</a>
                        {/if}
                    {else}
                        {uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)}
                    {/if}
                </td>
			{/foreach}

			{assign var='TEMPREPATY' value=$LISTVIEW_ENTRY['servicecontractstotal']-$LISTVIEW_ENTRY['paymenttotal']}
			{assign var='TEMPINVOICE' value=$LISTVIEW_ENTRY['servicecontractstotal']-$LISTVIEW_ENTRY['invoicetotal']}
			{assign var='ACCOUNTSRECEIVABLE' value=$LISTVIEW_ENTRY['servicecontractstotal']-$LISTVIEW_ENTRY['invoicetotal']}
			{if $TEMPREPATY<0}{$TEMPREPATY=0}{/if}
			{if $TEMPINVOICE<0}{$TEMPINVOICE=0}{/if}
            {$UNPAIDAMOUNT=$UNPAIDAMOUNT+$TEMPREPATY}
            {$NOTICKETAMOUNT=$NOTICKETAMOUNT+$TEMPINVOICE}
            {$PAYMENTTOTAL=$PAYMENTTOTAL+$LISTVIEW_ENTRY['paymenttotal']}
            {$INVOICETOTAL=$INVOICETOTAL+$LISTVIEW_ENTRY['invoicetotal']}
			<td nowrap>{$TEMPINVOICE}</td>
			<td nowrap>{$TEMPREPATY}</td>
			<td nowrap>{$LISTVIEW_ENTRY['monthlyIncome']}</td>
			<td nowrap>{$LISTVIEW_ENTRY['cumulativeIncome']}</td>
			<td nowrap>{$LISTVIEW_ENTRY['thisMonthlyIncome']}</td>
			<td nowrap>{$LISTVIEW_ENTRY['isMaturity']}</td>
			<td nowrap>{$LISTVIEW_ENTRY['accountsreceivable']}</td>
            {$SERVICECONTRACTSTOTAL=$SERVICECONTRACTSTOTAL+$LISTVIEW_ENTRY['servicecontractstotal']}

            <td class="listViewEntryValue" >
                {*{if $LISTVIEW_HEADER@last}

                    <div  style="width:90px">
                        <a  href="index.php?module={$MODULE}&view=Detail&record={$LISTVIEW_ENTRY['id']}"><i title="详细信息" class="icon-th-list alignMiddle"></i></a>&nbsp;
						<i title="改进意见" data-id="{$LISTVIEW_ENTRY['id']}" class="icon-comment alignMiddle"></i>&nbsp;
						{if $IS_MODULE_EDITABLE}
                        	<a  href='index.php?module={$MODULE}&view=Edit&record={$LISTVIEW_ENTRY['id']}'  target="_block"><i title="{vtranslate('LBL_EDIT', $MODULE)}" class="icon-pencil alignMiddle"></i></a>&nbsp;
                        {/if}
                        {if $IS_MODULE_DELETABLE}
                        	<a class="deleteRecordButton"><i title="删除" class="icon-trash alignMiddle"></i></a>
                        {/if}     
                    </div>
                {/if}*}
            </td>
		</tr>
		{/foreach}
		<tr class="listViewHeaders">
            {if $LISTVIEW_FIELDS}
                {foreach key=KEY item=LISTVIEW_HEADER from=$LISTVIEW_FIELDS}
					<td nowrap>
                        {if $LISTVIEW_HEADERS[$KEY]['columnname'] eq 'servicecontractstotal'}
                            {$SERVICECONTRACTSTOTAL}
                        {elseif $LISTVIEW_HEADERS[$KEY]['columnname'] eq 'paymenttotal'}
                            {$PAYMENTTOTAL}
                        {elseif $LISTVIEW_HEADERS[$KEY]['columnname'] eq 'invoicetotal'}
                            {$INVOICETOTAL}
                        {else}
							&nbsp;
                        {/if}
					</td>
                {/foreach}
            {else}
                {foreach key=KEY item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
					<td nowrap>
                        {if $LISTVIEW_HEADERS[$KEY]['columnname'] eq 'servicecontractstotal'}
                            {$SERVICECONTRACTSTOTAL}
                        {elseif $LISTVIEW_HEADERS[$KEY]['columnname'] eq 'paymenttotal'}
                            {$PAYMENTTOTAL}
                        {elseif $LISTVIEW_HEADERS[$KEY]['columnname'] eq 'invoicetotal'}
                            {$INVOICETOTAL}
                        {else}
							&nbsp;
                        {/if}
					</td>
                {/foreach}
            {/if}
			<td nowrap>{$NOTICKETAMOUNT}</td>
			<td nowrap>{$UNPAIDAMOUNT}</td>
			<td nowrap></td>
			<td nowrap></td>
			<td nowrap></td>
			<td nowrap></td>
			<td nowrap></td>
			<td nowrap></td>
		</tr>

	</table>

</div>
</div>
    </div>
{/strip}
