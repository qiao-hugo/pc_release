/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("InputInvoice_Detail_Js", {}, {

    saveDeductMessage: function () {
        var thisInstance = this;
        $(".saveDeductMessage").on("click",function () {
            var salename = $("input[name='salename']").val();
            var buyername = $("input[name='buyername']").val();
            var invoicecode = $("input[name='invoicecode']").val();
            var invoiceno = $("input[name='invoiceno']").val();
            var servicename = $("input[name='servicename']").val();
            var amount = $("input[name='amount']").val();
            var taxrate = $("input[name='taxrate']").val();
            var taxamount = $("input[name='taxamount']").val();
            var totalpricetax = $("input[name='totalpricetax']").val();
            if (!salename || !buyername || !invoicecode || !invoiceno || !servicename || !amount || !taxamount || !totalpricetax) {
                var params = {
                    text: "抵扣联信息所有项必填",
                    type: 'error'
                };
                Vtiger_Helper_Js.showMessage(params);
                return;
            }
            var params = {};
            params.data = {
                "module": "InputInvoice",
                "action": "BasicAjax",
                "mode": "saveDeductMessage",
                "recordid": $('#recordId').val(),
                "salename":salename,
                "buyername":buyername,
                "invoicecode":invoicecode,
                "invoiceno":invoiceno,
                "servicename":servicename,
                "amount":amount,
                "taxrate":taxrate,
                "taxamount":taxamount,
                "totalpricetax":totalpricetax,
            };
            params.async = false;
            AppConnector.request(params).then(
                function (data) {
                    console.log(data);
                    if (data.result.success) {
                        $("#discountcoupon").val(1);
                        var params = {
                            text: '保存成功',
                            type: 'success'
                        };
                        Vtiger_Helper_Js.showMessage(params);
                    }
                },
                function () {
                }
            );
        })
    },

    bindStagesubmit:function(){
        $('.details').on('click','.stagesubmit',function(){
            var name=$('#stagerecordname').val();
            var discountcoupon = parseInt($("#discountcoupon").val());
            if(!discountcoupon && name=='对应会计录入抵扣联信息'){
                Vtiger_Helper_Js.showMessage({type:'error',text:'请先填写抵扣联信息'});
                return;
            }
            var msg={
                'message':"确定要审核工单阶段"+name+"?",
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
                                    if(name=='对应会计录入抵扣联信息'){
                                        location.reload();
                                    }
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
    registerEvents: function () {
        this._super();
        this.saveDeductMessage();
    },
});

function initDiscountBool() {
    $("#discountcoupon").val(0)
}
