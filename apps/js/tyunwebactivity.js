$(function () {
    var productitems = [];
    var productlist = [];
    var giftproductlist = [];
    var selectproducttitle = '';
    /** 页面跳转start **/

    $("#container").on('click',"#moregiveproduct",function (event) {
        if(giftproductlist.length<=0){
            alert("没有赠送产品");
            return;
        }
        pageManager.go('moregiveproduct');
    });

    $("#container").on('click','#morediscountproduct',function (event) {
        if(productlist.length<=0){
            alert("没有折扣产品");
            return;
        }
        pageManager.go('morediscountproduct');
    });

    $("#container").on('click','#selectActivity',function (event) {
        window.activityproducttitle=$('input[name="accountid_display"]').val();
        productlist = [];
        giftproductlist = [];
        pageManager.go('selectactivity');
    });
    /** 页面跳转end **/


    /** 初始页面生成start **/
    window.activityLoad=function(params){
        console.log(params);
        //1新购活动 4续费活动 2升级活动
        // $("#instanceagents").attr('data-value',10642);
        var activityAgent = $("#instanceagents").data('value');

        selectproducttitle=window.selectproducttitle;
        $('#selectproducttitle').html((typeof selectproducttitle=='string')?((selectproducttitle!='')?selectproducttitle:'&nbsp;'):'&nbsp;');
        $('#agents').val(activityAgent);
        if(params==1){
            // activityAgent = 10642;
            // 活动类型 1优惠组合 2赠送产品 3赠送时间 4限时折扣
            $.ajax(
                {
                    url: '/index.php?module=TyunWebBuyService&action=getALLNowActivity',
                    type: 'POST',
                    dataType: 'json',
                    data:{
                        "activityAgent":activityAgent,
                        "activityModel":params,
                        "activityRange":2
                    },
                    beforeSend:function(){
                        $.showLoading('活动列表加载中');
                    },
                    success: function (data) {
                        console.log(data);
                        $.hideLoading();
                        if(data && data.success && data.data.length>0){
                            console.log(data.data.length);
                            var datas=data.data;
                            console.log(datas);
                            updateNewActivityList(datas,activityAgent);
                        }
                    }
                }
            );
            return;
        }

        var categoryID= $("#classtyperenew").val();
        var packageID = $("#oldproductnameid").val();
        if(params==2){
            packageID = $("#goalproduct").val();
        }
        // activityAgent = 10642;

        // 活动类型 1优惠组合 2赠送产品 3赠送时间 4限时折扣
        $.ajax(
            {
                url: '/index.php?module=TyunWebBuyService&action=getActivity',
                type: 'POST',
                dataType: 'json',
                data:{
                    "categoryID":categoryID,
                    "packageID":packageID,
                    "activityModel":params,
                    "activityAgent":activityAgent,
                },
                beforeSend:function(){
                    $.showLoading('活动列表加载中');
                },
                success: function (data) {
                    console.log(data);
                    $.hideLoading();
                    if(data && data.success && data.data.length>0){
                        var datas=data.data;
                        updateActivityList(datas,categoryID,activityAgent);
                    }
                }
            }
        );

        getRenewInterest(packageID);
    };


    function getRenewInterest(buyproductid) {
        var values = $('#classtyperenew').val();
        var tyunusercode = $('#tyunusercode').val();
        var tyunusername = $('#tyunusercode option:checked').text();
        if (tyunusercode == '') {
            return false;
        }
        $.ajax({
            url: '/index.php?module=TyunWebBuyService&action=getUserRenewProductInfo',
            type: 'POST',
            dataType: 'json',
            data: {
                "tyunusercode": tyunusercode,
                "classtyperenew": values,
                "tyunusername": tyunusername,
                "classtype": "renew",
                "buyproductid": buyproductid
            },
            success: function (data) {
                if (data.code == 200) {
                    var data2 = data.data;
                    window.domains = data2.domains;
                    window.domaininpackage = data2.domaininpackage;
                }
            }
        });
    }

    window.morediscountproductLoad = function(){
        console.log(productlist);
        var goalproduct = $("#goalproduct").val();
        var activitymodel = $("#activity_detail").data('activitymodel');
        var oldproductnameid = $("#oldproductnameid").val();

        str = '';
        $.each(productlist,function (k, v) {
            console.log(v);
            var stylestr = '';

            //升级跳过非要升级版本的产品
            if(activitymodel==2 && goalproduct && goalproduct!=v.PackageID){
                stylestr = "style='margin-top:5px;border-top:1px solid gainsboro;display:none;'"
                // return true;
            }
            //续费跳过非要续费版本的产品
            if(activitymodel==4 && oldproductnameid && oldproductnameid!=v.PackageID){
                stylestr = "style='margin-top:5px;border-top:1px solid gainsboro;display:none;'"
                // return true;
            }

            var minLimit = v.ActivityThresholdCount>1 ? v.ActivityThresholdCount: 1;
            var minBuyTerm = v.ActivityThresholdBuyTerm >1 ? v.ActivityThresholdBuyTerm:1;

            str +='<div class="weui-media-box__bd weui-cells_checkbox" '+stylestr+'>\n' +
                '        \n' +
                '        <div class="weui-media-box__title">\n' +
                '            <div class="weui-cell" style="padding:5px">\n' +
                '              <label class="weui-cell weui-check__label discountproduct discountproduct_label discountproduct'+k+'" data-canrenew="'+v.CanRenew+'" data-key="'+k+'" data-activitymarketprice="'+v.ActivityMarketPrice+'" data-activityrenewprice="'+v.ActivityRenewPrice+'" data-renewprice="'+v.OriginalPrice.renewPrice+'" data-price="'+v.OriginalPrice.price+'" data-activityrenewmarketprice="'+v.ActivityRenewMarketPrice+'" data-activityprice="'+v.ActivityPrice+'" data-marketrenewprice="'+v.OriginalPrice.marketRenewPrice+'" data-marketprice="'+v.OriginalPrice.marketPrice+'" data-specificationstitle="'+v.SpecificationTitle+'" data-producttitle="'+(v.PackageTitle?v.PackageTitle:v.ProductTitle)+'" data-activitythresholdcount="'+minLimit+'" data-activitythresholdbuyterm="'+minBuyTerm+'" data-candiscount="'+v.CanDiscount+'" data-packageid="'+v.PackageID+'" data-categoryid="'+v.CategoryID+'" data-userlimit="'+v.UseLimit+'" data-specialficationid="'+v.SpecificationID+'"  data-specialficationcount="'+v.SpecificationCount+'" data-productid="'+v.ProductID+'" data-buynum="'+minLimit+'" data-buyyear="'+minBuyTerm+'" data-buyterm="'+v.BuyTerm+'" data-count="'+v.Count+'" style="padding: 0px;">\n' +
                '            <div class="weui-cell__hd ">\n' +
                '                <input type="checkbox" class="weui-check discountproduct discountproduct'+k+' discountproductinput'+k+'" data-key="'+k+'">\n' +
                '                <i class="weui-icon-checked"></i>\n' +
                '            </div>\n' +
                '        </label>\n' +
                '    \n' +
                '                <div class="weui-cell__bd">\n' +
                (v.PackageTitle?v.PackageTitle:v.ProductTitle)+
                '                </div>\n' +
                '                \n' +
                '            </div>\n' +
                '        </div>\n' +

                '<div class="weui-media-box__title">\n' +
                '            <div class="weui-cell" style="padding:5px 10px;">\n' +
                '                <div class="weui-cell__bd">\n' +
                '                    <div class="button_sp_area" style="white-space: normal;">\n' +
                '<a href="javascript:;" class="weui-btn weui-btn_mini weui-btn_default" style="margin-top:0px;" data-unit="'+v.SpecificationUnit+'" data-specificationstitle="'+v.SpecificationTitle+'">'+(v.SpecificationTitle?v.SpecificationTitle:'无')+'</a>\n' +
                '</div>\n' +
                '                                    </div>\n' +
                '                                </div>\n' +
                '                            </div>'+

                '                            <div class="weui-media-box__desc">\n' +
                '                                <div class="weui-cell">\n' +
                '                                    <div class="weui-cell__bd">\n' +
                '                                        <div class="weui-count weui-count_custrom" style="width:100%">\n' +
                '                                            <a class="" style="color:#000000;font-size:14px;">数量：</a>                                            ' +
                '                                            <a class="weui-count__btn discountproductnum_sub weui-count__left_custrom" data-key="'+k+'"></a>\n' +
                '                                            <input class="weui-count__number discountproductnum'+k+'" style="width:1.5rem;-webkit-appearance:none !important;border-radius: 0 !important;" type="number" value="'+minLimit+'" data-values="'+minLimit+'" readonly>\n' +
                '                                            <a class="weui-count__btn discountproductnum_add weui-count__right_custrom" data-key="'+k+'"></a>\n' +
                '                                        <div style="margin-left:10px" class="unit54">'+v.SpecificationUnit+'</div></div> \n' +
                '                                    </div>\n' +
                '                                    \n' +
                '                                </div>\n' +
                '                            </div>\n' +
                '                            <div class="weui-media-box__desc">\n' +
                '                                <div class="weui-cell">\n' +
                '                                    <div class="weui-cell__bd">\n' +
                '                                        <div class="weui-count weui-count_custrom" style="width:100%">\n' +
                '                                            <a class="" style="color:#000000;font-size:14px;">年限：</a>                                            ' +
                '                                            <a class="weui-count__btn discountproductyear_sub weui-count__left_custrom" data-key="'+k+'"></a>\n' +
                '                                            <input class="weui-count__number discountproductyear'+k+'" style="width:1.5rem;-webkit-appearance:none !important;border-radius: 0 !important;" type="number" value="'+minBuyTerm+'" data-values="'+minBuyTerm+'"  readonly>\n' +
                '                                            <a class="weui-count__btn discountproductyear_add weui-count__right_custrom" data-key="'+k+'"></a>\n' +
                '                                        <div style="margin-left:10px" class="unit54">年</div></div> \n' +
                '                                    </div>\n' +
                '                                    \n' +
                '                                </div>\n' +
                '                            </div>\n' +

                '                            <div class="weui-media-box__desc">\n' +
                '                                <div class="weui-cell">\n' +
                '                                    <div class="weui-cell__bd">\n' +
                '                                        <div class="weui-count weui-count_custrom" style="width:100%">\n' +
                '                                            <a class="" style="color:#000000;font-size:14px;">市场活动价：</a>                                            ' +
                '                                        <div style="margin-left:10px" class="unit54 discountproductactivity'+k+'"></div></div> \n' +
                '                                    </div>\n' +
                '                                    \n' +
                '                                </div>\n' +
                '                            </div>\n' +
                '                        </div>';
        });

        $("#page_morediscountproduct").append(str);

        $.each($(".discountlabel"),function (key,value) {
            var k = $(value).data('key');
            $(".discountproductinput"+k).trigger('click');

            var buyproductnum = $(".discountproductnum"+k).data('values');
            var buyyear = $(".discountproductbuyyear"+k).data('values');
            $(".discountproductnum"+k).val(buyproductnum);
            $(".discountproductnum"+k).attr('data-values',buyproductnum);
            $(".discountproduct"+k).attr('data-buynum',buyproductnum);
            $(".discountproductyear"+k).val(buyyear);
            $(".discountproductyear"+k).attr('data-values',buyyear);
            $(".discountproduct"+k).attr('data-buyyear',buyyear);
        });
        cal5();
    };

    window.moregiveproductLoad = function(){
        str = '';
        console.log(giftproductlist);
        $(giftproductlist).each(function (k, v) {
            console.log(v);
            var stylestr = '';
            if(k>0){
                stylestr = "style='margin-top:5px;border-top:1px solid gainsboro;'"
            }
            str +='<div class="weui-media-box__bd weui-cells_checkbox" '+stylestr+'>\n' +
                '        \n' +
                '        <div class="weui-media-box__title">\n' +
                '            <div class="weui-cell" style="padding:5px">\n' +
                '              <label class="weui-cell weui-check__label giveproduct giveproduct'+k+'" data-canrenew="'+v.CanRenew+'" data-producttitle="'+(v.PackageTitle?v.PackageTitle:v.ProductTitle)+'('+(v.SpecificationTitle?v.SpecificationTitle:'无')+')'+'" data-giveproductnum="'+v.Count+'" data-giveproductyear="'+v.BuyTerm+'" data-candiscount="'+v.CanDiscount+'" data-packageid="'+v.PackageID+'" data-categoryid="'+v.CategoryID+'" data-userlimit="'+v.UseLimit+'" data-specialficationid="'+v.SpecificationID+'"  data-specialficationcount="'+v.SpecificationCount+'" data-productid="'+v.ProductID+'" data-buyterm="'+v.BuyTerm+'" data-count="'+v.Count+'" style="padding: 0px;">\n' +
                '            <div class="weui-cell__hd ">\n' +
                '                <input type="checkbox" class="weui-check giveproduct giveproductinput'+k+'" data-key="'+k+'">\n' +
                '                <i class="weui-icon-checked"></i>\n' +
                '            </div>\n' +
                '        </label>\n' +
                '    \n' +
                '                <div class="weui-cell__bd">\n' +
                (v.PackageTitle?v.PackageTitle:v.ProductTitle)+'('+(v.SpecificationTitle?v.SpecificationTitle:'无')+')'+
                '                </div>\n' +
                '                \n' +
                '            </div>\n' +
                '        </div>\n' +
                '                            <div class="weui-media-box__desc">\n' +
                '                                <div class="weui-cell">\n' +
                '                                    <div class="weui-cell__bd">\n' +
                '                                        <div class="weui-count weui-count_custrom" style="width:100%">\n' +
                '                                            <a class="" style="color:#000000;font-size:14px;">数量：</a>                                            ' +
                '                                            <a class="weui-count__btn giveproductnum_sub weui-count__left_custrom" data-key="'+k+'"></a>\n' +
                '                                            <input class="weui-count__number giveproductnum'+k+'" style="width:1.5rem;-webkit-appearance:none !important;border-radius: 0 !important;" type="number" value="'+v.Count+'"  readonly>\n' +
                '                                            <a class="weui-count__btn giveproductnum_add weui-count__right_custrom" data-key="'+k+'"></a>\n' +
                '                                        <div style="margin-left:10px" class="unit54">'+v.SpecificationUnit+'</div></div> \n' +
                '                                    </div>\n' +
                '                                    \n' +
                '                                </div>\n' +
                '                            </div>\n' +
                '                            <div class="weui-media-box__desc">\n' +
                '                                <div class="weui-cell">\n' +
                '                                    <div class="weui-cell__bd">\n' +
                '                                        <div class="weui-count weui-count_custrom" style="width:100%">\n' +
                '                                            <a class="" style="color:#000000;font-size:14px;">年限：</a>                                            ' +
                '                                            <a class="weui-count__btn giveproductyear_sub weui-count__left_custrom" data-key="'+k+'"></a>\n' +
                '                                            <input class="weui-count__number giveproductyear'+k+'" style="width:1.5rem;-webkit-appearance:none !important;border-radius: 0 !important;" type="number" value="'+v.BuyTerm+'"  readonly>\n' +
                '                                            <a class="weui-count__btn giveproductyear_add weui-count__right_custrom" data-key="'+k+'"></a>\n' +
                '                                        <div style="margin-left:10px" class="unit54">年</div></div> \n' +
                '                                    </div>\n' +
                '                                    \n' +
                '                                </div>\n' +
                '                            </div>\n' +
                '                        </div>';
        });

        $("#page_moregiveproduct").append(str);

        $.each($(".giveproductlistall"),function(key,val) {
            var k = $(val).data('key');
            console.log(k);
            $(".giveproductinput"+k).trigger('click');
            var giveproductyear = $(".giveproductyear"+k).data('values');
            var giveproductnum = $(".giveproductnum"+k).data('values');
            $(".giveproductyear"+k).val(giveproductyear);
            $(".giveproductyear"+k).attr('data-values',giveproductyear);
            $(".giveproductnum"+k).val(giveproductnum);
            $(".giveproductnum"+k).attr('data-values',giveproductnum);
        });

    };

    window.activityproductLoad = function () {
        renewDomainPage();
        selectproducttitle = window.selectproducttitle;
        console.log(selectproducttitle);
        $('#selectproducttitle2').html((typeof selectproducttitle=='string')?((selectproducttitle!='')?selectproducttitle:'&nbsp;'):'&nbsp;');
        var activityId = $(".activity_check").filter('input:checked').data('activityid');
        var categoryId = $(".activity_check").filter('input:checked').data('categoryid');
        var activityAgent = $(".activity_check").filter('input:checked').data('activityagent');

        //todo
        //电子合同start
        var signaturetype = $("#signaturetype").val();
        $("#formbutton").empty();
        if(signaturetype=='eleccontract'){
            str = '<a class="weui-btn" id="electronnextstep2" href="javascript:void(0);" style="background-color: #999999;color:#FFFFFF;font-size:18px;width:98%;">下一步</a>';
        }else{
            str = '<a class="weui-btn" id="submitfrom2" href="javascript:void(0);" style="background-color: #999999;color:#FFFFFF;font-size:18px;width:98%;">提交订单</a>';
        }
        $("#formbutton").append(str);
        //电子合同end

        //根据选择的活动 获取产品列表
        $.ajax(
            {
                url: '/index.php?module=TyunWebBuyService&action=GetProductActivityDetail',
                type: 'POST',
                dataType: 'json',
                data:{
                    "activityID":activityId,
                    "categoryID":categoryId,
                    "activityAgent":activityAgent,
                },
                beforeSend:function(){
                    $.showLoading('请稍等');
                },
                success: function (data) {
                    console.log(data);
                    $.hideLoading();
                    if(data.success){
                        updateProductActivity(data.data);
                        var activitymodel = data.data.ActivityModel;
                        //2赠送产品 3赠送时间 支持选择产品
                        var activitytype = $("#activity_detail").data('activitytype');
                        if(activitytype==3 || activitytype==2){
                            console.log(productlist);
                            productitems=[];
                            var productname = '';
                            $(productlist).each(function (k, v) {
                                //升级 赠送时间  筛选产品
                                if(activitymodel==2&&activitytype==3){
                                    var goalproduct = $("#goalproduct").val();
                                    if(goalproduct&&v.PackageID!=goalproduct){
                                        return true;
                                    }
                                }
                                //续费
                                if(activitymodel==4&&activitytype==3){
                                    var oldproductnameid = $("#oldproductnameid").val();
                                    if(oldproductnameid&&v.PackageID!=oldproductnameid){
                                        return true;
                                    }
                                }
                                console.log(v);
                                //产品名称
                                if(v.ProductID){
                                    productname = v.ProductTitle;
                                }else{
                                    productname = v.PackageTitle;
                                }

                                var temp={title: productname,value: k};
                                productitems.push(temp);
                            });


                            $("#buyproduct").select({
                                title: "请选择",
                                items: productitems,
                                onChange: function (d) {
                                    if($("#buyyear").val()==undefined){
                                        $(".otherproductlist").empty();
                                    }
                                    return false;
                                },
                                onClose: function (d) {
                                    $("#producttype_activity").val("请选择");
                                    $("#buyyear").val("请选择");
                                    $("#buyproductnum_activity").val("请选择");
                                    $("#buyproductgiveproduct_activity").empty();
                                    $(".otherproductlist").empty();
                                    $(".product").attr('data-buyyear',0);
                                    $(".product").attr('data-buynum',0);
                                    $("#buyyear").attr('data-values',0);
                                    $("#buyproductnum_activity").attr('data-values',0);

                                    value = d.data.values;
                                    var productdetail =productlist[value];
                                    $("#producttype_activity").val(productdetail.SpecificationTitle?productdetail.SpecificationTitle:'无');
                                    var type = $("#buyproduct").data('type');
                                    console.log(productdetail);

                                    $(".product").attr("data-productname",$("#buyproduct").val());
                                    $(".product").attr("data-producttype",(productdetail.SpecificationTitle?productdetail.SpecificationTitle:'无'));

                                    //选择套餐给当前套餐赋值
                                    $(".product").attr('data-categoryid',productdetail.CategoryID);
                                    $(".product").attr('data-packageid',productdetail.PackageID);
                                    $(".product").attr('data-productid',productdetail.ProductID);
                                    $(".product").attr('data-specificationid',productdetail.SpecificationID);
                                    $(".product").attr('data-uselimit',productdetail.UseLimit);
                                    if(type==2){
                                        cal2();
                                        $("#productclassone").val(productdetail.CategoryTitle);
                                        $("#productclassone").attr('data-values',productdetail.CategoryID);
                                        $("#productclasstwo").val(productdetail.PackageTitle);
                                        $("#productclasstwo").attr('data-values',productdetail.PackageID);

                                        var buyyear = [];
                                        for(i=productdetail.ActivityThresholdBuyTerm;i<=10;i++){
                                            buyyeartemp = {title:i+'年',value:i};
                                            buyyear.push(buyyeartemp);
                                        }

                                        $("#buyyear").select({
                                            title:"请选择",
                                            items:buyyear,
                                            onChange:function (d) {
                                                return false;
                                            },
                                            onClose:function (d) {
                                                $(".otherproductlist").empty();
                                                $("#buyyear").attr('data-values',d.data.values);
                                                $(".product").attr('data-buyyear',d.data.values);
                                                cal2();
                                            }
                                        });


                                        var productnum = [];
                                        var minLimit = productdetail.ActivityThresholdCount>1 ? productdetail.ActivityThresholdCount:1;
                                        userlimit = productdetail.UseLimit?productdetail.UseLimit:999;
                                        for(j=minLimit;j<=userlimit;j++){
                                            productnumtemp ={title:j+productdetail.SpecificationUnit,value:j}
                                            productnum.push(productnumtemp);
                                        }

                                        $("#buyproductnum_activity").select({
                                            title:"请选择",
                                            items:productnum,
                                            onChange:function (d) {
                                                return false;
                                            },
                                            onClose:function (d) {
                                                $(".product").attr('data-buynum',d.data.values);
                                                cal2();

                                            }
                                        });
                                        return
                                    }else if(type==3){
                                        cal3();
                                        $("#productclassone").val(productdetail.CategoryTitle);
                                        $("#productclassone").attr('data-values',productdetail.CategoryID);
                                        $("#productclasstwo").val(productdetail.PackageTitle);
                                        $("#productclasstwo").attr('data-values',productdetail.PackageID);
                                        var productnum = [];
                                        userlimit = productdetail.UseLimit?productdetail.UseLimit:999;
                                        var minLimit = productdetail.ActivityThresholdCount>1 ?productdetail.ActivityThresholdCount:1;
                                        for(j=minLimit;j<=userlimit;j++){
                                            productnumtemp ={title:j+productdetail.SpecificationUnit,value:j};
                                            productnum.push(productnumtemp);
                                        }

                                        $("#buyproductnum_activity").select({
                                            title:"请选择",
                                            items:productnum,
                                            onChange:function (d) {
                                                return false;
                                            },
                                            onClose:function (d) {
                                                $(".product").attr('data-buynum',d.data.values);
                                                cal3();

                                            }
                                        });

                                        var giverules = productdetail.GiveRule;
                                        var arr = [];
                                        $(giverules).each(function (k, v) {
                                            arr.push(v.BuyTerm);
                                        });
                                        console.log(arr);
                                        var startyear = Math.min(...arr);
                                        console.log(startyear);
                                        var buyyear = [];
                                        for(i=startyear;i<=10;i++){
                                            buyyeartemp = {title:i+'年',value:i};
                                            buyyear.push(buyyeartemp);
                                        }
                                        var reversegiverule = giverules.reverse();


                                        $("#buyyear").select({
                                            title:"请选择",
                                            items:buyyear,
                                            autoClose:true,
                                            onChange:function (f) {
                                                return false;
                                            },
                                            onClose:function (f) {
                                                $(".otherproductlist").empty();
                                                $("#buyproductgiveproduct_activity").empty();
                                                value = f.data.values;
                                                console.log(value);
                                                $("#buyyear").attr('data-values',value);

                                                var productlistkey = $("#buyproduct").data('values');
                                                console.log(productlistkey);
                                                var productdetail =productlist[productlistkey];
                                                console.log(productdetail);
                                                var GiveRule = productdetail.GiveRule;
                                                console.log(GiveRule);
                                                var str = '';
                                                var reversegiverule = GiveRule.slice();
                                                console.log(reversegiverule);
                                                $(reversegiverule).each(function (k, v) {
                                                    console.log(v.BuyTerm);
                                                    if(parseInt(v.BuyTerm)<=parseInt(value)){
                                                        console.log(value);
                                                        str +="<option value='"+v.Key+"'>"+v.GiveTerm+"年</option>";
                                                    }
                                                });
                                                $("#buyproductgiveproduct_activity").append(str);
                                                $(".product").attr('data-buyyear',value);
                                                console.log(value);
                                                cal3();
                                            }
                                        });
                                    }
                                }
                            });
                        }

                    }
                }
            }
        );
    };


    /** 初始页面生成end **/

    /** 页面生成的各种方法start **/
    function updateNewActivityList(datas,activityAgent) {
        var str = '';
        $.each(datas[0].ActivityList,function (k,v) {
            var type = v.ActivityType;
            if(type==1){
                type_text='优惠组合';
            }else if(type==2){
                type_text='赠送产品';
            }if(type==3){
                type_text='赠送时间';
            }else if(type==4){
                type_text='限时折扣';
            }

            var activity_title = v.ActivityTitle;
            var activity_type = type_text;
            var activity_duration = v.ActivityStartDate+' 至 '+v.ActivityEndDate;
            var status = v.ActivityStatus; //活动是否开启
            var activityid = v.ID;
            if(status==0 || status ==3 || status==-1){
                return true;
            }
            if(status==2){
                checkbox_str = '<input type="checkbox" class="weui-check activity_check" data-activitymodel="'+v.ActivityModel+'" ' +
                    'data-activitytype="'+v.ActivityType+'" data-activityagent="'+activityAgent+'" ' +
                    'data-categoryid="" data-type_text="'+type_text+'" data-activityid="'+activityid+'" ' +
                    'data-title="'+activity_title+'">\n' +
                    '           <i class="weui-icon-checked"></i>\n';
                status_str =   '<div class="weui-cell__ft" style="padding: 0px 10px;color: cornflowerblue;border: 1px solid cornflowerblue;border-radius: 5px;">\n' +
                    '           <span style="font-size: 14px;">进行中</span>\n';
            }else {
                checkbox_str = '';
                status_str =   '<div class="weui-cell__ft" style="padding: 0px 10px;color:rgb(255,153,0);border: 1px solid rgb(255,153,0);border-radius: 5px;">\n' +
                    '           <span style="font-size: 14px;">待开始</span>\n';
            }

            pre_activity_title=activity_title.substring(0,10);
            after_activity_title=activity_title.substring(10);
            console.log(pre_activity_title);
            console.log(after_activity_title);


            str += '<div class="weui-panel weui-cells weui-cells_checkbox">\n' +
                '                <div class="weui-panel__bd ">\n' +
                '                    <div class="weui-media-box weui-media-box_appmsg">\n' +
                '                        <div class="weui-media-box__bd">\n' +
                '                            <div class="weui-media-box__title">\n' +
                '                                <div class="weui-cell" style="padding:5px 10px;font-size:16px;">\n' +
                '                                    <label style="width: 13%">\n' +
                '                                            <div class="weui-cell__hd">\n' +
                checkbox_str +
                '                                            </div>\n' +
                '                                    </label>\n' +
                '                                    <div class="weui-cell__bd">\n' +
                pre_activity_title+'<br>'+ after_activity_title +
                '                                    </div>\n' +
                status_str +
                '                                    </div>\n' +
                '                                </div>\n' +
                '                            </div>\n' +
                '                            <div class="weui-media-box__desc"  style="margin-left: 10%">\n' +
                '                                <div class="weui-cell">\n' +
                '                                    <div class="weui-cell__bd">\n' +
                '                                            活动类型：'+activity_type+'\n' +
                '                                    </div>\n' +
                '                                </div>\n' +
                '                            </div>\n' +
                '                            <div class="weui-media-box__desc"  style="margin-left: 10%">\n' +
                '                                <div class="weui-cell">\n' +
                '                                    <div class="weui-cell__bd">\n' +
                '                                        活动时间：'+activity_duration+'\n' +
                '                                    </div>\n' +
                '                                </div>\n' +
                '                            </div>\n' +
                '                            <div class="weui-media-box__desc activity_products"  style="margin-left: 10%" data-activityagent="'+activityAgent+'" data-categoryid="" data-activityid="'+activityid+'">\n' +
                '                                <div class="weui-cell">\n' +
                '                                    <div class="weui-cell__bd" style="max-width: 100%;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;">\n' +
                '                                        活动产品：<span style="color: dodgerblue;">查看</span>\n' +
                '                                    </div>\n' +
                '                                </div>\n' +
                '                            </div>\n' +
                '                            <div class="weui-media-box__desc activity_product_introduce"  style="margin-left: 10%" data-activityagent="'+activityAgent+'" data-categoryid="" data-activityid="'+activityid+'">\n' +
                '                                <div class="weui-cell">\n' +
                '                                    <div class="weui-cell__bd" style="max-width: 100%;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;">\n' +
                '                                        活动介绍：<span style="color: dodgerblue;">查看</span>\n' +
                '                                    </div>\n' +
                '                                </div>\n' +
                '                            </div>\n' +
                '                        </div>\n' +
                '                    </div>\n' +
                '                </div>\n' +
                '            </div>';
        });

        $('#page_selectactivity').append(str);
    }


    function updateActivityList(datas,categoryID,activityAgent) {
        var str = '';
        $.each(datas,function (k,v) {
            var type = v.activityType;
            if(type==1){
                type_text='优惠组合';
            }else if(type==2){
                type_text='赠送产品';
            }if(type==3){
                type_text='赠送时间';
            }else if(type==4){
                type_text='限时折扣';
            }

            var activity_title = v.activityTitle;
            var activity_type = type_text;
            var activity_duration = v.activityStartDate+' 至 '+v.activityEndDate;
            var status = 1; //活动是否开启
            var activityid = v.activityID;
            pre_activity_title=activity_title.substring(0,10);
            after_activity_title=activity_title.substring(10);
            console.log(pre_activity_title);
            console.log(after_activity_title);

            if(status==1){
                checkbox_str = '<input type="checkbox" class="weui-check activity_check" data-activitymodel="'+v.activityModel+'" ' +
                    'data-activitytype="'+v.activityType+'" data-activityagent="'+activityAgent+'" ' +
                    'data-categoryid="'+categoryID+'" data-type_text="'+type_text+'" data-activityid="'+activityid+'" ' +
                    'data-title="'+activity_title+'">\n' +
                    '           <i class="weui-icon-checked"></i>\n';
                status_str =   '<div class="weui-cell__ft" style="padding: 0px 10px;color: cornflowerblue;border: 1px solid cornflowerblue;border-radius: 5px;">\n' +
                    '           <span style="font-size: 14px;">进行中</span>\n';
            }else {
                checkbox_str = '';
                status_str =   '<div class="weui-cell__ft" style="padding: 0px 10px;color:rgb(255,153,0);border: 1px solid rgb(255,153,0);border-radius: 5px;">\n' +
                    '           <span style="font-size: 14px;">待开始</span>\n';
            }

            str += '<div class="weui-panel weui-cells weui-cells_checkbox">\n' +
                '                <div class="weui-panel__bd ">\n' +
                '                    <div class="weui-media-box weui-media-box_appmsg">\n' +
                '                        <div class="weui-media-box__bd">\n' +
                '                            <div class="weui-media-box__title">\n' +
                '                                <div class="weui-cell" style="padding:5px 10px;font-size:16px;">\n' +
                '                                    <label style="width: 13%">\n' +
                '                                            <div class="weui-cell__hd">\n' +
                checkbox_str +
                '                                            </div>\n' +
                '                                    </label>\n' +
                '                                    <div class="weui-cell__bd">\n' +
                pre_activity_title+'<br>'+ after_activity_title +
                '                                    </div>\n' +
                status_str +
                '                                    </div>\n' +
                '                                </div>\n' +
                '                            </div>\n' +
                '                            <div class="weui-media-box__desc"  style="margin-left: 10%">\n' +
                '                                <div class="weui-cell">\n' +
                '                                    <div class="weui-cell__bd">\n' +
                '                                            活动类型：'+activity_type+'\n' +
                '                                    </div>\n' +
                '                                </div>\n' +
                '                            </div>\n' +
                '                            <div class="weui-media-box__desc"  style="margin-left: 10%">\n' +
                '                                <div class="weui-cell">\n' +
                '                                    <div class="weui-cell__bd">\n' +
                '                                        活动时间：'+activity_duration+'\n' +
                '                                    </div>\n' +
                '                                </div>\n' +
                '                            </div>\n' +
                '                            <div class="weui-media-box__desc activity_products"  style="margin-left: 10%" data-activityagent="'+activityAgent+'" data-categoryid="'+categoryID+'" data-activityid="'+activityid+'">\n' +
                '                                <div class="weui-cell">\n' +
                '                                    <div class="weui-cell__bd" style="max-width: 100%;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;">\n' +
                '                                        活动产品：<span style="color: dodgerblue;">查看</span>\n' +
                '                                    </div>\n' +
                '                                </div>\n' +
                '                            </div>\n' +
                '                            <div class="weui-media-box__desc activity_product_introduce"  style="margin-left: 10%" data-activityagent="'+activityAgent+'" data-categoryid="" data-activityid="'+activityid+'">\n' +
                '                                <div class="weui-cell">\n' +
                '                                    <div class="weui-cell__bd" style="max-width: 100%;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;">\n' +
                '                                        活动介绍：<span style="color: dodgerblue;">查看</span>\n' +
                '                                    </div>\n' +
                '                                </div>\n' +
                '                            </div>\n' +
                '                        </div>\n' +
                '                    </div>\n' +
                '                </div>\n' +
                '            </div>';
        });

        $('#page_selectactivity').append(str);
    }

    $("#container").on('change','.discountproductbuyyear',function (event) {
        var k = $(this).data('key');
        console.log(k);
        discountproductbuyyear = $(".discountproductbuyyear"+k).val();
        console.log(discountproductbuyyear);
        $(".product"+k).attr('data-buyyear',discountproductbuyyear);
        $(".otherproductlist").empty();
        cal4();
    });

    $("#container").on('change','.discountproductnum',function (event) {
        var k = $(this).data('key');
        console.log(k);
        buynum = $(".discountproductnum"+k).val();
        console.log(buynum);
        $(".product"+k).attr('data-buynum',buynum);
        cal4();
    });

    $("#container").on('change','.buyyearnum',function (event) {
        var k = $(this).data('num');
        console.log(k);
        buyyear = $(".buyyearnum"+k).val();
        console.log(buyyear);
        $(".product"+k).attr('data-buyyear',buyyear);
        $("#buyyear").attr('data-values',buyyear);
        cal1();
    });



    $("#container").on('change','.productnum',function (event) {
        var k = $(this).data('num');
        console.log(k);
        buynum = $(".productnum"+k).val();
        console.log(buynum);
        $(".product"+k).attr('data-buynum',buynum);
        cal1();
    });

    function renewDomainPage(){
        $('#renewdomain').empty();
        //续费域名
        var str2 ='';
        if((typeof domains)=='undefined') {
            return;
        }
        if( domains.length>0 && domaininpackage) {
            var str2 = '<div class="weui-cells weui-cells_checkbox">\n' +
                '                <div class="weui-cell weui-check__label" style="padding-left:0;display: flex;justify-content: space-between;">\n' +
                '                    <label style="display: flex;"  class="helpinfo"  data-title="域名权益转移" data-content="套餐内的域名权益，支持选择套餐内域名产品，另购内域名产品;">' +
                '                    <div class="weui-cell__hd">\n' +
                '                    </div>\n' +
                '                    <div class="weui-cell__bd">\n' +
                '                        <p>  域名权益转移 ' +
                '<span class="iconfont icon-iconset0143  " style="font-size: 16px;"></span>' +
                '</p>\n' +
                '                    </div></label>' +
                '                    <div class="weui-cell__ft">\n' +
                '                        <i class="iconfont icon-jian-tianchong packListshow2" style="color:#4994F2;font-size: 20px;margin-right: 10px;" data-flag="1"></i>' +
                '                    </div>' +
                '                </div>\n';

            var len = domains.length;
            $.each(domains, function (key, value) {
                var flagnum = 4;
                str2 += '                <label class="weui-cell weui-check__label packshow2">\n' +
                    '                    <div class="weui-cell__hd">\n' +
                    '                        <input type="checkbox"  data-key="'+key+'" class="weui-check packSpecificationList2  packSpecification'+flagnum+'" data-renewprice="'+value.ProductSpecifications[0].RenewPrice+'" data-count="'+value.ProductSpecifications[0].Count+'" data-packid="' + value.PackageID + '" data-productid="' + value.ProductID + '" data-closeDate="' + value.ProductSpecifications[0].CloseDate + '" data-productspecificationsid="' + value.ProductSpecifications[0].ID + '" data-producttitle="' + value.ProductTitle + '" data-productspecificationstitle="' + value.ProductSpecifications[0].Title + '" '+(key==0?'checked':'')+'>\n' +
                    '                        <i class="weui-icon-checked"></i>\n' +
                    '                    </div>\n' +
                    '                    <div class="weui-cell__bd">\n' +
                    '                        <p>' + value.ProductTitle + '--' + value.ProductSpecifications[0].Title + '--' + value.ProductSpecifications[0].CloseDate + '</p>\n' +
                    '                    </div>\n' +
                    '                </label>\n';

            });
            str2 += '            </div>';
        }
        $('#renewdomain').append(str2);
    }


    function updateProductActivity(data){
        type = data.ActivityType;
        $("#activity_product").empty();
        var activity_title = $(".activity_check").filter('input:checked').data('title');
        var activity_type_text =  $(".activity_check").filter('input:checked').data('type_text');
        $("#buydate").attr('data-mindate',data.ActivityStartDate);
        $("#buydate").attr('data-maxdate',data.ActivityEndDate);
        $("#buydate").attr('min',data.ActivityStartDate);
        $("#buydate").attr('max',data.ActivityEndDate);

        var str = '';
        str += '  <div class="weui-cell weui-cell-noborder" style="border-bottom: 1px solid lightgrey;" id="typetitle">\n' +
            '                    <div style="margin-left: 20px;">\n' +
            '                        <label id="activity_detail" data-activitystartdate="'+data.ActivityStartDate+'" data-activityenddate = "'+data.ActivityEndDate+'" data-combinationprice="'+data.CombinationPrice+'" data-activitytitle="'+data.ActivityTitle+'" data-activitytype="'+type+'" data-isstop="'+data.IsStop+'" data-isallagent="'+data.IsAllAgent+'" data-id="'+data.ID+'" data-activityrange="'+data.ActivityRange+'" data-activitymodel="'+data.ActivityModel+'" data-activityid="'+data.ActivityID+'" data-activityagent="'+data.ActivityAgent+'" style="text-align:right;font-weight: bold;font-size: 16px;">\n' +
            '                        '+activity_title+'<br/>\n' +
            '                        </label>\n' +
            '                        <label style="font-size: 12px;color: lightskyblue;">活动类型：'+activity_type_text+'</label>\n' +
            '                    </div>\n' +
            '                </div>\n' +
            '\n';
        if(type==1){
            //优惠组合
            $(data.ProductList).each(function (k, v) {
                console.log(v);
                var productname = '';
                //产品名称
                if(v.ProductID){
                    productname = v.ProductTitle;
                }else{
                    productname = v.PackageTitle;
                }
                console.log(v.PackageID);
                if(v.PackageID){
                    str += '<input type="hidden"  id="productclassone" class="productclassone" value="'+v.CategoryID+'"  data-values="'+v.CategoryID+'"/>';
                    str += '<input type="hidden"  id="productclasstwo" class="productclasstwo" value="'+v.PackageID+'"  data-values="'+v.PackageID+'"/>';
                }

                var producttype = v.SpecificationTitle?v.SpecificationTitle:'无';

                str +='<label class="product product'+k+'" data-key="'+k+'" data-productname="'+productname+'" data-producttype="'+producttype+'"  data-buynum="'+v.ActivityThresholdCount+'" data-buyyear="'+v.ActivityThresholdBuyTerm+'" data-type="'+type+'" data-uselimit="'+v.UseLimit+'" data-specificationid="'+v.SpecificationID+'" data-productid="'+v.ProductID+'" data-packageid="'+v.PackageID+'" data-categoryid="'+v.CategoryID+'" ></label>';
                str += '             <div class="weui-cell weui-cell-noborder" style="border-top: 1px solid darkgrey;">\n' +
                    '                    <div class="weui-cell__hd"><label class="weui-label" style="text-align:right;">产品名称：</label></div>\n' +
                    '                    <div class="weui-cell__bd">\n' +
                    '                        <input class="weui-input" type="text" name="buyproduct[]" value="'+productname+'" placeholder="产品名称" readonly>\n' +
                    '                    </div>\n' +
                    '                </div>\n';

                //产品规格
                str +=    '                <div class="weui-cell weui-cell-noborder">\n' +
                    '                    <div class="weui-cell__hd"><label class="weui-label" style="text-align:right;">规格名称：</label></div>\n' +
                    '                    <div class="weui-cell__bd">\n' +
                    '                        <input class="weui-input" type="text" name="producttype_activity[]" value="'+producttype+'" placeholder="规格名称" readonly>\n' +
                    '                    </div>\n' +
                    '                </div>\n';

                var buyyearstr = '<select class="weui-select buyyearnum buyyearnum'+k+'" id="'+((v.PackageID)?'buyyear':('buyyear'+k))+'" data-values="'+v.ActivityThresholdBuyTerm+'" data-num="'+k+'" style="height: 30px;line-height: 30px;" name="buyyear" placeholder="购买年限">';
                if(v.CanRenew){
                    for(i=v.ActivityThresholdBuyTerm;i<=10;i++){
                        buyyearstr += '<option value="'+i+'">'+i+'年</option>'
                    }
                }else{
                    buyyearstr += '<option value="1">1年</option>';
                }
                buyyearstr += '</select>';

                //购买年限
                str +=    '                <div class="weui-cell weui-cell-noborder">\n' +
                    '                    <div class="weui-cell__hd"><label class="weui-label" style="text-align:right;">购买年限：</label></div>\n' +
                    '                    <div class="weui-cell__bd">\n' +
                    buyyearstr+
                    // '                        <input class="weui-input" type="text" name="buyyear[]" placeholder="购买年限" readonly>\n' +
                    '\n' +
                    '                    </div>\n' +
                    '                    <div class="weui-cell__ft weui-cell__ft__add"></div>\n' +
                    '                </div>\n';

                var buyproductnumstr = '<select class="weui-select productnum productnum'+k+'" data-num="'+k+'" style="height: 30px;line-height: 30px;" name="buyproductnum_activity" placeholder="产品数量" >';
                var SpecificationUnit = v.SpecificationUnit?v.SpecificationUnit:'';
                var uselimit = v.UseLimit;
                uselimitnum = (uselimit?uselimit:999);
                var minLimit = v.ActivityThresholdCount>1?v.ActivityThresholdCount:1;
                for(j=minLimit;j<=uselimitnum;j++){
                    buyproductnumstr += '<option value="'+j+'">'+j+SpecificationUnit+'</option>'
                }
                buyproductnumstr += '</select>';
                str +=     '                <div class="weui-cell weui-cell-noborder">\n' +
                    '                    <div class="weui-cell__hd"><label class="weui-label" style="text-align:right;">产品数量：</label></div>\n' +
                    '                    <div class="weui-cell__bd">\n' +
                    buyproductnumstr+
                    // '                        <input class="weui-input" type="text" name="buyproductnum_activity[]" placeholder="产品数量" readonly>\n' +
                    '                    </div>\n';

                str +=  '                    <div class="weui-cell__ft weui-cell__ft__add"></div>\n'+
                    '                </div>\n';

            });
            str += '             <div class="weui-cell weui-cell-noborder" style="border-top: 1px solid darkgrey;">\n' +
                '                    <div class="weui-cell__hd"><label class="weui-label" style="text-align:right;">市场活动价：</label></div>\n' +
                '                    <div class="weui-cell__bd">\n' +
                '                        <input class="weui-input" type="text" name="marketactivityprice" value="￥'+data.CombinationMarketPrice+'" placeholder="市场活动价" readonly>\n' +
                '                        <input class="weui-input" type="hidden" name="marketactivitypriceval" placeholder="" readonly>\n' +
                '                    </div>\n' +
                '                </div>\n';

            $("#activity_product").append(str);
            productlist = data.ProductList;
            giftproductlist=data.GiftProductList;
            cal1();
            return;

        }else if(type==2){
            //赠送产品
            str += '<input type="hidden" id="productclassone" value="" data-value="">';
            str += '<input type="hidden" id="productclasstwo" value="" data-value="">';
            str +='<label class="product product0" data-key="0" data-buynum="" data-productname="" data-producttype=""  data-buyyear="" data-type="'+type+'" data-uselimit="" data-specificationid="" data-productid="" data-packageid="" data-categoryid="" ></label>';
            str += '             <div class="weui-cell weui-cell-noborder">\n' +
                '                    <div class="weui-cell__hd"><label class="weui-label" style="text-align:right;">产品名称：</label></div>\n' +
                '                    <div class="weui-cell__bd">\n' +
                '                        <input class="weui-input" type="text" data-type="'+type+'"  id="buyproduct" placeholder="产品名称" readonly>\n' +
                '                    </div>\n' +
                '                    <div class="weui-cell__ft weui-cell__ft__add"></div>\n' +
                '                </div>\n' +
                '                <div class="weui-cell weui-cell-noborder">\n' +
                '                    <div class="weui-cell__hd"><label class="weui-label" style="text-align:right;">规格名称：</label></div>\n' +
                '                    <div class="weui-cell__bd">\n' +
                '                        <input class="weui-input" type="text" id="producttype_activity" placeholder="规格名称" readonly>\n' +
                '                    </div>\n' +
                '                </div>\n' +
                '                <div class="weui-cell weui-cell-noborder">\n' +
                '                    <div class="weui-cell__hd"><label class="weui-label" style="text-align:right;">购买年限：</label></div>\n' +
                '                    <div class="weui-cell__bd">\n' +
                '                        <input class="weui-input" type="text" id="buyyear" placeholder="选择年限" readonly>\n' +
                '\n' +
                '                    </div>\n' +
                '                    <div class="weui-cell__ft weui-cell__ft__add"></div>\n' +
                '                </div>\n' +
                '                <div class="weui-cell weui-cell-noborder">\n' +
                '                    <div class="weui-cell__hd"><label class="weui-label" style="text-align:right;">产品数量：</label></div>\n' +
                '                    <div class="weui-cell__bd">\n' +
                '                        <input class="weui-input" type="text" id="buyproductnum_activity" placeholder="产品数量" readonly>\n' +
                '                    </div>\n' +
                '                    <div class="weui-cell__ft weui-cell__ft__add"></div>\n' +
                '                </div>\n';
            var give_product_str = '';
            $(data.GiftProductList).each(function (k,val){
                var giveproducttitle = (val.PackageTitle?val.PackageTitle:val.ProductTitle);
                var giveproductyear =  val.BuyTerm;
                var giveproductnum =  val.Count;
                var packageid =  val.PackageID;
                var categoryid =  val.CategoryID;
                var specialficationid =  val.SpecificationID;
                var productid = val.ProductID;
                var userlimit = val.UseLimit;
                give_product_str += '<div  id="giveproductlist'+k+'" class="giveproductlistall" data-giveproductname="'+giveproducttitle+'" data-key="'+k+'"  data-buynum="'+giveproductnum+'" data-buyyear="'+giveproductyear+'"  data-uselimit="'+userlimit+'" data-specificationid="'+specialficationid+'" data-productid="'+productid+'" data-packageid="'+packageid+'" data-categoryid="'+categoryid+'">' +
                    '              <div class="weui-cell weui-cell-noborder"  style="border-top: 1px solid lightgrey;">\n' +
                    '                    <div class="weui-cell__hd"><label class="weui-label" style="text-align:right;">赠送产品：</label></div>\n' +
                    '                    <div class="weui-cell__bd">\n' +
                    '                        <input class="weui-input" type="text" name="buyproductgiveproduct_activity" value="'+giveproducttitle+'"  placeholder="赠送产品" readonly>\n' +
                    '                    </div>\n' +
                    '                    <div class="weui-cell__ft">\n' +
                    '                        <div class="weui-count" style="height: 25px;">\n' +
                    '                             <a class="weui-count__btn weui-count__decrease weui-count__decrease_deleted giveproductlist" style="border:none;margin-left:5px;" data-id="'+k+'"></a>\n' +
                    '                        </div>\n' +
                    '                    </div>'+
                    '                </div>\n' +
                    '                <div class="weui-cell weui-cell-noborder">\n' +
                    '                    <div class="weui-cell__hd"><label class="weui-label" style="text-align:right;">赠送年限：</label></div>\n' +
                    '                    <div class="weui-cell__bd">\n' +
                    '                        <input class="weui-input giveproductyear'+k+'" type="text" name="buyproductgiveyear_activity" value="'+giveproductyear+'年" data-values="'+giveproductyear+'" placeholder="赠送年限" readonly>\n' +
                    '                    </div>\n' +
                    '                </div>\n' +
                    '                <div class="weui-cell weui-cell-noborder" style="border-bottom:1px solid lightgrey;">\n' +
                    '                    <div class="weui-cell__hd"><label class="weui-label" style="text-align:right;">产品数量：</label></div>\n' +
                    '                    <div class="weui-cell__bd">\n' +
                    '                        <input class="weui-input giveproductnum'+k+'" type="text" name="buyproductnumgive_activity" value="'+giveproductnum+'" data-values="'+giveproductnum+'" placeholder="产品数量" readonly>\n' +
                    '                    </div>\n' +
                    '                </div></div>\n';

            });
            str += '<div id="giveproductlist">' +
                give_product_str+
                '</div>';

            str += '                <div class="weui-cell weui-cell-noborder">\n' +
                '                    <div class="weui-cell__hd"><label class="weui-label" style="text-align:right;">市场活动价：</label></div>\n' +
                '                    <div class="weui-cell__bd">\n' +
                // '                        <input class="weui-input" type="text" id="buyproductmarket_activity" placeholder="￥10000" readonly value="￥10000">\n' +
                '                            <input class="weui-input" type="text" name="marketactivityprice" placeholder="市场价格" readonly>\n' +
                '                            <input class="weui-input" type="hidden" name="marketactivitypriceval" placeholder="" readonly>\n' +
                // '                            <input class="weui-input" type="hidden" id="Price" placeholder="市场价格" readonly>'+
                '                    </div>\n' +
                '                </div>\n';


            $("#activity_product").append(str);

            productlist = data.ProductList;
            giftproductlist=data.GiftProductList;

            //添加跳转 添加更多赠送产品
            var moregiveproductstr = '        <div class="page__bd page__bd_spacing" style="margin-top:20px;">\n' +
                '            <div class="weui-panel  weui-panel-noborder">\n' +
                '                <div class="weui-panel__bd  weui-panel-noborder">\n' +
                '                    <div class="weui-cell weui-cell-noborder">\n' +
                '                        <div class="weui-cell__hd"  style="margin: auto;color: #1296DB;" id="moregiveproduct">\n' +
                '                            <span class="iconfont icon-zengjia" style="color: #1296DB"></span> 添加赠送产品</div>\n' +
                '                    </div>\n' +
                '                </div>\n' +
                '            </div>'+
                '</div>';

            $("#activity_product").parent().after(moregiveproductstr);
            return;

        }else if(type==3){
            str += '<input type="hidden" id="productclassone" value="" data-values="">';
            str += '<input type="hidden" id="productclasstwo" value="" data-values="">';
            str +='<label class="product product0" data-key="0" data-buynum="" data-buyyear="" data-productname="" data-producttype="" data-type="'+type+'" data-uselimit="" data-specificationid="" data-productid="" data-packageid="" data-categoryid="" ></label>';
            str += '             <div class="weui-cell weui-cell-noborder">\n' +
                '                    <div class="weui-cell__hd"><label class="weui-label" style="text-align:right;">产品名称：</label></div>\n' +
                '                    <div class="weui-cell__bd">\n' +
                '                        <input class="weui-input" type="text" data-type="'+type+'" id="buyproduct" placeholder="产品名称" readonly>\n' +
                '                    </div>\n' +
                '                    <div class="weui-cell__ft weui-cell__ft__add"></div>\n' +
                '                </div>\n' +
                '                <div class="weui-cell weui-cell-noborder">\n' +
                '                    <div class="weui-cell__hd"><label class="weui-label" style="text-align:right;">规格名称：</label></div>\n' +
                '                    <div class="weui-cell__bd">\n' +
                '                        <input class="weui-input" type="text" id="producttype_activity" placeholder="规格名称" readonly>\n' +
                '                    </div>\n' +
                '                </div>\n' +
                '                <div class="weui-cell weui-cell-noborder">\n' +
                '                    <div class="weui-cell__hd"><label class="weui-label" style="text-align:right;">购买年限：</label></div>\n' +
                '                    <div class="weui-cell__bd">\n' +
                '                        <input class="weui-input" type="text" id="buyyear" placeholder="选择年限" readonly>\n' +
                '\n' +
                '                    </div>\n' +
                '                    <div class="weui-cell__ft weui-cell__ft__add"></div>\n' +
                '                </div>\n' +
                '                <div class="weui-cell weui-cell-noborder">\n' +
                '                    <div class="weui-cell__hd"><label class="weui-label" style="text-align:right;">产品数量：</label></div>\n' +
                '                    <div class="weui-cell__bd">\n' +
                '                        <input class="weui-input" type="text" id="buyproductnum_activity" placeholder="产品数量" readonly>\n' +
                '                    </div>\n' +
                '                    <div class="weui-cell__ft weui-cell__ft__add"></div>\n' +
                '                </div>\n';

            str += '<div class="weui-cell weui-cell-noborder"  style="border-top: 1px solid lightgrey;">\n' +
                '                    <div class="weui-cell__hd"><label class="weui-label" style="text-align:right;">赠送年限：</label></div>\n' +
                '                    <div class="weui-cell__bd">\n' +
                '<select id="buyproductgiveproduct_activity" class="weui-select" name="buyproductgiveproduct_activity">' +
                '</select>'+
                // '                        <input class="weui-input" type="text" id="buyproductgiveproduct_activity" placeholder="赠送年限" readonly value="">\n' +
                '                    </div>\n' +
                '                    <div class="weui-cell__ft weui-cell__ft__add"></div>\n' +
                '                </div>\n' +
                '                <div class="weui-cell weui-cell-noborder">\n' +
                '                    <div class="weui-cell__hd"><label class="weui-label" style="text-align:right;">市场活动价：</label></div>\n' +
                '                    <div class="weui-cell__bd">\n' +
                // '                        <input class="weui-input" type="text" id="buyproductmarket_activity" placeholder="￥10000" readonly value="￥10000">\n' +
                '                            <input class="weui-input" type="text"  name="marketactivityprice" placeholder="市场价格" readonly>\n' +
                '                        <input class="weui-input" type="hidden" name="marketactivitypriceval" placeholder="" readonly>\n' +
                // '                            <input class="weui-input" type="hidden" id="Price" placeholder="市场价格" readonly>'+
                '                    </div>\n' +
                '                </div>\n';
            $("#activity_product").append(str);
            productlist = data.ProductList;
            return;
        }else if(type==4){
            //限时折扣
            $("#activity_product").append(str);

            productlist = data.ProductList;
            //添加跳转 添加更多赠送产品
            var moregiveproductstr = '<div class="page__bd page__bd_spacing" style="margin-top:20px;">\n' +
                '            <div class="weui-panel  weui-panel-noborder">\n' +
                '                <div class="weui-panel__bd  weui-panel-noborder">\n' +
                '                    <div class="weui-cell weui-cell-noborder">\n' +
                '                        <div class="weui-cell__hd"  style="margin: auto;color: #1296DB;" id="morediscountproduct">\n' +
                '                            <span class="iconfont icon-zengjia" style="color: #1296DB"></span> 添加更多折扣产品</div>\n' +
                '                    </div>\n' +
                '                </div>\n' +
                '            </div>'+
                '</div>';

            $("#activity_product").parent().after(moregiveproductstr);
            return;
        }
    }
    /** 页面生成的各种方法end **/

    $("#container").on('change','#buyyear',function (event) {
        var activitytype = $("#activity_detail").data('activitytype');
        if(activitytype==1){
            $(".otherproductlist").empty();
            cal1();
        }
    });


    //切换购买数量
    $("#container").on("change",'.productnum',function (event) {
        var num = $(this).data('num');
        var productnum = $(".productnum"+num).val();
        var categoryid = $(".product"+num).data('categoryid');
        var packageid = $(".product"+num).data('packageid');
        var specificationid = $(".product"+num).data('specificationid');
        var uselimit = $(".product"+num).data('uselimit');
        var productid = $(".product"+num).data('productid');

        console.log(productnum);
    });

    //切换购买年限
    $("#container").on("change",'.buyyearnum',function (event) {
        var num = $(this).data('num');
        var buyyearnum = $(".buyyearnum"+num).val();
        console.log(buyyearnum);
    });

    $("#container").on("click",".activity_product_introduce",function (event) {
        var activityId = $(this).data('activityid');
        var activityAgent = $(this).data('activityagent');
        console.log(activityAgent);
        //获取产品列表
        $.ajax(
            {
                url: '/index.php?module=TyunWebBuyService&action=GetProductActivityDetail',
                type: 'POST',
                dataType: 'json',
                data:{
                    "activityID":activityId,
                },
                success: function (data) {
                    console.log(data);
                    $.hideLoading();
                    if(data.success){
                        var introduce=data.data.ActivityIntroduce;
                        $.alert(introduce,'活动介绍');
                    }
                }
            }
        );
    });

    $("#container").on("click",".activity_products",function (event) {
        var activityId = $(this).data('activityid');
        var activityAgent = $(this).data('activityagent');
        console.log(activityAgent);
        //获取产品列表
        $.ajax(
            {
                url: '/index.php?module=TyunWebBuyService&action=GetProductActivityDetail',
                type: 'POST',
                dataType: 'json',
                data:{
                    "activityID":activityId,
                },
                success: function (data) {
                    console.log(data);
                    $.hideLoading();
                    if(data.success){
                        var datas=data.data.ProductList;
                        activitytype = data.data.ActivityType;
                        var str = "活动产品:";
                        if(activitytype==4){
                            $(datas).each(function (k,v) {
                                if(v.PackageTitle){
                                    str += v.PackageTitle;
                                }else{
                                    str += v.ProductTitle;
                                }
                                str += "(";
                                str += v.ActivityPrice+'原价'+v.OriginalPrice.price;
                                str += "),";
                            });
                        }else if(activitytype==3){
                            $(datas).each(function (k,v) {
                                if(v.PackageTitle){
                                    str += v.PackageTitle;
                                }else{
                                    str += v.ProductTitle;
                                }
                                str += "(";
                                $(v.GiveRule).each(function (k1, v1) {
                                    str += '买'+v1.BuyTerm+'年以上送'+v1.GiveTerm+'年 '
                                });
                                str += "),";
                            });
                        }else if(activitytype==2){
                            $(datas).each(function (k,v) {
                                if(v.PackageTitle){
                                    str += v.PackageTitle+' ';
                                }else{
                                    str += v.ProductTitle+' ';
                                }
                            });

                            str += "(赠送产品:";
                            $(data.data.GiftProductList).each(function (k1, v1) {
                                str += v1.PackageTitle?v1.PackageTitle:v1.ProductTitle+' ';
                            });
                            str += "),";
                        }else if(activitytype==1){
                            $(datas).each(function (k,v) {
                                if(v.PackageTitle){
                                    str += v.PackageTitle+' ';
                                }else{
                                    str += v.ProductTitle+' ';
                                }

                                str +="(";
                                str += v.ActivityThresholdBuyTerm+','+v.ActivityThresholdCount+v.SpecificationUnit+' ';
                                str += "),";
                            });
                        }

                        str=str.substring(0,str.length-1);
                        $.alert(str,'活动产品');
                    }
                }
            }
        );

    });

    $("#container").on('click','.activity_check',function (event) {
        var activity_check = $('.activity_check').filter('input:checked');
        if(activity_check.length>1){
            $.alert('最多选择一项');
            $(this).trigger('click');
            return;
        }
    });

    $("#container").on('click','#submitselectactivity',function (event) {
        var activity_check = $('.activity_check').filter('input:checked');
        if(activity_check.length<1){
            $.alert('请选择要参加的活动');
            return;
        }
        $(".weui-picker-container").remove();
        window.pageManager.go('activityproduct');
    });

    $("#container").on('click',"#submitmoregiveproduct",function () {
        var giveproduct=$('.giveproduct').filter('input:checked');
        if(giveproduct.length==0){
            pageManager.back();
            return;
        }
        $.confirm("确定要添加赠送产品？", "", function(){
            var give_product_str = '';
            $("#giveproductlist").empty();
            $.each(giveproduct,function (key, val) {
                k = $(val).data('key');
                var giveproducttitle = $(".giveproduct"+k).data('producttitle');
                var giveproductyear = $(".giveproduct"+k).data('giveproductyear');
                var giveproductnum = $(".giveproduct"+k).data('giveproductnum');
                var packageid = $(".giveproduct"+k).data('packageid');
                var categoryid = $(".giveproduct"+k).data('categoryid');
                var specialficationid = $(".giveproduct"+k).data('specialficationid');
                var productid = $(".giveproduct"+k).data('productid');
                var userlimit = $(".giveproduct"+k).data('userlimit');
                give_product_str += '<div  id="giveproductlist'+k+'" class="giveproductlistall" data-giveproductname="'+giveproducttitle+'" data-key="'+k+'"  data-buynum="'+giveproductnum+'" data-buyyear="'+giveproductyear+'"  data-uselimit="'+userlimit+'" data-specificationid="'+specialficationid+'" data-productid="'+productid+'" data-packageid="'+packageid+'" data-categoryid="'+categoryid+'">' +
                    '              <div class="weui-cell weui-cell-noborder"  style="border-top: 1px solid lightgrey;">\n' +
                    '                    <div class="weui-cell__hd"><label class="weui-label" style="text-align:right;">赠送产品：</label></div>\n' +
                    '                    <div class="weui-cell__bd">\n' +
                    '                        <input class="weui-input" type="text" name="buyproductgiveproduct_activity" value="'+giveproducttitle+'"  placeholder="赠送产品" readonly>\n' +
                    '                    </div>\n' +
                    '                    <div class="weui-cell__ft">\n' +
                    '                        <div class="weui-count" style="height: 25px;">\n' +
                    '                             <a class="weui-count__btn weui-count__decrease weui-count__decrease_deleted giveproductlist" style="border:none;margin-left:5px;" data-id="'+key+'"></a>\n' +
                    '                        </div>\n' +
                    '                    </div>'+
                    '                </div>\n' +
                    '                <div class="weui-cell weui-cell-noborder">\n' +
                    '                    <div class="weui-cell__hd"><label class="weui-label" style="text-align:right;">赠送年限：</label></div>\n' +
                    '                    <div class="weui-cell__bd">\n' +
                    '                        <input class="weui-input giveproductyear'+k+'" type="text" name="buyproductgiveyear_activity" value="'+giveproductyear+'年" data-values="'+giveproductyear+'" placeholder="赠送年限" readonly>\n' +
                    '                    </div>\n' +
                    '                </div>\n' +
                    '                <div class="weui-cell weui-cell-noborder" style="border-bottom:1px solid lightgrey;">\n' +
                    '                    <div class="weui-cell__hd"><label class="weui-label" style="text-align:right;">产品数量：</label></div>\n' +
                    '                    <div class="weui-cell__bd">\n' +
                    '                        <input class="weui-input giveproductnum'+k+'" type="text" name="buyproductnumgive_activity" value="'+giveproductnum+'" data-values="'+giveproductnum+'" placeholder="产品数量" readonly>\n' +
                    '                    </div>\n' +
                    '                </div></div>\n';

            });

            $("#giveproductlist").append(give_product_str);
            pageManager.back();
        });
    });

    $("#container").on('click',"#submitmorediscountproduct",function () {
        var discountproduct=$('.discountproduct').filter('input:checked');
        if(discountproduct.length==0){
            pageManager.back();
            return;
        }
        $.confirm("确定要添加折扣产品？", "", function(){

            var give_product_str = '';
            $("#discountall").remove();
            var str = '<div id="discountall">';
            var canrenewotherproductids = []

            $.each(discountproduct,function (key, val) {
                k = $(val).data('key');
                var giveproducttitle = $(".discountproduct"+k).data('producttitle');
                var giveproductyear = $(".discountproductyear"+k).data('values');
                var giveproductnum = $(".discountproductnum"+k).data('values');
                var specificationstitle = $(".discountproduct"+k).data('specificationstitle');

                var categoryid = $(".discountproduct"+k).data('categoryid');
                var packageid = $(".discountproduct"+k).data('packageid');
                var specialficationid = $(".discountproduct"+k).data('specialficationid');
                var productid = $(".discountproduct"+k).data('productid');
                var userlimit = $(".discountproduct"+k).data('userlimit');
                var canrenew = $(".discountproduct"+k).data('canrenew');


                var buyyearstr= '';
                productdetail = productlist[k];
                if(canrenew){
                    for(i=productdetail.ActivityThresholdBuyTerm;i<=10;i++){
                        var selectstr ='';
                        if(giveproductyear==i){
                            selectstr = "selected";
                        }
                        buyyearstr += '<option value="'+i+'" '+selectstr+' >'+i+'年</option>';
                    }
                }else{
                    buyyearstr += '<option value="'+1+'" selected >1年</option>';
                }


                var productnumstr = '';
                userlimit = productdetail.UseLimit?productdetail.UseLimit:999;
                minLimit = productdetail.ActivityThresholdCount>1 ?productdetail.ActivityThresholdCount : 1;
                for(j=minLimit;j<=userlimit;j++){
                    var selectstr ='';
                    if(giveproductnum==j){
                        selectstr = "selected";
                    }
                    productnumstr += '<option value="'+j+'" '+selectstr+' >'+j+productdetail.SpecificationUnit+'</option>';
                }


                str += '<div id="discountproductlist'+k+'" style="border-top:1px solid gainsboro;">';
                str += '<input type="hidden" id="productclassone" value="'+categoryid+'" data-values="'+categoryid+'">';
                str += '<input type="hidden" id="productclasstwo" value="'+packageid+'" data-values="'+packageid+'">';
                str += '<label class="discountlabel product product'+k+'" data-productname="'+giveproducttitle+'" data-producttype="'+(specificationstitle?specificationstitle:'无')+'" data-buynum="'+giveproductnum+'" data-buyyear="'+giveproductyear+'"  data-uselimit="'+userlimit+'" data-specificationid="'+specialficationid+'" data-productid="'+productid+'" data-packageid="'+packageid+'" data-categoryid="'+categoryid+'"  data-key="'+k+'"></label>';
                str += '             <div class="weui-cell weui-cell-noborder">\n' +
                    '                    <div class="weui-cell__hd"><label class="weui-label" style="text-align:right;">产品名称：</label></div>\n' +
                    '                    <div class="weui-cell__bd">\n' +
                    '                        <input class="weui-input" type="text" name="buyproduct" placeholder="产品名称" value="'+giveproducttitle+'" readonly>\n' +
                    '                    </div>\n' +
                    '                    <div class="weui-cell__ft">\n' +
                    '                        <div class="weui-count" style="height: 25px;">\n' +
                    '                             <a class="weui-count__btn weui-count__decrease weui-count__decrease_deleted discountproductlist" data-id="'+k+'" style="border:none;margin-left:5px;" data-id="0"></a>\n' +
                    '                        </div>\n' +
                    '                    </div>'+
                    '                </div>\n' +
                    '                <div class="weui-cell weui-cell-noborder">\n' +
                    '                    <div class="weui-cell__hd"><label class="weui-label" style="text-align:right;">规格名称：</label></div>\n' +
                    '                    <div class="weui-cell__bd">\n' +
                    '                        <input class="weui-input" type="text" name="producttype_activity" placeholder="规格名称" value="'+(specificationstitle?specificationstitle:'无')+'" readonly>\n' +
                    '                    </div>\n' +
                    '                </div>\n' +
                    '                <div class="weui-cell weui-cell-noborder">\n' +
                    '                    <div class="weui-cell__hd"><label class="weui-label" style="text-align:right;">选择年限：</label></div>\n' +
                    '                    <div class="weui-cell__bd">\n' +
                    '<select class="weui-select discountproductbuyyear discountproductbuyyear'+k+'" id="'+(packageid?'buyyear':('buyyear'+k))+'" data-key="'+k+'" name="buyyear" data-values="'+giveproductyear+'" style="height:30px;line-height:30px;">'+
                    buyyearstr+
                    '</select>'+
                    // '                        <input class="weui-input discountproductbuyyear'+k+'" type="text" data-key="'+k+'" name="buyyear" placeholder="升级年限" value="'+giveproductyear+'年" data-values="'+giveproductyear+'" readonly>\n' +
                    '\n' +
                    '                    </div>\n' +
                    '                    <div class="weui-cell__ft weui-cell__ft__add"></div>\n' +
                    '                </div>\n' +
                    '                <div class="weui-cell weui-cell-noborder">\n' +
                    '                    <div class="weui-cell__hd"><label class="weui-label" style="text-align:right;">产品数量：</label></div>\n' +
                    '                    <div class="weui-cell__bd">\n' +
                    '<select class="weui-select discountproductnum discountproductnum'+k+'" data-key="'+k+'" name="buyproductnum_activity" data-values="'+giveproductnum+'" style="height:30px;line-height:30px;">'+
                    productnumstr+
                    '</select>'+
                    // '                        <input class="weui-input discountproductnum'+k+'" type="text" data-key="'+k+'" name="buyproductnum_activity" placeholder="产品数量" value="'+giveproductnum+'" data-values="'+giveproductnum+'" readonly>\n' +
                    '                    </div>\n' +
                    '                    <div class="weui-cell__ft weui-cell__ft__add"></div>\n' +
                    '                </div></div>' +

                    '\n';

                //记录所有可续费单品的productid
                if(!packageid && packageid!==0 && productid){
                    canrenewotherproductids.push(productid)
                }
            });

            str +=   '  <div class="weui-cell weui-cell-noborder">\n' +
            '                    <div class="weui-cell__hd"><label class="weui-label" style="text-align:right;">市场活动价：</label></div>\n' +
            '                    <div class="weui-cell__bd">\n' +
            '                        <input class="weui-input" type="text" name="marketactivityprice" placeholder="" readonly>\n' +
            '                        <input class="weui-input" type="hidden" name="marketactivitypriceval" placeholder="" readonly>\n' +
            '                    </div>\n' +
            '           </div>';

            str += '</div>';

            window.canrenewotherproductids = canrenewotherproductids;
            $("#typetitle").after(str);
            // judgecontain();
            cal4();
            pageManager.back();
        });
    });

    $('#container').on("click",'#otherproduct2',function(){
        if(isMoreCategory()){
            $.toast('存在多个套餐，不允许添加另购产品',"text");
            return false;
        }

        var productclasstwo=$('#productclasstwo').val();
        if(productclasstwo==undefined || productclasstwo==0){
            $.toast('请先选择套餐',"text");
            return false;
        }

        if(!checkProductValue3(2)){
            $.toast('请先完善信息！',"text");
            return false;
        }
        window.pageManager.go('otherproduct');
    });
    checkProductValue3=function (params){
        var productclassone=$('#productclassone').val();
        var productclasstwo=$('#productclasstwo').val();
        var activitytype = $("#activity_detail").data('activitytype');
        var buyyear=$('#buyyear').data('values');

        // buyyear = 3;
        // var agents=$('#agents').val();
        // if(agents<1){
        //     return false;
        // }
        if(productclassone!=undefined || productclasstwo!=undefined){
            if(buyyear<1 || buyyear==undefined){
                return false;
            }
        }else{
            if(productclassone==''){
                return false;
            }
            if(productclasstwo==''){
                return false;
            }
        }


        if(params==1){
            var servicetotal=$('#servicetotal').val();
            var totalmarketprice = $("#totalmarketprice").val();
            if(servicetotal>0 && totalmarketprice >0){
                $('#submitid').attr('data-value',2);
                $('#submitfrom2').css('backgroundColor','#4994F2');
                $('#electronnextstep2').css('backgroundColor','#4994F2');
                return true;
            }else{
                //如果是院校版不走这里。
                if($("#productclassonecollege").data("values")!=7 && $("#productclassonecollege").data("values")!=9){
                    $('#submitid').attr('data-value',1);
                    $('#submitfrom2').css('backgroundColor','#999999');
                    $('#electronnextstep2').css('backgroundColor','#999999');
                    return false;
                }
            }
            return false;

        }
        return true;
    }

    checkProductValue2=function (params){
        if($("input[name=marketactivitypriceval]").val()<=0){
            return false;
        }
        var servicetotal=$('#servicetotal').val();
        var totalmarketprice = $("#totalmarketprice").val();
        if(servicetotal>0 && totalmarketprice >0){
            $('#submitid').attr('data-value',2);
            $('#submitfrom2').css('backgroundColor','#4994F2');
            $('#electronnextstep2').css('backgroundColor','#4994F2');
            return true;
        }else{
            if($("#productclassonecollege").data("values")!=7 && $("#productclassonecollege").data("values")!=9){
                $('#submitid').attr('data-value',1);
                $('#submitfrom2').css('backgroundColor','#999999');
                $('#electronnextstep2').css('backgroundColor','#999999');
                return false;
            }
        }
        return false;
    }

    /** 打折选择年限和数量start **/
    $("#container").on("change",'.discountproductnum',function (key, value) {
       var k = $(this).data('key');
       $(".discountproductnum"+k).attr('data-values',$(".discountproductnum"+k).val());
        cal4();
    });

    $("#container").on("change",'.discountproductbuyyear',function (key, value) {
        var k = $(this).data('key');
        $(".discountproductbuyyear"+k).attr('data-values',$(".discountproductbuyyear"+k).val());
        cal4();
    });
    /** 打折选择年限和数量end **/


    $("#container").on('click','.giveproductlist',function (key, value) {
        var k = $(this).data('id');
        console.log(k);
        $.confirm("确定删除？", "", function(){
            $("#giveproductlist"+k).remove();
        });
    });

    $("#container").on('click','.discountproductlist',function (key, value) {
        var k = $(this).data('id');
        console.log(k);
        $.confirm("确定删除？", "", function(){
            $("#discountproductlist"+k).remove();
            if($("#buyyear").val()==undefined){
                $(".otherproductlist").empty();
            }
            cal4();
        });
    });



    /**   更多折扣商品页面start   **/
    $("#container").on('click','.discountproductyear_sub',function() {
        key = $(this).data('key');
        var canrenew =  $(".discountproduct"+key).data('canrenew');
        if(!canrenew){
            $.toast('该商品不支持选择多年!','text');
            return false;
        }
        var buyterm = $(".discountproduct"+key).data('activitythresholdbuyterm');
        discountproductyear = $(".discountproductyear"+key).val();
        if(discountproductyear<=buyterm){
            $.toast('不能再减了!','text');
            return false;
        }
        discountproductyear =discountproductyear*1-1;
        $('.discountproductyear'+key).val(discountproductyear);
        $('.discountproductyear'+key).attr('data-values',discountproductyear);
        $(".discountproduct"+key).attr('data-discountproductyear',discountproductyear);
        $(".discountproduct"+key).attr('data-buyyear',discountproductyear);
        cal5();
    });

    $("#container").on('click','.discountproductyear_add',function() {
        key = $(this).data('key');
        var canrenew =  $(".discountproduct"+key).data('canrenew');
        if(!canrenew){
            $.toast('该商品不支持选择多年!','text');
            return false;
        }
        var buyterm = $(".discountproduct"+key).data('activitythresholdbuyterm');
        discountproductyear = $(".discountproductyear"+key).val();
        if(discountproductyear>=10){
            $.toast('不能再增加了!','text');
            return false;
        }
        discountproductyear =1+discountproductyear*1;
        $('.discountproductyear'+key).val(discountproductyear);
        $('.discountproductyear'+key).attr('data-values',discountproductyear);
        $(".discountproductyear"+key).attr('data-discountproductyear',discountproductyear);
        $(".discountproduct"+key).attr('data-buyyear',discountproductyear);
        cal5();
    });


    $("#container").on('click','.discountproductnum_sub',function() {
        key = $(this).data('key');
        var count = $(".discountproduct"+key).data('activitythresholdcount');
        var userlimit = $(".discountproduct"+key).data('userlimit');
        discountproductnum = $(".discountproductnum"+key).val();
        if(discountproductnum<=count){
            $.toast('不能再减了!','text');
            return false;
        }
        discountproductnum =discountproductnum*1-1;
        $('.discountproductnum'+key).val(discountproductnum);
        $('.discountproductnum'+key).attr('data-values',discountproductnum);
        $(".discountproduct"+key).attr('data-discountproductnum',discountproductnum);
        $(".discountproduct"+key).attr('data-buynum',discountproductnum);
        cal5();
    });

    $("#container").on('click','.discountproductnum_add',function() {
        key = $(this).data('key');
        var count = $(".discountproduct"+key).data('activitythresholdcount');
        var userlimit = $(".discountproduct"+key).data('userlimit');
        discountproductnum = $(".discountproductnum"+key).val();
        if(userlimit && discountproductnum>=userlimit){
            $.toast('不能再加了!','text');
            return false;
        }
        discountproductnum =discountproductnum*1+1;
        $('.discountproductnum'+key).val(discountproductnum);
        $('.discountproductnum'+key).attr('data-values',discountproductnum);
        $(".discountproduct"+key).attr('data-discountproductnum',discountproductnum);
        $(".discountproduct"+key).attr('data-buynum',discountproductnum);
        cal5();
    });
    /**   更多折扣商品页面end   **/



    /** 查看更多赠送页面start **/
    $("#container").on('click','.giveproductyear_sub',function() {
        return false;
        key = $(this).data('key');
        var count = $(".giveproduct"+key).data('count');
        var buyterm = $(".giveproduct"+key).data('buyterm');
        giveproductyear = $(".giveproductyear"+key).val();
        if(giveproductyear<=1){
            $.toast('不能再减了!','text');
            return false;
        }
        giveproductyear =giveproductyear*1-1;
        $('.giveproductyear'+key).val(giveproductyear);
        $(".giveproduct"+key).attr('data-giveproductyear',giveproductyear);
    });

    $("#container").on('click','.giveproductyear_add',function() {
        return false;
        key = $(this).data('key');
        var count = $(".giveproduct"+key).data('count');
        var buyterm = $(".giveproduct"+key).data('buyterm');
        var canrenew = $(".giveproduct"+key).data('canrenew');
        giveproductyear = $(".giveproductyear"+key).val();
        if(giveproductyear>=buyterm){
            $.toast('不能再增加了!','text');
            return false;
        }
        if(!canrenew){
            $.toast('不支持续费,不能增加年限!','text');
            return false;
        }
        giveproductyear =1+giveproductyear*1;
        $('.giveproductyear'+key).val(giveproductyear);
        $(".giveproduct"+key).attr('data-giveproductyear',giveproductyear);
    });


    $("#container").on('click','.giveproductnum_sub',function() {
        return false;
       key = $(this).data('key');
       var count = $(".giveproduct"+key).data('count');
       var userlimit = $(".giveproduct"+key).data('userlimit');
       giveproductnum = $(".giveproductnum"+key).val();
       if(giveproductnum<=1){
           $.toast('不能再减了!','text');
           return false;
       }
        giveproductnum =giveproductnum*1-1;
       $('.giveproductnum'+key).val(giveproductnum);
        $(".giveproduct"+key).attr('data-giveproductnum',giveproductnum);
    });

    $("#container").on('click','.giveproductnum_add',function() {
        return false;
        key = $(this).data('key');
        var count = $(".giveproduct"+key).data('count');
        var userlimit = $(".giveproduct"+key).data('userlimit');
        giveproductnum = $(".giveproductnum"+key).val();
        if(giveproductnum>=count ){
        // if(giveproductnum>=userlimit || giveproductnum>=count ){
            $.toast('不能再加了!','text');
            return false;
        }
        giveproductnum =1+giveproductnum*1;
        $('.giveproductnum'+key).val(giveproductnum);
        $(".giveproduct"+key).attr('data-giveproductnum',giveproductnum);
    });
    /** 查看更多赠送页面end **/

    /** 限时折扣选择页面价格计算 **/
    function cal5(){
        $(".discountproduct_label").each(function (k, v) {
            var key = $(v).data('key');
            var buyyear = $(".discountproduct"+key).data('buyyear');
            var buynum = $(".discountproduct"+key).data('buynum');

            var activitymarketprice =  $(".discountproduct"+key).data('activitymarketprice');
            var activityrenewmarketprice =  $(".discountproduct"+key).data('activityrenewmarketprice');

            var activityrenewprice =  $(".discountproduct"+key).data('activityrenewprice');
            var activityprice =  $(".discountproduct"+key).data('activityprice');

            //原价
            var marketprice = $(".discountproduct"+key).data('marketprice');
            var marketrenewprice = $(".discountproduct"+key).data('marketrenewprice');
            var activitymodel = $("#activity_detail").data('activitymodel');
            if(activitymodel==4){
                showactivityprice = activityrenewprice*buyyear*buynum;
                showmarketprice = marketrenewprice*buyyear*buynum;
            }else{
                if(parseInt(buyyear)<=1){
                    showactivityprice= activityprice*buynum;
                    showmarketprice = marketprice*buynum;
                }else{
                    showactivityprice = (activityprice + activityrenewprice*(buyyear-1))*buynum;
                    showmarketprice = (marketprice + marketrenewprice*(buyyear-1))*buynum;
                }
            }
            if(activitymodel!=2){
                $(".discountproductactivity"+k).html('￥'+parseFloat(showactivityprice).toFixed(2)+'(原价:'+parseFloat(showmarketprice).toFixed(2)+')');
            }else{
                $(".discountproductactivity"+k).parent().parent().parent().parent().remove();
            }
        });
    }

    /*计算优惠组合的价格*/
    function cal4() {
        var activityAgent = $("#instanceagents").data('value');
        // activityAgent = 10642;
        var productinfo=[];

        var arr = [];
        $(".product").each(function (key, val) {
            arr[key]=$(val).data('key');
        });
        console.log(productlist);
        var i=0;
        $.each(productlist,function (k,v) {
            if(arr.indexOf(k) != -1) {
                productinfo[i] = {
                    'CategoryID': parseInt($(".product" + k).data('categoryid')),
                    'PackageID': parseInt($(".product" + k).data('packageid')),
                    'ProductID': parseInt($(".product" + k).data('productid')),
                    'SpecificationID': parseInt($(".product" + k).data('specificationid')),
                    'Count': parseInt($(".product" + k).data('buynum')),
                    'BuyTerm': parseInt($(".product" + k).data('buyyear')),
                };
                i++;
            }
        });
        console.log(productinfo);

        var activitytype = $("#activity_detail").data("activitytype");
        var id = $("#activity_detail").data('id');
        var activitymodel = $("#activity_detail").data('activitymodel');
        var tyunusercode=$('#tyunusercode').val();

        $.ajax(
            {
                url: '/index.php?module=TyunWebBuyService&action=CalculationShoppingCart',
                type: 'POST',
                dataType: 'json',
                data:{
                    "agentIdentity":activityAgent,
                    "productinfo":productinfo,
                    "activitytype":activitytype,
                    "activitymodel":activitymodel,
                    "activityid":id,
                    "tyunusercode":tyunusercode
                },
                beforeSend:function(){
                    $.showLoading('请稍等');
                },
                success: function (data) {
                    console.log(data);
                    $.hideLoading();
                    if(data.code=='200' && data.check){
                        caltotalprice(data);
                    }else{
                        zeroClearPrice();
                        // caltotalprice(data);
                        if($(".product") && $(".product").length>0){
                            $.toast(data.message,'text');
                            return;
                        }
                        return;
                    }
                }
            }
        );
    }


    /*计算优惠组合的价格*/
    function cal3() {
        var activityAgent = $("#instanceagents").data('value');
        // activityAgent = 10642;
        var buyproduct = $("#buyproduct").val();
        console.log(buyproduct);
        var buyyear = $("#buyyear").data('values');
        console.log(buyyear);
        var buyproductnum_activity = $("#buyproductnum_activity").data('values');
        console.log(buyproductnum_activity);
        var activitychildid = $("#buyproductgiveproduct_activity").val();
        if(!buyproduct){
            return false;
        }

        if(activitychildid){
            if(!buyproductnum_activity){
                return false;
            }
            var productinfo=[];
            $(".product").each(function (k, v) {
                productinfo[k] = {
                    'CategoryID': parseInt($(v).data('categoryid')),
                    'PackageID':parseInt($(v).data('packageid')),
                    'ProductID':parseInt($(v).data('productid')),
                    'SpecificationID':parseInt($(v).data('specificationid')),
                    'Count':parseInt($(v).data('buynum')),
                    'BuyTerm':parseInt($(v).data('buyyear')),
                };
            });


            console.log(productinfo);
            var activitytype = $("#activity_detail").data("activitytype");
            var id = $("#activity_detail").data('id');
            var ActivityModel = $("#activity_detail").data('activitymodel');
            var tyunusercode=$('#tyunusercode').val();

            $.ajax(
                {
                    url: '/index.php?module=TyunWebBuyService&action=CalculationShoppingCart',
                    type: 'POST',
                    dataType: 'json',
                    data:{
                        "agentIdentity":activityAgent,
                        "productinfo":productinfo,
                        "giftproduct":[],
                        "activitytype":activitytype,
                        "activitymodel":ActivityModel,
                        "activityid":id,
                        "activitychildid":activitychildid,
                        "tyunusercode":tyunusercode
                    },
                    beforeSend:function(){
                        $.showLoading('请稍等');
                    },
                    success: function (data) {
                        console.log(data);
                        $.hideLoading();
                        if(data.code=='200' && data.check){
                            caltotalprice(data);
                        }else{
                            // caltotalprice(data);
                            zeroClearPrice();
                            $.toast(data.message,'text');
                            return;
                        }
                    }
                }
            );
        }else{
            var price = {'data':{'subtotalMarketPrice':0,'money':0,'surplusMoney':0,'subtotal':0}};
            caltotalprice(price);
        }
    }



    /*计算优惠组合的价格*/
    function cal2() {
        var activityAgent = $("#instanceagents").data('value');
        // activityAgent = 10642;
        var buyproduct = $("#buyproduct").val();
        console.log(buyproduct);
        var buyyear = $("#buyyear").data('values');
        console.log(buyyear);
        var buyproductnum_activity = $("#buyproductnum_activity").data('values');
        console.log(buyproductnum_activity);
        if(!buyproduct || !buyyear || !buyproductnum_activity){
            return false;
        }

        var productinfo=[];

        $(".product").each(function (k, v) {
            productinfo[k] = {
                'CategoryID': parseInt($(v).data('categoryid')),
                'PackageID':parseInt($(v).data('packageid')),
                'ProductID':parseInt($(v).data('productid')),
                'SpecificationID':parseInt($(v).data('specificationid')),
                'Count':parseInt($(v).data('buynum')),
                'BuyTerm':parseInt($(v).data('buyyear')),
            };
        });




        var giveproductlistallkey=[];
        $(".giveproductlistall").each(function (k2, v2) {
            giveproductlistallkey.push($(v2).data('key'));
        });
        console.log(giveproductlistallkey);
        var giftproduct = [];
        console.log(giftproductlist);
        $.each(giftproductlist,function (k, v) {
            console.log(k);
            if(giveproductlistallkey==undefined || !giveproductlistallkey.length || giveproductlistallkey.indexOf(k)==-1){
                giftproduct[k]={
                    'CategoryID': parseInt(v.CategoryID),
                    'PackageID':parseInt(v.PackageID),
                    'ProductID':parseInt(v.ProductID),
                    'SpecificationID':parseInt(v.SpecificationID),
                    'Count':parseInt(0),
                    'BuyTerm':parseInt(0),
                    'IsDelete':true
                }
            }else{
                giftproduct[k]={
                    'CategoryID': parseInt($("#giveproductlist"+k).data('categoryid')),
                    'PackageID':parseInt($("#giveproductlist"+k).data('packageid')),
                    'ProductID':parseInt($("#giveproductlist"+k).data('productid')),
                    'SpecificationID':parseInt($("#giveproductlist"+k).data('specificationid')),
                    'Count':parseInt($("#giveproductlist"+k).data('buynum')),
                    'BuyTerm':parseInt($("#giveproductlist"+k).data('buyyear')),
                    'IsDelete':false
                }
            }
        });

        console.log(productinfo);
        console.log(giftproduct);

        var activitytype = $("#activity_detail").data("activitytype");
        var id = $("#activity_detail").data('id');
        var ActivityModel = $("#activity_detail").data('activitymodel');
        var tyunusercode=$('#tyunusercode').val();

        $.ajax(
            {
                url: '/index.php?module=TyunWebBuyService&action=CalculationShoppingCart',
                type: 'POST',
                dataType: 'json',
                data:{
                    "agentIdentity":activityAgent,
                    "productinfo":productinfo,
                    "giftproduct":giftproduct,
                    "activitytype":activitytype,
                    "activitymodel":ActivityModel,
                    "activityid":id,
                    "tyunusercode":tyunusercode
                },
                beforeSend:function(){
                    $.showLoading('请稍等');
                },
                success: function (data) {
                    console.log(data);
                    $.hideLoading();
                    if(data.code=='200' && data.check){
                        caltotalprice(data);
                    }else{
                        // caltotalprice(data);
                        zeroClearPrice();
                        $.toast(data.message,'text');
                        return;
                    }
                }
            }
        );
    }

    /*计算优惠组合的价格*/
    function cal1() {
        var activityAgent = $("#instanceagents").data('value');
        // activityAgent = 10642;
        var productinfo=[];
        $(".product").each(function (k, v) {
            productinfo[k] = {
                'CategoryID': parseInt($(v).data('categoryid')),
                'PackageID':parseInt($(v).data('packageid')),
                'ProductID':parseInt($(v).data('productid')),
                'SpecificationID':parseInt($(v).data('specificationid')),
                'Count':parseInt($(v).data('buynum')),
                'BuyTerm':parseInt($(v).data('buyyear')),
            };
        });

        console.log(productinfo);

        var activitytype = $("#activity_detail").data("activitytype");
        var id = $("#activity_detail").data('id');
        var ActivityModel = $("#activity_detail").data('activitymodel');
        var tyunusercode=$('#tyunusercode').val();

        $.ajax(
            {
                url: '/index.php?module=TyunWebBuyService&action=CalculationShoppingCart',
                type: 'POST',
                dataType: 'json',
                data:{
                    "agentIdentity":activityAgent,
                    "productinfo":productinfo,
                    "activitytype":activitytype,
                    "activitymodel":ActivityModel,
                    "activityid":id,
                    "tyunusercode":tyunusercode
                },
                beforeSend:function(){
                    $.showLoading('请稍等');
                },
                success: function (data) {
                    console.log(data);
                    $.hideLoading();
                    if(data.code=='200' && data.check){
                        caltotalprice(data);
                    }else{
                        zeroClearPrice();
                        // caltotalprice(data);
                        $.toast(data.message,'text');
                        return;
                    }
                }
            }
        );
    }
    
    function zeroClearPrice() {
        var activitymodel = $("#activity_detail").data('activitymodel');
        if(activitymodel==2){
            $('#unusedamount').val(0);
            $('#unusedamountshow').val('￥'+parseFloat(0).toFixed(2));
            $('#unusedamountshow1').text(0);

            var surplusMoney = 0;
            // $('#upgradecost').val('0');
            $('#upgradecostshow').val('￥'+0);
            $('#upgradecostshow1').text(0);
            $('#Price').val(0);
            $('#Priceshow').val('￥'+parseFloat(0));
        }
        var submarketprice = parseFloat(0).toFixed(2);
        if($(".product").length>0){
            $("input[name=marketactivityprice]").val('￥'+submarketprice);
        }else{
            submarketprice = 0;
            $("input[name=marketactivityprice]").val('￥'+submarketprice);
        }

        $("input[name=marketactivitypriceval]").val(submarketprice);

        var sumprice = 0;
        //获取另购产品价格
        $.each($('.select3check'),function(key,value){
            var temprice=parseInt($(value).data('number'))*parseFloat($(value).data('money'));
            sumprice=sumprice*1+temprice;
        });

        console.log(sumprice);
        console.log(submarketprice);
        sumallprice = sumprice*1+submarketprice*1;
        sumallprice = (!sumallprice||sumallprice<0)?0:sumallprice;
        console.log(sumallprice);
        $('#totalmarketprice').val(sumallprice);
        $("#totalmarketpriceshow").val('￥'+parseFloat(sumallprice).toFixed(2));
    }

    function caltotalprice(data) {
        console.log(data);
        var activitymodel = $("#activity_detail").data('activitymodel');
        if(activitymodel==2){
            $('#unusedamount').val(data.data.surplusMoney);
            $('#unusedamountshow').val('￥'+parseFloat(data.data.surplusMoney).toFixed(2));
            $('#unusedamountshow1').text(data.data.surplusMoney);

            var surplusMoney = data.data.surplusMoney;
            // $('#upgradecost').val(data.data.money.toFixed(2));
            $('#upgradecostshow').val('￥'+(data.data.money<0?0:data.data.money.toFixed(2)));
            $('#upgradecostshow1').text((data.data.money<0?0:data.data.money.toFixed(2)));
            $('#Price').val(data.data.money*1+surplusMoney*1);
            $('#Priceshow').val('￥'+parseFloat(data.data.money+surplusMoney).toFixed(2));
        }
        var submarketprice = parseFloat(data.data.subtotal).toFixed(2);
        console.log(submarketprice);
        if($(".product").length>0){
            $("input[name=marketactivityprice]").val('￥'+submarketprice);
        }else{
            submarketprice = 0;
            $("input[name=marketactivityprice]").val('￥'+submarketprice);
        }

        $("input[name=marketactivitypriceval]").val(submarketprice);
        console.log(submarketprice);
        var sumprice = 0;
        //获取另购产品价格
        $.each($('.select3check'),function(key,value){
            var temprice=parseInt($(value).data('number'))*parseFloat($(value).data('money'));
            sumprice=sumprice*1+temprice*1;
        });
        console.log(sumprice);
        sumallprice = sumprice*1+submarketprice*1;
        sumallprice = (!sumallprice||sumallprice<0)?0:sumallprice;
        console.log(sumallprice);
        $('#totalmarketprice').val(sumallprice);
        $("#totalmarketpriceshow").val('￥'+parseFloat(sumallprice).toFixed(2));
        if(activitymodel==2) {
            $("input[name=marketactivityprice]").val('￥' + parseFloat(data.data.money*1+surplusMoney*1).toFixed(2));
        }
    }


    $("#container").on('change',"#goalproduct",function () {
        var activityAgent = $("#instanceagents").data('value');
        var activityModel = $("#classtype").val();

        if(activityModel=='buy'){
            // activityAgent = 10642;
            // 活动类型 1优惠组合 2赠送产品 3赠送时间 4限时折扣
            $.ajax(
                {
                    url: '/index.php?module=TyunWebBuyService&action=getALLNowActivity',
                    type: 'POST',
                    dataType: 'json',
                    data:{
                        "activityAgent":activityAgent,
                        "activityModel":1,
                        "activityRange":2
                    },
                    beforeSend:function(){
                        $.showLoading('请稍等');
                    },
                    success: function (data) {
                        console.log(data);
                        $.hideLoading();
                        if(data && data.success && data.data.length>0){
                            $("#canviewactivity").attr('data-value',1);
                        }else {
                            $("#canviewactivity").attr('data-value',0);
                        }
                    }
                }
            );
            return;
        }

        var categoryID= $("#classtyperenew").val();
        var packageID = $("#oldproductnameid").val();
        if(activityModel=='renew'){
            params = 4;
        }else{
            params = 2;
            packageID = $("#goalproduct").val();
        }
        // activityAgent = 10642;

        // 活动类型 1优惠组合 2赠送产品 3赠送时间 4限时折扣
        $.ajax(
            {
                url: '/index.php?module=TyunWebBuyService&action=getActivity',
                type: 'POST',
                dataType: 'json',
                data:{
                    "categoryID":categoryID,
                    "packageID":packageID,
                    "activityModel":params,
                    "isCombination":false,
                    "activityAgent":activityAgent
                },
                beforeSend:function(){
                    $.showLoading('请稍等');
                },
                success: function (data) {
                    console.log(data);
                    $.hideLoading();
                    if(data && data.success && data.data.length>0){
                        $("#canviewactivity").attr('data-value',1);
                    }else {
                        $("#canviewactivity").attr('data-value',0);
                    }
                }
            }
        );
    });


    $("#container").on('change','#producttype',function () {
        var producttype = $(this).val();
        var authenticationtype = $("#authenticationtype").val();
        // var authenticationtypechecked =  $("#tyunusercode").find('option:checked').data("authenticationtype");
        // if(authenticationtypechecked!=undefined && authenticationtypechecked!=-1){
        //     $("#authenticationtype option").eq(authenticationtypechecked).prop("selected",true);
        //     $("#authenticationtype").attr('disabled',true);
        // }else {
        //     $("#authenticationtype").removeAttr('disabled');
        // }

        if(producttype=='activity'){
        //     if(authenticationtype==undefined){
        //         str ='                  <div class="weui-cell weui-cell_select weui-cell_select-after weui-cell_select_custom_spacing">\n' +
        //             '                        <div class="weui-cell__hd">\n' +
        //             '                            <label class="weui-label" style="text-align:right;">认证类型：</label>\n' +
        //             '                        </div>\n' +
        //             '                        <div class="weui-cell__bd">\n' +
        //             '                            <select class="weui-select" name="authenticationtype" id="authenticationtype" '+((authenticationtypechecked>=0?'disabled':''))+'>\n' +
        //             '                                <option value="1" '+((authenticationtypechecked==1 || authenticationtypechecked==-1)?'selected':'')+'>企业</option>\n' +
        //             '                                <option value="0" '+((authenticationtypechecked==0)?'selected':'')+'>个人</option>\n' +
        //             '                                <option value="2" '+((authenticationtypechecked==2)?'selected':'')+'>政府</option>\n' +
        //             '                                <option value="3" '+((authenticationtypechecked==3)?'selected':'')+'>其他组织</option>\n' +
        //             '                            </select>\n' +
        //             '                        </div>\n' +
        //             '                    </div>';
        //         $("#producttype").parent().parent().after(str);
        //     }

            $("#goalproduct").parent().parent().attr('style','display:""');

            var activityAgent = $("#instanceagents").data('value');
            var activityModel = $("#classtype").val();

            if(activityModel=='buy'){
                // activityAgent = 10642;
                // 活动类型 1优惠组合 2赠送产品 3赠送时间 4限时折扣
                $.ajax(
                    {
                        url: '/index.php?module=TyunWebBuyService&action=getALLNowActivity',
                        type: 'POST',
                        dataType: 'json',
                        data:{
                            "activityAgent":activityAgent,
                            "activityModel":1,
                            "activityRange":2
                        },
                        beforeSend:function(){
                            $.showLoading('请稍等');
                        },
                        success: function (data) {
                            console.log(data);
                            $.hideLoading();
                            if(data && data.success && data.data.length>0){
                                $("#canviewactivity").attr('data-value',1);
                            }
                        }
                    }
                );
                return;
            }

            var categoryID= $("#classtyperenew").val();
            var packageID = $("#oldproductnameid").val();
            if(activityModel=='renew'){
                params = 4;
            }else{
                params = 2;
                packageID = $("#goalproduct").val();
            }
            // activityAgent = 10642;

            // 活动类型 1优惠组合 2赠送产品 3赠送时间 4限时折扣
            $.ajax(
                {
                    url: '/index.php?module=TyunWebBuyService&action=getActivity',
                    type: 'POST',
                    dataType: 'json',
                    data:{
                        "categoryID":categoryID,
                        "packageID":packageID,
                        "activityModel":params,
                        "isCombination":false,
                        "activityAgent":activityAgent
                    },
                    beforeSend:function(){
                        $.showLoading('请稍等');
                    },
                    success: function (data) {
                        console.log(data);
                        $.hideLoading();
                        if(data && data.success && data.data.length>0){
                            $("#canviewactivity").attr('data-value',1);
                        }
                    }
                }
            );
        }else{
            if(authenticationtype!=undefined){
                // $("#authenticationtype").parent().parent().remove();
                $("#goalproduct").parent().parent().attr('style','display:none');
            }
        }

    });

    $('#container').on('input','#servicetotal',function(){
        checkProductValue2(1);
    });
    $('#container').on('change','#servicetotal',function(){
        checkProductValue2(1);
    });

    /** 提交订单 **/
    $("#container").on('click','#submitfrom2',function (event) {
        if(!$("#servicetotal").val()){
            $.toast('请填写合同金额','text');
            return false;
        }
        var submitid=$('#submitid').attr('data-value');
        if(submitid!=2){
            return false;
        }
        if(!parseFloat($("input[name=marketactivitypriceval]").val())){
            return false;
        }

        var servicecontractsid=$('input[name="servicecontractsid"]').val();
        var servicecontractsid_display=$('input[name="servicecontractsid_display"]').val();
        var accountid=$('input[name="accountid"]').val();
        var oldaccountid=$('input[name="oldaccountid"]').val();
        var accountid_display=$('input[name="accountid_display"]').val();
        var oldaccountid_display=$('input[name="oldaccountid_display"]').val();
        var mobile=$('#mobile').val();
        var mobilevcode=$('#mobilevcode').val();
        var classtype=$('#classtype').val();
        var buyyear=$('#buyyear').data('values');
        var oldproductnameid=$('#oldproductnameid').val();
        var servicetotal=parseFloat($('#servicetotal').val()).toFixed(2);
        var classtyperenew=$('#classtyperenew').val();
        var tyunusercode=$('#tyunusercode option:checked').text();
        var tyunusercodeid=$('#tyunusercode').val();
        var buyproduct=$('#buyproduct').attr('data-values');
        var tyunusercodetext=$('#tyunusercode').find("option:checked").text();
        var buydate=$('#buydate').val();
        var currentdata=(new Date()).getFullYear()+'-'+((new Date()).getMonth()+1)+'-'+(new Date()).getDate();
        var upgardecycle=$('#upgardecycle').val();
        var oldproductname_display=$('#oldproductname_display').val();
        var buyproductname=$('#buyproduct').val();
        var separateproductname_display= '';
        var activacode = $("#activacode").data('value');
        var oproductid = $("#oproductid").data('value');

        var give_title_display = $(".activity_check").filter('input:checked').data('title');

        var activitytype = $("#activity_detail").data("activitytype");
        var activityid = $("#activity_detail").data('id');
        var activityno = $("#activity_detail").data('activityid');
        var activitymodel = $("#activity_detail").data('activitymodel');
        var activityagent = $("#activity_detail").data('activityagent');
        var activitychildid = $("#buyproductgiveproduct_activity").val();
        var activitytitle = $("#activity_detail").data('activitytitle');
        var combinationprice = $("#activity_detail").data('combinationprice');
        var meetActivity = $("#activity_detail").data('meetactivity');

        var authenticationtype = $("#authenticationtype").val();

        //另购产品的
        var otherproduct = [];
        $.each($('.select3check'),function(k,v){
            otherproduct[k] ={
                'categoryID': parseInt($(v).data('categoryid')),
                'packageID':parseInt(0),
                'productID':parseInt($(v).data('productid')),
                'specificationID':parseInt($(v).data('id')),
                'count':parseInt($(v).data('number')),
                'buyTerm':parseInt($("#buyyear").data('values')),
                'isDelete':false,
                "price":parseFloat($(v).data('price')),
                "renewPrice":parseFloat($(v).data('renewprice')),
                "marketPrice":parseFloat($(v).data('marketprice')),
                "marketRenewPrice":parseFloat($(v).data('marketrenewprice')),
                "activityThresholdBuyTerm":0,
                "activityThresholdCount":0,
                "activityMarketPrice":0,
                "activityPrice":0,
                "activityRenewMarketPrice":0,
                "activityRenewPrice":0,
                "packageTitle":'',
                "productTitle":'',
                "specificationNumber":'',
                "specificationTitle":'',
                "unit":'',
                "userProductID":0
            };
            separateproductname_display += $(v).data('producttitle')+'('+$(v).data('number')+') ';
        });



        if(activitytype==1){
            type_text='优惠组合';
        }else if(activitytype==2){
            type_text='赠送产品';
        }if(activitytype==3){
            type_text='赠送时间';
        }else if(activitytype==4){
            type_text='限时折扣';
        }

        //新购
        if(activitymodel==1){
            if(activitytype==1 || activitytype==4){
                var productname = $(".product").data('productname');
                var producttype = $(".product").data('producttype');
                var buyyear = $(".product").data('buyyear');
                var buynum = $(".product").data('buynum');

                //另购服务end
                var str='<div class="weui-form-preview" style="overflow: auto;height: 450px;">' +
                    '<div class="weui-form-preview__bd">' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">T云账号</label>' +
                    '<span class="weui-form-preview__value">'+tyunusercode+'</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">选择活动</label>' +
                    '<span class="weui-form-preview__value">'+give_title_display+'</span>' +
                    '</div>';

                $(".product").each(function (k, v) {
                    str +=   '<div class="weui-form-preview__item">' +
                        '<label class="weui-form-preview__label">产品名称</label>' +
                        '<span class="weui-form-preview__value">'+$(v).data('productname')+'</span>' +
                        '</div>' +
                        '<div class="weui-form-preview__item">' +
                        '<label class="weui-form-preview__label">规格名称</label>' +
                        '<span class="weui-form-preview__value">'+$(v).data('producttype')+'</span>' +
                        '</div>' +
                        '<div class="weui-form-preview__item">' +
                        '<label class="weui-form-preview__label">购买年限</label>' +
                        '<span class="weui-form-preview__value">'+$(v).data('buyyear')+'年</span>' +
                        '</div>' +
                        '<div class="weui-form-preview__item">' +
                        '<label class="weui-form-preview__label">购买数量</label>' +
                        '<span class="weui-form-preview__value">'+$(v).data('buynum')+'</span>' +
                        '</div>';
                });


                str += '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">活动类型</label>' +
                    '<span class="weui-form-preview__value">'+type_text+'</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">另购单品</label>' +
                    '<span class="weui-form-preview__value">'+(separateproductname_display?separateproductname_display:'无')+'</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">合同金额</label>' +
                    '<span class="weui-form-preview__value">￥'+servicetotal+'</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">合同</label>' +
                    '<span class="weui-form-preview__value">'+servicecontractsid_display+'</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">客户名称</label>' +
                    '<span class="weui-form-preview__value">'+accountid_display+'</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">老客户名称</label>' +
                    '<span class="weui-form-preview__value">'+(oldaccountid_display?oldaccountid_display:'无')+'</span>' +
                    '</div>' +
                    '</div>' +
                    '</div>';
            }else if(activitytype==3){
                var productname = $(".product").data('productname');
                var producttype = $(".product").data('producttype');
                var buyproductgiveproduct_activity = $("#buyproductgiveproduct_activity").val();
                var buyproductgiveproductyear = buyproductgiveproduct_activity.split('-');
                var buyyear = $(".product").data('buyyear');
                var buynum = $(".product").data('buynum');

                //另购服务end
                var str='<div class="weui-form-preview" style="overflow: auto;height: 450px;">' +
                    '<div class="weui-form-preview__bd">' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">T云账号</label>' +
                    '<span class="weui-form-preview__value">'+tyunusercode+'</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">选择活动</label>' +
                    '<span class="weui-form-preview__value">'+give_title_display+'</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">产品名称</label>' +
                    '<span class="weui-form-preview__value">'+productname+'</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">规格名称</label>' +
                    '<span class="weui-form-preview__value">'+producttype+'</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">购买年限</label>' +
                    '<span class="weui-form-preview__value">'+buyyear+'年</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">购买数量</label>' +
                    '<span class="weui-form-preview__value">'+buynum+'</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">活动类型</label>' +
                    '<span class="weui-form-preview__value">'+type_text+'</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">赠送年限</label>' +
                    '<span class="weui-form-preview__value">'+buyproductgiveproductyear[1]+'年</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">另购单品</label>' +
                    '<span class="weui-form-preview__value">'+(separateproductname_display?separateproductname_display:'无')+'</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">合同金额</label>' +
                    '<span class="weui-form-preview__value">￥'+servicetotal+'</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">合同</label>' +
                    '<span class="weui-form-preview__value">'+servicecontractsid_display+'</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">客户名称</label>' +
                    '<span class="weui-form-preview__value">'+accountid_display+'</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">老客户名称</label>' +
                    '<span class="weui-form-preview__value">'+(oldaccountid_display?oldaccountid_display:'无')+'</span>' +
                    '</div>' +
                    '</div>' +
                    '</div>';
            }else if(activitytype==2){
                var productname = $(".product").data('productname');
                var producttype = $(".product").data('producttype');
                var buyyear = $(".product").data('buyyear');
                var buynum = $(".product").data('buynum');

                //另购服务end
                var str='<div class="weui-form-preview" style="overflow: auto;height: 450px;">' +
                    '<div class="weui-form-preview__bd">' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">T云账号</label>' +
                    '<span class="weui-form-preview__value">'+tyunusercode+'</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">选择活动</label>' +
                    '<span class="weui-form-preview__value">'+give_title_display+'</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">产品名称</label>' +
                    '<span class="weui-form-preview__value">'+productname+'</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">规格名称</label>' +
                    '<span class="weui-form-preview__value">'+producttype+'</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">购买年限</label>' +
                    '<span class="weui-form-preview__value">'+buyyear+'年</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">购买数量</label>' +
                    '<span class="weui-form-preview__value">'+buynum+'</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">活动类型</label>' +
                    '<span class="weui-form-preview__value">'+type_text+'</span>' +
                    '</div>';
                $(".giveproductlistall").each(function (k, v) {
                    str+=    '<div class="weui-form-preview__item">' +
                        '<label class="weui-form-preview__label">赠送产品</label>' +
                        '<span class="weui-form-preview__value">'+$(v).data('giveproductname')+'</span>' +
                        '</div>'+
                        '<div class="weui-form-preview__item">' +
                        '<label class="weui-form-preview__label">赠送年限</label>' +
                        '<span class="weui-form-preview__value">'+$(v).data('buyyear')+'年</span>' +
                        '</div>'+
                        '<div class="weui-form-preview__item">' +
                        '<label class="weui-form-preview__label">产品数量</label>' +
                        '<span class="weui-form-preview__value">'+$(v).data('buynum')+'</span>' +
                        '</div>';
                });


                str +=    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">另购单品</label>' +
                    '<span class="weui-form-preview__value">'+(separateproductname_display?separateproductname_display:'无')+'</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">合同金额</label>' +
                    '<span class="weui-form-preview__value">￥'+servicetotal+'</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">合同</label>' +
                    '<span class="weui-form-preview__value">'+servicecontractsid_display+'</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">客户名称</label>' +
                    '<span class="weui-form-preview__value">'+accountid_display+'</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">老客户名称</label>' +
                    '<span class="weui-form-preview__value">'+(oldaccountid_display?oldaccountid_display:'无')+'</span>' +
                    '</div>' +
                    '</div>' +
                    '</div>';
            }
        }else if(activitymodel==2 || activitymodel==4){
            //升级 续费
            if(activitymodel==4){
                modelname='续费';
            }else{
                modelname='升级';
            }
            if(activitytype==3){
                var productname = $(".product").data('productname');
                var producttype = $(".product").data('producttype');
                var buyproductgiveproduct_activity = $("#buyproductgiveproduct_activity").val();
                var buyproductgiveproductyear = buyproductgiveproduct_activity.split('-');
                var buyyear = $(".product").data('buyyear');
                var buynum = $(".product").data('buynum');

                //另购服务end
                    var str ='<div class="weui-form-preview" style="overflow: auto;height: 450px;">' +
                    '<div class="weui-form-preview__bd">' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">T云账号</label>' +
                    '<span class="weui-form-preview__value">'+tyunusercode+'</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">原版本</label>' +
                    '<span class="weui-form-preview__value">'+oldproductname_display+'</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">选择活动</label>' +
                    '<span class="weui-form-preview__value">'+give_title_display+'</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">产品名称</label>' +
                    '<span class="weui-form-preview__value">'+productname+'</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">规格名称</label>' +
                    '<span class="weui-form-preview__value">'+producttype+'</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">'+modelname+'年限</label>' +
                    '<span class="weui-form-preview__value">'+buyyear+'年</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">'+modelname+'数量</label>' +
                    '<span class="weui-form-preview__value">'+buynum+'</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">活动类型</label>' +
                    '<span class="weui-form-preview__value">'+type_text+'</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">赠送年限</label>' +
                    '<span class="weui-form-preview__value">'+buyproductgiveproductyear[1]+'年</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">另购单品</label>' +
                    '<span class="weui-form-preview__value">'+(separateproductname_display?separateproductname_display:'无')+'</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">合同金额</label>' +
                    '<span class="weui-form-preview__value">￥'+servicetotal+'</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">合同</label>' +
                    '<span class="weui-form-preview__value">'+servicecontractsid_display+'</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">客户名称</label>' +
                    '<span class="weui-form-preview__value">'+accountid_display+'</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">老客户名称</label>' +
                    '<span class="weui-form-preview__value">'+(oldaccountid_display?oldaccountid_display:'无')+'</span>' +
                    '</div>' +
                    '</div>' +
                    '</div>';
            }else if(activitytype==4){
                var productname = $(".product").data('productname');
                var producttype = $(".product").data('producttype');
                var buyyear = $(".product").data('buyyear');
                var buynum = $(".product").data('buynum');

                //另购服务end
                var str='<div class="weui-form-preview" style="overflow: auto;height: 450px;">' +
                    '<div class="weui-form-preview__bd">' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">T云账号</label>' +
                    '<span class="weui-form-preview__value">'+tyunusercode+'</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">原版本</label>' +
                    '<span class="weui-form-preview__value">'+oldproductname_display+'</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">选择活动</label>' +
                    '<span class="weui-form-preview__value">'+give_title_display+'</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">产品名称</label>' +
                    '<span class="weui-form-preview__value">'+productname+'</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">规格名称</label>' +
                    '<span class="weui-form-preview__value">'+producttype+'</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">'+modelname+'年限</label>' +
                    '<span class="weui-form-preview__value">'+buyyear+'年</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">'+modelname+'数量</label>' +
                    '<span class="weui-form-preview__value">'+buynum+'</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">活动类型</label>' +
                    '<span class="weui-form-preview__value">'+type_text+'</span>' +
                    '</div>';
                $(".giveproductlistall").each(function (k, v) {
                    str+=    '<div class="weui-form-preview__item">' +
                        '<label class="weui-form-preview__label">赠送产品</label>' +
                        '<span class="weui-form-preview__value">'+$(v).data('giveproductname')+'</span>' +
                        '</div>'+
                        '<div class="weui-form-preview__item">' +
                        '<label class="weui-form-preview__label">赠送年限</label>' +
                        '<span class="weui-form-preview__value">'+$(v).data('buyyear')+'年</span>' +
                        '</div>'+
                        '<div class="weui-form-preview__item">' +
                        '<label class="weui-form-preview__label">产品数量</label>' +
                        '<span class="weui-form-preview__value">'+$(v).data('buynum')+'</span>' +
                        '</div>';
                });


                str +=    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">另购单品</label>' +
                    '<span class="weui-form-preview__value">'+(separateproductname_display?separateproductname_display:'无')+'</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">合同金额</label>' +
                    '<span class="weui-form-preview__value">￥'+servicetotal+'</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">合同</label>' +
                    '<span class="weui-form-preview__value">'+servicecontractsid_display+'</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">客户名称</label>' +
                    '<span class="weui-form-preview__value">'+accountid_display+'</span>' +
                    '</div>' +
                    '<div class="weui-form-preview__item">' +
                    '<label class="weui-form-preview__label">老客户名称</label>' +
                    '<span class="weui-form-preview__value">'+(oldaccountid_display?oldaccountid_display:'无')+'</span>' +
                    '</div>' +
                    '</div>' +
                    '</div>';
            }
        }

        var activationcodeid = $("#activationcodeid").val();

        /** 获取要购买的产品start **/
        var arr = [];
        $(".product").each(function (k, v) {
            arr[k]=$(v).data('key');
        });

        var productinfo=[];
        console.log(productlist);
        var i=0;
        $(productlist).each(function (k, v) {
            var isdelete = false;
            if(activitytype==2){
                var categoryid2 = $("#productclassone").data('values');
                var packageid2 = $("#productclasstwo").data('values');
                if(categoryid2!=v.CategoryID || packageid2 != v.PackageID){
                    isdelete = true;
                    buynum=0;
                    buyyear=0;
                }
                buynum = $('.product'+k).data('buynum');
                buyyear = $('.product'+k).data('buyyear');
            }else if(activitytype==3){
                var buyproductkey = $("#buyproduct").data('values');
                if(buyproductkey!=k){
                    return true;
                }
                buynum = $('.product0').data('buynum');
                buyyear = $('.product0').data('buyyear');
            }else{
                if(arr.indexOf(k)==-1){
                    isdelete = true;
                    buynum=0;
                    buyyear=0;
                }
                buynum = $('.product'+k).data('buynum');
                buyyear = $('.product'+k).data('buyyear');
            }

            var flag = false;
            if((buynum >=v.ActivityThresholdCount) && (buyyear>=v.ActivityThresholdBuyTerm)){
                flag =true;
            }

            if(!isdelete){
                productinfo[i] = {
                    'categoryID': parseInt(v.CategoryID),
                    'packageID':parseInt(v.PackageID),
                    'productID':parseInt(v.ProductID),
                    'specificationID':parseInt(v.SpecificationID),
                    'count':parseInt(((buynum==undefined)?0:buynum)),
                    'buyTerm':parseInt(((buyyear==undefined)?0:buyyear)),
                    "price":(flag?parseFloat(v.ActivityPrice):parseFloat(v.OriginalPrice.price)),
                    "renewPrice":(flag?parseFloat(v.ActivityRenewPrice):parseFloat(v.OriginalPrice.renewPrice)),
                    "marketPrice":(flag?parseFloat(v.ActivityMarketPrice):parseFloat(v.OriginalPrice.marketPrice)),
                    "marketRenewPrice":(flag?parseFloat(v.ActivityRenewMarketPrice):parseFloat(v.OriginalPrice.marketRenewPrice)),
                    "activityThresholdBuyTerm":v.ActivityThresholdBuyTerm,
                    "activityThresholdCount":v.ActivityThresholdCount,
                    "activityMarketPrice":v.ActivityMarketPrice,
                    "activityPrice":v.ActivityPrice,
                    "activityRenewMarketPrice":v.ActivityRenewMarketPrice,
                    "activityRenewPrice":v.ActivityRenewPrice,
                    "packageTitle":v.PackageTitle,
                    "productTitle":v.ProductTitle,
                    "specificationNumber":v.SpecificationCount,
                    "specificationTitle":v.SpecificationTitle,
                    "unit":v.SpecificationUnit,
                    "userProductID":0
                };
                i++;
            }

        });

        var giveproductlistallkey = [];
        $(".giveproductlistall").each(function (k2, v2) {
            giveproductlistallkey.push($(v2).data('key'));
        });

        console.log(giveproductlistallkey);
        var giftproduct = [];
        console.log(giftproductlist);
        $.each(giftproductlist,function (k, v) {
            console.log(k);
            if(giveproductlistallkey==undefined || !giveproductlistallkey.length ||giveproductlistallkey.indexOf(k)==-1){
                giftproduct[k]={
                    'categoryID': parseInt(v.CategoryID),
                    'packageID':parseInt(v.PackageID),
                    'productID':parseInt(v.ProductID),
                    'specificationID':parseInt(v.SpecificationID),
                    'count':parseInt(0),
                    'buyTerm':parseInt(0),
                    'isDelete':true,
                    "price":parseFloat(v.OriginalPrice.price),
                    "renewPrice":parseFloat(v.OriginalPrice.renewPrice),
                    "marketPrice":parseFloat(v.OriginalPrice.marketPrice),
                    "marketRenewPrice":parseFloat(v.OriginalPrice.marketRenewPrice),
                    "activityThresholdBuyTerm":v.ActivityThresholdBuyTerm,
                    "activityThresholdCount":v.ActivityThresholdCount,
                    "activityMarketPrice":v.ActivityMarketPrice,
                    "activityPrice":v.ActivityPrice,
                    "activityRenewMarketPrice":v.ActivityRenewMarketPrice,
                    "activityRenewPrice":v.ActivityRenewPrice,
                    "packageTitle":v.PackageTitle,
                    "productTitle":v.ProductTitle,
                    "specificationNumber":v.SpecificationCount,
                    "specificationTitle":v.SpecificationTitle,
                    "unit":v.SpecificationUnit,
                    // "userProductID":0
                }
            }else{
                giftproduct[k]={
                    'categoryID': parseInt($("#giveproductlist"+k).data('categoryid')),
                    'packageID':parseInt($("#giveproductlist"+k).data('packageid')),
                    'productID':parseInt($("#giveproductlist"+k).data('productid')),
                    'specificationID':parseInt($("#giveproductlist"+k).data('specificationid')),
                    'count':parseInt($("#giveproductlist"+k).data('buynum')),
                    'buyTerm':parseInt($("#giveproductlist"+k).data('buyyear')),
                    'isDelete':false,
                    "price":parseFloat(v.OriginalPrice.price),
                    "renewPrice":parseFloat(v.OriginalPrice.renewPrice),
                    "marketPrice":parseFloat(v.OriginalPrice.marketPrice),
                    "marketRenewPrice":parseFloat(v.OriginalPrice.marketRenewPrice),
                    "activityThresholdBuyTerm":v.ActivityThresholdBuyTerm,
                    "activityThresholdCount":v.ActivityThresholdCount,
                    "activityMarketPrice":v.ActivityMarketPrice,
                    "activityPrice":v.ActivityPrice,
                    "activityRenewMarketPrice":v.ActivityRenewMarketPrice,
                    "activityRenewPrice":v.ActivityRenewPrice,
                    "packageTitle":v.PackageTitle,
                    "productTitle":v.ProductTitle,
                    "specificationNumber":v.SpecificationCount,
                    "specificationTitle":v.SpecificationTitle,
                    "unit":v.SpecificationUnit,
                    // "userProductID":0
                }
            }
        });

        //域名续费权益
        var chooseuserproduct = [];
        var packSpecificationList2 = $(".packSpecificationList2").filter('input:checked');
        $.each(packSpecificationList2,function (k2, v2) {
            chooseuserproduct.push(parseInt($(v2).data('productspecificationsid')));
        });

        console.log(productinfo);
        console.log(giftproduct);
        console.log(giftproduct);
        var tyunusercode=$('#tyunusercode').val();
        var params={
            "servicecontractsid":servicecontractsid,
            "servicecontractsid_display":servicecontractsid_display,
            "accountid":accountid,
            "accountid_display":accountid_display,
            "mobile":mobile,
            "mobilevcode":mobilevcode,
            "classtype":classtype,
            "categoryid":classtyperenew,
            "type":1,
            "buyyear":buyyear,
            "buydate":buydate,
            'packageid':oldproductnameid,
            "tyunusercode":tyunusercode,
            "servicetotal":servicetotal,
            "tyunusercodeid":tyunusercodeid,
            "buyproduct":buyproduct,
            "tyunusercodetext":tyunusercodetext,
            "oldcustomerid":oldaccountid,
            "oldcustomername":oldaccountid_display,
            "oldproductid":oproductid,
            "oldproductname":oldproductname_display,
            "activacode":activacode,

            //新增下单参数
            "activitychildid":activitychildid,
            "giftproduct":giftproduct,
            "productinfo":productinfo,
            "agentIdentity":activityagent,
            "activitytype":activitytype,
            "activitymodel":activitymodel,
            "activityid":activityid,
            //另购
            "otherproduct":otherproduct,
            //另购产品
            "separateproductname_display":separateproductname_display,
            "activitytitle":activitytitle,
            "combinationprice":combinationprice,
            "meetactivity":true,
            "activitytypetext":type_text,
            "activityno":activityno,
            "authenticationtype":authenticationtype,
            "chooseuserproduct":chooseuserproduct
        };

        console.log(params);
        $.confirm(str, "请核对订单信息", function() {
            $.ajax({
                    url: '/index.php?module=TyunWebBuyService&action=AddOrder2',
                    type: 'POST',
                    dataType: 'json',
                    data:params,
                    beforeSend:function(){
                        $.showLoading('订单处理中');
                    },
                    success: function (data) {
                        $.hideLoading();
                        if(data.success==1){
                            $.alert("下单成功", function() {
                                location.href=data.url;
                                // location.href="/index.php?module=TyunWebBuyService&action=index";
                            });
                        }else{
                            $.toast(data.msg,'text');
                        }
                    }
                }
            );
        }, function() {
            //取消操作
        });
    });


    //签署类型切换
    $('#container').on("change","#signaturetype",function(event){
        var signaturetype = $(this).val();
        if(signaturetype=='papercontract'){
            str1="<div class=\"weui-cell weui-cell_vcode weui-cell_select_custom_spacing\">\n" +
                "                        <div class=\"weui-cell__hd\">\n" +
                "                            <label class=\"weui-label\" style=\"text-align:right;\">服务合同：</label>\n" +
                "                        </div>\n" +
                "                        <div class=\"weui-cell__bd\" style=\"position: relative;\">\n" +
                "                            <input type=\"hidden\" id=\"contractowenid\" value=\"\">\n" +
                "                            <input type=\"hidden\" id=\"contractowenname\" value=\"\">\n" +
                "                            <input type=\"hidden\" id=\"servicecontractsid\" name=\"servicecontractsid\" value=\"0\" data-msg=\"服务合同\">\n" +
                "                            <input class=\"weui-input\" name=\"servicecontractsid_display\" type=\"text\" placeholder=\"请输入服务合同后四位后点击搜索按钮\" data-name=\"servicecontractsid\" data-id=\"searchbar\">\n" +
                "                            <div class=\"weui-panel__bd\" id=\"servicecontractsid_downlist\" style=\"position: absolute;transform-origin: 0px 0px 0px; opacity: 1; transform: scale(1, 1);width:100%;z-index:3; \">\n" +
                "                                <div class=\"weui-media-box weui-media-box_small-appmsg\">\n" +
                "                                    <div class=\"weui-cells\" id=\"servicecontractsid_downcontent\" style=\"overflow-y:auto;max-height:20em;font-size:0.9em;\">\n" +
                "\n" +
                "                                    </div>\n" +
                "                                </div>\n" +
                "                            </div>\n" +
                "                        </div>\n" +
                "\n" +
                "                        <div class=\"weui-cell__ft\">\n" +
                "                            <button class=\"weui-vcode-btn search_list\" style=\"border-left:none;\" data-name=\"servicecontractsid\" data-id=\"searchbar\"><i class=\"weui-icon-search\" style=\"color:#0B92D9;\"></i></button>\n" +
                "                        </div>\n" +
                "                    </div>";
            $("#accountid").parent().parent().before(str1);

            str2 = "<div class=\"weui-cell weui-cell_select_custom_spacing\">\n" +
                "                        <div class=\"weui-cell__hd\">\n" +
                "                            <label class=\"weui-label\" style=\"text-align:right;\">验证码：</label>\n" +
                "                        </div>\n" +
                "                        <div class=\"weui-cell__bd\">\n" +
                "                            <input id=\"checkmobilevcode\" type=\"hidden\">\n" +
                "                            <input class=\"weui-input\" type=\"text\" id=\"mobilevcode\" placeholder=\"短信验证码\" maxlength=\"5\">\n" +
                "                        </div>\n" +
                "                        <div class=\"weui-cell__ft\">\n" +
                "                            <button class=\"weui-vcode-btn\" id=\"mobilevcodebtn\" style=\"border-left:none;color:#0B92D9;\">获取验证码</button>\n" +
                "                        </div>\n" +
                "                    </div>";


            $("#clickhide").parent().parent().before(str2);
            // $("#authenticationtype").parent().parent().remove();
            $("#contactname").parent().parent().remove();
            $("#owncompany").parent().parent().remove();
            $("#elereceivermobile").parent().parent().remove();
            // cxh start 如果是院校版提示 选择其他类型签署合同。
            var productclassonecollege=$("#productclassonecollege").data("values");
            console.log(productclassonecollege);
            if((productclassonecollege==7 || productclassonecollege==9) && signaturetype=='eleccontract'){
                $.toast('院校版或集团版目前没有电子合同！重新选择合同类型。',"text");
            }
            // cxh end
        }else{
            $("#servicecontractsid").parent().parent().remove();
            $("#mobilevcode").parent().parent().remove();
            $.ajax({
                    url: '/index.php?module=TyunWebBuyService&action=getMainPart',
                    type: 'POST',
                    dataType: 'json',
                    beforeSend:function(){
                        $.showLoading('加载中');
                    },
                    success: function (data) {
                        $.hideLoading();
                        if(data.success==1){
                            var owncompanys = data.data.owncompany;
                            var str = '';
                            $.each(owncompanys,function (k, v) {
                                str += '<option value="'+v.companyid+'" '+((v.companyid==data.data.companyid)?'selected':'')+'>'+v.owncompany+'</option>'
                            })
                            str2 = "<div class=\"weui-cell weui-cell_select weui-cell_select-after weui-cell_select_custom_spacing\">\n" +
                                "                        <div class=\"weui-cell__hd\">\n" +
                                "                            <label for=\"owncompany\" class=\"weui-label\" style=\"text-align:right;\">合同主体：</label>\n" +
                                "                        </div>\n" +
                                "                        <div class=\"weui-cell__bd\">\n" +
                                "                            <select class=\"weui-select\" name=\"owncompany\" id=\"owncompany\">\n" +
                                str+
                                "                            </select>\n" +
                                "                        </div>\n" +
                                "                    </div>";
                            $("#authenticationtype").parent().parent().after(str2);
                            str1 = "<div class=\"weui-cell weui-cell_select weui-cell_select-after weui-cell_select_custom_spacing\">\n" +
                                "                        <div class=\"weui-cell__hd\"><label class=\"weui-label\" style=\"text-align:right;\">接收人：</label></div>\n" +
                                "                  <div class=\"weui-cell__bd\" style=\"position: relative;\">\n" +
                                "                            <div id=\"contactnameclickhide\" class=\"weui-select\"  style=\"position: absolute;z-index:2;background-color: #ffffff;padding:0;white-space: nowrap;overflow: hidden;\">点击选择客户联系人</div>\n" +
                                "                            <select class=\"weui-select\" name=\"contactname\" id=\"contactname\">\n" +
                                "                            </select>\n" +
                                "                        </div>"+
                                "                   </div>"+
                                "                <div class=\"weui-cell\">\n" +
                                "                        <div class=\"weui-cell__hd\"><label class=\"weui-label\" style=\"text-align:right;\">接收人手机：</label></div>\n" +
                                "                        <div class=\"weui-cell__bd\">\n" +
                                "                            <input class=\"weui-input\" type=\"text\" readonly name=\"elereceivermobile\" id=\"elereceivermobile\" placeholder=\"\" data-msg=\"接收人手机号\">\n" +
                                "                        </div>\n" +
                                "                    <div class=\"weui-cell__ft\"></div>"
                                "               </div>";
                            $("#owncompany").parent().parent().after(str1);
                        }else{
                            $.toast(data.msg,'text');
                        }
                        // cxh start 如果是院校版提示 选择其他类型签署合同。
                        var productclassonecollege=$("#productclassonecollege").data("values");
                        console.log(productclassonecollege);
                        if((productclassonecollege==7 || productclassonecollege==9) && signaturetype=='eleccontract'){
                            $.toast('院校版或集团版目前没有电子合同！重新选择合同类型。',"text");
                        }
                        // cxh end
                    }
                }
            );
        }
        initNextStepButton();
    });

    $('#container').on('click','#contactnameclickhide',function(event){
        var thisInstance=this;

        var accountid=$('input[name="accountid"]').val();
        if(!accountid){
            return false;
        }
        $.ajax({
            url: '/index.php?module=TyunWebBuyService&action=accountLink',
            type: 'POST',
            dataType: 'json',
            data:{
                "accountid":accountid,
            },
            beforeSend:function(){
                $.showLoading();
            },
            success: function (data) {
                $.hideLoading();
                if (data.success==1) {
                    var i = 0;
                    $.each(data.data,function(key,value){
                        var selected='';
                        if(i==0){
                            selected='selected';
                            $("#elereceivermobile").val(value.mobile);
                        }
                        $("#contactname").append('<option value="'+value.linkname+'" data-mobile="'+value.mobile+'" '+selected+'>'+value.linkname+'</option>');
                        i++;
                    });

                    $(thisInstance).hide();
                    $('#contactname').trigger('click');
                    initNextStepButton();
                }else{
                    $.toast(data.msg,'text');
                }
            }
        });
        event.stopPropagation();
        return false;
    });

    $("#container").on('change','#contactname',function () {
        var mobile = $("#contactname").find("option:checked").data('mobile');
        $("#elereceivermobile").val(mobile);
    });

    //由电子合同页面返回上一页面
    $("#container").on('click','#econtractback',function () {
        var producttype = $("#producttype").val();
        if(producttype=='common'){
            pageManager.go('selectproduct');
            return;
        }
        pageManager.go('activityproduct');
    });

    //提交手机号修改
    $("#container").on('click','#submitupdatemobile',function (k, v) {
        var elereceivermobile = $("#elereceivermobile").val();
        if(!elereceivermobile){
            $.toast('请输入手机号','text');
            return;
        }
        if(!(/^1[3456789]\d{9}$/.test(elereceivermobile))){
            $.toast("手机号码有误，请重填",'text');
            return false;
        }
        $("#lastelereceivermobile").attr('data-value',elereceivermobile);
        $.toast('修改成功','text');
        pageManager.back();
    });

    //续费另购部分单品
    function renewOtherProduct(){
        $('#renewproduct').empty();
        var isShow= false;
        if((typeof productSpecificationList)!='undefined' && productSpecificationList.length>0) {
            str += '<div class="weui-cells weui-cells_checkbox">\n' +
                '                <div class="weui-cell weui-check__label"  style="padding-left:0;display: flex;justify-content: space-between;">\n' +
                '                    <label style="display: flex;"><div class="weui-cell__hd">\n' +
                '                        <input type="checkbox" class="weui-check productSpecificationListgroup">\n' +
                '                        <i class="weui-icon-checked"></i>\n' +
                '                    </div>\n' +
                '                    <div class="weui-cell__bd">\n' +
                '                        <p>续费部分另购单品</p>\n' +
                '                    </div></label>' +
                '                    <div class="weui-cell__ft">\n' +
                '                        <i class="iconfont icon-jian-tianchong productListshow"  style="color:#4994F2;font-size: 20px;margin-right: 10px;" data-flag="1"></i>' +
                '                    </div>' +
                '                </div>\n';
            var flagnum = 3;
            $.each(productSpecificationList, function (key, value) {
                if(canrenewotherproductids.indexOf(value.ProductID)<0){
                    return true;
                }

                if(value.CanRenew){
                    $.each(value.ProductSpecifications,function(key1,value1){
                        isShow = true;
                        str += '                <label class="weui-cell weui-check__label productshow">\n' +
                            '                    <div class="weui-cell__hd">\n' +
                            '                        <input type="checkbox" class="weui-check productSpecificationList  productSpecification' + flagnum + '" data-specificationid="'+value.SpecificationID+'" data-marketrenewprice="'+value1.MarketRenewPrice+'" data-marketprice="'+value1.MarketPrice+'" data-price="'+value1.Price+'" data-renewprice="'+value1.RenewPrice+'" data-count="'+value1.Count+'" data-packid="' + value.PackageID + '" data-productid="' + value.ProductID + '" data-closeDate="' + value1.CloseDate + '" data-productspecificationsid="' + value1.ID + '" data-producttitle="'+value.ProductTitle+'" data-productspecificationstitle="'+value1.Title+'">\n' +
                            '                        <i class="weui-icon-checked"></i>\n' +
                            '                    </div>\n' +
                            '                    <div class="weui-cell__bd">\n' +
                            '                        <p>' + value.ProductTitle + '--' + value1.Title + '--' + value1.CloseDate + '</p>\n' +
                            '                    </div>\n' +
                            '                </label>\n';
                    });

                }

            });
            str += '</div>';
        }
        if(isShow){
            $('#renewproduct').append(str);
        }
    }


    $('#container').on('click','.helpinfo',function(){
        var thisInstance=$(this);
        $.alert(thisInstance.data('content'), thisInstance.data('title'));
    });


    // function judgecontain(){
    //     var activitymodel = $("#activity_detail").data('activitymodel');
    //     if($("#productclasstwo").val()){
    //         renewDomainPage(activitymodel);
    //     }else{
    //         $('#renewdomain').empty();
    //     }
    //     //续费的情况下才显示可续费的单品
    //     if(activitymodel==4){
    //         var iscontaindomain = false;
    //         $(".product").each(function (k, v) {
    //             if(canrenewotherproductids.indexOf($(v).data('productid'))>=0){
    //                 iscontaindomain = true;
    //             }
    //         });
    //         if(iscontaindomain){
    //             renewOtherProduct();
    //         }else{
    //             $('#renewproduct').empty();
    //         }
    //     }
    //
    // }

    function initNextStepButton(){
        var tyunusercode = $("#tyunusercode").val();
        var accountid = $("#accountid").val();
        var signaturetype = $("#signaturetype").val();
        if(signaturetype=='eleccontract'){
            var elereceivermobile = $("#elereceivermobile").val();
            var contactname = $("#contactname").val();
            var ismobile=false;
            if (elereceivermobile && elereceivermobile.match(/^((1[3-9])+\d{9})$/)) {
                ismobile=true;
            }
            if(tyunusercode && accountid && elereceivermobile && contactname && ismobile){
                $('#nextstep').css({"background-color":'#0B92D9'});
                return;
            }
        }else{
            var servicecontractsid_display = $("input[name=servicecontractsid_display]").val();
            var mobilevcode = $("#mobilevcode").val();

            if(tyunusercode && accountid && servicecontractsid_display && mobilevcode){
                $('#nextstep').css({"background-color":'#0B92D9'});
                return;
            }
        }

        $('#nextstep').css({"background-color":'#999999'});
        return;
    }


    //电子合同下单预览
    $('#container').on('click','#electronnextstep2',function(){
        if(!$("#servicetotal").val()){
            $.toast('请填写合同金额','text');
            return false;
        }
        if(isMoreCategory()){
            $.toast('存在多个套餐，不允许使用电子合同下单',"text");
            return false;
        }
        var submitid=$('#submitid').attr('data-value');
        if(submitid!=2){
            return false;
        }
        if(!parseFloat($("input[name=marketactivitypriceval]").val())){
            return false;
        }

        $("#templateid").attr('data-value',0);
        var classtype=$('#classtype').val();
        var producttypes = $("#producttype").val();

        var productclasstwo = $(".product").data('productname');
        var productclasstwovalues =  $("#productclasstwo").data('values');
        switch (classtype) {
            case 'renew':
                servicecontractstype = 2;
                break;
            case "upgrade":
                servicecontractstype = 3;
                break;
            default:
                servicecontractstype = 1;
                break;
        }

        if(productclasstwovalues){
            var templateParams = {
                "productCode":[productclasstwovalues],
                "servicecontractstype":servicecontractstype,
                "isPackage":1,
                "orderType":producttypes
            }
            var isPackage = 1;
            var productCode = [productclasstwovalues];
        }else{
            var productid = [];
            var i = 0;
            $.each($('.select3check'),function(keyt,valuet){
                productid[i]=$(valuet).data('productid');
                i++;
            });
            $.each($(".product"),function (key, value) {
                productid[i]=$(value).data('productid');
                i++;
            });

            $.each($(".giveproductlistall"),function (key, value) {
                productid[i]=$(value).data('productid');
                i++;
            });
            console.log(productid)
            var templateParams = {
                "productCode":productid,
                "servicecontractstype":servicecontractstype,
                "isPackage":0,
                "orderType":producttypes
            }
            var productCode = productid;
            var isPackage = 0;
        }

        $.ajax({
            url: '/index.php?module=TyunWebBuyService&action=matchElecContractTemplate',
            type: 'POST',
            dataType: 'json',
            data: templateParams,
            beforeSend: function () {
                $.showLoading('处理中');
            },
            success: function (data) {
                $.hideLoading();
                if (data.success == 1) {
                    datas = data.data;
                    if(datas.length>1 || datas.length==0){
                        $.alert('系统没有匹配到电子合同模板或者匹配的不是唯一的合同模板，请联系管理员','提示');
                        return ;
                    }
                    var templateId = datas[0]['templateId'];
                    $("#templateid").attr('data-value',templateId);

                    var servicecontractsid=$('input[name="servicecontractsid"]').val();
                    var servicecontractsid_display=$('input[name="servicecontractsid_display"]').val();
                    var accountid=$('input[name="accountid"]').val();
                    var oldaccountid=$('input[name="oldaccountid"]').val();
                    var accountid_display=$('input[name="accountid_display"]').val();
                    var oldaccountid_display=$('input[name="oldaccountid_display"]').val();
                    var mobile=$('#mobile').val();
                    var mobilevcode=$('#mobilevcode').val();
                    var classtype=$('#classtype').val();
                    var buyyear=$('#buyyear').data('values');
                    var oldproductnameid=$('#oldproductnameid').val();
                    var servicetotal=parseFloat($('#servicetotal').val()).toFixed(2);
                    var classtyperenew=$('#classtyperenew').val();
                    var tyunusercode=$('#tyunusercode option:checked').text();
                    var tyunusercodeid=$('#tyunusercode').val();
                    var buyproduct=$('#buyproduct').attr('data-values');
                    var tyunusercodetext=$('#tyunusercode').find("option:checked").text();
                    var buydate=$('#buydate').val();
                    var currentdata=(new Date()).getFullYear()+'-'+((new Date()).getMonth()+1)+'-'+(new Date()).getDate();
                    var upgardecycle=$('#upgardecycle').val();
                    var oldproductname_display=$('#oldproductname_display').val();
                    var buyproductname=$('#buyproduct').val();
                    var separateproductname_display= '';
                    var activacode = $("#activacode").data('value');
                    var oproductid = $("#oproductid").data('value');

                    var give_title_display = $(".activity_check").filter('input:checked').data('title');

                    var activitytype = $("#activity_detail").data("activitytype");
                    var activityid = $("#activity_detail").data('id');
                    var activityno = $("#activity_detail").data('activityid');
                    var activitymodel = $("#activity_detail").data('activitymodel');
                    var activityagent = $("#activity_detail").data('activityagent');
                    var activitychildid = $("#buyproductgiveproduct_activity").val();
                    var activitytitle = $("#activity_detail").data('activitytitle');
                    var combinationprice = $("#activity_detail").data('combinationprice');
                    var meetActivity = $("#activity_detail").data('meetactivity');

                    var authenticationtype = $("#authenticationtype").val();
                    var activationcodeid = $("#activationcodeid").val();

                    var companyid = $("#owncompany").val();
                    var elereceiver = $("#contactname").val();
                    var elereceivermobile = $("#elereceivermobile").val();

                    //另购产品的
                    var otherproduct = [];
                    $.each($('.select3check'),function(k,v){
                        separateproductname_display += $(v).data('producttitle')+'('+$(v).data('number')+') ';
                    });

                    if(activitytype==1){
                        type_text='优惠组合';
                    }else if(activitytype==2){
                        type_text='赠送产品';
                    }if(activitytype==3){
                        type_text='赠送时间';
                    }else if(activitytype==4){
                        type_text='限时折扣';
                    }

                    //新购
                    if(activitymodel==1){
                        if(activitytype==1 || activitytype==4){
                            var productname = $(".product").data('productname');
                            var producttype = $(".product").data('producttype');
                            var buyyear = $(".product").data('buyyear');
                            var buynum = $(".product").data('buynum');

                            //另购服务end
                            var str='<div class="weui-form-preview" style="overflow: auto;height: 450px;">' +
                                '<div class="weui-form-preview__bd">' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">T云账号</label>' +
                                '<span class="weui-form-preview__value">'+tyunusercode+'</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">选择活动</label>' +
                                '<span class="weui-form-preview__value">'+give_title_display+'</span>' +
                                '</div>';

                            $(".product").each(function (k, v) {
                                str +=   '<div class="weui-form-preview__item">' +
                                    '<label class="weui-form-preview__label">产品名称</label>' +
                                    '<span class="weui-form-preview__value">'+$(v).data('productname')+'</span>' +
                                    '</div>' +
                                    '<div class="weui-form-preview__item">' +
                                    '<label class="weui-form-preview__label">规格名称</label>' +
                                    '<span class="weui-form-preview__value">'+$(v).data('producttype')+'</span>' +
                                    '</div>' +
                                    '<div class="weui-form-preview__item">' +
                                    '<label class="weui-form-preview__label">购买年限</label>' +
                                    '<span class="weui-form-preview__value">'+$(v).data('buyyear')+'年</span>' +
                                    '</div>' +
                                    '<div class="weui-form-preview__item">' +
                                    '<label class="weui-form-preview__label">购买数量</label>' +
                                    '<span class="weui-form-preview__value">'+$(v).data('buynum')+'</span>' +
                                    '</div>';
                            });


                            str += '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">活动类型</label>' +
                                '<span class="weui-form-preview__value">'+type_text+'</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">另购单品</label>' +
                                '<span class="weui-form-preview__value">'+(separateproductname_display?separateproductname_display:'无')+'</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">合同金额</label>' +
                                '<span class="weui-form-preview__value">￥'+servicetotal+'</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">客户名称</label>' +
                                '<span class="weui-form-preview__value">'+accountid_display+'</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">老客户名称</label>' +
                                '<span class="weui-form-preview__value">'+(oldaccountid_display?oldaccountid_display:'无')+'</span>' +
                                '</div>' +
                                '</div>' +
                                '</div>';
                        }else if(activitytype==3){
                            var productname = $(".product").data('productname');
                            var producttype = $(".product").data('producttype');
                            var buyproductgiveproduct_activity = $("#buyproductgiveproduct_activity").val();
                            var buyproductgiveproductyear = buyproductgiveproduct_activity.split('-');
                            var buyyear = $(".product").data('buyyear');
                            var buynum = $(".product").data('buynum');

                            //另购服务end
                            var str='<div class="weui-form-preview" style="overflow: auto;height: 450px;">' +
                                '<div class="weui-form-preview__bd">' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">T云账号</label>' +
                                '<span class="weui-form-preview__value">'+tyunusercode+'</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">选择活动</label>' +
                                '<span class="weui-form-preview__value">'+give_title_display+'</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">产品名称</label>' +
                                '<span class="weui-form-preview__value">'+productname+'</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">规格名称</label>' +
                                '<span class="weui-form-preview__value">'+producttype+'</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">购买年限</label>' +
                                '<span class="weui-form-preview__value">'+buyyear+'年</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">购买数量</label>' +
                                '<span class="weui-form-preview__value">'+buynum+'</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">活动类型</label>' +
                                '<span class="weui-form-preview__value">'+type_text+'</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">赠送年限</label>' +
                                '<span class="weui-form-preview__value">'+buyproductgiveproductyear[1]+'年</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">另购单品</label>' +
                                '<span class="weui-form-preview__value">'+(separateproductname_display?separateproductname_display:'无')+'</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">合同金额</label>' +
                                '<span class="weui-form-preview__value">￥'+servicetotal+'</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">客户名称</label>' +
                                '<span class="weui-form-preview__value">'+accountid_display+'</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">老客户名称</label>' +
                                '<span class="weui-form-preview__value">'+(oldaccountid_display?oldaccountid_display:'无')+'</span>' +
                                '</div>' +
                                '</div>' +
                                '</div>';
                        }else if(activitytype==2){
                            var productname = $(".product").data('productname');
                            var producttype = $(".product").data('producttype');
                            var buyyear = $(".product").data('buyyear');
                            var buynum = $(".product").data('buynum');

                            //另购服务end
                            var str='<div class="weui-form-preview" style="overflow: auto;height: 450px;">' +
                                '<div class="weui-form-preview__bd">' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">T云账号</label>' +
                                '<span class="weui-form-preview__value">'+tyunusercode+'</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">选择活动</label>' +
                                '<span class="weui-form-preview__value">'+give_title_display+'</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">产品名称</label>' +
                                '<span class="weui-form-preview__value">'+productname+'</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">规格名称</label>' +
                                '<span class="weui-form-preview__value">'+producttype+'</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">购买年限</label>' +
                                '<span class="weui-form-preview__value">'+buyyear+'年</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">购买数量</label>' +
                                '<span class="weui-form-preview__value">'+buynum+'</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">活动类型</label>' +
                                '<span class="weui-form-preview__value">'+type_text+'</span>' +
                                '</div>';
                            $(".giveproductlistall").each(function (k, v) {
                                str+=    '<div class="weui-form-preview__item">' +
                                    '<label class="weui-form-preview__label">赠送产品</label>' +
                                    '<span class="weui-form-preview__value">'+$(v).data('giveproductname')+'</span>' +
                                    '</div>'+
                                    '<div class="weui-form-preview__item">' +
                                    '<label class="weui-form-preview__label">赠送年限</label>' +
                                    '<span class="weui-form-preview__value">'+$(v).data('buyyear')+'年</span>' +
                                    '</div>'+
                                    '<div class="weui-form-preview__item">' +
                                    '<label class="weui-form-preview__label">产品数量</label>' +
                                    '<span class="weui-form-preview__value">'+$(v).data('buynum')+'</span>' +
                                    '</div>';
                            });


                            str +=    '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">另购单品</label>' +
                                '<span class="weui-form-preview__value">'+(separateproductname_display?separateproductname_display:'无')+'</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">合同金额</label>' +
                                '<span class="weui-form-preview__value">￥'+servicetotal+'</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">客户名称</label>' +
                                '<span class="weui-form-preview__value">'+accountid_display+'</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">老客户名称</label>' +
                                '<span class="weui-form-preview__value">'+(oldaccountid_display?oldaccountid_display:'无')+'</span>' +
                                '</div>' +
                                '</div>' +
                                '</div>';
                        }
                    }else if(activitymodel==2 || activitymodel==4){
                        //升级 续费
                        if(activitymodel==4){
                            modelname='续费';
                        }else{
                            modelname='升级';
                        }
                        if(activitytype==3){
                            var productname = $(".product").data('productname');
                            var producttype = $(".product").data('producttype');
                            var buyproductgiveproduct_activity = $("#buyproductgiveproduct_activity").val();
                            var buyproductgiveproductyear = buyproductgiveproduct_activity.split('-');
                            var buyyear = $(".product").data('buyyear');
                            var buynum = $(".product").data('buynum');

                            //另购服务end
                            var str ='<div class="weui-form-preview" style="overflow: auto;height: 450px;">' +
                                '<div class="weui-form-preview__bd">' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">T云账号</label>' +
                                '<span class="weui-form-preview__value">'+tyunusercode+'</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">原版本</label>' +
                                '<span class="weui-form-preview__value">'+oldproductname_display+'</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">选择活动</label>' +
                                '<span class="weui-form-preview__value">'+give_title_display+'</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">产品名称</label>' +
                                '<span class="weui-form-preview__value">'+productname+'</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">规格名称</label>' +
                                '<span class="weui-form-preview__value">'+producttype+'</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">'+modelname+'年限</label>' +
                                '<span class="weui-form-preview__value">'+buyyear+'年</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">'+modelname+'数量</label>' +
                                '<span class="weui-form-preview__value">'+buynum+'</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">活动类型</label>' +
                                '<span class="weui-form-preview__value">'+type_text+'</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">赠送年限</label>' +
                                '<span class="weui-form-preview__value">'+buyproductgiveproductyear[1]+'年</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">另购单品</label>' +
                                '<span class="weui-form-preview__value">'+(separateproductname_display?separateproductname_display:'无')+'</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">合同金额</label>' +
                                '<span class="weui-form-preview__value">￥'+servicetotal+'</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">客户名称</label>' +
                                '<span class="weui-form-preview__value">'+accountid_display+'</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">老客户名称</label>' +
                                '<span class="weui-form-preview__value">'+(oldaccountid_display?oldaccountid_display:'无')+'</span>' +
                                '</div>' +
                                '</div>' +
                                '</div>';
                        }else if(activitytype==4){
                            var productname = $(".product").data('productname');
                            var producttype = $(".product").data('producttype');
                            var buyyear = $(".product").data('buyyear');
                            var buynum = $(".product").data('buynum');

                            //另购服务end
                            var str='<div class="weui-form-preview" style="overflow: auto;height: 450px;">' +
                                '<div class="weui-form-preview__bd">' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">T云账号</label>' +
                                '<span class="weui-form-preview__value">'+tyunusercode+'</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">原版本</label>' +
                                '<span class="weui-form-preview__value">'+oldproductname_display+'</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">选择活动</label>' +
                                '<span class="weui-form-preview__value">'+give_title_display+'</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">产品名称</label>' +
                                '<span class="weui-form-preview__value">'+productname+'</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">规格名称</label>' +
                                '<span class="weui-form-preview__value">'+producttype+'</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">'+modelname+'年限</label>' +
                                '<span class="weui-form-preview__value">'+buyyear+'年</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">'+modelname+'数量</label>' +
                                '<span class="weui-form-preview__value">'+buynum+'</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">活动类型</label>' +
                                '<span class="weui-form-preview__value">'+type_text+'</span>' +
                                '</div>';
                            $(".giveproductlistall").each(function (k, v) {
                                str+=    '<div class="weui-form-preview__item">' +
                                    '<label class="weui-form-preview__label">赠送产品</label>' +
                                    '<span class="weui-form-preview__value">'+$(v).data('giveproductname')+'</span>' +
                                    '</div>'+
                                    '<div class="weui-form-preview__item">' +
                                    '<label class="weui-form-preview__label">赠送年限</label>' +
                                    '<span class="weui-form-preview__value">'+$(v).data('buyyear')+'年</span>' +
                                    '</div>'+
                                    '<div class="weui-form-preview__item">' +
                                    '<label class="weui-form-preview__label">产品数量</label>' +
                                    '<span class="weui-form-preview__value">'+$(v).data('buynum')+'</span>' +
                                    '</div>';
                            });


                            str +=    '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">另购单品</label>' +
                                '<span class="weui-form-preview__value">'+(separateproductname_display?separateproductname_display:'无')+'</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">合同金额</label>' +
                                '<span class="weui-form-preview__value">￥'+servicetotal+'</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">客户名称</label>' +
                                '<span class="weui-form-preview__value">'+accountid_display+'</span>' +
                                '</div>' +
                                '<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">老客户名称</label>' +
                                '<span class="weui-form-preview__value">'+(oldaccountid_display?oldaccountid_display:'无')+'</span>' +
                                '</div>' +
                                '</div>' +
                                '</div>';
                        }
                    }

                    //另购产品的
                    var otherproduct = [];
                    var productid = [];
                    $.each($('.select3check'),function(k,v){
                        productid[k]=$(v).data('productid');
                        otherproduct[k] ={
                            'categoryID': parseInt($(v).data('categoryid')),
                            'packageID':parseInt(0),
                            'productID':parseInt($(v).data('productid')),
                            'specificationID':parseInt($(v).data('id')),
                            'count':parseInt($(v).data('number')),
                            'buyTerm':parseInt($("#buyyear").data('values')),
                            'isDelete':false,
                            "price":parseFloat($(v).data('price')),
                            "renewPrice":parseFloat($(v).data('renewprice')),
                            "marketPrice":parseFloat($(v).data('marketprice')),
                            "marketRenewPrice":parseFloat($(v).data('marketrenewprice')),
                            "activityThresholdBuyTerm":0,
                            "activityThresholdCount":0,
                            "activityMarketPrice":0,
                            "activityPrice":0,
                            "activityRenewMarketPrice":0,
                            "activityRenewPrice":0,
                            "packageTitle":'',
                            "productTitle":'',
                            "specificationNumber":'',
                            "specificationTitle":'',
                            "unit":'',
                            "userProductID":0
                        };
                    });

                    if(activitytype==1){
                        type_text='优惠组合';
                    }else if(activitytype==2){
                        type_text='赠送产品';
                    }if(activitytype==3){
                        type_text='赠送时间';
                    }else if(activitytype==4){
                        type_text='限时折扣';
                    }


                    /** 获取要购买的产品start **/
                    var arr = [];
                    var pj = 0;
                    var productinfoid = [];
                    $(".product").each(function (k, v) {
                        arr[k]=$(v).data('key');
                    });

                    var productinfo=[];
                    console.log(productlist);
                    var i=0;
                    $(productlist).each(function (k, v) {
                        var isdelete = false;
                        if(activitytype==2){
                            var categoryid2 = $("#productclassone").data('values');
                            var packageid2 = $("#productclasstwo").data('values');
                            if(categoryid2!=v.CategoryID || packageid2 != v.PackageID){
                                isdelete = true;
                                buynum=0;
                                buyyear=0;
                            }
                            buynum = $('.product'+k).data('buynum');
                            buyyear = $('.product'+k).data('buyyear');
                        }else if(activitytype==3){
                            var buyproductkey = $("#buyproduct").data('values');
                            if(buyproductkey!=k){
                                return true;
                            }
                            buynum = $('.product0').data('buynum');
                            buyyear = $('.product0').data('buyyear');
                        }else{
                            if(arr.indexOf(k)==-1){
                                isdelete = true;
                                buynum=0;
                                buyyear=0;
                            }
                            buynum = $('.product'+k).data('buynum');
                            buyyear = $('.product'+k).data('buyyear');
                        }

                        var flag = false;
                        if((buynum >=v.ActivityThresholdCount) && (buyyear>=v.ActivityThresholdBuyTerm)){
                            flag =true;
                        }

                        console.log(buynum);
                        console.log(buyyear);
                        if(!isdelete){
                            if(!v.PackageID){
                                productinfoid[pj] =parseInt(v.ProductID);
                                pj++;
                            }
                            productinfo[i] = {
                                'categoryID': parseInt(v.CategoryID),
                                'packageID':parseInt(v.PackageID),
                                'productID':parseInt(v.ProductID),
                                'specificationID':parseInt(v.SpecificationID),
                                'count':parseInt(((buynum==undefined)?0:buynum)),
                                'buyTerm':parseInt(((buyyear==undefined)?0:buyyear)),
                                "price":(flag?parseFloat(v.ActivityPrice):parseFloat(v.OriginalPrice.price)),
                                "renewPrice":(flag?parseFloat(v.ActivityRenewPrice):parseFloat(v.OriginalPrice.renewPrice)),
                                "marketPrice":(flag?parseFloat(v.ActivityMarketPrice):parseFloat(v.OriginalPrice.marketPrice)),
                                "marketRenewPrice":(flag?parseFloat(v.ActivityRenewMarketPrice):parseFloat(v.OriginalPrice.marketRenewPrice)),
                                "activityThresholdBuyTerm":v.ActivityThresholdBuyTerm,
                                "activityThresholdCount":v.ActivityThresholdCount,
                                "activityMarketPrice":v.ActivityMarketPrice,
                                "activityPrice":v.ActivityPrice,
                                "activityRenewMarketPrice":v.ActivityRenewMarketPrice,
                                "activityRenewPrice":v.ActivityRenewPrice,
                                "packageTitle":v.PackageTitle,
                                "productTitle":v.ProductTitle,
                                "specificationNumber":v.SpecificationCount,
                                "specificationTitle":v.SpecificationTitle,
                                "unit":v.SpecificationUnit,
                                "userProductID":0
                            };
                            i++;
                        }

                    });

                    var giveproductlistallkey = [];
                    $(".giveproductlistall").each(function (k2, v2) {
                        giveproductlistallkey.push($(v2).data('key'));
                    });

                    console.log(giveproductlistallkey);
                    var giftproduct = [];
                    console.log(giftproductlist);
                    $.each(giftproductlist,function (k, v) {
                        console.log(k);
                        if(giveproductlistallkey==undefined || !giveproductlistallkey.length ||giveproductlistallkey.indexOf(k)==-1){
                            giftproduct[k]={
                                'categoryID': parseInt(v.CategoryID),
                                'packageID':parseInt(v.PackageID),
                                'productID':parseInt(v.ProductID),
                                'specificationID':parseInt(v.SpecificationID),
                                'count':parseInt(0),
                                'buyTerm':parseInt(0),
                                'isDelete':true,
                                "price":parseFloat(v.OriginalPrice.price),
                                "renewPrice":parseFloat(v.OriginalPrice.renewPrice),
                                "marketPrice":parseFloat(v.OriginalPrice.marketPrice),
                                "marketRenewPrice":parseFloat(v.OriginalPrice.marketRenewPrice),
                                "activityThresholdBuyTerm":v.ActivityThresholdBuyTerm,
                                "activityThresholdCount":v.ActivityThresholdCount,
                                "activityMarketPrice":v.ActivityMarketPrice,
                                "activityPrice":v.ActivityPrice,
                                "activityRenewMarketPrice":v.ActivityRenewMarketPrice,
                                "activityRenewPrice":v.ActivityRenewPrice,
                                "packageTitle":v.PackageTitle,
                                "productTitle":v.ProductTitle,
                                "specificationNumber":v.SpecificationCount,
                                "specificationTitle":v.SpecificationTitle,
                                "unit":v.SpecificationUnit,
                                "userProductID":0
                            }
                        }else{
                            if(!$("#giveproductlist"+k).data('packageid')){
                                productinfoid[pj] =parseInt($("#giveproductlist"+k).data('productid'));
                                pj++;
                            }

                            giftproduct[k]={
                                'categoryID': parseInt($("#giveproductlist"+k).data('categoryid')),
                                'packageID':parseInt($("#giveproductlist"+k).data('packageid')),
                                'productID':parseInt($("#giveproductlist"+k).data('productid')),
                                'specificationID':parseInt($("#giveproductlist"+k).data('specificationid')),
                                'count':parseInt($("#giveproductlist"+k).data('buynum')),
                                'buyTerm':parseInt($("#giveproductlist"+k).data('buyyear')),
                                'isDelete':false,
                                "price":parseFloat(v.OriginalPrice.price),
                                "renewPrice":parseFloat(v.OriginalPrice.renewPrice),
                                "marketPrice":parseFloat(v.OriginalPrice.marketPrice),
                                "marketRenewPrice":parseFloat(v.OriginalPrice.marketRenewPrice),
                                "activityThresholdBuyTerm":v.ActivityThresholdBuyTerm,
                                "activityThresholdCount":v.ActivityThresholdCount,
                                "activityMarketPrice":v.ActivityMarketPrice,
                                "activityPrice":v.ActivityPrice,
                                "activityRenewMarketPrice":v.ActivityRenewMarketPrice,
                                "activityRenewPrice":v.ActivityRenewPrice,
                                "packageTitle":v.PackageTitle,
                                "productTitle":v.ProductTitle,
                                "specificationNumber":v.SpecificationCount,
                                "specificationTitle":v.SpecificationTitle,
                                "unit":v.SpecificationUnit,
                                "userProductID":0
                            }
                        }
                    });

                    //域名续费权益
                    var chooseuserproduct = [];
                    var packSpecificationList2 = $(".packSpecificationList2").filter('input:checked');
                    $.each(packSpecificationList2,function (k2, v2) {
                        chooseuserproduct.push(parseInt($(v2).data('productspecificationsid')));
                    });

                    console.log(productinfo);
                    console.log(giftproduct);
                    console.log(giftproduct);
                    var tyunusercode=$('#tyunusercode').val();
                    var oldcontractcode_display = $("#oldcontractcode_display").val();
                    var params={
                        "servicecontractsid":servicecontractsid,
                        "servicecontractsid_display":servicecontractsid_display,
                        "accountid":accountid,
                        "accountid_display":accountid_display,
                        "mobile":mobile,
                        "mobilevcode":mobilevcode,
                        "classtype":classtype,
                        "categoryid":classtyperenew,
                        "type":1,
                        "buyyear":buyyear,
                        "buydate":buydate,
                        'packageid':oldproductnameid,
                        "tyunusercode":tyunusercode,
                        "servicetotal":servicetotal,
                        "tyunusercodeid":tyunusercodeid,
                        "buyproduct":buyproduct,
                        "tyunusercodetext":tyunusercodetext,
                        "oldcustomerid":oldaccountid,
                        "oldcustomername":oldaccountid_display,
                        "oldproductid":oproductid,
                        "oldproductname":oldproductname_display,
                        "activacode":activacode,
                        "oldcontractcode_display":oldcontractcode_display,

                        //新增下单参数
                        "activitychildid":activitychildid,
                        "giftproduct":giftproduct,
                        "productinfo":productinfo,
                        "agentIdentity":activityagent,
                        "activitytype":activitytype,
                        "activitymodel":activitymodel,
                        "activityid":activityid,
                        //另购
                        "otherproduct":otherproduct,
                        //另购产品
                        "separateproductname_display":separateproductname_display,
                        "activitytitle":activitytitle,
                        "combinationprice":combinationprice,
                        "meetactivity":true,
                        "activitytypetext":type_text,
                        "activityno":activityno,
                        "authenticationtype":authenticationtype,
                        "chooseuserproduct":chooseuserproduct,
                        'companyid':companyid,
                        "elereceiver":elereceiver,
                        "templateid":templateId,
                        "elereceivermobile":elereceivermobile,
                        "packagename":productclasstwo,
                        "orderType":producttypes,
                        'productCode':productCode,
                        "isPackage":isPackage,
                        "productid":productid,
                        "productinfoid":productinfoid
                    };

                    $.confirm(str, "请核对订单信息", function() {
                        $.ajax({
                                url: '/index.php?module=TyunWebBuyService&action=preAddOrder2',
                                type: 'POST',
                                dataType: 'json',
                                data:params,
                                beforeSend:function(){
                                    $.showLoading('订单处理中');
                                },
                                success: function (data) {
                                    $.hideLoading();
                                    if(data.success==1){
                                        console.log(data);
                                        $("#contractid").attr('data-value',data.data.contractId);
                                        $("#paycode").attr("data-value",data.data.paycode);
                                        $("#eleccontracturl").attr("data-value",data.data.contractUrl);
                                        pageManager.go('eleccontract');
                                    }else{
                                        $.toast(data.msg,'text');
                                    }
                                }
                            }
                        );
                    }, function() {
                        //取消操作
                    });

                }else{
                    $.toast(data.msg, "forbidden");
                    return ;
                }
            }
        });

    });

    //电子合同下单
    $('#container').on('click','#electronsubmitfrom2',function(){

        var totalmarketprice = $("#totalmarketprice").val();
        var servicetotal = $("#servicetotal").val();

        var params = {
            "totalmarketprice":totalmarketprice,
            "servicetotal":servicetotal
        };
        $.ajax({
            url: '/index.php?module=TyunWebBuyService&action=elecContractSignCheck',
            type: 'POST',
            dataType: 'json',
            data: params,
            beforeSend: function () {
                $.showLoading('处理中');
            },
            success: function (data) {
                $.hideLoading();
                if (data.success == 1) {
                    console.log(data);
                    discountflag = data.data;
                    var elereceivermobile = $("#elereceivermobile").val();
                    var elereceiver= $("#contactname").val();
                    elereceivermobilestr = '<span style="border-bottom: 1px solid black;font-weight: bold;">'+elereceivermobile+'</span>';

                    var prestr = '';
                    var isverify = 0;
                    if(discountflag ){
                        prestr = '该电子合同金额低于可折扣金额，需要经过相关人员审批后，系统自动发起合同签署短信至接收人手机'+elereceivermobilestr+'(作为联系人接收短信)';
                        isverify = 1;
                    }else{
                        prestr = '确定发起合同签署短信至接收人手机'+elereceivermobilestr+'(作为联系人接收短信)';
                    }

                    var str='<div class="weui-form-preview">' +
                        '<div class="weui-form-preview__bd">' +
                        '<div class="weui-form-preview__item">' +
                        '<span class="weui-form-preview__value" style="text-align: center;">'+prestr+'</span>' +
                        '</div>' +
                        '</div>' +
                        '</div>';
                    $.confirm(str, "提醒", function() {
                        var servicecontractsid=$('input[name="servicecontractsid"]').val();
                        var servicecontractsid_display=$('input[name="servicecontractsid_display"]').val();
                        var accountid=$('input[name="accountid"]').val();
                        var oldaccountid=$('input[name="oldaccountid"]').val();
                        var accountid_display=$('input[name="accountid_display"]').val();
                        var oldaccountid_display=$('input[name="oldaccountid_display"]').val();
                        var mobile=$('#mobile').val();
                        var mobilevcode=$('#mobilevcode').val();
                        var classtype=$('#classtype').val();
                        var buyyear=$('#buyyear').data('values');
                        var oldproductnameid=$('#oldproductnameid').val();
                        var servicetotal=parseFloat($('#servicetotal').val()).toFixed(2);
                        var classtyperenew=$('#classtyperenew').val();
                        var tyunusercode=$('#tyunusercode option:checked').text();
                        var tyunusercodeid=$('#tyunusercode').val();
                        var buyproduct=$('#buyproduct').attr('data-values');
                        var tyunusercodetext=$('#tyunusercode').find("option:checked").text();
                        var buydate=$('#buydate').val();
                        var currentdata=(new Date()).getFullYear()+'-'+((new Date()).getMonth()+1)+'-'+(new Date()).getDate();
                        var upgardecycle=$('#upgardecycle').val();
                        var oldproductname_display=$('#oldproductname_display').val();
                        var buyproductname=$('#buyproduct').val();
                        var separateproductname_display= '';
                        var activacode = $("#activacode").data('value');
                        var oproductid = $("#oproductid").data('value');

                        var give_title_display = $(".activity_check").filter('input:checked').data('title');

                        var activitytype = $("#activity_detail").data("activitytype");
                        var activityid = $("#activity_detail").data('id');
                        var activityno = $("#activity_detail").data('activityid');
                        var activitymodel = $("#activity_detail").data('activitymodel');
                        var activityagent = $("#activity_detail").data('activityagent');
                        var activitychildid = $("#buyproductgiveproduct_activity").val();
                        var activitytitle = $("#activity_detail").data('activitytitle');
                        var activityenddate = $("#activity_detail").data('activityenddate');
                        var combinationprice = $("#activity_detail").data('combinationprice');
                        var meetActivity = $("#activity_detail").data('meetactivity');
                        var templateId = $("#templateid").data('value');
                        var contractid = $("#contractid").data('value');
                        var authenticationtype = $("#authenticationtype").val();
                        var paycode = $("#paycode").data("value");

                        var invoicecompany = $("#owncompany").find("option:checked").text();
                        var invoicecompanyid = $("#owncompany").val();
                        var signaturetype = $("#signaturetype").val();
                        var eleccontracturl = $("#eleccontracturl").data("value");

                        //另购产品的
                        var otherproduct = [];
                        $.each($('.select3check'),function(k,v){
                            otherproduct[k] ={
                                'categoryID': parseInt($(v).data('categoryid')),
                                'packageID':parseInt(0),
                                'productID':parseInt($(v).data('productid')),
                                'specificationID':parseInt($(v).data('id')),
                                'count':parseInt($(v).data('number')),
                                'buyTerm':parseInt($("#buyyear").data('values')),
                                'isDelete':false,
                                "price":parseFloat($(v).data('price')),
                                "renewPrice":parseFloat($(v).data('renewprice')),
                                "marketPrice":parseFloat($(v).data('marketprice')),
                                "marketRenewPrice":parseFloat($(v).data('marketrenewprice')),
                                "activityThresholdBuyTerm":0,
                                "activityThresholdCount":0,
                                "activityMarketPrice":0,
                                "activityPrice":0,
                                "activityRenewMarketPrice":0,
                                "activityRenewPrice":0,
                                "packageTitle":'',
                                "productTitle":'',
                                "specificationNumber":'',
                                "specificationTitle":'',
                                "unit":'',
                                "userProductID":0
                            };
                        });

                        if(activitytype==1){
                            type_text='优惠组合';
                        }else if(activitytype==2){
                            type_text='赠送产品';
                        }if(activitytype==3){
                            type_text='赠送时间';
                        }else if(activitytype==4){
                            type_text='限时折扣';
                        }

                        var activationcodeid = $("#activationcodeid").val();

                        /** 获取要购买的产品start **/
                        var arr = [];
                        $(".product").each(function (k, v) {
                            arr[k]=$(v).data('key');
                        });

                        var productinfo=[];
                        console.log(productlist);
                        var i=0;
                        $(productlist).each(function (k, v) {
                            buynum = $('.product'+k).data('buynum');
                            buyyear = $('.product'+k).data('buyyear');

                            var flag = false;
                            if((buynum >=v.ActivityThresholdCount) && (buyyear>=v.ActivityThresholdBuyTerm)){
                                flag =true;
                            }
                            var isdelete = false;

                            if(activitytype==3 || activitytype==2){
                                var categoryid2 = $("#productclassone").data('values');
                                var packageid2 = $("#productclasstwo").data('values');
                                if(categoryid2!=v.CategoryID || packageid2 != v.PackageID){
                                    isdelete = true;
                                    buynum=0;
                                    buyyear=0;
                                }
                            }else{
                                if(arr.indexOf(k)==-1){
                                    isdelete = true;
                                    buynum=0;
                                    buyyear=0;
                                }
                            }

                            if(!isdelete){
                                productinfo[i] = {
                                    'categoryID': parseInt(v.CategoryID),
                                    'packageID':parseInt(v.PackageID),
                                    'productID':parseInt(v.ProductID),
                                    'specificationID':parseInt(v.SpecificationID),
                                    'count':parseInt(((buynum==undefined)?0:buynum)),
                                    'buyTerm':parseInt(((buyyear==undefined)?0:buyyear)),
                                    "price":(flag?parseFloat(v.ActivityPrice):parseFloat(v.OriginalPrice.price)),
                                    "renewPrice":(flag?parseFloat(v.ActivityRenewPrice):parseFloat(v.OriginalPrice.renewPrice)),
                                    "marketPrice":(flag?parseFloat(v.ActivityMarketPrice):parseFloat(v.OriginalPrice.marketPrice)),
                                    "marketRenewPrice":(flag?parseFloat(v.ActivityRenewMarketPrice):parseFloat(v.OriginalPrice.marketRenewPrice)),
                                    "activityThresholdBuyTerm":v.ActivityThresholdBuyTerm,
                                    "activityThresholdCount":v.ActivityThresholdCount,
                                    "activityMarketPrice":v.ActivityMarketPrice,
                                    "activityPrice":v.ActivityPrice,
                                    "activityRenewMarketPrice":v.ActivityRenewMarketPrice,
                                    "activityRenewPrice":v.ActivityRenewPrice,
                                    "packageTitle":v.PackageTitle,
                                    "productTitle":v.ProductTitle,
                                    "specificationNumber":v.SpecificationCount,
                                    "specificationTitle":v.SpecificationTitle,
                                    "unit":v.SpecificationUnit,
                                    "userProductID":0
                                };
                                i++;
                            }

                        });

                        var giveproductlistallkey = [];
                        $(".giveproductlistall").each(function (k2, v2) {
                            giveproductlistallkey.push($(v2).data('key'));
                        });

                        console.log(giveproductlistallkey);
                        var giftproduct = [];
                        console.log(giftproductlist);
                        $.each(giftproductlist,function (k, v) {
                            console.log(k);
                            if(giveproductlistallkey==undefined || !giveproductlistallkey.length ||giveproductlistallkey.indexOf(k)==-1){
                                giftproduct[k]={
                                    'categoryID': parseInt(v.CategoryID),
                                    'packageID':parseInt(v.PackageID),
                                    'productID':parseInt(v.ProductID),
                                    'specificationID':parseInt(v.SpecificationID),
                                    'count':parseInt(0),
                                    'buyTerm':parseInt(0),
                                    'isDelete':true,
                                    "price":parseFloat(v.OriginalPrice.price),
                                    "renewPrice":parseFloat(v.OriginalPrice.renewPrice),
                                    "marketPrice":parseFloat(v.OriginalPrice.marketPrice),
                                    "marketRenewPrice":parseFloat(v.OriginalPrice.marketRenewPrice),
                                    "activityThresholdBuyTerm":v.ActivityThresholdBuyTerm,
                                    "activityThresholdCount":v.ActivityThresholdCount,
                                    "activityMarketPrice":v.ActivityMarketPrice,
                                    "activityPrice":v.ActivityPrice,
                                    "activityRenewMarketPrice":v.ActivityRenewMarketPrice,
                                    "activityRenewPrice":v.ActivityRenewPrice,
                                    "packageTitle":v.PackageTitle,
                                    "productTitle":v.ProductTitle,
                                    "specificationNumber":v.SpecificationCount,
                                    "specificationTitle":v.SpecificationTitle,
                                    "unit":v.SpecificationUnit,
                                    "userProductID":0
                                }
                            }else{
                                giftproduct[k]={
                                    'categoryID': parseInt($("#giveproductlist"+k).data('categoryid')),
                                    'packageID':parseInt($("#giveproductlist"+k).data('packageid')),
                                    'productID':parseInt($("#giveproductlist"+k).data('productid')),
                                    'specificationID':parseInt($("#giveproductlist"+k).data('specificationid')),
                                    'count':parseInt($("#giveproductlist"+k).data('buynum')),
                                    'buyTerm':parseInt($("#giveproductlist"+k).data('buyyear')),
                                    'isDelete':false,
                                    "price":parseFloat(v.OriginalPrice.price),
                                    "renewPrice":parseFloat(v.OriginalPrice.renewPrice),
                                    "marketPrice":parseFloat(v.OriginalPrice.marketPrice),
                                    "marketRenewPrice":parseFloat(v.OriginalPrice.marketRenewPrice),
                                    "activityThresholdBuyTerm":v.ActivityThresholdBuyTerm,
                                    "activityThresholdCount":v.ActivityThresholdCount,
                                    "activityMarketPrice":v.ActivityMarketPrice,
                                    "activityPrice":v.ActivityPrice,
                                    "activityRenewMarketPrice":v.ActivityRenewMarketPrice,
                                    "activityRenewPrice":v.ActivityRenewPrice,
                                    "packageTitle":v.PackageTitle,
                                    "productTitle":v.ProductTitle,
                                    "specificationNumber":v.SpecificationCount,
                                    "specificationTitle":v.SpecificationTitle,
                                    "unit":v.SpecificationUnit,
                                    "userProductID":0
                                }
                            }
                        });

                        //域名续费权益
                        var chooseuserproduct = [];
                        var packSpecificationList2 = $(".packSpecificationList2").filter('input:checked');
                        $.each(packSpecificationList2,function (k2, v2) {
                            chooseuserproduct.push(parseInt($(v2).data('productspecificationsid')));
                        });

                        console.log(productinfo);
                        console.log(giftproduct);
                        console.log(giftproduct);
                        var tyunusercode=$('#tyunusercode').val();
                        var productclasstwovalues =  $("#productclasstwo").data('values');
                        var productclassonevalues =  $("#productclassone").data('values');
                        var productclasstwo = $(".product").data('productname');
                        var agents =  $("#instanceagents").data('value');
                        if(productclasstwovalues){
                            ispackage=1;
                        }else{
                            ispackage=0;
                        }
                        var params={
                            "servicecontractsid":servicecontractsid,
                            "servicecontractsid_display":servicecontractsid_display,
                            "accountid":accountid,
                            "accountid_display":accountid_display,
                            "mobile":mobile,
                            "mobilevcode":mobilevcode,
                            "classtype":classtype,
                            "categoryid":classtyperenew,
                            "type":1,
                            "buyyear":buyyear,
                            "buydate":buydate,
                            'packageid':oldproductnameid,
                            "tyunusercode":tyunusercode,
                            "servicetotal":servicetotal,
                            "tyunusercodeid":tyunusercodeid,
                            "buyproduct":buyproduct,
                            "tyunusercodetext":tyunusercodetext,
                            "oldcustomerid":oldaccountid,
                            "oldcustomername":oldaccountid_display,
                            "oldproductid":oproductid,
                            "oldproductname":oldproductname_display,
                            "activacode":activacode,

                            //新增下单参数
                            "activitychildid":activitychildid,
                            "giftproduct":giftproduct,
                            "productinfo":productinfo,
                            "agentIdentity":activityagent,
                            "activitytype":activitytype,
                            "activitymodel":activitymodel,
                            "activityid":activityid,
                            "activityenddate":activityenddate,
                            //另购
                            "otherproduct":otherproduct,
                            //另购产品
                            "separateproductname_display":separateproductname_display,
                            "activitytitle":activitytitle,
                            "combinationprice":combinationprice,
                            "meetactivity":true,
                            "activitytypetext":type_text,
                            "activityno":activityno,
                            "authenticationtype":authenticationtype,
                            "chooseuserproduct":chooseuserproduct,

                            "elereceivermobile":elereceivermobile,
                            "elereceiver":elereceiver,
                            "totalmarketprice":totalmarketprice,
                            "paycode":paycode,
                            "contractid":contractid,
                            "isverify":isverify,
                            "templateid":templateId,
                            "invoicecompany":invoicecompany,
                            "signaturetype":signaturetype,
                            "invoicecompanyid":invoicecompanyid,
                            "ispackage":ispackage,
                            "packagename":productclasstwo,

                            "productclassonevalues":productclassonevalues,
                            "productclasstwovalues":productclasstwovalues,

                            "agents":agents,
                            "eleccontracturl":eleccontracturl,

                        };

                        console.log(params);
                        $.ajax({
                                url: '/index.php?module=TyunWebBuyService&action=elecContractAddOrder',
                                type: 'POST',
                                dataType: 'json',
                                data:params,
                                beforeSend:function(){
                                    $.showLoading('订单处理中');
                                },
                                success: function (data) {
                                    $.hideLoading();
                                    if(data.success==1){
                                        $.alert("下单成功", function() {
                                            location.href=data.url;
                                            // location.href="/index.php?module=TyunWebBuyService&action=index";
                                        });
                                    }else{
                                        $.toast(data.msg,'text');
                                    }
                                }
                            }
                        );
                    }, function() {
                        //取消操作
                        return false;
                    });
                } else {
                    $.toast(data.msg, 'text');
                }
            }
        });
    });

    function isMoreCategory() {
        var num = 0;
        $(".product").each(function (k, v) {
            if($(v).data('packageid')){
                num = num*1+1;
            }
        });
        $(".giveproductlistall").each(function (k, v) {
            if($(v).data('packageid')){
                num = num*1+1;
            }
        });
        if(num>1){
            return true;
        }
        return false;
    }

});
