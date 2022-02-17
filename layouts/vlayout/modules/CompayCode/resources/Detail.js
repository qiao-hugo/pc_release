/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("CompayCode_Detail_Js",{},{
    addCompanyFXQ: function () {
        $('#CompayCode_detailView_basicAction_LBL_ADD_COMPANYFXQ').click(function (){
            Vtiger_Helper_Js.showConfirmationBox({'message':'确定要同步到电子合同管理后台吗?'}).then(function(e){
                var params = {
                    'module' : 'CompayCode', //ServiceContracts
                    'action' : 'BasicAjax',
                    'mode':'addCompanyFXQ',
                    "recordid":$('#recordId').val()
                };
                AppConnector.request(params).then(
                    function(data){
                        if(data.success){
                            Vtiger_Helper_Js.showMessage({type:'success',text:'同步成功'});
                        }else{
                            Vtiger_Helper_Js.showMessage({type:'error',text:data.msg});
                        }
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
		this.addCompanyFXQ();
	}
});