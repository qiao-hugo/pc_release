/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("SContractNoGeneration_Edit_Js",{},{
	ckEditorInstance:'',
	ckEInstance:'',
    products_flag:false,
	rowSequenceHolder : false,
	registerReferenceSelectionEvent : function(container) {
		this._super(container);
		var thisInstance = this;
		//wangbin 2015-1-13 修改之前拜访单关联列表,input获取name值有所变化.
		jQuery('input[name="sc_related_to"]', container).on(Vtiger_Edit_Js.referenceSelectionEvent, function (e,data){accountlist(data['source_module']);});
		function accountlist(sourcemodule){	

				var servicecontractsruleid=$('input[name="sc_related_to"]').val();
				if(servicecontractsruleid.length>0){
					thisInstance.loadWidgetNote(servicecontractsruleid);
				}

		}	
	},
	loadWidgetNote : function(id){
        var thisInstance = this;
		$servicecontractsruleid=id;
		var params={};
		params['servicecontractsruleid'] =$servicecontractsruleid ;                  //公司的id
		params['company_code'] =$('select[name="company_code"]').find('option:selected').val() ;                  //公司的id
		params['products_code'] =$('select[name="products_code"]').find('option:selected').val() ;                  //公司的id
		params['module'] = 'SContractNoGeneration';
		params['action'] = 'SelectAjax';
		params['mode'] = 'getCheckData';
        thisInstance.products_flag=false;
		AppConnector.request(params).then(
				function(data){
					if(data.success==true){
                        if(data.result.products_codeflag==1){
                            thisInstance.products_flag=true;
                        }
                        console.log(data.result.codeprefix);
                        $('input[name="sc_related_to_display"]').attr('data-title','注意编码规则');$('input[name="sc_related_to_display"]').attr('data-content',data.result.codeprefix);$('input[name="sc_related_to_display"]').popover('show');
					}
				})
	},
    registerRecordPreSaveEvent : function(form) {
        var thisInstance = this;
        var editViewForm = this.getForm();

        editViewForm.on(Vtiger_Edit_Js.recordPreSave,function(e,data){
            var productid= $('select[name="products_code"]').val();
            var contractclassification = $("select[name='contractclassification']").val();
            console.log(productid);
            if(thisInstance.products_flag && (productid=='') && contractclassification=='ServiceContracts'){
                var  params = {text : app.vtranslate(),
                    title : app.vtranslate('产品必选')};
                Vtiger_Helper_Js.showPnotify(params);
                e.preventDefault(); //阻止提交事件先注释

            }
            if(parseInt($('input[name="quantity"]').val())<1){
                var  params = {text : app.vtranslate(),
                    title : app.vtranslate('份数要大于0')};
                Vtiger_Helper_Js.showPnotify(params);
                e.preventDefault(); //阻止提交事件先注释
            }
            //e.preventDefault(); //阻止提交事件先注释

        });
    },
    checkForm:function() {
        $('.btn-success').on('click',function (event) {
            var contractclassification = $("select[name='contractclassification']").val();
            var contract_classification = $("select[name='contract_classification']").val();
            if (contractclassification== 'SupplierContracts' && contract_classification=='tripcontract' ) {
                alert('采购合同暂不支持三方合同');
                event.preventDefault();
                return false;
            }
        })
    },
	/**
		pop two calander.
	*/
	registerBasicEvents:function(container){
		this._super(container);
		this.registerReferenceSelectionEvent(container);
        this.registerRecordPreSaveEvent();
        this.checkForm();
	}
});




















