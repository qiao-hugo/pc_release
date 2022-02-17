Vtiger_List_Js("SupplierStatement_List_Js",{

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

registerEvents : function(){
	this._super();
	this.registerChangeRecordClickEvent();
	this.BarLinkRemove();
	this.ActiveClick();

}

});
