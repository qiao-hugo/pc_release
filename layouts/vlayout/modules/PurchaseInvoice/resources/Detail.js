Vtiger_Detail_Js('PurchaseInvoice_Detail_Js',{
},{
	
	/*bindSalesorderProjectTasksrel:function(){
	
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
	},*/
    /**
     * young.yang 2015-05-21
     * 覆盖父级的审核按钮动作，主要是为了指定审核客服使用
     **/
    bindStagesubmit:function(){
    	

        $('.details').on('click','.stagesubmit',function(){
            var name=$('#stagerecordname').val(); //获取审核节点名称
            //bindselectchange();
            var selectname='';//拼接字符串使用
            var paramsn={};  //请求是否需要指定客服参数
            paramsn['module'] = 'PurchaseInvoice';
            paramsn['action'] = 'ChangeAjax';
            paramsn['record'] = $('#stagerecordid').val();

            var sname={};
            sname.data=paramsn;
            sname.type = 'GET';
            var  msg={};
            //获取当前的节点是否需要指定客服
           AppConnector.request(sname).then(function(data){
                if(data.result.flag=='responsibility_accounting'){ //需要指定客服
					var selectname = '<div class="form-horizontal"><div class="control-group"><label class="control-label" for="examine">审核状态</label><div class="controls"><select name="examine" id="examine" class="span2 chzn-select"><option selected="selected" value="1">签收</option><option value="2">无需认证</option></select></div></div><div class="control-group"><label class="control-label" for="month">认证月份</label><div class="controls"><select name="month" id="month" class="span2 chzn-select">';                    
                    var t_month = {'1': '一月', '2': '二月', '3': '三月', '4': '四月', '5': '五月', '6': '六月', '7': '七月', '8': '八月', '9': '九月', '10': '十月', '11': '十一月', '12': '十二月'};
                    var selected = '';
                    for(var i in t_month){
                    	selected = '';
                    	if (i ==data.result.month ) {
                    		selected = 'selected=selected';
                    	}
                    	selectname += '<option ' + selected + ' value="'+ t_month[i] +'">'+ t_month[i] +'</option>';
                    }
                    selectname +=  '</select></div></div></div>';
                    msg['title']='责任会计审核';
                    msg['message']=selectname;
                    //弹出框
                    Vtiger_Helper_Js.showDialogBox(msg).then(function(e){
                        //var thisInstance = this;
                        if(e){
                            geturl('responsibility_accounting',name);
                        }
                    },function(error, err) {});
                }else{ //不需要指定，直接审核
                    msg['title']='审核';
                    msg['message']='确定要审核当前的节点'+name;
                    //弹出框
                    Vtiger_Helper_Js.showDialogBox(msg).then(function(e){
                        if(e){
                            geturl(e,name);
                        }
                    },function(error, err) {});
                }
            });
        });

        //提取公共方法，用于审核
        function geturl(e,name){
            var params={};
            var info='节点'+name+'审核成功';
            //周海 20161213
            if(e=='responsibility_accounting'){
                params['examine'] = $("#examine").val();  // 审核状态
                params['month']   = $("#month").val();    // 认证认证月份
            }

            var d={};
            params['record'] = $('#recordid').val();
            params['stagerecordid'] = $('#stagerecordid').val();
            params['action'] = 'SaveAjax';
            params['module'] = 'SalesorderWorkflowStages';
            params['mode'] = 'updateSalseorderWorkflowStages';
            params['src_module'] = app.getModuleName();
            params['checkname'] = $('#backstagerecordname').val();

            d.data=params;
            d.type = 'GET'; //ie9下post请求是失败的，如果get可以的请修改

            AppConnector.request(d).then(
                function(data){
                    if(data.success==true){
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
                                Vtiger_Helper_Js.showMessage({type:'success',text:info});
                            },
                            function(){}
                        );
                    }else{
                        Vtiger_Helper_Js.showMessage({type:'error',text:'审核失败,原因'+data.error.message});
                    }
                },function(){}
            );
        }

        function bindselectchange(){
            $('#customer').on('change',function(){
                if($(this).val()>0){
                    $('.assignservice').removeClass('disabled');
                }else{
                    $('.assignservice').addClass('disabled');
                }
            });
        }
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
		//this.bindSalesorderProjectTasksrel();
		//

		
		$('.showproduct').click(function(){
			var id=$(this).data('id');
			UE.getEditor(id).ui.setFullScreen(true);
		})
		
		$('.editproduct').click(function(){
			//alert($('#recordId').val());
			
			var progressIndicatorElement = jQuery.progressIndicator({ 'message' : '产品信息加载中...','blockInfo':{'enabled':true }});
			var id=$(this).data('id'),urlParams = 'module=SalesOrder&view=ListAjax&mode=edit&relate=product';
			
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
										if(data['result']['flag']) {
											Vtiger_Helper_Js.showMessage({type:'error',text:'已审核的产品信息不支持编辑'});
										} else {
											window.location.reload();	
										}
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
			var msg={'message':"是否要给工单阶段<"+name+">添加备注？",};
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
		
		
		
		
		
	}
})