/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("Vendors_List_Js",{},{

	setVendorstate : function (container) {
		var listViewContentDiv = this.getListViewContentContainer();
		var type = 'PROTECTED';
		var listInstance = Vtiger_List_Js.getInstance();
		var message = app.vtranslate('LBL_' + type + '_CONFIRMATION');

		
		listViewContentDiv.on('click', '.setVendorstate', function(e){ 
			var msg = {
                'message': '供应商状态变更',
                "width":"400px",
            };
            var elem = jQuery(e.currentTarget);
            var $select_tr = elem.closest('tr');
			var recordId = elem.closest('tr').data('id');
			var me = this;

			Vtiger_Helper_Js.showConfirmationBox(msg).then(
				function(e) {
					var select_value = $('#select_vendor_state').val();
					var module = app.getModuleName();
					var postData = {
						"module": module,
						"action": "BasicAjax",
						"record": recordId,
						'status': select_value,
						'mode': 'setVendorState'
					}
					
					var Message = app.vtranslate('JS_RECORD_GETTING_'+type);
					
					var progressIndicatorElement = jQuery.progressIndicator({
							'message' : Message,
							'position' : 'html',
							'blockInfo' : {'enabled' : true}
							});
					AppConnector.request(postData).then(
						function(data){
							progressIndicatorElement.progressIndicator({
									'mode' : 'hide'
								});
							if (data.success) {
								var t = {'1': '是', '0': '否'};
								$select_tr.find('.c_vendorstate').html(vendorsTateData[select_value]);

								$(me).attr('data-status', select_value);
								//alert('更新合同自动关闭状态成功');
								var  params = {text : '供应商状态变更成功', title : '提示'};
								Vtiger_Helper_Js.showMessage(params);
							} else {
								var  params = {text : '供应商状态变更失败', title : '提示'};
								Vtiger_Helper_Js.showMessage(params);
							}
						},
						function(error,err){

						}
					);
				},function(error, err){}
			);

			var status = $(this).attr('data-status');
			var ss = '<select id="select_vendor_state">';
			var is_option = '';
			for(var i in vendorsTateData) {
				is_option = i == status ? 'selected' : '';
				ss += '<option '+is_option+' value="'+i+'">'+vendorsTateData[i]+'</option>';
			}
			ss += '</select>';
			$('.modal-body').append('<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0;"><tbody><tr><td class="fieldLabel medium"><label class="muted">供应商状态:</label></td><td class="fieldValue medium " colspan="3"><div class="row-fluid  pull-left"><span class="span10">'+ss+'</span></div></td></tr></tbody></table>');
		});
	},
	setAllowtransaction: function () {
		var listViewContentDiv = this.getListViewContentContainer();
		var type = 'PROTECTED';
		var listInstance = Vtiger_List_Js.getInstance();
		var message = app.vtranslate('LBL_' + type + '_CONFIRMATION');

		
		listViewContentDiv.on('click', '.setAllowtransaction', function(e){ 
			var msg = {
                'message': '允许交易变更',
                "width":"400px",
            };
            var elem = jQuery(e.currentTarget);
            var $select_tr = elem.closest('tr');
			var recordId = elem.closest('tr').data('id');
			var me = this;
			
			Vtiger_Helper_Js.showConfirmationBox(msg).then(
				function(e) {
					var select_value = $('#select_allowtransaction').val();
					var module = app.getModuleName();
					var postData = {
						"module": module,
						"action": "BasicAjax",
						"record": recordId,
						'status': select_value,
						'mode': 'setAllowtransaction'
					}
					
					var Message = app.vtranslate('JS_RECORD_GETTING_'+type);
					
					var progressIndicatorElement = jQuery.progressIndicator({
							'message' : Message,
							'position' : 'html',
							'blockInfo' : {'enabled' : true}
							});
					AppConnector.request(postData).then(
						function(data){
							progressIndicatorElement.progressIndicator({
									'mode' : 'hide'
								});
							if (data.success) {
								var t = {'1': '是', '0': '否'};
								$select_tr.find('.c_allowtransaction').html(t[select_value]);

								$(me).attr('data-status', t[select_value]);
								//alert('更新合同自动关闭状态成功');
								var  params = {text : '供应商状态变更成功', title : '提示'};
								Vtiger_Helper_Js.showMessage(params);
							} else {
								var  params = {text : '供应商状态变更失败', title : '提示'};
								Vtiger_Helper_Js.showMessage(params);
							}
						},
						function(error,err){

						}
					);
				},function(error, err){}
			);

			var status = $(this).attr('data-status');
			var ss = '<select id="select_allowtransaction">';
			var is_option = '';
			var vendorsTateData = {
				'0' : '否',
				'1' : '是'
			};
			for(var i in vendorsTateData) {
				is_option = vendorsTateData[i] == status ? 'selected' : '';
				ss += '<option '+is_option+' value="'+i+'">'+vendorsTateData[i]+'</option>';
			}
			ss += '</select>';
			$('.modal-body').append('<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0;"><tbody><tr><td class="fieldLabel medium"><label class="muted">是否允许交易:</label></td><td class="fieldValue medium " colspan="3"><div class="row-fluid  pull-left"><span class="span10">'+ss+'</span></div></td></tr></tbody></table>');
		});
	},
	registerEvents : function(container){
		this._super();
		
		this.setVendorstate();
		this.setAllowtransaction(container);
	}

});