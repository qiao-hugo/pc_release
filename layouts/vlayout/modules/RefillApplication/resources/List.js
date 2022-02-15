/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("RefillApplication_List_Js",{},{
    /**
     * 显示明细列表
     */
	doDetailList:function(){
		$('body').on('click','.detailList',function(){
			var data_reqstatus=$(this).attr('data-reqstatus');
			var data_statusd=$(this).attr('data-statusd');
			var recordId=$(this).data('id');
			var rechargesource=$("#rechargesources").val();
			if(data_statusd=='plus' && data_reqstatus=="Y"){
                var progressIndicatorElement = jQuery.progressIndicator({ 'message' : '拼命努力加载中...','blockInfo':{'enabled':true }});
                $(this).attr("data-reqstatus",'N')
                var module = app.getModuleName();
                var  postData={};
                postData.data = {
                    "module": module,
                    "action": "BasicAjax",
                    "record": recordId,
                    'mode':"getDetailList",
                    'rechargesource':rechargesource
                };
                postData.async=true;
                postData.dataType='html';
                var insertAfter=$(this).parents().closest('tr')
                AppConnector.request(postData).then(
                    function(data){
                        progressIndicatorElement.progressIndicator({
                         'mode' : 'hide'
                         });
                        insertAfter.after(data);

                    },
                    function(error,err){

                    }
                );

			}else{
                if(data_statusd=='plus'){
                    $('.listViewEntries'+recordId).show();
                }else{
                    $('.listViewEntries'+recordId).hide();
                }

            }
            if(data_statusd=='plus'){
                var str='<span class="label label-c_complete"><i class="icon-minus icon-white"></i></span>';
                $(this).attr("data-statusd",'minus')
            }else{
                var str='<span class="label label-c_complete"><i class="icon-plus icon-white"></i></span>';
                $(this).attr("data-statusd",'plus')
            }


			$(this).html(str);
		});
	},
    /**
     * 申请单作废;
     */
    doCancel:function(){
        $('.listViewContentDiv').on("click",'.docancel',function(e){
            var elem = jQuery(e.currentTarget);
            var recordId = elem.closest('tr').data('id');
            var name=$('#stagerecordname').val();
            var msg={'message':"是否要作废该工单？"};
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                msg={'message':"确定要作废该工单？"};
                var voidreason=$('#voidreason').val();
                if(voidreason==''){
                    Vtiger_Helper_Js.showMessage({type: 'error', text: '作废原因必填,请重新填写!'});
                    return false;
                }
                Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                    var params={};
                    var module = app.getModuleName();
                    params['record']=recordId;
                    params['action']='BasicAjax';
                    params['module']=module;
                    params['voidreason']=voidreason;
                    params['mode']='docancel';
                    AppConnector.request(params).then(
                        function(data){
                            window.location.reload(true);
                        }
                    );

                });
            });
            $('.modal-body').append('<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0;"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">作废原因<font color="red">*</font>:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><textarea id="voidreason" class="span11 "></textarea></span></div></td></tr></tbody></table>');
        });
    },
    /**
     * 财务销账;
     */
    financialstate:function(){
        $('.listViewContentDiv').on("click",'.financialstate',function(e){
            var thisInstance=this;
            var dataamountofsales=$(this).data('value');
            var status = $(this).data('status');
            var elem = jQuery(e.currentTarget);
            var recordId = elem.closest('tr').data('id');
            if(status=='no'){
                var msg={
                    'message':"确定要销账吗？",
                    "action":function(){
                        var selectValue = $('#amountofsales').val();
                        selectValue=1*selectValue;
                        if(isNaN(selectValue) || selectValue<=0 || selectValue>dataamountofsales){
                            Vtiger_Helper_Js.showPnotify(app.vtranslate('销账金额不能小于等于0且不大于垫款金额'));
                            return false;
                        }
                        return true;
                    }
                };
            }else{
                var msg={
                    'message':"确定要销账吗？",
                };
            }

            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                var params={};
                var module = app.getModuleName();
                params['record']=recordId;
                params['action']='BasicAjax';
                params['module']=module;
                params['mode']='financialstate';
                params['amountofsales']=$('#amountofsales').val();
                var progressIndicatorElement = jQuery.progressIndicator({ 'message' : '拼命努力加载中...','blockInfo':{'enabled':true }});

                AppConnector.request(params).then(
                    function(data){
                        progressIndicatorElement.progressIndicator({
                            'mode' : 'hide'
                        });
                        if(data.result.flag){
                            $(thisInstance).remove();
                        }
                        Vtiger_Helper_Js.showMessage({type: 'error', text: data.result.msg});

                        //window.location.reload(true);
                    }
                );

            });
            if(status=='no') {
                $('.modal-body').append('<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0;"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">销账金额<font color="red">*</font>:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><input id="amountofsales" class="span11 " value="' + dataamountofsales + '" readonly="readonly" /></span></div></td></tr></tbody></table>');
            }
        });
    },
    dontlisten:function(){
        var _this=this;
        var listViewContentDiv = this.getListViewContentContainer();
        listViewContentDiv.on('click','.noclick',function(event){
            event.stopPropagation();
        });
        listViewContentDiv.on("click",".checkedinverse,.checkedall",function(event){
            $('input[name="Detailrecord\[\]"]').iCheck('toggle');
            event.stopPropagation();
        });
        listViewContentDiv.on("click",".inversePayments",function(){
            $('input[name="DetailrecordPayments\[\]"]').iCheck('toggle');
            event.stopPropagation();
        });

    },
    /**
     * 导出
     */
    exportData:function(){
        $(".exportdata").click(function(){
            var progressIndicatorElement = jQuery.progressIndicator({
                'message' : "努力处理中请稍等...",
                'position' : 'html',
                'blockInfo' : {
                    'enabled' : true
                }
            });
            var searchParamsPreFix = 'BugFreeQuery';
            var rowOrder = "";
            var $searchRows = $("tr[id^=SearchConditionRow]");
            $searchRows.each(function(){
                rowOrder += $(this).attr("id")+",";
            });

            eval("$('#"+searchParamsPreFix+"_QueryRowOrder')").attr("value",rowOrder);
            var limit = $('#limit').val();
            var o = {};
            var a = $('#SearchBug').serializeArray();
            $.each(a, function() {
                if (o[this.name] !== undefined) {
                    if (!o[this.name].push) {
                        o[this.name] = [o[this.name]];
                    }
                    o[this.name].push(this.value || '');
                } else {
                    o[this.name] = this.value || '';
                }
            });
            var form=JSON.stringify(o);
            if($('#rechargesource').val()=='contractChanges'){
                /* 传的limit 实际未用到*/
                var urlParams = {"module":"RefillApplication","view":"List",'rechargesource':'contractChanges',"public":"contractChangesExport","page":1,"BugFreeQuery":form,"limit":limit};
            }else{
                var urlParams = {"module":"RefillApplication","action":"BasicAjax","mode":"exportdata","page":1,"BugFreeQuery":form,"limit":limit};
            }
            var url = location.search; //获取url中"?"符后的字串
            if (url.indexOf("?") != -1) {
                var str = url.substr(1);
                var strs = str.split("&");
                for (var i = 0; i < strs.length; i++) {
                    if(strs[i].split("=")[0]=='rechargesource'){
                        urlParams[strs[i].split("=")[0]] = unescape(strs[i].split("=")[1]);
                        break;
                    }
                }
            }
            AppConnector.request(urlParams).then(
                function(data){
                    progressIndicatorElement.progressIndicator({
                        'mode' : 'hide'
                    })
                    //因为 合同变更申请返回的是json 不是 json 对象 所以转化成json 对象
                    if($('#rechargesource').val()=='contractChanges'){
                        data=JSON.parse(data);
                    }
                   if(data.success){
                       window.location.href='index.php?module=RefillApplication&view=List&public=exportdata';
                   }
                }
            );
        });

    },
    /**
     * 打印
     */
    doprint:function(){
        var listViewContentDiv = this.getListViewContentContainer();
        $('.printall').on('click',function(){
            var Detailrecords=$('input[name="Detailrecord\[\]"]:checkbox:checked');
            if(Detailrecords.length>0){
                var records='';
                $.each(Detailrecords,function(key,value){
                    records+=$(value).val()+',';
                });
                //console.log(records.substring(0,records.length-1));
                records=records.substring(0,records.length-1);
                records=encodeURI(records);

                $url='index.php?module=RefillApplication&action=MultiExportPDF&records='+records;
                window.open($url);
            }
        });
    },
    /**
     * 回款匹配
     */
    matchreceivements:function(){
        var thisInstance=this;
        var listViewContentDiv = this.getListViewContentContainer();
        $('.matchreceivements').on('click',function(e){
            var Detailrecords=$('input[name="DetailrecordPayments\[\]"]:checkbox:checked');
            if(Detailrecords.length>0){
                var records='';
                var contractid='';
                var currentFlag=false;
                $.each(Detailrecords,function(key,value){
                    var datacontractid=$(value).data('contractid');
                    if(key!=0){
                        if(contractid!=datacontractid){
                           currentFlag=true;
                           return false;
                        }
                    }else{
                        contractid=datacontractid;
                    }
                    records+=$(value).val()+',';
                });
                if(currentFlag){
                    var params = {text: app.vtranslate(), title: app.vtranslate('请选相同合同对应的充值单!')};
                    Vtiger_Helper_Js.showPnotify(params);
                    e.preventDefault();
                    return false;
                }
                //console.log(records.substring(0,records.length-1));
                records=records.substring(0,records.length-1);
                records=encodeURI(records);
                var progressIndicatorElement = jQuery.progressIndicator({
                    'message' : "努力处理中请稍等...",
                    'position' : 'html',
                    'blockInfo' : {
                        'enabled' : true
                    }
                });
                var urlParams = {
                    "module":"RefillApplication",
                    "action":"BasicAjax",
                    "mode":"matchReceivementsList",
                    "records":records,
                    "servicecontractsid":contractid,
                    "sendNum":1
                };
                AppConnector.request(urlParams).then(
                    function(data){
                        progressIndicatorElement.progressIndicator({
                            'mode' : 'hide'
                        });
                        if(data.success){
                            if(data.result.flag){
                                var refillStr='<div style="width:100%;overflow: auto;height: 175px;"><table class="table listViewEntriesTable"><thead><tr class="listViewHeaders">'+
                                    '<th nowrap="">操作</th>'+
                                    '<th nowrap="">申请单编号</th>'+
                                    '<th nowrap="">服务合同</th>'+
                                    '<th nowrap="">客户</th>'+
                                    //'<th nowrap="">申请人</th>'+
                                    '<th nowrap="">应收款总额</th>'+
                                    '<th nowrap="">使用回款总额</th>'+
                                    '<th nowrap="">合计垫款金额</th>'+
                                    '<th nowrap="">使用金额</th>'+
                                    '<th nowrap="">备注</th>'+
                                    '</tr></thead><tbody>';
                                $.each(data.result.data.refill,function(key,value){
                                    refillStr+='<tr class="listViewEntries">'+
                                        '<th nowrap=""><b class="pull-right"><button class="btn btn-small delbuttonnewinvoicerayment" type="button" data-id="'+value.refillapplicationid+'" ><i class="icon-trash" title="删除关联回款信息"></i></button></b></th>'+
                                        '<th nowrap="">'+value.refillapplicationno+'</th>'+
                                        '<th nowrap="">'+value.servicecontractsid+'</th>'+
                                        '<th nowrap="">'+value.accountid+'</th>'+
                                        //'<th nowrap="">'+value.smownerid+'</th>'+
                                        '<th nowrap="">'+value.actualtotalrecharge+'</th>'+
                                        '<th nowrap="">'+value.totalrecharge+'</th>'+
                                        '<th nowrap="">'+value.grossadvances+'</th>'+
                                        '<th nowrap=""><input name="refillapptotal['+value.refillapplicationid+']" data-maxvalue="'+value.grossadvances+'" data-id="'+value.refillapplicationid+'" value="" /></th>'+
                                        '<th nowrap=""><input name="rremarks['+value.refillapplicationid+']" data-id="'+value.refillapplicationid+'" value="" /></th>'+
                                        '</tr>';
                                });

                                refillStr+='</tbody></html></div>'
                                var paymentsStr='<div><table class="table table-bordered blockContainer newinvoicerayment_tab detailview-table newinvoicerayment_tab">'+
                                    '<thead><tr><th class="blockHeader" colspan="8">&nbsp;&nbsp;关联回款信息 </th></tr></thead>'+
                                    '<tbody><tr><td><label class="muted">回款信息</label></td>'+
                                    '<td><label class="muted"><span class="redColor">*</span> 入账金额</label></td>'+
                                    '<td><label class="muted"><span class="redColor">*</span> 入账日期</label></td>'+
                                    '<td><label class="muted"><span class="redColor">*</span> 已使用工单金额</label></td>'+
                                    //'<td><label class="muted"><span class="redColor">*</span> 已使用充值金额</label></td>'+
                                    '<td><label class="muted"><span class="redColor">*</span> 可使用金额</label></td>'+
                                    //'<td><label class="muted"><span class="redColor">*</span> 使用金额</label></td>'+
                                    //'<td><label class="muted"><span class="redColor"></span> 备注</label></td>'
                                    '</tr>';
                                var selectStr='',occupationcost,unit_price,rechargeableamount,userMoneyd,reality_date;
                                $.each(data.result.data.payments,function(key,value){
                                    var userMoney=value.unit_price-value.rechargeableamount-value.occupationcost;
                                    var tempstr='{"receivedpaymentsid":"'+value.receivedpaymentsid+'",'+
                                        '"selectname":"'+value.owncompany+value.paytitle+'",'+
                                        '"reality_date":"'+value.reality_date+'",'+
                                        '"rechargeableamount":"'+value.rechargeableamount+'",'+
                                        '"userMoney":"'+userMoney+'",'+
                                        '"unit_price":"'+value.unit_price+'",'+
                                        '"occupationcost":"'+value.occupationcost+'"'+
                                    '}';
                                    var selectedd='';
                                    if(key==0){
                                        selectedd=' selected';
                                        rechargeableamount=value.rechargeableamount;
                                        unit_price=value.unit_price;
                                        reality_date=value.reality_date;
                                        occupationcost=value.occupationcost;
                                        userMoneyd=userMoney;
                                    }
                                    selectStr+='<option value="'+value.receivedpaymentsid+'" data-value=\''+tempstr+'\' '+selectedd+'>'+value.owncompany+value.paytitle+'</option>';
                                });
                                paymentsStr+='<tr><td><div class="row-fluid"><span class="span10"><select class="chzn-select t_tab_newinvoicerayment_id" name="receivedpaymentsid">'+selectStr+'</select></span></div></td>'+
                                    '<td><div class="row-fluid"><span class="span10"><input type="text" style="width:100px;" class="input-large total" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="total" readonly value="'+unit_price+'"></span></div></td>'+
                                    '<td><div class="row-fluid"><span class="span10"><input type="text" style="width:100px;" class="input-large arrivaldate" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" readonly name="arrivaldate" value="'+reality_date+'"></span></div></td>'+
                                    '<td><div class="row-fluid"><span class="span10"><input type="text" style="width:100px;" class="input-large arrivaldate" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" readonly name="occupationcost" value="'+occupationcost+'"></span></div></td>'+
                                    //'<td><div class="row-fluid"><span class="span10"><input type="text" style="width:100px;" class="input-large allowrefillapptotal" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" readonly name="allowrefillapptotal"  value="'+rechargeableamount+'"></span></div></td>'+
                                    '<td><div class="row-fluid"><span class="span10"><input type="text" style="width:100px;" class="input-large arrivaldate" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" readonly name="rechargeableamount" value="'+rechargeableamount+'"></span></div></td>'+
                                    //'<td><div class="row-fluid"><span class="span10"><input type="text" style="width:100px;" class="input-large refillapptotal receivedpayments_refillapptotal" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="refillapptotal" value=""></span></div></td>'+
                                    //'<td><div class="row-fluid"><span class="span10"><textarea class="span11" name="rremarks"></textarea></span></div></td>'+
                                    '</tr></tbody></table></div>'+refillStr;
                                var msg={
                                            'message':"<h3>请选择您要操作的匹配数据！</h3>",
                                            'width':'1024px',
                                            "action":thisInstance.paymentsform

                                        };
                                Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                                    console.log($('#paymentsform').serializeFormData());
                                    var params={};
                                    var module = app.getModuleName();
                                    params['action']='BasicAjax';
                                    params['module']=module;
                                    params['mode']='doAddNewReffRaymentMore';
                                    params['data']=$('#paymentsform').serializeFormData();
                                    params['data']['action']='BasicAjax';
                                    params['data']['module']=module;
                                    params['data']['mode']='doAddNewReffRaymentMore';
                                    var progressIndicatorElement = jQuery.progressIndicator({ 'message' : '拼命努力加载中...','blockInfo':{'enabled':true }});

                                    AppConnector.request(params).then(
                                        function(data){
                                            progressIndicatorElement.progressIndicator({
                                                'mode' : 'hide'
                                            });

                                            Vtiger_Helper_Js.showMessage({type: 'error', text: data.result.msg});

                                            //window.location.reload(true);
                                        }
                                    );

                                });
                                $('.modal-body').append('<hr><div style="width:100%;height:320px;overflow: hidden;"><form id="paymentsform">'+paymentsStr+'</form></div>');

                            }else{
                                var params = {text: app.vtranslate(), title: app.vtranslate(data.result.msg)};
                                Vtiger_Helper_Js.showPnotify(params);
                                return false;
                            }
                        }
                    }
                );
            }else{
                var params = {text: app.vtranslate(), title: app.vtranslate('请勾选要操作的项!')};
                Vtiger_Helper_Js.showPnotify(params);
                e.preventDefault();
                return false;
            }
        });
        $('body').on('change','select[name="receivedpaymentsid"]',function(){
            var DataValue=$(this).find("option:selected").data('value');
            $('input[name="total"]').val(DataValue.unit_price);
            $('input[name="arrivaldate"]').val(DataValue.reality_date);
            $('input[name="rechargeableamount"]').val(DataValue.rechargeableamount);
            $('input[name="occupationcost"]').val(DataValue.occupationcost);
            $('input[name^="refillapptotal\["]').val(0);
            $('input[name^="rremarks\["]').val('');
        });
        $('body').on('click','.delbuttonnewinvoicerayment',function(){
            if(confirm('确定要删除吗!')){
                $(this).closest('tr').remove();
            }
        });
        $('body').on('keyup','input[name^="refillapptotal\["]',function(){
            thisInstance.formatNumber($(this));
            var arr=$(this).val().split('.');//只有一个小数点
            if(arr.length>2){
                if(arr[1]==''){
                    $(this).val(arr[0]);
                }else{
                    $(this).val(arr[0]+'.'+arr[1]);
                }
            }
            var thisVal=$(this).val();
            var maxValue=$(this).data('maxvalue');
            if(thisInstance.FloatSub(maxValue,thisVal)<=0){
                thisVal=maxValue;
                $(this).val(maxValue);
            }
            var refillapptotal=0;
            $.each($('input[name^="refillapptotal\["]'),function(key,value){
                refillapptotal=thisInstance.FloatAdd(refillapptotal,$(value).val());
            });
            var rechargeableamount=$('input[name="rechargeableamount"]').val();
            var diffvalue=thisInstance.FloatSub(rechargeableamount,refillapptotal);
            if(diffvalue<0){
                var currentValue=thisInstance.FloatSub(rechargeableamount,thisInstance.FloatSub(refillapptotal,thisVal));
                $(this).val(currentValue);
            }
        }).on('blur','input[name^="refillapptotal\["]',function(){
           $(this).trigger("keyup");
        }).on('paste','input[name^="refillapptotal\["]',function(){
            $(this).trigger("keyup");
        })
    },
    /**
     * 合同变更申请
     */
    contractChangeApplication:function(){
        var thisInstance=this;
        $('.contractChangeApplication').on('click',function(e){
            window.location.href="index.php?module=RefillApplication&view=Edit&rechargesource=contractChanges";
        });
    },
    paymentsform:function(){
        var flag=false;
        var refillapptotalVal=0;
        var $refillapptotal=$('input[name^="refillapptotal\["]');
        if($refillapptotal.length==0){
            var params = {text: app.vtranslate(), title: app.vtranslate('没有可匹配的充值单')};
            Vtiger_Helper_Js.showPnotify(params);
            return false;
        }
        $.each($refillapptotal,function(key,value){
            var thisValue=($(value).val()=='')?0:$(value).val();
            refillapptotalVal+=parseFloat(thisValue);
            /*if(thisValue>0){
                var id=$(value).data('id');
                if($('input[name="rremarks['+id+']"]').val()==''){
                    flag=true;
                    return false;
                }
            };*/
        });
        /*if(flag){
            var params = {text: app.vtranslate(), title: app.vtranslate('有匹配回款的,备注必填!')};
            Vtiger_Helper_Js.showPnotify(params);
            return false;
        }*/
        if(refillapptotalVal<=0){
            var params = {text: app.vtranslate(), title: app.vtranslate('请选择要匹配的充值单')};
            Vtiger_Helper_Js.showPnotify(params);
            return false;
        }
        return true;
    },
    loading:function(){
        Vtiger_Helper_Js.showConfirmationBox =function(data){
            var aDeferred = jQuery.Deferred();
            var width='800px';
            var checkFlag=true
            if(typeof  data['width'] != "undefined"){
                width=data['width'];
            }
            var bootBoxModal = bootbox.confirm({message:data['message'],width:width, callback:function(result) {
                if(result){
                    var checkFlag=true
                    if(typeof  data['action'] != "undefined"){
                        checkFlag=(data['action'])();
                    }
                    if(checkFlag){
                        aDeferred.resolve();
                    }else{
                        return false;
                    }
                } else{
                    aDeferred.reject();
                }
            }, buttons: { cancel: {
                label: '取消',
                className: 'btn'
            },
                confirm: {
                    label: '确认',
                    className: 'btn-success'
                }
            }});
            bootBoxModal.on('hidden',function(e){
                if(jQuery('#globalmodal').length > 0) {
                    jQuery('body').addClass('modal-open');
                }
            })
            return aDeferred.promise();
        }
    },
    //浮点数加法运算
    FloatAdd:function(arg1,arg2){
        var r1,r2,m;
        try{r1=arg1.toString().split(".")[1].length}catch(e){r1=0}
        try{r2=arg2.toString().split(".")[1].length}catch(e){r2=0}
        m=Math.pow(10,Math.max(r1,r2));
        return (arg1*m+arg2*m)/m;
    },

    //浮点数减法运算
    FloatSub:function(arg1,arg2){
        var r1,r2,m,n;
        try{r1=arg1.toString().split(".")[1].length}catch(e){r1=0}
        try{r2=arg2.toString().split(".")[1].length}catch(e){r2=0}
        m=Math.pow(10,Math.max(r1,r2));
        //动态控制精度长度
        n=(r1>r2)?r1:r2;
        return ((arg1*m-arg2*m)/m).toFixed(n);
    },

    //浮点数乘法运算
    FloatMul:function(arg1,arg2)
    {
        var m=0,s1=arg1.toString(),s2=arg2.toString();
        try{m+=s1.split(".")[1].length}catch(e){}
        try{m+=s2.split(".")[1].length}catch(e){}
        return Number(s1.replace(".",""))*Number(s2.replace(".",""))/Math.pow(10,m);
    },


    //浮点数除法运算
    FloatDiv:function(arg1,arg2){
        var t1=0,t2=0,r1,r2;
        try{t1=arg1.toString().split(".")[1].length}catch(e){}
        try{t2=arg2.toString().split(".")[1].length}catch(e){}
        with(Math){
            r1=Number(arg1.toString().replace(".",""));
            r2=Number(arg2.toString().replace(".",""));
            return (r1/r2)*pow(10,t2-t1);
        }
    },
    formatNumber:function(_this){
        _this.val(_this.val().replace(/,/g,''));//只能输入数字小数点
        _this.val(_this.val().replace(/[^0-9.]/g,''));//只能输入数字小数点
        _this.val(_this.val().replace(/^0\d{1,}/g,''));//不能以0打头的整数
        _this.val(_this.val().replace(/^\./g,''));//不能以小数点打头
        _this.val(_this.val().replace(/[\d]{13,}/g,''));//超出长度全部清空
        _this.val(_this.val().replace(/\.\d{3,}$/g,''));//小数点后两位如果超过两位则将小数后的所有数清空包含小数点
        _this.val(_this.val().replace(/\.\d*\.$/g,''));//小数点后两位如果超过两位则将小数后的所有数清空包含小数点
    },

    //计算合计应收款总额
    totalMoney:function(){
        var thisInstance=this;
        $("#listViewContents").on("change",'input[name="Detailrecord[]"]',function(){
            var totalAmount=0;
            $('input[name="Detailrecord[]"]:checked').each(function () {
                var amount=$(this).data('amount');
                totalAmount=thisInstance.FloatAdd(totalAmount,amount);
            });
            $("#totalAmount").html("合计应收款总额："+totalAmount.toFixed(2));
        });
    },

    //全选时计算金额
    allCheck:function(){
        $("#listViewContents").on("click",'input[name="Deta"]',function(){
            $('#listViewContents input[name="Detailrecord[]').trigger('change');
        });
    },

	registerEvents : function(){
		this._super();
		this.doDetailList();
		this.doCancel();
		this.dontlisten();
		this.doprint();
		this.exportData();
		this.financialstate();
		this.matchreceivements();
		this.loading();
        this.findload();
        this.contractChangeApplication();
        this.totalMoney();
        this.allCheck();
	},
        findload:function(){
            var is_advances = $('#is_advances').val();
            var contract_no = $('#contract_no').val();
            var userid = $('#userid').val();
            var tmp = 0;
            if(is_advances == 1 ){
                $('#is_advances').val("2");
                    $('#BugFreeQuery_field1').val('vtiger_servicecontracts.contract_no##10##3070##reference');
                    $('#BugFreeQuery_value1').val(contract_no);
                addSearchField(1);
                $('#BugFreeQuery_field2').val('vtiger_crmentity.smownerid##53##3068##owner');
                $('#BugFreeQuery_field2').trigger("change");
                $('#BugFreeQuery_value2').val(userid);
                var sel_val = $('#BugFreeQuery_value2').find("option:selected").text();
                      $('#BugFreeQuery_value2_chzn').find('.chzn-results li').each(function(){
                    if(sel_val == $(this).text()){
                        $(this).addClass('result-selected');
                       $('#BugFreeQuery_value2_chzn .chzn-single span:first-child').text($(this).text());
                          console.log(sel_val);
                    }
                });
                addSearchField(2);
                $('#BugFreeQuery_field3').val('vtiger_refillapplication.modulestatus##15##3074##picklist');
                $('#BugFreeQuery_field3').trigger("change");
                $('#BugFreeQuery_value3').val('c_complete');
                var sel_val = $('#BugFreeQuery_value3').find("option:selected").text();
                 $('#BugFreeQuery_value3_chzn').find('.chzn-results li').each(function(){
                    if(sel_val == $(this).text()){
                        $(this).addClass('result-selected');
                       $('#BugFreeQuery_value3_chzn .chzn-single span:first-child').text($(this).text());
                          console.log(sel_val);
                    }
                });
                addSearchField(3);
                $('#BugFreeQuery_field4').val('vtiger_refillapplication.iscushion##56##11490##boolean');
                $('#BugFreeQuery_field4').trigger("change");
                $('#BugFreeQuery_value4').val('1');
                var sel_val = $('#BugFreeQuery_value4').find("option:selected").text();
                 $('#BugFreeQuery_value4_chzn').find('.chzn-results li').each(function(){
                    if(sel_val == $(this).text()){
                        $(this).addClass('result-selected');
                       $('#BugFreeQuery_value4_chzn .chzn-single span:first-child').text($(this).text());
                          console.log(sel_val);
                    }
                });
                $('#PostQuery').trigger("click");
//                $('#SearchBug').submit();
            }
        }
});