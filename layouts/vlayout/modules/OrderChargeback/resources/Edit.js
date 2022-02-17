/*+***********************
 * 退款申请流程
 ***********************/

Vtiger_Edit_Js("OrderChargeback_Edit_Js",{},{
	rowSequenceHolder : false,
	registerReferenceSelectionEvent : function(container) {
		this._super(container);
		var thisInstance = this;
		
		//切换工作流 按名称判断是否内部
		$('input[name="workflowsid"]',container).on(Vtiger_Edit_Js.referenceSelectionEvent,function (e,data){
			thisInstance.loadWidgetNote(data['selectedName']);
		});
		////workflowschange(data['record']);
		//function workflowschange(record){if(sourcemodule=='Workflows'){var workflowsid=$('input[name="workflowsid"]');if(workflowsid.val().length>0){}}}
		
		/*外部工单有合同*/
		$('input[name="servicecontractsid"]',container).on(Vtiger_Edit_Js.referenceSelectionEvent,function (e,data){servicecontractschange()});
		//合同变更事件
		//加载合同和产品信息
		function servicecontractschange(){
			var progressIndicatorElement = jQuery.progressIndicator({ 'message' : '合同信息加载中...','blockInfo':{'enabled':true }});
			var params = {'module':'OrderChargeback','action':'BasicAjax','record':$('input[name="servicecontractsid"]').val(),'salesorderid':$('input[name="record"]').val(),"mode":"getInvoiceSalesorderList"};
			AppConnector.request(params).then(
				function(data){
					if(data.success){
						progressIndicatorElement.progressIndicator({ 'mode' : 'hide'});
						$("tr.removetr").remove();
                        $('input[name=accountid_display]').val(data.result.accountname);
                        $('input[name="accountid"]').val(data.result.id);
                        $('select[name="assigned_user_id"]').val(data.result.userid);
                        $('input[name="contractamount"]').val(data.result.total);
                        $('input[name="receivingmoney"]').val(data.result.rtotal);
                        var text=$('select[name="assigned_user_id"]').find("option:selected").text();
                        $('select[name="assigned_user_id"]').next('.chzn-container-single').find('span').text(text);
						//当选择了服务合同时，根据选择的服务合同自动带出合同主体，且不支持编辑；
						$('select[name="invoicecompany"]').val(data.result.invoicecompany);
						var text=$('select[name="invoicecompany"]').find("option:selected").text();
						$('select[name="invoicecompany"]').next('.chzn-container-single').find('span').text(text);
						$('select[name="invoicecompany"]').next('.chzn-container-single').find('.chzn-drop').hide();
						var jsons = data.result['salesorderlist'];
						var str='';
						$.each(jsons,function(n,json){
							str+='<tr class="removetr"><td>&nbsp;&nbsp;<input type="checkbox" value="'+json.salesorderid+'" name="salesorderbid[]" class="entryCheckBox salesorderchild salesorderchildn'+json.salesorderid+'" data-id="'+json.salesorderid+'"></td><td>'+json.salesorder_no+
							'</td><td>'+json.subject+'</td><td>'+json.workflowsnode+'</td><td>'+json.modulestatus+'</td><td>'+json.salesorderowner+'</td>';
                            if(json.productlist!=''){
                                str+='<tr class="removetr"><td colspan="8"><table class="table table-striped blockContainer lineItemTable tableproduct detailview-table"><thead><tr><th><img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="1499" style="display: none;"><img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="1499" style="display: inline;"></th><th>产品明称</th><th>所属套餐</th><th>数量</th><th>年限(月)</th><th>市场价格</th><th>人力成本</th><th>外采成本</th></tr></thead><tbody>';
                                $.each(json.productlist,function(n,productl){
                                 str+='<tr><td style="text-align:center;"><input type="checkbox" value="'+productl.salesorderproductsrelid+'" name="salesorderproduct['+json.salesorderid+'][]" class="entryCheckBox salesordergrandson salesordergrandson'+json.salesorderid+'" data-id="'+json.salesorderid+'"> </td><td>'+productl.productname+'</td><td>'+productl.productcomboname+'</td><td>'+productl.productnumber+'</td><td>'+productl.agelife+'</td><td>'+productl.realprice+'</td><td>'+productl.costing+'</td><td>'+productl.purchasemount+'</td></tr>';
                                });
                                str+="</tbody></table></td></tr>";
                            }
						});
						if(str==''){
							str='<tr class="removetr warning"><td style="text-align:center;" colspan="6">没有工单</td></tr>';
						}
						$('#insertproduct').after(str);
                        var jsons = data.result['invoicelist'];
                        var str='';
                        $.each(jsons,function(n,json){
                            var checkf=json.invoicestatus=='normal'?'<input type="checkbox" value="'+json.invoiceextendid+'" class="entryCheckBox invoicechild" name="invocieid['+json.invoiceid+'][]">':(json.invoicestatus=='tovoid'?'':(json.invoicestatus=='redinvoice'?'':'<input type="checkbox" value="'+json.invoiceextendid+'" class="entryCheckBox invoicechild" name="invocieid['+json.invoiceid+'][]">'));
                            str+='<tr class="removetr"><td style="text-align:center;">'+checkf+'</td><td>'+json.invoice_noextend+
                            '</td><td>'+json.invoicecodeextend+'</td><td>'+json.billingtimeextend+'</td><td>'+json.totalandtaxextend+'</td><td>'+json.commoditynameextend+'</td><td>'+(json.invoicestatus=='normal'?'<span class="label btn-success">正常</span>':(json.invoicestatus=='tovoid'?'<span class="label btn-inverse">作废</span>':(json.invoicestatus=='redinvoice'?'<span class="label btn-danger">红冲</span>':'<span class="label btn-success">正常1</span>')))+'</td><td>'+(json.operator==undefined?'':json.operator)+'</td><td>'+(json.operatortime==undefined?'':json.operatortime)+'</td></tr>';
                        });
                        if(str==''){
                            str='<tr class="removetr warning"><td align="center" style="text-align:center;" colspan="9">没有发票</td></tr>';
                        }
                        $('#insertinvoice').after(str);
                        var jsons = data.result['rp'];
                        var str='';
                        $.each(jsons,function(n,json){
                            str+='<tr class="removetr"><td>'+json.owncompany+
                            '</td><td>'+json.paytitle+'</td><td>'+json.reality_date+'</td><td>'+json.standardmoney+'</td><td>'+json.exchangerate+'</td><td>'+json.unit_price+'</td><td>'+json.sumextra_price+'</td></tr>';
                        });
                        if(str==''){
                            str='<tr class="removetr warning"><td align="center" style="text-align:center;" colspan="7">没有回款</td></tr>';
                        }
                        $('#insertreceivepay').after(str);
                        $('.entryCheckBox').iCheck({
                            checkboxClass: 'icheckbox_minimal-blue'
                        });

					}
				},
				function(error){}
			);
		}

		//编辑工单
		var record=jQuery('input[name="record"]').val();
		if(record>0){
			//按工作流显示数据?
			//workflowschange($('input[name="workflowsid"]').val());thisInstance.loadWidgetNote($('.widgetContainer_salesorderworkflows'),$('input[name="workflowsid"]').val());
			if($('#servicecontractsid_display').val()){
			//编辑页面载入显示合同并加载合同下产品信息
				//thisInstance.hasContract();
				//servicecontractschange();
				//逻辑上选定合同以合同为准//禁止再修改工作流和合同
				//$('#SalesOrder_editView_fieldName_workflowsid_select,#SalesOrder_editView_fieldName_workflowsid_clear,#SalesOrder_editView_fieldName_servicecontractsid_select,#SalesOrder_editView_fieldName_servicecontractsid_clear,#SalesOrder_editView_fieldName_accountid_clear,#SalesOrder_editView_fieldName_accountid_select').parent().remove();
				$('#SalesOrder_editView_fieldName_servicecontractsid_select,#SalesOrder_editView_fieldName_servicecontractsid_clear,#SalesOrder_editView_fieldName_workflowsid_clear,#SalesOrder_editView_fieldName_workflowsid_select').parent().remove();
			}else{
				$('.tableadv').addClass('hide').find('input').attr("disabled","disabled");
				$('.tablecust').removeClass('hide').find('input').removeAttr("disabled");
				//按工单加载产品信息
				//thisInstance.loadWidgetNo(0,record);
			}
			//potentialchange();
		}else{
			//新建默认标准合同 禁用产品选择
			$('.tablecust').find('input').attr("disabled","disabled");
		}
	},
	

	/**
	 * 弹出参数？没用吧
	 */
	getPopUpParams : function(container) {
		var params = this._super(container);
        var sourceFieldElement = jQuery('input[class="sourceField"]',container);
		if(sourceFieldElement.attr('name') == 'contact_id' || sourceFieldElement.attr('name') == 'potential_id') {
			var form = this.getForm();
			var parentIdElement  = form.find('[name="accountid"]');
			if(parentIdElement.length > 0 && parentIdElement.val().length > 0) {
				var closestContainer = parentIdElement.closest('td');
				params['related_parent_id'] = parentIdElement.val();
				params['related_parent_module'] = closestContainer.find('[name="popupReferenceModule"]').val();
			} else if(sourceFieldElement.attr('name') == 'potential_id') {
				parentIdElement  = form.find('[name="contact_id"]');
				if(parentIdElement.length > 0 && parentIdElement.val().length > 0) {
					closestContainer = parentIdElement.closest('td');
					params['related_parent_id'] = parentIdElement.val();
					params['related_parent_module'] = closestContainer.find('[name="popupReferenceModule"]').val();
				}
			}
        }else if(sourceFieldElement.attr('name') == 'servicecontractsid'){
			//5.53版本增加根据原因找合同
			params['refundReason']=$("select[name='refundreason']").val();
		}
        return params;
    },
	/**
	 * 貌似是没用的
	 */
	searchModuleNames : function(params) {
		var aDeferred = jQuery.Deferred();
		if(typeof params.module == 'undefined') {
			params.module = app.getModuleName();
		}
		if(typeof params.action == 'undefined') {
			params.action = 'BasicAjax';
		}
		if (params.search_module == 'Contacts' || params.search_module == 'Potentials') {
			var form = this.getForm();
			var parentIdElement  = form.find('[name="accountid"]');
			if(parentIdElement.length > 0 && parentIdElement.val().length > 0) {
				var closestContainer = parentIdElement.closest('td');
				params.parent_id = parentIdElement.val();
				params.parent_module = closestContainer.find('[name="popupReferenceModule"]').val();
			} else if(params.search_module == 'Potentials') {
				parentIdElement  = form.find('[name="contact_id"]');
				if(parentIdElement.length > 0 && parentIdElement.val().length > 0) {
					closestContainer = parentIdElement.closest('td');
					params.parent_id = parentIdElement.val();
					params.parent_module = closestContainer.find('[name="popupReferenceModule"]').val();
				}
			}
		}
		AppConnector.request(params).then(
			function(data){
				aDeferred.resolve(data);
			},
			function(error){
				aDeferred.reject();
			}
		)
		return aDeferred.promise();
	},

	registerEventForWorkflows:function(){
		var workflows=$('input[name=workflowsid]');
		workflows.on('input',function(){
			alert(workflows.val());
		});
	},
	getLineItemContentsContainer:function(){
		return $('#lineItemTab');
	},

    //复选框选一事件
    checkboxselect:function(){
        ////发票的选定复选框事件
        $('#EditView').on('ifClicked','.invoiceall',function(event){
            if(event.target.checked){
                $('.invoicechild').iCheck('uncheck');
            }else{
                $('.invoicechild').iCheck('check');
            }
        });
        $('#EditView').on('ifUnchecked','.invoicechild',function(){
                $('.invoiceall').iCheck('uncheck');

        });
        $('#EditView').on('ifChecked','.invoicechild',function(){
            //$('.invoicechild').attr('checked',true);
            var flag=false;
            $.each($('.invoicechild'),function(n,k){
                if($(k).attr('checked')==undefined){
                    flag=true;
                    return false;
                }
            });
            if(!flag){
                $('.invoiceall').iCheck('check');
            }
        });
        ///工单的复选框事件
        //最上级复选框
        $('#EditView').on('ifClicked','.salesoderall',function(event){
            if(event.target.checked){
                $('.salesorderchild').iCheck('uncheck');
                $('.salesordergrandson').iCheck('uncheck');
            }else{
                $('.salesorderchild').iCheck('check');
                $('.salesordergrandson').iCheck('check');
            }
        });
        //次级复选框
        $('#EditView').on('ifUnchecked ifClicked','.salesorderchild',function(event){

                if(event.type=='ifClicked') {
                    $('.salesordergrandson' + $(this).data('id')).iCheck('uncheck');
                    $('.salesoderall').iCheck('uncheck');
                }
        });
        $('#EditView').on('ifChecked','.salesorderchild',function() {
            $('.salesordergrandson' + $(this).data('id')).iCheck('check');
            var flag = false;
            $.each($('.salesorderchild'), function (n, k) {
                if ($(k).attr('checked') == undefined) {
                    flag = true;
                    return false;
                }
            });
            if (!flag) {
                $('.salesoderall').iCheck('check');
            }
        });
        //三级复选框
        $('#EditView').on('ifUnchecked','.salesordergrandson',function(){
            $('.salesorderchildn'+$(this).data('id')).iCheck('uncheck');
            $('.salesoderall').iCheck('uncheck');

        });
        $('#EditView').on('ifChecked','.salesordergrandson',function() {
            var flag = false;
            $.each($('.salesordergrandson' + $(this).data('id')), function (n, k) {
                if ($(k).attr('checked') == undefined) {
                    flag = true;
                    return false;
                }
            });
            if (!flag) {
                $('.salesorderchildn'+$(this).data('id')).iCheck('check');
            }
            var flag = false;
            $.each($('.salesorderchild'), function (n, k) {
                if ($(k).attr('checked') == undefined) {
                    flag = true;
                    return false;
                }
            });
            if (!flag) {
                $('.salesoderall').iCheck('check');
            }
        });


    },

	//有合同
	hasContract:function(){
        var record=jQuery('input[name="record"]').val();
        if(record>0) {
            //按工作流显示数据?
            //workflowschange($('input[name="workflowsid"]').val());thisInstance.loadWidgetNote($('.widgetContainer_salesorderworkflows'),$('input[name="workflowsid"]').val());
            if ($('#servicecontractsid_display').val()) {
                //编辑页面载入显示合同并加载合同下产品信息
                //thisInstance.hasContract();
                $('.tableadv').removeClass('hide').find('input').removeAttr("disabled");
                $('.tablecust').addClass('hide').find('input').attr("disabled", "disabled");
                //$('#SalesOrder_editView_fieldName_workflowsid_select,#SalesOrder_editView_fieldName_workflowsid_clear,#SalesOrder_editView_fieldName_servicecontractsid_select,#SalesOrder_editView_fieldName_servicecontractsid_clear,#SalesOrder_editView_fieldName_account_id_clear,#SalesOrder_editView_fieldName_account_id_select').parent().remove();
                $('#OrderChargeback_editView_fieldName_servicecontractsid_select,#OrderChargeback_editView_fieldName_servicecontractsid_clear,#OrderChargeback_editView_fieldName_workflowsid_clear,#OrderChargeback_editView_fieldName_workflowsid_select').parent().remove();
            }
        }
        //$('input[name="productid"],input[name="productid_display"]').attr("disabled","disabled");	
	},
	showPopup : function(params) {
		var aDeferred = jQuery.Deferred();
		var popupInstance = Vtiger_Popup_Js.getInstance();
		popupInstance.show(params, function(data){
			aDeferred.resolve(data);
		});
		return aDeferred.promise();
	},

	//绑定收缩效果
	registerBlockAnimationEvent : function(){
		var detailContentsHolder = $('.editViewContainer');
		detailContentsHolder.on('click','.blockToggle',function(e){
			var currentTarget =  jQuery(e.currentTarget);
			var blockId = currentTarget.data('id');
			var closestBlock = currentTarget.closest('.detailview-table');
			var bodyContents = closestBlock.find('tbody');
			var data = currentTarget.data();
			var module = app.getModuleName();
			var hideHandler = function() {
				bodyContents.hide('slow');
				app.cacheSet(module+'.'+blockId, 0)
			}
			var showHandler = function() {
				bodyContents.show();
				app.cacheSet(module+'.'+blockId, 1)
			}
			var data = currentTarget.data();
			if(data.mode == 'show'){
				hideHandler();
				currentTarget.hide();
				closestBlock.find("[data-mode='hide']").show();
			}else{
				showHandler();
				currentTarget.hide();
				closestBlock.find("[data-mode='show']").show();
			}
		});

	},

	registerRecordPreSaveEvent : function(form) {
		var thisInstance = this;
		var editViewForm = this.getForm();
		editViewForm.on(Vtiger_Edit_Js.recordPreSave,function(e,data){
			var refundreason=$("select[name='refundreason']").val();
			if(refundreason=='无合同退款'){
				//这几个不填
				$("input[name='servicecontractsid_display']").val('');
				$("input[name='servicecontractsid']").val('');
				$("input[name='accountid']").val('');
				$("input[name='accountid_display']").val('');
				$("input[name='contractamount']").val('');
				$("input[name='receivingmoney']").val('');
			}else{
				if($("input[name='servicecontractsid_display']").val()==''||$("input[name='servicecontractsid']").val()==''){
					alert('请选择服务合同');
					e.preventDefault();
					return false;
				}
			}
			if(refundreason=='退款不终止业务'||refundreason=='退款终止业务') {
				//退款不终止业务、退款终止业务，框架合同 进行金额校验。累计退款金额 + 本次申请退款金额  ＞ 合同金额，则不允许用户发起退款申请
				$.ajax({
					type: 'post',
					url: 'index.php?module=OrderChargeback&action=BasicAjax&mode=checkContractAmount',
					data: {'serviceId': $('input[name="servicecontractsid"]').val(), 'record': $('input[name="record"]').val(),'amount':$('input[name="refundamount"]').val()},
					async: false,
					error: function (data) {
						e.preventDefault();
						return false;
					},
					success: function (data) {
						if (!data.result.success) {
							var msg={
								'width':400,
								'message':'你的合同金额为：{'+data.result.total+'}，累计退款金额 为{'+data.result.cumulativeAmount+'}  、 本次申请退款金额为{'+$('input[name="refundamount"]').val()+'} ，非框架合同累计退款金额 + 本次申请退款金额  ＞ 合同金额，请修改后重新提交'
							};
							Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
							},function(error, err) {});
							e.preventDefault();
							return false;
						}
					},
				});
			}
		});
	},

	/**
	 * 退款原因变化事件
	 */
	refundReasonChange:function(){
		$("select[name='refundreason']").change(function () {
			var refundreason=$(this).val();
			if(refundreason=='无合同退款'){
				//无合同退款直接隐藏
				$("#servicecontractsid_display").closest('td').prev().hide();
				$("#servicecontractsid_display").closest('td').hide();
				$("#accountid_display").closest('td').prev().hide();
				$("#accountid_display").closest('td').hide();
				$("#accountid_display").closest('td').prev().hide();
				$("#accountid_display").closest('td').hide();
				$("input[name='contractamount']").closest('td').prev().hide();
				$("input[name='contractamount']").closest('td').hide();
				$("input[name='receivingmoney']").closest('td').prev().hide();
				$("input[name='receivingmoney']").closest('td').hide();
				//解决提示偏移问题
				$("input[name='issubmit']").click();
				$("input[name='issubmit']").click();
			}else{
				//其他展示
				$("#servicecontractsid_display").closest('td').prev().show();
				$("#servicecontractsid_display").closest('td').show();
				$("#accountid_display").closest('td').prev().show();
				$("#accountid_display").closest('td').show();
				$("#accountid_display").closest('td').prev().show();
				$("#accountid_display").closest('td').show();
				$("input[name='contractamount']").closest('td').prev().show();
				$("input[name='contractamount']").closest('td').show();
				$("input[name='receivingmoney']").closest('td').prev().show();
				$("input[name='receivingmoney']").closest('td').show();
			}
		});
	},

	registerBasicEvents:function(container){
		this._super(container);
		this.registerReferenceSelectionEvent(container);
	},
	
	registerEvents: function(){
		this._super();
		var hidden = $("<input>").attr("type", "hidden").attr("id", "no-repeatid").val("0").appendTo("body");
		/*合同金额公司名称客户编号不直接编辑*/
		$('#OrderChargeback_editView_fieldName_contractamount,#OrderChargeback_editView_fieldName_customerno,#accountid_display,#servicecontractsid_display,input[name="receivingmoney"]').attr('readonly',true);
		/*公司信息只读*/
		$('#OrderChargeback_editView_fieldName_accountid_clear,#OrderChargeback_editView_fieldName_accountid_select').parent().remove();
		this.registerBlockAnimationEvent();
 		$('#OrderChargeback_editView_fieldName_issubmit').attr('data-content','<font color="red">勾选<<b>确认提交审核</b>>后退款申请才会被审核</font>');
        	$('#OrderChargeback_editView_fieldName_issubmit').popover('show');
        $('.popover').css('z-index',20);
		//this.registerEventForCkEditor();
		//this.registerAddingNewProducts();
		//this.registerDeleteLineItemEvent();
		//this.registerProductPopup();
		//this.registerEditProductBind();
		//this.registerForTogglingBillingandShippingAddress();
		//this.registerEventForCopyAddress();
        this.checkboxselect();
        this.hasContract();
        this.refundReasonChange();
		var record=$('input[name="record"]').val();
		if(record>0) {
			$("select[name='refundreason']").trigger('change');
		}
		this.registerRecordPreSaveEvent();
	}
});


