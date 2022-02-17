//T云购买产品
var tyun_products=[
    //{"productid":"c5f54cfc-36b5-11e7-a335-5254003c6d38","productname":"T云X1双推(首购)","shortname":'X1'},
    //{"productid":"caa9b301-36b5-11e7-a335-5254003c6d38","productname":"T云X2双推(首购)","shortname":'X2'},
    //{"productid":"c83cce8e-4993-11e7-a335-5254003c6d38","productname":"T云V2双推版","shortname":'V2双推'},
    {"productid":"0fea4ea4-78e3-438b-9b4f-1792f60bea06","productname":"T云系列旗舰版","shortname":'T云系列旗舰版'},
    {"productid":"fb01732e-4296-11e6-ad98-00155d069461","productname":"T云系列V(首购)","shortname":'V'},
    {"productid":"fafdc07c-4296-11e6-ad98-00155d069461","productname":"T云系列V1(首购)","shortname":'V1'},
    {"productid":"fb016797-4296-11e6-ad98-00155d069461","productname":"T云系列V2(首购)","shortname":'V2'},
    {"productid":"fb016866-4296-11e6-ad98-00155d069461","productname":"T云系列V3(首购)","shortname":'V3'},
    {"productid":"eb472d25-f1b1-11e6-a335-5254003c6d38","productname":"T云系列V3Plus(首购)","shortname":'V3 Plus'},
    {"productid":"fb0174bf-4296-11e6-ad98-00155d069461","productname":"T云系列V5(首购)","shortname":'V5'},
    {"productid":"b96c4ad7-27f3-4526-ab43-609d8dbd1170","productname":"T云系列V5Plus(首购)","shortname":'V5 Plus'},
    {"productid":"ad0bee9e-516f-11e6-a2ff-52540013dadb","productname":"T云系列V6(首购)","shortname":'V6'},
    {"productid":"eb480f94-f1b1-11e6-a335-5254003c6d38","productname":"T云系列V8(首购)","shortname":'V8'},
	{"productid":"b8d18d40-2b9d-42da-9096-b2d780a2c49b","productname":"T云臻推宝（首购）","shortname":'臻推宝'},
	{"productid":"b9feca81-98cf-4210-9168-405d81c3e66b","productname":"T云词霸（首购）","shortname":'词霸'},
    //{"productid":"a36a9cac-516f-11e6-a2ff-52540013dadb","productname":"T云系列发布宝(首购)","shortname":'发布宝'},
    {"productid":"512cb5c8-7609-11e7-a335-5254003c6d38","productname":"T云系列S1(首购)","shortname":'S1'},
    {"productid":"512cb5e6-7609-11e7-a335-5254003c6d38","productname":"T云系列S1Plus(首购)","shortname":'S1Plus'},
    {"productid":"512cb609-7609-11e7-a335-5254003c6d38","productname":"T云系列S2(首购)","shortname":'S2'},
    {"productid":"da1832bc-bc86-459f-a14c-285b2f69e1d3","productname":"T云系列S3(首购)","shortname":'S3'},
    {"productid":"5d1736e9-6b26-4932-8e74-0377374fa7ce","productname":"T云随推（首购）","shortname":'随推'},
	{"productid":"1c72a0e4-530b-4c46-b186-60b1a9251876","productname":"T云宝盟（首购）","shortname":'宝盟'},
	{"productid":"7aa589be-548d-48b5-a75f-e09cb0f06156","productname":"T云系列F（首购)","shortname":'F'},
    {"productid":"cde37624-c07f-49e2-ad8c-621da73e9566","productname":"T云系列F1（首购)","shortname":'F1'},
    {"productid":"81c8a558-f03d-475c-9430-da74c6e25d38","productname":"T云系列F2（首购)","shortname":'F2'},
    {"productid":"8d6d07f7-cd42-4df4-94cd-cf6a312a6d80","productname":"T云系列F3（首购)","shortname":'F3'},
    {"productid":"b95fdcf3-e59f-47d9-8d4e-b1b3d0d93f15","productname":"T云系列F5（首购)","shortname":'F5'},
	{"productid":"5b304eb3-4b58-4ded-b909-93095f1f439d","productname":"19T云-词霸超级版双推（首购）","shortname":'19T云-词霸超级版双推'},
    {"productid":"de080f27-5d3a-4470-86ee-27943ce96bf2","productname":"T云-宝盟超级版双推（首购）","shortname":'T云-宝盟超级版双推'},
    {"productid":"fac3c0c0-c9cd-464b-a5ec-4a08789b3e0b","productname":"19T云-V2双推（首购）","shortname":'19V2双推'},
    {"productid":"285c6832-458e-4b32-897c-a118e0d476fa","productname":"19T云-v5双推（首购）","shortname":'19v5双推'}
    //{"productid":"9bb55818-37ba-49cc-9c5b-493b68a19c21","productname":"小程序电商标准版","shortname":'小程序电商标准版'},
    //{"productid":"b9345acf-452d-4746-8533-4c59b6b02df8","productname":"小程序电商旗舰版","shortname":'小程序电商旗舰版'}
]
//初始化产品
function initBuyProductList(id) {
    $("#"+id).empty();
    var str_html = "<option value='' selected>请选择产品版本</option>";
    for(var i=0;i<tyun_products.length;i++){
        str_html += "<option value='"+ tyun_products[i]['productid'] +"'>"+ tyun_products[i]['productname'] +"</option>";
    }
    $("#"+id).append(str_html);
}
//初始化购买年限
function initBuyYearList(id) {
    $("#"+id).empty();
    var str_html = "<option value='' selected>请选择年限</option>";
    for(var i=1;i<11;i++){
        str_html += "<option value='"+ i +"'>"+ i +"年</option>";
    }
    $("#"+id).append(str_html);
}
//获取产品略称
function getShortNamebyId(id) {
    for(var i=0;i<tyun_products.length;i++){
        if(id == tyun_products[i]['productid']){
            return tyun_products[i]['shortname'];
        }
    }
    return '';
}
//获取产品名称
function getProductNamebyId(id) {
    for(var i=0;i<tyun_products.length;i++){
        if(id == tyun_products[i]['productid']){
            return tyun_products[i]['productname'];
        }
    }
    return '';
}
function select_product(val){
    window.parent.scroll(0,0);
    var str = '';
    if(val==''){
        return false;
    }
    str = getShortNamebyId(val);
    Tips.confirm({
        content: '您选择' + str + '版本',
        define: '确定',
        cancel: '取消',
        before: function(){
        },
        after: function(b){
            if(b){
                $("#pipre").val(val);
            }else{
                $("#productid").val($("#pipre").val());
            }
        }
    });
}
function select_year(val){
    window.parent.scroll(0,0);
    var str = '';
    if(val==''){
        return false;
    }
    str = $("#productlife").find("option:selected").text();
    Tips.confirm({
        content: '您选择了' + str + '的服务时间',
        define: '确定',
        cancel: '取消',
        before: function(){
        },
        after: function(b){
            if(b){
                $("#plpre").val(val);
            }else{
                $("#productlife").val($("#plpre").val());
            }
        }
    });
}
//=========另购服务=====================================================================================================
function checkServiceInput(type) {
    var service_count = $("#div_tyun_serviceitem table tbody tr").length;
    var is_repeat = false;
    var is_empty = false;
    var is_tyun_seo = false;
    if(service_count > 0){
        $("#div_tyun_serviceitem table tbody tr").each(function () {
            var curNum = $(this).attr("data-num");
            var curServiceID = $("#servicename"+curNum).val();
            // T云智能SEO资源
            if(curServiceID=='1e9c758a-2d65-44f1-98af-ff741a39601a'){
                is_tyun_seo = true;
            }

            if(curServiceID != ''){
                var service_count = 0;
                $("#div_tyun_serviceitem table tbody tr").each(function () {
                    var tmpNum = $(this).attr("data-num");
                    var tmpServiceID = $("#servicename"+tmpNum).val();
                    if(curServiceID == tmpServiceID){
                        service_count++;
                    }
                    if(service_count > 1){
                        return false;
                    }
                });

                if(service_count > 1){
                    is_repeat = true;
                    return false;
                }
            }else{
                is_empty = true;
                return false;
            }
        })

        if (is_empty) {
            Tips.alert({
                content: '存在未选择的另购服务'
            });
            return false;
        }
        if (is_repeat) {
            Tips.alert({
                content: '存在重复的另购服务'
            });
            return false;
        }

        //智能购买 V3 V3P
        //领取激活码时间大于 2017年11月4日
        if(is_tyun_seo){
            var productid = "";
            if(type == 'buy' || type == 'upgrade'){
                productid = $("#productid").val();
            }
            if(type == 'renew' || type == 'againbuy'){
                productid = $("#oldproductid_save").val();
            }

            if(productid != 'fb016866-4296-11e6-ad98-00155d069461' && productid != 'eb472d25-f1b1-11e6-a335-5254003c6d38'){
                Tips.alert({
                    content: '选择了【智能SEO】时,只能选择V3或V3P'
                });
                return false;
            }

            if(type != 'buy'){
                //领取激活码时间大于 2017年11月4日
                var receivetimeflag = $("#receivetimeflag").val();
                if(receivetimeflag == '0'){
                    Tips.alert({
                        content: '选择了【智能SEO】时,领取激活码时间必须要大于2017年11月4日'
                    });
                    return false;
                }
            }
        }
    }
    return true;
}

