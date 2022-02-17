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
						{foreach key=KEY item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
							<th nowrap data-field="{$LISTVIEW_HEADER['columnname']}">
								{vtranslate($KEY, $MODULE)}
							</th>
						{/foreach}
						<th nowrap style="width: 100px">操作</th>
					</tr>
					</thead>
					{foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=listview}
						<tr class="listViewEntries"  data-id='{$LISTVIEW_ENTRY['id']}' data-recordUrl='index.php?module={$MODULE}&view=Detail&record={$LISTVIEW_ENTRY['id']}' id="{$MODULE}_listView_row_{$smarty.foreach.listview.index+1}" data-contractid="{$LISTVIEW_ENTRY['contractid']}" data-stage="{$LISTVIEW_ENTRY['stage']}">
							{foreach key=fkey item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS name=fieldview}
								<td class="listViewEntryValue {if $LISTVIEW_HEADER['columnname'] eq 'isautoclose'}isautoclose_value{/if} {if $LISTVIEW_HEADER['columnname'] eq 'contractstate'}contractstate_value{/if}"
									nowrap>
									{if $LISTVIEW_HEADER['columnname'] eq 'contract_no'}
										<a class="btn-link" href='index.php?module={$MODULE}&view=Detail&record={$LISTVIEW_ENTRY['id']}' target="_block">{vtranslate( $LISTVIEW_ENTRY[$LISTVIEW_HEADER['columnname']],$MODULE)}</a>
									{elseif $LISTVIEW_HEADER['columnname'] eq 'overduedays'}
										{uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)}
									{elseif $LISTVIEW_HEADER['columnname'] eq 'contractreceivable'}
										<span style="color: red">{uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)}</span>
									{elseif $LISTVIEW_HEADER['columnname'] eq 'commentcontent'}
										<span class="ellipsis" title="{uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)}">{uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)}</span>
									{elseif $LISTVIEW_HEADER['columnname'] eq 'checkresult'}
										{if in_array($LISTVIEW_ENTRY['checkresult'], ['符合', '不符合'])}
											<span title="点击展开核对记录" class="checklog">{uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)}<i class="icon-list-alt"></i></span>
										{else}
											<span>{uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)}</span>
										{/if}
									{elseif $LISTVIEW_HEADER['columnname'] eq 'checkremark'}
										<span class="ellipsis" title="{uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)}">{uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)}</span>
									{else}
										{vtranslate(uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE),$MODULE)}
									{/if}
								</td>
							{/foreach}
							<td class="listViewEntryValue">
								{if $LISTVIEW_HEADER@last}
									<div style="width: 120px">
									<a href="#" class="btn btn-small btn-link followUp" data-id="{$LISTVIEW_ENTRY['id']}" data-stageshow="{$LISTVIEW_ENTRY['stageshow']}" data-contractid="{$LISTVIEW_ENTRY['contractid']}">跟进</a>
									<a href="#" class="collate" data-stage="{$LISTVIEW_ENTRY['stage']}" data-contractid="{$LISTVIEW_ENTRY['contractid']}"><i title="核对" class="icon-check alignMiddle"></i></a>
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
