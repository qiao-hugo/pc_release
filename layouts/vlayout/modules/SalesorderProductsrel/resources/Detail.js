/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("SalesorderProductsrel_Detail_Js",{
},{
	/**
	 * 审核处理
	 */
	registerAuditClickEvent:function(){
		$('#btnAudit').on('click',function(){
			//var name=$('#salesorderproductsrelname').val();
			var message = app.vtranslate('LBL_AUDITOR_CONFIRM_MESSAGE');
			Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
				function(e) {
					//参数设置
					var postData = {
						"module": app.getModuleName(),
						"action": "SaveAjax",
						"record": jQuery('#recordId').val(),
						"type":"audit"
					}
					//发送请求
					AppConnector.request(postData).then(
						function(data){
							//刷新页面
							window.location.reload();
						},
						function(error,err){
							console.log(error);
						}
					);
				},
				function(error){
				}
			)
		});
	},
	
	/**
	 * 打回处理
	 */
	registerRejectClickEvent:function(){
		$('#btnReject').on('click',function(){
			//var name=$('#salesorderproductsrelname').val();
			var message = app.vtranslate('LBL_REJECT_CONFIRM_MESSAGE');
			Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
				function(e) {
					//参数设置
					var postData = {
						"module": app.getModuleName(),
						"action": "SaveAjax",
						"record": jQuery('#recordId').val(),
						"backwhy":jQuery('input[name=backwhy]').val(),
						"type":"reject"
					}
					//发送请求
					AppConnector.request(postData).then(
						function(data){
							//刷新页面
							window.location.reload();
						},
						function(error){
							console.log(error);
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
		this.registerAuditClickEvent();
		this.registerRejectClickEvent();
	}
});