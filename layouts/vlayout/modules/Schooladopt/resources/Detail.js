/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("Schooladopt_Detail_Js",{
},{
	convertSchoolqualified: function() {
		$('#Schooladopt_detailView_basicAction_LBL_RELATED_LEAD').click(function () {
				var msg = {
	                'message': '确定要生成实训名单',
	                "width":"500px",
	            };

				Vtiger_Helper_Js.showConfirmationBox(msg).then(
					function(e) {
						var recordId = $('input[name=recordId]').val();
						//var assessownerid = $.trim($('#ddddd').val());
						//var remarks = $('#schoolpractical_remarks').val();
						var module = app.getModuleName();
						var postData = {
							"module": module,
							"action": "BasicAjax",
							"record": recordId,
							'mode': 'addSchoolpractical',
							//assessownerid : assessownerid,
							//remarks : remarks
						};

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
									var  params = {text : '生成实训人员记录 ' + data.result.num + ' 条记录', title : '提示'};
									Vtiger_Helper_Js.showMessage(params);
								}
							},
							function(error,err){

							}
						);
					},function(error, err){}
				);

				//var ss = '<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0"><tbody><tr><td class="fieldLabel medium"><label class="muted">实训教官:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10">'+accessible_users+'</span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">备注信息:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><textarea name="schoolpractical_remarks" id="schoolpractical_remarks"></textarea></span></div></td></tr></tbody></table>';
				//$('.modal-body').append(ss);
				//$('#ddddd').select2();
		});
	},
	init: function() {
		$('#Schooladopt_detailView_basicAction_LBL_ADD_RECORD').remove();
	},
	registerEvents:function(){
		this._super();
		this.convertSchoolqualified();
		this.init();
	}
});