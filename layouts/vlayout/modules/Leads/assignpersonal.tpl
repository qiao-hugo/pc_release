{strip}
    <style>
        .names{
            height: 20px;
            line-height: 20px;
            display: block;
            float: left;
            margin-top: 45px;
            margin-right: 10px;
            width: 150px;
        }
    </style>

    <table class="table table-bordered equalSplit detailview-table"><thead>
            <th colspan="4"><h4>分配人员设置</h4></th></thead><tbody>
        <tr>
            <td style="text-align: right;width: 25%"><span class="redColor">*</span>部门</td>
            <td colspan="3" style="width: 85%">
                <select id="department" name="department" class="chzn-select referenceModulesList streched" name="department">
                    {foreach key=index item=value from=$DEPARTMENT}
                        <option value="{$index}">{$value}</option>
                    {/foreach}
                </select>
            </td>
        </tr>
        <tr>
            <td style="text-align: right;width: 25%"><span class="redColor">*</span>部门人员</td>
            <td colspan="3">
                <div style="height: 300px;overflow: scroll;">
                    <div id="selectAll"><input type="checkbox"  class="entryCheckBoxAll" name="Detailrecord[]"> 全选</div>
                    <div id="userAll"></div>
                    {*<div style="float: left">*}
                        {*<label class="names"><input type="checkbox" value="109610" class="entryCheckBox" name="userid[]">张三2323</label>*}
                        {*<label class="names"><input type="checkbox" value="109610" class="entryCheckBox" name="Detailrecord[]">张三323</label>*}
                        {*<label class="names"><input type="checkbox" value="109610" class="entryCheckBox" name="Detailrecord[]">张三</label>*}
                        {*<label class="names"><input type="checkbox" value="109610" class="entryCheckBox" name="Detailrecord[]">张三3132</label>*}
                        {*<label class="names"><input type="checkbox" value="109610" class="entryCheckBox" name="Detailrecord[]">张三</label>*}
                        {*<label class="names"><input type="checkbox" value="109610" class="entryCheckBox" name="Detailrecord[]">张三323232</label>*}
                    {*</div>*}
                </div>
            </td>
        </tr>

        <tr><td colspan="2" style="text-align: center"><button class="btn btn-primary" id="preview">添加</button></td></tr>
        </tbody></table>


    <div style="margin-top:10px;">
        <div class="row-fluid" id="c" style="width:100%;">
            <div style="border:1px solid #ccc;margin:0 auto 20px;padding-right:20px;padding-top:10px;">
                <div id="bartable1" class="span12" style="height:490px;">
                    <table id="tbl_Detail" class="table listViewEntriesTable" width="100%"><thead><tr>
                            <th nowrap><b>姓名</b></th>
                            <th nowrap><b>部门</b></th>
                            <th nowrap><b>城市</b></th>
                            <th nowrap><b>职位</b></th>
                            <th nowrap><b>每次循环分配条数</b></th>
                            <th nowrap><b>操作</b></th>
                        </tr>
                        </thead><tbody>
                        {foreach item=value from=$RECOEDS}
                            <tr>
                                <td nowrap><b>{$value['last_name']}</b></td>
                                <td nowrap><b>{$value['departmentname']} </b></td>
                                <td nowrap><b>{$value['cityname']}</b></td>
                                <td nowrap><b>{$value['rolename']}</b></td>
                                <td nowrap><b><input type="number" min="1"  style="width: 60px" data-id="{$value['leadassignpersonnelid']}"  name="assignnum" value="{$value['assignnum']}" readonly> <i title="修改" class="icon-pencil alignMiddle modifyRecord" ></i></b></td>
                                <td nowrap><b><i title="删除" class="icon-trash alignMiddle deleteRecord"  data-id="{$value['leadassignpersonnelid']}"  style="cursor:pointer"></i></a></b></td>
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

        $(".entryCheckBoxAll").click(function () {
            if($(this).prop('checked')){
                $('.entryCheckBox').prop('checked',true);
            }else{
                $('.entryCheckBox').prop('checked',false);
            }
        });




        $(function(){
            $(".modifyRecord").click(function () {
                console.log(11);
                $(this).prev().attr("readonly",false);
                var str = '<i title="确认" class="icon-check alignMiddle confirmRecord" >';
                $(this).after(str);
                $(this).remove();
            });
            // $(".confirmRecord").click(function () {
            //     $(this).prev().attr("readonly",true);
            //     var str = '<i title="修改" class="pencil alignMiddle confirmRecord" >';
            //     $(this).after(str);
            //     $(this).remove();
            // });


            $("select[name='department']").on("change",function () {
                $("#userAll").empty();
                console.log($(this).val());
                var params = {};
                var module = app.getModuleName();
                params['departmentid'] = $(this).val();
                params['action'] = 'BasicAjax';
                params['module'] = module;
                params['mode'] = 'getDepartmentUsers';
                AppConnector.request(params).then(function(data){
                    console.log(data);
                    if (data.success) {
                        var str = '';
                        str +='<div style="float: left">\n';
                        $(data.data).each(function (k, v) {
                            last_name = v.last_name.replace("[",'<br>  [');

                            str += '<label class="names"><input type="checkbox" style="float:left;margin-top: 15px;margin-right: 10px;" value="'+v.id+'" class="entryCheckBox" name="userid[]"><span style="display:block;float:left;font-size: 14px;overflow: hidden;width:115px;"> '+last_name+'<span> </label>';
                        });
                        str +='</div>';
                        $("#userAll").append(str);

                        $(".entryCheckBox").click(function () {
                            if(!$(this).prop('checked')){
                                $(".entryCheckBoxAll").prop("checked",false);
                            }
                        });
                    } else {
                        alert(data.msg);
                    }
                });
            });


            $('#preview').click(function(){
                var userIds=[];
                $('input[name="userid[]"]:checked').each(function () {
                    userIds.push($(this).val());
                });
                if(userIds.length<1){
                    alert("请选择要添加的部门人员");
                    return;
                }

                var params = {};
                var module = app.getModuleName();
                params['userids'] = userIds;
                params['action'] = 'BasicAjax';
                params['module'] = module;
                params['mode'] = 'setAssignPersonal';
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
            $('.listViewEntriesTable').on('click','.deleteRecord',function(){
                var msg={
                    'message':'确定要删除该配置吗'
                };
                var id=$(this).data("id");


                Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e) {
                    var params = {};
                    var module = app.getModuleName();
                    params['id'] =id;
                    params['action'] = 'BasicAjax';
                    params['module'] = module;
                    params['mode'] = 'deleteAssignPersonal';
                    AppConnector.request(params).then(function (data) {
                        window.location.reload();
                    });
                });
            });

            $('.listViewEntriesTable').on('click',".confirmRecord",function(){
                var id=$(this).prev().data("id");
                var thisInstance=$(this);
                console.log(id);
                var assignnum=$(this).prev().val();
                console.log(assignnum);
                if(assignnum<1){
                    alert("每次循环分配条数必须大于0");
                    return;
                }
                var params = {};
                var module = app.getModuleName();
                params['id'] =id;
                params['assignnum'] =assignnum;
                params['action'] = 'BasicAjax';
                params['module'] = module;
                params['mode'] = 'updateAssignNum';
                AppConnector.request(params).then(function (data) {
                    console.log(data);
                    if (data.success) {
                        console.log(2323);
                        console.log(thisInstance);
                        thisInstance.prev().attr("readonly",true);
                        var str = '<i title="修改" class="icon-pencil alignMiddle modifyRecord" >';
                        thisInstance.after(str);
                        thisInstance.remove();

                        $(".modifyRecord").click(function () {
                            console.log(11);
                            $(this).prev().attr("readonly",false);
                            var str = '<i title="确认" class="icon-check alignMiddle confirmRecord" >';
                            $(this).after(str);
                            $(this).remove();
                        });
                    } else {
                        alert(data.msg);
                    }
                });
            });

        });
    </script>

        {/literal}
    {include file='JSResources.tpl'|@vtemplate_path MODULE=$MODULE}
{/strip}
