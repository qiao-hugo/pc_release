/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("Newinvoice_List_Js",{},{
    tickecthandle : function(recordId,type,messge) {
        var listInstance = Vtiger_List_Js.getInstance();
        var message = app.vtranslate(messge);
        Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
            function(e) {
                var module = app.getModuleName();
                var postData = {
                    "module": module,
                    "action": "Invoicehandle",
                    "record": recordId,
                    "type":type
                }
                var deleteMessage = app.vtranslate('处理中......');
                var progressIndicatorElement = jQuery.progressIndicator({
                    'message' : deleteMessage,
                    'position' : 'html',
                    'blockInfo' : {
                        'enabled' : true
                    }
                });
                AppConnector.request(postData).then(
                    function(data){
                        progressIndicatorElement.progressIndicator({
                            'mode' : 'hide'
                        })
                        $('#returnTicket'+recordId).remove();
                        $('#toVoid'+recordId).remove();
                        var newtype=(type=='tovoid')?'作废':'退票';
                        $('.invoicestatus'+recordId).text(newtype);
                        /*
                        if(data.success) {
                            var orderBy = jQuery('#orderBy').val();
                            var sortOrder = jQuery("#sortOrder").val();
                            var urlParams = {
                                "viewname": data.result.viewname,
                                "orderby": orderBy,
                                "sortorder": sortOrder
                            }
                            jQuery('#recordsCount').val('');
                            jQuery('#totalPageCount').text('');
                            listInstance.getListViewRecords(urlParams).then(function(){
                                listInstance.updatePagination();
                            });
                        } else {
                            var  params = {
                                text : app.vtranslate(data.error.message),
                                title : app.vtranslate('JS_LBL_PERMISSION')
                            }
                            Vtiger_Helper_Js.showPnotify(params);
                        }
                        */
                    },
                    function(error,err){

                    }
                );
            },
            function(error, err){
            }
        );
    },
    registerticketClickEvent: function(){
        var thisInstance = this;
        var listViewContentDiv = this.getListViewContentContainer();
        listViewContentDiv.on('click','.toVoidButton',function(e){
            var elem = jQuery(e.currentTarget);
            var recordId = elem.closest('tr').data('id');
            thisInstance.tickecthandle(recordId,'tovoid','您确定要作废该发票码?');
            e.stopPropagation();
        });
        listViewContentDiv.on('click','.returnTicketButton',function(e){
            var elem = jQuery(e.currentTarget);
            var recordId = elem.closest('tr').data('id');
            thisInstance.tickecthandle(recordId,'returnticket','您确定该发票要退票码?');
            e.stopPropagation();
        });
    },
    noNeedToExportButton:function(){
        var instancethis=this;
        $('.listViewContentDiv').on("click",'.noNeedToExportButton',function(e){
            var elem = jQuery(e.currentTarget);
            var recordId = elem.closest('tr').data('id');
            var msg={'message':"标记无需导出"};
            instancethis.showConfirmationBox(msg).then(function(e){
                var voidreason=$('#voidreason').val();
                var params={};
                var module = app.getModuleName();
                params['record']=recordId;
                params['action']='BasicAjax';
                params['module']=module;
                params['voidreason']=voidreason;
                params['mode']='noNeedToExport';
                AppConnector.request(params).then(
                    function(data){
                        if(data.success==true){
                            // window.location.reload(true);
                        }else{
                            alert("处理出现异常,请重新尝试");
                        }
                    }
                );
            });
            $('.modal-body').append('<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0;"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">无需导出原因<font color="red">*</font>:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><textarea id="voidreason" class="span11 "></textarea></span></div></td></tr></tbody></table>');
        });
    },
    needToExportButton:function () {
        var instancethis=this;
        $('.listViewContentDiv').on("click",'.needToExportButton',function(e){
            var elem = jQuery(e.currentTarget);
            var recordId = elem.closest('tr').data('id');
            var msg={'message':"取消标记无需导出？"};
            instancethis.showConfirmationBox(msg).then(function(e){
                var voidreason=$('#voidreason').val();
                var params={};
                var module = app.getModuleName();
                params['record']=recordId;
                params['action']='BasicAjax';
                params['module']=module;
                params['mode']='needToExport';
                AppConnector.request(params).then(
                    function(data){
                        if(data.success==true){
                            // window.location.reload(true);
                        }else{
                            alert("处理出现异常,请重新尝试");
                        }
                    }
                );
            });
        });
    },
    docancel:function(){
        var instancethis=this;
        $('.listViewContentDiv').on("click",'.docancel',function(e){
            var elem = jQuery(e.currentTarget);
            var recordId = elem.closest('tr').data('id');
            var name=$('#stagerecordname').val();
            var msg={'message':"是否要撤销该发票申请？"};
            instancethis.showConfirmationBox(msg).then(function(e){
                msg={'message':"确定要撤销该发票？"};
                var voidreason=$('#voidreason').val();
                Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                    var params={};
                    var module = app.getModuleName();
                    $('.isuserid').each(function(){
                        ids=ids+$(this).val()+',';
                    });
                    params['record']=recordId;
                    params['action']='BasicAjax';
                    params['module']=module;
                    params['voidreason']=voidreason;
                    params['mode']='docancel';
                    AppConnector.request(params).then(
                        function(data){
                            // window.location.reload(true);
                        }
                    );

                });
            });
            $('.modal-body').append('<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0;"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">撤销原因<font color="red">*</font>:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><textarea id="voidreason" class="span11 "></textarea></span></div></td></tr></tbody></table>');
        });
    },
    downloadPdf:function(){
        var instancethis=this;
        $('.listViewContentDiv').on("click",'.downloadPdf',function(e){
            $.blockUI({ message: '<h3><img src="./libraries/jquery/layer/skin/default/xubox_loading2.gif" />下载中...</h3>' });
            var elem = jQuery(e.currentTarget);
            var recordId = elem.closest('tr').data('id');
            var params={};
            var module = app.getModuleName();
            params['record']=recordId;
            params['action']='BasicAjax';
            params['module']=module;
            params['mode']='downloadPdf';
            AppConnector.request(params).then(
                function(data){
                    if(data.success==true){
                        $('.blockUI').remove();
                        var url = data.result['data'];
                        window.open(url);
                    }else{

                    }

                }
            );
        });
    },
    checkedform:function(data){
        if($('#voidreason').val()=='')
        {
            $('#voidreason').focus();
            $('#voidreason').attr('data-content','<font color="red">必填项不能为空</font>');
            $('#voidreason').attr('data-placement','right');
            $('#voidreason').popover("show");
            $('.popover-content').css({"color":"red","fontSize":"12px"});
            $('.popover').css('z-index',1000010);
            setTimeout("$('#voidreason').popover('destroy')",2000);
            return false;
        }
        if(''==$("#usercode").val()){
            alert("请输入工号");
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
                if(thisstance.checkedform(data)){
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
    dontlisten:function(){
        var _this=this;
        var listViewContentDiv = this.getListViewContentContainer();
        listViewContentDiv.on('click','.noclick',function(event){
            event.stopPropagation();
        });
        listViewContentDiv.on("click",".checkedinverse,.checkedall",function(event){
            $('input[name="Detailrecord\[\]"]').iCheck('toggle');
            event.stopPropagation();
        });

    },
    dosign:function(){
        var instanceThis=this;
        $('#BatchSignature').on('click',function(){
            var Detailrecords=$('input[name="Detailrecord\[\]"]:checkbox:checked');
            if(Detailrecords.length==0){
                return false;
            }
            var records='';
            $.each(Detailrecords,function(key,value){
                records+=$(value).val()+',';
                $(value).iCheck('disable');
            });
            //console.log(records.substring(0,records.length-1));
            records=records.substring(0,records.length-1);
            records=encodeURI(records);
            var act=$(this).data('act');
            var message='<h3>确定要领取该发票吗?请签写您的<font color="red">姓名</font><input type="text" id="inputusercode" style="margin-left:40px;" placeholder="请输入工号"/><span id="displayname" style="margin-left: 10px;"></span><input id="usercode" type="hidden"/><input id="username" type="hidden"/></h3> ';
            var windowwith=$(window).width();
            var windowheight=windowwith*0.25;
            var msg={
                'message':message,
                "width":windowwith
            };

            instanceThis.showConfirmationBox(msg).then(function(e){
                //alert($('#recordId').val());return;
                var params={};
                params['records'] = records;
                params['action'] = 'BasicAjax';
                params['module'] = 'Newinvoice';
                params['mode'] = 'savesignimages';
                params['id'] = $("#usercode").val();
                params['image'] = $('#canvssign').jSignature("getData", "default").toString();
                AppConnector.request(params).then(
                    function(data) {
                        //window.location.reload(true);
                    },
                    function(error,err){
                        //window.location.reload(true);
                    }
                );
            },function(error, err) {});
            $('.modal-content .modal-body').append('<div id="canvssign" ondragstart="return false" oncontextmenu="return false" onselectstart="return false" oncopy="return false" oncut="return false" style="-moz-user-select:none;width:100%;height:'+windowheight+'px; min-height:none; border:1px solid #ccc;margin:10px 0 0;overflow:hidden;"></div>');
            $('.modal-content .modal-body').css({overflow:'hidden'});
            $('#canvssign').jSignature();
            $('<input type="button" value="清空" style="float:left;margin-left:'+(windowwith/2)+'px;">').bind('click', function(e){
                $('#canvssign').jSignature('reset')
            }).appendTo('.modal-content .modal-footer');
        });
    },
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
                            }
                        },
                        function(error,err){

                        }
                    );
                }
            }
        });
    },
registerEvents : function(){
	this._super();
    this.registerticketClickEvent();
    this.docancel();
    this.dontlisten();
    this.dosign();
    this.scanUserCode();
    this.noNeedToExportButton();
    this.needToExportButton();
    this.downloadPdf();
	//this.Tableinstance();
	//this.BarLinkRemove();
	//this.ActiveClick();
	//this.registerLoadAjaxEvent();

}

});