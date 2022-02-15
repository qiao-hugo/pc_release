{strip}

    <table class="table table-bordered equalSplit detailview-table"><thead>
        <th colspan="2">设置权限</th></thead><tbody>

        <tr><td style="text-align: right">用户
            </td><td>
                <label class="pull-left">
                    <select id="user_id" name="user_id[]" class="chzn-select" >
                        {foreach key=index item=value from=$USER}
                            <option value="{$value.id}">{$value.last_name}</option>
                        {/foreach}
                    </select>
                </label>
            </td></tr>
        <tr><td style="text-align: right">导出部门
            </td><td>
                <select id="department" class="chzn-select referenceModulesList streched" name="department" multiple>
                    {foreach key=index item=value from=$DEPARTMENT}
                        <option value="{$index}">{$value}</option>
                    {/foreach}
                </select>
            </td></tr>
        <tr><td colspan="2" style="text-align: center"><button class="btn btn-primary" id="preview">保存</button></td></tr>
        </tbody></table>
    <div style="margin-top:10px;">
        <div class="row-fluid" id="c" style="width:100%;">
            <div style="border:1px solid #ccc;margin:0 auto 20px;padding-right:20px;padding-top:10px;">
                <div id="bartable1" class="span12" style="height:490px;">
                <table id="tbl_Detail" class="table listViewEntriesTable" width="100%"><thead><tr>
                        <th nowrap><b>用户</b></th>
                        <th nowrap><b>可导出部门</b></th>
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
                            <td nowrap><b><i title="删除" class="icon-trash alignMiddle deleteRecord" data-id="{$value['id']}" style="cursor:pointer"></i></a></b></td>
                        </tr>
                    {/foreach}
                </tbody></table>
                </div>
                <div class="clearfix"></div></div>
            </div>
        </div>
    </div>
    <script>
        {literal}
        $(function(){
            $('#preview').click(function(){
                var params={};
                var module = app.getModuleName();
                var userid=$("#user_id").val();
                var modulename=$("#modulename").val();
                var department=$("#department").val();
                var pdid=$("#pdid").val();
                var email=$("#email").val();
                var agentid=$("#agentid").val();
                params['department']=department;
                params['userid']=userid;
                params['modulename']=modulename;
                params['action']='BasicAjax';
                params['module']=module;
                params['mode']='addDepartment';
                params['pdid']=pdid;
                if(department==''){
                    alert("部门不能为空");
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
            $('body').on('click','.updateRecord',function(){
                var userids=$(this).data("userids");
                if(userids==''){
                    var useridsArr=[];
                }else {
                    userids=userids.toString();
                    var useridsArr=userids.split(',');
                }

                $("#user_id").val(useridsArr);
                $("#user_id").trigger("liszt:updated");
                $("#department").val($(this).data("departmentid"));
                $("#department").trigger("liszt:updated");
                $("#pdid").val($(this).data("id"));
                $("#email").val($(this).data("email"));
                $("#agentid").val($(this).data("agentid"));
                $("#pdepartment").text($(this).data("pdepartmentid"));

            });
            $('.listViewEntriesTable').on('click','.deleteRecord',function(){
                var msg={
                    'message':'确定要删除吗'

                };
                var id=$(this).data("id")
                Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e) {
                    var params = {};
                    var module = app.getModuleName();
                    params['did'] =id;
                    params['action'] = 'BasicAjax';
                    params['module'] = module;
                    params['mode'] = 'delDepartID';
                    AppConnector.request(params).then(function (data) {
                        window.location.reload();
                    });
                });
            });
            $('.updateAgentid').on('click',function(){
                var msg={
                    'message':'确定要更新吗'

                };
                Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e) {
                    var params = {};
                    var module = app.getModuleName();
                    params['action'] = 'BasicAjax';
                    params['module'] = module;
                    params['mode'] = 'updateAgentid';
                    AppConnector.request(params).then(function (data) {
                        window.location.reload();
                    });
                });
            });
            //$('.chzn-select').chosen();
        });



        {/literal}
    </script>
{/strip}
