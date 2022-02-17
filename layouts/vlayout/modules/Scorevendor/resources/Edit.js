/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("Scorevendor_Edit_Js",{},{

	init: function() {
		$('#Scorevendor_editView_fieldName_scoredate').attr('readonly', 'readonly');
		$('#Scorevendor_editView_fieldName_scoretotal').attr('readonly', 'readonly');
	},




	/**
		pop two calander.
	*/
	registerBasicEvents:function(container){
		this._super(container);
	}
});




















