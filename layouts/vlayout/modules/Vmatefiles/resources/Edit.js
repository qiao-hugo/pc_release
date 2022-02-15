Vtiger_Edit_Js("Vmatefiles_Edit_Js",{ },{
    //Stored history of account name and duplicate check result
	duplicateCheckCache : {},
	//This will store the editview form
	editViewForm : false,
	//Address field mapping within module
	addressFieldsMappingInModule : {'bill_street':'ship_street','bill_pobox':'ship_pobox','bill_city':'ship_city','bill_state':'ship_state','bill_code'	:'ship_code','bill_country':'ship_country'},							
	/**
	 * This function will return the current form
	 */


	//等级字段无需页面编辑 废弃 By Joe 
	/*checkAccountRank:function(form){	accountrank=$('select[name="accountrank"]').val();	if($('input[name="record"]').val()>1 && accountrank.indexOf('_isv')>0 ){	$('select[name="accountrank"] option').each(function () {	var that=$(this);	if(that.val().indexOf('_isv')<0){	that.attr("disabled", "disabled");	$('.chzn-results>li').each(function () {	if($(this).text()==that.text()){	$(this).remove();	}	});	} 	});	} }, */
	/**
	 * Function which will register basic events which will be used in quick create as well
	 */
	init: function() {
		// 界面初始化
		// 月份
        $('select[name=filestate]').next().find('.chzn-results').remove(); // 填写人员不可修改

    
	},

	registerRecordPreSaveEvent : function(form) {
		var editViewForm = this.getForm();
		var thisInstance = this;
		if(typeof form == 'undefined') {
			form = this.getForm();
		}

		editViewForm.on(Vtiger_Edit_Js.recordPreSave,function(e,data){
			if($("select[name='style-select']").val()=='files_style9') {
				var prefix = $("#path").val().split('.').pop().toLowerCase();
				if (prefix != 'pdf') {
					alert("vmate的附件类型必须上传pdf文件");
					return false;
				}
				return true;
			}
			return false;
		});
	},




	registerBasicEvents : function(container) {
		this._super(container);
		this.registerRecordPreSaveEvent();
		this.init();
	}
});