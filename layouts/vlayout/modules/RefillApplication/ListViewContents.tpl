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
<input type="hidden" id="rechargesource" value="{$RECHARGESOURCE}" />

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
				<th nowrap class="noclick">
					<div  class="noclick" style="width: 100%;height:100%;position: relative;">
						<label  title="打印,导出"><input type="checkbox" class="entryCheckBox1 checkedinverse" name="Deta"  title="打印,导出" style="position:absolute;top:0;left:14px;width:10px;height:10px;">打&nbsp;&nbsp;印</label>
						{*<button type="button" class="btn btn-success checkedall">全选</button><button type="button" class="btn btn-inverse checkedinverse">反选</button>*}</div>
				</th>
				<th nowrap class="noclick">
					<div  class="noclick" style="width: 100%;height:100%;position: relative;">
						<label title="匹配回款"><input type="checkbox" class="inversePayments" name="DetaPayments"  title="匹配回款" style="position:absolute;top:0;left:14px;width:10px;height:10px;">匹&nbsp;&nbsp;款</label>
                        {*<button type="button" class="btn btn-success checkedall">全选</button><button type="button" class="btn btn-inverse checkedinverse">反选</button>*}</div>
				</th>
				<th nowrap>明细</th>
				{foreach key=KEY item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
				<th nowrap data-field="{$LISTVIEW_HEADER['columnname']}">
					{vtranslate($KEY, $MODULE)}
				</th>
				{/foreach}
				<th nowrap style="width:90px">操作</th>
			</tr>
		</thead>
		{foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=listview}
		<tr class="listViewEntries"  data-id='{$LISTVIEW_ENTRY['id']}' data-recordUrl='index.php?module={$MODULE}&view=Detail&record={$LISTVIEW_ENTRY['id']}&realoperate={setoperate($LISTVIEW_ENTRY['id'],MODULE)}' id="{$MODULE}_listView_row_{$smarty.foreach.listview.index+1}">
			<td style="text-align: left;">
                {if !in_array($LISTVIEW_ENTRY['modulestatus'],array('c_cancel','a_exception'))}
					<div class="deletedflag">
						<label style="height:100%;display: inline-block;">
							<input type="checkbox" value="{$LISTVIEW_ENTRY['id']}" data-amount="{$LISTVIEW_ENTRY['actualtotalrecharge']}" class="entryCheckBox" name="Detailrecord[]" title="打印"></label>
                        {*<button type="button" data-id="{$LISTVIEW_ENTRY['id']}" style="visibility:hidden;" class="btn btn stamp">领用</button>*}
					</div>
                {/if}
			</td>
			<td style="text-align: left;">
                {if $LISTVIEW_ENTRY['modulestatus'] eq 'c_complete' && $LISTVIEW_ENTRY['paymentsflag'] eq 1 && $LISTVIEW_ENTRY['grossadvances']>0}
					<div class="deletedflagPayments">
						<label style="height:100%;display: inline-block;">
							<input type="checkbox" value="{$LISTVIEW_ENTRY['id']}" class="entryCheckBox" data-contractid="{$LISTVIEW_ENTRY['servicecontractsid_reference']}" name="DetailrecordPayments[]" title="匹配回款"></label>
                        {*<button type="button" data-id="{$LISTVIEW_ENTRY['id']}" style="visibility:hidden;" class="btn btn stamp">领用</button>*}
					</div>
				{/if}
			</td>
			<td class="detailList" data-id='{$LISTVIEW_ENTRY['id']}' data-statusd="plus" data-reqstatus="Y" nowrap><span class="label label-c_complete" title="点击显示明细列表">
							<i class="icon-plus icon-white"></i>
						</span></td>
            {foreach key=fkey item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS name=fieldview}
                <td class="listViewEntryValue"  nowrap>
					{if $LISTVIEW_HEADER['columnname'] eq 'workflowsnode'}
						{','|str_replace:'<br>':$LISTVIEW_ENTRY[$LISTVIEW_HEADER['columnname']]}
                    {elseif $LISTVIEW_HEADER['columnname'] eq 'subject'}
                            <a class="btn-link" href='index.php?module={$MODULE}&view=Detail&record={$LISTVIEW_ENTRY['id']}' target="_block">{vtranslate( $LISTVIEW_ENTRY[$LISTVIEW_HEADER['columnname']],$MODULE)}</a>
                    {else}
                        {uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)}
                    {/if}
                </td>
            {/foreach}
            <td class="listViewEntryValue" >
                {if $LISTVIEW_HEADER@last}

                    <div  style="width:90px"><i title="详细信息" class="icon-th-list alignMiddle"></i>&nbsp;
                        <a  href='index.php?module={$MODULE}&view=Edit&record={$LISTVIEW_ENTRY['id']}'  target="_block"><i title="{vtranslate('LBL_EDIT', $MODULE)}" class="icon-pencil alignMiddle"></i></a>&nbsp;

                        {if $DOCANCEL && $LISTVIEW_ENTRY['modulestatus'] eq 'a_exception'}
							<a class="docancel"><i title="作废" class="icon-remove-sign alignMiddle"></i></a>&nbsp;
                        {/if}
                        {*{if  $LISTVIEW_ENTRY['financialStateAuthority'] eq 1 && $LISTVIEW_ENTRY['modulestatus'] eq 'c_complete' && ($LISTVIEW_ENTRY['iscushion'] eq '是')}
							<a class="financialstate" data-value="{$LISTVIEW_ENTRY['grossadvances']}" data-status="{if $LISTVIEW_ENTRY['financialstate'] eq '是'}yes{else}no{/if}">
								{if $LISTVIEW_ENTRY['financialstate'] eq '是'}
									//<i title="恢复财务销账" class="icon-lock alignMiddle"></i>
								{else}
									{if $LISTVIEW_ENTRY['iscushion'] eq '是' && in_array($LISTVIEW_ENTRY['rechargesource'],array('Accounts','Vendors','NonMediaExtraction'))}
											<i title="财务销账" class="icon-flag alignMiddle"></i>
									{/if}
								{/if}
							</a>&nbsp;
            			{/if}*}
                        {if $LISTVIEW_ENTRY['deleted']}
                            <a class="deleteRecordButton"><i title="删除" class="icon-trash alignMiddle"></i></a>
                        {/if}
                    </div>
                {/if}
            </td>
		</tr>
		{/foreach}
                <input type="hidden" name="is_advances" id="is_advances" value="{$is_advances}"/>
                <input type="hidden" name="contract_no" id="contract_no" value="{$contract_no}"/>
                <input type="hidden" name="userid" id="userid" value="{$userid}"/>
	</table>

</div>
</div>
    </div>
{/strip}
