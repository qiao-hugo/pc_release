Vtiger_List_Js('OrderChargeback_List_Js',{
	
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
		//this.registerEventForDepartment();
		//this.registerLoadAjaxEvent();
		//this.Tableinstance();
	}
	
});