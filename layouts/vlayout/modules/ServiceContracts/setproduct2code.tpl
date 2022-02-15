{strip}

    <table class="table table-bordered equalSplit detailview-table"><thead>
        {*<form action="" method="post">*}
            <th colspan="2"><h4>设置产品合同编码对应表</h4></th></thead><tbody>
        <tr><td style="text-align: right"><span class="redColor">*</span>产品合同编码
            </td><td>
                <select id="products_codeid" class="chzn-select referenceModulesList streched" name="products_codeid">
                    {foreach key=index item=value from=$PRODUCTCODE}
                        <option value="{$value['products_codeid']}">{$value['products_code']}</option>
                    {/foreach}
                </select>
            </td></tr>
        <tr><td style="text-align: right"><span class="redColor">*</span>产品名称
            </td><td>
                <select id="product" name="product" class="chzn-select referenceModulesList streched">
                    <option value="">请选择</option>
                    {foreach key=index item=value from=$PRODUCTS}
                        <option value="{$value['productCode']}" data-id="{$value['id']}" data-ispackage="{$value['ispackage']}">{$value['productName']}</option>
                    {/foreach}
                </select>
            </td></tr>


        <tr><td style="text-align: right"><span class="redColor">*</span>合同类型
            </td><td>
                <label class="pull-left">
                    <select id="servicecontractstype" name="servicecontractstype" class="chzn-select referenceModulesList streched"">
                    <option value="all">全部</option>
                    <option value="buy">新增</option>
                    <option value="renew">续费</option>
                    <option value="upgrade">升级</option>
                    <option value="degrade">降级</option>
                    </select>
                </label>
            </td></tr>


        {*</form>*}
        <tr><td colspan="2" style="text-align: center"><button class="btn btn-primary" id="preview">添加</button></td></tr>
        </tbody></table>


    <div style="margin-top:10px;">
        <div class="row-fluid" id="c" style="width:100%;">
            <div style="border:1px solid #ccc;margin:0 auto 20px;padding-right:20px;padding-top:10px;">
                <div id="bartable1" class="span12" style="height:490px;">
                    <table id="tbl_Detail" class="table listViewEntriesTable" width="100%"><thead><tr>
                            <th nowrap><b>产品名称</b></th>
                            <th nowrap><b>合同类型</b></th>
                            <th nowrap><b>产品合同编码</b></th>
                            <th nowrap><b>操作</b></th>
                        </tr>
                        </thead><tbody>
                        {foreach item=value from=$RECOEDS}
                            <tr>
                                <td nowrap><b>{$value['productname']}</b></td>
                                <td nowrap><b>{$TYPETEXT[$value['servicecontractstype']]} </b></td>
                                <td nowrap><b>{$value['products_code']}</b></td>
                                <td nowrap><b><i title="删除" class="icon-trash alignMiddle deleteRecord" data-id="{$value['products_code_productidid']}" style="cursor:pointer"></i></a></b></td>
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
                var products_codeid = $("#products_codeid").val();
                var productname = $("#product").find("option:selected").text();
                var servicecontractstype = $("#servicecontractstype").val();
                var productid = $("#product").val();
                var productidcode = $("#product").find('option:selected').data('id');
                var ispackage = $("#product").find('option:selected').data('ispackage');
                console.log(productname);
                console.log(productid);
                if(!products_codeid){
                    alert("请选择产品合同编码");
                    return;
                }
                if(!productid){
                    alert("请选择产品");
                    return;
                }
                if(!servicecontractstype){
                    alert("请选择合同类型");
                    return;
                }

                var params = {};
                var module = app.getModuleName();
                params['products_codeid'] = products_codeid;
                params['productname'] = productname;
                params['servicecontractstype'] = servicecontractstype;
                params['productid'] = productid;
                params['productidcode'] = productidcode;
                params['ispackage'] = ispackage;
                params['action'] = 'BasicAjax';
                params['module'] = module;
                params['mode'] = 'addProduct2Code';

                var progressIndicatorElement = jQuery.progressIndicator({
                    'message' : '正在请求',
                    'position' : 'html',
                    'blockInfo' : {'enabled' : true}
                });

                AppConnector.request(params).then(function(data){
                    progressIndicatorElement.progressIndicator({
                        'mode' : 'hide'
                    });
                    if (data.result.flag == '1') {
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
                    'message':'确定要删除该合同编码和产品对应关系吗'
                };
                var id=$(this).data("id")


                Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e) {
                    var params = {};
                    var module = app.getModuleName();
                    params['id'] =id;
                    params['action'] = 'BasicAjax';
                    params['module'] = module;
                    params['mode'] = 'deletedProduct2Code';

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

        });

        {/literal}
    </script>
    {include file='JSResources.tpl'|@vtemplate_path MODULE=$MODULE}
{/strip}