/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("TyunWebBuyService_List_Js",{},{
	rebindContract:function(){
		$('body').on('click','.rebindContract',function(){
			var me = this;
			var $select_tr = $(this).closest('tr');
			var parenttd=$(this).closest('td');
			var showflag=parenttd.data('contractid')>0?true:false;
			var msg=showflag?'确定要重绑合同吗?':'确定要绑定合同吗?';
			var recordId = $(me).data('id');
			var msg = {
				'message': '<h5>'+msg+'</h5>',
				"width":"800px",
				'action':function(){
					var newcontractname=$('#newcontractname').val();
					if(newcontractname==''){
						var  params = {text : '新合同必填!', title : '提示'};
						Vtiger_Helper_Js.showMessage(params);
						return false;
					}
					return true;
				}
			};
			Vtiger_Helper_Js.showConfirmationBox(msg).then(
				function(e) {
					var module = app.getModuleName();
					var newcontractname=$('#newcontractname').val();
					var postData = {
						"module": module,
						"action": "BasicAjax",
						"record": recordId,
						'mode': 'rebindContract',
						'newrecord':newcontractname
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
								if(data.result.flag){
									var  params = {text : data.result.msg, title : '提示',type:'success'};
									Vtiger_Helper_Js.showMessage(params);
									$select_tr.find('.contractname').text(newcontractname);
									//location.reload();
								}else{
									var  params = {text : data.result.msg, title : '提示',type:'error'};
									Vtiger_Helper_Js.showMessage(params);
								}

							} else {
								var  params = {text : '更改失败', title : '提示'};
								Vtiger_Helper_Js.showMessage(params);
							}
						},
						function(error,err){}
					);
				},function(error, err){}
			);
			var str ='';
			if(showflag){
			var str = '<input name="contractname" id="contractname" type="text" value="'+$(me).data('contractname')+'" readonly/>';
			str='<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">原合同编号:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10">'+str+'</span></div></td></tr>'
			}
			$('.modal-body').append('<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0;"><tbody>'+str+
				'<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">新合同编号:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><input name="newcontractname" id="newcontractname" type="text" value="" /></span></div></td></tr></tbody></table>');

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
	exportData:function(){
		$(".exportdata").click(function(){
			var progressIndicatorElement = jQuery.progressIndicator({
				'message' : "努力处理中请稍等...",
				'position' : 'html',
				'blockInfo' : {
					'enabled' : true
				}
			});
			var searchParamsPreFix = 'BugFreeQuery';
			var rowOrder = "";
			var $searchRows = $("tr[id^=SearchConditionRow]");
			$searchRows.each(function(){
				rowOrder += $(this).attr("id")+",";
			});

			eval("$('#"+searchParamsPreFix+"_QueryRowOrder')").attr("value",rowOrder);
			var limit = $('#limit').val();
			var o = {};
			var a = $('#SearchBug').serializeArray();
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
			var urlParams = {"module":"TyunWebBuyService","action":"BasicAjax","mode":"exportdata","page":1,"BugFreeQuery":form,"limit":limit};
			AppConnector.request(urlParams).then(
				function(data){
					progressIndicatorElement.progressIndicator({
						'mode' : 'hide'
					});
					if(data.success){
						window.location.href='index.php?module=TyunWebBuyService&view=List&public=exportdata';
					}else{
						var  params = {text : data.error.message, title : '提示',type:'error'};
						Vtiger_Helper_Js.showMessage(params);
					}
				}
			);
		});
		// 已归档数据导出
        $(".exportdataReconciliation").click(function(){
            var progressIndicatorElement = jQuery.progressIndicator({
                'message' : "努力处理中请稍等...",
                'position' : 'html',
                'blockInfo' : {
                    'enabled' : true
                }
            });
            var searchParamsPreFix = 'BugFreeQuery';
            var rowOrder = "";
            var $searchRows = $("tr[id^=SearchConditionRow]");
            $searchRows.each(function(){
                rowOrder += $(this).attr("id")+",";
            });

            eval("$('#"+searchParamsPreFix+"_QueryRowOrder')").attr("value",rowOrder);
            var limit = $('#limit').val();
            var o = {};
            var a = $('#SearchBug').serializeArray();
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
            var urlParams = {"module":"TyunWebBuyService","action":"BasicAjax","mode":"exportdataReconciliation","page":1,"BugFreeQuery":form,"limit":limit};
            AppConnector.request(urlParams).then(
                function(data){
                    progressIndicatorElement.progressIndicator({
                        'mode' : 'hide'
                    });
                    if(data.success){
                        window.location.href='index.php?module=TyunWebBuyService&view=List&public=exportdata';
                    }else{
                        var  params = {text : data.error.message, title : '提示',type:'error'};
                        Vtiger_Helper_Js.showMessage(params);
                    }
                }
            );
        });
		$(".exportdatayun").click(function(){
			var progressIndicatorElement = jQuery.progressIndicator({
				'message' : "努力处理中请稍等...",
				'position' : 'html',
				'blockInfo' : {
					'enabled' : true
				}
			});
			var searchParamsPreFix = 'BugFreeQuery';
			var rowOrder = "";
			var $searchRows = $("tr[id^=SearchConditionRow]");
			$searchRows.each(function(){
				rowOrder += $(this).attr("id")+",";
			});

			eval("$('#"+searchParamsPreFix+"_QueryRowOrder')").attr("value",rowOrder);
			var limit = $('#limit').val();
			var o = {};
			var a = $('#SearchBug').serializeArray();
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
			var urlParams = {"module":"TyunWebBuyService","action":"BasicAjax","mode":"exportdatayun","page":1,"BugFreeQuery":form,"limit":limit};
			AppConnector.request(urlParams).then(
				function(data){
					progressIndicatorElement.progressIndicator({
						'mode' : 'hide'
					});
					if(data.success){
						window.location.href='index.php?module=TyunWebBuyService&view=List&public=exportdatayun';
					}
				}
			);
		});
	},
	cancelOrder:function(){
		$('body').on('click','.cancelOrder',function(){
			var old_advancesmoney = $(this).attr('data-status');
			var me = this;
			var $select_tr = $(this).closest('tr');
			var thisValue=$(this).data('value');
			var recordId = $($select_tr).data('id');
			var msg = {
				'message': '订单取消',
				"width":"800px",
				'action':function(){
					var refundamount=$('#refundamount').val();
					if(refundamount<=0) {
						var params = {text: '退款金额大于0!', title: '提示'};
						Vtiger_Helper_Js.showMessage(params);
						return false;
					}
					if(refundamount>thisValue){
						var  params = {text : '退款金额大于订单金额!', title : '提示'};
						Vtiger_Helper_Js.showMessage(params);
						return false;
					}
					return true;
				}
			};


			Vtiger_Helper_Js.showConfirmationBox(msg).then(
				function(e) {
					var module = app.getModuleName();
					var postData = {
						"module": module,
						"action": "BasicAjax",
						"record": recordId,
						'mode': 'cancelOrder',
						'refundamount':$('#refundamount').val()
					}

					var Message = app.vtranslate('正在处理...');

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
									var  params = {text : data.result.msg, title : '提示',type:'success'};
									Vtiger_Helper_Js.showMessage(params);
									$select_tr.find('')
									//location.reload();
								}else{
									var  params = {text : data.result.msg, title : '提示',type:'error'};
									Vtiger_Helper_Js.showMessage(params);
								}

							} else {
								var  params = {text : '更改失败', title : '提示'};
								Vtiger_Helper_Js.showMessage(params);
							}
						},
						function(error,err){}
					);
				},function(error, err){}
			);
			$('.modal-body').append('<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0;"><tbody>'+
				'<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">退款金额:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><input name="refundamount" id="refundamount" type="text" value="" /></span></div></td></tr></tbody></table>');

		});
	},
	signContract:function(){
		$('body').on('click','.signContract',function(){
			var me = this;
			var $select_tr = $(this).closest('tr');
			var recordId = $select_tr.data('id');
			var msg = {
				'message': '<h5>签收合同</h5>',
				"width":"600px"
			};

			//var newcontractname=$('#newcontractname').val();
			Vtiger_Helper_Js.showConfirmationBox(msg).then(
				function(e) {
					var module = app.getModuleName();
					var postData = {
						"module": module,
						"action": "BasicAjax",
						"record": recordId,
						'mode': 'signContract'
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
								if(data.result.flag){
									var  params = {text : data.result.msg, title : '提示',type:'success'};
									Vtiger_Helper_Js.showMessage(params);
									$select_tr.find('.contractname').text(newcontractname);
									//location.reload();
								}else{
									var  params = {text : data.result.msg, title : '提示',type:'error'};
									Vtiger_Helper_Js.showMessage(params);
								}
							} else {
								var  params = {text : '更改失败', title : '提示'};
								Vtiger_Helper_Js.showMessage(params);
							}
						},
						function(error,err){}
					);
				},function(error, err){}
			);

			/*var str = '<input name="contractname" id="contractname" type="text" value="'+$(me).data('contractname')+'" readonly/>';
			$('.modal-body').append('<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0;"><tbody>'+
				'<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">新合同编号:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><input name="newcontractname" id="newcontractname" type="text" value="" /></span></div></td></tr></tbody></table>');*/

		});
	},
	offlineReconciliation:function(){
		$('body').on('click','.offlineReconciliation',function(){
			var me = this;
			var $select_tr = $(this).closest('tr');
			var recordId = $select_tr.data('id');
			var msg = {
				'message': '<h5>手工对账</h5>',
				"width":"600px",
				'action':function(){
					var paymentcode=$('.paymentcode');
					var flag=false;
					var currentValueArray=[];
					var msg='';
					$.each(paymentcode,function(key,value){
						var currentValue=$(value).val()
						if(currentValue==''){
							flag=true;
							msg='流水号必填!'
							return false
						}
						if($.inArray(currentValue,currentValueArray)<0){
							currentValueArray.push(currentValue);
						}else{
							flag=true;
							msg='流水号重复!'
							return false;
						}
					});
					if(flag){
						var  params = {text : msg, title : '提示'};
						Vtiger_Helper_Js.showMessage(params);
						return false;
					}
					return true;
				}
			};
			Vtiger_Helper_Js.showConfirmationBox(msg).then(
				function(e) {
					var paymentcode=[];
					$.each($('.paymentcode'),function(key,value){
						paymentcode.push($(value).val())
					});
					var module = app.getModuleName();
					var postData = {
						"module": module,
						"action": "BasicAjax",
						"record": recordId,
						'mode': 'offlineReconciliation',
						'paymentcode':paymentcode
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
								if(data.result.flag){
									var  params = {text : data.result.msg, title : '提示',type:'success'};
									Vtiger_Helper_Js.showMessage(params);
									//$select_tr.find('.contractname').text(newcontractname);
									//location.reload();
								}else{
									var  params = {text : data.result.msg, title : '提示',type:'error'};
									Vtiger_Helper_Js.showMessage(params);
								}
							} else {
								var  params = {text : '更改失败', title : '提示'};
								Vtiger_Helper_Js.showMessage(params);
							}
						},
						function(error,err){}
					);
				},function(error, err){}
			);

			var str = '<input name="contractname" id="contractname" type="text" value="'+$(me).data('contractname')+'" readonly/>';
			$('.modal-body').append('<table class="table Reconciliationtable" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0;"><tbody>'+
				'<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">回款流水号:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><input name="paymentcode[]" class="paymentcode" type="text" value="" /><span class="addReconciliation" style="font-size: 30px;margin-left:5px;cursor:pointer;">+</span></span></div></td></tr></tbody></table>');

		});
		$('body').on('click','.addReconciliation',function(){
			if($('.paymentcode').length>=5)return false;
			$('.Reconciliationtable tbody').append('<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">回款流水号:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><input name="paymentcode[]" class="paymentcode" type="text" value="" /><span class="deletedReconciliation" style="font-size: 30px;margin-left:5px;cursor:pointer;">-</span></span></div></td></tr>');
		});
		$('body').on('click','.deletedReconciliation',function(){
			$(this).closest('tr').remove();
		});
	},
	showProductName:function(){
		$('body').on('click',".productname",function(){
			var msg = {
				'message': '<h5>版本详情</h5><hr>',
				"width":"600px"
			};
			var thisInstance=this;
			Vtiger_Helper_Js.showConfirmationBox(msg).then(
			);
			$('.modal-body').append($(thisInstance).data('content'));

		})
	},
    filedAndReconciliationResult:function () {
		//点击归档
        $(".startToFiled").click(function(){
            var o = {};
            var a = $('#SearchBug').serializeArray();
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
            var urlParams = {"module":"TyunWebBuyService","action":"BasicAjax","mode":"getSearchWhereContent","BugFreeQuery":form};
            AppConnector.request(urlParams).then(
                function(data){
                    if(data.success){
                    	if(data.result.data==""){
                             var  params = {text :'请添加筛选条件！', title : '提示',type:'error'};
                             Vtiger_Helper_Js.showMessage(params);
                             return false;
						}
                        var msg = {
                            'message': '<h5>归档</h5><hr>',
                            "width":"400px"
                        };
                        var thisInstance=this;
                        Vtiger_Helper_Js.showConfirmationBox(msg).then(
                        	function (){
                                var progressIndicatorElement = jQuery.progressIndicator({
                                    'message' : "努力处理中请稍等...",
                                    'position' : 'html',
                                    'blockInfo' : {
                                        'enabled' : true
                                    }
                                });
                                var urlParams = {"module":"TyunWebBuyService","action":"BasicAjax","mode":"startToFiled","BugFreeQuery":form};
                                AppConnector.request(urlParams).then(
                                    function(data){
                                        progressIndicatorElement.progressIndicator({
                                            'mode' : 'hide'
                                        });
                                        if(data.success){
                                        	console.log(data);
											
                                        	if(data.result.success==1){
                                                alert(data.result.data);
                                                $("#PostQuery").click();
											}else{
                                                var  params = {text : data.result.data, title : '提示',type:'error'};
                                                Vtiger_Helper_Js.showMessage(params);
											}
                                        }else{
                                            var  params = {text : data.error.message, title : '提示',type:'error'};
                                            Vtiger_Helper_Js.showMessage(params);
                                        }
                                    }
                                );
							}
                        );
                        $('.modal-body').append(data.result.data);
                    }else{
                       /* var  params = {text : data.error.message, title : '提示',type:'error'};
                        Vtiger_Helper_Js.showMessage(params);*/
                    }
                }
            );
		});
        // 点击对账
        $(".reconciliationResult").click(function(){
            var o = {};
            var a = $('#SearchBug').serializeArray();
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
            var urlParams = {"module":"TyunWebBuyService","action":"BasicAjax","mode":"getSearchWhereContent","BugFreeQuery":form};
            AppConnector.request(urlParams).then(
                function(data){
                    if(data.success){
                        var msg = {
                            'message': '<h5>对账</h5><hr>',
                            "width":"400px"
                        };
                        if(data.result.data){

						}else{
                            var  params = {text :'请添加筛选条件！', title : '提示',type:'error'};
                            Vtiger_Helper_Js.showMessage(params);
                            return false;
						}
                        var thisInstance=this;
                        Vtiger_Helper_Js.showConfirmationBox(msg).then(
                        	function(){
                                var progressIndicatorElement = jQuery.progressIndicator({
                                    'message' : "努力处理中请稍等...",
                                    'position' : 'html',
                                    'blockInfo' : {
                                        'enabled' : true
                                    }
                                });
                                urlParams ={"module":"TyunWebBuyService","view":"List","orderType":"reconciliation","AJAX":"AJAX","BugFreeQuery":form};
                                AppConnector.request(urlParams).then(
                                    function(data){
                                        progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                                        data=JSON.parse(data);
                                    	if(data.success=='true'){
                                    		if(data.jump=='true'){
                                                window.location.href="index.php?module=TyunWebBuyService&view=List&orderType=reconciliation&record="+data.recordId;
											}else{
                                                $("#PostQuery").click();
                                                alert(data.message);
                                                return false;
											}
										}else{
                                            var  params = {text : data.message, title : '提示',type:'error'};
                                            Vtiger_Helper_Js.showMessage(params);
										}
								    }
								);
						    }
                        );
                        if(data.result.data){
                            $('.modal-body').append(data.result.data);
						}
                    }else{
                        /* var  params = {text : data.error.message, title : '提示',type:'error'};
                         Vtiger_Helper_Js.showMessage(params);*/
                    }
                }
            );
        });
        //归档后数据点击对账
        $(".reconciliationResultAgain").click(function(){
            var o = {};
            var a = $('#SearchBug').serializeArray();
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
            var urlParams = {"module":"TyunWebBuyService","action":"BasicAjax","mode":"getSearchWhereContent","BugFreeQuery":form};
            AppConnector.request(urlParams).then(
                function(data){
                    if(data.success){
                        var msg = {
                            'message': '<h5>对账</h5><hr>',
                            "width":"400px"
                        };
                        if(data.result.data){

                        }else{
                            var  params = {text :'请添加筛选条件！', title : '提示',type:'error'};
                            Vtiger_Helper_Js.showMessage(params);
                            return false;
                        }
                        var thisInstance=this;
                        Vtiger_Helper_Js.showConfirmationBox(msg).then(
                            function(){
                                var progressIndicatorElement = jQuery.progressIndicator({
                                    'message' : "努力处理中请稍等...",
                                    'position' : 'html',
                                    'blockInfo' : {
                                        'enabled' : true
                                    }
                                });
                                urlParams ={"module":"TyunWebBuyService","view":"List","orderType":"reconciliation","again":"again","AJAX":"AJAX","BugFreeQuery":form};
                                AppConnector.request(urlParams).then(
                                    function(data){
                                        progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                                        data=JSON.parse(data);
                                        if(data.success=='true'){
                                            if(data.jump=='true'){
                                                window.location.href="index.php?module=TyunWebBuyService&view=List&orderType=reconciliation&record="+data.recordId;
                                            }else{
                                                $("#PostQuery").click();
                                                alert(data.message);
                                                return false;
                                            }
                                        }else{
                                            var  params = {text : data.message, title : '提示',type:'error'};
                                            Vtiger_Helper_Js.showMessage(params);
                                        }
                                    }
                                );
                            }
                        );
                        if(data.result.data){
                            $('.modal-body').append(data.result.data);
                        }
                    }else{
                        /* var  params = {text : data.error.message, title : '提示',type:'error'};
                         Vtiger_Helper_Js.showMessage(params);*/
                    }
                }
            );
        });
        // 对账后 对账出错的导出
        $(".exportDataReconciliationResult").click(function(){
            var record = $("#exportRecord").val();
            window.location.href='index.php?module=TyunWebBuyService&view=List&record='+record+'&public=exportDataReconciliationResult';
		});

    },
	registerEvents : function(){
		this._super();
		this.rebindContract();
		this.cancelOrder();
		this.signContract();
		this.offlineReconciliation();
		this.exportData();
		this.showProductName();
		this.filedAndReconciliationResult();
	}

});