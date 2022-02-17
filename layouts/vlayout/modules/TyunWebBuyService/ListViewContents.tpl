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
    {if $RECONCLILITION_LIST eq 'reconciliation' || $RECONCLILITION_LIST eq 'filed'}
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
                            <th nowrap >
                                编号
                            </th>
                            {foreach key=KEY item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
                                {if ($LISTVIEW_HEADER['columnname'] eq 'isitfiled' || $LISTVIEW_HEADER['columnname'] eq 'reconciliationresult') && $RECONCLILITION_LIST eq 'reconciliation' }
                                {else}
                                    <th nowrap data-field="{$LISTVIEW_HEADER['columnname']}">
                                        {vtranslate($KEY, $MODULE)}
                                    </th>
                                {/if}
                            {/foreach}
                        </tr>
                        </thead>
                        {assign var =key value=1}
                        {foreach  item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=listview}
                            <tr class="listViewEntries"  data-id='{$LISTVIEW_ENTRY['id']}' data-recordUrl='index.php?module={$MODULE}&view=Detail&record={$LISTVIEW_ENTRY['id']}&realoperate={setoperate($LISTVIEW_ENTRY['id'],MODULE)}&orderType=filed' id="{$MODULE}_listView_row_{$smarty.foreach.listview.index+1}">
                                <td>{$key++}</td>
                                {foreach key=fkey item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS name=fieldview}
                                    {if ($LISTVIEW_HEADER['columnname'] eq 'isitfiled' || $LISTVIEW_HEADER['columnname'] eq 'reconciliationresult') && $RECONCLILITION_LIST eq 'reconciliation'}
                                    {else}
                                        <td {if $LISTVIEW_ENTRY['errorType'] eq 2 }style="background: yellow;"{elseif $LISTVIEW_ENTRY['errorType'] eq 1 }style="background: #808080;" {/if} {if $LISTVIEW_ENTRY['errorType'] eq 3 }style="color: red;"{/if} class="listViewEntryValue {$LISTVIEW_HEADER['columnname']}"  nowrap {if $LISTVIEW_HEADER['columnname'] eq 'productname'} title="点我查看详情"  data-content="{$LISTVIEW_ENTRY['productnametitle']}"{/if}>
                                            {if $LISTVIEW_HEADER['columnname'] eq 'workflowsnode'}
                                                {','|str_replace:'<br>':$LISTVIEW_ENTRY[$LISTVIEW_HEADER['columnname']]}
                                            {elseif $LISTVIEW_HEADER['columnname'] eq 'contractname'}
                                                <a class="btn-link" {if  $LISTVIEW_ENTRY['errorType'] eq 3 }style="color: red;"{/if} href='index.php?module=ServiceContracts&view=Detail&record={$LISTVIEW_ENTRY['contractid']}' target="_block">{vtranslate( $LISTVIEW_ENTRY[$LISTVIEW_HEADER['columnname']],$MODULE)}</a>
                                            {else}
                                                {uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)}
                                            {/if}
                                        </td>
                                    {/if}
                                {/foreach}
                            </tr>
                        {/foreach}

                    </table>

                </div>
            </div>
        </div>
        {if $RECONCLILITION_LIST eq 'reconciliation'}
            <div style="margin-top: 1rem;margin-bottom: 2rem;">对账结果 对账成功：{$RECONCLILIATIONDATA['successnumber']} 对账失败：<font color="red">{$RECONCLILIATIONDATA['errornumber']}</font> （{$RECONCLILIATIONDATA['searchwhere']}）<span style="color: red;font-size: 20px;">注：灰色T云缺失的数据，黄色ERP缺失的数据</span></div>
            <input type="hidden" id="exportRecord"  value="{$RECONCLILIATIONDATA['id']}"/>
        {/if}
    {else}
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
                            <th nowrap>未开票金额</th>
                            <th nowrap>未收款金额</th>
                            <th nowrap>每月确认收入</th>
                            <th nowrap>累计确认收入</th>
                            <th nowrap>本月确认收入</th>
                            <th nowrap>是否到期</th>
                            <th nowrap>合同应收账款</th>
                            <th nowrap style="width:90px">操作</th>
                        </tr>
                        </thead>
                        {foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=listview}
                            <tr class="listViewEntries"  data-id='{$LISTVIEW_ENTRY['id']}' data-recordUrl='index.php?module={$MODULE}&view=Detail&record={$LISTVIEW_ENTRY['id']}&realoperate={setoperate($LISTVIEW_ENTRY['id'],MODULE)}' id="{$MODULE}_listView_row_{$smarty.foreach.listview.index+1}">

                                {foreach key=fkey item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS name=fieldview}
                                    <td class="listViewEntryValue {$LISTVIEW_HEADER['columnname']}"  nowrap {if $LISTVIEW_HEADER['columnname'] eq 'productname'} title="点我查看详情"  data-content="{$LISTVIEW_ENTRY['productnametitle']}"{/if}>
                                        {if $LISTVIEW_HEADER['columnname'] eq 'workflowsnode'}
                                            {','|str_replace:'<br>':$LISTVIEW_ENTRY[$LISTVIEW_HEADER['columnname']]}
                                        {elseif $LISTVIEW_HEADER['columnname'] eq 'contractname'}
                                            <a class="btn-link" href='index.php?module=ServiceContracts&view=Detail&record={$LISTVIEW_ENTRY['contractid']}' target="_block">{vtranslate( $LISTVIEW_ENTRY[$LISTVIEW_HEADER['columnname']],$MODULE)}</a>
                                    {elseif $LISTVIEW_HEADER['columnname'] eq 'customername'}
                                        <a class="btn-link" href='index.php?module=Accounts&view=Detail&record={$LISTVIEW_ENTRY['customerid_reference']}&realoperate={setoperate($LISTVIEW_ENTRY['customerid_reference'],'Accounts')}' target="_block">{vtranslate( $LISTVIEW_ENTRY[$LISTVIEW_HEADER['columnname']],$MODULE)}</a>
                                    {else}
                                            {uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)}
                                        {/if}
                                    </td>
                                {/foreach}
                                <td nowrap>{$LISTVIEW_ENTRY['tempinvoice']}</td><!--未开票金额-->
                                <td nowrap>{$LISTVIEW_ENTRY['temprepaty']}</td><!--未收款金额-->
                                <td nowrap>{$LISTVIEW_ENTRY['monthlyIncome']}</td><!--每月确认收入-->
                                <td nowrap>{$LISTVIEW_ENTRY['cumulativeIncome']}</td><!--累计确认收入-->
                                <td nowrap>{$LISTVIEW_ENTRY['thisMonthlyIncome']}</td><!--本月确认收入-->
                                <td nowrap>{$LISTVIEW_ENTRY['isMaturity']}</td><!--是否到期-->
                                <td nowrap>{$LISTVIEW_ENTRY['accountsreceivable']}</td><!--合同应收账款-->
                                <td class="listViewEntryValue" data-contractid="{$LISTVIEW_ENTRY['contractid_reference']}">
                                    {if $LISTVIEW_HEADER@last}

                                        <div  style="width:90px">
                                            <i title="重绑合同" class="icon-resize-small alignMiddle rebindContract" data-contractname="{$LISTVIEW_ENTRY['contractname']}" data-id='{$LISTVIEW_ENTRY['id']}'></i>&nbsp;
                                            <i title="取消下单" class="icon-ban-circle alignMiddle cancelOrder" data-value="{$LISTVIEW_ENTRY['contractprice']}"></i>&nbsp;
                                            <!--<i title="签收合同" class="icon-ok-circle alignMiddle signContract"></i>&nbsp;-->
                                            <!--<i title="线下对账" class="icon-tasks alignMiddle offlineReconciliation"></i>&nbsp;-->

                                            {if $LISTVIEW_ENTRY['deleted']}
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
    {/if}

{/strip}
