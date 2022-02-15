var idtopplatform={};
$(function () {
    //客户充值
    $('#detail_page_close').click(function() {
        getDetailsPageData();
    });
    // 跳转到 充值明细
    $('.add-detail-page').click(function(event) {
        event.stopPropagation();
        if (JSON.stringify(idtopplatform) == '{}') {
            return false;
        }
        var typecash=$(this).data('typecash');
        var msg=typecash=='in'?'转入明细':"转出明细";
        msg='确定要添加<span style="color:'+(typecash=='in'?'red':'blue')+';">'+msg+"</span>";
        Tips.confirm({
            content: msg,
            define: '确定',
            cancel: '取消',
            before: function(){
            },
            after: function(b){
                if(b){
                    var dlength=1+$('#main_page_form .Duplicates').length;
                    var currnetNum=0
                    $.each($($('#main_page_form .Duplicates')),function(key,value){
                        var valueNum=$(value).data('num');
                        if(valueNum>=currnetNum){
                            currnetNum=valueNum;
                        }
                    });
                    if (currnetNum>0) {
                        dlength = currnetNum + 1;
                    }
                    var str=$('#insert').html();
                    str=str.replace(/replacenum/g, dlength);
                    var replacename=typecash=='in'?'<span style="color:red">转入</span>':"<span style='color:blue;'>转出</span>";
                    str=str.replace(/replacename/g, replacename);
                    str=str.replace(/relacetype/g, typecash);
                    var insertposition=typecash=='in'?'insertafter':"insertbefore";
                    $('#'+insertposition).before(str);
                    $('#main_page_form select[name="mdid['+dlength+']"]').selectmenu();
                    $('#main_page_form select[name="mdid['+dlength+']"]').selectmenu("destroy");
                    $('#main_page_form select[name="mdid['+dlength+']"]').selectmenu();
                    $('#conset'+dlength).collapsible();
                    $('#conset'+dlength).collapsible("expand");
                    $('#conset'+dlength).collapsible("disable")
                    set_loading_accountplatform_info(dlength);
                }
            }
        });
    });
    $('#main_page_form').on('vclick','.deletedlist',function(event){
        event.stopPropagation();
        var that=$(this).parents('.Duplicates');
        Tips.confirm({
            content: '确定要删除吗?',
            define: '确定',
            cancel: '取消',
            before: function(){
            },
            after: function(b){
                if(b){
                    that.remove();
                    sumCashAccounttransfer();
                }
            }
        });
    });
    $('#main_page_form').on('change keyup','input[name="accounttransfer"],input[name^="maccounttransfer["]',function(event){
        var cid=$(this).data('cid');
        cid=cid>0?cid:0;
        formatNumber($(this));
        var arr=$(this).val().split('.');//只有一个小数点
        if(arr.length>2){
            if(arr[1]==''){
                $(this).val(arr[0]);
            }else{
                $(this).val(arr[0]+'.'+arr[1]);
            }
        }
        var thisValue=$(this).val();
        var maccountrebatetype=cid>0?'maccountrebatetype['+cid+']':"accountrebatetype";
        var mdiscount=cid>0?'mdiscount['+cid+']':"discount";
        var mcashtransfer=cid>0?'mcashtransfer['+cid+']':"cashtransfer";
        var mcashtransfervalue=0;
        var mdiscountValue=$('input[name="'+mdiscount+'"]').val();
        var maccountrebatetypeValue=$('input[name="'+maccountrebatetype+'"]').val();
        mdiscountValue=FloatDiv(mdiscountValue,100);
        if(maccountrebatetypeValue=='CashBack'){
            mdiscountValue=FloatSub(1,mdiscountValue);
            mcashtransfervalue=FloatMul(mdiscountValue,thisValue);
        }else{
            mdiscountValue=FloatAdd(1,mdiscountValue);
            mcashtransfervalue=FloatDiv(thisValue,mdiscountValue);
        }
        mcashtransfervalue*=1.0;
        $('input[name="'+mcashtransfer+'"]').val(mcashtransfervalue.toFixed(2));
        sumCashAccounttransfer();
    });
    // 服务合同搜索
    $('#servicecontracts_page_search').click(function() {
        search_servicecontracts();
    });
    // 点击服务合同
    $('#servicecontracts_list').on("click","a.servicecontracts_list_li", function() {
        set_search_servicecontracts_info($(this));
    });
    function sumCashAccounttransfer(){
        var thisInstance=this;
        var totalcashtransfer=0;//转出现金
        var totalturnoverofaccount=0;//转出转户币
        var totalcashin=0;//转入现金
        var totaltransfertoaccount=0;//转入账户币
        totalcashtransfer=$('input[name="cashtransfer"]').val();
        totalturnoverofaccount=$('input[name="accounttransfer"]').val();
        $.each($('input[name^="maccounttransfer["]'),function(key,value){
            var typecash=$(value).data('typecash');
            var cid=$(value).data('cid');
            var thisMcashtransfer=$('input[name="mcashtransfer['+cid+']"]').val();
            var thisValue=$(value).val();
            if(typecash=='in'){
                totaltransfertoaccount=FloatAdd(thisValue,totaltransfertoaccount);
                totalcashin=FloatAdd(thisMcashtransfer,totalcashin);
            }else{
                totalcashtransfer=FloatAdd(thisMcashtransfer,totalcashtransfer);
                totalturnoverofaccount=FloatAdd(thisValue,totalturnoverofaccount);
            }
        });
        totalcashtransfer*=1.0
        totalturnoverofaccount*=1.0
        totalcashin*=1.0
        totaltransfertoaccount*=1.0
        $('input[name="totalcashtransfer"]').val(totalcashtransfer.toFixed(2));
        $('input[name="totalturnoverofaccount"]').val(totalturnoverofaccount.toFixed(2));
        $('input[name="totalcashin"]').val(totalcashin.toFixed(2));
        $('input[name="totaltransfertoaccount"]').val(totaltransfertoaccount.toFixed(2));

    }
    function formatNumber(_this){
        _this.val(_this.val().replace(/,/g,''));//只能输入数字小数点
        _this.val(_this.val().replace(/[^0-9.]/g,''));//只能输入数字小数点
        _this.val(_this.val().replace(/^0\d{1,}/g,''));//不能以0打头的整数
        _this.val(_this.val().replace(/^\./g,''));//不能以小数点打头
        _this.val(_this.val().replace(/[\d]{13,}/g,''));//超出长度全部清空
        _this.val(_this.val().replace(/\.\d{3,}$/g,''));//小数点后两位如果超过两位则将小数后的所有数清空包含小数点
        _this.val(_this.val().replace(/\.\d*\.$/g,''));//小数点后两位如果超过两位则将小数后的所有数清空包含小数点
    }
    // 添加提交数据
    $('#main_page_form').submit(function() {
        // 服务合同不能为空
        var that,msg;
        var flag=false;
        do{
            var servicecontractsid = $('#servicecontractsid').val();
            if (!servicecontractsid) {
                that=$('#servicecontractsid');
                msg="合同不能为空!";
                flag=true;
                break
            }
            var  conversiontype=$("#conversiontype").val();
            var  vendorid=$("#vendorid").val();
            if(conversiontype=='ProductProvider' && !vendorid){
                that=$('#vendorid');
                msg="供应商不能为空!";
                flag=true;
                break;
            }
            sumCashAccounttransfer();
            var totalcashin=$('input[name="totalcashin"]').val();//合计转入现金
            var totalcashtransfer=$('input[name="totalcashtransfer"]').val();//合计转出现金
            if(totalcashin<=0){
                that=$('input[name="totalcashin"]');
                msg="转入,转出必需大于0!!";
                flag=true;
                break
            }
            if(FloatSub(totalcashin,totalcashtransfer)!=0){
                that=$('input[name="totalcashin"]');
                msg="转入,转出现金不等,无法进行下一步操作!";
                flag=true;
                break;

            }
            var did=$('select[name="did"]').val();
            var midobj=$('select[name^="mdid["]');
            var mid=[];
            $.each(midobj.serializeArray(), function(i, field){
                mid.push(field.value);
            });
            mid.push(did);
            var flag=false;
            var tempmid=[];
            $.each(mid,function(key,value){
                if($.inArray(value,tempmid)!=-1){
                    flag=true;
                    return false;
                }else{
                    tempmid.push(value);
                }
            });
            if(flag){
                that=$('select[name="did"]');
                msg="转入,转出ID重复不允许提交!";
                flag=true;
                break;
            }
        }while(0);
        if(flag){
            Tips.alert({
                content: msg
            });
            $(that).focus();
            return false;
        }
        mark('#main_page_popup_submit', 'show');
        $.ajax({
            type: "POST",
            url: 'index.php?module=RefillApplication&action=doaddCOINRETURN',
            data: $('#main_page_form').serialize(),// 你的formid
            success: function(data) {
                mark('#main_page_popup_submit', 'none');
                data = JSON.parse(data);
                //if(parseInt(data[1]) > 0){
                //    $("#main_page_popup").find('div').text('当前客户已垫款' + data[1] + '元');
                //}
                if(data.success==1){
                    Tips.confirm({
                        content: '添加成功是要继续添加?',
                        define: '确定',
                        cancel: '取消',
                        before: function(){
                        },
                        after: function(b){
                            if(b){
                                window.location.reload();
                            }else{
                                window.location.href='index.php?module=RefillApplication&action=index';
                            }
                        }
                    });
                } else {
                    Tips.alert({
                        content: data.msg
                    });
                }
            }
        });
        return false;
    });
    // 供应商搜索 cxh add 2020/04/27
    $('#search_vendors').on('keydown',function(event){
        if(event.keyCode==13){
            search_vendors();
            return false;
        }
    });
    // 供应商搜索 cxh add 2020/04/27
    $('#vendors_page_search').click(function() {
        search_vendors();
    });
    // 点击供应商
    $('#vendor_list').on("click","a.vendors_list_li", function() {
        set_search_vendor_info($(this));
    });
    // 如果选择的变更记录
    $('#conversiontype').change(function () {
         if($(this).val()=='ProductProvider'){
              var accountid= $("#accountid").val();
              var vendorid=$("#vendorid").val();
              $("#click-choose").css("display","block");
              if(accountid && vendorid){
                  getProductProvider(accountid,vendorid);
              }

         }else if($(this).val()=='AccountPlatform'){
             var accountid=$("#accountid").val();
             if(accountid){
                 getAccountPlatform(accountid);
             }
             $("#click-choose").css("display","none");
             $("#vendorid_display").val("");
             $("#vendorid").val("");
         }
    });

})
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
    // 判断是否已经选择了服务合同
    var servicecontractsid = $('#servicecontractsid').val();
    // 如果服务合同已经选择则 获取媒体外采账号信息
    if(servicecontractsid){
        var  accountid=$("#accountid").val();
        getProductProvider(accountid,vendorid);
    }
}
//媒体账户获取
function getAccountPlatform(accountid){
    var accountid=accountid;
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
            console.log(data);
            idtopplatform = data;
            if (data) {
                set_loading_accountplatform_info(0);
                set_loading_accountplatform_info(1);
            }
        }
    });
}
//媒体外采账户获取
function getProductProvider(accountid,vendorid){
    var vendorid=vendorid;
    var accountid=accountid;
    $.ajax({
        url: "index.php?module=RefillApplication&action=search_vendor_productservice",
        data: {
            'search_value' : vendorid,
            'accountid':accountid,
            'type':1
        },
        type:'POST',
        beforeSend:function() {
            //mark('#rechargesheet_page_popup', 'show');
        },
        success: function(data){
            //mark('#rechargesheet_page_popup', 'none');
            data = JSON.parse(data).productprovider;
            idtopplatform = data;
            console.log(data);
            if(data){
                set_loading_accountplatform_info(0);
                set_loading_accountplatform_info(1);
            }
        }
    });
}

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
    console.log($("#conversiontype").val());
    // 如果媒体账号（自有媒体）直接获取媒体账号
    if($("#conversiontype").val()=='AccountPlatform'){
        getAccountPlatform(accountid);
       //如果选择了 媒体外采类
    }else if($("#conversiontype").val()=='ProductProvider'){
        //如果供应商已经选择 // 根据供应商id 和 客户id 获取 媒体外采账号
        if($("#vendorid").val()){
            var  accountid=$("#accountid").val();
            var vendorid=$("#vendorid").val();
            getProductProvider(accountid,vendorid);
        //如果供应商没有选择则不获取账号信息
        }else{
          return false;
        }
    }

}
$('#search_servicecontracts').on('keydown',function(event){
    if(event.keyCode==13){
        search_servicecontracts();
        return false;
    }
});
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
    goto_rechargesheet_account();
}
/**
 * 设置转入转出明初始化的值
 * @param did
 */
