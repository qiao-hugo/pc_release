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
				<td>
					<input type="checkbox"  name="checkAll" ></label>
				</td>
				{foreach key=KEY item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
				<th nowrap data-field="{$LISTVIEW_HEADER['columnname']}">
					{vtranslate($KEY, $MODULE)}
				</th>

				{/foreach}
				<th nowrap style="width:90px">操作</th>
			</tr>
		</thead>
		{assign var=IS_COLLATE value=$MODULE_MODEL->exportGrouprt('ReceivedPayments','COLLATE')}
		{foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=listview}
		<tr class="listViewEntries"  data-id='{$LISTVIEW_ENTRY['receivedpaymentsid']}' data-recordUrl='index.php?module={$MODULE}&view=Detail&record={$LISTVIEW_ENTRY['receivedpaymentsid']}' id="{$MODULE}_listView_row_{$smarty.foreach.listview.index+1}">
			<td style="display: inline-block;">
				<input type="checkbox" value="{$LISTVIEW_ENTRY['id']}"  data-amount="{$LISTVIEW_ENTRY['unit_price']}"  data-oldamount="{$LISTVIEW_ENTRY['standardmoney']}" class="entryCheckBox" name="Detailrecord[]"></label>
			</td>
            {foreach key=fkey item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS name=fieldview}
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
					{elseif in_array($LISTVIEW_HEADER['columnname'], ['first_collate_time', 'last_collate_time'])}
						{if $LISTVIEW_ENTRY[$LISTVIEW_HEADER['columnname']]!='0000-00-00 00:00:00'}{uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)}{/if}
					{else}
                        {uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)}
                    {/if}
                </td>
            {/foreach}
            <td class="listViewEntryValue">
                {if $LISTVIEW_HEADER@last}
                    <div  style="width:120px">
                        <a href="index.php?module={$MODULE}&view=Detail&record={$LISTVIEW_ENTRY['receivedpaymentsid']}"><i title="详细信息" class="icon-th-list alignMiddle"></i></a>&nbsp;
{*                        {if $IS_EDIT eq 1 && $LISTVIEW_ENTRY['receivedstatus'] neq 'virtualrefund'}*}
{*                            <a class="cleanReceive" target="_block"><i title="清除回款匹配" class="icon-flag alignMiddle"></i></a>&nbsp;*}
{*                        {/if}*}
                        {if $IS_MODULE_EDITABLE && $LISTVIEW_ENTRY['receivedstatus'] neq 'virtualrefund'}
                        	<a  href='index.php?module={$MODULE}&view=Edit&record={$LISTVIEW_ENTRY['receivedpaymentsid']}'  target="_block"><i title="{vtranslate('LBL_EDIT', $MODULE)}" class="icon-pencil alignMiddle"></i></a>&nbsp;
                        {/if}
                        {if  $LISTVIEW_ENTRY['setReceiveStatus'] eq 1 && $LISTVIEW_ENTRY['receivedstatus'] neq 'virtualrefund'}
                        	<a data-status="{$LISTVIEW_ENTRY['receivedstatus']}" class="setReceiveStatus" target="_block"><i title="修改回款类型" class="icon-share alignMiddle"></i></a>&nbsp;
                        {/if}
                        {if $LISTVIEW_ENTRY['NonPayCertificate'] eq 1 && empty($LISTVIEW_ENTRY['relatetoid']) && $LISTVIEW_ENTRY['receivedstatus']=='normal'}
							<a data-status="{$LISTVIEW_ENTRY['receivedstatus']}" class="NonPayCertificate" target="_block"><i title="设未提供代付款证明" class="icon-adjust alignMiddle"></i></a>&nbsp;
                        {/if}
                        {if $LISTVIEW_ENTRY['dochargebacks'] eq 1}
						<a data-status="{$LISTVIEW_ENTRY['receivedstatus']}"  data-chargebacksa="{$LISTVIEW_ENTRY['rechargeableamount']}" class="chargebacks" target="_block"><i title="扣款" class="icon-tint alignMiddle"></i></a>&nbsp;
                        {/if}
                        {if $LISTVIEW_ENTRY['dorechargeable'] eq 1}
							<a data-chargebacks="{$LISTVIEW_ENTRY['chargebacks']}"  data-unit_price="{$LISTVIEW_ENTRY['unit_price']}" class="dorechargeableamount" target="_block"><i title="可使用金额" class="icon-magnet alignMiddle"></i></a>&nbsp;
                        {/if}
						{if $IS_SPLIT eq 1}
                            <a data-status="{$LISTVIEW_ENTRY['receivedstatus']}" class="splitReceive" target="_block"><i title="回款拆分" class="icon-move alignMiddle"></i></a>&nbsp;
                        {/if}
                        {if {$LISTVIEW_ENTRY['dobackcash']} eq 1}
							<a data-status="{$LISTVIEW_ENTRY['receivedstatus']}" class="dobackcash" target="_block"><i title="设为返点款" class="icon-plane alignMiddle"></i></a>&nbsp;
                        {/if}
                        {if $ISREPEATRECEIVEDPAYMENTS eq 1 && $LISTVIEW_ENTRY['receivedstatus'] neq 'virtualrefund'}
							<a data-status="{$LISTVIEW_ENTRY['receivedstatus']}" class="repeatReceive" target="_block"><i title="重新匹配" class="icon-repeat alignMiddle"></i></a>&nbsp;
                        {/if}
                        {if $IS_MODULE_DELETABLE && $LISTVIEW_ENTRY['receivedstatus'] eq 'normal'}
                            <a data-status="{$LISTVIEW_ENTRY['receivedstatus']}" class="deleteRecord"><i title="删除" class="icon-trash alignMiddle"></i></a>&nbsp;
                        {/if}
                        {if $isEditAllowinvoicetotal && $LISTVIEW_ENTRY['receivedstatus'] neq 'virtualrefund'}
                            <a data-status="{$LISTVIEW_ENTRY['allowinvoicetotal']}" class="isEditAllowinvoicetotal" target="_block"><i title="修改可开票金额" class="alignMiddle glyphicon icon-cog"></i></a>
                        {/if}
						{if $IS_COLLATE}
							<a href="#" class="collate"><i title="核对" class="icon-check alignMiddle"></i></a>
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
{/strip}
