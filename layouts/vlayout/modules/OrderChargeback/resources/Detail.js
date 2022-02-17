Vtiger_Detail_Js('OrderChargeback_Detail_Js',{
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
    /**
     * young.yang 2015-05-21
     * 覆盖父级的审核按钮动作，主要是为了指定审核客服使用
     **/
    bindStagesubmit:function(){

        $('.details').on('click','.stagesubmit',function(){
            var name=$('#stagerecordname').val(); //获取审核节点名称

            bindselectchange();
            var selectname='';//拼接字符串使用
            var paramsn={};  //请求是否需要指定客服参数
            paramsn['module'] = 'OrderChargeback';
            paramsn['action'] = 'ChangeAjax';
            paramsn['record'] = $('#stagerecordid').val();

            var sname={};
            sname.data=paramsn;
            sname.type = 'GET';
            var  msg={};
            //获取当前的节点是否需要指定客服
           AppConnector.request(sname).then(function(data){
                if(data.result.customer=='ServiceCheck'){ //需要指定客服
                    selectname+='<div class="form-horizontal"><div class="control-group"><label class="control-label" for="customer">指定下个节点审核客服</label><div class="controls"><select name="customer" id="customer" class="span2 chzn-select"><option value="0">选择客服</option>';
                    //绑定客服人员
                    $.each(data.result.names,function(id,last_name){
                        selectname+='<option value="'+this.id+'">'+this.last_name+'</option>';
                    });
                    selectname+='</select><span class="help-inline errormsg"></span></div></div><div class="control-group"><div class="controls"><label class="checkbox"><input type="checkbox" class="assigncustomer" name="assigncustomer">同时将客服分配给客户</label></div></div></div><p class="text-left" style="margin-top:10px;color:red;">如当前节点不需要分配客服,点击 直接审核不指定客服 跳过，选择要分配的客服后下一步会有客服审核！</p>';

                    msg['title']='是否指定客服审核';
                    msg['message']=selectname;
                    //弹出框
                    Vtiger_Helper_Js.showSDialogBox(msg).then(function(e){
                        //var thisInstance = this;
                        if(e){
                            geturl(e,name);
                        }
                    },function(error, err) {});
                }else if(data.result.customer=='NextCheck'){  //指定下级审核
                    selectname+='<div class="form-horizontal"><div class="control-group"><label class="control-label" for="customer">指定下个节点审核</label><div class="controls"><select name="customer" id="customer" class="span2"><option value="0">选择下属</option>';
                    //绑定客服人员
                    $.each(data.result.names,function(id,last_name){
                        selectname+='<option value="'+this.id+'">'+this.last_name+'</option>';
                    });
                    selectname+='</select></p>';

                    msg['title']='是否指定下一个节点';
                    msg['message']=selectname;
                    //弹出框
                    Vtiger_Helper_Js.showDialogBox(msg).then(function(e){
                        //var thisInstance = this;
                        if(e){
                            geturl('assign',name);
                        }
                    },function(error, err) {});
                }else if(data.result.customer=='DataCheck'){  //数据审核
                    var d={};
                    var html = $('<div></div>');
                    var datamodule=$('#datamodule').val();
                    var datamodulerecord=$('#datamodulerecord').val();
                    d.data='module='+datamodule+'&view=Edit&record='+datamodulerecord;
                    d.type="get";
                    AppConnector.request(d).then(
                        function(data){
                            html.html(data);
                            html.find('div').remove('.contentHeader').remove('.row-fluid .pull-right');
                            msg['title']='审核信息';
                            msg['message']=html.html();
                            //弹出框,合同里面有这个东西，是否要修改。
                            if(datamodule!='SalesorderProductsrel'){
                                $.getScript('layouts/vlayout/modules/'+datamodule+'/resources/Edit.js?&v=Beta 1.0.1',function(){
                                    eval('var aa=new '+datamodule+'_Edit_Js'); aa.registerEvents();
                                    //SalesOrder_Edit_Js.loadWidgets();
                                });
                            }

                            //显示对话框-》验证通过-》post数据-》审核节点
                            Vtiger_Helper_Js.showDialog(msg).then(function(e){
                                if(e=='ok'){
                                    var d={};
                                    d.data=$('#EditView').serialize();
                                    AppConnector.request(d).then(function(data){
                                        geturl('ok',$('#stagerecordname').val());
                                    },function(error,err){});
                                }else{
                                    var d={};
                                    d.data=$('#EditView').serialize();
                                    AppConnector.request(d).then(function(data){
                                        Vtiger_Helper_Js.showMessage({type:'success',text:'数据保存成功'});
                                    },function(error,err){});
                                }
                            },function(error, err) {});
                        },function(){});
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
            //young 2015-05-21 加入判断是否点击的指定分配客服
            if(e=='assign'){
                if($("#customer").val()>0){
                    info=name+'下个节点指定'+$("#customer").find("option:selected").text();
                    params['customer']=$("#customer").val();  //用户id
                    params['customername']=$("#customer").find("option:selected").text(); //用户名
                    params['assigncustomer'] = $('.assigncustomer').val();
                }
                /*else{
                    Vtiger_Helper_Js.showMessage({type:'error',text:'必须指定审核客服'});
                    return;
                }*/
            }else{ //不需要指定的参数设置
                params['customer']=0;
                params['customername']='';
            }//end

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
		this.bindSalesorderProjectTasksrel();
		//

		
		$('.showproduct').click(function(){
			var id=$(this).data('id');
			UE.getEditor(id).ui.setFullScreen(true);
		})
		
		$('.editproduct').click(function(){
			//alert($('#recordId').val());
			
			var progressIndicatorElement = jQuery.progressIndicator({ 'message' : '产品信息加载中...','blockInfo':{'enabled':true }});
			var id=$(this).data('id'),urlParams = 'module=OrderChargeback&view=ListAjax&mode=edit&relate=product';
			
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
		
		
		
		
		
	}
})