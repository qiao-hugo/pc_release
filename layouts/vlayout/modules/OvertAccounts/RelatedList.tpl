{*<!--
/*********
** 关联显示的模版
**取消分页信息
********/
-->*}
{strip}
    <div class="relatedContainer">
        {*<!--<input type="hidden" name="currentPageNum" value="{$PAGING->getCurrentPage()}" />
        <input type="hidden" name="relatedModuleName" class="relatedModuleName" value="{$RELATED_MODULE->get('name')}" />
        <input type="hidden" value="{$ORDER_BY}" id="orderBy">
        <input type="hidden" value="{$SORT_ORDER}" id="sortOrder">
        <input type="hidden" value="{$RELATED_ENTIRES_COUNT}" id="noOfEntries">
        <input type='hidden' value="{$PAGING->getPageLimit()}" id='pageLimit'>
        <input type='hidden' value="{$TOTAL_ENTRIES}" id='totalCount'>-->*}
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
           {*<!-- <button class="btn" id="relatedListPreviousPageButton" {if !$PAGING->isPrevPageExists()} disabled {/if} type="button"><span class="icon-chevron-left"></span></button>
            <button class="btn dropdown-toggle" type="button" id="relatedListPageJump" data-toggle="dropdown" {if $PAGE_COUNT eq 1} disabled {/if}>
                <i class="vtGlyph vticon-pageJump" title="{vtranslate('LBL_LISTVIEW_PAGE_JUMP',$moduleName)}"></i>
            </button>
            <ul class="listViewBasicAction dropdown-menu" id="relatedListPageJumpDropDown">
                <li>
                    <span class="row-fluid">
                        <span class="span3"><span class="pull-right">{vtranslate('LBL_PAGE',$moduleName)}</span></span>
                        <span class="span4">
                            <input type="text" id="pageToJump" class="listViewPagingInput" value="{$PAGING->getCurrentPage()}"/>
                        </span>
                        <span class="span2 textAlignCenter">
                            {vtranslate('LBL_OF',$moduleName)}
                        </span>
                        <span class="span2" id="totalPageCount">{$PAGE_COUNT}</span>
                    </span>
                </li>
            </ul>
            <button class="btn" id="relatedListNextPageButton" {if (!$PAGING->isNextPageExists()) or ($PAGE_COUNT eq 1)} disabled {/if} type="button"><span class="icon-chevron-right"></span></button>-->*}
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
                    {*{foreach item=HEADER_FIELD from=$RELATED_HEADERS}<th {if $HEADER_FIELD@last} colspan="2" {/if} nowrap class="{$WIDTHTYPE}">{if $HEADER_FIELD->get('column') eq 'access_count' or $HEADER_FIELD->get('column') eq 'idlists' }<a href="javascript:void(0);" class="noSorting">{vtranslate($HEADER_FIELD->get('label'), $RELATED_MODULE->get('name'))}</a>{elseif $HEADER_FIELD->get('column') eq 'time_start'}{else}<a href="javascript:void(0);" class="relatedListHeaderValues" data-nextsortorderval="{if $COLUMN_NAME eq $HEADER_FIELD->get('column')}{$NEXT_SORT_ORDER}{else}ASC{/if}" data-fieldname="{$HEADER_FIELD->get('column')}">{vtranslate($HEADER_FIELD->get('label'), $RELATED_MODULE->get('name'))}&nbsp;&nbsp;{if $COLUMN_NAME eq $HEADER_FIELD->get('column')}<img class="{$SORT_IMAGE} icon-white">{/if}</a>{/if}</th>{/foreach}*}
					{*HEADERFIELDS_LIST*}
					{foreach item=HEADER_FIELD from=$RELATED_HEADERS}
					<th nowrap class="{$WIDTHTYPE}"><a href="javascript:void(0);">{vtranslate($HEADER_FIELD, $RELATION_MODULENAME)}&nbsp;&nbsp;</a></th>
                    {/foreach}
					<th style="width:85px;"></th>
					
                </tr>
            </thead>
            {foreach item=RELATED_RECORD key=KEY from=$RELATED_RECORDS}
                <tr class="listViewEntries" data-id='{$RELATED_RECORD->getId()}' {if $RELATED_MODULE->get('name') eq 'Contacts' AND $KEY eq 0  }{elseif $RELATED_MODULE->get('name') eq 'AutoTask'}data-recordUrl='index.php?module=AutoTask&view=Detail&record={$COLUMN_DATA["autoworkflowentityid"]}&source_record={$COLUMN_DATA["autoworkflowid"]}'{else}data-recordUrl='{$RELATED_RECORD->getDetailViewUrl()}'{/if}>
				{assign var=COLUMN_FIELDNAME value=$RELATED_RECORD->getEntity()->column_fields}
                    {foreach item=HEADER_FIELD key=FIELDNAME from=$RELATED_HEADERS}
                        {assign var=RELATED_HEADERNAME value=$FIELDNAME}
							<td class="{$WIDTHTYPE}"  {if $RELATED_HEADERNAME neq 'taskremark'}nowrap{else}{/if}>
								{if isset($COLUMN_FIELDNAME[$RELATED_HEADERNAME])}
									{$RELATED_RECORD->getDisplayValue($RELATED_HEADERNAME)}
								{else}
									{assign var=COLUMN_DATA value=$RELATED_RECORD->getData()}
									{if $RELATED_MODULE->get('name') eq 'AutoTask'&& $RELATED_HEADERNAME eq 'isaction'}
                                        {if $COLUMN_DATA[$RELATED_HEADERNAME] eq '0'}
                                            <span class="label label-warning">未开始</span>
                                        {elseif $COLUMN_DATA[$RELATED_HEADERNAME] eq '1'}
                                            <span class="label label-success">进行中</span>
                                        {elseif $COLUMN_DATA[$RELATED_HEADERNAME] eq '2'}
                                            <span class="label label-important">已结束</span>
                                        {/if}
                                    {else}
                                        {$COLUMN_DATA[$RELATED_HEADERNAME]}
                                     {/if}
								{/if}
							 </td>	     
                    {/foreach}
					<td>
					{if $RELATED_MODULE->get('name') eq 'Contacts' AND $KEY eq 0 }
                    {elseif $RELATED_MODULE->get('name') eq 'AutoTask'}
                    {else}
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
</div>
{/strip}
