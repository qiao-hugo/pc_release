/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("WorkflowStages_Edit_Js", {} ,{
	registerEventForChose:function(){
        /*
		$('input[name="isproductmanger"]').on('click',function(){
			var roleselect = $('select[name="isrole"]').val();
			if($(this).attr('checked')&&roleselect!=''){
				$(this).removeAttr('checked');
					var params={
					type: 'notice',
					text:'产品负责人和角色只能选择一个'
					};
					Vtiger_Helper_Js.showMessage(params);
			}
		});
		
		$('select[name="isrole"]').on('change',function(){
			var isproduct=$('input[name="isproductmanger"]:visible').attr("checked");
			//console.log(isproduct);
			if(isproduct=='checked'&&$(this).val()!=''){
				$(this).val('');
				var handler=$(this).closest('span');
				
				handler.find('.chzn-results li').each(function(){
					$(this).removeClass('result-selected');
				});
				handler.find('.chzn-results li').eq(0).addClass('result-selected');
				handler.find('span').text('请选择角色');
				var params={
					type: 'notice',
					text:'产品负责人和角色只能选择一个'
					};
				Vtiger_Helper_Js.showMessage(params);
			}
			//console.log($(this).val());
		});*/
		
	},
/* 	registerSubmitEvent:function(){
		
		var editViewForm = this.getForm();

		editViewForm.submit(function(e){
			var isproduct=$('input[name="isproductmanger"]:visible').attr("checked");
			var roleselect = $('select[name="isrole"]').val();
			
			if(roleselect==''&&typeof isproduct=='undefined'){
				var params={
						type: 'error',
						text:'产品负责人和角色必须选择其中一个'
						};
				Vtiger_Helper_Js.showMessage(params);
				e.preventDefault();
			}
			//this._super();
			
		});
		
		//Vtiger_Edit_Js.registerSubmitEvent();
	}, */
	
	
	registerRecordPreSaveEvent : function() {
	
	/* var editViewForm = this.getForm();

		editViewForm.submit(function(e){
			var isproduct=$('input[name="isproductmanger"]:visible').attr("checked");
			var roleselect = $('select[name="isrole"]').val();
			
			if(roleselect==''&&typeof isproduct=='undefined'){
				var params={
						type: 'error',
						text:'产品负责人和角色必须选择其中一个'
						};
				Vtiger_Helper_Js.showMessage(params);
				e.preventDefault();
			}
			//this._super();
			
		}); */
		$('#EditView').on(Vtiger_Edit_Js.recordPreSave, function(e, data) {
			var isproduct=$('input[name="isproductmanger"]:visible').attr("checked");
			var roleselect = $('select[name="isrole"]').val();
			
			if(roleselect=='' && typeof isproduct=='undefined'){
					var params={
						type: 'error',
						text:'产品负责人和角色必须选择其中一个'
						};
					Vtiger_Helper_Js.showPnotify(params);
				}else{
					return true;
					//$('#EditView').submit();
				}
				
				
            e.preventDefault();
		})
	},
	registerEvents : function() {
		this._super();
		//this.registerEventForChose();
		//this.registerRecordPreSaveEvent();
	}
});


