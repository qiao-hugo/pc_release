/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

jQuery.Class("ContractExecution_Detail_Js",{

    detailInstance : false,

	getInstance: function(){
        if( Vtiger_Detail_Js.detailInstance == false ){
            var module = app.getModuleName();
            var moduleClassName = module+"_Detail_Js";
            var fallbackClassName = Vtiger_Detail_Js;
            if(typeof window[moduleClassName] != 'undefined'){
                var instance = new window[moduleClassName]();
            }else{
                var instance = new fallbackClassName();
            }
            Vtiger_Detail_Js.detailInstance = instance;
        }
        return Vtiger_Detail_Js.detailInstance;
	},



	/*
	 * function to trigger send Email
	 * @params: send email url , module name.
	 */
	triggerSendEmail : function(detailActionUrl, module){
       // Vtiger_Helper_Js.checkServerConfig(module).then(function(data){
        	//console.log(data);
			//if(data == true){
                var currentInstance = Vtiger_Detail_Js.getInstance();
                var parentRecord = new Array();
                var params = {};
                parentRecord.push(currentInstance.getRecordId());
                params['module'] = app.getModuleName();
                params['view'] = "MassActionAjax";
                params['selected_ids'] = parentRecord;
                params['mode'] = "showComposeEmailForm";
                params['step'] = "step1";
                params['relatedLoad'] = true;
                Vtiger_Index_Js.showComposeEmailPopup(params);
			//} else {
				//alert(app.vtranslate('JS_EMAIL_SERVER_CONFIGURATION'));
			//}
		//});
	},

    /*
	 * function to trigger Detail view actions
	 * @params: Action url , callback function.
	 */
    triggerDetailViewAction : function(detailActionUrl, callBackFunction){
		var detailInstance = Vtiger_Detail_Js.getInstance();
        var selectedIds = new Array();
        selectedIds.push(detailInstance.getRecordId());
        var postData = {
           "selected_ids": JSON.stringify(selectedIds)
        };
        var actionParams = {
			"type":"POST",
			"url":detailActionUrl,
			"dataType":"html",
			"data" : postData
		};

        AppConnector.request(actionParams).then(
			function(data) {
				if(data) {
					app.showModalWindow(data,{'text-align' : 'left'});
					if(typeof callBackFunction == 'function'){
						callBackFunction(data);
					}
				}
			},
			function(error,err){

			}
		);
    },

    /*
	 * function to trigger send Sms
	 * @params: send sms url , module name.
	 */
    triggerSendSms : function(detailActionUrl, module) {
        Vtiger_Helper_Js.checkServerConfig(module).then(function(data){
			if(data == true){
                Vtiger_Detail_Js.triggerDetailViewAction(detailActionUrl);
			} else {
				alert(app.vtranslate('JS_SMS_SERVER_CONFIGURATION'));
			}
		});
    },

	triggerTransferOwnership : function(massActionUrl){
		var thisInstance = this;
		thisInstance.getRelatedModulesContainer = false;
		var actionParams = {
			"type":"POST",
			"url":massActionUrl,
			"dataType":"html",
			"data" : {}
		};
		AppConnector.request(actionParams).then(
			function(data) {
				if(data) {
					var callback = function(data) {
						var params = app.validationEngineOptions;
						params.onValidationComplete = function(form, valid){
							if(valid){
								thisInstance.transferOwnershipSave(form)
							}
							return false;
						}
						jQuery('#changeOwner').validationEngine(app.validationEngineOptions);
					}
					app.showModalWindow(data, function(data){
						var selectElement = thisInstance.getRelatedModuleContainer();
						app.changeSelectElementView(selectElement, 'select2');
						if(typeof callback == 'function'){
							callback(data);
						}
					});
				}
			}
		);
	},

	transferOwnershipSave : function (form){
		var thisInstance = this;
		var transferOwner = jQuery('#transferOwnerId').val();
		var relatedModules = jQuery('#related_modules').val();
		var recordId = jQuery('#recordId').val();
		var params = {
			'module': app.getModuleName(),
			'action' : 'TransferOwnership',
			'record':recordId,
			'transferOwnerId' : transferOwner,
			'related_modules' : relatedModules
		}
		AppConnector.request(params).then(
			function(data) {
				if(data.success){
					app.hideModalWindow();
					var params = {
						title : app.vtranslate('JS_MESSAGE'),
						text: app.vtranslate('JS_RECORDS_TRANSFERRED_SUCCESSFULLY'),
						animation: 'show',
						type: 'info'
					};
					Vtiger_Helper_Js.showPnotify(params);
				}
			}
		);
	},

	/*
	 * Function to get the related module container
	 */
	getRelatedModuleContainer  : function(){
		if(this.getRelatedModulesContainer == false){
			this.getRelatedModulesContainer = jQuery('#related_modules');
		}
		return this.getRelatedModulesContainer;
	},

	/*
	 * function to trigger delete record action
	 * @params: delete record url.
	 */
    deleteRecord : function(deleteRecordActionUrl) {
		var message = app.vtranslate('LBL_DELETE_CONFIRMATION');
		Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(function(data) {
				AppConnector.request(deleteRecordActionUrl+'&ajaxDelete=true').then(
				function(data){
					if(data.success == true){
						window.location.href = data.result;
					}else{
						Vtiger_Helper_Js.showPnotify(data.error.message);
					}
				});
			},
			function(error, err){
			}
		);
	},

	reloadRelatedList : function(){
		var pageNumber = jQuery('[name="currentPageNum"]').val();
		var detailInstance = Vtiger_Detail_Js.getInstance();
		detailInstance.loadRelatedList(pageNumber);
	}

},{

	detailViewContentHolder : false,
	detailViewForm : false,
	detailViewSummaryTabLabel : 'LBL_RECORD_SUMMARY',
	detailViewRecentCommentsTabLabel : 'ModComments',
	detailViewRecentActivitiesTabLabel : 'Activities',
	detailViewRecentUpdatesTabLabel : 'LBL_UPDATES',
	detailViewRecentDocumentsTabLabel : 'Documents',

	fieldUpdatedEvent : 'Vtiger.Field.Updated',
	widgetPostLoad : 'Vtiger.Widget.PostLoad',

	//Filels list on updation of which we need to upate the detailview header
	updatedFields : ['company','designation','title'],
	//Event that will triggered before saving the ajax edit of fields
	fieldPreSave : 'Vtiger.Field.PreSave',

	referenceFieldNames : {
		'Accounts' : 'parent_id',
		'Contacts' : 'contact_id',
		'Leads' : 'parent_id',
		'Potentials' : 'parent_id',
		'HelpDesk' : 'parent_id'
	},

	//constructor
	init : function() {

	},

	//右侧关联列表删除点击事件 wangbin 注释
	getDeleteMessageKey : function() {
		return 'LBL_DELETE_CONFIRMATION';
	},

	//加载data url 里面的东西
	loadWidgets : function(){
		var thisInstance = this;
		var widgetList = jQuery('[class^="widgetContainer_"]');
		widgetList.each(function(index,widgetContainerELement){
			var widgetContainer = jQuery(widgetContainerELement);
			thisInstance.loadWidget(widgetContainer);
            app.tabletrodd();  //加入隔行换色
		});
	},

	loadWidget : function(widgetContainer) {
		var thisInstance = this;
		var contentHeader = jQuery('.widget_header',widgetContainer);
		var contentContainer = jQuery('.widget_contents',widgetContainer);
		var urlParams = widgetContainer.data('url');
		var relatedModuleName = contentHeader.find('[name="relatedModule"]').val();

		var params = {
			'type' : 'GET',
			'dataType': 'html',
			'data' : urlParams
		};
		console.log(urlParams);
		contentContainer.progressIndicator({});
		AppConnector.request(params).then(
			function(data){
			    // console.log(data);
				contentContainer.progressIndicator({'mode': 'hide'});
				contentContainer.html(data);
				app.registerEventForTextAreaFields(jQuery(".commentcontent"))
				contentContainer.trigger(thisInstance.widgetPostLoad,{'widgetName' : relatedModuleName});
                app.tabletrodd(); //隔行换色
			},
			function(){

			}
		);
	},

	/**
	 * Function to load only Comments Widget.
	 */
	//TODO improve this API.
	loadCommentsWidget : function() {

	},

	loadContents : function(url,data) {
		var thisInstance = this;
		var aDeferred = jQuery.Deferred();

		var detailContentsHolder = this.getContentHolder();
		var params = url;
		if(typeof data != 'undefined'){
			params = {};
			params.url = url;
			params.data = data;
		}
		AppConnector.requestPjax(params).then(
			function(responseData){
				detailContentsHolder.html(responseData);
				responseData = detailContentsHolder.html();
				//thisInstance.triggerDisplayTypeEvent();
				thisInstance.registerBlockStatusCheckOnLoad();
				//Make select box more usability
				app.changeSelectElementView(detailContentsHolder);
				//Attach date picker event to date fields
				app.registerEventForDatePickerFields(detailContentsHolder);
				app.registerEventForTextAreaFields(jQuery(".commentcontent"));
				jQuery('.commentcontent').autosize();
				thisInstance.getForm().validationEngine();
				jQuery('.pageNumbers',detailContentsHolder).tooltip();
				aDeferred.resolve(responseData);
			},
			function(){

			}
		);

		return aDeferred.promise();
	},

	getUpdatefFieldsArray : function(){
		return this.updatedFields;
	},

	/**
	 * Function to return related tab.
	 * @return : jQuery Object.
	 */
	getTabByLabel : function(tabLabel) {
		var tabs = this.getTabs();
		var targetTab = false;
		tabs.each(function(index,element){
			var tab = jQuery(element);
			var labelKey = tab.data('labelKey');
			if(labelKey == tabLabel){
				targetTab = tab;
				return false;
			}
		});
		return targetTab;
	},

	selectModuleTab : function(){
		var relatedTabContainer = this.getTabContainer();
		var moduleTab = relatedTabContainer.find('li.module-tab');
		this.deSelectAllrelatedTabs();
		this.markTabAsSelected(moduleTab);
	},

	deSelectAllrelatedTabs : function() {
		var relatedTabContainer = this.getTabContainer();
		this.getTabs().removeClass('active');
	},

	markTabAsSelected : function(tabElement){
		tabElement.addClass('active');
	},

	getSelectedTab : function() {
		var tabContainer = this.getTabContainer();
		return tabContainer.find('li.active');
	},

	getTabContainer : function(){
		return jQuery('div.related');
	},

	getTabs : function() {
		return this.getTabContainer().find('li');
	},

	getContentHolder : function() {
		if(this.detailViewContentHolder == false) {
			this.detailViewContentHolder = jQuery('div.details div.contents');
		}
		return this.detailViewContentHolder;
	},

	/**
	 * Function which will give the detail view form
	 * @return : jQuery element
	 */
	getForm : function() {
		if(this.detailViewForm == false) {
			this.detailViewForm = jQuery('#detailView');
		}
		return this.detailViewForm;
	},

	getRecordId : function(){
		return jQuery('#recordId').val();
	},

	getRelatedModuleName : function() {
		return jQuery('.relatedModuleName',this.getContentHolder()).val();
	},


	saveFieldValues : function (fieldDetailList) {
		var aDeferred = jQuery.Deferred();

		var recordId = this.getRecordId();

		var data = {};
		if(typeof fieldDetailList != 'undefined'){
			data = fieldDetailList;
		}

		data['record'] = recordId;

		data['module'] = app.getModuleName();
		data['action'] = 'SaveAjax';
		AppConnector.request(data).then(
			function(reponseData){
				aDeferred.resolve(reponseData);
			}
		);

		return aDeferred.promise();
	},


	getRelatedListCurrentPageNum : function() {
		return jQuery('input[name="currentPageNum"]',this.getContentHolder()).val();
	},

	/**
	 * function to remove comment block if its exists.
	 */
	removeCommentBlockIfExists : function() {
		var detailContentsHolder = this.getContentHolder();
		var Commentswidget = jQuery('.commentsBody',detailContentsHolder);
		jQuery('.addCommentBlock',Commentswidget).remove();
	},

	/**
	 * function to get the Comment thread for the given parent.
	 * params: Url to get the Comment thread
	 */
	getCommentThread : function(url) {
		var aDeferred = jQuery.Deferred();
		AppConnector.request(url).then(
			function(data) {
				aDeferred.resolve(data);
			},
			function(error,err){

			}
		)
		return aDeferred.promise();
	},

	/**
	 * function to save comment
	 * return json response
	 */
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
			'related_to': thisInstance.getRecordId(),
			'module' : 'ModComments',
			'modulename':app.getModuleName(),
			'moduleid':thisInstance.getRecordId(),
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



	/**
	 * function to return the UI of the comment.
	 * return html
	 */
	getCommentUI : function(commentId){
		var aDeferred = jQuery.Deferred();
		var postData = {
			'view' : 'DetailAjax',
			'module' : 'ModComments',
			'record' : commentId
		}
		AppConnector.request(postData).then(
			function(data){
				aDeferred.resolve(data);
			},
			function(error,err){

			}
		);
		return aDeferred.promise();
	},

	/**
	 * function to return cloned add comment block
	 * return jQuery Obj.
	 */
	getCommentBlock : function(){
		var detailContentsHolder = this.getContentHolder();
		var clonedCommentBlock = jQuery('.basicAddCommentBlock',detailContentsHolder).clone(true,true).removeClass('basicAddCommentBlock hide').addClass('addCommentBlock');
		clonedCommentBlock.find('.commentcontenthidden').removeClass('commentcontenthidden').addClass('commentcontent');
		return clonedCommentBlock;
	},

	/**
	 * function to return cloned edit comment block
	 * return jQuery Obj.
	 */
	getEditCommentBlock : function(){
		var detailContentsHolder = this.getContentHolder();
		var clonedCommentBlock = jQuery('.basicEditCommentBlock',detailContentsHolder).clone(true,true).removeClass('basicEditCommentBlock hide').addClass('addCommentBlock');
		clonedCommentBlock.find('.commentcontenthidden').removeClass('commentcontenthidden').addClass('commentcontent');
		return clonedCommentBlock;
	},

    /*
	 * Function to register the submit event for Send Sms
	 */
	registerSendSmsSubmitEvent : function(){
        var thisInstance = this;
		jQuery('body').on('submit','#massSave',function(e){
			var form = jQuery(e.currentTarget);
			thisInstance.SendSmsSave(form);
			e.preventDefault();
		});
	},

    /*
	 * Function to Save and sending the Sms and hide the modal window of send sms
	 */
    SendSmsSave : function(form){
		var SendSmsUrl = form.serializeFormData();
		AppConnector.request(SendSmsUrl).then(
			function(data) {
				app.hideModalWindow();
			},
			function(error,err){

			}
		);
	},

	/**
	 * Function which will register events to update the record name in the detail view when any of
	 * the name field is changed
	 */
	registerNameAjaxEditEvent : function() {
		var thisInstance = this;
		var detailContentsHolder = thisInstance.getContentHolder();
		detailContentsHolder.on(thisInstance.fieldUpdatedEvent, '.nameField', function(e, params){
			var form = thisInstance.getForm();
			var nameFields = form.data('nameFields');
			var recordLabel = '';
			for(var index in nameFields) {
				if(index != 0) {
					recordLabel += ' '
				}

				var nameFieldName = nameFields[index];
				recordLabel += form.find('[name="'+nameFieldName+'"]').val();
			}
			var recordLabelElement = detailContentsHolder.closest('.contentsDiv').find('.recordLabel');
			recordLabelElement.text(recordLabel);
		});
	},

	updateHeaderNameFields : function(){
		var thisInstance = this;
		var detailContentsHolder = thisInstance.getContentHolder();
		var form = thisInstance.getForm();
		var nameFields = form.data('nameFields');
		var recordLabelElement = detailContentsHolder.closest('.contentsDiv').find('.recordLabel');
		var title = '';
		for(var index in nameFields) {
			var nameFieldName = nameFields[index];
			var nameField = form.find('[name="'+nameFieldName+'"]');
			if(nameField.length > 0){
				var recordLabel = nameField.val();
				title += recordLabel+" ";
				recordLabelElement.find('[class="'+nameFieldName+'"]').text(recordLabel);
			}
		}
		var salutatioField = recordLabelElement.find('.salutation');
		if(salutatioField.length > 0){
			var salutatioValue = salutatioField.text();
			title = salutatioValue+title;
		}
		recordLabelElement.attr('title',title);
	},

	registerAjaxEditEvent : function(){
		var thisInstance = this;
		var detailContentsHolder =  thisInstance.getContentHolder();
		detailContentsHolder.on(thisInstance.fieldUpdatedEvent,'input,select,textarea',function(e){
			thisInstance.updateHeaderValues(jQuery(e.currentTarget));
		});
	},

	updateHeaderValues : function(currentElement){
		var thisInstance = this;
		if( currentElement.hasClass('nameField')){
			thisInstance.updateHeaderNameFields();
			return true;
		}

		var name = currentElement.attr('name');
		var updatedFields = this.getUpdatefFieldsArray();
		var detailContentsHolder =  thisInstance.getContentHolder();
		if(jQuery.inArray(name,updatedFields) != '-1'){
			var recordLabel = currentElement.val();
			var recordLabelElement = detailContentsHolder.closest('.contentsDiv').find('.'+name+'_label');
			recordLabelElement.text(recordLabel);
		}
	},

	/*
	 * Function to register the click event of email field
	 */
	registerEmailFieldClickEvent : function(){
		var detailContentsHolder = this.getContentHolder();
		detailContentsHolder.on('click','.emailField',function(e){
			e.stopPropagation();
		})
	},

	/*
	 * Function to register the click event of url field
	 */
	registerUrlFieldClickEvent : function(){
		var detailContentsHolder = this.getContentHolder();
		detailContentsHolder.on('click','.urlField',function(e){
			e.stopPropagation();
		})
	},

	/**
	 * Function to register event for related list row click
	 */
	registerRelatedRowClickEvent: function(){
		var detailContentsHolder = this.getContentHolder();
		detailContentsHolder.on('dblclick','.listViewEntries',function(e){
            var targetElement = jQuery(e.target, jQuery(e.currentTarget));
            if(targetElement.is('td:first-child') && (targetElement.children('input[type="checkbox"]').length > 0)) return;
			if(jQuery(e.target).is('input[type="checkbox"]')) return;
			var elem = jQuery(e.currentTarget);
			var recordUrl = elem.data('recordurl');
			if(typeof recordUrl != "undefined"){
                //wangbin 详细列表里的信息也是在新页面打开;
				//window.location.href = recordUrl;
                window.open(recordUrl,'_blank');
            }
		});

	},

	loadRelatedList : function(pageNumber){
		var relatedListInstance = new Vtiger_RelatedList_Js(this.getRecordId(), app.getModuleName(), this.getSelectedTab(), this.getRelatedModuleName());
		var params = {'page':pageNumber};
		relatedListInstance.loadRelatedList(params);
	},

	registerEventForRelatedListPagination : function(){
		var thisInstance = this;
		var detailContentsHolder = this.getContentHolder();
		detailContentsHolder.on('click','#relatedListNextPageButton',function(e){
			var element = jQuery(e.currentTarget);
			if(element.attr('disabled') == "disabled"){
				return;
			}
			var selectedTabElement = thisInstance.getSelectedTab();
			var relatedModuleName = thisInstance.getRelatedModuleName();
			var relatedController = new Vtiger_RelatedList_Js(thisInstance.getRecordId(), app.getModuleName(), selectedTabElement, relatedModuleName);
			relatedController.nextPageHandler();
		});
		detailContentsHolder.on('click','#relatedListPreviousPageButton',function(){
			var selectedTabElement = thisInstance.getSelectedTab();
			var relatedModuleName = thisInstance.getRelatedModuleName();
			var relatedController = new Vtiger_RelatedList_Js(thisInstance.getRecordId(), app.getModuleName(), selectedTabElement, relatedModuleName);
			relatedController.previousPageHandler();
		});
		detailContentsHolder.on('click','#relatedListPageJump',function(e){
			var selectedTabElement = thisInstance.getSelectedTab();
			var relatedModuleName = thisInstance.getRelatedModuleName();
			var relatedController = new Vtiger_RelatedList_Js(thisInstance.getRecordId(), app.getModuleName(), selectedTabElement, relatedModuleName);
			relatedController.getRelatedPageCount();
		});
		detailContentsHolder.on('click','#relatedListPageJumpDropDown > li',function(e){
			e.stopImmediatePropagation();
		}).on('keypress','#pageToJump',function(e){
			var selectedTabElement = thisInstance.getSelectedTab();
			var relatedModuleName = thisInstance.getRelatedModuleName();
			var relatedController = new Vtiger_RelatedList_Js(thisInstance.getRecordId(), app.getModuleName(), selectedTabElement, relatedModuleName);
			relatedController.pageJumpHandler(e);
		});
	},

	/**
	 * Function to register Event for Sorting
	 */
	registerEventForRelatedList : function(){
		var thisInstance = this;
		var detailContentsHolder = this.getContentHolder();
		detailContentsHolder.on('click','.relatedListHeaderValues',function(e){
			var element = jQuery(e.currentTarget);
			var selectedTabElement = thisInstance.getSelectedTab();
			var relatedModuleName = thisInstance.getRelatedModuleName();
			var relatedController = new Vtiger_RelatedList_Js(thisInstance.getRecordId(), app.getModuleName(), selectedTabElement, relatedModuleName);
			relatedController.sortHandler(element);
		});

		//右侧关联选择数据 wangbin(备注)
		detailContentsHolder.on('click', 'button.selectRelation', function(e){
			var selectedTabElement = thisInstance.getSelectedTab();
			var relatedModuleName = thisInstance.getRelatedModuleName();
			var relatedController = new Vtiger_RelatedList_Js(thisInstance.getRecordId(), app.getModuleName(), selectedTabElement, relatedModuleName);
			relatedController.showSelectRelationPopup().then(function(data){
				var emailEnabledModule = jQuery(data).find('[name="emailEnabledModules"]').val();
				if(emailEnabledModule){
					thisInstance.registerEventToEditRelatedStatus();
				}
			});
		});

		detailContentsHolder.on('click', 'a.relationDelete', function(e){
			e.stopImmediatePropagation();
			var element = jQuery(e.currentTarget);
			var instance = Vtiger_Detail_Js.getInstance();
			var key = instance.getDeleteMessageKey();
			var message = app.vtranslate(key);
			Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
				function(e) {
					var row = element.closest('tr');
					var relatedRecordid = row.data('id');
					var selectedTabElement = thisInstance.getSelectedTab();
					var relatedModuleName = thisInstance.getRelatedModuleName();
					var relatedController = new Vtiger_RelatedList_Js(thisInstance.getRecordId(), app.getModuleName(), selectedTabElement, relatedModuleName);

					relatedController.deleteRelation([relatedRecordid]).then(function(response){
						relatedController.loadRelatedList();
					});
				},
				function(error, err){
				}
			);
		});
	},

	registerBlockAnimationEvent : function(){
		var detailContentsHolder = this.getContentHolder();
		detailContentsHolder.on('click','.blockToggle',function(e){
			var currentTarget =  jQuery(e.currentTarget);
			var blockId = currentTarget.data('id');
			var closestBlock = currentTarget.closest('.detailview-table');
			var bodyContents = closestBlock.find('tbody');
			var data = currentTarget.data();
			var module = app.getModuleName();
			var hideHandler = function() {
				bodyContents.hide('slow');
				app.cacheSet(module+'.'+blockId, 0)
			}
			var showHandler = function() {
				bodyContents.show();
				app.cacheSet(module+'.'+blockId, 1)
			}
			var data = currentTarget.data();
			if(data.mode == 'show'){
				hideHandler();
				currentTarget.hide();
				closestBlock.find("[data-mode='hide']").show();
			}else{
				showHandler();
				currentTarget.hide();
				closestBlock.find("[data-mode='show']").show();
			}
		});

	},

	registerBlockStatusCheckOnLoad : function(){
		var blocks = this.getContentHolder().find('.detailview-table');
		var module = app.getModuleName();
		blocks.each(function(index,block){
			var currentBlock = jQuery(block);
			var headerAnimationElement = currentBlock.find('.blockToggle').not('.hide');
			var bodyContents = currentBlock.find('tbody')
			var blockId = headerAnimationElement.data('id');
			var cacheKey = module+'.'+blockId;
			var value = app.cacheGet(cacheKey, null);
			if(value != null){
				if(value == 1){
					headerAnimationElement.hide();
					currentBlock.find("[data-mode='show']").show();
					bodyContents.show();
				} else {
					headerAnimationElement.hide();
					currentBlock.find("[data-mode='hide']").show();
					bodyContents.hide();
				}
			}
		});
	},

	/**
	 * Function to register event for adding related record for module
	 * 右侧关联添加数据 wangbin
	 */
	registerEventForAddingRelatedRecord : function(){
		var thisInstance = this;
		var detailContentsHolder = this.getContentHolder();
		detailContentsHolder.on('click','[name="addButton"]',function(e){
			var element = jQuery(e.currentTarget);
			var selectedTabElement = thisInstance.getSelectedTab();
			var relatedModuleName = thisInstance.getRelatedModuleName();
            var quickCreateNode = jQuery('#quickCreateModules').find('[data-name="'+ relatedModuleName +'"]');
            if(quickCreateNode.length <= 0) {
                window.location.href = element.data('url');
                return;
            }

			var relatedController = new Vtiger_RelatedList_Js(thisInstance.getRecordId(), app.getModuleName(), selectedTabElement, relatedModuleName);
			relatedController.addRelatedRecord(element);
		})
	},


	/**
	 * Function to handle the ajax edit for detailview and summary view fields
	 * which will expects the currentTdElement
	 */
	ajaxEditHandling : function(currentTdElement) {
            $('.fileUploadContainer').find('form').css({width:"48px"});
            $('.fileUploadContainer').find('form').find('.btn-info').css({width:"48px",marginLeft:"-12px"});
			var thisInstance = this;
			var detailViewValue = jQuery('.value',currentTdElement);
			var editElement = jQuery('.edit',currentTdElement);
			var actionElement = jQuery('.summaryViewEdit', currentTdElement);
			if(editElement.length <= 0) {
				return;
			}

			if(editElement.is(':visible')){
				return;
			}

			detailViewValue.addClass('hide');
			editElement.removeClass('hide').show().children().filter('input[type!="hidden"]input[type!="image"],select').filter(':first').focus();
			var saveTriggred = false;
			var preventDefault = false;
			var saveHandler = function(e) {

				var element = jQuery(e.target);
				if((element.closest('td').is(currentTdElement))){
					return;
				}
				currentTdElement.removeAttr('tabindex');

				var fieldnameElement = jQuery('.fieldname', editElement);
				var previousValue = fieldnameElement.data('prevValue');
				var fieldName = fieldnameElement.val();
				var fieldElement = jQuery('[name="'+ fieldName +'"]', editElement);
				var formElement = thisInstance.getForm();
				var formData = formElement.serializeFormData();
				var ajaxEditNewValue = formData[fieldName];
				//value that need to send to the server
				var fieldValue = ajaxEditNewValue;
                var fieldInfo = Vtiger_Field_Js.getInstance(fieldElement.data('fieldinfo'));

                // Since checkbox will be sending only on and off and not 1 or 0 as currrent value
				if(fieldElement.is('input:checkbox')) {
					if(fieldElement.is(':checked')) {
						ajaxEditNewValue = '1';
					} else {
						ajaxEditNewValue = '0';
					}
					fieldElement = fieldElement.filter('[type="checkbox"]');
				}
				var errorExists = fieldElement.validationEngine('validate');
				//If validation fails

				if(errorExists&& fieldName!='file') {
					return;
				}




                //Before saving ajax edit values we need to check if the value is changed then only we have to save
                if(previousValue == ajaxEditNewValue) {
                    editElement.addClass('hide');
                    detailViewValue.removeClass('hide');
					actionElement.show();
					jQuery(document).off('click', '*', saveHandler);
                } else {
					var preFieldSaveEvent = jQuery.Event(thisInstance.fieldPreSave);
					fieldElement.trigger(preFieldSaveEvent, {'fieldValue' : fieldValue,  'recordId' : thisInstance.getRecordId()});
					if(preFieldSaveEvent.isDefaultPrevented()) {
						//Stop the save
						saveTriggred = false;
						preventDefault = true;
						return
					}
					preventDefault = false;

					jQuery(document).off('click', '*', saveHandler);

					if(!saveTriggred && !preventDefault) {
						saveTriggred = true;
					}else{
						return;
					}

                    currentTdElement.progressIndicator();
                    editElement.addClass('hide');
                    var fieldNameValueMap = {};
                    if(fieldInfo.getType() == 'multipicklist') {
                        var multiPicklistFieldName = fieldName.split('[]');
                        fieldName = multiPicklistFieldName[0];
                    }
                    fieldNameValueMap['value'] = fieldValue;
					fieldNameValueMap['field'] = fieldName;
                    if(fieldName=='file'){
                        var newvalu={};
                        var newattachmentsid=new Array();
                        $('input[name^="file["]').each(function(i,val){
                            newvalu[i]=$(val).val();
                            newattachmentsid[i]=$(val).data('id');
                        });
                        fieldNameValueMap['value']=newvalu;
                        fieldNameValueMap['attachmentsid']=newattachmentsid;
                    }
                    //return;
                    //console.log(fieldNameValueMap['field']);
                    //console.log(fieldNameValueMap['value']);
                    thisInstance.saveFieldValues(fieldNameValueMap).then(function(response) {
						var postSaveRecordDetails = response.result;
						currentTdElement.progressIndicator({'mode':'hide'});
                        detailViewValue.removeClass('hide');
						actionElement.show();
                        detailViewValue.html(postSaveRecordDetails[fieldName].display_value);
						fieldElement.trigger(thisInstance.fieldUpdatedEvent,{'old':previousValue,'new':fieldValue});
						fieldnameElement.data('prevValue', ajaxEditNewValue);
					},
                        function(error){
                            //TODO : Handle error
                            currentTdElement.progressIndicator({'mode':'hide'});
                        }
                    )
                }
			}

			jQuery(document).on('click','*', saveHandler);
	},


	triggerDisplayTypeEvent : function() {
		var widthType = app.cacheGet('widthType', 'narrowWidthType');
		if(widthType) {
			var elements = jQuery('#detailView').find('td');
			elements.addClass(widthType);
		}
	},

	/**
	 * Function updates the hidden elements which is used for creating relations
	 */
	addElementsToQuickCreateForCreatingRelation : function(container,moduleName,recordId){
		jQuery('<input type="hidden" name="sourceModule" value="'+moduleName+'" >').appendTo(container);
		jQuery('<input type="hidden" name="sourceRecord" value="'+recordId+'" >').appendTo(container);
		jQuery('<input type="hidden" name="relationOperation" value="true" >').appendTo(container);
	},

	/**
	 * Function to register event for activity widget for adding
	 * event and task from the widget
	 */
	registerEventForActivityWidget : function(){
		var thisInstance = this;

		/*
		 * Register click event for add button in Related Activities widget
		 */
		jQuery('.createActivity').on('click', function(e){
			var referenceModuleName = "Calendar";
			var quickCreateNode = jQuery('#quickCreateModules').find('[data-name="'+ referenceModuleName +'"]');
			var recordId = thisInstance.getRecordId();
			var module = app.getModuleName();
			var element = jQuery(e.currentTarget);

			if(quickCreateNode.length <= 0) {
				Vtiger_Helper_Js.showPnotify(app.vtranslate('JS_NO_CREATE_OR_NOT_QUICK_CREATE_ENABLED'))
			}
			var fieldName = thisInstance.referenceFieldNames[module];

			var customParams = {};
			customParams[fieldName] = recordId;

			var fullFormUrl = element.data('url');
			var preQuickCreateSave = function(data){
				thisInstance.addElementsToQuickCreateForCreatingRelation(data,module,recordId);

				var taskGoToFullFormButton = data.find('[class^="CalendarQuikcCreateContents"]').find('#goToFullForm');
				var eventsGoToFullFormButton = data.find('[class^="EventsQuikcCreateContents"]').find('#goToFullForm');
				var taskFullFormUrl = taskGoToFullFormButton.data('editViewUrl')+"&"+fullFormUrl;
				var eventsFullFormUrl = eventsGoToFullFormButton.data('editViewUrl')+"&"+fullFormUrl;
				taskGoToFullFormButton.data('editViewUrl',taskFullFormUrl);
				eventsGoToFullFormButton.data('editViewUrl',eventsFullFormUrl);
			}

			var callbackFunction = function() {
				var params = {};
				params['record'] = recordId;
				params['view'] = 'Detail';
				params['module'] = module;
				params['mode'] = 'getActivities';

				AppConnector.request(params).then(
					function(data) {
						var activitiesWidget = jQuery('#relatedActivities');
						activitiesWidget.html(data);
						app.changeSelectElementView(activitiesWidget);
						thisInstance.registerEventForActivityWidget();
					}
				);

                var summaryViewContainer = thisInstance.getContentHolder();
				var updatesWidget = summaryViewContainer.find("[data-name='LBL_UPDATES']");
				thisInstance.loadWidget(updatesWidget);
			}

			var QuickCreateParams = {};
			QuickCreateParams['callbackPostShown'] = preQuickCreateSave;
			QuickCreateParams['callbackFunction'] = callbackFunction;
			QuickCreateParams['data'] = customParams;
			QuickCreateParams['noCache'] = false;
			quickCreateNode.trigger('click', QuickCreateParams);
		});
	},


	/**
	 * Function to register all the events related to summary view widgets
	 */
	registerSummaryViewContainerEvents : function(summaryViewContainer) {
		var thisInstance = this;
		this.registerEventForActivityWidget();

		/**
		 * Function to handle the ajax edit for summary view fields
		 */
		summaryViewContainer.on('click', '.summaryViewEdit', function(e){
			var currentTarget = jQuery(e.currentTarget);
			currentTarget.hide();
			var currentTdElement = currentTarget.closest('td.fieldValue');
			thisInstance.ajaxEditHandling(currentTdElement);
		});

		/**
		 * Function to handle actions after ajax save in summary view
		 */
		summaryViewContainer.on(thisInstance.fieldUpdatedEvent, '.recordDetails', function(e, params){
			var updatesWidget = summaryViewContainer.find("[data-name='LBL_UPDATES']");
			thisInstance.loadWidget(updatesWidget);
		});

		/*
		 * Register the event to edit the status for for related activities
		 */
		summaryViewContainer.on('click', '.editStatus', function(e){
			var currentTarget = jQuery(e.currentTarget);
			var currentDiv = currentTarget.closest('.activityStatus');
			var editElement = currentDiv.find('.edit');
			var detailViewElement = currentDiv.find('.value');

			currentTarget.hide();
			detailViewElement.addClass('hide');
			editElement.removeClass('hide').show();

			var callbackFunction = function() {
				var fieldnameElement = jQuery('.fieldname', editElement);
				var fieldName = fieldnameElement.val();
				var fieldElement = jQuery('[name="'+ fieldName +'"]', editElement);
				var previousValue = fieldnameElement.data('prevValue');
				var ajaxEditNewValue = fieldElement.find('option:selected').text();

				if(previousValue == ajaxEditNewValue) {
                    editElement.addClass('hide');
                    detailViewElement.removeClass('hide');
					currentTarget.show();
                } else {
					var activityDiv = currentDiv.closest('.activityEntries');
					var activityId = activityDiv.find('.activityId').val();
					var moduleName = activityDiv.find('.activityModule').val();
					var activityType = activityDiv.find('.activityType').val();

					currentDiv.progressIndicator();
                    editElement.addClass('hide');
					var params = {
						action : 'SaveAjax',
						record : activityId,
						field : fieldName,
						value : ajaxEditNewValue,
						module : moduleName,
						activitytype : activityType
					};

					AppConnector.request(params).then(
						function(data) {
							currentDiv.progressIndicator({'mode':'hide'});
							detailViewElement.removeClass('hide');
							currentTarget.show();
							detailViewElement.html(ajaxEditNewValue);
							fieldnameElement.data('prevValue', ajaxEditNewValue);
						}
					);
				}
			}

			//adding clickoutside event on the currentDiv - to save the ajax edit of status values
			Vtiger_Helper_Js.addClickOutSideEvent(currentDiv, callbackFunction);
		});

		/*
		 * Register click event for add button in Related widgets
		 * to add record from widget
		 */

		jQuery('.changeDetailViewMode').on('click',function(e){
			var currentElement = jQuery(e.currentTarget);
			var detailViewTitleContainer = currentElement.closest('.toggleViewByMode');
			var viewModeElement = jQuery('input[name="viewMode"]',detailViewTitleContainer)
			var url = viewModeElement.data('fullUrl');

			var element = jQuery('<div></div>');
			element.progressIndicator({
				'position':'html',
				'blockInfo': {
					'enabled' : true,
					'elementToBlock' : summaryViewContainer
				}
			});

			thisInstance.loadContents(url).then(
				function(){
					element.progressIndicator({'mode' : 'hide'});
					thisInstance.deSelectAllrelatedTabs();
					thisInstance.loadWidgets();

					// Indicate the page content change
					app.notifyPostAjaxReady();
				}
			);
		});

		/*
		 * Register click event for add button in Related widgets
		 * to add record from widget
		 */
		jQuery('.createRecord').on('click',function(e){
			var currentElement = jQuery(e.currentTarget);
			var summaryWidgetContainer = currentElement.closest('.summaryWidgetContainer');
			var widgetHeaderContainer = summaryWidgetContainer.find('.widget_header');
			var referenceModuleName = widgetHeaderContainer.find('[name="relatedModule"]').val();
			var recordId = thisInstance.getRecordId();
			var module = app.getModuleName();
			var quickCreateNode = jQuery('#quickCreateModules').find('[data-name="'+ referenceModuleName +'"]');
			var fieldName = thisInstance.referenceFieldNames[module];

			var customParams = {};
			customParams[fieldName] = recordId;

			if(quickCreateNode.length <= 0) {
				Vtiger_Helper_Js.showPnotify(app.vtranslate('JS_NO_CREATE_OR_NOT_QUICK_CREATE_ENABLED'))
			}

			var postQuickCreateSave = function(data) {
				thisInstance.postSummaryWidgetAddRecord(data,currentElement);
			}

			var goToFullFormcallback = function(data){
				thisInstance.addElementsToQuickCreateForCreatingRelation(data,module,recordId);
			}

			var QuickCreateParams = {};
			QuickCreateParams['callbackFunction'] = postQuickCreateSave;
			QuickCreateParams['goToFullFormcallback'] = goToFullFormcallback;
			QuickCreateParams['data'] = customParams;
			QuickCreateParams['noCache'] = false;
			quickCreateNode.trigger('click', QuickCreateParams);
		});
	},

	addRelationBetweenRecords : function(relatedModule, relatedModuleRecordId){
		var aDeferred = jQuery.Deferred();
		var thisInstance = this;
		var selectedTabElement = thisInstance.getSelectedTab();
		var relatedController = new Vtiger_RelatedList_Js(thisInstance.getRecordId(), app.getModuleName(), selectedTabElement, relatedModule);
		relatedController.addRelations(relatedModuleRecordId).then(
			function(data){
				var summaryViewContainer = thisInstance.getContentHolder();
				var updatesWidget = summaryViewContainer.find("[data-name='LBL_UPDATES']");
				thisInstance.loadWidget(updatesWidget);
				aDeferred.resolve(data);
			},

			function(textStatus, errorThrown){
				aDeferred.reject(textStatus, errorThrown);
			}
		)
		return aDeferred.promise();
	},

	/**
	 * Function to handle Post actions after adding record from
	 * summary view widget
	 */
	postSummaryWidgetAddRecord : function(data,currentElement){
		var summaryWidgetContainer = currentElement.closest('.summaryWidgetContainer');
		var widgetHeaderContainer = summaryWidgetContainer.find('.widget_header');
		var widgetDataContainer = summaryWidgetContainer.find('.widget_contents');
		var referenceModuleName = widgetHeaderContainer.find('[name="relatedModule"]').val();
		var recordId = this.getRecordId();
		var module = app.getModuleName();
		var idList = new Array();
		idList.push(data.result._recordId);
		widgetDataContainer.progressIndicator({});
		this.addRelationBetweenRecords(referenceModuleName,idList).then(
			function(data){
				var params = {};
				params['record'] = recordId;
				params['view'] = 'Detail';
				params['module'] = module;
				params['page'] = widgetDataContainer.find('[name="page"]').val();
				params['limit'] = widgetDataContainer.find('[name="pageLimit"]').val();
				params['relatedModule'] = referenceModuleName;
				params['mode'] = 'showRelatedRecords';

				AppConnector.request(params).then(
					function(data) {
						var documentsWidget = jQuery('#relatedDocuments');
						widgetDataContainer.progressIndicator({'mode' : 'hide'});
						widgetDataContainer.html(data);
						app.changeSelectElementView(documentsWidget);
					}
				);
			}
		)
	},

	/**
	 * Function to register event for emails related record click
	 */
	registerEventForEmailsRelatedRecord : function(){
		var detailContentsHolder = this.getContentHolder();
		var emailsRelatedContainer = detailContentsHolder.find('[name="emailsRelatedRecord"]');
		var parentId = this.getRecordId();
		var popupInstance = Vtiger_Popup_Js.getInstance();
		detailContentsHolder.on('click','[name="emailsRelatedRecord"]',function(e){
			var element = jQuery(e.currentTarget);
			var recordId = element.data('id');
			var params = {};
			params['module'] = "Emails";
			params['view'] = "ComposeEmail";
			params['mode'] = "emailPreview";
			params['record'] = recordId;
			params['parentId'] = parentId;
			params['relatedLoad'] = true;
			popupInstance.show(params);
		})
		detailContentsHolder.on('click','[name="emailsEditView"]',function(e){
			e.stopPropagation();
			var module = "Emails";
			Vtiger_Helper_Js.checkServerConfig(module).then(function(data){
				if(data == true){
					var element = jQuery(e.currentTarget);
					var closestROw = element.closest('tr');
					var recordId = closestROw.data('id');
					var parentRecord = new Array();
					parentRecord.push(parentId);
					var params = {};
					params['module'] = "Emails";
					params['view'] = "ComposeEmail";
					params['mode'] = "emailEdit";
					params['record'] = recordId;
					params['selected_ids'] = parentRecord;
					params['parentId'] = parentId;
					params['relatedLoad'] = true;
					popupInstance.show(params);
				} else {
					Vtiger_Helper_Js.showPnotify(app.vtranslate('JS_EMAIL_SERVER_CONFIGURATION'));
				}
			})
		})
	},

	/**
	 * Function to register event for adding email from related list
	 */
	registerEventForAddingEmailFromRelatedList : function() {
		var detailContentsHolder = this.getContentHolder();
		var parentId = this.getRecordId();
		detailContentsHolder.on('click','[name="composeEmail"]',function(e){
			e.stopPropagation();
			var element = jQuery(e.currentTarget);
			var parentRecord = new Array();
			var params = {};
			parentRecord.push(parentId);
			params['module'] = app.getModuleName();
			params['view'] = "MassActionAjax";
			params['selected_ids'] = parentRecord;
			params['mode'] = "showComposeEmailForm";
			params['step'] = "step1";
			params['relatedLoad'] = true;
			Vtiger_Index_Js.showComposeEmailPopup(params);
		})
	},
	registerEnterClickEventForTagRecord : function() {
		jQuery('#tagRecordText').keypress(function(e) {
			if(e.which == 13) {
				jQuery('#tagRecord').trigger('click');
			}
		});
	},
	checkTagExists : function(tagText) {
		var tagsArray = tagText.split(' ');
		for(var i=0;i<tagsArray.length;i++){
			var tagElement = jQuery('#tagsList').find("[data-tagname='"+tagsArray[i]+"']");
			if(tagElement.length > 0){
				tagsArray.splice(i,1);
				i--;
			}
		}
		var tagName = tagsArray.join(' ');
		if(tagName == ''){
			return true;
		} else {
			return tagName;
		}

	},

	addTagsToList : function(data,tagText) {
		var tagsArray = tagText.split(' ');

		for(var i=0;i<tagsArray.length;i++){
			var id = data.result[1][tagsArray[i]];
			jQuery('#tagsList').prepend('<div class="tag row-fluid span11 marginLeftZero" data-tagname="'+tagsArray[i]+'" data-tagid="'+id+'"><span class="tagName textOverflowEllipsis span11 cursorPointer"><a>'+tagsArray[i]+'</a></span><span class="pull-right cursorPointer deleteTag">x</span></div>');
		}

	},

	checkTagMaxLengthExceeds : function(tagText) {
		var tagsArray = tagText.split(' ');
		var maxTagLength = jQuery('#maxTagLength').val();

		for(var i=0;i<tagsArray.length;i++){
			if(tagsArray[i].length > parseInt(maxTagLength)) {
				return true;
			}
		}
		return false;
	},

	registerClickEventForAddingTagRecord : function() {
		var thisInstance = this;
		jQuery('#tagRecord').on('click',function(){
			var textElement = jQuery('#tagRecordText');
			var tagText = textElement.val();
			if(tagText == ''){
				textElement.validationEngine('showPrompt', app.vtranslate('JS_PLEASE_ENTER_A_TAG') , 'error','bottomLeft',true);
				return;
			}
			var maxLengthExceeds = thisInstance.checkTagMaxLengthExceeds(tagText);
			if(maxLengthExceeds == true){
				var maxTagLenth = jQuery('#maxTagLength').val();
				textElement.validationEngine('showPrompt', app.vtranslate('JS_MAX_TAG_LENGTH_EXCEEDS')+' '+maxTagLenth, 'error','bottomLeft',true);
				return;
			}
			var tagExistResult = thisInstance.checkTagExists(tagText);
			if(tagExistResult == true){
				textElement.validationEngine('showPrompt', app.vtranslate('JS_TAG_NAME_ALREADY_EXIST') , 'error','bottomLeft',true);
				return;
			} else {
				tagText = tagExistResult;
			}
			var params = {
				module : app.getModuleName(),
				action : 'TagCloud',
				mode : 'save',
				tagname : tagText,
				record : thisInstance.getRecordId()
			}
			AppConnector.request(params).then(
					function(data) {
						thisInstance.addTagsToList(data,tagText);
						textElement.val('');
					}
				);
		});
	},
	registerRemovePromptEventForTagCloud : function(data) {
		jQuery('#tagRecordText').on('focus',function(e){
			var errorPrompt = jQuery('.formError',data);
			if(errorPrompt.length > 0) {
				errorPrompt.remove();
			}
		});
	},

	registerDeleteEventForTag : function(data) {
		var thisInstance = this;
		jQuery(data).on('click','.deleteTag',function(e){
			var tag = jQuery(e.currentTarget).closest('.tag');
			var tagId = tag.data('tagid');
			tag.fadeOut('slow', function() {
				tag.remove();
			});
			var params = {
				module : app.getModuleName(),
				action : 'TagCloud',
				mode : 'delete',
				tag_id : tagId,
				record : thisInstance.getRecordId()
			}
			AppConnector.request(params).then(
				function(data) {
				});
		});
	},
	registerTagClickEvent : function(data){
		var thisInstance = this;
		jQuery(data).on('click','.tagName',function(e) {
			var tagElement = jQuery(e.currentTarget);
			var tagId = tagElement.closest('.tag').data('tagid');
			var params = {
				'module' : app.getModuleName(),
				'view' : 'TagCloudSearchAjax',
				'tag_id' : tagId,
				'tag_name' : tagElement.find('a').text()
			}
			AppConnector.request(params).then(
				function(data) {
					var params = {
						'data' : data,
						'css'  : {'min-width' : '40%'}
					}
					app.showModalWindow(params);
					thisInstance.registerChangeEventForModulesList();
				}
			)
		});
	},

	registerChangeEventForModulesList : function() {
		jQuery('#tagSearchModulesList').on('change',function(e) {
			var modulesSelectElement = jQuery(e.currentTarget);
			if(modulesSelectElement.val() == 'all'){
				jQuery('[name="tagSearchModuleResults"]').removeClass('hide');
			} else{
				jQuery('[name="tagSearchModuleResults"]').removeClass('hide');
				var selectedOptionValue = modulesSelectElement.val();
				jQuery('[name="tagSearchModuleResults"]').filter(':not(#'+selectedOptionValue+')').addClass('hide');
			}
		});
	},

	registerPostTagCloudWidgetLoad : function() {
		var thisInstance = this;
		app.getContentsContainer().on('Vtiger.Widget.Load.LBL_TAG_CLOUD',function(e,data){
			thisInstance.registerClickEventForAddingTagRecord();
			thisInstance.registerEnterClickEventForTagRecord();
			thisInstance.registerDeleteEventForTag(data);
			thisInstance.registerRemovePromptEventForTagCloud(data);
			thisInstance.registerTagClickEvent(data);
		});
	},

	registerEventForRelatedTabClick : function(){
		var thisInstance = this;
		var detailContentsHolder = thisInstance.getContentHolder();
		var detailContainer = detailContentsHolder.closest('div.detailViewInfo');
		app.registerEventForDatePickerFields(detailContentsHolder);
		//Attach time picker event to time fields
		app.registerEventForTimeFields(detailContentsHolder);

		jQuery('.related', detailContainer).on('click', 'li', function(e, urlAttributes){
			var tabElement = jQuery(e.currentTarget);
			var element = jQuery('<div></div>');
			element.progressIndicator({
				'position':'html',
				'blockInfo' : {
					'enabled' : true,
					'elementToBlock' : detailContainer
				}
			});
			var url = tabElement.data('url');
			if(tabElement.data('label-key')=='ModComments'){
				url=url+'&page='+$('.nextpage').val();
			}
			if(typeof urlAttributes != 'undefined'){
				var callBack = urlAttributes.callback;
				delete urlAttributes.callback;
			}
			thisInstance.loadContents(url,urlAttributes).then(
				function(data){
					thisInstance.deSelectAllrelatedTabs();
					thisInstance.markTabAsSelected(tabElement);
					Vtiger_Helper_Js.showHorizontalTopScrollBar();
					element.progressIndicator({'mode': 'hide'});
					if(typeof callBack == 'function'){
						callBack(data);
					}
					//Summary tab is clicked
					if(tabElement.data('linkKey') == thisInstance.detailViewSummaryTabLabel) {
						thisInstance.loadWidgets();
						thisInstance.registerSummaryViewContainerEvents(detailContentsHolder);
					}

					// Let listeners know about page state change.
					app.notifyPostAjaxReady();
				},
				function (){
					//TODO : handle error
					element.progressIndicator({'mode': 'hide'});
				}
			);
		});
	},

	/**
	 * Function to get child comments
	 */
	getChildComments : function(commentId){
		var aDeferred = jQuery.Deferred();
		var url= 'module='+app.getModuleName()+'&view=Detail&record='+this.getRecordId()+'&mode=showChildComments&commentid='+commentId;
		var dataObj = this.getCommentThread(url);
		dataObj.then(function(data){
			aDeferred.resolve(data);
		});
		return aDeferred.promise();
	},

	/**
	 * Function to show total records count in listview on hover
	 * of pageNumber text
	 */
	registerEventForTotalRecordsCount : function(){
		var thisInstance = this;
		var detailContentsHolder = this.getContentHolder();
		detailContentsHolder.on('hover','.pageNumbers',function(e){
			var element = jQuery(e.currentTarget);
			var totalNumberOfRecords;
			var totalRecordsElement = jQuery('#totalCount');
			totalNumberOfRecords = totalRecordsElement.val();
			if(totalNumberOfRecords == '') {
				var selectedTabElement = thisInstance.getSelectedTab();
				var relatedModuleName = thisInstance.getRelatedModuleName();
				var relatedController = new Vtiger_RelatedList_Js(thisInstance.getRecordId(), app.getModuleName(), selectedTabElement, relatedModuleName);
				relatedController.totalRecordsCount().then(function(numberOfRecords){
					totalRecordsElement.val(numberOfRecords);
				});
			}
			if(totalNumberOfRecords != ''){
				var titleWithRecords = app.vtranslate("JS_TOTAL_RECORDS")+" "+totalNumberOfRecords;
				element.data('tooltip').options.title = titleWithRecords;
			} else {
				element.data('tooltip').options.title = "";
			}
		})
	},
	passExecutionNode:function(){
		$('body').on('click','#passContractExecutionNode',function () {
            var params={};
            params['record'] = $('#recordid').val();
            params['stagerecordid'] = $('#stagerecordid').val();
            params['action'] = 'SaveAjax';
            params['module'] = 'SalesorderWorkflowStages';
            params['mode'] = 'updateSalseorderWorkflowStages';
            params['src_module'] = app.getModuleName();
            params['checkname'] = $('#backstagerecordname').val();
            params['customer']=$("#customer").val()==undefined?0:$("#customer").val();
            params['customername']=$("#customer").find("option:selected").text()==undefined?'':$("#customer").find("option:selected").text();
            params['fileid'] = $("#fileid").val();
            params['filename'] = $("#fileid").data('name');
            //ie9下post请求是失败的，如果get可以的请修改
            var d={};
            d.data=params;
            d.type = 'GET';
            var progressIndicatorElement = jQuery.progressIndicator({
                'message' : '亲,正在拼命处理,请耐心等待哟',
                'position' : 'html',
                'blockInfo' : {'enabled' : true}
            });

            AppConnector.request(d).then(
                function(data){
                    if(data.success==true){
                        //刷新当前的挂件，在这里本来可以使用父类的方法，但是不生效，只能重新写了
                        var widgetContainer = $(".widgetContainer_workflows");
                        //
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
                                Vtiger_Helper_Js.showMessage({type:'success',text:'审核成功'});
                                window.location.reload();
                            },
                            function(){}
                        );
                    }else{
                        Vtiger_Helper_Js.showMessage({type:'error',text:'审核失败,原因'+data.error.message});
                    }
                    progressIndicatorElement.progressIndicator({
                        'mode' : 'hide'
                    });
                },function(){}
            );
        })
	},

	bindStagesubmit:function(){
		$('.details').on('click','.stagesubmit',function(){
            var name=$('#stagerecordname').val();
			var replaceSTR="合同执行";
			var patt1 = new RegExp(replaceSTR);

			name=patt1.test(name)?'合同“'+name.replace(replaceSTR,'')+'”':name;
			str = '<div id="myModal" class="modal" style="">\n' +
                '\t<div class="modal-dialog">\n' +
                '\t\t<div class="modal-content">\n' +
                '\t\t\t<div class="modal-header">\n' +
                '\t\t\t\t<button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>\n' +
                '\t\t\t\t<h4 class="modal-title">通过合同执行节点</h4>\n' +
                '\t\t\t\t<div style="margin-top: 20px;" id="supervisor">\n';
            str += '\n' +
                '\t\t\t\t</div>\n' +
                '\t\t\t</div>\n' +
                '\t\t\t<div class="modal-body" style="max-height:500px;">\n' +
                '\n' +
                '\t\t\t\t<div class="confirm tc">\n';
			str += 				'     <form>\n' +
                '                <input type="hidden" id="test" value="">\n' +
                '\t            <input type="hidden" value="newExecutionNode" name="mode">\n' +
                '\t            <input type="hidden" value="ChangeAjax" name="action">\n' +
                '\t            <input type="hidden" value="ContractExecution" name="module">\n';
            str += '<div class="" style="height:150px;"><div class=""><span style="color: white">*</span></div><div class="" style="font-size: 16px;font-weight: bolder;margin-bottom: 10px;">执行凭证</div><div class=""><div class="fileUploadContainer" xmlns="http://www.w3.org/1999/html">\n' +
                '                                <div class="upload">\n' +
                '                                    <div style="display:inline-block;width:1px;height:80px;overflow: hidden;vertical-align: middle;"  title="支持pdf/png/jpg文件，不超过3M"><div style="margin-top:-2px;"></div></div>\n' +
                '                                    <input type="button" id="uploadButton" value="上传"  title="支持pdf/png/jpg文件，不超过3M" />\n' +
                '<span style="font-size:8px;color:gray">支持pdf/png/jpg文件不超过3M</span>'+
                '                                    <div style="display:inline-block" id="fileall">\n' +
                '                                            <input class="ke-input-text filedelete" type="hidden" name="file" id="file" value="" readonly="readonly" />\n' +
                '                                            <input class="filedelete" type="hidden" name="attachmentsid" value="">\n' +
                '                                    </div>\n' +
                '                                </div>\n' +
                '                            </div></div></div>';
			str +='<div style="font-size:20px;">执行通过后将不可取消，确定'+name+'已执行？</div>';
			str += '</form>';
            str +=                        '\n' +
                '\t\t\t\t</div>\n' +
                '\t\t\t</div>\n' +
                '\t\t\t<div class="modal-footer">\n' +
                '\t\t\t\t<div class=" pull-right cancelLinkContainer"><a class="cancelLink" type="reset" data-dismiss="modal">取消</a></div>\n' +
                '\t\t\t\t<button class="btn btn-success" id="passContractExecutionNode" type="submit">确定</button>\n' +
                '\t\t\t</div>\n' +
                '\t\t</div>\n' +
                '\t</div>\n' +
                '</div>';
            app.showModalWindow(str);
            $("#uploadButton").trigger('click');
            $('.modal-backdrop').css({
                "opacity":"0.6",
                "z-index":"0"
            });

		});
	},

	/**
	 * wangbin 修改工单备注 2015年03月09日 星期一
	 */
	bindeditremark:function(){

		$('.details').on("click",'#editremark',function(){
			 salesorderhistoryid = $(this).siblings().find(':first').val();
			 oldval= $(this).siblings().eq(2).text();
			 		$('#editremarkval').val(oldval);
		});
		$('.details').on("click",'#realeditremark',function(){
				//alert(12345);
				//alert(salesorderhistoryid);
				var editremarkval = $('#editremarkval').val();
				var params={};
				params['action'] = 'SaveAjax';
				params['module'] = 'SalesorderWorkflowStages';
				params['mode'] = 'editremark';
				params['salesorderhistoryid'] = salesorderhistoryid;
				params['editremarkval'] = editremarkval;
				var d={};
				d.data=params;
				d.type = 'GET';
				AppConnector.request(d).then(
						function(data){
                            progressIndicatorElement.progressIndicator({
                                'mode' : 'hide'
                            });
							if(data.success==true){
								//刷新当前的挂件，在这里本来可以使用父类的方法，但是不生效，只能重新写了
								var widgetContainer = $(".widgetContainer_workflows");
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
										Vtiger_Helper_Js.showMessage({type:'success',text:'备注修改成功'});
									},
									function(){}
								);
							}else{
								Vtiger_Helper_Js.showMessage({type:'error',text:'备注修改失败,原因'+data.error.message});
							}
						},function(){}
				);
			})

	},
	/**
	 * wangbin 添加工单备注 2015-03-04 16:49:57
	 */
	bindremarkbutton:function(){
	},
	/**
	 * wangbin 打回工单2014-12-22 14:42:33
	 */
	bindrejectall:function(){
//		$('.widgetContainer_workflows').on("click",'#rejectbutton',function(){
//			$('#test').toggle("fast");
//		});
			$('.details').on("click",'#realstagereset',function(){
                //steel加入打回为空检测//////
                var rejectreason=$('#rejectreason');
                if(rejectreason.val()==''){
                    Vtiger_Helper_Js.showMessage({type:'error',text:'打回原因必须填写'});
                    rejectreason.focus();
                    return ;
                }
                ///////////////////////////
                var name=$('#stagerecordname').val();
                var msg={
                    'message':"确定要将工单阶段"+name+"打回？",
                };

				Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                    var params={};
                    params['record'] = $('#recordid').val();                  //工单id
                    params['isrejectid'] = $('#stagerecordid').val();
                    params['isbackname'] = $('#stagerecordname').val();
                    params['reject']=$('#rejectreason').val();
                    params['action'] = 'SaveAjax';
                    params['module'] = 'SalesorderWorkflowStages';
                    params['mode'] = 'backall';
                    params['src_module'] = app.getModuleName();
                    params['actionnode'] = 0;
                    backfun(params);
			},function(error, err) {});
			});
        $('.details').on('click','.resetaction',function(){
            var msg={
                'message':"确定要激活节点？",
            };
            var isrejectid =  $(this).data('id');
            var isbackname = $(this).data('name');
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                var params={};
                params['record'] = $('#recordid').val();                  //工单id
                params['isrejectid'] = isrejectid;
                params['isbackname'] = isbackname;
                params['reject'] = '节点被重新激活';
                params['action'] = 'SaveAjax';
                params['module'] = 'SalesorderWorkflowStages';
                params['mode'] = 'backall';
                params['src_module'] = app.getModuleName();
                params['actionnode'] = 1;
                backfun(params);
            },function(error, err) {});
        });
        $('.details').on('click','.modulestatus',function(){
            var msg={
                'message':"是否修改当前模块状态",
            };
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){

            },function(error, err) {});
        });
        function backfun(params){
            var d={};
            d.data=params;
            d.type = 'GET';
            var progressIndicatorElement = jQuery.progressIndicator({
                'message' : '亲,正在拼命处理,请耐心等待哟',
                'position' : 'html',
                'blockInfo' : {'enabled' : true}
            });
            AppConnector.request(d).then(
                function(data){
                    progressIndicatorElement.progressIndicator({
                        'mode' : 'hide'
                    });
                    if(data.success==true){
                        var widgetContainer = $(".widgetContainer_workflows");

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
                                Vtiger_Helper_Js.showMessage({type:'success',text:'操作成功'});
                                //隐藏回款明细 gaocl add 2018/05/16
                                if(app.getModuleName() == 'SalesOrder'){
									$(".salesorderrayment_title_tab").hide();
                                    $(".salesorderrayment_tab").hide();
								}
                            },
                            function(){}
                        );
                    }else{
                        Vtiger_Helper_Js.showMessage({type:'error',text:'操作失败,原因'+data.error.message});
                    }
                },function(){}
            );
        }
	},
	bindStagereset:function(){
		$('.widgetContainer_workflows').on("click",'#rejectbutton',function(){
			$('#test').toggle("fast");
		});
		backname="";
		$('#chooseback').live('change',function(){
			 backtoid =$(this).val();
			 backname = $("#chooseback").find("option:selected").attr("stagename");
		});
			$('.details').on("click",'#realstagereset',function(){
				if(backname==""){
					alert("请选择打回节点");
					return;
				}else{
					var name=$('#stagerecordname').val();
					var msg={
						'message':"确定要将工单阶段"+name+"至工单阶段"+backname,
					};
				}

				Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                    rejectfun(backtoid,backname);
			},function(error, err) {});
			});
        // 打回公共方法
        function rejectfun(backtoid,backname){
            var params={};
            params['record'] = $('#recordid').val();                  //工单id
            params['stagerecordid'] = backtoid; //节点id
            params['isrejectid'] = $('#backstagerecordeid').val();
            params['rejectname'] = backname;
            params['isbackname'] = $('#backstagerecordname').val();
            params['reject']=$('#rejectreason').val();
            params['action'] = 'SaveAjax';
            params['module'] = 'SalesorderWorkflowStages';
            params['mode'] = 'backSalesOrderWorkflowsstages';
            params['src_module'] = app.getModuleName();
            var d={};
            d.data=params;
            d.type = 'GET';
            AppConnector.request(d).then(
                function(data){
                    if(data.success==true){
                        //刷新当前的挂件，在这里本来可以使用父类的方法，但是不生效，只能重新写了
                        var widgetContainer = $(".widgetContainer_workflows");
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
                                Vtiger_Helper_Js.showMessage({type:'success',text:'打回成功'});
                            },
                            function(){}
                        );
                    }else{
                        Vtiger_Helper_Js.showMessage({type:'error',text:'打回失败,原因'+data.error.message});
                    }
                },function(){}
            );
        }
	},

    bingschedule:function(){

		$('.details').on('change','.schedule',function(){
			//console.log($(this).val());
			var oldschedule=$('.bar').data("schedule");
			if($(this).val()<=oldschedule){
				Vtiger_Helper_Js.showMessage({type:'error',text:'选择进度'+$(this).val()+'不能小于当前的进度'+oldschedule});
				return;
			}

			var params={};
			params['record'] = $('#recordid').val();                  //工单id
			params['stagerecordid'] = $('#backstagerecordeid').val(); //节点id
			params['rejectname'] = $('#backstagerecordname').val();
			params['reject']=$('#rejectreason').val();
			params['action'] = 'SaveAjax';
			params['module'] = 'SalesorderWorkflowStages';
			params['mode'] = 'updateWorkflowSchedule';
			params['src_module'] = app.getModuleName();
			params['schedule'] = $('.schedule').val();

			AppConnector.request(params).then(
					function(data){
						if(data.success==true){
							//刷新当前的挂件，在这里本来可以使用父类的方法，但是不生效，只能重新写了
							var widgetContainer = $(".widgetContainer_workflows");
							//
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
									Vtiger_Helper_Js.showMessage({type:'success',text:'进度更新成功'});
								},
								function(){}
							);
						}else{
							Vtiger_Helper_Js.showMessage({type:'error',text:'进度更新失败,原因'+data.error.message});
						}
					},function(){}
			);
		});
	},
    getuploadFile:function(){

        if($('#file').length>0){
            var module=$('#module').val();

            KindEditor.ready(function(K) {
                var uploadbutton = K.uploadbutton({
                    button : K('#uploadButton')[0],
                    fieldName : 'File',
                    extraParams :{
                        __vtrftk:csrfMagicToken,//$('input[name="__vtrftk"]').val(),
                        record:$('#recordId').val()
                    },
                    url : 'index.php?module='+module+'&action=FileUpload&record='+$('#recordId').val(),
                    afterUpload : function(data) {
                        /*if (data.success ==true) {
                         $('input[name="attachmentsid"]').val(data.result['id']);
                         K('#file').val(data.result['name']);
                         } else {
                         }*/
                        if (data.success ==true) {
                            $('.filedelete').remove();
                            var str='<span class="label file'+data.result['id']+'" style="margin-left:5px;">'+data.result['name']+'&nbsp;<b class="deletefile" data-id="'+data.result['id']+'" data-class="file'+data.result['id']+'" title="删除文件" style="display:inline-block;width:12px;height:12px;line-height:12px;text-align:center">x</b>&nbsp;</span><input class="ke-input-text file'+data.result['id']+'" type="hidden" name="file['+data.result['id']+']" data-id="'+data.result['id']+'" id="file" value="'+data.result['name']+'" readonly="readonly" /><input class="file'+data.result['id']+'" type="hidden" name="attachmentsid['+data.result['id']+']" value="'+data.result['id']+'">';
                            $("#fileall").append(str);
                            //K('#file').val(data.result['name']);
                        } else {
                        }
                    },
                    afterError : function(str) {
                        //alert('自定义错误信息: ' + str);
                    }
                });
                uploadbutton.fileBox.change(function(e) {
                    uploadbutton.submit();
                });
            });
        }
    },
    aleretCommentCallback:function(){

	},

		registerEvents : function(){
		var thisInstance = this;
		this.bindStagesubmit();
		this.passExecutionNode();
		this.pre_click_upload();
            var ckEditorInstance = new Vtiger_CkEditor_Js();
            ckEditorInstance.loadCkEditor("mailcontex");
		//this.bindStagereset();打回指定节点
		this.bindrejectall();
		this.bindremarkbutton();
		this.bindeditremark();//修改工单备注
		this.bingschedule();
		//thisInstance.triggerDisplayTypeEvent();
		thisInstance.registerSendSmsSubmitEvent();
		thisInstance.registerAjaxEditEvent();
		this.registerRelatedRowClickEvent();
		this.registerBlockAnimationEvent();
		this.registerBlockStatusCheckOnLoad();
		this.registerEmailFieldClickEvent();
		this.registerEventForRelatedList();
		this.registerEventForRelatedListPagination();
		this.registerEventForAddingRelatedRecord();
		this.registerEventForEmailsRelatedRecord();
		this.registerEventForAddingEmailFromRelatedList();
		this.registerPostTagCloudWidgetLoad();
		this.registerEventForRelatedTabClick();
		Vtiger_Helper_Js.showHorizontalTopScrollBar();
		this.registerUrlFieldClickEvent();
		this.getuploadFile();
        this.deleteuploadFile();
        this.addExecutionNode();
        this.submitExecutionStage();
		var detailViewContainer = jQuery('div.detailViewContainer');
		if(detailViewContainer.length <= 0) {
			// Not detail view page
			return;
		}

		var detailContentsHolder = thisInstance.getContentHolder();
		app.registerEventForDatePickerFields(detailContentsHolder);
		//Attach time picker event to time fields
		app.registerEventForTimeFields(detailContentsHolder);

		//register all the events for summary view container
		this.registerSummaryViewContainerEvents(detailContentsHolder);

		detailContentsHolder.on('click', '#detailViewNextRecordButton', function(e){
			var selectedTabElement = thisInstance.getSelectedTab();
			var url = selectedTabElement.data('url');
			var currentPageNum = thisInstance.getRelatedListCurrentPageNum();
			var requestedPage = parseInt(currentPageNum)+1;
			var nextPageUrl = url+'&page='+requestedPage;
			thisInstance.loadContents(nextPageUrl);
		});

		detailContentsHolder.on('click', '#detailViewPreviousRecordButton', function(e){
			var selectedTabElement = thisInstance.getSelectedTab();
			var url = selectedTabElement.data('url');
			var currentPageNum = thisInstance.getRelatedListCurrentPageNum();
			var requestedPage = parseInt(currentPageNum)-1;
			var params = {};
			var nextPageUrl = url+'&page='+requestedPage;
			thisInstance.loadContents(nextPageUrl);
		});

		detailContentsHolder.on('dblclick','table.detailview-table td.fieldValue', function(e) {
			var currentTdElement = jQuery(e.currentTarget);
			thisInstance.ajaxEditHandling(currentTdElement);
		});


		detailContentsHolder.on('click', '.relatedPopup', function(e){
			console.log("what");
			var editViewObj = new Vtiger_Edit_Js();
			editViewObj.openPopUp(e);
			return false;
		});

		detailContentsHolder.on('click','.addCommentBtn', function(e){
			thisInstance.removeCommentBlockIfExists();
			var addCommentBlock = thisInstance.getCommentBlock();
			addCommentBlock.appendTo('.commentBlock');
		});

		detailContentsHolder.on('click','.closeCommentBlock', function(e){
			var currentTarget = jQuery(e.currentTarget);
			var commentInfoBlock = currentTarget.closest('.singleComment');
			commentInfoBlock.find('.commentActionsContainer').show();
			commentInfoBlock.find('.commentInfoContent').show();
			thisInstance.removeCommentBlockIfExists();
		});

		//跟进提醒 2014-12-22/gaocl start
		detailContentsHolder.on('click','.alertComment', function(e){
			var currentTarget = jQuery(e.currentTarget);
			//var commentInfoBlock = currentTarget.closest('.singleComment');
			var url=currentTarget.data('url');

			AppConnector.request(url).then(
					function(data) {
						var css = {'text-align':'left','width':'45%'};
				        var callback = function(data){
//				        	var widgetList = jQuery('[class="widgetContainer_comments"]');
//							var vdjs=new Vtiger_Detail_Js;
//							vdjs.loadWidget(widgetList);
				        }
				        var modalWindowParams = {
							'data' : data,
							'css' : css,
							'cb' : callback
						};
				        app.showModalWindow(modalWindowParams);
				        thisInstance.aleretCommentCallback();
					}
				)
		});
		//跟进提醒 2014-12-22/gaocl end

		detailContentsHolder.on('click','.replyComment', function(e){

			var currentTarget = jQuery(e.currentTarget);
			//var commentInfoBlock = currentTarget.closest('.singleComment');
			var url=currentTarget.data('url');

			AppConnector.request(url).then(
					function(data) {
						var css = {'text-align':'left','width':'40%'};
				        var callback = function(data){
//				        	var widgetList = jQuery('[class="widgetContainer_comments"]');
//							var vdjs=new Vtiger_Detail_Js;
//							vdjs.loadWidget(widgetList);
				        }
				        var modalWindowParams = {
							'data' : data,
							'css' : css,
							'cb' : callback
						};
				        app.showModalWindow(modalWindowParams);
					}
				)
//			thisInstance.removeCommentBlockIfExists();
//			var currentTarget = jQuery(e.currentTarget);
//			var commentInfoBlock = currentTarget.closest('.singleComment');
//			var addCommentBlock = thisInstance.getCommentBlock();
//			//commentInfoBlock.find('.commentActionsContainer').hide();
//			addCommentBlock.appendTo(commentInfoBlock).show();
//			app.registerEventForTextAreaFields(jQuery('.commentcontent',commentInfoBlock));
		});

		detailContentsHolder.on('click','.editComment', function(e){
			thisInstance.removeCommentBlockIfExists();
			var currentTarget = jQuery(e.currentTarget);
			var commentInfoBlock = currentTarget.closest('.singleComment');
			var commentInfoContent = commentInfoBlock.find('.commentInfoContent');
			var commentReason = commentInfoBlock.find('[name="editReason"]');
			var editCommentBlock = thisInstance.getEditCommentBlock();
			editCommentBlock.find('.commentcontent').text(commentInfoContent.text());
			editCommentBlock.find('[name="reasonToEdit"]').val(commentReason.text());
			commentInfoContent.hide();
			commentInfoBlock.find('.commentActionsContainer').hide();
			editCommentBlock.appendTo(commentInfoBlock).show();
			app.registerEventForTextAreaFields(jQuery('.commentcontent',commentInfoBlock));
		});

		detailContentsHolder.on('click','.viewThread', function(e){
			var currentTarget = jQuery(e.currentTarget);
			var currentTargetParent = currentTarget.parent();
			var commentActionsBlock = currentTarget.closest('.commentActions');
			var currentCommentBlock = currentTarget.closest('.commentDetails');
			var ulElements = currentCommentBlock.find('ul');
			if(ulElements.length > 0){
				ulElements.show();
				commentActionsBlock.find('.hideThreadBlock').show();
				currentTargetParent.hide();
				return;
			}
			var commentId = currentTarget.closest('.commentDiv').find('.commentInfoHeader').data('commentid');
			thisInstance.getChildComments(commentId).then(function(data){
				jQuery(data).appendTo(jQuery(e.currentTarget).closest('.commentDetails'));
				commentActionsBlock.find('.hideThreadBlock').show();
				currentTargetParent.hide();
			});
		});

		detailContentsHolder.on('click','.hideThread', function(e){
			var currentTarget = jQuery(e.currentTarget);
			var currentTargetParent = currentTarget.parent();
			var commentActionsBlock = currentTarget.closest('.commentActions');
			var currentCommentBlock = currentTarget.closest('.commentDetails');
			currentCommentBlock.find('ul').hide();
			currentTargetParent.hide();
			commentActionsBlock.find('.viewThreadBlock').show();
		});

		detailContentsHolder.on('click','.detailViewThread',function(e){
			var recentCommentsTab = thisInstance.getTabByLabel(thisInstance.detailViewRecentCommentsTabLabel);
			var commentId = jQuery(e.currentTarget).closest('.singleComment').find('.commentInfoHeader').data('commentid');
			var commentLoad = function(data){
				window.location.href = window.location.href+'#'+commentId;
			}
			recentCommentsTab.trigger('click',{'commentid':commentId,'callback':commentLoad});
		});

		detailContentsHolder.on('click','.detailViewSaveComment', function(e){
			var dataObj = thisInstance.saveComment(e);
			dataObj.then(function(){
				if($('div').hasClass('widgetContainer_comments')){
				var commentsContainer = detailContentsHolder.find("[data-name='ModComments']");
				thisInstance.loadWidget(commentsContainer);
				}else{
					$('li[data-label-key="ModComments"]').trigger("click");
				}
			});
		});

		detailContentsHolder.on('click','.saveComment', function(e){
			var currentTarget = jQuery(e.currentTarget);
			var mode = currentTarget.data('mode');
			var dataObj = thisInstance.saveComment(e);
			dataObj.then(function(data){
				var closestAddCommentBlock = currentTarget.closest('.addCommentBlock');
				var commentTextAreaElement = closestAddCommentBlock.find('.commentcontent');
				var commentInfoBlock = currentTarget.closest('.singleComment');
				commentTextAreaElement.val('');
				if(mode == "add"){
					var commentId = data['result']['id'];
					var commentHtml = thisInstance.getCommentUI(commentId);
					commentHtml.then(function(data){
						var commentBlock = closestAddCommentBlock.closest('.commentDetails');
						var detailContentsHolder = thisInstance.getContentHolder();
						var noCommentsMsgContainer = jQuery('.noCommentsMsgContainer',detailContentsHolder);
						noCommentsMsgContainer.remove();
						if(commentBlock.length > 0){
							closestAddCommentBlock.remove();
							var childComments = commentBlock.find('ul');
							if(childComments.length <= 0){
								var currentChildCommentsCount = commentInfoBlock.find('.viewThreadBlock').data('childCommentsCount');
								var newChildCommentCount = currentChildCommentsCount + 1;
								commentInfoBlock.find('.childCommentsCount').text(newChildCommentCount);
								var parentCommentId = commentInfoBlock.find('.commentInfoHeader').data('commentid');
								thisInstance.getChildComments(parentCommentId).then(function(responsedata){
									jQuery(responsedata).appendTo(commentBlock);
									commentInfoBlock.find('.viewThreadBlock').hide();
									commentInfoBlock.find('.hideThreadBlock').show();
								});
							}else {
								jQuery('<ul class="liStyleNone"><li class="commentDetails">'+data+'</li></ul>').appendTo(commentBlock);
							}
						} else {
							jQuery('<ul class="liStyleNone"><li class="commentDetails">'+data+'</li></ul>').prependTo(closestAddCommentBlock.closest('.commentContainer').find('.commentsList'));
							commentTextAreaElement.css({height : '71px'});
						}
						commentInfoBlock.find('.commentActionsContainer').show();
					});
				}else if(mode == "edit"){
					var modifiedTime = commentInfoBlock.find('.commentModifiedTime');
					var commentInfoContent = commentInfoBlock.find('.commentInfoContent');
					var commentEditStatus = commentInfoBlock.find('[name="editStatus"]');
					var commentReason = commentInfoBlock.find('[name="editReason"]');
					commentInfoContent.text(data.result.commentcontent);
					commentReason.text(data.result.reasontoedit);
					modifiedTime.text(data.result.modifiedtime);
					modifiedTime.attr('title',data.result.modifiedtimetitle)
					if(commentEditStatus.hasClass('hide')){
						commentEditStatus.removeClass('hide');
					}
					commentInfoContent.show();
					commentInfoBlock.find('.commentActionsContainer').show();
					closestAddCommentBlock.remove();
				}
			});
		});

		detailContentsHolder.on('click','.moreRecentComments', function(){
			var recentCommentsTab = thisInstance.getTabByLabel(thisInstance.detailViewRecentCommentsTabLabel);
			//recentCommentsTab.attr('data-url',recentCommentsTab.data('url')+'&page='+jQuery('.nextpage').val());
			var page=jQuery('.nextpage').val()*1;
			if($(this).hasClass('nexttopage')){
				page=page*1+1;
			}else{
				page=page*1-1;
			}
			if(page<=0){
				page=1;
			}
			jQuery('.nextpage').val(page);
			recentCommentsTab.trigger('click');
		});

		detailContentsHolder.on('click','.moreRecentUpdates', function(){
			var recentUpdatesTab = thisInstance.getTabByLabel(thisInstance.detailViewRecentUpdatesTabLabel);
			recentUpdatesTab.trigger('click');
		});

		detailContentsHolder.on('click','.moreRecentDocuments', function(){
			var recentDocumentsTab = thisInstance.getTabByLabel(thisInstance.detailViewRecentDocumentsTabLabel);
			recentDocumentsTab.trigger('click');
		});

		detailContentsHolder.on('click','.moreRecentActivities', function(){
			var recentActivitiesTab = thisInstance.getTabByLabel(thisInstance.detailViewRecentActivitiesTabLabel);
			recentActivitiesTab.trigger('click');
		});

		thisInstance.getForm().validationEngine(app.validationEngineOptions);

		thisInstance.loadWidgets();

		app.registerEventForTextAreaFields(jQuery('.commentcontent'));
		this.registerEventForTotalRecordsCount();
		jQuery('.pageNumbers',detailContentsHolder).tooltip();
	},
    pre_click_upload:function(){
        	$("body").on('click','#uploadButton',function () {
                if($('#file').length>0){
                    var module=$('#module').val();
                    // KindEditor.ready(function(K) {
					var record = $("#recordId").val();
                    window.K = KindEditor;
                    var uploadbutton = K.uploadbutton({
                        button : K('#uploadButton')[0],
                        fieldName : 'File',
                        extraParams :{
                            __vtrftk:csrfMagicToken,
                            record:record
                        },
                        url : 'index.php?module='+module+'&action=FileUpload&record='+record,
                        afterUpload : function(data) {
                            /*if (data.success ==true) {
                             $('input[name="attachmentsid"]').val(data.result['id']);
                             K('#file').val(data.result['name']);
                             } else {
                             }*/
                            if (data.success ==true) {
                            	$("#fileall").empty();
                                $('.filedelete').remove();
                                // var str='<span class="label file'+data.result['id']+'" style="margin-left:5px;">'+data.result['name']+'&nbsp;<b class="deletefile" data-class="file'+data.result['id']+'" data-id="'+data.result['id']+'" title="删除文件" style="display:inline-block;width:12px;height:12px;line-height:12px;text-align:center">x</b>&nbsp;</span><input class="ke-input-text file'+data.result['id']+'" type="hidden" name="file['+data.result['id']+']" id="file" value="'+data.result['name']+'" readonly="readonly" /><input class="file'+data.result['id']+'" type="hidden" name="attachmentsid['+data.result['id']+']" value="'+data.result['id']+'">';
                                var str='<span class="label file'+data.result['id']+'" style="margin-left:5px;">'+data.result['name']+'&nbsp;<b class="deletefile" data-class="file'+data.result['id']+'" data-id="'+data.result['id']+'" title="删除文件" style="display:inline-block;width:12px;height:12px;line-height:12px;text-align:center">x</b>&nbsp;</span>';
                                 str += "<input type='hidden' id='fileid' value='"+data.result['id']+"' data-name='"+data.result['name']+"'>";
                                $("#fileall").append(str);
                            } else {
                                Vtiger_Helper_Js.showMessage({type: 'error', text: data.result.msg});
                            }
                        },
                        afterError : function(str) {
                            //alert('自定义错误信息: ' + str);
                        }
                    });
                    uploadbutton.fileBox.change(function(e) {
                        uploadbutton.submit();
                    });
                    $('.fileUploadContainer').find('form').css({width:"54px"});
                    $('.fileUploadContainer').find('form').find('.btn-info').css({width:"54px",marginLeft:"-15px"});
                    // });
                }


            }
        )},
    /**
     * 删除上传的文件
     */
    deleteuploadFile:function(){
        $('body').on('mouseover','.deletefile',function(){
            $(this).css({color:"#666",cursor:"pointer",border:"#666 solid 1px",borderRadius:"12px"});
        }).on('mouseout','.deletefile',function(){
            $(this).css({color:"#fff",border:"none",borderRadius:"none"});
        }).on('click','.deletefile',function(e){
            e.stopPropagation();
            var delclassid=$(this).data('id');
            var module=$('#module').val();
            var url='index.php?module='+module+'&action=DeleteFile&id='+delclassid+'&record=';
            AppConnector.request(url).then(
                function(data){
                    if(data['success']) {
                        $("#fileid").val('');
                        $("#fileid").attr('data-name','');
                        $('.file'+delclassid).remove();
                    } else {
                        aDeferred.reject(data['message']);
                    }
                },
                function(error){
                    //aDeferred.reject();
                }
            )
        });

    },
    addExecutionNode:function(){
        $("body").on('click',"#ContractExecution_detailView_basicAction_LBL_ADD_CONTRACTS_EXECUTION",function (e) {
        	var record = $("#recordId").val();
            var params_r = [];
            params_r['action'] = 'ChangeAjax';
            params_r['module'] = 'ContractExecution';
            params_r['mode'] = 'canAdd';
            params_r['record'] = record;
            var progressIndicatorElement = jQuery.progressIndicator({
                'message' : '亲,正在拼命处理,请耐心等待哟',
                'position' : 'html',
                'blockInfo' : {'enabled' : true}
            });

            AppConnector.request(params_r).then(
                function(data) {
                    progressIndicatorElement.progressIndicator({
                        'mode' : 'hide'
                    });
                    console.log(data);
                    if (data.success) {
						console.log(data);

                        str = '<div id="myModal" class="modal" style="">\n' +
                            '\t<div class="modal-dialog">\n' +
                            '\t\t<div class="modal-content">\n' +
                            '\t\t\t<div class="modal-header">\n' +
                            '\t\t\t\t<button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>\n' +
                            '\t\t\t\t<h4 class="modal-title">添加合同执行节点</h4>\n' +
                            '\t\t\t\t<div style="margin-top: 20px;" id="supervisor">\n';
                        str += '\n' +
                            '\t\t\t\t</div>\n' +
                            '\t\t\t</div>\n' +
                            '\t\t\t<div class="modal-body" style="max-height:500px;">\n' +
                            '\n' +
                            '<div style="color: grey">已签收状态且类型为框架合同，才可手动录入合同执行节点，手动录入合同执行节点，合同最近一个节点必须已执行通过才可添加新节点</div><br>'+
                            '\t\t\t\t<div class="confirm tc">\n';


                        str += '<div class="add-execution"><div class="add-execution-tip"><span style="color: red">*</span></div><div class="add-execution-node">合同编号</div><div class="add-execution-info"><input name="contractid" type="hidden" value=""><input name="contractno" type="text" readonly></div></div>';
                        str += '<div  style="display: none"><div class="add-execution-tip"><span style="color: white">*</span></div><div style="color: red">最近一次合同节点执行时间：<span id="last_execution_date"></span></div></div>';
                        str += '<div class="add-execution"><div class="add-execution-tip"><span style="color: white">*</span></div><div class="add-execution-node">客户名称</div><div class="add-execution-info"><input name="accountid" type="hidden" value=""><input name="accountname" type="text" readonly></div></div>';
                        str += '<div class="add-execution"><div class="add-execution-tip"><span style="color: white">*</span></div><div class="add-execution-node">收款阶段</div><div class="add-execution-info"><input name="stage" type="hidden" value=""><input name="receivestage" type="text" readonly></div></div>';
                        str += '<div class="add-execution"><div class="add-execution-tip"><span style="color: red">*</span></div><div class="add-execution-node">应收金额</div><div class="add-execution-info"><input name="recieveableamount" type="text" ></div></div>';
                        str += '<div class="add-execution"><div class="add-execution-tip"><span style="color: red">*</span></div><div class="add-execution-node">收款说明</div><div class="add-execution-info"><textarea name="collectiondescription"  maxlength="100"></textarea></div></div>';
                        str += '<div class="add-execution"><div class="add-execution-tip"><span style="color: white">*</span></div><div class="add-execution-node">默认节点执行通过</div><div class="add-execution-info"><input name="isPass" type="radio" value="1"  checked="checked" />是  <input name="isPass" type="radio" value="0" />否</div></div>';
                        str += '<div class="add-execution" xmlns="http://www.w3.org/1999/html"><div class="add-execution-tip"><span style="color: white">*</span></div><div class="add-execution-node">执行凭证</div><div class="add-execution-info">'+
                            '    <div class="add-execution-info"><div class="fileUploadContainer" xmlns="http://www.w3.org/1999/html">\n' +
                            '                                <div class="upload">\n' +
                            '                                    <input type="button" id="uploadButton" value="上传"  title="支持pdf/png/jpg文件，不超过3M" />\n' +
                            '<span style="font-size:8px;color:gray">支持pdf/png/jpg文件不超过3M</span>'+
                            '                                    <div style="display:inline-block" id="fileall">\n' +
                            '                                            <input class="ke-input-text filedelete" type="hidden" name="file" id="file" value="" readonly="readonly" />\n' +
                            '                                            <input class="filedelete" type="hidden" name="attachmentsid" value="">\n' +
                            '                                    </div>\n' +
                            '                                </div>\n' +
                            '                            </div></div>'
                            +'</div></div>';

                        str +=                        '\n' +
                            '\t\t\t\t</div>\n' +
                            '\t\t\t</div>\n' +
                            '\t\t\t<div class="modal-footer">\n' +
                            '\t\t\t\t<div class=" pull-right cancelLinkContainer"><a class="cancelLink" type="reset" data-dismiss="modal">取消</a></div>\n' +
                            '\t\t\t\t<button class="btn btn-success" id="transferPost" type="submit">确定</button>\n' +
                            '\t\t\t</div>\n' +
                            '\t\t</div>\n' +
                            '\t</div>\n' +
                            '</div>';
                        str += ' <style>\n' +
                            '        .add-execution{\n' +
                            '            padding: 10px;\n' +
                            '            height: 40px;\n' +
                            '        }\n' +
                            '\n' +
                            '        .add-execution-tip{\n' +
                            '            float:left;\n' +
                            '            width: 10%;\n' +
                            '            text-align: right;\n' +
                            '        }\n' +
                            '\n' +
                            '        .add-execution-node{\n' +
                            '            float: left;\n' +
                            '            width:12%;\n' +
                            '            text-align: right;\n' +
                            '        }\n' +
                            '        .add-execution-info{\n' +
                            '            float:left;\n' +
                            '            width: 70%;\n' +
                            '            margin-left: 10px;\n' +
                            '        }\n' +
                            '    </style>';
                        app.showModalWindow(str);

                        $("input[name='contractno']").val(data.result.contract_no);
                        if(data.result.processdate){
                            $("#last_execution_date").parent().parent().show();
                            $("#last_execution_date").text(data.result.processdate);
                        }
                        $("input[name='accountname']").val(data.result.accountname);
                        $("input[name='accountid']").val(data.result.accountid);
                        var nextstage = 1+parseInt(data.result.stage);
                        $("input[name='stage']").val(nextstage);
                        $("input[name='receivestage']").val('第'+nextstage+'阶段');
                        $("input[name='contractid']").val(data.result.contractid);

                        $("#uploadButton").trigger('click');
                        $('.modal-backdrop').css({
                            "opacity":"0.6",
                            "z-index":"0"
                        });

						return;
                    }
                    var params = {
                        text: data.error.message,
                        type: 'error'
                    };
                    Vtiger_Helper_Js.showMessage(params);

                }
			)
        })
    },
    submitExecutionStage:function(){
        $("body").on('click',"#transferPost",function (e) {
            var contractno = $("input[name='contractno']").val();
            var accountid = $("input[name='accountid']").val();
            if(!contractno || !accountid){
                var params2 = {
                    text: '<h4>请先填写正确的合同编号</h4>',
                    type: 'error'
                };
                Vtiger_Helper_Js.showMessage(params2);
                event.preventDefault();
                return;
            }
            var stage = $("input[name='stage']").val();
            var receiveableamount = $("input[name='recieveableamount']").val();
            if(!receiveableamount){
                var params2 = {
                    text: '<h4>应收金额必填</h4>',
                    type: 'error'
                };
                Vtiger_Helper_Js.showMessage(params2);
                event.preventDefault();
                return;
            }
            var collectiondescription = $("textarea[name='collectiondescription']").val();
            if(!collectiondescription){
                var params2 = {
                    text: '<h4>收款说明必填</h4>',
                    type: 'error'
                };
                Vtiger_Helper_Js.showMessage(params2);
                return;
            }
            var ispass = $('input:radio[name="isPass"]:checked').val();
            var contractid = $("input[name='contractid']").val();
            var voucher = $("input[name='voucher']").val();
            var fileid =  $("#fileid").val();
            if(ispass=='0'&&fileid){
                var params2 = {
                    text: '<h4>当前节点不执行，无需上传凭证</h4>',
                    type: 'error'
                };
                Vtiger_Helper_Js.showMessage(params2);
                return;
            }
            var params_r = [];
            params_r['ispass'] = ispass;
            params_r['collectiondescription'] = collectiondescription;
            params_r['receiveableamount'] = receiveableamount;
            params_r['stage'] = stage;
            params_r['accountid'] = accountid;
            params_r['contractno'] = contractno;
            params_r['contractid'] = contractid;
            params_r['action'] = 'ChangeAjax';
            params_r['module'] = 'ContractExecution';
            params_r['mode'] = 'newExecutionNode';
            params_r['fileid'] =fileid;
            params_r['filename'] = $("#fileid").data('name');
            var progressIndicatorElement = jQuery.progressIndicator({
                'message' : '亲,正在拼命处理,请耐心等待哟',
                'position' : 'html',
                'blockInfo' : {'enabled' : true}
            });

            AppConnector.request(params_r).then(
                function(data) {
                    progressIndicatorElement.progressIndicator({
                        'mode' : 'hide'
                    });
                    if (data.success) {
                        location.href="index.php?module=ContractExecution&view=Detail&record="+data.result.contractexecutionid;
                        return;
                    }
                    var params = {
                        text: data.error.message,
                        type: 'error'
                    };
                    Vtiger_Helper_Js.showMessage(params);
                },
            )
        })
    },

});
