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
                    {if $LISTVIEW_HEADER['columnname']=='smownerid' and  $smarty.request.public eq 'NoDaily'}
                        <th nowrap data-field="{$LISTVIEW_HEADER['columnname']}">
                            未写报表人员
                        </th>
                    {else}
                        <th nowrap data-field="{$LISTVIEW_HEADER['columnname']}">
                            {vtranslate($KEY, $MODULE)}
                        </th>
                    {/if}
				{/foreach}
				{/if}
				<th nowrap style="width:90px">操作</th>
			</tr>
		</thead>
        {assign var=DOIT value=['oldcustomers','allamount','issigncontract','discount']}
        {if $smarty.request.public neq 'NoDaily'}
		{foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=listview}
		<tr class="listViewEntries"  data-id='{$LISTVIEW_ENTRY['id']}' data-recordUrl='index.php?module={$MODULE}&view=Detail&record={$LISTVIEW_ENTRY['id']}' id="{$MODULE}_listView_row_{$smarty.foreach.listview.index+1}">
            {foreach key=fkey item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS name=fieldview}
                {if  in_array($LISTVIEW_HEADER['columnname'],array('todaycontent','tommorrowcontent','latestreply','todayquestion','todayfeel'))}
                    <td class="listViewEntryValue" data-title="{$LISTVIEW_ENTRY[$LISTVIEW_HEADER['columnname']]}"  nowrap>
                        {if $LISTVIEW_ENTRY[$LISTVIEW_HEADER['columnname']] neq 'null'}
                            {uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)}
                        {/if}
                    </td>
                {else}
                    <td class="listViewEntryValue"  nowrap>
                         {if in_array($LISTVIEW_HEADER['columnname'],$DOIT)}
                             {if $LISTVIEW_HEADER['columnname'] eq 'discount'}
                                 {if $LISTVIEW_ENTRY['dealamount'] < $LISTVIEW_ENTRY['marketprice']}
                                     {($LISTVIEW_ENTRY['dealamount']/$LISTVIEW_ENTRY['marketprice'])|number_format:"2"}
                                 {else}
                                     1
                                 {/if}
                             {else}
                                 {if $LISTVIEW_ENTRY[$LISTVIEW_HEADER['columnname']] eq 1}
                                     是
                                 {else}
                                    否
                                 {/if}
                             {/if}
                        {else}
                             {if $LISTVIEW_ENTRY[$LISTVIEW_HEADER['columnname']] neq 'null'}
                                 {uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)}
                             {/if}

                        {/if}
                    </td>
                {/if}
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
        {else}
            {foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=listview}
                <tr class="listViewEntries1">
                    {foreach key=fkey item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS name=fieldview}
                        <td class="listViewEntryValue1"  nowrap>
                            {if $LISTVIEW_ENTRY[$LISTVIEW_HEADER['columnname']] neq 'null'}
                                {uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)}
                            {/if}
                        </td>
                    {/foreach}

                    <td class="listViewEntryValue" >

                    </td>
                </tr>
            {/foreach}
        {/if}
	</table>

</div>
</div>
    </div>
    <script>
        $(function () {
            $(".listViewEntryValue").mouseover(function (e) {
                title=$(this).data('title');
                if(!title){
                    return;
                }

                var div_toop = '';
                div_toop += ' <div id="div_toop" style="width: 300px;">';
                div_toop += '<p>'+title+'</p>';
                div_toop += '</div>';

                $("body").append(div_toop);
                $("#div_toop")
                    .css({
                        "top": (e.pageY + 10) + "px",
                        "position": "absolute",
                        "left": (e.pageX + 20) + "px",
                    }).show("fast");
            }).mouseout(function () {
                $("#div_toop").remove();
            }).mousemove(function (e) {
                $("#div_toop")
                    .css({
                        "top": (e.pageY + 10) + "px",
                        "position": "absolute",
                        "left": (e.pageX + 20) + "px",
                    });
            });
        })

    </script>
{/strip}
