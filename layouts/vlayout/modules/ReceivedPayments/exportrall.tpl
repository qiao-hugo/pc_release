{strip}

    <table class="table table-bordered equalSplit detailview-table"><thead>
    <form action="index.php?module=ReceivedPayments&view=List&public=ExportRDALL" method="post">
        <th colspan="2">核对合同回款导出</th></thead><tbody>
        {*<tr><td style="text-align: right">部门
            </td><td>
                <select id="department_editView_fieldName_dropDown" class="chzn-select referenceModulesList streched" name="department">
                    {foreach key=index item=value from=$DEPARTMENT}
                        <option value="{$index}">{$value}</option>
                    {/foreach}
                </select>
            </td></tr>*}
        <tr><td style="text-align: right">导出时间
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
            <tr><td style="text-align: right">选择时间(合同)
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
            </td></tr>

        <tr><td colspan="2" style="text-align: center"><button class="btn btn-primary">导出</button></td></tr>
        </form>
        <tr><td colspan="2" style="text-align: center"><button class="btn btn-primary" id="preview">预览</button></td></tr>
        </tbody></table>
    <div style="margin-top:10px;">
        <div class="row-fluid" id="c" style="width:100%;">
            <div style="border:1px solid #ccc;margin:0 auto 20px;padding-right:20px;padding-top:10px;">
                <div id="bartable1" class="span12" style="height:490px;"></div>
                <div class="clearfix"></div></div>
            </div>
        </div>
    </div>

    <script src="/libraries/jquery/chosen/chosen.jquery.min.js"></script>
    <script src="/libraries/jquery/datetimepicker/bootstrap-datetimepicker.min.js"></script>
    {literal}
    <script>
        $(function(){
            $('#preview').click(function(){
                var params={};
                var module = app.getModuleName();
                var enddatatime=$('input[name="enddatatime"]').val();
                var datatime=$('input[name="datatime"]').val();
                var cmodule=$('input[name="cmodule"]:checked').val();
                var timeselected=$('input:radio:checked').val();
                params['datatime']=datatime;
                params['enddatatime']=enddatatime;
                params['cmodule']=cmodule;
                params['action']='BasicAjax';
                params['module']=module;
                params['mode']='preview';
                params['timeselected']=timeselected;
                $('#bartable1').text('');
                AppConnector.request(params).then(function(data){
                    if(data.result!=null) {
                        $('#bartable1').append(data.result);
                        jQuery('#tbl_Detail').DataTable({
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
                            scrollY: "460px",
                            sScrollX: "disabled",
                            aLengthMenu: [10, 20, 50, 100,],
                            fnDrawCallback: function () {

                            }
                        });
                    }else{
                        $('#bartable1').append('请先授权');
                    }
                });
            });
        });

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
