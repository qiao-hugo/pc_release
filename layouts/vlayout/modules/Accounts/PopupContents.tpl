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
				{if $MULTI_SELECT}
				<td class="{$WIDTHTYPE}">
					<input type="checkbox" style="width: 28px; height: 28px;position: absolute; clip: rect(0px 22px 22px 0px);"  class="selectAllInCurrentPage" />
				</td>
				{/if}
				{foreach key=KEY item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
				<th class="{$WIDTHTYPE}">
					<a href="javascript:void(0);" class="listViewHeaderValues"  data-columnname="{$LISTVIEW_HEADER}">{vtranslate($KEY, $MODULE)}
					</a>
				</th>
				{/foreach}
			</tr>
		</thead>

        {foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=popupListView}
            <tr class="listViewEntries" data-id="{$LISTVIEW_ENTRY['id']}" data-name="{$LISTVIEW_ENTRY['accountname']}"  data-info='{ZEND_JSON::encode($LISTVIEW_ENTRY)}'
                      id="{$MODULE}_popUpListView_row_{$smarty.foreach.popupListView.index+1}">
                {if $MULTI_SELECT}
                    <td class="{$WIDTHTYPE}">
                        <input class="entryCheckBox" type="checkbox" />
                    </td>
                {/if}
                {foreach key=KEY item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}

                    <td class="listViewEntryValue {$WIDTHTYPE}">
						{$LISTVIEW_ENTRY[$LISTVIEW_HEADER]}
                    </td>
                {/foreach}
            </tr>
        {/foreach}


	</table>

	<!--added this div for Temporarily -->
{if $LISTVIEW_ENTIRES_COUNT eq '0'}
	<div class="row-fluid">
		<div class="emptyRecordsDiv">{vtranslate('LBL_NO', $MODULE)} {vtranslate($MODULE, $MODULE)} {vtranslate('LBL_FOUND', $MODULE)}.</div>
	</div>
{/if}
</div>
{/strip}
