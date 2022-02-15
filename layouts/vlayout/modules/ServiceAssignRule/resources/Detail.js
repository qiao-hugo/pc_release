/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("ServiceAssignRule_Detail_Js",{
},{
	/**
	 * 分配客服处理
	 */
	registerAssignClickEvent:function(){
		$('#btnAssign').on('click',function(){
			var message = app.vtranslate('JS_ASSIGN_SERVICE_MESSAGE');
			Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
				function(e) {
					//参数设置
					var postData = {
						"module": app.getModuleName(),
						"action": "SaveAjax",
						"record": jQuery('#recordId').val()
					}
					//发送请求
					AppConnector.request(postData).then(
						function(data){
							if(data != null && data.success ==  true){
								var message ="";
								if (data.result[0]==0){
									message = app.vtranslate('JS_ASSIGN_SERVICE_SUCCESS_MESSAGE');
								}else if (data.result[0]==2){
									message = app.vtranslate('JS_ASSIGN_SERVICE_EXCEED_MAX_MESSAGE');
								}else{
									message = app.vtranslate('JS_ASSIGN_SERVICE_NO_DATA_MESSAGE');
								}
								var params = {
									text: message,
									type: 'notice'
								};
								Vtiger_Helper_Js.showMessage(params);
								return;
							}else{
								var message = app.vtranslate('JS_ASSIGN_SERVICE_ERROR_MESSAGE');
								var params = {
										text: message,
										type: 'error'
									};
								Vtiger_Helper_Js.showMessage(params);return;
							}
							//刷新页面
							//window.location.reload();
						},
						function(error){
							var message = app.vtranslate('JS_ASSIGN_SERVICE_ERROR_MESSAGE');
							var params = {
									text: message,
									type: 'error'
								};
							Vtiger_Helper_Js.showMessage(params);return;
						}
					);
				},
				function(error){
				}
			)
		});
	},
	
	registerEvents:function(){
		this._super();
		this.registerAssignClickEvent();
	}
});