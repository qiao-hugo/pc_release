/**
 * Created by jf on 2015/9/11.
 * Modified by bear on 2016/9/7.
 */
$(function () {
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
            if(/click/.test(arguments[0]) && typeof arguments[1] == 'function' && supportTouch){ // ???????????????touch??????????????????click??????
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
        // .container ????????? overflow ??????, ?????? Android ?????????????????????????????????, ??????????????????????????? bug
        // ?????? issue: https://github.com/weui/weui/issues/15
        // ????????????:
        // 0. .container ?????? overflow ??????, ?????? demo ????????????????????????
        // 1. ?????? http://stackoverflow.com/questions/23757345/android-does-not-correctly-scroll-on-input-focus-if-not-body-element
        //    Android ?????????, input ??? textarea ???????????????, ???????????????
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
            $('input, textarea').not(this).on("f??cus",function(){$(this).blur();});
        })
        $('input, textarea').on('blur', function() {
            $('input, textarea').not('.readonlyflag').removeAttr("readonly");
            $('input, textarea').not('.readonlyflag').removeAttr("unselectable");
            $('input, textarea').not('.readonlyflag').off("f??cus");
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
            window.location.href="/index.php?module=TyunWebBuyServiceClient&action=renew";
        }else{
            window.location.href="/index.php?module=TyunWebBuyService&action=renew";
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
                $.toast('????????????????????????','text');
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
            obj.innerHTML="???????????????";
            countdown = 60;
            return false;
        }else{
            obj.setAttribute("disabled", true);
            obj.innerHTML ="????????????(" + countdown + "s)";
            countdown--;
        }
        setTimeout(function() {
            settime(obj) },1000);
    }
    $('#container').on('click','#mobilevcodebtn',function(){
        var mobile = $("#mobile").val();
        if(mobile == ''){
            $.toast("??????????????????", "forbidden");
            return false;
        }
        var reg = /^(((13[0-9]{1})|(14[0-9]{1})|(15[0-9]{1})|(16[0-9]{1})|(19[0-9]{1})|(17[0-9]{1})|(18[0-9]{1}))+\d{8})$/;
        if (!reg.test(mobile)) {
            $.toast("??????????????????", "forbidden");
            return false;
        }
        var accountid=$('#accountid').val();
        if(accountid==0){
            $.toast("????????????", "forbidden");
            return false;
        }
        settime(this);
    });
    function sendMobileVerify(mobile){
        $.getJSON('/index.php?module=TyunWebBuyService&action=getMobileVerify&mobile='+mobile,function(res){
            if(res.success==1){
                $.toast('??????????????????','text');
            }else{
                $.toast(res.msg,'text');
            }
            $('#nextstep').css({"background-color":'#999999'});
            // $('#tyunusercodeonoff').val(1);
        });
    }
    $('#container').on("blur",'#tyunusercode,input[name="accountid_display"],input[name="servicecontractsid_display"],#mobile,#mobilevcode,#classtyperenew,#servicetotal',function(event){
        window.scroll(0,0);
    });
    $('#container').on('click','#clickhide',function(event) {
        var thisInstance=this;
        $("#tyunusercode")[0].options.length=0;
        var servicecontractsid=$('input[name="servicecontractsid"]').val();
        var servicecontractsid_display=$('input[name="servicecontractsid_display"]').val();
        var accountid=$('input[name="accountid"]').val();
        var accountid_display=$('input[name="accountid_display"]').val();
        var mobile=$('#mobile').val();
        var mobilevcode=$('#mobilevcode').val();
        var signaturetype = $("#signaturetype").val();
        var productclassonecollege = $("#productclassonecollege").data("values");
        if(checkGetUserCode(1)){
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
                "classtype":"renew",
                "signaturetype":signaturetype,
                'categoryID':productclassonecollege
            },
            beforeSend:function(){
                $.showLoading();
            },
            success: function (data) {
                $.hideLoading();
                if (data.success==1) {
                    var loginName='';
                    $.each(data.data,function(key,value){
                        var selected='';
                        if(key==0){
                            selected='selected';
                            loginName=value.loginName;
                        }
                        $("#tyunusercode").append('<option value="'+value.id+'" '+selected+'  data-authenticationType="'+value.authenticationType+'">'+value.loginName+'</option>');
                    });
                    // ??????????????? ??????????????????????????????
                    /*getAllCategory();*/
                    var classtyperenew=$("#productclassonecollege").data("values");
                    var classtyperename=$("#productclassonecollege").val();
                    $("#classtyperenew").val(classtyperenew);
                    $("#classtyperename").val(classtyperename);
                    // ??????????????????????????????
                    getUserRenewProductInfo();
                    $(thisInstance).hide();
                }else{
                    $.toast(data.msg,"text");
                }
            }
        });
    });
    $('#container').on('click','#nextstep',function(){

        if($('#tyunusercode').val()==''){
            return false;
        }
        var checkTyunExistBuyReturnT=$('#checkTyunExistBuyReturn').attr('data-value');
        if(checkTyunExistBuyReturnT!=1){
            checkTyunExistBuy();
            return false;
        }
        var signaturetype = $("#signaturetype").val();
        if(signaturetype=='papercontract'){
            var isService = $("input[name='isService']").val();
            if(isService!=1){
                var accountidowenid=$('#accountidowenid').val();
                var contractowenid=$('#contractowenid').val();
                if(accountidowenid!=contractowenid || accountidowenid=='' || contractowenid==''){
                    var accountidowenname=$('#accountidowenname').val();
                    var contractowenname=$('#contractowenname').val();
                    $.toast("???????????????:"+accountidowenname+"<br>???????????????:"+contractowenname+"<br>?????????????????????????????????????????????","text");
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
        //window.selectproducttitle=$('input[name="accountid_display"]').val();
        //sessionStorage.accountid_display=$('input[name="accountid_display"]').val();
        var tyunusercodeonoff=$('#tyunusercodeonoff').val();

        var mobile = $("#mobile").val();
        $("#lastelereceivermobile").attr('data-value',mobile);

        if(tyunusercodeonoff==1){
            return false;
        }else{
            if(checkGetUserCode()){
                return false;
            }
            var producttype  = $("#producttype").val();
            window.selectproducttitle=$('input[name="accountid_display"]').val();
            //???????????????????????? ?????????
            var classtyperenew=$("#classtyperenew").val();
            if(classtyperenew==7||classtyperenew==9){
                // ??????????????????????????? ??????????????????
                var productclassonecollege=$("#productclassonecollege").data("values");
                if((productclassonecollege==7||productclassonecollege==9) && signaturetype=='eleccontract'){
                    $.toast('??????????????????????????????????????????????????????????????????????????????',"text");
                    return false;
                }
                pageManager.go('collegeselectproduct');
            }else{
                if(producttype == 'activity'){
                    if(!$("#canviewactivity").data('value')){
                        $.toast("???????????????????????????","text");
                        return false;
                    }
                    window.activityproducttitle=$('input[name="accountid_display"]').val();
                    pageManager.go('selectactivity');
                    return;
                }
                pageManager.go('selectproduct');
            }
            sessionStorage.servicecontractsid=$('input[name="servicecontractsid"]').val();
            sessionStorage.servicecontractsid_display=$('input[name="servicecontractsid_display"]').val();
            sessionStorage.accountid=$('input[name="accountid"]').val();
            sessionStorage.accountid_display=$('input[name="accountid_display"]').val();
            sessionStorage.mobile=$('#mobile').val();
            sessionStorage.mobilevcode=$('#mobilevcode').val();
        }
    });
    function checkGetUserCode(params){
        var servicecontractsid=$('input[name="servicecontractsid"]').val();
        var accountid=$('input[name="accountid"]').val();
        var checkmobilevcode=$('#checkmobilevcode').val();
        var checkmobile=$('#checkmobile').val();
        var mobile=$('#mobile').val();
        var mobilevcode=$('#mobilevcode').val();
        var msg='';
        var classtyperenew=$('#classtyperenew').val();
        // var classtyperenew=$('#classtyperenew').data('values');
        var oldproductname_display=$('#oldproductname_display').val();
        var signaturetype = $("#signaturetype").val();

        var checkflag=true;
        do{
            if(accountid<=0){
                msg='?????????????????????';
                break;
            }
            if(mobile==''){
                msg='?????????????????????????????????';
                break;
            }

            if(signaturetype=='papercontract'){
                 if(servicecontractsid<=0){
                     msg='?????????????????????';
                     break;
                 }
                 if(mobilevcode==''){
                     msg='??????????????????';
                     break;
                 }
                 if(checkmobile==''){
                     msg='?????????????????????????????????';
                     break;
                 }
                 /*if(checkmobilevcode!=mobilevcode){
                     msg='?????????????????????????????????';
                     break;
                 }*/
                 if(mobile!=checkmobile){
                     msg='?????????????????????????????????';
                     break;
                 }
            }
	    var productclassonecollege = $("#productclassonecollege").val();
	    if(productclassonecollege==''){
	                msg='?????????????????????';
	                break;
	    }
            
            checkflag=false;
            if(params==1){
                checkflag=false;
            }else{
                /*if(classtyperenew==0 || classtyperenew==1){
                    checkflag=false;
                }*/
                //msg='?????????????????????';
            }
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
        var tyunusercode=$('#tyunusercode').find("option:checked").text();
        $.ajax({
            url: '/index.php?module=TyunWebBuyService&action=checkTyunExistBuyReturn',
            type: 'POST',
            dataType: 'json',
            data:{"tyunusercode":tyunusercode},
            beforeSend:function(){
                $.showLoading('??????????????????');
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
    $('#container').on('click','.accountlist',function(e){
        var datafield=$(this).data('field');
        $('input[name="'+datafield+'"]').val($(this).data('id'));
        $('input[name="'+datafield+'_display"]').val($(this).data('name'));
        if(datafield=='servicecontractsid'){
            $('#contractowenid').val($(this).data('userid'));
            $('#start_actualeffectivetime').val($(this).data('actualeffectivetime'));
            $('#contractowenname').val($(this).data('username'));
        }else if(datafield=='accountid'){
            $('#accountidowenid').val($(this).data('userid'));
            $('#accountidowenname').val($(this).data('username'));
        }
        $('#'+datafield+'_downlist').hide();
    });
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
        /*????????????start*/
        $("#contactname").empty();
        $("#contactnameclickhide").show();
        $("#elereceivermobile").val("");
        /*????????????end*/
        initNextStepButton();
    });
    $('#container').on('change','#classtype',function(){
        if($(this).val()=='buy'){
            location.href="/index.php?module=TyunWebBuyService&action=index";
        }
        if($(this).val()=='upgrade'){
            location.href="/index.php?module=TyunWebBuyService&action=upgrade";
        }
        if($(this).val()=='degrade'){
            location.href="/index.php?module=TyunWebBuyService&action=degrade";
        }
    });
    $('#container').on('click','.search_list',function(){
        var dataname=$(this).attr('data-name');
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
                    var actualeffectivetime = item2.actualeffectivetime;
                    oli.append('<a class="weui-cell weui-cell_access accountlist" data-field="'+dataname+'" data-id="'+nid+'" data-name="'+nArr+'" data-userid="'+userid+'" data-username="'+username+'" data-actualeffectivetime="'+actualeffectivetime+'" href="javascript:;">'+
                        '<div class="weui-cell__bd weui-cell_primary">'+
                        '<p>'+nArr+'</p>'+
                        '</div>'+
                        '<span class="weui-cell__ft"></span>'+
                        '</a>');
                });
            }else{
                $.toast('????????????????????????','text');
                $('input[name="'+dataname+'"]').val('');
                $('input[name="'+dataname+'_display"]').val('');
                if(dataname=='servicecontractsid'){
                    $('#contractowenid').val('');
                }else if(dataname=='accountid'){
                    $('#accountidowenid').val('');
                }
            }
        });
        // getUserRenewProductInfo();
        // var classtyperenew = $("#classtyperenew").val();
        // if(!classtyperenew){
        //     getAllCategory(false);
        // }
    });
    $('#container').on("click",".packListshow",function(event){
        var currentchecked=$(this).attr('data-flag');
        if(currentchecked==1){
            $(this).attr('data-flag',2);
            $(this).removeClass('icon-jian-tianchong');
            $(this).addClass('icon-jia-tianchong');
            $('.packshow').hide();
        }else{
            $(this).removeClass('icon-jia-tianchong');
            $(this).addClass('icon-jian-tianchong');
            $(this).attr('data-flag',1);
            $('.packshow').show();
        }
    });
    $('#container').on("click",".productListshow",function(event){
        var currentchecked=$(this).attr('data-flag');
        if(currentchecked==1){
            $(this).attr('data-flag',2);
            $(this).removeClass('icon-jian-tianchong');
            $(this).addClass('icon-jia-tianchong');
            $('.productshow').hide();
        }else{
            $(this).removeClass('icon-jia-tianchong');
            $(this).addClass('icon-jian-tianchong');
            $(this).attr('data-flag',1);
            $('.productshow').show();
        }
        return false;
    });
    $('#container').on("click",".packSpecificationListgroup",function(event){
        var currentchecked=$(this).attr('checked');
        $.each($('.packSpecificationList'),function(key,value){
            if(currentchecked){
                $(value).prop("checked",true);
            }else{
                $(value).prop('checked',false);
            }
        });
        if(currentchecked){
            renewDomainPage();
        }else{
            $("#renewdomain").remove();
        }
        checkCycle();
        // calcprice();
        console.log(6);
        calPrice();
        checkProductValue();
    });
    $('#container').on('click','.packshow',function(){
        // calcprice();
        console.log(5);
        // calPrice();
        checkProductValue();
    });
    $('#container').on('click','.productshow',function(){
        // calcprice();
        console.log(4);
        // calPrice();
        checkProductValue();
    });
    $('#container').on("click",".packSpecificationList",function(event){
        var temp=0;
        var currentchecked=$('.packSpecificationListgroup').attr('checked');
        if(currentchecked){
            return false;
        }
        $.each($('.packSpecificationList'),function(key,value){
            if($(value).attr('checked')==false){
                return false;
            }
            temp++;
        });
        // if($('.packSpecificationList').length==temp){
        //     $('.packSpecificationListgroup').prop("checked",true);
        //     renewDomainPage();
        // }else{
        //     $('.packSpecificationListgroup').prop("checked",false);
        //     $("#renewdomain").remove();
        //
        // }

        // if($(this).data("specificationid")==52){
        //     if($(this).attr('checked')){
        //         renewDomainPage();
        //     }else{
        //         $("#renewdomain").remove();
        //     }
        // }

        // calcprice();
        console.log(3);
        calPrice();
        checkProductValue();
    });
    $('#container').on("click",".productSpecificationListgroup",function(event){
        var currentchecked=$(this).attr('checked');
        $.each($('.productSpecificationList'),function(key,value){
            if(currentchecked){
                $(value).prop("checked",true);
            }else{
                $(value).prop('checked',false);
            }
        });
        // calcprice();
        console.log(2);
        calPrice();
        checkProductValue();
    });
    $('#container').on("click",".productSpecificationList",function(event){
        var temp=0;
        $.each($('.productSpecificationList'),function(key,value){
            if($(value).attr('checked')==false){
                return false;
            }
            temp++;
        });
        if($('.productSpecificationList').length==temp){
            $('.productSpecificationListgroup').prop("checked",true);
        }else{
            $('.productSpecificationListgroup').prop("checked",false);
        }
        console.log(1);
        calPrice();
        checkProductValue();
        // calcprice();
    });
    function calcprice(){
        var years=$('#buyyear').attr('data-values');
        if(years>0){
            var renewpricesum=0;
            $.each($('.productSpecificationList'),function(key,value){
                if($(value).attr('checked')) {
                    var renewprice = $(value).attr('data-renewprice');
                    renewprice = renewprice > 0 ? renewprice : 0;
                    renewpricesum = FloatAdd(renewpricesum,FloatMul(renewprice,years));
                }
            });
            var packageprice=$('#packageprice').attr('data-value');
            var currentchecked=$('.packSpecificationListgroup').attr('checked');
            var temppackageprice=0;
            if(!currentchecked){
                $.each($('.packSpecificationList'),function(key,value){
                    if($(value).attr('checked')){
                        var renewprice=$(value).attr('data-renewprice');
                        renewprice=renewprice>0?renewprice:0;
                        temppackageprice=FloatAdd(temppackageprice,FloatMul(renewprice,years));

                    }
                });
                temppackageprice=FloatAdd(temppackageprice,renewpricesum);
            }else{
                temppackageprice=FloatAdd(FloatMul(packageprice,years),renewpricesum);
            }
            $('#Price').val(temppackageprice);
            $('#Priceshow').val('???'+parseFloat(temppackageprice).toFixed(2));
            calcotherprice();
        }
    };
    
    function checkCycle() {
        var years=$('#buyyear').attr('data-values');
        var start_actualeffectivetime = $("#start_actualeffectivetime").val();
        var new_date = new Date(start_actualeffectivetime);
        var new_years = Number(new_date.getFullYear())+Number(years);
        var new_month = Number(new_date.getMonth())+1;
        if(new_month<10){
            new_month = '0'+new_month;
        }
        var edate = new_date.getDate();
        if(edate<10){
            edate = '0'+edate;
        }
        var end_actualeffectivetime = new_years+'-'+new_month+'-'+edate;
        var str = start_actualeffectivetime+'???'+end_actualeffectivetime;
        var isPackage = $(".packSpecificationListgroup").attr('checked');
        if(isPackage){
            $('.actualeffectivetime').parent().parent().show();
            $('.actualeffectivetime').html(str);
        }else{
            $('.actualeffectivetime').parent().parent().hide();
        }
    }

    // function checkProductValue(params){
    //     var productSpecificationListnum=$('.productSpecificationList').filter('input:checked').length;
    //     var packSpecificationListnum=$('.packSpecificationList').filter('input:checked').length;
    //     productSpecificationListnum=productSpecificationListnum>0?productSpecificationListnum:0;
    //     packSpecificationListnum=packSpecificationListnum>0?packSpecificationListnum:0;
    //     if((packSpecificationListnum+productSpecificationListnum)==0){
    //         $('#submitid').attr('data-value',1);
    //         $('#submitfrom').css('backgroundColor','#999999');
    //         return false;
    //     }
    //     var buyyear=$('#buyyear').data('values');
    //     if(buyyear<1 || buyyear==undefined){
    //         return false;
    //     }
    //     var servicetotal=$('#servicetotal').val();
    //     if(servicetotal>0){
    //         $('#submitid').attr('data-value',2);
    //         $('#submitfrom').css('backgroundColor','#4994F2');
    //         return true;
    //     }else{
    //         $('#submitid').attr('data-value',1);
    //         $('#submitfrom').css('backgroundColor','#999999');
    //         return false;
    //     }
    //     return false;
    // }

    // $('#container').on('click','#submitfrom',function(){
    //     var submitid=$('#submitid').attr('data-value');
    //     if(submitid!=2){
    //         return false;
    //     }
    //     var servicecontractsid=$('input[name="servicecontractsid"]').val();
    //     var servicecontractsid_display=$('input[name="servicecontractsid_display"]').val();
    //     var accountid=$('input[name="accountid"]').val();
    //     var oldaccountid=$('input[name="oldaccountid"]').val();
    //     var accountid_display=$('input[name="accountid_display"]').val();
    //     var oldaccountid_display=$('input[name="oldaccountid_display"]').val();
    //
    //     var mobile=$('#mobile').val();
    //     var mobilevcode=$('#mobilevcode').val();
    //     var classtype=$('#classtype').val();
    //     var buyyear=$('#buyyear').data('values');
    //     var oldproductnameid=$('#oldproductnameid').val();
    //     var servicetotal=parseFloat($('#servicetotal').val()).toFixed(2);
    //     var classtyperenew=$('#classtyperenew').val();
    //     var classtyperename=$('#classtyperenew').find("option:checked").text();
    //     var tyunusercode=$('#tyunusercode').val();
    //     var tyunusercodetext=$('#tyunusercode').find("option:checked").text();
    //     var oldproductname_display=$('#oldproductname_display').val();
    //     var separateproductname_display= '';
    //     var agents=$('#instanceagents').data('value');
    //
    //     //????????????start
    //     var productid=[];
    //     var categoryid=[];
    //     var number=[];
    //     var id=[];
    //     var price=[];
    //     var renewprice=[];
    //     var unit=[];
    //     var specificationstitle=[];
    //     var producttitle=[];
    //     $.each($('.select3check'),function(keyt,valuet){
    //         producttitle[keyt]=$(valuet).data('producttitle');
    //         productid[keyt]=$(valuet).data('productid');
    //         categoryid[keyt]=$(valuet).data('categoryid');
    //         number[keyt]=$(valuet).data('number');
    //         id[keyt]=$(valuet).data('id');
    //         price[keyt]=$(valuet).data('price');
    //         renewprice[keyt]=$(valuet).data('renewprice');
    //         unit[keyt]=$(valuet).data('unit');
    //         specificationstitle[keyt]=$(valuet).data('specificationstitle');
    //         separateproductname_display += $(valuet).data('producttitle')+'('+$(valuet).data('number')+') ';
    //     });
    //     //????????????end
    //     var buydate=$('#buydate').val();
    //     var str='<div class="weui-form-preview">' +
    //         '<div class="weui-form-preview__bd">' +
    //         '<div class="weui-form-preview__item">' +
    //         '<label class="weui-form-preview__label">????????????</label>' +
    //         '<span class="weui-form-preview__value">'+classtyperename+'</span>' +
    //         '</div>' +
    //         '<div class="weui-form-preview__item">' +
    //         '<label class="weui-form-preview__label">?????????</label>' +
    //         '<span class="weui-form-preview__value">'+oldproductname_display+'</span>' +
    //         '</div>' +
    //         '<div class="weui-form-preview__item">' +
    //         '<label class="weui-form-preview__label">????????????</label>' +
    //         '<span class="weui-form-preview__value">'+separateproductname_display+'</span>' +
    //         '</div>' +
    //         '<div class="weui-form-preview__item">' +
    //         '<label class="weui-form-preview__label">????????????</label>' +
    //         '<span class="weui-form-preview__value">???'+servicetotal+'</span>' +
    //         '</div>' +
    //         '<div class="weui-form-preview__item">' +
    //         '<label class="weui-form-preview__label">????????????</label>' +
    //         '<span class="weui-form-preview__value">'+buyyear+'???</span>' +
    //         '</div>' +
    //         '<div class="weui-form-preview__item">' +
    //         '<label class="weui-form-preview__label">????????????</label>' +
    //         '<span class="weui-form-preview__value">'+servicecontractsid_display+'</span>' +
    //         '</div>' +
    //         '<div class="weui-form-preview__item">' +
    //         '<label class="weui-form-preview__label">????????????</label>' +
    //         '<span class="weui-form-preview__value">'+accountid_display+'</span>' +
    //         '</div>' +
    //         '<div class="weui-form-preview__item">' +
    //         '<label class="weui-form-preview__label">???????????????</label>' +
    //         '<span class="weui-form-preview__value">'+oldaccountid_display+'</span>' +
    //         '</div>' +
    //         '<div class="weui-form-preview__item">' +
    //         '<label class="weui-form-preview__label">????????????</label>' +
    //         '<span class="weui-form-preview__value">'+mobile+'</span>' +
    //         '</div>' +
    //         '<div class="weui-form-preview__item">' +
    //         '<label class="weui-form-preview__label">T?????????</label>' +
    //         '<span class="weui-form-preview__value">'+tyunusercodetext+'</span>' +
    //         '</div>' +
    //         '</div>' +
    //         '</div>';
    //     var packproductid=[];
    //     var packcategoryid=[];
    //     var packnumber=[];
    //     var packspecificationstitle=[];
    //     var packspecificationsid=[];
    //     var packproducttitle=[];
    //     var otherproductid=[];
    //     var othercategoryid=[];
    //     var othernumber=[];
    //     var otherspecificationstitle=[];
    //     var otherspecificationsid=[];
    //     var otherproducttitle=[];
    //     var packSpecificationList=$('.packSpecificationList').filter('input:checked');
    //     var productSpecificationList=$('.productSpecificationList').filter('input:checked');
    //     var currentchecked=$('.packSpecificationListgroup').attr('checked');
    //     $.each(productSpecificationList,function(keyt,valuet){
    //         otherproductid[keyt]=$(valuet).data('productid');
    //         othercategoryid[keyt]=$(valuet).data('packid');
    //         othernumber[keyt]=$(valuet).data('count');
    //         otherspecificationsid[keyt]=$(valuet).data('productspecificationsid');
    //         otherproducttitle[keyt]=$(valuet).data('producttitle');
    //         otherspecificationstitle[keyt]=$(valuet).data('productspecificationstitle');
    //
    //     });
    //     var ispackage=0;
    //     if(currentchecked){
    //         ispackage=1;
    //     }else{
    //         $.each(packSpecificationList,function(keyt,valuet){
    //             packproductid[keyt]=$(valuet).data('productid');
    //             packcategoryid[keyt]=$(valuet).data('packid');
    //             packnumber[keyt]=$(valuet).data('count');
    //             packspecificationsid[keyt]=$(valuet).data('productspecificationsid');
    //             packproducttitle[keyt]=$(valuet).data('producttitle');
    //             packspecificationstitle[keyt]=$(valuet).data('productspecificationstitle');
    //             renewprice[keyt]=$(valuet).data('renewprice');
    //             price[keyt]=$(valuet).data('price');
    //             marketprice[keyt]=$(valuet).data('marketprice');
    //             marketPrice[keyt]=$(valuet).data('marketprice');
    //         });
    //     }
    //
    //
    //     //??????????????????
    //     var chooseuserproduct = [];
    //     var packSpecificationList2 = $(".packSpecificationList2").filter('input:checked');
    //     $.each(packSpecificationList2,function (k2, v2) {
    //         chooseuserproduct.push(parseInt($(v2).data('productspecificationsid')));
    //     });
    //
    //
    //     var params={"servicecontractsid":servicecontractsid,
    //         "servicecontractsid_display":servicecontractsid_display,
    //         "accountid":accountid,
    //         "accountid_display":accountid_display,
    //         "mobile":mobile,
    //         "mobilevcode":mobilevcode,
    //         "classtype":classtype,
    //         "categoryid":classtyperenew,
    //         "type":1,
    //         "buyyear":buyyear,
    //         "buydate":buydate,
    //         'packageid':oldproductnameid,
    //         "tyunusercode":tyunusercode,
    //         "servicetotal":servicetotal,
    //         "oldproductname":$('#oldproductname_display').val(),
    //         "ispackage":ispackage,
    //         "tyunusercodetext":tyunusercodetext,
    //
    //         "productclassonevalues":classtyperenew,
    //         "productclasstwovalues":oldproductnameid,
    //         "agents":agents,
    //         "oldcustomerid":oldaccountid,
    //         "oldcustomername":oldaccountid_display,
    //         "chooseuserproduct":chooseuserproduct
    //     };
    //     params['packproductid']=packproductid;
    //     params['packproducttitle']=packproducttitle;
    //     params['packcategoryid']=packcategoryid;
    //     params['packnumber']=packnumber;
    //     params['packspecificationsid']=packspecificationsid;
    //     params['packspecificationstitle']=packspecificationstitle;
    //
    //     params['otherproductid']=otherproductid;
    //     params['otherproducttitle']=otherproducttitle;
    //     params['otherpackcategoryid']=othercategoryid;
    //     params['otherpacknumber']=othernumber;
    //     params['otherpackspecificationstitle']=otherspecificationstitle;
    //     params['otherspecificationsid']=otherspecificationsid;
    //
    //     //????????????start
    //     params['producttitle']=producttitle;
    //     params['productid']=productid;
    //     params['categoryids']=categoryid;
    //     params['number']=number;
    //     params['id']=id;
    //     params['price']=price;
    //     params['renewprice']=renewprice;
    //     params['unit']=unit;
    //     params['specificationstitle']=specificationstitle;
    //     //????????????end
    //
    //     $.confirm(str, "?????????????????????", function() {
    //         $.ajax({
    //             url: '/index.php?module=TyunWebBuyService&action=renewdoOrder',
    //             type: 'POST',
    //             dataType: 'json',
    //             data:params,
    //             beforeSend:function(){
    //                 $.showLoading('???????????????');
    //             },
    //             success: function (data) {
    //                 $.hideLoading();
    //                 if(data.success==1){
    //                     $.alert("????????????", function() {
    //                         location.href="/index.php?module=TyunWebBuyService&action=renew";
    //                     });
    //
    //                 }else{
    //                     $.toast(data.msg,'text');
    //                 }
    //             }
    //         });
    //     }, function() {
    //         //????????????
    //     });
    // });
    $('#container').on('change','#servicetotal',function(){
        checkProductValue(1);
    });
    $('#container').on('input','#servicetotal',function(){
        checkProductValue(1);
    });

    function renewDomainPage(){
        $("#renewdomian").remove();
        var str = '';
        if((typeof domains)!='undefined'&& domains.length>0 && domaininpackage) {
            if (domains.length > 0) {
                str += '<div class="weui-cells weui-cells_checkbox" id="renewdomain" style="margin-top: 0px">\n' +
                    '                <div class="weui-cell weui-check__label" style="padding-left:0;display: flex;justify-content: space-between;">\n' +
                    '                    <label style="display: flex;" class="helpinfo"  data-title="??????????????????" data-content="????????????????????????????????????????????????????????????????????????????????????;">' +
                    '                    <div class="weui-cell__hd">\n' +
                    '                    </div>\n' +
                    '                    <div class="weui-cell__bd">\n' +
                    '                        <p>  ??????????????????  ' +
                    '<span class="iconfont icon-iconset0143 " style="font-size: 16px;"></span>' +
                    '</p>\n' +
                    '                    </div></label>' +
                    ''+
                    '                    <div class="weui-cell__ft">\n' +
                    '                        <i class="iconfont icon-jian-tianchong packListshow2" style="color:#4994F2;font-size: 20px;margin-right: 10px;" data-flag="1"></i>' +
                    '                    </div>' +
                    '                </div>\n';

                var len = domains.length;
                $.each(domains, function (key, value) {
                    var flagnum = 4;
                    str += '                <label class="weui-cell weui-check__label packshow2">\n' +
                        '                    <div class="weui-cell__hd">\n' +
                        '                        <input type="checkbox"  data-key="' + key + '" class="weui-check  packSpecificationList2  packSpecification' + flagnum + '" data-renewprice="' + value.ProductSpecifications[0].RenewPrice + '" data-count="' + value.ProductSpecifications[0].Count + '" data-packid="' + value.PackageID + '" data-productid="' + value.ProductID + '" data-closeDate="' + value.ProductSpecifications[0].CloseDate + '" data-productspecificationsid="' + value.ProductSpecifications[0].ID + '" data-producttitle="' + value.ProductTitle + '" data-productspecificationstitle="' + value.ProductSpecifications[0].Title + '" ' + (key == 0 ? 'checked' : '') + '>\n' +
                        '                        <i class="weui-icon-checked"></i>\n' +
                        '                    </div>\n' +
                        '                    <div class="weui-cell__bd">\n' +
                        '                        <p>' + value.ProductTitle + '--' + value.ProductSpecifications[0].Title + '--' + value.ProductSpecifications[0].CloseDate + '</p>\n' +
                        '                    </div>\n' +
                        '                </label>\n';

                });
                str += '            </div>';
                $("#package").after(str);
            }
        }

    }

    function updateOtherProduct(){
        var str ='';
        if((typeof packageSpecificationList)!='undefined'&&packageSpecificationList.length>0) {
            var str = '<div class="weui-cells weui-cells_checkbox" id="package" >\n' +
                '                <div class="weui-cell weui-check__label" style="padding-left:0;display: flex;justify-content: space-between;">\n' +
                '                    <label style="display: flex;"><div class="weui-cell__hd">\n' +
                '                        <input type="checkbox" class="weui-check packSpecificationListgroup" checked data-renewprice="'+package.DirectRenewPrice+'" data-price="'+package.DirectPrice+'" data-marketrenewprice="'+package.MarketRenewPrice+'" data-marketprice="'+package.MarketPrice+'">\n' +
                '                        <i class="weui-icon-checked"></i>\n' +
                '                    </div>\n' +
                '                    <div class="weui-cell__bd">\n' +
                '                        <p>?????????????????????</p>\n' +
                '                    </div></label>' +
                // '                    <div class="weui-cell__ft">\n' +
                // '                        <i class="iconfont icon-jian-tianchong packListshow" style="color:#4994F2;font-size: 20px;margin-right: 10px;" data-flag="1"></i>' +
                // '                    </div>' +
                '                </div>\n';

            $.each(packageSpecificationList, function (key, value) {
                console.log(value.ProductID);
                if(value.ProductID!=52){
                    return true;
                }
                var flagnum = 2;
                str += '                <label class="weui-cell weui-check__label packshow">\n' +
                    '                    <div class="weui-cell__hd">\n' +
                    '                        <input type="checkbox" class="weui-check packSpecificationList  packSpecification'+flagnum+'" data-specificationid="'+value.SpecificationID+'" data-marketrenewprice="'+value.ProductSpecifications[0].MarketRenewPrice+'" data-marketprice="'+value.ProductSpecifications[0].MarketPrice+'" data-price="'+value.ProductSpecifications[0].Price+'" data-renewprice="'+value.ProductSpecifications[0].RenewPrice+'" data-count="'+value.ProductSpecifications[0].Count+'" data-packid="' + value.PackageID + '" data-productid="' + value.ProductID + '" data-closeDate="' + value.ProductSpecifications[0].CloseDate + '" data-productspecificationsid="' + value.ProductSpecifications[0].ID + '" data-producttitle="' + value.ProductTitle + '" data-productspecificationstitle="' + value.ProductSpecifications[0].Title + '" checked>\n' +
                    '                        <i class="weui-icon-checked"></i>\n' +
                    '                    </div>\n' +
                    '                    <div class="weui-cell__bd">\n' +
                    '                        <p>' + value.ProductTitle + '--' + value.ProductSpecifications[0].Title + '--' + value.ProductSpecifications[0].CloseDate + '</p>\n' +
                    '                    </div>\n' +
                    '                </label>\n';


            });
            str += '            </div>';
        }

        //????????????
        //if((typeof domains)!='undefined'&& domains.length>0 ) {
        if((typeof domains)!='undefined'&& domaininpackage && packageSpecificationList!=undefined && packageSpecificationList.length>0) {
            if (domains.length > 0) {
                 str += '<div class="weui-cells weui-cells_checkbox" id="renewdomain" style="margin-top: 0px">\n' +
                    '                <div class="weui-cell weui-check__label" style="padding-left:0;display: flex;justify-content: space-between;">\n' +
                    '                    <label style="display: flex;" class="helpinfo"  data-title="??????????????????" data-content="????????????????????????????????????????????????????????????????????????????????????;">' +
                    '                    <div class="weui-cell__hd">\n' +
                    '                    </div>\n' +
                    '                    <div class="weui-cell__bd">\n' +
                    '                        <p>  ??????????????????  ' +
                    '<span class="iconfont icon-iconset0143 " style="font-size: 16px;"></span>' +
                    '</p>\n' +
                    '                    </div></label>' +
                    ''+
                    '                    <div class="weui-cell__ft">\n' +
                    '                        <i class="iconfont icon-jian-tianchong packListshow2" style="color:#4994F2;font-size: 20px;margin-right: 10px;" data-flag="1"></i>' +
                    '                    </div>' +
                    '                </div>\n';

                var len = domains.length;
                $.each(domains, function (key, value) {
                    var flagnum = 4;
                    str += '                <label class="weui-cell weui-check__label packshow2">\n' +
                        '                    <div class="weui-cell__hd">\n' +
                        '                        <input type="checkbox"  data-key="' + key + '" class="weui-check  packSpecificationList2  packSpecification' + flagnum + '" data-renewprice="' + value.ProductSpecifications[0].RenewPrice + '" data-count="' + value.ProductSpecifications[0].Count + '" data-packid="' + value.PackageID + '" data-productid="' + value.ProductID + '" data-closeDate="' + value.ProductSpecifications[0].CloseDate + '" data-productspecificationsid="' + value.ProductSpecifications[0].ID + '" data-producttitle="' + value.ProductTitle + '" data-productspecificationstitle="' + value.ProductSpecifications[0].Title + '" ' + (key == 0 ? 'checked' : '') + '>\n' +
                        '                        <i class="weui-icon-checked"></i>\n' +
                        '                    </div>\n' +
                        '                    <div class="weui-cell__bd">\n' +
                        '                        <p>' + value.ProductTitle + '--' + value.ProductSpecifications[0].Title + '--' + value.ProductSpecifications[0].CloseDate + '</p>\n' +
                        '                    </div>\n' +
                        '                </label>\n';

                });
                str += '            </div>';
            }
        }



        if((typeof productSpecificationList)!='undefined' && productSpecificationList.length>0) {
            str += '<div class="weui-cells weui-cells_checkbox">\n' +
                '                <div class="weui-cell weui-check__label"  style="padding-left:0;display: flex;justify-content: space-between;">\n' +
                '                    <label style="display: flex;"><div class="weui-cell__hd">\n' +
                '                        <input type="checkbox" class="weui-check productSpecificationListgroup">\n' +
                '                        <i class="weui-icon-checked"></i>\n' +
                '                    </div>\n' +
                '                    <div class="weui-cell__bd">\n' +
                '                        <p>????????????????????????</p>\n' +
                '                    </div></label>' +
                '                    <div class="weui-cell__ft">\n' +
                '                        <i class="iconfont icon-jian-tianchong productListshow"  style="color:#4994F2;font-size: 20px;margin-right: 10px;" data-flag="1"></i>' +
                '                    </div>' +
                '                </div>\n';
            var flagnum = 3;
            $.each(productSpecificationList, function (key, value) {
                if(value.CanRenew){
                    $.each(value.ProductSpecifications,function(key1,value1){
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
            str += '            </div>';
        }
        $('#renewproduct').append(str);
    }
    $('#container').on("change","#tyunusercode",function(event){
        initAuthType();
        $('#oldproductname_display').val('');
        $('#oldproductnameid').val('');
        $('.lately_add_display').text('');
        $("#checkTyunExistBuyReturn").attr('data-value',0);
        if(!$(this).find("option:checked").hasClass('tyuncode_option')){
            $("#oldaccountid").val("");
            $("#oldaccountidowenid").val("");
            $("input[name=oldaccountid_display]").val("");
            $(".tyuncode_option").remove();
        }
        getUserRenewProductInfo();
    });
    function getAllCategory(isGetProduct=true){
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
                                $("#classtyperenew").append('<option value="'+value.ID+'">'+value.Title+'</option>');
                            }
                        });
                        if(isGetProduct){
                            getUserRenewProductInfo();
                        }
                    }
                }
            }
        );
    }
    $('#classtyperenew').attr('data-last',$('#classtyperenew').val()).change(function(event){
        var lastvalue=$(this).attr('data-last');
        var currentvalue=$(this).val();
        if(lastvalue!=currentvalue){
            $('#tempclasstype').val(currentvalue);
            getUserRenewProductInfo();
        }
    });
    function getUserRenewProductInfo(){
        var tyunusercode=$('#tyunusercode').val();
        if(tyunusercode==''){
            $.toast('????????????T?????????','forbidden');
            return false;
        }
        window.scroll(0,0);
        $('#oldproductname_display').val('');
        $('#oldproductnameid').val('');
        $('.lately_add_display').text('');
        $("#activationcodeid").val('');
        $("#canviewactivity").attr('data-value',0);
        var tyunusername=$('#tyunusercode').find("option:checked").text();
        var classtype=$('#classtype').val();
        var classtyperenew=$('#classtyperenew').val();
        $.ajax({
            url: '/index.php?module=TyunWebBuyService&action=getUserRenewProductInfo',
            type: 'POST',
            dataType: 'json',
            data:{"tyunusercode":tyunusercode,
                "classtyperenew":classtyperenew,
                "classtype":classtype,
                "tyunusername":tyunusername
            },
            beforeSend:function(){
                $.showLoading();
            },
            success: function (data) {
                $.hideLoading();
                if (data.code==200) {
                    var data2=data.data;
                    if(data2.package){
                        $('#oldproductname_display').val(data2.package.Title);
                        $('#oldproductnameid').val(data2.package.ID);
                        $('#packageprice').attr('data-value',data2.package.DirectRenewPrice);
                        $("#oldcontract").val(data.productList.contractname);
                        $("#expirationdate").val(data2.packageHistory.CloseDate);
                        $("#numberstudentaccounts").val(data.accountNumber);
                        var close_date = data2.packageHistory.CloseDate;
                        var myDate = new Date;
                        var year = myDate.getFullYear(); //???????????????
                        var mon = myDate.getMonth() + 1; //???????????????
                        if(mon<10){
                            mon = '0'+mon
                        }
                        var date = myDate.getDate();
                        if(date<10){
                            date = '0'+date
                        }
                        var new_close_date = year+'-'+mon+'-'+date;
                        if(new_close_date>close_date){
                            $('#start_actualeffectivetime').val(new_close_date);
                        }else{
                            $('#start_actualeffectivetime').val(close_date);
                        }
                    }
                    window.package = data2.package;
                    window.packageSpecificationList=data2.packageSpecificationList;
                    window.productSpecificationList=data2.productSpecificationList;
                    window.domains=data2.domains;
                    window.domaininpackage=data2.domaininpackage;
                    var updateinfo=data.productList.latelyadd!=''?data.productList.latelyadd:'???????????????';
                    /*var updateinfo = data.contract.updateinfo == undefined ? '???????????????' : data.contract.updateinfo;*/
                    $('.lately_add_display').text(updateinfo);
                    $("#activationcodeid").val(data.productList.activationcodeid);
                    var oldproductname_display = $('#oldproductname_display').val();
                    if(data2.packageSpecificationList.length==0 && data2.productSpecificationList.length==0){
                        $('#tyunusercodeonoff').val(1);
                        $('#nextstep').css({"background-color":'#999999'});
                        $.toast('???????????????????????????','forbidden');
                    } else{
                        // if(oldproductname_display){
                            $('#tyunusercodeonoff').val(2);
                            // $('#nextstep').css({"background-color":'#0B92D9'});
                        initNextStepButton();
                        // }else{
                        //     $('#tyunusercodeonoff').val(1);
                        //     $('#nextstep').css({"background-color":'#999999'});
                        //     $.toast('???????????????????????????','forbidden');
                        // }
                    }
                    $("#producttype").trigger('change');
                }else{
                    $('#tyunusercodeonoff').val(1);
                    $('#nextstep').css({"background-color":'#999999'});
                    $.toast(data.message,'forbidden');
                }
            }
        });
    }

    window.selectproductload=function(){
        var selectproducttitle=window.selectproducttitle;
        $('#selectproducttitle').html((typeof selectproducttitle=='string')?((selectproducttitle!='')?selectproducttitle:'&nbsp;'):'&nbsp;');
        $('#agents').val($('#instanceagents').data('value'));
        $('.header-back').click(function(){
            //$('.close-select').trigger('click');
            //pageManager.back();
        });
        //????????????start
        var signaturetype = $("#signaturetype").val();
        $("#formbutton").empty();
        if(signaturetype=='eleccontract'){
            str = '<a class="weui-btn" id="electronnextstep" href="javascript:void(0);" style="background-color: #999999;color:#FFFFFF;font-size:18px;width:98%;">?????????</a>';
        }else{
            str = '<a class="weui-btn" id="submitfrom" href="javascript:void(0);" style="background-color: #999999;color:#FFFFFF;font-size:18px;width:98%;">????????????</a>';
        }
        $("#formbutton").append(str);
        //????????????end

        updateOtherProduct();
        $("#buyyear").select({
            title: "?????????",
            items: [
                {title: "1???",value: "1"},
                {title: "2???",value: "2"},
                {title: "3???",value: "3"},
                {title: "4???",value: "4"},
                {title: "5???",value: "5"},
                {title: "6???",value: "6"},
                {title: "7???",value: "7"},
                {title: "8???", value: "8"},
                {title: "9???",value: "9"},
                {title: "10???",value: "10"}
            ],
            onClose: function(d) {
                $(".otherproductlist").empty();
                // calcprice();
                console.log(7);
                calPrice();
                checkProductValue(1);
                checkCycle();
                calcotherprice();
            }
        });
        var productclassonecollege=$("#productclassonecollege").data("values");
        if(productclassonecollege==7 || productclassonecollege==9){
            $(".collegeedition").css("display","none");
        }
    }
    //?????????????????????
    function FloatAdd(arg1,arg2){
        var r1,r2,m;
        try{r1=arg1.toString().split(".")[1].length}catch(e){r1=0}
        try{r2=arg2.toString().split(".")[1].length}catch(e){r2=0}
        m=Math.pow(10,Math.max(r1,r2));
        return (arg1*m+arg2*m)/m;
    }

    //?????????????????????
    function FloatSub(arg1,arg2){
        var r1,r2,m,n;
        try{r1=arg1.toString().split(".")[1].length}catch(e){r1=0}
        try{r2=arg2.toString().split(".")[1].length}catch(e){r2=0}
        m=Math.pow(10,Math.max(r1,r2));
        //????????????????????????
        n=(r1>r2)?r1:r2;
        return ((arg1*m-arg2*m)/m).toFixed(n);
    }

    //?????????????????????
    function FloatMul(arg1,arg2)
    {
        var m=0,s1=arg1.toString(),s2=arg2.toString();
        try{m+=s1.split(".")[1].length}catch(e){}
        try{m+=s2.split(".")[1].length}catch(e){}
        return Number(s1.replace(".",""))*Number(s2.replace(".",""))/Math.pow(10,m);
    }


    //?????????????????????
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

    $('#container').on('click','.deleted_otherproduct',function(){
        var id=$(this).data('id');
        $.confirm('', "????????????????????????????", function() {
            $('.othger_product_'+id).remove();
            var sumprice=0;//?????????????????????
            var sumrenewprice=0;//????????????
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
                $.toast('?????????????????????'+limit_min,"text");
                return false;
            }
            $('.weui-count__current_number'+id).val(countnumber);
            $('.select3farther'+id).attr('data-number',countnumber);
            calcotherprice();
        }else{
            $.toast('?????????????????????????????????',"text");
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
                    $.toast('??????????????????????????????',"text");
                    return false;
                }
            }
            $('.weui-count__current_number'+id).val(countnumber);
            $('.select3farther'+id).attr('data-number',countnumber);

            var money = $('.select3farther'+id).data('money');

            // var price=$('.select3farther'+id).data('price');
            // var renewprice=$('.select3farther'+id).data('renewprice');
            if(money >0){
                // var buyyear=$('#buyyear').attr('data-values');
                var sumprice=FloatMul(money,parseInt(countnumber));
                // var sumprice=FloatMul(price,parseInt(countnumber));
                // buyyear=buyyear>1?FloatSub(buyyear,1):0;
                // var sumrenewprice=FloatMul(FloatMul(renewprice,parseInt(countnumber)),buyyear);
                $('.pricesum'+id).html('???'+parseFloat(sumprice).toFixed(2));
            }
            var sumprice=0;
            var sumrenewprice=0;
            $.each($('.select3check'),function(key,value){
                sumprice=FloatAdd(sumprice,FloatMul(parseInt($(value).data('number')),$(value).data('money')));
                // sumprice=FloatAdd(sumprice,FloatMul(parseInt($(value).data('number')),$(value).data('price')));
                // sumrenewprice=FloatAdd(sumrenewprice,FloatMul(parseInt($(value).data('number')),$(value).data('renewprice')));
            });
            // $('#otherproductrenewsum').val(sumrenewprice);
            $('#otherproductsum').val(sumprice);
            calcotherprice();
            return false;
        // }else{
        //     $.toast('??????????????????????????????',"text");
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
                $.toast('?????????????????????'+limit_min,"text");
                return false;
            }
            $('.countnumber'+id).val(countnumber);
            $('.select2farther'+id).attr('data-number',countnumber);
            var price=$('.select2farther'+id).data('money');
            if(price>0){
                $('.price'+id).text('???'+parseFloat(FloatMul(price,parseInt(countnumber))).toFixed(2));
            }
        }else{
            $.toast('?????????????????????????????????',"text");
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
                    $.toast('??????????????????????????????',"text");
                return false;
            }
            $('.countnumber'+id).val(countnumber);
            $('.select2farther'+id).attr('data-number',countnumber);
            var price=$('.select2farther'+id).data('money');
            if(price>0){
                $('.price'+id).text('???'+parseFloat(FloatMul(price,parseInt(countnumber))).toFixed(2));
            }
            return false;
        // }else{
        //     $.toast('??????????????????????????????',"text");
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
                data:{"categoryID":(productclassone?productclassone:0),
                    "seriesID":1,
                    "userID":$('#tyunusercode').val(),
                    "authenticationType":1,
                    "activityAgent": agents,
                    "buyTrem":buyyears
                },
                beforeSend:function(){
                    $.showLoading('?????????????????????');
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
        if(i>1){
            $("#tagnav").append(navStr);
        }

        var buyyears = $("#buyyear").data('values');
        var groupid=[]
        $.each($('.select3check'),function(key,value){
            var productid=$(value).data('productid');
            var fartherid=$(value).data('fartherid');
            var fartherid1=$(value).data('fartherid1');
            var ProductSpecificationsid=$(value).data('id');
            var number=$(value).data('number');
            $('.select2farther'+fartherid1).trigger('click');
            $('.ProductSpecificationsmodify'+ProductSpecificationsid).trigger("click");
            // var price=$(value).data('price');
            var price=$(value).data('money');
            $('.price'+fartherid1).text('???'+parseFloat(FloatMul(price,number)).toFixed(2));
            $('.select2farther'+fartherid1).attr('data-number',number);
            $('.ProductTitlePrice'+fartherid1).text('???'+parseFloat(price).toFixed(2)+'/'+buyyears+'???');
            $('.countnumber'+fartherid1).val(number);

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
        //     $.toast('?????????????????????',"text");
        //     return false;
        // }
        var buyyears = $("#buyyear").data('values');
        if(!buyyears){
            $.toast('?????????????????????',"text");
            return false;
        }
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
            console.log(servicetotal);
            console.log(totalmarketprice);
            var productclassonecollege=$("#productclassonecollege").data("values");
            console.log(productclassonecollege);
            if((productclassonecollege==7||productclassonecollege==9) && servicetotal>0){
                    $('#submitid').attr('data-value',2);
                    $('#submitfrom').css('backgroundColor','#4994F2');
                    $('#electronnextstep').css('backgroundColor','#4994F2');
                    return true;
            }else{
                if(servicetotal>0 && totalmarketprice >0){
                    console.log("logss1");
                    $('#submitid').attr('data-value',2);
                    $('#submitfrom').css('backgroundColor','#4994F2');
                    $('#electronnextstep').css('backgroundColor','#4994F2');
                    return true;
                }else{
                    console.log("logss2");
                    $('#submitid').attr('data-value',1);
                    $('#submitfrom').css('backgroundColor','#999999');
                    $('#electronnextstep').css('backgroundColor','#999999');
                    return false;
                }
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
        var marketprice=$(this).data('marketprice');
        var marketrenewprice=$(this).data('marketrenewprice');
        var unit=$(this).data('unit');
        var dataid=$(this).data('id');
        var specificationstitle=$(this).data('specificationstitle');
        $(this).removeClass("weui-btn_disabled");
        $('.ProductSpecifications'+id).not(this).addClass("weui-btn_disabled");
        var countnumber=$('.countnumber'+id).val();
        var buyyears = $("#buyyear").data('values');
        $('.ProductTitlePrice'+id).text('???'+parseFloat(money).toFixed(2)+'/'+buyyears+'???');
        $('.unit'+id).text(unit);
        $('.price'+id).text('???'+parseFloat(parseInt(money)*parseInt(countnumber)).toFixed(2));
        $('.select2farther'+id).attr('data-price',price);
        $('.select2farther'+id).attr('data-money',money);
        $('.select2farther'+id).attr('data-renewprice',renewprice);
        $('.select2farther'+id).attr('data-marketprice',marketprice);
        $('.select2farther'+id).attr('data-marketrenewprice',marketrenewprice);
        $('.select2farther'+id).attr('data-unit',unit);
        $('.select2farther'+id).attr('data-id',dataid);
        $('.select2farther'+id).attr('data-specificationstitle',specificationstitle);
        $('.select2farther'+id).attr('data-specificationsid',specificationstitle);
    });

    $('#container').on("click","#submitotherproduct",function(event){
        var select2check=$('.select2check').filter('input:checked');
        if(select2check.length==0){
            $.toast('?????????????????????!','text');
            return false;
        }
        $.confirm("??????????????????????????????", "", function(){
            $('.otherproductlist').empty();
            var str='';
            var sumprice=0;
            var sumrenewprice=0;
            var buyyear=$('#buyyear').attr('data-values');
            buyyear=buyyear>1?FloatSub(buyyear,1):0;
            $.each(select2check,function(key,value){
                var dprice=FloatMul(parseInt($(value).data('number')),$(value).data('money'))
                // var drenewprice=FloatMul(parseInt($(value).data('number')),$(value).data('renewprice'));
                var limit_min = $(value).data('limit_min');
                sumprice=FloatAdd(sumprice,dprice);
                // sumrenewprice=FloatAdd(sumrenewprice,drenewprice);
                var outerHTML=value.outerHTML.replace('type="checkbox"','type="hidden"');
                outerHTML=outerHTML.replace('select2check','select3check');
                outerHTML=outerHTML.replace('select2farther','select3farther');
                outerHTML=outerHTML.replace('groupname','group1name');
                // var drenewprice=FloatMul(drenewprice,buyyear);
                // var dsumprice=FloatAdd(drenewprice,dprice);
                var dsumprice= sumprice;
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
                    '                            <div class="weui-cell" style="padding:0;height:30px;">\n' +
                    '                                <div class="weui-cell__bd">\n' +
                    // '                                    <label class="weui-label" style="margin-left:0px;font-size:14px;"></label>\n' +
                    // '                                    <label class="weui-label" style="margin-left:0px;font-size:14px;">???:???'+parseFloat($(value).data('price')).toFixed(2)+'&nbsp;???:???'+parseFloat($(value).data('renewprice')).toFixed(2)+'</label>\n' +
                    '                                <div class="weui-count"  style="border:1px solid #fff">\n' +
                    '                                    <a class="" style="color:#000000;font-size:14px;">???????????????</a>\n' +
                    '                                    <a class="pricesum'+$(value).data('productid')+'" style="color:#000000;font-size:14px;">???'+parseFloat(dprice).toFixed(2)+'</a>\n' +
                    // '                                    <a class="pricesum'+$(value).data('productid')+'" style="color:#000000;font-size:14px;">???:???'+parseFloat(dprice).toFixed(2)+'+&nbsp;???:???'+parseFloat(drenewprice).toFixed(2)+'=???'+parseFloat(dsumprice).toFixed(2)+'</a>\n' +
                    '                                </div>\n' +
                    '                                </div>\n' +
                    '                                <div class="weui-cell__ft">\n' +
                    '                                    <div class="weui-count weui-count_custrom">\n' +
                    '                                        <a class="weui-count__btn weui-count__sub weui-count__left_custrom" data-id="'+$(value).data('productid')+'"></a>\n' +
                    '                                        <input class="weui-count__number  weui-count__current_number'+$(value).data('productid')+'" type="number" value="'+$(value).data('number')+'"  readonly data-limit_min="'+limit_min+'" style="-webkit-appearance:none !important;border-radius: 0 !important;opacity: 1;">\n' +
                    '                                        <a class="weui-count__btn weui-count__add weui-count__right_custrom" data-id="'+$(value).data('productid')+'"></a>\n' +
                    '                                        <a class="weui-count__btn weui-count__decrease weui-count__decrease_deleted deleted_otherproduct" style="border:none;margin-left:5px;" data-id="'+$(value).data('productid')+'"></a>\n' +
                    '                                    </div>\n' +
                    '                                </div>\n' +
                    '                            </div>\n' +
                    '                        </div>\n' +
                    // '                        <div class="weui-media-box__desc">\n' +
                    // '                            <div class="weui-cell" style="padding-left:0px;">\n' +
                    // '                                <div class="weui-cell__bd">\n' +
                    // '                                <div class="weui-count"  style="border:1px solid #fff">\n' +
                    // '                                    <a class="" style="color:#000000;font-size:14px;">???????????????</a>\n' +
                    // '                                    <a class="pricesum'+$(value).data('productid')+'" style="color:#000000;font-size:14px;">???'+parseFloat(dsumprice).toFixed(2)+'</a>\n' +
                    // // '                                    <a class="pricesum'+$(value).data('productid')+'" style="color:#000000;font-size:14px;">???:???'+parseFloat(dprice).toFixed(2)+'+&nbsp;???:???'+parseFloat(drenewprice).toFixed(2)+'=???'+parseFloat(dsumprice).toFixed(2)+'</a>\n' +
                    // '                                </div>\n' +
                    // '                                </div>\n' +
                    // '                            </div>\n' +
                    // '                        </div>\n' +
                    '                    </div>\n' +
                    '                </div>';

            });
            var price=$('#Price').val();
            price=price>0?price:0;
            $('#otherproductsum').val(sumprice);
            // $('#otherproductrenewsum').val(sumrenewprice);
            // sumprice=FloatAdd(sumprice,FloatMul(sumrenewprice,buyyear));
            var sumpriceall=FloatAdd(sumprice,price);
            //??????????????????
            var submarketprice = $("input[name=marketactivitypriceval]").val();
            console.log(submarketprice)
            if(submarketprice!=undefined){
                sumpriceall=FloatAdd(submarketprice,sumpriceall);
            }

            $('#totalmarketprice').val(sumpriceall);
            $('#totalmarketpriceshow').val('???'+parseFloat(sumpriceall).toFixed(2));
            str+='<div class="weui-media-box weui-media-box_appmsg othger_product_last">\n' +
                '                    <div class="weui-media-box__bd">\n' +
                '                        <div class="weui-media-box__title">\n' +
                '                            <div class="weui-cell" style="padding:0px;">\n' +
                '                                <div class="weui-cell__bd">\n' +
                '                                    <div class="weui-count"  style="border:1px solid #fff">\n' +
                '                                        <a class="" style="color:#000000;font-size:16px;">???????????????</a>\n' +
                '                                        <a class="pricelast" style="color:#000000;font-size:16px;">???'+parseFloat(sumprice).toFixed(2)+'</a>\n' +
                '                                    </div>\n' +
                '                                </div>\n' +
                '                            </div>\n' +
                '                        </div>\n' +
                '                    </div>\n' +
                '                </div>';
            $('.otherproductlist').append(str);
            pageManager.back();
        }, function() {
            //????????????
        });
    });

    /**
     * ????????????????????????
     * */
    function calcotherprice(){
        var sumprice=0;
        var sumrenewprice=0;
        var buyyear=$('#buyyear').attr('data-values');
        var sumpriceyear=buyyear>0?1:0;
        var sumrenewpriceyear=buyyear>1?(buyyear-1):0;
        $.each($('.select3check'),function(key,value){
            var id=$(value).data('currentid');
            var temprice=FloatMul(parseInt($(value).data('number')),$(value).data('money'));
            sumprice=FloatAdd(sumprice,temprice);
            $('.pricesum'+id).html('???'+parseFloat(temprice).toFixed(2));
        });
        $('#otherproductsum').val(sumprice);
        var price=$('#Price').val();
        price=price>0?price:0;


        $('.pricelast').text('???'+parseFloat(sumprice).toFixed(2));
        var sumallprice=FloatAdd(sumprice,price);

        var submarketprice = $("input[name=marketactivitypriceval]").val();
        console.log(submarketprice);
        if(submarketprice != undefined){
            sumallprice=FloatAdd(sumallprice,submarketprice);
        }

        $('#totalmarketprice').val(sumallprice);
        $('#totalmarketpriceshow').val('???'+parseFloat(sumallprice).toFixed(2));
    }

    $("#container").on("click","#submitcheckaccount",function (event) {
        var tyunaccount = $("#tyunaccount").val();
        console.log(tyunaccount);
        if(!tyunaccount){
            $.alert("?????????T?????????");
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
                            $.alert("T???????????????");
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
                        // getAllCategory();
                        getUserRenewProductInfo();
                        initAuthType();
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
            $.toast('????????????????????????','forbidden');
            return;
        }
        var mobile = $("#mobile").val();
        if(!mobile){
            $.toast('????????????????????????','forbidden');
            return;
        }

        var signaturetype = $("#signaturetype").val();
        var mobilevcode = $("#mobilevcode").val();
        if(!mobilevcode&&signaturetype !='eleccontract'){
            $.toast('?????????????????????','forbidden');
            return;
        }

        // $("#tyunusercode")[0].options.length=0;
        var servicecontractsid=$('input[name="servicecontractsid"]').val();
        var servicecontractsid_display=$('input[name="servicecontractsid_display"]').val();
        var accountid=$('input[name="accountid"]').val();
        var accountid_display=$('input[name="accountid_display"]').val();
        var mobile=$('#mobile').val();
        var mobilevcode=$('#mobilevcode').val();
        var productclassonecollege = $("#productclassonecollege").data("values");
        if(checkGetUserCode(1)){
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
                "classtype":"renew",
                'categoryID':productclassonecollege
            },
            beforeSend:function(){
                $.showLoading();
            },
            success: function (data) {
                $.hideLoading();
                if (data.success==1) {
                    var loginName='';
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
                            loginName=value.loginName;
                            $("#tyunusercode").attr('data-lastvalue',value.id);
                        }
                        $("#clickhide").hide();
                        $("#tyunusercode").append('<option value="'+value.id+'" '+selected+' data-authenticationType="'+value.authenticationType+'">'+value.loginName+'</option>');
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
            $.toast('?????????????????????'+limit_min,"text");
            return false;
        }
        var uselimit=$('.select2farther'+id).attr('data-uselimit');
        uselimit=uselimit>0?uselimit:0;
        var userbuycount=$('.select2farther'+id).attr('data-userbuycount');
        if(uselimit>0){
            var tempadd=FloatAdd(countnumber,parseInt(userbuycount));
            if(tempadd>=uselimit)
                $.toast('??????????????????????????????',"text");
            return false;
        }
        $('.countnumber'+id).val(countnumber);
        $('.select2farther'+id).attr('data-number',countnumber);
        var price=$('.select2farther'+id).data('price');
        if(price>0){
            $('.price'+id).text('???'+parseFloat(FloatMul(price,parseInt(countnumber))).toFixed(2));
        }
        return false;
    });

    function calPrice() {
        var buyyear=$('#buyyear').data('values');
        if(!buyyear){
            return;
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

        var oldproductnameid=$('#oldproductnameid').val();
        var servicetotal=parseFloat($('#servicetotal').val()).toFixed(2);
        var classtyperenew=$('#classtyperenew').val();
        var classtyperename=$('#classtyperename').val();
        // var classtyperename=$('#classtyperenew').find("option:checked").text();
        var tyunusercode=$('#tyunusercode').val();
        var tyunusercodetext=$('#tyunusercode').find("option:checked").text();
        var oldproductname_display=$('#oldproductname_display').val();
        var separateproductname_display= '';
        var agents=$('#instanceagents').data('value');

        //????????????start
        var productid=[];
        var categoryid=[];
        var number=[];
        var id=[];
        var price=[];
        var renewprice=[];
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
            unit[keyt]=$(valuet).data('unit');
            specificationstitle[keyt]=$(valuet).data('specificationstitle');
            separateproductname_display += $(valuet).data('producttitle')+'('+$(valuet).data('number')+') ';
        });
        //????????????end
        var buydate=$('#buydate').val();
        var packproductid=[];
        var packcategoryid=[];
        var packnumber=[];
        var packspecificationstitle=[];
        var packspecificationsid=[];
        var packproducttitle=[];
        var otherproductid=[];
        var othercategoryid=[];
        var othernumber=[];
        var otherspecificationstitle=[];
        var otherspecificationsid=[];
        var otherproducttitle=[];
        var packSpecificationList=$('.packSpecificationList').filter('input:checked');
        var productSpecificationList=$('.productSpecificationList').filter('input:checked');
        var currentchecked=$('.packSpecificationListgroup').attr('checked');
        $.each(productSpecificationList,function(keyt,valuet){
            otherproductid[keyt]=$(valuet).data('productid');
            othercategoryid[keyt]=$(valuet).data('packid');
            othernumber[keyt]=$(valuet).data('count');
            otherspecificationsid[keyt]=$(valuet).data('productspecificationsid');
            otherproducttitle[keyt]=$(valuet).data('producttitle');
            otherspecificationstitle[keyt]=$(valuet).data('productspecificationstitle');

        });
        var ispackage=0;
        if(currentchecked){
            ispackage=1;
        }else{
            $.each(packSpecificationList,function(keyt,valuet){
                packproductid[keyt]=$(valuet).data('productid');
                packcategoryid[keyt]=$(valuet).data('packid');
                packnumber[keyt]=$(valuet).data('count');
                packspecificationsid[keyt]=$(valuet).data('productspecificationsid');
                packproducttitle[keyt]=$(valuet).data('producttitle');
                packspecificationstitle[keyt]=$(valuet).data('productspecificationstitle');
            });
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
            "servicetotal":isNaN(servicetotal)?0:servicetotal,
            "oldproductname":$('#oldproductname_display').val(),
            "ispackage":ispackage,
            "tyunusercodetext":tyunusercodetext,
            "productclassonevalues":classtyperenew,
            "productclasstwovalues":oldproductnameid,
            "agents":agents,
            "oldcustomerid":oldaccountid,
            "oldcustomername":oldaccountid_display
        };
        params['packproductid']=packproductid;
        params['packproducttitle']=packproducttitle;
        params['packcategoryid']=packcategoryid;
        params['packnumber']=packnumber;
        params['packspecificationsid']=packspecificationsid;
        params['packspecificationstitle']=packspecificationstitle;

        params['otherproductid']=otherproductid;
        params['otherproducttitle']=otherproducttitle;
        params['otherpackcategoryid']=othercategoryid;
        params['otherpacknumber']=othernumber;
        params['otherpackspecificationstitle']=otherspecificationstitle;
        params['otherspecificationsid']=otherspecificationsid;

        //????????????start
        params['producttitle']=producttitle;
        params['productid']=productid;
        params['categoryids']=categoryid;
        params['number']=number;
        params['id']=id;
        params['price']=price;
        params['renewprice']=renewprice;
        params['unit']=unit;
        params['specificationstitle']=specificationstitle;
        //????????????end

        var sumprice = 0;
        //??????????????????????????????
        $.each($('.select3check'),function(key,value){
            var temprice=FloatMul(parseInt($(value).data('number')),$(value).data('money'));
            sumprice=FloatAdd(sumprice,temprice);
        });
        console.log(sumprice);
        $.ajax({
            url: '/index.php?module=TyunWebBuyService&action=calculationTotal',
            type: 'POST',
            dataType: 'json',
            data:params,
            beforeSend:function(){
                $.showLoading('?????????');
            },
            success: function (data) {
                $.hideLoading();
                console.log(data);
                if(data.success==1){
                    var money = data.data.data.money;
                    if(classtyperenew==7 || classtyperenew==9){
                        var numberstudentaccounts=parseInt($("#numberstudentaccounts").val());
                        $('#Price').val((money-sumprice)*numberstudentaccounts);
                        $('#Priceshow').val('???'+parseFloat((money-sumprice)*numberstudentaccounts).toFixed(2));
                    }else{
                        $('#Price').val(money-sumprice);
                        $('#Priceshow').val('???'+parseFloat(money-sumprice).toFixed(2));
                    }

                    calcotherprice();

                }else{
                    $('#Price').val(0);
                    $('#Priceshow').val('???'+parseFloat(0.00));
                    calcotherprice();
                    $.toast(data.msg,'text');
                }
            }
        });
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

    $('#container').on('click','#submitfrom',function(){
        var submitid=$('#submitid').attr('data-value');
        console.log(submitid);
        if(submitid!=2){
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
        var classtyperename=$('#classtyperename').val();
        // var classtyperename=$('#classtyperenew').find("option:checked").text();
        var tyunusercode=$('#tyunusercode').val();
        var tyunusercodetext=$('#tyunusercode').find("option:checked").text();
        var oldproductname_display=$('#oldproductname_display').val();
        var separateproductname_display= '';
        var agents=$('#instanceagents').data('value');

        //????????????start
        var productid=[];
        var categoryid=[];
        var number=[];
        var id=[];
        var price=[];
        var renewprice=[];
        var marketrenewprice = [];
        var marketprice = [];
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
            marketrenewprice[keyt]=$(valuet).data('marketrenewprice');
            marketprice[keyt]=$(valuet).data('marketprice');
            unit[keyt]=$(valuet).data('unit');
            specificationstitle[keyt]=$(valuet).data('specificationstitle');
            separateproductname_display += $(valuet).data('producttitle')+'('+$(valuet).data('number')+') ';
        });

        //????????????end
        var buydate=$('#buydate').val();
        if($('#buydateshow').data('value')!=1){
            var buydatestr='';
        }else{
            if(buydate==''){
                $.toast('??????????????????',"text");
                return false;
            }
            var buydatestr=$('#buydateshow').data('value')!=1?'':'<div class="weui-form-preview__item">' +
                '<label class="weui-form-preview__label">????????????</label>' +
                '<span class="weui-form-preview__value">'+buydate+'</span>' +
                '</div>'
        }
        // ??????????????????
        var iscollegeedition=0;
        if(classtyperenew==7 || classtyperenew==9){
            console.log("whatssss");
            // ??????????????????
            var myDate=new Date();
            var currentDate=myDate.getFullYear()+"/"+(myDate.getMonth()+1)+"/"+myDate.getDate();
            iscollegeedition=1;
            var numberstudentaccounts = $("#numberstudentaccounts").val();
            var str='<div class="weui-form-preview">' +
                '<div class="weui-form-preview__bd">' +
                '<div class="weui-form-preview__item">' +
                '<label class="weui-form-preview__label">T?????????</label>' +
                '<span class="weui-form-preview__value">'+tyunusercodetext+'</span>' +
                '</div>' +
                '<div class="weui-form-preview__item">' +
                '<label class="weui-form-preview__label">?????????</label>' +
                '<span class="weui-form-preview__value">'+oldproductname_display+'</span>' +
                '</div>' +
                '<div class="weui-form-preview__item">' +
                '<label class="weui-form-preview__label">????????????</label>' +
                '<span class="weui-form-preview__value">???'+servicetotal+'</span>' +
                '</div>' +
                '<div class="weui-form-preview__item">' +
                '<label class="weui-form-preview__label">????????????</label>' +
                '<span class="weui-form-preview__value">'+buyyear+'???</span>' +
                '</div>' +
                '<div class="weui-form-preview__item">' +
                '<label class="weui-form-preview__label">????????????</label>' +
                '<span class="weui-form-preview__value">'+servicecontractsid_display+'</span>' +
                '</div>' +
                '<div class="weui-form-preview__item">' +
                '<label class="weui-form-preview__label">????????????</label>' +
                '<span class="weui-form-preview__value">'+accountid_display+'</span>' +
                '</div>' +
                '<div class="weui-form-preview__item">' +
                '<label class="weui-form-preview__label">????????????</label>' +
                '<span class="weui-form-preview__value">'+currentDate+'</span>' +
                '</div>' +
                '</div>' +
                '</div>';
        }else {
            var str = '<div class="weui-form-preview">' +
                '<div class="weui-form-preview__bd">' +
                '<div class="weui-form-preview__item">' +
                '<label class="weui-form-preview__label">????????????</label>' +
                '<span class="weui-form-preview__value">' + classtyperename + '</span>' +
                '</div>' +
                '<div class="weui-form-preview__item">' +
                '<label class="weui-form-preview__label">?????????</label>' +
                '<span class="weui-form-preview__value">' + oldproductname_display + '</span>' +
                '</div>' +
                '<div class="weui-form-preview__item">' +
                '<label class="weui-form-preview__label">????????????</label>' +
                '<span class="weui-form-preview__value">' + separateproductname_display + '</span>' +
                '</div>' +
                '<div class="weui-form-preview__item">' +
                '<label class="weui-form-preview__label">????????????</label>' +
                '<span class="weui-form-preview__value">???' + servicetotal + '</span>' +
                '</div>' +
                '<div class="weui-form-preview__item">' +
                '<label class="weui-form-preview__label">????????????</label>' +
                '<span class="weui-form-preview__value">' + buyyear + '???</span>' +
                '</div>' +
                '<div class="weui-form-preview__item">' +
                '<label class="weui-form-preview__label">????????????</label>' +
                '<span class="weui-form-preview__value">' + servicecontractsid_display + '</span>' +
                '</div>' +
                '<div class="weui-form-preview__item">' +
                '<label class="weui-form-preview__label">????????????</label>' +
                '<span class="weui-form-preview__value">' + accountid_display + '</span>' +
                '</div>' +
                '<div class="weui-form-preview__item">' +
                '<label class="weui-form-preview__label">???????????????</label>' +
                '<span class="weui-form-preview__value">' + oldaccountid_display + '</span>' +
                '</div>' +
                '<div class="weui-form-preview__item">' +
                '<label class="weui-form-preview__label">????????????</label>' +
                '<span class="weui-form-preview__value">' + mobile + '</span>' +
                '</div>' +
                '<div class="weui-form-preview__item">' +
                '<label class="weui-form-preview__label">T?????????</label>' +
                '<span class="weui-form-preview__value">' + tyunusercodetext + '</span>' +
                '</div>' +
                '</div>' +
                '</div>';
        }
        var packproductid=[];
        var packcategoryid=[];
        var packnumber=[];
        var packspecificationstitle=[];
        var packspecificationsid=[];
        var packspecificationid=[];
        var packproducttitle=[];
        var otherproductid=[];
        var othercategoryid=[];
        var othernumber=[];
        var otherspecificationstitle=[];
        var otherspecificationsid=[];
        var otherspecificationid=[];
        var otherproducttitle=[];
        var otherrenewprice=[];
        var otherprice=[];
        var othermarketrenewprice=[];
        var othermarketprice=[];
        var packSpecificationList=$('.packSpecificationList').filter('input:checked');
        var productSpecificationList=$('.productSpecificationList').filter('input:checked');
        var currentchecked=$('.packSpecificationListgroup').attr('checked');
        $.each(productSpecificationList,function(keyt,valuet){
            otherproductid[keyt]=$(valuet).data('productid');
            othercategoryid[keyt]=$(valuet).data('packid');
            othernumber[keyt]=$(valuet).data('count');
            otherspecificationsid[keyt]=$(valuet).data('productspecificationsid');
            otherspecificationid[keyt]=$(valuet).data('specificationid');
            otherproducttitle[keyt]=$(valuet).data('producttitle');
            otherspecificationstitle[keyt]=$(valuet).data('productspecificationstitle');
            otherrenewprice[keyt]=$(valuet).data('renewprice');
            otherprice[keyt]=$(valuet).data('price');
            othermarketprice[keyt]=$(valuet).data('marketprice');
            othermarketrenewprice[keyt]=$(valuet).data('marketrenewprice');
        });


        var ispackage=0;
        var packageprice =0;
        var packagerenewprice =0;
        var packagemarketprice =0;
        var packagemarketrenewprice =0;
        var packrenewprice = [];
        var packprice = [];
        var packmarketprice = [];
        var packmarketrenewprice = [];
        if(currentchecked){
            ispackage=1;
            //????????????????????????ID
            packageprice = $('.packSpecificationListgroup').data('price');
            packagerenewprice = $('.packSpecificationListgroup').data('renewprice');
            packagemarketprice = $('.packSpecificationListgroup').data('marketprice');
            packagemarketrenewprice = $('.packSpecificationListgroup').data('marketrenewprice');
        }else{
            $.each(packSpecificationList,function(keyt,valuet){
                packproductid[keyt]=$(valuet).data('productid');
                packcategoryid[keyt]=$(valuet).data('packid');
                packnumber[keyt]=$(valuet).data('count');
                packspecificationsid[keyt]=$(valuet).data('productspecificationsid');
                packspecificationid[keyt]=$(valuet).data('specificationid');
                packproducttitle[keyt]=$(valuet).data('producttitle');
                packspecificationstitle[keyt]=$(valuet).data('productspecificationstitle');
                packrenewprice[keyt]=$(valuet).data('renewprice');
                packprice[keyt]=$(valuet).data('price');
                packmarketprice[keyt]=$(valuet).data('marketprice');
                packmarketrenewprice[keyt]=$(valuet).data('marketrenewprice');
            });
        }


        //??????????????????
        var chooseuserproduct = [];
        var packSpecificationList2 = $(".packSpecificationList2").filter('input:checked');
        $.each(packSpecificationList2,function (k2, v2) {
            chooseuserproduct.push(parseInt($(v2).data('productspecificationsid')));
        });
        var authenticationtype = $("#authenticationtype").val();

        var params={
            "servicecontractsid":servicecontractsid,
            "servicecontractsid_display":servicecontractsid_display,
            "accountid":accountid,
            "accountid_display":accountid_display,
            "mobile":mobile,
            "mobilevcode":mobilevcode,
            "classtype":classtype,
            // "categoryid":classtyperenew,
            "type":1,
            "buyyear":buyyear,
            "buydate":buydate,
            // 'packageid':oldproductnameid,
            "tyunusercode":tyunusercode,
            "servicetotal":servicetotal,
            "oldproductname":$('#oldproductname_display').val(),
            "ispackage":ispackage,
            "tyunusercodetext":tyunusercodetext,

            "productclassonevalues":classtyperenew,
            "productclasstwovalues":oldproductnameid,
            "agents":agents,
            "oldcustomerid":oldaccountid,
            "oldcustomername":oldaccountid_display,
            "chooseuserproduct":chooseuserproduct,
            "packageprice":packageprice,
            "packagerenewprice":packagerenewprice,
            "packagemarketprice":packagemarketprice,
            "packagemarketrenewprice":packagemarketrenewprice,
            "activitymodel":4,
            "authenticationtype":authenticationtype
        };
        //?????????????????????start
        params['packproductid']=packproductid;
        params['packproducttitle']=packproducttitle;
        params['packcategoryid']=packcategoryid;
        params['packnumber']=packnumber;
        params['packspecificationsid']=packspecificationsid;
        params['packspecificationid']=packspecificationid;
        params['packspecificationstitle']=packspecificationstitle;
        params['packrenewprice']=packrenewprice;
        params['packprice']=packprice;
        params['packmarketprice']=packmarketprice;
        params['packmarketrenewprice']=packmarketrenewprice;
        //?????????????????????end

        //??????????????????start
        params['otherproductid']=otherproductid;
        params['otherproducttitle']=otherproducttitle;
        params['otherpackcategoryid']=othercategoryid;
        params['otherpacknumber']=othernumber;
        params['otherpackspecificationstitle']=otherspecificationstitle;
        params['otherspecificationsid']=otherspecificationsid;
        params['otherspecificationid']=otherspecificationid;
        params['otherrenewprice']=otherrenewprice;
        params['otherprice']=otherprice;
        params['othermarketprice']=othermarketprice;
        params['othermarketrenewprice']=othermarketrenewprice;
        //??????????????????end

        //????????????start
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
        //????????????end
        if(numberstudentaccounts>0){
            params['numberstudentaccounts']=numberstudentaccounts;
        }
        params['iscollegeedition']=iscollegeedition;
        $.confirm(str, "?????????????????????", function() {
            $.ajax({
                url: '/index.php?module=TyunWebBuyService&action=order',
                type: 'POST',
                dataType: 'json',
                data:params,
                beforeSend:function(){
                    $.showLoading('???????????????');
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
            });
        }, function() {
            //????????????
        });
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

    $("#container").on('input','input[name="elereceivermobile"]',function () {
        initNextStepButton();
    });
    $("#container").on('change','#contactname',function () {
        initNextStepButton();
    });



    $('#container').on('click','.helpinfo',function(){
        var thisInstance=$(this);
        $.alert(thisInstance.data('content'), thisInstance.data('title'));
    });

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

    //??????????????????
    window.econtractLoad=function(){
        var producttype = $("#producttype").val();
        if(producttype=='activity') {
            str2 = '<a class="weui-btn" id="electronsubmitfrom2" href="javascript:void(0);" style="background-color: rgb(73, 148, 242);color:rgb(255, 255, 255);font-size:18px;width:70%;">???????????????????????????</a>\n';
        }else{
            str2 = '<a class="weui-btn" id="electronsubmitfrom" href="javascript:void(0);" style="background-color: rgb(73, 148, 242);color:rgb(255, 255, 255);font-size:18px;width:70%;">???????????????????????????</a>\n';
        }
        $("#econtractback").after(str2);
        var contractid = $("#contractid").data('value');
        $("#ifmcontent").attr('src','/index.php?module=TyunWebBuyService&action=pdf&contractid='+contractid);
    };


    //????????????????????????
    $('#container').on('click','#electronnextstep',function(){
        var submitid=$('#submitid').attr('data-value');
        if(submitid!=2){
            return false;
        }
        $("#templateid").attr('data-value',0);
        var classtype=$('#classtype').val();
        var producttype = $("#producttype").val();

        var productclasstwo = $("#oldproductname_display").val();
        var productclasstwovalues = $("#oldproductnameid").val();
        var currentchecked=$('.packSpecificationListgroup').attr('checked');
        if(currentchecked){
            var templateParams = {
                "productCode":[productclasstwovalues],
                "servicecontractstype":2,
                "isPackage":1,
                "orderType":producttype
            }
            var isPackage = 1;
            var productCode = [productclasstwovalues];
        } else {
            var matchTemplateProductIds = [];
            var packSpecificationList=$('.packSpecificationList').filter('input:checked');
            var productSpecificationList=$('.productSpecificationList').filter('input:checked');
            $.each(productSpecificationList,function(keyt,valuet){
                matchTemplateProductIds.push($(valuet).data('productid'))
            });
            $.each(packSpecificationList,function(keyt,valuet){
                matchTemplateProductIds.push($(valuet).data('productid'))
            });

            var templateParams = {
                "productCode":matchTemplateProductIds,
                "servicecontractstype":2,
                "isPackage":0,
                "orderType":producttype
            }
            var isPackage = 0;
            var productCode = matchTemplateProductIds;
        }

        // if(!isPackage){
        //     $.alert('???????????????,???????????????????????????','??????');
        //     return ;
        // }

        $.ajax({
                url: '/index.php?module=TyunWebBuyService&action=matchElecContractTemplate',
                type: 'POST',
                dataType: 'json',
                data:templateParams,
                beforeSend:function(){
                    $.showLoading('?????????');
                },
                success: function (data) {
                    $.hideLoading();
                    if(data.success==1){
                        datas = data.data;
                        if(datas.length>1 || datas.length==0){
                            $.alert('??????????????????????????????????????????????????????????????????????????????????????????????????????','??????');
                            return ;
                        }
                        var templateId = datas[0]['templateId'];
                        $("#templateid").attr('data-value',templateId);
                        console.log(templateId);
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
                        var classtyperename=$('#classtyperename').val();
                        // var classtyperename=$('#classtyperenew').find("option:checked").text();
                        var tyunusercode=$('#tyunusercode').val();
                        var tyunusercodetext=$('#tyunusercode').find("option:checked").text();
                        var oldproductname_display=$('#oldproductname_display').val();
                        var separateproductname_display= '';
                        var agents=$('#instanceagents').data('value');

                        var companyid = $("#owncompany").val();
                        var elereceiver = $("#contactname").val();
                        var elereceivermobile = $("#elereceivermobile").val();

                        //????????????start
                        var productid=[];
                        var categoryid=[];
                        var number=[];
                        var id=[];
                        var price=[];
                        var renewprice=[];
                        var marketrenewprice = [];
                        var marketprice = [];
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
                            marketrenewprice[keyt]=$(valuet).data('marketrenewprice');
                            marketprice[keyt]=$(valuet).data('marketprice');
                            unit[keyt]=$(valuet).data('unit');
                            specificationstitle[keyt]=$(valuet).data('specificationstitle');
                            separateproductname_display += $(valuet).data('producttitle')+'('+$(valuet).data('number')+') ';
                        });

                        //????????????end
                        var buydate=$('#buydate').val();
                        if($('#buydateshow').data('value')!=1){
                            var buydatestr='';
                        }else{
                            if(buydate==''){
                                $.toast('??????????????????',"text");
                                return false;
                            }
                            var buydatestr=$('#buydateshow').data('value')!=1?'':'<div class="weui-form-preview__item">' +
                                '<label class="weui-form-preview__label">????????????</label>' +
                                '<span class="weui-form-preview__value">'+buydate+'</span>' +
                                '</div>'
                        }
                        var str='<div class="weui-form-preview">' +
                            '<div class="weui-form-preview__bd">' +
                            '<div class="weui-form-preview__item">' +
                            '<label class="weui-form-preview__label">????????????</label>' +
                            '<span class="weui-form-preview__value">'+classtyperename+'</span>' +
                            '</div>' +
                            '<div class="weui-form-preview__item">' +
                            '<label class="weui-form-preview__label">?????????</label>' +
                            '<span class="weui-form-preview__value">'+oldproductname_display+'</span>' +
                            '</div>' +
                            '<div class="weui-form-preview__item">' +
                            '<label class="weui-form-preview__label">????????????</label>' +
                            '<span class="weui-form-preview__value">'+separateproductname_display+'</span>' +
                            '</div>' +
                            '<div class="weui-form-preview__item">' +
                            '<label class="weui-form-preview__label">????????????</label>' +
                            '<span class="weui-form-preview__value">???'+servicetotal+'</span>' +
                            '</div>' +
                            '<div class="weui-form-preview__item">' +
                            '<label class="weui-form-preview__label">????????????</label>' +
                            '<span class="weui-form-preview__value">'+buyyear+'???</span>' +
                            '</div>' +
                            '<div class="weui-form-preview__item">' +
                            '<label class="weui-form-preview__label">????????????</label>' +
                            '<span class="weui-form-preview__value">'+accountid_display+'</span>' +
                            '</div>' +
                            '<div class="weui-form-preview__item">' +
                            '<label class="weui-form-preview__label">???????????????</label>' +
                            '<span class="weui-form-preview__value">'+oldaccountid_display+'</span>' +
                            '</div>' +
                            '<div class="weui-form-preview__item">' +
                            '<label class="weui-form-preview__label">????????????</label>' +
                            '<span class="weui-form-preview__value">'+mobile+'</span>' +
                            '</div>' +
                            '<div class="weui-form-preview__item">' +
                            '<label class="weui-form-preview__label">T?????????</label>' +
                            '<span class="weui-form-preview__value">'+tyunusercodetext+'</span>' +
                            '</div>' +
                            '</div>' +
                            '</div>';
                        var packproductid=[];
                        var packcategoryid=[];
                        var packnumber=[];
                        var packspecificationstitle=[];
                        var packspecificationsid=[];
                        var packspecificationid=[];
                        var packproducttitle=[];
                        var otherproductid=[];
                        var othercategoryid=[];
                        var othernumber=[];
                        var otherspecificationstitle=[];
                        var otherspecificationsid=[];
                        var otherspecificationid=[];
                        var otherproducttitle=[];
                        var otherrenewprice=[];
                        var otherprice=[];
                        var othermarketrenewprice=[];
                        var othermarketprice=[];
                        var packSpecificationList=$('.packSpecificationList').filter('input:checked');
                        var productSpecificationList=$('.productSpecificationList').filter('input:checked');
                        var currentchecked=$('.packSpecificationListgroup').attr('checked');
                        $.each(productSpecificationList,function(keyt,valuet){
                            otherproductid[keyt]=$(valuet).data('productid');
                            othercategoryid[keyt]=$(valuet).data('packid');
                            othernumber[keyt]=$(valuet).data('count');
                            otherspecificationsid[keyt]=$(valuet).data('productspecificationsid');
                            otherspecificationid[keyt]=$(valuet).data('specificationid');
                            otherproducttitle[keyt]=$(valuet).data('producttitle');
                            otherspecificationstitle[keyt]=$(valuet).data('productspecificationstitle');
                            otherrenewprice[keyt]=$(valuet).data('renewprice');
                            otherprice[keyt]=$(valuet).data('price');
                            othermarketprice[keyt]=$(valuet).data('marketprice');
                            othermarketrenewprice[keyt]=$(valuet).data('marketrenewprice');
                        });


                        var ispackage=0;
                        var packageprice =0;
                        var packagerenewprice =0;
                        var packagemarketprice =0;
                        var packagemarketrenewprice =0;
                        var packrenewprice = [];
                        var packprice = [];
                        var packmarketprice = [];
                        var packmarketrenewprice = [];
                        if(currentchecked){
                            ispackage=1;
                            //????????????????????????ID
                            packageprice = $('.packSpecificationListgroup').data('price');
                            packagerenewprice = $('.packSpecificationListgroup').data('renewprice');
                            packagemarketprice = $('.packSpecificationListgroup').data('marketprice');
                            packagemarketrenewprice = $('.packSpecificationListgroup').data('marketrenewprice');
                        }else{
                            $.each(packSpecificationList,function(keyt,valuet){
                                packproductid[keyt]=$(valuet).data('productid');
                                packcategoryid[keyt]=$(valuet).data('packid');
                                packnumber[keyt]=$(valuet).data('count');
                                packspecificationsid[keyt]=$(valuet).data('productspecificationsid');
                                packspecificationid[keyt]=$(valuet).data('specificationid');
                                packproducttitle[keyt]=$(valuet).data('producttitle');
                                packspecificationstitle[keyt]=$(valuet).data('productspecificationstitle');
                                packrenewprice[keyt]=$(valuet).data('renewprice');
                                packprice[keyt]=$(valuet).data('price');
                                packmarketprice[keyt]=$(valuet).data('marketprice');
                                packmarketrenewprice[keyt]=$(valuet).data('marketrenewprice');
                            });
                        }


                        //??????????????????
                        var chooseuserproduct = [];
                        var packSpecificationList2 = $(".packSpecificationList2").filter('input:checked');
                        $.each(packSpecificationList2,function (k2, v2) {
                            chooseuserproduct.push(parseInt($(v2).data('productspecificationsid')));
                        });
                        var authenticationtype = $("#authenticationtype").val();

                        var params={
                            "servicecontractsid":servicecontractsid,
                            "servicecontractsid_display":servicecontractsid_display,
                            "accountid":accountid,
                            "accountid_display":accountid_display,
                            "mobile":mobile,
                            "mobilevcode":mobilevcode,
                            "classtype":classtype,
                            // "categoryid":classtyperenew,
                            "type":1,
                            "buyyear":buyyear,
                            "buydate":buydate,
                            // 'packageid':oldproductnameid,
                            "tyunusercode":tyunusercode,
                            "servicetotal":servicetotal,
                            "oldproductname":$('#oldproductname_display').val(),
                            "ispackage":ispackage,
                            "tyunusercodetext":tyunusercodetext,

                            "productclassonevalues":classtyperenew,
                            "productclasstwovalues":oldproductnameid,
                            "agents":agents,
                            "oldcustomerid":oldaccountid,
                            "oldcustomername":oldaccountid_display,
                            "chooseuserproduct":chooseuserproduct,
                            "packageprice":packageprice,
                            "packagerenewprice":packagerenewprice,
                            "packagemarketprice":packagemarketprice,
                            "packagemarketrenewprice":packagemarketrenewprice,
                            "activitymodel":4,
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
                        //?????????????????????start
                        params['packproductid']=packproductid;
                        params['packproducttitle']=packproducttitle;
                        params['packcategoryid']=packcategoryid;
                        params['packnumber']=packnumber;
                        params['packspecificationsid']=packspecificationsid;
                        params['packspecificationid']=packspecificationid;
                        params['packspecificationstitle']=packspecificationstitle;
                        params['packrenewprice']=packrenewprice;
                        params['packprice']=packprice;
                        params['packmarketprice']=packmarketprice;
                        params['packmarketrenewprice']=packmarketrenewprice;
                        //?????????????????????end

                        //??????????????????start
                        params['otherproductid']=otherproductid;
                        params['otherproducttitle']=otherproducttitle;
                        params['otherpackcategoryid']=othercategoryid;
                        params['otherpacknumber']=othernumber;
                        params['otherpackspecificationstitle']=otherspecificationstitle;
                        params['otherspecificationsid']=otherspecificationsid;
                        params['otherspecificationid']=otherspecificationid;
                        params['otherrenewprice']=otherrenewprice;
                        params['otherprice']=otherprice;
                        params['othermarketprice']=othermarketprice;
                        params['othermarketrenewprice']=othermarketrenewprice;
                        //??????????????????end

                        //????????????start
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
                        //????????????end

                        $.confirm(str, "?????????????????????", function() {
                            $.ajax({
                                url: '/index.php?module=TyunWebBuyService&action=preOrder',
                                type: 'POST',
                                dataType: 'json',
                                data:params,
                                beforeSend:function(){
                                    $.showLoading('???????????????');
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
                            });
                        }, function() {
                            //????????????
                        });
                    }else{
                        $.toast(data.msg, "forbidden");
                        return ;
                    }
                }
            }
        );

    });

    //??????????????????
    $('#container').on('click','#electronsubmitfrom',function() {
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
                $.showLoading('?????????');
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
                        prestr = '???????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????'+elereceivermobilestr+'(???????????????????????????)';
                        isverify = 1;
                    }else{
                        prestr = '????????????????????????????????????????????????'+elereceivermobilestr+'(???????????????????????????)';
                    }

                    var str = '<div class="weui-form-preview">' +
                        '<div class="weui-form-preview__bd">' +
                        '<div class="weui-form-preview__item">' +
                        '<span class="weui-form-preview__value" style="text-align: center;">' + prestr + '</span>' +
                        '</div>' +
                        '</div>' +
                        '</div>';
                    $.confirm(str, "??????", function () {
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
                        var classtyperename=$("#classtyperename").val();
                        // var classtyperename=$('#classtyperenew').find("option:checked").text();
                        var tyunusercode=$('#tyunusercode').val();
                        var tyunusercodetext=$('#tyunusercode').find("option:checked").text();
                        var oldproductname_display=$('#oldproductname_display').val();
                        var agents=$('#instanceagents').data('value');
                        var companyid = $("#owncompany").val();
                        var elereceiver = $("#contactname").val();
                        var elereceivermobile = $("#elereceivermobile").val();
                        var contractid = $("#contractid").data('value');
                        var invoicecompany = $("#owncompany").find("option:checked").text();
                        var invoicecompanyid = $("#owncompany").val();
                        var signaturetype = $("#signaturetype").val();
                        var paycode = $("#paycode").data('value');
                        var templateId = $("#templateid").data('value');
                        var productclasstwo = $("#oldproductname_display").val();


                        //????????????start
                        var productid=[];
                        var categoryid=[];
                        var number=[];
                        var id=[];
                        var price=[];
                        var renewprice=[];
                        var marketrenewprice = [];
                        var marketprice = [];
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
                            marketrenewprice[keyt]=$(valuet).data('marketrenewprice');
                            marketprice[keyt]=$(valuet).data('marketprice');
                            unit[keyt]=$(valuet).data('unit');
                            specificationstitle[keyt]=$(valuet).data('specificationstitle');
                        });

                        //????????????end
                        var buydate=$('#buydate').val();

                        var packproductid=[];
                        var packcategoryid=[];
                        var packnumber=[];
                        var packspecificationstitle=[];
                        var packspecificationsid=[];
                        var packspecificationid=[];
                        var packproducttitle=[];
                        var otherproductid=[];
                        var othercategoryid=[];
                        var othernumber=[];
                        var otherspecificationstitle=[];
                        var otherspecificationsid=[];
                        var otherspecificationid=[];
                        var otherproducttitle=[];
                        var otherrenewprice=[];
                        var otherprice=[];
                        var othermarketrenewprice=[];
                        var othermarketprice=[];
                        var packSpecificationList=$('.packSpecificationList').filter('input:checked');
                        var productSpecificationList=$('.productSpecificationList').filter('input:checked');
                        var currentchecked=$('.packSpecificationListgroup').attr('checked');
                        $.each(productSpecificationList,function(keyt,valuet){
                            otherproductid[keyt]=$(valuet).data('productid');
                            othercategoryid[keyt]=$(valuet).data('packid');
                            othernumber[keyt]=$(valuet).data('count');
                            otherspecificationsid[keyt]=$(valuet).data('productspecificationsid');
                            otherspecificationid[keyt]=$(valuet).data('specificationid');
                            otherproducttitle[keyt]=$(valuet).data('producttitle');
                            otherspecificationstitle[keyt]=$(valuet).data('productspecificationstitle');
                            otherrenewprice[keyt]=$(valuet).data('renewprice');
                            otherprice[keyt]=$(valuet).data('price');
                            othermarketprice[keyt]=$(valuet).data('marketprice');
                            othermarketrenewprice[keyt]=$(valuet).data('marketrenewprice');
                        });


                        var ispackage=0;
                        var packageprice =0;
                        var packagerenewprice =0;
                        var packagemarketprice =0;
                        var packagemarketrenewprice =0;
                        var packrenewprice = [];
                        var packprice = [];
                        var packmarketprice = [];
                        var packmarketrenewprice = [];
                        if(currentchecked){
                            ispackage=1;
                            //????????????????????????ID
                            packageprice = $('.packSpecificationListgroup').data('price');
                            packagerenewprice = $('.packSpecificationListgroup').data('renewprice');
                            packagemarketprice = $('.packSpecificationListgroup').data('marketprice');
                            packagemarketrenewprice = $('.packSpecificationListgroup').data('marketrenewprice');
                        }else{
                            $.each(packSpecificationList,function(keyt,valuet){
                                packproductid[keyt]=$(valuet).data('productid');
                                packcategoryid[keyt]=$(valuet).data('packid');
                                packnumber[keyt]=$(valuet).data('count');
                                packspecificationsid[keyt]=$(valuet).data('productspecificationsid');
                                packspecificationid[keyt]=$(valuet).data('specificationid');
                                packproducttitle[keyt]=$(valuet).data('producttitle');
                                packspecificationstitle[keyt]=$(valuet).data('productspecificationstitle');
                                packrenewprice[keyt]=$(valuet).data('renewprice');
                                packprice[keyt]=$(valuet).data('price');
                                packmarketprice[keyt]=$(valuet).data('marketprice');
                                packmarketrenewprice[keyt]=$(valuet).data('marketrenewprice');
                            });
                        }


                        //??????????????????
                        var chooseuserproduct = [];
                        var packSpecificationList2 = $(".packSpecificationList2").filter('input:checked');
                        $.each(packSpecificationList2,function (k2, v2) {
                            chooseuserproduct.push(parseInt($(v2).data('productspecificationsid')));
                        });
                        var authenticationtype = $("#authenticationtype").val();
                        var eleccontracturl = $("#eleccontracturl").data("value");

                        var params={
                            "servicecontractsid":servicecontractsid,
                            "servicecontractsid_display":servicecontractsid_display,
                            "accountid":accountid,
                            "accountid_display":accountid_display,
                            "mobile":mobile,
                            "mobilevcode":mobilevcode,
                            "classtype":classtype,
                            // "categoryid":classtyperenew,
                            "type":1,
                            "buyyear":buyyear,
                            "buydate":buydate,
                            // 'packageid':oldproductnameid,
                            "tyunusercode":tyunusercode,
                            "servicetotal":servicetotal,
                            "oldproductname":$('#oldproductname_display').val(),
                            "ispackage":ispackage,
                            "tyunusercodetext":tyunusercodetext,

                            "productclassonevalues":classtyperenew,
                            "productclasstwovalues":oldproductnameid,
                            "agents":agents,
                            "oldcustomerid":oldaccountid,
                            "oldcustomername":oldaccountid_display,
                            "chooseuserproduct":chooseuserproduct,
                            "packageprice":packageprice,
                            "packagerenewprice":packagerenewprice,
                            "packagemarketprice":packagemarketprice,
                            "packagemarketrenewprice":packagemarketrenewprice,
                            "activitymodel":4,
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
                            "eleccontracturl":eleccontracturl,
                        };
                        //?????????????????????start
                        params['packproductid']=packproductid;
                        params['packproducttitle']=packproducttitle;
                        params['packcategoryid']=packcategoryid;
                        params['packnumber']=packnumber;
                        params['packspecificationsid']=packspecificationsid;
                        params['packspecificationid']=packspecificationid;
                        params['packspecificationstitle']=packspecificationstitle;
                        params['packrenewprice']=packrenewprice;
                        params['packprice']=packprice;
                        params['packmarketprice']=packmarketprice;
                        params['packmarketrenewprice']=packmarketrenewprice;
                        //?????????????????????end

                        //??????????????????start
                        params['otherproductid']=otherproductid;
                        params['otherproducttitle']=otherproducttitle;
                        params['otherpackcategoryid']=othercategoryid;
                        params['otherpacknumber']=othernumber;
                        params['otherpackspecificationstitle']=otherspecificationstitle;
                        params['otherspecificationsid']=otherspecificationsid;
                        params['otherspecificationid']=otherspecificationid;
                        params['otherrenewprice']=otherrenewprice;
                        params['otherprice']=otherprice;
                        params['othermarketprice']=othermarketprice;
                        params['othermarketrenewprice']=othermarketrenewprice;
                        //??????????????????end

                        //????????????start
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
                        //????????????end
                        $.ajax(
                            {
                                url: '/index.php?module=TyunWebBuyService&action=elecContractAddOrder',
                                type: 'POST',
                                dataType: 'json',
                                data:params,
                                beforeSend:function(){
                                    $.showLoading('???????????????');
                                },
                                success: function (data) {
                                    $.hideLoading();
                                    console.log(data);
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


    window.clearHasexistData=function(){
        console.log('clearHasexistData');
        $("#oldaccountidowenid").val("");
        $("#oldaccountid").val("");
        $("input[name='oldaccountid_display']").val("");
        $("#oldproductname_display").val("");
        $("#oldproductnameid").val("");
        $("#activationcodeid").val("");
        $("#oldproductname_display").val("");
        $("#numberstudentaccounts").val("");
        $("#oldcontract").val("");
        $("#expirationdate").val("");
        $(".lately_add_display").text("");
    }

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
                $.showLoading('?????????????????????');
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