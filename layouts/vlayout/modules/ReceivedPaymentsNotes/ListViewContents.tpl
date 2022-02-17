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
{assign var=LINKFIELD value=['unit_price','reality_date','owncompany','checkid']}

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
				<th nowrap style="width:90px">操作</th>
			</tr>
		</thead>
		{foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=listview}
		<tr class="listViewEntries"  data-id='{$LISTVIEW_ENTRY['id']}' data-recordUrl='index.php?module={$MODULE}&view=Detail&record={$LISTVIEW_ENTRY['id']}' id="{$MODULE}_listView_row_{$smarty.foreach.listview.index+1}">
            {foreach key=fkey item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS name=fieldview}
                <td class="listViewEntryValue"  nowrap>
                    {if $LISTVIEW_HEADER['columnname'] eq 'subject'}
                        <a class="btn-link" href='index.php?module={$MODULE}&action=Detail&record={$LISTVIEW_ENTRY['id']}' target="_block">{vtranslate( $LISTVIEW_ENTRY[$LISTVIEW_HEADER['columnname']],$MODULE)}</a>
                    {elseif $LISTVIEW_HEADER['columnname'] eq 'related_to'}
                        {$LISTVIEW_ENTRY[$LISTVIEW_HEADER['columnname']]}
					{elseif $LISTVIEW_HEADER['columnname'] eq 'contract_no'}
						{$LISTVIEW_ENTRY['contract_no']}
					{elseif $LISTVIEW_HEADER['columnname'] eq 'matchstatus'}
						{if $LISTVIEW_ENTRY['matchstatus']  eq 1}
							匹配解除中
						{else}
							已匹配
						{/if}
					{else}
                        {uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)}
                    {/if}
                </td>
			{/foreach}
            <td class="listViewEntryValue" >
                {if $LISTVIEW_HEADER@last}
                    <div  style="width:100px;">
						{if $LISTVIEW_ENTRY['matchstatus']  eq 1 && $LISTVIEW_ENTRY['isShowButton']  eq 1}
							<a  class="relieve" style="color: #02A7F0" data-id="{$LISTVIEW_ENTRY['receivedpaymentsid_reference']}">确认解绑</a>
						{elseif $LISTVIEW_ENTRY['matchstatus'] neq 1}
							<a class="relieve" style="color: #02A7F0" data-id="{$LISTVIEW_ENTRY['receivedpaymentsid_reference']}">解绑</a>&nbsp;
						{/if}
						<a  class="changeBinding" style="color: #02A7F0;margin-left: 5px"   data-id="{$LISTVIEW_ENTRY['receivedpaymentsid_reference']}">换绑</a>
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
