/**
 充值申请单添加js gaocl add 2018/04/20
 **/
var detail_num = 0;
var refillapplicationtype = 'Accounts';
var idtopplatform = {};
var GOOGLE_TOPPLATFORM = '谷歌';
var Yandex_TOPPLATFORM = 'Yandex';
$(function () {
    $("#refillapplicationtype").on("change",function (e) {
        set_rechargesource($(this).val());
    })
    $("#paymentperiodshow").hide();
    //客户充值
    $('#detail_page_close').click(function() {
        getDetailsPageData();
    });
    //供应商充值
    $('#detail_page_close2').click(function() {
        getDetailsPageData();
    });

    // 修改明细
    $("#rechargesheet").on("click","a.rechargesheet_edit", function() {
        edit_rechargesheet($(this).parent());
    });

    // 删除明细
    $("#rechargesheet").on("click","a.rechargesheet_delete", function() {
        remove_rechargesheet($(this).parent());
    });

    // 跳转到 充值明细
    $('#goto-detail-page').click(function(e) {
        e.stopPropagation();
        if(refillapplicationtype == "Vendors"){
            goto_rechargesheet_vendor();
        }else{
            goto_rechargesheet_account();
        }
    });

    // 服务合同搜索
    $('#servicecontracts_page_search').click(function() {
        search_servicecontracts();
    });
    // 点击服务合同
    $('#servicecontracts_list').on("click","a.servicecontracts_list_li", function() {
        set_search_servicecontracts_info($(this));
    });

    // 供应商搜索 gaocl add 2018/04/19
    $('#vendors_page_search').click(function() {
        search_vendors();
    });
    // 点击供应商
    $('#vendor_list').on("click","a.vendors_list_li", function() {
        set_search_vendor_info($(this));
    });

    // 供应商合同搜索 gaocl add 2018/04/23
    $('#vendor-servicecontracts-page_search').click(function() {
        search_vendor_servicecontracts();
    });
    // 点击供应商合同
    $('#vendor_servicecontracts-page_list').on("click","a.vendor_servicecontracts_list_li", function() {
        set_search_vendor_servicecontracts_info($(this));
    });

    //上传附件
    $('.uploadfile').on('click', function(e) {
        e.preventDefault();
        $('#file').trigger('click');
    })
    $('input[type="file"]').on('change',function(){
        var filename=$(this)[0].files[0];
        var reader = new FileReader();
        var data = new FormData();
        data.append('uploadfiles',filename);

        var url='index.php?module=RefillApplication&action=upload';

        var xhr = new XMLHttpRequest();
        //xhr.upload.addEventListener("progress", uploadProgress, false);//监听上传进度
        xhr.addEventListener("load", uploadComplete, false);
        //xhr.addEventListener("error", uploadFailed, false);
        xhr.open("POST", url);
        xhr.send(data);
        function uploadComplete(evt) {
            /* 服务器端返回响应时候触发event事件*/
            var jsonparse=JSON.parse(evt.target.responseText);
            if(jsonparse.success){
                var str='<li><label>'+jsonparse.result.filename+'</label><br><input type="hidden" name="files[]" value="'+jsonparse.result.filename+'##'+jsonparse.result.id+'"></li>';
                $('#filevalues').append(str)
            }else{
                alert('上传失败');
                //alert(jsonparse.msg.type);
            }

        }

    });

    //充值明细计算-客户
    $('#detail-page-account').on("change",'select[name="tax"],input[name="prestoreadrate"],input[name="transferamount"],input[name="activationfee"],input[name="factorage"],input[name="taxation"]',function(){
        var thisName = $(this).attr("name");
        var thisValue=$(this).val();

        var detail_type = "#detail-page-account";
        var topplatform = $(detail_type +' input[name="productid_display"]').val();
        if(topplatform== GOOGLE_TOPPLATFORM || topplatform== Yandex_TOPPLATFORM) {
            if (thisName == 'transferamount' || thisName == 'taxation') {
                return;
            }
        }else{
            if(thisName == 'prestoreadrate'){
                return;
            }
        }

        if(thisName == 'factorage' || thisName == 'activationfee' || thisName == 'taxation' || thisName == 'transferamount'){
            if(thisValue.indexOf(".")){
                $(this).val(Number(thisValue).toFixed(2));
            }
        }
        if(topplatform== GOOGLE_TOPPLATFORM || topplatform==Yandex_TOPPLATFORM) {
            //1、税费 = 充值账户币*6%
            $(detail_type + ' input[name="taxation"]').val(parseFloat(taxationCalcByGoogle(detail_type)).toFixed(2));
            //2、现金充值 = 充值账户币/（1+返点）
            $(detail_type + ' input[name="rechargeamount"]').val(parseFloat(rechargeamountCalcByGoogle(detail_type)).toFixed(2));
            //3、应收款金额 = 开户费+代理商服务费+税费+现金充值
            $(detail_type + ' input[name="transferamount"]').val(parseFloat(transferamountCalcByGoogle(detail_type)).toFixed(2));
            //4、合计费用 = 开户费+代理商服务费+税费
            $(detail_type + ' input[name="totalcost"]').val(parseFloat(totalcostCalc(detail_type)).toFixed(2));
            //5、成本 = 充值账户币 *90% + 税费
            $(detail_type + ' input[name="servicecost"]').val(parseFloat(servicecostCalcByGoogle(detail_type)).toFixed(2));
            //6、毛利总计 = 应收款金额 - 成本
            $(detail_type + ' input[name="totalgrossprofit"]').val(parseFloat(totalgrossprofitCalc(detail_type)).toFixed(2));
        }else{
            //1、应收款金额 = 开户费+代理商服务费+税费+现金充值 => 现金充值=应收款金额 - (开户费 + 代理商服务费+税费)
            $(detail_type + ' input[name="rechargeamount"]').val(parseFloat(rechargeamountCalc(detail_type)).toFixed(2));
            //2、充值账户币 = 现金充值*（1+返点）
            $(detail_type + ' input[name="prestoreadrate"]').val(prestoreadrateCalc(detail_type).toFixed(2));
            //3、合计费用计算(合计费用 = 开户费+代理商服务费+税费)
            $(detail_type + ' input[name="totalcost"]').val(parseFloat(totalcostCalc(detail_type)).toFixed(2));
            //4、成本 = 现金充值 * (1+返点) / (1+供应商返点(supprebate))
            $(detail_type + ' input[name="servicecost"]').val(parseFloat(servicecostCalc(detail_type)).toFixed(2));
            //5、毛利总计 = 应收款金额 - 成本
            $(detail_type + ' input[name="totalgrossprofit"]').val(parseFloat(totalgrossprofitCalc(detail_type)).toFixed(2));
        }
    });

    //充值明细计算-供应商
    $('#detail-page-vendors').on("change",'select[name="tax"],input[name="prestoreadrate"],input[name="transferamount"],input[name="activationfee"],input[name="factorage"],input[name="taxation"]',function(){
        var thisName = $(this).attr("name");
        var thisValue=$(this).val();

        var detail_type = "#detail-page-vendors";
        var topplatform = $(detail_type +' input[name="productservice_display"]').val();
        if(topplatform== GOOGLE_TOPPLATFORM || topplatform==Yandex_TOPPLATFORM) {
            if (thisName == 'transferamount' || thisName == 'taxation') {
                return;
            }
        }else{
            if(thisName == 'prestoreadrate'){
                return;
            }
        }

        if(thisName == 'factorage' || thisName == 'activationfee' || thisName == 'taxation' || thisName == 'transferamount'){
            if(thisValue.indexOf(".")){
                $(this).val(Number(thisValue).toFixed(2));
            }
        }

        if(topplatform== GOOGLE_TOPPLATFORM || topplatform== Yandex_TOPPLATFORM) {
            //1、税费 = 充值账户币*6%,美金没有税费
            $(detail_type + ' input[name="taxation"]').val(parseFloat(taxationCalcByGoogle(detail_type)).toFixed(2));
            //2、现金充值 = 充值账户币/（1+返点）
            $(detail_type + ' input[name="rechargeamount"]').val(parseFloat(rechargeamountCalcByGoogle(detail_type)).toFixed(2));
            //3、应收款金额 = 开户费+代理商服务费+税费+现金充值
            $(detail_type + ' input[name="transferamount"]').val(parseFloat(transferamountCalcByGoogle(detail_type)).toFixed(2));
            //4、合计费用 = 开户费+代理商服务费+税费
            $(detail_type + ' input[name="totalcost"]').val(parseFloat(totalcostCalc(detail_type)).toFixed(2));
            //5、成本 = 充值账户币 *90% + 税费
            //$(detail_type + ' input[name="servicecost"]').val(parseFloat(servicecostCalcByGoogle(detail_type)).toFixed(2));
            $(detail_type + ' input[name="servicecost"]').val(parseFloat(servicecostCalc(detail_type)).toFixed(2));
            //6、毛利总计 = 应收款金额 - 成本
            $(detail_type + ' input[name="totalgrossprofit"]').val(parseFloat(totalgrossprofitCalc(detail_type)).toFixed(2));
        }else{
            //1、应收款金额 = 开户费+代理商服务费+税费+现金充值 => 现金充值=应收款金额 - (开户费 + 代理商服务费+税费)
            $(detail_type + ' input[name="rechargeamount"]').val(parseFloat(rechargeamountCalc(detail_type)).toFixed(2));
            //2、充值账户币 = 现金充值*（1+返点）
            $(detail_type + ' input[name="prestoreadrate"]').val(prestoreadrateCalc(detail_type).toFixed(0));
            //3、合计费用计算(合计费用 = 开户费+代理商服务费+税费)
            $(detail_type + ' input[name="totalcost"]').val(parseFloat(totalcostCalc(detail_type)).toFixed(2));
            //4、成本 = 现金充值 * (1+返点) / (1+供应商返点(supprebate))
            $(detail_type + ' input[name="servicecost"]').val(parseFloat(servicecostCalc(detail_type)).toFixed(2));
            //5、毛利总计 = 应收款金额 - 成本
            $(detail_type + ' input[name="totalgrossprofit"]').val(parseFloat(totalgrossprofitCalc(detail_type)).toFixed(2));
        }
    });
    $('#search_servicecontracts').on('keydown',function(event){
        if(event.keyCode==13){
            search_servicecontracts();
            return false;
        }
    });
    $('#search_vendors').on('keydown',function(event){
        if(event.keyCode==13){
            search_vendors();
            return false;
        }
    });
})
//===================计算方法===========================================================================================
//浮点数加法运算
function FloatAdd(arg1,arg2){
    var r1,r2,m;
    try{r1=arg1.toString().split(".")[1].length}catch(e){r1=0}
    try{r2=arg2.toString().split(".")[1].length}catch(e){r2=0}
    m=Math.pow(10,Math.max(r1,r2));
    return (arg1*m+arg2*m)/m;
}

