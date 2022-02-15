/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("ReceivedPaymentsNotes_List_Js",{},{
	layerObject:null,
	needWaitChangeBindInfo:null,
	layerTable:null,
	registerRowClickEvent: function() {
		return ;
	},

	relieve:function(){
		var thisInstance = this;
		$("body").on('click','.relieve',function () {
			var message = '是否要解除匹配？【回款在订单未激活、未开具发票、未关联充值单、工单情况下方可解绑】';
			var title='解除匹配';
			if($(this).html()=='确认解绑'){
				message = '是否确认要解除匹配？【回款已跨月，需财务确认解绑。且在订单未激活、未开具发票、未关联充值单、工单情况下方可解绑】<br><br><input type="checkbox" style="margin: 0 3px 0 0">已按制度执行';
				title = '确认解除匹配？';
			}
			var recordId=$(this).data('id');
			thisInstance.showConfirmationBox({'message' : message,'width':'300px','title': title}).then(
				function(e) {
					var module = app.getModuleName();
					var postData = {
						"module": module,
						"action": "BasicAjax",
						"record": recordId,
						"mode":"relieve"
					}
					var Message = app.vtranslate('正在解绑');
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
							if(!data.result.flag){
								thisInstance.showConfirmationBox({'message' : data.result.msg,'width':'300px','title': '解除匹配'}).then(
									function(e) {
										window.location.reload();
									}
								);
								$('.modal-title').replaceWith('<h6>解除匹配</h6>')
								$('.modal-header').css("padding","0 9px");
							}else{
								Vtiger_Helper_Js.showMessage({type:'success',text:'解绑完成'});
								window.location.reload();
							}
						},
						function(error,err){
						}
					);
				},
				function(error, err){
				}
			);
			$('.modal-title').replaceWith('<h6>解除匹配</h6>')
			$('.modal-header').css("padding","0 9px");
		});
	},



	showConfirmationBox : function(data){
		var aDeferred = jQuery.Deferred();
		var width='800px';
		if(typeof  data['width'] != "undefined"){
			width=data['width'];
		}
		var checkFlag=true
		var bootBoxModal = bootbox.confirm({message:data['message'],width:width,title:data['title'], callback:function(result) {
				if(result){
					if(typeof  data['action'] != "undefined"){
						checkFlag=(data['action'])();
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
		bootBoxModal.on('hidden',function(e){
			if(jQuery('#globalmodal').length > 0) {
				jQuery('body').addClass('modal-open');
			}
		})
		return aDeferred.promise();
	},

	//导出
	unboundExport:function(){
		$("body").on('click','.exportButton',function () {
			console.log($("#SearchBug").serialize());
			var module = app.getModuleName();
			var postData = {
				"module": module,
				"action": "BasicAjax",
				"json": $("#SearchBug").serialize(),
				"jsonArray":$("#SearchBug").serializeArray(),
				"mode":"unboundExport"
			}
			var Message = app.vtranslate('正在导出');
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
					window.location.href=data.result.msg;
				},
				function(error,err){
				}
			);
		});
	},

	changeBinding:function(){
		$("body").on('click','.changeBinding',function () {
			var recordId=$(this).data('id');
			layerObject.confirm('是否要换绑回款？<br><br>只有当回款跨月，已计算业绩提成不可进行<br>正常解绑的情况下，放可申请将金额相同的<br>未匹配回款进行换绑', {icon: 3, title:'回款换绑'}, function(index){
				var loadIndex=layerObject.load();
				layerObject.close(index);
				var module = app.getModuleName();
				var postData = {
					"module": module,
					"action": "BasicAjax",
					"record": recordId,
					"mode":"isCanChangeBinding"
				}
				AppConnector.request(postData).then(
					function(data){
						layerObject.close(loadIndex);
						if(data.result.flag){
							var contentHtml=data.result.contentHtml;
							needWaitChangeBindInfo=data.result.needWaitChangeBindInfo;
							layerObject.open({
								title:'回款换绑',
								type: 1,
								content: contentHtml,
								btn: ['确认', '取消'],
								yes: function(layero, index){
									var loadIndex=layerObject.load();
									var oldData = layerTable.cache['receivedPaymentsConfirmTable'];
									console.log(oldData);
									if(oldData&&oldData.length!=0){
										var postData = {
											"module": module,
											"action": "BasicAjax",
											"record": recordId,
											"jsonData":JSON.stringify(oldData),
											"mode":"confirmChangeBinding"
										}
										AppConnector.request(postData).then(
											function(data){
												layerObject.close(loadIndex);
												if(data.result.flag){
													console.log(data.result.msg);
													layerObject.alert('换绑成功');
													layerObject.closeAll();
													window.location.reload();
												}else{
													layerObject.alert(data.result.msg, {icon: 2});
												}
											},
											function(error,err){
												layerObject.close(loadIndex);
												layerObject.alert('请求失败', {icon: 2});
											}
										);
									}else{
										layerObject.close(loadIndex);
										layerObject.alert('请确定要换绑的回款', {icon: 2});
									}
								}
							});
						}else{
							layerObject.alert(data.result.msg, {icon: 2});
							window.location.reload();
						}
					},
					function(error,err){
						layerObject.close(loadIndex);
						layerObject.alert('请求失败', {icon: 2});
					}
				);
			});
		});
	},

	registerEvents : function(){
		this._super();
		this.relieve();
		this.unboundExport();
		this.changeBinding();
		layui.use(['layer'], function(){
			layerObject=layui.layer;
		});
	}

});