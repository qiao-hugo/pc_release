{strip}

    <table class="table table-bordered equalSplit detailview-table"><thead>
        <th colspan="2">业绩导出(<font color=red>如果财务的数据显示为空，则未设置相关时间，请及时提供时间</font>)</th></thead><tbody>
        <tr><td style="text-align: right">财务关帐时间
            </td><td>
                {$SETTLEMENTMONTH['Received'][0]} 到 {$SETTLEMENTMONTH['Received'][1]}
            </td></tr>
        <tr><td style="text-align: right">系统关帐时间
            </td><td>
                {$SETTLEMENTMONTH['System'][0]} 到 {$SETTLEMENTMONTH['System'][1]}
            </td></tr>

        <tr><td colspan="2" style="text-align: center"><a href="index.php?module=ServiceContracts&view=List&public=ExportD" class="btn btn-primary">导出</a></td></tr>
        </tbody></table>

{/strip}
