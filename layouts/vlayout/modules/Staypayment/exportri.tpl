{strip}
    <form action="index.php" method="post">
    <table class="table table-bordered equalSplit detailview-table"><thead>
        <th colspan="2">代付款附件导出</th></thead><tbody>
        <tr><td style="text-align: right">合同编号：
            </td><td>
                <label class="pull-left">
                    <textarea class="span9 dateField"type="text" name="filename" id="filename" value=""  style="width:300px;"></textarea>
                    <input class="span9 dateField"type="hidden" name="action" id="action" value="BasicAjax"  style="width:100px;">
                    <input class="span9 dateField"type="hidden" name="mode" id="mode" value="downfileZip"  style="width:100px;">
                    <input class="span9 dateField"type="hidden" name="module" id="module" value="Staypayment"  style="width:100px;">
                </label>
            </td></tr>
        <tr><td colspan="2" style="text-align: center"><button class="btn btn-primary">导出</button>&nbsp;&nbsp;&nbsp;&nbsp;<input type="reset" class="btn" value="清空输入框" /></td></tr>
        </tbody></table>
        </form>
{include file='JSResources.tpl'|@vtemplate_path MODULE=$MODULE}
{/strip}