//浮点数减法运算
function FloatSub(arg1,arg2){
    var r1,r2,m,n;
    try{r1=arg1.toString().split(".")[1].length}catch(e){r1=0}
    try{r2=arg2.toString().split(".")[1].length}catch(e){r2=0}
    m=Math.pow(10,Math.max(r1,r2));
    //动态控制精度长度
    n=(r1>r2)?r1:r2;
    return ((arg1*m-arg2*m)/m).toFixed(n);
}

//浮点数乘法运算
function FloatMul(arg1,arg2)
{
    var m=0,s1=arg1.toString(),s2=arg2.toString();
    try{m+=s1.split(".")[1].length}catch(e){}
    try{m+=s2.split(".")[1].length}catch(e){}
    return Number(s1.replace(".",""))*Number(s2.replace(".",""))/Math.pow(10,m);
}
//浮点数除法运算
function FloatDiv(arg1,arg2){
    var t1=0,t2=0,r1,r2;
    try{t1=arg1.toString().split(".")[1].length}catch(e){}
    try{t2=arg2.toString().split(".")[1].length}catch(e){}
    with(Math){
        r1=Number(arg1.toString().replace(".",""));
        r2=Number(arg2.toString().replace(".",""));
        return (r1/r2)*pow(10,t2-t1);
    }
}

//=========应收款计算(应收款金额 = 开户费+代理商服务费+税费+现金充值)===================================================
function transferamountCalcByGoogle(detail_type){
    //现金充值
    var rechargeamount = $(detail_type +' input[name="rechargeamount"]').val();
    rechargeamount = rechargeamount == ""?0:rechargeamount;
    //开户费
    var activationfee = $(detail_type +' input[name="activationfee"]').val();
    activationfee = activationfee == ""?0:activationfee;
    //代理商服务费
    var factorage = $(detail_type +' input[name="factorage"]').val();
    factorage = factorage == ""?0:factorage;
    //税费
    var taxation = $(detail_type +' input[name="taxation"]').val();
    taxation = taxation == ""?0:taxation;

    var resultValue = FloatAdd(rechargeamount,activationfee);
    resultValue = FloatAdd(resultValue,factorage);
    return FloatAdd(resultValue,taxation);
}
//========税费计算(税费 = 充值账户币*6%)================================================================================
function taxationCalcByGoogle(detail_type){

    var receivementcurrencytype = $(detail_type +' select[name="receivementcurrencytype"]').val();
    if(receivementcurrencytype=='美金'){
        return 0.00;
    }
    var prestoreadrate = $(detail_type +' input[name="prestoreadrate"]').val();
    if(!prestoreadrate){
        prestoreadrate = 0;
    }
    return FloatMul(prestoreadrate,0.06);
}
//=========成本计算(成本 = 现金充值 * (1+返点) / (1+供应商返点(supprebate)))============================================
function servicecostCalc(detail_type){
    var supprebate = $(detail_type +' input[name="supprebate"]').val();
    if(!supprebate){
        supprebate = 0;
    }
    //supprebate = FloatAdd(1,FloatDiv(supprebate,100));
    var currentDiscount = $(detail_type +' input[name="discount"]').val();
    if(!currentDiscount){
        currentDiscount = 0;
    }
    var accountrebatetype = $(detail_type +' select[name="accountrebatetype"]').val();
    var rebatetype = $(detail_type +' select[name="rebatetype"]').val();
    var rechargeamount = $(detail_type +' input[name="rechargeamount"]').val();
    if(rebatetype=='CashBack' && accountrebatetype=='CashBack'){
        //充值金额/(1-客户返点)*(1-供应商返点)
        currentDiscount=100-currentDiscount*1;
        supprebate=100-supprebate*1;
        var discountratio=FloatDiv(currentDiscount,supprebate);
        var servicecost=FloatDiv(rechargeamount,discountratio)*1;
    }else if(rebatetype=='GoodsBack' && accountrebatetype=='GoodsBack'){
        //充值金额*(1+客户返点)/(1+供应商返点)
        currentDiscount=currentDiscount*1+100;
        supprebate=supprebate*1+100;
        var discountratio=FloatDiv(currentDiscount,supprebate);
        var servicecost=FloatMul(rechargeamount,discountratio)*1;
    }else if(rebatetype=='CashBack' && accountrebatetype=='GoodsBack'){
        //值金额*(1+客户返点)*(1-供应商返点)
        currentDiscount=currentDiscount*1+100;
        supprebate=100-supprebate*1;
        var discountratio=FloatMul(currentDiscount,supprebate);
        discountratio=FloatDiv(discountratio,10000);
        var servicecost=FloatMul(rechargeamount,discountratio)*1;
    }else if(rebatetype=='GoodsBack' && accountrebatetype=='CashBack'){
        //充值金额/(1-客户返点)/(1+供应商返点)
        currentDiscount=100-currentDiscount*1;
        currentDiscount=FloatDiv(currentDiscount,100);
        supprebate=supprebate*1+100;
        supprebate=FloatDiv(supprebate,100);
        var discountratio=FloatDiv(rechargeamount,currentDiscount);
        var servicecost=FloatDiv(discountratio,supprebate)*1.0;
    }
    var topplatform = $(detail_type +' input[name="productservice_display"]').val();
    if(topplatform== GOOGLE_TOPPLATFORM || topplatform==Yandex_TOPPLATFORM) {
        var taxation = $(detail_type + ' input[name="taxation"]').val();
        if (!taxation) {
            taxation = 0;
        }
        servicecost = FloatAdd(servicecost, taxation);
    }
    return servicecost;
    /*currentDiscount = FloatAdd(1,FloatDiv(currentDiscount,100));
    rechargeamount = rechargeamount == ""?0:rechargeamount;
    var resultValue = FloatMul(rechargeamount,currentDiscount);
    return FloatDiv(resultValue,supprebate);*/
}
//谷歌成本计算(成本=充值账户币*90%+税费)
function servicecostCalcByGoogle(detail_type){
    var prestoreadrate = $(detail_type +' input[name="prestoreadrate"]').val();
    if(!prestoreadrate){
        prestoreadrate = 0;
    }
    var taxation = $(detail_type +' input[name="taxation"]').val();
    if(!taxation){
        taxation = 0;
    }
    var resultValue = FloatMul(prestoreadrate,0.9);
    return FloatAdd(resultValue,taxation);
}
//=========毛利总计计算(毛利总计 = 应收款金额 - 成本)============================================
function totalgrossprofitCalc(detail_type){
    var transferamount = $(detail_type +' input[name="transferamount"]').val();
    transferamount = transferamount == ""?0:transferamount;

    var servicecost = $(detail_type +' input[name="servicecost"]').val();
    servicecost = servicecost == ""?0:servicecost;

    return FloatSub(transferamount,servicecost);
}

