Vtiger_Edit_Js("CustomerStatement_Edit_Js",{ },{

    registerReferenceSelectionEvent : function(container) {
        this._super(container);
        var thisInstance = this;
        jQuery('input[name="contractid"]',container).on(Vtiger_Edit_Js.referenceSelectionEvent, function (e,data){thisInstance.servicecontractschange(data)});
    },

    servicecontractschange:function(data){
        //console.log(data);
            var params = {
                'module': 'CustomerStatement',
                'action': 'BasicAjax',
                'record': data['record'],
                'mode': 'getservicecontractsinfo'
            };
        AppConnector.request(params).then(
            function(data){
                if(data.success){
                   if(data.result){
                       //console.log(data.result);
                       $('input[name="sc_related_to_display"]').val(data.result['accountname']);
                       $('input[name="sc_related_to"]').val(data.result['accountid']);
                       $('input[name="sc_related_to_display"]').attr('readonly',true);

                   }
                }
            }
        );
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
        this.deleteuploadFile(container);
        this.saveData(container);
    }
});
