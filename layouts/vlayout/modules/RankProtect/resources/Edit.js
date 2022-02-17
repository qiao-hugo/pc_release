Vtiger_Edit_Js("RankProtect_Edit_Js",{},{
	duplicateCheckCache : {},
	registerRecordPreSaveEvent : function() {
	var thisInstance = this;
	var	form = jQuery('#EditView');
		
		form.on(Vtiger_Edit_Js.recordPreSave, function(e, data) {
			var accountrank = jQuery('select[name="accountrank"]').val();
			var performancerank =jQuery('select[name="performancerank"]').val();
			var recordId=jQuery('input[name="record"]').val();
			var duplicate=accountrank+'#'+performancerank;
			var params = {};
            if(!( duplicate in thisInstance.duplicateCheckCache)) {
                Vtiger_Helper_Js.checkDuplicateName({
                    'accountName' : duplicate, 
                    'recordId' : recordId,
                    'moduleName' : 'RankProtect'
                }).then(
                    function(data){
                        thisInstance.duplicateCheckCache[duplicate] = data['success'];
                        form.submit();
                    },
                    function(data, err){
                        thisInstance.duplicateCheckCache[duplicate] = data['success'];
                        thisInstance.duplicateCheckCache['message'] = data['message'];
						var  params = {text : app.vtranslate(data.message),
								title : '分类已存在'}
							Vtiger_Helper_Js.showPnotify(params);
                    }
				);
            }else {
				if(thisInstance.duplicateCheckCache[duplicate] == true){
					var params = {text : thisInstance.duplicateCheckCache['message'],
					title : '分类已存在'}
					Vtiger_Helper_Js.showPnotify(params);
				} else {
					delete thisInstance.duplicateCheckCache[duplicate];
					return true;
				}
			}
            e.preventDefault();
		})
	},
	
	
	
	registerEvents: function(){
		this._super();
		this.registerRecordPreSaveEvent();
	}
});


