


Vtiger_List_Js("AchievementSummary_List_Js",{
	
},{
	checkUsers:function(){
		$("body").on('click','.all_user',function () {
			var is_check = $(this).is(':checked');
			$(".separte_user").each(function (k,v) {
				if(is_check){
					$(v).attr('checked',true);
				}else{
					$(v).attr('checked',false);
				}
			})
		});

	},
	getDefaultParams : function() {
		var pageNumber = jQuery('#pageNumber').val();
		var module = app.getModuleName();
		var parent = app.getParentModuleName();
		var cvId = this.getCurrentCvId();
		var orderBy = jQuery('#orderBy').val();
		var sortOrder = jQuery("#sortOrder").val();
		var pub = $('#public').val();
		var filter=$('#filter').val();
		var DepartFilter=$('#DepartFilter').val();
		var params = {
			'__vtrftk':$('input[name="__vtrftk"]').val(),
			'module': module,
			'parent' : parent,
			'page' : pageNumber,
			'view' : "List",
			'viewname' : cvId,
			'orderby' : orderBy,
			'sortorder' : sortOrder,
			'public' : pub,
			'filter' :filter,
			'department':DepartFilter,
			'accountsname': $("input[name ='accountsname']").val(),
			'smown':$('select[name="smowen"]').val()
		}

        var searchValue = this.getAlphabetSearchValue();

        if((typeof searchValue != "undefined") && (searchValue.length > 0)) {
            params['search_key'] = this.getAlphabetSearchField();
            params['search_value'] = searchValue;
            params['operator'] = "s";
        }
		return params;
	},
	submitEnd:function(){
		$("body").on('click','#confirm_end',function () {
			var flag = false;
			var flag2 = false;
			var flag3 = false;
			var achievementids = [];
			$('.separte_user').each(function (e) {
				if($(this).attr('checked')){
					achievementids.push($(this).data('id'));
					flag = true;
					if($(this).data('confirmstatus')=='confirmed'){
						flag2 = true;
					}
					if($(this).data('modulestatus')=='b_actioning'){
                        flag3 = true;
					}
				}
			});
            if(flag3){
                var params3 = {
                    text: '<h4>暂不支持确认完结，存在审批流程</h4>',
                    type: 'notice'
                };
                Vtiger_Helper_Js.showMessage(params3);
                return;
			}
			if(flag2){
				var params2 = {
					text: '<h4>选中的人员中存在确认完结状态的用户</h4>',
					type: 'notice'
				};
				Vtiger_Helper_Js.showMessage(params2);
				return;
			}
			if(!flag){
				var params = {
					text: '<h4>请至少选中一个用户</h4>',
					type: 'notice'
				};
				Vtiger_Helper_Js.showMessage(params);
				return;
			}
            var msg = {
                'message': '确定"确认完结"吗?',
                "width":"300px",
            };
            Vtiger_Helper_Js.showConfirmationBox(msg).then(
                function(e) {
                    var params_r = [];
                    params_r['action'] = 'ChangeAjax';
                    params_r['module'] = 'AchievementSummary';
                    params_r['mode'] = 'confirmEnd';
                    params_r['achievementids'] = achievementids;
                    AppConnector.request(params_r).then(
                        function(data) {
                            if(data.success){
                                location.reload();
                                return;
                            }
                            var params = {
                                text: data.result,
                                type: 'notice'
                            };
                            Vtiger_Helper_Js.showMessage(params);
                        });
            });
		});
	},
	submitCancelConfirm:function(){
		$("body").on('click','#cancel_confirm_end',function () {
			var flag = false;
			var flag2 = false;
			var achievementids = [];
			$('.separte_user').each(function (e) {
				if($(this).attr('checked')){
					achievementids.push($(this).data('id'));
					flag = true;
					if($(this).data('confirmstatus')=='tobeconfirm'){
						flag2 = true;
					}
				}
			});

			if(flag2){
				var params2 = {
					text: '<h4>选中的人员中存在待确认完结状态的用户</h4>',
					type: 'notice'
				};
				Vtiger_Helper_Js.showMessage(params2);
				return;
			}

			if(!flag){
				var params = {
					text: '<h4>请至少选中一个用户</h4>',
					type: 'notice'
				};
				Vtiger_Helper_Js.showMessage(params);
				return;
			}
			var msg = {
				'message': '确定"撤销确认完结"吗?',
				"width":"300px",
			};
			Vtiger_Helper_Js.showConfirmationBox(msg).then(
				function(e) {
                    var params_r = [];
                    params_r['action'] = 'ChangeAjax';
                    params_r['module'] = 'AchievementSummary';
                    params_r['mode'] = 'cancelConfirmEnd';
                    params_r['achievementids'] = achievementids;
                    AppConnector.request(params_r).then(
                        function(data) {
                            console.log(data);
                            if(data.success){
                                location.reload();
                                return;
                            }
                            var params = {
                                text: data.result,
                                type: 'notice'
                            };
                            Vtiger_Helper_Js.showMessage(params);
                        });
			});
		});
	},
	exportCsv:function(){
		$("body").on('click','#export',function () {
			// var achievementids = [];
			// $('.separte_user').each(function (e) {
			// 	if($(this).attr('checked')){
			// 		achievementids.push($(this).data('id'));
			// 	}
			// });
			// //导出勾选的
			// if (achievementids.length>0) {
			// 	location.href="index.php?module=AchievementSummary&view=List&public=exportCsv&achievementids="+achievementids;
			//
			// 	return;
			// }
			//未勾选 则进入第二个选择页面
			window.open("index.php?module=AchievementSummary&view=List&public=selectPage");
		});
	},
    applicationUpdateAchievement:function () {
        var listViewContentDiv = this.getListViewContentContainer();
        listViewContentDiv.on('click', '.applicationUpdateAchievement', function(e){

        	if($(this).data("isover")=='confirmed'){
                Vtiger_Helper_Js.showMessage({type: 'error', text: '已完结不能调整业绩！'});
               return false;
			}
            var tr = $(this).closest('tr');
            var record=$(tr).data("id");
            var date=$("#date").val();
            var msg = {
                'message': '申请调整业绩',
                "width":"400px",
            };
            Vtiger_Helper_Js.showConfirmationBox(msg).then(
                function(e) {
                    if(!$("#adjustachievement").val()){
                        Vtiger_Helper_Js.showMessage({type: 'error', text: '调整金额不能为空!'});
                        return false;
					}
                    if(!$("#remarks").val()){
                        Vtiger_Helper_Js.showMessage({type: 'error', text: '备注不能为空!'});
                        return false;
                    }
                	var params = {
                        'module': 'AchievementSummary',
                        'action': 'ChangeAjax',
                        'mode': 'applicationUpdateAchievement',
                        'adjustachievement':$("#adjustachievement").val(),
                        'remarks':$("#remarks").val(),
                        'record':record
                    };
                    AppConnector.request(params).then(
                        function (data) {
                        	if(data.result.success==1){
                                window.location.reload();
							}else{
                                Vtiger_Helper_Js.showMessage({type: 'error', text:data.result.message});
							}

                        }
                    )
                }
            );
            $('.modal-body').append('<table style="margin-top: 25px;"><tr>\n' +
                '\t\t\t\t<td class="fieldLabel medium">\n' +
                '\t\t\t\t\t<label class="muted pull-right marginRight10px">业绩金额</label>\n' +
                '\t\t\t\t</td>\n' +
                '\t\t\t\t<td class="fieldValue medium">\n' +
                '\t\t\t\t\t<div class="input-append row-fluid">\n' +
                '\t\t\t\t\t\t<div class="span10 row-fluid date form_datetime">\n' +
                '\t\t\t\t\t\t\t\t\t<input id="adjustachievement" type="text" class="input-large"  onkeyup="num(this)" />\n'+
                '\t\t\t\t\t\t</div>\n' +
                '\t\t\t\t\t</div>\n' +
                '\t\t\t\t</td>\n' +
                '\t\t\t</tr>\n'+
                '<tr>\n' +
                '\t\t\t\t<td class="fieldLabel medium">\n' +
                '\t\t\t\t\t<label class="muted pull-right marginRight10px">备注</label>\n' +
                '\t\t\t\t</td>\n' +
                '\t\t\t\t<td class="fieldValue medium">\n' +
                '\t\t\t\t\t<textarea  id="remarks" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{&quot;mandatory&quot;:false,&quot;presence&quot;:true,&quot;quickcreate&quot;:false,&quot;masseditable&quot;:true,&quot;defaultvalue&quot;:false,&quot;type&quot;:&quot;text&quot;,&quot;name&quot;:&quot;remark&quot;,&quot;label&quot;:&quot;\u5907\u6ce8&amp;\u8bf4\u660e&quot;}"></textarea>\n' +
                '\t\t\t\t</td>\n' +
                '\t\t\t</tr></table><span style="color: red;">注：本月到账业绩相关修改审核截止日期为下个月的'+date+'号。没有完成审核不算入当月到账业绩，请及时联系相关人员进行确认。</span>');
		});


/*    业绩日期调整 html
      '\t\t\t\t\t\t\t<select class="chzn-select"  ><option value="1" >1号</option><option value="2" >2号</option><option value="3" >3号</option><option value="4" >4号</option><option value="5" >5号</option><option value="6" >6号</option><option value="7" >7号</option><option value="8" >8号</option><option value="9" >9号</option><option value="10" >10号</option><option value="11" >11号</option><option value="12" >12号</option><option value="13" >13号</option><option value="14" >14号</option><option value="15" >15号</option><option value="16" >16号</option><option value="17" >17号</option><option value="18" >18号</option><option value="19" >19号</option><option value="20" >20号</option><option value="21" >21号</option><option value="22" >22号</option><option value="23" >23号</option><option value="24" >24号</option><option value="25" >25号</option><option value="26" >26号</option><option value="27" >27号</option><option value="28" >28号</option></select>\n' +
*/
    },
	exportFinanceCsv:function(){
		$("body").on('click','#exportFinance',function(){
			var msg = {
				'message': '确定要导出数据吗？',
			};
			Vtiger_Helper_Js.showConfirmationBox(msg).then(
				function(e) {
					var progressIndicatorElement = jQuery.progressIndicator({
						'message': "努力处理中请稍等...",
						'position': 'html',
						'blockInfo': {
							'enabled': true
						}
					});
					var searchParamsPreFix = 'BugFreeQuery';
					var rowOrder = "";
					var $searchRows = $("tr[id^=SearchConditionRow]");
					$searchRows.each(function () {
						rowOrder += $(this).attr("id") + ",";
					});

					eval("$('#" + searchParamsPreFix + "_QueryRowOrder')").attr("value", rowOrder);
					var limit = $('#limit').val();
					var o = {};
					var a = $('#SearchBug').serializeArray();
					$.each(a, function () {
						if (o[this.name] !== undefined) {
							if (!o[this.name].push) {
								o[this.name] = [o[this.name]];
							}
							o[this.name].push(this.value || '');
						} else {
							o[this.name] = this.value || '';
						}
					});
					var form = JSON.stringify(o);
					var departfilter = $('#DepartFilter').val();
					var urlParams = {
						"module": "AchievementSummary",
						"action": "ChangeAjax",
						"mode": "exportFinanceCsv",
						"page": 1,
						"BugFreeQuery": form,
						"limit": limit,
						"department": departfilter
					};
					var url = location.search; //获取url中"?"符后的字串
					if (url.indexOf("?") != -1) {
						var str = url.substr(1);
						var strs = str.split("&");
						for (var i = 0; i < strs.length; i++) {
							if (strs[i].split("=")[0] == 'rechargesource') {
								urlParams[strs[i].split("=")[0]] = unescape(strs[i].split("=")[1]);
								break;
							}
						}
					}
					AppConnector.request(urlParams).then(
						function (data) {
							progressIndicatorElement.progressIndicator({
								'mode': 'hide'
							})
							if (data.success) {
								window.location.href = 'index.php?module=AchievementSummary&view=List&public=exportFinanceCsv';
							}
						}
					);
				})
		});
	},
	/**
	 * 减法运算相除JS问题
	 * @param arg1除数
	 * @param arg2被除数
	 * @returns {number}
	 */

	accSub:function (arg1,arg2){
		var r1,r2,m,n;
		try{r1=arg1.toString().split(".")[1].length}catch(e){r1=0}
		try{r2=arg2.toString().split(".")[1].length}catch(e){r2=0}
		m=Math.pow(10,Math.max(r1,r2));
		//动态控制精度长度
		n=(r1=r2)?r1:r2;
		return ((arg1*m-arg2*m)/m).toFixed(n);
	},

	/**
	 * 加法相加的问题
	 * @param arg1
	 * @param arg2
	 * @returns {number}
	 */
	accAdd:function(arg1,arg2){
		var r1,r2,m;
		try{r1=arg1.toString().split(".")[1].length}catch(e){r1=0}
		try{r2=arg2.toString().split(".")[1].length}catch(e){r2=0}
		m=Math.pow(10,Math.max(r1,r2))
		var s=(arg1*m+arg2*m)/m;
		if(isNaN(s)){
			s=0;
		}
		return s;
	},
	//计算合计总额
	totalMoney:function(){
		$(".listViewPageDiv .span5").append('<span id="totalAmount" style="max-width:200px; margin-left:20px;display: inline-block">合计到账业绩(￥)：0</span><span id="totalOldAmount" style="max-width:200px; margin-left:15px;display: inline-block">合计实际提成：0</span>');
		var thisInstance=this;
		$("#listViewContents").on("change",'input[name="Detailrecord[]"]',function(){
			var totalAmount=0;
			var totalOldAmount=0;
			$('input[name="Detailrecord[]"]:checked').each(function () {
				var amount=$(this).data('amount');
				totalAmount=thisInstance.accAdd(totalAmount,amount);
				totalAmount=totalAmount.toFixed(2);
				var oldAmount=$(this).data('oldamount');
				totalOldAmount=thisInstance.accAdd(totalOldAmount,oldAmount);
				totalOldAmount=totalOldAmount.toFixed(2);
			});
			$("#totalAmount").html("合计到账业绩(￥)："+totalAmount);
			$("#totalOldAmount").html("合计实际提成："+totalOldAmount);
		});
		$("#listViewContents").on("click",'input[name="checkAll"]',function(){
			if($(this).prop('checked')){
				$('#listViewContents input[name="Detailrecord[]').prop('checked',true);
			}else{
				$('#listViewContents input[name="Detailrecord[]').prop('checked',false);
			}
			$('#listViewContents input[name="Detailrecord[]').trigger('change');
		});
		$("#listViewContents").ajaxComplete(function(){
			$('input[name="Detailrecord[]"').trigger('change');
		});
	},
	updateAchievement:function(){
		$("body").on('click','.modfiAchievement',function(){
			var $_this=$(this);
			var datab=$_this.attr('data-valuedata');
			//var datauroyalty=$_this.attr('data-uroyalty');
			//var datauroyaltyremark=$_this.attr('data-uroyaltyremark');
			var datauroyalty=0;
			var datauroyaltyremark='';
			var msg = {
				'message':'<h4>调整提成/'+datab+'</h4><hr>',
				"width":"400px",
				"action":function(){
					var $mvalue=$("#mvalue").val();
					if($mvalue==''){
						Vtiger_Helper_Js.showMessage({type: 'error', text: '调整值必填！'});
						return false
					}
					if($("#mvalue").val()==0){
						Vtiger_Helper_Js.showMessage({type: 'error', text: '调整值不能为0！'});
						return false
					}
					if($mvalue!=$mvalue*1){
						Vtiger_Helper_Js.showMessage({type: 'error', text: '请输入有效的调整值！'});
						return false
					}

					if($("#remarks").val()==''){
						Vtiger_Helper_Js.showMessage({type: 'error', text: '备注必填！'});
						return false
					}
					return true;
				}
			};
			var tr = $(this).closest('tr');
			var record=$(tr).data("id");
			Vtiger_Helper_Js.showConfirmationBox(msg).then(
				function(e) {
					var params = {
						'module': 'AchievementSummary',
						'action': 'ChangeAjax',
						'mode': 'updateAchievement',
						'mfield':$("#mfield").val(),
						'mvalue':$("#mvalue").val(),
						'remarks':$("#remarks").val(),
						'record':record
					};
					AppConnector.request(params).then(
						function (data) {
							if(data.result.success){
								tr.children('.uroyalty').html('<span class="label label-success">'+data.result.data.uroyalty+'</span>');
								tr.children('.uroyaltyremark').html('<span class="label label-success">'+data.result.data.uroyaltyremark+'</span>');
								Vtiger_Helper_Js.showMessage({type: 'success', text: '更新成功'});
							}else{
								Vtiger_Helper_Js.showMessage({type: 'error', text: data.result.msg});
							}

						}
					)
				}
			);
			var str='<table style="margin-top: 25px;"><tr>\n';

				str+='<td class="fieldLabel medium">\n' +
					'     <label class="muted pull-right marginRight10px">调整字段</label>\n' +
					'       </td>\n' +
					'       <td class="fieldValue medium">\n' +
					'          <div class="input-append row-fluid">\n' +
					'               <select name="mfield" id="mfield"><option value="uroyalty">提成</option></select>\n' +
					'</div></td></tr><tr>';
			str+='<td class="fieldLabel medium">\n' +
				'     <label class="muted pull-right marginRight10px">调整值</label>\n' +
				'       </td>\n' +
				'       <td class="fieldValue medium">\n' +
				'          <div class="input-append row-fluid">\n' +
				'               <input  type="text" id="mvalue" name="mvalue"  value="'+datauroyalty+'" placeholder="调增输入正数，调减输入负数" autocomplete="off">\n' +
				'</div></td></tr><tr>';

			str+='\t\t\t\t<td class="fieldLabel medium">\n' +
				'\t\t\t\t\t<label class="muted pull-right marginRight10px">备注</label>\n' +
				'\t\t\t\t</td>\n' +
				'\t\t\t\t<td class="fieldValue medium">\n' +
				'\t\t\t\t\t<textarea  id="remarks" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo="{&quot;mandatory&quot;:false,&quot;presence&quot;:true,&quot;quickcreate&quot;:false,&quot;masseditable&quot;:true,&quot;defaultvalue&quot;:false,&quot;type&quot;:&quot;text&quot;,&quot;name&quot;:&quot;remark&quot;,&quot;label&quot;:&quot;\u5907\u6ce8&amp;\u8bf4\u660e&quot;}">'+datauroyaltyremark+'</textarea>\n' +
				'\t\t\t\t</td>\n' +
				'\t\t\t</tr></table><span style="color: red;"></span>'
			$('.modal-body').append(str);
		})
	},
	exportWithHold:function(){
		var thisInstance=this;
		$(document).on('click', '#exportwithhold', function(e) {
			thisInstance.exportData('导出暂扣明细','withhold');
		});
		$(document).on('click', '#exportgrant', function(e) {
			thisInstance.exportData('导出交付发放','grantdata');
		});
	},
	exportData:function (msgText,actionMethoed) {
			var msg = {
				'message': msgText,
				"width":"500px",
				action:function(){
					if($('#yearMonth').val()==''){
						Vtiger_Helper_Js.showMessage({type: 'error', text: '月份必填！'});
						return false;
					}
					return true;
				}
			};
			Vtiger_Helper_Js.showConfirmationBox(msg).then(
				function(e) {
					var params = {
						'module': 'AchievementSummary',
						'action': 'ChangeAjax',
						'mode': 'exportData',
						'yearmonth':$("#yearMonth").val(),
						'source':actionMethoed
					};
					AppConnector.request(params).then(
						function (data) {
							if(data.success){
								window.location.href='/'+data.result.urlpath;
								//window.location.reload();
							}else{
								Vtiger_Helper_Js.showMessage({type: 'error', text:'数据更新失改！'});
							}

						}
					)
				}
			);
			var str='<td class="fieldLabel medium">\n' +
			'     <label class="muted pull-right marginRight10px">月份</label>\n' +
			'       </td>\n' +
			'       <td class="fieldValue medium">\n' +
			'          <div class="input-append row-fluid">\n' +
			'            <div class="span10 row-fluid date form_datetime">\n' +
			'               <input  type="text" id="yearMonth"  name="budgetlockstart[]"   data-date-format="yyyy-mm-dd" readonly="" value="" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"  autocomplete="off">\n' +
			'               <span class="add-on"><i class="icon-calendar"></i></span>\n' +
			'</div></div></td></tr><tr>'
			$('.modal-body').append('<table style="margin-top: 25px;">' +
				str+
				'</table>');
		$("#yearMonth").datetimepicker({
			format: 'yyyy-mm',
			weekStart: 1,
			autoclose: true,
			startView: 3,
			minView: 3,
			forceParse: false,
			language: 'zh-CN'
		})
	},
	registerEvents : function(){
		this._super();
		this.checkUsers();
		this.submitEnd();
		this.submitCancelConfirm();
		this.exportCsv();
		this.applicationUpdateAchievement();
		this.exportFinanceCsv();
		this.totalMoney();
		this.updateAchievement();
		this.exportWithHold();
	}


});
function num(obj){
    obj.value = obj.value.replace(/[^\d.]/g,""); //清除"数字"和"."以外的字符
    obj.value = obj.value.replace(/^\./g,""); //验证第一个字符是数字
    obj.value = obj.value.replace(/\.{2,}/g,"."); //只保留第一个, 清除多余的
    obj.value = obj.value.replace(".","$#$").replace(/\./g,"").replace("$#$",".");
    obj.value = obj.value.replace(/^(\-)*(\d+)\.(\d\d).*$/,'$1$2.$3'); //只能输入两个小数
}