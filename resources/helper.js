/*+***********************************************************************************
 * 公共js类
 * @author young.yang 2015-05-27
 * @copyright CRM
 *************************************************************************************/
jQuery.Class("Vtiger_Helper_Js",{

	checkServerConfigResponseCache : '',
	/**
	 * 获取邮箱实例
	 */
	getEmailMassEditInstance : function(){
		var className = 'Emails_MassEdit_Js';
		var emailMassEditInstance = new window[className]();
		return emailMassEditInstance
	},
    /**
	 * 检查服务器配置
	 * 方法很重要，在ajax请求中使用
	 * returns boolean true or false
	 */
	checkServerConfig : function(module){
		var aDeferred = jQuery.Deferred();
		var actionParams = {
			"action": 'CheckServerInfo',
			'module' : module
		};
		AppConnector.request(actionParams).then(
			function(data) {
				var state = false;
				if(data.result){
					state = true;
				} else {
					state = false;
				}
				aDeferred.resolve(state);
			}
		);
		return aDeferred.promise();
	},
	/**
	 * 返回格式化的时间实例
	 * @params date---字段值
	 * @params dateFormat---时间格式
	 * @return date object
	 */
	getDateInstance : function(dateTime,dateFormat){
		var dateTimeComponents = dateTime.split(" ");
		var dateComponent = dateTimeComponents[0];
		var timeComponent = dateTimeComponents[1];
        var seconds = '00';

		var splittedDate = dateComponent.split("-");
		var splittedDateFormat = dateFormat.split("-");
		var year = splittedDate[splittedDateFormat.indexOf("yyyy")];
		var month = splittedDate[splittedDateFormat.indexOf("mm")];
		var date = splittedDate[splittedDateFormat.indexOf("dd")];
		if((year.length > 4) || (month.length > 2) || (date.length > 2)){
				var errorMsg = app.vtranslate("JS_INVALID_DATE");
				throw errorMsg;
		}

		if(typeof timeComponent == "undefined"){
			timeComponent = '00:00:00';
		}

        var timeSections = timeComponent.split(':');
        if(typeof timeSections[2] != 'undefined'){
            seconds = timeSections[2];
        }

		if(typeof dateTimeComponents[2] != 'undefined') {
			timeComponent += ' ' + dateTimeComponents[2];
            if(dateTimeComponents[2].toLowerCase() == 'pm' && timeSections[0] != '12') {
                timeSections[0] = parseInt(timeSections[0], 10) + 12;
            }

            if(dateTimeComponents[2].toLowerCase() == 'am' && timeSections[0] == '12') {
                timeSections[0] = '00';
            }
		}

        month = month-1;
		var dateInstance = new Date(year,month,date,timeSections[0],timeSections[1],seconds);
        return dateInstance;
	},
	requestToShowComposeEmailForm : function(selectedId,fieldname){
		var selectedFields = new Array();
		selectedFields.push(fieldname);
		var selectedIds =  new Array();
		selectedIds.push(selectedId);
		var params = {
			'module' : 'Emails',
			'selectedFields' : selectedFields,
			'selected_ids' : selectedIds,
			'view' : 'ComposeEmail'
		}
		var emailsMassEditInstance = Vtiger_Helper_Js.getEmailMassEditInstance();
		emailsMassEditInstance.showComposeEmailForm(params);
	},

	/**
	 * 邮件弹出
     */
	getInternalMailer  : function(selectedId,fieldname){
		var module = 'Emails';
		var cacheResponse = Vtiger_Helper_Js.checkServerConfigResponseCache;
		var  checkServerConfigPostOperations = function (data) {
			if(data == true){
				Vtiger_Helper_Js.requestToShowComposeEmailForm(selectedId,fieldname);
			} else {
				alert(app.vtranslate('JS_EMAIL_SERVER_CONFIGURATION'));
			}
		}
		if(cacheResponse === ''){
			var checkServerConfig = Vtiger_Helper_Js.checkServerConfig(module);
			checkServerConfig.then(function(data){
				Vtiger_Helper_Js.checkServerConfigResponseCache = data;
				checkServerConfigPostOperations(Vtiger_Helper_Js.checkServerConfigResponseCache);
			});
		} else {
			checkServerConfigPostOperations(Vtiger_Helper_Js.checkServerConfigResponseCache);
		}
	},
	
	/**
	 * 弹出框更新，加入确认取消按钮
	 */
	showConfirmationBox : function(data){
		var aDeferred = jQuery.Deferred();
		var width='800px';
		if(typeof  data['width'] != "undefined"){
            width=data['width'];
        }
        var checkFlag=true
		var bootBoxModal = bootbox.confirm({message:data['message'],width:width, callback:function(result) {
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
    /**
     * young.yang 2015-05-21 工单审核专用，弹出指定下一个人
     */
    showSDialogBox : function(data){
        var aDeferred = jQuery.Deferred();
        var msg={};
        msg.title=data.title;
        msg.message=data.message;
        msg.buttons= {
            success: {
                label: "指定客服并审核",
                // 按钮的类名
                className: "btn-success assignservice",
                callback: function () {
                        if($("#customer").val()>0){
                            aDeferred.resolve('assign');
                        }else{
                            //$('.alert-error').removeClass('hide');
                            $('.errormsg').html('<font color=red>请选择客服</font>');
                            return false;
                        }
                }
            },
            "Danger": {
                label: "直接审核不指定客服",
                className: "btn-info",
                callback: function () {
                    aDeferred.resolve('noassign');
                }
            },
            "Cancel": {
                label: "取消",
                className: "btn",
                callback: function () {
                    aDeferred.reject();
                }
            }
        };
        var bootBoxModal = bootbox.dialog(msg);
        bootBoxModal.on('hidden',function(e){
            if(jQuery('#globalmodal').length > 0) {
                // Mimic bootstrap modal action body state change
                jQuery('body').addClass('modal-open');
            }
        })
        return aDeferred.promise();
    },
    /**
     * young.yang 2015-05-21 工单审核专用，一般审核按钮
     */
    showDialogBox : function(data){
        var aDeferred = jQuery.Deferred();
        var msg={};
        msg.title=data.title;
        msg.message=data.message;
        /*msg.width="800px";*/
        msg.buttons= {
            success: {
                label: "审核节点",
                // 按钮的类名
                className: "btn-success",
                callback: function () {
                    aDeferred.resolve('ok');
                }
            },
            "Cancel": {
                label: "取消",
                className: "btn",
                callback: function () {
                    aDeferred.reject();
                }
            }
        };
        var bootBoxModal = bootbox.dialog(msg);
        bootBoxModal.on('hidden',function(e){
            //In Case of multiple modal. like mass edit and quick create, if bootbox is shown and hidden , it will remove
            // modal open
            if(jQuery('#globalmodal').length > 0) {
                // Mimic bootstrap modal action body state change
                jQuery('body').addClass('modal-open');
            }
        })
        return aDeferred.promise();
    },
    showPubDialogBox : function(data){
        var aDeferred = jQuery.Deferred();
        var msg={};
        msg.title=data.title;
        msg.message=data.message;
        msg.modal = false;
        if(typeof  data.width != "undefined"){
            msg.width=data.width;
        }
        msg.buttons= {
                success: {
                    label: "确认",
                    className: "btn-success",
                    callback: function () {
                        if(typeof msg.form == undefined){
                            aDeferred.resolve('ok');
                        }else{
                                $('#' + data.form).validationEngine({
                                    promptPosition: 'centerRight',
                                    scroll: false
                                });

                            if($('#'+data.form).validationEngine('validate')){
                                aDeferred.resolve('ok');
                            }else{
                                return false;
                            }
                        }
                    }
                },
                "Cancel": {
                    label: "取消",
                    className: "btn",
                    callback: function () {
                        aDeferred.reject();
                    }
                }
            };

        var bootBoxModal = bootbox.dialog(msg);
        bootBoxModal.on('hidden',function(e){
            //In Case of multiple modal. like mass edit and quick create, if bootbox is shown and hidden , it will remove
            // modal open
            if(jQuery('#globalmodal').length > 0) {
                // Mimic bootstrap modal action body state change
                jQuery('body').addClass('modal-open');
            }
        })
        return aDeferred.promise();
    },

    //2015年8月13日 wangbin 重写盒子调用方法 任务包发送邮件邮箱为空的问题;

    showPubDialogBox1 : function(data){
        var aDeferred = jQuery.Deferred();
        var msg={};
        msg.title=data.title;
        msg.message=data.message;
        msg.backdrop = false;
        if(typeof  data.width != "undefined"){
            msg.width=data.width;
        }
        msg.buttons= {
            "success": {
                label: "确认",
                className: "btn-success",
                callback: function () {
                    if (typeof msg.form == undefined) {
                        aDeferred.resolve('ok');
                    }else{
                        var t = $("#test").val();
                        var rr = $("#memberReceive").val();
                        var r = $("#custom_rece").val();
                        var c = $("#custom_copy").val();
                        if($("input[name='mail[issendit]']").is(':checked') && !$("input[name='pauseaudit']").is(':checked')){
                                if(t== 3 && (r==""||c == "")){
                                    alert("请补全无邮件的联系人");
                                    return false;
                                }else if(t==1 && r==""){
                                    alert("请补全收件人邮箱");
                                    return false;
                                }else if(t==2 && c==""){
                                    alert("请补全抄送人");
                                    return false;
                                }else if(rr==null && r==""){
                                    alert("收件人不能为空");
                                    return false;
                                }else{
                                    aDeferred.resolve('ok');
                                }
                        }else{
                            aDeferred.resolve('ok');
                        }

                    }
                }
            },
            "Cancel": {
                label: "取消",
                className: "btn",
                callback: function () {
                    location.reload();
                    aDeferred.reject();

                }
            }
        };

        var bootBoxModal = bootbox.dialog(msg);
        bootBoxModal.on('hidden',function(e){
            location.reload();
            //In Case of multiple modal. like mass edit and quick create, if bootbox is shown and hidden , it will remove
            // modal open
            if(jQuery('#globalmodal').length > 0) {
                // Mimic bootstrap modal action body state change
                jQuery('body').addClass('modal-open');
            }
        })
        return aDeferred.promise();
    },

    //end
	/**
	 * 检查重复
	 */
	checkDuplicateName : function(details) {
		var accountName = details.accountName;
		var recordId = details.recordId;
		var aDeferred = jQuery.Deferred();
		var moduleName = details.moduleName;
		if(typeof moduleName == "undefined"){
			moduleName = app.getModuleName();
		}
		var params = {
		'module' : moduleName,
		'action' : "CheckDuplicate",
		'accountname' : accountName,
		'record' : recordId
		}
		AppConnector.request(params).then(
			function(data) {
				var response = data['result'];
				var result = response['success'];
				if(result == true) {
                    jQuery('input[name="accountname"]').next().remove();
					jQuery('input[name="accountname"]').after('<P><font style="color:red">'+response['message']+'</font></P>');
					aDeferred.reject(response);
				} else {
                    jQuery('input[name="accountname"]').next().remove();
					aDeferred.resolve(response);
				}
			},
			function(error,err){
				aDeferred.reject();
			}
		);
		return aDeferred.promise();
	},
    /**
     * 弹出右上角信息，
     * params={text: message,type: 'info'};
     * type:notice(警告),info(信息),success(成功),error(错误)
     * @param params
     */
	showMessage : function(params){
		if(typeof params.type == "undefined"){
			params.type = 'info';//notice,info,success,error,
		}
		params.animation = "show";
		params.title = app.vtranslate('JS_MESSAGE'),
		Vtiger_Helper_Js.showPnotify(params);
	},

    /**
     * 弹出右上角信息，
     * params={title : '标题',text: '信息',animation: 'show',
     *       type: 'error'};
     * type:notice(警告),info(信息),success(成功),error(错误)
     * @param params
     */
	showPnotify : function(customParams) {
		var userParams = customParams;
		if(typeof customParams == 'string') {
			var userParams = {};
			userParams.text = customParams;
		}
		if(typeof userParams.type=='undefined'){
			userParams.type='error';
		}
		var params = {
			sticker: false,
			delay: '3000',
			type: 'error',
			pnotify_history: false,
			stack: {"dir1": "up", "dir2": "left", "push": "bottom", "spacing1": 25, "spacing2": 25}
		}
		if(typeof userParams != 'undefined'){
			var params = jQuery.extend(params,userParams);
		}
		return jQuery.pnotify(params);
	},
    
    /**
    * Function to add clickoutside event on the element - By using outside events plugin 
    * @params element---On which element you want to apply the click outside event 
    * @params callbackFunction---This function will contain the actions triggered after clickoutside event 
    */ 
    addClickOutSideEvent : function(element, callbackFunction) { 
        element.one('clickoutside',callbackFunction); 
    },

    /**
     * 滚动条
     */
	showHorizontalTopScrollBar : function() {
		var container = jQuery('.contentsDiv');
		var topScroll = jQuery('.contents-topscroll',container);
		var bottomScroll = jQuery('.contents-bottomscroll', container);
        var parentName=$('input[name="parent"]').val();
        var row=$('.row').height()+20;
        var navBarFixedTop=$('.navbar-fixed-top').height();
        var breadcrumb=$('.breadcrumb').height()+10;
        var SearchBlankCover=$('#SearchBug').height();
        if(parentName!='Settings') {
            var minheight = jQuery(window).height() - (row + breadcrumb + navBarFixedTop + SearchBlankCover + 35);
        }else{
            var minheight=jQuery(window).height()- 410;
        }
		
		//jQuery('.topscroll-div', container).css('width', jQuery('.bottomscroll-div', container).outerWidth());
		jQuery('.topscroll-div', container).css('width', minheight);

		topScroll.scroll(function(){
			bottomScroll.scrollLeft(topScroll.scrollLeft())

            if(parentName!='Settings') {
                var row = $('.row').height() + 20;
                var navBarFixedTop = $('.navbar-fixed-top').height();
                var breadcrumb = $('.breadcrumb').height() + 10;
                var SearchBlankCover = $('#SearchBug').height();
                var minheight = jQuery(window).height() - (row + breadcrumb + navBarFixedTop + SearchBlankCover+80);
            }else{
                var minheight=jQuery(window).height()- 410;
            }
            $('.fht-tbody').css({"height":minheight+'px'});
		});
		
		bottomScroll.scroll(function(){
			topScroll.scrollLeft(bottomScroll.scrollLeft());
		});
	},
    /**
     * 弹出远程url地址模式框,
     * @param url
     */
	showDialog :function(data){
        var aDeferred = jQuery.Deferred();
        var msg={};
        msg.title=data.title;
        msg.message=data.message;
        msg.width="1000px";
        msg.buttons= {
            success: {
                label: "数据完成提交",
                // 按钮的类名
                className: "btn-success",
                callback: function () {
                    $('#EditView').validationEngine('attach');
                    //console.log($('#EditView').validationEngine('validate'));
                    if($('#EditView').validationEngine('validate')){
                        aDeferred.resolve('ok');
                    }else{
                        Vtiger_Helper_Js.showMessage({text:'必填项'});
                        return false;
                    }
                    //aDeferred.resolve('ok');
                }
            },
            err: {
                label: "保存不提交",
                // 按钮的类名
                className: "btn-info",
                callback: function () {
                    $('#EditView').validationEngine('attach');
                    //console.log($('#EditView').validationEngine('validate'));
                    if($('#EditView').validationEngine('validate')){
                        aDeferred.resolve('Pause');
                    }else{
                        Vtiger_Helper_Js.showMessage({text:'必填项'});
                        return false;
                    }
                    //aDeferred.resolve('ok');
                }
            },
            "Cancel": {
                label: "取消",
                className: "btn",
                callback: function () {
                    aDeferred.reject();
                }
            }
        };

        var bootBoxModal = bootbox.dialog(msg);
        bootBoxModal.on('hidden',function(e){
            //In Case of multiple modal. like mass edit and quick create, if bootbox is shown and hidden , it will remove
            // modal open
            if(jQuery('#globalmodal').length > 0) {
                // Mimic bootstrap modal action body state change
                jQuery('body').addClass('modal-open');
            }
        })

        return aDeferred.promise();
	}


},{});