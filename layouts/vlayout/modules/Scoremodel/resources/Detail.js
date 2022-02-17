/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("School_Detail_Js",{
},{
	/**
	 * 跟进处理
	 */
	registerFollowClickEvent:function(){
		$('#btnFollow').on('click',function(){
			var message = app.vtranslate('JS_LBL_FOLLOW_CONFIRM_MESSAGE');
			Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
				function(e) {
					//参数设置
					var postData = {
						"module": app.getModuleName(),
						"action": "SaveAjax",
						"record": jQuery('#recordId').val(),
						"type":"followup"
					}
					//发送请求
					AppConnector.request(postData).then(
						function(data){
							if(data.success ==  true && data.result[0] =="followup"){
								var message = app.vtranslate('JS_NO_PASS_MESSAGE');
								var params = {
									text: message,
									type: 'notice'
								};
								Vtiger_Helper_Js.showMessage(params);
								return;
							}
							//刷新页面
							window.location.reload();
						},
						function(error){
							alert(error);
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
	 * 审核处理
	 */
	registerAuditClickEvent:function(){
		$('#btnAudit').on('click',function(){
			var message = app.vtranslate('JS_LBL_AUDITOR_CONFIRM_MESSAGE');
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
							if(data.success ==  true && data.result[0] =="followup"){
								var message = app.vtranslate('JS_FOLLOWUP_MESSAGE');
								var params = {
									text: message,
									type: 'notice'
								};
								Vtiger_Helper_Js.showMessage(params);
								return;
							}
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
	
	/**
	 * 拒绝处理
	 */
	registerRejectClickEvent:function(){
		$('#btnReject').on('click',function(){
			var message = app.vtranslate('JS_LBL_REJECT_CONFIRM_MESSAGE');
			Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
				function(e) {
					//参数设置
					var postData = {
						"module": app.getModuleName(),
						"action": "SaveAjax",
						"record": jQuery('#recordId').val(),
						//"backwhy":jQuery('input[name=backwhy]').val(),
						"type":"reject"
					}
					//发送请求
					AppConnector.request(postData).then(
						function(data){
							if(data.success ==  true && data.result[0] =="followup"){
								var message = app.vtranslate('JS_FOLLOWUP_MESSAGE');
								var params = {
									text: message,
									type: 'notice'
								};
								Vtiger_Helper_Js.showMessage(params);
								return;
							}
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
	detailViewSaveSchoolComment: function(){

		var detailContentsHolder = this.getContentHolder();
		detailContentsHolder.on('click','.detailViewSaveSchoolComment', function(e){
			var schoolid = $('input[name=schoolid]').val();
			var smodcommentpurpose = $('select[name=smodcommentpurpose]').val();
			var smodcommentcontacts = $('select[name=smodcommentcontacts]').val();
			var scommentcontents = $('textarea[name=scommentcontents]').val();
			var smodcommenttype = $('select[name=smodcommenttype]').val();
			var smodcommentmode = $('select[name=smodcommentmode]').val();

			var schoolid = $('input[name=schoolid]').val();

			if (!$.trim(scommentcontents)) {
				var  params = {text : '跟进内容不能为空', title : '错误提示'};
				Vtiger_Helper_Js.showPnotify(params);
				return false;
			}

			//参数设置
			var postData = {
				"module": 'Schoolcomments',
				"action": "SaveAjax",
				"record": jQuery('#recordId').val(),
				"schoolid": schoolid,
				smodcommentmode : smodcommentmode,
				smodcommenttype : smodcommenttype,
				scommentcontents : scommentcontents,
				smodcommentcontacts : smodcommentcontacts,
				smodcommentpurpose : smodcommentpurpose
			}


			// 遮罩层
			var progressIndicatorElement = jQuery.progressIndicator({
						'message' : '正在提交...',
						'position' : 'html',
						'blockInfo' : {'enabled' : true}
						});

			//发送请求
			AppConnector.request(postData).then(
				function(data){
					progressIndicatorElement.progressIndicator({
						'mode' : 'hide'
					});

					if(data.success ==  true){
						var message = app.vtranslate('跟进成功');
						var params = {
							text: message,
							type: 'notice'
						};
						Vtiger_Helper_Js.showMessage(params);
						setTimeout(function() {
							window.location.reload();
						}, 500)
					}
					//刷新页面
					
					return false;
				},
				function(error){
					console.log(error);
				}
			);
			return false;
		});

	},
	
	registerEvents:function(){
		this._super();
		this.detailViewSaveSchoolComment();
		this.registerFollowClickEvent();
		this.registerAuditClickEvent();
		this.registerRejectClickEvent();
	}
});