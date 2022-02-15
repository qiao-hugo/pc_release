/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("VisitingOrder_Detail_Js",{
},{
	/**
	 * 跟进处理
	 */
	registerFollowClickEvent:function(){
		$('#btnFollow').on('click',function(){
			var message = app.vtranslate('JS_LBL_FOLLOW_CONFIRM_MESSAGE');
			Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
				function(e) {
					//参数设置
					var postData = {
						"module": app.getModuleName(),
						"action": "SaveAjax",
						"record": jQuery('#recordId').val(),
						"type":"followup"
					}
					//发送请求
					AppConnector.request(postData).then(
						function(data){
							if(data.success ==  true && data.result[0] =="followup"){
								var message = app.vtranslate('JS_NO_PASS_MESSAGE');
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
							alert(error);
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
    addVisitImprovement:function(){
        var thisinstance=this;
		$('#VisitingOrder_detailView_basicAction_LBL_ADDVISITIMPROVEMENT').click(function(){


                var message='<h3>改进意见</h3>';
                var msg={
                    'message':message,
                    "width":800
                };
                var recordid=$('#recordId').val();
                thisinstance.showConfirmationBox(msg).then(function(e){
                    //alert($('#recordId').val());return;
                    var params={};
                    params['record'] = recordid;
                    params['remark'] = $('#remark').val();
                    params['action'] = 'ChangeAjax';
                    params['module'] = 'VisitingOrder';
                    params['mode'] = 'saveVisitImprovement';
                    AppConnector.request(params).then(
                        function(data) {
                            window.location.reload(true);
                        },
                        function(error,err){
                            //window.location.reload(true);
                        }
                    );
                },function(error, err) {});
                $('.modal-content .modal-body').append('<table class="table table-bordered blockContainer Duplicates showInlineTable  detailview-table" data-num="yesreplace"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>改进意见</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><textarea class="span11" name="remark" id="remark"></textarea></span></div></td></tr></tbody></table>');
                $('.modal-content .modal-body').css({overflow:'hidden'});

            });

	},
    checkedform:function(){
        if($('#remark').val()==''){
            $('#remark').focus();
            $('#remark').attr('data-content','<font color="red">必填项不能为空!</font>');;
            $('#remark').popover("show");
            $('.popover').css('z-index',1000010);
            $('.popover-content').css({"color":"red","fontSize":"12px"});
            setTimeout("$('#remark').popover('destroy')",2000);
            return false;
        }
        return true;
    },
    showConfirmationBox : function(data){
        var thisstance=this;
        var aDeferred = jQuery.Deferred();
        var width='800px';
        if(typeof  data['width'] != "undefined"){
            width=data['width'];
        }
        var bootBoxModal = bootbox.confirm({message:data['message'],width:width, callback:function(result) {
            if(result){
                if(thisstance.checkedform()){
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
    },
	btnEyeStatus:function(){
        var thisinstance=this;
		$('#page').on('click','.buthidden',function(){
			if($(this).attr('stats')=='hide'){
                $(this).attr('stats','show');
                $(this).html('<i class="icon-eye-close" title="显示"></i>');

                $('.hiddensc').hide();
			}else{
                $(this).attr('stats','hide');
                $(this).html('<i class="icon-eye-open" title="隐藏"></i>');
                $('.hiddensc').show();
			}
		});
        $('#page').on('click','.btnaddschedule',function(){
            var message='<h3>改进进度</h3>';
            var msg={
                'message':message,
                "width":800
            };
            var recordid=$('#recordId').val();
            var dataid=$(this).data('id');
            thisinstance.showConfirmationBox(msg).then(function(e){
                var params={};
                params['record'] = recordid;
                params['dataid'] = dataid;
                params['remark'] = $('#remark').val();
                //params['schedule'] = $('select[name="vschedule"]').val();
                params['schedule'] = $('input[name="vschedule"]').val();
                params['action'] = 'ChangeAjax';
                params['module'] = 'VisitingOrder';
               	params['mode'] = 'saveSchedule';
                AppConnector.request(params).then(
                    function(data) {
                        window.location.reload(true);
                    },
                    function(error,err){
                        //window.location.reload(true);
                    }
                );
            },function(error, err) {});
            //$('.modal-content .modal-body').append('<table class="table table-bordered blockContainer Duplicates showInlineTable  detailview-table" data-num="yesreplace"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>进度</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><select name="vschedule"><option value="10">10%</option><option value="20">20%</option><option value="30">30%</option><option value="40">40%</option><option value="50">50%</option><option value="60">60%</option><option value="70">70%</option><option value="80">80%</option><option value="90">90%</option><option value="100">100%</option></select></span></div><div id="slider"><div id="h-slider"></div></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>备注</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><textarea class="span11" name="remark" id="remark"></textarea></span></div></td></tr></tbody></table>');
            $('.modal-content .modal-body').append('<table class="table table-bordered blockContainer Duplicates showInlineTable  detailview-table" data-num="yesreplace"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>进度</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><input name="vschedule" type="hidden" value="10" /><div id="slider"><div id="h-slider"></div></div></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>备注</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><textarea class="span11" name="remark" id="remark"></textarea></span></div></td></tr></tbody></table>');
            $('.modal-content .modal-body').css({overflow:'hidden'});
            $( "#h-slider" ).slider( "value", 255 );
            //var handle = $( "#h-slider" );
            $( "#slider" ).slider({
                range: "min",min: 10,max: 100,value: 10,step: 10,
                create: function() {
                    $('#slider a').text(10);
                },
                slide: function( event, ui ) {
                    $('#slider a').text( ui.value );
                    $('input[name="vschedule"]').val(ui.value);
                    if(ui.value>80){
                        $('#slider div').css("background",'#5CB45C');
					}else if(ui.value>60){
                        $('#slider div').css("background",'#46ADCB');
					}else if(ui.value>30){
                        $('#slider div').css("background",'#F9A125');
                    }else{
                        $('#slider div').css("background",'#D64B45');
                    }
                }
            });
        });
	},
	loadingwidgetContainer:function(){
        var widgetContainer = $(".widgetContainer_SoundAComments");
        var urlParams = widgetContainer.attr('data-url');
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
            },
            function(){}
        );
	},
	doRevoke:function(){
		$("#VisitingOrder_detailView_basicAction_LBL_REVOKE").on("click",function(){
            var message='<h3>撤销拜访单</h3>';
            var msg={
                'message':message,
                "width":800
            };
            var recordid=$('#recordId').val();
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                var params={};
                params['record'] = recordid;
                params['action'] = 'ChangeAjax';
                params['module'] = 'VisitingOrder';
                params['mode'] = 'doRevoke';
                AppConnector.request(params).then(
                    function(data) {
                        window.location.reload(true);
                    },
                    function(error,err){
                        window.location.reload(true);
                    }
                );
            },function(error, err) {});
		});
	},
	registerEvents:function(){
		this._super();
		this.registerFollowClickEvent();
		this.registerAuditClickEvent();
		this.registerRejectClickEvent();
		this.addVisitImprovement();
		this.btnEyeStatus();
		this.doRevoke();
		this.loadingwidgetContainer();
	}
});