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
						<th nowrap style="width:90px">操作</th>
						{foreach key=KEY item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
							<th nowrap data-field="{$LISTVIEW_HEADER['columnname']}">
								{vtranslate($KEY, $MODULE)}
							</th>
						{/foreach}
					</tr>
					</thead>
					{foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=listview}


						{foreach key=fkey item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS name=fieldview}
							{if $smarty.foreach.fieldview.index eq 0}
								<tr class="listViewEntries"  data-id='{$LISTVIEW_ENTRY['newsalesorderid']}' data-recordUrl='index.php?module={$LISTVIEW_ENTRY['modulename']}&view=Detail&record={if $LISTVIEW_ENTRY['modulename']=='AchievementallotStatistic'}{$LISTVIEW_ENTRY['originalmoduleid']}&mode=showDetailViewByMode{else}{$LISTVIEW_ENTRY['newsalesorderid']}{/if}&realoperate={setoperate($LISTVIEW_ENTRY['newsalesorderid'],{$LISTVIEW_ENTRY['modulename']})}' id="{$MODULE}_listView_row_{$smarty.foreach.listview.index+1}">
								<td class="listViewEntryValue" >
									<div  style="width:90px">
										<a target="_blank"  href="index.php?module={$LISTVIEW_ENTRY['modulename']}&view=Detail&record={if $LISTVIEW_ENTRY['modulename']=='AchievementallotStatistic'}{$LISTVIEW_ENTRY['originalmoduleid']}&mode=showDetailViewByMode{else}{$LISTVIEW_ENTRY['newsalesorderid']}{/if}&realoperate={setoperate($LISTVIEW_ENTRY['newsalesorderid'],{$LISTVIEW_ENTRY['modulename']})}"><i title="详细信息" class="icon-th-list alignMiddle"></i></a>&nbsp;
									</div>
								</td>
							{/if}
							<td class="listViewEntryValue"  nowrap>
								{if $LISTVIEW_HEADER['columnname'] eq 'accountname'}
									<a class="btn-link" >{$LISTVIEW_ENTRY['accountname']}</a>
								{elseif $LISTVIEW_HEADER['columnname'] eq 'salesorder_nono'}
									<a class="btn-link" href="index.php?module={$LISTVIEW_ENTRY['modulename']}&view=Detail&record={$LISTVIEW_ENTRY['newsalesorderid']}&realoperate={setoperate($LISTVIEW_ENTRY['newsalesorderid'],{$LISTVIEW_ENTRY['modulename']})}"  target="_block">{vtranslate($LISTVIEW_ENTRY[$LISTVIEW_HEADER['columnname']], 'Vtiger')}</a>
								{elseif $LISTVIEW_HEADER['columnname'] eq 'modulestatus'}
                                    {if $LISTVIEW_ENTRY['modulestatus'] eq 'c_complete'}
                                        <span class="label label-c_complete">{vtranslate($LISTVIEW_ENTRY['modulestatus'],'Vtiger')}</span>
                                    {else}
                                        <span class="label label-b_actioning">{vtranslate($LISTVIEW_ENTRY['modulestatus'],'Vtiger')}</span>
                                    {/if}

								{else}
									{uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)}
								{/if}
							</td>


						{/foreach}

						</tr>
					{/foreach}

				</table>

			</div>
		</div>
	</div>
{/strip}
