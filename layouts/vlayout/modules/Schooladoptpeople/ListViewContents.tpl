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
                <th nowrap>
                    <div  class="noclick" style="width: 100%;height:100%;">
                        <button type="button" class="btn btn-success checkedall">全选</button>
                        <button type="button" class="btn btn-inverse checkedinverse">反选</button>
                        <button type="button" class="btn btn-primary stampall">考核设置</button>
                    </div>
                </th>
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
                {if $smarty.get.public eq 'unaudited'}
				<th nowrap style="width:90px">操作</th>
                {/if}
                <th nowrap style="width:90px">操作</th>
			</tr>
		</thead>
		{foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=listview}
		<tr class="listViewEntries"  data-id='{$LISTVIEW_ENTRY['id']}' data-recordUrl='index.php?module={$MODULE}&view=Detail&record={$LISTVIEW_ENTRY['id']}' id="{$MODULE}_listView_row_{$smarty.foreach.listview.index+1}">
            <td style="text-align: left;">
                {if $LISTVIEW_ENTRY['instructor'] eq ''}
                <div class="deletedflag"><label style="height:100%;display: inline-block;">
                        <input type="checkbox" value="{$LISTVIEW_ENTRY['id']}" class="entryCheckBox" name="Detailrecord[]" ></label>
                    <button type="button" data-id="{$LISTVIEW_ENTRY['id']}" style="visibility:hidden;" class="btn btn stamp">考核</button>
                    <button type="button" data-id="{$LISTVIEW_ENTRY['id']}" style="display:inline" class="btn btn stamp">考核设置</button>
                </div>
                {/if}
            </td>
            {foreach key=fkey item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS name=fieldview}
                <td class="listViewEntryValue {if $LISTVIEW_HEADER['columnname'] eq 'instructor'}td_instructor{/if} 
                 {if $LISTVIEW_HEADER['columnname'] eq 'assessmentdate'}td_iassessmentdate{/if} 
                 {if $LISTVIEW_HEADER['columnname'] eq 'assessmentresult'}td_assessmentresult{/if}
                "  nowrap>
                    {if $LISTVIEW_HEADER['columnname'] eq 'p_schoolrecruitid'}
                         <a class="btn-link" href='index.php?module=Schoolrecruit&view=Detail&record={$LISTVIEW_ENTRY['p_schoolrecruitid_reference']}' target="_blank">{vtranslate( $LISTVIEW_ENTRY[$LISTVIEW_HEADER['columnname']],$MODULE)}</a>
                    {elseif $LISTVIEW_HEADER['columnname'] eq 'schoolresumeid'}
                        <a class="btn-link" href='index.php?module=Schoolresume&view=Detail&record={$LISTVIEW_ENTRY['schoolresumeid_reference']}' target="_blank">{vtranslate( $LISTVIEW_ENTRY[$LISTVIEW_HEADER['columnname']],$MODULE)}</a>
                    {elseif $LISTVIEW_HEADER['columnname'] eq 'p_chargeperson'}
                        <a class="btn-link" href='index.php?module=Schooladopt&view=Detail&record={$LISTVIEW_ENTRY['schooladoptid']}' target="_blank">{vtranslate( $LISTVIEW_ENTRY[$LISTVIEW_HEADER['columnname']],$MODULE)}</a>
                    {else}
                        {uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)}
                    {/if}
                </td>
			{/foreach}
            {if $smarty.get.public eq 'unaudited'}
            <td class="listViewEntryValue" >
                {if $LISTVIEW_HEADER@last}

                    <div  style="width:90px">
                        <a  href="index.php?module={$MODULE}&action=ExportPDF&record={$LISTVIEW_ENTRY['id']}" target="_blank"><i title="月份明细" class="icon-eye-open alignMiddle"></i></a>&nbsp;
                        <a  href='index.php?module={$MODULE}&action=ExportPDF&record={$LISTVIEW_ENTRY['id']}&fliter=all'  target="_blank"><i title="合计汇总" class="icon-gift alignMiddle"></i></a>&nbsp;
                    </div>
                {/if}
                {/if}
            </td>
            <td class="listViewEntryValue" >
                {if $LISTVIEW_HEADER@last}

                    <div  style="width:90px">
                        <a  href="index.php?module={$MODULE}&view=Detail&record={$LISTVIEW_ENTRY['id']}"><i title="详细信息" class="icon-th-list alignMiddle"></i></a>&nbsp;


                        <a  href="javascript:void(0)" class="set_adoptpeople"><i title="考核设置" class="icon-move alignMiddle"></i></a>&nbsp;

						{if $IS_MODULE_EDITABLE}
                        	<a  href='index.php?module={$MODULE}&view=Edit&record={$LISTVIEW_ENTRY['id']}'  target="_blank"><i title="{vtranslate('LBL_EDIT', $MODULE)}" class="icon-pencil alignMiddle"></i></a>&nbsp;
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

    <script type="text/javascript">
        var accessible_users_data = {
            {foreach key=OWNER_ID item=OWNER_NAME from=$ACCESSIBLE_USERS}
                {$OWNER_ID} : '{$OWNER_NAME}',
            {/foreach}
        };
        $(function(){
            $('.entryCheckBox').iCheck({
                checkboxClass: 'icheckbox_minimal-orange'
            });
        });
        /*{*var accessible_users = "<select id=\"ddddd\" style='width: 200px;' class=\"chzn-select\" name=\"reportsower\"> <optgroup label=\"{vtranslate('LBL_USERS')}\">   <option value=''>请选择</option>  {foreach key=OWNER_ID item=OWNER_NAME from=$ACCESSIBLE_USERS} <option value=\"{$OWNER_ID}\" data-picklistvalue= '{$OWNER_ID}' {if $FIELD_VALUE eq $OWNER_ID} selected {/if} data-userId=\"{$CURRENT_USER_ID}\">{$OWNER_NAME}</option> {/foreach} </optgroup>    </select>";*}*/
    </script>
    <style type="text/css">
        .select2-drop{
            z-index: 1000043;
        }
    </style>
{/strip}
