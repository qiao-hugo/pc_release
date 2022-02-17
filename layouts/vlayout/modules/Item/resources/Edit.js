/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("Item_Edit_Js",{},{
	submitFlag : false,
	/**
		pop two calander.
	*/
	registerBasicEvents:function(container){
		this._super(container);
		this.registerRecordPreSaveEvent();
	},



	registerRecordPreSaveEvent: function () {
		var editViewForm = this.getForm();
		var thisInstance = this;
		editViewForm.on(Vtiger_Edit_Js.recordPreSave,function(e,data){
			if(thisInstance.submitFlag) return true;
			//查询数据库中是否可以更改
			var params = {
				'module': 'Item',
				'action': 'BasicAjax',
				'mode': 'hasRepeatSubItem',
				'parentcate':$("input[name='parentcate']").val(),
				'soncate':$("input[name='soncate']").val(),
			};
			AppConnector.request(params).then(
				function(data){
					console.log(data);
					if(data.result.flag){
						thisInstance.submitFlag=true;
						editViewForm.submit();
						return true;
					}else{
						e.preventDefault();
						Vtiger_Helper_Js.showMessage({type:'error',text:'此项目大项中已有相同的项目小项，请重新填写项目小类'});
						return false;
					}
				}
			);
			return  false;
		});
	},

});




















