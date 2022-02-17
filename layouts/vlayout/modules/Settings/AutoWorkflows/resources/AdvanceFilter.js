//wangibn 删除 Vtiger_AdvanceFilter_Js 方法

Vtiger_Field_Js('Workflows_Field_Js',{},{

	getUiTypeSpecificHtml : function() {
		var uiTypeModel = this.getUiTypeModel();
		return uiTypeModel.getUi();
	},

	getModuleName : function() {
		var currentModule = app.getModuleName();
		return currentModule;
	},

	/**
	 * Funtion to get the ui for the field  - generally this will be extend by the child classes to
	 * give ui type specific ui
	 * return <String or Jquery> it can return either plain html or jquery object
	 */
	getUi : function() {
		var html = '<input type="text" class="getPopupUi" name="'+ this.getName() +'"  /><input type="hidden" name="valuetype" value="'+this.get('workflow_valuetype')+'" />';
		html = jQuery(html);
		html.filter('.getPopupUi').val(app.htmlDecode(this.getValue()));
		return this.addValidationToElement(html);
	}
});

Vtiger_Date_Field_Js('Workflows_Date_Field_Js',{},{

	/**
	 * Function to get the user date format
	 */
	getDateFormat : function(){
		return this.get('date-format');
	},

	/**
	 * Function to get the ui
	 * @return - input text field
	 */
	getUi : function() {
		var comparatorSelectedOptionVal = this.get('comparatorElementVal');
        var dateSpecificConditions = this.get('dateSpecificConditions');
		if(comparatorSelectedOptionVal.length > 0) {
			if(comparatorSelectedOptionVal == 'between' || comparatorSelectedOptionVal == 'custom'){
				var html = '<div class="date"><input class="dateField" data-calendar-type="range" name="'+ this.getName() +'" data-date-format="'+ this.getDateFormat() +'" type="text" ReadOnly="true" value="'+  this.getValue() + '"></div>';
				var element = jQuery(html);
				return this.addValidationToElement(element);
			} else if(this._specialDateComparator(comparatorSelectedOptionVal)) {
				var html = '<input name="'+ this.getName() +'" type="text" value="'+this.getValue()+'" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-validator="[{"name":"PositiveNumber"}]">\n\
							<input type="hidden" name="valuetype" value="'+this.get('workflow_valuetype')+'" />';
				return jQuery(html);
			} else if (comparatorSelectedOptionVal in dateSpecificConditions) {
				var startValue = dateSpecificConditions[comparatorSelectedOptionVal]['startdate'];
				var endValue = dateSpecificConditions[comparatorSelectedOptionVal]['enddate'];
				var html = '<input name="'+ this.getName() +'"  type="text" ReadOnly="true" value="'+  startValue +','+ endValue +'">'
				return jQuery(html);
			} else if(comparatorSelectedOptionVal == 'is today') {
				//show nothing
			}else {
				return this._super();
			}
		} else {
			var html = '<input type="text" class="getPopupUi date" name="'+ this.getName() +'"  data-date-format="'+ this.getDateFormat() +'"  value="'+  this.getValue() + '" />'+ 
							'<input type="hidden" name="valuetype" value="'+this.get('workflow_valuetype')+'" />' 
			var element = jQuery(html); 
			return this.addValidationToElement(element);
		}
	},

	_specialDateComparator : function(comp) {
		var specialComparators = ['less than days ago', 'more than days ago', 'in less than', 'in more than', 'days ago', 'days later'];
		for(var index in specialComparators) {
			if(comp == specialComparators[index]) {
				return true;
			}
		}
		return false;
	}
});

