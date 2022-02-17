{strip}
    <br>
    <table class="table table-bordered equalSplit detailview-table"><thead>

        <tr><th colspan="2">百度V提成设置</th></tr></thead><tbody>
        <tr>
            <td style="text-align: right"><span class="redColor">*</span>部门</td>
            <td>
                <select id="department" name="department" class="chzn-select referenceModulesList streched" name="department">
                    {foreach key=index item=value from=$DEPARTMENT}
                        {if strpos($index,'H23') !== false}
                            <option value="{$index}">{$value}</option>
                        {/if}
                    {/foreach}
                </select>
            </td>
        </tr>
        <tr>
            <td style="text-align: right"><span class="redColor">*</span>设置月份</td>
            <td>
                <label class="pull-left">
                    <input class="span9 dateField"  type="text" name="settingmonth" data-date-format="yyyy-mm" id="settingmonth" value="{date("Y-m")}" style="width:100px;">
                </label>
            </td>
        </tr>
        <tr>
            <td><label class="pull-right"><span class="redColor">*</span>满月员工数量（有社保）</label></td>
            <td>
                <label class="pull-left">
                    <input id="peoplenum"  type="number" step="1" min="1"  class="input-large" />
                </label>
            </td>
        </tr>
        <tr>
            <td><label class="pull-right"><span class="redColor">*</span>满月未交社保实际工资成本</label></td>
            <td>
                <label class="pull-left">
                    <input id="monthpeoplemoney"  type="number" step="0.01" min="0.01"  class="input-large" />
                </label>
            </td>
        </tr>
        <tr>
            <td><label class="pull-right"><span class="redColor">*</span>未满月员工实际工资成本</label></td>
            <td>
                <label class="pull-left">
                    <input id="unmonthpeoplemoney"  type="number" step="0.01" min="0.01"  class="input-large" />
                </label>
            </td>
        </tr>
        <tr>
            <td><label class="pull-right"><span class="redColor">*</span>百度季度任务金额</label></td>
            <td>
                <label class="pull-left">
                    <input id="quarterlytasks"  type="number" step="0.01" min="0.01"  class="input-large" />
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
                            <th nowrap><b>部门</b></th>
                            <th nowrap><b>满月员工数量（有社保）</b></th>
                            <th nowrap><b>满月未交社保实际工资成本</b></th>
                            <th nowrap><b>未满月员工实际工资成本</b></th>
                            <th nowrap><b>百度季度任务金额</b></th>
                            <th nowrap><b>操作</b></th>
                        </tr>
                        </thead><tbody>
                        {foreach item=value from=$BAIDUVSETTINGLIST}
                            <tr>
                                <td nowrap><b>{$value['settingmonth']}</b></td>
                                <td nowrap><b>{$value['department2']}</b></td>
                                <td nowrap><b>{$value['peoplenum']}</b></td>
                                <td nowrap><b>{$value['monthpeoplemoney']}</b></td>
                                <td nowrap><b>{$value['unmonthpeoplemoney']}</b></td>
                                <td nowrap><b>{$value['quarterlytasks']}</b></td>
                                <td nowrap><b><i title="删除" class="icon-trash alignMiddle deleteRecord" data-id="{$value['baiduvsettingid']}" style="cursor:pointer"></i></b></td>
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
            $('#settingmonth').datetimepicker({
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
                var department=$("#department").val();
                if(!department){
                    Vtiger_Helper_Js.showPnotify({text :"部门必填",title :'信息必填'});
                    return;
                }
                var settingmonth=$("#settingmonth").val();
                if(!settingmonth){
                    Vtiger_Helper_Js.showPnotify({text :"设置月份",title :'信息必填'});
                    return;
                }
                var peoplenum=$("#peoplenum").val();
                if(!peoplenum){
                    Vtiger_Helper_Js.showPnotify({text :"满月员工数量（有社保）",title :'信息必填'});
                    return;
                }
                var monthpeoplemoney=$("#monthpeoplemoney").val();
                if(!monthpeoplemoney){
                    Vtiger_Helper_Js.showPnotify({text :"满月未交社保实际工资成本",title :'信息必填'});
                    return;
                }
                var unmonthpeoplemoney=$("#unmonthpeoplemoney").val();
                if(!unmonthpeoplemoney){
                    Vtiger_Helper_Js.showPnotify({text :"未满月员工实际工资成本",title :'信息必填'});
                    return;
                }
                var quarterlytasks=$("#quarterlytasks").val();
                if(!quarterlytasks){
                    Vtiger_Helper_Js.showPnotify({text :"百度季度任务金额",title :'信息必填'});
                    return;
                }
                params['action']='BasicAjax';
                params['module']='AchievementallotStatistic';
                params['mode']='saveBaiduvSetting';
                params['department']=department;
                params['settingmonth']=settingmonth;
                params['peoplenum']=peoplenum;
                params['monthpeoplemoney']=monthpeoplemoney;
                params['unmonthpeoplemoney']=unmonthpeoplemoney;
                params['quarterlytasks']=quarterlytasks;
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
                    params['mode']='deletedBaiduvSetting';
                    params['action'] = 'BasicAjax';
                    params['module'] = 'AchievementallotStatistic';
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
