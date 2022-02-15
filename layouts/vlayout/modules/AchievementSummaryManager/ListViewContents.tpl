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
	<input type="hidden" id="date" value="{$date}" />
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
				{*<th nowrap class="noclick">
					<div  class="noclick" style="width: 100%;height:100%;position: relative;">
						<label><input type="checkbox" class="entryCheckBox1 checkedinverse" name="Deta"   style="position:absolute;top:0;left:0px;width:10px;height:10px;"></label>
				</th>*}
				{foreach key=KEY item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
					{if $LISTVIEW_HEADER['ishidden']}
						{continue}
					{/if}
					<th nowrap data-field="{$LISTVIEW_HEADER['columnname']}">
						{vtranslate($KEY, $MODULE)}
					</th>
				{/foreach}
			</tr>
		</thead>
		{foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=listview}
		<tr class="listViewEntries"  data-id='{$LISTVIEW_ENTRY['achievementallotid']}' data-recordUrl='index.php?module=AchievementallotStatistic&view=Detail&record={$LISTVIEW_ENTRY['achievementallotid']}&mode=showDetailViewByMode' id="AchievementallotStatistic_listView_row_{$smarty.foreach.listview.index+1}">
			{foreach key=fkey item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS name=fieldview}
				{if $LISTVIEW_HEADER['ishidden']}
					{continue}
				{/if}
                <td class="listViewEntryValue"  nowrap>
                	{if ($LISTVIEW_HEADER['columnname'] eq scalling) || ($LISTVIEW_HEADER['columnname'] eq renewal_commission && $LISTVIEW_ENTRY['renewal_commission'] neq 0) }
						{uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)}%
                 	{else}
						{if $LISTVIEW_HEADER['columnname'] neq renewal_commission}
							{uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)}
						{/if}
                 	{/if}
                </td>
            {/foreach}
		</tr>
		{/foreach}
		
	</table>

</div>
</div>
    </div>
{/strip}
