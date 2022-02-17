{strip}

    <table class="table table-bordered equalSplit detailview-table"><thead>

        <tr><th colspan="2">商机查看</th></tr></thead><tbody>

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
            <tr><td><label class="pull-right">状态</label></td>
                <td>
                    <label class="pull-left">
                        <select id="status" class="chzn-select referenceModulesList streched">
                            <option value="a_not_allocated">未分配</option>
                            <option value="c_allocated" selected>已分配</option>


                        </select>
                    </select>
                    </label>
                </td>
            </tr>
            <tr><td><label class="pull-right">负责人</label></td>
                <td>
                    <label class="pull-left">
                        <select id="userid" class="chzn-select referenceModulesList streched">
                            <option value="0">请选择负责人</option>
                            {foreach key=index item=value from=$USER}
                            <option value="{$value.id}"{if in_array($value['id'],$FIXEDUSER)} selected{/if}>{$value.last_name}</option>
                        {/foreach}
                        </select>
                    </select>
                    </label>
                </td>
            </tr>
            <tr><td style="text-align: center" colspan="2"><button class="btn btn-primary" id="preview">查看</button></td></tr>
        </tbody></table>
        <table class="table table-bordered equalSplit detailview-table"><thead>

        <tr><th colspan="2">商机批量操作</th></tr></thead><tbody>


            <tr><td><label class="pull-right">新负责人</label></td>
                <td>
                    <label class="pull-left">
                        <select id="douserid" class="chzn-select referenceModulesList streched">
                            <option value="0">请选择负责人</option>
                            {foreach key=index item=value from=$USER}
                                <option value="{$value.id}"{if in_array($value['id'],$FIXEDUSER)} selected{/if}>{$value.last_name}</option>
                            {/foreach}
                        </select>
                    </select>
                    </label>
                </td>
            </tr>
            <tr><td style="text-align: center" colspan="2"><button class="btn btn-primary" id="resetassignerstatus">重新分配</button>&nbsp;&nbsp;&nbsp;&nbsp;<button class="btn btn-primary" id="togonghai">转入公海</button></td></tr>
        </tbody></table>
    <div style="margin-top:10px;">
        <div class="row-fluid span12" id="c">
        <div id="msg" style="height:20px;margin:0 auto;border:1px solid #ccc;border-bottom: none;padding-top:20px;"></div>
            <div style="border:1px solid #ccc;margin:0 auto 20px;padding-right:20px;">
                <div id="bartable1" class="span12" style="height:490px;cursor:pointer;">

                </div>
                <div class="clearfix"></div></div>
            </div>
        </div>

    </div>

    {literal}
    <script>
       $(document).ready(function(){
            $('#preview').click(function(){
                var params={};
                var module = app.getModuleName();
                var departmentid=$("#departmentid").val();
                var status=$("#status").val();
                var userid=$("#userid").val();
                params['action']='BasicAjax';
                params['module']=module;
                params['mode']='getLeadsBatchList';
                params['departmentid']=departmentid;
                params['status']=status;
                params['userid']=userid;
                $('#bartable1').empty();
                console.log(params);
                var progressIndicatorElement = jQuery.progressIndicator({
                    'message' : '正在处理,请耐心等待哟',
                    'position' : 'html',
                    'blockInfo' : {'enabled' : true}
                });
                AppConnector.request(params).then(function (data){
                        progressIndicatorElement.progressIndicator({
                            'mode' : 'hide'
                        });
                        if(data.success){
                            var tablestr='<table id="tbl_Detail" class="table listViewEntriesTable" width="100%;"><thead><tr style="cursor:pointer;"><th nowrap><label class="pull-left"><input class="span1 dateField selectbutton" type="checkbox" style="width:30px;"><b>选择</b></label></th><th nowrap><b>线索客户名称</b></th><th nowrap><b>转化后客户名称</b></th><th nowrap><b>录入时间</b></th><th nowrap><b>最后跟进时间</b></th><th nowrap><b>负责人</b></th><th nowrap><b>处理状态</b></th><th nowrap><b>分配时间</b></th></tr></thead><tbody>';
                            $.each(data.result,function(key,value){

                                tablestr+='<tr><td><label class="pull-left"><input class="span1 dateField leadidselect" type="checkbox" name="leadids[]" value="'+value.leadid+'" style="width:30px;"></label></td><td>'+value.company+'</td><td>'+value.accountname+'</td><td>'+value.mapcreattime+'</td><td>'+value.commenttime+'</td><td>'+value.last_name+'</td><td>'+value.assignerstatus+'</td><td>'+value.allocatetime+'</td></tr>';
                            });
                            tablestr+='</tbody></table>';
                            $('#bartable1').append(tablestr);
                            Tableinstance();
                        }
                });
            });
        function Tableinstance(){
        var table = jQuery('#tbl_Detail').DataTable({
            language: {"sProcessing":   "处理中...",	"sLengthMenu":   "显示 _MENU_ 项结果","sZeroRecords":  "没有匹配结果","sInfo":         "显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",	"sInfoEmpty":    "显示第 0 至 0 项结果，共 0 项","sInfoFiltered": "(由 _MAX_ 项结果过滤)","sInfoPostFix":  "","sSearch":"当前页快速检索:","sUrl":"","sEmptyTable":     "表中数据为空","sLoadingRecords": "载入中...",
                "sInfoThousands":  ",",	"oPaginate": {"sFirst":"首页","sPrevious": "上页","sNext":"下页","sLast":"末页"},
                "oAria": {"sSortAscending":  ": 以升序排列此列","sSortDescending": ": 以降序排列此列"}},
            scrollY:"400px",
            sScrollX:"disabled",
            //paging: false,
            //searching: false,
            columnDefs:[{
                 orderable:false,//禁用排序
                 targets:[0,0]   //指定的列
             }],
            aLengthMenu: [ 10, 20, 50, 100, 200],
            fnDrawCallback:function(){
                jQuery('#msg').html('<span style="font-size:12px;color:green;text-align:left;">超过2000条不显示</span>');
            }
        });
        }
        //重新分配
        $('#resetassignerstatus').click(function(){
            var leadids=$('input[name="leadids[]"]');
            if(leadids.val()===undefined){
                Vtiger_Helper_Js.showPnotify({text :"请选择要操作的商机",title :'信息必填'});
                return;
            }
            var douserid=$("#douserid").val();
            if(douserid==0){
                Vtiger_Helper_Js.showPnotify({text :"请选择新负责人",title :'信息必填'});
                return;
            }
            var leadidss=[];
            var params={}
            leadids.each(function(){
                if(this.checked){
                    leadidss.push($(this).val());
                }
            });
            if(leadidss.length==0){
                Vtiger_Helper_Js.showPnotify({text :"请选择要操作的商机",title :'信息必填'});
                return;
            }

            var params={};
                var module = app.getModuleName();
                var departmentid=$("#departmentid").val();
                var status=$("#status").val();
                params['action']='BasicAjax';
                params['module']=module;
                params['mode']='setLeadsChangeUser';
                params['leadids']=leadidss;
                params['userid']=douserid;
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
        //转入公海
        $('#togonghai').click(function(){
            var leadids=$('input[name="leadids[]"]');
            if(leadids.val()===undefined){
                Vtiger_Helper_Js.showPnotify({text :"请选择要操作的商机",title :'信息必填'});
                return;
            }

            var leadidss=[];
            var params={}
            leadids.each(function(){
                if(this.checked){
                    leadidss.push($(this).val());
                }
            });
            if(leadidss.length==0){
                Vtiger_Helper_Js.showPnotify({text :"请选择要操作的商机",title :'信息必填'});
                return;
            }

            var params={};
                var module = app.getModuleName();
                var departmentid=$("#departmentid").val();
                var status=$("#status").val();

                params['action']='BasicAjax';
                params['module']=module;
                params['mode']='setLeadsChangeGonghai';
                params['leadids']=leadidss;
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
        $('#bartable1').on('click','.selectbutton',function(){
            $(this).attr('checked')?$('.leadidselect').attr('checked',true):$('.leadidselect').attr('checked',false);
        });
       });
    </script>
    {/literal}
{include file='JSResources.tpl'|@vtemplate_path MODULE=$MODULE}
{/strip}
