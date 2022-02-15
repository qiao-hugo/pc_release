/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("RSalescomparison_List_Js",{},{
    //初始化
    loading:function(){
        $('.chzn-selectq').chosen();
        var thisInstance=this;
        var postData={};
        postData={'module':app.getModuleName(),
            'action':'selectAjax',
            'mode':'getCountsday'
        };
        var progressIndicatorElement = jQuery.progressIndicator({
            'message' : '正在拼命努力加载,请耐心等待哟',
            'position' : 'html',
            'blockInfo' : {'enabled' : true}
        });
        AppConnector.request(postData).then(
            function(data) {
                progressIndicatorElement.progressIndicator({
                    'mode' : 'hide'
                });
                if (data.success) {
                    var arr = new Array;
                    //合同
                    arr['Contracts'] = new Array();
                    arr['Contracts']['returntime'] = new Array();//月份
                    arr['Contracts']['department'] = new Array();
                    //合同平均
                    arr['Contractsavg'] = new Array();//通知
                    arr['Contractsavg']['returntime'] = new Array();//通知
                    arr['Contractsavg']['department'] = new Array();
                    //临时存放对应的值
                    var narr = new Array;
                    narr['Contracts'] = new Array();
                    narr['Contractsavg'] = new Array();
                    //回款
                    arr['Payment'] = new Array();
                    arr['Payment']['returntime'] = new Array();//月份
                    arr['Payment']['department'] = new Array();
                    //回款平均
                    arr['Paymentavg'] = new Array();//通知
                    arr['Paymentavg']['returntime'] = new Array();//通知
                    arr['Paymentavg']['department'] = new Array();
                    //临时存放对应的值
                    narr['Payment'] = new Array();
                    narr['Paymentavg'] = new Array();
                    arr['newdepartment'] = thisInstance.transobjtoarr(data.result.newdepartment);
                    arr['newdepartment'].unshift('');//加入空元素可强制换行
                    arr['ndepartmentid']=new Array()//加入空元素可强制换行
                    if (Object.keys(data.result).length > 0) {
                        $.each(data.result, function (kt, valuet) {
                            if (kt =='newdepartment') {
                                $.each(valuet, function (ktt, valuet) {
                                    narr['Contracts'][ktt]=new Array();
                                    narr['Contractsavg'][ktt]=new Array();
                                    narr['Payment'][ktt]=new Array();
                                    narr['Paymentavg'][ktt]=new Array();
                                    arr['ndepartmentid'][valuet]=ktt;
                                });
                            }
                        });
                        $.each(data.result, function (k, value) {
                            if (k != 'newdepartment') {
                                $.each(value, function (M, val) {
                                    $.each(val, function (N, v) {
                                        if(isNaN(N)){
                                            if(N=='returntime'){
                                                arr[k]['returntime'].push(v);
                                            }else{
                                                narr[k][N][M]=v;
                                            }
                                        }
                                    });
                                });
                            }
                        });
                        for(var key1 in narr){
                            for(var key2 in narr[key1]){
                                arr[key1]['department'].push({
                                    name: data.result.newdepartment[key2],
                                    barMaxWidth:60,
                                    type: 'bar',
                                    data: narr[key1][key2]
                                });
                            }
                        }
                    }
                    $("#listc").hide();
                    $("#listr").hide();
                    thisInstance.echartscon(arr);
                }
                },
                function (error, err) {

                }
        );
    },
    //加载柱状图
    echartscon:function(params){
        var thisInstance=this;
        require.config({
            paths: {
                echarts: '/libraries/echarts'
            }
        });
        require(
            [
                'echarts',
                'echarts/chart/bar',
                'echarts/chart/line'
            ],
            function (ec) {
                var myChart = ec.init(document.getElementById('bartable'));
                myChart.setOption({
                    title : {
                        text: '合同金额\n',
                        x:'center'
                    },
                    tooltip : {
                        trigger: 'axis',
                        axisPointer : {            // 坐标轴指示器，坐标轴触发有效
                            type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
                        }
                    },
                    legend: {
                        data:params.newdepartment
                    },
                    toolbox: {
                        show : true,
                        //orient: 'vertical',
                        //x: 'right',
                        //y: 'center',
                        feature : {
                            //mark : {show: true},
                            //dataView : {show: true, readOnly: false},
                            magicType : {show: true, type: ['line', 'bar', 'stack', 'tiled']},
                            restore : {show: true}
                            //saveAsImage : {show: true}
                        }
                    },
                    calculable : true,
                    xAxis : [
                        {
                            type : 'category',
                            data : params.Contracts.returntime
                        }
                    ],
                    yAxis : [
                        {
                            type : 'value'
                        }
                    ],
                    series :
                        params.Contracts.department


                },true);
                var ecConfig= require('echarts/config');
                myChart.on(ecConfig.EVENT.CLICK,function (param) {
                    if(params['ndepartmentid'][param.seriesName]==''){
                        return ;
                    }
                    var postData={};
                    var datetime=$('#timeslot').val();
                    postData={
                        'module':app.getModuleName(),
                        'action':'selectAjax',
                        'mode':'getcontractdetaillist',
                        'paramname':param.seriesName+'　合同明细',
                        'datauserid':params['ndepartmentid'][param.seriesName].replace(/user/,''),//不知道那个那个参数可转存,做一个笨方法来转存方便取得用用户ID
                        'datetime':param.name
                    };
                    thisInstance.gettables(postData)

                });
                var myChartavg = ec.init(document.getElementById('bartableavg'));
                myChartavg.setOption({
                    title : {
                        text: '合同金额部门平均\n',
                        x:'center'
                    },
                    tooltip : {
                        trigger: 'axis',
                        axisPointer : {            // 坐标轴指示器，坐标轴触发有效
                            type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
                        }
                    },
                    legend: {
                        data:params.newdepartment
                    },
                    toolbox: {
                        show : true,
                        feature : {
                            magicType : {show: true, type: ['line', 'bar', 'stack', 'tiled']},
                            restore : {show: true}
                        }
                    },
                    calculable : true,
                    xAxis : [
                        {
                            type : 'category',
                            data : params.Contractsavg.returntime
                        }
                    ],
                    yAxis : [
                        {
                            type : 'value'
                        }
                    ],
                    series :
                        params.Contractsavg.department


                },true);
                var ecConfig= require('echarts/config');

                myChartavg.on(ecConfig.EVENT.CLICK,function (param) {
                    if(params['ndepartmentid'][param.seriesName]==''){
                        return ;
                    }
                    var postData={};
                    var datetime=$('#timeslot').val();
                    postData={
                        'module':app.getModuleName(),
                        'action':'selectAjax',
                        'mode':'getcontractdetaillist',
                        'paramname':param.seriesName+'　合同明细',
                        'datauserid':params['ndepartmentid'][param.seriesName].replace(/user/,''),//不知道那个那个参数可转存,做一个笨方法来转存方便取得用用户ID
                        'datetime':param.name
                    };
                    thisInstance.gettables(postData)

                });
                var myCharts = ec.init(document.getElementById('bartables'));
                myCharts.setOption({
                    title : {
                        text: '有效回款业绩\n',
                        x:'center'
                    },
                    tooltip : {
                        trigger: 'axis',
                        axisPointer : {            // 坐标轴指示器，坐标轴触发有效
                            type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
                        }
                    },
                    legend: {
                        data:params.newdepartment
                    },
                    toolbox: {
                        show : true,
                        feature : {
                            magicType : {show: true, type: ['line', 'bar', 'stack', 'tiled']},
                            restore : {show: true}
                        }
                    },
                    calculable : true,
                    xAxis : [
                        {
                            type : 'category',
                            data : params.Payment.returntime
                        }
                    ],
                    yAxis : [
                        {
                            type : 'value'
                        }
                    ],
                    series :
                        params.Payment.department


                },true);
                var ecConfig1= require('echarts/config');
                myCharts.on(ecConfig1.EVENT.CLICK,function (param) {
                    if(params['ndepartmentid'][param.seriesName]==''){
                        return ;
                    }
                    var postData={};
                    var departmentid=$('#department_editView_fieldName_dropDown').val();
                    var userid=$('#user_editView_fieldName_dropDown').val();
                    var datetime=$('#datatime').val();
                    var enddatetime=$('#enddatatime').val();
                    postData={
                        'module':app.getModuleName(),
                        'action':'selectAjax',
                        'mode':'getpaymentdetaillist',
                        'paramname':param.seriesName+'　有效回款金额',
                        'datauserid':params['ndepartmentid'][param.seriesName].replace(/user/,''),//不知道那个那个参数可转存,做一个笨方法来转存方便取得用用户ID
                        'datetime':param.name
                    };
                    thisInstance.gettables(postData);
                });
                var myChartavgs = ec.init(document.getElementById('bartableavgs'));
                myChartavgs.setOption({
                    title : {
                        text: '有效回款业绩部门平均\n',
                        x:'center'
                    },
                    tooltip : {
                        trigger: 'axis',
                        axisPointer : {            // 坐标轴指示器，坐标轴触发有效
                            type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
                        }
                    },
                    legend: {
                        data:params.newdepartment
                    },
                    toolbox: {
                        show : true,
                        feature : {
                            magicType : {show: true, type: ['line', 'bar', 'stack', 'tiled']},
                            restore : {show: true}
                        }
                    },
                    calculable : true,
                    xAxis : [
                        {
                            type : 'category',
                            data : params.Paymentavg.returntime
                        }
                    ],
                    yAxis : [
                        {
                            type : 'value'
                        }
                    ],
                    series :
                        params.Paymentavg.department


                },true);
                myChartavgs.on(ecConfig1.EVENT.CLICK,function (param) {
                    if(params['ndepartmentid'][param.seriesName]==''){
                        return ;
                    }
                    var postData={};
                    var departmentid=$('#department_editView_fieldName_dropDown').val();
                    var userid=$('#user_editView_fieldName_dropDown').val();
                    var datetime=$('#datatime').val();
                    var enddatetime=$('#enddatatime').val();
                    postData={
                        'module':app.getModuleName(),
                        'action':'selectAjax',
                        'mode':'getpaymentdetaillist',
                        'paramname':param.seriesName+'　有效回款金额',
                        'datauserid':params['ndepartmentid'][param.seriesName].replace(/user/,''),//不知道那个那个参数可转存,做一个笨方法来转存方便取得用用户ID
                        'datetime':param.name
                    };
                    thisInstance.gettables(postData);
                });
            }
        );
    },

    //部门更改显示下面的用户
    departmentchange:function(){
        $('table').on('change','#department_editView_fieldName_dropDown',function(){
            var departmentid=$(this).val();
            var param={};
            param={
                'module':app.getModuleName(),
                'action':'selectAjax',
                'mode':'getUsers',
                'department':departmentid
            };
            $('#user_editView_fieldName_dropDown').empty();
            AppConnector.request(param).then(
                function(data){
                    if(data.success){
                        if(data.result.length>0){
                            var str='';
                            $.each(data.result,function(i,val){
                                str+='<option value="'+val.id+'">'+val.last_name+'</option>';
                            });
                            $('#user_editView_fieldName_dropDown').empty();
                            $('#user_editView_fieldName_dropDown').append('<option value="">请选择一项</option>'+str);
                        }
                        $('.chzn-select').trigger("liszt:updated");
                    }
                }
            )
        });
    },
    timeslotchange:function(){
        var thisInstance=this;
      $("table").on('change','#timeslot',function(){
          $('.timecode').remove();
          $('.datetimepicker').remove();
          if($(this).val()==15){
              var mydate=new Date();
              var timecode='<label class="pull-left timecode" style="width:220px;"><input class="span12 dateField"type="text"  id="datatime" value="'+mydate.getFullYear()+'-'+(mydate.getMonth()+1)+'-'+mydate.getDate()+'" readonly></label><label class="pull-left timecode" style="margin:5px 10px 0;">到</label><label class="pull-left timecode" style="width:220px;"><input class="span12 dateField"  type="text" name="enddatatime" data-date-format="yyyy-mm-dd" id="enddatatime" value="'+mydate.getFullYear()+'-'+(mydate.getMonth()+1)+'-'+mydate.getDate()+'" readonly></label>';
              $(this).parent().parent().append(timecode);
              thisInstance.getdatetime();
          }

      });
    },
    submitconfim:function(){
        var thisInstance=this;
        $('table').on('click','#PostQuery',function(){
            $('#detailtable').empty();
            //$('#bartable').empty();
            $('#msg').empty();
            var departmentid=$('#department_editView_fieldName_dropDown').val();
            var userid=$('#user_editView_fieldName_dropDown').val();
            var datetime=$('#timeslot').val();
            var startdatetime=$('#datatime').val();
            var enddatetime=$('#enddatatime').val();
            postData= {
                'module': app.getModuleName(),
                'action': 'selectAjax',
                'mode': 'getCountsday',
                'department':departmentid,
                'datetime':datetime,
                'userdata':userid
            };
            var progressIndicatorElement = jQuery.progressIndicator({
                'message' : '正在拼命努力加载,请耐心等待哟',
                'position' : 'html',
                'blockInfo' : {'enabled' : true}
            });
            AppConnector.request(postData).then(
                function(data){
                    progressIndicatorElement.progressIndicator({
                        'mode' : 'hide'
                    });
                    if(data.success) {
                        var arr = new Array;
                        //合同
                        arr['Contracts'] = new Array();
                        arr['Contracts']['returntime'] = new Array();//月份
                        arr['Contracts']['department'] = new Array();
                        //合同平均
                        arr['Contractsavg'] = new Array();//通知
                        arr['Contractsavg']['returntime'] = new Array();//通知
                        arr['Contractsavg']['department'] = new Array();
                        //临时存放对应的值
                        var narr = new Array;
                        narr['Contracts'] = new Array();
                        narr['Contractsavg'] = new Array();
                        //回款
                        arr['Payment'] = new Array();
                        arr['Payment']['returntime'] = new Array();//月份
                        arr['Payment']['department'] = new Array();
                        //回款平均
                        arr['Paymentavg'] = new Array();//通知
                        arr['Paymentavg']['returntime'] = new Array();//通知
                        arr['Paymentavg']['department'] = new Array();
                        //临时存放对应的值
                        narr['Payment'] = new Array();
                        narr['Paymentavg'] = new Array();
                        arr['newdepartment'] = thisInstance.transobjtoarr(data.result.newdepartment);
                        arr['newdepartment'].unshift('');//加入空元素可强制换行
                        arr['ndepartmentid']=new Array()//加入空元素可强制换行
                        if (Object.keys(data.result).length > 0) {
                            $.each(data.result, function (kt, valuet) {
                                if (kt =='newdepartment') {
                                    $.each(valuet, function (ktt, valuet) {
                                        narr['Contracts'][ktt]=new Array();
                                        narr['Contractsavg'][ktt]=new Array();
                                        narr['Payment'][ktt]=new Array();
                                        narr['Paymentavg'][ktt]=new Array();
                                        arr['ndepartmentid'][valuet]=ktt;
                                    });
                                }
                            });
                            $.each(data.result, function (k, value) {
                                if (k != 'newdepartment') {
                                    $.each(value, function (M, val) {
                                        $.each(val, function (N, v) {
                                            if(isNaN(N)){
                                                if(N=='returntime'){
                                                    arr[k]['returntime'].push(v);
                                                }else{
                                                    narr[k][N][M]=v;
                                                }
                                            }
                                        });
                                    });
                                }
                            });
                            for(var key1 in narr){
                                for(var key2 in narr[key1]){
                                    arr[key1]['department'].push({
                                        name: data.result.newdepartment[key2],
                                        type: 'bar',
                                        barMaxWidth:60,
                                        data: narr[key1][key2]
                                    });
                                }
                            }
                        }
                        $("#listc").hide();
                        $("#listr").hide();
                        thisInstance.echartscon(arr);

                    }
                }
            )
        });
    },
    //加载下方列表
    gettables:function(postData){
        var thisInstance=this;
        var progressIndicatorElement = jQuery.progressIndicator({
            'message' : '正在拼命努力加载,请耐心等待哟',
            'position' : 'html',
            'blockInfo' : {'enabled' : true}
        });
        AppConnector.request(postData).then(
            function(data){
                progressIndicatorElement.progressIndicator({
                    'mode' : 'hide'
                });
                if(data.success) {
                    if(data.result.length>0){
                        var str='';
                        if(postData.mode=='getcontractdetaillist'){
                            $("#listc").show();
                            $("#listr").hide();
                            var div_detail='<table id="tbl_Detail" class="table listViewEntriesTable" width="100%"">' +
                                '<thead><tr><th nowrap><b>归还日期</b></th><th nowrap><b>负责人</b></th><th nowrap><b>客户名称</b></th><th nowrap><b>合同编号</b></th><th nowrap><b>合同类型</b></th><th nowrap><b>合同金额</b></th></tr></thead><tbody>';
                            $.each(data.result,function(i,val){
                                str+='<tr><td nowrap>'+val.returndate+'</td><td nowrap>'+val.receiveid+'</td><td nowrap><a href="index.php?module=Accounts&view=Detail&record='+val.sc_related_to_reference+'" target="view_window">'+val.sc_related_to+'</a></td><td nowrap><a href="index.php?module=ServiceContracts&view=Detail&record='+val.cid+'" target="view_window">'+val.contract_no+'</a></td><td nowrap>'+val.contract_type+'</td><td nowrap>'+val.total+'</td></tr>';
                            });
                            jQuery('#msgc').html('<span style="font-size:12px;color:green;text-align:left;">超过1000条不显示</span><span  style="text-align:center;display:block;font-size:16px;color:red;font-weight:bold;">'+postData.paramname+'</span>');
                            $('#detailtablec').html(div_detail+str+'</tbody></table>');
                            var tableid="tbl_Detail";
                        }else if(postData.mode=='getpaymentdetaillist'){
                            var div_detail='<table id="tbl_Detailr" class="table listViewEntriesTable" width="100%"">' +
                                '<thead><tr><th nowrap><b>回款日期</b></th><th nowrap><b>负责人</b></th><th nowrap><b>客户名称</b></th><th nowrap><b>合同编号</b></th><th nowrap><b>合同金额</b></th><th nowrap><b>回款金额(已分成)</b></th><th nowrap><b>外采成本(已分成)</b></th><th nowrap><b>有效回款</b></th></tr></thead><tbody>';
                            $.each(data.result,function(i,val){
                                str+='<tr><td nowrap>'+val.reality_date+'</td><td nowrap>'+val.last_name+'</td><td nowrap><a href="index.php?module=Accounts&view=Detail&record='+val.accountid+'" target="view_window">'+val.accountname+'</a></td><td nowrap><a href="index.php?module=ServiceContracts&view=Detail&record='+val.cid+'" target="view_window">'+val.contract_no+'</a></td><td nowrap>'+val.total+'</td><td nowrap>'+val.businessunit+'</td><td nowrap>'+val.sss+'</td><td nowrap>'+thisInstance.accSubtr(val.businessunit,val.sss)+'</td></tr>';
                            });
                            jQuery('#msgr').html('<span style="font-size:12px;color:green;text-align:left;">超过1000条不显示</span><span  style="text-align:center;display:block;font-size:16px;color:red;font-weight:bold;">'+postData.paramname+'</span>');
                            $('#detailtabler').html(div_detail+str+'</tbody></table>');
                            $("#listr").show();
                            $("#listc").hide();
                            var tableid="tbl_Detailr";
                        }
                        thisInstance.Tableinstance(tableid);

                    }else{
                        $('#detailtable').html('没有相关数据');
                    }

                } else {

                }
            },
            function(error,err){

            }
        );
    },
    /**
     * 减法计算
     * @param arg1被减法
     * @param arg2减数
     * @returns {string}差{子符串类型}
     */
    accSubtr:function(arg1,arg2) {
        var t1 = 0, t2 = 0, m, n;
        try {
            t1 = arg1.toString().split(".")[1].length;
        }
        catch (e) {
            t1 = 0;
        }
        try {
            t2 = arg2.toString().split(".")[1].length;
        }
        catch (e) {
            t2 = 0;
        }
        with (Math) {
            //动态控制精度长度
            n = Math.max(t1, t2);
            m = Math.pow(10, n);
            //return (arg1  * m - arg2 * m) / m;
            return ((arg1 * m - arg2 * m) / m).toFixed(n);
        }
    },
    Tableinstance:function(postData){
        var table = jQuery('#'+postData).DataTable({
            language: {"sProcessing":   "处理中...",	"sLengthMenu":   "显示 _MENU_ 项结果","sZeroRecords":  "没有匹配结果","sInfo":         "显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",	"sInfoEmpty":    "显示第 0 至 0 项结果，共 0 项","sInfoFiltered": "(由 _MAX_ 项结果过滤)","sInfoPostFix":  "","sSearch":"当前页快速检索:","sUrl":"","sEmptyTable":     "表中数据为空","sLoadingRecords": "载入中...",
                "sInfoThousands":  ",",	"oPaginate": {"sFirst":"首页","sPrevious": "上页","sNext":"下页","sLast":"末页"},
                "oAria": {"sSortAscending":  ": 以升序排列此列","sSortDescending": ": 以降序排列此列"}},
            scrollY:"300px",
            sScrollX:"disabled",
            aLengthMenu: [ 10, 20, 50, 100, ],
            fnDrawCallback:function(){

            }
        });
    },
    transobjtoarr:function(obj){
        var arr = [];
        for(var item in obj){
            arr.push(obj[item]);
        }
        return arr;
    },
    registerEvents : function(){
        this._super();
        this.loading();
        this.timeslotchange();
        //this.getdatetime();
        //this.departmentchange();
        this.submitconfim();
    }

});