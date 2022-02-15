{*<!--
/*********************************************************************************

*<td>
	<b>是否符合Tsite</b>{$LINE_ITEM_DETAIL["Tsite"]}<b>是否符合Tsite新动力</b>{$LINE_ITEM_DETAIL["TsiteNew"]}</br>
</td>
********************************************************************************/
-->*}
{assign var=FINAL_DETAILS value=$RELATED_PRODUCTS.1.final_details}
<input type=hidden id="hid_totalcost" value='{$TOTALCOSTING.TOTALCOST}' />
<input type=hidden id="hid_laborcost" value='{$TOTALCOSTING.totalcosting}' />
<input type=hidden id="hid_purchasecost" value='{$TOTALCOSTING.totalpurchasemount}' />
<table class="table table-bordered mergeTables detailview-table ">
    <thead>
	    <th colspan="14" class="detailViewBlockHeader">工单产品明细</th>
	</thead>
	<tbody>
    <tr>
		<td><b>产品名称</b></td><td><b>所属套餐</b></td><td><b>供应商</b></td><td><b>采购合同</b></td><td><b>数量</b></td><td><b>年限(月)</b></td><td><b>市场价格(￥)</b><td><b>人力成本(￥)</b></td><td><b>外采成本(￥)</b></td><td><b>额外成本(￥)</b><td><b>审核人</b></td><td><b>成本明细</b><td><b>修改时间</b><td><b>产品资料</b></td>
    </tr>
    {foreach key=INDEX item=LINE_ITEM_DETAIL from=$RELATED_PRODUCTS}
	<tr>
		    <td><div class="row-fluid">{$LINE_ITEM_DETAIL["productname"]}</div></td>
	    	<td>{$LINE_ITEM_DETAIL["productcomboname"]}</td>
	    	<td>{$LINE_ITEM_DETAIL["vendorname"]}</td>
	    	<td>{$LINE_ITEM_DETAIL["supplier_contract_no"]}</td>
            <td>{$LINE_ITEM_DETAIL["productnumber"]}</td>
	    	<td>{$LINE_ITEM_DETAIL["agelife"]}</td>
		    <td>{$LINE_ITEM_DETAIL["marketprice"]}</td>
		    <td>{$LINE_ITEM_DETAIL["costing"]}</td>
		    <td>{$LINE_ITEM_DETAIL["purchasemount"]}</td>
			<td>{$LINE_ITEM_DETAIL["extracost"]}</td>
			<td>{$LINE_ITEM_DETAIL["checkproductuser"]}</td>
			<td>{$LINE_ITEM_DETAIL["remark"]}</td>
			<td>{$LINE_ITEM_DETAIL["createtime"]}</td>
			
			<td><textarea style="" id=note{$INDEX} readonly="readonly" class="productnote" >{$LINE_ITEM_DETAIL["notes"]}</textarea>
			<a class="btn-link editproduct" title="编辑产品信息" data-id="{$LINE_ITEM_DETAIL["productid"]}"><i  class="icon-edit"></i>编辑</a>
			<a class="btn-link showproduct" data-id="note{$INDEX}" ><i title="查看详情"  class="icon-file"></i>详情</a>
			</td>
	 </tr>
	


	    {/foreach}
	    
	    <tr>
            <td colspan="7">&nbsp;</td>
            <td><span class="pull-left"><b>{$TOTALCOSTING.totalcosting} </b>&nbsp;</span>&nbsp;</td>
            <td><span class="pull-left"><b>{$TOTALCOSTING.totalpurchasemount} </b>&nbsp;</span>&nbsp;</td>
            <td><span class="pull-left"><b>{$TOTALCOSTING.extracost} </b>&nbsp;</span>&nbsp;</td>
		    <td><span class="pull-right"><b>总成本(￥)</b></span></td>
		    <td colspan="3"><span class="pull-left"><b>{$TOTALCOSTING.TOTALCOST} </b>&nbsp;</span>&nbsp;</td>
	    </tr>
	    </tbody>
	</table>

	<div class="widgetContainer_editlog" data-url="module=SalesOrder&view=Detail&record={$RECORD->getId()}&mode=getProducteditlog">
		<div class="widget_contents">
		</div>
	</div>

	{*<!--<table class="table table-bordered">
	    <tr>
		<td width="83%"><div class="pull-right"><b>{vtranslate('LBL_ITEMS_TOTAL',$MODULE_NAME)}(￥)</b></div>
		</td>
		<td><span class="pull-right"><b>{$SUM}</b></span>
		</td>
		<td width="83%"><div class="pull-right"><b>实际成本(￥)</b></div>
		</td>
		<td><span class="pull-right"><b>{$REALSUM}</b></span>
		</td>
	    </tr>
	</table>-->*}	