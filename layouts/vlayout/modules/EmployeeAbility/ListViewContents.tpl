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
    <style>
        #div_toop {
            background-color:rgba(193, 223, 251, 1);
            padding: 10px;
            border: 1px solid #eeeeee;
            box-shadow: 5px 5px 5px #eeeeee;
        }

        #div_toop .title {
            width: 70px;
            display: inline-block;
            text-align: right;
            margin-right: 10px;
        }
    </style>
    <script type="text/javascript" src="libraries/jquery/Fixed-Header-Table/jquery.fixedheadertable.js"></script>
    <link href="libraries/jquery/Fixed-Header-Table/css/defaultTheme.css" rel="stylesheet" media="screen" />
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
                <table class="table listViewEntriesTable" id="listViewContentTable">
                    <thead>
                    <tr class="listViewHeaders">

                        {foreach key=KEY item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
                            <th nowrap data-field="{$LISTVIEW_HEADER['columnname']}" style="width: 35px;text-align: center;font-weight: bold;">
                                {vtranslate($KEY, $MODULE)}
                            </th>
                        {/foreach}
                        <th nowrap style="width:90px">操作</th>
                    </tr>
                    </thead>
                    <tr class="listViewHeaders">

                        {foreach key=KEY item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
                            <td nowrap data-field="{$LISTVIEW_HEADER['columnname']}_list_text"  style="word-wrap:break-word;word-break:break-all;width: 80px;text-align: center">
                                {if in_array({$LISTVIEW_HEADER['columnname']},array('userid','stafflevel','gradename','employeestage','isdimission'))}

                                {else}
                                    {vtranslate("{$LISTVIEW_HEADER['columnname']}_list_text", $MODULE)}
                                {/if}
                            </td>



                        {/foreach}
                        <th nowrap style="width:90px"></th>
                    </tr>
                    {foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=listview}
                        <tr class="listViewEntries"  data-id='{$LISTVIEW_ENTRY['employeeabilityid']}' data-recordUrl='index.php?module={$MODULE}&view=Detail&record={$LISTVIEW_ENTRY['employeeabilityid']}&realoperate={setoperate($LISTVIEW_ENTRY['employeeabilityid'],MODULE)}' id="{$MODULE}_listView_row_{$smarty.foreach.listview.index+1}">

                            {foreach key=fkey item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS name=fieldview}
                                {if ($LISTVIEW_ENTRY['stafflevel']=='intermediate' and in_array($fkey,$JUNIOR)) or
                                ($LISTVIEW_ENTRY['stafflevel']=='senior' and (in_array($fkey,$INTERMEDIAE) or  in_array($fkey,$JUNIOR)))
                                }
                                    <td class="listViewEntryValue" style="text-align: center" nowrap>
                                        /
                                    </td>
                                {else}
                                    {if  (in_array($LISTVIEW_ENTRY[$fkey],array('0','nosubmitted')) && (
                                    ($LISTVIEW_ENTRY['stafflevel']=='junior' and (in_array($fkey,$INTERMEDIAE) or  in_array($fkey,$SENIOR) )) or
                                    ($LISTVIEW_ENTRY['stafflevel']=='intermediate' and in_array($fkey,$SENIOR))
                                    ))}
                                        <td class="listViewEntryValue" style="text-align: center" nowrap>
                                            /
                                        </td>
                                    {else}

                                        <td class="listViewEntryValue" style="width: 35px;text-align: center" nowrap>
                                            {if is_numeric({$LISTVIEW_ENTRY[$fkey]}) && $LISTVIEW_ENTRY[$fkey]>=80}
                                                {$LISTVIEW_ENTRY[$fkey]}
                                            {elseif ($LISTVIEW_ENTRY[$fkey]<80 and is_numeric({$LISTVIEW_ENTRY[$fkey]})) and $LISTVIEW_ENTRY[$fkey]>0}
                                                <span  class="reject" data-rejector="" data-rejectreason="" data-rejectcolumn="{$fkey}"
                                                    data-employeeabilityid="{$LISTVIEW_ENTRY['employeeabilityid']}" style="color: red;" >
                                                     {$LISTVIEW_ENTRY[$fkey]}
                                                </span>
                                            {else}
                                                <span {if $LISTVIEW_ENTRY[$fkey]=='reject' }
                                                    class="reject" data-rejector="" data-rejectreason="" data-rejectcolumn="{$fkey}"
                                                    data-employeeabilityid="{$LISTVIEW_ENTRY['employeeabilityid']}" style="color: red;" {/if}>
                                                    {vtranslate(uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE),$MODULE)}
                                                </span>
                                            {/if}
                                        </td>
                                    {/if}
                                {/if}
                            {/foreach}
                            <td class="listViewEntryValue" >
                                {if $LISTVIEW_HEADER@last}

                                    <div  style="width:90px"><i title="详细信息" class="icon-th-list alignMiddle"></i>&nbsp;

                                        {*{if $LISTVIEW_ENTRY['deleted']}*}
                                            {*<a class="deleteRecordButton"><i title="删除" class="icon-trash alignMiddle"></i></a>*}
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
{/strip}
