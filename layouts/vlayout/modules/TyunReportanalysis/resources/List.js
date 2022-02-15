/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("TyunReportanalysis_List_Js",{},{
    default_date_index:4,
    //初始化
    urlArgs:[],
    loading:function(){
        $('#PostQuery').trigger('click');

        //var thisInstance=this;
        //thisInstance.getdatetime();
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
                    title : {text: $('input[name="radio_stat_index"]:checked').parent('label').text() +'\n',x:'center'},
                    tooltip : {trigger: 'axis',axisPointer : {type : 'shadow'}},
                    legend: {data:params.deparementname},
                    toolbox: {show : true,feature : {magicType : {show: true, type: ['line', 'bar']},restore : {show: true}}},
                    calculable : true,
                    xAxis : [{type : 'category',data : params.createdate}],
                    yAxis : [{type : 'value'}],
                    series : params.tyun_reports
                },true);

                /*var ecConfig= require('echarts/config');
                myChart.on(ecConfig.EVENT.DBLCLICK,function (param) {
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
                });*/
               /* var myChart1 = ec.init(document.getElementById('bartableb'));
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
                });*/
            }
        );
    },
    //加载时间控件
    getdatetime:function(){
        var endtime = app.addOneHour();
        $('#start_date').datetimepicker({
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
        $('#end_date').datetimepicker({
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

    marktables:function (data) {
        var thisInstance=this;

        $('#tyun_list_data').hide();
        var result_col = data.result.report_list_col;
        var result_data = data.result.report_list_data;
        /*var div_detail='<table id="tbl_tyun_Detaila" class="table listViewEntriesTable" width="100%"">' +
            '<thead><tr style="background-color: #E6E6E5;">' +
            '<th nowrap><b>部门</b></th>' +
            '<th nowrap><b>负责人</b></th>' +
            '<th nowrap><b>产品</b></th>' +
            '<th nowrap><b>合同编号</b></th>' +
            '<th nowrap><b>签单日期</b></th>' +
            '<th nowrap><b>回款日期</b></th>' +
            '<th nowrap><b>客户数量</b></th>' +
            '<th nowrap><b>合同金额</b></th>' +
            '<th nowrap><b>回款金额</b></th>' +
            '<th nowrap><b>发票金额</b></th>' +
            '</tr></thead><tbody>';*/

        var div_detail='<table id="tbl_tyun_Detaila" class="table listViewEntriesTable" width="100%"">' +
            '<thead><tr style="background-color: #E6E6E5;">';
        $.each(result_col,function(i,val){
            div_detail+='<th nowrap><b>'+ val +'</b></th>';
        });
        div_detail+='</tr></thead><tbody>';

        var str='';
        var is_has_data = true;
        /*$.each(result_data,function(i,val){
            str+='<tr><td nowrap>'+ val.departmentname +'</td><td nowrap>'+ val.last_name +'</td><td nowrap>'+ val.productname +'</td><td nowrap>'+  val.contract_no +'</td>' +
                '<td nowrap>'+  val.signdate + '</td><td nowrap>'+ val.reality_date +'</td><td nowrap>'+ val.accountcount +'</td>'+
                '<td nowrap>'+ val.servicecontractstotal +'</td><td nowrap>'+ val.paymenttotal +'</td><td nowrap>'+ val.allowinvoicetotal +'</td>' +
                '</tr>'
        });*/
        $.each(result_data,function(i,val){
            str+='<tr>';
            $.each(result_col,function(j,val1){
                str+='<td nowrap>'+ val[j] +'</td>';
            })
            str+='</tr>';
        });

        if(is_has_data){
            $('#tyun_list_data').show();
            $('#tyun_etail_data').html(div_detail+str+'</tbody></table>');

            thisInstance.tableinstance(data);
        }
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
                                var div_detail='<table id="tbl_tyun_Detaila" class="table listViewEntriesTable" width="100%"">' +
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
                        thisInstance.tableinstance(postData);

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
    tableinstance:function(postData){
        var table = jQuery('#tbl_tyun_Detaila').DataTable({
            language: {"sProcessing":   "处理中...",	"sLengthMenu":   "显示 _MENU_ 项结果","sZeroRecords":  "没有匹配结果","sInfo":         "显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",	"sInfoEmpty":    "显示第 0 至 0 项结果，共 0 项","sInfoFiltered": "(由 _MAX_ 项结果过滤)","sInfoPostFix":  "","sSearch":"当前页快速检索:","sUrl":"","sEmptyTable":     "表中数据为空","sLoadingRecords": "载入中...",
                "sInfoThousands":  ",",	"oPaginate": {"sFirst":"首页","sPrevious": "上页","sNext":"下页","sLast":"末页"},
                "oAria": {"sSortAscending":  ": 以升序排列此列","sSortDescending": ": 以降序排列此列"}},
            scrollY:"300px",
            sScrollX:"disabled",
            aLengthMenu: [ 20, 50, 100, ],
            fnDrawCallback:function(){
                jQuery('#msg').html('<span style="font-size:12px;color:green;text-align:left;">超过1000条不显示</span><span  style="text-align:center;display:block;font-size:16px;color:red;font-weight:bold;"></span>');
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
      $("table").on('change','#t_timeslot',function(){
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
            //check
            var departmentid=$('#department_editView_fieldName_dropDown').val();
            var tyunReportQuery = {};
            var ownerid="";
            //tyunReportQuery['department']= departmentid;

            $(".SearchConditionRow").each(function () {
                var index = parseInt($(this).attr("data-index"));
                if(index == 0 || index == 999999) return;
                var fieldType = $('#'+searchParamsPreFix+'_field'+ index).find("option:selected").attr('fieldtype');
                var f_name = $("#TyunReportQuery_field"+ index).val();

                if(fieldType == 'datetime' || fieldType == 'date'){
                    if (tyunReportQuery[f_name] !== undefined) {
                        tyunReportQuery[f_name] = tyunReportQuery[f_name] + '&&' + $("#TyunReportQuery_start_value"+ index).val() + '|' + $("#TyunReportQuery_end_value"+ index).val();
                    }else{
                        tyunReportQuery[$("#TyunReportQuery_field"+ index).val()] = fieldType + '##'+ $("#TyunReportQuery_start_value"+ index).val() + '|' + $("#TyunReportQuery_end_value"+ index).val();
                    }
                }else{
                    if (tyunReportQuery[f_name] !== undefined) {
                        tyunReportQuery[f_name] = tyunReportQuery[f_name] + '&&' + $("#TyunReportQuery_value"+ index).val();
                    }else{
                        tyunReportQuery[f_name] = fieldType + '##'+ $("#TyunReportQuery_value"+ index).val();
                    }
                    if(fieldType == 'owner'){
                        if(ownerid == ""){
                            ownerid = $("#TyunReportQuery_value"+ index).val();
                        }else{
                            ownerid+= "," +$("#TyunReportQuery_value"+ index).val();
                        }

                    }
                }
            })
            var col_count = 10;

            if(departmentid && departmentid.length > col_count){
                var  params = {text : app.vtranslate(),title : app.vtranslate('部门选择不能超过'+ col_count +"个")};
                Vtiger_Helper_Js.showPnotify(params);
                return;
            }
            /*if(userid && userid.length > col_count){
                var  params = {text : app.vtranslate(),title : app.vtranslate('人员选择不能超过\'+ col_count +"个')};
                Vtiger_Helper_Js.showPnotify(params);
                return;
            }*/

            $('#tyun_list_data').hide();
            var publict=thisInstance.urlArgs.public==undefined?'':thisInstance.urlArgs.public;

            var stat_index=$('input[name="radio_stat_index"]:checked').val();
            var stat_dim=$('input[name="radio_stat_dim"]:checked').val();
            var stat_type_index = $('input[name="radio_stat_type"]:checked').val();
            var stat_date_type = $('input[name="radio_stat_date_type"]:checked').val();
            postData= {
                'module': app.getModuleName(),
                'action': 'selectAjax',
                'mode': 'getTyunReportData',
                'department':departmentid,
                'ownerid':ownerid,
                'stat_index':stat_index,
                'stat_dim':stat_dim,
                'stat_type_index':stat_type_index,
                'stat_date_type':stat_date_type,
                'tyunReportQuery':JSON.stringify(tyunReportQuery),
                'fliter':publict
            };
            var progressIndicatorElement = jQuery.progressIndicator({
                'message' : '正在拼命努力加载,请耐心等待哟',
                'position' : 'html',
                'blockInfo' : {'enabled' : true}
            });

            AppConnector.request(postData).then(
                function(data){
                    progressIndicatorElement.progressIndicator({
                        'mode' : 'hide'
                    });
                    if(data && data.success) {
                        //
                        thisInstance.init_report(data);
                        //加载列表数据
                        thisInstance.marktables(data);
                    }
                }
            )
        });
    },
    init_report:function(data){
        var thisInstance=this;
        var arr=new Array;
        arr['createdate']=data.result.stat_date;
        arr['deparementname']=new Array;
        arr['deparementid']=new Array;
        arr['options']=new Array;
        arr['tyun_reports']=new Array;
        //arr['createdate'].push(data.result.stat_date)
        $.each(data.result.newdepartmentid,function(k,val){
                var d1='tyun_reports_'+val;
            //arr['deparementid'][data.result.newdepartment[val]]=new Array;
            arr['tyun_reports'].push({
                name: data.result.newdepartment[val],
                smooth:true,
                type: 'bar',
                barMaxWidth:60,
                data: data.result[d1]
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

    register_dateSelect_init:function () {
        var thisInstance=this;
        //设置默认选中状态
        $('.dateSelect').find('li').each(function () {
            var o = $(this);
            var index = o.index();
            if(index == thisInstance.default_date_index){
                if(!o.hasClass('report_date_on')){
                    o.addClass('report_date_on');
                }
            }else{
                o.removeClass('report_date_on');
            }
        })

        switch (thisInstance.default_date_index) {
            case 0:
                $("#start_date").val(getToday());
                $("#end_date").val(getToday());
                break;
            case 1:
                $("#start_date").val(getYestoday());
                $("#end_date").val(getYestoday());
                break;
            case 2:
                $("#start_date").val(getWeekStartDate());
                $("#end_date").val(getWeekEndDate());
                break;
            case 3:
                $("#start_date").val(getLastWeekStartDate());
                $("#end_date").val(getLastWeekEndDate());
                break;
            case 4:
                $("#start_date").val(getMonthStartDate());
                $("#end_date").val(getMonthEndDate());
                break;
            case 5:
                $("#start_date").val(getLastMonthStartDate());
                $("#end_date").val(getLastMonthEndDate());
                break;
            case 6:
                $("#start_date").val(getToday30());
                $("#end_date").val(getToday());
                break;
            case 7:
                $("#start_date").val(getThisYearStartDate());
                $("#end_date").val(getThisYearEndDate());
                break;
            case 8:
                $("#start_date").val(getPreYearStartDate());
                $("#end_date").val(getPreYearEndDate());
                break;
        }
    },
    register_dateSelect_click:function () {
        var thisInstance=this;
        //日期选择处理
        $('.dateSelect').find('li').on('click', function () {
            var o = $(this);
            var os = o.siblings('li');
            var myDate = new Date();
            var index = o.index();

            o.addClass('report_date_on');
            os.removeClass('report_date_on');

            thisInstance.default_date_index = index;

            switch (index) {
                case 0:
                    $("#start_date").val(getToday());
                    $("#end_date").val(getToday());
                    break;
                case 1:
                    $("#start_date").val(getYestoday());
                    $("#end_date").val(getYestoday());
                    break;
                case 2:
                    $("#start_date").val(getWeekStartDate());
                    $("#end_date").val(getWeekEndDate());
                    break;
                case 3:
                    $("#start_date").val(getLastWeekStartDate());
                    $("#end_date").val(getLastWeekEndDate());
                    break;
                case 4:
                    $("#start_date").val(getMonthStartDate());
                    $("#end_date").val(getMonthEndDate());
                    break;
                case 5:
                    $("#start_date").val(getLastMonthStartDate());
                    $("#end_date").val(getLastMonthEndDate());
                    break;
                case 6:
                    $("#start_date").val(getToday30());
                    $("#end_date").val(getToday());
                    break;
                case 7:
                    $("#start_date").val(getThisYearStartDate());
                    $("#end_date").val(getThisYearEndDate());
                    break;
                case 8:
                    $("#start_date").val(getPreYearStartDate());
                    $("#end_date").val(getPreYearEndDate());
                    break;
            }
        });
    },

    register_radio_user_click:function () {
        $('input[name="radio_stat_dim"]').click(function () {
                var v = $(this).val();
                if(v == "2"){
                    $("#div_user_dim").removeClass("div_report_user_hide");
                }else{
                    $("#div_user_dim").addClass("div_report_user_hide");
                }
            }
        )
    },

    register_radio_type_click:function () {
        $('input[name="radio_stat_type"]').click(function () {
            var v = $(this).val();
            if(v == '5'){
                $("#tr_stat_type").hide();
            }else{
                $("#tr_stat_type").show();
            }
        })
    },

    register_radio_dim_click:function () {
        var thisInstance=this;
        $('input[name="radio_stat_index"]').click(function () {
            $("#PostQuery").click();
        })
    },

    registerEvents : function(){
        this._super();
        this.getUrlArgs();
        this.timeslotchange();
        this.departmentchange();
        this.submitconfim();
        this.loading();

        this.register_dateSelect_init();
        this.register_dateSelect_click();
        //this.register_radio_user_click();
        this.register_radio_type_click();
        this.register_radio_dim_click();
    }

});