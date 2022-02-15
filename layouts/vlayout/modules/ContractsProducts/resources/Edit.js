/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("ContractsProducts_Edit_Js",{},{
    registerResultEvent : function(form) {
        var thisInstance = this;
        if(typeof form == 'undefined') {
            form = this.getForm();
        }

        form.on(Vtiger_Edit_Js.recordPreSave, function(e, data) {
            var tags = $("input[type = checkbox]");
            var flag = false;

            for (i = 0;i < tags.length; i++) {
                if(tags[i].checked ==true ) {
                    flag = true;
                    break;
                }
            }

            if( flag == false) {
                    var  params = {text : '产品至少选中一个',
                         title :'请勾选产品！'
                    }
                Vtiger_Helper_Js.showPnotify(params);
                e.preventDefault();

            }else{
                return true;
            }
            e.preventDefault();
        })
    },

 	registerEvents: function(container){
		this._super(container);
        this.registerResultEvent(container);

	}
});


