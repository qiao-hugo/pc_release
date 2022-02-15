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
{assign var=ISCANDO value=$RECORD->personalAuthority('AchievementallotStatistic','adjust')}

{assign var = ALPHABETS_LABEL value = vtranslate('LBL_ALPHABETS', 'Vtiger')}
{assign var = ALPHABETS value = ','|explode:$ALPHABETS_LABEL}



<div class="listViewEntriesDiv contents-bottomscroll" style="overflow:auto;">
	<div class="bottomscroll-div" >
	<input type="hidden" value="{$ORDER_BY}" id="orderBy">
	<input type="hidden" value="{$SORT_ORDER}" id="sortOrder">

	{assign var=WIDTHTYPE value=$CURRENT_USER_MODEL->get('rowheight')}
	{assign var=ISCANDO value=$RECORD->personalAuthority('AchievementSummary','adjust')}
	<table class="table listViewEntriesTable">
		<thead>
			<tr class="listViewHeaders">
				<td nowrap><input type="checkbox" class="all_user">&nbsp;&nbsp;<input type="checkbox"  name="checkAll" class="entryCheckBox" title="计算到账业绩,实际提成"></td>
				{foreach key=KEY item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
					{if $LISTVIEW_HEADER['ishidden']}
						{continue}
					{/if}
				<th nowrap data-field="{$LISTVIEW_HEADER['columnname']}">
					{vtranslate($KEY, $MODULE)}
				</th>
				{/foreach}
				<th nowrap style="width:90px">操作</th>
{*				<th nowrap style="width:90px">操作</th>*}
			</tr>
		</thead>
		{foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=listview}
		<tr class="listViewEntries"  data-id='{$LISTVIEW_ENTRY['achievementid']}' data-id='{$LISTVIEW_ENTRY['achievementallotid']}' data-recordUrl='index.php?module=AchievementSummary&view=Detail&record={$LISTVIEW_ENTRY['achievementid']}&mode=showDetailViewByMode&requestMode=full&tab_label=销售业绩汇总表 详细内容' id="AchievementSummary_listView_row_{$smarty.foreach.listview.index+1}">
{*		<tr class="listViewEntries"  data-id='{$LISTVIEW_ENTRY['achievementid']}' data-recordUrl='index.php?module=AchievementSummary&view=Detail&record={$LISTVIEW_ENTRY['achievementid']}' id="AchievementSummary_listView_row_{$smarty.foreach.listview.index+1}">*}
			<td nowrap>
				<input type="checkbox" name="checked_user[]" class="separte_user" data-id="{$LISTVIEW_ENTRY['achievementid']}"
					  data-modulestatus="{$LISTVIEW_ENTRY['modulestatus']}"  data-confirmstatus="{$LISTVIEW_ENTRY['confirmstatus']}" data-achievementmonth="{$LISTVIEW_ENTRY['achievementmonth']}"
						data-username="{$LISTVIEW_ENTRY['userid']}">&nbsp;&nbsp;
				<input type="checkbox" value="{$LISTVIEW_ENTRY['achievementid']}"  data-amount="{$LISTVIEW_ENTRY['realarriveachievement']}" data-oldamount="{$LISTVIEW_ENTRY['actualroyalty']}" class="entryCheckBox" name="Detailrecord[]" title="到账业绩,实际提成">
			</td>
            {foreach key=fkey item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS name=fieldview}
				{if $LISTVIEW_HEADER['ishidden']}
					{continue}
				{/if}
                <td class="listViewEntryValue {$LISTVIEW_HEADER['columnname']}"  nowrap>
                	{if $LISTVIEW_HEADER['columnname'] eq scalling}
                 		{uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)}%
                 	{else}
                 		{uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)}
                 	{/if}
                </td>
            {/foreach}
            <td class="listViewEntryValue">
                {if $LISTVIEW_HEADER@last}
                    <div  style="width:90px">
						<a  href="index.php?module=AchievementSummary&view=Detail&record={$LISTVIEW_ENTRY['achievementid']}&mode=showDetailViewByMode&requestMode=full&tab_label=销售业绩汇总表 详细内容">详情</a>&nbsp;
						{if $ISCANDO}
						<i title="调整提成" class="alignMiddle modfiAchievement" data-id="{$LISTVIEW_ENTRY['achievementid']}" data-uroyalty="{$LISTVIEW_ENTRY['uroyalty']}" data-uroyaltyremark="{$LISTVIEW_ENTRY['uroyaltyremark']}" data-valuedata="{$LISTVIEW_ENTRY['userid']}/{$LISTVIEW_ENTRY['achievementmonth']}/{vtranslate($LISTVIEW_ENTRY['achievementtype'],'AchievementSummary')}">调整提成</i>
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
