/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("Schooladoptpeople_List_Js",{},{

	init : function () {
		$('#Schooladoptpeople_listView_basicAction_LBL_ADD_RECORD').hide();
	},
	set_adoptpeople : function () {
		$(document).on('click', '.set_adoptpeople', function () {
			var msg = {'message': '考核设置',"width":"500px"};
            var $select_tr = $(this).closest('tr');
			var recordId = $select_tr.data('id');
			var me = this;

			// 请求数据
			var module = app.getModuleName();
			var postData = {"module": module,"action": "BasicAjax","record": recordId,'mode': 'get_adoptpeople_data'};
			//var Message = app.vtranslate('JS_RECORD_GETTING_'+type);
			var progressIndicatorElement = jQuery.progressIndicator({'message' : '正在请求...','position' : 'html','blockInfo' : {'enabled' : true}});
			AppConnector.request(postData).then(
				function(data){
					progressIndicatorElement.progressIndicator({
						'mode' : 'hide'
					});
					if (data.success) {
						if (data.result) {
							aaaaa(data.result);
						}
						
					}
				},
				function(error,err){}
			);

			function aaaaa(resData) {
				Vtiger_Helper_Js.showConfirmationBox(msg).then(
					function(e) {
						var p_assessmentdate = $('#p_assessmentdate').val();
						var p_assessmentresult = $('#p_assessmentresult').val();
						var p_instructor = $('#ddddd').val();

						if(p_assessmentresult == 'assessmentresult_yes') {
							if( ! (p_instructor && p_assessmentdate) ) {
								var  params = {text : '考核时间和教官不能为空', title : '提示'};
								Vtiger_Helper_Js.showPnotify(params);
								return false;
							}
						}
						var module = app.getModuleName();
						var postData = {
							"module": module,
							"action": "BasicAjax",
							"record": recordId,
							'mode': 'set_assessmentresult',
							assessmentdate : p_assessmentdate,
							assessmentresult : p_assessmentresult,
							instructor : p_instructor,
						};
						//var Message = app.vtranslate('JS_RECORD_GETTING_'+type);
						var progressIndicatorElement = jQuery.progressIndicator({
								'message' : '正在提交...',
								'position' : 'html',
								'blockInfo' : {'enabled' : true}
								});
						AppConnector.request(postData).then(
							function(data){
								progressIndicatorElement.progressIndicator({
									'mode' : 'hide'
								});
								if (data.success) {
									var  params = {text : '更改成功', title : '提示'};
									Vtiger_Helper_Js.showMessage(params);
									var t = {'assessmentresult_yes': '通过', 'assessmentresult_no':'未通过'};
									$select_tr.find('.td_iassessmentdate').html(p_assessmentdate);
									$select_tr.find('.td_assessmentresult').html(t[p_assessmentresult]);
									$select_tr.find('.td_instructor').html(accessible_users_data[p_instructor]);
									//$select_tr.find('.td_reportdate').html(reportsdate);

									//$(me).remove();
								}
							},
							function(error,err){}
						);
					},function(error, err){}
				);

				var accessible_users = '<select id="ddddd" style="width: 200px;" class="chzn-select" name="reportsower">';
				accessible_users += '<option value="">请选择</option>';
				var select_seleced = '';
				for (var i in accessible_users_data) {
					if (resData['instructor'] == i) {
						select_seleced = 'selected';
					} else {
						select_seleced = '';
					}
					accessible_users += '<option '+select_seleced+' value="' + i + '">' + accessible_users_data[i] + '</option>';
				}


				var is_train_checked = '';
				if (resData['is_train'] == '1') {
					is_train_checked = ' checked disabled readonly ';
				}

				var assessmentresult_html = '';
				var t = {'assessmentresult_yes': '通过', 'assessmentresult_no':'未通过'};
				for (var i in t) {
					if (resData['assessmentresult'] == i) {
						assessmentresult_html += '<option selected value="'+i+'">'+t[i]+'</option>';
					} else {
						assessmentresult_html += '<option value="'+i+'">'+t[i]+'</option>';
					}
					
				}
				//var ss = '<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0"><tbody><tr><td class="fieldLabel medium"><label class="muted">已培训:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input type="checkbox" readonly disabled checked name="is_train" id="list_train" value="1"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">培训开始时间:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input id="schoolqualifiedpeople_editView_date" type="text" class="span9 dateField" name="reportsdate"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">培训结束时间:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input id="schoolqualifiedpeople_editView_date" type="text" class="span9 dateField" name="reportsdate"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">培训合格:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input type="checkbox" readonly disabled checked name="is_train" id="list_train" value="1"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">参与考核:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input type="checkbox" readonly disabled checked name="is_train" id="list_train" value="1"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">考核人员:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input type="checkbox" readonly disabled checked name="is_train" id="list_train" value="1"></span></div></td></tr></tbody></table>';
				//var ss = '<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0"><tbody><tr><td class="fieldLabel medium"><label class="muted">已培训:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input type="checkbox" name="is_train" '+is_train_checked+' id="p_is_train" value="1"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">培训开始时间:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input id="p_trainstartdate" type="text" value="'+resData['trainstartdate']+'" class="span9 dateField" name="reportsdate"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">培训结束时间:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input id="p_trainenddate" value="'+resData['trainenddate']+'" type="text" class="span9 dateField" name="reportsdate"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">培训合格:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input id="p_is_trainok" '+is_trainok_checked+' type="checkbox" name="" value="1"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">参与考核:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input id="p_is_assessment" '+is_assessment_checked+' type="checkbox" name="is_train" id="list_train" value="1"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">考核人员:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10">'+accessible_users+'</span></div></td></tr></tbody></table>';
				var ss = '<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0"><tbody><tr><td class="fieldLabel medium"><label class="muted">已培训:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><select id="p_assessmentresult">'+assessmentresult_html+'</select></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">考核时间:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input id="p_assessmentdate" type="text" value="'+resData['assessmentdate']+'" class="span9 dateField" name="assessmentdate"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">教官:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10">'+accessible_users+'</span></div></td></tr></tbody></table>';
				$('.modal-body').append(ss);
				//$.datetimepicker.setLocale('ch');
				$('#p_assessmentdate').datetimepicker({format: "yyyy-mm-dd hh:ii",
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
			}
		});
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
                _this.doadoptpeople(ids);
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
                _this.doadoptpeople(ids);
                var recordId = $(this).closest('tr');
                recordId.find('.deletedflag').remove();
                event.stopPropagation();//阻止事件冒泡
            }

        });
    },
    doadoptpeople:function(ids){
        var msg = {'message': '考核设置',"width":"500px"};
        var $select_tr = $(this).closest('tr');
        var recordId = $select_tr.data('id');
        var me = this;

        // 请求数据
        var module = app.getModuleName();
        var postData = {"module": module,"action": "BasicAjax","record": recordId,'mode': 'get_adoptpeople_data'};
        //var Message = app.vtranslate('JS_RECORD_GETTING_'+type);
        var progressIndicatorElement = jQuery.progressIndicator({'message' : '正在请求...','position' : 'html','blockInfo' : {'enabled' : true}});
        AppConnector.request(postData).then(
            function(data){
                progressIndicatorElement.progressIndicator({
                    'mode' : 'hide'
                });
                if (data.success) {
                    if (data.result) {
                        aaaaa(data.result);
                    }

                }
            },
            function(error,err){}
        );

        function aaaaa(resData) {
            Vtiger_Helper_Js.showConfirmationBox(msg).then(
                function(e) {
                    var p_assessmentdate = $('#p_assessmentdate').val();
                    var p_assessmentresult = $('#p_assessmentresult').val();
                    var p_instructor = $('#ddddd').val();

                    if(p_assessmentresult == 'assessmentresult_yes') {
                        if( ! (p_instructor) ) {
                            var  params = {text : '考核时间和教官不能为空', title : '提示'};
                            Vtiger_Helper_Js.showPnotify(params);
                            return false;
                        }
                    }
                    var module = app.getModuleName();
                    var postData = {
                        "module": module,
                        "action": "BasicAjax",
                        "records": ids,
                        'mode': 'set_assessmentresult',
                        assessmentdate : p_assessmentdate,
                        assessmentresult : p_assessmentresult,
                        instructor : p_instructor,
                    };
                    //var Message = app.vtranslate('JS_RECORD_GETTING_'+type);
                    var progressIndicatorElement = jQuery.progressIndicator({
                        'message' : '正在提交...',
                        'position' : 'html',
                        'blockInfo' : {'enabled' : true}
                    });
                    AppConnector.request(postData).then(
                        function(data){
                            progressIndicatorElement.progressIndicator({
                                'mode' : 'hide'
                            });
                            if (data.success) {
                                var  params = {text : '更改成功', title : '提示'};
                                Vtiger_Helper_Js.showMessage(params);
                                var t = {'assessmentresult_yes': '通过', 'assessmentresult_no':'未通过'};
                                $select_tr.find('.td_iassessmentdate').html(p_assessmentdate);
                                $select_tr.find('.td_assessmentresult').html(t[p_assessmentresult]);
                                $select_tr.find('.td_instructor').html(accessible_users_data[p_instructor]);
                                //$select_tr.find('.td_reportdate').html(reportsdate);

                                //$(me).remove();
                            }
                        },
                        function(error,err){}
                    );
                },function(error, err){}
            );

            var accessible_users = '<select id="ddddd" style="width: 200px;" class="chzn-select" name="reportsower">';
            accessible_users += '<option value="">请选择</option>';
            var select_seleced = '';
            for (var i in accessible_users_data) {
                if (resData['instructor'] == i) {
                    select_seleced = 'selected';
                } else {
                    select_seleced = '';
                }
                accessible_users += '<option '+select_seleced+' value="' + i + '">' + accessible_users_data[i] + '</option>';
            }


            var is_train_checked = '';
            if (resData['is_train'] == '1') {
                is_train_checked = ' checked disabled readonly ';
            }

            var assessmentresult_html = '';
            var t = {'assessmentresult_yes': '通过', 'assessmentresult_no':'未通过'};
            for (var i in t) {
                if (resData['assessmentresult'] == i) {
                    assessmentresult_html += '<option selected value="'+i+'">'+t[i]+'</option>';
                } else {
                    assessmentresult_html += '<option value="'+i+'">'+t[i]+'</option>';
                }

            }
            //var ss = '<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0"><tbody><tr><td class="fieldLabel medium"><label class="muted">已培训:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input type="checkbox" readonly disabled checked name="is_train" id="list_train" value="1"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">培训开始时间:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input id="schoolqualifiedpeople_editView_date" type="text" class="span9 dateField" name="reportsdate"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">培训结束时间:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input id="schoolqualifiedpeople_editView_date" type="text" class="span9 dateField" name="reportsdate"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">培训合格:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input type="checkbox" readonly disabled checked name="is_train" id="list_train" value="1"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">参与考核:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input type="checkbox" readonly disabled checked name="is_train" id="list_train" value="1"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">考核人员:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input type="checkbox" readonly disabled checked name="is_train" id="list_train" value="1"></span></div></td></tr></tbody></table>';
            //var ss = '<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0"><tbody><tr><td class="fieldLabel medium"><label class="muted">已培训:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input type="checkbox" name="is_train" '+is_train_checked+' id="p_is_train" value="1"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">培训开始时间:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input id="p_trainstartdate" type="text" value="'+resData['trainstartdate']+'" class="span9 dateField" name="reportsdate"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">培训结束时间:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input id="p_trainenddate" value="'+resData['trainenddate']+'" type="text" class="span9 dateField" name="reportsdate"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">培训合格:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input id="p_is_trainok" '+is_trainok_checked+' type="checkbox" name="" value="1"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">参与考核:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input id="p_is_assessment" '+is_assessment_checked+' type="checkbox" name="is_train" id="list_train" value="1"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">考核人员:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10">'+accessible_users+'</span></div></td></tr></tbody></table>';
            var ss = '<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0"><tbody><tr><td class="fieldLabel medium"><label class="muted">已培训:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><select id="p_assessmentresult">'+assessmentresult_html+'</select></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">考核时间:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input id="p_assessmentdate" type="text" value="" class="span9 dateField" name="assessmentdate"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">教官:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10">'+accessible_users+'</span></div></td></tr></tbody></table>';
            $('.modal-body').append(ss);
            //$.datetimepicker.setLocale('ch');
            $('#p_assessmentdate').datetimepicker({format: "yyyy-mm-dd hh:ii",
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
        }
	},
	registerEvents : function(){
		this._super();
		//this.registerLoadAjaxEvent();
		this.init();
		this.set_adoptpeople();
		this.registerChangeRecordClickEvent();
	}

});