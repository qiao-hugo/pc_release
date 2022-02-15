/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("Accounts_Detail_Js",{
	
	//It stores the Account Hierarchy response data
	accountHierarchyResponseCache : {},
	
	/*
	 * function to trigger Account Hierarchy action
	 * @param: Account Hierarchy Url.
	 */
	triggerAccountHierarchy : function(accountHierarchyUrl) {
		Accounts_Detail_Js.getAccountHierarchyResponseData(accountHierarchyUrl).then(
			function(data) {
				Accounts_Detail_Js.displayAccountHierarchyResponseData(data);
			}
		);
		
	},
	TransferAccount:function(accountid){

		var msg = {
			'message': '确定要同步吗？'
		};
		Vtiger_Helper_Js.showConfirmationBox(msg).then(
			function(e) {
				var module = app.getModuleName();
				var postData = {
					"module": module,
					"action": "ChangeAjax",
					'mode': 'transferAccount',
					"record": accountid
				}
				var Message = "正在处理中,请稍等...";
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
							Vtiger_Helper_Js.showPnotify(app.vtranslate(data.result.msg));
						}
					},
					function(error,err){

					}
				);
			},function(error, err){}
		);
		
	},
	
	/*
	 * function to get the AccountHierarchy response data
	 */
	getAccountHierarchyResponseData : function(params) {
		var aDeferred = jQuery.Deferred();
		
		//Check in the cache
		if(!(jQuery.isEmptyObject(Accounts_Detail_Js.accountHierarchyResponseCache))) {
			aDeferred.resolve(Accounts_Detail_Js.accountHierarchyResponseCache);
		} else {
			AppConnector.request(params).then(
				function(data) {
					//store it in the cache, so that we dont do multiple request
					Accounts_Detail_Js.accountHierarchyResponseCache = data;
					aDeferred.resolve(Accounts_Detail_Js.accountHierarchyResponseCache);
				}
			);
		}
		return aDeferred.promise();
	},
	
	/*
	 * function to display the AccountHierarchy response data
	 */
	displayAccountHierarchyResponseData : function(data) {
        var callbackFunction = function(data) {
            app.showScrollBar(jQuery('#hierarchyScroll'), {
                height: '200px',
                railVisible: true,
                alwaysVisible: true,
                size: '6px'
            });
        }
        app.showModalWindow(data, function(data){
            if(typeof callbackFunction == 'function'){
                callbackFunction(data);
            }
        });
	}
},{
	//Cache which will store account name and whether it is duplicate or not
	accountDuplicationCheckCache : {},

	getDeleteMessageKey : function() {
		return 'LBL_RELATED_RECORD_DELETE_CONFIRMATION';
	},
	
	isAccountNameDuplicate : function(params) {
		var thisInstance = this;
		var accountName = params.accountName;
		var aDeferred = jQuery.Deferred();

		var analyzeResponse = function(response){
			if(response['success'] == true) {
				aDeferred.reject(response['message']);
			}else{
				aDeferred.resolve();
			}
		}

		if(accountName in thisInstance.accountDuplicationCheckCache) {
			analyzeResponse(thisInstance.accountDuplicationCheckCache[accountName]);
		}else{
			Vtiger_Helper_Js.checkDuplicateName(params).then(
				function(response){
					thisInstance.accountDuplicationCheckCache[accountName] = response;
					analyzeResponse(response);
				},
				function(response) {
					thisInstance.accountDuplicationCheckCache[accountName] = response;
					analyzeResponse(response);
				}
			);
		}
		return aDeferred.promise();
	},

	saveFieldValues : function (fieldDetailList) {
		var thisInstance = this;
		var targetFn = this._super;
		
		var fieldName = fieldDetailList.field;
		if(fieldName != 'accountname') {
			return targetFn.call(thisInstance, fieldDetailList);
		}

		var aDeferred = jQuery.Deferred();
		fieldDetailList.accountName = fieldDetailList.value;
		fieldDetailList.recordId = this.getRecordId();
		this.isAccountNameDuplicate(fieldDetailList).then(
			function() {
				targetFn.call(thisInstance, fieldDetailList).then(
					function(data){
						aDeferred.resolve(data);
					},function() {
						aDeferred.reject();
					}
				);
			},
			function(message) {
				var form = thisInstance.getForm();
				var params = {
					title: app.vtranslate('JS_DUPLICATE_RECORD'),
					text: app.vtranslate(message),
					width: '35%'
				};
				Vtiger_Helper_Js.showPnotify(params);
				form.find('[name="accountname"]').closest('td.fieldValue').trigger('click');
				aDeferred.reject();
			}
		)
		return aDeferred.promise();
	},

    aleretCommentCallback:function(){
        var startDate=$('#JobAlerts_editView_fieldName_alerttime').data('sdate');
        $('#JobAlerts_editView_fieldName_alerttime').datetimepicker({
            format: "yyyy-mm-dd hh:00",
            minView: 'day',
            language:  'zh-CN',
            autoclose: true,
            todayBtn: true,
            pickerPosition: "bottom-left",
            showMeridian: 0,
            startDate:startDate
        });
	},
	checkAlertData:function(container){
        $("body").on('click','.subAlertcomments',function(event){
            event.preventDefault();
            var subject = $('[name="subject"]').val();
            if(!subject){
                pp={
                    type:'error',text:'请输入主题!'
                };
                Vtiger_Helper_Js.showMessage(pp);
            	return false;
			}
            var alerttime = $('[name="alerttime"]').val();
            if(!alerttime){
                pp={
                    type:'error',text:'请输入提醒时间!'
                };
                Vtiger_Helper_Js.showMessage(pp);
                return false;
            }

            var alertid = $('[name="alertid[]"]').val();
            if(!alertid){
                pp={
                    type:'error',text:'请至少选择一个提醒人!'
                };
                Vtiger_Helper_Js.showMessage(pp);
                return false;
            }

            var alertcontent = $('[name="alertcontent"]').val();
            if(!alertcontent){
                pp={
                    type:'error',text:'请输入提醒内容!'
                };
                Vtiger_Helper_Js.showMessage(pp);
                return false;
            }

            $('.cancelLink').trigger('click');
            var params = {
                'module' : 'JobAlerts',
                'action' : 'SubSave',
                'modcommentsid' : $('#modcommentsid').val(),
                'type' : 'POST',
                'subject':$('[name="subject"]').val(),
                'alertcontent':$('[name="alertcontent"]').val(),
                'alerttime':$('[name="alerttime"]').val(),
                'alertid':$('[name="alertid[]"]').val(),
                'activitytype':$('[name="activitytype"]').val(),
                'taskpriority':$('[name="taskpriority"]').val(),
                'ownerid':$('[name="ownerid"]').val(),
                'remark':$('[name="remark"]').val(),
                'accountid':$('[name="accountid"]').val(),
                'edit':$('#id').val()
            };
            //alert($('[name="accountid"]').val());  弹出未取消
            var pp={};
            AppConnector.request(params).then(function(data){
                if(data.success==true){
                    if (data.result[0] ==1){
                        pp={
                            type:'error',text:'请至少选择一个提醒人!'
                        };
                        Vtiger_Helper_Js.showMessage(pp);
                        return false;
                    }
                    if (data.result[0] ==2){
                        pp={
                            type:'error',text:'请输入提醒时间!'
                        };
                        Vtiger_Helper_Js.showMessage(pp);
                        return false;
                    }
                    pp={
                        type:'success',text:'成功'
                    };
                    Vtiger_Helper_Js.showMessage(pp);
                }else{
                    pp={
                        type:'error',text:'失败'+data.result
                    };
                    Vtiger_Helper_Js.showMessage(pp);
                }

            },function(){}).then(function(){
                if($('div').hasClass('widgetContainer_0')){
                    var widgetList = jQuery('.widgetContainer_0');
                    var vdjs=new Vtiger_Detail_Js;
                    vdjs.loadWidget(widgetList);
                }else{
                    $('li[data-label-key="ModComments"]').trigger("click");
                }
            },function(){});

            return false;
        });

    },
	saveComment : function(e) {
		var thisInstance = this;
		var aDeferred = jQuery.Deferred();
		var currentTarget = jQuery(e.currentTarget);
		var commentMode = currentTarget.data('mode');
		var closestCommentBlock = currentTarget.closest('.addCommentBlock');
		var commentContent = closestCommentBlock.find('.commentcontent');
		var commentContentValue = commentContent.val();

		var modcommentmode = closestCommentBlock.find('.modcommentmode');
		var modcommentmodeValue = modcommentmode.val();

		var modcommenttype = closestCommentBlock.find('.modcommenttype');
		var modcommenttypeValue = modcommenttype.val();

		var modcommentpurpose = closestCommentBlock.find('.modcommentpurpose');
		var modcommentpurposeValue = modcommentpurpose.val();

		var modcommentcontacts = closestCommentBlock.find('.modcommentcontacts');
		var modcommentcontactsValue = modcommentcontacts.val();

		var modcommentupdateautotask =closestCommentBlock.find('.updateautotask');
		var modcommentupdateautotaskValue = modcommentupdateautotask.is(":checked");

		var modcommentis_service = closestCommentBlock.find('.is_service');
		var modcommentis_serviceValue = modcommentis_service.val();

		var accountintentionality = closestCommentBlock.find('.accountintentionality');
		var accountintentionalityValue = accountintentionality.val();
		console.log(accountintentionalityValue);
		var modcommentis_folowplanValue="";
		if($('input[name="isfollowplan"]').is(":checked")){
			var modcommentis_folowplanValue = $('#commentreturnplanid').val();//客服回访计划任务id
		}
		var errorMsg = app.vtranslate('JS_LBL_COMMENT_VALUE_CANT_BE_EMPTY');
		if(modcommenttypeValue==''){
			modcommenttype.validationEngine('showPrompt', errorMsg , 'error','topRight',true);
			aDeferred.reject();
			return aDeferred.promise();
		}
        if(accountintentionalityValue==''){
            accountintentionality.validationEngine('showPrompt', errorMsg , 'error','topRight',true);
            aDeferred.reject();
            return aDeferred.promise();
        }
		if(modcommentmodeValue==''){
			modcommentmode.validationEngine('showPrompt', errorMsg , 'error','topRight',true);
			aDeferred.reject();
			return aDeferred.promise();
		}
		if(modcommentcontactsValue==''){
			modcommentcontacts.validationEngine('showPrompt', errorMsg , 'error','topRight',true);
			aDeferred.reject();
			return aDeferred.promise();
		}
		if(strlen(commentContentValue)<20){
			commentContent.validationEngine('showPrompt', '跟进记录不得少于20字' , 'error','topRight',true);
			aDeferred.reject();
			return aDeferred.promise();
		}
		var trueflag=true;
		var followupData=[];
		if(app.getModuleName()=='Accounts'){
			if(modcommenttypeValue=='首次客户录入系统跟进' || modcommenttypeValue=='首次拜访客户后跟进'){
				trueflag=false;
				var followupflag=false;
                var followupinviteres = $("#followupinviteres").val();
                if(modcommenttypeValue=='首次拜访客户后跟进'){
					var followupdata=$('input[name*="followupvisit["]');
					var thisNum=11;
				}else{
					var followupdata=$('input[name*="followup["]');
					var thisNum=16;
					var followupinviteres = $("#followupinviteres").val();
					if(!followupinviteres){
                        $("#followupinviteres").validationEngine('showPrompt', errorMsg , 'error','bottomLeft',true);
                        followupflag=true;
                        return false;
					}

				}
				$.each(followupdata,function(key,value){
                    var valuedata=$(value).val();
                    var thisid=$(value).data('thisid');
                    if(thisid==2 && modcommenttypeValue=='首次拜访客户后跟进'){
						var leaderVal = $("input[name='leader']:checked").val();
						followupData[key]=key+'**#**'+leaderVal;
                    }else{
                        followupData[key]=key+'**#**'+valuedata;
					}
                    if(modcommenttypeValue=='首次客户录入系统跟进'){
                    	if((followupinviteres=='是' && thisid>=9 &&thisid<=10) || (followupinviteres=='否' && thisid>11)){
                    		console.log(thisid);
                    		return true;
						}
					}
					if($.trim(valuedata)=='' && thisid<thisNum){
						$(value).validationEngine('showPrompt', errorMsg , 'error','bottomLeft',true);
						followupflag=true;
						return false;
					}

				});
				if(followupflag){
					aDeferred.reject();
					return aDeferred.promise();
				}
			}
		}
		//return aDeferred.promise();
		var errorMsg;
		if(commentContentValue == "" && trueflag){
			errorMsg = app.vtranslate('JS_LBL_COMMENT_VALUE_CANT_BE_EMPTY');
			commentContent.validationEngine('showPrompt', errorMsg , 'error','bottomLeft',true);
			aDeferred.reject();
			return aDeferred.promise();
		}
		if(commentMode == "edit"){
			var editCommentReason = closestCommentBlock.find('.commenthistory').val();
		}

		var progressIndicatorElement = jQuery.progressIndicator({});
		var element = jQuery(e.currentTarget);
		element.attr('disabled', 'disabled');

		var commentInfoHeader = closestCommentBlock.closest('.commentDetails').find('.commentInfoHeader');
		var commentId = commentInfoHeader.data('commentid');
		var parentCommentId = commentInfoHeader.data('parentcommentid');
		var postData =
			{
				'commentcontent' : 	commentContentValue,
				'modcommentmode' :  modcommentmodeValue,
				'modcommenttype' :  modcommenttypeValue,
				'modcommentpurpose' : modcommentpurposeValue,
				'contact_id': modcommentcontactsValue,
				'related_to': thisInstance.getRecordId(),
				'module' : 'ModComments',
				'modulename':app.getModuleName(),
				'moduleid':thisInstance.getRecordId(),
				'ifupdateservice':modcommentupdateautotaskValue,
				'accountid':jQuery('#accountId').val(),
				'is_service':modcommentis_serviceValue,
				'isfollowplain':modcommentis_folowplanValue,
				'followupdata':followupData,
				'accountintentionality':accountintentionalityValue
			}
		if(commentMode == "edit"){
			delete(postData.commentcontent);

			postData['record'] = commentId;
			postData['modcommenthistory'] = editCommentReason;
			//postData['parent_comments'] = parentCommentId;
			postData['mode'] = 'edit';
			postData['action'] = 'Save';
		} else if(commentMode == "add"){
			postData['action'] = 'SaveAjax';
		}
		AppConnector.request(postData).then(
			function(data){
				if(data.success){
                    if(data.result.accountcategory && accountintentionalityValue!='zeropercentage'){
                        console.log(1111);
                        alert("意向度大于0%，当前客户为"+data.result.accountcategory+"客户，领取到正常保护区后客户才会进入意向客户池")
                    }
				}

				progressIndicatorElement.progressIndicator({'mode':'hide'});
				element.removeAttr('disabled');
				aDeferred.resolve(data);
				/*if(data.result){
                    window.location.href=data.result;
                }*/
			},
			function(textStatus, errorThrown){
				progressIndicatorElement.progressIndicator({'mode':'hide'});
				element.removeAttr('disabled');
				aDeferred.reject(textStatus, errorThrown);
			}
		);

		return aDeferred.promise();
	},
	modcommenttypeChange:function(){
		$("body").on("change","select[name='modcommenttype']",function () {
			var value=$(this).val();
			if(value=='首次客户录入系统跟进'){
				$("#firstInput").css("display","block");
				$("#firstVisit").css("display","none");
				$(".commentcontent").css("display","none");
			}else if(value=='首次拜访客户后跟进'){
				$("#firstVisit").css("display","block");
				$("#firstInput").css("display","none");
				$(".commentcontent").css("display","none");
			}else{
				$("#firstInput").css("display","none");
				$("#firstVisit").css("display","none");
				$(".commentcontent").css("display","block");
			}
		});
	},
	followupyaoyue:function(){
        $("body").on("change","#followupinviteres",function () {
        	var value = $(this).val();
        	if(value=='是'){
        		$(".followup11tono").hide();
        		$(".followup11toyes").show();
			}else if(value=='否'){
                $(".followup11tono").show();
                $(".followup11toyes").hide();
			}else{
                $(".followup11tono").hide();
                $(".followup11toyes").hide();
                value='无';
			}
            $("input[name='followup[11]']").val(value);
        });
	},
	registerEvents:function (container) {
		this._super(container);
		this.checkAlertData(container);
		this.modcommenttypeChange();
		this.followupyaoyue();
    }

});
/**
 * @author cxh
 * @content  行业“教育”增加二级选项 查看时 重新拼接 和 显示数据 行业信息 只对教育行业处理其他不影响
 * @type {*|jQuery}
 */
// 从客户列表进入查看 或者保存后直接查看详情  然后把教育的子内容拼接在教育栏
if($.trim($("#Accounts_detailView_fieldValue_educationproperty").text()).length>0){
    $("#Accounts_detailView_fieldValue_industry").text($("#Accounts_detailView_fieldValue_industry").text()+">"+$("#Accounts_detailView_fieldValue_educationproperty").text());
}
// 上面操作完成后移除相应的内容
$("#Accounts_detailView_fieldValue_educationproperty").text("");
$("#Accounts_detailView_fieldLabel_educationproperty").text("");

function strlen(str){
	var len = 0;
	for (var i=0; i<str.length; i++) {
		var c = str.charCodeAt(i);
		//单字节加1
		if ((c >= 0x0001 && c <= 0x007e) || (0xff60<=c && c<=0xff9f)) {
			len++;
		}
		else {
			len+=2;
		}
	}
	return len;
}