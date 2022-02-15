/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("Double_List_Js",{},{

	ChangeAccountCategory: function(recordId,type){
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
		
		
		
		
	},

	BarLinkRemove:function(){
		var  url = window.location.pathname + window.location.search;
		url=url.replace('/','');
		jQuery('.quickLinksDiv').find("a[href='" + url + "']").parent().addClass('leftmenu');
	},
	
	
	Tableinstance:function(){
        /*tab = "<table ><tr><td id='test'>"
        $('#table_double thead th').each( function () {
            var title = $('#table_double thead th').eq( $(this).index() ).text();
            tab+='<input type="text" placeholder="'+title+'" />';
        });
        tab+='</td></tr></table>';
        $('#table_double').before(tab);*/
        $('tfoot th').each( function () {
            var title = $('thead th').eq($(this).index()).text();
           // var length = $('thead th').eq($(this).index()).width();
            $(this).html( '<input style="width:90%;padding:0" type="text" placeholder="'+title+'" />' );
        } );

          var tables =   $('#table_double').DataTable({
              language: {
                  "sProcessing":"处理中...",
                 "sLengthMenu":"显示 _MENU_ 项结果",
                "sZeroRecords":  "没有匹配结果",
                "sInfo":"显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",
                "sInfoEmpty": "显示第 0 至 0 项结果，共 0 项","sInfoFiltered": "(由 _MAX_ 项结果过滤)",
                "sInfoPostFix":  "","sSearch":"当前页快速检索:",
                "sUrl":"","sEmptyTable":"表中数据为空","sLoadingRecords": "载入中...",
                "sInfoThousands": ",",
                 "oPaginate": {"sFirst":"首页","sPrevious": "上页","sNext":"下页","sLast":"末页"},
                "oAria": {"sSortAscending":  ": 以升序排列此列","sSortDescending": ": 以降序排列此列"}
              },
              "aoColumnDefs": [
                  {"sWidth": "300px", "aTargets":[5,7,9]},
                  {"sWidth": "200px", "aTargets":[0]},
                  {"sWidth": "70px", "aTargets":[2]}
              ],
             // "sPaginationType": "full_numbers",
              "aLengthMenu": [[20,50,100,-1],[20,50,100,'所有']],
              "bLengthChange": true,
              sScrollX: "3000",
              sScrollXInner: "2500px",
              scrollX:        true,
              bSort:true,
              scrollY:true,
              scrollY:$(window).height()-265,
             // "dom": '<"toolbar">frtip',
             // iDisplayLength: 1,
              "bInfo": true,//页脚信息,
              "bAutoWidth": true,//自动宽度e
              scrollCollapse: true
          });
       // new $.fn.DataTable.FixedColumns( tables ,{"iLeftColumns": 1,"iRightColumns": 1});
        new $.fn.DataTable.FixedColumns( tables ,{"iLeftColumns": 1});

       /* $("div.toolbar").html('<b style="color:red">自定义文字、图片等等</b>');*/
        tables.columns().eq( 0 ).each( function ( colIdx ) {
            $( 'input',tables.column( colIdx ).footer()).on( 'keyup change', function () {
                tables.column( colIdx ).search( this.value ).draw();
            } );
        });
       /* $('#test input').each(function(){
            var err = $(this).index()
            $(this).on('keyup change',function(){
                //console.log(err);
                //console.log(err+":"+this.value);
                tables.column(err).search( this.value ).draw();
            })
        })*/
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

registerEvents : function(){
	this._super();
	this.registerChangeRecordClickEvent();
	this.BarLinkRemove();
	this.ActiveClick();
	this.Tableinstance();
}

});