/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("RVisitingorderTransaction_List_Js",{},{
    //初始化
    urlArgs:[],
    loading:function(){
        var thisInstance=this;
        var postData={};
        var departmentid=$('#department_editView_fieldName_dropDown').val();
        var userid=$('#user_editView_fieldName_dropDown').val();
        var datetime=$('#datatime').val();
        var enddatetime=$('#enddatatime').val();
        var publict=thisInstance.urlArgs.public==undefined?'':thisInstance.urlArgs.public;
        postData={
            'module': app.getModuleName(),
            'action': 'selectAjax',
            'mode': 'getCountsday',
            'department':departmentid,
            'userid':userid,
            'datetime':datetime,
            'enddatetime':enddatetime,
            'fliter':publict
        };
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
                    var params=[];
                    params['datas']=[];
                    params['deparementname']=[];
                    params['deparementid']=[];
                    $.each(data.result.dataall,function(key,val){
                        if(key=='nums'){
                            params[key]=val;
                        }else{
                            params['datas'].push({
                                name: data.result.department[key],
                                smooth:true,
                                type: 'bar',
                                barMaxWidth:60,
                                data: val
                            });
                            params['deparementid'][data.result.department[key]]=key;
                            params['deparementname'].push(data.result.department[key]);
                        }

                    });
                    params['deparementname'].unshift('');
                    thisInstance.echartscon(params);
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
                'echarts/chart/line'
            ],
            function (ec) {
                var myChart = ec.init(document.getElementById('bartable'));
                myChart.setOption({
                    title : {text: '成交前拜访\n',x:'center'},
                    tooltip : {trigger: 'axis',axisPointer : {type : 'shadow'}},
                    legend: {data:params.deparementname},
                    toolbox: {
                        show : true,
                        orient: 'vertical',
                        x: 'right',
                        y: 'center',
                        feature : {
                            magicType : {show: true, type: ['line', 'bar', 'stack', 'tiled']},
                            restore : {show: true}
                        }
                    },
                    calculable : true,
                    xAxis : [
                        {
                            type : 'category',
                            data : params.nums
                        }
                    ],
                    yAxis : [
                        {
                            type : 'value'
                        }
                    ],
                    series :
                        params.datas
                },true);
                var ecConfig= require('echarts/config');
                myChart.on(ecConfig.EVENT.CLICK,function (param) {
                    if(param.value==0){return false;}
                    var postData={};
                    var publict=thisInstance.urlArgs.public==undefined?'':thisInstance.urlArgs.public;
                    postData={
                        'module':app.getModuleName(),
                        'action':'selectAjax',
                        'mode':'getdetaillist',
                        'nums':param.name,
                        'msg':'a',
                        'paramname':param.seriesName+'　'+this._option.title.text+'　'+param.name+'次　明细',
                        'deparement':params['deparementid'][param.seriesName],
                        'startdatetime':$('#datatime').val(),
                        'enddatetime':$('#enddatatime').val(),
                        'fliter':publict
                    };
                    thisInstance.gettables(postData)
                });
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
                        var div_detail='<table id="tbl_Detail" class="table listViewEntriesTable" width="100%">' +
                            '<thead><tr><th nowrap><b>合同第一次签订日期</b></th><th nowrap><b>负责人</b></th><th nowrap><b>客户名称</b></th><th nowrap><b>成交前拜访次数</b></th></tr></thead><tbody>';
                        $.each(data.result,function(i,val){
                            str+='<tr><td nowrap>'+val.signedate+'</td><td nowrap>'+val.rusername+'</td><td nowrap><a href="index.php?module=Accounts&view=Detail&record='+val.accountid+'" target="view_window">'+val.accountname+'</a></td><td nowrap>'+val.nums+'</td></tr>'
                        });
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
    getrefreshtables:function(){
        var thisInstance=this;
        $('table').on('click','#postrefresh',function(){
            $('#postrefresh').attr('id','postrefresh2');
            postData= {
                'module': app.getModuleName(),
                'action': 'selectAjax',
                'mode': 'getrefreshday'
            };
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
                        var  params = {text : app.vtranslate(),
                            title : app.vtranslate(data.result.msg)};
                        Vtiger_Helper_Js.showPnotify(params);

                    }
                }
            )
        });
    },
    submitconfim:function(){
        var thisInstance=this;
        $('table').on('click','#PostQuery',function(){
            var progressIndicatorElement = jQuery.progressIndicator({
                'message' : '正在处理,请耐心等待哟',
                'position' : 'html',
                'blockInfo' : {'enabled' : true}
            });
            $('#detailtable').empty();
            $('#msg').empty();
            var departmentid=$('#department_editView_fieldName_dropDown').val();
            var userid=$('#user_editView_fieldName_dropDown').val();
            var datetime=$('#datatime').val();
            var enddatetime=$('#enddatatime').val();
            var publict=thisInstance.urlArgs.public==undefined?'':thisInstance.urlArgs.public;
            postData= {
                'module': app.getModuleName(),
                'action': 'selectAjax',
                'mode': 'getCountsday',
                'department':departmentid,
                'userid':userid,
                'datetime':datetime,
                'enddatetime':enddatetime,
                'fliter':publict
            };
            AppConnector.request(postData).then(
                function(data){
                    progressIndicatorElement.progressIndicator({
                        'mode' : 'hide'
                    });
                    if(data.success) {
                        var params = [];
                        params['datas'] = [];
                        params['deparementname'] = [];
                        params['deparementid'] = [];
                        $.each(data.result.dataall, function (key, val) {
                            if (key == 'nums') {
                                params[key] = val;
                            } else {
                                params['datas'].push({
                                    name: data.result.department[key],
                                    smooth: true,
                                    type: 'bar',
                                    barMaxWidth: 60,
                                    data: val
                                });
                                params['deparementid'][data.result.department[key]] = key;
                                params['deparementname'].push(data.result.department[key]);
                            }
                        });
                        params['deparementname'].unshift('');
                        thisInstance.echartscon(params);
                    }
                }
            )
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
                jQuery('#msg').html('<span style="font-size:12px;color:green;text-align:left;">超过1000条不显示</span><span  style="text-align:center;display:block;font-size:16px;color:red;font-weight:bold;">'+postData.paramname+'</span>');
            }
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
        this.getrefreshtables();
    }

});