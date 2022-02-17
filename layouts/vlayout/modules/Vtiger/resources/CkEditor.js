/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
jQuery.Class("Vtiger_CkEditor_Js",{},{
	
	/*loadCkEditor : function(element){
		//解决ajax加载js的问题
		$.getScript('/libraries/jquery/kindeditor/kindeditor-min.js',function(){
			var editor;
			if(typeof element=='undefined'){
				element="textarea[name=notecontent]";
			}
			$(element).css({'height':'300px','width':'500px'});


			KindEditor.basePath = '/libraries/jquery/kindeditor/';

					editor  = KindEditor.create(element,{
						resizeType : 1,
						allowPreviewEmoticons : false,
                        filterMode : false,
                        allowImageRemote:false,
						items : [
							'source','|','fontname', 'fontsize', 'wordpaste','|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
							'removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
							'insertunorderedlist', '|', 'emoticons', 'image', 'link','|','fullscreen']
					});


		});
		
	},*/
	/**
	 * 富文本编辑器统一调用方法
	 * @author wangbin 2015年6月23日 星期二 
	 * @param 多行文本框id
	 * @param 默认编辑器内容
	 * @return unknow
	 */
	loadCkEditor:function(element,contex){
		//$.getScript('/libraries/ueditor1_4_3-utf8-php/ueditor.all.js',function(){
		var ue = UE.getEditor(element,{
		 
		           autoHeightEnabled: true,
		           autoFloatEnabled: true,
		           initialFrameWidth:820,
		           initialFrameHeight:300,
		           autoHeightEnabled: false,
		           autoFloatEnabled: false,
		           elementPathEnabled:false,
		           wordCount:false 
		       });
		ue.ready(function(){
		if(contex){
			ue.setContent(contex);
		}
	});
	}
});
    
