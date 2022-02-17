{strip}
<div class="row-fluid " id="c">
	<div>{$NUM['num']}</div>
	<table class="table listViewEntriesTable" style="width=100%" id="table_double">
		<thead>
			<tr style="background:#dff0d8;color:#FFF;">
                <th style="padding:0">客户</th>
                <th style="padding:0">签单时间</th>
				<th style="padding:0">产品</th>
				<th style="padding:0">商务</th>
				<th style="padding:0">客服</th>
				<th style="padding:0">备注</th>
				<th style="padding:0">状态</th>
				<th style="padding:0">跟进内容</th>
				<th style="padding:0">跟进时间</th>
				<th style="padding:0">下次跟进内容</th>
				<th style="padding:0">下次跟进时间</th>
				<th style="padding:0">收到资料时间</th>
				<th style="padding:0">拓词否</th>
				<th style="padding:0">拓词完成</th>
				<th style="padding:0">截稿</th>
			</tr>
		</thead>
        <tfoot>
        <tr>
            <th  style="padding:0">客户</th>
            <th  style="padding:0">签单时间</th>
            <th  style="padding:0">产品</th>
            <th  style="padding:0">商务</th>
            <th  style="padding:0">客服</th>
            <th  style="padding:0">备注</th>
            <th  style="padding:0">状态</th>
            <th  style="padding:0">跟进内容</th>
            <th  style="padding:0">跟进时间</th>
            <th  style="padding:0">下次跟进内容</th>
            <th  style="padding:0">下次跟进时间</th>
            <th  style="padding:0">收到资料时间</th>
            <th  style="padding:0">拓词否</th>
            <th  style="padding:0">拓词完成</th>
            <th  style="padding:0">截稿</th>
        </tr>
        </tfoot>
		<tbody>
			{foreach item=data key = key from=$DATA}
				<tr>
                    <td><a class="btn-link" href="index.php?module=Accounts&view=Detail&amp;record={$data['accountid']}&amp;" target="_blank">{$data['客户名称']}</a></td>
                    <td>{$data['签单时间']}</td>
					<td>{$data['产品名称']}</td>
					<td>{$data['商务' ]}</td>
					<td>{$data['客服'  ]}</td>
					<td>{$data['客服备注']}</td>
					<td>{$data['客户状态']}</td>
					<td>{$data['跟进内容']}</td>
					<td>{$data['跟进时间']}</td>
					<td>{$data['下次跟进内容']}</td>
					<td>{$data['下次跟进时间']}</td>
					<td>{$data['客服收到资料时间']}</td>
					<td>{$data['是否拓词']}</td>
					<td>{$data['拓词完成时间']}</td>
					<td>{$data['截稿时间']}</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
</div>
{/strip}