//=========合计费用计算(合计费用 = 开户费+代理商服务费+税费)============================================
function totalcostCalc(detail_type){
    var transferamount = $(detail_type +' input[name="transferamount"]').val();
    transferamount = transferamount == ""?0:transferamount;
    var activationfee = $(detail_type +' input[name="activationfee"]').val();
    activationfee = activationfee == ""?0:activationfee;
    var factorage = $(detail_type +' input[name="factorage"]').val();
    factorage = factorage == ""?0:factorage;
    var taxation = $(detail_type +' input[name="taxation"]').val();
    taxation = taxation == ""?0:taxation;

    var resultValue = FloatAdd(activationfee,factorage);
    return FloatAdd(resultValue,taxation);
}
//=========现金充值计算(现金充值 = 应付款金额 - (开户费+代理商服务费+税费))============================================
function rechargeamountCalc(detail_type){

    var rebatetype = $(detail_type +' select[name="rebatetype"]').val();
    var transferamount = $(detail_type +' input[name="transferamount"]').val();
    transferamount = transferamount == ""?0:transferamount;
    var activationfee = $(detail_type +' input[name="activationfee"]').val();
    activationfee = activationfee == ""?0:activationfee;
    var factorage = $(detail_type +' input[name="factorage"]').val();
    factorage = factorage == ""?0:factorage;
    var taxation = $(detail_type +' input[name="taxation"]').val();
    taxation = taxation == ""?0:taxation;

    var resultValue = FloatAdd(activationfee,factorage);
    resultValue = FloatAdd(resultValue,taxation);

    return FloatSub(transferamount,resultValue);
}
//=========谷歌现金充值计算(充值账户币 = 现金充值*（1+返点）)============================================
function rechargeamountCalcByGoogle(detail_type){
    var prestoreadrate = $(detail_type +' input[name="prestoreadrate"]').val();
    if(!prestoreadrate){
        return 0;
    }
    var currentDiscount = $(detail_type +' input[name="discount"]').val();
    if(!currentDiscount){
        currentDiscount = 0;
    }
    var returndata=0;
    var accountrebatetype = $(detail_type +' select[name="accountrebatetype"]').val();
    currentDiscount = FloatDiv(currentDiscount, 100);
    if(accountrebatetype=='CashBack'){
        currentDiscount = FloatSub(1, currentDiscount);
        returndata=FloatMul(prestoreadrate,currentDiscount);
    }else{
        currentDiscount = FloatAdd(1, currentDiscount);
        returndata=FloatDiv(prestoreadrate,currentDiscount);
    }
    return returndata;
}
//=========充值账户币计算(计算充值账户币：充值账户币 = 现金充值*（1+返点）)============================================
function prestoreadrateCalc(detail_type){
    var topplatform = $(detail_type +' input[name="productid_display"]').val();
    var resultValue = $(detail_type +' input[name="rechargeamount"]').val();
    if(!resultValue){
        return 0;
    }
    var currentDiscount = $(detail_type +' input[name="discount"]').val();
    if(topplatform == GOOGLE_TOPPLATFORM || topplatform == Yandex_TOPPLATFORM){
        /*var tax=$(detail_type +" select[name='tax']").val().replace(/%/g,'');
        tax=FloatAdd(1,FloatDiv(tax,100));
        resultValue=FloatDiv(resultValue,tax);
        if(currentDiscount) {
            currentDiscount = currentDiscount > 0 ? currentDiscount : 0;
            currentDiscount = FloatDiv(currentDiscount, 100);
            currentDiscount = FloatAdd(1, currentDiscount);
            resultValue = FloatMul(resultValue, currentDiscount);
        }*/

        //谷歌：税费 = 充值账户币*6% =>充值账户币=税费/6%
        var taxation = $(detail_type +' input[name="taxation"]').val();
        if(!taxation){
            taxation = 0;
        }
        resultValue = FloatDiv(taxation,FloatDiv(6,100));
    }else{
        if(!currentDiscount){
            return 0;
        }
        var accountrebatetype = $(detail_type +' select[name="accountrebatetype"]').val();
        currentDiscount = currentDiscount > 0 ? currentDiscount : 0;
        currentDiscount = FloatDiv(currentDiscount, 100);
        if(accountrebatetype=='CashBack') {
            currentDiscount = FloatSub(1, currentDiscount);
            resultValue=FloatDiv(resultValue,currentDiscount)
        }else{
            currentDiscount = FloatAdd(1, currentDiscount);
            resultValue = FloatMul(resultValue, currentDiscount);
        }

    }
    return resultValue;
}

//=========应付款、应收款总额计算(明细应收款合计)============================================
function actualtotalrechargeCalc(){
    var resultValue1 = 0;//应收款总额
    var resultValue2 = 0;//应付款总额
    $("#rechargesheet li .cls_recharge_detail").each(function () {
        var num = $(this).val();
        var transferamount = $("input[name='transferamount["+num+"]']").val();
        resultValue1 = FloatAdd(resultValue1,transferamount);
        //应付款总额
        var servicecost = $("input[name='servicecost["+num+"]']").val();
        resultValue2 = FloatAdd(resultValue2,servicecost);
    })
    //应收款总额
   $("input[name='actualtotalrecharge']").val(resultValue1);
    //应付款总额（明细成本合计）
    if(refillapplicationtype == 'Vendors'){
        $("input[name='totalreceivables']").val(resultValue2);
    }
}
//=========现金总额计算(回款充值现金合计)============================================
function totalrechargeCalc(){
    var resultValue = 0;
    $("#div_receivedpayments_list .cls_receivedpayments_detail").each(function () {
        var receivedpaymentsid = $(this).val();
        var refillapptotal = $("input[name='refillapptotal["+receivedpaymentsid+"]']").val();
        if(refillapptotal != ""){
            resultValue = FloatAdd(resultValue,refillapptotal)
        }
    })
    $("#totalrecharge").val(resultValue);
}
//===================遮罩层=============================================================================================
function mark(page_mark, type) {
    if(type == 'show') {
        //加载一个遮罩层
        $(page_mark).popup('open');
        document.getElementById("bg").style.display="block";
        $('html,body').animate({scrollTop: '0px'}, 100);
        $('#bg').bind("touchmove",function(e){
            e.preventDefault();
        });
    } else {
        $(page_mark).popup('close');
        document.getElementById("bg").style.display="none";
    }
}
//======================================================================================================================
//设置申请单类型
function set_rechargesource(v) {
    if(v == 'Vendors'){
        //$("#goto-detail-page").attr("href","#detail-page-vendors");
        $("#div_totalpayable").show();
        $("#sp_detail_name").text("采购明细");
        $("#paymentperiodshow").show();
        $("#div_vendors_info").show();
    }else{
        //$("#goto-detail-page").attr("href","#detail-page-account");
        $("#div_totalpayable").hide();
        $("#sp_detail_name").text("充值明细");
        $("#paymentperiodshow").hide();
        $("#div_vendors_info").hide();
    }

    $("#goto-detail-page").attr("href","#");
    refillapplicationtype = v;
    idtopplatform = {};
    detail_num = 0;
    //清除充值明细
    $('#rechargesheet').empty();
    //删除回款信息
    $("#div_receivedpayments_list").empty();
}

