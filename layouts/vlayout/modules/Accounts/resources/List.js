/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("Accounts_List_Js",{},{

	ChangeAccountCategory: function(recordId,type,accountName = ''){
		var listInstance = Vtiger_List_Js.getInstance();
		var message = app.vtranslate('LBL_'+type+'_CONFIRMATION');
		Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
			function(e) {
				var module = app.getModuleName();
				var postData = {
					"module": module,
					"action": "ChangeAjax",
					"record": recordId,
					'type':type,
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
						
						if(data.success) {
							//移除领取客户
							$('tr[data-id='+recordId+']').remove();
                            if(postData.type=='SELF'){
                                var message1 = app.vtranslate('客户领取成功,是否要继续领取?');
                                Vtiger_Helper_Js.showConfirmationBox({'message' : message1}).then(
                                    function(e){},function(e){location.href='/index.php?module=Accounts&view=Detail&record='+postData.record}	 
                                 );
							}else{
								//window.location.reload();
							}
						} else {
							var  params = {
								text : app.vtranslate(data.message),
								title : app.vtranslate('JS_LBL_PERMISSION')
							}
							Vtiger_Helper_Js.showPnotify(params);
						}
					},
					function(error,err){

					}
				);
			},
			function(error, err){
			}
		);
		if(accountName != ''){
			$('.modal-body').append('<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0;"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">客户名称:</label></td><td class="fieldValue medium" style="color:#F00" colspan="3"><label class="pull-left marginLeft10px">' + accountName + '</label></td></tr></tbody></table>');
		}
	},

	BarLinkRemove:function(){
		var  url = window.location.pathname + window.location.search;
		url=url.replace('/','');
		jQuery('.quickLinksDiv').find("a[href='" + url + "']").parent().addClass('leftmenu');
	},
	
	
	Tableinstance:function(){
		var instance=$('.listViewEntriesDiv');
		instance.progressIndicator({});
		var table = $('.listViewEntriesTable').DataTable( {
		sDom: '<"top">rt<"bottom"p><"clear">',
		iDisplayLength: 10,
		language: {"sProcessing":   "处理中...",	"sLengthMenu":   "显示 _MENU_ 项结果","sZeroRecords":  "没有匹配结果","sInfo":         "显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",	"sInfoEmpty":    "显示第 0 至 0 项结果，共 0 项","sInfoFiltered": "(由 _MAX_ 项结果过滤)","sInfoPostFix":  "","sSearch":"当前页快速检索:","sUrl":"","sEmptyTable":     "表中数据为空","sLoadingRecords": "载入中...",
	"sInfoThousands":  ",",	"oPaginate": {"sFirst":"首页","sPrevious": "上页","sNext":"下页","sLast":"末页"},
	"oAria": {"sSortAscending":  ": 以升序排列此列","sSortDescending": ": 以降序排列此列"}},
		sScrollXInner: "6000px",
			//scrollY:        $(window).height()-265,
			scrollX:        true,
			scrollCollapse: true,
			paging: false,
			bSort:false,
			//aLengthMenu: [ 20, 50, 100, 300 ],
			//aoColumnDefs: [ { "bSortable": false, "aTargets": [ 0 ] }], 
			fnDrawCallback:function(){
				instance.progressIndicator({'mode': 'hide'});
			}

		} );
		
		
		new $.fn.DataTable.FixedColumns( table ,{"iLeftColumns": 1,"iRightColumns": 1});
	
	
	},
	
	/*registerSelfRecordClickEvent: function(){
		var thisInstance = this;
		var listViewContentDiv = this.getListViewContentContainer();
		var message = app.vtranslate('LBL_SELF_CONFIRMATION');
		listViewContentDiv.on('click','.selfRecordButton',function(e){
			var elem = jQuery(e.currentTarget);
			alert(e.id);
			var recordId = elem.closest('tr').data('id');
			thisInstance.ChangeAccountCategory(recordId,message,'overt');
			e.stopPropagation();
		});
	},
	
	registerOvertRecordClickEvent: function(){
		var thisInstance = this;
		var listViewContentDiv = this.getListViewContentContainer();
		var message = app.vtranslate('LBL_OVERT_CONFIRMATION');
		listViewContentDiv.on('click','.overtRecordButton',function(e){
			var elem = jQuery(e.currentTarget);
			var recordId = elem.closest('tr').data('id');
			thisInstance.ChangeAccountCategory(recordId,message,'overt');
			e.stopPropagation();
		});
	},
	*/
	registerChangeRecordClickEvent: function(){
		var thisInstance = this;
		var listViewContentDiv = this.getListViewContentContainer();
		listViewContentDiv.on('click','.ChangeRecordButton',function(e){ 
			var elem = jQuery(e.currentTarget);
			var recordId = elem.closest('tr').data('id');
			var accountName = elem.closest('tr').data('name');
			thisInstance.ChangeAccountCategory(recordId,elem.attr('id'),accountName);
			e.stopPropagation();
		});
	},
    
	ActiveClick:function(){
		var  url = window.location.pathname + window.location.search;
		url=url.replace('/','');
		jQuery('.breadcrumb li').find("a[href='" + url + "']").css('color',"#1A94E6");
	
	},
	registerLoadAjaxEvent:function(){
		$('body').on(Vtiger_List_Js.loadAjax,function(e,data){
			var instace=new Accounts_List_Js;
			instace.Tableinstance();
		});
	},


