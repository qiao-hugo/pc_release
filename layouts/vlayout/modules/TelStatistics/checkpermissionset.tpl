{strip}

    <table class="table table-bordered equalSplit detailview-table"><thead>
    <form action="index.php?module=ReceivedPayments&view=List&public=ExportRDALL" method="post">
        <th colspan="2">设置权限(设置后，不会出现在列表)</th></thead><tbody>
        {*<tr><td style="text-align: right">部门
            </td><td>
                <select id="department_editView_fieldName_dropDown" class="chzn-select referenceModulesList streched" name="department">
                    {foreach key=index item=value from=$DEPARTMENT}
                        <option value="{$index}">{$value}</option>
                    {/foreach}
                </select>
            </td></tr>*}
        <tr><td style="text-align: right"><span class="redColor">*</span>角色
            </td><td>
                <label class="pull-left">
                    <select id="roleid" class="chzn-select referenceModulesList streched"">
                        <option value="">请选择一项</option>
                        {foreach key=index item=value from=$ROLES}
                            <option value="{$index}">{$value}</option>
                        {/foreach}
                    </select>
                </label>
            </td></tr>
        <tr><td style="text-align: right"><span class="redColor">*</span>模块
            </td><td>
                <label class="pull-left">
                    <select id="modulename" class="chzn-select referenceModulesList streched">
                        {foreach from=$ModuleName item=SMODULE}
                            <option value="{$SMODULE['module']}">{vtranslate({$SMODULE['module']})}</option>
                        {/foreach}
                    </select>
                </label>
                <span class="pull-left">&nbsp;</span>
            </td></tr>
        <tr><td style="text-align: right"><span class="redColor">*</span>可设置
            </td><td>
                <label class="pull-left">
                    <select id="classname" class="chzn-select referenceModulesList streched" multiple>
                        {$INIT_TELSTATISTICS}
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
                        <th nowrap><b>角色</b></th>
                        <th nowrap><b>模块</b></th>
                        <th nowrap><b>可设置</b></th>
                        <th nowrap><b>操作</b></th>
                       </tr>
                        </thead><tbody>
                {foreach item=value from=$RECOEDS}
                    <tr>
                        <td nowrap><b>{$value['rolename']}</b></td>
                        <td nowrap><b>{vtranslate({$value['module']})}
                            </b></td>
                        <td nowrap><b>{$value['classnamezh']}</b></td>
                        <td nowrap><b><i title="删除" class="icon-trash alignMiddle deleteRecord" data-id="{$value['id']}" style="cursor:pointer"></i></a></b></td>
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
        var  contractoption={$CLASSNAME};
        {literal}
        $(function(){
            $('#preview').click(function(){
                var params={};
                var module = app.getModuleName();
                var roleid=$("#roleid").val();
                var modulename=$("#modulename").val();
                var classname=$("#classname").val();
                params['classname']=classname;
                params['roleid']=roleid;
                params['modulename']=modulename;
                params['action']='BasicAjax';
                params['module']=module;
                params['mode']='add';
                console.log(modulename);
                if(roleid==''){
                    alert("角色不能为空");
                    return;
                }

                if(modulename==null&&typeof(modulename)!='undefined'){
                    alert("模块必选");
                    return;
                }
                if(classname==null&&typeof(classname)!='undefined'){
                    alert("可设置项必选");
                    return;
                }
                AppConnector.request(params).then(function(data){
                   window.location.reload();
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
                    'message':'确定要删除该用户的权限吗'

                };
                var id=$(this).data("id")
                Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e) {
                    var params = {};
                    var module = app.getModuleName();
                    params['id'] =id;
                    params['action'] = 'BasicAjax';
                    params['module'] = module;
                    params['mode'] = 'deleted';
                    AppConnector.request(params).then(function (data) {
                        window.location.reload();
                    });
                });
            });

        });
        $('#modulename').on('change',function(){
            var modename=$(this).val();
             $('#classname').empty();
             $('#classname').append(contractoption[modename]);
             $('#classname').trigger("liszt:updated");


            $('.chzn-select').chosen();
        });
        $('#classname').append(contractoption.ServiceContracts);
        $('.chzn-select').chosen();


        {/literal}
    </script>
{include file='JSResources.tpl'|@vtemplate_path MODULE=$MODULE}
{/strip}
