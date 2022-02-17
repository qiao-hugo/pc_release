
Vtiger_List_Js("Vtiger_PopupList_Js",{
    getInstance: function(){
		var instance = new Vtiger_PopupList_Js();
	    return instance;
	}

},{
	getDefaultParams : function() {
		var pageNumber = jQuery('#pageNumber').val();
		var module = app.getModuleName();
		var parent = app.getParentModuleName();
		var cvId = this.getCurrentCvId();
		var orderBy = jQuery('#orderBy').val();
		var sortOrder = jQuery("#sortOrder").val();
		var pub = jQuery('#public').val();
		var filter=jQuery('#filter').val();
		var DepartFilter=jQuery('#DepartFilter').val();
        var search_key=jQuery('#searchableColumnsList').val();
        var search_value = jQuery('#searchvalue').val();

		var params = {
			//'__vtrftk':$('input[name="__vtrftk"]').val(),
			'module': module,
			'parent' : parent,
			'page' : pageNumber,
			'view' : "PopupAjax",
            "search_key":search_key,
            "search_value":search_value,
			'viewname' : cvId,
			'orderby' : orderBy,
			'sortorder' : sortOrder,
			'public' : pub,
			'filter' :filter,
			'department':DepartFilter

		}

        var searchValue = this.getAlphabetSearchValue();

        if((typeof searchValue != "undefined") && (searchValue.length > 0)) {
            params['search_key'] = this.getAlphabetSearchField();
            params['search_value'] = searchValue;
            params['operator'] = "s";
        }
		return params;
	},
});


jQuery(document).ready(function() {
	$("#listViewPageJump").css("display","none");
	var PopupList = Vtiger_PopupList_Js.getInstance();
	PopupList.registerEvents();
});