/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("Workflows_Edit_Js", {} ,{
	/**
	 * Function to register event for ckeditor for description field
	 */
	registerEventForCkEditor : function(){
		var form = this.getForm();
		var noteContentElement = form.find('[name="notecontent"]');
		if(noteContentElement.length > 0){
			var ckEditorInstance = new Vtiger_CkEditor_Js();
			ckEditorInstance.loadCkEditor(Workflows_editView_fieldName_notecontent);
		}
	},
    checkboxSelct:function(){
        //steel 2015-05-13
        //更改复选框可能性选择,分为全不选或是只能选择一项
        // young 2015-05-20 加入产品合同二选一的限制
        $(':checkbox[name=iscontent],[name=iscontract]').click(function(){
            //加上下面这句只能勾选一项
            $(this).attr('checked',true);
            if($(this).attr('checked')){
                $(':checkbox[name=iscontent],[name=iscontract]:checked').not(this).attr('checked',false);
            }
        });
    },
	registerEvents : function() {
		this._super();
		this.registerEventForCkEditor();
        this.checkboxSelct();
	}
});


