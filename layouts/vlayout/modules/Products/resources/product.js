/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

 <table class="table table-bordered table-striped blockContainer showInlineTable ">
		<thead>	<tr><th class="blockHeader" colspan="5">多规格</th></tr></thead>
		<tbody>
		<tr><td>规格名称</td><td>单价</td><td>成本价格</td><td>规则</td><td>操作</td></tr>
		<tr>
			<td><input type="text" name="standardname[]"></td>
			<td><input type="text" name="singleprice[]"></td>
			<td><input type="text" name="realyprice[]"></td>
			<td><input type="text" name="rule[]"></td>
			<td>
			<div class="btn-toolbar">
			<span class="btn-group">
			<button type="button" class="btn addProduct addButton">增加规格<i class="icon-plus"></i><strong></strong></button>
			 <button type="button" class="btn removeProduct">移除规格<i class="icon-minus"></i><strong></strong></button>
			 </span>
			 </div>
			 </td>
		</tr>
		</tbody>
</table>
 
 
 
 
Vtiger_Edit_Js("Products_Edit_Js",{
	
},{
	baseCurrency : '',
	
	baseCurrencyName : '',
	//Container which stores the multi currency element
	multiCurrencyContainer : false,
	
	//Container which stores unit price
	unitPrice : false,
	
	/**
	 * Function to get unit price
	 */
	getUnitPrice : function(){
		if(this.unitPrice == false) {
			this.unitPrice = jQuery('input.unitPrice',this.getForm());
		}
		return this.unitPrice;
	},
	
	/**
	 * Function to get more currencies container
	 */
	getMoreCurrenciesContainer : function(){
		if(this.multiCurrencyContainer == false) {
			this.multiCurrencyContainer = jQuery('.multiCurrencyEditUI');
		}
		return this.multiCurrencyContainer;
	},
	
	/**
	 * Function which aligns data just below global search element
	 */
	alignBelowUnitPrice : function(dataToAlign) {
		var parentElem = jQuery('input[name="unit_price"]',this.getForm());
		dataToAlign.position({
			'of' : parentElem,
			'my': "left top",
			'at': "left bottom",
			'collision' : 'flip'
		});
		return this;
	},
	
	/**
	 * Function to get current Element
	 */
	getCurrentElem : function(e){
		return jQuery(e.currentTarget);
	},
	/**
	 *Function to register events for taxes
	 */
	registerEventForTaxes : function(){
		var thisInstance = this;
		var formElem = this.getForm();
		jQuery('.taxes').on('change',function(e){
			var elem = thisInstance.getCurrentElem(e);
			var taxBox  = elem.data('taxName');
			if(elem.is(':checked')) {
				jQuery('input[name='+taxBox+']',formElem).removeClass('hide').show();
			}else{
				jQuery('input[name='+taxBox+']',formElem).addClass('hide');
			}

		});
		return this;
	},
	
	/**
	 * Function to register event for enabling base currency on radio button clicked
	 */
	registerEventForEnableBaseCurrency : function(){
		var container = this.getMoreCurrenciesContainer();
		var thisInstance = this;
		jQuery(container).on('change','.baseCurrency',function(e){
			var elem = thisInstance.getCurrentElem(e);
			var parentElem = elem.closest('tr');
			if(elem.is(':checked')) {
				var convertedPrice = jQuery('.convertedPrice',parentElem).val();
				thisInstance.baseCurrencyName = parentElem.data('currencyId');
				thisInstance.baseCurrency = convertedPrice;
			}
		});
		return this;
	},
	
	/**
	 * Function to register event for reseting the currencies
	 */
	registerEventForResetCurrency : function(){
		var container = this.getMoreCurrenciesContainer();
		var thisInstance = this;
		jQuery(container).on('click','.currencyReset',function(e){
			var parentElem = thisInstance.getCurrentElem(e).closest('tr');
			var unitPriceFieldData = thisInstance.getUnitPrice().data();
			var unitPrice = thisInstance.getDataBaseFormatUnitPrice();
			var groupSeperator = unitPriceFieldData.groupSeperator;
			var re = new RegExp(groupSeperator, 'g');
			unitPrice = unitPrice.replace(re, '');
			var conversionRate = jQuery('.conversionRate',parentElem).val();
			var price = parseFloat(unitPrice) * parseFloat(conversionRate);
			var userPreferredDecimalPlaces = unitPriceFieldData.numberOfDecimalPlaces;
			price = price.toFixed(userPreferredDecimalPlaces);
			var calculatedPrice = price.toString().replace('.',unitPriceFieldData.decimalSeperator);
			jQuery('.convertedPrice',parentElem).val(calculatedPrice);
		});
		return this;
	},
	
	/**
	 *  Function to return stripped unit price
	 */
		getDataBaseFormatUnitPrice : function(){
			var field = this.getUnitPrice();
			var unitPrice = field.val();
			if(unitPrice == ''){
				unitPrice = 0;
			}else{
				var fieldData = field.data();
				var strippedValue = unitPrice.replace(fieldData.groupSeperator, '');
				strippedValue = strippedValue.replace(fieldData.decimalSeperator, '.');
				unitPrice = strippedValue;
			}
			return unitPrice;
		},
        
    calculateConversionRate : function() {
        var container = this.getMoreCurrenciesContainer();
        var baseCurrencyRow = container.find('.baseCurrency').filter(':checked').closest('tr');
        var baseCurrencyConvestationRate = baseCurrencyRow.find('.conversionRate');
        //if basecurrency has conversation rate as 1 then you dont have caliculate conversation rate
        if(baseCurrencyConvestationRate.val() == "1") {
            return;
        }
        var baseCurrencyRatePrevValue = baseCurrencyConvestationRate.val();
        
        container.find('.conversionRate').each(function(key,domElement) {
            var element = jQuery(domElement);
            if(!element.is(baseCurrencyConvestationRate)){
                var prevValue = element.val();
                element.val((prevValue/baseCurrencyRatePrevValue));
            }
        });
        baseCurrencyConvestationRate.val("1");
    },
	/**
	 * Function to register event for enabling currency on checkbox checked
	 */
	
	registerEventForEnableCurrency : function(){
		var container = this.getMoreCurrenciesContainer();
		var thisInstance = this;
		jQuery(container).on('change','.enableCurrency',function(e){
			var elem = thisInstance.getCurrentElem(e);
			var parentRow = elem.closest('tr');
			
			if(elem.is(':checked')) {
				elem.attr('checked',"checked");
				var conversionRate = jQuery('.conversionRate',parentRow).val();
				var unitPriceFieldData = thisInstance.getUnitPrice().data();
				var unitPrice = thisInstance.getDataBaseFormatUnitPrice();
				var groupSeperator = unitPriceFieldData.groupSeperator;
				var re = new RegExp(groupSeperator, 'g');
				unitPrice = unitPrice.replace(re, '');
				var price = parseFloat(unitPrice)*parseFloat(conversionRate);
				jQuery('input',parentRow).attr('disabled', true).removeAttr('disabled');
				jQuery('button.currencyReset', parentRow).attr('disabled', true).removeAttr('disabled');
				var userPreferredDecimalPlaces = unitPriceFieldData.numberOfDecimalPlaces;
				price = price.toFixed(userPreferredDecimalPlaces);
				var calculatedPrice = price.toString().replace('.',unitPriceFieldData.decimalSeperator);
				jQuery('input.convertedPrice',parentRow).val(calculatedPrice)
			}else{
				jQuery('input',parentRow).attr('disabled', true);
				jQuery('input.enableCurrency',parentRow).removeAttr('disabled');
				jQuery('button.currencyReset', parentRow).attr('disabled', 'disabled');
				var baseCurrency = jQuery('.baseCurrency', parentRow);
				if(baseCurrency.is(':checked')){
					baseCurrency.removeAttr('checked');
				}
			}
		})
		return this;
	},
	
	/**
	 * Function to get more currencies UI
	 */
	getMoreCurrenciesUI : function(){
		var aDeferred = jQuery.Deferred();
		var moduleName = app.getModuleName();
		var baseCurrency = jQuery('input[name="base_currency"]').val();
		var recordId = jQuery('input[name="record"]').val();
		var moreCurrenciesContainer = jQuery('#moreCurrenciesContainer');
		moreCurrenciesUi = moreCurrenciesContainer.find('.multiCurrencyEditUI');
		var moreCurrenciesUi;
			
		if(moreCurrenciesUi.length == 0){
			var moreCurrenciesParams = {
				'module' : moduleName,
				'view' : "MoreCurrenciesList",
				'currency' : baseCurrency,
				'record' : recordId
			}

			AppConnector.request(moreCurrenciesParams).then(
				function(data){
					moreCurrenciesContainer.html(data);
					aDeferred.resolve(data);
				},
				function(textStatus, errorThrown){
					aDeferred.reject(textStatus, errorThrown);
				}
			);
		} else{
			aDeferred.resolve();
		}
		return aDeferred.promise();
	},
	
	/*
	 * function to register events for more currencies link
	 */
	registerEventForMoreCurrencies : function(){
		var thisInstance = this;
		var form = this.getForm();
		jQuery('#moreCurrencies').on('click',function(e){
			var progressInstance = jQuery.progressIndicator();
			thisInstance.getMoreCurrenciesUI().then(function(data){
				var moreCurrenciesUi;
				moreCurrenciesUi = jQuery('#moreCurrenciesContainer').find('.multiCurrencyEditUI');
				if(moreCurrenciesUi.length > 0){
					moreCurrenciesUi = moreCurrenciesUi.clone(true,true);
					progressInstance.hide();
					var css = {'text-align' : 'left','width':'65%'};
					var callback = function(data){
						var params = app.validationEngineOptions;
						var form = data.find('#currencyContainer');
						params.onValidationComplete = function(form, valid){
							if(valid) {
								thisInstance.saveCurrencies();
							}
							return false;
						}
						form.validationEngine(params);
						app.showScrollBar(data.find('.currencyContent'), {'height':'400px'});
						thisInstance.baseCurrency = thisInstance.getUnitPrice().val();
						var multiCurrencyEditUI = jQuery('.multiCurrencyEditUI');
						thisInstance.multiCurrencyContainer = multiCurrencyEditUI;
                        thisInstance.calculateConversionRate();
						thisInstance.registerEventForEnableCurrency().registerEventForEnableBaseCurrency()
											.registerEventForResetCurrency().triggerForBaseCurrencyCalc();
					}
					var contentInsideForm = moreCurrenciesUi.find('.multiCurrencyContainer').html();
					moreCurrenciesUi.find('.multiCurrencyContainer').remove();
					var form = '<form id="currencyContainer"></form>'
					jQuery(form).insertAfter(moreCurrenciesUi.find('.modal-header'));
					moreCurrenciesUi.find('form').html(contentInsideForm);

					var modalWindowParams = {
						data : moreCurrenciesUi,
						css : css,
						cb : callback
					}
					app.showModalWindow(modalWindowParams)
				}
			})
		});
	},
	/**
	 * Function to calculate base currency price value if unit
	 * present on click of more currencies
	 */
	triggerForBaseCurrencyCalc : function(){
		var multiCurrencyEditUI = this.getMoreCurrenciesContainer();
		var baseCurrency = multiCurrencyEditUI.find('.enableCurrency');
		jQuery.each(baseCurrency,function(key,val){
			if(jQuery(val).is(':checked')){
				var baseCurrencyRow = jQuery(val).closest('tr');
				baseCurrencyRow.find('.currencyReset').trigger('click');
			}else{
                var baseCurrencyRow = jQuery(val).closest('tr');
                baseCurrencyRow.find('.convertedPrice').val('');
            }
		})
	},
	
	/**
	 * Function to register onchange event for unit price
	 */
	registerEventForUnitPrice : function(){
		var thisInstance = this;
		var unitPrice = this.getUnitPrice();
		unitPrice.on('change',function(){
			thisInstance.triggerForBaseCurrencyCalc();
		})
	},

	registerRecordPreSaveEvent : function(form) {
		var thisInstance = this;
		if(typeof form == 'undefined') {
			form = this.getForm();
		}

		form.on(Vtiger_Edit_Js.recordPreSave, function(e, data) {
			var multiCurrencyContent = jQuery('#moreCurrenciesContainer').find('.currencyContent');
			var unitPrice = thisInstance.getUnitPrice();
			if((multiCurrencyContent.length < 1) && (unitPrice.length > 0)){
				e.preventDefault();
				thisInstance.getMoreCurrenciesUI().then(function(data){
					thisInstance.preSaveConfigOfForm(form);
					form.submit();
				})
			}else if(multiCurrencyContent.length > 0){
				thisInstance.preSaveConfigOfForm(form);
			}
		})
	},
	
	/**
	 * Function to handle settings before save of record
	 */
	preSaveConfigOfForm : function(form) {
		var unitPrice = this.getUnitPrice();
		if(unitPrice.length > 0){
			var unitPriceValue = unitPrice.val();
			var baseCurrencyName = form.find('[name="base_currency"]').val();
			form.find('[name="'+ baseCurrencyName +'"]').val(unitPriceValue);
			form.find('#requstedUnitPrice').attr('name',baseCurrencyName).val(unitPriceValue);
		}
	},
	registerEventForCkEditor : function(){
		var form = this.getForm();
		var noteContentElement = form.find('[name="notecontent"],[name="solution"]');
		if(noteContentElement.length > 0){
			var ckEditorInstance = new Vtiger_CkEditor_Js();
			//ckEditorInstance.loadCkEditor("Products_editView_fieldName_solution");
			ckEditorInstance.loadCkEditor("Products_editView_fieldName_notecontent");
		}

	},
	saveCurrencies : function(){
		var thisInstance = this;
		var errorMessage,params;
		var form = jQuery('#currencyContainer');
		var editViewForm = thisInstance.getForm();
		var modalContainer = jQuery('#globalmodal');
		var enabledBaseCurrency = modalContainer.find('.enableCurrency').filter(':checked');
		if(enabledBaseCurrency.length < 1){
			errorMessage = app.vtranslate('JS_PLEASE_SELECT_BASE_CURRENCY_FOR_PRODUCT');
			params = {
				text: errorMessage,
				'type':'error'
			};
			Vtiger_Helper_Js.showMessage(params);
			form.removeData('submit');
			return;
		}
		enabledBaseCurrency.attr('checked',"checked");
		modalContainer.find('.enableCurrency').filter(":not(:checked)").removeAttr('checked');
		var selectedBaseCurrency = modalContainer.find('.baseCurrency').filter(':checked');
		if(selectedBaseCurrency.length < 1){
			errorMessage = app.vtranslate('JS_PLEASE_ENABLE_BASE_CURRENCY_FOR_PRODUCT');
			params = {
				text: errorMessage,
				'type':'error'
			};
			Vtiger_Helper_Js.showMessage(params);
			form.removeData('submit');
			return;
		}
		selectedBaseCurrency.attr('checked',"checked");
		modalContainer.find('.baseCurrency').filter(":not(:checked)").removeAttr('checked');
		var parentElem = selectedBaseCurrency.closest('tr');
		var convertedPrice = jQuery('.convertedPrice',parentElem).val();
		thisInstance.baseCurrencyName = parentElem.data('currencyId');
		thisInstance.baseCurrency = convertedPrice;
		
		thisInstance.getUnitPrice().val(thisInstance.baseCurrency);
		jQuery('input[name="base_currency"]',editViewForm).val(thisInstance.baseCurrencyName);
		
		var savedValuesOfMultiCurrency = modalContainer.find('.currencyContent').html();
		var moreCurrenciesContainer = jQuery('#moreCurrenciesContainer');
		moreCurrenciesContainer.find('.currencyContent').html(savedValuesOfMultiCurrency);
		app.hideModalWindow();
	},
	
	registerSubmitEvent: function() {
		var editViewForm = this.getForm();

		editViewForm.submit(function(e){
			if((editViewForm.find('[name="existingImages"]').length >= 1) || (editViewForm.find('[name="imagename[]"]').length > 1)){
				jQuery.fn.MultiFile.disableEmpty(); // before submiting the form - See more at: http://www.fyneworks.com/jquery/multiple-file-upload/#sthash.UTGHmNv3.dpuf
			}
			//Form should submit only once for multiple clicks also
			if(typeof editViewForm.data('submit') != "undefined") {
				return false;
			} else {
				var module = jQuery(e.currentTarget).find('[name="module"]').val();
				if(editViewForm.validationEngine('validate')) {
					//Once the form is submiting add data attribute to that form element
					editViewForm.data('submit', 'true');
						//on submit form trigger the recordPreSave event
						var recordPreSaveEvent = jQuery.Event(Vtiger_Edit_Js.recordPreSave);
						editViewForm.trigger(recordPreSaveEvent, {'value' : 'edit'});
						if(recordPreSaveEvent.isDefaultPrevented()) {
							//If duplicate record validation fails, form should submit again
							editViewForm.removeData('submit');
							e.preventDefault();
						}
				} else {
					//If validation fails, form should submit again
					editViewForm.removeData('submit');
					// to avoid hiding of error message under the fixed nav bar
					app.formAlignmentAfterValidation(editViewForm);
				}
			}
		});
	},
	//多规格添加
	Polyproduct:function(){
		var that=this;
		$('input[name="polytype"]').on('click',function(){
			if($(this).is(':checked')){
				var tr=that.addNewprotype();
				$(this).closest('table').append(tr);	
			}else{
				$('.typeadd').remove();
			}
		});

	},
	
	Typeschage:function(){
		$('textarea[name="description"]').change(function(){
			var type=$(this).val().split('\n');
			//for(i in type){ type[i]；
				/*$('.childtype option').each(function(){if($.inArray($(this).text(),type)==-1){$(this).remove();}	});*/
				$('.childtype').each(function(){
					/* if($.inArray($(this).text(),type)==-1){$(this).remove();} */
					//获取原有选中的值
					var val=$(this).attr('data-val').split(',');
					//清空
					$(this).empty();
					for(i in type){
						var chose='';
						if($.inArray(type[i],val)!=-1){
							chose='selected';
						}
						$(this).append("<option "+chose+" value="+type[i]+">"+type[i]+"</option>");
					}
				});
			//};
		});
	},
	
	
	Selecttab:function(){
		/*$('textarea[name="description"]').change(function(){if($('.typeadd').length>0){//alert(2);}	});*/
		//var type=$('textarea[name="description"]').val().split('\n');//$('#mt option').remove(); 
		//for(i in type){ $('#mt').append('<option value="'+type[i]+'">'+type[i]+'</option>')}
		var that=this;
		//多规格添加
		$('.addProduct').live('click',function(){
			var tr=that.addNewprotype();
			$(this).closest('table').append(tr);
		});
		//规格移除操作
		$('.removeProduct').live('click',function(){
			var tr=$(this).closest('tr');
			tr.prev('tr').remove();
			tr.remove();
			if($('.typeadd').length<1){
				$('input[name="polytype"]').attr('checked',false);
			}
		});
		
		
		$('.childtype').live('change',function(){
			$(this).attr('data-val',$(this).val());
		});
	},
	//获取产品功能或规格分类
	addNewprotype:function(){
		var type=$('textarea[name="description"]').val().split('\n');
		if($('textarea[name="description"]').val().length<1){
			alert('当前产品无功能信息！');
			$('input[name="polytype"]').attr('checked',false);
			return false;
		}
		
		//$('#mt option').remove(); 
		var op='';
		for(i in type){ op+='<option value="'+type[i]+'">'+type[i]+'</option>'};
		var tr='<tr class="typeadd"><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span>规格名称</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="text" class="input-large " data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="polytype[]" value="" ></span></div></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span>单价</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="text" class="input-large " data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="childprice[]" value="" ></span></div></td></tr><tr class="typeadd"><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span>参数</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><select multiple  class="childtype" data-val="" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="polytype[]"  >'+op+'</select></span></div></td><td class="fieldLabel medium"></td><td class="fieldValue medium"><div class="btn-toolbar"><span class="btn-group"><button type="button" class="btn addProduct addButton">增加规格<i class="icon-plus"></i><strong></strong></button> <button type="button" class="btn removeProduct">移除规格<i class="icon-minus"></i><strong></strong></button>     </span></div></td></tr>';
		return tr;
	},
	
	registerEvents : function(){
		this._super();
		this.registerEventForMoreCurrencies();
		this.registerEventForTaxes();
		this.registerEventForUnitPrice();
		//this.addNewprotype();
		//this.registerRecordPreSaveEvent();
		this.Polyproduct();
		this.Selecttab();
		this.Typeschage();
		this.registerEventForCkEditor();
	}
})