//保存(设置充值明细数据)
function getDetailsPageData(e) {
    var title = '';
    var detail_form = "";
    var popupBasicform=refillapplicationtype == 'Vendors'?'popupBasicform2':'popupBasicform'
    var popupfrommsg=refillapplicationtype == 'Vendors'?'popupfrommsg2':'popupfrommsg'
    if(refillapplicationtype == 'Vendors') {
        var did = $.trim($('#did2').find("option:selected").text()); //服务产品
        if (!did) {
            $("#main_page_popup").find('div').text($('#did').attr('check-msg'));
            $("#main_page_popup").popup('open');
            return;
        }

        var productservice=$('#productservice_display').val();
        $("#productid2").val($('#productservice').val());
        $("#productid_name2").val(productservice);

        /*var suppliercontractsid = $.trim($('#suppliercontractsid').val()); //供应商合同
        if (!suppliercontractsid) {
            $("#main_page_popup").find('div').text($('#suppliercontractsid').attr('check-msg'));
            $("#main_page_popup").popup('open');
            return;
        }*/
        var suppretotal = $.trim($('#suppretotal').val()); //供应商合同金额
        var servicecost2 = $.trim($('#servicecost2').val()); //成本
        if(suppretotal!=0 && FloatSub(servicecost2,suppretotal)>0){
            $("#main_page_popup").find('div').text($('#servicecost2').attr('check-msg'));
            $("#main_page_popup").popup('open');
            return;
        }

        title = productservice;
        detail_form = 'detail_page_form2';
    }else{
        var topplatform = $.trim($('input[name=productid_display]').val()); //充值平台
        //var accountzh = $.trim($('select[name=did]').val());
        var accountzh = $.trim($('input[name=did]').val());

        if (!accountzh) {
            $("#main_page_popup").find('div').text($('#did_display_hide').attr('check-msg'));
            $("#main_page_popup").popup('open');
            return;
        }
        title = accountzh + '['+topplatform+']';
        detail_form = 'detail_page_form';
    }

    var flag = true;
    $('#'+detail_form+' input,#'+detail_form+' select').each(function() {
        var check = $(this).attr('check');
        check = check ? check : '';
        if (check.indexOf('notEmpty') >= 0) {
            var v = $.trim($(this).val());
            if (!v) {
                //$("#popupBasicform").find('#popupfrommsg').text($(this).attr('check-msg'));
                $("#"+popupBasicform).find('#'+popupfrommsg).text($(this).attr('check-msg'));
                $("#"+popupBasicform).popup('open');
                flag = false;
                return false;
            }
        }
        if (check.indexOf('number') >= 0) {
            var v = $.trim($(this).val());
            if (isNaN(v)) {
                //$("#popupBasicform").find('#popupfrommsg').text($(this).attr('check-msg'));
                $("#"+popupBasicform).find('#'+popupfrommsg).text($(this).attr('check-msg'));
                $("#"+popupBasicform).popup('open');
                flag = false;
                return false;
            }
        }
    });
    if (!flag) {
        return ;
    }

    var page_source = '';
    if(refillapplicationtype == 'Vendors'){
        //供应商
        page_source = $('#detail-page-vendors').attr('source');
    }else{
        //客户
        page_source = $('#detail-page-account').attr('source');
    }

    if (page_source) {
        detail_num = page_source.replace('inserti', '');
    } else {
        detail_num ++;
    }

    var hidden_html = '<input type="hidden" name="inserti['+detail_num+']" class="cls_recharge_detail" value="'+detail_num+'">';
    $('#'+detail_form+' input,#'+detail_form+' select').each(function() {
        var v = $.trim($(this).val());
        var name = $(this).attr('name');
        if (name == 'num') {
            //hidden_html += '<input type="hidden" name="'+name+'" value="t'+num+'">';
            hidden_html += '';
        } else {
            hidden_html += '<input type="hidden" name="'+name+'['+detail_num+']" value="'+v+'">';
        }
    });

    if (page_source) {
        $('#inserti'+detail_num).remove();
    }
    var str='<li data-icon="delete" id="inserti'+detail_num+'"><a href="'+page_source+'" class="rechargesheet_edit" >'+title+'</a><a href="javascript:void(0)" class="rechargesheet_delete">data-icon="delete"</a>'+hidden_html+'</li>';
    $('#rechargesheet').append(str);

    //计算应付款总额(基本信息栏)
    actualtotalrechargeCalc();
    /*var page_source = $('#detail-page').attr('source');
    if (page_source) {
        alert(title);
        var str='<a href="#detail-page" class="rechargesheet_edit" >'+title+'</a><a href="javascript:void(0)" class="rechargesheet_delete">data-icon="delete"</a>'+hidden_html+'';
        $('#t'+page_source).html(str);
    } else {
        var str='<li data-icon="delete" id="t'+num+'"><a href="#detail-page" class="rechargesheet_edit" >'+title+'</a><a href="javascript:void(0)" class="rechargesheet_delete">data-icon="delete"</a>'+hidden_html+'</li>';
        $('#rechargesheet').append(str);
    }*/

    //$('#detail-page-back').trigger("click");
    $.mobile.changePage( "#demo-intro",  "slideup");
    //$('#demo-intro').page();
    $("#rechargesheet" ).listview("refresh");
    //window.history.go(-1);
}

