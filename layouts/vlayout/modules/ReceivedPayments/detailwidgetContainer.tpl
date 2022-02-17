{strip}
{*<table class="table table-bordered table-striped blockContainer showInlineTable ">
			<thead><tr  class="blockHeader"><th><span >产品明细</span></th>
				<th colspan="2">
					收款金额总剩余:<span>{$SUBTRACT}</span>
				</th>
			</tr>
			<tr>
</thead>
				<td><b>产品名称</b></td>
				<td><b>产品金额</b></td>
				<td><b>已收产品总金额</b></td>
			</tr>
			{foreach key=BLOCK_LABEL  item=productlis from=$PRODUCTLIS name="inputalready"}
			<tr>
				<td>{$productlis['productname']}</td>
				<td>{$productlis['marketprice']}</td>
				<td>{$productlis['already']}</td>
			</tr>
			{/foreach}
</table >*}

<table class="table table-bordered table-striped blockContainer  showInlineTable ">
			<tr><thead><th class="blockHeader" colspan="7"><span >回款历史</span></th></thead></tr>
			<tr>
			<tbody>
				<td><b>入账日期</b></td>
				<td><b>金额</b></td>
				<td><b>公司账号</b></td>
				<td><b>汇款抬头</b></td>
				<td><b>货币类型</b></td>
				<td><b>本位币</b></td>
				<td><b>汇率</b></td>
			<tr>
			{foreach key=BLOCK_LABEL  item=BLOCK_FIELDS from=$RECEIVEDHISTORY name="EditViewBlockLevelLoop"}
			<tr>
				<td>{$BLOCK_FIELDS['reality_date']}</td>
				<td>{$BLOCK_FIELDS['unit_price']}</td>
				<td>{$BLOCK_FIELDS['owncompany']}</td>
				<td>{$BLOCK_FIELDS['paytitle']}</td>
				<td>{$BLOCK_FIELDS['receivementcurrencytype']}</td>
				<td>{$BLOCK_FIELDS['standardmoney']}</td>
				<td>{$BLOCK_FIELDS['exchangerate']}</td>
			</tr>
			</thbody>
			{/foreach}
		</table>
{/strip}