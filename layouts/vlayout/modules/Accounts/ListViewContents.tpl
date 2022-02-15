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
	
{assign var =IS_PROTECTED value=$MODULE_MODEL->isprotected() }


<div class="listViewEntriesDiv contents-bottomscroll" style="overflow:auto;">
	<div class="bottomscroll-div" >
	<input type="hidden" value="{$ORDER_BY}" id="orderBy">
	<input type="hidden" value="{$SORT_ORDER}" id="sortOrder">

	{assign var=WIDTHTYPE value=$CURRENT_USER_MODEL->get('rowheight')}
	<table class="table listViewEntriesTable" id="listViewContentTable" >
		<thead>
			<tr class="listViewHeaders">
                {if $smarty.get.filter eq 'overt'}
                <th nowrap>
                    <div  class="noclick" style="width: 100%;height:100%;"><button type="button" class="btn btn-success checkedall">全选</button><button type="button" class="btn btn-inverse checkedinverse">反选</button><button type="button" class="btn btn-primary stampall">领用临时区</button></div>
                </th>
                {/if}
                {if $smarty.get.filter eq 'temporary'}
                    <th nowrap>
                        <div  class="noclick" style="width: 100%;height:100%;"><button type="button" class="btn btn-success over_checkedall">全选</button><button type="button" class="btn btn-inverse over_checkedinverse">反选</button><button type="button" class="btn btn-primary over_stampall">放入公海</button></div>
                    </th>
                {/if}
				{foreach key=KEY item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
				<th nowrap data-field="{$LISTVIEW_HEADER['columnname']}" class="listViewEntries">
					<img src="layouts/vlayout/skins/images/sort_all.png">{vtranslate($KEY, $MODULE)}
				</th>
				{/foreach}
                <th id="fixTh">操作</th>
			</tr>
		</thead>
		{foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=listview}
		<tr class="listViewEntries"  data-id='{$LISTVIEW_ENTRY['id']}' data-name="{$LISTVIEW_ENTRY['accountname']}" data-recordUrl='index.php?module={$MODULE}&view=Detail&record={$LISTVIEW_ENTRY['id']}' id="{$MODULE}_listView_row_{$smarty.foreach.listview.index+1}">
            {if $smarty.get.filter eq 'overt'}
                <td style="text-align: left;">
                    <div class="deletedflag">
                        <label style="height:100%;display: inline-block;">
                            <input type="checkbox" value="{$LISTVIEW_ENTRY['id']}" class="entryCheckBox" name="Detailrecord[]" ></label>
                        <button type="button" data-id="{$LISTVIEW_ENTRY['id']}" style="visibility:hidden;" class="btn btn stamp">领用</button>
                        <button type="button" data-id="{$LISTVIEW_ENTRY['id']}" style="display:inline" class="btn btn stamp">领用临时区</button>
                    </div>
                </td>
            {/if}
            {if $smarty.get.filter eq 'temporary'}
                <td style="text-align: left;">
                    <div class="deletedflag">
                        <label style="height:100%;display: inline-block;">
                            <input type="checkbox" value="{$LISTVIEW_ENTRY['id']}" class="entryCheckBoxOver" name="DetailrecordOver[]" ></label>
                        <button type="button" data-id="{$LISTVIEW_ENTRY['id']}" style="visibility:hidden;" class="btn btn stamp"></button>
                        <button type="button" data-id="{$LISTVIEW_ENTRY['id']}" style="display:inline" class="btn btn stamp_overt">放入公海</button>
                    </div>
                </td>
            {/if}




				{foreach key=fkey item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS name=fieldview}
					<td class="listViewEntryValue  {if $LISTVIEW_HEADER['columnname'] eq 'advancesmoney' }advancesmoney_value{/if} "  nowrap>
                        {if $LISTVIEW_HEADER['columnname'] eq 'accountname'}
                            <a class="btn-link" href='index.php?module={$MODULE}&view=Detail&record={$LISTVIEW_ENTRY['id']}' target="_block">{vtranslate( $LISTVIEW_ENTRY[$LISTVIEW_HEADER['columnname']],$MODULE)}</a>
                        {else}
                        
                            {uitypeformat($LISTVIEW_HEADER['uitype'],$LISTVIEW_ENTRY[$LISTVIEW_HEADER['columnname']],$MODULE)}
                        {/if}
                    </td>
			    {/foreach}

            <td class="listViewEntryValue" >
                {if $LISTVIEW_HEADER@last}
                    <div  style="width:120px">
                        <a  href="index.php?module={$MODULE}&view=Detail&record={$LISTVIEW_ENTRY['id']}"><i title="详细信息" class="icon-th-list alignMiddle"></i></a>&nbsp;

                        {if $IS_ADVANCEMONY }
                        <a class="setAdvancesmoney" data-status="{$LISTVIEW_ENTRY['advancesmoney']}"> <i title="{vtranslate('修改垫款', $MODULE)}" class="icon-move alignMiddle"></i></a>
                        {/if}
                        {if $LISTVIEW_ENTRY['protected'] eq '否' }
                            {if $LISTVIEW_ENTRY['accountcategory'] eq 0}
                                <a class="deleteRecordButton"><i title="删除" class="icon-trash alignMiddle"></i></a>
                                <a  href='index.php?module={$MODULE}&view=Edit&record={$LISTVIEW_ENTRY['id']}'  target="_block"><i title="{vtranslate('LBL_EDIT', $MODULE)}" class="icon-pencil alignMiddle"></i></a>&nbsp;
                                {if $LISTVIEW_ENTRY['isown'] eq 1}
                                    <a class="ChangeRecordButton" id="PROTECTED"> <i title="{vtranslate('LBL_TOPROTECT', $MODULE)}" class="icon-share alignMiddle"></i></a>
                                {/if}

                                {*{if $ISCHANNELDEPART eq '1'}*}
                                <a class="ChangeRecordButton" id="OVERT"> <i title="{vtranslate('LBL_TOOVERT', $MODULE)}" class="icon-shopping-cart alignMiddle"></i></a>
                                {*{/if}*}

                                {*打标记*}
                                {*
                                {assign var=TEMPASS value=['chan_notv','forp_notv','eigp_notv','sixp_notv']}
                                {if in_array($LISTVIEW_ENTRY['accountrank'],$TEMPASS) && $LISTVIEW_ENTRY['smownerid_owner'] eq $CURRENT_USER_MODEL->get('id')}
                                    {if $LISTVIEW_ENTRY['sign'] eq '否' }
                                        <a class="ChangeRecordButton" id="SIGN"> <i title="未标记" class="icon-star-empty alignMiddle"></i></a>
                                    {else}
                                        <a class="ChangeRecordButton" id="NOSIGN"> <i title="已标记" class="icon-star alignMiddle"></i></a>
                                    {/if}
                                {/if}
                                *}
                                {*打标记*}

                            {elseif $LISTVIEW_ENTRY['accountcategory'] eq 1 }
                                {if $IS_MODULE_EDITABLE}<a  href='index.php?module={$MODULE}&view=Edit&record={$LISTVIEW_ENTRY['id']}'  target="_block"><i title="{vtranslate('LBL_EDIT', $MODULE)}" class="icon-pencil alignMiddle"></i></a>&nbsp;{/if}

                                {*{if $ISCHANNELDEPART eq '1'}
                                <a class="ChangeRecordButton" id="OVERT"> <i title="{vtranslate('LBL_TOOVERT', $MODULE)}" class="icon-shopping-cart alignMiddle"></i></a>
                                {/if}*}

                                <a class="ChangeRecordButton" id="SELF"> <i title="{vtranslate('LBL_TOSELF', $MODULE)}" class="icon-inbox alignMiddle"></i></a>
                            {elseif $LISTVIEW_ENTRY['accountcategory'] eq 2}
                                <a class="ChangeRecordButton" id="TEMPORARY"> <i title="{vtranslate('LBL_TOTEMPORARY', $MODULE)}" class="icon-flag alignMiddle"></i></a>
                                <a class="ChangeRecordButton" id="SELF"> <i title="{vtranslate('LBL_TOSELF', $MODULE)}" class="icon-inbox alignMiddle"></i></a>
                            {/if}
                        {elseif $LISTVIEW_ENTRY['isown'] eq 1}
                            <a class="ChangeRecordButton" id="UNPROTECTED"> <i title="{vtranslate('LBL_PROTECTED', $MODULE)}" class="icon-lock alignMiddle"></i></a>
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
    {if $smarty.get.filter eq 'overt'}
    <script type="text/javascript">
        $(function(){
            $('.entryCheckBox').iCheck({
                checkboxClass: 'icheckbox_minimal-orange'
            });
        });

    </script>
    {/if}
    {if $smarty.get.filter eq 'temporary'}
        <script type="text/javascript">
            $(function(){
                $('.entryCheckBoxOver').iCheck({
                    checkboxClass: 'icheckbox_minimal-orange'
                });
            });

        </script>
    {/if}
{/strip}
