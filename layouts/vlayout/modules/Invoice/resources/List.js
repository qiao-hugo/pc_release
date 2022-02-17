/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("Invoice_List_Js",{},{
    tickecthandle : function(recordId,type,messge) {
        var listInstance = Vtiger_List_Js.getInstance();
        var message = app.vtranslate(messge);
        Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
            function(e) {
                var module = app.getModuleName();
                var postData = {
                    "module": module,
                    "action": "Invoicehandle",
                    "record": recordId,
                    "type":type
                }
                var deleteMessage = app.vtranslate('处理中......');
                var progressIndicatorElement = jQuery.progressIndicator({
                    'message' : deleteMessage,
                    'position' : 'html',
                    'blockInfo' : {
                        'enabled' : true
                    }
                });
                AppConnector.request(postData).then(
                    function(data){
                        progressIndicatorElement.progressIndicator({
                            'mode' : 'hide'
                        })
                        $('#returnTicket'+recordId).remove();
                        $('#toVoid'+recordId).remove();
                        var newtype=(type=='tovoid')?'作废':'退票';
                        $('.invoicestatus'+recordId).text(newtype);
                        /*
                        if(data.success) {
                            var orderBy = jQuery('#orderBy').val();
                            var sortOrder = jQuery("#sortOrder").val();
                            var urlParams = {
                                "viewname": data.result.viewname,
                                "orderby": orderBy,
                                "sortorder": sortOrder
                            }
                            jQuery('#recordsCount').val('');
                            jQuery('#totalPageCount').text('');
                            listInstance.getListViewRecords(urlParams).then(function(){
                                listInstance.updatePagination();
                            });
                        } else {
                            var  params = {
                                text : app.vtranslate(data.error.message),
                                title : app.vtranslate('JS_LBL_PERMISSION')
                            }
                            Vtiger_Helper_Js.showPnotify(params);
                        }
                        */
                    },
                    function(error,err){

                    }
                );
            },
            function(error, err){
            }
        );
    },
    registerticketClickEvent: function(){
        var thisInstance = this;
        var listViewContentDiv = this.getListViewContentContainer();
        listViewContentDiv.on('click','.toVoidButton',function(e){
            var elem = jQuery(e.currentTarget);
            var recordId = elem.closest('tr').data('id');
            thisInstance.tickecthandle(recordId,'tovoid','您确定要作废该发票码?');
            e.stopPropagation();
        });
        listViewContentDiv.on('click','.returnTicketButton',function(e){
            var elem = jQuery(e.currentTarget);
            var recordId = elem.closest('tr').data('id');
            thisInstance.tickecthandle(recordId,'returnticket','您确定该发票要退票码?');
            e.stopPropagation();
        });
    },

registerEvents : function(){
	this._super();
    this.registerticketClickEvent();
	//this.Tableinstance();
	//this.BarLinkRemove();
	//this.ActiveClick();
	//this.registerLoadAjaxEvent();

}

});