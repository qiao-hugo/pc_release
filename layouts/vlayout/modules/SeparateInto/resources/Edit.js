/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("SeparateInto_Edit_Js", {}, {
    registerReferenceSelectionEvent: function (container) {
        var thisInstance = this;

        //2015年4月24日 星期五 根据合同的客户负责人选择默认合同提单人 wangbin
        jQuery('input[name="servicecontractsid"]', container).on(Vtiger_Edit_Js.referenceSelectionEvent, function (e, data) {
            relatedchange();
        });

        jQuery('input[name="accountid"]', container).on(Vtiger_Edit_Js.referenceSelectionEvent, function (e, data) {
            accountidchange();
        });

        function accountidchange() {
            if($('input[name="accountid"]').val()=='0'){
                return;
            }
            var addfallintoNodes = $("#addfallinto").parent().parent().parent().siblings();
            if(addfallintoNodes.length>0){
                console.log(addfallintoNodes.length);
                addfallintoNodes.remove();
            }
            var sparams = {
                'module': 'SeparateInto',
                'action': 'BasicAjax',
                'record': $('input[name="accountid"]').val(),
                'mode': 'getShareInfo'
            };
            AppConnector.request(sparams).then(
                function (datas) {
                    if (datas.success == true && datas.result.flag) {

                        $('#fallintotable').append(aaaaa);
                        $('.chzn-select').chosen();

                        $("input[name='bili[]']").val(datas.result.data.promotionsharing);
                        $("input[name='bili[]']").attr("readonly",true);
                        $("select[name='suoshuren[]']").val(datas.result.data.suoshuren);
                        $("select[name='suoshuren[]']").attr("disabled",true);
                        $('select[name="suoshuren[]"]').trigger('liszt:updated');

                        $("select[name='suoshugongsi[]']").val(datas.result.data.suoshugongsi);
                        $("select[name='suoshugongsi[]']").attr("disabled",true);
                        $('select[name="suoshugongsi[]"]').trigger('liszt:updated');

                        $("input[name='salesharing']").val(datas.result.data.salesharing);

                        $(".deletefallinto").remove();
                    }
                }
            )
        }

        function relatedchange() {
            var addfallintoNodes = $("#addfallinto").parent().parent().parent().siblings();
            if(addfallintoNodes.length>0){
                console.log(addfallintoNodes.length);
                addfallintoNodes.remove();
            }
            var sparams = {
                'module': 'SeparateInto',
                'action': 'BasicAjax',
                'record': $('input[name="servicecontractsid"]').val(),
                'mode': 'getServicecontractInfo'
            };
            AppConnector.request(sparams).then(
                function (datas) {
                    if (datas.success == true && datas.result.flag) {
                        $('input[name="accountid"]').val(datas.result.accountid);
                        $('input[name="accountid_display"]').val(datas.result.accountname);
                        $('input[name="total"]').val(datas.result.total);
                        $('input[name="contract_type"]').val(datas.result.contract_type);
                        $('input[name="signdate"]').val(datas.result.signdate);
                        if(datas.result.accountid){
                            accountidchange();
                        }
                    }
                }
            )
        }
    },
    registerResultEvent: function (form) {
        /*$('input[name="accountid_display"]').prev('span').remove();
        $('input[name="accountid_display"]').next('span').remove();
        $('input[name="accountid_display"]').attr('readonly','readonly');*/
        //$('input[name="total"]').attr('readonly','readonly');
        $('input[name="contract_type"]').attr('readonly','readonly');

    },
    registerEventaddfallinto:function(){
        $("#addfallinto").click(function(){
            var accountid = $("input[name='accountid']").val();
            var servicecontractsid = $("input[name='servicecontractsid']").val();
            if(!accountid || !servicecontractsid){
                 alert("请先选择合同或客户");
                 return;
            }
            $('#fallintotable').append(aaaaa);
            $('.chzn-select').chosen();
        });
        $('body').on('click','.deletefallinto',function(){
            $(this).closest('tr').remove()
        });
        $('body').on('blur','.scaling',function(){
            if(!isNaN($(this).val())){
                $(this).val(Number($(this).val()).toFixed(0));
            }else{
                $(this).val(0)
            }
        });

    },
    registerEventRecordPreSave:function(){
        var thisInstance = this;
        var editViewForm = this.getForm();
        editViewForm.on(Vtiger_Edit_Js.recordPreSave,function(e,data){
            var scalingtotal = parseInt("0");
            $(".scaling").each(function(){
                    scalingtotal += parseInt(Number($(this).val()));
                }
            );
            if(scalingtotal!==100){
                Vtiger_Helper_Js.showMessage({type:'error',text:'分成比例之和必须为100%'});
                e.preventDefault(); //阻止提交事件先注释
            }
            // 设置被禁用的select 为可用这样可以用于post提交
            $("select[disabled=disabled]").each(function() {
                if (parseInt($(this).val()) != -1) {
                    $(this).attr("disabled", false);
                }
            });
        });
    },
    registerEvents: function (container) {
        this._super(container);

        this.registerReferenceSelectionEvent();
        this.registerResultEvent(container);
        this.registerEventaddfallinto();
        this.registerEventRecordPreSave();
        this.divideChoosed();
    },
    divideChoosed:function () {
        $('body').on('change','select[name="suoshuren[]"]',function(){
            var objects=$(this).closest("tr");
            var company=$(this).find("option:selected").data("company");
            console.log(company);
            objects.find("select[name='suoshugongsi[]']").val(company);
            objects.find("select[name='suoshugongsi[]']").trigger('liszt:updated');
        });
    }
});


