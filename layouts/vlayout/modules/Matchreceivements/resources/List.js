/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("Matchreceivements_List_Js",{},{
	ActiveClick:function(){
		var  url = window.location.pathname + window.location.search;
		url=url.replace('/','');
		jQuery('.breadcrumb li').find("a[href='" + url + "']").css('color',"#1A94E6");

	},

    click_button:function(){
        var  getThis= this;
        $('#table_match').on('click','.throw',function(){
           if(confirm('确定放弃这条回款？')){
               var tr = $(this).closest('tr');
               var params = {};
               params['action'] = 'BasicAjax';
               params['module'] = 'Matchreceivements';
               params['receivepayid'] = $(tr).find('.receivepayments').val();
               params['mode'] = 'throwreceivement';
               AppConnector.request(params).then(
                   function(data){
                       $(tr).remove();
                   });
           }
        });
        $('#table_match').on('click','.option',function(){
            var tr = $(this).closest('tr');
            if(!$(tr).find("select[name='contractid[]']").val()){
                Vtiger_Helper_Js.showMessage({type:'error',text:'请选择合同编号'});
                return;
            }
            var receivedstatus=$(tr).find('select[name="receivedstatus[]"]').val();
            if(receivedstatus==0){
                Vtiger_Helper_Js.showMessage({type:'error',text:'请选择回款类型！'});
                return false;
            }

            if(!$("#isHasChecked").prop("checked") && receivedstatus=='normal'){
                var msg={'message':"你还未确认合同及业绩信息，请勾选确认后再行匹配！"};
                getThis.showConfirmationBox(msg).then(function(e){
                    $("#isHasChecked").focus();
                    return false;
                });
                return false;
            }
            var prestaypaymentid = $(tr).find("select[name='contractid[]'] option:selected").data("staypaymentid");
            var preurl = '';
            if(prestaypaymentid){
                var staypaymentid = $(tr).find("select[name='staypaymentjine[]'] ").val();
                console.log(staypaymentid);
                var staypyamenttype =$(tr).find("input[name='staypaymenttype[]'] ").val();
                if(!staypaymentid && (staypyamenttype=='fixation')){
                    Vtiger_Helper_Js.showMessage({type:'error',text:'请先选择代付款金额'});
                    return;
                }
                preurl ="&staypaymentid="+staypaymentid;
                var selectStaypaymentContractNo= $(tr).find("select[name='contractid[]'] option:selected").html();
                var staypaymentContractNo= $(tr).find("select[name='staypaymentjine[]'] option:selected").data("contract_no");
                if(staypaymentContractNo!=undefined && staypaymentContractNo!=selectStaypaymentContractNo){
                    Vtiger_Helper_Js.showMessage({type:'error',text:'选择的代付款和要匹配的合同不一致'});
                    return;
                }

            }

             msg = '确定匹配这条合同么？';
            var str = $('#fallintotable').html();
            if(str != null){
                num = str.indexOf('离职');
                var msg = '';
                if(parseInt(num)>0){
                    msg = '该合同有离职的业绩所属人，可以点击取消然后放弃匹配，先到服务合同里申请业绩所属人变更；或者点击确定，继续匹配';
            }else{
                msg = '确定匹配这条合同么？';
                }
            }

            if(confirm(msg)){
                var listInstance = Vtiger_List_Js.getInstance();
                var shareuser=$(tr).find("input[name='shareuser[]']").val();
                var progressIndicatorElement = jQuery.progressIndicator({
                    'message' : '回款匹配中。。。',
                    'position' : 'html',
                    'blockInfo' : {'enabled' : true}
                });

                var url = "index.php?action=BasicAjax&module=Matchreceivements&receivepayid="+$(tr).find('.receivepayments').val()+"&shareuser="+shareuser+"&contractid="+$(tr).find("select[name='contractid[]']").val()+"&receivedstatus="+receivedstatus+"&total="+$(tr).find('.total').val();
                var prestaypaymentid = $(tr).find("select[name='contractid[]']").data("staypaymentid");
                if(preurl){
                    url +=preurl;
                }
                url += "&"+$('.inputalready').serialize();
                AppConnector.request(url).then(
                function(data){
                    progressIndicatorElement.progressIndicator({'mode': 'hide'});
                    if(data.success){
                        if(data.result.flag){
                            Vtiger_Helper_Js.showMessage({type:'success',text:'匹配成功'});
                            if(data.result.module=='invoice'){
                                alert("该回款有未匹配的预开票!");
                                $('.widgetContainer_receivehistory').before(data.result.msg);
                            }
                            if(data.result.module=='is_advances'){
                                msg = '该合同下有需要关联回款的垫款充值申请单，是否要进入到充值申请单列表进行关联销账，如果是，请点击确定.';
                                var userid = data.result.data.userid;
                                var  contract_no = data.result.data.contract_no;
                                   if(confirm(msg)){
//                                       alert("index.php?module=RefillApplication&view=List&rechargesource=COINRETURN&is_advances=1&contract_no="+contract_no+"&userid="+userid);
                                        window.location.href = "index.php?module=RefillApplication&view=List&is_advances=1&contract_no="+contract_no+"&userid="+userid;
                                   }

                            }
                            $('.widgetContainer_receivehistory').html('');
                            $('#servicecontract').html('');
                            $(tr).remove();
			    $("#isExistServicecontract").css("display","none");
                            $("#isExistServicecontract").prop("checked",false);
                            window.location.reload();
                        }else{
                            alert(data.result.msg);
                        }
                    }
                });
        }
        });
        $('#table_match').on('change',"select[name='contractid[]']",function(){
            $(this).closest('tr').find('td:last button:last').attr('data-id',$(this).val());
            $('#servicecontract').html('');
            $('.widgetContainer_receivehistory').html('');
            $("#isExistServicecontract").css("display","none");
            var tr1 = $(this).closest('tr');
            var tr = $(this).closest('tr');
            $("option:selected",this).data('module');
            if(!$(tr).find("select[name='contractid[]']").val()){
                return;
            }
            var moduleName=$("option:selected",this).data('module');
            if(moduleName=='SupplierContracts'){
                return ;
            }
            var progressIndicatorElement = jQuery.progressIndicator({
                'message' : '查找合同信息',
                'position' : 'html',
                'blockInfo' : {'enabled' : true}
            });
            var sc=$(tr1).find("select[name='contractid[]']").val();; //合同id
            var record =$(tr1).find('.receivepayments').val();//回款id
            var requestmodule = 'ReceivedPayments';
            var prices = $(tr1).find('.total').val();//回款金额
            var moduleName=$("option:selected",this).data('module');
            var realoperate=$("option:selected",this).data('realoperate');
            var urlParam ='module='+moduleName+'&view=Detail&mode=getservicecontractsinfo&record='+sc+"&receivepayid="+record+"&requestmodule="+requestmodule+'&realoperate='+realoperate;
            AppConnector.request(urlParam).then(
                function (data) {
                   $('.info').html('<h4 style="color:red;">请仔细核查合同信息,谨慎操作</h4>');
                   $('.widgetContainer_receivehistory').html(data);
                }
            );
            var tablelabel=(moduleName=='SupplierContracts')?'采购单合同 详细内容':'服务合同 详细内容';
            var servicecontracts_param={};
            servicecontracts_param['module']=moduleName;
            servicecontracts_param['view']='Detail';
            servicecontracts_param['record']=$(tr1).find("select[name='contractid[]']").val();
            servicecontracts_param['mode']='showDetailViewByMode';
            servicecontracts_param['requestMode']='full';
            servicecontracts_param['realoperate']=realoperate;
            servicecontracts_param['tab_label']=tablelabel;
            AppConnector.request(servicecontracts_param).then(
                function (datas) {
                    progressIndicatorElement.progressIndicator({'mode': 'hide'});
                  $('#servicecontract').html(datas);
                  $('.relatedContents').remove();
                  $("#isExistServicecontract").css("display","block");
                }
            );
        })

        //拆分回款
        $('#table_match').on('click','.split',function(){
             var receivePaymentId=$(this).data('rid');
             var postData = {
                 "module": 'Matchreceivements',
                 "action": "BasicAjax",
                 "record": receivePaymentId,
                 'status': 1,
                 'mode': 'getSplitServiceContracts'
             }
             AppConnector.request(postData).then(
                 // 请求成功
                 function(data){
                     if (data.success) {
                         if(data.result.isover==1){
                             Vtiger_Helper_Js.showPnotify(app.vtranslate('该回款已匹配且业绩已确认完结'));
                             return false;
                         }
                         var unit_price = parseFloat(data.result.unit_price);  //回款金额
                         // 成功
                         var msg = {
                             'message': '回款拆分 可拆分金额：'+unit_price,
                             "width":"400px",
                         };
                         Vtiger_Helper_Js.showConfirmationBox(msg).then(
                             function(e) {
                                 var step=false;
                                 //走另外一条路没合同
                                 var splitSumMoney=0;
                                 $("input[name='split_money[]']").each(function (i) {
                                     var splitSingleMoney=$.trim($(this).val());
                                     if(isNaN(splitSingleMoney)||splitSingleMoney==""||splitSingleMoney<=0){
                                         Vtiger_Helper_Js.showPnotify(app.vtranslate('拆分的第'+(i+1)+'行金额必须是数字且大于零'));
                                         return false;
                                     }else{
                                         splitSumMoney=getThis.accAdd(splitSumMoney,splitSingleMoney);
                                     }
                                     step=true;
                                 });
                                 if(step&&(splitSumMoney>=unit_price||splitSumMoney<=0)){
                                     Vtiger_Helper_Js.showPnotify(app.vtranslate('分拆金额总和必须小于原始金额且大于零'));
                                     return false;
                                 }else if(step){
                                     var postData = {
                                         "module": 'Matchreceivements',
                                         "action": "BasicAjax",
                                         "record": receivePaymentId,
                                         'split_money': $("input[name='split_money[]']").serialize(),
                                         'unit_price':unit_price,
                                         'mode': 'splitBatchReceive'
                                     }
                                     AppConnector.request(postData).then(
                                         function(data) {
                                             if (data.success) {
                                                 if (data.result.flag) {
                                                     location.reload();
                                                 } else {
                                                     var errorMsg="以下拆分失败，详情如下：<br>";
                                                     for(var i=0;i<data.result.msg.length;i++){
                                                         if(data.result.msg[i]){
                                                             errorMsg+='第'+(i+1)+'行拆分失败，原因是'+data.result.msg[i]+'<br>';
                                                         }
                                                     }
                                                     errorMsg+='查看完请刷新页面';
                                                     Vtiger_Helper_Js.showPnotify(app.vtranslate(errorMsg));
                                                 }
                                             }
                                         },
                                         function(error,err){}
                                     );
                                 }
                             },function(error, err){}
                         )
                         $('.modal-body').append('<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0;"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">分拆金额:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><input type="number" name="split_money[]" /></span><i title="增加" style="cursor:pointer;margin-right: 10px;" class="icon-plus alignMiddle addSplit"></i></div></td></tr></tbody></table><strong><p style="color:red">特别说明：</p></strong><p style="color:red">1.只需填写分拆金额，剩余拆分金额无需填写。</p><p style="color:red">2.匹配回款后，无法再拆分回款，请在匹配回款前进行回款拆分。</p>');
                     } else {
                         Vtiger_Helper_Js.showPnotify(app.vtranslate('操作失败'));
                     }
                 },
                 function(error,err){
                 }
             );
        });

    },

    /**
     * 加法相加的问题
     * @param arg1
     * @param arg2
     * @returns {number}
     */
    accAdd:function(arg1,arg2){
        var r1,r2,m;
        try{r1=arg1.toString().split(".")[1].length}catch(e){r1=0}
        try{r2=arg2.toString().split(".")[1].length}catch(e){r2=0}
        m=Math.pow(10,Math.max(r1,r2))
        var s=(arg1*m+arg2*m)/m;
        if(isNaN(s)){
            s=0;
        }
        return s;
    },

    addSplit : function () {
        $('body').on('click','.addSplit',function () {
            if($(".addSplit").length>9){
                alert("最大可拆分10次");
                return false;
            }
            $('.modal-body tbody').append('<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">分拆金额:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><input type="number" id="split_money1" name="split_money[]"></span><i title="增加" style="cursor:pointer;margin-right: 10px;" class="icon-plus alignMiddle addSplit"></i><i title="删除" class="icon-trash alignMiddle delSplit" style="cursor:pointer"></i></div></td></tr>');
        });
    },

    deleteSplit:function(){
        $('body').on('click','.delSplit',function () {
            $(this).closest("tr").remove();
        });
    },

    showConfirmationBox : function(data){
        var aDeferred = jQuery.Deferred();
        var width='800px';
        if(typeof  data['width'] != "undefined"){
            width=data['width'];
        }
        var bootBoxModal = bootbox.confirm({message:data['message'],width:width, callback:function(result) {
                if(result){
                    aDeferred.resolve();
                } else{
                    aDeferred.reject();
                }
            }, buttons: { cancel: {
                    label: '取消',
                    className: 'btn'
                },
                confirm: {
                    label: '去确认',
                    className: 'btn-success'
                }
            }});
        bootBoxModal.on('hidden',function(e){
            if(jQuery('#globalmodal').length > 0) {
                jQuery('body').addClass('modal-open');
            }
        })
        return aDeferred.promise();
    },

    setKeyUp:function(){
        //保留两位小数
        var thisInstance = this;
        $('body').on('change keyup',"input[name*='split_money']",function () {
            thisInstance.formatNumber($(this));
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

    selectStayPayMent:function(){
        $("select[name='staypaymentjine[]']").change(function () {
            var thisJquery=$(this);
            if($(this).val()){
                var postData = {
                    "module": 'Matchreceivements',
                    "action": "BasicAjax",
                    "record": $(this).val(),
                    'mode': 'getServiceInfoByStayId'
                }
                AppConnector.request(postData).then(
                    // 请求成功
                    function(data){
                        var html='<option value="">请选择合同</option>';
                        if(data.result){
                            html+='<option value="'+data.result.contractid+'" data-staypaymentid="'+data.result.staypaymentid+'" data-module="'+data.result.modulename+'" data-realoperate="'+data.result.realoperate+'">'+data.result.contract_no+'</option>'
                        }
                        $(thisJquery).parent().parent().find("select[name='contractid[]']").html(html);
                        $(thisJquery).parent().parent().find("select[name='contractid[]']").trigger('liszt:updated');
                        $(thisJquery).parent().parent().find("select[name='contractid[]']").val(data.result.contractid);
                        $(thisJquery).parent().parent().find("select[name='contractid[]']").trigger('change');
                        $(thisJquery).parent().parent().find("select[name='contractid[]']").trigger('liszt:updated');

                    },
                    function(error,err){

                    }
                );
            }
        });
    },

    registerEvents : function(){
        this._super();
        this.click_button();
        this.ActiveClick();
        this.addSplit();
        this.deleteSplit();
        this.setKeyUp();
        this.selectStayPayMent();
    }

});
