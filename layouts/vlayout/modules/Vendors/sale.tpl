{strip}
    <style type="text/css">
        .form_input, .chzn-container-single{
            position: relative;
            top: 6px;
        }
    </style>
    <form action="index.php?module=Vendors&view=List&public=R_sale" method="post" onsubmit="return sub();">
    <table class="table table-bordered equalSplit detailview-table"><thead>
        <th colspan="2">供应商报表</th></thead><tbody>
        {*<tr><td style="text-align: right">部门
            </td><td>
                <select id="department_editView_fieldName_dropDown" class="chzn-select referenceModulesList streched" name="department">
                    {foreach key=index item=value from=$DEPARTMENT}
                        <option value="{$index}">{$value}</option>
                    {/foreach}
                </select>
            </td></tr>*}

        <tr><td style="text-align: right"><span class="redColor"></span>产品
            </td><td>
                <label class="pull-left">
                    <select class="form_input chzn-select" name="productid" value="" > 
                        <option >请选择</option>
                        {foreach from=$PRODUCTDATA item=value}
                            <option {if $productid eq $value['productid']}selected{/if} value="{$value['productid']}">{$value['productname']}</option>
                        {/foreach}
                    </select>
                </label>
            </td></tr>
            <tr><td style="text-align: right"><span class="redColor"></span>生效日期
            </td><td>
                <label class="pull-left">
                    <input class="form_input input_date" type="text" name="effectdate_start" value="{$effectdate_start}">
                    &nbsp;&nbsp;到&nbsp;&nbsp;
                    <input class="form_input input_date" type="text" name="effectdate_end" value="{$effectdate_end}">
                </label>
                <span class="pull-left">&nbsp;</span>
            </td></tr>
            <tr><td style="text-align: right"><span class="redColor"></span>失效日期
            </td><td>
                <label class="pull-left">
                    <input class="form_input input_date" type="text" name="enddate_start" value="{$enddate_start}">
                    &nbsp;&nbsp;到&nbsp;&nbsp;
                    <input class="form_input input_date" type="text" name="enddate_end" value="{$enddate_end}">
                </label>
                <span class="pull-left">&nbsp;</span>
            </td></tr>
            

        
        <tr><td colspan="2" style="text-align: center"><button class="btn btn-primary" id="preview">查询</button></td></tr>
        </tbody>
        </table>
        </form>
    <div style="margin-top:10px;">
        <div class="row-fluid" id="c" style="width:100%;">
            <div style="border:1px solid #ccc;margin:0 auto 20px;padding-right:20px;padding-top:10px;">
                <div id="bartable1" class="span12" style="height:490px;">
                <table id="tbl_Detail" class="table listViewEntriesTable" width="100%"><thead><tr>
                        <th nowrap><b>供应商名称</b></th>
                        <th nowrap><b>地址</b></th>
                        <th nowrap><b>主要联系人</b></th>
                        <th nowrap><b>联系电话</b></th>
                        <th nowrap><b>合作业务类型</b></th>
                        <th nowrap><b>已合作项目</b></th>
                        <th nowrap><b>外包金额累计</b></th>
                        <th nowrap><b>内部主管部门</b></th>
                        <th nowrap><b>内部负责人</b></th>
                       </tr>
                        </thead><tbody>
                {foreach item=value from=$VEBDORDATA}
                    <tr>
                        <td nowrap><b>{$value['vendorname']}</b></td>
                        <td nowrap><b>{$value['address']}</b></td>
                        <td nowrap><b>{$value['linkman']}
                        <td nowrap><b>{$value['linkphone']}</b></td>
                        <td nowrap><b>{vtranslate($value['vendortype'], 'Vendors')}</b></td>
                        <td nowrap><b>{$value['contract_no']}</b></td>
                        <td nowrap><b>{$value['purchasemount']}</b></td>
                        <td nowrap><b>{$value['last_name']}</b></td>
                        <td nowrap><b>{$value['departmentname']}</b></td>
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
        function sub() {
            var effectdate_start = $('input[name=effectdate_start]').val();
            var effectdate_end = $('input[name=effectdate_end]').val();
            var enddate_start = $('input[name=enddate_start]').val();
            var enddate_end = $('input[name=enddate_end]').val();

            if (effectdate_start && effectdate_end) {
                var effectdate_start_timestamp = Date.parse(new Date(effectdate_start));
                var effectdate_end_timestamp = Date.parse(new Date(effectdate_end));
                if(effectdate_end_timestamp < effectdate_start_timestamp) {
                    alert('生效开始时间不能大于生效结束时间');
                    return false;
                }
            }

            if(enddate_start && enddate_end) {
                var enddate_start_timestamp = Date.parse(new Date(enddate_start));
                var enddate_end_timestamp = Date.parse(new Date(enddate_end));
                if(enddate_end_timestamp < enddate_start_timestamp) {
                    alert('失效开始时间不能大于失效结束时间');
                    return false;
                }
            }

            return true;
        }

        {literal}
        $(function(){
            /*$('#preview').click(function(){
                var params={};
                var module = app.getModuleName();
                params['classname']=classname;
                params['userid']=userid;
                params['modulename']=modulename;
                params['action']='BasicAjax';
                params['module']=module;
                params['mode']='add';

                if(userid==''){
                    alert("用户不能为空");
                    return;
                }

                if(modulename==null&&typeof(modulename)!='undefined'){
                    alert("模块必选");
                    return;
                }
                if(classname==null&&typeof(classname)!='undefined'){
                    alert("可导出项必选");
                    return;
                }
                AppConnector.request(params).then(function(data){
                   window.location.reload();
                });
            });*/


            $('.input_date').datetimepicker({
                format: "yyyy-mm-dd",
                language:  'zh-CN',
                autoclose: true,
                autoclose:1,
                todayHighlight:1,
                startView:2,
                minView:2,
                forceParse:0,
                pickerPosition: "bottom-left",
                showMeridian: 0
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


        });
        /*$('#modulename').on('change',function(){
            var modename=$(this).val();
             $('#classname').empty();
             $('#classname').append(contractoption[modename]);
             $('#classname').trigger("liszt:updated");


            $('.chzn-select').chosen();
        });
        $('#classname').append(contractoption.ServiceContracts);
        $('.chzn-select').chosen();*/


        {/literal}
    </script>
{include file='JSResources.tpl'|@vtemplate_path MODULE=$MODULE}
{/strip}
