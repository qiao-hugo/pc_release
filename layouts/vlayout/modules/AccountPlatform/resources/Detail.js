/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("AccountPlatform_Detail_Js",{
},{
	/**
	 * 跟进处理
	 */
	registerFollowClickEvent:function(){
		$('#AccountPlatform_detailView_basicAction_LBL_UPDATERECEIVED').on('click',function(){
			var message = app.vtranslate('确定要重新提交吗?');
			Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
				function(e){
					//参数设置
					var postData = {
						"module": app.getModuleName(),
						"action": "ChangeAjax",
						"record": jQuery('#recordId').val(),
						"mode":"Resubmit"
					}
					//发送请求
					AppConnector.requestPjaxPost(postData).then(
						function(data){
							alert(data);
							//刷新页面
							window.location.reload();
						},
						function(error){
						}
					);
				},
				function(error){
				}
			)
		});
		$('#AccountPlatform_detailView_basicAction_LBL_CHANGE_STATUS').on('click',function(){
			var message = app.vtranslate('确定更改账户状态?');
			Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
				function(e){
					//参数设置
					var postData = {
						"module": app.getModuleName(),
						"action": "ChangeAjax",
						"record": jQuery('#recordId').val(),
						"mode":"changeStatus"
					}
					//发送请求
					AppConnector.requestPjaxPost(postData).then(
						function(data){
							alert('更新成功');
							//刷新页面
							window.location.reload();
						},
						function(error){

						}
					);
				},
				function(error){
				}
			)
		});
	},
	
	/**
	 * 审核处理
	 */
	registerAuditClickEvent:function(){
		$('#btnAudit').on('click',function(){
			var message = app.vtranslate('JS_LBL_AUDITOR_CONFIRM_MESSAGE');
			Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
				function(e) {
					//参数设置
					var postData = {
						"module": app.getModuleName(),
						"action": "SaveAjax",
						"record": jQuery('#recordId').val(),
						"type":"audit"
					}
					//发送请求
					AppConnector.request(postData).then(
						function(data){
							if(data.success ==  true && data.result[0] =="followup"){
								var message = app.vtranslate('JS_FOLLOWUP_MESSAGE');
								var params = {
									text: message,
									type: 'notice'
								};
								Vtiger_Helper_Js.showMessage(params);
								return;
							}
							//刷新页面
							window.location.reload();
						},
						function(error){
							console.log(error);
						}
					);
				},
				function(error){
				}
			)
		});
	},
	
	/**
	 * 拒绝处理
	 */
	registerRejectClickEvent:function(){
		$('#btnReject').on('click',function(){
			var message = app.vtranslate('JS_LBL_REJECT_CONFIRM_MESSAGE');
			Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
				function(e) {
					//参数设置
					var postData = {
						"module": app.getModuleName(),
						"action": "SaveAjax",
						"record": jQuery('#recordId').val(),
						//"backwhy":jQuery('input[name=backwhy]').val(),
						"type":"reject"
					}
					//发送请求
					AppConnector.request(postData).then(
						function(data){
							if(data.success ==  true && data.result[0] =="followup"){
								var message = app.vtranslate('JS_FOLLOWUP_MESSAGE');
								var params = {
									text: message,
									type: 'notice'
								};
								Vtiger_Helper_Js.showMessage(params);
								return;
							}
							//刷新页面
							window.location.reload();
						},
						function(error){
							console.log(error);
						}
					);
				},
				function(error){
				}
			)
		});
	},
    accountDetailInfo:function () {
        $("#appendAccountDetail").click(function () {
            $('#accountDetail').append(appendAccountDetail);
        });
        // 删除
        $("#accountDetail").on('click','.deleteRecordButton',function () {
				var thisTr = $(this).closest("tr");
				var id = thisTr.data("id");
                var textidaccount=thisTr.find("#textidaccount").text();
                var textaccountplatform=thisTr.find("#textaccountplatform").text();
                var message = '确认要删除ID为“'+textidaccount+'” ，名称为“'+textaccountplatform+'”的账户吗？';
                Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
                    function(e) {
                        if (id) {
                            var module = app.getModuleName();
                            var postData = {
                                "module": module,
                                "action": "ChangeAjax",
                                'mode': 'deleteDetailOne',
                                'record': jQuery('#recordId').val(),
                                "id": id
                            }
                            var Message = app.vtranslate('正在处理中...');
                            var progressIndicatorElement = jQuery.progressIndicator({
                                'message': Message,
                                'position': 'html',
                                'blockInfo': {'enabled': true}
                            });
                            AppConnector.request(postData).then(
                                function (data) {
                                    progressIndicatorElement.progressIndicator({'mode': 'hide'});
                                    console.log(data);
                                    if (data.success) {
                                        thisTr.remove();
                                    } else {
                                        Vtiger_Helper_Js.showMessage({type: 'error', text: '删除失败!'});
                                    }
                                },
                                function (error, err) {

                                }
                            );
                        } else {
                            thisTr.remove();
                        }
                });
        });
        //保存
        $("#accountDetail").on('click','.clickSave',function () {

            var  thisTr=$(this).closest("tr");
            var id=thisTr.data("id");
            var module=app.getModuleName();
            var idaccount=thisTr.find("#idaccount").val();
            var accountplatform=thisTr.find("#accountplatform").val();
            var postData= {
                "module": module,
                "action": "ChangeAjax",
                'idaccount': idaccount,
                'accountplatform': accountplatform,
                'mode': 'updateDetailOne',
                'record': jQuery('#recordId').val(),
                "id": id
            }
            var i=0;
            $("input[name='idaccount[]']").each(function () {
                if($(this).val()==idaccount){
                    i=i+1;
                }
            });
            if(i>1){
                Vtiger_Helper_Js.showMessage({type:'error',text:'当前ID,账号重复不允许添加!!'});
                return false;
            }
            var Message = app.vtranslate('正在处理中...');
            var progressIndicatorElement = jQuery.progressIndicator({
                'message' : Message,
                'position' : 'html',
                'blockInfo' : {'enabled' : true}
            });
            AppConnector.request(postData).then(
                function(data){
                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                    console.log(data);
                    if(data.success) {
                        if(data.result.message){
                            Vtiger_Helper_Js.showMessage({type:'error',text:data.result.message});
                        }else{
                            thisTr.find("#idaccount").css("display","none");
                            thisTr.find("#accountplatform").css("display","none");
                            thisTr.find(".clickSave").css("display","none");
                            thisTr.find("#textidaccount").css("display","block");
                            thisTr.find("#textaccountplatform").css("display","block");
                            thisTr.find(".clickEdit").css("display","inline");
                            thisTr.find("#textidaccount").text(idaccount);
                            thisTr.find("#textaccountplatform").text(accountplatform);
                        }
                    }else{
                        Vtiger_Helper_Js.showMessage({type:'error',text:'保存失败!'});
                    }
                },
                function(error,err){

                }
            );
        });
        //编辑
        $("#accountDetail").on('click','.clickEdit',function () {
            var  thisTr=$(this).closest("tr");
            thisTr.find("#idaccount").css("display","block");
            thisTr.find("#accountplatform").css("display","block");
            thisTr.find(".clickSave").css("display","inline");
            thisTr.find("#textidaccount").css("display","none");
            thisTr.find("#textaccountplatform").css("display","none");
            thisTr.find(".clickEdit").css("display","none");
        });
    },

	importExplain:function(){
		$("#importExplain").click(function () {
			var message='<br>请按照模板，编辑数据，进行导入:<br><br><a style="color:#08c;cursor: pointer;" title="导入模板" target="_blank" href="./accountplatform_import.xlsx" >导入模板</a>';
			var msg={
				'message':message,
				"width":"400px",
			};
			Vtiger_Helper_Js.showConfirmationBox(msg);
			$(".modal-footer").remove();
		});
	},

	uploadImport:function(){
		var module=$('#module').val();
		KindEditor.ready(function(K) {
			var uploadbutton = K.uploadbutton({
				button : K('#uploadImport')[0],
				fieldName : 'importFile',
				extraParams :{
					__vtrftk:csrfMagicToken,
				},
				url : 'index.php?module='+module+'&action=Import',
				afterUpload : function(data) {
					if(data.success){
						var data=data.result;
						if(data.length>1){
							var appendDetail='';
							for (var i=1;i< data.length;i++){
								appendDetail +='<tr>' +
									'<td class="fieldLabel medium">' +
									'<label class="muted pull-right marginRight10px"><span class="redColor">*</span></label>' +
									'</td>' +
									'<td class="fieldValue medium">' +
									'<span class="value" data-field-type="string" id="textidaccount" ></span>'+
									'<input id="idaccount" type="text" class="input-large" onkeyup="this.value=this.value.replace(/\\s+/g,\'\')" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="idaccount[]" placeholder="请输入账户ID" value="'+data[i][0]+'" >' +
									'</td>' +
									'<td class="fieldLabel medium">' +
									'<span class="value" data-field-type="string" id="textaccountplatform"></span>'+
									'<input id="accountplatform" type="text" class="input-large" data-validation-engine="validate[ funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="accountplatform[]"  placeholder="请输入账户名称" value="'+data[i][1]+'" >'+
									'</td>' +
									'<td class="fieldValue medium">' +
									'<a class="clickSave"><i class="icon-ok alignMiddle" title="点击保存账户明细"></i></a>&nbsp;&nbsp;<a class="clickEdit" style="display:none"><i class="icon-pencil alignMiddle" title="点击编辑账户明细"></i></a>&nbsp;&nbsp;<a class="deleteRecordButton"><i title="删除" class="icon-trash alignMiddle"></i></a>' +
									'</td>' +
									'</tr>';
							}
							$('#accountDetail').append(appendDetail);
						}
						Vtiger_Helper_Js.showMessage({type:'success',text:"导入成功"});
					}else{
						Vtiger_Helper_Js.showMessage({type:'error',text:"导入失败"});
					}
				},
				afterError : function(str) {
					Vtiger_Helper_Js.showMessage({type:'error',text:"导入失败"});
				}
			});
			uploadbutton.fileBox.change(function(e) {
				var filePath=$('input[name=importFile]').val();
				if(filePath.indexOf(".xlsx")!=-1){
					uploadbutton.submit();
				}else{
					Vtiger_Helper_Js.showMessage({type:'error',text:"您未上传文件，或者您上传文件类型有误"});
					return false
				}
			});
		});
	},

	/**
	 * 批量保存
	 */
	batchSaveAccount:function(){
		$("#batchSave").click(function () {
			var detailArray=new Array();
			var idaccountArray=new Array();
			var flag=true;
			var errMsg='';
			var module=app.getModuleName();
			$("#accountDetail").find(".clickSave").each(function () {
				var idaccount=$(this).closest("tr").find("#idaccount").val();
				if($(this).is(':visible')){
					var detailOne=new Object();
					//需要保存的账号
					var id=$(this).closest("tr").data("id");
					var accountplatform=$(this).closest("tr").find("#accountplatform").val();
					detailOne.id=id;
					//判断是否有空值
					if(idaccount&&accountplatform&&flag){
						detailOne.idaccount=idaccount;
						detailOne.accountplatform=accountplatform;
						//判断是否有重复项
						if($.inArray(idaccount, idaccountArray)== -1){
							detailArray.push(JSON.stringify(detailOne));
							idaccountArray.push(idaccount);
						}else{
							flag=false;
							errMsg='账户id'+idaccount+'有重复项';
						}
					}else{
						flag=false;
						if(!errMsg){
							errMsg='请检查是否有未填写内容';
						}
					}
				}else{
					idaccountArray.push(idaccount);
				}
			});
			if(detailArray.length==0){
				flag=false;
				errMsg='无需要保存的账户';
			}
			if(flag){
				var postData= {
					"module": module,
					"action": "ChangeAjax",
					'mode': 'updateDetailBatch',
					'record': jQuery('#recordId').val(),
					'detailJson':'['+detailArray.toString()+']'
				}
				var Message = app.vtranslate('正在处理中...');
				var progressIndicatorElement = jQuery.progressIndicator({
					'message' : Message,
					'position' : 'html',
					'blockInfo' : {'enabled' : true}
				});
				AppConnector.request(postData).then(
					function(data){
						progressIndicatorElement.progressIndicator({'mode' : 'hide'});
						console.log(data);
						if(data.success) {
							if(data.result.message){
								var message=data.result.message;
								var errorId=new Array();
								var errorMsg='';
								$.each(message,function (i,item) {
									errorId.push(item.idaccount);
									errorMsg+="账户id为["+item.idaccount+"]的账户保存失败，原因是["+item.message+"]</br>";
								});
								$("#accountDetail").find(".clickSave").each(function () {
									var idaccount=$(this).closest("tr").find("#idaccount").val();
									var accountplatform=$(this).closest("tr").find("#accountplatform").val();
									if($(this).is(':visible')&&$.inArray(idaccount, errorId)== -1){
										//没有问题的改变状态
										$(this).closest("tr").find("#idaccount").css("display","none");
										$(this).closest("tr").find("#accountplatform").css("display","none");
										$(this).closest("tr").find(".clickSave").css("display","none");
										$(this).closest("tr").find("#textidaccount").css("display","block");
										$(this).closest("tr").find("#textaccountplatform").css("display","block");
										$(this).closest("tr").find(".clickEdit").css("display","inline");
										$(this).closest("tr").find("#textidaccount").text(idaccount);
										$(this).closest("tr").find("#textaccountplatform").text(accountplatform);
									}
								});
								Vtiger_Helper_Js.showMessage({type:'error',text:errorMsg});
							}else{
								//全部成功刷新页面
								window.location.reload();
							}
						}else{
							Vtiger_Helper_Js.showMessage({type:'error',text:'保存失败!'});
							return false;
						}
					},
					function(error,err){

					}
				);
			}else{
				Vtiger_Helper_Js.showMessage({type:'error',text:errMsg});
				return false;
			}
		});
	},

	registerEvents:function(){
		this._super();
		this.registerFollowClickEvent();
		this.registerAuditClickEvent();
		this.registerRejectClickEvent();
        this.accountDetailInfo();
		this.importExplain();
		this.uploadImport();
		this.batchSaveAccount();
	}
});