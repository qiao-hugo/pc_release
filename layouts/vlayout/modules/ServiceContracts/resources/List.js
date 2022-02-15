


Vtiger_List_Js("ServiceContracts_List_Js",{
	
},{
	editContractsClose: function() {
		var listViewContentDiv = this.getListViewContentContainer();
		var type = 'PROTECTED';
		var listInstance = Vtiger_List_Js.getInstance();
		var message = app.vtranslate('LBL_' + type + '_CONFIRMATION');

		
		listViewContentDiv.on('click', '.updateContractsCloseButton', function(e){ 
			var msg = {
                'message': '更改合同自动关闭状态',
                "width":"400px",
            };
            var elem = jQuery(e.currentTarget);
            var $select_tr = elem.closest('tr');
			var recordId = elem.closest('tr').data('id');
			var me = this;

			Vtiger_Helper_Js.showConfirmationBox(msg).then(
				function(e) {
					var selectValue = document.getElementById('contractsClose').checked;
					console.log(selectValue);
					if (selectValue) {
						selectValue = '1';
					} else {
						selectValue = '0';
					}
					var module = app.getModuleName();
					var postData = {
						"module": module,
						"action": "BasicAjax",
						"record": recordId,
						'status': selectValue,
						'mode': 'setContractsClose'
						//"parent": app.getParentModuleName()
					}
					
					var Message = app.vtranslate('JS_RECORD_GETTING_'+type);
					
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
							if (data.success) {
								var t = {'1': '是', '0': '否'};
								$select_tr.find('.isautoclose_value').html(t[selectValue]);

								$(me).attr('data-status', t[selectValue]);
								//alert('更新合同自动关闭状态成功');
								var  params = {text : '更新合同自动关闭状态成功', title : '提示'};
								Vtiger_Helper_Js.showMessage(params);
							} else {
								var  params = {text : '更新合同自动关闭状态失败', title : '提示'};
								Vtiger_Helper_Js.showMessage(params);
							}
						},
						function(error,err){

						}
					);
				},function(error, err){}
			);

			var status = $(this).attr('data-status');
			var ss = '';
			if (status == '是') {
				ss = 'checked';
			}
			$('.modal-body').append('<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0;"><tbody><tr><td class="fieldLabel medium"><label class="muted">合同自动关闭:</label></td><td class="fieldValue medium " colspan="3"><div class="row-fluid  pull-left"><span class="span10"><input type="checkbox" id="contractsClose" name="vehicle" ' +ss+ ' /> </span></div></td></tr></tbody></table>');
		});
	},
	editContractsStates: function(){
		var listViewContentDiv = this.getListViewContentContainer();
		var type = 'PROTECTED';
		var listInstance = Vtiger_List_Js.getInstance();
		var message = app.vtranslate('LBL_' + type + '_CONFIRMATION');

		
		listViewContentDiv.on('click', '.updateContractsStatesButton', function(e){ 
			var msg = {
                'message': '更改合同关闭状态',
                "width":"400px",
            };
            var elem = jQuery(e.currentTarget);
            var $select_tr = elem.closest('tr');
			var recordId = elem.closest('tr').data('id');
			var me = this;

			Vtiger_Helper_Js.showConfirmationBox(msg).then(
				function(e) {
					var selectValue = $('#contractsStates').val();
					var module = app.getModuleName();
					var postData = {
						"module": module,
						"action": "BasicAjax",
						"record": recordId,
						'status': selectValue,
						'mode': 'setContractsStates'
						//"parent": app.getParentModuleName()
					}
					
					var Message = app.vtranslate('JS_RECORD_GETTING_'+type);
					
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
							if (data.success) {
								var t = {'1': '是', '0': '否'};
								$select_tr.find('.contractstate_value').html(t[selectValue]);

								$(me).attr('data-status', t[selectValue]);
								//alert('更新合同关闭状态成功');
								//location.reload();
								var  params = {text : '更新合同关闭状态成功', title : '提示'};
								Vtiger_Helper_Js.showMessage(params);
							} else {
								var  params = {text : '更新合同关闭状态失败', title : '提示'};
								Vtiger_Helper_Js.showMessage(params);
							}
						},
						function(error,err){

						}
					);
				},function(error, err){}
			);

			var str = '';
			var temp_str = {
				'0': '正常',
				'1': '关闭'
			};
			var status = $(this).attr('data-status');
			var ttt = {'是': '1', '否':'0'};
			status = ttt[status];

			for(var index in temp_str) {
				if (index == status) {
					str += '<option selected="selected" value="'+ index +'">'+ temp_str[index] +'</option>';
				} else {
					str += '<option value="'+ index +'">'+ temp_str[index] +'</option>';
				}
			}
			$('.modal-body').append('<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0;"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">合同关闭状态:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><select id="contractsStates">'+str+'</span></div></td></tr></tbody></table>');
		});
	},
	collate : function() { //核对
		$('body').on("click", '.collate', function() { //单个核对
			var contractid = $(this).parents('tr').data('id');
			var dialog = bootbox.dialog({
				title: '服务合同核对',
				width:'600px',
				message: '<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0"><tbody>'+
			'<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>是否符合:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><select id="checkresult"><option value="fit">是</option><option value="unfit">否</option></select></span></div></td></tr>'+
			'<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor" style="display: none;" id="remarkstar">*</span>备注:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><textarea id="remark" style="overflow:hidden;overflow-wrap:break-word;resize:none; height:100px;width:320px;"></textarea></span></div></td></tr>'+
			'</tbody></table>',
				buttons: {
					ok: {
						label: "确定",
						className: 'btn-success',
						callback: function() {
							var checkresult = $('#checkresult').val();
							var remark = $('#remark').val();
							if (checkresult == 'unfit' && remark=='') {
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
								"module": 'ServiceContracts',
								"action": 'ChangeAjax',
								'contractid': contractid,
								"checkresult": checkresult,
								'remark': remark,
								'mode': 'collateContract'
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
									if(data.success) {
										if (data.result.status == 'success') {
											var params = {type: 'success', text: '成功核对'};
											Vtiger_Helper_Js.showMessage(params);
											$('#PostQuery').trigger('click');
										} else {
											var params = {type: 'error', text: data.result.msg};
											Vtiger_Helper_Js.showMessage(params);
										}
									} else {
										var params = {type: 'error', text: data.error.message};
										Vtiger_Helper_Js.showMessage(params);
									}
								},
								function(error,err) {

								}
							);
						}
					},
					cancel: {
						label: "取消",
						className: 'btn',
						callback: function(){

						}
					}
				}
			});
		}).on('click', '#collateContract', function() { //批量核对数据
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
				"module":"ServiceContracts",
				"action":"JsonAjax",
				"mode":"getListViewCount",
				"BugFreeQuery":form
			};
			var progressIndicatorElement = jQuery.progressIndicator({
				'message' : '请求中...',
				'position' : 'html',
				'blockInfo' : {'enabled' : true}
			});
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

						var dialog = bootbox.dialog({
							title: '服务合同核对（共' + total +'条数据）',
							width: '600px',
							message: '<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0"><tbody>'+
								'<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor">*</span>是否符合:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><select id="checkresult"><option value="fit">是</option><option value="unfit">否</option></select></span></div></td></tr>'+
								'<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px"><span class="redColor" style="display: none;" id="remarkstar">*</span>备注:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><textarea id="remark" style="overflow:hidden;overflow-wrap:break-word;resize:none; height:100px;width:320px;"></textarea></span></div></td></tr>'+
								'</tbody></table>',
							buttons: {
								ok: {
									label: "确定",
									className: 'btn-success',
									callback: function() {
										var res = confirm('确定要一键核对'+total+'条数据吗？');
										if (res == false) {
											return false;
										}
										var checkresult = $('#checkresult').val();
										var remark = $('#remark').val();
										if (checkresult == 'unfit' && remark=='') {
											var params = {type: 'error', text: '选择否时，备注必须填写'};
											Vtiger_Helper_Js.showMessage(params);
											return false;
										}
										if (remark.length > 2000) {
											var params = {type: 'error', text: '备注允许最大长度为2000'};
											Vtiger_Helper_Js.showMessage(params);
											return false;
										}
										var postData = {
											'module': 'ServiceContracts',
											'action': 'ChangeAjax',
											'checkresult': checkresult,
											'remark': remark,
											'mode': 'batchCollateContract',
											'BugFreeQuery': form
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
												if (data.success) {
													if (data.result.status == 'success') {
														var params = {type: 'success', text: data.result.msg};
														Vtiger_Helper_Js.showMessage(params);
														$('#PostQuery').trigger('click');
													} else {
														var params = {type: 'error', text: data.result.msg};
														Vtiger_Helper_Js.showMessage(params);
													}
												} else {
													var params = {type: 'error', text: data.error.message};
													Vtiger_Helper_Js.showMessage(params);
												}
											},
											function(error,err) {

											}
										);
									}
								},
								cancel: {
									label: "取消",
									className: 'btn',
									callback: function(){

									}
								}
							}
						});
					} else {
						var  params = {type:'error', text : data.error.message, title : '提示'};
						Vtiger_Helper_Js.showMessage(params);
					}
				}
			);
		}).on('change', '#checkresult', function() {
			if( $(this).val()=='unfit') {
				$('#remarkstar').show();
			} else {
				$('#remarkstar').hide();
			}
		});
	},
	checklog: function() {
		$("body").on('click', '.collatelog', function () {
			var dialog = bootbox.dialog({
				title: '核对记录',
				width:'500px',
				message: '<p style="text-align: center;font-size:15px;color:#666"> 数据加载中...</p>'
			});
			var tr = $(this).parents('tr');
			var contractid = tr.data('id');
			var postData = {
				'module': 'ServiceContracts',
				'action': 'ChangeAjax',
				'mode': 'collateLog',
				'contractid': contractid
			}
			AppConnector.request(postData).then(
				function(data) {
					if (data.success) {
						var htmlstr = '<ul class="collateloglist">';
						for (const i in data.result) {
							var item = data.result[i];
							var serialnum =parseInt(i)+1;
							htmlstr += '<li><span class="serialnum">' + serialnum + '</span><div><span class="collatetime">'+ item['collate_time'] +'</span><span class="collator" title = "' + item['collator'] + '">'+ item['collator'] +'</span><span class="status">'+ item['status'] +'</span></div><div>'+ item['remark'] +'</div></li>';
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
	exportContract:function() {
		$("body").on('click','#exportContract',function () {
			var public = $('#public').val();
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

			if(public == 'NoComplete'){
				var urlParams = {
					"module":"ServiceContracts",
					"action":"ChangeAjax",
					"mode":"exportData",
					"public":"NoComplete",
					"BugFreeQuery":form
				};
			}else{
				var urlParams = {
					"module":"ServiceContracts",
					"action":"ChangeAjax",
					"mode":"exportData",
					"BugFreeQuery":form
				};
			}
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
					if (data.success) {
						window.location.href='index.php?module=ServiceContracts&action=ChangeAjax&mode=exportFile';
					} else {
						var params = {text : data.error.message, title : '提示',type:'error'};
						Vtiger_Helper_Js.showMessage(params);
					}
				}
			);
		})
	},
	/**
	 * 合同关停
	 */
	closeContracts: function(){//关停
		var listViewContentDiv = this.getListViewContentContainer();
		listViewContentDiv.on('click', '.closedContracts', function(e){
			var thisInstance=$(this);
			var msg = {
				'message': '确定要关停合同<span style="color:red;">'+thisInstance.data('msg')+'</span>，关停后合同不可重新启用！',
				"width":"800px",
			};
			var elem = jQuery(e.currentTarget);
			var recordId = elem.closest('tr').data('id');
			Vtiger_Helper_Js.showConfirmationBox(msg).then(
				function(e) {
					var selectValue = $('#contractsStates').val();
					var module = app.getModuleName();
					var postData = {
						"module": module,
						"action": "BasicAjax",
						"record": recordId,
						'mode': 'closedContracts'
					}
					var progressIndicatorElement = jQuery.progressIndicator({
						'message' : '数据处理中，请稍后！',
						'position' : 'html',
						'blockInfo' : {'enabled' : true}
					});
					AppConnector.request(postData).then(
						function(data){
							progressIndicatorElement.progressIndicator({
								'mode' : 'hide'
							});
							if (data.success) {
								var  params = {text : data.result.msg, title : '提示'};
								Vtiger_Helper_Js.showMessage(params);
								if(data.result.flag){
									thisInstance.closest('tr').find('.label-c_complete').text('关停');
									thisInstance.remove();
								}
							} else {
								var  params = {text : '更新合同关闭状态失败', title : '提示'};
								Vtiger_Helper_Js.showMessage(params);
							}
						},
						function(error,err){
						}
					);
				},function(error, err){}
			);
		});
	},
	doUploadFile:function(){
	    var thisinstance=this;
	    $("body").on("click",'.clickupload',function (event) {
	        window.resultfile='';
	        $(".ke-upload-file").trigger("click")
	        $("#drop_area").remove("secondarea");
	        $("#drop_area").addClass("secondarea");
	    })

	    $("body").on('click','.secondarea',function (event) {
	        $(".ke-upload-file").trigger("click")
	    })

	},
	uploadAbilityFile:function(){
	    var thisinstance=this;
	    $("body").on("click",'#closeContract',function (event) {
	        var message='<h3>关停合同上传</h3>';
	        var msg={
	            'message':message,
	            "width":530
	        };
	        var recordid = $("input[name='recordid']").val();
	        var field = $(this).data("field");
	        var fileid = $(this).data("fileid");
	        var loadurl = '';
	        var fieldstr = '';
	        //var titlestr = '请选择需要上传的文件<span style="color: red">(重复上传将覆盖原有数据)</span>';


			loadurl = "<a style='color: dodgerblue' href='./tpl/closecon.xlsx'>点击下载</a>模板";
			fieldstr =           "<div style='height: 100px;margin: 20px;'>" +
				"<h4>一.请按照模板格式填写要上传的文件."+loadurl+"</h4>"+
				"<div style='margin-left: 20px;font-size:16px;color: grey;'>注意事项:\n<br>" +
				"\n" +
				"1.模板中表头名称不可更改，表头行不可删除\n<br>" +
				"\n" +
				"2.上传文件请勿超过4MB</div>"+
				"</div>";
			titlestr = '二.请选择需要上传的文件<span style="color: red">(重复上传将覆盖原有数据)</span>';


	        var str = "<div>" +fieldstr+
	            "<div style='height: 200px;margin: 20px;'>" +
	            "<input type='hidden' id='fieldselected' value='"+field+"'>"+
	            "<input type='hidden' id='fileidselected' value='"+fileid+"'>"+
	            "<h4>"+titlestr+"</h4>"+
	            "<div id='drop_area' style='margin-left: 20px;font-size:16px;color: grey;width: 400px;height: 135px;background-color: aliceblue;line-height: 135px;text-align: center;border: 1px dashed gray;'>" +
	            "将文件拖到此处"+
	            "</div>"+
	            '<div class="upload" style="display: none">' +
	                '<div style="display:inline-block;width:70px;height:30px;overflow: hidden;vertical-align: middle;" title="文件名请勿包含空格">' +
	                '<div style="margin-top:-2px;">文件名请勿</div><div style="margin-top:-5px;">包含空格</div></div>'+
	                '<input type="button" id="uploadButton" value="上传" title="文件名请勿包含空格" style="display: none;">' +
	             '</div>'+
	            '<form id="Form" style="display: none"  method="post" enctype="multipart/form-data"><input type="file" class="ke-upload-file" name="File" ></form>'
	        ;
			Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
	            resultfile = window.resultfile;
	            if(resultfile){
	                var data = resultfile;
	            }else{
	                var data = $("input[name='File']")[0].files[0];
	            }
	            var reader = new FileReader(); //实例化文件读取对象
	            reader.readAsDataURL(data); //
	            reader.onload = function(ev) { //文件读取成功完成时触发
	                var dataURL = ev.target.result; //获得文件读取成功后的DataURL,也就是base64编码
	                var params = {
	                    'module': 'ServiceContracts',
	                    'action': 'BasicAjax',
	                    'mode':"fileupload",
	                    'processData': false,
	                    'contentType': false,
	                    'dataType':"json",
	                    'type':'POST',
	                    'file':dataURL,
	                    'name':data.name,
	                    'size':data.size,
	                    'filedatatype':data.type,
	                    'record':recordid,
	                    'field':field,
	                };
	                var Message = app.vtranslate('正在上传中...');
	                var progressIndicatorElement = jQuery.progressIndicator({
	                    'message' : Message,
	                    'position' : 'html',
	                    'blockInfo' : {'enabled' : true}
	                });
	                AppConnector.request(params).then(
	                    function(data){
	                        progressIndicatorElement.progressIndicator({'mode' : 'hide'});
							var  params = {text : data.msg, title : '提示',type :'error',delay:20000};
							Vtiger_Helper_Js.showMessage(params);
	                        /*if(data.success=true){
	                            window.location.href='/'+data.filepath;
	                        }else{
								var  params = {text : data.msg, title : '提示'};
								Vtiger_Helper_Js.showMessage(params);
	                        }*/
	                    }
	                );
	            }
	        },function(error, err) {});

	        $('.modal-content .modal-body').append(str);
	        $('.modal-content .modal-body').css({overflow:'hidden'});
			$(document).on({
				dragleave:function(e){
					e.preventDefault();
				},
				drop:function(e){
					e.preventDefault();
				},
				dragenter:function(e){
					e.preventDefault();
				},
				dragover:function(e){
					e.preventDefault();
				}
			})
			thisinstance.dropAndTag();
	    });
	},
	dropAndTag:function() {
		var box = document.getElementById('drop_area'); //拖拽区域
		box.addEventListener("drop",function(e){
			var fileList = e.dataTransfer.files; //获取文件对象
			//检测是否是拖拽文件到页面的操作
			if(fileList.length == 0){
				return false;
			}
			if(fileList[0].size>4*1024*1024){
				alert("文件不能超过4M");
				return;
			}
			var extname=fileList[0].name.split('.');
			var ext=extname.pop();
			 if(ext!='xlsx'){
			     alert("导入格式必须为xlsx文件");
			     return;
			}
			//拖拉图片到浏览器，可以实现预览功能
			//规定视频格式
			Array.prototype.S=String.fromCharCode(2);
			Array.prototype.in_array=function(e){
				var r=new RegExp(this.S+e+this.S);
				return (r.test(this.S+this.join(this.S)+this.S));
			};
			var video_type=["video/mp4","video/ogg"];

			//创建一个url连接,供src属性引用
			var fileurl = window.URL.createObjectURL(fileList[0]);
			if(fileList[0].type.indexOf('image') === 0){  //如果是图片
				var str="<img style='max-width: 400px;height: 130px;'  src='"+fileurl+"'>";
				document.getElementById('drop_area').innerHTML=str;
			}else if(video_type.in_array(fileList[0].type)){   //如果是规定格式内的视频
				var str="<video width='350px' height='130px' controls='controls' src='"+fileurl+"'></video>";
				document.getElementById('drop_area').innerHTML=str;
			}else{ //其他格式，输出文件名
				filename =fileList[0].name.length>15?fileList[0].name.substring(0,15)+'...':fileList[0].name;
				var str='<div style="height: 20px;line-height: 20px;width:300px;margin: 0 auto;margin-top: 50px;">'+filename+'</div>';
				document.getElementById('drop_area').innerHTML=str
			}
			resultfile = fileList[0];

			window.resultfile = resultfile;
			$("#drop_area").remove("secondarea");
			$("#drop_area").addClass("secondarea");
		},false);
	},
	registerEvents : function(){
		this._super();
		this.editContractsStates();
		this.editContractsClose();
		this.collate();//核对
		this.checklog();//核对记录
		this.exportContract();//导出合同
		this.closeContracts();
		this.uploadAbilityFile();
		var public = $('#public').val();
		if(public == 'NoComplete'){
			$('.btn-group').hide();
			$('.diy').hide();
			$('#collateContract').hide();
			$('#closeContract').hide();
		}
	}
});