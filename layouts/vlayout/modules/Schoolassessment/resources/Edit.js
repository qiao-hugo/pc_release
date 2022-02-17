/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("Schoolrecruit_Edit_Js",{},{

	
	/**
	 * This function will register before saving any record
	 */
	registerRecordPreSaveEvent : function(form) {
		var thisInstance = this;

		if(typeof form == 'undefined') {
			form = this.getForm();
		}
		form.on(Vtiger_Edit_Js.recordPreSave, function(e, data) {
			var recruitname = $('input[name=recruitname]').val();
			var recordId = $('input[name=record]').val();
			
			var recruitmode = $('select[name=recruitmode]').val();
			if(recruitmode == 'network_recruit') {
				//若选择方式为网络校招，则报名开始日期、报名截止日期不可空白；
				var starttime = $.trim($('input[name=starttime]').val());
				var endtime = $.trim($('input[name=endtime]').val());
				if (! (starttime && endtime)) {
					var  params = {text : '开始日期、报名截止日期不可为空', title : '错误提示'};
					Vtiger_Helper_Js.showPnotify(params);
					e.preventDefault();
				}
			} else {
				//若选择方式为现场校招，则预计校招日期、预计校招地点不可空白。
				var estimate = $.trim($('input[name=estimate]').val());
				var recruitaddress = $.trim($('input[name=recruitaddress]').val());
				if (! (estimate && recruitaddress)) {
					var  params = {text : '预计校招日期、预计校招地点不可为空', title : '错误提示'};
					Vtiger_Helper_Js.showPnotify(params);
					e.preventDefault();
				}
			}

			var postData = {
					"module": 'Schoolrecruit',
					"action": "BasicAjax",
					"record": recordId,
					'mode': 'isCheckTow',
					'recruitname': recruitname
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
		$('select[name=createuserid]').next().find('.chzn-results').remove(); // 填写人员不可修改
		
	},

	registerReferenceSelectionEvent: function (container) {
        this._super(container);
        var thisInstance = this;

        //2015年4月24日 星期五 根据合同的客户负责人选择默认合同提单人 wangbin
        jQuery('input[name="schoolid"]', container).on(Vtiger_Edit_Js.referenceSelectionEvent, function (e, data) {
        	console.log(data);
            relatedchange(data);
        });

        function relatedchange(data) {
        	
            var sparams = {
                'module': 'Schoolrecruit',
                'action': 'BasicAjax',
                'schoolid': data.record,
                'mode': 'getCreateuser'
            };
            AppConnector.request(sparams).then(
                function (datas) {
                	//alert(datas.success);
                    if (datas.success == true) {
                    	var o = datas.result;
                    	var s = '';
                    	for(var i=0; i<o.length; i++) {
                    		s += '<option value="'+ o[i]['schoolcontactsname'] +'">'+o[i]['schoolcontactsname']+'</option>';
                    	}
                    	$('select[name=schoolcontacts]').html(s);
                    }
                }
            );
        }
    },

	/**
		pop two calander.
	*/
	registerBasicEvents:function(container){
		this._super(container);
		this.registerReferenceSelectionEvent(container);
		this.registerRecordPreSaveEvent(container);
		this.init();
	}
});




















