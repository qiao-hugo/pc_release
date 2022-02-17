{strip}
    <form action="index.php?module=Newinvoice&view=List&filter=all_export_data" method="post">
    <table class="table table-bordered equalSplit detailview-table"><thead>
        <th colspan="2">发票导出</th></thead><tbody>
        {*<tr><td style="text-align: right">部门
            </td><td>
                <select id="department_editView_fieldName_dropDown" class="chzn-select referenceModulesList streched" name="department">
                    {foreach key=index item=value from=$DEPARTMENT}
                        <option value="{$index}">{$value}</option>
                    {/foreach}
                </select>
            </td></tr>*}
        <tr><td style="text-align: right">发票申请日期
            </td><td>
                <label class="pull-left">
                    <input class="span9 dateField"type="text" name="datatime" id="datatime" value="{date("Y-m-d",strtotime("-2 months"))}" readonly style="width:100px;">
                </label>
                <label class="pull-left" style="margin:5px 10px 0;">
                    到
                </label>
                <label class="pull-left">
                    <input class="span9 dateField"  type="text" name="enddatatime" data-date-format="yyyy-mm-dd" id="enddatatime" value="{date("Y-m-d")}" readonly style="width:100px;">
                </label>
            </td></tr>

        <tr><td colspan="2" style="text-align: center"><button class="btn btn-primary">导出</button></td></tr>
        </tbody></table>
        </form>
    <script src="/libraries/jquery/chosen/chosen.jquery.min.js"></script>
    <script src="/libraries/jquery/datetimepicker/bootstrap-datetimepicker.min.js"></script>
    {literal}
    <script>
        $('.chzn-select').chosen();
        $('#datatime').datetimepicker({
            format: "yyyy-mm-dd",
            language:  'zh-CN',
            autoclose: true,
            todayBtn: true,
            pickerPosition: "bottom-right",
            showMeridian: 0,
            endDate:new Date(),
            weekStart:1,
            todayHighlight:1,
            startView:2,
            minView:2,
            forceParse:0
        });
        $('#enddatatime').datetimepicker({
            format: "yyyy-mm-dd",
            language:  'zh-CN',
            autoclose: true,
            todayBtn: true,
            pickerPosition: "bottom-right",
            showMeridian: 0,
            endDate:new Date(),
            weekStart:1,
            todayHighlight:1,
            startView:2,
            minView:2,
            forceParse:0
        });
    </script>
{/literal}
{include file='JSResources.tpl'|@vtemplate_path MODULE=$MODULE}
{/strip}
