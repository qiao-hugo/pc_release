/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("Schoolresume_List_Js",{},{

	registerEvents : function(){
		this._super();
        this.registerChangeRecordClickEvent();
        this.sendMail();

	},
    sendMail:function(){
        var _this=this;
	    $('#listViewContents').on("click",".sendMialButton",function(event){

            var msg = {
                'message': '<h3>简历录取</h3>',
                "width":"600px",
            };
            var userdata=_this.getRecordData(event);
            console.log(userdata);
            _this.showConfirmationBox(msg).then(
                function(e) {
                    _this.doSendMial(event);

                });
            var date=new Date();
            var month=date.getMonth()+1
            month=month<10?'0'+month:month;

            var datenow=date.getFullYear()+'-'+month+'-'+date.getDate();

            if(userdata['reportsdate']!='' && userdata['reportsdate']!=undefined){
                datenow=userdata['reportsdate'];
            }
            var strr='<form name="insertcomment" id="formcomment">\
                            <div id="insertcomment" style="height: 300px;overflow: auto">\
                            <table class="table table-bordered blockContainer Duplicates showInlineTable  detailview-table" data-num="1" id="comments1"><tbody>'+
                '<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>预计报道时间:</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="text" name="reportsdate" readonly id="datatime" value="'+datenow+'"/> </span></div></td></tr>' +
                '<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>录取职位:</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><select name="entityposition" class="chzn-select"><option value="internetmarketingconsultant" '+(userdata['entityposition']=='internetmarketingconsultant'?" selected":'')+'>营销方向-网络营销顾问</option><option value="managementtrainee" '+(userdata['entityposition']=='managementtrainee'?" selected":'')+'>管培生（营销方向）</option><option value="reservecadres" '+(userdata['entityposition']=='reservecadres'?" selected":'')+'>储备干部</option><option value="civiliantechnicaldirection" '+(userdata['entityposition']=='civiliantechnicaldirection'?" selected":'')+'>文职技术方向</option></select></span></div></td></tr>' +
                '</tbody></table>'+
                '</div></form>';

            $('.modal-content .modal-body').append(strr);
            $('.chzn-select').chosen();
            $('#datatime').datetimepicker({
                format: "yyyy-mm-dd",
                language:  'zh-CN',
                autoclose: true,
                todayBtn: true,
                pickerPosition: "bottom-right",
                showMeridian: 0,

                //endDate:new Date(),
                weekStart:1,
                todayHighlight:1,
                startView:2,
                minView:2,
                forceParse:0
            });
        });
    },
    getRecordData:function(e){
        var module = app.getModuleName();
        var elem = jQuery(e.currentTarget);
        var recordId = elem.closest('tr').data('id');
        console.log(recordId);
        var params={};
        params.data = {
            'module' : module, //ServiceContracts
            'action' : 'BasicAjax',
            'mode':'getRecordData',
            'record':recordId
        };
        params.async=false;
        var obj='';
        AppConnector.request(params).then(
            function(data){
                if(data.success){

                    obj=data.result;
                }
            },
            function(){
            }
        );
        return obj;
    },
    doSendMial:function(e){
        var module = app.getModuleName();
        var elem = jQuery(e.currentTarget);
        var reportsdate=$("input[name='reportsdate']").val();
        var entityposition=$("select[name='entityposition']").val();
        var recordId = elem.closest('tr').data('id');
        var params={};
        params.data = {
            'module' : module, //ServiceContracts
            'action' : 'BasicAjax',
            'mode':'sendMail',
            'record':recordId,
            'reportsdate':reportsdate,
            'entityposition':entityposition
        };
        params.async=true;
        var obj='';
        var Message = app.vtranslate('正在处理......');
        var progressIndicatorElement = jQuery.progressIndicator({
            'message' : Message,
            'position' : 'html',
            'blockInfo' : {'enabled' : true}
        });

        AppConnector.request(params).then(
            function(data){
                progressIndicatorElement.progressIndicator({
                    'mode' : 'hide'
                });
                if(data.success){
                    var  params = {
                        text : data.msg,
                        title : ''
                    }
                    Vtiger_Helper_Js.showPnotify(params);
                }else{
                    var  params = {
                        text : data.msg,
                        title : '发送失败'
                    }
                    Vtiger_Helper_Js.showPnotify(params);
                }
            },
            function(){
            }
        );


    },
    doStamp:function(ids,flag){
        var module = app.getModuleName();
        var reportsdate=$("input[name='reportsdate']").val();
        var reportaddress=$("input[name='reportaddress']").val();
        var reportsower=$("select[name='reportsower']").val();
        var entityposition=$("select[name='entityposition']").val();

        var postData = {
            "module": module,
            "action": "BasicAjax",
            "mode": "doBatchEnrollment",
            "records": ids,
            reportsdate:reportsdate,
            reportaddress:reportaddress,
            reportsower:reportsower,
            entityposition:entityposition,
            flag:flag
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
                //处理结果提示 gaocl add 2018/03/09
                /*var  params = {
                    text : '操作成功',
                    title : ''
                }
*/
                if(data.success){
                    var  params = {
                        text : data.msg,
                        title : ''
                    }
                }else{
                    var  params = {
                        text : data.msg,
                        title : '录取失败'
                    }
                }

                Vtiger_Helper_Js.showPnotify(params);

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

        listViewContentDiv.on("click",".checkedall",function(event){
            $('input[name="Detailrecord\[\]"]').iCheck('check');
            event.stopPropagation();
        });
        listViewContentDiv.on("click",".checkedinverse",function(event){
            $('input[name="Detailrecord\[\]"]').iCheck('toggle');
            event.stopPropagation();
        });
        listViewContentDiv.on("click",".stampall",function(event){
            var dataValue=$(this).data('value');
            var ids='';
            $.each($('input[name="Detailrecord\[\]"]:checkbox:checked'),function(key,value){
                ids+=$(value).val()+',';
            });
            ids=ids.substr(0,ids.length-1);
            if(''!=ids){
                _this.showConfirmationBoxInstance(ids,dataValue);
                $.each($('input[name="Detailrecord\[\]"]:checkbox:checked'), function (key, value) {
                    var recordId = $(value).closest('tr');
                    recordId.find('.deletedflag').remove();
                });
            }
            event.stopPropagation();//阻止事件冒泡
        });
        listViewContentDiv.on("click",".stamp",function(event){
            var ids=$(this).data('id');
            var dataValue=$(this).data('value');
            if(''!=ids){
                _this.showConfirmationBoxInstance(ids,dataValue);
                var recordId = $(this).closest('tr');
                recordId.find('.deletedflag').remove();
                event.stopPropagation();//阻止事件冒泡
            }

        });
    },
    showConfirmationBoxInstance:function(ids,flag){
        var _this=this;
        var msg = {
            'message': '<h3>简历录取</h3>',
            "width":"600px",
        };
        var userlist=_this.getUserList();
        _this.showConfirmationBox(msg).then(
            function(e) {
                _this.doStamp(ids,flag);

            });
        var date=new Date();
        var month=date.getMonth()+1
        month=month<10?'0'+month:month;
        var datenow=date.getFullYear()+'-'+month+'-'+date.getDate();
        var strr='<form name="insertcomment" id="formcomment">\
                            <div id="insertcomment" style="height: 300px;overflow: auto">\
                            <table class="table table-bordered blockContainer Duplicates showInlineTable  detailview-table" data-num="1" id="comments1"><tbody>'+
            '<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>预计报道时间:</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="text" name="reportsdate" readonly id="datatime" value="'+datenow+'"/> </span></div></td></tr>' +
            '<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>预计报道地点:</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="text" name="reportaddress" value="无锡"></span></div></td></tr>' +
            '<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>报道负责人:</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><select name="reportsower" class="chzn-select" id="reportsdate">'+userlist+'</span></div></td></tr>' +
            '<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>录取职位:</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><select name="entityposition" class="chzn-select"><option value="internetmarketingconsultant">营销方向-网络营销顾问</option><option value="managementtrainee">管培生（营销方向）</option><option value="reservecadres">储备干部</option><option value="civiliantechnicaldirection">文职技术方向</option></select></span></div></td></tr>' +
            '</tbody></table>'+
            '</div></form>';

        $('.modal-content .modal-body').append(strr);
        $('.chzn-select').chosen();
        $('#datatime').datetimepicker({
            format: "yyyy-mm-dd",
            language:  'zh-CN',
            autoclose: true,
            todayBtn: true,
            pickerPosition: "bottom-right",
            showMeridian: 0,

            //endDate:new Date(),
            weekStart:1,
            todayHighlight:1,
            startView:2,
            minView:2,
            forceParse:0
        });
    },
    /**
     * 获取用户报道负责人列表
     * @returns {string}
     */
    getUserList:function(){
        var module = app.getModuleName();
        var params={};
        params.data = {
            'module' : module, //ServiceContracts
            'action' : 'BasicAjax',
            'mode':'getuserlist'
        };
        params.async=false;
        var str='';
        AppConnector.request(params).then(
            function(data){
                if(data.success){

                    $.each(data.result,function(key,value){
                        str+='<option value="'+value.id+'">'+value.username+'</option>';
                    })
                }
            },
            function(){
            }
        );
        return str;
    },
    /**
     * 验证规则
     * @returns {boolean}
     */
    checkedform:function(){
        var reportaddress=$('input[name="reportaddress"]').val();
        if(reportaddress==''){
            $('input[name="reportaddress"]').focus();
            $('input[name="reportaddress"]').attr('data-content','<font color="red">预计报道地点必填!</font>');;
            $('input[name="reportaddress"]').popover("show");
            $('.popover').css('z-index',1000010);
            $('.popover-content').css({"color":"red","fontSize":"12px"});
            setTimeout("$('#remark').popover('destroy')",2000);
            return false;
        }
        return true;
    },
    /**
     *重写方加入自定义验证规则
     * @param data
     */
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

});