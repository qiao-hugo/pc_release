/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("ServiceContracts_Edit_Js", {}, {
    ckEditorInstance: '',
    ckEInstance: '',
    productnum:[],
    rowSequenceHolder: false,
    eleccontractSubmit:true,
    isonbeforeunload:true,
    customizedData:[],
    parentView:{},
    registerReferenceSelectionEvent: function (container) {
        this._super(container);
        var thisInstance = this;

        //2015年4月24日 星期五 根据合同的客户负责人选择默认合同提单人 wangbin
        jQuery('input[name="sc_related_to"]', container).on(Vtiger_Edit_Js.referenceSelectionEvent, function (e, data) {
            relatedchange();
        });

        function relatedchange() {
            var signaturetype=$('select[name="signaturetype"]').val();
            var sparams = {
                'module': 'ServiceContracts',
                'action': 'BasicAjax',
                'record': $('input[name="sc_related_to"]').val(),
                'mode': 'getsmownerid',
                'signaturetype':signaturetype
            };
            AppConnector.request(sparams).then(
                function (datas) {
                    var suoshurennum=$('select[name="suoshuren[]"').length;
                    if (datas.success == true) {
                        // 2016-9-12 周海 只要客户来自商机 就要分成20%
                        if(suoshurennum<2) {
                            console.log(datas);
                            if (datas.result['fromarket'] == '1' &&  datas.result['shareInfo']!=null) {
                                var shareInfo = datas.result['shareInfo'];
                                $("#fallintotable tbody tr:eq(0)").siblings().remove();
                                $("#fallintotable tbody tr:eq(0)").after(aaaaa);
                                $("#fallintotable tbody tr:eq(1) td:eq(1) select").val(shareInfo['userid']);
                                var yjssCompany = $("#fallintotable tbody tr:eq(1) td:eq(1) select option:selected").data('company');
                                $("#fallintotable tbody tr:eq(1) td:eq(0) select").val(shareInfo['invoicecompany']);
                                $("#fallintotable tbody tr:eq(1) td:eq(2) input").val(shareInfo['promotionsharing']);

                                $("#fallintotable tbody tr:eq(1)").after(aaaaa);
                                $("#fallintotable tbody tr:eq(2) td:eq(0) select").val(yjssCompany);
                                $("#fallintotable tbody tr:eq(2) td:eq(1) select").val(datas.result['smoid']);
                                $("#fallintotable tbody tr:eq(2) td:eq(2) input").val(shareInfo['salesharing']);
                                $("#fallintotable tbody tr:eq(1) td:eq(2) input").attr("readonly",true);
                                $("#fallintotable tbody tr:eq(1) td:eq(1) select").attr("disabled",true);
                                $("#fallintotable tbody tr:eq(1) td:eq(0) select").attr("disabled",true);

                                //$('.chzn-select').chosen();
                                $("#fallintotable tbody tr:eq(1)").find("button").remove();
                                $("#fallintotable tbody tr:eq(2)").find("button").remove();
                            } else {
                                $("#fallintotable tbody tr:eq(0)").siblings().remove();
                                $("#fallintotable tbody tr:eq(0)").after(aaaaa);
                                $("#fallintotable tbody tr:eq(1) td:eq(1) select").val(datas.result['smoid']);
                                var yjssCompany = $("#fallintotable tbody tr:eq(1) td:eq(1) select option:selected").data('company');
                                $("#fallintotable tbody tr:eq(1) td:eq(0) select").val(yjssCompany);
                                $("#fallintotable tbody tr:eq(1) td:eq(2) input").val(100);
                                $("#fallintotable tbody tr:eq(1)").find("button").remove();
                            }
                        }
                        $("select[name='Receiveid']").val(datas.result['smoid']).trigger('liszt:updated');
                        $("select[name='Signid']").val(datas.result['smoid']).trigger('liszt:updated');
                        $('.chzn-select').chosen();
                        var contacts=datas.result.contacts;
                        if(contacts.length!=0 && signaturetype=='eleccontract'){
                            $('input[name="elereceivermobile"]').val('');
                            $('input[name="originator"]').val('');
                            $('input[name="originatormobile"]').val('');
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
                            });
                            var wkcode = $("input[name='wkcode']").val();
                            var wkcontactname = $("input[name='wkcontactname']").val();
                            var wkcontactphone = $("input[name='wkcontactphone']").val();
                            if(signaturetype=='eleccontract' && wkcode){
                                str = "<option value="+wkcontactname+' data-linkname="'+wkcontactname+'" data-mobile="'+wkcontactphone+'">'+wkcontactname+"</option>";
                                $('input[name="elereceivermobile"]').val(wkcontactphone);
                            }

                            $('select[name="elereceiver"]').append(str);
                            $('select[name="elereceiver"]').trigger('liszt:updated');
                        }
                        /*if (datas.result['smoid'] && !datas.result['from']) {
                            $("select[name='Receiveid']").val(datas.result['smoid']).trigger('liszt:updated');
                            $("select[name='Signid']").val(datas.result['smoid']).trigger('liszt:updated');

                            $("#fallintotable tbody tr:eq(0)").siblings().remove();
                            $("#fallintotable tbody tr:eq(0)").after(aaaaa);
                            $("#fallintotable tbody tr:eq(1) td:eq(1) select").val(datas.result['smoid']);
                            $("#fallintotable tbody tr:eq(1) td:eq(2) input").val(100);
                            $('.chzn-select').chosen();
                            $("#fallintotable tbody tr:eq(1)").find("button").remove();
                        }else if(datas.result['smoid'] && datas.result['from']){
                            $("select[name='Receiveid']").val(datas.result['smoid']).trigger('liszt:updated');
                            $("select[name='Signid']").val(datas.result['smoid']).trigger('liszt:updated');

                            $("#fallintotable tbody tr:eq(0)").siblings().remove();
                            $("#fallintotable tbody tr:eq(0)").after(aaaaa);
                            $("#fallintotable tbody tr:eq(1) td:eq(1) select").val(datas.result['smoid']);
                            $("#fallintotable tbody tr:eq(1) td:eq(2) input").val(80);

                            $("#fallintotable tbody tr:eq(1)").after(aaaaa);
                            $("#fallintotable tbody tr:eq(2) td:eq(1) select").val(19);
                            $("#fallintotable tbody tr:eq(2) td:eq(2) input").val(20);
                            $('.chzn-select').chosen();
                            $("#fallintotable tbody tr:eq(1)").find("button").remove();
                        }*/
                        thisInstance.checkSignDempart();
                        // if(datas.result['needZizhi']=='yes'&&$("input[name='record']").val()==''&&($('input[name="isstandard"]').val()==0||$('input[name="isstandard"]').val()!=1)){
                        //     $("#fileUploadRemand").css("width",'10px')
                        //     $(".fileUploadContainerZizhi").css('display','block');
                        //     $("#needZizhi").val('yes');
                        // }else{
                        //     $(".fileUploadContainerZizhi").css('display','none');
                        //     $("#needZizhi").val('no');
                        // }
                    }else{
                        var str="";
                        var wkcode = $("input[name='wkcode']").val();
                        var wkcontactname = $("input[name='wkcontactname']").val();
                        var wkcontactphone = $("input[name='wkcontactphone']").val();
                        if(signaturetype=='eleccontract' && wkcode){
                            str = "<option value="+wkcontactname+' data-linkname="'+wkcontactname+'" data-mobile="'+wkcontactphone+'">'+wkcontactphone+"</option>";
                        }
                        $('select[name="elereceiver"]').append(str);
                        $('select[name="elereceiver"]').val(oldelereceiver);
                        $('select[name="elereceiver"]').trigger('liszt:updated');
                    }
                }
            )
        }
    },
    relatedchangeData:function(){
        var signaturetype=$('select[name="signaturetype"]').val();
        var sparams = {
            'module': 'ServiceContracts',
            'action': 'BasicAjax',
            'record': $('input[name="sc_related_to"]').val(),
            'mode': 'getsmownerid',
            'signaturetype':signaturetype
        };
        var oldelereceiver=$('select[name="elereceiver"]').val();
        AppConnector.request(sparams).then(
            function (datas) {
                if (datas.success == true) {
                    var contacts=datas.result.contacts;
                    $('select[name="elereceiver"]').empty();
                    var str="";
                    $.each(contacts,function(n,value){
                        if(n>0){
                            str += "<option value="+value.linkname+' data-linkname="'+value.linkname+'" data-mobile="'+value.mobile+'">'+value.linkname+"</option>";
                        }else{
                            str += "<option value="+value.linkname+' data-linkname="'+value.linkname+'" data-mobile="'+value.mobile+'" selected>'+value.linkname+"</option>";
                        }
                    });
                    var wkcode = $("input[name='wkcode']").val();
                    var wkcontactname = $("input[name='wkcontactname']").val();
                    var wkcontactphone = $("input[name='wkcontactphone']").val();
                    if(signaturetype=='eleccontract' && wkcode){
                        str = "<option value="+wkcontactname+' data-linkname="'+wkcontactname+'" data-mobile="'+wkcontactphone+'">'+wkcontactname+"</option>";
                        $('input[name="elereceivermobile"]').val(wkcontactphone);
                    }
                    $('select[name="elereceiver"]').append(str);
                    $('select[name="elereceiver"]').val(oldelereceiver);
                    $('select[name="elereceiver"]').trigger('liszt:updated');
                }else{
                    var str="";
                    var wkcode = $("input[name='wkcode']").val();
                    var wkcontactname = $("input[name='wkcontactname']").val();
                    var wkcontactphone = $("input[name='wkcontactphone']").val();
                    if(signaturetype=='eleccontract' && wkcode){
                        str = "<option value="+wkcontactname+' data-linkname="'+wkcontactname+'" data-mobile="'+wkcontactphone+'">'+wkcontactname+"</option>";
                        $('input[name="elereceivermobile"]').val(wkcontactphone);
                    }
                    $('select[name="elereceiver"]').append(str);
                    $('select[name="elereceiver"]').val(oldelereceiver);
                    $('select[name="elereceiver"]').trigger('liszt:updated');
                }
            }
        )
    },
    signChange:function(){
        var thisInstance=this;
        $('#EditView').on('change','select[name="Signid"]',function(){
            thisInstance.checkSignDempart();
        });
    },
    checkSignDempart:function(){
        return;
        var signedtext=$('select[name="Signid"] option:selected').text();
        var start=signedtext.indexOf('[');
        var end=signedtext.indexOf(']');
        signedtext=signedtext.substr(start+1,end);
        signedtext=signedtext.replace(']','');
        if(signedtext==''){
            //$('select[name="signdempart[]"]')[0].selectedIndex = 0;
        }else{
            $('select[name="signdempart[]"] option').each(function(){
                if($(this).text()==signedtext){
                    $(this).attr('selected','selected');
                }
            });
        }
        $('select[name="signdempart[]"]').trigger('liszt:updated');
    },
    registerEventaddfallinto:function(){
        $("#addfallinto").click(function(){
            $('#fallintotable').append(aaaaa);
            $('.chzn-select').chosen();
        });
        $('body').on('click','.deletefallinto',function(){
            $(this).closest('tr').remove()
        });
        $('body').on('blur','.scaling',function(){
            if(!isNaN($(this).val())){
                $(this).val(Number($(this).val()).toFixed(0));
            }else{
                $(this).val(0)
            }
        });
    },
    //加载ckeditor
    registerEventForCkEditor: function () {
        var form = this.getForm();
        var noteContentElement = $('textarea');
        if (noteContentElement.length > 0) {
            ckEditorInstance = new Vtiger_CkEditor_Js();
            var now_no = 1;
            //console.log(now_no);
            noteContentElement.each(function () {
                if (typeof $(this).attr('id') == 'undefined') {
                    $(this).attr('id', 'comment' + now_no);
                    //$(this).val("伙椒新添加在");
                    //ckEditorInstance.loadCkEditor($('#comment'+now_no));
                    //ckEditorInstance.loadCkEditor('comment' + now_no);
                    now_no = now_no * 1 + 1;
                }
            });
            $('#totalProductCount').val(now_no);
        }
    },
    /**
     * 编辑器加载时初始化
     * @param element
     */
    loadCkEditor:function(element){
        //$.getScript('/libraries/ueditor1_4_3-utf8-php/ueditor.all.js',function(){
        var ue = UE.getEditor(element,{
            toolbars: [['fullscreen', 'source', 'bold', 'italic', 'underline', 'cleardoc'],
            ],
            autoFloatEnabled: false,
            initialFrameWidth:'100%',
            //initialFrameHeight:200,
            autoHeightEnabled: true,
            autoFloatEnabled: false,
            elementPathEnabled:false,
            autoHeightEnabled:false,
            wordCount:false
            //readonly:true
        });
    },
    /**
     * 注册新增产品按钮
     */
    registerAddingNewProducts: function () {
        var record = $('input[name="record"]').val();

        var productid = "";
        //var productid=$('input[name="productid"]').val();
        $('input.productid').each(function () {
            if ($(this).attr("checked")) {
                productid += $(this).val() + ',';
            }
        });
        productid = productid.substring(0, productid.length - 1);
        var extraproductid = "";
        //var productid=$('input[name="productid"]').val();
        $('input.extraproductid').each(function () {
            if ($(this).attr("checked")) {
                extraproductid += $(this).val() + ',';
            }
        });
        extraproductid = extraproductid.substring(0, extraproductid.length - 1);

        if (record > 0 && (productid != '' || extraproductid !='')) {
            var urlParams = 'module=ServiceContracts&view=ListAjax';
            var servicecontractsid=$('input[name="record"]').val();
            var categoryid=$('select[name="categoryid"]').val();
            var agentid=$('input[name="agentid"]').val();
            var contract_classification=$('select[name="contract_classification"]').val();
            var params = {
                'type': 'GET',
                'dataType': 'html',
                'data': urlParams + '&mode=edit&productid=' + record+'&servicecontractsid='+servicecontractsid+'&categoryid='+categoryid+'&agents='+agentid+"&contract_classification="+contract_classification
            };
            this.loadWidgetProduct($('.widgetContainer_servicecontractproducts'), record, params);
        }
    },
    //注册删除产品事件
    registerDeleteLineItemEvent: function () {
        var thisInstance = this;
        //var lineItemTable = this.getLineItemContentsContainer();
        $('.widgetContainer_servicecontractproducts').on('click', '.deleteRow', function (e) {
            var contract_type=$('select[name="contract_type"]').val();
            if (confirm('确定删除产品吗？')) {
                //选中框取消
                var DelClass = $(this).closest('tr').attr('class'); //获取删除tr的产品id值

                var Delval = DelClass.split(' '); // alert(Delval[1]);
                var Delvalnum=$('.widgetContainer_servicecontractproducts').find('.'+Delval[1]).length;
                //当还剩一个节点时处理
                if(Delvalnum==1){
                    $('input.productid,input.extraproductid').each(function () {

                        if ($(this).val() == Delval[1]) {
                            $('.ppackage'+$(this).val()).remove();
                            $(this).attr("checked", false);
                        }
                    });
                }


                UE.delEditor('productsolution'+$(this).data('id'));
                UE.delEditor('producttext'+$(this).data('id'));
                $(this).closest('tr').remove();
                thisInstance.registerDeleteProduct();
                //更新多选框值
                //$('input.productid,input.extraproductid').iCheck('update');
                $('.entryCheckBox').iCheck({
                    checkboxClass: 'icheckbox_minimal-blue'
                });
            }
        });
    },
    //隐藏删除按钮
    checkLineItemRow: function () {
        var lineItemTable = this.getLineItemContentsContainer();
        var noRow = lineItemTable.find('.lineItemRow').length;
        if (noRow > 1) {
            lineItemTable.find('.deleteRow').show();
        } else {
            lineItemTable.find('.deleteRow').hide();
        }
    },
    //加载table
    getLineItemContentsContainer: function () {
        return $('#lineItemTab');
    },
    loadWidgets: function () {
        var thisInstance = this;
        var widgetList = jQuery('[class^="widgetContainer_"]');
        widgetList.each(function (index, widgetContainerELement) {
            var widgetContainer = jQuery(widgetContainerELement);
            thisInstance.loadWidget(widgetContainer);
        });

    },

    // 采购合同下拉框
    suppliercontractsSelect: function (suppliercontracts, selected, productid) {
        var option = '';
        suppliercontracts = suppliercontracts || [];
        for (var i=0; i<suppliercontracts.length; i++) {
            if(selected > 0 && selected==suppliercontracts[i]['suppliercontractsid']) {
                option += '<option selected value="'+suppliercontracts[i]['suppliercontractsid']+'">'+suppliercontracts[i]['contract_no']+'</option>';
            } else {
                option += '<option value="'+suppliercontracts[i]['suppliercontractsid']+'">'+suppliercontracts[i]['contract_no']+'</option>';
            }
        }
        var select = '<td nowrap><select name="suppliercontractsid['+productid+']" class="c_suppliercontracts"><option value="">请选择</option>'+option+'</select></td>';
        return select;
    },

    // 产品明细添加 供应商下来框
    productAddVendor: function (productVendorData, selected, productid) {
        var select = '<td nowrap><select class="chzn-select c_vendor" name="vendorid['+productid+']">';
        if(productVendorData) {
            select += '<option value="">请选择</option>';
            /*for(var i in productVendorData) {
                if(selected > 0 && selected==i) {
                    select += '<option selected value="'+i+'">'+productVendorData[i]+'</option>';
                } else {
                    select += '<option value="'+i+'">'+productVendorData[i]+'</option>';
                }

            }*/
            for (var i=0; i<productVendorData.length; i++) {
                if(selected > 0 && selected==productVendorData[i]['vendorid']) {
                    select += '<option selected value="'+productVendorData[i]['vendorid']+'">'+productVendorData[i]['vendorname']+'</option>';
                } else {
                    select += '<option value="'+productVendorData[i]['vendorid']+'">'+productVendorData[i]['vendorname']+'</option>';
                }
            }
        }
        select += '</select></td>';
        return select;
    },
    makeSupplierSelect: function (serviceContracts) {
        var msg = '<option value="">请选择</option>';
        for(var i=0; i<serviceContracts.length; i++) {
            msg += '<option value="'+serviceContracts[i]['suppliercontractsid']+'">'+serviceContracts[i]['contract_no']+'</option>';
        }
        return msg;
    },
    // 选择供应商
    selectVendor: function () {
        var me = this;
        $(document).on('change', '.c_vendor', function () {
            var c_vendor_this = this;
            var vendor_id = $(this).val();
            if (vendor_id) {
                var postData = {
                    "module": 'ServiceContracts',
                    "action": "BasicAjax",
                    "vendorid": vendor_id,
                    'mode': 'getSuppliercontracts'
                };
                AppConnector.request(postData).then(
                    function(data){
                        if(data.success) {
                            if(data['result']) {
                                var result = data['result'];
                                var option_html = me.makeSupplierSelect(result);
                                $(c_vendor_this).closest('td').next().find('.c_suppliercontracts').html(option_html);
                            }
                        }
                    },
                    function(error,err){

                    }
                );
            } else {
                $(c_vendor_this).closest('td').next().find('.c_suppliercontracts').html('<option value="">请选择</option>');
            }
        });
    },

    //加载产品列表--
    loadWidgetProduct: function (widgetContainer, id, params) {
        var thisInstance = this;
        var Sumrealmarketprice = 0.00;
        var Sumtotal = 0.00;
        var contentHeader = jQuery('.widget_header', widgetContainer);
        var contentContainer = jQuery('.widget_contents', widgetContainer);
        //contentContainer.html('');
        if (typeof params == 'undefined') { //点击复选框
            var urlParams = 'module=ServiceContracts&view=ListAjax';
            var servicecontractsid=$('input[name="record"]').val();
            var categoryid=$('select[name="categoryid"]').val();
            var agentid=$('input[name="agentid"]').val();
            var contract_classification=$('select[name="contract_classification"]').val();
            var params = {
                'type': 'GET',
                'dataType': 'html',
                'data': urlParams + '&productid=' + id+'&servicecontractsid='+servicecontractsid+'&categoryid='+categoryid+"&agents="+agentid+"&contract_classification="+contract_classification,
            };
        }else{
            var strproductc=params.strproductc;
        }
        contentContainer.progressIndicator({});
        AppConnector.request(params).then(
            function (data) {
                var info = eval("(" + data + ")");
                if (info.success) {
                    //合同总额
                    var html = '<table class="table table-bordered listViewEntriesTable"><thead><tr class="listViewHeaders"><th class="narrow" colspan="12">合同明细&nbsp;&nbsp; </span></th></tr></thead>' +
                        '<tbody><tr><td class="" nowrap>产品名称&nbsp;&nbsp; </td><td class="" nowrap>所属套餐&nbsp;&nbsp; </td><td class="" nowrap>额外产品&nbsp;&nbsp; </td>'+
                        '<td class="" nowrap>规格&nbsp;&nbsp; </td><td class="" nowrap>供应商&nbsp;&nbsp; </td><td class="" nowrap>采购合同&nbsp;&nbsp; </td>'+
                        '<td class="" nowrap>数量&nbsp;&nbsp; </td><td class="" nowarp>年限(月)&nbsp;&nbsp; </td><td class="" nowrap>成本价&nbsp;&nbsp; </td><td class="" nowrap>外采成本&nbsp;&nbsp; </td><td class="hide" nowrap><span class="hide">市场价</span>&nbsp;&nbsp;</td><td class="hide" nowrap><span class="hide">合同价&nbsp;&nbsp;</span></td><td class="" nowrap>备注&nbsp;&nbsp;</td><td class="hide" nowrap>规则&nbsp;&nbsp;</td><td  nowrap style="text-align: center;">删除&nbsp;&nbsp;</td></tr>';
                    var trhtml = '';
                    var upload = true;
                    var flagprice= new Array();//标识第一行或最后一行
                    var flagprice1= new Array();//标识第一行或最后一行产品在数级中是否已存在
                    var flagprice2= new Array();//标识第一行
                    var newflag=new Array();//标识套餐中重复的产品
                    var newflag1=new Array();//标识套餐中重复的产品的在数组是否已存在
                    var msgtext='产品<br><br>';//产品重复的提示信息
                    var sumrealprice=0;//当前添加产品的总成本价不是套餐则为0
                    thisInstance.productnum['p'+id]=0;
                    for (var i in info.products) {
                        thisInstance.productnum['p'+id]++;
                        var tagid='tagid'+info.products[i]['tagid'];
                        //统计产品出现的次数
                        if($.inArray(tagid,flagprice1)!=-1){
                            flagprice[tagid]=parseFloat(flagprice[tagid])+1;
                            flagprice2[tagid]=parseFloat(flagprice2[tagid])+1;
                        }else{
                            flagprice1.push(tagid);
                            flagprice[tagid]=1;
                            flagprice2[tagid]=1;
                        }
                        //产品是否重复
                        if($('.child'+ info.products[i]['productid']).size()>0){
                            if($.inArray(tagid,newflag1)!=-1){
                                newflag[tagid]=parseFloat(newflag[tagid])+1;
                            }else{
                                newflag1.push(tagid);
                                newflag[tagid]=1;
                            }
                            msgtext+='&nbsp;&nbsp;&nbsp;&nbsp;<span class="label label-warning" style="margin-bottom: 5px;">' + info.products[i]['productname'] +'</span><br>';
                        }else{
                            //产品不重复的合同总成本价
                            //是否有多规格
                            if (undefined == info.products[i]['morestand'] || 0 == info.products[i]['morestand'].length) {
                            } else {
                                var nstandard = info.products[i]['standard'] == undefined ? 0 : info.products[i]['standard'];

                                $.each(info.products[i]['morestand'], function (j, childstand) {
                                    strnew += '<option value="' + childstand['standardid'] + '" data-picklistvalue= "" ';
                                    if ((nstandard == 0 && 0 == j) || childstand['standardid'] == nstandard) {

                                        info.products[i]['realprice'] = childstand['realprice'] == null ? '0.00' : childstand['realprice'];

                                    }
                                });
                            }
                            info.products[i]['realprice']=info.products[i]['realprice']==null?'0.00':info.products[i]['realprice'];
                            sumrealprice+=parseFloat(info.products[i]['realprice']);
                        }
                    }

                    var ueid=new Array();//存放百度编辑器ID
                    var summoney = 0;//已添加产品的总价不包含套餐
                    $('input.calculate').filter('.realmarketprice').each(function () {
                        summoney += parseFloat($(this).val());
                    });
                    var total=$('input[name="total"]').val();//取得总金额
                    var packagesum = 0;//已添套餐的总成本价之和
                    if($('.tempackrealprice').size()>0){
                        $('.tempackrealprice').each(function () {
                            packagesum += parseFloat($(this).val());
                        });
                    }
                    if(info.products[i]['thepackage'] != '--'){
                        packagesum+=sumrealprice//所有套餐的成本价之和
                    }else{
                        summoney+=thisInstance.accMul(sumrealprice,2);//所在的不是套的的总合同价
                    }
                    //console.log(packagesum);
                    var currentremarkprice=0;//存放当前套餐的合同价
                    //拆套餐价
                    if(total>0){
                        var overplus=total-summoney;//剩余的总套餐价
                        if(packagesum>0 && overplus>0){
                            $('.tempackrealprice').each(function () {
                                //console.log($(this).val());
                                $('.realmarketprice'+$(this).data('id')).val(thisInstance.accMul(thisInstance.accDiv($(this).val(),packagesum),overplus).toFixed(2));
                                //console.log($('.realmarketprice'+$(this).data('id')));
                            });
                            currentremarkprice=thisInstance.accMul(thisInstance.accDiv(sumrealprice,packagesum),overplus).toFixed(2);
                        }
                    }
                    //console.log(currentremarkprice);

                    //console.log(packagesum);
                    for (var i in info.products) {

                        if (info.products[i]['productcategory'] != 'std') {
                            upload = false;
                        }
                        if (info.products[i]['producttext'] == undefined || info.products[i]['producttext'] == null) {
                            info.products[i]['producttext'] = '';
                        }
                        if (info.products[i]['newsolution'] == undefined || info.products[i]['newsolution'] == '&lt;p&gt;null&lt;/p&gt;') {
                            info.products[i]['newsolution'] = '';
                        }
                        //是不是额外产品
                        if(strproductc==undefined){
                            if(info.products[i]['isextra']==0){
                                var isextra=0;
                                var isextraname='<span class="label label-info">否</span>';
                                var strproductct='productidd';
                            }else{
                                var isextra=1;
                                var isextraname='<span class="label label-success">是</span>';
                                var strproductct='extraproductidd';
                            }
                        }else{
                            if(strproductc=='extraproductid'){
                                var isextra=1;
                                var isextraname='<span class="label label-success">是</span>';
                                var strproductct='extraproductidd';
                            }else{
                                var isextra=0;
                                var isextraname='<span class="label label-info">否</span>';
                                var strproductct='productidd';
                            }
                        }
                        var ntagid='tagid'+info.products[i]['tagid'];//定义一个标识是否显示对应套餐的价格

                        if(info.products[i]['thepackage']!='--' && flagprice[ntagid]==flagprice2[ntagid] && flagprice2[ntagid]!=newflag[ntagid]) {
                            //是套餐则显示
                            //info.products[i]['thepackage']!='--'必须是套餐
                            //flagprice[ntagid]==flagprice2[ntagid]第一个产品
                            //flagprice2[ntagid]!=newflag[ntagid]套餐所有的产品至少有一个不重复
                            trhtml += '<tr class="ppackage' + info.products[i]['tagid'] + ' ' + strproductct + '"><td nowrap colspan="11"><div class="row-fluid text-center"><span class="label label-success">'+ info.products[i]['thepackage'] +'</span></div></td></tr>';
                        }
                        if (info.products[i]['thepackage'] == '--') {
                            var fDisplay = '';
                            var calculate = 'calculate';//不是套餐加上一个class用来做计算
                            var pproductnumber = '';
                            var pagelife = '';
                            var pmarketprice = '';
                        } else {
                            var fDisplay ='readonly';//是套餐加上只读
                            var calculate = '';//不是套加上一个class用来做计算
                            var pproductnumber = 'pproductnumber' + info.products[i]['tagid'];//更新套餐子产品对应的数量class
                            var pagelife = 'pagelife' + info.products[i]['tagid'];//更新套餐子产品对应的年限class
                            var pmarketprice = 'pmarketprice' + info.products[i]['tagid'];//对应的套餐价格的class
                        }
                        //console.log(info.products[i]['pmarketprice']);
                        if (info.products[i]['pmarketprice'] == undefined || info.products[i]['pmarketprice'] == null) {
                            //info.products[i]['pmarketprice'] = info.products[i]['punit_price'];//套餐的价格
                            info.products[i]['pmarketprice'] = currentremarkprice;//套餐的价格当前拆分套餐的价格
                        }
                        info.products[i]['productnumber'] = info.products[i]['productnumber'] == undefined ? 1 : info.products[i]['productnumber'];//数量
                        info.products[i]['agelife'] = info.products[i]['agelife'] == undefined ? 12 : info.products[i]['agelife'];//年限
                        info.products[i]['ptemprealprice']=(info.products[i]['ptemprealprice']==undefined)?sumrealprice:info.products[i]['ptemprealprice'];
                        info.products[i]['ptempunit_price']=(info.products[i]['ptempunit_price']==undefined)?info.products[i]['punit_price']:info.products[i]['ptempunit_price'];
                        flagprice[ntagid]=parseFloat(flagprice[ntagid])-1;//用于标识是套餐是否是最后一个
                        if($('.child'+ info.products[i]['productid']).size()==0){
                            ueid.push(info.products[i]['productid']);
                            trhtml += '<tr class="' + info.products[i]['productcategory'] + " " + info.products[i]['tagid'] + ' ' + strproductct + '">' +
                                '<td nowrap class="child' + info.products[i]['packproduct'] + '">' + info.products[i]['productname'] + '</td>' +
                                '<td nowrap><input type="hidden" name="thepackagename[' + info.products[i]['packproduct'] + ']" value="' + info.products[i]['thepackage'] + '"><input type="hidden" name="productname[' + info.products[i]['packproduct'] + ']" value="' + info.products[i]['productname'] + '"><input type="hidden" name="thepackage[' + info.products[i]['packproduct'] + ']" value="' + info.products[i]['thepackage'] + '">' + info.products[i]['thepackage'] + '</td>' +
                                '<td nowrap><input type="hidden" name="isextra[' + info.products[i]['packproduct'] + ']" value="' + isextra + '">' + isextraname + '</td>' +
                                '<td nowrap style="width:160px;">';
                            if (undefined == info.products[i]['morestand'] || 0 == info.products[i]['morestand'].length) {
                                trhtml += '默认规格</td>';
                            } else {
                                trhtml += '<select  class="chzn-select" name="standard[' + info.products[i]['packproduct'] + ']" data-value="' + info.products[i]['tagid'] + '" data-productid="'+info.products[i]['productid']+'" data-id="' + info.products[i]['packproduct'] + '" id="standard' + info.products[i]['packproduct'] + '" style="width:150px;" > <optgroup label="规格">';
                                var strnew = '';
                                var nstandard = info.products[i]['standard'] == undefined ? 0 : info.products[i]['standard'];
                                var selectedStandardName='';
                                $.each(info.products[i]['morestand'], function (j, childstand) {
                                    strnew += '<option value="' + childstand['standardid'] + '" data-picklistvalue= "" ';
                                    if ((nstandard == 0 && 0 == j) || childstand['standardid'] == nstandard) {
                                        strnew += 'selected ';
                                        selectedStandardName=childstand['standardname'];
                                        //加默认值
                                        var realprice = info.products[i]['realprice'] = childstand['realprice'] == null ? '0.00' : childstand['realprice'];
                                        var unit_price = info.products[i]['unit_price'] = childstand['singleprice'] == null ? '0.00' : childstand['singleprice'];
                                        //var realmarketprice = info.products[i]['realmarketprice'] = childstand['singleprice'] == null ? '0.00' : childstand['singleprice'];
                                        var producttext = info.products[i]['producttext'] = info.products[i]['producttext'] == '' ? childstand['standardvalue'] == null ? '' : childstand['standardvalue'] : info.products[i]['producttext'];

                                    } else {
                                        var realprice = childstand['realprice'] == null ? '0.00' : childstand['realprice'];
                                        var unit_price = childstand['singleprice'] == null ? '0.00' : childstand['singleprice'];
                                        //var realmarketprice = childstand['singleprice'] == null ? '0.00' : childstand['singleprice'];
                                        var producttext = childstand['standardvalue'] == null ? '' : childstand['standardvalue'];
                                    }
                                    strnew += 'data-userId="" data-realprice="' + realprice + '" data-singleprice="' + unit_price + '" data-standardvalue="' + producttext + '">' + childstand['standardname'] + ' </option>';
                                });
                                strnew += '<input type="hidden" name="standardname[' + info.products[i]['packproduct'] + ']" value="'+selectedStandardName+'" />';
                                trhtml += strnew + '</optgroup> </select></td>';
                            }
                            info.products[i]['purchasemount']=info.products[i]['purchasemount']==undefined?info.products[i]['excost']:info.products[i]['purchasemount'];
                            info.products[i]['costing']=info.products[i]['costing']==undefined?info.products[i]['realprice']:info.products[i]['costing'];
                            info.products[i]['marketprice']=info.products[i]['marketprice']==undefined?thisInstance.accAdd(thisInstance.accMul(info.products[i]['realprice'],2),info.products[i]['purchasemount']).toFixed(2):info.products[i]['marketprice'];
                            info.products[i]['realmarketprice']=info.products[i]['realmarketprice']==undefined?thisInstance.accAdd(thisInstance.accMul(info.products[i]['realprice'],2),info.products[i]['purchasemount']).toFixed(2):info.products[i]['realmarketprice'];
                            var fixed = "";
                            if(info.products[i]['isfixed'] == 1){
                                fixed = "fixed";
                            }

                            // 添加供应商下拉
                            trhtml += thisInstance.productAddVendor(info.products[i]['vendor_info'], info.products[i]['vendorid'], info.products[i]['packproduct']);

                            // 添加采购合同
                            //trhtml += '<td nowrap><select name="suppliercontractsid['+info.products[i]['productid']+']" class="c_suppliercontracts"><option value="">请选择</option></select></td>';
                            trhtml += thisInstance.suppliercontractsSelect(info.products[i]['suppliercontracts_info'], info.products[i]['suppliercontractsid'], info.products[i]['packproduct']);

                            trhtml += '<td nowrap><input id="" type="number" class="input-medium productnumber' + info.products[i]['packproduct'] + ' ' + pproductnumber + '" name="productnumber[' + info.products[i]['packproduct'] + ']" data-id="' + info.products[i]['packproduct'] + '" value="' + info.products[i]['productnumber'] + '"  data-productid="'+info.products[i]['productid']+'" step="any" style="width:60px;" ' + fDisplay + '></td>' +
                                '<td nowrap><input type="hidden" name="pmarketprice[' + info.products[i]['packproduct'] + ']" value="' + info.products[i]['pmarketprice'] + '" class="' + pmarketprice + '" data-productid="'+info.products[i]['productid']+'" data-id="' + info.products[i]['packproduct'] + '"><input type="hidden" name="punit_price[' + info.products[i]['packproduct'] + ']" value="' + info.products[i]['punit_price'] + '" class="punit_price' + info.products[i]['tagid'] + '"><input type="hidden" name="prealprice[' + info.products[i]['packproduct'] + ']" value="' + (info.products[i]['prealprice']=='NO'?sumrealprice:info.products[i]['prealprice'] ) + '" class="prealprice' + info.products[i]['tagid'] + '">' +
                                '<input id="" type="number" class="'+fixed +' '+'input-medium agelife'+ info.products[i]['packproduct'] + ' ' + pagelife + '" name="agelife[' + info.products[i]['packproduct'] + ']" data-productid="'+info.products[i]['productid']+'" data-id="' + info.products[i]['packproduct'] + '" value="' + info.products[i]['agelife'] + '"  step="any" style="width:40px;" ' + fDisplay + '> 月 </td>' +
                                '<td nowrap><input type="hidden" name="realprice[' + info.products[i]['packproduct'] + ']" class="realprice' + info.products[i]['packproduct'] + '" value="' + info.products[i]['costing'] + '" data-productid="'+info.products[i]['productid']+'" data-id="' + info.products[i]['packproduct'] + '"/><input type="hidden"  class="temprealprice' + info.products[i]['packproduct'] + '" value="' + info.products[i]['realprice'] + '" data-id="' + info.products[i]['packproduct'] + '"/><span id="realprice' + info.products[i]['packproduct'] + '"> ' + info.products[i]['costing'] + '</span> </td>' +
                                '<td nowrap><input type="hidden" name="purchasemount[' + info.products[i]['packproduct'] + ']" class="purchasemount' + info.products[i]['packproduct'] + '" value="' + info.products[i]['purchasemount'] + '" data-productid="'+info.products[i]['productid']+'" data-id="' + info.products[i]['packproduct'] + '"/><input type="hidden"  class="tempexcost' + info.products[i]['packproduct'] + '" value="' + info.products[i]['excost'] + '" data-productid="'+info.products[i]['productid']+'" data-id="' + info.products[i]['packproduct'] + '"/><span id="purchasemount' + info.products[i]['packproduct'] + '" class="'+(info.products[i]['thepackage'] == '--'?'':'')+'"> ' + info.products[i]['purchasemount'] + '</span> </td>' +
                                '<td class="hide" nowrap><input name="opendate[' + info.products[i]['packproduct'] + ']" value="' + info.products[i]['opendate'] + '"><input name="closedate[' + info.products[i]['packproduct'] + ']" value="' + info.products[i]['closedate'] + '"><input type="hidden" name="unit_price[' + info.products[i]['packproduct'] + ']" class="unit_price unit_price' + info.products[i]['packproduct'] + '" value="' + info.products[i]['marketprice'] + '"><input type="hidden"  class="tempunit_price' + info.products[i]['packproduct'] + '" value="' + thisInstance.accMul(info.products[i]['realprice'],2)+ '" data-productid="'+info.products[i]['productid']+'" data-id="' + info.products[i]['packproduct'] + '" disabled/><span id="unit_price' + info.products[i]['packproduct'] + '" class="'+(info.products[i]['thepackage'] == '--'?'':'hide')+'"> ' + info.products[i]['marketprice'] + '</span></td>' +
                                '<td class="hide"> '+(info.products[i]['thepackage'] == '--'?'<input type="text" name="realmarketprice[' + info.products[i]['productid'] + ']" class="realmarketprice ' + calculate + ' realmarketprice' + info.products[i]['packproduct'] + '" data-value="' + info.products[i]['packproduct'] + '" data-errormessage="" value="' + info.products[i]['realmarketprice'] + '" style="width:90px;" ' + fDisplay + '>':'')+'<input type="hidden" name="productcomboid[' + info.products[i]['packproduct'] + ']" class="productcomboid" value="' + info.products[i]['productcomboid'] + '"></td>' +
                                '<td class="" nowrap><textarea  style="width:100%;height:100%;" class="span6" id="productsolution' + info.products[i]['productid'] + '"name="productsolution[' + info.products[i]['packproduct'] + ']">' + info.products[i]['newsolution'] + '</textarea></td>' +
                                '<td class="ueflag' + info.products[i]['packproduct'] + ' hide" nowrap><textarea style="width:100%;height:100%;" class="span6" id="producttext' + info.products[i]['productid'] + '"   name="producttext[' + info.products[i]['packproduct'] + ']">' + info.products[i]['producttext'] + '</textarea></td>' +
                                '<td style="text-align:center;"><i class="icon-trash deleteRow cursorPointer" title="删除"  data-id="' + info.products[i]['packproduct'] + '">  </i><input type="hidden" value="' + info.products[i]['packproduct'] + '" name="productids[]"> <input type="hidden" value="' + info.products[i]['productcategory'] + '" name="productcategor[]"> </td></tr>';
                            Sumrealmarketprice += parseFloat(info.products[i]['realmarketprice']);

                        }
                        if(fDisplay=='readonly' && flagprice[ntagid]==0) {
                            //是套餐则显示
                            if(flagprice2[ntagid]!=newflag[ntagid]){
                                trhtml += '<tr class="ppackage' + info.products[i]['tagid'] + ' success ' + strproductct + '"><td nowrap></td><td nowrap><span class="label label-info">' + info.products[i]['thepackage'] + '</span></td><td nowrap></td><td nowrap></td>' +
                                    '<td nowrap></td>' +
                                    '<td nowrap></td>' +
                                    '<td nowrap><input id="" type="number" class="input-medium  productnumber productnumber' + info.products[i]['tagid'] + '" data-name="' + pproductnumber + '" data-id="' + info.products[i]['tagid'] + '" value="' + info.products[i]['productnumber'] + '"  step="any" style="width:40px;" ></td>' +
                                    '<td nowrap><input id="" type="number" class="input-medium  agelife agelife' + info.products[i]['tagid'] + '"  data-name="' + pagelife + '" data-id="' + info.products[i]['tagid'] + '" value="' + info.products[i]['agelife'] + '"  step="any" style="width:40px;" >月 </td>' +
                                    '<td nowrap><input type="hidden" class="tempackrealprice" data-id="' + info.products[i]['tagid'] + '" value="' + (info.products[i]['prealprice']=='NO'?sumrealprice:info.products[i]['prealprice'] ) + '" disabled /><input type="hidden" class="realprice' + info.products[i]['tagid'] + '" value="' + info.products[i]['ptemprealprice'] + '" disabled /><span id="realprice' + info.products[i]['tagid'] + '"> ' + (info.products[i]['prealprice']=='NO'?sumrealprice:info.products[i]['prealprice'] )+ '</span> </td>' +
                                    '<td nowrap></td>' +

                                    '<td class="hide" nowrap><input type="hidden" class="unit_price unit_price' + info.products[i]['tagid'] + '" value="' + info.products[i]['ptempunit_price'] + '"><span id="unit_price' + info.products[i]['tagid'] + '"> ' + info.products[i]['punit_price'] + '</span> </td>' +
                                    '<td class="hide" ><input type="text" class="calculate realmarketprice' + info.products[i]['tagid'] + '" data-productid="'+info.products[i]['productid']+'" data-value="' + info.products[i]['tagid'] + '" data-id="' + pmarketprice + '" value="' + info.products[i]['pmarketprice'] + '" style="width:90px;" ></td>' +
                                    '<td nowrap></td>' +
                                    '<td nowrap></td>' +
                                    '</tr>';
                            }else{
                                //产品全部重复时去除该套餐的勾选
                                $('input[value="'+info.products[i]['tagid']+'"]').removeAttr("checked");
                                $('.entryCheckBox').iCheck({
                                    checkboxClass: 'icheckbox_minimal-blue'
                                });
                            }
                        }
                    }
                    //console.log(newflag1.length);
                    if(newflag1.length>0){
                        //提示信息
                        msgtext+='<br>已经添加,请在合同明细中选择';
                        var  params = {text : app.vtranslate(),
                            title : app.vtranslate(msgtext)};
                        Vtiger_Helper_Js.showPnotify(params);
                    }

                    var summoney = 0.00;
                    //	$("#no-repeatid").val(ii);  //隐藏值作为编辑唯一id
                    var trhtmlend = '<tr class="collect hide"><td>汇总:</td><td colspan="6"></td><td colspan="4"><span  style="color: red; padding-left: 20px;">合同总额：<span class="Sumtotal">' + Sumtotal + '</span>，已分配：<span class="SumViewSpan">' + Sumrealmarketprice + '</span>，未分配：<span class="ResultTotal"></span></td></tr>';
                    //判断是新增还是追加html
                    if (contentContainer.children('table').length > 0) {
                        //contentContainer.children('table').append(trhtml);
                        $('.collect').before(trhtml);

                    } else {
                        contentContainer.html(html + trhtml + trhtmlend + '</tbody></table>');
                        //加入换行防止footer上移
                        $('#EditView').append('<br><br><br><br><br><br><br><br>');
                    }
                    $('.chzn-select').chosen();
                    thisInstance.registerProductMoney();  //计算合同总价格
                    if ($("input[name='total']").val() == '') {
                        $(".Sumtotal").html(0.00);
                    } else {
                        if (!isNaN(parseFloat($("input[name='total']").val()))) {
                            $(".Sumtotal").html($("input[name='total']").val());  //获取合同总额
                        } else {
                            $(".Sumtotal").html(0.00);
                        }

                    }
                    //加载百度编辑器
                    if(ueid.length>0){
                        $.each(ueid,function(i,value){
                            //thisInstance.loadCkEditor('producttext' + value);
                            //thisInstance.loadCkEditor('productsolution' + value);
                        });
                    }

                    thisInstance.registerResultMoney();  //计算剩余的金额

                }else{
                    //没有找到产品时
                    var  params = {text : app.vtranslate(),
                        title : app.vtranslate(info.msg)};
                    Vtiger_Helper_Js.showPnotify(params);
                    //去除该产品的勾选
                    $('input[value="'+info.subid+'"]').removeAttr("checked");
                    $('.entryCheckBox').iCheck({
                        checkboxClass: 'icheckbox_minimal-blue'
                    });
                }

                contentContainer.progressIndicator({'mode': 'hide'});

            },
            function () {

            }
        );
    },
    //编辑产品加载//不可编辑
    /*loadWidgetNote : function(widgetContainer,id) {
     var thisInstance = this;
     var Sumunit_price = 0;
     var contentHeader = jQuery('.widget_header',widgetContainer);
     var contentContainer = jQuery('.widget_contents',widgetContainer);

     if(typeof params=='undefined'){ //点击复选框
     var urlParams = 'module=ServiceContracts&view=ListAjax';
     var params = {
     'type' : 'GET',
     'dataType': 'html',
     'data' : urlParams+'&productid='+id
     };
     }


     contentContainer.progressIndicator({});//弹出加载图片
     AppConnector.request(params).then(
     function(data){
     var info=eval("("+data+")");
     if(info.success){
     var html='<table class="table table-bordered listViewEntriesTable" id="productcombo'+id+'"><thead><tr class="listViewHeaders"><th class="narrow" colspan="5">合同明细&nbsp;&nbsp;<span class="SumViewSpan" style="color: red; padding-left: 20px;">合同总价格：'+Sumunit_price+'，合同总额：，剩余</span> </th></tr></thead>' +
     '<tbody><tr><td class="">产品名称&nbsp;&nbsp; </td><td class="">合同价格&nbsp;&nbsp; </td><td class="">产品规则&nbsp;&nbsp;</td><td class="">备注信息&nbsp;&nbsp;</td><td class="">删除&nbsp;&nbsp;</td></tr>';
     var trhtml='';
     var upload=true;
     for(var i in info.products){
     trhtml+='<tr class="'+info.products[i]['productcategory']+'"><td>'+info.products[i]['productname']+'</td><td><input type="text" name="unit_price[]" class="unit_price" value="'+info.products[i]['unit_price']+'"></td><td class="span6"><textarea class="span6" name="productsolution['+info.products[i]['productid']+']">'+info.products[i]['productsolution']+'</textarea></td><td class="span6"><textarea style="width:100%;height:100%;" id="producttext'+info.products[i]['productid']+'" class="span6" name="producttext['+info.products[i]['productid']+']">'+info.products[i]['producttext']+'</textarea></td><td><i class="icon-trash deleteRow cursorPointer" title="删除">  </i><input type="hidden" value="'+info.products[i]['productid']+'" name="productids[]"> <input type="hidden" value="'+info.products[i]['productcategory']+'" name="productcategory[]"> </td></tr>';
     Sumunit_price += parseFloat(info.products[i]['unit_price']) ;
     }
     contentContainer.html(html+trhtml+'</tbody></table>');
     }
     contentContainer.progressIndicator({'mode': 'hide'});
     thisInstance.registerProductMoney();//计算合同价格
     thisInstance.registerEventForCkEditor();//加载编辑器
     },
     function(){
     contentContainer.progressIndicator({'mode': 'hide'});
     }
     );
     },
     showPopup : function(params) {
     var aDeferred = jQuery.Deferred();
     var popupInstance = Vtiger_Popup_Js.getInstance();
     popupInstance.show(params, function(data){
     aDeferred.resolve(data);
     });
     return aDeferred.promise();
     },
     //每行的弹出产品选择
     lineItemPopupEventHandler : function(popupImageElement) {
     var aDeferred = jQuery.Deferred();
     var thisInstance = this;

     var referenceModule = popupImageElement.data('moduleName');
     var moduleName = app.getModuleName();
     //thisInstance.getModulePopUp(e,referenceModule);
     var params = {};
     params.view = popupImageElement.data('popup');
     params.module = moduleName;
     params.multi_select = false;
     //params.currency_id = jQuery('#currency_id option:selected').val();

     this.showPopup(params).then(function(data){
     var responseData = JSON.parse(data);
     var len = Object.keys(responseData).length;
     var contentHeader = jQuery('.widget_header','widgetContainer_1');
     var contentContainer = jQuery('.widget_contents','widgetContainer_1');
     contentContainer.progressIndicator({});
     var record=0;
     if(len==1){
     for(var id in responseData){
     record = responseData[id].id;
     }
     var params = {
     'module' : 'SalesOrder',
     'view' : 'Detail',
     'record' : record,
     'mode'   : 'getProductById',
     'relate_module' : 'Products'
     };

     AppConnector.request(params).then(
     function(data){
     contentContainer.progressIndicator({'mode': 'hide'});
     //console.log(data);
     $(popupImageElement).closest("tr").prop('outerHTML', data);
     thisInstance.registerEventForCkEditor();
     thisInstance.registerCalculationMoney();
     },
     function(error){
     contentContainer.progressIndicator({'mode': 'hide'});
     //TODO : Handle error
     }
     );
     aDeferred.resolve();
     }
     })
     return aDeferred.promise();
     },
     //绑定产品弹出框--作废
     registerProductPopup : function() {
     var thisInstance = this;
     var lineItemTable = this.getLineItemContentsContainer();
     lineItemTable.on('click','img.lineItemPopup', function(e){
     var element = jQuery(e.currentTarget);
     thisInstance.lineItemPopupEventHandler(element).then(function(data){
     var parent = element.closest('tr');
     var deletedItemInfo = parent.find('.deletedItem');
     if(deletedItemInfo.length > 0){
     deletedItemInfo.remove();
     }
     })
     });
     },*/
    //作废
    registerBasicEvents: function (container) {
        this._super(container);
        this.registerReferenceSelectionEvent(container);
        this.getuploadZzFile();
    },

    getuploadZzFile:function(){
        if($('#zizhifile').length>0){
            KindEditor.ready(function(K) {
                var uploadbutton = K.uploadbutton({
                    button : K('#uploadzizhiButton')[0],
                    fieldName : 'Zizhifile',
                    extraParams :{
                        __vtrftk:$('input[name="__vtrftk"]').val(),
                        record:$('input[name="record"]').val()
                    },
                    url : 'index.php?module=Accounts&action=FileUpload&record='+$('input[name="record"]').val(),
                    afterUpload : function(data) {
                        if (data.success ==true) {
                            $('.zizhifiledelete').remove();
                            var str='<span class="label file'+data.result['id']+'" style="margin-left:5px;">'+data.result['name']+'&nbsp;<b class="deletefile" data-class="file'+data.result['id']+'" data-id="'+data.result['id']+'" title="删除文件" style="display:inline-block;width:12px;height:12px;line-height:12px;text-align:center">x</b>&nbsp;</span><input class="ke-input-text file'+data.result['id']+'" type="hidden" name="zizhifile['+data.result['id']+']" id="zizhifile" value="'+data.result['name']+'" readonly="readonly" /><input class="file'+data.result['id']+'" type="hidden" name="attachmentsid['+data.result['id']+']" value="'+data.result['id']+'">';
                            $("#fileallzizhi").append(str);
                        } else {
                        }
                    },
                    afterError : function(str) {
                    }
                });
                uploadbutton.fileBox.change(function(e) {
                    uploadbutton.submit();
                });
                $('.fileUploadContainerZizhi').find('form').css({width:"54px"});
                $('.fileUploadContainerZizhi').find('form').find('.btn-info').css({width:"54px",marginLeft:"-15px"});
            });
        }
    },

    /**
     * 上传附件验证
     */
    registerRecordPreSaveEvent: function () {
        var thisInstance = this;
        var editViewForm = this.getForm();
        editViewForm.on(Vtiger_Edit_Js.recordPreSave,function(e,data) {
            // if($("#needZizhi").val()=='yes'&&$("input[name='record']").val()==''&&($('input[name="isstandard"]').val()==0||$('input[name="isstandard"]').val()!=1)&&$("#fileallzizhi").find('input[name^="attachmentsid["]').length==0){
            //     Vtiger_Helper_Js.showMessage({type:'error', text:'请上传客户资质认证'});
            //     e.preventDefault();
            //     return false;
            // }


            if ('无锡珍岛数字生态服务平台技术有限公司' == $('select[name="invoicecompany"]').val() && !$('select[name="sealplace"]').val()) {
                Vtiger_Helper_Js.showMessage({type:'error', text:'无锡珍岛数字生态服务平台技术有限公司必须选择用章地点'});
                e.preventDefault();
                return false;
            }


            //合同是已回收状态时，已完成复选框必须勾选
            if($("input[name='current_modulestatus']").val()=='c_recovered') {
                if(!$('#ServiceContracts_editView_fieldName_iscomplete').attr('checked')) {
                    Vtiger_Helper_Js.showMessage({type:'error', text:'已收回状态合同必须勾选已签收复选框'});
                    e.preventDefault();
                    return false;
                }

            }

            $("#fallintotable tbody tr select").attr("disabled",false);
            $("#fallintotable tbody tr input").attr("readonly",false);
            //产品套餐的子产品数是否有减少
            var signaturetype=$('select[name="signaturetype"]').val();
            var parent_contracttypeid=$('select[name="parent_contracttypeid"]').val();
            if(signaturetype=='eleccontract' && parent_contracttypeid==2){
                initCouponData();
                Vtiger_Helper_Js.showMessage({type:'error',text:'T云系列电子合同需从移动端线上服务购买处下单发起'});
                e.preventDefault();
                return false;
            }
            if($('input[name="total"]').val()<1 && $('select[name="frameworkcontract"]').val()=='no'){
                Vtiger_Helper_Js.showMessage({type:'error',text:'合同“总额”必需大于0！'});
                e.preventDefault();
                return false;
            }
            var submitflag=false;
            $.each($('input[name="productid\[\]"]:checkbox:checked'),function(key,value){
                var productid=$(value).val();
                if(!productid){
                    return false;
                }
                if(thisInstance.productnum['p'+productid]!=$('.'+productid).length && thisInstance.productnum['p'+productid] !=undefined){
                    submitflag=true;
                    return false;
                }
            });
            var checkedproductid = $('input[name="productid\[\]"]:checkbox:checked');
            // if(submitflag&&checkedproductid.length==1){
            //     var yesValue=prompt('套餐的产品数量不一致,是否确认提交!!!如果要提交请输入YES')
            //     if(yesValue!='YES'){
            //         return false;
            //     }
            // }
            var modulestatus=$('select[name="modulestatus"]').val();
            if(modulestatus=='已发放'){
                $num=thisInstance.getsumconcats();
                //var supercollar=$('select[name="supercollar"]').val();
                if($num>2/* &&  supercollar==undefined*/){
                    /*$(".tempnode").remove();
                    var options=''
                    $('select[name="assigned_user_id"] option').each(function(){
                        $(this).val();
                        options+='<option value="'+$(this).val()+'">'+$(this).text()+'</option>';
                    });
                    var tes='<tr class="tempnode"><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><font color="red">超领审核人</font></label></td><td class="fieldValue medium"><select class="chzn-select supercollar" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-name="supercollar" name="supercollar"><option value="" selected>请选超领审核人</option>'+options+'</option></select></td><td class="fieldLabel medium"></td><td class="fieldValue medium"></td></tr>';
                    $('.detailview-table').eq(0).append(tes);
                    $('.chzn-select').chosen();
                    Vtiger_Helper_Js.showMessage({type:'error',text:'该领取人已领超过"'+$num+'"份合同<br>如果继续领取请选择"超领审核人"'});*/
                    Vtiger_Helper_Js.showMessage({type:'error',text:'该领取人已领超过"'+$num+'"份合同<br>不允许再次领取'});
                    e.preventDefault();
                    return false;
                }

            }

            //判断选择的合同主体是否已被修改
            var contractattribute = $("select[name='contractattribute']").val();
            var signaturetype = $("select[name='signaturetype']").val();
            var invoicecompany = $("select[name='invoicecompany']").val();
            if(contractattribute=='customized'&&signaturetype=='eleccontract'){
                var isExistCompany = thisInstance.isExistMainCompany();
                if(!isExistCompany){
                    initCouponData();
                    Vtiger_Helper_Js.showMessage({type:'error',text:'该使用最新的合同主体公司'});
                    e.preventDefault();
                    return false;
                }
            }

            var recordid =  $('input[name="record"]').val();
            if(recordid) {
                var sc_related_to = $("input[name=sc_related_to]").val();
                var old_sc_related_to = $("input[name=old_sc_related_to]").val();
                var invoice_company = $('select[name="invoicecompany"]').val();
                var old_invoice_company = $("input[name=old_invoice_company]").val();
                if(old_sc_related_to && sc_related_to != old_sc_related_to){
                    initCouponData();
                    Vtiger_Helper_Js.showMessage({type:'error',text:"当前合同已提交发票/充值申请单，不可变更客户"});
                    e.preventDefault();
                    return false;
                }

                if(old_invoice_company && invoice_company != old_invoice_company){
                    initCouponData();
                    Vtiger_Helper_Js.showMessage({type:'error',text:"当前合同已经申请开票，合同主体不可变更"});
                    e.preventDefault();
                    return false;
                }
            }

            var iscomplete=$('#ServiceContracts_editView_fieldName_iscomplete').is(':checked');
            var servicecontractstype=$('select[name="servicecontractstype"]').val();
            //if(modulestatus=='c_complete' && servicecontractstype !='续费'){
            if(iscomplete){
                // var checkcoupon = $("input[name='check_coupon']").val();
                // var isInputCouponCode = $("input[name='is_input_coupon_code']").val();
                // var isComplete = $('#ServiceContracts_editView_fieldName_iscomplete').is(':checked');
                // if(checkcoupon==1 && isInputCouponCode==0) {
                //     var msg = {
                //         'message': "<h4>请输入券码和券码用户名</h4><hr>",
                //         'action': function () {
                //             var inputcouponcode = $('input[name="inputcouponcode"]').val();
                //             var inputcouponcodeusername = $('input[name="inputcouponcodeusername"]').val();
                //             if (inputcouponcode == '' || inputcouponcodeusername == '') {
                //                 Vtiger_Helper_Js.showMessage({type: 'error', text: '券码和券码用户名必填！'});
                //                 return false;
                //             }
                //             if (inputcouponcode.length != 18 || inputcouponcodeusername.length < 5 || inputcouponcodeusername.length > 10) {
                //                 Vtiger_Helper_Js.showMessage({type: 'error', text: '券码必须为18位纯数字，券码用户名须为5-10位纯数字！'});
                //                 return false;
                //             }
                //             return true;
                //         }
                //     };
                //     var isShow = false;
                //     Vtiger_Helper_Js.showConfirmationBox(msg).then(function (e) {
                //         var inputcouponcode = $('input[name="inputcouponcode"]').val();
                //         var inputcouponcodeusername = $('input[name="inputcouponcodeusername"]').val();
                //         $("input[name='couponcode']").val(inputcouponcode);
                //         $("input[name='couponname']").val(inputcouponcodeusername);
                //         $("input[name='is_input_coupon_code']").val(1);
                //         $("#servicecontractsub").trigger("click");
                //         return false;
                //     });
                //     var str = '<div class="control-group"><div class="control-group"><br/><div class="controls" ><span class="" style="font-size: 16px;">券码:</span><input type="number" name="inputcouponcode" id="inputcouponcode" value=""  class="span8" style="font-size: 18px;border: none;border-bottom: 1px solid #ccc;box-shadow: none !important;"></div></div><div class="control-group"><div class="controls" ><span class="" style="font-size: 16px;">券码用户名:</span><input type="number" name="inputcouponcodeusername" id="inputcouponcodeusername" value=""  class="span8" style="font-size: 18px;border: none;border-bottom: 1px solid #ccc;box-shadow: none !important;"></div></div>';
                //
                //     $('.modal-dialog').css("marginTop", "200px");
                //     $('.modal-body .bootbox-close-button').after('<button type="button" class="showhelp close" style="margin-top: -10px;margin-right:10px;" data-title="<span style=\'color:#169BD5;\'>操作说明</span>" data-content=\'<div><p><span style="font-weight:700;">撤回并发送：</span><span style="font-weight:400;">撤回后立即发送</span></p><p><span style="font-weight:700;">撤回修改合同内容后发送：</span><span style="font-weight:400;">撤回后，将会跳转到合同表单编辑页，重新修改合同内容后发送（合同类型、购买类型、合同模板ERP限制不可变更）</span></p><p><span style="font-weight:700;">仅撤回：</span><span style="font-weight:400;">撤回合同不再发送，合同置为作废状态，如需修改合同，可以重新新建电子合同</span></p></div>\'>?</button>');
                //     $('.bootbox-body').append(str);
                //     if (!isShow) {
                //         e.preventDefault();
                //         return false;
                //     }
                //     return;
                // }

                var returndate=$('input[name="Returndate"]').val();
                var receivedate=$('input[name="Receivedate"]').val();
                var signdates=$('input[name="signdate"]').val();
                if(returndate=='' || receivedate=='' || signdates==''){
                    initCouponData();
                    Vtiger_Helper_Js.showMessage({type:'error',text:"签收状态,日期必填!!"});
                    e.preventDefault();
                    return false;
                }
                var invoicecompany=$('select[name="invoicecompany"]').val();
                if(invoicecompany==''){
                    initCouponData();
                    Vtiger_Helper_Js.showMessage({type:'error',text:"签收状态,合同主体必填!!"});
                    e.preventDefault();
                    return false;
                }
                /*if(servicecontractstype =='续费' || servicecontractstype =='upgrade'){
                    return true;
                }*/
                /*if(servicecontractstype =='续费'){
                    return true;
                }*/
                var frameworkcontract=$('select[name="frameworkcontract"]').val();
                if(frameworkcontract=='no'){
                    var SETTLEMENT_CLAUSE=$('.SETTLEMENT_CLAUSE')
                    if(SETTLEMENT_CLAUSE.length>0){
                        var total=$('input[name="total"]').val();
                        var mreceiveableamount=0;
                        $.each($('input[name^="mreceiveableamount["]'),function(key,value){
                            mreceiveableamount+=parseFloat($(value).val());
                        });
                        var total=total.replace(/,/g,'');
                        if(total!=mreceiveableamount){
                            initCouponData();
                            Vtiger_Helper_Js.showMessage({type:'error',text:"合同金额与应收金额不等!!"});
                            e.preventDefault();
                            return false;
                        }
                    }
                }

                var parent_contracttypeid=$('select[name="parent_contracttypeid"]').val();
                //var checkedproduct='837,426322,426335,426337,426340,426342,474817,565988,566004,631769,631612,631761';
                //T云产品都需要和合同做验证 以下注释掉 gaocl add 2018/06/12
                //var checkedproduct='426335,426337,565988,426340,426342,566004,474817,426322,837,631769,631612,631761,2115444,787685,2113422,603314';
                var productid=[];
                var extraproductid=[];
                var ajax_extraproductidnum=[];
                var ajax_productidpack=[];
                var ajax_packageyear=[];
                var ajax_extraproductidyear = [];
                var str = '';
                //var checkedflag=false;
                $.each($('input[name="productid[]"]').serializeArray(), function(i, field){
                    productid.push(field.value);
                    if($('.productnumber'+field.value).size()==0){
                        ajax_productidpack.push(field.value+':'+$('.productnumber0DZE'+field.value).val());
                        str += "<input type='hidden' name='ajax_productidpack[]' value='"+field.value+':'+$('.productnumber0DZE'+field.value).val()+"'/>";
                        ajax_packageyear.push(field.value+':'+$('.agelife0DZE'+field.value).val());
                        str += "<input type='hidden' name='ajax_packageyear[]' value='"+field.value+':'+$('.agelife0DZE'+field.value).val()+"'/>";
                    }else{
                        ajax_productidpack.push(field.value+':'+$('.productnumber'+field.value).val());
                        str += "<input type='hidden' name='ajax_productidpack[]' value='"+field.value+':'+$('.productnumber'+field.value).val()+"'/>";
                        ajax_packageyear.push(field.value+':'+$('.agelife'+field.value).val());
                        str += "<input type='hidden' name='ajax_packageyear[]' value='"+field.value+':'+$('.agelife'+field.value).val()+"'/>";
                    }

                    /*var tempckeckedstr=field.value+',';
                    if(checkedproduct.indexOf(tempckeckedstr)>=0){
                        checkedflag=true;
                    }*/
                });
                $.each($('input[name="extraproductid[]"]').serializeArray(), function(i, field){
                    extraproductid.push(field.value);
                    if($('input[name="productnumber\['+field.value+'DZE'+field.value+'\]"]').size()==0){
                        ajax_extraproductidnum.push(field.value+':'+$('.extraproductidd input[name="productnumber\[0DZE'+field.value+'\]"]').val());
                        str += "<input type='hidden' name='ajax_extraproductidnum[]' value='"+field.value+':'+$('.extraproductidd input[name="productnumber\[0DZE'+field.value+'\]"]').val()+"'/>";
                        ajax_extraproductidyear.push(field.value+':'+$('.extraproductidd input[name="agelife\[0DZE'+field.value+'\]"]').val());
                        str += "<input type='hidden' name='ajax_extraproductidyear[]' value='"+field.value+':'+$('.extraproductidd input[name="agelife\[0DZE'+field.value+'\]"]').val()+"'/>";
                    }else{
                        ajax_extraproductidnum.push(field.value+':'+$('.extraproductidd input[name="productnumber\['+field.value+'DZE'+field.value+'\]"]').val());
                        str += "<input type='hidden' name='ajax_extraproductidnum[]' value='"+field.value+':'+$('.extraproductidd input[name="productnumber\['+field.value+'DZE'+field.value+'\]"]').val()+"'/>";
                        ajax_extraproductidyear.push(field.value+':'+$('.extraproductidd input[name="agelife\['+field.value+'DZE'+field.value+'\]"]').val());
                        str += "<input type='hidden' name='ajax_extraproductidyear[]' value='"+field.value+':'+$('.extraproductidd input[name="agelife\['+field.value+'DZE'+field.value+'\]"]').val()+"'/>";
                    }

                });
                $("#ajaxanalogy").append(str);
                var contract_type=$('select[name="contract_type"]').val();
                //if(checkedflag){
                // if(parent_contracttypeid == '2'||contract_type=='SaaS新零售系统'){
                var iscomplete=$('#ServiceContracts_editView_fieldName_iscomplete').is(':checked');
                var contractbuytype=$('input[name="contractbuytype"]').val();
                if(contract_type!='T云WEB版' && contract_type!='T云院校版' && contract_type!='T云集团版' && contract_type !='T云系列补充协议（非标）' && iscomplete && (contractbuytype == 'upgrade' || contractbuytype == 'renew' || contractbuytype == 'againbuy' || contractbuytype == 'degrade')) {
                    //判断是否查询原合同信息
                    var oldcustomerid = $("#txt_oldcustomerid").val();
                    var newoldcustomerid = $("#txt_newoldcustomerid").val();
                    var oldcontractcode = $("#txt_oldcontractcode").val();
                    var oldoldproductid = $("#txt_oldproductid").val();
                    if(oldoldproductid == ""){
                        initCouponData();
                        Vtiger_Helper_Js.showMessage({type:'error',text:"在原合同信息栏,请先通过T云账号查询原版本信息!"});
                        e.preventDefault();
                        return false;
                    }
                    //判断原合同编号是否输入
                    /*var oldcontractcode_input = $("#txt_oldcontractcode_input").val();
                    if(oldcontractcode_input == ""){
                        Vtiger_Helper_Js.showMessage({type:'error',text:"在原版本信息栏,输入原合同编号!"});
                        e.preventDefault();
                        return false;
                    }*/
                    //判断输入的原合同编号是否一致
                    /*var oldcontractcode = $("#txt_oldcontractcode").val();
                    if(oldcontractcode_input != oldcontractcode){
                        Vtiger_Helper_Js.showMessage({type:'error',text:"在原版本信息栏,输入原合同编号和客户端合同编号不一致!"});
                        e.preventDefault();
                        return false;
                    }*/
                    //判断客户是否一致
                    var customerid=$('input[name="sc_related_to"]').val();

                    if((oldcustomerid && oldcustomerid !=''&& oldcustomerid != customerid) && (newoldcustomerid && newoldcustomerid!='' && newoldcustomerid != customerid) ){
                        initCouponData();
                        Vtiger_Helper_Js.showMessage({type:'error',text:"客户不一致!"});
                        // Vtiger_Helper_Js.showMessage({type:'error',text:"客户和原版本客户不一致!"});
                        e.preventDefault();
                        return false;
                    }
                }

                var productcomboid=[];
                $.each($('input[name*="productcomboid"]').serializeArray(), function(i, field){
                    if(field.value >0 && $.inArray(field.value,productcomboid)){
                        productcomboid.push(field.value);
                    }
                });

                var ajax_productnumber=[];
                $.each($('input[name*="productnumber"]'), function(){
                    ajax_productnumber.push($(this).data("productid") +":"+ $(this).val());
                });
                var ajax_agelife=[];
                $.each($('input[name*="agelife"]'), function(){
                    ajax_agelife.push($(this).data("productid") +":"+ $(this).val());
                });

                var agelife=[];
                $.each($('input[name*="agelife"]').serializeArray(), function(i, field){
                    agelife.push(field.value);
                });
                var total=$('input[name="total"]').val();
                var hasOrder=$("input[name='hasOrder']").val();

                var couponcode = $("input[name='couponcode']").val();
                var couponname = $("input[name='couponname']").val();
                var isjoinactivity = $('#ServiceContracts_editView_fieldName_isjoinactivity').is(':checked');
                var contract_classification=$('select[name="contract_classification"]').val();

                var params={};
                params.data = {
                    "module": "ServiceContracts",
                    "action": "ChangeAjax",
                    "mode": "checkProductAYear",
                    "record": $('input[name="record"]').val(),
                    "contract_no": $('input[name="contract_no"]').val(),
                    "parent_contracttypeid": parent_contracttypeid,
                    "sc_related_to": $('input[name="sc_related_to"]').val(),
                    'sc_related_to_display':$('input[name="sc_related_to_display"]').val(),
                    "modulestatus": 1,
                    "productcomboid": productcomboid,
                    "productid": productid,
                    "extraproductid":extraproductid,
                    "ajax_productnumber":ajax_productnumber,
                    "ajax_agelife":ajax_agelife,
                    "ajax_productidpacknum":ajax_productidpack,
                    "ajax_extraproductidnum":ajax_extraproductidnum,
                    "agelife":agelife,
                    "tyun_account":$("#txt_oldtyun_account").val(),
                    "contract_type":contract_type,
                    "total":total,
                    "contractbuytype":contractbuytype,
                    "servicecontractstype":$('select[name="servicecontractstype"]').val(),
                    "ajax_packageyear":ajax_packageyear,
                    "ajax_extraproductidyear":ajax_extraproductidyear,
                    "contract_classification":contract_classification,
                    "hasorder":hasOrder,
                    "isjoinactivity":isjoinactivity?1:0
                    // "couponname":couponname,
                    // "couponcode":couponcode
                };
                params.async=false;
                var ajaxflag=false;
                AppConnector.request(params).then(
                    function (data) {
                        var result = data.result;
                        if(!result.success){
                            /* var msg = 'T云合同,客户,产品与年限必需和客户端一致,请保持一致后再进行操作';
                             if(servicecontractstype =='upgrade'){
                                 msg = 'T云升级合同不能领取激活码,请确认后再操作';
                             }*/
                            $("input[name='couponcode']").val('');
                            $("input[name='couponname']").val('');
                            $("input[name='is_input_account_and_total']").val(0);
                            // $("input[name='is_input_coupon_code']").val(0);
                            console.log($("input[name='is_input_account_and_total']").val());
                            Vtiger_Helper_Js.showMessage({type:'error',text:result.msg});
                            ajaxflag=true;
                            e.preventDefault();
                            return false;
                        }else{
                            var check_account_and_total = $("input[name='check_account_and_total']").val();
                            var is_input_account_and_total = $("input[name='is_input_account_and_total']").val();
                            var isComplete = $('#ServiceContracts_editView_fieldName_iscomplete').is(':checked');
                            console.log(is_input_account_and_total);
                            if(check_account_and_total==1 && is_input_account_and_total==0) {
                                var msg = {
                                    'message': "<h4>请输入纸质合同的客户名和合同金额，进行最终确认</h4><hr>",
                                    'action': function () {
                                        var checkaccount = $.trim($('input[name="checkaccount"]').val());
                                        var checktotal = $('input[name="checktotal"]').val();
                                        if (checkaccount == '' || checktotal == '') {
                                            Vtiger_Helper_Js.showMessage({type: 'error', text: '客户名和合同金额必填！'});
                                            return false;
                                        }
                                        var sc_related_to_display = $.trim($("#sc_related_to_display").val());
                                        var ServiceContracts_editView_fieldName_total = $("#ServiceContracts_editView_fieldName_total").val();
                                        if(sc_related_to_display!=checkaccount){
                                            Vtiger_Helper_Js.showMessage({type: 'error', text: '输入的客户名和客户不一致,请修改！'});
                                            return false;
                                        }

                                        if(parseFloat(ServiceContracts_editView_fieldName_total)!=parseFloat(checktotal)){
                                            Vtiger_Helper_Js.showMessage({type: 'error', text: '输入的合同金额和总额不一致,请修改！'});
                                            return false;
                                        }

                                        return true;
                                    }
                                };
                                var isShow = false;
                                Vtiger_Helper_Js.showConfirmationBox(msg).then(function (e) {
                                    $("input[name='is_input_account_and_total']").val(1);
                                    $("#servicecontractsub").trigger("click");
                                    // return false;
                                    isShow=true;
                                });
                                var str = '<div class="control-group"><div class="control-group"><br/><div class="controls" ><span style="color: red">*</span><span class="" style="font-size: 16px;"> 客户名:</span><input type="text" name="checkaccount" id="checkaccount" value=""  class="span8" style="font-size: 18px;border: none;border-bottom: 1px solid #ccc;box-shadow: none !important;"></div></div><div class="control-group"><div class="controls" ><span style="color: red">*</span><span class="" style="font-size: 16px;"> 合同金额:</span><input type="text" name="checktotal" id="checktotal" value=""  class="span8" style="font-size: 18px;border: none;border-bottom: 1px solid #ccc;box-shadow: none !important;"></div></div>';

                                $('.modal-dialog').css("marginTop", "200px");
                                $('.modal-body .bootbox-close-button').after();
                                $('.bootbox-body').append(str);
                                if (!isShow) {
                                    e.preventDefault();
                                    return false;
                                }
                            }

                        }
                    },
                    function (error) {
                    });
                if(ajaxflag){
                    initCouponData();
                    e.preventDefault();
                    return false;
                }
                // }
                var params={};
                params.data = {
                    "module": "ServiceContracts",
                    "action": "ChangeAjax",
                    "mode": "getAccountStatus",
                    "record": $('input[name="record"]').val(),
                    "sc_related_to": $('input[name="sc_related_to"]').val()
                };
                params.async=false;
                var ajaxflag=false;
                AppConnector.request(params).then(
                    function(data){
                        if(data.result){
                            initCouponData();
                            Vtiger_Helper_Js.showMessage({type:'error',text:'客户名称和补充协议中不一致!'});
                            ajaxflag=true;
                            e.preventDefault();
                            return false;
                        }
                    },
                    function (error) {
                    });
                if(ajaxflag){
                    initCouponData();
                    e.preventDefault();
                    return false;
                }
            }
            var scalingtotal = parseInt("0");
            $(".scaling").each(function(){
                    scalingtotal += parseInt(Number($(this).val()));
                }
            );
            var sideagreement = $("input[name='sideagreement']").val();
            if(scalingtotal!==100 && !sideagreement){
                initCouponData();
                Vtiger_Helper_Js.showMessage({type:'error',text:'分成比例之和必须为100%'});
                e.preventDefault(); //阻止提交事件先注释
            }
            if(signaturetype=='eleccontract' && thisInstance.eleccontractSubmit){
                var contractattribute=$('select[name="contractattribute"]').val();
                if(contractattribute=='customized'){
                    thisInstance.customizedContractSubmit();
                }else{
                    thisInstance.standContractSubmit();
                }
                e.preventDefault();
                return false;
            }
        });
    },

    //wangibn 2015年5月8日 星期五 新建时货币类型自动改为人民币，货币类型下拉项跟金额前缀同步
    totalprefix: function () {
        if (!$('input[name="record"]').val()) {
            var type = $('select[name="currencytype"]').val('人民币');
            $("select[name='currencytype']").next('div').children('a').children('span').text('人民币');
        }
        $('select[name="currencytype"]').on('change', function () {
            var type = $('select[name="currencytype"]').val();
            if (type) {
                $('#ServiceContracts_editView_fieldName_total').prev().text(type);
            }
        })




    },
    /**
     * 获取T云web分类
     */
    contractTypeEvent: function () {
        var thisInstance=this;
        $('form').on('change','select[name="contract_type"]', function () {
            if(thisInstance.isElecLoadData()){
                return false;
            }
            // $('select[name="categoryid"]').next().remove();
            // $('select[name="categoryid"]').remove();
            $("#categoryid").empty();
            // $(this).siblings().not('#'+$(this).attr('id')+'_chzn').remove();
            if ($('select[name="contract_type"]').val() != "") {
                var contract_typeName = $('select[name="contract_type"]').val();  //请求异常处理，对字符进行编码
                var hasOrder=$("input[name='hasOrder']").val();
                if(contract_typeName!='T云WEB版'){
                    return;
                }
                if(hasOrder==1){
                    // return;
                }
                var params = {
                    'type': 'GET',
                    'dataType': 'html',
                    'data': 'module=ServiceContracts&action=ChangeAjax&mode=getTyunWebCategory'
                };
                AppConnector.request(params).then(
                    function (data) {

                        var selejson= $.parseJSON(data);
                        $("#categoryid").empty();
                        // $('select[name="categoryid"]').remove();
                        var selectprodcut='<select class="chzn-select" name="categoryid" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"> <option value="">选择一个选项</option>';
                        if (data == null || selejson.data.length == 0) {
                            selectprodcut+='</select>';
                        } else {
                            var option=''
                            $.each(selejson.data,function(i,val){
                                option+='<option value='+val.id+'>'+val.title+'</optio>';
                            });
                            selectprodcut+=option+'</select>';

                        }
                        $("#categoryid").append(selectprodcut);
                        // $('select[name="categoryid"]').siblings().not('#'+$('select[name="categoryid"]').attr('id')+'_chzn').remove();
                        // $('select[name="contract_type"]').next().after(selectprodcut);
                        $('.chzn-select').chosen();
                    },
                    function (error) {
                    });
                //合同是T-云系列或者TSITE系列自动选择
                if(contract_typeName==2||contract_typeName==9||contract_typeName==12){
                    var frameworkSelectObject=$('select[name="frameworkcontract"]');
                    frameworkSelectObject.val('no').trigger('change');
                    frameworkSelectObject.next().find("a span").html('否');
                }
            }
        })
    },
    /*
     * 2015-05-26 合同类型下拉显示所对应的产品信息contract_type额外产品
     */
    contractProductsEvent: function () {
        //编辑合同时，合同类型为“TSITE续费合同“，把域名空间邮箱续费转移到自定义信息（奇葩需求）
        if ($("input[name='isEdit']").val()=='1' && $('select[name="contract_type"]').val()=='TSITE续费合同') {
            var pro = $("input[name='eproducttypename[362103]']").parent().parent();
            if (pro) {
                $('.PriorityName').append(pro);
            }
        }
        var thisInstance=this;
        $('form').on('change','select[name="contract_type"],select[name="productcategory"]', function () {
            if($(this).attr('name')=='productcategory'){
                //额外产品取
                /*
                var contract_typeName = encodeURIComponent($(this).val());  //请求异常处理，对字符进行编码
                contract_typeName='getextaproducts&contract_typeName=' + contract_typeName;
                var strclassname='.extraproductidname';
                var tablefield='extraproductid';
                var delproducts=$('.extraproductidd').find('input[name^="productnumber"]');
                thisInstance.deleteUE(delproducts);
                $('.extraproductidd').html(''); //清空额外产品
                $('.extraproductidd').remove(); //清空额外产品*/
            }else if($(this).attr('name')=='contract_type'){
                if(thisInstance.isElecLoadData()){
                    return false;
                }
                $("#div_old_contract_no").empty().remove();

                //$('.widgetContainer_servicecontractproducts').html(''); //清空产品
                var delproducts=$('.productidd').find('input[name^="productnumber"]');
                thisInstance.deleteUE(delproducts);
                $('.productidd').html(''); //清空合同类型产品
                $('.productidd').remove(); //清空合同类型产品
                var record = $('input[name="record"]').val();
                var thisVar=$(this).val();

                //20201215 兼容新零售，使其和T云web端一样
                if(record&&thisVar=='SaaS新零售系统'){
                    thisVar='T云WEB版';
                }
                var contract_typeName = encodeURIComponent(thisVar);  //请求异常处理，对字符进行编码
                contract_typeName='getproducts&contract_typeName=' + contract_typeName+'&record='+record;
                var strclassname='.PriorityName';
                var tablefield='productid';
                // 这里为了兼容PHP端代码 添加一个变量
                contract_typeName += '&type=serviceContractsEdit';
                var parent_contracttypeid = $("select[name='parent_contracttypeid']").val();
                contract_typeName += "&parent_contracttypeid="+parent_contracttypeid;
                var servicecontractstype = $("select[name='servicecontractstype']").val();
                contract_typeName += '&servicecontractstype='+servicecontractstype;
                var category=$("select[name='categoryid']").val();
                if(category!=undefined && thisVar=='T云WEB版'){
                    contract_typeName += '&category='+category;
                }

                // var agentid = $("#agentid").val();
                var agentid = $("input[name='agentid']").val();
                var contract_classification = $("select[name='contract_classification']").val();
                if(agentid && contract_classification=='tripcontract'){
                    contract_typeName += '&agents='+agentid;
                }
                if(contract_classification){
                    contract_typeName += '&contract_classification='+contract_classification;
                }

                //加载原合同信息
                thisInstance.loadOldContractInfo();
                thisInstance.getTPList();
            }

            thisInstance.registerDeleteProduct();//更新一下是否有客外产品或是合同类型产品需要计算
            if ($(this).val() != "") {
                var thisp=$(this);
                var params = {
                    'type': 'GET',
                    'dataType': 'html',
                    'data': 'module=ServiceContracts&action=ChangeAjax&mode='+ contract_typeName
                };
                $('.productidd').remove();
                $('.extraproductidd').remove();
                AppConnector.request(params).then(
                    function (data) {
                        var t_data = data;

                        var t_info = eval("(" + t_data + ")");

                        var isstandard = t_info.isstandard;
                        data = t_info.product_list;

                        if (isstandard == '1') { //非标合同
                            $('#ServiceContracts_editView_fieldName_isstandard').attr('checked', true);
                            $('input[name=isstandard]').val(1);
                        } else {
                            $('#ServiceContracts_editView_fieldName_isstandard').attr('checked', false);
                            $('input[name=isstandard]').val(0);
                        }



                        if (data == null || data.length == 0) {
                            $(strclassname).html('<font color=red>没有相对应的产品信息!</font>');
                            var otherList=t_info.otherproduct_list;
                            var otherproductHTML='';
                            if(t_info.otherproducttype==1){
                                $('.extraproductidname').html(otherList);
                            }else{
                                if(otherList.length>0){
                                    $.each(otherList,function(key,value){
                                        otherproductHTML += '<div style="line-height: 30px;float: left; float: left; width: 290px; border: 1px solid  rgba(57, 15, 40, 0.18); margin: 2px;  border-radius: 5px;"><label class="checkbox"><input type="checkbox"  value="' + value['productid'] + '" id="extraproductid'+ value['productid']+'" name="extraproductid[]" data-name="extraproductid" data-istyun="' + value['istyun'] + '" data-pid="' + value['parentid'] + '" data-tid="' + value['tyunproductid'] + '" class="entryCheckBox extraproductid">' + value['productname'] + '</label></div>';
                                    });
                                    $('.extraproductidname').html(otherproductHTML);
                                }else{
                                    $('.extraproductidname').html('<font color="red">没有相对应的产品信息!</font>');
                                }
                            }
                        } else {
                            $(strclassname).html('');
                            //var info = eval("(" + data + ")");
                            var info = data;
                            var PriorityNameHTML = "";
                            if (info.length == 0) {
                                $(strclassname).html('<font color=red>没有相对应的产品信息!</font>');
                            } else {
                                var searchproducts='';
                                for (var i = 0; i < info.length; i++) {
                                    if(!info[i]&& typeof(info[i])!="undefined"&&info[i]!=0){
                                        continue;
                                    }
                                    PriorityNameHTML += '<div style="line-height: 30px;float: left;width: 260px; border: 1px solid  rgba(57, 15, 40, 0.18); margin: 2px;  border-radius: 5px;padding-bottom:5px;"><label class="checkbox inline"><input type="checkbox"  value="' + info[i]['productid'] + '" id="'+tablefield+'s'+ info[i]['productid']+'" name="'+tablefield+'[]" data-name="'+tablefield+'" data-istyun="' + info[i]['istyun'] + '" data-pid="' + info[i]['parentid'] + '" data-tid="' + info[i]['tyunproductid'] + '" class="entryCheckBox '+tablefield+'"> ' + info[i]['productname'] + '<input type="hidden" name="producttypename[' + info[i]['productid'] + ']" value="' + info[i]['productname'] + '"/></label></div>';
                                    searchproducts+='<option value='+tablefield+'s'+info[i]['productid'] + '>'+info[i]['productname']+'</optio>';
                                }
                                if(tablefield=='extraproductid'){
                                    PriorityNameHTML='<div style="float: left;width: 290px;   margin: 2px;"><select class="chzn-select" id="searchproduct" > <option value="">选择要查找的产品名称</option>'+searchproducts+'</select></div>'+PriorityNameHTML;
                                }
                                $(strclassname).html(PriorityNameHTML);
                            }
                            var otherList=t_info.otherproduct_list;
                            var otherproductHTML='';
                            if(t_info.otherproducttype==1){
                                $('.extraproductidname').html(otherList);
                            }else{
                                if(otherList.length>0){
                                    $.each(otherList,function(key,value){
                                        otherproductHTML += '<div style="line-height: 30px;float: left;width: 260px; border: 1px solid  rgba(57, 15, 40, 0.18); margin: 2px;  border-radius: 5px;padding-bottom:5px;"><label class="checkbox inline"><input type="checkbox"  value="' + value['productid'] + '" id="extraproductid'+ value['productid']+'" name="extraproductid[]" data-name="extraproductid" data-istyun="' + value['istyun'] + '" data-pid="' + value['parentid'] + '" data-tid="' + value['tyunproductid'] + '" class="entryCheckBox extraproductid"> ' + value['productname'] + '</label></div>';
                                    });
                                    $('.extraproductidname').html(otherproductHTML);
                                }
                            }


                        }
                        $('.entryCheckBox').iCheck({
                            checkboxClass: 'icheckbox_minimal-blue'
                        });
                        $('.chzn-select').chosen();
                        //编辑合同时，合同类型为“TSITE续费合同“，把域名空间邮箱续费转移到自定义信息（奇葩需求）
                        if ($("input[name='isEdit']").val()=='1' && thisVar=='TSITE续费合同') {
                            var pro = $("input[name='eproducttypename[362103]']").parent().parent();
                            if (pro) {
                                $(strclassname).append(pro);
                            }
                        }
                    },
                    function (error) {
                        $(strclassname).html('<font color=red>获取产品信息失败!</font>');
                    });
            }
        })
    },
    /**
     * steel 类型分类调用产品列表
     */
    parentProductsEvent: function () {
        var thisInstance=this;
        $('form').on('change','select[name="parent_contracttypeid"]', function () {
            if(thisInstance.isElecLoadData()){
                return false;
            }
            $(this).siblings().not('#'+$(this).attr('id')+'_chzn').remove();
            if ($('select[name="parent_contracttypeid"]').val() != "") {
                var contract_typeName = $('select[name="parent_contracttypeid"]').val();  //请求异常处理，对字符进行编码
                var params = {
                    'type': 'GET',
                    'dataType': 'html',
                    'data': 'module=ServiceContracts&action=ChangeAjax&mode=getproductlist&parent_contracttypeid=' + contract_typeName
                };
                AppConnector.request(params).then(
                    function (data) {
                        var selejson= $.parseJSON(data);
                        $('select[name="contract_type"]').remove();
                        var selectprodcut='<select class="chzn-select" name="contract_type" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"> <option value="">选择一个选项</option>';
                        if (data == null || selejson.result.length == 0) {
                            selectprodcut+='</select>';
                        } else {
                            var option=''
                            $.each(selejson.result,function(i,val){
                                option+='<option value='+val+'>'+val+'</optio>';
                            });
                            selectprodcut+=option+'</select>';

                        }

                        $('select[name="parent_contracttypeid"]').siblings().not('#'+$('select[name="parent_contracttypeid"]').attr('id')+'_chzn').remove();
                        $('select[name="parent_contracttypeid"]').parent().append(selectprodcut);
                        $('.chzn-select').chosen();
                    },
                    function (error) {
                    });
                //合同是T-云系列或者TSITE系列自动选择
                if(contract_typeName==2||contract_typeName==9||contract_typeName==12){
                    var frameworkSelectObject=$('select[name="frameworkcontract"]');
                    frameworkSelectObject.val('no').trigger('change');
                    frameworkSelectObject.next().find("a span").html('否');
                }
            }
        })
    },
    /**
     * 2015-05-26 复选产品的时候执行的事件
     */
    checkedProduct: function () {
        var instance = this;
        $('.PriorityName,.extraproductidname').on('ifChecked', 'input', function (e) { //事件绑定
            //异常处理如果为空了，必须加入div
            if ($('.widgetContainer_servicecontractproducts').children().html() == null) {
                $('.widgetContainer_servicecontractproducts').html('<div class="widget_contents"> </div>');
            }
            var istyun=$(this).attr('data-istyun');
            if ($(this).attr("checked")) { //如果为选中状态执行
                var urlParams = 'module=ServiceContracts&view=ListAjax';
                var servicecontractsid=$('input[name="record"]').val();
                var categoryid=$('select[name="categoryid"]').val();
                var agentid=$('input[name="agentid"]').val();
                var contract_classification=$('select[name="contract_classification"]').val();
                var params = {
                    'type': 'GET',
                    'dataType': 'html',
                    'data': urlParams + '&productid=' + $(this).val()+'&servicecontractsid='+servicecontractsid+'&istyun='+istyun+"&categoryid="+categoryid+"&agents="+agentid+"&contract_classification="+contract_classification,
                    'strproductc':$(this).data('name')
                };
                //判断一下是否有产品已经选中
                if(/*$('.'+$(this).val()).size()>0|| */$('.child'+$(this).val()).size()>0){

                    $(this).removeAttr("checked");
                    $('.entryCheckBox').iCheck({
                        checkboxClass: 'icheckbox_minimal-blue'
                    });
                    var  params = {text : app.vtranslate(),
                        title : app.vtranslate('该产品已经添加,请在合同明细中选择')};
                    Vtiger_Helper_Js.showPnotify(params);
                    return;
                }

                instance.loadWidgetProduct($('.widgetContainer_servicecontractproducts'), $(this).val(), params);
            } else {  //取消
                if (confirm('确定删除产品，包含套餐下所有的产品吗？')) {
                    var val = $(this).val();
                    $("." + val).remove();
                    instance.registerDeleteProduct();

                } else {//取消删除，选中状态存在
                    $(this).attr("checked", "checked");
                }
            }
        }).on('ifUnchecked', 'input', function () { //事件绑定
            //异常处理如果为空了，必须加入div

            // if (confirm('确定删除产品，包含套餐下所有的产品吗？')) {
            var val = $(this).val();
            var delproducts=$('.'+val).find('input[name^="productnumber"]');
            instance.deleteUE(delproducts);//删除UE编辑器
            $("." + val).remove();
            $(".ppackage" + val).remove();
            instance.registerDeleteProduct();

            // } else {//取消删除，选中状态存在
            //     $(this).attr("checked", "checked");
            //     $('.entryCheckBox').iCheck({
            //         checkboxClass: 'icheckbox_minimal-blue'
            //     });
            // }

        })
    },
    /*
     *
     * 删除产品时，计算合同价格
     * */
    registerDeleteProduct: function () {
        this.autoAllocation();

        var summoney = 0.00;
        $('input.calculate').each(function () {
            summoney += parseFloat($(this).val());
        });
        $(".SumViewSpan").html(summoney.toFixed(2));
        var total=$('input[name="total"]').val();
        total=total==''?'0.00':total.replace(/\,/g,'');//除空和去除数字间的逗号
        var ResultTotal = parseFloat(total) - summoney;
        ResultTotal = isNaN(ResultTotal) ? 0.00 : ResultTotal;
        $('.ResultTotal').html(ResultTotal.toFixed(2));  //剩余金额
        $('.Sumtotal').html(total);
        $('.Sumtotal').addClass('label label-info');
        $('.ResultTotal').addClass('label label-warning');


    },

    /*初次进来时，计算合同价格
     *
     */
    registerProductMoney: function () {
        var summoney = 0.00;
        $('input.calculate').each(function () {
            summoney += parseFloat($(this).val());
        });

        $(".SumViewSpan").html(summoney.toFixed(2));
        $(".SumViewSpan").addClass('label label-info');
    },
    /*
     * 鼠标离开时触发，累加合同价格
     *
     */

    priceBlur: function () {
        var priceBlurEvent = this;
        var summoney = 0.00;//focus
        var sumTotal = 0.00;
        var foucsValue = 0.00;
        /*
        $('form').on('focus', '.realmarketprice', function () {
            if (!isNaN($(this).val()) && !isNaN($(".SumViewSpan").html())) {
                foucsValue = $(".SumViewSpan").html() - $(this).val();
                $(".SumViewSpan").html(foucsValue.toFixed(2));
                priceBlurEvent.registerResultMoney();
            } else {
                $(".SumViewSpan").html(0);
            }

        })*/
        $('form').on('blur', '.calculate', function () {
            if (!isNaN($(this).val()) && !isNaN($(".SumViewSpan").html())) {
                sumTotal = parseFloat($(this).val() * 1 + $(".SumViewSpan").html() * 1);
                $(".SumViewSpan").html(sumTotal.toFixed(2));
                priceBlurEvent.registerResultMoney();
            } else {
                $(".SumViewSpan").html(0);
            }
        })


    },
    /**
     * 套餐的自动分配
     */
    autoAllocation:function(){
        var thisInstance=this;

        var summoney = 0;//已添加产品的总价不包含套餐
        $('input.calculate').filter('.realmarketprice').each(function () {
            summoney += parseFloat($(this).val());
        });
        var total=$('input[name="total"]').val().replace(/\,/,'');//取得总金额
        var packagesum = 0;//已添套餐的总成本价之和

        $('.tempackrealprice').each(function () {
            //packagesum += parseFloat($(this).val());
            var npackagesum=0;//重新分配的单个套餐的总成本
            //console.log($(this).data('id'));
            $('.pmarketprice'+$(this).data('productid')).each(function(){
                npackagesum+=parseFloat($('.realprice'+$(this).data('productid')).val());
            });
            $('#realprice'+$(this).data('productid')).text(npackagesum.toFixed(2));
            $('.realprice'+$(this).data('productid')).val(npackagesum.toFixed(2));
            $('.prealprice'+$(this).data('productid')).val(npackagesum.toFixed(2));//单价
            packagesum+=npackagesum;
        });
        //拆套餐价
        if(total>0){

            var overplus=total-summoney;//剩余的总套餐价
            if(packagesum>0 && overplus>0){

                $('.tempackrealprice').each(function () {
                    var ageanumber=thisInstance.accMul($('.productnumber'+$(this).data('id')).val(),thisInstance.accDiv($('.agelife'+$(this).data('id')).val(),12));

                    $('.realmarketprice'+$(this).data('id')).val(thisInstance.accMul(thisInstance.accDiv($('.realprice'+$(this).data('id')).val(),packagesum),overplus).toFixed(2));
                    $('.pmarketprice'+$(this).data('id')).val(thisInstance.accMul(thisInstance.accDiv($('.realprice'+$(this).data('id')).val(),packagesum),overplus).toFixed(2));
                    $('.punit_price'+$(this).data('id')).val($('#unit_price'+$(this).data('id')).text());//更新对应的套餐价格
                    //$('.prealprice'+$(this).data('id')).val($('.realprice'+$(this).data('id')).val());//更新对应的套餐价格

                });

            }else{
                $('.tempackrealprice').each(function () {
                    $('.realmarketprice'+$(this).data('id')).val('0.00');
                    $('.pmarketprice'+$(this).data('id')).val('0.00');

                });
            }
        }

    },
    /**
     *套餐自动计算
     */
    autoCalc:function(id,pid,flag){

        var thisInstance=this;
        var ageanumber=thisInstance.accMul($('.productnumber'+id).val(),thisInstance.accDiv($('.agelife'+id).val(),12));
        var realprice=thisInstance.accMul(ageanumber,$('.temprealprice'+id).val()).toFixed(2);
        var exprice=thisInstance.accMul(ageanumber,$('.tempexcost'+id).val());//外产成本
        $("#realprice"+id).text(realprice);//成本价
        $(".realprice"+id).val(realprice);//成本价
        $("#purchasemount"+id).text(exprice.toFixed(2));//成本价
        $(".purchasemount"+id).val(exprice.toFixed(2));//成本价
        var unit_price =thisInstance.accAdd(thisInstance.accMul($('.tempunit_price'+id).val(),ageanumber),exprice).toFixed(2)
        $("#unit_price"+id).text(unit_price);//市场价
        $(".unit_price"+id).val(unit_price);//市场价

        if(flag==1){//当为套餐时
            //thisInstance.autoAllocation();
            /*if($('.realprice'+pid).val()==0){
                $('.realmarketprice'+id).val('0.00');
            }else{
                var Percentrealprice =thisInstance.accDiv($('.temprealprice'+id).val(),$('.realprice'+pid).val());
                var punit_price =thisInstance.accMul(Percentrealprice,$('.realmarketprice'+pid).val()).toFixed(2)
                $('.realmarketprice'+id).val(punit_price);
            }*/

        }else{
            $('.realmarketprice'+id).val(unit_price);
        }
        thisInstance.autoAllocation();

    },
    /**
     *steel年限和数量的校验
     */
    checkintnumber:function(){
        var thisInstance=this;
        $('form').on('change blur keyup','input[name^="productnumber"],input[name^="agelife"],.productnumber,.agelife',function(){
            $(this).val($(this).val().replace(/[^0-9]/g,''));//只能输入数字
            $(this).val($(this).val().replace(/^0/g,1));//若是0直接改为1
            $(this).val($(this).val().replace(/[\d]{8,}/g,1));//若是0直接改为1
            if($(this).val()==''){
                $(this).val(1);
            }
            if($(this).data('name')!=undefined){
                var pid=$(this).data('id');//
                $('.'+$(this).data('name')).not('.fixed').val($(this).val());//更新对应的年限或是数量
                var ageanumber=thisInstance.accMul($('.productnumber'+$(this).data('id')).val(),thisInstance.accDiv($('.agelife'+$(this).data('id')).val(),12));
                var realprice=thisInstance.accMul(ageanumber,$('.realprice'+$(this).data('id')).val());
                var markprice=thisInstance.accMul(ageanumber,$('.unit_price'+$(this).data('id')).val()).toFixed(2);

                //$('.punit_price'+$(this).data('id')).val(markprice);//更新对应的套餐价格
                //$('.realmarketprice'+$(this).data('id')).val(markprice)//更新对应的套餐合同价价格
                $('.pmarketprice'+$(this).data('id')).val( $('.realmarketprice'+$(this).data('id')).val());//存放套餐的合同价
                //console.log(realprice);
                //$('.prealprice'+$(this).data('id')).val(realprice.toFixed(2));//单价
                $('#realprice'+$(this).data('id')).text(realprice.toFixed(2));//单价显示
                $('#unit_price'+$(this).data('id')).text(markprice);//成本价显示

                $.each($('.'+$(this).data('name')),function(i,value){
                    thisInstance.autoCalc($(value).data('id'),pid,1);

                });
            }else{

                thisInstance.autoCalc($(this).data('id'));
            }
            thisInstance.registerDeleteProduct();
        }).css("ime-mode", "disabled"); //CSS设置输入法不可用*/

    },
    /**
     * 格式化数字允许输入的位数
     * @param _this
     */
    checkremarkprice:function(_this){
        /*_this.val(_this.val().replace(/[^0-9.]/g,''));//只能输入数字小数点
        _this.val(_this.val().replace(/^0\d{1,}/g,''));//不能以0打头的整数
        _this.val(_this.val().replace(/^\./g,''));//不能以小数点打头
        _this.val(_this.val().replace(/[\d]{13,}/g,''));//超出长度全部清空
        _this.val(_this.val().replace(/\.\d{3,}$/g,''));//小数点后两位如果超过两位则将小数后的所有数清空包含小数点
        _this.val(_this.val().replace(/\.\d{0,2}\.+$/g,''));//小数点后两位后再再加.*/
        _this.val(_this.val().replace(/[^0-9.]|^0\d{1,}|^\.|[\d]{13,}|\.\d{3,}|\.\d{0,2}\.+/g,''));//小数点后两位后再再加.
    },

    inputcalcpack:function(_this){
        var pnid=_this.data('value');
        var thisInstance=this;

        $('.'+_this.data('id')).val(_this.val());
        //console.log($('.'+_this.data('id')).size());
        $.each($('.'+_this.data('id')),function(i,value){
            if($('.realprice'+pnid).val()==0){
                /*var sumrealprice=0;
                $('.pmarketprice'+pnid).each(function(i,val){
                    //console.log($(value).data('id'));
                    if($(val).data('id')!=pnid && $('.temprealprice'+$(val).data('id')).val()!=0){
                        sumrealprice+=parseFloat($('.temprealprice'+$(val).data('id')).val());
                    }
                });
                if(sumrealprice!=0){

                    var Percentrealprice =thisInstance.accDiv($('.temprealprice'+$(value).data('id')).val(),sumrealprice);
                    var punit_price =thisInstance.accMul(Percentrealprice,$('.realmarketprice'+pnid).val()).toFixed(2)
                    $('.realmarketprice'+$(value).data('id')).val(punit_price);
                }*/
                $('.realmarketprice'+$(value).data('id')).val('0.00');
            }else{
                var Percentrealprice =thisInstance.accDiv($('.temprealprice'+$(value).data('id')).val(),$('.realprice'+pnid).val());
                $('.realmarketprice'+$(value).data('id')).val(thisInstance.accMul(Percentrealprice,$('.realmarketprice'+pnid).val()).toFixed(2));
            }
        });
    },
    //格式化输入只能转入数字或小数保留两位
    inputnumberchange : function(){
        var thisInstance=this;
        $('form').on('keyup','input[name="total"],.calculate',function(){
            thisInstance.checkremarkprice($(this));
            var arr=$(this).val().split('.');//只有一个小数点

            if(arr.length>2){
                if(arr[1]==''){
                    $(this).val(arr[0]);
                }else{
                    $(this).val(arr[0]+'.'+arr[1]);
                }
            }
            //clearTimeout(idss);
            if($(this).val()==''  && $(this).attr('name')!='total'){//当为空时处理
                var pthis=this;
                /*var idss=setTimeout(function(){if($(pthis).val()==''){
                    //var ageanumber=thisInstance.accMul($('.productnumber'+$(pthis).data('value')).val(),thisInstance.accDiv($('.agelife'+$(pthis).data('value')).val(),12));
                    $(pthis).val(thisInstance.accMul(2,$('.realprice'+$(pthis).data('value')).val()).toFixed(2));
                    thisInstance.inputcalcpack($(pthis));
                    thisInstance.registerDeleteProduct();}
                },1000);*/
                return;

            }
            if($(this).data('id')!=undefined){
                thisInstance.inputcalcpack($(this));
            }
            thisInstance.registerDeleteProduct();
        }).on('blur','input[name="total"],.calculate',function(){  //失焦事件
            thisInstance.checkremarkprice($(this));
            var arr=$(this).val().split('.');//只有一个小数点当小数后没有数字时清除小数点
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
            if($(this).val()=='' && $(this).attr('name')!='total'){
                //var ageanumber=thisInstance.accMul($('.productnumber'+$(this).data('value')).val(),thisInstance.accDiv($('.agelife'+$(this).data('value')).val(),12));
                $(this).val(thisInstance.accMul(2,$('.realprice'+$(this).data('value')).val()).toFixed(2));
            }
            //更新对应的套餐价
            if($(this).data('id')!=undefined){
                thisInstance.inputcalcpack($(this));
                //$('.'+$(this).data('id')).val($(this).val());
            }
            thisInstance.registerDeleteProduct();
        }).on('focus','input[name="total"],.calculate',function(){  //失焦事件

            if(parseInt($(this).val())==0 && $(this).attr('name')!='total'){
                //var ageanumber=thisInstance.accMul($('.productnumber'+$(this).data('value')).val(),thisInstance.accDiv($('.agelife'+$(this).data('value')).val(),12));
                $(this).val('');
            }

        }).css("ime-mode", "disabled"); //CSS设置输入法不可用


    },
    /**
     * 乘法运算解决Js相乘的问题
     */

    accMul:function(arg1,arg2){
        var m=0,s1=arg1.toString(),s2=arg2.toString();
        try{m+=s1.split(".")[1].length}catch(e){}
        try{m+=s2.split(".")[1].length}catch(e){}
        return Number(s1.replace(".",""))*Number(s2.replace(".",""))/Math.pow(10,m)
    },
    /**
     * 除法运算相除JS问题
     * @param arg1除数
     * @param arg2被除数
     * @returns {number}
     */
    accDiv:function(arg1,arg2){
        var t1=0,t2=0,r1,r2;
        try{t1=arg1.toString().split(".")[1].length}catch(e){}
        try{t2=arg2.toString().split(".")[1].length}catch(e){}
        with(Math){
            r1=Number(arg1.toString().replace(".",""))
            r2=Number(arg2.toString().replace(".",""))
            return (r1/r2)*pow(10,t2-t1);
        }
    },
    /**
     * 加法运算
     * @param arg1
     * @param arg2
     * @returns {number}
     */
    accAdd:function(arg1,arg2){
        var r1,r2,m;
        try{r1=arg1.toString().split(".")[1].length}catch(e){r1=0}
        try{r2=arg2.toString().split(".")[1].length}catch(e){r2=0}
        m=Math.pow(10,Math.max(r1,r2))
        return (arg1*m+arg2*m)/m
    },
    /*
     * 计算剩余金额
     *
     */
    registerResultMoney: function () {
        var Sumtotala = $(".Sumtotal").html();
        var SumViewSpanb = $(".SumViewSpan").html();
        Sumtotala = Sumtotala.replace(/,/g, '');
        SumViewSpanb = SumViewSpanb.replace(/,/g, '');
        var ResultTotal = parseFloat(Sumtotala) - parseFloat(SumViewSpanb);
        $(".ResultTotal").html(ResultTotal.toFixed(2));

    },
    getsumconcats:function(){
        //var owerid=$('select[name="Receiveid"]').val();
        var owerid=$('select[name="assigned_user_id"]').val();
        if(owerid>0){
            var params={};
            params.data = {
                "module": "ServiceContracts",
                "action": "ChangeAjax",
                "mode": "getservicecontracts_reviced",
                "ownerid": owerid
            };
            params.async=false
            var num;
            AppConnector.request(params).then(
                function (data) {
                    num=data.result[0];
                },
                function (error) {
                });
            return num;
        }
    },
    /**
     * 删除UE编辑器所占用的ID
     * @param delproducts
     */
    deleteUE:function(delproducts){
        if(delproducts.length==0){
            return;
        }else{
            $.each(delproducts,function(i,value){

                UE.delEditor('productsolution'+$(value).data('id'))
                UE.delEditor('producttext'+$(value).data('id'))
            });
        }

    },
    /*
     * 鼠标移入移开计算合同价格
     *
     */
    registerRecordSaveEvent: function () {
        var summoney = 0.00;//focus
        var sumTotal = 0.00;
        var foucsValue = 0.00;
        $('input.calculate').live('focus', 'input', function () {
            foucsValue = $(".SumViewSpan").html() - $(this).val();
            $(".SumViewSpan").html(foucsValue.toFixed(2));
        })
        $('input.calculate').live('blur', 'input', function () {
            sumTotal = parseFloat($(this).val() * 1 + $(".SumViewSpan").html() * 1);
            $(".SumViewSpan").html(sumTotal.toFixed(2));

        })
    },
    /*
     *判断合同金额剩余0时，才可提交
     * */
    registerResultEvent: function (form) {
        return;
        var thisInstance = this;
        if (typeof form == 'undefined') {
            form = this.getForm();
        }

        form.on(Vtiger_Edit_Js.recordPreSave, function (e, data) {
            /*var ResultTotal = $('.ResultTotal').html();
             if($('input[name="total"]').val()==''&& $(".SumViewSpan").html() > 0){   //总额为空时
             var total= 0.00;   //总额
             var ResultTotalNew = parseFloat(total) - $(".SumViewSpan").html();  //总额-合同总价格
             if(ResultTotalNew<0){
             var  params = {text : '合同剩余金额必须为0',
             title :'合同剩余金额必须为0,不能为负值，请核对！'
             }
             }
             Vtiger_Helper_Js.showPnotify(params);
             e.preventDefault();
             }
             if( ResultTotal!=0.00 && ResultTotal!= null) {
             if(ResultTotal<0){
             var  params = {text : '合同剩余金额必须为0',
             title :'合同剩余金额必须为0,不能为负值，请核对！'
             }
             }else{
             var  params = {text : '合同剩余金额必须为0',
             title :'请把合同金额分配给相应的合同价格用完！'
             }
             }
             Vtiger_Helper_Js.showPnotify(params);
             e.preventDefault();

             }else{
             return true;
             }
             e.preventDefault();*/
        })
    },

    /*
     *
     * 总额（货币类型），鼠标离开事件  *（合同总额改变  总价格不变，剩余金额改变）
     * */
    /*
    registerTotalSaveEvent: function () {
        var total = 0.00;
        var ResultTotal = 0.00;
        $('form').on('blur keyup', 'input[name="total"]', function () {
            console.log($(this).val());
            if (!isNaN($(this).val())) {
                if ($(this).val() == "") {
                    total = 0.00;
                } else {
                    total = parseFloat($(this).val());
                }
                $(".Sumtotal").html(total.toFixed(2)); //合同总额
                var SumViewSpan = $(".SumViewSpan").html(); //总价格
                ResultTotal = total - SumViewSpan;
                $('.ResultTotal').html(ResultTotal.toFixed(2));  //剩余金额
                $('.Sumtotal').addClass('label label-info');
                $('.ResultTotal').addClass('label label-warning');
            } else {
                $(".Sumtotal").html(0.00); //合同总额
                $('.ResultTotal').html(0.00);  //剩余金额
            }

        })
    },*/
    /**
     * 产品多规格更改触发
     * @param container
     */
    setprouctdprice:function(){
        var thisInstance=this;
        $('form').on('change','select[name^="standard"]', function () {
            var newid=$(this).data('id');
            var pnewid=$(this).data('value');
            $('.temprealprice'+newid).val($(this).find("option:selected").data('realprice'));//设置成本价格
            $('.tempunit_price'+newid).val($(this).find("option:selected").data('singleprice'));//设置成本价格

            if($('.realmarketprice'+pnewid).data('id')==undefined){
                thisInstance.autoCalc(newid);
            }else{
                thisInstance.autoCalc(newid,pnewid,1);
            }
            thisInstance.registerDeleteProduct();

        });

    },
    /**
     * 查找额外产品
     * 已不用了
     */
    findproduct:function(){
        $('form').on('change','#searchproduct',function(){
            if($(this).val()!=''&& $('#'+$(this).val()).attr('checked')==undefined){
                $('#'+$(this).val()).attr("checked", "checked");
                $('.entryCheckBox').iCheck({
                    checkboxClass: 'icheckbox_minimal-blue'
                });
                //模拟选中事件
                $('#'+$(this).val()).trigger('ifChecked');
            }

        });
    },
    /**
     *当这个两值改变时清除超领担保人节点
     **/
    onchangesuper:function(){

        $('form').on('change','select[name="assigned_user_id"],select[name="modulestatus"]',function(){
            $(".tempnode").remove();
        });
    },
    init: function() {
        $('.customDateField').each(function(key,value){
            var fieldinfo=$(value).data('fieldinfo');
            var params = jQuery.extend({
                format: "yyyy-mm-dd",
                language:  'zh-CN',
                autoclose: true,
                todayBtn: false,
                todayHighlight:true,
                pickerPosition: "bottom-left"
            },fieldinfo);
            $(value).datepicker(params);
        });
        return;
        $('#ServiceContracts_editView_fieldName_isstandard').attr('disabled', 'disabled');

        if ($('#ServiceContracts_editView_fieldName_isautoclose').size() > 0){
            $('#ServiceContracts_editView_fieldName_isautoclose').attr('disabled', 'disabled');
        }
    },

    /**
     * 签收事件处理
     */
    registerContractIscomplete:function() {
        var thisInstance = this;
        $("#ServiceContracts_editView_fieldName_iscomplete").change(function() {
            thisInstance.loadOldContractInfo();
        });
        thisInstance.loadOldContractInfo();
    },
    /**
     * 加载原合同信息 gaocl add 2018/06/14
     */
    loadOldContractInfo:function () {
        var thisInstance = this;
        //升级、续费和另购
        var iscomplete=$('#ServiceContracts_editView_fieldName_iscomplete').is(':checked');
        var contract_type=$('select[name="contract_type"]').val();

        var contractbuytype =$('input[name="contractbuytype"]').val();
        var contractbuyName = "";
        if(contractbuytype == 'buy'){
            contractbuyName = "【购买合同】";
        }else if(contractbuytype == 'upgrade'){
            contractbuyName = "【升级合同】";
        }else if(contractbuytype == 'degrade'){
            contractbuyName = "【降级合同】";
        }else if(contractbuytype == 'renew'){
            contractbuyName = "【续费合同】";
        }else if(contractbuytype == 'againbuy'){
            contractbuyName = "【另购合同】";
        }else{
            contractbuyName = "【未在移动端下单】";
        }
        var contract_type=$('select[name="contract_type"]').val();
        var parent_contracttypeid=$('select[name="parent_contracttypeid"]').val();
        $('#EditView .cls_oldcontract_info').empty().remove();
        if(iscomplete && parent_contracttypeid==2 && contract_type !='T云WEB版' && (contractbuytype == 'upgrade' || contractbuytype == 'degrade' || contractbuytype == 'renew' || contractbuytype == 'againbuy')) {
            var str='<div class="cls_oldcontract_info"><br><table class="table table-bordered equalSplit detailview-table"><thead><tr><th class="blockHeader" colspan="3"><img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;"><img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">&nbsp;&nbsp;原版本信息<span style="color: red">【查询并核对原版本信息】</span></th><th style="text-align: right;">购买类型:<span style="color: red">'+contractbuyName+'</span></th></tr></thead><tbody style="display: table-row-group;">';

            str=str+'<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span>T云账号</label></td><td><div class="input-append"><input id="txt_tyun_account" type="text" placeholder = "请输入T云账号"  value="" ><span class="add-on" style="cursor: pointer" id="btn_searchTyunBuyServiceInfo">查询</span></div></td><td></td><td></td></tr>';
            str=str+'<tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">客户名称</label></td><td><div class="input-append"><label id="oldcustomername_display" class="muted pull-right marginRight10px"></label></div></td>' +
                '<td class="fieldLabel medium"><label class="muted pull-right marginRight10px">合同编号</label></td><td><div class="input-append"><label id="oldcontractcode_display" class="muted pull-right marginRight10px"></label></div></td></tr>';

            str=str+'<tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">版本</label></td><td><div class="input-append"><label id="oldproductname_display" class="muted pull-right marginRight10px"></label></div></td>' +
                '<td class="fieldLabel medium"><label class="muted pull-right marginRight10px">到期时间</label></td><td><div class="input-append"><label id="oldexpiredate_display" class="muted pull-right marginRight10px"></label></div></td></tr>';

            //str=str+'<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> <span class="redColor">*</span>原合同编号</label></td><td><div class="input-append"><input id="txt_oldcontractcode_input" name="oldcontractcode_input" type="text" placeholder = "请输入原合同编号"  value="" ></div></td><td></td><td></td></tr>';

            str=str+'<input type="hidden" id="txt_oldcontractid" name="oldcontractid">';
            str=str+'<input type="hidden" id="txt_oldproductid" name="oldproductid">';
            str=str+'<input type="hidden" id="txt_oldcontractcode" name="oldcontractcode">';
            str=str+'<input type="hidden" id="txt_oldcustomerid" name="oldcustomerid">';
            str=str+'<input type="hidden" id="txt_newoldcustomerid" name="newoldcustomerid">';
            str=str+'<input type="hidden" id="txt_oldtyun_account" name="tyun_account">';

            str=str+'</tbody></table></div>';

            $('#EditView .LBL_SERVICE_CONTRACT_INFORMATION').after(str);
            //通过查询原合同信息
            $("#btn_searchTyunBuyServiceInfo").click(function(){
                thisInstance.searchTyunBuyServiceInfo();
            });
            $('#txt_tyun_account').bind('keypress',function(event){
                if(event.keyCode == "13"){
                    thisInstance.searchTyunBuyServiceInfo();
                }
            });
        }
    },
    /**
     * 根据T云账号查询原合同信息
     * @returns {*}
     */
    searchTyunBuyServiceInfo:function(){
        var thisInstance = this;
        var tyun_account = $("#txt_tyun_account").val();
        if($.trim(tyun_account) == '') {
            Vtiger_Helper_Js.showMessage({type:'error',text:'请输入T云账号'});
            return;
        }

        //清空数据
        $("#oldcustomername_display").text("");
        $("#oldcontractcode_display").text("");
        $("#oldproductname_display").text("");
        $("#oldexpiredate_display").text("");
        //隐藏控件赋值
        $("#txt_oldcontractid").val("");
        $("#txt_oldproductid").val("");
        $("#txt_oldcontractcode").val("");
        $("#txt_oldcustomerid").val("");
        $("#txt_newoldcustomerid").val("");
        $("#txt_oldtyun_account").val("");

        var contractbuytype =$('input[name="contractbuytype"]').val();
        if(contractbuytype == 'upgrade' || contractbuytype == 'degrade') {
            var strclassname = '.PriorityName';
            $(strclassname).html('<font color=red>没有相对应的产品信息!</font>');
        }
        var params={};
        params.data = {
            "module": "ServiceContracts",
            "action": "ChangeAjax",
            "mode": "searchTyunBuyServiceInfo",
            "tyun_account": $.trim(tyun_account),
            "record": $("input[name='record']").val()
        };
        params.async=false
        AppConnector.request(params).then(
            function (data) {
                if (data.result.success) {
                    var dataList = data.result.buyList;
                    if (dataList.length == 0) {
                        Vtiger_Helper_Js.showMessage({type:'error',text:'未查询到原合同信息,请确认'});
                        return;
                    } else {
                        var buydata = dataList[0];
                        if(buydata.customername==null || buydata.customername==""){
                            $("#oldcustomername_display").html("<span style='color: red;'>无客户</span>");
                        }else{
                            $("#oldcustomername_display").text(buydata.customername);
                        }
                        if(buydata.contractname==null || buydata.contractname==""){
                            $("#oldcontractcode_display").html("<span style='color: red;'>无原合同</span>");
                        }else{
                            $("#oldcontractcode_display").text(buydata.contractname);
                        }
                        $("#oldproductname_display").text(buydata.productname);
                        $("#oldexpiredate_display").text(buydata.expiredate == '' ? '--' : buydata.expiredate);

                        //隐藏控件赋值
                        $("#txt_oldcontractid").val(buydata.contractid);
                        $("#txt_oldproductid").val(buydata.productid);
                        $("#txt_oldcontractcode").val(buydata.contractname);
                        $("#txt_oldcustomerid").val(buydata.customerid);
                        $("#txt_newoldcustomerid").val(data.result.customerid);
                        $("#txt_oldtyun_account").val($.trim(tyun_account));

                        if(contractbuytype == 'upgrade' || contractbuytype == 'degrade') {
                            thisInstance.loadCustomProductList(buydata.productid);
                        }
                    }
                }else{
                    Vtiger_Helper_Js.showMessage({type:'error',text:data.result.message});
                    return;
                }
            },
            function (error) {
            });
    },

    //加载自定义产品信息
    loadCustomProductList:function (tyunproductid) {
        var record = $('input[name="record"]').val();
        var contractbuytype =$('input[name="contractbuytype"]').val();
        var contract_typeName = encodeURIComponent($("select[name='contract_type']").val());  //请求异常处理，对字符进行编码
        contract_typeName='getproducts&contract_typeName=' + contract_typeName + "&buytype="+contractbuytype;
        var strclassname='.PriorityName';
        var tablefield='productid';
        // 这里为了兼容PHP端代码 添加一个变量
        contract_typeName += '&type=serviceContractsEdit&p_productid='+tyunproductid +'&record='+record;

        var params = {
            'type': 'GET',
            'dataType': 'html',
            'data': 'module=ServiceContracts&action=ChangeAjax&mode='+ contract_typeName
        };
        AppConnector.request(params).then(
            function (data) {
                var t_data = data;
                var t_info = eval("(" + t_data + ")");
                data = t_info.product_list;
                if (!data || data.length == 0) {
                    $(strclassname).html('<font color=red>没有相对应的产品信息!</font>');
                } else {
                    $(strclassname).html('');
                    //var info = eval("(" + data + ")");
                    var info = data;
                    var PriorityNameHTML = "";
                    if (info.length == 0) {
                        $(strclassname).html('<font color=red>没有相对应的产品信息!</font>');
                    } else {
                        var searchproducts = '';
                        for (var i = 0; i < info.length; i++) {
                            if (!info[i] && typeof(info[i]) != "undefined" && info[i] != 0) {
                                continue;
                            }
                            PriorityNameHTML += '<div style="line-height: 30px;float: left; float: left; width: 290px; border: 1px solid  rgba(57, 15, 40, 0.18); margin: 2px;  border-radius: 5px;"><label class="checkbox"><input type="checkbox"  value="' + info[i]['productid'] + '" id="' + tablefield + 's' + info[i]['productid'] + '" name="' + tablefield + '[]" data-name="' + tablefield + '" data-istyun="' + info[i]['istyun'] + '" data-pid="' + info[i]['parentid'] + '" data-tid="' + info[i]['tyunproductid'] + '" class="entryCheckBox ' + tablefield + '">' + info[i]['productname'] + '</label></div>';
                            searchproducts += '<option value=' + tablefield + 's' + info[i]['productid'] + '>' + info[i]['productname'] + '</optio>';
                        }
                        if (tablefield == 'extraproductid') {
                            PriorityNameHTML = '<div style="float: left;width: 290px;   margin: 2px;"><select class="chzn-select" id="searchproduct" > <option value="">选择要查找的产品名称</option>' + searchproducts + '</select></div>' + PriorityNameHTML;
                        }
                        $(strclassname).html(PriorityNameHTML);
                    }
                }
                $('.entryCheckBox').iCheck({
                    checkboxClass: 'icheckbox_minimal-blue'
                });
                $('.chzn-select').chosen();
            })
    },

    getElecContractTable:function(){
        var thisInstance=this;
        var oldsignaturetypevale=$('select[name="signaturetype"]').val();
        $('form').on('change','select[name="signaturetype"]',function(){
            var thisVale=$(this).val();
            if(thisVale!=oldsignaturetypevale) {
                thisInstance.isonbeforeunload=false;
                if(thisVale=='eleccontract'){
                    window.location.href='/index.php?module=ServiceContracts&view=Edit&signaturetypehref=eleccontract';
                }else{
                    window.location.href='/index.php?module=ServiceContracts&view=Edit';
                }
            }
        });
        $('select[name="eleccontracttplid"]').parent('td').append('<button type="button" class="btn preeleccontracttpl" data-name="eleccontracttplid" style="display:inline-block;vertical-align:top;" disabled="disabled">预览</button>');
    },
    displayTPL:function(){
        var thisInstance=this;
        $('body').on('click','.preeleccontracttpl',function(){
            var dataName=$(this).data('name');


            if(dataName=='eleccontracttplid'){
                var dataurl=$('select[name="eleccontracttplid"]').find('option:selected').data('url');
                var dataname=$('select[name="eleccontracttplid"]').find('option:selected').data('name');
                var templateId=$('select[name="eleccontracttplid"]').val();
            }else if(dataName=='eleccontracttplid'){
                var dataurl=$('select[name="relatedattachmentid"]').find('option:selected').data('url');
                var dataname=$('select[name="relatedattachmentid"]').find('option:selected').data('name');
                var templateId=$('select[name="relatedattachmentid"]').val();
            }
            if(templateId>0){
            }else{
                return ;
            }
            var res={};
            var contractattribute=$('select[name="contractattribute"]').val();
            contractattribute=contractattribute=='standard'?0:1;
            res.data={'contract':dataurl,'name':dataname,
                'contractType':contractattribute,
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
    /**
     *获取列表的信息
     */
    getTPList:function(modeaction,flag){
        modeaction=modeaction || 'ElecTPLList';
        var thisInstance=this;
        var contractattribute=$('select[name="contractattribute"]').val();//合同属性
        var contract_type=$('select[name="contract_type"]').val();//合同类型
        var signaturetype=$('select[name="signaturetype"]').val();//签署类型
        var servicecontractstype=$('select[name="servicecontractstype"]').val();//签署类型
        if(signaturetype!='eleccontract' || contractattribute!='standard' || contract_type=='' || servicecontractstype==''){
            return ;
        }
        var postData = {
            "module": 'ServiceContracts',
            "action": "BasicAjax",
            'mode': 'getElecTPLList',
            'classtpl': modeaction,
            'contractattribute':contractattribute,
            'servicecontractstype':servicecontractstype,
            'contract_type':contract_type
        };
        if(modeaction=='ElecTPLList'){
            var tpl='eleccontracttpl';
            var tplurl='eleccontracttplurl';
            var tplid='eleccontracttplid';
        }else{
            var tpl='relatedattachment';
            var tplurl='relatedattachmenturl';
            var tplid='relatedattachmentid';
        }
        AppConnector.request(postData).then(
            function(res){
                if(res && res.success) {
                    var data=res.data;
                    var eleccontracttplid= $('select[name="eleccontracttplid"]').val();
                    $('select[name="'+tplid+'"]').empty();
                    $('.preeleccontracttpl').removeClass('btn-info');
                    $('.preeleccontracttpl').attr('disabled','disabeld');
                    if(data.length>0){
                        var optionsStr=''
                        var tplflag=false;
                        var selectStr="";
                        $.each(data,function(i,v){
                            selectStr='';
                            if(flag==2){
                                if(v.id==eleccontracttplid){
                                    tplflag=true;
                                    $('input[name="'+tpl+'"]').val(v.name);
                                    $('input[name="'+tplurl+'"]').val(v.url);
                                    selectStr=' selected';
                                    optionsStr+='<option value="'+v.id+'" data-url="'+v.url+'" data-name="'+v.name+'" data-json=\''+JSON.stringify(v)+'\''+selectStr+'>'+v.name+'</option>';
                                }
                            }else{
                                tplflag=true;
                                if(i==0){
                                    $('input[name="'+tpl+'"]').val(v.name);
                                    $('input[name="'+tplurl+'"]').val(v.url);
                                    selectStr=' selected';
                                }
                                optionsStr+='<option value="'+v.id+'" data-url="'+v.url+'" data-name="'+v.name+'" data-json=\''+JSON.stringify(v)+'\''+selectStr+'>'+v.name+'</option>';
                            }

                        });
                        if(tplflag){
                            $('.preeleccontracttpl').addClass('btn-info');
                        }
                        $('select[name="'+tplid+'"]').append(optionsStr);
                        if(flag==2){
                            $('select[name="'+tplid+'"]').val(eleccontracttplid);
                        }
                        $('.preeleccontracttpl').removeAttr('disabled');
                    }
                    $('select[name="'+tplid+'"]').trigger('liszt:updated');
                }
            }
        );
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
    contractSubmitPreView:function(){
        var thisInstance=this;
        var msg = {
            'message': ' ',
        };
        Vtiger_Helper_Js.showConfirmationBox(msg).then(function (data) {
        });
        $('.modal-dialog').remove();
        $('.modal-footer').remove();
        $('.bootbox-confirm').append('<div style="position: absolute;left:0px;top:0px;width:100%;height:100%;"><iframe id="prevcontact" src="/contracttpl/pc.html" style="width:100%;height:100%;background-color:#ffffff;"/></div>');
        $('#prevcontact').on('load', function () {
            var iframeThis = this;
            var contractId=parentView.contractId; // 合同id
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
                //$(this).prop('disabled', true)
                if(confirmflag){
                    return false;
                }
                var that = this;
                var itd=[];
                $("input[id^=editinput]",iframeThis.contentDocument).each((i, v) => {
                    itd.push({
                        positionId: $(v).attr('data-id'),
                        value: $(v).val()
                    });
                    $('input[name="'+$(this).attr('id')+'"]').val($(v).val());
                })
                ;
                confirmflag=true;
                $('input[name="eleccontractid"]',parent.document).val(contractId);
                var postData = {
                    "module": 'ServiceContracts',
                    "action": "BasicAjax",
                    "contractId": contractId,
                    'mode': 'elecErpEdit',
                    'udata':itd
                };
                AppConnector.request(postData).then(function(res){
                    if (res && res.success) {
                        thisInstance.eleccontractSubmit=false;
                        $('[type="submit"]',parent.document).trigger('click');
                    }else{
                        confirmflag=false;
                        Vtiger_Helper_Js.showMessage({type: 'error', text: resd.msg});
                    }
                });
            });
            $(".contract-send", iframeThis.contentDocument).click(function () {//添加点击事件
                $('.reveiver-name', iframeThis.contentDocument).html($('select[name="elereceiver"]').val());
                $('.reveiver-phone', iframeThis.contentDocument).html($('input[name="elereceivermobile"]').val());
                $('.ensrue-popup', iframeThis.contentDocument).show();
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
                $('input[name="oldeleccontractid"]').val(contractId);
                $('input[name="oldeleccontracttplid"]').val($('select[name="eleccontracttplid"]').val());
                /*var postData = {
                    "module": 'ServiceContracts',
                    "action": "BasicAjax",
                    "contractId": contractId,
                    'mode': 'elecCommonTovoid',
                };
                AppConnector.request(postData).then(function(res){
                    if (res && res.success) {

                    }else{

                    }
                });*/
                $('.bootbox-confirm').modal('hide');

            });
            $('.head-tab ul li',iframeThis.contentDocument).on('click', function () {
                $('.head-tab ul li',iframeThis.contentDocument).removeClass('active').eq($(this).index()).addClass('active');
                $('.contact-container',iframeThis.contentDocument).hide().eq($(this).index()).show();
                $('.navigation',iframeThis.contentDocument).hide().eq($(this).index()).show();
            });
            $(".navigation",iframeThis.contentDocument).on('click','.pageContainer',function(){
                var  imgH = 1052.4;//中间图片的高度
                $(".contact-container .viewerContainer:visible",iframeThis.contentDocument).animate({scrollTop:($(this).attr('data-page')-1)*imgH},500)
            })
            $(".contract-sync", iframeThis.contentDocument).click(function () {
                var postData = {
                    "module": 'ServiceContracts',
                    "action": "BasicAjax",
                    "contractId": contractId,
                    'mode': 'getContractSync'
                };
                AppConnector.request(postData).then(
                    function (res) {
                        if (res && res.success) {
                            $.each(res.data,function(i,v){
                                //$("#editinput" + v.inputTextId, iframeThis.contentDocument).val(v.textValue);
                                $("#editinput" + v.id, iframeThis.contentDocument).val(v.value);
                                //$('input[name="editinput'+inputTextId+'"]').val(v.textValue)
                            });
                        } else {
                            alert(res.msg)
                        }
                    });
            });
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
    contractSubmitPreViewforCustom:function(){
        var thisInstance=this;
        var msg = {
            'message': ' ',
        };
        Vtiger_Helper_Js.showConfirmationBox(msg).then(function (data) {
        });
        $('.modal-dialog').remove();
        $('.modal-footer').remove();
        $('.bootbox-confirm').append('<div style="position: absolute;left:0px;top:0px;width:100%;height:100%;"><iframe id="prevcontactcust" src="/contracttplcustom/index.html" style="width:100%;height:100%;background-color:#ffffff;"/></div>');
        $('#prevcontactcust').on('load', function () {
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
    serviceContractstypeChange:function(){
        var thisInstance=this;
        $('#EditView').on('change','select[name="servicecontractstype"]',function(){
            thisInstance.getTPList();
        });
        $('#EditView').on('change','select[name="contractattribute"]',function(){
            var thisValue=$(this).val();
            if(thisValue=='customized'){
                $('#eleccontracttplidblock').hide();
                $('#fileshowblock').show();
                $('.ke-upload-file').attr('accept','.doc,.docx,.pdf');
                setTimeout('$(".ke-upload-file").attr("accept",".docx");$(".upload div").first().css({"width":"120px"}).html(\'<div style="margin-top: -2px;">支持docx格式</div><div style="margin-top: -5px;">文件大小不超过2M</div>\')',1000);
            }else{
                $('#eleccontracttplidblock').show();
                $('#fileshowblock').hide();
                thisInstance.getTPList();
            }
        });
        $('#EditView').on('change','select[name="elereceiver"]',function(){
            $('input[name="elereceivermobile"]').val($(this).find('option:selected').data('mobile'))
        });
        var record=$('input[name="record"]').val();
        if(record>0){
            var oldsignaturetypevale=$('select[name="signaturetype"]').val();
            if(oldsignaturetypevale=='eleccontract'){
                $('select[name="parent_contracttypeid"]').find('option').not(':selected').remove();
                $('select[name="parent_contracttypeid"]').trigger('liszt:updated');
                $('select[name="contract_type"]').find('option').not(':selected').remove();
                $('select[name="contract_type"]').trigger('liszt:updated');
                $('select[name="servicecontractstype"]').find('option').not(':selected').remove();
                $('select[name="servicecontractstype"]').trigger('liszt:updated');
                $('select[name="contractattribute"]').find('option').not(':selected').remove();
                $('select[name="contractattribute"]').trigger('liszt:updated');
                thisInstance.relatedchangeData();
                thisInstance.getTPList('ElecTPLList',2);
            }
        }
        setTimeout('$(".ke-upload-file").attr("accept",".doc,.docx,.pdf");$(".upload div").first().css({"width":"120px"}).html(\'<div style="margin-top: -2px;">支持docx格式</div><div style="margin-top: -5px;">文件大小不超过2M</div>\')',1000);
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
        var servicecontractsid=$('input[name="record"]').val();
        var expirationTime=$('input[name="actualeffectivetime"]').val();
        var oldeleccontractid=$('input[name="oldeleccontractid"]').val();
        var clientproperty=$('select[name="clientproperty"]').val();
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
            "senderName":senderName,
            "oldeleccontractid":oldeleccontractid,
            "clientproperty":clientproperty,
            "total":total,
            "accountid":accountid,
            "senderPhone":senderPhone,
            "receiverName":receiverName,
            "receiverPhone":receiverPhone
        };
        return postData;
    },
    standContractSubmit:function(){
        var thisInstance=this;
        var templateId=$('select[name="eleccontracttplid"]').val();
        if(templateId>0){
        }else{
            return false;
        }
        var postData=thisInstance.saveAndReplaceParams();
        postData.mode='saveAndReplace';
        var progressIndicatorElement = jQuery.progressIndicator({
            'message': '正在获取电子合同相关信息...',
            'position': 'html',
            'blockInfo': {'enabled': true}
        });
        AppConnector.request(postData).then(function (res) {
            progressIndicatorElement.progressIndicator({'mode': 'hide'});
            if (res && res.success) {
                thisInstance.getTPLViewByContractId(res.data.contractId)
            } else {
                Vtiger_Helper_Js.showMessage({type:'error',text:res.msg});
            }
        }, function () {
            return false;
        });
    },
    customizedContractSubmit:function(){
        var thisInstance=this;
        var attachmentsids=$('#fileall input[name^="attachmentsid["]');
        if(attachmentsids.length==0){
            Vtiger_Helper_Js.showMessage({type:'error',text:"请上传附件！"});
            return false;
        }
        if(attachmentsids.length!=1){
            Vtiger_Helper_Js.showMessage({type:'error',text:"附件只能有一个"});
            return false;
        }
        var postData=thisInstance.saveAndReplaceParams();
        postData.mode='erpUpload';
        postData.fileid=attachmentsids.val();
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
                thisInstance.contractSubmitPreViewforCustom()
            } else {
                Vtiger_Helper_Js.showMessage({type:'error',text:res.msg});
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
    isElecLoadData:function(){
        var record=$('input[name="record"]').val();
        var returnFlag=false;
        if(record>0) {
            var oldsignaturetypevale = $('select[name="signaturetype"]').val();
            if (oldsignaturetypevale == 'eleccontract'){
                returnFlag=true;
            }
        }
        return returnFlag;
    },
    isBringOutDefaultData:function(){
        var len = $('input[name^="mreceiveableamount["]').length;
        if(len == 1){
            var isbringout = $('#isbringout').val();
            var mreceiveableamount = $('input[name^="mreceiveableamount["]:first').val();
            if(isbringout == 1 && mreceiveableamount == ''){
                var total = $('input[name="total"]').val();
                $('input[name^="mreceiveableamount["]:first').val(total);
                $('input[name^="mcollectiondescription["]:first').val('一次性付款');
            }
        }
    },
    /**********应收start**************/
    addPhaseSplit:function(){
        var thisInstance=this;

        $('#EditView').on('blur','input[name="total"]',function(){
            var total = $('input[name="total"]').val();
            var len = $('input[name^="mreceiveableamount["]').length;
            var isbringout = $('#isbringout').val();
            if(len == 1 && isbringout == 1){
                $('input[name^="mreceiveableamount["]:first').val(total);
                $('input[name^="mcollectiondescription["]:first').val('一次性付款');
            }
        });
        $('#EditView').on('click','#addPhaseSplit',function(){
            /*var msg = {
                'message': '确定要添加收款阶段',
            };*/
            //Vtiger_Helper_Js.showConfirmationBox(msg).then(function (data) {
            var stagenum=$('.CONTRACT_PHASE_SPLIT').attr('data-stagenum');
            stagenum=parseInt(stagenum);
            ++stagenum;
            thisInstance.getPhaseSplit(stagenum,0);
            //});

        })
        $('#EditView').on('click','.subPhaseSplit',function(){
            var _this=this;
            var stagenum=$('.CONTRACT_PHASE_SPLIT').attr('data-stagenum');
            var msg = {
                'message': '确定要删除收款阶段?',
            };
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function (data) {
                var current_stagenum=$(_this).data('stagenum');
                $(_this).closest('tr').remove();
                if(stagenum!=current_stagenum){
                    $.each($('input[name^="mstageshow["]'),function(key,value){
                        $(value).val('第'+(key+1)+'阶段');
                    })
                    if($('input[name^="mstageshow["]').length==1){
                        $('.CONTRACT_PHASE_SPLIT').attr('data-stagenum',1);
                    }else{
                        $('.CONTRACT_PHASE_SPLIT').attr('data-stagenum',$('input[name^="mstageshow["]').length);
                    }
                }else{
                    if($('input[name^="mstageshow["]').length==1){
                        $('.CONTRACT_PHASE_SPLIT').attr('data-stagenum',1);
                    }else{
                        $('.CONTRACT_PHASE_SPLIT').attr('data-stagenum',--stagenum);
                    }
                }
            })
        });
        $('#EditView').on('change','select[name="frameworkcontract"]',function(){
            var _this=this;
            var thisValue=$(this).val();
            if(thisValue=='no'){
                if($('.SETTLEMENT_CLAUSE').length==0){
                    return false;
                }
                if($('.CONTRACT_PHASE_SPLIT').length==0){
                    thisInstance.getPhaseSplit(1,1);
                }
            }else{
                $('.CONTRACT_PHASE_SPLIT').remove();
            }
        })
        $('#EditView').on('change blur keyup','input[name^="mreceiveableamount["]',function(){
            thisInstance.checkremarkprice($(this));
        })

    },
    getPhaseSplit:function(stagenum,showHeader){
        var thisInstance = this;
        var postData = {
            "module": 'ServiceContracts',
            "action": "BasicAjax",
            "mode":'addPhaseSplit',
            'stagenum':stagenum,
            'showHeader':showHeader
        };
        var progressIndicatorElement = jQuery.progressIndicator({
            'message': '正在获取相关信息...',
            'position': 'html',
            'blockInfo': {'enabled': true}
        });
        AppConnector.request(postData).then(function (resd) {
            progressIndicatorElement.progressIndicator({'mode': 'hide'});
            if (resd && resd.success) {
                if(showHeader==1){
                    $('.SETTLEMENT_CLAUSE').after(resd.result);
                }else{
                    $('.CONTRACT_PHASE_SPLIT_LIST').append(resd.result);
                    $('.CONTRACT_PHASE_SPLIT').attr('data-stagenum',stagenum);
                    $('input[name="mstageshow['+stagenum+']"]').val('第'+($('input[name^="mstageshow["]').length)+'阶段');
                }
                thisInstance.isBringOutDefaultData();
            } else {
                //Vtiger_Helper_Js.showMessage({type: 'error', text: resd.msg});
            }
        }, function () {
            return false;
        });
    },
    caclMamountreceivable:function(){
        var $mamountreceivable=$('input[name^="mreceiveableamount["]');
        var amountreceivable=0;
        if($mamountreceivable.length>0){
            $.each($mamountreceivable,function(key,value){
                amountreceivable+=isNaN(parseFloat($(value).val()))?0:parseFloat($(value).val());
            });
        }
        return amountreceivable;
    },
    /**********应收end*************/
    registerEvents: function (container) {
        this._super(container);
        this.checkForm();
        //this.init();
        this.registerEventaddfallinto();
        this.registerRecordPreSaveEvent();
        this.totalprefix();
        this.contractProductsEvent();
        this.checkedProduct();
        this.priceBlur();
        this.registerResultEvent(container);
        //this.registerTotalSaveEvent();
        this.parentProductsEvent();
        //格式化输入
        this.inputnumberchange();
        $('#ServiceContracts_editView_fieldName_issubmit').attr('data-content','<span color="red">勾选<<b>确认提交审核</b>>合同才会被审核</span>');
        $('#ServiceContracts_editView_fieldName_issubmit').popover('show');
        $('#ServiceContracts_editView_fieldName_iscomplete').attr('data-content','<span color="red">勾选<<b>已签收</b>>合同才会签收</span>');
        $('#ServiceContracts_editView_fieldName_iscomplete').popover('show');
        //合同是已回收状态时，已完成复选框必须勾选
        if($("input[name='current_modulestatus']").val()=='c_recovered') {
            $('#ServiceContracts_editView_fieldName_iscomplete').parent().prev().children('label').prepend('<span class="redColor">* </span>');
        }
        //附件整行不换行显示
        $('.fileUploadContainer').parent().parent().css('whiteSpace', 'nowrap');
        //var hidden = $("<input>").attr("type", "hidden").attr("id", "no-repeatid").val("0").appendTo("body");
        this.registerAddingNewProducts();
        this.registerDeleteLineItemEvent();
        this.checkintnumber();
        this.setprouctdprice();
        this.onchangesuper();
        this.signChange();
        this.selectVendor();
        //this.findproduct();//不用了
        this.registerContractIscomplete();
        this.checkInvoice();
        this.getElecContractTable();
        this.displayTPL();
        this.divideChoosed();
        this.serviceContractstypeChange();
        this.addPhaseSplit();
        this.contractClassficationChange();
        this.showAgentList();
        this.contractTypeEvent();
        this.contractProductsEvent2();
        // this.standardnamechange();
        this.sealplaceChange();
        // if($("input[name='record']").val()&&($('input[name="isstandard"]').val()!=0||$('input[name="isstandard"]').val()==1)&&$("input[name='contract_no']").val()!=''){
        //     //编辑时的状态查看是否有附件
        //     this.editWithFile();
        // }
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

    editWithFile:function(){
        var sparams = {
            'module': 'ServiceContracts',
            'action': 'BasicAjax',
            'record': $('input[name="sc_related_to"]').val(),
            'mode': 'getEditZizhiFile',
        };
        AppConnector.request(sparams).then(
            function (datas) {
                if (datas.success==true) {
                    if(datas.result.flag){
                        // $("#needZizhi").val('yes');
                        var html='';
                        for(var i in datas.result.data){
                            html+='<span class="label file'+i+'" style="margin-left:5px;">'+datas.result.data[i]+'&nbsp;<b class="deletefile" data-class="file'+i+'" data-id="'+i+'" title="删除文件" style="display:inline-block;width:12px;height:12px;line-height:12px;text-align:center">x</b>&nbsp;</span><input class="ke-input-text file'+i+'" type="hidden" name="zizhifile['+i+']" id="zizhifile" value="'+datas.result.data[i]+'" readonly="readonly"><input class="file'+i+'" type="hidden" name="attachmentsid['+i+']" value="'+i+'">';
                        }
                        $("#fileallzizhi").html(html);
                        $(".fileUploadContainerZizhi").css('display','block');
                        $("#fileUploadRemand").css("width",'10px')
                    }else{
                        $(".fileUploadContainerZizhi").css('display','none');
                        // $("#needZizhi").val('no');
                    }
                }
            }
        )
    },


    checkInvoice :function(){
        $('body').on('click','input[name=iscomplete]',function(){
            var record = $('input[name="record"]').val();
            if ($(this).is(":checked")) {
                var postData = {
                    "module": 'ServiceContracts',
                    "action": "BasicAjax",
                    "record": record,
                    'mode': 'getCheckInvoice'
                };
                AppConnector.request(postData).then(
                    function(data){
                        if(data['result'] == 'fail') {
                            Vtiger_Helper_Js.showMessage({type:'error',text:"该合同有未审核完成的分成单!!"});
                            return false;
                        }
                    }
                );
            }
        });
    },
    divideChoosed:function () {
        $('body').on('change','select[name="suoshuren[]"]',function(){
            var objects=$(this).closest("tr");
            var company=$(this).find("option:selected").data("company");
            objects.find("select[name='suoshugongsi[]']").val(company);
            objects.find("select[name='suoshugongsi[]']").trigger('liszt:updated');
        });
    },
    isExistMainCompany:function(){
        var invoicecompany=$('select[name="invoicecompany"]').val();
        var params={};
        var res = false;
        params.data = {
            "module": "ServiceContracts",
            "action": "ChangeAjax",
            "mode": "isExistMainCompany",
            "invoicecompany": invoicecompany
        };
        params.async=false;
        AppConnector.request(params).then(
            function (data) {
                res=data.result[0];
            },
            function (error) {
            });
        return res;
    },
    contractClassficationChange:function(){
        var contract_classification = $('select[name="contract_classification"]').val();


        $('body').on('change','select[name="contract_classification"]',function(){
            var contract_classification = $(this).val();
            if(contract_classification=='normalcontract'){
                $("#ServiceContracts_editView_fieldName_agentname").parent().prev().children('.muted').hide();
                $("#ServiceContracts_editView_fieldName_agentname").hide();
                $("#tripscrelatedto").html("*");
                $("#tripscrelatedto").addClass("redColor");
            }else{
                var signaturetype = $("select[name='signaturetype']").val();
                if(signaturetype!='eleccontract'){
                    $("#ServiceContracts_editView_fieldName_agentname").parent().prev().children('.muted').show();
                    $("#ServiceContracts_editView_fieldName_agentname").show();
                    $("#tripscrelatedto").html("");
                    $("#tripscrelatedto").removeClass("redColor");
                    return;
                }
                $("#ServiceContracts_editView_fieldName_agentname").parent().prev().children('.muted').hide();
                $("#ServiceContracts_editView_fieldName_agentname").hide();
                $('select[name="contract_classification"]').find("option:contains('正常合同')").attr("selected",true);
                $('select[name="contract_classification"]').next().children('a').children('span').text('正常合同');
                Vtiger_Helper_Js.showMessage({type:'error',text:"电子合同暂不支持三方合同"});
            }
        });


        if(contract_classification!='tripcontract'){
            $("#ServiceContracts_editView_fieldName_agentname").parent().prev().children('.muted').hide();
            $("#ServiceContracts_editView_fieldName_agentname").hide();
            $("#tripscrelatedto").html("*");
            $("#tripscrelatedto").addClass("redColor");
        }

        var params={};
        params.data = {
            "module": "ServiceContracts",
            "action": "ChangeAjax",
            "mode": "agentList",
        };
        AppConnector.request(params).then(
            function (data) {
                console.log(data);
                if(data.success){
                    results = data.result;
                    optionstr = '';
                    $.each(results,function (k, v) {
                        optionstr +='<option data-agentid="'+v.agentIdentity+'" value="'+v.companyName+'">'
                    });
                    $("#agentlist").append(optionstr);
                }else{
                    // Vtiger_Helper_Js.showMessage({type:'error',text:"获取代理商列表失败"});
                }
            },
            function (error) {
            });


    },
    showAgentList:function () {
        $('body').on('focus','#ServiceContracts_editView_fieldName_agentname',function(){
            $("#ServiceContracts_editView_fieldName_agentname").val('');
            $("input[name='agentid']").val(0);
        });
    },
    checkForm:function() {
        var thisInstance = this;
        $('#servicecontractsub').on('click',function (event) {
            var agentname = $("#ServiceContracts_editView_fieldName_agentname").val();
            var agentid = $("input[name='agentid']").val();
            var contract_classification = $("select[name='contract_classification']").val();
            var sc_related_to = $("input[name='sc_related_to']").val();
            if (contract_classification== 'tripcontract' && (!agentid || !agentname)) {
                alert('合同分类为三方合同,代理商必填');
                event.preventDefault();
                return false;
            }
            if(contract_classification!= 'tripcontract' && !sc_related_to ){
                alert('客户必填');
                event.preventDefault();
                return false;
            }

            // var inputcouponcode= $("input[name='couponcode']").val();
            // var inputcouponcodeusername = $("input[name='couponname']").val();
            // console.log(inputcouponcode);
            // console.log(inputcouponcodeusername);
        })
    },
    contractProductsEvent2: function () {
        var thisInstance=this;
        $('form').on('change','select[name="categoryid"]', function () {
            if(thisInstance.isElecLoadData()){
                return false;
            }
            var record = $('input[name="record"]').val();
            var thisVar=$("select[name='contract_type']").val();

            //20201215 兼容新零售，使其和T云web端一样
            if(record&&thisVar=='SaaS新零售系统'){
                thisVar='T云WEB版';
            }

            $("#div_old_contract_no").empty().remove();

            //$('.widgetContainer_servicecontractproducts').html(''); //清空产品
            var delproducts=$('.productidd').find('input[name^="productnumber"]');
            thisInstance.deleteUE(delproducts);
            $('.productidd').html(''); //清空合同类型产品
            $('.productidd').remove(); //清空合同类型产品

            var contract_typeName = encodeURIComponent(thisVar);  //请求异常处理，对字符进行编码
            contract_typeName='getproducts&contract_typeName=' + contract_typeName+'&record='+record;
            var strclassname='.PriorityName';
            var tablefield='productid';
            // 这里为了兼容PHP端代码 添加一个变量
            contract_typeName += '&type=serviceContractsEdit';
            var servicecontractstype = $("select[name='servicecontractstype']").val();
            contract_typeName += '&servicecontractstype='+servicecontractstype;
            var category=$("select[name='categoryid']").val();
            if(category!=undefined){
                contract_typeName += '&category='+category;
            }
            var agentid = $("input[name='agentid']").val();
            var contract_classification = $("select[name='contract_classification']").val();
            if(agentid && contract_classification=='tripcontract'){
                contract_typeName += '&agents='+agentid;
            }
            if(contract_classification){
                contract_typeName += '&contract_classification='+contract_classification;
            }

            //加载原合同信息
            thisInstance.loadOldContractInfo();
            thisInstance.getTPList();

            thisInstance.registerDeleteProduct();//更新一下是否有客外产品或是合同类型产品需要计算

            if ($(this).val() != "") {
                var thisp=$(this);
                var params = {
                    'type': 'GET',
                    'dataType': 'html',
                    'data': 'module=ServiceContracts&action=ChangeAjax&mode='+ contract_typeName
                };
                $('.productidd').remove();
                $('.extraproductidd').remove();
                AppConnector.request(params).then(
                    function (data) {
                        var t_data = data;

                        var t_info = eval("("+t_data+")");

                        var isstandard = t_info.isstandard;
                        data = t_info.product_list;
                        if (isstandard == '1') { //非标合同
                            $('#ServiceContracts_editView_fieldName_isstandard').attr('checked', true);
                            $('input[name=isstandard]').val(1);
                        } else {
                            $('#ServiceContracts_editView_fieldName_isstandard').attr('checked', false);
                            $('input[name=isstandard]').val(0);
                        }
                        if (data == null || data.length == 0) {
                            $(strclassname).html('<font color=red>没有相对应的产品信息!</font>');
                            var otherList=t_info.otherproduct_list;
                            var otherproductHTML='';
                            if(t_info.otherproducttype==1){
                                $('.extraproductidname').html(otherList);
                            }else{
                                if(otherList.length>0){
                                    $.each(otherList,function(key,value){
                                        otherproductHTML += '<div style="line-height: 30px;float: left; float: left; width: 260px; border: 1px solid  rgba(57, 15, 40, 0.18); margin: 2px;  border-radius: 5px;padding-bottom: 5px;"><label class="checkbox inline"><input type="checkbox"  value="' + value['productid'] + '" id="extraproductid'+ value['productid']+'" name="extraproductid[]" data-name="extraproductid" data-istyun="' + value['istyun'] + '" data-pid="' + value['parentid'] + '" data-tid="' + value['tyunproductid'] + '" class="entryCheckBox extraproductid"> ' + value['productname'] + '</label></div>';
                                    });
                                    $('.extraproductidname').html(otherproductHTML);
                                }else{
                                    $('.extraproductidname').html('<font color="red">没有相对应的产品信息!</font>');
                                }
                            }
                        } else {
                            $(strclassname).html('');
                            //var info = eval("(" + data + ")");
                            var info = data;
                            var PriorityNameHTML = "";
                            if (info.length == 0) {
                                $(strclassname).html('<font color=red>没有相对应的产品信息!</font>');
                            } else {
                                var searchproducts='';
                                for (var i = 0; i < info.length; i++) {
                                    if(!info[i]&& typeof(info[i])!="undefined"&&info[i]!=0){
                                        continue;
                                    }
                                    PriorityNameHTML += '<div style="line-height:30px;float: left; float: left; width: 260px; border: 1px solid  rgba(57, 15, 40, 0.18); margin: 2px;  border-radius: 5px;padding-bottom: 5px"><label class="checkbox inline"><input type="checkbox" value="' + info[i]['productid'] + '" id="'+tablefield+'s'+ info[i]['productid']+'" name="'+tablefield+'[]" data-name="'+tablefield+'" data-istyun="' + info[i]['istyun'] + '" data-pid="' + info[i]['parentid'] + '" data-tid="' + info[i]['tyunproductid'] + '" class="entryCheckBox '+tablefield+'"> ' + info[i]['productname'] + '<input type="hidden" name="producttypename[' + info[i]['productid'] + ']" value="' + info[i]['productname'] + '"/></label></div>';
                                    searchproducts+='<option value='+tablefield+'s'+info[i]['productid'] + '>'+info[i]['productname']+'</optio>';
                                }
                                if(tablefield=='extraproductid'){
                                    PriorityNameHTML='<div style="float: left;width:260px;margin:2px;"><select class="chzn-select" id="searchproduct" > <option value="">选择要查找的产品名称</option>'+searchproducts+'</select></div>'+PriorityNameHTML;
                                }
                                $(strclassname).html(PriorityNameHTML);
                            }
                            var otherList=t_info.otherproduct_list;
                            var otherproductHTML='';
                            if(t_info.otherproducttype==1){
                                $('.extraproductidname').html(otherList);
                            }else{
                                if(otherList.length>0){
                                    $.each(otherList,function(key,value){
                                        otherproductHTML += '<div style="line-height: 30px;float: left; float: left; width: 260px; border: 1px solid  rgba(57, 15, 40, 0.18); margin: 2px;  border-radius: 5px;padding-bottom: 5px"><label class="checkbox inline"><input type="checkbox"  value="' + value['productid'] + '" id="extraproductid'+ value['productid']+'" name="extraproductid[]" data-name="extraproductid" data-istyun="' + value['istyun'] + '" data-pid="' + value['parentid'] + '" data-tid="' + value['tyunproductid'] + '" class="entryCheckBox extraproductid"> ' + value['productname'] + '</label></div>';
                                    });
                                    $('.extraproductidname').html(otherproductHTML);
                                }
                            }


                        }
                        $('.entryCheckBox').iCheck({
                            checkboxClass: 'icheckbox_minimal-blue'
                        });
                        $('.chzn-select').chosen();

                    },
                    function (error) {
                        $(strclassname).html('<font color=red>获取产品信息失败!</font>');
                    });
            }
        })
    },
    standardnamechange:function(){
        $("body").on("change",'select[name*="standard"]',function () {
            var productid=$(this).data("id");
            console.log(productid);
            var standard = $(this).val();
            var standardname = $(this).find("option:checked").text();
            console.log(standardname);
            $("input[name='standardname["+productid+"]']").val(standardname);
        })
    },
});

function initCouponData() {
    // $("input[name='couponcode']").val('');
    // $("input[name='couponname']").val('');
    // $("input[name='is_input_coupon_code']").val(0);
}

function inputSelect(){
    var agentname=$("#ServiceContracts_editView_fieldName_agentname").val();
    var option_length=$("#agentlist option").length;
    var option_id='';
    for(var i=0;i<option_length;i++){
        var option_value=$("#agentlist option").eq(i).val();
        if(agentname==option_value){
            option_id=$("#agentlist option").eq(i).attr('data-agentid');
            break;
        }
    }
    $("input[name='agentid']").val(option_id);
};
