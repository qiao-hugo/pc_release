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
	<input type="hidden" value="{$ORDER_BY}" id ="orderBy">
	<input type="hidden" value="{$SORT_ORDER}" id="sortOrder">

	{assign var=WIDTHTYPE value=$CURRENT_USER_MODEL->get('rowheight')}
	<table class="table listViewEntriesTable">
		<thead>
			<tr class="listViewHeaders">
				{if $ISSIGN}
				<th nowrap class="noclick">
					<div  class="noclick" style="width: 100%;height:100%;">
						<input type="checkbox" value="" class="entryCheckBox1 checkedinverse" name="Deta"  title="全选"></label>
					</div>
				</th>
				{/if}
				{foreach key=KEY item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
				<th nowrap data-field="{$LISTVIEW_HEADER['columnname']}">
					{vtranslate($KEY, $MODULE)}
				</th>
				{/foreach}
				<th nowrap style="width:110px">操作</th>
			</tr>
		</thead>


		{foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=listview}
		<tr class="listViewEntries"  data-id='{$LISTVIEW_ENTRY['id']}' data-recordUrl='index.php?module={$MODULE}&view=Detail&record={$LISTVIEW_ENTRY['id']}' id="{$MODULE}_listView_row_{$smarty.foreach.listview.index+1}">
            {if $ISSIGN }
			<td style="text-align: left;">
				{if in_array($LISTVIEW_ENTRY['modulestatus'],array('b_check')) && $LISTVIEW_ENTRY['workflowsnode'] eq '发票领取' && $LISTVIEW_ENTRY['havasigned'] neq '是'}
				<div class="deletedflag">
					<label style="height:100%;display: inline-block;">
						<input type="checkbox" value="{$LISTVIEW_ENTRY['id']}" class="entryCheckBox" name="Detailrecord[]"></label>
					{*<button type="button" data-id="{$LISTVIEW_ENTRY['id']}" style="visibility:hidden;" class="btn btn stamp">领用</button>*}
				</div>
				{/if}
			</td>
            {/if}
			{foreach key=fkey item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS name=fieldview}
                <td class="listViewEntryValue {if $LISTVIEW_HEADER['columnname'] eq 'invoicestatus'}{$LISTVIEW_HEADER['columnname']}{$LISTVIEW_ENTRY['id']}{/if}"  nowrap title="双击查看详情">
                    {*uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)*}
                    {*{if $LISTVIEW_HEADER['columnname'] eq 'taxtype' or $LISTVIEW_HEADER['columnname'] eq 'businessnames'}*}
                    {if $LISTVIEW_HEADER['columnname'] eq 'invoiceno'}
                    <a class="btn-link" href='index.php?module={$MODULE}&view=Detail&record={$LISTVIEW_ENTRY['id']}' target="_block">{vtranslate( $LISTVIEW_ENTRY[$LISTVIEW_HEADER['columnname']],$MODULE)}</a>
                    {elseif $LISTVIEW_HEADER['columnname'] eq 'contractid'}
						<a class="btn-link" href='index.php?module={$LISTVIEW_ENTRY['modulename']}&view=Detail&record={$LISTVIEW_ENTRY['contractid_reference']}' target="_block">{$LISTVIEW_ENTRY[$LISTVIEW_HEADER['columnname']]}</a>
					{elseif $LISTVIEW_HEADER['columnname'] eq 'accountid'}
						{if $LISTVIEW_ENTRY['modulename'] eq 'ServiceContracts'}
							<a class="btn-link" href='index.php?module=Accounts&view=Detail&record={$LISTVIEW_ENTRY['accountid_reference']}' target="_block">{$LISTVIEW_ENTRY[$LISTVIEW_HEADER['columnname']]}</a>
                    	{else}
							<a class="btn-link" href='index.php?module=Vendors&view=Detail&record={$LISTVIEW_ENTRY['accountid_reference']}' target="_block">{$LISTVIEW_ENTRY[$LISTVIEW_HEADER['columnname']]}</a>
						{/if}
					{else}
							{uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)}
                    {/if}
                </td>
            {/foreach}
            <td class="listViewEntryValue" >
                {if $LISTVIEW_HEADER@last}

                    <div  style="width:110px">
                        <a  href="index.php?module={$MODULE}&view=Detail&record={$LISTVIEW_ENTRY['id']}" target="_blank"><i title="详细信息" class="icon-th-list alignMiddle"></i></a>&nbsp;

						{if $IS_MODULE_EDITABLE}
                        	<a  href='index.php?module={$MODULE}&view=Edit&record={$LISTVIEW_ENTRY['id']}&billproperty={$LISTVIEW_ENTRY['billproperty']}&applicationtype={$LISTVIEW_ENTRY['applicationtype']}'  target="_block"><i title="{vtranslate('LBL_EDIT', $MODULE)}" class="icon-pencil alignMiddle"></i></a>&nbsp;
                        {/if}
						{if $OFFSETAMOUNT}
							<a  href="#" class="updateSurplusAmountButton" data-surplusamount="{$LISTVIEW_ENTRY['surplusamount']}" data-id="{$LISTVIEW_ENTRY['id']}"><i title="欠票金额抵消" class="icon-move alignMiddle"></i></a>&nbsp;
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
        $(function(){
            $('.entryCheckBox').iCheck({
                checkboxClass: 'icheckbox_minimal-orange'
            });
        });

	</script>
{/strip}
