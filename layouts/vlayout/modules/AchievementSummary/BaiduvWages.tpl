{strip}
    <br>
    <table class="table table-bordered equalSplit detailview-table"><thead>

        <tr><th colspan="2">百度V工资提成设置</th></tr></thead><tbody>
        <tr>
            <td style="text-align: right"><span class="redColor">*</span>用户</td>
            <td>
                <label class="pull-left">
                    <select id="userid" name="userid" class="chzn-select" >
                        {foreach key=index item=value from=$USER}
                            <option value="{$value.id}">{$value.last_name}</option>
                        {/foreach}
                    </select>
                </label>
            </td>
        </tr>
        <tr>
            <td style="text-align: right"><span class="redColor">*</span>设置月份</td>
            <td>
                <label class="pull-left">
                    <input class="span9 dateField"  type="text" name="setmonth" data-date-format="yyyy-mm" id="setmonth" value="{date("Y-m")}" style="width:100px;">
                </label>
            </td>
        </tr>
        <tr>
            <td><label class="pull-right"><span class="redColor">*</span>工资</label></td>
            <td>
                <label class="pull-left">
                    <input id="staffwages"  type="number" step="0.01" min="0.01"  class="input-large" />
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
                            <th nowrap><b>设置月份</b></th>
                            <th nowrap><b>用户</b></th>
                            <th nowrap><b>部门</b></th>
                            <th nowrap><b>工资</b></th>
                            <th nowrap><b>操作</b></th>
                        </tr>
                        </thead><tbody>
                        {foreach item=value from=$BAIDUVWAGESLIST}
                            <tr>
                                <td nowrap><b>{$value['setmonth']}</b></td>
                                <td nowrap><b>{$value['last_name']}</b></td>
                                <td nowrap><b>{$value['departmentname']}</b></td>
                                <td nowrap><b>{$value['staffwages']}</b></td>
                                <td nowrap><b><i title="删除" class="icon-trash alignMiddle deleteRecord" data-id="{$value['baiduvstaffwagesid']}" style="cursor:pointer"></i></b></td>
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
            $('#setmonth').datetimepicker({
                language:'zh-CN',
                format: 'yyyy-mm',
                autoclose: true,
                todayBtn: true,
                todayHighlight: true,
                startView: 'year',
                minView:'year',
                maxView:'year',
            });

            $('#savedepartuser').click(function(){
                var params={};
                var userid=$("#userid").val();
                if(!userid){
                    Vtiger_Helper_Js.showPnotify({text :"用户必填",title :'信息必填'});
                    return;
                }
                var setmonth=$("#setmonth").val();
                if(!setmonth){
                    Vtiger_Helper_Js.showPnotify({text :"设置月份",title :'信息必填'});
                    return;
                }
                var staffwages=$("#staffwages").val();
                if(!staffwages){
                    Vtiger_Helper_Js.showPnotify({text :"工资",title :'信息必填'});
                    return;
                }
                
                params['action']='BasicAjax';
                params['module']='AchievementallotStatistic';
                params['mode']='saveBaiduvWages';
                params['userid']=userid;
                params['setmonth']=setmonth;
                params['staffwages']=staffwages;

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
                    params['id'] =id;
                    params['mode']='deletedBaiduvWages';
                    params['action'] = 'BasicAjax';
                    params['module'] = 'AchievementallotStatistic';
                    AppConnector.request(params).then(function (data) {
                        window.location.reload();
                    });
                });
            });

            jQuery('#tbl_Detail').DataTable({
                language: {"sProcessing":   "处理中...",   "sLengthMenu":   "显示 _MENU_ 项结果","sZeroRecords":  "没有匹配结果","sInfo":         "显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",  "sInfoEmpty":    "显示第 0 至 0 项结果，共 0 项","sInfoFiltered": "(由 _MAX_ 项结果过滤)","sInfoPostFix":  "","sSearch":"当前页快速检索:","sUrl":"","sEmptyTable":     "表中数据为空","sLoadingRecords": "载入中...",
                    "sInfoThousands":  ",", "oPaginate": {"sFirst":"首页","sPrevious": "上页","sNext":"下页","sLast":"末页"},
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
