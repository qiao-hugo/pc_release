{strip}

    <table class="table table-bordered equalSplit detailview-table"><thead>
    <form action="index.php?module=ReceivedPayments&view=List&public=ExportRDALL" method="post">
        <th colspan="2">核对合同回款导出部门设置</th></thead><tbody>
        {*<tr><td style="text-align: right">部门
            </td><td>
                <select id="department_editView_fieldName_dropDown" class="chzn-select referenceModulesList streched" name="department">
                    {foreach key=index item=value from=$DEPARTMENT}
                        <option value="{$index}">{$value}</option>
                    {/foreach}
                </select>
            </td></tr>*}
        <tr><td style="text-align: right">用户
            </td><td>
                <label class="pull-left">
                    <select id="user_id" class="chzn-select referenceModulesList streched"">
                        <option value="">请选择一项</option>
                        {foreach key=index item=value from=$USER}
                            <option value="{$value.id}">{$value.last_name}</option>
                        {/foreach}
                    </select>
                </label>
            </td></tr>
            <tr><td style="text-align: right">合同所属部门
            </td><td>
                <label class="pull-left">
                            <select id="department_s" class="chzn-select referenceModulesList streched" multiple>
                                {foreach key=index item=value from=$USERDEPARTMENT}
                                <option value="{$index}">{$value}</option>
                            {/foreach}
                            </select>
                </label>
                <span class="pull-left">&nbsp;</span>
            </td></tr>
            <tr><td style="text-align: right">回款所属部门
            </td><td>
                <label class="pull-left">
                    <select id="department_r" class="chzn-select referenceModulesList streched" multiple>
                                {foreach key=index item=value from=$USERDEPARTMENT}
                            <option value="{$index}">{$value}</option>
                        {/foreach}
                            </select>
                </label>
            </td></tr>
        <tr><td style="text-align: right">&nbsp;
            </td><td>
                <label class="pull-left">
                    <input  type="checkbox" name="cmodules" value="ServiceContracts" checked>合同
                </label>
                <span class="pull-left">&nbsp;</span>
                <label class="pull-left">
                    &nbsp;&nbsp;<input type="checkbox" name="cmoduler" value="ReceivedPayments" checked>回款
                </label>
                <label class="pull-left" style="color:red;">
                   &nbsp;&nbsp;&nbsp;&nbsp; PS:(部门包含其下的部门,修改请直接添加用户的权限即可)
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
                        <th nowrap><b>姓名</b></th>
                        <th nowrap><b>部门</b></th>
                        <th nowrap><b>模块</b></th>
                        <th nowrap><b>操作</b></th>
                       </tr>
                        </thead><tbody>
                {foreach item=value from=$RECOEDS}
                    {assign var=demptemp value=explode(',',$value['permissions'])}
                    {assign var=demptempt value=''}
                    <tr>
                        <td nowrap><b>{$value['last_name']}</b></td>
                        <td nowrap><b>{foreach item=demptempvalue from=$demptemp}
                                    {$demptempt|cat:$PARTMENTS[$demptempvalue]|cat:','}
                                {/foreach}
                            </b></td>
                        <td nowrap><b>{$value['module']}</b></td>
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
    {literal}
    <script>
        $(function(){
            $('#preview').click(function(){
                var params={};
                var module = app.getModuleName();
                var userid=$("#user_id").val();
                var dempartcontracts=$("#department_s").val();
                var dempartpayments=$("#department_r").val();
                var cmodules=$('input[name="cmodules"]:checked').val();
                var cmoduler=$('input[name="cmoduler"]:checked').val();
                params['dempartcontracts']=dempartcontracts;
                params['dempartpayments']=dempartpayments;
                params['userid']=userid;
                params['cmodules']=cmodules;
                params['cmoduler']=cmoduler;
                params['action']='BasicAjax';
                params['module']=module;
                params['mode']='add';
                if(userid==''){
                    alert("用户不能为空");
                    return;
                }
                if(typeof(cmoduler)==='undefined' && typeof(cmodules)==='undefined'){
                    alert("请名勾选其中一项");
                    return;
                }
                if(dempartcontracts==null&&typeof(cmodules)!='undefined'){
                    alert("请选择合同所属部门");
                    return;
                }
                if(dempartpayments==null&&typeof(cmoduler)!='undefined'){
                    alert("请选择回款所属部门");
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
                    params['mode'] = 'delete';
                    AppConnector.request(params).then(function (data) {
                        window.location.reload();
                    });
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

        {/literal}
    </script>
{include file='JSResources.tpl'|@vtemplate_path MODULE=$MODULE}
{/strip}
