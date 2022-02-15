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
				{foreach key=KEY item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
				<th nowrap data-field="{$LISTVIEW_HEADER['columnname']}">
					{vtranslate($KEY, $MODULE)}
				</th>
				{/foreach}
				<th nowrap style="width:90px">操作</th>
			</tr>
		</thead>
		{foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=listview}
		<tr class="listViewEntries"  data-id='{$LISTVIEW_ENTRY['id']}' data-id='{$LISTVIEW_ENTRY['id']}' data-recordUrl='index.php?module=ClosingDate&view=Detail&record={$LISTVIEW_ENTRY['id']}&mode=showDetailViewByMode&requestMode=full&tab_label=业绩日期设置 详细内容' id="ClosingDate_listView_row_{$smarty.foreach.listview.index+1}">
{*		<tr class="listViewEntries"  data-id='{$LISTVIEW_ENTRY['achievementid']}' data-recordUrl='index.php?module=AchievementSummary&view=Detail&record={$LISTVIEW_ENTRY['achievementid']}' id="AchievementSummary_listView_row_{$smarty.foreach.listview.index+1}">*}
            {foreach key=fkey item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS name=fieldview}
                <td class="listViewEntryValue"  nowrap>
                	{if $LISTVIEW_HEADER['columnname'] eq 'date'}
                 		每月{uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)}号
                 	{else}
                 		{uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)}
                 	{/if}
                </td>
            {/foreach}
            <td class="listViewEntryValue">
                {if $LISTVIEW_HEADER@last}
                    <div  style="width:90px">
						{if $ISCANDO }
						<a  href="###" class="applicationUpdateDate">调整日期</a>&nbsp;
						{/if}
                       <a  href="index.php?module=ClosingDate&view=Detail&record={$LISTVIEW_ENTRY['id']}&mode=showDetailViewByMode&requestMode=full&tab_label=销售业绩汇总表 详细内容">详情</a>&nbsp;
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
