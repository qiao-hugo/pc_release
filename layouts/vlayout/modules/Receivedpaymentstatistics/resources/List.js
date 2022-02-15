Vtiger_List_Js("Receivedpaymentstatistics_List_Js",{
	
},{
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
			'__vtrftk':$('input[name="__vtrftk"]').val(),
			'module': module,
			'parent' : parent,
			'page' : pageNumber,
			'view' : "List",
			'viewname' : cvId,
			'orderby' : orderBy,
			'sortorder' : sortOrder,
			'public' : pub,
			'filter' :filter,
			'department':DepartFilter,
			'accountsname': $("input[name ='accountsname']").val(),
			'smown':$('select[name="smowen"]').val()
		}

        var searchValue = this.getAlphabetSearchValue();

        if((typeof searchValue != "undefined") && (searchValue.length > 0)) {
            params['search_key'] = this.getAlphabetSearchField();
            params['search_value'] = searchValue;
            params['operator'] = "s";
        }
		return params;
	},
	registerEvents : function(){
		this._super();
	}
});