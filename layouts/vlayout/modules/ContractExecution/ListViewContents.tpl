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
                        <th nowrap style="width:90px">??????</th>
                    </tr>
                    </thead>
                    {foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=listview}
                        <tr class="listViewEntries"  data-id='{$LISTVIEW_ENTRY['id']}' data-mode='{$LISTVIEW_ENTRY["modulename"]}' data-recordUrl='index.php?module={$MODULE}&view=Detail&record={$LISTVIEW_ENTRY['id']}' id="{$MODULE}_listView_row_{$smarty.foreach.listview.index+1}">
                            {foreach key=fkey item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS name=fieldview}
                                <td class="listViewEntryValue"  nowrap>
                                    {if $LISTVIEW_HEADER['columnname'] eq 'subject'}
                                        <a class="btn-link" href='index.php?module={$MODULE}&view=Detail&record={$LISTVIEW_ENTRY['id']}' target="_block">{vtranslate( $LISTVIEW_ENTRY[$LISTVIEW_HEADER['columnname']],$MODULE)}</a>
                                    {else}
                                        {vtranslate(uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE),$MODULE)}
                                    {/if}
                                </td>
                            {/foreach}
                            <td class="listViewEntryValue" >
                                {if $LISTVIEW_HEADER@last}
                                    <div  style="width:100px">
                                        <a  href="index.php?module={$MODULE}&view=Detail&record={$LISTVIEW_ENTRY['id']}"><i title="????????????" class="icon-th-list alignMiddle"></i></a>&nbsp;

                                        {*{if $IS_MODULE_EDITABLE}*}
                                            {*<a  href='index.php?module={$MODULE}&view=Edit&record={$LISTVIEW_ENTRY['id']}'  target="_block"><i title="{vtranslate('LBL_EDIT', $MODULE)}" class="icon-pencil alignMiddle"></i></a>&nbsp;*}
                                        {*{/if}*}
                                        {*{if $IS_MODULE_DELETABLE}*}
                                            {*<a class="deleteRecordButton"><i title="??????" class="icon-trash alignMiddle" ></i></a>*}
                                        {*{/if}*}
                                        {*{if $LISTVIEW_ENTRY['workflowsnode'] eq '?????????'}*}
                                            {*<a class="toVoidButton" style="margin-left: 6px;" dd="{$LISTVIEW_ENTRY['contractguaranteeid']}"><i title="??????????????????????????????"  class="icon-folder-close alignMiddle"></i></a>*}
                                        {*{/if}*}
                                        {*<a class="cancelContractButton" style="margin-left: 6px;" dd="{$LISTVIEW_ENTRY['contractguaranteeid']}"><i title="??????????????????"  class="icon-remove-sign alignMiddle"></i></a>*}
                                        {*{if $IS_EXPORTABLE eq true }*}
                                            {*{if $LISTVIEW_ENTRY['is_exportable'] eq 'able_toexport' }*}
                                                {*<a class="noNeedToExportButton"><i title="????????????????????????" class="icon-resize-small alignMiddle"></i></a>&nbsp;*}
                                            {*{elseif $LISTVIEW_ENTRY['is_exportable'] eq 'unable_export' }*}
                                                {*<a class="needToExportButton"><i title="??????????????????????????????" class="icon-resize-full  alignMiddle"></i></a>&nbsp;*}
                                            {*{/if}*}
                                        {*{/if}*}
                                    </div>
                                {/if}
                            </td>
                        </tr>
                    {/foreach}
                </table>
            </div>
        </div>
    </div>



    <div class="modal-dialog" style="width: 1000px;display: none;left:0; right:0; top:60px; bottom:0;position:fixed;height: 500px;" id="show_data2" >
        <div class="modal-content" style="height: 500px;">
            <div class="modal-body" style="overflow: hidden;">
                <button type="button" class="bootbox-close-button close" data-dismiss="modal" aria-hidden="true" style="margin-top: -10px;" id="closedmodal">??</button>
                <br/>
                <div style="overflow: hidden;overflow-y: auto;height:420px;">
                    <table class="table table-bordered blockContainer showInlineTable detailview-table LBL_CUSTOM_INFORMATION">
                        <thead>
                        <tr>
                            <th class="blockHeader" colspan="5">
                                <img class="cursorPointer alignMiddle blockToggle  hide" src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;">
                                <img class="cursorPointer alignMiddle blockToggle" src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">
                                &nbsp;&nbsp; ??????????????????&nbsp;&nbsp;<b class="pull-right"></b></th>
                        </tr>
                        </thead>
                        <tbody id='show_data1'>



                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
{/strip}