// 修改充值明细
function edit_rechargesheet($li) {
    var numflag = $li.attr('id').replace('inserti', '');
    var productid = "";
    var productname = "";
    var did = "";
    $li.find('select,input').each(function (){
        var name = $(this).attr('name');
        name = name.replace('['+numflag+']', '');

        if(name =='rechargetypedetail'|| name =='receivementcurrencytype'){
            $('select[name='+name +']').val($(this).val());
        }else{
            $('input[name='+name +']').val($(this).val());
        }

        if(name == "productid"){
            productid = $(this).val();
        }
        if(name == "productid_display"){
            productname = $(this).val();
        }
        if(name == "did"){
            did = $(this).val();
        }
    });

    if(productname == GOOGLE_TOPPLATFORM || productname == Yandex_TOPPLATFORM){
        $("#detail-page-account input[name='prestoreadrate']").removeAttr('readonly');//充值账户币
        $("#detail-page-account input[name='taxation']").attr('readonly',"true");//税费
        $("#detail-page-account input[name='transferamount']").attr("readonly","true");//应收款金额
    }else{
        $("#detail-page-account input[name='prestoreadrate']").attr('readonly',"true");//充值账户币
        $("#detail-page-account input[name='taxation']").removeAttr('readonly');//税费
        $("#detail-page-account input[name='transferamount']").removeAttr("readonly");//应收款金额
    }

    if(refillapplicationtype == 'Vendors'){
        $('#productservice').empty();
        var optionStr = '<option value="' + productid + '">' + productname + '</option>';
        $('#productservice').append(optionStr);
        $('#productservice').selectmenu('refresh');

        $('#detail-page-vendors').attr('source', numflag);
        //设置跳转页
        $(".rechargesheet_edit").attr("href","#detail-page-vendors");
    }else{
        $('#did').empty();
        var optionStr = '<option value="' + did + '">' + did + '</option>';
        $('#did').append(optionStr);
        $('#did').selectmenu('refresh');

        $('#detail-page-account').attr('source', numflag);

        //设置跳转页
        $(".rechargesheet_edit").attr("href","#detail-page-account");
    }
}
// 删除充值明细
function remove_rechargesheet($li) {
    $li.remove();
    $("#rechargesheet" ).listview("refresh");
    actualtotalrechargeCalc();
}
// 跳转到充值明细-客户
function goto_rechargesheet_account() {
    //验证是否有合同
    var servicecontractsid = $('#servicecontractsid').val();
    if (!servicecontractsid) {
        $("#main_page_popup").find('div').text($('#servicecontractsid').attr('check-msg'));
        $("#main_page_popup").popup('open');
        return;
    }

    //check客户是否为空
    var accountid = $('#accountid').val();
    if (!accountid) {
        $("#main_page_popup").find('div').text($('#accountid').attr('check-msg'));
        $("#main_page_popup").popup('open');
        return false;
    }

    $('#detail-page-account').attr('source', '');
    //设置跳转页
    $("#goto-detail-page").attr("href","#detail-page-account");

    //清空数据
    $('#detail_page_form input').each(function(){
        var  name = $(this).attr('name');
        if (name == 'exchangerate') {
            $(this).val('1.0');
        } else {
            $(this).val('');
        }
        //$("#discount").val(idtopplatform[i]['accountrebate']); //返点

    });
    /*
    $.ajax({
        url: "index.php?module=RefillApplication&action=search_accountplatform",
        data: {
            search_value : accountid
        },
        type:'POST',
        beforeSend:function() {
            //mark('#rechargesheet_page_popup', 'show');
        },
        success: function(data){
            //mark('#rechargesheet_page_popup', 'none');
            data = JSON.parse(data);

            idtopplatform = data;
            if (data) {
                var optionStr='';
                for(var i in data) {
                    if(!checkDidExist(data[i]['idaccount'])){
                        //客户
                        if(i==0){
                            optionStr += '<option value="' + data[i]['idaccount'] + '" selected="selected">' + data[i]['idaccount'] + '</option>';
                        }else{
                            optionStr += '<option value="' + data[i]['idaccount'] + '">' + data[i]['idaccount'] + '</option>';
                        }
                    }
                }
                $('#did').empty();
                $('#did').append(optionStr);
                $('#did').selectmenu('refresh');

                set_return_accountplatform_info();
            }

            $('#detail-page-account').attr('source', '');
        }
    });*/
}
// 跳转到充值明细-供应商
function goto_rechargesheet_vendor() {
    //验证是否有合同
    var servicecontractsid = $('#servicecontractsid').val();
    if (!servicecontractsid) {
        $("#main_page_popup").find('div').text($('#servicecontractsid').attr('check-msg'));
        $("#main_page_popup").popup('open');
        return;
    }

    //check供应商是否为空
    var vendorid = $('#vendorid').val();
    if (!vendorid) {
        $("#main_page_popup").find('div').text($('#vendorid').attr('check-msg'));
        $("#main_page_popup").popup('open');
        return false;
    }
    $('#detail-page-vendors').attr('source', '');
    //设置跳转页
    $("#goto-detail-page").attr("href","#detail-page-vendors");

    //清空数据
    $('#detail_page_form2 input').each(function(){
        var  name = $(this).attr('name');
        if (name == 'exchangerate') {
            $(this).val('1.0');
        } else {
            $(this).val('');
        }
        //$("#discount").val(idtopplatform[i]['accountrebate']); //返点

    });
    var accountid=$('input[name="accountid"]').val();
    $.ajax({
        url: "index.php?module=RefillApplication&action=search_vendor_productservice",
        data: {
            search_value : vendorid,
            'accountid':accountid
        },
        type:'POST',
        beforeSend:function() {
            //mark('#rechargesheet_page_popup', 'show');
        },
        success: function(data){
            //mark('#rechargesheet_page_popup', 'none');
            data = JSON.parse(data).productprovider;

            idtopplatform = data;
            if (data) {
                var optionStr='';
                for(var i in data) {
                    if(!checkProductidExist(data[i]['idaccount'])) {
                        //供应商
                        //optionStr += '<option value="' + data[i]['productid'] + '">' + data[i]['productname'] + '</option>';
                        optionStr += '<option value="' + data[i]['idaccount'] + '">' + data[i]['idaccount'] + '</option>';
                    }
                }
                /*$('#productservice').empty();
                $('#productservice').append(optionStr);
                $('#productservice').selectmenu('refresh');*/
                $('#did2').empty();
                $('#did2').append(optionStr);
                $('#did2').selectmenu('refresh');

                //$('#rechargetypedetail2').selectmenu('refresh');
                //$('#receivementcurrencytype2').selectmenu('refresh');
                //$('#tax2').selectmenu('refresh');

                set_return_vendorsplatform_info();
            }

            $('#detail-page-vendors').attr('source', '');
            $("#detail-page-vendors" ).trigger("create");
        }
    });
}
//服务合同检索
function search_servicecontracts() {
    var search_servicecontracts = $.trim($('input[name=search_servicecontracts]').val());
    if (!search_servicecontracts) {
        return;
    }
    $.ajax({
        url: "index.php?module=RefillApplication&action=search_servicecontracts",
        data: {
            search_servicecontracts : search_servicecontracts
        },
        type:'POST',
        beforeSend:function() {
            mark('#servicecontracts_page_popup', 'show');
        },
        success: function(data){
            mark('#servicecontracts_page_popup', 'none');

            data = JSON.parse(data);
            if (data) {
                var str = '';
                for(var i in data) {
                    var servicecontractsid = data[i]['servicecontractsid'];
                    var contract_no = data[i]['contract_no'];
                    var accountname = data[i]['accountname'];
                    var signdate = data[i]['signdate'];
                    var iscontracted = data[i]['iscontracted'];
                    var accountid = data[i]['accountid'];
                    var contractamount = data[i]['total'];
                    var customertype = data[i]['customertype'];

                    str += '<li><a href="#demo-intro" customertype="'+customertype+'"signdate="'+signdate+'" iscontracted="'+iscontracted+'" accountname="'+accountname+'" accountid="'+accountid+'" contract_no="'+contract_no+'" accountname="'+accountname+'" servicecontractsid="'+servicecontractsid+'" contractamount="'+contractamount+'" class="servicecontracts_list_li">'+contract_no+'['+accountname+']</a></li>';
                }
                if (str=='') {
                    $('#servicecontracts_list_display').show();
                } else {
                    $('#servicecontracts_list_display').hide();
                }
                $('#servicecontracts_list').html('');
                $('#servicecontracts_list').append(str);
                $("#servicecontracts_list" ).listview("refresh");
            }
        }
    });
}
//服务合同返回信息设置
function set_search_servicecontracts_info(o) {
    var contract_no = o.attr('contract_no');
    var accountname = o.attr('accountname');
    var servicecontractsid = o.attr('servicecontractsid');
    var accountid = o.attr('accountid');
    var accountname = o.attr('accountname');
    var signdate = o.attr('signdate');
    var iscontracted = o.attr('iscontracted');
    var contractamount = o.attr('contractamount');
    var customertype = o.attr('customertype');

    $('#customertype_display').val(customertype=='StraightCustomers'?'直客':'渠道');
    $('#customertype').val(customertype);

    $('#accountid').val(accountid);
    $('#accountid_dispaly').val(accountname);
    $('#servicecontractsid').val(servicecontractsid);
    $('#servicecontractsid_dispaly').val(contract_no);
    $('#servicesigndate').val(signdate);
    $('#iscontracted_display').val(iscontracted=='alreadySigned'?'已签订':'未签订');
    $('#iscontracted').val(iscontracted);
    $('#contractamount').val(contractamount);
    //$("#iscontracted").val(iscontracted).slider('refresh');
    //获取回款信息
    search_receivedpayments(servicecontractsid);
}

