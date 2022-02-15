{strip}
    <form action="index.php?module=ServiceContracts&view=List&public=isfulldeliverydata" method="post">
        <table class="table table-bordered equalSplit detailview-table"><thead>
            <th colspan="2">产品交付导出</th></thead><tbody>
            <tr><td style="text-align: right">导出月份
                </td><td>
                    <label class="pull-left">
                        <input class="span9 dateField" type="text" name="datatime" id="datatime" value="{date("Y-m")}" readonly style="width:100px;">
                    </label>
                    <!--<label class="pull-left" style="margin:5px 10px 0;">
                        到
                    </label>
                    <label class="pull-left">
                        <input class="span9 dateField"  type="text" name="enddatatime" data-date-format="yyyy-mm-dd" id="enddatatime" value="{date("Y-m")}" readonly style="width:100px;">
                    </label>-->
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
            format: "yyyy-mm",
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
            startView:3,
            minView:3,
            forceParse:0
        });
        $('#enddatatime').datetimepicker({
            format: "yyyy-mm",
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
            startView:3,
            minView:3,
            forceParse:0
        });
    </script>
{/literal}
{/strip}
