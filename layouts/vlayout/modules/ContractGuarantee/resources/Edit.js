/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("ContractGuarantee_Edit_Js", {}, {
    registerReferenceSelectionEvent: function (container) {
        var thisInstance = this;

        //2015年4月24日 星期五 根据合同的客户负责人选择默认合同提单人 wangbin
        jQuery('input[name="contractid"]', container).on(Vtiger_Edit_Js.referenceSelectionEvent, function (e, data) {
            relatedchange();
        });

        function relatedchange() {
            var sparams = {
                'module': 'ContractGuarantee',
                'action': 'BasicAjax',
                'record': $('input[name="contractid"]').val(),
                'mode': 'getAccountName'
            };
            AppConnector.request(sparams).then(
                function (datas) {
                    if (datas.success == true && datas.result.flag) {
                        $('input[name="accountid"]').val(datas.result.accountid);
                        $('input[name="accountid_display"]').val(datas.result.accountname);
                    }
                }
            )
        }
    },
    registerResultEvent: function (form) {
        $('input[name="accountid_display"]').prev('span').remove();
        $('input[name="accountid_display"]').next('span').remove();
        $('input[name="accountid_display"]').attr('readonly','readonly');
    },
    registerEvents: function (container) {
        this._super(container);

        this.registerReferenceSelectionEvent();
        this.registerResultEvent(container);

    }
});


