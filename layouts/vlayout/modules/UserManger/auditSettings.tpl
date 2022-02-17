{strip}

    <table class="table table-bordered equalSplit detailview-table"><thead>
    <form action="index.php?module=ReceivedPayments&view=List&public=ExportRDALL" method="post">
        <th colspan="2"><h4>用户添加审核设置</h4></th></thead><tbody>
        <tr><td style="text-align: right"><span class="redColor">*</span>所属公司
            </td><td>
                <select id="department" name="department" class="chzn-select referenceModulesList streched" name="department">
                    {foreach key=index item=value from=$DEPARTMENT}
                        <option value="{$value['cname']}">{$value['cname']}</option>
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
        <tr><td style="text-align: right"><span class="redColor">*</span>是否需要走审核
            </td><td>
                <label class="pull-left">
                    <select id="audituid5" name="audituid5" class="chzn-select referenceModulesList streched"">
                    <option value="1">是</option>
                    <option value="0">否</option>

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
                        <th nowrap><b>所属公司</b></th>
                        <th nowrap><b>第一次审核人</b></th>
                        <th nowrap><b>第二次审核人</b></th>
                        <th nowrap><b>第三次审核人</b></th>
                        <th nowrap><b>是否需要走审核</b></th>
                        <th nowrap><b>操作</b></th>
                       </tr>
                        </thead><tbody>
                {foreach item=value from=$RECOEDS}
                    <tr>
                        <td nowrap><b>{$value['department']}</b></td>
                        <td nowrap><b>{$value['oneaudituid']}</b></td>
                        <td nowrap><b>{$value['towaudituid']}</b></td>
                        <td nowrap><b>{$value['audituid3']}</b></td>
                        <td nowrap><b>{if $value['audituid5'] eq 1}是{else}否{/if}</b></td>
                        <td nowrap><b><i title="删除" class="icon-trash alignMiddle deleteRecord" data-id="{$value['auditsettingsid']}" style="cursor:pointer"></i>&nbsp;
                                <i title="用户录入审核配置修改" class="icon-pencil alignMiddle modifyRecord" data-id="{$value['auditsettingsid']}"  data-oneaudituid="{$value['oneaudituidn']}"  data-towaudituid="{$value['towaudituidn']}" data-audituid3="{$value['audituid3n']}" data-department="{$value['department']}" data-audituid5="{$value['audituid5']}"  style="cursor:pointer"></i></b></td>
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
                //var auditsettingtype = $("#auditsettingtype").val();
                var department = $("#department").val();
                var oneaudituid = $("#oneaudituid").val();
                var towaudituid = $("#towaudituid").val();
                var audituid3 = $("#audituid3").val();
                var audituid5 = $("#audituid5").val();
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
                if(!audituid3){
                    alert("请选择第三次审核人");
                    return;
                }

                var params = {};
                var module = app.getModuleName();
                params['auditsettingtype'] = "UserManger";
                params['department'] = department;
                params['oneaudituid'] = oneaudituid;
                params['towaudituid'] = towaudituid;
                params['audituid3'] = audituid3;
                params['audituid5'] = audituid5;
                params['action'] = 'ChangeAjax';
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
                    params['action'] = 'ChangeAjax';
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
            $('.listViewEntriesTable').on('click','.modifyRecord',function(){
                var msg={
                    'message':'<h3>用户录入审核配置修改</h3>',
                    action:function(){
                        var oneaudituidm = $("#oneaudituidm").val();
                        var towaudituidm = $("#towaudituidm").val();
                        var audituid3m = $("#audituid3m").val();
                        if(!oneaudituidm){
                            Vtiger_Helper_Js.showPnotify('请选择第一次审核人!');
                            return false;
                        }
                        if(!towaudituidm){
                            alert("请选择第二次审核人");
                            Vtiger_Helper_Js.showPnotify('请选择第二次审核人!');
                            return false;
                        }
                        if(!audituid3m){
                            Vtiger_Helper_Js.showPnotify('请选择第三次审核人!');
                            return false;
                        }

                        return true;
                    }
                };
                var id=$(this).data("id");
                var department = $(this).data("department");
                var oneaudituid=$(this).data("oneaudituid");
                var towaudituid=$(this).data("towaudituid");
                var audituid3=$(this).data("audituid3");
                var audituid5=$(this).data("audituid5");
                var userlists=$("#oneaudituid").html();
                var str='<table class="table table-bordered equalSplit detailview-table"><tbody>\n' +
                    '        <tr><td style="text-align: right"><span class="redColor">*</span>所属公司\n' +
                    '            </td><td>\n' +
                    '                <select id="departmentm" name="departmentm" class="chzn-select referenceModulesList streched" name="department">' +
                    '                  <option value="'+department+'">'+department+'</option>'+
                    '                </select>\n' +
                    '            </td></tr>\n' +
                    '        <tr><td style="text-align: right"><span class="redColor">*</span>第一次审核人\n' +
                    '            </td><td>\n' +
                    '                <label class="pull-left">\n' +
                    '                    <select id="oneaudituidm" name="oneaudituidm" class="chzn-select referenceModulesList streched">\n' +userlists+
                    '                    </select>\n' +
                    '                </label>\n' +
                    '            </td></tr>\n' +
                    '        <tr><td style="text-align: right"><span class="redColor">*</span>第二次审核人\n' +
                    '            </td><td>\n' +
                    '                <label class="pull-left">\n' +
                    '                    <select id="towaudituidm" name="towaudituidm" class="chzn-select referenceModulesList streched"">\n' +userlists+
                    '                    </select>\n' +
                    '                </label>\n' +
                    '            </td></tr>\n' +
                    '        <tr><td style="text-align: right"><span class="redColor">*</span>第三次审核人\n' +
                    '            </td><td>\n' +
                    '                <label class="pull-left">\n' +
                    '                    <select id="audituid3m" name="audituid3m" class="chzn-select referenceModulesList streched"">\n'+userlists+
                    '                    </select>\n' +
                    '                </label>\n' +
                    '            </td></tr>\n' +
                    '        <tr><td style="text-align: right"><span class="redColor">*</span>是否需要走审核\n' +
                    '            </td><td>\n' +
                    '                <label class="pull-left">\n' +
                    '                    <select id="audituid5m" name="audituid5m" class="chzn-select referenceModulesList streched"">\n' +
                    '                    <option value="1">是</option>\n' +
                    '                    <option value="0">否</option>\n' +
                    '                    </select>\n' +
                    '                </label>\n' +
                    '            </td></tr>\n' +
                    '        </tbody></table>';
                Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e) {
                    var oneaudituidm = $("#oneaudituidm").val();
                    var towaudituidm = $("#towaudituidm").val();
                    var audituid3m = $("#audituid3m").val();
                    var audituid5m = $("#audituid5m").val();
                    var params = {};
                    var module = app.getModuleName();
                    params['auditsettingtype'] = "UserManger";
                    params['id'] =id;
                    params['oneaudituid'] = oneaudituidm;
                    params['towaudituid'] = towaudituidm;
                    params['audituid3'] = audituid3m;
                    params['audituid5'] = audituid5m;
                    params['action'] = 'ChangeAjax';
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
                $('.modal-content .modal-body').append('<div name="massEditContent"><div class="modal-body" style="height:400px;">'+str+'</div></div>');
                $('.modal-content .modal-body').css({overflow:'hidden'});
                $("#oneaudituidm").val(oneaudituid);
                $("#towaudituidm").val(towaudituid);
                $("#audituid3m").val(audituid3);
                $("#audituid5m").val(audituid5);
                $("#oneaudituidm").chosen();
                $("#towaudituidm").chosen();
                $("#audituid3m").chosen();
                $("#audituid5m").chosen();
            });

        });



        {/literal}
    </script>
{include file='JSResources.tpl'|@vtemplate_path:'UserManger' MODULE=$MODULE}
{/strip}