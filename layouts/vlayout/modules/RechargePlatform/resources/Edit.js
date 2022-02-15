Vtiger_Edit_Js("RechargePlatform_Edit_Js",{ },{
    //Stored history of account name and duplicate check result
	duplicateCheckCache : {},
	//This will store the editview form
	editViewForm : false,
    checkFlag : true,
	/**
	 * This function will return the current form
	 */
	getForm : function(){
		if(this.editViewForm == false) {
			this.editViewForm = jQuery('#EditView');
		}
		return this.editViewForm;
	},    
	/**
	 * This function will return the account name
	 */
    getPlatformName : function(container){
		return jQuery('input[name="platformname"]',container).val();
	},
	/**
	 * This function will register before saving any record
	 */
	registerRecordPreSaveEvent : function(form) {
		var thisInstance = this;
		if(typeof form == 'undefined') {
			form = this.getForm();
		}
		form.on(Vtiger_Edit_Js.recordPreSave, function(e, data) {
           if(!thisInstance.checkFlag){
               Vtiger_Helper_Js.showMessage({type:'error',text:'充值平台名称重复啦!'});
               e.preventDefault();
		   }
		})
	},

	//实时检测提示重复平台
	registerCheck:function(form){
		var thisInstance = this;
        var platformname =$('input[name=platformname]').val();
        $("input[name=platformname]").blur(function(e){
            thisInstance.checkplatformname(platformname,$(this).val());
		});
		
	},
    checkplatformname :function (oldPlatformname,newPlatformname) {
        var thisInstance = this;
        if($.trim(oldPlatformname) == $.trim(newPlatformname)){
            return true;
        }
        var params={};
        params['action'] = 'BasicAjax';
        params['module'] = 'RechargePlatform';
        params['mode'] = 'checkplatformname';
        params['platformname'] = $.trim(newPlatformname);
        var d={};
        d.data=params;
        d.type = 'GET';
        AppConnector.request(d).then(
            function(data){
                if(data && data.result && data.result == 1){
                    Vtiger_Helper_Js.showMessage({type:'error',text:'充值平台名称重复啦!'});
                    thisInstance.checkFlag = false;
                }else{
                    thisInstance.checkFlag = true;
				}
            },
            function(error){}
        );
    },
	/**
	 * Function which will register basic events which will be used in quick create as well
	 */
	registerBasicEvents : function(container) {
		this._super(container);
		this.registerRecordPreSaveEvent(container);
		this.registerCheck(container);
	}
});