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
<input type="hidden" id="public" value="{$public}" />

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
                            <th nowrap data-field="{$LISTVIEW_HEADER['columnname']}" {if $LISTVIEW_HEADER['columnname'] eq 'receivedtotal' &&  $public neq 'NoComplete'}style="display: none;"{/if} {if in_array($LISTVIEW_HEADER['columnname'],['attachmenttype','firstreceivedate','eleccontractstatus','firstcontract','firstfrommarket','originator','originatormobile','elereceiver','elereceivermobile','contractattribute','clientproperty','bussinesstype','stageshow','agentname','categoryid','workflowsnode','contractstate','servicecontractsprint','isstandard','pagenumber','cancelid','accountsdue','signdate','canceltime','receiptnumber','receiveid','serviceid','multitype','confirmlasttime','supercollar','cantheinvoice','isconfirm','delayuserid','iscomplete','receiptorid','sideagreement','effectivetime','isguarantee','actualeffectivetime','ispay','fulldeliverytime','isstage','returndate','currencytype','supplementarytype','receiverabledate','executor','executedate','executestatus','voucher','frameworkcontract','settlementtype','settlementclause','file','productsearchid','remark','pre_deposit','cancelvoid','cancelfeeid','cancelremark','service_charge','account_opening_fee','createdtime','tax_point','modifiedby','modifiedtime','quotes_no','first_collate_operator','first_collate_time','collate_num','first_collate_status','first_collate_remark','last_collate_operator','last_collate_time','last_collate_status','last_collate_remark','isjoinactivity']) && $public eq 'NoComplete'} style="display: none;"{/if}>
                                {if $LISTVIEW_HEADER['columnname'] eq 'total'}合同金额{else}{vtranslate($KEY, $MODULE)}{/if}
                            </th>
                            {/foreach}
                            <th {if $public eq 'NoComplete' }style="display:none;"{/if} nowrap style="width:90px">操作</th>
                    </tr>
		</thead>
        {assign var=IS_COLLATE value=$MODULE_MODEL->exportGrouprt('ServiceContracts','COLLATE')}
		{foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=listview}
		<tr class="listViewEntries"  data-id='{$LISTVIEW_ENTRY['id']}' data-recordUrl='index.php?module={$MODULE}&view=Detail&record={$LISTVIEW_ENTRY['id']}' id="{$MODULE}_listView_row_{$smarty.foreach.listview.index+1}">

			{foreach key=fkey item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS name=fieldview}

			<td {if $LISTVIEW_HEADER['columnname'] eq 'receivedtotal' &&  $public neq 'NoComplete'}style="display: none;"{/if} {if in_array($LISTVIEW_HEADER['columnname'],['attachmenttype','firstreceivedate','eleccontractstatus','firstcontract','firstfrommarket','originator','originatormobile','elereceiver','elereceivermobile','contractattribute','clientproperty','bussinesstype','stageshow','agentname','categoryid','workflowsnode','contractstate','servicecontractsprint','isstandard','pagenumber','cancelid','accountsdue','signdate','canceltime','receiptnumber','receiveid','serviceid','multitype','confirmlasttime','supercollar','cantheinvoice','isconfirm','delayuserid','iscomplete','receiptorid','sideagreement','effectivetime','isguarantee','actualeffectivetime','ispay','fulldeliverytime','isstage','returndate','currencytype','supplementarytype','receiverabledate','executor','executedate','executestatus','voucher','frameworkcontract','settlementtype','settlementclause','file','productsearchid','remark','pre_deposit','cancelvoid','cancelfeeid','cancelremark','service_charge','account_opening_fee','createdtime','tax_point','modifiedby','modifiedtime','quotes_no','first_collate_operator','first_collate_time','collate_num','first_collate_status','first_collate_remark','last_collate_operator','last_collate_time','last_collate_status','last_collate_remark','isjoinactivity']) && $public eq 'NoComplete'} style="display: none;"{/if} class="listViewEntryValue {if $LISTVIEW_HEADER['columnname'] eq 'isautoclose'}isautoclose_value{/if} {if $LISTVIEW_HEADER['columnname'] eq 'contractstate'}contractstate_value{/if}" nowrap>
                {if $LISTVIEW_HEADER['columnname'] eq 'contract_no'}
                    <a class="btn-link" href='index.php?module={$MODULE}&view=Detail&record={$LISTVIEW_ENTRY['id']}' target="_block">{vtranslate( $LISTVIEW_ENTRY[$LISTVIEW_HEADER['columnname']],$MODULE)}</a>
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
                {else}
                    {uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)}
                {/if}
            </td>
			{/foreach}
            <td {if $public eq 'NoComplete' }style="display:none;"{/if}  class="listViewEntryValue" >
                {if $LISTVIEW_HEADER@last}

                    <div style="width:120px">
                    	{if $ISUPDATECONTRACTSCLOSE}
                        <a  href="#" class="updateContractsCloseButton" data-status="{$LISTVIEW_ENTRY['isautoclose']}"><i title="修改自动关闭状态" class="icon-share alignMiddle"></i></a>&nbsp;
                        {/if}

                        <a  href="index.php?module={$MODULE}&view=Detail&record={$LISTVIEW_ENTRY['id']}"><i title="详细信息" class="icon-th-list alignMiddle"></i></a>&nbsp;
                        {if $ISUPDATECONTRACTSSTATES}
                        <a  href="#" class="updateContractsStatesButton" data-status="{$LISTVIEW_ENTRY['contractstate']}"><i title="修改关闭状态" class="icon-move alignMiddle"></i></a>&nbsp;
                        {/if}
								{if $IS_MODULE_EDITABLE}
                                <a class="deleteRecordButton"><i title="删除" class="icon-trash alignMiddle"></i></a>
								{/if}
						{if $IS_MODULE_DELETABLE}
                                <a  href='index.php?module={$MODULE}&view=Edit&record={$LISTVIEW_ENTRY['id']}'  target="_block"><i title="{vtranslate('LBL_EDIT', $MODULE)}" class="icon-pencil alignMiddle"></i></a>&nbsp;
								{/if}
                        {if $IS_COLLATE}
                            <a href="#" class="collate"><i title="核对" class="icon-check alignMiddle"></i></a>
                        {/if}
			{if $LISTVIEW_ENTRY['closedContracts'] eq 1 && $LISTVIEW_ENTRY['modulestatus'] eq 'c_complete'}
                            <i title="关停合同" class="icon-minus-sign alignMiddle closedContracts" data-msg="{$LISTVIEW_ENTRY['contract_no']}--{$LISTVIEW_ENTRY['sc_related_to']}"></i>
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
