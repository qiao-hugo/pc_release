{strip}

    <table class="table table-bordered equalSplit detailview-table"><thead>

        <tr><th colspan="2">合同主体设置</th></tr></thead><tbody>

            <tr><td><label class="pull-right">合同主体</label></td>
                <td>
                    <label class="pull-left">
                        <input class="span9 dateField" type="text" name="invoicecompany" value="" style="width:100px;">
                    </label>
                </td>
            </tr>
            {*<tr><td><label class="pull-right">合同主体编码</label></td>
                <td>
                    <label class="pull-left">
                        <input class="span9 dateField" type="text" name="companycode" value="" style="width:100px;">
                    </label>
                </td>
            </tr>*}
            <tr><td><label class="pull-right">公司编码</label></td>
                <td>
                    <label class="pull-left">
                        <select id="company_code" class="chzn-select referenceModulesList streched">
                            {foreach key=index item=value from=$COMPANYCODE}
                                <option value="{$value['company_codeno']}">{$value['companyname']}</option>
                            {/foreach}
                        </select>
                    </label>
                </td>
            </tr>
            <tr><td style="text-align: center" colspan="2"><button class="btn btn-primary" id="savegonghaisetting">保存</button></td></tr>
        </tbody></table>

                    <table id="tbl_Detail1" class="table listViewEntriesTable" width="100%"><thead><tr>
                            <th nowrap><b>合同主体</b></th>
                            <th nowrap><b>编码</b></th>
                            <th nowrap><b>操作</b></th>
                        </tr>
                        </thead><tbody>
                        {foreach item=value from=$INVOICECOMPANY}

                            <tr>
                                <td nowrap><b>{$value['invoicecompany']}</b></td>
                                <td nowrap><b>{$value['companycode']}</b></td>
                                <td nowrap><b><i title="删除" class="icon-trash alignMiddle deleteRecord" data-id="{$value['invoicecompanyid']}" data-type="deletedInvoiceCompany" style="cursor:pointer"></i></b></td>
                            </tr>
                        {/foreach}
                        </tbody></table>


    <br>
    <br>
        <table class="table table-bordered equalSplit detailview-table"><thead>

        <tr><th colspan="2">合同管理人员设定</th></tr></thead><tbody>
            <tr><td><label class="pull-right">合同主体</label></td>
                <td>
                    <label class="pull-left">
                        <select id="companycode" class="chzn-select referenceModulesList streched">
                                {foreach key=index item=value from=$INVOICECOMPANY}
                                    <option value="{$value['companycode']}">{$value['invoicecompany']}</option>
                                {/foreach}
                            </select>
                    </label>
                </td>
            </tr>
            <tr><td><label class="pull-right">模块</label></td>
                <td>
                    <label class="pull-left">
                        <select id="modulename" class="chzn-select referenceModulesList streched">
                            <option value="ht">合同</option>
                            <option value="fp">发票</option>
                            <option value="gs">审核公司</option>
                        </select>
                    </label>
                </td>
            </tr>
            <tr><td><label class="pull-right">人员</label></td>
                <td>
                    <label class="pull-left">
                    <select id="user_id" class="chzn-select streched" multiple>
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
                        <th nowrap><b>合同主体</b></th>
                        <th nowrap><b>人员</b></th>
                        <th nowrap><b>模块</b></th>
                        <th nowrap><b>操作</b></th>
                       </tr>
                        </thead><tbody>
                        {foreach item=value from=$LISTDUSER}

                        <tr>
                            <td nowrap><b>{$value['invoicecompany']}</b></td>
                            <td nowrap><b>{$value['last_name']}</b></td>
                            <td nowrap><b>{$value['modulename']}</b></td>
                            <td nowrap><b><i title="删除" class="icon-trash alignMiddle deleteRecord" data-id="{$value['invoicecompanyuserid']}" data-type="deletedInvoiceCompanyUser"  style="cursor:pointer"></i></b></td>
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
            $('#savegonghaisetting').click(function(){
                var params={};
                var module = app.getModuleName();

                var invoicecompany=$("input[name='invoicecompany']").val();
                var companycode=$("#company_code").val();
                params['action']='BasicAjax';
                params['module']=module;
                params['mode']='saveinvoicecompany';
                params['invoicecompany']=invoicecompany;
                params['companycode']=companycode;
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
            $('#savedepartuser').click(function(){
                var params={};
                var module = app.getModuleName();

                var user_id=$("#user_id").val();
                if(user_id==null){
                    Vtiger_Helper_Js.showPnotify({text :"人员必填",title :'信息必填'});
                    return;
                }
                var user_id=$("#user_id").val();
                var companycode=$("#companycode").val();
                params['action']='BasicAjax';
                params['module']=module;
                params['mode']='savecompanycodeuserid';
                params['companycode']=companycode;
                params['userid']=user_id;
                params['modulename']=$('#modulename').val();


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
