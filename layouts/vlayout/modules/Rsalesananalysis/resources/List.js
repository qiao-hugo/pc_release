/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("Rsalesananalysis_List_Js",{},{
    //初始化
    loading:function(){
        var thisInstance=this;
        var postData={};
        postData={'module':app.getModuleName(),
            'action':'selectAjax',
            'mode':'getCountsday'
        };
        AppConnector.request(postData).then(
            function(data) {
                if (data.success) {
                    var arr = new Array;
                    arr['daymonth'] = new Array();
                    arr['daymonth']['Contracts'] = new Array();
                    arr['daymonth']['Payments'] = new Array();
                    arr['dateyear'] = data.result.dateyear;
                    arr['dateyear'].unshift('');//首位加入空元素可强制换行
                    //console.log(data.result);
                    if (Object.keys(data.result).length > 0) {
                        $.each(data.result, function (k, value) {
                            if(k!='dateyear'){
                                $.each(value, function (M, val) {
                                    var temparr = new Array();
                                    $.each(val, function (n, v) {
                                        temparr[(parseInt(n,10)-1)]=v;
                                    });
                                    arr['daymonth'][k].push({
                                        name: M,
                                        type: 'bar',
                                        data: temparr
                                    });
                                });
                            }
                        });
                        thisInstance.echartscon(arr);
                    }
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
                        data:params.dateyear
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
                            data : ['一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月']
                        }
                    ],
                    yAxis : [
                        {
                            type : 'value'
                        }
                    ],
                    series :
                        params.daymonth.Contracts


                },true);
                var myChart1 = ec.init(document.getElementById('bartablev'));
                myChart1.setOption({
                    title : {
                        text: '有效回款金额\n',
                        x:'center'
                    },
                    tooltip : {
                        trigger: 'axis',
                        axisPointer : {            // 坐标轴指示器，坐标轴触发有效
                            type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
                        }
                    },

                    legend: {

                        data:params.dateyear
                    },
                    toolbox: {
                        show : true,
                        //orient: 'vertical',
                        //x: 'right',
                        //y: 'top',
                        feature : {
                            magicType : {show: true, type: ['line', 'bar', 'stack', 'tiled']},
                            restore : {show: true}
                        }
                    },
                    calculable : true,
                    xAxis : [
                        {
                            type : 'category',
                            data : ['一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月']
                        }
                    ],
                    yAxis : [
                        {
                            type : 'value'
                        }
                    ],
                    series :
                        params.daymonth.Payments


                },true);

                var ecConfig= require('echarts/config');
                myChart.on(ecConfig.EVENT.CLICK,function (param) {
                    if(param.value==0){
                        return;
                    }
                    var datetime=(param.dataIndex+1)<10?param.seriesName+'-0'+(param.dataIndex+1):param.seriesName+'-'+(param.dataIndex+1);
                    //console.log(datetime);
                    var postData={};
                    var departmentid=$('#department_editView_fieldName_dropDown').val();
                    postData={
                        'module':app.getModuleName(),
                        'action':'selectAjax',
                        'mode':'getcontractdetaillist',
                        'paramname':datetime+'　合同明细',
                        'department':departmentid,
                        'datetime':datetime
                    };
                    thisInstance.gettables(postData);
                });
                myChart1.on(ecConfig.EVENT.CLICK,function (param) {
                    if(param.value==0){
                        return;
                    }
                    var datetime=(param.dataIndex+1)<10?param.seriesName+'-0'+(param.dataIndex+1):param.seriesName+'-'+(param.dataIndex+1);
                    //console.log(datetime);
                    var postData={};
                    var departmentid=$('#department_editView_fieldName_dropDown').val();
                    postData={
                        'module':app.getModuleName(),
                        'action':'selectAjax',
                        'mode':'getpaymentdetaillist',
                        'paramname':datetime+'　有效业绩回款明细',
                        'department':departmentid,
                        'datetime':datetime
                    };
                    thisInstance.gettables(postData);
                });
                myChart.refresh();
            }
        );
    },
    //加载时间控件
    getdatetime:function(){
        var endtime = app.addOneHour();
        $('#datatime').datetimepicker({
            format: "yyyy-mm-dd",
            language:  'zh-CN',
            autoclose: true,
            todayBtn: true,
            pickerPosition: "bottom-right",
            showMeridian: 0,
            endDate:new Date(),
            weekStart:1,
            todayHighlight:1,
            startView:2,
            minView:2,
            forceParse:0
        });
        $('#enddatatime').datetimepicker({
            format: "yyyy-mm-dd",
            language:  'zh-CN',
            autoclose: true,
            todayBtn: true,
            pickerPosition: "bottom-right",
            showMeridian: 0,
            endDate:new Date(),
            weekStart:1,
            todayHighlight:1,
            startView:2,
            minView:2,
            forceParse:0
        });
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
                'userid':userid,
                'datetime':datetime
            };

            AppConnector.request(postData).then(
                function(data){
                    if(data.success) {
                        var arr = new Array;
                        arr['daymonth'] = new Array();
                        arr['daymonth']['Contracts'] = new Array();
                        arr['daymonth']['Payments'] = new Array();
                        arr['dateyear'] = data.result.dateyear;
                        arr['dateyear'].unshift('');//加入空元素可强制换行
                        if (Object.keys(data.result).length > 0) {
                            $.each(data.result, function (k, value) {
                                if (k != 'dateyear') {
                                    $.each(value, function (M, val) {
                                        var temparr = new Array();
                                        $.each(val, function (n, v) {
                                            temparr[(parseInt(n, 10) - 1)] = v;
                                        });
                                        arr['daymonth'][k].push({
                                            name: M,
                                            type: 'bar',
                                            data: temparr
                                        });
                                    });
                                }
                            });
                        }
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
            'message' : '正在处理,请耐心等待哟',
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
                            var div_detail='<table id="tbl_Detail" class="table listViewEntriesTable" width="100%"">' +
                                '<thead><tr><th nowrap><b>归还日期</b></th><th nowrap><b>负责人</b></th><th nowrap><b>客户名称</b></th><th nowrap><b>合同编号</b></th><th nowrap><b>合同类型</b></th><th nowrap><b>合同金额</b></th></tr></thead><tbody>';
                            $.each(data.result,function(i,val){
                                str+='<tr><td nowrap>'+val.returndate+'</td><td nowrap>'+val.receiveid+'</td><td nowrap><a href="index.php?module=Accounts&view=Detail&record='+val.sc_related_to_reference+'" target="view_window">'+val.sc_related_to+'</a></td><td nowrap><a href="index.php?module=ServiceContracts&view=Detail&record='+val.cid+'" target="view_window">'+val.contract_no+'</a></td><td nowrap>'+val.contract_type+'</td><td nowrap>'+val.total+'</td></tr>';
                            });
                        }else if(postData.mode=='getpaymentdetaillist'){
                            var div_detail='<table id="tbl_Detail" class="table listViewEntriesTable" width="100%"">' +
                                '<thead><tr><th nowrap><b>回款日期</b></th><th nowrap><b>负责人</b></th><th nowrap><b>客户名称</b></th><th nowrap><b>合同编号</b></th><th nowrap><b>合同金额</b></th><th nowrap><b>回款金额(已分成)</b></th><th nowrap><b>外采成本(已分成)</b></th><th nowrap><b>有效回款</b></th></tr></thead><tbody>';
                            $.each(data.result,function(i,val){
                                str+='<tr><td nowrap>'+val.reality_date+'</td><td nowrap>'+val.last_name+'</td><td nowrap><a href="index.php?module=Accounts&view=Detail&record='+val.accountid+'" target="view_window">'+val.accountname+'</a></td><td nowrap><a href="index.php?module=ServiceContracts&view=Detail&record='+val.cid+'" target="view_window">'+val.contract_no+'</a></td><td nowrap>'+val.total+'</td><td nowrap>'+val.businessunit+'</td><td nowrap>'+val.sss+'</td><td nowrap>'+thisInstance.accSubtr(val.businessunit,val.sss)+'</td></tr>';
                            });
                        }


                        $('#detailtable').html(div_detail+str+'</tbody></table>');
                        thisInstance.Tableinstance(postData);

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
        var table = jQuery('#tbl_Detail').DataTable({
            language: {"sProcessing":   "处理中...",	"sLengthMenu":   "显示 _MENU_ 项结果","sZeroRecords":  "没有匹配结果","sInfo":         "显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",	"sInfoEmpty":    "显示第 0 至 0 项结果，共 0 项","sInfoFiltered": "(由 _MAX_ 项结果过滤)","sInfoPostFix":  "","sSearch":"当前页快速检索:","sUrl":"","sEmptyTable":     "表中数据为空","sLoadingRecords": "载入中...",
                "sInfoThousands":  ",",	"oPaginate": {"sFirst":"首页","sPrevious": "上页","sNext":"下页","sLast":"末页"},
                "oAria": {"sSortAscending":  ": 以升序排列此列","sSortDescending": ": 以降序排列此列"}},
            scrollY:"300px",
            sScrollX:"disabled",
            aLengthMenu: [ 10, 20, 50, 100, ],
            fnDrawCallback:function(){
                jQuery('#msg').html('<span style="font-size:12px;color:green;text-align:left;">超过1000条不显示</span><span  style="text-align:center;display:block;font-size:16px;color:red;font-weight:bold;">'+postData.paramname+'</span>');
            }
        });
    },
    registerEvents : function(){
        this._super();
        this.loading();
        this.timeslotchange();
        //this.getdatetime();
        this.departmentchange();
        this.submitconfim();
    }

});