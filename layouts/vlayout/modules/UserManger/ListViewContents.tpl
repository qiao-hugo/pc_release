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
				<td><input type="checkbox" class="all_user"></td>
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
				<th nowrap style="width:90px">操作</th>
			</tr>
		</thead>
		{foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=listview}
		<tr class="listViewEntries"  data-id='{$LISTVIEW_ENTRY['id']}' data-recordUrl='index.php?module={$MODULE}&view=Detail&record={$LISTVIEW_ENTRY['id']}' id="{$MODULE}_listView_row_{$smarty.foreach.listview.index+1}">
            <td>
				{if $LISTVIEW_ENTRY['modulestatus'] == 'c_complete'}
				<input type="checkbox" name="checked_user[]" class="separte_user" data-id="{$LISTVIEW_ENTRY['id']}" data-last_name="{$LISTVIEW_ENTRY['last_name']}"
					   data-department="{$LISTVIEW_ENTRY['department']}" data-reports_to_id="{$LISTVIEW_ENTRY['reports_to_id']}" data-invoicecompany="{$LISTVIEW_ENTRY['invoicecompany']}"
						data-roleid="{$LISTVIEW_ENTRY['roleid_reference']}" data-employeelevel="{$LISTVIEW_ENTRY['employeelevel']}" data-departmentid="{$LISTVIEW_ENTRY['departmentid']}"
					   data-title="{$LISTVIEW_ENTRY['title']}" data-departmentid_reference="{$LISTVIEW_ENTRY['departmentid_reference']}"  data-companyid="{$LISTVIEW_ENTRY['companyid']}"
					   data-stafftype="{$LISTVIEW_ENTRY['stafftype']}" data-graduatetime="{$LISTVIEW_ENTRY['graduatetime']}" data-reports_to_id_reference="{$LISTVIEW_ENTRY['reports_to_id_reference']}"
				>
				{/if}
			</td>
			{foreach key=fkey item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS name=fieldview}
                <td class="listViewEntryValue"  nowrap>
					{if $LISTVIEW_HEADER['columnname'] eq 'last_name'}
					<a class="btn-link" href='index.php?module={$MODULE}&view=Detail&record={$LISTVIEW_ENTRY['id']}' target="_block">{vtranslate( $LISTVIEW_ENTRY[$LISTVIEW_HEADER['columnname']],$MODULE)}</a>
					{else}
                    {uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)}
					{/if}
                </td>
			{/foreach}
            <td class="listViewEntryValue" >
                {if $LISTVIEW_HEADER@last}
                    <div  style="width:90px">
                        <a  href="index.php?module={$MODULE}&view=Detail&record={$LISTVIEW_ENTRY['id']}"><i title="详细信息" class="icon-th-list alignMiddle"></i></a>&nbsp;
						{if $IS_MODULE_EDITABLE && $LISTVIEW_ENTRY['ownornot']==1}
                        	<a  href='index.php?module={$MODULE}&view=Edit&record={$LISTVIEW_ENTRY['id']}'  target="_block"><i title="{vtranslate('LBL_EDIT', $MODULE)}" class="icon-pencil alignMiddle"></i></a>&nbsp;
                        {/if}
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
<script>
	$(function () {
		$('.close').click(function () {
			$('.modal-backdrop').remove();
		});
		$('.cancelLinkContainer').click(function () {
			$('.modal-backdrop').remove();
		});
	})
</script>
