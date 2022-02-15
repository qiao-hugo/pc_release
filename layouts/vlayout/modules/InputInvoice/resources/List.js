/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("InputInvoice_List_Js",{},{
    changeUserData:function(){
        var thisinstance=this;
        $('#page').on('click','.updateSurplusAmountButton',function(){
            var recordid=$(this).data('id');
            var surplusamount = $(this).data("surplusamount");
            if(!surplusamount){
                Vtiger_Helper_Js.showMessage({type:'error',text:'欠票金额为0'});
                return;
            }

            var message='<h5>欠票金额抵消</h5><hr />';
            var msg={
                'message':message,
                "width":600,
                "action":function(){
                    var offsetamount = $('#offsetamount').val();
                    if(isNaN(offsetamount)){
                        var params = {
                            text: '请填写有效的数学',
                            type: 'error'
                        };
                        Vtiger_Helper_Js.showMessage(params);
                        return false;
                    }
                    if(offsetamount=='' || Number(offsetamount)<0 ) {
                        var params = {
                            text: '必填项不能为空，必为数字且不能小于0',
                            type: 'error'
                        };
                        Vtiger_Helper_Js.showMessage(params);
                        return false;
                    }
                    return true;
                }
            };
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                var offsetamount = $('#offsetamount').val();
                var params={};
                params['record'] = recordid;
                params['offsetamount'] = offsetamount;
                params['action'] = 'BasicAjax';
                params['module'] = 'InputInvoice';
                params['mode'] = 'offsetAmount';
                AppConnector.request(params).then(
                    function(data) {
                        if(data.result.success){
                            var params = {
                                text: '抵消金额成功',
                                type: 'success'
                            };
                            Vtiger_Helper_Js.showMessage(params);
                            window.location.reload();
                        }else{
                            var params = {
                                text: data.result.msg,
                                type: 'error'
                            };
                            Vtiger_Helper_Js.showMessage(params);
                        }
                    },
                    function(error,err){
                        //window.location.reload(true);
                    }
                );
            },function(error, err) {});
            var str = '<table class="table table-bordered blockContainer Duplicates showInlineTable  detailview-table">' +
                '<tbody>' +
                '<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">欠票金额</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><input type="text" class="span11 input-small" readonly name="surplusamount" id="surplusamount" value="'+surplusamount+'" /></span></div></td></tr>' +
                '<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>抵消金额</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><input class="span11 input-large checknumber" name="offsetamount" type="text" id="offsetamount" value="" /></span></div></td></tr>' +
                '</tbody></table>';
            $('.modal-content .modal-body').append(str);
            $('.modal-content .modal-body').css({overflow:'scroll'});
            $('.modal-content .modal-body').css('max-height','600px');

        });
    },

    registerEvents : function(){
        this._super();
        this.changeUserData();
    }

});
