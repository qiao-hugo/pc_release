/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.	
 *************************************************************************************/
Vtiger_Edit_Js("ReceivedPayments_Edit_Js",{},{
	registerReferenceSelectionEvent : function(container) {
		this._super(container);
		var thisInstance = this;
		
		jQuery('input[name="relatetoid"]',container).on(Vtiger_Edit_Js.referenceSelectionEvent, function (e,data){thisInstance.servicecontractschange();});		
		jQuery('input[name="relatetoid"]',container).on(Vtiger_Edit_Js.referenceSelectionEvent, function (e,data){thisInstance.servicecontractschange2();});
	},
	servicecontractschange2 : function(){
		//刷新自定义的插件显示，早就用过了，却没有仔细研究
		var widgetContainer = $(".widgetContainer_receivehistory");
		var sc=$('input[name="relatetoid"]').val(); //合同id 
		var record = $('input[name="record"]').val();//回款id
		var prices = $('input[name="unit_price"]').val();//回款金额
        var requestmodule = 'ReceivedPayments';
		var urlParams ='module=ServiceContracts&view=Detail&mode=getservicecontractsinfo&record='+sc+"&receivepayid="+record+"&requestmodule="+requestmodule;
		var urlParam  ='module=ServiceContracts&view=Detail&mode=getservicecontractsinfo&record='+sc+"&receivepayid="+record+"&ischuna=true&requestmodule="+requestmodule;
		if(sc && !$('input[name="relatetoid"]').attr('disabled')){
			params = {
					'type' : 'GET',
					'dataType': 'html',
					'data' : urlParams
				};
			widgetContainer.progressIndicator({});
		AppConnector.request(params).then(
		function(data){
				widgetContainer.progressIndicator({'mode': 'hide'});
				widgetContainer.html(data);
			});
		}else if(sc && $('input[name="relatetoid"]').attr('disabled')){
			//出纳也能显示不能编辑的产品明细跟回款历史;
			params = {
					'type' : 'GET',
					'dataType': 'html',
					'data' : urlParam
				};
			widgetContainer.progressIndicator({});
		AppConnector.request(params).then(
		function(data){
				widgetContainer.progressIndicator({'mode': 'hide'});
				widgetContainer.html(data);
			})
		}
	},
	servicecontractschange : function() {	
		var sc=$('input[name="relatetoid"]').val();
		if(sc !==""){
			/*var params = {
					'module' : 'ServiceContracts',
					'action' : 'BasicAjax', 
					'record' : sc,
					'mode' : 'getservicecontractsinfo'
					};*/
			var params = '?module=ServiceContracts&action=BasicAjax&mode=getservicecontractsinfo&record='+sc+"&receivepayid="+$('input[name="record"]').val();
				
			AppConnector.request(params).then(
							function(data){
								//console.log(data);
								if(data.success==true){
									var info = data.result['0']['0'];
									//console.log(data.result);
									var contract_type = (info['contract_type'] == null)?"":info['contract_type'];
									var servicecontracts_smownerId = "";
									if(info){
										//console.log(info);
										var currencytype = (info['currencytype'] == null)?"人民币":info['currencytype'];
                                        var total = (info['total'] == null)?"0":info['total'];
										if(info['label']){
											var last_name = (info['last_name'] == null)?"当前客户无负责人":info['last_name'];
											var departmentname = (info['departmentname'] == null)?"--":info['departmentname'] ;
											//console.log(info['departmentname']);
											var status = (info['status'] == "")?"":info['status'];
											//获取合同负责人id 2017/02/27 gaocl add
											if(status == ""){
                                                servicecontracts_smownerId = info['id'];
											}
											if(last_name == "当前客户无负责人"){
												var string = last_name+currencytype+":"+total;
											}else{
												//var string = "<td>"+info['label']+"</td><td>"+last_name+"["+departmentname+"]<a color='red'>"+status+"</a></td><td>"+currencytype+":"+total+"</td>";
												var string = "<td>"+last_name+"["+departmentname+"]<a color='red'>"+status+"</a></td><td>"+currencytype+':'+total+"</td><td> "+contract_type+"</td><td>"+info['label']+"</td>";
											}
										}else{
											var string = "<td>--</td><td>"+currencytype+":"+total+"</td><td>"+contract_type+"</td><td>无客户</td>";
										}
									}else{
										var string = "<td colspan='3'>此合同下无相关信息</td>";
									}

									//console.log(data.result['0']['1']);
									var receieve = data.result['1']; //该合同下的业绩分成信息;
									//如果对应收会计有编辑权限并且分成表中，能读取到当前合同下回款的分成信息;变可以编辑;
									if(receieve && !$('input[name="relatetoid"]').attr('disabled')){
										//可以编辑
										$("#fallintotable thead").siblings().remove();
                                        $("#fallintotable tbody").remove();
										var scalling = 0;
										var index = 1;
                                        $.each(receieve,function(i,item) {
                                            //追加判断是否离职处理 2017/02/27 gaocl add
                                            if (item['receivedpaymentownname'].indexOf('离职') > -1
												|| servicecontracts_smownerId == item['receivedpaymentownid']) {
                                                scalling += item['scalling'] * 1;
                                            }
                                        });
										//修改第一个业绩负责人分成比例(合计) 2017/02/27 gaocl add
                                        if(scalling > 0) {
                                            $.each(receieve, function (i, item) {
                                                $('#fallintotable').append(aaaaa);
                                                $('.chzn-select').chosen();
                                                //追加判断是否离职处理
                                                var isQuit = "";
                                                var trr = $('#fallintotable tbody tr:eq(0)');
                                                trr.find("td:eq(0) select").val(item['owncompanys']);
                                                trr.find("td:eq(0) select").trigger('liszt:updated');
                                                if (servicecontracts_smownerId == '') {
                                                    trr.find("td:eq(1) select").val('0');
                                                    trr.find("td:eq(1) select").trigger('liszt:updated');
                                                    trr.find("td:eq(2) input").val('0');
                                                    trr.find("td:eq(3) input").val('0');
                                                } else {
                                                    trr.find("td:eq(1) select").val(servicecontracts_smownerId);
                                                    trr.find("td:eq(1) select").trigger('liszt:updated');

                                                    trr.find("td:eq(2) input").val(scalling);

                                                    //根据前台计算分成信息
                                                    re = new RegExp(",", "g");
                                                    var mat = $("input[name='unit_price']").val().replace(re, "");
                                                    trr.find("td:eq(3) input").val((scalling * mat) / 100);
                                                }
                                                return false;
                                            });
                                        }
										$.each(receieve,function(i,item){
                                            //追加判断是否离职处理 2017/02/27 gaocl add
                                            if (item['receivedpaymentownname'].indexOf('离职') > -1
                                                || servicecontracts_smownerId == item['receivedpaymentownid']) {
                                            	return true;
                                            }
											$('#fallintotable').append(aaaaa);
											$('.chzn-select').chosen();
											var trr = $('#fallintotable tbody tr:eq('+index+')');
											trr.find("td:eq(0) select").val(item['owncompanys']);
											trr.find("td:eq(0) select").trigger('liszt:updated');
											trr.find("td:eq(1) select").val(item['receivedpaymentownid']);
											trr.find("td:eq(1) select").trigger('liszt:updated');

											trr.find("td:eq(2) input").val(item['scalling']);

                                            //根据前台计算分成信息
                                            re=new RegExp(",","g");
                                            var mat = $("input[name='unit_price']").val().replace(re,"");
                                            trr.find("td:eq(3) input").val((item['scalling']*mat)/100);
                                            index++;
                                        });
										//2015年6月12日 出纳编辑回款时,只能显示不能编辑分成信息;
									}else if(receieve && $('input[name="relatetoid"]').attr('disabled')){
                                        var aaa = ""
											$.each(receieve,function(i,item){
												aaa +="<tr><td>"+item['owncompanys']+"</td><td>"+item['receiveownid2']+"</td><td>"+item['scalling']+"</td><td>"+item['businessunit']+"</td><td></td></tr>";
											});
										$('#fallintotable').append(aaa);
										$("#fallintotable").find("button").remove();
									}else if(!$('input[name="relatetoid"]').attr('disabled')){
										$("#fallintotable thead").siblings().remove();
										$('#fallintotable').append(aaaaa);
										$('.chzn-select').chosen();
										//对第一条默认的tr 默认分成人进行筛选;
										$("#fallintotable thead").next().find("tr td:eq(1) select").val(info['id']);
										$("#fallintotable thead").next().find("tr td:eq(1) select").trigger('liszt:updated');

									 	//回款分成数据错误问题;
									 	re=new RegExp(",","g");
									 	 var mat = $("input[name='unit_price']").val().replace(re,"");
									 	$("#fallintotable thead").next().find("tr td:eq(2) input").val(100);
									 	$("#fallintotable thead").next().find("tr td:eq(3) input").val(mat);
									}
								 	$("#serviceinfo").html(string);
								 	$("#fallintotable tbody tr:eq(0)").find("button").remove(); 
								}
							})
		}
		
	},
	registerBasicEvents:function(container){
		this._super(container);
		this.registerReferenceSelectionEvent(container);	
	},
	
	/**
	 * @autor wangbin 2015年5月4日 星期一 格式化货币类型 去掉货币值的逗号
	 * @param string;
	 * @return mumber;
	 */
	currencyformat : function(number){
		re=new RegExp(",","g");
	var mat = parseFloat(number.replace(re,""));
		if(isNaN(mat) || number == ''){
			return parseInt(0);
		}else{
			return mat;
		};
	},
	
	
	//wangbin 增加自定义阻止提交事件
	registerRecordPreSaveEvent : function(form) {
		var thisInstance = this;
		var editViewForm = this.getForm();
		editViewForm.on(Vtiger_Edit_Js.recordPreSave,function(e,data){
			if($("#ReceivedPayments_editView_fieldName_owncompany").val().indexOf("请选择") != -1){
				Vtiger_Helper_Js.showMessage({type:'error',text:'请选择正确的公司账号'});
				e.preventDefault(); //阻止提交事件先注释
				return;
			}
			var paytitle=$("input[name='paytitle']").val();
			var paymentchannel=$("select[name='paymentchannel']").val();
			if(paymentchannel!='扫码'&&paytitle==''){
				Vtiger_Helper_Js.showMessage({type:'error',text:'支付渠道是对公转账/支付宝转账时汇款抬头必填'});
				e.preventDefault(); //阻止提交事件先注释
				return;
			}
			var paymentcode=$("input[name='paymentcode']").val();
			// if(paymentchannel!='对公转账'&& paymentcode==''){
			// 	Vtiger_Helper_Js.showMessage({type:'error',text:'支付渠道是支付宝转账/扫码时交易单号必填'});
			// 	e.preventDefault(); //阻止提交事件先注释
			// 	return;
			// }
			var chineseReg=/[\u4E00-\u9FA5]|[\uFE30-\uFFA0]/g;
			if(paymentcode!=''&&chineseReg.test(paymentcode)){
				Vtiger_Helper_Js.showMessage({type:'error',text:'交易单号不允许填写中文'});
				e.preventDefault(); //阻止提交事件先注释
				return;
			}
			if(!$('input[name="relatetoid"]').attr('disabled')){
				if(parseInt($('input[name="relatetoid"]').val())){
					//添加自动分配产品金额注释掉验证
//			if($("#currsubtract").text() !== "0.00"){
//				Vtiger_Helper_Js.showMessage({type:'error',text:'收款金额未填写完整,还剩余'+$("#currsubtract").text()+'收款金额未填写'}); 
//				e.preventDefault(); //阻止提交事件先注释
//				return;
//			}
            //判断业绩所属人是否为空
			var isError = false;
            //$('#fallintotable tbody tr:eq(0) td:eq(1)')
			$('#fallintotable tbody tr').each(
                    function(){
                        //var v = $(this).find("td:eq(1)").find(".chzn-results").find(".result-selected").val();
                        v = $(this).find("td:eq(1) select").val();
                    	if(v == ""){
						Vtiger_Helper_Js.showMessage({type:'error',text:'业绩所属人不能为空'});
                        isError = true;
						e.preventDefault(); //阻止提交事件先注释
						return;
					}
				}
			);

			if(isError){
				return;
			}
			var scalingtotal = parseInt("0");
			$(".scaling").each(
					function(){
						if(parseInt(Number($(this).val())) == 0){
							Vtiger_Helper_Js.showMessage({type:'error',text:'分成比例不能为空'}); 
							e.preventDefault(); //阻止提交事件先注释
							return;
						}
						 scalingtotal += parseInt(Number($(this).val()));
					}	
				);
			if(scalingtotal!==100){
				Vtiger_Helper_Js.showMessage({type:'error',text:'分成比例之和必须为100%'}); 
				e.preventDefault(); //阻止提交事件先注释
			}
			}}
		});
		
	},
	
	//wangbin 2015年5月21日17:51:40 根据回款的本位币和汇率自动计算回款的金额
	countprice : function(){
		var thisInstance = this;
		$("input[name='standardmoney'],input[name='exchangerate'],input[name='unit_price']").blur(function(){
			re=new RegExp(",","g");
			var mat = $("input[name='standardmoney']").val().replace(re,"");
			var standardmoney = mat;
			var exchangerate  = $("input[name='exchangerate']").val();
			
			if(!isNaN(standardmoney) && !isNaN(exchangerate)  && exchangerate>0){
				var exchangerate = Number(exchangerate).toFixed(4);
				var unitprice = Number(standardmoney*exchangerate).toFixed(2);
				$("input[name='unit_price']").val(unitprice);
				$("input[name='exchangerate']").val(exchangerate);
			}else{
				$("input[name='unit_price']").val("");
			}
		});
        $('body').on('blur','input[name="extra_price[]"]',function(){
            extra_price = $(this).val();
            if(!isNaN(extra_price)){
                Number(extra_price).toFixed(2);
            }else{
                $(this).val('');
            }
        })
	},
	
	//分成勾选的判断
	registerFallinto : function(){
		//$("input[name='fallinto']").is(":checked")
		$("input[name='fallinto']").on("change",function(){
			var ischecked = $("input[name='fallinto']").is(":checked");
			var contentContainer = jQuery('.widget_contents');
			if(ischecked){
				//分成按钮勾选
				if($("#addfallinto").hasClass("disabled")){
					$("#addfallinto").removeClass("disabled");
				}
			}else{
				//未勾选
				$("#fallintotable thead").siblings().find("tr:eq(0)").siblings().remove();
				$("#fallintotable thead").next().find("tr td:eq(2) input").val(100);
			 	$("#fallintotable thead").next().find("tr td:eq(3) input").val($("input[name='unit_price']").val());
				if(!$("#addfallinto").hasClass("disabled")){
					$("#addfallinto").addClass("disabled");
				}
			}
		})
	},
	
	//分成明细的操作
	optionfallinto : function(){
		$("#scaling").on('blur',function(){
			var	scaling = $("#scaling").val();
			var unitprice = $("input[name='unit_price']").val();
			
			if(!isNaN(scaling)&& Number(scaling)<=100){
				var fallintoprice = (Number(unitprice)*Number(scaling))/100;
				$("#fallintoprice").val(fallintoprice);
			}
		});
		$("#addfallinto").on('click',function(){
			if($("input[name='fallinto']").is(":checked") && $("input[name='relatetoid']").val()){
				$('#fallintotable').append(aaaaa);
				$('.chzn-select').chosen();
			}else{
				Vtiger_Helper_Js.showMessage({type:'error',text:'请选择合同并勾选是否有分成选项'});
			}
		});
		
		$("#fallintotable").on("click",".deletefallinto",function(e){
			if(confirm('确定删除此条记录吗？')){
				$(this).closest('tr').remove();
			};
		});
		//输入回款比例时的验证。
		$("body").on('blur','.scaling',function(){
			if(!isNaN($(this).val())){
				if($(this).val()>100 || $(this).val()<=0){
			//		console.log($(this).val());
					 Vtiger_Helper_Js.showMessage({type:'error',text:'分成比例不正确'}); 
					 $(this).val("");
				}else{
					re=new RegExp(",","g");
					var mat = $("input[name='unit_price']").val().replace(re,"");
					var unitprice = mat;
					var inputscaling = parseInt(Number($(this).val()));
					var scalingtotal = parseInt("0");
					$(".scaling").each(
							function(){
								scalingtotal += parseInt(Number($(this).val()));
							}	
					);
						if(scalingtotal>100){
							$(this).val("");
							Vtiger_Helper_Js.showMessage({type:'error',text:'输入比例不能大能大于100%'}); 
						}else{
							$(this).closest("td").next().find("input").val((inputscaling*unitprice)/100);
						}
					//console.log(scalingtotal);
				}
			}else{
				Vtiger_Helper_Js.showMessage({type:'error',text:'分成比例如格式为数字'}); 
				$(this).val("");
			}
		});
	},
	//输入收款金额的验证 即 产品明细输入金额
	inputalready : function(){
		$("body").on('blur','.inputalready',function(){
			var totalalready = $(this).parent().prev('td').prev().text();
			var already = $(this).parent().prev('td').text();
			var inputalready = $(this).val();
			
			re=new RegExp(",","g");
			var mat = $("input[name='unit_price']").val().replace(re,"");
			var unit_price = mat;
			//输入已收产品的总金额不能大于回款的总金额
			var inputalreadytotal =Number("");
			$(".inputalready").each(
					  function(){
						 var temp = Number($(this).val());
						  inputalreadytotal += temp; //当前输入总金额;
					}
					)
			
			//输入产品金额与已收产品金额不能大于产品总额的验证
			if(!isNaN(inputalready)){
				if(Number(inputalready)+Number(already)>Number(totalalready)){
					$(this).validationEngine('showPrompt','收款金额不能超出产品金额','false','centerRight');
//					Vtiger_Helper_Js.showMessage({type:'error',text:'收款金额不能超出产品金额'});
					//$(this).val(Number(totalalready)-Number(already));
					$(this).val(Number(''));
					return;
				}
			}else{
				$(this).validationEngine('showPrompt','收款金额格式错误','false','centerRight','autoHideDelay[]');
//				Vtiger_Helper_Js.showMessage({type:'error',text:'收款金额格式错误'});
				$(this).val("0");
				return;
			}
			
			if(inputalreadytotal<=unit_price){
				  $('#currsubtract').text(Number(unit_price-inputalreadytotal).toFixed(2)); 
			   }else{
				   $(this).val(Number(''));
				   $(this).validationEngine('showPrompt','收款金额不能大于回款金额','false','centerRight');
//				   Vtiger_Helper_Js.showMessage({type:'error',text:'输入已收产品的总金额不能大于回款的总金额'}); 
				   return;
			   }
		});
	},
	//添加对是否是担保以及担保人的相关操作
	isguarantee:function(){
		if(!$("input[name='isguarantee']").is(":checked")){
			$("select[name='guaranteeperson']").attr('disabled','disabled').closest("td").addClass('hide').prev("td").addClass('hide');
		}else{
			$("select[name='guaranteeperson']").removeAttr('disabled','disabled').closest("td").removeClass('hide').prev("td").removeClass('hide');
		}
		$("input[name='isguarantee']").on("change",function(){
			var boolen =  $("input[name='isguarantee']").is(":checked");
			if(!boolen){
				$("select[name='guaranteeperson']").attr('disabled','disabled').closest("td").addClass('hide').prev("td").addClass('hide');
			}else{
				$("select[name='guaranteeperson']").removeAttr('disabled','disabled').closest("td").removeClass('hide').prev("td").removeClass('hide');
			}
		})
	},
	
	//wangbin 添加对货币类型的控制，当货币类型为人民币时，汇率锁定为1，不可更改;
	currencytypechange : function(){
		if($('select[name="currencytype"]').val() == "人民币"){
			$("input[name='exchangerate']").val("1.0000").attr("readonly","readonly");
		}
		$('select[name="currencytype"]').on("change",function(){
			if($(this).val()=="人民币"){
				$("input[name='exchangerate']").val("1.0000").attr("readonly","readonly").trigger("blur");
				$(".exchangeratetips").remove();
			}else{
				if($(this).val()){
                    $("input[name='exchangerate']").removeAttr("readonly");
                    $("input[name='exchangerate']").val("");
                    $(".exchangeratetips").remove();
                    $("input[name='exchangerate']").after("<span  class='exchangeratetips' style='color: red;'> 以入账当天汇率为准</span>");
				}else{
                    $(".exchangeratetips").remove();
				}
			}
		});
	},
	
	selectevent : function(type){
		var selector = '<span calss="span6"><select id="Select1"></select></span><span calss="span6"><select id="Select2"></select></span>';
		if($("#Select1").length==0){
			$("#ReceivedPayments_editView_fieldName_owncompany").attr("style","width:530px").before(selector);
		}
		var params = {
			"action" : 'BasicAjax',
			'module':'ReceivedPayments',
			'mode':'getCompanyAccountsByChannel',
			'channel':$("select[name='paymentchannel']").val()
		}
		AppConnector.request(params).then(
			function(data) {
				if(data.success&&data.result.flag){
					var dataString=eval(data.result.dataString);
					var defaults = {
						NextSelId: '#Select2',
						SelTextId: '#ReceivedPayments_editView_fieldName_owncompany',
						Separator: '##',
						SelStrSet: dataString
					};
					$('#Select1').html('');
					$('#Select1').unbind();
					$('#Select1').selected(defaults);
					if(type=='go'){
						$('#Select2').trigger('change');
					}
				}
		});
	},

	paymentchannelChange:function(){
		var thisInstance = this;
		$("select[name='paymentchannel']").change(function () {
			if($(this).val()=='对公转账'){
				$("#paytitleMust").show();
				$("input[name='paytitle']").enable();
				// $("#paymentcodeMust").hide();
			}else if($(this).val()=='支付宝转账'){
				$("#paytitleMust").show();
				$("input[name='paytitle']").enable();
				// $("#paymentcodeMust").show();
			}else if($(this).val()=='扫码'){
				$("#paytitleMust").hide();
				$("input[name='paytitle']").disable();
				$("input[name='paytitle']").val('');
				// $("#paymentcodeMust").show();
			}
			thisInstance.selectevent('go');
		});
	},

    match_Account:function(){
        $('#EditView').on('blur','input[name=paytitle]',function(){
            var paytitle = $(this).val();
            var params = {
                "paytitle" : paytitle,
                'module':'ReceivedPayments',
                'action':'Record'
        }
            AppConnector.request(params).then(
            function(data) {
                if(data.success){
                    $('input[name="maybe_account_display"]').val(data.result['1']);
                    $('input[name="maybe_account"]').val(data.result['0']);
                }
            })
        });
    },
    extra_option : function(){
        $("#add_extra").on('click',function(){
            $('#extra_body').append(bbbbb);
            $('.chzn-select').chosen();
        });
        $("#extra_body").on('click','.del_extra',function(){
            if(confirm('确定删除此条记录吗？')){
                $(this).closest('tr').remove();
            }
        });
    },
	registerEvents : function(){

		this._super();
        this.extra_option();
        var tablestring = '<tr> <td></td> <td class="text-center" colspan="3"> <div class="row"> <table border="1"> <tr> <td>负责人</td><td>合同总金额</td><td>合同类型</td><td>客户名称</td> </tr> <tr id="serviceinfo"> <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> </tr> </table> </div> </td> </tr>';
        $("input[name='relatetoid']").closest("tr").after(tablestring);
        $('input[name="paytitle"]').before('<div id="match_account"></div>');
		$("#ReceivedPayments_editView_fieldName_owncompany").attr("readonly",true);
        this.match_Account();
		// this.editvalidate();
		this.countprice();
		this.inputalready();
		this.registerRecordPreSaveEvent();
		this.servicecontractschange();
		this.servicecontractschange2();
		this.registerFallinto();
		this.optionfallinto();
		this.isguarantee();
		this.currencytypechange();
		this.selectevent();
		this.paymentchannelChange();
		$(".form_datetime").datepicker({format: 'yyyy-mm-dd',autoclose:true,todayBtn: true,language:  'zh-CN',todayHighlight:true});
		$('body').on('click','.today',function () {
			var now = new Date();
			$("#ReceivedPayments_editView_fieldName_reality_date").val(now.getFullYear() + "-" +((now.getMonth()+1)<10?"0":"")+(now.getMonth()+1)+"-"+(now.getDate()<10?"0":"")+now.getDate());
			$(".datepicker").hide();
			$(".table-condensed").find('td').removeClass('active');
			$(".table-condensed").find('.today').addClass('active');
			$("#ReceivedPayments_editView_fieldName_reality_date").trigger('blur');
		});
		if($("select[name='paymentchannel']").val()=='对公转账'){
			$("#paytitleMust").show();
			$("input[name='paytitle']").enable();
			// $("#paymentcodeMust").hide();
		}else if($("select[name='paymentchannel']").val()=='支付宝转账'){
			$("#paytitleMust").show();
			$("input[name='paytitle']").enable();
			// $("#paymentcodeMust").show();
		}else if($("select[name='paymentchannel']").val()=='扫码'){
			$("input[name='paytitle']").disable();
			$("#paytitleMust").hide();
			// $("#paymentcodeMust").show();
		}
		if($("input[name='record']").val()>0){
			var paytitle = $("input[name='paytitle']").val();
			var params = {
				"paytitle" : paytitle,
				'module':'ReceivedPayments',
				'action':'Record'
			}
			AppConnector.request(params).then(
				function(data) {
					if(data.success){
						$('input[name="maybe_account_display"]').val(data.result['1']);
						$('input[name="maybe_account"]').val(data.result['0']);
					}
				})
		}
	}
});