//回款检索
function search_receivedpayments(search_servicecontracts) {
    if (!search_servicecontracts) {
        return;
    }
    $('#div_receivedpayments_list').html("");
    //$('#div_receivedpayments_list').append("<h3>回款明细</h3>");
    $.ajax({
        url: "index.php?module=RefillApplication&action=search_receivedpayments",
        data: {
            servicecontractid : search_servicecontracts
        },
        type:'POST',
        beforeSend:function() {
            //mark('#servicecontracts_page_popup', 'show');
        },
        success: function(data){
            //mark('#servicecontracts_page_popup', 'none');

            data = JSON.parse(data);
            if (data) {
                //var str = '<h3 class="ui-collapsible-heading ui-collapsible-heading-collapsed"><a href="#" class="ui-collapsible-heading-toggle ui-btn ui-icon-carat-d ui-btn-icon-left ui-btn-inherit">回款明细<span class="ui-collapsible-heading-status"> click to expand contents</span></a></h3>';
                var num = 1;
                var str="";
                for(var i in data) {
                    str += '<div data-role="collapsible" data-collapsed-icon="carat-d" data-expanded-icon="carat-u">';
                    str += '<h3>回款明细('+num+')</h3>';
                    str += '<input type="hidden" class="cls_receivedpayments_detail" name="insertii['+data[i]["receivedpaymentsid"]+']" value="'+data[i]["receivedpaymentsid"]+'">';
                    str += '<input type="hidden" class="invoicecompany" name="owncompany['+ data[i]["receivedpaymentsid"] +']"  value="'+data[i]["owncompany"]+'">';
                    str += '<label class="t_label select" >回款信息 : </label><select name="paytitle['+data[i]["receivedpaymentsid"]+']" class="form-control" data-id="'+data[i]["receivedpaymentsid"]+'" ><option value="'+ data[i]["paytitle"]+'">'+ data[i]["owncompany"] + data[i]["paytitle"]+ '</option></select></label>';
                    str += '<label class="t_label select" >入账日期 : <input type="text" name="reality_date['+data[i]["receivedpaymentsid"]+']" value="'+data[i]["reality_date"]+'" readonly></label>';
                    str += '<label class="t_label select" >入账金额 : <input type="number" name="unit_price['+data[i]["receivedpaymentsid"]+']" value="'+data[i]["unit_price"]+'" readonly></label>';
                    str += '<label class="t_label select" >已使用工单金额 : <input type="number" name="occupationcost['+data[i]["receivedpaymentsid"]+']" value="'+data[i]["occupationcost"]+'" readonly></label>';
                    str += '<label class="t_label select" >可使用金额 : <input type="number" name="rechargeableamount['+data[i]["receivedpaymentsid"]+']" value="'+data[i]["rechargeableamount"]+'" readonly></label>';
                    str += '<label class="t_label select" >使用金额 : <input type="number" onchange="totalrechargeCalc()" name="refillapptotal['+data[i]["receivedpaymentsid"]+']" value="" step="0.01"></label>';
                    str += '<label class="t_label select" >备注 : <textarea name="remarkss['+data[i]["receivedpaymentsid"]+']" rows="3" class="form-control" data-content=""></textarea></label>';
                    str += '</div>';

                    num++;
                }
            }
            $('#div_receivedpayments_list').html(str);
            $("#div_receivedpayments_list" ).trigger("create");
        }
    });
}

//供应商检索
function search_vendors() {
    var search_vendors = $.trim($('input[name=search_vendors]').val());
    if (!search_vendors) {
        return;
    }
    $.ajax({
        url: "index.php?module=RefillApplication&action=search_vendors",
        data: {
            search_vendors : search_vendors
        },
        type:'POST',
        beforeSend:function() {
            mark('#vendors_page_popup', 'show');
        },
        success: function(data){
            mark('#vendors_page_popup', 'none');

            data = JSON.parse(data);
            if (data) {
                var str = '';
                for(var i in data) {
                    var vendorid = data[i]['vendorid'];//供应商id
                    var vendor_no = data[i]['vendor_no'];//供应商NO
                    var vendorname = data[i]['vendorname'];//供应商名称
                    var bankaccount = data[i]['bankaccount'];//开户行
                    var bankname = data[i]['bankname'];//开户名
                    var banknumber = data[i]['banknumber'];//银行账号
                    str += '<li><a href="#demo-intro" class="vendors_list_li" ' +
                        'vendorid="'+vendorid+'"' +
                        'vendorname="'+vendorname+'"' +
                        'bankaccount="'+bankaccount+'"' +
                        'bankname="'+bankname+'"'+
                        'banknumber="'+banknumber+'"'+
                        '>'+vendorname+'['+vendor_no+']</a></li>';
                }
                if (str=='') {
                    $('#vendor_list_display').show();
                } else {
                    $('#vendor_list_display').hide();
                }
                $('#vendor_list').html('');
                $('#vendor_list').append(str);
                $("#vendor_list" ).listview("refresh");
            }
        }
    });
}
// 设置返回供应商信息
function set_search_vendor_info(o) {
    var vendorid = o.attr('vendorid');
    var vendorname = o.attr('vendorname');
    var bankaccount = o.attr('bankaccount');
    var bankname = o.attr('bankname');
    var banknumber = o.attr('banknumber');

    $('#vendorid').val(vendorid);
    $('#vendorid_display').val(vendorname);
    $('#bankaccount').val(bankaccount);
    $('#bankname').val(bankname);
    $('#banknumber').val(banknumber);
}

