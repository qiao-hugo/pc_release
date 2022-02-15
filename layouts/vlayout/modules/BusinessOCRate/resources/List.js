/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("BusinessOCRate_List_Js",{},{


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
    LeadsInitialization:function(){
        var thisInstance=this;
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
                    var datas=[];
                    datas['leadsnum']=[];
                    datas['data']=[];
                    $.each(data.result,function(key,value){
                        datas['data'].push(value);
                        datas['leadsnum'].push(value.name);
                    });
                    //thisInstance.echartscon(datas);
                    thisInstance.echartscon(data.result);
                }
            }
        );

    },
    submitconfim:function(){
        var thisInstance=this;
        $('table').on('click','#PostQuery',function(){
            thisInstance.LeadsInitialization();
        });

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
                'echarts/chart/line',
                'echarts/chart/pie'
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
                    title : {text: '商机来源转化率分析\n',x:'center'},
                    legend: {
                        orient: 'horizontal',
                        x: 'center',
                        //y: 'bottom',
                        data:['','新增商机数','转化为客户数量','签单数量']
                    },
                    /*dataZoom:{
                        type: 'slider',
                        show: true,
                        start: 0,
                        end: 80
                    },*/
                    xAxis : [{ axisLabel: {rotate: 60,margin:20},type : 'category', data :params.name}],
                    grid: {
                        x: 40, x2: 10, y2: 120},
                    yAxis : [{type : 'value'}],
                    series : [
                        {
                            name:'新增商机数',
                            type:'bar',
                            data:params.value,
                            barGap: '0%',
                            barMaxWidth:60,
                            barCategoryGap: '20%'
                        },
                        {
                            name:'转化为客户数量',
                            type:'bar',
                            barMaxWidth:60,
                            data:params.transformation,
                            barCategoryGap: '30%'
                        },
                        {
                            name:'签单数量',
                            type:'bar',
                            barMaxWidth:60,
                            data:params.firstcontr,
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
                    var leadsnum = param.name;
                    var datetime=$('#datatime').val();
                    var enddatetime=$('#enddatatime').val();
                    var titles=[];
                    titles['新增商机数']='one';
                    titles['转化为客户数量']='two';
                    titles['签单数量']='three';
                    var postdata = {
                        'module': app.getModuleName(),
                        'mode':'getnewlist',
                        'action':'selectAjax',
                        'leadsnum':leadsnum,
                        'datetime':datetime,
                        'enddatetime':enddatetime,
                        'classes':titles[param.seriesName]
                    }
                    if(param.value>0){
                        AppConnector.request(postdata).then(
                            function(data){
                                if(data.success){
                                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                                    var tabletr = '';

                                       var divtable = '<table id="tbl_Detail" class="table listViewEntriesTable" width="100%"">' +'<thead><tr>' +
                                           '<th nowrap><b>录入时间</b></th>' +
                                           '<th nowrap><b>转化后客户名称</b></th>' +
                                           '<th nowrap><b>分配人  </b></th>' +
                                           '<th nowrap><b>负责人  </b></th>' +
                                           '<th nowrap><b>状态  </b></th>' +
                                           '<th nowrap><b>描述  </b></th>' +
                                           '</tr></thead><tbody>';
                                       $.each(data.result,function(k,v){
                                           var acc=""
                                           if(v.accountname){acc=v.accountname}else{acc='--'}
                                           tabletr+='<tr><td nowrap><a href=index.php?module=Leads&view=Detail&record='+ v.leadid+'>'+ v.mapcreattime+'</td><td nowrap><a href="index.php?module=Accounts&view=Detail&record='+ v.accountid+'" target="view_window">'+acc+'</td><td>'+ v.assigner+'</td><td>'+ v.smownerid+'</td><td>'+ app.vtranslate(v.assignerstatus)+'</td><td>'+v.description+'</td></tr>';
                                       });

                                    divtable+=tabletr+"</table>";
                                    $('#detailtable').html(divtable);
                                    thisInstance.Tableinstance(param);
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

    Tableinstance:function(postData){
        var table = jQuery('#tbl_Detail').DataTable({
            language: {"sProcessing":   "处理中...",	"sLengthMenu":   "显示 _MENU_ 项结果","sZeroRecords":  "没有匹配结果","sInfo":         "显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",	"sInfoEmpty":    "显示第 0 至 0 项结果，共 0 项","sInfoFiltered": "(由 _MAX_ 项结果过滤)","sInfoPostFix":  "","sSearch":"当前页快速检索:","sUrl":"","sEmptyTable":     "表中数据为空","sLoadingRecords": "载入中...",
                "sInfoThousands":  ",",	"oPaginate": {"sFirst":"首页","sPrevious": "上页","sNext":"下页","sLast":"末页"},
                "oAria": {"sSortAscending":  ": 以升序排列此列","sSortDescending": ": 以降序排列此列"}},
            scrollY:"300px",
            sScrollX:"disabled",
            aLengthMenu: [ 10, 20, 50, 100, ],
            fnDrawCallback:function(){
                jQuery('#msg').html('<span style="font-size:12px;color:green;text-align:left;">超过1000条不显示</span><span  style="text-align:center;display:block;font-size:16px;color:red;font-weight:bold;">'+postData.name+'&nbsp;&nbsp;&nbsp;&nbsp;'+postData.seriesName+'&nbsp;&nbsp;&nbsp;&nbsp;商机来源转化率分析'+'</span>');
            }
        });
    },

    registerEvents : function(){
        this._super();
        this.getdatetime();
        this.submitconfim();
        this.LeadsInitialization();
    }

});