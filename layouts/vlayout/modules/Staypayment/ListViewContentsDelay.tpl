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
                        <th nowrap>代付款编号</th>
                        <th nowrap>合同编号</th>
                        <th nowrap>合同客户名称</th>
                        <th nowrap>打款人全称</th>
                        <th nowrap>签订代付款金额</th>
                        <th nowrap>代付款已使用金额</th>
                        <th nowrap>代付款剩余金额</th>
                        <th nowrap>首次回款匹配时间</th>
                        <th nowrap>代付款最晚签收时间</th>
                        <th nowrap>代付款签收时间</th>
                        <th nowrap>代付款状态</th>
                        <th nowrap>是否延期</th>
                        <th nowrap>是否模拟新建</th>
                    </tr>
                    </thead>
                    {foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=listview}
                        <tr class="listViewEntries"  data-id='{$LISTVIEW_ENTRY['id']}' data-recordUrl='index.php?module={$MODULE}&view=Detail&record={$LISTVIEW_ENTRY['id']}&realoperate={setoperate($LISTVIEW_ENTRY['id'],MODULE)}' id="{$MODULE}_listView_row_{$smarty.foreach.listview.index+1}">
                                <td class="listViewEntryValue"  nowrap>{$LISTVIEW_ENTRY['staymentcode']}</td>
                                <td class="listViewEntryValue"  nowrap>{$LISTVIEW_ENTRY['contractid']}</td>
                                <td class="listViewEntryValue"  nowrap>{$LISTVIEW_ENTRY['accountid']}</td>
                                <td class="listViewEntryValue"  nowrap>{$LISTVIEW_ENTRY['payer']}</td>
                                <td class="listViewEntryValue"  nowrap>{$LISTVIEW_ENTRY['staypaymentjine']}</td>
                                <td class="listViewEntryValue"  nowrap>{bcsub($LISTVIEW_ENTRY['staypaymentjine'],$LISTVIEW_ENTRY['surplusmoney'],2)}</td>
                                <td class="listViewEntryValue"  nowrap>{$LISTVIEW_ENTRY['surplusmoney']}</td>
                                <td class="listViewEntryValue"  nowrap>{$LISTVIEW_ENTRY['changetime']}</td>
                                <td class="listViewEntryValue"  nowrap>{$LISTVIEW_ENTRY['last_sign_time']}</td>
                                <td class="listViewEntryValue"  nowrap>{if $LISTVIEW_ENTRY['modulestatus'] eq 'c_complete'}{$LISTVIEW_ENTRY['workflowstime']}{/if}</td>
                                <td class="listViewEntryValue"  nowrap>
                                    {if $LISTVIEW_ENTRY['modulestatus'] eq 'c_complete'}
                                        <span class="label label-c_complete">已签收</span>
                                    {else}
                                        <span class="label label-default">未签收</span>
                                    {/if}
                                </td>
                                <td class="listViewEntryValue"  nowrap>
                                    {if  $LISTVIEW_ENTRY['modulestatus'] eq 'c_complete'&&$LISTVIEW_ENTRY['last_sign_time']&&(strtotime(date('Y-m-d H:i:s'))-strtotime($LISTVIEW_ENTRY['last_sign_time'])) gt  0 }
                                        是
                                    {else}
                                        否
                                    {/if}
                                </td>
                            <td class="listViewEntryValue"  nowrap>
                                {if $LISTVIEW_ENTRY['isauto'] eq 1}
                                    是
                                {else}
                                    否
                                {/if}
                            </td>
                        </tr>
                    {/foreach}

                </table>

            </div>
        </div>
    </div>
{/strip}
