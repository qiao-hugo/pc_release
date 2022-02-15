/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("ServiceContracts_Detail_Js", {}, {

    /**
     * Function to get listprice edit form
     */
    getconfirmrecord: function (requestUrl) {
        var thisInstance = this;
        $('#ServiceContracts_detailView_basicAction_LBL_CONFIRM').on('click', function () {
            var message = '请先确定是否已经审查了该合同？';
            var msg = {
                'message': message
            };

            Vtiger_Helper_Js.showConfirmationBox(msg).then(function (e) {
                var params = {
                    'module': 'ServiceContracts', //ServiceContracts
                    'action': 'ChangeAjax',
                    'mode': 'serviceconfirm',
                    "recordid": $('#recordId').val()
                };
                AppConnector.request(params).then(
                        function (data) {
                            window.location.reload();
                        },
                        function () {
                        }
                );

            }, function (error, err) {});
        });
        $('#ServiceContracts_detailView_basicAction_LBL_NOSTDAPPLY').on('click', function () {
            var message = '您确定要提交该合同吗？';
            var msg = {
                'message': message
            };

            Vtiger_Helper_Js.showConfirmationBox(msg).then(function (e) {
                var params = {
                    'module': 'ServiceContracts', //ServiceContracts
                    'action': 'ChangeAjax',
                    'mode': 'makeWorkflowStages',
                    "recordid": $('#recordId').val()
                };
                AppConnector.request(params).then(
                        function (data) {
                            window.location.reload();
                        },
                        function () {
                        }
                );

            }, function (error, err) {});
        });
        $('#ServiceContracts_detailView_basicAction_LBL_TOBACKSTATUS').on('click', function () {
            var message = '您确定要改该合同状态吗吗？';
            var msg = {
                'message': message
            };

            Vtiger_Helper_Js.showConfirmationBox(msg).then(function (e) {
                var params = {
                    'module': 'ServiceContracts', //ServiceContracts
                    'action': 'ChangeAjax',
                    'mode': 'toBackStatus',
                    "recordid": $('#recordId').val()
                };
                AppConnector.request(params).then(
                        function (data) {
                            if(data.result.flag){
                                window.location.reload();
                            }else{
                                Vtiger_Helper_Js.showMessage({type: 'error', text:data.result.msg});
                            }
                        },
                        function () {
                        }
                );

            }, function (error, err) {});
        });
        $('#ServiceContracts_detailView_basicAction_LBL_CONFIRMDELIVERY').on('click',function(){
            var message = '确认合同内产品完全交付吗？';
            var msg = {
                'message': message
            };

            Vtiger_Helper_Js.showConfirmationBox(msg).then(function (e) {
                var params = {
                    'module': 'ServiceContracts', //ServiceContracts
                    'action': 'ChangeAjax',
                    'mode': 'confirmDelivery',
                    "recordid": $('#recordId').val()
                };
                var progressIndicatorElement = jQuery.progressIndicator({
                    'message': '正在努力处理...',
                    'position': 'html',
                    'blockInfo': {'enabled': true}
                });
                AppConnector.request(params).then(
                    function (data) {
                        progressIndicatorElement.progressIndicator({'mode': 'hide'});
                        if(data.success){
                            if(data.result.flag){
                                Vtiger_Helper_Js.showMessage({type: 'success', text:data.result.msg});
                                $('#ServiceContracts_detailView_basicAction_LBL_CONFIRMDELIVERY').remove();
                            }else{
                                Vtiger_Helper_Js.showMessage({type: 'error', text:data.result.msg});
                            }
                        }

                    },
                    function () {
                    }
                );

            }, function (error, err) {});
        });
        $('#ServiceContracts_detailView_basicAction_LBL_CONFIRMDELIVERYBACK').on('click',function(){
            var message = '确认撤销合同产品交付？';
            var msg = {
                'message': message
            };

            Vtiger_Helper_Js.showConfirmationBox(msg).then(function (e) {
                var params = {
                    'module': 'ServiceContracts', //ServiceContracts
                    'action': 'ChangeAjax',
                    'mode': 'confirmDelivery',
                    "recordid": $('#recordId').val()
                };
                var progressIndicatorElement = jQuery.progressIndicator({
                    'message': '正在努力处理...',
                    'position': 'html',
                    'blockInfo': {'enabled': true}
                });
                AppConnector.request(params).then(
                    function (data) {
                        progressIndicatorElement.progressIndicator({'mode': 'hide'});
                        if(data.success){
                            if(data.result.flag){
                                Vtiger_Helper_Js.showMessage({type: 'success', text:data.result.msg});
                                $('#ServiceContracts_detailView_basicAction_LBL_CONFIRMDELIVERYBACK').remove();
                            }else{
                                Vtiger_Helper_Js.showMessage({type: 'error', text:data.result.msg});
                            }
                        }

                    },
                        function () {
                        }
                );

            }, function (error, err) {});
        });
        $('#ServiceContracts_detailView_basicAction_LBL_UPDATERECEIVED').on('click', function () {
            var params = {
                'module': 'ServiceContracts', //ServiceContracts
                'action': 'ChangeAjax',
                'mode': 'getuserlist',
                "recordid": $('#recordId').val()
            };
            var progressIndicatorElement = jQuery.progressIndicator({
                'message': '正在努力处理...',
                'position': 'html',
                'blockInfo': {'enabled': true}
            });
            AppConnector.request(params).then(
                    function (data) {
                        progressIndicatorElement.progressIndicator({'mode': 'hide'});
                        if (data.success) {
                            var str = '';
                            $.each(data.result, function (key, value) {
                                str += '<option value="' + value.id + '">' + value.username + '</option>';
                            })
                            str = '<div style="margin:10px; 20px;text-align:center;height:250px;"><select id="reportid" class="chzn-select">' + str + '</select></div>';

                            var message = '确定要更换提单人吗？';
                            var msg = {
                                'message': message,
                                'width': '400px'
                            };
                            Vtiger_Helper_Js.showConfirmationBox(msg).then(function (e) {
                                var userid = $('#reportid').val();
                                console.log(userid)
                                var params = {
                                    'module': 'ServiceContracts', //ServiceContracts
                                    'action': 'ChangeAjax',
                                    'mode': 'changereceived',
                                    'userid': userid,
                                    "recordid": $('#recordId').val()
                                };
                                AppConnector.request(params).then(
                                    function (data) {
                                        console.log(data);
                                        if(data.success){
                                            window.location.reload();
                                        }else{
                                            var params = {
                                                title : '',
                                                text : data.error.message
                                            };
                                            Vtiger_Helper_Js.showPnotify(params);
                                        }
                                    },
                                    function () {
                                    }
                                );

                            }, function (error, err) {});

                            $('.modal-content .modal-body').append(str);
                            $(".chzn-select").chosen();
                        }else {
                            Vtiger_Helper_Js.showMessage({type: 'error', text:data.error.message});
                        }
                    },
                    function () {
                    }
            );
        });
        $('#ServiceContracts_detailView_basicAction_LBL_CONTRACTCANCEL').on('click', function () {
                var params = {
                    'module': 'ServiceContracts', //ServiceContracts
                    'action': 'ChangeAjax',
                    'mode': 'checkcancel',
                    "recordid": $('#recordId').val()
                };
                var progressIndicatorElement = jQuery.progressIndicator({
                    'message': '正在努力处理...',
                    'position': 'html',
                    'blockInfo': {'enabled': true}
                });
                AppConnector.request(params).then(
                    function (data) {
                        progressIndicatorElement.progressIndicator({'mode': 'hide'});
                        if(data.success){
                            var message = '<h3>请填写如下信息!</h3><hr>';
                            var msg = {
                                'message': message,
                                'width': '800px'
                            };
                            var strdata = thisInstance.getContractsAgreement();

            thisInstance.showConfirmationBox(msg).then(function (e) {
                var pagenumber = $('#pagenumber').val();
                var remark = $('#remark').val();
                var reasoncan = $('#reasoncancellation').val();
                var params = {
                    'module': 'ServiceContracts',
                    'action': 'ChangeAjax',
                    'mode': 'ContractCancel',
                    'reasoncan': reasoncan,
                    'pagenumber': pagenumber,
                    'remark': remark,
                    "recordid": $('#recordId').val()
                };
                AppConnector.request(params).then(
                        function (data) {
                            window.location.reload();
                        },
                        function () {
                            window.location.reload();
                        }
                );


            }, function (error, err) {});
            var str = '';

            var strr = '<form name="insertcomment" id="formcomment">\
                            <div id="insertcomment" style="height: 300px;overflow: auto">\
                            <table class="table table-bordered blockContainer Duplicates showInlineTable  detailview-table" data-num="1" id="comments1"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> ' +
                    '<span class="redColor">*</span> 作废页数(一式三联算一页)</label></td>' +
                    '<td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="number" id="pagenumber" name="pagenumber" min="1" value="1"></span></div></td>' +
                    '<td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 应缴款金额:</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="text" id="je" name="je" value="300" disabled></span></div></td></tr>' +
                    '<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>作废原因:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><select name="reasoncancellation" id="reasoncancellation"><option value="losevoid">遗失作废</option><option value="normallyvoid">正常作废</option><option value="othervoid">其他</option></select></span></div></td></tr>' +
                    '<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>作废原因描述:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><textarea name="remark" id="remark" class="span11"></textarea></span></div></td></tr>' +
                    '<tr><td colspan="4"><label class="muted pull-right marginRight10px"><span class="redColor">主合同申请作废请连同附加协议一并作废。作废页数请和主合同及附加协议一并作废，否则拒绝作废。</span></label></td></tr>' +
                    '<tr><td colspan="4"><label class="muted pull-right marginRight10px"><span style="font-size: 12px;color: #0d0d0d">请在2个工作日内持<span style="color: red">合同原件/合同遗失证明原件</span>等材料到财务部完成作废审核流程，否则系统将会被锁死。</span></label></td></tr>' +
                    '</tbody></table>' + strdata +
                    '</div></form>';

                            $('.modal-content .modal-body').append(strr);
                        }else{
                            Vtiger_Helper_Js.showMessage({type: 'error', text:data.error.message});
                        }
                    }
                );

        });
        $('body').on('change', '#pagenumber,#reasoncancellation', function () {
            var reasoncan = $('#reasoncancellation').val();
            var pagenumber = $('#pagenumber').val();
            if (reasoncan == 'losevoid')
            {
                $('#je').val(300);
            } else if (reasoncan == 'normallyvoid')
            {
                $('#je').val(pagenumber);
            } else
            {
                $('#je').val(0);
            }

        });
        $('#ServiceContracts_detailView_basicAction_LBL_CONTRACTCANCELING').on('click', function () {

            var params = {};
            params.data = {
                "module": "ServiceContracts",
                "action": "ChangeAjax",
                "mode": "getCancelInfo",
                "recordid": $('#recordId').val()
            };
            params.async = false;
            var strdata = thisInstance.getContractsAgreement();
            var strr = ''
            AppConnector.request(params).then(
                    function (data) {
                        if (data.success) {
                            var losevoid = data.result.cancelvoid != 'normallyvoid' ? ' selected' : '';
                            var normallyvoid = data.result.cancelvoid == 'normallyvoid' ? ' selected' : '';
                            strr = '<form name="insertcomment" id="formcomment">\
                            <div id="insertcomment" style="height: 300px;overflow: auto">\
                            <table class="table table-bordered blockContainer Duplicates showInlineTable  detailview-table" data-num="1" id="comments1"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> ' +
                                    '<span class="redColor">*</span> 作废页数(一式三联算一页)</label></td>' +
                                    '<td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="number" id="pagenumber" name="pagenumber" min="1" value="' + data.result.pagenumber + '"></span></div></td>' +
                                    '<td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 应缴款金额:</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="text" id="je" name="je" value="' + data.result.cancelmoney + '" disabled></span></div></td></tr>' +
                                    '<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>作废原因:</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><select name="reasoncancellation" id="reasoncancellation"><option value="losevoid"' + losevoid + '>遗失作废</option><option value="normallyvoid"' + normallyvoid + '>正常作废</option></select></span></div></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">申请人:</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10">' + data.result.cancelname + '</span></div></td></tr>' +
                                    '<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>作废原因描述:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><textarea name="remark" id="remark" class="span11">' + data.result.cancelremark + '</textarea></span></div></td></tr>' +
                                    '<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>已收款金额:</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="number" id="souje" name="souje" value="' + data.result.accountsdue + '" min="1"></span></div></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>收据编号:</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="text" id="soujbanhao" name="soujbanhao" value="' + data.result.receiptnumber + '"></span></div></td></tr>' +
                                    '<tr><td colspan="4"><label class="muted pull-right marginRight10px"><span class="redColor">主合同申请作废请连同附加协议一并作废。作废页数请和主合同及附加协议一并作废，否则拒绝作废。</span></label></td></tr>' +
                                    '<tr><td colspan="4"><label class="muted pull-right marginRight10px"><span class="redColor">请出纳确认该作废合同一式三联，所有联次齐全，并确认作废页数无误。</span></label></td></tr>' +
                                    '</tbody></table>' + strdata + '</div></form>';
                        }
                    },
                    function () {
                    }
            );
            var message = '<h3>请填写如下信息!</h3><hr>';
            var msg = {
                'message': message,
                'width': '800px'
            };

            thisInstance.showConfirmationBox(msg).then(function (e) {
                var pagenumber = $('#pagenumber').val();
                var remark = $('#remark').val();
                var reasoncan = $('#reasoncancellation').val();
                var souje = $('#souje').val();
                var soujbanhao = $('#soujbanhao').val();
                var params = {
                    'module': 'ServiceContracts',
                    'action': 'ChangeAjax',
                    'mode': 'chuNaDoContractCancel',
                    'reasoncan': reasoncan,
                    'souje': souje,
                    'soujbanhao': soujbanhao,
                    'reasoncan': reasoncan,
                    'pagenumber': pagenumber,
                    'remark': remark,
                    "recordid": $('#recordId').val()
                };
                AppConnector.request(params).then(
                        function (data) {
                            window.location.reload();
                        },
                        function () {
                            window.location.reload();
                        }
                );


            }, function (error, err) {});
            var str = '';



            $('.modal-content .modal-body').append(strr);

        });
        $('#ServiceContracts_detailView_basicAction_LBL_RECEIPTOR').on('click', function () {
            var userdata = thisInstance.getUserData();
            var message = '<h4>请选择代领人？</h4><hr>';
            var msg = {
                'message': message,
                'width': '400px'
            };
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function (e) {
                var userid = $('#reportid').val();

                var params = {
                    'module': 'ServiceContracts', //ServiceContracts
                    'action': 'ChangeAjax',
                    'mode': 'assignreceiptor',
                    'userid': userid,
                    "recordid": $('#recordId').val()
                };
                AppConnector.request(params).then(
                        function (data) {
                            window.location.reload();
                        },
                        function () {
                        }
                );

            }, function (error, err) {});
            $('.modal-content .modal-body').append(userdata);
            $(".chzn-select").chosen();

        });
        /*$('#ServiceContracts_detailView_basicAction_LBL_SALESORDER').on('click', function(){
         var message='<h3>T云工单工作流</h3><hr>';
         var msg={
         'message':message
         };
         var str='<div id="insertcomment" style="height: 300px;overflow: auto">\
         <table class="table table-bordered blockContainer Duplicates showInlineTable  detailview-table" data-num="1" id="comments1"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> ' +
         '<span class="redColor">*</span> 用户名</label></td>' +
         '<td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="text" id="usercode" name="usercode" value=""></span> </div></td></tr>'+
         '</tbody></table></div>'

         Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
         //thisInstance.showSalesOrderStatus();
         },function(error, err) {
         });
         $('.modal-content .modal-body').append(str);
         var widths=$('.modal-content').width();
         $('<input type="button" value="查找" style="float:left;margin-left:'+widths/2+'px;">').bind('click', function(e){
         var usercode=$('#usercode').val();
         if(usercode==''){
         $('#usercode').focus();
         $('#usercode').attr('data-content','<font color="red">必填项不能为空!</font>');;
         $('#usercode').popover("show");
         $('.popover').css('z-index',1000010);
         $('.popover-content').css({"color":"red","fontSize":"12px"});
         setTimeout("$('#usercode').popover('destroy')",2000);
         return false;//跳出each
         }
         thisInstance.showSalesOrderStatus();
         $(this).remove();
         }).appendTo('.modal-content .modal-footer');
         });

         },*/
        $('#ServiceContracts_detailView_basicAction_LBL_SALESORDER').on('click', function () {
            var message = '<h3>T云工单工作流</h3><hr>';
            var msg = {
                'message': message
            };
            var params = {};
            params.data = {
                "module": "ServiceContracts",
                "action": "ChangeAjax",
                "mode": "getSalesOrderStatus",
                "recordid": $('#recordId').val(),
            };
            params.async = false;
            var str = '';

            AppConnector.request(params).then(
                    function (data) {
                        if (data.success)
                        {
                            str = '<div style="padding:5px;"><ul class="nav nav-pills">';
                            $.each(data.message, function (key, value) {
                                str += '<li style="float: none;vertical-align: middle;display: inline-block;">';
                                $.each(value.ServerItem, function (ckey, cvalue) {
                                    var isaction = cvalue.IsComplete ? 'label-success' : 'label-inverse';
                                    str += '<span class="label ' + isaction + '" title="' + cvalue.Name + '">' + value.ServerName + '\\' + cvalue.Name + '</span><br>';

                                });
                                str += '</li>';
                                if (key != data.message.length - 1) {
                                    str += '<li style="float: none;vertical-align: middle;display: inline-block;"><i class="icon-arrow-right" style=""></i></li>';
                                }
                            });
                            str += '</ul></div><div><hr>图例说明：<span class="label  label-success" title="">已经完成的节点</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\
                                           <span class="label label-inverse" title="">即将完成的节点</span></div>';
                        } else
                        {
                            str = data.message;
                        }
                    },
                    function () {
                    }

            );
            $('.modal-content .modal-body').append(str);

            Vtiger_Helper_Js.showConfirmationBox(msg).then(function (e) {
                //thisInstance.showSalesOrderStatus();
            }, function (error, err) {
            });
            $('.modal-content .modal-body').append(str);
        });
        //确认到款
        $("#ServiceContracts_detailView_basicAction_LBL_CONFIRMPAYMENT").on("click",function () {
            var params = {
                'module': 'ServiceContracts',
                'action': 'ChangeAjax',
                'mode': 'userMobile',
                "recordid": $('#recordId').val()
            };
            AppConnector.request(params).then(
                function (data) {
                    console.log(data);
                    if(data.success){
                       var mobile = data.result.mobile;
                        var message = '<br><span style="font-size: 16px;">确认到款后，系统会自动发送账号密码给客户，客户可以操作账号激活<br><br>接收人手机号：<b style="border-bottom: 1px solid black">'+mobile+'</b><br></span>';
                        var msg = {
                            'message': message,
                            'width': '400px'
                        };
                        console.log( $('#recordId').val());
                        Vtiger_Helper_Js.showConfirmationBox(msg).then(function (e) {
                            var params = {
                                'module': 'ServiceContracts',
                                'action': 'ChangeAjax',
                                'mode': 'confirmPayment',
                                'mobile': mobile,
                                "recordid": $('#recordId').val()
                            };
                            var progressIndicatorElement = jQuery.progressIndicator({
                                'message': '正在努力处理...',
                                'position': 'html',
                                'blockInfo': {'enabled': true}
                            });
                            AppConnector.request(params).then(
                                function (data) {
                                    progressIndicatorElement.progressIndicator({'mode': 'hide'});
                                    if(data.success){
                                        Vtiger_Helper_Js.showMessage({type: 'success', text: '确认到款成功,短信已发送'});
                                        window.location.reload();
                                    }else{
                                        Vtiger_Helper_Js.showMessage({type: 'error', text: data.error.message});
                                    }
                                },
                                function () {
                                    window.location.reload();
                                }
                            );
                        });
                    }else{
                        Vtiger_Helper_Js.showMessage({type: 'error', text: data.error.message});
                    }
                },
                function () {
                    window.location.reload();
                }
            );
        });

        $("#ServiceContracts_detailView_basicAction_LBL_LEASTPAYMOENY").on("click",function () {
            var params = {
                'module': 'ServiceContracts',
                'action': 'ChangeAjax',
                'mode': 'leastPayMoney',
                "recordid": $('#recordId').val()
            };
            // var progressIndicatorElement = jQuery.progressIndicator({
            //     'message': '正在努力处理...',
            //     'position': 'html',
            //     'blockInfo': {'enabled': true}
            // });
            AppConnector.request(params).then(
                function (data) {
                    // progressIndicatorElement.progressIndicator({'mode': 'hide'});
                    // console.log(data);
                    if(data.success){
                        alert("该合同最少支付金额为 "+data.data+' 元');
                        return;
                    }else{
                        Vtiger_Helper_Js.showMessage({type: 'error', text: data.msg});
                    }
                },
                function () {
                }
            );
        });
        //确认到款
        $("#ServiceContracts_detailView_basicAction_LBL_MANUALCONFIRMPAYMENT").on("click",function () {
            var params = {
                'module': 'ServiceContracts',
                'action': 'ChangeAjax',
                'mode': 'manualConfirmPayment',
                "recordid": $('#recordId').val()
            };
            var progressIndicatorElement = jQuery.progressIndicator({
                'message': '正在努力处理...',
                'position': 'html',
                'blockInfo': {'enabled': true}
            });
            AppConnector.request(params).then(
                function (data) {
                    progressIndicatorElement.progressIndicator({'mode': 'hide'});
                    if(data.success){
                        Vtiger_Helper_Js.showMessage({type: 'success', text: '确认到款成功,短信已发送'});
                        window.location.reload();
                    }else{
                        Vtiger_Helper_Js.showMessage({type: 'error', text: data.error.message});
                    }
                },
                function () {
                    window.location.reload();
                }
            );
        });

        //确认到款
        $("#ServiceContracts_detailView_basicAction_LBL_CHANGESTAGE").on("click",function () {
            var oldstage = $("input[name='oldstage']").val();
            var modulestatus = $("input[name='modulestatus']").val();
            var isfenfile = $("input[name='isfenfile']").val();

            oldstagevalue='全款';
            newstagevalue='分期';
            newstage=1;
            var str = '';
            // var str = ' (请注意:回款方式变为分期时，需提供分期协议，否则将影响业绩提成的发放)';
            if(oldstage=='1'){
                oldstagevalue='分期';
                newstagevalue='全款';
                newstage=0;
                str ='';
            }
            if(oldstage!='1' && isfenfile<1){
                alert("请先上传分期协议后，再进行切换");
                return;
            }
            if(modulestatus!='c_complete'){
                str ='';
            }
            var message = '目前合同付款方式为'+oldstagevalue+str+',确定要更改为'+newstagevalue+'?';
            var msg = {
                'message': message,
                'width': '800px'
            };
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function (e) {
                var params = {
                    'module': 'ServiceContracts',
                    'action': 'ChangeAjax',
                    'mode': 'changeStage',
                    "recordid": $('#recordId').val(),
                    "stage":newstage
                };
                var progressIndicatorElement = jQuery.progressIndicator({
                    'message': '正在努力处理...',
                    'position': 'html',
                    'blockInfo': {'enabled': true}
                });
                AppConnector.request(params).then(
                    function (data) {
                        progressIndicatorElement.progressIndicator({'mode': 'hide'});
                        if(data.success){
                            Vtiger_Helper_Js.showMessage({type: 'success', text: '付款方式更改成功'});
                            window.location.reload();
                        }else{
                            Vtiger_Helper_Js.showMessage({type: 'error', text: data.message});
                        }
                    },
                    function () {
                        window.location.reload();
                    }
                );

            }, function (error, err) {});



        });
    },
    getContractsAgreement: function ()
    {
        var params = {};
        params.data = {
            "module": "ServiceContracts",
            "action": "ChangeAjax",
            "mode": "getContractsAgreement",
            "recordid": $('#recordId').val()
        };
        params.async = false;
        var strdata = '';
        AppConnector.request(params).then(
                function (data) {
                    if (data.num > 0) {
                        strdata = '<fieldset>\
                        <legend>补充协议合同编号</legend>';


                        $.each(data.result, function (key, value) {
                            var contractsno = value.contractsno == null ? '未生成合同编号' : value.contractsno;
                            strdata += '<span class="label label-a_normal">' + contractsno + "</span>&nbsp;&nbsp;";
                            var keya = key + 1;
                            if (keya > 1 && keya % 3 == 0) {
                                strdata += '<br><hr>';
                            }

                        });
                        strdata += '</fieldset>';
                    }
                },
                function () {
                }
        );
        return strdata;
    },
    getUserData: function () {
        var params = {};
        params.data = {
            'module': 'ServiceContracts',
            'action': 'ChangeAjax',
            'mode': 'getuserlist'
        };
        params.async = false;
        var strs = '';
        var currentuser = $('#current_user_id').val();
        AppConnector.request(params).then(
                function (data) {
                    if (data.success) {
                        var str = '';
                        var userselect = '';
                        $.each(data.result, function (key, value) {
                            userselect = value.id == currentuser ? 'selected' : '';
                            str += '<option value="' + value.id + '" ' + userselect + '>' + value.username + '</option>';
                        })
                        strs = '<div style="margin:10px; 20px;text-align:center;height:250px;"><select id="reportid" class="chzn-select">' + str + '</select></div>';

                    }
                },
                function () {
                }
        );
        return strs;
    },
    ToVoidActivationCode: function () {
        $('body').on('click','#ServiceContracts_detailView_basicAction_LBL_TOVOIDACTIVATIONCODE', function () {
            var message = 'T云客户端可取消激活码,71360平台取消激活会作废订单,确定取消吗?';
            var msg = {
                'message': message,
                'width': '800px'
            };
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function (e) {
                var params = {
                    'module': 'ServiceContracts',
                    'action': 'ChangeAjax',
                    'mode': 'ToVoidActivationCode',
                    "recordid": $('#recordId').val()
                };
                var progressIndicatorElement = jQuery.progressIndicator({
                    'message': '正在努力处理...',
                    'position': 'html',
                    'blockInfo': {'enabled': true}
                });
                AppConnector.request(params).then(
                        function (data) {
                            progressIndicatorElement.progressIndicator({'mode': 'hide'});
                            if (data.success) {
                                if (data.result.success) {
                                    Vtiger_Helper_Js.showMessage({type: 'success', text: data.result.message});
                                    window.location.reload();
                                } else {
                                    Vtiger_Helper_Js.showMessage({type: 'error', text: data.result.message});
                            }

                            }

                        },
                        function () {
                        }
                );

            }, function (error, err) {});

        });
    },
    showSalesOrderStatus: function ()
    {

        var message = '<h3>T云工单工作流</h3><hr>';
        var msg = {
            'message': message
        };
        var params = {};
        params.data = {
            "module": "ServiceContracts",
            "action": "ChangeAjax",
            "mode": "getSalesOrderStatus",
            "recordid": $('#recordId').val(),
            "usercode": $('#usercode').val()
        };
        params.async = false;
        var str = '';

        AppConnector.request(params).then(
                function (data) {
                    if (data.success)
                    {
                        str = '<hr><div style="padding:5px;"><ul class="nav nav-pills">';
                        $.each(data.message, function (key, value) {
                            str += '<li style="float: none;vertical-align: middle;display: inline-block;">';
                            $.each(value.ServerItem, function (ckey, cvalue) {
                                var isaction = cvalue.IsComplete ? 'label-success' : 'label-inverse';
                                str += '<span class="label ' + isaction + '" title="' + cvalue.Name + '">' + value.ServerName + '\\' + cvalue.Name + '</span><br>';

                            });
                            str += '</li>';
                            if (key != data.message.length - 1) {
                                str += '<li style="float: none;vertical-align: middle;display: inline-block;"><i class="icon-arrow-right" style=""></i></li>';
                            }
                        });
                        str += '</ul></div><div><hr>图例说明：<span class="label  label-success" title="">已经完成的节点</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\
                                           <span class="label label-inverse" title="">即将完成的节点</span></div>';
                    } else
                    {
                        str = data.message;
                    }
                },
                function () {
                }
        );
        $('#insertcomment').append(str);
    },
    files_deliver: function () {
        $('.details').on("click", '#realremarkbutton', function () {
            var remark = $('#remarkvalue');
            if (remark.val() == '') {
                remark.focus();
                return false;
            }
            var name = $('#stagerecordname').val();
            var msg = {'message': "是否要给工单阶段<" + name + ">添加备注？", };
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function (e) {
                var params = {};
                params['record'] = $('#recordid').val();//工单id
                params['isrejectid'] = $('#backstagerecordeid').val();
                params['isbackname'] = $('#backstagerecordname').val();
                params['reject'] = $('#remarkvalue').val();
                params['action'] = 'SaveAjax';
                params['module'] = 'SalesorderWorkflowStages';
                params['mode'] = 'submitremark';
                params['src_module'] = app.getModuleName();
                var d = {};
                d.data = params;
                AppConnector.request(d).then(
                        function (data) {
                            if (data.success == true) {
                                var widgetContainer = $(".widgetContainer_workflows");
                                var urlParams = widgetContainer.attr('data-url');
                                params = {
                                    'type': 'GET',
                                    'dataType': 'html',
                                    'data': urlParams
                                };
                                widgetContainer.progressIndicator({});
                                AppConnector.request(params).then(
                                        function (data) {
                                            widgetContainer.progressIndicator({'mode': 'hide'});
                                            widgetContainer.html(data);
                                            Vtiger_Helper_Js.showMessage({type: 'success', text: '备注添加成功'});
                                        },
                                        function () {}
                                );
                            } else {
                                Vtiger_Helper_Js.showMessage({type: 'error', text: '备注添加失败,原因' + data.error.message});
                            }
                        }, function () {}
                );
            });
        });
    },
    showConfirmationBox: function (data) {
        var thisstance = this;
        var aDeferred = jQuery.Deferred();
        var width = '800px';
        if (typeof data['width'] != "undefined") {
            width = data['width'];
        }
        var bootBoxModal = bootbox.confirm({message: data['message'], width: width, callback: function (result) {
                if (result) {
                    if (thisstance.checkedform(data['is_form'])) {
                        aDeferred.resolve();
                    } else {
                        return false;
                    }
                } else {
                    aDeferred.reject();
                }
            }, buttons: {cancel: {
                    label: '取消',
                    className: 'btn'
                },
                confirm: {
                    label: '确认',
                    className: 'btn-success'
                }
            }});
        bootBoxModal.on('hidden', function (e) {
            if (jQuery('#globalmodal').length > 0) {
                jQuery('body').addClass('modal-open');
            }
        });
        return aDeferred.promise();
    },
    checkedform: function (is_form =0) {
        var pagenumber = $('#pagenumber').val();
        var remark = $('#remark').val();
        if (pagenumber < 1) {
            $('#pagenumber').focus();
            $('#pagenumber').attr('data-content', '<font color="red">页数要大于0!</font>');
            ;
            $('#pagenumber').popover("show");
            $('.popover').css('z-index', 1000010);
            $('.popover-content').css({"color": "red", "fontSize": "12px"});
            setTimeout("$('#remark').popover('destroy')", 2000);
            return false;
        }
        if (remark == '') {

            $('#remark').focus();
            $('#remark').attr('data-content', '<font color="red">必填项不能为空!</font>');
            ;
            $('#remark').popover("show");
            $('.popover').css('z-index', 1000010);
            $('.popover-content').css({"color": "red", "fontSize": "12px"});
            setTimeout("$('#remark').popover('destroy')", 2000);
            return false;//跳出each
        }
        var souje = $('#souje').val();
        var soujbanhao = $('#soujbanhao').val();
        if (souje == '') {

            $('#souje').focus();
            $('#souje').attr('data-content', '<font color="red">必填项不能为空!</font>');
            ;
            $('#souje').popover("show");
            $('.popover').css('z-index', 1000010);
            $('.popover-content').css({"color": "red", "fontSize": "12px"});
            setTimeout("$('#souje').popover('destroy')", 2000);
            return false;//跳出each
        }
        if (soujbanhao == '') {
            $('#soujbanhao').focus();
            $('#soujbanhao').attr('data-content', '<font color="red">必填项不能为空!</font>');
            ;
            $('#soujbanhao').popover("show");
            $('.popover').css('z-index', 1000010);
            $('.popover-content').css({"color": "red", "fontSize": "12px"});
            setTimeout("$('#soujbanhao').popover('destroy')", 2000);
            return false;//跳出each
        }

        //添加分成验证
        if(is_form){
            var suoshurenObj = $('.select-suoshuren');
            var scallingObj = $('.input-scalling');
            var totalScalling = 0;
            var suoshuren = [];
            var error = false;
            $.each(scallingObj, function(key, v) {
                var scalling = $(v).val();
                var tmp_suoshuren = suoshurenObj.eq(key).val();
                if(scalling == '' || scalling <= 0) {
                    alert('分成比例应为大于0的数字');
                    error = true;
                    return;
                } else if(scalling > 100) {
                    alert('分成比列允许最大值为100');
                    error = true;
                    return;
                }
                if (tmp_suoshuren == '') {
                    alert('业绩所属人必须选择');
                    error = true;
                    return;
                }
                if (suoshuren.includes(tmp_suoshuren)) {
                    alert('业绩所属人不能相同');
                    error = true;
                    return;
                }
                suoshuren.push(tmp_suoshuren);
                totalScalling += parseInt(scalling);
            });
            if(error) {
                return false;
            }
            if(totalScalling != 100){
                alert('分成比例之和需等于100');
                return false;
            }
        }

        return true;
    },
    /**
     * 撤销发送
     */
    revokeSending: function () {
        $('body').on('click','#ServiceContracts_detailView_basicAction_LBL_REVOKESENDING',function () {
            var params = {};
            params['record'] = $('#recordid').val();//工单id
            params['action'] = 'BasicAjax';
            params['module'] = 'ServiceContracts';
            params['mode'] = 'getReceiverInfo';
            var progressIndicatorElement = jQuery.progressIndicator({
                'message': '正在获取电子合同相关信息...',
                'position': 'html',
                'blockInfo': {'enabled': true}
            });
            AppConnector.request(params).then(
                function (data) {
                    progressIndicatorElement.progressIndicator({'mode': 'hide'});
                    if (data.success) {
                        var msg = {'message': "<h4>此电子合同客户还未签署，请选择操作</h4><hr>",
                            'action':function(){
                                var emstatus=$('input:radio[name="emstatus"]:checked').val();
                                if(emstatus==undefined){
                                    Vtiger_Helper_Js.showMessage({type: 'error', text: '请选择撤销类型！' });
                                    return false;
                                }
                                if(emstatus==1){
                                    if($('#inputusername').val()==''){
                                        Vtiger_Helper_Js.showMessage({type: 'error', text: '联系人必填！' });
                                        return false;
                                    }
                                    var inputmobile=$('#inputmobile').val();
                                    if(inputmobile==''){
                                        Vtiger_Helper_Js.showMessage({type: 'error', text: '联系人手机号必填！' });
                                        return false;
                                    }
                                    if(!(/^1[3456789]\d{9}$/.test(inputmobile))){
                                        Vtiger_Helper_Js.showMessage({type: 'error', text: '联系人手机号有误！' });
                                        return false;
                                    }
                                }
                                return true;
                            }
                        };
                        var iscustomized = data.result.iscustomized;
                        Vtiger_Helper_Js.showConfirmationBox(msg).then(function (e) {
                            var emstatus=$('input:radio[name="emstatus"]:checked').val();
                            var inputusername=$('#inputusername').val()
                            var inputmobile=$('#inputmobile').val();
                            var params = {};
                            params['record'] = $('#recordid').val();//工单id
                            params['emstatus'] =emstatus;
                            params['inputusername'] =inputusername;
                            params['inputmobile'] =inputmobile;
                            params['action'] = 'BasicAjax';
                            params['module'] = 'ServiceContracts';
                            params['mode'] = 'doRevokeSending';
                            var d = {};
                            d.data = params;
                            AppConnector.request(d).then(
                                function (data) {
                                    if (data.success == true) {
                                        if(data.result.flag){
                                            if(2==emstatus){
                                                window.location.href='/index.php?module=ServiceContracts&view=Edit&record='+$('#recordid').val();
                                            }else{
                                                window.location.reload();
                                            }
                                        } else {
                                            Vtiger_Helper_Js.showMessage({type: 'error', text: data.result.msg});
                                        }
                                    }
                                }, function () {}
                            );
                        });
                        if(iscustomized){
                            var str = '<div class="control-group"><div class="controls"></div><label class="radio inline"><input type="radio" name="emstatus" value="1">重新发送</label><label class="radio inline"><input type="radio" name="emstatus" value="3">仅撤回</label></div></div><br><br><div class="control-group"><div class="controls" ><span class="" style="font-size: 16px;">接收人:</span><input type="text" name="inputusername" id="inputusername" value="'+data.result.elereceiver+'" readonly="readonly" class="span8" style="font-size: 18px;border: none;border-bottom: 1px solid #ccc;box-shadow: none !important;"></div></div><div class="control-group"><div class="controls" ><span class="" style="font-size: 16px;">接收手机号码:</span><input type="text" name="inputmobile" id="inputmobile" readonly="readonly" value="'+data.result.elereceivermobile+'"  class="span8" style="font-size: 18px;border: none;border-bottom: 1px solid #ccc;box-shadow: none !important;"></div></div>';
                        }else{
                            var str = '<div class="control-group"><div class="controls"></div><label class="radio inline"><input type="radio" name="emstatus" value="1">重新发送</label><label class="radio inline"><input type="radio" name="emstatus" value="2">撤回修改合同内容后发送</label><label class="radio inline"><input type="radio" name="emstatus" value="3">仅撤回</label></div></div><br><br><div class="control-group"><div class="controls" ><span class="" style="font-size: 16px;">接收人:</span><input type="text" name="inputusername" id="inputusername" value="'+data.result.elereceiver+'" readonly="readonly" class="span8" style="font-size: 18px;border: none;border-bottom: 1px solid #ccc;box-shadow: none !important;"></div></div><div class="control-group"><div class="controls" ><span class="" style="font-size: 16px;">接收手机号码:</span><input type="text" name="inputmobile" id="inputmobile" readonly="readonly" value="'+data.result.elereceivermobile+'"  class="span8" style="font-size: 18px;border: none;border-bottom: 1px solid #ccc;box-shadow: none !important;"></div></div>';
                        }

                        $('.modal-dialog').css("marginTop","200px");
                        $('.modal-body .bootbox-close-button').after('<button type="button" class="showhelp close" style="margin-top: -10px;margin-right:10px;" data-title="<span style=\'color:#169BD5;\'>操作说明</span>" data-content=\'<div><p><span style="font-weight:700;">撤回并发送：</span><span style="font-weight:400;">撤回后立即发送</span></p><p><span style="font-weight:700;">撤回修改合同内容后发送：</span><span style="font-weight:400;">撤回后，将会跳转到合同表单编辑页，重新修改合同内容后发送（合同类型、购买类型、合同模板ERP限制不可变更）</span></p><p><span style="font-weight:700;">仅撤回：</span><span style="font-weight:400;">撤回合同不再发送，合同置为作废状态，如需修改合同，可以重新新建电子合同</span></p></div>\'>?</button>');
                        $('.bootbox-body').append(str);
                        $('.showhelp').popover({placement: 'left','trigger':'hover',template: '<div class="popover" style="z-index:10000000;" role="tooltip"><div class="arrow"></div><h3 class="popover-title"><span style="color:#169BD5;">操作说明</span></h3><div class="popover-content"><div><p><span style="font-weight:700;">撤回并发送：</span><span style="font-weight:400;">撤回后立即发送，支持修改接收人及手机号</span></p><p><span style="font-weight:700;">撤回修改合同内容后发送：</span><span style="font-weight:400;">撤回后，将会跳转到合同表单编辑页，重新修改合同内容后发送（合同类型、购买类型、合同模板ERP限制不可变更）</span></p><p><span style="font-weight:700;">仅撤回：</span><span style="font-weight:400;">撤回合同不再发送，合同置为作废状态，如需修改合同，可以重新新建电子合同</span></p></div></div></div>'});
                    }
                }, function () {}
            );

        });
    },
    reSendElecContracts:function(){
        $('body').on('click','#ServiceContracts_detailView_basicAction_LBL_RESARTSENDING',function(){
            var msg = {
                'message': '电子合同确定要重新发送？',
            };
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(data){
                var postData = {
                    "module": 'ServiceContracts',
                    "action": "BasicAjax",
                    "recordid": $('#recordid').val(),
                    'mode': 'elecResend'
                };
                AppConnector.request(postData).then(
                    function (res) {
                        if (res && res.success) {
                            Vtiger_Helper_Js.showMessage({type: 'success', text: '电子合同已重新发送'});
                            window.location.reload();
                        } else {
                            Vtiger_Helper_Js.showMessage({type: 'error', text: res.msg});
                        }
                    });
            });
        });
    },
    elecDoCancel:function(){
        $('body').on('click','#ServiceContracts_detailView_basicAction_LBL_ELECDOCANCEL',function(){
            var msg = {
                'message': '电子合同确定要作废吗？',
            };
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(data){
                var postData = {
                    "module": 'ServiceContracts',
                    "action": "BasicAjax",
                    "recordid": $('#recordid').val(),
                    'mode': 'elecDoCancel'
                };
                AppConnector.request(postData).then(
                    function (data) {
                        if (data && data.result.flag) {
                            window.location.reload();
                        } else {
                            Vtiger_Helper_Js.showMessage({type: 'error', text: data.result.msg});
                        }
                    });
            });
        });
    },
    customizeSendMessage:function() {
        var thisInstance = this;
        $('body').on('click','#ServiceContracts_detailView_basicAction_LBL_SEND_MESSAGE', function () {
            var ServiceContracts_detailView_fieldValue_contract_no = $("#ServiceContracts_detailView_fieldValue_contract_no span").text();
            var ServiceContracts_detailView_fieldValue_Signid = $("#ServiceContracts_detailView_fieldValue_Signid span").text();
            var ServiceContracts_detailView_fieldValue_assigned_user_id = $("#ServiceContracts_detailView_fieldValue_assigned_user_id span").text();
            var ServiceContracts_detailView_fieldValue_Receiveid = $("#ServiceContracts_detailView_fieldValue_Receiveid span").text();
            contract_no = ServiceContracts_detailView_fieldValue_contract_no.trim();
            signid = ServiceContracts_detailView_fieldValue_Signid.trim();
            assignid = ServiceContracts_detailView_fieldValue_assigned_user_id.trim();
            receiveid = ServiceContracts_detailView_fieldValue_Receiveid.trim();

            var strr = '<form name="insertcomment" id="formcomment">\
                            <div id="insertcomment" style="height: 300px;overflow: auto">\
                            <table class="table table-bordered blockContainer Duplicates showInlineTable  detailview-table" data-num="1" id="comments1"><tbody>\
                            <tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">合同编号:</label></td>' +
                '<td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><input type="text" id="contract_no" name="contract_no" readonly value="'+contract_no+'"></span></div></td>' +
                '<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">消息内容<span class="redColor">*</span>:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><textarea name="msg" id="msg" style="height: 160px;" class="span11" maxlength="100" placeholder="最多可输入100个汉字"></textarea></span></div></td></tr>' +
                '<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">接收人:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10">'+signid+'、'+assignid+'、'+receiveid+'</span></div></td></tr>' +
                '</tbody></table>' + "" +
                '</div></form>';

            var message = '<h3>消息发送</h3><hr>';
            var msg = {
                'message': message,
                'width': '800px'
            };

            thisInstance.showConfirmationBox(msg).then(function (e) {
                var postData = {
                    "module": 'ServiceContracts',
                    "action": "BasicAjax",
                    "recordid": $('#recordid').val(),
                    'mode': 'customizeSendMessage',
                    'msg':$("#msg").val(),
                };
                AppConnector.request(postData).then(
                    function (data) {
                        if (data && data.result.flag) {
                            Vtiger_Helper_Js.showMessage({type: 'success', text: data.result.msg});
                        } else {
                            Vtiger_Helper_Js.showMessage({type: 'error', text: data.result.msg});
                        }
                    });
            });
            $('.modal-content .modal-body').append(strr);
        });
    },
        wkSign:function(){
        $('body').on('click','#ServiceContracts_detailView_basicAction_LBL_WKSIGN',function(){
            var msg = {
                'message': '<h4>一键签收</h4><hr>',
                'action':function(){
                    var suppliercontractsid=$('#suppliercontractsid').val();
                    var suppliercontractsname=$('#suppliercontractsname').val();
                    if(suppliercontractsname==''){
                        Vtiger_Helper_Js.showMessage({type: 'error', text: '请输入采购合同'});
                        return false;
                    }
                    if(suppliercontractsid==0){
                        Vtiger_Helper_Js.showMessage({type: 'error', text: '请先验证采购合同'});
                        return false;
                    }
                    if(suppliercontractsid>0 && suppliercontractsname!=''){
                        return true;
                    }
                    return false;
                }
            };
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(data){
                var suppliercontractsid=$('#suppliercontractsid').val();
                var suppliercontractsname=$('#suppliercontractsname').val();
                var postData = {
                    "module": 'ServiceContracts',
                    "action": "BasicAjax",
                    "recordid": $('#recordid').val(),
                    'mode': 'wkSign',
                    'suppname': suppliercontractsname,
                    'suppliercontractsid': suppliercontractsid

                };
                AppConnector.request(postData).then(
                    function (data) {
                        if (data && data.result.flag) {
                            Vtiger_Helper_Js.showMessage({type: 'success', text: data.result.msg});
                            setTimeout('window.location.reload();',1000);
                            //window.location.reload();
                        } else {
                            Vtiger_Helper_Js.showMessage({type: 'error', text: data.result.msg});
                        }
                    });
            });
            var str = '<br><br><div class="control-group"><div class="row-fluid input-prepend input-append"><span class="add-on clearReferenceSelectionwk cursorPointer"><i id="" class="icon-remove-sign" title="清除"></i></span><input id="suppliercontractsid" type="hidden" value="0"/><input id="suppliercontractsname" name="suppliercontractsname" type="text" class="span11 \tmarginLeftZero autoComplete ui-autocomplete-input" value="" placeholder="请输入采购合同编号并点查找" autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true"><span class="add-on relatedPopup cursorPointer"><i id="ServiceContracts_editView_fieldName_scwk_search" class="icon-search relatedPopup" title="选择"></i></span></div></div>';
            $('.modal-dialog').css("marginTop","200px");
            $('.bootbox-body').append(str);
        });
        $('body').on('click','.clearReferenceSelectionwk',function(){
            $('#suppliercontractsid').val(0);
            $('#suppliercontractsname').val('');
        });
        $('body').on('click','#ServiceContracts_editView_fieldName_scwk_search',function(){
            $('#suppliercontractsid').val(0);
            var suppliercontractsname=$('#suppliercontractsname').val();
            if(suppliercontractsname!=''){
                var postData = {
                    "module": 'ServiceContracts',
                    "action": "BasicAjax",
                    "recordid": $('#recordid').val(),
                    "suppname": suppliercontractsname,
                    'mode': 'searchSupplierContractsNo'
                };
                AppConnector.request(postData).then(
                    function (data) {
                        if (data && data.result.flag) {
                            $('#suppliercontractsid').val(data.result.data.suppid);
                            Vtiger_Helper_Js.showMessage({type: 'success', text: data.result.msg});
                        } else {
                            Vtiger_Helper_Js.showMessage({type: 'error', text: data.result.msg});
                        }
                    });
            }
        });
    },
    /**
     * Function to register events
     */

    receRefresh:function(){
        //刷新
        $("body").on('click','#receRefresh',function () {
            var Message = '';
            var progressIndicatorElement = jQuery.progressIndicator({
                'message' : Message,
                'position' : 'html',
                'blockInfo' : {'enabled' : true}
            });
            var module = app.getModuleName();
            var postData = {
                "module": module,
                "action": "BasicAjax",
                "record": $('#recordId').val(),
                "mode": 'getContractAmount'
            }
            AppConnector.request(postData).then(
                function(data){
                    progressIndicatorElement.progressIndicator({
                        'mode' : 'hide'
                    });
                    if(data.success) {
                        $("#totalMoney").html(data.result.paymentTotal);
                        $("#receivedMoney").html(data.result.paymentReceived);
                        $("#remainMoney").html(data.result.paymentElse);
                        $("#lowestMoney").html(data.result.leastPayMoney);
                    }
                },
                function(error,err){

                }
            );
        });
    },
    SPECIALCONTRACT:function(){//普通合同特殊合同切换
        $('body').on('click','#ServiceContracts_detailView_basicAction_LBL_SPECIALCONTRACT,#ServiceContracts_detailView_basicAction_LBL_SPECIALCONTRACT1',function(){
            var _this=$(this);
            var thisid=_this.attr('id');
            var thistitle='普通合同切换为特殊合同';
            var changecurrnetid='ServiceContracts_detailView_basicAction_LBL_SPECIALCONTRACT1';
            var changecurrnettitle='特殊合同切换为普通合同';
            if(thisid=='ServiceContracts_detailView_basicAction_LBL_SPECIALCONTRACT1'){
                thistitle='特殊合同切换为普通合同';
                changecurrnetid='ServiceContracts_detailView_basicAction_LBL_SPECIALCONTRACT';
                changecurrnettitle='普通合同切换为特殊合同';
            }
            var msg = {
                'message': '<h4>'+thistitle+'</h4><hr>',
            };
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(data) {
                var postData = {
                    "module": 'ServiceContracts',
                    "action": "BasicAjax",
                    "recordid": $('#recordid').val(),
                    'mode': 'setSpecialContracto'
                };
                AppConnector.request(postData).then(
                    function (data) {
                        Vtiger_Helper_Js.showMessage({type: 'success', text: data.result.msg});
                        _this.attr('id',changecurrnetid);
                        _this.children('strong').text(changecurrnettitle);
                });
            });
        });
    },

    registerEvents: function () {
        this._super();
        this.getconfirmrecord();
        this.files_deliver();
        this.ToVoidActivationCode();
        this.dividedModification();
        this.registerEventaddfallinto();
        this.revokeSending();
        this.reSendElecContracts();
        this.elecDoCancel();
        this.customizeSendMessage();
	    this.wkSign();
	    this.getAccountFile();
        this.collate();//核对
        this.receRefresh();
        this.changeSmowner();
        this.SPECIALCONTRACT()
    },

    getAccountFile:function(){
        var postData = {
            "module": 'ServiceContracts',
            "action": "BasicAjax",
            "record": $("#recordid").val(),
            'mode': 'getAccountFile'
        }
        AppConnector.request(postData).then(
            // 请求成功
            function(data){
                var html='';
                if(data.result){
                    for(var i in data.result.result){
                        html+='<a style="margin-right: 5px" href="index.php?module=Newinvoice&action=DownloadFile&filename='+i+'">'+data.result.result[i]+'</a>';
                    }
                }
                $("#FileUpload").html(html);
            },
            function(error,err){

            }
        );
    },

    registerEventaddfallinto:function(){
        $('body').on('click','"#addfallinto_1"',function(){
            var datanum = $(this).attr('data-num');
            var options = '';
            if(staffList) {
                staffList.forEach(function(value, index){
                    options += '<option value="' + value['id']+'" data-company="' + value['invoicecompany'] + '" data-picklistvalue= "'+value['id'] + '">' + value['last_name'] + '</option>';
                })
            }
            var html = '<tr id="divide_tr_' + datanum + '"><td><input type="text" class="input-xlarge input-suoshugongsi" data-numbers="0" name="suoshugongsi[]" disabled="disabled"></td>' +
            '<td><select class="chzn-select select-suoshuren" name="suoshuren[]">' + options + '</select></td>' +
            '<td><div class="input-append "> <input name="bili[]" type="text" placeholder = "请输入比例" class="scaling input-scalling"><span class="add-on">%</i></span></div></td>' +
            '<td><button class="btn btn-small deletefallinto" type="button"><i class="icon-trash"></i></button></td>' +
            '</tr>';
            $('#fallintotable_2 tbody').append(html);
            var sel = $('#divide_tr_' + datanum + ' .select-suoshuren');
            sel.val(divideUserId);
            $('#divide_tr_' + datanum + ' .input-suoshugongsi').val(sel.find("option:selected").data('company'));
            $('#divide_tr_' + datanum + ' .select-suoshuren').chosen();

            $(this).attr('data-num', datanum + 1);
        });

        $('body').on('change', '.select-suoshuren', function(){
            var tr = $(this).parents('tr');
            tr.find('.input-suoshugongsi').val($(this).find("option:selected").data('company'));
        });

        $('body').on('blur','.scaling',function(){
            if(!isNaN($(this).val())){
                $(this).val(Number($(this).val()).toFixed(0));
            }else{
                $(this).val(0);
            }

        });

        $('body').on('click','.deletefallinto',function(){
            $(this).closest('tr').remove();
        });

    },
	/**
     * Function to handle the ajax edit for detailview and summary view fields
     * which will expects the currentTdElement
     */
    ajaxEditHandling : function(currentTdElement) {
        $('.fileUploadContainer').find('form').css({width:"48px"});
        $('.fileUploadContainer').find('form').find('.btn-info').css({width:"48px",marginLeft:"-12px"});
        var thisInstance = this;
        var detailViewValue = jQuery('.value',currentTdElement);
        var editElement = jQuery('.edit',currentTdElement);
        var actionElement = jQuery('.summaryViewEdit', currentTdElement);
        if(editElement.length <= 0) {
            return;
        }

        if(editElement.is(':visible')){
            return;
        }

        detailViewValue.addClass('hide');
        editElement.removeClass('hide').show().children().filter('input[type!="hidden"]input[type!="image"],select').filter(':first').focus();
        var saveTriggred = false;
        var preventDefault = false;
        var saveHandler = function(e) {
            var element = jQuery(e.target);
            if((element.closest('td').is(currentTdElement))){
                return;
            }
            if(element[0]['className']=='next' || element[0]['className']=='prev' || element[0]['className']=='datepicker-switch' || element[0]['className']=='month active' || element[0]['className']=='month'){
                console.log(element);
                return false;
            }
            currentTdElement.removeAttr('tabindex');
            var fieldnameElement = jQuery('.fieldname', editElement);
            var previousValue = fieldnameElement.data('prevValue');
            var fieldName = fieldnameElement.val();
            var fieldElement = jQuery('[name="'+ fieldName +'"]', editElement);
            var formElement = thisInstance.getForm();
            var formData = formElement.serializeFormData();
            var ajaxEditNewValue = formData[fieldName];
            //value that need to send to the server
            var fieldValue = ajaxEditNewValue;
            var fieldInfo = Vtiger_Field_Js.getInstance(fieldElement.data('fieldinfo'));

            // Since checkbox will be sending only on and off and not 1 or 0 as currrent value
            if(fieldElement.is('input:checkbox')) {
                if(fieldElement.is(':checked')) {
                    ajaxEditNewValue = '1';
                } else {
                    ajaxEditNewValue = '0';
                }
                fieldElement = fieldElement.filter('[type="checkbox"]');
            }
            var errorExists = fieldElement.validationEngine('validate');
            //If validation fails

            if(errorExists&& fieldName!='file') {
                return;
            }




            //Before saving ajax edit values we need to check if the value is changed then only we have to save
            if(previousValue == ajaxEditNewValue) {
                editElement.addClass('hide');
                detailViewValue.removeClass('hide');
                actionElement.show();
                jQuery(document).off('click', '*', saveHandler);
            } else {
                var preFieldSaveEvent = jQuery.Event(thisInstance.fieldPreSave);
                fieldElement.trigger(preFieldSaveEvent, {'fieldValue' : fieldValue,  'recordId' : thisInstance.getRecordId()});
                if(preFieldSaveEvent.isDefaultPrevented()) {
                    //Stop the save
                    saveTriggred = false;
                    preventDefault = true;
                    return
                }
                preventDefault = false;

                jQuery(document).off('click', '*', saveHandler);

                if(!saveTriggred && !preventDefault) {
                    saveTriggred = true;
                }else{
                    return;
                }

                currentTdElement.progressIndicator();
                editElement.addClass('hide');
                var fieldNameValueMap = {};
                if(fieldInfo.getType() == 'multipicklist') {
                    var multiPicklistFieldName = fieldName.split('[]');
                    fieldName = multiPicklistFieldName[0];
                }
                fieldNameValueMap['value'] = fieldValue;
                fieldNameValueMap['field'] = fieldName;
                if(fieldName=='file'){
                    var newvalu={};
                    var newattachmentsid=new Array();
                    $('input[name^="file["]').each(function(i,val){
                        newvalu[i]=$(val).val();
                        newattachmentsid[i]=$(val).data('id');
                    });
                    fieldNameValueMap['value']=newvalu;
                    fieldNameValueMap['attachmentsid']=newattachmentsid;
                }
                //return;
                //console.log(fieldNameValueMap['field']);
                //console.log(fieldNameValueMap['value']);
                thisInstance.saveFieldValues(fieldNameValueMap).then(function(response) {
                        var postSaveRecordDetails = response.result;
                        currentTdElement.progressIndicator({'mode':'hide'});
                        detailViewValue.removeClass('hide');
                        actionElement.show();
                        detailViewValue.html(postSaveRecordDetails[fieldName].display_value);
                        fieldElement.trigger(thisInstance.fieldUpdatedEvent,{'old':previousValue,'new':fieldValue});
                        fieldnameElement.data('prevValue', ajaxEditNewValue);
                    },
                    function(error){
                        //TODO : Handle error
                        currentTdElement.progressIndicator({'mode':'hide'});
                    }
                )
            }
        }

        jQuery(document).on('click','*', saveHandler);
    },
    /**
     * Divided  jason.chang
     */
    dividedModification :function(){
        var thisInstance=this;

        $('body').on('click', '#divided_modification', function () {
            //分成params
            var param={};
            param['record']=$('#recordId').val();
            param['action']='BasicAjax';
            param['module']='ServiceContracts';
            param['mode']='isCanDivided';
            AppConnector.request(param).then(
                function(data){
                    if(data.result.result==true){
                        var message = '修改合同分成申请';
                        var msg = {
                            'message': message,
                            'width': '1000px',
                            'is_form': '1',
                        };
                        thisInstance.showConfirmationBox(msg).then(function (e) {
                            var params = {};
                            params['action'] = 'BasicAjax';
                            params['module'] = 'ServiceContracts';
                            params['mode'] = 'addDivided';
                            params['recordid']= $('#recordId').val();

                            var suoshugongsiObj = $('.input-suoshugongsi');
                            var suoshurenObj = $('.select-suoshuren');
                            var scallingObj = $('.input-scalling');

                            var suoshugongsi = [];
                            var suoshuren = [];
                            var biliren = [];
                            $.each(scallingObj, function(key, v){
                                suoshugongsi.push(suoshugongsiObj.eq(key).val());
                                suoshuren.push(suoshurenObj.eq(key).val());
                                biliren.push($(v).val());
                            });
                            params['suoshugongsi'] = suoshugongsi;
                            params['suoshuren'] = suoshuren;
                            params['biliren'] = biliren;
                            AppConnector.request(params).then(
                                function (data) {
                                    if(data.success) {
                                        location.reload();
                                    }
                                    else
                                    {
                                        Vtiger_Helper_Js.showMessage({type: 'error', text: data.error.message});
                                        return false;
                                    }
                                }, function () {}
                            );
                        }, function (error, err) {
                            //location.reload();
                        });
                        staffList = data.result.staffList;
                        shareInfo = data.result.shareInfo;


                        var html = '<div style="overflow-y:auto;max-height:500px;height:500px;padding-top:10px;padding-right:2px">' +
                            '<table class="table table-bordered blockContainer showInlineTable detailview-table blockContainer_tab blockContainer_tab_1" id="fallintotable_2">' +
                            '<thead><th class="blockHeader" colspan="4">合同分成信息</th></tr></thead><tbody><tr>' +
                            '<td><b>所属公司</b></td><td><b>业绩所属人</b></td><td><b>比例</b></td>' +
                            '<td style="width:60px;"><button class="btn btn-small" type="button" id="addfallinto_1" data-num="0"><i class="icon-plus"></i></button></td></tr>' +
                            '</tbody></table></div>';
                        $('.modal-content .modal-body').append(html);

                        console.log(shareInfo);
                        if(shareInfo){
                            var options = '';
                            if(staffList) {
                                staffList.forEach(function(value, index){
                                    selectStr='';
                                    if(shareInfo.userid==value['id']){
                                        selectStr='selected:selected';
                                    }
                                    options += '<option value="' + value['id']+'" '+selectStr+' data-company="' + value['invoicecompany'] + '" data-picklistvalue= "'+value['id'] + '">' + value['last_name'] + '</option>';
                                })
                            }
                            var html2 = '<tr "><td><input type="text" class="input-xlarge input-suoshugongsi" data-numbers="0" name="suoshugongsi[]" disabled="disabled" value="'+shareInfo.invoicecompany+'"></td>' +
                                '<td><select class="chzn-select select-suoshuren" disabled="disabled" name="suoshuren[]">' + options + '</select></td>' +
                                '<td><div class="input-append "> <input name="bili[]" type="text" readonly placeholder = "请输入比例" class="scaling input-scalling" value="'+shareInfo.promotionsharing+'"><span class="add-on">%</i></span></div></td>' +
                                '<td></td>' +
                                '</tr>';
                            $('#fallintotable_2 tbody').append(html2);
                        }

                        var sel = $("select[name='suoshuren[0]']");
                        sel.val(divideUserId);
                        console.log(sel.find("option:selected").data('company'));
                        $("input[name='suoshugongsi[0]']").val(sel.find("option:selected").data('company'));
                        $('.chzn-select').chosen();
                        $('.modal-body').css("overflow-y",'hidden');
                        $('.chzn-select_1').chosen();
                        $('.modal-footer .btn+.btn').addClass("areyou");
                    }else{
                        Vtiger_Helper_Js.showMessage({type: 'error', text: data.result.message});
                    }
                }
            )
        });

    },
    collate : function() { //核对
        $('body').on("click", '#ServiceContracts_detailView_basicAction_LBL_COLLATE', function() { //单个核对
            var contractid = $('#recordId').val();
            var dialog = bootbox.dialog({
                title: '服务合同核对',
                width:'600px',
                message: '<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0"><tbody>'+
                    '<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>是否符合:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><select id="checkresult"><option value="fit">是</option><option value="unfit">否</option></select></span></div></td></tr>'+
                    '<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor" style="display: none;" id="remarkstar">*</span>备注:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><textarea id="remark" style="overflow:hidden;overflow-wrap:break-word;resize:none; height:100px;width:320px;"></textarea></span></div></td></tr>'+
                    '</tbody></table>',
                buttons: {
                    ok: {
                        label: "确定",
                        className: 'btn-success',
                        callback: function() {
                            var checkresult = $('#checkresult').val();
                            var remark = $('#remark').val();
                            if (checkresult == 'unfit' && remark=='') {
                                var params = {type: 'error', text: '选择否时，备注必须填写'};
                                Vtiger_Helper_Js.showMessage(params);
                                return false;
                            }
                            if (remark.length>2000) {
                                var params = {type: 'error', text: '备注允许最大长度为2000'};
                                Vtiger_Helper_Js.showMessage(params);
                                return false;
                            }
                            var postData = {
                                "module": 'ServiceContracts',
                                "action": 'ChangeAjax',
                                'contractid': contractid,
                                "checkresult": checkresult,
                                'remark': remark,
                                'mode': 'collateContract'
                            }
                            var Message = "提交中...";
                            var progressIndicatorElement = jQuery.progressIndicator({
                                'message' : Message,
                                'position' : 'html',
                                'blockInfo' : {'enabled' : true}
                            });
                            AppConnector.request(postData).then(
                                function(data) {
                                    // 隐藏遮罩层
                                    progressIndicatorElement.progressIndicator({
                                        'mode' : 'hide'
                                    });
                                    if(data.success) {
                                        if (data.result.status == 'success') {
                                            var params = {type: 'success', text: '成功核对'};
                                            Vtiger_Helper_Js.showMessage(params);
                                        } else {
                                            var params = {type: 'error', text: data.result.msg};
                                            Vtiger_Helper_Js.showMessage(params);
                                        }
                                    } else {
                                        var params = {type: 'error', text: data.error.message};
                                        Vtiger_Helper_Js.showMessage(params);
                                    }
                                },
                                function(error,err) {

                                }
                            );
                        }
                    },
                    cancel: {
                        label: "取消",
                        className: 'btn',
                        callback: function(){

                        }
                    }
                }
            });
        }).on('change', '#checkresult', function() {
            if( $(this).val()=='unfit') {
                $('#remarkstar').show();
            } else {
                $('#remarkstar').hide();
            }
        });
    },
    changeSmowner:function () {
        $("body").on("click","#ServiceContracts_detailView_basicAction_LBL_CHANGSMOWNER",function () {
            var msg = {
                'message': "<h4>请选择合同新领用人</h4><hr>",
                'action': function () {
                    return true;
                }
            };
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function (e) {
                var newsmownerid=$("select[name='reportsower']").val();
                var postData = {
                    "module": 'ServiceContracts',
                    "action": "BasicAjax",
                    "recordid": $('#recordid').val(),
                    "newsmownerid": newsmownerid,
                    'mode': 'doChangeSmowner'
                };
                AppConnector.request(postData).then(
                    function (data) {
                        if (data.success) {
                            Vtiger_Helper_Js.showMessage({type: 'success', text: data.msg});
                            window.location.reload();
                        } else {
                            Vtiger_Helper_Js.showMessage({type: 'error', text: data.msg});
                        }
                    });
            });
            var str = '<div class="control-group" style="height: 300px;"><div class="control-group"><br/><div class="controls" ><span style="color: red">*</span><span class="" style="font-size: 16px;"> 新领用人 </span>'+accessible_users+'</div></div>';

            $('.modal-dialog').css("marginTop", "200px");
            $('.modal-body .bootbox-close-button').after();
            $('.bootbox-body').append(str);
            $(".chzn-select").chosen();
        });

    },
    bindStagesubmit:function(){
        $('.details').on('click','.stagesubmit',function(){
            var name=$('#stagerecordname').val();
            var msgStr = "确定要审核工单阶段"+name+"?";
            var workflowsid=$("#workflowsid").val();
            if(workflowsid==3072416){
                msgStr +="<br><br><span style='color: red'>(完成该项审批后,该合同则归属你名下,请谨慎操作)</span>"
                var maxhandlecontractnum = $("input[name='maxhandlecontractnum']").val();
                if(maxhandlecontractnum){
                    Vtiger_Helper_Js.showMessage({type:'error',text:'你已领取的合同已超出合同领取份额，可将现有合同归还后再次审批'});
                    return;
                }
            }
            var msg={
                'message':msgStr,
            };
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                var params={};
                params['record'] = $('#recordid').val();
                params['stagerecordid'] = $('#stagerecordid').val();
                params['action'] = 'SaveAjax';
                params['module'] = 'SalesorderWorkflowStages';
                params['mode'] = 'updateSalseorderWorkflowStages';
                params['src_module'] = app.getModuleName();
                params['checkname'] = $('#backstagerecordname').val();
                params['customer']=$("#customer").val()==undefined?0:$("#customer").val();
                params['customername']=$("#customer").find("option:selected").text()==undefined?'':$("#customer").find("option:selected").text();
                //ie9下post请求是失败的，如果get可以的请修改

                var d={};
                d.data=params;
                d.type = 'GET';
                var progressIndicatorElement = jQuery.progressIndicator({
                    'message' : '亲,正在拼命处理,请耐心等待哟',
                    'position' : 'html',
                    'blockInfo' : {'enabled' : true}
                });

                AppConnector.request(d).then(
                    function(data){
                        if(data.success==true){
                            //刷新当前的挂件，在这里本来可以使用父类的方法，但是不生效，只能重新写了
                            var widgetContainer = $(".widgetContainer_workflows");
                            //
                            var urlParams = widgetContainer.attr('data-url');
                            params = {
                                'type' : 'GET',
                                'dataType': 'html',
                                'data' : urlParams
                            };
                            widgetContainer.progressIndicator({});
                            AppConnector.request(params).then(

                                function(data){
                                    widgetContainer.progressIndicator({'mode': 'hide'});
                                    widgetContainer.html(data);
                                    Vtiger_Helper_Js.showMessage({type:'success',text:'审核成功'});
                                },
                                function(){}
                            );
                        }else{
                            Vtiger_Helper_Js.showMessage({type:'error',text:'审核失败,原因'+data.error.message});
                        }
                        progressIndicatorElement.progressIndicator({
                            'mode' : 'hide'
                        });
                    },function(){}
                );
            },function(error, err) {});
        });
    },


})
