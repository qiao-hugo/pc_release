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
    <style>
        .ellipsis {
            display:inline-block;
            width:140px;
            white-space:nowrap;
            overflow:hidden;
            text-overflow:ellipsis;
        }
        .add-execution{
            padding: 10px;
            height: 40px;
        }
        .add-execution-tip{
            float:left;
            width: 10%;
            text-align: right;
        }
        .add-execution-node{
            float: left;
            width:12%;
            text-align: right;
        }
        .add-execution-info{
            float:left;
            width: 70%;
            margin-left: 10px;
        }
        .checkloglist {
            font-size: 13px;
            margin-left: 20px;
            list-style:none;
        }
        .checkloglist li {
            position: relative;
            padding: 0 0 20px 20px;
            border-left: 1px solid #ccc;
        }
        .checkloglist li .checktime {
            display: inline-block;
            width: 150px;
            vertical-align: middle;
        }
        .checkloglist li .collator {
            display:inline-block;
            width:200px;
            white-space:nowrap;
            overflow:hidden;
            text-overflow:ellipsis;
            vertical-align:middle
        }
        .checkloglist li .checkresult {
            vertical-align:middle;
            margin-left:10px;
        }
        .checkloglist li .serialnum {
            position: absolute;
            display: block;
            left: -10px;
            top: 0px;
            border: 2px #178fdd solid;
            color: #178fdd;
            background-color: #fff;
            font-size: 12px;
            width: 16px;
            height: 16px;
            text-align: center;
            line-height: 16px;
            vertical-align: middle;
            border-radius: 50%;
        }
    </style>
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
                        <tr class="listViewEntries"  data-id='{$LISTVIEW_ENTRY['id']}' data-mode='{$LISTVIEW_ENTRY["modulename"]}' data-recordUrl='index.php?module={$MODULE}&view=Detail&record={$LISTVIEW_ENTRY['id']}' id="{$MODULE}_listView_row_{$smarty.foreach.listview.index+1}" data-accountid="{$LISTVIEW_ENTRY['accountid_reference']}">
                            {foreach key=fkey item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS name=fieldview}
                                <td class="listViewEntryValue" {if $LISTVIEW_HEADER['columnname'] eq 'receivestatus' and uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)=='有逾期'} style="background-color: red" {/if}  nowrap>
                                    {if $LISTVIEW_HEADER['columnname'] eq 'subject'}
                                        <a class="btn-link" href='index.php?module={$MODULE}&view=Detail&record={$LISTVIEW_ENTRY['id']}' target="_block">{vtranslate( $LISTVIEW_ENTRY[$LISTVIEW_HEADER['columnname']],$MODULE)}</a>
                                    {elseif $LISTVIEW_HEADER['columnname'] eq 'contractnum'}
                                        <a class="btn-link relation_account"  data-mode="contract_no" data-id='{$LISTVIEW_ENTRY['id']}' >{$LISTVIEW_ENTRY[$LISTVIEW_HEADER['columnname']]}</a>
                                    {elseif $LISTVIEW_HEADER['columnname'] eq 'bussinesstypenum'}
                                        <a class="btn-link relation_account"  data-mode="bussinesstype" data-id='{$LISTVIEW_ENTRY['id']}' >{$LISTVIEW_ENTRY[$LISTVIEW_HEADER['columnname']]}</a>
                                    {elseif $LISTVIEW_HEADER['columnname'] eq 'contractoverduebalance'}
                                        <span style="color: red">{uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)}</span>
                                    {elseif $LISTVIEW_HEADER['columnname'] eq 'receivestatus' and uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)=='有逾期'}
                                        <span style="color: white">{vtranslate(uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE),'AccountReceivable')}</span>
                                    {elseif $LISTVIEW_HEADER['columnname'] eq 'checkresult'}
                                        {if in_array($LISTVIEW_ENTRY['checkresult'], ['符合', '不符合'])}
                                            <span title="点击展开核对记录" class="checklog">{uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)}<i class="icon-list-alt"></i></span>
                                        {else}
                                            <span>{uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)}</span>
                                        {/if}
                                    {elseif $LISTVIEW_HEADER['columnname'] eq 'checkremark'}
                                        <span class="ellipsis" title="{uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)}">{uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)}</span>
                                    {else}
                                        {vtranslate(uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE),'AccountReceivable')}
                                    {/if}
                                </td>
                            {/foreach}
                            <td class="listViewEntryValue" >
                                {if $LISTVIEW_HEADER@last}
                                    <div  style="width:100px">
                                        <a href="index.php?module={$MODULE}&view=Detail&record={$LISTVIEW_ENTRY['id']}"><i title="详细信息" class="icon-th-list alignMiddle"></i></a>&nbsp
                                        <a href="#" class="collate"><i title="核对" class="icon-check alignMiddle"></i></a>
                                    </div>
                                {/if}
                            </td>
                        </tr>
                    {/foreach}
                </table>
            </div>
        </div>
    </div>
    </div>
{/strip}
