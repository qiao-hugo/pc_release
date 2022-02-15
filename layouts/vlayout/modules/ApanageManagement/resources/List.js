


Vtiger_List_Js("ApanageManagement_List_Js",{
    updateuserinfo:function(){
        $('#UpdateUserINFO').click(function(){
            var msg = {
                'message':"确定要更新",
                "width":"500px",
                "action":function(){
                    if($("#yearMonth").val()==''){
                        Vtiger_Helper_Js.showMessage({type: 'error', text: '请选择月份！'});
                        return false
                    }
                    return true;
                }
            };
            Vtiger_Helper_Js.showConfirmationBox(msg).then(
                function(e) {
                    var params = {
                        'module': 'ApanageManagement',
                        'action': 'ChangeAjax',
                        'mode': 'updateUserInfo',
                        'yearMonth':$("#yearMonth").val()

                    };
                    var progressIndicatorElement = jQuery.progressIndicator({
                        'message' : '数据更新中请稍等！',
                        'position' : 'html',
                        'blockInfo' : {'enabled' : true}
                    });
                    AppConnector.request(params).then(
                        function (data) {
                            progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                            if(data.result.success){
                                Vtiger_Helper_Js.showMessage({type: 'success', text: data.result.msg});
                                window.location.reload();
                            }else{
                                Vtiger_Helper_Js.showMessage({type: 'error', text: data.result.msg});
                            }

                        }
                    )
                }
            );
            var str='<table style="margin-top: 25px;"><tr>\n';
            str+='<td class="fieldLabel medium">\n' +
                '     <label class="muted pull-right marginRight10px">数据月份</label>\n' +
                '       </td>\n' +
                '       <td class="fieldValue medium">\n' +
                '          <div class="input-append row-fluid">\n' +
                '            <div class="span10 row-fluid date form_datetime">\n' +
                '               <input  type="text" id="yearMonth"  name="budgetlockstart[]"   data-date-format="yyyy-mm-dd" readonly="" value="" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  autocomplete="off">\n' +
                '               <span class="add-on"><i class="icon-calendar"></i></span>\n' +
                '</div></div></td></tr><tr></table>'
            $('.modal-body').append(str);
            $("#yearMonth").datetimepicker({
                format: 'yyyy-mm',
                weekStart: 1,
                autoclose: true,
                startView: 3,
                minView: 3,
                forceParse: false,
                language: 'zh-CN'
            })
        });

    },
    deleteRecord:function(){
        $(document).on('click', '.deleteRecordButtont', function () {
            var msg = {
                'message':"确定删除该配置？",
                "width":"500px"
            };
            var recordid=$(this).data('id');
            var closeset=$(this).closest('tr');
            Vtiger_Helper_Js.showConfirmationBox(msg).then(
                function(e) {
                    var params = {
                        'module': 'ApanageManagement',
                        'action': 'ChangeAjax',
                        'mode': 'deleteRecord',
                        'record': recordid
                    };
                    var progressIndicatorElement = jQuery.progressIndicator({
                        'message' : '数据更新中请稍等！',
                        'position' : 'html',
                        'blockInfo' : {'enabled' : true}
                    });
                    AppConnector.request(params).then(
                        function (data) {
                            progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                            if(data.result.success){
                                Vtiger_Helper_Js.showMessage({type: 'success', text:data.result.msg});
                                closeset.children('.ucityname').text('');
                                closeset.children('.ucityratio').text('');
                                closeset.children('.modifiedtime').text('');
                                closeset.children('.usmownerid').text('');
                            }else{
                                Vtiger_Helper_Js.showMessage({type: 'error', text:data.result.msg});
                            }

                        }
                    )
                }
            );
        });
    },
	registerEvents : function(){
		this._super();
		this.updateuserinfo();
		this.deleteRecord();
		//this.exportData();
        //this.applicationUpdateAchievement();
        //this.totalMoney();
	}
});