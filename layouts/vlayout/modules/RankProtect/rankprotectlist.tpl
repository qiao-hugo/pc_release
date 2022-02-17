{strip}
    <table class="table table-bordered equalSplit detailview-table">
        <thead>
        <form action="index.php?module=ReceivedPayments&view=List&public=ExportRDALL" method="post">
            <th colspan="2">客户保护规则设置</th>
        </thead>
        <tbody>

        <tr>
            <td style="text-align: right">配置项
            </td>
            <td>
                <label class="pull-left">
                    <select id="configurationitem" class="chzn-select referenceModulesList streched"">
                    <option value="">请选择一项</option>
                    <option value="客户保护规则设置">客户保护规则设置</option>
                    </select>
                </label>
            </td>
        </tr>
        <tr>
            <td style="text-align: right"><span class="redColor">*</span>部门
            </td>
            <td>
                <label class="pull-left">
                    <select id="department" class="chzn-select referenceModulesList streched">
                        {foreach key=index item=value from=$DEPARTMENT}
                            <option value="{$index}">{$value}</option>
                        {/foreach}
                    </select>
                </label>
                <span class="pull-left">&nbsp;</span>
            </td>
        </tr>
        <tr>
            <td style="text-align: right">员工阶段
            </td>
            <td>
                <label class="pull-left">
                    <select id="staff_stage" class="chzn-select referenceModulesList streched">
                        <option value=""></option>
                        {foreach key=index item=values from=$STAFFSTAGE}
                            <option value="{$values.staff_stage}">{vtranslate($values.staff_stage)}</option>
                        {/foreach}
                    </select>
                </label>
            </td>
        </tr>
        <tr>
            <td style="text-align: right"><span class="redColor">*</span>客户等级
            </td>
            <td>
                <label class="pull-left">
                    <select id="accountrank" class="chzn-select referenceModulesList streched">
                        <option value=""></option>
                        {foreach key=index item=values from=$ACCOUNTRANK}
                            <option value="{$values.accountrank}">{vtranslate($values.accountrank)}</option>
                        {/foreach}
                    </select>
                </label>
            </td>
        </tr>
        <tr>
            <td style="text-align: right"><span class="redColor">*</span>商务等级
            </td>
            <td>
                <label class="pull-left">
                    <select id="performancerank" class="chzn-select referenceModulesList streched">
                        <option value=""></option>
                        {foreach key=index item=values from=$PERFORMANCERANK}
                            <option value="{$values.performancerank}">{vtranslate($values.performancerank)}</option>
                        {/foreach}
                    </select>
                </label>
            </td>
        </tr>
        <tr>
            <td style="text-align: right"><span class="redColor">*</span>保护数量
            </td>
            <td>
                <label class="pull-left">
                    <input type="text" id="protectnum" class="input-large"
                           data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>
                </label>
            </td>
        </tr>
        <tr>
            <td style="text-align: right"><span class="redColor">*</span>保护天数
            </td>
            <td>
                <label class="pull-left">
                    <input type="text" id="protectday" class="input-large"
                           data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>
                </label>
            </td>
        </tr>
        <tr>
            <td style="text-align: right"><span class="redColor">*</span>是否更新保护天数
            </td>
            <td>
                <label class="pull-left">
                    <select id="isupdate" class="chzn-select referenceModulesList streched">
                        <option value="ryes">是</option>
                        <option value="rno">否</option>
                    </select>
                </label>
            </td>
        </tr>
        <tr>
            <td style="text-align: right"><span class="redColor">*</span>跟进天数
            </td>
            <td>
                <label class="pull-left">
                    <input type="text" id="followday" class="input-large"
                           data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>
                </label>
            </td>
        </tr>
        <tr>
            <td style="text-align: right"><span class="redColor">*</span>是否需要跟进
            </td>
            <td>
                <label class="pull-left">
                    <select id="isfollow" class="chzn-select referenceModulesList streched">
                        <option value="rno">否</option>
                        <option value="ryes">是</option>
                    </select>
                </label>
            </td>
        </tr>

        </form>
        <tr>
            <td colspan="2" style="text-align: center">
                <button class="btn btn-primary" id="preview">添加</button>
            </td>
        </tr>
        </tbody>
    </table>
    <div style="margin-top:10px;">
        <div class="row-fluid" id="c" style="width:100%;">
            <div style="border:1px solid #ccc;margin:0 auto 20px;padding-right:20px;padding-top:10px;">
                <div id="bartable1" class="span12" style="height:490px;">
                    <table id="tbl_Detail" class="table listViewEntriesTable" width="100%">
                        <thead>
                        <tr>
                            <th nowrap><b>配置项</b></th>
                            <th nowrap><b>部门</b></th>
                            <th nowrap><b>员工阶段</b></th>
                            <th nowrap><b>客户等级</b></th>
                            <th nowrap><b>商务等级</b></th>
                            <th nowrap><b>保护数量</b></th>
                            <th nowrap><b>保护天数</b></th>
                            <th nowrap><b>是否更新保护天数</b></th>
                            <th nowrap><b>跟进天数</b></th>
                            <th nowrap><b>是否需要跟进</b></th>
                            <th nowrap><b>操作</b></th>
                        </tr>
                        </thead>
                        <tbody>
                        {foreach item=value from=$RECOEDS}
                            <tr data-rankid="{$value['rankid']}" data-department="{$value['departments']}"
                                data-staff_stage="{$value['staff_stage']}"
                                data-configurationitem="{$value['configurationitem']}"
                                data-accountrank="{$value['accountrank']}"
                                data-performancerank="{$value['performancerank']}"
                                data-protectnum="{$value['protectnum']}" data-protectday="{$value['protectday']}"
                                data-isupdate="{$value['isupdate']}" data-followday="{$value['followday']}"
                                data-isfollow="{$value['isfollow']}">
                                <td nowrap><b>{$value['configurationitem']}</b></td>
                                <td nowrap><b>{$value['department']}</b></td>
                                <td nowrap><b>{vtranslate($value['staff_stage'])}</b></td>
                                <td nowrap><b>{vtranslate($value['accountrank'])}</b></td>
                                <td nowrap><b>{vtranslate($value['performancerank'])}</b></td>
                                <td nowrap><b>{$value['protectnum']}</b></td>
                                <td nowrap><b>{$value['protectday']}</b></td>
                                <td nowrap><b>{vtranslate($value['isupdate'])}</b></td>
                                <td nowrap><b>{$value['followday']}</b></td>
                                <td nowrap><b>{vtranslate($value['isfollow'])}</b></td>
                                <td nowrap>
                                    <b><i title="删除" class="icon-trash alignMiddle deleteRecord"
                                          data-id="{$value['id']}" style="cursor:pointer"></i></a></b>
                                    <b><a><i title="编辑" class="icon-pencil alignMiddle editRankProtect"></i></a></b>
                                </td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
{*                    {include file='JSResources.tpl'|@vtemplate_path MODULE=$MODULE}*}
                </div>
                <div class="clearfix"></div>
            </div>
        </div>

    </div>
    </div>
    <script src="/libraries/jquery/chosen/chosen.jquery.min.js"></script>
    <script src="/libraries/jquery/datetimepicker/bootstrap-datetimepicker.min.js"></script>
    <script>
        {literal}
        $(function () {
            $(".vtFooter").css("display", "none");
            $('#preview').click(function () {
                var params = {};
                var module = app.getModuleName();
                var department = $("#department").val();
                var staff_stage = $("#staff_stage").val();
                var configurationitem = $("#configurationitem").val();
                var accountrank = $("#accountrank").val();
                var performancerank = $("#performancerank").val();
                var protectnum = $("#protectnum").val();
                var protectday = $("#protectday").val();
                var isupdate = $("#isupdate").val();
                var followday = $("#followday").val();
                var isfollow = $("#isfollow").val();
                params['configurationitem'] = configurationitem;
                params['department'] = department;
                params['staff_stage'] = staff_stage;
                params['accountrank'] = accountrank;
                params['performancerank'] = performancerank;
                params['protectnum'] = protectnum;
                params['protectday'] = protectday;
                params['isupdate'] = isupdate;
                params['followday'] = followday;
                params['isfollow'] = isfollow;
                params['action'] = 'BasicAjax';
                params['module'] = module;
                params['mode'] = 'add';
                if (department == '') {
                    alert("部门不能为空");
                    return;
                }
                if (accountrank == '') {
                    alert("客户等级不能为空");
                    return;
                }
                if (performancerank == '') {
                    alert("商务等级不能为空");
                    return;
                }
                if (protectnum == '') {
                    alert("保护数量不能为空");
                    return;
                }
                if (protectday == '') {
                    alert("保护天数不能为空");
                    return;
                }
                AppConnector.request(params).then(function (data) {
                    if (data.result.status == true) {
                        alert(data.result.message);
                    } else {
                        window.location.reload();
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
                    "bSort": false,
                },
                scrollY: "460px",
                sScrollX: "disabled",
                aLengthMenu: [10, 20, 50, 100,],
                fnDrawCallback: function () {

                }
            });
            $('.listViewEntriesTable').on('click', '.deleteRecord', function () {
                var msg = {
                    'message': '<th colspan="2">确定要删除该用户的权限吗</th>'
                };
                var id = $(this).closest("tr").data("rankid");
                Vtiger_Helper_Js.showConfirmationBox(msg).then(function (e) {
                    var params = {};
                    var module = app.getModuleName();
                    params['id'] = id;
                    params['action'] = 'BasicAjax';
                    params['module'] = module;
                    params['mode'] = 'deleted';
                    AppConnector.request(params).then(function (data) {
                        window.location.reload();
                    });
                });
            });
            $('.listViewEntriesTable').on('click', '.editRankProtect', function () {
                var params = {};
                var module = app.getModuleName();
                params['action'] = 'BasicAjax';
                params['module'] = module;
                params['mode'] = 'getDepartment';
                var elements = $(this).closest("tr");
                var configurationitem = elements.data("configurationitem");
                var department = elements.data("department");
                var staff_stage = elements.data("staff_stage");
                var accountrank = elements.data("accountrank");
                var performancerank = elements.data("performancerank");
                var protectnum = elements.data("protectnum");
                var protectday = elements.data("protectday");
                var rankid = elements.data("rankid");
                var isupdate = elements.data("isupdate");
                var followday = elements.data("followday");
                var isfollow = elements.data("isfollow");
                AppConnector.request(params).then(function (data) {
                    var strDepartment = '<option value="">请选择一项</option>';
                    console.log(department);
                    for (let i in data['department']) {
                        if (department == i) {
                            console.log(i);
                            strDepartment += "<option value='" + i + "'  selected >" + data['department'][i] + "</option>";
                        } else {
                            strDepartment += "<option value='" + i + "'>" + data['department'][i] + "</option>";
                        }
                    }
                    var strStaff_stage = '<option value="">请选择一项</option>';
                    for (let i in data['staff_stage']) {
                        if (i == staff_stage) {
                            strStaff_stage += "<option value='" + i + "'  selected >" + data['staff_stage'][i] + "</option>";
                        } else {
                            strStaff_stage += "<option value='" + i + "'>" + data['staff_stage'][i] + "</option>";
                        }
                    }
                    var strisupdate = ''
                    if (isupdate == 'ryes') {
                        strisupdate += "<option value='ryes'  selected >是</option><option value='rno'>否</option>";
                    } else {
                        strisupdate += "<option value='ryes' >是</option><option value='rno'  selected>否</option>";
                    }
                    var strisfollow = ''
                    if (isfollow == 'ryes') {
                        strisfollow += "<option value='ryes'  selected >是</option><option value='rno'>否</option>";
                    } else {
                        strisfollow += "<option value='ryes' >是</option><option value='rno'  selected>否</option>";
                    }
                    var strAccountrank = '<option value="">请选择一项</option>';
                    for (let i in data['accountrank']) {
                        if (i == accountrank) {
                            strAccountrank += "<option value='" + i + "'  selected >" + data['accountrank'][i] + "</option>";
                        } else {
                            strAccountrank += "<option value='" + i + "'   >" + data['accountrank'][i] + "</option>";
                        }
                    }
                    var strPerformancerank = '<option value="">请选择一项</option>';
                    for (let i in data['performancerank']) {
                        if (i == performancerank) {
                            strPerformancerank += "<option value='" + i + "'  selected >" + data['performancerank'][i] + "</option>";
                        } else {
                            strPerformancerank += "<option value='" + i + "'   >" + data['performancerank'][i] + "</option>";
                        }
                    }
                    var strconfigurationitem = '';
                    if (configurationitem == '客户保护规则设置') {
                        strconfigurationitem = '<option value="">请选择一项</option><option value="客户保护规则设置" selected >客户保护规则设置</option> ';
                    } else {
                        strconfigurationitem = '<option value="">请选择一项</option><option value="客户保护规则设置"  >客户保护规则设置</option> ';
                    }
                    var msg = {
                        'message': '<th colspan="2">客户保护规则设置</th>'
                    };
                    Vtiger_Helper_Js.showConfirmationBox(msg).then(function (e) {
                        var params = {};
                        var module = app.getModuleName();
                        params['action'] = 'BasicAjax';
                        params['module'] = module;
                        params['mode'] = 'updateRankProtect';
                        params['department'] = $("#departments").val();
                        params['configurationitem'] = $("#configurationitems").val();
                        params['staff_stage'] = $("#staff_stages").val();
                        params['accountrank'] = $("#accountranks").val();
                        params['performancerank'] = $("#performanceranks").val();
                        params['protectnum'] = $("#protectnums").val();
                        params['protectday'] = $("#protectdays").val();
                        params['rankid'] = $("#rankid").val();
                        params['isupdate'] = $("#isupdates").val();
                        params['followday'] = $("#followdays").val();
                        params['isfollow'] = $("#isfollows").val();
                        AppConnector.request(params).then(function (data) {
                            window.location.reload();
                        });
                    });
                    // var str = '<table class="table table-bordered equalSplit detailview-table"><thead></thead><tbody><tr><td style="text-align:right"><input type="hidden" id="rankid" value="' + rankid + '"/>配置项</td><td><label class="pull-left"><select id="configurationitems" class="chzn-select referenceModulesList streched">' + strconfigurationitem + '</select></label></td></tr><tr><td style="text-align:right"><span class="redColor">*</span>部门</td><td><label class="pull-left"><select id="departments" class="chzn-select referenceModulesList streched">' + strDepartment + '</select></label></td></tr><tr><td style="text-align:right">员工阶段</td><td><label class="pull-left"><select id="staff_stages" class="chzn-select referenceModulesList streched">' + strStaff_stage + '</select></label></td></tr><tr><td style="text-align:right"><span class="redColor">*</span>客户等级</td><td><label class="pull-left"><select id="accountranks" class="chzn-select referenceModulesList streched">' + strAccountrank + '</select></label></td></tr><tr><td style="text-align:right"><span class="redColor">*</span>商务等级 </td><td><label class="pull-left"><select id="performanceranks" class="chzn-select referenceModulesList streched" >' + strPerformancerank + '</select></label></td></tr><tr><td style="text-align:right"><span class="redColor">*</span>保护数量 </td><td> <label class="pull-left"> <input  type="text" id="protectnums" value="' + protectnum + '"  class="input-large" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" /></label> </td></tr> <tr><td style="text-align: right"><span class="redColor">*</span>保护天数</td><td><label class="pull-left"><input  type="text" id="protectdays" value="' + protectday + '" class="input-large" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  /></label></td></tr><tr><td style="text-align: right"><span class="redColor">*</span>是否更新保护天数</td><td><label class="pull-left"><select id="isupdates" class="chzn-select referenceModulesList streched" >' + strisupdate + '</select></label></td></tr><tr><td style="text-align: right"><span class="redColor">*</span>跟进天数</td><td><label class="pull-left"><input type="text" id="followday" value="' + followday + '" class="input-large" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/></label></td></tr><tr><td style="text-align: right"><span class="redColor">*</span>是否更新跟进天数</td><td><label class="pull-left"><select id="isfollow" class="chzn-select referenceModulesList streched">' + strisfollow + '</select></label></td></tr></tbody></table>';
                    var str = `<table class="table table-bordered equalSplit detailview-table">
    <thead></thead>
    <tbody>
    <tr>
        <td style="text-align:right"><input type="hidden" id="rankid" value="`+rankid+`"/>配置项</td>
        <td><label class="pull-left"><select id="configurationitems" class="chzn-select referenceModulesList streched">`+strconfigurationitem+`</select></label>
        </td>
    </tr>
    <tr>
        <td style="text-align:right"><span class="redColor">*</span>部门</td>
        <td><label class="pull-left"><select id="departments" class="chzn-select referenceModulesList streched">`+strDepartment+`</select></label>
        </td>
    </tr>
    <tr>
        <td style="text-align:right">员工阶段</td>
        <td><label class="pull-left"><select id="staff_stages" class="chzn-select referenceModulesList streched">`+strStaff_stage+`</select></label>
        </td>
    </tr>
    <tr>
        <td style="text-align:right"><span class="redColor">*</span>客户等级</td>
        <td><label class="pull-left"><select id="accountranks" class="chzn-select referenceModulesList streched">`+strAccountrank+`</select></label>
        </td>
    </tr>
    <tr>
        <td style="text-align:right"><span class="redColor">*</span>商务等级</td>
        <td><label class="pull-left"><select id="performanceranks" class="chzn-select referenceModulesList streched">`+strPerformancerank+`</select></label>
        </td>
    </tr>
    <tr>
        <td style="text-align:right"><span class="redColor">*</span>保护数量</td>
        <td><label class="pull-left"> <input type="text" id="protectnums" value="`+protectnum+`" class="input-large"
                                             data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/></label>
        </td>
    </tr>
    <tr>
        <td style="text-align: right"><span class="redColor">*</span>保护天数</td>
        <td><label class="pull-left"><input type="text" id="protectdays" value="`+protectday+`" class="input-large"
                                            data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/></label>
        </td>
    </tr>
    <tr>
        <td style="text-align: right"><span class="redColor">*</span>是否更新保护天数</td>
        <td><label class="pull-left"><select id="isupdates" class="chzn-select referenceModulesList streched">`+strisupdate+`</select></label>
        </td>
    </tr>
    <tr>
        <td style="text-align: right"><span class="redColor">*</span>跟进天数</td>
        <td><label class="pull-left"><input type="text" id="followdays" value="`+followday+`" class="input-large"
                                            data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/></label>
        </td>
    </tr>
    <tr>
        <td style="text-align: right"><span class="redColor">*</span>是否更新跟进天数</td>
        <td><label class="pull-left"><select id="isfollows" class="chzn-select referenceModulesList streched">`+strisfollow+`</select></label>
        </td>
    </tr>
    </tbody>
</table>`;
                    $(".modal-body").append(str);
                    $(".chzn-select").chosen();
                });
            });
        });
        {/literal}
    </script>
{/strip}

