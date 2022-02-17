/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("TelStatistics_List_Js",{},{
    changeUserData:function(){
		var thisinstance=this;
		$('#page').on('click','.changeUserData',function(){
            var progressIndicatorElement1 = jQuery.progressIndicator({ 'message' : '数据获取中...','blockInfo':{'enabled':true }});

            var recordid=$(this).data('id');
            var username=$(this).closest('tr').children('.useid').text();
            var telnumberdate=$(this).closest('tr').children('.telnumberdate').text();
            var teldurationObj=$(this).closest('tr').children('.telduration');
            var totaltelnumberObj=$(this).closest('tr').children('.total_telnumber');
            var telconnectrateObj=$(this).closest('tr').children('.tel_connect_rate');
            var telnumberObj=$(this).closest('tr').children('.telnumber');

            // var traget_amountObj=$(this).closest('tr').children('.traget_amount');
            // var actual_incomeObj=$(this).closest('tr').children('.actual_income');
            // var promotion_incomeObj=$(this).closest('tr').children('.promotion_income');
            // var month_of_dayObj=$(this).closest('tr').children('.month_of_day');
            // var reach_dayObj=$(this).closest('tr').children('.reach_day');
            // var wxaddObj=$(this).closest('tr').children('.wxadd');
            // var searchinfoObj=$(this).closest('tr').children('.searchinfo');

            // var total_teldurationObj=$(this).closest('tr').children('.total_telduration');
            // var total_invitenumObj=$(this).closest('tr').children('.total_invitenum');
            // var total_visitnumObj=$(this).closest('tr').children('.total_visitnum');
            // var total_strangevisitnumObj=$(this).closest('tr').children('.total_strangevisitnum');

            var message='<h5>修改-'+username+'--'+telnumberdate+'</h5><hr />';
            var msg={
                'message':message,
                "width":600,
                "action":function(){
                    var temptelnumber = $('#telnumber').val();
                    var temptelduration = $('#telduration').val();
                    if(isNaN(temptelnumber) || isNaN(temptelduration)){
                        var params = {
                            text: '请填写有效的数学',
                            type: 'error'
                        };
                        Vtiger_Helper_Js.showMessage(params);
                        return false;
                    }
                    if(temptelnumber=='' || Number(temptelnumber)<0 || temptelduration=='' || Number(temptelduration)<0 ) {
                        var params = {
                            text: '必填项不能为空，必为数字且不能小于0',
                            type: 'error'
                        };
                        Vtiger_Helper_Js.showMessage(params);
                        return false;
                    }
                    return true;
                }
            };
            var params={};
            params['record'] = recordid;
            params['action'] = 'ChangeAjax';
            params['module'] = 'TelStatistics';
            params['mode'] = 'getRecordData';
            var p={};
            p.data=params;
            p.async = false;
            var dataflag=false;
            var telnumber=0;
            var telduration=0;
            var total_telnumber = 0;
            var tel_connect_rate = 0;
            // var traget_amount=0;
            // var actual_income=0;
            // var promotion_income=0;
            // var month_of_day=0;
            // var reach_day=0;
            // var wxadd=0;
            // var searchinfo=0;
            // var returnmsg='';
            // var total_telduration = 0;
            // var total_invitenum = 0;
            // var total_visitnum = 0;
            // var total_strangevisitnum = 0;

            AppConnector.request(p,true).then(
                function(data) {
                    progressIndicatorElement1.progressIndicator({ 'mode' : 'hide'});
                    if(data.result.flag){
                        telnumber=data.result.data.telnumber;
                        telduration=data.result.data.telduration;
                        total_telnumber = data.result.data.total_telnumber;

                        // total_telduration = data.result.data.total_telduration;
                        // total_invitenum = data.result.data.total_invitenum;
                        // total_visitnum = data.result.data.total_visitnum;
                        // total_strangevisitnum = data.result.data.total_strangevisitnum;
                        tel_connect_rate = data.result.data.tel_connect_rate;

                        // traget_amount = data.result.data.traget_amount;
                        // actual_income = data.result.data.actual_income;
                        // promotion_income = data.result.data.promotion_income;
                        // month_of_day = data.result.data.month_of_day;
                        // reach_day = data.result.data.reach_day;
                        // wxadd = data.result.data.wxadd;
                        // searchinfo = data.result.data.searchinfo;
                    }else{
                        dataflag=true;
                        returnmsg=data.result.msg;
                    }
                },
                function(error,err){
                    //window.location.reload(true);
                }
            );
            if(dataflag){
                var params = {
                    text: returnmsg,
                    type: 'error'
                };
                Vtiger_Helper_Js.showMessage(params);
                return;
            }
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                var flag = checkNoEmpty();
                if(!flag){
                    return false;
                }
                var params={};
                var changetelduration=$('#telduration').val();
                var changetelnumber=$('#telnumber').val();
                var changetotaltelnumber = $('#total_telnumber').val();
                // var changetotaltotal_telduration = $('#total_telduration').val();
                // var changetotaltotal_invitenum = $('#total_invitenum').val();
                // var changetotaltotal_visitnum = $('#total_visitnum').val();
                // var changetotaltotal_strangevisitnum = $('#total_strangevisitnum').val();
                var changetelconnectrate = $("#tel_connect_rate").val();

                // var changetetraget_amount=$('#traget_amount').val();
                // var changeteactual_income=$('#actual_income').val();
                // var changetepromotion_income=$('#promotion_income').val();
                // var changetemonth_of_day=$('#month_of_day').val();
                // var changetereach_day=$('#reach_day').val();
                // var changetewxadd=$('#wxadd').val();
                // var changetesearchinfo=$('#searchinfo').val();

                params['record'] = recordid;
                params['telduration'] =  changetelduration;
                params['telnumber'] = changetelnumber;
                params['total_telnumber'] = changetotaltelnumber;
                params['tel_connect_rate'] = changetelconnectrate;
                // params['total_telduration'] = changetotaltotal_telduration;
                // params['total_invitenum'] = changetotaltotal_invitenum;
                // params['total_visitnum'] = changetotaltotal_visitnum;
                // params['total_strangevisitnum'] = changetotaltotal_strangevisitnum;
                //
                // params['traget_amount'] = changetetraget_amount;
                // params['actual_income'] = changeteactual_income;
                // params['promotion_income'] = changetepromotion_income;
                // params['month_of_day'] = changetemonth_of_day;
                // params['reach_day'] = changetereach_day;
                // params['wxadd'] = changetewxadd;
                // params['searchinfo'] = changetesearchinfo;
                params['action'] = 'ChangeAjax';
                params['module'] = 'TelStatistics';
                params['mode'] = 'changeUserData';
                AppConnector.request(params).then(
                    function(data) {
                        if(data.result.flag){
                            teldurationObj.text(changetelduration);
                            telnumberObj.text(changetelnumber);
                            totaltelnumberObj.text(changetotaltelnumber);
                            telconnectrateObj.text(changetelconnectrate);

                            // total_teldurationObj.text(changetotaltotal_telduration);
                            // total_invitenumObj.text(changetotaltotal_invitenum);
                            // total_visitnumObj.text(changetotaltotal_visitnum);
                            // total_strangevisitnumObj.text(changetotaltotal_strangevisitnum);
                            //
                            // traget_amountObj.text(changetetraget_amount);
                            // actual_incomeObj.text(changeteactual_income);
                            // promotion_incomeObj.text(changetepromotion_income);
                            // month_of_dayObj.text(changetemonth_of_day);
                            // reach_dayObj.text(changetereach_day);
                            // wxaddObj.text(changetewxadd);
                            // searchinfoObj.text(changetesearchinfo);

                            var params = {
                                text: '修改成功',
                                type: 'success'
                            };
                            Vtiger_Helper_Js.showMessage(params);
                        }else{
                            var params = {
                                text: data.result.msg,
                                type: 'error'
                            };
                            Vtiger_Helper_Js.showMessage(params);
                        }
                    },
                    function(error,err){
                        //window.location.reload(true);
                    }
                );
            },function(error, err) {});
            var str = '<table class="table table-bordered blockContainer Duplicates showInlineTable  detailview-table">' +
                '<tbody>' +
                // '<tr style="display: none"><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>本月目标</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><input class="span11 input-large checknumber" name="traget_amount" type="text" id="traget_amount" value="0" /></span></div></td></tr>' +
                // '<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>本月实际进款</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><input class="span11 input-large checknumber" name="actual_income" type="text" id="actual_income" value="'+actual_income+'" /></span></div></td></tr>' +
                // '<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>本月保级进款</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><input class="span11 input-large checknumber" name="promotion_income" type="text" id="promotion_income" value="'+promotion_income+'" /></span></div></td></tr>' +
                // '<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>本月总天数</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><input class="span11 input-large checknumber" name="month_of_day" type="text" id="month_of_day" value="'+month_of_day+'" /></span></div></td></tr>' +
                // '<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>工作达标天数</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><input class="span11 input-large checknumber" name="reach_day" type="text" id="reach_day" value="'+reach_day+'" /></span></div></td></tr>' +
                // '<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>微信添加（新增客户/活跃客户）</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><input class="span11 input-large checknumber" name="wxadd" type="text" id="wxadd" value="'+wxadd+'" /></span></div></td></tr>' +
                // '<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>累计邀约量（KP/负责人）</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><input class="span11 input-large checknumber" name="total_invitenum" type="text" id="total_invitenum" value="'+total_invitenum+'" /></span></div></td></tr>' +
                // '<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>累计拜访量（独立几次，被陪访几次）</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><input class="span11 input-large checknumber" name="total_visitnum" type="text" id="total_visitnum" value="'+total_visitnum+'" /></span></div></td></tr>' +
                // '<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>累计陌拜量</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><input class="span11 input-large checknumber" name="total_strangevisitnum" type="text" id="total_strangevisitnum" value="'+total_strangevisitnum+'" /></span></div></td></tr>' +
                // '<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>查资料</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><input class="span11 input-large checknumber" name="searchinfo" type="text" id="searchinfo" value="'+searchinfo+'" /></span></div></td></tr>' +
                '<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>总电话量(个)</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><input type="text" class="span11 input-small" name="total_telnumber" id="total_telnumber" value="'+total_telnumber+'" /></span></div></td></tr>' +
                '<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>电话量(个)</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><input type="hidden" id="telstatistics" value="'+recordid+'"><input type="text" class="span11 input-small" name="telnumber" id="telnumber" value="'+telnumber+'" /></span></div></td></tr>' +
                // '<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>总电话时长（分钟）</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><input class="span11 input-large checknumber" name="total_telduration" type="text" id="total_telduration" value="'+total_telduration+'" /></span></div></td></tr>' +
                '<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>电话时长（分钟）</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><input class="span11 input-large checknumber" name="telduration" type="text" id="telduration" value="'+telduration+'" /></span></div></td></tr>' +
                '</tbody></table>';
            $('.modal-content .modal-body').append(str);
            $('.modal-content .modal-body').css({overflow:'scroll'});
            $('.modal-content .modal-body').css('max-height','600px');

		});
	},
    loading : function(data){
        Vtiger_Helper_Js.showConfirmationBox =function(data){
            var aDeferred = jQuery.Deferred();
            var width='800px';
            var checkFlag=true
            if(typeof  data['width'] != "undefined"){
                width=data['width'];
            }
            var bootBoxModal = bootbox.confirm({message:data['message'],width:width, callback:function(result) {
                    if(result){
                        if(typeof  data['action'] != "undefined"){
                            checkFlag=(data['action'])();
                        }
                        if(checkFlag){
                            aDeferred.resolve();
                        }else{
                            return false;
                        }
                    } else{
                        aDeferred.reject();
                    }
                }, buttons: { cancel: {
                        label: '取消',
                        className: 'btn'
                    },
                    confirm: {
                        label: '确认',
                        className: 'btn-success'
                    }
                }});
            bootBoxModal.on('hidden',function(e){
                if(jQuery('#globalmodal').length > 0) {
                    jQuery('body').addClass('modal-open');
                }
            })
            return aDeferred.promise();
        }
        var thisInstance=this;
        var url = location.href;
        var paraString = url.substring(url.indexOf("?")+1,url.length).split("&");
        //console.log(paraString);
        var paraObj = {}
        for (var i=0,j=0; j=paraString[i]; i++){
            paraObj[j.substring(0,j.indexOf("=")).toLowerCase()] = j.substring(j.indexOf("=")+1,j.length);
        }
        var returnValue = paraObj['public'];
        if(returnValue=='eworkstatistics'){
            thisInstance.geteworkstatisticsdata();
            thisInstance.geteworkstatisticsfresh();
            thisInstance.getdetaileworkstatistics();
            thisInstance.getdatetime();
        }else if(returnValue=='eworksituationtrends'){
            thisInstance.geteworkstatisticsdata();
            thisInstance.geteworkstatisticsfresh();
            thisInstance.getdetaileworkstatistics();
            thisInstance.getdatetime();
        }else{
            thisInstance.changeUserData();
        }
    },
    geteworkstatisticsfresh:function(){
        $('#postrefresh').click(function(){
            var params={};
            params['action'] = 'ChangeAjax';
            params['module'] = 'TelStatistics';
            params['mode'] = 'geteworkstatisticsfresh';
            var progressIndicatorElement1 = jQuery.progressIndicator({ 'message' : '数据更新中...','blockInfo':{'enabled':true }});
            AppConnector.request(params).then(
                function(data) {
                    progressIndicatorElement1.progressIndicator({ 'mode' : 'hide'});
                    var params = {
                        text: data.result.msg,
                        type: 'success'
                    };
                    Vtiger_Helper_Js.showMessage(params);
                },
                function(error,err){

                }
            );
        });
    },
    geteworkstatisticsdata:function(){
        var thisInstance=this;
        $('table').on('click','#eworkstatisticsPostQuery',function(){
            var progressIndicatorElement = jQuery.progressIndicator({
                'message' : '正在处理,请耐心等待哟',
                'position' : 'html',
                'blockInfo' : {'enabled' : true}
            });

            var departmentid=$('#department_editView_fieldName_dropDown').val();
            var userid=$('#user_editView_fieldName_dropDown').val();
            var datetime=$('#datatime').val();
            var enddatetime=$('#enddatatime').val();
            var modefrom=$('#MODEFROM').data('value');
            var postData= {
                'module': app.getModuleName(),
                'action': 'ChangeAjax',
                'mode': 'geteworkstatisticsdata',
                'department':departmentid,
                'userid':userid,
                'datetime':datetime,
                'enddatetime':enddatetime,
                'modefrom':modefrom
            };
            if(modefrom=='eworkstatistics'){
                $('.getdetaileworkstatistics').popover('hide');
                $('#bartable').empty();
                AppConnector.requestPjaxPost(postData).then(
                    function(data){
                        progressIndicatorElement.progressIndicator({
                            'mode' : 'hide'
                        });
                        $('#bartable').html(data);
                        $("#fixscrollrf").smartFloat();
                    }
                )
            }else{
                $('.detailtableeworksituationtrends').hide();
                postData.mode='geteworksituationtrendsdata';
                AppConnector.request(postData).then(
                    function(data){
                        progressIndicatorElement.progressIndicator({
                            'mode' : 'hide'
                        });
                        if(data.success){
                            thisInstance.init_report(data);
                        }
                    }
                )
            }
        });
        $.fn.smartFloat = function() {
            var position = function(element) {
                var top = element.position().top; //当前元素对象element距离浏览器上边缘的距离
                var pos = element.css("position"); //当前元素距离页面document顶部的距离
                $(window).scroll(function() { //侦听滚动时
                    var scrolls = $(this).scrollTop();
                    if (scrolls > 225) { //如果滚动到页面超出了当前元素element的相对页面顶部的高度
                        $('#flalted').css({width:$('#one1').width()+2});
                        $('#fixscrollrf').css({width:$('#scrollrf').width()});
                        $("#flaltt1>th").each(function(i){
                            $("#flalte1>th").eq(i).css({width:$("#flaltt1>th").eq(i).width()});
                        });

                        $('#fixscrollrf').css({position: "fixed", //固定定位,即不再跟随滚动
                            top: 45}).removeClass('hide');
                    }else {

                        $('#fixscrollrf').addClass('hide');
                    }
                });
                $('#scrollrf').scroll(function() { //侦听滚动时
                    var scrollleft=$('#one1').offset();
                    if(scrollleft.left<30){
                        $('#flalted').css({'left':scrollleft.left-30});
                    }else{
                        $('#flalted').css({'left':0});
                    }
                });
            };
            return $(this).each(function() {
                position($(this));
            });
        };
    },
    getdetaileworkstatistics:function(){
        $('#bartable').on('click','.getdetaileworkstatistics',function(){
            var _this=this;
            if($(_this).attr('clickfalg')==2){
                return;
            }
            var progressIndicatorElement = jQuery.progressIndicator({
                'message' : '正在处理,请耐心等待哟',
                'position' : 'html',
                'blockInfo' : {'enabled' : true}
            });
            var postData= {
                'module': app.getModuleName(),
                'action': 'ChangeAjax',
                'mode': 'getdetaileworkstatistics',
                'datas':$(_this).data('uid')
            };

            var module = $(_this).data('module');
            AppConnector.request(postData).then(
                function(data){
                    progressIndicatorElement.progressIndicator({
                        'mode' : 'hide'
                    });
                    if(data.result.flag){
                        var username=$(_this).siblings('.username').data('username');
                        var edate=$(_this).siblings('.edate').data('edate');
                        var str='';
                        $(_this).removeAttr('title');
                        if(!module){
                            $.each(data.result.data,function(key,value){
                                str+='<a href="/index.php?module='+data.result.module+'&view=Detail&record='+value.showid+'" target="_blank">'+value.showname+'</a><br>'
                            });
                        }else{
                            str +='<table>';
                            switch (module) {
                                case 'verifynum':
                                    str += '<tr><th>模块</th><th>编号</th><th>客户</th></tr>'
                                    $.each(data.result.data,function(key,value){
                                        str += '<tr>' +
                                            '<td>'+value.transmodulename+'</td>' +
                                            '<td>'+'<a href="/index.php?module='+value.modulename+'&view=Detail&record='+value.showid+'" target="_blank">'+(value.showname?value.showname:'---')+'</a><br>'+'</td>' +
                                            '<td>'+value.accountname+'</td>' +
                                            '</tr>'
                                    });
                                    break;
                                case 'followupaccountnum':
                                    str += '<tr><th>公司名称</th><th>跟进内容</th></tr>'
                                    $.each(data.result.data,function(key,value){
                                        str += '<tr>' +
                                            '<td>'+'<a href="/index.php?module='+data.result.module+'&view=Detail&record='+value.showid+'" target="_blank">'+value.showname+'</a><br>'+'</td>' +
                                            '<td>'+value.commentcontent+'</td>' +
                                            '</tr>'
                                    });
                                    break;
                                case 'commentaccountnum':
                                    str += '<tr><th>公司名称</th><th>评论内容</th></tr>'
                                    $.each(data.result.data,function(key,value){
                                        str += '<tr>' +
                                            '<td>'+'<a href="/index.php?module='+data.result.module+'&view=Detail&record='+value.showid+'" target="_blank">'+value.showname+'</a><br>'+'</td>' +
                                            '<td>'+value.modcommenthistory+'</td>' +
                                            '</tr>'
                                    });
                                    break;
                                case 'newvisitingnum':
                                    str += '<tr><th>客户</th></tr>'
                                    $.each(data.result.data,function(key,value){
                                        str += '<tr>' +
                                            '<td>'+'<a href="/index.php?module='+data.result.module+'&view=Detail&record='+value.showid+'" target="_blank">'+value.showname+'</a><br>'+'</td>' +
                                            '</tr>'
                                    });
                                    break;
                            }
                            str += '</table>';
                        }

                        $(_this).attr('clickfalg',2);
                        $(_this).attr('data-title',username+'-'+edate);
                        $(_this).attr('data-content',str);
                        $(_this).popover("show");
                        $(".popover.right").attr("style:max-width","100%");
                        $(".popver .popover-content").attr("height","300px");
                        $(".popver .popover-content").attr("overflow","scroll");
                    }
                }
            )
        });
    },
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
    //加载柱状图
    echartscon:function(params,viewnamearr){
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
                viewnamearr.map(function(key,value){
                    var myChart = ec.init(document.getElementById(key));
                    myChart.setOption({
                        title : {text: app.vtranslate(key)+'\n',x:'center'},
                        tooltip : {trigger: 'axis',axisPointer : {type : 'shadow'}},
                        legend: {data:params.deparementname},
                        toolbox: {show : true,feature : {magicType : {show: true, type: ['line', 'bar']},restore : {show: true}}},
                        calculable : true,
                        dataZoom:{
                            type: 'slider',
                            show: true,
                            start: 0,
                            end: 100
                        },
                        xAxis : [{type : 'category',data : params.edate}],
                        yAxis : [{type : 'value'}],
                        series : params[key]
                    },true);
                    var ecConfig= require('echarts/config');
                    myChart.on(ecConfig.EVENT.CLICK,function (param) {
                        if(param.value==0){return false;}
                        var postData={};
                        postData={
                            'module':app.getModuleName(),
                            'action':'ChangeAjax',
                            'mode':'geteworksituationtrendslist',
                            'seriesIndex':0,
                            'paramname':param.name+'　'+param.seriesName+'　'+this._option.title.text+'　明细',
                            'datauserid':params['deparementid'][param.seriesName],
                            'datetime':param.name,
                            'classtype':key
                        };
                        if(postData.classtype=='nactualvisitors'){return false;}
                        thisInstance.gettables(postData)
                    });
                });
            }
        );
    },
    init_report:function(data){
        var thisInstance=this;
        var arr={};
        arr['edate']=data.result.edate;
        arr['deparementname']=new Array;
        arr['deparementid']=new Array;
        arr['options']=new Array;
        var temparr=['telnumber','telduration','addacounts','transferaccount','highseaaccount','billvisits','numbervisitors','accompanyingvisits','nactualvisitors','signaccount','amountpaid'];
        $.each(temparr,function(key,value){
            arr[value]=[];
        });
        $.each(data.result.newdepartmentid,function(k,val){
            $.each(temparr,function(key,value){
                arr[value].push({
                    name: data.result.newdepartment[val],
                    smooth:true,
                    type: 'line',
                    barMaxWidth:60,
                    data: data.result[value+'_'+val]
                });
            });
            arr['deparementname'].push(data.result.newdepartment[val]);
            arr['deparementid'][data.result.newdepartment[val]]=val;

        });
        arr['deparementname'].unshift('');
        thisInstance.echartscon(arr,temparr);
    },
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
                    if(data.result.flag){
                        $('#listdat'+postData.classtype).show();
                        var div_detail='<table id="tbl_Detail'+postData.classtype+'" class="table listViewEntriesTable" width="100%""><thead><tr>'
                        data.result.thead.map(function(value,key){
                            div_detail+="<th>"+app.vtranslate(value)+"</th>";
                        });
                        div_detail+='</tr></thead><tbody>';

                        $.each(data.result.data,function(i,val){
                            div_detail+='<tr>';
                            data.result.thead.map(function(value,key){
                                div_detail+="<td>"+val[value]+"</td>";
                            });
                            div_detail+='</tr>';
                        });
                        $('#listdata'+postData.classtype).show();
                        $('#detailtable'+postData.classtype).html(div_detail+'</tbody></table>');

                        thisInstance.Tableinstance(postData);

                    }else{
                        $('#detailtable'+postData.classtype).html('没有相关数据');
                    }

                } else {

                }
            },
            function(error,err){

            }
        );
    },
    Tableinstance:function(postData){
        var table = jQuery('#tbl_Detail'+postData.classtype).DataTable({
            language: {"sProcessing":   "处理中...",	"sLengthMenu":   "显示 _MENU_ 项结果","sZeroRecords":  "没有匹配结果","sInfo":         "显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",	"sInfoEmpty":    "显示第 0 至 0 项结果，共 0 项","sInfoFiltered": "(由 _MAX_ 项结果过滤)","sInfoPostFix":  "","sSearch":"当前页快速检索:","sUrl":"","sEmptyTable":     "表中数据为空","sLoadingRecords": "载入中...",
                "sInfoThousands":  ",",	"oPaginate": {"sFirst":"首页","sPrevious": "上页","sNext":"下页","sLast":"末页"},
                "oAria": {"sSortAscending":  ": 以升序排列此列","sSortDescending": ": 以降序排列此列"}},
            scrollY:"300px",
            sScrollX:"disabled",
            aLengthMenu: [ 10, 20, 50, 100, ],
            fnDrawCallback:function(){
                jQuery('#msg'+postData.classtype).html('<span  style="text-align:center;display:block;font-size:16px;color:red;font-weight:bold;">'+postData.paramname+'</span>');
            }
        });
    },
    registerEvents : function(){
        this._super();
        this.loading();
    }

});

