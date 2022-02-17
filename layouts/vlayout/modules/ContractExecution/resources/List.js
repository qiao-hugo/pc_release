/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("ContractExecution_List_Js", {}, {

    registerEvents: function () {
        this._super();
        this.addExecutionNode();
        this.searchContractNo();
        this.listenContractNoChange();
        this.submitExecutionStage();
        this.pre_click_upload();
        this.deleteuploadFile();
    },
    /**
     * 删除上传的文件
     */
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
    submitExecutionStage:function(){
        $("body").on('click',"#transferPost",function (e) {
            var contractno = $("input[name='contractno']").val();
            var accountid = $("input[name='accountid']").val();
            if(!contractno || !accountid){
                var params2 = {
                    text: '<h4>请先填写正确的合同编号</h4>',
                    type: 'error'
                };
                Vtiger_Helper_Js.showMessage(params2);
                event.preventDefault();
                return;
            }
            var stage = $("input[name='stage']").val();
            var receiveableamount = $("input[name='recieveableamount']").val();
            if(!receiveableamount){
                var params2 = {
                    text: '<h4>应收金额必填</h4>',
                    type: 'error'
                };
                Vtiger_Helper_Js.showMessage(params2);
                event.preventDefault();
                return;
            }
            var collectiondescription = $("textarea[name='collectiondescription']").val();
            if(!collectiondescription){
                var params2 = {
                    text: '<h4>收款说明必填</h4>',
                    type: 'error'
                };
                Vtiger_Helper_Js.showMessage(params2);
                return;
            }
            var ispass = $('input:radio[name="isPass"]:checked').val();
            var contractid = $("input[name='contractid']").val();
            var voucher = $("input[name='voucher']").val();
            var fileid =  $("#fileid").val();
            if(ispass=='0'&&fileid){
                var params2 = {
                    text: '<h4>当前节点不执行，无需上传凭证</h4>',
                    type: 'error'
                };
                Vtiger_Helper_Js.showMessage(params2);
                return;
            }
            var params_r = [];
            params_r['ispass'] = ispass;
            params_r['collectiondescription'] = collectiondescription;
            params_r['receiveableamount'] = receiveableamount;
            params_r['stage'] = stage;
            params_r['accountid'] = accountid;
            params_r['contractno'] = contractno;
            params_r['contractid'] = contractid;
            params_r['action'] = 'ChangeAjax';
            params_r['module'] = 'ContractExecution';
            params_r['mode'] = 'newExecutionNode';
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
                        location.href="index.php?module=ContractExecution&view=Detail&record="+data.result.contractexecutionid;
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
    listenContractNoChange:function(){
      $("body").on("input propertychange","input[name='contractno']",function (e) {
          $("#last_execution_date").parent().parent().hide();
          $("#last_execution_date").text("");
          $("input[name='accountname']").val("");
          $("input[name='accountid']").val(0);
          $("input[name='stage']").val(0);
          $("input[name='receivestage']").val("");
      })
    },
    searchContractNo:function(){
      $("body").on('click','#serach_contractno',function (e) {
          contractno = $("input[name='contractno']").val();
          if(!contractno){
              var params2 = {
                  text: '<h4>请先填写正确的合同编号</h4>',
                  type: 'error'
              };
              Vtiger_Helper_Js.showMessage(params2);
              return;
          }
          var params_r = [];
          params_r['action'] = 'ChangeAjax';
          params_r['module'] = 'ContractExecution';
          params_r['mode'] = 'searchContractNo';
          params_r['contractno'] = contractno;
          AppConnector.request(params_r).then(
              function(data){
                  console.log(data);
                  if(data.success){
                      if(data.result.processdate){
                          $("#last_execution_date").parent().parent().show();
                          $("#last_execution_date").text(data.result.processdate);
                      }
                      $("input[name='accountname']").val(data.result.accountname);
                      $("input[name='accountid']").val(data.result.accountid);
                      var nextstage = 1+parseInt(data.result.stage);
                      $("input[name='stage']").val(nextstage);
                      $("input[name='receivestage']").val('第'+nextstage+'阶段');
                      $("input[name='contractid']").val(data.result.contractid);
                      return;
                  }

                  var params = {
                      text: data.error.message,
                      type: 'error'
                  };
                  Vtiger_Helper_Js.showMessage(params);
              },
              function(error,err){
              }
          );
      })
    },
    addExecutionNode:function(){
      $("body").on('click',"#add_contract_execution_node",function (e) {
          str = '<div id="myModal" class="modal" style="">\n' +
              '\t<div class="modal-dialog">\n' +
              '\t\t<div class="modal-content">\n' +
              '\t\t\t<div class="modal-header">\n' +
              '\t\t\t\t<button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>\n' +
              '\t\t\t\t<h4 class="modal-title">添加合同执行节点</h4>\n' +
              '\t\t\t\t<div style="margin-top: 20px;" id="supervisor">\n';
          str += '\n' +
              '\t\t\t\t</div>\n' +
              '\t\t\t</div>\n' +
              '\t\t\t<div class="modal-body" style="max-height:500px;">\n' +
              '\n' +
              '<div style="color: grey">已签收状态且类型为框架合同，才可手动录入合同执行节点，手动录入合同执行节点，合同最近一个节点必须已执行通过才可添加新节点</div><br>'+
              '\t\t\t\t<div class="confirm tc">\n';


          str += '<div class="add-execution"><div class="add-execution-tip"><span style="color: red">*</span></div><div class="add-execution-node">合同编号</div><div class="add-execution-info"><input name="contractid" type="hidden" value=""><input name="contractno" type="text" ><span class="paddingLeft10px cursorPointer help-inline" id="serach_contractno"><img src="layouts/vlayout/skins/softed/images/search.png" alt="搜索按钮" title="搜索按钮"></span></div></div>';
          str += '<div  style="display: none"><div class="add-execution-tip"><span style="color: white">*</span></div><div style="color: red">最近一次合同节点执行时间：<span id="last_execution_date"></span></div></div>';
          str += '<div class="add-execution"><div class="add-execution-tip"><span style="color: white">*</span></div><div class="add-execution-node">客户名称</div><div class="add-execution-info"><input name="accountid" type="hidden" value=""><input name="accountname" type="text" readonly></div></div>';
          str += '<div class="add-execution"><div class="add-execution-tip"><span style="color: white">*</span></div><div class="add-execution-node">收款阶段</div><div class="add-execution-info"><input name="stage" type="hidden" value=""><input name="receivestage" type="text" readonly></div></div>';
          str += '<div class="add-execution"><div class="add-execution-tip"><span style="color: red">*</span></div><div class="add-execution-node">应收金额</div><div class="add-execution-info"><input name="recieveableamount" type="text" ></div></div>';
          str += '<div class="add-execution"><div class="add-execution-tip"><span style="color: red">*</span></div><div class="add-execution-node">收款说明</div><div class="add-execution-info"><textarea name="collectiondescription" maxlength="100"></textarea></div></div>';
          str += '<div class="add-execution"><div class="add-execution-tip"><span style="color: white">*</span></div><div class="add-execution-node">默认节点执行通过</div><div class="add-execution-info"><input name="isPass" type="radio" value="1"  checked="checked" />是  <input name="isPass" type="radio" value="0" />否</div></div>';
          str += '<div class="add-execution" xmlns="http://www.w3.org/1999/html"><div class="add-execution-tip"><span style="color: white">*</span></div><div class="add-execution-node">执行凭证</div><div class="add-execution-info">'+
          '    <div class="add-execution-info"><div class="fileUploadContainer" xmlns="http://www.w3.org/1999/html">\n' +
          '                                <div class="upload">\n' +
          '                                    <input type="button" id="uploadButton" value="上传"  title="支持pdf/png/jpg文件，不超过3M" />\n' +
           '<span style="font-size:8px;color:gray">支持pdf/png/jpg文件不超过3M</span>'+
          '                                    <div style="display:inline-block" id="fileall">\n' +
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

    getuploadFile:function(){
        if($('#file').length>0){
            var module=$('#module').val();
            KindEditor.ready(function(K) {
                var uploadbutton = K.uploadbutton({
                    button : K('#uploadButton')[0],
                    fieldName : 'File',
                    extraParams :{
                        __vtrftk:$('input[name="__vtrftk"]').val(),
                        record:$('input[name="record"]').val()
                    },
                    url : 'index.php?module='+module+'&action=FileUpload&record='+$('input[name="record"]').val(),
                    afterUpload : function(data) {
                        if (data.success ==true) {
                            $('.filedelete').remove();
                            var str='<span class="label file'+data.result['id']+'" style="margin-left:5px;">'+data.result['name']+'&nbsp;<b class="deletefile" data-class="file'+data.result['id']+'" data-id="'+data.result['id']+'" title="删除文件" style="display:inline-block;width:12px;height:12px;line-height:12px;text-align:center">x</b>&nbsp;</span><input class="ke-input-text file'+data.result['id']+'" type="hidden" name="file['+data.result['id']+']" id="file" value="'+data.result['name']+'" readonly="readonly" /><input class="file'+data.result['id']+'" type="hidden" name="attachmentsid['+data.result['id']+']" value="'+data.result['id']+'">';
                            $("#fileall").append(str);
                            //K('#file').val(data.result['name']);
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
            });
        }
    },
});