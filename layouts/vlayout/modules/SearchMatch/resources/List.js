Vtiger_List_Js("SearchMatch_List_Js",{},{
	DATA:null,
	bootBoxOBJECT:null,
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
			var module = 'Matchreceivements';
			var postData = {
				"module": module,
				"action": "BasicAjax",
				"record": recordId,
				'status': 1,
				'mode': 'getSplitServiceContracts'
				//"parent": app.getParentModuleName()
			}

			var Message = app.vtranslate('正在请求');
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
											var Message = app.vtranslate('正在请求');
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
															Vtiger_Helper_Js.showMessage({type:'success',text:'拆分成功'});
															$("#PostQuery").trigger("click");
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
									}
								},function(error, err){}
							);

							var str = '';
							for(var i=0; i<contract_no_arr.length; i++) {
								str += '<option value="'+ contract_no_arr[i]['servicecontractsid'] +'">'+ contract_no_arr[i]['contract_no'] +'</option>';
							}
							var display = '';
							if (contract_no_arr.length == 0) {
								//没有合同号的未匹配可多次拆分
								$('.modal-body').append('<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0;"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">分拆金额:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><input type="number" name="split_money[]" /></span><i title="增加" style="cursor:pointer;margin-right: 10px;" class="icon-plus alignMiddle addSplit"></i></div></td></tr></tbody></table>');
							}else{
								$('.modal-body').append('<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0;"><tbody><tr style="display:' + display + '"><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">分拆合同:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><select id="contract_no_select" name="contract_no">'+str+'</span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">分拆金额:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><input type="number" id="split_money" name="split_money" /></span></div></td></tr></tbody></table>');
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

	/**
	 * 回款匹配
	 */
	matchReceive:function(){
		var listViewContentDiv = this.getListViewContentContainer();
		var thisInstance=this;
		listViewContentDiv.on('click', '.matchReceive', function(e){
			var elem = jQuery(e.currentTarget);
			var recordId = elem.closest('tr').data('id');
			var channel = elem.closest('tr').data('channel');
			var module = 'SearchMatch';
			var postData = {
				"module": module,
				"action": "BasicAjax",
				"record": recordId,
				'mode': 'getCanMatchServiceContracts'
			}
			var Message = app.vtranslate('正在请求');
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
					if(data.result){
						var msg = {
							'message': '回款匹配',
							"width":"700px",
							"action":'checkMatch'
						};
						DATA=data;
						thisInstance.showConfirmationBox(msg);
						var options='<option value="">请选择合同</option>';
						//循环把合同id和合同号放进option
						if(data.result.servicecontracts){
							for(var servicecontractsid  in data.result.servicecontracts) {
								options+='<option value="'+servicecontractsid+'">'+data.result.servicecontracts[servicecontractsid]+'</option>';
							}
						}
						var staymentHtml='';
						if(data.result.matchtype==0){
							//没有正常回款，只有代付款
							var inputValue='';
							if(channel=='对公转账'){
								inputValue=data.result.paytitle;
							}

							staymentHtml='<tr>\n' +
								'          <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">回款抬头或打款人全称</label></td>\n' +
								'          <td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input name="title" data-channel="'+channel+'" style="margin-bottom: 0;width:300px" value="'+inputValue+'" type="text" placeholder="请输入回款抬头或打款人全称"/><button style="margin-left: 10px" data-type="open" data-id="'+data.result.receivedpaymentsid+'" id="searchTitle" class=" btn btn-primary">查询</button></span></div></td>\n' +
								'        </tr>\n'+
							'        <tr>\n' +
							'          <td class="fieldLabel medium"></td>\n' +
							'          <td class="fieldValue medium"><div class="row-fluid"><span class="span10" style="text-align: left;color: red">提示：如果存在代付款协议，将会按合同与回款抬头查询对应的代付款协议，请选择并关联</span></div></td>\n' +
							'        </tr>\n';
							staymentHtml+='<tr>\n' +
								'            <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">关联代付款</label></td>\n' +
								'            <td>\n' +
								'                <table class="table" id="stayPayMentinofs" data-id="1" style="border-left:none;border-bottom:none;margin-bottom:0">\n' +
								'                    <thead><tr><td>暂无结果，请查询</td></tr></thead>'+
								'                </table>\n' +
								'            </td>\n' +
								'          </tr>';
						}
						$('.modal-body').append('<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0;">\n' +
							'      <tbody>\n' +
							'        <tr>\n' +
							'          <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">待匹配回款抬头</label></td>\n' +
							'          <td class="fieldValue medium"><div class="row-fluid"><span class="span10" id="paytitle">'+data.result.paytitle+'</span></div></td>\n' +
							'        </tr>\n' +
							'        <tr>\n' +
							'          <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">待匹配回款金额</label></td>\n' +
							'          <td class="fieldValue medium"><div class="row-fluid"><span class="span10">￥'+data.result.unit_price+'</span></div></td>\n' +
							'        </tr>\n' +
							'        <tr>\n' +
							'          <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">打款日期</label></td>\n' +
							'          <td class="fieldValue medium"><div class="row-fluid"><span class="span10">'+data.result.reality_date+'</span></div></td>\n' +
							'        </tr>\n' +
							'        <tr>\n' +
							'          <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">交易单号</label></td>\n' +
							'          <td class="fieldValue medium"><div class="row-fluid"><span class="span10">'+data.result.paymentcode+'</span></div></td>\n' +
							'        </tr>\n' +
							'        <tr>\n' +
							'          <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">请选择匹配的合同</label></td>\n' +
							'          <td class="fieldValue medium">\n' +
							'            <div class="row-fluid">\n' +
							'              <span class="span10">\n' +
							'                <select class="chzn-select" name="contract_no">\n' +options+
							'                </select>\n' +
							'              </span>\n' +
							'            </div>\n' +
							'          </td>\n' +
							'        </tr>\n' +
							'        <tr>\n' +
							'          <td class="fieldValue medium" colspan="2"><div class="row-fluid"><span class="span10" style="text-align: right;color: red">订阅激活类合同（标准合同）只有下单或者签收后，才可以匹配合同</span></div></td>\n' +
							'        </tr>\n' +
							'        <tr>\n' +
							'          <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">合同客户名称</label></td>\n' +
							'          <td class="fieldValue medium"><div class="row-fluid"><span class="span10 accountnamespan">'+data.result.accountname+'</span></div></td>\n' +
							'        </tr>\n' +
							'        <tr>\n' +
							'          <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">总金额</label></td>\n' +
							'          <td class="fieldValue medium"><div class="row-fluid"><span class="span10 totelMoney">'+data.result.totelMoney+'</span></div></td>\n' +
							'        </tr>\n' +
							'        <tr>\n' +
							'          <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">已回款金额</label></td>\n' +
							'          <td class="fieldValue medium"><div class="row-fluid"><span class="span10 receivedMoney">'+data.result.receivedMoney+'</span></div></td>\n' +
							'        </tr>\n' +
							'        <tr>\n' +
							'          <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">剩余未回款金额</label></td>\n' +
							'          <td class="fieldValue medium"><div class="row-fluid"><span class="span10 remainMoney">'+data.result.remainMoney+'</span></div></td>\n' +
							'        </tr>\n' +staymentHtml+
							'      </tbody>\n' +
							'    </table>');
						$('.modal-body .chzn-select').chosen();
						var chznResultsWidth='300px';
						if(data.result.matchtype!=0){
							chznResultsWidth='180px';
						}
						$('.modal-body .chzn-results').css('height',chznResultsWidth);

					}else {
						Vtiger_Helper_Js.showPnotify(app.vtranslate('操作失败'));
					}
				},
				function(error,err){

				}
			);

		});
	},

	/**
	 * 搜索回款抬头
	 */
	searchTitle:function(){
		$('body').on('click','#searchTitle',function () {
			$("input[name='title']").blur(function () {
				this.style.borderColor = this.value == '' ? 'red' : '#ccc';
			});
			var type=$(this).data('type');
			var recordId=$(this).data('id');
			if(type=='open'){
				//可以查询
				var paytitle=$("#paytitle").text();
				var payTitle=$("input[name=title]").val();
				var channel=$("input[name=title]").data('channel');
				if(payTitle==''){
					Vtiger_Helper_Js.showPnotify(app.vtranslate('请输入回款抬头或打款人全称'));
					$("input[name='title']").trigger('blur');
					return false;
				}
				if(channel=='对公转账'&&payTitle!=paytitle){
					Vtiger_Helper_Js.showPnotify(app.vtranslate('支付方式是对公转账，回款抬头或打款人全称必须与待匹配回款抬头一样'));
					return false;
				}
				if(channel=='支付宝转账'&&(payTitle.length!=paytitle.length||payTitle.substr(payTitle.length-1,1)!=paytitle.substr(paytitle.length-1,1))){
					Vtiger_Helper_Js.showPnotify(app.vtranslate('支付类型为支付宝时，录入信息最后一个字应该与汇款抬头一致'));
					return false;
				}
				var servicecontractsid=$('select[name=contract_no]').val();
				if(!servicecontractsid){
					Vtiger_Helper_Js.showPnotify(app.vtranslate('服务合同必填'));
					return false;
				}
				var postData = {
					"module": 'SearchMatch',
					"action": "BasicAjax",
					"payTitle": payTitle,
					'servicecontractsid':servicecontractsid,
					'recordId':recordId,
					'mode': 'getStayPaymentByTitle'
				}
				var Message = app.vtranslate('请求搜索中');
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
							if(data.result&&data.result.length>0){
								//查询成功
								var staymenthtml='<thead>\n' +
								    '<tr>\n' +
									'<th>选择</th>\n' +
									'<th>代付款客户</th>\n' +
									'<th>打款人全称</th>\n' +
									'<th>签订代付款金额</th>\n' +
									'<th>剩余代付款金额</th>\n' +
									'</tr>\n' +
									'</thead>\n' +
									'<tbody align="center">';
								for (var i=0;i<data.result.length;i++){
									var payer=data.result[i].payer;
									if(!payer){
										payer='';
									}
									staymenthtml+='<tr>\n' +
										'<td class="fieldLabel medium"><input type="checkbox"  name="stayCheck" data-id="'+data.result[i].staypaymentid+'"></td>\n' +
										'<td class="fieldLabel medium">'+data.result[i].staypaymentname+'</td>\n' +
										'<td class="fieldLabel medium">'+payer+'</td>\n' +
										'<td class="fieldLabel medium">'+data.result[i].staypaymentjine+'</td>\n' +
										'<td class="fieldLabel medium">'+data.result[i].surplusmoney+'</td>\n' +
										'</tr>';
								}
								staymenthtml+='</tbody>';
								$("#stayPayMentinofs").html(staymenthtml);
								$("select[name=contract_no]").attr('readOnly',true)
								$("input[name=title]").attr('readOnly',true)
								$("#searchTitle").html('重置')
								$("#searchTitle").data('type','close');
								if(data.result.length==1){
									//只有一个时自动勾选
									$("input[name=stayCheck]").trigger('click');
									$("#stayPayMentinofs").data('id',$("input[name=stayCheck]").data('id'));
								}
							}else{
								//没查到
								var staymenthtml='<thead><tr><td width="300px">暂无相关代付款协议\n' +
									'\n' +
									'\n' +
									'\n' +
									'点击确定时系统会自动创建一条草稿状态的代付款协议，可在签收代付款原件前，在代付款页面进行编辑并提交正式审核申请。\n' +
									'\n' +
									'或重置关联条件</td></tr></thead>';
								$("#stayPayMentinofs").html(staymenthtml);
								$("select[name=contract_no]").attr('readOnly',true)
								$("input[name=title]").attr('readOnly',true)
								$("#searchTitle").html('重置')
								$("#searchTitle").data('type','close');
								$("#stayPayMentinofs").data('id',0);//没有代付款，草稿新建个
							}
						}
					},
					function(error,err){}
				);

			}else{
				//重置
				var staymenthtml='<thead><tr><td>暂无结果，请查询</td></tr></thead>';
				$("#stayPayMentinofs").html(staymenthtml);
				$("select[name=contract_no]").removeAttr('readOnly');
				$("input[name=title]").removeAttr('readOnly')
				$("#searchTitle").html('查询')
				$("#searchTitle").data('type','open');
				$("#stayPayMentinofs").data('id',1);
			}
		});
	},

	/**
	 * check选中
	 */
	checkStayPayMent:function(){
		$('body').on("click","input[name=stayCheck]",function () {
			if($(this).prop("checked")){
				//被选中
				var len = $("input[name=stayCheck]:checked").length;
				if(len>1){
					alert('只允许选择一个代付款');
					return false;
				}
				$("#stayPayMentinofs").data('id',$(this).data('id'));
			}else{
				//被取消
				$("#stayPayMentinofs").data('id',1);
			}
		});
	},


	serviceClick:function(){
		$('body').on('change',"select[name='contract_no']",function () {
			var servicecontractsid=$('select[name=contract_no]').val();
			var postData = {
				"module": 'SearchMatch',
				"action": "BasicAjax",
				"record": servicecontractsid,
				'mode': 'getAccountName'
			}
			var Message = app.vtranslate('正在请求');
			AppConnector.request(postData).then(
				// 请求成功
				function(data){
					if(data.result){
						$(".accountnamespan").html(data.result.accountname);
						$(".totelMoney").html(data.result.totelMoney);
						$(".receivedMoney").html(data.result.receivedMoney);
						$(".remainMoney").html(data.result.remainMoney);
					}else {
						Vtiger_Helper_Js.showPnotify(app.vtranslate('操作失败'));
					}
				},
				function(error,err){

				}
			);
		});
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

	//重写弹出框
	showConfirmationBox : function(data){
		var thisInstance=this;
		var aDeferred = jQuery.Deferred();
		var width='600px';
		if(typeof  data['width'] != "undefined"){
			width=data['width'];
		}
		var checkFlag=true;
		var bootBoxModal = bootbox.confirm({message:data['message'],width:width,title:data['title'], callback:function(result) {
				if(result){
					if(typeof  data['action'] != "undefined"){
						checkFlag=eval("thisInstance."+data['action']+'()');
					}
					if(checkFlag){
						aDeferred.resolve();
					}else{
						return false
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
		$(".modal-body").css('max-height','600px');

		bootBoxModal.on('hidden',function(e){
			if(jQuery('#globalmodal').length > 0) {
				jQuery('body').addClass('modal-open');
			}
		})
		bootBoxOBJECT=bootBoxModal;
		return aDeferred.promise();
	},

	//匹配确定时的判断
	checkMatch:function(){
		$("input[name='title']").blur(function () {
			this.style.borderColor = this.value == '' ? 'red' : '#ccc';
		});
		var servicecontractsid=$('select[name=contract_no]').val();
		var payer=$("input[name='title']").val();
		if(payer==''){
			Vtiger_Helper_Js.showPnotify(app.vtranslate('请输入回款抬头或打款人全称'));
			$("input[name='title']").trigger('blur');
			return false;
		}
		if(servicecontractsid==''){
			Vtiger_Helper_Js.showPnotify(app.vtranslate('服务合同必填'));
			return false;
		}
		if($("#stayPayMentinofs").data('id')==1){
			//0是新建代付款,有值是已有代付款要选择，1是还没选择过
			Vtiger_Helper_Js.showPnotify(app.vtranslate('请选择代付款'));
			return false;
		}
		var matchType=DATA.result.matchtype;
		var staypaymentid='';
		if(matchType==0){
			staypaymentid=$("#stayPayMentinofs").data('id');
			//如果有代付款，处理代付款
			if(($("input[name=stayCheck]").length>0&&staypaymentid<=1)){
				//有代付款却没选
				Vtiger_Helper_Js.showPnotify(app.vtranslate('请选择代付款'));
				return false;
			}
		}
		var postData = {
			"module": 'SearchMatch',
			"action": "BasicAjax",
			'mode': 'goMatch',
			"receivepayid": DATA.result.receivedpaymentsid,
			'contractid': servicecontractsid,
			'total': DATA.result.unit_price,
			'shareuser':DATA.result.shareuser,
			'staypaymentid':staypaymentid,
			'paytitle':DATA.result.paytitle,
			'payer':$("input[name='title']").val()
		}
		$('body .modal-footer .btn-success').disable();
		AppConnector.request(postData).then(
			function(data) {
				$('body .modal-footer .btn-success').enable();
				if (data.success) {
					if(data.result.flag){
						Vtiger_Helper_Js.showMessage({type:'success',text:'匹配成功'});
						bootBoxOBJECT.modal('hide');
						$("#PostQuery").trigger("click");
					}else{
						Vtiger_Helper_Js.showMessage({type:'error',text:'匹配失败,'+data.result.msg});
						return false
					}
				}else{
					Vtiger_Helper_Js.showMessage({type:'error',text:'匹配失败,'+data.error.message});
					return false
				}
			},
			function(error,err){
				$('body .modal-footer .btn-success').enable();
				Vtiger_Helper_Js.showMessage({type:'error',text:'匹配失败'});
				return false
			}
		);
	},

	registerEvents : function(){
		this._super();
		this.splitReceive();
		this.addSplit();
		this.deleteSplit();
		this.matchReceive();
		this.checkStayPayMent();
		this.serviceClick();
		this.searchTitle();
	}
});