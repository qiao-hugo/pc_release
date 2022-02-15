/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("Newinvoice_Edit_Js",{},{
	
	/**
	 * Function which will register event for Reference Fields Selection
	 */
	ckeckedflagv:0,
    checkFormSubmit:0,
	newinvoiceraymentdata : null,
	newinvoicerayment_select_html : '',
	invoiceRelatesData : null,
    invoice_code_exist_flag:0,
    totalandtaxextendsum_flag:0,
    invoicecompany_list:[],

	registerReferenceSelectionEvent : function(container) {
		//wangbin 2015-1-20 发票新增
		//this._super(container);
		var thisInstance = this;

        var taxtype = $("select[name='taxtype']").val();
        if(taxtype == 'invoice'){
            $(".invoicehide").remove();
        }
		
		jQuery('input[name="contractid"]', container).on(Vtiger_Edit_Js.referenceSelectionEvent, function(e, data){
			//thisInstance.referenceSelectionEventHandler(data, container);
			//alert(data['record'])
			//thisInstance.loadWidgetProduct($('.widgetContainer_0'),data['record']);
			//thisInstance.loadWidgetAccount(data['record']);
			//thisInstance.loadReceivepay(data['record']);

            //清空关联项数据
            thisInstance.empty_relation_data();

            ////steel 2015-4-30调用回款信息
            thisInstance.loadReceivepaynew(data['record']);

            thisInstance.empty_newinvoicerayment(); // 清空回款关联信息           
		});
		//加载开票信息
	        jQuery('input[name="account_id"]').on(Vtiger_Edit_Js.referenceSelectionEvent, function (e,data){
	        	accountlist(data['source_module']);
	        });
	        function accountlist(sourcemodule){
	            if(sourcemodule=='Accounts'){
	                var Accountid=$('input[name="account_id"]').val();
	                if(Accountid>0){
	                    thisInstance.loadWidgetNote();
	                    ////steel 2015-4-30 调用回款信息
            			thisInstance.empty_newinvoicerayment(); // 清空回款关联信息
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

    //追加合同关联发票申请类型控制 gaocl add 2018/03/26 ================================================================
    referenceModulePopupRegisterEvent : function(container){
        var thisInstance = this;
        container.on("click",'.relatedPopup',function(e){
            var invoicetype = jQuery('select[name="invoicetype"]').val();
            if(typeof(invoicetype) == "undefined" || invoicetype == "" || invoicetype == 'undefined'){
                Vtiger_Helper_Js.showMessage({type:'error',text:'请先指定发票的申请类型!'});
                return;
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
    getPopUpParams : function(container) {
        var params = {};
        var sourceModule = app.getModuleName();
        var popupReferenceModule = jQuery('input[name="popupReferenceModule"]',container).val();
        var sourceFieldElement = jQuery('input[class="sourceField"]',container);
        var sourceField = sourceFieldElement.attr('name');
        var sourceRecordElement = jQuery('input[name="record"]');
        var invoicetype = jQuery('select[name="invoicetype"]').val();
        var sourceRecordId = '';
        if(sourceRecordElement.length > 0) {
            sourceRecordId = sourceRecordElement.val();
        }

        var isMultiple = false;
        if(sourceFieldElement.data('multiple') == true){
            isMultiple = true;
        }

        var params = {
            'module' : popupReferenceModule,
            'src_module' : sourceModule,
            'src_field' : sourceField,
            'src_record' : sourceRecordId,
            'invoicetype':invoicetype
        }

        if(isMultiple) {
            params.multi_select = true ;
        }
        return params;
    },
    //==================================================================================================================

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
						total += parseFloat(info.products[i]['unit_price']);
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
				$(this).attr('data-title','注意');$(this).attr('data-content','关联回款须小于等于申请开票总额');$(this).popover('show');
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
        if($('select[name=invoicetype]').val() != 'c_billing') {
        	$('input[name="billingcontent"]').attr("readonly","readonly");
        }
        //$('input[name="billingcontent"]').attr("readonly","readonly");
		//$('input[name="taxpayers_no"]').attr("readonly","readonly");
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
                // $('.add-on').each(function(){
                //     if($(this).text() == '$'){
                //         $(this).text('¥');
                //     }
                // });
            }else if(taxname =='specialinvoice'){
                $('.tableadv').removeClass('hide');
                $('.tableadv').find('form').css({width:'48px'});
                $('.tableadv :input').removeAttr('disabled');
                // $('.add-on').each(function(){
                //     if($(this).text() == '$'){
                //         $(this).text('¥');
                //     }
                // });
				//thisInstance.loadWidgetNote();
                //$('.fileUploadContainer').parent().parent().css('whiteSpace','nowrap');
                //$("#Invoice_editView_fieldName_taxpayers_no,#Invoice_editView_fieldName_registeraddress,#Invoice_editView_fieldName_telephone,#Invoice_editView_fieldName_depositbank,#Invoice_editView_fieldName_accountnumber").removeAttr('readonly');
            }else if(taxname =='invoice'){
                // $('.add-on').each(function(){
                //     if($(this).text() == '¥'){
                //         $(this).text('$');
                //     }
                // });
            }else{
                // $('.add-on').each(function(){
                //     if($(this).text() == '$'){
                //         $(this).text('¥');
                //     }
                // });
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
                "module": "Newinvoice",
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
        params["module"] = "Newinvoice"; //ServiceContracts
        params["action"] = "InvoiceRelates";
        params["record"]= id;
        $('.invoicenolist').html('');
        $('.invoicelist').html('');
        AppConnector.request(params).then(
            function(data){
                if(data.success == true){
                	
                    $('input[name="account_id_display"]').val(data.result.accountname);
                    $('input[name=account_id]').val(data.result.id);
                    $('.norecevied').remove();
                    thisInstance.invoiceRelatesData = data.result;
                    //基本信息(申请人录入) 控制
                    thisInstance.setBillingcontent();

                    /* if(data.result.resultlist=='yes'){
                        var json = data.result;

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

                        if(json.marknl=='yes'){
                            var str = '<tr><td>发票编号</td><td>合同编号</td><td>包含回款</td><td>创建时间</td><td>开票日期</td><td>开票抬头</td><td>开票金额</td><td>账号</td></tr>';
                            $.each(json.receivepaynolist, function (i, val) {
                                str += "<tr><td>" + val['invoice_no'] + "</td><td>" + val['contract_no'] + "</td><td>" + val['unit_price'] + "</td><td>" + val['createdtime'] + "</td><td>" + val['billingtime'] + "</td><td>" + val['companyname'] + "</td><td>" + val['taxtotal'] + "</td><td>" + val['accountnumber'] + "</td></tr>";
                        });
                            $('.invoicenolistdisplay').removeClass('hide');
                            $('.invoicenolist').html(str);
                        }
                    }*/

                    //开票金额与合同金额比较只做提示不做其它处理
                    thisInstance.taxtotalandcontact();

                    //加载发票信息 - 发票信息(增值税发票专用) 栏
		            thisInstance.loadWidgetNote();

		            //加载回款信息 gaocl add 2018/03/27
                    var invoicetype = jQuery('select[name="invoicetype"]').val();
                    if(invoicetype == "c_normal") {
                        thisInstance.loadRelationReceivedPayments(id);
                    }

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
    test : function () {
    },
    //steel提交表单事件验证
    registerRecordPreSaveEvent : function(form) {
        var editViewForm = this.getForm();
        var thisInstance = this;
        if(typeof form == 'undefined') {
			form = this.getForm();
		}

        editViewForm.on(Vtiger_Edit_Js.recordPreSave,function(e,data){
            if(thisInstance.submitFlag) return true;

            //开票公司不能为空
            var taxtype = $("select[name='taxtype']").val();
            var invoice_fee = $("input[name='invoice_fee']").val();
            if(taxtype == "invoice" && invoice_fee == ""){
                var  params = {text : app.vtranslate(),title : app.vtranslate('手续费不能为空')};
                Vtiger_Helper_Js.showPnotify(params);
                e.preventDefault();
                return false;
            }

            //开票公司不能为空
            var invoicecompany1 = $("select[name='invoicecompany']").val();
            var invoicecompany2 = $("input[name='invoicecompany']").val();
            if(invoicecompany1 == "" && invoicecompany2 == ""){
                var  params = {text : app.vtranslate(),title : app.vtranslate('开票公司不能为空')};
                Vtiger_Helper_Js.showPnotify(params);
                e.preventDefault();
                return false;
            }

            // var zizhi=$('#fileallzizhi .deletefile').length;
            // if(zizhi<1){
            //     var  params = {text : app.vtranslate(),title : app.vtranslate('纳税资质附件不能为空！')};
            //     Vtiger_Helper_Js.showPnotify(params);
            //     e.preventDefault();
            //     return false;
            // }
            var billingsourcedata=$("select[name='billingsourcedata']").val();//开票数据来源

            //合同方公司抬头
            var account_id_display = $("input[name='account_id_display']").val();
            if(account_id_display == ""&&billingsourcedata=='contractsource'){
                var  params = {text : app.vtranslate(),title : app.vtranslate('合同方公司抬头不能为空')};
                Vtiger_Helper_Js.showPnotify(params);
                e.preventDefault();
                return false;
            }
            var taxtype=$('select[name="taxtype"]').val();
            if(taxtype=='electronicinvoice'){
                var email1=$('input[name="email"]').val();
                if(email1==''){
                    var  params = {text : app.vtranslate(),title : app.vtranslate('邮箱不能为空，必填！')};
                    Vtiger_Helper_Js.showPnotify(params);
                    e.preventDefault();
                    return false;
                }
                var invoicecompanyArray=['珍岛信息技术（上海）股份有限公司','无锡珍岛数字生态服务平台技术有限公司','凯丽隆（上海）软件信息科技有限公司','广东珍岛信息技术有限公司','成都珍岛信息技术有限公司','金华市珍岛信息技术有限公司','上海珍岛智能技术集团有限公司佛山分公司','上海珍岛智能技术集团有限公司广州分公司','上海珍岛网络科技有限公司','苏州珍岛信息技术有限公司','杭州珍岛信息技术有限公司','台州珍岛信息技术有限公司','上海珍岛智能技术集团有限公司东莞分公司','上海珍岛智能技术集团有限公司义乌分公司'];
                var invoicecompany=$('select[name="invoicecompany"]').val();
                if($.inArray(invoicecompany,invoicecompanyArray)==-1){
                    var  params = {text : app.vtranslate(),title : app.vtranslate('当前开票公司不支持开具电子发票，请修改票据类型')};
                    Vtiger_Helper_Js.showPnotify(params);
                    e.preventDefault();
                    return false;
                }
            }else if(taxtype!='invoice'){
                var addressee = $.trim($('input[name=addressee]').val());
                var address = $.trim($('input[name=address]').val());
                var addresseephone = $.trim($('input[name=addresseephone]').val());
                if(!addressee ||!address ||!addresseephone) {
                    var  params = {text : app.vtranslate(),title : app.vtranslate('非增值税电子发票，收件人、收件人手机号、收货地址不能为空')};
                    Vtiger_Helper_Js.showPnotify(params);
                    e.preventDefault();
                    return false;
                }

            }
            var headuptype=$('select[name=headuptype]').val();//抬头类型

            if((billingsourcedata=='contractsource'||headuptype!='personheadup')&&$.trim($('input[name=taxpayers_no]').val())==''){
                var  params = {text : app.vtranslate(),title : app.vtranslate('纳税人识别税号/税号不能为空')};
                Vtiger_Helper_Js.showPnotify(params);
                e.preventDefault();
                return false;
            }

        	// 不同的票据类型 判断是否为空
        	var taxtype = $('select[name=taxtype]').val();
        	if (taxtype == 'specialinvoice'||headuptype=='companyheadup') {
        		var registeraddress = $.trim($('input[name=registeraddress]').val());
        		var depositbank = $.trim($('input[name=depositbank]').val());
        		var telephone = $.trim($('input[name=telephone]').val());
        		var accountnumber = $.trim($('input[name=accountnumber]').val());
        		//var isformtable = $.trim($('input[name=isformtable]').attr('checked'));
        		//var isformtable = $('input[name="isformtable"]').is(':checked');
        		if(!registeraddress || !depositbank || !telephone || !accountnumber) {
        			var  params = {text : app.vtranslate(),title : app.vtranslate('增值税专用发票或者公司抬头类型注册地址、开户行、电话、账号等不能为空')};
        			Vtiger_Helper_Js.showPnotify(params);
        			e.preventDefault();
        			return false;
        		}
        	}

        	// 发票金额不能为空
        	var taxtotal = $('input[name=taxtotal]').val();
        	if(taxtotal) {
        		taxtotal = taxtotal.replace(/,/g, '');
        	}
        	taxtotal = parseFloat(taxtotal);
        	if((!taxtotal)||isNaN(taxtotal)){
        		var  params = {text : app.vtranslate(),title : app.vtranslate('申请开票总额不能为空')};
        		Vtiger_Helper_Js.showPnotify(params);
        		e.preventDefault();
        		return false;
        	}
        	
        	// 预开票的， 开票内容不能为空
        	var invoicetype = $('select[name=invoicetype]').val();
        	if(invoicetype == 'c_billing') {
        		var contractid = $('input[name=contractid]').val();
        		var t_flag = false;
        		if(contractid) {
        			if(! $.trim($('input[name=billingcontent]').val()) && !$.trim($('select[name=billingcontent]').val()) ) {
        				t_flag = true;
        			}
        		} else {
        			if(! $.trim($('select[name=billingcontent]').val()) ) {
        				t_flag = true;
        			}
        		}
        		if(t_flag) {
        			var  params = {text : app.vtranslate(),title : app.vtranslate('预开票的发票内容不能为空')};
	        		Vtiger_Helper_Js.showPnotify(params);
	        		e.preventDefault();
	        		return false;
        		}
        	} else if(billingsourcedata!='ordersource'){
        		if(parseFloat(taxtotal) <= 0) {
	        		var  params = {text : app.vtranslate(),title : app.vtranslate('正常发票使用开票金额必须大于0')};
	        		// var  params = {text : app.vtranslate(),title : app.vtranslate('正常发票发票金额必须大于0')};
	        		Vtiger_Helper_Js.showPnotify(params);
	        		e.preventDefault();
	        		return false;
	        	}

	        	// 回款关联不能为空
                var id = $('input[name=record]').val();
                if (id == "") {
                    if ($('.newinvoicerayment_tab').size() == 0) {
                        var params = {text: app.vtranslate(), title: app.vtranslate('没有关联回款')};
                        Vtiger_Helper_Js.showPnotify(params);
                        e.preventDefault();
                        return false;
                    }
                }
                var invoicetotalSum = 0;
                $(".invoicetotal").each(function(k,v) {
                        invoicetotalSum += parseFloat($(v).val());
                });

                if(taxtype != "invoice"){
                    if(invoicetotalSum>parseFloat(taxtotal)){
                        var params = {text: app.vtranslate(), title: app.vtranslate('关联回款须小于等于申请开票总额')};
                        Vtiger_Helper_Js.showPnotify(params);
                        e.preventDefault();
                        return false;
                    }
                }

	        	var tttt_flag = false;

                $('.newinvoicerayment_tab').each(function () {
                    var ttt_invoicetotal = $(this).find('.invoicetotal').val();
                    ttt_invoicetotal = parseFloat(ttt_invoicetotal);
                    if(isNaN(ttt_invoicetotal)) {
                        tttt_flag = true;
                    }
                    if(!tttt_flag) {
                        if(ttt_invoicetotal <=0 ) {
                            tttt_flag = true;
                        }
                    }
                });

	        	if(tttt_flag) {
	        		var  params = {text : app.vtranslate(),title : app.vtranslate('关联回款的使用开票金额必须大于零')};
	        		// var  params = {text : app.vtranslate(),title : app.vtranslate('关联回款的发票金额发票金额必须大于零')};
	        		Vtiger_Helper_Js.showPnotify(params);
	        		e.preventDefault();
	        		return false;
	        	}
        	}
        	var f = thisInstance.checkInvoicetotalSum();
        	if (f) {
        		var  params = {text : app.vtranslate(),title : app.vtranslate('回款关联的使用开票金额不能大于回款的入账金额')};
        		// var  params = {text : app.vtranslate(),title : app.vtranslate('回款关联的发票金额不能大于回款的入账金额')};
        		Vtiger_Helper_Js.showPnotify(params);
        		e.preventDefault();
        		return false;
        	}

        	// 判断关联回款的合同主体 是否 和 开票公司一样
        	var invoicecompany = $('select[name=invoicecompany]').val();
        	var is_eqinvoicecompany = false;

        	// 判断 数据库中的回款可开发票金额 是否大于 发票金额
        	var invoicerayment_arr = {};
        	var receivedpaymentsid_arr = [];
        	var i=1;
        	var notifystr='';
        	$('.newinvoicerayment_tab').each(function (k,v) {

        		var newinvoicerayment_id = $(this).find('.receivedpaymentsid').val();
        		var invoicetotal = $(this).find('.receivedpayments_invoicetotal').val();
        		if (newinvoicerayment_id>0) {
        			invoicerayment_arr[newinvoicerayment_id] = invoicetotal;
        			receivedpaymentsid_arr.push(newinvoicerayment_id);
        		}

        		var ment_invoicecompany = $(this).find('.invoicecompany').val();
        		if(ment_invoicecompany != invoicecompany) {
        			is_eqinvoicecompany = true;
                    notifystr +=$(v).find('.blockHeader').text()+' ';
        		}
        		i++;
        	});


        	if(is_eqinvoicecompany) {
        		var  params = {text : app.vtranslate(),title : app.vtranslate(notifystr+'回款已解除,请删除')};
        		Vtiger_Helper_Js.showPnotify(params);
        		e.preventDefault();
        		return false;
        	}
            var len = $('input[name="receivedid[]"]:checked:checked').length;
            thisInstance.submitcheck();
            if(thisInstance.ckeckedflagv==1){
                e.preventDefault(); //阻止提交事件先注释
                return false;
            }

            //发票代码&发票号码完全重复的两张发票不能保存 gaocl add 2018/03/28
            if(thisInstance.invoice_code_exist_flag==1){
                var  params = {text : app.vtranslate(),title : app.vtranslate('发票代码+发票号码有重复')};
                Vtiger_Helper_Js.showPnotify(params);
                e.preventDefault();
                return false;
            }

            //发票财务数据那里价税合计大于申请开票金额应不能保存 gaocl add 2018/03/28
            var totalandtaxextendsum=0
            $('#EditView input[name*="totalandtaxextend"]').each(function () {
                totalandtaxextendsum=thisInstance.accAdd(totalandtaxextendsum,Number($(this).val().replace(/,/g,'')));
            })
            var taxtotal=Number($('input[name="taxtotal"]').val().replace(/,/g,''));
            if(totalandtaxextendsum>0 && totalandtaxextendsum!=taxtotal){
                var  params = {text : app.vtranslate(),title : app.vtranslate('发票总价税合计等于申请开票总额方可保存')};
                Vtiger_Helper_Js.showPnotify(params);
                e.preventDefault();
                return false;
            }

            //计算所有发票的开票日期必须在同一个月
            var billingTimeLength=$('input[name^="billingtimerextend"]').length;
            if(billingTimeLength>1){
                submitFlag=true;
                var firstBillingTime=$('input[name^="billingtimerextend"]').eq(0).val();
                $('input[name^="billingtimerextend"]').each(function () {
                    var otherBillingTime=$(this).val();
                    var isTheSameMonth=thisInstance.isTheSameMonth(firstBillingTime,otherBillingTime);
                    if(!isTheSameMonth){
                        submitFlag=false;
                    }
                });
                if(!submitFlag){
                    Vtiger_Helper_Js.showMessage({type: 'error', text: '所有发票开票必须在同一个月'});
                    e.preventDefault();
                    return false;
                }
            }

            if($("input[name='email']").val()=='' && $('select[name="isaccountinvoice"]').val() == 'noneed' && ($('select[name="taxtype"]').val() == 'invoice' || $('select[name="taxtype"]').val() == 'electronicinvoice')){
                //是否需要发票字段 填写否则邮箱必填
                Vtiger_Helper_Js.showMessage({type: 'error', text: '邮箱必填'});
                e.preventDefault();
                return false;
            }

            if($("input[name='record']").val()=='' && $('select[name="taxtype"]').val() == 'specialinvoice'){
                //如果是add，且票据类型是增值税专用发票，资质附件必填
                if($("#fileallzizhi").find('input[name^="attachmentsid["]').length==0){
                    Vtiger_Helper_Js.showMessage({type: 'error', text: '没有纳税资质附件'});
                    e.preventDefault();
                    return false;
                }
            }

            if($("input[name^='file[']").length==0){
                //证明没发票附件
                $("#fileall").html('');
            }

            //确认提示
            if(invoicetype == 'c_billing') {
                if($("input[name='is_fp_admin']").val()!=0){
                    if(!thisInstance.checkFormSubmit){
                        var message='<br>确定要提交吗?<br><br><strong>提醒:</strong><br><br style="color:red">1.核对开具的票据类型和提单的是否一致<br><br>2.核对发票代码、发票号码是否和机打出来的一致<br> <br>3.检查开票信息是否与提单一致<br> <br>4.检查开票内容是否和提单一致，如备注了开票内容，则以备注为准<br> <br>5.核对不含税金额及税额<br> <br>6.检查是否加盖发票专用章（专票第二、三联，普票第二联）<br> <br>7.注意：第一联我们自行留存<br>';
                        var msg={
                            'message':message
                        };
                        Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                            var msg={
                                'message':'请记得提交审核！否则无法进入开票审批环节！'
                            };
                            Vtiger_Helper_Js.showPubDialogBox(msg).then(function(e){
                                thisInstance.checkFormSubmit=1;
                                editViewForm.submit();
                            },function(error, err) {});
                        },function(error, err) {
                        });
                    }
                }else{
                    if(!thisInstance.checkFormSubmit){
                        var msg={
                            'message':'请记得提交审核！否则无法进入开票审批环节！'
                        };
                        Vtiger_Helper_Js.showPubDialogBox(msg).then(function(e){
                            thisInstance.checkFormSubmit=1;
                            editViewForm.submit();
                        },function(error, err) {});
                    }
                }
                if(thisInstance.checkFormSubmit){
                    return thisInstance.checkButtonReceivedpayments(receivedpaymentsid_arr,invoicerayment_arr);
                }
            }else if(billingsourcedata!='ordersource'){
                var taxtotal = $("input[name='taxtotal']").val();
                var rayment_count = $(".newinvoicerayment_div table").length;
                var  msg={};
                msg['title']='发票申请确认';
                var message='';
                if($("input[name='is_fp_admin']").val()!=0){
                    message='<br><strong>提醒:</strong><br><br style="color:red">1.核对开具的票据类型和提单的是否一致<br><br>2.核对发票代码、发票号码是否和机打出来的一致<br> <br>3.检查开票信息是否与提单一致<br> <br>4.检查开票内容是否和提单一致，如备注了开票内容，则以备注为准<br> <br>5.核对不含税金额及税额<br> <br>6.检查是否加盖发票专用章（专票第二、三联，普票第二联）<br> <br>7.注意：第一联我们自行留存<br>';
                }
                msg['message']=message+'<br>'+'本次申请开票总额合计为【'+taxtotal+'】，已关联【'+rayment_count+'】笔回款，确认申请？';

                /*var ss = confirm(msg['message']);
                if(ss){
                    return thisInstance.checkButtonReceivedpayments(receivedpaymentsid_arr,invoicerayment_arr);
                }else{
                    return false;
                }*/

                //弹出框
                Vtiger_Helper_Js.showPubDialogBox(msg).then(function(e){
                    if(e){
                        var msg={
                            'message':'请记得提交审核！否则无法进入开票审批环节！'
                        };
                        Vtiger_Helper_Js.showPubDialogBox(msg).then(function(e){
                            thisInstance.submitFlag = thisInstance.checkButtonReceivedpayments(receivedpaymentsid_arr,invoicerayment_arr);
                            if(thisInstance.submitFlag){
                                editViewForm.submit();
                                thisInstance.submitFlag = false;
                            }
                        },function(error, err) {});
                    }
                },function(error, err) {});
            }else{
                //订单开票渠道
                if(headuptype=='personheadup'&&taxtype!='generalinvoice'){
                    var  params = {text : app.vtranslate(),title : app.vtranslate('抬头类型为个人的只能开增值税普通发票')};
                    $("select[name='taxtype']").val('generalinvoice');
                    $("select[name='taxtype']").trigger('change');
                    $("select[name='taxtype']").trigger('liszt:updated');
                    Vtiger_Helper_Js.showPnotify(params);
                    e.preventDefault();
                    return false;
                }

                if($("select[name='ordersystem']").val()=='witkeysystem'){
                    var  params = {text : app.vtranslate(),title : app.vtranslate('数字威客系统暂不开放')};
                    Vtiger_Helper_Js.showPnotify(params);
                    e.preventDefault();
                    return false;
                }

                if($(".linkedOrder_div").find('.delButtonOrder').length==0){
                    //订单开票渠道必须有订单
                    var  params = {text : app.vtranslate(),title : app.vtranslate('订单开票渠道必须有订单信息')};
                    Vtiger_Helper_Js.showPnotify(params);
                    e.preventDefault();
                    return false;
                }
                editViewForm.submit();
                return true;
            }
            return false;
        });
    },
    submitFlag:false,

    /**
     *判断是否年月一致
     * @param invoiceTime
     * @param nowTime
     * @returns {boolean|boolean}
     */
    isTheSameMonth:function(invoiceTime,nowTime){
        var dt1 = new Date(invoiceTime.replace(/-/g,"/"));
        var dt2 = new Date(nowTime.replace(/-/g,"/"));
        return dt1.getFullYear() === dt2.getFullYear() && dt1.getMonth() === dt2.getMonth();
    },

    checkButtonReceivedpayments:function(receivedpaymentsid_arr,invoicerayment_arr) {
            //if (receivedpaymentsid_arr.length > 0) {
            var module = app.getModuleName();
            /*	var postData = {
                    "module": module,
                    "action": "BasicAjax",
                    'mode': 'getReceivedpayments',
                    'record': $('input[name=record]').val(),
                    "receivedpaymentsids": receivedpaymentsid_arr.join(','),
                }*/
            var postData = {};
            postData.data = {
                "module": module,
                "action": "BasicAjax",
                'mode': 'getReceivedpayments',
                'record': $('input[name=record]').val(),
                'invoicecompany': $('select[name=invoicecompany]').val(),
                "receivedpaymentsids": receivedpaymentsid_arr.join(','),
            }
            postData.async = false;
            var Message = app.vtranslate('正在提交...');
            var progressIndicatorElement = jQuery.progressIndicator({
                'message' : Message,
                'position' : 'html',
                'blockInfo' : {'enabled' : true}
            });

            var error_flag = false;
            //if (!thisInstance.flag) {
            AppConnector.request(postData).then(
                function(data){
                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                    if(data.success) {
                        receivedpaymentsid_res = data.result;

                        //判断合同客户与发票合同方公司抬头是否一致 gaocl add 2018/03/28
                        var id = $('input[name=record]').val();
                        if(id >0 ){
                           if(receivedpaymentsid_res.account_same_flag == 0) {
                               var params = {text: app.vtranslate(), title: app.vtranslate('发票合同方公司抬头与合同客户不一致')};
                               Vtiger_Helper_Js.showPnotify(params);
                               error_flag = true;
                           }else{
                               if(receivedpaymentsid_res.invoicecompany_same_flag == 0) {
                                   var params = {text: app.vtranslate(), title: app.vtranslate('发票合同主体与开票公司不一致')};
                                   Vtiger_Helper_Js.showPnotify(params);
                                   error_flag = true;
                               }
                           }
                        }
                        if(error_flag){
                            return false;
                        }

                        var j = 0;
                        if(receivedpaymentsid_res && receivedpaymentsid_arr.length > 0) {
                            for(var i in invoicerayment_arr) {
                                if (parseFloat(invoicerayment_arr[i]) > parseFloat(receivedpaymentsid_res[i])) {
                                    $('.newinvoicerayment_tab').eq(j).find('.allowinvoicetotal').val(receivedpaymentsid_res[i]);
                                    var  params = {text : app.vtranslate(),title : app.vtranslate('回款关联的使用开票金额不能大于回款的可开发票金额')};
                                    // var  params = {text : app.vtranslate(),title : app.vtranslate('回款关联的发票金额不能大于回款的可开发票金额')};
                                    error_flag = true;
                                    Vtiger_Helper_Js.showPnotify(params);
                                    j = -1;
                                    break;
                                }
                                j ++;
                            }
                        }

                        /*if(j != -1) {
                            thisInstance.flag = true;
                            form.submit();
                        }*/

                    }
                    if(error_flag){
                        return false;
                    }
                },
                function(error,err){

                }
            );
            //e.preventDefault();
            if(error_flag){
                return false;
            }
            return true;
            //}
            //}
    },

    addMinus:function(){
        $('input[name="amountofmoneynegative"]').siblings().append('<span style="color:#FF0000;font-size:16px;">　-</span>');
        $('input[name="taxnegative"]').siblings().append('<span style="color:#FF0000;font-size:16px;">　-</span>');
        $('input[name="totalandtaxnegative"]').siblings().append('<span style="color:#FF0000;font-size:16px;">　-</span>');
    },
    //发票信息(增值税发票专用) 栏
    loadWidgetNote : function(){
        var accountid=$('input[name="account_id"]').val();
        var taxtype=$('select[name="taxtype"]').val();
        if(accountid<1){
            return;
        }
        var params={};
        params['accountid'] =accountid ;                  //公司的id
        params['module'] = 'Newinvoice';
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
                            //$('input[name="businessnamesone"]').val(data.result[0].businessnamesone);
                            $('input[name="businessnames"]').val(data.result[0].businessnamesone);
                            $('input[name="businessnamesnegative"]').val(data.result[0].businessnamesone);
			                $('input[name="billingid"]').val(data.result[0].billingid);

                            
                            if(data.result[0].isformtable == '1') {
                            	$('input[name="isformtable"]').attr('checked','checked');
                            }	 else {
                            	$('input[name="isformtable"]').removeAttr('checked');
                            }
                            
                        }else{
                        	var record = $('input[name=record]').val();
                        	if(!record) {
                        		$('input[name="taxpayers_no"]').val(data.result[0].taxpayers_no);
	                            $('input[name="registeraddress"]').val(data.result[0].registeraddress);
	                            $('input[name="depositbank"]').val(data.result[0].depositbank);
	                            $('input[name="telephone"]').val(data.result[0].telephone);
	                            $('input[name="accountnumber"]').val(data.result[0].accountnumber);
	                            $('input[name="businessnamesone"]').val(data.result[0].businessnamesone);
	                            $('input[name="businessnames"]').val(data.result[0].businessnamesone);
	                            $('input[name="businessnamesnegative"]').val(data.result[0].businessnamesone);
				                $('input[name="billingid"]').val(data.result[0].billingid);
	                            //$('input[name="isformtable"]').val(data.result[0].isformtable);
	                            //$('input[name="isformtable"]').attr('checked','checked');

	                            if(data.result[0].isformtable == '1') {
	                            	$('input[name="isformtable"]').attr('checked','checked');
	                            }	 else {
	                            	$('input[name="isformtable"]').removeAttr('checked');
	                            }
                        	} 
                            
                            
                            var str="";
                            $.each(data.result,function(n,value){
                                str += "<option value="+JSON.stringify(data.result[n])+'>'+value.businessnamesone+"</option>";
                            })
                            newstr = "<select id='contactorselect'>"+str+"</select>"
                            $('#Newinvoice_editView_fieldName_taxpayers_no').closest('span').append(newstr);
                            }
		    }else{
                       /* $('input[name="taxpayers_no"]').val('');
                        $('input[name="registeraddress"]').val('');
                        $('input[name="depositbank"]').val('');
                        $('input[name="telephone"]').val('');
                        $('input[name="accountnumber"]').val('');
                        $('input[name="businessnamesnegative"]').val('');
                        $('input[name="billingid"]').val('');
                        $('input[name="isformtable"]').val(1);
                        $('input[name="isformtable"]').attr('checked','checked');*/
                    }
                }
            })
    },
    changebilling:function(){
        $(document).on('change','#contactorselect',function(){
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
	    	if(objjson.isformtable == '1') {
	    		$('input[name="isformtable"]').attr('checked', 'checked');
	    	} else {
	    		$('input[name="isformtable"]').removeAttr('checked');
	    	}
           

            //$('input[name="isformtable"]').attr('checked','checked');
        });
    },

    // 清空关联项数据 gaocl add 2018/03/26
    empty_relation_data : function () {
        var me = this;
        //开票公司
        $('input[name="invoicecompany"]').val("");
        //合同方公司抬头
        $('input[name="account_id_display"]').val("");
        //实际开票抬头
        $('input[name="businessnamesone"]').val("");
        me.invoiceRelatesData = null;
        me.newinvoiceraymentdata = null;
    },

    // 清空回款关联
    empty_newinvoicerayment : function () {
    	$('.newinvoicerayment_tab').remove();
    	this.newinvoiceraymentdata=null;
    	$('#Newinvoice_editView_fieldName_taxtotal').val();
    },

    // 回款关联添加
    addNewinvoicerayment2 : function() {
    	var newinvoiceraymentnum = $('.newinvoicerayment_tab').length + 1;
		if (newinvoiceraymentnum > 100) {return ;}
		var nowdnum=$('.newinvoicerayment_tab').last().data('num');
        if(nowdnum){
            newinvoiceraymentnum=nowdnum+1;
        }
		var t_newinvoicerayment_html = newinvoicerayment_html.replace(/\[\]/g,'['+newinvoiceraymentnum+']');
        t_newinvoicerayment_html = t_newinvoicerayment_html.replace(/yesreplace/g, newinvoiceraymentnum);
        t_newinvoicerayment_html = t_newinvoicerayment_html.replace(/newinvoicerayment_select_html/g, this.newinvoicerayment_select_html);
		$('.newinvoicerayment_div').append(t_newinvoicerayment_html);
        $('.chzn-select').chosen();
    },


    // 回款信息选择
    select_newinvoicerayment: function() {
    	var me = this;
    	$(document).on('change','.t_tab_newinvoicerayment_id', function () {

    		var t = {};
			$('.newinvoicerayment_tab').each(function () {
				var  newinvoicerayment_id = parseFloat($(this).find('.t_tab_newinvoicerayment_id').val());
				if (newinvoicerayment_id) {
					if (! t[newinvoicerayment_id]) {
						t[newinvoicerayment_id] = 1;
					} else {
						t[newinvoicerayment_id] += 1;
					}
				}
			});
			for (var j in t) {
				if (t[j] > 1) {
					var  params = {text : app.vtranslate(),title : app.vtranslate('不能添加多个相同回款的关联信息')};
        			Vtiger_Helper_Js.showPnotify(params);
					return false;
				}
			}

    		var $t = $(this).closest('.newinvoicerayment_tab');
    		var i = $(this).val();
    		if (i) {
    			$t.find('.servicecontractsid_display').val(me.newinvoiceraymentdata[i]['contract_no']);
    			$t.find('.servicecontractsid').val(me.newinvoiceraymentdata[i]['servicecontractsid']);
    			$t.find('.total').val(me.newinvoiceraymentdata[i]['unit_price']);
    			$t.find('.arrivaldate').val(me.newinvoiceraymentdata[i]['reality_date']);
    			$t.find('.allowinvoicetotal').val(me.newinvoiceraymentdata[i]['allowinvoicetotal']);
    			$t.find('.invoicecontent').val(me.newinvoiceraymentdata[i]['billingcontent']);
    			$t.find('.invoicetotal').val(me.newinvoiceraymentdata[i]['allowinvoicetotal']);
    			$t.find('.receivedpaymentsid_display').val(me.newinvoiceraymentdata[i]['t_paytitle']);
    			$t.find('.invoicecompany').val(me.newinvoiceraymentdata[i]['invoicecompany']);
    			//
    			me.calculation_invoicetotal_sum();
    		}

    		me.setAllInvoicecontent();
    	});


    	$(document).on('blur', '.receivedpayments_invoicetotal', function () {
    		me.calculation_invoicetotal_sum();
    	});
    },
    // 计算 回款关联中的 开票金额的总和
    calculation_invoicetotal_sum : function() {
    	var me = this;
    	var total = 0;
    	$('.newinvoicerayment_tab .receivedpayments_invoicetotal').each(function () {
    		me.formatNumbern($(this));
    		var t = parseFloat($(this).val());
    		if (t) {
    			total = parseFloat(total) + t;
    		}
            total =total.toFixed(2);
    	});
        var invoicedTotal = parseFloat($("input[name='invoicedTotal']").val());
        total = (total>invoicedTotal)?invoicedTotal:total;

    	$('input[name=taxtotal]').val(total);
        $('input[name="taxtotal_money"]').val(total);
    },

    // 回款关联添加
    addNewinvoicerayment : function () {
    	var me = this;
    	$('#add_newinvoicerayment, #add_newinvoicerayment_tt').on('click', function () {
    		var tt_record = $('input[name=record]').val();
    		var account_id = $('input[name=account_id]').val();
    		if (!account_id) {
    			var  params = {text : '合同方公司抬头不能为空', title : '错误提示'};
				Vtiger_Helper_Js.showPnotify(params);
    			return ;
    		}

    		var invoicecompany = $('input[name=invoicecompany]').val();
        	if(!invoicecompany) {
        		var  params = {text : '请先选择开票公司', title : '错误提示'};
				Vtiger_Helper_Js.showPnotify(params);
				return ;
        	}

    		if (me.newinvoiceraymentdata) {
    			me.addNewinvoicerayment2();
    		} else {

    			var urlParams = 'module=Newinvoice&action=BasicAjax';
				var params = {
					'type' : 'GET',
					'dataType': 'html',
					'data' : urlParams+'&mode=getNewinvoicerayment&account_id='+account_id + '&invoicecompany='+invoicecompany + '&recordid='+tt_record  //module=ServiceContracts&view=ListAjax&mode=edit&record=615
				};
				AppConnector.request(params).then(
					function(data){
						var info=eval("("+data+")");
						if(info.success){
							me.newinvoiceraymentdata=info.result;
							var t = info.result;
							me.newinvoicerayment_select_html = '<option value="">请选择</option>';
							for (var i in t) {
								me.newinvoicerayment_select_html += '<option value="'+ i +'">'+ t[i]['paytitle']  +'</option>';
							}
							me.addNewinvoicerayment2();
						}
					},
					function(){
					}
				);
    		}
    		
    	} );
    },

    //多发票的添加
    addinvoice:function(){
    	var me = this;
        $('#addfallinto').on('click',function(){
            var numd=$('.Duplicates').length+1;
            if(numd>100){return;}/*超过100个不允许添加*/
            var nowdnum=$('.Duplicates').last().data('num');
            if(nowdnum!=undefined){
                numd=nowdnum+1;
            }

            var taxtype = $('input[name=taxtype]').val();
            if(taxtype == 'invoice'){
                var extend=extendinvoiceless.replace(/\[\]|replaceyes/g,'['+numd+']');
            }else{
            var extend=extendinvoice.replace(/\[\]|replaceyes/g,'['+numd+']');
            }

            extend=extend.replace(/yesreplace/g,numd);
           
            //extend=extend.replace(/reg_commoditynameextend/g, me.getAllInvoicecontent());
            //var invoicetype = $('select[name=invoicetype]').val();
            var billingcontent = $('input[name=billingcontent]').val();
            billingcontent = billingcontent || $('select[name=billingcontent]').val();
            ///extend = extend.replace(/reg_commoditynameextend/g, billingcontent);
            




            
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
            //alert(billingcontent);
            $('input[name="commoditynameextend['+numd+']"]').val(billingcontent);
            //$('input[name="commoditynameextend['+numd+']"]').val($('input[name="billingcontent"]').val());
            //$('input[name="commoditynameextend['+numd+']"]').val(me.getAllInvoicecontent());
            if($('select[name=invoicetype]').val() == 'c_normal') {
            	me.setAllInvoicecontent();
            }
            
            //$('input[name="commoditynameextend['+numd+']"]').val($('input[name*="commoditynameextend"]').first().val());
            $('.chzn-select').chosen();
        });
    },

  	// 获取关联回款的 开票内容
  	setAllInvoicecontent : function () {
  		var invoicecontentAll = [];
  		$('.newinvoicerayment_tab').each(function () {
  			var invoicecontent = $(this).find('.invoicecontent').val();
  			if(invoicecontentAll.indexOf(invoicecontent) == -1) {
  				invoicecontentAll.push(invoicecontent);
  			}
  		});
  		$('input[name*=commoditynameextend]').val(invoicecontentAll.join(' / '));
  		//return invoicecontentAll.join(' / ');
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

    // 删除匹配的回款
    delbuttonnewinvoicerayment: function() {
    	var me = this;
    	$(document).on('click', '.delbuttonnewinvoicerayment', function () {
    		var newthis=$(this);
            var message='确定要删除吗？';
            var msg={
                'message':message
            };
            var dataid=$(this).data('id');
            //flagv=2;
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                newthis.closest('.newinvoicerayment_tab').remove();
                me.calculation_invoicetotal_sum();
            },function(error, err) {});
    	});
    },	

    //删除多发票
    deleteinvoice:function(){
        var thisInstance=this;
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
                thisInstance.caclActualtotal();
            },function(error, err) {});

        });

    },
    caclActualtotal:function(){
        var thisInstance=this;
        var totalandtaxextendsum=0
        $('input[name*="totalandtaxextend"]').each(function(k,v){
            //totalandtaxextendsum+=Number($(v).val().replace(/,/g,''));
            totalandtaxextendsum=thisInstance.accAdd(totalandtaxextendsum,Number($(v).val().replace(/,/g,'')));
        });
        //totalandtaxextendsum+=Number($('input[name="totalandtax"]').val().replace(/,/g,''));
        totalandtaxextendsum+=0;


        // 设置实际开票金额

        $('input[name=actualtotal]').val(totalandtaxextendsum);
    },
    //多发票验证计算
    amountofmoneyextend : function(){
        var thisInstance=this;

        $('#EditView').on('blur','input[name*="totalandtaxextend"]',function(){
            var dataid=$(this).data('id');
            var totalandtaxextendsum=0
            $('input[name*="totalandtaxextend"]').each(function(k,v){
                //totalandtaxextendsum+=Number($(v).val().replace(/,/g,''));
                totalandtaxextendsum=thisInstance.accAdd(totalandtaxextendsum,Number($(v).val().replace(/,/g,'')));
            });
            //totalandtaxextendsum+=Number($('input[name="totalandtax"]').val().replace(/,/g,''));
	    	totalandtaxextendsum+=0;
            var taxtotal=Number($('input[name="taxtotal"]').val().replace(/,/g,''));

            // 设置实际开票金额
            
            $('input[name=actualtotal]').val(totalandtaxextendsum);

            if(isNaN($(this).val().replace(/,/g,''))){
                $(this).val('');
                return;
            }else if(totalandtaxextendsum!=taxtotal){
                //$(this).parent().before('<div class="alert alert-error"> <button type="button" class="close" data-dismiss="alert">×</button><strong>金额与开票金额不一致!</strong></div>');
                $(this).attr('data-title','注意:');$(this).attr('data-content','价税合计与申请开票总额不一致<br>申请开票总额:<font color="red" id="pclosed'+dataid+'">'+taxtotal+'</font><br>已开价税合计:<font color="red">'+totalandtaxextendsum+'</font>');$(this).popover('show');
                if(totalandtaxextendsum > taxtotal){
                    thisInstance.totalandtaxextendsum_flag = 1;
                }else{
                    thisInstance.totalandtaxextendsum_flag = 0;
                }
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
                thisInstance.caclActualtotal();
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
                thisInstance.caclActualtotal();
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
                    if(!/^(\d{10}|\d{12})$/.test($('input[name="invoicecodeextend['+ndataid+']"]').val())){
                        $('input[name="invoicecodeextend['+ndataid+']"]').attr('data-title','注意:');$('input[name="invoicecodeextend['+ndataid+']"]').attr('data-content','<font color="red">发票代码长度为10位数字或12位数字</font>');$('input[name="invoicecodeextend['+ndataid+']"]').popover('show');
                        instancethis.ckeckedflagv=1;
                    }
                    if(!/^\d{8}$/.test($('input[name="invoice_noextend['+ndataid+']"]').val())){
                        $('input[name="invoice_noextend['+ndataid+']"]').attr('data-title','注意:');$('input[name="invoice_noextend['+ndataid+']"]').attr('data-content','<font color="red">发票号码长度为8位数字</font>');$('input[name="invoice_noextend['+ndataid+']"]').popover('show');
                        instancethis.ckeckedflagv=1;
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

    // 修改开票内容
    /*selectContractidSetBillingcontent : function(billcontent) {
    	$('input[name="billingcontent"]').val(billcontent);
        var invoicetype = $('select[name=invoicetype]').val();
		if(invoicetype == 'c_billing') {
			$('input[name=billingcontent]').attr('readonly', 'readonly');
		}
    },*/
	// 设置开票内容
	setBillingcontent123: function () {
        var me = this;

        //正常/预开票:开票公司和合同方公司抬头，并且都不可编辑
        //合同方公司抬头
        //$("#Newinvoice_editView_fieldName_account_id_select").parent("span").hide();
        $("#account_id_display").attr('disabled', 'disabled');
        //开票公司
        if (me.invoiceRelatesData) {
            $("select[name='invoicecompany']").val(me.invoiceRelatesData.invoicecompany);
        }
        //开票公司不可编辑
        $("select[name='invoicecompany']").attr('readonly', 'readonly');

		var invoicetype = $('select[name=invoicetype]').val();
		var record = $('input[name=record]').val();

        $('.newinvoicerayment_div').show();
        $('input[name=billingcontent]').closest('td').hide();
        $('input[name=billingcontent]').closest('td').prev().hide();

		//if (!record) {
			if (invoicetype == 'c_normal') {

    			$('input[name=billingcontent]').val('').attr('readonly', 'readonly');

    			$('input[name=taxtotal]').attr('readonly', 'readonly');
    			$('select[name=billingcontent]').val('');

    			$('input[name=billingcontent]').removeAttr('disabled').show();
    			$('select[name=billingcontent]').attr('disabled', 'disabled').hide();

    		} else if(invoicetype == 'c_billing'){
    			$('input[name=billingcontent]').closest('td').show();
    			$('input[name=billingcontent]').closest('td').prev().show();

    			// 判断是否有
    			//me.setBillingcontent();
    			//$('input[name=billingcontent]').val('');
    			/*var contractid = $('input[name=contractid]').val();
    			if(contractid > 0) {  //如果有服务合同
    				$('input[name=billingcontent]').attr('readonly', 'readonly');
    				$('input[name=billingcontent]').removeAttr('disabled').show();
    				$('select[name=billingcontent]').attr('disabled', 'disabled').hide();
    			} else {
    				$('select[name=billingcontent]').removeAttr('disabled').show();
    				$('input[name=billingcontent]').attr('disabled', 'disabled').hide();
    			}*/

    			if(record > 0){
    			    //编辑
                    $('input[name=billingcontent]').attr('readonly', 'readonly');
                    $('input[name=billingcontent]').show();
                    $('select[name=billingcontent]').attr('disabled', 'disabled').hide();
                }else{
                    if (me.invoiceRelatesData) {
                        //开票内容关联不到时，可编辑
                        if(me.invoiceRelatesData.billcontent == null || me.invoiceRelatesData.billcontent == ""){
                            $('#Newinvoice_editView_fieldName_billingcontent').removeAttr('readonly');
                            $('select[name=billingcontent]').removeAttr('disabled').show();
                            $('input[name=billingcontent]').attr('disabled', 'disabled').hide();
                        }else{
                            $('#Newinvoice_editView_fieldName_billingcontent').attr('readonly', 'readonly');
                            $('#Newinvoice_editView_fieldName_billingcontent').val(me.invoiceRelatesData.billcontent);
                            $('input[name=billingcontent]').attr('readonly', 'readonly');
                            $('input[name=billingcontent]').removeAttr('disabled').show();
                            $('select[name=billingcontent]').attr('disabled', 'disabled').hide();
                        }

                        /*if($("select[name='invoicecompany']").val() == ""){
                            $("select[name='invoicecompany']").removeAttr('readonly');
                        }else{
                            $("select[name='invoicecompany']").attr('readonly', 'readonly');
                        }*/
                    }else{
                        $('select[name=billingcontent]').removeAttr('disabled').show();
                        $('input[name=billingcontent]').attr('disabled', 'disabled').hide();
                        //开票公司不可编辑
                        //$("input[name='invoicecompany']").removeAttr('readonly');
                    }
                }

				if(!record) {
					$('input[name=taxtotal]').val('').removeAttr('readonly');
				}
    			
    			//$('.newinvoicerayment_div').hide();
    			me.empty_newinvoicerayment();
    		}
		//}

		/*var invoicetype = $('select[name=invoicetype]').val();
		if (invoicetype == 'c_billing') {
			var t = $('input[name=contractid]').val();
			if (this.invoiceRelatesData && t) {
				$('#Newinvoice_editView_fieldName_billingcontent').val(this.invoiceRelatesData.billcontent);
			}
		} else {
			$('#Newinvoice_editView_fieldName_billingcontent').val('');
		}*/
	},
// 设置开票内容
    setBillingcontent: function () {
        var me = this;
        var invoicetype = $('select[name=invoicetype]').val();
        var record = $('input[name=record]').val();

        $("#account_id_display").attr('disabled', 'disabled');

        if(record>0) {

            //开票公司

            me.initInvoicecompanyContent($('select[name=invoicecompany]').val());

            //编辑
            $('input[name=billingcontent]').attr('readonly', 'readonly');
            $('input[name=billingcontent]').show();
            $('select[name=billingcontent]').attr('disabled', 'disabled').hide();
            return;
        }

        //清空财务数据
        $('input[name="taxpayers_no"]').val("");
        $('input[name="registeraddress"]').val("");
        $('input[name="depositbank"]').val("");
        $('input[name="telephone"]').val("");
        $('input[name="accountnumber"]').val("");
        //$('input[name="businessnamesone"]').val("");
        $('input[name="businessnames"]').val("");
        $('input[name="businessnamesnegative"]').val("");
        $('input[name="billingid"]').val("");

        //合同方公司抬头
        $("#Newinvoice_editView_fieldName_account_id_select").parent("span").show();
        $("#Newinvoice_editView_fieldName_account_id_clear").parent("span").show();
        if (invoicetype == 'c_normal') {
            //正常发票
            //合同方公司抬头
            $("#Newinvoice_editView_fieldName_account_id_select").parent("span").hide();
            $("#Newinvoice_editView_fieldName_account_id_clear").parent("span").hide();
            //开票金额
            $('input[name=taxtotal]').attr('readonly', 'readonly');

            $('select[name=billingcontent]').removeAttr('disabled').hide();
            $('input[name=billingcontent]').attr('disabled', 'disabled').show();

            $('input[name=billingcontent]').val('').attr('readonly', 'readonly');
            $('select[name=billingcontent]').val('');
            $('input[name=billingcontent]').removeAttr('disabled').show();
            $('select[name=billingcontent]').attr('disabled', 'disabled').hide();

            if (me.invoiceRelatesData) {
                //开票公司
                me.initInvoicecompanyContent(me.invoiceRelatesData.invoicecompany);
                //开票内容
                $('#Newinvoice_editView_fieldName_billingcontent').val(me.invoiceRelatesData.billcontent);
            }else{

            }
        }else if(invoicetype == 'c_billing'){
            //预发票
            if (me.invoiceRelatesData) {
                $('input[name=billingcontent]').val('');
                $('select[name=billingcontent]').val('');
                $('select[name=billingcontent]').removeAttr('disabled').show();
                $('input[name=billingcontent]').attr('disabled', 'disabled').hide();

                //合同方公司抬头
                if(me.invoiceRelatesData.accountname){
                    $("#Newinvoice_editView_fieldName_account_id_select").parent("span").hide();
                    $("#Newinvoice_editView_fieldName_account_id_clear").parent("span").hide();
                }

                //开票金额
                $('input[name=taxtotal]').attr('readonly', 'readonly');

                //开票公司
                me.initInvoicecompanyContent(me.invoiceRelatesData.invoicecompany);
                $('input[name=taxtotal]').val('').removeAttr('readonly');
                //开票内容关联不到时，可编辑
                if (me.invoiceRelatesData.billcontent == null || me.invoiceRelatesData.billcontent == "") {
                    $('#Newinvoice_editView_fieldName_billingcontent').removeAttr('readonly');
                    $('select[name=billingcontent]').removeAttr('disabled').show();
                    $('input[name=billingcontent]').attr('disabled', 'disabled').hide();
                } else {
                    $('#Newinvoice_editView_fieldName_billingcontent').attr('readonly', 'readonly');
                    $('#Newinvoice_editView_fieldName_billingcontent').val(me.invoiceRelatesData.billcontent);
                    $('input[name=billingcontent]').attr('readonly', 'readonly');
                    $('input[name=billingcontent]').removeAttr('disabled').show();
                    $('select[name=billingcontent]').attr('disabled', 'disabled').hide();
                }
            }

/*            if(record>0) {
                $('input[name=billingcontent]').attr('disabled', 'disabled').show();
                $('select[name=billingcontent]').attr('disabled', 'disabled').hide();
            }*/
            me.empty_newinvoicerayment();
        }

    },
    invoicetypechange: function(){
    	var me = this;
    	$('select[name=invoicetype]').change(function () {
            //清空关联项数据
            $("input[name='contractid_display']").val("");
            $("select[name='invoicecompany']").val("");
            me.empty_relation_data();
            // 清空回款关联
            me.empty_newinvoicerayment();

            me.initInvoicecompanyContent(null);

    		//me.setBillingcontent();
    		me.setBillingcontent();
    	});
    },
    // 关联回款 
    init : function() {
                var contractid = $('[name=contractid]').val();
                if(contractid>0){
                    var contractid_display = $('[name=contractid_display]').val();
                    if(contractid_display == '' || contractid_display ==null){
                        $('[name=contractid_display]').val("担保合同编号");
                    }
                }
		$('select[name=assigned_user_id]').next().find('.chzn-results').remove(); // 填写人员不可修改
		if (rayment_json_data) {
			this.newinvoiceraymentdata = rayment_json_data;
			this.newinvoicerayment_select_html = '';
			this.newinvoicerayment_select_html = '<option value="">请选择</option>';
			for (var i in rayment_json_data) {
				this.newinvoicerayment_select_html += '<option value="'+ i +'">'+ rayment_json_data[i]['paytitle']  +'</option>';
			}
		}
		$('input[name=taxtotal]').val($('input[name=taxtotal]').val().replace(/,/g, ''));
		$('input[name=actualtotal]').val($('input[name=actualtotal]').val().replace(/,/g, ''));
		
		//实际开票金额
		$('input[name=actualtotal]').attr('readonly', 'readonly');
        //实际开票金额
        $('input[name=invoice_num]').attr('readonly', 'readonly');

		$('input[name=contractid_display]').on('keyup', function () {
			var v = $.trim($(this).val());
			if(!v) {
				$('input[name=contractid]').val('');
				$('input[name=billingcontent]').val('');
				
				$('select[name=billingcontent]').removeAttr('disabled').show();
	    		$('input[name=billingcontent]').attr('disabled', 'disabled').hide();
			}
		});


		// 如果是编辑 不能修改发票的基本信息
		
		var record = $('input[name=record]').val();
		var n_modulestatus = $('input[name=n_modulestatus]').val();
		//if(record && n_modulestatus != 'a_exception') {
        if(record) {
		$('select[name=invoicetype]').next().find('.chzn-results').remove(); 
			//$('select[name=invoicecompany]').next().find('.chzn-results').remove();
			$('select[name=taxtype]').next().find('.chzn-results').remove();
			$('textarea[name=businesscontent]').attr('readonly', 'readonly');
			$('input[name=businessnamesone]').attr('readonly', 'readonly');
			$('select[name=billingcontent]').attr('readonly', 'readonly');
			$('input[name=taxtotal]').attr('readonly', 'readonly');

			$('span.add-on').remove();
			//$('.cursorPointer').remove();
		}
		if (record) {
			this.loadWidgetNote();
		}

        var taxtype = $("select[name='taxtype']").val();
        if(taxtype=='electronicinvoice' || taxtype=='invoice'){
            $(".email").show();
            $(".addressee").hide();
            $(".address").hide();
            $(".addresseephone").hide();
        }else{
            $(".email").hide();
            $(".addressee").show();
            $(".address").show();
            $(".addresseephone").show();
        }
	},
	// 关联回款中的 多个回款关联相同回款id 的 发票金额 是否大于 入账金额
	checkInvoicetotalSum : function () {
		var t = {};
		$('.newinvoicerayment_tab').each(function () {
			var  newinvoicerayment_id = parseFloat($(this).find('.t_tab_newinvoicerayment_id').val());
			var  receivedpayments_invoicetotal = parseFloat($(this).find('.receivedpayments_invoicetotal').val());
			if (receivedpayments_invoicetotal) {
				if (! t[newinvoicerayment_id]) {
					t[newinvoicerayment_id] = receivedpayments_invoicetotal;
				} else {
					t[newinvoicerayment_id] += receivedpayments_invoicetotal;
				}
			}
		});
		if (t != {}) {
			for (var i in this.newinvoiceraymentdata) {
				if (t[i]) {
					if (t[i] > parseFloat(this.newinvoiceraymentdata[i]['unit_price'])) {
						return true;
					}
				}
			}
		}
		return false;
	},

	// 发票代码 + 发票号码不能重复
	invoicecodeextendBlur: function () {
		var me = this;
		$(document).on('blur', '.input_invoicecodeextend', function () {
            me.invoice_code_exist_flag = 0;
			var invoicecodeextend = $.trim($(this).val());
			if(invoicecodeextend) {
				var invoice_noextend = $.trim($(this).closest('tr').find('.input_invoice_noextend').val());
				if (invoice_noextend) {
					me.isInvoicecodeextendCheck(invoicecodeextend+invoice_noextend, $(this).data('id'), $(this));
				}
			}
		});

		$(document).on('blur', '.input_invoice_noextend', function () {
            me.invoice_code_exist_flag = 0;
			var invoice_noextend = $.trim($(this).val());
			if(invoice_noextend) {
				var invoicecodeextend = $.trim($(this).closest('tr').find('.input_invoicecodeextend').val());
				if (invoicecodeextend) {
					me.isInvoicecodeextendCheck(invoicecodeextend+invoice_noextend, $(this).data('id'), $(this));
				}
			}
		});

	},

	isInvoicecodeextendCheck : function (str,invoiceextendid, $par) {
        var me = this;
		var module = app.getModuleName();
		var postData = {
			"module": module,
			"action": "BasicAjax",
			'mode': 'isInvoicecodeextendCheck',
			'invoiceextendid': invoiceextendid,
			"s": str,
		}
		AppConnector.request(postData).then(
			function(data){
				if(data.success) {
					if(data.result.flag) {
                        me.invoice_code_exist_flag = 1;
						$par.val('');
						var  params = {text : app.vtranslate(),title : app.vtranslate('发票代码+发票号码有重复')};
		        		Vtiger_Helper_Js.showPnotify(params);
					}
				}
			},
			function(error,err){

			}
		);
	},

    //加载合同关联回款信息 gaocl add 2018/03/27
    loadRelationReceivedPayments:function (servicecontractsid) {
        var me = this;
        var params = {
            'type':'GET',
            'module' : 'Newinvoice',
            'action' : 'BasicAjax',
            'mode':'getRelationReceivedPayments',
            'servicecontractsid':servicecontractsid,
        };
        AppConnector.request(params).then(
            function(data){
                if(data.success){
                    var len = data.result.length;
                    var string =  {text : '无回款可能原因：客户到款未匹配到该合同上或者已经申请过开票',
                        title : '该合同下没有可开发票金额的回款，您可以申请预开票'};
                    if( len<= 0){
                        $('#Newinvoice_editView_fieldName_contractid_clear').trigger('click');
                        Vtiger_Helper_Js.showPnotify(string);
                        return;
                    }
                    $("input[name='invoicedTotal']").val(data.result.invoicedTotal);
                    var allowinvoicetotal = 0;
                    var standardmoney = 0;
                    $.each(data.result.invoicerayment,function(i,val){
                        allowinvoicetotal += val['allowinvoicetotal'];
                        if(i>=len-1&&allowinvoicetotal<=0){
                            $('#Newinvoice_editView_fieldName_contractid_clear').trigger('click');
                            Vtiger_Helper_Js.showPnotify(string);
                            return;
                        }
                        var newinvoiceraymentnum = $('.newinvoicerayment_tab').length + 1;
                        if (newinvoiceraymentnum > 100) {return ;}
                        var nowdnum=$('.newinvoicerayment_tab').last().data('num');
                        if(nowdnum){
                            newinvoiceraymentnum=nowdnum+1;
                        }
                        var t_newinvoicerayment_html = newinvoicerayment_html.replace(/\[\]/g,'['+newinvoiceraymentnum+']');
                        t_newinvoicerayment_html = t_newinvoicerayment_html.replace(/yesreplace/g, newinvoiceraymentnum);
                        t_newinvoicerayment_html = t_newinvoicerayment_html.replace(/newinvoicerayment_select_html/g, this.newinvoicerayment_select_html);
                        $('.newinvoicerayment_div').append(t_newinvoicerayment_html);

                        var $t = $('.newinvoicerayment_div').find('table:last');
                        $t.find('.servicecontractsid_display').val(val['contract_no']);
                        $t.find('.servicecontractsid').val(val['servicecontractsid']);
                        $t.find('.total').val(val['unit_price']);
                        $t.find('.arrivaldate').val(val['reality_date']);
                        $t.find('.allowinvoicetotal').val(val['allowinvoicetotal']);
                        $t.find('.invoicecontent').val(val['billingcontent']);
                        $t.find('.invoicetotal').val(val['allowinvoicetotal']);
                        $t.find('.receivedpaymentsid').val(val['receivedpaymentsid']);
                        $t.find('.receivedpaymentsid_display').val(val['t_paytitle']);
                        $t.find('.invoicecompany').val(val['invoicecompany']);
                        standardmoney = parseFloat(standardmoney) + parseFloat(val['standardmoney']);
                    })
                    $('input[name="standardmoney"]').val(standardmoney);
                    $('.newinvoicerayment_div').show();

                    //合计基本信息栏的开票金额
                    me.calculation_invoicetotal_sum();
                    $(document).on('blur', '.receivedpayments_invoicetotal', function () {
                        me.calculation_invoicetotal_sum();
                    });
                }
            },
            function(){
            }
        )
    },

    /**
     * 发票重新匹配合同
     */
    hasRepeatServiceContracts:function(){
        var me =this;
        var module = app.getModuleName();
        var recordId = $('input[name="record"]').val();
        var postData = {
            "module": module,
            "action": "BasicAjax",
            "record": recordId,
            'mode': 'hasRepeatServiceContracts'

        }
        AppConnector.request(postData).then(
            function(data){
                if (data.success) {
                    if(data.result.change_flag == true){
                       me.repeatServiceContracts();
                    }
                }
            },
            function(error,err){

            }
        );
    },

    /**
     * 发票重新匹配合同
     */
    repeatServiceContracts:function(){

        $('#contractid_display').after('<button id="btn_change_contract" type="button" class="btn btn-info">合同变更</button>');

        $("#btn_change_contract").click( function(e){
            var msg = {
                'message': '请输入重新匹配的合同编号',
                "width":"400px",
            };
            var me = this;
            Vtiger_Helper_Js.showConfirmationBox(msg).then(
                function(e) {
                    var recordId = $('input[name="record"]').val();
                    var service_no = $("#repeat_contract_no").val();

                    if($.trim(service_no) == '') {
                        Vtiger_Helper_Js.showPnotify(app.vtranslate('合同编号不能为空'));
                        return ;
                    }
                    var contractid_display = $("#contractid_display").val();
                    if($.trim(service_no) == $.trim(contractid_display)){
                        Vtiger_Helper_Js.showPnotify(app.vtranslate('输入的合同编号和当前合同编号重复'));
                        return ;
                    }

                    var module = app.getModuleName();
                    var postData = {
                        "module": module,
                        "action": "BasicAjax",
                        "record": recordId,
                        'service_no': service_no,
                        'mode': 'repeatServiceContracts'

                    }

                    var Message = "正在处理中,请稍等...";

                    var progressIndicatorElement = jQuery.progressIndicator({
                        'message' : Message,
                        'position' : 'html',
                        'blockInfo' : {'enabled' : true}
                    });
                    AppConnector.request(postData).then(
                        function(data){
                            // 隐藏遮罩层
                            progressIndicatorElement.progressIndicator({
                                'mode' : 'hide'
                            });
                            if (data.success) {
                                //$t_tr.find('.allowinvoicetotal_value').html(selectValue);
                                //$(me).data('status', selectValue);
                                if(data.result.msg != ""){
                                    Vtiger_Helper_Js.showPnotify(app.vtranslate(data.result.msg));
                                }else{
                                    $("#contractid_display").val(service_no);
                                    $("input[name='contractid']").val(data.result.servicecontractsid);
                                    $("#account_id_display").val(data.result.accountname);
                                    $("input[name='invoicecompany']").val(data.result.accountname);
                                    $("input[name='account_id']").val(data.result.accountid);
                                    $("input[name='invoicecompany']").val(data.result.invoicecompany);
                                    $("#Newinvoice_editView_fieldName_billingcontent").val(data.result.billcontent);

                                    $("#Newinvoice_editView_fieldName_taxtotal").removeAttr('disabled').removeAttr('readonly');
                                    $("#Newinvoice_editView_fieldName_actualtotal").removeAttr('disabled').removeAttr('readonly');
                                    if(data.result.billcontent == ""){
                                        $("#Newinvoice_editView_fieldName_billingcontent").removeAttr('disabled').removeAttr('readonly');
                                    }
                                }
                            }
                        },
                        function(error,err){

                        }
                    );
                },function(error, err){}
            );

            $('.modal-body').append('<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">合同编号:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><input value="" id="repeat_contract_no"></span></div></td></tr></tbody></table>');
        });
    },

    //初始化开票公司
    initInvoicecompanyContent:function (invoicecompany) {
        var that =this;
        var invoicecompany_list = that.invoicecompany_list;
        if (invoicecompany == null && (invoicecompany_list == null || invoicecompany_list.length == 0)){
            $("select[name='invoicecompany']").find("option").each(function () {
                var v = $(this).val();
                if(v){
                    invoicecompany_list.push(v)
                }
            })
            return;
        }

        $("select[name='invoicecompany']").empty();
        var optionStr = "";
        if(invoicecompany){
            optionStr = '<option value="'+invoicecompany+'">'+invoicecompany+'</option>';
        }else{
            if(invoicecompany_list.length >0) {
                optionStr += '<option value="">选择一个选项</option>';
                for (var i = 0; i < invoicecompany_list.length; i++) {
                    optionStr += '<option value="' + invoicecompany_list[i] + '">' + invoicecompany_list[i] + '</option>';
                }
            }
        }
        $("select[name='invoicecompany']").append(optionStr);
        $("select[name='invoicecompany']").trigger('liszt:updated');
    },
    toAccountDetail:function() {
        $("#toAccountDetail").click(function () {
            var account_id = $('input[name="account_id"]').val();
            if(!account_id){
                var params = {
                    title: '',
                    text: '无合同方公司抬头，不可跳转',
                };
                Vtiger_Helper_Js.showPnotify(params);
                return;
            }

            window.open("?module=Accounts&relatedModule=Billing&view=Detail&record="+account_id+"&mode=showRelatedList&tab_label=Billing",'_blank');
        });
    },

    getuploadZzFile:function(){
        if($('#zizhifile').length>0){
            var module=$('#module').val();
            KindEditor.ready(function(K) {
                var uploadbutton = K.uploadbutton({
                    button : K('#uploadzizhiButton')[0],
                    fieldName : 'Zizhifile',
                    extraParams :{
                        __vtrftk:$('input[name="__vtrftk"]').val(),
                        record:$('input[name="record"]').val()
                    },
                    url : 'index.php?module='+module+'&action=FileUpload&record='+$('input[name="record"]').val(),
                    afterUpload : function(data) {
                        if (data.success ==true) {
                            $('.zizhifiledelete').remove();
                            var str='<span class="label file'+data.result['id']+'" style="margin-left:5px;">'+data.result['name']+'&nbsp;<b class="deletefile" data-class="file'+data.result['id']+'" data-id="'+data.result['id']+'" title="删除文件" style="display:inline-block;width:12px;height:12px;line-height:12px;text-align:center">x</b>&nbsp;</span><input class="ke-input-text file'+data.result['id']+'" type="hidden" name="zizhifile['+data.result['id']+']" id="zizhifile" value="'+data.result['name']+'" readonly="readonly" /><input class="file'+data.result['id']+'" type="hidden" name="attachmentsid['+data.result['id']+']" value="'+data.result['id']+'">';
                            if($("#fileallzizhi").find('.deletefile').length>0){
                                $("#fileallzizhi").append(str);
                            }else{
                                $("#fileallzizhi").html(str);
                            }
                        } else {
                            Vtiger_Helper_Js.showMessage({type:'error',text:data.msg});
                            e.preventDefault();
                            return false;
                        }
                    },
                    afterError : function(str) {
                    }
                });
                uploadbutton.fileBox.change(function(e) {
                    uploadbutton.submit();
                });
                $('.fileUploadContainer').find('form').css({width:"54px"});
                $('.fileUploadContainer').find('form').find('.btn-info').css({width:"54px",marginLeft:"-15px"});
            });
        }
    },
    getuploadInvoiceFile:function(){
        if($('#invoicefile').length>0){
            var module=$('#module').val();
            KindEditor.ready(function(K) {
                var uploadbutton = K.uploadbutton({
                    button : K('#uploadinvoiceButton')[0],
                    fieldName : 'Invoicefile',
                    extraParams :{
                        __vtrftk:$('input[name="__vtrftk"]').val(),
                        record:$('input[name="record"]').val()
                    },
                    url : 'index.php?module='+module+'&action=FileUpload&record='+$('input[name="record"]').val(),
                    afterUpload : function(data) {
                        if (data.success ==true) {
                            $('.invoicefiledelete').remove();
                            var str='<span class="label file'+data.result['id']+'" style="margin-left:5px;">'+data.result['name']+'&nbsp;<b class="deletefile" data-class="file'+data.result['id']+'" data-id="'+data.result['id']+'" title="删除文件" style="display:inline-block;width:12px;height:12px;line-height:12px;text-align:center">x</b>&nbsp;</span><input class="ke-input-text file'+data.result['id']+'" type="hidden" name="invoicefile['+data.result['id']+']" id="invoicefile" value="'+data.result['name']+'" readonly="readonly" /><input class="file'+data.result['id']+'" type="hidden" name="attachmentsid['+data.result['id']+']" value="'+data.result['id']+'">';
                            // if($("#fileallinvoice").find('.deletefile').length>0){
                            //     $("#fileallinvoice").append(str);
                            // }else{
                                $("#fileallinvoice").html(str);
                            // }
                        } else {
                            Vtiger_Helper_Js.showMessage({type:'error',text:data.msg});
                            e.preventDefault();
                            return false;
                        }
                    },
                    afterError : function(str) {
                    }
                });
                uploadbutton.fileBox.change(function(e) {
                    uploadbutton.submit();
                });
                $('.fileUploadContainer').find('form').css({width:"54px"});
                $('.fileUploadContainer').find('form').find('.btn-info').css({width:"54px",marginLeft:"-15px"});


            });
        }
    },
    /**
     * 删除上传的文件
     */
    deleteuploadZzFile:function(){
        $('form').on('mouseover','.deletefile',function(){
            $(this).css({color:"#666",cursor:"pointer",border:"#666 solid 1px",borderRadius:"12px"});
        }).on('mouseout','.deletefile',function(){
            $(this).css({color:"#fff",border:"none",borderRadius:"none"});
        }).on('click','.deletefile',function(){
            var delclassid=$(this).data('id');

            var module=$('#module').val();
            var url='index.php?module='+module+'&action=DeleteFile&id='+delclassid+'&record='+$('input[name="record"]').val();
            AppConnector.request(url).then(
                function(data){
                    if(data['success']) {
                        $('.file'+delclassid).remove();
                    } else {
                        //aDeferred.reject(data['message']);
                    }
                },
                function(error){
                    //aDeferred.reject();
                }
            )
        });

    },
    /**
     * 删除上传的文件
     */
    deleteuploadInvoiceFile:function(){
        $('form').on('mouseover','.deletefile',function(){
            $(this).css({color:"#666",cursor:"pointer",border:"#666 solid 1px",borderRadius:"12px"});
        }).on('mouseout','.deletefile',function(){
            $(this).css({color:"#fff",border:"none",borderRadius:"none"});
        }).on('click','.deletefile',function(){
            var delclassid=$(this).data('id');

            var module=$('#module').val();
            var url='index.php?module='+module+'&action=DeleteFile&id='+delclassid+'&record='+$('input[name="record"]').val();
            AppConnector.request(url).then(
                function(data){
                    if(data['success']) {
                        $('.file'+delclassid).remove();
                    } else {
                        //aDeferred.reject(data['message']);
                    }
                },
                function(error){
                    //aDeferred.reject();
                }
            )
        });

    },
    displayZzHtml:function(){
        $('#uploadzizhiButton').closest('tr').before('<tr class="zhizi_search">\n' +
            '\t<td class="fieldLabel medium">\n' +
            '\t\t<label class="muted pull-right marginRight10px">纳税资质查询</label>\n' +
            '\t</td>\n' +
            '\t<td class="fieldValue medium" colspan="3">\n' +
            '\t\t<div class="row-fluid">\n' +
            '\t\t\t<span class="span10">\n' +
            '\t\t\t\t<a target="_blank" href="http://www.yibannashuiren.com/" style="color:#08c;margin-right:30px">http://www.yibannashuiren.com/</a>请查询购方是否具备一般纳税人资格，若具备，可选择开具增值税专用发票，并把查询的结果截图上传至附件\n' +
            '\t\t\t</span>\n' +
            '\t\t</div>\n' +
            '\t</td>\n' +
            '</tr>');
        var taxtype =$("input[name='taxtype']").val()
        if(taxtype == 'invoice'){
            $(".zhizi_search").hide();
        }
    },

    /**
     * 开票数据来源
     */
    billingSourceDataChange:function(){
        var thisInstance = this;
        $("select[name='billingsourcedata']").change(function () {
            if($(this).val()=='contractsource'){
                //根据合同开票
                $("select[name='ordersystem']").closest('td').prev().hide();
                $("select[name='ordersystem']").closest('td').hide();
                $("select[name='headuptype']").closest('td').prev().hide();
                $("select[name='headuptype']").closest('td').hide();
                $("input[name='systemuser']").closest('td').prev().hide();
                $("input[name='systemuser']").closest('td').hide();
                $("input[name='systemuser']").attr("disabled",true);
                $("select[name='headuptype']").attr("disabled",true);
                $("select[name='ordersystem']").attr("disabled",true);
                $("select[name='invoicetype']").closest('td').prev().show();
                $("select[name='invoicetype']").closest('td').show();
                $("input[name='contractid']").closest('td').prev().show();
                $("input[name='contractid']").closest('td').show();
                $("input[name='account_id']").closest('td').prev().show();
                $("input[name='account_id']").closest('td').show();
                $("select[name='invoicetype']").attr("disabled",false);
                $("input[name='contractid']").attr("disabled",false);
                $("input[name='account_id']").attr("disabled",false);
                //如果是合同渠道，发票可填
                $("input[name='registeraddress']").attr("readonly",true);
                $("input[name='depositbank']").attr("readonly",true);
                $("input[name='telephone']").attr("readonly",true);
                $("input[name='accountnumber']").attr("readonly",true);
                $(".linkedOrder_div").html('');
            }else if($(this).val()=='ordersource'){
                //根据订单开票
                $("select[name='ordersystem']").closest('td').prev().show();
                $("select[name='ordersystem']").closest('td').show();
                $("select[name='headuptype']").closest('td').prev().show();
                $("select[name='headuptype']").closest('td').show();
                $("input[name='systemuser']").closest('td').prev().show();
                $("input[name='systemuser']").closest('td').show();
                $("input[name='systemuser']").attr("disabled",false);
                $("select[name='headuptype']").attr("disabled",false);
                $("select[name='ordersystem']").attr("disabled",false);
                $("select[name='invoicetype']").closest('td').prev().hide();
                $("select[name='invoicetype']").closest('td').hide();
                $("input[name='contractid']").closest('td').prev().hide();
                $("input[name='contractid']").closest('td').hide();
                $("input[name='account_id']").closest('td').prev().hide();
                $("input[name='account_id']").closest('td').hide();
                $("select[name='invoicetype']").attr("disabled",true);
                $("input[name='contractid']").attr("disabled",true);
                $("input[name='account_id']").attr("disabled",true);
                //如果是订单渠道的公司，发票可填
                if(!recordId){
                    $("input[name='registeraddress']").attr("readonly",false);
                    $("input[name='depositbank']").attr("readonly",false);
                    $("input[name='telephone']").attr("readonly",false);
                    $("input[name='accountnumber']").attr("readonly",false);
                }
                //自动计算
                $("input[name='taxtotal']").attr("readonly",true);
                $(".newinvoicerayment_div").html('');
                thisInstance.calculateAmountByOrder();
            }
        });
        var recordId = $('input[name="record"]').val();
        if(!recordId){
            $("select[name='billingsourcedata']").val('contractsource');
            $("select[name='billingsourcedata']").trigger('change');
            $("select[name='billingsourcedata']").trigger('liszt:updated');
        }else{
            $("select[name='billingsourcedata']").trigger('change');
            $("select[name='billingsourcedata']").trigger('liszt:updated');
            //编辑时订单渠道的选项固定
            $('select[name=billingsourcedata]').next().find('.chzn-results').remove();
            $('select[name=ordersystem]').next().find('.chzn-results').remove();
            $('select[name=headuptype]').next().find('.chzn-results').remove();
            $('#systemuser_display').val($("input[name='systemuser']").val());
        }
    },

    /**
     * 点击用户账户
     */
    registerSystemUserPop:function(){
        var thisInstance = this;
        $("#EditView").on("click",'.relatedPopupUser',function(e){
            var ordersystem = $('select[name="ordersystem"]').val();
            if(typeof(ordersystem) == "undefined" || ordersystem == "" || ordersystem == 'undefined'){
                Vtiger_Helper_Js.showMessage({type:'error',text:'请先指定订单系统!'});
                return;
            }
            thisInstance.openSystemUserPopUp(e);
        });
        $('input[name="systemuser"]').on(Vtiger_Edit_Js.referenceSelectionEvent, function (e,data){
            //用户账号选择事件
            var systemuser_id=$('#systemuser_id').val();
            if(systemuser_id){
                var module=$('#module').val();
                var postData = {
                    "module": module,
                    "action": "BasicAjax",
                    "systemuser_id": systemuser_id,
                    'mode': 'getSystemUserOrder'
                }
                AppConnector.request(postData).then(
                    function(data){
                        $(".linkedOrder_div").html('');
                        if (data['success']&&data['result']['data']) {
                            var html='';
                            for (var i in data['result']['data']) {
                                i=parseInt(i);
                                html+='<table class="table table-bordered blockContainer linkedOrder_tab detailview-table" data-num="'+(i+1)+'">\n' +
                                    '\t<thead>\n' +
                                    '\t\t<tr>\n' +
                                    '\t\t\t<th class="blockHeader" colspan="4">\n' +
                                    '\t\t\t\t\t&nbsp;&nbsp;订单信息'+(i+1)+'\n' +
                                    '\t\t\t\t<b class="pull-right"><button class="btn btn-small delButtonOrder" type="button"><i class="icon-trash" title="删除订单信息"></i></button></b>\n' +
                                    '\t\t\t</th>\n' +
                                    '\t\t</tr>\n' +
                                    '\t</thead>\n' +
                                    '\t<tbody>\n' +
                                    '\t\t<tr>\n' +
                                    '\t\t\t<td class="fieldLabel medium">\n' +
                                    '\t\t\t\t<label class="muted pull-right marginRight10px"><span class="redColor">*</span>订单编号</label>\n' +
                                    '\t\t\t</td>\n' +
                                    '\t\t\t<td class="fieldValue medium">\n' +
                                    '\t\t\t\t<div class="row-fluid">\n' +
                                    '\t\t\t\t\t<span class="span10"><input type="text" name="orderNo['+i+']"  class="input-large" readonly value="'+data['result']['data'][i]['OrderCode']+'"></span>\n' +
                                    '\t\t\t\t</div>\n' +
                                    '\t\t\t</td>\n' +
                                    '\t\t\t<td class="fieldLabel medium">\n' +
                                    '\t\t\t\t<label class="muted pull-right marginRight10px"><span class="redColor">*</span>订单支付时间</label>\n' +
                                    '\t\t\t</td>\n' +
                                    '\t\t\t<td class="fieldValue medium">\n' +
                                    '\t\t\t\t<div class="row-fluid">\n' +
                                    '\t\t\t\t\t<span class="span10"><input type="text" class="input-large" readonly value="'+data['result']['data'][i]['AddDate']+'"></span>\n' +
                                    '\t\t\t\t</div>\n' +
                                    '\t\t\t</td>\n' +
                                    '\t\t</tr>\n' +
                                    '\t\t<tr>\n' +
                                    '\t\t\t<td class="fieldLabel medium">\n' +
                                    '\t\t\t\t<label class="muted pull-right marginRight10px"><span class="redColor">*</span>商品名称</label>\n' +
                                    '\t\t\t</td>\n' +
                                    '\t\t\t<td class="fieldValue medium">\n' +
                                    '\t\t\t\t<div class="row-fluid">\n' +
                                    '\t\t\t\t\t<span class="span10"><input type="text" class="input-large" readonly value="'+data['result']['data'][i]['ProductTitle']+'"></span>\n' +
                                    '\t\t\t\t</div>\n' +
                                    '\t\t\t</td>\n' +
                                    '\t\t\t<td class="fieldLabel medium">\n' +
                                    '\t\t\t\t<label class="muted pull-right marginRight10px"><span class="redColor">*</span>金额</label>\n' +
                                    '\t\t\t</td>\n' +
                                    '\t\t\t<td class="fieldValue medium">\n' +
                                    '\t\t\t\t<div class="row-fluid">\n' +
                                    '\t\t\t\t\t<span class="span10"><input type="text" class="input-large" id="orderMoney['+i+']" readonly value="'+data['result']['data'][i]['Money']+'"></span>\n' +
                                    '\t\t\t\t</div>\n' +
                                    '\t\t\t</td>\n' +
                                    '\t\t</tr>\n' +
                                    '\t</tbody>\n' +
                                    '</table>';
                            }
                            $(".linkedOrder_div").html(html);
                            thisInstance.calculateAmountByOrder();
                        }
                    },
                    function(error,err){

                    }
                );
            }else{
                Vtiger_Helper_Js.showMessage({type:'error',text:'请先选择系统用户!'});
                return;
            }
        });
        $("#EditView").on("click",".delButtonOrder",function (e) {
            var dongchaliorderid=$(this).data('id');
            var me=this;
            //删除订单信息
            if($("input[name='record']").val()){
                //编辑时解除关联
                var msg = {
                    'message': "确认要解除关联吗",
                    "width":"400px",
                };
                Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e) {
                    if($(".linkedOrder_div").find('.delButtonOrder').length<=1){
                        //订单开票渠道必须有订单
                        var  params = {text : app.vtranslate(),title : app.vtranslate('最少必须有一张订单关联')};
                        Vtiger_Helper_Js.showPnotify(params);
                        return false;
                    }
                    var module = app.getModuleName();
                    var postData = {
                        "module": module,
                        "action": "BasicAjax",
                        "record": $("input[name='record']").val(),
                        "dongchaliorderid": dongchaliorderid,
                        "mode": 'disassociateDongchaliOrder'
                    }
                    var Message = "正在处理中,请稍等...";

                    var progressIndicatorElement = jQuery.progressIndicator({
                        'message' : Message,
                        'position' : 'html',
                        'blockInfo' : {'enabled' : true}
                    });
                    AppConnector.request(postData).then(
                        function(data){
                            // 隐藏遮罩层
                            progressIndicatorElement.progressIndicator({
                                'mode' : 'hide'
                            });
                            if (data.success) {
                                $(me).closest('table').remove();
                                thisInstance.calculateAmountByOrder();
                            }
                        },
                        function(error,err){

                        }
                    );
                });
            }else{
                $(this).closest('table').remove();
            }
            thisInstance.calculateAmountByOrder();
        });
        $("#EditView").on("change","select[name='headuptype']",function (e) {
            //个人只能开普通发票
            $("select[name='taxtype']").attr("readonly",true);
            if($(this).val()=='personheadup'){
                $("select[name='taxtype']").val('generalinvoice');
                $("select[name='taxtype']").trigger('change');
                $("select[name='taxtype']").trigger('liszt:updated');
            }
        });
    },

    /**
     * 计算获取洞察力订单后金额
     */
    calculateAmountByOrder:function(){
        var thisInstance = this;
        var orderMoney=0;
        $('input[id^="orderMoney"]').each(function () {
            console.log($(this).val());
            orderMoney=thisInstance.accAdd(orderMoney,$(this).val());
        });
        $("input[name='taxtotal']").val(orderMoney);
    },

    //洞察力登录名获取
    openSystemUserPopUp : function(e){
        var thisInstance = this;
        var parentElem = jQuery(e.target).closest('td');
        var params = this.getPopUpParams(parentElem);
        params.src_record=$('input[name="record"]').val();
        params.module='Newinvoice';
        var isMultiple = false;
        if(params.multi_select) {
            isMultiple = true;
        }
        params.ordersystem= $('select[name="ordersystem"]').val();
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
                    'name' : responseData[id]['info']['loginName'],
                    'id' : responseData[id]['info']['loginName']
                }
                dataList.push(data);
                $('#systemuser_id').val(responseData[id]['info']['id']);
                if(!isMultiple) {
                    thisInstance.setReferenceFieldValue(parentElem, data);
                }else{
                    idList['id'].push(id);
                    idList['name'].push(responseData[id]['info']['loginName']);
                }
            }

            if(isMultiple) {
                thisInstance.setMultiReferenceFieldValue(parentElem, idList);
                //sourceFieldElement.trigger(Vtiger_Edit_Js.refrenceMultiSelectionEvent,{'data':dataList});
            }
            sourceFieldElement.trigger(Vtiger_Edit_Js.postReferenceSelectionEvent,{'data':responseData});
        });
    },

    taxtypeChange:function(){
      $("select[name='taxtype']").on("change",function () {
          var taxtype = $(this).val();
          if(taxtype=='electronicinvoice' || taxtype=='invoice'){
            $(".email").show();
            $(".addressee").hide();
            $(".address").hide();
            $(".addresseephone").hide();
            $(".invoice_fee").hide();
          }else{
              $(".email").hide();
              $(".addressee").show();
              $(".address").show();
              $(".addresseephone").show();
              $(".invoice_fee").hide();
          }
          if(taxtype=='invoice'){
              $(".invoice_fee").show();
              var contractid_display = $("#contractid_display").val();
              if((contractid_display.indexOf("GG") == '-1') && (contractid_display.indexOf("GOOGLE") == '-1') && (contractid_display.indexOf("YANDEX") == '-1')){
                  var params = {text: app.vtranslate(), title: app.vtranslate('服务合同选择错误！')};
                  Vtiger_Helper_Js.showPnotify(params);
                  return false;
              }
              var invoicecompany =$("select[name='invoicecompany']").val();

              if(invoicecompany != '凯丽隆国际控股（香港）有限公司' && invoicecompany != 'AMERICAN KAILILONG INTERNATIONAL HOLDING (H.K.) LIMITED'){
                  var params = {text: app.vtranslate(), title: app.vtranslate('开票公司选择错误！')};
                  Vtiger_Helper_Js.showPnotify(params);
                  return false;
              }

              var standardmoney = $('input[name="standardmoney"]').val();
              $('input[name=taxtotal]').val(standardmoney);

              $('.add-on').each(function(){
                  if($(this).text() == '¥'){
                      $(this).text('$');
                  }
              });

          }else{

              var taxtotal_money = $('input[name="taxtotal_money"]').val();
              $('input[name=taxtotal]').val(taxtotal_money);
              $('.add-on').each(function(){
                  if($(this).text() == '$'){
                      $(this).text('¥');
                  }
              });

          }
      })
    },

    electronicinvoiceChange:function(){
        $("select[name='isaccountinvoice']").on("change",function () {
            var isaccountinvoice = $(this).val();
            var taxtype = $("select[name='taxtype']").val();
            if(isaccountinvoice=='noneed' && taxtype=='invoice'){
                $(".email").show();
                $(".invoicefile").hide();
            }else if(taxtype=='electronicinvoice'){
                $(".email").show();
            }else{
                $(".email").hide();
                $(".invoicefile").show();
            }
        })
    },
	registerEvents: function(){
		this._super();
		this.registerReferenceSelectionEvent($("#EditView"));
		this.taxtypechange();
        this.electronicinvoiceChange();
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
	    this.addNewinvoicerayment();
        this.deleteinvoice();
        this.inputnumberextend();
        this.amountofmoneyextend();
        this.checkinvoiceextend();
        //this.addMinus();
        this.invoicetypechange();
        this.init();
        //this.select_newinvoicerayment();
        this.delbuttonnewinvoicerayment();
        this.setBillingcontent();
        this.test();
        this.invoicecodeextendBlur();
       // this.setBillingcontent();

        //this.hasRepeatServiceContracts();

        this.initInvoicecompanyContent(null);
        this.toAccountDetail();
        this.getuploadZzFile();
        this.deleteuploadZzFile();
        this.getuploadInvoiceFile();
        this.deleteuploadInvoiceFile();
        this.displayZzHtml();
        this.billingSourceDataChange();
        this.registerSystemUserPop();
                this.taxtypeChange();
    }

});


