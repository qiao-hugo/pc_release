Vtiger_Edit_Js("Vendors_Edit_Js",{ },{
    //Stored history of account name and duplicate check result
	duplicateCheckCache : {},
	//This will store the editview form
	editViewForm : false,
	//Address field mapping within module
	addressFieldsMappingInModule : {'bill_street':'ship_street','bill_pobox':'ship_pobox','bill_city':'ship_city','bill_state':'ship_state','bill_code'	:'ship_code','bill_country':'ship_country'},							
	/**
	 * This function will return the current form
	 */

	//区域选择空间 第三方集成 By Joe
	registerArea:function(){
		if(jQuery('#areadata').length>0){
			var area=jQuery('#areadata').attr('data');
			if(typeof area!='undefined'&& area.length>1){
				area=area.split('#');
				new PCAS("province","city","area",area[0],area[1],area[2]);
				jQuery('input[name=address]').val(area[3]);
			}else{
				new PCAS("province","city","area");
			}	
		}
	},
	select_mainplatform : function () {
	},

	add_vendorsrebate: function () {
		$('#add_vendorsrebate').click(function () {
			var numd=$('.Duplicates').length+1;
            if(numd>100){return;}/*超过100个不允许添加*/
            var nowdnum=$('.Duplicates').last().data('num');
            if(nowdnum!=undefined){
                numd=nowdnum+1;
            }
            var t_vendorsrebate_html=vendorsrebate_html.replace(/\[\]|replaceyes/g,'['+numd+']');
            t_vendorsrebate_html=t_vendorsrebate_html.replace(/yesreplace/g,numd);
            t_vendorsrebate_html=t_vendorsrebate_html.replace(/reg_select_html/g, product_html);
            // 产品json数据
            //reg_select_html
            $('#vendorsrebate').append(t_vendorsrebate_html);
            $('.t_date').datetimepicker({
                format: "yyyy-mm-dd",
                language:  'zh-CN',
                autoclose: true,
                autoclose:1,
                todayHighlight:1,
                startView:2,
                minView:2,
                forceParse:0,
                pickerPosition: "bottom-left",
                showMeridian: 0
            });
            $('.chzn-select').chosen();
		});
	},
	product_select: function () {
		$(document).on('change', '.product_select', function () {
			$(this).closest('td').find('.t_productname').val($(this).find("option:selected").text());
		});
	},
	delbutton: function () {
		$(document).on('click', '.delbutton', function () {
			$(this).closest('.Duplicates').remove();
		});
	},

	//等级字段无需页面编辑 废弃 By Joe 
	/*checkAccountRank:function(form){	accountrank=$('select[name="accountrank"]').val();	if($('input[name="record"]').val()>1 && accountrank.indexOf('_isv')>0 ){	$('select[name="accountrank"] option').each(function () {	var that=$(this);	if(that.val().indexOf('_isv')<0){	that.attr("disabled", "disabled");	$('.chzn-results>li').each(function () {	if($(this).text()==that.text()){	$(this).remove();	}	});	} 	});	} }, */
	/**
	 * Function which will register basic events which will be used in quick create as well
	 */
	init: function () {
		$('select[name=vendortype]').change(function () {
			var vendortype = $(this).val();
			if (vendortype == 'medium') {
				$('#mainplatform_select_chzn').show();
				$('#mainplatform_display').attr('disabled', 'disabled').hide();

				$('#mainplatform_select').removeAttr('disabled');
			} else {
				$('#mainplatform_select').attr('disabled', 'disabled');
				$('#mainplatform_select_chzn').hide();
				$('#mainplatform_display').removeAttr('disabled').show();
				
			}
		});

		var t = $('#mainplatform_display').attr('disabled');
		if (t == undefined) {
			$('#mainplatform_select_chzn').hide();
		}

		$(document).on('blur', '.product_rebate', function () {
			var t = $(this).val();
			t = parseFloat(t);
			if(!isNaN(t)) {
				t = t.toFixed(2);
			} else {
				t = '';
			}	
			$(this).val(t);	
		});
		//$('input[name="mainplatform"]').after('<button id="select_mainplatform" type="button" class="btn btn-info">选择产品</button>');
		
		var record = $('input[name=record').val();
		if(!record) {
			$('select[name=vendorstate]').next().find('.chzn-results').remove();
		}

		$('input[name=phone]').blur(function () {
			var t = $(this).val();
			if(t) {
				if(! /^1\d{10}$/.test(t)) {
					alert('手机号码格式不正确,请重新输入');
					$(this).val('');
				}
			}
		});
		$('input[name=linkphone]').blur(function () {
			var t = $(this).val();
			if(t) {
				if(! /^[\d-]{6,}$/.test(t)) {
					alert('公司电话格式不正确,请重新输入');
					$(this).val('');
				}
			}
		});
	},
	registerBasicEvents : function(container) {
		this._super(container);
		this.registerArea();
		this.select_mainplatform();
		this.add_vendorsrebate();
		this.product_select();
		this.delbutton();
	}
});