Vtiger_Date_Field_Js('Workflows_Datetime_Field_Js',{},{
	/**
	 * Function to get the user date format
	 */
	getDateFormat : function(){
		return this.get('date-format');
	},

	/**
	 * Function to get the ui
	 * @return - input text field
	 */
	getUi : function() {
		var comparatorSelectedOptionVal = this.get('comparatorElementVal');
		if(this._specialDateTimeComparator(comparatorSelectedOptionVal)) {
			var html = '<input name="'+ this.getName() +'" type="text" value="'+this.getValue()+'" data-validator="[{name:PositiveNumber}]"><input type="hidden" name="valuetype" value="'+this.get('workflow_valuetype')+'" />';
			var element = jQuery(html);
		} else {
			var html = '<input type="text" class="getPopupUi date" name="'+ this.getName() +'"  data-date-format="'+ this.getDateFormat() +'"  value="'+  this.getValue() + '" />'+
						'<input type="hidden" name="valuetype" value="'+this.get('workflow_valuetype')+'" />'
			var element = jQuery(html);
		}
		return element;
	},

	_specialDateTimeComparator : function(comp) {
		var specialComparators = ['less than hours before', 'less than hours later', 'more than hours later', 'more than hours before'];
		for(var index in specialComparators) {
			if(comp == specialComparators[index]) {
				return true;
			}
		}
		return false;
	}
});

Vtiger_Currency_Field_Js('Workflows_Currency_Field_Js',{},{

	getUi : function() {
		var html = '<input type="text" class="getPopupUi marginLeftZero" name="'+ this.getName() +'" value="'+  this.getValue() + '"  />'+
					'<input type="hidden" name="valuetype" value="'+this.get('workflow_valuetype')+'" />';
		var element = jQuery(html);
		return this.addValidationToElement(element);
	}

});

Vtiger_Time_Field_Js('Workflows_Time_Field_Js',{},{

	/**
	 * Function to get the ui
	 * @return - input text field
	 */
	getUi : function() {
		var html = '<input type="text" class="getPopupUi time" name="'+ this.getName() +'"  value="'+  this.getValue() + '" />'+
					'<input type="hidden" name="valuetype" value="'+this.get('workflow_valuetype')+'" />';
		var element = jQuery(html);
		return this.addValidationToElement(element);
	}
});

Vtiger_Field_Js('Vtiger_Percentage_Field_Js',{},{

	/**
	 * Function to get the ui
	 * @return - input percentage field
	 */
	getUi : function() {
		var html = '<input type="text" class="getPopupUi" name="'+ this.getName() +'" value="'+  this.getValue() + '" />'+
					'<input type="hidden" name="valuetype" value="'+this.get('workflow_valuetype')+'" />';
		var element = jQuery(html);
		return this.addValidationToElement(element);
	}
});

Vtiger_Field_Js('Vtiger_Text_Field_Js',{},{

	/**
	 * Function to get the ui
	 * @return - input text field
	 */
	getUi : function() {
		var html = '<input type="text" class="getPopupUi" name="'+ this.getName() +'" value="'+  this.getValue() + '" />'+
					'<input type="hidden" name="valuetype" value="'+this.get('workflow_valuetype')+'" />';
		var element = jQuery(html);
		return this.addValidationToElement(element);
	}
});

Vtiger_Field_Js('Vtiger_Boolean_Field_Js',{},{

	/**
	 * Function to get the ui
	 * @return - input text field
	 */
	getUi : function() {
		var html = '<input type="text" class="getPopupUi boolean" name="'+ this.getName() +'" value="'+  this.getValue() + '" />'+
					'<input type="hidden" name="valuetype" value="'+this.get('workflow_valuetype')+'" />';
		var element = jQuery(html);
		return this.addValidationToElement(element);
	}
});

Vtiger_Owner_Field_Js('Workflows_Owner_Field_Js',{},{

    getUi : function() {
		var html = '<select class="row-fluid chzn-select" name="'+ this.getName() +'">';
		var pickListValues = this.getPickListValues();
		var selectedOption = this.getValue();
		for(var optGroup in pickListValues){
			html += '<optgroup label="'+ optGroup +'">'
			var optionGroupValues = pickListValues[optGroup];
			for(var option in optionGroupValues) {
				html += '<option value="'+option+'" ';
				if(option == selectedOption) {
					html += ' selected ';
				}
				html += '>'+optionGroupValues[option]+'</option>';
			}
			html += '</optgroup>'
		}

		html +='</select>';
		var selectContainer = jQuery(html);
		this.addValidationToElement(selectContainer);
		return selectContainer;
	}
});
