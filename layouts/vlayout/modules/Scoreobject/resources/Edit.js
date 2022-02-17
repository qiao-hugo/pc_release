/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("Scoreobject_Edit_Js",{},{
	scorepara_rule_num : {

	},
	scorepara_rule : {
		o_number : {
			item : {
				'required': false,
				'msg': ''
			},
			upper : {
				'required': false,
				'msg': ''
			},
			lower : {
				'required': false,
				'msg': ''
			},
			score : {
				'required': false,
				'msg': ''
			}
		},

		o_check : {
			item : {
				'required': true,
				'msg': '(必填)'
			},
			upper : {
				'required': false,
				'msg': ''
			},
			lower : {
				'required': false,
				'msg': ''
			},
			score : {
				'required': true,
				'msg': '(必填)'
			}
		},

		o_numberinterval : {
			item : {
				'required': false,
				'msg': ''
			},
			upper : {
				'required': true,
				'msg': '(必填)'
			},
			lower : {
				'required': true,
				'msg': '(必填)'
			},
			score : {
				'required': true,
				'msg': '(必填)'
			}
		},

		o_text : {
			item : {
				'required': false,
				'msg': ''
			},
			upper : {
				'required': true,
				'msg': '【文字个数】(必填)'
			},
			lower : {
				'required': true,
				'msg': '【文字个数】(必填)'
			},
			score : {
				'required': true,
				'msg': '(必填)'
			}
		},
		o_select : {
			item : {
				'required': true,
				'msg': '(必填)'
			},
			upper : {
				'required': false,
				'msg': ''
			},
			lower : {
				'required': false,
				'msg': ''
			},
			score : {
				'required': true,
				'msg': '(必填)'
			}
		},
		o_radio : {
			item : {
				'required': true,
				'msg': '(必填)'
			},
			upper : {
				'required': false,
				'msg': ''
			},
			lower : {
				'required': false,
				'msg': ''
			},
			score : {
				'required': true,
				'msg': '(必填)'
			}
		},
	},

	init: function() {
		var me = this;
		$(document).on('keyup', 'input[type=number]', function () {
			var t = parseInt($(this).val());
			if(!NaN) {
				$(this).val(t);
			}
		});
		$('select[name=scoreobject_type]').change(function () {
			var scoreobject_type = $(this).val();
			if (scoreobject_type == 'o_number') {
				$('#add_scorepara').hide();
				$('.scorepara_row').remove();
			} else {
				$('#add_scorepara').show();
				me.setMark();
			}
		});
		$(document).on('click', '.delete_scorepara', function () {
			$(this).closest('tr').remove();
		});
		me.setMark();
	},

	add_test: function () {
		var me = this;
		$('#add_test').click(function() {
			
		});
	},
	registerRecordPreSaveEvent : function(form) {
		var me = this;
		if(typeof form == 'undefined') {
			form = this.getForm();
		}
		form.on(Vtiger_Edit_Js.recordPreSave, function(e, data) {

			var flag = false;
			var scoreobject_type = $('select[name=scoreobject_type]').val();
			if (scoreobject_type && scoreobject_type != 'o_number') {
				var o = me.scorepara_rule[scoreobject_type];
				$('.scorepara_row').each(function () {
					for(var i in o) {
						// 不能为空
						if(o[i]['required']) {
							var $input = $(this).find('.input_scorepara_'+i);
							var t = $.trim($input.val());
							if (!t){
								$input.attr('data-title','');
								$input.attr('data-content','<span style="color:red;">不能为空</span>');
								$input.popover('show');
								flag = true;
							}
						}
					}
					
				});
				if ($('.scorepara_row').size()==0) {
					flag = true;
					var  params = {text : app.vtranslate(),title : app.vtranslate('组件参数必须添加')};
        			Vtiger_Helper_Js.showPnotify(params);
				}
			} 
			if(flag) {
				e.preventDefault();
				return false;
			}

			// 判断参数名称不能重复
			var module = app.getModuleName();
			var postData = {
				"module": module,
				"action": "BasicAjax",
				"mode" : "isCheckTow",
				"scoreobject_name": $('input[name=scoreobject_name]').val(),
				"record": $('input[name=record]').val()
			}
			var Message = app.vtranslate('正在提交...');
			var progressIndicatorElement = jQuery.progressIndicator({
					'message' : Message,
					'position' : 'html',
					'blockInfo' : {'enabled' : true}
					});
			
			if (!me.flag) {
				AppConnector.request(postData).then(
					function(data){
						progressIndicatorElement.progressIndicator({
									'mode' : 'hide'
								});

						if(data.success) {
							var result = data['result'];
							if (result.is_check == 1) {
								var  params = {text : result.message, title : '错误提示'};
								Vtiger_Helper_Js.showPnotify(params);
							} else {
								me.flag = true;
								form.submit();
							}
						} else {
							return false;
						}
					},
					function(error,err){

					}
				);
				e.preventDefault();
			}
			//e.preventDefault();
            
		})
	},

	setMark: function() {
		var scoreobject_type = $('select[name=scoreobject_type]').val();
		if (scoreobject_type) {
			var o = this.scorepara_rule[scoreobject_type];
			$('.scorepara_item_mark').html(o['item']['msg']);
			$('.scorepara_upper_mark').html(o['upper']['msg']);
			$('.scorepara_lower_mark').html(o['lower']['msg']);
			$('.scorepara_score_mark').html(o['score']['msg']);
		}
		
	},

	add_scorepara: function() {
		$('#add_scorepara').click(function () {
			var num = $('.scorepara_row').length + 1;
			if (num > 100) {return ;}
			var t_num=$('.scorepara_row').last().data('num');
	        if(t_num){
	            num=t_num+1;
	        }
			var t_scorepara_row_html = scorepara_row_html.replace(/\[\]/g,'['+num+']');
	        t_scorepara_row_html = t_scorepara_row_html.replace(/reg_scorepara_num/g, num);
			$('.scorepara_tab').append(t_scorepara_row_html);
		});
	},


	/**
		pop two calander.
	*/
	registerBasicEvents:function(container){
		this._super(container);
		this.init();
		this.add_scorepara();
		this.registerRecordPreSaveEvent(container);
		this.add_test();
	}
});




