setAdvancesmoney : function () {
	$(document).on('click', '.setAdvancesmoney', function () {
		var msg = {
            'message': '更改垫款额',
            "width":"400px",
        };
		var old_advancesmoney = $(this).attr('data-status');
		var me = this;
		var $select_tr = $(this).closest('tr');
		var recordId = $select_tr.data('id');
		var me = this;
		Vtiger_Helper_Js.showConfirmationBox(msg).then(
			function(e) {
				var selectValue = $('#input_advancesmoney').val();
				var t = /^\d+(\.\d+)?$/.test(selectValue);
				if (!t) {
					alert('垫款必须为数字');
					return;
				}
				var module = app.getModuleName();
				var postData = {
					"module": module,
					"action": "ChangeAjax",
					"record": recordId,
					'status': selectValue,
					'mode': 'setAdvancesmoney',
					'old_advancesmoney' : old_advancesmoney
					//"parent": app.getParentModuleName()
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
							$select_tr.find('.advancesmoney_value').html(selectValue);
							
							$(me).attr('data-status', selectValue);
							//alert('更新合同自动关闭状态成功');
							var  params = {text : '更改成功', title : '提示'};
							Vtiger_Helper_Js.showMessage(params);
						} else {
							var  params = {text : '更改失败', title : '提示'};
							Vtiger_Helper_Js.showMessage(params);
						}
						//location.reload();
						
					},
					function(error,err){}
				);
			},function(error, err){}
		);
		
		var str = '<input name="advancesmoney" id="input_advancesmoney" type="text" value="'+old_advancesmoney+'" />';
		$('.modal-body').append('<table class="table" style="border-left:none;border-bottom:none;margin-top:20px;margin-bottom:0;"><tbody><tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">垫款额:</label></td><td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10">'+str+'</span></div></td></tr></tbody></table>');
	});
    var _this=this;
    var listViewContentDiv = this.getListViewContentContainer();
    listViewContentDiv.on('click','.noclick',function(event){
        event.stopPropagation();
    });
    listViewContentDiv.on("click",".checkedall",function(event){
        $('input[name="Detailrecord\[\]"]').iCheck('check');
        event.stopPropagation();
    });

    //放入公海-全选 gaocl add 2018/02/28
    listViewContentDiv.on("click",".over_checkedall",function(event){
        $('input[name="DetailrecordOver\[\]"]').iCheck('check');
        event.stopPropagation();
    });
    listViewContentDiv.on("click",".over_checkedinverse",function(event){
        $('input[name="DetailrecordOver\[\]"]').iCheck('toggle');
        event.stopPropagation();
    });

    listViewContentDiv.on("click",".checkedinverse",function(event){
        $('input[name="Detailrecord\[\]"]').iCheck('toggle');
        event.stopPropagation();
    });
    listViewContentDiv.on("click",".stampall",function(event){
        $.each($('input[name="Detailrecord\[\]"]:checkbox:checked'),function(key,value){
            var ids=$(value).val();
            if(ids>0)
			{

                var dofalg=_this.BatchReceive(ids);
                if(dofalg)
				{
					return false;
				}
                var recordId = $(value).closest('tr');
                recordId.find('.deletedflag').remove();
			}

        });
        event.stopPropagation();//阻止事件冒泡
    });

    //批量放入公海 gaocl add 2018/02/28
    listViewContentDiv.on("click",".over_stampall",function(event){
        var message = app.vtranslate('放入公海后5天内不能再领取,确认要放到公海中吗？');
        Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
            function(e){
                $.each($('input[name="DetailrecordOver\[\]"]:checkbox:checked'),function(key,value){
                    var ids=$(value).val();
                    if(ids>0)
                    {
                        var dofalg=_this.BatchReceive_overt(ids);
                        if(dofalg)
                        {
                            return false;
                        }
                        var recordId = $(value).closest('tr');
                        recordId.find('.deletedflag').remove();
                    }

                })
            },
            function(e){

            }
        );
        event.stopPropagation();//阻止事件冒泡
    });

    listViewContentDiv.on("click",".stamp",function(event){
        var ids=$(this).data('id');
        if(''!=ids){
            _this.BatchReceive(ids);
        }
        var recordId = $(this).closest('tr');
        recordId.find('.deletedflag').remove();
        event.stopPropagation();//阻止事件冒泡
    });

    //放入公海 gaocl add 2018/02/28
    listViewContentDiv.on("click",".stamp_overt",function(event){
        var _that = this;
        var message = app.vtranslate('放入公海后5天内不能再领取,确认要放到公海中吗？');
        Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
            function(e){
                var ids=$(_that).data('id');
                if(''!=ids){
                    _this.BatchReceive_overt(ids);
                }
                var recordId = $(this).closest('tr');
                recordId.find('.deletedflag').remove();
                event.stopPropagation();//阻止事件冒泡*!/
            },
            function(e) {

            }
        )
    });
},
/*批量领取取时区*/
BatchReceive:function(recordId){

    var module = app.getModuleName();
    var  postData={};
    postData.data = {
        "module": module,
        "action": "ChangeAjax",
        "record": recordId,
        'type':"TEMPORARY"
    };
    postData.async=false;

    var Message = app.vtranslate("客户领取中...");

    var progressIndicatorElement = jQuery.progressIndicator({
        'message' : Message,
        'position' : 'html',
        'blockInfo' : {'enabled' : true}
    });
    var falg=false;
    AppConnector.request(postData).then(
        function(data){
            progressIndicatorElement.progressIndicator({
                'mode' : 'hide'
            });
            if(data.success) {
                //移除领取客户
                $('tr[data-id='+recordId+']').remove();
                var  params = {
                    text : app.vtranslate('客户领取成功!!'),
                    title : app.vtranslate('JS_LBL_PERMISSION')
                }
                Vtiger_Helper_Js.showPnotify(params);
                falg=false;
            } else {
                var  params = {
                    text : app.vtranslate(data.message),
                    title : app.vtranslate('JS_LBL_PERMISSION')
                }
                Vtiger_Helper_Js.showPnotify(params);
                falg= true;
            }
        },
        function(error,err){

        }
    );
    return falg;
},
    // 批量放入公海 gaocl add 2018/02/28
    BatchReceive_overt:function(recordId){

        var module = app.getModuleName();
        var  postData={};
        postData.data = {
            "module": module,
            "action": "ChangeAjax",
            "record": recordId,
            'type':"OVERT"
        };
        postData.async=false;

        var Message = app.vtranslate("放入公海中...");

        var progressIndicatorElement = jQuery.progressIndicator({
            'message' : Message,
            'position' : 'html',
            'blockInfo' : {'enabled' : true}
        });
        var falg=false;
        AppConnector.request(postData).then(
            function(data){
                progressIndicatorElement.progressIndicator({
                    'mode' : 'hide'
                });
                if(data.success) {
                    //移除领取客户
                    $('tr[data-id='+recordId+']').remove();
                    var  params = {
                        text : app.vtranslate('放入公海成功!!'),
                        title : app.vtranslate('JS_LBL_PERMISSION')
                    }
                    Vtiger_Helper_Js.showPnotify(params);
                    falg=false;
                } else {
                    var  params = {
                        text : app.vtranslate(data.message),
                        title : app.vtranslate('JS_LBL_PERMISSION')
                    }
                    Vtiger_Helper_Js.showPnotify(params);
                    falg= true;
                }
            },
            function(error,err){

            }
        );
        return falg;
    },

    registerEvents : function(){
        this._super();
        this.registerChangeRecordClickEvent();
        //this.Tableinstance();
        this.BarLinkRemove();
        this.ActiveClick();
        this.setAdvancesmoney();
        // this.registerLoadAjaxEvent();
    }

});