//设置返回平台信息-客户
function set_return_accountplatform_info() {
    var data = idtopplatform || {};
    var did = $("#did").val();
    for(var i in data){
        if(did == data[i]['idaccount']){
            $("#accountzh").val(data[i]['accountplatform']); //账户
            var topplatform = data[i]['topplatform'];

            $("#detail-page-account input[name='prestoreadrate']").val("");
            $("#detail-page-account input[name='taxation']").val("");
            $("#detail-page-account input[name='transferamount']").val("");
            $("#detail-page-account input[name='rechargeamount']").val("");
            $("#detail-page-account input[name='discount']").val("");
            $("#detail-page-account input[name='factorage']").val("");
            $("#detail-page-account input[name='activationfee']").val("");
            $("#detail-page-account input[name='taxation']").val("");
            $("#detail-page-account input[name='totalcost']").val("");
            $("#detail-page-account input[name='servicecost']").val("");
            $("#detail-page-account input[name='totalgrossprofit']").val("");

            if(topplatform == GOOGLE_TOPPLATFORM || topplatform ==Yandex_TOPPLATFORM){
                $("#detail-page-account input[name='prestoreadrate']").removeAttr('readonly');//充值账户币
                $("#detail-page-account input[name='taxation']").attr('readonly',"true");//税费
                $("#detail-page-account input[name='transferamount']").attr("readonly","true");//应收款金额
            }else{
                $("#detail-page-account input[name='prestoreadrate']").attr('readonly',"true");//充值账户币
                $("#detail-page-account input[name='taxation']").removeAttr('readonly');//税费
                $("#detail-page-account input[name='transferamount']").removeAttr("readonly");//应收款金额
            }

            $("#detail-page-account input[name='productid_display']").val(topplatform); //充值平台

            var customeroriginattr = data[i]['customeroriginattr'];
            $("#detail-page-account input[name='customeroriginattr']").val(customeroriginattr); //客户来源属性
            $("#detail-page-account input[name='customeroriginattr_display']").val(customeroriginattr=='nonfree'?'非自有':'自有'); //客户来源属性

            var isprovideservice = data[i]['isprovideservice'];
            $("#detail-page-account input[name='isprovideservice']").val(isprovideservice); //有无服务
            $("#detail-page-account input[name='isprovideservice_display']").val(isprovideservice=='on'?'有':'无'); //有无服务

            //充值类型
            var rechargetypedetail = data[i]['rechargetypedetail'];
            var str = "<option value='renew' >续费</option>";
            $("#detail-page-account select[name='rechargetypedetail']").empty();
            if(rechargetypedetail == 'renew'){
                //充值类型根据充值明细ID抓取历史，出现第二次默认为续费类型，不可改
                $("#detail-page-account select[name='rechargetypedetail']").append(str);
            }else{
                str += "<option value='OpenAnAccount' selected>开户</option>";
                $("#detail-page-account select[name='rechargetypedetail']").append(str);
            }

            $("#detail-page-account select[name='rechargetypedetail']").selectmenu('refresh');
            //客户返点类型
            var accountrebatetype=data[i]['accountrebatetype']=='CashBack'?'<option value="CashBack" selected>返现</option>':'<option value="GoodsBack" selected >返货</option>';
            $("#detail-page-account select[name='accountrebatetype']").empty();
            $("#detail-page-account select[name='accountrebatetype']").append(accountrebatetype);
            $("#detail-page-account select[name='accountrebatetype']").selectmenu('refresh');
            //供应商返点类型
            var rebatetype=data[i]['rebatetype']=='CashBack'?'<option value="CashBack" selected>返现</option>':'<option value="GoodsBack" selected>返货</option>';
            $("#detail-page-account select[name='rebatetype']").empty();
            $("#detail-page-account select[name='rebatetype']").append(rebatetype);
            $("#detail-page-account select[name='rebatetype']").selectmenu('refresh');
            $("#detail-page-account input[name='productid']").val(data[i]['productid']); //产品id

            $("#detail-page-account input[name='discount']").val(data[i]['accountrebate']); //返点

            $("#detail-page-account input[name='supprebate']").val(data[i]['supplierrebate']); //供应商返点
            $("#did_display_hide").val(did);

            break;
        }
    }
}
//设置返回平台信息-供应商
function set_return_vendorsplatform_info() {
    var data = idtopplatform || {};
    var productservice = $("#did2").val();
    for(var i in data){
        //if(productservice == data[i]['productid']){
        if(productservice == data[i]['idaccount']){
            var topplatform = data[i]['productname'];

            $("#detail-page-vendors input[name='prestoreadrate']").val("");
            $("#detail-page-vendors input[name='taxation']").val("");
            $("#detail-page-vendors input[name='transferamount']").val("");
            if(topplatform == GOOGLE_TOPPLATFORM || topplatform ==Yandex_TOPPLATFORM){
                $("#detail-page-vendors input[name='prestoreadrate']").removeAttr('readonly');//充值账户币
                $("#detail-page-vendors input[name='taxation']").attr('readonly',"true");//税费
                $("#detail-page-vendors input[name='transferamount']").attr("readonly","true");//应收款金额
            }else{
                $("#detail-page-vendors input[name='prestoreadrate']").attr('readonly',"true");//充值账户币
                $("#detail-page-vendors input[name='taxation']").removeAttr('readonly');//税费
                $("#detail-page-vendors input[name='transferamount']").removeAttr("readonly");//应收款金额
            }

            $("#detail-page-vendors input[name='productid_display']").val(topplatform); //充值平台
            $("#detail-page-vendors input[name='productid']").val(data[i]['productid']); //充值平台

            $("#detail-page-vendors input[name='productservice_display']").val(topplatform); //产品服务
            $("#detail-page-vendors input[name='productservice']").val(data[i]['productid']); //产品服务

            //客户返点类型
            var accountrebatetype=data[i]['accountrebatetype']=='CashBack'?'<option value="CashBack" selected>返现</option>':'<option value="GoodsBack" selected >返货</option>';
            $("#detail-page-vendors select[name='accountrebatetype']").empty();
            $("#detail-page-vendors select[name='accountrebatetype']").append(accountrebatetype);
            $("#detail-page-vendors select[name='accountrebatetype']").selectmenu('refresh');
            //供应商返点类型
            var rebatetype=data[i]['rebatetype']=='CashBack'?'<option value="CashBack" selected>返现</option>':'<option value="GoodsBack" selected>返货</option>';
            $("#detail-page-vendors select[name='rebatetype']").empty();
            $("#detail-page-vendors select[name='rebatetype']").append(rebatetype);
            $("#detail-page-vendors select[name='rebatetype']").selectmenu('refresh');

            //$("#detail-page-vendors input[name='did']").val(data[i]['idaccount']); //账户id
            $("#detail-page-vendors input[name='accountzh']").val(data[i]['accountzh']); //账户名称

            //设置供应商合同
            var contract_no = data[i]['contract_no'];
            var modulestatus = data[i]['modulestatus'];
            if(contract_no){
                $("#detail-page-vendors input[name='suppliercontractsid_display']").val(contract_no); //供应商合同
                $("#detail-page-vendors input[name='suppliercontractsid']").val(data[i]['suppliercontractsid']); //供应商合同
                $("#detail-page-vendors .cls_servicecontracts_select").hide();

                $("#detail-page-vendors input[name='signdate']").val(data[i]['signdate']); //签订日期

                if(modulestatus=='c_complete'){
                    $("#detail-page-vendors input[name='havesignedcontract_display']").val("已签订"); //是否签订合同
                    $("#detail-page-vendors input[name='havesignedcontract']").val("alreadySigned"); //是否签订合同
                }else{
                    $("#detail-page-vendors input[name='havesignedcontract_display']").val("未签订"); //是否签订合同
                    $("#detail-page-vendors input[name='havesignedcontract']").val("notSigned"); //是否签订合同
                }
            }else{
                $("#detail-page-vendors .cls_servicecontracts_select").show();
            }

            var customeroriginattr = data[i]['customeroriginattr'];
            $("#detail-page-vendors input[name='customeroriginattr']").val(customeroriginattr); //客户来源属性
            $("#detail-page-vendors input[name='customeroriginattr_display']").val(customeroriginattr=='nonfree'?'非自有':'自有'); //客户来源属性

            var isprovideservice = data[i]['isprovideservice'];
            $("#detail-page-vendors input[name='isprovideservice']").val(isprovideservice); //有无服务
            $("#detail-page-vendors input[name='isprovideservice_display']").val(isprovideservice=='on'?'有':'无'); //有无服务

            //充值类型
            var rechargetypedetail = data[i]['rechargetypedetail'];
            var str = "<option value='renew' >续费</option>";
            $("#detail-page-vendors select[name='rechargetypedetail']").empty();
            if(rechargetypedetail == 'renew'){
                //充值类型根据充值明细ID抓取历史，出现第二次默认为续费类型，不可改
                $("#detail-page-vendors select[name='rechargetypedetail']").append(str);
            }else{
                str += "<option value='OpenAnAccount' selected>开户</option>";
                $("#detail-page-vendors select[name='rechargetypedetail']").append(str);
            }
            $("#detail-page-vendors select[name='rechargetypedetail']").selectmenu('refresh');

            $("#detail-page-vendors input[name='discount']").val(data[i]['accountrebate']); //返点

            $("#detail-page-vendors input[name='supprebate']").val(data[i]['supplierrebate']); //供应商返点

            //合同金额
            $("#detail-page-vendors input[name='suppretotal']").val(data[i]['total']);
            break;
        }
    }
    $("#detail-page-vendors input[name='productid_display']").val($('#detail-page-vendors select[name="productservice"]').find("option:selected").text());
}
//供应商服务合同检索
function search_vendor_servicecontracts(){
    var search_servicecontracts = $.trim($('input[name=search_vendor_servicecontracts]').val());
    if (!search_servicecontracts) {
        return;
    }
    $.ajax({
        url: "index.php?module=RefillApplication&action=search_vendors_servicecontracts",
        data: {
            search_servicecontracts : search_servicecontracts
        },
        type:'POST',
        beforeSend:function() {
            mark('#vendor_servicecontracts_page_popup', 'show');
        },
        success: function(data){
            mark('#vendor_servicecontracts_page_popup', 'none');

            data = JSON.parse(data);
            if (data) {
                var str = '';
                for(var i in data) {
                    var servicecontractsid = data[i]['suppliercontractsid'];
                    var contract_no = data[i]['contract_no'];
                    var vendorname = data[i]['vendorname'];
                    var vendorid = data[i]['vendorid'];
                    var signdate = data[i]['signdate'];
                    var iscontracted = data[i]['iscontracted'];

                    str += '<li><a href="#detail-page-vendors" signdate="'+signdate+ '" iscontracted="'+iscontracted+ '" vendorid="'+vendorid+ '" vendorname="'+vendorname+'" suppliercontractsid="'+servicecontractsid+'" contract_no="'+contract_no+'" class="vendor_servicecontracts_list_li">'+contract_no+'['+vendorname+']</a></li>';
                }
                if (str=='') {
                    $('#vendor_servicecontracts_list_display').show();
                } else {
                    $('#vendor_servicecontracts_list_display').hide();
                }
                $('#vendor_servicecontracts-page_list').html('');
                $('#vendor_servicecontracts-page_list').append(str);
                $("#vendor_servicecontracts-page_list" ).listview("refresh");

            }
        }
    });
}
//供应商合同返回信息设置
function set_search_vendor_servicecontracts_info(o) {
    var contract_no = o.attr('contract_no');
    var suppliercontractsid = o.attr('suppliercontractsid');
    var signdate = o.attr('signdate');
    var iscontracted = o.attr('iscontracted');

    $('#suppliercontractsid').val(suppliercontractsid);
    $('#suppliercontractsid_display').val(contract_no);
    $('#signdate').val(signdate);
    $('#havesignedcontract_display').val(iscontracted=='alreadySigned'?'已签订':'未签订');
    $('#havesignedcontract').val(iscontracted);
    //$("#havesignedcontract").val(iscontracted).slider('refresh');
}

