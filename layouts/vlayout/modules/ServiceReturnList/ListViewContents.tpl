{*<!--
/**********
**修改字段为workflowsnode时换行
 *
 *
 *
*
 *************/
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
                    </tr>
                    </thead>
                    {foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=listview}
                        <tr class="listViewEntries"  data-id='{$LISTVIEW_ENTRY['commentsid_reference']}' data-recordUrl="index.php?module=ServiceComments&view=Detail&record={$LISTVIEW_ENTRY['commentsid_reference']}&mode=showRecentComments&tab_label=ModComments&page=1" id="{$MODULE}_listView_row_{$smarty.foreach.listview.index+1}">
                            {foreach key=fkey item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS name=fieldview}
                                <td class="listViewEntryValue"  nowrap>
                                    {if $LISTVIEW_HEADER['columnname'] eq 'isfollow'}
                                        <span class="badge badge-{if $LISTVIEW_ENTRY['isfollow'] eq '进行中'}success{elseif $LISTVIEW_ENTRY['isfollow'] eq '已完成'}info{elseif $LISTVIEW_ENTRY['isfollow'] eq '未开始'}{elseif $LISTVIEW_ENTRY['isfollow'] eq '已超期'}warning{/if}">{$LISTVIEW_ENTRY['isfollow']}</span>
                                    {elseif $LISTVIEW_HEADER['columnname'] eq 'subject'}
                                        <a class="btn-link" href='index.php?module={$MODULE}&view=Detail&record={$LISTVIEW_ENTRY['id']}' target="_block">{vtranslate( $LISTVIEW_ENTRY[$LISTVIEW_HEADER['columnname']],$MODULE)}</a>
                                    {else}
                                        {uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)}
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
