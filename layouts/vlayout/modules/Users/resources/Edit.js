
Vtiger_Edit_Js("Users_Edit_Js",{},{
	
	duplicateCheckCache : {},
	
	//Hold the conditions for a hour format
	hourFormatConditionMapping : false,
	
	
	registerWidthChangeEvent : function() {
		var widthType = app.cacheGet('widthType', 'narrowWidthType');
		jQuery('#currentWidthType').html(jQuery('li[data-class="'+widthType+'"]').html());
		jQuery('#widthType').on('click', 'li', function(e){
			var value = jQuery(e.currentTarget).data('class');
			app.cacheSet('widthType', value);
			jQuery('#currentWidthType').html(jQuery(e.currentTarget).html());
			window.location.reload();
		});
	},
	
	registerHourFormatChangeEvent : function() {
		
	},
	
	changeStartHourValuesEvent : function(form){
		var thisInstance = this;
		form.on('change','select[name="hour_format"]',function(e){
			var hourFormatVal = jQuery(e.currentTarget).val();
			var startHourElement = jQuery('select[name="start_hour"]',form);
			var conditionSelected = startHourElement.val();
			var list = thisInstance.hourFormatConditionMapping['hour_format'][hourFormatVal]['start_hour'];
			var options = '';
			for(var key in list) {
				//IE Browser consider the prototype properties also, it should consider has own properties only.
				if(list.hasOwnProperty(key)) {
					var conditionValue = list[key];
					options += '<option value="'+key+'"';
					if(key == conditionSelected){
						options += ' selected="selected" ';
					}
					options += '>'+conditionValue+'</option>';
				}
			}
			startHourElement.html(options).trigger("liszt:updated");
		});
		
		
	},
	
	triggerHourFormatChangeEvent : function(form) {
		this.hourFormatConditionMapping = jQuery('input[name="timeFormatOptions"]',form).data('value');
		this.changeStartHourValuesEvent(form);
		jQuery('select[name="hour_format"]',form).trigger('change');
	},
	
	/**
	 * Function to register recordpresave event
	 */
	registerRecordPreSaveEvent : function(form){
		var thisInstance = this;
		form.on(Vtiger_Edit_Js.recordPreSave, function(e, data) {
			var userName = jQuery('input[name="user_name"]').val();
			var newuserCode = jQuery('input[name="usercode"]').val();
			var newPassword = jQuery('input[name="user_password"]').val();
			var confirmPassword = jQuery('input[name="confirm_password"]').val();
			var isdimission = jQuery('input[name="isdimission"]:checked').val();
			var leavedate = jQuery('input[name="leavedate"]').val();
			var ustatus = jQuery('input[name="status"]').val();
			var record = jQuery('input[name="record"]').val();
			if(record == ''){
				if(newPassword != confirmPassword){
					Vtiger_Helper_Js.showPnotify(app.vtranslate('JS_REENTER_PASSWORDS'));
					e.preventDefault();
				}

				if(!(userName in thisInstance.duplicateCheckCache)) {
					thisInstance.checkDuplicateUser('user_name',userName).then(
						function(data){
							if(data.result) {
								thisInstance.duplicateCheckCache[userName] = data.result;
								Vtiger_Helper_Js.showPnotify('用户已存在');
								//return;
							}
						}, 
						function (data, error){
							thisInstance.duplicateCheckCache[userName] = data.result;
							form.submit();	
						}
					);
				} else {
					if(thisInstance.duplicateCheckCache[userName] == true){
						Vtiger_Helper_Js.showPnotify('用户已存在');
					} else {
						delete thisInstance.duplicateCheckCache[userName];
						return true;
					}
				}
				e.preventDefault();
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
		})
	},
	//ajax验证
	checkDuplicateUser: function(fieldtype,fieldvalue){
		var aDeferred = jQuery.Deferred();
		var params = {
				'module': app.getModuleName(),
				'action' : "SaveAjax",
				'mode' : 'userExists',
				'querytype':fieldtype,
				'fieldvalue' : fieldvalue
			}
			AppConnector.request(params).then(
				function(data) {
					if(data.result){
						aDeferred.resolve(data);
					}else{
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
			if(newusername!=user_name && newusername!=''){
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
	
	//增加选择按钮
	getNewcode:function(){
		jQuery('#getcode').on('click',function(e){
			app.showModalWindow("",'index.php?module=Users&view=Ajaxcode&mode=getcode', function() {
			
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
	//选中工号赋值
	codeChose:function(){
		
	},
	/**
	 pop two calander.
	 */
	calendarInit:function(){
		//enddate=new Date(date);
		var enddate=$('#Users_editView_fieldName_leavedate_in').data("edate");
		//enddate.setMinutes(enddate.getMinutes()+30);
		$('#Users_editView_fieldName_leavedate_in').datetimepicker({
			format: "yyyy-mm-dd hh:ii:00",
			language:  'zh-CN',
			autoclose: true,
			todayBtn: true,
			pickerPosition: "bottom-right",
			showMeridian: 0,
			minuteStep:15
		});
		//$("#VisitingOrder_editView_fieldName_enddate").val(endtime);
	},
	registerEvents : function() {
        this._super();
		this.codeChose();
		//用户工号修改为最大+1 2017/02/27 gaocl edit
		//$('input[name="usercode"]').after('<button id="getcode" type="button" class="btn btn-info">查询可用工号</button><input type="hidden" name="maxcode">');
       // $('input[name="usercode"]').attr("disabled",true);

		//this.getNewcode();
		var form = this.getForm();
		userCode = jQuery('input[name="usercode"]').val();
		user_name= jQuery('input[name="user_name"]').val();
		this.registerWidthChangeEvent();
		this.checkStr();
		//this.checkUsercode();
		this.checkUsername();
		this.triggerHourFormatChangeEvent(form);
		this.registerRecordPreSaveEvent(form);
		$('.listViewActionsDiv').show();
		$('#Users_editView_fieldName_fillinsales').prop("checked","checked");
		this.calendarInit()
	}
});
