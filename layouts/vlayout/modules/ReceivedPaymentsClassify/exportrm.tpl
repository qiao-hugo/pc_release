{strip}

    <table class="table table-bordered equalSplit detailview-table"><thead>
        <form action="" method="post">
            <th colspan="2">查询权限配置</th></thead><tbody>

        <tr><td style="text-align: right"><span class="redColor">*</span>用户
            </td><td>
                <label class="pull-left">
                    <select id="user_id" class="chzn-select referenceModulesList streched" multiple="multiple">
                    <option value="">请选择一项</option>
                    {foreach key=index item=value from=$USER}
                        <option value="{$value.id}">{$value.last_name}</option>
                    {/foreach}
                    </select>
                </label>
            </td></tr>
        <tr><td style="text-align: right"><span class="redColor">*</span>二级分类
            </td><td>
                <label class="pull-left">
                    <select id="artificialclassications" class="chzn-select referenceModulesList streched" multiple="multiple">
                        {foreach from=$ARTIFICIALCLASSFICATIONS item=ARTIFICIALCLASSFICATION key=ckey}
                            <optgroup label="{$ckey}">
                                {foreach key=ckey1 item=ARTIFICIALCLASSFICATIONITEM from=$ARTIFICIALCLASSFICATION}
                                    <option {if $ARTIFICIALCLASSFICATIONKEY==$ckey1}selected{/if} value="{$ckey1}">{$ARTIFICIALCLASSFICATIONITEM}</option>
                                {/foreach}
                            </optgroup>
                        {/foreach}
                    </select>
                </label>
                <span class="pull-left">&nbsp;</span>
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
                            <th nowrap><b>一级分类</b></th>
                            <th nowrap><b>二级分类</b></th>
                            <th nowrap><b>操作</b></th>
                        </tr>
                        </thead><tbody>
                        {foreach item=value from=$RECOEDS}
                            <tr>
                                <td nowrap><b>{$value['last_name']}</b></td>
                                <td nowrap><b>{$value['parent']}</b></td>
                                <td nowrap><b>{$value['name']}</b></td>
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
        {literal}
        $(function(){
            $('#preview').click(function(){
                var params={};
                var module = app.getModuleName();
                var userids=$("#user_id").val();
                var artificialclassications=$("#artificialclassications").val();
                params['userids']=userids;
                params['artificialclassications']=artificialclassications;
                params['action']='BasicAjax';
                params['module']=module;
                params['mode']='add';

                if(userids.length==0){
                    alert("用户不能为空");
                    return;
                }

                if(artificialclassications.length==0){
                    alert("二级分类不能为空");
                    return;
                }
                AppConnector.request(params).then(function(data){
                    console.log(data);
                    if(data.result.success==false){
                        alert(data.result.msg);
                    }else{
                        window.location.reload();
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
        {/literal}
    </script>
    {include file='JSResources.tpl'|@vtemplate_path MODULE=$MODULE}
{/strip}
