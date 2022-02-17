Vtiger_Edit_Js("Receipt_Edit_Js",{},{
	ckEditorInstance:'',
	ckEInstance:'',
	rowSequenceHolder : false,
	pageInit:function(){
		$("input[name='receiptmoney']").attr("readonly","readonly");
		$("input[name='receivedpaymentsid_display']").val($("input[name='paytitle']").val());
		var record = $('input[name="record"]').val();
		if(!record){
			$("input[name='receiptno']").attr("disabled","disabled");
			$("input[name='isMail']").attr("checked",true);
			$("input[name='courierNumber']").parents('td').prev().find('label').prepend('<span class="redColor">* </span>');
			$("input[name='receiver']").parents('td').prev().find('label').prepend('<span class="redColor">* </span>');
			$("input[name='receiverMobile']").parents('td').prev().find('label').prepend('<span class="redColor">* </span>');
			$("input[name='receiverAddress']").parents('td').prev().find('label').prepend('<span class="redColor">* </span>');
		}
		$("input[name='isMail']").on('change', function () {
			if($(this).is(':checked')) {
				$("input[name='courierNumber']").parents('td').prev().find('label').prepend('<span class="redColor">* </span>');
				$("input[name='receiver']").parents('td').prev().find('label').prepend('<span class="redColor">* </span>');
				$("input[name='receiverMobile']").parents('td').prev().find('label').prepend('<span class="redColor">* </span>');
				$("input[name='receiverAddress']").parents('td').prev().find('label').prepend('<span class="redColor">* </span>');
			} else {
				$("input[name='courierNumber']").parents('td').prev().find('.redColor').remove();
				$("input[name='receiver']").parents('td').prev().find('.redColor').remove();
				$("input[name='receiverMobile']").parents('td').prev().find('.redColor').remove();
				$("input[name='receiverAddress']").parents('td').prev().find('.redColor').remove();
			}
		});
	},
	registerReferenceSelectionEvent : function(container) {
		var thisInstance = this;
		$('.receiptno_hide').remove();
		jQuery('input[name="contractid"]', container).on(Vtiger_Edit_Js.referenceSelectionEvent, function(e, data){
			$('input[name="receivedpaymentsid"]').val('');
			$('input[name="receivedpaymentsid_display"]').val('');
			$('input[name="receiptmoney"]').val('');
			$('.newinvoicerayment_div').empty();
		});
		jQuery('input[name="receivedpaymentsid"]', container).on(Vtiger_Edit_Js.referenceSelectionEvent, function(e, data){
			thisInstance.loadReceivedPayments(data['record']);
		});
	},

	loadReceivedPayments:function (receivedpaymentsid) {
		var me = this;
		var params = {
			'type':'GET',
			'module' : 'Receipt',
			'action' : 'ChangeAjax',
			'mode':'getReceivedPayments',
			'receivedpaymentsid':receivedpaymentsid,
		};
		AppConnector.request(params).then(
			function(data){
				if(data.success){
					var len = data.result.length;
					var string =  {text : '无回款可能原因：客户到款未匹配到该合同上或者已经申请过开票',
						title : '该合同下没有可开发票金额的回款，您可以申请预开票'};
					if( len<= 0){
						$('#Newinvoice_editView_fieldName_contractid_clear').trigger('click');
						Vtiger_Helper_Js.showPnotify(string);
						return;
					}
					$("input[name='invoicedTotal']").val(data.result.invoicedTotal);
					var allowinvoicetotal = 0;
					var money = 0.00;
					$('.newinvoicerayment_div').empty();
					$.each(data.result.invoicerayment,function(i,val){
						allowinvoicetotal += val['allowinvoicetotal'];
						if(i>=len-1&&allowinvoicetotal<=0){
							$('#Newinvoice_editView_fieldName_contractid_clear').trigger('click');
							Vtiger_Helper_Js.showPnotify(string);
							return;
						}
						var newinvoiceraymentnum = $('.newinvoicerayment_tab').length + 1;
						if (newinvoiceraymentnum > 100) {return ;}
						var nowdnum=$('.newinvoicerayment_tab').last().data('num');
						if(nowdnum){
							newinvoiceraymentnum=nowdnum+1;
						}
						var t_newinvoicerayment_html = newinvoicerayment_html.replace(/\[\]/g,'['+newinvoiceraymentnum+']');
						t_newinvoicerayment_html = t_newinvoicerayment_html.replace(/yesreplace/g, newinvoiceraymentnum);
						t_newinvoicerayment_html = t_newinvoicerayment_html.replace(/pricereplace/g, val['unit_price']);
						t_newinvoicerayment_html = t_newinvoicerayment_html.replace(/newinvoicerayment_select_html/g, this.newinvoicerayment_select_html);
						$('.newinvoicerayment_div').append(t_newinvoicerayment_html);

						var $t = $('.newinvoicerayment_div').find('table:last');
						$t.find('.owncompany').text(val['owncompany']);
						$t.find('.paytitle').text(val['paytitle']);
						$t.find('.total').text(val['unit_price']);
						$t.find('.arrivaldate').text(val['reality_date']);
						money = Number(money)+Number(val['unit_price']);
					})
					$('input[name="receiptmoney"]').val(money)
					$('.newinvoicerayment_div').show();

					//合计基本信息栏的开票金额
					// me.calculation_invoicetotal_sum();
					// $(document).on('blur', '.receivedpayments_invoicetotal', function () {
					// 	me.calculation_invoicetotal_sum();
					// });
				}
			},
			function(){
			}
		)
	},

	// 删除匹配的回款
	delbuttonnewinvoicerayment: function() {
		var me = this;
		$(document).on('click', '.delbuttonnewinvoicerayment', function () {
			var newthis=$(this);
			var message='确定要删除吗？';
			var msg={
				'message':message
			};
			var dataid=$(this).data('id');
			var price=$(this).data('price');
			//flagv=2;
			Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                //收据金额更新
				var money = $("input[name='money']").val();
				var total = parseFloat(money) - parseFloat(price);
				$('input[name="money"]').val(total);
				newthis.closest('.newinvoicerayment_tab').remove();
			},function(error, err) {});
		});
	},

	/**
	 * steel 类型分类调用产品列表
	 */
	parentProductsEvent: function () {
		var thisInstance=this;
		$('form').on('change','select[name="parentcate"]', function () {
			// if(thisInstance.isElecLoadData()){
			// 	return false;
			// }
			$(this).siblings().not('#'+$(this).attr('id')+'_chzn').remove();
			if ($('select[name="parentcate"]').val() != "") {
				var parentcate = $('select[name="parentcate"]').val();  //请求异常处理，对字符进行编码
				var params = {
					'type': 'GET',
					'dataType': 'html',
					'data': 'module=PayApply&action=ChangeAjax&mode=getproductlist&parentcate=' + parentcate
				};
				AppConnector.request(params).then(
					function (data) {
						var selejson= $.parseJSON(data);
						$('select[name="soncate"]').remove();
						var selectprodcut='<select class="chzn-select" name="soncate" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"> <option value="">选择一个选项</option>';
						if (data == 'null' || selejson.result.length == 0) {
							selectprodcut+='</select>';
						} else {
							var option=''
							$.each(selejson.result,function(i,val){
								option+='<option value='+val[0]+'>'+val[1]+'</option>';
							});
							selectprodcut+=option+'</select>';
						}

						$('select[name="parentcate"]').siblings().not('#'+$('select[name="parentcate"]').attr('id')+'_chzn').remove();
						$('select[name="parentcate"]').parent().append(selectprodcut);
						$('.chzn-select').chosen();
					},
					function (error) {
					});
				//合同是T-云系列或者TSITE系列自动选择
			}
		})
	},

	registerRecordPreSaveEvent: function () {
		var thisInstance = this;
		var editViewForm = this.getForm();
		editViewForm.on(Vtiger_Edit_Js.recordPreSave,function(e,data) {
			if($("input[name='f']").val()==''){
				Vtiger_Helper_Js.showMessage({type:'error',text:'请填写申请有效时间【开始日期】!'});
				e.preventDefault();
				return false;
			}
			if($("input[name='s']").val()==''){
				Vtiger_Helper_Js.showMessage({type:'error',text:'请填写申请有效时间【结束日期】!'});
				e.preventDefault();
				return false;
			}
			if($("input[name='isMail']").is(':checked')) {
				if(!$("input[name='courierNumber']").val()) {
					Vtiger_Helper_Js.showMessage({type:'error',text:'请填写快递单号!'});
					e.preventDefault();
					return false;
				}
				if(!$("input[name='receiver']").val()) {
					Vtiger_Helper_Js.showMessage({type:'error',text:'请填写收件人!'});
					e.preventDefault();
					return false;
				}
				if(!$("input[name='receiverMobile']").val()) {
					Vtiger_Helper_Js.showMessage({type:'error',text:'请填写手机号!'});
					e.preventDefault();
					return false;
				}
				if(!$("input[name='receiverAddress']").val()) {
					Vtiger_Helper_Js.showMessage({type:'error',text:'请填写收件地址!'});
					e.preventDefault();
					return false;
				}
			}
		});
	},

	getPopUpParams : function(container) {
		var params = {};
		var sourceModule = app.getModuleName();
		var popupReferenceModule = jQuery('input[name="popupReferenceModule"]',container).val();
		var sourceFieldElement = jQuery('input[class="sourceField"]',container);
		var sourceField = sourceFieldElement.attr('name');
		var sourceRecordElement = jQuery('input[name="record"]');
		var sourceRecordId = '';
		if(sourceRecordElement.length > 0) {
			sourceRecordId = sourceRecordElement.val();
		}

		var isMultiple = false;
		if(sourceFieldElement.data('multiple') == true){
			isMultiple = true;
		}
		if(sourceField == 'receivedpaymentsid'){
			var contractid = jQuery('input[name="contractid"]').val();
			if(typeof(contractid) == "undefined" || contractid == "" || contractid == 'undefined'){
				Vtiger_Helper_Js.showMessage({type:'error',text:'请先选择合同!'});
				return;
			}
			var params = {
				'module' : 'ReceivedPayments',
				'src_module' : sourceModule,
				'src_field' : sourceField,
				'src_record' : sourceRecordId,
				'contractid' : contractid
			}
		}else{
			var params = {
				'module' : popupReferenceModule,
				'src_module' : sourceModule,
				'src_field' : sourceField,
				'src_record' : sourceRecordId
			}
		}
		if(isMultiple) {
			params.multi_select = true ;
		}
		return params;
	},

	openPopUp : function(e){
		var thisInstance = this;
		var parentElem = jQuery(e.target).closest('td');

		var params = this.getPopUpParams(parentElem);

		var isMultiple = false;
		if(params.multi_select) {
			isMultiple = true;
		}

		var sourceFieldElement = jQuery('input[class="sourceField"]',parentElem);
		var prePopupOpenEvent = jQuery.Event(Vtiger_Edit_Js.preReferencePopUpOpenEvent);
		sourceFieldElement.trigger(prePopupOpenEvent);

		if(prePopupOpenEvent.isDefaultPrevented()) {
			return ;
		}

		var popupInstance =Vtiger_Popup_Js.getInstance();
		popupInstance.show(params,function(data){
			var responseData = JSON.parse(data);
			var dataList = new Array();
			var idList = new Array();
			idList['id']=new Array();
			idList['name']=new Array();
			for(var id in responseData){
				var data = {
					'name' : responseData[id].name,
					'id' : id
				}
				dataList.push(data);
				if(!isMultiple) {
					thisInstance.setReferenceFieldValue(parentElem, data);
				}else{
					idList['id'].push(id);
					idList['name'].push(responseData[id].name);
				}
			}

			if(isMultiple) {
				thisInstance.setMultiReferenceFieldValue(parentElem, idList);
			}
			sourceFieldElement.trigger(Vtiger_Edit_Js.postReferenceSelectionEvent,{'data':responseData});
		});
	},

	registerEvents: function () {
		this._super();
		this.pageInit();
		this.parentProductsEvent();
		this.registerRecordPreSaveEvent();
		this.delbuttonnewinvoicerayment();
		this.registerReferenceSelectionEvent($("#EditView"));
	},

});




















