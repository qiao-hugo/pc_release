Vtiger_Edit_Js("Staffcapacity_Edit_Js",{ },{
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
	registerReferenceSelectionEvent: function (container) {
        this._super(container);
        var thisInstance = this;

        //2016-9-20 点击商务人员带出 商务人员的信息
        $('select[name=businessid]').change(function() {
        	thisInstance.relatedchange();
        });

        
    },
    //
    relatedchange: function () {
        var sparams = {
            'module': 'Staffcapacity',
            'action': 'BasicAjax',
            'record': $('select[name="businessid"]').val(),
            'mode': 'getBusinessUser'
        };
        AppConnector.request(sparams).then(
            function (datas) {
                if (datas.success == true) {
                	var d = datas.result;
                	$('#Staffcapacity_editView_fieldName_department').val(datas.result.departmentname);
                	$('#Staffcapacity_editView_fieldName_entertime').val(datas.result.date_entered);
                }
            }
        );
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
			var businessid = $('select[name=businessid]').val();
			var recordId = $('input[name=record]').val();

			var postData = {
					"module": 'Staffcapacity',
					"action": "BasicAjax",
					"record": recordId,
					'mode': 'isCheckTow',
					'businessid': businessid
				};
			if (!recordId) {
				if (!thisInstance.flag) {
					AppConnector.request(postData).then(
						function(data){
							if(data.success) {
								var result = data['result'];
								if (result.is_check == 1) {
									var  params = {text : result.message, title : '错误提示'};
									Vtiger_Helper_Js.showPnotify(params);
								} else {
									thisInstance.flag = true;
									form.submit();
								}
							} else {
								return false;
							}
						},
						function(error,err){

						}
					);
					e.preventDefault();
				}
			}
		});
	},


	//weekTable添加
    addrechargesheets:function() {
        $('.insertbefore').on('click',function(){
            var index = $(this).attr('lang');
            $('.week' + index).show();
        });
    },

    deleteWeekTable: function() {
    	$('.deleteTable').on('click', function() {
    		$(this).parents('table').hide();
    	});
    },

	//等级字段无需页面编辑 废弃 By Joe 
	/*checkAccountRank:function(form){	accountrank=$('select[name="accountrank"]').val();	if($('input[name="record"]').val()>1 && accountrank.indexOf('_isv')>0 ){	$('select[name="accountrank"] option').each(function () {	var that=$(this);	if(that.val().indexOf('_isv')<0){	that.attr("disabled", "disabled");	$('.chzn-results>li').each(function () {	if($(this).text()==that.text()){	$(this).remove();	}	});	} 	});	} }, */
	/**
	 * Function which will register basic events which will be used in quick create as well
	 */
	init: function() {
		// 界面初始化
		// 月份
		$('input[name=department]').attr('readonly', 'readonly');
		$('input[name=entertime]').attr('readonly', 'readonly');

		/*var date = $('input[name=now_date]').val();
		$('#Staffcapacity_editView_fieldName_querylegalpersondate').val(date);
		$('#Staffcapacity_editView_fieldName_querylegalpersondate').datetimepicker('setStartDate',date);
		$('#Staffcapacity_editView_fieldName_querylegalpersondate').datetimepicker('setEndDate',date);


		$('#Staffcapacity_editView_fieldName_receiveinformationdate').val(date);
		$('#Staffcapacity_editView_fieldName_receiveinformationdate').datetimepicker('setStartDate',date);
		$('#Staffcapacity_editView_fieldName_receiveinformationdate').datetimepicker('setEndDate',date);


		$('#Staffcapacity_editView_fieldName_businessinquirydate').val(date);
		$('#Staffcapacity_editView_fieldName_businessinquirydate').datetimepicker('setStartDate',date);
		$('#Staffcapacity_editView_fieldName_businessinquirydate').datetimepicker('setEndDate',date);*/
		var record = $('input[name=record]').val();
		if (!record) {
			this.relatedchange();
		}
		
	},

	// 销售主目标不可修改
	notEditSales: function() {
		$('input[name=invitationtarget]').attr('readonly', 'readonly');
		$('input[name=achievementtargt]').attr('readonly', 'readonly');
		$('input[name=visittarget]').attr('readonly', 'readonly');
		$('textarea[name=remarks]').attr('readonly', 'readonly');
		$(".chzn-results").remove();
	},




	registerBasicEvents : function(container) {
		this._super(container);
		this.registerRecordPreSaveEvent(container);
		this.registerReferenceSelectionEvent(container);
		this.init();
	}
});