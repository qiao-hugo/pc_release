/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("SupplierContracts_Detail_Js",{},{
	

	
	/**
	 * Function to get listprice edit form
	 */
	getconfirmrecord : function(requestUrl){
        var thisInstance=this;
        $('#ServiceContracts_detailView_basicAction_LBL_CONFIRM').on('click', function(){
            var message='请先确定是否已经审查了该合同？';
            var msg={
                'message':message
            };

            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                var params = {
                    'module' : 'SupplierContracts', //ServiceContracts
                    'action' : 'ChangeAjax',
                    'mode':'serviceconfirm',
                    "recordid":$('#recordId').val()
                };
                AppConnector.request(params).then(
                    function(data){
                        window.location.reload();
                    },
                    function(){
                    }
                );

            },function(error, err) {});
        });
        $('#SupplierContracts_detailView_basicAction_LBL_NOSTDAPPLY').on('click', function(){
            var message='您确定要提交该采购合同吗？';
            var msg={
                'message':message
            };

            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                var params = {
                    'module' : 'SupplierContracts', //ServiceContracts
                    'action' : 'ChangeAjax',
                    'mode':'makeWorkflowStages',
                    "recordid":$('#recordId').val()
                };
                AppConnector.request(params).then(
                    function(data){
                        if(data.result.falg){
                            window.location.reload();
                        }else{
                            Vtiger_Helper_Js.showMessage({type:'error',text:data.result.msg});
                        }

                    },
                    function(){
                    }
                );

            },function(error, err) {});
        });
        $('.detailViewTitle').on('click','#SupplierContracts_detailView_basicAction_LBL_SIGN',function(){
            var act=$(this).data('act');
            var message='请签写您的<font color="red">姓名</font>';
            var windowwith=$(window).width();
            var windowheight=windowwith*0.25;
            var msg={
                'message':message,
                "width":windowwith
            };

            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                //alert($('#recordId').val());return;
                var params={};
                params['record'] = $('#recordId').val();
                params['action'] = 'ChangeAjax';
                params['module'] = 'SupplierContracts';
                params['mode'] = 'savesignimage';
                params['image'] = $('#canvssign').jSignature("getData", "default").toString();
                AppConnector.request(params).then(
                    function(data) {
                        window.location.reload(true);
                    },
                    function(error,err){
                        window.location.reload(true);
                    }
                );
            },function(error, err) {});
            $('.modal-content .modal-body').append('<div id="canvssign" ondragstart="return false" oncontextmenu="return false" onselectstart="return false" oncopy="return false" oncut="return false" style="-moz-user-select:none;width:100%;height:'+windowheight+'px; min-height:none; border:1px solid #ccc;margin:10px 0 0;overflow:hidden;"></div>');
            $('.modal-content .modal-body').css({overflow:'hidden'});
            $('#canvssign').jSignature();
            $('<input type="button" value="清空" style="float:left;margin-left:'+(windowwith/2)+'px;">').bind('click', function(e){
                $('#canvssign').jSignature('reset')
            }).appendTo('.modal-content .modal-footer');
        });
        /**虚拟合同编号生成**/
        $('.detailViewTitle').on('click','#SupplierContracts_detailView_basicAction_LBL_VIRTUALVUMBER',function(){
            var msg={
                'message':'<h4>确定要生成虚拟合同编号?</h4><hr>'
            };
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                //alert($('#recordId').val());return;
                var params={};
                params['record'] = $('#recordId').val();
                params['action'] = 'BasicAjax';
                params['module'] = 'SupplierContracts';
                params['mode'] = 'createVirtualNumber';
                AppConnector.request(params).then(
                    function(data) {
                        if(data.success){
                            if(data.result.flag){
                                window.location.reload(true);
                            }else{
                                Vtiger_Helper_Js.showMessage({type:'error',text:data.result.msg});
                            }
                        }else{
                            window.location.reload(true);
                        }

                    },
                    function(error,err){
                        window.location.reload(true);
                    }
                );
            },function(error, err) {});
            $('.modal-content .modal-body').append('<div style="font-size: 20px;">虚拟合同只能走报销单流程,其他流程无效!</div>');


        });
        /**作废申请**/
        $('#SupplierContracts_detailView_basicAction_LBL_CONTRACTCANCEL').on('click', function(){
            //验证是否有1、未作废的充值申请单  2、非作废状态的发票 3、返点款类型的回款
            var check_params={
                'module' : 'SupplierContracts',
                'action' : 'ChangeAjax',
                'mode':'CheckContractCancel',
                "recordid":$('#recordId').val()
            };
            AppConnector.request(check_params).then(
                function(data){
                    if(data.success==true){
                        if(data.result.success==false){
                            Vtiger_Helper_Js.showMessage({type:'error',text:data.result.message});
                        }else{
                            var message='<h3>请填写如下信息!</h3><hr>';
                            var msg={
                                'message':message,
                                'width':'800px'
                            };
                            var strdata=thisInstance.getSuppContractsAgreement();

                            thisInstance.showConfirmationBox(msg).then(function(e){
                                var pagenumber=$('#pagenumber').val();
                                var remark=$('#remark').val();
                                var reasoncan=$('#reasoncancellation').val();
                                var params = {
                                    'module' : 'SupplierContracts',
                                    'action' : 'ChangeAjax',
                                    'mode':'ContractCancel',
                                    'reasoncan':reasoncan,
                                    'pagenumber':pagenumber,
                                    'remark':remark,
                                    "recordid":$('#recordId').val()
                                };
                                AppConnector.request(params).then(
                                    function(data){
                                        window.location.reload();
                                    },
                                    function(){
                                        window.location.reload();
                                    }
                                );


                            },function(error, err) {});
                            var str='';

                            var strr='<form name="insertcomment" id="formcomment">\
                            <div id="insertcomment" style="height: 300px;overflow: auto">\
                            <table class="table table-bordered blockContainer Duplicates showInlineTable  detailview-table" data-num="1" id="comments1"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> ' +
                                '<span class="redColor">*</span> 作废页数(一式三联算一页)</label></td>' +
                                '<td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="number" id="pagenumber" name="pagenumber" min="1" value="1"></span></div></td>' +
                                '<td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 应缴款金额:</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="text" id="je" name="je" value="300" disabled></span></div></td></tr>' +
                                '<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>作废原因:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><select name="reasoncancellation" id="reasoncancellation"><option value="losevoid">遗失作废</option><option value="normallyvoid">正常作废</option><option value="othervoid">其他</option></select></span></div></td></tr>' +
                                '<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>作废原因描述:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><textarea name="remark" id="remark" class="span11"></textarea></span></div></td></tr>' +
                                '<tr><td colspan="4"><label class="muted pull-right marginRight10px"><span class="redColor">主合同申请作废请连同附加协议一并作废。作废页数请和主合同及附加协议一并作废，否则拒绝作废。</span></label></td></tr>' +
                                '</tbody></table>'+strdata+
                                '</div></form>';

                            $('.modal-content .modal-body').append(strr);
                        }
                    }
                },
                function(){
                }
            );
        });
        $('body').on('change','#pagenumber,#reasoncancellation',function(){
            var reasoncan=$('#reasoncancellation').val();
            var pagenumber=$('#pagenumber').val();
            if(reasoncan=='losevoid')
            {
                $('#je').val(300);
            }
            else if(reasoncan=='normallyvoid')
            {
                $('#je').val(pagenumber);
            }
            else
            {
                $('#je').val(0);
            }

        });
        $('#SupplierContracts_detailView_basicAction_LBL_CONTRACTCANCELING').on('click', function(){

            var params={};
            params.data = {
                "module": "SupplierContracts",
                "action": "ChangeAjax",
                "mode": "getCancelInfo",
                "recordid":$('#recordId').val()
            };
            params.async=false;
            var strdata=thisInstance.getSuppContractsAgreement();
            var strr=''
            AppConnector.request(params).then(
                function(data){
                    if(data.success){
                        var losevoid=data.result.cancelvoid!='normallyvoid'?' selected':'';
                        var normallyvoid=data.result.cancelvoid=='normallyvoid'?' selected':'';
                        strr='<form name="insertcomment" id="formcomment">\
                            <div id="insertcomment" style="height: 300px;overflow: auto">\
                            <table class="table table-bordered blockContainer Duplicates showInlineTable  detailview-table" data-num="1" id="comments1"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> ' +
                            '<span class="redColor">*</span> 作废页数(一式三联算一页)</label></td>' +
                            '<td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="number" id="pagenumber" name="pagenumber" min="1" value="'+data.result.pagenumber+'"></span></div></td>' +
                            '<td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 应缴款金额:</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="text" id="je" name="je" value="'+data.result.cancelmoney+'" disabled></span></div></td></tr>' +
                            '<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>作废原因:</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><select name="reasoncancellation" id="reasoncancellation"><option value="losevoid"'+losevoid+'>遗失作废</option><option value="normallyvoid"'+normallyvoid+'>正常作废</option></select></span></div></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">申请人:</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10">'+data.result.cancelname+'</span></div></td></tr>' +
                            '<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>作废原因描述:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><textarea name="remark" id="remark" class="span11">'+data.result.cancelremark+'</textarea></span></div></td></tr>' +
                            '<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>已收款金额:</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="number" id="souje" name="souje" value="'+data.result.accountsdue+'" min="1"></span></div></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>收据编号:</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="text" id="soujbanhao" name="soujbanhao" value="'+data.result.receiptnumber+'"></span></div></td></tr>' +
                            '<tr><td colspan="4"><label class="muted pull-right marginRight10px"><span class="redColor">主合同申请作废请连同附加协议一并作废。作废页数请和主合同及附加协议一并作废，否则拒绝作废。</span></label></td></tr>' +
                            '<tr><td colspan="4"><label class="muted pull-right marginRight10px"><span class="redColor">请出纳确认该作废合同一式三联，所有联次齐全，并确认作废页数无误。</span></label></td></tr>' +
                            '</tbody></table>'+strdata+'</div></form>';
                    }
                },
                function(){
                }
            );
            var message='<h3>请填写如下信息!</h3><hr>';
            var msg={
                'message':message,
                'width':'900px'
            };

            thisInstance.showConfirmationBox(msg).then(function(e){
                var pagenumber=$('#pagenumber').val();
                var remark=$('#remark').val();
                var reasoncan=$('#reasoncancellation').val();
                var souje=$('#souje').val();
                var soujbanhao=$('#soujbanhao').val();
                var params = {
                    'module' : 'SupplierContracts',
                    'action' : 'ChangeAjax',
                    'mode':'chuNaDoContractCancel',
                    'reasoncan':reasoncan,
                    'souje':souje,
                    'soujbanhao':soujbanhao,
                    'reasoncan':reasoncan,
                    'pagenumber':pagenumber,
                    'remark':remark,
                    "recordid":$('#recordId').val()
                };
                AppConnector.request(params).then(
                    function(data){
                        window.location.reload();
                    },
                    function(){
                        window.location.reload();
                    }
                );


            },function(error, err) {});
            var str='';



            $('.modal-content .modal-body').append(strr);

        });
        //指定代领人
        $('#SupplierContracts_detailView_basicAction_LBL_RECEIPTOR').on('click', function(){
            var userdata=thisInstance.getUserData();
            var message='<h4>请选择代领人？</h4><hr>';
            var msg={
                'message':message,
                'width':'400px'
            };
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                var userid=$('#reportid').val();

                var params = {
                    'module' : 'SupplierContracts', //ServiceContracts
                    'action' : 'ChangeAjax',
                    'mode':'assignreceiptor',
                    'userid':userid,
                    "recordid":$('#recordId').val()
                };
                AppConnector.request(params).then(
                    function(data){
                        window.location.reload();
                    },
                    function(){
                    }
                );

            },function(error, err) {});
            $('.modal-content .modal-body').append(userdata);
            $(".chzn-select").chosen();

        });
        
	},
    getSuppContractsAgreement:function()
    {
        var params={};
        params.data = {
            "module": "SupplierContracts",
            "action": "ChangeAjax",
            "mode": "getSuppContractsAgreement",
            "recordid":$('#recordId').val()
        };
        params.async=false;
        var strdata=''
        AppConnector.request(params).then(
            function(data){
                if(data.num>0){
                    strdata='<fieldset>\
                        <legend>补充协议合同编号</legend>';


                    $.each(data.result,function(key,value){
                        var contractsno=value.contractsno==null?'未生成合同编号':value.contractsno;
                        strdata+='<span class="label label-a_normal">'+contractsno+"</span>&nbsp;&nbsp;";
                        var keya=key+1;
                        if(keya>1&&keya%3==0){
                            strdata+='<br><hr>';
                        }

                    });
                    strdata+='</fieldset>';
                }
            },
            function(){
            }
        );
        return strdata;
    },
    getUserData:function(){
        var params={};
        params.data = {
            'module' : 'ServiceContracts',
            'action' : 'ChangeAjax',
            'mode':'getuserlist'
        };
        params.async=false;
        var strs='';
        var currentuser=$('#current_user_id').val();
        AppConnector.request(params).then(
            function(data){
                if(data.success){
                    var str='';
                    var userselect='';
                    $.each(data.result,function(key,value){
                        userselect=value.id==currentuser?'selected':'';
                        str+='<option value="'+value.id+'" '+userselect+'>'+value.username+'</option>';
                    })
                    strs='<div style="margin:10px; 20px;text-align:center;height:250px;"><select id="reportid" class="chzn-select">'+str+'</select></div>';

                }
            },
            function(){
            }
        );
        return strs;
    },
    /**
     * 添加付款明细
     * @constructor
     */
    AddRStatement:function(){
        var thisInstance=this;
        $('#ADDRSTATEMENT').on('click',function(){

            var message = '<h4>添加付款记录!</h4><hr>';
            var msg = {
                'message': message,
                'width': '800px',
                "action":function(){
                    var flownumberofpaymentform = $('#flownumberofpaymentform').val();
                    if(flownumberofpaymentform=='') {
                        Vtiger_Helper_Js.showPnotify(app.vtranslate('付款单流水号'));
                        return false;
                    }
                    var paymentamount = $('#paymentamount').val();
                    paymentamount=paymentamount>0?paymentamount:0;
                    if(paymentamount==0) {
                        Vtiger_Helper_Js.showPnotify(app.vtranslate('付款金额大于0'));
                        return false;
                    }
                    var paymentdate = $('#paymentdate').val();
                    if(paymentdate=='') {
                        Vtiger_Helper_Js.showPnotify(app.vtranslate('付款日期不能为空'));
                        return false;
                    }
                    return true;
                }
            };

            Vtiger_Helper_Js.showConfirmationBox(msg).then(function (e) {
                var flownumberofpaymentform = $('#flownumberofpaymentform').val();
                var paymentamount = $('#paymentamount').val();
                var paymentdate = $('#paymentdate').val();
                var params = {
                    'module': 'SupplierContracts',
                    'action': 'BasicAjax',
                    'mode': 'AddRStatement',
                    'flownumberofpaymentform': flownumberofpaymentform,
                    'paymentamount': paymentamount,
                    'paymentdate': paymentdate,
                    "recordid": $('#recordId').val()
                };
                AppConnector.request(params).then(
                    function (data) {
                        if(data.result.flag){
                            window.location.reload(true);
                        }else{
                            Vtiger_Helper_Js.showMessage({type:'error',text:data.result.msg});
                        }
                    },
                    function () {
                        window.location.reload();
                    }
                );


            }, function (error, err) {});
            var str = '';

            var strr = '<form name="insertcomment" id="formcomment">\
                            <div id="insertcomment" style="height: 300px;overflow: auto">\
                            <table class="table table-bordered blockContainer Duplicates showInlineTable  detailview-table" data-num="1" id="comments1"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> ' +
                '<span class="redColor">*</span>付款单流水号</label></td>' +
                '<td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="text" id="flownumberofpaymentform" name="flownumberofpaymentform" ></span></div></td></tr>' +
                '<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span> 付款金额:</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="text" id="paymentamount" name="paymentamount" value=""></span></div></td></tr>' +
                '<td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>付款日期:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><input type="text" class="form_datetime" name="paymentdate" id="paymentdate" readonly="readonly"></span></div></td></tr>' +
                '</tbody></table>' +
                '</div></form>';

            $('.modal-content .modal-body').append(strr);
            $('#paymentdate').datepicker({
                format: "yyyy-mm-dd",
                language:  'zh-CN',
                autoclose: true,
                todayBtn: true,
                orientation: "top left"
            });
        });
    },
    showConfirmationBox : function(data){
        var thisstance=this;
        var aDeferred = jQuery.Deferred();
        var width='800px';
        if(typeof  data['width'] != "undefined"){
            width=data['width'];
        }
        var bootBoxModal = bootbox.confirm({message:data['message'],width:width, callback:function(result) {
            if(result){
                if(thisstance.checkedform()){
                    aDeferred.resolve();
                }else{
                    return false;
                }
            } else{
                aDeferred.reject();
            }
        }, buttons: { cancel: {
            label: '取消',
            className: 'btn'
        },
            confirm: {
                label: '确认',
                className: 'btn-success'
            }
        }});
        bootBoxModal.on('hidden',function(e){
            if(jQuery('#globalmodal').length > 0) {
                jQuery('body').addClass('modal-open');
            }
        })
        return aDeferred.promise();
    },
    checkedform:function(){
        var pagenumber=$('#pagenumber').val();
        var remark=$('#remark').val();
        if(pagenumber<1){
            $('#pagenumber').focus();
            $('#pagenumber').attr('data-content','<font color="red">页数要大于0!</font>');;
            $('#pagenumber').popover("show");
            $('.popover').css('z-index',1000010);
            $('.popover-content').css({"color":"red","fontSize":"12px"});
            setTimeout("$('#remark').popover('destroy')",2000);
            return false;
        }
        if(remark==''){

            $('#remark').focus();
            $('#remark').attr('data-content','<font color="red">必填项不能为空!</font>');;
            $('#remark').popover("show");
            $('.popover').css('z-index',1000010);
            $('.popover-content').css({"color":"red","fontSize":"12px"});
            setTimeout("$('#remark').popover('destroy')",2000);
            return false;//跳出each
        }
        var souje=$('#souje').val();
        var soujbanhao=$('#soujbanhao').val();
        if(souje==''){

            $('#souje').focus();
            $('#souje').attr('data-content','<font color="red">必填项不能为空!</font>');;
            $('#souje').popover("show");
            $('.popover').css('z-index',1000010);
            $('.popover-content').css({"color":"red","fontSize":"12px"});
            setTimeout("$('#souje').popover('destroy')",2000);
            return false;//跳出each
        }
        if(soujbanhao==''){
            $('#soujbanhao').focus();
            $('#soujbanhao').attr('data-content','<font color="red">必填项不能为空!</font>');;
            $('#soujbanhao').popover("show");
            $('.popover').css('z-index',1000010);
            $('.popover-content').css({"color":"red","fontSize":"12px"});
            setTimeout("$('#soujbanhao').popover('destroy')",2000);
            return false;//跳出each
        }
        return true;
    },
    /**
     * Function to handle the ajax edit for detailview and summary view fields
     * which will expects the currentTdElement
     */
    ajaxEditHandling : function(currentTdElement) {
        $('.fileUploadContainer').find('form').css({width:"48px"});
        $('.fileUploadContainer').find('form').find('.btn-info').css({width:"48px",marginLeft:"-12px"});
        var thisInstance = this;
        var detailViewValue = jQuery('.value',currentTdElement);
        var editElement = jQuery('.edit',currentTdElement);
        var actionElement = jQuery('.summaryViewEdit', currentTdElement);
        if(editElement.length <= 0) {
            return;
        }

        if(editElement.is(':visible')){
            return;
        }

        detailViewValue.addClass('hide');
        editElement.removeClass('hide').show().children().filter('input[type!="hidden"]input[type!="image"],select').filter(':first').focus();
        var saveTriggred = false;
        var preventDefault = false;
        var saveHandler = function(e) {
            var element = jQuery(e.target);
            if((element.closest('td').is(currentTdElement))){
                return;
            }
            if(element[0]['className']=='next' || element[0]['className']=='prev' || element[0]['className']=='datepicker-switch' || element[0]['className']=='month active' || element[0]['className']=='month'){
                console.log(element);
                return false;
            }
            currentTdElement.removeAttr('tabindex');
            var fieldnameElement = jQuery('.fieldname', editElement);
            var previousValue = fieldnameElement.data('prevValue');
            var fieldName = fieldnameElement.val();
            var fieldElement = jQuery('[name="'+ fieldName +'"]', editElement);
            var formElement = thisInstance.getForm();
            var formData = formElement.serializeFormData();
            var ajaxEditNewValue = formData[fieldName];
            //value that need to send to the server
            var fieldValue = ajaxEditNewValue;
            var fieldInfo = Vtiger_Field_Js.getInstance(fieldElement.data('fieldinfo'));

            // Since checkbox will be sending only on and off and not 1 or 0 as currrent value
            if(fieldElement.is('input:checkbox')) {
                if(fieldElement.is(':checked')) {
                    ajaxEditNewValue = '1';
                } else {
                    ajaxEditNewValue = '0';
                }
                fieldElement = fieldElement.filter('[type="checkbox"]');
            }
            var errorExists = fieldElement.validationEngine('validate');
            //If validation fails

            if(errorExists&& fieldName!='file') {
                return;
            }




            //Before saving ajax edit values we need to check if the value is changed then only we have to save
            if(previousValue == ajaxEditNewValue) {
                editElement.addClass('hide');
                detailViewValue.removeClass('hide');
                actionElement.show();
                jQuery(document).off('click', '*', saveHandler);
            } else {
                var preFieldSaveEvent = jQuery.Event(thisInstance.fieldPreSave);
                fieldElement.trigger(preFieldSaveEvent, {'fieldValue' : fieldValue,  'recordId' : thisInstance.getRecordId()});
                if(preFieldSaveEvent.isDefaultPrevented()) {
                    //Stop the save
                    saveTriggred = false;
                    preventDefault = true;
                    return
                }
                preventDefault = false;

                jQuery(document).off('click', '*', saveHandler);

                if(!saveTriggred && !preventDefault) {
                    saveTriggred = true;
                }else{
                    return;
                }

                currentTdElement.progressIndicator();
                editElement.addClass('hide');
                var fieldNameValueMap = {};
                if(fieldInfo.getType() == 'multipicklist') {
                    var multiPicklistFieldName = fieldName.split('[]');
                    fieldName = multiPicklistFieldName[0];
                }
                fieldNameValueMap['value'] = fieldValue;
                fieldNameValueMap['field'] = fieldName;
                if(fieldName=='file'){
                    var newvalu={};
                    var newattachmentsid=new Array();
                    $('input[name^="file["]').each(function(i,val){
                        newvalu[i]=$(val).val();
                        newattachmentsid[i]=$(val).data('id');
                    });
                    fieldNameValueMap['value']=newvalu;
                    fieldNameValueMap['attachmentsid']=newattachmentsid;
                }
                //return;
                //console.log(fieldNameValueMap['field']);
                //console.log(fieldNameValueMap['value']);
                thisInstance.saveFieldValues(fieldNameValueMap).then(function(response) {
                        var postSaveRecordDetails = response.result;
                        currentTdElement.progressIndicator({'mode':'hide'});
                        detailViewValue.removeClass('hide');
                        actionElement.show();
                        detailViewValue.html(postSaveRecordDetails[fieldName].display_value);
                        fieldElement.trigger(thisInstance.fieldUpdatedEvent,{'old':previousValue,'new':fieldValue});
                        fieldnameElement.data('prevValue', ajaxEditNewValue);
                    },
                    function(error){
                        //TODO : Handle error
                        currentTdElement.progressIndicator({'mode':'hide'});
                    }
                )
            }
        }

        jQuery(document).on('click','*', saveHandler);
    },
	/**
	 * Function to register events
	 */
    files_deliver: function() {
        $('.details').on("click",'#realremarkbutton',function(){
            var remark=$('#remarkvalue');
            if(remark.val()==''){
                remark.focus();
                return false;
            }
            var name=$('#stagerecordname').val();
            var msg={'message':"是否要给工单阶段<"+name+">添加备注？",};
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                var params={};
                params['record'] = $('#recordid').val();//工单id
                params['isrejectid'] = $('#backstagerecordeid').val();
                params['isbackname'] = $('#backstagerecordname').val();
                params['reject']=$('#remarkvalue').val();
                params['action'] = 'SaveAjax';
                params['module'] = 'SalesorderWorkflowStages';
                params['mode'] = 'submitremark';
                params['src_module'] = app.getModuleName();
                var d={};
                d.data=params;
                AppConnector.request(d).then(
                    function(data){
                        if(data.success==true){
                            var widgetContainer = $(".widgetContainer_workflows");
                            var urlParams = widgetContainer.attr('data-url');
                            params = {
                                'type' : 'GET',
                                'dataType': 'html',
                                'data' : urlParams
                            };
                            widgetContainer.progressIndicator({});
                            AppConnector.request(params).then(
                                function(data){
                                    widgetContainer.progressIndicator({'mode': 'hide'});
                                    widgetContainer.html(data);
                                    Vtiger_Helper_Js.showMessage({type:'success',text:'备注添加成功'});
                                },
                                function(){}
                            );
                        }else{
                            Vtiger_Helper_Js.showMessage({type:'error',text:'备注添加失败,原因'+data.error.message});
                        }
                    },function(){}
                );
            });
        });
    },


    registerEvents : function(){
		this._super();
        Vtiger_Helper_Js.showConfirmationBox =function(data){
            var aDeferred = jQuery.Deferred();
            var width='800px';
            var checkFlag=true
            if(typeof  data['width'] != "undefined"){
                width=data['width'];
            }
            var bootBoxModal = bootbox.confirm({message:data['message'],width:width, callback:function(result) {
                    if(result){
                        if(typeof  data['action'] != "undefined"){
                            checkFlag=(data['action'])();
                        }
                        if(checkFlag){
                            aDeferred.resolve();
                        }else{
                            return false;
                        }
                    } else{
                        aDeferred.reject();
                    }
                }, buttons: { cancel: {
                        label: '取消',
                        className: 'btn'
                    },
                    confirm: {
                        label: '确认',
                        className: 'btn-success'
                    }
                }});
            bootBoxModal.on('hidden',function(e){
                if(jQuery('#globalmodal').length > 0) {
                    jQuery('body').addClass('modal-open');
                }
            })
            return aDeferred.promise();
        }
		this.getconfirmrecord();
        this.AddRStatement();
        this.files_deliver();
    }
})