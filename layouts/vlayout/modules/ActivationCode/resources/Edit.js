Vtiger_Edit_Js("ActivationCode_Edit_Js",{ },{

    registerRecordPreSaveEvent : function(form) {
        var thisInstance = this;
        if(typeof form == 'undefined') {
            form = this.getForm();
        }
        form.on(Vtiger_Edit_Js.recordPreSave, function(e, data) {
            var f = thisInstance.check();

            var classtype = $("select[name='classtype']").val();
            var usercode = $("input[name='usercode']").val()
            if(classtype != 'buy'){
                var usercode = $("input[name='usercode']").val()
                if(usercode == ""){
                    var params = {text : 'T云账号不能为空,请确认', title : '错误提示'};
                    Vtiger_Helper_Js.showPnotify(params);
                    return false;
                }
            }

            var curDate = new Date();
            var adddate = $("input[name='adddate']").val()
            var dt_adddate = new Date(adddate.replace(/\-/g, "\/"));
            if(dt_adddate > curDate){
                var params = {text : '下单时间必须小于等于系统时间', title : '错误提示'};
                Vtiger_Helper_Js.showPnotify(params);
                return false;
            }

            var blError = false;
            var arr_serviceId = [];
            var is_tyun_seo = false;
            $('.cls_tbl_buyservice tbody tr').each(function () {
                var serviceId = $(this).find("td").eq(0).find("select").val();
                // T云智能SEO资源
                if(serviceId=='1e9c758a-2d65-44f1-98af-ff741a39601a'){
                    is_tyun_seo = true;
                }
                if($.inArray(serviceId,arr_serviceId)>-1){
                    var  params = {text : '另购服务中存在服务名称重复,请确认', title : '错误提示'};
                    Vtiger_Helper_Js.showPnotify(params);
                    blError = true;
                    return false;
                }
                arr_serviceId.push(serviceId);
            })
            if(blError) return false;
            //智能购买 V3 V3P
            //领取激活码时间大于 2017年11月4日
            if(is_tyun_seo){
                var productid = "";
                productid = $("select[name='productid']").find("option:selected").val();
                if(productid != 'fb016866-4296-11e6-ad98-00155d069461' && productid != 'eb472d25-f1b1-11e6-a335-5254003c6d38'){
                    var  params = {text : '选择了【智能SEO】时,只能选择V3或V3P版本,请确认', title : '错误提示'};
                    Vtiger_Helper_Js.showPnotify(params);
                    return false;
                }
                if(classtype != 'buy'){
                    //领取激活码时间大于 2017年11月4日
                    var receivetimeflag = $("input[name='receivetimeflag']").val();
                    if(receivetimeflag == '0'){
                        Tips.alert({
                            content: '选择了【智能SEO】时,领取激活码时间必须要大于2017年11月4日'
                        });
                        return false;
                    }
                }
            }

            /*if (f > 0) {
                var message = '';
                if (f == 1) {
                    message = '激活码位数必须是36位。';
                } else if(f == 2) {
                    message = '激活码含有非法字符，请检查后重新录入';
                } else if (f == 3) {
                    message = '激活码中划线必须是4位';
                }
                var  params = {text : app.vtranslate(message),title : app.vtranslate('JS_DUPLICTAE_CREATION_CONFIRMATION')}
                Vtiger_Helper_Js.showPnotify(params);
                e.preventDefault();
            }*/

            var contractNo = $('input[name=contractname]').val();
            var customerName = $('input[name=customername]').val();
            var postData = {
                    "module": 'ActivationCode',
                    "action": "BasicAjax",
                    "mode": "checkBuyInput",
                    'record':$("input[name='record']").val(),
                    'productid':$("select[name='productid']").val(),
                    'productlife':$("select[name='productlife']").val(),
                    'mobile':$("input[name='mobile']").val(),
                    'buyid':$("input[name='buyid']").val(),
                    'contractname' : contractNo,
                    'usercode':$("input[name='usercode']").val(),
                    'oldusercode':$("input[name='oldusercode']").val(),
                    'classtype':classtype,
                    'is_tyun_seo':is_tyun_seo,
                    'customername' : customerName
                };
            if (!thisInstance.flag) {
                AppConnector.request(postData).then(
                    function(data){
                        if(data.success) {
                            var result = data['result'];
                            if (!result.success) {
                                var  params = {text : result.message, title : '错误提示'};
                                Vtiger_Helper_Js.showPnotify(params);
                            } else {
                                thisInstance.flag = true;
                                form.submit();
                            }
                        } else {
                            return false;
                        }
                    },
                    function(error,err){

                    }
                );
                e.preventDefault();
            } 
        });
    },
    
    check: function () {
        var uname = $('input[name="activecode"]').val();
        if (uname.length != 36) {
            return 1;
        }
        if(! /^[a-z0-9-]+$/.test(uname)){
            return 2;
        }
        var t = uname.split('-');
        if (t.length != 5) {
            return 3;
        }
        return 0;
    },

    addBuyService:function() {
        var instancethis = this;
        $('.cls_tbl_buyservice').on("click", '#addBuyService', function (e) {
            //查找行号
            var nowdnum=$('.cls_tbl_buyservice tbody tr').last().data('num');
            if(nowdnum){
                nowdnum=nowdnum+1;
            }else{
                nowdnum = 1;
            }
            var tr = '<tr data-num="'+nowdnum+'"><td><input type="hidden" name="buyindex[]" value="'+nowdnum+'"/><select id="select_buy_service'+nowdnum+'" data-num="'+nowdnum+'" class="chzn-select referenceModulesList streched select_buy_service" name="ServiceID['+nowdnum+']" style="width:320px;"><optgroup>';
            var module = app.getModuleName();
            var postData = {
                "module": module,
                "action": "BasicAjax",
                'mode': 'getTyunServiceItem'
            }
            AppConnector.request(postData).then(
                function(data){
                    if(data.success) {
                        var list = data.result;
                        //var display_unit = "";
                        var tyun_count = 0;
                        if(list.length >0){
                            for(var i=0;i<list.length;i++){
                                if(i == 0){
                                    tr += '<option data-unit="' + list[i]['Unit'] + '" data-multiple="' + list[i]['Multiple'] + '" value="' + list[i]['ServiceID'] + '" selected>' + list[i]['ServiceName'] + '</option>';
                                    //display_unit = list[i]['Unit'];
                                    tyun_count = list[i]['Multiple'];
                                }else {
                                    tr += '<option data-unit="' + list[i]['Unit'] + '" data-multiple="' + list[i]['Multiple'] + '" value="' + list[i]['ServiceID'] + '">' + list[i]['ServiceName'] + '</option>';
                                }
                            }
                        }

                        tr += '</optgroup></select><input type="hidden" id="tyunBuyCount'+nowdnum+'" name="TyunBuyCount['+nowdnum+']" value="'+tyun_count+'" /></td>';
                        //tr += '<td><input type="number" class="input-large" name="BuyCount['+nowdnum+']" value="" maxlength="5" maxlength="2" step="1"><span style="color: red;padding-left: 10px;font-weight: bold;" id="display_unit'+nowdnum+'">'+display_unit+'</span></td>';
                        tr += '<td><select id="buycount'+nowdnum+'" data-num="'+nowdnum+'" class="chzn-select referenceModulesList streched select_buy_count" name="BuyCount['+nowdnum+']" style="width:200px;"></select></td>';
                        tr += '<td><i class="icon-trash deleteBuyService" title="删除另购服务" style="cursor: pointer" ></i></td></tr>';
                        $('.cls_tbl_buyservice tbody').append(tr);

                        instancethis.select_buy_service();
                        $('#select_buy_service'+nowdnum).change();
                        //$('.cls_tbl_buyservice  tbody tr').eq($('.cls_tbl_buyservice  tbody tr').length-1).find('select').trigger('liszt:updated');
                    }
                },
                function(error,err){

                }
            );

        })
    },
    deleteBuyService:function() {
        var instancethis = this;
        $('.cls_tbl_buyservice').on("click", '.deleteBuyService', function (e) {
            var that = $(this);
            var msg={'message':"确定要删除该另购服务？"};
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                that.closest('tr').empty().remove();
            })
        })
    },
    init: function() {
        $('input[name="activecode"]').css({width: '300px'});
        $('input[name=contractid_display]').next().remove();

        //$(".clearReferenceSelection").empty().remove();
        var classtype = $("select[name='classtype']").val();

        if(classtype == 'buy'){
            $("input[name='usercode']").attr("readonly","true");
        }else{
            $("input[name='customername']").attr("readonly","true");
            $("input[name='mobile']").attr("readonly","true");
            $("input[name='agents']").attr("readonly","true");
        }
        $("input[name='activecode']").attr("readonly","true");
    },
    select_buy_service:function () {
        var instancethis = this;
        $('.select_buy_service').change(function(){
            var s_unit = $(this).find("option:selected").attr("data-unit");
            var s_multiple = $(this).find("option:selected").attr("data-multiple");
            var num = $(this).attr("data-num");
            $("#buycount"+ num).empty();

            //购买数量
            var start_num = parseInt(s_multiple);
            var step_num = parseInt(s_multiple);
            var html_count = "";
            for(var j=1;j<11;j++){
                html_count += '<option value="'+j+'" tyun-value="'+start_num+'">'+j + s_unit +'</option>';
                start_num += step_num;
            }
            $("#buycount"+ num).append(html_count);
            $("#buycount"+ num).trigger('liszt:updated');

            $("#tyunBuyCount"+ num).val($("#buycount"+ num).find("option:selected").attr("tyun-value"));
            instancethis.select_buy_count();
        })
    },
    select_buy_count:function () {
        $('.select_buy_count').change(function(){
            var num = $(this).attr("data-num");
            $("#tyunBuyCount"+ num).val($(this).find("option:selected").attr("tyun-value"));
        })
    },
	registerBasicEvents : function(container) {
        this._super(container);
        this.registerRecordPreSaveEvent(container);
        this.init();
        this.addBuyService();
        this.deleteBuyService();
        this.select_buy_service();
        this.select_buy_count();
	}
});