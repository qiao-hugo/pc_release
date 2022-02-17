
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
    <link href="libraries/icheck/blue.css" rel="stylesheet">
    <script src="libraries/icheck/icheck.min.js"></script>
    <!--产品明细表-->
    <table class="table table-bordered blockContainer lineItemTable tableproduct detailview-table" id="lineItemTab">
        <thead>
        <tr>
            <th colspan="6">
	            <img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;">
	            <img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">
	            &nbsp;&nbsp;工单明细
	        </th> 
        </tr>
        </thead>
        <tbody>
        <tr id="insertproduct">
            <td><b><label><input type="checkbox" class="salesoderall entryCheckBox">&nbsp;选定</label></b></td>
            <td><b>工单编号</b></td>
            <td><b>主题</b></td>
            <td><b>流程节点</b></td>
            <td><b>状态</b></td>
            <td><b>负责人</b></td>
        </tr>
        {if $RECORD_ID >0}
            {if !empty($C_NEWPORDUCT['salesorderlist'])}
                {foreach key=row_no item=data from=$C_NEWPORDUCT['salesorderlist']}
                    <tr class="removetr"><td>&nbsp;&nbsp;<input type="checkbox" value="{$data.salesorderid}'" name="salesorderbid[]" class="entryCheckBox salesorderchild salesorderchildn{$data.salesorderid}" data-id="{$data.salesorderid}"></td><td>{$data.salesorder_no}
                        </td><td>{$data.subject}</td><td>{$data.workflowsnode}</td><td>{$data.modulestatus}</td><td>{$data.salesorderowner}</td>
                    {if !empty($data.productlist)}
                        <tr class="removetr"><td colspan="8"><table class="table table-striped blockContainer lineItemTable tableproduct detailview-table"><thead><tr><th><img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="1499" style="display: none;"><img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="1499" style="display: inline;"></th><th>产品明称</th><th>所属套餐</th><th>数量</th><th>年限(月)</th><th>市场价格</th><th>人力成本</th><th>外采成本</th></tr></thead><tbody>
                            {foreach item=ndata from=$data.productlist}
                            <tr><td style="text-align:center;"><input type="checkbox" value="{$ndata.salesorderproductsrelid}" name="salesorderproduct[{$data.salesorderid}][]" class="entryCheckBox salesordergrandson salesordergrandson{$data.salesorderid}" data-id="{$data.salesorderid}" {if in_array($ndata.salesorderproductsrelid,$C_OLDPRODUCT['salesoldorderid'])}checked="checked" {/if}/> </td><td>{$ndata.productname}</td><td>{$ndata.productcomboname}</td><td>{$ndata.productnumber}</td><td>{$ndata.agelife}</td><td>{$ndata.realprice}</td><td>{$ndata.costing}</td><td>{$ndata.purchasemount}</td></tr>
                            {/foreach}
                            </tbody></table></td></tr>
                    {/if}
                {/foreach}
            {/if}
        {/if}
        </tbody>
    </table>
    <br>
    <table class="table table-bordered blockContainer lineItemTable tableproduct detailview-table">
        <thead>
        <tr>
            <th colspan="9">
                <img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;">
                <img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">
                &nbsp;&nbsp;发票明细
            </th>
        </tr>
        </thead>
        <tbody>
        <tr id="insertinvoice">
            <td><b><label></label><input type="checkbox" class="invoiceall entryCheckBox">&nbsp;选定</label></b></td>
            <td><b>发票代码</b></td>
            <td><b>发票号码</b></td>
            <td><b>开票日期</b></td>
            <td><b>发票金额</b></td>
            <td><b>发票内容</b></td>
            <td><b>处理状态</b></td>
            <td><b>操作处理</b></td>
            <td><b>处理日期</b></td>
        </tr>
        {if $RECORD_ID >0}
            {if !empty($C_NEWPORDUCT['invoicelist'])}
                {foreach key=row_no item=data from=$C_NEWPORDUCT['invoicelist']}
                    <tr class="removetr"><td style="text-align:center;"><input type="checkbox" value="{$data.invoiceextendid}" class="entryCheckBox invoicechild" name="invocieid[{$data.invoiceid}][]"  {if in_array($data.invoiceextendid,$C_OLDPRODUCT['invoiceoldorderid'])}checked="checked" {/if}></td><td>{$data.invoice_noextend}</td><td>{$data.invoicecodeextend}</td><td>{$data.billingtimeextend}</td><td>{$data.totalandtaxextend}</td><td>{$data.commoditynameextend}</td><td>{$data.commoditynameextend}</td><td>{$data.commoditynameextend}</td><td>{$data.commoditynameextend}</td></tr>
                {/foreach}
            {/if}
        {/if}
        </tbody>
    </table>
    <br>
    <table class="table table-bordered blockContainer lineItemTable tableproduct detailview-table">
        <thead>
        <tr>
            <th colspan="7">
                <img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;">
                <img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">
                &nbsp;&nbsp;回款明细
            </th>
        </tr>
        </thead>
        <tbody>
        <tr id="insertreceivepay">
            <td><b>公司账号</b></td>
            <td><b>汇款抬头</b></td>
            <td><b>入账日期</b></td>
            <td><b>原币金额</b></td>
            <td><b>汇率</b></td>
            <td><b>金额</b></td>
            <td><b>额外成本</b></td>
        </tr>
        {if $RECORD_ID >0}
            {if !empty($C_NEWPORDUCT['receivepayments'])}
                {foreach key=row_no item=data from=$C_NEWPORDUCT['receivepayments']}
                    <tr class="removetr"><td>{$data.owncompany}</td><td>{$data.paytitle}</td><td>{$data.reality_date}</td><td>{$data.standardmoney}</td><td>{$data.exchangerate}</td><td>{$data.unit_price}</td><td>{$data.sumextra_price}</td></tr>
                {/foreach}
            {/if}
        {/if}
        </tbody>
    </table>
    {if $RECORD_ID >0}
        <script>
            {literal}
            $('.entryCheckBox').iCheck({
                checkboxClass: 'icheckbox_minimal-blue'
            });
            {/literal}
        </script>
    {/if}
{/strip}