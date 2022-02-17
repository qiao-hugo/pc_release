/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("School_Edit_Js",{},{

	
	//区域选择空间 第三方集成 By Joe
	registerArea:function(){
		if(jQuery('#areadata').length>0){
			var area=jQuery('#areadata').attr('data');
			if(typeof area!='undefined'&& area.length>1){
				area=area.split('#');
				new PCAS("province","city","area",area[0],area[1],area[2]);
				jQuery('input[name=address]').val(area[3]);
			}else{
				new PCAS("province","city","area");
			}	
		}
	},
	/**
	 * This function will register before saving any record
	 */
	registerRecordPreSaveEvent : function(form) {
		var thisInstance = this;

		if(typeof form == 'undefined') {
			form = this.getForm();
		}
		form.on(Vtiger_Edit_Js.recordPreSave, function(e, data) {
			var schoolname = $('input[name=schoolname]').val();
			var recordId = $('input[name=record]').val();
			var postData = {
					"module": 'School',
					"action": "BasicAjax",
					"record": recordId,
					'mode': 'isCheckTow',
					'schoolname': schoolname
				};
			if (!thisInstance.flag) {
				AppConnector.request(postData).then(
					function(data){
						if(data.success) {
							var result = data['result'];
							if (result.is_check == 1) {
								var  params = {text : result.message, title : '错误提示'};
								Vtiger_Helper_Js.showPnotify(params);
							} else {
								thisInstance.flag = true;
								form.submit();
							}
						} else {
							return false;
						}
					},
					function(error,err){

					}
				);
				e.preventDefault();
			}
		});
	},
	init: function() {
		//$('select[name=chargeperson]').next().find('.chzn-results').remove(); // 填写人员不可修改
	},

	/**
		pop two calander.
	*/
	registerBasicEvents:function(container){
		this._super(container);
		this.registerArea();
		this.registerRecordPreSaveEvent(container);
		this.init();
	}
});




















