/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("TelStatistics_Edit_Js",{},{
    registerGetUserData:function(container){
        var thisInstance=this;
        $('.getUserData').click(function(){
            var telnumberdate=$('input[name="telnumberdate"]').val();
            if(telnumberdate==''){
                var params = {
                    text: '请先填写日期',
                    type: 'error'
                };
                Vtiger_Helper_Js.showMessage(params);
                return false;
            }
            var params={};
            params['telnumberdate'] = telnumberdate;
            params['departmentid'] = $('select[name="departmentid"]').val();
            params['action'] = 'ChangeAjax';
            params['module'] = 'TelStatistics';
            params['mode'] = 'getUserdata';
            $('.datatable tbody').empty();
            var progressIndicatorElement = jQuery.progressIndicator({ 'message' : '拼命获取中...','blockInfo':{'enabled':true }});
            AppConnector.request(params).then(
                function(data) {
                    progressIndicatorElement.progressIndicator({ 'mode' : 'hide'});
                    if(data.result.flag){
                        var tablestr='<tr><td>姓名</td><td>部门</td><td>总电话量(个)</td><td>电话量(个)</td><td>接通率(%)</td><td>电话时长(分钟)</td><td>操作</td></tr>';
                        // var tablestr='<tr><td>姓名</td><td>部门</td><td>本月实际进款</td><td>本月保级进款</td><td>本月总天数</td><td>工作达标天数</td><td>微信添加（新增客户/活跃客户）</td><td>累计邀约量（KP/负责人）</td><td>累计拜访量（独立几次，被陪访几次）</td><td>累计陌拜量</td><td>查资料</td><td>总电话量(个)</td><td>电话量(个)</td><td>接通率(%)</td><td>总电话时长（分钟）</td><td>电话时长(分钟)</td><td>操作</td></tr>';
                        var thisdata=data.result.data;
                        $.each(thisdata,function(key,value){
                            // tablestr+='<tr><td class="fieldValue medium">'+value.username+'</td><td class="fieldValue medium">'+value.department+'</td><td><input id="telnumber'+value.id+'" type="text" class="input-large checknumber" value=""></td><td><input id="telduration'+value.id+'" type="text" class="input-large checknumber" value=""></td><td><i class="icon-ok-circle saveUserData" data-userid="'+value.id+'" style="cursor: pointer;"></i></td></tr>';
                            tablestr+='<tr><td class="fieldValue medium">' +value.username+'<input type="hidden" class="userid" id="userid'+value.id+'" name="userid" value="'+value.id+'"></td>';
                            tablestr+='<td class="fieldValue medium">'+value.department+'<input type="hidden" class="departmentid" id="departmentid'+value.id+'" name="departmentid" value="'+value.departmentid+'"></td>';
                            tablestr+='<input id="traget_amount'+value.id+'" type="hidden" class="traget_amount" value="0">';
                            // tablestr+='<input id="traget_amount'+value.id+'" type="hidden" class="traget_amount" value="'+value.traget_amount+'">';
                            // tablestr+='<td><input id="actual_income'+value.id+'" type="text" class="input-mini checknumber actual_income" value="'+value.actual_income+'"></td>';
                            // tablestr+='<td><input id="promotion_income'+value.id+'" type="text" class="input-mini checknumber promotion_income" value="'+value.promotion_income+'"></td>';
                            // tablestr+='<td><input id="month_of_day'+value.id+'" data-value="'+value.id+'" type="text" class="input-mini checknumber month_of_day" value="'+value.month_of_day+'"></td>';
                            // tablestr+='<td><input id="reach_day'+value.id+'" data-value="'+value.id+'" type="text" class="input-mini checknumber reach_day" value="'+value.reach_day+'"></td>';
                            // tablestr+='<td><input id="wxadd'+value.id+'" type="text" class="input-mini checknumber wxadd" value="'+value.wxadd+'"></td>';
                            //
                            // tablestr+='<td><input id="total_invitenum'+value.id+'" type="text" class="input-mini checknumber total_invitenum" value="'+value.total_invitenum+'"></td>';
                            // tablestr+='<td><input id="total_visitnum'+value.id+'" type="text" class="input-mini checknumber total_visitnum" value="'+value.total_visitnum+'"></td>';
                            // tablestr+='<td><input id="total_strangevisitnum'+value.id+'" type="text" class="input-mini checknumber total_strangevisitnum" value="'+value.total_strangevisitnum+'"></td>';
                            //
                            //
                            //
                            // tablestr+='<td><input id="searchinfo'+value.id+'" type="text" class="input-mini checknumber searchinfo" value="'+value.searchinfo+'"></td>';
                            tablestr+='<td><input id="total_telnumber'+value.id+'" type="text" class="input-mini checknumber total_telnumber" value="'+value.total_telnumber+'"></td>';
                            tablestr+='<td><input id="telnumber'+value.id+'" type="text" class="input-mini checknumber telnumber" value="'+value.telnumber+'"></td>';
                            tablestr+='<td><input id="tel_connect_rate'+value.id+'" type="text" class="input-mini checknumber tel_connect_rate" value="'+value.tel_connect_rate+'" disabled></td>';
                            // tablestr+='<td><input id="total_telduration'+value.id+'" type="text" class="input-mini checknumber total_telduration" value="'+value.total_telduration+'"></td>';
                            tablestr+='<td><input id="telduration'+value.id+'" type="text" class="input-mini checknumber telduration" value="'+value.telduration+'"></td>';

                            tablestr+='<td><i class="icon-ok-circle saveUserData" data-userid="'+value.id+'" style="cursor: pointer;"></i></td>';
                            tablestr+='</tr>';

                        });
                        $('.datatable tbody').append(tablestr);
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
        });
        $(container).on('blur change keyup','.checknumber',function(){
            thisInstance.formatNumber($(this));
            var num = $(this).attr('id').replace(/[^0-9]/ig,"");
            if($("#total_telnumber"+num).val()==0){
                $('#tel_connect_rate'+num).val(0.00);
                return;
            }
            var rate = ($("#telnumber"+num).val()/$("#total_telnumber"+num).val())*100;
            $('#tel_connect_rate'+num).val(rate.toFixed(2));
        }).on('paste','.checknumber',function(){
            thisInstance.formatNumber($(this));
        });

        // $(container).on("blur",".month_of_day",function(k,v){
        //     var month_of_day = $(this).val();
        //     var id = $(this).data('value');
        //     var reach_day = $("#reach_day"+id).val();
        //     console.log(month_of_day);
        //     console.log(reach_day);
        //     if(month_of_day && reach_day && (month_of_day<reach_day)){
        //         $(this).val('');
        //         var params = {
        //             text: '本月总天数需大于工作达标天数',
        //             type: 'error'
        //         };
        //         Vtiger_Helper_Js.showMessage(params);
        //     }
        // });
        // $(container).on("blur",".reach_day",function(k,v){
        //     var reach_day = $(this).val();
        //     var id = $(this).data('value');
        //     var month_of_day = $("#month_of_day"+id).val();
        //     console.log(month_of_day);
        //     console.log(reach_day);
        //     if(month_of_day && reach_day && (month_of_day<reach_day)){
        //         $(this).val('');
        //         var params = {
        //             text: '工作达标天数需小于本月总天数',
        //             type: 'error'
        //         };
        //         Vtiger_Helper_Js.showMessage(params);
        //     }
        // });
    },
    formatNumber:function(_this){
        _this.val(_this.val().replace(/,/g,''));//只能输入数字小数点
        _this.val(_this.val().replace(/[^0-9]/g,''));//只能输入数字小数点
        _this.val(_this.val().replace(/^0\d{1,}/g,''));//不能以0打头的整数
        _this.val(_this.val().replace(/[\d]{13,}/g,''));//超出长度全部清空
    },
    registersavaUserData:function(container){
        $(container).on('click','.saveUserData',function(){
            var $savethis=this;
            var telnumberdate=$('input[name="telnumberdate"]').val();
            if(telnumberdate==''){
                var params = {
                    text: '请先填写日期',
                    type: 'error'
                };
                Vtiger_Helper_Js.showMessage(params);
                return false;
            }
            var userid=$(this).data('userid');
            if(userid<1){
                var params = {
                    text: '商务选择有误！',
                    type: 'error'
                };
                Vtiger_Helper_Js.showMessage(params);
                return false;
            }
            var telnumber=$('#telnumber'+userid).val();
            var telduration=$('#telduration'+userid).val();
            // var total_telduration=$('#total_telduration'+userid).val();
            // var total_invitenum=$('#total_invitenum'+userid).val();
            // var total_visitnum=$('#total_visitnum'+userid).val();
            // var total_strangevisitnum=$('#total_strangevisitnum'+userid).val();
            var total_telnumber = $('#total_telnumber'+userid).val();
            // var traget_amount=$('#traget_amount'+userid).val();
            // var actual_income=$('#actual_income'+userid).val();
            // var promotion_income=$('#promotion_income'+userid).val();
            // var month_of_day=$('#month_of_day'+userid).val();
            // var reach_day=$('#reach_day'+userid).val();
            // var wxadd=$('#wxadd'+userid).val();
            // var searchinfo=$('#searchinfo'+userid).val();
            var departmentid = $("select[name='departmentid']").val();

            console.log(total_telnumber);
            var flag = checkNoEmpty(userid);
            if(!flag){
                return false;
            }
            console.log(11111);

            var tel_connect_rate = $('#tel_connect_rate'+userid).val();
            // if(telnumber<1){
            //     $('#telnumber'+userid).popover({'title':'<font style="color:red;">必填项</font>',show: 500, hide: 100,'content':'必填项不能为空，必为数字且大于0'});
            //     $('#telnumber'+userid).popover('show');
            //     return;
            // }
            // if(telduration<1){
            //     $('#telduration'+userid).popover({'title':'<font style="color:red;">必填项</font>',show: 500, hide: 100,'content':'必填项不能为空，必为数字且大于0'});
            //     $('#telduration'+userid).popover('show');
            //     return;
            // }
            // if(total_telduration<1){
            //     $('#total_telduration'+userid).popover({'title':'<font style="color:red;">必填项</font>',show: 500, hide: 100,'content':'必填项不能为空，必为数字且大于0'});
            //     $('#total_telduration'+userid).popover('show');
            //     return;
            // }
            // if(total_invitenum<0){
            //     $('#total_invitenum'+userid).popover({'title':'<font style="color:red;">必填项</font>',show: 500, hide: 100,'content':'必填项不能为空，必为数字且大于0'});
            //     $('#total_invitenum'+userid).popover('show');
            //     return;
            // }
            // if(total_visitnum<0){
            //     $('#total_visitnum'+userid).popover({'title':'<font style="color:red;">必填项</font>',show: 500, hide: 100,'content':'必填项不能为空，必为数字且大于0'});
            //     $('#total_visitnum'+userid).popover('show');
            //     return;
            // }
            // if(total_strangevisitnum<0){
            //     $('#total_strangevisitnum'+userid).popover({'title':'<font style="color:red;">必填项</font>',show: 500, hide: 100,'content':'必填项不能为空，必为数字且大于0'});
            //     $('#total_strangevisitnum'+userid).popover('show');
            //     return;
            // }
            //
            // if(total_telnumber<1){
            //     $('#total_telnumber'+userid).popover({'title':'<font style="color:red;">必填项</font>',show: 500, hide: 100,'content':'必填项不能为空，必为数字且大于0'});
            //     $('#total_telnumber'+userid).popover('show');
            //     return;
            // }
            //
            // if(tel_connect_rate<=0 ||tel_connect_rate>100){
            //     $('#tel_connect_rate'+userid).popover({'title':'<font style="color:red;">提醒</font>',show: 500, hide: 100,'content':'接通率须大于0且小于100'});
            //     $('#tel_connect_rate'+userid).popover('show');
            //     return;
            // }
            //
            // // if(traget_amount<1){
            // //     $('#traget_amount'+userid).popover({'title':'<font style="color:red;">必填项</font>',show: 500, hide: 100,'content':'必填项不能为空，必为数字且大于0'});
            // //     $('#traget_amount'+userid).popover('show');
            // //     return;
            // // }
            // if(actual_income<1){
            //     $('#actual_income'+userid).popover({'title':'<font style="color:red;">必填项</font>',show: 500, hide: 100,'content':'必填项不能为空，必为数字且大于0'});
            //     $('#actual_income'+userid).popover('show');
            //     return;
            // }
            // if(promotion_income<1){
            //     $('#promotion_income'+userid).popover({'title':'<font style="color:red;">必填项</font>',show: 500, hide: 100,'content':'必填项不能为空，必为数字且大于0'});
            //     $('#promotion_income'+userid).popover('show');
            //     return;
            // }
            // if(month_of_day<1){
            //     $('#month_of_day'+userid).popover({'title':'<font style="color:red;">必填项</font>',show: 500, hide: 100,'content':'必填项不能为空，必为数字且大于0'});
            //     $('#month_of_day'+userid).popover('show');
            //     return;
            // }
            // if(reach_day<0){
            //     $('#reach_day'+userid).popover({'title':'<font style="color:red;">必填项</font>',show: 500, hide: 100,'content':'必填项不能为空，必为数字且大于0'});
            //     $('#reach_day'+userid).popover('show');
            //     return;
            // }
            // if(wxadd<0){
            //     $('#wxadd'+userid).popover({'title':'<font style="color:red;">必填项</font>',show: 500, hide: 100,'content':'必填项不能为空，必为数字且大于0'});
            //     $('#wxadd'+userid).popover('show');
            //     return;
            // }
            // if(searchinfo<1){
            //     $('#searchinfo'+userid).popover({'title':'<font style="color:red;">必填项</font>',show: 500, hide: 100,'content':'必填项不能为空，必为数字且大于0'});
            //     $('#searchinfo'+userid).popover('show');
            //     return;
            // }


            var params={};
            params['telnumberdate'] = telnumberdate;
            params['action'] = 'ChangeAjax';
            params['module'] = 'TelStatistics';
            params['mode'] = 'saveUserData';
            params['telnumber'] = telnumber;
            params['total_telnumber'] = total_telnumber;
            params['tel_connect_rate'] = tel_connect_rate;
            params['departmentid'] = departmentid;
            params['telduration'] = telduration;
            // params['total_telduration'] = total_telduration;
            // params['total_invitenum'] = total_invitenum;
            // params['total_visitnum'] = total_visitnum;
            // params['total_strangevisitnum'] = total_strangevisitnum;
            // params['traget_amount'] = traget_amount;
            // params['actual_income'] = actual_income;
            // params['promotion_income'] = promotion_income;
            // params['month_of_day'] = month_of_day;
            // params['reach_day'] = reach_day;
            // params['wxadd'] = wxadd;
            // params['searchinfo'] = searchinfo;
            params['userid'] = userid;
            AppConnector.request(params).then(
                function(data) {
                    if(data.result.flag){
                        // $($savethis).closest('tr').remove();
                        var params = {
                            text: '保存成功',
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
        });

    },
    batchSave:function(container){
        $(container).on('click','.batchSave',function(){
            var flag = false;
            $(".checknumber").each(function (k, v) {
                if (!$(v).val()) {
                    flag =true;
                    return true;
                }
            });
            if(flag){
                var params = {
                    text: '请填写完整后再提交',
                    type: 'error'
                };
                Vtiger_Helper_Js.showMessage(params);
                return;
            }


            telnumberdate = $("input[name='telnumberdate']").val();
            var telnumber = [];
            var total_telnumber = [];
            // var total_telduration = [];
            // var total_invitenum = [];
            // var total_visitnum = [];
            // var total_strangevisitnum = [];
            var departmentid = [];
            var telduration = [];
            // var traget_amount = [];
            // var actual_income = [];
            // var promotion_income = [];
            // var month_of_day = [];
            // var reach_day = [];
            // var wxadd = [];
            // var searchinfo = [];
            var userid = [];
            var tel_connect_rate = [];
            $(".telnumber").each(function (k, v) {
                telnumber[k] = $(v).val();
            });
            $(".total_telnumber").each(function (k, v) {
                total_telnumber[k] = $(v).val();
            });
            $(".departmentid").each(function (k, v) {
                departmentid[k] = $(v).val();
            });
            $(".telduration").each(function (k, v) {
                telduration[k] = $(v).val();
            });
            // $(".traget_amount").each(function (k, v) {
            //     traget_amount[k] = $(v).val();
            // });
            // $(".actual_income").each(function (k, v) {
            //     actual_income[k] = $(v).val();
            // });
            // $(".promotion_income").each(function (k, v) {
            //     promotion_income[k] = $(v).val();
            // });
            // $(".month_of_day").each(function (k, v) {
            //     month_of_day[k] = $(v).val();
            // });
            // $(".reach_day").each(function (k, v) {
            //     reach_day[k] = $(v).val();
            // });
            // $(".wxadd").each(function (k, v) {
            //     wxadd[k] = $(v).val();
            // });
            // $(".searchinfo").each(function (k, v) {
            //     searchinfo[k] = $(v).val();
            // });
            $(".userid").each(function (k, v) {
                userid[k] = $(v).val();
            });
            $(".tel_connect_rate").each(function (k, v) {
                tel_connect_rate[k] = $(v).val();
            });

            // $(".total_telduration").each(function (k, v) {
            //     total_telduration[k] = $(v).val();
            // });
            // $(".total_invitenum").each(function (k, v) {
            //     total_invitenum[k] = $(v).val();
            // });
            // $(".total_visitnum").each(function (k, v) {
            //     total_visitnum[k] = $(v).val();
            // });
            // $(".total_strangevisitnum").each(function (k, v) {
            //     total_strangevisitnum[k] = $(v).val();
            // });

            var params={};
            params['telnumberdate'] = telnumberdate;
            params['action'] = 'ChangeAjax';
            params['module'] = 'TelStatistics';
            params['mode'] = 'batchSave';
            params['telnumber'] = telnumber;
            params['total_telnumber'] = total_telnumber;
            // params['total_telduration'] = total_telduration;
            // params['total_invitenum'] = total_invitenum;
            // params['total_visitnum'] = total_visitnum;
            // params['total_strangevisitnum'] = total_strangevisitnum;
            params['tel_connect_rate'] = tel_connect_rate;
            params['departmentids'] = departmentid;
            params['telduration'] = telduration;
            // params['traget_amount'] = traget_amount;
            // params['actual_income'] = actual_income;
            // params['promotion_income'] = promotion_income;
            // params['month_of_day'] = month_of_day;
            // params['reach_day'] = reach_day;
            // params['wxadd'] = wxadd;
            // params['searchinfo'] = searchinfo;
            params['userid'] = userid;
            params['departmentid'] = $("select[name='departmentid']").val();
            console.log(params);
            AppConnector.request(params).then(
                function(data) {
                    if(data.result.flag){
                        var params = {
                            text: '保存成功',
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

        });
    },
    registerBasicEvents:function(container) {
        this._super(container);
        this.registerGetUserData(container);
        this.registersavaUserData(container);
        this.batchSave(container);
        $('.getUserData').trigger("click");
    }
});

function checkNoEmpty(userid){
    var columns = [
        'telnumber','telduration','total_telnumber',
        // 'actual_income','promotion_income','month_of_day','searchinfo','total_telduration'
    ];
    var len = columns.length;
    for(var i=0;i<len;i++){
        var val = $("#"+columns[i]+userid).val();
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
    //     var val = $("#" + columns2[j] + userid).val();
    //     if (val < 0) {
    //         var params = {
    //             text: app.vtranslate(columns2[j]) + '必填项不能为空，必为数字且大于等于0',
    //             type: 'error'
    //         };
    //         Vtiger_Helper_Js.showMessage(params);
    //         return false;
    //     }
    // }
    var tel_connect_rate = $('#tel_connect_rate'+userid).val();
    if(tel_connect_rate<=0 ||tel_connect_rate>100){
        var params = {
            text: '接通率须大于0且小于100',
            type: 'error'
        };
        Vtiger_Helper_Js.showMessage(params);
        return false;
    }

    // if($("#total_telnumber"+userid).val()<$("#telnumber"+userid).val()){
    //     var params = {
    //         text: '电话量不能大于总电话量',
    //         type: 'error'
    //     };
    //     Vtiger_Helper_Js.showMessage(params);
    //     return false;
    // }
    //
    // if($("#total_telduration"+userid).val()<$("#telduration"+userid).val()){
    //     var params = {
    //         text: '电话时长不能大于总电话时长',
    //         type: 'error'
    //     };
    //     Vtiger_Helper_Js.showMessage(params);
    //     return false;
    // }
    //
    // if($("#month_of_day"+userid).val()<$("#reach_day"+userid).val()){
    //     var params = {
    //         text: '工作达标天数不能大于本月总天数',
    //         type: 'error'
    //     };
    //     Vtiger_Helper_Js.showMessage(params);
    //     return false;
    // }

    return true;
}

















