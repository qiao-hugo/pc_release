/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("ServiceAssignRule_Edit_Js", {} ,{
	
	/*
	 * 分配类型设置
	 */
	registerAssignTypeChangeEvent : function() {
		jQuery('select[name="assigntype"]', this.getForm()).on('change', function(e){
			var assigntypeElement = jQuery(e.currentTarget);
			if (assigntypeElement.val() == 'productby'){
				//按产品分配
				//jQuery('#blockContainer_LBL_ACCOUNT_ASSIGN').addClass('hide').addClass('tableadv');
				jQuery('#blockContainer_LBL_PRODUCT_ASSIGN').removeClass('hide').removeClass('tableadv');
			}else if (assigntypeElement.val() == 'accountby'){
				//按客户分配
				//jQuery('#blockContainer_LBL_ACCOUNT_ASSIGN').removeClass('hide').removeClass('tableadv');
				jQuery('#blockContainer_LBL_PRODUCT_ASSIGN').addClass('hide').addClass('tableadv');
			}else{
				//默认按产品
				//jQuery('#blockContainer_LBL_ACCOUNT_ASSIGN').addClass('hide').addClass('tableadv');
				jQuery('#blockContainer_LBL_PRODUCT_ASSIGN').removeClass('hide').removeClass('tableadv');
			}
		});
	},
	
	/*
	 * 客服选择处理
	 */
	registerServiceChangeEvent : function() {
		var intace=this;
		jQuery('select[name="serviceid"]', this.getForm()).on('change', function(e){
			var serviceidElement = jQuery(e.currentTarget);

			jQuery('select[name="oldserviceid"]').find("option[value='+serviceidElement.val()+']").attr("selected",true);
			//jQuery('select[name="oldserviceid"]').attr("value",serviceidElement.val());
			//客服分配信息取得
			intace.getServiceAssignInfo(serviceidElement.val());
		});
	},
	
	/*
	 * 部门选择处理
	 */
	registerDepartmentChangeEvent : function() {
		jQuery('select[name="departmentid"]', this.getForm()).on('change', function(e){
			var assigntypeElement = jQuery(e.currentTarget);
			//app.destroyChosenElement(jQuery('.showInlineTable'));
			/*jQuery('#select_ServiceAssignRule_ownerid_chzn').find('.chzn-results').children().filter('li').remove();
			jQuery('#select_ServiceAssignRule_ownerid_chzn').find('.chzn-choices li').remove('li[class=search-choice]');*/
			jQuery('[name="ownerid[]"]').val('')
			//选择的部门
			var curdepartmentid = assigntypeElement.val();
			var params = {
					'module' : app.getModuleName(),
					'action' : 'SubRule',
					'mode':'getUserInfosByDepartment',
					'departmentid':curdepartmentid,
					'checkflg':'1'
			};
			
			//发送请求
			AppConnector.request(params).then(
				function(data){
					if(data.success && data.result) {
						var rowindex=0;
						var option = [];
						for(var key in data.result){
							/*var limsg = '<li id="select_ServiceAssignRule_ownerid_chzn_o_'+ (rowindex*1+1) +'" class="active-result group-option" style="">'+ data.result[key] + '</li>';
							jQuery('#select_ServiceAssignRule_ownerid_chzn').find('.chzn-results').append(limsg);*/
							option.push('<option value="'+ key +'" data-picklistvalue="'+data.result[key]+'" data-userid="1">'+ data.result[key] +'</option>');
							rowindex++;
						}
						jQuery('#select_ServiceAssignRule_ownerid').html(option.join(''));
						jQuery('#select_ServiceAssignRule_ownerid').trigger('liszt:updated');
					}	
					//刷新页面
					//window.location.reload();
				},
				function(error){
				}
			);
			
		});
	},

 	isSubmit : false,
	registerRecordPreSaveEvent : function() {
		var thisInstance = this;
		jQuery('#EditView').on(Vtiger_Edit_Js.recordPreSave, function(e, data) {
			var assigntype = jQuery('select[name="assigntype"]').val();
			if(assigntype == 'productby') {
				var productid = jQuery('select[name="productid"]').val();
				if (productid ==''){
					var message = app.vtranslate('JS_SELECT_PRODUCT_MESSAGE');
					var  params = {text : message, title :'提示：'}
					Vtiger_Helper_Js.showPnotify(params);
				}else{
					jQuery('#EditView').submit();
					return true;
				}
			}
			if(assigntype == 'accountby') {
				/*var ownerid = jQuery('select[name="ownerid[]"]').val();
				if (ownerid ==null){
					var message = app.vtranslate('JS_SELECT_OWNERID_MESSAGE');
					var  params = {text : message, title :'提示：'}
					Vtiger_Helper_Js.showPnotify(params);
				}else{*/
					jQuery('#EditView').submit();
					return true;
//				}
			}
          e.preventDefault();
		})
	},
	
	/**
	 * 获取客户信息
	 */
	registerGetAccountsClickEvent:function(){
		var intace=this;
		jQuery('#btnGetAccounts').on('click',function(){
			var assigntype=jQuery('select[name="assigntype"]').val();
			var departmentid=jQuery('select[name="departmentid"]').val();
			var productid=jQuery('select[name="productid"]').val();
			var ownerid=jQuery('[name="ownerid[]"]').val();
			var serviceid=jQuery('select[name="serviceid"]').val();
			var oldserviceid=jQuery('select[name="oldserviceid"]').val();
			var accountid=jQuery('input[name="related_to"]').val();
            var accountrank=jQuery('select[name="accountrank"]').val();
			//不包含已分配客户
			var notAssignCheckBox=jQuery('#notAssignCheckBox').attr("checked");
			if (notAssignCheckBox=='checked'){
				notAssignCheckBox=1;
			}else{
				notAssignCheckBox=0;
			}
			//删除数据
			//jQuery("#tbl_ServiceAssignRule_Account_Detail tbody tr").remove();
			 
//			if (ownerid == null || ownerid==''){
//				var message = app.vtranslate('JS_SELECT_OWNERID_MESSAGE');
//				var  params = {text : message, title :'提示：'}
//				Vtiger_Helper_Js.showPnotify(params);
//				jQuery('.msg').html('');  
//				return;
//			}
			var params = {
					'type':'GET',
					'module' : app.getModuleName(),
					'action' : 'SubRule',
					'mode':'getAccountInfos',
					'assigntype':assigntype,
					'departmentid':departmentid,
					'productid':productid,
					'ownerid':ownerid,
					'serviceid':serviceid,
					'oldserviceid':oldserviceid,
					'accountid':accountid,
                    'accountrank':accountrank,
					'notAssignCheckBox':notAssignCheckBox,
					'checkflg':'1'
			};
			/*var params = {
					'type':'GET',
					'url':'module='+app.getModuleName()+'&action=SubRule&mode=getAccountInfos&assigntype='+assigntype+'&departmentid='+departmentid+'&productid='+productid+'&ownerid='+ownerid+'&serviceid='+serviceid+'&notAssignCheckBox='+notAssignCheckBox,
					'data':''
			};*/

            var progressIndicatorElement = jQuery.progressIndicator({ 'message' : '正在获取客户信息中，请稍等...','blockInfo':{'enabled':true }});
            jQuery('.msg').html('');
			//jQuery('.msg').html('<font color=red>正在获取客户信息中，请稍等......</font>');
			//发送请求
			$('#div_account_detail').html('<table id="tbl_ServiceAssignRule_Account_Detail" class="table listViewEntriesTable" >'+
				    '<thead><tr><th class="{$WIDTHTYPE}"><input type="checkbox" id="listViewEntriesMainCheckBox" />'+
					'</th><th><b>客户</b></th><th><b>客户等级</b></th><th><b>部门</b></th><th><b>负责人</b></th><th><b>客服</b></th></tr></thead><tbody></tbody></table>');
			//$('#pagination').html('<ul class="pagination-demo"></ul>');
			
			AppConnector.request(params).then(
				function(data){
					if(data != null && data.success && data.result) {
						if (data.result.length==0){
							jQuery('.msg').html('');  
							var message = app.vtranslate('JS_ACCOUNTS_NO_DATA_MESSAGE');
							var  params = {text : message, title :'提示：'}
							Vtiger_Helper_Js.showPnotify(params);
							return;
						}
						
						var rowindex=0;
//						var tablehtml='<table id="tbl_ServiceAssignRule_Account_Detail" class="table listViewEntriesTable" >'+
//									'<thead><tr><th class="{$WIDTHTYPE}"><input type="checkbox" id="listViewEntriesMainCheckBox" />'+
//									'</th><th><b>客户</b></th><th><b>客户等级</b></th><th><b>部门</b></th><th><b>负责人</b></th><th><b>客服</b></th></tr></thead><tbody></tbody></table>';
						for(var key in data.result){
							var accountid=data.result[key]['accountid'];
							var accountname=data.result[key]['accountname'];
							var smownername=data.result[key]['smownername'];
							var accountrank=data.result[key]['accountrank'];
							var servicename=data.result[key]['servicename'];
							var departmentname=data.result[key]['departmentname'];
							var newRow = '<tr><td><input type="checkbox" value="'+accountid+'" class="listViewEntriesCheckBox"/></td><td>'+accountname+'</td><td>'+accountrank+'</td><td>'+departmentname+'</td><td>'+smownername+'</td><td>'+servicename+'</td></tr>';
							$("#tbl_ServiceAssignRule_Account_Detail tbody").append(newRow);
						}
						intace.Tableinstance();
						jQuery('.msg').html('');
					}else{
						jQuery('.msg').html('<font color=red>没有要获取的客户信息!</font>');
					}
                    progressIndicatorElement.progressIndicator({ 'mode' : 'hide'});
					//刷新页面
					//window.location.reload();
				},
				function(error){
					jQuery('.msg').html('<font color=red>获取客户信息失败!</font>');
				}
			);
		})	
	},
	
	/**
	 * 分配客服
	 */
	registerAssignClickEvent:function(){
		var intace=this;
		jQuery('#btnAssign').on('click',function(){
			var assigntype=jQuery('select[name="assigntype"]').val();
			var departmentid=jQuery('select[name="departmentid"]').val();
			var productid=jQuery('select[name="productid"]').val();
			var ownerid=jQuery('[name="ownerid[]"]').val();
			var serviceid=jQuery('select[name="serviceid"]').val();
			var oldserviceid=jQuery('select[name="oldserviceid"]').val();
			var accountid=jQuery('input[name="related_to"]').val();

			//1:全部分配，0：选择分配
			var assignRadiovalue=jQuery("input[name='list']:checked").val();
			
			//不包含已分配客户
			var notAssignCheckBox=jQuery('#notAssignCheckBox').attr("checked");
			if (notAssignCheckBox=='checked'){
				notAssignCheckBox=1;
			}else{
				notAssignCheckBox=0;
			}
			
//			if (ownerid == null || ownerid==''){
//				var message = app.vtranslate('JS_SELECT_OWNERID_MESSAGE');
//				var  params = {text : message, title :'提示：'}
//				Vtiger_Helper_Js.showPnotify(params);
//				return;
//			}
			
			//var datarows=$("#tbl_ServiceAssignRule_Account_Detail tr:gt(0)").length;
			var selectAccountids={};
			if (assignRadiovalue =="0"){
				var iRow=0;
				$("#tbl_ServiceAssignRule_Account_Detail tr td input:checkbox").each(function(){
					//alert($(this).attr("checked"));
					if ($(this).attr("checked")=="checked"){
						selectAccountids[iRow]=$(this).val();
						iRow++;
					}
				});
				if (selectAccountids.length==0){
					var message = app.vtranslate('JS_ACCOUNTS_NO_SELECT_DATA_MESSAGE');
					var  params = {text : message, title :'提示：'}
					Vtiger_Helper_Js.showPnotify(params);
					return;
				}
			}
			
			var params = {
					'module' : app.getModuleName(),
					'action' : 'SubRule',
					'mode':'doServiceAssign',
					'assigntype':assigntype,
					'departmentid':departmentid,
					'productid':productid,
					'ownerid':ownerid,
					'serviceid':serviceid,
					'oldserviceid':oldserviceid,
					'accountid':accountid,
					'assignRadio':assignRadiovalue,
					'notAssignCheckBox':notAssignCheckBox,
					'selectAccountids':selectAccountids,
					'checkflg':'1'
			};
			jQuery('.msg').html('<font color=red>正在进行客服分配处理，请稍等......</font>');     
			//发送请求
			AppConnector.request(params).then(
				function(data){
					if(data != null && data.success ==  true){
						var message ="";
						if (data.result[0]==0){
							message = app.vtranslate('JS_ASSIGN_SERVICE_SUCCESS_MESSAGE');
						}else if (data.result[0]==2){
							message = app.vtranslate('JS_ASSIGN_SERVICE_EXCEED_MAX_MESSAGE');
						}else{
							message = app.vtranslate('JS_ASSIGN_SERVICE_NO_DATA_MESSAGE');
						}
						var params = {
							text: message,
							type: 'notice'
						};
						Vtiger_Helper_Js.showMessage(params);
					}else{
						var message = app.vtranslate('JS_ASSIGN_SERVICE_ERROR_MESSAGE');
						var params = {
								text: message,
								type: 'error'
							};
						Vtiger_Helper_Js.showMessage(params);
					}
					if(data != null && data.success ==  true){
						//客服分配信息表示
						jQuery('.divserviceinfo').html(data.result[1]);
					}
					
					jQuery('.msg').html('<font  color=red>客服分配完成</font>');
					//刷新页面
					//window.location.reload();
				},
				function(error){
					jQuery('.msg').html('<font  color=red>客服分配失败</font>');
				}
			);
		})	
	},
	
	registerCheckBoxClickEvent : function(){
		jQuery('#listViewEntriesMainCheckBox').live('click',function(event){
			if(jQuery(this).is(':checked')){
				jQuery(".listViewEntriesCheckBox").attr("checked", true);  
			}else{
				jQuery(".listViewEntriesCheckBox").attr("checked", false);  
			}
			 
			
		});
		
	},
	Tableinstance:function(){
		
		var table = jQuery('.listViewEntriesTable').DataTable( {
			language: {"sProcessing":   "处理中...",	"sLengthMenu":   "显示 _MENU_ 项结果","sZeroRecords":  "没有匹配结果","sInfo":         "显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",	"sInfoEmpty":    "显示第 0 至 0 项结果，共 0 项","sInfoFiltered": "(由 _MAX_ 项结果过滤)","sInfoPostFix":  "","sSearch":"当前页快速检索:","sUrl":"","sEmptyTable":     "表中数据为空","sLoadingRecords": "载入中...",
				"sInfoThousands":  ",",	"oPaginate": {"sFirst":"首页","sPrevious": "上页","sNext":"下页","sLast":"末页"},
				"oAria": {"sSortAscending":  ": 以升序排列此列","sSortDescending": ": 以降序排列此列"}},
			scrollY:"300px",
            sScrollX:"disabled",



            aLengthMenu: [ 50, 100, 500, 2000, ],
			fnDrawCallback:function(){
				jQuery('.msg').html('<font  color=red>数据加载完成</font>');
			}


		} );
	},
	
	getServiceAssignInfo:function(curserviceid){
		var params = {
				'module' : app.getModuleName(),
				'action' : 'SubRule',
				'mode':'getServiceAssignInfos',
				'serviceid':curserviceid
		};
		
		//发送请求
		AppConnector.request(params).then(
			function(data){
				$('.divserviceinfo').html(data.result);
				//刷新页面
				//window.location.reload();
			},
			function(error){
				jQuery('.divserviceinfo').html('获取客服分配信息失败!');
			}
		);
	},
	
	registerEvents : function() {
		this._super();
		this.registerServiceChangeEvent();
		this.registerAssignClickEvent();
		this.registerGetAccountsClickEvent();
		this.registerAssignTypeChangeEvent();
		this.registerDepartmentChangeEvent();
		this.registerRecordPreSaveEvent();
		this.registerCheckBoxClickEvent();
		//客服分配信息取得
		this.getServiceAssignInfo(jQuery('select[name="serviceid"]').val());
	}
});


