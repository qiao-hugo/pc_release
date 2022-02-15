/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("Schoolrecruit_Detail_Js",{
},{
	flag : 0,
	init : function() {
		this.flag ++;
		if (this.flag == 1) {

			$('.make_schoolqualified').click(function() {

				var msg = {
	                'message': '生成合格简历名单',
	                "width":"500px",
	            };
	            var is_make_qualified = $('input[name=is_make_qualified]').val();

				Vtiger_Helper_Js.showConfirmationBox(msg).then(
					function(e) {
						var recordId = $('input[name=recordId]').val();
						var reportsdate = $('#Schoolrecruit_editView_date').val();
						var reportsower = $.trim($('#ddddd').val());
						var reportaddress = $('#reportaddress').val();
						
						var module = app.getModuleName();
						var postData = {
							"module": module,
							"action": "BasicAjax",
							"record": recordId,
							'mode': 'addSchoolqualified',
							reportsdate : reportsdate,
							reportsower : reportsower,
							reportaddress : reportaddress
							//"parent": app.getParentModuleName()
						};
						if (is_make_qualified != '1') {
							if(! (reportsdate && reportaddress)) {
								var  params = {text : app.vtranslate('请输入报道时间和报道地点'),
								title : app.vtranslate('')}
								Vtiger_Helper_Js.showPnotify(params);
								return false;
							}
						}

						//var Message = app.vtranslate('JS_RECORD_GETTING_'+type);
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
								if (data.success) {
									var  params = {text : '生成合格简历记录 ' + data.result.num + ' 条记录', title : '提示'};
									Vtiger_Helper_Js.showMessage(params);
								}
							},
							function(error,err){

							}
						);
					},function(error, err){}
				);
				if (is_make_qualified != '1') {
					var status = $(this).attr('data-status');
					var ss = '';
					if (status == '是') {
						ss = 'checked';
					}
					var ss = '<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0"><tbody><tr><td class="fieldLabel medium"><label class="muted">报道负责人:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10">'+accessible_users+'</span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">预计报道时间:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input id="Schoolrecruit_editView_date" type="text" class="span9 dateField" name="reportsdate"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">预计报道地点:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input id="reportaddress" type="text" class="span9 dateField" name="reportaddress"></span></div></td></tr></tbody></table>';
					$('.modal-body').append(ss);
					//$.datetimepicker.setLocale('ch');
					$('#Schoolrecruit_editView_date').datetimepicker({format: "yyyy-mm-dd hh:ii",
						language:  'zh-CN',
				        autoclose: true,
				        todayBtn: true,
				        pickerPosition: "bottom-left",
				        showMeridian: false,
				        format: "yyyy-mm-dd",
				        timepicker:false,
				        minView: "month",
				        forceParse:0,
			            startDate:new Date()});
					$('#ddddd').select2();
					$.fn.modal.Constructor.prototype.enforceFocus = function () {};
				}
				
			});
		}
	},
	
	registerEvents:function(){
		this.init();
		this._super();
		
	}
});