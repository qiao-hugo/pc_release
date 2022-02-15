/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("Schoolqualifiedpeople_Edit_Js",{},{

	
	/**
	 * This function will register before saving any record
	 */
	registerRecordPreSaveEvent : function(form) {
		var thisInstance = this;
		if(typeof form == 'undefined') {
			form = this.getForm();
		}
		form.on(Vtiger_Edit_Js.recordPreSave, function(e, data) {
			
			var is_train = $('#Schoolqualifiedpeople_editView_fieldName_is_train').attr('checked');
			if (is_train == 'checked') {

				var trainstartdate = $('#Schoolqualifiedpeople_editView_fieldName_trainstartdate').val();
				var trainenddate = $('#Schoolqualifiedpeople_editView_fieldName_trainenddate').val();
				if (!(trainstartdate && trainenddate)) {
					var  params = {text : app.vtranslate(' 培训开始时间和培训结束时间不能为空'),
					title : app.vtranslate('')}
					Vtiger_Helper_Js.showPnotify(params);
					e.preventDefault();
				}
			}
			// 	仅当培训合格=Y时，可点击“参与考核”按钮，填写考核主管（必填）。 Schoolqualifiedpeople_editView_fieldName_is_assessment
			var is_trainok = $('#Schoolqualifiedpeople_editView_fieldName_is_trainok').attr('checked');
			var assessmentuser = $('select[name=assessmentuser]').val();
			if (assessmentuser && is_trainok != 'checked') {
				var  params = {text : app.vtranslate(' 勾选培训合格才能填写考核人员'),
				title : app.vtranslate('')}
				Vtiger_Helper_Js.showPnotify(params);
				e.preventDefault();
			}

			
		});
	},
	setCheckReadonly: function(query) {
		$(query).attr('readonly', 'readonly');
		$(query).click(function() {
			return false;
		});
	},
	init: function() {
		var is_report = $('#Schoolqualifiedpeople_editView_fieldName_is_report').attr('checked');
		if (is_report == 'checked') {
			this.setCheckReadonly('#Schoolqualifiedpeople_editView_fieldName_is_report');
		} else {
			$('#Schoolqualifiedpeople_editView_fieldName_is_report').change(function() {
				var t = $(this).attr('checked');
				if (t == 'checked') {
					$('#Schoolqualifiedpeople_editView_fieldName_reportdate').val($('input[name=now_date]').val()).datetimepicker('update');
				} else {
					$('#Schoolqualifiedpeople_editView_fieldName_reportdate').val('').datetimepicker('update');
					$('#Schoolqualifiedpeople_editView_fieldName_is_train').attr('checked', false);
					$('#Schoolqualifiedpeople_editView_fieldName_is_trainok').attr('checked', false);
					$('#Schoolqualifiedpeople_editView_fieldName_is_assessment').attr('checked', false);

				}
			});
		}


		var is_train = $('#Schoolqualifiedpeople_editView_fieldName_is_train').attr('checked');
		if (is_train == 'checked') {
			this.setCheckReadonly('#Schoolqualifiedpeople_editView_fieldName_is_train');
		} else {
			//	仅当已报道=Y AND 已培训=N时，可点击“已培训”按钮，填写培训开始日期、培训结束日期；可勾选培训是否合格。
			$('#Schoolqualifiedpeople_editView_fieldName_is_train').click(function(){
				var this_checked = $(this).attr('checked');
				if (!this_checked) {
					$('#Schoolqualifiedpeople_editView_fieldName_is_trainok').attr('checked', false);
					$('#Schoolqualifiedpeople_editView_fieldName_is_assessment').attr('checked', false);
				}

				var is_report = $('#Schoolqualifiedpeople_editView_fieldName_is_report').attr('checked');
				
				if (is_report == 'checked') {
					return true;
				}	
				var  params = {text : app.vtranslate(' 已培训必须先勾选已报道'),
				title : app.vtranslate('')}
				Vtiger_Helper_Js.showPnotify(params);
				return false;
			});
		}
 
		var is_trainok = $('#Schoolqualifiedpeople_editView_fieldName_is_trainok').attr('checked');
		if (is_trainok == 'checked') {
			this.setCheckReadonly('#Schoolqualifiedpeople_editView_fieldName_is_trainok');
		} else {
			// 只有 勾选培训后 才会有培训合格
			$('#Schoolqualifiedpeople_editView_fieldName_is_trainok').click(function() {
				var this_checked = $(this).attr('checked');
				if (!this_checked) {
					$('#Schoolqualifiedpeople_editView_fieldName_is_assessment').attr('checked', false);
				}

				var is_train = $('#Schoolqualifiedpeople_editView_fieldName_is_train').attr('checked');
				if (is_train == 'checked') {
					return true;
				}
				var  params = {text : app.vtranslate(' 培训合格必须先勾选已培训'),
				title : app.vtranslate('')}
				Vtiger_Helper_Js.showPnotify(params);
				return false;
			});
		}

		var is_assessment = $('#Schoolqualifiedpeople_editView_fieldName_is_assessment').attr('checked');
		if (is_assessment == 'checked') {
			this.setCheckReadonly('#Schoolqualifiedpeople_editView_fieldName_is_assessment');
		} else {
			// 只有 勾选培训后 才会有培训合格
			$('#Schoolqualifiedpeople_editView_fieldName_is_assessment').click(function() {
				var is_trainok = $('#Schoolqualifiedpeople_editView_fieldName_is_trainok').attr('checked');
				if (is_trainok == 'checked') {
					return true;
				}
				var  params = {text : app.vtranslate(' 参与考核必须先勾选培训合格'),
				title : app.vtranslate('')}
				Vtiger_Helper_Js.showPnotify(params);
				return false;
			});
		}
	},

	/**
		pop two calander.
	*/
	registerBasicEvents:function(container){
		this._super(container);
		this.registerRecordPreSaveEvent(container);
		this.init();
	}
});




