function set_loading_accountplatform_info(did) {
    var data = idtopplatform || {};
    var accountzh=did>0?"maccountzh["+did+"]":'accountzh';
    var mdid=did>0?"mdid["+did+"]":'did';
    var cashtransfer=did>0?"mcashtransfer["+did+"]":'cashtransfer';
    var maccounttransfer=did>0?"maccounttransfer["+did+"]":'accounttransfer';
    var discount=did>0?"mdiscount["+did+"]":'discount';
    var maccountrebatetype=did>0?"maccountrebatetype["+did+"]":'accountrebatetype';
    var accountrebatetype_display=did>0?"maccountrebatetype_display["+did+"]":'accountrebatetype_display';
    var productid=did>0?"mproductid["+did+"]":'productid';
    var productid_display=did>0?"mproductid_display["+did+"]":'productid_display';
    var isprovideservice_display=did>0?"misprovideservice_display["+did+"]":'isprovideservice_display';
    var misprovideservice=did>0?"misprovideservice["+did+"]":'isprovideservice';
    var optionStr='';
    for(var i in data){
        if(i==0){
            $("#main_page_form input[name='"+accountzh+"']").val(data[i]['accountplatform']); //账户
            var topplatform = data[i]['topplatform'];
            $("#main_page_form input[name='"+cashtransfer+"']").val("0");
            $("#main_page_form input[name='"+maccounttransfer+"']").val("0");
            $("#main_page_form input[name='"+discount+"']").val("");
            $("#main_page_form input[name='"+maccountrebatetype+"']").val("");
            $("#main_page_form input[name='"+accountrebatetype_display+"']").val("");
            $("#main_page_form input[name='"+productid+"']").val(data[i]['""']); //产品id
            $("#main_page_form input[name='"+productid_display+"']").val(""); //充值平台
            $("#main_page_form input[name='"+productid_display+"']").val(topplatform); //充值平台


            var isprovideservice = data[i]['isprovideservice'];
            $("#main_page_form input[name='"+misprovideservice+"']").val(isprovideservice); //有无服务
            $("#main_page_form input[name='"+isprovideservice_display+"']").val(isprovideservice=='on'?'有':'无'); //有无服务
            //客户返点类型
            var accountrebatetype=data[i]['accountrebatetype']=='CashBack'?'返现':'返货';
            $("#main_page_form input[name='"+maccountrebatetype+"']").val(data[i]['accountrebatetype']);
            $("#main_page_form input[name='"+accountrebatetype_display+"']").val(accountrebatetype);
            $("#main_page_form input[name='"+productid+"']").val(data[i]['productid']); //产品id

            $("#main_page_form input[name='"+discount+"']").val(data[i]['accountrebate']); //返点
            optionStr += '<option value="' + data[i]['idaccount'] + '" selected="selected">' + data[i]['idaccount'] + '</option>';
        }else{
            optionStr += '<option value="' + data[i]['idaccount'] + '">' + data[i]['idaccount'] + '</option>';
        }
    }
    $('#main_page_form select[name="'+mdid+'"]')[0].options.length=0;
    $('#main_page_form select[name="'+mdid+'"]').append(optionStr);
    $('#main_page_form select[name="'+mdid+'"]').selectmenu('refresh');

}
//设置返回平台信息-客户
function set_return_accountplatform_info(did) {
    var data = idtopplatform || {};
    var mdid=did>0?"mdid["+did+"]":'did';
    var idaccount=$('#main_page_form select[name="'+mdid+'"]').val();
    var accountzh=did>0?"maccountzh["+did+"]":'accountzh';
    var cashtransfer=did>0?"mcashtransfer["+did+"]":'cashtransfer';
    var accounttransfer=did>0?"maccounttransfer["+did+"]":'accounttransfer';
    var discount=did>0?"mdiscount["+did+"]":'discount';
    var maccountrebatetype=did>0?"maccountrebatetype["+did+"]":'accountrebatetype';
    var accountrebatetype_display=did>0?"maccountrebatetype_display["+did+"]":'accountrebatetype_display';
    var productid=did>0?"mproductid["+did+"]":'productid';
    var productid_display=did>0?"mproductid_display["+did+"]":'productid_display';
    var isprovideservice_display=did>0?"misprovideservice_display["+did+"]":'isprovideservice_display';
    for(var i in data){
        if(idaccount==data[i]['idaccount']){
            $("#main_page_form input[name='"+accountzh+"']").val(data[i]['accountplatform']); //账户
            var topplatform = data[i]['topplatform'];
            $("#main_page_form input[name='"+cashtransfer+"']").val("0");
            $("#main_page_form input[name='"+accounttransfer+"']").val("0");
            $("#main_page_form input[name='"+discount+"']").val("");
            $("#main_page_form input[name='"+maccountrebatetype+"']").val("");
            $("#main_page_form input[name='"+accountrebatetype_display+"']").val("");
            $("#main_page_form input[name='"+productid+"']").val(data[i]['""']); //产品id
            $("#main_page_form input[name='"+productid_display+"']").val(""); //充值平台


            $("#main_page_form input[name='"+productid_display+"']").val(topplatform); //充值平台

            var isprovideservice = data[i]['isprovideservice'];
            $("#main_page_form input[name='"+isprovideservice+"']").val(isprovideservice); //有无服务
            $("#main_page_form input[name='"+isprovideservice_display+"']").val(isprovideservice=='on'?'有':'无'); //有无服务
            //客户返点类型
            var accountrebatetype=data[i]['accountrebatetype']=='CashBack'?'返现':'返货';
            $("#main_page_form input[name='"+maccountrebatetype+"']").val(data[i]['accountrebatetype']);
            $("#main_page_form input[name='"+accountrebatetype_display+"']").val(accountrebatetype);
            $("#main_page_form input[name='"+productid+"']").val(data[i]['productid']); //产品id

            $("#main_page_form input[name='"+discount+"']").val(data[i]['accountrebate']); //返点
            break;
        }
    }
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
