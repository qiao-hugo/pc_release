{strip}

    <table class="table table-bordered equalSplit detailview-table"><thead>
        <th colspan="2">设置权限&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="updateAgentid" style="color: #08c;cursor: pointer;">更新代理商架构</span></th></thead><tbody>
        <tr><td style="text-align: right">部门
            </td><td>
                <select id="department" class="chzn-select referenceModulesList streched" name="department">
                    {foreach key=index item=value from=$DEPARTMENT}
                        <option value="{$index}">{$value}</option>
                    {/foreach}
                </select>
            </td></tr>
        <tr><td style="text-align: right">用户
            </td><td>
                <label class="pull-left">
                    <select id="user_id" name="user_id[]" class="chzn-select" multiple>
                        {foreach key=index item=value from=$USER}
                            <option value="{$value.id}">{$value.last_name}</option>
                        {/foreach}
                    </select>
                </label>
            </td></tr>
            <tr><td style="text-align: right"><span class="redColor">*</span>代理商ID
            </td><td>
                <label class="pull-left">
                    <input name="agentid" id="agentid" class="input-large nameField" type="text">
                </label>
            </td></tr>
        <tr><td style="text-align: right"><span class="redColor">*</span>负责人邮箱
            </td><td>
                <label class="pull-left">
                    <input name="email" id="email" class="input-large nameField" type="text">
                </label>
            </td></tr>
        <tr><td colspan="2" style="text-align: center"><button class="btn btn-primary" id="preview">保存</button></td></tr>
        </tbody></table>
    <div style="margin-top:10px;">
        <div class="row-fluid" id="c" style="width:100%;">
            <div style="border:1px solid #ccc;margin:0 auto 20px;padding-right:20px;padding-top:10px;">
                <div id="bartable1" class="span12" style="height:490px;">
                <table id="tbl_Detail" class="table listViewEntriesTable" width="100%"><thead><tr>
                        <th nowrap><b>部门</b></th>
                        <th nowrap><b>用户</b></th>
                        <th nowrap><b>代理商ID</b></th>
                        <th nowrap><b>负责人邮箱</b></th>
                        <th nowrap><b>操作</b></th>
                       </tr>
                        </thead><tbody>
                {foreach item=value from=$RECOEDS}
                    <tr>
                        <td nowrap><b>{$value['departmentname']}</b></td>
                        <td nowrap><b>{$value['useridsname']}
                        <td nowrap><b>{$value['agentid']}
                        <td nowrap><b>{$value['email']}
                        <td nowrap><b><i title="修改" class="icon-pencil alignMiddle updateRecord" data-id="{$value['departmentagentid']}" data-pdepartmentid="{$value['pdepartmentid']}" data-agentid="{$value['agentid']}" data-email="{$value['email']}" data-departmentid="{$value['departmentid']}" data-userids="{$value['userids']}" style="cursor:pointer"></i></a></b><b><i title="删除" class="icon-trash alignMiddle deleteRecord" data-id="{$value['departmentagentid']}" style="cursor:pointer"></i></a></b></td>
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
                params['mode']='addAgentid';
                params['pdid']=pdid;
                params['email']=email;
                params['agentid']=agentid;
                if(department==''){
                    alert("部门不能为空");
                    return;
                }
                if(agentid==0){
                    alert("代理商ID无效");
                    return;
                }
                if(email==''){
                    alert("邮箱不能为空");
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
                    params['mode'] = 'delAgentid';
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
