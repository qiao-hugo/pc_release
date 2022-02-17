{*<table class="table table-bordered table-striped blockContainer showInlineTable ">
			<tr><th class="blockHeader"><span >产品明细</span></th>
				<th colspan="3">
					收款金额总剩余:<span>{$SUBTRACT};</span>
					<!-- 本次收款金额剩余<span id="currsubtract">{$afterSUBTRACT}</span> -->
				</th>
			</tr>
			<tr>
				<td><b>产品名称</b></td>
				<td><b>产品金额</b></td>
				<td><b>已收产品总金额</b></td>
				<td><b>收款金额(人民币)</b></td>
			</tr>
			{foreach key=BLOCK_LABEL  item=productlis from=$PRODUCTLIS name="inputalready"}
			<tr>
			{if $ISCHUNA eq true}
				<td>{$productlis['productname']}</td>
				<td>{$productlis['marketprice']}</td>
				<td>{$productlis['already']}</td>
				<td>{$productlis['alreadyprice']}</td>
			{else}
				<td>{$productlis['productname']}</td>
				<td>{$productlis['marketprice']}</td>
				<td>{$productlis['already']}</td>
				*}{*<!--<td><input {$BLOCK_LABEL}  name= "inputalready[{$productlis['salesorderproductsrelid']},{$productlis['productid']}]" class="inputalready" type="text" value="{$productlis['alreadyprice']}"></td>-->*}{*
				<td><input {$BLOCK_LABEL} readonly="readonly" name= "inputalready[{$productlis['salesorderproductsrelid']},{$productlis['productid']}]" class="inputalready" type="text" value="{$productlis['fenchen']}"></td>
			{/if}
			</tr>
			{/foreach}
</table >*}

<table class="table table-bordered table-striped blockContainer  showInlineTable ">
			<tr><th class="blockHeader" colspan="7"><span >回款历史</span></th></tr>
			<tr>
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
			{/foreach}
		</table>
