{strip}

    <table class="table table-bordered equalSplit detailview-table"><thead>
    <form action="index.php?module=ReceivedPayments&view=List&public=ExportNPPD" method="post">
        <th colspan="2">未匹配回款导出</th></thead><tbody>
        <tr><td style="text-align: right">账号
            </td><td>
                <select id="department_editView_fieldName_dropDown" class="chzn-select referenceModulesList streched" name="owncompany">
                    <option value="all">全部</option>
                    {foreach key=index item=value from=$OWNCOMPANY}
                        <option value="{$value.owncompany}">{$value.owncompany}</option>
                    {/foreach}
                </select>
            </td></tr>
        <tr><td style="text-align: right">入账日期
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
            {*<tr><td style="text-align: right">回款账号
            </td><td>
                <label class="pull-left">
                    <input  type="radio" name="timeselected" value="1" checked>签订时间
                </label>
                <span class="pull-left">&nbsp;</span>
                <label class="pull-left">
                    <input type="radio" name="timeselected" value="2">归还时间
                </label>
            </td></tr>
        <tr><td style="text-align: right">&nbsp;
            </td><td>
                <label class="pull-left">
                    <input  type="radio" name="cmodule" value="1" checked>合同
                </label>
                <span class="pull-left">&nbsp;</span>
                <label class="pull-left">
                    <input type="radio" name="cmodule" value="2">回款
                </label>
            </td></tr>*}

        <tr><td colspan="2" style="text-align: center"><button class="btn btn-primary">导出</button></td></tr>
        </form>

        </tbody></table>


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
            {/literal}
            {*startDate:'{date("Y-m-d",strtotime("-2 months"))}',*}
            {literal}
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
            {/literal}
            {*startDate:'{date("Y-m-d",strtotime("-2 months"))}',*}
            {literal}
            endDate:new Date(),
            weekStart:1,
            todayHighlight:1,
            startView:2,
            minView:2,
            forceParse:0
        });
        {/literal}
    </script>
{include file='JSResources.tpl'|@vtemplate_path MODULE=$MODULE}
{/strip}
