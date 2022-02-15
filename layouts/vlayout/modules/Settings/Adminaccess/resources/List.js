

Settings_Vtiger_List_Js("Settings_Adminaccess_List_Js",{
	
	triggerDelete : function(event,url){
		event.stopPropagation();
		var instance = Vtiger_List_Js.getInstance();
		instance.DeleteRecord(url);
	}
},{
	
	/*
	 * Function to register the list view delete record click event
	 */
	DeleteRecord: function(url){
		var thisInstance = this;
		var css = jQuery.extend({'text-align' : 'left'},css);
		
		AppConnector.request(url).then(
			function(data) {
				if(data) {
					window.location.reload();
				}
			},
			function(error,err){

			}
		);
	},
	
	
	registerEvents : function() {
		//this.triggerDisplayTypeEvent();
		this.registerRowClickEvent();
		this.registerHeadersClickEvent();
		this.registerPageNavigationEvents();
		this.registerEventForTotalRecordsCount();
		jQuery('.pageNumbers').tooltip();
	}
});