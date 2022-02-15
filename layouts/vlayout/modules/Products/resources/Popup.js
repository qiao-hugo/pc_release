/*+***********************************************************************************
 * 产品选择处理
 **********/
Vtiger_Popup_Js("Products_Popup_Js",{
	
},{

	
	registerSelectButton : function(){
		var popupPageContentsContainer = this.getPopupPageContainer();
		var thisInstance = this;
		popupPageContentsContainer.on('click','button.select', function(e){
			var tableEntriesElement = popupPageContentsContainer.find('table');
			var selectedRecordDetails = {};
			var recordIds = new Array();
			var srcmodule=$('#srcmodule').val();
			var dataUrl;
		if(srcmodule=='ServiceContracts' || srcmodule=='SalesOrder'){
			jQuery('input.entryCheckBox').each(function(index, checkBoxElement){
				var checkBoxJqueryObject = jQuery(checkBoxElement)
				if(! checkBoxJqueryObject.is(":checked")){
					return true;
				}
				var row = checkBoxJqueryObject.closest('.listViewEntries');
				var id = row.data('id');
				recordIds.push(id);
				var name = row.data('name');
				dataUrl = row.data('url');
				selectedRecordDetails[id] = {'name' : name};
			});

		}else{
			jQuery('input.entryCheckBox', tableEntriesElement).each(function(index, checkBoxElement){
				var checkBoxJqueryObject = jQuery(checkBoxElement)
				if(! checkBoxJqueryObject.is(":checked")){
					return true;
				}
				var row = checkBoxJqueryObject.closest('tr');
				var id = row.data('id');
				recordIds.push(id);
				var name = row.data('name');
				dataUrl = row.data('url');
				selectedRecordDetails[id] = {'name' : name};
			});
		}
			var jsonRecorIds = JSON.stringify(recordIds);
			if(Object.keys(selectedRecordDetails).length <= 0) {
				alert(app.vtranslate('JS_PLEASE_SELECT_ONE_RECORD'));
			}else{
				if(typeof dataUrl != 'undefined'){
				    dataUrl = dataUrl+'&idlist='+jsonRecorIds+'&currency_id='+jQuery('#currencyId').val();
				    AppConnector.request(dataUrl).then(
					function(data){
						for(var id in data){
						    if(typeof data[id] == "object"){
							var recordData = data[id];
						    }
						}
						var recordDataLength = Object.keys(recordData).length;
						if(recordDataLength == 1){
							recordData = recordData[0];
						}
						thisInstance.done(recordData, thisInstance.getEventName());
						e.preventDefault();
					},
					function(error,err){

					}
				);
				}else{
				    thisInstance.done(selectedRecordDetails, thisInstance.getEventName());
				}
			}
		});
        var srcmodule=$('#srcmodule').val();
        //steel加入平台账户弹出操作
        if(srcmodule=='AccountPlatform' || srcmodule=='ProductProvider') {
            popupPageContentsContainer.on('click', '.listViewEntries', function (e) {
                thisInstance.getListViewEntries(e);
            });
        }
	},

	
	registerEvents: function(){
		/*var pageNumber = jQuery('#pageNumber').val();
		if(pageNumber == 1){
			jQuery('#listViewPreviousPageButton').attr("disabled", "disabled");
		}
		this.registerEventForSelectAllInCurrentPage();*/
		this.registerSelectButton();
		/*this.registerEventForCheckboxChange();
		this.registerEventForSearch();
		this.registerEventForSort();
		this.registerEventForListViewEntries();
		//this.triggerDisplayTypeEvent();
		var popupPageContainer = jQuery('#popupPageContainer');
		if(popupPageContainer.length > 0){
			this.registerEventForTotalRecordsCount();
			this.registerEventForPagination();
			jQuery('.pageNumbers').tooltip();
		}*/
	}
});