// 添加提交数据
function main_check() {
    // 服务合同不能为空
    var servicecontractsid = $('#servicecontractsid').val();
    if (!servicecontractsid) {
        $("#main_page_popup").find('div').text($('#servicecontractsid').attr('check-msg'));
        $("#main_page_popup").popup('open');
        return false;
    }

    var li_num = $('#rechargesheet').find('li').size();
    if (li_num == 0) {
        $("#main_page_popup").find('div').text('请添加充值明细');
        $("#main_page_popup").popup('open');
        return false;
    }

    //充值金额大于合同金额的验证
    var actualtotalrecharge=$('#actualtotalrecharge').val();//充值金额
    var contractamount=$('#contractamount').val();//合同金额
    if(FloatSub(contractamount,0)>0 && FloatSub(actualtotalrecharge,contractamount)>0){
        $("#main_page_popup").find('div').text('充值金额不能大于合同金额,请重新修改!');
        $("#main_page_popup").popup('open');
        return false;
    }

    //check 充值现金总额大于应付款总额
    var totalrecharge = $("#totalrecharge").val(); //使用回款总额
    totalrecharge = totalrecharge==""?0:totalrecharge;
    var actualtotalrecharge = $("#actualtotalrecharge").val(); //应收款总额
    if(FloatSub(totalrecharge,actualtotalrecharge)>0){
        $("#main_page_popup").find('div').text('使用回款总额不能大于应收款总额,请重新修改!');
        $("#main_page_popup").popup('open');
        return false;
    }

    //判断是否有垫款
    if (FloatSub(totalrecharge,actualtotalrecharge) < 0) {
        var expcashadvances = $('input[name=expcashadvances]').val();//垫款预计回款日期
        if (expcashadvances == '') {
            $("#main_page_popup").find('div').text('有垫款,垫款预计回款日期必填!');
            $("#main_page_popup").popup('open');
            return false;
        }

        var currentDate = new Date().getFullYear() + '\/' + (new Date().getMonth() + 1) + '\/' + new Date().getDate();
        if ((new Date(expcashadvances.replace(/-/g, '\/'))) < (new Date(currentDate))) {
            $("#main_page_popup").find('div').text('垫款预计回款日期应大于当前日期!');
            $("#main_page_popup").popup('open');
            return false;
        }

    }

    //check 垫款金额(应收款总额-充值现金总额==合同垫款金额)
    var grossadvances=$('#grossadvances').val();//合同垫款金额
    if(FloatSub(FloatSub(actualtotalrecharge,totalrecharge),grossadvances)!= 0){
        $("#main_page_popup").find('div').text('垫款金额不等,请重新修改!');
        $("#main_page_popup").popup('open');
        return false;
    }
    var accountid=$('input[name="accountid"]').val();
    if(grossadvances>0){
        var grossadvancesdata=checkAuditInformation(accountid,grossadvances);
        if(grossadvancesdata.flag){
            if(confirm(grossadvancesdata.msg)==false){
                return false;
            }
        }
    }
    //验证回款中 充值现金是否小于可使用充值金额
    var ck_error = false;
    var msg = "";
    $("#div_receivedpayments_list .cls_receivedpayments_detail").each(function (index) {
        var receivedpaymentsid = $(this).val();
        //可使用充值金额
        var rechargeableamount = $("input[name='rechargeableamount["+receivedpaymentsid+"]']").val();
        //充值现金
        var refillapptotal = $("input[name='refillapptotal["+receivedpaymentsid+"]']").val();
        if(FloatSub(refillapptotal,rechargeableamount)>0){
            ck_error = true;
            msg = "回款明细("+(index+1)+")中的充值现金不可大于使用充值金额，请修改";
            return true;
        }
    })
    if(ck_error){
        $("#main_page_popup").find('div').text(msg);
        $("#main_page_popup").popup('open');
        return false;
    }

    mark('#main_page_popup_submit', 'show');

    $.ajax({
        type: "POST",
        url: 'index.php?module=RefillApplication&action=doadd',
        data: $('#main_page_form').serialize(),// 你的formid
        success: function(data) {
            mark('#main_page_popup_submit', 'none');
            data = JSON.parse(data);
            //if(parseInt(data[1]) > 0){
            //    $("#main_page_popup").find('div').text('当前客户已垫款' + data[1] + '元');
            //}
            if(data.success==1){
                setTimeout(function() {
                    window.location.href="index.php?module=RefillApplication&action=index";
                }, 100);
                //$('#main_page_back').trigger('click');
            } else {
                alert(data.msg);
            }
        }
    });
    return false;
}
//确认账户id是否已添加明细
function checkDidExist(did) {
    var blcheck = false;
    $("#rechargesheet li .cls_recharge_detail").each(function () {
        var num = $(this).val();
        if(did == $("input[name='did["+num+"]']").val()){
            blcheck = true;
            return false;
        }
    })
    return blcheck;
}
//确认产品服务是否已添加明细
function checkProductidExist(productid) {
    var blcheck = false;
    $("#rechargesheet li .cls_recharge_detail").each(function () {
        var num = $(this).val();
        if(productid == $("input[name='productservice["+num+"]']").val()){
            blcheck = true;
            return false;
        }
    })
    return blcheck;
}
function checkAuditInformation(accountid,advancesmoney){
    var returndata={};
    $.ajax({
        "type":"POST",
        "url":'index.php?module=RefillApplication&action=checkAuditInformation',
        "data":{"accountid":accountid,"advancesmoney":advancesmoney},
        "async":false,
        "dataType":"json",
        "success":function(data){
            returndata=data
        }
    });
    return returndata;
}
$('body').on('click','#did_page_search',function(){
    didSearch();
});
$('#didpagelist').on("click","a.didpagelist_li", function() {
    $('input[name="did"]').val($(this).attr('didvalue'))
    $('input[name="did_display_hide"]').val($(this).attr('didvalue'))
    set_return_accountplatform_info();
    $('#detail-page-account').attr('source', '');
});
$(document).on( "pagebeforeshow", "#did-page", function( event ) {
    $('#didpagelist').html('');
});
function didSearch(){
    var did=$('#search_did').val();
    var search_did=$('#search_did').val();
    if(did==''){
        return ;
    }
    if(search_did==''){
        return '';
    }
    var accountid = $('#accountid').val();
    $.ajax({
        url: "index.php?module=RefillApplication&action=search_accountplatform",
        data: {
            search_value : accountid,
            search_did : search_did
        },
        type:'POST',
        beforeSend:function() {
            mark('#did_page_popup', 'show');
        },
        success: function(data){
            mark('#did_page_popup', 'none');
            data = JSON.parse(data);
            idtopplatform = data;
            if (data) {
                var str='';
                for(var i in data) {
                    if(!checkDidExist(data[i]['idaccount'])){
                        str += '<li><a href="#detail-page-account" didvalue="'+data[i]['idaccount'] +'" class="didpagelist_li">'+data[i]['idaccount']+'</a></li>';
                    }
                }
                $('#didpagelist').html('');
                $('#didpagelist').append(str);
                $("#didpagelist" ).listview("refresh");
                if (str=='') {
                    $('#did_list_display').show();
                } else {
                    $('#did_list_display').hide();
                }

            }
        }
    });
}