/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Inventory_Detail_Js("Invoice_Detail_Js",{},{
    getsigndalog:function(){
        $('.detailViewTitle').on('click','#Invoice_detailView_basicAction_LBL_SIGN',function(){
            var act=$(this).data('act');
            var message='确定要领取该发票吗?请签写您的<font color="red">姓名</font>';
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
                params['action'] = 'BasicAjax';
                params['module'] = 'Invoice';
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
            $('.modal-content .modal-body').append('<div id="canvssign" ondragstart="return false" oncontextmenu="return false" onselectstart="return false" oncopy="return false" oncut="return false" style="-moz-user-select:none;width:100%;height:'+windowheight+'px;border:1px solid #ccc;margin:10px 0 0;overflow:hidden;"></div>');
            $('.modal-content .modal-body').css({overflow:'hidden'});
            $('#canvssign').jSignature();
            $('<input type="button" value="清空" style="float:left;margin-left:'+(windowwith/2)+'px;">').bind('click', function(e){
                $('#canvssign').jSignature('reset')
            }).appendTo('.modal-content .modal-footer');
        });
        $('#invoicelist').on('click','.addcancelflag',function(){
            var dataid=$(this).data('id');
            var message='确定要取消标记?';
            var msg={
                'message':message
            };
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                //alert($('#recordId').val());return;
                var params={};
                params['record'] = $('#recordId').val();
                params['action'] = 'BasicAjax';
                params['module'] = 'Invoice';
                params['mode'] = 'addCancelFlag';
                params['invoiceextendid'] = dataid;

                AppConnector.request(params).then(
                    function(data) {
                        window.location.reload(true);
                    },
                    function(error,err){
                        window.location.reload(true);
                    }
                );
            },function(error, err) {});

        });
        $('#invoicelist').on('click','.addcancel',function(){
            var dataid=$(this).data('id');
            var message='<H4>确定要作废该发票吗?</H4>';
            var msg={
                'message':message
            };

            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                var params={};
                params['record'] = $('#recordId').val();
                params['action'] = 'BasicAjax';
                params['module'] = 'Invoice';
                params['mode'] = 'addCancel';
                params['invoiceextendid'] = dataid;
                AppConnector.request(params).then(
                    function(data) {
                        window.location.reload(true);
                    },
                    function(error,err){
                        window.location.reload(true);
                    }
                );
            },function(error, err) {});
        });
        var thisinstance=this;
        $('#invoicelist').on('click','.addnegative',function(){
            var dataid=$(this).data('id');
            var message='<H3>请填写红冲发票</H3>';
            var windowwith=$(window).width()*0.5;
            var windowheight=windowwith*0.25;
            var msg={
                'message':message,
                "width":windowwith
            };

            thisinstance.showConfirmationBox(msg).then(function(e){
                var params={};
                params['record'] = $('#recordId').val();
                params['action'] = 'BasicAjax';
                params['module'] = 'Invoice';
                params['mode'] = 'addRedInvoice';
                params['savedata'] =$('#negativeinvoice').serializeArray();
                AppConnector.request(params).then(
                    function(data) {
                        window.location.reload(true);
                    },
                    function(error,err){
                        window.location.reload(true);
                    }
                );
            },function(error, err) {});
            var strd='<form id="negativeinvoice" method="post">'+extendinvoice+'</form>';
            $('.modal-content .modal-body').append(strd);
            $('.modal-content .modal-body').css({overflow:'hidden'});
            $('.billingtimerextends').datetimepicker({
                language:'zh-CN',weekStart:1,todayBtn:1,autoclose:1,todayHighlight:1,startView:2,minView:2,forceParse:0,format: 'yyyy-mm-dd',pickDate: true, pickTime: true,hourStep: 1,autoclose:1
            });
            $('input[name="invoiceextendid"]').val(dataid);
            //$('input[name="negativeinvoicecodeextend"]').val($('#invoicecodeextend'+dataid).val());
            //$('input[name="negativeinvoice_noextend"]').val($('#invoice_noextend'+dataid).val());
            $('input[name="negativebusinessnamesextend"]').val($('#businessnamesextend'+dataid).val());
            //$('input[name="negativebillingtimerextend"]').val($('#billingtimerextend'+dataid).val());
            $('input[name="negativecommoditynameextend"]').val($('#commoditynameextend'+dataid).val());
            //$('input[name="negativeamountofmoneyextend"]').val($('#amountofmoneyextend'+dataid).val());
            $('select[name="negativetaxrateextend"]').val($('#taxrateextend'+dataid).val());
            //$('input[name="negativetaxextend"]').val($('#taxextend'+dataid).val());
            //$('input[ame="negativetotalandtaxextend"]').val($('#totalandtaxextend'+dataid).val());
            //$('input[name="negativeremarkextend"]').val($('#remarkextend'+dataid).val());
                jQuery(document).ready(function() {
                    $('input[name="negativeinvoicecodeextend"]').validationEngine(app.validationEngineOptions);
                });
            $('form').on('blur','input[name="negativeinvoicecodeextend"],input[name="negativeinvoice_noextend"],input[name="negativeamountofmoneyextend"],input[name="negativetaxextend"],input[ame="negativetotalandtaxextend"],input[name="negativebusinessnamesextend"]',function(){
                if($(this).val()==''){
                    $(this).attr('data-content','<font color="red">必填项不能为空</font>');
                    $(this).popover("show");
                    $(this).css({"color":"red","fontSize":"12px"});
                    $('.popover').css('z-index',1000010);
                    setTimeout('$(\'input[name="negativeinvoicecodeextend"],input[name="negativeinvoice_noextend"],input[name="negativeamountofmoneyextend"],input[name="negativetaxextend"],input[ame="negativetotalandtaxextend"],input[name="negativebusinessnamesextend"]\').popover(\'destroy\')',2000);
                }
            });
            function formatNumber(_this){
                _this.val(_this.val().replace(/,/g,''));//去掉,
                _this.val(_this.val().replace(/[^0-9.\-]/g,''));//只能输入数字小数点
                _this.val(_this.val().replace(/^0\d{1,}/g,''));//不能以0打头的整数
                _this.val(_this.val().replace(/^\./g,''));//不能以小数点打头
                _this.val(_this.val().replace(/[\d]{13,}/g,''));//超出长度全部清空
                _this.val(_this.val().replace(/\.\d{3,}$/g,''));//小数点后两位如果超过两位则将小数后的所有数清空包含小数点
                _this.val(_this.val().replace(/(\d)\-*$/g,"$1"));//不能以
                _this.val(_this.val().replace(/^\-{2,}/g,"-"));//不能以
            }
            //乘法运算解决Js相乘的问题
            function accMul(arg1,arg2){
                var m=0,s1=arg1.toString(),s2=arg2.toString();
                try{m+=s1.split(".")[1].length}catch(e){}
                try{m+=s2.split(".")[1].length}catch(e){}
                return Number(s1.replace(".",""))*Number(s2.replace(".",""))/Math.pow(10,m)
            }
            /**
             * 除法运算相除JS问题
             * @param arg1除数
             * @param arg2被除数
             * @returns {number}
             */
            function accDiv(arg1,arg2){
                var t1=0,t2=0,r1,r2;
                try{t1=arg1.toString().split(".")[1].length}catch(e){}
                try{t2=arg2.toString().split(".")[1].length}catch(e){}
                with(Math){
                    r1=Number(arg1.toString().replace(".",""))
                    r2=Number(arg2.toString().replace(".",""))
                    return (r1/r2)*pow(10,t2-t1);
                }
            }
            //加法运算,解决JS浮点数问题
            function accAdd(arg1,arg2){
                var r1,r2,m;
                try{r1=arg1.toString().split(".")[1].length}catch(e){r1=0}
                try{r2=arg2.toString().split(".")[1].length}catch(e){r2=0}
                m=Math.pow(10,Math.max(r1,r2))
                var s=(arg1*m+arg2*m)/m;
                if(isNaN(s)){
                    s=0;
                }
                return s;
            }

                $('input[name="negativetotalandtaxextend"],input[name="negativeamountofmoneyextend"],input[name="negativetaxextend"]').on("keyup",function(){
                    formatNumber($(this));
                    var arr=$(this).val().split('.');//只有一个小数点
                    if(arr.length>2){
                        if(arr[1]==''){
                            $(this).val(arr[0]);
                        }else{
                            $(this).val(arr[0]+'.'+arr[1]);
                        }
                    }
                }).on("blur",function(){  //CTR+V事件处理
                    formatNumber($(this));
                    //console.log($(this).val())
                    var arr=$(this).val().split('.');//只有一个小数点
                    if(arr.length>2){
                        if(arr[1]==''){
                            $(this).val(arr[0]);
                        }else{
                            $(this).val(arr[0]+'.'+arr[1]);
                        }
                    }else if(arr.length==2){
                        //小数点后没有数字的则将小数点删除
                        if(arr[1]==''){
                            $(this).val(arr[0]);
                        }
                    }
                });
            $('input[name="negativetotalandtaxextend"],input[name="negativeamountofmoneyextend"],input[name="negativetaxextend"]').on('keyup blur change',function(){
                if(isNaN($(this).val().replace(/,/g,''))){
                    $(this).val('');
                    return;
                }else if($(this).attr('name')=='negativetotalandtaxextend' && Number($(this).val())>Number($('#totalandtaxextend'+dataid).val().replace(/,/g,'')) ){
                    $(this).attr('data-title','注意');$(this).attr('data-content','<font color="red">红冲不能大于金额</font>');$(this).popover('show');
                    $('.popover').css('z-index',1000010);
                    return ;
                }else{
                    $(this).popover('destroy');
                }
                if($(this).val()>0){
                    if($(this).attr('name')=='negativetotalandtaxextend'){
                        if($('select[name="negativetaxrateextend"]').find('option:selected').val()!=''){
                            var taxrate=$('select[name="negativetaxrateextend"]').find('option:selected').val()=='6%'?1.06:1.17;
                            var amountofmoneyval=accDiv($('input[name="negativetotalandtaxextend"]').val(),taxrate);
                            $('input[name="negativeamountofmoneyextend"]').val(amountofmoneyval.toFixed(2))
                            var taxrate=$('select[name="negativetaxrateextend"]').find('option:selected').val()=='6%'?0.06:0.17;
                            $('input[name="negativetaxextend"]').val(accMul(amountofmoneyval,taxrate).toFixed(2));
                        }
                    }else if($(this).attr('name')=='negativeamountofmoneyextend'){
                        if($('select[name="negativetaxrateextend"]').find('option:selected').val()!=''){
                            var taxrate=$('select[name="negativetaxrateextend"]').find('option:selected').val()=='6%'?0.06:0.17;
                            //var valuetax=$('input[name="tax"]').val()*taxrate;
                            $('input[name="negativetaxextend"]').val(accMul($('input[name="negativeamountofmoneyextend"]').val(),taxrate).toFixed(2));
                        }
                        $('input[name="negativetotalandtaxextend"]').val(accAdd($('input[name="negativetaxextend"]').val(),$('input[name="negativeamountofmoneyextend"]').val()).toFixed(2));
                    }
                }
            });
            $('select[name="negativetaxrateextend"]').on('change',function(){
                if($(this).val()=='6%' || $(this).val()=='17%'){
                    if($('select[name="negativetaxrateextend"]').find('option:selected').val()!=''){
                        var taxrate=$('select[name="negativetaxrateextend"]').find('option:selected').val()=='6%'?1.06:1.17;
                        var amountofmoneyval=accDiv($('input[name="negativetotalandtaxextend"]').val(),taxrate);
                        $('input[name="negativeamountofmoneyextend"]').val(amountofmoneyval.toFixed(2))
                        var taxrate=$('select[name="negativetaxrateextend"]').find('option:selected').val()=='6%'?0.06:0.17;
                        $('input[name="negativetaxextend"]').val(accMul(amountofmoneyval,taxrate).toFixed(2));
                    }
                }
            });


        });

    },
    checkedform:function(){

        $('input[name="negativeinvoicecodeextend"]').popover('destroy');
        if(''==$('input[name="negativeinvoicecodeextend"]').val()){
            $('input[name="negativeinvoicecodeextend"]').focus();
            $('input[name="negativeinvoicecodeextend"]').attr('data-content','<font color="red">必填项不能为空</font>');
            $('input[name="negativeinvoicecodeextend"]').popover("show");
            $('.popover-content').css({"color":"red","fontSize":"12px"});
            $('.popover').css('z-index',1000010);
            setTimeout("$('input[name=\"negativeinvoicecodeextend\"]').popover('destroy')",2000);
            return false;
        }
        $('input[name="negativeinvoice_noextend"]').popover('destroy');
        if(''==$('input[name="negativeinvoice_noextend"]').val()){
            $('input[name="negativeinvoice_noextend"]').focus();
            $('input[name="negativeinvoice_noextend"]').attr('data-content','<font color="red">必填项不能为空</font>');
            $('input[name="negativeinvoice_noextend"]').popover("show");
            $('.popover').css('z-index',1000010);
            $('.popover-content').css({"color":"red","fontSize":"12px"});
            setTimeout("$('input[name=\"negativeinvoice_noextend\"]').popover('destroy')",2000);
            return false;
        }
        $('input[name="negativebusinessnamesextend"]').popover('destroy');
        if(''==$('input[name="negativebusinessnamesextend"]').val()){
            $('input[name="negativebusinessnamesextend"]').focus();
            $('input[name="negativebusinessnamesextend"]').attr('data-content','<font color="red">必填项不能为空</font>');
            $('input[name="negativebusinessnamesextend"]').popover("show");
            $('.popover').css('z-index',1000010);
            $('.popover-content').css({"color":"red","fontSize":"12px"});
            setTimeout("$('input[name=\"negativebusinessnamesextend\"]').popover('destroy')",2000);
            return false;
        }
        $('input[name="negativebillingtimerextend"]').popover('destroy');
        if(''==$('input[name="negativebillingtimerextend"]').val()){
            $('input[name="negativebillingtimerextend"]').focus();
            $('input[name="negativebillingtimerextend"]').attr('data-content','<font color="red">必填项不能为空</font>');
            $('input[name="negativebillingtimerextend"]').popover("show");
            $('.popover').css('z-index',1000010);
            $('.popover-content').css({"color":"red","fontSize":"12px"});
            setTimeout("$('input[name=\"negativebillingtimerextend\"]').popover('destroy')",2000);
            return false;
        }
        $('input[name="negativecommoditynameextend"]').popover('destroy');
        if(''==$('input[name="negativecommoditynameextend"]').val()){
            $('input[name="negativecommoditynameextend"]').focus();
            $('input[name="negativecommoditynameextend"]').attr('data-content','<font color="red">必填项不能为空</font>');
            $('input[name="negativecommoditynameextend"]').popover("show");
            $('.popover').css('z-index',1000010);
            $('.popover-content').css({"color":"red","fontSize":"12px"});
            setTimeout("$('input[name=\"negativecommoditynameextend\"]').popover('destroy')",2000);
            return false;
        }
        $('input[name="negativeamountofmoneyextend"]').popover('destroy');
        if(''==$('input[name="negativeamountofmoneyextend"]').val()){
            $('input[name="negativeamountofmoneyextend"]').focus();
            $('input[name="negativeamountofmoneyextend"]').attr('data-content','<font color="red">必填项不能为空</font>');
            $('input[name="negativeamountofmoneyextend"]').popover("show");
            $('.popover').css('z-index',1000010);
            $('.popover-content').css({"color":"red","fontSize":"12px"});
            setTimeout("$('input[name=\"negativeamountofmoneyextend\"]').popover('destroy')",2000);
            return false;
        }
        if(0==$('input[name="negativeamountofmoneyextend"]').val()){
            $('input[name="negativeamountofmoneyextend"]').focus();
            $('input[name="negativeamountofmoneyextend"]').attr('data-content','<font color="red">金额不能为零</font>');
            $('input[name="negativeamountofmoneyextend"]').popover("show");
            $('.popover').css('z-index',1000010);
            $('.popover-content').css({"color":"red","fontSize":"12px"});
            setTimeout("$('input[name=\"negativeamountofmoneyextend\"]').popover('destroy')",2000);
            return false;
        }
        $('input[name="negativetaxextend"]').popover('destroy');
        if(''==$('input[name="negativetaxextend"]').val()){
            $('input[name="negativetaxextend"]').focus();
            $('input[name="negativetaxextend"]').attr('data-content','<font color="red">必填项不能为空</font>');
            $('input[name="negativetaxextend"]').popover("show");
            $('.popover').css('z-index',1000010);
            $('.popover-content').css({"color":"red","fontSize":"12px"});
            setTimeout("$('input[name=\"negativetaxextend\"]').popover('destroy')",2000);
            return false;
        }
        if(0==$('input[name="negativetaxextend"]').val()){
            $('input[name="negativetaxextend"]').focus();
            $('input[name="negativetaxextend"]').attr('data-content','<font color="red">金额不能为零</font>');
            $('input[name="negativetaxextend"]').popover("show");
            $('.popover').css('z-index',1000010);
            $('.popover-content').css({"color":"red","fontSize":"12px"});
            setTimeout("$('input[name=\"negativetaxextend\"]').popover('destroy')",2000);
            return false;
        }
        $('input[name="negativetotalandtaxextend"]').popover('destroy');
        if(''==$('input[name="negativetotalandtaxextend"]').val()){
            $('input[name="negativetotalandtaxextend"]').focus();
            $('input[name="negativetotalandtaxextend"]').attr('data-content','<font color="red">必填项不能为空</font>');
            $('input[name="negativetotalandtaxextend"]').popover("show");
            $('.popover').css('z-index',1000010);
            $('.popover-content').css({"color":"red","fontSize":"12px"});
            setTimeout("$('input[name=\"negativetotalandtaxextend\"]').popover('destroy')",2000);
            return false;
        }
        if(0==$('input[name="negativetotalandtaxextend"]').val()){
            $('input[name="negativetotalandtaxextend"]').focus();
            $('input[name="negativetotalandtaxextend"]').attr('data-content','<font color="red">金额不能为零</font>');
            $('input[name="negativetotalandtaxextend"]').popover("show");
            $('.popover').css('z-index',1000010);
            $('.popover-content').css({"color":"red","fontSize":"12px"});
            setTimeout("$('input[name=\"negativetotalandtaxextend\"]').popover('destroy')",2000);
            return false;
        }
        if(parseFloat($('input[name="negativetotalandtaxextend"]').val()) > parseFloat($('#totalandtaxextend'+$('input[name="invoiceextendid"]').val()).val())){
            $('input[name="negativetotalandtaxextend"]').focus();
            $('input[name="negativetotalandtaxextend"]').attr('data-content','<font color="red">红冲不能大于金额</font>');;
            $('input[name="negativetotalandtaxextend"]').popover("show");
            $('.popover').css('z-index',1000010);
            $('.popover-content').css({"color":"red","fontSize":"12px"});
            setTimeout("$('input[name=\"negativetotalandtaxextend\"]').popover('destroy')",2000);
            return false;
        }
        return true;
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
     getBilling:function(){
        $('.detailViewTitle').on('click','#Invoice_detailView_basicAction_LBL_BILLING',function(){
            var act=$(this).data('act');
            var message='确定关联开票信息吗？';
            var msg={
                'message':message
            };
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                //alert($('#recordId').val());return;
                var params={};
                params['record'] = $('#recordId').val();
                params['action'] = 'BasicAjax';
                params['module'] = 'Invoice';
                params['mode'] = 'relatebilling';
                AppConnector.request(params).then(
                    function(data) {
                        window.location.reload(true);
                    },
                    function(error,err){
                        window.location.reload(true);
                    }
                );
            },function(error, err) {});
        });
    },
    addmarker:function(){
        $('.details').on("click",'#realremarkbutton',function(){
            var remark=$('#remarkvalue');
            if(remark.val()==''){
                remark.focus();
                return false;
            }
            var name=$('#stagerecordname').val();
            var msg={'message':"是否要给发票<"+name+">添加备注？",};
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



    registerEvents: function(){
        this._super();
        this.getsigndalog();
        this.getBilling();
        this.addmarker();
        //this.addMinus();
    }
});