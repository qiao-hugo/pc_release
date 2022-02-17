/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("Leads_List_Js",{},{

	ChangeLeadsCategory: function(recordId,type){
		var listInstance = Vtiger_List_Js.getInstance();
		var message = app.vtranslate('LBL_'+type+'_CONFIRMATION');
		Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
			function(e) {
				var module = app.getModuleName();
				var postData = {
					"module": module,
					"action": "BasicAjax",
					"record": recordId,
					'type':type,
					'mode':'changcategroy'
					//"parent": app.getParentModuleName()
				}
				
				
				var Message = app.vtranslate('JS_RECORD_GETTING');
				
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
						
						if(data.success) {
							//移除领取客户
							$('tr[data-id='+recordId+']').remove();
                            if(postData.type=='SELF'){
                                var message1 = app.vtranslate('领取成功,是否要继续领取?');
                                Vtiger_Helper_Js.showConfirmationBox({'message' : message1}).then(
                                    function(e){},function(e){location.href='/index.php?module=Leads&view=Detail&record='+postData.record}
                                 );
							}else{
								//window.location.reload();
							}
						} else {
							var  params = {
								text : app.vtranslate(data.message),
								title : app.vtranslate('JS_LBL_PERMISSION')
							}
							Vtiger_Helper_Js.showPnotify(params);
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

	registerChangeRecordClickEvent: function(){
		var thisInstance = this;
		var listViewContentDiv = this.getListViewContentContainer();
		listViewContentDiv.on('click','.ChangeRecordButton',function(e){ 
			var elem = jQuery(e.currentTarget);
			var recordId = elem.closest('tr').data('id');
			thisInstance.ChangeLeadsCategory(recordId,elem.attr('id'));
			e.stopPropagation();
		});
	},
    



registerEvents : function(){
	this._super();
	this.registerChangeRecordClickEvent();
}

});