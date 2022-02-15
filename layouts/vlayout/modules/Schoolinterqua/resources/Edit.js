/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("Schooladoptpeople_Edit_Js",{},{

	
	/**
	 * This function will register before saving any record
	 */
	registerRecordPreSaveEvent : function(form) {
		var thisInstance = this;
		if(typeof form == 'undefined') {
			form = this.getForm();
		}
		form.on(Vtiger_Edit_Js.recordPreSave, function(e, data) {
			
			
		});
	},
	setCheckReadonly: function(query) {
		$(query).attr('readonly', 'readonly');
		$(query).click(function() {
			return false;
		});
	},
	init: function() {
		$('input[name=assessmentdate]').attr('readonly','readonly');
		$('select[name=assessmentresult]').next().find('.chzn-results').remove(); 
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




















