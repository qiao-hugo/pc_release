/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("Vendors_Detail_Js",{},{

	getDeleteMessageKey : function() {
		return 'LBL_RELATED_RECORD_DELETE_CONFIRMATION';
	},
	init: function () {
		$('#Vendors_detailView_basicAction_LBL_SET_VENDORSTATE').click(function (){
            Vtiger_Helper_Js.showConfirmationBox({'message':'确定转为正式供应商'}).then(function(e){
                var params = {
                    'module' : 'Vendors', //ServiceContracts
                    'action' : 'BasicAjax',
                    'mode':'addWorkFlows',
                    "recordid":$('#recordId').val()
                };
                AppConnector.request(params).then(
                    function(data){
                        //window.location.reload();
                        if(data.result.success){
                            window.location.reload();
                        }else{
                            var  params = {text : data.result.msg,title : ""};
                            Vtiger_Helper_Js.showPnotify(params);
                        }
                    },
                    function(){
                    }
                );

            },function(error, err) {});
		});
	},
    setVendorScore: function () {
        $('#Vendors_detailView_basicAction_LBL_SET_VENDORSCORE').click(function (){
            Vtiger_Helper_Js.showConfirmationBox({'message':'确定要评分吗?'}).then(function(e){
                var params = {
                    'module' : 'Vendors', //ServiceContracts
                    'action' : 'BasicAjax',
                    'mode':'setVendorScore',
                    "recordid":$('#recordId').val(),
                    'vendorscore':$('#select_vendor_score').val()
                };
                AppConnector.request(params).then(
                    function(data){
                        window.location.reload();
                        /*if(data.result.success){
                            window.location.reload();
                        }else{
                            var  params = {text : data.result.msg,title : ""};
                            Vtiger_Helper_Js.showPnotify(params);
                        }*/
                    },
                    function(){
                    }
                );

            },function(error, err) {});
            var status = $(this).attr('data-status');
            var ss = '<select id="select_vendor_score">';
            var is_option = '';
            var vendorsTateData=["A","B","C","D","E"];
            for(var i in vendorsTateData) {
                is_option = i == status ? 'selected' : '';
                ss += '<option '+is_option+' value="'+vendorsTateData[i]+'">'+vendorsTateData[i]+'</option>';
            }
            ss += '</select>';
            $('.modal-body').append('<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0;"><tbody><tr><td class="fieldLabel medium"><label class="muted">供应商评级:</label></td><td class="fieldValue medium " colspan="3"><div class="row-fluid  pull-left"><span class="span10">'+ss+'</span></div></td></tr></tbody></table>');
        });
    },
    changeSmowner:function () {
        $("body").on("click","#Vendors_detailView_basicAction_LBL_CHANGESMOWNER",function () {
            var isVerify = $("input[name='isverify']").val();
            if(isVerify=='1'){
                Vtiger_Helper_Js.showMessage({type: 'error', text: "供应商正在审核中，不允许更改！"});
                return;
            }
            var msg = {
                'message': "<h4>更换负责人</h4><hr>",
                'action': function () {
                    return true;
                }
            };
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function (e) {
                var newsmownerid=$("select[name='reportsower']").val();
                var postData = {
                    "module": 'Vendors',
                    "action": "BasicAjax",
                    "recordid": $('#recordId').val(),
                    "newsmownerid": newsmownerid,
                    'mode': 'doChangeSmowner'
                };
                AppConnector.request(postData).then(
                    function (data) {
                        if (data.success) {
                            Vtiger_Helper_Js.showMessage({type: 'success', text: data.msg});
                            location.href="./index.php?module=Vendors&view=List"
                        } else {
                            Vtiger_Helper_Js.showMessage({type: 'error', text: data.msg});
                        }
                    });
            });
            var str = '<div class="control-group" style="height: 300px;"><div class="control-group"><br/><div class="controls" ><span style="color: red">*</span><span class="" style="font-size: 16px;"> 新负责人 </span>'+accessible_users+'</div></div>';

            $('.modal-dialog').css("marginTop", "200px");
            $('.modal-body .bootbox-close-button').after();
            $('.bootbox-body').append(str);
            $(".chzn-select").chosen();
        });

    },

	/**
	 * Function to register events
	 */
	registerEvents : function(){
		this._super();
		this.setVendorScore();
		this.changeSmowner();
	}
});
