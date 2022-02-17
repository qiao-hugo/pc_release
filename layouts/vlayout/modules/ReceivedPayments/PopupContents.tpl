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
<input type='hidden' id='pageNumber' value="{$PAGE_NUMBER}">
<input type='hidden' id='pageLimit' value="{$PAGING_MODEL->getPageLimit()}">
<input type="hidden" id="noOfEntries" value="{$LISTVIEW_COUNT}">
<input type="hidden" id="pageStartRange" value="{$PAGING_MODEL->getRecordStartRange()}" />
<input type="hidden" id="pageEndRange" value="{$PAGING_MODEL->getRecordEndRange()}" />
<input type="hidden" id="previousPageExist" value="{$PAGING_MODEL->isPrevPageExists()}" />
<input type="hidden" id="nextPageExist" value="{$PAGING_MODEL->isNextPageExists()}" />
<input type="hidden" id="totalCount" value="{$PAGE_COUNT}" />
<div class="popupEntriesDiv" id="listViewContents">
	<input type="hidden" value="{$ORDER_BY}" id="orderBy">
	<input type="hidden" value="{$SORT_ORDER}" id="sortOrder">
	{if $SOURCE_MODULE eq "Emails"}
		<input type="hidden" value="Vtiger_EmailsRelatedModule_Popup_Js" id="popUpClassName"/>
	{/if}
	{assign var=WIDTHTYPE value=$CURRENT_USER_MODEL->get('rowheight')}
	<table class="table table-bordered listViewEntriesTable">
		<thead>
			<tr class="listViewHeaders">
				<th class="{$WIDTHTYPE}">公司账号</th>
				<th class="{$WIDTHTYPE}">汇款抬头</th>
				<th class="{$WIDTHTYPE}">回款金额</th>
				<th class="{$WIDTHTYPE}">入账日期</th>
				<th class="{$WIDTHTYPE}">回款类型</th>
			</tr>
		</thead>
		{if $SOURCE_MODULE eq "Receipt"}
			{foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=popupListView}
				<tr class="listViewEntries" data-id="{$LISTVIEW_ENTRY['id']}" data-name='{$LISTVIEW_ENTRY['paytitle']}'>
					<td>{$LISTVIEW_ENTRY['owncompany']}</td>
					<td>{$LISTVIEW_ENTRY['paytitle']}</td>
					<td>{$LISTVIEW_ENTRY['unit_price']}</td>
					<td>{$LISTVIEW_ENTRY['reality_date']}</td>
					<td>保证金</td>
				</tr>
			{/foreach}
		{else}
			{foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=popupListView}
				<tr class="listViewEntries" >
					<td><a href="index.php?module=ReceivedPayments&amp;view=Edit&amp;record={$LISTVIEW_ENTRY['receivedpaymentsid']}" target="_block">{$LISTVIEW_ENTRY['paytitle']}</a></td>
					<td>{$LISTVIEW_ENTRY['receivementcurrencytype']}</td>
					<td>{$LISTVIEW_ENTRY['exchangerate']}</td>
					<td>{$LISTVIEW_ENTRY['unit_price']}</td>
					<td>{$LISTVIEW_ENTRY['reality_date']}</td>
				</tr>
			{/foreach}
		{/if}


	</table>

	<!--added this div for Temporarily -->
{if $LISTVIEW_ENTIRES_COUNT eq '0'}
	<div class="row-fluid">
		<div class="emptyRecordsDiv">{vtranslate('LBL_NO', $MODULE)} {vtranslate($MODULE, $MODULE)} {vtranslate('LBL_FOUND', $MODULE)}.</div>
	</div>
{/if}
</div>
{/strip}
