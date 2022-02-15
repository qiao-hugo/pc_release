/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("UserManger_Edit_Js",{},{
	ckEditorInstance:'',
	ckEInstance:'',
	olduser_name:'',
	isreduplicate:true,
	isemilreduplicate:true,
	rowSequenceHolder : false,
	checkDuplicateUser: function(fieldtype,fieldvalue){
		var thisInstance=this;
		var aDeferred = jQuery.Deferred();
		var params = {
			'module': app.getModuleName(),
			'action' : "ChangeAjax",
			'mode' : 'userExists',
			'querytype':fieldtype,
			'fieldvalue' : fieldvalue,
			'record':$('input[name="record"]').val()
		}
		//thisInstance.isreduplicate=true;
		//thisInstance.isemilreduplicate=true;
		AppConnector.request(params).then(
			function(data) {
				if(data.result){
					if(fieldtype=='user_name'){
						thisInstance.isreduplicate=true;
					}else if(fieldtype=='email1'){
						thisInstance.isemilreduplicate=true;
					}
					aDeferred.resolve(data);
				}else{
					if(fieldtype=='user_name'){
						thisInstance.isreduplicate=false;
					}else if(fieldtype=='email1'){
						thisInstance.isemilreduplicate=false;
					}
					aDeferred.reject(data);
				}
			}
		);
		return aDeferred.promise();
	},
	//过滤中文输入
	checkStr:function(){
		$('input[name="user_name"]').keyup(function(){
			var uname=$(this).val();
			$(this).val(uname.replace(/[\u4E00-\u9FA5]/g,''));
		});
	},
	//输入后验证工号唯一性 By Joe @20150416
	checkUsercode:function(){
		var thisInstance=this;
		$('input[name="usercode"]').blur(function(){
			var newuserCode=$(this).val();
			if(newuserCode!=userCode && newuserCode!=''){
				thisInstance.checkDuplicateUser('usercode',newuserCode).then(
					function(data){
						if(data.result) {
							$('input[name="usercode"]').val('').focus();
							Vtiger_Helper_Js.showPnotify('工号已被使用');
						}
					},
					function (data, error){
					}
				);
			}
		});
	},
	//输入用户登录名后验证唯一性 By Joe @20150424
	checkUsername:function(){
		var thisInstance=this;
		$('input[name="user_name"]').blur(function(){
			var newusername=$(this).val();
			if(newusername!=thisInstance.user_name && newusername!=''){
				thisInstance.checkDuplicateUser('user_name',newusername).then(
					function(data){
						if(data.result) {
							$('input[name="user_name"]').val('').focus();
							Vtiger_Helper_Js.showPnotify('登录名重复');
						}
					},
					function (data, error){
					}
				);
			}
		});

	},
	checkEmail:function(){
		var thisInstance=this;
		$('input[name="email1"]').blur(function(){
			var newusername=$(this).val();
			if(newusername!=thisInstance.user_name && newusername!=''){
				thisInstance.checkDuplicateUser('email1',newusername).then(
					function(data){
						if(data.result) {
							$('input[name="email1"]').val('').focus();
							Vtiger_Helper_Js.showPnotify('邮箱重复');
						}
					},
					function (data, error){
					}
				);
			}
		});
	},
	changeDepartmentid:function(){
		var thisInstance=this;
		$('#EditView').on('change','select[name="departmentid"]',function() {
			thisInstance.getRoles();
		});
	},
	getRoles:function(param){
		var flag=param||0;
		var params = {
			'module': app.getModuleName(),
			'action' : "ChangeAjax",
			'mode' : 'getRoles',
			'departmentid':$('select[name="departmentid"]').val()
		}
		var roleid=$('select[name="roleid"]').val();
		AppConnector.request(params).then(
			function(data) {
				var str='';
				$('select[name="roleid"]').empty();
				var tempcategory='';
				var dataLength=data.result.length-1;
				$.each(data.result,function(key,value){
					var selectstr='';
					if(value.id==roleid){
						selectstr=' selected';
					}
					if(key==0){
						str+='<optgroup label="'+value.categoryname+'">';
					}
					if(key!=0 && tempcategory!=value.category){
						str+=' </optgroup><optgroup label="'+value.categoryname+'">';
						tempcategory=value.category;
					}
					str+='<option value="'+value.id+'"'+selectstr+'>'+value.name+'</option>';
					if(key==dataLength){
						str+=' </optgroup>';
					}
				})
				$('select[name="roleid"]').append(str);

				$('select[name="roleid"]').trigger('liszt:updated');
			}
		);
	},
	initInstance:function(){
		var record=$('input[name="record"]').val();
		var thisInstance=this;
		if(record>0){
			thisInstance.getRoles(1);
		}
		$('input[name="user_name"]').keyup(function(){
			var uname=$(this).val();
			$(this).val(uname.replace(/[\u4E00-\u9FA5]/g,''));
		});
	},
	//增加选择按钮
	getNewcode:function(){
		jQuery('#getcode').on('click',function(e){
			app.showModalWindow("",'index.php?module=UserManger&view=Ajaxcode&mode=getcode', function() {

				jQuery('input[name="optionsRadios"]').on('change',function(e){
					//$('button[type="submit"]').removeAttr('disabled');
					var ucode=$(this).val();
					$('input[name="maxcode"]').val($('#maxcode').val());
					$('.blockUI').remove();

					$('input[name="usercode"]').val(ucode).blur();
				});
			});
		});
	},
	registerRecordPreSaveEvent : function(form){
		var thisInstance = this;
		form.on(Vtiger_Edit_Js.recordPreSave, function(e, data) {
			var newPassword = jQuery('input[name="user_password"]').val();
			var confirmPassword = jQuery('input[name="confirm_password"]').val();
			var isdimission = jQuery('input[name="isdimission"]:checked').val();
			var leavedate = jQuery('input[name="leavedate"]').val();
			var record = jQuery('input[name="record"]').val();
			if(record == ''){
				if(newPassword != confirmPassword){
					Vtiger_Helper_Js.showPnotify('两次密码不一致!');
					e.preventDefault();
				}
			}
			if(isdimission=='on' && leavedate==''){
				Vtiger_Helper_Js.showPnotify(app.vtranslate('离职日期必填！'));
				e.preventDefault();
				return false;
			}
			if(isdimission!='on' && leavedate!=''){
				Vtiger_Helper_Js.showPnotify(app.vtranslate('请不要填写离职日期！'));
				e.preventDefault();
				return false;
			}
			if(thisInstance.isreduplicate){
				var progressIndicatorElement = jQuery.progressIndicator({
					'message' : '正在验证用户信息...',
					'position' : 'html',
					'blockInfo' : {'enabled' : true}
				});
				var fieldvalue=$('input[name="user_name"]').val();
				var params = {
					'module': app.getModuleName(),
					'action' : "ChangeAjax",
					'mode' : 'userExists',
					'querytype':'user_name',
					'fieldvalue' : fieldvalue,
					'record':$('input[name="record"]').val()
				}
				thisInstance.isreduplicate=true;
				AppConnector.request(params).then(
					function(data) {
						progressIndicatorElement.progressIndicator({
							'mode' : 'hide'
						});
						if(data.result){
							thisInstance.isreduplicate=true;
							Vtiger_Helper_Js.showPnotify(app.vtranslate('用户名重复！'));
						}else{
							thisInstance.isreduplicate=false;
							$('.btn-success').trigger('click');
						}
					}
				);

				return false;
			}
			if(thisInstance.isemilreduplicate){
				var progressIndicatorElement = jQuery.progressIndicator({
					'message' : '正在验证用户邮箱信息是否重复...',
					'position' : 'html',
					'blockInfo' : {'enabled' : true}
				});
				var fieldvalue=$('input[name="email1"]').val();
				var params = {
					'module': app.getModuleName(),
					'action' : "ChangeAjax",
					'mode' : 'userExists',
					'querytype':'email1',
					'fieldvalue' : fieldvalue,
					'record':$('input[name="record"]').val()
				}
				thisInstance.isemilreduplicate=true;
				AppConnector.request(params).then(
					function(data) {
						progressIndicatorElement.progressIndicator({
							'mode' : 'hide'
						});
						if(data.result){
							thisInstance.isemilreduplicate=true;
							Vtiger_Helper_Js.showPnotify(app.vtranslate('邮箱重复！'));
						}else{
							thisInstance.isemilreduplicate=false;
							$('.btn-success').trigger('click');
						}
					}
				);
				return false;
			}

			/*if(record>0){
				if(!confirm("确定要修改提交吗?提交后审核完成后生效")){
					return false;
				}
			}*/
		});
	},
	/**
		pop two calander.
	*/
	registerBasicEvents:function(container){
		this._super(container);
		this.olduser_name= jQuery('input[name="user_name"]').val();
		userCode = jQuery('input[name="usercode"]').val();
		this.checkUsername();
		this.checkUsercode();
		this.checkEmail();
		this.changeDepartmentid();
		this.initInstance();
		this.getNewcode();
		this.registerRecordPreSaveEvent(container);
		$('input[name="leavedate"]').datetimepicker({
			format: "yyyy-mm-dd hh:ii:00",
			language:  'zh-CN',
			autoclose: true,
			pickerPosition: "bottom-right",
			showMeridian: 0,
			minuteStep:15

		});
	}
});




















