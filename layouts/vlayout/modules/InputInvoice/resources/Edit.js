/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("InputInvoice_Edit_Js", {}, {
    ckEditorInstance: '',
    ckEInstance: '',
    productnum: [],
    rowSequenceHolder: false,
    isonbeforeunload: true,
    customizedData: [],
    parentView: {},
    registerEvents: function (container) {
        this._super(container);
        this.checkApplicationNo();
        this.getInputInvoiceTable();
        this.initColumn();
        this.calSurplus();
        this.totalprefix();
        this.registerRecordPreSaveEvent();
    },
    totalprefix: function () {
        if (!$('input[name="record"]').val()) {
            var type = $('select[name="currencytype"]').val('人民币');
            $("select[name='currencytype']").next('div').children('a').children('span').text('人民币');
            $('#InputInvoice_editView_fieldName_paymentamount').prev().text('人民币');
            $('#InputInvoice_editView_fieldName_subamount').prev().text('人民币');
            $('#InputInvoice_editView_fieldName_surplusamount').prev().text('人民币');
        }else{
            var type = $('select[name="currencytype"]').val();
            $("select[name='currencytype']").next('div').children('a').children('span').text(type);
            $('#InputInvoice_editView_fieldName_paymentamount').prev().text(type);
            $('#InputInvoice_editView_fieldName_subamount').prev().text(type);
            $('#InputInvoice_editView_fieldName_surplusamount').prev().text(type);
        }
        $('select[name="currencytype"]').on('change', function () {
            var type = $('select[name="currencytype"]').val();
            if (type) {
                $('#InputInvoice_editView_fieldName_paymentamount').prev().text(type);
                $('#InputInvoice_editView_fieldName_subamount').prev().text(type);
                $('#InputInvoice_editView_fieldName_surplusamount').prev().text(type);
            }
        })
    },
    calSurplus:function(){
        $('body').on('focus', 'input[name=paymentamount]', function () {
            var billproperty= $("select[name='billproperty']").val();
            if(billproperty=='AfterHandSub'){
                return;
            }
            $("#InputInvoice_editView_fieldName_surplusamount").attr("readonly","readonly");
        });

        $('body').on('input', 'input[name=paymentamount]', function () {
            var billproperty= $("select[name='billproperty']").val();
            if(billproperty=='AfterHandSub'){
                return;
            }
            var paymentamount = $("#InputInvoice_editView_fieldName_paymentamount").val();
            console.log(paymentamount);
            var subamount = $("#InputInvoice_editView_fieldName_subamount").val();
            console.log(subamount);
            if(parseFloat(subamount)>=parseFloat(paymentamount)){
                $("#InputInvoice_editView_fieldName_surplusamount").val(0);
                return;
            }
            $("#InputInvoice_editView_fieldName_surplusamount").val(parseFloat(paymentamount-subamount).toFixed(2));
        });
        $('body').on('input', 'input[name=subamount]', function () {
            var billproperty= $("select[name='billproperty']").val();
            if(billproperty=='AfterHandSub'){
                return;
            }
            var paymentamount = $("#InputInvoice_editView_fieldName_paymentamount").val();
            console.log(paymentamount);
            var subamount = $("#InputInvoice_editView_fieldName_subamount").val();
            console.log(subamount);
            if(parseFloat(subamount)>=parseFloat(paymentamount)){
                $("#InputInvoice_editView_fieldName_surplusamount").val(0);
                return;
            }
            $("#InputInvoice_editView_fieldName_surplusamount").val(parseFloat(paymentamount-subamount).toFixed(2));
        })
    },
    initColumn:function(){
        $('body').on('focus', 'input[name=applicationno]', function () {
            var applicationtype = $('select[name="applicationtype"]').val();
            if (applicationtype != 'RefillApplicationVendors') {
                $("#InputInvoice_editView_fieldName_payeecompany").val('');
                $("#InputInvoice_editView_fieldName_payeecompany").attr("readonly",false);
                $("#refillapplicationid").val('');
                return;
            }
        });
        $('form').on('change', 'select[name=applicationtype]', function () {
            var applicationtype = $('select[name="applicationtype"]').val();
            if (applicationtype != 'RefillApplicationVendors') {
                $("#InputInvoice_editView_fieldName_payeecompany").val('');
                $("#InputInvoice_editView_fieldName_payeecompany").attr("readonly",false);
                $("#refillapplicationid").val('');
                return;
            }else{
                $("#InputInvoice_editView_fieldName_applicationno").val("");
            }
        });
    },
    getInputInvoiceTable: function () {
        var thisInstance = this;
        var oldsignaturetypevale = $('select[name="billproperty"]').val();
        $('form').on('change', 'select[name="billproperty"]', function () {
            var applicationtype = $('select[name="applicationtype"]').val();
            console.log(applicationtype);
            var thisVale = $(this).val();
            var record = $('input[name="record"]').val();
            if (thisVale != oldsignaturetypevale) {
                thisInstance.isonbeforeunload = false;
                if (thisVale == 'BeforeHandSub') {
                    var url = '/index.php?module=InputInvoice&view=Edit&billproperty=BeforeHandSub';
                    if (applicationtype) {
                        url += '&applicationtype=' + applicationtype;
                    }
                } else {
                    var url = '/index.php?module=InputInvoice&view=Edit&billproperty=AfterHandSub';
                    if (applicationtype) {
                        url += '&applicationtype=' + applicationtype;
                    }
                }
                if(record){
                    url +='&record='+record;
                }
                console.log()
                window.location.href = url;
            }
        });
    },
    checkApplicationNo: function () {
        $('body').on('blur', 'input[name=applicationno]', function () {
            var applicationtype = $('select[name="applicationtype"]').val();
            var applicationno = $("input[name='applicationno']").val();
            console.log(applicationtype);
            if (applicationtype != 'RefillApplicationVendors' || !applicationno) {
                return;
            }
            var postData = {
                "module": 'InputInvoice',
                "action": "BasicAjax",
                'mode': 'checkApplicationNo',
                'applicationno': applicationno
            };
            AppConnector.request(postData).then(
                function (data) {
                    if (data.result.success == true) {
                        $("#InputInvoice_editView_fieldName_payeecompany").val(data.result.customer_name);
                        $("#InputInvoice_editView_fieldName_payeecompany").attr("readonly","readonly");
                        $("#refillapplicationid").val(data.result.refillapplicationid);
                    }else{
                        Vtiger_Helper_Js.showMessage({type:'error',text:data.result.msg});
                    }
                }
            );
        });
    },
    //wangbin 增加自定义阻止提交事件
    registerRecordPreSaveEvent : function(form) {
        var thisInstance = this;
        var editViewForm = this.getForm();
        editViewForm.on(Vtiger_Edit_Js.recordPreSave,function(e,data){
            var fileInfo = $("#file").val();
            var billproperty = $("select[name='billproperty']").val();
            if(billproperty == 'BeforeHandSub' && (fileInfo==undefined || !fileInfo)){
                Vtiger_Helper_Js.showMessage({type:'error',text:'事前提交附件必填'});
                e.preventDefault(); //阻止提交事件先注释
                return;
            }
            var smownerid = $("select[name='smownerid']").val();
            if(billproperty == 'AfterHandSub' && (smownerid==undefined || !smownerid)){
                Vtiger_Helper_Js.showMessage({type:'error',text:'事后提交申请人必填'});
                e.preventDefault(); //阻止提交事件先注释
                return;
            }

        });

    },

    registerLeavePageWithoutSubmit : function(form){
        var thisInstance=this;
        InitialFormData = form.serialize();
        window.onbeforeunload = function(e){
            if (InitialFormData != form.serialize() && form.data('submit') != "true" && thisInstance.isonbeforeunload) {
                return app.vtranslate("JS_CHANGES_WILL_BE_LOST");
            }
        };
    },
});


