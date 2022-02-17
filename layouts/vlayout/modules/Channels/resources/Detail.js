/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("Channels_Detail_Js",{
},{
	addComment:function(){
        $('#fllowupdate,#nextdate').datepicker({
            format: "yyyy-mm-dd",
            language:  'zh-CN',
            autoclose: true,
            todayBtn: false,
            pickerPosition: "bottom-left",
            showMeridian: 0
        });
        $('.detailSaveComment').on('click',function(){
            var recordId=$('#recordId').val();
            var fllowupdate=$('#fllowupdate').val();
            var nextdate=$('#nextdate').val();
            var hasaccess=$('#hasaccess:checked').val()==1?1:0;
            var currentprogess=$('#currentprogess').val();
            var nextwork=$('#nextwork').val();
            var policeindicator=$('#policeindicator').val();
            var params={
                "module": app.getModuleName(),
                "action": "ChangeAjax",
                "mode": "saveComment",
                recordId:recordId,
                fllowupdate:fllowupdate,
                nextdate:nextdate,
                hasaccess:hasaccess,
                currentprogess:currentprogess,
                nextwork:nextwork,
                policeindicator:policeindicator
            };
            AppConnector.request(params).then(
                function(data){
                    window.location.reload();
                },
                function(error){
                    console.log(error);
                }
            );
        });
	},
	

	
	registerEvents:function(){
		this._super();
		this.addComment();
	}
});