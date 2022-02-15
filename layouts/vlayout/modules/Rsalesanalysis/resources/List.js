/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("Rsalesanalysis_List_Js",{},{
    //初始化
    urlArgs:[],
    loading:function(){
        $('#PostQuery').trigger('click');
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
               

                var publict=thisInstance.urlArgs.public==undefined?'':thisInstance.urlArgs.public;
                var myChart = ec.init(document.getElementById('bartablea'));
                myChart.setOption({
                    title : {text: '新增客户数\n',x:'center'},
                    tooltip : {trigger: 'axis',axisPointer : {type : 'shadow'}},
                    legend: {data:params.deparementname},
                    toolbox: {show : true,feature : {magicType : {show: true, type: ['line', 'bar']},restore : {show: true}}},
                    calculable : true,
                    xAxis : [{type : 'category',data : params.createdate}],
                    yAxis : [{type : 'value'}],
                    series : params.daycounts
                },true);
                var ecConfig= require('echarts/config');
                myChart.on(ecConfig.EVENT.CLICK,function (param) {
                    if(param.value==0){return false;}
                    var postData={};
                    postData={
                        'module':app.getModuleName(),
                        'action':'selectAjax',
                        'mode':'getdetaillist',
                        'seriesIndex':0,
                        'msg':'a',
                        'paramname':param.name+'　'+param.seriesName+'　'+this._option.title.text+'　明细',
                        'datauserid':params['deparementid'][param.seriesName],
                        'datetime':param.name,
                        'fliter':publict
                    };
                    thisInstance.gettables(postData)
                });
                var myChart1 = ec.init(document.getElementById('bartableb'));
                myChart1.setOption({
                    title : {text: '新增40%客户数\n',x:'center'},
                    tooltip : {trigger: 'axis',axisPointer : {type : 'shadow'}},
                    legend: {data:params.deparementname},
                    toolbox: {show : true,feature : {magicType : {show: true, type: ['line', 'bar']},restore : {show: true}}},
                    calculable : true,
                    xAxis : [{type : 'category',data : params.createdate}],
                    yAxis : [{type : 'value'}],
                    series : params.dayforp
                },true);
                var ecConfig= require('echarts/config');
                myChart1.on(ecConfig.EVENT.CLICK,function (param) {
                    if(param.value==0){return false;}
                    var postData={};

                    postData={
                        'module':app.getModuleName(),
                        'action':'selectAjax',
                        'mode':'getdetaillist',
                        'seriesIndex':1,
                        'msg':'b',
                        'paramname':param.name+'　'+param.seriesName+'　'+this._option.title.text+'　明细',
                        'datauserid':params['deparementid'][param.seriesName],
                        'datetime':param.name,
                        'fliter':publict
                    };
                    thisInstance.gettables(postData)
                });
                var myChart2 = ec.init(document.getElementById('bartablec'));
                myChart2.setOption({
                    title : {text: '拜访完成客户数\n',x:'center'},
                    tooltip : {trigger: 'axis',axisPointer : {type : 'shadow'}},
                    legend: {data:params.deparementname},
                    toolbox: {show : true,feature : {magicType : {show: true, type: ['line', 'bar']},restore : {show: true}}},
                    calculable : true,
                    xAxis : [{type : 'category',data : params.createdate}],
                    yAxis : [{type : 'value'}],
                    series : params.dayvisiting
                },true);
                var ecConfig= require('echarts/config');
                myChart2.on(ecConfig.EVENT.CLICK,function (param) {
                    if(param.value==0){return false;}
                    var postData={};
                    postData={
                        'module':app.getModuleName(),
                        'action':'selectAjax',
                        'mode':'getdetaillist',
                        'seriesIndex':2,
                        'msg':'c',
                        'paramname':param.name+'　'+param.seriesName+'　'+this._option.title.text+'　明细',
                        'datauserid':params['deparementid'][param.seriesName],
                        'datetime':param.name,
                        'fliter':publict
                    };
                    thisInstance.gettables(postData)
                });
                var myChart3 = ec.init(document.getElementById('bartabled'));
                myChart3.setOption({
                    title : {text: '成交客户数\n',x:'center'},
                    tooltip : {trigger: 'axis',axisPointer : {type : 'shadow'}},
                    legend: {data:params.deparementname},
                    toolbox: {show : true,feature : {magicType : {show: true, type: ['line', 'bar']},restore : {show: true}}},
                    calculable : true,
                    xAxis : [{type : 'category',data : params.createdate}],
                    yAxis : [{type : 'value'}],
                    series : params.daysaler
                },true);
                var ecConfig= require('echarts/config');
                myChart3.on(ecConfig.EVENT.CLICK,function (param) {
                    if(param.value==0){return false;}
                    var postData={};
                    postData={
                        'module':app.getModuleName(),
                        'action':'selectAjax',
                        'mode':'getdetaillist',
                        'seriesIndex':3,
                        'msg':'d',
                        'paramname':param.name+'　'+param.seriesName+'　'+this._option.title.text+'　明细',
                        'datauserid':params['deparementid'][param.seriesName],
                        'datetime':param.name,
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
            showMeridian: 0,
            endDate:new Date(),
            weekStart:1,
            todayHighlight:1,
            startView:2,
            minView:2,
            pickerPosition: "bottom-right",
            forceParse:0
        });
        $('#enddatatime').datetimepicker({
            format: "yyyy-mm-dd",
            language:  'zh-CN',
            autoclose: true,
            todayBtn: true,
            showMeridian: 0,
            endDate:new Date(),
            weekStart:1,
            todayHighlight:1,
            startView:2,
            minView:2,
            pickerPosition: "bottom-right",
            forceParse:0
        });
    },
    gettables:function(postData){
        var thisInstance=this;
        var progressIndicatorElement = jQuery.progressIndicator({
            'message' : '正在处理,请耐心等待哟',
            'position' : 'html',
            'blockInfo' : {'enabled' : true}
        });
        $('#listdataa').hide();
        $('#listdatab').hide();
        $('#listdatac').hide();
        $('#listdatad').hide();
        AppConnector.request(postData).then(
            function(data){
                progressIndicatorElement.progressIndicator({
                    'mode' : 'hide'
                });
                if(data.success) {
                    if(data.result.length>0){
                        $('#listdata').show();
                        var str='';
                        switch(postData.seriesIndex){
                            case 0:
                                var div_detail='<table id="tbl_Detaila" class="table listViewEntriesTable" width="100%"">' +
                                    '<thead><tr><th nowrap><b>日期</b></th><th nowrap><b>部门</b></th><th nowrap><b>负责人</b></th><th nowrap><b>客户名称</b></th><th nowrap><b>客户等级</b></th><th nowrap><b>所属行业</b></th><th nowrap><b>主营业务</b></th><th nowrap><b>区域分区</b></th><th nowrap><b>联系人</b></th></tr></thead><tbody>';
                                $.each(data.result,function(i,val){
                                    str+='<tr><td nowrap>'+val.createdtime+'</td><td nowrap>'+val.department+'</td><td nowrap>'+val.smownerid+'</td><td nowrap><a href="index.php?module=Accounts&view=Detail&record='+val.accountid+'" target="view_window">'+val.accountname+'</a></td><td nowrap>'+val.accountrank+'</td><td nowrap>'+val.industry+'</td><td nowrap>'+val.business+'</td><td nowrap>'+val.regionalpartition+'</td><td nowrap>'+val.linkname+'</td></tr>'
                                });
                                $('#listdataa').show();
                                $('#detailtablea').html(div_detail+str+'</tbody></table>');

                                break;
                            case 1:
                                var div_detail='<table id="tbl_Detailb" class="table listViewEntriesTable" width="100%"">' +
                                    '<thead><tr><th nowrap><b>日期</b></th><th nowrap><b>部门</b></th><th nowrap><b>负责人</b></th><th nowrap><b>客户名称</b></th><th nowrap><b>是否老板</b></th><th nowrap><b>邀约拜访时间</b></th></tr></thead><tbody>';
                                $.each(data.result,function(i,val){
                                    str+='<tr><td nowrap>'+val.createdtime+'</td><td nowrap>'+val.department+'</td><td nowrap>'+val.smownerid+'</td><td nowrap><a href="index.php?module=Accounts&view=Detail&record='+val.accountid+'" target="view_window">'+val.accountname+'</a></td><td nowrap>'+val.makedecision+'</td><td nowrap>'+val.firstvisittime+'</td></tr>'
                                });
                                $('#listdatab').show();
                                $('#detailtableb').html(div_detail+str+'</tbody></table>');

                                break;
                            case 2:
                                var div_detail='<table id="tbl_Detailc" class="table listViewEntriesTable" width="100%"">' +
                                    '<thead><tr><th nowrap><b>日期</b></th><th nowrap><b>部门</b></th><th nowrap><b>负责人</b></th><th nowrap><b>客户名称</b></th><th nowrap><b>对方职位</b></th><th nowrap><b>决策圈</b></th><th nowrap><b>所谈业务</b></th><th nowrap><b>拜访次数</b></th><th nowrap><b>拜访目的</b></th></tr></thead><tbody>';
                                $.each(data.result,function(i,val){
                                    str+='<tr><td nowrap>'+val.visitingtime+'</td><td nowrap>'+val.department+'</td><td nowrap>'+val.smownerid+'</td><td nowrap><a href="index.php?module=Accounts&view=Detail&record='+val.accountid+'" target="view_window">'+val.accountname+'</a></td><td nowrap>'+val.title+'</td><td nowrap>'+val.makedecision+'</td><td nowrap>'+val.servicetypename+'</td><td nowrap>'+val.visitingtimes+'</td><td nowrap>'+val.purpose+'</td></tr>'
                                });
                                $('#listdatac').show();
                                $('#detailtablec').html(div_detail+str+'</tbody></table>');

                                break;
                            case 3:
                                var div_detail='<table id="tbl_Detaild" class="table listViewEntriesTable" width="100%"">' +
                                    '<thead><tr><th nowrap><b>所属经理</b></th><th nowrap><b>部门</b></th><th nowrap><b>负责人</b></th><th nowrap><b>入职时间</b></th><th nowrap><b>客户名称</b></th><th nowrap><b>合同编号</b></th><th nowrap><b>成交时间</b></th><th nowrap><b>成交合同</b></th><th nowrap><b>成交合同金额</b></th><th nowrap><b>是否全款</b></th><th nowrap><b>拜访次数</b></th><th nowrap><b>所属行业</b></th></tr></thead><tbody>';
                                $.each(data.result,function(i,val){
                                    str+='<tr><td nowrap>'+val.report_name+'</td><td nowrap>'+val.department+'</td><td nowrap>'+val.smownerid+'</td><td nowrap>'+val.user_entered+'</td><td nowrap><a href="index.php?module=Accounts&view=Detail&record='+val.accountid+'" target="view_window">'+val.accountname+'</a></td><td nowrap><a href="index.php?module=ServiceContracts&view=Detail&record='+val.c_id+'" target="view_window">'+val.c_no+'</a></td><td nowrap>'+val.saleorderlastdealtime+'</td><td nowrap>'+val.productname+'</td><td nowrap>'+val.salescommission+'</td><td nowrap>'+val.until_price+'</td><td nowrap>'+val.visitingtimes+'</td><td nowrap>'+val.industry+'</td></tr>'
                                });
                                $('#listdatad').show();
                                $('#detailtabled').html(div_detail+str+'</tbody></table>');

                                break;
                            default:
                                break;
                        }

                        //$('#detailtable').html(div_detail+str+'</tbody></table>');
                        thisInstance.Tableinstance(postData);

                    }else{
                        $('#detailtable'+postData.msg).html('没有相关数据');
                    }

                } else {

                }
            },
            function(error,err){

            }
        );
    },
    Tableinstance:function(postData){
        var table = jQuery('#tbl_Detail'+postData.msg).DataTable({
            language: {"sProcessing":   "处理中...",	"sLengthMenu":   "显示 _MENU_ 项结果","sZeroRecords":  "没有匹配结果","sInfo":         "显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",	"sInfoEmpty":    "显示第 0 至 0 项结果，共 0 项","sInfoFiltered": "(由 _MAX_ 项结果过滤)","sInfoPostFix":  "","sSearch":"当前页快速检索:","sUrl":"","sEmptyTable":     "表中数据为空","sLoadingRecords": "载入中...",
                "sInfoThousands":  ",",	"oPaginate": {"sFirst":"首页","sPrevious": "上页","sNext":"下页","sLast":"末页"},
                "oAria": {"sSortAscending":  ": 以升序排列此列","sSortDescending": ": 以降序排列此列"}},
            scrollY:"300px",
            sScrollX:"disabled",
            aLengthMenu: [ 10, 20, 50, 100, ],
            fnDrawCallback:function(){
                jQuery('#msg'+postData.msg).html('<span style="font-size:12px;color:green;text-align:left;">超过1000条不显示</span><span  style="text-align:center;display:block;font-size:16px;color:red;font-weight:bold;">'+postData.paramname+'</span>');
            }
        });
    },
    //部门更改显示下面的用户
    departmentchange:function(){
        $('table').on('change','#department_editView_fieldName_dropDown',function(){

        });
    },
    timeslotchange:function(){
        var thisInstance=this;
      $("table").on('change','#timeslot',function(){
          $('.timecode').remove();
          $('.datetimepicker').remove();
          if($(this).val()==15){
              var mydate=new Date();
              var timecode='<label class="pull-left timecode" style="width:100px;"><input class="span12 dateField"type="text"  id="datatime" value="'+mydate.getFullYear()+'-'+(mydate.getMonth()+1)+'-'+mydate.getDate()+'" readonly></label><label class="pull-left timecode" style="margin:5px 10px 0;">到</label><label class="pull-left timecode" style="width:100px;"><input class="span12 dateField"  type="text" name="enddatatime" data-date-format="yyyy-mm-dd" id="enddatatime" value="'+mydate.getFullYear()+'-'+(mydate.getMonth()+1)+'-'+mydate.getDate()+'" readonly></label>';
              $(this).parent().parent().append(timecode);
              thisInstance.getdatetime();
          }

      });
    },
    submitconfim:function(){
        var thisInstance=this;
        $('table').on('click','#PostQuery',function(){
            $('#listdataa').hide();
            $('#listdatab').hide();
            $('#listdatac').hide();
            $('#listdatad').hide();
            var publict=thisInstance.urlArgs.public==undefined?'':thisInstance.urlArgs.public;
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
                'datetime':datetime,
                'startdatetime':startdatetime,
                'enddatetime':enddatetime,
                'fliter':publict
            };
            var progressIndicatorElement = jQuery.progressIndicator({
                'message' : '正在拼命努力加载,请耐心等待哟',
                'position' : 'html',
                'blockInfo' : {'enabled' : true}
            });
            $('#listdata').hide();
            AppConnector.request(postData).then(
                function(data){
                    progressIndicatorElement.progressIndicator({
                        'mode' : 'hide'
                    });
                    if(data.success) {
                        thisInstance.init_report(data);
                    }
                }
            )
        });
    },
    init_report:function(data){
        var thisInstance=this;
        var arr=new Array;
        arr['createdate']=data.result.createdtime;
        arr['deparementname']=new Array;
        arr['deparementid']=new Array;
        arr['options']=new Array;
        arr['daycounts']=new Array;
        arr['dayforp']=new Array;
        arr['dayvisiting']=new Array;
        arr['daysaler']=new Array;
        //arr['createdate'].push(data.result.createdtime)
        $.each(data.result.newdepartmentid,function(k,val){
                var d1='daycounts_'+val;
                var d2='dayforp_'+val;
                var d3='daysaler_'+val;
                var d4='dayvisiting_'+val;
            //arr['deparementid'][data.result.newdepartment[val]]=new Array;
            arr['daycounts'].push({
                name: data.result.newdepartment[val],
                smooth:true,
                type: 'line',
                barMaxWidth:60,
                data: data.result[d1]
            });
            arr['dayforp'].push({
                name: data.result.newdepartment[val],
                smooth:true,
                type: 'line',
                barMaxWidth:60,
                data: data.result[d2]
            });arr['dayvisiting'].push({
                name: data.result.newdepartment[val],
                smooth:true,
                type: 'line',
                barMaxWidth:60,
                data: data.result[d4]
            });arr['daysaler'].push({
                name: data.result.newdepartment[val],
                smooth:true,
                barMaxWidth:60,
                type: 'line',
                data: data.result[d3]
            });
            arr['deparementname'].push(data.result.newdepartment[val]);
            arr['deparementid'][data.result.newdepartment[val]]=val;
            //console.log(data.result[d1]);
        });
        arr['deparementname'].unshift('');
        thisInstance.echartscon(arr);
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
        this.timeslotchange();
        this.departmentchange();
        this.submitconfim();
        this.loading();
    }

});