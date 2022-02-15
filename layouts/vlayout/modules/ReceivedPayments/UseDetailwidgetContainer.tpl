{strip}
<table class="table table-bordered table-striped blockContainer  showInlineTable ">
			<tr><thead><th class="blockHeader" colspan="9"><span >回款使用明细</span></th></thead></tr>
			<tr>
			<tbody>
				<td><b>操作模块</b></td>
				<td><b>操作者</b></td>
				<td><b>操作时间</b></td>
				<td><b>充值申请时间</b></td>
				<td><b>回款使用情况</b></td>
				<td><b>充值账户币总额</b></td>
				<td><b>实际使用充值账户币</b></td>
				<td><b>备注</b></td>
				<td><b>充值来源</b></td>
			<tr>
			{if !empty($RECEIVED_PAYMENTS_USE_DETAIL)}
				{foreach key=BLOCK_LABEL  item=BLOCK_FIELDS from=$RECEIVED_PAYMENTS_USE_DETAIL name="EditViewBlockLevelLoop"}
					<tr>
						<td>
							{if $BLOCK_FIELDS['type'] eq '1'}
								工单：<a href="index.php?module=SalesOrder&view=Detail&record={$BLOCK_FIELDS['recordid']}" target="_blank">{$BLOCK_FIELDS['recordno']}</a>
							{else}
								充值单：<a href="index.php?module=RefillApplication&view=Detail&record={$BLOCK_FIELDS['recordid']}" target="_blank">{$BLOCK_FIELDS['recordno']}</a>
							{/if}
						</td>
						<td>{$BLOCK_FIELDS['last_name']}</td>
						<td>{$BLOCK_FIELDS['matchdate']}</td>
						<td>{$BLOCK_FIELDS['createdtime']}</td>
						<td>{$BLOCK_FIELDS['detail']}</td>
						<td>{$BLOCK_FIELDS['summoney']}</td>
						<td>{$BLOCK_FIELDS['rate']}</td>
						<td>{$BLOCK_FIELDS['remarks']}</td>
						<td>{vtranslate($BLOCK_FIELDS['rechargesource'],'RefillApplication')}</td>
					</tr>
				{/foreach}
			{else}
				<tr>
					<td colspan="8" style="text-align: center">此回款还未被使用过</td>
				</tr>
			{/if}
			</tbody>
		</table>
{/strip}