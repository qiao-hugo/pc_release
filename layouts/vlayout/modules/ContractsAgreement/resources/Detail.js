/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("ContractsAgreement_Detail_Js",{},{

    files_deliver: function() {
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
    },

	
	registerEvents : function(){
		this._super();
        this.files_deliver();
	}
})