{strip}
    <link rel="stylesheet" type="text/css" href="libraries/font-awesome/css/font-awesome.min.css">
    <style>
        .table{ font-size: 12px; }
        .table-bordered { border-collapse:collapse; }
        .panel-title { font-size:15px;color:#333;font-weight:bold;margin: 10px 0 20px; }
        .plan-panel .table td{ line-height: 26px;}
        .plan-panel .input-amount { margin: 0;padding: 2px; width: 80px;height: 18px; }
        .rule-panel .btn-delete { cursor: pointer; font-size: 15px; }
        .rule-panel .btn-edit{ margin-left: 10px;color:#4286F5;; cursor: pointer; }
        #edit-layer .title{ margin: 10px 0;font-weight: bold;text-align: center; }
        #edit-layer input{ margin-bottom:0; font-size: 12px; }
        #edit-layer input[disabled], #edit-layer input[readonly]{ cursor:default; }
        #edit-layer .fa{ font-size:16px;cursor: pointer; }
        #edit-layer .contract-list, #edit-layer .department-list, #edit-layer .companyaccount-list{ min-height: 150px }
        #edit-layer .department-list li, #edit-layer .companyaccount-list li{ margin-bottom:10px; }
        #edit-layer .contract-list li select{ width:160px;margin: 0 5px; }
        #edit-layer .department-list li select{ width:160px; margin: 0 5px; }
        #edit-layer .companyaccount-list li select{ width:256px; margin: 0 5px; }
        .chzn-container .chzn-results{ max-height:300px; }
        .chzn-container .chzn-results li{ padding:5px 8px }
        .chzn-container-multi .chzn-choices .search-field input{ padding: 3px; }
        .layui-layer ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
            background: transparent;
            border-radius: 6px;
        }
        .layui-layer ::-webkit-scrollbar-button {
            width: 0;
            height: 0;
        }
        .layui-layer ::-webkit-scrollbar-track {
            border-radius: 6px;
        }
        .layui-layer ::-webkit-scrollbar-track-piece {
            width: 6px;
            margin: 0 -2px 0;
        }
        .layui-layer ::-webkit-scrollbar-thumb {
            background-color: #aaa;
            min-height: 60px;
            min-width: 60px;
            border-radius: 6px;
        }
        .layui-layer ::-webkit-scrollbar-thumb:vertical:hover {
            background-color: #999;
        }
        .layui-layer ::-webkit-scrollbar-thumb:horizontal:hover {
            background-color: #999;
        }
    </style>
<div style="margin: 0 auto;padding: 10px 20px;font-size: 12px;">
   <div class="plan-panel">
        <div class="text-center">
            <div class="panel-title">回款计划维护表</div>
            <div style="margin:20px 0;">
                <select style="width:80px;margin-bottom: 0;" id="planYear">
                {foreach $yearList as $year}
                <option value="{$year}">{$year}</option>
                {/foreach}
                </select>
                <span style="margin: 0 10px;">年</span>
                <select style="width:80px;margin-bottom: 0;" id="planMonth">
                    <option value="0">请选择</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                    <option value="11">11</option>
                    <option value="12">12</option>
                </select>
                <span style="margin: 0 10px;">月</span>
                <button type="button" class="layui-btn layui-btn-normal btn-confirm" style="margin-left: 30px;" disabled>数据生效</button>
            </div>
        </div>
        <table class="table table-bordered" style="min-width: 1420px;overflow: auto;">
            <thead>
            <tr>
                <th>一级分类</th>
                <th>二级分类</th>
                <th>1月计划回款数</th>
                <th>2月计划回款数</th>
                <th>3月计划回款数</th>
                <th>4月计划回款数</th>
                <th>5月计划回款数</th>
                <th>6月计划回款数</th>
                <th>7月计划回款数</th>
                <th>8月计划回款数</th>
                <th>9月计划回款数</th>
                <th>10月计划回款数</th>
                <th>11月计划回款数</th>
                <th>12月计划回款数</th>
            </tr>
            </thead>
            <tbody>
            {foreach $ruleList as $parentname=>$prule}
                {assign var = rulenunm value = count($prule)}
                {foreach $prule as $k=>$rule}
                    <tr id="plan{$rule['id']}" data-id="{$rule['id']}">
                        {if $k eq 0}
                            <td rowspan="{$rulenunm}">{$parentname}</td>
                        {/if}
                        <td>{$rule['name']}</td>
                        <td class="month month-1"></td>
                        <td class="month month-2"></td>
                        <td class="month month-3"></td>
                        <td class="month month-4"></td>
                        <td class="month month-5"></td>
                        <td class="month month-6"></td>
                        <td class="month month-7"></td>
                        <td class="month month-8"></td>
                        <td class="month month-9"></td>
                        <td class="month month-10"></td>
                        <td class="month month-11"></td>
                        <td class="month month-12"></td>
                    </tr>
                {/foreach}
                {foreachelse}
                <tr><td colspan="14" style="text-align: center">暂无数据</td></tr>
            {/foreach}
            </tbody>
        </table>
    </div>
    <div class="rule-panel">
        <div class="text-center">
            <div class="panel-title">分类规则维护表</div>
        </div>
        <div style="display:flex;justify-content:center;align-items:center;">
            <table class="table table-bordered"style="width:1280px">
                <thead>
                <tr>
                    <th>一级分类</th>
                    <th>二级分类</th>
                    <th>已匹配合同-数据筛选1</th>
                    <th>已匹配合同-数据筛选2</th>
                    <th>未匹配合同-数据筛选2</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                    {foreach $ruleList as $parentname=>$prule}
                        {assign var = rulenunm value = count($prule)}
                        {foreach $prule as $k=>$rule}
                        <tr data-id="{$rule['id']}">
                            {if $k eq 0}
                            <td rowspan="{$rulenunm}">{$parentname}</td>
                            {/if}
                            <td>{$rule['name']}</td>
                            <td>
                                {if isset($ruleContractList[$rule['id']])}
                                {foreach $ruleContractList[$rule['id']] as $item}
                                <div>{$item['contract_prefix']}</div>
                                {/foreach}
                                {/if}
                            </td>
                            <td>
                                {if isset($ruleDepartmentList[$rule['id']])}
                                    {foreach $ruleDepartmentList[$rule['id']] as $item}
                                        <div>{$item['parentname']}-{$item['departmentname']}</div>
                                    {/foreach}
                                {/if}
                            </td>
                            <td>
                                {if isset($ruleCompanyAccountList[$rule['id']])}
                                    {foreach $ruleCompanyAccountList[$rule['id']] as $item}
                                        <div>{$item['accountname']}</div>
                                    {/foreach}
                                {/if}
                            </td>
                            <td><span class="fa fa-close btn-delete"></span><span class="btn-edit">编辑规则</span></td>
                        </tr>
                        {/foreach}
                    {foreachelse}
                        <tr><td colspan="12" style="text-align: center">暂无数据</td></tr>
                    {/foreach}
                </tbody>
            </table>
            <button type="button" class="layui-btn layui-btn-normal btn-add" style="margin-left: 30px">增加分类行</button>
        </div>
    </div>
</div>
    <div id="add-layer" style="display:none;padding: 20px">
        <div>
            <span style="display: inline-block;width: 90px;text-align: right">一级分类：</span>
            <select name="parentype" id="parentype" style="width:200px">
            {if $topType}
            {foreach $topType as $value}
                <option value="{$value}">{$value}</option>
            {/foreach}
            {/if}
            </select>
        </div>
        <div>
            <span style="display: inline-block;width: 90px;text-align: right">二级分类名称：</span>
            <input type="text" maxlength="20" name="typename" id="typename" style="width:186px">
        </div>
    </div>
    <div id="edit-layer" style="display:none;padding: 20px;max-height:500px;overflow-y:auto;">
        <div style="display:flex;align-items:center;">
            <span style="font-weight: bold">二级分类名称：</span>
            <input type="hidden" id="ruleid">
            <input type="text" maxlength="20" name="typename" id="typename">
        </div>
        <div style="display:flex;margin-top: 10px;">
            <div style="margin-right:20px;width: 170px;">
                <div class="title">已匹配合同—数据筛选层级1</div>
                <span style="display:inline-block;width:190px;text-align:center;margin-bottom: 5px">服务合同</span>
                <ul class="contract-list"></ul>
            </div>
            <div style="370px;">
                <div class="title">已匹配合同—数据筛选层级2</div>
                <div> <span style="display:inline-block;width:170px;text-align:center;margin-bottom: 5px">一级架构</span> <span style="display:inline-block;width:170px;text-align:center;margin-bottom: 5px">二级架构</span></div>
                <ul class="department-list"></ul>
            </div>
        </div>
        <div style="width: 560px">
            <div class="title">未匹配合同—数据筛选层级2</div>
            <div><span style="display:inline-block;width:280px;text-align:center;margin-bottom: 5px">主体</span><span style="display:inline-block;width:280px;text-align:center;margin-bottom: 5px">账户</span></div>
            <ul class="companyaccount-list"></ul>
        </div>
        <div style="display:flex;align-items:center;margin-top: 10px;">
            <span style="font-weight: bold">数据刷新范围：</span><input type="text" id="startmonth" style="width: 120px;" readonly><span style="margin: 0 5px">至</span><input type="text" id="endmonth" style="width: 120px;" readonly> <span class="fa fa-warning" style="margin-left: 10px;color:#333"></span> 最晚至变更当月
        </div>
    </div>
    <link rel="stylesheet" type="text/css" href="libraries/jquery/layui/css/layui.css">
    <script type="text/javascript" src="libraries/jquery/layui/layui.js"></script>
    <script>
        var layer;
        var planMonth;
        var deparmentOptionList;
        var companyAccountOptionList;
        layui.use(['layer', 'laydate'], function(){
            layer = layui.layer;
            laydate = layui.laydate;
            laydate.render({
                elem: '#startmonth,#endmonth',
                type: 'month',
                min: '2010-01-01',
                max: '{date('Y-m-d')}',
                btns: ['clear', 'confirm']
            });
        });
        getPlanList();
        $(document).on('change', '.plan-panel #planYear', function () {
            $('.plan-panel #planMonth').val(0);
            getPlanList();
            $('.plan-panel .btn-confirm').attr('disabled', true);
        }).on('change', '.plan-panel #planMonth', function () {
            if(planMonth > 0) {
                $('.month-' + planMonth).each(function() {
                    $(this).html($(this).data('amount'));
                });
            }
            planMonth = $(this).val();
            if(planMonth > 0) {
                $('.month-' + planMonth).each(function() {
                    $(this).html('<input type="number" class="input-amount" min="0" value="'+$(this).data('amount')+'">');
                });
                $('.plan-panel .btn-confirm').attr('disabled', false);
            } else {
                $('.plan-panel .btn-confirm').attr('disabled', true);
            }
            $(this).blur();
        }).on('click', '.plan-panel .btn-confirm', function () {
            if(planMonth>0) {
                var check = true;
                var amountList = [];
                $('.month-' + planMonth).each(function() {
                    var amount = $(this).children('input').val();
                    if (amount.length==0) {
                        amount=0;
                    }
                    var id = $(this).parents('tr').data('id');
                    amountList.push({ id:id, amount:amount });
                });
                $.ajax({
                    url:'index.php',
                    type:'POST',
                    data: {
                        module: 'ReceivedPaymentsClassify',
                        action: 'BasicAjax',
                        mode: 'setReceivedPaymentsPlan',
                        year: $('#planYear').val(),
                        month: planMonth,
                        amountList: amountList
                    },
                    beforeSend:function() {
                        layer.load();
                    },
                    complete:function() {
                        layer.closeAll('loading');
                    },
                    success:function (data) {
                        if (data.result.status == 'success') {
                            $('.plan-panel .btn-confirm').attr('disabled', true);
                            layer.msg(data.result.msg, { icon: 1,time: 1500 });
                            $('.month-' + planMonth).each(function() {
                                var amount = $(this).children('input').val();
                                if(amount.length==0) {
                                    amount = 0;
                                }
                                $(this).html(amount).data('amount', amount);
                            });
                            $('.plan-panel #planMonth').val(0);
                        } else {
                            layer.msg(data.result.msg, { icon: 2,time: 1500 });
                        }
                    }
                });
            }
        }).on('click', '.rule-panel .btn-add', function () {
            layer.open({
                type:1,
                resize:false,
                title:'增加分类行',
                content: $('#add-layer'),
                btn: ['确认', '取消'],
                btnAlign: 'c',
                yes: function(index, layero) {
                    var parentype = $('#add-layer #parentype').val();
                    var typename = $('#add-layer #typename').val();
                    if(typename.length==0) {
                        layer.msg('请输入二级分类名称',{ icon: 0,time: 1500 });
                        return false;
                    }
                    $.ajax({
                        url:'index.php',
                        type:'POST',
                        data: {
                            module: 'ReceivedPaymentsClassify',
                            action: 'BasicAjax',
                            mode: 'addClassifyRule',
                            parentype: parentype,
                            typename: typename
                        },
                        beforeSend:function(){
                            layer.load();
                        },
                        complete:function() {
                            layer.closeAll('loading');
                        },
                        success:function (data) {
                            if (data.result.status == 'success') {
                                layer.closeAll('page');
                                layer.msg(data.result.msg, { icon: 1,time: 1500 });
                                window.location.reload();
                            } else {
                                layer.msg(data.result.msg, { icon: 2,time: 1500 });
                            }
                        }
                    })
                },
                btn2: function(index, layero){
                    layer.close(index);
                }
            });
        }).on('click', '.rule-panel .btn-delete', function () {
            var id = $(this).parents('tr').data('id');
            layer.confirm('执行行删除后，数据将进行重新分类，未配置<br>规则的数据将全部刷新至“系统粗筛”结果为空。', { title:'是否确认删除' }, function(index){
                layer.close(index);
                $.ajax({
                    url:'index.php',
                    type:'POST',
                    data: {
                        module: 'ReceivedPaymentsClassify',
                        action: 'BasicAjax',
                        mode: 'delClassifyRule',
                        id: id
                    },
                    beforeSend:function() {
                        layer.load();
                    },
                    complete:function() {
                        layer.closeAll('loading');
                    },
                    success:function (data) {
                        if (data.result.status == 'success') {
                            layer.msg(data.result.msg, { icon: 1,time: 1500 });
                            window.location.reload();
                        } else {
                            layer.msg(data.result.msg, { icon: 2,time: 1500 });
                        }
                    }
                })
            });
        }).on('click', '.rule-panel .btn-edit', function () {
            var ruleid = $(this).parents('tr').data('id');
            $.ajax({
                url:'index.php',
                type:'POST',
                data: {
                    module: 'ReceivedPaymentsClassify',
                    action: 'BasicAjax',
                    mode: 'getReceivedPaymentsRule',
                    ruleid: ruleid
                },
                beforeSend:function() {
                    layer.load();
                },
                complete:function() {
                    layer.closeAll('loading');
                },
                success:function (data) {
                    if (data.result.status == 'success') {
                        var contractList = data.result.ruleInfo.contractList;
                        var contractOptionList = data.result.contractOptionList;
                        var departmentList = data.result.ruleInfo.departmentList;
                        deparmentOptionList = data.result.deparmentOptionList;
                        var companyAccountList = data.result.ruleInfo.companyAccountList;
                        companyAccountOptionList = data.result.companyAccountOptionList;
                        $('#edit-layer .contract-list').empty();
                        $('#edit-layer .department-list').empty();
                        $('#edit-layer .companyaccount-list').empty();
                        $('#edit-layer #ruleid').val(data.result.ruleInfo.id);
                        $('#edit-layer #typename').val(data.result.ruleInfo.name);
                        $('#edit-layer #startmonth').val(data.result.ruleInfo.startmonth);
                        $('#edit-layer #endmonth').val(data.result.ruleInfo.endmonth);
                        /* 合同编号 start */
                        var listr = '<li><select multiple>';
                        for (const k in contractOptionList) {
                            if (contractList.includes(contractOptionList[k])) {
                                listr += '<option value="' + contractOptionList[k] + '" selected>' + contractOptionList[k] + '</option>';
                            } else {
                                listr += '<option value="' + contractOptionList[k] + '">' + contractOptionList[k] + '</option>';
                            }
                        }
                        listr +='</select></li>';
                        $('#edit-layer .contract-list').append(listr);
                        $('#edit-layer .contract-list select').chosen();
                        /* 合同编号 end */
                        /* 所属部门 start */
                        if (departmentList.length>0) {
                            for (const key in departmentList) {
                                var listr = '<li><i class="fa fa-plus-circle"></i><select class="select-department-lv1"><option value="">请选择一级部门</option>';
                                for (const k in deparmentOptionList) {
                                    if (departmentList[key]['parentid'] == deparmentOptionList[k]['departmentid']) {
                                        listr += '<option value="' + deparmentOptionList[k]['departmentid'] + '" selected>' + deparmentOptionList[k]['departmentname']+ '</option>';
                                    } else {
                                        listr += '<option value="' + deparmentOptionList[k]['departmentid'] + '">' + deparmentOptionList[k]['departmentname']+ '</option>';
                                    }
                                }
                                listr += '</select><select class="select-department-lv2">';
                                if(departmentList[key]['parentid'] && deparmentOptionList[departmentList[key]['parentid']]) {
                                    if(deparmentOptionList[departmentList[key]['parentid']]['hasAll']) {
                                        listr += '<option value="">全部</option>';
                                    }
                                    var subDeparmentOptionList = deparmentOptionList[departmentList[key]['parentid']]['children'];
                                    for (const k in subDeparmentOptionList) {
                                        if (departmentList[key]['departmentid'] == subDeparmentOptionList[k]['departmentid']) {
                                            listr += '<option value="' + subDeparmentOptionList[k]['departmentid'] + '" selected>' + subDeparmentOptionList[k]['departmentname'] + '</option>';
                                        } else {
                                            listr += '<option value="' + subDeparmentOptionList[k]['departmentid'] + '">' + subDeparmentOptionList[k]['departmentname'] + '</option>';
                                        }
                                    }
                                }
                                listr += '</select><i class="fa fa-minus-circle"></i></li>';
                                $('#edit-layer .department-list').append(listr);
                            }
                        } else {
                            var listr = '<li><i class="fa fa-plus-circle"></i><select class="select-department-lv1"><option value="">请选择一级部门</option>';
                            for (const k in deparmentOptionList) {
                                listr += '<option value="' + deparmentOptionList[k]['departmentid'] + '">' + deparmentOptionList[k]['departmentname']+ '</option>';
                            }
                            listr += '</select><select class="select-department-lv2"></select><i class="fa fa-minus-circle"></i></li>';
                            $('#edit-layer .department-list').append(listr);
                        }
                        /* 所属部门 end */
                        /* 主体公司账号 start */
                        if (companyAccountList.length>0) {
                            for (const key in companyAccountList) {
                                var listr = '<li><i class="fa fa-plus-circle"></i><select class="select-company"><option value="">请选择主体公司</option>';
                                for (const k in companyAccountOptionList) {
                                    if (companyAccountList[key]['company'] == k) {
                                        listr += '<option value="' + k + '" selected>' + k + '</option>';
                                    } else {
                                        listr += '<option value="' + k + '">' + k + '</option>';
                                    }
                                }
                                listr += '</select><select class="select-account">';
                                if(companyAccountList[key]['company'] && companyAccountOptionList[companyAccountList[key]['company']]) {
                                    if(companyAccountOptionList[companyAccountList[key]['company']]['hasAll']) {
                                        listr += '<option value="0">全部</option>';
                                    }
                                    var accountOptionList = companyAccountOptionList[companyAccountList[key]['company']]['children'];
                                    for (const k in accountOptionList) {
                                        if (companyAccountList[key]['companyaccountid'] == accountOptionList[k]['id']) {
                                            listr += '<option value="' + accountOptionList[k]['id'] + '" selected>' + accountOptionList[k]['accountname'] + '</option>';
                                        } else {
                                            listr += '<option value="' + accountOptionList[k]['id'] + '">' + accountOptionList[k]['accountname'] + '</option>';
                                        }
                                    }
                                }
                                listr += '</select><i class="fa fa-minus-circle"></i></li>';
                                $('#edit-layer .companyaccount-list').append(listr);
                            }
                        } else {
                            var listr = '<li><i class="fa fa-plus-circle"></i><select class="select-company"><option value="">请选择主体公司</option>';
                            for (const k in companyAccountOptionList) {
                                listr += '<option value="' + k + '">' + k + '</option>';
                            }
                            listr += '</select><select class="select-account"></select><i class="fa fa-minus-circle"></i></li>';
                            $('#edit-layer .companyaccount-list').append(listr);
                        }
                        /* 主体公司账号 end */
                        layer.open({
                            type:1,
                            resize:false,
                            title:'编辑规则',
                            content: $('#edit-layer'),
                            btn: ['确认', '取消'],
                            btnAlign: 'c',
                            yes: function(index, layero){
                                var ruleid = $('#edit-layer #ruleid').val();
                                var typename =  $('#edit-layer #typename').val();
                                var startmonth =  $('#edit-layer #startmonth').val();
                                var endmonth =  $('#edit-layer #endmonth').val();
                                var contactList = [];
                                var departmentList = [];
                                var accountList = [];
                                if(typename.length==0) {
                                    layer.msg('二级分类名称不能为空', { icon: 0,time: 1500 });
                                    return false;
                                }
                                if(startmonth.length==0) {
                                    layer.msg('数据刷新开始月份不能为空', { icon: 0,time: 1500 });
                                    return false;
                                }
                                if(endmonth.length==0) {
                                    layer.msg('数据刷新结束月份不能为空', { icon: 0,time: 1500 });
                                    return false;
                                }
                                contactList = $('#edit-layer .contract-list select').val();
                                var repeat = false;
                                var departmentListStr=',';
                                $('#edit-layer .department-list li').each(function () {
                                    var parentId = $(this).find('.select-department-lv1').val();
                                    var departmentId = $(this).find('.select-department-lv2').val();
                                    if (parentId.length > 0) {
                                        if(departmentListStr.indexOf(','+parentId+'-'+departmentId+',')>-1) {
                                            repeat = true;
                                            return false;
                                        }
                                        departmentListStr += parentId+'-'+departmentId+',';
                                        departmentList.push(parentId+'-'+departmentId);
                                    }
                                });
                                if(repeat == true) {
                                    layer.msg('所选部门不能重复', { icon: 0,time: 1500 });
                                    return false;
                                }
                                var accountListStr=',';
                                $('#edit-layer .companyaccount-list li').each(function () {
                                    var company = $(this).find('.select-company').val();
                                    var accountId = $(this).find('.select-account').val();
                                    if (company.length > 0) {
                                        if(accountListStr.indexOf(','+company+'-'+accountId+',')>-1) {
                                            console.log(accountListStr);
                                            console.log(company+'-'+accountId+',');
                                            repeat = true;
                                            return false;
                                        }
                                        accountListStr += company+'-'+accountId+',';
                                        accountList.push(company+'-'+accountId);
                                    }
                                });
                                if(repeat == true) {
                                    layer.msg('所选主体公司账号不能重复', { icon: 0,time: 1500 });
                                    return false;
                                }
                                $.ajax({
                                    url:'index.php',
                                    type:'POST',
                                    data: {
                                        module: 'ReceivedPaymentsClassify',
                                        action: 'BasicAjax',
                                        mode: 'saveReceivedPaymentsRule',
                                        ruleid: ruleid,
                                        name: typename,
                                        contactList: contactList,
                                        departmentList: departmentList,
                                        accountList: accountList,
                                        startmonth: startmonth,
                                        endmonth: endmonth
                                    },
                                    beforeSend:function() {
                                        layer.load();
                                    },
                                    complete:function() {
                                        layer.closeAll('loading');
                                    },
                                    success:function (data) {
                                        if (data.result.status == 'success') {
                                            layer.msg(data.result.msg, { icon: 1,time: 1500 });
                                            window.location.reload();
                                        } else {
                                            layer.msg(data.result.msg, { icon: 2,time: 1500 });
                                        }
                                    }
                                })
                            },
                            btn2: function(index, layero){
                                layer.close(index);
                            }
                        });
                    } else {
                        layer.msg(data.result.msg, { icon: 2,time: 1500 });
                    }
                }
            });
        }).on('click', '.department-list .fa-plus-circle', function () {
            if($(this).parents('.department-list').find('li').length>=30) {
                layer.msg('至多允许添加30项', { icon: 0,time: 1500 });
                return false;
            }
            var li = $(this).parent();
            var newli = li.clone();
            newli.find('.select-department-lv1').val('');
            newli.find('.select-department-lv2').empty();
            li.after(newli);
        }).on('click', '.department-list .fa-minus-circle', function () {
            if($(this).parents('.department-list').find('li').length==1) {
                layer.msg('至少需保留一项', { icon: 0,time: 1500 });
                return false;
            }
           $(this).parent().remove();
        }).on('click', '.companyaccount-list .fa-plus-circle', function () {
            if($(this).parents('.companyaccount-list').find('li').length>=30) {
                layer.msg('至多允许添加30项', { icon: 0,time: 1500 });
                return false;
            }
            var li = $(this).parent();
            var newli = li.clone();
            newli.find('.select-company').val('');
            newli.find('.select-account').empty();
            li.after(newli);
        }).on('click', '.companyaccount-list .fa-minus-circle', function () {
            if($(this).parents('.companyaccount-list').find('li').length==1) {
                layer.msg('至少需保留一项', { icon: 0,time: 1500 });
                return false;
            }
           $(this).parent().remove();
        }).on('change', '.select-department-lv1', function () {
           var parentDep = $(this).val();
           var subSelect = $(this).next();
            subSelect.empty();
            var listr = '';
           if (parentDep.length>0) {
               if(deparmentOptionList[parentDep]['hasAll']) {
                    listr += '<option value="0">全部</option>';
               }
               var subDeparmentOptionList = deparmentOptionList[parentDep]['children'];
               for (const k in subDeparmentOptionList) {
                    listr += '<option value="' + subDeparmentOptionList[k]['departmentid'] + '">' + subDeparmentOptionList[k]['departmentname']+ '</option>';
               }
            }
            subSelect.append(listr);
        }).on('change', '.select-company', function () {
            var conpany = $(this).val();
            var subSelect = $(this).next();
            subSelect.empty();
            var listr = '';
            if (conpany.length>0) {
                if(companyAccountOptionList[conpany]['hasAll']) {
                    listr += '<option value="0">全部</option>';
                }
                var accountOptionList = companyAccountOptionList[conpany]['children'];
                for (const k in accountOptionList) {
                    listr += '<option value="' + accountOptionList[k]['id'] + '">' + accountOptionList[k]['accountname']+ '</option>';
                }
            }
            subSelect.append(listr);
        });
        function getPlanList()
        {
            var planYear = $('#planYear').val();
            $.ajax({
                url:'index.php',
                type:'POST',
                data: {
                    module: 'ReceivedPaymentsClassify',
                    action: 'BasicAjax',
                    mode: 'getPlanList',
                    year: planYear,
                },
                beforeSend:function() {
                    layer.load();
                },
                complete:function() {
                    layer.closeAll('loading');
                },
                success:function (data) {
                    $('.plan-panel .month').data('amount', '').html('');
                    if(data.result) {
                        $.each(data.result, function(index, item){
                            var tr = $('#plan' + item['ruleid']);
                            if(tr) {
                                var td = tr.find('.month-'+item['month']);
                                if(td) {
                                    td.data('amount', item['amount']).html(item['amount']);
                                }
                            }
                        });
                    }
                }
            })
        }
    </script>
{/strip}
