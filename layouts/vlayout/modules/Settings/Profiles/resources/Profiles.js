var Settings_Profiles_Js = {
	
	initEditView: function() {
		//隐藏或显示字段
		function toggleEditViewTableRow(e) {
			var target = jQuery(e.currentTarget);
			var container = jQuery('[data-togglecontent="'+ target.data('togglehandler') + '"]');
			var closestTrElement = container.closest('tr');
			
			if (target.find('i').hasClass('icon-chevron-down')) {
				closestTrElement.removeClass('hide');
				container.slideDown('slow');
				target.find('.icon-chevron-down').removeClass('icon-chevron-down').addClass('icon-chevron-up');
			} else {
				container.slideUp('slow',function(){
					closestTrElement.addClass('hide');
				});
				target.find('.icon-chevron-up').removeClass('icon-chevron-up').addClass('icon-chevron-down');
			}
		}
		
		function handleChangeOfPermissionRange(e, ui) {
			var target = jQuery(ui.handle);
			if (!target.hasClass('mini-slider-control')) {
				target = target.closest('.mini-slider-control');
			}
			var input  = jQuery('[data-range-input="'+target.data('range')+'"]');
			input.val(ui.value);
			target.attr('data-value', ui.value);
		}
		
		function handleModuleSelectionState(e) {
			var target = jQuery(e.currentTarget);
			var tabid  = target.data('value');
			var parent = target.closest('tr');
			if (target.attr('checked')) {
				//jQuery('[data-action-state]', parent).attr('checked', 'checked');
				//禁用显示字段按钮
				jQuery('[data-handlerfor]', parent).removeAttr('disabled');
			} else {
				//取消后关闭所有
				jQuery('[data-action-state]', parent).val(0);
				// Pull-up fields / tools details in disabled state.
				jQuery('[data-handlerfor]', parent).attr('disabled', 'disabled');
				jQuery('[data-togglecontent="'+tabid+'-fields"]').hide();
				jQuery('[data-togglecontent="'+tabid+'-tools"]').hide();
			}
		}
		//权限下拉逻辑验证
		function handleActionSelectionState(e) {
			var target = jQuery(e.currentTarget);
			var parent = target.closest('tr');
			var checked = target.val();
			if (target.data('action-state') == 'EditView' || target.data('action-state') == 'Delete') {
				if (checked) {
					jQuery('[data-action-state="DetailView"]', parent).attr('checked', 'checked');
					jQuery('[data-module-state]', parent).attr('checked', 'checked');
					jQuery('[data-handlerfor]', parent).removeAttr('disabled');
				}
			}
			//列表或详细选择
			if (target.data('action-state') == 'DetailView') {
				if (checked==0) {
				//取消则关闭模块权限
					jQuery('[data-action-state]', parent).removeAttr('checked');
					jQuery('[data-module-state]', parent).removeAttr('checked').trigger('change');
				} else {
					jQuery('[data-module-state]', parent).attr('checked', 'checked');
					jQuery('[data-handlerfor]', parent).removeAttr('disabled');
				}
			}
		}
		
		function selectAllModulesViewAndToolPriviliges(e) {
			var target = jQuery(e.currentTarget);
			var checked = target.val();
			jQuery('#mainAction4CheckBox').val(checked);
			jQuery('#mainModulesCheckBox').attr('checked','checked');
			jQuery('.modulesCheckBox').attr('checked','checked');
			jQuery('.action4CheckBox').val(checked);
			jQuery('[data-handlerfor]').removeAttr('disabled');
		}
		//模块权限点击事件
		jQuery('[data-module-state]').change(handleModuleSelectionState);
		//视图权限下拉事件
		jQuery('[data-action-state]').change(handleActionSelectionState);
		//创建编辑或删除
		jQuery('#mainAction1CheckBox,#mainAction2CheckBox').change(selectAllModulesViewAndToolPriviliges);
		//字段显示按钮
		jQuery('[data-togglehandler]').click(toggleEditViewTableRow);
		jQuery('[data-range]').each(function(index, item) {
			item = jQuery(item);
			var value = item.data('value');
			item.slider({
				min: 0,
				max: 2,
				value: value,
				//disabled: item.data('locked'),
				slide: handleChangeOfPermissionRange
			});
		});	
		
		//fix for IE jQuery UI slider
		jQuery('[data-range]').find('a').css('filter','');

	},
	
	registerSelectAllModulesEvent : function() {
		var moduleCheckBoxes = jQuery('.modulesCheckBox');
		var viewAction = jQuery('#mainAction4CheckBox');
		var createAction = jQuery('#mainAction1CheckBox');
		var deleteACtion = jQuery('#mainAction2CheckBox');
		var mainModulesCheckBox = jQuery('#mainModulesCheckBox');
		mainModulesCheckBox.on('change',function(e) {
			var mainCheckBox = jQuery(e.currentTarget);
			if(mainCheckBox.is(':checked')){
				moduleCheckBoxes.attr('checked',true);
				viewAction.attr('checked',true);
				createAction.show().attr('checked',true);
				deleteACtion.show().attr('checked',true);
				moduleCheckBoxes.trigger('change');
			} else {
				moduleCheckBoxes.attr('checked',false);
				moduleCheckBoxes.trigger('change');
				viewAction.attr('checked',false);
				createAction.attr('checked', false);
				deleteACtion.attr('checked', false);
			}
		});
		
		moduleCheckBoxes.on('change',function(){
			Settings_Profiles_Js.checkSelectAll(moduleCheckBoxes,mainModulesCheckBox);
			Settings_Profiles_Js.checkSelectAll(jQuery('.action4CheckBox'),viewAction);
			Settings_Profiles_Js.checkSelectAll(jQuery('.action1CheckBox'),createAction);
			Settings_Profiles_Js.checkSelectAll(jQuery('.action2CheckBox'),deleteACtion);
		});
	},
	
	//下拉全选
	registerSelectAllViewActionsEvent : function() {
		var viewActionCheckBoxes = jQuery('.action4CheckBox');
		var mainViewActionCheckBox = jQuery('#mainAction4CheckBox');
		var modulesMainCheckBox = jQuery('#mainModulesCheckBox');
		
		mainViewActionCheckBox.on('change',function(e){
			var mainCheckBox = jQuery(e.currentTarget);
			
				viewActionCheckBoxes.val(mainCheckBox.val());
			 if(mainCheckBox.val()!=0){
				modulesMainCheckBox.attr('checked',true);
				modulesMainCheckBox.trigger('change');
			} else {
				modulesMainCheckBox.attr('checked',false);
				modulesMainCheckBox.trigger('change');
			}
		});
		
		/* viewActionCheckBoxes.on('change',function() {
			Settings_Profiles_Js.checkSelectAll(viewActionCheckBoxes,mainViewActionCheckBox);
		}); */
		
	},
	
	registerSelectAllCreateActionsEvent : function() {
		var createActionCheckBoxes = jQuery('.action1CheckBox');
		var mainCreateActionCheckBox =  jQuery('#mainAction1CheckBox');
		mainCreateActionCheckBox.on('change',function(e){
			var mainCheckBox = jQuery(e.currentTarget);
			//if(mainCheckBox.is(':checked')){
				createActionCheckBoxes.val(mainCheckBox.val());
			/* } else {
				createActionCheckBoxes.attr('checked',false);
			} */
		});
		/* createActionCheckBoxes.on('change',function() {
			Settings_Profiles_Js.checkSelectAll(createActionCheckBoxes,mainCreateActionCheckBox);
		}); */
		
	},
	
		registerSelectAllPopupActionsEvent : function() {
		var popupActionCheckBoxes = jQuery('.action11CheckBox');
		var mainPopupActionCheckBox =  jQuery('#mainAction11CheckBox');
		mainPopupActionCheckBox.on('change',function(e){
			var mainCheckBox = jQuery(e.currentTarget);
			//if(mainCheckBox.is(':checked')){
				popupActionCheckBoxes.val(mainCheckBox.val());
			/* } else {
				createActionCheckBoxes.attr('checked',false);
			} */
		});
		/* createActionCheckBoxes.on('change',function() {
			Settings_Profiles_Js.checkSelectAll(createActionCheckBoxes,mainCreateActionCheckBox);
		}); */
		
	},
	
	registerSelectAllDeleteActionsEvent : function() {
		var deleteActionCheckBoxes = jQuery('.action2CheckBox');
		var mainDeleteActionCheckBox =  jQuery('#mainAction2CheckBox');
		mainDeleteActionCheckBox.on('change',function(e){
			var mainCheckBox = jQuery(e.currentTarget);
			//if(mainCheckBox.is(':checked')){
				deleteActionCheckBoxes.val(mainCheckBox.val());
			/* } else {
				deleteActionCheckBoxes.attr('checked',false);
			} */
		});
		deleteActionCheckBoxes.on('change',function() {
			Settings_Profiles_Js.checkSelectAll(deleteActionCheckBoxes,mainDeleteActionCheckBox);
		});
	},

	checkSelectAll : function(checkBoxElement,mainCheckBoxElement){
		var state = true;
		if(typeof checkBoxElement == 'undefined' || typeof mainCheckBoxElement == 'undefined'){
			return false;
		}
		checkBoxElement.each(function(index,element){
			if(jQuery(element).is(':checked')){
				state = true;
			}else{
				state = false;
				return false;
			}
		});
		if(state == true){
			mainCheckBoxElement.attr('checked',true);
		} else {
			mainCheckBoxElement.attr('checked', false);
		}
	},
	
	performSelectAllActionsOnLoad : function() {
		if(jQuery('[data-module-unchecked]').length > 0){
			jQuery('#mainModulesCheckBox').attr('checked',false);
		}
        
		if(jQuery('[data-action4-unchecked]').length <= 0){
			jQuery('#mainAction4CheckBox').attr('checked',true);
		}
		if(jQuery('[data-action1-unchecked]').length <= 0) {
			jQuery('#mainAction1CheckBox').attr('checked',true);
		}
		if(jQuery('[data-action2-unchecked]').length > 0) {
			jQuery('#mainAction2CheckBox').attr('checked',false);
		}
	}, 
	
	registerSubmitEvent : function() {
		var thisInstance = this;
		var form = jQuery('[name="EditProfile"]');
		form.on('submit',function(e) {
			if(form.data('submit') == 'true' && form.data('performCheck') == 'true') {
				return true;
			} else {
				if(form.data('jqv').InvalidFields.length <= 0) {
					var formData = form.serializeFormData();
					thisInstance.checkDuplicateName({
						'profileName' : formData.profilename,
						'profileId' : formData.record
					}).then(
						function(data){
							form.data('submit', 'true');
							form.data('performCheck', 'true');
							form.submit();
						},
						function(data, err){
							var params = {};
							params['text'] = data['message'];
							params['type'] = 'error';
							Settings_Vtiger_Index_Js.showMessage(params);
							return false;
						}
					);
				} else {
					//If validation fails, form should submit again
					form.removeData('submit');
					// to avoid hiding of error message under the fixed nav bar
					app.formAlignmentAfterValidation(form);
				}
				e.preventDefault();
			}
		})
	},
	
	//配置名称不重复
	checkDuplicateName : function(details) {
		var profileName = details.profileName;
		var recordId = details.profileId;
		var aDeferred = jQuery.Deferred();
		var params = {
		'module' : app.getModuleName(),
		'parent' : app.getParentModuleName(),
		'action' : 'EditAjax','mode' : 'checkDuplicate',
		'profilename' : profileName,
		'record' : recordId
		}
		AppConnector.request(params).then(
			function(data) {
				var response = data['result'];
				var result = response['success'];
				if(result == true) {
					aDeferred.reject(response);
				} else {
					aDeferred.resolve(response);
				}
			},
			function(error,err){
				aDeferred.reject();
			}
		);
		return aDeferred.promise();
	},
	//显示和编辑所有模块
	registerGlobalPermissionActionsEvent : function() {
		var editAllAction = jQuery('[name="editall"]').filter(':checkbox');
		var viewAllAction = jQuery('[name="viewall"]').filter(':checkbox');
		if(editAllAction.is(':checked')){viewAllAction.attr('readonly','readonly');}//可编辑必可显示
		viewAllAction.on('change', function(e) {
			var currentTarget = jQuery(e.currentTarget);
			if(currentTarget.attr('readonly') == 'readonly') {
				var status = jQuery(e.currentTarget).is(':checked');
				if(!status){
					jQuery(e.currentTarget).attr('checked','checked')
				}else{
					jQuery(e.currentTarget).removeAttr('checked');
				}
				e.preventDefault();
			}
		})
		editAllAction.on('change', function(e) {
			var currentTarget = jQuery(e.currentTarget);
			if(currentTarget.is(':checked')) {
				viewAllAction.attr('checked', 'checked');
				viewAllAction.attr('readonly', 'readonly');
			} else {
				viewAllAction.removeAttr('readonly');
			}
		})
	},
	
	registerEvents : function() {
		Settings_Profiles_Js.initEditView();
		Settings_Profiles_Js.registerSelectAllModulesEvent();
		Settings_Profiles_Js.registerSelectAllViewActionsEvent();
		Settings_Profiles_Js.registerSelectAllCreateActionsEvent();
		Settings_Profiles_Js.registerSelectAllDeleteActionsEvent();
		Settings_Profiles_Js.registerSelectAllPopupActionsEvent();
		Settings_Profiles_Js.performSelectAllActionsOnLoad();
		Settings_Profiles_Js.registerSubmitEvent();
		Settings_Profiles_Js.registerGlobalPermissionActionsEvent();
	}
	
}
jQuery(document).ready(function(){
	Settings_Profiles_Js.registerEvents();
})
