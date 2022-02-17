/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("VisitingOrder_List_Js",{},{

	addCommnet:function(){
		var thisinstance=this;
		$('.icon-comment').on('click',function(){
            var message='<h3>改进意见</h3>';
            var msg={
                'message':message,
                "width":800
            };
            var icon=this;
            var recordid=$(this).data('id');
            thisinstance.showConfirmationBox(msg).then(function(e){
                //alert($('#recordId').val());return;
                var params={};
                params['record'] = recordid;
                params['remark'] = $('#remark').val();
                params['action'] = 'ChangeAjax';
                params['module'] = 'VisitingOrder';
                params['mode'] = 'saveVisitImprovement';
                AppConnector.request(params).then(
                    function(data) {
                        $(icon).remove();
                    },
                    function(error,err){
                        //window.location.reload(true);
                    }
                );
            },function(error, err) {});
            $('.modal-content .modal-body').append('<table class="table table-bordered blockContainer Duplicates showInlineTable  detailview-table" data-num="yesreplace"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>改进意见</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><textarea class="span11" name="remark" id="remark"></textarea></span></div></td></tr></tbody></table>');
            $('.modal-content .modal-body').css({overflow:'hidden'});

		});
	},
    checkedform:function(){
        if($('#remark').val()==''){
            $('#remark').focus();
            $('#remark').attr('data-content','<font color="red">必填项不能为空!</font>');;
            $('#remark').popover("show");
            $('.popover').css('z-index',1000010);
            $('.popover-content').css({"color":"red","fontSize":"12px"});
            setTimeout("$('#remark').popover('destroy')",2000);
            return false;
        }
        return true;
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
registerEvents : function(){
	this._super();
	this.addCommnet();

}

});