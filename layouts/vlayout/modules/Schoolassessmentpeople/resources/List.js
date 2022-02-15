/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("Schoolassessmentpeople_List_Js",{},{
	set_assessmentresult: function (e) {
		$('.set_assessmentresult').click(function () {
			var msg = {
                'message': '更改考核状态',
                "width":"400px",
            };
			
			var $select_tr = $(this).closest('tr');
			var recordId = $select_tr.data('id');
			var me = this;
			Vtiger_Helper_Js.showConfirmationBox(msg).then(
				function(e) {
					var selectValue = $('#receivedstatus').val();
					var assessmentdate = $('#assessmentdate').val();
					var assessownerid = $('#ddddd').val();
					var module = app.getModuleName();
					var postData = {
						"module": module,
						"action": "BasicAjax",
						"records": recordId,
						'status': selectValue,
						'assessmentdate':assessmentdate,
						'mode': 'setAssessmentresult',
						assessownerid : assessownerid
						//"parent": app.getParentModuleName()
					}
					if (!assessmentdate) {
						var  params = {text : app.vtranslate(''),
								title : app.vtranslate('考核日期不能为空')}
								Vtiger_Helper_Js.showPnotify(params);
							return false;
					}
					
					var Message = app.vtranslate('正在请求...');
					
					var progressIndicatorElement = jQuery.progressIndicator({
							'message' : Message,
							'position' : 'html',
							'blockInfo' : {'enabled' : true}
							});
					AppConnector.request(postData).then(
						function(data){
							progressIndicatorElement.progressIndicator({'mode' : 'hide'});
							if (data.success) {
								var t = {'assessmentresult_yes': '通过', 'assessmentresult_no': '未通过'};
								$select_tr.find('.assessmentresult_value').html(t[selectValue]);
								$select_tr.find('.assessmentdate_value').html(assessmentdate);
								
								$(me).attr('data-status', selectValue);
								//alert('更新合同自动关闭状态成功');
								var  params = {text : '更改考核状态成功', title : '提示'};
								Vtiger_Helper_Js.showMessage(params);
							} else {
								var  params = {text : '更改考核状态失败', title : '提示'};
								Vtiger_Helper_Js.showMessage(params);
							}


								//location.reload();
							
						},
						function(error,err){}
					);
				},function(error, err){}
			);

			var str = '';
			var temp_str = {
				'assessmentresult_yes': '通过',
				'assessmentresult_no': '未通过',
			};
			var status = $(this).attr('data-status');
			for(var index in temp_str) {
				if (index == status) {
					str += '<option selected="selected" value="'+ index +'">'+ temp_str[index] +'</option>';
				} else {
					str += '<option value="'+ index +'">'+ temp_str[index] +'</option>';
				}
			}
			//var sss = '<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">审核状态:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><select id="receivedstatus">'+str+'</select></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">审核时间:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><input type="text" name="assessmentdate" id="assessmentdate"></span></div></td></tr></tbody></table>';
			var sss = '<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">考核状态:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><select id="receivedstatus">'+str+'</select></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">考核时间:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><input type="text" name="assessmentdate" id="assessmentdate"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">新兵营负责人:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid">'+accessible_users+'</div></td></tr></tbody></table>';
			$('.modal-body').append(sss);
			$('#assessmentdate').datetimepicker({format: "yyyy-mm-dd hh:ii",
					language:  'zh-CN',
			        autoclose: true,
			        todayBtn: true,
			        pickerPosition: "bottom-left",
			        showMeridian: false,
			        format: "yyyy-mm-dd",
			        timepicker:false,
			        minView: "month",
			        forceParse:0,
		            startDate:new Date()});
			$('#ddddd').select2();
			$.fn.modal.Constructor.prototype.enforceFocus = function () {};
			//$('.modal-body').append('<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0;"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">审核状态:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><select id="receivedstatus">'+str+'</span></div></td></tr></tbody></table>');
		});
	},
	init : function () {
		$('#Schoolassessmentpeople_listView_basicAction_LBL_ADD_RECORD').hide();
	},
    doassessmentresult:function(ids){
        var msg = {
            'message': '更改考核状态',
            "width":"400px",
        };

        //var $select_tr = $(this).closest('tr');
        //var recordId = $select_tr.data('id');
        var me = this;
        Vtiger_Helper_Js.showConfirmationBox(msg).then(
            function(e) {
                var selectValue = $('#receivedstatus').val();
                var assessmentdate = $('#assessmentdate').val();
                var assessownerid = $('#ddddd').val();
                var module = app.getModuleName();
                var postData = {
                    "module": module,
                    "action": "BasicAjax",
                    "records": ids,
                    'status': selectValue,
                    'assessmentdate':assessmentdate,
                    'mode': 'setAssessmentresult',
                    assessownerid : assessownerid
                    //"parent": app.getParentModuleName()
                }
                if (!assessmentdate) {
                    var  params = {text : app.vtranslate(''),
                        title : app.vtranslate('考核日期不能为空')}
                    Vtiger_Helper_Js.showPnotify(params);
                    return false;
                }

                var Message = app.vtranslate('正在请求...');

                var progressIndicatorElement = jQuery.progressIndicator({
                    'message' : Message,
                    'position' : 'html',
                    'blockInfo' : {'enabled' : true}
                });
                AppConnector.request(postData).then(
                    function(data){
                        progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                        if (data.success) {
                            var t = {'assessmentresult_yes': '通过', 'assessmentresult_no': '未通过'};
                            $select_tr.find('.assessmentresult_value').html(t[selectValue]);
                            $select_tr.find('.assessmentdate_value').html(assessmentdate);

                            $(me).attr('data-status', selectValue);
                            //alert('更新合同自动关闭状态成功');
                            var  params = {text : '更改考核状态成功', title : '提示'};
                            Vtiger_Helper_Js.showMessage(params);
                        } else {
                            var  params = {text : '更改考核状态失败', title : '提示'};
                            Vtiger_Helper_Js.showMessage(params);
                        }


                        //location.reload();

                    },
                    function(error,err){}
                );
            },function(error, err){}
        );

        var str = '';
        var temp_str = {
            'assessmentresult_yes': '通过',
            'assessmentresult_no': '未通过',
        };
        var status = $(this).attr('data-status');
        for(var index in temp_str) {
            if (index == status) {
                str += '<option selected="selected" value="'+ index +'">'+ temp_str[index] +'</option>';
            } else {
                str += '<option value="'+ index +'">'+ temp_str[index] +'</option>';
            }
        }
        //var sss = '<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">审核状态:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><select id="receivedstatus">'+str+'</select></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">审核时间:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><input type="text" name="assessmentdate" id="assessmentdate"></span></div></td></tr></tbody></table>';
        var sss = '<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">审核状态:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><select id="receivedstatus">'+str+'</select></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">审核时间:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><input type="text" name="assessmentdate" id="assessmentdate"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">新兵营负责人:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid">'+accessible_users+'</div></td></tr></tbody></table>';
        $('.modal-body').append(sss);
        $('#assessmentdate').datetimepicker({format: "yyyy-mm-dd hh:ii",
            language:  'zh-CN',
            autoclose: true,
            todayBtn: true,
            pickerPosition: "bottom-left",
            showMeridian: false,
            format: "yyyy-mm-dd",
            timepicker:false,
            minView: "month",
            forceParse:0,
            startDate:new Date()});
        $('#ddddd').select2();
        $.fn.modal.Constructor.prototype.enforceFocus = function () {};
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
            var ids='';
            $.each($('input[name="Detailrecord\[\]"]:checkbox:checked'),function(key,value){
                ids+=$(value).val()+',';
            });
            ids=ids.substr(0,ids.length-1);
            if(''!=ids){
                _this.doassessmentresult(ids);
                $.each($('input[name="Detailrecord\[\]"]:checkbox:checked'), function (key, value) {
                    var recordId = $(value).closest('tr');
                    recordId.find('.deletedflag').remove();
                });
            }
            event.stopPropagation();//阻止事件冒泡
        });
        listViewContentDiv.on("click",".stamp",function(event){
            var ids=$(this).data('id');
            if(''!=ids){
                _this.doassessmentresult(ids);
                var recordId = $(this).closest('tr');
                recordId.find('.deletedflag').remove();
                event.stopPropagation();//阻止事件冒泡
            }

        });
    },
	registerEvents : function(){
		this._super();
		this.set_assessmentresult();
		this.init();
		this.registerChangeRecordClickEvent();
	}

});