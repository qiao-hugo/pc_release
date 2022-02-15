Vtiger_Edit_Js("Staypayment_Edit_Js",{ },{
    submitFlag : false,
    registerReferenceSelectionEvent : function(container) {
        this._super(container);
        var thisInstance = this;
        if($("input[name='record']").val()&&$("input[name='isauto']").val()==1){
            var old_record=$("input[name='contractid']").val();
            var old_name=$('input[name="contractid_display"]').val();
        }
        jQuery('input[name="contractid"]',container).on(Vtiger_Edit_Js.referenceSelectionEvent, function (e,data){
            data['old_record']=old_record;
            data['old_name']=old_name;
            thisInstance.servicecontractschange(data)
        });
    },

    servicecontractschange:function(data){
        if($("input[name='record']").val()&&$("input[name='isauto']").val()==1&&data['old_record']!=data['record']){
            Vtiger_Helper_Js.showMessage({type:'error',text:'自动创建的代付款不可更改合同'});
            $('input[name="contractid_display"]').val(data['old_name']);
            $('input[name="contractid"]').val(data['old_record']);
            data['record']=data['old_record'];
        }
        //console.log(data);
            var params = {
                'module': 'Staypayment',
                'action': 'BasicAjax',
                'record': data['record'],
                'mode': 'getservicecontractsinfo'
            };
        AppConnector.request(params).then(
            function(data){
                if(data.success){
                   if(data.result){
                       //console.log(data.result);
                       $('input[name="accountid_display"]').val(data.result['accountname']);
                       $('input[name="accountid"]').val(data.result['accountid']);
                       $('input[name="overdute"]').val(data.result['effectivetime']);
                       $('input[name="accountid_display"]').attr('readonly',true);
                       $('input[name="overdute"]').attr('readonly',true);
                       $('input[name="companycode"]').val(data.result['companycode']);

                   }
                }
            }
        );
    },
    checkFormData:function(data){
        // $('input[name="staypaymentaccountno"]').blur(function () {
        //     var staypaymentaccountno = $('input[name="staypaymentaccountno"]').val();
        //     var staypaymentname = $('input[name="staypaymentname"]').val();
        //     if(staypaymentaccountno&&staypaymentname){
        //         var params = {
        //             'module': 'Staypayment',
        //             'action': 'BasicAjax',
        //             'mode': 'getaccountnocheck',
        //             'staypaymentaccountno':staypaymentaccountno,
        //             'staypaymentname':staypaymentname
        //         };
        //         AppConnector.request(params).then(
        //             function(data){
        //                 console.log(data);
        //                 if(data.result.success){
        //                     str = '<div id="myModal" class="modal" style="">\n' +
        //                         '\t<div class="modal-dialog" style="width: 800px;">\n' +
        //                         '\t\t<div class="modal-content">\n' +
        //                         '\t\t\t<div class="modal-header">\n' +
        //                         '\t\t\t\t<button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>\n' +
        //                         '\t\t\t\t<h4 class="modal-title">未找到与代付方名称一致的回款记录，系统已为您检测到可能回款，您可以点击"同步名称"按钮快捷同步代付方名称</h4>\n' +
        //                         '\t\t\t\t<div style="margin-top: 20px;">\n';
        //
        //                     str += '<table class="table table-bordered equalSplit" style="text-align: center">\n' +
        //                         '<thead style="font-size: 14px;font-weight: bold;background-color: lightgrey">\n' +
        //                         '<td>回款抬头</td>'+
        //                         '<td>支付账号</td>'+
        //                         '<td>金额</td>'+
        //                         '<td>入账日期</td>'+
        //                         '<td>操  作</td>'+
        //                         '</thead>\n' +
        //                         '<tbody>\n';
        //                     $.each(data.result.data,function(key,value){
        //                         console.log(value);
        //                         str +='<tr style="text-align: center;">\n' +
        //                             '<td>'+value.paytitle+'</td>\n'+
        //                             '<td>'+value.paymentaccountno+'</td>\n'+
        //                             '<td>'+value.unit_price+'</td>\n'+
        //                             '<td>'+value.reality_date+'</td>\n'+
        //                             '<td><button class="syncname" data-paytitle="'+value.paytitle+'" style="width:100px;background-color:orange;color:white">同步名称</button></td>\n'+
        //                             '</tr>\n';
        //                     });
        //
        //                     str += ' </tbody>\n' +
        //                         '</table>\n';
        //                     str +=  '\n' +
        //                         '\t\t\t\t</div>\n' +
        //                         '\t\t\t</div>\n' +
        //                         '\t\t\t<div class="modal-footer">\n' +
        //                         '\t\t\t\t<div class=" pull-right cancelLinkContainer"><a class="cancelLink" type="reset" data-dismiss="modal">忽略</a></div>\n' +
        //                         '\t\t\t</div>\n' +
        //                         '\t\t</div>\n' +
        //                         '\t</div>\n' +
        //                         '</div>';
        //                     app.showModalWindow(str);
        //                     $('.modal-backdrop').css({
        //                         "opacity":"0.6",
        //                         "z-index":"0"
        //                     });
        //
        //                     $(".syncname").click(function () {
        //                         var paytitle = $(this).data('paytitle');
        //                         console.log(paytitle);
        //                         $("input[name='staypaymentname']").val(paytitle);
        //                         $('.close').trigger('click');
        //                     });
        //                 }
        //             }
        //         );
        //     }
        // });

        // $('input[name="staypaymentname"]').blur(function () {
        //     var staypaymentaccountno = $('input[name="staypaymentaccountno"]').val();
        //     var staypaymentname = $('input[name="staypaymentname"]').val();
        //     if(staypaymentaccountno&&staypaymentname){
        //         var params = {
        //             'module': 'Staypayment',
        //             'action': 'BasicAjax',
        //             'mode': 'getaccountnocheck',
        //             'staypaymentaccountno':staypaymentaccountno,
        //             'staypaymentname':staypaymentname
        //         };
        //         AppConnector.request(params).then(
        //             function(data){
        //                 console.log(data);
        //                 if(data.result.success){
        //                     str = '<div id="myModal" class="modal" style="">\n' +
        //                         '\t<div class="modal-dialog" style="width: 800px;">\n' +
        //                         '\t\t<div class="modal-content">\n' +
        //                         '\t\t\t<div class="modal-header">\n' +
        //                         '\t\t\t\t<button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>\n' +
        //                         '\t\t\t\t<h4 class="modal-title">未找到与代付方名称一致的回款记录，系统已为您检测到可能回款，您可以点击"同步名称"按钮快捷同步代付方名称</h4>\n' +
        //                         '\t\t\t\t<div style="margin-top: 20px;">\n';
        //
        //                     str += '<table class="table table-bordered equalSplit" style="text-align: center">\n' +
        //                         '<thead style="font-size: 14px;font-weight: bold;background-color: lightgrey">\n' +
        //                         '<td>回款抬头</td>'+
        //                         '<td>支付账号</td>'+
        //                         '<td>金额</td>'+
        //                         '<td>入账日期</td>'+
        //                         '<td>操  作</td>'+
        //                         '</thead>\n' +
        //                         '<tbody>\n';
        //                     $.each(data.result.data,function(key,value){
        //                         console.log(value);
        //                         str +='<tr style="text-align: center;">\n' +
        //                             '<td>'+value.paytitle+'</td>\n'+
        //                             '<td>'+value.paymentaccountno+'</td>\n'+
        //                             '<td>'+value.unit_price+'</td>\n'+
        //                             '<td>'+value.reality_date+'</td>\n'+
        //                             '<td><button class="syncname" data-paytitle="'+value.paytitle+'" style="background-color:orange;color:white">同步名称</button></td>\n'+
        //                             '</tr>\n';
        //                     });
        //
        //                     str += ' </tbody>\n' +
        //                         '</table>\n';
        //                     str +=  '\n' +
        //                         '\t\t\t\t</div>\n' +
        //                         '\t\t\t</div>\n' +
        //                         '\t\t\t<div class="modal-footer">\n' +
        //                         '\t\t\t\t<div class=" pull-right cancelLinkContainer"><a class="cancelLink" type="reset" data-dismiss="modal">忽略</a></div>\n' +
        //                         '\t\t\t</div>\n' +
        //                         '\t\t</div>\n' +
        //                         '\t</div>\n' +
        //                         '</div>';
        //                     app.showModalWindow(str);
        //                     $('.modal-backdrop').css({
        //                         "opacity":"0.6",
        //                         "z-index":"0"
        //                     });
        //
        //                     $(".syncname").click(function () {
        //                         var paytitle = $(this).data('paytitle');
        //                         console.log(paytitle);
        //                         $("input[name='staypaymentname']").val(paytitle);
        //                         $('.close').trigger('click');
        //                     });
        //                 }
        //             }
        //         );
        //     }
        // });
    },
    checkForm:function(){
        $('#EditView').submit(function (event) {
            // var selZ47 = $("select[name='paymenttype']").val();
            // var  overdute = $("#Staypayment_editView_fieldName_overdute").val();
            // if(selZ47=='定期代付'&&!overdute){
            //     event.preventDefault();
            //     alert('代付类型为定期代付,过期时间必选');
            //     return false;
            // }
            // 直接在事件处理程序中返回false
            // var filelen = $(".ke-input-text").length;
            // if(filelen>1){
            //     alert("最多只能上传一个附件");
            //     event.preventDefault();
            //     return false;
            // }
            // filename = $(".ke-input-text").val();
            // arr = filename.split('.');
            // if(arr[1]!='png' && arr[1]!='jpg'){
            //     event.preventDefault();
            //     alert('附件必须为png/jpg图片格式');
            //     return false;
            // }
        });
    },

    registerRecordPreSaveEvent : function(form) {
        var editViewForm = this.getForm();
        var thisInstance = this;
        if(typeof form == 'undefined') {
            form = this.getForm();
        }

        editViewForm.on(Vtiger_Edit_Js.recordPreSave,function(e,data){
            if(thisInstance.submitFlag) return true;
            //查询数据库中是否可以更改
            if($("select[name='payertype']").val()=='incompany'||$("select[name='payertype']").val()=='outcompany'){
                if($("select[name='payertype']").val()=='incompany'){
                    var taxpayers_no=$("input[name=taxpayers_no]").val();
                    if(!taxpayers_no||(taxpayers_no.length!=15&&taxpayers_no.length!=18&&taxpayers_no.length!=20)){
                        e.preventDefault();
                        Vtiger_Helper_Js.showMessage({type:'error',text:'请输入正确的纳税人识别号（15位或18位或20位）'});
                        return false;
                    }
                    $("input[name=idcard]").val('');
                    thisInstance.isNeedFile(e,thisInstance,editViewForm,'in');
                }else{
                    //境外公司没有纳税人识别号
                    $("input[name=taxpayers_no]").val('');
                    $("input[name=idcard]").val('');
                    if($("#fileallexplain").find('.deletefile').length==0){
                        $("#fileallexplain").html('');
                    }
                    thisInstance.isNeedFile(e,thisInstance,editViewForm,'out');
                }
            }else if($("select[name='payertype']").val()=='inperson'||$("select[name='payertype']").val()=='outperson'){
                $("input[name=taxpayers_no]").val('');
                if($("select[name='payertype']").val()=='inperson'){
                    //初步查询身份证号是否合规
                    var idcard=$("input[name='idcard']").val();
                    var payer=$("input[name='payer']").val();
                    var reg =/(^[1-9]\d{5}(18|19|([23]\d))\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$)|(^[1-9]\d{5}\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}$)/;
                    if(!reg.test(idcard)){
                        e.preventDefault();
                        Vtiger_Helper_Js.showMessage({type:'error',text:'请输入正确的身份证号码'});
                        return false;
                    }

                    //查询数据库中是否有这个身份证
                    var params = {
                        'module': 'Staypayment',
                        'action': 'BasicAjax',
                        'mode': 'getIdCardCheck',
                        'idcard':idcard,
                        'payer':payer,
                        'record':$('input[name="record"]').val()
                    };
                    AppConnector.request(params).then(
                        function(data){
                            console.log(data);
                            if(!data.result.flag){
                                e.preventDefault();
                                Vtiger_Helper_Js.showMessage({type:'error',text:data.result.msg});
                                return false;
                            }else{
                                thisInstance.isNeedFile(e,thisInstance,editViewForm,'in');
                            }
                        }
                    );
                }else{
                    thisInstance.isNeedFile(e,thisInstance,editViewForm,'out');
                }
            }
            return  false;
        });
    },

    isNeedFile:function(e,thisInstance,editViewForm,type){
        if(($("input[name='taxpayers_no']").val()!=''&&$("input[name='payer']").val()!='')||($("input[name='idcard']").val()!=''&&$("input[name='payer']").val()!='')||type=='out'){
            var params = {
                'module': 'Staypayment',
                'action': 'BasicAjax',
                'mode': 'isNeedFile',
                'record':$("input[name='record']").val(),
                'taxpayers_no':$("input[name='taxpayers_no']").val(),
                'payer':$("input[name='payer']").val(),
                'accountid':$("input[name='accountid']").val(),
                'idcard':$('input[name="idcard"]').val(),
                'stay_type':$('select[name="payertype"]').val(),
                'type':type,
                'staypaymenttype':$("select[name='staypaymenttype']").val(),
                'currencytype':$('select[name="currencytype"]').val(),
                'staypaymentjine':$('input[name="staypaymentjine"]').val()
            };
            AppConnector.request(params).then(
                function(data){
                    console.log(data);
                    if(data.result.flag){
                        if(data.result.type==1){
                            e.preventDefault();
                            Vtiger_Helper_Js.showMessage({type:'error',text:data.result.msg});
                            return false;
                        }else{
                            if($("#fileallexplain").find('.deletefile').length>0&&$("#Staypayment_editView_fieldName_explaintext").val()!=''){
                                //有附件
                                thisInstance.submitFlag=true;
                                editViewForm.submit();
                                return true;
                            }
                            e.preventDefault();
                            Vtiger_Helper_Js.showMessage({type:'error',text:'请上传不同客户代付款说明文件和填写不同客户代付款申请说明'});
                            $("#explainfileMust").show();
                            $("#explaintextMust").show();
                            return false;
                        }
                    }else{
                        //不需要，直接去掉附件
                        if($("#fileallexplain").find('.deletefile').length==0){
                            $("#fileallexplain").html('');
                        }
                        $("#explainfileMust").hide();
                        $("#explaintextMust").hide();
                        thisInstance.submitFlag=true;
                        editViewForm.submit();
                        return true;
                    }
                }
            );
        }
    },

    // paymenttype:function(){
    //     $("select[name='paymenttype']").on("change",function (k, v) {
    //         var paymentypeval = $(this).val();
    //         if(paymentypeval=='定期代付'){
    //             $("#EditView > table > tbody > tr:nth-child(5) > td:nth-child(1)").show();
    //             $("#Staypayment_editView_fieldName_overdute").parent().parent().parent().parent().parent().show();
    //             return;
    //         }
    //         $("#EditView > table > tbody > tr:nth-child(5) > td:nth-child(1)").hide();
    //         $("#Staypayment_editView_fieldName_overdute").parent().parent().parent().parent().parent().hide();
    //     })
    // },

    /**
     * 代付款类型变更
     */
    staypaymenttype:function(){
        $("select[name='staypaymenttype']").on("change",function (e) {
            if($("input[name='record']").val()&&$("input[name='isauto']").val()==1&&$(this).val()!=$("input[name='stay_type']").val()){
                e.preventDefault();
                Vtiger_Helper_Js.showMessage({type:'error',text:'自动创建的代付款不可更改代付款类型'});
                $("select[name='staypaymenttype']").val($("input[name='stay_type']").val());
                $("select[name='staypaymenttype']").trigger('liszt:updated');
                return false;
            }else{
                //可以变更时进行变更
                if($(this).val()=='nofixation'){
                    $('input[name="startdate"]').closest('td').prev().show();
                    $('input[name="startdate"]').closest('td').show();
                    $('input[name="enddate"]').closest('td').prev().show();
                    $('input[name="enddate"]').closest('td').show();
                    $('select[name="currencytype"]').closest('td').prev().hide();
                    $('select[name="currencytype"]').closest('td').hide();
                    $('input[name="staypaymentjine"]').closest('td').prev().hide();
                    $('input[name="staypaymentjine"]').closest('td').hide();
                    $("#enddateMust").show();
                    $("#startdateMust").show();
                    $("#staypaymentjineMust").hide();
                    $("#currencytypeMust").hide();
                }else if($(this).val()=='fixation'){
                    $('input[name="startdate"]').closest('td').prev().hide();
                    $('input[name="startdate"]').closest('td').hide();
                    $('input[name="enddate"]').closest('td').prev().hide();
                    $('input[name="enddate"]').closest('td').hide();
                    $('select[name="currencytype"]').closest('td').prev().show();
                    $('select[name="currencytype"]').closest('td').show();
                    $('input[name="staypaymentjine"]').closest('td').prev().show();
                    $('input[name="staypaymentjine"]').closest('td').show();
                    $("#enddateMust").hide();
                    $("#startdateMust").hide();
                    $("#staypaymentjineMust").show();
                    $("#currencytypeMust").show();
                }
            }
        });
    },


    totalprefix: function () {
        if (!$('input[name="record"]').val()) {
            var type = $('select[name="currencytype"]').val('人民币');
            $("select[name='currencytype']").next('div').children('a').children('span').text('人民币');
            $('#Staypayment_editView_fieldName_staypaymentjine').prev().html('人民币');
        }else{
            var type = $('select[name="currencytype"]').val();
            $('#Staypayment_editView_fieldName_staypaymentjine').prev().html(type);
        }
        $('select[name="currencytype"]').on('change', function () {
            var type = $('select[name="currencytype"]').val();
            if (type) {
                $('#Staypayment_editView_fieldName_staypaymentjine').prev().text(type);
            }
        })
    },
    limitTwoPoint:function(){
      $("#Staypayment_editView_fieldName_staypaymentjine").on('blur',function () {
          var staypaymentjine = $("#Staypayment_editView_fieldName_staypaymentjine").val();
          staypaymentjine = staypaymentjine==''?0:staypaymentjine;
          $("#Staypayment_editView_fieldName_staypaymentjine").val(parseFloat(staypaymentjine).toFixed(2));
      })
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
          if($("select[name='staypaymenttype']").val=='nofixation'){
              //非固定金额
              if($("input[name='startdate']").val==''){
                  e.preventDefault();
                  Vtiger_Helper_Js.showMessage({type:'error',text:'代付款开始时间不能为空'});
                  return;
              }
              if($("input[name='enddate']").val==''){
                  e.preventDefault();
                  Vtiger_Helper_Js.showMessage({type:'error',text:'代付款到期时间不能为空'});
                  return;
              }
              var start = new Date($("input[name='startdate']").val().replace("-", "/").replace("-", "/"));
              var end = new Date($("input[name='enddate']").val().replace("-", "/").replace("-", "/"));
              if(start>end){
                  e.preventDefault();
                  Vtiger_Helper_Js.showMessage({type:'error',text:'代付款开始时间不能大于代付款到期时间'});
                  return;
              }
              $("select[name='currencytype']").val('');
              $("input[name='staypaymentjine']").val('');
          }else if($("select[name='staypaymenttype']").val=='fixation'){
             //固定金额
              if($("select[name='currencytype']").val==''){
                  e.preventDefault();
                  Vtiger_Helper_Js.showMessage({type:'error',text:'货币类型不能为空'});
                  return;
              }
              if($("input[name='staypaymentjine']").val==''){
                  e.preventDefault();
                  Vtiger_Helper_Js.showMessage({type:'error',text:'代付款金额不能为空'});
                  return;
              }
              $("select[name='currencytype']").val('');
              $("input[name='staypaymentjine']").val('');
          }
      });
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

    init:function(){
        $("select[name='payertype']").on("change",function (k, v) {
            var payertype = $(this).val();
            if(payertype=='inperson'||payertype=='outperson'){
                $("input[name='idcard']").closest('td').prev().show();
                $("input[name='idcard']").closest('td').show();
                if(payertype=='inperson'){
                    $("#idcardMust").show();
                }else{
                    $("#idcardMust").hide();
                }
                $("input[name='taxpayers_no']").closest('td').prev().hide();
                $("input[name='taxpayers_no']").closest('td').hide();
                $("#taxpayersnoMust").hide();
            }else if(payertype=='incompany'||payertype=='outcompany'){
                $("input[name='idcard']").closest('td').prev().hide();
                $("input[name='idcard']").closest('td').hide();
                $("#idcardMust").hide();
                if(payertype=='outcompany'){
                    $("input[name='taxpayers_no']").closest('td').prev().hide();
                    $("input[name='taxpayers_no']").closest('td').hide();
                    $("#taxpayersnoMust").hide();
                }else{
                    $("input[name='taxpayers_no']").closest('td').prev().show();
                    $("input[name='taxpayers_no']").closest('td').show();
                    $("#taxpayersnoMust").show();
                }
            }
        });
        if(!$("input[name='record']").val()){
            $("select[name='payertype']").val('inperson');
            $("select[name='payertype']").trigger('change');
            $("select[name='payertype']").trigger('liszt:updated');
            $("input[name='taxpayers_no']").closest('td').prev().hide();
            $("input[name='taxpayers_no']").closest('td').hide();
            $("#taxpayersnoMust").hide();
        }else{
            $("select[name='payertype']").trigger('change');
            $("select[name='payertype']").trigger('liszt:updated');
            $("select[name='staypaymenttype']").trigger('change');
            $("select[name='staypaymenttype']").trigger('liszt:updated');
        }
    },

    getuploadZzFile:function(){
        if($('#explainfile').length>0){
            var module=$('#module').val();
            KindEditor.ready(function(K) {
                var uploadbutton = K.uploadbutton({
                    button : K('#uploadexplainButton')[0],
                    fieldName : 'Explainfile',
                    extraParams :{
                        __vtrftk:$('input[name="__vtrftk"]').val(),
                        record:$('input[name="record"]').val()
                    },
                    url : 'index.php?module='+module+'&action=FileUpload&record='+$('input[name="record"]').val(),
                    afterUpload : function(data) {
                        if (data.success ==true) {
                            $('.explaindelete').remove();
                            var str='<span class="label file'+data.result['id']+'" style="margin-left:5px;">'+data.result['name']+'&nbsp;<b class="deletefile" data-class="file'+data.result['id']+'" data-id="'+data.result['id']+'" title="删除文件" style="display:inline-block;width:12px;height:12px;line-height:12px;text-align:center">x</b>&nbsp;</span><input class="ke-input-text file'+data.result['id']+'" type="hidden" name="explainfile['+data.result['id']+']" id="explainfile" value="'+data.result['name']+'" readonly="readonly" /><input class="file'+data.result['id']+'" type="hidden" name="attachmentsid['+data.result['id']+']" value="'+data.result['id']+'">';
                            if($("#fileallexplain").find('.deletefile').length>0){
                                $("#fileallexplain").append(str);
                            }else{
                                $("#fileallexplain").html(str);
                            }
                        } else {
                            Vtiger_Helper_Js.showMessage({type:'error',text:data.msg});
                            e.preventDefault();
                            return false;
                        }
                    },
                    afterError : function(str) {
                    }
                });
                uploadbutton.fileBox.change(function(e) {
                    uploadbutton.submit();
                });
                $('.fileUploadContainer').find('form').css({width:"54px"});
                $('.fileUploadContainer').find('form').find('.btn-info').css({width:"54px",marginLeft:"-15px"});
                $('.ke-upload-file').css('z-index','0');
            });
        }
    },
    /**
     * 删除上传的文件
     */
    deleteuploadZzFile:function(){
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
        this.checkForm();
        this.registerReferenceSelectionEvent(container);
        this.checkFormData(container);
        // this.paymenttype(container);
        this.totalprefix(container);
        this.limitTwoPoint(container);
        this.saveData(container);
        this.deleteuploadFile(container);
        $("#Staypayment_editView_fieldName_overdute").attr('readonly',true);
        this.registerRecordPreSaveEvent();
        this.staypaymenttype();
        this.init();
        this.getuploadZzFile();
        this.deleteuploadZzFile();
    }
});