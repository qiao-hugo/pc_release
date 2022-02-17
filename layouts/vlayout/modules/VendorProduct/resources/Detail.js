/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("ProductProvider_Detail_Js",{
},{
	/**
	 * 跟进处理
	 */
	registerFollowClickEvent:function(){
		$('#ProductProvider_detailView_basicAction_LBL_UPDATERECEIVED').on('click',function(){
			var message = app.vtranslate('确定要重新提交码');
			Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
				function(e){
					//参数设置
					var postData = {
						"module": app.getModuleName(),
						"action": "ChangeAjax",
						"record": jQuery('#recordId').val(),
						"mode":"Resubmit"
					}
					//发送请求
					AppConnector.request(postData).then(
						function(data){

							//刷新页面
							window.location.reload();
						},
						function(error){
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
		this.registerFollowClickEvent();
	}
});