/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("BusinessAnalysis_List_Js",{},{


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
                    datas['name']=[];
                    datas['value']=[];
                    datas['namev']=[];
                    datas['label']=[];
                    datas['label']['allocated']='求和项：跟进数';
                    datas['label']['transformation']='求和项：转化数';
                    datas['label']['cancelled']='求和项：作废数';
                    datas['label']['firstcontr']='求和项：成交数';
                    datas['label']['allvalue']='求和项：总数';
                    datas['labelv']=[];
                    datas['labelvalue']=[];
                    datas['labelvalue'].push('');
                    $.each(data.result,function(key,value){
                        if(key=='name'){
                            $.each(value,function(k,val){
                                datas['name'].push(app.vtranslate(val));
                                datas['namev'][app.vtranslate(val)]=val;
                            });
                        }else{
                            datas['value'].push({
                                name:datas['label'][key],
                                type:'bar',
                                data:value,
                                barGap: '0%',
                                barMaxWidth:60,
                                barCategoryGap: '20%'
                            });
                            datas['labelvalue'].push(datas['label'][key]);
                            datas['labelv'][datas['label'][key]]=key;
                        }

                    });
                    console.log(datas)
                    thisInstance.echartscon(datas);

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
                    title : {text: '商机业务分析\n',x:'center'},
                    legend: {x: 'center',y: 'top',data:params.labelvalue},
                    dataZoom:{
                        type: 'slider',
                        show: true,
                        start: 0,
                        end: 80
                    },
                    xAxis : [{ axisLabel: {rotate: 60,margin:20,textStyle: {color: '#000',fontStyle: 'normal',fontWeight: 'bold',fontSize: 12}},type : 'category', data :params.name}],
                    grid: {x: 40, x2: 10, y2: 140},
                    yAxis : [{type : 'value'}],
                    series : params.value
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
                    //var type = param.seriesIndex;
                    //var leadsnum = param.name;
                    var datetime=$('#datatime').val();
                    var enddatetime=$('#enddatatime').val();
                    var postdata = {
                        'module': app.getModuleName(),
                        'mode':'getnewlist',
                        'action':'selectAjax',
                        'leadsnum':params.namev[param.name],
                        'datetime':datetime,
                        'enddatetime':enddatetime,
                        'classes':params.labelv[param.seriesName]
                    }

                    console.log(postdata);
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
                jQuery('#msg').html('<span style="font-size:12px;color:green;text-align:left;">超过1000条不显示</span><span  style="text-align:center;display:block;font-size:16px;color:red;font-weight:bold;">'+postData.name+'&nbsp;&nbsp;&nbsp;&nbsp;商机业务分析'+'</span>');
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