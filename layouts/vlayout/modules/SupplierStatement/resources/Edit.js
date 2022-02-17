Vtiger_Edit_Js("SupplierStatement_Edit_Js",{ },{

    registerReferenceSelectionEvent : function(container) {
        this._super(container);
        var thisInstance = this;
        jQuery('input[name="suppliercontractsid"]',container).on(Vtiger_Edit_Js.referenceSelectionEvent, function (e,data){thisInstance.servicecontractschange(data)});
    },

    servicecontractschange:function(data){
        //console.log(data);
            var params = {
                'module': 'SupplierStatement',
                'action': 'BasicAjax',
                'record': data['record'],
                'mode': 'getservicecontractsinfo'
            };
        AppConnector.request(params).then(
            function(data){
                if(data.success){
                   if(data.result){
                       //console.log(data.result);
                       $('input[name="vendorid_display"]').val(data.result['accountname']);
                       $('input[name="vendorid"]').val(data.result['accountid']);
                       $('input[name="vendorid_display"]').attr('readonly',true);

                   }
                }
            }
        );
    },
    bindStagesubmit:function(){
        $('.details').on('click','.stagesubmit',function(){
            var msg={
                'message':"确定要审核工单阶段"+name+"?",
            };
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){

                var params={};
                params['record'] = $('#recordId').val();
                params['stagerecordid'] = $('#stagerecordid').val();
                params['action'] = 'SaveAjax';
                params['module'] = 'SalesorderWorkflowStages';
                params['mode'] = 'updateSalseorderWorkflowStages';
                params['src_module'] = app.getModuleName();
                params['checkname'] = $('#backstagerecordname').val();
                params['customer']=$("#customer").val()==undefined?0:$("#customer").val();
                params['customername']=$("#customer").find("option:selected").text()==undefined?'':$("#customer").find("option:selected").text();
                //ie9下post请求是失败的，如果get可以的请修改
                var d={};
                d.data=params;
                d.type = 'GET';
                var progressIndicatorElement = jQuery.progressIndicator({
                    'message' : '亲,正在拼命处理,请耐心等待哟',
                    'position' : 'html',
                    'blockInfo' : {'enabled' : true}
                });

                AppConnector.request(d).then(
                    function(data){
                        if(data.success==true){
                            Vtiger_Helper_Js.showMessage({type:'success',text:'审核成功'});
                            window.location.reload();
                        }else{
                            Vtiger_Helper_Js.showMessage({type:'error',text:'审核失败,原因'+data.error.message});
                        }
                        progressIndicatorElement.progressIndicator({
                            'mode' : 'hide'
                        });
                    },function(){}
                );
            },function(error, err) {});
        });
    },
    saveData:function(){
      $(".btn-success").on("click",function (e) {
          // var staypaymentjine = $("#Staypayment_editView_fieldName_staypaymentjine").val();
          var file = $("#file");
          if(!file || (file &&(file.val()==''|| file.val()==undefined))){
              e.preventDefault();
              Vtiger_Helper_Js.showMessage({type:'error',text:'未上传附件'});
              return;
          }
        var supplierstatementtype = $("select[name='supplierstatementtype']").val();
        var platform = $("select[name='platform']").val();
        if(supplierstatementtype=='media'&&!platform){
            e.preventDefault();
            Vtiger_Helper_Js.showMessage({type:'error',text:'结算单为媒体时，投放平台必填'});
            return;
        }
      })
    },
    /**
     * 删除上传的文件
     */
    deleteuploadFile:function(){
        $('form').on('mouseover','.deletefile',function(){
            $(this).css({color:"#666",cursor:"pointer",border:"#666 solid 1px",borderRadius:"12px"});
        }).on('mouseout','.deletefile',function(){
            $(this).css({color:"#fff",border:"none",borderRadius:"none"});
        }).on('click','.deletefile',function(){
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
	registerBasicEvents : function(container) {
        this._super(container);
        this.registerReferenceSelectionEvent(container);
        this.saveData(container);
        this.deleteuploadFile(container);

        $("select[name='supplierstatementtype']").on("change",function (e) {
            console.log(11);
            var supplierstatementtype = $("select[name='supplierstatementtype']").val();
            console.log(supplierstatementtype);
            if(supplierstatementtype=='media'){
                $('.platform').show();
            }else{
                $('.platform').hide();
            }

        })
    }
});
