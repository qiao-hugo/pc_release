/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("Schoolpracticalpeople_List_Js",{},{
	set_assessmentresult: function (e) {
		$('.set_assessmentresult').click(function () {
			var msg = {
                'message': '修改实训结果',
                "width":"400px",
            };
			
			var $select_tr = $(this).closest('tr');
			var recordId = $select_tr.data('id');
			var me = this;
			Vtiger_Helper_Js.showConfirmationBox(msg).then(
				function(e) {
					var selectValue = $('#receivedstatus').val();
					var module = app.getModuleName();
					var postData = {
						"module": module,
						"action": "BasicAjax",
						"record": recordId,
						'status': selectValue,
						'mode': 'setAssessmentresult',
						'assessownerid' : $('#ddddd').val()
						//"parent": app.getParentModuleName()
					}
					
					var Message = app.vtranslate('正在请求...');
					
					var progressIndicatorElement = jQuery.progressIndicator({
							'message' : Message,
							'position' : 'html',
							'blockInfo' : {'enabled' : true}
							});
					AppConnector.request(postData).then(
						function(data){
							progressIndicatorElement.progressIndicator({'mode' : 'hide'});
							if (data.success) {
								var t = {'assessmentresult_yes': '通过', 'assessmentresult_no': '未通过'};
								$select_tr.find('.assessmentresult_value').html(t[selectValue]);

								$(me).attr('data-status', selectValue);
								//alert('更新合同自动关闭状态成功');
								var  params = {text : '更改实训结果', title : '提示'};
								Vtiger_Helper_Js.showMessage(params);
							} else {
								var  params = {text : '更改实训结果失败', title : '提示'};
								Vtiger_Helper_Js.showMessage(params);
							}


								//location.reload();
							
						},
						function(error,err){}
					);
				},function(error, err){}
			);

			var str = '';
			var temp_str = {
				'assessmentresult_yes': '通过',
				'assessmentresult_no': '未通过',
			};
			var status = $(this).attr('data-status');
			for(var index in temp_str) {
				if (index == status) {
					str += '<option selected="selected" value="'+ index +'">'+ temp_str[index] +'</option>';
				} else {
					str += '<option value="'+ index +'">'+ temp_str[index] +'</option>';
				}
			}
			var sss = '<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">实训结果:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><select id="receivedstatus">'+str+'</select></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">人事负责人:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10">'+accessible_users+'</span></div></td></tr></tbody></table>';
			$('.modal-body').append(sss);
			$('#ddddd').select2();
			$.fn.modal.Constructor.prototype.enforceFocus = function () {};
			//$('.modal-body').append('<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0;"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">实训结果:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><select id="receivedstatus">'+str+'</span></div></td></tr></tbody></table>');
		});
	},
	init : function () {
		$('#Schoolpracticalpeople_listView_basicAction_LBL_ADD_RECORD').hide();
	},
	registerEvents : function(){
		this._super();
		this.set_assessmentresult();
		this.init();
	}

});