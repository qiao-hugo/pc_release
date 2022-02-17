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
		<tr class="listViewEntries"  data-id='{$LISTVIEW_ENTRY['id']}' data-recordUrl='index.php?module={$MODULE}&view=Detail&record={$LISTVIEW_ENTRY['id']}' id="{$MODULE}_listView_row_{$smarty.foreach.listview.index+1}">
            {foreach key=fkey item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS name=fieldview}

                <td class="listViewEntryValue"  nowrap>
                    {if $LISTVIEW_HEADER['columnname'] eq 'related_to'}
                        <a class="btn-link" href='index.php?module=Accounts&view=Detail&record={$LISTVIEW_ENTRY['accountid']}' target="_block">{$LISTVIEW_ENTRY[$LISTVIEW_HEADER['columnname']]}</a>
                    {elseif  $LISTVIEW_HEADER['columnname'] eq 'accountrank'}
                        {if $LISTVIEW_ENTRY['accountrank'] eq 'iron_isv'}铁牌成交客户
                            {elseif $LISTVIEW_ENTRY['accountrank'] eq 'bras_isv'}铜牌成交客户
                            {elseif $LISTVIEW_ENTRY['accountrank'] eq 'silv_isv'}银牌成交客户
                            {elseif $LISTVIEW_ENTRY['accountrank'] eq 'gold_isv'}金牌成交客户
                            {elseif $LISTVIEW_ENTRY['accountrank'] eq 'chan_notv'}机会客户
                            {elseif $LISTVIEW_ENTRY['accountrank'] eq 'forp_notv'}40%意向客户
                            {elseif $LISTVIEW_ENTRY['accountrank'] eq 'norm_notv'}准客户
                            {elseif $LISTVIEW_ENTRY['accountrank'] eq 'spec_isv'}特殊关系客户
                            {elseif $LISTVIEW_ENTRY['accountrank'] eq 'eigp_notv'}80%意向客户
                            {elseif $LISTVIEW_ENTRY['accountrank'] eq 'sixp_notv'}60%意向客户
                            {elseif $LISTVIEW_ENTRY['accountrank'] eq 'visp_isv'}VIP成交客户
                            {elseif $LISTVIEW_ENTRY['accountrank'] eq 'wlad_isv'}物流网广告客户
                            {elseif $LISTVIEW_ENTRY['accountrank'] eq 'wlvp_isv'}物流网vip会员
                            {elseif $LISTVIEW_ENTRY['accountrank'] eq 'wlbr_isv'}物流网整合会员
                            {elseif $LISTVIEW_ENTRY['accountrank'] eq 'wlsi_isv'}物流网银牌会员
                            {elseif $LISTVIEW_ENTRY['accountrank'] eq 'wlgo_isv'}物流网金牌会员
                        {/if}
                    {else}
                        {uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)}
                    {/if}
                </td>
            {/foreach}
            <td class="listViewEntryValue" >
                {if $LISTVIEW_HEADER@last}

                    <div  style="width:90px">
                        <a  href="index.php?module={$MODULE}&view=Detail&record={$LISTVIEW_ENTRY['id']}"><i title="详细信息" class="icon-th-list alignMiddle"></i></a>&nbsp;

						{if $IS_MODULE_EDITABLE}
                        	<a  href='index.php?module={$MODULE}&view=Edit&record={$LISTVIEW_ENTRY['id']}'  target="_block"><i title="{vtranslate('LBL_EDIT', $MODULE)}" class="icon-pencil alignMiddle"></i></a>&nbsp;
                        {/if}
                        {if $IS_MODULE_DELETABLE}
                        	<a class="deleteRecordButton"><i title="删除" class="icon-trash alignMiddle"></i></a>
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
