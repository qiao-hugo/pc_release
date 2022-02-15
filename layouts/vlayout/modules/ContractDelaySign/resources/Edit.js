/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("ContractDelaySign_Edit_Js", {}, {
    registerReferenceSelectionEvent: function (container) {
        var thisInstance = this;

        //2015年4月24日 星期五 根据合同的客户负责人选择默认合同提单人 wangbin
        jQuery('input[name="servicecontractsid"]', container).on(Vtiger_Edit_Js.referenceSelectionEvent, function (e, data) {
            relatedchange();
        });

        function relatedchange() {
            var sparams = {
                'module': 'ContractsAgreement',
                'action': 'BasicAjax',
                'record': $('input[name="servicecontractsid"]').val(),
                'mode': 'getAccount'
            };
            AppConnector.request(sparams).then(
                function (datas) {
                    if (datas.success == true && datas.result.flag) {
                        $('input[name="account_id"]').val(datas.result.accountid);
                        $('input[name="account_id_display"]').val(datas.result.accountname)
                    }
                }
            )
        }
    },
    registerResultEvent: function (form) {
        var thisInstance = this;
        if (typeof form == 'undefined') {
            form = this.getForm();
        }

        form.on(Vtiger_Edit_Js.recordPreSave, function (e, data) {
            var attachments=$('input[name*="attachmentsid["]');
            if(attachments.length==0)
            {
                Vtiger_Helper_Js.showMessage({type:'error',text:"合同附件必填!!!"});
                e.preventDefault();
            }
        })
    },
    registerEvents: function (container) {
        this._super(container);

        this.registerReferenceSelectionEvent();
        this.registerResultEvent(container);

    }
});


