Vtiger_Detail_Js('RefillApplication_Detail_Js',{
},{
	
bindSalesorderProjectTasksrel:function(){
	
	$('.widgetContainer_workflows').on("click",'#SalesorderProjectTasksrel',function(){
		$('#projectselectdiv').toggle("fast");
	});
	var projectid ="";
	$('#projectselect').live('change',function(){
		projectid=$(this).val();
	});
	$('.details').on("click",'#realSalesorderProjectTasksrel',function(){
		if(projectid == ""){
			alert('请选择项目模版');
		}

		var params={};
		params['action'] = 'SaveAjax';
		params['module'] = 'SalesorderProjectTasksrel';
		params['mode'] = 'autogeneration';
		params['projectid'] = projectid;
		params['record'] = $('#recordid').val();
		var d={};
		d.data=params;
		d.type = 'GET';
		AppConnector.request(d).then(
				function(data){
					if(data.success==true){
						var num = data.result;
						if(num==''){
							var tex = "模版下无工单任务";
						}else{
							var tex = num+"条工单任务生成成功";
						}
						//刷新当前的挂件，在这里本来可以使用父类的方法，但是不生效，只能重新写了
						var widgetContainer = $(".widgetContainer_workflows");
						var urlParams = widgetContainer.attr('data-url');
						params = {
							'type' : 'GET',
							'dataType': 'html',
							'data' : urlParams
						};
						widgetContainer.progressIndicator({});
						AppConnector.request(params).then(	
						function(data){
								widgetContainer.progressIndicator({'mode': 'hide'});
								widgetContainer.html(data);
								Vtiger_Helper_Js.showMessage({type:'success',text:tex});
							},
							function(){}
						);
					}
				}
		);
	});		
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
    formatNumber:function(_this){
        _this.val(_this.val().replace(/,/g,''));//只能输入数字小数点
        _this.val(_this.val().replace(/[^0-9.]/g,''));//只能输入数字小数点
        _this.val(_this.val().replace(/^0\d{1,}/g,''));//不能以0打头的整数
        _this.val(_this.val().replace(/^\./g,''));//不能以小数点打头
        _this.val(_this.val().replace(/[\d]{13,}/g,''));//超出长度全部清空
        _this.val(_this.val().replace(/\.\d{3,}$/g,''));//小数点后两位如果超过两位则将小数后的所有数清空包含小数点
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
	formDataArray:function(fieldName){
        var returnValue=[];
        $.each($('input[name^="'+fieldName+'"]').serializeArray(), function(i, field){
            returnValue.push(field.value);
        });
        return returnValue;
	},
    refundsOrTransfers:function(){
		var thisInstance=this;
		$("#detailView").on('click','.refundsOrTransfers',function(){
			var rechargesheetid=$(this).data('id');
			var recordId=$("#recordid").val();
			var params={};
            params.data = {
                "module": "RefillApplication",
                "action": "BasicAjax",
                "mode": "getRechargeSheet",
                "record": recordId,
                "rechargesheetid": rechargesheetid
            };
            params.async=false;
            params.dataType='html';
            var respondata='';
            AppConnector.request(params).then(
                function(data){
                    respondata=data;
                },
                function (error) {
			});
            //console.log(respondata);
            if(respondata==1){
                var messages='当前外采单正在进行打包付款，请先处理打包出款再红冲';
                Vtiger_Helper_Js.showMessage({type:'error',text:messages});
                return false;
            }
            var msg={
                'message':' '
            };
            thisInstance.showConfirmationBox(msg).then(function(e){
                var params={};
                params.data = {
                    "module": "RefillApplication",
                    "action": "BasicAjax",
                    "mode": "dorefundsOrTransfers",
                    "data": $('#refundsTransfers').serializeFormData(),
					"record":$('#recordid').val(),
					"updaterefillprayment":thisInstance.formDataArray('updaterefillprayment'),
					"refundamount":thisInstance.formDataArray('refundamount'),
                    "repaymenttotal":thisInstance.formDataArray('repaymenttotal'),
                    "updaterepayment":thisInstance.formDataArray('updaterepayment'),
                };
                params.async=false;
                AppConnector.request(params).then(
                    function(data){
                        if(data.result.flag){
                            window.location.reload();
                        }else{
                            Vtiger_Helper_Js.showMessage({type:'error',text:data.result.msg});
                            return false;
                        }
                    },
                    function (error) {
                    });

            },function(error, err) {});
            $('.modal-content .modal-body .bootbox-body').append('<div style="overflow-y:auto;width:100%;height:430px;">'+respondata+'</div>');
            $('.modal-content .modal-body').css({overflow:'hidden'});
		})
	},
 	numberCalc:function(){
		var thisInstance=this;
		$('body').on('keyup blur','input[name="mprestoreadrate"],input[name="mrechargeamount"]',function(){
            thisInstance.formatNumber($(this));
			var dataType=$(this).data('type');
			var dataValue=$(this).data('value');
			var rebatetypeValue=$('input[name="rebatetypevalue"]').val();
			var accountrebatetypevalue=$('input[name="accountrebatetypevalue"]').val();
			var relatedField=dataType=='mprestoreadrate'?'mrechargeamount':'mprestoreadrate';
            var thisValue=$(this).val();
            thisValue=thisValue>0?thisValue:0;
            if(dataValue<thisValue){
                $(this).val(dataValue);
                return false;
			}
            var currentDiscount=$('input[name="mdiscount"]').val();
            if(currentDiscount==''){
                //返点不是带出来的不填写
                $(this).val(0);
                return false;
            }
            currentDiscount = currentDiscount > 0 ? currentDiscount : 0;
            if(accountrebatetypevalue=='CashBack'){
                currentDiscount = thisInstance.FloatDiv(currentDiscount, 100);
                currentDiscount=thisInstance.FloatSub(1,currentDiscount);

                var relatedValue = dataType == 'mprestoreadrate' ? thisInstance.FloatMul(thisValue, currentDiscount) : thisInstance.FloatDiv(thisValue, currentDiscount);
            }else {
                currentDiscount = thisInstance.FloatDiv(currentDiscount, 100);
                currentDiscount = thisInstance.FloatAdd(1, currentDiscount);
                var relatedValue = dataType == 'mprestoreadrate' ? thisInstance.FloatDiv(thisValue, currentDiscount) : thisInstance.FloatMul(thisValue, currentDiscount);
            }
            var rechargesource=$('input[name="rechargesource"]').val();
            if(rechargesource=='Vendors'){
                var mprestoreadrate=$('input[name="mprestoreadrate"]').val();
                var msupprebate=$('input[name="msupprebate"]').val();
                msupprebate=msupprebate>0?msupprebate:0;
                if(rebatetypeValue=='CashBack'){
                    msupprebate=thisInstance.FloatDiv(msupprebate,100);
                    msupprebate=thisInstance.FloatSub(1,msupprebate);
                    var mprestoreadrateValue=thisInstance.FloatMul(mprestoreadrate,msupprebate)*1.0;
                }else{
                    msupprebate=thisInstance.FloatDiv(msupprebate,100);
                    msupprebate=thisInstance.FloatAdd(1,msupprebate);
                    var mprestoreadrateValue=thisInstance.FloatDiv(mprestoreadrate,msupprebate)*1.0;
                }
                $('input[name="amountpayable"]').val(mprestoreadrateValue.toFixed(2));
            }
            var topplatform=$('input[name="productid_display"]').val();
            if(topplatform==undefined){
                topplatform=$('input[name="productid"]').val();
            };
            if(topplatform=='谷歌'){
                //var tax=thisInstance.taxCalc();
                //tax=thisInstance.FloatAdd(1,thisInstance.FloatDiv(tax,100));
                //relatedValue=dataType=='mprestoreadrate'?thisInstance.FloatMul(relatedValue,tax):thisInstance.FloatDiv(relatedValue,tax);
            }
            $('input[name="'+relatedField+'"]').val(relatedValue.toFixed(2));
            thisInstance.mrefundamountCalc();
		});
        $('body').on("keyup blur",'input[name="mtaxation"],input[name="mfactorage"],input[name="mactivationfee"]',function(){
            thisInstance.formatNumber($(this));
            var dataValue=$(this).data('value');
            var thisValue=$(this).val();
            thisValue=thisValue>0?thisValue:0;
            if(dataValue<thisValue){
                $(this).val(dataValue);
            }
            thisInstance.mrefundamountCalc();
        });
        $('body').on("keyup blur",'input[name^="refundamount"]',function(){
            thisInstance.formatNumber($(this));
           	var dataId=$(this).data('id');
           	var thisValue=$(this).val();
           	var backwashtotal=$('input[name="backwashtotal'+dataId+'"]').val();
            thisValue=thisInstance.FloatSub(thisValue,backwashtotal)>0?backwashtotal:thisValue;
            $(this).val(thisValue);
            var mrefundamount=$('input[name="mrefundamount"]').val();
            var refundamountSum=0;
           	$('input[name^="refundamount"]').each(function(key,value){
                refundamountSum=thisInstance.FloatAdd(refundamountSum,$(value).val());
			});
           	if(thisInstance.FloatSub(refundamountSum,mrefundamount)*1>0){
           		var diffValue=refundamountSum-thisValue;
           		thisValue=thisInstance.FloatSub(mrefundamount,diffValue)*1;
                thisValue=thisValue.toFixed(2);
			}
			$(this).val(thisValue);
        });
        $('body').on("keyup blur",'input[name^="repaymenttotal"]',function(){
            thisInstance.formatNumber($(this));
            var dataId=$(this).data('id');
            var thisValue=$(this).val();
            var backwashtotal=$('input[name="repaymentbackwashtotal'+dataId+'"]').val();
            thisValue=thisInstance.FloatSub(thisValue,backwashtotal)>0?backwashtotal:thisValue;
            $(this).val(thisValue);
            var amountpayable=$('input[name="amountpayable"]').val();
            var repaymenttotalSum=0;
            $('input[name^="repaymenttotal"]').each(function(key,value){
                repaymenttotalSum=thisInstance.FloatAdd(repaymenttotalSum,$(value).val());
            });
            if(thisInstance.FloatSub(repaymenttotalSum,amountpayable)*1>0){
                var diffValue=repaymenttotalSum-thisValue;
                thisValue=thisInstance.FloatSub(amountpayable,diffValue)*1;
                thisValue=thisValue.toFixed(2);
            }
            $(this).val(thisValue);
        });
        $('body').on("keyup blur",'input[name="transferamount"]',function(){
            thisInstance.formatNumber($(this));
            var thisValue=$(this).val();
            var dataValue=$(this).data('value');
            thisValue=thisValue>0?thisValue:0;
            if(dataValue<thisValue){
                $(this).val(dataValue);
            }
            thisValue=thisValue*1;
            $('input[name="mrefundamount"]').val(thisValue.toFixed(2));
        });
        $('body').on("keyup blur",'input[name="amountpayable"]',function(){
            thisInstance.formatNumber($(this));
            var thisValue=$(this).val();
            var dataValue=$(this).data('value');
            thisValue=thisValue>0?thisValue:0;
            if(dataValue<thisValue){
                $(this).val(dataValue);
            }
            thisValue=thisValue*1;
            //$('input[name="mrefundamount"]').val(thisValue.toFixed(2));
        });
	},
    /**
     * 税点计算
     */
    taxCalc:function(){
        var tax=$('input[name="tax"]').val();
        return tax.replace(/%/g,'');
    },
    mrefundamountCalc:function(){
        var thisInstance=this;
        var rechargeamount=$('input[name="mrechargeamount"]').val();//充值金额
        /*var factorage=$('input[name="mfactorage"]').val();//代理商服务费
        var taxation=$('input[name="mtaxation"]').val();//税费
        var activationfee=$('input[name="mactivationfee"]').val();//开户费*/
        var factorage=0;//代理商服务费
        var taxation=0;//税费
        var activationfee=0;//开户费
        var totalcost=this.FloatAdd(activationfee,this.FloatAdd(taxation,factorage))*1;
        var transferamount=this.FloatAdd(rechargeamount,totalcost)*1;
        $('input[name="mrefundamount"]').val(transferamount.toFixed(2));
        $('input[name="transferamount"]').val(transferamount.toFixed(2));
    },
    showConfirmationBox : function(data){
        var thisstance=this;
        var aDeferred = jQuery.Deferred();
        var width='800px';
        if(typeof  data['width'] != "undefined"){
            width=data['width'];
        }
        var bootBoxModal = bootbox.confirm({message:data['message'],width:width, callback:function(result) {
            if(result){
                if(thisstance.checkedform()){
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
        return aDeferred.promise();
    },
    addreffrayment : function () {
        var me = this;
        $('#add_newinvoicerayment').on('click', function(){
            var params = {};
            params.data={
                "module": "RefillApplication",
                "action": "BasicAjax",
                "mode": "addNewReffRayment",
                "record":$('#recordid').val()
            };
            params.async=false;
            var progressIndicatorElement = jQuery.progressIndicator({
                'message' : '正在加载...',
                'position' : 'html',
                'blockInfo' : {'enabled' : true}
            });
            AppConnector.request(params).then(
                function(data){
                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                    if(data.success){
                        if(data.result.flag){
                            var str='';
                            if(data.result.dataresult.length) {
                                $.each(data.result.dataresult, function (key, value) {
                                    var userMoney=value.unit_price-value.rechargeableamount-value.occupationcost;
                                    userMoney*=1.0;
                                    userMoney=userMoney.toFixed(2);
                                    str += '<br><table class="table table-bordered newinvoicerayment_tab detailview-table newinvoicerayment_tab' + value.receivedpaymentsid + '" data-id="' + value.receivedpaymentsid + '">' +
                                        '<thead><tr><th class="blockHeader" colspan="9">&nbsp;&nbsp;关联回款信息[' + (key + 1) + '] <b class="pull-right"><button class="btn btn-small savebuttonnewinvoicerayment" type="button" data-id=' + value.receivedpaymentsid + '><i class="icon-ok-circle" title="保存关联回款信息"></i></button>&nbsp;<button class="btn btn-small delbuttonnewinvoicerayment" type="button" data-id="' + value.receivedpaymentsid + '"><i class="icon-trash" title="删除关联回款信息"></i></button></b></th></tr></thead>' +
                                        '<tbody><tr><td><label class="muted">回款信息</label></td>' +
                                        '<td><label class="muted"><span class="redColor">*</span> 入账金额</label></td>' +
                                        '<td><label class="muted"><span class="redColor">*</span> 入账日期</label></td>' +
                                        '<td><label class="muted"><span class="redColor">*</span> 来源</label></td>'+
                                        '<td><label class="muted"><span class="redColor">*</span> 已用工单成本</label></td>' +
                                        '<td><label class="muted"><span class="redColor">*</span> 已使用充值金额</label></td>' +
                                        '<td><label class="muted"><span class="redColor">*</span> 可使用金额</label></td>' +
                                        '<td><label class="muted"><span class="redColor">*</span> 使用金额</label></td>' +
                                        '<td><label class="muted"><span class="redColor"></span> 备注</label></td></tr>' +
                                        '<tr><td><input type="hidden" name="insertii[' + value.receivedpaymentsid + ']" value="' + value.receivedpaymentsid + '"><input type="hidden" class="receivedpaymentsid_display" name="receivedpaymentsid_display[' + value.receivedpaymentsid + ']" data-id="' + value.receivedpaymentsid + '" value=""> <input type="hidden" class="invoicecompany" name="owncompany[' + value.receivedpaymentsid + ']" data-id="' + value.receivedpaymentsid + '" value="' + value.owncompany + '"><div class="row-fluid"><span class="span10"><select class="chzn-select t_tab_newinvoicerayment_id" name="paytitle[' + value.receivedpaymentsid + ']" data-id="' + value.receivedpaymentsid + '" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"><option value="' + value.paytitle + '">' + value.owncompany + value.paytitle + '</option></select></span></div></td>' +
                                        '<td><div class="row-fluid"><span class="span10"><input type="text" style="width:100px;" class="input-large total" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="total[' + value.receivedpaymentsid + ']" data-id="' + value.receivedpaymentsid + '" readonly value="' + value.unit_price + '"></span></div></td>' +
                                        '<td><div class="row-fluid"><span class="span10"><input type="text" style="width:100px;" class="input-large arrivaldate" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" readonly name="arrivaldate[' + value.receivedpaymentsid + ']" data-id="' + value.receivedpaymentsid + '" value="' + value.reality_date + '"></span></div></td>' +
                                        '<td><div class="row-fluid"><span class="span10"><input type="text" style="width:100px;" class="input-large arrivaldate" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" readonly name="arrivaldate[' + value.receivedpaymentsid + ']" data-id="' + value.receivedpaymentsid + '" value="' + value.rorigin + '"></span></div></td>' +
                                        '<td><div class="row-fluid"><span class="span10"><input type="text" style="width:100px;" class="input-large arrivaldate" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" readonly name="occupationcost['+value.receivedpaymentsid+']" data-id="'+value.receivedpaymentsid+'" value="'+value.occupationcost+'"></span></div></td>'+
                                        '<td><div class="row-fluid"><span class="span10"><input type="text" style="width:100px;" class="input-large arrivaldate" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" readonly data-id="'+value.receivedpaymentsid+'" value="'+userMoney+'"></span></div></td>'+
                                        '<td><div class="row-fluid"><span class="span10"><input type="text" style="width:100px;" class="input-large allowrefillapptotal" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" readonly name="allowrefillapptotal[' + value.receivedpaymentsid + ']" data-id="' + value.receivedpaymentsid + '" value="' + value.rechargeableamount + '"></span></div></td>' +
                                        '<td><div class="row-fluid"><span class="span10"><input type="text" style="width:100px;" class="input-large refillapptotal receivedpayments_refillapptotal" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="refillapptotal[' + value.receivedpaymentsid + ']" data-id="' + value.receivedpaymentsid + '" value=""></span></div></td>' +
                                        '<td><div class="row-fluid"><span class="span10"><textarea class="span11" data-id="' + value.receivedpaymentsid + '" name="rremarks[' + value.receivedpaymentsid + ']"></textarea></span></div></td></tr></tbody></table>';
                                });
                                str+='<input type="hidden" id="abletotalrecharge" value="'+data.result.totalrecharge+'">';
                                $('#addrepayment').html(str);
                            }
                        }else{
                            Vtiger_Helper_Js.showMessage({type:'error',text:data.result.msg});
                        }

                    }
                },
                function(){
                }
            );
        });
    },
    checkedform:function(){
        $('input[name="mprestoreadrate"]').trigger('keyup');
    	var thisInstance=this;
        var mrefundamount=$('input[name="mrefundamount"]').val();
        mrefundamount=mrefundamount>0?mrefundamount:0;
        if(mrefundamount==0){
            $('input[name="mrefundamount"]').focus();
            $('input[name="mrefundamount"]').attr('data-content','<font color="red">必填项不能为空</font>');
            $('input[name="mrefundamount"]').popover("show");
            $('.popover-content').css({"color":"red","fontSize":"12px"});
            $('.popover').css('z-index',1000010);
            setTimeout("$('input[name=\"mrefundamount\"]').popover('destroy')",2000);
            return false;
		}
		var mstatus=$('input[name="mstatus"]').val();
        if(mstatus==''){
            $('input[name="mstatus"]').focus();
            $('input[name="mstatus"]').attr('data-content','<font color="red">必填项不能为空</font>');
            $('input[name="mstatus"]').popover("show");
            $('.popover-content').css({"color":"red","fontSize":"12px"});
            $('.popover').css('z-index',1000010);
            setTimeout("$('input[name=\"mstatus\"]').popover('destroy')",2000);
            return false;
        }

        //验证代理商服务费  开户费  税费
            var old_taxation = thisInstance.isNotANumber($('[name=taxation]').val()) ? $('[name=taxation]').val() : $('[name=taxation]').val('0');//税费
            var old_factorage = thisInstance.isNotANumber($('[name=factorage]').val()) ? $('[name=factorage]').val() : $('[name=factorage]').val('0');//代理费
            var old_activationfee = thisInstance.isNotANumber($('[name=activationfee]').val()) ? $('[name=activationfee]').val() : $('[name=activationfee]').val('0');//开户费

            var taxation_data = $('[name=taxation]').data("value");
            //计算
            if( (taxation_data ) < old_taxation){
                $('input[name="taxation"]').focus();
                $('input[name="taxation"]').attr('data-content','<font color="red">填写数字不能大于退款额</font>');
                $('input[name="taxation"]').popover("show");
                $('.popover-content').css({"color":"red","fontSize":"12px"});
                $('.popover').css('z-index',1000010);
                setTimeout("$('input[name=\"taxation\"]').popover('destroy')",200);
                return false;
            }
            var factorage_data = $('[name=factorage]').data("value");
            //计算
            if((factorage_data) < old_factorage){
                old_factorage = factorage_tmp;
                $('input[name="factorage"]').focus();
                $('input[name="factorage"]').attr('data-content','<font color="red">填写数字不能大于退款额</font>');
                $('input[name="factorage"]').popover("show");
                $('.popover-content').css({"color":"red","fontSize":"12px"});
                $('.popover').css('z-index',1000010);
                setTimeout("$('input[name=\"factorage\"]').popover('destroy')",1000);
                return false;
            }
            var activationfee_data = $('[name=activationfee]').data("value");
            //计算
            if((activationfee_data) < old_activationfee){
                $('input[name="activationfee"]').focus();
                $('input[name="activationfee"]').attr('data-content','<font color="red">填写数字不能大于退款额</font>');
                $('input[name="activationfee"]').popover("show");
                $('.popover-content').css({"color":"red","fontSize":"12px"});
                $('.popover').css('z-index',1000010);
                setTimeout("$('input[name=\"activationfee\"]').popover('destroy')",1000);
                return false;

//                $('[name=activationfee]').val(old_factorage);
            }

        var trefundamount=$('input[name="trefundamount"]').val();
        if(thisInstance.FloatSub(mrefundamount,trefundamount)>0){
            Vtiger_Helper_Js.showMessage({type:'error',text:'退款金额不可大于最高可退款金额,请重新填写!'});
            return false;
        }
        var refundamountSum=0;
        $('input[name^="refundamount"]').each(function(key,value){
            refundamountSum=thisInstance.FloatAdd(refundamountSum,$(value).val());
        });

        if(mrefundamount<refundamountSum){
            Vtiger_Helper_Js.showMessage({type:'error',text:'退款金额不可大于最高可退款金额,请重新填写!'});
            return false;
		}
		var rechargesource=$('input[name="rechargesource"]').val();
        // 如果 如果是 未支付则不做下面的判断
        var ispayment=$('input[name="ispayment"]').val();
        if(ispayment!='unpaid'){
            if(rechargesource=='Vendors'){
                //供应商退款

                if(amountpayable==''|| amountpayable<=0){
                    $('input[name="amountpayable"]').focus();
                    $('input[name="amountpayable"]').attr('data-content','<font color="red">必填项不能为空</font>');
                    $('input[name="amountpayable"]').popover("show");
                    $('.popover-content').css({"color":"red","fontSize":"12px"});
                    $('.popover').css('z-index',1000010);
                    setTimeout("$('input[name=\"amountpayable\"]').popover('destroy')",2000);
                    return false;
                }
                var repaymenttotalSum=0;
                $('input[name^="repaymenttotal"]').each(function(key,value){
                    repaymenttotalSum=thisInstance.FloatAdd(repaymenttotalSum,$(value).val());
                });
                var amountpayable=$('input[name="amountpayable"]').val();

                if(thisInstance.FloatSub(amountpayable,repaymenttotalSum)!=0 && ispayment!='unpaid'){
                    Vtiger_Helper_Js.showMessage({type:'error',text:'红冲退款金额减去退款现金总额不可大于充值单合计垫款额或者供应商退款金额不等,请重新填写!'});
                    return false;
                }
            }
        }
        if(thisInstance.checkRefundsOrTransfers()){
            return false;
        }
        return true;
    },
    refillapptotalCalc:function(){
        var thisInstance=this;
        $('#detailView').on('keyup blur','input[name^="refillapptotal["]',function(){
            thisInstance.formatNumber($(this));
            var thisValue=$(this).val();
            var dataId=$(this).data('id');
            var allowrefillapptotal=$('input[name^="allowrefillapptotal['+dataId+']"]').val();
            //console.log(allowrefillapptotal);
            if(thisInstance.FloatSub(thisValue,allowrefillapptotal)>0){
                Vtiger_Helper_Js.showMessage({type:'error',text:'匹配金额大于可匹配金额!'});
                thisValue=allowrefillapptotal*1.0;
                $(this).val(thisValue.toFixed(2));
            }
            var abletotalrecharge=$('#abletotalrecharge').val();
            if(thisInstance.FloatSub(thisValue,abletotalrecharge)>0){
                Vtiger_Helper_Js.showMessage({type:'error',text:'匹配金额大于可匹配金额!'});
                abletotalrecharge=abletotalrecharge*1.0;
                $(this).val(abletotalrecharge.toFixed(2));
            }
        });
    },
    doRevokeRelation:function(){
       $("#detailView").on("click",'.doRevokeRelation',function(){
          var refillappraymentid=$(this).data('refillappraymentid');
          var record=$(this).data('record');
           var message='确定要解除关联吗？';
           var msg={
               'message':message
           };
           Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
               var params= {
                   "module": "RefillApplication",
                   "action": "BasicAjax",
                   "mode": "revokeRelation",
                   "refillappraymentid":refillappraymentid,
                   "record":record
               };
               AppConnector.request(params).then(
                   function(data){
                       if(data.result.flag){
                           Vtiger_Helper_Js.showMessage({type:'error',text:data.result.msg});

                       }else{
                           window.location.reload();

                       }
                   },
                   function (error) {
                   });
           },function(error, err) {});


       });
    },
    delbuttonnewinvoicerayment:function(){
        var thisInstance=this;
        $('#detailView').on('click','.delbuttonnewinvoicerayment',function(){
              var id=$(this).data('id');
            var message='确定要删除吗？';
            var msg={
                'message':message
            };
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){

                $('.newinvoicerayment_tab'+id).remove();

            },function(error, err) {});
        });
    },
    savebuttonnewinvoicerayment:function(){
        var thisInstance=this;
        $('#detailView').on('click','.savebuttonnewinvoicerayment',function(){
            var id=$(this).data('id');
            var message='确定要匹配吗？';
            var msg={
                'message':message
            };
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                var params={};
                var refillapptotal=$('input[name="refillapptotal['+id+']"]').val()
                params.data={
                    "module": "RefillApplication",
                    "action": "BasicAjax",
                    "mode": "doAddNewReffRayment",
                    "raymentid":id,
                    "record":$('#recordid').val(),
                    'refillapptotal':refillapptotal,
                    'rremarks':$('textarea[name="rremarks['+id+']"').val()

                };
                if(0>=refillapptotal){
                    Vtiger_Helper_Js.showMessage({type:'error',text:'可匹配金额大于0!'});
                    return false;
                }
                params.async=false;
                var progressIndicatorElement = jQuery.progressIndicator({
                    'message' : '正在加载...',
                    'position' : 'html',
                    'blockInfo' : {'enabled' : true}
                });
                AppConnector.request(params).then(
                    function(data){
                        progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                        if(!data.result.flag){
                            Vtiger_Helper_Js.showMessage({type:'error',text:data.result.msg});
                            $(this).val(abletotalrecharge);
                        }else{
                            $('.newinvoicerayment_tab'+id).remove();
                            window.location.reload();
                        }

                    },function(){
                });
            },function(error, err) {});
        });
    },
    checkRefundsOrTransfers:function(){
        var thisInstance=this;
        var params={};
        params.data = {
            "module": "RefillApplication",
            "action": "BasicAjax",
            "mode": "checkRefundsOrTransfers",
            "data": $('#refundsTransfers').serializeFormData(),
            "record":$('#recordid').val(),
            "updaterefillprayment":thisInstance.formDataArray('updaterefillprayment'),
            "refundamount":thisInstance.formDataArray('refundamount'),
            "repaymenttotal":thisInstance.formDataArray('repaymenttotal'),
            "updaterepayment":thisInstance.formDataArray('updaterepayment'),
        };
        params.async=false;
        var flag=true;
        AppConnector.request(params).then(
            function(data){
                if(data.result.flag){
                   flag=false;
                }else{
                    Vtiger_Helper_Js.showMessage({type:'error',text:data.result.msg});
                    flag=true
                }
            },
            function (error) {
            });
        return flag;
    },
	registerEvents: function(){
		this._super();
		UE.registerUI('closescreen', function(editor, uiName) {
			editor.registerCommand(uiName, {
				execCommand: function() {}
			});
			var btn = new UE.ui.Button({
				name: uiName,
				title: '关闭',
				cssRules: 'background-position: -100px -20px;',
				onclick:function () {
					editor.ui.setFullScreen(false);
					editor.setHide();
				}
			});
			return btn;
		});
		window.UEDITOR_CONFIG.toolbars= [['closescreen']];
		//window.UEDITOR_CONFIG.readonly =true;
		window.UEDITOR_CONFIG.isShow =false;
		var ckEditorInstance = new Vtiger_CkEditor_Js();
		var classes = $('.productnote');
		$.each(classes,function(n,ids){
			ckEditorInstance.loadCkEditor($(this).attr('id'));
		})
		//this.bindStagesubmit();
        //this.registerDataEvents();
		this.bindSalesorderProjectTasksrel();
        this.refundsOrTransfers();
        this.numberCalc();
        this.addreffrayment();
        this.refillapptotalCalc();
        this.delbuttonnewinvoicerayment();
        this.savebuttonnewinvoicerayment();
        this.setrefund();
        this.doRevokeRelation();
        this.showInlineTableSubmit();
        this.activeChange();


		$('.showproduct').click(function(){
			var id=$(this).data('id');
			UE.getEditor(id).ui.setFullScreen(true);
		})
		
		$('.editproduct').click(function(){
			//alert($('#recordId').val());
			
			var progressIndicatorElement = jQuery.progressIndicator({ 'message' : '产品信息加载中...','blockInfo':{'enabled':true }});
			var id=$(this).data('id'),urlParams = 'module=RefillApplication&view=ListAjax&mode=edit&relate=product';
			
			//AppConnector.request(params).then( 
			var params = {'type' : 'GET','dataType': 'json','data' : urlParams+'&productid='+id+'&record='+$('#recordId').val()};
		
		AppConnector.request(params).then(
				function(data){
					if(data.success){
						
						progressIndicatorElement.progressIndicator({ 'mode' : 'hide'});
						if(!data.products[id]['isEditForm']){
							Vtiger_Helper_Js.showMessage({type:'error',text:'当前产品信息不支持审核中编辑'});
							return;
						}
						var info=data.products[id];
						var hidden='<input type="hidden" name="record" value="'+$('#recordId').val()+'"><input type="hidden" name="module" value="SalesOrder"><input  type="hidden" name="action" value="SaveAjax"><input type="hidden" name="tpl['+id+']" value="'+info["product_tplid"]+'"><input type="hidden" name="productids[]" value="'+id+'" >';
						var msg={title:'编辑 '+info['productname'],
						message:'<form class="form-horizontal" id="productEdit" name="productEdit" method="post" action="index.php">'+hidden+info["productform"]+'</form>','width':"860px",form:"productedit"};
						Vtiger_Helper_Js.showPubDialogBox(msg).then(function(e){
							var actionParams = {"type":"POST","url":'index.php',"dataType":"json","data" : $('#productEdit').serialize()},progressIndicatorElement = jQuery.progressIndicator({'message' :'信息正在提交...' ,'blockInfo' : {'enabled' : true}});
							AppConnector.request(actionParams).then(
								function(data){
									 
									if(data.success){
										progressIndicatorElement.progressIndicator({ 'mode' : 'hide'});
										window.location.reload();	
									}
								}
			
							);
						})	
					}
				})
	
		})
		//好像没有用
		$('.widgetContainer_workflows').on("click",'#remarkbutton',function(){$('#remarkdiv').toggle("fast");});
		$('.details').on("click",'#realremarkbutton',function(){
			var remark=$('#remarkvalue');
			if(remark.val()==''){
				remark.focus();
				return false;	
			}
			var name=$('#stagerecordname').val();
			var msg={'message':"是否要给工单阶段<"+name+">添加备注？"};
			Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
				var params={};
				params['record'] = $('#recordid').val();//工单id
				params['isrejectid'] = $('#backstagerecordeid').val();
				params['isbackname'] = $('#backstagerecordname').val();
				params['reject']=$('#remarkvalue').val();
				params['action'] = 'SaveAjax';
				params['module'] = 'SalesorderWorkflowStages';
				params['mode'] = 'submitremark';
				params['src_module'] = app.getModuleName();
				var d={};
				d.data=params;
				AppConnector.request(d).then(
						function(data){
							if(data.success==true){
								var widgetContainer = $(".widgetContainer_workflows");
								var urlParams = widgetContainer.attr('data-url');
								params = {
									'type' : 'GET',
									'dataType': 'html',
									'data' : urlParams
								};
								widgetContainer.progressIndicator({});
								AppConnector.request(params).then(	
								function(data){
										widgetContainer.progressIndicator({'mode': 'hide'});
										widgetContainer.html(data);
										Vtiger_Helper_Js.showMessage({type:'success',text:'备注添加成功'});
									},
									function(){}
								);
							}else{
								Vtiger_Helper_Js.showMessage({type:'error',text:'备注添加失败,原因'+data.error.message});
							}
						},function(){}
				);
			});
		})
	},
    setrefund:function(){
	    $('#RefillApplication_detailView_basicAction_LBL_REFUND').click(function(){
            var msg={'message':"确定要退款？"};
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                var params={};
                params['record'] = $('#recordid').val();//工单id
                params['action'] = 'BasicAjax';
                params['module'] = 'RefillApplication';
                params['mode'] = 'submitRefund';
                AppConnector.request(params).then(
                    function(data){
                        if(data.success==true){
                            var widgetContainer = $(".widgetContainer_workflows");
                            var urlParams = widgetContainer.attr('data-url');
                            params = {
                                'type' : 'GET',
                                'dataType': 'html',
                                'data' : urlParams
                            };
                            widgetContainer.progressIndicator({});
                            AppConnector.request(params).then(
                                function(data){
                                    widgetContainer.progressIndicator({'mode': 'hide'});
                                    widgetContainer.html(data);
                                    Vtiger_Helper_Js.showMessage({type:'success',text:'备注添加成功'});
                                },
                                function(){}
                            );
                        }else{
                            Vtiger_Helper_Js.showMessage({type:'error',text:'备注添加失败,原因'+data.error.message});
                        }
                    },function(){}
                );
            });
        });
	    $('#RefillApplication_detailView_basicAction_LBL_ISBACKWASH').on("click",function(){
            var msg={'message':"确定要生成红冲,退款流程？"};
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                var params={};
                params['record'] = $('#recordid').val();//工单id
                params['action'] = 'BasicAjax';
                params['module'] = 'RefillApplication';
                params['mode'] = 'dorefundsOrTransfersconfirm';
                AppConnector.request(params).then(
                    function(data){
                        window.location.reload();
                    },function(){}
                );
            });
        });
    },
    showInlineTableSubmit:function(){
        var thisInstance=this; 
        $('[name=taxation]').val(0);//税费
        $('[name=factorage]').val(0);//代理费
        $('[name=activationfee]').val(0);//开户费
//        var old_mrefundamount = $('[name=mrefundamount]').val();//退款金额
       
        $('body').on('keyup blur','input[name="mprestoreadrate"]',function(){
              var factorage_tmp = $('[name=factorage_tmp]').val();
            var activationfee_tmp = $('[name=activationfee_tmp]').val();
            var taxation_tmp = $('[name=taxation_tmp]').val();
            
            var old_taxation = thisInstance.isNotANumber($('[name=taxation]').val()) ? $('[name=taxation]').val() : $('[name=taxation]').val('0');//税费
            var old_factorage = thisInstance.isNotANumber($('[name=factorage]').val()) ? $('[name=factorage]').val() : $('[name=factorage]').val('0');//代理费
            var old_activationfee = thisInstance.isNotANumber($('[name=activationfee]').val()) ? $('[name=activationfee]').val() : $('[name=activationfee]').val('0');//开户费
            
            var taxation_data = $('[name=taxation]').data("value");
            //计算 
            if( (taxation_data ) < old_taxation){
                old_taxation = taxation_tmp;
                
                  $('input[name="taxation"]').focus();
                $('input[name="taxation"]').attr('data-content','<font color="red">填写数字不能大于退款额</font>');
                $('input[name="taxation"]').popover("show");
                $('.popover-content').css({"color":"red","fontSize":"12px"});
                $('.popover').css('z-index',1000010);
                setTimeout("$('input[name=\"taxation\"]').popover('destroy')",200);
                return false;
                
            }
          //            var mrefundamount = $('#high_refund').val();

                var mrechargeamount =$('[name=mrechargeamount]').val();
               mun = Number((parseFloat(mrechargeamount) + parseFloat(old_taxation) + parseFloat(old_factorage) + parseFloat(old_activationfee)).toFixed(2));  
               if(mun<0){
                	Vtiger_Helper_Js.showMessage({type:'error',text:'请填写正确数字'});
            }else{
                   $('[name=mrefundamount]').val(mun);
                   var trefundamount=$('input[name="trefundamount"]').val(mun);
            }
            return false;
        });
        
        $('body').on('keyup blur','input[name=taxation]',function(){
            var factorage_tmp = $('[name=factorage_tmp]').val()?$('[name=factorage_tmp]').val():0;
            var activationfee_tmp = $('[name=activationfee_tmp]').val()?$('[name=activationfee_tmp]').val():0;
            var taxation_tmp = $('[name=taxation_tmp]').val()?$('[name=taxation_tmp]').val():0;
            
            var old_taxation = thisInstance.isNotANumber($('[name=taxation]').val()) ? $('[name=taxation]').val() : $('[name=taxation]').val('0');//税费
            var old_factorage = thisInstance.isNotANumber($('[name=factorage]').val()) ? $('[name=factorage]').val() : $('[name=factorage]').val('0');//代理费
            var old_activationfee = thisInstance.isNotANumber($('[name=activationfee]').val()) ? $('[name=activationfee]').val() : $('[name=activationfee]').val('0');//开户费
            
            var taxation_data = $('[name=taxation]').data("value");
            if(!thisInstance.checkRate(old_taxation)){
                $('[name=taxation]').val('0');
            }
            //计算 
            if( (taxation_data ) < old_taxation){
                old_taxation = taxation_tmp;
                
                  $('input[name="taxation"]').focus();
                $('input[name="taxation"]').attr('data-content','<font color="red">填写数字不能大于退款额</font>');
                $('input[name="taxation"]').popover("show");
                $('.popover-content').css({"color":"red","fontSize":"12px"});
                $('.popover').css('z-index',1000010);
                setTimeout("$('input[name=\"taxation\"]').popover('destroy')",1000);
                return false;
                
//                $('[name=taxation]').val(old_taxation);
            }
          //            var mrefundamount = $('#high_refund').val();

                var mrechargeamount =$('[name=mrechargeamount]').val();
                       mun = Number((parseFloat(mrechargeamount) + parseFloat(old_taxation) + parseFloat(old_factorage) + parseFloat(old_activationfee)).toFixed(2));  
               if(mun<0){
                	Vtiger_Helper_Js.showMessage({type:'error',text:'请填写正确数字'});
            }else{
                   $('[name=mrefundamount]').val(mun);
                   var trefundamount=$('input[name="trefundamount"]').val(mun);
            }
            return false;
        });
        $('body').on('keyup blur','input[name=factorage]',function(){
            var factorage_tmp = $('[name=factorage_tmp]').val()?$('[name=factorage_tmp]').val():0;
            var activationfee_tmp = $('[name=activationfee_tmp]').val()?$('[name=activationfee_tmp]').val():0;
            var taxation_tmp = $('[name=taxation_tmp]').val()?$('[name=taxation_tmp]').val():0;
            
            var old_taxation = thisInstance.isNotANumber($('[name=taxation]').val()) ? $('[name=taxation]').val() : $('[name=taxation]').val('');//税费
            var old_factorage = thisInstance.isNotANumber($('[name=factorage]').val()) ? $('[name=factorage]').val() : $('[name=factorage]').val('');//代理费
            var old_activationfee = thisInstance.isNotANumber($('[name=activationfee]').val()) ? $('[name=activationfee]').val() : $('[name=activationfee]').val('');//开户费
            
            var factorage_data = $('[name=factorage]').data("value");
            //计算 
            console.log('代理商服务费---'+(factorage_data-factorage_tmp));
            console.log('代理商服务费2---'+old_factorage);
            if(!thisInstance.checkRate(old_taxation)){
                $('[name=taxation]').val('0');
            }
            if((factorage_data) < old_factorage){
                old_factorage = factorage_tmp;
                $('input[name="factorage"]').focus();
                $('input[name="factorage"]').attr('data-content','<font color="red">填写数字不能大于退款额</font>');
                $('input[name="factorage"]').popover("show");
                $('.popover-content').css({"color":"red","fontSize":"12px"});
                $('.popover').css('z-index',1000010);
                setTimeout("$('input[name=\"factorage\"]').popover('destroy')",1000);
                return false;
            }
         
//            var mrefundamount = $('#high_refund').val();

                var mrechargeamount =$('[name=mrechargeamount]').val();
                    mun = Number((parseFloat(mrechargeamount) + parseFloat(old_taxation) + parseFloat(old_factorage) + parseFloat(old_activationfee)).toFixed(2));  
//                 alert(mun);
            if(mun<0){
                Vtiger_Helper_Js.showMessage({type:'error',text:'请填写正确数字'});
            }else{
                $('[name=mrefundamount]').val(mun);
                var trefundamount=$('input[name="trefundamount"]').val(mun);
            }
            return false;
          
        });  
        $('body').on('keyup blur','input[name=activationfee]',function(){
            var factorage_tmp = $('[name=factorage_tmp]').val()?$('[name=factorage_tmp]').val():0;
            var activationfee_tmp = $('[name=activationfee_tmp]').val()?$('[name=activationfee_tmp]').val():0;
            var taxation_tmp = $('[name=taxation_tmp]').val()?$('[name=taxation_tmp]').val():0;
            var old_taxation = thisInstance.isNotANumber($('[name=taxation]').val()) ? $('[name=taxation]').val() : $('[name=taxation]').val('0');//税费
            var old_factorage = thisInstance.isNotANumber($('[name=factorage]').val()) ? $('[name=factorage]').val() : $('[name=factorage]').val('0');//代理费
            var old_activationfee = thisInstance.isNotANumber($('[name=activationfee]').val()) ? $('[name=activationfee]').val() : $('[name=activationfee]').val('0');//开户费
//            var mrefundamount = $('#high_refund').val();
            
             var activationfee_data = $('[name=activationfee]').data("value");
            //计算 
            console.log('开户费---'+(activationfee_data-activationfee_tmp));
            console.log('开户费2---'+old_activationfee);
             if(!thisInstance.checkRate(old_activationfee)){
                $('[name=activationfee]').val('0');
            }
            if((activationfee_data) < old_activationfee){
                old_factorage = activationfee_tmp;
                
                $('input[name="activationfee"]').focus();
                $('input[name="activationfee"]').attr('data-content','<font color="red">填写数字不能大于退款额</font>');
                $('input[name="activationfee"]').popover("show");
                $('.popover-content').css({"color":"red","fontSize":"12px"});
                $('.popover').css('z-index',1000010);
                setTimeout("$('input[name=\"activationfee\"]').popover('destroy')",1000);
                return false;
                
//                $('[name=activationfee]').val(old_factorage);
            }
         //            var mrefundamount = $('#high_refund').val();

                var mrechargeamount =$('[name=mrechargeamount]').val();
                   mun = Number((parseFloat(mrechargeamount) + parseFloat(old_taxation) + parseFloat(old_factorage) + parseFloat(old_activationfee)).toFixed(2));  
            if(mun<0){
                	Vtiger_Helper_Js.showMessage({type:'error',text:'请填写正确数字'});
            }else{
                   $('[name=mrefundamount]').val(mun);
                   var trefundamount=$('input[name="trefundamount"]').val(mun);
            }
         
            return false;
          
        });
    },
     isNotANumber:function(inputData) {
        if (parseFloat(inputData).toString() == "NaN") {
            return false;
        } else {
            return true;
        }
    },
    checkRate:function(nubmer) {
        var re = /^[0-9]+.?[0-9]*$/; //判断字符串是否为数字 //判断正整数 /^[1-9]+[0-9]*]*$/ 

        if (!re.test(nubmer)) {
            return false;
        }else{
            return true;
        }
    },
    //变更申请人
    activeChange: function () {
        $('body').on('click', '#RefillApplication_detailView_basicAction_变更申请人', function () {
            var progressIndicatorElement = jQuery.progressIndicator({
                'message': '正在加载...',
                'position': 'html',
                'blockInfo': {'enabled': true}
            });
            var show_data = '';
            var recordId = $('#recordId').val();
            var postData = {
                "module": 'SalesOrder',
                "action": "BasicAjax",
                'mode': 'changesApplicant',
                "record": recordId,
            };

            AppConnector.request(postData).then(

                function (data) {
                    progressIndicatorElement.progressIndicator({'mode': 'hide'});
                    if (!data.result.success) {
                        Vtiger_Helper_Js.showMessage({type: 'error', text: '没有操作权限，如需变更,请找部门负责人操作'});
                    } else {
                        var message = '更换申请人？';
                        var msg = {
                            'message': message,
                            'width': '400px'
                        };
                        Vtiger_Helper_Js.showConfirmationBox(msg).then(function (e) {
                            is_change();

                        }, function (error, err) {});
                        $('#user_name_id').empty();
                        $.each(data.result.data, function (key, value) {
                            if(value.brevitycode==null){
                                value.brevitycode='';
                            }
                            show_data += "<option value=" + value.id + ">" +"("+value.brevitycode+")"+ value.last_name + "[" + value.department + "]"+ "</option>";
                        });
                        var strr = '<form name="insertcomment" id="formcomment">\
                                        <div id="insertcomment" style="height: 300px;overflow: auto">\
                                        <table class="table table-bordered blockContainer Duplicates showInlineTable  detailview-table" id="comments1"><tbody>' +
                            '<tr><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><select name="changesApplicant" class="chzn-select"  id="changesApplicant">' + show_data + '</select></span></div></td></tr>' +
                            '</tbody></table>' +
                            '</div></form>';
                        $('.modal-content .modal-body').append(strr);
                        $('.chzn-select').chosen();
                    }
                },
                function (error, err) {
                }
            );

        });
        function is_change() {
            var recordId = $('#recordId').val();
            var smcreatorid = $("#changesApplicant").val();
            var postData = {
                "module": 'SalesOrder',
                "action": "BasicAjax",
                'mode': 'changesUpdate',
                "record": recordId,
                'smcreatorid': smcreatorid,
            };
            AppConnector.request(postData).then(
                function (data) {
                    Vtiger_Helper_Js.showMessage({type: 'info', text: '更新成功'});
                    sleep(200);
                    window.location.reload();
                },
                function (error, err) {
                }
            );
        }


        function sleep(numberMillis) {
            var now = new Date();
            var exitTime = now.getTime() + numberMillis;
            while (true) {
                now = new Date();
                if (now.getTime() > exitTime)
                    return;
            }
        }
    },
    bindStagesubmit:function(){
        $('.details').on('click','.stagesubmit',function(){
            var name=$('#stagerecordname').val();

            var msg={
                'message':"确定要审核工单阶段"+name+"?",
            };
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){

                var params={};
                params['record'] = $('#recordid').val();
                params['stagerecordid'] = $('#stagerecordid').val();
                params['action'] = 'SaveAjax';
                params['module'] = 'SalesorderWorkflowStages';
                params['mode'] = 'updateSalseorderWorkflowStages';
                params['src_module'] = app.getModuleName();
                params['checkname'] = $('#backstagerecordname').val();
                params['customer']=$("#customer").val()==undefined?0:$("#customer").val();
                params['customername']=$("#customer").find("option:selected").text()==undefined?'':$("#customer").find("option:selected").text();
                //ie9下post请求是失败的，如果get可以的请修改

                var d={};
                d.data=params;
                d.type = 'GET';
                var progressIndicatorElement = jQuery.progressIndicator({
                    'message' : '亲,正在拼命处理,请耐心等待哟',
                    'position' : 'html',
                    'blockInfo' : {'enabled' : true}
                });

                AppConnector.request(d).then(
                        function(data){
                            if(data.success==true){
                                //刷新当前的挂件，在这里本来可以使用父类的方法，但是不生效，只能重新写了
                                var widgetContainer = $(".widgetContainer_workflows");
                                //
                                var urlParams = widgetContainer.attr('data-url');
                                params = {
                                    'type' : 'GET',
                                    'dataType': 'html',
                                    'data' : urlParams
                                };
                                widgetContainer.progressIndicator({});
                                Vtiger_Helper_Js.showMessage({type:'success',text:'审核成功'});
                                sleep(200);
                                window.location.reload();
                                // AppConnector.request(params).then(

                                // function(data){
                                //         widgetContainer.progressIndicator({'mode': 'hide'});
                                //         widgetContainer.html(data);
                                //         Vtiger_Helper_Js.showMessage({type:'success',text:'审核成功'});
                                //     },
                                //     function(){}
                                // );
                            }else{
                                Vtiger_Helper_Js.showMessage({type:'error',text:'审核失败,原因'+data.error.message});
                            }
                            progressIndicatorElement.progressIndicator({
                                'mode' : 'hide'
                            });
                        },function(){}
                );
            },function(error, err) {});
        });

        function sleep(numberMillis) {
            var now = new Date();
            var exitTime = now.getTime() + numberMillis;
            while (true) {
                now = new Date();
                if (now.getTime() > exitTime)
                    return;
            }
        }
    },
    bindrejectall:function(){
        $('.details').on("click",'#realstagereset',function(){
            //steel加入打回为空检测//////
            var rejectreason=$('#rejectreason');
            if(rejectreason.val()==''){
                Vtiger_Helper_Js.showMessage({type:'error',text:'打回原因必须填写'});
                rejectreason.focus();
                return ;
            }
            ///////////////////////////
            var name=$('#stagerecordname').val();
            var msg={
                'message':"确定要将工单阶段"+name+"打回？",
            };

            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                var params={};
                params['record'] = $('#recordid').val();                  //工单id
                params['isrejectid'] = $('#stagerecordid').val();
                params['isbackname'] = $('#stagerecordname').val();
                params['reject']=$('#rejectreason').val();
                params['action'] = 'SaveAjax';
                params['module'] = 'SalesorderWorkflowStages';
                params['mode'] = 'backall';
                params['src_module'] = app.getModuleName();
                params['actionnode'] = 0;
                backfun(params);
        },function(error, err) {});
        });
        $('.details').on('click','.resetaction',function(){
            var msg={
                'message':"确定要激活节点？",
            };
            var isrejectid =  $(this).data('id');
            var isbackname = $(this).data('name');
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                var params={};
                params['record'] = $('#recordid').val();                  //工单id
                params['isrejectid'] = isrejectid;
                params['isbackname'] = isbackname;
                params['reject'] = '节点被重新激活';
                params['action'] = 'SaveAjax';
                params['module'] = 'SalesorderWorkflowStages';
                params['mode'] = 'backall';
                params['src_module'] = app.getModuleName();
                params['actionnode'] = 1;
                backfun(params);
            },function(error, err) {});
        });
        $('.details').on('click','.modulestatus',function(){
            var msg={
                'message':"是否修改当前模块状态",
            };
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){

            },function(error, err) {});
        });
        function backfun(params){
            var d={};
            d.data=params;
            d.type = 'GET';
            var progressIndicatorElement = jQuery.progressIndicator({
                'message' : '亲,正在拼命处理,请耐心等待哟',
                'position' : 'html',
                'blockInfo' : {'enabled' : true}
            });
            AppConnector.request(d).then(
                function(data){
                    progressIndicatorElement.progressIndicator({
                        'mode' : 'hide'
                    });
                    if(data.success==true){
                        var widgetContainer = $(".widgetContainer_workflows");

                        var urlParams = widgetContainer.attr('data-url');
                        params = {
                            'type' : 'GET',
                            'dataType': 'html',
                            'data' : urlParams
                        };
                        widgetContainer.progressIndicator({});
                        Vtiger_Helper_Js.showMessage({type:'success',text:'操作成功'});
                        sleep(200);
                        window.location.reload();
                        // AppConnector.request(params).then(
                        //     function(data){
                        //         widgetContainer.progressIndicator({'mode': 'hide'});
                        //         widgetContainer.html(data);
                        //         Vtiger_Helper_Js.showMessage({type:'success',text:'操作成功'});
                        //         //隐藏回款明细 gaocl add 2018/05/16
                        //         if(app.getModuleName() == 'SalesOrder'){
                        //             $(".salesorderrayment_title_tab").hide();
                        //             $(".salesorderrayment_tab").hide();
                        //         }
                        //     },
                        //     function(){}
                        // );
                    }else{
                        Vtiger_Helper_Js.showMessage({type:'error',text:'操作失败,原因'+data.error.message});
                    }
                },function(){}
            );
        }

        function sleep(numberMillis) {
            var now = new Date();
            var exitTime = now.getTime() + numberMillis;
            while (true) {
                now = new Date();
                if (now.getTime() > exitTime)
                    return;
            }
        }
    }
})
