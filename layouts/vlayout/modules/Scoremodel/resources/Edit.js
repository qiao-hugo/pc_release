/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("Scoremodel_Edit_Js",{},{
	scorepara_rule_num : {

	},
	t_num : 0,

	

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
			console.log($('.scorepaper_itme_name').size());
			$('select.scorepaper_itme_name').each(function () {
				if(! $(this).val()) {
					console.log($(this).html());
					var  params = {text : '参数名称不能为空', title : '错误提示'};
					Vtiger_Helper_Js.showPnotify(params);
					flag = true;
					return false;
				}
			});
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
				"scoremodel_name": $('input[name=scoremodel_name]').val(),
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
		var o = this.scorepara_rule[scoreobject_type];
		$('.scorepara_item_mark').html(o['item']['msg']);
		$('.scorepara_upper_mark').html(o['upper']['msg']);
		$('.scorepara_lower_mark').html(o['lower']['msg']);
		$('.scorepara_score_mark').html(o['score']['msg']);
	},

	add_scoremodel: function() {
		var me = this;
		$('#add_scoremodel').click(function () {
			var num = 'n_' + me.t_num;
			me.t_num ++;
			/*var num = $('.scoremodel_row').length + 1;
			if (num > 100) {return ;}
			var t_num=$('.scoremodel_row').last().data('num');
	        if(t_num){
	            num=t_num+1;
	        }
	        console.log(num);*/
	        
			var t_scorepara_row_html = scoremodel_row_html.replace(/\[\]/g,'['+num+']');
	        t_scorepara_row_html = t_scorepara_row_html.replace(/reg_scoremodel_num/g, num);

	        var select_option = '<option selected="selected" value="">请选择</option>';
	        for(var i in scoreobject_json) {
	        	select_option += '<option value="'+scoreobject_json[i]['scoreobjectid']+'">'+scoreobject_json[i]['scoreobject_name']+'</option>';
	        }
	        t_scorepara_row_html = t_scorepara_row_html.replace(/reg_scoreobject_data_select/g, select_option);
			$('.scoremodel_tab').append(t_scorepara_row_html);
			$('.scorepaper_itme_name_'+num).select2();
		});
	},
	init: function() {
		var me = this;
		/*$(document).on('keyup', 'input[type=number]', function () {
			var t = parseInt($(this).val());
			if(!NaN) {
				$(this).val(t);
			}
		});*/
		$(document).on('change', '.scorepaper_itme_name', function () {
			var id = $(this).val();
			if(id) {
				var $tt = $(this).closest('tr');
				//$tt.find('.explan').html(scoreobject_json[id]['scoreobject_explain']);
				//$tt.find('.scorepaper_itme_explan').val(scoreobject_json[id]['scoreobject_explain']);
				$tt.find('.scorepaper_itme_name_dispaly').val(scoreobject_json[id]['scoreobject_name']);
				$tt.find('.scorepaper_itme_type').val(scoreobject_json[id]['scoreobject_type']);
				//
			}
			
		});
		$(document).on('blur','.scorepaper_itme_weight',function() {
			var t = $(this).val();
			var t_arr = t.split('%');
			if (t) {
				if (! /^[\d]{1,}%$/.test(t)) {
				//$(this).attr('data-title','注意').attr('data-content','权重必须是百分比').popover('show');
					$(this).val('');
	        		Vtiger_Helper_Js.showPnotify({text : app.vtranslate(),title : app.vtranslate('权重必须百分比')});
				}
			}
			
		});
		/*$(document).on('keyup','.scorepaper_itme_weight',function() {
			var t = $(this).val();
			if (! /^\d{1,}%$/.test(t)) {
				$(this).val('');
			}
		});*/
		$(document).on('click','.delete_scoremodel', function () {
			$(this).closest('tr').remove();
		});
		// 
		$(document).on('click', '.arrow_down', function () {
			var $t = $(this).closest('tr');
			if ($t.next().size() > 0) {
				$t.next().after($t);
			}
		});
		//上移移 
		$(document).on('click', '.arrow_up', function () {
			var $t = $(this).closest('tr');
			if($t.prev('tr:not(.head_title)').size() > 0) {
				$t.prev().before($t);
			}
		});
	},

	set_is_effect: function () {
		$('input[name=is_effect]').change(function () {
			var checked = $(this).attr('checked');
			if(checked) {
				var total = 0;
				$('.scorepaper_itme_weight').each(function () {
					var t = $(this).val().replace(/%/, '');
					total += parseInt(t);
				});
				// 判断 所有权重加起来为100%
				if (total != 100) {
					var  params = {text : app.vtranslate(),title : app.vtranslate('权重之和必须是100%')};
        			Vtiger_Helper_Js.showPnotify(params);
        			$(this).removeAttr('checked');
				}
				
				
			}
		});
	},


	/**
		pop two calander.
	*/
	registerBasicEvents:function(container){
		this._super(container);
		this.registerRecordPreSaveEvent(container);
		this.add_test();
		this.add_scoremodel();
		this.set_is_effect();
	}
});




















