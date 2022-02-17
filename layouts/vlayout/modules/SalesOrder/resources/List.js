Vtiger_List_Js('SalesOrder_List_Js',{
	
},{
	//阶段
	registerEventForSalesorderGetid:function(){
		var ids='';
		var params={};
		var module = app.getModuleName();
		var widgetDataContainer=$('.hello');
		$('.listViewEntriesCheckBox').each(function(){
				ids=ids+$(this).val()+',';
		});
		// console.log(ids);
		params['records']=ids;
		params['action']='JsonAjax';
		params['module']=module;
		params['mode']='getSalesOrderWorkflows';
		params['type']='html';

		AppConnector.request(params).then(
			function(data) {
				var jsons= data ;
				var id='';
				$('.listViewEntriesCheckBox').each(function(){
					id=$(this).val();
					if(jsons[id]==undefined){
						$('.a'+id).html('<span class="label">完成</span>');
					}else{
						if(jsons[id].rejectid>0){
							label="<span class='label label-important' title='打回'>打回</span>";
						}else{
							label="<span class='label label-success'>正常</span>";
						}
						$('.a'+id).html(label+'  &nbsp; '+jsons[id].workflowstagesname);
					}
				});
				
//				 widgetDataContainer.html(data);
			
			}
		);
	},
	//部门
	registerEventForDepartment:function(){
		var ids='';
		var params={};
		var module = app.getModuleName();
		$('.isuserid').each(function(){
			ids=ids+$(this).val()+',';
		});
		params['records']=ids;
		params['action']='JsonAjax';
		params['module']=module;
		params['mode']='getDepartments';
		params['type']='json';
		AppConnector.request(params).then(
			function(data){
				if(data&&data.success==true){
					var jsons=data.result;
					$('.isuserid').each(function(){
						var id=$(this).val();
						var instance=$(this).closest('tr').find('.departments');
						for(var json in jsons){
							if(json==id){
								instance.text(jsons[json].departmentname);break;
							}
						}
						if(instance.text()==''){
							instance.text('-');
						}
					});
				}
				
			}
		);
	},
	
	Tableinstance:function(){
		
		/*$('.listViewEntriesTable').dataTable({
			"sDom": '<"top"fli>rt<"bottom"p><"clear">',
			"iDisplayLength": 40,
		});*/
		var table = $('#listViewEntriesTable').DataTable( {
		sDom: '<"top"fli>rt<"bottom"p><"clear">',
		iDisplayLength: 50,
		
		sScrollXInner: "5000px",
			scrollY:        $(window).height()-300,
			scrollX:        true,
			scrollCollapse: true,
			paging: false,
			
			aLengthMenu: [ 20, 50, 100, 300 ],
			aoColumnDefs: [ { "bSortable": false, "aTargets": [ 0 ] }],  

		} );
		
		new $.fn.DataTable.FixedColumns( table ,{"iLeftColumns": 2,"iRightColumns": 1});
	
	},
    docancel:function(){
        var instancethis=this;
        $('.listViewContentDiv').on("click",'.docancel',function(e){
            var elem = jQuery(e.currentTarget);
            var recordId = elem.closest('tr').data('id');
            var name=$('#stagerecordname').val();
            var msg={'message':"是否要作废该工单？"};
            instancethis.showConfirmationBox(msg).then(function(e){
                msg={'message':"确定要作废该工单？"};
                var voidreason=$('#voidreason').val();
                Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                    var params={};
                    var module = app.getModuleName();
                    $('.isuserid').each(function(){
                        ids=ids+$(this).val()+',';
                    });
                    params['record']=recordId;
                    params['action']='BasicAjax';
                    params['module']=module;
                    params['voidreason']=voidreason;
                    params['mode']='docancel';
                    AppConnector.request(params).then(
                        function(data){
                        	if(data.success){
                                if(data.result.success){
                                    window.location.reload(true);
                                }else{
                                    Vtiger_Helper_Js.showMessage({type:'error',text:data.result.message});
                                }
							}else{
                                Vtiger_Helper_Js.showMessage({type:'error',text:'作废失败'});
							}
                        }
                    );

                 });
            });
            $('.modal-body').append('<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0;"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">作废原因<font color="red">*</font>:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10"><textarea id="voidreason" class="span11 "></textarea></span></div></td></tr></tbody></table>');
        });
    },
    checkedform:function(data){
        if($('#voidreason').val()=='')
        {
            $('#voidreason').focus();
            $('#voidreason').attr('data-content','<font color="red">必填项不能为空</font>');
            $('#voidreason').attr('data-placement','right');
            $('#voidreason').popover("show");
            $('.popover-content').css({"color":"red","fontSize":"12px"});
            $('.popover').css('z-index',1000010);
            setTimeout("$('#voidreason').popover('destroy')",2000);
            return false;
        }else{
            return true;
        }
    },
    showConfirmationBox : function(data){
        var thisstance=this;
        var aDeferred = jQuery.Deferred();
        var width='800px';
        if(typeof  data['width'] != "undefined"){
            width=data['width'];
        }
        var bootBoxModal = bootbox.confirm({message:data['message'],width:width, callback:function(result) {
            if(result){
                if(thisstance.checkedform(data)){
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
	registerLoadAjaxEvent:function(){
		$('body').on(Vtiger_List_Js.loadAjax,function(e,data){
			var instace=new SalesOrder_List_Js;
			instace.registerEventForSalesorderGetid();
			instace.registerEventForDepartment();
		});
	},
	registerEvents:function(){
		this._super();
//		this.registerEventForSalesorderGetid();
		this.registerEventForDepartment();
		this.registerLoadAjaxEvent();
        this.docancel();
		//this.Tableinstance();
	}
	
});