/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("RIndustrysalesanalysis_List_Js",{},{
    //初始化
    urlArgs:[],
    loading:function(){
        var thisInstance=this;
        var postData={};
        var publict=thisInstance.urlArgs.public==undefined?'':thisInstance.urlArgs.public;
        postData={'module':app.getModuleName(),
            'action':'selectAjax',
            'mode':'getCountsday',
            'fliter':publict
        };
        AppConnector.request(postData).then(
            function(data){
                if(data.success) {
                    var arr=new Array;
                    arr['numcountsname']=new Array;
                    arr['numcountsval']=new Array;
                    arr['Contractedamountname']=new Array;
                    arr['Contractedamountval']=new Array;
                    arr['Paymentname']=new Array;
                    arr['Paymentval']=new Array;

                    if(data.result.numcounts.length>0){
                        $.each(data.result.numcounts,function(k,val){
                            //长的在上
                            //var tempn=data.result.numcounts.length-k-1;
                            arr['numcountsname'][k]=val.industry;
                            arr['numcountsval'][k]={value:val.totals, name:val.industry};
                        });
                    }
                    if(data.result.Contractedamount.length>0){
                        $.each(data.result.Contractedamount,function(k,val){
                            //长的在上
                            //var tempn=data.result.numcounts.length-k-1;
                            arr['Contractedamountname'][k]=val.industry;
                            arr['Contractedamountval'][k]={value:val.totals, name:val.industry};
                        });
                    }
                    if(data.result.Payment.length>0){
                        $.each(data.result.Payment,function(k,val){
                            //长的在上
                            //var tempn=data.result.numcounts.length-k-1;
                            arr['Paymentname'][k]=val.industry;
                            arr['Paymentval'][k]={value:val.totals, name:val.industry};
                        });
                    }
                   /* if(data.result.Payment.length>0){
                        $.each(data.result.Payment,function(k,val){
                            var tempn=data.result.Payment.length-k-1;
                            arr['paymentname'][tempn]=val.user_name;
                            arr['paymentval'][tempn]=val.totals;
                        });
                    }*/
                    //console.log(arr);
                    thisInstance.echartscon(arr);

                } else {

                }
            },
            function(error,err){

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
                'echarts/chart/line',
                'echarts/chart/pie',
                'echarts/chart/funnel'
            ],
            function (ec) {
                //--- 折柱 ---
                var myChart = ec.init(document.getElementById('bartable'));
                myChart.setOption({
                    title : {
                        text: '客户数量',
                        //subtext: '客户数量',
                        x:'center'
                    },
                    tooltip : {
                        trigger: 'item',
                        formatter: "{a} <br/>{b} : {c} ({d}%)"
                    },
                    legend: {
                        orient : 'vertical',
                        x : 'left',
                        data:params.numcountsname
                    },
                    toolbox: {
                        show : true,
                        feature : {
                            restore : {show: true}
                        }
                    },
                    calculable : true,
                    series : [
                        {
                            name:'新增客户数量',
                            type:'pie',
                            radius : '40%',
                            center: ['60%', '50%'],
                            data:params.numcountsval
                        }
                    ]
                });
                var myChart1 = ec.init(document.getElementById('bartablev'));
                myChart1.setOption({
                    title : {
                        text: '签约额',
                        //subtext: '签约额',
                        x:'center'
                    },
                    tooltip : {
                        trigger: 'item',
                        formatter: "{a} <br/>{b} : {c} ({d}%)"
                    },
                    legend: {
                        orient : 'vertical',
                        x : 'left',
                        data:params.Contractedamountname
                    },
                    toolbox: {
                        show : true,
                        feature : {
                            restore : {show: true}
                        }
                    },
                    calculable : true,
                    series : [
                        {
                            name:'签约额',
                            type:'pie',
                            radius : '40%',
                            center: ['60%', '50%'],
                            data:params.Contractedamountval
                        }
                    ]
                });
                var myChart2 = ec.init(document.getElementById('bartablem'));
                myChart2.setOption({
                    title : {
                        text: '收款额',
                        //subtext: '收款额',
                        x:'center'
                    },
                    tooltip : {
                        trigger: 'item',
                        formatter: "{a} <br/>{b} : {c} ({d}%)"
                    },
                    legend: {
                        orient : 'vertical',
                        x : 'left',
                        data:params.Paymentname
                    },
                    toolbox: {
                        show : true,
                        feature : {
                            restore : {show: true}
                        }
                    },
                    calculable : true,
                    series : [
                        {
                            name:'收款额',
                            type:'pie',
                            radius : '40%',
                            center: ['60%', '50%'],
                            data:params.Paymentval
                        }
                    ]
                });

                var ecConfig= require('echarts/config');
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
    submitconfim:function(){
        var thisInstance=this;
        $('table').on('click','#PostQuery',function(){
            $('#detailtable').empty();
            $('#msg').empty();
            var departmentid=$('#department_editView_fieldName_dropDown').val();
            var userid=$('#user_editView_fieldName_dropDown').val();
            var datetime=$('#datatime').val();
            var enddatetime=$('#enddatatime').val();
            var pagenum=$('#pagenum').val();
            var publict=thisInstance.urlArgs.public==undefined?'':thisInstance.urlArgs.public;
            postData= {
                'module': app.getModuleName(),
                'action': 'selectAjax',
                'mode': 'getCountsday',
                'department':departmentid,
                'userid':userid,
                'datetime':datetime,
                'enddatetime':enddatetime,
                'pagenum':pagenum,
                'fliter':publict
            };

            AppConnector.request(postData).then(
                function(data){
                    if(data.success) {
                        var arr=new Array;
                        arr['numcountsname']=new Array;
                        arr['numcountsval']=new Array;
                        arr['Contractedamountname']=new Array;
                        arr['Contractedamountval']=new Array;
                        arr['Paymentname']=new Array;
                        arr['Paymentval']=new Array;
                        if(data.result.numcounts.length>0){
                            $.each(data.result.numcounts,function(k,val){
                                //长的在上
                                //var tempn=data.result.numcounts.length-k-1;
                                arr['numcountsname'][k]=val.industry;
                                arr['numcountsval'][k]={value:val.totals, name:val.industry};
                            });
                        }
                        if(data.result.Contractedamount.length>0){
                            $.each(data.result.Contractedamount,function(k,val){
                                //长的在上
                                //var tempn=data.result.numcounts.length-k-1;
                                arr['Contractedamountname'][k]=val.industry;
                                arr['Contractedamountval'][k]={value:val.totals, name:val.industry};
                            });
                        }
                        if(data.result.Payment.length>0){
                            $.each(data.result.Payment,function(k,val){
                                //长的在上
                                //var tempn=data.result.numcounts.length-k-1;
                                arr['Paymentname'][k]=val.industry;
                                arr['Paymentval'][k]={value:val.totals, name:val.industry};
                            });
                        }
                        //console.log(arr);
                        thisInstance.echartscon(arr);

                    }
                }
            )
        });
    },
    getUrlArgs:function(){
        var url = location.href;
        var paraString = url.substring(url.indexOf("?")+1,url.length).split("&");
        var paraObj = {}
        for (i=0; j=paraString[i]; i++){
            paraObj[j.substring(0,j.indexOf("=")).toLowerCase()] = j.substring(j.indexOf("=")+1,j.length);
        }
        this.urlArgs=paraObj;
    },
    registerEvents : function(){
        this._super();
        this.getUrlArgs();
        this.loading();
        this.getdatetime();
        this.departmentchange();
        this.submitconfim();
    }

});