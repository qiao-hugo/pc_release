/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("AccountReceivable_List_Js", {}, {

    exportReceivableOverdue:function(){
        $("body").on('click','#exportReceivableOverdue',function () {
            console.log(1);
            var a = $('#SearchBug').serializeArray();
            var o = {};
            $.each(a, function() {
                if (o[this.name] !== undefined) {
                    if (!o[this.name].push) {
                        o[this.name] = [o[this.name]];
                    }
                    o[this.name].push(this.value || '');
                } else {
                    o[this.name] = this.value || '';
                }
            });
            var form=JSON.stringify(o);

            var urlParams = {"module":"AccountReceivable","action":"ChangeAjax","mode":"exportData","BugFreeQuery":form};
            var progressIndicatorElement = jQuery.progressIndicator({
                'message' : '请求中',
                'position' : 'html',
                'blockInfo' : {'enabled' : true}
            });
            AppConnector.request(urlParams).then(
                function(data){
                    progressIndicatorElement.progressIndicator({
                        'mode' : 'hide'
                    });
                    if(data.success){
                        window.location.href='index.php?module=AccountReceivable&view=List&public=export';
                    }else{
                        var  params = {text : data.error.message, title : '提示',type:'error'};
                        Vtiger_Helper_Js.showMessage(params);
                    }
                }
            );
        })
    },
    relationAccount:function(){
        $('body').on('click','.relation_account',function(){
            $('.relation_account').popover('hide');
            var _this=this;
            var type = $(this).data('mode');
            if($(_this).attr('clickfalg')==2){
                $(_this).attr('clickfalg',1);
                return;
            }
            var progressIndicatorElement = jQuery.progressIndicator({
                'message' : '正在处理,请耐心等待哟',
                'position' : 'html',
                'blockInfo' : {'enabled' : true}
            });
            var postData= {
                'module': 'AccountReceivable',
                'action': 'ChangeAjax',
                'mode': 'relationAccount',
                'accountreceivableid':$(_this).data('id'),
                'type':type
            };

            AppConnector.request(postData).then(
                function(data){
                    progressIndicatorElement.progressIndicator({
                        'mode' : 'hide'
                    });
                    var str = '';
                    if(data.success){
                        str +='<table>';
                        switch (type) {
                            case 'contract_no':
                                $.each(data.result,function(key,value){
                                    str += '<tr>' +
                                        '<td>'+value+'</td>' +
                                        '</tr>'
                                });
                                var title='合同数';
                                break;
                            case 'bussinesstype':
                                $.each(data.result,function(key,value){
                                    str += '<tr>' +
                                        '<td>'+value+'</td>' +
                                        '</tr>'
                                });
                                var title='业务大类数';
                                break;
                        }
                        str += '</table>';
                        $(_this).attr('clickfalg',2);
                        $(_this).attr('data-title',title);
                        $(_this).attr('data-content',str);
                        $(_this).popover("show");
                        $(".popover.right").attr("style:max-width","100%");
                    }
                }
            )
        });
    },
    collate : function() { //核对
        $('body').on("click", '.collate', function() { //单个核对
            var accountid = $(this).parents('tr').data('accountid');
            var msg = {
                'message': '<strong>应收核对</strong>',
                "width":"600px"
            };
            Vtiger_Helper_Js.showConfirmationBox(msg).then(
                function(e) {
                    var checkresult = $('#checkresult').val();
                    var remark = $('#remark').val();

                    if (checkresult == '0' && remark=='') {
                        var params = {type: 'error', text: '选择否时，备注必须填写'};
                        Vtiger_Helper_Js.showMessage(params);
                        return false;
                    }
                    if (remark.length>2000) {
                        var params = {type: 'error', text: '备注允许最大长度为2000'};
                        Vtiger_Helper_Js.showMessage(params);
                        return false;
                    }
                    var postData = {
                        "module": 'ReceivableOverdue',
                        "action": "ChangeAjax",
                        "checktype": 'AccountReceivable',
                        'contractid': accountid,
                        'stage': 0,
                        "checkresult": checkresult,
                        'remark': remark,
                        'mode': 'collateReceivable'
                    }
                    var Message = "提交中...";
                    var progressIndicatorElement = jQuery.progressIndicator({
                        'message' : Message,
                        'position' : 'html',
                        'blockInfo' : {'enabled' : true}
                    });
                    AppConnector.request(postData).then(
                        function(data) {
                            // 隐藏遮罩层
                            progressIndicatorElement.progressIndicator({
                                'mode' : 'hide'
                            });
                            if (data.result.status == 'success') {
                                var params = {type: 'success', text: '成功核对'};
                                Vtiger_Helper_Js.showMessage(params);
                                window.location.reload();
                            } else {
                                var params = {type: 'error', text: data.result.msg};
                                Vtiger_Helper_Js.showMessage(params);
                            }
                        },
                        function(error,err){

                        }
                    );
                },function(error, err){}
            );
            var html = '<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0"><tbody>'+
                '<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>是否符合:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><select id="checkresult"><option value="1">是</option><option value="0">否</option></select></span></div></td></tr>'+
                '<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor" style="display: none;" id="remarkstar">*</span>备注:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><textarea id="remark" style="overflow:hidden;overflow-wrap:break-word;resize:none; height:100px;width:320px;"></textarea></span></div></td></tr>'+
                '</tbody></table>';
            $('.modal-body').append(html);
        }).on('click', '#collateReceivableOverdue', function() { //批量核对数据
            var a = $('#SearchBug').serializeArray();
            var o = {};
            $.each(a, function() {
                if (o[this.name] !== undefined) {
                    if (!o[this.name].push) {
                        o[this.name] = [o[this.name]];
                    }
                    o[this.name].push(this.value || '');
                } else {
                    o[this.name] = this.value || '';
                }
            });
            var form=JSON.stringify(o);

            var urlParams = {
                "module":"AccountReceivable",
                "action":"JsonAjax",
                "mode":"getListViewCount",
                "BugFreeQuery":form
            };
            var progressIndicatorElement = jQuery.progressIndicator({
                'message' : '请求中...',
                'position' : 'html',
                'blockInfo' : {'enabled' : true}
            });
            var state = true;
            AppConnector.request(urlParams).then(
                function(data){
                    progressIndicatorElement.progressIndicator({
                        'mode' : 'hide'
                    });
                    if(data.success){
                        var total = data.result;
                        if(total == 0) {
                            var params = {
                                type: 'error',
                                text: '当前共0条数据，请修改查询条件'
                            };
                            Vtiger_Helper_Js.showMessage(params);
                            return false;
                        } else if(total > 1000) {
                            var params = {
                                type: 'error',
                                text: '当前共' + total + '条数据,超过单次允许核对的最大记录数(1000)'
                            };
                            Vtiger_Helper_Js.showMessage(params);
                            return false;
                        }
                        var msg = {
                            'message': '<strong>应收核对（共' + total +'条数据）</strong>',
                            "width":"600px"
                        };
                        Vtiger_Helper_Js.showConfirmationBox(msg).then(
                            function(e) {
                                var res = confirm('确定要一键核对'+total+'条数据吗？');
                                if (res == false) {
                                    return false;
                                }
                                var checkresult = $('#checkresult').val();
                                var remark = $('#remark').val();

                                if (checkresult == '0' && remark=='') {
                                    var params = {type: 'error', text: '选择否时，备注必须填写'};
                                    Vtiger_Helper_Js.showMessage(params);
                                    return false;
                                }
                                if (remark.length>2000) {
                                    var params = {type: 'error', text: '备注允许最大长度为2000'};
                                    Vtiger_Helper_Js.showMessage(params);
                                    return false;
                                }
                                var postData = {
                                    "module": 'AccountReceivable',
                                    "action": "ChangeAjax",
                                    "checkresult": checkresult,
                                    'remark': remark,
                                    'mode': 'batchCollateReceivable',
                                    "BugFreeQuery":form
                                }
                                var Message = "提交中...";
                                var progressIndicatorElement = jQuery.progressIndicator({
                                    'message' : Message,
                                    'position' : 'html',
                                    'blockInfo' : {'enabled' : true}
                                });
                                AppConnector.request(postData).then(
                                    function(data) {
                                        // 隐藏遮罩层
                                        progressIndicatorElement.progressIndicator({
                                            'mode' : 'hide'
                                        });
                                        if (data.result.status == 'success') {
                                            var params = {type: 'success', text: data.result.msg};
                                            Vtiger_Helper_Js.showMessage(params);
                                            window.location.reload();
                                        } else {
                                            var params = {type: 'error', text: data.result.msg};
                                            Vtiger_Helper_Js.showMessage(params);
                                        }
                                    },
                                    function(error,err){

                                    }
                                );
                            },function(error, err){}
                        );
                        var html = '<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0"><tbody>'+
                            '<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>是否符合:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><select id="checkresult"><option value="1">是</option><option value="0">否</option></select></span></div></td></tr>'+
                            '<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor" style="display: none;" id="remarkstar">*</span>备注:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><textarea id="remark" style="overflow:hidden;overflow-wrap:break-word;resize:none; height:100px;width:320px;"></textarea></span></div></td></tr>'+
                            '</tbody></table>';
                        $('.modal-body').append(html);
                    } else {
                        var  params = {type:'error', text : data.error.message, title : '提示'};
                        Vtiger_Helper_Js.showMessage(params);
                    }
                }
            );
        }).on('change', '#checkresult', function() { //批量核对数据
            if( $(this).val()==0) {
                $('#remarkstar').show();
            } else {
                $('#remarkstar').hide();
            }
        });
    },
    checklog: function() {
        $("body").on('click', '.checklog', function () {
            var dialog = bootbox.dialog({
                title: '核对记录',
                width:'500px',
                message: '<p style="text-align: center;font-size:15px;color:#666"> 数据加载中...</p>'
            });
            var tr = $(this).parents('tr');
            var accountid = tr.data('accountid');
            var stage = tr.data('stage');
            var postData = {
                'module': 'ReceivableOverdue',
                'action': "ChangeAjax",
                'mode': 'checkLog',
                'checktype': 'AccountReceivable',
                'contractid': accountid,
                'stage': 0,
            }
            AppConnector.request(postData).then(
                function(data) {
                    if (data.success) {
                        var htmlstr = '<ul class="checkloglist">';
                        for (const i in data.result) {
                            var item = data.result[i];
                            var serialnum =parseInt(i)+1;
                            htmlstr += '<li><span class="serialnum">' + serialnum + '</span><div><span class="checktime">'+ item['checktime'] +'</span><span class="collator" title = "' + item['collator'] + '">'+ item['collator'] +'</span><span class="checkresult">'+ item['checkresult'] +'</span></div><div>'+ item['remark'] +'</div></li>';
                        }
                        htmlstr += '</ul>';
                        dialog.find('.bootbox-body').html(htmlstr);
                    }
                },
                function(error,err) {

                }
            );
        })
    },
    registerEvents: function () {
        this._super();
        this.exportReceivableOverdue();
        this.relationAccount();
        this.collate();//核对
        this.checklog();//核对记录
    }
});