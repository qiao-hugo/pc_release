Vtiger_List_Js("Staypayment_List_Js",{

},{
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
	registerChangeRecordClickEvent: function(){
		var thisInstance = this;
		var listViewContentDiv = this.getListViewContentContainer();
		listViewContentDiv.on('click','.ChangeRecordButton',function(e){ 
			var elem = jQuery(e.currentTarget);
			var recordId = elem.closest('tr').data('id');
			thisInstance.ChangeAccountCategory(recordId,elem.attr('id'));
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

	exportButton:function(){
		$(".exportButton").click(function () {
			var idsArray=new Array();
			$(".listViewEntries").each(function () {
				idsArray.push($(this).data('id'));
			});
			if(idsArray.length>0){
				$("#cpublic").val('ExportAll');
				$("#PostQuery").trigger("click");
			}else{
				alert('此页没有数据无法导出');
				return false;
			}
		});
	},

	postQuery:function(){
		$("#PostQuery").click(function () {
			$("#cpublic").val('normal');
		});
	},

	delReceive:function(){
		var listViewContentDiv = this.getListViewContentContainer();

		listViewContentDiv.on('click', '.deleteRecord', function(e){
			var msg = {
				'message': '确定要删除吗?',
				"width":"400px",
			};
			var elem = jQuery(e.currentTarget);
			var recordId = elem.closest('tr').data('id');

			Vtiger_Helper_Js.showConfirmationBox(msg).then(
				function(e) {
					var module = app.getModuleName();
					var postData = {
						"module": module,
						"action": "BasicAjax",
						"record": recordId,
						'mode': 'delReceive'
					}
					var Message = "努力删除中...";

					var progressIndicatorElement = jQuery.progressIndicator({
						'message' : Message,
						'position' : 'html',
						'blockInfo' : {'enabled' : true}
					});
					AppConnector.request(postData).then(
						function(data){
							// 隐藏遮罩层
							progressIndicatorElement.progressIndicator({
								'mode' : 'hide'
							});
							if (data.success&&data.result.flag) {
								Vtiger_Helper_Js.showMessage({type : 'success', text : '删除成功'});
								location.reload();
							}else{
								Vtiger_Helper_Js.showMessage({type:'error',text:data.result.msg});
							}
						},
						function(error,err){
							Vtiger_Helper_Js.showMessage({type:'error',text:"删除失败"});
						}
					);
				},function(error, err){}
			);
		});

	},

	//导出
	delayExport:function(){
		$("body").on('click','.delayExportButton',function () {
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
			var module = app.getModuleName();
			var postData = {
				"module": module,
				'BugFreeQuery':form,
				'view':'List',
				'public':'delayExport'
			}
			var Message = app.vtranslate('正在导出');
			var progressIndicatorElement = jQuery.progressIndicator({
				'message' : Message,
				'position' : 'html',
				'blockInfo' : {'enabled' : true}
			});
			AppConnector.request(postData).then(
				function(data){
					var result=eval('(' + data + ')');
					console.log(eval('(' + data + ')'));
					progressIndicatorElement.progressIndicator({
						'mode' : 'hide'
					});
					window.location.href=result.result.msg;
				},
				function(error,err){
				}
			);
		});
	},

registerEvents : function(){
	this._super();
	this.registerChangeRecordClickEvent();
	this.BarLinkRemove();
	this.ActiveClick();
	this.exportButton();
	this.postQuery();
	this.delReceive();
	this.delayExport();
}

});