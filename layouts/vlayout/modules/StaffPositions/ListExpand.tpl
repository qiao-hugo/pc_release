{strip}
    <script type="text/javascript" src="/libraries/media/jquery.dataTables.js"></script>
<div class="row-fluid " id="c">
    <div class="span12" style="margin:0 auto;font-size:16px;text-align: center"><span class="label label-a_normal" style="font-size:16px;">人员部门职位信息</span></div>
	<table class="table table-bordered table-hover" id="tbl_Detail">
		<thead>
            <tr>
                <td style="text-align: center">姓名</td>
                <td style="text-align: center">部门</td >
                <td style="text-align: center">职位</td>
            </tr>
		</thead>

		<tbody>
			{foreach item=USER key = key from=$SMALLUSER}

				<tr>
					<td style="text-align: center"> {$USER['last_name']}</td>

					<td style="text-align: center">
					{$USER["department"]}

					</td>
                    <td style="text-align: center">
                        {if $USER['title'] neq 'null'}{$USER['title']}{/if}

                    </td>
				</tr>
			{/foreach}
		</tbody>
	</table>
</div>
<br>
<br>
<br>

{literal}
<script>
	$('#tbl_Detail').DataTable({
                language: {
                    "sProcessing": "处理中...",
                    "sLengthMenu": "显示 _MENU_ 项结果",
                    "sZeroRecords": "没有匹配结果",
                    "sInfo": "显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",
                    "sInfoEmpty": "显示第 0 至 0 项结果，共 0 项",
                    "sInfoFiltered": "(由 _MAX_ 项结果过滤)",
                    "sInfoPostFix": "",
                    "sSearch": "当前页快速检索:",
                    "sUrl": "",
                    "sEmptyTable": "表中数据为空",
                    "sLoadingRecords": "载入中...",
                    "sInfoThousands": ",",
                    "oPaginate": {"sFirst": "首页", "sPrevious": "上页", "sNext": "下页", "sLast": "末页"},
                    "oAria": {"sSortAscending": ": 以升序排列此列", "sSortDescending": ": 以降序排列此列"}
                },
                scrollY: "580px",
                sScrollX: "disabled",
                aLengthMenu: [15, 30, 50, 100,],
                fnDrawCallback: function () {
                }
            });
</script>
{/literal}


{/strip}
