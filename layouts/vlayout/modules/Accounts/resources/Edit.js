Vtiger_Edit_Js("Accounts_Edit_Js",{ },{
    //Stored history of account name and duplicate check result
	duplicateCheckCache : {},
	//This will store the editview form
	editViewForm : false,
	//Address field mapping within module
	addressFieldsMappingInModule : {'bill_street':'ship_street','bill_pobox':'ship_pobox','bill_city':'ship_city','bill_state':'ship_state','bill_code'	:'ship_code','bill_country':'ship_country'},							
	/**
	 * This function will return the current form
	 */
	getForm : function(){
		if(this.editViewForm == false) {
			this.editViewForm = jQuery('#EditView');
		}
		return this.editViewForm;
	},    
	/**
	 * This function will return the account name
	 */
	getAccountName : function(container){
		return jQuery('input[name="accountname"]',container).val();
	},    
	/**
	 * This function will return the current RecordId
	 */
	getRecordId : function(container){
		return jQuery('input[name="record"]',container).val();
	},  
	/**
	 * This function will register before saving any record
	 */
	registerRecordPreSaveEvent : function(form) {
		var thisInstance = this;
		if(typeof form == 'undefined') {
			form = this.getForm();
		}
		form.on(Vtiger_Edit_Js.recordPreSave, function(e, data) {
			var accountName = thisInstance.getAccountName(form);
			var recordId = thisInstance.getRecordId(form);
			var params = {};
            if(!(accountName in thisInstance.duplicateCheckCache)) {
                Vtiger_Helper_Js.checkDuplicateName({
                    'accountName' : accountName, 
                    'recordId' : recordId,
                    'moduleName' : 'Accounts'
                }).then(
                    function(data){
                        thisInstance.duplicateCheckCache[accountName] = data['success'];
                        form.submit();
                    },
                    function(data, err){
                        thisInstance.duplicateCheckCache[accountName] = data['success'];
                        thisInstance.duplicateCheckCache['message'] = data['message'];
						//var message = app.vtranslate('');
						var  params = {text : app.vtranslate(data.message),
								title : app.vtranslate('JS_DUPLICTAE_CREATION_CONFIRMATION')}
							Vtiger_Helper_Js.showPnotify(params);
						/* Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
							function(e) {thisInstance.duplicateCheckCache[accountName] = false;form.submit();},
							function(error, err) {}); */
                    }
				);
            }else {
				if(thisInstance.duplicateCheckCache[accountName] == true){
					var params = {text : thisInstance.duplicateCheckCache['message'],
					title : app.vtranslate('JS_DUPLICTAE_CREATION_CONFIRMATION')}
					Vtiger_Helper_Js.showPnotify(params);
				} else {
					delete thisInstance.duplicateCheckCache[accountName];
					return true;
				}
				/* if(thisInstance.duplicateCheckCache[accountName] == true){var message = app.vtranslate('JS_DUPLICTAE_CREATION_CONFIRMATION');
					Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(function(e) {//thisInstance.duplicateCheckCache[accountName] = false;form.submit();},function(error, err) {});
				} else {//delete thisInstance.duplicateCheckCache[accountName];return true;} */
			}
            e.preventDefault();
		})
	},
	
	/**
	 * ????????????????????? ?????? By Joe
	swapObject : function(objectToSwap){
		var swappedArray = {};
		var newKey,newValue;
		for(var key in objectToSwap){
			newKey = objectToSwap[key];
			newValue = key;
			swappedArray[newKey] = newValue;
		}
		return swappedArray;
	},
	copyAddress : function(swapMode, container){
		var thisInstance = this;
		var addressMapping = this.addressFieldsMappingInModule;
		if(swapMode == "false"){
			for(var key in addressMapping) {
				var fromElement = container.find('[name="'+key+'"]');
				var toElement = container.find('[name="'+addressMapping[key]+'"]');
				toElement.val(fromElement.val());
			}
		} else if(swapMode){
			var swappedArray = thisInstance.swapObject(addressMapping);
			for(var key in swappedArray) {
				var fromElement = container.find('[name="'+key+'"]');
				var toElement = container.find('[name="'+swappedArray[key]+'"]');
				toElement.val(fromElement.val());
			}
		}
	},
	registerEventForCopyingAddress : function(container){ var thisInstance = this; var swapMode; jQuery('[name="copyAddress"]').on('click',function(e){ var element = jQuery(e.currentTarget); var target = element.data('target');	if(target == "billing"){ swapMode = "false"; }else if(target == "shipping"){ swapMode = "true"; }	thisInstance.copyAddress(swapMode, container);	}) },
	 */
	/**
	 * ?????????????????????????????????
	 */
/* 	registerReferenceSelectionEvent : function(container) {
		var thisInstance = this;
		jQuery('input[name="account_id"]', container).on(Vtiger_Edit_Js.referenceSelectionEvent, function(e, data){
			thisInstance.referenceSelectionEventHandler(data, container);
		});
	},
	referenceSelectionEventHandler :  function(data, container) {
		var thisInstance = this;
		var message = app.vtranslate('OVERWRITE_EXISTING_MSG1')+app.vtranslate('SINGLE_'+data['source_module'])+' ('+data['selectedName']+') '+app.vtranslate('OVERWRITE_EXISTING_MSG2');
		Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
			function(e) {	thisInstance.copyAddressDetails(data, container);},
			function(error, err){});
	},
	copyAddressDetails : function(data, container) {
		var thisInstance = this;
		thisInstance.getRecordDetails(data).then(
			function(data){	var response = data['result'];
				thisInstance.mapAddressDetails(thisInstance.addressFieldsMappingInModule, response['data'], container);},
			function(error, err){});
	}, 
	mapAddressDetails : function(addressDetails, result, container) {
		for(var key in addressDetails) {
			container.find('[name="'+key+'"]').val(result[addressDetails[key]]);
			container.find('[name="'+key+'"]').trigger('change');
			container.find('[name="'+addressDetails[key]+'"]').val(result[addressDetails[key]]);
			container.find('[name="'+addressDetails[key]+'"]').trigger('change');
		}
	},*/
	//?????????????????? ??????????????? By Joe
	registerArea:function(){
		if(jQuery('#areadata').length>0){
			var area=jQuery('#areadata').attr('data');
			if(typeof area!='undefined'&& area.length>1){
				area=area.split('#');
				new PCAS("province","city","area",area[0],area[1],area[2]);
				jQuery('input[name=address]').val(area[3]);
			}else{
				new PCAS("province","city","area");
			}	
		}
	},
	//??????????????????????????????
	registerCheck:function(form){
		var thisInstance = this;
		var accountName =$('input[name=accountname]').val();
		var recordId = thisInstance.getRecordId(form);
		$('#EditView').on('input blur','input[name=accountname]',function(){
			if(typeof form == 'undefined') {
				form = this.getForm();
			}
			if($(this).val()==accountName){
				return;
			}
			Vtiger_Helper_Js.checkDuplicateName({
                'accountName' : $(this).val(), 
                'moduleName' : 'Accounts'
            }).then(
                function(data){	
                },
                function(data, err){
					var  params = {text : app.vtranslate(data.message),
							title : app.vtranslate('JS_DUPLICTAE_CREATION_CONFIRMATION')}
						Vtiger_Helper_Js.showPnotify(params);
                }
            );
        });
        $('#EditView').on('keyup','#Accounts_editView_fieldName_annual_revenue',function(){
            if(isNaN($(this).val())){
                $(this).val("");
            }
        });
	$('#EditView').on('input blur keyup','input[name=accountname]',function(){
			var _this=this;
			var _thisValue=$(_this).val();
			if(_thisValue!=''){
				_thisValue=_thisValue.replace(/\s/g,'_');
				$('input[name="accountname"]').popover('destroy').popover({
					html:true, // ??????HTML????????????????????????
					trigger:'manual', // ???????????? click or hover
					content:'<span style="word-break: break-all;color:red;font-weight: bold;">'+_thisValue+'</span>', // ????????????
					placement:'top'
				}).popover('show');
			}else{
				$('input[name="accountname"]').popover('destroy');
			}
		});
    },
    /**
     * ????????????????????????  ?????????????????????????????????  select name = customerproperty  ??? cooperationtypesproperty ????????????????????????
     * cxh 2019-06-27
     */
    detailProcessingOperation:function(){
        // ????????????????????????????????????????????????
        var requires="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]";
        /**
         * @author cxh
         * @content  ????????????????????????
         * @type {*|jQuery}
         * */
        //  ????????????????????????????????? ???????????? ???????????????*
        if($("select[name='customerproperty']").val()=='????????????'){
            $("select[name='cooperationtypesproperty']").attr("data-validation-engine",requires);
            $("select[name='cooperationtypesproperty']").parent().parent().parent().prev().find("label").prepend("<span class='redColor'>*&nbsp;</span>");
        }
        // ???????????? ?????????????????????
        $("select[name='customerproperty']").change(function () {
            if($(this).val()=="????????????"){
                // ???????????????????????????????????????????????????????????????
                if($("select[name='cooperationtypesproperty']").parent().parent().parent().prev().find("label").find(".redColor").length<=0){
                    $("select[name='cooperationtypesproperty']").attr("data-validation-engine",requires);
                    $("select[name='cooperationtypesproperty']").parent().parent().parent().prev().find("label").prepend("<span class='redColor'>*&nbsp;</span>");
                }
            }else{
                $("select[name='cooperationtypesproperty']").attr("data-validation-engine","");
                $("select[name='cooperationtypesproperty']").parent().parent().parent().prev().find("label>span").remove();
            }
        });
        /**
         * @author cxh
         * @content  ????????????????????????????????????
         * @type {*|jQuery}
         */
        //??????????????????????????????????????????
        educationproperty =$("select[name='educationproperty']").val();
        $("input[name='educationproperty']").val(educationproperty);
        industry = $("select[name='industry']");
        console.log(industry);
        var selectHtml=  '<select class="chzn-select"  id="industryChildren" data-validation-engine="" style="position:absolute;width:110px;display:none;" >'+
            '<option  value="">??????????????????</option>';
        if(educationproperty=="???????????????"){
            selectHtml += '<option selected  value="???????????????" >???????????????</option> ';
        }else{
            selectHtml += '<option   value="???????????????" >???????????????</option> ';
        }
        if(educationproperty=="???????????????"){
            selectHtml += '<option selected  value="???????????????" >???????????????</option> ';
        }else{
            selectHtml += '<option   value="???????????????" >???????????????</option> ';
        }
        if(educationproperty=="????????????"){
            selectHtml += '<option selected  value="????????????" >????????????</option> ';
        }else{
            selectHtml += '<option   value="????????????" >????????????</option> ';
        }
        if(educationproperty=="????????????"){
            selectHtml += '<option selected  value="????????????" >????????????</option> ';
        }else{
            selectHtml += '<option   value="????????????" >????????????</option> ';
        }
        if(educationproperty=="????????????"){
            selectHtml += '<option selected  value="????????????" >????????????</option> ';
        }else{
            selectHtml += '<option   value="????????????" >????????????</option> ';
        }
        selectHtml += '</select>';

        industry.after(selectHtml);
        $("select[name='industry']").css("float","left");


        $("select[name='educationproperty']").parent().parent().parent().parent().remove();
        //?????????????????? ?????? ?????????????????????????????????
        industry.change(function () {
            console.log($(this).val());
            if($(this).val()=='Education'){
                //???????????? ??????$('.chzn-select').chosen(); ??????????????????????????????????????????chosen()
                //$('#industryChildren').chosen();
                $("#industryChildren_chzn").css("display","block");
                //$("#industryChildren").trigger("liszt:updated");
                $("#industryChildren").attr("data-validation-engine",requires);
            }else{
                //$('.industryChildren').trigger('chosen:close');
                $("input[name='educationproperty']").val(" ")
                // console.log($("#industryChildren_chzn").length);
                //if($("#industryChildren_chzn").length>0){
                $("#industryChildren_chzn").css("display","none");
                //$("#industryChildren").chosen("destroy");
                /*$("#industryChildren_chzn").remove();*/
                // $('#industryChildren').trigger('liszt:close');
                //$("#industryChildren").trigger("liszt:updated");
                //}
                //$("#industryChildren").css("display","none");
                $("#industryChildren").attr("data-validation-engine","");
            }
            $("#industryChildren").trigger("liszt:updated");
        });
        //??? ?????? industry ?????????????????????????????? ?????????????????????
        if(industry.val()=='Education'){
            //$("#industryChildren").css("display","block");
            $('#industryChildren').chosen();
            //$("#industryChildren").chosen("destroy");
        }else{
            $('#industryChildren').chosen();
            $("#industryChildren_chzn").css("display","none");
            //$("#industryChildren").css("display","none");
        }
        //???????????????????????? ??????????????????????????????????????????????????????
        $("#industryChildren").change(function () {
            //console.log($(this).val());
            $("input[name='educationproperty']").val($(this).val());
        });

        $("#industryChildren_chzn").css("float","right");

    },
	//?????????????????????????????? ?????? By Joe 
	/*checkAccountRank:function(form){	accountrank=$('select[name="accountrank"]').val();	if($('input[name="record"]').val()>1 && accountrank.indexOf('_isv')>0 ){	$('select[name="accountrank"] option').each(function () {	var that=$(this);	if(that.val().indexOf('_isv')<0){	that.attr("disabled", "disabled");	$('.chzn-results>li').each(function () {	if($(this).text()==that.text()){	$(this).remove();	}	});	} 	});	} }, */
	/**
	 * Function which will register basic events which will be used in quick create as well
	 */
	registerBasicEvents : function(container) {
		this._super(container);
		this.registerRecordPreSaveEvent(container);
		//this.registerEventForCopyingAddress(container);
		this.registerArea();
        //this.checkAccountRank(container);
		this.registerCheck(container);
        this.detailProcessingOperation();
		//this.registerReferenceSelectionEvent(container);
		//container.trigger(Vtiger_Edit_Js.recordPreSave, {'value': 'edit'});
	}
});