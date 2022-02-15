/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("WorkSummarize_List_Js",{},{

    registerticketClickEvent: function(){
        var thisInstance = this;
        $('.checkall').live('click',function(){
            $('.reversecheck').attr('checked',false);
            if($(this).is(":checked")){
                $('.rmuser').attr('checked',true);
            }else{
                $('.rmuser').attr('checked',false);
            }
        });
        $('.reversecheck').live('click',function(){
            $('.checkall').attr('checked',false);
            $('.rmuser').each(function(){
                $(this).attr('checked',!this.checked);
            });
        });

        jQuery('body').on('submit','#findDuplicat',function(e){
            var fieldlist=new Array();
            $('.rmuser:checked').each(function(index,element){fieldlist.push($(this).val())});
            var params = {
                'module': app.getModuleName(),
                'view' : "FieldAjax",
                'mode':'updatesort',
                'fieldList':fieldlist
            };
             AppConnector.request(params).then(
                function(data) {
                    window.location.reload();
                },
                function(error,err){
                }
            );
            e.preventDefault();
        });
    },

registerEvents : function(){
	this._super();
    this.registerticketClickEvent();



}

});