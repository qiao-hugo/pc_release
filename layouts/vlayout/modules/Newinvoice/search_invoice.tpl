{strip}

    <table class="table table-bordered equalSplit detailview-table"><thead>
    <form action="index.php?module=Newinvoice&view=List&filter=search_invoice_data" method="post" onsubmit="return _submit()">
        <th colspan="2"><h4>合同发票查询</h4></th></thead><tbody>
        
        <tr><td style="text-align: right">合同编号
            </td><td>
                <label class="pull-left">
                    <input type="text" name="contract_no">
                </label>
            </td>
        </tr>
        <tr><td style="text-align: right">签订日期
            </td><td>
                <label class="pull-left">
                    {assign var="signdate_end"   value=date('Y-m-d')} 
                    {assign var="signdate_start" value=date('Y-m-d', strtotime('-3 month'))} 
                    <input type="text" value="{$signdate_start}" id="signdate_start" name="signdate_start"> 到 <input type="text" id="signdate_end" name="signdate_end" value="{$signdate_end}">
                </label>
            </td>
        </tr>
        <tr><td style="text-align: right">签订人
            </td><td>
                <label class="pull-left">
                <select  name="signid" class="chzn-select referenceModulesList streched"">
                    <option value="">请选择一项</option>
                    {foreach key=index item=value from=$USER}
                        <option value="{$value.id}">{$value.last_name}</option>
                    {/foreach}
                </select>
                </label>
            </td>
        </tr>
        <tr><td style="text-align: right">提单人
            </td><td>
                <label class="pull-left">
                    <select  name="receiveid" class="chzn-select referenceModulesList streched"">
                        <option value="">请选择一项</option>
                        {foreach key=index item=value from=$USER}
                            <option value="{$value.id}">{$value.last_name}</option>
                        {/foreach}
                    </select>
                </label>
            </td>
        </tr>

        
        <tr><td colspan="2" style="text-align: center"><button class="btn btn-primary" id="preview">搜索</button></td></tr>
        </tbody>
    </form>
    </table>


    <div style="margin-top:10px;">
        <div class="row-fluid" id="c" style="width:100%;">
            <div style="border:1px solid #ccc;margin:0 auto 20px;padding-right:20px;padding-top:10px;">
                <div id="bartable1" class="span12" style="height:490px;">
                <table id="tbl_Detail" class="table listViewEntriesTable" width="100%"><thead><tr>
                        <th nowrap><b>合同编号</b></th>
                        <th nowrap><b>签订日期</b></th>
                        <th nowrap><b>签订人</b></th>
                        <th nowrap><b>提单人</b></th>

                        <th nowrap><b>合同金额</b></th>
                        <th nowrap><b>回款入账日期</b></th>
                        <th nowrap><b>入账账户</b></th>
                        <th nowrap><b>入账金额</b></th>
                        <th nowrap><b>已开票金额</b></th>
                        <th nowrap><b>剩余发票金额</b></th>

                        <th nowrap><b>操作</b></th>
                       </tr>
                        </thead><tbody>
                {foreach item=value from=$RES_DATA}
                    <tr>
                        <td nowrap><b>{$value['contract_no']}</b></td>
                        <td nowrap><b>{$value['signdate']}
                            </b></td>
                        <td nowrap><b>{$value['signid']}</b></td>
                        <td nowrap><b>{$value['receiveid']}</b></td>

                        <td nowrap><b>{$value['total']}</b></td>
                        <td nowrap><b>{$value['arrivaldate']}</b></td>
                        <td nowrap><b>{$value['paytitle']}</b></td>
                        <td nowrap><b>{$value['newinvoiceayment_totoal']}</b></td>
                        <td nowrap><b>{$value['invoicetotal']}</b></td>
                        <td nowrap><b>{$value['surpluinvoicetotal']}</b></td>


                        <td nowrap><b><a href="index.php?module=Newinvoice&view=Detail&record={$value['invoiceid']}" target="_blank"><i title="发票详情" class="icon-th-list alignMiddle" style="cursor:pointer"></i></a></b></td>
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
    <script type="text/javascript">
        
        $('#signdate_start, #signdate_end').datetimepicker({
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
    </script>
    <script>
        {literal}

        function _submit() {
            //var contract_no = $('input[name=contract_no]').val();
            var signdate_start = $('#signdate_start').val();
            var signdate_end = $('#signdate_end').val();
            //alert(signdate_start); alert(signdate_end);
            if( Date.parse(new Date(signdate_start)) > Date.parse(new Date(signdate_end)) ) {
                alert('签订开始日期不能大于签订结束日期');
            }
            return true;
        }

        $(function(){
            

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
/*        $('#modulename').on('change',function(){
            var modename=$(this).val();
             $('#classname').empty();
             $('#classname').append(contractoption[modename]);
             $('#classname').trigger("liszt:updated");


            $('.chzn-select').chosen();
        });
        $('#classname').append(contractoption.ServiceContracts);*/
        $('.chzn-select').chosen();


        {/literal}
    </script>
{include file='JSResources.tpl'|@vtemplate_path MODULE=$MODULE}
{/strip}