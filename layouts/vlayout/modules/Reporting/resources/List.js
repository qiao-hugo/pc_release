/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("Reporting_List_Js",{},{
    //初始化
    urlArgs:[],
    loading:function(){
        var thisInstance=this;
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
                    params['daycolum']=[];
                    var temparr={daycounts:'新增客户数',gonghai:'公海转入客户数',dayforforp:'新增客户转40%客户数',dayforp:'新增40%客户数',dayvisiting:'拜访完成客户数',dayallvisiting:'拜访单未审核数',daynotfollow:'24小时未跟进拜访数',daysaler:'成交的客户数',didnotsignup:'已跟进未签到的拜访单数',hasbeenfollowedup:'已跟进已签到的拜访单数'};
                    $.each(data.result.dataall,function(key,val){
                        if(key=='classification'){
                            var tempnclass=[];
                            $.each(val,function(k,v){
                                tempnclass.push(temparr[v]);
                                params['daycolum'][temparr[v]]=v;
                            });
                            params[key]=tempnclass;
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
                'echarts/chart/line'
            ],
            function (ec) {
                //--- 折柱 ---
                var myChart = ec.init(document.getElementById('bartable'));
                myChart.setOption({
                    title : {text: '工作总结\n',x:'center'},
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
                            data : params.classification
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
                    var postData={};
                    var userid=$('#user_editView_fieldName_dropDown').val();
                    var datetime=$('#datatime').val();
                    var enddatetime=$('#enddatatime').val();
                    var publict=thisInstance.urlArgs.public==undefined?'':thisInstance.urlArgs.public;
                    postData={'module':app.getModuleName(),
                        'action':'selectAjax',
                        'mode':'getdetaillist',
                        'dataIndex':param.seriesName,
                        'paramname':param.name,
                        'paramcolum':params['daycolum'][param.name],
                        'department':params['deparementid'][param.seriesName],
                        'userid':userid,
                        'datetime':datetime,
                        'enddatetime':enddatetime,
                        'fliter':publict
                };

                    //console.log(params['deparementid'][param.seriesName]);
                    thisInstance.gettables(postData)
                    //console.log(postData);
                    //console.log(param);
                    //console.log(param.seriesName);
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
                        switch(postData.paramcolum){
                            case 'daycounts':
                                var div_detail='<table id="tbl_Detail" class="table listViewEntriesTable" width="100%"">' +
                                    '<thead><tr><th nowrap><b>日期</b></th><th nowrap><b>部门</b></th><th nowrap><b>负责人</b></th><th nowrap><b>客户名称</b></th><th nowrap><b>客户等级</b></th><th nowrap><b>所属行业</b></th><th nowrap><b>主营业务</b></th><th nowrap><b>区域分区</b></th><th nowrap><b>联系人</b></th></tr></thead><tbody>';
                                $.each(data.result,function(i,val){
                                    str+='<tr><td nowrap>'+val.createdtime+'</td><td nowrap>'+val.department+'</td><td nowrap>'+val.smownerid+'</td><td nowrap><a href="index.php?module=Accounts&view=Detail&record='+val.accountid+'" target="view_window">'+val.accountname+'</a></td><td nowrap>'+val.accountrank+'</td><td nowrap>'+val.industry+'</td><td nowrap>'+val.business+'</td><td nowrap>'+val.regionalpartition+'</td><td nowrap>'+val.linkname+'</td></tr>'
                                });
                                break;
                            case 'gonghai':
                                var div_detail='<table id="tbl_Detail" class="table listViewEntriesTable" width="100%"">' +
                                    '<thead><tr><th nowrap><b>日期</b></th><th nowrap><b>部门</b></th><th nowrap><b>负责人</b></th><th nowrap><b>客户名称</b></th><th nowrap><b>客户等级</b></th><th nowrap><b>所属行业</b></th><th nowrap><b>主营业务</b></th><th nowrap><b>区域分区</b></th><th nowrap><b>联系人</b></th></tr></thead><tbody>';
                                $.each(data.result,function(i,val){
                                    str+='<tr><td nowrap>'+val.createdtime+'</td><td nowrap>'+val.department+'</td><td nowrap>'+val.smownerid+'</td><td nowrap><a href="index.php?module=Accounts&view=Detail&record='+val.accountid+'" target="view_window">'+val.accountname+'</a></td><td nowrap>'+val.accountrank+'</td><td nowrap>'+val.industry+'</td><td nowrap>'+val.business+'</td><td nowrap>'+val.regionalpartition+'</td><td nowrap>'+val.linkname+'</td></tr>'
                                });
                                break;
                            case 'dayforforp':
                                var div_detail='<table id="tbl_Detail" class="table listViewEntriesTable" width="100%"">' +
                                    '<thead><tr><th nowrap><b>日期</b></th><th nowrap><b>部门</b></th><th nowrap><b>负责人</b></th><th nowrap><b>客户名称</b></th><th nowrap><b>是否老板</b></th><th nowrap><b>邀约拜访时间</b></th></tr></thead><tbody>';
                                $.each(data.result,function(i,val){
                                    str+='<tr><td nowrap>'+val.createdtime+'</td><td nowrap>'+val.department+'</td><td nowrap>'+val.smownerid+'</td><td nowrap><a href="index.php?module=Accounts&view=Detail&record='+val.accountid+'" target="view_window">'+val.accountname+'</a></td><td nowrap>'+val.makedecision+'</td><td nowrap>'+val.firstvisittime+'</td></tr>'
                                });
                                break;
                            case 'dayforp':
                                var div_detail='<table id="tbl_Detail" class="table listViewEntriesTable" width="100%"">' +
                                    '<thead><tr><th nowrap><b>日期</b></th><th nowrap><b>部门</b></th><th nowrap><b>负责人</b></th><th nowrap><b>客户名称</b></th><th nowrap><b>是否老板</b></th><th nowrap><b>邀约拜访时间</b></th></tr></thead><tbody>';
                                $.each(data.result,function(i,val){
                                    str+='<tr><td nowrap>'+val.createdtime+'</td><td nowrap>'+val.department+'</td><td nowrap>'+val.smownerid+'</td><td nowrap><a href="index.php?module=Accounts&view=Detail&record='+val.accountid+'" target="view_window">'+val.accountname+'</a></td><td nowrap>'+val.makedecision+'</td><td nowrap>'+val.firstvisittime+'</td></tr>'
                                });
                                break;
                            case 'dayvisiting':
                                var div_detail='<table id="tbl_Detail" class="table listViewEntriesTable" width="100%"">' +
                                    '<thead><tr><th nowrap><b>日期</b></th><th nowrap><b>部门</b></th><th nowrap><b>负责人</b></th><th nowrap><b>客户名称</b></th><th nowrap><b>对方职位</b></th><th nowrap><b>决策圈</b></th><th nowrap><b>所谈业务</b></th><th nowrap><b>拜访次数</b></th><th nowrap><b>拜访目的</b></th></tr></thead><tbody>';
                                $.each(data.result,function(i,val){
                                    str+='<tr><td nowrap>'+val.visitingtime+'</td><td nowrap>'+val.department+'</td><td nowrap>'+val.smownerid+'</td><td nowrap><a href="index.php?module=Accounts&view=Detail&record='+val.accountid+'" target="view_window">'+val.accountname+'</a></td><td nowrap>'+val.title+'</td><td nowrap>'+val.makedecision+'</td><td nowrap>'+val.servicetypename+'</td><td nowrap>'+val.visitingtimes+'</td><td nowrap>'+val.purpose+'</td></tr>'
                                });
                                break;
                            case 'didnotsignup':
                                var div_detail='<table id="tbl_Detail" class="table listViewEntriesTable" width="100%"">' +
                                    '<thead><tr><th nowrap><b>日期</b></th><th nowrap><b>部门</b></th><th nowrap><b>负责人</b></th><th nowrap><b>客户名称</b></th><th nowrap><b>对方职位</b></th><th nowrap><b>决策圈</b></th><th nowrap><b>所谈业务</b></th><th nowrap><b>拜访目的</b></th></tr></thead><tbody>';
                                $.each(data.result,function(i,val){
                                    str+='<tr><td nowrap>'+val.visitingtime+'</td><td nowrap>'+val.department+'</td><td nowrap>'+val.smownerid+'</td><td nowrap><a href="index.php?module=Accounts&view=Detail&record='+val.accountid+'" target="view_window">'+val.accountname+'</a></td><td nowrap>'+val.title+'</td><td nowrap>'+val.makedecision+'</td><td nowrap>'+val.servicetypename+'</td><td nowrap>'+val.purpose+'</td></tr>'
                                });
                                break;
                            case 'hasbeenfollowedup':
                                var div_detail='<table id="tbl_Detail" class="table listViewEntriesTable" width="100%"">' +
                                    '<thead><tr><th nowrap><b>日期</b></th><th nowrap><b>部门</b></th><th nowrap><b>负责人</b></th><th nowrap><b>客户名称</b></th><th nowrap><b>对方职位</b></th><th nowrap><b>决策圈</b></th><th nowrap><b>所谈业务</b></th><th nowrap><b>拜访目的</b></th></tr></thead><tbody>';
                                $.each(data.result,function(i,val){
                                    str+='<tr><td nowrap>'+val.visitingtime+'</td><td nowrap>'+val.department+'</td><td nowrap>'+val.smownerid+'</td><td nowrap><a href="index.php?module=Accounts&view=Detail&record='+val.accountid+'" target="view_window">'+val.accountname+'</a></td><td nowrap>'+val.title+'</td><td nowrap>'+val.makedecision+'</td><td nowrap>'+val.servicetypename+'</td><td nowrap>'+val.purpose+'</td></tr>'
                                });
                                break;
                            case 'dayallvisiting':
                                var div_detail='<table id="tbl_Detail" class="table listViewEntriesTable" width="100%"">' +
                                    '<thead><tr><th nowrap><b>拜访单开始日期</b></th><th nowrap><b>部门</b></th><th nowrap><b>负责人</b></th><th nowrap><b>客户名称</b></th><th nowrap><b>对方职位</b></th><th nowrap><b>决策圈</b></th><th nowrap><b>所谈业务</b></th><th nowrap><b>拜访次数</b></th><th nowrap><b>拜访目的</b></th></tr></thead><tbody>';
                                $.each(data.result,function(i,val){
                                    str+='<tr><td nowrap>'+val.visitingtime+'</td><td nowrap>'+val.department+'</td><td nowrap>'+val.smownerid+'</td><td nowrap><a href="index.php?module=Accounts&view=Detail&record='+val.accountid+'" target="view_window">'+val.accountname+'</a></td><td nowrap>'+val.title+'</td><td nowrap>'+val.makedecision+'</td><td nowrap>'+val.servicetypename+'</td><td nowrap>'+val.visitingtimes+'</td><td nowrap>'+val.purpose+'</td></tr>'
                                });
                                break;

                            case 'daynotfollow':
                                var div_detail='<table id="tbl_Detail" class="table listViewEntriesTable" width="100%"">' +
                                    '<thead><tr><th nowrap><b> 拜访单结束日期</b></th><th nowrap><b>部门</b></th><th nowrap><b>负责人</b></th><th nowrap><b>客户名称</b></th><th nowrap><b>对方职位</b></th><th nowrap><b>决策圈</b></th><th nowrap><b>所谈业务</b></th><th nowrap><b>拜访次数</b></th><th nowrap><b>拜访目的</b></th></tr></thead><tbody>';
                                $.each(data.result,function(i,val){
                                    str+='<tr><td nowrap>'+val.visitingtime+'</td><td nowrap>'+val.department+'</td><td nowrap>'+val.smownerid+'</td><td nowrap><a href="index.php?module=Accounts&view=Detail&record='+val.accountid+'" target="view_window">'+val.accountname+'</a></td><td nowrap>'+val.title+'</td><td nowrap>'+val.makedecision+'</td><td nowrap>'+val.servicetypename+'</td><td nowrap>'+val.visitingtimes+'</td><td nowrap>'+val.purpose+'</td></tr>'
                                });
                                break;
                            case 'daysaler':
                                var div_detail='<table id="tbl_Detail" class="table listViewEntriesTable" width="100%"">' +
                                    '<thead><tr><th nowrap><b>所属经理</b></th><th nowrap><b>部门</b></th><th nowrap><b>负责人</b></th><th nowrap><b>入职时间</b></th><th nowrap><b>客户名称</b></th><th nowrap><b>合同编号</b></th><th nowrap><b>成交时间</b></th><th nowrap><b>成交合同</b></th><th nowrap><b>成交合同金额</b></th><th nowrap><b>是否全款</b></th><th nowrap><b>拜访次数</b></th><th nowrap><b>所属行业</b></th></tr></thead><tbody>';
                                $.each(data.result,function(i,val){
                                    str+='<tr><td nowrap>'+val.report_name+'</td><td nowrap>'+val.department+'</td><td nowrap>'+val.smownerid+'</td><td nowrap>'+val.user_entered+'</td><td nowrap><a href="index.php?module=Accounts&view=Detail&record='+val.accountid+'" target="view_window">'+val.accountname+'</a></td><td nowrap><a href="index.php?module=ServiceContracts&view=Detail&record='+val.c_id+'" target="view_window">'+val.c_no+'</a></td><td nowrap>'+val.saleorderlastdealtime+'</td><td nowrap>'+val.productname+'</td><td nowrap>'+val.salescommission+'</td><td nowrap>'+val.until_price+'</td><td nowrap>'+val.visitingtimes+'</td><td nowrap>'+val.industry+'</td></tr>'
                                });
                                break;
                            default:
                                break;
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
                        var params=[];
                        params['datas']=[];
                        params['deparementname']=[];
                        params['deparementid']=[];
                        params['daycolum']=[];
                        var temparr={daycounts:'新增客户数',gonghai:'公海转入客户数',dayforforp:'新增客户转40%客户数',dayforp:'新增40%客户数',dayvisiting:'拜访完成客户数',dayallvisiting:'拜访单未审核数',daynotfollow:'24小时未跟进拜访数',daysaler:'成交的客户数',didnotsignup:'已跟进未签到的拜访单数',hasbeenfollowedup:'已跟进已签到的拜访单数'};
                        $.each(data.result.dataall,function(key,val){
                            if(key=='classification'){
                                var tempnclass=[];
                                $.each(val,function(k,v){
                                    tempnclass.push(temparr[v]);
                                    params['daycolum'][temparr[v]]=v;
                                });
                                params[key]=tempnclass;
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
                'message' : '数据量比较大,请耐心等待哟',
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
                jQuery('#msg').html('<span style="font-size:12px;color:green;text-align:left;">超过1000条不显示</span><span  style="text-align:center;display:block;font-size:16px;color:red;font-weight:bold;">'+postData.dataIndex+'  '+postData.paramname+'</span>');
            }
        });
    },
    getrefreshvisiting:function(){
        var thisInstance=this;
        $('table').on('click','#visitrefresh',function(){
            $('#visitrefresh').attr('id','visitrefresh2');
            postData= {
                'module': app.getModuleName(),
                'action': 'selectAjax',
                'mode': 'getvisitrefresh'
            };
            var progressIndicatorElement = jQuery.progressIndicator({
                'message' : '数据量比较大,请耐心等待哟',
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
    visitconfim:function(){
        var thisInstance=this;
        $('table').on('click','#visitQuery',function(){
            var progressIndicatorElement = jQuery.progressIndicator({
                'message' : '正在处理,请耐心等待哟',
                'position' : 'html',
                'blockInfo' : {'enabled' : true}
            });
            $('#bartable').empty();

            var departmentid=$('#department_editView_fieldName_dropDown').val();
            var userid=$('#user_editView_fieldName_dropDown').val();
            var datetime=$('#timeslot').val();
            //var enddatetime=$('#enddatatime').val();
            postData= {
                'module': app.getModuleName(),
                'action': 'selectAjax',
                'mode': 'getvisitstatistics',
                'department':departmentid,
                'userid':userid,
                'datetime':datetime
            };

            AppConnector.requestPjaxPost(postData).then(
                function(data){
                    progressIndicatorElement.progressIndicator({
                        'mode' : 'hide'
                    });
                    $('#bartable').html(data);
                    $("#flaltt1").smartFloat();
                }
            )
        });
    },
    accountdconfim:function(){
        var thisInstance=this;
        $('table').on('click','#visitQuery',function(){
            var progressIndicatorElement = jQuery.progressIndicator({
                'message' : '正在处理,请耐心等待哟',
                'position' : 'html',
                'blockInfo' : {'enabled' : true}
            });
            $('#bartable').empty();

            var departmentid=$('#department_editView_fieldName_dropDown').val();
            var userid=$('#user_editView_fieldName_dropDown').val();
            var datetime=$('#timeslot').val();
            //var enddatetime=$('#enddatatime').val();
            postData= {
                'module': app.getModuleName(),
                'action': 'selectAjax',
                'mode': 'getaccountstatistics',
                'department':departmentid,
                'userid':userid,
                'datetime':datetime
            };

            AppConnector.requestPjaxPost(postData).then(
                function(data){
                    progressIndicatorElement.progressIndicator({
                        'mode' : 'hide'
                    });
                    $('#bartable').html(data);
                    $("#fixscrollrf").smartFloat();
                }
            )
        });
    },
    entryconfim:function(){
        var thisInstance=this;
        $('table').on('click','#visitQuery',function(){
            var progressIndicatorElement = jQuery.progressIndicator({
                'message' : '正在处理,请耐心等待哟',
                'position' : 'html',
                'blockInfo' : {'enabled' : true}
            });
            $('#bartable').empty();

            var departmentid=$('#department_editView_fieldName_dropDown').val();
            var userid=$('#user_editView_fieldName_dropDown').val();
            var datetime=$('#timeslot').val();
            //var enddatetime=$('#enddatatime').val();
            postData= {
                'module': app.getModuleName(),
                'action': 'selectAjax',
                'mode': 'getentrystatistics',
                'department':departmentid,
                'userid':userid,
                'datetime':datetime
            };

            AppConnector.requestPjaxPost(postData).then(
                function(data){
                    progressIndicatorElement.progressIndicator({
                        'mode' : 'hide'
                    });
                    $('#bartable').html(data);
                    $("#fixscrollrf").smartFloat();
                }
            )
        });
    },
    performance:function(){
        var thisInstance=this;
        $('table').on('click','#visitQuery',function(){
            var progressIndicatorElement = jQuery.progressIndicator({
                'message' : '正在处理,请耐心等待哟',
                'position' : 'html',
                'blockInfo' : {'enabled' : true}
            });
            $('#bartable').empty();

            var departmentid=$('#department_editView_fieldName_dropDown').val();
            var userid=$('#user_editView_fieldName_dropDown').val();
            var datetime=$('#timeslot').val();
            //var enddatetime=$('#enddatatime').val();
            postData= {
                'module': app.getModuleName(),
                'action': 'selectAjax',
                'mode': 'getperformance',
                'department':departmentid,
                'userid':userid,
                'datetime':datetime
            };

            AppConnector.requestPjaxPost(postData).then(
                function(data){
                    progressIndicatorElement.progressIndicator({
                        'mode' : 'hide'
                    });
                    $('#bartable').html(data);
                    $("#fixscrollrf").smartFloat();
                }
            )
        });
    },
    request:function(paras){
        var thisInstance=this;
        var url = location.href;
        var paraString = url.substring(url.indexOf("?")+1,url.length).split("&");
        //console.log(paraString);
        var paraObj = {}
        for (i=0; j=paraString[i]; i++){
            paraObj[j.substring(0,j.indexOf("=")).toLowerCase()] = j.substring(j.indexOf("=")+1,j.length);
        }
        var returnValue = paraObj['public'];
        //console.log(returnValue);
        if(returnValue=='visitstatistics'){
            thisInstance.visitconfim();
            thisInstance.getrefreshvisiting();
        }else if(returnValue=='accountstatistics'){
            thisInstance.accountdconfim();
        }else if(returnValue=='entrystatistics'){
             thisInstance.entryconfim();
        }else if(returnValue=='performance'){
            thisInstance.performance();
         }else{
            thisInstance.loading();
            thisInstance.submitconfim();
            thisInstance.getrefreshtables();
        }

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
        this.request();
        this.getdatetime();
        this.departmentchange();

    }
});