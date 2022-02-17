/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("WorkSummarize_Edit_Js", {} ,{
	registerRecordPreSaveEvent : function() {
		if($('input[name="record"]').val()==""){
			var user_last_name=$('input[name="user_last_name"]').val();
			$('#WorkSummarize_editView_fieldName_worksummarizename').val(user_last_name);
		}
		
	},
	registerEvents : function() {
		this._super();
		//this.registerEventForChose();
		this.registerRecordPreSaveEvent();
	}
});


