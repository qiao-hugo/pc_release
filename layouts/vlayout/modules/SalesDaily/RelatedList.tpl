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
    {if $RELATED_MODULE->get('name') neq 'SalesSummaryReport'}
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
                <tr class="listViewEntries" data-id='{$RELATED_RECORD->getId()}' data-recordUrl='{$RELATED_RECORD->getDetailViewUrl()}'>
                    {foreach item=HEADER_FIELD key=FIELDNAME from=$RELATED_HEADERS}
                        {assign var=RELATED_HEADERNAME value=$FIELDNAME}
							<td class="{$WIDTHTYPE}"  nowrap>
                                {$RELATED_RECORD->getDisplayValue($RELATED_HEADERNAME)}
							 </td>
       
                    {/foreach}
					<td>
                        <div class="pull-right actions">
                            <span class="actionImages">
                                <a target="_blank" href="{$RELATED_RECORD->getDetailViewUrl()}"><i title="{vtranslate('LBL_SHOW_COMPLETE_DETAILS', $MODULE)}" class="icon-th-list alignMiddle"></i></a>&nbsp;
							</span>
                        </div>
                    </td>
                </tr>
            {/foreach}
        </table>
    </div>
</div>
</div>
{else}
        <table class="table table-bordered blockContainer detailview-table" id="lineItemNotv">
            <thead>
            <tr>
                <th colspan="10" class="blockHeader">
                    <img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;">
                    <img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">
                    &nbsp;&nbsp;当月新增40%客户
                </th>
            </tr>

            </thead>
            <tbody>
            <tr>
                <td nowrap>日期</td>
                <td nowrap>{vtranslate('LBL_ACCOUNTNAME',$MODULE)}</td>
                <td nowrap>{vtranslate('LBL_Lead_Source',$MODULE)}</td>
                <td nowrap>{vtranslate('LBL_CONTACTSNAME',$MODULE)}</td>
                <td nowrap>{vtranslate('LBL_PHONE',$MODULE)}</td>
                <td nowrap>{vtranslate('LBL_TITLE',$MODULE)}</td>
                <td nowrap>{vtranslate('LBL_STARTDATE',$MODULE)}</td>
                <td nowrap>{vtranslate('LBL_Manager_return_date',$MODULE)}</td>
                <td nowrap>{vtranslate('LBL_DESCRIPTION',$MODULE)}</td>
                <td></td>
            </tr>
            {foreach item=FOURNOTV from=$DETAILLIST['foutnotv']}
                <tr>
                    <td>{$FOURNOTV['dailydatetime']}</td>
                    <td>{$FOURNOTV['accountname']}</td>
                    <td>{$FOURNOTV['leadsource']}</td>
                    <td>{$FOURNOTV['linkname']}</td>
                    <td>{$FOURNOTV['mobile']}</td>
                    <td>{$FOURNOTV['title']}</td>
                    <td nowrap>{$FOURNOTV['startdatetime']}</td>
                    <td>{$FOURNOTV['mangereturntime']}</td>
                    <td>{$FOURNOTV['mcontent']}</td>
                    <td nowrap></td>

                </tr>
            {/foreach}


            </tbody>
        </table>
        <br />

        <!--近期可成交的客户-->

        <table class="table table-bordered blockContainer detailview-table" id="lineItemCanDeal">
            <thead>
            <tr>
                <th colspan="10" class="blockHeader">
                    <img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;">
                    <img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">
                    &nbsp;&nbsp;当月可成交的客户
                </th>
            </tr>

            </thead>
            <tbody>
            <tr>
                <td nowrap>日期</td>
                <td nowrap>客户名称</td>
                <td nowrap>姓名</td>
                <td nowrap>手机</td>
                <td nowrap>职位</td>
                <td nowrap>客户情况</td>
                <td nowrap>产品</td>
                <td nowrap>报价</td>
                <td nowrap>首付款</td>
                <td nowrap>已签合同</td>
            </tr>
            {foreach item=CANDEAL from=$DETAILLIST['candeal']}
                <tr>
                    <td{$CANDEAL['datacolor']}>{$CANDEAL['dailydatetime']}</td>
                    <td{$CANDEAL['datacolor']}>{$CANDEAL['accountname']}</td>
                    <td{$CANDEAL['datacolor']}>{$CANDEAL['contactname']}</td>
                    <td{$CANDEAL['datacolor']}>{$CANDEAL['mobile']}</td>
                    <td{$CANDEAL['datacolor']}>{$CANDEAL['title']}</td>
                    <td{$CANDEAL['datacolor']}>{$CANDEAL['accountcontent']}</td>
                    <td{$CANDEAL['datacolor']}>{$CANDEAL['productname']}</td>
                    <td{$CANDEAL['datacolor']}>{$CANDEAL['quote']}</td>
                    <td{$CANDEAL['datacolor']}>{$CANDEAL['firstpayment']}</td>
                    <td{$CANDEAL['datacolor']}>{$CANDEAL['issigncontract']}</td>

                </tr>
            {/foreach}
            </tbody>
        </table>
        <br />

        <!--每日成交客户-->
        <table class="table table-bordered blockContainer detailview-table" id="lineItemDayDeal">
            <thead>
            <tr>
                <th colspan="16" class="blockHeader">
                    <img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;">
                    <img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">
                    &nbsp;&nbsp;当月收款客户&nbsp;&nbsp;当月有效业绩&nbsp;<span class="label label-b_actioning">{$DETAILLIST['daydealarrivalamount']}</span>
                </th>
            </tr>


            </thead>
            <tbody>
            <tr>
                <td nowrap>日期</td>
                <td nowrap>客户名称</td>
                <td nowrap>成交业务</td>
                <td nowrap>市场价</td>
                <td nowrap>成交金额</td>
                <td nowrap>是否全款</td>
                <td nowrap>到款性质</td>
                <td nowrap>收款</td>
                <td nowrap>拜访次数</td>
                <td nowrap>老客户</td>
                <td nowrap>行业</td>
                <td nowrap>拜访对象</td>
                <td nowrap>有陪访</td>
                <td nowrap>陪访者</td>
                <td nowrap>折扣</td>
                <td nowrap>到账业绩</td>
            </tr>
            {foreach item=DAYDEAL from=$DETAILLIST['daydeal']}
                <tr>
                    <td>{$DAYDEAL['dailydatetime']}</td>
                    <td>{$DAYDEAL['accountname']}</td>
                    <td>{$DAYDEAL['productname']}</td>
                    <td>{$DAYDEAL['marketprice']}</td>
                    <td>{$DAYDEAL['dealamount']}</td>
                    <td>{$DAYDEAL['allamount']}</td>
                    <td>{$DAYDEAL['paymentnature']}</td>
                    <td>{$DAYDEAL['firstpayment']}</td>
                    <td>{$DAYDEAL['visitingordercount']}</td>
                    <td>{$DAYDEAL['oldcustomers']}</td>
                    <td>{$DAYDEAL['industry']}</td>
                    <td>{$DAYDEAL['visitingobj']}</td>
                    <td>{$DAYDEAL['isvisitor']}</td>
                    <td>{$DAYDEAL['withvisitor']}</td>
                    <td nowrap>{$DAYDEAL['discount']}</td>
                    <td>{$DAYDEAL['arrivalamount']}</td>

                </tr>
            {/foreach}


            </tbody>
        </table>
        <br />

        <!--次日拜访情况-->
        <table class="table table-bordered blockContainer detailview-table" id="lineItemNextDayVisit">
            <thead>
            <tr>
                <th colspan="9" class="blockHeader">
                    <img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;">
                    <img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">
                    &nbsp;&nbsp;当月拜访情况
                </th>
            </tr>


            </thead>
            <tbody>
            <tr>
                <td nowrap>日期</td>
                <td nowrap>客户名称</td>
                <td nowrap>姓名</td>
                <td nowrap>是否老板</td>
                <td nowrap>第几次拜访</td>
                <td nowrap>拜访说明</td>
                <td nowrap>有陪访</td>
                <td nowrap>陪访者</td>
            </tr>
            {foreach item=NEXTDAYVISIT from=$DETAILLIST['nextdayvisit']}
                <tr>
                    <td>{$NEXTDAYVISIT['dailydatetime']}</td>
                    <td>{$NEXTDAYVISIT['accountname']}</td>
                    <td>{$NEXTDAYVISIT['contacts']}</td>
                    <td>{$NEXTDAYVISIT['title']}</td>
                    <td>{$NEXTDAYVISIT['visitingordernum']}</td>
                    <td>{$NEXTDAYVISIT['purpose']}</td>
                    <td>{$NEXTDAYVISIT['isvisitor']}</td>
                    <td nowrap>{$NEXTDAYVISIT['withvisitor']}</td>

                </tr>
            {/foreach}


            </tbody>
        </table>
{/if}
{/strip}
