{strip}
    <form action="index.php?module=RefillApplication&action=BasicAjax&mode=RefillSumExportData" method="post">
   <!-- <form action="index.php?module=RefillApplication&view=List&public=refillDetailExportData" method="post">-->
    <table class="table table-bordered equalSplit detailview-table"><thead>

        <th colspan="2"><h4>回款充值申请单导出</h4></th></thead><tbody>
        <tr><td style="text-align: right"><span class="redColor">*</span>入账日期
            </td><td>
                <label class="pull-left">
                    <input class="span9 dateField"type="text" name="datatime" id="datatime" value="{date("Y-m-d",strtotime("-1 months"))}" readonly style="width:100px;">
                </label>
                <label class="pull-left" style="margin:5px 10px 0;">
                    到
                </label>
                <label class="pull-left">
                    <input class="span9 dateField"  type="text" name="enddatatime" data-date-format="yyyy-mm-dd" id="enddatatime" value="{date("Y-m-d")}" readonly style="width:100px;">
                </label>
            </td></tr>

        <tr><td colspan="2" style="text-align: center"><button class="btn btn-primary" id="preview">导出</button></td></tr>
        </tbody></table>
    </form>




    <script src="/libraries/jquery/datetimepicker/bootstrap-datetimepicker.min.js"></script>

    <script>
        {literal}
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