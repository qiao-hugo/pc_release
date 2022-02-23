/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("SupplierContracts_Edit_Js", {}, {
    ckEditorInstance: '',
    ckEInstance: '',
    rowSequenceHolder: false,
    temp_tr : '',
    registerRecordPreSaveEvent: function () {
        var thisInstance = this;
        var editViewForm = this.getForm();
        editViewForm.on(Vtiger_Edit_Js.recordPreSave,function(e,data){

        if ('无锡珍岛数字生态服务平台技术有限公司' == $('select[name="invoicecompany"]').val() && !$('select[name="sealplace"]').val()) {
            Vtiger_Helper_Js.showMessage({type:'error', text:'无锡珍岛数字生态服务平台技术有限公司必须选择用章地点'});
            e.preventDefault();
            return false;
        }

            //合同是已回收状态时，签收复选框必须勾选
        if($("input[name='current_modulestatus']").val()=='c_recovered') {
            if(!$('#SupplierContracts_editView_fieldName_iscomplete').attr('checked')) {
                Vtiger_Helper_Js.showMessage({type:'error', text:'已收回状态合同必须勾选签收复选框'});
                e.preventDefault();
                return false;
            }
        }
        var modulestatus=$('select[name="modulestatus"]').val();
            if(modulestatus=='s_sign') {
                var paymethed = $('select[name=paymethed]').val();
                var effectivetime = $('#SupplierContracts_editView_fieldName_effectivetime').val();
                if( !(paymethed && effectivetime) ) {
                    Vtiger_Helper_Js.showMessage({type:'error',text:'付款方式和有效期限不能为空'});
                    e.preventDefault();
                }
                var returndate = $('#SupplierContracts_editView_fieldName_Returndate').val(); //归还日期
                var signdate = $('#SupplierContracts_editView_fieldName_signdate').val();     //签订日期
                var receivedate = $('#SupplierContracts_editView_fieldName_receivedate').val();     //领取日期
                if(! (returndate && signdate && receivedate)) {
                    Vtiger_Helper_Js.showMessage({type:'error',text:'归还日期、签订日期、领取日期不能为空'});
                    e.preventDefault();
                }
            }

            var old_vendorid = $('input[name=old_vendorid]').val();
            var vendorid = $('input[name=vendorid]').val();
            if(old_vendorid&&old_vendorid !=vendorid){
                Vtiger_Helper_Js.showMessage({type:'error',text:'当前采购合同供应商不可变更'});
                e.preventDefault();
                return false;
            }

            var enddate = $('input[name^="enddate["]');
            var dateFlag=false;
            $.each(enddate,function(key,value){
                var enddateValue=$(value).val();
                var thisDataId=$(value).data('id');
                var effectdate=$('input[name="effectdate['+thisDataId+']"').val();
                if ((new Date(effectdate.replace(/-/g,'\/')))>(new Date(enddateValue.replace(/-/g,'\/')))) {
                    dateFlag=true;
                    return false;
                }
            });
            if(dateFlag){
                var  params = {text : app.vtranslate(),title : app.vtranslate('生效时间不能大于失效日期!')};
                Vtiger_Helper_Js.showPnotify(params);
                e.preventDefault();
                return false;
            }
            var total = $("input[name='total']").val().replace(/\,/g,"");
            var compareprice = $("input[name='compareprice']").val();
            var existfile=$("#file").val();
            if(parseFloat(total)>0 && parseFloat(compareprice)>0 && parseFloat(total)>=parseFloat(compareprice) && (!existfile||existfile==undefined)){
                var  params = {text : app.vtranslate(),title : app.vtranslate('请先上传询比价单附件!')};
                Vtiger_Helper_Js.showPnotify(params);
                e.preventDefault();
                return false;
            }

            var frameworkcontract = $('select[name="frameworkcontract"]').val();
            var limitprice = $("input[name='limitprice']").val();
            if(limitprice==0 ||frameworkcontract=='yes' || parseFloat(total)>0 && parseFloat(limitprice)>0 && parseFloat(total)>=parseFloat(limitprice)){
                var vendorid = $("input[name='vendorid']").val();
                var bankaccount = $("#SupplierContracts_editView_fieldName_bankaccount").val();
                var bankname = $("#SupplierContracts_editView_fieldName_bankname").val();
                var banknumber = $("#SupplierContracts_editView_fieldName_banknumber").val();
                if(!vendorid || !bankaccount || !bankname ||!banknumber){
                    var  params = {text : app.vtranslate(),title : app.vtranslate('账号信息栏目必填项不能为空!')};
                    Vtiger_Helper_Js.showPnotify(params);
                    e.preventDefault();
                    return false;
                }
            }

            //5.53,提交前解除disabled
            $('select[name="contractattribute"]').prop("disabled",false);
        });
    },
    init: function(){
        var m = $('select[name=modulestatus]').val();
        if (m != 's_sign') {
            //有效日期
            TdHtmlHide('input[name=paymethed]', 'hide', 0);

        } else {
            var b = $('input[name=indefinite]').is(":checked");
            if (!b) {
                TdHtmlHide('#SupplierContracts_editView_fieldName_effectivetime', 'hide');
            }
        }

        $('select[name=modulestatus]').change(function() {
            if($(this).val() == 's_sign') {
                var b = $('input[name=indefinite]').is(":checked");
                if (!b) {
                    TdHtmlHide('#SupplierContracts_editView_fieldName_effectivetime', 'show');
                }

                TdHtmlHide('input[name=paymethed]', 'show', 500);
            } else {
                TdHtmlHide('#SupplierContracts_editView_fieldName_effectivetime', 'hide');
                TdHtmlHide('input[name=paymethed]', 'hide', 500);
            }
        });

        function TdHtmlHide(query, type, time){
            var $effectivetime_td = $(query).closest('td');
            var effectivetime_td_html =  '<td class="fieldValue medium">' + $effectivetime_td.html() + '</td>';

            var $effectivetime_field_td = $effectivetime_td.prev();
            var effectivetime_field_td_html = '<td class="fieldValue medium">' + $effectivetime_field_td.html() + '</td>';

            var twoTd = effectivetime_field_td_html + effectivetime_td_html;
            if (type == 'show') {
                $(query).closest('td').prev().show(time);
                $(query).closest('td').show(time);
            } else {
                $(query).closest('td').prev().hide(time);
                $(query).closest('td').hide(time);
            }
        }


        // 采购合同编码
        var record = $('input[name=record]').val();
        if (!record) {
            // 添加的时候 隐藏
            //TdHtmlHide('input[name=contract_no]', 'hide', 0);
        }

        $('input[name=indefinite]').change(function() {
            var boolen =  $(this).is(":checked");
            if (boolen) {
                TdHtmlHide('#SupplierContracts_editView_fieldName_effectivetime', 'hide');
            } else {
                var modulestatus=$('select[name="modulestatus"]').val();
                if(modulestatus=='s_sign') {
                    TdHtmlHide('#SupplierContracts_editView_fieldName_effectivetime', 'show', 500);
                }
            }
        });


        $('#SupplierContracts_editView_fieldName_contract_no').attr('disabled','disabled');


        $('#SupplierContracts_editView_fieldName_Returndate').change(function() {
            //$('#modulestatus').setValue('s_return');
        });
    },
    add_vendorsrebate: function () {
        $('#add_vendorsrebate').click(function () {
            var numd=$('.Duplicates').length+1;
            if(numd>100){return;}/*超过100个不允许添加*/
            var nowdnum=$('.Duplicates').last().data('num');
            if(nowdnum!=undefined){
                numd=nowdnum+1;
            }
            var t_vendorsrebate_html=vendorsrebate_html.replace(/\[\]|replaceyes/g,'['+numd+']');
            t_vendorsrebate_html=t_vendorsrebate_html.replace(/yesreplace/g,numd);
            t_vendorsrebate_html=t_vendorsrebate_html.replace(/reg_select_html/g, product_html);
            // 产品json数据
            //reg_select_html
            $('#vendorsrebate').append(t_vendorsrebate_html);
            $('.t_date').datetimepicker({
                format: "yyyy-mm-dd",
                language:  'zh-CN',
                autoclose: true,
                autoclose:1,
                todayHighlight:1,
                startView:2,
                minView:2,
                forceParse:0,
                pickerPosition: "bottom-left",
                showMeridian: 0
            });
            $('.chzn-select').chosen();
        });
        $("#EditView").on('click','.delbutton',function(){
            var msg = {
                'message': '<span style="color:red">确定要删除吗?</span>',
                "width":"400px",
            };
            var thisInstance=this;
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(){
                $(thisInstance).closest("table").remove();
            });
        });
        var record = $('input[name=record]').val();
        if (!record) {
            $('#add_vendorsrebate').trigger('click');
        }
    },
    product_select: function () {
        $(document).on('change', '.product_select', function () {
            $(this).closest('td').find('.t_productname').val($(this).find("option:selected").text());
        });
    },
    delbutton: function () {
        $(document).on('click', '.delbutton', function () {
            $(this).closest('.Duplicates').remove();
        });
    },
    registerReferenceSelectionEvent : function(container) {
        this._super(container);
        var thisInstance=this;
        $('input[name="vendorid"]').on(Vtiger_Edit_Js.postReferenceSelectionEvent, function (e, data) {
            thisInstance.selectBankInfo($(this).val());
        });

    },
    selectBankInfo:function(vendorid,flag){
        var flag=flag||0;
        var isneed = $("select[name='soncate'] option:selected").attr("data-isneed");
        var progressIndicatorElement = jQuery.progressIndicator({ 'message' : '努力加载中...','blockInfo':{'enabled':true }});
        var params= {'module':'SupplierContracts','mode':'getVendorBankInfo','action':'BasicAjax','accountid':vendorid,'isneed':isneed};
        var banklistValue=$('select[name="banklist"]').val();
        $('select[name="banklist"]')[0].options.length=0;
        if(!flag){
            $('input[name="bankaccount"]').val('');
            $('input[name="bankname"]').val('');
            $('input[name="banknumber"]').val('');
            $('input[name="bankcode"]').val('');
        }
        AppConnector.request(params).then(
            function(data){
                progressIndicatorElement.progressIndicator({ 'mode' : 'hide'});
                console.log(data);
                if(data.success) {
                    var bankinfo=data.result;
                    var bankinfostr='';
                    $.each(bankinfo,function(key,value){
                        if(key==0 && flag==0){
                            $('input[name="bankaccount"]').val(value.bankaccount);
                            $('input[name="bankname"]').val(value.bankname);
                            $('input[name="banknumber"]').val(value.banknumber);
                            $('input[name="bankcode"]').val(value.bankcode);
                        }
                        var selectedValueb=((value.bankaccount==banklistValue)?' selected':'');
                        bankinfostr+='<option value="'+value.bankaccount+'" data-bankaccount="'+value.bankaccount+'" data-bankname="'+value.bankname+'" data-banknumber="'+value.banknumber+'" data-bankcode="'+value.bankcode+'" '+selectedValueb+'>'+value.banknumber+'</option>'
                    });
                    $('select[name="banklist"]')[0].options.length=0;
                    $('select[name="banklist"]').append(bankinfostr);
                    $('select[name="banklist"]').trigger("liszt:updated");

                }else{
                    Vtiger_Helper_Js.showMessage({type:'error',text:data.error['message']});
                    $("input[name='vendorid_display']").val('');
                    $("input[name='vendorid']").val('');
                    var frameworkcontract = $('select[name="frameworkcontract"]').val();
                    var limitprice = $("input[name='limitprice']").val();
                    var total = $("input[name='total']").val().replace(/\,/g,"");
                    console.log(limitprice);
                    console.log(total);
                    if(flag==0 && (frameworkcontract=='yes' || limitprice==0 ||parseFloat(total)>0 && parseFloat(limitprice)>0 && parseFloat(total)>=parseFloat(limitprice))){
                        Vtiger_Helper_Js.showMessage({type:'error',text:'请先在供应商页上传特殊行业资质'});
                    }
                    $("#vendorid_display").val('');
                    $("input[name='vendorid']").val('');
                }
            })
    },
    registerBasicEvents:function(container){
        this._super(container);
        this.registerReferenceSelectionEvent(container);
    },
    banklistchange:function(){
        var thisInstance=this;
        $('#EditView').on("change",'select[name="banklist"]',function(){
            var bankdata=$(this).find("option:selected");
            $('input[name="bankaccount"]').val(bankdata.data("bankaccount"));
            $('input[name="bankname"]').val(bankdata.data("bankname"));
            $('input[name="banknumber"]').val(bankdata.text());
            $('input[name="bankcode"]').val(bankdata.data("bankcode"));
        });
        var record = $('input[name=record]').val();
        if(record>0){
            thisInstance.selectBankInfo($('input[name="vendorid"]').val(),1);
            console.log(2222);
        }
    },

    /**
     *当新建采购合同时，直接选择定制合同
     */
    fixContractattribute:function(){
        var record = $('input[name=record]').val();
        $('select[name="contractattribute"]').prop("disabled",true);
        if(!record){
            $('select[name="contractattribute"]').val('customized');
        }
        $('select[name="contractattribute"]').trigger("liszt:updated");
    },
    blurTotalChange:function(){
        var thisInstance=this;
        $('#EditView').on("blur",'input[name="total"]',function(){

            var limitprice = $("input[name='limitprice']").val();
            var compareprice = $("input[name='compareprice']").val();
            var total = $("input[name='total']").val().replace(/\,/g,"");
            var frameworkcontract = $('select[name="frameworkcontract"]').val();
            console.log(limitprice);
            console.log(total);
            // if(frameworkcontract=='yes' || limitprice==0 || parseFloat(total)>0 && parseFloat(limitprice)>0 && parseFloat(total)>=parseFloat(limitprice)){
            //     console.log(111);
            //     $("#bankinfo").show();
            //     $("#bankinfo").find(":input").attr("disabled", false);
            // }else{
            //     console.log(222);
            //     $("#bankinfo").hide();
            //     $("#bankinfo").find(":input").attr("disabled", true);
            // }
            showTagMust();

            $("#compareinfo").find(":input").attr("disabled", false);
            if(parseFloat(total)>0 && parseFloat(compareprice)>0 && parseFloat(total)>=parseFloat(compareprice)){
                $('#compareinfo').show();
            }else{
                $('#compareinfo').hide();
                $("#compareinfo").find(":input").attr("disabled", true);
            }
        });
    },
    getApplyList:function(){
        var thisInstance=this;
        $('#EditView').on("change",'select[name="soncate"]',function(){
            var soncate=$(this).val();
            var limitprice= $(this).find('option:checked').data("limitprice");
            var compareprice= $(this).find('option:checked').data("compareprice");
            var parentcate=$('select[name="parentcate"]').val();
            var ismultiple = $("input[name='ismultiple']").val();
            var oldpayapply = $("input[name='oldpayapplyids']").val();
            var frameworkcontract = $('select[name="frameworkcontract"]').val();

            console.log(oldpayapply);
            payapplyids=[];
            if(oldpayapply!=''){
                var payapplyids = oldpayapply.split(',');
                console.log(payapplyids);
            }

            $("input[name='limitprice']").val(limitprice);
            $("input[name='compareprice']").val(compareprice);
            console.log(soncate);
            console.log(limitprice);
            console.log(compareprice);
            console.log(parentcate);

            if(!soncate){
                return;
            }
            var total = $("input[name='total']").val().replace(/\,/g,"");
            $("#compareinfo").find(":input").attr("disabled", false);


            $("#compareinfo").find(":input").attr("disabled", false);
            if(parseFloat(total)>0 && parseFloat(compareprice)>0 && parseFloat(total)>=parseFloat(compareprice)){
                $('#compareinfo').show();
            }else{
                $('#compareinfo').hide();
                $("#compareinfo").find(":input").attr("disabled", true);
            }
            showTagMust();
            // $("#bankinfo").find(":input").attr("disabled", false);
            // if(frameworkcontract=='yes' || limitprice==0 ||parseFloat(total)>0 && parseFloat(limitprice)>0 && parseFloat(total)>=parseFloat(limitprice) || frameworkcontract=='yes'){
            //     $("#bankinfo").show();
            // }else{
            //     $("#bankinfo").hide();
            //     $("#bankinfo").find(":input").attr("disabled", true);
            // }
            var record = $('input[name=record]').val();
            var progressIndicatorElement = jQuery.progressIndicator({ 'message' : '努力加载中...','blockInfo':{'enabled':true }});
            var params= {
                'module':'SupplierContracts',
                'mode':'getPayApply',
                'action':'ChangeAjax',
                'parentcate':parentcate,
                'soncate':soncate,
                'record':record,
            };
            AppConnector.request(params).then(
                function(data){
                    progressIndicatorElement.progressIndicator({ 'mode' : 'hide'});
                    if(data.success) {
                        $("#applylist").empty();
                        console.log(data);
                        var option = '';
                        $.each(data.list,function(key,value){
                            var optionstr = '支出申请单单号:<span style="color: red">'+value.payapply_no+'</span>---申请名称:'+value.payapply_name+'---申请人:'+value.last_name+"---申请主体:"+value.invoicecompany;
                            console.log(value.soncate);
                            if($.inArray(value.payapplyid,payapplyids)>=0){
                                option +='<option selected value="'+value.payapplyid+'">'+optionstr+'</option>';
                            }else{
                                option +='<option value="'+value.payapplyid+'">'+optionstr+'</option>';
                            }
                        });
                        if(ismultiple){
                            var selectprodcut='<select class="chzn-select" multiple style="width: 87%"  name="payapplyids[]" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"> ';
                        }else{
                            var selectprodcut='<select class="chzn-select"  style="width: 87%"  name="payapplyids" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"> ';
                        }
                        selectprodcut+=option+'</select>';

                        $("#applylist").append(selectprodcut);
                        $('.chzn-select').chosen();

                    }
                });
        });
    },
    getSonCate:function(){
      var thisInstance=this;
      if(!$('select[name="parentcate"]').val()){
          //return;
      }
        $('#EditView').on("change",'select[name="parentcate"]',function(){
            console.log('aaaaaaaaa');
            var parentcate=$(this).val();
            if(!parentcate){
                return;
            }
            var soncate=$("select[name='soncate']").val();
            console.log(soncate);
            console.log(parentcate);
            var progressIndicatorElement = jQuery.progressIndicator({ 'message' : '努力加载中...','blockInfo':{'enabled':true }});
            var params= {
                'module':'SupplierContracts',
                'mode':'getSonCate',
                'action':'ChangeAjax',
                'parentcate':parentcate
            };
            AppConnector.request(params).then(
                function(data){
                    progressIndicatorElement.progressIndicator({ 'mode' : 'hide'});
                    if(data.success) {
                        $("#soncate").empty();
                        $("#applylist").empty();
                        console.log(data);
                        var option = "" ;
                        var flag = false;
                        $.each(data.list,function(key,value){
                            console.log(value.soncate);
                            var ischecked='';
                            if(value.soncate==soncate){
                                 ischecked='selected';
                                 flag=true;
                            }

                            option +='<option data-isneed="'+value.special+'" data-soncateid="'+value.soncateid+'" '+ischecked+' data-compareprice="'+value.compareprice+'" data-limitprice="'+value.limitprice+'" value="'+value.soncate+'">'+value.soncate+'</option>';
                        });
                        var selectprodcut='<input type=\'hidden\' name=\'soncateid\' value=\'\'><select class="chzn-select" name="soncate" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"> <option value="">选择一个选项</option>';
                        selectprodcut+=option+'</select>';

                        $("#soncate").append(selectprodcut);
                        $('.chzn-select').chosen();

                        soncateid = $("select[name='soncate'] option:selected").attr("data-soncateid");
                        $("input[name='soncateid']").val(soncateid);

                        $("select[name='soncate']").on("change",function (k, v) {
                            soncateid = $("select[name='soncate'] option:selected").attr("data-soncateid");
                            $("input[name='soncateid']").val(soncateid);
                        });
                        if(flag){
                            $("select[name='soncate']").trigger("change");
                        }
                    }
                });
        });
    },
    initSonCate:function(){
        console.log('bbbbbbbb');
        if(!$('select[name="parentcate"]').val()){
            return;
        }
        $('select[name="parentcate"]').trigger("change");
    },
    frameworkcontractChange:function() {
        var thisInstance = this;
        $('#EditView').on("change", 'select[name="frameworkcontract"]', function () {
            frameworkcontract = $(this).val();
            var limitprice = $("input[name='limitprice']").val();
            var total = $("input[name='total']").val().replace(/\,/g,"");
            console.log(limitprice);
            console.log(total);
            showTagMust();
            // if(limitprice==0 ||frameworkcontract=='yes' || parseFloat(total)>0 && parseFloat(limitprice)>0 && parseFloat(total)>=parseFloat(limitprice)){
            //    $("#bankinfo").show();
            //    $("#bankinfo").find(":input").attr("disabled", false);
            // }else{
            //    $("#bankinfo").hide();
            //    $("#bankinfo").find(":input").attr("disabled", true);
            // }
        });
    },
    iscomplete:function(){
        $("#SupplierContracts_editView_fieldName_iscomplete").change(function() {
            //console.log($(this).is(':checked'));
            if($(this).is(':checked')){
                $('select[name="parentcate"]').removeAttr("data-validation-engine");
                $('select[name="soncate"]').removeAttr("data-validation-engine");
                $('select[name="payapplyids"]').removeAttr("data-validation-engine");
                $('select[name="frameworkcontract"]').removeAttr("data-validation-engine");
                $('select[name="parentcate"]').parent('td').prev().children('label').children('span').html('');
                $('select[name="soncate"]').parent('td').prev().children('label').children('span').html('');
                $('select[name="payapplyids"]').parents('td').prev().children('label').children('span').html('');
                $('select[name="frameworkcontract"]').parent('td').prev().children('label').children('span').html('');

            }else{
                $('select[name="parentcate"]').attr("data-validation-engine",$('select[name="parentcate"]').attr("data-validation-engine1"));
                $('select[name="soncate"]').attr("data-validation-engine",$('select[name="soncate"]').attr("data-validation-engine1"));
                $('select[name="payapplyids"]').attr("data-validation-engine",$('select[name="payapplyids"]').attr("data-validation-engine1"));
                $('select[name="frameworkcontract"]').attr("data-validation-engine",$('select[name="frameworkcontract"]').attr("data-validation-engine1"));
                $('select[name="parentcate"]').parent('td').prev().children('label').children('span').html('*');
                $('select[name="soncate"]').parent('td').prev().children('label').children('span').html('*');
                $('select[name="payapplyids"]').parents('td').prev().children('label').children('span').html('*');
                $('select[name="frameworkcontract"]').parent('td').prev().children('label').children('span').html('*');
            }
        });
        $('select[name="parentcate"]').attr("data-validation-engine1",$('select[name="parentcate"]').attr("data-validation-engine"));
        $('select[name="soncate"]').attr("data-validation-engine1",$('select[name="soncate"]').attr("data-validation-engine"));
        $('select[name="payapplyids"]').attr("data-validation-engine1",$('select[name="payapplyids"]').attr("data-validation-engine"));
        $('select[name="frameworkcontract"]').attr("data-validation-engine1",$('select[name="frameworkcontract"]').attr("data-validation-engine"));
        if($("#SupplierContracts_editView_fieldName_iscomplete").is(':checked')){
            $('select[name="parentcate"]').removeAttr("data-validation-engine");
            $('select[name="soncate"]').removeAttr("data-validation-engine");
            $('select[name="payapplyids"]').removeAttr("data-validation-engine");
            $('select[name="frameworkcontract"]').removeAttr("data-validation-engine");
            $('select[name="parentcate"]').parent('td').prev().children('label').children('span').html('');
            $('select[name="soncate"]').parent('td').prev().children('label').children('span').html('');
            $('select[name="payapplyids"]').parents('td').prev().children('label').children('span').html('');
            $('select[name="frameworkcontract"]').parent('td').prev().children('label').children('span').html('');
        }
    },

    clearVendorData:function(){
        $("#SupplierContracts_editView_fieldName_vendorid_clear").on("click",function () {
            $('input[name="vendorid"]').val('');
            $('input[name="vendorid_display"]').val('');
            $('input[name="bankaccount"]').val('');
            $('input[name="bankname"]').val('');
            $('input[name="bankcode"]').val('');
            $('input[name="banknumber"]').val('');
            $('select[name="banklist"]').val('');

            $('select[name="banklist"]')[0].options.length=0;
            $('select[name="banklist"]').trigger("liszt:updated");
        })
    },
    currencytypeChange:function(){
        var currencytype = $("select[name='currencytype']").val();
        if(currencytype == '人民币'){
            $('.add-on').each(function(){
                if($(this).text() == '美金'){
                    $(this).text('人民币');
                }
            });
        }else{
            $('.add-on').each(function(){
                if($(this).text() == '人民币'){
                    $(this).text('美金');
                }
            });
        }
        $("select[name='currencytype']").on("change",function () {
            var currencytype = $(this).val();
            if(currencytype == '人民币'){
                $('.add-on').each(function(){
                    if($(this).text() == '美金'){
                        $(this).text('人民币');
                    }
                });
            }else{
                $('.add-on').each(function(){
                    if($(this).text() == '人民币'){
                        $(this).text('美金');
                    }
                });
            }
        })
    },
    sealplaceChange: function () {
        if ('无锡珍岛数字生态服务平台技术有限公司' != $('select[name="invoicecompany"]').val()) {
            // $('select[name="sealplace"]').parent().hide();
            $('select[name="sealplace"]').val('');
            $('select[name="sealplace"]').parent().prev().css('visibility', 'hidden');
            $('select[name="sealplace"]').parent().css('visibility', 'hidden');
        }
        $('#EditView').on('change', 'select[name="invoicecompany"]', function () {
            // alert($('select[name="invoicecompany"]').val());
            //当操作类型为“订单信息”
            if ('无锡珍岛数字生态服务平台技术有限公司' == $('select[name="invoicecompany"]').val()) {
                // alert('show');
                // $('select[name="sealplace"]').parent().show();
                $('select[name="sealplace"]').parent().prev().css('visibility', 'visible');
                $('select[name="sealplace"]').parent().css('visibility', 'visible');
            } else {
                // alert('hide');
                // $('select[name="sealplace"]').parent().hide();
                $('select[name="sealplace"]').val('');
                $('select[name="sealplace"]').parent().prev().css('visibility', 'hidden');
                $('select[name="sealplace"]').parent().css('visibility', 'hidden');
            }
        });
    },
    registerEvents: function (container) {
        this._super(container);
        this.registerRecordPreSaveEvent();
        this.init();
        this.add_vendorsrebate();
        this.product_select();
        this.banklistchange();
        this.fixContractattribute();
        this.getApplyList();
        this.getSonCate();
        this.blurTotalChange();
        this.initSonCate();
        this.frameworkcontractChange();
        this.iscomplete();
        this.clearVendorData();
        this.currencytypeChange();
        this.sealplaceChange();
        showTagMust();
    }
});

function  showTagMust(){
    var frameworkcontract = $('select[name="frameworkcontract"]').val();
    var limitprice = $("input[name='limitprice']").val();
    var total = $("input[name='total']").val().replace(/\,/g,"");
    if(limitprice==0 ||frameworkcontract=='yes' || parseFloat(total)>0 && parseFloat(limitprice)>0 && parseFloat(total)>=parseFloat(limitprice)) {
        $(".vendors").show();
        $(".bankaccount").show();
        $(".bankname").show();
        $(".banknumber").show();

    }else{
        $(".vendors").hide();
        $(".bankaccount").hide();
        $(".bankname").hide();
        $(".banknumber").hide();
    }
}





