/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("Schoolqualifiedpeople_List_Js",{},{

	init : function () {
		$('#Schoolqualifiedpeople_listView_basicAction_LBL_ADD_RECORD').hide();
	},
	set_report: function () {
		$(document).on('click' , '.set_report', function() {
			var msg = {'message': '已报道设置',"width":"500px"};
 
            var $select_tr = $(this).closest('tr');
			var recordId = $select_tr.data('id');
			var me = this;

			Vtiger_Helper_Js.showConfirmationBox(msg).then(
				function(e) {
					var reportsdate = $('#schoolqualifiedpeople_editView_date').val();
					var train = $('#list_train').val();
					
					var module = app.getModuleName();
					var postData = {
						"module": module,
						"action": "BasicAjax",
						"record": recordId,
						'mode': 'set_report',
						reportsdate : reportsdate,
						train : train
						//"parent": app.getParentModuleName()
					};
					if (! reportsdate) {
						var  params = {text : app.vtranslate('请输入报道时间'),
							title : app.vtranslate('')}
							Vtiger_Helper_Js.showPnotify(params);
						return false;
					}

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

								$select_tr.find('.td_is_report').html('是');
								$select_tr.find('.td_reportdate').html(reportsdate);
								$select_tr.find('.set_train').show();
								$(me).remove();
							}
						},
						function(error,err){}
					);
				},function(error, err){}
			);

			var ss = '<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0"><tbody><tr><td class="fieldLabel medium"><label class="muted">已报道:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input type="checkbox" readonly disabled="true" checked  name="is_train" id="list_train" value="1"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">报道时间:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input id="schoolqualifiedpeople_editView_date" type="text" class="span9 dateField" name="reportsdate"></span></div></td></tr></tbody></table>';
			$('.modal-body').append(ss);
			//$.datetimepicker.setLocale('ch');
			$('#schoolqualifiedpeople_editView_date').datetimepicker({format: "yyyy-mm-dd hh:ii",
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
			$.fn.modal.Constructor.prototype.enforceFocus = function () {};
		});
	},

	set_train: function () {
		$(document).on('click' , '.set_train', function() {
			var msg = {'message': '已培训设置',"width":"500px"};
 
            var $select_tr = $(this).closest('tr');
			var recordId = $select_tr.data('id');
			var me = this;


			// 请求数据
			var module = app.getModuleName();
			var postData = {"module": module,"action": "BasicAjax","record": recordId,'mode': 'get_train_data'};
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
						var p_is_train = $('#p_is_train').attr('checked');
						var p_is_assessment = $('#p_is_assessment').attr('checked');
						var p_is_trainok = $('#p_is_trainok').attr('checked');
						var p_trainstartdate = $('#p_trainstartdate').val();
						var p_trainenddate = $('#p_trainenddate').val();
						var p_assessmentuser = $('#ddddd').val();

						// 判断参数是否合法
						if (p_is_train == undefined || p_trainstartdate == '' || p_trainenddate == '') {
							var  params = {text : '已培训，培训开始时间，培训结束时间不能为空', title : '提示'};
							Vtiger_Helper_Js.showPnotify(params);
							return false;
						}
						if (p_is_assessment == 'checked' && p_assessmentuser == '') {
							var  params = {text : app.vtranslate(''),
								title : app.vtranslate('考核人员不能为空')}
								Vtiger_Helper_Js.showPnotify(params);
							return false;
						}
						p_is_train = p_is_train == 'checked' ? '1' : '0';
						p_is_assessment = p_is_assessment == 'checked' ? '1' : '0';
						p_is_trainok = p_is_trainok == 'checked' ? '1' : '0';
						
						var module = app.getModuleName();
						var postData = {
							"module": module,
							"action": "BasicAjax",
							"record": recordId,
							'mode': 'set_train',
							is_train : p_is_train,
							is_assessment : p_is_assessment,
							is_trainok : p_is_trainok,
							trainstartdate : p_trainstartdate,
							trainenddate : p_trainenddate,
							assessmentuser : p_assessmentuser
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

									var t = {'0': '否', '1': '是'};
									$select_tr.find('.td_is_assessment').html(t[p_is_assessment]);
									$select_tr.find('.td_is_trainok').html(t[p_is_trainok]);
									$select_tr.find('.td_is_train').html(t[p_is_assessment]);

									$select_tr.find('.td_trainstartdate').html(p_trainstartdate);
									$select_tr.find('.td_trainenddate').html(p_trainenddate);
									$select_tr.find('.td_assessmentuser').html(accessible_users_data[p_assessmentuser]);
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
					
					if (resData['assessmentuser'] == i) {
						select_seleced = 'selected';
					} else {
						select_seleced = '';
					}
					accessible_users += '<option '+select_seleced+' value="' + i + '">' + accessible_users_data[i] + '</option>';
				}

				var is_assessment_checked = '';
				if (resData['is_assessment'] == '1') {
					is_assessment_checked = ' checked disabled readonly ';
				}

				var is_train_checked = '';
				if (resData['is_train'] == '1') {
					is_train_checked = ' checked disabled readonly ';
				}

				var is_trainok_checked = '';
				if (resData['is_trainok'] == '1') {
					is_trainok_checked = ' checked disabled readonly ';
				}
				resData['trainenddate'] = resData['trainenddate'] || '';
				resData['trainstartdate'] = resData['trainstartdate'] || '';

				//var ss = '<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0"><tbody><tr><td class="fieldLabel medium"><label class="muted">已培训:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input type="checkbox" readonly disabled checked name="is_train" id="list_train" value="1"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">培训开始时间:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input id="schoolqualifiedpeople_editView_date" type="text" class="span9 dateField" name="reportsdate"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">培训结束时间:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input id="schoolqualifiedpeople_editView_date" type="text" class="span9 dateField" name="reportsdate"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">培训合格:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input type="checkbox" readonly disabled checked name="is_train" id="list_train" value="1"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">参与考核:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input type="checkbox" readonly disabled checked name="is_train" id="list_train" value="1"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">考核人员:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input type="checkbox" readonly disabled checked name="is_train" id="list_train" value="1"></span></div></td></tr></tbody></table>';
				var ss = '<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0"><tbody><tr><td class="fieldLabel medium"><label class="muted">已培训:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input type="checkbox" name="is_train" '+is_train_checked+' id="p_is_train" value="1"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">培训开始时间:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input id="p_trainstartdate" type="text" value="'+resData['trainstartdate']+'" class="span9 dateField" name="reportsdate"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">培训结束时间:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input id="p_trainenddate" value="'+resData['trainenddate']+'" type="text" class="span9 dateField" name="reportsdate"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">培训合格:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input id="p_is_trainok" '+is_trainok_checked+' type="checkbox" name="" value="1"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">参与考核:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input id="p_is_assessment" '+is_assessment_checked+' type="checkbox" name="is_train" id="list_train" value="1"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">考核人员:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10">'+accessible_users+'</span></div></td></tr></tbody></table>';
				
				$('.modal-body').append(ss);
				//$.datetimepicker.setLocale('ch');
				$('#p_trainstartdate, #p_trainenddate').datetimepicker({format: "yyyy-mm-dd hh:ii",
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
    doStamp:function(ids){
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
                var  params = {
                    text : '操作成功',
                    title : ''
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
        listViewContentDiv.on("click",".reportall,.trainall,.traincompleteall",function(event){

        	var current=$(this).data('type');
            var ids='';
            $.each($('input[name="Detailrecord\[\]"]:checkbox:checked'),function(key,value){
            	if($(value).data('type')==current){
                    ids+=$(value).val()+',';
				}

            });
            ids=ids.substr(0,ids.length-1);
            if(''!=ids){
                //_this.showConfirmationBoxInstance(ids);
				_this[current](ids);
                $.each($('input[name="Detailrecord\[\]"]:checkbox:checked'), function (key, value) {
                    if($(value).data('type')==current) {
                        var recordId = $(value).closest('tr');
                        recordId.find('.deletedflag').remove();
                    }
                });
            }
            event.stopPropagation();//阻止事件冒泡
        });
        listViewContentDiv.on("click",".report,.train,.traincomplete",function(event){
            var ids=$(this).data('id');
            var current=$(this).data('type')
            if(''!=ids){
                _this[current](ids);
                var recordId = $(this).closest('tr');
                recordId.find('.deletedflag').remove();
                event.stopPropagation();//阻止事件冒泡
            }

        });
    },
    /**
	 * 报道
     */
	report:function(ids){

        var msg = {'message': '<h3>已报道设置</h3>',"width":"500px"};

        //var $select_tr = $(this).closest('tr');
        //var recordId = $select_tr.data('id');
        var me = this;

        Vtiger_Helper_Js.showConfirmationBox(msg).then(
            function(e) {
                var reportsdate = $('#schoolqualifiedpeople_editView_date').val();
                var train = $('#list_train').val();

                var module = app.getModuleName();
                var postData = {
                    "module": module,
                    "action": "BasicAjax",
                    "records": ids,
                    'mode': 'set_report',
                    reportsdate : reportsdate,
                    train : train
                    //"parent": app.getParentModuleName()
                };
                if (! reportsdate) {
                    var  params = {text : app.vtranslate('请输入报道时间'),
                        title : app.vtranslate('')}
                    Vtiger_Helper_Js.showPnotify(params);
                    return false;
                }

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

                            $select_tr.find('.td_is_report').html('是');
                            $select_tr.find('.td_reportdate').html(reportsdate);
                            $select_tr.find('.set_train').show();
                            $(me).remove();
                        }
                    },
                    function(error,err){}
                );
            },function(error, err){}
        );

        var ss = '<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0"><tbody><tr><td class="fieldLabel medium"><label class="muted">已报道:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input type="checkbox" readonly disabled="true" checked  name="is_train" id="list_train" value="1"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">报道时间:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input id="schoolqualifiedpeople_editView_date" type="text" class="span9 dateField" name="reportsdate"></span></div></td></tr></tbody></table>';
        $('.modal-body').append(ss);
        //$.datetimepicker.setLocale('ch');
        $('#schoolqualifiedpeople_editView_date').datetimepicker({format: "yyyy-mm-dd hh:ii",
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
        $.fn.modal.Constructor.prototype.enforceFocus = function () {};
	},
    /**
	 * 培训师
     * @param ids
     */
    train:function(ids){


        var msg = {'message': '<h3>培训师</h3>',"width":"500px"};

            Vtiger_Helper_Js.showConfirmationBox(msg).then(
                function(e) {
                    var p_assessmentuser = $('#ddddd').val();
                    if (p_assessmentuser == '') {
                        var  params = {text : app.vtranslate(''),
                            title : app.vtranslate('考核人员不能为空')}
                        Vtiger_Helper_Js.showPnotify(params);
                        return false;
                    }

                    var module = app.getModuleName();
                    var postData = {
                        "module": module,
                        "action": "BasicAjax",
                        "records": ids,
                        'mode': 'set_trainer',
                        assessmentuser : p_assessmentuser
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

                accessible_users += '<option value="' + i + '">' + accessible_users_data[i] + '</option>';
            }
            var ss = '<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0"><tbody><tr><td class="fieldLabel medium"><label class="muted">培训师:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10">'+accessible_users+'</span></div></td></tr></tbody></table>';

            $('.modal-body').append(ss);
            $('#ddddd').select2();
            $.fn.modal.Constructor.prototype.enforceFocus = function () {};


	},
    /**
	 * 培训完成
     */
    traincomplete:function(ids){

        var msg = {'message': '<h3>已培训设置</h3>',"width":"500px"};

        /*var $select_tr = $(this).closest('tr');
        var recordId = $select_tr.data('id');*/
        var me = this;


        // 请求数据
        /*var module = app.getModuleName();
        var postData = {"module": module,"action": "BasicAjax","record": recordId,'mode': 'get_train_data'};
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
        );*/
        var resData=[];
        resData['assessmentuser']='';
        aaaaa(resData);
        function aaaaa(resData) {
            Vtiger_Helper_Js.showConfirmationBox(msg).then(
                function(e) {
                    var p_is_train = $('#p_is_train').attr('checked');
                    var p_is_assessment = $('#p_is_assessment').attr('checked');
                    var p_is_trainok = $('#p_is_trainok').attr('checked');
                    var p_trainstartdate = $('#p_trainstartdate').val();
                    var p_trainenddate = $('#p_trainenddate').val();
                    var p_assessmentuser = $('#ddddd').val();

                    // 判断参数是否合法
                    if (p_is_train == undefined || p_trainstartdate == '' || p_trainenddate == '') {
                        var  params = {text : '已培训，培训开始时间，培训结束时间不能为空', title : '提示'};
                        Vtiger_Helper_Js.showPnotify(params);
                        return false;
                    }
                    if (p_is_assessment == 'checked' && p_assessmentuser == '') {
                        var  params = {text : app.vtranslate(''),
                            title : app.vtranslate('考核人员不能为空')}
                        Vtiger_Helper_Js.showPnotify(params);
                        return false;
                    }
                    p_is_train = p_is_train == 'checked' ? '1' : '0';
                    p_is_assessment = p_is_assessment == 'checked' ? '1' : '0';
                    p_is_trainok = p_is_trainok == 'checked' ? '1' : '0';

                    var module = app.getModuleName();
                    var postData = {
                        "module": module,
                        "action": "BasicAjax",
                        "records": ids,
                        'mode': 'set_train',
                        is_train : p_is_train,
                        is_assessment : p_is_assessment,
                        is_trainok : p_is_trainok,
                        trainstartdate : p_trainstartdate,
                        trainenddate : p_trainenddate,
                        assessmentuser : p_assessmentuser
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

                                var t = {'0': '否', '1': '是'};
                                $select_tr.find('.td_is_assessment').html(t[p_is_assessment]);
                                $select_tr.find('.td_is_trainok').html(t[p_is_trainok]);
                                $select_tr.find('.td_is_train').html(t[p_is_assessment]);

                                $select_tr.find('.td_trainstartdate').html(p_trainstartdate);
                                $select_tr.find('.td_trainenddate').html(p_trainenddate);
                                $select_tr.find('.td_assessmentuser').html(accessible_users_data[p_assessmentuser]);
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

                if (resData['assessmentuser'] == i) {
                    select_seleced = 'selected';
                } else {
                    select_seleced = '';
                }
                accessible_users += '<option '+select_seleced+' value="' + i + '">' + accessible_users_data[i] + '</option>';
            }

            var is_assessment_checked = '';
            if (resData['is_assessment'] == '1') {
                is_assessment_checked = ' checked disabled readonly ';
            }

            var is_train_checked = '';
            if (resData['is_train'] == '1') {
                is_train_checked = ' checked disabled readonly ';
            }

            var is_trainok_checked = '';
            if (resData['is_trainok'] == '1') {
                is_trainok_checked = ' checked disabled readonly ';
            }
            resData['trainenddate'] = resData['trainenddate'] || '';
            resData['trainstartdate'] = resData['trainstartdate'] || '';

            //var ss = '<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0"><tbody><tr><td class="fieldLabel medium"><label class="muted">已培训:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input type="checkbox" readonly disabled checked name="is_train" id="list_train" value="1"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">培训开始时间:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input id="schoolqualifiedpeople_editView_date" type="text" class="span9 dateField" name="reportsdate"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">培训结束时间:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input id="schoolqualifiedpeople_editView_date" type="text" class="span9 dateField" name="reportsdate"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">培训合格:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input type="checkbox" readonly disabled checked name="is_train" id="list_train" value="1"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">参与考核:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input type="checkbox" readonly disabled checked name="is_train" id="list_train" value="1"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">考核人员:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input type="checkbox" readonly disabled checked name="is_train" id="list_train" value="1"></span></div></td></tr></tbody></table>';
            var ss = '<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0"><tbody><tr><td class="fieldLabel medium"><label class="muted">已培训:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input type="checkbox" name="is_train" '+is_train_checked+' id="p_is_train" value="1"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">培训开始时间:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input id="p_trainstartdate" type="text" value="'+resData['trainstartdate']+'" class="span9 dateField" name="reportsdate"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">培训结束时间:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input id="p_trainenddate" value="'+resData['trainenddate']+'" type="text" class="span9 dateField" name="reportsdate"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">培训合格:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input id="p_is_trainok" '+is_trainok_checked+' type="checkbox" name="" value="1"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">参与考核:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10"><input id="p_is_assessment" '+is_assessment_checked+' type="checkbox" name="is_train" id="list_train" value="1"></span></div></td></tr><tr><td class="fieldLabel medium"><label class="muted">考核人员:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid pull-left"><span class="span10">'+accessible_users+'</span></div></td></tr></tbody></table>';

            $('.modal-body').append(ss);
            //$.datetimepicker.setLocale('ch');
            $('#p_trainstartdate, #p_trainenddate').datetimepicker({format: "yyyy-mm-dd hh:ii",
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
    showConfirmationBoxInstance:function(ids){
        var _this=this;
        var msg = {
            'message': '<h3>简历录取</h3>',
            "width":"600px",
        };
        var userlist=_this.getUserList();
        _this.showConfirmationBox(msg).then(
            function(e) {
                _this.doStamp(ids);

            });
        var date=new Date();
        var month=date.getMonth()+1
        month=month<10?'0'+month:month;
        var datenow=date.getFullYear()+'-'+month+'-'+date.getDate();
        var strr='<form name="insertcomment" id="formcomment">\
                            <div id="insertcomment" style="height: 300px;overflow: auto">\
                            <table class="table table-bordered blockContainer Duplicates showInlineTable  detailview-table" data-num="1" id="comments1"><tbody>'+
            '<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>预计报道时间:</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="text" name="reportsdate" readonly id="datatime" value="'+datenow+'"/> </span></div></td></tr>' +
            '<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>预计报道地点:</label></td><td class="fieldValue medium"><div class="row-fluid"><span class="span10"><input type="text" name="reportaddress"></span></div></td></tr>' +
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
     *重写方法加入自定义验证规则
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

	registerEvents : function(){
		this._super();
		//this.registerLoadAjaxEvent();
		this.init();
		this.set_report();
		this.set_train();
		this.registerChangeRecordClickEvent();
	}

});