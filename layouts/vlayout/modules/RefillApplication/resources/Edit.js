/*+***********************
 * 退款申请流程
 ***********************/

Vtiger_Edit_Js("RefillApplication_Edit_Js",{},{
	rowSequenceHolder : false,
    idtopplatform:{},
    idproductid:{},
    isgetReceivedPayments:new Array(),
    seletedIndexValue:0,
    seletedValue:0,
    topplatformid:[],
	registerReferenceSelectionEvent : function(container) {
		this._super(container);
		var thisInstance = this;


		// 客户变更事件,只有退币转充中选择外采媒体有
		$('input[name="accountid"]', container).on(Vtiger_Edit_Js.referenceSelectionEvent,function (e,data){
			$('input[name=servicecontractsid_display]').val('');
            $('input[name=servicecontractsid]').val('');
            if($('input[name="rechargesource"]').val()=='COINRETURN'){
                $.each($('.Duplicates'),function(key,value){
                    var valueNum=$(value).data('num');
                    if(valueNum!=1){
                        //只要不是员原本的通通删掉，只留
                        $(value).remove();
                    }else{
                        $('input[name="did"]').val('');
                        $('input[name="did_display"]').val('');
                        $('input[name="mid[1]"]').val('');
                        $('input[name="mid_display[1]"]').val('');
                        $('input[name="productid_display"]').val('');
                        $('input[name="mproductid_display[1]"]').val('');
                        $('input[name="accountzh"]').val('');
                        $('input[name="maccountzh[1]"]').val('');
                        $('input[name="discount"]').val('');
                        $('input[name="mdiscount[1]"]').val('');
                        $('input[name="cashtransfer"]').val('');
                        $('input[name="mcashtransfer[1]"]').val('');
                        $('input[name="accounttransfer"]').val('');
                        $('input[name="maccounttransfer[1]"]').val('');
                    }
                });
            }
            //2021.2.4修改账户不再自动变更id
            // var accountid=$('input[name="accountid"]').val();
            // thisInstance.getAccountPlatform(accountid,1);
		});
        $(container).on(Vtiger_Edit_Js.referenceSelectionEvent,'input[name^="mmservicecontractsid\["],input[name="mservicecontractsid"]',function (e,data){
            var num=$(e.target).data('num');
            var record=data.record;
            var mservicecontractsidField=num>0?'mmservicecontractsid['+num+']':'mservicecontractsid';
            var mservicecontractsidDisplayField=num>0?'mmservicecontractsid[display'+num+']_display':'mservicecontractsid_display';
            var maccountidField=num>0?'mmaccountid['+num+']':'maccountid';
            var maccountidDisplayField=num>0?'mmaccountid[display'+num+']_display':'maccountid_display';
            var discountField=num>0?'mdiscount['+num+']':'discount';
            var cashincreaseField=num>0?'mcashincrease['+num+']':'cashincrease';
            var taxrefundField=num>0?'mtaxrefund['+num+']':'taxrefund';
            var cashconsumptionField=num>0?'mcashconsumption['+num+']':'cashconsumption';
            var grantquarterField=num>0?'mgrantquarter['+num+']':'grantquarter';
            var mstatusField=num>0?'mmstatus['+num+']':'mstatus';
            $('input[name="'+maccountidField+'"],\
                input[name="'+maccountidDisplayField+'"],\
                input[name="'+discountField+'"],\
                input[name="'+cashincreaseField+'"],\
                input[name="'+taxrefundField+'"],\
                input[name="'+cashconsumptionField+'"],\
                input[name="'+grantquarterField+'"],\
                input[name="'+mstatusField+'"]').val('');
            var servicecontractsid=$('input[name="mservicecontractsid"]').val();
            $("input[name='"+cashconsumptionField+"']").trigger("keyup");
            if(record==servicecontractsid && servicecontractsid>0 && num>0){
                $('input[name="'+mservicecontractsidField+'"]').val(0);
                $('input[name="'+mservicecontractsidDisplayField+'"]').val('');
                var params = {'text':'合同重复选择！', 'title': ''};
                Vtiger_Helper_Js.showPnotify(params);
                return false;
            }
            var flag=false;
            $.each($('input[name^="mmservicecontractsid\["]'),function(key,value){
                var thisValue=$(value).val();
                var thisNum=$(value).data("num");
                if(record==thisValue && thisValue>0 && thisNum!=num){
                    flag=true;
                    return false
                }
            });
            if(flag){
                $('input[name="'+mservicecontractsidField+'"]').val(0);
                $('input[name="'+mservicecontractsidDisplayField+'"]').val('');
                var params = {'text':'合同重复选择!！', 'title': ''};
                Vtiger_Helper_Js.showPnotify(params);
                return false;
            }
            var progressIndicatorElement = jQuery.progressIndicator({ 'message' : '合同信息加载中...','blockInfo':{'enabled':true }});
            var params= {'module':'RefillApplication','mode':'getaccountinfo','action':'BasicAjax','record':record};
            AppConnector.request(params).then(
                function(data){
                    progressIndicatorElement.progressIndicator({ 'mode' : 'hide'});
                    if(data.success) {
                        var result=data.result[1];
                        if(result.modulestatus=='c_complete' ||(result.modulestatus!='c_cancel' && result.modulestatus!='c_canceling')){
                            $('input[name="'+maccountidField+'"]').val(result.id);
                            $('input[name="'+maccountidDisplayField+'"]').val(result.accountname);
                        }else{
                            $('input[name="'+mservicecontractsidField+'"]').val(0);
                            $('input[name="'+mservicecontractsidDisplayField+'"]').val('');
                            var params = {'text':'合同必须是已签收的状态!！', 'title': ''};
                            Vtiger_Helper_Js.showPnotify(params);
                        }
                    }
            })

        });
         //合同申请变更 目标合同选择
        $('input[name="newcontractsid"]',container).on(Vtiger_Edit_Js.referenceSelectionEvent,function (e,data){
            console.log($('input[name="newcontractsid"]').val());
            var rechargesource = $("input[name='rechargesource']").val();
            if(rechargesource=='contractChanges'){
                var changecontracttype = $("select[name='changecontracttype']").val();
                if(!(changecontracttype=='ServiceContracts'|| changecontracttype=='SupplierContracts')){
                    clearNewcontractsInfo();
                    Vtiger_Helper_Js.showMessage({type: 'error', text: '变更类型不能为空！'});
                    return false;
                }
                if ($('input[name="newcontractsid"]').val()==$('input[name="servicecontractsid"]').val()){
                    clearNewcontractsInfo();
                    Vtiger_Helper_Js.showMessage({type: 'error', text: '原合同和目标合同不能相同！'});
                    return false;
                }
                newcontractschange();
            }
        });
        function clearNewcontractsInfo(){
            $("input[name='newcontractsid']").val('');
            $("#newcontractsid_display").val('');
            $("#newaccountid_display").val('');
            $("select[name='newcustomertype']").val('');
            $("select[name='newiscontracted']").val('');
            $("input[name='newservicesigndate']").val('');
            $("input[name='newcontractamount']").val('');
            $('select[name="newcustomertype"]').trigger("liszt:updated");
            $('select[name="newiscontracted"]').trigger("liszt:updated");
            $('input[name="isautoclose"]').val('');
        }
        function newcontractschange(flag){
            var needContract = $("select[name='changecontracttype']").val();
            var  mode='getaccountinfo';
            if( needContract=='SupplierContracts' ){
                mode='getSupplierAccountInfo';
            }
            var args=flag||0;
            var progressIndicatorElement = jQuery.progressIndicator({ 'message' : '合同信息加载中...','blockInfo':{'enabled':true }});
            var rechargesource = $("input[name='rechargesource']").val();
            var params={};
            params.data= {'module':'RefillApplication','mode':mode,'action':'BasicAjax','rechargesource':rechargesource,'record':$('input[name="newcontractsid"]').val()};
            params.async=false;
            $("#advancesmoney").remove();

            if(args==0){
                $('input[name=newservicesigndate]').val('');
                $('#RefillApplication_editView_fieldName_newiscontracted').prop("checked",false);
                $('.newinvoicerayment_tab ').remove();
            }

            //var record=jQuery('input[name="record"]').val();
            AppConnector.request(params).then(
                function(data){
                    progressIndicatorElement.progressIndicator({ 'mode' : 'hide'});
                    if(data.success){
                        console.log(data);
                        //progressIndicatorElement.progressIndicator({ 'mode' : 'hide'});
                        if(data.success==true){
                            var json=data.result[1];
                            if(args>0){

                            }else{
                                $('input[name=newaccountid_display]').val(json.accountname);
                                $('input[name=newservicesigndate]').val('');
                                $('input[name=newaccountid]').val(json.id);
                                $('input[name="newcontractamount"]').val(json.total);
                                $('input[name="contractamountrecharged"]').val(json.sumrefilltotal);
                                if(json.modulestatus=='c_complete'){
                                    var iscontracted='alreadySigned';
                                }else{
                                    var iscontracted='notSigned';
                                }
                                $('select[name="newiscontracted"]').val(iscontracted);
                                $('select[name="newiscontracted"]').trigger("liszt:updated");
                                if($("select[name='changecontracttype']")!='SupplierContracts'){
                                    $('select[name="newcustomertype"]').val(json.customertype);
                                    $('select[name="newcustomertype"]').trigger("liszt:updated");
                                }
                                $('input[name=newservicesigndate]').val(json.signdate);
                                $('input[name="isautoclose"]').val(json.isautoclose);
                            }
                            //$('input[name=newaccountid_display]').after('<span id="advancesmoney" class="label label-a_exception table_show" title="客户已垫款的总额" dd='+json.id+'>'+json.advancesmoney+'</span>');
                        }
                    }
                },
                function(error){}
            );
        }

        /*var rechargesource = $("input[name='rechargesource']").val();
        if(rechargesource=='contractChanges'){
            var needContract = $("select[name='changecontracttype']").val();
            console.log(needContract);
            // 对 合同变更申请专用 如果 选择变更类型 则 走if  其他的都走else
            if(needContract=='ServiceContracts' || needContract=='SupplierContracts'){
                console.log("what");
                initContractChangesEvents();
            }
        }else{

        }*/
        $('input[name="servicecontractsid"]',container).on(Vtiger_Edit_Js.referenceSelectionEvent,function (e,data){
            if($('input[name="rechargesource"]').val()=='Accounts') {
                thisInstance.loadingClear();
            }
            var rechargesource = $("input[name='rechargesource']").val();
            if(rechargesource=='contractChanges'){
                var servicecontractsid = $("input[name='servicecontractsid']").val();
                var changecontracttype = $("select[name='changecontracttype']").val();
                if(!(changecontracttype=='ServiceContracts'||changecontracttype=='SupplierContracts')){
                    clearServicecontractschange();
                    Vtiger_Helper_Js.showMessage({type: 'error', text: '变更类型不能为空！'});
                    return false;
                }
                if(!$("select[name='oldrechargesource']").val()){
                    clearServicecontractschange();
                    Vtiger_Helper_Js.showMessage({type: 'error', text: '原充值来源没有选择！'});
                    return false;
                }
                if (($('input[name="newcontractsid"]').val() || $('input[name="servicecontractsid"]').val()) && ($('input[name="newcontractsid"]').val()==$('input[name="servicecontractsid"]').val())){
                    clearServicecontractschange();
                    Vtiger_Helper_Js.showMessage({type: 'error', text: '原合同和目标合同不能相同！'});
                    return false;
                }
                // 原合同信息获取
                supplierContractsChange();
            }else{
                servicecontractschange();
            }
        });

        function clearServicecontractschange(){
            $("input[name='servicecontractsid']").val('');
            $("#servicecontractsid_display").val('');
            $("#accountid_display").val('');
            $("select[name='customertype']").val('');
            $("select[name='iscontracted']").val('');
            $("input[name='servicesigndate']").val('');
            $("input[name='contractamount']").val('');
            $('select[name="customertype"]').trigger("liszt:updated");
            $('select[name="iscontracted"]').trigger("liszt:updated");
        }
        //加载合同变更时 供应商 或者  合同产品信息（用于目标合同）
        function supplierContractsChange(flag){
            var needContract = $("select[name='changecontracttype']").val();
            var  mode='getaccountinfo';
            if( needContract=='SupplierContracts'){
                mode='getSupplierAccountInfo';
            }
            var args=flag||0;
            var progressIndicatorElement = jQuery.progressIndicator({ 'message' : '合同信息加载中...','blockInfo':{'enabled':true }});
            var rechargesource = $("input[name='rechargesource']").val();
            var params={};
            params.data= {'module':'RefillApplication','mode':mode,'action':'BasicAjax','oldrechargesource':$("select[name='oldrechargesource']").val(),'rechargesource':rechargesource,'record':$('input[name="servicecontractsid"]').val()};
            params.async=false;
            $("#advancesmoney").remove();
            if($('input[name="rechargesource"]').val()=='PreRecharge'){
                thisInstance.vendoridInstance(flag);
                return false;
            }
            if(args==0){
                $('input[name=servicesigndate]').val('');
                $('#RefillApplication_editView_fieldName_iscontracted').prop("checked",false);
                $('.newinvoicerayment_tab ').remove();
            }
            //var record=jQuery('input[name="record"]').val();
            AppConnector.request(params).then(
                function(data){
                    progressIndicatorElement.progressIndicator({ 'mode' : 'hide'});
                    console.log(data);
                    if(data.success){
                        console.log(data);
                        //progressIndicatorElement.progressIndicator({ 'mode' : 'hide'});
                        if(data.success ==  true){
                            var result=data.result[1];
                            if(args>0){

                            }else{
                                $('input[name=accountid_display]').val(result.accountname);
                                $('input[name=servicesigndate]').val('');
                                $('input[name=accountid]').val(result.id);
                                $('input[name="contractamount"]').val(result.total);
                                if(result.modulestatus=='c_complete'){
                                    var iscontracted='alreadySigned';
                                }else{
                                    var iscontracted='notSigned';
                                }
                                $('select[name="iscontracted"]').val(iscontracted);
                                $('select[name="iscontracted"]').trigger("liszt:updated");
                                if($("select[name='changecontracttype']")!='SupplierContracts'){
                                    $('select[name="customertype"]').val(result.customertype);
                                    $('select[name="customertype"]').trigger("liszt:updated");
                                }
                                $('input[name=servicesigndate]').val(result.signdate);
                                $(".needToRemove").remove();
                                $("#refillApplicationList").after(result.strHtml);
                            }
                        }
                    }
                },
                function(error){
                    console.log(error);
                }
            );
        }

		//加载合同和产品信息
		function servicecontractschange(flag){
		    var args=flag||0;
			var progressIndicatorElement = jQuery.progressIndicator({ 'message' : '合同信息加载中...','blockInfo':{'enabled':true }});
			var params={};
            params.data= {'module':'RefillApplication','mode':'getaccountinfo','action':'BasicAjax','record':$('input[name="servicecontractsid"]').val()};
            params.async=false;
			$("#advancesmoney").remove();
			if($('input[name="rechargesource"]').val()=='PreRecharge'){
			    thisInstance.vendoridInstance(flag);
			    return false;
            }
            if(args==0){
                $('input[name=servicesigndate]').val('');
                $('#RefillApplication_editView_fieldName_iscontracted').prop("checked",false);
                $('.newinvoicerayment_tab ').remove();
            }

            //var record=jQuery('input[name="record"]').val();
			AppConnector.request(params).then(
				function(data){
                    progressIndicatorElement.progressIndicator({ 'mode' : 'hide'});
					if(data.success){
						//progressIndicatorElement.progressIndicator({ 'mode' : 'hide'});
                        if(data.success ==  true){
                            var json=data.result[1];
                            if(args>0){

                            }else{
                                $('input[name=accountid_display]').val(json.accountname);
                                $('input[name=servicesigndate]').val('');
                                $('input[name=accountid]').val(json.id);
                                $('input[name="contractamount"]').val(json.total);
                                $('input[name="usecontractamount"]').val(json.sumrefilltotal);
                                if(json.modulestatus=='c_complete'){
                                    var iscontracted='alreadySigned';
                                }else{
                                    var iscontracted='notSigned';
                                }
                                $('select[name="iscontracted"]').val(iscontracted);
                                $('select[name="iscontracted"]').trigger("liszt:updated");
                                $('select[name="customertype"]').val(json.customertype);
                                $('select[name="customertype"]').trigger("liszt:updated");

                                $('input[name=servicesigndate]').val(json.signdate);
                            }
                            $('input[name=accountid_display]').after('<span id="advancesmoney" class="label label-a_exception table_show" title="客户已垫款的总额" dd='+json.id+'>'+json.advancesmoney+'</span>');
                            $('input[name="usecontractamount"]').val(json.sumrefilltotal);
                            var rechargesource=$('input[name="rechargesource"]').val();
                            if(rechargesource=='Accounts' ||
                                rechargesource=='Vendors' ||
                                rechargesource=='NonMediaExtraction'){
                                thisInstance.getReceivedPayments();
                            }

                            if(rechargesource=='Accounts'){
                                // thisInstance.getAccountPlatform(json.id);
                            }
                            if(rechargesource=='COINRETURN'){
                                var conversiontype=$('select[name="conversiontype"]').val();
                                if(conversiontype=='AccountPlatform'){
                                    // thisInstance.getAccountPlatform(json.id);
                                }else if(conversiontype=='ProductProvider'){
                                    thisInstance.vendoridInstance();
                                }
                            }

                        }
					}
				},
				function(error){}
			);
		}
        $('input[name="vendorid"]', container).on(Vtiger_Edit_Js.referenceSelectionEvent,function (e,data){
            var rechargesource=$('input[name="rechargesource"]').val();
            if(rechargesource!='PACKVENDORS'){
                if(rechargesource=='COINRETURN'){
                    // thisInstance.vendoridInstance(1);
                    thisInstance.vendoridInstanceByRecord();
                    return;
                }
                thisInstance.loadingClear();
                if(rechargesource=='Vendors'){
                    thisInstance.vendoridInstanceByRecord();
                }else{
                    thisInstance.vendoridInstance(1);
                }
            }else{
                thisInstance.getVendorsList();
            }

        });
		//工单加载
        $('input[name="salesorderid"]', container).on(Vtiger_Edit_Js.referenceSelectionEvent,function (e,data){
            thisInstance.technicalProcurement();
        });

		//编辑工单
		var record=jQuery('input[name="record"]').val();
		if(record>0){
			//按工作流显示数据?
            if($('input[name="rechargesource"]').val()=='Vendors' || $('input[name="rechargesource"]').val()=='Accounts'){
                servicecontractschange(1);
                var productid_display=$('input[name="productid_display"]').val();
                thisInstance.googleReadonlyField(productid_display,0);//谷歌平台切换(
                var mproductid_display=$('input[name^="mproductid_display["]');
                $.each(mproductid_display,function(key,value){
                    var cid=$(value).attr('data-cid');
                    var productname=$(value).val();
                    thisInstance.googleReadonlyField(productname,cid);//谷歌平台切换
                });
            }
            thisInstance.aggregateSummary();
            if($('input[name="rechargesource"]').val()=='Vendors'){
                //2021/2/4去除
                // thisInstance.vendoridInstance();
            }
            if($('input[name="rechargesource"]').val()=='TECHPROCUREMENT'){
                thisInstance.technicalProcurement();
                thisInstance.vendoridInstance();
            }
            if($('input[name="rechargesource"]').val()=='OtherProcurement'){
                thisInstance.vendoridInstance();
            }
            if($('input[name="rechargesource"]').val()=='NonMediaExtraction'){
                servicecontractschange(1);
                thisInstance.vendoridInstance();
            }
            if($('input[name="rechargesource"]').val()=='PACKVENDORS'){
                thisInstance.getVendorsList();
            }
            if($('input[name="rechargesource"]').val()=='COINRETURN'){
                var accountid=$('input[name="accountid"]').val();
                var conversiontype=$('select[name="conversiontype"]').val()
                if(conversiontype=='AccountPlatform'){
                    //2021/2/4去除
                    // thisInstance.getAccountPlatform(accountid);
                }else if(conversiontype=='ProductProvider'){
                    //2021/2/4去除
                    // thisInstance.vendoridInstance(1);
                }
            }
			//workflowschange($('input[name="workflowsid"]').val());thisInstance.loadWidgetNote($('.widgetContainer_salesorderworkflows'),$('input[name="workflowsid"]').val());
			if($('#servicecontractsid_display').val()){
			//编辑页面载入显示合同并加载合同下产品信息
				//thisInstance.hasContract();
				//servicecontractschange();
				//逻辑上选定合同以合同为准//禁止再修改工作流和合同
				//$('#SalesOrder_editView_fieldName_workflowsid_select,#SalesOrder_editView_fieldName_workflowsid_clear,#SalesOrder_editView_fieldName_servicecontractsid_select,#SalesOrder_editView_fieldName_servicecontractsid_clear,#SalesOrder_editView_fieldName_accountid_clear,#SalesOrder_editView_fieldName_accountid_select').parent().remove();
				$('#RefillApplication_editView_fieldName_servicecontractsid_select,#RefillApplication_editView_fieldName_servicecontractsid_clear,#RefillApplication_editView_fieldName_workflowsid_clear,#RefillApplication_editView_fieldName_workflowsid_select').parent().remove();
			}else{
				$('.tableadv').addClass('hide').find('input').attr("disabled","disabled");
				$('.tablecust').removeClass('hide').find('input').removeAttr("disabled");
				//按工单加载产品信息
				//thisInstance.loadWidgetNo(0,record);
			}
			//potentialchange();
		}else{
			//新建默认标准合同 禁用产品选择
			$('.tablecust').find('input').attr("disabled","disabled");
		}
	},

    /**
     * 单独获取账户
     * @returns {boolean}
     */
    vendoridInstanceByRecord:function(){
        var thisInstance=this;
        var modulestatus=$('input[name="rechargesource"]').val();
        var accountid=0;
        if(modulestatus=='Vendors' || modulestatus=='COINRETURN'){
            accountid=$('input[name="accountid"]').val();
            accountid*=1;
            if(accountid==0){
                var params = {'text':'充值单未选择合同或合同无客户，请先将基本信息补充完整！', 'title': ''};
                Vtiger_Helper_Js.showPnotify(params);
                //e.preventDefault();
                return false;
            }
        }
        var params={};
        params.data= {'module':'RefillApplication','mode':'getVendorBankInfo','action':'BasicAjax','record':$('input[name="vendorid"]').val(),'rechargesource':modulestatus,'accountid':accountid};
        params.async=false;
        var record=jQuery('input[name="record"]').val();
        record=record>0?record:0;
        AppConnector.request(params).then(
            function(data){
                //progressIndicatorElement.progressIndicator({ 'mode' : 'hide'});
                if(data.success){
                    //progressIndicatorElement.progressIndicator({ 'mode' : 'hide'});
                    if(data.success ==  true){
                        var columnfields=data.result.columnfields;//取开户信息
                        $('input[name="bankaccount"]').val(columnfields.bankaccount);
                        $('input[name="bankname"]').val(columnfields.bankname);
                        $('input[name="banknumber"]').val(columnfields.banknumber);
                        $('input[name="bankcode"]').val(columnfields.bankcode);
                        var bankinfo=data.result.bankinfo;
                        var bankinfostr='';
                        $.each(bankinfo,function(key,value){
                            bankinfostr+='<option value="'+value.bankaccount+'" data-bankaccount="'+value.bankaccount+'" data-bankname="'+value.bankname+'" data-banknumber="'+value.banknumber+'" data-bankcode="'+value.bankcode+'">'+value.banknumber+'</option>'
                        });
                        $('select[name="banklist"]')[0].options.length=0;
                        $('select[name="banklist"]').append(bankinfostr);
                        $('select[name="banklist"]').trigger("liszt:updated");
                    }
                }
            }
        )
    },


    vendoridInstance:function(flag){
        var args=flag||0;
        var thisInstance=this;
        //var progressIndicatorElement = jQuery.progressIndicator({ 'message' : '合同信息加载中...','blockInfo':{'enabled':true }});
        var modulestatus=$('input[name="rechargesource"]').val();
        var accountid=0;
        if(modulestatus=='Vendors' || modulestatus=='COINRETURN'){
            accountid=$('input[name="accountid"]').val();
            accountid*=1;
            if(accountid==0){
                var params = {'text':'充值单未选择合同或合同无客户，请先将基本信息补充完整！', 'title': ''};
                Vtiger_Helper_Js.showPnotify(params);
                //e.preventDefault();
                return false;
            }
        }
        var params={};
            params.data= {'module':'RefillApplication','mode':'getVendorBankInfo','action':'BasicAjax','record':$('input[name="vendorid"]').val(),'rechargesource':modulestatus,'accountid':accountid};
        params.async=false;
        var record=jQuery('input[name="record"]').val();
        record=record>0?record:0;

        AppConnector.request(params).then(
            function(data){
                //progressIndicatorElement.progressIndicator({ 'mode' : 'hide'});
                if(data.success){
                    //progressIndicatorElement.progressIndicator({ 'mode' : 'hide'});
                    if(data.success ==  true){
                        if('COINRETURN'==modulestatus){
                            var productprovider=data.result.productprovider;//取产品服务产品信息

                            record==0&&thisInstance.clearIdInfo();
                            var did=$('select[name="did"]').val();
                            $('select[name="did"]')[0].options.length=0;
                            if(productprovider.length>0){
                                thisInstance.idtopplatform=productprovider;
                                var afteraction=$('input[name="action"]');
                                var optionStr='';
                                $(productprovider).each(function(key,value){
                                    var selectOption=(record==0&&key==0)?' selected':((record>0&&did==value.idaccount)?' selected':'');
                                    if((record==0 || flag==1)&&key==0) {
                                        $('input[name="productid_display"]').val(value.topplatform);
                                        $('input[name="productid"]').val(value.productid);
                                        $('input[name="accountzh"]').val(value.accountplatform);
                                        $('input[name="discount"]').val(value.accountrebate);
                                        $('select[name="isprovideservice"]').val(value.isprovideservice);
                                        $('select[name="isprovideservice"]').trigger('liszt:updated');
                                        $('select[name="rechargetypedetail"]').val(value.rechargetypedetail);
                                        $('select[name="rechargetypedetail"]').trigger('liszt:updated');
                                        $('select[name="customeroriginattr"]').val(value.customeroriginattr);
                                        $('select[name="customeroriginattr"]').trigger('liszt:updated');
                                        $('select[name="rebatetype"]').val(value.rebatetype);
                                        $('select[name="rebatetype"]').trigger('liszt:updated');
                                        $('select[name="accountrebatetype"]').val(value.accountrebatetype);
                                        $('select[name="accountrebatetype"]').trigger('liszt:updated');
                                        $('input[name="supprebate"]').val(value.supplierrebate);
                                        if(value.topplatform=='谷歌' || value.topplatform=='Yandex'){
                                            $('input[name="transferamount"]').attr('readonly','readonly');
                                            $('input[name="taxation"]').attr('readonly','readonly');
                                            $('input[name="prestoreadrate"]').removeAttr('readonly');
                                        }else{
                                            $('input[name="transferamount"]').removeAttr('readonly');
                                            $('input[name="taxation"]').removeAttr('readonly');
                                            $('input[name="prestoreadrate"]').attr('readonly','readonly');
                                        }
                                        if(modulestatus=='COINRETURN'){
                                            $('input[name="mproductid_display[1]"]').val(value.topplatform);
                                            $('input[name="mproductid[1]"]').val(value.productid);
                                            $('input[name="maccountzh[1]"]').val(value.accountplatform);
                                            $('input[name="mdiscount[1]"]').val(value.accountrebate);
                                            $('select[name="misprovideservice[1]"]').val(value.isprovideservice);
                                            $('select[name="misprovideservice[1]"]').trigger('liszt:updated');
                                            $('select[name="mrechargetypedetail[1]"]').val(value.rechargetypedetail);
                                            $('select[name="mrechargetypedetail[1]"]').trigger('liszt:updated');
                                            $('select[name="mcustomeroriginattr[1]"]').val(value.customeroriginattr);
                                            $('select[name="mcustomeroriginattr[1]"]').trigger('liszt:updated');
                                            $('select[name="mrebatetype[1]"]').val(value.rebatetype);
                                            $('select[name="mrebatetype[1]"]').trigger('liszt:updated');
                                            $('select[name="maccountrebatetype[1]"]').val(value.accountrebatetype);
                                            $('select[name="maccountrebatetype[1]"]').trigger('liszt:updated');
                                        }
                                    }
                                    afteraction.after('<input type="hidden" name="mastersupprebate['+value.accountplatformid+']" value="'+value.supplierrebate+'" disabled>');
                                    optionStr+='<option data-changeid="'+key+'" value="'+value.idaccount+'"'+selectOption+'>'+value.idaccount+'</option>';
                                })
                                $('select[name="did"]').append(optionStr);
                                if(record==0 && modulestatus=='COINRETURN'){
                                    $('select[name="mid[1]"]').append(optionStr);
                                    $('select[name="mid[1]"]').trigger('liszt:updated');
                                }
                                if(record>0){
                                    $.each($('select[name^="mid["]'),function(pkey,pvalue){
                                        var optionStr='';
                                        var did=$(pvalue).val();
                                        var cid=$(pvalue).data('cid');
                                        $('select[name="mid['+cid+']"]')[0].options.length=0
                                        $(data.result).each(function(key,value){
                                            var selectOption=did==value.idaccount?' selected':'';

                                            //afteraction.after('<input type="hidden" name="mastersupprebate['+value.accountplatformid+']" value="'+value.supplierrebate+'">');
                                            optionStr+='<option data-changeid="'+key+'" value="'+value.idaccount+'"'+selectOption+'>'+value.idaccount+'</option>';
                                        });
                                        $('select[name="mid['+cid+']"]').append(optionStr);
                                        $('select[name="mid['+cid+']"]').trigger('liszt:updated');
                                    });
                                }
                            }else{
                                Vtiger_Helper_Js.showMessage({type:'error',text:'客户没有相关的账户信息，请联系相关人员添加!'});
                                thisInstance.idtopplatform={};
                            }
                            $('select[name="did"]').trigger('liszt:updated');
                            return;
                        }
                        var columnfields=data.result.columnfields;//取开户信息
                        $('input[name="bankaccount"]').val(columnfields.bankaccount);
                        $('input[name="bankname"]').val(columnfields.bankname);
                        $('input[name="banknumber"]').val(columnfields.banknumber);
                        $('input[name="bankcode"]').val(columnfields.bankcode);
                        var bankinfo=data.result.bankinfo;
                        var bankinfostr='';
                        $.each(bankinfo,function(key,value){
                            bankinfostr+='<option value="'+value.bankaccount+'" data-bankaccount="'+value.bankaccount+'" data-bankname="'+value.bankname+'" data-banknumber="'+value.banknumber+'" data-bankcode="'+value.bankcode+'">'+value.banknumber+'</option>'
                        });
                        $('select[name="banklist"]')[0].options.length=0;
                        $('select[name="banklist"]').append(bankinfostr);
                        $('select[name="banklist"]').trigger("liszt:updated");;
                        //var purchaseinvoice=data.result.purchaseinvoice;//取发票信息
                        var productprovider=data.result.productprovider;//取产品服务产品信息
                        $('#purchaseinvoiceidselect').remove();
                        /*if(purchaseinvoice.length>0){
                            $('input[name="purchaseinvoiceid_display"]').val(purchaseinvoice[0].businessname);
                            $('input[name="purchaseinvoiceid"]').val(purchaseinvoice[0].purchaseinvoiceid);
                            $('input[name="businessnames"]').val(purchaseinvoice[0].invoicecompany);
                            $('input[name="billingcontent"]').val(purchaseinvoice[0].businessname);
                            $('input[name="invoicecode"]').val(purchaseinvoice[0].invoicecode);
                            $('input[name="invoicenumber"]').val(purchaseinvoice[0].invoicenumber);
                            $('input[name="amountofmoney"]').val(purchaseinvoice[0].amountofmoney);
                            if(purchaseinvoice.length>1){
                                var str="";
                                $.each(purchaseinvoice,function(n,value){
                                    var valueStr=JSON.stringify(value);
                                    str += "<option value="+value.purchaseinvoiceid+' data-fieldinfo=\''+valueStr+'\'>'+value.businessname+"</option>";
                                })
                                var newstr = "<select id='purchaseinvoiceidselect'>"+str+"</select>"
                                $('#purchaseinvoiceid_display').after(newstr);
                                $("#purchaseinvoiceidselect").on('change',function(){
                                    var values=$(this).find("option:selected").data("fieldinfo");
                                    $('input[name="purchaseinvoiceid_display"]').val(values.businessname);
                                    $('input[name="purchaseinvoiceid"]').val(values.purchaseinvoiceid);
                                    $('input[name="businessnames"]').val(values.invoicecompany);
                                    $('input[name="billingcontent"]').val(values.businessnames);
                                    $('input[name="invoicecode"]').val(values.invoicecode);
                                    $('input[name="invoicenumber"]').val(values.invoicenumber);
                                    $('input[name="amountofmoney"]').val(values.amountofmoney);
                                })
                            }
                        }*/
                        if(args==1){
                            $('select[name="productservice"]')[0].options.length=0;
                            $('select[name="productservice"]').trigger('liszt:updated');
                        }
                        if(productprovider.length>0){
                            var did=$('select[name="productservice"]').val();
                            $('select[name="productservice"]')[0].options.length=0;
                            if(modulestatus == 'Vendors'){
                                $('select[name="did"]')[0].options.length=0;
                            }

                            thisInstance.idproductid=productprovider;
                            var afteraction=$('input[name="action"]');
                            var optionStr='';
                            var optionidaccount='';
                            $(productprovider).each(function(key,value){
                                //初始加载
                                var selectOption=''
                                if(args==0){
                                    var selectOption=did==value.productid?' selected':'';
                                }else{
                                    //手动加载
                                    if(key==0){
                                        selectOption = ' selected';
                                        $('input[name="productid_display"]').val(value.productname);
                                        $('input[name="productid"]').val(value.productid);
                                        $('input[name="supprebate"]').val(value.supplierrebate);
                                        $('input[name="discount"]').val(value.accountrebate);
                                        $('input[name="did"]').val(value.idaccount);
                                        $('input[name="accountzh"]').val( value.accountzh);
                                        if (modulestatus == 'PreRecharge') {
                                            $('input[name="discount"]').val(value.supplierrebate);
                                        }
                                        if((modulestatus=='Vendors'||modulestatus=='Accounts') && (value.productname=='谷歌' || value.productname=='Yandex')){
                                            $('input[name="transferamount"]').attr('readonly','readonly');
                                            $('input[name="taxation"]').attr('readonly','readonly');
                                            $('input[name="prestoreadrate"]').removeAttr('readonly');
                                        }else{
                                            $('input[name="transferamount"]').removeAttr('readonly');
                                            $('input[name="taxation"]').removeAttr('readonly');
                                            $('input[name="prestoreadrate"]').attr('readonly','readonly');
                                        }
                                        if(value.modulestatus=='c_complete'){
                                            var iscontracted='alreadySigned';
                                        }else{
                                            var iscontracted='notSigned';
                                        }
                                        $('select[name="havesignedcontract"]').val(iscontracted);
                                        $('select[name="havesignedcontract"]').trigger("liszt:updated");
                                        $('select[name="isprovideservice"]').val(value.isprovideservice);
                                        $('select[name="isprovideservice"]').trigger("liszt:updated");
                                        $('select[name="rechargetypedetail"]').val(value.rechargetypedetail);
                                        $('select[name="rechargetypedetail"]').trigger('liszt:updated');
                                        $('select[name="customeroriginattr"]').val(value.customeroriginattr);
                                        $('select[name="customeroriginattr"]').trigger('liszt:updated');
                                        $('input[name="signdate"]').val(value.signdate);
                                        $('input[name="suppliercontractsid_display"]').val(value.contract_no);
                                        $('input[name="suppliercontractsid"]').val(value.suppliercontractsid);
                                        $('select[name="rebatetype"]').val(value.rebatetype);
                                        $('select[name="rebatetype"]').trigger('liszt:updated');
                                        $('select[name="accountrebatetype"]').val(value.accountrebatetype);
                                        $('select[name="accountrebatetype"]').trigger('liszt:updated');
                                    }
                                }
                                afteraction.after('<input type="hidden" name="mastersupprebate['+value.productid+']" value="'+value.supplierrebate+'" disabled>');
                                optionStr+='<option data-changeid="'+key+'" value="'+value.productid+'"'+selectOption+'>'+value.productname+'</option>';
                                optionidaccount+='<option data-changeid="'+key+'" value="'+value.idaccount+'"'+selectOption+'>'+value.idaccount+'</option>';
                            })
                            $('select[name="productservice"]').empty();
                            $('select[name="productservice"]').append(optionStr);
                            $('select[name="did"]').empty();
                            $('select[name="did"]').append(optionidaccount);
                            if(record>0){
                                var productprovideroptionStr='';
                                $(productprovider).each(function(key,value){
                                    productprovideroptionStr+='<option data-changeid="'+key+'" value="'+value.productid+'">'+value.productname+'</option>';
                                });
                                 $.each($('select[name^="mproductservice["]'),function(pkey,pvalue){
                                     var did=$(pvalue).val();
                                     var cid=$(pvalue).attr('data-cid');
                                     /*$('select[name="mproductservice['+cid+']"]').empty();
                                     $(productprovider).each(function(key,value){
                                     var selectOption=did==value.productid?' selected':'';
                                         optionStr+='<option data-changeid="'+key+'" value="'+value.productid+'"'+selectOption+'>'+value.productname+'</option>';
                                     });*/
                                     $('select[name="mproductservice['+cid+']"]').empty();
                                     $('select[name="mproductservice['+cid+']"]').append(productprovideroptionStr);
                                     $('select[name="mproductservice['+cid+']"]').val(did);
                                     $('select[name="mproductservice['+cid+']"]').trigger('liszt:updated');
                                 });
                                var didoptionStr='';
                                $(productprovider).each(function(key,value){
                                    didoptionStr+='<option data-changeid="'+key+'" value="'+value.idaccount+'">'+value.idaccount+'</option>';
                                });
                                $.each($('select[name^="mid["]'),function(pkey,pvalue){
                                    var did=$(pvalue).val();
                                    var cid=$(pvalue).attr('data-cid');
                                    /*$(productprovider).each(function(key,value){
                                        var selectOption=did==value.idaccount?' selected':'';
                                        didoptionStr+='<option data-changeid="'+key+'" value="'+value.idaccount+'"'+selectOption+'>'+value.idaccount+'</option>';
                                    });*/
                                    $('select[name="mid['+cid+']"]').empty();
                                    $('select[name="mid['+cid+']"]').append(didoptionStr);
                                    $('select[name="mid['+cid+']"]').val(did);
                                    $('select[name="mid['+cid+']"]').trigger('liszt:updated');
                                });
                             }
                            $('select[name="productservice"]').trigger('liszt:updated');
                            $('select[name="did"]').trigger('liszt:updated');
                        }
                    }
                }
            },
            function(error){}
        );
    },

    //多充值明细添加
    addrechargesheets:function(){
        var thisInstance = this;
        $('#addfallinto').on('click',function(){
           var rechargesource=$('input[name="rechargesource"]').val();
           var accountId=$('input[name="accountid"]').val();
            if(rechargesource=='Accounts') {
                if(!accountId){
                    Vtiger_Helper_Js.showMessage({type: 'error', text: '请先选择客户！'});
                    return false;
                }
                var numd = $('.Duplicates').length + 1;
                if (numd > 20) {
                    return;
                }
                /*超过20个不允许添加*/
                var nowdnum = $('.Duplicates').last().data('num');
                if (nowdnum != undefined) {
                    numd = nowdnum + 1;
                }else{
                    numd=1;
                }
                var extend = erechargesheet.replace(/\[\]|replaceyes/g, '[' + numd + ']');
                extend = extend.replace(/yesreplace/g, numd);
                //替换历史账号按钮class
                extend = extend.replace(/\[\]|historyAccountzh_temp/g, 'historyAccountzh_' + numd);
                $('#insertbefore').before(extend);
                //去掉原本的下拉框，换上弹框
                $('.Duplicates').last().find('tbody .fieldValue').eq(0).remove();
                var replaceIdStr='<td class="fieldValue medium"><input name="popupReferenceModule" type="hidden" value="RefillApplication" autocomplete="off"><input name="mid['+numd+']" type="hidden" data-cid="'+numd+'" value="" data-multiple="0" class="sourceField" data-displayvalue="" data-fieldinfo="{&quot;mandatory&quot;:true,&quot;presence&quot;:true,&quot;quickcreate&quot;:true,&quot;masseditable&quot;:true,&quot;defaultvalue&quot;:false,&quot;type&quot;:&quot;string&quot;,&quot;name&quot;:&quot;did&quot;,&quot;label&quot;:&quot;ID&quot;}" autocomplete="off"><div class="row-fluid input-prepend input-append"><span class="add-on clearReferenceSelection cursorPointer"><i id="RefillApplication_editView_fieldName_did_clear" class="icon-remove-sign" title="清除"></i></span><input id="mid_display['+numd+']" readonly="readonly" name="mid_display['+numd+']" type="text" class=" span7 marginLeftZero autoComplete" value="" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{&quot;mandatory&quot;:true,&quot;presence&quot;:true,&quot;quickcreate&quot;:true,&quot;masseditable&quot;:true,&quot;defaultvalue&quot;:false,&quot;type&quot;:&quot;string&quot;,&quot;name&quot;:&quot;did&quot;,&quot;label&quot;:&quot;ID&quot;}" placeholder="查找.." autocomplete="off"><span data-id="RefillApplication_editView_fieldName_did_select" class="add-on relatedPopupDid cursorPointer"><i id="RefillApplication_editView_fieldName_did_select" data-id="RefillApplication_editView_fieldName_did_select" class="icon-search relatedPopupDid" title="选择"></i></span></div></td>'
                $('.Duplicates').last().find('tbody .fieldLabel').eq(0).after(replaceIdStr);

                // thisInstance.googleReadonlyField(currentTopplatform,numd);//谷歌平台切换
                $('.chzn-select').chosen();
                //注册点击事件
                thisInstance.getHistoryAccountzh(numd);
            }else if(rechargesource=='Vendors') {
                if(!accountId){
                    Vtiger_Helper_Js.showMessage({type: 'error', text: '请先选择客户！'});
                    return false;
                }
                var numd = $('.Duplicates').length + 1;
                if (numd > 20) {
                    return;
                }
                /*超过20个不允许添加*/
                var nowdnum = $('.Duplicates').last().data('num');
                if (nowdnum != undefined) {
                    numd = nowdnum + 1;
                }
                var extend = vendorchargesheet.replace(/\[\]|replaceyes/g, '[' + numd + ']');
                extend = extend.replace(/yesreplace/g, numd);
                //替换历史账号按钮class
                extend = extend.replace(/\[\]|historyAccountzh_temp/g, 'historyAccountzh_' + numd);
                $('#insertbefore').before(extend);
                //去掉原本的下拉框，换上弹框
                $('.Duplicates').last().find('tbody .fieldValue').eq(1).remove();
                var replaceIdStr='<td class="fieldValue medium"><input name="popupReferenceModule" type="hidden" value="RefillApplication" autocomplete="off"><input name="mid['+numd+']" type="hidden" data-cid="'+numd+'" value="" data-multiple="0" class="sourceField" data-displayvalue="" data-fieldinfo="{&quot;mandatory&quot;:true,&quot;presence&quot;:true,&quot;quickcreate&quot;:true,&quot;masseditable&quot;:true,&quot;defaultvalue&quot;:false,&quot;type&quot;:&quot;string&quot;,&quot;name&quot;:&quot;did&quot;,&quot;label&quot;:&quot;ID&quot;}" autocomplete="off"><div class="row-fluid input-prepend input-append"><span class="add-on clearReferenceSelection cursorPointer"><i id="RefillApplication_editView_fieldName_did_clear" class="icon-remove-sign" title="清除"></i></span><input id="mid_display['+numd+']" readonly="readonly" name="mid_display['+numd+']" type="text" class=" span7 marginLeftZero autoComplete" value="" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{&quot;mandatory&quot;:true,&quot;presence&quot;:true,&quot;quickcreate&quot;:true,&quot;masseditable&quot;:true,&quot;defaultvalue&quot;:false,&quot;type&quot;:&quot;string&quot;,&quot;name&quot;:&quot;did&quot;,&quot;label&quot;:&quot;ID&quot;}" placeholder="查找.." autocomplete="off"><span data-id="RefillApplication_editView_fieldName_did_select" class="add-on relatedPopupDid cursorPointer"><i id="RefillApplication_editView_fieldName_did_select" data-id="RefillApplication_editView_fieldName_did_select" class="icon-search relatedPopupDid" title="选择"></i></span></div></td>'
                $('.Duplicates').last().find('tbody .fieldLabel').eq(1).after(replaceIdStr);
                //注册点击事件
                thisInstance.getHistoryAccountzh(numd);
            }else if(rechargesource=='PreRecharge'){
                if (JSON.stringify(thisInstance.idproductid) == '{}') {
                    return false;
                }
                var numd = $('.Duplicates').length + 1;
                if (numd > 20) {
                    return;
                }
                /*超过20个不允许添加*/
                var nowdnum = $('.Duplicates').last().data('num');
                if (nowdnum != undefined) {
                    numd = nowdnum + 1;
                }
                var extend = PreRecharge.replace(/\[\]|replaceyes/g, '[' + numd + ']');
                extend = extend.replace(/yesreplace/g, numd);

                var productids=','+$('select[name="productservice"]').val();
                var mproductservice=$('select[name^="mproductservice["]');
                if(mproductservice.length>0){
                    $.each(mproductservice,function(pkey,pvalue){
                        productids+=','+$(pvalue).val();
                    });
                }
                productids+=',';
                var optionStr = '';
                var stpe=0;
                var cproductname,cproductid,csupplierrebate,havesignedcontract,csigndate,csuppliercontractsid,suppliercontractsiddisplay,rebatetype;
                $(thisInstance.idproductid).each(function (key, value) {
                    var productidstr=','+value.productid+',';
                    if(productids.indexOf(productidstr)==-1){
                        var selectOption = stpe == 0 ? ' selected' : '';
                        if(stpe==0){
                            if(value.modulestatus=='c_complete'){
                                havesignedcontract="alreadySigned";
                            }else{
                                havesignedcontract="notSigned";
                            }
                            cproductname=value.productname;
                            cproductid=value.productid;
                            csupplierrebate=value.supplierrebate;
                            csigndate=value.signdate;
                            suppliercontractsiddisplay=value.contract_no;
                            csuppliercontractsid=value.suppliercontractsid;
                            rebatetype=value.rebatetype;
                        }
                        stpe++;
                        optionStr += '<option data-cid="'+numd+'" data-changeid="' + key + '" value="' + value.productid + '"' + selectOption + '>' + value.productname +'</option>';
                    }
                });
                if(stpe==0){
                    return false;
                }
                $('#insertbefore').before(extend);
                $('input[name="mproductid_display['+ numd +']"]').val(cproductname);
                $('input[name="mproductid['+ numd +']"]').val(cproductid);
                $('input[name="msupprebate[' + numd + ']"]').val(csupplierrebate);
                $('input[name="mdiscount[' + numd + ']"]').val(csupplierrebate);
                $('input[name="msigndate[' + numd + ']"]').val(csigndate);
                $('input[name="msuppliercontractsid[' + numd + ']"]').val(csuppliercontractsid);
                $('input[name="msuppliercontractsid[display' + numd + ']_display"]').val(suppliercontractsiddisplay);
                $('select[name="mproductservice[' + numd + ']"]').append(optionStr);
                $('select[name="mhavesignedcontract[' + numd + ']"]').val(havesignedcontract);
                $('select[name="mrebatetype[' + numd + ']"]').val(rebatetype);
                $('.chzn-select').chosen();
                /*$('input[name="msigndate['+numd+']"]').datepicker({
                    format: "yyyy-mm-dd",
                    language:  'zh-CN',
                    autoclose: true,
                    todayBtn: true,
                    pickerPosition: "bottom-right",
                    showMeridian: 0

                });*/
            }else if(rechargesource=='TECHPROCUREMENT'){
                if (JSON.stringify(thisInstance.idproductid) == '{}') {
                    return false;
                }
                var numd = $('.Duplicates').length + 1;
                if (numd > 20) {
                    return;
                }
                /*超过20个不允许添加*/
                var nowdnum = $('.Duplicates').last().data('num');
                if (nowdnum != undefined) {
                    numd = nowdnum + 1;
                }
                var extend = techsheet.replace(/\[\]|replaceyes/g, '[' + numd + ']');
                extend = extend.replace(/yesreplace/g, numd);

                var productids=','+$('select[name="productservice"]').val();
                var mproductservice=$('select[name^="mproductservice["]');
                if(mproductservice.length>0){
                    $.each(mproductservice,function(pkey,pvalue){
                        productids+=','+$(pvalue).val();
                    });
                }
                productids+=',';
                var optionStr = '';
                var stpe=0;
                var cproductname,cproductid,csupplierrebate,havesignedcontract,csigndate,csuppliercontractsid,suppliercontractsiddisplay;
                $(thisInstance.idproductid).each(function (key, value) {
                    var productidstr=','+value.productid+',';
                    if(productids.indexOf(productidstr)==-1){
                        var selectOption = stpe == 0 ? ' selected' : '';
                        if(stpe==0){
                            if(value.modulestatus=='c_complete'){
                                havesignedcontract="alreadySigned";
                            }else{
                                havesignedcontract="notSigned";
                            }
                            cproductname=value.productname;
                            cproductid=value.productid;
                            csupplierrebate=value.supplierrebate;
                            csigndate=value.signdate;
                            suppliercontractsiddisplay=value.contract_no;
                            csuppliercontractsid=value.suppliercontractsid;
                        }
                        stpe++;
                        optionStr += '<option data-cid="'+numd+'" data-changeid="' + key + '" value="' + value.productid + '"' + selectOption + '>' + value.productname +'</option>';
                    }
                });
                if(stpe==0){
                    return false;
                }
                $('#insertbefore').before(extend);
                $('input[name="mproductid_display['+ numd +']"]').val(cproductname);
                $('input[name="mproductid['+ numd +']"]').val(cproductid);
                $('input[name="msupprebate[' + numd + ']"]').val(csupplierrebate);
                $('input[name="mdiscount[' + numd + ']"]').val(csupplierrebate);
                $('input[name="msigndate[' + numd + ']"]').val(csigndate);
                $('input[name="msuppliercontractsid[' + numd + ']"]').val(csuppliercontractsid);
                $('input[name="msuppliercontractsid[display' + numd + ']_display"]').val(suppliercontractsiddisplay);
                $('select[name="mproductservice[' + numd + ']"]').append(optionStr);
                $('select[name="mhavesignedcontract[' + numd + ']"]').val(havesignedcontract);
                $('.chzn-select').chosen();
                /*$('input[name="msigndate['+numd+']"]').datepicker({
                    format: "yyyy-mm-dd",
                    language:  'zh-CN',
                    autoclose: true,
                    todayBtn: true,
                    pickerPosition: "bottom-right",
                    showMeridian: 0

                });*/
            }else if(rechargesource=='OtherProcurement'){
                if (JSON.stringify(thisInstance.idproductid) == '{}') {
                    return false;
                }
                var numd = $('.Duplicates').length + 1;
                if (numd > 20) {
                    return;
                }
                /*超过20个不允许添加*/
                var nowdnum = $('.Duplicates').last().data('num');
                if (nowdnum != undefined) {
                    numd = nowdnum + 1;
                }
                var extend = otherProcurementSheet.replace(/\[\]|replaceyes/g, '[' + numd + ']');
                extend = extend.replace(/yesreplace/g, numd);

                var productids=','+$('select[name="productservice"]').val();
                var mproductservice=$('select[name^="mproductservice["]');
                if(mproductservice.length>0){
                    $.each(mproductservice,function(pkey,pvalue){
                        productids+=','+$(pvalue).val();
                    });
                }
                productids+=',';
                var optionStr = '';
                var stpe=0;
                var cproductname,cproductid,csupplierrebate,havesignedcontract,csigndate,csuppliercontractsid,suppliercontractsiddisplay;
                $(thisInstance.idproductid).each(function (key, value) {
                    var productidstr=','+value.productid+',';
                    if(productids.indexOf(productidstr)==-1){
                        /*var selectOption = stpe == 0 ? ' selected' : '';
                        stpe != 0 || (cproductname=value.productname,
                                cproductid=value.productid,
                                csupplierrebate=value.supplierrebate
                        );*/
                        if(stpe==0){
                            if(value.modulestatus=='c_complete'){
                                havesignedcontract="alreadySigned";
                            }else{
                                havesignedcontract="notSigned";
                            }
                            cproductname=value.productname;
                            cproductid=value.productid;
                            csupplierrebate=value.supplierrebate;
                            csigndate=value.signdate;
                            suppliercontractsiddisplay=value.contract_no;
                            csuppliercontractsid=value.suppliercontractsid;
                        }
                        stpe++;
                        optionStr += '<option data-changeid="' + key + '" value="' + value.productid + '"' + selectOption + '>' + value.productname + '</option>';
                    }
                });
                if(stpe==0){
                    return false;
                }
                $('#insertbefore').before(extend);
                $('input[name="mproductid_display['+ numd +']"]').val(cproductname);
                $('input[name="mproductid['+ numd +']"]').val(cproductid);
                $('select[name="mproductservice[' + numd + ']"]').append(optionStr);
                $('.chzn-select').chosen();
                /*$('input[name="msigndate['+numd+']"]').datepicker({
                    format: "yyyy-mm-dd",
                    language:  'zh-CN',
                    autoclose: true,
                    todayBtn: true,
                    pickerPosition: "bottom-right",
                    showMeridian: 0

                });*/
            }else if(rechargesource=='NonMediaExtraction'){
                if (JSON.stringify(thisInstance.idproductid) == '{}') {
                    return false;
                }
                var numd = $('.Duplicates').length + 1;
                if (numd > 20) {
                    return;
                }
                /*超过20个不允许添加*/
                var nowdnum = $('.Duplicates').last().data('num');
                if (nowdnum != undefined) {
                    numd = nowdnum + 1;
                }
                var extend = NonMediaExtractionSheet.replace(/\[\]|replaceyes/g, '[' + numd + ']');
                extend = extend.replace(/yesreplace/g, numd);

                var productids=','+$('select[name="productservice"]').val();
                var mproductservice=$('select[name^="mproductservice["]');
                if(mproductservice.length>0){
                    $.each(mproductservice,function(pkey,pvalue){
                        productids+=','+$(pvalue).val();
                    });
                }
                productids+=',';
                var optionStr = '';
                var stpe=0;
                var cproductname,cproductid,csupplierrebate,havesignedcontract,csigndate,csuppliercontractsid,suppliercontractsiddisplay;
                $(thisInstance.idproductid).each(function (key, value) {
                    var productidstr=','+value.productid+',';
                    if(productids.indexOf(productidstr)==-1){
                        var selectOption = stpe == 0 ? ' selected' : '';
                        /*stpe != 0 || (cproductname=value.productname,
                                cproductid=value.productid,
                                csupplierrebate=value.supplierrebate
                        );*/
                        if(stpe==0){
                            if(value.modulestatus=='c_complete'){
                                havesignedcontract="alreadySigned";
                            }else{
                                havesignedcontract="notSigned";
                            }
                            cproductname=value.productname;
                            cproductid=value.productid;
                            csupplierrebate=value.supplierrebate;
                            csigndate=value.signdate;
                            suppliercontractsiddisplay=value.contract_no;
                            csuppliercontractsid=value.suppliercontractsid;
                        }
                        stpe++;
                        optionStr += '<option data-cid="'+numd+'" data-changeid="' + key + '" value="' + value.productid + '"' + selectOption + '>' + value.productname +'</option>';
                    }
                });
                if(stpe==0){
                    return false;
                }
                $('#insertbefore').before(extend);
                $('input[name="mproductid_display['+ numd +']"]').val(cproductname);
                $('input[name="mproductid['+ numd +']"]').val(cproductid);
                $('input[name="msupprebate[' + numd + ']"]').val(csupplierrebate);
                $('input[name="mdiscount[' + numd + ']"]').val(csupplierrebate);
                $('input[name="msigndate[' + numd + ']"]').val(csigndate);
                $('input[name="msuppliercontractsid[' + numd + ']"]').val(csuppliercontractsid);
                $('input[name="msuppliercontractsid[display' + numd + ']_display"]').val(suppliercontractsiddisplay);
                $('select[name="mproductservice[' + numd + ']"]').append(optionStr);
                $('select[name="mhavesignedcontract[' + numd + ']"]').val(havesignedcontract);
                $('.chzn-select').chosen();
                /*$('input[name="msigndate['+numd+']"]').datepicker({
                    format: "yyyy-mm-dd",
                    language:  'zh-CN',
                    autoclose: true,
                    todayBtn: true,
                    pickerPosition: "bottom-right",
                    showMeridian: 0

                });*/
            }
            thisInstance.selectedChange();

            var arrayList=['customertype','oldrechargesource','iscontracted','newiscontracted','customeroriginattr','isprovideservice','havesignedcontract','miscontracted','mcustomeroriginattr','misprovideservice','mhavesignedcontract'];
            $.each($('select'),function(key,value){
                var valueName=$(value).attr('name');
                valueName=valueName.replace(/\[\d*\]/,'');
                if($.inArray(valueName,arrayList)!=-1){
                    var thisid=$(value).attr('id');
                    $('#'+thisid+'_chzn').find('.chzn-drop').remove();
                }
            });
        });
    },
    /**
     *
     * @param _this
     */
    getReceivedPayments:function(){
        var receivedstatus=$('select[name="receivedstatus"]').val();
        var rechargesource=$('input[name="rechargesource"]').val();
        var rorigin=(rechargesource=='Accounts'&& receivedstatus=='virtualrefund')?true:false;
        var params={};
            params.data= {'module':'RefillApplication',
                'action':'BasicAjax',
                'mode':'getReceivedPayments',
                'record':$('input[name="servicecontractsid"]').val(),
                'receivedstatus':receivedstatus,
                'rechargesource':rechargesource
            };
        params.async=false;
        $(".newinvoicerayment_tab").remove();
        $("#allCheck").remove();
        $("#batchDeletePayment").remove();
        AppConnector.request(params).then(function(data){
            if(data.success){
                var str='';
                $.each(data.result,function(key,value){
                    var userMoney=value.standardmoney-value.rechargeableamount-value.occupationcost;
                    userMoney*=1.0;
                    userMoney=userMoney.toFixed(2);

                    paytitle = value.paytitle;
                    companyname = value.owncompany+value.paytitle;
                    if(paytitle == ''){
                        paytitle = value.paymentcode;
                        companyname = value.paymentcode;
                    }

                    str+='<table class="table table-bordered blockContainer newinvoicerayment_tab detailview-table newinvoicerayment_tab'+value.receivedpaymentsid+'" data-num="'+value.receivedpaymentsid+'">'+
                        '<thead><tr><th class="blockHeader" colspan="9"><input type="checkbox" name="paymentCheck" style="margin:0px" data-id="'+value.receivedpaymentsid+'"">&nbsp;&nbsp;关联回款信息['+(key+1)+'] <b class="pull-right"><button class="btn btn-small delbuttonnewinvoicerayment" type="button" data-id="'+value.receivedpaymentsid+'"><i class="icon-trash" title="删除关联回款信息"></i></button></b></th></tr></thead>'+
                        '<tbody><tr><td><label class="muted">回款信息</label></td>'+
                        '<td><label class="muted"><span class="redColor">*</span> 入账金额</label></td>'+
                        '<td><label class="muted"><span class="redColor">*</span> 入账日期</label></td>'+
                        '<td><label class="muted"><span class="redColor">*</span> 来源</label></td>'+
                        '<td><label class="muted"><span class="redColor">*</span> 已使用工单金额</label></td>'+
                        '<td><label class="muted"><span class="redColor">*</span> 已使用充值金额</label></td>'+
                        '<td><label class="muted"><span class="redColor">*</span> 可使用金额<span class="rpaymentid" data-receivedpaymentsid="'+value.receivedpaymentsid+'" title="点击查看明细"><span class="icon-question-sign"></span></span></label></td>'+
                        '<td><label class="muted"><span class="redColor">*</span> 使用金额</label></td>'+
                        '<td><label class="muted"><span class="redColor"></span> 备注</label></td></tr>'+
                        '<tr><td><input type="hidden" name="insertii['+value.receivedpaymentsid+']" value="'+value.receivedpaymentsid+'"><input type="hidden" class="receivedpaymentsid_display" name="receivedpaymentsid_display['+value.receivedpaymentsid+']" data-id="'+value.receivedpaymentsid+'" value=""> <input type="hidden" class="invoicecompany" name="owncompany['+value.receivedpaymentsid+']" data-id="'+value.receivedpaymentsid+'" value="'+value.owncompany+'"><div class="row-fluid"><span class="span10"><select class="chzn-select t_tab_newinvoicerayment_id" name="paytitle['+value.receivedpaymentsid+']" data-id="'+value.receivedpaymentsid+'" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"><option value="'+paytitle+'">'+companyname+'</option></select></span></div></td>'+
                        '<td><div class="row-fluid"><span class="span10"><input type="text" style="width:100px;" class="input-large total" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="total['+value.receivedpaymentsid+']" data-id="'+value.receivedpaymentsid+'" readonly value="'+value.standardmoney+'"></span></div></td>'+
                        '<td><div class="row-fluid"><span class="span10"><input type="text" style="width:100px;" class="input-large arrivaldate" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" readonly name="arrivaldate['+value.receivedpaymentsid+']" data-id="'+value.receivedpaymentsid+'" value="'+value.reality_date+'"></span></div></td>'+
                        '<td><div class="row-fluid"><span class="span10"><input type="text" style="width:100px;" class="input-large arrivaldate" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" readonly name="rorigin['+value.receivedpaymentsid+']" data-id="'+value.receivedpaymentsid+'" value="'+value.rorigin+'"></span></div></td>'+
                        '<td><div class="row-fluid"><span class="span10"><input type="text" style="width:100px;" class="input-large arrivaldate" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" readonly name="occupationcost['+value.receivedpaymentsid+']" data-id="'+value.receivedpaymentsid+'" value="'+value.occupationcost+'"></span></div></td>'+
                        '<td><div class="row-fluid"><span class="span10"><input type="text" style="width:100px;" class="input-large arrivaldate" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" readonly data-id="'+value.receivedpaymentsid+'" value="'+userMoney+'"></span></div></td>'+
                        '<td><div class="row-fluid"><span class="span10"><input type="text" style="width:100px;" class="input-large allowrefillapptotal" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" readonly name="allowrefillapptotal['+value.receivedpaymentsid+']" data-id="'+value.receivedpaymentsid+'" value="'+value.rechargeableamount+'"></span></div></td>'+
                        '<td><div class="row-fluid"><span class="span10"><input type="text" style="width:100px;" class="input-large refillapptotal receivedpayments_refillapptotal" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="refillapptotal['+value.receivedpaymentsid+']" data-id="'+value.receivedpaymentsid+'" value=""></span></div></td>'+
                        '<td><div class="row-fluid"><span class="span10"><textarea class="span11" data-id="'+value.receivedpaymentsid+'" name="rremarks['+value.receivedpaymentsid+']"></textarea></span></div></td></tr></tbody></table>';


                });
                if(str){
                    str='<input type="checkbox" id="allCheck" style="margin-left: 9px;"><button class="btn btn-small" type="button" id="batchDeletePayment" style="margin-left: 3px;">批量删除关联回款信息</button>'+str;
                }
                $('.LBL_INFO').after(str);
            }
        });
    },

    //新建充值单批量删除
    batchDeletePay:function(){
        var thisInstance = this;
        $("#EditView").on('click','#batchDeletePayment',function () {
            var hasCheckedLen=$("input[name='paymentCheck']:checked").length;
            if(hasCheckedLen){
                var message='确定要删除勾选的关联回款信息吗？';
                var msg={
                    'message':message
                };
                Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                    $("input[name='paymentCheck']:checked").each(function(){
                        $('.newinvoicerayment_tab'+$(this).data('id')).remove();
                        var totalrechargeValue=0;
                        $('input[name^="refillapptotal["]').each(function(key,value){
                            totalrechargeValue=thisInstance.FloatAdd(totalrechargeValue,$(value).val());
                        });
                        totalrechargeValue=totalrechargeValue*1
                        $("input[name='totalrecharge']").val(totalrechargeValue.toFixed(2));
                        if($("input[name='paymentCheck']").length==0){
                            $("#allCheck").remove();
                            $("#batchDeletePayment").remove();

                        }
                    });
                },function(error, err) {});
            }else{
                Vtiger_Helper_Js.showMessage({type: 'error', text: '请至少选择一个需要删除的的关联汇款信息！'});
                return false;
            }
        });
    },

    /**
     * 全选
     */
    checkAll:function(){
        var thisInstance = this;
        $("#EditView").on('click','#allCheck',function () {
            var isAllCheck=$(this).attr('checked');
            if(isAllCheck=='checked'){
                $("input[name='paymentCheck']").attr("checked","checked");
            }else{
                $("input[name='paymentCheck']").attr("checked",false);
            }
        });
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
    formatNumberNegative:function(_this){
        _this.val(_this.val().replace(/,/g,''));//只能输入数字小数点
        _this.val(_this.val().replace(/[^0-9.\-]/g,''));//只能输入数字小数点
        _this.val(_this.val().replace(/^0\d{1,}/g,''));//不能以0打头的整数
        _this.val(_this.val().replace(/^\./g,''));//不能以小数点打头
        _this.val(_this.val().replace(/[\d]{13,}/g,''));//超出长度全部清空
        _this.val(_this.val().replace(/\.\d{3,}$/g,''));//小数点后两位如果超过两位则将小数后的所有数清空包含小数点
        _this.val(_this.val().replace(/\.\d*\.$/g,''));//小数点后两位如果超过两位则将小数后的所有数清空包含小数点
        _this.val(_this.val().replace(/\-\d*\-$/g,''));//小数点后两位如果超过两位则将小数后的所有数清空包含小数点
        _this.val(_this.val().replace(/^\d+\-$/g,''));//小数点后两位如果超过两位则将小数后的所有数清空包含小数点
    },
    inputnumberchange : function(){
        var thisInstance=this;
        $('#EditView').on("keyup",'input[name="rechargeamount"],input[name="prestoreadrate"],.checknumber,input[name="taxtotal"],input[name^="invoicetotal["],input[name="discount"],input[name="rechargeamount"],input[name="accounttransfer"],input[name^="maccounttransfer["]',function(){
            if($(this).attr('readonly')=='readonly'){
                return false;
            }
            thisInstance.formatNumber($(this));
            var arr=$(this).val().split('.');//只有一个小数点
            if(arr.length>2){
                if(arr[1]==''){
                    $(this).val(arr[0]);
                }else{
                    $(this).val(arr[0]+'.'+arr[1]);
                }
            }
        }).on("blur",'input[name="rechargeamount"],input[name="prestoreadrate"],.checknumber,input[name="taxtotal"],input[name^="invoicetotal["],input[name="discount"],input[name="rechargeamount"],input[name="accounttransfer"],input[name^="maccounttransfer["]',function(){  //CTR+V事件处理
            if($(this).attr('readonly')=='readonly'){
                return false;
            }
            $(this).trigger("keyup");
        }).on('paste','input[name="rechargeamount"],input[name="prestoreadrate"],.checknumber,input[name="taxtotal"],input[name^="invoicetotal["],input[name="discount"],input[name="rechargeamount"],input[name="accounttransfer"],input[name^="maccounttransfer["]',function(e){
            if($(this).attr('readonly')=='readonly'){
                return false;
            }
            $(this).trigger("keyup");
        }); //CSS设置输入法不可用

    },
    loadingClear:function(){
        $('.LBL_CUSTOM_INFORMATION').find(':input').not('select[name="tax"],input[name="popupReferenceModule"],input[name="exchangerate"]').val('');
        $('.Duplicates').remove();//,select[name="isprovideservice"],select[name="rechargetypedetail"],select[name="receivementcurrencytype"]
        //清除所有加载2021.2.4加入
        $('input[name="did"]').val('');
        $('input[name="did_display"]').val('');
        // var rechargesource=$('input[name="rechargesource"]').val();
        // if(rechargesource!='PreRecharge' && rechargesource!='TECHPROCUREMENT' && rechargesource!='NonMediaExtraction'){
        //      $('select[name="did"]')[0].options.length=0;
        // }
        //$('select[name="did"]').trigger('liszt:updated')
        $('.chzn-select').trigger('liszt:updated');
    },
    //充值明细删除
    deleteregchargesheet:function(){
        var thisInstance=this;
        $('#EditView').on('click','.delbutton',function(){
            var message='确定要删除吗？';
            var msg={
                'message':message
            };
            var thisstance=$(this);
            var rechargesource=$('input[name="rechargesource"]').val();
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                thisstance.parents('.Duplicates').remove();
                if(rechargesource=="COINRETURN"){
                    thisInstance.sumCashAccounttransfer();
                }else{
                    thisInstance.actualtotalrechargeCalc();
                    thisInstance.aggregateSummary();
                }
            },function(error, err) {});
        });
    },
	registerBasicEvents:function(container){
		this._super(container);
		this.registerReferenceSelectionEvent(container);
        this.referenceModulePopupRegisterEvent(container);
        this.referenceModulePopupRegisterDidEvent(container);
	},
    referenceModulePopupRegisterEvent : function(container){
        var thisInstance = this;
        container.on("click",'.relatedPopup',function(e){
            var rechargesource = $("input[name='rechargesource']").val();
            if(rechargesource=='contractChanges'){
                var changecontracttype = $("select[name='changecontracttype']").val();
                var oldrechargesource = $("select[name='oldrechargesource']").val();
                if(!changecontracttype){
                    Vtiger_Helper_Js.showMessage({type: 'error', text: '变更类型不能为空！'});
                    return false;
                }
                if(!oldrechargesource){
                    Vtiger_Helper_Js.showMessage({type: 'error', text: '原充值来源不能为空！'});
                    return false;
                }
                var id = $(this).data("id");
                var servicecontractsid=$("input[name='servicecontractsid']").val();
                if(id=='RefillApplication_editView_fieldName_newcontractsid_select'){
                      if(servicecontractsid>0){
                      }else{
                          Vtiger_Helper_Js.showMessage({type: 'error', text: '请先选择原合同！'});
                          return false;
                      }
                      $("#whichContract").val(1);
                }else if(id=='RefillApplication_editView_fieldName_servicecontractsid_select'){
                      $("#whichContract").val(0);
                }
            }else if(rechargesource=='COINRETURN'){
                var conversiontype=$("select[name='conversiontype']").val();
                if(conversiontype=='ProductProvider'|| conversiontype=='AccountPlatform'){
                }else{
                    Vtiger_Helper_Js.showMessage({type: 'error', text: '请先选择转充类型！'});
                    return false;
                }
            }

            thisInstance.openPopUp(e);
        });
        container.find('.referenceModulesList').chosen().change(function(e){
            var element = jQuery(e.currentTarget);
            var closestTD = element.closest('td').next();
            var popupReferenceModule = element.val();
            var referenceModuleElement = jQuery('input[name="popupReferenceModule"]', closestTD);
            var prevSelectedReferenceModule = referenceModuleElement.val();
            referenceModuleElement.val(popupReferenceModule);

            //If Reference module is changed then we should clear the previous value
            if(prevSelectedReferenceModule != popupReferenceModule) {
                closestTD.find('.clearReferenceSelection').trigger('click');
            }
        });
    },

    //Did事件注册
    referenceModulePopupRegisterDidEvent : function(container){
        var thisInstance = this;
        container.on("click",'.relatedPopupDid',function(e){
            var accountid=$("input[name='accountid']").val();
            if(!accountid){
                Vtiger_Helper_Js.showMessage({type: 'error', text: '请先选择客户！'});
                return false;
            }
            var rechargesource=$('input[name="rechargesource"]').val();
            var conversiontype=$('select[name="conversiontype"]').val();
            if(rechargesource=='Accounts'||(rechargesource=='COINRETURN'&&conversiontype=='AccountPlatform')){
                thisInstance.openDidPopUp(e,0);
            }else if(rechargesource=='Vendors'||(rechargesource=='COINRETURN'&&conversiontype=='ProductProvider')){
                var vendorId=$("input[name='vendorid']").val();
                if(!vendorId){
                    Vtiger_Helper_Js.showMessage({type: 'error', text: '请先选择供应商！'});
                    return false;
                }
                thisInstance.openDidPopUp(e,1);
            }
        });
        container.find('.referenceModulesList').chosen().change(function(e){
            var element = jQuery(e.currentTarget);
            var closestTD = element.closest('td').next();
            var popupReferenceModule = element.val();
            var referenceModuleElement = jQuery('input[name="popupReferenceModule"]', closestTD);
            var prevSelectedReferenceModule = referenceModuleElement.val();
            referenceModuleElement.val(popupReferenceModule);

            //If Reference module is changed then we should clear the previous value
            if(prevSelectedReferenceModule != popupReferenceModule) {
                closestTD.find('.clearReferenceSelection').trigger('click');
            }
        });
    },

    //重写url
    openDidPopUp : function(e,isProvider){
        var accountid=$("input[name='accountid']").val();
        var thisInstance = this;
        var parentElem = jQuery(e.target).closest('td');

        var params = this.getPopUpParams(parentElem);
        params.src_otherfield='rechargesheet';
        params.src_record=accountid;
        if(isProvider){
            var vendorId=$("input[name='vendorid']").val();
            params.src_vendor=vendorId;
        }
        params.src_module='Rechargesheet';
        var old_src_field=params.src_field;
        if(params.src_field.indexOf('mid') != -1){
            params.src_field='mid';
        }
        params.isProvider=isProvider;
        var isMultiple = false;
        if(params.multi_select) {
            isMultiple = true;
        }

        var sourceFieldElement = jQuery('input[class="sourceField"]',parentElem);

        var prePopupOpenEvent = jQuery.Event(Vtiger_Edit_Js.preReferencePopUpOpenEvent);
        sourceFieldElement.trigger(prePopupOpenEvent);

        if(prePopupOpenEvent.isDefaultPrevented()) {
            return ;
        }

        var popupInstance =Vtiger_Popup_Js.getInstance();
        popupInstance.show(params,function(data){
            var responseData = JSON.parse(data);
            var dataList = new Array();
            var idList = new Array();
            idList['id']=new Array();
            idList['name']=new Array();
            for(var id in responseData){
                var data = {
                    'name' : responseData[id].info.idaccount,
                    'id' : id
                }
                dataList.push(data);
                if(!isMultiple) {
                    var midobj=$('input[name^="mid["]');
                    thisInstance.topplatformid=[];
                    thisInstance.topplatformid.push($('input[name="did"]').val());
                    $.each(midobj.serializeArray(), function(i, field){
                        if(field.name!=old_src_field){
                            thisInstance.topplatformid.push(field.value);
                        }
                    });
                    if(thisInstance.topplatformid.indexOf(data.name)>-1){
                        Vtiger_Helper_Js.showMessage({type: 'error', text: 'ID重复，请重新选择！'});
                        return false;
                    }
                    thisInstance.topplatformid.push(data.name);
                    thisInstance.setReferenceDidFieldValue(parentElem, data);
                }else{
                    idList['id'].push(id);
                    idList['name'].push(responseData[id].info.idaccount);
                }
            }

            if(isMultiple) {
                thisInstance.setMultiReferenceFieldValue(parentElem, idList);
                //sourceFieldElement.trigger(Vtiger_Edit_Js.refrenceMultiSelectionEvent,{'data':dataList});
            }
            sourceFieldElement.trigger(Vtiger_Edit_Js.postReferenceSelectionEvent,{'data':responseData});
            console.log(responseData);
            thisInstance.setAccountPlatformOneInfo(responseData[id].info,old_src_field);
        });
    },

    //设置充值明细
    setAccountPlatformOneInfo:function(data,inputName){
        var thisInstance = this;
        var rechargesource=$("input[name='rechargesource']").val();
        var prefix='';
        var afterFix=''
        var num=$("input[name='"+inputName+"']").data('cid');
        if(inputName.indexOf('mid') != -1){
            prefix='m';
            afterFix='['+num+']';
        }
        $('input[name="'+prefix+'productid_display'+afterFix+'"]').val(data.topplatform);
        $('input[name="'+prefix+'productid'+afterFix+'"]').val(data.productid);
        $('input[name="'+prefix+'accountzh'+afterFix+'"]').val(data.accountplatform);
        $('input[name="'+prefix+'discount'+afterFix+'"]').val(data.accountrebate);
        $('select[name="'+prefix+'isprovideservice'+afterFix+'"]').val(data.isprovideservice);
        $('select[name="'+prefix+'isprovideservice'+afterFix+'"]').trigger('liszt:updated');
        $('select[name="'+prefix+'rechargetypedetail'+afterFix+'"]').val(data.rechargetypedetail);
        $('select[name="'+prefix+'rechargetypedetail'+afterFix+'"]').trigger('liszt:updated');
        $('select[name="'+prefix+'customeroriginattr'+afterFix+'"]').val(data.customeroriginattr);
        $('select[name="'+prefix+'customeroriginattr'+afterFix+'"]').trigger('liszt:updated');
        $('select[name="'+prefix+'rebatetype'+afterFix+'"]').val(data.rebatetype);
        $('select[name="'+prefix+'rebatetype'+afterFix+'"]').trigger('liszt:updated');
        $('select[name="'+prefix+'accountrebatetype'+afterFix+'"]').val(data.accountrebatetype);
        $('select[name="'+prefix+'accountrebatetype'+afterFix+'"]').trigger('liszt:updated');
        $('input[name="'+prefix+'supprebate'+afterFix+'"]').val(data.supplierrebate);

        $('input[name="'+prefix+'factorage'+afterFix+'"]').val(data.factorage);
        $('input[name="'+prefix+'activationfee'+afterFix+'"]').val(data.activationfee);
        $('input[name="'+prefix+'prestoreadrate'+afterFix+'"]').val(data.prestoreadrate);
        $('input[name="'+prefix+'mstatus'+afterFix+'"]').val(data.mstatus);
        $('select[name="'+prefix+'receivementcurrencytype'+afterFix+'"]').val(data.receivementcurrencytype);
        $('select[name="'+prefix+'receivementcurrencytype'+afterFix+'"]').trigger('liszt:updated');

        $('input[name="'+prefix+'idaccount'+afterFix+'"]').val(data.idaccount);

        $('input[name="'+prefix+'transferamount'+afterFix+'"]').val(data.transferamount);
        $('input[name="'+prefix+'taxation'+afterFix+'"]').val(data.taxation);
        $('input[name="'+prefix+'accounttransfer'+afterFix+'"]').val(data.accounttransfer);

        if(rechargesource=='Vendors'){
            //如果是外采账户
            $('input[name="'+prefix+'accountzh'+afterFix+'"]').val(data.accountzh);
            $('input[name="'+prefix+'productid_display'+afterFix+'"]').val(data.productname);
            if(prefix=='m'){
                $('input[name="'+prefix+'suppliercontractsid[display'+ num +']_display"]').val(data.contract_no);
            }else{
                $('input[name="'+prefix+'suppliercontractsid_display"]').val(data.contract_no);
            }
            $('input[name="'+prefix+'suppliercontractsid'+afterFix+'"]').val(data.suppliercontractsid);
            $('input[name="'+prefix+'signdate'+afterFix+'"]').val(data.signdate);
            var havesignedcontract="notSigned";
            if(data.modulestatus=='c_complete'){
                havesignedcontract="alreadySigned";
            }
            $('select[name="'+prefix+'havesignedcontract'+afterFix+'"]').val(havesignedcontract);
            $('select[name="'+prefix+'havesignedcontract'+afterFix+'"]').trigger('liszt:updated');

            $('select[name="'+prefix+'productservice'+afterFix+'"]')[0].options.length=0;
            var selectOption = ' selected';
            var optionStr='<option data-changeid="0" value="'+data.productid+'"'+selectOption+'>'+data.productname+'</option>';
            $('select[name="'+prefix+'productservice'+afterFix+'"]').empty();
            $('select[name="'+prefix+'productservice'+afterFix+'"]').append(optionStr);
            $('select[name="'+prefix+'productservice'+afterFix+'"]').trigger('liszt:updated');
        }
        if(rechargesource=="COINRETURN"){
            if(data.topplatform=='谷歌' || data.topplatform=='Yandex'){
                $('input[name="'+prefix+'transferamount'+afterFix+'"]').attr('readonly','readonly');
                $('input[name="'+prefix+'taxation'+afterFix+'"]').attr('readonly','readonly');
                $('input[name="'+prefix+'prestoreadrate'+afterFix+'"]').removeAttr('readonly');
            }else{
                $('input[name="'+prefix+'transferamount'+afterFix+'"]').removeAttr('readonly');
                $('input[name="'+prefix+'taxation'+afterFix+'"]').removeAttr('readonly');
                $('input[name="'+prefix+'prestoreadrate'+afterFix+'"]').attr('readonly','readonly');
            }
            thisInstance.sumCashAccounttransfer();
        }else{
            if(rechargesource=='Accounts'){
                if(data.topplatform=='谷歌' || data.topplatform=='Yandex'){
                    $('input[name="'+prefix+'transferamount'+afterFix+'"]').attr('readonly','readonly');
                    $('input[name="'+prefix+'taxation'+afterFix+'"]').attr('readonly','readonly');
                    $('input[name="'+prefix+'prestoreadrate'+afterFix+'"]').removeAttr('readonly');
                }else{
                    $('input[name="'+prefix+'transferamount'+afterFix+'"]').removeAttr('readonly');
                    $('input[name="'+prefix+'taxation'+afterFix+'"]').removeAttr('readonly');
                    $('input[name="'+prefix+'prestoreadrate'+afterFix+'"]').attr('readonly','readonly');
                }
            }
            if(rechargesource=='Vendors'){
                if(data.productname=='谷歌' || data.productname=='Yandex'){
                    $('input[name="'+prefix+'transferamount'+afterFix+'"]').attr('readonly','readonly');
                    $('input[name="'+prefix+'taxation'+afterFix+'"]').attr('readonly','readonly');
                    $('input[name="'+prefix+'prestoreadrate'+afterFix+'"]').removeAttr('readonly');
                }else{
                    $('input[name="'+prefix+'transferamount'+afterFix+'"]').removeAttr('readonly');
                    $('input[name="'+prefix+'taxation'+afterFix+'"]').removeAttr('readonly');
                    $('input[name="'+prefix+'prestoreadrate'+afterFix+'"]').attr('readonly','readonly');
                }
            }
            thisInstance.actualtotalrechargeCalc();
            thisInstance.aggregateSummary();

            $('input[name="'+prefix+'prestoreadrate'+afterFix+'"]').trigger("keyup");
        }
    },

	//提交验证
	registerRecordPreSaveEvent : function(form) {
		var thisInstance = this;
		var editViewForm = this.getForm();

		editViewForm.on(Vtiger_Edit_Js.recordPreSave,function(e,data){
            var totalrechargeValue=0;
            $('input[name^="refillapptotal["]').each(function(key,value){
                totalrechargeValue=thisInstance.FloatAdd(totalrechargeValue,$(value).val());
            });
            totalrechargeValue=totalrechargeValue*1;
            var contractamount=$('input[name="contractamount"]').val();
            $("input[name='totalrecharge']").val(totalrechargeValue.toFixed(2));
            var actualtotalrecharge=$('input[name="actualtotalrecharge"]').val();
            var rechargesource=$('input[name="rechargesource"]').val();
            if(rechargesource=='Vendors' || rechargesource=='PreRecharge' || rechargesource=='NonMediaExtraction' || rechargesource=='TECHPROCUREMENT'){
                if(thisInstance.checkSupplierContantAmount()){
                    var params = {text: app.vtranslate(), title: app.vtranslate('充值金额大于采购合同金额!不允许提交!')};
                    Vtiger_Helper_Js.showPnotify(params);
                    e.preventDefault();
                    return false;
                }
            }
            var actualtotalreceivables=$('input[name="actualtotalreceivables"]').val();
            if(actualtotalreceivables&&isNaN(actualtotalreceivables)){
                var params = {text: app.vtranslate(), title: app.vtranslate('实际应付款总额必须为数字（不要加逗号）!')};
                Vtiger_Helper_Js.showPnotify(params);
                e.preventDefault();
                return false;
            }

            var proudctid=$('input[name="productid"]').val();
            var proudctidobj=$('input[name^="mproductid["]');
            var mproductid=thisInstance.formDataArray(proudctidobj);
            var productidLength=mproductid.length;
            mproductid.push(proudctid);
            var flag=false;
            if($.inArray("379262",mproductid)!=-1 && productidLength>0){
                $.each(mproductid,function(key,value){
                    if(value!="379262"){
                        flag=true;
                        return false;
                    }
                });
            }
            if(flag){
                var params = {text: app.vtranslate(), title: app.vtranslate('有今日头条的,不能和其他产品一起提交!')};
                Vtiger_Helper_Js.showPnotify(params);
                e.preventDefault();
                return false;
            }
            /*if(rechargesource=='Vendors' || rechargesource=='TECHPROCUREMENT' || rechargesource=='PreRecharge'){
                var productids=','+$('select[name="productservice"]').val()+',';
                var mproductservice=$('select[name^="mproductservice["]');
                var flag=false;
                if(mproductservice.length>0){
                    $.each(mproductservice,function(pkey,pvalue){
                        var tempValue=','+$(pvalue).val()+',';
                        if(productids.indexOf(tempValue)==-1){
                            productids+=tempValue;
                        }else{
                            flag=true;
                            return false;
                        }
                    });
                }
                if(flag){
                    var params = {text: app.vtranslate(), title: app.vtranslate('服务产品重复,不允许重复提交!')};
                    Vtiger_Helper_Js.showPnotify(params);
                    e.preventDefault();
                    return false;
                }
            }*/
            if(rechargesource=='Vendors' || rechargesource=='Accounts'){
                var usecontractamount=$('input[name="usecontractamount"]').val();
                if(contractamount*1>0){
                    var actualtotalrecharge=$('input[name="actualtotalrecharge"]').val();
                    if(thisInstance.FloatSub(contractamount,thisInstance.FloatAdd(usecontractamount,actualtotalrecharge))*1<0){
                        var params = {text: app.vtranslate(), title: app.vtranslate('充值金额大于合同金额,不能提交!')};
                        Vtiger_Helper_Js.showPnotify(params);
                        e.preventDefault();
                        return false;
                    }
                }
            }
            var refillapptotals=$('input[name^="refillapptotal["]');
            var flag=false;
            var refillapptotalsmsg='';
            $.each(refillapptotals,function(pkey,pvalue){
                if($(pvalue).val()==0){
                    refillapptotalsmsg='回款中,金额必需大于0,若该笔回款不需要请删除!';
                    flag=true;
                    return false;
                }
                var dataid=$(pvalue).data('id');
                var allowrefillapptotal=$('input[name="allowrefillapptotal['+dataid+']"]').val();
                if(thisInstance.FloatSub($(pvalue).val(),allowrefillapptotal)>0){
                    refillapptotalsmsg='回款中,使用金额'+$(pvalue).val()+'不能大于可使用金额'+allowrefillapptotal+'!';
                    flag=true;
                    return false;
                }
            });
            if(flag){
                var params = {text: app.vtranslate(), title: app.vtranslate(refillapptotalsmsg)};
                Vtiger_Helper_Js.showPnotify(params);
                e.preventDefault();
                return false;
            }
            if(rechargesource=='OtherProcurement'){
                var purchaseprice=$('input[name="purchaseprice"]').val();
                if(purchaseprice<=0){
                    var params = {text: app.vtranslate(), title: app.vtranslate('采购价必需有效!')};
                    Vtiger_Helper_Js.showPnotify(params);
                    e.preventDefault();
                    return false;
                }
                var purchasequantity=$('input[name="purchasequantity"]').val();
                if(purchasequantity<=0){
                    var params = {text: app.vtranslate(), title: app.vtranslate('采购数量必需有效!')};
                    Vtiger_Helper_Js.showPnotify(params);
                    e.preventDefault();
                    return false;
                }
                var purchaseprices=$('input[name^="mpurchaseprice["]');
                var flag=false;
                purchaseprices.each(function(key,value){
                    if($(value).val()<=0){
                        flag=true;
                        return false;
                    }
                });
                if(flag){
                    var params = {text: app.vtranslate(), title: app.vtranslate('采购价必需有效!')};
                    Vtiger_Helper_Js.showPnotify(params);
                    e.preventDefault();
                    return false;
                }
                var purchasequantitys=$('input[name^="mpurchasequantity["]');
                var flag=false;
                purchasequantitys.each(function(key,value){
                    if($(value).val()<=0){
                        flag=true;
                        return false;
                    }
                });
                if(flag){
                    var params = {text: app.vtranslate(), title: app.vtranslate('采购价必需有效!')};
                    Vtiger_Helper_Js.showPnotify(params);
                    e.preventDefault();
                    return false;
                }
                var effectivestartaccount = $('input[name=expecteddatepayment]').val();
                var effectiveendaccount = $('input[name=expectedpaymentdeadline]').val();
                if ((new Date(effectivestartaccount.replace(/-/g,'\/')))>(new Date(effectiveendaccount.replace(/-/g,'\/')))) {
                    var  params = {text : app.vtranslate(),title : app.vtranslate('期望支付开始日期大于期望支付截止日期')};
                    Vtiger_Helper_Js.showPnotify(params);
                    e.preventDefault();
                    return false;
                }

            }
            if(rechargesource=='TECHPROCUREMENT') {
                thisInstance.totalreceivablesCalcOther();
                var totalreceivables=$('input[name="totalreceivables"]').val();
                if (thisInstance.FloatSub(totalrechargeValue, totalreceivables) != 0) {
                    Vtiger_Helper_Js.showMessage({type: 'error', text: '使用回款总额不等于应付款总额,请重新修改!'});
                    e.preventDefault();
                    return false;
                }
                if(contractamount*1>0){
                    if(thisInstance.FloatSub(contractamount,totalreceivables)*1<0){
                        var params = {text: app.vtranslate(), title: app.vtranslate('充值金额大于合同金额,不能提交!')};
                        Vtiger_Helper_Js.showPnotify(params);
                        e.preventDefault();
                        return false;
                    }
                }
                var purchasecost=$('input[name="purchasecost"]').val();
                if(thisInstance.FloatSub(purchasecost,totalreceivables)*1<0){
                    var params = {text: app.vtranslate(), title: app.vtranslate('充值金额大于外采成本,不能提交!')};
                    Vtiger_Helper_Js.showPnotify(params);
                    e.preventDefault();
                    return false;
                }
                var params={};
                params.data= {
                    'module':'RefillApplication',
                    'mode':'checkTechprocurement',
                    'action':'BasicAjax',
                    'salesorderid':$('input[name="salesorderid"]').val(),
                    'totalreceivables':totalreceivables
                };
                params.async=false;
                var flag=false;
                AppConnector.request(params).then(
                    function(data){
                        if(data.success){
                            if(data.result){
                                flag=true;
                            }
                        }
                    },
                    function(error){}
                );
                if(flag){
                    var params = {text: app.vtranslate(), title: app.vtranslate('充值金额大于外采金额,不能提交!')};
                    Vtiger_Helper_Js.showPnotify(params);
                    e.preventDefault();
                    return false;
                }

            }else if(rechargesource=='NonMediaExtraction'){
                thisInstance.actualtotalrechargeCalcNonMedia();
                var actualtotalrecharge=$('input[name="actualtotalrecharge"]').val();
                var totalrecharge=$('input[name="totalrecharge"]').val();
                if (thisInstance.FloatSub(totalrecharge, actualtotalrecharge) > 0) {
                    Vtiger_Helper_Js.showMessage({type: 'error', text: '使用回款总额大于应付款总额,请重新修改!'});
                    e.preventDefault();
                    return false;
                }
                if (thisInstance.FloatSub(actualtotalrecharge, totalrecharge) > 0) {
                    var grossadvances=$('input[name="grossadvances"]').val();
                    grossadvances=thisInstance.FloatAdd(grossadvances,totalrecharge);
                    if(thisInstance.FloatSub(grossadvances,actualtotalrecharge)!=0){
                        var params = {text: app.vtranslate(), title: app.vtranslate('有垫款,垫款金额不等!')};
                        Vtiger_Helper_Js.showPnotify(params);
                        e.preventDefault();
                        return false;
                    }
                    var expcashadvances = $('input[name=expcashadvances]').val();
                    if (expcashadvances == '') {
                        var params = {text: app.vtranslate(), title: app.vtranslate('有垫款,垫款预计回款日期必填!')};
                        Vtiger_Helper_Js.showPnotify(params);
                        e.preventDefault();
                        return false;
                    }
                    var currentDate = new Date().getFullYear() + '\/' + (new Date().getMonth() + 1) + '\/' + new Date().getDate();
                    if ((new Date(expcashadvances.replace(/-/g, '\/'))) < (new Date(currentDate))) {
                        var params = {text: app.vtranslate(), title: app.vtranslate('垫款预计回款日期应大于当前日期!')};
                        Vtiger_Helper_Js.showPnotify(params);
                        e.preventDefault();
                        return false;
                    }
                }
                var contractamount=$('input[name="contractamount"]').val();
                var usecontractamount=$('input[name="usecontractamount"]').val();

                if(contractamount*1>0){
                    usecontractamount=thisInstance.FloatAdd(usecontractamount,totalreceivables);
                    if(thisInstance.FloatSub(usecontractamount,contractamount)>0){
                        var params = {text: app.vtranslate(), title: app.vtranslate('累计已充值金额大于合同金额不允许提交!')};
                        Vtiger_Helper_Js.showPnotify(params);
                        e.preventDefault();
                        return false;
                    }
                }

                //有使用回款金额和垫款的必须等于应收款总额
                if($('input[name="totalrecharge"]').val()&&$('input[name="grossadvances"]').val()&&$('input[name="actualtotalrecharge"]').val()){
                    var totalAmount=thisInstance.FloatAdd($('input[name="totalrecharge"]').val(),$('input[name="grossadvances"]').val());
                    if(thisInstance.FloatSub(totalAmount,$('input[name="actualtotalrecharge"]').val())!=0){
                        var params = {text: app.vtranslate(), title: app.vtranslate('使用回款总额和合计垫款金额相加必须等于应收款总额')};
                        Vtiger_Helper_Js.showPnotify(params);
                        e.preventDefault();
                        return false;
                    }
                }

                if($('select[name="isthrowtime"]').val()=='yes'&&$('input[name="throwtime"]').val()==''){
                    var params = {text: app.vtranslate(), title: app.vtranslate('投放期间不能为空!')};
                    Vtiger_Helper_Js.showPnotify(params);
                    e.preventDefault();
                    return false;
                }

            }else if(rechargesource=='COINRETURN'){
                thisInstance.sumCashAccounttransfer();
                var totalcashin=$('input[name="totalcashin"]').val();//合计转入现金
                var totalcashtransfer=$('input[name="totalcashtransfer"]').val();//合计转出现金
                //var totaltransfertoaccount=$('input[name="totaltransfertoaccount"]').val();//合计转入账户币
                //var totalturnoverofaccount=$('input[name="totalturnoverofaccount"]').val();//合计转出账户币
                if(totalcashin<=0){
                    var params = {text: app.vtranslate(), title: app.vtranslate('转入,转出必需大于0!')};
                    Vtiger_Helper_Js.showPnotify(params);
                    e.preventDefault();
                    return false;
                }
                if(thisInstance.FloatSub(totalcashin,totalcashtransfer)!=0){
                    var params = {text: app.vtranslate(), title: app.vtranslate('转入,转出现金不等,无法进行下一步操作!')};
                    Vtiger_Helper_Js.showPnotify(params);
                    e.preventDefault();
                    return false;
                }
                /*if(thisInstance.FloatSub(totaltransfertoaccount,totalturnoverofaccount)!=0){
                    var params = {text: app.vtranslate(), title: app.vtranslate('转入,转出账户币不等,无法进行下一步操作!')};
                    Vtiger_Helper_Js.showPnotify(params);
                    e.preventDefault();
                    return false;
                }*/
                var did=$('input[name="did"]').val();
                var midobj=$('input[name^="mid["]');
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
                    var params = {text: app.vtranslate(), title: app.vtranslate('转入,转出ID重复不允许提交!')};
                    Vtiger_Helper_Js.showPnotify(params);
                    e.preventDefault();
                    return false;
                }
            }else if(rechargesource=='INCREASE'){
                var servicecontractsid=$('input[name="mservicecontractsid"]').val();
                var mservicecontractsid=[servicecontractsid];
                var flag=false;
                $.each($('input[name^="mmservicecontractsid\["]'),function(key,value){
                    var thisValue=$(value).val();
                    if($.inArray(thisValue,mservicecontractsid)!=-1){
                        flag=true;
                        return false
                    }else{
                        mservicecontractsid.push(thisValue);
                    }
                });
                if(flag){
                    var params = {'text':'合同重复选择!!!', 'title': ''};
                    Vtiger_Helper_Js.showPnotify(params);
                    return false;
                }
            }else if(rechargesource=='contractChanges'){
                var Detailrecords=$('input[name="contractChangeApplication\[\]"]:checkbox:checked');
                var lengths=Detailrecords.length;
                // 验证变
                var isfalse=0;
                if(lengths>0){
                    $.each(Detailrecords, function (key, value) {
                        if($(this).data("error")==1){
                            isfalse=1;
                        }
                    });
                }
                // 包含错误的数据屏蔽提交 合同变更编辑时会走到这里
                if(isfalse==1){
                    Vtiger_Helper_Js.showMessage({type: 'error', text: '标红充值单不满足变更条件，可能原因：充值单流程未完成/充值单已关联回款。请取消勾选后再提交审核!'});
                    return false;
                }
                var isautoclose=$("input[name='isautoclose']").val();
                var  contractamountrecharged=parseFloat($("input[name='contractamountrecharged']").val());
                var  actualtotalrecharge=parseFloat($("input[name='actualtotalrecharge']").val());
                var  totalreceivables=parseFloat($("input[name='totalreceivables']").val());
                var  newcontractamount=parseFloat($("input[name='newcontractamount']").val());
                var  changecontracttype=$("select[name='changecontracttype']").val();
                // 为非框架合同且目标合同金额大于零 走下面判断 (目标合同为框架合同或者合同金额为空/0 ) 只有服务合同有框架合同和非框架合同
                if(changecontracttype=='ServiceContracts' && isautoclose == 1 && newcontractamount >0){
                    //充值来源为 退币转冲的也不需要验证金额
                    if((contractamountrecharged+actualtotalrecharge)>newcontractamount && $("select[name='oldrechargesource']").val()!='COINRETURN'){
                        Vtiger_Helper_Js.showMessage({type: 'error', text: '目标合同已充值合同金额加变更充值应收款总额应小于等于合同金额!'});
                        return false;
                    }
                }
                //如果是供应商则校验数据是否可以保存
                if(changecontracttype=='SupplierContracts' && newcontractamount >0){
                    if((contractamountrecharged+totalreceivables)>newcontractamount){
                        Vtiger_Helper_Js.showMessage({type: 'error', text: '目标合同已充值合同金额加变更充值应付款总额应小于等于合同金额!'});
                        return false;
                    }
                }
            }else{
                if(rechargesource=='Accounts'){
                    var productids=','+$('input[name="did"]').val()+',';
                    var mproductservice=$('input[name^="mid["]');
                    var flag=false;
                    if(mproductservice.length>0){
                        $.each(mproductservice,function(pkey,pvalue){
                            var tempValue=','+$(pvalue).val()+',';
                            if(productids.indexOf(tempValue)==-1){
                                productids+=tempValue;
                            }else{
                                flag=true;
                                return false;
                            }
                        });
                    }
                    if(flag){
                        var params = {text: app.vtranslate(), title: app.vtranslate('ID重复,不允许重复提交!')};
                        Vtiger_Helper_Js.showPnotify(params);
                        e.preventDefault();
                        return false;
                    }
                }
                if (thisInstance.FloatSub(totalrechargeValue, actualtotalrecharge) > 0) {
                    Vtiger_Helper_Js.showMessage({type: 'error', text: '使用回款总额大于应收款总额,请重新修改!'});
                    e.preventDefault();
                    return false;
                }
                if (thisInstance.FloatSub(actualtotalrecharge, totalrechargeValue) > 0) {
                    var expcashadvances = $('input[name=expcashadvances]').val();
                    if (expcashadvances == '') {
                        var params = {text: app.vtranslate(), title: app.vtranslate('有垫款,垫款预计回款日期必填!')};
                        Vtiger_Helper_Js.showPnotify(params);
                        e.preventDefault();
                        return false;
                    }
                    var currentDate = new Date().getFullYear() + '\/' + (new Date().getMonth() + 1) + '\/' + new Date().getDate();
                    if ((new Date(expcashadvances.replace(/-/g, '\/'))) < (new Date(currentDate))) {
                        var params = {text: app.vtranslate(), title: app.vtranslate('垫款预计回款日期应大于当前日期!')};
                        Vtiger_Helper_Js.showPnotify(params);
                        e.preventDefault();
                        return false;
                    }
                }
                thisInstance.actualtotalrechargeCalc();
                var grossadvances=$('input[name="grossadvances"]').val();
                var grossadvancest=actualtotalrecharge*1-totalrechargeValue*1-grossadvances*1;
                grossadvancest=grossadvancest.toFixed(2);
                // if((rechargesource=='Vendors' || rechargesource=='Accounts' || rechargesource=='NonMediaExtraction') && grossadvancest!= 0){
                //     var params = {text: app.vtranslate(), title: app.vtranslate('垫款金额不等!')};
                //     Vtiger_Helper_Js.showPnotify(params);
                //     e.preventDefault();
                //     return false;
                // }
                var rechargetype = $('select[name=rechargetype]').val();
                if (rechargetype == 'c_refund') {
                    var transferamount = $.trim($('input[name=transferamount]').val());
                    if (!parseFloat(transferamount)) {
                        Vtiger_Helper_Js.showMessage({type: 'error', text: '类型为退款时，转账金额不能为空'});
                        e.preventDefault();
                    }
                }

                $('.t_mrechargetype').each(function () {
                    var tt = $(this).val();
                    if (tt == 'c_refund') {
                        var tt_name = $(this).attr('name');
                        tt_name = tt_name.replace('mrechargetype', '');
                        tt_name = 'mtransferamount' + tt_name;
                        var v = $('input[name="' + tt_name + '"]').val();
                        if (!parseFloat(v)) {
                            Vtiger_Helper_Js.showMessage({type: 'error', text: '类型为退款时，转账金额不能为空'});
                            e.preventDefault();
                        }
                    }
                });
            }
            if(rechargesource=='Accounts'){
                var receivedstatus=$('select[name="receivedstatus"]').val();
                if(receivedstatus=='virtualrefund'){
                    var grossadvances = $('input[name="grossadvances"]').val();
                    if(grossadvances>0){
                        Vtiger_Helper_Js.showMessage({type: 'error', text: '赠款充值,不能走垫款流程!'});
                        return false;
                    }
                    var actualtotalrecharge=$('input[name="actualtotalrecharge"]').val();
                    var totalrecharge=$('input[name="totalrecharge"]').val();
                    if(totalrecharge!=actualtotalrecharge){
                        Vtiger_Helper_Js.showMessage({type: 'error', text: '赠款充值,不能走垫款流程!'});
                        return false;
                    }
                }
            }
            if(rechargesource=='Vendors' || rechargesource=='Accounts'){
                var grossadvances = $('input[name="grossadvances"]').val();
                var accountid=$('input[name="accountid"]').val();
                if(grossadvances>0){
                    var grossadvancesdata=thisInstance.checkAuditInformation(accountid,grossadvances);
                    if(grossadvancesdata.flag){
                        if(confirm(grossadvancesdata.msg)==false){
                            return false;
                        }
                    }
                }
            }
		});

	},
    formDataArray:function(fieldOBJ){
        var returnValue=[];
        $.each(fieldOBJ.serializeArray(), function(i, field){
            if(field.value>0){
                returnValue.push(field.value);
            }
        });
        return returnValue;
    },
    //检测充值金额是否大于合同总额
    checkSupplierContantAmount:function(){
        var thisInstance=this;
	    var params={};
	    var msuppliercontractsidobj=$('input[name^="msuppliercontractsid["]')
        var msuppliercontractsid=thisInstance.formDataArray(msuppliercontractsidobj);
        params.data= {'module':'RefillApplication',
            'mode':'AmountRepaidContract',
            'action':'BasicAjax',
            'data':$('#EditView').serializeFormData(),
            'record':$('input[name="record"]').val(),
            'msuppliercontractsid':msuppliercontractsid
        };
        params.async=false;
        var flag=false;
        AppConnector.request(params).then(
            function(data){
                if(data.success){
                    if(data.result){
                        flag=true;
                    }
                }
            },
            function(error){}
        );
        return flag;
    },
    //增加查看历史账号按钮
    getHistoryAccountzh:function(num){
        var thisInstance = this;
		var id = "#historyAccountzh_";
		if(num != undefined){
            id += num;
		}
        jQuery(id).on('click',function(e){
            var data_num = $(this).parents(".Duplicates").attr("data-num");
            var accountid = $('input[name="accountid"]').val();
            if(accountid == ""){
                Vtiger_Helper_Js.showMessage({type:'error',text:'客户不能为空'});
                return;
            }
            var duplicates = $(this).parents(".Duplicates");
            var topplatform = "";
            if(duplicates && duplicates.length > 0){
                topplatform = duplicates.find('select[name="mtopplatform['+data_num+']"]').val();
			}else{
                topplatform = $('select[name="topplatform"]').val();
			}
            if(topplatform == ""){
                Vtiger_Helper_Js.showMessage({type:'error',text:'充值平台不能为空'});
                return;
            }

			var parm ="&accountid="+accountid+"&topplatform="+topplatform;
            app.showModalWindow("",'index.php?module=RefillApplication&view=Ajaxcode&mode=getHistoryAccountzh'+parm, function() {

                jQuery('input[name="optionsRadios"]').on('change',function(e){
                    var accountzh=$(this).val();
                    var did=$(this).attr("data-did");
                    $('.blockUI').find('.close').trigger('click');
                    if(data_num !=undefined && data_num > 0){
                        $('input[name="maccountzh\['+data_num+'\]"]').val(accountzh).blur();
                        $('input[name="mid\['+data_num+'\]"]').val(did);
					}else{
                        $('input[name="accountzh"]').val(accountzh).blur();
                        $('input[name="did"]').val(did);
					}
                });
            });
        });
    },
    delbuttonnewinvoicerayment:function(){
        var thisInstance=this;
        $('#EditView').on('click','.delbuttonnewinvoicerayment',function(){
            var id=$(this).data("id");
            var message='确定要删除该回款吗？';
            var msg={
                'message':message
            };
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){

                $('.newinvoicerayment_tab'+id).remove();
                var totalrechargeValue=0;
                $('input[name^="refillapptotal["]').each(function(key,value){
                    totalrechargeValue=thisInstance.FloatAdd(totalrechargeValue,$(value).val());
                });
                totalrechargeValue=totalrechargeValue*1
                $("input[name='totalrecharge']").val(totalrechargeValue.toFixed(2));

            },function(error, err) {});


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
    instancethis:function(){
        var thisInstance=this;
        //$("input[name='totalrecharge']").attr("readonly","readonly");
        //$("input[name='actualtotalrecharge']").attr("readonly","readonly");
        //$('#RefillApplication_editView_fieldName_accountid_clear').parent().remove();
        //$('#RefillApplication_editView_fieldName_accountid_select').parent().remove();
        $('#EditView').on('change','input[name^="refillapptotal["]',function(){
            var id=$(this).data('id');
            thisInstance.formatNumber($(this));
            var allowinvoicetotal=$('input[name="allowrefillapptotal['+id+']"]').val();
            var thisValue=$(this).val();
            //thisValue=thisInstance.FloatSub(thisValue,allowinvoicetotal)>0?allowinvoicetotal:thisValue;

            //$(this).val(thisValue);
            if(thisInstance.FloatSub(thisValue,allowinvoicetotal)>0){
                //var params = {text: app.vtranslate(), title: app.vtranslate('使用金额大于可使用金额!')};
                $(this).attr('data-content','<font color="red">使用金额大于可使用金额!</font>');
                $(this).popover('show');
                //Vtiger_Helper_Js.showPnotify(params);
                return false;
            }else{
                $(this).popover('destroy');
            }
            var totalrechargeValue=0;
            $('input[name^="refillapptotal["]').each(function(key,value){
                totalrechargeValue=thisInstance.FloatAdd(totalrechargeValue,$(value).val());
            });
            if($('input[name="rechargesource"]').val()=='TECHPROCUREMENT'){
                var occupationamount=$('input[name="occupationamount"]').val();
                var purchasecost=$('input[name="purchasecost"]').val();
                var diffpurchase=thisInstance.FloatSub(purchasecost,occupationamount);
                if(diffpurchase>0){
                    var diffValue=thisInstance.FloatSub(diffpurchase,totalrechargeValue);
                    if(diffValue<0){
                        var thisValue=totalrechargeValue*1+diffValue*1;
                        $(this).val(thisValue);
                        totalrechargeValue=thisValue;
                    }
                }
            }
            totalrechargeValue=totalrechargeValue*1;
            $("input[name='totalrecharge']").val(totalrechargeValue.toFixed(2));
            thisInstance.aggregateSummary();
        })
    },
    getAccountPlatform:function(accountid,flag){
        var record=jQuery('input[name="record"]').val();
        var args=flag||0;
        record=record>0?record:0;
        var rechargesource=$('input[name="rechargesource"]').val();
        var thisInstance=this;
        var params = {
            'module':'RefillApplication',
            'action':'BasicAjax',
            'record':accountid,
            'mode':'getAccountPlatform'
        };
        AppConnector.request(params).then(
            function(data){
                if(data.success){
                    record==0&&thisInstance.clearIdInfo();
                    var did=$('select[name="did"]').val();
                    $('select[name="did"]')[0].options.length=0;
                    if(data.result.length>0){
                        thisInstance.idtopplatform=data.result;
                        var afteraction=$('input[name="action"]');
                        var optionStr='';
                        $(data.result).each(function(key,value){
                           var selectOption=(record==0&&key==0)?' selected':((record>0&&did==value.idaccount)?' selected':'');
                            if((record==0 || flag==1)&&key==0) {
                                $('input[name="productid_display"]').val(value.topplatform);
                                $('input[name="productid"]').val(value.productid);
                                $('input[name="accountzh"]').val(value.accountplatform);
                                $('input[name="discount"]').val(value.accountrebate);
                                $('select[name="isprovideservice"]').val(value.isprovideservice);
                                $('select[name="isprovideservice"]').trigger('liszt:updated');
                                $('select[name="rechargetypedetail"]').val(value.rechargetypedetail);
                                $('select[name="rechargetypedetail"]').trigger('liszt:updated');
                                $('select[name="customeroriginattr"]').val(value.customeroriginattr);
                                $('select[name="customeroriginattr"]').trigger('liszt:updated');
                                $('select[name="rebatetype"]').val(value.rebatetype);
                                $('select[name="rebatetype"]').trigger('liszt:updated');
                                $('select[name="accountrebatetype"]').val(value.accountrebatetype);
                                $('select[name="accountrebatetype"]').trigger('liszt:updated');
                                $('input[name="supprebate"]').val(value.supplierrebate);
                                if(value.topplatform=='谷歌' || value.topplatform=='Yandex'){
                                    $('input[name="transferamount"]').attr('readonly','readonly');
                                    $('input[name="taxation"]').attr('readonly','readonly');
                                    $('input[name="prestoreadrate"]').removeAttr('readonly');
                                }else{
                                    $('input[name="transferamount"]').removeAttr('readonly');
                                    $('input[name="taxation"]').removeAttr('readonly');
                                    $('input[name="prestoreadrate"]').attr('readonly','readonly');
                                }
                                if(rechargesource=='COINRETURN'){
                                    $('input[name="mproductid_display[1]"]').val(value.topplatform);
                                    $('input[name="mproductid[1]"]').val(value.productid);
                                    $('input[name="maccountzh[1]"]').val(value.accountplatform);
                                    $('input[name="mdiscount[1]"]').val(value.accountrebate);
                                    $('select[name="misprovideservice[1]"]').val(value.isprovideservice);
                                    $('select[name="misprovideservice[1]"]').trigger('liszt:updated');
                                    $('select[name="mrechargetypedetail[1]"]').val(value.rechargetypedetail);
                                    $('select[name="mrechargetypedetail[1]"]').trigger('liszt:updated');
                                    $('select[name="mcustomeroriginattr[1]"]').val(value.customeroriginattr);
                                    $('select[name="mcustomeroriginattr[1]"]').trigger('liszt:updated');
                                    $('select[name="mrebatetype[1]"]').val(value.rebatetype);
                                    $('select[name="mrebatetype[1]"]').trigger('liszt:updated');
                                    $('select[name="maccountrebatetype[1]"]').val(value.accountrebatetype);
                                    $('select[name="maccountrebatetype[1]"]').trigger('liszt:updated');
                                }
                            }
                            afteraction.after('<input type="hidden" name="mastersupprebate['+value.accountplatformid+']" value="'+value.supplierrebate+'" disabled>');
                            optionStr+='<option data-changeid="'+key+'" value="'+value.idaccount+'"'+selectOption+'>'+value.idaccount+'</option>';
                        })
                        $('select[name="did"]').empty();
                        $('select[name="did"]').append(optionStr);
                        if(record==0 && rechargesource=='COINRETURN'){
                            $('select[name="mid[1]"]').empty();
                            $('select[name="mid[1]"]').append(optionStr);
                            $('select[name="mid[1]"]').trigger('liszt:updated');
                        }
                        if(record>0){
                            $.each($('select[name^="mid["]'),function(pkey,pvalue){
                                //var optionStr='';
                                var did=$(pvalue).val();
                                var cid=$(pvalue).data('cid');
                                $('select[name="mid['+cid+']"]')[0].options.length=0
                                /*$(data.result).each(function(key,value){
                                    var selectOption=did==value.idaccount?' selected':'';

                                    //afteraction.after('<input type="hidden" name="mastersupprebate['+value.accountplatformid+']" value="'+value.supplierrebate+'" data-id="22222">');
                                    optionStr+='<option data-changeid="'+key+'" value="'+value.idaccount+'"'+selectOption+'>'+value.idaccount+'</option>';
                                });*/
                                $('select[name="mid['+cid+']"]').append(optionStr);
                                $('select[name="mid['+cid+']"]').val(did);
                                $('select[name="mid['+cid+']"]').trigger('liszt:updated');
                            });
                        }
                    }else{
                        Vtiger_Helper_Js.showMessage({type:'error',text:'客户没有相关的账户信息，请联系相关人员添加!'});
                        thisInstance.idtopplatform={};
                    }
                    $('select[name="did"]').trigger('liszt:updated');
                }
            },
            function(error){}
        );
    },
    /**
     * 清空相关ID信息
     */
    clearIdInfo:function(){
        //$('input[name="mastersupprebate"]').remove();
        $('input[name^="mastersupprebate["]').remove();
    },
    /**
     * id更改触发相关信息
     */
    changeDID:function(){
        var thisInstance=this;
        $('#EditView').on("change",'select[name="did"],select[name^="mid["]',function(){
            var changeid=$(this).find("option:selected").data("changeid");

            var cid=$(this).data('cid');
            cid=cid>0?cid:0;
            var rechargesource=$('input[name="rechargesource"]').val();
            if(rechargesource=='Vendors'){

                thisInstance.productserviceChangeDid(changeid,cid);
                return ;
            }
            var changeSelectedId=thisInstance.idtopplatform[changeid];
            if(changeSelectedId==undefined){
                return false;
            }
            var topplatformField=cid==0?'productid_display':'mproductid_display['+cid+']';
            var productidField=cid==0?'productid':'mproductid['+cid+']';
            var accountzhField=cid==0?'accountzh':'maccountzh['+cid+']';
            var discountField=cid==0?'discount':'mdiscount['+cid+']';
            var mastersupprebateField=cid==0?'supprebate':'msupprebate['+cid+']';
            var transferamountField=cid==0?'transferamount':'mtransferamount['+cid+']';
            var taxationField=cid==0?'taxation':'mtaxation['+cid+']';
            var prestoreadrateField=cid==0?'prestoreadrate':'mprestoreadrate['+cid+']';
            var isprovideserviceField=cid==0?'isprovideservice':'misprovideservice['+cid+']';
            var rechargetypedetailField=cid==0?'rechargetypedetail':'mrechargetypedetail['+cid+']';
            var customeroriginattrField=cid==0?'customeroriginattr':'mcustomeroriginattr['+cid+']';
            var rebatetypeField=cid==0?'rebatetype':'mrebatetype['+cid+']';
            var accountrebatetypeField=cid==0?'accountrebatetype':'maccountrebatetype['+cid+']';
            thisInstance.googleReadonlyField(changeSelectedId.topplatform,cid);//谷歌平台切换
            $('input[name="'+topplatformField+'"]').val(changeSelectedId.topplatform);
            $('input[name="'+productidField+'"]').val(changeSelectedId.productid);
            $('input[name="'+accountzhField+'"]').val(changeSelectedId.accountplatform);
            $('input[name="'+discountField+'"]').val(changeSelectedId.accountrebate);
            $('input[name="'+mastersupprebateField+'"]').val(changeSelectedId.supplierrebate);
            $('select[name="'+isprovideserviceField+'"]').val(changeSelectedId.isprovideservice);
            $('select[name="'+isprovideserviceField+'"]').trigger('liszt:updated');
            $('select[name="'+rechargetypedetailField+'"]').val(changeSelectedId.rechargetypedetail);
            $('select[name="'+rechargetypedetailField+'"]').trigger('liszt:updated');
            $('select[name="'+customeroriginattrField+'"]').val(changeSelectedId.customeroriginattr);
            $('select[name="'+customeroriginattrField+'"]').trigger('liszt:updated');
            $('select[name="'+rebatetypeField+'"]').val(changeSelectedId.rebatetype);
            $('select[name="'+rebatetypeField+'"]').trigger('liszt:updated');
            $('select[name="'+accountrebatetypeField+'"]').val(changeSelectedId.accountrebatetype);
            $('select[name="'+accountrebatetypeField+'"]').trigger('liszt:updated');
            thisInstance.eraseValueRecharge(cid);
            thisInstance.actualtotalrechargeCalc();
        })
    },
    googleReadonlyField:function(topplatform,cid){
        var transferamountField=cid==0?'transferamount':'mtransferamount['+cid+']';
        var taxationField=cid==0?'taxation':'mtaxation['+cid+']';
        var prestoreadrateField=cid==0?'prestoreadrate':'mprestoreadrate['+cid+']';
        if(topplatform=='谷歌' || topplatform=='Yandex'){
            $('input[name="'+transferamountField+'"]').attr('readonly','readonly');
            $('input[name="'+taxationField+'"]').attr('readonly','readonly');
            $('input[name="'+prestoreadrateField+'"]').removeAttr('readonly');
        }else{
            $('input[name="'+transferamountField+'"]').removeAttr('readonly');
            $('input[name="'+taxationField+'"]').removeAttr('readonly');
            $('input[name="'+prestoreadrateField+'"]').attr('readonly','readonly');
        }
    },
    eraseValueRecharge:function(cid){
        var prestoreadrate=cid==0?'prestoreadrate':'mprestoreadrate['+cid+']';
        var rechargeamount=cid==0?'rechargeamount':'mrechargeamount['+cid+']';
        var factorage=cid==0?'factorage':'mfactorage['+cid+']';
        var totalcost=cid==0?'totalcost':'mtotalcost['+cid+']';
        var transferamount=cid==0?'transferamount':'mtransferamount['+cid+']';
        var totalgrossprofit=cid==0?'totalgrossprofit':'mtotalgrossprofit['+cid+']';
        var activationfee=cid==0?'activationfee':'mactivationfee['+cid+']';
        var dailybudget=cid==0?'dailybudget':'mdailybudget['+cid+']';
        var rebateamount=cid==0?'rebateamount':'mrebateamount['+cid+']';
        var servicecost=cid==0?'servicecost':'mservicecost['+cid+']';
        var purchaseamount=cid==0?'purchaseamount':'mpurchaseamount['+cid+']';
        var cashtransfer=cid==0?'cashtransfer':'mcashtransfer['+cid+']';
        var accounttransfer=cid==0?'accounttransfer':'maccounttransfer['+cid+']';
        $('input[name="'+prestoreadrate+'"]').val('');
        $('input[name="'+rechargeamount+'"]').val('');
        $('input[name="'+factorage+']').val('');
        $('input[name="'+totalcost+'"]').val('');
        $('input[name="'+transferamount+'"]').val('');
        $('input[name="'+totalgrossprofit+'"]').val('');
        $('input[name="'+activationfee+'"]').val('');
        $('input[name="'+dailybudget+'"]').val('');
        $('input[name="'+rebateamount+'"]').val('');
        $('input[name="'+servicecost+'"]').val('');
        $('input[name="'+purchaseamount+'"]').val('');
        $('input[name="'+cashtransfer+'"]').val('');
        $('input[name="'+accounttransfer+'"]').val('');
        $('.chzn-select').trigger('liszt:updated');
    },
    /**
     * 账号币计算
     */
    prestoreadratecalc:function(){
        var thisInstance=this;
        $('#EditView').on("keyup",'input[name="prestoreadrate"],input[name^="mprestoreadrate["]',function(){
            if($(this).attr('readonly')=='readonly'){
                return false;
            }
            var thisValue=$(this).val();
            thisValue=thisValue>0?thisValue:0;
            var cid=$(this).data("cid");
            var rechargesource=$('input[name="rechargesource"]').val();
            if(rechargesource=='PreRecharge'){
                return;
            }
            cid=cid>0?cid:0;
            var discount=cid==0?'discount':'mdiscount['+cid+']';
            var topplatform=cid==0?'productid_display':'mproductid_display['+cid+']';
            var rechargeamountField=cid==0?'rechargeamount':'mrechargeamount['+cid+']';
            var taxationField=cid==0?'taxation':'mtaxation['+cid+']';
            var transferamountField=cid==0?'transferamount':'mtransferamount['+cid+']';
            var factorageField=cid==0?'factorage':'mfactorage['+cid+']';
            var activationfeeField=cid==0?'activationfee':'mactivationfee['+cid+']';
            var receivementcurrencytypeField=cid==0?'receivementcurrencytype':'mreceivementcurrencytype['+cid+']';
            var rebatetypeField=cid==0?'rebatetype':'mrebatetype['+cid+']';
            var rebatetypeValue=$('select[name="'+rebatetypeField+'"]').val();
            var currentDiscount=$('input[name="'+discount+'"]').val();
            if(currentDiscount==''){
                //返点不是带出来的不填写
                $(this).val(0);
                return false;
            }
            currentDiscount=currentDiscount>0?currentDiscount:0;
            currentDiscount=thisInstance.FloatDiv(currentDiscount,100);
            if(rebatetypeValue=='CashBack'){
                currentDiscount=thisInstance.FloatSub(1,currentDiscount);
                var rechargeamount=thisInstance.FloatMul(thisValue,currentDiscount);
            }else{
                currentDiscount=thisInstance.FloatAdd(1,currentDiscount);
                var rechargeamount=thisInstance.FloatDiv(thisValue,currentDiscount);
            }
            var topplatform=$('input[name="'+topplatform+'"]').val();
            var taxation=0;
            if(topplatform=='谷歌' || topplatform=='Yandex'){
                //var tax=thisInstance.taxCalc(cid);
                //tax=thisInstance.FloatAdd(1,thisInstance.FloatDiv(tax,100));
                //rechargeamount=thisInstance.FloatMul(rechargeamount,tax);
                var receivementcurrencytypeValue=$('select[name="'+receivementcurrencytypeField+'"]').val();
                if(receivementcurrencytypeValue=='美金'){
                    taxation=0.0;
                }else{
                    taxation=thisInstance.FloatMul(thisValue,0.06)*1.0;
                }
                $('input[name="'+taxationField+'"]').val(taxation.toFixed(2));


            }
            var factorageValue=$('input[name="'+factorageField+'"]').val();
            var activationfeeValue=$('input[name="'+activationfeeField+'"]').val();
            /*taxation=thisInstance.FloatSub(rechargeamount,thisInstance.FloatDiv(rechargeamount,tax));
            taxation=taxation*1;//类型转换
            $('input[name="taxation"]').val(taxation.toFixed(2));*/
            $('input[name="'+rechargeamountField+'"]').val(rechargeamount.toFixed(2));
           /* var transferamountValue=thisInstance.FloatAdd(rechargeamount,taxation)
            transferamountValue=thisInstance.FloatAdd(transferamountValue,factorageValue)
            transferamountValue=thisInstance.FloatAdd(transferamountValue,activationfeeValue)
            $('input[name="'+transferamountField+'"]').val(transferamountValue);*/
            thisInstance.googleTransferamountCalc(cid);
            //thisInstance.servicecostCalc(rechargeamount,cid);


            //thisInstance.transferamountCalc(cid);
        });
        $('#EditView').on("keyup",'input[name="taxation"],input[name="factorage"],input[name="activationfee"],input[name^="mtaxation["],input[name^="mfactorage["],input[name^="mactivationfee["]',function(){
            if($(this).attr('readonly')=='readonly'){
                return false;
            }
            thisInstance.formatNumber($(this));
            var cid=$(this).data('cid');
            cid=cid>0?cid:0;
            var topplatform=cid==0?'productid_display':'mproductid_display['+cid+']';
            var topplatform=$('input[name="'+topplatform+'"]').val();
            if(topplatform=='谷歌' || topplatform=='Yandex'){
                thisInstance.googleTransferamountCalc(cid);
            }else{
                thisInstance.calcAechargeamountANDPrestoreadrate(cid);
            }

            //thisInstance.transferamountCalc(cid);
            //thisInstance.aggregateSummary();
        });
        $('#EditView').on("keyup",'input[name="rechargeamount"],input[name^="mrechargeamount["]',function(){
            if($(this).attr('readonly')=='readonly'){
                return false;
            }
            var rechargesource=$('input[name="rechargesource"]').val();

            thisInstance.formatNumber($(this));
            var cid=$(this).data('cid');
            cid=cid>0?cid:0;
            var thisValue=$(this).val();
            var prestoreadrateField=cid==0?'prestoreadrate':'mprestoreadrate['+cid+']';
            var rebatetypeField=cid==0?'rebatetype':'mrebatetype['+cid+']';
            var discountField=cid==0?'discount':'mdiscount['+cid+']';
            var discountValue=$('input[name="'+discountField+'"]').val();
            var rebatetype=$('select[name="'+rebatetypeField+'"]').val();
            if(discountValue<0){
                return false;
            }
            if(rechargesource=='PreRecharge') {
                var rechargeamount=$('input[name="rechargeamount"]').val()*1;
                var mrechargeamount=$('input[name^="mrechargeamount["]');
                $.each(mrechargeamount,function(key,value){
                    rechargeamount=thisInstance.FloatAdd(rechargeamount,$(value).val())*1;
                });
                $('input[name="totalreceivables"]').val(rechargeamount.toFixed(2));
                var prestoreadrateValue=0.0;
                if(rebatetype=='GoodsBack'){
                    discountValue=thisInstance.FloatDiv(discountValue,100);
                    discountValue=thisInstance.FloatAdd(1,discountValue);
                    prestoreadrateValue=thisInstance.FloatMul(thisValue,discountValue).toFixed(2);
                }else{
                    discountValue=thisInstance.FloatDiv(discountValue,100);
                    var cashDiscountValue=thisInstance.FloatSub(1,discountValue);
                    prestoreadrateValue=thisInstance.FloatDiv(thisValue,cashDiscountValue);
                }
                prestoreadrateValue=prestoreadrateValue*1.0;
                prestoreadrateValue=prestoreadrateValue.toFixed(2);
                $('input[name="'+prestoreadrateField+'"]').val(prestoreadrateValue);
            }

            return ;
            if(rechargesource=='Accounts' || rechargesource=='Vendors'){
                var topplatform=cid==0?'productid_display':'mproductid_display['+cid+']';
                discountValue=discountValue>0?discountValue:0;
                discountValue=thisInstance.FloatDiv(discountValue,100);
                discountValue=thisInstance.FloatAdd(1,discountValue);
                var prestoreadrate=thisInstance.FloatMul(thisValue,discountValue);
                var topplatform=$('input[name="'+topplatform+'"]').val();
                if(topplatform=='谷歌' || topplatform=='Yandex'){
                    var tax=thisInstance.taxCalc(cid);
                    tax=thisInstance.FloatAdd(1,thisInstance.FloatDiv(tax,100));//税率
                    prestoreadrate=thisInstance.FloatDiv(prestoreadrate,tax);
                }
                $('input[name="'+prestoreadrateField+'"]').val(prestoreadrate.toFixed(0));
                thisInstance.servicecostCalc(thisValue,cid);
                thisInstance.transferamountCalc(cid);
                thisInstance.aggregateSummary();
                return ;
            }else if(rechargesource=='PreRecharge') {
                var rechargeamount=$('input[name="rechargeamount"]').val()*1;
                var mrechargeamount=$('input[name^="mrechargeamount["]');
                $.each(mrechargeamount,function(key,value){
                    rechargeamount=thisInstance.FloatAdd(rechargeamount,$(value).val())*1;
                });
                $('input[name="totalreceivables"]').val(rechargeamount.toFixed(2));
            }
            discountValue=thisInstance.FloatAdd(1,thisInstance.FloatDiv(discountValue,100));
            $('input[name="'+prestoreadrateField+'"]').val(thisInstance.FloatMul(thisValue,discountValue).toFixed(2));
        });
        $('#EditView').on("keyup",'input[name="discount"],input[name^="mdiscount["]',function(){
            if($(this).attr('readonly')=='readonly'){
                return false;
            }
            thisInstance.formatNumber($(this));
            var rechargesource=$('input[name="rechargesource"]').val();
            if(rechargesource=='PreRecharge'){
                return;
            }
            var cid=$(this).data('cid');
            cid=cid>0?cid:0;
            var rechargeamountField=cid==0?'transferamount':'mtransferamount['+cid+']';
            var topplatform=cid==0?'productid_display':'mproductid_display['+cid+']';
            var topplatform=$('input[name="'+topplatform+'"]').val();
            if(topplatform=='谷歌' || topplatform=='Yandex'){
                rechargeamountField=cid==0?'prestoreadrate':'mprestoreadrate['+cid+']';
            }
            $('input[name="'+rechargeamountField+'"]').trigger('keyup');
        });
        $('#EditView').on("keyup",'input[name="rebates"],input[name^="mrebates["],input[name="grossadvances"]',function(){
            if($(this).attr('readonly')=='readonly'){
                return false;
            }
            thisInstance.formatNumber($(this));
        });
        //转款金额
        $('#EditView').on("keyup",'input[name="transferamount"],input[name^="mtransferamount["]',function(){
            if($(this).attr('readonly')=='readonly'){
                return false;
            }
            thisInstance.formatNumber($(this));
            var cid=$(this).data('cid');
            cid=cid>0?cid:0;
            var topplatform=cid==0?'productid_display':'mproductid_display['+cid+']';
            var topplatform=$('input[name="'+topplatform+'"]').val();
            if(topplatform=='谷歌' || topplatform=='Yandex'){
                return ;
            }
            var rechargesource=$('input[name="rechargesource"]').val();
            if(rechargesource=='Accounts' || rechargesource=='Vendors'){
                var cid=$(this).data('cid');
                cid=cid>0?cid:0;
                thisInstance.calcAechargeamountANDPrestoreadrate(cid);
            }else if(rechargesource=='TECHPROCUREMENT'){
                thisInstance.actualtotalrechargeCalc();
            }
        });
        $('#EditView').on("change",'select[name="tax"],select[name^="mtax["]',function(){
            return ;
            var rechargesource=$('input[name="rechargesource"]').val();
            if(rechargesource=='Accounts' || rechargesource=='Vendors'){
                var cid=$(this).data('cid');
                cid=cid>0?cid:0;
                var transferamountField=cid==0?'transferamount':'mtransferamount['+cid+']';
                $('input[name="'+transferamountField+'"]').trigger('keyup');
            }
        });
        $('#EditView').on("change keyup",'input[name="purchaseprice"],input[name^="mpurchaseprice["],input[name="purchasequantity"],input[name^="mpurchasequantity["]',function(){
            if($(this).attr('readonly')=='readonly'){
                return false;
            }
            thisInstance.formatNumber($(this));
            var cid=$(this).data('cid');
            cid=cid>0?cid:0;
            var purchasepriceField=cid==0?'purchaseprice':'mpurchaseprice['+cid+']';
            var purchasepriceValue=$('input[name="'+purchasepriceField+'"]').val();
            var purchasequantityField=cid==0?'purchasequantity':'mpurchasequantity['+cid+']';
            var purchaseamountField=cid==0?'purchaseamount':'mpurchaseamount['+cid+']';
            var purchasequantityValue=$('input[name="'+purchasequantityField+'"]').val();
            var arr=purchasequantityValue.split('.');//只有一个小数点
            if(arr.length>0){
                $('input[name="'+purchasequantityField+'"]').val(arr[0]);
            }
            purchasequantityValue=$('input[name="'+purchasequantityField+'"]').val();
            var currentValue=thisInstance.FloatMul(purchasequantityValue,purchasepriceValue)*1;

            $('input[name="'+purchaseamountField+'"]').val(currentValue.toFixed(2));
        });
        $('#EditView').on("change keyup",'input[name="purchaseamount"],input[name^="mpurchaseamount["],input[name="purchasequantity"],input[name^="mpurchasequantity["]',function(){
            if($(this).attr('readonly')=='readonly'){
                return false;
            }
            thisInstance.formatNumber($(this));
            var cid=$(this).data('cid');
            cid=cid>0?cid:0;
            thisInstance.NonMediaExtractionCalc(cid);
        });
        $('#EditView').on("change keyup",'input[name="totalgrossprofit"],input[name^="mtotalgrossprofit["]',function(){
            if($(this).attr('readonly')=='readonly'){
                return false;
            }
            var rechargesource=$('input[name="rechargesource"]').val();
            if(rechargesource!='NonMediaExtraction'){
                thisInstance.formatNumber($(this));
                return;
            }
            thisInstance.formatNumberNegative($(this));
            var cid=$(this).data('cid');
            cid=cid>0?cid:0;
            thisInstance.NonMediaExtractionCalc(cid);
        });
        $('#EditView').on("keyup",'input[name="amountpayable"],input[name^="mamountpayable["]',function(){
            if($(this).attr('readonly')=='readonly'){
                return false;
            }
            var cid=$(this).data('cid');
            cid=cid>0?cid:0;
            var topplatform=cid==0?'productid_display':'mproductid_display['+cid+']';
            var topplatform=$('input[name="'+topplatform+'"]').val();
            /*if(topplatform=='谷歌'){
                return ;
            }*/
            var rechargesource=$('input[name="rechargesource"]').val();
            //if(rechargesource=='TECHPROCUREMENT'){
                thisInstance.totalreceivablesCalcOther();
            //}
        });
        $('#EditView').on("change",'select[name="receivementcurrencytype"],select[name^="mreceivementcurrencytype["]',function(){
            if($(this).attr('readonly')=='readonly'){
                return false;
            }
            var cid=$(this).data('cid');
            cid=cid>0?cid:0;
            var topplatform=cid==0?'productid_display':'mproductid_display['+cid+']';
            var topplatform=$('input[name="'+topplatform+'"]').val();
            if(topplatform=='谷歌' || topplatform=='Yandex'){
                var rechargeamountField=cid==0?'prestoreadrate':'mprestoreadrate['+cid+']';
                $('input[name="'+rechargeamountField+'"]').trigger('keyup');
            }
        });
    },
    NonMediaExtractionCalc:function(cid){
        var thisInstance=this;
        var purchaseamountValue=$('input[name="purchaseamount"]').val();
        var purchaseamountObj=$('input[name^="mpurchaseamount["]');
        if(purchaseamountObj.length>0){
            $.each(purchaseamountObj,function (key,value) {
                purchaseamountValue=thisInstance.FloatAdd(purchaseamountValue,$(value).val());
            });
        }
        var totalgrossprofitValue=$('input[name="totalgrossprofit"]').val();
        totalgrossprofitValue=totalgrossprofitValue=='-'?0:totalgrossprofitValue
        var totalgrossprofitObj=$('input[name^="mtotalgrossprofit["]');
        if(totalgrossprofitObj.length>0){
            var thistotalgrossprofit=0;
            $.each(totalgrossprofitObj,function (key,value) {
                thistotalgrossprofit=$(value).val();
                thistotalgrossprofit=thistotalgrossprofit=='-'?0:thistotalgrossprofit;
                totalgrossprofitValue=thisInstance.FloatAdd(totalgrossprofitValue,thistotalgrossprofit);
            });
        }
        purchaseamountValue=purchaseamountValue*1;
        purchaseamountValue=purchaseamountValue>0?purchaseamountValue:0.00;
        $('input[name="totalreceivables"]').val(purchaseamountValue.toFixed(2));
        totalgrossprofitValue=totalgrossprofitValue*1;
        //totalgrossprofitValue=totalgrossprofitValue>0?totalgrossprofitValue:0.00;//允许小于0
        totalgrossprofitValue=thisInstance.FloatAdd(purchaseamountValue,totalgrossprofitValue);
        var rechargesource=$('input[name="rechargesource"]').val();
        //2021.3.1修改，非媒体外采不计算毛利
        if(rechargesource!='NonMediaExtraction'){
            $('input[name="actualtotalrecharge"]').val(totalgrossprofitValue.toFixed(2));
        }else{
            $('input[name="actualtotalrecharge"]').trigger('change');
        }
    },
    googleTransferamountCalc:function(cid){
        var thisInstance=this;
        var prestoreadrateField=cid==0?'prestoreadrate':'mprestoreadrate['+cid+']';
        var rechargeamountField=cid==0?'rechargeamount':'mrechargeamount['+cid+']';
        var transferamountField=cid==0?'transferamount':'mtransferamount['+cid+']';
        var factorageField=cid==0?'factorage':'mfactorage['+cid+']';
        var activationfeeField=cid==0?'activationfee':'mactivationfee['+cid+']';
        var taxationField=cid==0?'taxation':'mtaxation['+cid+']';
        var servicecostField=cid==0?'servicecost':'mservicecost['+cid+']';
        var mastersupprebatefield=cid==0?'supprebate':'msupprebate['+cid+']';
        var receivementcurrencytypeField=cid==0?'receivementcurrencytype':'mreceivementcurrencytype['+cid+']';
        var rechargeamount=$('input[name="'+rechargeamountField+'"]').val();
        var taxationValue=$('input[name="'+taxationField+'"]').val();
        var prestoreadrateValue=$('input[name="'+prestoreadrateField+'"]').val()*1;
        var factorageValue=$('input[name="'+factorageField+'"]').val()*1;
        var activationfeeValue=$('input[name="'+activationfeeField+'"]').val()*1;
        var mastersupprebateValue=$('input[name="'+mastersupprebatefield+'"]').val()*1;//供应商返点
        var receivementcurrencytypeValue=$('select[name="'+receivementcurrencytypeField+'"]').val()*1;
        var transferamountValue=thisInstance.FloatAdd(rechargeamount,taxationValue);
        transferamountValue=thisInstance.FloatAdd(transferamountValue,factorageValue);
        transferamountValue=thisInstance.FloatAdd(transferamountValue,activationfeeValue);
        $('input[name="'+transferamountField+'"]').val(transferamountValue.toFixed(2));
        mastersupprebateValue=thisInstance.FloatSub(100,mastersupprebateValue);
        mastersupprebateValue=thisInstance.FloatDiv(mastersupprebateValue,100);
        var servicecostValue=thisInstance.FloatMul(prestoreadrateValue,mastersupprebateValue);
        servicecostValue=thisInstance.FloatAdd(servicecostValue,taxationValue)*1.0;
        var rechargesource=$('input[name="rechargesource"]').val();
        var receivedstatusflag=false;
        if(rechargesource=='Accounts'){
            var receivedstatus=$('select[name="receivedstatus"]').val();
            if(receivedstatus=='virtualrefund'){
                servicecostValue=0.0;
                receivedstatusflag=true;
            }
        }
        $('input[name="'+servicecostField+'"]').val(servicecostValue.toFixed(2));
        var totalcostField=cid==0?'totalcost':'mtotalcost['+cid+']';
        var totalcost=thisInstance.FloatAdd(taxationValue,thisInstance.FloatAdd(factorageValue,activationfeeValue))*1;

        $('input[name="'+totalcostField+'"]').val(totalcost.toFixed(2));

        var totalgrossprofitField=cid==0?'totalgrossprofit':'mtotalgrossprofit['+cid+']';
        var servicecost=this.FloatSub(transferamountValue,servicecostValue)*1;
        if(receivedstatusflag){
            servicecost=0.0;
        }
        $('input[name="'+totalgrossprofitField+'"]').val(servicecost.toFixed(2));
        if(rechargesource=='Vendors'){
            thisInstance.totalreceivablesCalc();
        }
        thisInstance.actualtotalrechargeCalc();
        thisInstance.aggregateSummary();

    },
    getReceivedPaymentsList:function(){
        var thisInstance = this;
        $('#EditView').on('click','.rpaymentid',function(){
            var currentThis=this;
            var rpaymentid=$(this).data('receivedpaymentsid');
            if(thisInstance.isgetReceivedPayments.indexOf(rpaymentid)==-1){
                var progressIndicatorElement = jQuery.progressIndicator({ 'message' : '信息加载中...','blockInfo':{'enabled':true }});
                thisInstance.isgetReceivedPayments.push(rpaymentid);
                var params = {
                    'module': 'RefillApplication',
                    'action': 'BasicAjax',
                    'record': rpaymentid,
                    'mode': 'getReceivedPaymentsHistory'
                };
                AppConnector.request(params).then(
                    function (data) {
                        progressIndicatorElement.progressIndicator({ 'mode' : 'hide'});
                        if(data.success) {
                            var str='<table>'+
                                '<tbody><tr>'+
                                '<td style="width: 15%" nowrap><b>操作模块</b></td>'+
                                '<td style="width: 15%" nowrap><b>操作者</b></td>'+
                                '<td style="width: 20%" nowrap><b>操作时间</b></td>'+
                                '<td style="width: 20%" nowrap><b>充值申请时间</b></td>'+
                                '<td style="width: 30%" nowrap><b>回款使用情况</b></td>'+
                                '<td style="width: 20%" nowrap><b>备注</b></td>'+
                                '<tr></tr>';
                            $.each(data.result,function(key,value){
                                var typeValue=value.type==1?'工单':'充值申请单';
                                str+='<tr><td nowrap>'+typeValue+value.recordno+'</td><td nowrap>'+value.last_name+'</td>\
                                <td nowrap>'+value.matchdate+'</td>\
                                <td nowrap>'+value.createdtime+'</td>\
                                <td nowrap>'+value.detail+'</td>\
                                <td nowrap>'+value.remarks+'</td></tr>';
                                //str+='<tr><td>'+value.allowrefillapptotal+'</td><td>'+value.refillapptotal+'</td><td>'+value.createdtime+'</td></tr>';
                            });
                            str+='</tbody></table>';
                            $(currentThis).attr('title','回款使用明细');
                            $(currentThis).attr('data-original-title','回款使用明细');
                            $(currentThis).attr('data-content','<div style="max-height:200px;overflow: auto;">'+str+'</div>');
                            $(currentThis).popover('show');

                        }
                    },
                    function (error) {
                    }
                );
            }


        });
    },
    calcAechargeamountANDPrestoreadrate:function(cid){
        var thisInstance=this;
        var prestoreadrateField=cid==0?'prestoreadrate':'mprestoreadrate['+cid+']';
        var discountField=cid==0?'discount':'mdiscount['+cid+']';
        var rechargeamountField=cid==0?'rechargeamount':'mrechargeamount['+cid+']';
        var transferamountField=cid==0?'transferamount':'mtransferamount['+cid+']';
        var factorageField=cid==0?'factorage':'mfactorage['+cid+']';
        var activationfeeField=cid==0?'activationfee':'mactivationfee['+cid+']';
        var taxationField=cid==0?'taxation':'mtaxation['+cid+']';
        //var rebatetypeField=cid==0?'rebatetype':'mrebatetype['+cid+']';
        var accountrebatetypeField=cid==0?'accountrebatetype':'maccountrebatetype['+cid+']';
        var thisValue=$('input[name="'+transferamountField+'"]').val()*1;
        var factorageValue=$('input[name="'+factorageField+'"]').val()*1;
        var activationfeeValue=$('input[name="'+activationfeeField+'"]').val()*1;
        var taxationValue=$('input[name="'+taxationField+'"]').val()*1;
        //var rebatetypeValue=$('select[name="'+rebatetypeField+'"]').val();
        var accountrebatetypeValue=$('select[name="'+accountrebatetypeField+'"]').val();
        var rechargeamountValue=thisInstance.FloatSub(thisValue,thisInstance.FloatAdd(thisInstance.FloatAdd(factorageValue,activationfeeValue),taxationValue));


        var discountValue=$('input[name="'+discountField+'"]').val();
        if(discountValue<0){
            $('input[name="'+prestoreadrateField+'"]').val(0);
            $('input[name="'+rechargeamountField+'"]').val(0);
            return false;
        }
        if(rechargeamountValue<=0){
            $('input[name="'+prestoreadrateField+'"]').val(0);
            $('input[name="'+rechargeamountField+'"]').val(0);
            return false;
        }

        var topplatform=cid==0?'productid_display':'mproductid_display['+cid+']';
        discountValue=discountValue>0?discountValue:0;
        discountValue=thisInstance.FloatDiv(discountValue,100);
        var cashDiscountValue=thisInstance.FloatSub(1,discountValue);
        discountValue=thisInstance.FloatAdd(1,discountValue);
        rechargeamountValue=rechargeamountValue*1
        $('input[name="'+rechargeamountField+'"]').val(rechargeamountValue.toFixed(2));
        if(accountrebatetypeValue=='CashBack'){
            var prestoreadrate=thisInstance.FloatDiv(rechargeamountValue,cashDiscountValue);
        }else{
            var prestoreadrate=thisInstance.FloatMul(rechargeamountValue,discountValue);
        }
        var topplatform=$('input[name="'+topplatform+'"]').val();
        if(topplatform=='谷歌' || topplatform=='Yandex'){
            var tax=thisInstance.taxCalc(cid);
            tax=thisInstance.FloatAdd(1,thisInstance.FloatDiv(tax,100));//税率
            prestoreadrate=thisInstance.FloatDiv(prestoreadrate,tax);
        }
        $('input[name="'+prestoreadrateField+'"]').val(prestoreadrate.toFixed(2));
        thisInstance.servicecostCalc(thisValue,cid);
        thisInstance.transferamountCalc(cid);
        thisInstance.aggregateSummary();
    },
    /**
     * 服务成本
     */
    servicecostCalc:function(thisValue,cid){
        var thisInstance=this;
        var mastersupprebatefield=cid==0?'supprebate':'msupprebate['+cid+']';
        var discountField=cid==0?'discount':'mdiscount['+cid+']';
        var rechargeamountField=cid==0?'rechargeamount':'mrechargeamount['+cid+']';
        var servicecostField=cid==0?'servicecost':'mservicecost['+cid+']';
        var rebatetypeField=cid==0?'rebatetype':'mrebatetype['+cid+']';
        var accountrebatetypeField=cid==0?'accountrebatetype':'maccountrebatetype['+cid+']';
        var accountrebatetypeValue=$('select[name="'+accountrebatetypeField+'"]').val();
        var mastersupprebate=$('input[name="'+mastersupprebatefield+'"]').val();
        var rebatetypeValue=$('select[name="'+rebatetypeField+'"]').val();
        if(mastersupprebate<0){
            return false;
        }
        var discount=$('input[name="'+discountField+'"]').val();
        var rechargeamountValue=$('input[name="'+rechargeamountField+'"]').val();
        discount=discount>0?discount:0;
        if(rebatetypeValue=='CashBack' && accountrebatetypeValue=='CashBack'){
            //充值金额/(1-客户返点)*(1-供应商返点)
            discount=100-discount*1;
            mastersupprebate=100-mastersupprebate*1;
            var discountratio=thisInstance.FloatDiv(discount,mastersupprebate);
            var servicecost=thisInstance.FloatDiv(rechargeamountValue,discountratio)*1;
        }else if(rebatetypeValue=='GoodsBack' && accountrebatetypeValue=='GoodsBack'){
            //充值金额*(1+客户返点)/(1+供应商返点)
            discount=discount*1+100;
            mastersupprebate=mastersupprebate*1+100;
            var discountratio=thisInstance.FloatDiv(discount,mastersupprebate);
            var servicecost=thisInstance.FloatMul(rechargeamountValue,discountratio)*1;
        }else if(rebatetypeValue=='CashBack' && accountrebatetypeValue=='GoodsBack'){
            //值金额*(1+客户返点)*(1-供应商返点)
            discount=discount*1+100;
            mastersupprebate=100-mastersupprebate*1;
            var discountratio=thisInstance.FloatMul(discount,mastersupprebate);
            discountratio=thisInstance.FloatDiv(discountratio,10000);
            var servicecost=thisInstance.FloatMul(rechargeamountValue,discountratio)*1;
        }else if(rebatetypeValue=='GoodsBack' && accountrebatetypeValue=='CashBack'){
            //充值金额/(1-客户返点)/(1+供应商返点)
            discount=100-discount*1;
            discount=thisInstance.FloatDiv(discount,100);
            mastersupprebate=mastersupprebate*1+100;
            mastersupprebate=thisInstance.FloatDiv(mastersupprebate,100);
            var discountratio=thisInstance.FloatDiv(rechargeamountValue,discount);
            servicecost=thisInstance.FloatDiv(discountratio,mastersupprebate)*1.0;
            //var servicecost=thisInstance.FloatMul(rechargeamountValue,discountratio)*1;
        }
        var rechargesource=$('input[name="rechargesource"]').val();
        if(rechargesource=='Accounts'){
            var receivedstatus=$('select[name="receivedstatus"]').val();
            if(receivedstatus=='virtualrefund'){
                servicecost=0.0;
            }
        }
        $('input[name="'+servicecostField+'"]').val(servicecost.toFixed(2));

        if(rechargesource=='Vendors'){
            thisInstance.totalreceivablesCalc();
        }

    },
    /**
     *
     * @param cid
     */
    totalreceivablesCalc:function(){
        var thisInstance=this;
        var servicecostValue=$('input[name="servicecost"]').val();
        servicecostValue*=1;
        var mservicecost=$('input[name^="mservicecost["]');
        $.each(mservicecost,function(key,value){
            var thisValue=$(value).val();
            servicecostValue=thisInstance.FloatAdd(servicecostValue,thisValue);
        })
        servicecostValue*=1.0;
        $('input[name="totalreceivables"]').val(servicecostValue.toFixed(2));

    },
    transferamountCalc:function(cid){
        var rechargeamountField=cid==0?'rechargeamount':'mrechargeamount['+cid+']';
        var factorageField=cid==0?'factorage':'mfactorage['+cid+']';
        var taxationField=cid==0?'taxation':'mtaxation['+cid+']';
        var activationfeeField=cid==0?'activationfee':'mactivationfee['+cid+']';
        var servicecostField=cid==0?'servicecost':'mservicecost['+cid+']';
        var totalcostField=cid==0?'totalcost':'mtotalcost['+cid+']';
        var transferamountField=cid==0?'transferamount':'mtransferamount['+cid+']';
        var totalgrossprofitField=cid==0?'totalgrossprofit':'mtotalgrossprofit['+cid+']';
        var thisInstance=this;
        var rechargeamount=$('input[name="'+rechargeamountField+'"]').val();//充值金额
        var factorage=$('input[name="'+factorageField+'"]').val();//代理商服务费
        var taxation=$('input[name="'+taxationField+'"]').val();//税费
        var activationfee=$('input[name="'+activationfeeField+'"]').val();//开户费
        var totalcost=this.FloatAdd(activationfee,this.FloatAdd(taxation,factorage))*1;
        var servicecost=$('input[name="'+servicecostField+'"]').val();
        var transferamount=this.FloatAdd(rechargeamount,totalcost)*1;
        $('input[name="'+totalcostField+'"]').val(totalcost.toFixed(2));
        //console.log(totalcost);
        //$('input[name="'+transferamountField+'"]').val(transferamount.toFixed(2));
        servicecost=this.FloatSub(transferamount,servicecost)*1;
        var rechargesource=$('input[name="rechargesource"]').val();
        if(rechargesource=='Accounts'){
            var receivedstatus=$('select[name="receivedstatus"]').val();
            if(receivedstatus=='virtualrefund'){
                servicecost=0.0;
            }
        }
        $('input[name="'+totalgrossprofitField+'"]').val(servicecost.toFixed(2));
        thisInstance.actualtotalrechargeCalc();
    },
    /**
     * 转款金额求合
     */
    actualtotalrechargeCalc:function(){
        var thisInstance=this;
        var rechargesource=$('input[name="rechargesource"]').val();
        if(rechargesource=='Accounts'){
            var actualtotalrecharge=thisInstance.calctotalreceivables("transferamount","mtransferamount");
            $('input[name="actualtotalrecharge"]').val(actualtotalrecharge.toFixed(2));
        }else if(rechargesource=='Vendors'){
            var actualtotalrecharge=thisInstance.calctotalreceivables("transferamount","mtransferamount");
            $('input[name="actualtotalrecharge"]').val(actualtotalrecharge.toFixed(2));
            var totalreceivables=thisInstance.calctotalreceivables("servicecost","mservicecost");
            $('input[name="totalreceivables"]').val(totalreceivables.toFixed(2));
        }else if(rechargesource=='TECHPROCUREMENT'){
            var totalreceivables=thisInstance.calctotalreceivables("amountpayable","mamountpayable");
            $('input[name="totalreceivables"]').val(totalreceivables.toFixed(2));
        }else if(rechargesource=='PreRecharge'){
            var totalreceivables=thisInstance.calctotalreceivables("rechargeamount","mrechargeamount");
            $('input[name="totalreceivables"]').val(totalreceivables.toFixed(2));
        }else if(rechargesource=='NonMediaExtraction'){
            var totalreceivables=thisInstance.calctotalreceivables("purchaseamount","mpurchaseamount");
            $('input[name="totalreceivables"]').val(totalreceivables.toFixed(2));
        }else if(rechargesource=='COINRETURN'){
            thisInstance.sumCashAccounttransfer();
        }

    },
    calctotalreceivables:function(signName,multiName){
        var thisInstance=this;
        var servicecost=$('input[name="'+signName+'"]').val();//充值金额
        var mservicecostOBJ=$('input[name^="'+multiName+'["]');
        var mservicecost=0;
        $.each(mservicecostOBJ,function(key,value){
            var mtvalue=$(value).val();
            mtvalue=mtvalue>0?mtvalue:0
            mservicecost=thisInstance.FloatAdd(mservicecost,mtvalue);
        });
        return thisInstance.FloatAdd(servicecost,mservicecost)*1;
    },
    /**
     * 应付款合计计算
     */
    totalreceivablesCalcOther:function(){
        var amountpayable=$('input[name="amountpayable"]').val();//充值金额
        var thisInstance=this;
        var mamountpayableOBJ=$('input[name^="mamountpayable["]');
        var mamountpayable=0;
        $.each(mamountpayableOBJ,function(key,value){
            var mtvalue=$(value).val();
            mtvalue=mtvalue>0?mtvalue:0
            mamountpayable=thisInstance.FloatAdd(mamountpayable,mtvalue);
        });
        var totalreceivablesvalue=thisInstance.FloatAdd(amountpayable,mamountpayable)*1;
        $('input[name="totalreceivables"]').val(totalreceivablesvalue.toFixed(2));
    },
    /**
     * 非媒体类充值
     */
    actualtotalrechargeCalcNonMedia:function(){
        var transferamount=$('input[name="purchaseamount"]').val();//充值金额
        var thisInstance=this;
        var mtransferamountOBJ=$('input[name^="mpurchaseamount["]');
        var mtransferamount=0;
        $.each(mtransferamountOBJ,function(key,value){
            var mtvalue=$(value).val();
            mtvalue=mtvalue>0?mtvalue:0
            mtransferamount=thisInstance.FloatAdd(mtransferamount,mtvalue);
        });
        var totalgrossprofit=$('input[name="totalgrossprofit"]').val();//充值金额
        var mtotalgrossprofitOBJ=$('input[name^="mtotalgrossprofit["]');
        var mtotalgrossprofit=0;
        $.each(mtotalgrossprofitOBJ,function(key,value){
            var mtvalue=$(value).val();
            //mtvalue=mtvalue>0?mtvalue:0//允许小于0
            mtotalgrossprofit=thisInstance.FloatAdd(mtotalgrossprofit,mtvalue);
        });
        // var actualtotalrecharge=thisInstance.FloatAdd(transferamount,mtransferamount)*1;
        // actualtotalrecharge=thisInstance.FloatAdd(actualtotalrecharge,totalgrossprofit)*1;
        // actualtotalrecharge=thisInstance.FloatAdd(actualtotalrecharge,mtotalgrossprofit)*1;
        // $('input[name="actualtotalrecharge"]').val(actualtotalrecharge.toFixed(2));
        $('input[name="actualtotalrecharge"]').trigger("change");
    },
    /**
     * 税点计算
     */
    taxCalc:function(cid){
        var taxField=cid==0?'tax':'mtax['+cid+']'
        var tax=$('select[name="'+taxField+'"]').val();
        return tax.replace(/%/g,'');
    },
    productserviceChange:function(){
        var thisInstance=this;
        $('#EditView').on("change",'select[name="productservice"],select[name^="mproductservice["]',function(){
            if($(this).attr('readonly')=='readonly'){
                return false;
            }
            var rechargesource=$('input[name="rechargesource"]').val();
            if(rechargesource=='Accounts' ||rechargesource=='Vendors'){
                return;
            }
            var changeid=$(this).find("option:selected").data("changeid");
            var changeSelectedId=thisInstance.idproductid[changeid];
            if(changeSelectedId==undefined) {
                return false;
            }
            var cid=$(this).attr('data-cid');
            cid=cid>0?cid:0;
            var topplatformField=cid==0?'productid_display':'mproductid_display['+cid+']';
            var productidField=cid==0?'productid':'mproductid['+cid+']';
            var productserviceField=cid==0?'productservice':'mproductservice['+cid+']';
            var mastersupprebateField=cid==0?'supprebate':'msupprebate['+cid+']';
            var mdiscountField=cid==0?'discount':'mdiscount['+cid+']';
            var suppliercontractsid_displayField=cid==0?'suppliercontractsid_display':'msuppliercontractsid[display'+cid+']_display';
            var suppliercontractsidField=cid==0?'suppliercontractsid':'msuppliercontractsid['+cid+']';
            var havesignedcontractField=cid==0?'havesignedcontract':'mhavesignedcontract['+cid+']';
            var signdateField=cid==0?'signdate':'msigndate['+cid+']';
            var midField=cid==0?'did':'mid['+cid+']';
            var rebatetypeField=cid==0?'rebatetype':'mrebatetype['+cid+']'
            var accountzhField=cid==0?'accountzh':'maccountzh['+cid+']';
            var customeroriginattrField=cid==0?'customeroriginattr':'mcustomeroriginattr['+cid+']';
            var rechargetypedetailField=cid==0?'rechargetypedetail':'mrechargetypedetail['+cid+']';
            if(changeSelectedId.modulestatus=='c_complete'){
                $('select[name="'+havesignedcontractField+'"]').val("alreadySigned");
            }else{
                $('select[name="'+havesignedcontractField+'"]').val("notSigned");
            }
            $('select[name="'+havesignedcontractField+'"]').trigger('liszt:updated');
            $('select[name="'+productserviceField+'"]').val(changeSelectedId.productid);
            $('select[name="'+havesignedcontractField+'"]').trigger('liszt:updated');
            $('input[name="'+signdateField+'"]').val(changeSelectedId.signdate);
            $('input[name="'+suppliercontractsid_displayField+'"]').val(changeSelectedId.contract_no);
            $('input[name="'+suppliercontractsidField+'"]').val(changeSelectedId.suppliercontractsid);
            $('input[name="'+topplatformField+'"]').val(changeSelectedId.productname);
            $('input[name="'+productidField+'"]').val(changeSelectedId.productid);
            $('input[name="'+accountzhField+'"]').val(changeSelectedId.accountzh);
            $('input[name="'+midField+'"]').val(changeSelectedId.idaccount);
            $('input[name="'+mastersupprebateField+'"]').val(changeSelectedId.supplierrebate);
            $('select[name="'+customeroriginattrField+'"]').val(changeSelectedId.customeroriginattr);
            $('select[name="'+customeroriginattrField+'"]').trigger('liszt:updated');
            $('select[name="'+rechargetypedetailField+'"]').val(changeSelectedId.rechargetypedetail);
            $('select[name="'+rechargetypedetailField+'"]').trigger('liszt:updated');
            $('select[name="'+rebatetypeField+'"]').val(changeSelectedId.rebatetype);
            $('select[name="'+rebatetypeField+'"]').trigger('liszt:updated');
            rechargesource=='PreRecharge' && $('input[name="'+mdiscountField+'"]').val(changeSelectedId.supplierrebate);
            if(rechargesource=='Vendors'){
                var transferamountField=cid==0?'transferamount':'mtransferamount['+cid+']';
                var taxationField=cid==0?'taxation':'mtaxation['+cid+']';
                var prestoreadrateField=cid==0?'prestoreadrate':'mprestoreadrate['+cid+']';
                if(changeSelectedId.productname=='谷歌'  || changeSelectedId.productname=='Yandex'){
                    $('input[name="'+transferamountField+'"]').attr('readonly','readonly');
                    $('input[name="'+taxationField+'"]').attr('readonly','readonly');
                    $('input[name="'+prestoreadrateField+'"]').removeAttr('readonly');
                }else{
                    $('input[name="'+transferamountField+'"]').removeAttr('readonly');
                    $('input[name="'+taxationField+'"]').removeAttr('readonly');
                    $('input[name="'+prestoreadrateField+'"]').attr('readonly','readonly');
                }
            }
            thisInstance.eraseValueRecharge(cid);
            thisInstance.actualtotalrechargeCalc();
        })
    },
    productserviceChangeDid:function(changeid,cid){
        var thisInstance=this;
        //$('#EditView').on("change",'select[name="productservice"],select[name^="mproductservice["]',function(){
        var rechargesource=$('input[name="rechargesource"]').val();
        //var changeid=$(this).find("option:selected").data("changeid");
        var changeSelectedId=thisInstance.idproductid[changeid];
        if(changeSelectedId==undefined) {
            return false;
        }
        //var cid=$(this).attr('data-cid');
        cid=cid>0?cid:0;
        var topplatformField=cid==0?'productid_display':'mproductid_display['+cid+']';
        var productidField=cid==0?'productid':'mproductid['+cid+']';
        var productserviceField=cid==0?'productservice':'mproductservice['+cid+']';
        var mastersupprebateField=cid==0?'supprebate':'msupprebate['+cid+']';
        var mdiscountField=cid==0?'discount':'mdiscount['+cid+']';
        var suppliercontractsid_displayField=cid==0?'suppliercontractsid_display':'msuppliercontractsid[display'+cid+']_display';
        var suppliercontractsidField=cid==0?'suppliercontractsid':'msuppliercontractsid['+cid+']';
        var havesignedcontractField=cid==0?'havesignedcontract':'mhavesignedcontract['+cid+']';
        var signdateField=cid==0?'signdate':'msigndate['+cid+']';
        var midField=cid==0?'did':'mid['+cid+']';
        var accountzhField=cid==0?'accountzh':'maccountzh['+cid+']';
        var customeroriginattrField=cid==0?'customeroriginattr':'mcustomeroriginattr['+cid+']';
        var rechargetypedetailField=cid==0?'rechargetypedetail':'mrechargetypedetail['+cid+']';
        var rebatetypeField=cid==0?'rebatetype':'mrebatetype['+cid+']';
        var accountrebatetypeField=cid==0?'accountrebatetype':'maccountrebatetype['+cid+']';
        if(changeSelectedId.modulestatus=='c_complete'){
            $('select[name="'+havesignedcontractField+'"]').val("alreadySigned");
        }else{
            $('select[name="'+havesignedcontractField+'"]').val("notSigned");
        }
        $('select[name="'+havesignedcontractField+'"]').trigger('liszt:updated');
        $('select[name="'+productserviceField+'"]').val(changeSelectedId.productid);
        $('select[name="'+havesignedcontractField+'"]').trigger('liszt:updated');
        $('input[name="'+signdateField+'"]').val(changeSelectedId.signdate);
        $('input[name="'+suppliercontractsid_displayField+'"]').val(changeSelectedId.contract_no);
        $('input[name="'+suppliercontractsidField+'"]').val(changeSelectedId.suppliercontractsid);
        $('input[name="'+topplatformField+'"]').val(changeSelectedId.productname);
        $('input[name="'+productidField+'"]').val(changeSelectedId.productid);
        $('input[name="'+accountzhField+'"]').val(changeSelectedId.accountzh);
        $('input[name="'+midField+'"]').val(changeSelectedId.idaccount);
        $('input[name="'+mastersupprebateField+'"]').val(changeSelectedId.supplierrebate);
        $('select[name="'+customeroriginattrField+'"]').val(changeSelectedId.customeroriginattr);
        $('select[name="'+customeroriginattrField+'"]').trigger('liszt:updated');
        $('select[name="'+rechargetypedetailField+'"]').val(changeSelectedId.rechargetypedetail);
        $('select[name="'+rechargetypedetailField+'"]').trigger('liszt:updated');
        $('select[name="'+rebatetypeField+'"]').val(changeSelectedId.rebatetype);
        $('select[name="'+rebatetypeField+'"]').trigger('liszt:updated');
        $('select[name="'+accountrebatetypeField+'"]').val(changeSelectedId.accountrebatetype);
        $('select[name="'+accountrebatetypeField+'"]').trigger('liszt:updated');
        $('input[name="'+mdiscountField+'"]').val(changeSelectedId.accountrebate);
        rechargesource=='PreRecharge' && $('input[name="'+mdiscountField+'"]').val(changeSelectedId.supplierrebate);
        $('input[name="'+mdiscountField+'"]').attr('readonly','readonly');
        if(rechargesource=='Vendors'){
            var transferamountField=cid==0?'transferamount':'mtransferamount['+cid+']';
            var taxationField=cid==0?'taxation':'mtaxation['+cid+']';
            var prestoreadrateField=cid==0?'prestoreadrate':'mprestoreadrate['+cid+']';
            if(changeSelectedId.productname=='谷歌' || changeSelectedId.productname=='Yandex'){
                $('input[name="'+transferamountField+'"]').attr('readonly','readonly');
                $('input[name="'+taxationField+'"]').attr('readonly','readonly');
                $('input[name="'+mdiscountField+'"]').attr('readonly','readonly');
                $('input[name="'+prestoreadrateField+'"]').removeAttr('readonly');
            }else{
                $('input[name="'+transferamountField+'"]').removeAttr('readonly');
                $('input[name="'+taxationField+'"]').removeAttr('readonly');
                //$('input[name="'+mdiscountField+'"]').removeAttr('readonly');
                $('input[name="'+prestoreadrateField+'"]').attr('readonly','readonly');
            }
        }
        thisInstance.eraseValueRecharge(cid);
        thisInstance.actualtotalrechargeCalc();
        //})
    },
    registerAutoCompleteFields:function(container){
        /**
         * 重写该方法阻止父方法执行
         */
    },
    getPopUpParams : function(container) {
        var params = {};
        var sourceModule = app.getModuleName();
        var popupReferenceModule = jQuery('input[name="popupReferenceModule"]',container).val();
        var sourceFieldElement = jQuery('input[class="sourceField"]',container);
        var sourceField = sourceFieldElement.attr('name');
        var sourceRecordElement = jQuery('input[name="record"]');
        var otherSourceField = sourceFieldElement.attr('othername');
        var sourceRecordId = '';
        if(sourceRecordElement.length > 0) {
            sourceRecordId = sourceRecordElement.val();
        }
        var otherField=otherSourceField;
        if(otherSourceField!=undefined){
            otherField=sourceField
            sourceField=otherSourceField;
        }
        var isMultiple = false;
        if(sourceFieldElement.data('multiple') == true){
            isMultiple = true;
        }
        var rechargesource=$('input[name="rechargesource"]').val();
        var needContract = $("select[name='changecontracttype']").val();
        // 对 合同变更申请专用 如果 选择变更类型 则 走if  其他的都走else
        if(needContract=='ServiceContracts' || needContract=='SupplierContracts'){

            // 如果是供应商和同且是目标合同 则 选择目标合同时再穿一个参数识别  获取目标合同时必须要选择
            if($("#whichContract").val()==1 && needContract=='SupplierContracts'){
                var oldAccountId = $("input[name='accountid']").val();
                var params = {
                    'module' : needContract,
                    'src_module' : sourceModule,
                    'src_field' : sourceField,
                    'src_record' : sourceRecordId,
                    'src_otherfield' : otherField,
                    'rechargesource':rechargesource,
                    'needContract':needContract,
                    'oldAccountId':oldAccountId
                }
            }else{
                var params = {
                    'module' : needContract,
                    'src_module' : sourceModule,
                    'src_field' : sourceField,
                    'src_record' : sourceRecordId,
                    'src_otherfield' : otherField,
                    'rechargesource':rechargesource,
                    'needContract':needContract
                }
            }
        }else{
            var params = {
                'module' : popupReferenceModule,
                'src_module' : sourceModule,
                'src_field' : sourceField,
                'src_record' : sourceRecordId,
                'src_otherfield' : otherField,
                'rechargesource':rechargesource
            }
        }
        console.log(params);
        if(isMultiple) {
            params.multi_select = true ;
        }
        return params;
    },
    //技术采购
    technicalProcurement:function(){
        var salesorderid=$('input[name="salesorderid"]').val();
        if(salesorderid>0) {
            var record = jQuery('input[name="record"]').val();
            record = record > 0 ? record : 0;
            var thisInstance = this;
            var params = {
                'module': 'RefillApplication',
                'action': 'BasicAjax',
                'record': salesorderid,
                'mode': 'getSalesorderRelalist'
            };
            AppConnector.request(params).then(
                function (data) {
                    if(data.success) {
                        if (record==0){
                            $('input[name="accountid"]').val(data.result.id);
                            $('input[name="accountid_display"]').val(data.result.accountname);
                            $('input[name="servicecontractsid"]').val(data.result.servicecontractsid);
                            $('input[name="servicecontractsid_display"]').val(data.result.contract_no);
                            $('input[name="humancost"]').val(data.result.costing.costings);
                            $('input[name="purchasecost"]').val(data.result.costing.purchasemount);
                            $('input[name="contractamount"]').val(data.result.total);

                        }else{
                            $('input[name="occupationamount"]').val(data.result.occupationamount);
                        }
                        thisInstance.getSaleSorderPayments();
                    }
                },
                function (error) {
                }
            );
        }
    },
    getSaleSorderPayments:function(){
        var params={};
        params.data= {'module':'RefillApplication','action':'BasicAjax','mode':'getSaleSorderPayments','record':$('input[name="salesorderid"]').val()};
        params.async=false;
        $(".newinvoicerayment_tab").remove();
        AppConnector.request(params).then(function(data){
            if(data.success){
                var str='';
                $.each(data.result,function(key,value){
                    str+='<table class="table table-bordered blockContainer newinvoicerayment_tab detailview-table newinvoicerayment_tab'+value.receivedpaymentsid+'" data-num="'+value.salesorderproductsrelid+'">'+
                        '<thead><tr><th class="blockHeader" colspan="7">&nbsp;&nbsp;外采产品['+(key+1)+'] <b class="pull-right"><button class="btn btn-small delbuttonnewinvoicerayment" type="button" data-id="'+value.salesorderproductsrelid+'"><i class="icon-trash" title="删除关联回款信息"></i></button></b></th></tr></thead>'+
                        '<tbody><tr><td><label class="muted">工单产品</label></td>'+
                        '<td><label class="muted"><span class="redColor">*</span> 外采成本</label></td>'+
                        '<td><label class="muted"><span class="redColor">*</span> 已使用外采成本</label></td>'+
                        '<td><label class="muted"><span class="redColor">*</span> 可使用外采金额</label></td>'+
                        '<td><label class="muted"><span class="redColor">*</span> 使用外采金额</label></td>'+
                        '<td><label class="muted"><span class="redColor"></span> 备注</label></td></tr>'+
                        '<tr><td><input type="hidden" name="insertii['+value.salesorderproductsrelid+']" value="'+value.salesorderproductsrelid+'"><input type="hidden" class="receivedpaymentsid_display" name="receivedpaymentsid_display['+value.salesorderproductsrelid+']" data-id="'+value.salesorderproductsrelid+'" value=""> <input type="hidden" class="invoicecompany" name="owncompany['+value.salesorderproductsrelid+']" data-id="'+value.salesorderproductsrelid+'" value="'+value.owncompany+'"><div class="row-fluid"><span class="span10"><select class="chzn-select t_tab_newinvoicerayment_id" name="paytitle['+value.salesorderproductsrelid+']" data-id="'+value.salesorderproductsrelid+'" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"><option value="'+value.productname+'">'+value.productname+'</option></select></span></div></td>'+
                        '<td><div class="row-fluid"><span class="span10"><input type="text" style="width:100px;" class="input-large total" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="total['+value.salesorderproductsrelid+']" data-id="'+value.salesorderproductsrelid+'" readonly value="'+value.purchasemount+'"></span></div></td>'+
                        '<td><div class="row-fluid"><span class="span10"><input type="text" style="width:100px;" class="input-large arrivaldate" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" readonly name="occupationcost['+value.salesorderproductsrelid+']" data-id="'+value.salesorderproductsrelid+'" value="'+value.costofuse+'"></span></div></td>'+
                        '<td><div class="row-fluid"><span class="span10"><input type="text" style="width:100px;" class="input-large allowrefillapptotal" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" readonly name="allowrefillapptotal['+value.salesorderproductsrelid+']" data-id="'+value.salesorderproductsrelid+'" value="'+value.rechargeableamount+'"></span></div></td>'+
                        '<td><div class="row-fluid"><span class="span10"><input type="text" style="width:100px;" class="input-large refillapptotal receivedpayments_refillapptotal" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="refillapptotal['+value.salesorderproductsrelid+']" data-id="'+value.salesorderproductsrelid+'" value=""></span></div></td>'+
                        '<td><div class="row-fluid"><span class="span10"><textarea class="span11" data-id="'+value.salesorderproductsrelid+'" name="rremarks['+value.salesorderproductsrelid+']"></textarea></span></div></td></tr></tbody></table>';
                });
                $('.LBL_INFO').after(str);
            }
        });
    },
    transferamountevent:function(){

    },
    setReferenceFieldValue : function(container, params) {
        var sourceField = container.find('input[class="sourceField"]').attr('name');
        var fieldElement = container.find('input[name="'+sourceField+'"]');
        var otherSourceField = fieldElement.attr('othername');
        if(otherSourceField!=undefined){
            sourceField=sourceField.replace(/\[/,'[display');
        }
        var sourceFieldDisplay = sourceField+"_display";
        var fieldDisplayElement = container.find('input[name="'+sourceFieldDisplay+'"]');
        var popupReferenceModule = container.find('input[name="popupReferenceModule"]').val();

        var selectedName = params.name;
        var id = params.id;

        fieldElement.val(id)
        fieldDisplayElement.val(selectedName);
        fieldElement.trigger(Vtiger_Edit_Js.referenceSelectionEvent, {'source_module' : popupReferenceModule, 'record' : id, 'selectedName' : selectedName});
        fieldDisplayElement.validationEngine('closePrompt',fieldDisplayElement);
    },

    setReferenceDidFieldValue : function(container, params) {
        var sourceField = container.find('input[class="sourceField"]').attr('name');
        var fieldElement = container.find('input[name="'+sourceField+'"]');
        var otherSourceField = fieldElement.attr('othername');
        if(otherSourceField!=undefined){
            sourceField=sourceField.replace(/\[/,'[display');
        }
        if(sourceField.indexOf('mid') != -1){
            var sourceFieldDisplay ="mid_display["+container.find('input[class="sourceField"]').data('cid')+"]";
        }else{
            var sourceFieldDisplay = sourceField+"_display";
        }
        var fieldDisplayElement = container.find('input[name="'+sourceFieldDisplay+'"]');
        var popupReferenceModule = container.find('input[name="popupReferenceModule"]').val();

        var selectedName = params.name;
        var id = params.id;

        fieldElement.val(selectedName)
        fieldDisplayElement.val(selectedName);

        fieldElement.trigger(Vtiger_Edit_Js.referenceSelectionEvent, {'source_module' : popupReferenceModule, 'record' : id, 'selectedName' : selectedName});
        fieldDisplayElement.validationEngine('closePrompt',fieldDisplayElement);
    },

    aggregateSummary:function(){
        var thisInstance=this;
        //成本合计
        var servicecost=$('input[name="servicecost"]').val()*1;
        var servicecosts=$('input[name^="mservicecost["]');
        $.each(servicecosts,function(key,value){
            servicecost=thisInstance.FloatAdd(servicecost,$(value).val());
        });
        $('input[name="totalcosts"]').val(servicecost);
        //合计账户充值金额t
        var rechargeamount=$('input[name="rechargeamount"]').val()*1;
        var rechargeamounts=$('input[name^="mrechargeamount["]');
        rechargeamounts.each(function(key,value){
            rechargeamount=thisInstance.FloatAdd(rechargeamount,$(value).val())*1;
        });
        $('input[name="totalrechargeamount"]').val(rechargeamount.toFixed(2));
        //合计账户币
        var prestoreadrate=$('input[name="prestoreadrate"]').val()*1;
        var prestoreadrates=$('input[name^="mprestoreadrate["]');
        $.each(prestoreadrates,function(key,value){
            prestoreadrate=thisInstance.FloatAdd(prestoreadrate,$(value).val())*1;
        });
        $('input[name="totalaccountcurrency"]').val(prestoreadrate.toFixed(2));
        //合计毛利
        var totalgrossprofit=$('input[name="totalgrossprofit"]').val()*1;
        var totalgrossprofits=$('input[name^="mtotalgrossprofit["]');
        $.each(totalgrossprofits,function(key,value){
            //console.log($(value).val());
            totalgrossprofit=thisInstance.FloatAdd(totalgrossprofit,$(value).val())*1;
        });
        $('input[name="totalmaori"]').val(totalgrossprofit);
        //合计毛利
        var totalgrossprofit=$('input[name="totalgrossprofit"]').val()*1;
        var totalgrossprofits=$('input[name^="mtotalgrossprofit["]');
        $.each(totalgrossprofits,function(key,value){
            totalgrossprofit=thisInstance.FloatAdd(totalgrossprofit,$(value).val());
        });
        $('input[name="totalmaori"]').val(totalgrossprofit.toFixed(2));
        //合同收款
        var totalrecharge=$('input[name="totalrecharge"]').val()*1;
        $('input[name="totalgatheri"]').val(totalrecharge.toFixed(2));
        //合计垫款金额
        var actualtotalrecharge=$('input[name="actualtotalrecharge"]').val()*1;
        var totaladvances=actualtotalrecharge*1-totalrecharge*1;
        $('input[name="totaladvances"]').val(totaladvances.toFixed(2));
    },
    selectedChange:function(){
        var thisInstance=this;
        var rechargesource=$('input[name="rechargesource"]').val();
        // 如果是合同变更申请编辑则加上newcustomertype oldrechargesource 不可编辑
        if(rechargesource == 'contractChanges' && $('input[name="record"]').val()>0){
            var arrayList=['productservice',/*'rechargetypedetail',*/'customertype','newcustomertype','oldrechargesource','changecontracttype','newiscontracted','iscontracted','customeroriginattr','isprovideservice','havesignedcontract','miscontracted','mcustomeroriginattr','misprovideservice','mhavesignedcontract','rebatetype','mrebatetype',/*'mrechargetypedetail',*/'maccountrebatetype','accountrebatetype'];
        }else{
            var arrayList=['productservice',/*'rechargetypedetail',*/'customertype','newcustomertype','newiscontracted','iscontracted','customeroriginattr','isprovideservice','havesignedcontract','miscontracted','mcustomeroriginattr','misprovideservice','mhavesignedcontract','rebatetype','mrebatetype',/*'mrechargetypedetail',*/'maccountrebatetype','accountrebatetype'];
        }
        $.each($('select'),function(key,value){
            var valueName=$(value).attr('name');
            valueName=valueName.replace(/\[\d*\]/,'');
            if($.inArray(valueName,arrayList)!=-1){
                var thisid=$(value).attr('id');
                if(rechargesource=='INCREASE' && (valueName=='maccountrebatetype' || valueName=='accountrebatetype')){

                }else if(rechargesource=='PreRecharge' || rechargesource=='NonMediaExtraction' || rechargesource=='TECHPROCUREMENT'){
                    if(valueName!='productservice'){
                        $('#'+thisid+'_chzn').find('.chzn-drop').remove();
                    }
                }else{
                    $('#'+thisid+'_chzn').find('.chzn-drop').remove();
                }
            }
        });
        $('#EditView').on('click','.chzn-container',function(){
            var thisId=$(this).attr('id');
            var thisIdSelect=thisId.split('_');
            thisInstance.seletedIndexValue=$('#'+thisIdSelect[0])[0].selectedIndex;
            thisInstance.seletedValue=$('#'+thisIdSelect[0]).val();
        });
        $('#EditView').on('change','select[name="productservice"],select[name^="mproductservice["],select[name="iscontracted"],select[name="newiscontracted"],select[name="isprovideservice"],select[name^="misprovideservice["],select[name="customertype"],select[name="newcustomertype"],select[name="customeroriginattr"],select[name^="mcustomeroriginattr["],select[name="havesignedcontract"],select[name^="mhavesignedcontract["],select[name^="rebatetype"],select[name^="mrebatetype["],select[name^="maccountrebatetype["]',function(){
            if(rechargesource=='PreRecharge' || rechargesource=='NonMediaExtraction' || rechargesource=='TECHPROCUREMENT' || rechargesource=='INCREASE'){
            }else{
                this.selectedIndex=thisInstance.seletedIndexValue;
                $(this).trigger('liszt:updated');
                var thisid=$(this).attr('id');
                $('#'+thisid+'_chzn').find('.chzn-drop').remove();
            }

        });
        $('#EditView').on('change','select[name="rechargetypedetail"],select[name^="mrechargetypedetail"]',function(){
            if(thisInstance.seletedValue!='OpenAnAccount'){
                this.selectedIndex=thisInstance.seletedIndexValue;
            }
            $(this).trigger('liszt:updated');
        });

    },
    instanceLoading:function(){
        var thinInstance=this;
        $('#RefillApplication_editView_fieldName_servicesigndate').datepicker('remove');
        $('#RefillApplication_editView_fieldName_signdate').datepicker('remove');
        $('input[name^="msigndate["]').datepicker('remove');
        //$('select[name="iscontracted"]').chosen('destroy')//.chosen({'display_disabled_options':true});
        //$('select[name="iscontracted"]').trigger('liszt:updated');
        $('#EditView').on("click",".checkedall",function(event){
            $('input[name^="insertid\["]').iCheck('check');
            thinInstance.vendorIdcheckedValue();
        });
        $('#EditView').on("click",".checkedinverse",function(event){
            $('input[name^="insertid\["]').iCheck('toggle');
            thinInstance.vendorIdcheckedValue();
        });
        $('#EditView').on("ifClicked",".entryCheckBox",function(event){
            $(this).iCheck('toggle');
            thinInstance.vendorIdcheckedValue();
        });

    },
    getVendorsList:function(){
        var thisInstance=this;
        var recordid=$('input[name="record"]').val();
        var vendorid=$('input[name="vendorid"]').val();
        var expecteddatepayment=$('input[name="expecteddatepayment"]').val();
        var expectedpaymentdeadline=$('input[name="expectedpaymentdeadline"]').val();
        if(expecteddatepayment==''||expectedpaymentdeadline==''){
            var params = {'text':'提单开始时间或提单结束时间必填!', 'title': ''};
            Vtiger_Helper_Js.showPnotify(params);
            thisInstance.clearVendorid();
            return false;
        }
        if ((new Date(expecteddatepayment.replace(/-/g,'\/')))>(new Date(expectedpaymentdeadline.replace(/-/g,'\/')))) {
            var  params = {text : app.vtranslate(),title : app.vtranslate('提单开始日期不能大于提单结束日期')};
            Vtiger_Helper_Js.showPnotify(params);
            thisInstance.clearVendorid();
            return false;
        }
        params={
            "module": "RefillApplication",
            "action": "BasicAjax",
            "mode": "getVendorList",
            "record":recordid,
            "vendorid":vendorid,
            "startdate":expecteddatepayment,
            "enddate":expectedpaymentdeadline
        };
        var progressIndicatorElement = jQuery.progressIndicator({
            'message' : '正在加载...',
            'position' : 'html',
            'blockInfo' : {'enabled' : true}
        });
        $('#vendoridslist').html('');
        AppConnector.request(params).then(
            function(data){
                progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                if(data.success){
                    var columnfields=data.result.columnfields;//取开户信息
                    $('input[name="bankaccount"]').val(columnfields.bankaccount);
                    $('input[name="bankname"]').val(columnfields.bankname);
                    $('input[name="banknumber"]').val(columnfields.banknumber);
                    $('input[name="bankcode"]').val(columnfields.bankcode);
                    var bankinfo=data.result.bankinfo;
                    var bankinfostr='';
                    $.each(bankinfo,function(key,value){
                        bankinfostr+='<option value="'+value.bankaccount+'" data-bankaccount="'+value.bankaccount+'" data-bankname="'+value.bankname+'" data-banknumber="'+value.banknumber+'" data-bankcode="'+value.bankcode+'">'+value.banknumber+'</option>'
                    });
                    $('select[name="banklist"]')[0].options.length=0;
                    $('select[name="banklist"]').append(bankinfostr);
                    $('select[name="banklist"]').trigger("liszt:updated");
                    if(data.result.data){
                        var str='';
                        if(data.result.data.length) {
                            str='<table class="table table-bordered detailview-table">' +
                                '<thead>' +
                                '<tr><th style="width:110px;"><button type="button" class="btn btn-success checkedall">全选</button><button type="button" class="btn btn-inverse checkedinverse">反选</button></th>' +
                                '<th><label class="muted">申请单编号</label></th>' +
                                '<th><label class="muted"><span class="redColor">*</span> 服务合同</label></th>' +
                                '<th><label class="muted"><span class="redColor">*</span> 客户</label></th>' +
                                '<th><label class="muted"><span class="redColor">*</span> 申请人</label></th>' +
                                '<th><label class="muted"><span class="redColor">*</span> 充值来源</label></th>' +
                                '<th><label class="muted"><span class="redColor">*</span> 应收款总额</label></th>' +
                                '<th><label class="muted"><span class="redColor">*</span> 应付款金额</label></th>' +
                                '<th><label class="muted"><span class="redColor">*</span> 申请时间</label></th>' +
                                '<th><label class="muted"><span class="redColor"></span> 备注</label></th></tr></thead><tbody>';
                            $.each(data.result.data, function (key, value) {
                                var checkboxed=value.selected==1?' checked':'';
                                str += '<tr><td><div class="row-fluid"><input type="checkbox" name="insertid['+value.refillapplicationid+']" data-id="'+value.refillapplicationid+'" class="entryCheckBox"'+checkboxed+' value="'+value.refillapplicationid+'" data-total="'+value.totalreceivables+'"><input type="hidden" name="totalreceivablesd['+value.refillapplicationid+']" value="'+value.totalreceivables+'"> </div></td>' +
                                    '<td><div class="row-fluid"><span class="span10"><a href="/index.php?module=RefillApplication&view=Detail&record='+value.refillapplicationid+'" target="_blank">'+value.refillapplicationno+'</a></span></div></td>' +
                                    '<td><div class="row-fluid"><span class="span10">'+value.contract_no+'</span></div></td>' +
                                    '<td><div class="row-fluid"><span class="span10">'+value.accountname+'</span></div></td>'+
                                    '<td><div class="row-fluid"><span class="span10">'+value.username+'</span></div></td>'+
                                    '<td><div class="row-fluid"><span class="span10">'+value.rechargesource+'</span></div></td>'+
                                    '<td><div class="row-fluid"><span class="span10">'+value.actualtotalrecharge+'</span></div></td>' +
                                    '<td><div class="row-fluid"><span class="span10">'+value.totalreceivables+'</span></div></td>' +
                                    '<td><div class="row-fluid"><span class="span10">'+value.createdtime+'</span></div></td>'+
                                    '<td><div class="row-fluid"><span class="span10">'+value.remarks+'</span></div></td></tr>';
                            });
                            str+='</tbody></table>';
                            $('#vendoridslist').html(str);
                            $('.entryCheckBox').iCheck({
                                checkboxClass: 'icheckbox_minimal-blue'
                            });
                        }
                    }else{
                        Vtiger_Helper_Js.showMessage({type:'error',text:'没有相关记录'});
                    }

                }
            },
            function(){
            }
        );

    },
    vendorIdcheckedValue:function(){
        var thisInstance=this;
        var entryCheckBox=$('.entryCheckBox:checkbox:checked');
        var totalreceivables=0.0;
        $.each(entryCheckBox,function(key,value){
            totalreceivables = thisInstance.FloatAdd(totalreceivables, $(value).data('total'));
        });
        totalreceivables=totalreceivables*1.0;
        $('input[name="totalreceivables"]').val(totalreceivables.toFixed(2));
    },
    clearVendorid:function(){
        $('input[name="vendorid"]').val('');
        $('input[name="vendorid_display"]').val('');
    },
    checkAuditInformation:function(accountid,advancesmoney){
        var params={};
        params.data={
            "module": "RefillApplication",
            "action": "BasicAjax",
            "mode": "setAuditInformation",
            "accountid":accountid,
            "advancesmoney":advancesmoney
        };
        params.async=false;
        var progressIndicatorElement = jQuery.progressIndicator({
            'message' : '正在加载...',
            'position' : 'html',
            'blockInfo' : {'enabled' : true}
        });
        var returndata={'flag':false};
        AppConnector.request(params).then(
            function(data){
                progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                if(data.success){
                    returndata=data.result;
                }
            },
            function(){
            }
        );
        //returndata={'flag':true,'msg':'是否要确认提交!'};
        return returndata;
    },
    banklistchange:function(){
        $('#EditView').on("change",'select[name="banklist"]',function(){
            var bankdata=$(this).find("option:selected");
            $('input[name="bankaccount"]').val(bankdata.data("bankaccount"));
            $('input[name="bankname"]').val(bankdata.data("bankname"));
            //$('input[name="banknumber"]').val(bankdata.data("banknumber"));
            $('input[name="banknumber"]').val(bankdata.text());
            $('input[name="bankcode"]').val(bankdata.data("bankcode"));
        });
    },
    turncashin:function(){
        var thisInstance=this;
        $('#EditView').on("click",'.turncashin',function(){
            var datatype=$(this).data('type');
            var accountId=$('input[name="accountid"]').val();
            var rechargesource=$('input[name="rechargesource"]').val();
                if(!accountId){
                    Vtiger_Helper_Js.showMessage({type:'error',text:'请先选择客户'});
                    return false;
                }
                var numd = $('.Duplicates').length + 1;
                if (numd > 16) {
                    return;
                }
                /*超过20个不允许添加*/
                var currnetNum=0
                $.each($('.Duplicates'),function(key,value){
                    var valueNum=$(value).data('num');
                    if(valueNum>=currnetNum){
                        currnetNum=valueNum;
                    }
                });
                if (currnetNum>0) {
                    numd = currnetNum + 1;
                }
                var extend = COINRETURNsheet.replace(/\[\]|replaceyes/g, '[' + numd + ']');
                extend = extend.replace(/yesreplace/g, numd);
                extend = extend.replace(/#inorout#/g, datatype);
                var insertit=datatype=='in'?'insertafter':'insertbefore';
                var inoroutname=datatype=='in'?'<span class="label label-a_normal">转入</span>':'<span class="label label-c_stamp">转出</span>';
                extend = extend.replace(/inoroutname/g, inoroutname);
                $('#'+insertit).before(extend);
                //去掉原本的下拉框，换上弹框
                var replaceIdStr='<td class="fieldValue medium"><input name="popupReferenceModule" type="hidden" value="RefillApplication" autocomplete="off"><input name="mid['+numd+']" type="hidden" data-cid="'+numd+'" value="" data-multiple="0" class="sourceField" data-displayvalue="" data-fieldinfo="{&quot;mandatory&quot;:true,&quot;presence&quot;:true,&quot;quickcreate&quot;:true,&quot;masseditable&quot;:true,&quot;defaultvalue&quot;:false,&quot;type&quot;:&quot;string&quot;,&quot;name&quot;:&quot;did&quot;,&quot;label&quot;:&quot;ID&quot;}" autocomplete="off"><div class="row-fluid input-prepend input-append"><span class="add-on clearReferenceSelection cursorPointer"><i id="RefillApplication_editView_fieldName_did_clear" class="icon-remove-sign" title="清除"></i></span><input id="mid_display['+numd+']" readonly="readonly" name="mid_display['+numd+']" type="text" class=" span7 marginLeftZero autoComplete" value="" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{&quot;mandatory&quot;:true,&quot;presence&quot;:true,&quot;quickcreate&quot;:true,&quot;masseditable&quot;:true,&quot;defaultvalue&quot;:false,&quot;type&quot;:&quot;string&quot;,&quot;name&quot;:&quot;did&quot;,&quot;label&quot;:&quot;ID&quot;}" placeholder="查找.." autocomplete="off"><span data-id="RefillApplication_editView_fieldName_did_select" class="add-on relatedPopupDid cursorPointer"><i id="RefillApplication_editView_fieldName_did_select" data-id="RefillApplication_editView_fieldName_did_select" class="icon-search relatedPopupDid" title="选择"></i></span></div></td>'
                if(datatype=='in'){
                    $('.Duplicates').last().find('tbody .fieldValue').eq(0).remove();
                    $('.Duplicates').last().find('tbody .fieldLabel').eq(0).after(replaceIdStr);
                }else{
                    $('#insertbefore').prev().find('tbody .fieldValue').eq(0).remove();
                    $('#insertbefore').prev().find('tbody .fieldLabel').eq(0).after(replaceIdStr);
                }
                $('.chzn-select').chosen();
        });
        $('#EditView').on("change keyup",'input[name="accounttransfer"],input[name^="maccounttransfer["]',function(){
            if($(this).attr('readonly')=='readonly'){
                return false;
            }
            var cid=$(this).data('cid');
            cid=cid>0?cid:0;
            var thisValue=$(this).val();
            var maccountrebatetype=cid>0?'maccountrebatetype['+cid+']':"accountrebatetype";
            var mdiscount=cid>0?'mdiscount['+cid+']':"discount";
            var mcashtransfer=cid>0?'mcashtransfer['+cid+']':"cashtransfer";
            var mcashtransfervalue=0;
            var mdiscountValue=$('input[name="'+mdiscount+'"]').val();
            var maccountrebatetypeValue=$('select[name="'+maccountrebatetype+'"]').val();
            mdiscountValue=thisInstance.FloatDiv(mdiscountValue,100);

            if(maccountrebatetypeValue=='CashBack'){
                mdiscountValue=thisInstance.FloatSub(1,mdiscountValue);
                mcashtransfervalue=thisInstance.FloatMul(mdiscountValue,thisValue);
            }else{
                mdiscountValue=thisInstance.FloatAdd(1,mdiscountValue);
                mcashtransfervalue=thisInstance.FloatDiv(thisValue,mdiscountValue);
            }
            mcashtransfervalue*=1.0;
            $('input[name="'+mcashtransfer+'"]').val(mcashtransfervalue.toFixed(2));
            thisInstance.sumCashAccounttransfer();

        });
    },
    sumCashAccounttransfer:function(){
        var thisInstance=this;
        var totalcashtransfer=0;//转出现金
        var totalturnoverofaccount=0;//转出转户币
        var totalcashin=0;//转入现金
        var totaltransfertoaccount=0;//转入账户币
        totalcashtransfer=$('input[name="cashtransfer"]').val();
        totalturnoverofaccount=$('input[name="accounttransfer"]').val();
        var discountarrayinout=[];//转入返点临时数组
        var discountarray=[];//返点临时数组
        var numdiscountarray=[];//返点个数临时数组,做加权平均用暂时不用
        var maccounttransferinout=[];//转入账户币
        $.each($('input[name^="maccounttransfer["]'),function(key,value){
            var typecash=$(value).data('typecash');
            var cid=$(value).data('cid');
            var thisMcashtransfer=$('input[name="mcashtransfer['+cid+']"]').val();
            var thismdiscount=$('input[name="mdiscount['+cid+']"]').val();
            var maccountrebatetype=$('select[name="maccountrebatetype['+cid+']"]').val();
            var thisValue=$(value).val();
            if(typecash=='in'){
                totaltransfertoaccount=thisInstance.FloatAdd(thisValue,totaltransfertoaccount);
                totalcashin=thisInstance.FloatAdd(thisMcashtransfer,totalcashin);
                if($.inArray('in'+maccountrebatetype+(thismdiscount*100),discountarray)==-1){
                    discountarrayinout['in'+maccountrebatetype+(thismdiscount*100)]=thismdiscount;
                    maccounttransferinout['in'+maccountrebatetype+(thismdiscount*100)]=thisValue;
                    discountarray.push('in'+maccountrebatetype+(thismdiscount*100));
                    numdiscountarray['in'+maccountrebatetype+(thismdiscount*100)]=1;
                }else{
                    maccounttransferinout['in'+maccountrebatetype+(thismdiscount*100)]=thisInstance.FloatAdd(thisValue,maccounttransferinout['in'+maccountrebatetype+(thismdiscount*100)]);
                    numdiscountarray['in'+maccountrebatetype+(thismdiscount*100)]+=1;
                }
            }else{
                totalcashtransfer=thisInstance.FloatAdd(thisMcashtransfer,totalcashtransfer);
                totalturnoverofaccount=thisInstance.FloatAdd(thisValue,totalturnoverofaccount);
                if($.inArray('out'+maccountrebatetype+(thismdiscount*100),discountarray)==-1){
                    discountarrayinout['out'+maccountrebatetype+(thismdiscount*100)]=thismdiscount;
                    maccounttransferinout['out'+maccountrebatetype+(thismdiscount*100)]=thisValue;
                    discountarray.push('out'+maccountrebatetype+(thismdiscount*100));
                    numdiscountarray['out'+maccountrebatetype+(thismdiscount*100)]=1;
                }else{
                    maccounttransferinout['out'+maccountrebatetype+(thismdiscount*100)]=thisInstance.FloatAdd(thisValue,maccounttransferinout['out'+maccountrebatetype+(thismdiscount*100)]);
                    numdiscountarray['out'+maccountrebatetype+(thismdiscount*100)]+=1;
                }
            }
        });
        var tmaccounttransferin=0;
        var tmaccounttransferout=0;
        var tempaccounttransfer=$('input[name="accounttransfer"]').val();
        var tempdiscount=$('input[name="discount"]').val();
        var accountrebatetype=$('select[name="accountrebatetype"]').val();;
        if($.inArray('out'+accountrebatetype+(tempdiscount*100),discountarray)==-1){
            discountarrayinout['out'+accountrebatetype+(tempdiscount*100)]=tempdiscount;
            maccounttransferinout['out'+accountrebatetype+(tempdiscount*100)]=tempaccounttransfer;
            discountarray.push('out'+accountrebatetype+(tempdiscount*100));
            numdiscountarray['out'+accountrebatetype+(tempdiscount*100)]=1;
        }else{
            maccounttransferinout['out'+accountrebatetype+(tempdiscount*100)]=thisInstance.FloatAdd(tempaccounttransfer,maccounttransferinout['out'+accountrebatetype+(tempdiscount*100)]);
            numdiscountarray['out'+accountrebatetype+(tempdiscount*100)]+=1;
        }
        $.each(discountarray,function(keytemp,valuetemp) {
            var mcashtransfervalue=0;
            var mdiscountValue=discountarrayinout[valuetemp];
            mdiscountValue=thisInstance.FloatDiv(mdiscountValue,100);
            if(valuetemp.indexOf('CashBack')>-1){
                mdiscountValue=thisInstance.FloatSub(1,mdiscountValue);
                mcashtransfervalue=thisInstance.FloatMul(mdiscountValue,maccounttransferinout[valuetemp]);
            }else{
                mdiscountValue=thisInstance.FloatAdd(1,mdiscountValue);
                mcashtransfervalue=thisInstance.FloatDiv(maccounttransferinout[valuetemp],mdiscountValue);
            }
            if(valuetemp.indexOf('out')==-1){
                tmaccounttransferin=thisInstance.FloatAdd(mcashtransfervalue,tmaccounttransferin);
            }else{
                tmaccounttransferout=thisInstance.FloatAdd(mcashtransfervalue,tmaccounttransferout);
            }
        });

        totalcashtransfer*=1.0;
        totalturnoverofaccount*=1.0;
        totalcashin*=1.0;
        totaltransfertoaccount*=1.0;
        totalcashtransfer=totalcashtransfer.toFixed(2);
        totalturnoverofaccount=totalturnoverofaccount.toFixed(2);
        totalcashin=totalcashin.toFixed(2);
        totaltransfertoaccount=totaltransfertoaccount.toFixed(2);
        tmaccounttransferout*=1.0;
        tmaccounttransferin*=1.0;
        tmaccounttransferout=tmaccounttransferout.toFixed(2);
        tmaccounttransferin=tmaccounttransferin.toFixed(2);


        if(tmaccounttransferout==tmaccounttransferin){
            var tempsub=thisInstance.FloatSub(totalcashtransfer,totalcashin);
            //那个小用那个值平均
            if(tempsub>0){
                var firstcashtransfer=$('input[name="cashtransfer"]').val();
                $('input[name="cashtransfer"]').val(thisInstance.FloatSub(firstcashtransfer,tempsub));
            }else if(tempsub<0){
                tempsub*=-1;
                var firstcashtransfer=$('input[name="mcashtransfer[1]"]').val();
                $('input[name="mcashtransfer[1]"]').val(thisInstance.FloatSub(firstcashtransfer,tempsub));
            }
        }
        $('input[name="totalcashtransfer"]').val(tmaccounttransferout);
        $('input[name="totalturnoverofaccount"]').val(totalturnoverofaccount);
        $('input[name="totalcashin"]').val(tmaccounttransferin);
        $('input[name="totaltransfertoaccount"]').val(totaltransfertoaccount);

    },
    showCustomerDetails:function(){
        $('#EditView').on("click",'#advancesmoney',function(event){
            var id =  $('#advancesmoney').attr('dd');
            var params={};
            params.data={
                "module": "RefillApplication",
                "action": "BasicAjax",
                "mode": "showTable",
                "accountid":id,
            };
            params.async=false;
            //var progressIndicatorElement = jQuery.progressIndicator({
            //    'message' : '正在加载...',
            //    'position' : 'html',
            //    'blockInfo' : {'enabled' : true}
            //});
            var returndata={'flag':false};
            AppConnector.request(params).then(
                function(data){
                    if(data.success){
                        var show_data = '';
                         $('#show_data1').empty();
                    show_data = '<tr>'+
                        '<td class="fieldLabel medium">'+
                            '<label class="muted pull-right marginRight10px">申请单编号</label>'+
                        '</td>'+
                        '<td class="fieldValue medium" style="width: 15px"><label class="muted pull-right marginRight10px">申请人</label></td>'+
                        '<td class="fieldLabel medium" style="width: 15px"><label class="muted pull-right marginRight10px">充值来源</label></td>'+
                        '<td class="fieldValue medium" style="width: 15px"><label class="muted pull-right marginRight10px">合计垫款金额</label></td>'+
                        '<td class="fieldValue medium" style="width: 15px"><label class="muted pull-right marginRight10px">状态</label></td>'+
                    '</tr>';
                            $.each(data.result.data,function(key,value){
                                show_data += "<tr><td class='fieldValue medium' style='width: 20px'><label class='muted pull-right marginRight10px'>"+value.refillapplicationno+"</label></td>"+
                                            "<td class='fieldLabel medium' style='width: 15px'><label class='muted pull-right marginRight10px'>"+value.last_name+' ['+value.department+']'+"</label></td>"+
                                            "<td class='fieldValue medium' style='width: 15px'><label class='muted pull-right marginRight10px'>"+value.rechargesource+"</label></td>"+
                                            "<td class='fieldValue medium' style='width: 15px'><label class='muted pull-right marginRight10px'>"+value.grossadvances+"</label></td>"+
                                            "<td class='fieldValue medium' style='width: 15px'><label class='muted pull-right marginRight10px'>"+value.modulestatus+"</label></td></tr>";
                            });
//                        return data=data.result;
                        $('#show_data1').append(show_data);
                        $('#show_data2').show();
                    }
                }
            );
            //returndata={'flag':true,'msg':'是否要确认提交!'};
//            return data;
        });
        $('.bootbox-close-button').on("click",function(event){
            $('#show_data2').hide();
        });

    },
    /***start***/
    /**
     * 增款申请
     */
    addincrease:function(){
        var thisInstance=this;
        $('.addincrease').click(function(){
            var message='确定要添加增款申请吗？';
            var msg={
                'message':message
            };
            var that=this;
            var num=$(that).attr('data-num');
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){

                num++;
                var rechargesource=$('input[name="rechargesource"]').val();
                var params={};
                params.data = {
                    "module": "RefillApplication",
                    "action": "BasicAjax",
                    "mode": "getAddEditCommon",
                    "datanum": num,
                    "rechargesource": rechargesource
                };
                $(that).attr('data-num',num);
                params.async=false;
                params.dataType='html';
                var respondata='';
                AppConnector.request(params).then(
                    function(data){
                        $('#increaselist').before(data);
                        $('select[name="maccountrebatetype['+num+']"]').chosen();
                        $('select[name="mreceivementcurrencytype['+num+']"]').chosen();
                    },
                    function (error) {
                    });



            },function(error, err) {});

        });
        $('#EditView').on("click",".delincrease",function(){
            var message='确定要删除增款申请吗？';
            var msg={
                'message':message
            };
            var num=$(this).data('num');
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e) {
                $(".increase"+num).remove();
                thisInstance.calcIncrease();
            });
        });
        $('#EditView').on("change","select[name^='maccountrebatetype\['],select[name='accountrebatetype']",function(){
            if($(this).attr('readonly')=='readonly'){
                return false;
            }
            var num=$(this).data('num');
            var cashconsumptionField=num>0?'mcashconsumption['+num+']':'cashconsumption';
            $("input[name='"+cashconsumptionField+"']").trigger("keyup");
        });
        $('#EditView').on("change keyup","input[name^='mtaxrefund\['],input[name='taxrefund'],input[name^='mcashconsumption\['],input[name='cashconsumption'],input[name^='mdiscount\['],input[name='discount']",function(){
            if($(this).attr('readonly')=='readonly'){
                return false;
            }
            thisInstance.formatNumber($(this));
            var num=$(this).data('num');
            var prefix=num>0?'m':'';
            var Suffix=num>0?'['+num+']':'';
            var rebatetype=$('select[name="'+prefix+'accountrebatetype'+Suffix+'"]').val();
            var taxrefund=$('input[name="'+prefix+'taxrefund'+Suffix+'"]').val();
            var cashconsumption=$('input[name="'+prefix+'cashconsumption'+Suffix+'"]').val();
            var cashincrease=$('input[name="'+prefix+'cashincrease'+Suffix+'"]');
            var discount=$('input[name="'+prefix+'discount'+Suffix+'"]').val();
            discount=thisInstance.FloatDiv(discount,100);
            if(rebatetype=='GoodsBack' && discount>0){
                discount=thisInstance.FloatAdd(1,discount);
                discount=thisInstance.FloatDiv(1,discount);
                discount=thisInstance.FloatSub(1,discount);
            }
            var cashincreaseVal=thisInstance.FloatMul(cashconsumption,discount);
            cashincreaseVal=thisInstance.FloatAdd(cashincreaseVal,taxrefund);
            cashincreaseVal*=1.0;
            cashincrease.val(cashincreaseVal.toFixed(2));
            thisInstance.calcIncrease();
        });
        $('#EditView').on("change",'select[name="granttype"]',function(){
            if($(this).val()=='paymentout'){
                $('.VENDOR_LBL_INFO').show();
            }else{
                $('.VENDOR_LBL_INFO').hide();
            }
        });
    },
    /**
     * 计算增款申请
     */
    calcIncrease:function(){
        var thisInstance=this;
        var cashincrease=$('input[name="cashincrease"]').val();
        var cashconsumption=$('input[name="cashconsumption"]').val();
        $.each($('input[name^="mcashincrease\["]'),function(key,value){
            var num=$(value).data("num");
            cashincrease=thisInstance.FloatAdd(cashincrease,$(value).val());
            cashconsumption=thisInstance.FloatAdd(cashconsumption,$('input[name="mcashconsumption['+num+']"]').val());
        });
        $('input[name="cashincreasetotal"]').val(cashincrease);
        $('input[name="cashconsumptiontotal"]').val(cashconsumption);
    },
    /**
     * 申请类型的更改触发
     */
    receivedstatuschange:function(){
        var thisInstance=this;
        $('select[name="receivedstatus"]').on("change",function(){
            if(this.selectedIndex==0){
                this.selectedIndex=thisInstance.seletedIndexValue;
                $(this).trigger('liszt:updated');
                return false;
            }
            var servicecontractsid=$('input[name="servicecontractsid"]').val();
            if(this.selectedIndex!=thisInstance.seletedIndexValue && servicecontractsid>0){
                $('input[name="totalrecharge"]').val(0);
                thisInstance.getReceivedPayments();
                $('input[name="factorage"],input[name^="mfactorage"]').trigger('keyup');
            }
        });
    },
    contractChange:function(){
        // 如果是充值申请单后边的问号去掉
        if($("input[name='rechargesource']").val()=='contractChanges'){
            $(".icon-question-sign").remove();
        }

        $("select[name='changecontracttype']").change(function(){
            if($(this).val()=='ServiceContracts'||$(this).val()=='SupplierContracts'){
                // 清空目标和同信息
                $("#newcontractsid_display").val('');
                $("#newaccountid_display").val('');
                $("select[name='newcustomertype']").val('');
                $('select[name="newcustomertype"]').trigger("liszt:updated");
                $("select[name='newiscontracted']").val('');
                $('select[name="newiscontracted"]').trigger("liszt:updated");
                $("input[name='newservicesigndate']").val('');
                $("input[name='newcontractamount']").val('');
                //清空原合同信息
                $("#servicecontractsid_display").val('');
                $("#accountid_display").val('');
                $("select[name='customertype']").val('');
                $('select[name="customertype"]').trigger("liszt:updated");
                $("select[name='iscontracted']").val('');
                $('select[name="iscontracted"]').trigger("liszt:updated");
                $("input[name='servicesigndate']").val('');
                $("input[name='contractamount']").val('');
                //清空已充值合同金额
                $("input[name='contractamountrecharged']").val('');
                if($(this).val()=='SupplierContracts'){
                      $("select[name='customertype']").val('');
                      $('select[name="customertype"]').trigger("liszt:updated");
                      $("select[name='newcustomertype']").val('');
                      $('select[name="newcustomertype"]').trigger("liszt:updated");
                      $(".blockOrNone").css("display","none");
                }else{
                      $(".blockOrNone").css("display","block");
                }
            }else{
                $(".blockOrNone").css("display","block");
            }
        });
        //当原充值单类型改变时
        $("select[name='oldrechargesource']").change(function(){
            var  servicecontractsid = $("input[name='servicecontractsid']").val();
            console.log(servicecontractsid);
            if(servicecontractsid){
                supplierContractsChanges();

            }
        })
        // 当原充值类型改变时修改
        function supplierContractsChanges(flag){
            var needContract = $("select[name='changecontracttype']").val();
            var  mode='getaccountinfo';
            if( needContract=='SupplierContracts'){
                mode='getSupplierAccountInfo';
            }
            var args=flag||0;
            var progressIndicatorElement = jQuery.progressIndicator({ 'message' : '合同信息加载中...','blockInfo':{'enabled':true }});
            var rechargesource = $("input[name='rechargesource']").val();
            var params={};
            params.data= {'module':'RefillApplication','mode':mode,'action':'BasicAjax','oldrechargesource':$("select[name='oldrechargesource']").val(),'rechargesource':rechargesource,'record':$('input[name="servicecontractsid"]').val()};
            params.async=false;
            $("#advancesmoney").remove();
            if($('input[name="rechargesource"]').val()=='PreRecharge'){
                thisInstance.vendoridInstance(flag);
                return false;
            }
            if(args==0){
                $('input[name=servicesigndate]').val('');
                $('#RefillApplication_editView_fieldName_iscontracted').prop("checked",false);
                $('.newinvoicerayment_tab ').remove();
            }
            //var record=jQuery('input[name="record"]').val();
            AppConnector.request(params).then(
                function(data){
                    progressIndicatorElement.progressIndicator({ 'mode' : 'hide'});
                    if(data.success){
                        //progressIndicatorElement.progressIndicator({ 'mode' : 'hide'});
                        if(data.success ==  true){
                            var result=data.result[1];
                            if(args>0){
                            }else{
                                $(".needToRemove").remove();
                                $("#refillApplicationList").after(result.strHtml);
                                //清空已经计算好的充值申请单金额信息
                                $("input[name='grossadvances']").val('');
                                $("input[name='actualtotalrecharge']").val('');
                                $("input[name='totalreceivables']").val('');
                                $("input[name='changesnumber']").val('');
                            }
                        }
                    }
                },
                function(error){
                    console.log(error);
                }
            );
        }


        $(".contractChange").click(function () {
            if ($(this).prop("checked") == true) {
                $("input[name='contractChangeApplication[]']").prop("checked", true);
            } else {
                $("input[name='contractChangeApplication[]']").prop("checked", false);
            }
            calculationRechargeInfo();
        });
        $('#EditView').on("click",".entryCheckBox",function(){
            calculationRechargeInfo();
        });
        function checkFormContent(){
            return false;
        }
        // 计算要变更充值单金额信息求和
        function calculationRechargeInfo(){
            var Detailrecords=$('input[name="contractChangeApplication\[\]"]:checkbox:checked');
            var lengths=Detailrecords.length;
            if(lengths>0){
                var  grossadvances=0;
                var actualtotalrecharge=0;
                var totalreceivables=0;
                $.each(Detailrecords,function(key,value){
                    grossadvances = grossadvances+$(value).data("grossadvances");
                    actualtotalrecharge = actualtotalrecharge+$(value).data("actualtotalrecharge");
                    totalreceivables = totalreceivables+$(value).data("totalreceivables");
                });
                $("input[name='grossadvances']").val(grossadvances);
                $("input[name='actualtotalrecharge']").val(actualtotalrecharge);
                $("input[name='totalreceivables']").val(totalreceivables);
                $("input[name='changesnumber']").val(lengths);
            }else{
                $("input[name='grossadvances']").val('');
                $("input[name='actualtotalrecharge']").val('');
                $("input[name='totalreceivables']").val('');
                $("input[name='changesnumber']").val('');
            }
        }
    },
    conversiontypeChange:function(){
        var thisInstance=this;
        var oldValue=$('select[name="conversiontype"]').val();
        if(oldValue=='AccountPlatform'){
            $('#vendorid_display+.relatedPopup').remove();
            $('#vendorid_display').removeAttr('data-validation-engine');
            $('input[name="vendorid"]').val('');
            $('#vendorid_display').val('');
        }
        var datavalidationengine=$('select[name="conversiontype"]').attr('data-validation-engine');
        $('select[name="conversiontype"]').on('change',function(){
            var thisValue=$(this).val();
            var accountid=$('input[name="accountid"]').val();
            var vendorid=$('input[name="vendorid"]').val();
            if(oldValue!=thisValue && thisValue!=''){
                if(thisValue=='AccountPlatform'){
                    $('#vendorid_display+.relatedPopup').remove();
                    $('#vendorid_display').removeAttr('data-validation-engine');
                    $('input[name="vendorid"]').val('');
                    $('#vendorid_display').val('');
                    $('#vendorid_display').attr("readonly","readonly");
                    if(accountid>0){
                        // thisInstance.getAccountPlatform(accountid);
                    }
                }else{
                    $('#vendorid_display').removeAttr('data-validation-engine');
                    $('#vendorid_display+.relatedPopup').remove();
                    $('#vendorid_display').after('<span class="add-on relatedPopup cursorPointer"><i id="RefillApplication_editView_fieldName_vendorid_select" class="icon-search relatedPopup1" title="选择"></i></span>');
                    $('#vendorid_display').attr('data-validation-engine',datavalidationengine);
                    $('#vendorid_display').removeAttr("readonly");
                    if(accountid>0 && vendorid>0){
                        //thisInstance
                    }
                }
            }
            oldValue=thisValue;
        });
    },

    calculationProportion:function(){
        //在非媒体类外采情况下计算应付款金额
        if($("input[name='rechargesource']").val()=='NonMediaExtraction'){
            var thisInstance=this;
            $('#EditView').on("change keyup",'input[name="nonaccountrebate"],select[name="nonaccountrebatetype"],input[name="actualtotalrecharge"],input[name="totalreceivables"]',function(){
                if($(this).prop('name')=='nonaccountrebate'){
                    thisInstance.formatNumber($(this));
                }
                var nonaccountrebate=$("input[name='nonaccountrebate']").val();
                var nonaccountrebatetype=$("select[name='nonaccountrebatetype']").val();
                var actualtotalrecharge=$("input[name='actualtotalrecharge']").val()?$("input[name='actualtotalrecharge']").val():0;
                var totalreceivables=$("input[name='totalreceivables']").val()?$("input[name='totalreceivables']").val():0;
                if(nonaccountrebate&&nonaccountrebatetype){
                    if(nonaccountrebatetype=='CashBack'){
                        //返现
                       var rate=thisInstance.FloatSub(100,nonaccountrebate);
                        $("input[name='actualtotalrecharge']").val(thisInstance.FloatDiv(thisInstance.FloatMul(totalreceivables,rate),100).toFixed(2));
                    } else {
                        //返货
                        var rate=thisInstance.FloatAdd(100,nonaccountrebate);
                        $("input[name='actualtotalrecharge']").val(thisInstance.FloatMul(thisInstance.FloatDiv(totalreceivables,rate),100).toFixed(2));
                    }
                }
            });
        }
    },

    /**
     * 投放初始化
     */
    initThrowtime:function(){
        $('#RefillApplication_editView_fieldName_throwtime').datetimepicker({
            format: "yyyy-mm",
            language:  'zh-CN',
            autoclose: true,
            todayBtn: true,
            pickerPosition: "bottom-right",
            showMeridian: 0,
            weekStart:1,
            todayHighlight:1,
            forceParse:0,
            startView: 'year',
            minView:'year',
            maxView:'year'
        });
        $('#RefillApplication_editView_fieldName_throwtime').attr('readonly',true);
    },
    batchimport:function(){
        var thisInstance=this;
        $('#batchimport').on("click",function(){
            var accountid = $("input[name='accountid']").val();
            if(!accountid){
                 Vtiger_Helper_Js.showMessage({type:'error',text:'请先选择合同'});
                 return;
            }
            $("#importrefill").trigger("click");

        });
    },
    batchoutput:function(){
        var thisInstance=this;
        $('#batchoutput').on("click",function(){
            var accountid = $("input[name='accountid']").val();
            if(!accountid){
                Vtiger_Helper_Js.showMessage({type:'error',text:'请先选择合同'});
                return;
            }
            $("#outputrefill").trigger("click");

        });
    },
    uploadBatchOutput:function(){
        var thisInstance=this;
        $("#outputrefill").on("change",function () {
            var outputrefill = $('#outputrefill').val();
            if(!outputrefill){
                // Vtiger_Helper_Js.showMessage({type:'error',text:'文件格式异常'});
                return;
            }
            var file =$('#outputrefill')[0].files[0];
            var reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function() {
                // console.log(this.result);

                var base64Str = this.result;
                var startNum = base64Str.indexOf("base64,");
                startNum = startNum * 1 + 7;
                var headerStr = base64Str.slice(0,startNum);
                if(headerStr!='data:application/vnd.ms-excel;base64,'){
                    Vtiger_Helper_Js.showMessage({type:'error',text:'文件格式异常'});
                    return;
                }
                // console.log(headerStr);
                var baseStr = base64Str.slice(startNum);

                //临时存储二进制流
                $("#tempName").val(baseStr);
                var accountid = $('input[name=accountid]').val();
                var params={};
                params.module="RefillApplication";
                params.action="BasicAjax";
                params.mode="uploadBatchOutput";
                params.fileData=baseStr;
                params.fileName=file.name;
                params.record=accountid;
                AppConnector.request(params).then(
                    function(data){
                        $('#importrefill').val('');
                        if(data.success){
                            // $(".Duplicates").remove();
                            $(data.data).each(function (k, v) {
                                if(k==0){
                                    var mid = $('input[class="sourceField"]').attr("name");
                                    $('input[name="did"]').val(v.idaccount);
                                    $('input[name="did_display"]').val(v.idaccount);
                                    thisValue=$('input[name="prestoreadrate"]').val(v.prestoreadrate);
                                }else{
                                    // $("#addfallinto").trigger("click");

                                    var datatype='out';
                                    var accountId=$('input[name="accountid"]').val();
                                    var rechargesource=$('input[name="rechargesource"]').val();
                                    if(!accountId){
                                        Vtiger_Helper_Js.showMessage({type:'error',text:'请先选择客户'});
                                        return false;
                                    }
                                    var numd = $('.Duplicates').length + 1;
                                    if (numd > 16) {
                                        return;
                                    }
                                    /*超过20个不允许添加*/
                                    var currnetNum=0
                                    $.each($('.Duplicates'),function(key,value){
                                        var valueNum=$(value).data('num');
                                        if(valueNum>=currnetNum){
                                            currnetNum=valueNum;
                                        }
                                    });
                                    if (currnetNum>0) {
                                        numd = currnetNum + 1;
                                    }
                                    var extend = COINRETURNsheet.replace(/\[\]|replaceyes/g, '[' + numd + ']');
                                    extend = extend.replace(/yesreplace/g, numd);
                                    extend = extend.replace(/#inorout#/g, datatype);
                                    var insertit=datatype=='in'?'insertafter':'insertbefore';
                                    var inoroutname=datatype=='in'?'<span class="label label-a_normal">转入</span>':'<span class="label label-c_stamp">转出</span>';
                                    extend = extend.replace(/inoroutname/g, inoroutname);
                                    $('#'+insertit).before(extend);
                                    //去掉原本的下拉框，换上弹框
                                    var replaceIdStr='<td class="fieldValue medium"><input name="popupReferenceModule" type="hidden" value="RefillApplication" autocomplete="off"><input name="mid['+numd+']" type="hidden" data-cid="'+numd+'" value="" data-multiple="0" class="sourceField" data-displayvalue="" data-fieldinfo="{&quot;mandatory&quot;:true,&quot;presence&quot;:true,&quot;quickcreate&quot;:true,&quot;masseditable&quot;:true,&quot;defaultvalue&quot;:false,&quot;type&quot;:&quot;string&quot;,&quot;name&quot;:&quot;did&quot;,&quot;label&quot;:&quot;ID&quot;}" autocomplete="off"><div class="row-fluid input-prepend input-append"><span class="add-on clearReferenceSelection cursorPointer"><i id="RefillApplication_editView_fieldName_did_clear" class="icon-remove-sign" title="清除"></i></span><input id="mid_display['+numd+']" readonly="readonly" name="mid_display['+numd+']" type="text" class=" span7 marginLeftZero autoComplete" value="" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{&quot;mandatory&quot;:true,&quot;presence&quot;:true,&quot;quickcreate&quot;:true,&quot;masseditable&quot;:true,&quot;defaultvalue&quot;:false,&quot;type&quot;:&quot;string&quot;,&quot;name&quot;:&quot;did&quot;,&quot;label&quot;:&quot;ID&quot;}" placeholder="查找.." autocomplete="off"><span data-id="RefillApplication_editView_fieldName_did_select" class="add-on relatedPopupDid cursorPointer"><i id="RefillApplication_editView_fieldName_did_select" data-id="RefillApplication_editView_fieldName_did_select" class="icon-search relatedPopupDid" title="选择"></i></span></div></td>'
                                    if(datatype=='in'){
                                        $('.Duplicates').last().find('tbody .fieldValue').eq(0).remove();
                                        $('.Duplicates').last().find('tbody .fieldLabel').eq(0).after(replaceIdStr);
                                    }else{
                                        $('#insertbefore').prev().find('tbody .fieldValue').eq(0).remove();
                                        $('#insertbefore').prev().find('tbody .fieldLabel').eq(0).after(replaceIdStr);
                                    }
                                    $('.chzn-select').chosen();


                                    // k=k+1;
                                    k=numd;
                                    var mid = 'mid['+k+']';
                                    var mid_display = 'mid_display['+k+']';
                                    $('input[name="'+mid+'"]').val(v.idaccount);
                                    $('input[name="'+mid_display+'"]').val(v.idaccount);
                                    thisValue=$('input[name="mprestoreadrate['+k+']"').val(v.prestoreadrate);



                                }
                                thisInstance.setAccountPlatformOneInfo(v,mid);

                                var cid=k;
                                cid=cid>0?cid:0;


                                var topplatform=cid==0?'productid_display':'mproductid_display['+cid+']';
                                var topplatform=$('input[name="'+topplatform+'"]').val();
                                if(topplatform=='谷歌' || topplatform=='Yandex'){
                                    thisInstance.googleTransferamountCalc(cid);
                                }else{
                                    thisInstance.calcAechargeamountANDPrestoreadrate(cid);
                                }
                            })
                        }else{
                            Vtiger_Helper_Js.showMessage({type:'error',text:data.msg});
                        }

                    },
                    function (error) {

                    });
            }

        })
    },
    batchinput:function(){
        var thisInstance=this;
        $('#batchinput').on("click",function(){
            var accountid = $("input[name='accountid']").val();
            if(!accountid){
                Vtiger_Helper_Js.showMessage({type:'error',text:'请先选择合同'});
                return;
            }
            $("#inputrefill").trigger("click");

        });
    },
    uploadBatchInput:function(){
        var thisInstance=this;
        $("#inputrefill").on("change",function () {
            var inputrefill = $('#inputrefill').val();
            if(!inputrefill){
                // Vtiger_Helper_Js.showMessage({type:'error',text:'文件格式异常'});
                return;
            }
            var file =$('#inputrefill')[0].files[0];
            var reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function() {
                // console.log(this.result);

                var base64Str = this.result;
                var startNum = base64Str.indexOf("base64,");
                startNum = startNum * 1 + 7;
                var headerStr = base64Str.slice(0,startNum);
                if(headerStr!='data:application/vnd.ms-excel;base64,'){
                    Vtiger_Helper_Js.showMessage({type:'error',text:'文件格式异常'});
                    return;
                }
                // console.log(headerStr);
                var baseStr = base64Str.slice(startNum);

                //临时存储二进制流
                $("#tempName").val(baseStr);
                var accountid = $('input[name=accountid]').val();
                var params={};
                params.module="RefillApplication";
                params.action="BasicAjax";
                params.mode="uploadBatchOutput";
                params.fileData=baseStr;
                params.fileName=file.name;
                params.record=accountid;
                AppConnector.request(params).then(
                    function(data){
                        $('#importrefill').val('');
                        if(data.success){
                            // $(".Duplicates").remove();
                            $(data.data).each(function (k, v) {
                                if(k==0){
                                    k=k+1;
                                    var mid = 'mid['+k+']';
                                    var mid_display = 'mid_display['+k+']';
                                    $('input[name="'+mid+'"]').val(v.idaccount);
                                    $('input[name="'+mid_display+'"]').val(v.idaccount);
                                    thisValue=$('input[name="mprestoreadrate['+k+']"').val(v.prestoreadrate);

                                    // var mid = $('input[class="sourceField"]').attr("name");
                                    // $('input[name="mdid[1]"]').val(v.idaccount);
                                    // $('input[name="mid_display[1]"]').val(v.idaccount);
                                    // thisValue=$('input[name="prestoreadrate"]').val(v.prestoreadrate);
                                    // thisInstance.setAccountPlatformOneInfo(v,mid);
                                }else{
                                    // $("#addfallinto").trigger("click");

                                    var datatype='in';
                                    var accountId=$('input[name="accountid"]').val();
                                    var rechargesource=$('input[name="rechargesource"]').val();
                                    if(!accountId){
                                        Vtiger_Helper_Js.showMessage({type:'error',text:'请先选择客户'});
                                        return false;
                                    }
                                    var numd = $('.Duplicates').length + 1;
                                    if (numd > 16) {
                                        return;
                                    }
                                    /*超过20个不允许添加*/
                                    var currnetNum=0
                                    $.each($('.Duplicates'),function(key,value){
                                        var valueNum=$(value).data('num');
                                        if(valueNum>=currnetNum){
                                            currnetNum=valueNum;
                                        }
                                    });
                                    if (currnetNum>0) {
                                        numd = currnetNum + 1;
                                    }
                                    var extend = COINRETURNsheet.replace(/\[\]|replaceyes/g, '[' + numd + ']');
                                    extend = extend.replace(/yesreplace/g, numd);
                                    extend = extend.replace(/#inorout#/g, datatype);
                                    var insertit=datatype=='in'?'insertafter':'insertbefore';
                                    var inoroutname=datatype=='in'?'<span class="label label-a_normal">转入</span>':'<span class="label label-c_stamp">转出</span>';
                                    extend = extend.replace(/inoroutname/g, inoroutname);
                                    $('#'+insertit).before(extend);
                                    //去掉原本的下拉框，换上弹框
                                    var replaceIdStr='<td class="fieldValue medium"><input name="popupReferenceModule" type="hidden" value="RefillApplication" autocomplete="off"><input name="mid['+numd+']" type="hidden" data-cid="'+numd+'" value="" data-multiple="0" class="sourceField" data-displayvalue="" data-fieldinfo="{&quot;mandatory&quot;:true,&quot;presence&quot;:true,&quot;quickcreate&quot;:true,&quot;masseditable&quot;:true,&quot;defaultvalue&quot;:false,&quot;type&quot;:&quot;string&quot;,&quot;name&quot;:&quot;did&quot;,&quot;label&quot;:&quot;ID&quot;}" autocomplete="off"><div class="row-fluid input-prepend input-append"><span class="add-on clearReferenceSelection cursorPointer"><i id="RefillApplication_editView_fieldName_did_clear" class="icon-remove-sign" title="清除"></i></span><input id="mid_display['+numd+']" readonly="readonly" name="mid_display['+numd+']" type="text" class=" span7 marginLeftZero autoComplete" value="" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{&quot;mandatory&quot;:true,&quot;presence&quot;:true,&quot;quickcreate&quot;:true,&quot;masseditable&quot;:true,&quot;defaultvalue&quot;:false,&quot;type&quot;:&quot;string&quot;,&quot;name&quot;:&quot;did&quot;,&quot;label&quot;:&quot;ID&quot;}" placeholder="查找.." autocomplete="off"><span data-id="RefillApplication_editView_fieldName_did_select" class="add-on relatedPopupDid cursorPointer"><i id="RefillApplication_editView_fieldName_did_select" data-id="RefillApplication_editView_fieldName_did_select" class="icon-search relatedPopupDid" title="选择"></i></span></div></td>'
                                    if(datatype=='in'){
                                        $('.Duplicates').last().find('tbody .fieldValue').eq(0).remove();
                                        $('.Duplicates').last().find('tbody .fieldLabel').eq(0).after(replaceIdStr);
                                    }else{
                                        $('#insertbefore').prev().find('tbody .fieldValue').eq(0).remove();
                                        $('#insertbefore').prev().find('tbody .fieldLabel').eq(0).after(replaceIdStr);
                                    }
                                    $('.chzn-select').chosen();


                                    k=numd;
                                    var mid = 'mid['+k+']';
                                    var mid_display = 'mid_display['+k+']';
                                    $('input[name="'+mid+'"]').val(v.idaccount);
                                    $('input[name="'+mid_display+'"]').val(v.idaccount);
                                    thisValue=$('input[name="mprestoreadrate['+k+']"').val(v.prestoreadrate);

                                }
                                thisInstance.setAccountPlatformOneInfo(v,mid);

                                var cid=k;
                                cid=cid>0?cid:0;


                                var topplatform=cid==0?'productid_display':'mproductid_display['+cid+']';
                                var topplatform=$('input[name="'+topplatform+'"]').val();
                                if(topplatform=='谷歌' || topplatform=='Yandex'){
                                    thisInstance.googleTransferamountCalc(cid);
                                }else{
                                    thisInstance.calcAechargeamountANDPrestoreadrate(cid);
                                }
                            })
                        }else{
                            Vtiger_Helper_Js.showMessage({type:'error',text:data.msg});
                        }

                    },
                    function (error) {

                    });
            }

        })
    },
    uploadBatchImport:function(){
        var thisInstance=this;
        $("#importrefill").on("change",function () {
            var importrefill = $('#importrefill').val();
            if(!importrefill){
                // Vtiger_Helper_Js.showMessage({type:'error',text:'文件格式异常'});
                return;
            }
            var file =$('#importrefill')[0].files[0];
            var reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function() {
                // console.log(this.result);

                var base64Str = this.result;
                var startNum = base64Str.indexOf("base64,");
                startNum = startNum * 1 + 7;
                var headerStr = base64Str.slice(0,startNum);
                if(headerStr!='data:application/vnd.ms-excel;base64,'){
                     Vtiger_Helper_Js.showMessage({type:'error',text:'文件格式异常'});
                     return;
                }
                // console.log(headerStr);
                var baseStr = base64Str.slice(startNum);

                //临时存储二进制流
                $("#tempName").val(baseStr);
                var accountid = $('input[name=accountid]').val();
                var params={};
                params.module="RefillApplication";
                params.action="BasicAjax";
                params.mode="uploadBatchImport";
                params.fileData=baseStr;
                params.fileName=file.name;
                params.record=accountid;
                AppConnector.request(params).then(
                    function(data){
                        $('#importrefill').val('');
                        if(data.success){
                            $(".Duplicates").remove();
                            $(data.data).each(function (k, v) {
                                if(k==0){
                                    var mid = $('input[class="sourceField"]').attr("name");
                                    $('input[name="did"]').val(v.idaccount);
                                    $('input[name="did_display"]').val(v.idaccount);
                                    thisValue=$('input[name="prestoreadrate"]').val(v.prestoreadrate);
                                }else{
                                    $("#addfallinto").trigger("click");
                                    var mid = 'mid['+k+']';
                                    var mid_display = 'mid_display['+k+']';
                                    $('input[name="'+mid+'"]').val(v.idaccount);
                                    $('input[name="'+mid_display+'"]').val(v.idaccount);
                                    thisValue=$('input[name="mprestoreadrate['+k+']"').val(v.prestoreadrate);
                                }
                                thisInstance.setAccountPlatformOneInfo(v,mid);

                                var cid=k;
                                cid=cid>0?cid:0;


                                var topplatform=cid==0?'productid_display':'mproductid_display['+cid+']';
                                var topplatform=$('input[name="'+topplatform+'"]').val();
                                if(topplatform=='谷歌' || topplatform=='Yandex'){
                                    thisInstance.googleTransferamountCalc(cid);
                                }else{
                                    thisInstance.calcAechargeamountANDPrestoreadrate(cid);
                                }
                            })
                        }else{
                            Vtiger_Helper_Js.showMessage({type:'error',text:data.msg});
                        }

                    },
                    function (error) {

                    });
            }

        })
    },


    /***endl***/
	registerEvents: function(){
		this._super();
        //$('#accountid_display').attr('readonly','readonly');
        //$('#accountid_display').removeAttr('placeholder');
        //$('#RefillApplication_editView_fieldName_accountid_clear,#RefillApplication_editView_fieldName_accountid_select').parent().remove();
        $(':input').attr("autocomplete","off");//去掉input记忆功能
        this.inputnumberchange();
        this.addrechargesheets();
        this.deleteregchargesheet();
        this.registerRecordPreSaveEvent();
        this.instancethis();
        //查询充值申请单账户 2017/03/6 gaocl add
        //$('input[name="accountzh"]').after('<button id="historyAccountzh_0" type="button" class="btn btn-info">历史账号</button>');
        // $('input[name="accountzh"]').attr("disabled",true);
        this.getHistoryAccountzh(0);
        this.delbuttonnewinvoicerayment();
        this.changeDID();
        this.prestoreadratecalc();
        this.productserviceChange();
        this.instanceLoading();
        this.getReceivedPaymentsList();
        this.selectedChange();
        this.banklistchange();
        this.turncashin();
        this.showCustomerDetails();
        this.addincrease();
        this.receivedstatuschange();
        this.contractChange();
        this.conversiontypeChange();
        this.batchDeletePay();
        this.checkAll();
        window.onbeforeunload = function(){
            return;
        }
        this.calculationProportion();
        this.initThrowtime();
        this.batchimport();
        this.batchoutput();
        this.batchinput();
        this.uploadBatchImport();
        this.uploadBatchOutput();
        this.uploadBatchInput();
	}
});


