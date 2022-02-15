/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Inventory_Detail_Js("Newinvoice_Detail_Js",{},{
    newinvoiceraymentdata : null,
    getsigndalog:function(){
        var instanceThis=this;
        $('.detailViewTitle').on('click','#Newinvoice_detailView_basicAction_LBL_SIGN',function(){
            var act=$(this).data('act');
            var message='<h3>确定要领取该发票吗?请签写您的<font color="red">姓名</font><input type="text" id="inputusercode" style="margin-left:40px;" placeholder="请输入工号"/><span id="displayname" style="margin-left: 10px;"></span><input id="usercode" type="hidden"/><input id="username" type="hidden"/></h3> ';
            var windowwith=$(window).width();
            var windowheight=windowwith*0.25;
            var msg={
                'message':message,
                "width":windowwith
            };

            instanceThis.showConfirmationBox(msg).then(function(e){
                //alert($('#recordId').val());return;
                var params={};
                params['record'] = $('#recordId').val();
                params['action'] = 'BasicAjax';
                params['module'] = 'Newinvoice';
                params['mode'] = 'savesignimage';
                params['id'] = $("#usercode").val();
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
        $('.detailViewTitle').on('click','#Newinvoice_detailView_basicAction_LBL_NFILLCANCEL',function(){
            var dataid=$(this).data('id');
            var message='<H4>确定要作废该发票吗?</H4>';
            var msg={
                'message':message
            };

            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                var params={};
                params['record'] = $('#recordId').val();
                params['action'] = 'BasicAjax';
                params['module'] = 'Newinvoice';
                params['mode'] = 'nFillInCancel';
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

            var recordid = $('#recordid').val();
            var id = $(this).attr('data-id');
            var me = this;
            // 如果发票是 预开票 判断 回款匹配金额 和 发票金额 是否一致
            var module = app.getModuleName();
            var postData = {"module": module,"action": "BasicAjax","invoiceid": recordid,'mode': 'is_show_tovoid'};
            var progressIndicatorElement = jQuery.progressIndicator({'message' : '正在提交...','position' : 'html','blockInfo' : {'enabled' : true}});
            AppConnector.request(postData).then(
                function(data){
                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                    if (data.result.flag) {
                        var msg = {'message': '预开票作废或红冲前必须清空回款信息'};
                        Vtiger_Helper_Js.showConfirmationBox(msg).then(
                            function(e) {
                                var module = app.getModuleName();
                                var postData = {
                                    "module": module,
                                    "action": "BasicAjax",
                                    "invoiceid": recordid,
                                    'mode': 'emptyInvoiceRey'
                                };
                                var progressIndicatorElement = jQuery.progressIndicator({
                                        'message' : '正在提交...',
                                        'position' : 'html',
                                        'blockInfo' : {'enabled' : true}
                                        });
                                AppConnector.request(postData).then(
                                    function(data){
                                        location.reload();
                                    },
                                    function(error,err){

                                    }
                                );
                            },function(error, err){}
                        );
                    } else {
                        t_addnegative();
                    }
                },
                function(error,err){

                }
            );

            function t_addnegative() {
                var dataid=$(me).data('id');
                var message='<H3>请填写红冲发票</H3>';
                //var windowwith=$(window).width()*0.5;
                //var windowheight=windowwith*1;
                var msg={
                    'message':message,
                    "width":1000,
                    "height":3000
                    //"height":windowheight
                };

                // 获取当前的发票的一些信息
                var $_table = $(me).closest('.table');
                //var totalandtaxextend = $_table.find('.invoice_totalandtaxextend').val();  //
                var totalandtaxextend = $_table.find('.invoice_surplusnewnegativeinvoice').val(); 
                var invoiceextendid = $(me).data('id');

                
                //t_makeRedReceivedpayments = t_makeRedReceivedpayments.replace(/reg_invoiceextendid/, invoiceextendid);
                var aaa = $('input[name=__vtrftk]').eq(0).val();
                var input_vtrftk = '<input type="hidden" name="__vtrftk" value="'+aaa+'"/>';
                //console.log(thisinstance.showConfirmationBox(msg).then);
                thisinstance.showConfirmationBox(msg).then(function(e){
                    $('#negativeinvoice').submit();
                },function(error, err) {});

                var t_extendinvoice = extendinvoice.replace(/reg_totalandtaxextend/g, totalandtaxextend);
                t_extendinvoice = t_extendinvoice.replace(/reg_invoiceextendid/g, invoiceextendid);
                t_extendinvoice = t_extendinvoice.replace(/reg_record/g, $('#recordid').val());
                
                var strd='<div style="overflow :yes;"><form id="negativeinvoice" action="index.php" method="post">'+input_vtrftk+t_extendinvoice+makeRedReceivedpayments+'</form></div>';
                $('.modal-content .modal-body').append(strd);
                $('.modal-content .modal-body').css({overflow:'hidden'});
                $('.billingtimerextends').datetimepicker({
                    defaultDate : new Date(),
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


                //$('input[name="negativetotalandtaxextend"]').val($('#totalandtaxextend'+dataid).val());
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
                    }else if($(this).attr('name')=='negativetotalandtaxextend'){
                        // && Number($(this).val())>Number($('#totalandtaxextend'+dataid).val().replace(/,/g,'')) 
                        if( Number($(this).val())>Number($('#totalandtaxextend'+dataid).val().replace(/,/g,'')) ) {
                            $(this).attr('data-title','注意');$(this).attr('data-content','<font color="red">红冲不能大于金额</font>');
                            $(this).popover('show');
                            $('.popover').css('z-index',1000010);
                            return ;
                        } else {
                            $(this).popover('destroy');
                        }
                        
                    }else{
                        $(this).popover('destroy');
                    }

                    if($(this).val()>0){
                        if($(this).attr('name')=='negativetotalandtaxextend'){
                            if($('select[name="negativetaxrateextend"]').find('option:selected').val()!=''){
                                var taxrate=$('select[name="negativetaxrateextend"]').find('option:selected').val()=='6%'?1.06:1.17;
                                console.log($('input[name="negativetotalandtaxextend"]').size());
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

            }
        });

    },
    accAdd:function(arg1,arg2){
        var r1,r2,m;
        try{r1=arg1.toString().split(".")[1].length}catch(e){r1=0}
        try{r2=arg2.toString().split(".")[1].length}catch(e){r2=0}
        m=Math.pow(10,Math.max(r1,r2))
        var s=(arg1*m+arg2*m)/m;
        if(isNaN(s)){
            s=0;
        }
        return s;
    },
    //浮点数加法运算
    FloatAdd:function(arg1,arg2){
        var r1,r2,m;
        try{r1=arg1.toString().split(".")[1].length}catch(e){r1=0}
        try{r2=arg2.toString().split(".")[1].length}catch(e){r2=0}
        m=Math.pow(10,Math.max(r1,r2));
        return (arg1*m+arg2*m)/m;
    },

    //浮点数减法运算
    FloatSub:function(arg1,arg2){
        var r1,r2,m,n;
        try{r1=arg1.toString().split(".")[1].length}catch(e){r1=0}
        try{r2=arg2.toString().split(".")[1].length}catch(e){r2=0}
        m=Math.pow(10,Math.max(r1,r2));
        //动态控制精度长度
        n=(r1>r2)?r1:r2;
        return ((arg1*m-arg2*m)/m).toFixed(n);
    },

    //浮点数乘法运算
    FloatMul:function(arg1,arg2)
    {
        var m=0,s1=arg1.toString(),s2=arg2.toString();
        try{m+=s1.split(".")[1].length}catch(e){}
        try{m+=s2.split(".")[1].length}catch(e){}
        return Number(s1.replace(".",""))*Number(s2.replace(".",""))/Math.pow(10,m);
    },


    //浮点数除法运算
    FloatDiv:function(arg1,arg2){
        var t1=0,t2=0,r1,r2;
        try{t1=arg1.toString().split(".")[1].length}catch(e){}
        try{t2=arg2.toString().split(".")[1].length}catch(e){}
        with(Math){
            r1=Number(arg1.toString().replace(".",""));
            r2=Number(arg2.toString().replace(".",""));
            return (r1/r2)*pow(10,t2-t1);
        }
    },
    checkedform:function(){
        var thisInstance=this;
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
        if(''==$("#usercode").val()){
            alert("请输入工号");
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
            //$('input[name="negativetotalandtaxextend"]').popover('hide');
            $('input[name="negativetotalandtaxextend"]').focus();
            $('input[name="negativetotalandtaxextend"]').attr('data-content','<font color="red">红冲不能大于金额</font>');;
            $('input[name="negativetotalandtaxextend"]').popover("show");
            $('.popover').css('z-index',1000010);
            $('.popover-content').css({"color":"red","fontSize":"12px"});
            setTimeout("$('input[name=\"negativetotalandtaxextend\"]').popover('destroy')",2000);
            return false;
        } else {
            $('input[name="negativetotalandtaxextend"]').popover("destroy");
        }
        var negativeinvoice_tovoid_toal = 0;
        var flagtovoie=false;
        $('#negativeinvoice .tovoid_tovoie_total').each(function() {
            var negativeinvoicevalue= parseFloat($(this).val())>0?parseFloat($(this).val()):0;
            negativeinvoice_tovoid_toal=thisInstance.FloatAdd(negativeinvoicevalue,negativeinvoice_tovoid_toal);
            flagtovoie=true;
            //negativeinvoice_tovoid_toal += parseFloat($(this).val());
        });
        negativeinvoice_tovoid_toal=negativeinvoice_tovoid_toal*1.0;
        negativeinvoice_tovoid_toal=negativeinvoice_tovoid_toal.toFixed(2);

        /*// 原发票的 价税合计
        var invoice_totalandtaxextend = parseFloat($('#negativeinvoice').find('.t_invoice_totalandtaxextend').val());
        if (negativeinvoice_tovoid_toal > invoice_totalandtaxextend) {
            var params = {text : '作废发票中的作废金额累计必须小于原发票的价税合计',title : '提示'}
            Vtiger_Helper_Js.showPnotify(params);
            return false;
        }
        // 现发票的 价税合计
        var tt_negativetotalandtaxextend = parseFloat($('#negativeinvoice').find('.tt_negativetotalandtaxextend').val());
        if(tt_negativetotalandtaxextend > invoice_totalandtaxextend) {
            var params = {text : '红冲发票中的价税合计必须小于原发票的价税合计',title : '提示'}
            Vtiger_Helper_Js.showPnotify(params);
            return false;
        }*/

        // 原发票的 价税合计
        //var invoice_totalandtaxextend = parseFloat($('#negativeinvoice').find('.t_invoice_totalandtaxextend').val());
        var invoice_totalandtaxextend = thisInstance.FloatAdd($('#negativeinvoice').find('.t_invoice_totalandtaxextend').val(),0);
        // 现发票的 价税合计
        //var tt_negativetotalandtaxextend = parseFloat($('#negativeinvoice').find('.tt_negativetotalandtaxextend').val());
        var tt_negativetotalandtaxextend = thisInstance.FloatAdd($('#negativeinvoice').find('.tt_negativetotalandtaxextend').val(),0);
        if (tt_negativetotalandtaxextend > invoice_totalandtaxextend) {
            var params = {text : '红冲发票的价税合计不可大于原发票的剩余价税合计',title : '提示'}
            Vtiger_Helper_Js.showPnotify(params);
            return false;
        }

        //if(flagtovoie && tt_negativetotalandtaxextend != negativeinvoice_tovoid_toal) {
        if(flagtovoie && thisInstance.FloatSub(tt_negativetotalandtaxextend,negativeinvoice_tovoid_toal)!=0) {
            var params = {text : '红冲发票对应的回款作废金额合计必须等于红冲发票的价税合计',title : '提示'}
            Vtiger_Helper_Js.showPnotify(params);
            return false;
        }

        
        // 判断 发票作废 不能大于 此次开票金额
        var $trs = $('#negativeinvoice').find('.negativeinvoiceextend').find('tr');
        var ttt_flag = false;
        $trs.each(function () {
            var tovoid_tovoie_total = $(this).find('.tovoid_tovoie_total').val(); // 作废金额
            if (tovoid_tovoie_total != undefined) {
                var surpluinvoicetotal = $(this).find('.t_surpluinvoicetotal').val();   // 剩余此次可开发票金额
                var neg_invoicetotal = $(this).find('.neg_invoicetotal').val();   // 此次可开发票金额
                tovoid_tovoie_total = parseFloat(tovoid_tovoie_total);
                surpluinvoicetotal = parseFloat(surpluinvoicetotal);
                if (tovoid_tovoie_total > surpluinvoicetotal) {
                    var params = {text : '作废金额不可大于剩余此次可开发票金额', title : '提示'}
                    Vtiger_Helper_Js.showPnotify(params);
                    ttt_flag = true;
                    return false;
                }
            }
        });
        if(ttt_flag){return false;}
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
        /*bootBoxModal.on('hidden',function(e){
            if(jQuery('#globalmodal').length > 0) {
                jQuery('body').addClass('modal-open');
            }
        })
*/        return aDeferred.promise();
    },
     getBilling:function(){
        $('.detailViewTitle').on('click','#Newinvoice_detailView_basicAction_LBL_BILLING',function(){
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
                params['module'] = 'Newinvoice';
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


     //格式化输入只能转入数字或小数保留两位
    formatNumber:function(_this){
        _this.val(_this.val().replace(/,/g,''));//只能输入数字小数点
        _this.val(_this.val().replace(/[^0-9.]/g,''));//只能输入数字小数点
        _this.val(_this.val().replace(/^0\d{1,}/g,''));//不能以0打头的整数
        _this.val(_this.val().replace(/^\./g,''));//不能以小数点打头
        _this.val(_this.val().replace(/[\d]{13,}/g,''));//超出长度全部清空
        _this.val(_this.val().replace(/\.\d{3,}$/g,''));//小数点后两位如果超过两位则将小数后的所有数清空包含小数点
    },


    // 作废
    tovoid: function() {
        var me = this;
        $('.tovoid_button').click(function (e) {
            var $tovoid_form = $(this).closest('.tovoid_form');
            var negativetotalandtaxextend = $tovoid_form.find('.tovoid_negativetotalandtaxextend').val();
            var $tovoid_tovoie_total = $tovoid_form.find('.tovoid_tovoie_total');

            var tovoie_total = 0;
            if ($tovoid_tovoie_total.size() > 0) {
                $tovoid_tovoie_total.each(function () {
                    me.formatNumber($(this));
                    //tovoie_total += parseFloat($(this).val());
                    tovoie_total= me.accAdd($(this).val(),tovoie_total);
                });
            }
            negativetotalandtaxextend = parseFloat(negativetotalandtaxextend);
            if (tovoie_total != negativetotalandtaxextend) {
                var  params = {text : '作废发票中的作废金额累计必须等于原发票的价税合计', title : '错误提示'};
                Vtiger_Helper_Js.showPnotify(params);
                e.preventDefault(); 
            }

            // 判断 发票作废 中的回款 作废金额+可开发票金额<=入账金额
            var $trs = $tovoid_form.find('.table').find('tr');
            $trs.each(function () {
                var tovoid_t_total = $(this).find('.tovoid_t_total').val();
                if (tovoid_t_total != undefined) {

                    //var tovoid_invoicetotal = $(this).find('.tovoid_invoicetotal').val(); //此次开票金额
                    var tovoid_tovoie_total = $(this).find('.tovoid_tovoie_total').val(); //作废金额
                    var surpluinvoicetotal = $(this).find('.c_surpluinvoicetotal').val(); //剩余此次开票金额
                    if (parseFloat(surpluinvoicetotal) < parseFloat(tovoid_tovoie_total) ) {
                        var  params = {text : '作废金额不能大于剩余此次开票金额', title : '错误提示'};
                        Vtiger_Helper_Js.showPnotify(params);
                        e.preventDefault(); 
                    }
                }
            });

            
        });
    },
    vovoid_button_click: function() {
        $('.tovoid_show_button').click(function(e) {
            var recordid = $('#recordid').val();
            var id = $(this).attr('data-id');
            // 如果没有回款信息
            if($('#tovoid_id_' + id).size() == 0) {
                var msg = {'message': '没有回款关联，确定要作废发票？'};
                Vtiger_Helper_Js.showConfirmationBox(msg).then(
                    function(e) {
                        var module = app.getModuleName();
                        var postData = {
                            "module": module,
                            "action": "BasicAjax",
                            "invoiceextendid": id,
                            'mode': 'tovoid'
                        };
                        var progressIndicatorElement = jQuery.progressIndicator({
                                'message' : '正在提交...',
                                'position' : 'html',
                                'blockInfo' : {'enabled' : true}
                                });
                        AppConnector.request(postData).then(
                            function(data){
                                location.reload();
                            },
                            function(error,err){

                            }
                        );
                    },function(error, err){}
                );
            } else {
                // 如果发票是 预开票 判断 回款匹配金额 和 发票金额 是否一致
                var module = app.getModuleName();
                var postData = {
                    "module": module,
                    "action": "BasicAjax",
                    "invoiceid": recordid,
                    'mode': 'is_show_tovoid'
                };
                var progressIndicatorElement = jQuery.progressIndicator({
                        'message' : '正在提交...',
                        'position' : 'html',
                        'blockInfo' : {'enabled' : true}
                        });
                AppConnector.request(postData).then(
                    function(data){
                        progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                        if (data.result.flag) {
                            var msg = {'message': '预开票作废或红冲前必须清空回款信息'};
                            Vtiger_Helper_Js.showConfirmationBox(msg).then(
                                function(e) {
                                    var module = app.getModuleName();
                                    var postData = {
                                        "module": module,
                                        "action": "BasicAjax",
                                        "invoiceid": recordid,
                                        'mode': 'emptyInvoiceRey'
                                    };
                                    var progressIndicatorElement = jQuery.progressIndicator({
                                            'message' : '正在提交...',
                                            'position' : 'html',
                                            'blockInfo' : {'enabled' : true}
                                            });
                                    AppConnector.request(postData).then(
                                        function(data){
                                            location.reload();
                                        },
                                        function(error,err){

                                        }
                                    );
                                },function(error, err){}
                            );
                        } else {
                            $('#tovoid_id_' + id).toggle();
                        }
                    },
                    function(error,err){

                    }
                );
            }
            e.preventDefault(); 
        });
    },
    // 生成红冲中的回款关联
    makeRedReceivedpayments: function() {

    },

    formatNumbern:function(_this){
        _this.val(_this.val().replace(/,/g,''));//去掉,
        _this.val(_this.val().replace(/[^0-9.\-]/g,''));//只能输入数字小数点
        _this.val(_this.val().replace(/^0\d{1,}/g,''));//不能以0打头的整数
        _this.val(_this.val().replace(/^\./g,''));//不能以小数点打头
        _this.val(_this.val().replace(/[\d]{13,}/g,''));//超出长度全部清空
        _this.val(_this.val().replace(/\.\d{3,}$/g,''));//小数点后两位如果超过两位则将小数后的所有数清空包含小数点
        _this.val(_this.val().replace(/(\d)\-*$/g,"$1"));//不能以
        _this.val(_this.val().replace(/^\-{2,}/g,"-"));//不能以
    },


    // 保存回款
    savebuttonnewinvoicerayment: function() {
        var me = this;
        $(document).on('click', '.savebuttonnewinvoicerayment', function () {
            var newthis=$(this);
            var message='确定要保存回款吗？';
            var msg={
                'message':message
            };
            var dataid=$(this).data('id');
            //flagv=2;
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                var $t = newthis.closest('.newinvoicerayment_tab');

                me.formatNumbern($t.find('.invoicetotal'));
                var data = {
                    servicecontractsid : $t.find('.servicecontractsid').val(),
                    total : $t.find('.total').val(),
                    arrivaldate : $t.find('.arrivaldate').val(),
                    allowinvoicetotal : $t.find('.allowinvoicetotal').val(),
                    invoicecontent : $t.find('.invoicecontent').val(),
                    invoicetotal : $t.find('.invoicetotal').val(),
                    receivedpaymentsid : $t.find('.receivedpaymentsid').val(),
                    remarks : $t.find('.remarks').val(),
                    contract_no : $t.find('.servicecontractsid_display').val(),
                    invoicecompany : $t.find('.invoicecompany').val()
                };
                if (!data['servicecontractsid']) {
                    var  params = {text : app.vtranslate(),title : app.vtranslate('所属合同不能为空')};
                    Vtiger_Helper_Js.showPnotify(params);
                    return false;
                }
                if ( (!parseFloat(data['invoicetotal'])) || parseFloat(data['invoicetotal']) <= 0) {
                    var  params = {text : app.vtranslate(),title : app.vtranslate('发票金额不合法')};
                    Vtiger_Helper_Js.showPnotify(params);
                    return false;
                }
                if (parseFloat(data['invoicetotal']) > parseFloat(data['allowinvoicetotal'])) {
                    var  params = {text : app.vtranslate(),title : app.vtranslate('发票金额不可大于可开票金额')};
                    Vtiger_Helper_Js.showPnotify(params);
                    return false;
                }

                var invoicecompany = $.trim($("#Newinvoice_detailView_fieldValue_invoicecompany span").text());
                if (data['invoicecompany'] != invoicecompany) {
                    var  params = {text : app.vtranslate(),title : app.vtranslate('合同主体是否与开票公司不一致')};
                    Vtiger_Helper_Js.showPnotify(params);
                    return false;
                }


                data['module'] = 'Newinvoice';
                data['action'] = 'BasicAjax';
                data['mode'] = 'addNewinvoice';
                data['record'] = $('input[name=record_id]').val();
                var params = {
                    'type' : 'POST',
                    'dataType': 'json',
                    'data' : data 
                };
                AppConnector.request(params).then(
                    function(data){
                        if (data.success) {
                            if(data.result.flag) {
                                location.reload();
                            } else {
                                var  params = {text : app.vtranslate(),title : app.vtranslate(data.result.msg)};
                                Vtiger_Helper_Js.showPnotify(params);
                            }
                            
                        }
                    },
                    function(){
                    }
                );

                //me.calculation_invoicetotal_sum();
            },function(error, err) {});
        });
    },  
    deletedbuttonnewinvoicerayment:function(){
        $("#detailView").on("click",".deletedbuttonnewinvoicerayment",function(){
            var msg={
                'message':"确定要删除吗?",
                'width':'600px'
            };
            var thisButton=this;
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                $(thisButton).closest("table").remove();
            });
        });
    },
    /**
     * 删除已匹配的回款
     */
    deletedNewinvoiceRayment:function(){
        $("#detailView").on("click",".deleted_newinvoicerayment",function(){
            var msg={
                'message':"确定要解除关联吗?",
                'width':'600px'
            };
            var thisButton=this;
            var recordid=$(this).data("id");
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                // 如果发票是 预开票 判断 回款匹配金额 和 发票金额 是否一致
                var module = app.getModuleName();
                var postData = {
                    "module": module,
                    "action": "BasicAjax",
                    "record": recordid,
                    'mode': 'deletedNewinvoicePayment'
                };
                var progressIndicatorElement = jQuery.progressIndicator({
                    'message' : '正在提交...',
                    'position' : 'html',
                    'blockInfo' : {'enabled' : true}
                });
                AppConnector.request(postData).then(
                    function(data){
                        progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                        if(data.result.flag){
                            $(thisButton).closest("table").remove();
                            location.reload();
                        }else{
                            var  params = {text : app.vtranslate(),title : app.vtranslate(data.result.msg)};
                            Vtiger_Helper_Js.showPnotify(params);
                        }
                    },
                    function(error,err){

                    }
                );
            });
        });
    },
    // 回款信息选择
    select_newinvoicerayment: function() {
        var me = this;
        $(document).on('change','.t_tab_newinvoicerayment_id', function () {

            var t = {};
            $('.newinvoicerayment_tab').each(function () {
                var  newinvoicerayment_id = parseFloat($(this).find('.t_tab_newinvoicerayment_id').val());
                if (! isNaN(newinvoicerayment_id)) {
                    if (! t[newinvoicerayment_id]) {
                        t[newinvoicerayment_id] = 1;
                    } else {
                        t[newinvoicerayment_id] += 1;
                    }
                }
            });
            for (var j in t) {
                if (t[j] > 1) {
                    var  params = {text : app.vtranslate(),title : app.vtranslate('不能添加多个相同回款的关联信息')};
                    Vtiger_Helper_Js.showPnotify(params);
                    return false;
                }
            }
            var $t = $(this).closest('.newinvoicerayment_tab');
            var i = $(this).val();
            if (i) {
                $t.find('.servicecontractsid_display').val(me.newinvoiceraymentdata[i]['contract_no']);
                $t.find('.servicecontractsid').val(me.newinvoiceraymentdata[i]['servicecontractsid']);
                $t.find('.total').val(me.newinvoiceraymentdata[i]['unit_price']);
                $t.find('.arrivaldate').val(me.newinvoiceraymentdata[i]['reality_date']);
                $t.find('.allowinvoicetotal').val(me.newinvoiceraymentdata[i]['allowinvoicetotal']);
                $t.find('.invoicecontent').val(me.newinvoiceraymentdata[i]['billingcontent']);
                $t.find('.invoicetotal').val(me.newinvoiceraymentdata[i]['allowinvoicetotal']);
                $t.find('.contract_no').val(me.newinvoiceraymentdata[i]['contract_no']);
                //
                //me.calculation_invoicetotal_sum();
            }
        });


        /*$(document).on('blur', '.receivedpayments_invoicetotal', function () {
            me.calculation_invoicetotal_sum();
        });*/
    },

    // 回款关联添加
    addNewinvoicerayment2 : function() {
        var newinvoiceraymentnum = $('.newinvoicerayment_tab').length + 1;
        if (newinvoiceraymentnum > 100) {return ;}
        var nowdnum=$('.newinvoicerayment_tab').last().data('num');
        if(nowdnum){
            newinvoiceraymentnum=nowdnum+1;
        }
        var t_newinvoicerayment_html = newinvoicerayment_html.replace(/\[\]/g,'['+newinvoiceraymentnum+']');
        t_newinvoicerayment_html = t_newinvoicerayment_html.replace(/yesreplace/g, newinvoiceraymentnum);
        t_newinvoicerayment_html = t_newinvoicerayment_html.replace(/newinvoicerayment_select_html/g, this.newinvoicerayment_select_html);
        $('.newinvoicerayment_div').append(t_newinvoicerayment_html);
        $('.chzn-select').chosen();
    },



    // 回款关联添加
    addNewinvoicerayment : function () {
        var me = this;
        $('#add_newinvoicerayment').on('click', function () {
            
            var account_id = $('input[name=account_id]').val();
            if (!account_id) {
                var  params = {text : '合同方公司抬头不能为空', title : '错误提示'};
                Vtiger_Helper_Js.showPnotify(params);
                return ;
            }
            var invoicecompany = $('input[name=invoicecompany]').val();
            if (me.newinvoiceraymentdata) {
                me.addNewinvoicerayment2();
            } else {
                var urlParams = 'module=Newinvoice&action=BasicAjax';
                var params = {
                    'type' : 'GET',
                    'dataType': 'html',
                    'data' : urlParams+'&mode=getNewinvoicerayment&account_id='+account_id+'&invoicecompany='+invoicecompany //module=ServiceContracts&view=ListAjax&mode=edit&record=615
                };
                AppConnector.request(params).then(
                    function(data){
                        var info=eval("("+data+")");
                        if(info.success){
                            me.newinvoiceraymentdata=info.result;
                            var t = info.result;
                            me.newinvoicerayment_select_html = '<option value="">请选择</option>';
                            for (var i in t) {
                                me.newinvoicerayment_select_html += '<option value="'+ i +'">'+ t[i]['paytitle']  +'</option>';
                            }
                            me.addNewinvoicerayment2();
                        }
                    },
                    function(){
                    }
                );
            }
            
        } );
    },
    /**
     * 工牌扫码
     */
    scanUserCode:function(){
        $("body").on("keydown","#inputusercode",function(event){
            if(event.keyCode==13){
                var instanceThis=$(this);
                var userCode=instanceThis.val();
                console.log(userCode);
                if(userCode!=''){
                    $("#username").val('');
                    $("#usercode").val('');
                    var postData = {
                        "module": "Newinvoice",
                        "action": "BasicAjax",
                        "userCode": userCode,
                        'mode': 'getUserInfo'
                    };
                    /*var progressIndicatorElement = jQuery.progressIndicator({
                        'message' : '正在提交...',
                        'position' : 'html',
                        'blockInfo' : {'enabled' : true}
                    });*/
                    AppConnector.request(postData).then(
                        function(data){
                            //location.reload();
                            if(data.result.flag){
                                $("#username").val(data.result.data.last_name);
                                $("#usercode").val(data.result.data.id);
                                $("#displayname").text(data.result.data.last_name);
                                instanceThis.val('');
                            }
                        },
                        function(error,err){

                        }
                    );
                }
            }
        });
    },
    changeSmownerid:function(){
        $('#Newinvoice_detailView_basicAction_LBL_UPDATERECEIVED').on('click', function(){
            var params={};
            params.data = {
                'module' : 'Newinvoice', //ServiceContracts
                'action' : 'BasicAjax',
                'mode':'getuserlist'
            };
            params.async=false;
            var progressIndicatorElement = jQuery.progressIndicator({
                'message' : '正在加载...',
                'position' : 'html',
                'blockInfo' : {'enabled' : true}
            });
            var str='';
            AppConnector.request(params).then(
                function(data){
                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                    if(data.success){
                        $.each(data.result,function(key,value){
                            str+='<option value="'+value.id+'">'+value.username+'</option>';
                        })
                    }
                },
                function(){
                }
            );
            var message='<H4 style="color:#ff0000">确定要更换负责人吗？</H4><hr/>';
            var msg={
                'message':message,
                'width':'300px'
            };

            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                var userid=$('#reportid').val();

                var params = {
                    'module' : 'Newinvoice', //ServiceContracts
                    'action' : 'BasicAjax',
                    'mode':'changereceived',
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
            $('.modal-content .modal-body').css('overflow','hidden');
            $('.modal-content .modal-body').append('<div style="margin:10px; 0px;text-align:center;height:250px;"><select id="reportid" class="chzn-select">'+str+'</select></div>');
            $(".chzn-select").chosen();

        });
    },
    /**
     * 生成工作流
     */
    makeWorkflowStages:function(){
        $('#Newinvoice_detailView_basicAction_LBL_STDAPPLY').on('click', function(){
            var message='<span style="color:red;font-size: 16px;font-weight:bold;">您确定要提交该发票吗？</span>';
            var msg={
                'message':message
            };

            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                var params = {
                    'module' : 'Newinvoice', //ServiceContracts
                    'action' : 'BasicAjax',
                    'mode':'makeWorkflowStages',
                    "recordid":$('#recordId').val()
                };
                AppConnector.request(params).then(
                    function(data){
                        if(data.success){
                            if(data.result.falg){
                                window.location.reload();
                            }else{
                                var  params = {text : app.vtranslate(),title : data.result.msg};
                                Vtiger_Helper_Js.showPnotify(params);
                            }
                        }else{
                            window.location.reload();
                        }

                    },
                    function(){
                    }
                );

            },function(error, err) {});
        });
    },

    //加回款信息 gaocl add 2018/03/27
    loadRelationReceivedPayments:function (mode) {
        var me = this;
        var servicecontractsid = $("input[name='contractid']").val();
        var modulestatus = $("input[name='modulestatus']").val();
        var params = {
            'type':'GET',
            'module' : 'Newinvoice',
            'action' : 'BasicAjax',
            'mode':mode,
            "recordid":$('#recordId').val(),
            'servicecontractsid':servicecontractsid,
            'modulestatus':modulestatus
        };
        AppConnector.request(params).then(
            function(data){
                if(data.success){
                    $.each(data.result,function(i,val){
                        var newinvoiceraymentnum = $('.newinvoicerayment_tab').length + 1;
                        var newinvoiceraymentnum = 1;
                        if (newinvoiceraymentnum > 100) {return ;}
                        var nowdnum= $('.newinvoicerayment_tab').length;//$('.newinvoicerayment_tab').last().data('num');
                        if(nowdnum){
                            newinvoiceraymentnum=nowdnum+1;
                        }
                        var t_newinvoicerayment_html = "";
                        if(val['data_flag'] == '1'){
                            if(!newinvoicerayment_html2) return;
                            //有解除按钮
                            t_newinvoicerayment_html = newinvoicerayment_html2.replace(/\[\]/g,'['+newinvoiceraymentnum+']');
                            var newinvoiceraymentid = val['newinvoiceraymentid'];
                            t_newinvoicerayment_html = t_newinvoicerayment_html.replace(/yesreplace/g, newinvoiceraymentid);
                            t_newinvoicerayment_html = t_newinvoicerayment_html.replace(/newinvoicerayment_select_html/g, this.newinvoicerayment_select_html);
                            $('.newinvoicerayment_div').append(t_newinvoicerayment_html);

                            var $t = $('.newinvoicerayment_div').find('table:last');
                            $t.find('.servicecontractsid_display').text(val['contract_no']);
                            $t.find('.servicecontractsid').text(val['servicecontractsid']);
                            $t.find('.total').text(val['total']);
                            $t.find('.arrivaldate').text(val['arrivaldate']);
                            $t.find('.allowinvoicetotal').text(val['allowinvoicetotal']);
                            $t.find('.invoicecontent').text(val['invoicecontent']);
                            $t.find('.invoicetotal').text(val['invoicetotal']);
                            $t.find('.receivedpaymentsid_display').text(val['paytitle']);
                            $t.find('.invoicecompany').text(val['invoicecompany']);
                            $t.find('.tab_newinvoicerayment_id').text(val['receivedpaymentsid']);
                            $t.find('.t_tab_newinvoicerayment_id').text(val['receivedpaymentsid']);
                            $t.find('.remarks').text('');
                        }else{
                            if(!newinvoicerayment_html1) return;
                            //保存和删除
                            t_newinvoicerayment_html = newinvoicerayment_html1.replace(/\[\]/g,'['+newinvoiceraymentnum+']');
                            //var newinvoiceraymentid = val['newinvoiceraymentid'];

                            t_newinvoicerayment_html = t_newinvoicerayment_html.replace(/yesreplace/g, '');
                            t_newinvoicerayment_html = t_newinvoicerayment_html.replace(/newinvoicerayment_select_html/g, this.newinvoicerayment_select_html);
                            $('.newinvoicerayment_div').append(t_newinvoicerayment_html);

                            var $t = $('.newinvoicerayment_div').find('table:last');
                            $t.find('.servicecontractsid_display').val(val['contract_no']);
                            $t.find('.servicecontractsid').val(val['servicecontractsid']);
                            $t.find('.total').val(val['total']);
                            $t.find('.arrivaldate').val(val['arrivaldate']);
                            $t.find('.allowinvoicetotal').val(val['allowinvoicetotal']);
                            $t.find('.invoicecontent').val(val['invoicecontent']);
                            $t.find('.invoicetotal').val(val['allowinvoicetotal']);
                            $t.find('.receivedpaymentsid').val(val['receivedpaymentsid']);
                            $t.find('.receivedpaymentsid_display').val(val['paytitle']);
                            $t.find('.invoicecompany').val(val['invoicecompany']);
                        }

                    })
                    //合计基本信息栏的开票金额
                    // me.calculation_invoicetotal_sum();
                    // $(document).on('blur', '.receivedpayments_invoicetotal', function () {
                    //     me.calculation_invoicetotal_sum();
                    // });
                }
            },
            function(){
            }
        )
    },
    serviceContracts_change:function () {
        var me = this;
        $('#Newinvoice_detailView_basicAction_LBL_CHANGE_CONTRACTS').on('click', function(){
            var recordId = $('input[name="record"]').val();
            var module = app.getModuleName();
            var postData = {
                "module": module,
                "action": "BasicAjax",
                "record": recordId,
                'mode': 'getChangeServiceContracts'
            }
            AppConnector.request(postData).then(
                // 请求成功
                function(data){
                    if (data.success) {
                        var contract_no_arr = data.result.arr_contract_no;
                        me.submitServiceContractsChange(contract_no_arr);
                    }
                },
                function(error,err){

                });
        });

    },

    submitServiceContractsChange:function (contract_no_arr) {
        var msg = {
            'message': '请选择重新匹配的合同编号',
            "width":"400px",
        };
        var me = this;
        Vtiger_Helper_Js.showConfirmationBox(msg).then(
            function(e) {
                var recordId = $('input[name="record"]').val();
                var service_no = $("#repeat_contract_no").val();
                if($.trim(service_no) == '') {
                    Vtiger_Helper_Js.showPnotify(app.vtranslate('合同编号不能为空'));
                    return ;
                }
                var contractid_display = $("#Newinvoice_detailView_fieldValue_contractid a").text();
                if($.trim(service_no) == $.trim(contractid_display)){
                    Vtiger_Helper_Js.showPnotify(app.vtranslate('输入的合同编号和当前合同编号重复'));
                    return ;
                }

                var module = app.getModuleName();
                var postData = {
                    "module": module,
                    "action": "BasicAjax",
                    "record": recordId,
                    'service_no': service_no,
                    'mode': 'repeatServiceContracts'

                }

                var Message = "正在处理中,请稍等...";

                var progressIndicatorElement = jQuery.progressIndicator({
                    'message' : Message,
                    'position' : 'html',
                    'blockInfo' : {'enabled' : true}
                });
                AppConnector.request(postData).then(
                    function(data){
                        // 隐藏遮罩层
                        progressIndicatorElement.progressIndicator({
                            'mode' : 'hide'
                        });
                        if (data.success) {
                            //$t_tr.find('.allowinvoicetotal_value').html(selectValue);
                            //$(me).data('status', selectValue);
                            if(data.result.msg != ""){
                                Vtiger_Helper_Js.showPnotify(app.vtranslate(data.result.msg));
                            }else{
                                location.reload();
                                //me.loadRelationReceivedPayments("getRelationReceivedPayments");
                            }
                        }
                    },
                    function(error,err){

                    }
                );
            },function(error, err){}
        );
        if ($('tbl_serviceContracts_change').length === 0) {
            var str = '';
            if (contract_no_arr == null || contract_no_arr.length == 0) {
                str = '<option value="">无满足条件合同编号</option>';
            }else{
                for(var i=0; i<contract_no_arr.length; i++) {
                    str += '<option value="'+ contract_no_arr[i]['contract_no'] +'">'+ contract_no_arr[i]['contract_no'] +'</option>';
                }
            }
            var title = "可变更的合同满足条件：合同为已签收状态；合同客户与发票合同方公司抬头一致；合同主体与开票公司一致";
            $('.modal-content .modal-body').append('<table id="tbl_serviceContracts_change" class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">合同编号:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><select name="repeat_contract_no" title="'+title+'" id="repeat_contract_no">'+str+'</select></span></div></td></tr></tbody></table>');
        }
    },

    //发票作废红冲流程
    invoiceAbandonedOrRed:function(){
        var instanceThis=this;
        $("#Newinvoice_detailView_basicAction_LBL_BANDONED").click(function () {
            var taxType=$("#taxType").val();
            var invoiceTime=$('input[id^=billingtimeextend]').eq(0).val();
            var today=$("#today").val();
            var isTheSameMonth=instanceThis.isTheSameMonth(invoiceTime,today);
            if(taxType==='generalinvoice'){
                //普通票
                if(isTheSameMonth){
                    //开票时间与目前废弃时间在同一个月
                    instanceThis.workFlowVoid();
                }else{
                    //普通票不在同一月份红冲
                    instanceThis.workFlowRedInvoice(taxType,0);
                }
            }else if(taxType==='specialinvoice'){
                //专票
                if(isTheSameMonth){
                    //在同一个月直接作废
                    instanceThis.workFlowVoid()
                }else{
                    //不在在同一个月判断
                    var msg={
                        'message':'发票使用情况',
                        'width':'400px'
                    };
                    Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                        if($("#invoiceUsed").val()==='已抵扣'){
                            //已使用部分红冲
                            instanceThis.workFlowRedInvoice(taxType,1);
                        }else{
                            //没使用全部红冲
                            instanceThis.workFlowRedInvoice(taxType,0);
                        }
                    },function(error, err) {});
                    var select=" <div class='form-group'><label for='name' class='control-label' style='display: inline-block'>发票使用情况：<select class='form-control' id='invoiceUsed'><option value='已抵扣'>已抵扣</option><option value='未抵扣'>未抵扣</option></select></div>";
                    $('.modal-content .modal-body').append(select);
                }

            }else if(taxType==='electronicinvoice'){
                //电子票
                instanceThis.workFlowRedInvoice(taxType,0);
            }else{
                //发票类型不对
                Vtiger_Helper_Js.showMessage({type:'error',text:'发票类型有误'});
            }
        });
    },

    //保存红冲信息
    saveRedInvoiceInfo:function(){
        //过滤输入数字
        $('input[name^="negativetotalandtaxextend"],input[name^="negativeamountofmoneyextend"],input[name^="negativetaxextend"]').on("keyup",function(){
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


        /**
         *自动计算金额
         */
        $('input[name^="negativetotalandtaxextend"],input[name^="negativeamountofmoneyextend"],input[name^="negativetaxextend"]').on('keyup blur change',function(){
            var extendId=$(this).data('extend');
            if(isNaN($(this).val().replace(/,/g,''))){
                $(this).val('');
                return;
            }
            if($(this).val()>0){
                if($(this).attr('name')=='negativetotalandtaxextend'+extendId){
                    if($('select[name=negativetaxrateextend'+extendId+']').find('option:selected').val()!=''){
                        var taxrate=$('select[name=negativetaxrateextend'+extendId+']').find('option:selected').val()=='6%'?1.06:1.17;
                        var amountofmoneyval=accDiv($('input[name=negativetotalandtaxextend'+extendId+']').val(),taxrate);
                        $('input[name=negativeamountofmoneyextend'+extendId+']').val(amountofmoneyval.toFixed(2))
                        var taxrate=$('select[name=negativetaxrateextend'+extendId+']').find('option:selected').val()=='6%'?0.06:0.17;
                        $('input[name=negativetaxextend'+extendId+']').val(accMul(amountofmoneyval,taxrate).toFixed(2));
                    }
                }else if($(this).attr('name')=='negativeamountofmoneyextend'+extendId){
                    if($('select[name=negativetaxrateextend'+extendId+']').find('option:selected').val()!=''){
                        var taxrate=$('select[name=negativetaxrateextend'+extendId+']').find('option:selected').val()=='6%'?0.06:0.17;
                        $('input[name=negativetaxextend'+extendId+']').val(accMul($('input[name=negativeamountofmoneyextend'+extendId+']').val(),taxrate).toFixed(2));
                    }
                    $('input[name=negativetotalandtaxextend'+extendId+']').val(accAdd($('input[name=negativetaxextend'+extendId+']').val(),$('input[name=negativeamountofmoneyextend'+extendId+']').val()).toFixed(2));
                }
            }
        });


        /**
         *改税率事件
         */
        $('select[name^="negativetaxrateextend"]').on('change',function(){
            if($(this).val()=='6%' || $(this).val()=='17%'){
                var extendId=$(this).data('extend');
                if($('select[name=negativetaxrateextend'+extendId+']').find('option:selected').val()!=''){
                    var taxrate=$('select[name=negativetaxrateextend'+extendId+']').find('option:selected').val()=='6%'?1.06:1.17;
                    var amountofmoneyval=accDiv($('input[name=negativetotalandtaxextend'+extendId+']').val(),taxrate);
                    $('input[name=negativeamountofmoneyextend'+extendId+']').val(amountofmoneyval.toFixed(2))
                    taxrate=$('select[name=negativetaxrateextend'+extendId+']').find('option:selected').val()=='6%'?0.06:0.17;
                    $('input[name=negativetaxextend'+extendId+']').val(accMul(amountofmoneyval,taxrate).toFixed(2));
                }
            }
        });

        //如果是普票等等不允许填写金额
        var taxType=$("#taxType").val();
        if(taxType=='generalinvoice'||taxType=='electronicinvoice'){
            $('input[name^="negativetotalandtaxextend"]').each(function () {
                $(this).val($(this).data('fee'));
                $(this).trigger('keyup');
            });
            $('input[name^="negativetotalandtaxextend"],input[name^="negativeamountofmoneyextend"],input[name^="negativetaxextend"]').prop("readonly",true);
        }
        //保存红冲数据事件
        $(".saveRedInvoice").click(function () {
            var extendId=$(this).data('id');
            var negativeInvoiceCode=$("input[name=negativeinvoicecodeextend"+extendId+"]").val();//红冲发票代码
            var negativeInvoiceNo=$("input[name=negativeinvoice_noextend"+extendId+"]").val();//红冲发票号码
            var negativeBillingTime=$("input[name=negativebillingtimerextend"+extendId+"]").val();//开票时间
            var negativeAmountOfMoney=$("input[name=negativeamountofmoneyextend"+extendId+"]").val();//开票金额
            var negativeTax=$("input[name=negativetaxextend"+extendId+"]").val();//开票税
            var negativeTaxRate=$("select[name=negativetaxrateextend"+extendId+"]").val();//开票税
            var negativeTotalAndTax=$("input[name=negativetotalandtaxextend"+extendId+"]").val();//价税合计
            var negativeRemark=$("textarea[name=negativeremarkextend"+extendId+"]").val();//备注
            var negativeBusinessNames=$("input[name=negativebusinessnamesextend"+extendId+"]").val();//开票抬头
            var negativeCommodityName=$("input[name=negativecommoditynameextend"+extendId+"]").val();//商品名称
            var infoArray=new Array();
            if(negativeInvoiceCode==""){
                Vtiger_Helper_Js.showMessage({type:'error',text:'发票代码不能为空'});
                return false;
            }
            if(negativeInvoiceNo==""){
                Vtiger_Helper_Js.showMessage({type:'error',text:'发票号码不能为空'});
                return false;
            }
            if(negativeBillingTime==""){
                Vtiger_Helper_Js.showMessage({type:'error',text:'开票时间不能为空'});
                return false;
            }
            if(negativeAmountOfMoney==""){
                Vtiger_Helper_Js.showMessage({type:'error',text:'金额不能为空'});
                return false;
            }
            if(negativeTaxRate==""){
                Vtiger_Helper_Js.showMessage({type:'error',text:'税率不能为空'});
                return false;
            }
            if(negativeTax==""){
                Vtiger_Helper_Js.showMessage({type:'error',text:'税额不能为空'});
                return false;
            }
            if(negativeTotalAndTax==""){
                Vtiger_Helper_Js.showMessage({type:'error',text:'价税合计不能为空'});
                return false;
            }
            if(negativeBusinessNames==""){
                Vtiger_Helper_Js.showMessage({type:'error',text:'实际开票抬头不能为空'});
                return false;
            }
            if(negativeCommodityName==""){
                Vtiger_Helper_Js.showMessage({type:'error',text:'商品名称不能为空'});
                return false;
            }
            //插入json数据
            infoArray.push({'negativeRemark':negativeRemark,'negativeInvoiceCode':negativeInvoiceCode,'negativeInvoiceNo':negativeInvoiceNo,'negativeBillingTime':negativeBillingTime,'negativeAmountOfMoney':negativeAmountOfMoney,'negativeTaxRate':negativeTaxRate,'negativeTax':negativeTax,'negativeTotalAndTax':negativeTotalAndTax,'negativeBusinessNames':negativeBusinessNames,'negativeCommodityName':negativeCommodityName});
            var module = app.getModuleName();
            var postData = {
                "module": module,
                "action": "BasicAjax",
                'extendId':extendId,
                'mode': 'saveRedInvoiceInfo',
                'saveInfo':infoArray,
            }
            AppConnector.request(postData).then(
                // 请求成功
                function(data){
                    if(data.result.flag){
                        Vtiger_Helper_Js.showMessage({type:'success',text:'保存成功，审核后生效'});
                    }else{
                        Vtiger_Helper_Js.showMessage({type:'error',text:data.result.msg});
                        return false;
                    }
                },function(error,err){
                    Vtiger_Helper_Js.showMessage({type:'error',text:'请求失败'});
                    return false;
                });
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

        //乘法运算解决Js相乘的问题
        function accMul(arg1,arg2){
            var m=0,s1=arg1.toString(),s2=arg2.toString();
            try{m+=s1.split(".")[1].length}catch(e){}
            try{m+=s2.split(".")[1].length}catch(e){}
            return Number(s1.replace(".",""))*Number(s2.replace(".",""))/Math.pow(10,m)
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


    },

    //流程红冲
    workFlowRedInvoice:function(taxType,isUse){
        var step=true;
        var instanceThis=this;
        var recordId = $('input[name="record"]').val();
        var module = app.getModuleName();
        var postData = {
            "module": module,
            "action": "BasicAjax",
            "record": recordId,
            'mode': 'getFinanceList',
            'type':'redInvoice',
            'billingsourcedata':$("#billingsourcedata").val()
        }
        AppConnector.request(postData).then(
            // 请求成功
            function(data){
                if (data.success) {
                    table='<table class="table" id="invoiceTable" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0"><thead><tr><th><input type="checkbox"  id="abandonedAllCheck" /></th><th>发票号码</th><th>发票代码</th><th>价税合计</th><th>剩余价税合计</th></tr></thead><tbody align="center">';
                    for (var i=0;i<data.result.invoice.length;i++){
                        table+='<tr><td class="fieldLabel medium"><input type="checkbox" name="abandonedCheck" data-id="'+data.result.invoice[i].invoiceextendid+'" data-fee="' + data.result.invoice[i].totalandtaxextend + '"/></td><td class="fieldLabel medium">'+data.result.invoice[i].invoice_noextend+'</td><td class="fieldLabel medium">'+data.result.invoice[i].invoicecodeextend+'</td><td class="fieldLabel medium">'+data.result.invoice[i].totalandtaxextend+'</td><td class="fieldLabel medium">'+data.result.invoice[i].surplusnewnegativeinvoice+'</td></tr>';
                    }
                    table+='</tbody></table>';
                    table+='<table id="invoiceExtendTable" class="table table-bordered blockContainer showInlineTable  detailview-table"><thead><tr><th class="blockHeader" colspan="8"><span class="redColor">发票红冲</span></th></tr></thead><tbody><tr>';
                    if(data.result.order){
                        table+='<td><b>所属订单</b></td><td><b>商品名称</b></td><td><b>订单支付时间</b></td>';
                    }else{
                        table+='<td><b>所属合同</b></td><td><b>入账日期</b></td>';
                    }
                    table+='<td><b>入账金额</b></td><td><b>可开票金额</b></td><td><b>剩余开票金额</b></td><td><b>作废金额</b></td></tr>';
                    if(data.result.payment&&data.result.payment.length>0){
                        for (var i=0;i<data.result.payment.length;i++){
                            table+='<tr><td>'+data.result.payment[i].contract_no+'</td><td>'+data.result.payment[i].arrivaldate+'</td><td>'+data.result.payment[i].total+'</td><td>'+data.result.payment[i].allowinvoicetotal+'</td><input type="hidden" data-newinvoiceraymentid="'+data.result.payment[i].newinvoiceraymentid+'"  data-servicecontractsid="'+data.result.payment[i].servicecontractsid+'" data-receivedpaymentsid="'+data.result.payment[i].receivedpaymentsid+'" data-contract_no="'+data.result.payment[i].contract_no+'" data-total="'+data.result.payment[i].total+'" data-allowinvoicetotal="'+data.result.payment[i].allowinvoicetotal+'" data-invoicetotal="'+data.result.payment[i].invoicetotal+'"><td>'+data.result.payment[i].surpluinvoicetotal+'</td><td><input type="number" class="tovoid_tovoie_total" value="0" min="0.0" step="0.01" name="tovoie_total['+(i+1)+']" style="width: 100px; height:15px;"/></td></tr>';
                        }
                    }else if(data.result.order){
                        //订单开票金额
                        for(var i in data.result.order){
                            i=parseInt(i);
                            table += '<tr><td>' + data.result.order[i].ordercode + '</td><td>' + data.result.order[i].producttitle + '</td><td>' + data.result.order[i].createtime + '</td><td>' + data.result.order[i].money + '</td><td>' + data.result.order[i].invoicemoney + '</td><input type="hidden" data-newinvoiceraymentid="' + data.result.order[i].dongchaliorderid + '"><td>' + data.result.order[i].remainingmoney + '</td><td><input type="number" class="tovoid_tovoie_total" value="0" min="0.0" step="0.01" name="tovoie_total[' + (i + 1) + ']" style="width: 100px; height:15px;"/></td></tr>';
                        }
                    }else{
                        table+='<tr><th class="blockHeader" colspan="7"><span class="redColor">'+data.result.msg+'</span></th></tr>';
                    }
                    table+='</tbody></table>';
                    var msg={
                        'message':'红冲发票',
                        'width':'1000px'
                    };
                    Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                        //选择完后进行提交
                        var ids=new Array();
                        var newInvoicePaymentIds=new Array();//发票回款关联表的id
                        var newInvoicePayment=new Array();//废弃的金额
                        var totalCheckedFee = 0;
                        var totalCheckedVoidFee = 0
                        var billingsourcedata=$("#billingsourcedata").val();
                        $("input[name='abandonedCheck']:checked").each(function () {
                            ids.push($(this).data('id'));
                            totalCheckedFee = instanceThis.accAdd(totalCheckedFee, $(this).data('fee'));
                            if(taxType=='specialinvoice'&&isUse==1){
                                //如已使用部分红冲必须上传附件
                                if($('input[name^="extendfile'+$(this).data('id')+'["]').length==0){
                                    step=false;
                                    Vtiger_Helper_Js.showMessage({type: 'error', text: '专票部分红冲需要上传红字信息表'});
                                    return false;
                                }
                            }
                        });
                        $("#invoiceExtendTable .tovoid_tovoie_total").each(function () {
                            if(parseFloat($(this).parent().prev().html()) < parseFloat($(this).val())){
                                Vtiger_Helper_Js.showMessage({
                                    type: 'error',
                                    text: '作废发票金额' + $(this).val() + '必须小于或等于剩余此次开票金额'
                                });
                                step=false;
                                return false;
                            }
                            totalCheckedVoidFee = instanceThis.accAdd(totalCheckedVoidFee, $(this).val());
                            //拿到所有的关联回款数据
                            newInvoicePaymentIds.push($(this).parent().prev().prev().data('newinvoiceraymentid'));
                            //获取所有废弃金额
                            newInvoicePayment.push($(this).val());
                        });
                        if(totalCheckedFee==0){
                            step=false;
                            Vtiger_Helper_Js.showMessage({type: 'error', text: '已经选择的价税合计必须大于0'});
                            return false;
                        }
                        if(totalCheckedVoidFee==0){
                            step=false;
                            Vtiger_Helper_Js.showMessage({type: 'error', text: '红冲发票金额必须大于0'});
                            return false;
                        }

                        if(totalCheckedFee != totalCheckedVoidFee&&isUse==0&&totalCheckedVoidFee!=0){
                            //必须价税合计相等
                            step=false;
                            Vtiger_Helper_Js.showMessage({type: 'error', text: '红冲发票金额必须与已经选择的价税合计相等'});
                            return false;
                        }
                        if(!step){
                            return false;
                        }
                        //有回款的情况下
                        if(ids.length>0){
                            //提交废弃
                            var recordId = $('input[name="record"]').val();
                            var module = app.getModuleName();
                            var postData = {
                                "module": module,
                                "action": "BasicAjax",
                                "record": recordId,
                                'extendIds':ids,
                                'mode': 'abandonedWorkFlow',
                                'newInvoicePaymentIds':newInvoicePaymentIds,
                                'newInvoicePayment':newInvoicePayment,
                                'type':'redInvoice',
                                'billingsourcedata':$("#billingsourcedata").val()
                            }
                            AppConnector.request(postData).then(
                                // 请求成功
                                function(data){
                                    if (data.result.flag) {
                                        Vtiger_Helper_Js.showMessage({type:'success',text:'申请红冲成功'});
                                    }else{
                                        Vtiger_Helper_Js.showMessage({type:'error',text:data.result.msg});
                                        return false;
                                    }
                                    window.location.reload();
                                },function(error,err){
                                    Vtiger_Helper_Js.showMessage({type:'error',text:'请求失败'});
                                    return false;
                                });
                        }else{
                            Vtiger_Helper_Js.showMessage({type:'error',text:'无选择发票'});
                            return false;
                        }
                    },function(error, err) {});
                    $('.bootbox-confirm').css('overflow-y','scroll');
                    $('.modal-content .modal-body').css('overflow','hidden');
                    $('.modal-content .modal-body').append(table);
                    $(".modal-content .modal-body").on('click','#abandonedAllCheck',function () {
                        if($(this).prop('checked')){
                            $("input[name='abandonedCheck']").prop('checked',true);
                        }else{
                            $("input[name='abandonedCheck']").prop('checked',false);
                        }
                    });
                }
            },
            function(error,err){
                Vtiger_Helper_Js.showMessage({type:'error',text:'获取发票失败'});
            });
    },

    //流程作废
    workFlowVoid:function(){
        var step=true;
        var instanceThis=this;
        //废弃
        var recordId = $('input[name="record"]').val();
        var module = app.getModuleName();
        var postData = {
            "module": module,
            "action": "BasicAjax",
            "record": recordId,
            'mode': 'getFinanceList',
            'billingsourcedata':$("#billingsourcedata").val()
        }
        AppConnector.request(postData).then(
            // 请求成功
            function (data) {
                if (data.success) {
                    table = '<table class="table" id="invoiceTable" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0"><thead><tr><th><input type="checkbox"  id="abandonedAllCheck" /></th><th>发票号码</th><th>价税合计</th><th>作废金额</th></tr></thead><tbody align="center">';
                    for (var i = 0; i < data.result.invoice.length; i++) {
                        table += '<tr><td class="fieldLabel medium"><input type="checkbox" name="abandonedCheck" data-id="' + data.result.invoice[i].invoiceextendid + '" data-fee="' + data.result.invoice[i].totalandtaxextend + '"/></td><td class="fieldLabel medium">' + data.result.invoice[i].invoice_noextend + '</td><td class="fieldLabel medium">' + data.result.invoice[i].totalandtaxextend + '</td><td class="fieldLabel medium">' + data.result.invoice[i].totalandtaxextend + '</td></tr>';
                    }
                    table += '</tbody></table>';
                    table += '<table id="invoiceExtendTable" class="table table-bordered blockContainer showInlineTable  detailview-table"><thead><tr><th class="blockHeader" colspan="8"><span class="redColor">发票作废</span></th></tr></thead><tbody><tr>';
                    if(data.result.order){
                        table+='<td><b>所属订单</b></td><td><b>商品名称</b></td><td><b>订单支付时间</b></td>';
                    }else{
                        table+='<td><b>所属合同</b></td><td><b>入账日期</b></td>';
                    }
                    table+='<td><b>入账金额</b></td><td><b>可开票金额</b></td><td><b>剩余开票金额</b></td><td><b>作废金额</b></td></tr>';
                    if (data.result.payment&&data.result.payment.length > 0) {
                        for (var i = 0; i < data.result.payment.length; i++) {
                            table += '<tr><td>' + data.result.payment[i].contract_no + '</td><td>' + data.result.payment[i].arrivaldate + '</td><td>' + data.result.payment[i].total + '</td><td>' + data.result.payment[i].allowinvoicetotal + '</td><input type="hidden" data-newinvoiceraymentid="' + data.result.payment[i].newinvoiceraymentid + '"  data-servicecontractsid="' + data.result.payment[i].servicecontractsid + '" data-receivedpaymentsid="' + data.result.payment[i].receivedpaymentsid + '" data-contract_no="' + data.result.payment[i].contract_no + '" data-total="' + data.result.payment[i].total + '" data-allowinvoicetotal="' + data.result.payment[i].allowinvoicetotal + '" data-invoicetotal="' + data.result.payment[i].invoicetotal + '"><td>' + data.result.payment[i].surpluinvoicetotal + '</td><td><input type="number" class="tovoid_tovoie_total" value="0" min="0.0" step="0.01" name="tovoie_total[' + (i + 1) + ']" style="width: 100px; height:15px;"/></td></tr>';
                        }
                    } else if(data.result.order){
                        //订单开票金额
                        for(var i in data.result.order){
                            i=parseInt(i);
                            table += '<tr><td>' + data.result.order[i].ordercode + '</td><td>' + data.result.order[i].producttitle + '</td><td>' + data.result.order[i].createtime + '</td><td>' + data.result.order[i].money + '</td><td>' + data.result.order[i].invoicemoney + '</td><input type="hidden" data-newinvoiceraymentid="' + data.result.order[i].dongchaliorderid + '"><td>' + data.result.order[i].remainingmoney + '</td><td><input type="number" class="tovoid_tovoie_total" value="0" min="0.0" step="0.01" name="tovoie_total[' + (i + 1) + ']" style="width: 100px; height:15px;"/></td></tr>';
                        }
                    }else {
                        table += '<tr><th class="blockHeader" colspan="7"><span class="redColor">' + data.result.msg + '</span></th></tr>';
                    }
                    table += '</tbody></table>';
                    var msg = {
                        'message': '作废发票',
                        'width': '1000px'
                    };
                    Vtiger_Helper_Js.showConfirmationBox(msg).then(function (e) {
                        //选择完后进行提交
                        var ids = new Array();
                        var newInvoicePaymentIds = new Array();//发票回款关联表的id
                        var newInvoicePayment = new Array();//废弃的金额
                        var totalCheckedFee = 0;
                        var totalCheckedVoidFee = 0
                        var billingsourcedata=$("#billingsourcedata").val();
                        $("input[name='abandonedCheck']:checked").each(function () {
                            ids.push($(this).data('id'));//发票id
                            totalCheckedFee = instanceThis.accAdd(totalCheckedFee, $(this).data('fee'));//发票一共废除总额
                        });
                        $("#invoiceExtendTable .tovoid_tovoie_total").each(function () {
                            if (billingsourcedata=='contractsource'&&(parseFloat($(this).parent().prev().html()) < parseFloat($(this).val()))) {
                                Vtiger_Helper_Js.showMessage({
                                    type: 'error',
                                    text: '合同作废发票金额' + $(this).val() + '必须小于或等于剩余此次开票金额'
                                });
                                step=false;
                                return false;
                            }else if(billingsourcedata=='ordersource'&&parseFloat($(this).val())!=0&&instanceThis.FloatSub(parseFloat($(this).parent().prev().html()),parseFloat($(this).val()))!=0){
                                //如是订单渠道作废要么为0不填，要么必须全部把票开掉
                                Vtiger_Helper_Js.showMessage({
                                    type: 'error',
                                    text: '订单作废发票金额' + $(this).val() + '必须等于剩余此次开票金额'
                                });
                                step=false;
                                return false;
                            }
                            totalCheckedVoidFee = instanceThis.accAdd(totalCheckedVoidFee, $(this).val());
                            //拿到所有的关联回款数据
                            newInvoicePaymentIds.push($(this).parent().prev().prev().data('newinvoiceraymentid'));
                            //获取所有废弃金额
                            newInvoicePayment.push($(this).val());
                        });
                        if(!step){
                            return false;
                        }
                        //有回款的条件下价税合计与废除金额相等或者无回款关联
                        if (totalCheckedFee == totalCheckedVoidFee || (data.result.payment&&data.result.payment.length == 0)) {
                            if (ids.length > 0) {
                                //提交废弃
                                var recordId = $('input[name="record"]').val();
                                var module = app.getModuleName();
                                var postData = {
                                    "module": module,
                                    "action": "BasicAjax",
                                    "record": recordId,
                                    'extendIds': ids,
                                    'mode': 'abandonedWorkFlow',
                                    'newInvoicePaymentIds': newInvoicePaymentIds,
                                    'newInvoicePayment': newInvoicePayment,
                                    'billingsourcedata':$("#billingsourcedata").val()
                                }
                                AppConnector.request(postData).then(
                                    // 请求成功
                                    function (data) {
                                        if (data.result.flag) {
                                            Vtiger_Helper_Js.showMessage({type: 'success', text: '申请作废成功'});
                                        } else {
                                            Vtiger_Helper_Js.showMessage({type: 'error', text: data.result.msg});
                                            return false;
                                        }
                                        window.location.reload();
                                    }, function (error, err) {
                                        Vtiger_Helper_Js.showMessage({type: 'error', text: '请求失败'});
                                        return false;
                                    });
                            } else {
                                Vtiger_Helper_Js.showMessage({type: 'error', text: '无选择发票'});
                                return false;
                            }
                        } else {
                            //必须价税合计
                            Vtiger_Helper_Js.showMessage({type: 'error', text: '作废发票金额必须与已经选择的价税合计相等'});
                            return false;
                        }
                    }, function (error, err) {
                    });
                    $('.bootbox-confirm').css('overflow-y','scroll');
                    $('.modal-content .modal-body').css('overflow', 'hidden');
                    $('.modal-content .modal-body').append(table);
                    $(".modal-content .modal-body").on('click', '#abandonedAllCheck', function () {
                        if ($(this).prop('checked')) {
                            $("input[name='abandonedCheck']").prop('checked', true);
                        } else {
                            $("input[name='abandonedCheck']").prop('checked', false);
                        }
                    });
                }
            },
            function (error, err) {
                Vtiger_Helper_Js.showMessage({type: 'error', text: '获取发票失败'});
            });
    },

    /**
     *判断是否年月一致
     * @param invoiceTime
     * @param nowTime
     * @returns {boolean|boolean}
     */
    isTheSameMonth:function(invoiceTime,nowTime){
        var dt1 = new Date(invoiceTime.replace(/-/g,"/"));
        var dt2 = new Date(nowTime.replace(/-/g,"/"));
        return dt1.getFullYear() === dt2.getFullYear() && dt1.getMonth() === dt2.getMonth();
    },

    //上传部分红冲附件
    getUploadExtendFile:function(){
        $('div[class^=fileUploadContainer]').each(function () {
            var extendId=$(this).data('id');
            if($('#extendfile'+extendId).length>0){
                var module=$('#module').val();
                KindEditor.ready(function(K) {
                    var uploadbutton = K.uploadbutton({
                        button : K('#uploadButtonExtend'+extendId)[0],
                        fieldName : 'extendfile'+extendId,
                        extraParams :{
                            __vtrftk:$('input[name="__vtrftk"]').val(),
                            record:$('input[name="record"]').val(),
                            extend:extendId
                        },
                        url : 'index.php?module='+module+'&action=FileUpload',
                        afterUpload : function(data) {
                            if (data.success ==true) {
                                $('.extendfiledelete').remove();
                                var str='<span class="label file'+data.result['id']+'" style="margin-left:5px;"><a href="index.php?module=Newinvoice&action=DownloadFile&filename='+data.result['id']+'" target="_blank">'+data.result['name']+'</a>&nbsp;<b class="extendDeletefile" data-extendId="'+extendId+'" data-class="file'+data.result['id']+'" data-id="'+data.result['id']+'" title="删除文件" style="display:inline-block;width:12px;height:12px;line-height:12px;text-align:center">x</b>&nbsp;</span><input class="ke-input-text file'+data.result['id']+'" type="hidden" name="extendfile'+extendId+'['+data.result['id']+']" id="extendfile'+extendId+'" value="'+data.result['name']+'" readonly="readonly" /><input class="file'+data.result['id']+'" type="hidden" name="extendfilesid'+extendId+'['+data.result['id']+']" value="'+data.result['id']+'">';
                                $("#fileallExtend"+extendId).append(str);
                            } else {
                                Vtiger_Helper_Js.showMessage({type: 'error', text: '上传失败'});
                                return false;
                            }
                        },
                        afterError : function(str) {
                        }
                    });
                    uploadbutton.fileBox.change(function(e) {
                        uploadbutton.submit();
                    });
                    $('.fileUploadContainer'+extendId).find('form').css({width:"54px"});
                    $('.fileUploadContainer'+extendId).find('form').find('.btn-info').css({width:"54px",marginLeft:"-15px"});
                });
            }
        });
    },

    /**
     * 删除上传的文件
     */
    deleteuploadExtendFile:function(){
        $('form').on('mouseover','.extendDeletefile',function(){
            $(this).css({color:"#666",cursor:"pointer",border:"#666 solid 1px",borderRadius:"12px"});
        }).on('mouseout','.extendDeletefile',function(){
            $(this).css({color:"#fff",border:"none",borderRadius:"none"});
        }).on('click','.extendDeletefile',function(){
            var delclassid=$(this).data('id');
            var module=$('#module').val();
            var url='index.php?module='+module+'&action=DeleteFile&id='+delclassid+'&record='+$('input[name="record"]').val()+'&extend='+$(this).data('extendid');
            AppConnector.request(url).then(
                function(data){
                    if(data['success']) {
                        $('.file'+delclassid).remove();
                    } else {
                        Vtiger_Helper_Js.showMessage({type: 'error', text: '删除失败'});
                        return false;
                    }
                },
                function(error){
                    Vtiger_Helper_Js.showMessage({type: 'error', text: '请求失败'});
                    return false;
                }
            )
        });

    },

    registerEvents: function(){
        this._super();
        this.getsigndalog();
        this.getBilling();
        this.addmarker();
        this.tovoid();
        this.vovoid_button_click();
        this.addNewinvoicerayment();
        this.select_newinvoicerayment();
        this.savebuttonnewinvoicerayment();
        this.deletedNewinvoiceRayment();
        //this.deletedbuttonnewinvoicerayment();
        this.scanUserCode();
        this.changeSmownerid();
        this.makeWorkflowStages();
        //this.addMinus();
        var invoicetype = $("input[name='d_invoicetype']").val();
        if(invoicetype == 'c_billing'){
            this.loadRelationReceivedPayments('getDetailNewinvoicerayment');
            this.serviceContracts_change();
        }
        var is_void_flow=$("input[name='is_void_flow']").val();
        if(is_void_flow==2){
            //所有需要红冲的发票开票时间
            $('input[name^=negativebillingtimerextend]').each(function () {
                $(this).datetimepicker({
                    defaultDate : new Date(),
                    language:'zh-CN',weekStart:1,todayBtn:1,autoclose:1,todayHighlight:1,startView:2,minView:2,forceParse:0,format: 'yyyy-mm-dd',pickDate: true, pickTime: true,hourStep: 1,autoclose:1
                });
            });
            this.saveRedInvoiceInfo();
        }
        this.invoiceAbandonedOrRed();
        this.getUploadExtendFile();
        this.deleteuploadExtendFile();
    }
});