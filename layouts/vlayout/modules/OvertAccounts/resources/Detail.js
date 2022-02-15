/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("OvertAccounts_Detail_Js",{
	
	//It stores the Account Hierarchy response data
	accountHierarchyResponseCache : {},
	
	/*
	 * function to trigger Account Hierarchy action
	 * @param: Account Hierarchy Url.
	 */
	triggerAccountHierarchy : function(accountHierarchyUrl) {
        OvertAccounts_Detail_Js.getAccountHierarchyResponseData(accountHierarchyUrl).then(
			function(data) {
                OvertAccounts_Detail_Js.displayAccountHierarchyResponseData(data);
			}
		);
		
	},
	
	/*
	 * function to get the AccountHierarchy response data
	 */
	getAccountHierarchyResponseData : function(params) {
		var aDeferred = jQuery.Deferred();
		
		//Check in the cache
		if(!(jQuery.isEmptyObject(OvertAccounts_Detail_Js.accountHierarchyResponseCache))) {
			aDeferred.resolve(OvertAccounts_Detail_Js.accountHierarchyResponseCache);
		} else {
			AppConnector.request(params).then(
				function(data) {
					//store it in the cache, so that we dont do multiple request
                    OvertAccounts_Detail_Js.accountHierarchyResponseCache = data;
					aDeferred.resolve(OvertAccounts_Detail_Js.accountHierarchyResponseCache);
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
    fallToovertTime:function(){
        $('#OvertAccounts_detailView_basicAction_LBL_FALL_TOOVERT_TIME').click(function(){
            var msg={'message':"确定要解除公海五天限制吗？"};
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                var params={};
                params['record'] = $('#recordId').val();
                params['action'] = 'ChangeAjax';
                params['module'] = 'OvertAccounts';
                params['mode'] = 'cleanFallToovertTime';
                AppConnector.request(params).then(
                    function(data){
						window.location.reload();
                    },function(){}
                );
            });
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
        var trueflag=true;
        var followupData=[];
        if(app.getModuleName()=='Accounts'){
            if(modcommenttypeValue=='首次客户录入系统跟进' || modcommenttypeValue=='首次拜访客户后跟进'){
                trueflag=false;
                var followupflag=false;
                if(modcommenttypeValue=='首次拜访客户后跟进'){
                    var followupdata=$('input[name*="followupvisit["]');
                    var thisNum=11;
                }else{
                    var followupdata=$('input[name*="followup["]');
                    var thisNum=9;
                }
                $.each(followupdata,function(key,value){
                    var valuedata=$(value).val();
                    var thisid=$(value).data('thisid')
                    followupData[key]=key+'**#**'+valuedata;
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
    registerEvents: function() {
        this._super();
        this.fallToovertTime();
    }

});