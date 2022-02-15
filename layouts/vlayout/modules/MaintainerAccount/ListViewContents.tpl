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
        &nbsp;&nbsp;&nbsp;  一共<span style="color:red">{$NUM_ACC}</span>个客户
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
	<table class="table listViewEntriesTable" >
		<thead>
			<tr class="listViewHeaders">
				{foreach key=KEY item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
				<th nowrap data-field="{if $LISTVIEW_HEADER['columnname'] eq 'endtime'}vtiger_salesorderproductsrel.{$LISTVIEW_HEADER['columnname']}{else}{$LISTVIEW_HEADER['columnname']}{/if}" class="listViewEntries">
					<img src="layouts/vlayout/skins/images/sort_all.png">{vtranslate($KEY, $MODULE)}
				</th>
				{/foreach}
			</tr>
		</thead>
		{foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=listview}
		<tr class="listViewEntries"  data-id='{$LISTVIEW_ENTRY['id']}'  id="{$MODULE}_listView_row_{$smarty.foreach.listview.index+1}">

				{foreach key=fkey item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS name=fieldview}
					<td class="listViewEntryValue" nowrap >
                        {if $LISTVIEW_HEADER['fieldlabel'] eq 'accountname'}
                            <a class="btn-link" href='index.php?module=Accounts&view=Detail&record={$LISTVIEW_ENTRY['客户编号']}' target="_block">{vtranslate( $LISTVIEW_ENTRY[$LISTVIEW_HEADER['columnname']],$MODULE)}</a>
                        {elseif $LISTVIEW_HEADER['fieldlabel'] eq '客服备注' || $LISTVIEW_HEADER['fieldlabel'] eq '最新的回访内容'||$LISTVIEW_HEADER['fieldlabel'] eq '下次回访内容'}
                           <div style="width: 400px;white-space: normal;">{vtranslate(uitypeformat($LISTVIEW_HEADER['uitype'],$LISTVIEW_ENTRY[$LISTVIEW_HEADER['fieldlabel']],$MODULE),$MODULE)}</div>
                            {*mb_substr(vtranslate(uitypeformat($LISTVIEW_HEADER['uitype'],$LISTVIEW_ENTRY[$LISTVIEW_HEADER['fieldlabel']],$MODULE),$MODULE),0,20,'utf-8')*}
                        {else}
                            {vtranslate(uitypeformat($LISTVIEW_HEADER['uitype'],$LISTVIEW_ENTRY[$LISTVIEW_HEADER['fieldlabel']],$MODULE),$MODULE)}
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
