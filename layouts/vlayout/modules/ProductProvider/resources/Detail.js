/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("ProductProvider_Detail_Js",{
},{
	/**
	 * 跟进处理
	 */
	registerFollowClickEvent:function(){
		$('#ProductProvider_detailView_basicAction_LBL_UPDATERECEIVED').on('click',function(){
			var message = app.vtranslate('确定要重新提交吗');
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
							alert(data)
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
		$('#ProductProvider_detailView_basicAction_LBL_CHANGE_STATUS').on('click',function(){
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
	accountDetailInfo:function () {
        $("#appendAccountDetail").click(function () {
            $('#accountDetail').append(appendAccountDetail);
        });
        // 删除
        $("#accountDetail").on('click','.deleteRecordButton',function () {
                var thisTr = $(this).closest("tr");
                var id = thisTr.data("id");
                var textidaccount=thisTr.find("#textidaccount").text();
                var textaccountzh=thisTr.find("#textaccountzh").text();
                var message = '确认要删除ID为“'+textidaccount+'” ，名称为“'+textaccountzh+'”的账户吗？';
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
            var accountzh=thisTr.find("#accountzh").val();
            var postData= {
                "module": module,
                "action": "ChangeAjax",
                'idaccount': idaccount,
                'accountzh': accountzh,
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
                            thisTr.find("#accountzh").css("display","none");
                            thisTr.find(".clickSave").css("display","none");
                            thisTr.find("#textidaccount").css("display","block");
                            thisTr.find("#textaccountzh").css("display","block");
                            thisTr.find(".clickEdit").css("display","inline");
                            thisTr.find("#textidaccount").text(idaccount);
                            thisTr.find("#textaccountzh").text(accountzh);
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
            thisTr.find("#accountzh").css("display","block");
            thisTr.find(".clickSave").css("display","inline");
            thisTr.find("#textidaccount").css("display","none");
            thisTr.find("#textaccountzh").css("display","none");
            thisTr.find(".clickEdit").css("display","none");
        });
    },
	registerEvents:function(){
		this._super();
		this.registerFollowClickEvent();
		this.accountDetailInfo();
	}
});