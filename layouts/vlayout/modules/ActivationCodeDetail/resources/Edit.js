Vtiger_Edit_Js("ActivationCode_Edit_Js",{ },{

    registerRecordPreSaveEvent : function(form) {
        var thisInstance = this;
        if(typeof form == 'undefined') {
            form = this.getForm();
        }
        form.on(Vtiger_Edit_Js.recordPreSave, function(e, data) {
            var f = thisInstance.check();

            if (f > 0) {
                var message = '';
                if (f == 1) {
                    message = '激活码位数必须是36位。';
                } else if(f == 2) {
                    message = '激活码含有非法字符，请检查后重新录入';
                } else if (f == 3) {
                    message = '激活码中划线必须是4位';
                }
                var  params = {text : app.vtranslate(message),title : app.vtranslate('JS_DUPLICTAE_CREATION_CONFIRMATION')}
                Vtiger_Helper_Js.showPnotify(params);
                e.preventDefault();
            }

            var contractid = $('input[name=contractid]').val();
            var postData = {
                    "module": 'ActivationCode',
                    "action": "ChangeAjax",
                    'contractid' : contractid,
                    'activecode' : $('input[name="activecode"]').val()
                };
            if (!thisInstance.flag) {
                AppConnector.request(postData).then(
                    function(data){
                        if(data.success) {
                            var result = data['result'];
                            if (result.success == 0) {
                                var  params = {text : result.message, title : '错误提示'};
                                Vtiger_Helper_Js.showPnotify(params);
                            } else {
                                thisInstance.flag = true;
                                form.submit();
                            }
                        } else {
                            return false;
                        }
                    },
                    function(error,err){

                    }
                );
                e.preventDefault();
            } 
        });
    },
    
    check: function () {
        var uname = $('input[name="activecode"]').val();
        if (uname.length != 36) {
            return 1;
        }
        if(! /^[a-z0-9-]+$/.test(uname)){
            return 2;
        }
        var t = uname.split('-');
        if (t.length != 5) {
            return 3;
        }
        return 0;
    },
    init: function() {
        $('input[name="activecode"]').css({width: '300px'});
        $('input[name=contractid_display]').next().remove();
    },
	registerBasicEvents : function(container) {
        this._super(container);
        this.registerRecordPreSaveEvent(container);
	}
});