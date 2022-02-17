/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("ContractGuarantee_Detail_Js",{

    /**
     * 取消消除合同;
     */
    cancelContract:function(){
        $('#ContractGuarantee_detailView_basicAction_LBL_CANCEL_CONTRACTS').on("click",function(e){
            var module = app.getModuleName();
            var recordId =$("#recordId").val();
            var contractType = $("#modulename").val();
            console.log(recordId);
            console.log(contractType);
            // 判断是否可以执行消除合同担保
            var param={};
            param['record']=recordId;
            param['action']='BasicAjax';
            param['module']=module;
            param['mode']='checkIsCanCancel';
            param['contractType']=contractType;
            AppConnector.request(param).then(
                function(data){
                    if(data.result.result==true){
                        //如果是不能继续消除 则中断
                        var msg={'message':"是否要消除合同担保？"};
                        Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                            msg={'message':"确定要消除合同担保？"};
                            var voidreason='';
                            // 因为文件中包含好几个 modal-body 所以需要通过each 获取文本域内容
                            $('.voidreasons').each(function(){
                                if($(this).val()==''){
                                }else{
                                    voidreason=$(this).val();
                                }
                            });
                            if(voidreason==''){
                                Vtiger_Helper_Js.showMessage({type: 'error', text: '消除原因必须填写!'});
                                return false;
                            }
                            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                                var params={};
                                params['record']=recordId;
                                params['action']='BasicAjax';
                                params['module']=module;
                                params['voidreason']=voidreason;
                                params['mode']='cancelContract';
                                params['contractType']=contractType;
                                AppConnector.request(params).then(
                                    function(data){
                                        if(data.result.data.result==true){
                                            window.location.reload(true);
                                        }else{
                                            Vtiger_Helper_Js.showMessage({type: 'error', text:data.result.data.message});
                                            return false;
                                        }
                                    }
                                );
                            });
                        });
                        $('.modal-body').append('<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0;"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">消除担保原因<font color="red">*</font>:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><textarea class="voidreasons span10" ></textarea></span></div></td></tr></tbody></table>');
                        //window.location.reload(true);
                    }else{
                        Vtiger_Helper_Js.showMessage({type: 'error', text:data.result.message });
                    }
                }
            );


        });
    },
    registerEvents: function (container) {
        this._super(container);
        this.cancelContract();
    }
});

