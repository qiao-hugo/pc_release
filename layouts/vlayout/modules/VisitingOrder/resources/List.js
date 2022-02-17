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
    selectedTips:function(){

        $("#searchtable").on("change",'select',function () {
            //console.log($(this).val());
            var  val=$(this).val();
            if(val=='threemonthsvisit' || val=='duplicateremoval'){
                if(val=='threemonthsvisit'){
                    $("#duplicateremoval").remove();
                    $("#threemonthsvisit").remove();
                    $("#SearchBlankCover").after('<div  id="threemonthsvisit" style="text-align: center;color: red;">自当前拜访单开始日期向前推3个月，自动过滤掉这3个月内同一人提单或者陪同拜访同一客户的拜访单。</div>');
                }else if(val=='duplicateremoval'){
                    $("#duplicateremoval").remove();
                    $("#threemonthsvisit").remove();
                    $("#SearchBlankCover").after('<div  id="duplicateremoval" style="text-align: center;color: red;">自动过滤当前筛选列表重复拜访客户的拜访单，保留该区间内第一次拜访单数据.</div>');
                }
            }else{
                $(this).find("option[value='threemonthsvisit']").val();
                if($(this).find("option[value='threemonthsvisit']").val()=='threemonthsvisit' || $(this).find("option[value='duplicateremoval']").val()=='duplicateremoval'){
                    $("#duplicateremoval").remove();
                    $("#threemonthsvisit").remove();
                }
            }
        });
        $("#searchtable").on("click",'.cancel_search_button',function () {
            if($(this).parents("tr").find("option[value='threemonthsvisit']").length>0){
                $("#duplicateremoval").remove();
                $("#threemonthsvisit").remove();
            }
        });
    },
    changeVisitsigntypesToSelect:function () {
        $("#searchtable").on("change",'select',function () {
                   var id=$(this).attr("id");
                   var newID=id.replace(/BugFreeQuery_field/g,'BugFreeQuery_value');
                   var name =id.replace(/BugFreeQuery_field/g,'BugFreeQuery[value');
                   name=name+']';
                   if($(this).val()=='vtiger_visitsign.visitsigntype##1##3144##string'){
                       console.log(newID);
                       console.log($("#"+newID).parent().html());
                       var strHtml= '<select id="'+newID+'" style="width: 100%;" class="chzn-select chzn-done" name="'+name+'">'+
                                       '<option value="">选择一个选项</option>'+
                                       '<option value="提单人">提单人</option>'+
                                       '<option value="陪同人">陪同人</option>'+
                                    '</select>'
                       $("#"+newID).parent().html(strHtml);
                      /* $('.chzn-select').chosen();*/
                      /* $("#"+newID).trigger("liszt:updated");*/

                   }

        });
    },
    setStrangeVisit:function(){
        $("body").on("click",'.setStrangeVisitButton',function () {
            var recordid=$(this).data('id');
            var flag=$(this).attr('data-flag');
            var instance=$(this);
            Vtiger_Helper_Js.showConfirmationBox({'message' : '陌拜设置'}).then(
                function(e) {
                    //参数设置
                    var postData = {
                        "module": app.getModuleName(),
                        "action": "ChangeAjax",
                        "recordid": recordid,
                        "mode":"setStrangeVisit"
                    }
                    //发送请求
                    var progressIndicatorElement = jQuery.progressIndicator({
                        'message': '正在努力处理...',
                        'position': 'html',
                        'blockInfo': {'enabled': true}
                    });
                    AppConnector.request(postData).then(
                        function(data){
                            progressIndicatorElement.progressIndicator({'mode': 'hide'});
                            if(flag==1){
                                instance.attr('data-flag',2);
                                instance.attr('title','取消陌拜');
                                instance.removeClass('icon-plane');
                                instance.addClass('icon-repeat');
                            }else{
                                instance.addClass('icon-plane');
                                instance.removeClass('icon-repeat');
                                instance.attr('data-flag',1);
                                instance.attr('title','设为陌拜');
                            }
                        },
                        function(error){

                        }
                    );
                },
                function(error){
                }
            )

        });
    },
    registerEvents : function(){
        this._super();
        this.addCommnet();
        this.selectedTips();
        this.changeVisitsigntypesToSelect();
        this.setStrangeVisit();
    }

});