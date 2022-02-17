/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("Businesstrend_List_Js",{},{


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
    submitconfim:function(){
        var thisInstance=this;
        $('table').on('click','#PostQuery',function(){
            var datetime=$('#datatime').val();
            var enddatetime=$('#enddatatime').val();
            postData= {
                'module': app.getModuleName(),
                'action': 'selectAjax',
                'mode': 'getdata',
                'charid':$('#user_editView_fieldName_dropDown').val(),
                'datetime':datetime,
                'enddatetime':enddatetime
            };
            //注释加载提示
            var progressIndicatorElement = jQuery.progressIndicator({
                'message' : '正在处理,请耐心等待哟',
                'position' : 'html',
                'blockInfo' : {'enabled' : true}
            });
            AppConnector.request(postData).then(
                function(data){
                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                    if(data.success) {
                        thisInstance.echartscon(data.result);
                    }
                }
            )
        });
    },
    //加载柱状图
    echartscon:function(params){
        var dat=new Array();
        var neq=new Array();
        var con=new Array();
        var sin=new Array();
        $.each(params,function(k,v){
            dat.push(k);
            neq.push(v['new']);
            con.push(v['con']);
            sin.push(v['sin']);
        });
       /* data = data1.map(function(item){
           return item.num;
        });*/
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
                // 指定图表的配置项和数据
                var option = {
                    tooltip : {
                        trigger: 'axis',
                        axisPointer : {            // 坐标轴指示器，坐标轴触发有效
                            type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
                        }
                    },
                    legend: {
                        data:['新增商机数','转换商机数','签单数量']
                    },
                    dataZoom:{
                        type: 'slider',
                        show: true,
                        start: 0,
                        end: 80
                    },
                    xAxis : [{type : 'category', data : dat }],
                    yAxis : [{type : 'value'}],
                    series : [
                        {
                            name:'新增商机数',
                            type:'bar',
                            data:neq,
                            barGap: '0%',
                            barCategoryGap: '20%'
                        },
                        {
                            name:'转换商机数',
                            type:'bar',
                            data:con,
                            barCategoryGap: '30%'
                        },
                        {
                            name:'签单数量',
                            type:'bar',
                            data:sin,
                            barCategoryGap: '30%'
                        }
                    ]
                };
                // 使用刚指定的配置项和数据显示图表。
                myChart.setOption(option);
               // var ecConfig= require('echarts/config');
                myChart.on('click',function (param) {
                    var progressIndicatorElement = jQuery.progressIndicator({
                        'message' : '正在处理,请耐心等待哟',
                        'position' : 'html',
                        'blockInfo' : {'enabled' : true}
                    });
                    $('#detailtable').html("");
                    //console.log(param);
                    var type = param.seriesIndex;
                    var date = param.name;
                    var charid = $('#user_editView_fieldName_dropDown').val();
                    var mode="";
                    if(type==0){
                        title="新增商机列表";
                        mode = "getnewlist";
                    }else if(type==1){
                        title="转换商机列表";
                        mode = "getconlist";
                    }else{
                        title="签单商机列表";
                        mode = "getsinlist";
                    }
                    var postdata = {
                        'module': app.getModuleName(),
                        'mode':mode,
                        'action':'selectAjax',
                        'date':date,
                        'charid':charid
                    }
                    if(param.value>0){
                        AppConnector.request(postdata).then(
                            function(data){
                                if(data.success){
                                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                                    var tabletr = '';
                                   if(mode=='getsinlist'){
                                       var divtable = '<table id="tbl_Detail" class="table listViewEntriesTable" width="100%"">' +'<thead><tr>' +
                                           '<th nowrap><b>录入时间</b></th>' +
                                           '<th nowrap><b>转化后客户名称</b></th>' +
                                           '<th nowrap><b>分配人  </b></th>' +
                                           '<th nowrap><b>合同编号</b></th>' +
                                           '</tr></thead><tbody>';
                                       $.each(data.result,function(k,v){
                                           var acc=""
                                           if(v.accountname){acc=v.accountname}else{acc='--'}
                                           tabletr+='<tr><td nowrap><a href=index.php?module=Leads&view=Detail&record='+ v.leadid+'>'+ v.mapcreattime+'</td><td nowrap><a href="index.php?module=Accounts&view=Detail&record='+ v.accountid+'" target="view_window">'+acc+'</td><td>'+ v.assigner+'</td><td><a href="index.php?module=Accounts&view=Detail&record="'+ v.servicecontractsid+'" target="view_window">'+v.contract_no+'</td></tr>';
                                       });
                                   }else{
                                       var divtable = '<table id="tbl_Detail" class="table listViewEntriesTable" width="100%"">' +'<thead><tr>' +
                                           '<th nowrap><b>录入时间</b></th>' +
                                           '<th nowrap><b>转化后客户名称</b></th>' +
                                           '<th nowrap><b>分配人  </b></th>' +
                                           '</tr></thead><tbody>';
                                       $.each(data.result,function(k,v){
                                           var acc=""
                                           if(v.accountname){acc=v.accountname}else{acc='--'}
                                           tabletr+='<tr><td nowrap><a href=index.php?module=Leads&view=Detail&record='+ v.leadid+'>'+ v.mapcreattime+'</td><td nowrap><a href="index.php?module=Accounts&view=Detail&record='+ v.accountid+'" target="view_window">'+acc+'</td><td>'+ v.assigner+'</td></tr>';
                                       });
                                   }
                                    divtable+=tabletr+"</table>";
                                    $('#detailtable').html(divtable);
                                    thisInstance.Tableinstance(title);
                                }else{
                                    console.log('当前没有数据');
                                }
                            });
                    }else{
                        '数据';
                    }
                });
            });
    },

    Tableinstance:function(title){
        var table = jQuery('#tbl_Detail').DataTable({
            language: {"sProcessing":   "处理中...",	"sLengthMenu":   "显示 _MENU_ 项结果","sZeroRecords":  "没有匹配结果","sInfo":         "显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",	"sInfoEmpty":    "显示第 0 至 0 项结果，共 0 项","sInfoFiltered": "(由 _MAX_ 项结果过滤)","sInfoPostFix":  "","sSearch":"当前页快速检索:","sUrl":"","sEmptyTable":     "表中数据为空","sLoadingRecords": "载入中...",
                "sInfoThousands":  ",",	"oPaginate": {"sFirst":"首页","sPrevious": "上页","sNext":"下页","sLast":"末页"},
                "oAria": {"sSortAscending":  ": 以升序排列此列","sSortDescending": ": 以降序排列此列"}},
            scrollY:"300px",
            sScrollX:"disabled",
            aLengthMenu: [ 10, 20, 50, 100, ],
            fnDrawCallback:function(){
                jQuery('#msg').html('<span style="font-size:12px;color:green;text-align:left;">超过1000条不显示</span><span  style="text-align:center;display:block;font-size:16px;color:red;font-weight:bold;">'+title+'</span>');
            }
        });
    },

    registerEvents : function(){
        this._super();
        this.getdatetime();
        this.submitconfim();
    }

});