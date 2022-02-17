/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("ContractsProducts_Detail_Js",{

    fallToovertTime:function(){
		$('#ContractsProducts_detailView_basicAction_LBL_ADD_BILLCONTENT').click(function(){
            var progressIndicatorElement = jQuery.progressIndicator({
                'message' : '正在拼命加载...',
                'position' : 'html',
                'blockInfo' : {'enabled' : true}
            });

			var params={};
            params.data={
            	'action' : 'ChangeAjax',
            	'module': 'ContractsProducts',
            	'mode' : 'getInvoicecompany',
			}
            params.async=false;
            var optionstr='';
            AppConnector.request(params).then(
                function(data){
                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});
					if(data.success==true){
						var result=data.result;
						$.each(result,function(key,value){
                            optionstr+='<option value="'+value.companycode+'">'+value.invoicecompany+'</option>';
						});
					}
                },function(){}
            );
            var msg={'message':"<h3>添加开票内容</h3>",
                "action":function(){
                    var selectValue = $('#billcontent').val();
                    if(selectValue!=''){
                        return true;
                    }else{
                        Vtiger_Helper_Js.showPnotify(app.vtranslate('开票内容必填！'));
                        return false;
                    }
                    return true;
                }
            };
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                var params={};
                params['record'] = $('#recordId').val();//工单id
                params['action'] = 'ChangeAjax';
                params['module'] = 'ContractsProducts';
                params['mode'] = 'addInvoicecompany';
                params['billcontent'] = $('#billcontent').val();
                params['invoicecompany'] = $('#invoicecompany').val();
                AppConnector.request(params).then(
                    function(data){
						window.location.reload();
                    },function(){}
                );
            });
            var str='';

            var strr='<form name="insertcomment" id="formcomment">\
                            <div id="insertcomment" style="height: 300px;overflow: auto">\
                            <table class="table table-bordered blockContainer Duplicates showInlineTable  detailview-table" id="comments1"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"> ' +
                '<span class="redColor">*</span> 开票内容</label></td>' +
                '<td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><input type="text" id="billcontent" name="billcontent"  value=""></span></div></td></tr>' +
                '<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>合同主体:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><select name="invoicecompany" id="invoicecompany">'+optionstr+'</select></span></div></td></tr>' +
                '</tbody></table>'+
                '</div></form>';

            $('.modal-content .modal-body').append(strr);
		});

	},
    invoicecompanydel:function(){
        $('.invoicecompanydel').click(function(){
            var msg={'message':"<h3>确定要删除该记录吗？</h3>"
            };
            var id=$(this).data('id');
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                var params={};
                params['record'] = id;//工单id
                params['action'] = 'ChangeAjax';
                params['module'] = 'ContractsProducts';
                params['mode'] = 'delInvoicecompany';
                var progressIndicatorElement = jQuery.progressIndicator({
                    'message' : '正在努力处理...',
                    'position' : 'html',
                    'blockInfo' : {'enabled' : true}
                });
                AppConnector.request(params).then(
                    function(data){
                        progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                        window.location.reload();
                    },function(){}
                );
            });

        });
	},
    registerEvents: function() {
        this._super();
        this.fallToovertTime();
        this.loading();
        this.invoicecompanydel();
    },
	loading:function(){
        Vtiger_Helper_Js.showConfirmationBox =function(data){
            var aDeferred = jQuery.Deferred();
            var width='800px';
            var checkFlag=true
            if(typeof  data['width'] != "undefined"){
                width=data['width'];
            }
            var bootBoxModal = bootbox.confirm({message:data['message'],width:width, callback:function(result) {
                if(result){
                    var checkFlag=true
                    if(typeof  data['action'] != "undefined"){
                        checkFlag=(data['action'])();
                    }
                    if(checkFlag){
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
        }
	}


});