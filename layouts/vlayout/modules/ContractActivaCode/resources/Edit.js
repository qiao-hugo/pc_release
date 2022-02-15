/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("VisitingOrder_Edit_Js",{},{
	ckEditorInstance:'',
	ckEInstance:'',
	rowSequenceHolder : false,
	registerReferenceSelectionEvent : function(container) {
		this._super(container);
		var thisInstance = this;
		//wangbin 2015-1-13 修改之前拜访单关联列表,input获取name值有所变化.
		jQuery('input[name="related_to"]', container).on(Vtiger_Edit_Js.referenceSelectionEvent, function (e,data){accountlist(data['source_module']);});				
		function accountlist(sourcemodule){	
			if(sourcemodule=='Accounts'){
				var Accountid=$('input[name="related_to"]');
				if(Accountid.val().length>0){
					thisInstance.loadWidgetNote(Accountid.val());
				}
				
			}			
		}	
	},
	loadWidgetNote : function(id){
		$accountid=id;
		var params={};
		params['accountid'] =$accountid ;                  //公司的id
		params['module'] = 'VisitingOrder';
		params['action'] = 'SaveAjax';
		params['mode'] = 'autofillvisitingorder';
		AppConnector.request(params).then(
				function(data){
					if(data.success==true){
						address=data.result[0].address;
						if(address !==null){
							re=new RegExp("#","g");
							var	address=address.replace(re,"");
							$('#VisitingOrder_editView_fieldName_destination').val(address);					
						}
						
						//console.log(data);
						contact=data.result['1'];
						$("select#contactorselect").remove();
						if(data.result[1].length!==0){
							if(contact.length==1){
								$('#VisitingOrder_editView_fieldName_contacts').val(contact[0].name);
							}else{
								
								var str="";
								$.each(contact,function(n,value){
									str += "<option value="+value.name+'>'+value.name+"</option>";
								})
								newstr = "<select id='contactorselect'>"+str+"</select>"
								$('#VisitingOrder_editView_fieldName_contacts').closest('span').append(newstr);
								$("#contactorselect").on('change',function(){
									$('#VisitingOrder_editView_fieldName_contacts').val($(this).val());
							})
									
									$('#VisitingOrder_editView_fieldName_contacts').val($("#contactorselect option:first").val());
							;}
						}				
					}
				})
	},
	/**
		pop two calander.
	*/
	registerBasicEvents:function(container){
		this._super(container);
		this.registerReferenceSelectionEvent(container);	
		var time = $("#VisitingOrder_editView_fieldName_startdate").val();
		var endtime = app.addOneHour();
        //var date = new Date($.ajax({async: false}).getResponseHeader("Date"));
        //var bombay = date + (3600000 * 8);
        //var times = new Date(bombay);
		var startDate=$('#VisitingOrder_editView_fieldName_startdate').data('sdate');
		$('#VisitingOrder_editView_fieldName_startdate').datetimepicker({
			format: "yyyy-mm-dd hh:ii",
			language:  'zh-CN',
	        autoclose: true,
	        todayBtn: true,
	        pickerPosition: "bottom-left",
	        showMeridian: 0,
             startDate:startDate
	    });
        //enddate=new Date(date);
        var enddate=$('#VisitingOrder_editView_fieldName_enddate').data("edate");
        //enddate.setMinutes(enddate.getMinutes()+30);
        $('#VisitingOrder_editView_fieldName_enddate').datetimepicker({
			format: "yyyy-mm-dd hh:ii",
			language:  'zh-CN',
	        autoclose: true,
	        todayBtn: true,
	        pickerPosition: "bottom-left",
	        showMeridian: 0,
            startDate:enddate
	    });
	    //$("#VisitingOrder_editView_fieldName_enddate").val(endtime);
	}
});




















