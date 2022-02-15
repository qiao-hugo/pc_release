/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("Knowledge_Edit_Js",{},{
	ckEditorInstance:'',
	ckEInstance:'',
	rowSequenceHolder : false,
	registerReferenceSelectionEvent : function(container) {
		this._super(container);
		var thisInstance = this;
	},

	//加载ckeditor
	registerEventForCkEditor : function(){
		var form = this.getForm();
		
		/* var ckEditorInstance = new Vtiger_CkEditor_Js();
		ckEditorInstance.loadCkEditor('Knowledge_editView_fieldName_knowledgecontent');
        ckEditorInstance.loadServerConfig(); */
        var ue = UE.getEditor('Knowledge_editView_fieldName_knowledgecontent',{
            ///toolbars: [['fullscreen', 'source', 'undo', 'redo', 'bold', 'italic', 'underline', 'fontborder',  'inserttable', 'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'selectall', 'cleardoc']],
            toolbars: [['source','undo', 'redo','|','bold','indent','italic','underline','strikethrough','fontborder','superscript','subscript','touppercase','tolowercase','blockquote','pasteplain','selectall','horizontal','unlink','formatmatch','removeformat','|','simpleupload','insertimage','|','inserttable','insertrow','insertcol','mergeright','mergedown','deleterow','deletecol','splittorows','splittocols','splittocells','deletecaption','inserttitle','mergecells','edittable','edittd','deletetable','insertparagraphbeforetable','cleardoc','|','justifyleft','justifyright','justifycenter','justifyjustify','imagenone','imageleft','imageright','imagecenter','|','directionalityltr','directionalityrtl','rowspacingtop','rowspacingbottom','lineheight','forecolor','backcolor','insertorderedlist','insertunorderedlist','fullscreen','edittip ','customstyle','autotypeset','time','date']],
            initialFrameWidth:'100%',
            initialFrameHeight:200,
            autoHeightEnabled: false,
            autoFloatEnabled: true,
            elementPathEnabled:false,
            disabledTableInTable:false,
            wordCount:false,
            catchRemoteImageEnable:true
        });
        ue.loadServerConfig();
		
	},
	registerEvents: function(){
		this._super();
		this.registerEventForCkEditor();
		
	}
});


