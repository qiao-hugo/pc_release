/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("Schoolresume_Edit_Js",{},{

	
	/**
	 * This function will register before saving any record
	 */
	registerRecordPreSaveEvent : function(form) {
		var thisInstance = this;

		if(typeof form == 'undefined') {
			form = this.getForm();
		}
		form.on(Vtiger_Edit_Js.recordPreSave, function(e, data) {
			var shool_resume_source = $('select[name=shool_resume_source]').val();
			
			if(shool_resume_source == 'school_recruit') {
				//若来源是校招，则校招计划名称不可空白
				var schoolrecruitid = $.trim($('input[name=schoolrecruitid]').val());
				if (! schoolrecruitid) {
					var  params = {text : '校招计划名称不可为空', title : '错误提示'};
					Vtiger_Helper_Js.showPnotify(params);
					e.preventDefault();
				}
			}

			// 验证电话和邮箱
			var telephone = $('input[name=telephone]').val();
			if(!(/^1[34578]\d{9}$/.test(telephone))){ 
		        var  params = {text : '联系电话格式不正确', title : '错误提示'};
				Vtiger_Helper_Js.showPnotify(params);
		        e.preventDefault(); 
		    } 

		    var email = $('input[name=email]').val();
		    if(email) {
                if( ! (/^([a-zA-Z0-9_\\-\\.])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/.test(email))) {
		    		var  params = {text : '邮箱格式不正确', title : '错误提示'};
					Vtiger_Helper_Js.showPnotify(params);
		        	e.preventDefault();
		    	}
		    }

            var is_resume_qualified = $('input[name=is_resume_qualified]').attr('checked');
            if(is_resume_qualified){
                var entityposition = $('input[name=entityposition]').val();
                if( entityposition == "") {
                    var  params = {text : '招聘录取勾选时,录取职位必须填写!', title : '错误提示'};
                    Vtiger_Helper_Js.showPnotify(params);
                    e.preventDefault();
                }
            }

			/*var postData = {
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
			}*/
		});
	},
	init: function() {
		$('select[name=createuserid]').next().find('.chzn-results').remove(); // 填写人员不可修改
		var t_schoold_id = $('input[name=t_schoold_id]').val();
		var t_schoold_name = $('input[name=t_schoold_name]').val();
		if (t_schoold_id && t_schoold_name) {
			$('input[name=schoolid_display]').val(t_schoold_name);
			$('input[name=schoolid]').val(t_schoold_id);
		}
		// 勾选项都
		$('input[type=checkbox]:gt(0)').click(function () {
			return false;
		}).attr('readonly', 'readonly');
		//$('input[type=checkbox]').
	},

    /*registerIsResumeQualified:function () {
        $("#Schoolresume_editView_fieldName_is_resume_qualified").change(function() {
            var chk = $(this).attr('checked');
            if(chk){

            }else{

            }
        });
    },*/

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

		//this.registerIsResumeQualified();
    }
});




















