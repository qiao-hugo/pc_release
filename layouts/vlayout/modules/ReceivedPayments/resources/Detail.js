/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("ReceivedPayments_Detail_Js", {}, {
	registerEvents: function () {
		this._super();
		this.collate();//核对
	},
	collate : function() {
		$('body').on("click", '#ReceivedPayments_detailView_basicAction_LBL_COLLATE', function() {
			var recordid = $('#recordId').val();
			var dialog = bootbox.dialog({
				title: '回款核对',
				width: '600px',
				message: '<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0"><tbody>'+
					'<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>是否符合:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><select id="checkresult"><option value="fit">是</option><option value="unfit">否</option></select></span></div></td></tr>'+
					'<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor" style="display: none;" id="remarkstar">*</span>备注:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><textarea id="remark" style="overflow:hidden;overflow-wrap:break-word;resize:none; height:100px;width:320px;"></textarea></span></div></td></tr>'+
					'</tbody></table>',
				buttons: {
					ok: {
						label: "确定",
						className: 'btn-success',
						callback: function() {
							var checkresult = $('#checkresult').val();
							var remark = $('#remark').val();
							remark = $.trim(remark);
							if (checkresult == 'unfit' && remark=='') {
								var params = {type: 'error', text: '选择否时，备注必须填写'};
								Vtiger_Helper_Js.showMessage(params);
								return false;
							}
							if (remark.length>2000) {
								var params = {type: 'error', text: '备注允许最大长度为2000'};
								Vtiger_Helper_Js.showMessage(params);
								return false;
							}
							var postData = {
								'module': 'ReceivedPayments',
								'action': 'BasicAjax',
								'recordid': recordid,
								'checkresult': checkresult,
								'remark': remark,
								'mode': 'collate'
							}
							var Message = "提交中...";
							var progressIndicatorElement = jQuery.progressIndicator({
								'message' : Message,
								'position' : 'html',
								'blockInfo' : {'enabled' : true}
							});
							AppConnector.request(postData).then(
								function(data) {
									// 隐藏遮罩层
									progressIndicatorElement.progressIndicator({
										'mode' : 'hide'
									});
									if(data.success) {
										if (data.result.status == 'success') {
											var params = {type: 'success', text: '成功核对'};
											Vtiger_Helper_Js.showMessage(params);
											window.location.reload();
										} else {
											var params = {type: 'error', text: data.result.msg};
											Vtiger_Helper_Js.showMessage(params);
										}
									} else {
										var params = {type: 'error', text: data.error.message};
										Vtiger_Helper_Js.showMessage(params);
									}
								},
								function(error,err) {

								}
							);
						}
					},
					cancel: {
						label: "取消",
						className: 'btn',
						callback: function(){

						}
					}
				}
			});
		}).on('change', '#checkresult', function() {
			if( $(this).val()=='unfit') {
				$('#remarkstar').show();
			} else {
				$('#remarkstar').hide();
			}
		});
	}
})