function checkNoEmpty(){
    var columns = [
        'telnumber','telduration','total_telnumber',
        // 'actual_income','promotion_income','month_of_day','searchinfo','total_telduration',
    ];
    var len = columns.length;
    for(var i=0;i<len;i++){
        var val = $("#"+columns[i]).val();
        if(val<1){
            var params = {
                text: app.vtranslate(columns[i])+'为必填项，不能为空，必为数字且大于0！',
                type: 'error'
            };
            Vtiger_Helper_Js.showMessage(params);
            return false;
        }
    }

    // var columns2 = ['total_invitenum','total_visitnum','total_strangevisitnum', 'reach_day','wxadd'];
    // var len2 = columns2.length;
    // for(var j=0;j<len2;j++) {
    //     var val = $("#" + columns2[j]).val();
    //     if (val < 0) {
    //         var params = {
    //             text: app.vtranslate(columns2[j]) + '必填项不能为空，必为数字且大于等于0',
    //             type: 'error'
    //         };
    //         Vtiger_Helper_Js.showMessage(params);
    //         return false;
    //     }
    // }
    var tel_connect_rate = $('#tel_connect_rate').val();
    if(tel_connect_rate<=0 ||tel_connect_rate>100){
        var params = {
            text: '接通率须大于0且小于100',
            type: 'error'
        };
        Vtiger_Helper_Js.showMessage(params);
        return false;
    }

    if($("#total_telnumber").val()<$("#telnumber").val()){
        var params = {
            text: '电话量不能大于总电话量',
            type: 'error'
        };
        Vtiger_Helper_Js.showMessage(params);
        return false;
    }

    // if($("#total_telduration").val()<$("#telduration").val()){
    //     var params = {
    //         text: '电话时长不能大于总电话时长',
    //         type: 'error'
    //     };
    //     Vtiger_Helper_Js.showMessage(params);
    //     return false;
    // }
    //
    // if($("#month_of_day").val()<$("#reach_day").val()){
    //     var params = {
    //         text: '工作达标天数不能大于本月总天数',
    //         type: 'error'
    //     };
    //     Vtiger_Helper_Js.showMessage(params);
    //     return false;
    // }

    return true;
}