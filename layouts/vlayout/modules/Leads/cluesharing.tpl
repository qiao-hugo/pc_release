{strip}
    <input type="hidden" name="record" value="">
    <table class="table table-bordered equalSplit detailview-table"><thead>
        <th colspan="2">商机分成设置</th></thead><tbody>
        <tr><td style="text-align: right"><span class="redColor">*</span>分成方式
            </td><td>
                <select id="department_editView_fieldName_dropDown" class="chzn-select referenceModulesList streched" name="sharetype">
                    <option value="sharing">按比例</option>
                </select>
            </td></tr>
        <tr><td style="text-align: right"><span class="redColor">*</span>推广部分成(%)
            </td><td>
                <input type="text" onkeyup="num(this)" max="100" name="promotionsharing" value="">

            </td></tr>
        <tr><td style="text-align: right"><span class="redColor">*</span>商务分成(%)
            </td><td>
                <input type="text" name="salesharing" readonly value="">
            </td></tr>
        <tr><td style="text-align: right"><span class="redColor">*</span>启用时间
            </td><td>
                <label class="pull-left">
                    <input class="span9 dateField"type="text" name="datatime" id="datatime" value="{date("Y-m-d H:i")}" readonly style="width:150px;">
                </label>
            </td></tr>
        <tr><td style="text-align: right">备注
            </td><td>
                <label class="pull-left">
                    <textarea name="remark"></textarea>
                </label>
            </td></tr>
        <tr><td colspan="2" style="text-align: center"><button id="preview" class="btn btn-primary">保存</button></td></tr>
        </tbody></table>
    <div style="margin-top:10px;">
        <div class="row-fluid" id="c" style="width:100%;">
            <div style="border:1px solid #ccc;margin:0 auto 20px;padding-right:20px;padding-top:10px;">
                <div id="bartable1" class="span12" style="height:490px;">
                    <table id="tbl_Detail" class="table listViewEntriesTable" width="100%"><thead><tr>
                            <th nowrap><b>分成方式</b></th>
                            <th nowrap><b>推广部分成(%)</b></th>
                            <th nowrap><b>商务分成(%)</b></th>
                            <th nowrap><b>启用时间</b></th>
                            <th nowrap><b>备注</b></th>
                            <th nowrap><b>创建人</b></th>
                            <th nowrap><b>创建时间</b></th>
                            <th nowrap><b>操作</b></th>
                        </tr>
                        </thead><tbody>
                        {foreach item=value from=$DATAS}
                            <tr>
                                <td nowrap><b>{$value['sharetypelng']}</b></td>
                                <td nowrap><b>{$value['promotionsharing']} </b></td>
                                <td nowrap><b>{$value['salesharing']}</b></td>
                                <td nowrap><b>{$value['starttime']}</b></td>
                                <td nowrap><b>{$value['remark']}</b></td>
                                <td nowrap><b>{$value['last_name']}</b></td>
                                <td nowrap><b>{$value['createdtime']}</b></td>
                                <td nowrap>
                                    {if time()<strtotime($value['starttime'])}
                                    <b><i title="修改" class="icon-pencil alignMiddle modifyRecord"   data-sharetype="{$value['sharetype']}"  data-starttime="{$value['starttime']}"  data-salesharing="{$value['salesharing']}"  data-promotionsharing="{$value['promotionsharing']}"  data-remark="{$value['remark']}" data-id="{$value['leadsharesettingid']}" style="cursor:pointer"></i>
                                        <i title="删除" class="icon-trash alignMiddle deleteRecord" data-id="{$value['leadsharesettingid']}" style="cursor:pointer"></i></b>
                                    {/if}
                                </td>
                            </tr>
                        {/foreach}
                        </tbody></table>
                </div>
                <div class="clearfix"></div></div>
        </div>
    </div>

    <script src="/libraries/jquery/chosen/chosen.jquery.min.js"></script>
    <script src="/libraries/jquery/datetimepicker/bootstrap-datetimepicker.min.js"></script>
    {literal}
    <script>
        $('.chzn-select').chosen();
        $('#datatime').datetimepicker({
            format: "yyyy-mm-dd hh:ii",
            language:  'zh-CN',
            autoclose: true,
            todayBtn: true,
            pickerPosition: "bottom-right",
            showMeridian: 0,
            {/literal}
            {*startDate:'{date("Y-m-d h:i")}',*}
            {literal}
            startDate:new Date(),
            // endDate:new Date(),
            weekStart:1,
            todayHighlight:1,
            startView:2,
            minView:0,
            forceParse:0
        });
        function num(obj){
            obj.value = obj.value.replace(/[^\d.]/g,""); //清除"数字"和"."以外的字符
            obj.value = obj.value.replace(/^\./g,""); //验证第一个字符是数字
            obj.value = obj.value.replace(/\.{2,}/g,"."); //只保留第一个, 清除多余的
            obj.value = obj.value.replace(".","$#$").replace(/\./g,"").replace("$#$",".");
            obj.value = obj.value.replace(/^(\-)*(\d+)\.(\d\d).*$/,'$1$2.$3'); //只能输入两个小数
            console.log(obj.value);
            if(obj.value>100){
                obj.value=100;
            }
        }

        $(function(){
            $('#preview').click(function(){
                var promotionsharing = $("input[name='promotionsharing']").val();
                var salesharing = $("input[name='salesharing']").val();
                var starttime = $("input[name='datatime']").val();
                var sharetype = $("select[name='sharetype']").val();
                var remark = $("textarea[name='remark']").val();
                var record = $("input[name='record']").val();

                if(!salesharing){
                    alert("请填写推广部分成");
                    return;
                }
                if(!starttime){
                    alert("请选择启用时间");
                    return;
                }
                var params = {};
                var module = app.getModuleName();
                params['promotionsharing'] = promotionsharing;
                params['salesharing'] = salesharing;
                params['starttime'] = starttime;
                params['sharetype'] = sharetype;
                params['remark'] = remark;
                params['action'] = 'BasicAjax';
                params['module'] = module;
                params['mode'] = 'setShare';
                params['record'] = record;
                var progressIndicatorElement = jQuery.progressIndicator({
                    'message' : '正在处理,请耐心等待哟',
                    'position' : 'html',
                    'blockInfo' : {'enabled' : true}
                });
                AppConnector.request(params).then(function(data){
                    progressIndicatorElement.progressIndicator({
                        'mode' : 'hide'
                    });
                    if (data.success) {
                        alert(data.msg);
                        $("input[name='record']").val('')
                        window.location.reload();
                    } else {
                        alert(data.msg);
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

            $('.listViewEntriesTable').on('click','.modifyRecord',function(){
                var id = $(this).data("id");
                $("input[name='record']").val(id);
                $("input[name='promotionsharing']").val($(this).data('promotionsharing'));
                $("input[name='salesharing']").val($(this).data('salesharing'));
                $("select[name='sharetype']").val($(this).data('sharetype'));
                $("textarea[name='remark']").val($(this).data('remark'));
                $("input[name='datatime']").val($(this).data('starttime'));
            });

            $('.listViewEntriesTable').on('click','.deleteRecord',function(){
                var msg={
                    'message':'确定要删除该线索分成吗'
                };
                var id=$(this).data("id");
                Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e) {
                    var params = {};
                    var module = app.getModuleName();
                    params['id'] =id;
                    params['action'] = 'BasicAjax';
                    params['module'] = module;
                    params['mode'] = 'deleteShareSetting';

                    AppConnector.request(params).then(function (data) {
                        window.location.reload();
                    });
                });
            });

        });
        {/literal}
    </script>
    <script>

        $(function () {
            $("input[name='promotionsharing']").on("input blur",function () {
                var promotionsharing=$("input[name='promotionsharing']").val();
                var salesharing=0;
                if(promotionsharing<100){
                    salesharing=100-promotionsharing
                }
                $("input[name='salesharing']").val(salesharing.toFixed(2));
            })
        })
    </script>
{include file='JSResources.tpl'|@vtemplate_path MODULE=$MODULE}
{/strip}
