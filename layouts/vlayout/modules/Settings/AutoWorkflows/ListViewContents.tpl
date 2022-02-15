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
<input type="hidden" id="pageStartRange" value="{$PAGING_MODEL->getRecordStartRange()}" />
<input type="hidden" id="pageEndRange" value="{$PAGING_MODEL->getRecordEndRange()}" />
<input type="hidden" id="previousPageExist" value="{$PAGING_MODEL->isPrevPageExists()}" />
<input type="hidden" id="nextPageExist" value="{$PAGING_MODEL->isNextPageExists()}" />
<input type="hidden" id="totalCount" value="{$LISTVIEW_COUNT}" />
<input type="hidden" value="{$ORDER_BY}" id="orderBy">
<input type="hidden" value="{$SORT_ORDER}" id="sortOrder">
<input type="hidden" id="totalCount" value="{$LISTVIEW_COUNT}" />
<input type='hidden' value="{$PAGE_NUMBER}" id='pageNumber'>
<input type='hidden' value="{$PAGING_MODEL->getPageLimit()}" id='pageLimit'>
<input type="hidden" value="{$LISTVIEW_ENTIRES_COUNT}" id="noOfEntries">

<div class="listViewEntriesDiv" style='overflow-x:auto;'>
	<span class="listViewLoadingImageBlock hide modal" id="loadingListViewModal">
		<img class="listViewLoadingImage" src="{vimage_path('loading.gif')}" alt="no-image" title="{vtranslate('LBL_LOADING', $MODULE)}"/>
		<p class="listViewLoadingMsg">{vtranslate('LBL_LOADING_LISTVIEW_CONTENTS', $MODULE)}........</p>
	</span>
	{assign var="NAME_FIELDS" value=$MODULE_MODEL->getNameFields()}
	{assign var=WIDTHTYPE value=$CURRENT_USER_MODEL->get('rowheight')}
	<table class="table table-bordered table-condensed listViewEntriesTable">
		<thead>
			<tr class="listViewHeaders">
				
				{assign var=WIDTH value={99/(count($LISTVIEW_HEADERS))}}
				{foreach item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
				<th width="{$WIDTH}%" nowrap  class="{$WIDTHTYPE}">
					<a  {if !($LISTVIEW_HEADER->has('sort'))} class="listViewHeaderValues cursorPointer" data-nextsortorderval="{if $COLUMN_NAME eq $LISTVIEW_HEADER->get('name')}{$NEXT_SORT_ORDER}{else}ASC{/if}" data-columnname="{$LISTVIEW_HEADER->get('name')}" {/if}>{vtranslate($LISTVIEW_HEADER->get('label'), $QUALIFIED_MODULE)}
						{if $COLUMN_NAME eq $LISTVIEW_HEADER->get('name')}<img class="{$SORT_IMAGE} icon-white">{/if}</a>
				</th>
				{/foreach}
				<th>操作</th>
			</tr>
		</thead>
		<tbody>
		{foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES}
			<tr class="listViewEntries" data-id="{$LISTVIEW_ENTRY->getId()}"
					{if method_exists($LISTVIEW_ENTRY,'getDetailViewUrl')}data-recordurl="{$LISTVIEW_ENTRY->getDetailViewUrl()}"{/if}
			 >
				{foreach item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}				
					{assign var=LISTVIEW_HEADERNAME value=$LISTVIEW_HEADER->get('name')}
					{assign var=LAST_COLUMN value=$LISTVIEW_HEADER@last}
					<td class="listViewEntryValue {$WIDTHTYPE}"  width="{$WIDTH}%" nowrap>
						&nbsp;{$LISTVIEW_ENTRY->getDisplayValue($LISTVIEW_HEADERNAME)}
					</td>
					{if $LAST_COLUMN}
					<td nowrap class="{$WIDTHTYPE}">
						<div  style="width:90px">
                        	<a  href ='index.php?module=AutoWorkflows&parent=Settings&view=Edit&record={$LISTVIEW_ENTRY->get("autoworkflowid")}'  target="_block"><i title="编辑" class="icon-pencil alignMiddle"></i></a>&nbsp;
                            <a  href ='index.php?module=AutoWorkflows&parent=Settings&view=Edit&mode=designFlow&source_record={$LISTVIEW_ENTRY->get("autoworkflowid")}'><i title="编辑任务" class="icon alignMiddle  icon-wrench"></i></a>
                    </div>
					</td>
					{/if}
				{/foreach}
			</tr>
		{/foreach}
		</tbody>
	</table>

	<!--added this div for Temporarily -->
	{if $LISTVIEW_ENTIRES_COUNT eq '0'}
	<table class="emptyRecordsDiv">
		<tbody>
			<tr>
				<td>
					{vtranslate('LBL_NO')} {vtranslate($MODULE, $QUALIFIED_MODULE)} {vtranslate('LBL_FOUND')}
				</td>
			</tr>
		</tbody>
	</table>
	{/if}
</div>
{/strip}