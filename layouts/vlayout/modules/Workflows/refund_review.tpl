{strip}
    <br>
        <table class="table table-bordered equalSplit detailview-table"><thead>
        <tr><th colspan="2">财务主管复核人员设定</th></tr></thead><tbody>
            <tr><td><label class="pull-right">节点标识</label></td>
                <td>
                    <label class="pull-left">
                        <input id="workflowstagesflag" type="text" class="input-large" value="REFUND_REVIEW" disabled="disabled"/>
                    </label>
                </td>
            </tr>
            <tr><td><label class="pull-right">人员</label></td>
                <td>
                    <label class="pull-left">
                    <select id="user_id" class="chzn-select streched">
                        {foreach key=index item=value from=$USER}
                                <option value="{$value.id}">{$value.last_name}</option>
                            {/foreach}
                        </select>
                    </label>
                </td>
            </tr>
            <tr><td><label class="pull-right">主体公司</label></td>
                <td>
                    <label class="pull-left">
                        <select id="companycode" class="chzn-select referenceModulesList streched" multiple>
                            {foreach key=index item=value from=$INVOICECOMPANY}
                                <option value="{$value['companycode']}">{$value['invoicecompany']}</option>
                            {/foreach}
                        </select>
                    </label>
                </td>
            </tr>
            <tr><td style="text-align: center" colspan="2"><button class="btn btn-primary" id="savedepartuser">保存</button></td></tr>
        </tbody></table>
    <div style="margin-top:10px;">
        <div class="row-fluid span12" id="c">
            <div style="border:1px solid #ccc;margin:0 auto 20px;padding: 10px 10px 0;">
                <div id="bartable1" class="span12" style="height:530px;">
                    <table id="tbl_Detail" class="table" style="border:none;">
                        <thead><tr>
                        <th nowrap><b>主体公司</b></th>
                        <th nowrap><b>人员</b></th>
                        <th nowrap><b>节点标识</b></th>
                        <th nowrap><b>操作</b></th>
                       </tr>
                        </thead><tbody>
                        {foreach item=value from=$LISTDUSER}
                        <tr>
                            <td nowrap><b>{$value['invoicecompany']}</b></td>
                            <td nowrap><b>{$value['last_name']}</b></td>
                            <td nowrap><b>{$value['workflowstagesflag']}</b></td>
                            <td nowrap><b><i title="删除" class="icon-trash alignMiddle deleteRecord" data-id="{$value['auditinvoicecompanyid']}" data-type="deletedInvoiceCompanyUser"  style="cursor:pointer"></i></b></td>
                        </tr>
                        {/foreach}
                    </tbody>
                    </table>
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

                var user_id=$("#user_id").val();
                var workflowstagesflag=$('#workflowstagesflag').val();
                if(workflowstagesflag==''){
                    Vtiger_Helper_Js.showPnotify({text :"节点标识必填",title :'信息必填'});
                    return;
                }
                if(user_id==''){
                    Vtiger_Helper_Js.showPnotify({text :"人员必填",title :'信息必填'});
                    return;
                }
                var companycode=$("#companycode").val();
                if(companycode==null){
                    Vtiger_Helper_Js.showPnotify({text :"主体公司必填",title :'信息必填'});
                    return;
                }
                params['action']='BasicAjax';
                params['module']=module;
                params['mode']='savecompanycodeuserid';
                params['companycode']=companycode;
                params['userid']=user_id;
                params['workflowstagesflag']=workflowstagesflag;
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
            $('#tbl_Detail').on('click','.deleteRecord',function(){
                var msg={
                    'message':'确定要删除该用户的权限吗'

                };
                var id=$(this).data("id");
                var typename=$(this).data("type");
                Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e) {
                    var params = {};
                    var module = app.getModuleName();
                    params['id'] =id;
                    params['action'] = 'BasicAjax';
                    params['module'] = module;
                    params['mode'] = typename;
                    params['id'] =id;
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
               aLengthMenu: [ 10, 20, 50, 100],
               fnDrawCallback:function(){
               }
           });
       });
    </script>
    {/literal}
{include file='JSResources.tpl'|@vtemplate_path MODULE=$MODULE}
{/strip}
