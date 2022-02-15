/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
Vtiger_Edit_Js("ReceivedPaymentsCollate_Edit_Js", {}, {
    registerReferenceSelectionEvent: function (container) {
        this._super(container);
        var thisInstance = this;

    },
    registerBasicEvents: function (container) {
        this._super(container);
        this.registerReferenceSelectionEvent(container);
    },
    //wangbin 增加自定义阻止提交事件
    registerRecordPreSaveEvent: function (form) {
        var thisInstance = this;
        var editViewForm = this.getForm();
        editViewForm.on(Vtiger_Edit_Js.recordPreSave, function (e, data) {
            var isCheck = $("input[name='isshow']").val();
            var isShow = false;
            if(!isCheck){
                var params = {
                    'module': 'ReceivedPaymentsCollate',
                    'action': 'BasicAjax',
                    'mode': 'checkPaymentCode',
                    "recordid": $('#recordId').val(),
                    'paymentcode': $("#ReceivedPaymentsCollate_editView_fieldName_paymentcode").val()
                };
                var progressIndicatorElement = jQuery.progressIndicator({
                    'message' : '正在努力加载,请稍后',
                    'position' : 'html',
                    'blockInfo' : {'enabled' : true}
                });
                params.async=false;
                AppConnector.request(params).then(function (data) {
                        progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                        console.log(data);
                        if (data.success) {
                            if (data.result.flag) {
                                $("input[name='paymentnum']").val(data.result.paymentnum);
                                var msg = {
                                    'message': "<h4>存在相同交易单号的" + data.result.paymentnum + "笔回款，确定要提交修改？</h4><hr>",
                                    'action': function () {
                                        return true;
                                    }
                                };
                                Vtiger_Helper_Js.showConfirmationBox(msg).then(function (e) {
                                    $("input[name='isshow']").val(1);
                                    isShow = true;
                                    $("#saveData").trigger("click");
                                });
                            }else{
                                $("input[name='isshow']").val(1);
                                isShow = true;
                                $("#saveData").trigger("click");
                            }
                        }
                    },
                    function () {
                    }
                );
            }

            if (!isCheck) {
                e.preventDefault();
                return false;
            }

            // var paymentnum = $("input[name='paymentnum']").val();
            // var isshow = $("input[name='isshow']").val();
            // if (paymentnum && !isshow) {
            //     var msg = {
            //         'message': "<h4>存在相同交易单号的" + paymentnum + "笔回款，确定要提交修改？</h4><hr>",
            //         'action': function () {
            //             return true;
            //         }
            //     };
            //     Vtiger_Helper_Js.showConfirmationBox(msg).then(function (e) {
            //         $("input[name='isshow']").val(1);
            //         $(".btn-success").trigger("click");
            //         isShow = true;
            //     });
            //     if (!isShow) {
            //         e.preventDefault();
            //         return false;
            //     }
            // }

            if ($("#ReceivedPaymentsCollate_editView_fieldName_owncompany").val().indexOf("请选择") != -1) {
                Vtiger_Helper_Js.showMessage({type: 'error', text: '请选择正确的公司账号'});
                e.preventDefault(); //阻止提交事件先注释
                return;
            }
            var paytitle = $("input[name='paytitle']").val();
            var paymentchannel = $("select[name='paymentchannel']").val();
            if (paymentchannel != '扫码' && paytitle == '') {
                Vtiger_Helper_Js.showMessage({type: 'error', text: '支付渠道是对公转账/支付宝转账时汇款抬头必填'});
                e.preventDefault(); //阻止提交事件先注释
                return;
            }
            var paymentcode = $("input[name='paymentcode']").val();
            // if(paymentchannel!='对公转账'&& paymentcode==''){
            // 	Vtiger_Helper_Js.showMessage({type:'error',text:'支付渠道是支付宝转账/扫码时交易单号必填'});
            // 	e.preventDefault(); //阻止提交事件先注释
            // 	return;
            // }
            var chineseReg = /[\u4E00-\u9FA5]|[\uFE30-\uFFA0]/g;
            if (paymentcode != '' && chineseReg.test(paymentcode)) {
                Vtiger_Helper_Js.showMessage({type: 'error', text: '交易单号不允许填写中文'});
                e.preventDefault(); //阻止提交事件先注释
                return;
            }

            if (!$('input[name="relatetoid"]').attr('disabled')) {
                if (parseInt($('input[name="relatetoid"]').val())) {
                    //添加自动分配产品金额注释掉验证
//			if($("#currsubtract").text() !== "0.00"){
//				Vtiger_Helper_Js.showMessage({type:'error',text:'收款金额未填写完整,还剩余'+$("#currsubtract").text()+'收款金额未填写'});
//				e.preventDefault(); //阻止提交事件先注释
//				return;
//			}


                    return;
                    //判断业绩所属人是否为空
                    var isError = false;
                    //$('#fallintotable tbody tr:eq(0) td:eq(1)')
                    $('#fallintotable tbody tr').each(
                        function () {
                            //var v = $(this).find("td:eq(1)").find(".chzn-results").find(".result-selected").val();
                            v = $(this).find("td:eq(1) select").val();
                            if (v == "") {
                                Vtiger_Helper_Js.showMessage({type: 'error', text: '业绩所属人不能为空'});
                                isError = true;
                                e.preventDefault(); //阻止提交事件先注释
                                return;
                            }
                        }
                    );

                    if (isError) {
                        return;
                    }
                    var scalingtotal = parseInt("0");
                    $(".scaling").each(
                        function () {
                            if (parseInt(Number($(this).val())) == 0) {
                                Vtiger_Helper_Js.showMessage({type: 'error', text: '分成比例不能为空'});
                                e.preventDefault(); //阻止提交事件先注释
                                return;
                            }
                            scalingtotal += parseInt(Number($(this).val()));
                        }
                    );
                    if (scalingtotal !== 100) {
                        Vtiger_Helper_Js.showMessage({type: 'error', text: '分成比例之和必须为100%'});
                        e.preventDefault(); //阻止提交事件先注释
                    }
                }
            }


        });

    },
    selectevent: function (type) {
        var selector = '<span calss="span6"><select id="Select1"></select></span><span calss="span6"><select id="Select2"></select></span>';
        if ($("#Select1").length == 0) {
            $("#ReceivedPaymentsCollate_editView_fieldName_owncompany").attr("style", "width:530px").before(selector);
        }
        var params = {
            "action": 'BasicAjax',
            'module': 'ReceivedPayments',
            'mode': 'getCompanyAccountsByChannel',
            'channel': $("select[name='paymentchannel']").val()
        }
        AppConnector.request(params).then(
            function (data) {
                if (data.success && data.result.flag) {
                    var dataString = eval(data.result.dataString);
                    var defaults = {
                        NextSelId: '#Select2',
                        SelTextId: '#ReceivedPaymentsCollate_editView_fieldName_owncompany',
                        Separator: '##',
                        SelStrSet: dataString
                    };
                    $('#Select1').html('');
                    $('#Select1').unbind();
                    $('#Select1').selected(defaults);
                    if (type == 'go') {
                        $('#Select2').trigger('change');
                    }
                }
            });
    },

    paymentchannelChange: function () {
        var thisInstance = this;
        $("select[name='paymentchannel']").change(function () {
            if ($(this).val() == '对公转账') {
                $("#paytitleMust").show();
                $("input[name='paytitle']").enable();
                // $("#paymentcodeMust").hide();
            } else if ($(this).val() == '支付宝转账') {
                $("#paytitleMust").show();
                $("input[name='paytitle']").enable();
                // $("#paymentcodeMust").show();
            } else if ($(this).val() == '扫码') {
                $("#paytitleMust").hide();
                $("input[name='paytitle']").disable();
                $("input[name='paytitle']").val('');
                // $("#paymentcodeMust").show();
            }
            thisInstance.selectevent('go');
        });
    },

    payCodeBlur: function () {
        $("#ReceivedPaymentsCollate_editView_fieldName_paymentcode").on("blur", function () {
            var params = {
                'module': 'ReceivedPaymentsCollate',
                'action': 'BasicAjax',
                'mode': 'checkPaymentCode',
                "recordid": $('#recordId').val(),
                'paymentcode': $("#ReceivedPaymentsCollate_editView_fieldName_paymentcode").val()
            };
                var progressIndicatorElement = jQuery.progressIndicator({
                    'message' : '正在努力加载,请稍后',
                    'position' : 'html',
                    'blockInfo' : {'enabled' : true}
                });
            AppConnector.request(params).then(function (data) {
                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                    console.log(data);
                    if (data.success) {
                        if (data.result.flag) {
                            $("input[name='paymentnum']").val(data.result.paymentnum);
                        }
                    }
                },
                function () {
                }
            );
        })
    },
    registerEvents: function () {

        this._super();
        var tablestring = '<tr> <td></td> <td class="text-center" colspan="3"> <div class="row"> <table border="1"> <tr> <td>负责人</td><td>合同总金额</td><td>合同类型</td><td>客户名称</td> </tr> <tr id="serviceinfo"> <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> </tr> </table> </div> </td> </tr>';
        $("input[name='relatetoid']").closest("tr").after(tablestring);
        $('input[name="paytitle"]').before('<div id="match_account"></div>');
        $("#ReceivedPaymentsCollate_editView_fieldName_owncompany").attr("readonly", true);
        this.registerRecordPreSaveEvent();
        this.selectevent();
        this.paymentchannelChange();
        // this.payCodeBlur();

        if ($("select[name='paymentchannel']").val() == '对公转账') {
            $("#paytitleMust").show();
            $("input[name='paytitle']").enable();
            // $("#paymentcodeMust").hide();
        } else if ($("select[name='paymentchannel']").val() == '支付宝转账') {
            $("#paytitleMust").show();
            $("input[name='paytitle']").enable();
            // $("#paymentcodeMust").show();
        } else if ($("select[name='paymentchannel']").val() == '扫码') {
            $("input[name='paytitle']").disable();
            $("#paytitleMust").hide();
            // $("#paymentcodeMust").show();
        }
    }
});


