{strip}

    <table class="table table-bordered equalSplit detailview-table"><thead>
        <form action="index.php?module=Newinvoice&view=List&public=pre_invoice_remind" method="post">
            <th colspan="2"><h4>预开票回款提醒设置</h4></th></thead><tbody>
        <tr><td style="text-align: right"><span class="redColor">*</span>设置类型
            </td><td>
                <select id="remindtype" class="chzn-select referenceModulesList streched" name="remindtype">
                    <option value="PreInvoiceRemindSetting">预开票回款提醒设置</option>
                </select>
            </td></tr>
        <tr><td style="text-align: right"><span class="redColor">*</span>部门
            </td><td>
                <select id="department" name="department" class="chzn-select referenceModulesList streched" name="department">
                    {foreach key=index item=value from=$DEPARTMENT}
                        <option value="{$index}">{$value}</option>
                    {/foreach}
                </select>
            </td></tr>

        <tr><td style="text-align: right"><span class="redColor">*</span>回款逾期锁号天数
            </td><td>
                <label class="pull-left">
                    <input type="number" id="over_days" name="over_days"/>
                </label>
            </td></tr>
        <tr><td style="text-align: right"><span class="redColor">*</span>提前提醒天数
            </td><td>
                <label class="pull-left">
                    <input type="number" id="days" name="days"/>
                </label>
            </td></tr>

        </form>
        <tr><td colspan="2" style="text-align: center"><button class="btn btn-primary" id="preview">添加</button></td></tr>
        </tbody></table>


    <div style="margin-top:10px;">
        <div class="row-fluid" id="c" style="width:100%;">
            <div style="border:1px solid #ccc;margin:0 auto 20px;padding-right:20px;padding-top:10px;">
                <div id="bartable1" class="span12" style="height:490px;">
                    <table id="tbl_Detail" class="table listViewEntriesTable" width="100%"><thead><tr>
                            <th nowrap><b>提醒类型</b></th>
                            <th nowrap><b>部门</b></th>
                            <th nowrap><b>回款逾期锁号天数</b></th>
                            <th nowrap><b>提前提醒天数</b></th>
                            <th nowrap><b>操作</b></th>
                        </tr>
                        </thead><tbody>
                        {foreach item=value from=$RECOEDS}
                            <tr>
                                <td nowrap><b>{$value['remindtype']}</b></td>
                                <td nowrap><b>{$value['department']}</b></td>
                                <td nowrap><b>{$value['over_days']}</b></td>
                                <td nowrap><b>{$value['days']}</b></td>
                                <td nowrap><b><i title="删除" class="icon-trash alignMiddle deleteRecord" data-id="{$value['remindid']}" style="cursor:pointer"></i></a></b></td>
                            </tr>
                        {/foreach}
                        </tbody></table>
                </div>
                <div class="clearfix"></div></div>
        </div>
    </div>
    </div>

    <script src="/libraries/jquery/chosen/chosen.jquery.min.js"></script>
    <script src="/libraries/jquery/datetimepicker/bootstrap-datetimepicker.min.js"></script>

    <script>
        {literal}
        $(function(){
            $('#preview').click(function(){
                var remindType = $("#remindtype").val();
                var department = $("#department").val();
                var remind_days = $("#days").val();
                var over_days = $("#over_days").val();

                if(!remindType){
                    alert("请选择提醒类型");
                    return;
                }
                if(!department){
                    alert("请选择部门");
                    return;
                }
                if(!over_days){
                    alert("请正确填写回款逾期锁号天数");
                    return;
                }
                if(!remind_days){
                    alert("请正确填写提前提醒天数");
                    return;
                }

                var params = {};
                var module = app.getModuleName();
                params['remindtype'] = remindType;
                params['department'] = department;
                params['over_days'] = over_days;
                params['days'] = remind_days;
                params['action'] = 'BasicAjax';
                params['module'] = module;
                params['mode'] = 'addPreInvoiceRemind';

                var progressIndicatorElement = jQuery.progressIndicator({
                    'message' : '正在请求',
                    'position' : 'html',
                    'blockInfo' : {'enabled' : true}
                });

                AppConnector.request(params).then(function(data){
                    if (data.result.flag == '1') {
                        /*progressIndicatorElement.progressIndicator({
                                    'mode' : 'hide'
                                });*/
                        window.location.reload();
                    } else {
                        alert(data.result.msg);
                    }
                });
            });

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
            $('.listViewEntriesTable').on('click','.deleteRecord',function(){
                var msg={
                    'message':'确定要删除该设置吗'
                };
                var id=$(this).data("id")


                Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e) {
                    var params = {};
                    var module = app.getModuleName();
                    params['id'] =id;
                    params['action'] = 'BasicAjax';
                    params['module'] = module;
                    params['mode'] = 'delPreInvoiceRemind';

                    var progressIndicatorElement = jQuery.progressIndicator({
                        'message' : '正在请求',
                        'position' : 'html',
                        'blockInfo' : {'enabled' : true}
                    });
                    AppConnector.request(params).then(function (data) {
                        window.location.reload();
                    });
                });
            });

        });
        // $('#modulename').on('change',function(){
        //     var modename=$(this).val();
        //     $('#classname').empty();
        //     $('#classname').append(contractoption[modename]);
        //     $('#classname').trigger("liszt:updated");
        //
        //
        //     $('.chzn-select').chosen();
        // });
        // $('#classname').append(contractoption.ServiceContracts);
        $('.chzn-select').chosen();


        {/literal}
    </script>
    {include file='JSResources.tpl'|@vtemplate_path MODULE=$MODULE}
{/strip}