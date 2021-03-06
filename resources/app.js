/*+***********************************************************************************
 * 获取页面通用
 * @author young.yang 2015-05-27
 * @copyright CRM
 *************************************************************************************/
var app = {
	/**JS的提示字面翻译 准备废弃 */
	languageString : [],
	weekDaysArray : {Sunday : 0,Monday : 1, Tuesday : 2, Wednesday : 3,Thursday : 4, Friday : 5, Saturday : 6},
	/**
	 * 从隐藏字段获取当前模块名
	 */
	getModuleName : function(){return jQuery('#module').val();},
	/**
	 * 获取父级模块名
	 */
	getParentModuleName : function(){return jQuery('#parent').val();},

	/**
	 * 当前视图名称 详细编辑列表
	 */
	getViewName : function(){return jQuery('#view').val();},
	/**
	 * 页面主内容区域bodyContents对象
	 */
	getContentsContainer : function(){return jQuery('.bodyContents');},

	/**
	 * 重写下拉框？
	 * @params parent - select element
	 * @params view - select2
	 * @params viewParams - select2 params
	 * @returns jquery object list which represents changed select elements
	 */
	changeSelectElementView : function(parent, view, viewParams){
		var selectElement = jQuery();
		if(typeof parent == 'undefined') {
			parent = jQuery('body');
		}
		//If view is select2, This will convert the ui of select boxes to select2 elements.
		if(view == 'select2') {
			app.showSelect2ElementView(parent, viewParams);
			return;
		}
		selectElement = jQuery('.chzn-select', parent);
		//parent itself is the element
		if(parent.is('select.chzn-select')) {
			selectElement = parent;
		}
		//fix for multiselect error prompt hide when validation is success
		selectElement.filter('[multiple]').filter('[data-validation-engine*="validate"]').on('change',function(e){
			//$(this).attr('name','['+$(this).attr('name')+']');
			jQuery(e.currentTarget).trigger('focusout');
			
		});
		var chosenElement = selectElement.chosen();
		var chosenSelectConainer = jQuery('.chzn-container');
		//Fix for z-index issue in IE 7
		if (jQuery.browser.msie && jQuery.browser.version === "7.0") {
			var zidx = 1000;
			chosenSelectConainer.each(function(){
				$(this).css('z-index', zidx);
				zidx-=10;
			});
		}
		return chosenSelectConainer;
	},
	/**
	 * Function to destroy the chosen element and get back the basic select Element
	 */
	destroyChosenElement : function(parent) {
		var selectElement = jQuery();
		if(typeof parent == 'undefined') {
			parent = jQuery('body');
		}
		selectElement = jQuery('.chzn-select', parent);
		//parent itself is the element
		if(parent.is('select.chzn-select')) {
			selectElement = parent;
		}
		selectElement.css('display','block').removeClass("chzn-done").data("chosen", null).next().remove();
		return selectElement;
	},
	/**
	 * Function which will show the select2 element for select boxes . This will use select2 library
	 */
	showSelect2ElementView : function(selectElement, params) {
		if(typeof params == 'undefined') {
			params = {};
		}
		var data = selectElement.data();
		if(data != null) {
			params = jQuery.extend(data,params);
		}
		// Sort DOM nodes alphabetically in select box.
		if (typeof params['customSortOptGroup'] != 'undefined' && params['customSortOptGroup']) {
			jQuery('optgroup', selectElement).each(function(){
				var optgroup = jQuery(this);
				var options  = optgroup.children().toArray().sort(function(a, b){
					var aText = jQuery(a).text();
					var bText = jQuery(b).text();
					return aText < bText ? 1 : -1;
				});
				jQuery.each(options, function(i, v){
					optgroup.prepend(v);
				});
			});
			delete params['customSortOptGroup'];
		}
		//formatSelectionTooBig param is not defined even it has the maximumSelectionSize,
		//then we should send our custom function for formatSelectionTooBig
		if(typeof params.maximumSelectionSize != "undefined" && typeof params.formatSelectionTooBig == "undefined") {
			var limit = params.maximumSelectionSize;
			//custom function which will return the maximum selection size exceeds message.
			var formatSelectionExceeds = function(limit) {
					return app.vtranslate('JS_YOU_CAN_SELECT_ONLY')+' '+limit+' '+app.vtranslate('JS_ITEMS');
			}
			params.formatSelectionTooBig = formatSelectionExceeds;
		}
		if(selectElement.attr('multiple') != 'undefined' && typeof params.closeOnSelect == 'undefined') {
			params.closeOnSelect = false;
		}
		selectElement.select2(params)
					 .on("open", function(e) {
						 var element = jQuery(e.currentTarget);
						 var instance = element.data('select2');
						 instance.dropdown.css('z-index',1000002);
					 });
		if(typeof params.maximumSelectionSize != "undefined") {
			app.registerChangeEventForMultiSelect(selectElement,params);
		}
		return selectElement;
	},

	/**
	 * 下拉多选校验
	 */
	registerChangeEventForMultiSelect :  function(selectElement,params) {
		if(typeof selectElement == 'undefined'){return;}
		var instance = selectElement.data('select2');
		var limit = params.maximumSelectionSize;
		selectElement.on('change',function(e){
			var data = instance.data()
			if (jQuery.isArray(data) && data.length >= limit ) {
				instance.updateResults();
            }
		});
	},

	/**
	 * Function to get data of the child elements in serialized format
	 * @params <object> parentElement - element in which the data should be serialized. Can be selector , domelement or jquery object
	 * @params <String> returnFormat - optional which will indicate which format return value should be valid values "object" and "string"
	 * @return <object> - encoded string or value map
	 */
	getSerializedData : function(parentElement, returnFormat){
		if(typeof returnFormat == 'undefined') {
			returnFormat = 'string';
		}

		parentElement = jQuery(parentElement);

		var encodedString = parentElement.children().serialize();
		if(returnFormat == 'string'){
			return encodedString;
		}
		var keyValueMap = {};
		var valueList = encodedString.split('&')

		for(var index in valueList){
			var keyValueString = valueList[index];
			var keyValueArr = keyValueString.split('=');
			var nameOfElement = keyValueArr[0];
			var valueOfElement =  keyValueArr[1];
			keyValueMap[nameOfElement] = decodeURIComponent(valueOfElement);
		}
		return keyValueMap;
	},

	showModalWindow: function(data, url, cb, css) {

		var unBlockCb = function(){};
		var overlayCss = {};

		//null is also an object
		if(typeof data == 'object' && data != null && !(data instanceof jQuery)){
			css = data.css;
			cb = data.cb;
			url = data.url;
			unBlockCb = data.unblockcb;
			overlayCss = data.overlayCss;
			data = data.data

		}
		if (typeof url == 'function') {
			if(typeof cb == 'object') {
				css = cb;
			}
			cb = url;
			url = false;
		}
		else if (typeof url == 'object') {
			cb = function() { };
			css = url;
			url = false;
		}

		if (typeof cb != 'function') {
			cb = function() { }
		}

		var id = 'globalmodal';
		var container = jQuery('#'+id);
		if (container.length) {
			container.remove();
		}
		container = jQuery('<div></div>');
		container.attr('id', id);

		var showModalData = function (data) {

			var defaultCss = {
							'top' : '0px',
							'width' : 'auto',
							'max-width':'800px',
							'cursor' : 'default',
							'left' : '35px',
							'text-align' : 'left',
							'border-radius':'6px'
							};
			var effectiveCss = defaultCss;
			if(typeof css == 'object') {
				effectiveCss = jQuery.extend(defaultCss, css)
			}

			var defaultOverlayCss = {
										'cursor' : 'default'
									};
			var effectiveOverlayCss = defaultOverlayCss;
			if(typeof overlayCss == 'object' ) {
				effectiveOverlayCss = jQuery.extend(defaultOverlayCss,overlayCss);
			}
			container.html(data);

			// Mimic bootstrap modal action body state change
			jQuery('body').addClass('modal-open');

			//container.modal();
			jQuery.blockUI({
					'message' : container,
					'overlayCSS' : effectiveOverlayCss,
					'css' : effectiveCss,

					// disable if you want key and mouse events to be enable for content that is blocked (fix for select2 search box)
					bindEvents: false,

					//Fix for overlay opacity issue in FF/Linux
					applyPlatformOpacityRules : false
				});
			var unblockUi = function() {
				app.hideModalWindow(unBlockCb);
				jQuery(document).unbind("keyup",escapeKeyHandler);
			}
			var escapeKeyHandler = function(e){
				if (e.keyCode == 27) {
						unblockUi();
				}
			}
			jQuery('.blockOverlay').click(unblockUi);
			jQuery(document).on('keyup',escapeKeyHandler);
			jQuery('[data-dismiss="modal"]', container).click(unblockUi);

			container.closest('.blockMsg').position({
				'of' : jQuery(window),
				'my' : 'center top',
				'at' : 'center top',
				'collision' : 'flip none',
				//TODO : By default the position of the container is taking as -ve so we are giving offset
				// Check why it is happening
				'offset' : '0 50'
			});
			//container.css({'height' : container.innerHeight()+15+'px'});

			// TODO Make it better with jQuery.on
			app.changeSelectElementView(container);
            //register all select2 Elements
            app.showSelect2ElementView(container.find('select.select2'));
			//register date fields event to show mini calendar on click of element
			app.registerEventForDatePickerFields(container);
			cb(container);
		}

		if (data) {
			showModalData(data)

		} else {
			jQuery.get(url).then(function(response){
				showModalData(response);
			});
		}

		return container;
	},

	/**
	 * 隐藏对话框
	 * This api assumes that we are using block ui plugin and uses unblock api to unblock it
	 */
	hideModalWindow : function(callback) {
		// Mimic bootstrap modal action body state change - helps to avoid body scroll
		// when modal is shown using css: http://stackoverflow.com/a/11013994
		jQuery('body').removeClass('modal-open');
		var id = 'globalmodal';
		var container = jQuery('#'+id);
		if (container.length <= 0) {
			return;
		}
		if(typeof callback != 'function') {
			callback = function() {};
		}
		jQuery.unblockUI({
			'onUnblock' : callback
		});
	},

	isHidden : function(element) {
		if(element.css('display')== 'none') {
			return true;
		}
		return false;
	},

	/**
	 * 验证引擎配置
	 */
	validationEngineOptions: {
		// Avoid scroll decision and let it scroll up page when form is too big
		// Reference: http://www.position-absolute.com/articles/jquery-form-validator-because-form-validation-is-a-mess/
		scroll: false,promptPosition: 'topLeft',
		//to support validation for chosen select box
		prettySelect : true,useSuffix: "_chzn", usePrefix : "s2id_"},

	/**
	 * Function to push down the error message size when validation is invoked
	 * @params : form Element
	 */

	formAlignmentAfterValidation : function(form){
		// to avoid hiding of error message under the fixed nav bar
		var destination = form.find(".formError:not('.greenPopup'):first").offset().top;
		var resizedDestnation = destination-105;
		jQuery('html').animate({
			scrollTop:resizedDestnation
		}, 'slow');
	},

	/**
	 * Function to push down the error message size when validation is invoked
	 * @params : form Element
	 */
	formAlignmentAfterValidation : function(form){
		// to avoid hiding of error message under the fixed nav bar
		var destination = form.find(".formError:not('.greenPopup'):first").offset().top;
		var resizedDestnation = destination-105;
		jQuery('html').animate({
			scrollTop:resizedDestnation
		}, 'slow');
	},

	convertToDatePickerFormat: function(dateFormat){
		if(dateFormat == 'yyyy-mm-dd'){
			return 'Y-m-d';
		} else if(dateFormat == 'mm-dd-yyyy') {
			return 'm-d-Y';
		} else if (dateFormat == 'dd-mm-yyyy') {
			return 'd-m-Y';
		}
	},

	convertTojQueryDatePickerFormat: function(dateFormat){
		var i = 0;
		var splitDateFormat = dateFormat.split('-');
		for(var i in splitDateFormat){
			var sectionDate = splitDateFormat[i];
			var sectionCount = sectionDate.length;
			if(sectionCount == 4){
				var strippedString = sectionDate.substring(0,2);
				splitDateFormat[i] = strippedString;
			}
		}
		var joinedDateFormat =  splitDateFormat.join('-');
		return joinedDateFormat;
	},
	getDateInVtigerFormat: function(dateFormat,dateObject){
		var finalFormat = app.convertTojQueryDatePickerFormat(dateFormat);
		var date = jQuery.datepicker.formatDate(finalFormat,dateObject);
		return date;
	},

	registerEventForTextAreaFields : function(parentElement) {
		if(typeof parentElement == 'undefined') {
			parentElement = jQuery('body');
		}

		parentElement = jQuery(parentElement);

		if(parentElement.is('textarea')){
			var element = parentElement;
		}else{
			var element = jQuery('textarea', parentElement);
		}
		if(element.length == 0){
			return;
		}
		element.autosize();
	},

	registerEventForDatePickerFields : function(parentElement,registerForAddon,customParams){
		if(typeof parentElement == 'undefined') {
			parentElement = jQuery('body');
		}
		if(typeof registerForAddon == 'undefined'){
			registerForAddon = true;
		}

		parentElement = jQuery(parentElement);

		if(parentElement.hasClass('dateField')){
			var element = parentElement;
		}else{
			var element = jQuery('.dateField', parentElement);
		}
		if(element.length == 0){
			return;
		}
		if(registerForAddon == true){
			var parentDateElem = element.closest('.date');
			jQuery('.add-on',parentDateElem).on('click',function(e){
				var elem = jQuery(e.currentTarget);
				//Using focus api of DOM instead of jQuery because show api of datePicker is calling e.preventDefault
				//which is stopping from getting focus to input element
				elem.closest('.date').find('input.dateField').get(0).focus();
			});
		}
		var dateFormat = element.data('dateFormat');
		var vtigerDateFormat = app.convertToDatePickerFormat(dateFormat);
		var language = jQuery('body').data('language');
		var lang = language.split('_');
		
		//Default first day of the week
		var defaultFirstDay = jQuery('#start_day').val();
		if(defaultFirstDay == '' || typeof(defaultFirstDay) == 'undefined'){
			var convertedFirstDay = 1
		} else {
			convertedFirstDay = this.weekDaysArray[defaultFirstDay];
		}
		var params = {
			format : vtigerDateFormat,
			calendars: 1,
			//locale: $.fn.datepicker.dates[lang[0]],
			starts: convertedFirstDay,
			eventName : 'focus',
			onChange: function(formated){
                var element = jQuery(this).data('datepicker').el;
                element = jQuery(element);
                var datePicker = jQuery('#'+ jQuery(this).data('datepicker').id);
                var viewDaysElement = datePicker.find('table.datepickerViewDays');
                //If it is in day mode and the prev value is not eqaul to current value
                //Second condition is manily useful in places where user navigates to other month
                if(viewDaysElement.length > 0 && element.val() != formated) {
                    element.DatePickerHide();
                    element.blur();
                }
				element.val(formated).trigger('change');
			}
		}
		if(typeof customParams != 'undefined'){
			var params = jQuery.extend(params,customParams);
		}
		element.each(function(index,domElement){
			var jQelement = jQuery(domElement);
			var dateObj = new Date();
			var selectedDate = app.getDateInVtigerFormat(dateFormat, dateObj);
			//Take the element value as current date or current date
			if(jQelement.val() != '') {
				selectedDate = jQelement.val();
			}
			params.date = selectedDate;
			params.current = selectedDate;
			//jQelement.DatePicker(params)
			jQelement.datepicker({
				format: "yyyy-mm-dd",
                language:  'zh-CN',
		        autoclose: true,
		        todayBtn: false,
                todayHighlight:true,
		        pickerPosition: "bottom-left"
		    });
		});

	},
	registerEventForDateFields : function(parentElement) {
		if(typeof parentElement == 'undefined') {
			parentElement = jQuery('body');
		}

		parentElement = jQuery(parentElement);

		if(parentElement.hasClass('dateField')){
			var element = parentElement;
		}else{
			var element = jQuery('.dateField', parentElement);
		}
		element.datepicker({'autoclose':true}).on('changeDate', function(ev){
			var currentElement = jQuery(ev.currentTarget);
			var dateFormat = currentElement.data('dateFormat');
			var finalFormat = app.getDateInVtigerFormat(dateFormat,ev.date);
			var date = jQuery.datepicker.formatDate(finalFormat,ev.date);
			currentElement.val(date);
		});
	},

	/**
	 * Function which will register time fields
	 *
	 * @params : container - jquery object which contains time fields with class timepicker-default or itself can be time field
	 *			 registerForAddon - boolean value to register the event for Addon or not
	 *			 params  - params for the  plugin
	 *
	 * @return : container to support chaining
	 */
	registerEventForTimeFields : function(container, registerForAddon, params) {

		if(typeof cotainer == 'undefined') {
			container = jQuery('body');
		}
		if(typeof registerForAddon == 'undefined'){
			registerForAddon = true;
		}

		container = jQuery(container);

		if(container.hasClass('timepicker-default')) {
			var element = container;
		}else{
			var element = container.find('.timepicker-default');
		}

		if(registerForAddon == true){
			var parentTimeElem = element.closest('.time');
			jQuery('.add-on',parentTimeElem).on('click',function(e){
				var elem = jQuery(e.currentTarget);
				elem.closest('.time').find('.timepicker-default').focus();
			});
		}

		if(typeof params == 'undefined') {
			params = {};
		}

		var timeFormat = element.data('format');
		if(timeFormat == '24') {
			timeFormat = 'H:i';
		} else {
			timeFormat = 'h:i A';
		}
		var defaultsTimePickerParams = {
			'timeFormat' : timeFormat,
			'className'  : 'timePicker'
		};
		var params = jQuery.extend(defaultsTimePickerParams, params);

		element.datepicker({
			format: "yyyy-mm-dd",
	        autoclose: true,
	        todayBtn: false,
	        pickerPosition: "bottom-left",
	        showMeridian: 0
	    });

		return container;
	},

	/**
	 * Function to destroy time fields
	 */
	destroyTimeFields : function(container) {

		if(typeof cotainer == 'undefined') {
			container = jQuery('body');
		}

		if(container.hasClass('timepicker-default')) {
			var element = container;
		}else{
			var element = container.find('.timepicker-default');
		}
		element.data('timepicker-list',null);
		return container;
	},

	/**
	 * Function to get the chosen element from the raw select element
	 * @params: select element
	 * @return : chosenElement - corresponding chosen element
	 */
	getChosenElementFromSelect : function(selectElement) {
		var selectId = selectElement.attr('id');
		var chosenEleId = selectId+"_chzn";
		return jQuery('#'+chosenEleId);
	},

	/**
	 * Function to get the select2 element from the raw select element
	 * @params: select element
	 * @return : select2Element - corresponding select2 element
	 */
	getSelect2ElementFromSelect : function(selectElement) {
		var selectId = selectElement.attr('id');
		//since select2 will add s2id_ to the id of select element
		var select2EleId = "s2id_"+selectId;
		return jQuery('#'+select2EleId);
	},

	/**
	 * Function to get the select element from the chosen element
	 * @params: chosen element
	 * @return : selectElement - corresponding select element
	 */
	getSelectElementFromChosen : function(chosenElement) {
		var chosenId = chosenElement.attr('id');
		var selectEleIdArr = chosenId.split('_chzn');
		var selectEleId = selectEleIdArr['0'];
		return jQuery('#'+selectEleId);
	},

	/**
	 * Function to set with of the element to parent width
	 * @params : jQuery element for which the action to take place
	 */
	setInheritWidth : function(elements) {
		jQuery(elements).each(function(index,element){
			var parentWidth = jQuery(element).parent().width();
			jQuery(element).width(parentWidth);
		});
	},


	initGuiders: function (list) {
		if (list) {
			for (var index=0, len=list.length; index < len; ++index) {
				var guiderData = list[index];
				guiderData['id'] = ""+index;
				guiderData['overlay'] = true;
				guiderData['highlight'] = true;
				guiderData['xButton'] = true;
				if (index < len-1) {
					guiderData['buttons'] = [{name: 'Next'}];
					guiderData['next'] = ""+(index+1);

				}
				guiders.createGuider(guiderData);
			}
			// TODO auto-trigger the guider.
			guiders.show('0');
		}
	},

	showScrollBar : function(element, options) {
		if(typeof options == 'undefined') {
			options = {};
		}
		if(typeof options.height == 'undefined') {
			options.height = element.css('height');
		}

		return element.slimScroll(options);
	},

	showHorizontalScrollBar : function(element, options) {
		if(typeof options == 'undefined') {
			options = {};
		}
		var params = {
			horizontalScroll: true,
			theme: "dark-thick",
			advanced: {
				autoExpandHorizontalScroll:true
			}
		}
		if(typeof options != 'undefined'){
			var params = jQuery.extend(params,options);
		}
		return element.mCustomScrollbar(params);
	},

	/**
	 * 翻译JS提示信息
	 */
	vtranslate : function(key) {
		if(app.languageString[key] != undefined) {
			return app.languageString[key];
		} else {
			//var strings = jQuery('#js_strings').text();
			var jsl= eval(jQuery('#module').val()+'language');
			if(jsl != '') {
				app.languageString = jsl;
				if(key in app.languageString){
					return app.languageString[key];
				}
			}
		}
		return key;
	},

	/**
	 * Function which will set the contents height to window height
	 */
	setContentsHeight : function() {
		var row=$('.row').height()+20;
		var navBarFixedTop=$('.navbar-fixed-top').height();
		var breadcrumb=$('.breadcrumb').height()+10;
		var SearchBlankCover=$('#SearchBug').height();
		var minheight=jQuery(window).height()- (row +breadcrumb+navBarFixedTop+SearchBlankCover);
		var parentName=$('input[name="parent"]').val();
		if(parentName!='Settings') {
			$('.listViewEntriesDiv').css('height',minheight-35);
			$('.fht-tbody').css({"height":(minheight-80)+'px'});
		}else{
			$('.listViewEntriesDiv').css('height',jQuery(window).height()-270);

		}
		/*var bodyContentsElement = app.getContentsContainer();
		var borderTopWidth = parseInt(bodyContentsElement.css('borderTopWidth'));
		var borderBottomWidth = parseInt(bodyContentsElement.css('borderBottomWidth'));
		
		//var searchHeight=bodyContentsElement.find('#showSearch').height();
		//console.log(searchHeight);
		var h=$('.listViewEntriesDiv').height();
		var minheight=jQuery(window).height()- (borderTopWidth + borderBottomWidth+56);
		//console.log(minheight);
		//young 无法对异步加载的数据进行控制
		if(h>minheight){$('.listViewEntriesDiv').css('height',minheight-205);}
		if(h<minheight){$('.listViewEntriesDiv').css('height',minheight-205);}
		//console.log($(window).height());console.log(minheight);
		bodyContentsElement.css('min-height',jQuery(window).height()-56);*/
	},

	/**
	 * Function will return the current users layout + skin path
	 * @param <string> img - image name
	 * @return <string>
	 */
	vimage_path : function(img) {
		return jQuery('body').data('skinpath')+ '/images/' + img ;
	},

	/*
	 * Cache API on client-side
	 */
	cacheNSKey: function(key) { // Namespace in client-storage
		return 'vtiger6.' + key;
	},
	cacheGet: function(key, defvalue) {
		key = this.cacheNSKey(key);
		return jQuery.jStorage.get(key, defvalue);
	},
	cacheSet: function(key, value) {
		key = this.cacheNSKey(key);
		jQuery.jStorage.set(key, value);
	},
	cacheClear : function(key) {
		key = this.cacheNSKey(key);
		return jQuery.jStorage.deleteKey(key);
	},

	htmlEncode : function(value){
		if (value) {
			return jQuery('<div />').text(value).html();
		} else {
			return '';
		}
	},

	htmlDecode : function(value) {
		if (value) {
			return $('<div />').html(value).text();
		} else {
			return '';
		}
	},

	/**
	 * Function places an element at the center of the page
	 * @param <jQuery Element> element
	 */
	placeAtCenter : function(element) {
		element.css("position","absolute");
		element.css("top", ((jQuery(window).height() - element.outerHeight()) / 2) + jQuery(window).scrollTop() + "px");
		element.css("left", ((jQuery(window).width() - element.outerWidth()) / 2) + jQuery(window).scrollLeft() + "px");
	},

	getvalidationEngineOptions : function(select2Status){
		return app.validationEngineOptions;
	},

	/**
	 * Function to notify UI page ready after AJAX changes.
	 * This can help in re-registering the event handlers (which was done during ready event).
	 */
	notifyPostAjaxReady: function() {
		jQuery(document).trigger('postajaxready');
	},

	/**
	 * Listen to xready notiications.
	 */
	listenPostAjaxReady: function(callback) {
		jQuery(document).on('postajaxready', callback);
	},

	/**
	 * Form function handlers
	 */
	setFormValues: function(kv) {
		for (var k in kv) {
			jQuery(k).val(kv[k]);
		}
	},

	setRTEValues: function(kv) {
		for (var k in kv) {
			var rte = CKEDITOR.instances[k];
			if (rte) rte.setData(kv[k]);
		}
	},
	//datatable插件配置 关闭 By Joe@20150609
	/*Tableinstance:function(){*//*$('.listViewEntriesTable').dataTable({"sDom": '<"top"fli>rt<"bottom"p><"clear">',"iDisplayLength": 40,});*//*var table = $('#listViewEntriesTable').DataTable({sDom: '<"top"fli>rt<"bottom"p><"clear">',iDisplayLength: 50,language: {"sProcessing":"处理中...","sLengthMenu":"显示 _MENU_ 项结果","sZeroRecords":"没有匹配结果","sInfo":"显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项","sInfoEmpty":"显示第 0 至 0 项结果，共 0 项","sInfoFiltered":"(由 _MAX_ 项结果过滤)","sInfoPostFix":"","sSearch":"搜索:","sUrl":"","sEmptyTable":"表中数据为空","sLoadingRecords":"载入中...","sInfoThousands":",","oPaginate":{"sFirst":"首页","sPrevious":"上页","sNext":"下页","sLast":"末页"},"oAria":{"sSortAscending":": 以升序排列此列","sSortDescending":":以降序排列此列"}},sScrollXInner: "5100px",scrollY:$(window).height()-300,scrollX:true,scrollCollapse: true,paging: false,aLengthMenu: [ 20, 50, 100, 300 ],aoColumnDefs: [ { "bSortable": false, "aTargets": [ 0 ] }],});new $.fn.DataTable.FixedColumns( table ,{"iLeftColumns": 2,"iRightColumns": 1});},*/
	/**
	 * Function returns the javascript controller based on the current view
	 */
	getPageController : function() {
		var moduleName = app.getModuleName();
		var view = app.getViewName()
		var parentModule = app.getParentModuleName();

		var moduleClassName = parentModule+"_"+moduleName+"_"+view+"_Js";
		if(typeof window[moduleClassName] == 'undefined'){
			moduleClassName = parentModule+"_Vtiger_"+view+"_Js";
		}
		if(typeof window[moduleClassName] == 'undefined') {
			moduleClassName = moduleName+"_"+view+"_Js";
		}
		if(typeof window[moduleClassName] == 'undefined') {
			moduleClassName = "Vtiger_"+view+"_Js";
		}
        if(typeof window[moduleClassName] != 'undefined') {
			return new window[moduleClassName]();
		}
	},

	/**
	 * Function to decode the encoded htmlentities values
	 */
	getDecodedValue : function(value) {
		return jQuery('<div></div>').html(value).text();
	},

	/**
	 * Function to check whether the color is dark or light
	 */
	getColorContrast: function(hexcolor){
		var r = parseInt(hexcolor.substr(0,2),16);
		var g = parseInt(hexcolor.substr(2,2),16);
		var b = parseInt(hexcolor.substr(4,2),16);
		var yiq = ((r*299)+(g*587)+(b*114))/1000;
		return (yiq >= 128) ? 'light' : 'dark';
	},
    
    updateRowHeight : function() {
        var rowType = jQuery('#row_type').val();
        if(rowType.length <=0 ){
            //Need to update the row height
            var widthType = app.cacheGet('widthType', 'mediumWidthType');
            var serverWidth = widthType;
            switch(serverWidth) {
                case 'narrowWidthType' : serverWidth = 'narrow'; break;
                case 'wideWidthType' : serverWidth = 'wide'; break;
                default : serverWidth = 'medium';
            }
			var userid = jQuery('#current_user_id').val();
            var params = {
                'module' : 'Users',
                'action' : 'SaveAjax',
                'record' : userid,
                'value' : serverWidth,
                'field' : 'rowheight'
            };
            AppConnector.request(params).then(function(){
                jQuery(rowType).val(serverWidth);
            });
        }
    },
    /**
	*	system time add one hour.
	*	author: fly
    */
    addOneHour: function() {
    	var d=new Date();
		var year=d.getFullYear();
		var month=d.getMonth()+1+"";
		var day=d.getDate()+"";
		var hour=d.getHours()+1+"";
		var min=d.getMinutes()+"";
		if(month.length == 1) {
			month = 0 + month;
		}
		if(day.length == 1) {
			day = 0 + day;
		}
		if(hour.length == 1) {
			hour = 0 + hour;
		}
		if(min.length == 1) {
			min = 0 + min;
		}
		var str = year +"-"+ month +"-"+ day +" "+ hour +":"+ min;
		return str;
    },
    tabletrodd:function(){
        $('.listViewEntriesDiv table.table tr:even').addClass('success');
    }
}

jQuery(document).ready(function(){
	app.changeSelectElementView();

    //加入隔行换色

    app.tabletrodd();

	//register all select2 Elements
	app.showSelect2ElementView(jQuery('body').find('select.select2'));
	app.setContentsHeight();
	//app.Tableinstance();
	//Updating row height
	app.updateRowHeight();
	$(window).resize(function(){
		app.setContentsHeight();
	})
	String.prototype.toCamelCase = function(){
		var value = this.valueOf();
		return  value.charAt(0).toUpperCase() + value.slice(1).toLowerCase()
	}
	// Instantiate Page Controller
	var pageController = app.getPageController();
	if(pageController) pageController.registerEvents();
});

/* Global function for UI5 embed page to callback */
function resizeUI5IframeReset() {
	jQuery('#ui5frame').height(650);
}
function resizeUI5Iframe(newHeight) {
	jQuery('#ui5frame').height(parseInt(newHeight,10)+15); // +15px - resize on IE without scrollbars
}

//调试输出js变量
function ialert(p){
	var str = '';
	if(typeof p=='object'){
		for(var k in p){
			str = str+k+':'+p[k]+',\r\n ';
		}
		str = '{'+str+'}';
	}else{
		str = p;
	}
	alert(str);
}
