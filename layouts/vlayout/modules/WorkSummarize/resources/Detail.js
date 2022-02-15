/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("WorkSummarize_Detail_Js",{
	
	registerRecordPreSaveEvent:function(){
		$('#replybutton').on('click',function(e){
			var content=$('#replycontent').val();
			var record=$('#recordId').val();
			var userid=$('#current_user_id').val();
			if(content==''){
				return ;
			}
			var postData = {
					"module": "WorkSummarize",
					"action": "SaveAjax",
					"record": record,
					"content":content,
					"userid":userid
			}
			AppConnector.request(postData).then(
					function(data){
						if(data.success) {
							var  params = {text : app.vtranslate('回复成功'),
							title : app.vtranslate('')}
							Vtiger_Helper_Js.showPnotify(params);
							window.location.reload();
						}
					},
					function(error,err){

					}
				);
		});
		
		
		
	
	},
	

	registerEvents : function() {
		this._super();
		
		this.registerRecordPreSaveEvent();
	}

});