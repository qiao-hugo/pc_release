/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("VisitDepartment_Edit_Js",{},{
	ckEditorInstance:'',
	ckEInstance:'',
	rowSequenceHolder : false,
	/**
		pop two calander.
	*/
	updateVComment:function(){
		$('#EditView').on('click','#updatevdcomment',function(){
			var deparmentid=$('select[name="deparmentid[]"]').val();
			var year=$('input[name="year"]').val();
			var month=$('input[name="month"]').val();
			if(deparmentid=='' || year=='' || month==''){
                var params = {text : '部门,年度,月份必填',
                    title : app.vtranslate('必填项不能为空')};
                Vtiger_Helper_Js.showPnotify(params);
				return false;
			}
            urlParams={
                "module": 'VisitDepartment',
                "view": "Detail",
                "record": 0,
                "deparmentid": deparmentid,
                "year": year,
                "month": month,
                'mode': 'updateVdepartinfo'
            };
            var progressIndicatorElement = jQuery.progressIndicator({
                'message' : '正在处理,请耐心等待哟',
                'position' : 'html',
                'blockInfo' : {'enabled' : true}
            });
            AppConnector.requestPjaxPost(urlParams).then(
                function(data){
                    $('#insertbeforecontents').html(data);
                    progressIndicatorElement.progressIndicator({
                        'mode' : 'hide'
                    });
				});
		});

	},
	reloading:function(){
		var record=$('input[name="record"]').val();
		if(record>0) {
            urlParams = {
                "module": 'VisitDepartment',
                "view": "Detail",
                "record": record,
                'mode': 'updateVdepartinfo'
            };

            AppConnector.requestPjaxPost(urlParams).then(
                function (data) {
                    $('#insertbeforecontents').html(data);

                });
        }
        $('#VisitDepartment_editView_fieldName_year').datepicker({
            format: "yyyy",
            language:  'zh-CN',
            changeYear: true,
            startView: 2,
            maxViewMode: 2,
            minViewMode:2,
            autoclose: true,
            pickerPosition: "bottom-left",
            showMeridian: 0

        });
        $('#VisitDepartment_editView_fieldName_month').datepicker({
            format: "mm",
            language:  'zh-CN',
            autoclose: true,
            startView: 1,
            maxViewMode: 1,
            minViewMode:1,
            pickerPosition: "bottom-left",
            showMeridian: 0,
        });
	},
	registerBasicEvents:function(container){
		this._super(container);
		this.updateVComment();
		this.reloading();

	    //$("#VisitingOrder_editView_fieldName_enddate").val(endtime);
	}
});




















