Vtiger_Edit_Js("Formdesign_Edit_Js",{},{
	
	registerEvents : function(){
		this._super();
		$.getScript('libraries/ueditor/formdesign/formdesign.js',function(){
			window.UEDITOR_CONFIG.contextMenu=[{label:'文本框', icon:'forecolor',cmdName:'text'},
											{label:'多行文本',icon:'justifyjustify',cmdName:'textarea'},
											{label:'下拉菜单', icon:'textmore',cmdName:'select'},
											{label:'单选框', icon:'selectall',cmdName:'radios'},
											{label:'复选框', icon:'checkbox',cmdName:'checkboxs'},
											{label:'列表', icon:'insertorderedlist',cmdName:'listctrl'}
											];
			
			
			window.UEDITOR_CONFIG.toolbars= [
			['fullscreen','source','undo','redo','bold','italic','underline','fontborder','strikethrough','removeformat','formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'selectall', 'cleardoc','inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol', 'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols']
		           ];
			var ckEditorInstance = new Vtiger_CkEditor_Js();
			ckEditorInstance.loadCkEditor("Formdesign_editView_fieldName_content");
		});
		
	}
});


