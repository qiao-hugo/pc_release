{strip}
    <br>
    <table class="table table-bordered equalSplit detailview-table"><thead>

        <tr><th colspan="2">财务运营审核设置</th></tr></thead><tbody>

        <tr><td><label class="pull-right"><span class="redColor">*</span>节点标识</label></td>
            <td>
                <label class="pull-left">
                    <input id="workflowstagesflag" readonly type="text" class="input-large" value="CWSH"/>
                </label>
            </td>
        </tr>
        <tr><td style="text-align: right"><span class="redColor">*</span>部门
            </td><td>
                <select id="department" name="department" class="chzn-select referenceModulesList streched" name="department">
                    {foreach key=index item=value from=$DEPARTMENT}
                        <option value="{$index}">{$value}</option>
                    {/foreach}
                </select>
            </td></tr>

        <tr><td><label class="pull-right"><span class="redColor">*</span>财务运营主管审核</label></td>
            <td>
                <label class="pull-left">
                    <select id="supervisor" class="chzn-select streched">
                        {foreach key=index item=value from=$USER}
                            <option value="{$value.id}">{$value.last_name}</option>
                        {/foreach}
                    </select>
                </label>
            </td>
        </tr>
        <tr><td><label class="pull-right"><span class="redColor">*</span>财务运营经理审核</label></td>
            <td>
                <label class="pull-left">
                    <select id="manager" class="chzn-select streched">
                        {foreach key=index item=value from=$USER}
                            <option value="{$value.id}">{$value.last_name}</option>
                        {/foreach}
                    </select>
                </label>
            </td>
        </tr>
        <tr><td style="text-align: center" colspan="2"><button class="btn btn-primary" id="savedepartuser">保存</button></td></tr>
        </tbody></table>
    <div style="margin-top:10px;">
        <div class="row-fluid span12" id="c">
            <div id="msg" style="height:20px;margin:0 auto;border:1px solid #ccc;border-bottom: none;padding-top:20px;"></div>
            <div style="border:1px solid #ccc;margin:0 auto 20px;padding-right:20px;">
                <div id="bartable1" class="span12" style="height:490px;cursor:pointer;">
                    <table id="tbl_Detail" class="table listViewEntriesTable" width="100%"><thead><tr>
                            <th nowrap><b>节点标识</b></th>
                            <th nowrap><b>部门</b></th>
                            <th nowrap><b>财务运营主管审核</b></th>
                            <th nowrap><b>财务运营经理审核</b></th>
                            <th nowrap><b>操作</b></th>
                        </tr>
                        </thead><tbody>
                        {foreach item=value from=$LISTDUSER}
                            <tr>
                                <td nowrap><b>CWSH</b></td>
                                <td nowrap><b>{$value['department']}</b></td>
                                <td nowrap><b>{$value['supervisor']}</b></td>
                                <td nowrap><b>{$value['manager']}</b></td>
                                <td nowrap><b><i title="删除" class="icon-trash alignMiddle deleteRecord" data-id="{$value['auditcwshid']}" style="cursor:pointer"></i></b></td>
                            </tr>
                        {/foreach}
                        </tbody></table>
                </div>
                <div class="clearfix"></div></div>
        </div>
    </div>

    </div>

{literal}
    <script>
        $(document).ready(function(){
            $('#savedepartuser').click(function(){
                var params={};
                var module = app.getModuleName();
                var department=$("#department").val();
                if(!department){
                    Vtiger_Helper_Js.showPnotify({text :"部门必填",title :'信息必填'});
                    return;
                }
                var supervisor=$("#supervisor").val();
                if(!supervisor){
                    Vtiger_Helper_Js.showPnotify({text :"财务运营主管审核",title :'信息必填'});
                    return;
                }
                var manager=$("#manager").val();
                if(!manager){
                    Vtiger_Helper_Js.showPnotify({text :"财务运营经理审核",title :'信息必填'});
                    return;
                }

                params['action']='BasicAjax';
                params['module']=module;
                params['mode']='saveCWSH';
                params['department']=department;
                params['supervisor']=supervisor;
                params['manager']=manager;
                var progressIndicatorElement = jQuery.progressIndicator({
                    'message' : '正在处理,请耐心等待哟',
                    'position' : 'html',
                    'blockInfo' : {'enabled' : true}
                });

                AppConnector.request(params).then(function (data){
                    progressIndicatorElement.progressIndicator({
                        'mode' : 'hide'
                    });
                    if(!data.result.flag){
                        Vtiger_Helper_Js.showPnotify({text :data.result.msg,title :'保存失败'});
                        return;
                    }else{
                        window.location.reload();
                    }
                });
            });
            $('.listViewEntriesTable').on('click','.deleteRecord',function(){
                var msg={
                    'message':'确定要删除吗'
                };
                var id=$(this).data("id");
                Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e) {
                    var params = {};
                    var module = app.getModuleName();
                    params['id'] =id;
                    params['mode']='deletedCWSH';
                    params['action'] = 'BasicAjax';
                    params['module'] = module;
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



        });
    </script>
{/literal}
    {include file='JSResources.tpl'|@vtemplate_path MODULE=$MODULE}
{/strip}
