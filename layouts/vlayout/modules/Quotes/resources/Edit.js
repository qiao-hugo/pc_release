Vtiger_Edit_Js("Quotes_Edit_Js",{},{
	registerReferenceSelectionEvent : function(container) {
		var thisInstance = this;
		jQuery('input[name="potential_id"]',container).on(Vtiger_Edit_Js.referenceSelectionEvent, function (e,data){thisInstance.potentialchange(data);});
		
	},
	//2015-02-16 wangbin 销售订单关联报价单,自动读取客户名称跟id
	potentialchange:function(){
		var id = jQuery("input[name='potential_id']").val();
		var params = {
				'module' :'Quotes',
				'action' :'BasicAjax',
				'record' :id,
				
		};
		AppConnector.request(params).then(
				function(date){
					if(date.success == true){
						jQuery("input[name='account_id_display']").val(date.result['0']);
						jQuery("input[name='account_id']").val(date.result['1']);
					};
				});
	},
	registerEvents: function(){
		this._super();
		//this.autofillaccount();
		this.registerReferenceSelectionEvent();
        //2015-04-28 adatian 修改正常显示公司名称
		//this.potentialchange();
	}
})