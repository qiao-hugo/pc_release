{strip}


    <div style="margin-top:10px;">
        <div class="row-fluid" id="c" style="width:100%;">
            <div style="border:1px solid #ccc;margin:0 auto 20px;padding-right:20px;padding-top:10px;">
                <div id="bartable1" class="span12" style="height:490px;">
                    <table id="tbl_Detail" class="table listViewEntriesTable" width="100%"><thead><tr>
                            <th nowrap><b>分类名称</b></th>
                            <th nowrap><b>产品编码</b></th>
                            <th nowrap><b>操作</b></th>
                        </tr>
                        </thead><tbody>
                        {foreach item=value from=$RECOEDS}
                            <tr>
                                <td nowrap><b>{$value['contract_type']}</b></td>
                                <td nowrap><b>{$value['productclass']}</b></td>
                                <td nowrap><b><i title="同步" class="icon-repeat alignMiddle syncRecord" data-id="{$value['contract_typeid']}" style="cursor:pointer"></i></a></b></td>
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
            $('.listViewEntriesTable').on('click','.syncRecord',function(){
                var msg={
                    'message':'确定要同步该编码到电子合同管理后台'
                };
                var id=$(this).data("id")


                Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e) {
                    var params = {};
                    var module = app.getModuleName();
                    params['id'] =id;
                    params['action'] = 'BasicAjax';
                    params['module'] = module;
                    params['mode'] = 'syncProduct2CodeNoTyun';

                    var progressIndicatorElement = jQuery.progressIndicator({
                        'message' : '正在请求',
                        'position' : 'html',
                        'blockInfo' : {'enabled' : true}
                    });
                    AppConnector.request(params).then(function (data) {
                        progressIndicatorElement.progressIndicator({'mode': 'hide'});
                        if(data && data.success){
                            window.location.reload();
                        }else{
                            alert(data.msg);
                        }
                    });
                });
            });

        });

        {/literal}
    </script>
    {include file='JSResources.tpl'|@vtemplate_path MODULE=$MODULE}
{/strip}
