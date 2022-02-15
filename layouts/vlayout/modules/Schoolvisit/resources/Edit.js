/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("Schoolvisit_Edit_Js",{},{

	

	init: function() {
	},



	/**
		pop two calander.
	*/
	registerBasicEvents:function(container){
		this._super(container);
		this.init();

		var time = $("#Schoolvisit_editView_fieldName_startdate").val();
		var endtime = app.addOneHour();
		$('#Schoolvisit_editView_fieldName_startdate').datetimepicker({
			format: "yyyy-mm-dd hh:ii",
			language:  'zh-CN',
	        autoclose: true,
	        todayBtn: true,
	        pickerPosition: "bottom-left",
	        showMeridian: 0,
            startDate:new Date()
	    });
        enddate=new Date();
        enddate.setMinutes(enddate.getMinutes()+30);
        $('#Schoolvisit_editView_fieldName_enddate').datetimepicker({
			format: "yyyy-mm-dd hh:ii",
			language:  'zh-CN',
	        autoclose: true,
	        todayBtn: true,
	        pickerPosition: "bottom-left",
	        showMeridian: 0,
            startDate:enddate
	    });

	}
});




















