Vtiger_Detail_Js("ContractReceivable_Detail_Js", {}, {
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

        var modcommentis_folowplanValue="";
        if($('input[name="isfollowplan"]').is(":checked")){
            var modcommentis_folowplanValue = $('#commentreturnplanid').val();//客服回访计划任务id
        }
        var errorMsg;
        if(commentContentValue == ""){
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
                'related_to': jQuery('#contractId').val(),
                'module' : 'ModComments',
                'modulename':'ServiceContracts',
                'moduleid':jQuery('#contractId').val(),
                'ifupdateservice':modcommentupdateautotaskValue,
                'accountid':jQuery('#accountId').val(),
                'is_service':modcommentis_serviceValue,
                'isfollowplain':modcommentis_folowplanValue
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

})