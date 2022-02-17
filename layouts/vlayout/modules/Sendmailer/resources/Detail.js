/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("Sendmailer_Detail_Js",{
    cache : {},

    //Holds detail view instance
    detailCurrentInstance : false,

    /*
     * function to trigger Convert Lead action
     * @param: Convert Lead url, currentElement.
     */
    sendallmail : function(convertLeadUrl, buttonElement) {
        var instance = Sendmailer_Detail_Js.detailCurrentInstance;
        //Initially clear the elements to overwtite earliear cache
        instance.convertLeadContainer = false;
        instance.convertLeadForm = false;
        instance.convertLeadModules = false;
        var progressIndicatorElement = jQuery.progressIndicator({
            'message' : '实时发送邮件可能要等很长一段时间,请耐心等待哟',
            'position' : 'html',
            'blockInfo' : {'enabled' : true}
        });
        //$('#dialog-message').after('<div class="bootbox modal fade bootbox-confirm in" tabindex="-1" role="dialog" aria-hidden="false"><div class="modal-dialog" style="width: 400px;height:500px;"><div class="modal-content"><div class="modal-body"><div class="bootbox-body">正在发送请稍后</div></div><div class="modal-footer" style="height:400px;" id="receivemessage"></div></div></div></div><div class="modal-backdrop fade in"></div>');
        //return;
        AppConnector.request(convertLeadUrl).then(
            function(data) {
                progressIndicatorElement.progressIndicator({
                    'mode' : 'hide'
                });
                if(data) {
                    //$('#dialog-message').after(data)
                    var  params = {
                        text : app.vtranslate(data.result.msg),
                        title : app.vtranslate('')
                    }
                    Vtiger_Helper_Js.showPnotify(params);
                    setTimeout(window.location.reload(),5000);
                    //Leads_Detail_Js.cache = data;
                    //instance.displayConvertLeadModel(data, buttonElement);
                }
            },
            function(error,err){

            }
        );

    }
    /*writemessage:function($msg){
        $('#receivemessage').append($msg);
    }*/
},{

    sendEmailnow:function(){
        $('#tbl_ServiceAssignRule_Account_Detail').on('click','.nowsend',function(){
            var message = '您确定要发送吗?';
            var msg={
                'message':message,
                "width":"400px"
            };
            var postData = {
                "module": app.getModuleName(),
                "action": "SelectAjax",
                "record": jQuery('#recordId').val(),
                "sendid": jQuery(this).data("id"),
                "inorout":jQuery('#inorout').val(),
                "mode":"sendemail"
            }
            var progressIndicatorElement = jQuery.progressIndicator({
                'message' : '正在处理',
                'position' : 'html',
                'blockInfo' : {'enabled' : true}
            });
            //发送请求
            AppConnector.request(postData).then(
                function(data){
                    progressIndicatorElement.progressIndicator({
                        'mode' : 'hide'
                    });
                    var  params = {
                        text : app.vtranslate(data.result.msg),
                        title : app.vtranslate('')
                    }
                    Vtiger_Helper_Js.showPnotify(params);

                },
                function(error){

                }
            );

        });
    },
    installing:function(){
        UE.getEditor('editView_fieldName_body',{
                toolbars: [
                ],
                autoFloatEnabled: false,
                initialFrameWidth:'100%',
                initialFrameHeight:200,
                autoHeightEnabled: false,
                autoFloatEnabled: false,
                elementPathEnabled:false,
                wordCount:false,
                readonly:true
            });

    },
	
	registerEvents:function(){
		this._super();
        this.installing();
        this.sendEmailnow();
	}
});