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
			width:200px;
			white-space:nowrap;
			overflow:hidden;
			text-overflow:ellipsis;
		}
		.collateloglist {
			font-size: 13px;
			margin-left: 20px;
			list-style:none;
		}
		.collateloglist li {
			position: relative;
			padding: 0 0 20px 20px;
			border-left: 1px solid #ccc;
		}
		.collateloglist li .serialnum {
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
		.collateloglist li .collatetime {
			display: inline-block;
			width: 150px;
			vertical-align: middle;
		}
		.collateloglist li .collator {
			display:inline-block;
			width:200px;
			white-space:nowrap;
			overflow:hidden;
			text-overflow:ellipsis;
			vertical-align:middle
		}
		.collateloglist li .status {
			vertical-align:middle;
			margin-left:10px;
		}
		.collateloglist li .remark {
			word-wrap:break-word;
		}
		input[type="checkbox"] {
			margin:0;
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
				<th>
					<input type="checkbox" name="checkAll" ></label>
				</th>
				{foreach key=KEY item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
					{if in_array($LISTVIEW_HEADER['columnname'], ['artificialclassfication'])}
						{continue}
					{/if}
					<th nowrap data-field="{$LISTVIEW_HEADER['columnname']}">
					{vtranslate($KEY, $MODULE)}
				</th>

				{/foreach}
				<th nowrap style="width:90px">人工分类</th>
			</tr>
		</thead>
		{assign var=IS_COLLATE value=$MODULE_MODEL->exportGrouprt('ReceivedPayments','COLLATE')}
		{foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=listview}
		<tr class="listViewEntries"  data-id='{$LISTVIEW_ENTRY['receivedpaymentsid']}' data-recordUrl='index.php?module=ReceivedPayments&view=Detail&record={$LISTVIEW_ENTRY['receivedpaymentsid']}' id="{$MODULE}_listView_row_{$smarty.foreach.listview.index+1}">
			<td style="">
				<input type="checkbox" value="{$LISTVIEW_ENTRY['id']}"  data-amount="{$LISTVIEW_ENTRY['unit_price']}"  data-oldamount="{$LISTVIEW_ENTRY['standardmoney']}" class="entryCheckBox" name="Detailrecord[]"></label>
			</td>
            {foreach key=fkey item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS name=fieldview}
				{if in_array($LISTVIEW_HEADER['columnname'], ['artificialclassfication'])}
					{assign var=artificialclassfication value=$LISTVIEW_ENTRY[$LISTVIEW_HEADER['columnname']]}
					{continue}
				{/if}
                <td class="listViewEntryValue {if $LISTVIEW_HEADER['columnname'] eq 'allowinvoicetotal'}allowinvoicetotal_value{/if} {if $LISTVIEW_HEADER['columnname'] eq 'ismatchdepart'}ismatchdepart_value{/if} {if $LISTVIEW_HEADER['columnname'] eq 'relatetoid'}relatetoid_value{/if}"  nowrap>
                      {*uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY[$LISTVIEW_HEADER['columnname']],$MODULE)*}
                    {if $LISTVIEW_HEADER['columnname'] eq 'relatetoid'}
						<a class="btn-link" href='index.php?module={$LISTVIEW_ENTRY['modulename']}&view=Detail&record={$LISTVIEW_ENTRY['relatetoid_reference']}' target="_block">{$LISTVIEW_ENTRY[$LISTVIEW_HEADER['columnname']]}</a>
					{elseif $LISTVIEW_HEADER['columnname'] eq 'collate_num'}
						{if $LISTVIEW_ENTRY['collate_num'] >= 1}
							<span title="点击展开核对记录" class="collatelog">{uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)}<i class="icon-list-alt"></i></span>
						{else}
							{uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)}
						{/if}
					{elseif in_array($LISTVIEW_HEADER['columnname'], ['first_collate_status', 'last_collate_status'])}
						{if $LISTVIEW_ENTRY[$LISTVIEW_HEADER['columnname']] eq 'fit'}
							符合
						{elseif $LISTVIEW_ENTRY[$LISTVIEW_HEADER['columnname']] eq 'unfit'}
							不符合
						{/if}
					{elseif in_array($LISTVIEW_HEADER['columnname'], ['first_collate_remark', 'last_collate_remark'])}
						<span class="ellipsis" title="{uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)}">{uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)}</span>
					{else}
                        {uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)}
                    {/if}
                </td>
            {/foreach}
            <td class="listViewEntryValue">
                {if $LISTVIEW_HEADER@last}
					<select name="artificialclassfication" style="width: 100px;" class="artificialclassfication" id="artificialclassfication{$LISTVIEW_ENTRY['receivedpaymentsid']}" data-receivedpaymentsid="{$LISTVIEW_ENTRY['receivedpaymentsid']}">
						<option value="0"></option>
						{foreach $ARTIFICIALCLASSFICATIONS as $pkey=>$ARTIFICIALCLASSFICATION}
							<optgroup label="{$pkey}">
								{foreach $ARTIFICIALCLASSFICATION as $key=>$vlaue}
									<option {if $key==$artificialclassfication}selected{/if} value="{$key}">{$vlaue}</option>
								{/foreach}
							</optgroup>
						{/foreach}
					</select>
                    {*<div  style="width:120px">*}
                        {*<a href="index.php?module=ReceivedPayments&view=Detail&record={$LISTVIEW_ENTRY['receivedpaymentsid']}"><i title="详细信息" class="icon-th-list alignMiddle"></i></a>*}
					{*</div>*}
                {/if}
            </td>
		</tr>
		{/foreach}

	</table>

</div>
</div>
    </div>
{/strip}

