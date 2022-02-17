/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("Leads_Detail_Js",{
	
	//cache will store the convert lead data(Model)
	cache : {},
	
	//Holds detail view instance
	detailCurrentInstance : false,
	
	/*
	 * function to trigger Convert Lead action
	 * @param: Convert Lead url, currentElement.
	 */
	convertLead : function(convertLeadUrl, buttonElement) {
		var instance = Leads_Detail_Js.detailCurrentInstance;
		//Initially clear the elements to overwtite earliear cache
		instance.convertLeadContainer = false;
		instance.convertLeadForm = false;
		instance.convertLeadModules = false;
		if(jQuery.isEmptyObject(Leads_Detail_Js.cache)) {
			AppConnector.request(convertLeadUrl).then(
				function(data) {
					if(data) {
						Leads_Detail_Js.cache = data;
						instance.displayConvertLeadModel(data, buttonElement);
					}
				},
				function(error,err){

				}
			);
		} else {
			instance.displayConvertLeadModel(Leads_Detail_Js.cache, buttonElement);
		}
	}
	
},{
	//Contains the convert lead form
	convertLeadForm : false,
	
	//contains the convert lead container
	convertLeadContainer : false,
	
	//contains all the checkbox elements of modules
	convertLeadModules : false,
	
	//constructor
	init : function() {
		this._super();
		Leads_Detail_Js.detailCurrentInstance = this;
	},
	
	/*
	 * function to disable the Convert Lead button
	 */
	disableConvertLeadButton : function(button) {
		jQuery(button).attr('disabled','disabled');
	},
	
	/*
	 * function to enable the Convert Lead button
	 */
	enableConvertLeadButton : function(button) {
		jQuery(button).removeAttr('disabled');
	},
	
	/*
	 * function to enable all the input and textarea elements
	 */
	removeDisableAttr : function(moduleBlock) {
		moduleBlock.find('input,textarea,select').removeAttr('disabled');
	},
	
	/*
	 * function to disable all the input and textarea elements
	 */
	addDisableAttr : function(moduleBlock) {
		moduleBlock.find('input,textarea,select').attr('disabled', 'disabled');
	},
	
	/*
	 * function to display the convert lead model
	 * @param: data used to show the model, currentElement.
	 */
	displayConvertLeadModel : function(data, buttonElement) {
		var instance = this;
		var errorElement = jQuery(data).find('#convertLeadError');
		if(errorElement.length != '0') {
			var errorMsg = errorElement.val();
			var errorTitle = jQuery(data).find('#convertLeadErrorTitle').val();
			var params = {
				title: errorTitle,
				text: errorMsg,
				addclass: "convertLeadNotify",
				width: '35%',
				pnotify_after_open: function(){
					instance.disableConvertLeadButton(buttonElement);
				},
				pnotify_after_close: function(){
					instance.enableConvertLeadButton(buttonElement);
				}
			}
			Vtiger_Helper_Js.showPnotify(params);
		} else {
			var callBackFunction = function(data){
				var editViewObj = Vtiger_Edit_Js.getInstance();
				jQuery(data).find('.fieldInfo').collapse({
					'parent': '#leadAccordion',
					'toggle' : false
				});
				app.showScrollBar(jQuery(data).find('#leadAccordion'), {'height':'350px'});
				editViewObj.registerBasicEvents(data);
				var checkBoxElements = instance.getConvertLeadModules();
				jQuery.each(checkBoxElements, function(index, element){
					instance.checkingModuleSelection(element);
				});
				instance.registerForReferenceField();
				instance.registerConvertLeadEvents();
				instance.getConvertLeadForm().validationEngine(app.validationEngineOptions);
				instance.registerConvertLeadSubmit();
			}
			app.showModalWindow(data,function(data){
				if(typeof callBackFunction == 'function'){
					callBackFunction(data);
				}
			},{
				'text-align' : 'left'
			});
		}
	},
	
	/*
	 * function to check which module is selected 
	 * to disable or enable all the elements with in the block
	 */
	checkingModuleSelection : function(element) {
		var instance = this;
		var module = jQuery(element).val();
		var moduleBlock = jQuery(element).closest('.accordion-group').find('#'+module+'_FieldInfo');
        //console.log(moduleBlock);
		if(jQuery(element).is(':checked')) {
			instance.removeDisableAttr(moduleBlock);
		} else {
			instance.addDisableAttr(moduleBlock);
		}
	},

	registerForReferenceField : function() {
		var container = this.getConvertLeadContainer();
		var referenceField = jQuery('.reference', container);
		if(referenceField.length > 0) {
			jQuery('#AccountsModule').attr('readonly', 'readonly');
		}
	},
	
	/*
	 * function to register Convert Lead Events
	 */
	registerConvertLeadEvents : function() {
		var container = this.getConvertLeadContainer();
		var instance = this;
		
		//Trigger Event to change the icon while shown and hidden the accordion body 
		container.on('hidden', '.accordion-body', function(e){
			var currentTarget = jQuery(e.currentTarget);
			currentTarget.closest('.convertLeadModules').find('.iconArrow').removeClass('icon-chevron-up').addClass('icon-chevron-down');
		}).on('shown', '.accordion-body', function(e){
			var currentTarget = jQuery(e.currentTarget);
			currentTarget.closest('.convertLeadModules').find('.iconArrow').removeClass('icon-chevron-down').addClass('icon-chevron-up');
		});
		
		//Trigger Event on click of Transfer related records modules
		container.on('click', '.transferModule', function(e){
			var currentTarget = jQuery(e.currentTarget);
			var module = currentTarget.val();
			var moduleBlock = jQuery('#'+module+'_FieldInfo');
			if(currentTarget.is(':checked')) {
				jQuery('#'+module+'Module').attr('checked','checked');
				moduleBlock.collapse('show');
				instance.removeDisableAttr(moduleBlock);
			}
		});
		
		//Trigger Event on click of the Modules selection to convert the lead 
		container.on('click','.convertLeadModuleSelection', function(e){
			var currentTarget = jQuery(e.currentTarget);
			var currentModuleName = currentTarget.val();
			var moduleBlock = currentTarget.closest('.accordion-group').find('#'+currentModuleName+'_FieldInfo');

			var currentTransferModuleElement = jQuery('#transfer'+currentModuleName);
			var otherTransferModuleElement = jQuery('input[name="transferModule"]').not(currentTransferModuleElement);
			var otherTransferModuleValue = jQuery(otherTransferModuleElement).val();
			var otherModuleElement = jQuery('#'+otherTransferModuleValue+'Module');
			
			if(currentTarget.is(':checked')) {
				moduleBlock.collapse('show');
				instance.removeDisableAttr(moduleBlock);
				if(!otherModuleElement.is(':checked')) {
					jQuery(currentTransferModuleElement).attr('checked', 'checked');
				}
			} else {
				moduleBlock.collapse('hide');
				instance.addDisableAttr(moduleBlock);
				jQuery(currentTransferModuleElement).removeAttr('checked');
				if(otherModuleElement.is(':checked')) {
					jQuery(otherTransferModuleElement).attr('checked','checked');
				}
			}
			e.stopImmediatePropagation();
		});
	},
	
	/*
	 * function to register Convert Lead Submit Event
	 */
	registerConvertLeadSubmit : function() {
		var thisInstance = this;
		var formElement = this.getConvertLeadForm();
		
		formElement.on('jqv.form.validating', function(e){
			var jQv = jQuery(e.currentTarget).data('jqv');
			//Remove the earlier validated fields from history so that it wont count disabled fields 
			jQv.InvalidFields = [];
		});
		
		//Convert Lead Form Submission
		formElement.on('submit',function(e) {
			var convertLeadModuleElements = thisInstance.getConvertLeadModules();
			var moduleArray = [];
			var contactModel = formElement.find('#ContactsModule');
			var accountModel = formElement.find('#AccountsModule');
			
			//If the validation fails in the hidden Block, we should show that Block with error.
			var invalidFields = formElement.data('jqv').InvalidFields;
			if(invalidFields.length > 0) {
				var fieldElement = invalidFields[0];
				var moduleBlock = jQuery(fieldElement).closest('div.accordion-body');
				moduleBlock.collapse('show');
				e.preventDefault();
				return;
			}
			
			jQuery.each(convertLeadModuleElements, function(index, element) {
				if(jQuery(element).is(':checked')) {
					moduleArray.push(jQuery(element).val());
				}
			});
			formElement.find('input[name="modules"]').val(JSON.stringify(moduleArray));
			
			var contactElement = contactModel.length;
			var organizationElement = accountModel.length;
			
			if(contactElement != '0' && organizationElement != '0') {
				if(jQuery.inArray('Accounts',moduleArray) == -1 && jQuery.inArray('Contacts',moduleArray) == -1) {
					alert(app.vtranslate('JS_SELECT_ORGANIZATION_OR_CONTACT_TO_CONVERT_LEAD'));
					e.preventDefault();
				} 
			} else if(organizationElement != '0') {
				if(jQuery.inArray('Accounts',moduleArray) == -1) {
					alert(app.vtranslate('JS_SELECT_ORGANIZATION'));
					e.preventDefault();
				}
			} else if(contactElement != '0') {
				if(jQuery.inArray('Contacts',moduleArray) == -1) {
					alert(app.vtranslate('JS_SELECT_CONTACTS'));
					e.preventDefault();
				}
			}
            /* var progressIndicatorElement = jQuery.progressIndicator({
            'message' : '正在处理请稍后',
            'position' : 'html',
            'blockInfo' : {'enabled' : true}
            }); */
		});
	},
	
	/*
	 * function to get all the checkboxes which are representing the modules selection
	 */
	getConvertLeadModules : function() {
		var container = this.getConvertLeadContainer();
		if(this.convertLeadModules == false) {
			this.convertLeadModules = jQuery('.convertLeadModuleSelection', container);
		}
		return this.convertLeadModules;
	},
	
	/*
	 * function to get Convert Lead Form
	 */
	getConvertLeadForm : function() {
		if(this.convertLeadForm == false) {
			this.convertLeadForm = jQuery('#convertLeadForm');
		}
		return this.convertLeadForm;
	},
	
	/*
	 * function to get Convert Lead Container
	 */
	getConvertLeadContainer : function() {
		if(this.convertLeadContainer == false) {
			this.convertLeadContainer = jQuery('#leadAccordion');
		}
		return this.convertLeadContainer;
	},
    /**
     * 作废按钮功能
     */
    getCancel:function(){
        var thisInstance = this;
        $('.stagesubmit').on('click',function(){
            var act=$(this).data('act');
            var message=act=='LBL_RELATED_LEAD'?'确定要关联客户吗?':(act=='LBL_cancelled_LEAD'?"确定要作废该商机吗?":"确定要激添该商机吗??");
            var msg={
                'message':message,
                "width":"400px",
                'act':act
            };
            console.log(act);
            var isoverprotectnum = $("input[name='isoverprotectnum']").val();
            if(act=='LBL_RELATED_LEAD'&&isoverprotectnum!=undefined){
                alert('已超过客户保护数量，不允许关联客户');
                return;
            }
            thisInstance.showConfirmationBox(msg).then(function(e){
                //alert($('#recordId').val());return;
                var params={};
                params['record'] = $('#recordId').val();
                params['action'] = 'ChangeAjax';
                params['module'] = 'Leads';
                params['voidreason'] = $('#voidreason').val();
                params['act'] =act;
                //params['userid']=$('select[name="userid"]').val();
                params['accountid']=$('input[name="account_id"]').val();
                AppConnector.request(params).then(
                    function(data) {
                        window.location.reload(true);
                    },
                    function(error,err){
                        window.location.reload(true);
                    }
                );
            },function(error, err) {});
            if($(this).data('act')=='LBL_cancelled_LEAD'){
                $('.modal-body').append('<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0;"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">作废原因:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><textarea id="voidreason" class="span11 "></textarea></span></div></td></tr></tbody></table>');
            }else if($(this).data('act')=='LBL_RELATED_LEAD'){
               /* var params={};
                params['record'] = $('#recordId').val();
                params['action'] = 'ChangeAjax';
                params['module'] = 'Leads';
                //params['voidreason'] = $('#voidreason').val();
                params['act'] ='LBL_Accounts_LEAD';
                $('.btn-success').addClass('hide');
                AppConnector.request(params).then(
                    function(data) {
                        if(data.success==true){
                            var usersel=''
                            var usersel='<select class="chzn-select" name="userid" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]">';
                            $.each(data.users,function(i,val){
                                usersel+='<option value="'+val.id+'"'+(val.id==data.data.id?"selected":'')+'>'+val.last_name+'</option>';
                            });
                            usersel+='</select>';
                            var usersel2='<div class="row-fluid"><span class="span10"><input name="popupReferenceModule" type="hidden" value="Accounts" /><input name="account_id" type="hidden" value="" data-multiple="0" class="sourceField" data-displayvalue="" data-fieldinfo="" /><div class="row-fluid input-prepend input-append"><span class="add-on clearReferenceSelection cursorPointer"><i id="Invoice_editView_fieldName_account_id_clear" class="icon-remove-sign" title="清除"></i></span><input id="account_id_display" name="account_id_display" type="text" class=" span7 	marginLeftZero autoComplete" value="" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"data-fieldinfo="" placeholder="查找.."/><span class="add-on relatedPopup cursorPointer"><i id="Invoice_editView_fieldName_account_id_select" class="icon-search relatedPopup" title="选择" ></i></span></div> </span></div>';
                            ///$('.modal-body').append('<table class="tabless" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:45px;"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">新负责人</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10">'+usersel+'</span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">公司名称</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10">'+usersel2+'</span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">保护模式</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10">'+data.data.category+'</span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">客户等级</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10">'+data.data.rank+'</span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">原负责人</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10">'+data.data.name+'</span></div></td></tr></tbody></table>');
                            $('.modal-body').append('<table class="tabless" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:45px;"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">公司名称<font color="red">*</font></label></td><td class="fieldValue medium" colspan="3">'+usersel2+'</td></tr><tr></tbody></table>');
                            $('.chzn-select').chosen();
                            $('.btn-success').removeClass('hide');
                            function referenceModulePopupRegisterEvent(container){

                                container.on("click",'.relatedPopup',function(e){
                                    thisInstance.openPopUp(e);
                                });
                                container.find('.referenceModulesList').chosen().change(function(e){
                                    var element = jQuery(e.currentTarget);
                                    var closestTD = element.closest('td').next();
                                    var popupReferenceModule = element.val();
                                    var referenceModuleElement = jQuery('input[name="popupReferenceModule"]', closestTD);
                                    var prevSelectedReferenceModule = referenceModuleElement.val();
                                    referenceModuleElement.val(popupReferenceModule);

                                    //If Reference module is changed then we should clear the previous value
                                    if(prevSelectedReferenceModule != popupReferenceModule) {
                                        closestTD.find('.clearReferenceSelection').trigger('click');
                                    }
                                });
                            }
                            referenceModulePopupRegisterEvent($(".tabless"));
                        }else{
                            $('.modal-body').append('<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0;"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-left marginRight10px">'+data.msg+'</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"></span></div></td></tr></tbody></table>');
                            $('.btn-success').remove();
                        }
                        //window.location.reload(true);
                    },
                    function(error,err){
                        //window.location.reload(true);
                    }
                );*/
                var usersel=''
                var usersel2='<div class="row-fluid"><span class="span10"><input name="popupReferenceModule" type="hidden" value="Accounts" /><input name="account_id" type="hidden" value="" data-multiple="0" class="sourceField" data-displayvalue="" data-fieldinfo="" /><div class="row-fluid input-prepend input-append"><span class="add-on clearReferenceSelection cursorPointer"><i id="Invoice_editView_fieldName_account_id_clear" class="icon-remove-sign" title="清除"></i></span><input id="account_id_display" name="account_id_display" type="text" class=" span7 	marginLeftZero autoComplete" value="" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"data-fieldinfo="" placeholder="查找.."/><span class="add-on relatedPopup cursorPointer"><i id="Invoice_editView_fieldName_account_id_select" class="icon-search relatedPopup" title="选择" ></i></span></div> </span></div>';
                $('.modal-body').append('<table class="tabless" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:45px;"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">公司名称<font color="red">*</font></label></td><td class="fieldValue medium" colspan="3">'+usersel2+'</td></tr><tr></tbody></table>');
                $('.chzn-select').chosen();
                $('.btn-success').removeClass('hide');
                function referenceModulePopupRegisterEvent(container){

                    container.on("click",'.relatedPopup',function(e){
                        thisInstance.openPopUp(e);
                    });
                    container.find('.referenceModulesList').chosen().change(function(e){
                        var element = jQuery(e.currentTarget);
                        var closestTD = element.closest('td').next();
                        var popupReferenceModule = element.val();
                        var referenceModuleElement = jQuery('input[name="popupReferenceModule"]', closestTD);
                        var prevSelectedReferenceModule = referenceModuleElement.val();
                        referenceModuleElement.val(popupReferenceModule);

                        //If Reference module is changed then we should clear the previous value
                        if(prevSelectedReferenceModule != popupReferenceModule) {
                            closestTD.find('.clearReferenceSelection').trigger('click');
                        }
                    });
                }
                referenceModulePopupRegisterEvent($(".tabless"));
            }
        });
    },
    //edit.js中拿过来的
    checkedform:function(data){
        var thisstance=this;
        console.log(data);
        if(data['act']=='LBL_RELATED_LEAD'){
            if($('input[name="account_id"]').val()>0)
            {
                var a=confirm("确定要强制关联该客户吗???");
                if(a==true){
                    var a=confirm("确定要强制关联该客户吗2???");
                    if(a==true){
                        var a=confirm("确定要强制关联该客户吗3???");
                        if(a==true){
                            return true;
                        }else{
                            window.location.reload(true);
                        }
                    }else{
                        window.location.reload(true);
                    }
                }else{
                    window.location.reload(true);
                }
            }else{
                $('input[name="account_id_display"]').focus();
                $('input[name="account_id_display"]').attr('data-content','<font color="red">必填项不能为空</font>');
                $('input[name="account_id_display"]').attr('data-placement','bottom');
                $('input[name="account_id_display"]').popover("show");
                $('.popover-content').css({"color":"red","fontSize":"12px"});
                $('.popover').css('z-index',1000010);
                setTimeout("$('input[name=\"account_id_display\"]').popover('destroy')",2000);
                return false;
            }
        }else if(data['act']=='LBL_cancelled_LEAD'){
            if($('#voidreason').val()=='')
            {
                $('#voidreason').focus();
                $('#voidreason').attr('data-content','<font color="red">必填项不能为空</font>');
                $('#voidreason').attr('data-placement','bottom');
                $('#voidreason').popover("show");
                $('.popover-content').css({"color":"red","fontSize":"12px"});
                $('.popover').css('z-index',1000010);
                setTimeout("$('#voidreason').popover('destroy')",2000);
                return false;
            }else{
                return true;
            }
        }else{
            return true;
        }

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
                if(thisstance.checkedform(data)){
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
    getPopUpParams : function(container) {
        var params = {};
        var sourceModule = app.getModuleName();
        var popupReferenceModule = jQuery('input[name="popupReferenceModule"]',container).val();
        var sourceFieldElement = jQuery('input[class="sourceField"]',container);
        var sourceField = sourceFieldElement.attr('name');
        var sourceRecordElement = jQuery('input[name="record"]');
        var sourceRecordId = '';
        if(sourceRecordElement.length > 0) {
            sourceRecordId = sourceRecordElement.val();
        }

        var isMultiple = false;
        if(sourceFieldElement.data('multiple') == true){
            isMultiple = true;
        }

        var params = {
            'module' : popupReferenceModule,
            'src_module' : sourceModule,
            'src_field' : sourceField,
            'src_record' : sourceRecordId
        }

        if(isMultiple) {
            params.multi_select = true ;
        }
        return params;
    },
    openPopUp : function(e){
        var thisInstance = this;
        var parentElem = jQuery(e.target).closest('td');

        var params = this.getPopUpParams(parentElem);

        var isMultiple = false;
        if(params.multi_select) {
            isMultiple = true;
        }

        var sourceFieldElement = jQuery('input[class="sourceField"]',parentElem);

        var prePopupOpenEvent = jQuery.Event(Vtiger_Edit_Js.preReferencePopUpOpenEvent);
        sourceFieldElement.trigger(prePopupOpenEvent);

        if(prePopupOpenEvent.isDefaultPrevented()) {
            return ;
        }

        var popupInstance =Vtiger_Popup_Js.getInstance();
        popupInstance.show(params,function(data){
            var responseData = JSON.parse(data);
            var dataList = new Array();
            var idList = new Array();
            idList['id']=new Array();
            idList['name']=new Array();
            for(var id in responseData){
                var data = {
                    'name' : responseData[id].name,
                    'id' : id
                }
                dataList.push(data);
                if(!isMultiple) {
                    thisInstance.setReferenceFieldValue(parentElem, data);
                }else{
                    idList['id'].push(id);
                    idList['name'].push(responseData[id].name);
                }
            }

            if(isMultiple) {
                thisInstance.setMultiReferenceFieldValue(parentElem, idList);
                //sourceFieldElement.trigger(Vtiger_Edit_Js.refrenceMultiSelectionEvent,{'data':dataList});
            }
            sourceFieldElement.trigger(Vtiger_Edit_Js.postReferenceSelectionEvent,{'data':responseData});
        });
    },
    setReferenceFieldValue : function(container, params) {
        var sourceField = container.find('input[class="sourceField"]').attr('name');
        var fieldElement = container.find('input[name="'+sourceField+'"]');
        var sourceFieldDisplay = sourceField+"_display";
        var fieldDisplayElement = container.find('input[name="'+sourceFieldDisplay+'"]');
        var popupReferenceModule = container.find('input[name="popupReferenceModule"]').val();

        var selectedName = params.name;
        var id = params.id;
        var params2={};
        params2['record'] = $('#recordId').val();
        params2['action'] = 'BasicAjax';
        params2['module'] = 'Leads';
        params2['mode'] = 'isOverProtect';
        params2['accountid']=id;
        AppConnector.request(params2).then(
            function(data) {
                console.log(data);
                if(data.success){

                    fieldElement.val(id)
                    fieldDisplayElement.val(selectedName);
                    console.log(selectedName);
                    fieldElement.trigger(Vtiger_Edit_Js.referenceSelectionEvent, {'source_module' : popupReferenceModule, 'record' : id, 'selectedName' : selectedName});

                    fieldDisplayElement.validationEngine('closePrompt',fieldDisplayElement);
                }else{
                    alert(data.msg);
                    return;
                }
            },
            function(error,err){
                window.location.reload(true);
            }
        );

    },
    initData:function(){
        var cluefollowstatus= $("input[name='cluefollowstatus']").val();
        $("#cluefollowstatus").html(cluefollowstatus);

    },
    //edit.js 中拿过来的
    registerEvents:function(){
        this._super();
        this.getCancel();
        this.initData();
    }
	
});