/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("ContractsAgreement_Edit_Js", {}, {
    seletedIndexValue:'',
    seletedValue:'',
    eleccontractSubmit:true,
    isonbeforeunload:true,
    customizedData:[],
    parentView:{},
    registerReferenceSelectionEvent: function (container) {
        var thisInstance = this;
        jQuery('input[name="servicecontractsid"]', container).on(Vtiger_Edit_Js.referenceSelectionEvent, function (e, data) {
            relatedchange();
        });

        function relatedchange() {
            var sparams = {
                'module': 'ContractsAgreement',
                'action': 'BasicAjax',
                'record': $('input[name="servicecontractsid"]').val(),
                'mode': 'getAccount'
            };
            AppConnector.request(sparams).then(
                function (datas) {
                    if (datas.success == true && datas.result.flag) {
                        if(datas.result.accountid>0){
                            $('input[name="account_id"]').val(datas.result.accountid);
                            $('input[name="account_id_display"]').val(datas.result.accountname);
                            thisInstance.setElecInfo();
                        }
                        $("select[name='invoicecompany']").empty();
                        if(datas.result.invoicecompany!=''){
                            $("select[name='invoicecompany']").append('<option value="' + datas.result.invoicecompany + '">' + datas.result.invoicecompany + '</option>');
                        }
                        $("select[name='invoicecompany']").trigger('liszt:updated');
                    }
                }
            )
        }
        jQuery('input[name="account_id"]', container).on(Vtiger_Edit_Js.referenceSelectionEvent, function (e, data) {
            thisInstance.setElecInfo();
        });
    },
    registerResultEvent: function (form) {
        var thisInstance = this;
        if (typeof form == 'undefined') {
            form = this.getForm();
        }
        form.on(Vtiger_Edit_Js.recordPreSave, function (e, data) {

            if ('无锡珍岛数字生态服务平台技术有限公司' == $('select[name="invoicecompany"]').val() && !$('select[name="sealplace"]').val()) {
                Vtiger_Helper_Js.showMessage({type:'error', text:'无锡珍岛数字生态服务平台技术有限公司必须选择用章地点'});
                e.preventDefault();
                return false;
            }

            var attachments=$('input[name*="attachmentsid["]');
            var signaturetype=$('select[name="signaturetype"]').val();
            if(attachments.length==0 )
            {
                Vtiger_Helper_Js.showMessage({type:'error',text:"合同附件必填!!!"});
                e.preventDefault();
                return false;
            }
            if(signaturetype=='eleccontract'){
                if(attachments.length!=1 )
                {
                    Vtiger_Helper_Js.showMessage({type:'error',text:"合同附件只能有一个!!!"});
                    e.preventDefault();
                    return false;
                }
            }
            if(thisInstance.checkAccountDiff()){
                Vtiger_Helper_Js.showMessage({type:'error',text:"所选客户与合同上客户不是同一个!!!"});
                e.preventDefault();
               return false;
            }
            if(thisInstance.checkOrderIsCancel()){
                Vtiger_Helper_Js.showMessage({type:'error',text:"客户已签署且已确认到款的电子合同，无法直接提交合同作废流程!!!"});
                e.preventDefault();
                return false;
            }
            if(signaturetype=='eleccontract' && thisInstance.eleccontractSubmit){
                var templateId=$('select[name="eleccontracttplid"]').val();
                var oldeleccontracttplid=$('input[name="oldeleccontracttplid"]').val();
                var oldeleccontractid=$('input[name="oldeleccontractid"]').val();
                if(templateId==oldeleccontracttplid && oldeleccontractid>0) {
                    thisInstance.getTPLViewByContractId(oldeleccontractid)
                }else{
                    var invoicecompany=$('select[name="invoicecompany"]').val();
                    var senderName=$('input[name="originator"]').val();
                    var senderPhone=$('input[name="originatormobile"]').val();
                    var receiverName=$('select[name="elereceiver"]').val();
                    var receiverPhone=$('input[name="elereceivermobile"]').val();
                    var type=0;
                    var record=$('input[name="record"]').val();
                    var templateId=$('select[name="eleccontracttplid"]').val();
                    var servicecontractsid=$('input[name="servicecontractsid"]').val();
                    var account_id=$('input[name="account_id"]').val();
                    var expirationTime=0;
                    var oldeleccontractid=$('input[name="oldeleccontractid"]').val();
                    var attachmentsids=$('input[name^="attachmentsid["]')
                    var postData = {
                        "module": 'ServiceContracts',
                        "action": "BasicAjax",
                        "templateId": templateId,
                        "updateRecord": record,
                        'mode': 'erpUpload',
                        "invoicecompany":invoicecompany,
                        "servicecontractsid":servicecontractsid,
                        "accountid":account_id,
                        "needAudit":1,
                        "oldeleccontractid":oldeleccontractid,
                        "senderName":senderName,
                        "senderPhone":senderPhone,
                        "receiverName":receiverName,
                        "receiverPhone":receiverPhone,
                        "fileid":attachmentsids.val()
                    };
                    var progressIndicatorElement = jQuery.progressIndicator({
                        'message': '正在获取电子合同相关信息...',
                        'position': 'html',
                        'blockInfo': {'enabled': true}
                    });
                    AppConnector.request(postData).then(function (res) {
                        progressIndicatorElement.progressIndicator({'mode': 'hide'});
                        if (res && res.success) {
                            window.custromurl=res.data;
                            $('input[name="eleccontracttplurl"]').val(res.data.orgurl);
                            //thisInstance.getTPLViewByContractId(res.data.contractId)
                            thisInstance.contractSubmitPreView()
                        } else {
                            Vtiger_Helper_Js.showMessage({type:'error',text:res.msg});
                        }
                    }, function () {
                        return false;
                    });
                }
                e.preventDefault();
                return false;
            }
        })
    },
    saveAndReplaceParams:function(){
        var record=$('input[name="record"]').val();
        var invoicecompany=$('select[name="invoicecompany"]').val();
        var senderName=$('input[name="originator"]').val();
        var senderPhone=$('input[name="originatormobile"]').val();
        var receiverName=$('select[name="elereceiver"]').val();
        var receiverPhone=$('input[name="elereceivermobile"]').val();
        var templateId=$('select[name="eleccontracttplid"]').val();
        var contractattribute=$('select[name="contractattribute"]').val();
        var clientproperty=$('select[name="clientproperty"]').val();
        var servicecontractsid=$('input[name="record"]').val();
        var expirationTime=$('input[name="actualeffectivetime"]').val();
        var oldeleccontractid=$('input[name="oldeleccontractid"]').val();
        var total=$('input[name="total"]').val();
        var accountid=$('input[name="sc_related_to"]').val();
        var needAudit=contractattribute!='standard'?1:0;
        var postData = {
            "module": 'ServiceContracts',
            "action": "BasicAjax",
            "templateId": templateId,
            "updateRecord": record,

            "invoicecompany":invoicecompany,
            "servicecontractsid":servicecontractsid,
            "needAudit":needAudit,
            "contractattribute":contractattribute,
            "expirationTime":expirationTime,
            "clientproperty":clientproperty,
            "oldeleccontractid":oldeleccontractid,
            "senderName":senderName,
            "total":total,
            "accountid":accountid,
            "senderPhone":senderPhone,
            "receiverName":receiverName,
            "receiverPhone":receiverPhone
        };
        return postData;
    },
    contractSubmitPreView:function(){
        var thisInstance=this;
        var msg = {
            'message': ' ',
        };
        Vtiger_Helper_Js.showConfirmationBox(msg).then(function (data) {
        });
        $('.modal-dialog').remove();
        $('.modal-footer').remove();
        $('.bootbox-confirm').append('<div style="position: absolute;left:0px;top:0px;width:100%;height:100%;"><iframe id="prevcontact" src="/contracttplcustom/index.html" style="width:100%;height:100%;background-color:#ffffff;"/></div>');
        $('#prevcontact').on('load', function () {
            var iframeThis = this;
            // 确认提交
            var confirmflag=false;
            var contractattribute=$('select[name="contractattribute"]').val();
            if(contractattribute=='standard'){
                $('.popup-titlechange',iframeThis.contentDocument).text('确认发送此电子合同？');
                $('.non-standard',iframeThis.contentDocument).text('标准');
            }else{
                $('.non-standard',iframeThis.contentDocument).text('定制');
                $('.popup-titlechange',iframeThis.contentDocument).html('<span style="font-size:16px;">此电子合同为定制合同，需要审批流程完成后系统自动发送合同签署短信</span>');
            }
            $('.popup-confirm',iframeThis.contentDocument).click(function () {
                if(confirmflag){
                    return false;
                }
                confirmflag=true;
                var postData=thisInstance.saveAndReplaceParams();
                postData.custromData=thisInstance.customizedData;
                postData.mode='erpContractSet';
                postData.tplname=custromurl.name;
                postData.tplurl=custromurl.orgurl;
                AppConnector.request(postData).then(function(res){
                    if (res && res.success) {
                        $('input[name="eleccontractid"]').val(res.data.contractId);
                        $('input[name="eleccontractidurl"]').val(res.data.contractUrl);
                        thisInstance.eleccontractSubmit=false;
                        $('[type="submit"]',parent.document).trigger('click');
                    }else{
                        confirmflag=false;
                        Vtiger_Helper_Js.showMessage({type: 'error', text: res.msg});
                    }
                });
            });
            $(".savebtn", iframeThis.contentDocument).click(function () {//添加点击事件
                var childrenWindow=iframeThis.contentWindow;
                var comfirmdata=childrenWindow.childrenVue.confirm();
                if(comfirmdata){
                    thisInstance.customizedData=comfirmdata;
                    $('.reveiver-name', iframeThis.contentDocument).html($('select[name="elereceiver"]').val());
                    $('.reveiver-phone', iframeThis.contentDocument).html($('input[name="elereceivermobile"]').val());
                    $('.ensrue-popup', iframeThis.contentDocument).show();
                }

            });
            $(".popup-cancel", iframeThis.contentDocument).click(function () {//添加点击事件
                if(confirmflag){
                    return false;
                }
                $('.reveiver-name', iframeThis.contentDocument).html("");
                $('.reveiver-phone', iframeThis.contentDocument).html("");
                $('.ensrue-popup', iframeThis.contentDocument).hide();
            });
            $('.back', iframeThis.contentDocument).click(function () {
                $('.bootbox-confirm').modal('hide');
            });
            var oldeleccontractid=$('input[name="oldeleccontractid"]').val();
            var record=$('input[name="record"]').val();
            if(oldeleccontractid>0 && record>0){
                var oldfile=$('input[name="oldfile"]').val();
                var $attachmentsid=$('input[name^="attachmentsid["]')
                if($attachmentsid.length!=1){
                    return '';
                }
                var oldfileArr=oldfile.split('##');
                if($attachmentsid.val()!=oldfileArr[1]){
                    return '';
                }
                var postData = {
                    "module": 'ServiceContracts',
                    "action": "BasicAjax",
                    "contractId": oldeleccontractid,
                    "updateRecord": record,
                    "mode":'erpGetArea'
                }
                AppConnector.request(postData).then(function (res) {
                    if (res && res.success) {
                        var childrenWindow=iframeThis.contentWindow;
                        childrenWindow.childrenVue.getAreaData(res);
                    }
                }, function () {
                    return false;
                });
            }
        });
    },
    checkAccountDiff:function(){
        var params={};
        params.data = {
            "module": "ContractsAgreement",
            "action": "BasicAjax",
            "mode": "checkAccountid",
            "accountid": $('input[name="account_id"]').val(),
            "servicecontractsid": $('input[name="servicecontractsid"]').val(),
        };
        params.async=false;
        var returnflag;
        AppConnector.request(params).then(
            function (data) {
                if (data && data.success) {
                    if(data.result){
                        returnflag=true;
                    }else{
                        returnflag=false;
                    }
                } else {
                    returnflag=false;
                }
            });
        return  returnflag;
    },
    checkOrderIsCancel:function(){
        var params={};
        params.data = {
            "module": "ContractsAgreement",
            "action": "BasicAjax",
            "mode": "checkOrderIsCancel",
            "servicecontractsid": $('input[name="servicecontractsid"]').val(),
            "supplementarytype":$("select[name='supplementarytype']").val()
        };
        params.async=false;
        var returnflag;
        AppConnector.request(params).then(
            function (data) {
                if (data && data.success) {
                    if(data.result){
                        returnflag=true;
                    }else{
                        returnflag=false;
                    }
                } else {
                    returnflag=false;
                }
            });
        return  returnflag;
    },
    signaturetypeChange:function(){
        var thisInstance=this;
        var oldsignaturetypevale=$('select[name="signaturetype"]').val();
        $('form').on('change','select[name="signaturetype"]',function(){
            var thisVale=$(this).val();
            if(thisVale!=oldsignaturetypevale) {
                thisInstance.isonbeforeunload=false;
                if(thisVale=='eleccontract'){
                    window.location.href='/index.php?module=ContractsAgreement&view=Edit&signaturetypehref=eleccontract';
                }else{
                    window.location.href='/index.php?module=ContractsAgreement&view=Edit';
                }
            }
        });
        //$('select[name="eleccontracttplid"]').parent('td').append('<button type="button" class="btn preeleccontracttpl" data-name="eleccontracttplid" style="display:inline-block;vertical-align:top" disabled="disabled">预览</button>');
    },
    displayTPL:function(){
        return false;
        var thisInstance=this;
        $('body').on('click','.preeleccontracttpl',function(){
            var dataValue=$('select[name="eleccontracttplid"]').val();
            if(!dataValue){
                return false;
            }
            var dataurl=$('select[name="eleccontracttplid"]').find('option:selected').data('url');
            var dataname=$('select[name="eleccontracttplid"]').find('option:selected').data('name');
            var res={};
            res.data={'contract':dataurl,'name':dataname,
                'inputs':[],
                'reveiver':{'name':$('select[name="elereceiver"]').val(),'phone':$('input[name="elereceivermobile"]').val()}
            };
            var message = ' ';
            var thisWidth=$(window).width();
            var thisHeight=$(window).height();
            var thisWidthorg=thisWidth;
            thisWidth=thisWidth*0.9;
            var msg = {
                'message': message,
                'width':thisWidth+'px'
            };
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(data){

            });
            $('.modal-dialog').remove();
            $('.modal-footer').remove();
            window.parentView=res.data;
            thisInstance.parentView=res.data
            $('.bootbox-confirm').append('<div id="u928" class="ax_default box_1" style="position:absolute;right:15px;top:15px;width: 53px;height: 36px;opacity:0.7;">\n' +
                '        <div class="bootbox-close-button close" data-dismiss="modal" aria-hidden="true" style="position: absolute;left: 0px;top: 0px;width: 53px;height: 36px;background: inherit;background-color: rgba(255, 255, 255, 1);box-sizing: border-box;border-width: 1px;border-style: solid;border-color: rgba(255, 255, 255, 1);border-radius: 5px;-moz-box-shadow: none;-webkit-box-shadow: none;box-shadow: none;text-align: center;line-height:30px;opacity:1;"><img id="u930_img" class="img " src="libraries/images/u930.png"></div>\n' +
                '          <p><span></span></p>' +
                '        </div>' +
                '      </div>');
            $('.bootbox-confirm').append('<div style="position: absolute;left:'+(thisWidthorg/2-600)+'px;top:10px;width:1200px;height:'+(thisHeight-10)+'px;background-color:#F4F5F7;border:#F4F5F7 solid 3px;padding;1px;"><iframe src="/contracttpl/pcprev.html" style="width:1196px;height:100%;color:#ffffff;"/></div>');
        });
    },
    setElecInfo:function(flag){
        var thisInance=this;
        var signaturetype=$('select[name="signaturetype"]').val();
        if(signaturetype!='eleccontract'){
            return false;
        }
        var accountid=$('input[name="account_id"]').val();
        if(accountid<1){
            return false;
        }
        var sparams = {
            'module': 'ServiceContracts',
            'action': 'BasicAjax',
            'record': $('input[name="account_id"]').val(),
            'mode': 'getsmownerid',
            'signaturetype':signaturetype
        };
        AppConnector.request(sparams).then(
            function (datas) {
                if (datas.success == true) {
                    var contacts=datas.result.contacts;
                    if(flag==2){
                        if(contacts.length!=0 && signaturetype=='eleccontract'){
                            var elereceiver=$('select[name="elereceiver"]').val();
                            $('select[name="elereceiver"]').empty();
                            var str="";
                            $.each(contacts,function(n,value){
                                str += "<option value="+value.linkname+' data-linkname="'+value.linkname+'" data-mobile="'+value.mobile+'" selected>'+value.linkname+"</option>";
                            });
                            $('select[name="elereceiver"]').append(str);
                            $('select[name="elereceiver"]').val(elereceiver);
                            $('select[name="elereceiver"]').trigger('liszt:updated');
                            thisInance.getTPList(flag);
                        }
                    }else{
                        $('select[name="elereceiver"]').val('');
                        $('input[name="elereceivermobile"]').val('');
                        $('input[name="originator"]').val('');
                        $('input[name="originatormobile"]').val('');
                        if(contacts.length!=0 && signaturetype=='eleccontract'){
                            $('input[name="elereceivermobile"]').val(contacts[0].mobile);
                            $('input[name="originator"]').val(datas.result.user.name);
                            $('input[name="originatormobile"]').val(datas.result.user.mobile);
                            $('select[name="elereceiver"]').empty();
                            var str="";
                            $.each(contacts,function(n,value){
                                if(n>0){
                                    str += "<option value="+value.linkname+' data-linkname="'+value.linkname+'" data-mobile="'+value.mobile+'">'+value.linkname+"</option>";
                                }else{
                                    str += "<option value="+value.linkname+' data-linkname="'+value.linkname+'" data-mobile="'+value.mobile+'" selected>'+value.linkname+"</option>";
                                }
                            })
                            $('select[name="elereceiver"]').append(str);
                            $('select[name="elereceiver"]').trigger('liszt:updated');
                            thisInance.getTPList(flag);
                        }
                    }

                }
            }
        )
    },
    /**
     *获取列表的信息
     */
    getTPList:function(flag){
        return false;
        var thisInstance=this;
        var record=$('input[name="record"]').val();
        var postData = {
            "module": 'ContractsAgreement',
            "action": "BasicAjax",
            'mode': 'getElecTPLList'
        };
        $('.preeleccontracttpl').removeClass('btn-info');
        var eleccontracttpl= $('select[name="eleccontracttplid"]').val();
        AppConnector.request(postData).then(
            function(res){
                if(res && res.success) {
                    var data=res.data;
                    $('select[name="eleccontracttplid"]').empty();
                    $('.preeleccontracttpl').attr('disabled','disabeld');
                    $('.preeleccontracttpl').removeClass('btn-info');
                    if(data.length>0){
                        var optionsStr=''
                        $.each(data,function(i,v){
                            if(record>0){
                                if(v.id==eleccontracttpl){
                                    $('input[name="eleccontracttpl"]').val(v.name);
                                    $('input[name="eleccontracttplurl"]').val(v.url);
                                    optionsStr+='<option value="'+v.id+'" data-url="'+v.url+'" data-name="'+v.name+'" data-json=\''+JSON.stringify(v)+'\'>'+v.name+'</option>';
                                    return false;
                                }

                            }else{
                                if(i==0){
                                    $('input[name="eleccontracttpl"]').val(v.name);
                                    $('input[name="eleccontracttplurl"]').val(v.url);
                                }
                                optionsStr+='<option value="'+v.id+'" data-url="'+v.url+'" data-name="'+v.name+'" data-json=\''+JSON.stringify(v)+'\'>'+v.name+'</option>';
                            }
                        });
                        $('select[name="eleccontracttplid"]').append(optionsStr);
                        if(flag==2){
                            $('select[name="eleccontracttplid"]').val(eleccontracttpl);
                        }
                        $('.preeleccontracttpl').addClass('btn-info');
                        $('.preeleccontracttpl').removeAttr('disabled');
                    }
                    $('select[name="eleccontracttplid"]').trigger('liszt:updated')
                }
            }
        );
    },
    eleccontracttplidChange:function(){
        $('body').on('click','select[name="eleccontracttplid"]',function() {
            $('input[name="eleccontracttpl"]').val($('select[name="eleccontracttplid"]').find('option:selected').text());
            $('input[name="eleccontracttplurl"]').val($('select[name="eleccontracttplid"]').find('option:selected').data('url'));
        });
        $('#EditView').on('change','select[name="elereceiver"]',function(){
            $('input[name="elereceivermobile"]').val($(this).find('option:selected').data('mobile'))
        });
        //$('select[name="eleccontracttplid"]').parent().append('<button type="button" class="btn preeleccontracttpl" data-name="eleccontracttplid" style="display:inline-block;vertical-align:top">预览</button>')
    },
    initInstance:function(){
        var thisInstance=this;
        var record=$('input[name="record"]').val();
        var signaturetypevale=$('select[name="signaturetype"]').val();
        if(signaturetypevale=='eleccontract') {
           setTimeout('$(".ke-upload-file").attr("accept",".docx");$(".upload div").first().css({"width":"120px"}).html(\'<div style="margin-top: -2px;">支持docx格式</div><div style="margin-top: -5px;">文件大小不超过2M</div>\')',1000);
        }
        if(record==0){
            return false;
        }
        thisInstance.setElecInfo(2);
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
                    url : 'index.php?module='+module+'&action=FileUpload&record='+$('input[name="record"]').val()+'&signaturetype='+$('select[name="signaturetype"]').val(),
                    afterUpload : function(data) {
                        if (data.success ==true) {
                            $('.filedelete').remove();
                            var str='<span class="label file'+data.result['id']+'" style="margin-left:5px;">'+data.result['name']+'&nbsp;<b class="deletefile" data-class="file'+data.result['id']+'" data-id="'+data.result['id']+'" title="删除文件" style="display:inline-block;width:12px;height:12px;line-height:12px;text-align:center">x</b>&nbsp;</span><input class="ke-input-text file'+data.result['id']+'" type="hidden" name="file['+data.result['id']+']" id="file" value="'+data.result['name']+'" readonly="readonly" /><input class="file'+data.result['id']+'" type="hidden" name="attachmentsid['+data.result['id']+']" value="'+data.result['id']+'">';
                            $("#fileall").append(str);
                            //K('#file').val(data.result['name']);
                        } else {
                            Vtiger_Helper_Js.showMessage({type:'error',text:data.msg});
                            e.preventDefault();
                            return false;
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
    getTPLViewByContractId:function(contractId){
        var thisInstance=this;
        var postData = {
            "module": 'ServiceContracts',
            "action": "BasicAjax",
            "contractId": contractId,
            "mode":'getElecTPLView'
        };
        AppConnector.request(postData).then(function (resd) {
            if (resd && resd.success) {
                window.parentView = resd.data;
                $('input[name="eleccontractidurl"]').val(resd.data.contracturlbase);
                thisInstance.contractSubmitPreView();
                return false;
            } else {
                Vtiger_Helper_Js.showMessage({type: 'error', text: resd.msg});
            }
        }, function () {
            return false;
        });
    },
    registerLeavePageWithoutSubmit : function(form){
        var thisInstance=this;
        InitialFormData = form.serialize();
        window.onbeforeunload = function(e){
            if (InitialFormData != form.serialize() && form.data('submit') != "true" && thisInstance.isonbeforeunload) {
                return app.vtranslate("JS_CHANGES_WILL_BE_LOST");
            }
        };
    },
    registerEvents: function (container) {
        this._super(container);

        this.registerReferenceSelectionEvent();
        this.registerResultEvent(container);
        this.signaturetypeChange();
        this.displayTPL();
        this.initInstance();
        this.eleccontracttplidChange();
        this.sealplaceChange();
    },
    sealplaceChange: function () {
        if ('无锡珍岛数字生态服务平台技术有限公司' != $('select[name="invoicecompany"]').val()) {
            // $('select[name="sealplace"]').parent().hide();
            $('select[name="sealplace"]').val('');
            $('select[name="sealplace"]').parents('.fieldValue').prev().css('visibility', 'hidden');
            $('select[name="sealplace"]').parents('.fieldValue').css('visibility', 'hidden');
        }
        $('#EditView').on('change', 'select[name="invoicecompany"]', function () {
            // alert($('select[name="invoicecompany"]').val());
            //当操作类型为“订单信息”
            if ('无锡珍岛数字生态服务平台技术有限公司' == $('select[name="invoicecompany"]').val()) {
                // alert('show');
                // $('select[name="sealplace"]').parent().show();
                $('select[name="sealplace"]').parents('.fieldValue').prev().css('visibility', 'visible');
                $('select[name="sealplace"]').parents('.fieldValue').css('visibility', 'visible');
            } else {
                // alert('hide');
                // $('select[name="sealplace"]').parent().hide();
                $('select[name="sealplace"]').val('');
                $('select[name="sealplace"]').parents('.fieldValue').prev().css('visibility', 'hidden');
                $('select[name="sealplace"]').parents('.fieldValue').css('visibility', 'hidden');
            }
        });
    },
});


