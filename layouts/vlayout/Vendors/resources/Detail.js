/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("Vendors_Detail_Js",{},{

	getDeleteMessageKey : function() {
		return 'LBL_RELATED_RECORD_DELETE_CONFIRMATION';
	},
	init: function () {
		$('#Vendors_detailView_basicAction_LBL_SET_VENDORSTATE').click(function (){
            Vtiger_Helper_Js.showConfirmationBox({'message':'确定转为正式供应商'}).then(function(e){
                var params = {
                    'module' : 'Vendors', //ServiceContracts
                    'action' : 'BasicAjax',
                    'mode':'addWorkFlows',
                    "recordid":$('#recordId').val()
                };
                AppConnector.request(params).then(
                    function(data){
                        window.location.reload();
                    },
                    function(){
                    }
                );

            },function(error, err) {});
		});
	},

	/**
	 * Function to register events
	 */
	registerEvents : function(){
		this._super();
	}
});