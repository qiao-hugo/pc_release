/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("DataTransfer_Edit_Js",{
},{
	isSubmit : false,
	registerRecordPreSaveEvent : function() {
		var thisInstance = this;
		$('#EditView').on(Vtiger_Edit_Js.recordPreSave, function(e, data) {
			var sourceid = $('select[name="sourceid"]').val();
			var targetid = $('select[name="targetid"]').val();
			if(sourceid == targetid) {
				//同一用户转移提示
				var message = app.vtranslate('JS_SAME_USER_MESSAGE');
				var  params = {text : message, title :'提示：'}
				Vtiger_Helper_Js.showPnotify(params);
			}else{
				//数据转移前确认提示
				if(!thisInstance.isSubmit){
					var message = app.vtranslate('JS_LBL_TRANSFER_CONFIRM_MESSAGE');

					Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
							function(e) {
								thisInstance.isSubmit=true;
								$('#EditView').submit();
							},
							function(error){
							}
					);
				}else{
					return true;
				}
			}
           e.preventDefault();
		})
	},
	userChange:function(){
		var thisInstance = this;
		$("select[name='sourceid']").on("change",function (e) {
			$(".separte_user").each(function (k, v) {
				$(this).parent().parent().remove();
            });
            var sourceid = $(this).val();
            var params = {
                'module': 'Accounts',
                'action': 'ChangeAjax',
                'smownerid': sourceid,
                'mode': 'getAccountsBySmownerid'
            };
            AppConnector.request(params).then(
                function (datas) {
                	console.log(datas);
                    if (datas.success == true) {
                    	var str = '';
						$(datas.result).each(function (k, v) {
							console.log(v.accountrank);
                            str += "<tr><td>"+'<input type="checkbox" name="accountids[]" class="separte_user" value="'+v.accountid+'">'+"</td><td>"+v.accountname+"</td><td>"+v.accountrank+"</td></tr>";
                        })
                        $("#tdheader").after(str);
                    }else{

					}
                }
            );
        })
	},
	checkAll:function(){
		$(".over_checkedall").on('click',function (e) {
			$(".separte_user").attr('checked',true);
        })
	},
	checkVerse:function(){
		$(".over_checkedinverse").on('click',function (e) {
            $(".separte_user").each(function (k, v) {
				console.log($(v).attr('checked'));
				if($(v).attr('checked')=='checked'){
					$(v).attr('checked',false)
				}else{
                    $(v).attr('checked','checked')
				}
            })
        })
	},
	registerEvents: function(){
		this._super();
		this.registerRecordPreSaveEvent();
		this.userChange();
		this.checkAll();
		this.checkVerse();
	}
});