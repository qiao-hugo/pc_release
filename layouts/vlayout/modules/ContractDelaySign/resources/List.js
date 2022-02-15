/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("ContractDelaySign_List_Js",{},{
    delayApply:function(){
        var thisinstance=this;
        $(".delayapply").on("click",function () {
            var recordid=$(this).data('id');
            var accountname=$(this).data('accountname');
            var contractno=$(this).data('contract_no');
            str = '<div id="myModal" class="modal" style="">\n' +
                '\t<div class="modal-dialog">\n' +
                '\t\t<div class="modal-content">\n' +
                '\t\t\t<div class="modal-header">\n' +
                '\t\t\t\t<button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>\n' +
                '\t\t\t\t<h4 class="modal-title">申请延期签收</h4>\n' +
                '\t\t\t\t<div style="margin-top: 20px;" id="supervisor">\n';
            str += '\t\t\t\t<div class="confirm tc">\n';
            str += '<input type="hidden" name="contractdelaysignid" value="'+recordid+'"/>';


            str += '<div class="add-execution"><div class="add-execution-tip"><span style="color: white">*</span></div><div class="add-execution-node">合同编号</div><div class="add-execution-info"><input name="contractno" type="text" value="'+contractno+'" readonly></div></div>';
            str += '<div class="add-execution"><div class="add-execution-tip"><span style="color: white">*</span></div><div class="add-execution-node">客户名称</div><div class="add-execution-info"><input name="accountname" type="text" value="'+accountname+'" readonly></div></div>';
            str += '<div class="add-execution"><div class="add-execution-tip"><span style="color: red">*</span></div><div class="add-execution-node">延长的理由</div><div class="add-execution-info"><textarea name="reason"  placeholder="请输入延长的理由" ></textarea></div></div>';
            str += '<div class="add-execution" style="height: 100px;" xmlns="http://www.w3.org/1999/html"><div class="add-execution-tip"><span style="color: red">*</span></div><div class="add-execution-node">合同扫描件</div><div class="add-execution-info">'+
                '    <div class="add-execution-info"><div class="fileUploadContainer" xmlns="http://www.w3.org/1999/html">\n' +
                '                                <div class="upload">\n' +
                '                                    <input type="button" id="uploadButton" value="上传"  title="支持pdf/png/jpg文件，不超过3M" />\n' +
                '<span style="font-size:8px;color:gray">支持pdf/png/jpg文件不超过3M</span>'+
                '                                    <div style="display:inline-block;margin-top: 15px;" id="fileall">\n' +
                '                                            <input class="ke-input-text filedelete" type="hidden" name="file" id="file" value="" readonly="readonly" />\n' +
                '                                            <input class="filedelete" type="hidden" name="attachmentsid" value="">\n' +
                '                                    </div>\n' +
                '                                </div>\n' +
                '                            </div></div>'
                +'</div></div>';

            str +=                        '\n' +
                '\t\t\t\t</div>\n' +
                '\t\t\t</div>\n' +
                '\t\t\t<div class="modal-footer">\n' +
                '\t\t\t\t<div class=" pull-right cancelLinkContainer"><a class="cancelLink" type="reset" data-dismiss="modal">取消</a></div>\n' +
                '\t\t\t\t<button class="btn btn-success" id="transferPost" type="submit">确定</button>\n' +
                '\t\t\t</div>\n' +
                '\t\t</div>\n' +
                '\t</div>\n' +
                '</div>';
            app.showModalWindow(str);
            $("#uploadButton").trigger('click');
            $('.modal-backdrop').css({
                "opacity":"0.6",
                "z-index":"0"
            });
        })

    },
    pre_click_upload:function(){
        $("body").on('click','#uploadButton',function () {
                if($('#file').length>0){
                    var module=$('#module').val();
                    // KindEditor.ready(function(K) {
                    var record = $("#recordId").val();
                    record = 2427022;
                    window.K = KindEditor;
                    var uploadbutton = K.uploadbutton({
                        button : K('#uploadButton')[0],
                        fieldName : 'File',
                        extraParams :{
                            __vtrftk:csrfMagicToken,
                            record:record
                        },
                        url : 'index.php?module='+module+'&action=FileUpload&record='+record,
                        afterUpload : function(data) {
                            /*if (data.success ==true) {
                             $('input[name="attachmentsid"]').val(data.result['id']);
                             K('#file').val(data.result['name']);
                             } else {
                             }*/
                            if (data.success ==true) {
                                $("#fileall").empty();
                                $('.filedelete').remove();
                                // var str='<span class="label file'+data.result['id']+'" style="margin-left:5px;">'+data.result['name']+'&nbsp;<b class="deletefile" data-class="file'+data.result['id']+'" data-id="'+data.result['id']+'" title="删除文件" style="display:inline-block;width:12px;height:12px;line-height:12px;text-align:center">x</b>&nbsp;</span><input class="ke-input-text file'+data.result['id']+'" type="hidden" name="file['+data.result['id']+']" id="file" value="'+data.result['name']+'" readonly="readonly" /><input class="file'+data.result['id']+'" type="hidden" name="attachmentsid['+data.result['id']+']" value="'+data.result['id']+'">';
                                var  str ='<span class="label file'+data.result['id']+'" style="margin-left:5px;">'+data.result['name']+'&nbsp;<b class="deletefile" data-class="file'+data.result['id']+'" data-id="'+data.result['id']+'" title="删除文件" style="display:inline-block;width:12px;height:12px;line-height:12px;text-align:center">x</b>&nbsp;</span>';
                                str += "<input type='hidden' id='fileid' value='"+data.result['id']+"' data-name='"+data.result['name']+"'>";
                                $("#fileall").append(str);
                            } else {
                                Vtiger_Helper_Js.showMessage({type: 'error', text: data.result.msg});
                            }
                        },
                        afterError : function(str) {
                            //alert('自定义错误信息: ' + str);
                        }
                    });
                    uploadbutton.fileBox.change(function(e) {
                        uploadbutton.submit();
                    });
                    $('.fileUploadContainer').find('form').css({width:"54px"});
                    $('.fileUploadContainer').find('form').find('.btn-info').css({width:"54px",marginLeft:"-15px"});
                    // });
                }


            }
        )},
    submitDelayApply:function(){
        $("body").on('click',"#transferPost",function (e) {
            var recordid=$("input[name='contractdelaysignid']").val();
            var reason=$("textarea[name='reason']").val();
            console.log(reason);
            var fileid =  $("#fileid").val();
            if(!fileid){
                var params2 = {
                    text: '<h4>必须上传附件</h4>',
                    type: 'error'
                };
                Vtiger_Helper_Js.showMessage(params2);
                return;
            }
            if(!reason){
                var params2 = {
                    text: '<h4>请输入延长理由</h4>',
                    type: 'error'
                };
                Vtiger_Helper_Js.showMessage(params2);
                return;
            }
            var params_r = [];
            params_r['record'] = recordid;
            params_r['action'] = 'BasicAjax';
            params_r['module'] = 'ContractDelaySign';
            params_r['mode'] = 'makeWorkflowStagesByOrder';
            params_r['reason'] =reason;
            params_r['fileid'] =fileid;
            params_r['filename'] = $("#fileid").data('name');
            var progressIndicatorElement = jQuery.progressIndicator({
                'message' : '亲,正在拼命处理,请耐心等待哟',
                'position' : 'html',
                'blockInfo' : {'enabled' : true}
            });
            AppConnector.request(params_r).then(
                function(data) {
                    progressIndicatorElement.progressIndicator({
                        'mode' : 'hide'
                    });
                    if (data.success) {
                        location.href="index.php?module=ContractDelaySign&view=Detail&record="+recordid;
                        return;
                    }
                    var params = {
                        text: data.error.message,
                        type: 'error'
                    };
                    Vtiger_Helper_Js.showMessage(params);
                },
            )
        })
    },
    deleteuploadFile:function(){
        $('body').on('mouseover','.deletefile',function(){
            $(this).css({color:"#666",cursor:"pointer",border:"#666 solid 1px",borderRadius:"12px"});
        }).on('mouseout','.deletefile',function(){
            $(this).css({color:"#fff",border:"none",borderRadius:"none"});
        }).on('click','.deletefile',function(e){
            e.stopPropagation();
            var delclassid=$(this).data('id');
            var module=$('#module').val();
            var url='index.php?module='+module+'&action=DeleteFile&id='+delclassid+'&record=';
            AppConnector.request(url).then(
                function(data){
                    if(data['success']) {
                        $("#fileid").val('');
                        $("#fileid").attr('data-name','');
                        $('.file'+delclassid).remove();
                    } else {
                        aDeferred.reject(data['message']);
                    }
                },
                function(error){
                    //aDeferred.reject();
                }
            )
        });

    },

    // showConfirmationBox : function(data){
    //     var thisstance=this;
    //     var aDeferred = jQuery.Deferred();
    //     var width='800px';
    //     if(typeof  data['width'] != "undefined"){
    //         width=data['width'];
    //     }
    //     var bootBoxModal = bootbox.confirm({message:data['message'],width:width, callback:function(result) {
    //         if(result){
    //             if(thisstance.checkedform()){
    //                 aDeferred.resolve();
    //             }else{
    //                 return false;
    //             }
    //         } else{
    //             aDeferred.reject();
    //         }
    //     }, buttons: { cancel: {
    //         label: '取消',
    //         className: 'btn'
    //     },
    //         confirm: {
    //             label: '确认',
    //             className: 'btn-success'
    //         }
    //     }});
    //     bootBoxModal.on('hidden',function(e){
    //         if(jQuery('#globalmodal').length > 0) {
    //             jQuery('body').addClass('modal-open');
    //         }
    //     })
    //     return aDeferred.promise();
    // },
    //绑定查询事件 By Joe@20150421
registerEvents : function(){
	this._super();
	// this.delayApply();
    this.submitDelayApply();
    this.pre_click_upload();
    this.deleteuploadFile();
}

});