//添加另购服务
function add_TyunServiceItem(id,type) {
    if (arr_all_serviceItem.length == 0) {
        getTyunServiceItem(id,type);
    }else{
        makeTyunServiceItem(id,type);
    }
}
//查询另购服务
function getTyunServiceItem(id,type) {
    $('#loading').show();

    //$("#btn_tyun_buy").removeAttr("disabled");
    //$("#btn_tyun_upgarde").removeAttr("disabled");
    //$("#btn_tyun_renew").removeAttr("disabled");
    //$("#btn_tyun_againbuy").removeAttr("disabled");
    $("#"+id).removeAttr("disabled");
    $.ajax({
        url: "/index.php?module=TyunBuyService&action=getTyunServiceItem",
        type: 'POST',
        dataType: 'json',
        success: function (data) {
            $('#loading').hide();
            if(!data.success){
                Tips.alert({
                    content: data.message,
                });
                $("#"+id).attr("disabled","disabled");
            }else{
                arr_all_serviceItem = data.data;
                makeTyunServiceItem(id,type);
            }
        }
    });
}
//添加另购服务
var arr_all_serviceItem = [];
var arr_set_serviceItem = [];
var service_num = 0;
function makeTyunServiceItem(id,type) {
    var list = arr_all_serviceItem;
    if (list.length == 0) {
        Tips.alert({
            content: '没有查询到另购服务'
        });
        return;
    }

    var html_tr = '<tr id="tr_service_'+ service_num +'" data-num="'+service_num+'"><td style="width: 60%;border: 0px">';
    html_tr += '<select id="servicename'+service_num+'" class="form-control" onchange="changeService(this,'+service_num+');" data-toggle="popover" data-placement="bottom" data-content="服务名称">';
    html_tr += '<option value="">选择服务</option>';
    for(var i=0;i<list.length;i++){
        html_tr += '<option value="'+ list[i].ServiceID + '">' + list[i].ServiceName +'</option>';
    }
    html_tr += '</select></td>';
    html_tr += '<td style="border: 0px"><select id="buycount'+service_num+'" class="form-control" " data-toggle="popover" data-placement="bottom" data-content="购买数量"><option value="">选择服务</option></select></td>';
    //html_tr += '<td style="border: 0px"><select id="buyyear'+service_num+'" class="form-control"  data-toggle="popover" data-placement="bottom" data-content="购买年限"><option value="">选择服务</option></select></td>';
    html_tr += '<td style="width: 10px;border: 0px"><i class="fa icon-minus fa-6" aria-hidden="true" title="移除" onclick="removeServiceitem('+service_num+')"></i></td></tr>';
    $("#div_tyun_serviceitem table tbody").append(html_tr);

    //重新设置iframe高度
    parent.setTyunIframeHeight(type,0);

    service_num ++;
    checkServiceitem();
}
//移除服务
function removeServiceitem(num) {
    $("#tr_service_"+ num).remove();
    checkServiceitem();
}
//判断是否可添加
function checkServiceitem() {
    var service_count = $("#div_tyun_serviceitem table tbody tr").length;
    if(arr_all_serviceItem && arr_all_serviceItem.length>0 && service_count < arr_all_serviceItem.length){
        $("#div_serviceItem").show();
    }else{
        $("#div_serviceItem").hide();
    }
}
function changeService(obj,num) {
    var curServiceID = $(obj).val();
    var list = arr_all_serviceItem;
    if (list.length == 0) {
        Tips.alert({
            content: '没有查询到另购服务'
        });
        return;
    }

    $("#buycount"+ num).empty();
    //$("#buyyear"+ num).empty();
    var html_count = "";
    var html_year = "";
    for(var i=0;i<list.length;i++){
        if(list[i].ServiceID == curServiceID){
            //var s_year = list[i].Year;
            var s_unit = list[i].Unit;
            var s_multiple = list[i].Multiple;
            //购买数量
            var start_num = parseInt(s_multiple);
            var step_num = parseInt(s_multiple);
            for(var j=1;j<11;j++){
                html_count += '<option value="'+j+'" tyun-value="'+start_num+'">'+j + s_unit +'</option>';
                start_num += step_num;
            }

            //购买年限
            /*if(s_year == "0"){
                html_year += '<option value="">无</option>';
            }else{
                for(var j=1;j<11;j++){
                    html_year += '<option value="'+j+'">'+j+'年</option>';
                }
                //html_year += '<option value="'+s_year+'">'+s_year+'年</option>';
            }*/
            break;
        }
    }

    $("#buycount"+ num).append(html_count);
    //$("#buyyear"+ num).append(html_year);
    arr_set_serviceItem.push(curServiceID);
}
//=======================================================================================================================
//通过T云账号查询购买信息(查询最近购买)
function searchTyunBuyServiceInfo(tyun_type) {
    var tyun_account = $.trim($("#tyun_account_s").val());
    if (tyun_account == "") {
        Tips.alert({
            content: 'T云账号不能为空',
        });
        $("#tyun_account").focus();
        return;
    }

    var classtype = '';
    var classtype_name = "";
    if(tyun_type == 1){
        classtype='upgrade';
        classtype_name="升级";
    }else if(tyun_type == 2){
        classtype='renew';
        classtype_name="续费";
    }else if(tyun_type == 3){
        classtype='againbuy';
        classtype_name="另购";
    }else if(tyun_type == 4) {
        classtype = 'degrade';
        classtype_name = "降级";
    }

    //清空数据
    $("#oldcustomername_display").text("");
    $("#customername_display").text("");
    $("#oldcontractcode_display").text("");
    $("#oldproductname_display").text("");
    $("#oldexpiredate_display").text("");
    $("#receivetimeflag").val("");
    $("#buyid_save").val("");
    $("#oldcontractid").val("");
    $("#oldcustomerid").val("");
    $("#loginname_save").val("");
    $("#secretkeyid_save").val("");
    $("#oldproductid_save").val("");
    $("#oldexpiredate_save").val("");
    $("#agents_save").val("");
    $("#customername_save").val("");
    $("#tyun_account_display").text("");
    $("#tyun_account").val("");
    $("#lately_renew_display").text("");

    $('#loading').show();
    $.ajax({
        url: "/index.php?module=TyunBuyService&action=searchTyunBuyServiceInfo&tyun_account=" + tyun_account +"&tyun_type="+classtype,
        type: 'POST',
        dataType: 'json',
        success: function (data) {
            $('#loading').hide();
            if (data.success) {
                if (data.buyList.length == 0) {
                    Tips.alert({
                        content: '未查询到购买信息',
                    });
                } else {
                    var buydata = data.buyList[0];
                    $("#oldcustomername_display").text(buydata.customername==""?buydata.companyname:buydata.customername);
                    $("#customername_display").val(buydata.customername==""?buydata.companyname:buydata.customername);
                    $("#oldcontractcode_display").text(buydata.contractname == ""?"--":buydata.contractname);
                    $("#oldproductname_display").text(buydata.productname);
                    $("#oldexpiredate_display").text(buydata.expiredate == '' ? '--' : buydata.expiredate);
                    $("#receivetimeflag").val(buydata.receivetimeflag);

                    $("#buyid_save").val(buydata.activationcodeid);
                    $("#oldcontractid").val(buydata.contractid);
                    $("#oldcustomerid").val(buydata.customerid);
                    $("#customerid").val(buydata.customerid);
                    $("#loginname_save").val(tyun_account);
                    $("#secretkeyid_save").val(buydata.activecode);
                    $("#oldproductid_save").val(buydata.productid);
                    $("#oldexpiredate_save").val(buydata.expiredate);
                    $("#agents_save").val(buydata.agents);
                    $("#customername_save").val(buydata.customername);

                    $("#tyun_account_display").text(tyun_account);
                    $("#tyun_account").val(tyun_account);
                    $("#lately_add_display").text(buydata.latelyadd==""?'未'+classtype_name+'过':buydata.latelyadd);
                    //获取升级产品
                    if(tyun_type==1){
                        searchTyunUpgradeProduct(buydata.productid);
                    }
                    //获取降级产品
                    if(tyun_type==4){
                        searchTyunUpgradeProduct(buydata.productid,tyun_type);
                    }

                    //重新设置iframe高度
                    parent.setTyunIframeHeight(tyun_type,0);
                }

            } else {
                Tips.alert({
                    content: data.message,
                });
            }
        }
    });
}
//获取升级版本
function searchTyunUpgradeProduct(productid,tyun_type) {
    //初始化升级版本
    $("#productid").empty();
    var str_html = "<option value='' selected>请选择版本</option>";
    $("#productid").append(str_html);

    var type_name = "升级";
    var url = "/index.php?module=TyunBuyService&action=searchTyunUpgradeProduct&p_productid="+productid;

    if(tyun_type == '4'){
        url+='&is_degrade=1'
        type_name = "降级";
        $("#btn_tyun_degrade").removeAttr("disabled");
    }else{
        $("#btn_tyun_upgarde").removeAttr("disabled");
    }
    $('#loading').show();
    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'json',
        success: function (data) {
            $('#loading').hide();
            if(data.success){
                if(data.productList.length == 0){
                    Tips.alert({
                        content: '未查询到对应'+type_name+'版本',
                    });
                    if(tyun_type == '4'){
                        $("#btn_tyun_degarde").attr("disabled","disabled");
                    }else{
                        $("#btn_tyun_upgarde").attr("disabled","disabled");
                    }
                }else{
                    initUpgradeProductList(data.productList,tyun_type);
                }
            }else{
                Tips.alert({
                    content: data.message,
                });
                if(tyun_type == '4'){
                    $("#btn_tyun_degarde").attr("disabled","disabled");
                }else{
                    $("#btn_tyun_upgarde").attr("disabled","disabled");
                }
            }
        }
    });
}
//初始化产品
function initUpgradeProductList(productList) {
    $("#productid").empty();
    var str_html = "<option value='' selected>请选择版本</option>";
    for(var i=0;i<productList.length;i++){
        str_html += "<option value='"+ productList[i] +"'>"+ getProductNamebyId(productList[i]) +"</option>";
        //str_html += "<option value='"+ productList[i]['productid'] +"'>"+ productList[i]['productname'] +"</option>";
    }
    $("#productid").append(str_html);
}
function customDateToString(now) {
    var yy = now.getFullYear();      //年
    var mm = now.getMonth() + 1;     //月
    var dd = now.getDate();          //日

    var dt = new Date();
    var hh = dt.getHours();         //时
    var ii = dt.getMinutes();       //分
    var ss = dt.getSeconds();       //秒
    var clock = yy + "-";
    if(mm < 10) clock += "0";
    clock += mm + "-";
    if(dd < 10) clock += "0";
    clock += dd + " ";
    if(hh < 10) clock += "0";
    clock += hh + ":";
    if (ii < 10) clock += '0';
    clock += ii + ":";
    if (ss < 10) clock += '0';
    clock += ss;
    return clock;
}


