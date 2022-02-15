/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("ReceivedPaymentsThrow_List_Js",{},{

	ChangeAccountCategory: function(recordId, $t_tr){
		var listInstance = Vtiger_List_Js.getInstance();
		var message = '确定要删除撤销回款匹配？';
		Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
			function(e) {
				var module = app.getModuleName();
				var postData = {
					"module": module,
					"action": "BasicAjax",
					"record": recordId
				}
				
				
				var Message = app.vtranslate('JS_RECORD_GETTING_');
				
				var progressIndicatorElement = jQuery.progressIndicator({
						'message' : Message,
						'position' : 'html',
						'blockInfo' : {'enabled' : true}
						});
				AppConnector.request(postData).then(
					function(data){
						progressIndicatorElement.progressIndicator({
									'mode' : 'hide'
								});
						if(data.success=='1') {
							
							$t_tr.remove();
						}
					},
					function(error,err){

					}
				);
			},
			function(error, err){
			}
		);
		
		
		
		
	},

	registerEvents : function(){
		this._super();
		//this.registerLoadAjaxEvent();
		this.ddd();
	},	
	ddd: function() {
		var thisInstance = this;
		var listViewContentDiv = this.getListViewContentContainer();
		listViewContentDiv.on('click','.deleteRow',function(e){ 
			var elem = jQuery(e.currentTarget);
			var $t_tr = elem.closest('tr');
			var recordId = elem.closest('tr').data('id');
			thisInstance.ChangeAccountCategory(recordId, $t_tr);
			e.stopPropagation();
		});
	}

});