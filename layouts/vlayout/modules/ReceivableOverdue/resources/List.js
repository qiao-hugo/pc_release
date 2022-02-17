/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("ReceivableOverdue_List_Js",{},{
    followUp:function(){
        var instancethis=this;
        $('body').on("click",'.followUp',function(e){
            var id = $(this).data('id');
            console.log(id);
            var stageshow = $(this).data('stageshow');
            console.log(stageshow)
            var contractid = $(this).data('contractid');
            console.log(contractid);
            str = '<div id="myModal" class="modal" style="">\n' +
                '\t<div class="modal-dialog">\n' +
                '\t\t<div class="modal-content">\n' +
                '\t\t\t<div class="modal-header">\n' +
                '\t\t\t\t<button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>\n' +
                '\t\t\t\t<h4 class="modal-title">应收跟进</h4>\n' +
                '\t\t\t\t<div style="margin-top: 20px;" id="supervisor">\n';
            // return;
            $("#supervisor").empty();
            var flag = false;
            str += '\n' +
                '\t\t\t\t</div>\n' +
                '\t\t\t</div>\n' +
                '\t\t\t<div class="modal-body" style="max-height:500px;">\n' +
                '\n' +
                '\t\t\t\t<div class="confirm tc">\n';

            str += '<input type="hidden" name="modcommentpurposeValue" value="'+stageshow+'" />';
            str += '<input type="hidden" name="contractid" value="'+contractid+'" />';
            str += '<input type="hidden" name="id" value="'+id+'" />';
            str +='<textarea name="commentcontent" style="width: 95%;height: 200px;"></textarea>';
            str +=                        '\n' +
                '\t\t\t\t</div>\n' +
                '\t\t\t</div>\n' +
                '\t\t\t<div class="modal-footer">\n' +
                '\t\t\t\t<div class=" pull-right cancelLinkContainer"><a class="cancelLink" type="reset" data-dismiss="modal">取消</a></div>\n' +
                '\t\t\t\t<button class="btn btn-success" id="transferPost" type="submit">保存</button>\n' +
                '\t\t\t</div>\n' +
                '\t\t</div>\n' +
                '\t</div>\n' +
                '</div>';
            app.showModalWindow(str);
            $('.modal-backdrop').css({
                "opacity":"0.6",
                "z-index":"0"
            });
        });
    },
    saveComment : function(e) {
        $("body").on('click',"#transferPost",function () {
            var commentContentValue = $("textarea[name='commentcontent']").val();
            var modcommentmodeValue = $("input[name='modcommentmodeValue']").val();
            var modcommenttypeValue = $("input[name='modcommenttypeValue']").val();
            var modcommentpurposeValue = $("input[name='modcommentpurposeValue']").val();
            var modcommentcontactsValue = $("input[name='modcommentcontactsValue']").val();
            var id = $("input[name='id']").val();
            var moduleid = $("input[name='contractid']").val();
            var postData =
                {
                    'commentcontent' : 	commentContentValue,
                    'modcommentmode' :  modcommentmodeValue,
                    'modcommenttype' :  modcommenttypeValue,
                    'modcommentpurpose' : modcommentpurposeValue,
                    'contact_id': modcommentcontactsValue,
                    'related_to': moduleid,
                    'module' : 'ModComments',
                    'modulename':'ServiceContracts',
                    'moduleid':moduleid,
                    'ifupdateservice':false,
                    'accountid':'',
                    'is_service':'',
                    'isfollowplain':'',
                    'action':'SaveAjax'
                }
            AppConnector.request(postData).then(
                function(data){
                    location.href='index.php?module=ReceivableOverdue&view=List'
                },
                function(textStatus, errorThrown){
                    alert('跟进失败，请刷新后重试')
                    return;
                }
            )
        })

    },
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

          var urlParams = {"module":"ReceivableOverdue","action":"ChangeAjax","mode":"exportData","BugFreeQuery":form};
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
                      window.location.href='index.php?module=ReceivableOverdue&view=List&public=export';
                  }else{
                      var  params = {text : data.error.message, title : '提示',type:'error'};
                      Vtiger_Helper_Js.showMessage(params);
                  }
              }
          );
      })
    },
    collate : function() { //核对
        $('body').on("click", '.collate', function() { //单个核对
            var contractid = $(this).data('contractid');
            var stage = $(this).data('stage');
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
                        "checktype": 'ReceivableOverdue',
                        'contractid': contractid,
                        'stage': stage,
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

            var urlParams = {"module":"ReceivableOverdue","action":"JsonAjax","mode":"getListViewCount","BugFreeQuery":form};
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
                                    "module": 'ReceivableOverdue',
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
            var contractid = tr.data('contractid');
            var stage = tr.data('stage');
            console.log(stage);
            var postData = {
                "module": 'ReceivableOverdue',
                "action": "ChangeAjax",
                'mode': 'checkLog',
                'checktype': 'ReceivableOverdue',
                "contractid": contractid,
                'stage': stage,
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
    registerEvents : function() {
        this._super();
        this.followUp();
        this.saveComment();
        this.exportReceivableOverdue();
        this.collate();
        this.checklog();
    }
});