/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("Staypayment_Detail_Js",{
	
	//It stores the Account Hierarchy response data
	accountHierarchyResponseCache : {},
	doBlurEvent:true,
	/*
	 * function to trigger Account Hierarchy action
	 * @param: Account Hierarchy Url.
	 */
	triggerAccountHierarchy : function(accountHierarchyUrl) {
		Accounts_Detail_Js.getAccountHierarchyResponseData(accountHierarchyUrl).then(
			function(data) {
				Accounts_Detail_Js.displayAccountHierarchyResponseData(data);
			}
		);
		
	},
	
	/*
	 * function to get the AccountHierarchy response data
	 */
	getAccountHierarchyResponseData : function(params) {
		var aDeferred = jQuery.Deferred();
		
		//Check in the cache
		if(!(jQuery.isEmptyObject(Accounts_Detail_Js.accountHierarchyResponseCache))) {
			aDeferred.resolve(Accounts_Detail_Js.accountHierarchyResponseCache);
		} else {
			AppConnector.request(params).then(
				function(data) {
					//store it in the cache, so that we dont do multiple request
					Accounts_Detail_Js.accountHierarchyResponseCache = data;
					aDeferred.resolve(Accounts_Detail_Js.accountHierarchyResponseCache);
				}
			);
		}
		return aDeferred.promise();
	},
	
	/*
	 * function to display the AccountHierarchy response data
	 */
	displayAccountHierarchyResponseData : function(data) {
        var callbackFunction = function(data) {
            app.showScrollBar(jQuery('#hierarchyScroll'), {
                height: '200px',
                railVisible: true,
                alwaysVisible: true,
                size: '6px'
            });
        }
        app.showModalWindow(data, function(data){
            if(typeof callbackFunction == 'function'){
                callbackFunction(data);
            }
        });
	}
},{
	//Cache which will store account name and whether it is duplicate or not
	accountDuplicationCheckCache : {},

	getDeleteMessageKey : function() {
		return 'LBL_RELATED_RECORD_DELETE_CONFIRMATION';
	},
	
	isAccountNameDuplicate : function(params) {
		var thisInstance = this;
		var accountName = params.accountName;
		var aDeferred = jQuery.Deferred();

		var analyzeResponse = function(response){
			if(response['success'] == true) {
				aDeferred.reject(response['message']);
			}else{
				aDeferred.resolve();
			}
		}

		if(accountName in thisInstance.accountDuplicationCheckCache) {
			analyzeResponse(thisInstance.accountDuplicationCheckCache[accountName]);
		}else{
			Vtiger_Helper_Js.checkDuplicateName(params).then(
				function(response){
					thisInstance.accountDuplicationCheckCache[accountName] = response;
					analyzeResponse(response);
				},
				function(response) {
					thisInstance.accountDuplicationCheckCache[accountName] = response;
					analyzeResponse(response);
				}
			);
		}
		return aDeferred.promise();
	},

	saveFieldValues : function (fieldDetailList) {
		var thisInstance = this;
		var targetFn = this._super;
		
		var fieldName = fieldDetailList.field;
		if(fieldName != 'accountname') {
			return targetFn.call(thisInstance, fieldDetailList);
		}

		var aDeferred = jQuery.Deferred();
		fieldDetailList.accountName = fieldDetailList.value;
		fieldDetailList.recordId = this.getRecordId();
		this.isAccountNameDuplicate(fieldDetailList).then(
			function() {
				targetFn.call(thisInstance, fieldDetailList).then(
					function(data){
						aDeferred.resolve(data);
					},function() {
						aDeferred.reject();
					}
				);
			},
			function(message) {
				var form = thisInstance.getForm();
				var params = {
					title: app.vtranslate('JS_DUPLICATE_RECORD'),
					text: app.vtranslate(message),
					width: '35%'
				};
				Vtiger_Helper_Js.showPnotify(params);
				form.find('[name="accountname"]').closest('td.fieldValue').trigger('click');
				aDeferred.reject();
			}
		)
		return aDeferred.promise();
	},

	checkform:function (data) {
        var instanceThis=this;
		$("#Staypayment_detailView_basicAction_LBL_ADD_ONLINE_SIGN").click(function () {
            var act=$(this).data('act');
            var message='<h3>代付款证明在线签收</h3>' +
				'<input type="text" id="inputusercode" style="margin-left:40px;" placeholder="请输入工号"/><span id="displayname" style="margin-left: 10px;"></span><input id="usercode" type="hidden"/><input id="username" type="hidden"/> <button id="submit">确定</button>';
            var windowwith=$(window).width();
            var windowheight=windowwith*0.25;
            var msg={
                'message':message,
                "width":windowwith
            };

            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                var usercode = $("#usercode").val();
                if(!usercode){
                    alert('请输入工号');
                    return false;
                }

                var params={};
                params['record'] = $('#recordId').val();
                params['action'] = 'BasicAjax';
                params['module'] = 'Staypayment';
                params['mode'] = 'savesignimage';
                params['id'] = $("#usercode").val();
                params['image'] = $('#canvssign').jSignature("getData", "default").toString();
                AppConnector.request(params).then(
                    function(data) {
                        window.location.reload(true);
                    },
                    function(error,err){
                        window.location.reload(true);
                    }
                );
            },function(error, err) {});
            $('.modal-content .modal-body').append('<div id="canvssign" ondragstart="return false" oncontextmenu="return false" onselectstart="return false" oncopy="return false" oncut="return false" style="-moz-user-select:none;width:100%;height:'+windowheight+'px; min-height:none; border:1px solid #ccc;margin:10px 0 0;overflow:hidden;"></div>');
            $('.modal-content .modal-body').css({overflow:'hidden'});
            $('#canvssign').jSignature();
            $('<input type="button" value="清空" style="float:left;margin-left:'+(windowwith/2)+'px;">').bind('click', function(e){
                $('#canvssign').jSignature('reset')
            }).appendTo('.modal-content .modal-footer');
        })
    },
    bindStagesubmit:function(){
        // $(".stagesubmit").mouseenter(function(){
        //     doBlurEvent=false;
        // });
        // $(".stagesubmit").mouseleave(function(){
        //     doBlurEvent=true;
        //     $(".details").find("input[name='staypaymentjine']").bind("blur");
        // });

        $('.details').on('click','.stagesubmit',function(){
            var staypaymenttype = $("#staypaymenttype").val();
            var currencytype = $("select[name='currencytype']").val();
            var staypaymentjine = $("input[name='staypaymentjine']").val();
            var staypaymentname = $("input[name='staypaymentname']").val();
            var startdate = $("input[name='startdate']").val();
            var enddate = $("input[name='enddate']").val();
            // if(!staypaymentname){
            //     Vtiger_Helper_Js.showMessage({type:'error',text:'代付款客户必填'});
            //     return;
            // }
            // if($("#nowWorkFlowFlag").val()!='CFO'){
            //     if(staypaymenttype=='fixation'){
            //         if(!currencytype || !staypaymentjine){
            //             Vtiger_Helper_Js.showMessage({type:'error',text:'请先输入代付款金额、货币类型'});
            //             return;
            //         }
            //     }else{
            //         if(!startdate || !enddate){
            //             Vtiger_Helper_Js.showMessage({type:'error',text:'代付款开始时间、代付款到期时间必填'});
            //             return;
            //         }
            //         var start = new Date(startdate.replace("-", "/").replace("-", "/"));
            //         var end = new Date(enddate.replace("-", "/").replace("-", "/"));
            //         if(start>end){
            //             Vtiger_Helper_Js.showMessage({type:'error',text:'代付款开始时间大于代付款到期时间'});
            //             return;
            //         }
            //         makeUp('startdate',startdate,'代付款开始时间');
            //         makeUp('enddate',enddate,'代付款结束时间');
            //     }
            // }

            var name=$('#stagerecordname').val();

            var msg={
                'message':"确定要审核工单阶段"+name+"?",
            };
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){

                var params={};
                params['record'] = $('#recordId').val();
                params['staypaymentjine'] = staypaymentjine;
                params['currencytype'] = currencytype;
                params['staypaymenttype'] = staypaymenttype;
                params['startdate'] = startdate;
                params['enddate'] = enddate;
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
                            Vtiger_Helper_Js.showMessage({type:'success',text:'审核成功'});
                            window.location.reload();
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
    },
    bindrejectall:function(){
//		$('.widgetContainer_workflows').on("click",'#rejectbutton',function(){
//			$('#test').toggle("fast");
//		});
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
                params['record'] = $('#recordId').val();                  //工单id
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
                        Vtiger_Helper_Js.showMessage({type:'success',text:'打回成功'});
                        window.location.reload();
                    }else{
                        Vtiger_Helper_Js.showMessage({type:'error',text:'操作失败,原因'+data.error.message});
                    }
                },function(){}
            );
        }
    },
    // supplyData:function() {
    //     $(".details").on('blur', "input[name='staypaymentname']", function () {
    //         var staypaymentname = $("input[name='staypaymentname']").val();
    //         if (!staypaymentname) {
    //             return;
    //         }
    //         makeUp('staypaymentname', staypaymentname);
    //     });
    //     $(".details").on('blur', "input[name='staypaymentjine']", function (doBlurEvent) {
    //         if(doBlurEvent){
    //             var staypaymentjine = $("input[name='staypaymentjine']").val();
    //             if (!staypaymentjine) {
    //                 $("input[name='staypaymentjine']").val('');
    //                 return;
    //             }
    //             var contracttotal = $("#contracttotal").val();
    //             var frameworkcontract = $("#frameworkcontract").val();
    //             if(frameworkcontract=='no' && ((Number(staypaymentjine)>Number(contracttotal)))){
    //                 console.log(Number(staypaymentjine));
    //                 console.log(Number(contracttotal));
    //                 $("input[name='staypaymentjine']").val('');
    //                 Vtiger_Helper_Js.showMessage({type: 'error', text: '代付款金额必须＜=合同总金额'});
    //                 return;
    //             }
    //             makeUp('staypaymentjine', staypaymentjine);
    //         }
    //     });
    //     $(".details").on('change', "select[name='currencytype']", function () {
    //         var currencytype = $("select[name='currencytype']").val();
    //         if (!currencytype) {
    //             return;
    //         }
    //         makeUp('currencytype', currencytype);
    //     });
    // },

    files_deliver_workflowNoM:function(){
        $('.details').on("click", '#realremarkbutton', function () {
            var remark = $('#remarkvalue');
            if (remark.val() == '') {
                remark.focus();
                return false;
            }
            var name = $('#stagerecordname').val();
            var msg = {'message': "是否要给工单阶段<" + name + ">添加备注？", };
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function (e) {
                var params = {};
                params['record'] = $('#recordId').val();//工单id
                params['isrejectid'] = $('#backstagerecordeid').val();
                params['isbackname'] = $('#backstagerecordname').val();
                params['reject'] = $('#remarkvalue').val();
                params['action'] = 'BasicAjax';
                params['module'] = 'Staypayment';
                params['mode'] = 'submitremark';
                var attach= new Array();
                $("#fileallexplain").find('input[name^="attachmentsid["]').each(function () {
                    attach.push($(this).val());
                });

                params['attachmentsid']=attach;
                params['src_module'] = app.getModuleName();
                var d = {};
                d.data = params;
                console.log(params);
                AppConnector.request(d).then(
                    function (data) {
                        if (data.success == true) {
                            Vtiger_Helper_Js.showMessage({type: 'success', text: '备注添加成功'});
                            // location.reload();
                        } else {
                            Vtiger_Helper_Js.showMessage({type: 'error', text: '备注添加失败,原因' + data.error.message});
                        }
                    }, function () {}
                );
            });
        });

    },

    /**
     * 重新提交
     */
    reSubmit:function(){
        $("#Staypayment_detailView_basicAction_LBL_RESUBMIT").click(function () {
            var msg = {'message': "是否要重新提交", };
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function (e) {
                var params = {};
                params['record'] = $('#recordId').val();//工单id
                params['action'] = 'BasicAjax';
                params['module'] = 'Staypayment';
                params['mode'] = 'reSubmit';
                var d = {};
                d.data = params;
                console.log(params);
                AppConnector.request(d).then(
                    function (data) {
                        if (data.success) {
                            Vtiger_Helper_Js.showMessage({type: 'success', text: '重新提交成功'});
                            location.reload();
                        } else {
                            Vtiger_Helper_Js.showMessage({type: 'error', text: '重新提交失败'});
                        }
                    }, function () {}
                );
            });
        });
    },

    getuploadZzFile:function(){
        if($('#explainfile').length>0){
            var module=$('#module').val();
            KindEditor.ready(function(K) {
                var uploadbutton = K.uploadbutton({
                    button : K('#uploadexplainButton')[0],
                    fieldName : 'Explainfile',
                    extraParams :{
                        __vtrftk:$('input[name="__vtrftk"]').val(),
                    },
                    url : 'index.php?module='+module+'&action=FileUpload&record=',
                    afterUpload : function(data) {
                        if (data.success ==true) {
                            $('.explaindelete').remove();
                            var str='<span class="label file'+data.result['id']+'" style="margin-left:5px;">'+data.result['name']+'&nbsp;<b class="deletefile" data-class="file'+data.result['id']+'" data-id="'+data.result['id']+'" title="删除文件" style="display:inline-block;width:12px;height:12px;line-height:12px;text-align:center">x</b>&nbsp;</span><input class="ke-input-text file'+data.result['id']+'" type="hidden" name="explainfile['+data.result['id']+']" id="explainfile" value="'+data.result['name']+'" readonly="readonly" /><input class="file'+data.result['id']+'" type="hidden" name="attachmentsid['+data.result['id']+']" value="'+data.result['id']+'">';
                            $("#fileallexplain").append(str);
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

    registerEvents: function() {
        this._super();
        this.checkform();
        this.scanUserCode();
        this.lookupUserCode();
        // this.supplyData();
        this.files_deliver_workflowNoM();
        this.reSubmit();
        this.getuploadZzFile();
        this.deleteuploadZzFile();
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
        /*bootBoxModal.on('hidden',function(e){
            if(jQuery('#globalmodal').length > 0) {
                jQuery('body').addClass('modal-open');
            }
        })
*/        return aDeferred.promise();
    },
    /**
     * 工牌扫码
     */
    scanUserCode:function(){
        $("body").on("keydown","#inputusercode",function(event){
            if(event.keyCode==13){
                var instanceThis=$(this);
                var userCode=instanceThis.val();
                console.log(userCode);
                if(userCode!=''){
                    $("#username").val('');
                    $("#usercode").val('');
                    var postData = {
                        "module": "Newinvoice",
                        "action": "BasicAjax",
                        "userCode": userCode,
                        'mode': 'getUserInfo'
                    };
                    /*var progressIndicatorElement = jQuery.progressIndicator({
                        'message' : '正在提交...',
                        'position' : 'html',
                        'blockInfo' : {'enabled' : true}
                    });*/
                    AppConnector.request(postData).then(
                        function(data){
                            //location.reload();
                            if(data.result.flag){
                                $("#username").val(data.result.data.last_name);
                                $("#usercode").val(data.result.data.id);
                                $("#displayname").text(data.result.data.last_name);
                                instanceThis.val('');
                            }else{
                                $("#inputusercode").val('');
                                alert(data.result.msg)

                            }
                        },
                        function(error,err){
                        }
                    );
                }
            }
        });
    },
    /**
     * 查找人员工号
     */
    lookupUserCode:function(){
        $("body").on("click","#submit",function(event){
            $("#displayname").html('');
            var instanceThis=$("#inputusercode");
            var userCode=instanceThis.val();
            console.log(userCode);
            if(userCode!=''){
                $("#username").val('');
                $("#usercode").val('');
                var postData = {
                    "module": "Newinvoice",
                    "action": "BasicAjax",
                    "userCode": userCode,
                    'mode': 'getUserInfo'
                };
                AppConnector.request(postData).then(
                    function(data){
                        if(data.result.flag){
                            $("#username").val(data.result.data.last_name);
                            $("#usercode").val(data.result.data.id);
                            $("#displayname").text(data.result.data.last_name);
                            instanceThis.val('');
                        }else{
                            $("#inputusercode").val('');
                            alert(data.result.msg)

                        }
                    },
                    function(error,err){
                    }
                );
            }
        });
    },

});

function makeUp(field,value,msg='') {
    var postData = {
        "module": "Staypayment",
        "action": "BasicAjax",
        "mode":"supplydata",
        "record": $('#recordId').val(),
        "value":value,
        "field":field
    };
    // var progressIndicatorElement = jQuery.progressIndicator({
    //     'message' : '亲,正在拼命处理,请耐心等待哟',
    //     'position' : 'html',
    //     'blockInfo' : {'enabled' : true}
    // });
    AppConnector.request(postData).then(
        function(data){
            // progressIndicatorElement.progressIndicator({
            //     'mode' : 'hide'
            // });
            if(data.result.success){
                Vtiger_Helper_Js.showMessage({type:'success',text:msg+'操作成功'});
            }else{
                if(data.result.msg){
                    msg=data.result.msg;
                }
                Vtiger_Helper_Js.showMessage({type:'error',text:'操作失败,'+msg});
            }
        },
        function(error,err){
        });
}