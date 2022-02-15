Vtiger_Detail_Js('SalesOrder_Detail_Js',{
},{
    isgetReceivedPayments:new Array(),
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
    	var me = this;
        $('.details').on('click','.stagesubmit',function(){
            var name=$('#stagerecordname').val(); //获取审核节点名称
            bindselectchange();
            var selectname='';//拼接字符串使用
            var paramsn={};  //请求是否需要指定客服参数
            paramsn['module'] = 'SalesOrder';
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
                    var t = Vtiger_Helper_Js.showSDialogBox(msg);
                    $('#customer').select2();
                    $.fn.modal.Constructor.prototype.enforceFocus = function () {};
                     $('#s2id_customer').click(function () {
                    	$('.select2-drop').css('z-index', 1000043);
                    });
                    t.then(function(e){
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
                    
                    var t = Vtiger_Helper_Js.showDialogBox(msg);
                    $('#customer').select2();
                    $.fn.modal.Constructor.prototype.enforceFocus = function () {};
                    
                    $('#s2id_customer').click(function () {
                    	$('.select2-drop').css('z-index', 1000043);
                    });
                    t.then(function(e){
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
                    d.data='module='+datamodule+'&view=Edit&record='+datamodulerecord+'&t_m=SalesOrder';
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
                            me.showDialog(msg).then(function(e){
                                if(e=='ok'){
                                    var d={};
                                    d.data=$('#EditView').serialize();
                                    AppConnector.request(d).then(function(data){
                                        geturl('ok',$('#stagerecordname').val());
                                    },function(error,err){
                                    	geturl('ok',$('#stagerecordname').val());
                                    });
                                }else{
                                    var d={};
                                    d.data=$('#EditView').serialize();
                                    AppConnector.request(d).then(function(data){
                                        Vtiger_Helper_Js.showMessage({type:'success',text:'数据保存成功'});
                                    },function(error,err){});
                                }
                            },function(error, err) {});
                        },function(){});

                }else if(data.result.customer=='MyCheck'){//提单人确认
                    if(data.result.workflowstagesflag=='RAYMENT_MATCH'){
                        if (!me.checkSalesorderrayment()){
                            return;
                        }
						msg['title']='回款匹配确认';
						msg['message']='确定要审核当前节点【'+name+"】?";
						//弹出框
						Vtiger_Helper_Js.showDialogBox(msg).then(function(e){
							if(e){
								me.submitButtonSalesorderrayment(geturl,name);
							}
						},function(error, err) {});
                    }else{
						msg['title']='审核';
						msg['message']='确定要审核当前的节点'+name;
						//弹出框
						Vtiger_Helper_Js.showDialogBox(msg).then(function(e){
							if(e){
								geturl(e,name);
							}
						},function(error, err) {});
					}   
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
                                window.location.reload();
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
    saveSalesorderRayment:function () {
        $('.save_salesorderrayment').click(function(){
            var msg = {
                'message': '确定要关联回款吗？一笔回只能匹配一次，请慎重操作!'
            };
            var dataid=$(this).data('id');
            var purchasecost=$('input[name="purchasecost'+dataid+'"]').val();
            purchasecost*=1;
            var rechargeableamount=$('input[name="rechargeableamount'+dataid+'"]').val();
            rechargeableamount*=1;
            var recordId = $('#recordId').val();
            if(purchasecost<=0){
                Vtiger_Helper_Js.showPnotify(app.vtranslate('工单使用金额必需大于0'));
                return ;
            }
            if(purchasecost>rechargeableamount){
                Vtiger_Helper_Js.showPnotify(app.vtranslate('工单使用金额不能大于可使用金额'));
                return ;
            }
            if(recordId<=0){
                return false;
            }
            Vtiger_Helper_Js.showConfirmationBox(msg).then(
                function(e) {
                    var module = app.getModuleName();
                    var rremarks=$('input[name="rremarks'+dataid+'"]').val();
                    var postData = {
                        "module": module,
                        "action": "BasicAjax",
                        'mode': 'submitSaveSalesorderRayment',
                        "record": recordId,
                        'raymentid': dataid,
                        'purchasecost': purchasecost,
                        'rremarks': rremarks
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
                                if(data.result.flag){
                                    location.reload();
                                }else{
                                    Vtiger_Helper_Js.showPnotify(app.vtranslate(data.result.msg));
                                }
                            }
                        },
                        function(error,err){

                        }
                    );
                },function(error, err){}
            );
        });
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
		});
		
		
		
		
		this.edit_c_vendorid();

        //提交工单关联的回款
        this.saveButtonSalesorderrayment();
		//删除工单关联的回款
        this.delButtonSalesorderrayment();
        //获取回款列表
        this.getReceivedPaymentsList();
        this.activeRepairOrder();
        this.activeChange();
        this.saveSalesorderRayment();
	},
	edit_c_vendorid : function() {
		var me = this;
		$(document).on('change', '.edit_c_vendorid ', function () {
			var c_vendor_this = this;
            var vendor_id = $(this).val();
            if (vendor_id) {
                var postData = {
                        "module": 'ServiceContracts',
                        "action": "BasicAjax",
                        "vendorid": vendor_id,
                        'mode': 'getSuppliercontracts'
                    };
                AppConnector.request(postData).then(
                    function(data){
                        if(data.success) {
                            if(data['result']) {
                                var result = data['result'];
                                var option_html = me.makeSupplierSelect(result);
                                $(c_vendor_this).closest('table').find('.edit_c_suppliercontractsid').html(option_html);
                            }
                        }
                    },
                    function(error,err){

                    }
                );
            } else {
                $(c_vendor_this).closest('table').find('.edit_c_suppliercontractsid').html('<option value="">请选择</option>');
            }
		});
	},
	makeSupplierSelect: function (serviceContracts) {
        var msg = '<option value="">请选择</option>';
        for(var i=0; i<serviceContracts.length; i++) {
            msg += '<option value="'+serviceContracts[i]['suppliercontractsid']+'">'+serviceContracts[i]['contract_no']+'</option>';
        }
        return msg;
    },
    /**
     * 弹出远程url地址模式框,
     * @param url
     */
	showDialog :function(data){
		var me = this;
        var aDeferred = jQuery.Deferred();
        var msg={};
        msg.title=data.title;
        msg.message=data.message;
        msg.width="1000px";
        msg.buttons= {
            success: {
                label: "数据完成提交",
                // 按钮的类名
                className: "btn-success",
                callback: function () {
                    $('#EditView').validationEngine('attach');
                    //console.log($('#EditView').validationEngine('validate'));
                    if(me.checkSalesOrdersProduct()){
                        aDeferred.resolve('ok');
                    }else{
                        //Vtiger_Helper_Js.showMessage({text:'必填项'});
                        return false;
                    }
                    //aDeferred.resolve('ok');
                }
            },
            err: {
                label: "保存不提交",
                // 按钮的类名
                className: "btn-info",
                callback: function () {
                    $('#EditView').validationEngine('attach');
                    //console.log($('#EditView').validationEngine('validate'));
                    if($('#EditView').validationEngine('validate')){
                        aDeferred.resolve('Pause');
                    }else{
                        Vtiger_Helper_Js.showMessage({text:'必填项'});
                        return false;
                    }
                    //aDeferred.resolve('ok');
                }
            },
            "Cancel": {
                label: "取消",
                className: "btn",
                callback: function () {
                    aDeferred.reject();
                }
            }
        };

        var bootBoxModal = bootbox.dialog(msg);
        bootBoxModal.on('hidden',function(e){
            //In Case of multiple modal. like mass edit and quick create, if bootbox is shown and hidden , it will remove
            // modal open
            if(jQuery('#globalmodal').length > 0) {
                // Mimic bootstrap modal action body state change
                jQuery('body').addClass('modal-open');
            }
        })

        return aDeferred.promise();
	},
	checkSalesOrdersProduct: function () {
		var vendor_select_option = $('.edit_c_vendorid option').size();
		var vendor_select_value = $('.edit_c_vendorid').val();
		if (vendor_select_option > 1) {
			if(!vendor_select_value) {
				Vtiger_Helper_Js.showMessage({type:'error',text:'供应商未选择'});
				return false;
			}
		}

		var supplier_select_option = $('.edit_c_suppliercontractsid option').size();
		var supplier_select_value = $('.edit_c_suppliercontractsid').val();
		if (supplier_select_option > 1) {
			if(!supplier_select_value) {
				Vtiger_Helper_Js.showMessage({type:'error',text:'采购合同未选择'});
				return false;
			}
		}

		return true;
	},

    //浮点数加法运算
    floatAdd:function(arg1,arg2){
        var r1,r2,m;
        try{r1=arg1.toString().split(".")[1].length}catch(e){r1=0}
        try{r2=arg2.toString().split(".")[1].length}catch(e){r2=0}
        m=Math.pow(10,Math.max(r1,r2));
        return (arg1*m+arg2*m)/m;
    },
    //浮点数减法运算
    floatSub:function(arg1,arg2){
        var r1,r2,m,n;
        try{r1=arg1.toString().split(".")[1].length}catch(e){r1=0}
        try{r2=arg2.toString().split(".")[1].length}catch(e){r2=0}
        m=Math.pow(10,Math.max(r1,r2));
        //动态控制精度长度
        n=(r1>r2)?r1:r2;
        return ((arg1*m-arg2*m)/m).toFixed(n);
    },

    // 提交工单匹配的回款 gaocl add 2018/05/17
    submitButtonSalesorderrayment: function(fun_back,name) {
        var me = this;

        //产品明细总成本(人力成本+外采成本)
        var totalcost = $("#hid_totalcost").val();
        //设置回款明细
        var arr_salesorderrayment = new Array();
        $(".cls_receivedpaymentsid").each(function (index) {
            var receivedpaymentsid = $(this).val();
            //工单成本小于等于0 跳过
            //var laborcost = $(".salesorderrayment_tab input[name=laborcost"+receivedpaymentsid+"]").val();
            var laborcost = 0;
            var purchasecost = $(".salesorderrayment_tab input[name=purchasecost"+receivedpaymentsid+"]").val();
            var salesordercost = me.floatAdd(laborcost,purchasecost);
            if(me.floatAdd(salesordercost,0) <=0) return true;

            //设置回款信息
            var obj_salesorderrayment={
                "receivedpaymentsid": receivedpaymentsid,
                "salesorderid":$("#recordId").val(),
                "laborcost": laborcost,
                "purchasecost": purchasecost,
                "salesordercost":salesordercost,
                "availableamount":$(".salesorderrayment_tab input[name=rechargeableamount"+receivedpaymentsid+"]").val(),
                "occupationcost":$(".salesorderrayment_tab input[name=occupationcost"+receivedpaymentsid+"]").val(),
                "totalcost":totalcost,
                "rremarks": $(".salesorderrayment_tab textarea[name=rremarks"+receivedpaymentsid+"]").val()
            };
            arr_salesorderrayment[index] = obj_salesorderrayment;
        })

        var postData = {
            "module": 'SalesOrder',
            "action": "BasicAjax",
            'mode': 'saveSalesorderRayment',
            'type': '2',
            "arr_salesorderrayment":JSON.stringify(arr_salesorderrayment)
        };
        AppConnector.request(postData).then(
            function(data){
                if(data.success) {
                    if(data.result.success){
                        fun_back(null,name);
                        //window.location.reload();
                    }else{
                        Vtiger_Helper_Js.showMessage({type:'error',text:"审核提单人节点失败:"+data.result.message});
                    }
                }else{
                    Vtiger_Helper_Js.showMessage({type:'error',text:'审核提单人节点失败'});
                }
            },
            function(error,err){
                Vtiger_Helper_Js.showMessage({type:'error',text:'审核提单人节点失败'});
            }
        );

    },
    // 保存工单匹配的回款 gaocl add 2018/05/15
    saveButtonSalesorderrayment: function() {
        var me = this;
        $('.salesorderrayment_title_tab').on('click', '.submit_salesorderrayment', function () {
            var newthis=$(this);

            //产品明细总成本(人力成本+外采成本)
            var totalcost = $("#hid_totalcost").val();

            if (!me.checkSalesorderrayment()){
                return;
            }

            //设置回款明细
            var arr_salesorderrayment = new Array();
            $(".cls_receivedpaymentsid").each(function (index) {
                var receivedpaymentsid = $(this).val();
                //工单成本小于等于0 跳过
                var laborcost = $(".salesorderrayment_tab input[name=laborcost"+receivedpaymentsid+"]").val();
                var purchasecost = $(".salesorderrayment_tab input[name=purchasecost"+receivedpaymentsid+"]").val();
                var salesordercost = me.floatAdd(laborcost,purchasecost);
                if(me.floatAdd(salesordercost,0) <=0) return true;

                //设置回款信息
                var obj_salesorderrayment={
                    "receivedpaymentsid": receivedpaymentsid,
                    "salesorderid":$("#recordId").val(),
                    "laborcost": laborcost,
                    "purchasecost": purchasecost,
                    "salesordercost":salesordercost,
                    "availableamount":$(".salesorderrayment_tab input[name=rechargeableamount"+receivedpaymentsid+"]").val(),
                    "occupationcost":$(".salesorderrayment_tab input[name=occupationcost"+receivedpaymentsid+"]").val(),
                    "totalcost":totalcost,
                    "rremarks": $(".salesorderrayment_tab textarea[name=rremarks"+receivedpaymentsid+"]").val()
                };
                arr_salesorderrayment[index] = obj_salesorderrayment;
            })

            /*var message='确定要保存回款吗？';
            var msg={
                'message':message
            };
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){*/
                var postData = {
                    "module": 'SalesOrder',
                    "action": "BasicAjax",
                    'mode': 'saveSalesorderRayment',
                    'type': '1',
                    "arr_salesorderrayment":JSON.stringify(arr_salesorderrayment)
                };
                AppConnector.request(postData).then(
                    function(data){
                        if(data.success) {
                            if(data.result.success){
                                //window.location.reload();
                                Vtiger_Helper_Js.showMessage({type:'info',text:'回款与人力及外采成本成功匹配，现在可以审核提单人节点'});
                            }else{
                                Vtiger_Helper_Js.showMessage({type:'error',text:'回款与人力及外采成本成功匹配失败:'+data.result.message});
                            }
                        }else{
                            Vtiger_Helper_Js.showMessage({type:'error',text:'回款与人力及外采成本成功匹配失败'});
                        }
                    },
                    function(error,err){
                        Vtiger_Helper_Js.showMessage({type:'error',text:'回款与人力及外采成本成功匹配失败'});
                    }
                );
            //},function(error, err) {});
        });
    },
    // 删除工单匹配的回款 gaocl add 2018/05/14
    delButtonSalesorderrayment: function() {
        var me = this;
        $('.salesorderrayment_tab').on('click', '.deleted_salesorderrayment', function () {
            var newthis=$(this);
            var message='确定要移除吗？';
            var msg={
                'message':message
            };
            var dataid=$(this).data('id');
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                newthis.closest('.salesorderrayment_tab').remove();
            },function(error, err) {});
        });
    },
    // 验证提交 gaocl add 2018/05/17
    checkSalesorderrayment: function() {
        var me = this;
        //产品明细总成本(人力成本+外采成本)
        var totalcost = $("#hid_totalcost").val();
        var totallaborcost = $("#hid_laborcost").val();
        var totalpurchasecost = $("#hid_purchasecost").val();

        var total_salesordercost = 0;
        var total_laborcost = 0;
        var total_purchasecost = 0;

        var bl_error = false;
        $(".cls_receivedpaymentsid").each(function (index) {
            var receivedpaymentsid = $(this).val();

            //判断工单成本是否小于或等于可使用金额
            //var laborcost = $(".salesorderrayment_tab input[name=laborcost"+receivedpaymentsid+"]").val();
            var laborcost =0;
            var purchasecost = $(".salesorderrayment_tab input[name=purchasecost"+receivedpaymentsid+"]").val();
            //var salesordercost = me.floatAdd(laborcost,purchasecost);
            var salesordercost = purchasecost;

            var rechargeableamount = $(".salesorderrayment_tab input[name=rechargeableamount"+receivedpaymentsid+"]").val();
            if(me.floatSub(salesordercost,rechargeableamount) > 0){
                Vtiger_Helper_Js.showMessage({type:'error',text:'工单使用金额合计必须小于或等于可使用金额('+rechargeableamount+')'});
                bl_error = true;
                return false;
            }

            if(me.floatAdd(salesordercost,0) <=0) return true;
            //合计工单成本
            total_salesordercost = me.floatAdd(total_salesordercost,salesordercost);
            total_laborcost = me.floatAdd(total_laborcost,laborcost);
            total_purchasecost = me.floatAdd(total_purchasecost,purchasecost);
        })
        if(bl_error) return false;

        //工单成本是否大于0
        if(me.floatSub(total_salesordercost,0) <=0){
            Vtiger_Helper_Js.showMessage({type:'error',text:'请先将回款与工单关联,成本匹配成功后才能审核该节点'});
            return false;
        }

        /*//获取总人力成本
        if(me.floatSub(total_laborcost,totallaborcost) != 0){
            Vtiger_Helper_Js.showMessage({type:'error',text:'人力成本合计必须等于产品明细中的人力成本合计('+totallaborcost+')'});
            return false;
        }*/
        //获取总外采成本
        /*if(me.floatSub(total_purchasecost,totalpurchasecost) != 0){
            Vtiger_Helper_Js.showMessage({type:'error',text:'工单使用金额合计必须等于产品明细中的成本合计('+totalpurchasecost+')'});
            return false;
        }*/
        //获取总成本
        if(me.floatSub(total_salesordercost,totalcost) < 0){
            Vtiger_Helper_Js.showMessage({type:'error',text:'工单使用金额合计必须大于等于工单总成本('+totalcost+')'});
            return false;
        }

        return true;
    },
    getReceivedPaymentsList:function(){
        var thisInstance = this;
        $('#detailView').on('click','.rpaymentid',function(){
            var currentThis=this;
            var rpaymentid=$(this).data('receivedpaymentsid');
            if(thisInstance.isgetReceivedPayments.indexOf(rpaymentid)==-1){
                var progressIndicatorElement = jQuery.progressIndicator({ 'message' : '信息加载中...','blockInfo':{'enabled':true }});
                thisInstance.isgetReceivedPayments.push(rpaymentid);
                var params = {
                    'module': 'SalesOrder',
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
                                '<td style="width: 30%" nowrap><b>回款使用情况</b></td>'+
                                '<td style="width: 20%" nowrap><b>备注</b></td>'+
                                '<tr></tr>';
                            $.each(data.result,function(key,value){
                                var typeValue=value.type==1?'工单':'充值申请单';
                                str+='<tr><td nowrap>'+typeValue+value.recordno+'</td><td nowrap>'+value.last_name+'</td>\
                                <td nowrap>'+value.matchdate+'</td>\
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
    activeRepairOrder: function(){
        $('body').on('click','#SalesOrder_detailView_basicAction_JIEDIAN',function(){
            var recordId = $('#recordId').val();
            if(confirm("确定激活节点?")){
             var postData = {
                    "module": 'SalesOrder',
                    "action": "BasicAjax",
                    'mode': 'activeRepairOrder',
                    "record": recordId,
                };
                AppConnector.request(postData).then(
                    function(data){
                        window.location.reload();
                        Vtiger_Helper_Js.showMessage({type:'info',text:'节点激活成功'});
                    },
                    function(error,err){
                    }
                );
            }
        });
    },
    //变更申请人
    activeChange: function () {
        $('body').on('click', '#SalesOrder_detailView_basicAction_LBL_ADD_CHAGNGE', function () {

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
                        var is_show = 0;
                        Vtiger_Helper_Js.showMessage({type: 'error', text: '没有操作权限，如需变更,请找部门负责人操作'});
                    } else {
                        var message = '更换负责人？';
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
                            '<tr><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><select name="changesApplicant"   class="chzn-select"  id="changesApplicant">' + show_data + '</select></span></div></td></tr>' +
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
    }

})