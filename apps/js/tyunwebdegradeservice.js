/**
 * Created by jf on 2015/9/11.
 * Modified by bear on 2016/9/7.
 */
$(function () {
    var currentProduct={};
    var pageManager = {
        $container: $('#container'),
        _pageStack: [],
        _configs: [],
        _replacedata:[],
        _pageAppend: function(){},
        _defaultPage: null,
        _pageIndex: 1,
        setDefault: function (defaultPage) {
            this._defaultPage = this._find('name', defaultPage);
            return this;
        },
        setPageAppend: function (pageAppend) {
            this._pageAppend = pageAppend;
            return this;
        },
        init: function () {
            var self = this;

            $(window).on('hashchange', function () {
                var state = history.state || {};
                var url = location.hash.indexOf('#') === 0 ? location.hash : '#';
                var page = self._find('url', url) || self._defaultPage;
                if (state._pageIndex <= self._pageIndex || self._findInStack(url)) {
                    self._back(page);
                } else {
                    self._go(page);
                }
            });

            if (history.state && history.state._pageIndex) {
                this._pageIndex = history.state._pageIndex;
            }

            this._pageIndex--;

            var url = location.hash.indexOf('#') === 0 ? location.hash : '#';
            var page = self._find('url', url) || self._defaultPage;
            this._go(page);
            return this;
        },
        push: function (config) {
            this._configs.push(config);
            return this;
        },
        go: function (to) {
            var config = this._find('name', to);
            if (!config) {
                return;
            }
            location.hash = config.url;
        },
        _go: function (config) {
            this._pageIndex ++;
            history.replaceState && history.replaceState({_pageIndex: this._pageIndex}, '', location.href);
            var html = $(config.template).html();
            var $html = $(html).addClass('slideIn').addClass(config.name);
            $html.on('animationend webkitAnimationEnd', function(){
                $html.removeClass('slideIn').addClass('js_show');
            });
            this.$container.append($html);
            this._pageAppend.call(this, $html);
            this._pageStack.push({
                config: config,
                dom: $html
            });

            if (!config.isBind) {
                this._bind(config);
            }

            return this;
        },
        back: function () {
            history.back();
        },
        _back: function (config) {
            this._pageIndex --;
            var stack = this._pageStack.pop();
            if (!stack) {
                return;
            }
            var url = location.hash.indexOf('#') === 0 ? location.hash : '#';
            var found = this._findInStack(url);
            if (!found) {
                var html = $(config.template).html();
                var $html = $(html).addClass('js_show').addClass(config.name);
                $html.insertBefore(stack.dom[0]);

                if (!config.isBind) {
                    this._bind(config);
                }
                this._pageStack.push({
                    config: config,
                    dom: $html
                });
            }

            stack.dom.addClass('slideOut').on('animationend webkitAnimationEnd', function () {
                stack.dom.remove();
            });

            return this;
        },
        _findInStack: function (url) {
            var found = null;
            for(var i = 0, len = this._pageStack.length; i < len; i++){
                var stack = this._pageStack[i];
                if (stack.config.url === url) {
                    found = stack;
                    break;
                }
            }
            return found;
        },
        _find: function (key, value) {
            var page = null;
            for (var i = 0, len = this._configs.length; i < len; i++) {
                if (this._configs[i][key] === value) {
                    page = this._configs[i];
                    break;
                }
            }
            return page;
        },
        _bind: function (page) {
            var events = page.events || {};
            for (var t in events) {
                for (var type in events[t]) {
                    this.$container.on(type, t, events[t][type]);
                }
            }
            page.isBind = true;
        }
    };

    function fastClick(){
        var supportTouch = function(){
            try {
                document.createEvent("TouchEvent");
                return true;
            } catch (e) {
                return false;
            }
        }();
        var _old$On = $.fn.on;

        $.fn.on = function(){
            if(/click/.test(arguments[0]) && typeof arguments[1] == 'function' && supportTouch){ // 只扩展支持touch的当前元素的click事件
                var touchStartY, callback = arguments[1];
                _old$On.apply(this, ['touchstart', function(e){
                    touchStartY = e.changedTouches[0].clientY;
                }]);
                _old$On.apply(this, ['touchend', function(e){
                    if (Math.abs(e.changedTouches[0].clientY - touchStartY) > 10) return;

                    e.preventDefault();
                    callback.apply(this, [e]);
                }]);
            }else{
                _old$On.apply(this, arguments);
            }
            return this;
        };
    }
    function androidInputBugFix(){
        // .container 设置了 overflow 属性, 导致 Android 手机下输入框获取焦点时, 输入法挡住输入框的 bug
        // 相关 issue: https://github.com/weui/weui/issues/15
        // 解决方法:
        // 0. .container 去掉 overflow 属性, 但此 demo 下会引发别的问题
        // 1. 参考 http://stackoverflow.com/questions/23757345/android-does-not-correctly-scroll-on-input-focus-if-not-body-element
        //    Android 手机下, input 或 textarea 元素聚焦时, 主动滚一把
        if (/Android/gi.test(navigator.userAgent)) {
            window.addEventListener('resize', function () {
                if (document.activeElement.tagName == 'INPUT' || document.activeElement.tagName == 'TEXTAREA') {
                    window.setTimeout(function () {
                        document.activeElement.scrollIntoViewIfNeeded();
                    }, 0);
                }
            })
        }
    }

    if (navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
        $('input, textarea').on('focus', function() {
            $('input, textarea').not(this).attr("readonly", "readonly");
            $('input, textarea').not(this).attr("unselectable", "on");
            $('input, textarea').not(this).on("fοcus",function(){$(this).blur();});
        })
        $('input, textarea').on('blur', function() {
            $('input, textarea').not('.readonlyflag').removeAttr("readonly");
            $('input, textarea').not('.readonlyflag').removeAttr("unselectable");
            $('input, textarea').not('.readonlyflag').off("fοcus");
        })
        $('select').attr('tabindex', '-1')
    }
    function setPageManager(){
        var pages = {}, tpls = $('script[type="text/html"]');
        var winH = $(window).height();
        for (var i = 0, len = tpls.length; i < len; ++i) {
            var tpl = tpls[i], name = tpl.id.replace(/tpl_/, '');
            pages[name] = {
                name: name,
                url: '#' + name,
                template: '#' + tpl.id
            };
        }
        pages.home.url = '#';

        for (var page in pages) {
            pageManager.push(pages[page]);
        }
        pageManager
            .setPageAppend(function($html){
                var $foot = $html.find('.page__ft');
                if($foot.length < 1) return;

                if($foot.position().top + $foot.height() < winH){
                    $foot.addClass('j_bottom');
                }else{
                    $foot.removeClass('j_bottom');
                }
            })
            .setDefault('home')
            .init();
    }

    function init(){
        fastClick();
        setPageManager();
        window.pageManager = pageManager;
        window.home = function(){
            location.hash = '';
        };
    }
    init();
    $('#customerstype').change(function () {
        if($(this).val()=='clientmigration'){
            window.location.href="/index.php?module=TyunWebBuyServiceClient&action=degrade";
        }else{
            window.location.href="/index.php?module=TyunWebBuyService&action=degrade";
        }
    });
    $('#customerstype').change(function () {
        if($(this).val()=='clientmigration'){
            window.location.href="/index.php?module=TyunWebBuyServiceClient&action=degrade";
        }else{
            window.location.href="/index.php?module=TyunWebBuyService&action=degrade";
        }
    });
    $('#container').on('click','.changesearchdata',function(){
        var tempid=$('#tempid').attr('data-value');
        if(tempid=='servicecontractsid'){
            $('input[name="servicecontractsid"]').val($(this).data('id'));
            $('input[name="servicecontractsid_display"]').val($(this).data('narr'));
            $('#contractowenid').val($(this).data('userid'));
        }else if(tempid=='accountid'){
            $('input[name="accountid"]').val($(this).data('id'));
            $('input[name="accountid_display"]').val($(this).data('narr'));
            $('#accountidowenid').val($(this).data('userid'));
        }
        $('#nextstep').css({"background-color":'#999999'});
        $('#tyunusercodeonoff').val(1);
        history.back();
    });
    $('#container').on('click', '#searchCancel',function(){
        if($('#searchInput').val()==''){
            return false;
        }
        var tempid=$('#tempid').attr('data-value');
        if(tempid=='servicecontractsid'){
            var customerid=$('#accountidowenid').val();
        }else if(tempid=='accountid'){
            var customerid=$('#contractowenid').val();
        }else{
            history.back();
            return false;
        }
        var classtype=$('select[name="classtype"]').val();
        $.showLoading();
        var searchInput=$('#searchInput').val();
        $('#searchResult').empty();
        $.getJSON('/index.php?module=TyunWebBuyService&action=searchTyunBuyServiceContract&contract_no='+searchInput+'&customerid='+customerid+'&tempid='+tempid+'&classtype='+classtype,function(data){
            $.hideLoading();
            if (data && data.length > 0) {
                for (var i = 0,len=data.length;i<len; i++) {
                    var item2=data[i];
                    var nArr = item2.mname;
                    var nid = item2.mid;
                    var username = item2.username;
                    var userid = item2.userid;
                    $('#searchResult').append('<div class="weui-cell weui-cell_access changesearchdata" data-id="'+nid+'" data-narr="'+nArr+'" data-username="'+username+'" data-userid="'+userid+'">\
                                    <div class="weui-cell__bd weui-cell_primary">\
                                    <p>'+nArr+'</p>\
                                    </div>\
                                    </div>');
                }
            }else{
                $.toast('没有找到相关信息','text');
            }
        });
        return false;
    });
    var countdown=60;
    function settime(obj){
        if(countdown == 60){
            countdown = 59;
            var mobile = $("#mobile").val();
            obj.setAttribute("disabled", true);
            $('#checkmobile').val(mobile);
            sendMobileVerify(mobile);
        }
        if(countdown == 0){
            obj.removeAttribute("disabled");
            obj.innerHTML="获取验证码";
            countdown = 60;
            return false;
        }else{
            obj.setAttribute("disabled", true);
            obj.innerHTML ="重新发送(" + countdown + "s)";
            countdown--;
        }
        setTimeout(function() {
            settime(obj) },1000);
    }
    $('#container').on('click','#mobilevcodebtn',function(){
        var mobile = $("#mobile").val();
        if(mobile == ''){
            $.toast("手机号码必填", "forbidden");
            return false;
        }
        var reg = /^(((13[0-9]{1})|(14[0-9]{1})|(15[0-9]{1})|(16[0-9]{1})|(19[0-9]{1})|(17[0-9]{1})|(18[0-9]{1}))+\d{8})$/;
        if (!reg.test(mobile)) {
            $.toast("手机号码无效", "forbidden");
            return false;
        }
        var accountid=$('#accountid').val();
        if(accountid==0){
            $.toast("客户必选", "forbidden");
            return false;
        }
        settime(this);
    });
    function sendMobileVerify(mobile){
        $.getJSON('/index.php?module=TyunWebBuyService&action=getMobileVerify&mobile='+mobile,function(res){
            if(res.success==1){
                $.toast('验证码已发送','text');
            }else{
                $.toast(res.msg,'text');
            }
            $('#nextstep').css({"background-color":'#999999'});
            // $('#tyunusercodeonoff').val(1);
        });
    }
    $('#container').on("blur",'#classtype,#tyunusercode,input[name="accountid_display"],input[name="servicecontractsid_display"],#mobile,#mobilevcode,#classtyperenew,#servicetotal,#buyyear,#buyproduct,#oldproductname_display',function(event){
        window.scroll(0,0);
    });
    $('#container').on('click','#clickhide',function(event){
        var thisInstance=this;
        $("#tyunusercode")[0].options.length=0;
        var servicecontractsid=$('input[name="servicecontractsid"]').val();
        var servicecontractsid_display=$('input[name="servicecontractsid_display"]').val();
        var accountid=$('input[name="accountid"]').val();
        var accountid_display=$('input[name="accountid_display"]').val();
        var mobile=$('#mobile').val();
        var mobilevcode=$('#mobilevcode').val();
        var classtype=$('#classtype').val();
        var agents=$('#instanceagents').data('value');
        var signaturetype = $("#signaturetype").val();
        if(checkGetUserCode()){
            return false;
        }
        $.ajax({
            url: '/index.php?module=TyunWebBuyService&action=getTyunUserCode',
            type: 'POST',
            dataType: 'json',
            data:{"servicecontractsid":servicecontractsid,
                "servicecontractsid_display":servicecontractsid_display,
                "accountid":accountid,
                "accountid_display":accountid_display,
                "mobile":mobile,
                "mobilevcode":mobilevcode,
                "classtype":classtype,
                "agents": agents,
                "signaturetype":signaturetype
            },
            beforeSend:function(){
                $.showLoading();
            },
            success: function (data) {
                $.hideLoading();
                if (data.success==1) {
                    $.each(data.data,function(key,value){
                        var selected='';
                        if(key==0){
                            selected='selected';
                        }
                        $("#tyunusercode").append('<option value="'+value.id+'" '+selected+'>'+value.loginName+'</option>');
                    });
                    getAllCategory();
                    $(thisInstance).hide();
                }else{
                    $.toast(data.msg,'text');
                }
            }
        });
        event.stopPropagation();
        return false;
    });

    $('#container').on('click','.preproduct',function(event){
        var productid = $(this).data('preproductid');
        console.log(productid);
        $('#preproductid').attr('data-value',productid);

        var configureproductid = $(this).data('configureproductid');
        $("#configureproductid").attr('data-value',configureproductid);

        window.pageManager.go('selectpreproduct');
    });

    $('#container').on('change','#tyunusercode',function(event){
        if(!$(this).find("option:checked").hasClass('tyuncode_option')){
            $("#oldaccountid").val("");
            $("#oldaccountidowenid").val("");
            $("input[name=oldaccountid_display]").val("");
            $(".tyuncode_option").remove();
        }
        $('#checkTyunExistBuyReturn').attr('data-value',0);
        getUserRenewProductInfo();
    });
    function getAllCategory(){
        $("#classtyperenew")[0].options.length=0;
        $.ajax({
                url: '/index.php?module=TyunWebBuyService&action=getAllCategory',
                type: 'POST',
                dataType: 'json',
                data:{
                    "categoryID":'1'
                },
                beforeSend:function(){
                    $.showLoading();
                },
                success: function (data) {
                    $.hideLoading();
                    if(data.success && data.data.length>0){
                        var datas=data.data;
                        $.each(datas,function(key,value){
                            if(value.IsPackage){
                                if(value.ID==7){
                                    return true;
                                }
                                $("#classtyperenew").append('<option value="'+value.ID+'">'+value.Title+'</option>');
                            }
                        });
                        getUserRenewProductInfo();
                    }
                }
            }
        );
    }
    function getUserRenewProductInfo(){
        var tempclasstype=$('#tempclasstype').val();
        var values=$('#classtyperenew').val();
        var tyunusercode=$('#tyunusercode').val();
        var classtype=$('#classtype').val();
        var tyunusername=$('#tyunusercode option:checked').text();
        if(tyunusercode==''){
            return false;
        }
        $('#oldproductname_display').val('');
        $('#oldcontractcode_display').val('');
        $('#oldexpiredate_display').val('');
        $('#lastupgarde').val('');
        $('#lastupgardes').html('');
        $('#oldproductnameid').val('');
        $('#tempclasstype').val(values);
        $.ajax({
            url: '/index.php?module=TyunWebBuyService&action=getAllProducts',
            type: 'POST',
            dataType: 'json',
            data: {
                "tyunusercode": tyunusercode,
                "classtyperenew": values,
                "tyunusername": tyunusername,
                "classtype": "degrade",
                "type": 0
            },
            beforeSend: function () {
                $.showLoading();
            },
            success: function (data) {
                $.hideLoading();
                if (data.code == 200) {
                    var data2 = data.data;
                    window.packageSpecificationList = data2[0].degradePackageList;
                    $('#oldproductname_display').val(data2[0].Title);
                    $('#oldexpiredate_display').val(data2[0].CloseDate);
                    if (data2[0].degradePackageList != []) {
                        // $('#nextstep').css({"background-color": '#0B92D9'});
                        $('#tyunusercodeonoff').val(2);
                    }
                    $('#oldproductnameid').val(data2[0].ID);
                    var contractname = data.contract.contractname == undefined ? '' : data.contract.contractname;
                    if(contractname){
                        $('#oldcontractcode_display').val(contractname);
                        var updateinfo=data.productList.latelyadd!=''?data.productList.latelyadd:'最近没有降级';
                        /*var updateinfo = data.contract.updateinfo == undefined ? '最近没有降级' : data.contract.updateinfo;*/
                        $('#lastupgarde').val(updateinfo);
                        $('#lastupgardes').html(updateinfo);
                    }
                    var productList = data.productList;
                    $('#oproductid').attr('data-value',productList.productid);
                    $('#activacode').attr('data-value',productList.activecode);
                    initNextStepButton();
                } else {
                    window.packageSpecificationList=false;
                    initNextStepButton();
                    $.toast(data.message, 'forbidden');
                }
            }
        });
    }
    function getRenewInterest(buyproductid){
        var values=$('#classtyperenew').val();
        var tyunusercode=$('#tyunusercode').val();
        var tyunusername=$('#tyunusercode option:checked').text();
        if(tyunusercode==''){
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
                "buyproductid":buyproductid
            },
            success: function (data) {
                if (data.code == 200) {
                    var data2 = data.data;
                    window.domains=data2.domains;
                    window.domaininpackage = data2.domaininpackage;
                    GetUserPackageDegradeInfoData()
                }
            }
        });
    }
    $('#classtyperenew').attr('data-last',$('#classtyperenew').val()).change(function(event){
        var lastvalue=$(this).attr('data-last');
        var currentvalue=$(this).val();
        if(lastvalue!=currentvalue){
            $('#tempclasstype').val(currentvalue);
            getUserRenewProductInfo();
        }
    });

    $('#container').on('click','#nextstep',function(){
        var mobile = $("#mobile").val();
        $("#lastelereceivermobile").attr('data-value', mobile);
        if(packageSpecificationList==false){
            $.toast('没有可降级的套餐','text');
            return false;
        }
        var signaturetype = $("#signaturetype").val();
        if(signaturetype=='papercontract'){
            var isService = $("input[name='isService']").val();
            if(!isService){
                if($('#contractowenid').val()!=$('#accountidowenid').val()){
                    var accountidowenname=$('#accountidowenname').val();
                    var contractowenname=$('#contractowenname').val();
                    $.toast("客户负责人:"+accountidowenname+"<br>合同领取人:"+contractowenname+"<br>客户负责人和合同领取人必须一致","text");
                    return false;
                    return false;
                }
            }
            var checkVerifyCodeVal=$('#checkVerifyCode').attr('data-value');
            if(checkVerifyCodeVal!=1){
                checkVerifyCode();
                return false;
            }
        }else{
            var contactname = $("#contactname").val();
            var elereceivermobile = $("#elereceivermobile").val();
            var ismobile = false;
            if (elereceivermobile.match(/^((1[3-9])+\d{9})$/)) {
                ismobile=true;
            }
            if(!elereceivermobile || !contactname || !ismobile){
                return false;
            }
        }


        if ($('#tyunusercode').val() == '') {
            return false;
        }
        var checkTyunExistBuyReturn = $('#checkTyunExistBuyReturn').attr('data-value');
        if (checkTyunExistBuyReturn != 1) {
            checkTyunExistBuy();
            return false;
        }
        window.selectproducttitle=$('input[name="accountid_display"]').val();
        sessionStorage.accountid_display=$('input[name="accountid_display"]').val();
        var tyunusercodeonoff=$('#tyunusercodeonoff').val();
        if(tyunusercodeonoff==1){
            return false;
        }else{
            if(checkGetUserCode()){
                return false;
            }
            window.selectproducttitle=$('input[name="accountid_display"]').val();
            pageManager.go('selectproduct');
        }
    });
    function checkGetUserCode(){
        var servicecontractsid=$('input[name="servicecontractsid"]').val();
        var accountid=$('input[name="accountid"]').val();
        var checkmobilevcode=$('#checkmobilevcode').val();
        var checkmobile=$('#checkmobile').val();
        var mobile=$('#mobile').val();
        var mobilevcode=$('#mobilevcode').val();
        var signaturetype = $("#signaturetype").val();
        var msg='';
        var checkflag=true;
        do{
            if(accountid<=0){
                msg='请选择客户名称';
                break;
            }
            if(mobile==''){
                msg='请填写要验证的手机号码';
                break;
            }
            if (signaturetype == 'papercontract') {
                 if(servicecontractsid<=0){
                     msg='请选择合同编号';
                     break;
                 }
                 if(mobilevcode==''){
                     msg='请填写验证码';
                     break;
                 }
                 if(checkmobile==''){
                     msg='验证码无效，请重新获取';
                     break;
                 }
                 if(mobile!=checkmobile){
                     msg='验证码失效，请重新获取';
                     break;
                 }
            }
            checkflag=false;
        }while(0);
        if(checkflag){
            $('#nextstep').css({"background-color":'#999999'});
            $('#tyunusercodeonoff').val(1);
            $('#clickhide').show();
            $("#tyunusercode")[0].options.length=0;
            $.toast(msg,'forbidden');
            return true;
        }
        return false;
    }
    function checkTyunExistBuy(){
        // var tyunusercode = $('#tyunusercode').val();
        var tyunusercode=$('#tyunusercode').find("option:checked").text();
        $.ajax({
            url: '/index.php?module=TyunWebBuyService&action=checkTyunExistBuyReturn',
            type: 'POST',
            dataType: 'json',
            data:{"tyunusercode":tyunusercode},
            beforeSend:function(){
                $.showLoading('正在验证账号');
            },
            success: function (data) {
                $.hideLoading();
                if (data.success==1) {
                    $('#checkTyunExistBuyReturn').attr('data-value',1);
                    $('#nextstep').trigger('click');
                }else{
                    $.toast(data.msg,"text");
                }
            }
        });
    }
    $('#container').on('input','input[name="servicecontractsid_display"],input[name="accountid_display"]',function(){
        var dataname=$(this).data('name');
        if(dataname=='servicecontractsid'){
            $('#contractowenid').val(0);
            $('#servicecontractsid').val(0);
        }else if(dataname=='accountid'){
            var customerid=$('#accountidowenid').val(0);
            $('#accountid').val(0);
        }
        $("#tyunusercode")[0].options.length=0;
        $('#clickhide').show();
        $('#servicecontractsid_downcontent').empty();
        $('#accountid_downcontent').empty();
        $("input[name=oldaccountid_display]").val("");
        $("#oldaccountid").val(0);
        $("#oldaccountidowenid").val(0);
        /*电子合同start*/
        $("#contactname").empty();
        $("#contactnameclickhide").show();
        $("#elereceivermobile").val("");
        /*电子合同end*/
        initNextStepButton();
    });
    $('#container').on('click','.search_list',function(){
        var dataname=$(this).data('name');
        var searchInput=$('input[name="'+dataname+'_display"]').val();
        if(searchInput==''){
            return false;
        }
        if(dataname=='servicecontractsid'){
            var customerid=$('#accountidowenid').val();
        }else if(dataname=='accountid'){
            var customerid=$('#contractowenid').val();
        }
        var classtype=$('select[name="classtype"]').val();
        $.showLoading();
        var oli=$('#'+dataname+'_downcontent');
        oli.empty();
        $.getJSON('/index.php?module=TyunWebBuyService&action=searchTyunBuyServiceContract&contract_no='+searchInput+'&customerid='+customerid+'&tempid='+dataname+'&classtype='+classtype,function(data){
            $.hideLoading();
            if (data) {
                $('#'+dataname+'_downlist').show();
                $.each(data,function(key,item2){
                    var nArr = item2.mname;
                    var nid = item2.mid;
                    var username = item2.username;
                    var userid = item2.userid;
                    oli.append('<a class="weui-cell weui-cell_access accountlist" data-field="'+dataname+'" data-id="'+nid+'" data-name="'+nArr+'" data-userid="'+userid+'" data-username="'+username+'" href="javascript:;">'+
                        '<div class="weui-cell__bd weui-cell_primary">'+
                        '<p>'+nArr+'</p>'+
                        '</div>'+
                        '<span class="weui-cell__ft"></span>'+
                        '</a>');
                });
            }else{
                $.toast('没有找到相关信息','text');
                $('input[name="'+dataname+'"]').val('');
                $('input[name="'+dataname+'_display"]').val('');
                if(dataname=='servicecontractsid'){
                    $('#contractowenid').val('');
                }else if(dataname=='accountid'){
                    $('#accountidowenid').val('');
                }
            }
        });
    });
    $('#container').on('click','.accountlist',function(e){
        var datafield=$(this).data('field');
        $('input[name="'+datafield+'"]').val($(this).data('id'));
        $('input[name="'+datafield+'_display"]').val($(this).data('name'));
        if(datafield=='servicecontractsid'){
            $('#contractowenid').val($(this).data('userid'));
            $('#contractowenname').val($(this).data('username'));
        }else if(datafield=='accountid'){
            $('#accountidowenid').val($(this).data('userid'));
            $('#accountidowenname').val($(this).data('username'));
        }
        $('#'+datafield+'_downlist').hide();

    });
    $('#container').on('change','#classtype',function(){
        if($(this).val()=='renew'){
            location.href="/index.php?module=TyunWebBuyService&action=renew";
        }
        if($(this).val()=='upgrade'){
            location.href="/index.php?module=TyunWebBuyService&action=upgrade";
        }
        if($(this).val()=='buy'){
            location.href="/index.php?module=TyunWebBuyService&action=index";
        }
    });


    $('#container').on('click','#submitfrom',function(){
        var submitid=$('#submitid').attr('data-value');
        if(submitid!=2){
            return false;
        }
        if(!specialOtherProduct()){
            $.toast('请输入另购单品的合同金额',"text");
            return false;
        }
        var servicetotal=parseFloat($('#servicetotal').val()).toFixed(2);

        if(totalSpecialOtherProductPrice()>servicetotal){
            $.toast('另购单品合同金额不能高于总合同金额',"text");
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
        var tyunusercode=$('#tyunusercode').val();//用户ID
        var tyunusercodename=$('#tyunusercode').find("option:checked").text();//用户名
        var classtyperenew=$('#classtyperenew').val();//产品分类
        var classtyperenewname=$('#classtyperenew').find("option:checked").text();//产品分类名称
        var oldproductnameid=$('#oldproductnameid').val();//原版本ID
        var oldproductname_display=$('#oldproductname_display').val();//原版本
        var buyproduct=$('#buyproduct').val();//降级版本ID
        var buyproductname=$('#buyproduct').find("option:checked").text();//降级版本名称
        var upgardecycle=$('#upgardecycle').val();//降级周期
        var currentdate=(new Date()).getFullYear()+'-'+((new Date()).getMonth()+1)+'-'+(new Date()).getDate();
        var separateproductname_display = '';
        var Price=$('#Price').val();//差价合计
        var buyyear=$('#buyyear').val();//降级年限
        buyyear=buyyear>0?buyyear:0;
        var servicetotal=parseFloat($('#servicetotal').val()).toFixed(2);//合同金额
        var agents=$('#instanceagents').data('value');
        var activacode = $("#activacode").data('value');
        var oproductid = $("#oproductid").data('value');
        var buydate=$('#buydate').val();
        if($('#buydateshow').data('value')!=1){
            var buydatestr='';
        }else{
            if(buydate==''){
                $.toast('购买日期必选',"text");
                return false;
            }
            var buydatestr=$('#buydateshow').data('value')!=1?'':'<div class="weui-form-preview__item">' +
                '<label class="weui-form-preview__label">降级时间</label>' +
                '<span class="weui-form-preview__value">'+buydate+'</span>' +
                '</div>'
        }
        //另购服务start
        var productid=[];
        var categoryid=[];
        var number=[];
        var id=[];
        var price=[];
        var renewprice=[];
        var marketprice=[];
        var marketrenewprice=[];
        var unit=[];
        var specificationstitle=[];
        var producttitle=[];
        var othercontractprice = [];
        $.each($('.select3check'),function(keyt,valuet){
            producttitle[keyt]=$(valuet).data('producttitle');
            productid[keyt]=$(valuet).data('productid');
            categoryid[keyt]=$(valuet).data('categoryid');
            number[keyt]=$(valuet).data('number');
            id[keyt]=$(valuet).data('id');
            price[keyt]=$(valuet).data('price');
            renewprice[keyt]=$(valuet).data('renewprice');
            marketprice[keyt]=$(valuet).data('marketprice');
            marketrenewprice[keyt]=$(valuet).data('marketrenewprice');
            unit[keyt]=$(valuet).data('unit');
            specificationstitle[keyt]=$(valuet).data('specificationstitle');
            var contractprice = $('.othercontractprice'+$(valuet).data('productid')).val();
            othercontractprice[keyt] = (contractprice==undefined ? 0:parseFloat(contractprice));
            separateproductname_display += $(valuet).data('producttitle')+'('+$(valuet).data('number')+') ';
        });
        //另购服务end

        //域名续费权益
        var chooseuserproduct = [];
        var packSpecificationList2 = $(".packSpecificationList2").filter('input:checked');
        $.each(packSpecificationList2,function (k2, v2) {
            chooseuserproduct.push(parseInt($(v2).data('productspecificationsid')));
        });


        var str='<div class="weui-form-preview">' +
            '<div class="weui-form-preview__bd">' +
            '<div class="weui-form-preview__item">' +
            '<label class="weui-form-preview__label">T云账号</label>' +
            '<span class="weui-form-preview__value">'+tyunusercodename+'</span>' +
            '</div>' +
            '<div class="weui-form-preview__item">' +
            '<label class="weui-form-preview__label">原版本</label>' +
            '<span class="weui-form-preview__value">'+oldproductname_display+'</span>' +
            '</div>' +
            '<div class="weui-form-preview__item">' +
            '<label class="weui-form-preview__label">降级版本</label>' +
            '<span class="weui-form-preview__value">'+buyproductname+'</span>' +
            '</div>' +
            '<div class="weui-form-preview__item">' +
            '<label class="weui-form-preview__label">另购单品</label>' +
            '<span class="weui-form-preview__value">'+separateproductname_display+'</span>' +
            '</div>' +
            '<div class="weui-form-preview__item">' +
            '<label class="weui-form-preview__label">合同金额</label>' +
            '<span class="weui-form-preview__value">￥'+servicetotal+'</span>' +
            '</div>' +
            '<div class="weui-form-preview__item">' +
            '<label class="weui-form-preview__label">降级年限</label>' +
            '<span class="weui-form-preview__value">'+buyyear+'年</span>' +
            '</div>' +
            '<div class="weui-form-preview__item">' +
            '<label class="weui-form-preview__label">降级周期</label>' +
            '<span class="weui-form-preview__value">'+upgardecycle+'</span>' +
            '</div>' +
            '<div class="weui-form-preview__item">' +
            '<label class="weui-form-preview__label">降级合同</label>' +
            '<span class="weui-form-preview__value">'+servicecontractsid_display+'</span>' +
            '</div>' +
            '<div class="weui-form-preview__item">' +
            '<label class="weui-form-preview__label">客户名称</label>' +
            '<span class="weui-form-preview__value">'+accountid_display+'</span>' +
            '</div>' +
            '<div class="weui-form-preview__item">' +
            '<label class="weui-form-preview__label">老客户名称</label>' +
            '<span class="weui-form-preview__value">'+oldaccountid_display+'</span>' +
            '</div>' +
            // '<div class="weui-form-preview__item">' +
            // '<label class="weui-form-preview__label">降级时间</label>' +
            // '<span class="weui-form-preview__value">'+currentdate+'</span>' +
            // '</div>' +
            '</div>' +
            '</div>';
        var $stru=''
        var groupid=[];
        var title=[];
        var specificationid=[];
        var specificationtitle=[];
        $.each($('.tobeconfiguredproducts'),function(keyt,valuet){
            if($(valuet).attr('checked')){
                title[keyt]=$(valuet).data('title');
                specificationid[keyt]=$(valuet).data('id');
                groupid[keyt]=$(valuet).data('groupid');
                specificationtitle[keyt]=$(valuet).data('specificationtitle');
            }

        });

        var buyproductss = $('#buyproduct').find("option:checked");
        packageprice = buyproductss.data('price');
        packagerenewprice = buyproductss.data('renewprice');
        packagemarketprice = buyproductss.data('marketprice');
        packagemarketrenewprice = buyproductss.data('marketrenewprice');
        var authenticationtype = $("#authenticationtype").val();


        var params={"servicecontractsid":servicecontractsid,
            "servicecontractsid_display":servicecontractsid_display,
            "accountid":accountid,
            "oldaccountid":oldaccountid,
            "accountid_display":accountid_display,
            "mobile":mobile,
            "mobilevcode":mobilevcode,
            "classtype":classtype,
            "type":1,
            "buyyear":buyyear,
            "agents":agents,
            "servicetotal":servicetotal,
            "tyunusercode":tyunusercode,
            "tyunusercodetext":tyunusercodename,
            "tyunusercodeid":tyunusercode,
            "oldcustomerid":oldaccountid,
            "oldcustomername":oldaccountid_display,
            "oldproductid":oproductid,
            "oldproductname":oldproductname_display,
            "activacode":activacode,
            "productclassonevalues":classtyperenew,
            "productclasstwovalues":oldproductnameid,
            "chooseuserproduct":chooseuserproduct,
            "packageprice":packageprice,
            "packagerenewprice":packagerenewprice,
            "packagemarketprice":packagemarketprice,
            "packagemarketrenewprice":packagemarketrenewprice,
            "activitymodel":3,
            "authenticationtype":authenticationtype,
            "buydate":buydate
        };
        params['groupid']=groupid;
        params['buyproduct']=buyproduct;
        params['title']=title;
        params['categoryid']=classtyperenew;
        params['classtyperenewname']=classtyperenewname;
        params['oldproductnameid']=oldproductnameid;
        params['oldproductname_display']=oldproductname_display;
        params['specificationid']=specificationid;
        params['specificationtitle']=specificationtitle;
        params['price']=Price;
        params['servicetotal']=servicetotal;

        //另购服务start
        params['producttitle']=producttitle;
        params['productid']=productid;
        params['categoryids']=categoryid;
        params['number']=number;
        params['id']=id;
        params['price']=price;
        params['renewprice']=renewprice;
        params['marketprice']=marketprice;
        params['marketrenewprice']=marketrenewprice;
        params['unit']=unit;
        params['specificationstitle']=specificationstitle;
        params['othercontractprice']=othercontractprice;
        //另购服务end


        $.confirm(str, "请核对订单信息", function() {
            $.ajax({
                    url: '/index.php?module=TyunWebBuyService&action=order',
                    type: 'POST',
                    dataType: 'json',
                    data:params,
                    beforeSend:function(){
                        $.showLoading('订单处理中');
                    },
                    success: function (data) {
                        $.hideLoading();
                        if(data.success==1){
                            $.alert(data.msg, function() {
                                location.href=data.url;
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
    $('#container').on('change','#servicetotal',function(){
        checkProductValue(1);
    });
    $('#container').on('input','#servicetotal',function(){
        checkProductValue(1);
    });
    window.selectproductLoad=function(){
        packageSpecificationList=packageSpecificationList||{};
        $('#buyproduct')[0].options.length=0;
        $('#buyproduct').append('<option value="0">请选择降级版本</option>');
        $('#buyproduct').attr('data-lastvalue',0);
        $.each(packageSpecificationList,function(key,value){
            if(value.ID==79){
                return true;
            }
            $('#buyproduct').append('<option value="'+value.ID+'" data-marketrenewprice="'+value.marketRenewPrice+'" data-marketprice="'+value.marketPrice+'" data-renewprice="'+value.packageRenewalPrice+'" data-packageprice="'+value.packagePrice+'" data-packagerenewalprice="'+value.packageRenewalPrice+'">'+value.Title+'</option>');
        });
        //电子合同start
        var signaturetype = $("#signaturetype").val();
        $("#formbutton").empty();
        if (signaturetype == 'eleccontract') {
            str = '<a class="weui-btn" id="electronnextstep" href="javascript:void(0);" style="background-color: #999999;color:#FFFFFF;font-size:18px;width:98%;">下一步</a>';
        } else {
            str = '<a class="weui-btn" id="submitfrom" href="javascript:void(0);" style="background-color: #999999;color:#FFFFFF;font-size:18px;width:98%;">提交订单</a>';
        }
        $("#formbutton").append(str);
        //电子合同end
    };



    window.selectpreproductLoad=function() {
        var buyproductid = $('#buyproduct').val();
        var year = $('#buyyear').val();
        var preproductid = $("#preproductid").data('value');
        console.log(preproductid);
        var selectproductid = 'selectproductid'+preproductid;
        if (buyproductid > 0 && year > 0) {
            var configureproductid = $("#configureproductid").data('value');
            if(!isDomainExist && domains && domains.length>1 && domaininpackage && configureproductid==52){
                $("#preproduct").empty();
                var tobeConfiguredProductsstr = '';
                tobeConfiguredProductsstr += '<div class="weui-panel__hd" style="padding-top:5px;padding-bottom: 5px;">' +
                    '    <div class="weui-cell__hd weui-cell__ft__add">域名(可选1个)' + '</div>' +
                    '       <input id="max_select_num" type="hidden" value="1">'+
                    '    <div class="weui-cell__ft weui-cell__ft__add"></div>' +
                    '</div>';
                var userproductstr = '<div class="weui-panel__bd">';
                $.each(domains, function (key3, value3) {
                    var productInfo = (value3.ProductSpecifications)[0];
                    userproductstr += '<a href="javascript:void(0);" class="weui-media-box weui-media-box_appmsg" style="padding-top:0px;padding-bottom: 0px;">' +
                        '    <div class="weui-media-box__bd">' +
                        '        <div class="weui-cells_checkbox">' +
                        '            <label class="weui-cell weui-check__label" for="cc' + productInfo.ID + '">' +
                        '                <div class="weui-cell__hd">' +
                        '                    <input type="checkbox" class="weui-check tobeconfiguredproducts2" name="checkbox" data-closedate="'+productInfo.CloseDate+'" data-grouptitle="' + value3.GroupName + '" data-groupid="' + value3.PackageID + '" id="cc' + productInfo.ID + '" data-id="' + productInfo.ID + '" data-groupcount="1" data-productid="' + value3.ProductID + '" data-specificationid="' + value3.SpecificationID + '" data-SpecificationCount="' + productInfo.Count + '" data-title="' + value3.ProductTitle + '" data-specificationtitle="' + productInfo.Title + '">' +
                        '                    <i class="weui-icon-checked"></i>' +
                        '                </div>' +
                        '                <div class="weui-cell__bd">' + value3.ProductTitle +
                        '                </div>' +
                        '            </label>' +
                        '        </div>' +
                        '    </div>' +
                        '    <div class="weui-media-box__bd">' +
                        '        <p class="weui-media-box__desc">' + productInfo.Title + '</p>' +
                        '        <p class="weui-media-box__desc">到期时间:' + productInfo.CloseDate + '</p>' +
                        '    </div>' +
                        '</a>';
                });

                $("#preproduct").append(tobeConfiguredProductsstr+userproductstr+ '</div>');

                var tobeproducts = $(".tobeconfiguredproducts").filter('input:checked');
                $.each(tobeproducts,function (key3,value3) {
                    var id = $(value3).data('id');
                    if($("#cc"+id) !=undefined){
                        $("#cc"+id).trigger('click');
                    }
                });

                return;
            }
            var params = {
                productid: buyproductid,
                buyyear: year,
                tyunusercode: $('#tyunusercode').val()
            };
            $.ajax({
                url: '/index.php?module=TyunWebBuyService&action=GetUserPackageDegradeInfoData',
                type: 'POST',
                dataType: 'json',
                data: params,
                beforeSend: function () {
                    $.showLoading('数据获取中');
                },
                success: function (datas) {
                    $.hideLoading();
                    if (datas.code == 200) {
                        $("#preproduct").empty();
                        var tobeConfiguredProductsstr = '';
                        var value = (datas.data.tobeConfiguredProducts);
                        value = value[preproductid];
                        tobeConfiguredProductsstr += '<div class="weui-panel__hd" style="padding-top:5px;padding-bottom: 5px;">' +
                            '    <div class="weui-cell__hd weui-cell__ft__add">' + value.Title + '(可选' + value.Count + '个)' + '</div>' +
                            '       <input id="max_select_num" type="hidden" value="'+value.Count+'">'+
                            '    <div class="weui-cell__ft weui-cell__ft__add"></div>' +
                            '</div>';
                        var userproductstr = '<div class="weui-panel__bd">';
                        $.each(value.userProducts, function (key, value2) {
                            if(value.ProductID==52){
                                $.each(domains, function (key3, value3) {
                                    var productInfo = (value3.ProductSpecifications)[0];
                                    userproductstr += '<a href="javascript:void(0);" class="weui-media-box weui-media-box_appmsg" style="padding-top:0px;padding-bottom: 0px;">' +
                                        '    <div class="weui-media-box__bd">' +
                                        '        <div class="weui-cells_checkbox">' +
                                        '            <label class="weui-cell weui-check__label" for="cc' + productInfo.ID + '">' +
                                        '                <div class="weui-cell__hd">' +
                                        '                    <input type="checkbox" class="weui-check tobeconfiguredproducts2" name="checkbox" data-closedate="'+productInfo.CloseDate+'" data-grouptitle="' + value.Title + '" data-groupid="' + value.ProductID + '" id="cc' + productInfo.ID + '" data-id="' + productInfo.ID + '" data-groupcount="' + value.Count + '" data-productid="' + value3.ProductID + '" data-specificationid="' + value3.SpecificationID + '" data-SpecificationCount="' + productInfo.Count + '" data-title="' + value3.ProductTitle + '" data-specificationtitle="' + productInfo.Title + '">' +
                                        '                    <i class="weui-icon-checked"></i>' +
                                        '                </div>' +
                                        '                <div class="weui-cell__bd">' + value3.ProductTitle +
                                        '                </div>' +
                                        '            </label>' +
                                        '        </div>' +
                                        '    </div>' +
                                        '    <div class="weui-media-box__bd">' +
                                        '        <p class="weui-media-box__desc">' + productInfo.Title + '</p>' +
                                        '        <p class="weui-media-box__desc">到期时间:' + productInfo.CloseDate + '</p>' +
                                        '    </div>' +
                                        '</a>';
                                });
                                return true;
                            }

                            userproductstr += '<a href="javascript:void(0);" class="weui-media-box weui-media-box_appmsg" style="padding-top:0px;padding-bottom: 0px;">' +
                                '    <div class="weui-media-box__bd">' +
                                '        <div class="weui-cells_checkbox">' +
                                '            <label class="weui-cell weui-check__label" for="cc' + value2.ID + '">' +
                                '                <div class="weui-cell__hd">' +
                                '                    <input type="checkbox" class="weui-check tobeconfiguredproducts2" name="checkbox" data-closedate="'+value2.CloseDate+'" data-grouptitle="' + value.Title + '" data-groupid="' + value.ProductID + '" id="cc' + value2.ID + '" data-id="' + value2.ID + '" data-groupcount="' + value.Count + '" data-productid="' + value2.ProductID + '" data-specificationid="' + value2.SpecificationID + '" data-SpecificationCount="' + value2.SpecificationCount + '" data-title="' + value2.Title + '" data-specificationtitle="' + value2.SpecificationTitle + '">' +
                                '                    <i class="weui-icon-checked"></i>' +
                                '                </div>' +
                                '                <div class="weui-cell__bd">' + value2.Title +
                                '                </div>' +
                                '            </label>' +
                                '        </div>' +
                                '    </div>' +
                                '    <div class="weui-media-box__bd">' +
                                '        <p class="weui-media-box__desc">' + value2.SpecificationTitle + '</p>' +
                                '        <p class="weui-media-box__desc">到期时间:' + value2.CloseDate + '</p>' +
                                '    </div>' +
                                '</a>';
                        });
                        $("#preproduct").append(tobeConfiguredProductsstr+userproductstr+ '</div>');

                        var tobeproducts = $(".tobeconfiguredproducts").filter('input:checked');
                        console.log(tobeproducts);
                        $.each(tobeproducts,function (key3,value3) {
                            var id = $(value3).data('id');
                            if($("#cc"+id) !=undefined){
                                $("#cc"+id).trigger('click');
                            }
                        });
                    }
                }
            })
        }

    }

    $("#container").on('click','.tobeconfiguredproducts2',function () {
        var max_select_num = $("#max_select_num").val();
        console.log(max_select_num);
        var select_num = $(".tobeconfiguredproducts2").filter('input:checked').length;
        if(max_select_num<select_num){
            $.toast('最多选择'+max_select_num+'项',"text");
            $(this).trigger('click');
            return;
        }
    });

    $("#container").on('click','#submitpreproduct',function () {
        var tobeconfiguredproducts2 = $(".tobeconfiguredproducts2").filter('input:checked');
        console.log('1aaaaaaa');
        if(tobeconfiguredproducts2.length >0){
            console.log('2aaaaaaa');
            var userproductstr = '<div class="weui-panel__bd">';
            $.each(tobeconfiguredproducts2, function (key, value2) {
                console.log(value2);
                var id = $(value2).data('id');
                var grouptitle = $(value2).data('grouptitle');
                var groupid = $(value2).data('groupid');
                var groupcount = $(value2).data('groupcount');
                var productid = $(value2).data('productid');
                var specificationid = $(value2).data('specificationid');
                var specificationcount = $(value2).data('specificationcount');
                var title = $(value2).data('title');
                var specificationtitle = $(value2).data('specificationtitle');
                var closedate = $(value2).data('closedate');
                userproductstr += '<a href="javascript:void(0);" class="weui-media-box weui-media-box_appmsg" style="padding-top:0px;padding-bottom: 0px;">' +
                    '    <div class="weui-media-box__bd">' +
                    '        <div class="weui-cells_checkbox">' +
                    '            <label class="weui-cell weui-check__label" for="c' + id + '">' +
                    '                <div class="weui-cell__hd">' +
                    '                    <input type="checkbox" class="weui-check tobeconfiguredproducts" checked="checked" disabled="disabled" name="checkbox" data-grouptitle="' + grouptitle + '" data-groupid="' + groupid + '" id="c' + id + '" data-id="' + id + '" data-groupcount="' + groupcount + '" data-productid="' + productid + '" data-specificationid="' + specificationid + '" data-SpecificationCount="' + specificationcount + '" data-title="' + title + '" data-specificationtitle="' + specificationtitle + '">' +
                    '                    <i class="weui-icon-checked"></i>' +
                    '                </div>' +
                    '                <div class="weui-cell__bd">' + title +
                    '                </div>' +
                    '            </label>' +
                    '        </div>' +
                    '    </div>' +
                    '    <div class="weui-media-box__bd">' +
                    '        <p class="weui-media-box__desc">' + specificationtitle + '</p>' +
                    '        <p class="weui-media-box__desc">到期时间:' + closedate + '</p>' +
                    '    </div>' +
                    '</a>';
            });
            console.log(userproductstr);
            var preproductid = $("#preproductid").data('value');
            console.log(preproductid);
            $("#selectproduct"+preproductid).html(userproductstr);
        }
        pageManager.back();
    })

    //selectproductLoad();
    $('#container').on('input','#buyproduct',function(){
        var lastvalue=$(this).attr('data-lastvalue');
        var value=$(this).val();
        $('#buyproduct').attr('data-lastvalue',value);
        if(value!=lastvalue){
            getRenewInterest(value);
        }
    });
    $('#container').on('input','#buyyear',function(){
        var lastvalue=$(this).attr('data-lastvalue');
        var value=$(this).val();
        $('#buyyear').attr('data-lastvalue',value);
        $('#buyyear').attr('data-values',value);
        if(value!=lastvalue){
            $(".otherproductlist").empty();
            GetUserPackageDegradeInfoData()
        }
        calcotherprice();
    });
    $('#container').on('click','.helpinfo',function(){
        var thisInstance=$(this);
        $.alert(thisInstance.data('content'), thisInstance.data('title'));
    });

    function GetUserPackageDegradeInfoData(){
        var buyproductid=$('#buyproduct').val();
        var year=$('#buyyear').val();
        if(buyproductid>0 && year>0)
        {
            var packageRenewalPrice=$('#buyproduct').find("option:checked").attr('data-packagerenewalprice');
            $('#Price').val(parseFloat(FloatMul(packageRenewalPrice,year)).toFixed(2));
            $('#Priceshow').val('￥'+parseFloat(FloatMul(packageRenewalPrice,year)).toFixed(2));
            var params = {
                productid: buyproductid,
                buyyear: year,
                tyunusercode: $('#tyunusercode').val()
            };
            $('.degradepanel').html('');
            $.ajax({
                url: '/index.php?module=TyunWebBuyService&action=GetUserPackageDegradeInfoData',
                type: 'POST',
                dataType: 'json',
                data: params,
                beforeSend: function () {
                    $.showLoading('数据获取中');
                },
                success: function (datas) {
                    $.hideLoading();
                    if (datas.code == 200) {
                        var CloseDate = datas.data.CloseDate;
                        var d = new Date(CloseDate);
                        d.setMonth(d.getMonth() + year * 12);
                        var fdate = d.getDate();
                        if(fdate<10){
                            fdate = '0'+fdate;
                        }
                        var dateyear = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + fdate;
                        var c = new Date(CloseDate);
                        var gdate = c.getDate();
                        if(gdate<10){
                            gdate = '0'+gdate;
                        }
                        var dateyearcurrent = c.getFullYear() + '-' + (c.getMonth() + 1) + '-' + gdate;
                        $('#upgardecycle').val(dateyearcurrent + '~' + dateyear);
                        var noConfiguredProducts = datas.data.noConfiguredProducts;//无需配置产品集合noConfiguredProducts
                        var notIncludedProducts = datas.data.notIncludedProducts;//降级套餐中不包含的产品notIncludedProducts
                        var tobeConfiguredProducts = datas.data.tobeConfiguredProducts;//待配置产品集合
                        var notIncludedProductsstr='';
                        var notIncludedProductssnum=0;
                        $.each(notIncludedProducts,function(key,value){
                            var isdisplay='';
                            var tmpflag='';
                            notIncludedProductssnum++;
                            if(key>1){
                                var isdisplay='display:none;';
                                var tmpflag=' notIncludedProductsdisplay';
                            }
                            notIncludedProductsstr+='<a href="javascript:void(0);" class="weui-media-box weui-media-box_appmsg'+tmpflag+'" style="padding:10px 15px;'+isdisplay+'">\n' +
                                '    <div class="weui-media-box__bd">\n' +
                                '        <div class="weui-media-box__desc"  style="padding-left:10px;">'+value.Title+
                                '        <input type="hidden" class="notIncludedProductslist"  data-specificationtitle="'+value.SpecificationTitle+'" data-title="'+value.Title+'" data-productid="'+value.ProductID+'" data-specificationid="'+value.SpecificationID+'"></div>\n' +
                                '    </div>\n' +
                                '    <div class="weui-media-box__bd">\n' +
                                '        <p class="weui-media-box__desc">'+value.SpecificationTitle+'</p>\n' +
                                '    </div>\n' +
                                '</a>';

                        });
                        var endnotIncludedProductsstr='';
                        if(notIncludedProductssnum>2){
                            endnotIncludedProductsstr='<a href="javascript:void(0);" class="weui-cell weui-cell_access weui-cell_link notIncludedProductsshowall" data-flag="1">\n' +
                                '    <div class="weui-cell__bd" style="text-align: center;">查看更多</div>\n' +
                                '</a>';
                        }
                        var noConfiguredProductsstr='';
                        var noConfiguredProductsnum=0;
                        $.each(noConfiguredProducts,function(key,value){
                            var isdisplay='';
                            var tmpflag='';
                            noConfiguredProductsnum++;
                            if(key>1){
                                var isdisplay='display:none;';
                                var tmpflag=' noConfiguredProductsdisplay';
                            }
                            noConfiguredProductsstr+='<a href="javascript:void(0);" class="weui-media-box weui-media-box_appmsg'+tmpflag+'" style="padding:10px 15px;'+isdisplay+'">' +
                                '    <div class="weui-media-box__bd">' +
                                '        <div class="weui-media-box__desc" style="padding-left:10px;">'+value.Title+
                                '        <input type="hidden" class="noConfiguredProductslist"  data-specificationtitle="'+value.SpecificationTitle+'" data-title="'+value.Title+'" data-productid="'+value.ProductID+'" data-specificationid="'+value.SpecificationID+'"></div>' +
                                '    </div>' +
                                '    <div class="weui-media-box__bd">' +
                                '        <p class="weui-media-box__desc">'+value.SpecificationTitle+'</p>' +
                                '    </div>' +
                                '</a>';
                        });
                        var endnoConfiguredProductsstr='';
                        if(noConfiguredProductsnum>2){
                            endnoConfiguredProductsstr='<a href="javascript:void(0);" class="weui-cell weui-cell_access weui-cell_link triggernoConfiguredProductsdisplay" data-msg="还有'+(noConfiguredProductsnum-2)+'服务">\n' +
                                '<div class="weui-cell__bd showAll" style="text-align: center;">还有'+(noConfiguredProductsnum-2)+'服务</div>\n' +
                                '</a>';
                        }
                        var tobeConfiguredProductsstr='';
                        window.isDomainExist = false;
                        var i = 0;
                        $.each(tobeConfiguredProducts,function(key,value){
                            if(value.ProductID==52){
                                isDomainExist = true;
                            }
                            tobeConfiguredProductsstr+='<div class="weui-panel__hd " style="padding-top:5px;padding-bottom: 5px;">' +
                                '    <div class="weui-cell__hd weui-cell__ft__add preproduct" data-configureproductid="'+value.ProductID+'" data-preproductid="'+i+'">'+value.Title+'(可选'+value.Count+'个)'+'</div>' +
                                '    <div class="weui-cell__ft weui-cell__ft__add"></div>' +
                                '</div><div  class="weui-panel__bd" id="selectproduct'+i+'"></div>';
                            i++;
                        });
                        console.log(isDomainExist);
                        console.log(domains);
                        if(!isDomainExist && domains && domains.length>1 && domaininpackage){
                            tobeConfiguredProductsstr+='<div class="weui-panel__hd " style="padding-top:5px;padding-bottom: 5px;">' +
                                '    <div class="weui-cell__hd weui-cell__ft__add preproduct" data-configureproductid="52" data-preproductid="'+i+'">域名(可选1个)'+'</div>' +
                                '    <div class="weui-cell__ft weui-cell__ft__add"></div>' +
                                '</div><div  class="weui-panel__bd" id="selectproduct'+i+'"></div>';
                        }

                        var laststring='<div class="weui-tab">\n' +
                            '    <div class="weui-navbar">\n' +
                            '        <a class="weui-navbar__item weui-bar__item--on-custom" data-href="tab1">\n' +
                            '            套餐权益\n' +
                            '        </a>\n' +
                            '        <a class="weui-navbar__item" data-href="tab2">\n' +
                            '            非套餐权益&nbsp;<span class="iconfont icon-help-copy helpinfo" style="font-size: 16px;" data-title="非套餐权益" data-content="降级套餐中仍可正常使用的产品;"></span>\n' +
                            '        </a>\n' +
                            '    </div>\n' +
                            '    <div class="weui-tab__bd">\n' +
                            '        <div id="tab1" class="weui-tab__bd-item weui-tab__bd-item--active">\n' +
                            '            <div class="weui-panel weui-panel_access">\n' +
                            '                <div class="weui-cells__title" style="border-bottom: 1px solid #e5e5e5;"><span style="font-size: 14px;font-weight: bold;">待配置产品</span>&nbsp;<span class="iconfont icon-help-copy helpinfo" style="font-size: 20px;" data-title="待配置产品" data-content="请选择套餐降级后，仍可使用的产品"></span></div>'+tobeConfiguredProductsstr+
                            '            </div>\n' +
                            '            <div class="weui-cells__title" style="border-bottom: 1px solid #e5e5e5;"><span style="font-size: 14px;font-weight: bold;">无需配置产品</span>&nbsp;<span class="iconfont icon-help-copy helpinfo" style="font-size: 20px;" data-title="无需配置产品" data-content="不包含在降级套餐中的产品，但仍可正常使用"></span></div>\n' +
                            '            <div class="weui-panel__bd">\n' +
                            '            '+ noConfiguredProductsstr+'</div>\n' +
                            '            <div class="weui-panel__ft">\n' +endnoConfiguredProductsstr+
                            '            </div>\n' +
                            '        </div>\n' +
                            '        <div id="tab2" class="weui-tab__bd-item">\n' +
                            '            <div class="weui-panel weui-panel_access">\n' +
                            '                <div class="weui-panel__bd"><br />\n'+notIncludedProductsstr+
                            '                </div>\n' +
                            '                <div class="weui-panel__ft">\n' +endnotIncludedProductsstr+
                            '                </div>\n' +
                            '            </div>\n' +
                            '        </div>\n' +
                            '    </div>\n' +
                            '</div>';
                        $('.degradepanel').html(laststring);
                        calcotherprice();
                    } else {
                        $.toast(data.message, 'text');
                    }
                }
            });
        }
    }
    $('#container').on('click','.weui-navbar__item',function(){
        var thisInstance=$(this);
        var tabid=thisInstance.attr('data-href');
        var newtabid=tabid=='tab1'?'tab2':'tab1';
        if(thisInstance.hasClass('weui-bar__item--on-custom')){

        }else{
            thisInstance.addClass('weui-bar__item--on-custom');
            thisInstance.siblings().removeClass('weui-bar__item--on-custom');
            $('#'+tabid).addClass('weui-tab__bd-item--active');
            $('#'+newtabid).removeClass('weui-tab__bd-item--active');
        }
    });
    $('#container').on('click','.triggernoConfiguredProductsdisplay',function(){
        var thisInstance=$(this);
        if(thisInstance.attr('data-flag')==1){
            $('.showAll').text(thisInstance.attr('data-msg'));
            thisInstance.attr('data-flag',2);
        }else{
            thisInstance.attr('data-flag',1);
            $('.showAll').text('收起');
        }
        $('.noConfiguredProductsdisplay').toggle();
    });
    $('#container').on('click','.notIncludedProductsshowall',function(){
        var thisInstance=$(this);
        if(thisInstance.attr('data-flag')==1){
            $('.notIncludedProductsshowall .weui-cell__bd').text('收起');
            thisInstance.attr('data-flag',2);
        }else{
            thisInstance.attr('data-flag',1);
            $('.notIncludedProductsshowall .weui-cell__bd').text('查看更多');
        }
        $('.notIncludedProductsdisplay').toggle();
    });

    $('#submitfrom11').on('click',function(){
        var submitid=$('#submitid').attr('data-value');
        if(submitid!=2){
            return false;
        }
        var servicecontractsid=$('input[name="servicecontractsid"]').val();
        var servicecontractsid_display=$('input[name="servicecontractsid_display"]').val();
        var accountid=$('input[name="accountid"]').val();
        var accountid_display=$('input[name="accountid_display"]').val();
        var mobile=$('#mobile').val();
        var mobilevcode=$('#mobilevcode').val();
        var classtype=$('#classtype').val();
        var buyyear=$('#buyyear').data('values');
        var oldproductnameid=$('#oldproductnameid').val();
        var servicetotal=parseFloat($('#servicetotal').val()).toFixed(2);
        var classtyperenew=$('#classtyperenew').attr('data-values');
        var tyunusercode=$('#tyunusercode option:checked').text();
        var tyunusercodeid=$('#tyunusercode').val();
        var buyproduct=$('#buyproduct').attr('data-values');
        var tyunusercodetext=$('#tyunusercode').find("option:checked").text();
        var buydate=$('#buydate').val();
        var currentdata=(new Date()).getFullYear()+'-'+((new Date()).getMonth()+1)+'-'+(new Date()).getDate();
        var upgardecycle=$('#upgardecycle').val();
        var oldproductname_display=$('#oldproductname_display').val();
        var buyproductname=$('#buyproduct').val();
        var str='<div class="weui-form-preview">' +
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
            '<label class="weui-form-preview__label">降级版本</label>' +
            '<span class="weui-form-preview__value">'+buyproductname+'</span>' +
            '</div>' +
            '<div class="weui-form-preview__item">' +
            '<label class="weui-form-preview__label">合同金额</label>' +
            '<span class="weui-form-preview__value">'+servicetotal+'</span>' +
            '</div>' +
            '<div class="weui-form-preview__item">' +
            '<label class="weui-form-preview__label">降级年限</label>' +
            '<span class="weui-form-preview__value">'+buyyear+'</span>' +
            '</div>' +
            '<div class="weui-form-preview__item">' +
            '<label class="weui-form-preview__label">降级周期</label>' +
            '<span class="weui-form-preview__value">'+upgardecycle+'</span>' +
            '</div>' +
            '<div class="weui-form-preview__item">' +
            '<label class="weui-form-preview__label">降级合同</label>' +
            '<span class="weui-form-preview__value">'+servicecontractsid_display+'</span>' +
            '</div>' +
            '<div class="weui-form-preview__item">' +
            '<label class="weui-form-preview__label">客户名称</label>' +
            '<span class="weui-form-preview__value">'+accountid_display+'</span>' +
            '</div>' +
            '<div class="weui-form-preview__item">' +
            '<label class="weui-form-preview__label">降级时间</label>' +
            '<span class="weui-form-preview__value">'+currentdata+'</span>' +
            '</div>' +
            '</div>' +
            '</div>';
        //var $stru=''

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
            "tyunusercodetext":tyunusercodetext
        };
        $.confirm(str, "请核对订单信息", function() {
            $.ajax({
                    url: '/index.php?module=TyunWebBuyService&action=upgardedoOrder',
                    type: 'POST',
                    dataType: 'json',
                    data:params,
                    beforeSend:function(){
                        $.showLoading('订单处理中');
                    },
                    success: function (data) {
                        $.hideLoading();
                        if(data.success==1){
                            $.toast('下单成功','text');
                            location.href="/index.php?module=TyunwebBuyService&action=upgarde";
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
    $('#container').on('change','#servicetotal',function(){
        checkProductValue(1);
    });
    $('#container').on('input','#servicetotal',function(){
        checkProductValue(1);
    });
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


    $('#container').on("click",".selectGroupName",function(event){
        var id=$(this).data('id');
        var currentchecked=!$(this).attr('checked');
        var checkedvalue=$(this).attr('checked');
        if(checkedvalue=='checked'){
            $(this).removeAttr('checked');
            currentchecked=true;
        }
        $.each($('.groupname'+id),function(key,value){
            if(currentchecked){
                $(value).prop("checked",false);
            }else{
                $(value).prop('checked',true);
                var fartherid1=$(value).data('fartherid1');
                $('.ProductSpecifications'+fartherid1).eq(0).trigger('click');
            }
        });
        var categoryid = $(this).data("categoryid");
        var checkNum  = $(".ancestor"+categoryid).filter('input:checked').length;
        if(checkNum){
            $(".nav-right-tag"+categoryid).html(checkNum);
            $(".nav-right-tag"+categoryid).css('background-color',"crimson");
        }else {
            $(".nav-right-tag"+categoryid).html('');
            $(".nav-right-tag"+categoryid).css('background-color',"");
        }
    });
    $('#container').on("click",".select2check",function(event){
        var id=$(this).data('fartherid');
        var currentchecked=false;
        if($(this).attr('checked')){
            var fartherid1=$(this).data('fartherid1');
            $('.ProductSpecifications'+fartherid1).eq(0).trigger('click');
        }
        var temp=0;
        $.each($('.groupname'+id),function(key,value){
            if($(this).attr('checked')==false){
                $('.selectGroup'+id).prop("checked",false);
                return false;
            }
            temp++;
        });
        if($('.groupname'+id).length==temp){
            $('.selectGroup'+id).prop("checked",true);
        }
        var categoryid = $(this).data("categoryid");
        var checkNum  = $(".ancestor"+categoryid).filter('input:checked').length;
        if(checkNum){
            $(".nav-right-tag"+categoryid).html(checkNum);
            $(".nav-right-tag"+categoryid).css('background-color',"crimson");
        }else {
            $(".nav-right-tag"+categoryid).html('');
            $(".nav-right-tag"+categoryid).css('background-color',"");
        }
    });

    $('#container').on('click','.weui-count__decrease_deleted',function(){
        var id=$(this).data('id');
        $.confirm('', "确定要删除另购产品?", function() {
            $('.othger_product_'+id).remove();
            var sumprice=0;//首购价格总之合
            var sumrenewprice=0;//续费价格
            if($('.select3check').length==0){
                $('.othger_product_last').remove();
                $('#otherproductsum').val(sumprice);
                $('#otherproductrenewsum').val(sumrenewprice);
            }
            calcotherprice();
        });
    });
    $('#container').on("click",".weui-count__sub",function(event){
        var id=$(this).data('id');
        var countnumber=$('.weui-count__current_number'+id).val();
        var limit_min = $('.weui-count__current_number'+id).data('limit_min');
        if(countnumber>1){
            countnumber--;
            if(limit_min>countnumber){
                $.toast('最少购买数量为'+limit_min,"text");
                return false;
            }
            $('.weui-count__current_number'+id).val(countnumber);
            $('.select3farther'+id).attr('data-number',countnumber);
            calOtherProductPriceInSelectProduct(id);
            // calcotherprice();
        }else{
            $.toast('受不了，不能再减少了哦',"text");
        }

    });

    $('#container').on("click",".weui-count__add",function(event){
        var id=$(this).data('id');
        var countnumber=parseInt($('.weui-count__current_number'+id).val());
        // if(countnumber<99){
            countnumber++;
            var uselimit=$('.select3farther'+id).attr('data-uselimit');
            uselimit=uselimit>0?uselimit:0;
            var userbuycount=$('.select3farther'+id).attr('data-userbuycount');
            if(uselimit>0){
                //var tempadd=parseInt(countnumber)+parseInt(userbuycount);
                var tempadd=FloatAdd(parseInt(countnumber),parseInt(userbuycount));
                if(tempadd>=uselimit){
                    $.toast('哎哟，不能再添加了哦',"text");
                    return false;
                }
            }
            $('.weui-count__current_number'+id).val(countnumber);
            $('.select3farther'+id).attr('data-number',countnumber);
            // var price=$('.select3farther'+id).data('price');
            // var renewprice=$('.select3farther'+id).data('renewprice');
            // if(price>0 || renewprice>0){
            //     var buyyear=$('#buyyear').attr('data-values');
            //     var sumprice=FloatMul(price,parseInt(countnumber));
            //     buyyear=buyyear>1?FloatSub(buyyear,1):0;
            //     var sumrenewprice=FloatMul(FloatMul(renewprice,parseInt(countnumber)),buyyear);
            //     $('.pricesum'+id).html('购:￥'+parseFloat(sumprice).toFixed(2)+'+&nbsp;续:￥'+parseFloat(sumrenewprice).toFixed(2)+'=￥'+parseFloat(FloatAdd(sumprice,sumrenewprice)).toFixed(2));
            // }
            var sumprice=0;
            var sumrenewprice=0;
            $.each($('.select3check'),function(key,value){
                sumprice=FloatAdd(sumprice,FloatMul(parseInt($(value).data('number')),$(value).data('price')));
                // sumrenewprice=FloatAdd(sumrenewprice,FloatMul(parseInt($(value).data('number')),$(value).data('renewprice')));
            });
            // $('#otherproductrenewsum').val(sumrenewprice);
            $('#otherproductsum').val(sumprice);
        calOtherProductPriceInSelectProduct(id);
        // calcotherprice();
            return false;
        // }else{
        //     $.toast('哎哟，不能再添加了哦',"text");
        // }

    });

    $('#container').on("click",".weui-count__decrease",function(event){
        var id=$(this).data('fartherid1');
        if(!$('.select2farther'+id).attr('checked')){
            return false;
        }
        var countnumber=$('.countnumber'+id).val();
        if(countnumber>1){
            countnumber--;
            var limit_min = $('.countnumber'+id).data('limit_min');
            if(limit_min>countnumber){
                $.toast('最少购买数量为'+limit_min,"text");
                return false;
            }
            $('.countnumber'+id).val(countnumber);
            $('.select2farther'+id).attr('data-number',countnumber);
            var price=$('.select2farther'+id).data('price');
            // if(price>0){
            //     $('.price'+id).text('￥'+parseFloat(FloatMul(price,parseInt(countnumber))).toFixed(2));
            // }
            calOtherProductPrice(id);
        }else{
            $.toast('受不了，不能再减少了哦',"text");
        }

    });

    $('#container').on("click",".weui-count__increase",function(event){
        var id=$(this).data('fartherid1');
        if(!$('.select2farther'+id).attr('checked')){
            return false;
        }
        var countnumber=parseInt($('.countnumber'+id).val());
        // if(countnumber<99){
            countnumber++;
            var uselimit=$('.select2farther'+id).attr('data-uselimit');
            uselimit=uselimit>0?uselimit:0;
            var userbuycount=$('.select2farther'+id).attr('data-userbuycount');
            if(uselimit>0){
                var tempadd=FloatAdd(countnumber,parseInt(userbuycount));
                if(tempadd>=uselimit)
                    $.toast('哎哟，不能再添加了哦',"text");
                return false;
            }
            $('.countnumber'+id).val(countnumber);
            $('.select2farther'+id).attr('data-number',countnumber);
            var price=$('.select2farther'+id).data('price');
            // if(price>0){
            //     $('.price'+id).text('￥'+parseFloat(FloatMul(price,parseInt(countnumber))).toFixed(2));
            // }
        calOtherProductPrice(id);

        return false;
        // }else{
        //     $.toast('哎哟，不能再添加了哦',"text");
        // }

    });

    window.otherproductLoad=function(){
        var agents=$('#instanceagents').data('value');
        var buyyears = $("#buyyear").data('values');
        var productclassone = $("#classtyperenew").val();
        $.ajax(
            {
                url: '/index.php?module=TyunWebBuyService&action=getOtherPorduct',
                type: 'POST',
                dataType: 'json',
                data:{"categoryID":0,
                    "seriesID":1,
                    "userID":$('#tyunusercode').val(),
                    "authenticationType":1,
                    "activityAgent": agents,
                    "buyTrem":buyyears
                },
                beforeSend:function(){
                    $.showLoading('另购产品加载中');
                },
                success: function (data) {
                    $.hideLoading();
                    if(data.success){
                        copyUpdateOtherProduct(data);
                    }
                }
            }
        );
    };

    $('#container').on('click','.navTab',function(){
        var key = $(this).data('key');
        $(".groupTab").hide();
        $(".groupTab"+key).show();
        console.log(key);
    });
    function copyUpdateOtherProduct(alldatas){
        var datas=alldatas.data;
        $("#tagnav").empty();
        var allstr='';
        var tabstr = '';
        var productclassone=$('#productclassone').attr('data-values');
        var navStr = '  <ul class="weui-navigator-list" style="transition-timing-function: cubic-bezier(0.1, 0.57, 0.1, 1); transition-duration: 0ms; transform: translate(0px, 0px) translateZ(0px);">\n' ;

        $('#page_othgerproduct').append(tabstr);
        var i = 0;
        var tagnavindex = 0;
        var isDefault = false;
        $.each(datas,function(k3,v3){
            // if(value['Products'].length<=0){
            //     return true;
            // }
            console.log(k3);
            console.log(alldatas.categoryid);

            var isShow = 'display:none'
            if(k3==alldatas.categoryid){
                isShow = '';
                tagnavindex=i;
                isDefault = true;
            }
            i++;
            checkStr = "";
            navStr += '<li class=" navTab  navTab'+k3+'" data-key="'+k3+'"><div class="nav-right-tag nav-right-tag' + k3 + '"></div><a href="javascript:;">'+v3['title']+'</a></li>\n';
            $.each(v3['data'],function (key,value) {
                var ancestor = 'ancestor'+k3;
                var groupname='groupname'+key;
                var fartherid=key;
                var othershowflag=0;
                var str='<div class="weui-panel weui-cells weui-cells_checkbox groupTab groupTab'+k3+'" style="'+isShow+'">\n' +
                    '                <div class="weui-panel__hd" style="padding:0;">\n' +
                    '                    <label class="weui-cell weui-check__label">\n' +
                    '                        <div class="weui-cell__hd">\n' +
                    '                            <input type="checkbox" class="weui-check selectGroupName selectGroup'+key+'" data-id="'+key+'" data-categoryid="'+k3+'">\n' +
                    '                            <i class="weui-icon-checked"></i>\n' +
                    '                        </div>\n' +
                    '                        <div class="weui-cell__bd">\n' +
                    '                            <p style="font-size:20px;">'+value['GroupName']+'</p>\n' +
                    '                        </div>\n' +
                    '                    </label>\n' +
                    '                </div>\n'+
                    '                <div class="weui-panel__bd ">\n';
                $.each(value['Products'],function(k1,v1){
                     if(!v1['CanSeparatePurchase']){
                         return true;
                     }

                    if(v1['SellStatus']==0){
                        return true;
                    }
                    var limit_min = 1;
                    if(v1['CategoryID']==6){
                        limit_min = 5;
                    }

                    othershowflag=1;
                    var fartherid1=v1['ProductID'];
                    str+=
                        '<div class="weui-media-box weui-media-box_appmsg">\n' +
                        '    <div class="weui-media-box__hd">\n' +
                        '        <label class="weui-cell weui-check__label">\n' +
                        '            <div class="weui-cell__hd">\n' +
                        '                <input type="checkbox" class="weui-check  '+ ancestor+'  '+groupname+' select2check select2farther'+fartherid1+'" data-money="" data-limit_min="'+limit_min+'" data-currentid="'+v1['ProductID']+'" data-fartherid="'+fartherid+'" data-producttitle="'+v1['ProductTitle']+'" data-groupname="'+value['GroupName']+'" data-fartherid1="'+fartherid1+'" data-productid="'+v1['ProductID']+'" data-candiscount="'+v1['CanDiscount']+'" data-categoryid="'+v1['CategoryID']+'" data-producttitle="'+v1['ProductTitle']+'" data-number="'+limit_min+'" data-count="1" data-id=""  data-price=""  data-renewprice="" data-marketprice="" data-marketrenewprice="" data-unit="" data-specificationstitle="" data-uselimit="'+v1.UseLimit+'" data-userbuycount="'+v1.UserBuyCount+'" >\n' +
                        '                <i class="weui-icon-checked"></i>\n' +
                        '            </div>\n' +
                        '        </label>\n' +
                        '    </div>\n' +
                        '    <div class="weui-media-box__bd">\n' +
                        '        <div class="weui-media-box__title">\n' +
                        '            <div class="weui-cell"  style="padding:5px 10px;font-size:16px;">\n' +
                        '                <div class="weui-cell__bd">\n' +v1['ProductTitle']+
                        '                </div>\n' +
                        '                <div class="weui-cell__ft">\n' +
                        '                    <span class="ProductTitlePrice'+fartherid1+'" style="font-size: 12px;"></span>\n' +
                        '                </div>\n' +
                        '            </div>\n' +
                        '        </div>\n' +
                        '        <div class="weui-media-box__title">\n' +
                        '            <div class="weui-cell" style="padding:5px 10px;">\n' +
                        '                <div class="weui-cell__bd">\n' +
                        '                    <div class="button_sp_area" style="white-space: normal;">\n';
                    $.each(v1['ProductSpecifications'],function(k2,v2){
                        if(v2['CanSeparatePurchase']){
                            var fartherid2=v2['ID'];
                            str+='<a href="javascript:;" class="weui-btn weui-btn_mini selectProductSpecifications weui-btn_disabled weui-btn_default ProductSpecifications'+fartherid1+' ProductSpecificationsmodify'+v2['ID']+'" style="margin-top:0px;" data-fartherid="'+fartherid+'" data-fartherid1="'+fartherid1+'" data-fartherid2="'+fartherid2+'" data-count="'+v2['Count']+'"  data-id="'+v2['ID']+'" data-money="'+v2['money']+'" data-marketprice="'+v2['MarketPrice']+'" data-marketrenewprice="'+v2['MarketRenewPrice']+'" data-price="'+v2['Price']+'"  data-renewprice="'+v2['RenewPrice']+'" data-unit="'+v2['Unit']+'" data-specificationstitle="'+v2['Title']+'">'+v2['Title']+'</a>\n';

                        }
                    });
                    str+='</div>\n' +
                        '                                    </div>\n' +
                        '                                </div>\n' +
                        '                            </div>\n' +
                        '                            <div class="weui-media-box__desc">\n' +
                        '                                <div class="weui-cell">\n' +
                        '                                    <div class="weui-cell__bd">\n' +
                        '                                        <div class="weui-count weui-count_custrom">\n' +
                        '                                            <a class="weui-count__btn weui-count__decrease weui-count__left_custrom"  data-currentid="'+v1['ProductID']+'" data-fartherid="'+fartherid+'" data-fartherid1="'+fartherid1+'" data-candiscount="'+v1['CanDiscount']+'" data-categoryid="'+v1['CategoryID']+'" data-productid="'+v1['ProductID']+'" data-producttitle="'+v1['ProductTitle']+'"></a>\n' +
                        '                                            <input class="weui-count__number countnumber'+fartherid1+'" style="width:1.5rem;-webkit-appearance:none !important;border-radius: 0 !important;" type="number" value="'+limit_min+'" data-currentid="'+v1['ProductID']+'" data-limit_min="'+limit_min+'" data-fartherid="'+fartherid+'" data-fartherid1="'+fartherid1+'" data-candiscount="'+v1['CanDiscount']+'" data-categoryid="'+v1['CategoryID']+'" data-productid="'+v1['ProductID']+'" data-producttitle="'+v1['ProductTitle']+'" >\n' +
                        '                                            <a class="weui-count__btn weui-count__increase weui-count__right_custrom"  data-currentid="'+v1['ProductID']+'" data-fartherid="'+fartherid+'" data-fartherid1="'+fartherid1+'" data-candiscount="'+v1['CanDiscount']+'" data-categoryid="'+v1['CategoryID']+'" data-productid="'+v1['ProductID']+'" data-producttitle="'+v1['ProductTitle']+'"></a>\n' +
                        '                                        <div style="margin-left:10px" class="unit'+fartherid1+'"></div></div> \n' +
                        '                                    </div>\n' +
                        '                                    <div class="weui-cell__ft">\n' +
                        '                                        <span class="price price'+fartherid1+'"></span>\n' +
                        '                                    </div>\n' +
                        '                                </div>\n' +
                        '                            </div>\n' +
                        '                        </div>\n' +
                        '                    </div>\n';
                });
                str+='</div></div>';
                if(othershowflag==1){
                    allstr+=str;
                }
            })

        });

        navStr +=  '            </ul>\n';
        $('#page_othgerproduct').append(allstr);
        if(i>1) {
            $("#tagnav").append(navStr);
        }

        var buyyears = $("#buyyear").data('values');
        var groupid=[]
        $.each($('.select3check'),function(key,value){
            var productid=$(value).data('productid');
            var fartherid=$(value).data('fartherid');
            var fartherid1=$(value).data('fartherid1');
            var ProductSpecificationsid=$(value).data('id');
            var totalprice=$(value).data('totalprice');
            var number=$(value).data('number');
            var othercontractprice = $(".othercontractprice"+fartherid1).val();

            var price=$(value).data('price');
            $('.price'+fartherid1).text('￥'+parseFloat(totalprice).toFixed(2));

            $('.select2farther'+fartherid1).attr('data-number',number);
            $('.select2farther'+fartherid1).attr('data-totalprice',totalprice);
            $('.select2farther'+fartherid1).attr('data-othercontractprice',othercontractprice);
            $('.ProductTitlePrice'+fartherid1).text('￥'+parseFloat(price).toFixed(2));
            $('.countnumber'+fartherid1).val(number);
            $('.select2farther'+fartherid1).trigger('click');
            $('.ProductSpecificationsmodify'+ProductSpecificationsid).trigger("click");
            var categoryid = $(value).data('categoryid');
            var checkedNum = $(".ancestor" + categoryid).filter('input:checked').length;
            $(".nav-right-tag"+categoryid).html(checkedNum);
            $(".nav-right-tag"+categoryid).addClass('nav-right-tag');
            $(".nav-right-tag"+categoryid).css('background',"crimson");
        });
        if(i>1) {
            TagNav('#tagnav', {
                type: 'scrollToNext',
                curClassName: 'weui-state-active',
                index: tagnavindex
            });
            console.log(isDefault);
            if (!isDefault) {
                $(".groupTab0").show();
            }
        }
    }

    $('#container').on("click",'#otherproduct',function(){
        // if(!checkProductValue(2)){
        //     $.toast('请先完善信息！',"text");
        //     return false;
        // }
        window.pageManager.go('otherproduct');
    });
    checkProductValue=function (params){
        var productclassone=$('#productclassone').val();
        var productclasstwo=$('#productclasstwo').val();
        var buyyear=$('#buyyear').data('values');
        var agents=$('#agents').val();
        if(agents<1){
            return false;
        }
        if(buyyear<1 || buyyear==undefined){
            return false;
        }
        if(productclassone==''){
            return false;
        }
        if(productclasstwo==''){
            return false;
        }
        if(params==1){
            var servicetotal=$('#servicetotal').val();
            var totalmarketprice = $("#totalmarketprice").val();
            var isSpecialOtherProduct = specialOtherProduct();
            if(servicetotal>0 && totalmarketprice >0 && isSpecialOtherProduct){
                $('#submitid').attr('data-value',2);
                $('#submitfrom').css('backgroundColor','#4994F2');
                $('#electronnextstep').css('backgroundColor', '#4994F2');
                return true;
            }else{
                $('#submitid').attr('data-value',1);
                $('#submitfrom').css('backgroundColor','#999999');
                $('#electronnextstep').css('backgroundColor', '#999999');
                return false;
            }
            return false;

        }
        return true;
    }

    $('#container').on("click",".selectProductSpecifications",function(event){
        var id=$(this).data('fartherid1');
        if(!$('.select2farther'+id).attr('checked')){
            return false;
        }
        var price=$(this).data('price');
        var renewprice=$(this).data('renewprice');
        var money = $(this).data('money');
        var unit=$(this).data('unit');
        var dataid=$(this).data('id');
        var specificationstitle=$(this).data('specificationstitle');
        $(this).removeClass("weui-btn_disabled");
        $('.ProductSpecifications'+id).not(this).addClass("weui-btn_disabled");
        var countnumber=$('.countnumber'+id).val();
        var buyyears = $("#buyyear").data('values');
        $('.ProductTitlePrice'+id).text('￥'+parseFloat(money).toFixed(2)+'/'+buyyears+'年');
        $('.unit'+id).text(unit);
        $('.price'+id).text('￥'+parseFloat(money).toFixed(2));

        $('.select2farther'+id).attr('data-price',price);
        $('.select2farther'+id).attr('data-renewprice',renewprice);
        $('.select2farther'+id).attr('data-money',money);
        $('.select2farther'+id).attr('data-totalprice',money);
        $('.select2farther'+id).attr('data-unit',unit);
        $('.select2farther'+id).attr('data-id',dataid);
        $('.select2farther'+id).attr('data-specificationstitle',specificationstitle);
        $('.select2farther'+id).attr('data-specificationsid',specificationstitle);
    });

    window.selectproductinit=function (){
        var cpackageSpecificationList=window.packageSpecificationList||{};
        var buyproductitems=[];
        $.each(cpackageSpecificationList,function(key,value){
            var temp={title: value.Title,value: value.ID};
            buyproductitems.push(temp);
        });
        $("#buyproduct").select({
            title: "请选择",
            items: buyproductitems,
            onClose: function(d) {
                var buyproductid=d.data.values;
                console.log(buyproductid);
                var year=$("#buyyear").attr('data-values');
                console.log(year);
                getSecretKeySurplusMoney(buyproductid,year);
                calcotherprice();
                $.each(cpackageSpecificationList,function(key1,value1){
                    if(value1.ID==buyproductid){
                        $("#buyproduct").attr("data-title",value1.Title);
                        $("#buyproduct").attr("data-price",value1.packagePrice);
                        $("#buyproduct").attr("data-renewprice",value1.packageRenewalPrice);
                        $("#buyproduct").attr("data-marketprice",value1.marketPrice);
                        $("#buyproduct").attr("data-marketrenewprice",value1.marketRenewPrice);
                    }
                });
            }
        });
        var cminiupgradeyear=window.miniupgradeyear>1?1:1;
        var buyyearitems=[];
        for(cminiupgradeyear;cminiupgradeyear<=10;cminiupgradeyear++){
            var temp={title: cminiupgradeyear+"年",value: cminiupgradeyear};
            buyyearitems.push(temp);
        }
        $("#buyyear").select({
            title: "请选择",
            items: buyyearitems,
            onClose: function(d) {
                $(".otherproductlist").empty();
                var years=d.data.values;
                console.log(years);
                var buyproductid=$("#buyproduct").attr('data-values');
                console.log(buyproductid);
                getSecretKeySurplusMoney(buyproductid,years);
                calcotherprice();
            }
        });
    };

    function getSecretKeySurplusMoney(buyproductid,year) {
        var d = new Date();
        d.setMonth(d.getMonth() + year * 12);
        var edate = d.getDate();
        if(edate<10){
            edate = '0'+edate;
        }
        var dateyear = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + edate;
        var c = new Date();
        var dateyearcurrent = c.getFullYear() + '-' + (c.getMonth() + 1) + '-' + edate;
        $('#upgardecycle').val(dateyearcurrent + '~' + dateyear);

        var params = {
            clientPackageID: buyproductid,
            BuyTerm: year,
            ProductType: 6,
            discount: 1,
            agentType: 0,
            surplusMoney: 0
        };
        console.log(params);
        $.ajax({
            url: '/index.php?module=TyunWebBuyServiceClient&action=getSecretKeySurplusMoney',
            type: 'POST',
            dataType: 'json',
            data: params,
            beforeSend: function () {
                $.showLoading('数据获取中');
            },
            success: function (data) {
                $.hideLoading();
                if (data.success) {
                    $('#upgradecost').val(data.money);
                    // 价格+0 其实就是 返回的金额data.money
                    $('#Price').val(FloatAdd(data.money, 0));
                    calcotherprice();
                } else {
                    $.toast(data.Message, 'text');
                }
            }
        });
    }

    $('#container').on("click","#submitotherproduct",function(event){
        var select2check=$('.select2check').filter('input:checked');
        if(select2check.length==0){
            $.toast('请选择另购产品!','text');
            return false;
        }
        $.confirm("确定要添加另购服务？", "", function(){
            $('.otherproductlist').empty();
            var str='';
            var sumprice=0;
            var sumrenewprice=0;
            var buyyear=$('#buyyear').attr('data-values');
            buyyear=buyyear>1?FloatSub(buyyear,1):0;
            $.each(select2check,function(key,value){
                var dprice=FloatMul(parseInt($(value).data('number')),$(value).data('price'))
                var drenewprice=FloatMul(parseInt($(value).data('number')),$(value).data('renewprice'))
                var limit_min = $(value).data('limit_min');
                sumprice=FloatAdd(sumprice,dprice);
                sumrenewprice=FloatAdd(sumrenewprice,drenewprice);
                var outerHTML=value.outerHTML.replace('type="checkbox"','type="hidden"');
                outerHTML=outerHTML.replace('select2check','select3check');
                outerHTML=outerHTML.replace('select2farther','select3farther');
                outerHTML=outerHTML.replace('groupname','group1name');
                var drenewprice=FloatMul(drenewprice,buyyear);
                var dsumprice=FloatAdd(drenewprice,dprice);
                str+='<div class="weui-media-box weui-media-box_appmsg othger_product_'+$(value).data('productid')+'">\n' +
                    '                    <div class="weui-media-box__bd">\n' +
                    '                        <div class="weui-media-box__title">\n' +
                    '                            <div class="weui-cell" style="padding:0px;font-size:16px;">\n' +
                    '                                <div class="weui-cell__bd">\n' +
                    '                                    <div class="button_sp_area" style="vertical-align: middle;">\n' +outerHTML+
                    '                                        <a href="javascript:;" class="weui-btn weui-btn_mini weui-btn_default weui-btn_default_noborder separate_product_name" style="margin-top:0px;background-color: #fff;font-size:16px;padding:0;">'+$(value).data('producttitle')+'</a>\n' +
                    '                                        <a href="javascript:;" class="weui-btn weui-btn_mini weui-btn_default" style="margin-top:0px;">'+$(value).data('specificationstitle')+'</a>\n' +
                    '                                    </div>\n' +
                    '                                </div>\n' +
                    '                            </div>\n' +
                    '                        </div>\n' +
                    '                        <div class="weui-media-box__title">\n' +
                    '                            <div class="weui-cell" style="padding:0;height: 30px;">\n' +
                    '                                <div class="weui-cell__bd">\n' +
                    '                                    <label class="weui-label" style="margin-left:0px;font-size:14px;">购:￥'+parseFloat($(value).data('price')).toFixed(2)+'&nbsp;续:￥'+parseFloat($(value).data('renewprice')).toFixed(2)+'</label>\n' +
                    '                                </div>\n' +
                    '                                <div class="weui-cell__ft">\n' +
                    '                                    <div class="weui-count weui-count_custrom">\n' +
                    '                                        <a class="weui-count__btn weui-count__sub weui-count__left_custrom" data-id="'+$(value).data('productid')+'"></a>\n' +
                    '                                        <input class="weui-count__number  weui-count__current_number'+$(value).data('productid')+'" disabled type="number" value="'+$(value).data('number')+'" readonly="" data-limit_min="'+limit_min+'" style="-webkit-appearance:none !important;border-radius: 0 !important;opacity: 1;">\n' +
                    '                                        <a class="weui-count__btn weui-count__add weui-count__right_custrom" data-id="'+$(value).data('productid')+'"></a>\n' +
                    '                                        <a class="weui-count__btn weui-count__decrease weui-count__decrease_deleted" style="border:none;margin-left:5px;" data-id="'+$(value).data('productid')+'"></a>\n' +
                    '                                    </div>\n' +
                    '                                </div>\n' +
                    '                            </div>\n' +
                    '                        </div>\n' +
                    '                        <div class="weui-media-box__desc">\n' +
                    '                            <div class="weui-cell" style="padding-left:0px;">\n' +
                    '                                <div class="weui-cell__bd">\n' +
                    '                                <div class="weui-count"  style="border:1px solid #fff">\n' +
                    '                                    <a class="" style="color:#000000;font-size:14px;">市场价格：</a>\n' +
                    '                                    <a class="pricesum'+$(value).data('productid')+'" style="color:#000000;font-size:14px;">购:￥'+parseFloat(dprice).toFixed(2)+'+&nbsp;续:￥'+parseFloat(drenewprice).toFixed(2)+'=￥'+parseFloat(dsumprice).toFixed(2)+'</a>\n' +
                    '                                </div>\n' +
                    '                                </div>\n' +
                    '                            </div>\n' +
                    '                        </div>\n' +
                    '                    </div>\n' +
                    '                </div>';

            });
            var price=$('#Price').val();
            price=price>0?price:0;
            $('#otherproductsum').val(sumprice);
            $('#otherproductrenewsum').val(sumrenewprice);
            sumprice=FloatAdd(sumprice,FloatMul(sumrenewprice,buyyear));
            var sumpriceall=FloatAdd(sumprice,price);
            $('#totalmarketprice').val(sumpriceall);
            $('#totalmarketpriceshow').val('￥'+parseFloat(sumpriceall).toFixed(2));
            str+='<div class="weui-media-box weui-media-box_appmsg othger_product_last">\n' +
                '                    <div class="weui-media-box__bd">\n' +
                '                        <div class="weui-media-box__title">\n' +
                '                            <div class="weui-cell" style="padding:0px;">\n' +
                '                                <div class="weui-cell__bd">\n' +
                '                                    <div class="weui-count"  style="border:1px solid #fff">\n' +
                '                                        <a class="" style="color:#000000;font-size:16px;">另购合计：</a>\n' +
                '                                        <a class="pricelast" style="color:#000000;font-size:16px;">￥'+parseFloat(sumprice).toFixed(2)+'</a>\n' +
                '                                    </div>\n' +
                '                                </div>\n' +
                '                            </div>\n' +
                '                        </div>\n' +
                '                    </div>\n' +
                '                </div>';
            $('.otherproductlist').append(str);
            calcotherprice();
            pageManager.back();
        }, function() {
            //取消操作
        });
    });

    /**
     * 单品修改计算价格
     * */
    function calcotherprice(){
        var sumprice=0;
        var sumrenewprice=0;
        var buyyear=$('#buyyear').attr('data-values');
        var sumpriceyear=buyyear>0?1:0;
        var sumrenewpriceyear=buyyear>1?(buyyear-1):0;
        $.each($('.select3check'),function(key,value){
            var id=$(value).data('currentid');
            var temprice=FloatMul(parseInt($(value).data('number')),$(value).data('price'));
            sumprice=FloatAdd(sumprice,temprice);
            var temprenewprice=FloatMul(parseInt($(value).data('number')),$(value).data('renewprice'))
            sumrenewprice=FloatAdd(sumrenewprice,temprenewprice);
            $('.pricesum'+id).html('购:￥'+parseFloat(FloatMul(temprice,sumpriceyear)).toFixed(2)+'+&nbsp;续:￥'+parseFloat(FloatMul(temprenewprice,sumrenewpriceyear)).toFixed(2)+'=￥'+parseFloat(FloatAdd(FloatMul(temprice,sumpriceyear),FloatMul(temprenewprice,sumrenewpriceyear))).toFixed(2));
        });
        $('#otherproductsum').val(sumprice);
        $('#otherproductrenewsum').val(sumrenewprice);
        var price=$('#Price').val();
        price = price.substr(1);
        price=price>0?price:0;


        //var sumallprice=sumprice*sumpriceyear+sumrenewprice*sumrenewpriceyear;
        var sumallprice=FloatAdd(FloatMul(sumprice,sumpriceyear),FloatMul(sumrenewprice,sumrenewpriceyear));
        $('.pricelast').text('￥'+parseFloat(sumallprice).toFixed(2));
        //sumallprice+=parseInt(price);
        sumallprice=FloatAdd(sumallprice,price);
        if(sumallprice<=0){
            sumprice = 0;
        }
        $('#totalmarketprice').val(sumallprice);
        $('#totalmarketpriceshow').val('￥'+parseFloat(sumallprice).toFixed(2));
    }
    /**
     * 套餐修改计算价格
     * */
    function calcpackagePrice(){
        var price=$('#Price').val();
        var sumprice=0;
        var otherproductsum=$('#otherproductsum').val();
        otherproductsum=otherproductsum>0?otherproductsum:0;
        var otherproductrenewsum=$('#otherproductrenewsum').val();
        otherproductrenewsum=otherproductrenewsum>0?otherproductrenewsum:0;
        var buyyear=$('#buyyear').attr('data-values');
        var sumpriceyear=buyyear>0?1:0;
        var sumrenewpriceyear=buyyear>1?(buyyear-1):0;
        //sumprice=parseInt(sumpriceyear)*parseInt(otherproductsum);
        sumprice=FloatMul(parseInt(sumpriceyear),parseInt(otherproductsum));
        sumprice=FloatAdd(sumprice,FloatMul(parseInt(otherproductrenewsum),parseInt(sumrenewpriceyear)));
        $.each($('.select3check'),function(key,value){
            var id=$(value).data('currentid');
            var countnumber=$(value).data('number');
            var spricetemp=$(value).data('price');
            var srenewpricetemp=$(value).data('renewprice');
            spricetemp=FloatMul(spricetemp,parseInt(countnumber));
            srenewpricetemp=FloatMul(FloatMul(srenewpricetemp,parseInt(countnumber)),sumrenewpriceyear);
            $('.pricesum'+id).html('购:￥'+parseFloat(spricetemp).toFixed(2)+'+&nbsp;续:￥'+parseFloat(srenewpricetemp).toFixed(2)+'=￥'+parseFloat(FloatAdd(spricetemp,srenewpricetemp)).toFixed(2));
        });
        $('.pricelast').text('￥'+parseFloat(sumprice).toFixed(2));
        sumprice=FloatAdd(sumprice,price);
        $('#totalmarketprice').val(sumprice);
        $('#totalmarketpriceshow').val('￥'+parseFloat(sumprice).toFixed(2));
    }

    $("#container").on("click","#submitcheckaccount",function (event) {
        var tyunaccount = $("#tyunaccount").val();
        console.log(tyunaccount);
        if(!tyunaccount){
            $.alert("请输入T云账号");
            return;
        };

        $.ajax({
            url: '/index.php?module=TyunWebBuyService&action=checkOldTyunAccount',
            type: 'POST',
            dataType: 'json',
            data:{
                "tyunaccount":tyunaccount,
                "mobile":$("#mobile").val()
            },
            beforeSend:function(){
                $.showLoading();
            },
            success: function (data) {
                $.hideLoading();
                console.log(data);
                if (data.success==1) {
                    var tyunusercodes = $("#tyunusercode option");
                    var flag = false;
                    tyunusercodes.each(function (k,v) {
                        if($(v).val()==data.accountid){
                            $.alert("T云账号重复");
                            flag = true;
                        }
                    });
                    if(!flag){
                        $(".tyuncode_option").remove();
                        $("#oldaccountid").val(data.accountid);
                        $("#oldaccountidowenid").val(data.userid);
                        $("input[name=oldaccountid_display]").val(data.companyName);
                        $("#tyunusercode").find("option").removeAttr("selected");
                        var selected = 'selected';
                        $("#clickhide").hide();
                        $('#tyunusercode').append('<option class="tyuncode_option" value="'+data.accountid+'" '+selected+'>'+data.loginName+'</option>');
                        getAllCategory();
                        initAuthType();
                        // getUserRenewProductInfo();
                        pageManager.go('home');
                        return;
                    }
                    return;

                }else{
                    $.alert(data.msg);
                }
            }
        });

    });

    $("#container").on("click","#other_tyunaccount",function (event) {
        var accountid = $("input[name=accountid_display]").val();
        if(!accountid){
            $.toast('请先输入客户名称','forbidden');
            return;
        }
        var mobile = $("#mobile").val();
        if(!mobile){
            $.toast('请先输入客户手机','forbidden');
            return;
        }

        var mobilevcode = $("#mobilevcode").val();
        var signaturetype = $("#signaturetype").val();
        if (!mobilevcode && signaturetype == 'papercontract') {
            $.toast('请先输入验证码', 'forbidden');
            return;
        }

        // $("#tyunusercode")[0].options.length=0;
        var servicecontractsid=$('input[name="servicecontractsid"]').val();
        var servicecontractsid_display=$('input[name="servicecontractsid_display"]').val();
        var accountid=$('input[name="accountid"]').val();
        var accountid_display=$('input[name="accountid_display"]').val();
        var mobile=$('#mobile').val();
        var mobilevcode=$('#mobilevcode').val();
        var classtype=$('#classtype').val();
        var agents=$('#instanceagents').data('value');
        if(checkGetUserCode()){
            return false;
        }
        $.ajax({
            url: '/index.php?module=TyunWebBuyService&action=getTyunUserCode',
            type: 'POST',
            dataType: 'json',
            data:{"servicecontractsid":servicecontractsid,
                "servicecontractsid_display":servicecontractsid_display,
                "accountid":accountid,
                "accountid_display":accountid_display,
                "mobile":mobile,
                "mobilevcode":mobilevcode,
                "classtype":classtype,
                "agents":agents
            },
            beforeSend:function(){
                $.showLoading();
            },
            success: function (data) {
                $.hideLoading();
                if (data.success==1) {
                    if(data.data.length>0){
                        $("#tyunusercode")[0].options.length=0;
                        $("#oldaccountid").val("");
                        $("#oldaccountidowenid").val("");
                        $("input[name=oldaccountid_display]").val("");
                    }
                    $.each(data.data,function(key,value){
                        var selected='';
                        if(key==0){
                            selected='selected';
                        }
                        $("#clickhide").hide();
                        $("#tyunusercode").append('<option value="'+value.id+'" '+selected+'>'+value.loginName+'</option>');
                    });
                    // getAllCategory();
                }
                pageManager.go('checkaccount');

            }
        });
    });

    $('#container').on("blur",".weui-count__number",function(event){
        var id=$(this).data('fartherid1');
        console.log(id)
        if(!$('.select2farther'+id).attr('checked')){
            return false;
        }
        var countnumber=parseInt($('.countnumber'+id).val());
        var limit_min = $('.countnumber'+id).data('limit_min');
        if(limit_min>countnumber){
            $.toast('最少购买数量为'+limit_min,"text");
            return false;
        }
        var uselimit=$('.select2farther'+id).attr('data-uselimit');
        uselimit=uselimit>0?uselimit:0;
        var userbuycount=$('.select2farther'+id).attr('data-userbuycount');
        if(uselimit>0){
            var tempadd=FloatAdd(countnumber,parseInt(userbuycount));
            if(tempadd>=uselimit)
                $.toast('哎哟，不能再添加了哦',"text");
            return false;
        }
        $('.countnumber'+id).val(countnumber);
        $('.select2farther'+id).attr('data-number',countnumber);
        var price=$('.select2farther'+id).data('price');
        if(price>0){
            $('.price'+id).text('￥'+parseFloat(FloatMul(price,parseInt(countnumber))).toFixed(2));
        }
        return false;
    });

    function renewDomainPage() {
        //续费域名
        var str2 ='';
        if((typeof domains)=='undefined') {
            return;
        }
        if( domains.length>0) {
            var str2 = '<div class="weui-cells weui-cells_checkbox">\n' +
                '                <div class="weui-cell weui-check__label" style="padding-left:0;display: flex;justify-content: space-between;">\n' +
                '                    <label style="display: flex;">' +
                '                    <div class="weui-cell__hd">\n' +
                '                    </div>\n' +
                '                    <div class="weui-cell__bd">\n' +
                '                        <p>  续费套餐内域名权益</p>\n' +
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
    $('#container').on("click",".packSpecificationList2",function(event){
        var currentchecked=$(this).attr('checked');
        console.log(currentchecked);
        var k = $(this).data('key');
        if(!currentchecked){
            $(this).prop("checked",true);
            return;
        }

        $.each($('.packSpecificationList2'),function(key,value){
            if(currentchecked&&(k==key)){
                console.log(1);
                $(value).prop("checked",true);
            }else{
                console.log(2);
                $(value).prop('checked',false);
            }
        });
        // calPrice();
        // checkProductValue();
    });

    $('#container').on("click",".packListshow2",function(event){
        var currentchecked=$(this).attr('data-flag');
        if(currentchecked==1){
            $(this).attr('data-flag',2);
            $(this).removeClass('icon-jian-tianchong');
            $(this).addClass('icon-jia-tianchong');
            $('.packshow2').hide();
        }else{
            $(this).removeClass('icon-jia-tianchong');
            $(this).addClass('icon-jian-tianchong');
            $(this).attr('data-flag',1);
            $('.packshow2').show();
        }
    });

    function initAuthType(){
        var authenticationtype = $("#authenticationtype").val();
        var authenticationtypechecked =  $("#tyunusercode").find('option:checked').data("authenticationtype");
        if(authenticationtypechecked!=undefined && authenticationtypechecked!=-1){
            $("#authenticationtype option").eq(authenticationtypechecked).prop("selected",true);
            $("#authenticationtype").attr('disabled',true);
        }else {
            $("#authenticationtype").removeAttr('disabled');
        }
    }
    $("#container").on("change","#tyunusercode",function () {
        initAuthType();
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
                        i++;
                        $("#contactname").append('<option value="'+value.linkname+'" data-mobile="'+value.mobile+'"'+selected+'>'+value.linkname+'</option>');
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

    $("#container").on('change',"#contactname",function () {
        var mobile = $("#contactname").find("option:checked").data('mobile');
        $("#elereceivermobile").val(mobile);
    });

    //由电子合同页面返回上一页面
    $("#container").on('click','#econtractback',function () {
        pageManager.go('selectproduct');
    });

    //控制提交按钮
    window.econtractLoad=function(){
        var str2 = '<a class="weui-btn" id="electronsubmitfrom" href="javascript:void(0);" style="background-color: rgb(73, 148, 242);color:rgb(255, 255, 255);font-size:18px;width:70%;">签署合同并提交订单</a>\n';
        $("#econtractback").after(str2);
        var contractid = $("#contractid").data('value');
        $("#ifmcontent").attr('src','/index.php?module=TyunWebBuyService&action=pdf&contractid='+contractid);
    };


    $("#container").on('input', 'input[name="elereceivermobile"]', function () {
        initNextStepButton();
    });
    $("#container").on('change', '#contactname', function () {
        initNextStepButton();
    });

    function initNextStepButton() {
        var tyunusercode = $("#tyunusercode").val();
        var accountid = $("#accountid").val();
        var signaturetype = $("#signaturetype").val();
        packageSpecificationList = window.packageSpecificationList;
        if(packageSpecificationList==undefined || packageSpecificationList==false){
            $('#nextstep').css({"background-color": '#999999'});
            return;
        }
        if (signaturetype == 'eleccontract') {
            var elereceivermobile = $("#elereceivermobile").val();
            var contactname = $("#contactname").val();
            var ismobile = false;
            if (elereceivermobile && elereceivermobile.match(/^((1[3-9])+\d{9})$/)) {
                ismobile = true;
            }
            if (tyunusercode && accountid && elereceivermobile && contactname && ismobile) {
                $('#nextstep').css({"background-color": '#0B92D9'});
                return;
            }
        } else {
            var servicecontractsid_display = $("input[name=servicecontractsid_display]").val();
            var mobilevcode = $("#mobilevcode").val();

            if (tyunusercode && accountid && servicecontractsid_display && mobilevcode) {
                $('#nextstep').css({"background-color": '#0B92D9'});
                return;
            }
        }

        $('#nextstep').css({"background-color": '#999999'});
        return;
    }

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
                    }
                }
            );
        }
        initNextStepButton();
    });

    //电子合同下单预览
    $('#container').on('click', '#electronnextstep', function () {
        var submitid = $('#submitid').attr('data-value');
        if (submitid != 2) {
            return false;
        }
        $("#templateid").attr('data-value', 0);
        //todo 匹配合同接口参数待定

        var producttype= $("#producttype").val();
        var productclasstwo = $("#buyproduct").find("option:checked").text();
        var productclasstwovalues=$("#buyproduct").val();
        if(productclasstwovalues==undefined){
            $.toast('请选择要购买的套餐或单品',"text");
            return false;
        }
        var templateParams = {
            "productCode":[productclasstwovalues],
            "servicecontractstype":4,
            "isPackage":1,
            "orderType":'common'
        };
        var productCode = [productclasstwovalues];
        var isPackage = 1;

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
                    if (datas.length > 1 || datas.length == 0) {
                        $.alert('系统没有匹配到电子合同模板或者匹配的不是唯一的合同模板，请联系管理员', '提示');
                        return;
                    }
                    var templateId = datas[0]['templateId'];
                    $("#templateid").attr('data-value',templateId);

                    var servicecontractsid = $('input[name="servicecontractsid"]').val();
                    var servicecontractsid_display = $('input[name="servicecontractsid_display"]').val();
                    var accountid = $('input[name="accountid"]').val();
                    var oldaccountid = $('input[name="oldaccountid"]').val();
                    var accountid_display = $('input[name="accountid_display"]').val();
                    var oldaccountid_display = $('input[name="oldaccountid_display"]').val();
                    var mobile = $('#mobile').val();
                    var mobilevcode = $('#mobilevcode').val();
                    var classtype = $('#classtype').val();
                    var tyunusercode = $('#tyunusercode').val();//用户ID
                    var tyunusercodename = $('#tyunusercode').find("option:checked").text();//用户名
                    var classtyperenew = $('#classtyperenew').val();//产品分类
                    var classtyperenewname = $('#classtyperenew').find("option:checked").text();//产品分类名称
                    var oldproductnameid = $('#oldproductnameid').val();//原版本ID
                    var oldproductname_display = $('#oldproductname_display').val();//原版本
                    var buyproduct = $('#buyproduct').val();//降级版本ID
                    var buyproductname = $('#buyproduct').find("option:checked").text();//降级版本名称
                    var upgardecycle = $('#upgardecycle').val();//降级周期
                    var currentdate = (new Date()).getFullYear() + '-' + ((new Date()).getMonth() + 1) + '-' + (new Date()).getDate();
                    var separateproductname_display = '';
                    var Price = $('#Price').val();//差价合计
                    var buyyear = $('#buyyear').val();//降级年限
                    buyyear = buyyear > 0 ? buyyear : 0;
                    var servicetotal = parseFloat($('#servicetotal').val()).toFixed(2);//合同金额
                    var agents = $('#instanceagents').data('value');
                    var activacode = $("#activacode").data('value');
                    var oproductid = $("#oproductid").data('value');
                    var buydate=$('#buydate').val();
                    if($('#buydateshow').data('value')!=1){
                        var buydatestr='';
                    }else{
                        if(buydate==''){
                            $.toast('购买日期必选',"text");
                            return false;
                        }
                        var buydatestr=$('#buydateshow').data('value')!=1?'':'<div class="weui-form-preview__item">' +
                            '<label class="weui-form-preview__label">降级时间</label>' +
                            '<span class="weui-form-preview__value">'+buydate+'</span>' +
                            '</div>'
                    }
                    //另购服务start
                    var productid = [];
                    var categoryid = [];
                    var number = [];
                    var id = [];
                    var price = [];
                    var renewprice = [];
                    var marketprice = [];
                    var marketrenewprice = [];
                    var unit = [];
                    var specificationstitle = [];
                    var producttitle = [];
                    $.each($('.select3check'), function (keyt, valuet) {
                        producttitle[keyt] = $(valuet).data('producttitle');
                        productid[keyt] = $(valuet).data('productid');
                        categoryid[keyt] = $(valuet).data('categoryid');
                        number[keyt] = $(valuet).data('number');
                        id[keyt] = $(valuet).data('id');
                        price[keyt] = $(valuet).data('price');
                        renewprice[keyt] = $(valuet).data('renewprice');
                        marketprice[keyt] = $(valuet).data('marketprice');
                        marketrenewprice[keyt] = $(valuet).data('marketrenewprice');
                        unit[keyt] = $(valuet).data('unit');
                        specificationstitle[keyt] = $(valuet).data('specificationstitle');
                        separateproductname_display += $(valuet).data('producttitle') + '(' + $(valuet).data('number') + ') ';
                    });
                    //另购服务end

                    //域名续费权益
                    var chooseuserproduct = [];
                    var packSpecificationList2 = $(".packSpecificationList2").filter('input:checked');
                    $.each(packSpecificationList2, function (k2, v2) {
                        chooseuserproduct.push(parseInt($(v2).data('productspecificationsid')));
                    });


                    var str = '<div class="weui-form-preview">' +
                        '<div class="weui-form-preview__bd">' +
                        '<div class="weui-form-preview__item">' +
                        '<label class="weui-form-preview__label">T云账号</label>' +
                        '<span class="weui-form-preview__value">' + tyunusercodename + '</span>' +
                        '</div>' +
                        '<div class="weui-form-preview__item">' +
                        '<label class="weui-form-preview__label">原版本</label>' +
                        '<span class="weui-form-preview__value">' + oldproductname_display + '</span>' +
                        '</div>' +
                        '<div class="weui-form-preview__item">' +
                        '<label class="weui-form-preview__label">降级版本</label>' +
                        '<span class="weui-form-preview__value">' + buyproductname + '</span>' +
                        '</div>' +
                        '<div class="weui-form-preview__item">' +
                        '<label class="weui-form-preview__label">另购单品</label>' +
                        '<span class="weui-form-preview__value">' + separateproductname_display + '</span>' +
                        '</div>' +
                        '<div class="weui-form-preview__item">' +
                        '<label class="weui-form-preview__label">合同金额</label>' +
                        '<span class="weui-form-preview__value">￥' + servicetotal + '</span>' +
                        '</div>' +
                        '<div class="weui-form-preview__item">' +
                        '<label class="weui-form-preview__label">降级年限</label>' +
                        '<span class="weui-form-preview__value">' + buyyear + '年</span>' +
                        '</div>' +
                        '<div class="weui-form-preview__item">' +
                        '<label class="weui-form-preview__label">降级周期</label>' +
                        '<span class="weui-form-preview__value">' + upgardecycle + '</span>' +
                        '</div>' +
                        '<div class="weui-form-preview__item">' +
                        '<label class="weui-form-preview__label">客户名称</label>' +
                        '<span class="weui-form-preview__value">' + accountid_display + '</span>' +
                        '</div>' +
                        '<div class="weui-form-preview__item">' +
                        '<label class="weui-form-preview__label">老客户名称</label>' +
                        '<span class="weui-form-preview__value">' + oldaccountid_display + '</span>' +
                        '</div>' +
                        '<div class="weui-form-preview__item">' +
                        '<label class="weui-form-preview__label">降级时间</label>' +
                        '<span class="weui-form-preview__value">' + currentdate + '</span>' +
                        '</div>' +
                        '</div>' +
                        '</div>';
                    var $stru = ''
                    var groupid = [];
                    var title = [];
                    var specificationid = [];
                    var specificationtitle = [];
                    $.each($('.tobeconfiguredproducts'), function (keyt, valuet) {
                        if ($(valuet).attr('checked')) {
                            title[keyt] = $(valuet).data('title');
                            specificationid[keyt] = $(valuet).data('id');
                            groupid[keyt] = $(valuet).data('groupid');
                            specificationtitle[keyt] = $(valuet).data('specificationtitle');
                        }

                    });

                    var buyproductss = $('#buyproduct').find("option:checked");
                    packageprice = buyproductss.data('price');
                    packagerenewprice = buyproductss.data('renewprice');
                    packagemarketprice = buyproductss.data('marketprice');
                    packagemarketrenewprice = buyproductss.data('marketrenewprice');
                    var authenticationtype = $("#authenticationtype").val();
                    var companyid = $("#owncompany").val();
                    var elereceiver = $("#contactname").val();
                    var elereceivermobile = $("#elereceivermobile").val();
                    var templateId = $("#templateid").data('value');
                    var params={"servicecontractsid":servicecontractsid,
                        "servicecontractsid_display":servicecontractsid_display,
                        "accountid":accountid,
                        "oldaccountid":oldaccountid,
                        "accountid_display":accountid_display,
                        "mobile":mobile,
                        "mobilevcode":mobilevcode,
                        "classtype":classtype,
                        "type":1,
                        "buyyear":buyyear,
                        "agents":agents,
                        "servicetotal":servicetotal,
                        "tyunusercode":tyunusercode,
                        "tyunusercodetext":tyunusercodename,
                        "tyunusercodeid":tyunusercode,
                        "oldcustomerid":oldaccountid,
                        "oldcustomername":oldaccountid_display,
                        "oldproductid":oproductid,
                        "oldproductname":oldproductname_display,
                        "activacode":activacode,
                        "productclassonevalues":classtyperenew,
                        "productclasstwovalues":oldproductnameid,
                        "chooseuserproduct":chooseuserproduct,
                        "packageprice":packageprice,
                        "packagerenewprice":packagerenewprice,
                        "packagemarketprice":packagemarketprice,
                        "packagemarketrenewprice":packagemarketrenewprice,
                        "activitymodel":3,
                        "authenticationtype":authenticationtype,

                        'companyid':companyid,
                        "elereceiver":elereceiver,
                        "templateid":templateId,
                        "elereceivermobile":elereceivermobile,
                        "packagename":productclasstwo,
                        "orderType":producttype,
                        'productCode':productCode,
                        "isPackage":isPackage,
                    };
                    params['groupid']=groupid;
                    params['buyproduct']=buyproduct;
                    params['title']=title;
                    params['categoryid']=classtyperenew;
                    params['classtyperenewname']=classtyperenewname;
                    params['oldproductnameid']=oldproductnameid;
                    params['oldproductname_display']=oldproductname_display;
                    params['specificationid']=specificationid;
                    params['specificationtitle']=specificationtitle;
                    params['price']=Price;
                    params['servicetotal']=servicetotal;

                    //另购服务start
                    params['producttitle']=producttitle;
                    params['productid']=productid;
                    params['categoryids']=categoryid;
                    params['number']=number;
                    params['id']=id;
                    params['price']=price;
                    params['renewprice']=renewprice;
                    params['marketprice']=marketprice;
                    params['marketrenewprice']=marketrenewprice;
                    params['unit']=unit;
                    params['specificationstitle']=specificationstitle;
                    //另购服务end

                    console.log(params);
                    $.confirm(str, "请核对订单信息", function() {
                        $.ajax({
                                url: '/index.php?module=TyunWebBuyService&action=preOrder',
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
                } else {
                    $.toast(data.msg, "forbidden");
                    return;
                }
            }
        })
    });
    //电子合同下单
    $('#container').on('click','#electronsubmitfrom',function(){
        var totalmarketprice = $("#totalmarketprice").val();
        var servicetotal = $("#servicetotal").val();

        var params = {
            "totalmarketprice": totalmarketprice,
            "servicetotal": servicetotal
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
                    var elereceiver = $("#contactname").val();
                    elereceivermobilestr = '<span style="border-bottom: 1px solid black;font-weight: bold;">'+elereceivermobile+'</span>';

                    var prestr = '';
                    var isverify = 0;
                    if(discountflag ){
                        prestr = '该电子合同金额低于可折扣金额，需要经过相关人员审批后，系统自动发起合同签署短信至接收人手机'+elereceivermobilestr+'(作为联系人接收短信)';
                        isverify = 1;
                    }else{
                        prestr = '确定发起合同签署短信至接收人手机'+elereceivermobilestr+'(作为联系人接收短信)';
                    }

                    var str = '<div class="weui-form-preview">' +
                        '<div class="weui-form-preview__bd">' +
                        '<div class="weui-form-preview__item">' +
                        '<span class="weui-form-preview__value" style="text-align: center;">' + prestr + '</span>' +
                        '</div>' +
                        '</div>' +
                        '</div>';
                    $.confirm(str, "提醒", function () {
                        var servicecontractsid=$('input[name="servicecontractsid"]').val();
                        var servicecontractsid_display=$('input[name="servicecontractsid_display"]').val();
                        var accountid=$('input[name="accountid"]').val();
                        var oldaccountid=$('input[name="oldaccountid"]').val();
                        var accountid_display=$('input[name="accountid_display"]').val();
                        var oldaccountid_display=$('input[name="oldaccountid_display"]').val();
                        var mobile=$('#mobile').val();
                        var mobilevcode=$('#mobilevcode').val();
                        var classtype=$('#classtype').val();
                        var tyunusercode=$('#tyunusercode').val();//用户ID
                        var tyunusercodename=$('#tyunusercode').find("option:checked").text();//用户名
                        var classtyperenew=$('#classtyperenew').val();//产品分类
                        var classtyperenewname=$('#classtyperenew').find("option:checked").text();//产品分类名称
                        var oldproductnameid=$('#oldproductnameid').val();//原版本ID
                        var oldproductname_display=$('#oldproductname_display').val();//原版本
                        var buyproduct=$('#buyproduct').val();//降级版本ID
                        var buyproductname=$('#buyproduct').find("option:checked").text();//降级版本名称
                        var upgardecycle=$('#upgardecycle').val();//降级周期
                        var currentdate=(new Date()).getFullYear()+'-'+((new Date()).getMonth()+1)+'-'+(new Date()).getDate();
                        var separateproductname_display = '';
                        var Price=$('#Price').val();//差价合计
                        var buyyear=$('#buyyear').val();//降级年限
                        buyyear=buyyear>0?buyyear:0;
                        var servicetotal=parseFloat($('#servicetotal').val()).toFixed(2);//合同金额
                        var agents=$('#instanceagents').data('value');
                        var activacode = $("#activacode").data('value');
                        var oproductid = $("#oproductid").data('value');
                        var companyid = $("#owncompany").val();
                        var elereceiver = $("#contactname").val();
                        var elereceivermobile = $("#elereceivermobile").val();
                        var contractid = $("#contractid").data('value');
                        var invoicecompany = $("#owncompany").find("option:checked").text();
                        var invoicecompanyid = $("#owncompany").val();
                        var signaturetype = $("#signaturetype").val();
                        var paycode = $("#paycode").data('value');
                        var authenticationtype = $("#authenticationtype").val();
                        var templateId = $("#templateid").data('value');
                        var productclasstwo = $("#buyproduct").find("option:checked").text();
                        var eleccontracturl = $("#eleccontracturl").data("value");

                        //另购服务start
                        var productid=[];
                        var categoryid=[];
                        var number=[];
                        var id=[];
                        var price=[];
                        var renewprice=[];
                        var marketprice=[];
                        var marketrenewprice=[];
                        var unit=[];
                        var specificationstitle=[];
                        var producttitle=[];
                        $.each($('.select3check'),function(keyt,valuet){
                            producttitle[keyt]=$(valuet).data('producttitle');
                            productid[keyt]=$(valuet).data('productid');
                            categoryid[keyt]=$(valuet).data('categoryid');
                            number[keyt]=$(valuet).data('number');
                            id[keyt]=$(valuet).data('id');
                            price[keyt]=$(valuet).data('price');
                            renewprice[keyt]=$(valuet).data('renewprice');
                            marketprice[keyt]=$(valuet).data('marketprice');
                            marketrenewprice[keyt]=$(valuet).data('marketrenewprice');
                            unit[keyt]=$(valuet).data('unit');
                            specificationstitle[keyt]=$(valuet).data('specificationstitle');
                        });
                        //另购服务end

                        //域名续费权益
                        var chooseuserproduct = [];
                        var packSpecificationList2 = $(".packSpecificationList2").filter('input:checked');
                        $.each(packSpecificationList2,function (k2, v2) {
                            chooseuserproduct.push(parseInt($(v2).data('productspecificationsid')));
                        });

                        var $stru=''
                        var groupid=[];
                        var title=[];
                        var specificationid=[];
                        var specificationtitle=[];
                        $.each($('.tobeconfiguredproducts'),function(keyt,valuet){
                            if($(valuet).attr('checked')){
                                title[keyt]=$(valuet).data('title');
                                specificationid[keyt]=$(valuet).data('id');
                                groupid[keyt]=$(valuet).data('groupid');
                                specificationtitle[keyt]=$(valuet).data('specificationtitle');
                            }

                        });

                        var buyproductss = $('#buyproduct').find("option:checked");
                        packageprice = buyproductss.data('price');
                        packagerenewprice = buyproductss.data('renewprice');
                        packagemarketprice = buyproductss.data('marketprice');
                        packagemarketrenewprice = buyproductss.data('marketrenewprice');

                        var params={"servicecontractsid":servicecontractsid,
                            "servicecontractsid_display":servicecontractsid_display,
                            "accountid":accountid,
                            "oldaccountid":oldaccountid,
                            "accountid_display":accountid_display,
                            "mobile":mobile,
                            "mobilevcode":mobilevcode,
                            "classtype":classtype,
                            "type":1,
                            "buyyear":buyyear,
                            "agents":agents,
                            "servicetotal":servicetotal,
                            "tyunusercode":tyunusercode,
                            "tyunusercodetext":tyunusercodename,
                            "tyunusercodeid":tyunusercode,
                            "oldcustomerid":oldaccountid,
                            "oldcustomername":oldaccountid_display,
                            "oldproductid":oproductid,
                            "oldproductname":oldproductname_display,
                            "activacode":activacode,
                            "productclassonevalues":classtyperenew,
                            "productclasstwovalues":oldproductnameid,
                            "chooseuserproduct":chooseuserproduct,
                            "packageprice":packageprice,
                            "packagerenewprice":packagerenewprice,
                            "packagemarketprice":packagemarketprice,
                            "packagemarketrenewprice":packagemarketrenewprice,
                            "activitymodel":3,
                            "authenticationtype":authenticationtype,


                            'companyid':companyid,
                            "elereceiver":elereceiver,
                            "templateid":templateId,
                            "elereceivermobile":elereceivermobile,
                            "packagename":productclasstwo,
                            "totalmarketprice":totalmarketprice,
                            "paycode":paycode,
                            "contractid":contractid,
                            "isverify":isverify,
                            "invoicecompany":invoicecompany,
                            "signaturetype":signaturetype,
                            "invoicecompanyid":invoicecompanyid,
                            "ispackage":1,
                            "eleccontracturl":eleccontracturl,
                        };
                        params['groupid']=groupid;
                        params['buyproduct']=buyproduct;
                        params['title']=title;
                        params['categoryid']=classtyperenew;
                        params['classtyperenewname']=classtyperenewname;
                        params['oldproductnameid']=oldproductnameid;
                        params['oldproductname_display']=oldproductname_display;
                        params['specificationid']=specificationid;
                        params['specificationtitle']=specificationtitle;
                        params['price']=Price;
                        params['servicetotal']=servicetotal;

                        //另购服务start
                        params['producttitle']=producttitle;
                        params['productid']=productid;
                        params['categoryids']=categoryid;
                        params['number']=number;
                        params['id']=id;
                        params['price']=price;
                        params['renewprice']=renewprice;
                        params['marketprice']=marketprice;
                        params['marketrenewprice']=marketrenewprice;
                        params['unit']=unit;
                        params['specificationstitle']=specificationstitle;
                        //另购服务end

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
                                        $.alert(data.msg, function() {
                                            location.href=data.url;
                                        });
                                    }else{
                                        $.toast(data.msg,'text');
                                    }
                                }
                            }
                        );

                    })
                }
            }
        })
    });

    function checkVerifyCode(){
        var mobile = $("#mobile").val();
        var mobilevcode = $("#mobilevcode").val();
        $.ajax({
            url: '/index.php?module=TyunWebBuyService&action=checkVerifyCode',
            type: 'POST',
            dataType: 'json',
            async:false,
            data: {
                "mobile": mobile,
                "code": mobilevcode
            },
            beforeSend: function () {
                $.showLoading('正在验证验证码');
            },
            success: function (data) {
                $.hideLoading();
                if (data.success) {
                    $('#checkVerifyCode').attr('data-value',1);
                    $('#nextstep').trigger('click');
                }else{
                    $.toast(data.message,"text");
                }
            }
        });
    }
    $("#container").on('blur','#mobilevcode',function () {
        $('#checkVerifyCode').attr('data-value',0);
        initNextStepButton();
    });
});
