{strip}

    <table class="table table-bordered equalSplit detailview-table"><thead>

        <tr><th colspan="2">客保角色设置</th></tr></thead><tbody>
            <tr><td><label class="pull-right">角色</label></td>
                <td>
                    <label class="pull-left">
                        <select id="roleid" class="chzn-select referenceModulesList streched">
                            {foreach key=index item=value from=$ROLES}
                                <option value="{$value['roleid']}">{$value['rolename']}</option>
                            {/foreach}
                        </select>
                    </label>
                </td>
            </tr>
            <tr><td style="text-align: center" colspan="2"><button class="btn btn-primary" id="savegonghaisetting">保存</button></td></tr>
        </tbody></table>

                    <table id="tbl_Detail1" class="table listViewEntriesTable" width="100%"><thead><tr>
                            <th nowrap><b>名称</b></th>
                            <th nowrap><b>编码</b></th>
                            <th nowrap><b>操作</b></th>
                        </tr>
                        </thead><tbody>
                        {foreach item=value from=$ACCOUNTROLES}

                            <tr>
                                <td nowrap><b>{$value['rolename']}</b></td>
                                <td nowrap><b>{$value['roleid']}</b></td>
                                <td nowrap><b><i title="删除" class="icon-trash alignMiddle deleteRecord" data-id="{$value['roleid']}" data-type="deletedInvoiceCompany" style="cursor:pointer"></i></b></td>
                            </tr>
                        {/foreach}
                        </tbody></table>
    <br>

    {literal}
    <script>
       $(document).ready(function(){
            $('#savegonghaisetting').click(function(){
                var params={};
                var module = app.getModuleName();
                var roleid=$("#roleid").val();

                params['action']='BasicAjax';
                params['module']=module;
                params['mode']='saveaccountroleid';
                params['roleid']=roleid;

                var progressIndicatorElement = jQuery.progressIndicator({
                    'message' : '正在处理,请耐心等待哟',
                    'position' : 'html',
                    'blockInfo' : {'enabled' : true}
                });
                AppConnector.request(params).then(function (data){
                        progressIndicatorElement.progressIndicator({
                            'mode' : 'hide'
                        });
                        window.location.reload();
                });
            });
            $('.listViewEntriesTable').on('click','.deleteRecord',function(){
                var msg={
                    'message':'确定要删除该角色吗'

                };
                var id=$(this).data("id");
                Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e) {
                    var params = {};
                    var module = app.getModuleName();
                    params['roleid'] =id;
                    params['action'] = 'BasicAjax';
                    params['module'] = module;
                    params['mode'] ='deleteRole';
                    AppConnector.request(params).then(function (data) {
                        window.location.reload();
                    });
                });
            });
            jQuery('#tbl_Detail').DataTable({
                language: {"sProcessing":   "处理中...",	"sLengthMenu":   "显示 _MENU_ 项结果","sZeroRecords":  "没有匹配结果","sInfo":         "显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",	"sInfoEmpty":    "显示第 0 至 0 项结果，共 0 项","sInfoFiltered": "(由 _MAX_ 项结果过滤)","sInfoPostFix":  "","sSearch":"当前页快速检索:","sUrl":"","sEmptyTable":     "表中数据为空","sLoadingRecords": "载入中...",
                    "sInfoThousands":  ",",	"oPaginate": {"sFirst":"首页","sPrevious": "上页","sNext":"下页","sLast":"末页"},
                    "oAria": {"sSortAscending":  ": 以升序排列此列","sSortDescending": ": 以降序排列此列"}},
                scrollY:"400px",
                sScrollX:"disabled",
                //paging: false,
                //searching: false,
                aLengthMenu: [ 10, 20, 50, 100, ],
                fnDrawCallback:function(){
                }
            });
           jQuery('#tbl_Detail1').DataTable({
               language: {"sProcessing":   "处理中...",	"sLengthMenu":   "显示 _MENU_ 项结果","sZeroRecords":  "没有匹配结果","sInfo":         "显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",	"sInfoEmpty":    "显示第 0 至 0 项结果，共 0 项","sInfoFiltered": "(由 _MAX_ 项结果过滤)","sInfoPostFix":  "","sSearch":"当前页快速检索:","sUrl":"","sEmptyTable":     "表中数据为空","sLoadingRecords": "载入中...",
                   "sInfoThousands":  ",",	"oPaginate": {"sFirst":"首页","sPrevious": "上页","sNext":"下页","sLast":"末页"},
                   "oAria": {"sSortAscending":  ": 以升序排列此列","sSortDescending": ": 以降序排列此列"}},
               scrollY:"400px",
               sScrollX:"disabled",
               //paging: false,
               //searching: false,
               aLengthMenu: [ 10, 20, 50, 100, ],
               fnDrawCallback:function(){
               }
           });



       });
    </script>
    {/literal}
{include file='JSResources.tpl'|@vtemplate_path MODULE=$MODULE}
{/strip}
