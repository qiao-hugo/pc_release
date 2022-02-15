{*<!--
/*********
** 关联显示的模版
**取消分页信息
********/
-->*}
{strip}
    <div class="relatedContainer">
        <div class="relatedHeader ">
            <div class="btn-toolbar row-fluid">
                <div class="span8">

                    {foreach item=RELATED_LINK from=$RELATED_LIST_LINKS['LISTVIEWBASIC']}
                        <div class="btn-group">
                            {assign var=IS_SELECT_BUTTON value={$RELATED_LINK->get('_selectRelation')}}
                            <button type="button" class="btn addButton
                            {if $IS_SELECT_BUTTON eq true} selectRelation {/if} "
                        {if $IS_SELECT_BUTTON eq true} data-moduleName={$RELATED_LINK->get('_module')->get('name')} {/if}
                        {if ($RELATED_LINK->isPageLoadLink())}
                        {if $RELATION_FIELD} data-name="{$RELATION_FIELD->getName()}" {/if}
                        data-url="{$RELATED_LINK->getUrl()}"
                    {/if}
            {if $IS_SELECT_BUTTON neq true}name="addButton"{/if}>{if $IS_SELECT_BUTTON eq false}<i class="icon-plus icon-white"></i>{/if}&nbsp;<strong>{$RELATED_LINK->getLabel()}</strong></button>
    </div>
{/foreach}
&nbsp;
</div>
<div class="span4">
    <span class="row-fluid">
        <span class="span7 pushDown">
            <span class="pull-right pageNumbers alignTop" data-placement="bottom" data-original-title="" style="margin-top: -5px">
            {if !empty($RELATED_RECORDS)} {$PAGING->getRecordStartRange()} {vtranslate('LBL_to', $RELATED_MODULE->get('name'))} {$PAGING->getRecordEndRange()}{/if}
        </span>
    </span>
    <span class="span5 pull-right">
        <span class="btn-group pull-right">
        </span>
    </span>
</span>
</div>
</div>
</div>
<div class="contents-topscroll">
    <div class="topscroll-div">
        &nbsp;
    </div>
</div>
<div class="relatedContents contents-bottomscroll">
    <div class="bottomscroll-div">
			{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
        <table class="table table-bordered listViewEntriesTable">
            <thead>
                <tr class="listViewHeaders">
					{foreach item=HEADER_FIELD from=$RELATED_HEADERS}
					<th nowrap class="{$WIDTHTYPE}"><a href="javascript:void(0);">{vtranslate($HEADER_FIELD, $RELATION_MODULENAME)}&nbsp;&nbsp;</a></th>
                    {/foreach}
					<th style="width:85px;"></th>

                </tr>
            </thead>
            {foreach item=RELATED_RECORD key=KEY from=$RELATED_RECORDS}
                <tr class="listViewEntries" data-id='{$RELATED_RECORD->getId()}'
                    data-recordUrl='{$RELATED_RECORD->getDetailViewUrl()}'>
				{assign var=COLUMN_FIELDNAME value=$RELATED_RECORD->getEntity()->column_fields}
                    {foreach item=HEADER_FIELD key=FIELDNAME from=$RELATED_HEADERS}
                        {assign var=RELATED_HEADERNAME value=$FIELDNAME}
							<td class="{$WIDTHTYPE}"  nowrap>
								{if isset($COLUMN_FIELDNAME[$RELATED_HEADERNAME])}
									{$RELATED_RECORD->getDisplayValue($RELATED_HEADERNAME)}
								{else}
									{assign var=COLUMN_DATA value=$RELATED_RECORD->getData()}
									{$COLUMN_DATA[$RELATED_HEADERNAME]}
								{/if}
							 </td>
                    {/foreach}
					<td>
					{if $RELATED_MODULE->get('name') eq 'Contacts' AND $KEY eq 0 }{else}
                        <div class="pull-right actions">
                            <span class="actionImages">
                                <a target="_blank" href="{$RELATED_RECORD->getDetailViewUrl()}"><i title="{vtranslate('LBL_SHOW_COMPLETE_DETAILS', $MODULE)}" class="icon-th-list alignMiddle"></i></a>&nbsp;
							</span>
                        </div>
					{/if}
                    </td>
                </tr>
            {/foreach}
        </table>
    </div>
</div>
        {if $RELATION_MODULENAME eq 'ReceivedPayments'}
        <div >
            <div class="widget_contents">
                <table class="table table-bordered table-striped blockContainer  showInlineTable ">
                    <tr><thead><th class="blockHeader" colspan="10"><span >回款使用明细</span></th></thead></tr>
                    <tr>
                        <tbody>
                        <td style="width: 15%"><b>操作模块</b></td>
                        <td style="width: 8%"><b>操作者</b></td>
                        <td style="width: 16%"><b>汇款抬头</b></td>
                        <td style="width: 5%"><b>入账金额</b></td>
                        <td style="width: 8%"><b>入账日期</b></td>
                        <td style="width: 8%"><b>操作时间</b></td>
                        <td style="width: 15%"><b>回款使用情况</b></td>
                        <td style="width: 15%"><b>充值平台</b></td>
                        <td style="width: 10%"><b>备注</b></td>
                        <tr>
                            {assign var=RECEIVED_PAYMENTS_USE_DETAIL value=$PARENT_RECORD->getReceivedPaymentsUseDetail($PARENT_RECORD->getID())}
                            {if !empty($RECEIVED_PAYMENTS_USE_DETAIL)}
                            {foreach key=BLOCK_LABEL  item=BLOCK_FIELDS from=$RECEIVED_PAYMENTS_USE_DETAIL name="EditViewBlockLevelLoop"}
                        <tr>
                            <td>
                                {if $BLOCK_FIELDS['type'] eq '1'}
                                    工单：{$BLOCK_FIELDS['recordno']}
                                {else}
                                    充值单：{$BLOCK_FIELDS['recordno']}
                                {/if}
                            </td>
                            <td>{$BLOCK_FIELDS['last_name']}</td>
                            <td><a href="index.php?module=ReceivedPayments&view=Detail&record={$BLOCK_FIELDS['receivedpaymentsid']}" target="_blank">{$BLOCK_FIELDS['paytitle']}</a></td>
                            <td>{$BLOCK_FIELDS['unit_price']}</td>
                            <td>{$BLOCK_FIELDS['reality_date']}</td>
                            <td>{$BLOCK_FIELDS['matchdate']}</td>
                            <td>{$BLOCK_FIELDS['detail']}</td>
                            <td>{$BLOCK_FIELDS['productname']}</td>
                            <td>{$BLOCK_FIELDS['remarks']}</td>
                        </tr>
                        {/foreach}
                        {else}
                        <tr>
                            <td colspan="5" style="text-align: center">此回款还未被使用过</td>
                        </tr>
                        {/if}
                        </tbody>
                </table>
            </div>
        </div>
        {/if}
</div>
{/strip}