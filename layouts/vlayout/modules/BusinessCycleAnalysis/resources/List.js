/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("BusinessCycleAnalysis_List_Js",{},{

    vtranslatevalue:[],
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
            'charid':$('#system_editView_fieldName_dropDown').val(),
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
        function vtranslated(param){
            thisInstance.vtranslatevalue[app.vtranslate(param)]=param;
            return app.vtranslate(param)
        }
        require(
            ['echarts','echarts/chart/bar','echarts/chart/line','echarts/chart/pie'],
            function (ec) {
                var myChart = ec.init(document.getElementById('bartable'));
                var option = {
                    tooltip : {
                        trigger: 'axis',
                        axisPointer : {            // 坐标轴指示器，坐标轴触发有效
                            type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
                        }
                    },
                    title:{text: '商机周期分析--体系\n',x:'center'},
                    dataZoom:{type: 'slider',
                        show: true,
                        start: 0,
                        end: 80
                    },
                    xAxis : [{ axisLabel: {rotate: 60,margin:20,textStyle: {color: '#000',fontStyle: 'normal',fontWeight: 'bold',fontSize: 12}},type : 'category', data :params.systems.name.map(vtranslated)}],
                    grid: {x: 40, x2: 10, y2: 140},
                    yAxis : [{type : 'value'}],
                    series :[
                        {
                            name:'数量',
                            type:'bar',
                            data:params.systems.value,
                            barGap: '0%',
                            barMaxWidth:60,
                            barCategoryGap: '20%'
                        },
                    ]
                };
                // 使用刚指定的配置项和数据显示图表。
                myChart.setOption(option);
                myChart.on('click',function (param) {
                    $('#detailtable').html("");
                    var datetime=$('#datatime').val();
                    var enddatetime=$('#enddatatime').val();
                    var postdata = {
                        'module': app.getModuleName(),
                        'mode':'getnewlist',
                        'action':'selectAjax',
                        'datetime':datetime,
                        'enddatetime':enddatetime,
                        'leadsnum':thisInstance.vtranslatevalue[param.name],
                        'classes':'one'
                    }
                    if(param.value>0){
                        var progressIndicatorElement = jQuery.progressIndicator({
                            'message' : '正在处理,请耐心等待哟',
                            'position' : 'html',
                            'blockInfo' : {'enabled' : true}
                        });
                        AppConnector.request(postdata).then(
                            function(data){
                                if(data.success){
                                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                                    var tabletr = '';

                                    var divtable = '<table id="tbl_Detail" class="table listViewEntriesTable" width="100%"">' +'<thead><tr>' +
                                        '<th nowrap><b>部门</b></th>' +
                                        '<th nowrap><b>日期</b></th>' +
                                        '<th nowrap><b>公司名称  </b></th>' +
                                        '<th nowrap><b>电话</b></th>' +
                                        '<th nowrap><b>对接人  </b></th>' +
                                        '<th nowrap><b>成交项目</b></th>' +
                                        '<th nowrap><b>成交时间</b></th>' +
                                        '<th nowrap><b>成交金额</b></th>' +
                                        '<th nowrap><b>成交周期</b></th>' +
                                        '</tr></thead><tbody>';
                                    $.each(data.result,function(k,v){
                                        var acc=""
                                        if(v.accountname){acc=v.accountname}else{acc='--'}
                                        tabletr+='<tr><td nowrap><a href=index.php?module=Leads&view=Detail&record='+ v.leadid+'>'+ app.vtranslate(v.leadsystem)+'</td><td>'+ v.mapcreattime+'</td><td><a href="index.php?module=Accounts&view=Detail&record='+ v.accountid+'" target="view_window">'+ v.accountname+'</a></td><td>'+ v.mobile+'</td><td>'+ v.assigner+'</td><td>'+ v.contract_type+'</td><td>'+ v.firstreceivepaydate+'</td><td>'+ v.total+'</td><td>'+ v.diffday+'</td></tr>';
                                    });

                                    divtable+=tabletr+"</tbody></table>";
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

        var myChart1 = ec.init(document.getElementById('bartablev'));
        var option1 = {
            tooltip : {
                trigger: 'axis',
                axisPointer : {            // 坐标轴指示器，坐标轴触发有效
                    type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
                }
            },
            title:{text: '商机周期分析--业务\n',x:'center'},
            dataZoom:{type: 'slider',
                show: true,
                start: 0,
                end: 80
            },
            xAxis : [{ axisLabel: {rotate: 60,margin:20,textStyle: {color: '#000',fontStyle: 'normal',fontWeight: 'bold',fontSize: 12}},type : 'category', data :params.leadstype.name.map(vtranslated)}],
            grid: {x: 40, x2: 10, y2: 140},
            yAxis : [{type : 'value'}],
            series :[
                {
                    name:'数量',
                    type:'bar',
                    data:params.leadstype.value,
                    barGap: '0%',
                    barMaxWidth:60,
                    barCategoryGap: '20%'
                },
            ]
        };
        // 使用刚指定的配置项和数据显示图表。
        myChart1.setOption(option1);
        myChart1.on('click',function (param) {
            $('#detailtable').html("");
            var datetime=$('#datatime').val();
            var enddatetime=$('#enddatatime').val();
            var postdata = {
                'module': app.getModuleName(),
                'mode':'getnewlist',
                'action':'selectAjax',
                'datetime':datetime,
                'enddatetime':enddatetime,
                'leadsnum':thisInstance.vtranslatevalue[param.name],
                'classes':'two'
            }
            if(param.value>0){
                var progressIndicatorElement = jQuery.progressIndicator({
                    'message' : '正在处理,请耐心等待哟',
                    'position' : 'html',
                    'blockInfo' : {'enabled' : true}
                });
                AppConnector.request(postdata).then(
                    function(data){
                        if(data.success){
                            progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                            var tabletr = '';

                            var divtable = '<table id="tbl_Detail" class="table listViewEntriesTable" width="100%"">' +'<thead><tr>' +
                                '<th nowrap><b>部门</b></th>' +
                                '<th nowrap><b>日期</b></th>' +
                                '<th nowrap><b>公司名称  </b></th>' +
                                '<th nowrap><b>电话</b></th>' +
                                '<th nowrap><b>对接人  </b></th>' +
                                '<th nowrap><b>成交项目</b></th>' +
                                '<th nowrap><b>成交时间</b></th>' +
                                '<th nowrap><b>成交金额</b></th>' +
                                '<th nowrap><b>成交周期</b></th>' +
                                '</tr></thead><tbody>';
                            $.each(data.result,function(k,v){
                                var acc=""
                                if(v.accountname){acc=v.accountname}else{acc='--'}
                                //tabletr+='<tr><td nowrap><a href=index.php?module=Leads&view=Detail&record='+ v.leadid+'>'+ v.leadsystem+'</td><td nowrap><a href="index.php?module=Accounts&view=Detail&record='+ v.accountid+'" target="view_window">'+acc+'</td><td>'+ v.assigner+'</td><td>'+ v.smownerid+'</td><td>'+ app.vtranslate(v.assignerstatus)+'</td><td>'+v.description+'</td></tr>';
                                tabletr+='<tr><td nowrap><a href=index.php?module=Leads&view=Detail&record='+ v.leadid+'>'+ app.vtranslate(v.leadsystem)+'</td><td>'+ v.mapcreattime+'</td><td><a href="index.php?module=Accounts&view=Detail&record='+ v.accountid+'" target="view_window">'+ v.accountname+'</a></td><td>'+ v.mobile+'</td><td>'+ v.assigner+'</td><td>'+ v.contract_type+'</td><td>'+ v.firstreceivepaydate+'</td><td>'+ v.total+'</td><td>'+ v.diffday+'</td></tr>';
                            });

                            divtable+=tabletr+"</tbody></table>";
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
                jQuery('#msg').html('<span style="font-size:12px;color:green;text-align:left;">超过1000条不显示</span><span  style="text-align:center;display:block;font-size:16px;color:red;font-weight:bold;">'+postData.name+'&nbsp;&nbsp;&nbsp;&nbsp;商机周期分析'+'</span>');
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