{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
{if $HEADER_FIELD@last}
</td>
{if $IS_EDITABLE}
<a href='{$RELATED_RECORD->getEditViewUrl()}&relationOperation=true&sourceModule={$PARENT_RECORD->get('record_module')}&sourceRecord={$PARENT_RECORD->get('record_id')}'><i title="{vtranslate('LBL_EDIT', $MODULE)}" class="icon-pencil alignMiddle"></i></a>{/if}{if $IS_DELETABLE}<a class="relationDelete"><i title="{vtranslate('LBL_DELETE', $MODULE)}" class="icon-trash alignMiddle"></i></a>{/if} 
{/if}
********************************************************************************/
-->*}
{strip}
    <div class="relatedContainer">
        <input type="hidden" name="currentPageNum" value="{$PAGING->getCurrentPage()}" />
        <input type="hidden" name="relatedModuleName" class="relatedModuleName" value="{$RELATED_MODULE->get('name')}" />
        <input type="hidden" value="{$ORDER_BY}" id="orderBy">
        <input type="hidden" value="{$SORT_ORDER}" id="sortOrder">
        <input type="hidden" value="{$RELATED_ENTIRES_COUNT}" id="noOfEntries">
        <input type='hidden' value="{$PAGING->getPageLimit()}" id='pageLimit'>
        <input type='hidden' value="{$TOTAL_ENTRIES}" id='totalCount'>
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
            <button class="btn" id="relatedListPreviousPageButton" {if !$PAGING->isPrevPageExists()} disabled {/if} type="button"><span class="icon-chevron-left"></span></button>
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
            <button class="btn" id="relatedListNextPageButton" {if (!$PAGING->isNextPageExists()) or ($PAGE_COUNT eq 1)} disabled {/if} type="button"><span class="icon-chevron-right"></span></button>
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
        {if $RELATED_MODULE->get('name') eq 'Invoicesign'}
        <div class="relatedContents contents-bottomscroll">
            <div class="bottomscroll-div">
                {assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
                <table class="table table-bordered listViewEntriesTable">
                    <thead>
                    <tr class="listViewHeaders">
                        {foreach item=HEADER_FIELD from=$RELATED_HEADERS}
                            <th nowrap class="{$WIDTHTYPE}"><a href="javascript:void(0);">{vtranslate($HEADER_FIELD, $RELATION_MODULENAME)}&nbsp;&nbsp;</a></th>
                        {/foreach}
                    </tr>
                    </thead>
                    {foreach item=RELATED_RECORD from=$RELATED_RECORDS}
                        <tr class="listViewEntries" data-id='{$RELATED_RECORD->getId()}' data-recordUrl=''>

                                <td class=""  nowrap style="text-align:center;">
                                    <img src="index.php?module=Schoolrecruit&action=BasicAjax&record={$RELATED_RECORD->getId()}&mode=showQRcode">
                                </td>

                        </tr>
                    {/foreach}
                </table>
            </div>
        </div>
        {else}
<div class="relatedContents contents-bottomscroll">
    <div class="bottomscroll-div">
			{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
        <table class="table table-bordered listViewEntriesTable">
            <thead>
                <tr class="listViewHeaders">
                    {foreach item=HEADER_FIELD from=$RELATED_HEADERS}
					<!-- <th nowrap class="{$WIDTHTYPE}"><a href="javascript:void(0);">{vtranslate($HEADER_FIELD, $RELATION_MODULENAME)}&nbsp;&nbsp;</a></th> -->
                    {if $HEADER_FIELD neq 'schoolresumeid'}
                            <th nowrap class="{$WIDTHTYPE}"><a href="javascript:void(0);">{vtranslate($HEADER_FIELD, $RELATION_MODULENAME)}&nbsp;&nbsp;</a></th>
                            
                        {/if}
                    {/foreach}


					<th style="width:85px;"></th>
                </tr>
            </thead>
            {foreach item=RELATED_RECORD from=$RELATED_RECORDS}
                <tr class="listViewEntries" data-id='{$RELATED_RECORD->getId()}' data-recordUrl='{$RELATED_RECORD->getDetailViewUrl()}'>
                    {foreach item=HEADER_FIELD key=FIELDNAME from=$RELATED_HEADERS}
                        {assign var=RELATED_HEADERNAME value=$FIELDNAME}
							
                            {if $RELATED_HEADERNAME eq 'schoolresumeid'}
                                {assign var=schoolresumeid value=$RELATED_RECORD->getDisplayValue($RELATED_HEADERNAME)}
                            {else}
                            <td class="{$WIDTHTYPE}"  nowrap>
                                {$RELATED_RECORD->getDisplayValue($RELATED_HEADERNAME)}
                            </td>
                            {/if}

                    {/foreach}
					<td>
                        <div class="pull-right actions">
                            <span class="actionImages">
                                {if $smarty.get.relatedModule eq "Schoolqualifiedpeople"}
                                    <a target="_blank" href="index.php?module=Schoolresume&view=Detail&record={$schoolresumeid}"><i title="{vtranslate('LBL_SHOW_COMPLETE_DETAILS', $MODULE)}" class="icon-th-list alignMiddle"></i></a>&nbsp;
                                {else}
                                    <a target="_blank" href="{$RELATED_RECORD->getDetailViewUrl()}"><i title="{vtranslate('LBL_SHOW_COMPLETE_DETAILS', $MODULE)}" class="icon-th-list alignMiddle"></i></a>&nbsp;
                                {/if}
                                
							</span> 
                        </div>
                    </td>
                </tr>
            {/foreach}
        </table>
    </div>
</div>
    {/if}
</div>

{/strip}


<script type="text/javascript">
        
        /*var accessible_users = '<select class="chzn-select" name=""><optgroup label={vtranslate("LBL_USERS")}>{foreach key=OWNER_ID item=OWNER_NAME from=$ACCESSIBLE_USERS}<option value="{$OWNER_ID}" data-picklistvalue="{$OWNER_ID}">{$OWNER_NAME}</option>{/foreach}</optgroup></select>' ;*/

        var accessible_users = "<select id=\"ddddd\" class=\"chzn-select\" name=\"reportsower\"> <optgroup label=\"{vtranslate('LBL_USERS')}\">     {foreach key=OWNER_ID item=OWNER_NAME from=$ACCESSIBLE_USERS} <option value=\"{$OWNER_ID}\" data-picklistvalue= '{$OWNER_ID}' {if $FIELD_VALUE eq $OWNER_ID} selected {/if} data-userId=\"{$CURRENT_USER_ID}\">{$OWNER_NAME}</option> {/foreach} </optgroup>    </select>";
    </script>
    <style type="text/css">
        .select2-drop{
            z-index: 1000043;
        }
    </style>
    <input type="hidden" name="recordId" value="{$recordId}">