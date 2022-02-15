/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("ServiceTask_Edit_Js", {} ,{

	registerRecordPreSaveEvent : function() {
		
		jQuery('#EditView').on(Vtiger_Edit_Js.recordPreSave, function(e, data) {
			//0:相对日期[上次完成后]，1：循环[激活后]
			var runconditiontype=jQuery("input[name='list']:checked").val();
			jQuery('#hidrunconditiontype').val(runconditiontype);
		})
	},
	
	registerEvents : function() {
		this._super();
		this.registerRecordPreSaveEvent();
	}
});