//搜索购买客户
function searchBuyCustome(ispermission=0) {
    var o = $('#customername_display');
    var ov = o.val();
    var op = o.parent();
    var sb = [];
    if('' == ov){
        Tips.alert({
            content: '客户名称不能为空',
        });
        return;
    }
    o.next('ul').remove();
    $('.delefalg').remove();
    var dheight=$(document).height();
    dheight=dheight*0.5;
    oul = op.append('<ul id="keyText2" class="keyText delefalg" style="max-height:'+dheight+'px;overflow:auto;left: 82px"></ul>');

    if (ov) {
        $('#loading').show();
        op.addClass('keyBox');
        $.ajax({
            url: '/index.php?module=Accounts&action=searchAccount&company='+ov+'&ispermission='+ispermission,
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                $('#loading').hide();
                if (data && data.length > 0) {
                    for (var i = 0;i<data.length; i++) {
                        var item2=data[i];
                        var nArr = item2.value;console.log(nArr)
                        var oli=op.children('ul');
                        oli.append("<li onclick='selectBuyCustome("+item2.id+")'>" + nArr + '</li>');

                    }
                    $("#keyText2").show();
                }else{
                    Tips.alert({
                        content: '找不到客户',
                        define:'确定',
                        after:function(){
                            $("#customername_display").val('');
                            $("#customerid").val('');
                        }
                    });
                }
            },error:function(){
                $('#loading').hide();
                Tips.alert({
                    content: 'error'
                });
            }
        });
    }
}

function selectBuyCustome(id){
    var idval = id;
    $.ajax({
        url: "/index.php?module=Accounts&action=getAccountMsg&id="+id,
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            if(data==''){
                Tips.alert({
                    content: '客户信息不全'
                });
                return false;
            }
            //$('#destination').val(data.address);
            $('#customerid').val(data.accountid);
            $('#customerowenid').val(data.userid);
            $('#customerowenidmsg').text('客户负责人:'+data.username);
            //$('#contacts').val(data.linkname);
            $('#customername_display').val(data.accountname);//$('#related_to_display').val(data.accountname);
            //$('#customeraddress').val(data.customeraddress);
            $("#customername_save").val(data.accountname);
            $("#customerid_save").val(data.accountid);
            window.parent.scroll(0,0);
        },error:function(){
            Tips.alert({
                content: 'error'
            });
        }
    });
}
