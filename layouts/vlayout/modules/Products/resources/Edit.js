/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

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
			//ckEditorInstance.loadCkEditor(noteContentElement);
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
	//wangbin 2015-07-01 产品多规格 是否套餐选择
	 
	 //多规格勾选按钮
	 clickmultistandard : function(){ 
		 $("input[name='polytype']").live('click',function(){
			if($(this).is(':checked')){
				var multistandardtable = '<table id="polytypetable" class="table table-bordered table-striped blockContainer showInlineTable "> <thead>	<tr><th class="blockHeader" colspan="5">多规格</th></tr></thead> <tbody> <tr><td>规格名称</td><td>单价</td><td>成本价格</td><td>规格</td><td>操作</td></tr> <tr> <td><span class="redColor">*</span><input data-validation-engine="validate[required]" type="text" name="standardname[]"></td> <td><span class="redColor">*</span><div class="input-prepend"> <span class="add-on">￥</span> <input data-validation-engine="validate[required]" name="singleprice[]" class="span8"  type="text" placeholder="请输入单价"> </div></td> <td><span class="redColor">*</span><div class="input-prepend"> <span class="add-on">￥</span> <input name="realyprice[]" data-validation-engine="validate[required]" class="span8"  type="text" placeholder="请输入成本价格"> </div></td> <td><input type="text" name="rule[]"></td> <td> <div class="btn-toolbar"> <span class="btn-group"> <button type="button" class="btn addstandard addButton">增加规格<i class="icon-plus"></i><strong></strong></button> <button type="button" class="btn removestandard">移除规格<i class="icon-minus"></i><strong></strong></button> </span> </div> </td> </tr> </tbody> </table>';
				$(this).closest("table").after(multistandardtable);
			}else{
				$("#polytypetable").remove();
			}
		})
	 },
	 //添加删除多规格
	 changestandard : function(){
		 $(".addstandard").live('click',function(){
			 $(this).closest('tr').after('<tr> <td><span class="redColor">*</span><input data-validation-engine="validate[required]" type="text" name="standardname[]"></td> <td><span class="redColor">*</span><div class="input-prepend"> <span class="add-on">￥</span> <input data-validation-engine="validate[required]" name="singleprice[]" class="span8"  type="text" placeholder="请输入单价"> </div></td> <td><span class="redColor">*</span><div class="input-prepend"> <span class="add-on">￥</span> <input name="realyprice[]" data-validation-engine="validate[required]" class="span8"  type="text" placeholder="请输入成本价格"> </div></td> <td><input type="text" name="rule[]"></td> <td> <div class="btn-toolbar"> <span class="btn-group"> <button type="button" class="btn addstandard addButton">增加规格<i class="icon-plus"></i><strong></strong></button> <button type="button" class="btn removestandard">移除规格<i class="icon-minus"></i><strong></strong></button> </span> </div> </td> </tr>');
		 });
		$(".removestandard").live('click',function(){
			$(this).closest('tr').remove();
			if($("#polytypetable tr").length == 2){
				$("#polytypetable").remove();
				$("input[name='polytype']").attr("checked",false);
			}
		})
	 },
	 //是否套餐勾选
	 clickispackage : function(){
		 $("input[name='ispackage']").live('click',function(){
			if($(this).is(':checked')){
				$(this).closest("table").after(aaaa);
				$('.chzn-select').chosen();
				// $(".chzn-select").select2({
					// allowClear: true//单选
				// });

			}else{
				$("#istable").remove();
			} 
		 });
	 },
	 //添加删除产品
	 changeproduct :function(){
		 $(".addproduct").live('click',function(){
			 $(this).closest('tr').after(aaaaaa);
			 $('.chzn-select').chosen();
		 });
		$(".removeproduct").live('click',function(){
			$(this).closest('tr').remove();
			if($("#istable tr").length == 2){
				$("#istable").remove();
				$("input[name='ispackage']").attr("checked",false);
			}
		}) 
	 },
	 //对产品的多规格字段 成本价格 以及 市场价 字段验证 只能输入数字 精确度两位 (市场价格不能小于成本价格);
	 checkstandard : function(){
		 $("body").on("blur","input[name='singleprice[]'],input[name='realyprice[]'],input[name^='defaultcost']",function(){
             if(isNaN($(this).val())){
				alert("只能填写数字");
				$(this).val("");
			}else if(parseInt($(this).val())<0){
				$(this).val("");
			}else if($(this).val()==""){
				
			}else{
                    $(this).val(Number($(this).val()).toFixed(2));
			}

			if($(this).attr('name') == 'singleprice[]'){
				//对单价(市场价)的验证
				var realprice = $(this).closest('td').next('td').find("input").val();
				if(realprice !== ""){
					if(Number($(this).val())< Number(realprice)){
						$(this).validationEngine('showPrompt','市场价价格不能小于成本价','false','centerRight','autoHideDelay[10]');
						$(this).val(realprice);
					}
				}
			}else{
				//对成本价格的验证
				var singlprice = $(this).closest('td').prev('td').find("input").val();
				if(singlprice !==""){
					if(Number($(this).val())> Number(singlprice)){
						$(this).validationEngine('showPrompt','市场价价格不能小于成本价','false','centerRight','autoHideDelay[10]');
						$(this).val(singlprice);
					}
				}
			}
		});

         $("body").on('blur','input[name="years[]"]',function(){
             if(isNaN($(this).val())){
                 alert("只能填写数字");
                 $(this).val("");
             }else if(parseInt($(this).val())<0){
                 $(this).val(1);
             }else{
                 $(this).val(Number($(this).val()).toFixed(0));
             }
         });
		//市场价格不能小于成本价格
	 },
	 //编辑时加载套餐产品跟规格
	 loadingboth : function(){
		if($("input[name='ispackage']").is(':checked')||$("input[name='polytype']").is(':checked')){
			var record = $("input[name='record']").val();
			if(record){
				var urlParams = "module=Products&view=Detail&mode=getpackstand&record="+record;
				params = {
					'type' : 'GET',
					'dataType': 'html',
					'data' : urlParams
				};
				var widgetContainer = $("input[name='ispackage']").closest("table").progressIndicator({});
				AppConnector.request(params).then(
					function(data){
						widgetContainer.progressIndicator({'mode': 'hide'});
						widgetContainer.after(data);
						$('.chzn-select').chosen();
				});
			}
		} 
	 },
	 //套餐下产品规格加载，以及套餐产品不能够重复的验证;
	 loadingproductstandard : function(){
		$("select[name='packagepro[]']").live("change",function(){
			if($(this).val() != ""){
				temp = $(this);
				var id= $(this).attr('id');
				var ifloading = "true";
				$.each($("select[name='packagepro[]']"),function(i,index){				
					if($(this).attr('id')!==id && temp.val() == $(this).val() && $(this).val()!==""){
						ifloading = "false";
						temp.val("");
                        temp.trigger('liszt:updated');
                        //temp.next('div').children('a').children('span').text("选择一个选项");
						//清空默认规格
                        temp.closest('td').next("td").html('<select name="defaultstand[]"  class="chzn-select" value = ""><option>请选择一个选项</option></select>');

                        //清空可选规格
                        temp.closest('tr').find('select[name="choosablestand[]"]').closest("td").html('<select class="chzn-select"  multiple="true" name="choosablestand[]" value = ""><option>请选择一个选项</option></select>');
                        $('.chzn-select').chosen();
                        alert("套餐内的产品不能够相同");
                    }
				});
				if(ifloading == "true"){
					var params = "?module=Products&action=BasicAjax&mode=getproductstandard&productid="+$(this).val()+"&record="+$('input[name="record"]').val();
					AppConnector.request(params).then(
									function(data){
										if(data.success==true){										
											temp.closest('td').next("td").html(data.result['0']);
                                            temp.closest('tr').find('select[name^="choosablestand"]').closest("td").html(data.result['1']);
											$('.chzn-select').chosen();
										}
					})
				}
			}
		})
	 },
	//end
	registerEvents : function(){
		this._super();
		this.registerEventForMoreCurrencies();
		this.registerEventForTaxes();
		this.registerEventForUnitPrice();
		//this.addNewprotype();
		//this.registerRecordPreSaveEvent();
		this.registerEventForCkEditor();
		this.clickmultistandard();
		this.changestandard();
		this.clickispackage();
		this.changeproduct();
		this.loadingboth();
		this.checkstandard();
		this.loadingproductstandard();
		
		$('body').on('change','#formid',function(){
			var moreCurrenciesParams = {
				'module' : 'Products',
				'view' : "Detail",
				'mode':'getcustomerfields',
				'formid':$(this).val()
			}
			 
			AppConnector.request(moreCurrenciesParams).then(
				function(data){
					$('#form_show').html(data);
					 
				}
				
			)
		
		
		})
		
		
		
	}
})