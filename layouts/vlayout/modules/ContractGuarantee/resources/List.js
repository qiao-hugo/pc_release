/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("ContractGuarantee_List_Js", {}, {
        
    registerEvents: function () {
           this._super();
        this.show_details();
        this.cancelContract();
        this.noNeedToExportButton();
        this.needToExportButton();
    },
    noNeedToExportButton:function(){
        $('.listViewContentDiv').on("click",'.noNeedToExportButton',function(e){
            var elem = jQuery(e.currentTarget);
            var recordId = elem.closest('tr').data('id');
            var msg={'message':"标记无需导出"};
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                var voidreason='';
                $(".voidreasons").each(function (i) {
                    if($(this).val()){
                        voidreason=$(this).val();
                    }
                })
                var params={};
                var module = app.getModuleName();
                params['record']=recordId;
                params['action']='BasicAjax';
                params['module']=module;
                params['voidreason']=voidreason;
                params['mode']='noNeedToExport';
                AppConnector.request(params).then(
                    function(data){
                        if(data.success==true){
                            window.location.reload(true);
                        }else{
                            alert("处理出现异常,请重新尝试");
                        }
                    }
                );
            });
            $('.modal-body').append('<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0;"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">无需导出原因<font color="red">*</font>:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><textarea class="voidreasons" class="span11 "></textarea></span></div></td></tr></tbody></table>');
        });
    },
    needToExportButton:function () {
        $('.listViewContentDiv').on("click",'.needToExportButton',function(e){
            var elem = jQuery(e.currentTarget);
            var recordId = elem.closest('tr').data('id');
            var msg={'message':"取消标记无需导出？"};
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                var params={};
                var module = app.getModuleName();
                params['record']=recordId;
                params['action']='BasicAjax';
                params['module']=module;
                params['mode']='needToExport';
                AppConnector.request(params).then(
                    function(data){
                        if(data.success==true){
                            window.location.reload(true);
                        }else{
                            alert("处理出现异常,请重新尝试");
                        }
                    }
                );
            });
        });
    },
    show_details:function(){
          $('body').on('click', '.toVoidButton', function () {
            var progressIndicatorElement = jQuery.progressIndicator({ 'message' : '合同信息加载中...','blockInfo':{'enabled':true }});
            var id = $(this).attr('dd');
            var params={};
            params.data={
                "module": "ContractGuarantee",
                "action": "BasicAjax",
                "mode": "showDetails",
                'contract_no': id
            };
            params.async=false;
             
            var returndata={'flag':false};
            AppConnector.request(params).then(
                function(data){
                var  arr = data.result;
                   progressIndicatorElement.progressIndicator({ 'mode' : 'hide'});
                    if(data.success){   
                                var show_data = '';
                                 $('#show_data1').empty();
                            show_data = '<tr>'+
                                '<td class="fieldLabel medium">'+
                                    '<label class="muted pull-right marginRight10px">使用模块</label>'+
                                '</td>'+
                                '<td class="fieldValue medium" style="width: 45px"><label class="muted pull-right marginRight10px">负责人</label></td>'+
                                '<td class="fieldLabel medium" style="width: 35px"><label class="muted pull-right marginRight10px">编号</label></td>'+
                                '<td class="fieldValue medium" style="width: 25px"><label class="muted pull-right marginRight10px">流程状态</label></td>'+
                            '</tr>';
                                    $.each(arr,function(key,value){
                                       
                                        show_data += "<tr><td class='fieldValue medium' style='width: 20px'><label class='muted pull-right marginRight10px'>"+value.modulename+"</label></td>"+
                                                    "<td class='fieldLabel medium' style='width: 15px'><label class='muted pull-right marginRight10px'>"+value.last_name+' ['+value.department+']'+"</label></td>"+
                                                    "<td class='fieldValue medium' style='width: 15px'><label class='muted pull-right marginRight10px'><a href ='"+ value.link+"' target=_blank>"+value.invoiceno+"</a></label></td>"+
                                                    "<td class='fieldValue medium' style='width: 15px'><label class='muted pull-right marginRight10px'>"+value.modulestatus+"</label></td></tr>";
                                    });
        //                        return data=data.result;
                                $('#show_data1').append(show_data);
                                $('#show_data2').show();
                        }
                }
            );
            //returndata={'flag':true,'msg':'是否要确认提交!'};
//            return data;
        });
        $('body').on("click",'.bootbox-close-button',function(event){
            $('#show_data2').hide();
        });
    },

    /**
     * 申请单作废;
     */
    cancelContract:function(){
        $('.listViewContentDiv').on("click",'.cancelContractButton',function(e){
            var module = app.getModuleName();
            var elem = jQuery(e.currentTarget);
            var recordId = elem.closest('tr').data('id');
            var name=$('#stagerecordname').val();
            var contractType = elem.closest('tr').data('mode');
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
                        $('.modal-body').append('<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0;"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">消除担保原因<font color="red">*</font>:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><textarea class="voidreasons span10"  ></textarea></span></div></td></tr></tbody></table>');
                        //window.location.reload(true);
                    }else{
                        Vtiger_Helper_Js.showMessage({type: 'error', text:data.result.message });
                    }
                }
            );


        });
    }

});