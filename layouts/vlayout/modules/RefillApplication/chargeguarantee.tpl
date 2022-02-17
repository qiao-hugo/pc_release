{strip}


    {if $UPDATETHIS}
        <table class="table table-bordered equalSplit detailview-table"><thead>

        <tr><th colspan="2">默认担保人金额设置</th></tr></thead><tbody>
            <tr><td style="text-align: right"><span class="redColor">*</span>部门
                </td><td>
                    <select id="department" name="department" class="chzn-select referenceModulesList streched" name="department">
                        {foreach key=index item=value from=$DEPARTMENT}
                            <option value="{$index}">{$value}</option>
                        {/foreach}
                    </select>
                </td></tr>
            <tr><td style="text-align: right"><span class="redColor">*</span>一级担保人
                </td><td>
                    <label class="pull-left">
                        <select id="userid" name="userid" class="chzn-select referenceModulesList streched"">
                        <option value="">请选择一项</option>
                        {foreach key=index item=value from=$USER}
                            <option value="{$value.id}">{$value.last_name}</option>
                        {/foreach}
                        </select>
                    </label>
                </td></tr>

            <tr><td style="text-align: right">二级担保人
                </td><td>
                    <label class="pull-left">
                        <select id="twoleveluserid" name="twoleveluserid" class="chzn-select referenceModulesList streched"">
                        <option value="">请选择一项</option>
                        {foreach key=index item=value from=$USER}
                            <option value="{$value.id}">{$value.last_name}</option>
                        {/foreach}
                        </select>
                    </label>
                </td></tr>
            <tr><td style="text-align: right">三级担保人
                </td><td>
                    <label class="pull-left">
                        <select id="threeleveluserid" name="threeleveluserid" class="chzn-select referenceModulesList streched"">
                        <option value="">请选择一项</option>
                        {foreach key=index item=value from=$USER}
                            <option value="{$value.id}">{$value.last_name}</option>
                        {/foreach}
                        </select>
                    </label>
                </td></tr>


            <tr><td><label class="pull-right">一级担保金额</label></td>
                <td>
                    <label class="pull-left">
                        <input type="number" id="unitprice" class="input-large nameField"/>
                    </label>
                </td>
            </tr>
            <tr><td><label class="pull-right">二级担保金额</label></td>
                <td>
                    <label class="pull-left">
                        <input type="number" id="twounitprice" class="input-large nameField"/>
                    </label>
                </td>
            </tr>
            <tr><td><label class="pull-right">三级担保金额</label></td>
                <td>
                    <label class="pull-left">
                        <input type="number" id="threeunitprice" class="input-large nameField"/>
                    </label>
                </td>
            </tr>

            <tr><td style="text-align: center" colspan="2"><button class="btn btn-primary" id="savedepartuser">保存</button></td></tr>
        </tbody></table>
    {/if}
    <div style="margin-top:10px;">
        <div class="row-fluid span12" id="c">
        <div id="msg" style="height:20px;margin:0 auto;border:1px solid #ccc;border-bottom: none;padding-top:20px;"></div>
            <div style="border:1px solid #ccc;margin:0 auto 20px;padding-right:20px;">
                <div id="bartable1" class="span12" style="height:490px;cursor:pointer;">
                    <table id="tbl_Detail" class="table listViewEntriesTable" width="100%"><thead><tr>
                        <th nowrap><b>部门</b></th>
                        <th nowrap><b>一级担保</b></th>
                        <th nowrap><b>一级担保金额</b></th>
                            <th nowrap><b>二级担保</b></th>
                            <th nowrap><b>二级担保金额</b></th>
                            <th nowrap><b>三级担保</b></th>
                            <th nowrap><b>三级担保金额</b></th>
                        <th nowrap><b>操作</b></th>
                       </tr>
                        </thead><tbody>
                        {foreach item=value from=$LISTDUSER}

                        <tr>
                            <td nowrap><b>{$value['departmentname']}</b></td>
                            <td nowrap><b>{$value['username']}</b></td>
                            <td nowrap><b>{$value['unitprice']}</b></td>
                            <td nowrap><b>{$value['twousername']}</b></td>
                            <td nowrap><b>{$value['twounitprice']}</b></td>
                            <td nowrap><b>{$value['threeusername']}</b></td>
                            <td nowrap><b>{$value['threeunitprice']}</b></td>
                            <td nowrap>{if $UPDATETHIS}<b><i title="删除" class="icon-trash alignMiddle deleteRecord" data-id="{$value['rechargeguaranteeid']}"  style="cursor:pointer"></i></a></b>{/if}</td>
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

                var userid=$("select[name='userid']").val();
                var twoleveluserid=$("select[name='twoleveluserid']").val();
                var threeleveluserid=$("select[name='threeleveluserid']").val();

                var unitprice=$("#unitprice").val()-0;
                var twounitprice=$("#twounitprice").val()-0;
                var threeunitprice=$("#threeunitprice").val()-0;
                if(unitprice<=0){
                    Vtiger_Helper_Js.showPnotify({text :"担保金额不能为空!",title :'信息必填'});
                    return false;
                }
                if(twounitprice<=0){
                    Vtiger_Helper_Js.showPnotify({text :"二级担保金额不能为空!",title :'信息必填'});
                    return false;
                }
                var department=$('#department').val();
                var datatime=$('#datatime').val();
                var enddatetime=$('#enddatatime').val();
                params['action']='BasicAjax';
                params['module']=module;
                params['mode']='setChargeGuarantee';
                params['userid']=userid;
                params['department']=department;
                params['unitprice']=unitprice;
                params['domodule']={/literal}'{$DOMODULE}'{literal};
                params['twoleveluserid']=twoleveluserid;
                params['twounitprice']=twounitprice;
                params['threeleveluserid']=threeleveluserid;
                params['threeunitprice']=threeunitprice;


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
                    params['mode'] = 'delChargeGuarantee';

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
