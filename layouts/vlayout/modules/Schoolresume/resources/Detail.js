/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("Schoolresume_Detail_Js",{
},{
	
	// 安排面试
	interview: function () {
		$('#Schoolresume_detailView_basicAction_LBL_RELATED_LEAD').click(function () {
				var msg = {
	                'message': '安排面试',
	                "width":"500px",
	            };
				Vtiger_Helper_Js.showConfirmationBox(msg).then(
					function(e) {
						var recordId = $('input[name=recordId]').val();
						var interviewdate = $.trim($('#interviewdate').val());
						var interviewer = $('#ddddd').val();
						var module = app.getModuleName();
						if (!interviewdate) {
							alert('计划面试时间不能为空');
							return false;
						}
						var postData = {
							"module": module,
							"action": "BasicAjax",
							"record": recordId,
							'mode': 'addInterviewdate',
							interviewdate : interviewdate,
							interviewer : interviewer,
						};

						var progressIndicatorElement = jQuery.progressIndicator({
								'message' : '正在提交...',
								'position' : 'html',
								'blockInfo' : {'enabled' : true}
								});
						AppConnector.request(postData).then(
							function(data){
								progressIndicatorElement.progressIndicator({
										'mode' : 'hide'
									});
								var  params = {text : '安排面试成功', title : '提示'};
									Vtiger_Helper_Js.showMessage(params);
							},
							function(error,err){}
						);
					},function(error, err){}
				);

				var status = $(this).attr('data-status');
				var ss = '';
				if (status == '是') {
					ss = 'checked';
				}
				var ss = '<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0"><tbody><tr><td class="fieldLabel medium"><label class="muted">计划面试官:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10">'+accessible_users+'</span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">计划面试时间:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input id="interviewdate" type="text" class="span9 dateField" name="interviewdate"></span></div></td></tr></tbody></table>';
				$('.modal-body').append(ss);
				//$.datetimepicker.setLocale('ch');
				$('#interviewdate').datetimepicker({
					format: "yyyy-mm-dd hh:ii",
					language:  'zh-CN',
			        autoclose: true,
			        todayBtn: true,
			        pickerPosition: "bottom-left",
			        showMeridian: 0,
		            startDate: new Date()
			    });
				$('#ddddd').select2();

		});


		$('#Schoolresume_detailView_basicAction_LBL_RELATED_LEAD_ADOPT').click(function() {
			var msg = {'message': '确定要加入人才库',"width":"500px"};
			Vtiger_Helper_Js.showConfirmationBox(msg).then(
				function(e) {
					var recordId = $('input[name=recordId]').val();
					var postData = {
						"module": app.getModuleName(),
						"action": "BasicAjax",
						"record": recordId,
						'mode': 'addPersonnel',
					};

					var progressIndicatorElement = jQuery.progressIndicator({
							'message' : '正在提交...',
							'position' : 'html',
							'blockInfo' : {'enabled' : true}
							});
					AppConnector.request(postData).then(
						function(data){
							progressIndicatorElement.progressIndicator({
									'mode' : 'hide'
								});
							var  params = {text : '加入人才库成功', title : '提示'};
								Vtiger_Helper_Js.showMessage(params);
						},
						function(error,err){}
					);
				}
			);
		});
	},

	registerEvents:function(){
		this._super();
		this.interview();
	}
});