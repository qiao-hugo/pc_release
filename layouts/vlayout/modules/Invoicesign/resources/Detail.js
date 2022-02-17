/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("Invoicesign_Detail_Js",{
    /**
     * 作废按钮功能
     */
    getLocked:function(){
        $('#Billing_detailView_basicAction_LBL_LOCKED').on('click',function(){
            var act=$(this).data('act');
            var message='确定锁定该开票信息?';
            var msg={
                'message':message
            };
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                //alert($('#recordId').val());return;
                var params={};
                params['record'] = $('#recordId').val();
                params['action'] = 'ChangeAjax';
                params['module'] = 'Billing';
                AppConnector.request(params).then(
                    function(data) {
                        window.location.reload(true);
                    },
                    function(error,err){
                        window.location.reload(true);
                    }
                );
            },function(error, err) {});

        });
    },

    registerEvents:function(){
        this._super();
        this.getLocked();
    }
});