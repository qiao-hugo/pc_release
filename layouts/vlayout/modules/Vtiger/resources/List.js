jQuery.Class("Vtiger_List_Js",{listInstance:false,getRelatedModulesContainer:false,massEditPreSave:'Vtiger.MassEdit.PreSave',loadAjax:'Vtiger.Ajax.load',getInstance:function(){if(Vtiger_List_Js.listInstance==false){var module=app.getModuleName(),parentModule=app.getParentModuleName();if(parentModule=='Settings'){var moduleClassName=parentModule+"_"+module+"_List_Js";if(typeof window[moduleClassName]=='undefined'){moduleClassName = module+"_List_Js";}var fallbackClassName=parentModule+"_Vtiger_List_Js";if(typeof window[fallbackClassName]=='undefined'){fallbackClassName = "Vtiger_List_Js";}}else{moduleClassName=module+"_List_Js";fallbackClassName="Vtiger_List_Js";}if(typeof window[moduleClassName]!='undefined'){var instance= new window[moduleClassName]();}else{var instance=new window[fallbackClassName]();}Vtiger_List_Js.listInstance=instance;return instance;}return Vtiger_List_Js.listInstance;},getRelatedModuleContainer:function(){if(this.getRelatedModulesContainer==false){this.getRelatedModulesContainer=jQuery('#related_modules');}return this.getRelatedModulesContainer;},

	massDeleteRecords : function(url,instance) {
		var	listInstance = Vtiger_List_Js.getInstance();
		if(typeof instance != "undefined"){
			listInstance = instance;
		}
		var validationResult = listInstance.checkListRecordSelected();
		if(validationResult != true){
			// Compute selected ids, excluded ids values, along with cvid value and pass as url parameters
			var selectedIds = listInstance.readSelectedIds(true);
			var excludedIds = listInstance.readExcludedIds(true);
			var cvId = listInstance.getCurrentCvId();
			var message = app.vtranslate('LBL_MASS_DELETE_CONFIRMATION');
			Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
				function(e) {

					var deleteURL = url+'&viewname='+cvId+'&selected_ids='+selectedIds+'&excluded_ids='+excludedIds;
                    var listViewInstance = Vtiger_List_Js.getInstance();
                    var searchValue = listViewInstance.getAlphabetSearchValue();

                   	if((typeof searchValue != "undefined") && (searchValue.length > 0)) {
                        deleteURL += '&search_key='+listViewInstance.getAlphabetSearchField();
                        deleteURL += '&search_value='+searchValue;
                        deleteURL += '&operator=s';
                    }
					var deleteMessage = app.vtranslate('JS_RECORDS_ARE_GETTING_DELETED');
					var progressIndicatorElement = jQuery.progressIndicator({
						'message' : deleteMessage,
						'position' : 'html',
						'blockInfo' : {
							'enabled' : true
						}
					});
					AppConnector.request(deleteURL).then(
						function() {
							progressIndicatorElement.progressIndicator({
								'mode' : 'hide'
							})
							listInstance.postMassDeleteRecords();
						}
					);
				},
				function(error, err){
				Vtiger_List_Js.clearList();
				})
		} else {
			listInstance.noRecordSelectedAlert();
		}
	},
	whiteListRecords : function(url,instance) {
		var	listInstance = Vtiger_List_Js.getInstance();
		if(typeof instance != "undefined"){
			listInstance = instance;
		}
		var validationResult = listInstance.checkListRecordSelected();
		if(validationResult != true){
			// Compute selected ids, excluded ids values, along with cvid value and pass as url parameters
			var selectedIds = listInstance.readSelectedIds(true);
			var excludedIds = listInstance.readExcludedIds(true);
			var cvId = listInstance.getCurrentCvId();
			var message = app.vtranslate('LBL_MASS_WHITE_CONFIRMATION');
			/*console.log(listInstance);console.log(selectedIds);console.log(cvId);console.log(excludedIds);console.log(message);*/

			Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
				function(e) {

					var deleteURL = url+'&viewname='+cvId+'&selected_ids='+selectedIds+'&excluded_ids='+excludedIds;
                    var listViewInstance = Vtiger_List_Js.getInstance();
                    var searchValue = listViewInstance.getAlphabetSearchValue();
                    //console.log(listViewInstance);
                   	if((typeof searchValue != "undefined") && (searchValue.length > 0)) {
                        deleteURL += '&search_key='+listViewInstance.getAlphabetSearchField();
                        deleteURL += '&search_value='+searchValue;
                        deleteURL += '&operator=s';
                    }
					var deleteMessage = app.vtranslate('JS_RECORDS_ARE_GETTING_WHITE_LIST');
					//console.log(deleteMessage);

					var actionParams = {
							"type":"POST",
							"url":url,
							"dataType":"html",
							"data" : {'selectedId':selectedIds}
						};
					var progressIndicatorElement = jQuery.progressIndicator({
						'message' : deleteMessage,
						'position' : 'html',
						'blockInfo' : {
							'enabled' : true
						}
					});

					AppConnector.request(actionParams).then(
						function() {
							progressIndicatorElement.progressIndicator({
								'mode' : 'hide'
							})
							listInstance.postMassDeleteRecords();
						}
					);

				},
				function(error, err){
				Vtiger_List_Js.clearList();
				})


		} else {
			listInstance.noRecordSelectedAlert();
		}
	},


	deleteRecord : function(recordId) {
		var listInstance = Vtiger_List_Js.getInstance();
		var message = app.vtranslate('LBL_DELETE_CONFIRMATION');
		Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
			function(e) {
				var module = app.getModuleName();
				var postData = {"module": module,"action": "DeleteAjax","record": recordId,"parent": app.getParentModuleName()};
				var deleteMessage = app.vtranslate('JS_RECORD_GETTING_DELETED');
				var progressIndicatorElement = jQuery.progressIndicator({'message' : deleteMessage,'position' : 'html','blockInfo' : {'enabled' : true}});
				AppConnector.request(postData).then(
					function(data){
						progressIndicatorElement.progressIndicator({'mode' : 'hide'})
						if(data.success) {
						//删除后移除数据并减一
							$('tr[data-id='+recordId+']').remove();
							var jscount=parseInt($('#jscount').text()),orderBy = jQuery('#orderBy').val(),sortOrder = jQuery("#sortOrder").val(),urlParams = {"viewname": data.result.viewname,"orderby": orderBy,"sortorder": sortOrder};
							$('#jscount').text(jscount-1);
							//jQuery('#recordsCount').val('');jQuery('#totalPageCount').text('');
							/* listInstance.getListViewRecords(urlParams).then(function(){listInstance.updatePagination();}); */
						} else {
							var  params = {text : app.vtranslate(data.error.message),title : app.vtranslate('JS_LBL_PERMISSION')}
							Vtiger_Helper_Js.showPnotify(params);
						}
					},
					function(error,err){}
				);
			},
			function(error, err){}
		);
	},


	triggerMassAction : function(massActionUrl,callBackFunction,beforeShowCb, css) {
		if(typeof beforeShowCb == 'undefined') {beforeShowCb = function(){return true;};}
		if(typeof beforeShowCb == 'object') {	css = beforeShowCb;	beforeShowCb = function(){return true;};}
		var listInstance = Vtiger_List_Js.getInstance();
		var validationResult = listInstance.checkListRecordSelected();
		if(validationResult != true){
		// Compute selected ids, excluded ids values, along with cvid value and pass as url parameters
		var selectedIds = listInstance.readSelectedIds(true),excludedIds = listInstance.readExcludedIds(true),cvId = listInstance.getCurrentCvId(),postData = {"viewname" : cvId,"selected_ids":selectedIds,"excluded_ids" : excludedIds},listViewInstance = Vtiger_List_Js.getInstance(),searchValue = listViewInstance.getAlphabetSearchValue();
		if((typeof searchValue != "undefined") && (searchValue.length > 0)) {
            postData['search_key'] = listViewInstance.getAlphabetSearchField();
            postData['search_value'] = searchValue;
            postData['operator'] = "s";
        }
		var actionParams = {"type":"POST","url":massActionUrl,"dataType":"html","data" : postData};
		if(typeof css == 'undefined'){	css = {};}
		var css = jQuery.extend({'text-align' : 'left'},css);
		AppConnector.request(actionParams).then(
			function(data) {
				if(data) {
					var result = beforeShowCb(data);
					if(!result) {	return;	}
					app.showModalWindow(data,function(data){
						if(typeof callBackFunction == 'function'){	callBackFunction(data);
							//listInstance.triggerDisplayTypeEvent();
						}
					},css)
				}
			},
			function(error,err){}
		);
		} else {	listInstance.noRecordSelectedAlert();}
	},

	triggerMassEdit : function(massEditUrl) {
		Vtiger_List_Js.triggerMassAction(massEditUrl, function(container){
			var massEditForm = container.find('#massEdit');
			massEditForm.validationEngine(app.validationEngineOptions);
			var listInstance = Vtiger_List_Js.getInstance();
			listInstance.inactiveFieldValidation(massEditForm);
			listInstance.registerReferenceFieldsForValidation(massEditForm);
			listInstance.registerFieldsForValidation(massEditForm);
			listInstance.registerEventForTabClick(massEditForm);
			listInstance.registerRecordAccessCheckEvent(massEditForm);
			var editInstance = Vtiger_Edit_Js.getInstance();
			editInstance.registerBasicEvents(massEditForm);
			//To remove the change happended for select elements due to picklist dependency
			container.find('select').trigger('change',{'forceDeSelect':true});
			listInstance.postMassEdit(container);
			listInstance.registerSlimScrollMassEdit();
		},{'width':'65%'});
	},

	/*
	 * function to trigger export action
	 * returns UI
	 */
	triggerExportAction :function(exportActionUrl){
		var listInstance = Vtiger_List_Js.getInstance();
		// Compute selected ids, excluded ids values, along with cvid value and pass as url parameters
		var selectedIds = listInstance.readSelectedIds(true);
		var excludedIds = listInstance.readExcludedIds(true);
		var cvId = listInstance.getCurrentCvId();
		//var cvId = 1;
		var pageNumber = jQuery('#pageNumber').val();
        exportActionUrl += '&selected_ids='+selectedIds+'&excluded_ids='+excludedIds+'&viewname='+cvId+'&page='+pageNumber;
        var listViewInstance = Vtiger_List_Js.getInstance();
        var searchValue = listViewInstance.getAlphabetSearchValue();
		if((typeof searchValue != "undefined") && (searchValue.length > 0)) {
            exportActionUrl += '&search_key='+listViewInstance.getAlphabetSearchField()+'&search_value='+searchValue+'&operator=s';
        }
        window.location.href = exportActionUrl;
	},

	/**
	 * 刷新
	 */
	clearList : function() {jQuery('#deSelectAllMsg').trigger('click');jQuery("#selectAllMsgDiv").hide();},

	showDuplicateSearchForm : function(url) {
		app.showModalWindow("", url, function() {
			Vtiger_List_Js.registerDuplicateSearchButtonEvent();
		});
	},

	//绑定查询事件 By Joe@20150421
	showUserFieldEdit : function(url) {
		app.showModalWindow("", url, function() {
			Vtiger_List_Js.registerDuplicateSearchButtonEvent();
			$('#addField').click(function() {$('#fieldsToSelectList option:selected').appendTo('#fieldsToShowList');});
			$('#deleteField').click(function(e) {$('#fieldsToShowList option:selected').appendTo('#fieldsToSelectList');});
			$('#addAllField').click(function(e) {$('#fieldsToSelectList option').appendTo('#fieldsToShowList');});
			$('#deleteAllField').click(function(e) {$('#fieldsToShowList option').appendTo('#fieldsToSelectList');});
			$('#upButton').click(function(e){var selectedItems = $('#fieldsToShowList option:selected');selectedItems.each(function(){$(this).after($(this).prev());});});
			$('#downButton').click(function(e){var selectedItems = $('#fieldsToShowList option:selected');selectedItems.each(function(){$(this).before($(this).next());});});
		});	
	},



	/**
	 * Function that will enable Duplicate Search Find button
	 */
	registerDuplicateSearchButtonEvent : function() {
		jQuery('#fieldList').on('change', function(e) {
			var value = jQuery(e.currentTarget).val();
			var button = jQuery('#findDuplicate').find('button[type="submit"]');
			if(value != null) {
				button.attr('disabled', false);
			} else {
				button.attr('disabled', true);
			}
		})
	}
},{

	//contains the List View element.
	listViewContainer : false,
	//Contains list view top menu element
	listViewTopMenuContainer : false,
	//Contains list view content element
	listViewContentContainer : false,
	//Contains filter Block Element
	filterBlock : false,
	filterSelectElement : false,
	BugFreeQueryOBJ:{},
	getListViewContainer : function() {
		if(this.listViewContainer == false){
			this.listViewContainer = jQuery('div.listViewPageDiv');
		}
		return this.listViewContainer;
	},
	getListViewTopMenuContainer : function(){
		if(this.listViewTopMenuContainer == false){
			this.listViewTopMenuContainer = jQuery('.listViewTopMenuDiv');
		}
		return this.listViewTopMenuContainer;
	},
	getListViewContentContainer : function(){
		if(this.listViewContentContainer == false){
			this.listViewContentContainer = jQuery('.listViewContentDiv');
		}
		return this.listViewContentContainer;
	},
	getFilterBlock : function(){
		if(this.filterBlock == false){
			var filterSelectElement = this.getFilterSelectElement();
            if(filterSelectElement.length <= 0) {
                this.filterBlock = jQuery();
            }else if(filterSelectElement.is('select')){
                this.filterBlock = filterSelectElement.data('select2').dropdown;
            }
		}
		return this.filterBlock;
	},
	getFilterSelectElement : function() {

		if(this.filterSelectElement == false) {
			this.filterSelectElement = jQuery('#customFilter');
		}
		return this.filterSelectElement;
	},
	
	//初始化当前页面所有的参数
	getDefaultParams : function() {
		var pageNumber = jQuery('#pageNumber').val();
		var module = app.getModuleName();
		var parent = app.getParentModuleName();
		var cvId = this.getCurrentCvId();
		var orderBy = jQuery('#orderBy').val();
		var sortOrder = jQuery("#sortOrder").val();
		var pub = $('#public').val();
		var filter=$('#filter').val();
		var DepartFilter=$('#DepartFilter').val();
		var params = {
			//'__vtrftk':$('input[name="__vtrftk"]').val(),
			'module': module,
			'parent' : parent,
			'page' : pageNumber,
			'view' : "List",
			'viewname' : cvId,
			'orderby' : orderBy,
			'sortorder' : sortOrder,
			'public' : pub,
			'filter' :filter,
			'department':DepartFilter
		}

        var searchValue = this.getAlphabetSearchValue();

        if((typeof searchValue != "undefined") && (searchValue.length > 0)) {
            params['search_key'] = this.getAlphabetSearchField();
            params['search_value'] = searchValue;
            params['operator'] = "s";
        }
		return params;
	},

	/*
	 * Function which will give you all the list view params
	 */
	getListViewRecords : function(urlParams) {
		var aDeferred = jQuery.Deferred();
		if(typeof urlParams == 'undefined') {
			urlParams = {};
		}

		var thisInstance = this;
		var loadingMessage = jQuery('.listViewLoadingMsg').text();
		var progressIndicatorElement = jQuery.progressIndicator({
			'message' : loadingMessage,
			'position' : 'html',
			'blockInfo' : {
				'enabled' : true
			}
		});

		var defaultParams = this.getDefaultParams();
		var urlParams = jQuery.extend(defaultParams, urlParams);
		AppConnector.requestPjax(urlParams).then(
			function(data){
				progressIndicatorElement.progressIndicator({
					'mode' : 'hide'
				})
                jQuery('#listViewContents').html(data);
				thisInstance.calculatePages().then(function(data){
					//thisInstance.triggerDisplayTypeEvent();
					Vtiger_Helper_Js.showHorizontalTopScrollBar();

					var selectedIds = thisInstance.readSelectedIds();

					aDeferred.resolve(data);

					// Let listeners know about page state change.
					app.notifyPostAjaxReady();
				});
			},

			function(textStatus, errorThrown){
				aDeferred.reject(textStatus, errorThrown);
			}
		);
		return aDeferred.promise();
	},

	/**
	 * Function to calculate number of pages
	 */
	calculatePages : function() {
		var aDeferred = jQuery.Deferred();
		var element = jQuery('#totalPageCount');
		var totalPageNumber = element.text();
		if(totalPageNumber == ""){
			var totalRecordCount = jQuery('#totalCount').val();
			if(totalRecordCount != '') {
				var pageLimit = jQuery('#pageLimit').val();
				if(pageLimit == '0') pageLimit = 1;
				pageCount = Math.ceil(totalRecordCount/pageLimit);
				if(pageCount == 0){
					pageCount = 1;
				}
				element.text(pageCount);
				aDeferred.resolve();
				return aDeferred.promise();
			}
			this.getPageCount().then(function(data){
				var pageCount=0;
				if(typeof data['result'] !='undefined'){
					pageCount = data['result']['page'];
				}

				if(pageCount == 0){
					pageCount = 1;
				}
				element.text(pageCount);
				aDeferred.resolve();
			});
		} else {
			aDeferred.resolve();
		}
		return aDeferred.promise();
	},

	noRecordSelectedAlert : function(){	return alert('请至少选择一个记录');},

	massActionSave : function(form, isMassEdit){
		if(typeof isMassEdit == 'undefined') {
			isMassEdit = false;
		}
		var aDeferred = jQuery.Deferred();
		var massActionUrl = form.serializeFormData();
		if(isMassEdit) {
			var fieldsChanged = false;
            var massEditFieldList = jQuery('#massEditFieldsNameList').data('value');
			for(var fieldName in massEditFieldList){
                var fieldInfo = massEditFieldList[fieldName];

                var fieldElement = form.find('[name="'+fieldInfo.name+'"]');
                if(fieldInfo.type == "reference") {
                    //get the element which will be shown which has "_display" appended to actual field name
                    fieldElement = form.find('[name="'+fieldInfo.name+'_display"]');
                }else if(fieldInfo.type == "multipicklist") {
                    fieldElement = form.find('[name="'+fieldInfo.name+'[]"]');
                }

                //Not all fields will be enabled for mass edit
                if(fieldElement.length == 0) {
                    continue;
                }

                var validationElement = fieldElement.filter('[data-validation-engine]');
                //check if you have element enabled has changed
                if(validationElement.length == 0){
                    if(fieldInfo.type == "multipicklist") {
                        fieldName = fieldName+"[]";
                    }
                    delete massActionUrl[fieldName];
                    if(fieldsChanged != true){
                        fieldsChanged = false;
                    }
                } else {
                    fieldsChanged = true;
                }
			}
			if(fieldsChanged == false){
				Vtiger_Helper_Js.showPnotify(app.vtranslate('NONE_OF_THE_FIELD_VALUES_ARE_CHANGED_IN_MASS_EDIT'));
				form.find('[name="saveButton"]').removeAttr('disabled');
				aDeferred.reject();
				return aDeferred.promise();
			}
			//on submit form trigger the massEditPreSave event
			var massEditPreSaveEvent = jQuery.Event(Vtiger_List_Js.massEditPreSave);
			form.trigger(massEditPreSaveEvent);
			if(massEditPreSaveEvent.isDefaultPrevented()) {
				form.find('[name="saveButton"]').removeAttr('disabled');
				aDeferred.reject();
				return aDeferred.promise();
			}
		}
		AppConnector.request(massActionUrl).then(
			function(data) {
				app.hideModalWindow();
				aDeferred.resolve(data);
			},
			function(error,err){
				app.hideModalWindow();
				aDeferred.reject(error,err);
			}
		);
		return aDeferred.promise();
	},

	/*
	 * Function to check the view permission of a record after save
	 */
	registerRecordAccessCheckEvent : function(form) {

		form.on(Vtiger_List_Js.massEditPreSave, function(e) {
			var assignedToSelectElement = form.find('[name="assigned_user_id"][data-validation-engine]');
			if(assignedToSelectElement.length > 0){
				if(assignedToSelectElement.data('recordaccessconfirmation') == true) {
					return;
				}else{
					if(assignedToSelectElement.data('recordaccessconfirmationprogress') != true) {
						var recordAccess = assignedToSelectElement.find('option:selected').data('recordaccess');
						if(recordAccess == false) {
							var message = app.vtranslate('JS_NO_VIEW_PERMISSION_AFTER_SAVE');
							Vtiger_Helper_Js.showConfirmationBox({
								'message' : message
							}).then(
								function(e) {
									assignedToSelectElement.data('recordaccessconfirmation',true);
									assignedToSelectElement.removeData('recordaccessconfirmationprogress');
									form.submit();
								},
								function(error, err){
									assignedToSelectElement.removeData('recordaccessconfirmationprogress');
									e.preventDefault();
								});
							assignedToSelectElement.data('recordaccessconfirmationprogress',true);
						} else {
							return true;
						}
					}
				}
			} else{
				return true;
			}
			e.preventDefault();
		});
	},

	checkSelectAll : function(){
		var state = true;
		jQuery('.listViewEntriesCheckBox').each(function(index,element){
			if(jQuery(element).is(':checked')){
				state = true;
			}else{
				state = false;
				return false;
			}
		});
		if(state == true){
			jQuery('#listViewEntriesMainCheckBox').attr('checked',true);
		} else {
			jQuery('#listViewEntriesMainCheckBox').attr('checked', false);
		}
	},

	getRecordsCount : function(){
		var aDeferred = jQuery.Deferred();
		var recordCountVal = jQuery("#recordsCount").val();
		if(recordCountVal != ''){
			aDeferred.resolve(recordCountVal);
		} else {
			var count = '';
			var cvId = this.getCurrentCvId();
			var module = app.getModuleName();
			var parent = app.getParentModuleName();
			var postData = {
				"module": module,
				"parent": parent,
				"view": "ListAjax",
				"viewname": cvId,
				"mode": "getRecordsCount"
			}

            var searchValue = this.getAlphabetSearchValue();
            if((typeof searchValue != "undefined") && (searchValue.length > 0)) {
                postData['search_key'] = this.getAlphabetSearchField();
                postData['search_value'] = this.getAlphabetSearchValue();
                postData['operator'] = "s";
            }

			AppConnector.request(postData).then(
				function(data) {
					var response = JSON.parse(data);
					jQuery("#recordsCount").val(response['result']['count']);
					count =  response['result']['count'];
					aDeferred.resolve(count);
				},
				function(error,err){

				}
			);
		}

		return aDeferred.promise();
	},

	getSelectOptionFromChosenOption : function(liElement){
		var classNames = liElement.attr("class");
		var classNamesArr = classNames.split(" ");
		var currentOptionId = '';
		jQuery.each(classNamesArr,function(index,element){
			if(element.match("^filterOptionId")){
				currentOptionId = element;
				return false;
			}
		});
		return jQuery('#'+currentOptionId);
	},

	readSelectedIds : function(decode){
		var cvId = this.getCurrentCvId();
		var selectedIdsElement = jQuery('#selectedIds');
		var selectedIdsDataAttr = cvId+'Selectedids';
		var selectedIdsElementDataAttributes = selectedIdsElement.data();
		if (!(selectedIdsDataAttr in selectedIdsElementDataAttributes) ) {
			var selectedIds = new Array();
			this.writeSelectedIds(selectedIds);
		} else {
			selectedIds = selectedIdsElementDataAttributes[selectedIdsDataAttr];
		}
		if(decode == true){
			if(typeof selectedIds == 'object'){
				return JSON.stringify(selectedIds);
			}
		}
		return selectedIds;
	},
	readExcludedIds : function(decode){
		var cvId = this.getCurrentCvId();
		var exlcudedIdsElement = jQuery('#excludedIds');
		var excludedIdsDataAttr = cvId+'Excludedids';
		var excludedIdsElementDataAttributes = exlcudedIdsElement.data();
		if(!(excludedIdsDataAttr in excludedIdsElementDataAttributes)){
			var excludedIds = new Array();
			this.writeExcludedIds(excludedIds);
		}else{
			excludedIds = excludedIdsElementDataAttributes[excludedIdsDataAttr];
		}
		if(decode == true){
			if(typeof excludedIds == 'object') {
				return JSON.stringify(excludedIds);
			}
		}
		return excludedIds;
	},

	writeSelectedIds : function(selectedIds){
		var cvId = this.getCurrentCvId();
		jQuery('#selectedIds').data(cvId+'Selectedids',selectedIds);
	},

	writeExcludedIds : function(excludedIds){
		var cvId = this.getCurrentCvId();
		jQuery('#excludedIds').data(cvId+'Excludedids',excludedIds);
	},

	getCurrentCvId : function(){
		return jQuery('#customFilter').find('option:selected').data('id');
	},

	getAlphabetSearchField : function() {
		return jQuery("#alphabetSearchKey").val();
	},

	getAlphabetSearchValue : function() {
		return jQuery("#alphabetValue").val();
	},


	/*
	 * Function to check whether atleast one record is checked
	 */
	checkListRecordSelected : function(){
		var selectedIds = this.readSelectedIds();
		if(typeof selectedIds == 'object' && selectedIds.length <= 0) {
			return true;
		}
		return false;
	},

	postMassEdit : function(massEditContainer) {
		var thisInstance = this;
		massEditContainer.find('form').on('submit', function(e){
			e.preventDefault();
			var form = jQuery(e.currentTarget);
			var invalidFields = form.data('jqv').InvalidFields;
			if(invalidFields.length == 0){
				form.find('[name="saveButton"]').attr('disabled',"disabled");
			}
			var invalidFields = form.data('jqv').InvalidFields;
			if(invalidFields.length > 0){
				return;
			}
			thisInstance.massActionSave(form, true).then(
				function(data) {
					thisInstance.getListViewRecords();
					Vtiger_List_Js.clearList();
				},
				function(error,err){
				}
			)
		});
	},
	/*
	 * Function to register List view Page Navigation
	 */
	registerPageNavigationEvents : function(){
		var aDeferred = jQuery.Deferred();
		var thisInstance = this;
		jQuery('#listViewNextPageButton').on('click',function(){
			var pageLimit = jQuery('#pageLimit').val();
			var noOfEntries = jQuery('#noOfEntries').val();
			if(noOfEntries == pageLimit){
				var orderBy = jQuery('#orderBy').val();
				var sortOrder = jQuery("#sortOrder").val();
				var cvId = thisInstance.getCurrentCvId();
				var urlParams = {
					"orderby": orderBy,
					"sortorder": sortOrder,
					"viewname": cvId
				}
				var pageNumber = jQuery('#pageNumber').val();
				var nextPageNumber = parseInt(parseFloat(pageNumber)) + 1;
				jQuery('#pageNumber').val(nextPageNumber);
				jQuery('#pageToJump').val(nextPageNumber);
				thisInstance.getListViewRecords(urlParams).then(
					function(data){
						thisInstance.updatePagination();
						var loadAjaxEvent = jQuery.Event(Vtiger_List_Js.loadAjax);
						jQuery('#listViewNextPageButton').trigger(loadAjaxEvent);
						aDeferred.resolve();
					},

					function(textStatus, errorThrown){
						aDeferred.reject(textStatus, errorThrown);
					}
				);
			}
			return aDeferred.promise();
		});
		jQuery('#listViewPreviousPageButton').on('click',function(){
			var aDeferred = jQuery.Deferred();
			var pageNumber = jQuery('#pageNumber').val();
			if(pageNumber > 1){
				var orderBy = jQuery('#orderBy').val();
				var sortOrder = jQuery("#sortOrder").val();
				var cvId = thisInstance.getCurrentCvId();
				var urlParams = {
					"orderby": orderBy,
					"sortorder": sortOrder,
					"viewname" : cvId
				}
				var previousPageNumber = parseInt(parseFloat(pageNumber)) - 1;
				jQuery('#pageNumber').val(previousPageNumber);
				jQuery('#pageToJump').val(previousPageNumber);
				thisInstance.getListViewRecords(urlParams).then(
					function(data){
						thisInstance.updatePagination();
						var loadAjaxEvent = jQuery.Event(Vtiger_List_Js.loadAjax);
						jQuery('#listViewPreviousPageButton').trigger(loadAjaxEvent);
						aDeferred.resolve();
					},

					function(textStatus, errorThrown){
						aDeferred.reject(textStatus, errorThrown);
					}
				);
			}
		});

		jQuery('#listViewPageJump').on('click',function(e){
			jQuery('#pageToJump').validationEngine('hideAll');
			var element = jQuery('#totalPageCount');
			var totalPageNumber = element.text();
			if(totalPageNumber == ""){
				var totalRecordCount = jQuery('#totalCount').val();
				if(totalRecordCount != '') {
					var recordPerPage = jQuery('#noOfEntries').val();
					if(recordPerPage == '0') recordPerPage = 1;
					pageCount = Math.ceil(totalRecordCount/recordPerPage);
					if(pageCount == 0){
						pageCount = 1;
					}
					element.text(pageCount);
					return;
				}
				element.progressIndicator({});
				thisInstance.getPageCount().then(function(data){
					var pageCount = data['result']['page'];
					if(pageCount == 0){
						pageCount = 1;
					}
					element.text(pageCount);
					element.progressIndicator({'mode': 'hide'});
			});
		}
		})

		jQuery('#listViewPageJumpDropDown').on('click','li',function(e){
			e.stopImmediatePropagation();
		}).on('keypress','#pageToJump',function(e){
			if(e.which == 13){
				e.stopImmediatePropagation();
				var element = jQuery(e.currentTarget);
				var response = Vtiger_WholeNumberGreaterThanZero_Validator_Js.invokeValidation(element);
				if(typeof response != "undefined"){
					element.validationEngine('showPrompt',response,'',"topLeft",true);
				} else {
					element.validationEngine('hideAll');
					var currentPageElement = jQuery('#pageNumber');
					var currentPageNumber = currentPageElement.val();
					var newPageNumber = parseInt(jQuery(e.currentTarget).val());
					var totalPages = parseInt(jQuery('#totalPageCount').text());
					if(newPageNumber > totalPages){
						var error = app.vtranslate('JS_PAGE_NOT_EXIST');
						element.validationEngine('showPrompt',error,'',"topLeft",true);
						return;
					}
					if(newPageNumber == currentPageNumber){
						var message = app.vtranslate('JS_YOU_ARE_IN_PAGE_NUMBER')+" "+newPageNumber;
						var params = {
							text: message,
							type: 'info'
						};
						Vtiger_Helper_Js.showMessage(params);
						return;
					}
					currentPageElement.val(newPageNumber);
					thisInstance.getListViewRecords().then(
						function(data){
							thisInstance.updatePagination();
							var loadAjaxEvent = jQuery.Event(Vtiger_List_Js.loadAjax);
							jQuery('#pageToJump').trigger(loadAjaxEvent);

							element.closest('.btn-group ').removeClass('open');
						},
						function(textStatus, errorThrown){
						}
					);
				}
				return false;
			}
		});
	},

	/**
	 * Function to get page count and total number of records in list
	 */
	getPageCount : function(){
		var aDeferred = jQuery.Deferred();
		var pageCountParams = this.getPageJumpParams();
		AppConnector.request(pageCountParams).then(
			function(data) {
				var response;
				if(typeof data != "object"){
					response = JSON.parse(data);
				} else{
					response = data;
				}
				aDeferred.resolve(response);
			},
			function(error,err){

			}
		);
		return aDeferred.promise();
	},

	/**
	 * Function to get Page Jump Params
	 */
	getPageJumpParams : function(){
		var params = this.getDefaultParams();
		params['view'] = "ListAjax";
		params['mode'] = "getPageCount";

		return params;
	},

	/**
	 * Function to update Pagining status
	 */
	updatePagination : function(){
		var previousPageExist = jQuery('#previousPageExist').val();
		var nextPageExist = jQuery('#nextPageExist').val();
		var previousPageButton = jQuery('#listViewPreviousPageButton');
		var nextPageButton = jQuery('#listViewNextPageButton');
		var pageJumpButton = jQuery('#listViewPageJump');
		var listViewEntriesCount = parseInt(jQuery('#noOfEntries').val());
		var pageStartRange = parseInt(jQuery('#pageStartRange').val());
		var pageEndRange = parseInt(jQuery('#pageEndRange').val());
		var pages = jQuery('#totalPageCount').text();


		if(pages == 1){
			pageJumpButton.attr('disabled',"disabled");
		}
		if(pages > 1){
			pageJumpButton.removeAttr('disabled');
		}
		if(previousPageExist != ""){
			previousPageButton.removeAttr('disabled');
		} else if(previousPageExist == "") {
			previousPageButton.attr("disabled","disabled");
		}

		if((nextPageExist != "") && (pages >1)){
			nextPageButton.removeAttr('disabled');
		} else if((nextPageExist == "") || (pages == 1)) {
			nextPageButton.attr("disabled","disabled");
		}
		if(listViewEntriesCount != 0){
		//	var pageNumberText = pageStartRange+" "+app.vtranslate('to')+" "+pageEndRange;
            var pageNumberText = pageStartRange+" "+pageEndRange; //取消to
			jQuery('.pageNumbers').html(pageNumberText);
		} else {
			jQuery('.pageNumbers').html("");
		}

	},
	/*
	 * Function to register the event for changing the custom Filter
	 */
	registerChangeCustomFilterEvent : function(){
		var thisInstance = this;
		var filterSelectElement = jQuery('#DepartFilter');
		filterSelectElement.change(function(e){
			jQuery('#pageNumber').val("1");
			jQuery('#pageToJump').val('1');
			jQuery('#orderBy').val('');
			jQuery("#sortOrder").val('');

			//var cvId = thisInstance.getCurrentCvId();
			selectedIds = new Array();
			excludedIds = new Array();

            var urlParams ={
                "department" : filterSelectElement.val(),
                //to make alphabetic search empty
                "search_key" : thisInstance.getAlphabetSearchField(),
                "search_value" : ""
            }
			//Make the select all count as empty
			jQuery('#recordsCount').val('');
			//Make total number of pages as empty
			jQuery('#totalPageCount').text("");
			thisInstance.getListViewRecords(urlParams).then (function(){
				thisInstance.updatePagination();
				var loadAjaxEvent = jQuery.Event(Vtiger_List_Js.loadAjax);
				filterSelectElement.trigger(loadAjaxEvent);
            });
		});
	},

	/*
	 * Function to register the click event for list view main check box.
	 */
	registerMainCheckBoxClickEvent : function(){
		var listViewPageDiv = this.getListViewContainer();
		var thisInstance = this;
		listViewPageDiv.on('click','#listViewEntriesMainCheckBox',function(){
			var selectedIds = thisInstance.readSelectedIds();
			var excludedIds = thisInstance.readExcludedIds();
			if(jQuery('#listViewEntriesMainCheckBox').is(":checked")){
				var recordCountObj = thisInstance.getRecordsCount();
				recordCountObj.then(function(data){
					jQuery('#totalRecordsCount').text(data);
					if(jQuery("#deSelectAllMsgDiv").css('display') == 'none'){
						jQuery("#selectAllMsgDiv").show();
					}
				});

				jQuery('.listViewEntriesCheckBox').each( function(index,element) {
					jQuery(this).attr('checked', true).closest('tr').addClass('highlightBackgroundColor');
					if(selectedIds == 'all'){
						if((jQuery.inArray(jQuery(element).val(), excludedIds))!= -1){
							excludedIds.splice(jQuery.inArray(jQuery(element).val(),excludedIds),1);
						}
					} else if((jQuery.inArray(jQuery(element).val(), selectedIds)) == -1){
						selectedIds.push(jQuery(element).val());
					}
				});
			}else{
				jQuery("#selectAllMsgDiv").hide();
				jQuery('.listViewEntriesCheckBox').each( function(index,element) {
					jQuery(this).attr('checked', false).closest('tr').removeClass('highlightBackgroundColor');
				if(selectedIds == 'all'){
					excludedIds.push(jQuery(element).val());
					selectedIds = 'all';
				} else {
					selectedIds.splice( jQuery.inArray(jQuery(element).val(), selectedIds), 1 );
				}
				});
			}
			thisInstance.writeSelectedIds(selectedIds);
			thisInstance.writeExcludedIds(excludedIds);

		});
	},

	/*
	 * Function  to register click event for list view check box.
	 */
	registerCheckBoxClickEvent : function(){
		var listViewPageDiv = this.getListViewContainer();
		var thisInstance = this;
		listViewPageDiv.delegate('.listViewEntriesCheckBox','click',function(e){
			var selectedIds = thisInstance.readSelectedIds();
			var excludedIds = thisInstance.readExcludedIds();
			var elem = jQuery(e.currentTarget);
			if(elem.is(':checked')){
				elem.closest('tr').addClass('highlightBackgroundColor');
				if(selectedIds== 'all'){
					excludedIds.splice( jQuery.inArray(elem.val(), excludedIds), 1 );
				} else if((jQuery.inArray(elem.val(), selectedIds)) == -1) {
					selectedIds.push(elem.val());
				}
			} else {
				elem.closest('tr').removeClass('highlightBackgroundColor');
				if(selectedIds == 'all') {
					excludedIds.push(elem.val());
					selectedIds = 'all';
				} else {
					selectedIds.splice( jQuery.inArray(elem.val(), selectedIds), 1 );
				}
			}
			thisInstance.checkSelectAll();
			thisInstance.writeSelectedIds(selectedIds);
			thisInstance.writeExcludedIds(excludedIds);
		});
	},

	/*
	 * Function to register the click event for select all.
	 */
	registerSelectAllClickEvent :  function(){
		var listViewPageDiv = this.getListViewContainer();
		var thisInstance = this;
		listViewPageDiv.delegate('#selectAllMsg','click',function(){
			jQuery('#selectAllMsgDiv').hide();
			jQuery("#deSelectAllMsgDiv").show();
			jQuery('#listViewEntriesMainCheckBox').attr('checked',true);
			jQuery('.listViewEntriesCheckBox').each( function(index,element) {
				jQuery(this).attr('checked', true).closest('tr').addClass('highlightBackgroundColor');
			});
			thisInstance.writeSelectedIds('all');
		});
	},

	/*
	* Function to register the click event for deselect All.
	*/
	registerDeselectAllClickEvent : function(){
		var listViewPageDiv = this.getListViewContainer();
		var thisInstance = this;
		listViewPageDiv.delegate('#deSelectAllMsg','click',function(){
			jQuery('#deSelectAllMsgDiv').hide();
			jQuery('#listViewEntriesMainCheckBox').attr('checked',false);
			jQuery('.listViewEntriesCheckBox').each( function(index,element) {
				jQuery(this).attr('checked', false).closest('tr').removeClass('highlightBackgroundColor');
			});
			var excludedIds = new Array();
			var selectedIds = new Array();
			thisInstance.writeSelectedIds(selectedIds);
			thisInstance.writeExcludedIds(excludedIds);
		});
	},

	/*
	 * Function to register the click event for listView headers
	 */
	registerHeadersClickEvent :  function(){
		var listViewPageDiv = this.getListViewContainer();
		var thisInstance = this;
		listViewPageDiv.on('click','.listViewHeaderValues',function(e){
			var fieldName = jQuery(e.currentTarget).data('columnname');
			var sortOrderVal = jQuery(e.currentTarget).data('nextsortorderval');
			var cvId = thisInstance.getCurrentCvId();
			var urlParams = {
				"orderby": fieldName,
				"sortorder": sortOrderVal,
				"viewname" : cvId
			}
			thisInstance.getListViewRecords(urlParams);
		});
	},

	/*
	 * function to register the click event event for create filter
	 */
	registerCreateFilterClickEvent : function(){
		var thisInstance = this;
		jQuery('#createFilter').on('click',function(event){
			//to close the dropdown
			thisInstance.getFilterSelectElement().data('select2').close();
			var currentElement = jQuery(event.currentTarget);
			var createUrl = currentElement.data('createurl');
			Vtiger_CustomView_Js.loadFilterView(createUrl);
		});
	},

	/*
	 * Function to register the click event for edit filter
	 */
	registerEditFilterClickEvent : function(){
		var thisInstance = this;
		var listViewFilterBlock = this.getFilterBlock();
		if(listViewFilterBlock != false){
			listViewFilterBlock.on('mouseup','li i.editFilter',function(event){
				//to close the dropdown
				thisInstance.getFilterSelectElement().data('select2').close();
				var liElement = jQuery(event.currentTarget).closest('.select2-result-selectable');
				var currentOptionElement = thisInstance.getSelectOptionFromChosenOption(liElement);
				var editUrl = currentOptionElement.data('editurl');
				Vtiger_CustomView_Js.loadFilterView(editUrl);
				event.stopPropagation();
			});
		}
	},

	/*
	 * Function to register the click event for delete filter
	 */
	registerDeleteFilterClickEvent: function(){
		var thisInstance = this;
		var listViewFilterBlock = this.getFilterBlock();
		if(listViewFilterBlock != false){
			//used mouseup event to stop the propagation of customfilter select change event.
			listViewFilterBlock.on('mouseup','li i.deleteFilter',function(event){
				//to close the dropdown
				thisInstance.getFilterSelectElement().data('select2').close();
				var liElement = jQuery(event.currentTarget).closest('.select2-result-selectable');
				var message = app.vtranslate('JS_LBL_ARE_YOU_SURE_YOU_WANT_TO_DELETE');
				Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
					function(e) {
						var currentOptionElement = thisInstance.getSelectOptionFromChosenOption(liElement);
						var deleteUrl = currentOptionElement.data('deleteurl');
						window.location.href = deleteUrl;
					},
					function(error, err){
					}
				);
				event.stopPropagation();
			});
		}
	},

	/*
	 * Function to register the click event for approve filter
	 */
	registerApproveFilterClickEvent: function(){
		var thisInstance = this;
		var listViewFilterBlock = this.getFilterBlock();

		if(listViewFilterBlock != false){
			listViewFilterBlock.on('mouseup','li i.approveFilter',function(event){
				//to close the dropdown
				thisInstance.getFilterSelectElement().data('select2').close();
				var liElement = jQuery(event.currentTarget).closest('.select2-result-selectable');
				var currentOptionElement = thisInstance.getSelectOptionFromChosenOption(liElement);
				var approveUrl = currentOptionElement.data('approveurl');
				window.location.href = approveUrl;
				event.stopPropagation();
			});
		}
	},

	/*
	 * Function to register the click event for deny filter
	 */
	registerDenyFilterClickEvent: function(){
		var thisInstance = this;
		var listViewFilterBlock = this.getFilterBlock();

		if(listViewFilterBlock != false){
			listViewFilterBlock.on('mouseup','li i.denyFilter',function(event){
				//to close the dropdown
				thisInstance.getFilterSelectElement().data('select2').close();
				var liElement = jQuery(event.currentTarget).closest('.select2-result-selectable');
				var currentOptionElement = thisInstance.getSelectOptionFromChosenOption(liElement);
				var denyUrl = currentOptionElement.data('denyurl');
				window.location.href = denyUrl;
				event.stopPropagation();
			});
		}
	},

	/*
	 * Function to register the hover event for customview filter options
	 */
	registerCustomFilterOptionsHoverEvent : function(){
		var thisInstance = this;
		var listViewTopMenuDiv = this.getListViewTopMenuContainer();
		var filterBlock = this.getFilterBlock()
		if(filterBlock != false){
			filterBlock.on('hover','li.select2-result-selectable',function(event){
				var liElement = jQuery(event.currentTarget);
				var liFilterImages = liElement.find('.filterActionImgs');
				if (liElement.hasClass('group-result')){
					return;
				}

				if( event.type === 'mouseenter' ) {
					if(liFilterImages.length > 0){
						liFilterImages.show();
					}else{
						thisInstance.performFilterImageActions(liElement);
					}

				} else {
					liFilterImages.hide();
				}
			});
		}
	},

	performFilterImageActions : function(liElement) {
		jQuery('.filterActionImages').clone(true,true).removeClass('filterActionImages').addClass('filterActionImgs').appendTo(liElement.find('.select2-result-label')).show();
		var currentOptionElement = this.getSelectOptionFromChosenOption(liElement);
		var deletable = currentOptionElement.data('deletable');
		if(deletable != '1'){
			liElement.find('.deleteFilter').remove();
		}
		var editable = currentOptionElement.data('editable');
		if(editable != '1'){
			liElement.find('.editFilter').remove();
		}
		var pending = currentOptionElement.data('pending');
		if(pending != '1'){
			liElement.find('.approveFilter').remove();
		}
		var approve = currentOptionElement.data('public');
		if(approve != '1'){
			liElement.find('.denyFilter').remove();
		}
	},

	/*
	 * 列表数据行双击打开详细，按钮单击详细
	 */
	registerRowClickEvent: function(){
		var thisInstance = this;
		var listViewContentDiv = this.getListViewContentContainer();
		listViewContentDiv.on('dblclick','.listViewEntries',function(e){
			if(jQuery(e.target, jQuery(e.currentTarget)).is('td:first-child')) return;
			if(jQuery(e.target).is('input[type="checkbox"]')) return;
			//if(jQuery(e.target).find('a')) return;

			var elem = jQuery(e.currentTarget);
			var recordUrl = elem.data('recordurl');
            if(typeof recordUrl == 'undefined') {
                return;
            }
			//window.location.href = recordUrl;
            window.open(recordUrl,'_blank');  
		});
		listViewContentDiv.on('click','.icon-th-list',function(e){
			var elem = jQuery(e.currentTarget);
			var recordUrl = elem.parent().parent().parent().data('recordurl');
            if(typeof recordUrl == 'undefined') {
                return;
            }
            window.open(recordUrl,'_blank');  
		});
	},

	/*
	 * 删除事件
	 */
	registerDeleteRecordClickEvent: function(){
		var thisInstance = this;
		var listViewContentDiv = this.getListViewContentContainer();
		listViewContentDiv.on('click','.deleteRecordButton',function(e){
			var elem = jQuery(e.currentTarget);
			var recordId = elem.closest('tr').data('id');
			Vtiger_List_Js.deleteRecord(recordId);
			e.stopPropagation();
		});
	},
	/*
	 * Function to register the click event of email field
	 */
	registerEmailFieldClickEvent : function(){
		var listViewContentDiv = this.getListViewContentContainer();
		listViewContentDiv.on('click','.emailField',function(e){
			e.stopPropagation();
		})
	},
	/*
	 * Function to register the click event of url field
	 */
	registerUrlFieldClickEvent : function(){
		var listViewContentDiv = this.getListViewContentContainer();
		listViewContentDiv.on('click','.urlField',function(e){
			e.stopPropagation();
		})
	},

	/**
	 * Function to inactive field for validation in a form
	 * this will remove data-validation-engine attr of all the elements
	 * @param Accepts form as a parameter
	 */
	inactiveFieldValidation : function(form){
        var massEditFieldList = jQuery('#massEditFieldsNameList').data('value');
		for(var fieldName in massEditFieldList){
            var fieldInfo = massEditFieldList[fieldName];

            var fieldElement = form.find('[name="'+fieldInfo.name+'"]');
            if(fieldInfo.type == "reference") {
                //get the element which will be shown which has "_display" appended to actual field name
                fieldElement = form.find('[name="'+fieldInfo.name+'_display"]');
            }else if(fieldInfo.type == "multipicklist") {
                fieldElement = form.find('[name="'+fieldInfo.name+'[]"]');
            }

            //Not all the fields will be enabled for mass edit
            if(fieldElement.length == 0 ) {
                continue;
            }

			var elemData = fieldElement.data();

            //Blank validation by default
            var validationVal = "validate[]"
            if('validationEngine' in elemData) {
                validationVal =  elemData.validationEngine;
                delete elemData.validationEngine;
            }
            fieldElement.data('invalidValidationEngine',validationVal);
			fieldElement.removeAttr('data-validation-engine');
		}
	},

	/**
	 * function to register field for validation
	 * this will add the data-validation-engine attr of all the elements
	 * make the field available for validation
	 * @param Accepts form as a parameter
	 */
	registerFieldsForValidation : function(form){
		form.find('.fieldValue').on('change','input,select,textarea',function(e, params){
			if(typeof params == 'undefined'){
				params = {};
			}

			if(typeof params.forceDeSelect == 'undefined') {
				params.forceDeSelect = false;
			}
			var element = jQuery(e.currentTarget);
			var fieldValue = element.val();
			var parentTd = element.closest('td');
			if(((fieldValue == "" || fieldValue == null) && (typeof(element.attr('data-validation-engine')) != "undefined")) || params.forceDeSelect){
				if(parentTd.hasClass('massEditActiveField')){
					parentTd.removeClass('massEditActiveField');
				}
				element.removeAttr('data-validation-engine');
				element.validationEngine('hide');
				var invalidFields = form.data('jqv').InvalidFields;
				var response = jQuery.inArray(element.get(0),invalidFields);
				if(response != '-1'){
					invalidFields.splice(response,1);
				}
			} else if((fieldValue != "") && (typeof(element.attr('data-validation-engine')) == "undefined")){
				element.attr('data-validation-engine', element.data('invalidValidationEngine'));
				parentTd.addClass('massEditActiveField');
			}
		})
	},

	registerEventForTabClick : function(form){
		var ulContainer = form.find('.massEditTabs');
		ulContainer.on('click','a[data-toggle="tab"]',function(e){
			form.validationEngine('validate');
			var invalidFields = form.data('jqv').InvalidFields;
			if(invalidFields.length > 0){
				e.stopPropagation();
			}
		});
	},

	registerReferenceFieldsForValidation : function(form){
		var referenceField = form.find('.sourceField');
		form.find('.sourceField').on(Vtiger_Edit_Js.referenceSelectionEvent,function(e,params){
			var element = jQuery(e.currentTarget);
			var elementName = element.attr('name');
			var fieldDisplayName = elementName+"_display";
			var fieldDisplayElement = form.find('input[name="'+fieldDisplayName+'"]');
			if(params.selectedName == ""){
				return;
			}
			fieldDisplayElement.attr('data-validation-engine', fieldDisplayElement.data('invalidValidationEngine'));
            var parentTd = fieldDisplayElement.closest('td');
            if(!parentTd.hasClass('massEditActiveField')){
                parentTd.addClass('massEditActiveField');
            }
		})
		form.find('.clearReferenceSelection').on(Vtiger_Edit_Js.referenceDeSelectionEvent,function(e){
			var sourceField = form.find('.sourceField');
			var sourceFieldName = sourceField.attr('name');
			var fieldDisplayName = sourceFieldName+"_display";
			var fieldDisplayElement = form.find('input[name="'+fieldDisplayName+'"]').removeAttr('data-validation-engine');
            var parentTd = fieldDisplayElement.closest('td');
            if(parentTd.hasClass('massEditActiveField')){
                parentTd.removeClass('massEditActiveField');
            }
		})
	},

	registerSlimScrollMassEdit : function() {
		app.showScrollBar(jQuery('div[name="massEditContent"]'), {'height':'400px'});
	},

	/*
	 * Function to register the submit event for mass Actions save
	 */
	registerMassActionSubmitEvent : function(){
        var thisInstance = this;
		jQuery('body').on('submit','#massSave',function(e){
			var form = jQuery(e.currentTarget);
			var commentContent = form.find('#commentcontent')
			var commentContentValue = commentContent.val();
			if(commentContentValue == "") {
				var errorMsg = app.vtranslate('JS_LBL_COMMENT_VALUE_CANT_BE_EMPTY')
				commentContent.validationEngine('showPrompt', errorMsg , 'error','bottomLeft',true);
				e.preventDefault();
				return;
			}
			commentContent.validationEngine('hide');
			thisInstance.massActionSave(form).then(function(data){
					Vtiger_List_Js.clearList();
			});
			e.preventDefault();
		});
	},

	changeCustomFilterElementView : function() {
		var filterSelectElement = this.getFilterSelectElement();
		if(filterSelectElement.length > 0 && filterSelectElement.is("select")) {
			app.showSelect2ElementView(filterSelectElement,{
				formatSelection : function(data, contianer){
					var resultContainer = jQuery('<span></span>');
					resultContainer.append(jQuery(jQuery('.filterImage').clone().get(0)).show());
					resultContainer.append(data.text);
					return resultContainer;
				},
				customSortOptGroup : true
			});

			var select2Instance = filterSelectElement.data('select2');
            jQuery('span.filterActionsDiv').appendTo(select2Instance.dropdown).removeClass('hide');
		}
	},

	triggerDisplayTypeEvent : function() {
		var widthType = app.cacheGet('widthType', 'narrowWidthType');
		if(widthType) {
			var elements = jQuery('.listViewEntriesTable').find('td,th');
			elements.attr('class', widthType);
		}
	},

	registerEventForAlphabetSearch : function() {
		var thisInstance = this;
		var listViewPageDiv = this.getListViewContentContainer();
		listViewPageDiv.on('click','.alphabetSearch',function(e) {
			var alphabet = jQuery(e.currentTarget).find('a').text();
			var cvId = thisInstance.getCurrentCvId();
			var AlphabetSearchKey = thisInstance.getAlphabetSearchField();
			var urlParams = {
				"viewname" : cvId,
				"search_key" : AlphabetSearchKey,
				"search_value" : alphabet,
				"operator" : 's',
				"page"	:	1
			}
			jQuery('#recordsCount').val('');
			//To Set the page number as first page
			jQuery('#pageNumber').val('1');
			jQuery('#pageToJump').val('1');
			jQuery('#totalPageCount').text("");
			thisInstance.getListViewRecords(urlParams).then(
					function(data){
						thisInstance.updatePagination();
                        //To unmark the all the selected ids
                        jQuery('#deSelectAllMsg').trigger('click');
					},

					function(textStatus, errorThrown){
					}
			);
		});
	},

	/**
	 * Function to show total records count in listview on hover
	 * of pageNumber text
	 */
	registerEventForTotalRecordsCount : function(){
		var thisInstance = this;
		jQuery('.pageNumbers').on('mouseenter',function(e){
			var element = jQuery(e.currentTarget);
			var totalRecordsElement = jQuery('#totalCount');
			var totalNumberOfRecords = totalRecordsElement.val();
			if(totalNumberOfRecords == '') {
				thisInstance.getPageCount().then(function(data){
					totalNumberOfRecords = data['result']['numberOfRecords'];
					totalRecordsElement.val(totalNumberOfRecords);
				});
			}
			if(totalNumberOfRecords != ''){
				var titleWithRecords = app.vtranslate("JS_TOTAL_RECORDS")+" "+totalNumberOfRecords;
				element.data('tooltip').options.title = titleWithRecords;
				return false;
			} else {
				element.data('tooltip').options.title = "";
			}
		})
	},
	registerChangehistoryFilterEvent : function(){
		var thisInstance = this;
		var filterSelectElement = $('a.historyFilter');
		//鼠标经过事件
		filterSelectElement.hover(function(){
			$(this).find('i').removeClass('hide');
		},function(){
			$(this).find('i').addClass('hide');
		});
		//点击事件
		filterSelectElement.click(function(e){
			jQuery('#pageNumber').val("1");
			jQuery('#pageToJump').val('1');
			jQuery('#orderBy').val('');
			jQuery("#sortOrder").val('');

			var cvId = $(this).data('id');
			var editurl = $(this).data('editurl');
			selectedIds = new Array();
			excludedIds = new Array();

            var urlParams ={
                "viewname" : cvId,
            }
			jQuery('#recordsCount').val('');
			jQuery('#totalPageCount').text("");

			window.location.href='index.php?module='+app.getModuleName()+'&parent=&page=1&view=List&viewname='+cvId+'&orderby=&sortorder=&public='+$('#public').val();

			//thisInstance.getListViewRecords(urlParams).then (function(){

				//Vtiger_CustomView_Js.loadFilterView(editurl);
				//thisInstance.updatePagination();

            //});
		});
		//删除事件
		filterSelectElement.find('.icon-remove').click(function(e){
			var message = app.vtranslate('JS_LBL_ARE_YOU_SURE_YOU_WANT_TO_DELETE');
			var thisInstance=$(this);
			event.stopPropagation();
			Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
				function(e) {
					var deleteUrl = thisInstance.data('deleteurl');
					window.location.href = deleteUrl;
				},
				function(error, err){
				}
			);

		});

		//Vtiger_CustomView_Js.loadFilterViewa();By Joe@20150525
		//Vtiger_CustomView_Js.advanceFilterInstance = Vtiger_AdvanceFilter_Js.getInstance(jQuery('.filterContainer',jQuery("#showSearch").html()));

	},
	registerEventForSearch : function (){
		var thisInstance = this;
		$('#advsearch').on('click',function(){
			if($('#showSearch').hasClass('hide')){
				$('#showSearch').removeClass('hide');
				$('#searchicon').removeClass('icon-chevron-down').addClass('icon-chevron-up');
			}else{
				$('#showSearch').addClass('hide');
				$('#searchicon').removeClass('icon-chevron-up').addClass('icon-chevron-down');
			}
			$(window).resize(function(){
				app.setContentsHeight();
			})
		});


	},
    registerPageNavigationEventsK : function(){
		
        var thisInstance = this;
        var pageLimit = jQuery('#pageLimit').val();
        var noOfEntries = jQuery('#noOfEntries').val();
        var orderBy = jQuery('#orderBy').val();
        var sortOrder = jQuery("#sortOrder").val();
        var cvId = thisInstance.getCurrentCvId();

        if($('.pagination-demo').length<1){
            return;
        }
        var pageNumber = jQuery('#pageNumber').val(); //当前页码
        var totalCount = jQuery('#totalCount').val(); // 总页数
        //默认绑定分页
        /*$('.pagination-demo').twbsPagination({
            total:noOfEntries,  //总记录数
            totalPages: totalCount,
            visiblePages: 6,
            first: '首页',
            prev: '上页',
            next: '下页',
            last: '末页',
            startPage:1,
            onPageClick: function (event, p){
                getpage(p,'#listViewContents',false);
				
            }
        });*/
        //列表页面通用获取html方法
        function getpage(p,id,pagereset){
            var aDeferred = jQuery.Deferred();
            $('#pagecount').val(p);
            var loadingMessage = jQuery('.listViewLoadingMsg').text();
            var progressIndicatorElement = jQuery.progressIndicator({
                'message' : loadingMessage,
                'position' : 'html',
                'blockInfo' : {
                    'enabled' : true
                }
            });
            //$('.pagination-demo').css({'display':'none'});
            //console.log(p);

            var searchParamsPreFix = 'BugFreeQuery';
            var rowOrder = "";
            var $searchRows = $("tr[id^=SearchConditionRow]");
            $searchRows.each(function(){
                rowOrder += $(this).attr("id")+",";
            });

            eval("$('#"+searchParamsPreFix+"_QueryRowOrder')").attr("value",rowOrder);
            var limit = $('#limit').val();
            /*var o = {};
            var a = $('#SearchBug').serializeArray();
            $.each(a, function() {
                if (o[this.name] !== undefined) {
                    if (!o[this.name].push) {
                        o[this.name] = [o[this.name]];
                    }
                    o[this.name].push(this.value || '');
                } else {
                    o[this.name] = this.value || '';
                }
            });
            var form=JSON.stringify(o);*/
            var form=thisInstance.BugFreeQueryOBJ;
            var urlParams = {"page":p,"BugFreeQuery":form,"limit":limit};

            var defaultParams = thisInstance.getDefaultParams();
            urlParams = jQuery.extend(defaultParams,urlParams);
			var limit=$('#pageLimit').val();
			var noOfEntries=$('#noOfEntries').val();
			var totalCount=Math.ceil(noOfEntries/limit);
			totalCount=totalCount>0?totalCount:1;
            //搜索提交查询后调用到这里
            //console.log(urlParams);
            AppConnector.requestPjaxPost(urlParams).then(
                function(data){

                    progressIndicatorElement.progressIndicator({
                        'mode' : 'hide'
                    })
                    //$('.pagination-demo').css({'display':''});
                    if(pagereset){
						//键入条转页的时候会用到？？？
                        //$('#pagination').html('<ul class="pagination-demo"></ul>');
                    }

                    jQuery(id).html('');
                    jQuery(id).html(data);
					thisInstance.fixedTable();
					jQuery('#noOfEntries').val(noOfEntries);
					jQuery('#totalCount').val(totalCount);
                    noOfEntries = jQuery('#noOfEntries').val();
                    totalCount = jQuery('#totalCount').val();
					pageNumber = jQuery('#pageNumber').val();
                    app.setContentsHeight();//2015-4-15 young.yang #8720 分页请求重新调整页面高度
                    app.tabletrodd();  //隔行换色

					err = $("#sortOrder").val();
					ett = $("#orderBy").val();
					if(ett){
							imgclass = "";
						if(err == "ASC"){
							//imgclass = "icon-chevron-up icon-white";
							imgclass="layouts/vlayout/skins/images/sort_up.png"
						}else{
							//imgclass = "icon-chevron-down icon-white"
							imgclass="layouts/vlayout/skins/images/sort_down.png"
						}
						//$("#listViewContents").find("th[data-field= "+ett+"]").children("img").attr("class",imgclass);
						//$("#listViewContents").find("th").prepend('<img src="layouts/vlayout/skins/images/sort_all.png">');
						$("#listViewContents").find("th[data-field='"+ett+"']").children("img").attr("src",imgclass);
					}
                    aDeferred.resolve(data);
                },
                function(textStatus, errorThrown){
                    aDeferred.reject(textStatus, errorThrown);
                    progressIndicatorElement.progressIndicator({
                        'mode' : 'hide'
                    });
                    //aDeferred.resolve(false);
                }
            );
            $('#jumppage').val(''); //调整之后清空跳转
            return aDeferred.promise();
        }
		thisInstance.getPageOut=getpage;
        //列表搜索
        $('#PostQuery').on('click',function(){
        	thisInstance.registerInitListBugFree();
            getpage(1,'#listViewContents',true).then(
                function(data){
					thisInstance.registerGetCountsList();
                });
        });
        //后台用户检索
        $('#userSearchButton').on('click',function(){
			thisInstance.registerInitListBugFree();
            getpage(1,'#listViewContents',true).then(
                function(data){
					thisInstance.registerGetCountsList();
                    /*$('.pagination-demo').twbsPagination({
                        total:noOfEntries,
                        totalPages: totalCount,
                        visiblePages: 6,
                        first: '首页',
                        prev: '上页',
                        next: '下页',
                        last: '末页',
                        startPage:1,
                        onPageClick: function (event, p){
                            getpage(p,'#listViewContents',false);
                            //document.SearchBug.submit();
                        }
                    });*/
                },function(){});
        });
			
		//wangbin 2015-7-1 添加表头排序
		$("#listViewContents").find("th").live("click",function(){
			$("#orderBy").val($(this).attr('data-field')); //排序字段
			
			if($("#sortOrder").val() == "" || $("#sortOrder").val() == "ASC"){
				$("#sortOrder").val("DESC");
			}else{
				$("#sortOrder").val("ASC");
			} 
			tmp = parseInt(jQuery('#pageNumber').val());
            var limit = $('#pageLimit').val();
            var noOfEntries=$('#noOfEntries').val();
            var totalCount=Math.ceil(noOfEntries/limit);
			getpage(tmp,'#listViewContents',true).then(
				function(data){
					thisInstance.showTwbsPagination(noOfEntries,totalCount,1);
					/*$('.pagination-demo').twbsPagination({
						total:noOfEntries,
						totalPages: totalCount,
						visiblePages: 6,
						first: '首页',
						prev: '上页',
						next: '下页',
						last: '末页',
						startPage:tmp,
						onPageClick: function (event, p){
							getpage(p,'#listViewContents',false);
                        }
					});*/
                });
		});
		//点击排序
        /*$('.listViewEntriesTable .listViewHeaders th').on('click',function(){
            //alert($(this).data('field'));
            //$('.listViewEntriesTable .listViewHeaders th').find('i').remove();
            var iclass='icon-arrow-down';
            var defsort="DESC";
            //console.log($(this).find('i').hasClass(iclass));console.log(iclass);
            if($(this).find('i').length>0){ //如果为当前字段
                if($(this).find('i').hasClass('icon-arrow-up')){
                    iclass='icon-arrow-down';
                    defsort='DESC';
                }else{
                    iclass='icon-arrow-up';
                    defsort='ASC';
                }
            }else{ //非排序过的字段，先清除标记
                $('.listViewEntriesTable .listViewHeaders th').find('i').remove();
            }
            $(this).html($(this).text()+'<i class="'+iclass+'"></i>');
            //设置隐藏指
            jQuery('#orderBy').val($(this).data('field'));
            jQuery("#sortOrder").val(defsort);
            jQuery('#PostQuery').trigger('click');

        });*/
        //弹出框检索
        jQuery('#popupSearchButton').on('click',function(e) {
			thisInstance.registerInitListBugFree();
            getpage(1,'#popupContents',true).then(
                function(data){
					thisInstance.registerGetCountsList();
                    /*$('.pagination-demo').twbsPagination({
                        total:noOfEntries,
                        totalPages: totalCount,
                        visiblePages: 6,
                        first: '首页',
                        prev: '上页',
                        next: '下页',
                        last: '末页',
                        startPage:1,
                        onPageClick: function (event, p){
                            getpage(p,'#popupContents',false);
                            //document.SearchBug.submit();
                        }
                    });*/
                });
        });
        //跳转页
        $('#jumppage').on('change',function(){
            var jumppage=parseInt($(this).val());//$('#jumppage').val();
            var totalCount=$('#totalCount').val();
            var noOfEntries=$('#noOfEntries').val();
            if(jumppage<=totalCount){
                getpage(jumppage,'#listViewContents',true).then(
                    function(data){
						thisInstance.showTwbsPagination(noOfEntries,totalCount,jumppage);

                });
            }else{
                var params = {
                    title : 'ERROR',
                    text: '输入有误请重新输入',
                    animation: 'show',
                    type: 'error'
                };
                Vtiger_Helper_Js.showPnotify(params);
            }
            $('#jumppage').val('');
        });
        //每页多少条
        $('#limit').on('change',function(){
            //$('#pagination').html('<ul class="pagination-demo"></ul>');
			var limit=$(this).val();
			var noOfEntries=$('#noOfEntries').val();
			var totalCount=Math.ceil(noOfEntries/limit);
            getpage(1,'#listViewContents',true).then(
                function(data){
					thisInstance.showTwbsPagination(noOfEntries,totalCount,1);
                });
        });
        //2015-04-28 young 列表页面绑定回车时间
        $(document).bind('keypress',function(event){
            if(event.keyCode == "13")
            {
                event.preventDefault();
                if($('#jumppage').is(":focus")) {

                	$('#jumppage').trigger("change");
                }
                if($('#PostQuery').length>0 && !$('#jumppage').is(":focus")){
                    $('#PostQuery').trigger("click");
                }

            }
        });
		thisInstance.registerInitListBugFree();
		thisInstance.registerGetCountsList();
    },
	/**
	 * @param noOfEntries
	 * @param totalCount
	 * @param startpage
	 */
	showTwbsPagination:function(noOfEntries,totalCount,startpage){
		var thisInstance=this;
		var defaultParams = thisInstance.GetRequest();
		var listviewContents=defaultParams.src_module==undefined?'#listViewContents':'#popupContents';
		$('#pagination').html('<ul class="pagination-demo"></ul>');
		$('.pagination-demo').twbsPagination({
			total:noOfEntries,
			totalPages: totalCount,
			visiblePages: 6,
			first: '首页',
			prev: '上页',
			next: '下页',
			last: '末页',
			startPage:startpage,
			onPageClick: function (event, p){
				thisInstance.getPageOut(p,listviewContents,true);
			}
		});
		$('#totalCount').val(totalCount);
		$('#noOfEntries').val(noOfEntries);
	},
	fixedTable:function(){
		var module=$('#module').val();
		if(module=='Matchreceivements'){
			return false;
		}
		var parentModule=$('#parentModule').val();
		if(parentModule!=undefined){
			return false;
		}
		if($('.listViewEntriesTable tr').length<2){
			return false;
		}
		var parentName=$('input[name="parent"]').val();
		if(parentName=='Settings') {
			return false;
		}
		$('.listViewEntriesTable').fixedHeaderTable({footer: true});

		if(parentName!='Settings'){
			var row=$('.row').height()+20;
			var navBarFixedTop=$('.navbar-fixed-top').height();
			var breadcrumb=$('.breadcrumb').height()+10;
			var SearchBlankCover=$('#SearchBug').height();
			var minheight=jQuery(window).height()- (row +breadcrumb+navBarFixedTop+SearchBlankCover+80);
		}else{
			var minheight=jQuery(window).height()- 410;
		}

		$('.fht-tbody').css({"height":minheight+'px'});
	},
	/**
	 * 业面加载进来时初始化BugFree
	 */
	registerInitListBugFree:function(){
		this.BugFreeQueryOBJ={};
		var searchParamsPreFix = 'BugFreeQuery';
		var rowOrder = "";
		var $searchRows = $("tr[id^=SearchConditionRow]");
		$searchRows.each(function(){
			rowOrder += $(this).attr("id")+",";
		});
		$('#BugFreeQuery_QueryRowOrder').val(rowOrder);
		var o = {};
		var a = $('#SearchBug').serializeArray();
		$.each(a, function() {
			if (o[this.name] !== undefined) {
				if (!o[this.name].push) {
					o[this.name] = [o[this.name]];
				}
				o[this.name].push(this.value || '');
			} else {
				o[this.name] = this.value || '';
			}
		});
		this.BugFreeQueryOBJ=JSON.stringify(o);
	},

	 GetRequest:function() {
		var thisInstance=this;
		var defaultParams = thisInstance.getDefaultParams();
		var url = location.search; //获取url中"?"符后的字串
		if (url.indexOf("?") != -1) {
			var str = url.substr(1);
			var strs = str.split("&");
			for(var i = 0; i < strs.length; i ++) {
				var splitData=strs[i].split("=");
				defaultParams[splitData[0]]=splitData[1];
			}
		}
		return defaultParams;
	},
	registerGetCountsList:function(){
		var thisInstance=this;
		var module = app.getModuleName();
		var limit = $('#pageLimit').val();
		var form=this.BugFreeQueryOBJ;
		var defaultParams = thisInstance.GetRequest();
		delete defaultParams.module;
		delete defaultParams.view;
		var postData = {
			"module": module,
			"action": "JsonAjax",
			"mode": "getListViewCount",
			"page":1,
			"BugFreeQuery":form,
			"limit":limit
		}
		for(var temp in defaultParams){
			postData[temp]=defaultParams[temp];
		}
		AppConnector.request(postData).then(
			function(data){
				if(data.success) {
					var noOfEntries=data.result;
					$('#noOfEntries').val(noOfEntries);
					var totalCount=Math.ceil(noOfEntries/limit);
					totalCount=totalCount>0?totalCount:1;
					$('#totalCount').val(totalCount);
					thisInstance.showTwbsPagination(noOfEntries,totalCount,1);

				}
			},
			function(error,err){

			});
	},
	registerEventTd:function(){
		$('td[data-field-name="modulestatus"]').each(function(){
			var t=$(this).text();
			if(t=='正常'){
				$(this).html('<span class="label label-inverse">'+t+'</span>');
			}else if(t=='审核中'){
				$(this).html('<span class="label label-success">'+t+'</span>');
			}else if(t=='异常'){
				$(this).html('<span class="label label-warning">'+t+'</span>');
			}else{
				$(this).html('<span class="label label-info">'+t+'</span>');
			}
		});
	},
	//编辑自定义操作 By Joe@20150421
	sortFieldActionEvent : function(){
        var thisInstance = this;
		jQuery('body').on('submit','#findDuplicate',function(e){
			var fieldlist=new Array();
			$('#fieldsToShowList option').each(function(index,element){fieldlist.push($(this).val())});
			if(fieldlist==''){
				alert('请选择字段');
				e.preventDefault();
			}else{
				var params = {
				'module': app.getModuleName(),
				'view' : "FieldAjax",
				'mode':'updatesort',
				'fieldList':fieldlist,
				}
				AppConnector.request(params).then(
				function(data) {
					window.location.reload();
				},
				function(error,err){
				}
				);
			}
			e.preventDefault();
		});
		jQuery('body').on('reset','#findDuplicate',function(e){
			var fieldlist=new Array();
			$('#fieldsToShowList option').each(function(index,element){fieldlist.push($(this).val())});
			if(fieldlist==''){
				alert('已是默认状态，无需还原');
				e.preventDefault();
			}else{
				var params = {
					'module': app.getModuleName(),
					'view' : "FieldAjax",
					'mode':'updatesort',
					'fieldList':fieldlist,
					'fieldtype':"reset"
				}
				AppConnector.request(params).then(
					function(data) {
						window.location.reload();
					},
					function(error,err){
					}
				);
			}
			e.preventDefault();
		});
	},
	registerEvents : function(){
		
		//this.registerEventTd();
		this.registerRowClickEvent();
		this.sortFieldActionEvent();
		this.registerPageNavigationEvents();
		this.registerMainCheckBoxClickEvent();
		this.registerCheckBoxClickEvent();
		this.registerSelectAllClickEvent();
		this.registerDeselectAllClickEvent();
		this.registerDeleteRecordClickEvent();
		this.registerHeadersClickEvent();
		this.registerMassActionSubmitEvent();
		this.registerEventForAlphabetSearch();

		this.changeCustomFilterElementView();
		//this.registerChangeCustomFilterEvent(); 部门筛选去掉

		this.registerCreateFilterClickEvent();
		this.registerEditFilterClickEvent();
		this.registerDeleteFilterClickEvent();
		this.registerApproveFilterClickEvent();
		this.registerDenyFilterClickEvent();
		this.registerCustomFilterOptionsHoverEvent();
		this.registerEmailFieldClickEvent();
		//this.triggerDisplayTypeEvent();
		Vtiger_Helper_Js.showHorizontalTopScrollBar();
		this.registerUrlFieldClickEvent();
		this.registerEventForTotalRecordsCount();
		this.registerEventForSearch();
		this.registerChangehistoryFilterEvent();
		jQuery('.pageNumbers').tooltip();
        this.registerPageNavigationEventsK();
		if($('.addButton').length>1 || $('.addButton').length<1){
			$('.listViewActionsDiv').remove();
		}else{
			$('.listViewActionsDiv').show();
		}
		//Just reset all the checkboxes on page load: added for chrome issue.
		var listViewContainer = this.getListViewContentContainer();
		listViewContainer.find('#listViewEntriesMainCheckBox,.listViewEntriesCheckBox').prop('checked', false);
		this.tableFix();
		this.fixedTable();
		this.tableFixByAjax();
	},

	tableFix:function(){
		$('#listViewContents .fht-tbody .listViewEntriesTable').tableHeadFixer({
			'right': 1,
			'head' : false,
		});
	},

	tableFixByAjax:function(){
		$('#listViewContents').ajaxComplete(function () {
			$('#listViewContents .fht-tbody .listViewEntriesTable').tableHeadFixer({
				'right': 1,
				'head' : false,
			});
		});
	},

	/**
	 * Function that executes after the mass delete action
	 */
	postMassDeleteRecords : function() {
		var aDeferred = jQuery.Deferred();
		var listInstance = Vtiger_List_Js.getInstance();
		app.hideModalWindow();
		var module = app.getModuleName();
		var params = listInstance.getDefaultParams();
		AppConnector.request(params).then(
			function(data) {
				jQuery('#recordsCount').val('');
				jQuery('#totalPageCount').text('');
				var listViewContainer = listInstance.getListViewContentContainer();
				listViewContainer.html(data);
				//listInstance.triggerDisplayTypeEvent();
				jQuery('#deSelectAllMsg').trigger('click');
				listInstance.calculatePages().then(function(){
					listInstance.updatePagination();
				});
				aDeferred.resolve();
		});
		jQuery('#recordsCount').val('');
		return aDeferred.promise();
	},
	/* triggerSendEmail : function(massActionUrl, module, params){
		var listInstance = Vtiger_List_Js.getInstance();
		var validationResult = listInstance.checkListRecordSelected();
		if(validationResult != true){
			var selectedIds = listInstance.readSelectedIds(true);
			var excludedIds = listInstance.readExcludedIds(true);
			var cvId = listInstance.getCurrentCvId();
			var postData = {"viewname" : cvId,"selected_ids":selectedIds,"excluded_ids" : excludedIds};
            var listViewInstance = Vtiger_List_Js.getInstance();
            var searchValue = listViewInstance.getAlphabetSearchValue();
			if((typeof searchValue != "undefined") && (searchValue.length > 0)) {
                postData['search_key'] = listViewInstance.getAlphabetSearchField();
                postData['search_value'] = searchValue;
                postData['operator'] = "s";
            }
			jQuery.extend(postData,params);
			var actionParams = {"type":"POST","url":massActionUrl,"dataType":"html","data" : postData};
			Vtiger_Index_Js.showComposeEmailPopup(actionParams);
		} else {
			listInstance.noRecordSelectedAlert();
		}
	},
	triggerSendSms : function(massActionUrl, module){
		var listInstance = Vtiger_List_Js.getInstance();
		var validationResult = listInstance.checkListRecordSelected();
		if(validationResult != true){
			Vtiger_Helper_Js.checkServerConfig(module).then(function(data){
				if(data == true){
					Vtiger_List_Js.triggerMassAction(massActionUrl);
				} else {
					alert(app.vtranslate('JS_SMS_SERVER_CONFIGURATION'));
				}
			});
		} else {
			listInstance.noRecordSelectedAlert();
		}

	},//系统原有领取
	triggerTransferOwnership : function(massActionUrl){
		var thisInstance = this;
		var listInstance = Vtiger_List_Js.getInstance();
		var validationResult = listInstance.checkListRecordSelected();
		if(validationResult != true){
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
		} else {
			listInstance.noRecordSelectedAlert();
		}
	},
	transferOwnershipSave : function (form){
		var listInstance = Vtiger_List_Js.getInstance();
		var selectedIds = listInstance.readSelectedIds(true);
		var excludedIds = listInstance.readExcludedIds(true);
		var cvId = listInstance.getCurrentCvId();
		var transferOwner = jQuery('#transferOwnerId').val();
		var relatedModules = jQuery('#related_modules').val();

		var params = {
			'module': app.getModuleName(),
			'action' : 'TransferOwnership',
			"viewname" : cvId,
			"selected_ids":selectedIds,
			"excluded_ids" : excludedIds,
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
					listInstance.getListViewRecords();
					Vtiger_List_Js.clearList();
				}
			}
		);
	}, */
});
