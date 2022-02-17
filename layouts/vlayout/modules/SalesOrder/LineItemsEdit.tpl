
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
    <!--
    All final details are stored in the first element in the array with the index name as final_details
    so we will get that array, parse that array and fill the details
    -->
    {assign var="FINAL" value=$RELATED_PRODUCTS.1.final_details}
    <div class="widgetContainer_1" data-url="module=Products&amp;view=Detail&amp;mode=getWorkflowsContent&amp;record=" data-name="Workflows">
    <div class="widget_contents">
	</div>
	</div>
	<div class="row-fluid verticalBottomSpacing  hide tableproduct" id="beforeproductdetail">
        <!--<div>
           	<div class="btn-toolbar">
                <span class="btn-group">
                    <button type="button" class="btn addButton" id="addProduct">
                       <i class="icon-plus icon-white"></i><strong>{vtranslate('LBL_ADD_PRODUCT',$MODULE)}</strong>
                    </button>
                </span>
            </div>
        </div>-->
    </div>
    <!--产品明细表-->
    <table class="table table-bordered blockContainer lineItemTable tableproduct detailview-table" id="lineItemTab">
        <thead>
        <tr>
            <th colspan="5">
	            <img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;">
	            <img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">
	            &nbsp;&nbsp;{vtranslate('LBL_ITEM_DETAILS', $MODULE)}
	        </th> 
        </tr>
        </thead>
        <tbody>
        <tr id="insertproduct">
            <td><b>{*vtranslate('LBL_TOOLS',$MODULE)*}产品编号</b></td>
            <td><span class="redColor">*</span><b>{vtranslate('LBL_ITEM_NAME',$MODULE)}</b></td>
            <td><b>所属套餐</b></td>
            <td colspan="2" style="witdh:800px;"><b>{vtranslate('LBL_PRODUCTS_DATA',$MODULE)}</b></td>
        </tr>
        
        <tr id="row0" class="hide lineItemCloneCopy">
            {include file="LineItemsContent.tpl"|@vtemplate_path:'SalesOrder' row_no=0 data=[]}
        </tr>
        {foreach key=row_no item=data from=$RELATED_PRODUCTS}
            <tr class="lineItemRow">
                {include file="LineItemsContent.tpl"|@vtemplate_path:'SalesOrder' row_no=$row_no data=$data}
            </tr>
        {/foreach}
        </tbody>
    </table>
	<div class="row-fluid verticalBottomSpacing" id="additionfenc">
	</div>
	<div class="row-fluid verticalBottomSpacing" id="additionhuik">
	</div>
    <div class="row-fluid verticalBottomSpacing  hide tableproduct">
        <!--<div>
                <div class="btn-toolbar">
                    <span class="btn-group">
                        <button type="button" class="btn addButton" id="addProduct">
                            <i class="icon-plus icon-white"></i><strong>{vtranslate('LBL_ADD_PRODUCT',$MODULE)}</strong>
                        </button>
                    </span>
                </div>
        </div>-->
    </div>
     <!--2015-1-23 wangbin-->
    <!--<table class="table table-bordered blockContainer lineItemTable hide tableproduct detailview-table" id="lineItemResult">       
        <tr>
            <td  width="83%">
                <div class="pull-right"><strong>{vtranslate('LBL_ITEMS_TOTAL',$MODULE)}</strong></div>
            </td>
            <td>
                <div id="netTotal" class="pull-right netTotal">{if !empty($FINAL.hdnSubTotal)}{$FINAL.hdnSubTotal}{else}0.00{/if}</div>
            </td>
        </tr>
    </table>-->
    <input type="hidden" name="totalProductCount" id="totalProductCount" value="{$row_no}" />
    <input type="hidden" name="subtotal" id="subtotal" value="" />
    <input type="hidden" name="total" id="total" value="" />
{/strip}