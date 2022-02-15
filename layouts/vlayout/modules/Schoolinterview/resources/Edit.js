/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("Schoolinterview_Edit_Js",{},{

	init :  function() {
		$('#Schoolinterview_editView_fieldName_planinterviewdate').datetimepicker({
			format: "yyyy-mm-dd hh:ii",
			language:  'zh-CN',
	        autoclose: true,
	        todayBtn: true,
	        pickerPosition: "bottom-left",
	        showMeridian: 0,
            startDate:new Date()
	    });
        $('#Schoolinterview_editView_fieldName_nextinterviewdate').datetimepicker({
			format: "yyyy-mm-dd hh:ii",
			language:  'zh-CN',
	        autoclose: true,
	        todayBtn: true,
	        pickerPosition: "bottom-left",
	        showMeridian: 0,
            startDate: new Date()
	    });



	    $('#Schoolinterview_editView_fieldName_planinterviewdate').attr('disabled', 'disabled');
		$('select[name=planinterviewer]').next().find('.chzn-results').remove(); 
	},

	registerRecordPreSaveEvent : function(form) {
		var thisInstance = this;
		if(typeof form == 'undefined') {
			form = this.getForm();
		}
		form.on(Vtiger_Edit_Js.recordPreSave, function(e, data) {

			//
			var interviewresult = $('select[name=interviewresult]').val();
			if (interviewresult == 'c_goto') {
				var nextinterviewer = $('select[name=nextinterviewer]').val();
				var nextinterviewdate = $('#Schoolinterview_editView_fieldName_nextinterviewdate').val();
				if ( !(nextinterviewer && nextinterviewdate) ) {
					var  params = {text : app.vtranslate('下一轮面试官和下一轮面试时间不能为空'),
							title : app.vtranslate('提示')}
						Vtiger_Helper_Js.showPnotify(params);
					e.preventDefault();
				}
			} else if( interviewresult == 'c_employ' ) { // ；录用
				var trainowner = $('select[name=trainowner]').val();
				if (!trainowner) {
					var  params = {text : app.vtranslate('培训部负责人不能为空'),
							title : app.vtranslate('提示')}
						Vtiger_Helper_Js.showPnotify(params);
					e.preventDefault();
				}
			}



			/*var params = {};
            if(!(accountName in thisInstance.duplicateCheckCache)) {
                Vtiger_Helper_Js.checkDuplicateName({
                    'accountName' : accountName, 
                    'recordId' : recordId,
                    'moduleName' : 'Accounts'
                }).then(
                    function(data){
                        thisInstance.duplicateCheckCache[accountName] = data['success'];
                        form.submit();
                    },
                    function(data, err){
                        thisInstance.duplicateCheckCache[accountName] = data['success'];
                        thisInstance.duplicateCheckCache['message'] = data['message'];
						//var message = app.vtranslate('');
						var  params = {text : app.vtranslate(data.message),
								title : app.vtranslate('JS_DUPLICTAE_CREATION_CONFIRMATION')}
							Vtiger_Helper_Js.showPnotify(params);
                    }
				);
            }else {
				if(thisInstance.duplicateCheckCache[accountName] == true){
					var params = {text : thisInstance.duplicateCheckCache['message'],
					title : app.vtranslate('JS_DUPLICTAE_CREATION_CONFIRMATION')}
					Vtiger_Helper_Js.showPnotify(params);
				} else {
					delete thisInstance.duplicateCheckCache[accountName];
					return true;
				}
			}
            e.preventDefault();*/
		});
	},

	/**
		pop two calander.
	*/
	registerBasicEvents:function(container){
		this._super(container);
		this.registerRecordPreSaveEvent(container);
	}
});




















