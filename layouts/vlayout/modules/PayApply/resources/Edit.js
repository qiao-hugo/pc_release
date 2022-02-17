/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("PayApply_Edit_Js",{},{
	ckEditorInstance:'',
	ckEInstance:'',
	rowSequenceHolder : false,

	/**********应收end*************/
	registerEvents: function (container) {
		this._super(container);
		this.parentProductsEvent();
		this.registerRecordPreSaveEvent();
	},

	/**
	 * steel 类型分类调用产品列表
	 */
	parentProductsEvent: function () {
		var thisInstance=this;
		$('form').on('change','select[name="parentcate"]', function () {
			// if(thisInstance.isElecLoadData()){
			// 	return false;
			// }
			$(this).siblings().not('#'+$(this).attr('id')+'_chzn').remove();
			if ($('select[name="parentcate"]').val() != "") {
				var parentcate = $('select[name="parentcate"]').val();  //请求异常处理，对字符进行编码
				var params = {
					'type': 'GET',
					'dataType': 'html',
					'data': 'module=PayApply&action=ChangeAjax&mode=getproductlist&parentcate=' + parentcate
				};
				AppConnector.request(params).then(
					function (data) {
						var selejson= $.parseJSON(data);
						$('select[name="soncate"]').remove();
						var selectprodcut='<select class="chzn-select" name="soncate" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"> <option value="">选择一个选项</option>';
						if (data == 'null' || selejson.result.length == 0) {
							selectprodcut+='</select>';
						} else {
							var option=''
							$.each(selejson.result,function(i,val){
								option+='<option value='+val[0]+'>'+val[1]+'</option>';
							});
							selectprodcut+=option+'</select>';

						}

						$('select[name="parentcate"]').siblings().not('#'+$('select[name="parentcate"]').attr('id')+'_chzn').remove();
						$('select[name="parentcate"]').parent().append(selectprodcut);
						$('.chzn-select').chosen();
					},
					function (error) {
					});
				//合同是T-云系列或者TSITE系列自动选择
			}
		})
	},

	registerRecordPreSaveEvent: function () {
		var thisInstance = this;
		var editViewForm = this.getForm();
		editViewForm.on(Vtiger_Edit_Js.recordPreSave,function(e,data) {
			if($("input[name='f']").val()==''){
				Vtiger_Helper_Js.showMessage({type:'error',text:'请填写申请有效时间【开始日期】!'});
				e.preventDefault();
				return false;
			}
			if($("input[name='s']").val()==''){
				Vtiger_Helper_Js.showMessage({type:'error',text:'请填写申请有效时间【结束日期】!'});
				e.preventDefault();
				return false;
			}
		});
	},
});




















