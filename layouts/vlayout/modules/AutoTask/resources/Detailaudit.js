/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
Vtiger_Detail_Js("AutoTask_Detailaudit_Js",{},{
	init : function() {
		this.registerEvents();
	},

    submit:function(){
        /*$("body").on("blur","#inputPassword",function(){
            alert(4444)
        });*/
    },

    pre_click_upload:function(){
      $("#uploadButton").live("click",function(){
              if($('#file').length>0){
                  var module=$('#module').val();
                  //KindEditor.ready(function(K) {
                  window.K = KindEditor;
                  var uploadbutton = K.uploadbutton({
                      button : K('#uploadButton')[0],
                      fieldName : 'File',
                      extraParams :{
                          __vtrftk:$('input[name="__vtrftk"]').val(),
                          record:53
                      },
                      url : 'index.php?module='+module+'&action=FileUpload&record=53',
                      afterUpload : function(data) {
                          /*if (data.success ==true) {
                           $('input[name="attachmentsid"]').val(data.result['id']);
                           K('#file').val(data.result['name']);
                           } else {
                           }*/
                          if (data.success ==true) {
                              $('.filedelete').remove();
                             // var str='<span class="label file'+data.result['id']+'" style="margin-left:5px;">'+data.result['name']+'&nbsp;<b class="deletefile" data-class="file'+data.result['id']+'" data-id="'+data.result['id']+'" title="删除文件" style="display:inline-block;width:12px;height:12px;line-height:12px;text-align:center">x</b>&nbsp;</span><input class="ke-input-text file'+data.result['id']+'" type="hidden" name="file['+data.result['id']+']" id="file" value="'+data.result['name']+'" readonly="readonly" /><input class="file'+data.result['id']+'" type="hidden" name="attachmentsid['+data.result['id']+']" value="'+data.result['id']+'">';
                              var str='<span class="label file'+data.result['id']+'" style="margin-left:5px;">'+data.result['name']+'&nbsp;<b class="deletefile" data-class="file'+data.result['id']+'" data-id="'+data.result['id']+'" title="删除文件" style="display:inline-block;width:12px;height:12px;line-height:12px;text-align:center">x</b>&nbsp;</span><input class="ke-input-text file'+data.result['id']+'" type="hidden" name="file['+data.result['id']+']" id="file" value="'+data.result['name']+'" readonly="readonly" /><input class="file'+data.result['id']+'" type="hidden" name="attachmentsid['+data.result['id']+']" value="'+data.result['id']+'"><input class="path'+data.result['id']+'" type="hidden" name="filepath['+data.result['id']+']" value="'+data.result['path']+'">';
                              $("#fileall").append(str);
                          } else {
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
                  //});
              }


      }
    )},
    /**
     * 删除上传的文件
     */
    deleteuploadFile:function(){
        $('.deletefile').live('mouseover',function(){
            $(this).css({color:"#666",cursor:"pointer",border:"#666 solid 1px",borderRadius:"12px"});
        }).live('mouseout','.deletefile',function(){
            $(this).css({color:"#fff",border:"none",borderRadius:"none"});
        }).live('click','.deletefile',function(){
            var delclassid=$(this).data('id');
            var module=$('#module').val();
            var url='index.php?module='+module+'&action=DeleteFile&id='+delclassid+'&record='+$('input[name="record"]').val();
            AppConnector.request(url).then(
                function(data){
                    if(data['success']) {
                        $('.file'+delclassid).remove();
                    } else {
                        //aDeferred.reject(data['message']);
                    }
                },
                function(error){
                    //aDeferred.reject();
                }
            )
        });

    },
	registerEvents:function(){
	    this.submit();
        this.pre_click_upload();
        this.deleteuploadFile();
		var ckEditorInstance = new Vtiger_CkEditor_Js();
		ckEditorInstance.loadCkEditor("mailcontex");
	}
});