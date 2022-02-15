{*<!--
/*********************************************************************************

*<td>
	<b>是否符合Tsite</b>{$LINE_ITEM_DETAIL["Tsite"]}<b>是否符合Tsite新动力</b>{$LINE_ITEM_DETAIL["TsiteNew"]}</br>
</td>
********************************************************************************/
-->*}
{assign var=FINAL_DETAILS value=$RELATED_PRODUCTS.1.final_details}
<table class="table table-bordered mergeTables detailview-table ">
    <thead>
	    <th colspan="11" class="detailViewBlockHeader">退款产品明细</th>
	</thead>
	<tbody>
    <tr>
		<td><b>产品名称</b></td><td><b>所属套餐</b></td><td><b>数量</b></td><td><b>年限(月)</b></td><td><b>市场价格(￥)</b><td><b>人力成本(￥)</b></td><td><b>外采成本(￥)</b><td><b>审核人</b></td><td><b>成本明细</b><td><b>修改时间</b></td>
    </tr>
    {foreach key=INDEX item=LINE_ITEM_DETAIL from=$RELATED_PRODUCTS}
	<tr>
        <td><div class="row-fluid">{$LINE_ITEM_DETAIL["productname"]}</div></td>
        <td>{$LINE_ITEM_DETAIL["productcomboname"]}</td>
        <td>{$LINE_ITEM_DETAIL["productnumber"]}</td>
        <td>{$LINE_ITEM_DETAIL["agelife"]}</td>
        <td>{$LINE_ITEM_DETAIL["marketprice"]}</td>
        <td>{$LINE_ITEM_DETAIL["costing"]}</td>
        <td>{$LINE_ITEM_DETAIL["purchasemount"]}</td>
        <td>{$LINE_ITEM_DETAIL["checkproductuser"]}</td>
        <td>{$LINE_ITEM_DETAIL["remark"]}</td>
        <td>{$LINE_ITEM_DETAIL["createtime"]}</td>
	 </tr>
	    {/foreach}
	    <tr>
            <td colspan="5">&nbsp;</td>
            <td><span class="pull-left"><b>{$TOTALCOSTING.totalcosting} </b>&nbsp;</span>&nbsp;</td>
            <td><span class="pull-left"><b>{$TOTALCOSTING.totalpurchasemount} </b>&nbsp;</span>&nbsp;</td>
		    <td><span class="pull-right"><b>总成本(￥)</b></span></td>
		    <td colspan="2"><span class="pull-left"><b>{$TOTALCOSTING.TOTALCOST} </b>&nbsp;</span>&nbsp;</td>
	    </tr>
	    </tbody>
	</table>
<br>
<br>
<table class="table table-bordered blockContainer lineItemTable tableproduct detailview-table" id="lineItemTab">
    <thead>
    <tr>
        <th colspan="5">
            <img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;">
            <img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">
            &nbsp;&nbsp;退单原工单明细
        </th>
    </tr>
    </thead>
    <tbody>
    <tr id="insertproduct">
        <td><b>工单编号</b></td>
        <td><b>主题</b></td>
        <td><b>流程节点</b></td>
        <td><b>状态</b></td>
        <td><b>负责人</b></td>
    </tr>
        {if !empty($C_NEWPORDUCT['salesorderlist'])}
            {foreach key=row_no item=data from=$C_NEWPORDUCT['salesorderlist']}
                {if in_array($data.salesorderid,$C_OLDPRODUCT['salesoldorderid'])}
                <tr class="removetr"><td>{$data.salesorder_no}
                </td><td>{$data.subject}</td><td>{$data.workflowsnode}</td><td>{$data.modulestatus}</td><td>{$data.salesorderowner}</td>
                {if !empty($data.productlist)}
                    <tr class="removetr">
                        <td colspan="7">
                            <table class="table table-striped blockContainer lineItemTable tableproduct detailview-table"><thead><tr><th><img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="1499" style="display: none;"><img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="1499" style="display: inline;">产品明称</th><th>所属套餐</th><th>数量</th><th>年限(月)</th><th>市场价格</th><th>人力成本</th><th>外采成本</th></tr></thead><tbody>
                        {foreach item=ndata from=$data.productlist}
                            {if in_array($ndata.salesorderproductsrelid,$C_OLDPRODUCT['salesoldorderid'])}
                            <tr><td>{$ndata.productname}</td><td>{$ndata.productcomboname}</td><td>{$ndata.productnumber}</td><td>{$ndata.agelife}</td><td>{$ndata.realprice}</td><td>{$ndata.costing}</td><td>{$ndata.purchasemount}</td></tr>
                            {/if}
                        {/foreach}
                        </tbody></table></td></tr>
                {/if}
                {/if}
            {/foreach}
        {/if}

    </tbody>
</table>
<br>
<table class="table table-bordered blockContainer lineItemTable tableproduct detailview-table">
    <thead>
    <tr>
        <th colspan="8">
            <img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;">
            <img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">
            &nbsp;&nbsp;退款申请发票明细
        </th>
    </tr>
    </thead>
    <tbody>
    <tr id="insertinvoice">
        <td><b>发票代码</b></td>
        <td><b>发票号码</b></td>
        <td><b>开票日期</b></td>
        <td><b>发票金额</b></td>
        <td><b>发票内容</b></td>
        <td><b>处理状态</b></td>
        <td><b>操作处理</b></td>
        <td><b>处理日期</b></td>
    </tr>

        {if !empty($C_NEWPORDUCT['invoicelist'])}
            {foreach key=row_no item=data from=$C_NEWPORDUCT['invoicelist']}
                {if in_array($data.invoiceextendid,$C_OLDPRODUCT['invoiceoldorderid'])}
                {if $data.invoicestatus eq 'redinvoice'}
                    {assign var=INVOICESTATUS value='<span class="label btn-danger">红冲</span>'}
                {elseif $data.invoicestatus eq 'tovoid'}
                    {assign var=INVOICESTATUS value='<span class="label btn-inverse">作废</span>'}
                    {else}
                    {assign var=INVOICESTATUS value='<span class="label btn-success">正常</span>'}
                    {/if}
                <tr class="removetr"><td>{$data.invoice_noextend}</td><td>{$data.invoicecodeextend}</td><td>{$data.billingtimeextend}</td><td>{$data.totalandtaxextend}</td><td>{$data.commoditynameextend}</td><td>{$INVOICESTATUS}</td><td>{$data.operator}</td><td>{$data.operatortime}</td></tr>
                {/if}
            {/foreach}
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

        {if !empty($C_NEWPORDUCT['receivepayments'])}
            {foreach key=row_no item=data from=$C_NEWPORDUCT['receivepayments']}
                <tr class="removetr"><td>{$data.owncompany}</td><td>{$data.paytitle}</td><td>{$data.reality_date}</td><td>{$data.standardmoney}</td><td>{$data.exchangerate}</td><td>{$data.unit_price}</td><td>{$data.sumextra_price}</td></tr>
            {/foreach}
        {/if}

    </tbody>
</table>