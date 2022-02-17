/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("ServiceContractsPrint_List_Js",{},{
	doStamp:function(ids){
        var module = app.getModuleName();
        var postData = {
            "module": module,
            "action": "ChangeAjax",
            "mode": "doStamp",
            "records": ids
        }
        var Message = app.vtranslate('正在处理......');
        var progressIndicatorElement = jQuery.progressIndicator({
            'message' : Message,
            'position' : 'html',
            'blockInfo' : {'enabled' : true}
        });
        AppConnector.request(postData).then(
            function(data){
                progressIndicatorElement.progressIndicator({
                    'mode' : 'hide'
                });
                //console.log(data);
                if(data.success) {
                    if(data.result.length>0){
                        //console.log(data);
                        var str='';
                        $.each(data.result,function(key,value){

                            str+=value['no']+'&nbsp;&nbsp;'+value['msg']+'<br>';
                        });
                        var  params = {
                            text : str,
                            title : ''
                        }
                        Vtiger_Helper_Js.showPnotify(params);
                    }
                } else {
                    var  params = {
                        text : app.vtranslate(data.message),
                        title : app.vtranslate('JS_LBL_PERMISSION')
                    }
                    Vtiger_Helper_Js.showPnotify(params);
                }
            },
            function(error,err){

            }
        );
	},
	registerChangeRecordClickEvent: function(){
		var _this=this;
		var listViewContentDiv = this.getListViewContentContainer();
        listViewContentDiv.on('click','.noclick',function(event){
            event.stopPropagation();
        });
		listViewContentDiv.on('click','.ChangeRecordButton',function(e){
			var elem = jQuery(e.currentTarget);
			var recordId = elem.closest('tr').data('id');
            var thisInstance = this;
            var message = app.vtranslate('确定要重新打印');
            Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
                function(e) {
                    var module = app.getModuleName();
                    var postData = {
                        "module": module,
                        "action": "ChangeAjax",
                        "mode": "changPrintStatus",
                        "record": recordId
                    }
                    var Message = app.vtranslate('正在处理......');
                    var progressIndicatorElement = jQuery.progressIndicator({
                        'message' : Message,
                        'position' : 'html',
                        'blockInfo' : {'enabled' : true}
                    });

                    elem.closest('tr').find('.deletedflag').remove()
                    AppConnector.request(postData).then(
                        function(data){
                            progressIndicatorElement.progressIndicator({
                                'mode' : 'hide'
                            });

                            if(data.success) {
                            }else{
                                var  params = {
                                    text : app.vtranslate(data.message),
                                    title : app.vtranslate('JS_LBL_PERMISSION')
                                }
                                Vtiger_Helper_Js.showPnotify(params);
                            }
                        },
                        function(error,err){

                        }
                    );
                },
                function(error, err){
                }
            );
			e.stopPropagation();
		});
        listViewContentDiv.on("click",".checkedall",function(event){
			$('input[name="Detailrecord\[\]"]').iCheck('check');
            event.stopPropagation();
		});
        listViewContentDiv.on("click",".checkedinverse",function(event){
            $('input[name="Detailrecord\[\]"]').iCheck('toggle');
            event.stopPropagation();
        });
        listViewContentDiv.on("click",".stampall",function(event){
        	var ids='';
            $.each($('input[name="Detailrecord\[\]"]:checkbox:checked'),function(key,value){
                ids+=$(value).val()+',';
                var recordId = $(value).closest('tr');
                recordId.find('.deletedflag').remove();
			});
            ids=ids.substr(0,ids.length-1);
            if(''!=ids){
                _this.doStamp(ids);
			}
            event.stopPropagation();//阻止事件冒泡
        });
        listViewContentDiv.on("click",".stamp",function(event){
            var ids=$(this).data('id');
            if(''!=ids){
                _this.doStamp(ids);
            }
            var recordId = $(this).closest('tr');
            recordId.find('.deletedflag').remove();
            event.stopPropagation();//阻止事件冒泡
        });
	},
    createContractNo:function(){
        var thisInstance=this;
        $("#addContractNO").click(function(){
            var message = app.vtranslate('合同编号创建');
            thisInstance.showConfirmationBox({'message' : message}).then(
                function(e) {
                    var module = app.getModuleName();
                    var contractNo=$('#contractno').val();
                    var postData = {
                        "module": module,
                        "action": "ChangeAjax",
                        "mode": "createContractNo",
                        "contractno": contractNo
                    }
                    var Message = app.vtranslate('正在处理......');
                    var progressIndicatorElement = jQuery.progressIndicator({
                        'message' : Message,
                        'position' : 'html',
                        'blockInfo' : {'enabled' : true}
                    });
                    AppConnector.request(postData).then(
                        function(data){
                            progressIndicatorElement.progressIndicator({
                                'mode' : 'hide'
                            });
                            if(data.result.flag){
                                var  params = {
                                    text : app.vtranslate(data.result.msg),
                                    title : app.vtranslate('JS_MESSAGE')
                                }
                                Vtiger_Helper_Js.showPnotify(params);
                                window.location.reload();
                               }else{
                                var  params = {
                                    text : app.vtranslate(data.result.msg),
                                    title : app.vtranslate('JS_MESSAGE')
                                }
                                Vtiger_Helper_Js.showPnotify(params);
                            }
                        },
                        function(error,err){

                        }
                    );
                },
                function(error, err){
                }
            );

            var strr='<div id="insertcomment" style="height: 55px;overflow: hidden">\
                            <table class="table table-bordered blockContainer Duplicates showInlineTable  detailview-table" data-num="1" id="comments1"><tbody>'+
                '<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>合同编号:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><input type="text" id="contractno" name="contractno" ></span></div></td></tr>' +
                '</tbody></table>'+
                '</div>';

            $('.modal-content .modal-body').append(strr);
        });
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
        bootBoxModal.on('hidden',function(e){
            if(jQuery('#globalmodal').length > 0) {
                jQuery('body').addClass('modal-open');
            }
        })
        return aDeferred.promise();
    },
    checkedform:function(){
        var contractno=$('#contractno').val();
        if(contractno==''){
            $('#contractno').focus();
            $('#contractno').attr('data-content','<font color="red">必填项不能为空!</font>');;
            $('#contractno').popover("show");
            $('.popover').css('z-index',1000010);
            $('.popover-content').css({"color":"red","fontSize":"12px"});
            setTimeout("$('#contractno').popover('destroy')",1000);
            return false;
        }
        return true;
    },
    registerEvents : function(){
        this._super();
        this.registerChangeRecordClickEvent();
        this.createContractNo();


}

});