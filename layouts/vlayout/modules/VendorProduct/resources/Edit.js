/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("ProductProvider_Edit_Js",{},{
	ckEditorInstance:'',
	ckEInstance:'',
	rowSequenceHolder : false,

    registerRecordPreSaveEvent : function(form) {
        var editViewForm = this.getForm();
        var thisInstance = this;
        if(typeof form == 'undefined') {
            form = this.getForm();
        }
        editViewForm.on(Vtiger_Edit_Js.recordPreSave,function(e,data){
            // 不同的票据类型 判断是否为空
            var servicestartdate = $('input[name=servicestartdate]').val();
            var serviceenddate = $('input[name=serviceenddate]').val();
            if ((new Date(servicestartdate.replace(/-/g,'\/')))>(new Date(serviceenddate.replace(/-/g,'\/')))) {
				var  params = {text : app.vtranslate(),title : app.vtranslate('产品服务有效开始日期不能大于账户有效结束日期')};
				Vtiger_Helper_Js.showPnotify(params);
				e.preventDefault();
				return false;
            }

           	var productid=$('input[name="productid"]').val();
           	var vendorid=$('input[name="vendorid"]').val();
           	var idaccount=$('input[name="idaccount"]').val();
			var module = app.getModuleName();
			var postData={};
			postData.data = {
				"module": module,
				"action": "ChangeAjax",
				'mode': 'checkIdAndProductProvider',
				'record': $('input[name=record]').val(),
				"productid": productid,
				"vendorid": vendorid,
				"idaccount":idaccount
			}
			postData.async=false;
			var ajaxflag=false;
			var Message = app.vtranslate('正在验证...');
			var progressIndicatorElement = jQuery.progressIndicator({
				'message' : Message,
				'position' : 'html',
				'blockInfo' : {'enabled' : true}
			});
			AppConnector.request(postData).then(
				function(data){
					progressIndicatorElement.progressIndicator({'mode' : 'hide'});
					if(data.success) {
                        if(data.result.flag){
                            Vtiger_Helper_Js.showMessage({type:'error',text:'产品,供应商存在,不充许添加!'});
                            ajaxflag=true;
                        }
					}
				},
				function(error,err){

				}
			);
            if(ajaxflag){
                e.preventDefault();
                return false;
            }
        });
    },
	/**
		pop two calander.
	*/
	registerBasicEvents:function(container){
		this._super(container);
		this.registerRecordPreSaveEvent(container);
	}
});




















