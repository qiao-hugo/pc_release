<strip>
<div>
	<table id="polytypetable"
		class="table table-bordered table-striped blockContainer showInlineTable ">
		<thead>
			<tr>
				<th class="blockHeader" colspan="4">
						多规格
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>规格名称</td>
				<td>市场价</td>
				<td>成本价格</td>
				<td>规则</td>
			</tr>
			{foreach key=CURRENTSTAND_LABEL item=CURRENTSTAND_FIELD from=$STAND}
			<tr>
				<td>{$CURRENTSTAND_FIELD['standardname']}</td>
				<td>{$CURRENTSTAND_FIELD['singleprice']}</td>
				<td>{$CURRENTSTAND_FIELD['realprice']}</td>
				<td>{$CURRENTSTAND_FIELD['standardvalue']}</td>
			</tr>
			{/foreach}
		</tbody>
	</table>

	<table
		class="table table-bordered table-striped blockContainer showInlineTable ">
		<thead>
			<tr>
				<th class="blockHeader" colspan="4">套餐信息</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>产品名称</td>
				<td>默认规格</td>
			</tr>
			{foreach item=PACKAGE from=$package}
			<tr>
				<td>{$PACKAGE['label']}</td>
				<td>{$PACKAGE['defaultdtand']}</td>
			</tr>
			{/foreach}
		</tbody>
	</table>
</div>
</strip>