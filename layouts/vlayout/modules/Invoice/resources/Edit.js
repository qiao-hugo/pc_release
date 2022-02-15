/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("Invoice_Edit_Js",{},{
	
	/**
	 * Function which will register event for Reference Fields Selection
	 */
	ckeckedflagv:0,
	registerReferenceSelectionEvent : function(container) {
		//wangbin 2015-1-20 发票新增
		//this._super(container);
		var thisInstance = this;
		
		jQuery('input[name="contractid"]', container).on(Vtiger_Edit_Js.referenceSelectionEvent, function(e, data){
			//thisInstance.referenceSelectionEventHandler(data, container);
			//alert(data['record'])
			//thisInstance.loadWidgetProduct($('.widgetContainer_0'),data['record']);
			//thisInstance.loadWidgetAccount(data['record']);
			//thisInstance.loadReceivepay(data['record']);
            ////steel 2015-4-30调用回款信息
            thisInstance.loadReceivepaynew(data['record']);
            ////steel 2015-4-30 调用回款信息
		});
		//加载开票信息
	        jQuery('input[name="account_id"]').on(Vtiger_Edit_Js.referenceSelectionEvent, function (e,data){accountlist(data['source_module']);});
	        function accountlist(sourcemodule){
	            if(sourcemodule=='Accounts'){
	                var Accountid=$('input[name="account_id"]').val();
	                if(Accountid>0){
	                    thisInstance.loadWidgetNote();
	                }
	            }
	        }
	},

	/**
	 * Function to get popup params
	 */
	getPopUpParams123 : function(container) {
		var params = this._super(container);
        var sourceFieldElement = jQuery('input[class="sourceField"]',container);

		if(sourceFieldElement.attr('name') == 'contact_id') {
			var form = this.getForm();
			var parentIdElement  = form.find('[name="account_id"]');
			if(parentIdElement.length > 0 && parentIdElement.val().length > 0) {
				var closestContainer = parentIdElement.closest('td');
				params['related_parent_id'] = parentIdElement.val();
				params['related_parent_module'] = closestContainer.find('[name="popupReferenceModule"]').val();
			}
        }
        return params;
    },

	/**
	 * Function to search module names
	 */
	searchModuleNames : function(params) {
		var aDeferred = jQuery.Deferred();

		if(typeof params.module == 'undefined') {
			params.module = app.getModuleName();
		}
		if(typeof params.action == 'undefined') {
			params.action = 'BasicAjax';
		}

		if (params.search_module == 'Contacts') {
			var form = this.getForm();
			var parentIdElement  = form.find('[name="account_id"]');
			if(parentIdElement.length > 0 && parentIdElement.val().length > 0) {
				var closestContainer = parentIdElement.closest('td');
				params.parent_id = parentIdElement.val();
				params.parent_module = closestContainer.find('[name="popupReferenceModule"]').val();
			}
		}
		AppConnector.request(params).then(
			function(data){
				aDeferred.resolve(data);
			},
			function(error){
				aDeferred.reject();
			}
		)
		return aDeferred.promise();
	},
	
	//编辑产品加载
	loadWidgetProduct : function(widgetContainer,id) {
		var thisInstance = this;
		var contentHeader = jQuery('.widget_header',widgetContainer);
		var contentContainer = jQuery('.widget_contents',widgetContainer);
		
		var urlParams = 'module=ServiceContracts&view=ListAjax';
		
		var params = {
			'type' : 'GET',
			'dataType': 'html',
			'data' : urlParams+'&mode=edit&record='+id //module=ServiceContracts&view=ListAjax&mode=edit&record=615
		};
		contentContainer.progressIndicator({});
		AppConnector.request(params).then(
			function(data){
				var info=eval("("+data+")");
				if(info.success){
					//$(data.products).each(function(index) {$.each(data.products, function (n, value) {});
					var html='<table class="table table-bordered listViewEntriesTable"><thead><tr class="listViewHeaders"><th class="narrow">产品名称&nbsp;&nbsp; </th><th class="narrow">市场价&nbsp;&nbsp;</th></tr></thead><tbody>';
					var trhtml='';
					var upload=true;
					var total=0;
					for(var i in info.products){
						//wangbin 2015-1-7 产品信息金额添加单位
						var RMB ='';
						if(info.products[i]['currency_id']==1){
							RMB='¥';
						}
						trhtml+='<tr class="'+info.products[i]['productcategory']+'"><td>'+info.products[i]['productname']+'</td><td>'+RMB+info.products[i]['unit_price']+'</td></tr>';
						//wangbin 2015年1月12日 添加总额计算
						total += parseInt(info.products[i]['unit_price']);
					}
					$('input[name=hdnGrandTotal]').val(total);
					contentContainer.html(html+trhtml+'</tbody></table>');						
					$('#Invoice_editView_fieldName_customerno').val(info.customerno);
				}
				
			},
			 function(){
			}
		);
	},
	//编辑公司名称加载   wangbin 2015-1-13 
	loadWidgetAccount : function(id){
		var params = {
			'module' : 'ServiceContracts', //ServiceContracts
			'action' : 'BasicAjax', 
			'record' : id	
		};
		AppConnector.request(params).then(
				function(data){
					//console.log(data);
					if(data.success == true){
						var json=data.result;
						$('input[name=account_id_display]').val(json.accountname);
						$('input[name=account_id]').val(json.id);
						$('select[name=assigned_user_id]').val(json.userid);
						$('input[name=salescommission]').val(json.total);
						$('input[name=customerno]').val(json.customerno);
					};
				});
	},
	//编辑尾款加载    wangbin 2015-1-13 
	loadReceivepay : function(id){
		var params={};
		params['record'] =id ;                  
		params['module'] = 'ServiceContracts',
		params['action'] = 'BasicAjax',
		params['mode'] = 'receivepay';
		AppConnector.request(params).then(
		function(data){
			if(data.success == true){
				//console.log(data.result);
				var str1='<table class="table table-bordered listViewEntriesTable"><thead><tr class="listViewHeaders"><th class="narrow">名称</th><th class="narrow">金额&nbsp;&nbsp;</th><th class="narrow">还款时间&nbsp;&nbsp;</th><th class="narrow">计划还款时间&nbsp;&nbsp;</th></tr></thead><tbody>';
				var str2="";

				for(var i in data.result){
                     str2 +='<tr class="'+"huankuan"+'"><td>'+data.result[i]['relmodule']+'</td><td>'+data.result[i]['unit_price']+'</td><td>'+data.result[i]['reality_date']+'</td><td>'+data.result[i]['checktime']+'</td></tr>';
				}
				var str3 = '</tbody></table>';
				str4 = str1+str2+str3;
				$('.widget_content').html(str4);
			}
		})
	},

    
    //公司名称与购方企业名称值写入
    accountiddisplaychange : function(){
        $('table').on('click','.setcompanyname',function(){
            var source=$('input[name="'+$(this).data('name')+'"]');

            if(source.val()!=''){

                $('input[name="businessnamesone"]').val(source.val());
                $('input[name="businessnames"]').val(source.val());
                $('input[name="businessnamesnegative"]').val(source.val());
		$('input[name*="businessnamesextend"]').val(source.val());
            }
        });
    },
    //乘法运算解决Js相乘的问题
    accMul:function(arg1,arg2){
        var m=0,s1=arg1.toString(),s2=arg2.toString();
        try{m+=s1.split(".")[1].length}catch(e){}
        try{m+=s2.split(".")[1].length}catch(e){}
        return Number(s1.replace(".",""))*Number(s2.replace(".",""))/Math.pow(10,m)
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
    //计算价税合计amountofmoney
    amountofmoneychange : function(){
        var thisInstance=this;

        $('input[name="totalandtax"]').on('blur',function(){

			if(isNaN($(this).val().replace(/,/g,''))){
				$(this).val('');
				return;
			}else if($(this).attr('name')=='totalandtax' && Number($(this).val())>Number($('input[name="taxtotal"]').val().replace(/,/g,'')) ){
				//$(this).parent().before('<div class="alert alert-error"> <button type="button" class="close" data-dismiss="alert">×</button><strong>金额与开票金额不一致!</strong></div>');
				$(this).attr('data-title','注意');$(this).attr('data-content','金额与开票金额不一致');$(this).popover('show');
				$('input[name*="totalandtaxextend"]').each(function(k,v){
		                    $(v).trigger('focus');//模拟一下让他重新计算
		                    $(v).trigger('blur');//直接失焦不触发
		                });
				//var  params = {text : app.vtranslate(),title : app.vtranslate('请注意金额与开票金额一致！')};// Vtiger_Helper_Js.showPnotify(params);
				//setTimeout("$('.alert').fadeOut('8000')",3000);
			}else{
				$(this).popover('destroy');
			}
            if($('input[name="totalandtax"]').val()>0){
                if($('select[name="taxrate"]').find('option:selected').val()!=''){
                    var taxrate=$('select[name="taxrate"]').find('option:selected').val()=='6%'?1.06:1.17;
                    var amountofmoneyval=thisInstance.accDiv($('input[name="totalandtax"]').val(),taxrate);
                    $('input[name="amountofmoney"]').val(amountofmoneyval.toFixed(2))
                    var taxrate=$('select[name="taxrate"]').find('option:selected').val()=='6%'?0.06:0.17;
                    $('input[name="tax"]').val(thisInstance.accMul(amountofmoneyval,taxrate).toFixed(2));
                }
            }
        });
        $('input[name="amountofmoney"]').on('blur',function(){
             if($(this).val()>0){

                if($('select[name="taxrate"]').find('option:selected').val()!=''){
                    var taxrate=$('select[name="taxrate"]').find('option:selected').val()=='6%'?0.06:0.17;
                    //var valuetax=$('input[name="tax"]').val()*taxrate;
                    $('input[name="tax"]').val(thisInstance.accMul($('input[name="amountofmoney"]').val(),taxrate).toFixed(2));
                }
                $('input[name="totalandtax"]').val(thisInstance.accAdd($('input[name="tax"]').val(),$('input[name="amountofmoney"]').val()).toFixed(2));
            }
        });
        $('select[name="taxrate"]').on('change',function(){
            if($(this).val()=='6%' || $(this).val()=='17%'){
                if($('select[name="taxrate"]').find('option:selected').val()!=''){
                    var taxrate=$('select[name="taxrate"]').find('option:selected').val()=='6%'?0.06:0.17;
                    $('input[name="tax"]').val(thisInstance.accMul($('input[name="amountofmoney"]').val(),taxrate).toFixed(2));
                }
                $('input[name="totalandtax"]').val(thisInstance.accAdd($('input[name="tax"]').val(),$('input[name="amountofmoney"]').val()).toFixed(2));
            }
        });
        $('input[name="totalandtax"]').on('keyup blur change',function(){
            if($(this).val()>0 && $(this).attr('name')=='totalandtax'){
                if($('select[name="taxrate"]').find('option:selected').val()!=''){
                    var taxrate=$('select[name="taxrate"]').find('option:selected').val()=='6%'?1.06:1.17;
                    var amountofmoneyval=thisInstance.accDiv($('input[name="totalandtax"]').val(),taxrate);
                    $('input[name="amountofmoney"]').val(amountofmoneyval.toFixed(2))
                    var taxrate=$('select[name="taxrate"]').find('option:selected').val()=='6%'?0.06:0.17;
                    $('input[name="tax"]').val(thisInstance.accMul(amountofmoneyval,taxrate).toFixed(2));
                }
            }
        });
    },
    //计算价税合计
    taxchange : function(){
        var thisInstance=this;
        $('table').on('blur','input[name="tax"]',function(){
            if($(this).val()>0){
                $('input[name="totalandtax"]').val(thisInstance.accAdd($('input[name="tax"]').val(),$('input[name="amountofmoney"]').val()).toFixed(2));
            }
        });

    },
    //计算价税合计amountofmoney负数
    amountofmoneychangen : function(){
        var thisInstance=this;
        $('input[name="amountofmoneynegative"],input[name="taxnegative"],input[name="totalandtaxnegative"]').on("keyup",function(){
            thisInstance.formatNumbern($(this));
            var arr=$(this).val().split('.');//只有一个小数点
            if(arr.length>2){
                if(arr[1]==''){
                    $(this).val(arr[0]);
                }else{
                    $(this).val(arr[0]+'.'+arr[1]);
                }
            }
            if($(this).val()>0){
                $(this).val(0.00);
            }else if($(this).val()<0){
                var arr1=$(this).val().split('-');
                //console.log(arr1);
                if(arr1.length>2){
                    $(this).val(arr[0]+'-'+arr[1]);
                }
            }

        }).on("blur",function(){  //CTR+V事件处理
            thisInstance.formatNumbern($(this));

            var arr=$(this).val().split('.');//只有一个小数点
            if(arr.length>2){
                if(arr[1]==''){
                    $(this).val(arr[0]);
                }else{
                    $(this).val(arr[0]+'.'+arr[1]);
                }
            }else if(arr.length==2){
                //小数点后没有数字的则将小数点删除
                if(arr[1]==''){
                    $(this).val(arr[0]);
                }
            }
        });

        $('input[name="totalandtaxnegative"]').on('blur',function(){
            if($('input[name="totalandtaxnegative"]').val()<=0 && $(this).attr('name')=='totalandtaxnegative'){
                if($('select[name="taxratenegative"]').find('option:selected').val()!=''){
                    var taxrate=$('select[name="taxratenegative"]').find('option:selected').val()=='6%'?1.06:1.17;
                    var amountofmoneyval=thisInstance.accDiv($('input[name="totalandtaxnegative"]').val(),taxrate);
                    $('input[name="amountofmoneynegative"]').val(amountofmoneyval.toFixed(2))
                    var taxrate=$('select[name="taxratenegative"]').find('option:selected').val()=='6%'?0.06:0.17;
                    $('input[name="taxnegative"]').val(thisInstance.accMul(amountofmoneyval,taxrate).toFixed(2));
                }
            }
        });
        $('input[name="amountofmoneynegative"]').on('blur',function(){
            if($('input[name="amountofmoneynegative"]').val()<=0 && $(this).attr('name')=='amountofmoneynegative'){

                if($('select[name="taxratenegative"]').find('option:selected').val()!=''){
                    var taxrate=$('select[name="taxratenegative"]').find('option:selected').val()=='6%'?0.06:0.17;
                    //var valuetax=$('input[name="tax"]').val()*taxrate;
                    $('input[name="taxnegative"]').val(thisInstance.accMul($('input[name="amountofmoneynegative"]').val(),taxrate).toFixed(2));
                }
                $('input[name="totalandtaxnegative"]').val(thisInstance.accAdd($('input[name="taxnegative"]').val(),$('input[name="amountofmoneynegative"]').val()).toFixed(2));
            }
        });
        $('select[name="taxratenegative"]').on('change',function(){
            if($('input[name="amountofmoneynegative"]').val()<=0 && $(this).attr('name')=='amountofmoneynegative'){
                if($('select[name="taxratenegative"]').find('option:selected').val()!=''){
                    var taxrate=$('select[name="taxratenegative"]').find('option:selected').val()=='6%'?0.06:0.17;
                    $('input[name="taxnegative"]').val(thisInstance.accMul($('input[name="amountofmoneynegative"]').val(),taxrate).toFixed(2));
                }
                $('input[name="totalandtaxnegative"]').val(thisInstance.accAdd($('input[name="taxnegative"]').val(),$('input[name="amountofmoneynegative"]').val()).toFixed(2));

            }
        });
        $('input[name="totalandtaxnegative"]').on('keyup blur change',function(){
            if($('input[name="totalandtaxnegative"]').val()<=0 && $(this).attr('name')=='totalandtaxnegative'){
                if($('select[name="taxratenegative"]').find('option:selected').val()!=''){
                    var taxrate=$('select[name="taxratenegative"]').find('option:selected').val()=='6%'?1.06:1.17;
                    var amountofmoneyval=thisInstance.accDiv($('input[name="totalandtaxnegative"]').val(),taxrate);
                    $('input[name="amountofmoneynegative"]').val(amountofmoneyval.toFixed(2))
                    var taxrate=$('select[name="taxratenegative"]').find('option:selected').val()=='6%'?0.06:0.17;
                    $('input[name="taxnegative"]').val(thisInstance.accMul(amountofmoneyval,taxrate).toFixed(2));
                }
            }
        });
    },
    //计算价税合计负数
    taxchangen : function(){
        var thisInstance=this;
        $('table').on('blur','input[name="taxnegative"]',function(){
            if($(this).val()<0){
                $('input[name="totalandtaxnegative"]').val(thisInstance.accAdd($('input[name="taxnegative"]').val(),$('input[name="amountofmoneynegative"]').val()).toFixed(2));
            }
        });

    },
    //初始化购方企业名称
    businessnamesonechange : function(){
        //$('input[name="businessnamesone"],').attr('readonly',true);
        $('input[name="account_id_display"]').next().after('<button type="button" class="btn btn-info setcompanyname" data-name="account_id_display">设为实际开票抬头</button>');
        //$('#Invoice_editView_fieldName_companyname').after('<button id="getcompanyname" type="button" class="btn btn-info">设为购方企业名称</button>');
        $('input[name="companyname"]').parent().css('whiteSpace','nowrap');
        //新增/编辑时根据条件加载disabled
        if($('select[name="taxtype"]').val()==''||$('select[name="taxtype"]').val()=='generalinvoice'){
            $('.tableadv :input').attr('disabled','disabled');
        }
        $('input[name="billingcontent"]').attr("readonly","readonly");
	$('input[name="taxpayers_no"]').attr("readonly","readonly");
        $('input[name="registeraddress"]').attr("readonly","readonly");
        $('input[name="depositbank"]').attr("readonly","readonly");
        $('input[name="telephone"]').attr("readonly","readonly");
        $('input[name="accountnumber"]').attr("readonly","readonly");
        $('input[name="isformtable"]').attr("readonly","readonly");
        $('input[name="isformtable"]').attr("readonly","readonly");
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

    inputnumberchange : function(){
        var thisInstance=this;
        $('input[name="tax"],input[name="totalandtax"],input[name="amountofmoney"],input[name="taxtotal"]').on("keyup",function(){
            thisInstance.formatNumber($(this));
            var arr=$(this).val().split('.');//只有一个小数点
            if(arr.length>2){
                if(arr[1]==''){
                    $(this).val(arr[0]);
                }else{
                    $(this).val(arr[0]+'.'+arr[1]);
                }
            }
        }).on("blur",function(){  //CTR+V事件处理
            thisInstance.formatNumber($(this));
            //console.log($(this).val())
            var arr=$(this).val().split('.');//只有一个小数点
            if(arr.length>2){
                if(arr[1]==''){
                    $(this).val(arr[0]);
                }else{
                    $(this).val(arr[0]+'.'+arr[1]);
                }
            }else if(arr.length==2){
                //小数点后没有数字的则将小数点删除
                if(arr[1]==''){
                    $(this).val(arr[0]);
                }
            }
        }); //CSS设置输入法不可用
        var instance=this;
        $('input[name="taxtotal"]').on('blur',function(){
            instance.taxtotalandcontact();
        });
    },
    formatNumbern:function(_this){
        _this.val(_this.val().replace(/,/g,''));//去掉,
        _this.val(_this.val().replace(/[^0-9.\-]/g,''));//只能输入数字小数点
        _this.val(_this.val().replace(/^0\d{1,}/g,''));//不能以0打头的整数
        _this.val(_this.val().replace(/^\./g,''));//不能以小数点打头
        _this.val(_this.val().replace(/[\d]{13,}/g,''));//超出长度全部清空
        _this.val(_this.val().replace(/\.\d{3,}$/g,''));//小数点后两位如果超过两位则将小数后的所有数清空包含小数点
        _this.val(_this.val().replace(/(\d)\-*$/g,"$1"));//不能以
        _this.val(_this.val().replace(/^\-{2,}/g,"-"));//不能以
    },

    //增值税发票显示与否
    taxtypechange : function(){
	var thisInstance=this;
        //$('.fileUploadContainer').parent().parent().css('whiteSpace','nowrap');
        $('select[name="taxtype"]').on('change',function(){
            var taxname = $(this).val();
            if(taxname =='generalinvoice'){
                //清空增值发票信息
                $('.tableadv').addClass('hide');
                $("#Invoice_editView_fieldName_taxpayers_no,#Invoice_editView_fieldName_registeraddress,#Invoice_editView_fieldName_telephone,#Invoice_editView_fieldName_depositbank,#Invoice_editView_fieldName_accountnumber").val('');
		$('input[name="billingid"]').val('');
                $('#Invoice_editView_fieldName_isformtable').attr('checked',false);
                //$('input[name="file"]').val('');
                $('.tableadv :input').attr('disabled','disabled');
            }else if(taxname =='specialinvoice'){
                $('.tableadv').removeClass('hide');
                $('.tableadv').find('form').css({width:'48px'});
                $('.tableadv :input').removeAttr('disabled');
		thisInstance.loadWidgetNote();
                //$('.fileUploadContainer').parent().parent().css('whiteSpace','nowrap');
                //$("#Invoice_editView_fieldName_taxpayers_no,#Invoice_editView_fieldName_registeraddress,#Invoice_editView_fieldName_telephone,#Invoice_editView_fieldName_depositbank,#Invoice_editView_fieldName_accountnumber").removeAttr('readonly');
            }
        })
    },
    //加法运算,解决JS浮点数问题
    accAdd:function (arg1,arg2){
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
    //回款与开票金额之间的联系
    receivedpaymentsandmoneysum : function(){
        var thisInstance=this;
        $('input[name="receivedid[]"]').live('click',function(){
            var sum=0;
            $(this+':checked:checked').each(function(i){
                sum=thisInstance.accAdd(sum,$(this).parent().nextAll().eq(4).text());//求合合
            });
            //不做运算
            //$('input[name="taxtotal"]').val(sum.toFixed(2));
            thisInstance.taxtotalandcontact();
        })
    },
    //开票金额与合同金额比较只做提示不做其它处理
    taxtotalandcontact : function() {
        var taxtotalvalue =parseFloat($('input[name="taxtotal"]').val());
        var recordId=$('input[name="contractid"]').val();
	var recordinovice=$('input[name="record"]').val();
        if(recordId>0 && taxtotalvalue>0){

            var postData = {
                "module": "Invoice",
                "action": "Invoicehandle",
                "record": recordId,
                "recordinovice":recordinovice,
                "type":"comtaxt"
            }
            $('input[name="taxtotal"]').popover('destroy');
            AppConnector.request(postData).then(
                function(data){
                    if(data.success==true){
                        var values=taxtotalvalue+parseFloat(data.result.taxtotal)-parseFloat(data.result.tovoid)-parseFloat(data.result.redinvoice.replace('-',''));
                        var stringvalues=data.result.taxtotal==0?'':'<br>已开票金额:　<font size="4" style="color:#FF0000">'+data.result.taxtotal+'</font>';
                            stringvalues+=data.result.tovoid==0?'':'<br>作废金额:　<font size="4" style="color:#FF0000">'+data.result.tovoid+'</font>';
                            stringvalues+=data.result.redinvoice==0?'':'<br>红冲金额:　<font size="4" style="color:#FF0000">'+data.result.redinvoice+'</font>';
                        if(data.result.total>0 && data.result.total<values){
                            //提示信息
			                $('input[name="taxtotal"]').attr('data-title','注意:');
                            $('input[name="taxtotal"]').attr('data-content','合同总金额:　<font size="4" style="color:#0000FF">'+data.result.total+'</font>'+stringvalues+'<br>开票总金额:　<font size="4" style="color:#FF0000">'+values+'</font><br><font size="4" style="color:#FF0000">开票总金额大于合同金额</font>');$('input[name="taxtotal"]').popover('show');
                            //$('input[name="taxtotal"]').parent().after('<span class="totalvalue" style="color:red">合同金额:<font size="4" style="color:#000000">'+data.result.total+'</font>,  开票金额大于合同金额');
                        }
                    }

                }
            );
        }
    },
    //合同关联显示回款
    loadReceivepaynew:function(id){
        var thisInstance=this;
        var recordid=$('input[name="record"]').val();
        var params={};
        params["module"] = "Invoice"; //ServiceContracts
        params["action"] = "InvoiceRelates";
        params["record"]= id;
        $('.invoicenolist').html('');
        $('.invoicelist').html('');
        AppConnector.request(params).then(
            function(data){
                if(data.success == true){
                    $('input[name="account_id_display"]').val(data.result.accountname);
                    $('input[name="billingcontent"]').val(data.result.billcontent);
                    $('input[name=account_id]').val(data.result.id);
                    $('.norecevied').remove();
                    if(data.result.resultlist=='yes'){
                        var json = data.result;
                        /*
                        if(json.markl=='yes') {
                            var str = '<tr><td>勾选</td><td>所属合同</td><td>货币类型</td><td>本位币</td><td>汇率</td><td>回款金额</td><td>回款时间</td><td>创建人</td><td>汇款抬头</td><td>开据状态</td><td>发票号码</td></tr>';
                            $.each(json.receivepaylist, function (i, val) {

                                if (val['invoice_no'] == '--' || recordid==val['invoiceid']) {
                                    var modulestaus=(val['modulestatus']=='--' || val['modulestatus']=='a_normal')?'<span class="label label-success">正常</span>':val['modulestatus']=='c_complete'?'<span class="label label-warning">已完成</span>':val['modulestatus']=='b_check'?'<span class="label label-warning">审核中</span>':'<span class="label label-warning">已开据</span>';

                                    str += "<tr><td><input type=\"checkbox\" name=\"receivedid[]\" value=\"" + val['receivedid'] + '" '+(val['modulestatus']=='b_check'?'checked class="hide"':'')+'></td><td>' + val['contract_no'] + "</td><td>" + val['currencytype'] + "</td><td>" + val['standardmoney'] + "</td><td>" + val['exchangerate'] + "</td><td>" + val['unit_price'] + "</td><td>" + val['reality_date'] + "</td><td>" + val['createid'] + '</td><td><input name="companytwo" value="'+val['paytitle']+'" type="hidden" disabled><button id="getcompanyname" type="button" class="btn btn-info setcompanyname" data-name="companytwo" title="设置购方企业名称">'+val['paytitle'] +"</button></td><td>"+modulestaus+"</td><td>" + val['invoice_no'] + "</td></tr>";
                                } else {
                                    var modulestaus=( val['modulestatus']=='a_normal')?'<span class="label label-warning">已开据</span>':val['modulestatus']=='c_complete'?'<span class="label label-c_complete">已完成</span>':val['modulestatus']=='b_check'?'<span class="label label-b_check">审核中</span>':'<span class="label label-warning">已开据</span>';

                                    str += "<tr><td><input type=\"checkbox\" disabled class=\"hide\"></td><td>"+ val['contract_no'] +"</td><td>" + val['currencytype'] + "</td><td>" + val['standardmoney'] + "</td><td>" + val['exchangerate'] + "</td><td>" + val['unit_price'] + "</td><td>" + val['reality_date'] + "</td><td>" + val['createid'] + "</td><td>" + val['paytitle'] + "</td><td>"+modulestaus+"</td><td>" + val['invoice_no'] + "</td></tr>";
                                }
                            });
                            $('.invoicelistdisplay').removeClass('hide');
                            $('.invoicelist').html(str);
                        }else{
                            $('.invoicelistdisplay').removeClass('hide');
                            $('.invoicelistdisplay').children('thead').children('tr').children('th').append('<span style="color:red;margin-left:20px;" class="norecevied">该合同没有回款,请选择有合同无回款发票流程</span>');
                        }
                        */
                        if(json.marknl=='yes'){
                            var str = '<tr><td>发票编号</td><td>合同编号</td><td>包含回款</td><td>创建时间</td><td>开票日期</td><td>开票抬头</td><td>开票金额</td><td>账号</td></tr>';
                            $.each(json.receivepaynolist, function (i, val) {
                                str += "<tr><td>" + val['invoice_no'] + "</td><td>" + val['contract_no'] + "</td><td>" + val['unit_price'] + "</td><td>" + val['createdtime'] + "</td><td>" + val['billingtime'] + "</td><td>" + val['companyname'] + "</td><td>" + val['taxtotal'] + "</td><td>" + val['accountnumber'] + "</td></tr>";
                        });
                            $('.invoicenolistdisplay').removeClass('hide');
                            $('.invoicenolist').html(str);
                        }
                    }
                    thisInstance.taxtotalandcontact();
		            thisInstance.loadWidgetNote();

                };

        })

    },
    request:function(paras){
        var url = location.href;
        var paraString = url.substring(url.indexOf("?")+1,url.length).split("&");
        var paraObj = {}
        for (i=0; j=paraString[i]; i++){
            paraObj[j.substring(0,j.indexOf("=")).toLowerCase()] = j.substring(j.indexOf("=")+1,j.length);
        }
        var returnValue = paraObj[paras.toLowerCase()];
        if(typeof(returnValue)=="undefined"){
            return "faile";
        }else{
            return returnValue;
        }
    },
    //steel提交表单事件验证
    registerRecordPreSaveEvent : function(form) {
        var thisInstance = this;
        var editViewForm = this.getForm();

        editViewForm.on(Vtiger_Edit_Js.recordPreSave,function(e,data){
            var len = $('input[name="receivedid[]"]:checked:checked').length;
            thisInstance.submitcheck();
            if(thisInstance.ckeckedflagv==1){
                e.preventDefault(); //阻止提交事件先注释
            }
            return;
            if(len>0 && thisInstance.request('Negative')!='NegativeEdit'){
                var sum=0;
                $(this+':checked:checked').each(function(i){
                    sum=thisInstance.accAdd(sum,$(this).parent().nextAll().eq(4).text());//求合
                });
                if(sum!=$('input[name="taxtotal"]').val()){
                    var  params = {text : app.vtranslate(),
                                title : app.vtranslate('若勾选回款,请保证回款金额与开票金额一致')};
                    $('input[name="taxtotal"]').focus();
                    Vtiger_Helper_Js.showPnotify(params);
                    e.preventDefault(); //阻止提交事件先注释
                }
            }
        });
    },
    addMinus:function(){
        $('input[name="amountofmoneynegative"]').siblings().append('<span style="color:#FF0000;font-size:16px;">　-</span>');
        $('input[name="taxnegative"]').siblings().append('<span style="color:#FF0000;font-size:16px;">　-</span>');
        $('input[name="totalandtaxnegative"]').siblings().append('<span style="color:#FF0000;font-size:16px;">　-</span>');
    },
    loadWidgetNote : function(){
        var accountid=$('input[name="account_id"]').val();
        var taxtype=$('select[name="taxtype"]').val();
        if(accountid<1||taxtype!='specialinvoice'){
            return;
        }
        var params={};
        params['accountid'] =accountid ;                  //公司的id
        params['module'] = 'Invoice';
        params['action'] = 'SelectAjax';
        params['mode'] = 'autofillBilling';
        AppConnector.request(params).then(
            function(data){
                if(data.success==true){
                    $("select#contactorselect").remove();
                    if(data.result.length!==0){
                        if(data.result.length==1){
                            $('input[name="taxpayers_no"]').val(data.result[0].taxpayers_no);
                            $('input[name="registeraddress"]').val(data.result[0].registeraddress);
                            $('input[name="depositbank"]').val(data.result[0].depositbank);
                            $('input[name="telephone"]').val(data.result[0].telephone);
                            $('input[name="accountnumber"]').val(data.result[0].accountnumber);
                            $('input[name="businessnamesone"]').val(data.result[0].businessnamesone);
                            $('input[name="businessnames"]').val(data.result[0].businessnamesone);
                            $('input[name="businessnamesnegative"]').val(data.result[0].businessnamesone);
			                $('input[name="billingid"]').val(data.result[0].billingid);
                            $('input[name="isformtable"]').val(data.result[0].isformtable);
                            $('input[name="isformtable"]').attr('checked','checked');
                        }else{
                            $('input[name="taxpayers_no"]').val(data.result[0].taxpayers_no);
                            $('input[name="registeraddress"]').val(data.result[0].registeraddress);
                            $('input[name="depositbank"]').val(data.result[0].depositbank);
                            $('input[name="telephone"]').val(data.result[0].telephone);
                            $('input[name="accountnumber"]').val(data.result[0].accountnumber);
                            $('input[name="businessnamesone"]').val(data.result[0].businessnamesone);
                            $('input[name="businessnames"]').val(data.result[0].businessnamesone);
                            $('input[name="businessnamesnegative"]').val(data.result[0].businessnamesone);
			                $('input[name="billingid"]').val(data.result[0].billingid);
                            $('input[name="isformtable"]').val(data.result[0].isformtable);
                            $('input[name="isformtable"]').attr('checked','checked');
                            var str="";
                            $.each(data.result,function(n,value){
                                str += "<option value="+JSON.stringify(data.result[n])+'>'+value.businessnamesone+"</option>";
                            })
                            newstr = "<select id='contactorselect'>"+str+"</select>"
                            $('#Invoice_editView_fieldName_taxpayers_no').closest('span').append(newstr);
                            }
		    }else{
                        $('input[name="taxpayers_no"]').val('');
                        $('input[name="registeraddress"]').val('');
                        $('input[name="depositbank"]').val('');
                        $('input[name="telephone"]').val('');
                        $('input[name="accountnumber"]').val('');
                        $('input[name="businessnamesnegative"]').val('');
                        $('input[name="billingid"]').val('');
                        $('input[name="isformtable"]').val(1);
                        $('input[name="isformtable"]').attr('checked','checked');
                    }
                }
            })
    },
    changebilling:function(){
        $(".tableadv").on('change','#contactorselect',function(){
            var jsontext=$(this).val();
            var objjson=JSON.parse(jsontext);
            $('input[name="taxpayers_no"]').val(objjson.taxpayers_no);
            $('input[name="registeraddress"]').val(objjson.registeraddress);
            $('input[name="depositbank"]').val(objjson.depositbank);
            $('input[name="telephone"]').val(objjson.telephone);
            $('input[name="accountnumber"]').val(objjson.accountnumber);
            $('input[name="businessnamesone"]').val(objjson.businessnamesone);
            $('input[name="businessnames"]').val(objjson.businessnamesone);
            $('input[name="businessnamesnegative"]').val(objjson.businessnamesone);
	    $('input[name="billingid"]').val(objjson.billingid);
            $('input[name="isformtable"]').val(objjson.isformtable);
            $('input[name="isformtable"]').attr('checked','checked');
        });
    },
//多发票的添加
    addinvoice:function(){
        $('#addfallinto').on('click',function(){
            var numd=$('.Duplicates').length+1;
            if(numd>100){return;}/*超过100个不允许添加*/
            var nowdnum=$('.Duplicates').last().data('num');
            if(nowdnum!=undefined){
                numd=nowdnum+1;
            }
            var extend=extendinvoice.replace(/\[\]|replaceyes/g,'['+numd+']');
            extend=extend.replace(/yesreplace/g,numd);
            $('.invoicelistdisplay').before(extend);
            $('.billingtimerextends').datetimepicker({
                format: "yyyy-mm-dd",
                language:  'zh-CN',
                autoclose: true,
                autoclose:1,
                todayHighlight:1,
                startView:2,
                minView:2,
                forceParse:0,
                pickerPosition: "bottom-left",
                showMeridian: 0
            });
 	    //更新
            $('input[name="businessnamesextend['+numd+']"]').val($('input[name="businessnamesone"]').val());
            $('input[name="commoditynameextend['+numd+']"]').val($('input[name="billingcontent"]').val());
            //$('input[name="commoditynameextend['+numd+']"]').val($('input[name*="commoditynameextend"]').first().val());
            $('.chzn-select').chosen();
        });
    },
    //多发票输入过滤
    inputnumberextend : function(){
        var thisInstance=this;
        $('#EditView').on("keyup",'input[name*="taxextend"],input[name*="amountofmoneyextend"],input[name*="totalandtaxextend"]',function(){
            thisInstance.formatNumber($(this));
            var arr=$(this).val().split('.');//只有一个小数点
            if(arr.length>2){
                if(arr[1]==''){
                    $(this).val(arr[0]);
                }else{
                    $(this).val(arr[0]+'.'+arr[1]);
                }
            }
        }).on("blur",'input[name*="taxextend"],input[name*="amountofmoneyextend"],input[name*="totalandtaxextend"]',function(){  //CTR+V事件处理
            thisInstance.formatNumber($(this));
            //console.log($(this).val())
            var arr=$(this).val().split('.');//只有一个小数点
            if(arr.length>2){
                if(arr[1]==''){
                    $(this).val(arr[0]);
                }else{
                    $(this).val(arr[0]+'.'+arr[1]);
                }
            }else if(arr.length==2){
                //小数点后没有数字的则将小数点删除
                if(arr[1]==''){
                    $(this).val(arr[0]);
                }
            }
        }); //CSS设置输入法不可用
        /*var instance=this;
        $('input[name="taxtotal"]').on('blur',function(){
            instance.taxtotalandcontact();
        });*/
    },
    //删除多发票
    deleteinvoice:function(){
        $('#EditView').on('click','.delbuttonextend',function(){
            var newthis=$(this);
            var message='确定要删除吗？';
            var msg={
                'message':message
            };
            var dataid=$(this).data('id');
            //flagv=2;
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                //alert($('#recordId').val());return;
                $('input[name="totalandtaxextend['+dataid+']"]').popover('destroy');
                $('input[name="invoice_noextend['+dataid+']"]').popover('destroy');
                newthis.parents('.Duplicates').remove();
                $('input[name*="totalandtaxextend"]').each(function(k,v){
                    $(v).trigger('focus');//模拟一下让他从新计算
                    $(v).trigger('blur');
                });
                $('input[name*="invoice_noextend"]').each(function(k,v){
                    var ndataid=$(v).data('id');
                    $('input[name="invoice_noextend['+ndataid+']"]').popover('destroy');
                    $(v).trigger('focus');//模拟一下让他从新计算
                    $(v).trigger('blur');
                });
            },function(error, err) {});

        });

    },
    //多发票验证计算
    amountofmoneyextend : function(){
        var thisInstance=this;

        $('#EditView').on('blur','input[name*="totalandtaxextend"]',function(){
            var dataid=$(this).data('id');
            var totalandtaxextendsum=0
            $('input[name*="totalandtaxextend"]').each(function(k,v){
                totalandtaxextendsum+=Number($(v).val().replace(/,/g,''));
            });
            //totalandtaxextendsum+=Number($('input[name="totalandtax"]').val().replace(/,/g,''));
	    totalandtaxextendsum+=0;
            var taxtotal=Number($('input[name="taxtotal"]').val().replace(/,/g,''));
            if(isNaN($(this).val().replace(/,/g,''))){
                $(this).val('');
                return;
            }else if(totalandtaxextendsum!=taxtotal){
                //$(this).parent().before('<div class="alert alert-error"> <button type="button" class="close" data-dismiss="alert">×</button><strong>金额与开票金额不一致!</strong></div>');
                $(this).attr('data-title','注意:');$(this).attr('data-content','价税合计与开票金额不一致<br>开票金额:<font color="red" id="pclosed'+dataid+'">'+taxtotal+'</font><br>已开价税合计:<font color="red">'+totalandtaxextendsum+'</font>');$(this).popover('show');

                //var  params = {text : app.vtranslate(),title : app.vtranslate('请注意金额与开票金额一致！')};// Vtiger_Helper_Js.showPnotify(params);
                //setTimeout("$('.alert').fadeOut('8000')",3000);
            }else{
                $(this).popover('destroy');
            }
            if($(this).val()>0){
                if($('select[name="taxrateextend['+dataid+']"]').find('option:selected').val()!=''){
                    var taxrate=$('select[name="taxrateextend['+dataid+']"]').find('option:selected').val()=='6%'?1.06:1.17;
                    var amountofmoneyval=thisInstance.accDiv($('input[name="totalandtaxextend['+dataid+']"]').val(),taxrate);
                    $('input[name="amountofmoneyextend['+dataid+']"]').val(amountofmoneyval.toFixed(2))
                    var taxrate=$('select[name="taxrateextend['+dataid+']"]').find('option:selected').val()=='6%'?0.06:0.17;
                    $('input[name="taxextend['+dataid+']"]').val(thisInstance.accMul(amountofmoneyval,taxrate).toFixed(2));
                }
            }
        });
        $('#EditView').on('blur','input[name*="amountofmoneyextend"]',function(){
            var dataid=$(this).data('id');
            if($(this).val()>0){

                if($('select[name="taxrateextend['+dataid+']"]').find('option:selected').val()!=''){
                    var taxrate=$('select[name="taxrateextend['+dataid+']"]').find('option:selected').val()=='6%'?0.06:0.17;
                    //var valuetax=$('input[name="tax"]').val()*taxrate;
                    $('input[name="taxextend['+dataid+']"]').val(thisInstance.accMul($('input[name="amountofmoneyextend['+dataid+']"]').val(),taxrate).toFixed(2));
                }
                $('input[name="totalandtaxextend['+dataid+']"]').val(thisInstance.accAdd($('input[name="taxextend['+dataid+']"]').val(),$('input[name="amountofmoneyextend['+dataid+']"]').val()).toFixed(2));
            }
        });
        $('#EditView').on('change','select[name*="taxrateextend"]',function(){

            var dataid=$(this).data('id');
            if($(this).val()=='6%' || $(this).val()=='17%'){
                if($('select[name="taxrateextend['+dataid+']"]').find('option:selected').val()!=''){
                    //var taxrate=$('select[name="taxrateextend['+dataid+']"]').find('option:selected').val()=='6%'?0.06:0.17;
                    //$('input[name="taxextend['+dataid+']"]').val(thisInstance.accMul($('input[name="amountofmoneyextend['+dataid+']"]').val(),taxrate).toFixed(2));
                    var taxrate=$('select[name="taxrateextend['+dataid+']"]').find('option:selected').val()=='6%'?1.06:1.17;
                    var amountofmoneyval=thisInstance.accDiv($('input[name="totalandtaxextend['+dataid+']"]').val(),taxrate);
                    $('input[name="amountofmoneyextend['+dataid+']"]').val(amountofmoneyval.toFixed(2))
                    var taxrate=$('select[name="taxrateextend['+dataid+']"]').find('option:selected').val()=='6%'?0.06:0.17;
                    $('input[name="taxextend['+dataid+']"]').val(thisInstance.accMul(amountofmoneyval,taxrate).toFixed(2));
                }
                //$('input[name="totalandtaxextend['+dataid+']"]').val(thisInstance.accAdd($('input[name="taxextend['+dataid+']"]').val(),$('input[name="amountofmoneyextend['+dataid+']"]').val()).toFixed(2));

            }
        });
        $('#EditView').on('keyup blur change','input[name*="totalandtaxextend"]',function(){
            var dataid=$(this).data('id');
            if($(this).val()>0 && $(this).attr('name')=='totalandtaxextend['+dataid+']'){
                if($('select[name="taxrateextend['+dataid+']"]').find('option:selected').val()!=''){
                    var taxrate=$('select[name="taxrateextend['+dataid+']"]').find('option:selected').val()=='6%'?1.06:1.17;
                    var amountofmoneyval=thisInstance.accDiv($('input[name="totalandtaxextend['+dataid+']"]').val(),taxrate);
                    $('input[name="amountofmoneyextend['+dataid+']"]').val(amountofmoneyval.toFixed(2))
                    var taxrate=$('select[name="taxrateextend['+dataid+']"]').find('option:selected').val()=='6%'?0.06:0.17;
                    $('input[name="taxextend['+dataid+']"]').val(thisInstance.accMul(amountofmoneyval,taxrate).toFixed(2));
                }
            }
        });
        $('#EditView').on('blur','input[name*="taxextend"]',function(){
            var dataid=$(this).data('id');
            if($(this).val()>0){
                $('input[name="totalandtaxextend['+dataid+']"]').val(thisInstance.accAdd($('input[name="taxextend['+dataid+']"]').val(),$('input[name="amountofmoneyextend['+dataid+']"]').val()).toFixed(2));
            }
        });
    },
    //发票代码和发票号码重复验证
    checkinvoiceextend:function(){
	var instancethis=this;
        $('#EditView').on('blur','input[name*="invoicecodeextend"],input[name*="invoice_noextend"]',function(){
            var dataid=$(this).data('id');
	    instancethis.ckeckedflagv=0;
            $('input[name="invoicecodeextend['+dataid+']"],input[name="invoice_noextend['+dataid+']"]').popover('destroy');
            if($('input[name="invoicecodeextend['+dataid+']"]').val()!=''&&$('input[name="invoice_noextend['+dataid+']"]').val()!=''){
                /*if($('input[name="invoicecodeextend['+dataid+']"]').val()==$('input[name="invoicecode"]').val()&&$('input[name="invoice_noextend['+dataid+']"]').val()==$('input[name="invoice_no"]').val()){
                    $('input[name="invoice_noextend['+dataid+']"]').attr('data-title','注意:');$('input[name="invoice_noextend['+dataid+']"]').attr('data-content','<font color="red">发票代码和发票号码有重复</font>');$('input[name="invoice_noextend['+dataid+']"]').popover('show');
                    instancethis.ckeckedflagv=1;
                }*/
		if($('input[name="invoicecodeextend['+dataid+']"]').val()==$('input[name="invoice_noextend['+dataid+']"]').val()){
                    $('input[name="invoicecodeextend['+dataid+']"]').attr('data-title','注意:');$('input[name="invoicecodeextend['+dataid+']"]').attr('data-content','<font color="red">同一组发票代码和发票号码有重复</font>');$('input[name="invoicecodeextend['+dataid+']"]').popover('show');
                    instancethis.ckeckedflagv=1;
                }
                $('input[name*="invoicecodeextend"]').each(function(key,value){
                    var ndataid=$(value).data('id');
                    if($('input[name="invoicecodeextend['+ndataid+']"]').val()!=''&&$('input[name="invoice_noextend['+ndataid+']"]').val()!=''&&dataid!=ndataid){
                        if($('input[name="invoicecodeextend['+dataid+']"]').val()==$('input[name="invoicecodeextend['+ndataid+']"]').val()&&$('input[name="invoice_noextend['+dataid+']"]').val()==$('input[name="invoice_noextend['+ndataid+']"]').val()){
                            $('input[name="invoice_noextend['+dataid+']"]').attr('data-title','注意:');$('input[name="invoice_noextend['+dataid+']"]').attr('data-content','<font color="red">发票代码和发票号码有重复</font>');$('input[name="invoice_noextend['+dataid+']"]').popover('show');
                            instancethis.ckeckedflagv=1;
                        }
                    }
                });
            }
        });
        /*$('#EditView').on('blur','input[name="invoicecode"],input[name="invoice_no"]',function(){
            if($('input[name="invoicecode"]').val()!=''&&$('input[name="invoice_no"]').val()!=''){
                $('input[name="invoice_no"]').popover('destroy');
                $('input[name*="invoicecodeextend"]').each(function(key,value){
                    var ndataid=$(value).data('id');
                    if($('input[name="invoicecodeextend['+ndataid+']"]').val()!=''&&$('input[name="invoice_noextend['+ndataid+']"]').val()!=''){
                        if($('input[name="invoicecodeextend['+ndataid+']"]').val()==$('input[name="invoicecode"]').val()&&$('input[name="invoice_noextend['+ndataid+']"]').val()==$('input[name="invoice_no"]').val()){
                            $('input[name="invoice_no"]').attr('data-title','注意:');$('input[name="invoice_no"]').attr('data-content','<font color="red">发票代码和发票号码有重复</font>');$('input[name="invoice_no"]').popover('show');
                            instancethis.ckeckedflagv=1;
                        }
                    }
                });
            }
        });*/
    },
    //表单提交时验证
    submitcheck:function(){
        //$('input[name="invoicecode"]').trigger('focus');//模拟获取焦点事件
        //$('input[name="invoicecode"]').trigger('blur');//模拟失去焦点事件
        $('input[name*="invoicecodeextend"]').each(function(k,v){
            $(v).trigger('focus');//模拟一下让他从新计算
            $(v).trigger('blur');
        });
    },

	registerEvents: function(){
		this._super();
		this.registerReferenceSelectionEvent();
		this.taxtypechange();
        this.businessnamesonechange();
        this.accountiddisplaychange();
        //this.taxchange();
        //this.amountofmoneychange();
        //this.inputnumberchange();
        this.receivedpaymentsandmoneysum();
        this.registerRecordPreSaveEvent();
        //this.taxchangen();
        this.amountofmoneychangen();
	    this.changebilling();
	    this.addinvoice();
        this.deleteinvoice();
        this.inputnumberextend();
        this.amountofmoneyextend();
        this.checkinvoiceextend();
        //this.addMinus();
	}
});


