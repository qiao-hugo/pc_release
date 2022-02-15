


Vtiger_List_Js("ReceivedPaymentsClassify_List_Js",{

},{
	getDefaultParams : function() {
		var pageNumber = jQuery('#pageNumber').val();
		var module = app.getModuleName();
		var parent = app.getParentModuleName();
		var cvId = this.getCurrentCvId();
		var orderBy = jQuery('#orderBy').val();
		var sortOrder = jQuery("#sortOrder").val();
		var pub = $('#public').val();
		var filter=$('#filter').val();
		var DepartFilter=$('#DepartFilter').val();
		var params = {
			'__vtrftk':$('input[name="__vtrftk"]').val(),
			'module': module,
			'parent' : parent,
			'page' : pageNumber,
			'view' : "List",
			'viewname' : cvId,
			'orderby' : orderBy,
			'sortorder' : sortOrder,
			'public' : pub,
			'filter' :filter,
			'department':DepartFilter,
			'accountsname': $("input[name ='accountsname']").val(),
			'smown':$('select[name="smowen"]').val()
		}

        var searchValue = this.getAlphabetSearchValue();

        if((typeof searchValue != "undefined") && (searchValue.length > 0)) {
            params['search_key'] = this.getAlphabetSearchField();
            params['search_value'] = searchValue;
            params['operator'] = "s";
        }
		return params;
	},
	// 修改可开发票金额
	isEditAllowinvoicetotal: function () {
		var listViewContentDiv = this.getListViewContentContainer();
		var type = 'PROTECTED';
		var listInstance = Vtiger_List_Js.getInstance();
		var message = app.vtranslate('LBL_' + type + '_CONFIRMATION');

		listViewContentDiv.on('click', '.isEditAllowinvoicetotal', function(e){
			var msg = {
                'message': '更改可开发票金额',
                "width":"400px",
            };
            var elem = jQuery(e.currentTarget);
            var me = this;
            var $t_tr = elem.closest('tr');
			var recordId = elem.closest('tr').data('id');
			var allowinvoicetotal = $(this).data('status');
			Vtiger_Helper_Js.showConfirmationBox(msg).then(
				function(e) {
					var selectValue = $('#i_allowinvoicetotal').val();
					selectValue = parseFloat(selectValue);
					// 判断是否合法
					if(isNaN(selectValue)) {
						Vtiger_Helper_Js.showPnotify(app.vtranslate('可开发票金额必须为数字'));
						return ;
					}
					if(selectValue < 0) {
						Vtiger_Helper_Js.showPnotify(app.vtranslate('可开发票金额不可小于零'));
						return ;
					}

					var module = app.getModuleName();
					var postData = {
						"module": module,
						"action": "BasicAjax",
						"record": recordId,
						'allowinvoicetotal': selectValue,
						'mode': 'setAllowinvoicetotal'
						//"parent": app.getParentModuleName()
					}

					var Message = app.vtranslate('JS_RECORD_GETTING_'+type);

					var progressIndicatorElement = jQuery.progressIndicator({
							'message' : Message,
							'position' : 'html',
							'blockInfo' : {'enabled' : true}
							});
					AppConnector.request(postData).then(
						function(data){
							// 隐藏遮罩层
							progressIndicatorElement.progressIndicator({
								'mode' : 'hide'
							});
							if (data.success) {
								$t_tr.find('.allowinvoicetotal_value').html(selectValue);
								$(me).data('status', selectValue);
								var  params = {text : '更新可开发票成功', title : '提示'};
								Vtiger_Helper_Js.showMessage(params);

							}
						},
						function(error,err){

						}
					);
				},function(error, err){}
			);

			$('.modal-body').append('<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">可开发票金额:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><input value="'+allowinvoicetotal+'" id="i_allowinvoicetotal"></span></div></td></tr></tbody></table>');
		});
	},

	// 拆分回款
	splitReceive : function() {
		var listViewContentDiv = this.getListViewContentContainer();
		var type = 'PROTECTED';
		var listInstance = Vtiger_List_Js.getInstance();
		var message = app.vtranslate('LBL_' + type + '_CONFIRMATION');
		var thisInstance=this;

		listViewContentDiv.on('click', '.splitReceive', function(e){
			var elem = jQuery(e.currentTarget);
			var recordId = elem.closest('tr').data('id');

			var selectValue = $('#receivedstatus').val();
			var module = app.getModuleName();
			var postData = {
				"module": module,
				"action": "BasicAjax",
				"record": recordId,
				'status': 1,
				'mode': 'getSplitServiceContracts'
				//"parent": app.getParentModuleName()
			}

			var Message = app.vtranslate('JS_RECORD_GETTING_'+type);
			var progressIndicatorElement = jQuery.progressIndicator({
					'message' : Message,
					'position' : 'html',
					'blockInfo' : {'enabled' : true}
					});
				AppConnector.request(postData).then(
					// 请求成功
					function(data){
						// 隐藏遮罩层
						progressIndicatorElement.progressIndicator({
							'mode' : 'hide'
						});

						if (data.success) {
							if(data.result.isover==1){
                                Vtiger_Helper_Js.showPnotify(app.vtranslate('该回款已匹配且业绩已确认完结'));
                                return false;
							}
							var contract_no_arr = data.result.contract_no;
							var unit_price = parseFloat(data.result.unit_price);  //回款金额
							// 成功
							if (true) {
								var msg = {
					                'message': '回款拆分 可拆分金额：'+unit_price,
					                "width":"400px",
					            };
								Vtiger_Helper_Js.showConfirmationBox(msg).then(
									function(e) {
										if(contract_no_arr.length == 0){
											var step=false;
											//走另外一条路没合同
											var splitSumMoney=0;
											$("input[name='split_money[]']").each(function (i) {
												var splitSingleMoney=$.trim($(this).val());
												if(isNaN(splitSingleMoney)||splitSingleMoney==""||splitSingleMoney<=0){
													Vtiger_Helper_Js.showPnotify(app.vtranslate('拆分的第'+(i+1)+'行金额必须是数字且大于零'));
													return false;
												}else{
													splitSumMoney=thisInstance.accAdd(splitSumMoney,splitSingleMoney);
												}
												step=true;
											});
											if(step&&(splitSumMoney>unit_price||splitSumMoney<=0)){
												Vtiger_Helper_Js.showPnotify(app.vtranslate('分拆金额总和必须小于等于原始金额且大于零'));
												return false;
											}else if(step){
												var postData = {
													"module": module,
													"action": "BasicAjax",
													"record": recordId,
													'split_money': $("input[name='split_money[]']").serialize(),
													'unit_price':unit_price,
													'mode': 'splitBatchReceive'
												}
												var Message = app.vtranslate('JS_RECORD_GETTING_'+type);
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
														if (data.success) {
															if (data.result.flag) {
																location.reload();
															} else {
																var errorMsg="以下拆分失败，详情如下：<br>";
																for(var i=0;i<data.result.msg.length;i++){
																	if(data.result.msg[i]){
																		errorMsg+='第'+(i+1)+'行拆分失败，原因是'+data.result.msg[i]+'<br>';
																	}
																}
																errorMsg+='查看完请刷新页面';
																Vtiger_Helper_Js.showPnotify(app.vtranslate(errorMsg));
															}
														}
													},
													function(error,err){}
												);
											}
										}else{
											// 拆分提交（有合同号）
											var contract_no = $('#contract_no_select').val();
											var split_money = $.trim($('#split_money').val());
											if(! isNaN(split_money) ) {  // 判断是否是数字
												if (split_money > unit_price || split_money <= 0) {
													Vtiger_Helper_Js.showPnotify(app.vtranslate('分拆金额必须小于等于原始金额且大于零'));
												} else {
													var postData = {
														"module": module,
														"action": "BasicAjax",
														"record": recordId,
														'contract_no': contract_no,
														'split_money': split_money,
														't_split_money':thisInstance.accSub(unit_price,split_money),
														'unit_price':unit_price,
														'mode': 'splitReceive'
														//"parent": app.getParentModuleName()
													}
													var Message = app.vtranslate('JS_RECORD_GETTING_'+type);
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
															if (data.success) {
																if (data.result.flag) {
																	location.reload();
																} else {
																	Vtiger_Helper_Js.showPnotify(app.vtranslate(data.result.msg));
																}
																//alert('更新回款状态成功');

															}
														},
														function(error,err){}
													);
												}
											} else {
												Vtiger_Helper_Js.showPnotify(app.vtranslate('金额必须是数字'));
											}
										}
									},function(error, err){}
								);

								var str = '';
								for(var i=0; i<contract_no_arr.length; i++) {
									str += '<option value="'+ contract_no_arr[i]['servicecontractsid'] +'">'+ contract_no_arr[i]['contract_no'] +'</option>';
								}
								$display = '';
								if (contract_no_arr.length == 0) {
									//没有合同号的未匹配可多次拆分
									$('.modal-body').append('<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0;"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">分拆金额:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><input type="number" name="split_money[]" /></span><i title="增加" style="cursor:pointer;margin-right: 10px;" class="icon-plus alignMiddle addSplit"></i></div></td></tr></tbody></table>');
								}else{
									$('.modal-body').append('<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0;"><tbody><tr style="display:' + $display + '"><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">分拆合同:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><select id="contract_no_select" name="contract_no">'+str+'</span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">分拆金额:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><input type="number" id="split_money" name="split_money" /></span></div></td></tr></tbody></table>');
								}

							} else {
								// 没有对应的合同
								Vtiger_Helper_Js.showPnotify(app.vtranslate('没有找到回款对应的服务合同'));
							}

						} else {
							Vtiger_Helper_Js.showPnotify(app.vtranslate('操作失败'));
						}
					},
					function(error,err){

				}
			);
		});

		/*listViewContentDiv.on('click', '.splitReceive', function(e){

			var msg = {
                'message': '回款拆分',
                "width":"400px",
            };
            var elem = jQuery(e.currentTarget);
			var recordId = elem.closest('tr').data('id');
			Vtiger_Helper_Js.showConfirmationBox(msg).then(
				function(e) {
					alert(111);
				},function(error, err){}
			);

			var str = '';
			var temp_str = {
				'normal': '正常',
				'void': '已作废',
				'refund': '已退款',
			};
			var status = $(this).attr('data-status');
			for(var index in temp_str) {
				if (index == status) {
					str += '<option selected="selected" value="'+ index +'">'+ temp_str[index] +'</option>';
				} else {
					str += '<option value="'+ index +'">'+ temp_str[index] +'</option>';
				}
			}
			$('.modal-body').append('<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0;"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">采购单状态:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><select id="receivedstatus">'+str+'</span></div></td></tr></tbody></table>');

		});*/
	},

	addSplit : function () {
		$('body').on('click','.addSplit',function () {
			if($(".addSplit").length>9){
				alert("最大可拆分10次");
				return false;
			}
			$('.modal-body tbody').append('<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">分拆金额:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><input type="number" id="split_money1" name="split_money[]"></span><i title="增加" style="cursor:pointer;margin-right: 10px;" class="icon-plus alignMiddle addSplit"></i><i title="删除" class="icon-trash alignMiddle delSplit" style="cursor:pointer"></i></div></td></tr>');
		});
	},

	deleteSplit:function(){
		$('body').on('click','.delSplit',function () {
			$(this).closest("tr").remove();
		});
	},

	setReceiveStatus: function() {
		var listViewContentDiv = this.getListViewContentContainer();
		var type = 'PROTECTED';
		var listInstance = Vtiger_List_Js.getInstance();
		var message = app.vtranslate('LBL_' + type + '_CONFIRMATION');

		listViewContentDiv.on('click', '.setReceiveStatus', function(e){
			var msg = {
                'message': '更改回款类型',
                "width":"400px",
            };
            var elem = jQuery(e.currentTarget);
			var recordId = elem.closest('tr').data('id');

			Vtiger_Helper_Js.showConfirmationBox(msg).then(
				function(e) {
					var selectValue = $('#receivedstatus').val();
					var module = app.getModuleName();
					var postData = {
						"module": module,
						"action": "BasicAjax",
						"record": recordId,
						'status': selectValue,
						'mode': 'setReceivedstatus'
						//"parent": app.getParentModuleName()
					}

					var Message = app.vtranslate('JS_RECORD_GETTING_'+type);

					var progressIndicatorElement = jQuery.progressIndicator({
							'message' : Message,
							'position' : 'html',
							'blockInfo' : {'enabled' : true}
							});
					AppConnector.request(postData).then(
						function(data){
                            progressIndicatorElement.progressIndicator({'mode' : 'hide'});
							if (data.success) {
								if(data.result.flag){
                                    alert('更新回款类型成功');
                                    location.reload();
								}else{
                                    alert(data.result.msg);
								}
							}
						},
						function(error,err){

						}
					);
				},function(error, err){}
			);

			var str = '';
			var temp_str = {
				'normal': '正常',
				'void': '已作废',
				'refund': '已退款',
				'SupplierRefund': '供应商退款',
				'RebateAmount': '返点款',
				'deposit': '保证金',
				'intercoursefunds':'往来款'
			};
			var status = $(this).attr('data-status');
			for(var index in temp_str) {
				if (index == status) {
					str += '<option selected="selected" value="'+ index +'">'+ temp_str[index] +'</option>';
				} else {
					str += '<option value="'+ index +'">'+ temp_str[index] +'</option>';
				}
			}
			$('.modal-body').append('<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0;"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">回款类型:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><select id="receivedstatus">'+str+'</span></div></td></tr></tbody></table>');
		});
	},
    NonPayCertificate:function(){
        var listViewContentDiv = this.getListViewContentContainer();
        listViewContentDiv.on('click', '.NonPayCertificate', function(e){
            var msg = {
                'message': '设为未提供代付款证明',
                "width":"400px",
				"action":function(){
                    var selectValue = $('#repeatReceiveValue').val();
                    if(selectValue=='') {
                        Vtiger_Helper_Js.showPnotify(app.vtranslate('合同编号不能为空'));
                        return false;
                    }
                    return true;
				}
            };
            var elem = jQuery(e.currentTarget);
            var recordId = elem.closest('tr').data('id');

            Vtiger_Helper_Js.showConfirmationBox(msg).then(
                function(e) {
                    var selectValue = $('#repeatReceiveValue').val();
                    var module = app.getModuleName();
                    var postData = {
                        "module": module,
                        "action": "BasicAjax",
                        "record": recordId,
                        'contractno': selectValue,
                        'mode': 'NonPayCertificate',

                        //"parent": app.getParentModuleName()
                    }
                    var progressIndicatorElement = jQuery.progressIndicator({
                        'message' : '正在努力加载,请稍后',
                        'position' : 'html',
                        'blockInfo' : {'enabled' : true}
                    });
                    AppConnector.request(postData).then(
                        function(data){
                            progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                            if (data.success) {
                                if(data.result.flag){
                                    alert('更新回款类型成功');
                                    location.reload();
                                }else{
                                    alert(data.result.msg);
                                }
                            }
                        },
                        function(error,err){

                        }
                    );
                },function(error, err){}
            );
            $('.modal-body').append('<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">合同编号:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><input value="" id="repeatReceiveValue"></span></div></td></tr></tbody></table>');
        });
        Vtiger_Helper_Js.showConfirmationBox =function(data){
            var aDeferred = jQuery.Deferred();
            var width='800px';
            var checkFlag=true
            if(typeof  data['width'] != "undefined"){
                width=data['width'];
            }
            var bootBoxModal = bootbox.confirm({message:data['message'],width:width, callback:function(result) {
                if(result){
                    if(typeof  data['action'] != "undefined"){
                        checkFlag=(data['action'])();
                    }
                    if(checkFlag){
                        aDeferred.resolve();
                    }else{
                        return false;
                    }
                } else{
                    aDeferred.reject();
                }
            }, buttons: { cancel: {
                label: '取消',
                className: 'btn'
            },
                confirm: {
                    label: '确认',
                    className: 'btn-success'
                }
            }});
            bootBoxModal.on('hidden',function(e){
                if(jQuery('#globalmodal').length > 0) {
                    jQuery('body').addClass('modal-open');
                }
            })
            return aDeferred.promise();
        }
	},
    dorechargeableamount:function(){
        var listViewContentDiv = this.getListViewContentContainer();
        listViewContentDiv.on('click', '.dorechargeableamount', function(e){
            var msg = {
                'message': '修改可使用金额',
                "width":"400px",
                "action":function(){
                    var selectValue = $('#repeatReceiveValue').val();
                    if(selectValue>=0){
                        return true;
                    }else{
                        Vtiger_Helper_Js.showPnotify(app.vtranslate('可使用金额不能为空,且大于等0'));
                        return false;
					}
                    return true;
                }
            };
            var elem = jQuery(e.currentTarget);
            var recordId = elem.closest('tr').data('id');

            Vtiger_Helper_Js.showConfirmationBox(msg).then(
                function(e) {
                    var selectValue = $('#repeatReceiveValue').val();
                    var module = app.getModuleName();
                    var postData = {
                        "module": module,
                        "action": "BasicAjax",
                        "record": recordId,
                        'rechargeableamount': selectValue,
                        'mode': 'changerechargeableamount',

                        //"parent": app.getParentModuleName()
                    }
                    var progressIndicatorElement = jQuery.progressIndicator({
                        'message' : '正在努力加载,请稍后',
                        'position' : 'html',
                        'blockInfo' : {'enabled' : true}
                    });
                    AppConnector.request(postData).then(
                        function(data){
                            progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                            if (data.success) {
                                if(data.result.flag){
                                    alert('更新成功');
                                    location.reload();
                                }else{
                                    alert(data.result.msg);
                                }
                            }
                        },
                        function(error,err){

                        }
                    );
                },function(error, err){}
            );
            $('.modal-body').append('<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">可使用金额:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><input value="" id="repeatReceiveValue"></span></div></td></tr></tbody></table>');
        });
        Vtiger_Helper_Js.showConfirmationBox =function(data){
            var aDeferred = jQuery.Deferred();
            var width='800px';
            var checkFlag=true
            if(typeof  data['width'] != "undefined"){
                width=data['width'];
            }
            var bootBoxModal = bootbox.confirm({message:data['message'],width:width, callback:function(result) {
                if(result){
                    if(typeof  data['action'] != "undefined"){
                        checkFlag=(data['action'])();
                    }
                    if(checkFlag){
                        aDeferred.resolve();
                    }else{
                        return false;
                    }
                } else{
                    aDeferred.reject();
                }
            }, buttons: { cancel: {
                label: '取消',
                className: 'btn'
            },
                confirm: {
                    label: '确认',
                    className: 'btn-success'
                }
            }});
            bootBoxModal.on('hidden',function(e){
                if(jQuery('#globalmodal').length > 0) {
                    jQuery('body').addClass('modal-open');
                }
            })
            return aDeferred.promise();
        }
	},
	cleanReceive : function() {
		var listViewContentDiv = this.getListViewContentContainer();
		var type = 'PROTECTED';
		var listInstance = Vtiger_List_Js.getInstance();
		var message = app.vtranslate('LBL_' + type + '_CONFIRMATION');


		listViewContentDiv.on('click', '.cleanReceive', function(e){

			var elem = jQuery(e.currentTarget);
			var $t_tr = elem.closest('tr');
			var recordId = $t_tr.data('id');
			var message ='确定要清除回款匹配？';
			Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
				function(e) {
					var module = app.getModuleName();
					var postData = {
						"module": module,
						"action": "BasicAjax",
						"record": recordId,
						'mode': 'cleanReceive'
						//"parent": app.getParentModuleName()
					}

					var Message = app.vtranslate('JS_RECORD_GETTING_'+type);
					var progressIndicatorElement = jQuery.progressIndicator({
							'message' : Message,
							'position' : 'html',
							'blockInfo' : {'enabled' : true}
							});
						AppConnector.request(postData).then(
							// 请求成功
							function(data){
								// 隐藏遮罩层
								progressIndicatorElement.progressIndicator({
									'mode' : 'hide'
								});

								if (data.result.flag==1) {
									$t_tr.find('.relatetoid_value').html('');
									$t_tr.find('.ismatchdepart_value').html('否');
									var  params = {text : '清除回款匹配成功', title : '提示'};
									Vtiger_Helper_Js.showMessage(params);
								} else {
									if(data.result.msg) {
										Vtiger_Helper_Js.showPnotify(app.vtranslate(data.result.msg));
									} else {
										Vtiger_Helper_Js.showPnotify(app.vtranslate('没有权限或者回款已开发票或已充值'));
									}

								}
							},
							function(error,err){

						}
					);


			});

		});
	},
    chargebacks:function(){
        var listViewContentDiv = this.getListViewContentContainer();
        var listInstance = Vtiger_List_Js.getInstance();
        var message = "变更可使用金额";


        listViewContentDiv.on('click', '.chargebacks', function(e){
        	var currentThis=this;
			var maxaccount=$(this).data('chargebacksa');
            var elem = jQuery(e.currentTarget);
            var $t_tr = elem.closest('tr');
            var recordId = $t_tr.data('id');
            var message ='确定变更可使用金额？';
            Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
                function(e) {
                    var module = app.getModuleName();
                    var chargebacksvalue=$('#chargebacksvalue').val();
                    var chargebacksremark=$('#chargebacksremark').val();
                    var postData = {
                        "module": module,
                        "action": "BasicAjax",
                        "record": recordId,
                        'mode': 'chargebacks',
						'chargebacksvalue':chargebacksvalue,
						'chargebacksremark':chargebacksremark
                        //"parent": app.getParentModuleName()
                    }
                    var progressIndicatorElement = jQuery.progressIndicator({
                        'message' : '正努力加载请稍后...',
                        'position' : 'html',
                        'blockInfo' : {'enabled' : true}
                    });
                    AppConnector.request(postData).then(
                        // 请求成功
                        function(data){
                            // 隐藏遮罩层
                            progressIndicatorElement.progressIndicator({
                                'mode' : 'hide'
                            });

                            if (data.result.flag) {
                                $t_tr.find('.relatetoid_value').html('');
                                $t_tr.find('.ismatchdepart_value').html('否');
                                var  params = {text : '扣款成功', title : '提示'};
                                Vtiger_Helper_Js.showMessage(params);
                                $(currentThis).remove();
                            } else {
                                if(data.result.msg) {
                                    Vtiger_Helper_Js.showPnotify(app.vtranslate(data.result.msg));
                                } else {
                                    Vtiger_Helper_Js.showPnotify(app.vtranslate('没有权限'));
                                }

                            }
                        },
                        function(error,err){

                        }
                    );


                });
            $('.modal-body').append('<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0;"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">扣款金额:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><input type="number" id="chargebacksvalue" value="" max="'+maxaccount+'" data-max="'+maxaccount+'" min="1" step="0.01" ></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">备注:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><textarea id="chargebacksremark" ></textarea></span></div></td></tr></tbody></table>');
        });
        $('body').on('blur keyup', '#chargebacksvalue', function(e){
			var thisValue=$(this).val();
			var thisMaxValue=$(this).data('max');
			if(thisValue>thisMaxValue){
                $(this).val(thisMaxValue);
			}
        });
	},
    dobackcash:function(){
        var listViewContentDiv = this.getListViewContentContainer();
        var listInstance = Vtiger_List_Js.getInstance();
        var message = "设为返点款";


        listViewContentDiv.on('click', '.dobackcash', function(e){
            var currentThis=this;
            var elem = jQuery(e.currentTarget);
            var $t_tr = elem.closest('tr');
            var recordId = $t_tr.data('id');
            var message ='确定要设为返点款？';
            Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
                function(e) {
                    var module = app.getModuleName();
                    var postData = {
                        "module": module,
                        "action": "BasicAjax",
                        "record": recordId,
                        'mode': 'dobackcash'
                    }
                    var progressIndicatorElement = jQuery.progressIndicator({
                        'message' : '正努力加载请稍后...',
                        'position' : 'html',
                        'blockInfo' : {'enabled' : true}
                    });
                    AppConnector.request(postData).then(
                        // 请求成功
                        function(data){
                            // 隐藏遮罩层
                            progressIndicatorElement.progressIndicator({
                                'mode' : 'hide'
                            });

                            if (data.result.flag) {
                                $t_tr.find('.relatetoid_value').html('');
                                $t_tr.find('.ismatchdepart_value').html('否');
                                var  params = {text : '修改成功', title : '提示'};
                                Vtiger_Helper_Js.showMessage(params);
                                $(currentThis).remove();
                            } else {
                                if(data.result.msg) {
                                    Vtiger_Helper_Js.showPnotify(app.vtranslate(data.result.msg));
                                } else {
                                    Vtiger_Helper_Js.showPnotify(app.vtranslate('没有权限'));
                                }

                            }
                        },
                        function(error,err){

                        }
                    );


                });
        });
    },
    /**
	 * 回款的重新匹配
     */
    repeatReceive:function(){
        var listViewContentDiv = this.getListViewContentContainer();

        var listInstance = Vtiger_List_Js.getInstance();

        listViewContentDiv.on('click', '.repeatReceive', function(e){
            var msg = {
                'message': '确定要重新匹配吗?',
                "width":"400px",
            };
            var elem = jQuery(e.currentTarget);
            var me = this;
            var $t_tr = elem.closest('tr');
            var recordId = elem.closest('tr').data('id');
            var allowinvoicetotal = $(this).data('status');
            Vtiger_Helper_Js.showConfirmationBox(msg).then(
                function(e) {
                    var selectValue = $('#repeatReceiveValue').val();

                    if(selectValue=='') {
                        Vtiger_Helper_Js.showPnotify(app.vtranslate('合同编号不能为空'));
                        return ;
                    }


                    var module = app.getModuleName();
                    var postData = {
                        "module": module,
                        "action": "BasicAjax",
                        "record": recordId,
                        'serviceid': selectValue,
                        'mode': 'repeatReceive'

                    }

                    var Message = "努力加载中...";

                    var progressIndicatorElement = jQuery.progressIndicator({
                        'message' : Message,
                        'position' : 'html',
                        'blockInfo' : {'enabled' : true}
                    });
                    AppConnector.request(postData).then(
                        function(data){
                            // 隐藏遮罩层
                            progressIndicatorElement.progressIndicator({
                                'mode' : 'hide'
                            });
                            if (data.success) {
                                //$t_tr.find('.allowinvoicetotal_value').html(selectValue);
                                //$(me).data('status', selectValue);
                                var  params = {text : data.result.msg, title : '提示'};
                                Vtiger_Helper_Js.showMessage(params);

                            }
                        },
                        function(error,err){

                        }
                    );
                },function(error, err){}
            );

            $('.modal-body').append('<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">合同编号:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><input value="" id="repeatReceiveValue"></span></div></td></tr></tbody></table>');
        });

	},
    /**
     * 减法运算相除JS问题
     * @param arg1除数
     * @param arg2被除数
     * @returns {number}
     */

    accSub:function (arg1,arg2){
		var r1,r2,m,n;
		try{r1=arg1.toString().split(".")[1].length}catch(e){r1=0}
		try{r2=arg2.toString().split(".")[1].length}catch(e){r2=0}
		m=Math.pow(10,Math.max(r1,r2));
		//动态控制精度长度
		n=(r1=r2)?r1:r2;
		return ((arg1*m-arg2*m)/m).toFixed(n);
	},

	/**
	 * 加法相加的问题
	 * @param arg1
	 * @param arg2
	 * @returns {number}
	 */
	accAdd:function(arg1,arg2){
		var r1,r2,m;
		try{r1=arg1.toString().split(".")[1].length}catch(e){r1=0}
		try{r2=arg2.toString().split(".")[1].length}catch(e){r2=0}
		m=Math.pow(10,Math.max(r1,r2))
		var s=(arg1*m+arg2*m)/m;
		if(isNaN(s)){
			s=0;
		}
		return s;
	},

	//计算合计总额
	totalMoney:function(){
		$(".listViewPageDiv .span4").append('<span id="totalAmount" style="max-width:200px; margin-left:20px;display: inline-block">合计金额(￥)：0</span><span id="totalOldAmount" style="max-width:200px; margin-left:15px;display: inline-block">合计原币金额：0</span>');
		var thisInstance=this;
		$("#listViewContents").on("change",'input[name="Detailrecord[]"]',function(){
			var totalAmount=0;
			var totalOldAmount=0;
			$('input[name="Detailrecord[]"]:checked').each(function () {
				var amount=$(this).data('amount');
				totalAmount=thisInstance.accAdd(totalAmount,amount);
				var oldAmount=$(this).data('oldamount');
				totalOldAmount=thisInstance.accAdd(totalOldAmount,oldAmount);
			});
			$("#totalAmount").html("合计金额(￥)："+totalAmount);
			$("#totalOldAmount").html("合计原币金额："+totalOldAmount);
		});
	},

	totalMoneyAfterajax:function(){
		$("#listViewContents").ajaxComplete(function(){
			$('input[name="Detailrecord[]"').trigger('change');
		});
	},

	//全选时计算金额
	allCheck:function(){
		$("#listViewContents").on("click",'input[name="checkAll"]',function(event){
			event.stopPropagation();
			if($(this).prop('checked')){
				$('#listViewContents input[name="Detailrecord[]').prop('checked',true);
			}else{
				$('#listViewContents input[name="Detailrecord[]').prop('checked',false);
			}
			$('#listViewContents input[name="Detailrecord[]').trigger('change');
		});
	},
	collate : function() { //核对
		$('body').on("click", '.collate', function() { //单个核对
			var recordid = $(this).parents('tr').data('id');
			var dialog = bootbox.dialog({
				title: '回款核对',
				width:'600px',
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
											$('#PostQuery').trigger('click');
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
		}).on('click', '#collateContract', function() { //批量核对数据
			var a = $('#SearchBug').serializeArray();
			var o = {};
			$.each(a, function() {
				if (o[this.name] !== undefined) {
					if (!o[this.name].push) {
						o[this.name] = [o[this.name]];
					}
					o[this.name].push(this.value || '');
				} else {
					o[this.name] = this.value || '';
				}
			});
			var form=JSON.stringify(o);

			var urlParams = {
				"module":"ReceivedPayments",
				"action":"JsonAjax",
				"mode":"getListViewCount",
				"BugFreeQuery":form
			};
			var progressIndicatorElement = jQuery.progressIndicator({
				'message' : '请求中...',
				'position' : 'html',
				'blockInfo' : {'enabled' : true}
			});
			AppConnector.request(urlParams).then(
				function(data){
					progressIndicatorElement.progressIndicator({
						'mode' : 'hide'
					});
					if(data.success){
						var total = data.result;
						if(total == 0) {
							var params = {
								type: 'error',
								text: '当前共0条数据，请修改查询条件'
							};
							Vtiger_Helper_Js.showMessage(params);
							return false;
						} else if(total > 1000) {
							var params = {
								type: 'error',
								text: '当前共' + total + '条数据,超过单次允许核对的最大记录数(1000)'
							};
							Vtiger_Helper_Js.showMessage(params);
							return false;
						}

						var dialog = bootbox.dialog({
							title: '回款核对（共' + total +'条数据）',
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
										var res = confirm('确定要一键核对'+total+'条数据吗？');
										if (res == false) {
											return false;
										}
										var checkresult = $('#checkresult').val();
										var remark = $('#remark').val();
											remark = $.trim(remark);
										if (checkresult == 'unfit' && remark=='') {
											var params = {type: 'error', text: '选择否时，备注必须填写'};
											Vtiger_Helper_Js.showMessage(params);
											return false;
										}
										if (remark.length > 2000) {
											var params = {type: 'error', text: '备注允许最大长度为2000'};
											Vtiger_Helper_Js.showMessage(params);
											return false;
										}
										var postData = {
											'module': 'ReceivedPayments',
											'action': 'BasicAjax',
											'checkresult': checkresult,
											'remark': remark,
											'mode': 'batchCollate',
											'BugFreeQuery': form
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
												if (data.success) {
													if (data.result.status == 'success') {
														var params = {type: 'success', text: data.result.msg};
														Vtiger_Helper_Js.showMessage(params);
														$('#PostQuery').trigger('click');
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
					} else {
						var  params = {type:'error', text : data.error.message, title : '提示'};
						Vtiger_Helper_Js.showMessage(params);
					}
				}
			);
		}).on('change', '#checkresult', function() {
			if( $(this).val()=='unfit') {
				$('#remarkstar').show();
			} else {
				$('#remarkstar').hide();
			}
		});
	},
	checklog: function() {
		$("body").on('click', '.collatelog', function () {
			var dialog = bootbox.dialog({
				title: '核对记录',
				width:'500px',
				message: '<p style="text-align: center;font-size:15px;color:#666"> 数据加载中...</p>'
			});
			var recordid = $(this).parents('tr').data('id');
			var postData = {
				'module': 'ReceivedPayments',
				'action': 'BasicAjax',
				'mode': 'collateLog',
				'recordid': recordid
			}
			AppConnector.request(postData).then(
				function(data) {
					if (data.success) {
						var htmlstr = '<ul class="collateloglist">';
						for (const i in data.result) {
							var item = data.result[i];
							var serialnum =parseInt(i)+1;
							htmlstr += '<li><span class="serialnum">' + serialnum + '</span><div><span class="collatetime">'+ item['collate_time'] +'</span><span class="collator" title = "' + item['collator'] + '">'+ item['collator'] +'</span><span class="status">'+ item['status'] +'</span></div><div class="remark">'+ item['remark'] +'</div></li>';
						}
						htmlstr += '</ul>';
						dialog.find('.bootbox-body').html(htmlstr);
					}
				},
				function(error,err) {

				}
			);
		})
	},
	import:function () {
		$("body").on("click",'#importContract',function () {
			location.href="./index.php?module=ReceivedPaymentsClassify&view=Import"
        })
    },
	export:function() {
		$("body").on('click','#exportContract',function () {
			var a = $('#SearchBug').serializeArray();
			var o = {};
			$.each(a, function() {
				if (o[this.name] !== undefined) {
					if (!o[this.name].push) {
						o[this.name] = [o[this.name]];
					}
					o[this.name].push(this.value || '');
				} else {
					o[this.name] = this.value || '';
				}
			});

			var exportIds=[];
            $(".entryCheckBox").each(function(k,v){
                if($(v).is(":checked")){
                    exportIds.push($(v).val());
                }
            });
			var form=JSON.stringify(o);

			var urlParams = {
				'module':'ReceivedPaymentsClassify',
				'action':'BasicAjax',
				'mode':'exportData',
				'BugFreeQuery':form,
				'exportIds':exportIds
			};
			var progressIndicatorElement = jQuery.progressIndicator({
				'message' : '请求中',
				'position' : 'html',
				'blockInfo' : {'enabled' : true}
			});
			AppConnector.request(urlParams).then(
				function(data){
					progressIndicatorElement.progressIndicator({
						'mode' : 'hide'
					});
					if (data.success) {
						window.location.href='index.php?module=ReceivedPaymentsClassify&action=BasicAjax&mode=exportFile';
					} else {
						var params = {text : data.error.message, title : '提示',type:'error'};
						Vtiger_Helper_Js.showMessage(params);
					}
				}
			);
		})
	},
	/**
	 * 回款的重新匹配
	 */
	delReceive:function(){
		var listViewContentDiv = this.getListViewContentContainer();

		listViewContentDiv.on('click', '.deleteRecord', function(e){
			var msg = {
				'message': '确定要删除吗?',
				"width":"400px",
			};
			var elem = jQuery(e.currentTarget);
			var recordId = elem.closest('tr').data('id');

			Vtiger_Helper_Js.showConfirmationBox(msg).then(
				function(e) {
					var module = app.getModuleName();
					var postData = {
						"module": module,
						"action": "BasicAjax",
						"record": recordId,
						'mode': 'delReceive'
					}
					var Message = "努力删除中...";

					var progressIndicatorElement = jQuery.progressIndicator({
						'message' : Message,
						'position' : 'html',
						'blockInfo' : {'enabled' : true}
					});
					AppConnector.request(postData).then(
						function(data){
							// 隐藏遮罩层
							progressIndicatorElement.progressIndicator({
								'mode' : 'hide'
							});
							if (data.success&&data.result.flag) {
								Vtiger_Helper_Js.showMessage({type : 'success', text : '删除成功'});
								location.reload();
							}else{
								Vtiger_Helper_Js.showMessage({type:'error',text:data.result.msg});
							}
						},
						function(error,err){
							Vtiger_Helper_Js.showMessage({type:'error',text:"删除失败"});
						}
					);
				},function(error, err){}
			);
		});

	},
    artificialClassificationSelect:function() {
		$(document).on('change', '.artificialclassfication', function () {
            var recordId = $(this).data("receivedpaymentsid");
            var artificialclassfication=$(this).val();
            var module = app.getModuleName();
            var postData = {
                "module": module,
                "action": "BasicAjax",
                "record": recordId,
				"artificialclassfication":artificialclassfication,
                'mode': 'artificialClassificationSelect'
            };
            AppConnector.request(postData).then(
                function(data){
                    if (data.success&&data.result.flag) {
                        Vtiger_Helper_Js.showMessage({type : 'success', text : '修改成功'});
                    }else{
                        Vtiger_Helper_Js.showMessage({type:'error',text:data.result.msg});
                    }
                },
                function(error,err){
                    Vtiger_Helper_Js.showMessage({type:'error',text:"修改失败"});
                }
            );
        })
	},
	registerEvents : function(){
        this._super();
        this.artificialClassificationSelect();

        // this.setReceiveStatus();
		// this.splitReceive();
		// this.cleanReceive();
		// this.isEditAllowinvoicetotal();
		// this.repeatReceive();
		// this.chargebacks();
		// this.NonPayCertificate();
		// this.dorechargeableamount();
		// this.dobackcash();
		// this.addSplit();
		// this.deleteSplit();
		this.totalMoney();
		this.allCheck();
		this.totalMoneyAfterajax();
		// this.collate();//核对
		// this.checklog();//核对记录
		this.export();//导出回款
		this.import();//导出回款
		// this.delReceive();//删除回款
	}
});
