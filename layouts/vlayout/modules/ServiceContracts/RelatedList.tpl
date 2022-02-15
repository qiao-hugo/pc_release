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
    {if $RELATION_MODULENAME eq 'Files'}
        {if !$collate_fit}
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
        {/if}
    {else}
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
    {/if}
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
                            {foreach item=HEADER_FIELD key=FIELDNAME from=$RELATED_HEADERS}
                                {assign var=RELATED_HEADERNAME value=$FIELDNAME}
                                <td class="{$WIDTHTYPE}"  nowrap style="text-align:center;">
                                    <img src="{$RELATED_RECORD->getDisplayValue($RELATED_HEADERNAME)}" width="900">
                                </td>

                            {/foreach}
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
					<th nowrap class="{$WIDTHTYPE}"><a href="javascript:void(0);">{vtranslate($HEADER_FIELD, $RELATION_MODULENAME)}&nbsp;&nbsp;</a></th>
                    {/foreach}
					<th style="width:85px;"></th>
                </tr>
            </thead>
            {foreach item=RELATED_RECORD from=$RELATED_RECORDS}
                <tr class="{if $RELATION_MODULENAME neq 'TyunStationSale'}listViewEntries{/if}" data-id='{$RELATED_RECORD->getId()}' data-recordUrl='{$RELATED_RECORD->getDetailViewUrl()}'>
                    {foreach item=HEADER_FIELD key=FIELDNAME from=$RELATED_HEADERS}
                        {assign var=RELATED_HEADERNAME value=$FIELDNAME}
                            {if $IS_FILES eq '1'}
                                {if $RELATED_HEADERNAME eq 'filestate'}
                                    {assign var=FILESTATE value=$RELATED_RECORD->getDisplayValue($RELATED_HEADERNAME)}
                                {/if}
                                {if $RELATED_HEADERNAME eq 'uploader'}
                                    {assign var=UPLOADER value=$RELATED_RECORD->getDisplayValue($RELATED_HEADERNAME)}
                                {/if}


                                {if $RELATED_HEADERNAME eq 'name'}
                                    <td class="{$WIDTHTYPE}  {$RELATED_HEADERNAME}"  nowrap>
                                        {if $IS_ROLEID OR (!$IS_ROLEID AND !in_array($MODULESTATUS,array('c_complete','c_cancel')))}
                                            {assign var=EXTENSION value='.'|explode:$RELATED_RECORD->getDisplayValue($RELATED_HEADERNAME)|end|lower}
                                            {if in_array($RELATED_RECORD->getDisplayValue('style'),array('合同D','Vmate附件'))&& in_array($EXTENSION,array('pdf'))}
                                                {if $RELATED_RECORD->getDisplayValue('style') eq 'Vmate附件'}
                                                    {if $V_VIEW eq 1}
                                                        <a title="点击预览" class="btn-link" href="pdfread.php?fileid={$RELATED_RECORD->getId()|base64_encode}&type=vmate" target="_blank">{$RELATED_RECORD->getDisplayValue($RELATED_HEADERNAME)}</a>
                                                    {else}
                                                        {$RELATED_RECORD->getDisplayValue($RELATED_HEADERNAME)}
                                                    {/if}
                                                {else}
                                                    <a title="点击预览" class="btn-link" href="pdfread.php?fileid={$RELATED_RECORD->getId()|base64_encode}" target="_blank">{$RELATED_RECORD->getDisplayValue($RELATED_HEADERNAME)}</a>
                                                {/if}
                                            {else}
                                                <a title="点击下载" class="btn-link" href="index.php?module=ServiceContracts&action=DownloadFile&filename={$RELATED_RECORD->getId()|base64_encode}">{$RELATED_RECORD->getDisplayValue($RELATED_HEADERNAME)}</a>
                                            {/if}
                                        {else}
                                            {$RELATED_RECORD->getDisplayValue($RELATED_HEADERNAME)}
                                        {/if}
                                    </td>
                                {else}
                                    <td class="{$WIDTHTYPE}  {$RELATED_HEADERNAME}"  nowrap>
                                        {$RELATED_RECORD->getDisplayValue($RELATED_HEADERNAME)}
                                    </td>
                                {/if}
                            {else}
                                <td class="{$WIDTHTYPE}  {$RELATED_HEADERNAME}"  nowrap>
                                    {if $RELATED_HEADERNAME eq 'newservicecontractsno'}
                                        <a target="_blank" href="{$RELATED_RECORD->getDetailViewUrl()}" title="补充协议">{$RELATED_RECORD->getDisplayValue($RELATED_HEADERNAME)}</a>
                                    {else}
                                        {$RELATED_RECORD->getDisplayValue($RELATED_HEADERNAME)}
                                     {/if}
                                 </td>
                            {/if}


                    {/foreach}
					<td>
                        <div class="pull-right actions">
                            <span class="actionImages">
                                {if $RELATION_MODULENAME eq 'Files'}
                                    {if !$collate_fit}
                                        {if $FILESTATE eq '草稿'}
                                            {if $IS_FILESDELIVERT eq '1'}
                                                <a  data-id="{$RELATED_RECORD->getId()}" class="files_deliver" href="javascript:void(0);"><i title="签收" class="icon-share alignMiddle"></i></a>&nbsp;
                                            {/if}
                                            {if $UPLOADER eq $LAST_NAME}
                                                <a  data-id="{$RELATED_RECORD->getId()}" class="files_delete" href="javascript:void(0);"><i title="删除" class="icon-trash alignMiddle"></i></a>&nbsp;
                                            {/if}
                                        {else}
                                            {if $IS_CONTRACTSFILESDELETE eq '1'}
                                                <a  data-id="{$RELATED_RECORD->getId()}" class="files_delete" href="javascript:void(0);"><i title="删除" class="icon-trash alignMiddle"></i></a>
                                            {/if}
                                        {/if}
                                    {/if}
                                {/if}
                                {if $CAN_DOWNLOAD eq 1 && $RELATED_RECORD->getDisplayValue('style') eq '合同D'}
                                    <a   class="btn-link" href="index.php?module=ServiceContracts&action=DownloadFile&filename={$RELATED_RECORD->getId()|base64_encode}"><i title="合同下载" class="icon-download alignMiddle"></i></a>
                                {/if}

                                {if $RELATED_RECORD->getDisplayValue('style') eq 'Vmate附件' && $V_CAN_DOWNLOAD eq 1}
                                    <a   class="btn-link" href="index.php?module=Vmatefiles&action=DownloadFile&record={$RELATED_RECORD->getId()}&type=1"><i title="合同下载" class="icon-download alignMiddle"></i></a>
                                {/if}

                                {*
                                <a target="_blank" href="{$RELATED_RECORD->getDetailViewUrl()}"><i title="详情" class="icon-th-list alignMiddle"></i></a>&nbsp;
                                {/if}
                                *}
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
{literal}
<script type="text/javascript">
    $('.files_delete').click(function() {
        var $t_tr = $(this).closest('tr');

        var listInstance = Vtiger_Detail_Js.getInstance();
        var message = '确定要删除附件？';
        var srecorId=$('#recordId').val();
        var recordId = $(this).attr('data-id');
        Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
            function(e) {
                var module = app.getModuleName();
                var postData = {
                    "module": module,
                    "action": "BasicAjax",
                    "record": recordId,
                    "srecorId": srecorId,
                    "srcModule":$("input[name='relatedModuleName']").val(),
                    "mode": 'files_delete'
                }


                var Message = app.vtranslate('JS_RECORD_GETTING_');

                var progressIndicatorElement = jQuery.progressIndicator({
                        'message' : Message,
                        'position' : 'html',
                        'blockInfo' : {'enabled' : true}
                        });
                AppConnector.request(postData).then(
                    function(data){
                        progressIndicatorElement.progressIndicator({
                                    'mode' : 'hide'
                                });
                        if(data.success == true) {
                            $t_tr.remove();
                        }
                    },
                    function(error,err){

                    }
                );
            },
            function(error, err){
            }
        );
    });

    $('.files_deliver').click(function() {
        var $t_tr = $(this).closest('tr');

        var t_filestate = $t_tr.find('.filestate').html();
        if (t_filestate.indexOf('已签收') >= 0) {
            var params = {
                title: app.vtranslate('提醒'),
                text: app.vtranslate('附件已签收'),
                width: '35%'
            };
            Vtiger_Helper_Js.showPnotify(params);
            return false;
        }

        var listInstance = Vtiger_Detail_Js.getInstance();
        var message = '确定签收附件？';

        var recordId = $(this).attr('data-id');
        Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
            function(e) {
                var module = app.getModuleName();
                var postData = {
                    "module": module,
                    "action": "BasicAjax",
                    "record": recordId,
                    "srcModule":$("input[name='relatedModuleName']").val(),
                    "mode": 'files_deliver'
                }


                var Message = app.vtranslate('JS_RECORD_GETTING_');

                var progressIndicatorElement = jQuery.progressIndicator({
                        'message' : Message,
                        'position' : 'html',
                        'blockInfo' : {'enabled' : true}
                        });
                AppConnector.request(postData).then(
                    function(data){
                        progressIndicatorElement.progressIndicator({
                                    'mode' : 'hide'
                                });
                        if(data.success == true) {
                            $t_tr.find('.filestate').html('已签收');
                            $t_tr.find('.deliversuserid').html(data.result.last_name);
                            $t_tr.find('.delivertime').html(data.result.delivertime);

                        }
                    },
                    function(error,err){

                    }
                );
            },
            function(error, err){
            }
        );
    });
</script>
{/literal}