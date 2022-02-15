/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("SalesDaily_Edit_Js",{},{
	ckEditorInstance:'',
	ckEInstance:'',
	rowSequenceHolder : false,

	loadWidgetNote : function(id){

	},

	ControlInit:function(){
	    app.registerEventForDatePickerFields=function(){};


        var endtime = app.addOneHour();
        if(this.getUrlArgStr()){
            enddate=new Date();
            enddate.setMinutes(enddate.getMinutes()-(60*24*3));
            $('#SalesDaily_editView_fieldName_dailydatetime').datepicker({
                format: "yyyy-mm-dd",
                language:  'zh-CN',
                autoclose: true,
                todayHighlight:true,
                pickerPosition: "bottom-left",
                startDate:enddate,
                endDate:new Date()
            });
        }
        this.checkSignDempart();
        this.refreshvisitingorder();
        this.addvisitingorder();

	},
	getUrlArgStr:function(){
        var q=location.search.substr(1);
        var qs=q.split('&');
        var argStr='';
        if(qs){
            for(var i=0;i<qs.length;i++){
                argStr+=qs[i].substring(0,qs[i].indexOf('='))+'='+qs[i].substring(qs[i].indexOf('=')+1)+'&';
            }
        }
        return argStr.indexOf('record')>-1?false:true;
    },
    getrefreshvisitingorder:function(){
        var record = $('input[name="record"]').val();
        var params={
            'module' : 'SalesDaily',
            'action' :	'BasicAjax',
            'record' :	$('input[name="record"]').val(),
            'dailydate':$('input[name="dailydatetime"]').val(),
            'mode'   : 	'getDayFoutNotv'
        };
        var progressIndicatorElement = jQuery.progressIndicator({
            'message' : '正在处理,请耐心等待哟',
            'position' : 'html',
            'blockInfo' : {'enabled' : true}
        });
        $('#lineItemNotv tbody').empty();
        AppConnector.request(params).then(
            function(data){
                progressIndicatorElement.progressIndicator({
                    'mode' : 'hide'
                });
                if(data.success ==  true){
                    if(data.result.fournotv.status){
                        if(data.result.fournotv.num>0){
                            var dateresult=data.result.fournotv.result;
                            var str=''
                            $.each(dateresult,function(key,value){
                                str+='<tr><td><input type="hidden" name="fnvaccount['+value.accountid+']" value="'+value.accountid+'"/><input type="hidden" name="fnvvisitingorder['+value.accountid+']" value="'+value.visitingorderid+'"/>'+value.accountname+'</td>'+
                                    '<td><input type="hidden" name="fnvaccountsmownerid['+value.accountid+']" value="'+value.smownerid+'"/><input type="hidden" name="fnvleadsource['+value.accountid+']" value="'+value.leadsourceen+'"/>'+value.leadsource+'</td>'+
                                    '<td><input type="hidden" name="fnvcontacts['+value.accountid+']" value="'+value.contacts+'"/>'+value.contacts+'</td>'+
                                    '<td><input type="hidden" name="fnvmobile['+value.accountid+']" value="'+value.mobile+'"/>'+value.mobile+'</td>'+
                                    '<td><input type="hidden" name="fnvaccountname['+value.accountid+']" value="'+value.accountname+'"/><input type="hidden" name="fnvtitle['+value.accountid+']" value="'+value.title+'"/>'+value.title+'</td>'+
                                    '<td><input type="hidden" name="fnvstartdate['+value.accountid+']" value="'+value.startdate+'"/>'+value.startdate+'</td>'+
                                    '<td><input type="hidden" name="fnvmangereturnendtime['+value.accountid+']" value="'+value.mangereturnendtime+'"/>'+value.mangereturnendtime+'</td>'+
                                    '<td><input type="hidden" name="fnvcommentcontent['+value.accountid+']" value="'+value.commentcontent+'"/>'+value.commentcontent+'</td>'+
                                    '<td></td><td></td><td></td></tr>';
                            });
                            $('#lineItemNotv tbody').append(str);
                        }else{
                           /* var params = {text :"",
                                title : "没有数据"}
                            Vtiger_Helper_Js.showPnotify(params);*/
                        }

                    }else{
                        /*var params = {text : data.result.msg,
                            title : "更新出错"}
                        Vtiger_Helper_Js.showPnotify(params);*/
                    }

                }
            },
            function(error){
                console.log(error+'+');
            }
        );
    },
	refreshvisitingorder:function(){
        var installthis=this;
	    $('#refreshvisitingorder').click(function(){
            installthis.getrefreshvisitingorder();
        });
        $('#refreshcandeal').click(function(){
            var params={
                'module' : 'SalesDaily',
                'action' :	'BasicAjax',
                'record' :	$('input[name="record"]').val(),
                'dailydate':$('input[name="dailydatetime"]').val(),
                'mode'   : 	'getAppenddailycandeal'
            };
            var progressIndicatorElement = jQuery.progressIndicator({
                'message' : '正在处理,请耐心等待哟',
                'position' : 'html',
                'blockInfo' : {'enabled' : true}
            });
             $('#lineItemCanDeal tbody').empty();
            AppConnector.request(params).then(
                function(data){
                    progressIndicatorElement.progressIndicator({
                        'mode' : 'hide'
                    });
                    if(data.success ==  true){

                        if(data.result.num>0){
                            var dateresult=data.result.result;
                            var str=''
                            $.each(dateresult,function(key,value){
                               str+='<tr class="candealaccount'+value.accountid+'">'+
                                 '<td nowrap><input type="hidden" name="prevcandealrecordid['+value.salesdailycandealid+']" value="'+value.salesdailycandealid+'"/>'+value.accountname+'</td>'+
                                 '<td nowrap><input type="hidden" name="prevcandealdeleted['+value.salesdailycandealid+']" value="0" id="candealdeleted'+value.salesdailycandealid+'"/>'+value.contactname+'</td>'+
                                 '<td nowrap>'+value.mobile+'</td>'+
                                 '<td nowrap>'+value.title+'</td>'+
                                 '<td nowrap>'+value.accountcontent+'</td>'+
                                 '<td nowrap>'+value.productname+'</td>'+
                                 '<td nowrap>'+value.quote+'</td>'+
                                 '<td nowrap>'+value.firstpayment+'</td>'+
                                 '<td nowrap><label style="display:inline-block;"><input type="radio" data-id="'+value.salesdailycandealid+'" class="ncandealissigncontract" name="prevcandealissigncontract['+value.salesdailycandealid+']" value="1">是</label><label style="display:inline-block;"><input type="radio" data-id="'+value.salesdailycandealid+'" name="prevcandealissigncontract['+value.salesdailycandealid+']" value="0" checked>否</label></td>'+
                                 '<td nowrap><a class="displayRecordButton" data-id="candealdeleted'+value.salesdailycandealid+'" style="cursor:pointer;"><i title="删除" class="icon-trash alignMiddle"></i></a></td>';
                             '</tr>';
                            });
                            $('#lineItemCanDeal tbody').append(str);
                        }
                    }
                },
                function(error){
                    console.log(error+'+');
                }
            );
        });
        $('#EditView').on('change','#SalesDaily_editView_fieldName_dailydatetime',function(){
            if($(this).val()!=changedate){
                $('#refreshcandeal').show();
            }else{
                $('#refreshcandeal').hide();
            }

        });
	},
	addvisitingorder:function(){
	    $('#addvisitingorder').click(function(){
	        window.location.href='/index.php?module=VisitingOrder&view=Edit';
	    });
	},
	checkSignDempart:function(){
        var signedtext=$('select[name="smownerid"] option:selected').text();
        var start=signedtext.indexOf('[');
        var end=signedtext.indexOf(']');
        signedtext=signedtext.substr(start+1,end);
        signedtext=signedtext.replace(']','');
        if(signedtext==''){
         //$('select[name="signdempart[]"]')[0].selectedIndex = 0;
        }else{
          $('select[name="departmentid[]"] option').each(function(){
              if($(this).text()==signedtext){
                  $(this).attr('selected','selected');
              }
          });
        }
        $('select[name="departmentid[]"]').removeClass('chzn-select,chzn-done');
        $('select[name="departmentid[]"]').trigger('liszt:updated');
        $('select[name="departmentid[]"]').siblings('div').children('div').html('');
        $('select[name="smownerid"]').siblings('div').children('div').html('');

    },
    addCanDeal:function(){
        $('#addCanDeal').on('click',function(){
            var accountclass=accountinfo.replace('###',' addcandealaccountchange');
            var candealstr='<tr class="addcandealing"><td>'+accountclass+'</td><td class="contractname">姓名</td><td class="contractmobile">手机</td><td class="contracttitle">职位</td><td class="accountcontent">客户情况</td><td class="candealproductinfo">产品</td><td class="candealquote">报价</td><td class="candealfirstpayment">首付款</td><td>否</td><td><a class="deleteRecordButton" style="cursor:pointer;"><i title="删除" class="icon-trash alignMiddle"></i></a></td></tr>';
            $('#lineItemCanDeal tbody').append(candealstr);
            //$('#lineItemCanDeal tbody .chzn-select').addClass('addcandeal');
            $('#lineItemCanDeal tbody .chzn-select').chosen();
        });
    },
    CanDealaccountchange:function(){
        $('#EditView').on('change','.addcandealaccountchange',function(){
            var _this=this;
            var accountid=$(_this).val();
            if(accountid==''){
                $(_this).closest('tr').children('.contractname').text('联系人');
                $(_this).closest('tr').children('.contractmobile').html('手机');
                $(_this).closest('tr').children('.contracttitle').text('职位');
                $(_this).closest('tr').children('.accountcontent').html('客户情况');
                $(_this).closest('tr').children('.candealquote').html('报价');
                $(_this).closest('tr').children('.candealproductinfo').html('产品');
                $(_this).closest('tr').children('.candealfirstpayment').html('首付款');
                return false;
            }
            var dataaccountid=$(_this).closest('tr').attr('data-accountid');
            $(_this).closest('tr').removeClass('candealaccount'+dataaccountid);
            $(_this).closest('tr').attr('data-accountid',accountid);
            //$(_this).before('<input type="hidden" name="candealaccount['+accountid+']" value="'+accountid+'" class="candealdeleted'+accountid+'">');
            //return ;
            if($('#lineItemCanDeal').find('tr').hasClass('candealaccount'+accountid)){
                Vtiger_Helper_Js.showPnotify({text : '客户不允许重复添加',title : "客户重复"});
                $(_this).closest('tr').children('.contractname').text('联系人');
                $(_this).closest('tr').children('.contractmobile').html('手机');
                $(_this).closest('tr').children('.contracttitle').text('职位');
                $(_this).closest('tr').children('.accountcontent').html('客户情况');
                $(_this).closest('tr').children('.candealquote').html('报价');
                $(_this).closest('tr').children('.candealproductinfo').html('产品');
                $(_this).closest('tr').children('.candealfirstpayment').html('首付款');
                return false;
            }
            var progressIndicatorElement = jQuery.progressIndicator({
                'message' : '正在处理,请耐心等待哟',
                'position' : 'html',
                'blockInfo' : {'enabled' : true}
            });

            var params={
                'module' : 'SalesDaily',
                'action' : 'BasicAjax',
                'record' :	accountid,
                'mode'   : 	'getCanDealContacts'
            };
            AppConnector.request(params).then(
                function(data){
                    progressIndicatorElement.progressIndicator({
                        'mode' : 'hide'
                    });
                    if(data.success ==true){
                        if(data.result.status){
                            if(data.result.num>0){
                                var dateresult=data.result.result;
                                var str=''
                                $.each(dateresult,function(key,value){
                                    $(_this).closest('tr').addClass("candealaccount"+accountid);
                                    $(_this).closest('tr').children('.contractname').text(value.linkname);
                                    $(_this).closest('tr').children('.contractmobile').html('<input type="hidden" name="candealaccount['+accountid+']" value="'+accountid+'"><input type="hidden" name="candealissigncontract['+accountid+']" value="'+accountid+'"> <input type="hidden" name="candeallinkname['+accountid+']" value="'+value.linkname+'"><input type="hidden" name="candealmobile['+accountid+']" value="'+value.mobile+'"><input type="hidden" name="candealtitle['+accountid+']" value="'+value.title+'">'+value.mobile);
                                    $(_this).closest('tr').children('.contracttitle').text(value.title);
                                    $(_this).closest('tr').children('.accountcontent').html('<input type="text" name="candealaccountcontent['+accountid+']" style="width:100px;" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />');
                                    $(_this).closest('tr').children('.candealquote').html('<input type="text" class="formatprice" name="candealquote['+accountid+']" style="width:100px;" data-validation-engine="validate[required,min[1],funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />');
                                    $(_this).closest('tr').children('.candealproductinfo').html('<input type="text" name="candealproduct['+accountid+']" style="width:100px;" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />');
                                    $(_this).closest('tr').children('.candealfirstpayment').html('<input type="text" class="formatprice" name="candealfirstpayment['+accountid+']" style="width:100px;" data-validation-engine="validate[required,min[1],funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />');
                                });
                            }
                        }else{
                            var params = {text : data.result.msg,
                            title : "更新出错"}
                            Vtiger_Helper_Js.showPnotify(params);
                        }

                    }
                }
            );

        });
    },
    candeleter:function(){
        var thisInstance=this;
        $('#EditView').on('click','.deleteRecordButton',function(){
            var _this=this;
            var message= '确定要删除吗?';
            Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
                function(e){
                    $(_this).closest('tr').remove();
                },function(e){}
            );
        });
        $('#EditView').on('click','.displayRecordButton',function(){
            var _this=this;
            var message= '确定要删除吗?';
            Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
                function(e){
                    $("#"+$(_this).data("id")).val(1);
                    $(_this).closest('tr').hide();
                },function(e){}
            );
        });
        $('#EditView').on('click','.ncandealissigncontract',function(){
            var _this=this;

            var params = {text : '',title : "已签订合同的客户将不再显示"}
                            Vtiger_Helper_Js.showPnotify(params);
            $(_this).closest('tr').hide();
        });

        $('#EditView').on('keyup','.formatprice',function(){
            thisInstance.formatNumber($(this));
            var arr=$(this).val().split('.');//只有一个小数点
            if(arr.length>2){
                if(arr[1]==''){
                    $(this).val(arr[0]);
                }else{
                    $(this).val(arr[0]+'.'+arr[1]);
                }
            }

        }).on('blur','.formatprice',function(){
            thisInstance.formatNumber($(this));
            var arr=$(this).val().split('.');//只有一个小数点
            if(arr.length>2){
                if(arr[1]==''){
                    $(this).val(arr[0]);
                }else{
                    $(this).val(arr[0]+'.'+arr[1]);
                }
            }
        });
    },
    /**
    *近期可成交的客户
    */
    initAddCanDeal:function(){
        this.addCanDeal();
        this.candeleter();
        this.CanDealaccountchange();
    },
    getaddDayDeal:function(){

        /*var accountclass=accountinfo.replace('###','adddaydealaccountchange');
         var productclass=productinfo.replace('###','adddaydealproductchange');
         var daydealstr='<tr class="addDayDealing"><td>'+accountclass+'</td><td class="daydealproduct">'+productclass+'</td><td class="daydealmarketprice">市场价</td><td class="daydealdealamount">成交金额</td><td class="datdeakallamount" nowrap>是否全款</td><td class="daydealpaymentnature">到款性质</td><td class="daydealfirstpayment">收款</td><td class="daydealvisitingordercount">拜访次数</td><td class="daydealoldcustomers">老客户</td><td class="daydealindustry">行业</td><td class="daydealvisitingobj">拜访对象</td><td class="daydealisvisitor">有陪访</td><td class="daydealwithvisitor">陪访者</td><td class="daydealdiscount">折扣</td><td class="daydealarrivalamount">到账业绩</td><td><a class="deleteRecordButton" style="cursor:pointer;"><i title="删除" class="icon-trash alignMiddle"></i></a></td></tr>';
         $('#lineItemDayDeal tbody').append(daydealstr);
         $('#lineItemDayDeal tbody .chzn-select').chosen();*/
        var progressIndicatorElement = jQuery.progressIndicator({
            'message' : '正在处理,请耐心等待哟',
            'position' : 'html',
            'blockInfo' : {'enabled' : true}
        });
        var datetime=$('#SalesDaily_editView_fieldName_dailydatetime').val();
        var params={
            'module' : 'SalesDaily',
            'action' : 'BasicAjax',
            'datetime':datetime,
            'mode'   : 	'getDayDealContent'
        };
        $('#lineItemDayDeal tbody').html("");
        var thisInstance=this;
        AppConnector.request(params).then(
            function(data){
                progressIndicatorElement.progressIndicator({
                    'mode' : 'hide'
                });
                if(data.success ==  true){
                    if(data.result.status){
                        if(data.result.num>0){
                            var daydealstr='';
                            $.each(data.result.result,function(key,valuedata){
                                var discounst=thisInstance.CalculatedDiscount(valuedata.marketprice,valuedata.total,valuedata.unit_price,valuedata.allcost);
                                var iswithvisitor=(valuedata.visitingorderwithvisitor=='' || valuedata.visitingorderwithvisitor==undefined)?'没有':'有陪访';
                                daydealstr+='<tr><td>'+valuedata.accountname+'</td>\
                                <td><input type="hidden" name="daydealproduct['+valuedata.accountid+']" value="'+valuedata.productid+'">'+valuedata.productname+'</td>\
                                <td class="daydealmarketprice">\
                                    <input type="hidden" name="daydealproductname['+valuedata.accountid+']" value="'+valuedata.productname+'">\
                                    <input type="hidden" name="daydealaccountid['+valuedata.accountid+']" value="'+valuedata.accountid+'">\
                                    <input type="hidden" name="daydealmarketprice['+valuedata.accountid+']" value="'+valuedata.marketprice+'">'+valuedata.marketprice+'</td>\
                                <td class="daydealdealamount">\
                                    <input type="hidden" name="daydealdealamount['+valuedata.accountid+']" value="'+valuedata.total+'">'+valuedata.total+'</td>\
                                <td class="datdeakallamount" nowrap="">\
                                    <label style="display:inline-block;">\
                                    <input type="radio" name="daydealallamount['+valuedata.accountid+']" value="1" checked="">是</label>\
                                    <label style="display:inline-block;">\
                                    <input type="radio" name="daydealallamount['+valuedata.accountid+']" value="0">否</label></td>\
                                <td class="daydealpaymentnature">\
                                    <label style="display:inline-block;">\
                                    <input type="radio" name="daydealpaymentnature['+valuedata.accountid+']" value="firstpaymentnature" checked="">首付款</label>\
                                    <label style="display:inline-block;">\
                                    <input type="radio" name="daydealpaymentnature['+valuedata.accountid+']" value="lastpaymentnature">尾款</label></td>\
                                <td class="daydealfirstpayment">\
                                    <input type="hidden" name="daydealfirstpayment['+valuedata.accountid+']" value="'+valuedata.unit_price+'">'+valuedata.unit_price+'</td>\
                                <td class="daydealvisitingordercount">\
                                    <input type="hidden" name="daydealstepprice['+valuedata.accountid+']" value="'+valuedata.allcost+'">\
                                    <input type="hidden" name="daydealvisitingordernum['+valuedata.accountid+']" value="'+valuedata.visitingordernum+'">'+valuedata.visitingordernum+'</td>\
                                <td class="daydealoldcustomers">\
                                    <input type="hidden" name="daydealoldcustomers['+valuedata.accountid+']" value="'+valuedata.oldcust+'">'+valuedata.oldcustmsg+'</td>\
                                <td class="daydealindustry">\
                                    <input type="hidden" name="daydealindustry['+valuedata.accountid+']" value="'+valuedata.industry+'">'+valuedata.industry+'</td>\
                                <td class="daydealvisitingobj">\
                                    <input type="hidden" name="daydealvisitingobj['+valuedata.accountid+']" value="'+valuedata.visitingordercontacts+'">'+valuedata.visitingordercontacts+'</td>\
                                <td class="daydealisvisitor">'+iswithvisitor+'</td>\
                                <td class="daydealwithvisitor">\
                                    <input type="hidden" name="daydealwithvisitor['+valuedata.accountid+']" value="'+valuedata.visitingorderwithvisitor+'">'+valuedata.visitingorderwithvisitor+'</td>\
                                <td class="daydealdiscount">'+discounst.msg+'</td>\
                                <td class="daydealarrivalamount">\
                                    <input type="hidden" name="daydealarrivalamount['+valuedata.accountid+']" value="'+discounst.value+'"><span>'+discounst.value+'</span></td>\
                                <td><a class="deleteRecordButtonsa" style="cursor:pointer;"></a></td></tr>';
                            });
                            $('#lineItemDayDeal tbody').append(daydealstr);
                        }
                    }else{
                        /*var params = {text : data.result.msg,
                            title : "更新出错"}
                        Vtiger_Helper_Js.showPnotify(params);*/
                    }

                }
            }
        );
    },
    addDayDeal:function(){
        var thisInstance=this;
        $('#addDayDeal').on('click',function(){
            thisInstance.getaddDayDeal();
        });
    },
    dayDealaccountchange:function(){
        $('#EditView').on('change','.adddaydealaccountchange',function(){
            var _this=this;
            var accountid=$(_this).val();
            if(accountid==''){
                $(_this).closest('tr').children('.daydealmarketprice').html('');
                $(_this).closest('tr').children('.daydealdealamount').html('');
                $(_this).closest('tr').children('.datdeakallamount').html('');
                $(_this).closest('tr').children('.daydealfirstpayment').html('');
                $(_this).closest('tr').children('.daydealvisitingordercount').html('');
                $(_this).closest('tr').children('.daydealproduct').children('.adddaydealproductchange').removeAttr('name');
                $(_this).removeAttr('name');
                $(_this).closest('tr').children('.daydealoldcustomers').html('');
                $(_this).closest('tr').children('.daydealdiscount').text('');
                $(_this).closest('tr').children('.daydealindustry').html('');
                $(_this).closest('tr').children('.daydealvisitingobj').html('');
                $(_this).closest('tr').children('.daydealwithvisitor').html('');
                $(_this).closest('tr').children('.daydealisvisitor').text("");
                $(_this).closest('tr').children('.daydealarrivalamount').html('');

                return false;
            }

            if($('#lineItemDayDeal tbody tr').hasClass('addDayDealing'+accountid)){
                var params = {text : '',title : "客户已经存在"}
                Vtiger_Helper_Js.showPnotify(params);
                return false;
            }
            $(_this).closest('tr').attr('data-accountid',accountid);
            $(_this).closest('tr').addClass('addDayDealing'+accountid);
            var progressIndicatorElement = jQuery.progressIndicator({
                'message' : '正在处理,请耐心等待哟',
                'position' : 'html',
                'blockInfo' : {'enabled' : true}
            });

            var params={
                'module' : 'SalesDaily',
                'action' : 'BasicAjax',
                'record' :	accountid,
                'mode'   : 	'getDayDealContent'
            };
            AppConnector.request(params).then(
                function(data){
                    progressIndicatorElement.progressIndicator({
                        'mode' : 'hide'
                    });
                    if(data.success ==  true){
                        if(data.result.status){
                            if(data.result.num>0){
                                var dateresult=data.result.result;
                                var iswithvisitor=(dateresult.visitingorderwithvisitor=='' || dateresult.visitingorderwithvisitor==undefined)?'没有':'有陪访';
                                $(_this).closest('tr').children('.daydealmarketprice').html('<input type="hidden" class="daydealproductname'+accountid+'" name="daydealproductname['+accountid+']" value=""/><input type="hidden" name="daydealaccountid['+accountid+']" value="'+accountid+'"/><input type="text" name="daydealmarketprice['+accountid+']" data-id="'+accountid+'" class="checkdiscount daydealmarketprice'+accountid+'" value="" style="width:80px;"  data-validation-engine="validate[required,min[1],funcCall[Vtiger_Base_Validator_Js.invokeValidation]]">');
                                $(_this).closest('tr').children('.daydealdealamount').html('<input type="text" name="daydealdealamount['+accountid+']" data-id="'+accountid+'" class="checkdiscount daydealdealamount'+accountid+'"  value="" style="width:80px;"  data-validation-engine="validate[required,min[1],funcCall[Vtiger_Base_Validator_Js.invokeValidation]]">');
                                $(_this).closest('tr').children('.daydealpaymentnature').html('<label style="display:inline-block;"><input type="radio" data-id="'+accountid+'" class="daydealpaymentnature'+accountid+'" name="daydealpaymentnature['+accountid+']" value="firstpaymentnature" checked>首付款</label><label style="display:inline-block;"><input type="radio" name="daydealpaymentnature['+accountid+']" value="lastpaymentnature">尾款</label>');
                                $(_this).closest('tr').children('.datdeakallamount').html('<label style="display:inline-block;"><input type="radio" data-id="'+accountid+'" class="daydealallamount'+accountid+'" name="daydealallamount['+accountid+']" value="1" checked>是</label><label style="display:inline-block;"><input type="radio" name="daydealallamount['+accountid+']" value="0">否</label>');
                                $(_this).closest('tr').children('.daydealfirstpayment').html('<input type="text" name="daydealfirstpayment['+accountid+']" data-id="'+accountid+'" class="checkdiscount daydealfirstpayment'+accountid+'" value="" style="width:80px;"  data-validation-engine="validate[required,min[1],funcCall[Vtiger_Base_Validator_Js.invokeValidation]]">');
                                $(_this).closest('tr').children('.daydealvisitingordercount').html('<input type="hidden" name="daydealstepprice['+accountid+']" data-id="'+accountid+'" class="daydealstepprice'+accountid+'"><input type="hidden" name="daydealvisitingordernum['+accountid+']" data-id="'+accountid+'" class="daydealvisitingordernum'+accountid+'" value="'+dateresult.visitingordernum+'">'+dateresult.visitingordernum);
                                $(_this).closest('tr').children('.daydealproduct').children('.adddaydealproductchange').attr('name','daydealproduct['+accountid+']').attr('data-id',accountid);
                                $(_this).closest('tr').children('.daydealoldcustomers').html('<input type="hidden" name="daydealoldcustomers['+accountid+']" data-id="'+accountid+'" class="daydealoldcustomers'+accountid+'" value="'+dateresult.oldcustmsg+'">'+dateresult.oldcustmsg);
                                $(_this).closest('tr').children('.daydealindustry').html('<input type="hidden" name="daydealindustry['+accountid+']" data-id="'+accountid+'" class="daydealindustry'+accountid+'" value="'+dateresult.industry+'">'+dateresult.industry);
                                $(_this).closest('tr').children('.daydealvisitingobj').html('<input type="hidden" name="daydealvisitingobj['+accountid+']" data-id="'+accountid+'" class="daydealvisitingobj'+accountid+'" value="'+dateresult.visitingordercontacts+'">'+dateresult.visitingordercontacts);
                                $(_this).closest('tr').children('.daydealwithvisitor').html('<input type="hidden" name="daydealwithvisitor['+accountid+']" data-id="'+accountid+'" class="daydealwithvisitor'+accountid+'" value="'+dateresult.visitingorderwithvisitor+'">'+dateresult.visitingorderwithvisitor);
                                $(_this).closest('tr').children('.daydealisvisitor').text(iswithvisitor);
                                $(_this).closest('tr').children('.daydealarrivalamount').html('<input type="hidden" name="daydealarrivalamount['+accountid+']" data-id="'+accountid+'" class="daydealarrivalamount'+accountid+'"><span class="daydealarrivalamount'+accountid+'text"></span>');
                            }
                        }else{
                            var params = {text : data.result.msg,
                            title : "更新出错"}
                            Vtiger_Helper_Js.showPnotify(params);
                        }

                    }
                }
            );

        });
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
    dayDealProductChange:function(){
        var thisInstance=this;
         $('#EditView').on('change','.adddaydealproductchange',function(){
            var _this=this;
            var dataid=$(_this).data('id');
            var productprice=$(_this).find('option:selected').data('price');
            var productstepprice=$(_this).find('option:selected').data('stepprice');
            $(_this).find('option:selected').text();
            $('.daydealmarketprice'+dataid).val(productprice);
            $('.daydealstepprice'+dataid).val(productstepprice);
            $('.daydealproductname'+dataid).val($(_this).find('option:selected').text());
            var dealamount=$('.daydealdealamount'+dataid).val();
            if(parseInt(productprice)==0 || isNaN(parseInt(productprice)) || parseInt(dealamount)==0 || isNaN(parseInt(dealamount))){
                $(_this).closest('tr').children('.daydealdiscount').text('折扣');
                return false;
            }

            if(dealamount>productprice){
                $(_this).closest('tr').children('.daydealdiscount').text('不打折');
                return false;
            }
            var discount=dealamount/productprice*10
            $(_this).closest('tr').children('.daydealdiscount').text(discount.toFixed(2)+'折');
         });
         $('#EditView').on('keyup','.checkdiscount',function(){
             thisInstance.formatNumber($(this));
             var arr=$(this).val().split('.');//只有一个小数点
             if(arr.length>2){
                 if(arr[1]==''){
                     $(this).val(arr[0]);
                 }else{
                     $(this).val(arr[0]+'.'+arr[1]);
                 }
             }
         }).on('blur','.checkdiscount',function(){
             var _this=this;
              thisInstance.formatNumber($(this));
              var arr=$(this).val().split('.');//只有一个小数点
              if(arr.length>2){
                  if(arr[1]==''){
                      $(this).val(arr[0]);
                  }else{
                      $(this).val(arr[0]+'.'+arr[1]);
                  }
              }
             var dataid=$(_this).data('id');
             var productprice=$('.daydealmarketprice'+dataid).val();//市场价
             var dealamount=$('.daydealdealamount'+dataid).val();//成交金额
             var firstpayment=$('.daydealfirstpayment'+dataid).val();//收款


             var stepprice=$(_this).closest('tr').children('.daydealproduct').children('.adddaydealproductchange').find('option:selected').data('stepprice');
             $('.daydealstepprice'+dataid).val(stepprice);
             if(parseInt(productprice)==0 || isNaN(parseInt(productprice)) || parseInt(dealamount)==0 || isNaN(parseInt(dealamount))){
                 $(_this).closest('tr').children('.daydealdiscount').text('折扣');
                 return false;
             }
             if(parseFloat(dealamount)>=parseFloat(productprice)){
                 $(_this).closest('tr').children('.daydealdiscount').text('不打折');
                 if(parseInt(firstpayment)>0){
                      if(stepprice>1){
                          //有成本价
                          //stepprice成本价
                          var currentprice=firstpayment-firstpayment/dealamount*stepprice;
                          //回款-回款/成交价*成本
                      }else{
                          //没有成本价
                          //var currentprice=firstpayment*dealamount/productprice
                          //不打折的就是其收款金额
                          var currentprice=firstpayment;
                      }
                      currentprice=currentprice>0?parseFloat(currentprice):0;
                      $(_this).closest('tr').children('.daydealarrivalamount').children('.daydealarrivalamount'+dataid).val(currentprice.toFixed(2));
                      $(_this).closest('tr').children('.daydealarrivalamount').children('.daydealarrivalamount'+dataid+'text').text(currentprice.toFixed(2));
                 }
             }else{
                var discount=dealamount/productprice*10
                $(_this).closest('tr').children('.daydealdiscount').text(discount.toFixed(2)+'折');
                if(parseInt(firstpayment)>0){
                    if(thisInstance.accDiv(dealamount,productprice)>=0.75){
                        //折扣大于75
                         if(stepprice>1){
                             //有成本
                             var currentprice=firstpayment*dealamount/productprice-firstpayment/dealamount*stepprice;
                             //回款*成交价/市场价-回款/成交价*成本价
                         }else{
                             //没有成本
                             var currentprice=firstpayment*dealamount/productprice;
                             //回款* 成交价/市场价
                         }
                     }else{
                        //折扣小于75折
                        var currentprice=0;
                     }
                     currentprice=currentprice>0?parseFloat(currentprice):0;
                     $(_this).closest('tr').children('.daydealarrivalamount').children('.daydealarrivalamount'+dataid).val(currentprice.toFixed(2));
                     $(_this).closest('tr').children('.daydealarrivalamount').children('.daydealarrivalamount'+dataid+'text').text(currentprice.toFixed(2));
                }
             }


         });
    },
    //格式化输入只能转入数字或小数保留两位
    formatNumber:function(_this){
        _this.val(_this.val().replace(/,/g,''));//只能输入数字小数点
        _this.val(_this.val().replace(/[^0-9.]/g,''));//只能输入数字小数点
        _this.val(_this.val().replace(/^0\d{1,}/g,''));//不能以0打头的整数
        _this.val(_this.val().replace(/^\./g,''));//不能以小数点打头
        _this.val(_this.val().replace(/[\d]{13,}/g,''));//超出长度全部清空
        _this.val(_this.val().replace(/\.\d{3,}$/g,''));//小数点后两位如果超过两位则将小数后的所有数清空包含小数点

    },
    /*每日成交客户*/
    initAddDayDeal:function(){
        this.addDayDeal();
        this.dayDealaccountchange();
        this.dayDealProductChange();
    },
    getrefreshnextdayvisit:function(){

        var params={
            'module' : 'SalesDaily',
            'action' :	'BasicAjax',
            'record' :	$('input[name="record"]').val(),
            'dailydate':$('input[name="dailydatetime"]').val(),
            'mode'   : 	'getNextDayVisit'
        };
        var progressIndicatorElement = jQuery.progressIndicator({
            'message' : '正在处理,请耐心等待哟',
            'position' : 'html',
            'blockInfo' : {'enabled' : true}
        });
        $('#lineItemNextDayVisit tbody').empty();
        AppConnector.request(params).then(
            function(data){
                progressIndicatorElement.progressIndicator({
                    'mode' : 'hide'
                });
                if(data.success ==  true){
                    if(data.result.num>0){
                        var dateresult=data.result.result;
                        var str=''
                        $.each(dateresult,function(key,value){
                            var iswithvisitor=value.visitingorderwithvisitor==''?'':'有';
                            str+='<tr><td><input type="hidden" name="ndvaccount['+value.visitingorderid+']" value="'+value.accountid+'"/><input type="hidden" name="ndvvisitingorder['+value.visitingorderid+']" value="'+value.visitingorderid+'"/><input type="hidden" name="ndvaccountname['+value.visitingorderid+']" value="'+value.accountname+'" />'+value.accountname+'</td>'+
                                '<td><input type="hidden" name="ndvcontacts['+value.visitingorderid+']" value="'+value.contacts+'" />'+value.contacts+'</td>'+
                                '<td><input type="hidden" name="ndvtitle['+value.visitingorderid+']" value="'+value.title+'" />'+value.title+'</td>'+
                                '<td><input type="hidden" name="ndvvisitingordernum['+value.visitingorderid+']" value="'+value.visitingordernum+'" />'+value.visitingordernum+'</td>'+
                                '<td><input type="hidden" name="ndvpurpose['+value.visitingorderid+']" value="'+value.purpose+'" />'+value.purpose+'</td><td>'+iswithvisitor+'</td>'+
                                '<td><input type="hidden" name="ndvvisitingorderwithvisitor['+value.visitingorderid+']" value="'+value.visitingorderwithvisitor+'" />'+value.visitingorderwithvisitor+'</td>'+
                                '<td><input type="hidden" name="ndvmodulestatus['+value.visitingorderid+']" value="'+value.modulestatus+'" />'+value.zhmodulestatus+'</td><td></td></tr>';
                        });

                        $('#lineItemNextDayVisit tbody').append(str);
                    }
                }
            },
            function(error){
                console.log(error+'+');
            }
        );
    },
    refreshnextdayvisit:function(){
        var instancethis=this;
        $('#refreshnextdayvisit').click(function(){
            instancethis.getrefreshnextdayvisit();
        });
    },
    CalculatedDiscount:function(productprice,dealamount,firstpayment,stepprice)
    {
        var result={};
        if(parseInt(productprice)==0 || isNaN(parseInt(productprice)) || parseInt(dealamount)==0 || isNaN(parseInt(dealamount))){

            result.msg='折扣';result.value=dealamount>0?firstpayment-firstpayment*stepprice/dealamount:firstpayment;
            return result;
        }
        if(parseFloat(dealamount)>=parseFloat(productprice)){
            result.msg='不打折';
            if(parseInt(firstpayment)>0){
                if(stepprice>1){
                    //有成本价
                    //stepprice成本价
                    var currentprice=firstpayment-firstpayment/dealamount*stepprice;
                    //回款-回款/成交价*成本
                }else{
                    //没有成本价
                    //var currentprice=firstpayment*dealamount/productprice
                    //不打折的就是其收款金额
                    var currentprice=firstpayment;
                }
                result.value=currentprice>0?parseFloat(currentprice):0;
                return result;
            }
        }else{
            var discount=dealamount/productprice*10
            result.msg=discount.toFixed(2)+'折';
            if(parseInt(firstpayment)>0){
                if(thisInstance.accDiv(dealamount,productprice)>=0.75){
                    //折扣大于75
                    if(stepprice>1){
                        //有成本
                        var currentprice=firstpayment*dealamount/productprice-firstpayment/dealamount*stepprice;
                        //回款*成交价/市场价-回款/成交价*成本价
                    }else{
                        //没有成本
                        var currentprice=firstpayment*dealamount/productprice;
                        //回款* 成交价/市场价
                    }
                }else{
                    //折扣小于75折
                    var currentprice=0;
                }
                result.value=currentprice>0?parseFloat(currentprice):0;
                return result;
            }
        }
    },
    accountStatistics:function(container){
        var params={
            'module' : 'SalesDaily',
            'action' :	'BasicAjax',
            'record' :	$('input[name="record"]').val(),
            'dailydate':$('input[name="dailydatetime"]').val(),
            'mode'   : 	'getAccountStatistics'
        };
        AppConnector.request(params).then(
            function(data){
                console.log(data);
                if(data.success ==  true){
                    var stasData = data.data;
                    var wxnumberlastweeknumber = stasData.wxnumberlastweeknumber;
                    var wxnumberlastmonthnumber = stasData.wxnumberlastmonthnumber;
                    $("input[name='todayvisitnum']").val(stasData.todayvisitnum);
                    $("input[name='total_telnumber']").val(stasData.total_telnumber);
                    $("input[name='telnumber']").val(stasData.telnumber);
                    $("input[name='tel_connect_rate']").val(stasData.tel_connect_rate);

                    if($('input[name="record"]').val()){
                        $("input[name='wxnumber']").val(stasData.wxnumber);
                        $("input[name='wxnewlyaddnumber']").val(stasData.wxnewlyaddnumber);
                        $("input[name='wxnumberweek']").val(stasData.wxnumberweek);
                        $("input[name='wxnumberweekaddnumber']").val((stasData.wxnumberweek-wxnumberlastweeknumber));
                        $("input[name='wxnumbermonth']").val(stasData.wxnumbermonth);
                        $("input[name='wxnumbermonthaddnumber']").val((stasData.wxnumbermonth-wxnumberlastmonthnumber));
                        $("input[name='wxnumberlastweeknumber']").val(wxnumberlastweeknumber);
                        $("input[name='wxnumberlastmonthnumber']").val(wxnumberlastmonthnumber);
                    }
                }
            },
            function(error){
                console.log(error+'+');
            }
        );
    },
	/**
		pop two calander.
	*/
	registerBasicEvents:function(container){
		this._super(container);
        this.ControlInit();
        this.initAddCanDeal();
        this.initAddDayDeal();
        this.refreshnextdayvisit();
        this.getrefreshnextdayvisit();
        this.getrefreshvisitingorder();
        this.getaddDayDeal();
        this.accountStatistics();
        this.calWxNumber();
        this.registerRecordPreSaveEvent();
	},
    calWxNumber:function (container) {
        $("input[name='wxnumberweek']").on('input propertychange',function () {
            var wxnumberweek = $("input[name='wxnumberweek']").val();
            var wxnumberlastweeknumber = $("input[name='wxnumberlastweeknumber']").val();
            $("input[name='wxnumberweekaddnumber']").val((wxnumberweek-wxnumberlastweeknumber));
        });
        $("input[name='wxnumbermonth']").on('input propertychange',function () {
            var wxnumbermonth = $("input[name='wxnumbermonth']").val();
            var wxnumberlastmonthnumber = $("input[name='wxnumberlastmonthnumber']").val();
            $("input[name='wxnumbermonthaddnumber']").val((wxnumbermonth-wxnumberlastmonthnumber));
        })
    },
    registerRecordPreSaveEvent: function () {
        var thisInstance = this;
        var editViewForm = this.getForm();
        editViewForm.on(Vtiger_Edit_Js.recordPreSave,function(e,data) {
            var todayvisitnum = $("input[name='todayvisitnum']").val();
            var telnumber = $("input[name='telnumber']").val();
            var tel_connect_rate=$("input[name='tel_connect_rate']").val();
            var wxnumber=$("input[name='wxnumber']").val();
            var wxnewlyaddnumber=$("input[name='wxnewlyaddnumber']").val();
            var wxnumberweek=$("input[name='wxnumberweek']").val();
            var wxnumberweekaddnumber=$("input[name='wxnumberweekaddnumber']").val();
            var wxnumbermonth=$("input[name='wxnumbermonth']").val();
            var wxnumbermonthaddnumber=$("input[name='wxnumbermonthaddnumber']").val();
            if(wxnumber==='' || wxnewlyaddnumber==='' || wxnumberweek==='' || wxnumbermonth===''){
                Vtiger_Helper_Js.showMessage({type:'error',text:'客户统计必填字段不能为空'});
                e.preventDefault();
                return false;
            }
        });

    },

});




















