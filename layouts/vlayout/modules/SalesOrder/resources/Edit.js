/*+***********************
 * 编辑有无合同的工单和新增内部工单
 ***********************/

Vtiger_Edit_Js("SalesOrder_Edit_Js",{},{
	rowSequenceHolder : false,
	registerReferenceSelectionEvent : function(container) {
		this._super(container);
		var thisInstance = this;
		/*无合同的内部工单*/
		jQuery('input[name="productid"]', container).on(Vtiger_Edit_Js.refrenceMultiSelectionEvent, function (e,data){productschange(data);});
		function productschange(sourcemodule){
			if(sourcemodule['source_module']=='Products' && sourcemodule['record'].length>0){
				thisInstance.loadWidgetNo(sourcemodule['record'],0);
			}			
		}
		
		function Editchange(){
			var sc=$('input[name="servicecontractsid"]');
			var params = {'module' : sc.parent('td').find('input:eq(0)').val(),'action' : 'BasicAjax', 'record' : sc.val()};
			AppConnector.request(params).then(
				function(data){
					if(data.success ==  true){
						var json=data.result;
						$('input[name=account_id_display]').val(json.accountname);
						$('input[name=account_id]').val(json.id);
						$('select[name=assigned_user_id]').val(json.userid);
						$('input[name=salescommission]').val(json.total);
						$('input[name=customerno]').val(json.customerno);
						var rps=data.result.rp;
						var str='<br><table class="table table-bordered equalSplit detailview-table"><thead><tr><th class="blockHeader" colspan="3"><img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;"><img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">&nbsp;&nbsp;合同回款记录</th></tr></thead><tbody style="display: table-row-group;">';
						str=str+'<tr><td>回款标题</td><td>回款时间</td><td>回款金额</td></tr>';
						$.each(rps,function(n,json){
							str=str+'<tr><td>'+json.relmodule+'</td><td>'+json.createtime+'</td><td>'+json.unit_price+'</td></tr>';
							
						});
						str=str+'</tbody></table>';
						$('#additionhuik').html(str);
						//console.log(rps);			
					}
				},
				function(error){
				//TODO : Handle error
				}
			);
		}
		
		//切换工作流 按名称判断是否内部
		$('input[name="workflowsid"]',container).on(Vtiger_Edit_Js.referenceSelectionEvent,function (e,data){
			thisInstance.loadWidgetNote(data['selectedName']);
		});
		////workflowschange(data['record']);
		//function workflowschange(record){if(sourcemodule=='Workflows'){var workflowsid=$('input[name="workflowsid"]');if(workflowsid.val().length>0){}}}
		
		/*外部工单有合同*/
		$('input[name="servicecontractsid"]',container).on(Vtiger_Edit_Js.referenceSelectionEvent,function (e,data){servicecontractschange()});
		//合同变更事件
		//加载合同和产品信息
		function servicecontractschange(){
			var progressIndicatorElement = jQuery.progressIndicator({ 'message' : '合同信息加载中...','blockInfo':{'enabled':true }});
			var params = {'module':'ServiceContracts','action':'BasicAjax','record':$('input[name="servicecontractsid"]').val(),'salesorderid':$('input[name="record"]').val()};
			AppConnector.request(params).then(
				function(data){
					if(data.success){
						progressIndicatorElement.progressIndicator({ 'mode' : 'hide'});
						$("tr.removetr").remove();
						var ckEditorInstance = new Vtiger_CkEditor_Js();
						var jsons = data.result['0'];
						var str='';
						$.each(jsons,function(n,json){
							//str ='<tr class="removetr"><td>'+json.product_no+'</td><td>'+json.productname+'</td><td>'+json.realprice+'</td><td>'+json.unit_price+'</td><td><textarea name="productnote['+json.id+']" style="width:100%;" id="'+'insertnote'+n+'"'+'>'+json.solutions+'</textarea></td></tr>';
							str+='<tr class="removetr"><td>'+json.product_no+
							'</td><td>'+json.productname+'</td><td>'+json.productcomboid+'</td><td colspan="2">';
							
							if(data.result['3']){
								str+=json.productform+'<input type="hidden" name="tpl['+json.productid+']" value="'+json.product_tplid+'"></td></tr>';
							}else{
								str+='<textarea name="productnote['+json.productid+']" style="width:100%;" id="'+'insertnote'+json.productid+'">'+json.productform+
							'</textarea></td></tr>';
								//UE.delEditor("insertnote"+json.id);
								ckEditorInstance.loadCkEditor("insertnote"+json.productid);
							}
							
							//ckEditorInstance.loadCkEditor("insertnote"+json.id);
							//UE.getEditor('insertnote'+ii).execCommand('forecolor','#f0e68c');
							//ii++;
						});
						if(str==''){
							str='<tr class="removetr warning"><td align="center" style="text-align:center;" colspan="5">当前合同下无产品</td></tr>';
						}
						$('#insertproduct').after(str);
						//$("#no-repeatid").val(ii);
						//合同工单自动生成无需修改信息
						//新增可切换合同
						if($('input[name="record"]').val()==''){
							var json=data.result['1'];
							$('input[name=account_id_display]').val(json.accountname);
							$('input[name=account_id]').val(json.id);
							$('select[name=assigned_user_id]').val(json.userid);
							$('input[name=salescommission]').val(json.total);
							$('input[name=customerno]').val(json.customerno);
							$('input[name="pending"]').val(json.remark);
							var text=$('select[name="assigned_user_id"]').find("option:selected").text();
							$('#SalesOrder_editView_fieldName_subject').val(json.accountname+'的合同工单');
							$('select[name="assigned_user_id"]').next('.chzn-container-single').find('span').text(text);
						}
						//
						/*  */
						var strtd='';
						var rps=data.result['1'].rp;
						str='<br><table class="table table-bordered equalSplit detailview-table"><thead><tr><th class="blockHeader" colspan="4"><img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;"><img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">&nbsp;&nbsp;合同回款记录</th></tr></thead><tbody style="display: table-row-group;"><tr><td>回款标题</td><td>回款时间</td><td>回款金额</td><td>回款状态</td></tr>';
						var status=""
						$.each(rps,function(n,json){
							if(json.createtime==null){
								json.createtime="";
							}
							if(json.discontinued==0){
								status="未回款";
							}else{
							    status="已回款";
							}
							strtd+='<tr><td>'+json.relmodule+'</td><td>'+json.createtime+'</td><td>'+json.unit_price+'</td><td>'+status+'</td></tr>';
						});
						if(strtd==''){
							//strtd='<tr class="removetr warning"><td align="center" style="text-align:center;" colspan="4">当前合同无回款记录</td></tr>';
						}
						//$('#additionhuik').html(str+strtd+'</tbody></table>');
					}
				},
				function(error){}
			);
		}
		
		
		jQuery('input[name="potential_id"]', container).on(Vtiger_Edit_Js.referenceSelectionEvent, function (e,data){potentialchange();});

		function potentialchange(){
			var sc=$('input[name="potential_id"]');
//			var params = {
//			'module' : sc.parent('td').find('input:eq(0)').val(),
//			'action' : 'BasicAjax', 
//			'record' : sc.val(),
//			'mode'   : 'getRelateProduct'
//			};
//			AppConnector.request(params).then(
//				function(data){
//					if(data.success ==  true){	
//					}
//				},
//				function(error){
//				//TODO : Handle error
//					console.log(error);
//				}
//			);
			var params = {
					'type' : 'GET',
					'dataType': 'html',
					'data' : 'module=SalesOrder&view=Detail&mode=getProducts&relate_module=Potentials&record='+sc.val()+'&relaterecord='+$('input[name="record"]').val()
			};
			thisInstance.loadWidgetProduct(params,$('.widgetContainer_1'));//获取产品
			//fenc();
		}
		function fenc(){
			//获取分成
			var sparams={
				'module' : 'PotentialScalesrel',	
				'action' :	'BasicAjax',
				'record' :	$('input[name="potential_id"]').val(),
				'mode'   : 	'getAllPotentialScales'
			};
			AppConnector.request(sparams).then(
				function(sdata){					
					if(sdata.success ==  true){
						//@TODO 获取销售机会的分成
						var str='<br><table class="table table-bordered equalSplit detailview-table"><thead><tr><th class="blockHeader" colspan="4"><img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;"><img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">&nbsp;&nbsp;销售业绩分成</th></tr></thead><tbody style="display: table-row-group;">';
						str=str+'<tr><td>分成标题</td><td>分成人</td><td>分成时间</td><td>分成比例</td></tr>';
						var jsons=sdata.result;
						$.each(jsons,function(n,json){
							str=str+'<tr><td>'+json.potentialscalename+'</td><td>'+json.username+'</td><td>'+json.scaletime+'</td><td>'+json.scale+'</td></tr>';
							
						});
						
						
						str=str+'</tbody></table>';
						$('#additionfenc').html(str);
					}
				},
				function(error){
					console.log(error+'+');
				}
			);
		}
		
		//编辑工单
		var record=jQuery('input[name="record"]').val();
		if(record>0){
			//按工作流显示数据?
			//workflowschange($('input[name="workflowsid"]').val());thisInstance.loadWidgetNote($('.widgetContainer_salesorderworkflows'),$('input[name="workflowsid"]').val());
			if($('#servicecontractsid_display').val()){
			//编辑页面载入显示合同并加载合同下产品信息
				//thisInstance.hasContract();
				servicecontractschange();
				//逻辑上选定合同以合同为准//禁止再修改工作流和合同
				//$('#SalesOrder_editView_fieldName_workflowsid_select,#SalesOrder_editView_fieldName_workflowsid_clear,#SalesOrder_editView_fieldName_servicecontractsid_select,#SalesOrder_editView_fieldName_servicecontractsid_clear,#SalesOrder_editView_fieldName_account_id_clear,#SalesOrder_editView_fieldName_account_id_select').parent().remove();
				$('#SalesOrder_editView_fieldName_servicecontractsid_select,#SalesOrder_editView_fieldName_servicecontractsid_clear,#SalesOrder_editView_fieldName_workflowsid_clear,#SalesOrder_editView_fieldName_workflowsid_select').parent().remove();
			}else{
				$('.tableadv').addClass('hide').find('input').attr("disabled","disabled");
				$('.tablecust').removeClass('hide').find('input').removeAttr("disabled");
				//按工单加载产品信息
				thisInstance.loadWidgetNo(0,record);
			}
			//potentialchange();
		}else{
			//新建默认标准合同 禁用产品选择
			$('.tablecust').find('input').attr("disabled","disabled");
		}
		if(record>0 && jQuery('input[name="productid"]').val()!=''){
			//thisInstance.loadWidgetNot($('.widgetContainer_salesorderworkflows'),record);
		}
	},
	
	
	/*无合同的内部工单新增加载产品信息*/
	loadWidgetNo : function(id,record) {
		//var thisInstance = this;
		//var contentHeader = jQuery('.widget_header',widgetContainer),contentContainer = jQuery('.widget_contents',widgetContainer);contentContainer.html('');
		//var record=$('input[name="record"]').val();
		//var workflowsid=$('input[name="workflowsid"]').val();
		var urlParams = 'module=SalesOrder&view=ListAjax&mode=edit&relate=product';
		var params = {'type' : 'GET','dataType': 'json','data' : urlParams+'&productid='+id+'&record='+record};
		var progressIndicatorElement = jQuery.progressIndicator({ 'message' : '产品信息加载中...','blockInfo':{'enabled':true }});
		AppConnector.request(params).then(
				function(data){
					$("tr.removetr").remove();
					if(data.success){
					var str='',info=data.products;
						for(var i in info){
							str+='<tr class="removetr"><td>'+info[i]['product_no']+'</td><td>'+info[i]['productname']+'</td><td>--</td><td>'+info[i]['productform']+'</td><td><i class="icon-trash deleteRow cursorPointer" title="删除">  </i><input type="hidden" name="tpl['+info[i]['productid']+']" value="'+info[i]['product_tplid']+'"><input type="hidden" value="'+info[i]['productid']+'" name="productids[]"></td></tr>';
						}
						$('#insertproduct').after(str);
					}
					progressIndicatorElement.progressIndicator({'mode': 'hide'});
						//var info=eval("("+data+")");
						//$(data.products).each(function(index) {$.each(data.products, function (n, value) {});
						//var html='<table class="table table-bordered listViewEntriesTable"><thead><tr class="listViewHeaders"><th class="narrow">产品名称&nbsp;&nbsp; </th><th class="narrow">产品规则&nbsp;&nbsp;</th><th class="narrow"></th><th class="narrow">删除&nbsp;&nbsp;</th></tr></thead><tbody>';
						//var upload=true,ckEditorInstance = new Vtiger_CkEditor_Js(), num=$('#no-repeatid').val();
						/* if(info.products[i]['productcategory']!='std'){upload=false;} *///var solution=record>0?'productform':'solution';
						//trhtml+='<tr data-id="'+info[i]['productid']+'" class="'+info[i]['productcategory']+'"><td>'+info[i]['productname']+'</td><td class="span6">'+info[i]['productform']+'</td><td class="span6"></td><td><i class="icon-trash deleteRow cursorPointer" title="删除">  </i><input type="hidden" value="'+info[i]['productid']+'" name="productids[]"><input type="hidden" value="'+info[i]['product_tplid']+'" name="tpl['+info[i]['productid']+']"><input type="hidden" value="'+info[i]['productcategory']+'" name="productcategory[]"> </td></tr>';
						//num++ckEditorInstance.loadCkEditor("insertnot"+i);
						//$('#no-repeatid').val(num);
						/* if(contentContainer.children('table').length>0){contentContainer.children('table').append(trhtml);}else{contentContainer.html(html+trhtml+'</tbody></table>');	} */
						/* var classes = $('.differentiate');$.each(classes,function(n,ids){//console.log($(this).attr('id'));ckEditorInstance.loadCkEditor($(this).attr('id'));}) */
						//2015-01-27 17:12:51 王斌 新建合同必须上传附件 去掉产品判断
						/*if($('#file').val().length<1 && !upload){Vtiger_Helper_Js.showPnotify({text : '您选择了非标准合同',title :'非标准合同需要上传附件！'});}*/		

					/*
					if($('#iscontract').val()==1){$('.tableadv').removeClass('hide');
						//$('.tableproduct').removeClass('hide');
						$('input[name="productid"]').val('');$('input[name="productid_display"]').val('');
					}else{
						if(!$('.tableadv').hasClass('hide')){$('.tableadv').addClass('hide');}
						// if(!$('.tableproduct').hasClass('hide')){ $('.tableproduct').addClass('hide');}
						$('input[name="servicecontractsid"]').val('');
						$('input[name="servicecontractsid_display"]').val('');$('input[name="salescommission"]').val('');
						$('input[name="customerno"]').val('');$('input[name="account_id_display"]').val('');
						$('input[name="account_id"]').val('');$('input[name="potential_id"]').val('');
						$('input[name="potential_id_display"]').val('');$('input[name="pending"]').val('');
					}
					*/
				},
				function(){}
			);
	},

	/**
	 * 弹出参数？没用吧
	 */
	getPopUpParams : function(container) {
		var params = this._super(container);
        var sourceFieldElement = jQuery('input[class="sourceField"]',container);
		if(sourceFieldElement.attr('name') == 'contact_id' || sourceFieldElement.attr('name') == 'potential_id') {
			var form = this.getForm();
			var parentIdElement  = form.find('[name="account_id"]');
			if(parentIdElement.length > 0 && parentIdElement.val().length > 0) {
				var closestContainer = parentIdElement.closest('td');
				params['related_parent_id'] = parentIdElement.val();
				params['related_parent_module'] = closestContainer.find('[name="popupReferenceModule"]').val();
			} else if(sourceFieldElement.attr('name') == 'potential_id') {
				parentIdElement  = form.find('[name="contact_id"]');
				if(parentIdElement.length > 0 && parentIdElement.val().length > 0) {
					closestContainer = parentIdElement.closest('td');
					params['related_parent_id'] = parentIdElement.val();
					params['related_parent_module'] = closestContainer.find('[name="popupReferenceModule"]').val();
				}
			}
        }
        return params;
    },
	/**
	 * 貌似是没用的
	 */
	searchModuleNames : function(params) {
		var aDeferred = jQuery.Deferred();
		if(typeof params.module == 'undefined') {
			params.module = app.getModuleName();
		}
		if(typeof params.action == 'undefined') {
			params.action = 'BasicAjax';
		}
		if (params.search_module == 'Contacts' || params.search_module == 'Potentials') {
			var form = this.getForm();
			var parentIdElement  = form.find('[name="account_id"]');
			if(parentIdElement.length > 0 && parentIdElement.val().length > 0) {
				var closestContainer = parentIdElement.closest('td');
				params.parent_id = parentIdElement.val();
				params.parent_module = closestContainer.find('[name="popupReferenceModule"]').val();
			} else if(params.search_module == 'Potentials') {
				parentIdElement  = form.find('[name="contact_id"]');
				if(parentIdElement.length > 0 && parentIdElement.val().length > 0) {
					closestContainer = parentIdElement.closest('td');
					params.parent_id = parentIdElement.val();
					params.parent_module = closestContainer.find('[name="popupReferenceModule"]').val();
				}
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
	
	/*无合同的内部工单编辑加载产品信息*/
	loadWidgetNot : function(widgetContainer,id) {
		var thisInstance = this;
		//var contentHeader = jQuery('.widget_header',widgetContainer);
		var contentContainer = jQuery('.widget_contents',widgetContainer);
		contentContainer.html('');
		var record=jQuery('input[name="record"]').val();
		var workflowsid=jQuery('input[name="workflowsid"]').val();
		if(record>0 && jQuery('input[name="productid"]').val()!=''){
			var urlParams = 'module=SalesOrder&view=ListAjax&mode=edit';
			var id=record;		
		}else{
			var urlParams = 'module=ServiceContracts&view=ListAjax';
		}
		var params = {
			'type' : 'GET',
			'dataType': 'html',
			'data' : urlParams+'&productid='+id
		};
		contentContainer.progressIndicator({});
		AppConnector.request(params).then(
				function(data){
					var info=eval("("+data+")");
					if(info.success){
						var html='<table class="table table-bordered listViewEntriesTable"><thead><tr class="listViewHeaders"><th class="narrow">产品名称&nbsp;&nbsp; </th><th class="narrow">产品信息&nbsp;&nbsp;</th><th class="narrow"></th><th class="narrow">删除&nbsp;&nbsp;</th></tr></thead><tbody>';
						var trhtml='';
						var upload=true;
						var ckEditorInstance = new Vtiger_CkEditor_Js();
						for(var i in info.products){
							if(info.products[i]['productcategory']!='std'){
								upload=false;
							}
							var solution=record>0?'productform':'newsolution';
							trhtml+='<tr class="'+info.products[i]['productcategory']+'"><td>'+info.products[i]['productname']+'</td><td class="span6"><textarea class="span6" name="productsolution['+info.products[i]['productid']+']" id="'+'insertnote'+i+'"'+'>'+info.products[i][solution]+'</textarea></td><td class="span6"></td><td><i class="icon-trash deleteRow cursorPointer" title="删除">  </i><input type="hidden" value="'+info.products[i]['productid']+'" name="productids[]"> <input type="hidden" value="'+info.products[i]['productcategory']+'" name="productcategory[]"> </td></tr>';
							//console.log(info.products[i]);
							//$('#insertproduct').after(str);
							ckEditorInstance.loadCkEditor("insertnote"+i);
						}
						if(contentContainer.children('table').length>0){
							contentContainer.children('table').append(trhtml);	
						}else{
							contentContainer.html(html+trhtml+'</tbody></table>');	
						}
							
					}
					contentContainer.progressIndicator({'mode': 'hide'});
					if($('#iscontract').val()==1){
						$('.tableadv').removeClass('hide');
						$('.tableproduct').removeClass('hide');
                        $('input[name="productid"]').val('');
                        $('input[name="productid_display"]').val('');
					}else{
						if(!$('.tableadv').hasClass('hide')){
							$('.tableadv').addClass('hide');
						}
						if(!$('.tableproduct').hasClass('hide')){
							$('.tableproduct').addClass('hide');
						}
                        $('input[name="servicecontractsid"]').val('');
                        $('input[name="servicecontractsid_display"]').val('');
                        $('input[name="salescommission"]').val('');
                        $('input[name="customerno"]').val('');
                        $('input[name="account_id_display"]').val('');
                        $('input[name="account_id"]').val('');
                        $('input[name="potential_id"]').val('');
                        $('input[name="potential_id_display"]').val('');
                        $('input[name="pending"]').val('');
					}
					
					thisInstance.registerEventForCkEditor();
				},
				function(){}
			);
		},
		
	//加载ckeditor 废弃
	registerEventForCkEditor : function(){
		return;
		var form = this.getForm();
		//var noteContentElement = $('textarea');
		var ckEditorInstance = new Vtiger_CkEditor_Js();
		ckEditorInstance.loadCkEditor('textarea[name=notecontent]');
		ckEditorInstance.loadCkEditor('textarea.lineItemCommentBox');
		/*if(noteContentElement.length > 0){
			ckEditorInstance = new Vtiger_CkEditor_Js();
			var now_no = $('#totalProductCount').val();
			//console.log(now_no);
			noteContentElement.each(function(){
				if(typeof $(this).attr('id')=='undefined'){
					$(this).attr('id','comment'+now_no);
					ckEditorInstance.loadCkEditor('#comment'+now_no);
					now_no=now_no*1+1;
				}
			});
			$('#totalProductCount').val(now_no);
		}*/
	},
	//加载ckeditor
	registerProductAllCk:function(){
		var lineItemTable = this.getLineItemContentsContainer();
		ckEInstance = new Vtiger_CkEditor_Js();
		var parent=$('.lineItemCommentBox:visible',lineItemTable).each(function(){
			ckEInstance.loadCkEditor($(this));
		});		
	},
	registerEventForWorkflows:function(){
		var workflows=$('input[name=workflowsid]');
		workflows.on('input',function(){
			alert(workflows.val());
		});
	},
	//注册新增产品按钮
	/*registerAddingNewProducts: function(){
		var thisInstance = this;
		$('#totalProductCount').val(1);
		var lineItemTable = this.getLineItemContentsContainer();
		thisInstance.registerProductAllCk();
		$('#addProduct').live('click',function(){
			var oldRow = $('.lineItemCloneCopy',lineItemTable);;
			var newRow = oldRow.clone(true,true).removeClass('hide lineItemCloneCopy').addClass('lineItemRow');
			newRow.appendTo(lineItemTable);
			//thisInstance.registerEventForCkEditor();
			thisInstance.registerCalculationMoney();
		});
    },
    //注册删除产品事件
    registerDeleteLineItemEvent : function(){
		var thisInstance = this;
		var lineItemTable = this.getLineItemContentsContainer();
		lineItemTable.on('click','.deleteRow',function(e){
			var element = jQuery(e.currentTarget);
			if(element.closest('tr.lineItemRow').attr('name').length>0){
				if(confirm('确定删除产品，包含套餐下所有的产品')){
					$('tr[name="'+element.closest('tr.lineItemRow').attr('name')+'"]').each(function(){
						$(this).remove();
					});
				}	
			}else{
				element.closest('tr.lineItemRow').remove();
			}
			//element.closest('tr.lineItemRow').remove();
			//thisInstance.checkLineItemRow();
		});
	 },
	 */
	 //隐藏删除按钮?无效？
	 //checkLineItemRow : function(){var lineItemTable = this.getLineItemContentsContainer();var noRow = lineItemTable.find('.lineItemRow').length;if(noRow >1){lineItemTable.find('.deleteRow').show();}else{lineItemTable.find('.deleteRow').hide();}},
	//加载table
	getLineItemContentsContainer:function(){
		return $('#lineItemTab');
	},
/* 	loadWidgets : function(){
		var thisInstance = this;
		var widgetList = jQuery('[class^="widgetContainer_"]');
		widgetList.each(function(index,widgetContainerELement){
			var widgetContainer = jQuery(widgetContainerELement);
			thisInstance.loadWidget(widgetContainer);
		});	
	}, */
	//切换工作流触发 区分内外部工单 是否有合同 仅新增
	loadWidgetNote : function(name) {
		var thisInstance = this;
		//产品清空
		$("tr.removetr").remove();
		$('.tableadv').find('input:visible').attr("disabled","disabled").val('');
		$('.tablecust').find('input:visible').attr("disabled","disabled").val('');
		if(name.indexOf('内部')>-1 || name.indexOf('退单')>-1){
			$('#iscontract').val(0);
			//合同区域隐藏
			$('.tableadv').addClass('hide');
			//显示产品选择
			$('.tablecust').removeClass('hide').find('input').removeAttr("disabled");
			$('#additionhuik').empty();
		}else{
			$('#iscontract').val(1);
			$('.tableadv').removeClass('hide').find('input').removeAttr("disabled");
			$('.tablecust').addClass('hide').find('input').attr("disabled","disabled");
		}
		
		/* var thisInstance = this,contentContainer = jQuery('.widget_contents',widgetContainer),params = {'type' : 'GET','dataType': 'html','data' : widgetContainer.data('url')+id};
		contentContainer.progressIndicator({});	//进度条加载
		AppConnector.request(params).then(
			function(data){
				contentContainer.progressIndicator({'mode': 'hide'});
				contentContainer.html(data);
				if($('#iscontract').val()==1){
					//有合同的外部工单工作流
					if($('.tableadv').hasClass('hide')){thisInstance.hasContract();}
				}else{
					if(!$('.tableadv').hasClass('hide')){
						$('.tableadv,.tableproduct').addClass('hide');
						$('.tableadv').find('input').attr("disabled","disabled");
					}
					if(jQuery('#iscontent').val()==1){
						$('.tablecust').removeClass('hide').find('input').removeAttr("disabled");
					}
                    //$('input[name="servicecontractsid"]').val('');$('input[name="servicecontractsid_display"]').val('');
                    //$('input[name="salescommission"]').val('');$('input[name="customerno"]').val('');
                    //$('input[name="account_id_display"]').val('');$('input[name="account_id"]').val('');
                    //$('input[name="potential_id"],input[name="potential_id_display,input[name="pending"]').val('');	
				}
				//thisInstance.registerEventForCkEditor();
			},
			function(){}
		); */
	},
	
	//有合同
	hasContract:function(){
		$('.tableadv').removeClass('hide').find('input').removeAttr("disabled");
		$('.tablecust').addClass('hide').find('input').attr("disabled","disabled");
        //$('input[name="productid"],input[name="productid_display"]').attr("disabled","disabled");	
	},
	//产品加载
	loadWidgetProduct : function(params,widgetContainer) {
		var thisInstance = this;
		var contentHeader = jQuery('.widget_header',widgetContainer);
		var contentContainer = jQuery('.widget_contents',widgetContainer);
		contentContainer.progressIndicator({});
		AppConnector.request(params).then(
			function(data){
				contentContainer.progressIndicator({'mode': 'hide'});
				//contentContainer.html(data);
				var tb=thisInstance.getLineItemContentsContainer();
				tb.children('tbody').append(data);
				//thisInstance.registerEventForCkEditor();
				thisInstance.registerCalculationMoney();
			},
			function(){
				contentContainer.progressIndicator({'mode': 'hide'});
			}
		);
	},
	showPopup : function(params) {
		var aDeferred = jQuery.Deferred();
		var popupInstance = Vtiger_Popup_Js.getInstance();
		popupInstance.show(params, function(data){
			aDeferred.resolve(data);
		});
		return aDeferred.promise();
	},
	//每行的弹出产品选择
/* 	lineItemPopupEventHandler : function(popupImageElement) {
		var aDeferred = jQuery.Deferred();
		var thisInstance = this;
		
		var referenceModule = popupImageElement.data('moduleName');
		var moduleName = app.getModuleName();
		//thisInstance.getModulePopUp(e,referenceModule);
		var params = {};
		params.view = popupImageElement.data('popup');
		params.module = moduleName;
		params.multi_select = false;
		//params.currency_id = jQuery('#currency_id option:selected').val();

		this.showPopup(params).then(function(data){
			var responseData = JSON.parse(data);
			var len = Object.keys(responseData).length;
			var contentHeader = jQuery('.widget_header','widgetContainer_1');
			var contentContainer = jQuery('.widget_contents','widgetContainer_1');
			contentContainer.progressIndicator({});
			var record=0;
			if(len==1){
				for(var id in responseData){
					record = responseData[id].id;
				}
				var params = {
					'module' : 'SalesOrder',
					'view' : 'Detail', 
					'record' : record,
					'mode'   : 'getProductById',
					'relate_module' : 'Products'
				};
						
				AppConnector.request(params).then(
					function(data){
						contentContainer.progressIndicator({'mode': 'hide'});
						//console.log(data);
						$(popupImageElement).closest("tr").prop('outerHTML', data);
						//thisInstance.registerEventForCkEditor();
						thisInstance.registerCalculationMoney();
					},
					function(error){
						contentContainer.progressIndicator({'mode': 'hide'});
						//TODO : Handle error
					}
				);
				aDeferred.resolve();
			}
		})
		return aDeferred.promise();
	}, */
	//绑定产品弹出框
/* 	registerProductPopup : function() {
		var thisInstance = this;
		var lineItemTable = this.getLineItemContentsContainer();
		lineItemTable.on('click','img.lineItemPopup', function(e){
			var element = jQuery(e.currentTarget);
			thisInstance.lineItemPopupEventHandler(element).then(function(data){
				var parent = element.closest('tr');
				var deletedItemInfo = parent.find('.deletedItem');
				if(deletedItemInfo.length > 0){
					deletedItemInfo.remove();
				}
			})
		});
	}, */
	/*
	registerEditProductBind:function(){
		if($("input[name='record']").val()!=""){
			var thisInstance=this;
			var params = {
				'type' : 'GET',
				'dataType': 'html',
				'data' : 'module=SalesOrder&view=Detail&mode=getProductBySalesorderid&relate_module=SalesorderProductsrel&record='+$("input[name='record']").val()
			};
			thisInstance.loadWidgetProduct(params,$('.widgetContainer_1'));
		}
	},*/
	//计算价格
	registerCalculationMoney:function(){
		var lineItemTable = this.getLineItemContentsContainer();
		var summoney=0.00;
		
		lineItemTable.find(".productTotal").each(function(){
			summoney+=parseFloat($(this).text());
		});
		
		$("#netTotal").html(summoney.toFixed(2));
	},
	//绑定收缩效果
	registerBlockAnimationEvent : function(){
		var detailContentsHolder = $('.editViewContainer');
		detailContentsHolder.on('click','.blockToggle',function(e){
			var currentTarget =  jQuery(e.currentTarget);
			var blockId = currentTarget.data('id');
			var closestBlock = currentTarget.closest('.detailview-table');
			var bodyContents = closestBlock.find('tbody');
			var data = currentTarget.data();
			var module = app.getModuleName();
			var hideHandler = function() {
				bodyContents.hide('slow');
				app.cacheSet(module+'.'+blockId, 0)
			}
			var showHandler = function() {
				bodyContents.show();
				app.cacheSet(module+'.'+blockId, 1)
			}
			var data = currentTarget.data();
			if(data.mode == 'show'){
				hideHandler();
				currentTarget.hide();
				closestBlock.find("[data-mode='hide']").show();
			}else{
				showHandler();
				currentTarget.hide();
				closestBlock.find("[data-mode='show']").show();
			}
		});

	},
	registerBasicEvents:function(container){
		this._super(container);
		this.registerReferenceSelectionEvent(container);
	},
	
	registerEvents: function(){
		this._super();
		var hidden = $("<input>").attr("type", "hidden").attr("id", "no-repeatid").val("0").appendTo("body");
		/*合同金额公司名称客户编号不直接编辑*/
		$('#SalesOrder_editView_fieldName_salescommission,#SalesOrder_editView_fieldName_customerno,#account_id_display,#servicecontractsid_display').attr('readonly',true);
		/*公司信息只读*/
		$('#SalesOrder_editView_fieldName_account_id_clear,#SalesOrder_editView_fieldName_account_id_select').parent().remove();
		this.registerBlockAnimationEvent();
		//内部工单产品删除
		$('#lineItemTab').on('click','.deleteRow',function(e){
			var that=$(this);
			Vtiger_Helper_Js.showConfirmationBox({'message':'确定删除产品吗？'}).then(
				function(e) {
					that.closest('tr').remove();
				}
			);	
		});
 		$('#SalesOrder_editView_fieldName_issubmit').attr('data-content','<font color="red">勾选<<b>确认提交审核</b>>后工单才会被审核</font>');
        	$('#SalesOrder_editView_fieldName_issubmit').popover('show');
		//this.registerEventForCkEditor();
		//this.registerAddingNewProducts();
		//this.registerDeleteLineItemEvent();
		//this.registerProductPopup();
		//this.registerEditProductBind();
		//this.registerForTogglingBillingandShippingAddress();
		//this.registerEventForCopyAddress();
	}
});


