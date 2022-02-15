Vtiger_Edit_Js("Approval_Edit_Js",{ },{
	registerBasicEvents : function(container) {
		this._super(container);
		this.init();
	},

	init: function() {
		$('select[name=createid]').next().find('.chzn-results').remove();
		$('#Approval_editView_fieldName_createtime').attr('disabled', 'disabled').val($('input[name=nowtime]').val());

	}
});