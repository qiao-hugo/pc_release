/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("Schoolemploy_Edit_Js",{},{

	
	init: function() {
		$('select[name=assessownerid]').next().find('.chzn-results').remove(); // 填写人员不可修改
		$('select[name=shool_resume_source]').next().find('.chzn-results').remove(); 
	},

	/**
		pop two calander.
	*/
	registerBasicEvents:function(container){
		this._super(container);
		this.init();
	}
});




















