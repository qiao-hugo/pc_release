/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("RSalesorderBackTop_List_Js",{},{
    //初始化
    urlArgs:[],
    loading:function(){
        var thisInstance=this;
        thisInstance.processing();
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
                //--- 折柱 ---
                var myChart = ec.init(document.getElementById('bartable'));
                myChart.setOption({
                    tooltip : {
                        trigger: 'axis'
                    },
                    legend: {
                        data:['合同到款金额排行榜']
                    },
                    calculable : true,
                    xAxis : [
                        {
                            type : 'value',
                            splitArea : {show : true}
                        }
                    ],
                    yAxis : [
                        {
                            type : 'category',
                            data : params.contractname
                        }
                    ],
                    series : [
                        {
                            name:'合同到款金额排行榜',
                            type:'bar',
                            barWidth:20,
                            itemStyle : { normal: {label : {show: true}}},
                            data:params.contractval
                        }
                    ]
                });
                var ecConfig= require('echarts/config');
                myChart.on(ecConfig.EVENT.CLICK,function (param) {
                    if(params['contractid'][param.name]<1){
                        return ;
                    }
                    var postData={};
                    var departmentid=$('#department_editView_fieldName_dropDown').val();
                    var userid=$('#user_editView_fieldName_dropDown').val();
                    var datetime=$('#datatime').val();
                    var enddatetime=$('#enddatatime').val();
                    var publict=thisInstance.urlArgs.public==undefined?'':thisInstance.urlArgs.public;
                    postData={
                        'module':app.getModuleName(),
                        'action':'selectAjax',
                        'mode':'getcontractdetaillist',
                        'dataIndex':param.dataIndex,
                        'paramname':param.name+'　到款金额明细',
                        'department':departmentid,
                        'userid':userid,
                        'datauserid':params['contractid'][param.name],//不知道那个那个参数可转存,做一个笨方法来转存方便取得用用户ID
                        'datetime':datetime,
                        'enddatetime':enddatetime,
                        'fliter':publict
                    };
                    thisInstance.gettables(postData)
                });

            }
        );
    },
    processing:function(){
        var thisInstance=this;
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
                    var arr=[];
                    arr['contractname']=[];
                    arr['contractid']=[];
                    arr['contractval']=[];
                    if(data.result.Contract.length>0){
                        $.each(data.result.Contract,function(k,val){
                            //长的在上
                            var tempn=data.result.Contract.length-k-1;
                            arr['contractname'][tempn]=val.user_name;
                            arr['contractid'][val.user_name]=val.cid;
                            arr['contractval'][tempn]=new Number(val.totals).toFixed(2);
                        });
                    }

                    if(data.result.Contract.length>0){
                        var dheight=data.result.Contract.length;
                        dheight=dheight*40;
                        if(dheight>400){
                            $("#bartable").css("height",dheight+'px');
                        }else{
                            $("#bartable").css("height",'400px');
                        }
                    }
                    thisInstance.echartscon(arr);

                }
            }
        )
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
                                '<thead><tr><th nowrap><b>到款日期</b></th><th nowrap><b>负责人</b></th><th nowrap><b>客户名称</b></th><th nowrap><b>合同编号</b></th><th nowrap><b>到款金额</b></th></tr></thead><tbody>';
                            $.each(data.result,function(i,val){
                                str+='<tr><td nowrap>'+val.returndate+'</td><td nowrap>'+val.receiveid+'</td><td nowrap><a href="index.php?module=Accounts&view=Detail&record='+val.accountid+'" target="view_window">'+val.accountname+'</a></td><td nowrap><a href="index.php?module=ServiceContracts&view=Detail&record='+val.cid+'" target="view_window">'+val.contract_no+'</a></td><td nowrap>'+val.total+'</td></tr>';
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
            thisInstance.processing();
        });
    },
     //更新有效回款
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