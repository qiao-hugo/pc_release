/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("UserManger_Detail_Js",{
},{
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

    changePassword:function(){
		$("#UserManger_detailView_basicAction_LBL_CHANGEPASSWORD").on("click",function(){
            var message='<h4>更改密码</h4><hr>';
            console.log("what");
            var msg={
                'message':message,
                "width":300,
                action:function(){
                    var new_password=$('input[name="new_password"]').val();
                    var confirm_password=$('input[name="confirm_password"]').val();
                    if(new_password==''){
                        Vtiger_Helper_Js.showPnotify('密码不能为空!');
                        return false;
                    }
                    if(new_password!=confirm_password){
                        Vtiger_Helper_Js.showPnotify('两次密码不一致!');
                        return false;
                    }
                    return true;
                }
            };
            var recordid=$('#recordId').val();
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
				var progressIndicatorElement = jQuery.progressIndicator({
					'message' : '努力处理中请稍等...',
					'position' : 'html',
					'blockInfo' : {'enabled' : true}
				});
                var new_password=$('input[name="new_password"]').val();
                var confirm_password=$('input[name="confirm_password"]').val();
                var params={};
                params['record'] = recordid;
                params['action'] = 'ChangeAjax';
                params['module'] = 'UserManger';
                params['mode'] = 'changePassword';
                params['new_password'] =new_password;
                params['confirm_password'] = confirm_password;
                AppConnector.request(params).then(
                    function(data) {
						progressIndicatorElement.progressIndicator({
							'mode' : 'hide'
						});
                        Vtiger_Helper_Js.showPnotify({text:data.result.message,type: 'success'});
                    },
                    function(error,err){

                    }
                );
            },function(error, err) {});
            $('.modal-content .modal-body').append('<div name="massEditContent"><div class="modal-body"><div class="control-group"></div><div class="control-group"><label class="control-label">新密码</label><div class="controls"><input type="password" name="new_password" data-validation-engine="validate[required]"></div></div><div class="control-group"><label class="control-label">确认新密码</label><div class="controls"><input type="password" name="confirm_password" data-validation-engine="validate[required]"></div></div></div></div>');
            $('.modal-content .modal-body').css({overflow:'hidden'});
		});
	},
	/**
	 * 更新微信状态
	 */
	updateWexinStatus:function(){
		$("#UserManger_detailView_basicAction_LBL_UPDATEWEXIN").on("click",function(){
			var message='<h4>确认要更新微信状态?</h4><hr>';
			var msg={
				'message':message,
				"width":600
			};
			var recordid=$('#recordId').val();
			Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
				var progressIndicatorElement = jQuery.progressIndicator({
					'message' : '努力处理中请稍等...',
					'position' : 'html',
					'blockInfo' : {'enabled' : true}
				});
				var params={};
				params['record'] = recordid;
				params['action'] = 'ChangeAjax';
				params['module'] = 'UserManger';
				params['mode'] = 'updateWexinInfo';
				AppConnector.request(params).then(
					function(data) {
						progressIndicatorElement.progressIndicator({
							'mode' : 'hide'
						});
						Vtiger_Helper_Js.showPnotify({text:'更新成功',type: 'success'});
					},
					function(error,err){

					}
				);
			},function(error, err) {});
		});
		$("#UserManger_detailView_basicAction_LBL_USERSTATUS").on("click",function(){
			var message='<h4>用户状态修改?</h4><hr>';
			var msg={
				'message':message,
				"width":400,
				action:function(){
					var cuserstatus=$('#cuserstatus').val();
					var cleavedate=$('#cleavedate').val();
					if(cuserstatus==2 || cuserstatus==3){
						if(cleavedate==''){
							Vtiger_Helper_Js.showPnotify('请填离职日期!');
							return false;
						}
					}
					return true;
				}
			};
			var recordid=$('#recordId').val();
			var userstatus=$('input[name="userstatus"]').val();
			var isdimission=$('input[name="isdimission"]').val();
			var leavedate=$('input[name="leavedate"]').val();
			if(userstatus=='Active'){
				var str='<div class="control-group"><label class="control-label">离职日期</label><div class="controls"><input type="text" id="cleavedate" name="cleavedate" value="'+leavedate+'" readonly></div></div>';
				var tempstr='';
				if(isdimission==1){
					tempstr+='<option value="1">还原</option>';
				}else{
					tempstr+='<option value="2">离职</option>';
				}
				tempstr+='<option value="3">禁用</option>';
				str+='<div class="control-group"><label class="control-label">用户状态</label><div class="controls"><select name="cuserstatus" id="cuserstatus">'+tempstr+'</select></div></div>';
			}else{
				var str='<div class="control-group"><label class="control-label">用户状态</label><div class="controls"><select name="cuserstatus" id="cuserstatus"><option value="5">启用</option></select></div></div>'
			}
			Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
				var cuserstatus=$('#cuserstatus').val();
				var cleavedate=$('#cleavedate').val();
				var params={};
				params['record'] = recordid;
				params['action'] = 'ChangeAjax';
				params['module'] = 'UserManger';
				params['mode'] = 'updateUserStatus';
				params['leavedate'] = cleavedate;
				params['userstatus'] = cuserstatus;
				var progressIndicatorElement = jQuery.progressIndicator({
					'message' : '努力处理中请稍等...',
					'position' : 'html',
					'blockInfo' : {'enabled' : true}
				});
				AppConnector.request(params).then(
					function(data) {
						progressIndicatorElement.progressIndicator({
							'mode' : 'hide'
						});
						if(data.success){
							Vtiger_Helper_Js.showPnotify({text:'更新成功',type: 'success'});
							window.location.reload();
						}else{
							Vtiger_Helper_Js.showPnotify({text:'请通过编辑进行修改',type: 'error'});
						}

					},
					function(error,err){

					}
				);
			},function(error, err) {});
			$('.modal-content .modal-body').append('<div name="massEditContent"><div class="modal-body"><div class="control-group"></div>'+str+'</div></div>');
			$('.modal-content .modal-body').css({overflow:'hidden'});
			$('#cleavedate').datetimepicker({
				format: "yyyy-mm-dd hh:ii:00",
				language:  'zh-CN',
				autoclose: true,
				todayBtn: true,
				pickerPosition: "bottom-right",
				showMeridian: 0,
				minuteStep:15

			});
		});
		$("#UserManger_detailView_basicAction_LBL_UPDATEYXT").on("click",function(){
			var message='<h4>确认要更新云学堂用户?</h4><hr>';
			var msg={
				'message':message,
				"width":600
			};
			var recordid=$('#recordId').val();
			Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
				var progressIndicatorElement = jQuery.progressIndicator({
					'message' : '努力处理中请稍等...',
					'position' : 'html',
					'blockInfo' : {'enabled' : true}
				});
				var params={};
				params['record'] = recordid;
				params['action'] = 'ChangeAjax';
				params['module'] = 'UserManger';
				params['mode'] = 'updateYXTInfo';
				AppConnector.request(params).then(
					function(data) {
						progressIndicatorElement.progressIndicator({
							'mode' : 'hide'
						});
						if(data.result.flag){
							var type='success';
							var title='更新成功';
						}else{
							var type='error';
							var title='更新失改';
						}
						Vtiger_Helper_Js.showPnotify({title : title,text:data.result.msg,type: type});
					},
					function(error,err){

					}
				);
			},function(error, err) {});
		});
	},
	removeuser:function(){
		$('.remove').click(function(){
			var recordIds = new Array();
			jQuery('.rmuser').each(function(index, checkBoxElement){
				var checkBoxJqueryObject = jQuery(checkBoxElement)
				if(checkBoxJqueryObject.is(":checked")){
					recordIds.push(checkBoxJqueryObject.val());
				}
			});

			if(recordIds.length<1){
				alert('没有任何选中');
				return;
			}
			var toid=$('#toid').val();


			if($.inArray(toid,recordIds)!=-1){
				alert('汇报对象不能为自己！');
				return;
			}
			var params = {
				'module': 'UserManger',
				'action': "ChangeAjax",
				'mode':'removeuser',
				'toid':toid,
				'removeuser': JSON.stringify(recordIds)
			}
			AppConnector.request(params).then(
				function(data) {
					if(data){
						for(i in recordIds){
							$('#user'+recordIds[i]).remove();
						}
						Vtiger_Helper_Js.showPnotify('人员转移成功');
					}else{
					}
				}
			);
		});

		$('.checkall').click(function(){
			if($(this).is(":checked")){
				$('.rmuser').attr('checked',true);
			}else{
				$('.rmuser').attr('checked',false);
			}
		})
	},
	registerEvents:function(){
		this._super();
		this.registerAuditClickEvent();
		this.registerRejectClickEvent();
		this.changePassword();
		this.updateWexinStatus();
		this.removeuser();
	}
});