{strip}

    <table class="table table-bordered equalSplit detailview-table"><thead>
        <form action="index.php?module=ReceivedPayments&view=List&public=ExportRDALL" method="post">
            <th colspan="2"><h4>非标合同审核设置</h4></th></thead><tbody>
        <tr><td style="text-align: right"><span class="redColor">*</span>审核类型
            </td><td>
                <select id="auditsettingtype" class="chzn-select referenceModulesList streched" name="auditsettingtype">
                    <option value="ContractsAuditset">非标合同审核</option>
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


        <tr><td style="text-align: right"><span class="redColor">*</span>第一次审核人
            </td><td>
                <label class="pull-left">
                    <select id="oneaudituid" name="oneaudituid" class="chzn-select referenceModulesList streched"">
                    <option value="">请选择一项</option>
                    {foreach key=index item=value from=$USER}
                        <option value="{$value.id}">{$value.last_name}</option>
                    {/foreach}
                    </select>
                </label>
            </td></tr>

        <tr><td style="text-align: right"><span class="redColor">*</span>第二次审核人
            </td><td>
                <label class="pull-left">
                    <select id="towaudituid" name="towaudituid" class="chzn-select referenceModulesList streched"">
                    <option value="">请选择一项</option>
                    {foreach key=index item=value from=$USER}
                        <option value="{$value.id}">{$value.last_name}</option>
                    {/foreach}
                    </select>
                </label>
            </td></tr>
        <tr><td style="text-align: right"><span class="redColor">*</span>第三次审核人
            </td><td>
                <label class="pull-left">
                    <select id="audituid3" name="audituid3" class="chzn-select referenceModulesList streched"">
                    <option value="">请选择一项</option>
                    {foreach key=index item=value from=$USER}
                        <option value="{$value.id}">{$value.last_name}</option>
                    {/foreach}
                    </select>
                </label>
            </td></tr>
        <tr><td style="text-align: right">第四次审核人
            </td><td>
                <label class="pull-left">
                    <select id="audituid4" name="audituid4" class="chzn-select referenceModulesList streched"">
                    <option value="">请选择一项</option>
                    {foreach key=index item=value from=$USER}
                        <option value="{$value.id}">{$value.last_name}</option>
                    {/foreach}
                    </select>
                </label>
            </td></tr>
        <tr><td style="text-align: right">第五次审核人
            </td><td>
                <label class="pull-left">
                    <select id="audituid5" name="audituid5" class="chzn-select referenceModulesList streched"">
                    <option value="">请选择一项</option>
                    {foreach key=index item=value from=$USER}
                        <option value="{$value.id}">{$value.last_name}</option>
                    {/foreach}
                    </select>
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
                            <th nowrap><b>审核类型</b></th>
                            <th nowrap><b>部门</b></th>
                            <th nowrap><b>第一次审核人</b></th>
                            <th nowrap><b>第二次审核人</b></th>
                            <th nowrap><b>第三次审核人</b></th>
                            <th nowrap><b>第四次审核人</b></th>
                            <th nowrap><b>第五次审核人</b></th>
                            <th nowrap><b>操作</b></th>
                        </tr>
                        </thead><tbody>
                        {foreach item=value from=$RECOEDS}
                            <tr>
                                <td nowrap><b>{$value['auditsettingtype']}</b></td>
                                <td nowrap><b>{$value['department']}
                                    </b></td>
                                <td nowrap><b>{$value['oneaudituid']}</b></td>
                                <td nowrap><b>{$value['towaudituid']}</b></td>
                                <td nowrap><b>{$value['audituid3']}</b></td>
                                <td nowrap><b>{$value['audituid4']}</b></td>
                                <td nowrap><b>{$value['audituid5']}</b></td>
                                <td nowrap><b><i title="删除" class="icon-trash alignMiddle deleteRecord" data-id="{$value['auditsettingsid']}" style="cursor:pointer"></i></a></b></td>
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
                var auditsettingtype = $("#auditsettingtype").val();
                var department = $("#department").val();
                var oneaudituid = $("#oneaudituid").val();
                var towaudituid = $("#towaudituid").val();
                var threeaudituid = $("#audituid3").val();
                var audituid4 = $("#audituid4").val();
                var audituid5 = $("#audituid5").val();

                if(!auditsettingtype){
                    alert("请选择审核类型");
                    return;
                }
                if(!department){
                    alert("请选择部门");
                    return;
                }
                if(!oneaudituid){
                    alert("请选择第一次审核人");
                    return;
                }
                if(!towaudituid){
                    alert("请选择第二次审核人");
                    return;
                }
                if(!threeaudituid){
                    alert("请选择第三次审核人");
                    return;
                }

                var params = {};
                var module = app.getModuleName();
                params['auditsettingtype'] = auditsettingtype;
                params['department'] = department;
                params['oneaudituid'] = oneaudituid;
                params['towaudituid'] = towaudituid;
                params['threeaudituid'] = threeaudituid;
                params['audituid4'] = audituid4;
                params['audituid5'] = audituid5;
                params['action'] = 'BasicAjax';
                params['module'] = module;
                params['mode'] = 'addAuditsettings';

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
                    'message':'确定要删除该权限吗'
                };
                var id=$(this).data("id")


                Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e) {
                    var params = {};
                    var module = app.getModuleName();
                    params['id'] =id;
                    params['action'] = 'BasicAjax';
                    params['module'] = module;
                    params['mode'] = 'deletedAuditsettings';

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

        {/literal}
    </script>
    {include file='JSResources.tpl'|@vtemplate_path MODULE=$MODULE}
{/strip}