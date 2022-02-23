/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("SuppContractsAgreement_Edit_Js", {}, {
    SuppContractsType : 'cost',

    registerReferenceSelectionEvent: function (container) {
        var thisInstance = this;

        //2015年4月24日 星期五 根据合同的客户负责人选择默认合同提单人 wangbin
        jQuery('input[name="suppliercontractsid"]', container).on(Vtiger_Edit_Js.referenceSelectionEvent, function (e, data) {
            thisInstance.relatedchange();
        });
    },

    relatedchange:function(){
        var thisInstance = this;
        var sparams = {
            'module': 'SuppContractsAgreement',
            'action': 'BasicAjax',
            'record': $('input[name="suppliercontractsid"]').val(),
            'mode': 'getAccount'
        };
        AppConnector.request(sparams).then(
            function (datas) {
                if (datas.success == true && datas.result.flag) {
                    $('input[name="vendorid"]').val(datas.result.accountid);
                    $('input[name="vendorid_display"]').val(datas.result.accountname)

                    // 合同主体隐藏值进行赋值
                    // console.log(datas.result);
                    $('input[name="invoicecompany"]').val(datas.result.invoicecompany)

                    if ('无锡珍岛数字生态服务平台技术有限公司' == datas.result.invoicecompany) {
                    // if ('凯丽隆（上海）软件信息科技有限公司' == datas.result.invoicecompany) {
                        // alert('show');
                        // $('select[name="sealplace"]').parent().show();
                        $('select[name="sealplace"]').parents('.fieldValue').prev().css('visibility', 'visible');
                        $('select[name="sealplace"]').parents('.fieldValue').css('visibility', 'visible');
                    } else {
                        // alert('hide');
                        // $('select[name="sealplace"]').parent().hide();
                        $('select[name="sealplace"]').val('');
                        $('select[name="sealplace"]').parents('.fieldValue').prev().css('visibility', 'hidden');
                        $('select[name="sealplace"]').parents('.fieldValue').css('visibility', 'hidden');
                    }

                    if(datas.result.type=='purchase'){
                        thisInstance.SuppContractsType='purchase';
                        $("#receiptoridMust").show();
                        $("#vendoridMust").show();
                        $("#select_SuppContractsAgreement_receiptorid").val($("#current_user_id").val());
                        $("select[name='receiptorid[]']").chosen();
                        $("select[name='receiptorid[]']").trigger("liszt:updated");
                    }else{
                        thisInstance.SuppContractsType='cost';
                        $("#receiptoridMust").hide();
                        $("#vendoridMust").hide();
                        $("#select_SuppContractsAgreement_receiptorid").val('');
                        $("select[name='receiptorid[]']").chosen();
                        $("select[name='receiptorid[]']").trigger("liszt:updated");
                    }
                }
            }
        )
    },


    registerResultEvent: function (form) {
        var thisInstance = this;
        if (typeof form == 'undefined') {
            form = this.getForm();
        }
        form.on(Vtiger_Edit_Js.recordPreSave, function (e, data) {

            console.log($('input[name="invoicecompany"]').val());
            console.log($('select[name="sealplace"]').val());


            // if (!$('select[name="sealplace"]').val()) {
            //     Vtiger_Helper_Js.showMessage({type:'error', text:'合同主体为无锡珍岛数字生态服务平台技术有限公司必须选择用章地点'});
            //     e.preventDefault();
            //     return false;
            // }



            if ('无锡珍岛数字生态服务平台技术有限公司' == $('input[name="invoicecompany"]').val() && !$('select[name="sealplace"]').val()) {
                Vtiger_Helper_Js.showMessage({type:'error', text:'合同主体为无锡珍岛数字生态服务平台技术有限公司必须选择用章地点'});
                e.preventDefault();
                return false;
            }

            return;

            if(thisInstance.SuppContractsType=='purchase'&&$("#select_SuppContractsAgreement_receiptorid").val()==''){
                //采购合同代领人必填
                Vtiger_Helper_Js.showMessage({type:'error',text:'采购合同【采购/费用合同代领人】必填'});
                return false;
            }
            if(thisInstance.SuppContractsType=='purchase'&&$("input[name='vendorid']").val()==''){
                //供应商必须有
                Vtiger_Helper_Js.showMessage({type:'error',text:'采购合同【采购/费用合同代领人】必填'});
                return false;
            }
            var attachments=$('input[name*="attachmentsid["]');
            if(attachments.length==0){
                Vtiger_Helper_Js.showMessage({type:'error',text:"合同附件必填!!!"});
                return false;
            }
            if(thisInstance.SuppContractsType=='cost'){
                $("select[input='receiptorid[]']").val('');
                $("select[input='receiptorid[]']").chosen();
                $("select[name='receiptorid[]']").trigger("liszt:updated");
                $("input[name='vendorid']").val('');
            }
            return true;
        });
    },
    sealplaceChange: function () {
        // $('select[name="sealplace"]').parent().hide();
        $('select[name="sealplace"]').val('');
        $('select[name="sealplace"]').parents('.fieldValue').prev().css('visibility', 'hidden');
        $('select[name="sealplace"]').parents('.fieldValue').css('visibility', 'hidden');
    },
    registerEvents: function (container) {
        this._super(container);

        this.sealplaceChange();
        if($("input[name='record']").val()>0){
            this.relatedchange();
        }
        this.registerReferenceSelectionEvent();
        this.registerResultEvent(container);


    }
});


