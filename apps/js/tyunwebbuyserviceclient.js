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
            this.clearDom();
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
        },
        clearDom:function(){
            $('.weui-picker-container').remove();
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
            $('#tyunusercodeonoff').val(1);
        });
    }
    $('#container').on("blur",'#tyunusercode,input[name="accountid_display"],input[name="servicecontractsid_display"],#mobile,#mobilevcode,#classtyperenew,#servicetotal',function(event){
        window.scroll(0,0);
    });
    $('body').on('blur','#weui-prompt-input',function(){
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
                    $.each(data.data,function(key,value){
                        var selected='';
                        if(key==0){
                            selected='selected';
                        }
                        $("#tyunusercode").append('<option value="'+value.id+'" '+selected+'>'+value.loginName+'</option>');
                    });
                    $('#tyunusercodeonoff').val(2);
                    $(thisInstance).hide();
                    $('#tyunusercode').trigger('click');
                    $('#nextstep').css({"background-color":'#0B92D9'});
                }else{
                    $.toast(data.msg,'text');
                }
            }
        });
        event.stopPropagation();
        return false;
    });
    $('#container').on('click','#registerd',function(){
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
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
        $.prompt({
            title: '立即注册',
            input: '',
            empty: false, // 是否允许为空
            onOK: function (input) {
                window.scroll(0,0);
                $.ajax({
                    url: '/index.php?module=TyunWebBuyService&action=regTyunUserCode',
                    type: 'POST',
                    dataType: 'json',
                    data:{"servicecontractsid":servicecontractsid,
                        "servicecontractsid_display":servicecontractsid_display,
                        "accountid":accountid,
                        "accountid_display":accountid_display,
                        "mobile":mobile,
                        "mobilevcode":mobilevcode,
                        "classtype":classtype,
                        "agents":agents,
                        "usercode":input
                    },
                    beforeSend:function(){
                        $.showLoading();
                    },
                    success: function (data) {
                        $.hideLoading();
                        if (data.success==1) {
                            $("#tyunusercode")[0].options.length=0;
                            $.each(data.data,function(key,value){
                                var selected='';
                                if(value.loginName==input){
                                    selected='selected';
                                }
                                $("#tyunusercode").append('<option value="'+value.id+'" '+selected+'>'+value.loginName+'</option>');
                            });
                            $('#tyunusercodeonoff').val(2);
                            $('#clickhide').hide();
                            $('#tyunusercode').trigger('click');
                            $('#nextstep').css({"background-color":'#0B92D9'});
                        }else{
                            $.toast(data.msg,'text');
                        }
                    }
                });
            },
            onCancel: function () {
                window.scroll(0,0);
            }
        });
        $('#weui-prompt-input').attr('placeholder','请输入用户名(必填)');
    });
    /*$('#container').on('click','#tyunusercode',function(){
        var tyunusercodeonoff=$('#tyunusercodeonoff').val();
        if(tyunusercodeonoff==1){
            $("#tyunusercode")[0].options.length=1;
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
                        $.each(data.data,function(key,value){
                            var selected='';
                            if(key==0){
                                selected='selected';
                            }
                            $("#tyunusercode").append('<option value="'+value.id+'" '+selected+'>'+value.loginName+'</option>');
                        });
                        $('#tyunusercodeonoff').val(2);
                        $('#nextstep').css({"background-color":'#0B92D9'});
                    }else{
                       $.toast(data.msg,'text');
                    }
                }
            });
        }
    });*/
    $('#container').on('click','#nextstep',function(){

        if($('#tyunusercode').val()==''){
            return false;
        }
        var checkTyunExistBuyReturn=$('#checkTyunExistBuyReturn').attr('data-value');
        if(checkTyunExistBuyReturn!=1){
            checkTyunExistBuy();
            return false;
        }
        //window.selectproducttitle=$('input[name="accountid_display"]').val();
        //sessionStorage.accountid_display=$('input[name="accountid_display"]').val();
        var tyunusercodeonoff=$('#tyunusercodeonoff').val();
        if(tyunusercodeonoff==1){
            return false;
        }else{
            if(checkGetUserCode()){
                return false;
            }
            window.selectproducttitle=$('input[name="accountid_display"]').val();

            pageManager.go('selectproduct');


            sessionStorage.servicecontractsid=$('input[name="servicecontractsid"]').val();
            sessionStorage.servicecontractsid_display=$('input[name="servicecontractsid_display"]').val();
            sessionStorage.accountid=$('input[name="accountid"]').val();
            sessionStorage.accountid_display=$('input[name="accountid_display"]').val();
            sessionStorage.mobile=$('#mobile').val();
            sessionStorage.mobilevcode=$('#mobilevcode').val();
            sessionStorage.classtype=$('#classtype').val();
        }
    });
    function checkGetUserCode(){
        var servicecontractsid=$('input[name="servicecontractsid"]').val();
        var accountid=$('input[name="accountid"]').val();
        var checkmobilevcode=$('#checkmobilevcode').val();
        var checkmobile=$('#checkmobile').val();
        var mobile=$('#mobile').val();
        var mobilevcode=$('#mobilevcode').val();
        var msg='';
        var checkflag=true;
        do{
            if(servicecontractsid<=0){
                msg='请选择合同编号';
                break;
            }
            if(accountid<=0){
                msg='请选择客户名称';
                break;
            }
            if(mobile==''){
                msg='请填写要验证的手机号码';
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
            /*if(checkmobilevcode!=mobilevcode){
                msg='验证码无效，请重新获取';
                break;
            }*/
            if(mobile!=checkmobile){
                msg='验证码失效，请重新获取';
                break;
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
    });
    $('#container').on("click",".weui-count__decrease",function(event){
        var id=$(this).data('fartherid1');
        if(!$('.select2farther'+id).attr('checked')){
            return false;
        }
        var countnumber=$('.countnumber'+id).val();
        if(countnumber>1){
            countnumber--;
            $('.countnumber'+id).val(countnumber);
            $('.select2farther'+id).attr('data-number',countnumber);
            var price=$('.select2farther'+id).data('price');
            if(price>0){
                $('.price'+id).text('￥'+parseFloat(FloatMul(price,parseInt(countnumber))).toFixed(2));
            }
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
            if(price>0){
                $('.price'+id).text('￥'+parseFloat(FloatMul(price,parseInt(countnumber))).toFixed(2));
            }
            return false;
        // }else{
        //     $.toast('哎哟，不能再添加了哦',"text");
        // }

    });
    $('#container').on("click",".selectProductSpecifications",function(event){
        var id=$(this).data('fartherid1');
        if(!$('.select2farther'+id).attr('checked')){
            return false;
        }
        var price=$(this).data('price');
        var renewprice=$(this).data('renewprice');
        var unit=$(this).data('unit');
        var dataid=$(this).data('id');
        var specificationstitle=$(this).data('specificationstitle');
        $(this).removeClass("weui-btn_disabled");
        $('.ProductSpecifications'+id).not(this).addClass("weui-btn_disabled");
        var countnumber=$('.countnumber'+id).val();
        $('.ProductTitlePrice'+id).text('￥'+parseFloat(price).toFixed(2));
        $('.unit'+id).text(unit);
        $('.price'+id).text('￥'+parseFloat(parseInt(price)*parseInt(countnumber)).toFixed(2));
        $('.select2farther'+id).attr('data-price',price);
        $('.select2farther'+id).attr('data-renewprice',renewprice);
        $('.select2farther'+id).attr('data-unit',unit);
        $('.select2farther'+id).attr('data-id',dataid);
        $('.select2farther'+id).attr('data-specificationstitle',specificationstitle);
        $('.select2farther'+id).attr('data-specificationsid',specificationstitle);
    });
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
                    '                                        <a href="javascript:;" class="weui-btn weui-btn_mini weui-btn_default weui-btn_default_noborder" style="margin-top:0px;background-color: #fff;font-size:16px;padding:0;">'+$(value).data('producttitle')+'</a>\n' +
                    '                                        <a href="javascript:;" class="weui-btn weui-btn_mini weui-btn_default" style="margin-top:0px;">'+$(value).data('specificationstitle')+'</a>\n' +
                    '                                    </div>\n' +
                    '                                </div>\n' +
                    '                            </div>\n' +
                    '                        </div>\n' +
                    '                        <div class="weui-media-box__title">\n' +
                    '                            <div class="weui-cell" style="padding:0;">\n' +
                    '                                <div class="weui-cell__bd">\n' +
                    '                                    <label class="weui-label" style="margin-left:0px;font-size:14px;">购:￥'+parseFloat($(value).data('price')).toFixed(2)+'&nbsp;续:￥'+parseFloat($(value).data('renewprice')).toFixed(2)+'</label>\n' +
                    '                                </div>\n' +
                    '                                <div class="weui-cell__ft">\n' +
                    '                                    <div class="weui-count weui-count_custrom"  style="height: 25px";>\n' +
                    '                                        <a class="weui-count__btn weui-count__sub weui-count__left_custrom" data-id="'+$(value).data('productid')+'"></a>\n' +
                    '                                        <input class="weui-count__number  weui-count__current_number'+$(value).data('productid')+'" disabled type="number" value="'+$(value).data('number')+'" readonly="" style="-webkit-appearance:none !important;border-radius: 0 !important;opacity: 1;">\n' +
                    '                                        <a class="weui-count__btn weui-count__add weui-count__right_custrom" data-id="'+$(value).data('productid')+'"></a>\n' +
                    '                                        <a class="weui-count__btn weui-count__decrease weui-count__decrease_deleted" style="border:none;margin-left:5px;" data-id="'+$(value).data('productid')+'"></a>\n' +
                    '                                    </div>\n' +
                    '                                </div>\n' +
                    '                            </div>\n' +
                    '                        </div>\n' +
                    '                        <div class="weui-media-box__desc">\n' +
                    '                            <div class="weui-cell" style="padding-left:0px;">\n' +
                    '                                <div class="weui-cell__bd">\n' +
                    '                                <div class="weui-count">\n' +
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
                '                                    <div class="weui-count">\n' +
                '                                        <a class="" style="color:#000000;font-size:16px;">另购合计：</a>\n' +
                '                                        <a class="pricelast" style="color:#000000;font-size:16px;">￥'+parseFloat(sumprice).toFixed(2)+'</a>\n' +
                '                                    </div>\n' +
                '                                </div>\n' +
                '                            </div>\n' +
                '                        </div>\n' +
                '                    </div>\n' +
                '                </div>';
            $('.otherproductlist').append(str);
            pageManager.back();
        }, function() {
            //取消操作
        });
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
            location.href="/index.php?module=TyunWebBuyServiceClient&action=renew";
        }
        if($(this).val()=='upgrade'){
            location.href="/index.php?module=TyunWebBuyServiceClient&action=upgrade";
        }
        if($(this).val()=='degrade'){
            location.href="/index.php?module=TyunWebBuyServiceClient&action=degrade";
        }
    });
    window.otherproductLoad=function(){
        $.ajax(
            {
                url: '/index.php?module=TyunWebBuyService&action=getOtherPorduct',
                type: 'POST',
                dataType: 'json',
                data:{"categoryID":$("#productclassone").data('values'),
                    "seriesID":1,
                    "userID":$('#tyunusercode').val(),
                    "authenticationType":1
                },
                beforeSend:function(){
                    $.showLoading('另购产品加载中');
                },
                success: function (data) {
                    $.hideLoading();
                    if(data.success && data.data.length>0){
                        var datas=data.data
                        updateOtherProduct(datas);
                    }
                }
            }
        );
    }
    function updateOtherProduct(datas){
        var allstr='';
        var productclassone=$('#productclassone').attr('data-values');
        $.each(datas,function(key,value){
            var groupname='groupname'+key;
            var fartherid=key;
            var othershowflag=0;
            var str='<div class="weui-panel weui-cells weui-cells_checkbox">\n' +
                '                <div class="weui-panel__hd" style="padding:0;">\n' +
                '                    <label class="weui-cell weui-check__label">\n' +
                '                        <div class="weui-cell__hd">\n' +
                '                            <input type="checkbox" class="weui-check selectGroupName selectGroup'+key+'" data-id="'+key+'">\n' +
                '                            <i class="weui-icon-checked"></i>\n' +
                '                        </div>\n' +
                '                        <div class="weui-cell__bd">\n' +
                '                            <p style="font-size:20px;">'+value['GroupName']+'</p>\n' +
                '                        </div>\n' +
                '                    </label>\n' +
                '                </div>\n'+
                '                <div class="weui-panel__bd ">\n';
            $.each(value['Products'],function(k1,v1){
                if(!v1['CanSeparatePurchase'] || productclassone!=v1['CategoryID']){
                    return true;
                }
                othershowflag=1;
                var fartherid1=v1['ProductID'];
                str+=
                    '<div class="weui-media-box weui-media-box_appmsg">\n' +
                    '    <div class="weui-media-box__hd">\n' +
                    '        <label class="weui-cell weui-check__label">\n' +
                    '            <div class="weui-cell__hd">\n' +
                    '                <input type="checkbox" class="weui-check  '+groupname+' select2check select2farther'+fartherid1+'" data-currentid="'+v1['ProductID']+'" data-fartherid="'+fartherid+'" data-producttitle="'+v1['ProductTitle']+'" data-groupname="'+value['GroupName']+'" data-fartherid1="'+fartherid1+'" data-productid="'+v1['ProductID']+'" data-candiscount="'+v1['CanDiscount']+'" data-categoryid="'+v1['CategoryID']+'" data-producttitle="'+v1['ProductTitle']+'" data-number="1" data-count="1" data-id=""  data-price=""  data-renewprice="" data-unit="" data-specificationstitle="" data-uselimit="'+v1.UseLimit+'" data-userbuycount="'+v1.UserBuyCount+'" >\n' +
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
                    '                    <span class="ProductTitlePrice'+fartherid1+'"></span>\n' +
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
                        str+='<a href="javascript:;" class="weui-btn weui-btn_mini selectProductSpecifications weui-btn_disabled weui-btn_default ProductSpecifications'+fartherid1+' ProductSpecificationsmodify'+v2['ID']+'" style="margin-top:0px;" data-fartherid="'+fartherid+'" data-fartherid1="'+fartherid1+'" data-fartherid2="'+fartherid2+'" data-count="'+v2['Count']+'"  data-id="'+v2['ID']+'"  data-price="'+v2['Price']+'"  data-renewprice="'+v2['RenewPrice']+'" data-unit="'+v2['Unit']+'" data-specificationstitle="'+v2['Title']+'">'+v2['Title']+'</a>\n';

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
                    '                                            <input class="weui-count__number countnumber'+fartherid1+'" disabled style="width:1.5rem;-webkit-appearance:none !important;border-radius: 0 !important;opacity:1;" type="number" value="1" readonly data-currentid="'+v1['ProductID']+'" data-fartherid="'+fartherid+'" data-fartherid1="'+fartherid1+'" data-candiscount="'+v1['CanDiscount']+'" data-categoryid="'+v1['CategoryID']+'" data-productid="'+v1['ProductID']+'" data-producttitle="'+v1['ProductTitle']+'" >\n' +
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
        });
        $('#page_othgerproduct').append(allstr);
        var groupid=[]
        $.each($('.select3check'),function(key,value){
            var productid=$(value).data('productid');
            var fartherid=$(value).data('fartherid');
            var fartherid1=$(value).data('fartherid1');
            var ProductSpecificationsid=$(value).data('id');
            var number=$(value).data('number');
            $('.select2farther'+fartherid1).trigger('click');
            $('.ProductSpecificationsmodify'+ProductSpecificationsid).trigger("click");
            var price=$(value).data('price');
            $('.price'+fartherid1).text('￥'+parseFloat(FloatMul(price,number)).toFixed(2));
            $('.select2farther'+fartherid1).attr('data-number',number);
            $('.ProductTitlePrice'+fartherid1).text('￥'+parseFloat(price).toFixed(2));
            $('.countnumber'+fartherid1).val(number);

        });

    }
    $('#container').on('click','#submitfrom',function(){
        var submitid=$('#submitid').attr('data-value');
        if(submitid!=2){
            return false;
        }
        var productclasstwovalues=$('#productclasstwo').data('values');
        if(productclasstwovalues==undefined){
            $.toast('请选择要购买的套餐或单品',"text");
            return false;
        }
        if(productclasstwovalues=='nobuypack'){
            if($('.select3check').length==0){
                $.toast('请选择要购买的单品',"text");
                return false;
            }
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
        var productclassone=$('#productclassone').val();
        var productclassonevalues=$('#productclassone').data('values');
        var productclasstwo=$('#productclasstwo').val();

        var buyyear=$('#buyyear').data('values');
        buyyear=buyyear>0?buyyear:0;
        var servicetotal=parseFloat($('#servicetotal').val()).toFixed(2);
        var buydate=$('#buydate').val();
        var agents=$('#instanceagents').data('value');
        if($('#buydateshow').data('value')!=1){
            var buydatestr='';
        }else{
            if(buydate==''){
                $.toast('购买日期必选',"text");
                return false;
            }
            var buydatestr=$('#buydateshow').data('value')!=1?'':'<div class="weui-form-preview__item">' +
                '<label class="weui-form-preview__label">购买时间</label>' +
                '<span class="weui-form-preview__value">'+buydate+'</span>' +
                '</div>'
        }
        var str='<div class="weui-form-preview">' +
            '<div class="weui-form-preview__bd">' +
            '<div class="weui-form-preview__item">' +
            '<label class="weui-form-preview__label">产品分类</label>' +
            '<span class="weui-form-preview__value">'+productclassone+'</span>' +
            '</div>' +
            '<div class="weui-form-preview__item">' +
            '<label class="weui-form-preview__label">购买套餐</label>' +
            '<span class="weui-form-preview__value">'+productclasstwo+'</span>' +
            '</div>' +
            '<div class="weui-form-preview__item">' +
            '<label class="weui-form-preview__label">合同金额</label>' +
            '<span class="weui-form-preview__value">￥'+servicetotal+'</span>' +
            '</div>' +
            '<div class="weui-form-preview__item">' +
            '<label class="weui-form-preview__label">购买年限</label>' +
            '<span class="weui-form-preview__value">'+buyyear+'年</span>' +
            '</div>' +
            '<div class="weui-form-preview__item">' +
            '<label class="weui-form-preview__label">购买合同</label>' +
            '<span class="weui-form-preview__value">'+servicecontractsid_display+'</span>' +
            '</div>' +
            '<div class="weui-form-preview__item">' +
            '<label class="weui-form-preview__label">客户名称</label>' +
            '<span class="weui-form-preview__value">'+accountid_display+'</span>' +
            '</div>' +
            '<div class="weui-form-preview__item">' +
            '<label class="weui-form-preview__label">老客户名称</label>' +
            '<span class="weui-form-preview__value">'+olaccountid_display+'</span>' +
            '</div>' +
            '<div class="weui-form-preview__item">' +
            '<label class="weui-form-preview__label">客户手机</label>' +
            '<span class="weui-form-preview__value">'+mobile+'</span>' +
            '</div>' +
            '<div class="weui-form-preview__item">' +
            '<label class="weui-form-preview__label">代理商标识</label>' +
            '<span class="weui-form-preview__value">'+agents+'</span>' +
            '</div>' +buydatestr+
            '</div>' +
            '</div>';
        var $stru=''
        var productid=[];
        var categoryid=[];
        var number=[];
        var id=[];
        var price=[];
        var renewprice=[];
        var unit=[];
        var specificationstitle=[];
        var producttitle=[];
        var tyunusercode=$('#tyunusercode').val();
        var tyunusercodetext=$('#tyunusercode').find("option:checked").text();
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
        });
        var params={"servicecontractsid":servicecontractsid,
            "servicecontractsid_display":servicecontractsid_display,
            "accountid":accountid,
            "accountid_display":accountid_display,
            "mobile":mobile,
            "mobilevcode":mobilevcode,
            "classtype":classtype,
            "type":1,
            "productclassonevalues":productclassonevalues,
            "productclasstwovalues":productclasstwovalues,
            "buyyear":buyyear,
            "buydate":buydate,
            "agents":agents,
            "servicetotal":servicetotal,
            "tyunusercodetext":tyunusercodetext,
            "tyunusercode":tyunusercode,
            "oldcustomerid":oldaccountid,
            "oldcustomername":oldaccountid_display
        };
        params['producttitle']=producttitle;
        params['productid']=productid;
        params['categoryid']=categoryid;
        params['number']=number;
        params['id']=id;
        params['price']=price;
        params['renewprice']=renewprice;
        params['unit']=unit;
        params['specificationstitle']=specificationstitle;
        $.confirm(str, "请核对订单信息", function() {
            $.ajax(
                {
                    url: '/index.php?module=TyunWebBuyService&action=doOrder',
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
                                location.href="/index.php?module=TyunWebBuyService&action=index";
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
            calcprice();
        });
    });
    $('#container').on("click",".weui-count__sub",function(event){
        var id=$(this).data('id');
        var countnumber=$('.weui-count__current_number'+id).val();
        if(countnumber>1){
            countnumber--;
            $('.weui-count__current_number'+id).val(countnumber);
            $('.select3farther'+id).attr('data-number',countnumber);
            calcprice();
        }else{
            $.toast('受不了，不能再减少了哦',"text");
        }

    });

    /**
     * 单品修改计算价格
     * */
    function calcprice(){
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
        price=price>0?price:0;


        //var sumallprice=sumprice*sumpriceyear+sumrenewprice*sumrenewpriceyear;
        var sumallprice=FloatAdd(FloatMul(sumprice,sumpriceyear),FloatMul(sumrenewprice,sumrenewpriceyear));
        $('.pricelast').text('￥'+parseFloat(sumallprice).toFixed(2));
        //sumallprice+=parseInt(price);
        sumallprice=FloatAdd(sumallprice,price);
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
            var price=$('.select3farther'+id).data('price');
            var renewprice=$('.select3farther'+id).data('renewprice');
            if(price>0 || renewprice>0){
                var buyyear=$('#buyyear').attr('data-values');
                var sumprice=FloatMul(price,parseInt(countnumber));
                buyyear=buyyear>1?FloatSub(buyyear,1):0;
                var sumrenewprice=FloatMul(FloatMul(renewprice,parseInt(countnumber)),buyyear);
                $('.pricesum'+id).html('购:￥'+parseFloat(sumprice).toFixed(2)+'+&nbsp;续:￥'+parseFloat(sumrenewprice).toFixed(2)+'=￥'+parseFloat(FloatAdd(sumprice,sumrenewprice)).toFixed(2));
            }
            var sumprice=0;
            var sumrenewprice=0;
            $.each($('.select3check'),function(key,value){
                sumprice=FloatAdd(sumprice,FloatMul(parseInt($(value).data('number')),$(value).data('price')));
                sumrenewprice=FloatAdd(sumrenewprice,FloatMul(parseInt($(value).data('number')),$(value).data('renewprice')));
            });
            $('#otherproductrenewsum').val(sumrenewprice);
            $('#otherproductsum').val(sumprice);
            calcprice();
            return false;
        // }else{
        //     $.toast('哎哟，不能再添加了哦',"text");
        // }

    });
    $('#container').on("click",'#otherproduct',function(){
        if(!checkProductValue(2)){
            $.toast('请先完善信息！',"text");
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
            if(servicetotal>0){
                $('#submitid').attr('data-value',2);
                $('#submitfrom').css('backgroundColor','#4994F2');
                return true;
            }else{
                $('#submitid').attr('data-value',1);
                $('#submitfrom').css('backgroundColor','#999999');
                return false;
            }
            return false;

        }
        return true;
    }
    changeproductclasstwo=function (datas){
        var productclassonevalues=$("#productclassone").data('values');
        var items=[];
        items.push({title:"购买单品(只购买单品,不购买套餐)",
            value: 'nobuypack'});
        $.each(datas,function(key,value){
            currentProduct['PackageID'+productclassonevalues+value.Package.ID]=value.Package;
            items.push({title: value.Package.Title+'-'+value.Package.Price,
                value: value.Package.ID});
        });
        $("#productclasstwo").select("update",{
                title: "请选择",
                items: items,
                input:'选择套餐',
                onClose: function(d) {
                    var buyyearValues=$("#buyyear").data('values');
                    var price=0;
                    if(buyyearValues>0 && JSON.stringify(currentProduct)!='{}' && d.data.values!='nobuypack'){
                        var tempdata=currentProduct['PackageID'+productclassonevalues+d.data.values];
                        //price=tempdata.Price+(tempdata.RenewPrice*(buyyearValues-1)*1);
                        var renewyearvalue=buyyearValues-1;
                        price=FloatAdd(tempdata.Price,FloatMul(tempdata.RenewPrice,renewyearvalue));
                    }
                    $('#Price').val(price);
                    $('#Priceshow').val('￥'+parseFloat(price).toFixed(2));
                    calcpackagePrice();
                    checkProductValue(1);
                }
            }
        );
    }
    window.otherproductlistLoad=function(){
        var selectproducttitle=window.selectproducttitle;
        $('#selectproducttitle').html((typeof selectproducttitle=='string')?((selectproducttitle!='')?selectproducttitle:'&nbsp;'):'&nbsp;');
        $('#agents').val($('#instanceagents').data('value'));
        $('.header-back').click(function(){
            $('.close-select').trigger('click');
            //pageManager.back();
        });
        $.ajax(
            {
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
                        var datas=data.data
                        var productclassoneitems=[];
                        $.each(datas,function(key,value){
                            if(value.IsPackage){
                                var temp={title: value.Title,value: value.ID};
                                productclassoneitems.push(temp);
                            }
                        });
                        $("#productclassone").select({
                            title: "请选择",
                            items: productclassoneitems,
                            onChange: function(d) {
                                return false;
                            },
                            onClose: function(d) {
                                var datavalues=d.data.values;
                                $('#Priceshow').val(0);
                                $('#otherproductsum').val(0);
                                $('#otherproductrenewsum').val(0);
                                $('#totalmarketpriceshow').val(0);
                                $('.otherproductlist').empty();
                                $.ajax(
                                    {
                                        url: '/index.php?module=TyunWebBuyService&action=getproduct',
                                        type: 'POST',
                                        dataType: 'json',
                                        data:{
                                            "categoryID":datavalues
                                        },
                                        beforeSend:function(){
                                            $.showLoading();
                                        },
                                        success: function (data) {
                                            $.hideLoading();
                                            if(data.success && data.data.length>0){
                                                var datas=data.data
                                                changeproductclasstwo(datas);
                                            }else{
                                                $("#productclasstwo").select("update",{
                                                        title: "请选择",
                                                        items: [{
                                                            title: "",
                                                            value: "",
                                                        }],
                                                        input:'选择套餐'
                                                    }
                                                );
                                            }
                                        }
                                    }
                                );
                                checkProductValue(1);
                            }
                        });
                    }
                }
            }
        );
        $("#productclasstwo").select({
            title: "请选择",
            items: [
                {
                    title: "",
                    value: "",
                }
            ],
            beforeOpen:function(){
                return false;
            }
        });
        $("#buyyear").select({
            title: "请选择",
            items: [
                {
                    title: "1年",
                    value: "1",
                },
                {
                    title: "2年",
                    value: "2",
                },
                {
                    title: "3年",
                    value: "3",
                },
                {
                    title: "4年",
                    value: "4",
                },
                {
                    title: "5年",
                    value: "5",
                },
                {
                    title: "6年",
                    value: "6",
                },
                {
                    title: "7年",
                    value: "7",
                },
                {
                    title: "8年",
                    value: "8",
                },
                {
                    title: "9年",
                    value: "9",
                },
                {
                    title: "10年",
                    value: "10",
                }
            ],
            onClose: function(d) {
                var years=d.data.values;
                var price=0;
                var productclasstwo=$("#productclasstwo").data('values')
                if(years>0 && JSON.stringify(currentProduct)!='{}' && productclasstwo!='nobuypack' && productclasstwo!=undefined){
                    var productclassonevalues=$("#productclassone").data('values');
                    var productclasstwovalues=$("#productclasstwo").data('values');
                    var tempData=currentProduct['PackageID'+productclassonevalues+productclasstwovalues];
                    var renewyears=years-1;
                    price=FloatAdd(tempData.Price,(FloatMul(tempData.RenewPrice,renewyears)));
                }
                $('#Price').val(price);
                $('#Priceshow').val('￥'+parseFloat(price).toFixed(2));
                calcpackagePrice();
                checkProductValue(1);
            }
        });
    }
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
                        $('#tyunusercode').append('<option class="tyuncode_option" value="'+data.accountid+'" '+selected+'>'+data.loginName+'</option>');
                        getAllCategory();
                        getUserRenewProductInfo();
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
        if(!mobilevcode){
            $.toast('请先输入验证码','forbidden');
            return;
        }

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
                    }
                    $.each(data.data,function(key,value){
                        var selected='';
                        if(key==0){
                            selected='selected';
                        }
                        $("#tyunusercode").append('<option value="'+value.id+'" '+selected+'>'+value.loginName+'</option>');
                    });
                    $('#tyunusercodeonoff').val(2);
                    $('#tyunusercode').trigger('click');
                    $('#nextstep').css({"background-color":'#0B92D9'});
                    pageManager.go('checkaccount');
                }else{
                    $.toast(data.msg,'text');
                }
            }
        });
    });


});
