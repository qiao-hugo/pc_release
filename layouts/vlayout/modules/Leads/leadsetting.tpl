{strip}

    <table class="table table-bordered equalSplit detailview-table"><thead>

        <tr><th colspan="2">商机设置</th></tr></thead><tbody>
         <tr><td style="text-align: center" colspan="2">商机自动掉公海规则</td></tr>
            <tr><td><label class="pull-right">分配后跟踪期限</label></td>
                <td>
                    <label class="pull-left">
                        <input class="span9 dateField" type="text" name="allocationaftertracking" value="{$GONGHAIMAIL['allocationaftertracking']}" style="width:100px;">
                    </label>
                </td>
            </tr>
            <tr><td><label class="pull-right">最长跟踪间隔</label></td>
                <td>
                    <label class="pull-left">
                        <input class="span9 dateField" type="text" name="longesttracking" value="{$GONGHAIMAIL['longesttracking']}" style="width:100px;">
                    </label>
                </td>
            </tr>
            <tr><td style="text-align: center" colspan="2">商机邮件提醒规则</td></tr>
            <tr><td><label class="pull-right">负责人本人</label></td>
                <td>
                    <label class="pull-left">
                        <input class="span9 dateField" type="checkbox" name="smower" value="1" {if $GONGHAIMAIL['smower'] eq 1}checked="checked" {/if}style="width:30px;">
                    </label>
                </td>
            </tr>
            <tr><td><label class="pull-right">负责人直接上级</label></td>
                <td>
                    <label class="pull-left">
                        <input class="span9 dateField" type="checkbox" name="reportto" value="1" {if $GONGHAIMAIL['reportto'] eq 1}checked="checked" {/if}style="width:30px;">
                    </label>
                </td>
            </tr>
            <tr><td><label class="pull-right">负责人变改前的负责人</label></td>
                <td>
                    <label class="pull-left">
                        <input class="span9 dateField" type="checkbox" name="oldsmower" value="1" {if $GONGHAIMAIL['oldsmower'] eq 1}checked="checked" {/if}style="width:30px;">
                    </label>
                </td>
            </tr>
            <tr><td><label class="pull-right">部门指定人员</label></td>
                <td>
                    <label class="pull-left">
                        <input class="span9 dateField" type="checkbox" name="departmentdesignated" value="1" {if $GONGHAIMAIL['departmentdesignated'] eq 1}checked="checked" {/if}style="width:30px;">
                    </label>
                </td>
            </tr>
            <tr><td><label class="pull-right">固定人员</label></td>
                <td>
                    <label class="pull-left">
                        <input class="span9 dateField" type="checkbox" name="fixedpersonnel" value="1" {if $GONGHAIMAIL['fixedpersonnel'] eq 1}checked="checked" {/if}style="width:30px;">
                    </label>
                </td>
            </tr>
            <tr><td><label class="pull-right">固定人员</label></td>
                <td>
                    <label class="pull-left">
                        <select id="fixedpersonnellist" class="chzn-select referenceModulesList streched" multiple>
                            {foreach key=index item=value from=$USER}
                                <option value="{$value.id}"{if in_array($value['id'],$FIXEDUSER)} selected{/if}>{$value.last_name}</option>
                            {/foreach}
                        </select>
                    </select>
                    </label>
                </td>
            </tr>
         <tr><td style="text-align: center" colspan="2">线索签单业绩分成保护时间</td></tr>
         <tr><td><label class="pull-right">保护天数</label></td>
             <td>
                 <label class="pull-left">
                     <input class="span9 dateField" id="protectday" type="number" min="0" name="protectday" value="{$GONGHAIMAIL['protectday']}" style="width:100px;">
                 </label>
             </td>
         </tr>
            <tr><td style="text-align: center" colspan="2"><button class="btn btn-primary" id="savegonghaisetting">保存</button></td></tr>
        </tbody></table>

        <table class="table table-bordered equalSplit detailview-table"><thead>

        <tr><th colspan="2">部门指定接收邮件人员</th></tr></thead><tbody>
            <tr><td><label class="pull-right">部门</label></td>
                <td>
                    <label class="pull-left">
                        <select id="departmentid" class="chzn-select referenceModulesList streched">
                                {foreach key=index item=value from=$DEPARTMENTUSER}
                                    <option value="{$index}" {if in_array($index,$arr)} selected{/if}>{$value}</option>
                                {/foreach}
                            </select>
                    </label>
                </td>
            </tr>
            <tr><td><label class="pull-right">收件人</label></td>
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
                        <th nowrap><b>姓名</b></th>
                        <th nowrap><b>部门</b></th>
                        <th nowrap><b>操作</b></th>
                       </tr>
                        </thead><tbody>
                        {foreach item=value from=$LISTDUSER}

                        <tr>
                            <td nowrap><b>{$value['last_name']}</b></td>
                            <td nowrap><b>{$DEPARTMENTUSER[$value['departmentid']]}</b></td>
                            <td nowrap><b><i title="删除" class="icon-trash alignMiddle deleteRecord" data-id="{$value['id']}" data-departmentid="{$value['departmentid']}" style="cursor:pointer"></i></a></b></td>
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

                var allocationaftertracking=$("input[name='allocationaftertracking']").val();
                var longesttracking=$("input[name='longesttracking']").val();
                var smower=$("input[name='smower']:checked").val();
                smower=smower==1?1:0;
                var reportto=$("input[name='reportto']:checked").val();
                reportto=reportto==1?1:0;
                var oldsmower=$("input[name='oldsmower']:checked").val();
                oldsmower=oldsmower==1?1:0;
                var departmentdesignated=$("input[name='departmentdesignated']:checked").val();
                departmentdesignated=departmentdesignated==1?1:0;
                var fixedpersonnel=$("input[name='fixedpersonnel']:checked").val();
                fixedpersonnel=fixedpersonnel==1?1:0;
                var fixedpersonnellist=$("#fixedpersonnellist").val();
                var protectday=$("#protectday").val();

                var datatime=$('#datatime').val();
                var enddatetime=$('#enddatatime').val();
                params['action']='BasicAjax';
                params['module']=module;
                params['mode']='setLeadsSetting';
                params['allocationaftertracking']=allocationaftertracking;
                params['longesttracking']=longesttracking;
                params['smower']=smower;
                params['oldsmower']=oldsmower;
                params['reportto']=reportto;
                params['departmentdesignated']=departmentdesignated;
                params['fixedpersonnel']=fixedpersonnel;
                params['fixedpersonnellist']=fixedpersonnellist;
                params['protectday']=protectday;
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
                    Vtiger_Helper_Js.showPnotify({text :"收件人必填",title :'信息必填'});
                    return;
                }
                var departmentid=$("#departmentid").val();
                params['action']='BasicAjax';
                params['module']=module;
                params['mode']='setLeadsSettingDepart';
                params['user_id']=user_id;
                params['departmentid']=departmentid;


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
                var id=$(this).data("id")
                var departmentid=$(this).data("departmentid")
                Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e) {
                    var params = {};
                    var module = app.getModuleName();
                    params['id'] =id;
                    params['action'] = 'BasicAjax';
                    params['module'] = module;
                    params['mode'] = 'deleteUser';
                    params['departmentid'] =departmentid;
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
