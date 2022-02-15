/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("Approval_Detail_Js",{
	registerBasicEvents : function(container) {
		this._super(container);
		this.init();
	},

	init: function() {
		$('#Approval_detailView_basicAction_LBL_ADD_RECORD').remove();
		$('#Approval_detailView_basicAction_LBL_EDIT').remove();
	}
});