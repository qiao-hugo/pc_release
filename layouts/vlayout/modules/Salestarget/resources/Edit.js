Vtiger_Edit_Js("Salestarget_Edit_Js",{ },{
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

    relatedchange: function () {
        var sparams = {
            'module': 'Salestarget',
            'action': 'BasicAjax',
            'record': $('select[name="businessid"]').val(),
            'mode': 'getBusinessUser'
        };
        AppConnector.request(sparams).then(
            function (datas) {
                if (datas.success == true) {
                	var d = datas.result;
                	$('#Salestarget_editView_fieldName_department').val(datas.result.departmentname);
                	$('#Salestarget_editView_fieldName_entrydate').val(datas.result.date_entered);
                }
            }
        );
    },

    // 验证
    checkPara: function() {
    	var invitationtarget = $('input[name=invitationtarget]').val();
    	if(isNaN(invitationtarget)  ||  parseInt(invitationtarget) <= 0) {  // 不是数字
    		var  params = {text : '邀约目标必须数字大于0', title : '错误提示'};
			Vtiger_Helper_Js.showPnotify(params);
    		return false;
    	} 

    	var achievementtargt = $('input[name=achievementtargt]').val();
    	if(isNaN(achievementtargt)  ||  parseInt(achievementtargt) <= 0) {  // 不是数字
    		var  params = {text : '业绩目标必须数字大于0', title : '错误提示'};
			Vtiger_Helper_Js.showPnotify(params);
    		return false;
    	}


    	var visittarget = $('input[name=visittarget]').val();
    	if(isNaN(invitationtarget)  ||  parseInt(visittarget) <= 0) {  // 不是数字
    		var  params = {text : '拜访目标必须数字大于0', title : '错误提示'};
			Vtiger_Helper_Js.showPnotify(params);
    		return false;
    	}

    	return true;
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
			if (!thisInstance.checkPara() ) {
				return false;
			}
			if (thisInstance.checkMainTableEdit()) {
				return false;
			}
			
			var year = $('input[name=year]').val();
			var month = $('input[name=month]').val();
			var businessid = $('select[name=businessid]').val();
			var recordId = $('input[name=record]').val();

			var postData = {
					"module": 'Salestarget',
					"action": "BasicAjax",
					"record": recordId,
					'mode': 'isCheckTow',
					'year': year,
					'month': month,
					'businessid': businessid
				};
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
		var year = $('#Salestarget_editView_fieldName_year').val();
		var month = $('#Salestarget_editView_fieldName_month').val();
		var last_name = $('#Salestarget_editView_fieldName_createid').val();
		if (!year) {
			$('#Salestarget_editView_fieldName_year').val($('input[name=defa_year]').val());
		}
		if (!month) {
			$('#Salestarget_editView_fieldName_month').val($('input[name=defa_month]').val());
		}

		$('#Salestarget_editView_fieldName_createid').val($('input[name=last_name]').val());
		
		$('#Salestarget_editView_fieldName_year').attr('readonly', 'readonly');
		$('#Salestarget_editView_fieldName_month').attr('readonly', 'readonly');
		$('#Salestarget_editView_fieldName_createid').attr('readonly', 'readonly');
		$('#Salestarget_editView_fieldName_department').attr('readonly', 'readonly');
		$('#Salestarget_editView_fieldName_entrydate').attr('readonly', 'readonly');

		$('#Salestarget_editView_fieldName_visitrate').attr('readonly', 'readonly');
		$('#Salestarget_editView_fieldName_invitationrate').attr('readonly', 'readonly');
		$('#Salestarget_editView_fieldName_achievementrate').attr('readonly', 'readonly');

		if ($('input[name=main_ismodify').val() == '1') {
			this.notEditSales();
		}

		$('select[name=createid]').next().find('.chzn-results').remove(); // 填写人员不可修改

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

	// 判断周报表 是否能修改
	checkWeekTableEdit: function() {
		$('.weektable').each(function() {
			var v = $(this).find('input[name=weekismodify]').val();
			if (v == 1) {  // 不可修改
				$(this).find('input, textarea').attr('readonly', 'readonly');
			}
		});
	},
	checkMainTableEdit: function() {
		if ( $('input[name=main_ismodify]').val() == '1' ) {
			var  params = {text : '销售目标不可修改，修改周销售目标请点击提交', title : '提示'};
			Vtiger_Helper_Js.showPnotify(params);
			return true;
		}
		return false;
	},

	// 周销售目标 提交
	weebSalesSubmit: function() {
		var me = this;
		$('.weebSalesSubmit').on('click', function() {
			var recordid = $('input[name=record]').val();
			if (!recordid) {  //如果不是修改的话
				var  params = {text : '请点击保存按钮', title : '提示'};
				Vtiger_Helper_Js.showPnotify(params);
				return false;
			} 

		
			var data = {};
			data['salestargetid'] = recordid;

			var $curr_weektable = $(this).parents('.weektable');
			$curr_weektable.find('input,textarea').each(function() {
				var k = $(this).attr('t-name');
				var v = $(this).val();
				if (k) {
					data[k] = v;
				}
			});
			if (data['weekismodify']!='1' && data['salestargetdetailid']) {
				if (data['weekinvitationtarget'] <= 0 || data['weekvisittarget'] <= 0 || data['weekachievementtargt'] <= 0) {
					var  params = {text : '计划邀约目标、计划拜访目标、计划业绩目标必须大于0', title : '提示'};
					Vtiger_Helper_Js.showPnotify(params);
					return false;
				}
				data['mode'] = 'weebSalesSubmit';
				data['action'] = 'BasicAjax';
				data['module'] = 'Salestarget';

				AppConnector.request(data).then(
					function(data){
						if(data.success) {
							var result = data['result'];
							if (result.success) {
								var  params = {text : result.message, title : '提示'};
								Vtiger_Helper_Js.showMessage(params);
								$curr_weektable.find('input[name=weekismodify]').val(1);
								$('input[name=main_ismodify]').val(1);
								me.notEditSales();
								//location.reload();
								$curr_weektable.find('input,textarea').each(function() {
									$(this).attr('readonly', 'readonly');
								});
							}
						}
					},
					function(error,err){}
				);


			} else {
				var  params = {text : '周销售目标提交后不能修改', title : '提示'};
				Vtiger_Helper_Js.showPnotify(params);
			}
		});
	},

	weebSalesReturn : function() {
		$('.weebSalesReturn').on('click', function() {
			var data = {};

			var $curr_weektable = $(this).parents('.weektable');
			var salestargetdetailid = $curr_weektable.find('input[t-name=salestargetdetailid]').val();
			var data = {};
			data['mode'] = 'weebSalesReturn';
			data['action'] = 'BasicAjax';
			data['module'] = 'Salestarget';
			data['salestargetdetailid'] = salestargetdetailid;

			AppConnector.request(data).then(
				function(data){
					if(data.success) {
						var result = data['result'];
						if (result.success) {
							var  params = {text : result.message, title : '提示'};
							Vtiger_Helper_Js.showMessage(params);
							location.reload();
						}
					}
				},
				function(error,err){}
			);
		});
	},

	registerBasicEvents : function(container) {
		this._super(container);
		this.registerRecordPreSaveEvent(container);
		this.registerReferenceSelectionEvent(container);
		this.init();
		this.addrechargesheets();
		this.deleteWeekTable();
		this.checkWeekTableEdit();
		this.weebSalesSubmit();
		this.weebSalesReturn();
	}
});