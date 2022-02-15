/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("TyunUpgradeRule_Edit_Js",{},{
	ckEditorInstance:'',
	ckEInstance:'',
	rowSequenceHolder : false,

    registerRecordPreSaveEvent: function () {
        var thisInstance = this;
        var editViewForm = this.getForm();
        editViewForm.on(Vtiger_Edit_Js.recordPreSave,function(e,data){
            var record=$('input[name="record"]').val();
            if(record>0){
            	return true;
			}
			var params={};
			params.data = {
				"module": "TyunUpgradeRule",
				"action": "ChangeAjax",
				"mode": "checkProduct",
				"record": $('input[name="record"]').val(),
				"productid": $('select[name="productid"]').val(),
				"tyundownup": $('select[name="tyundownup"]').val(),
			};
			params.async=false;
			var ajaxflag=false;
			AppConnector.request(params).then(
				function (data) {
					if(data.result){
						Vtiger_Helper_Js.showMessage({type:'error',text:'该产品已经添加了'});
						ajaxflag=true;
						e.preventDefault();
						return false;
					}
				},
				function (error) {
				});
			if(ajaxflag){
               	e.preventDefault();
				return false;
			}
        });
        var record = $('input[name=record]').val();
        if(record ) {
            $('select[name=productid]').next().find('.chzn-results').remove();
            $('select[name=tyundownup]').next().find('.chzn-results').remove();
        }
    },
	/**
		pop two calander.
	*/
	registerBasicEvents:function(container){
		this._super(container);
		this.registerRecordPreSaveEvent();

	}
});




















