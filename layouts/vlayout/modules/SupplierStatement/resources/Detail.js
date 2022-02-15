/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("SupplierStatement_Detail_Js",{

	//It stores the Account Hierarchy response data
	accountHierarchyResponseCache : {},

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
    bindStagesubmit:function(){
        $('.details').on('click','.stagesubmit',function(){
            var msg={
                'message':"确定要审核工单阶段"+name+"?",
            };
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){

                var params={};
                params['record'] = $('#recordId').val();
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
                params['action'] = 'SaveAjax';
                params['module'] = 'SalesorderWorkflowStages';
                params['mode'] = 'submitremark';
                params['src_module'] = app.getModuleName();
                var d = {};
                d.data = params;
                console.log(params);
                AppConnector.request(d).then(
                    function (data) {
                        if (data.success == true) {
                            Vtiger_Helper_Js.showMessage({type: 'success', text: '备注添加成功'});
                            location.reload();
                        } else {
                            Vtiger_Helper_Js.showMessage({type: 'error', text: '备注添加失败,原因' + data.error.message});
                        }
                    }, function () {}
                );
            });
        });

    },

    registerEvents: function() {
        this._super();
        this.files_deliver_workflowNoM();
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


});

