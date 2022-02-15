/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/


Vtiger_Detail_Js("Knowledge_Detail_Js",{},{
	loadCkEditor:function(element){
		//$.getScript('/libraries/ueditor1_4_3-utf8-php/ueditor.all.js',function(){
		var ue = UE.getEditor(element,{
		    toolbars: [
		           ],
		           autoFloatEnabled: false,
		           initialFrameWidth:'100%',
		           //initialFrameHeight:200,
		           autoHeightEnabled: true,
		           autoFloatEnabled: false,
		           elementPathEnabled:false,
		           wordCount:false,
                   readonly:true
		       });
	},
	registerEventForCkEditor : function(){
		var form = this.getForm();
		this.loadCkEditor('contentnote');
		//延迟一秒待元素生成后再查找
        //延迟一秒待元素生成后再查找
        setTimeout("$('#ueditor_0').contents().find('body').on('mousedown',function(){return false;});$('#ueditor_0').contents().find('body').on('mouseup',function(){return false;});$('#ueditor_0').contents().find('body').css({'-moz-user-select': 'none','-khtml-user-select': 'none','user-select': 'none','cursor':'pointer'});",1000);

		
	},
		registerEvents : function(){
			
			this._super();
			$(".details").addClass('span12').removeClass('span10');
			this.registerEventForCkEditor();
			
			}
});	
	