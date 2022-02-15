/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("Billing_Edit_Js",{},{
//公司名称与购方企业名称值写入
    accountiddisplaychange : function(){
        $('table').on('click','.setcompanyname',function(){
            var source=$('input[name="'+$(this).data('name')+'"]');

            if(source.val()!=''){

                $('input[name="businessnamesone"]').val(source.val());
                $('input[name="businessnames"]').val(source.val());
                $('input[name="businessnamesnegative"]').val(source.val());
            }
        });
    },

    registerEvents: function(container){
        this._super(container);
        $('input[name="account_id_display"]').next().after('<button type="button" class="btn btn-info setcompanyname" data-name="account_id_display">设为开票对象</button>');
        $('input[name="companyname"]').parent().css('whiteSpace','nowrap');
        this.accountiddisplaychange();
    }
});


