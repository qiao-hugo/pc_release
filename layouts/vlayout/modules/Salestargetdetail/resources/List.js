/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("Salestargetdetail_List_Js",{},{


	registerEvents : function(){
		this._super();
		//this.registerLoadAjaxEvent();
		this.ddd();
	},	
	ddd: function() {
		$('#Salestargetdetail_listView_basicAction_LBL_ADD_RECORD').remove();
	}

});