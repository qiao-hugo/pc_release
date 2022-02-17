/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("VCommentAnalysis_List_Js",{},{
    //初始化
    loading:function(){
        $('#visitQuery').trigger('click');
    },

    //加载时间控件
    getdatetime:function(){
        var endtime = app.addOneHour();
        $('#datayear').datepicker({
            format: "yyyy",
            language:  'zh-CN',
            autoclose: true,
            pickerPosition: "bottom-right",
            showMeridian: 0,
            endDate:new Date(),
            weekStart:1,
            todayHighlight:1,
            startView: 2,
            maxViewMode: 2,
            minViewMode:2,
            forceParse:0
        });
        $('#datamonth').datepicker({
            format: "mm",
            language:  'zh-CN',
            autoclose: true,
            pickerPosition: "bottom-right",
            showMeridian: 0,
            endDate:new Date(),
            weekStart:1,
            todayHighlight:1,
            startView: 1,
            maxViewMode: 1,
            minViewMode:1,
            forceParse:0
        });
    },
    entryconfim:function(){
        var thisInstance=this;
        $('table').on('click','#visitQuery',function(){
            var progressIndicatorElement = jQuery.progressIndicator({
                'message' : '正在处理,请耐心等待哟',
                'position' : 'html',
                'blockInfo' : {'enabled' : true}
            });
            $('#bartable').empty();

            var departmentid=$('#department_editView_fieldName_dropDown').val();
            var userid=$('#user_editView_fieldName_dropDown').val();
            var datayear=$('#datayear').val();
            var datamonth=$('#datamonth').val();
            postData= {
                'module': app.getModuleName(),
                'action': 'selectAjax',
                'mode': 'getVCASIC',
                'department':departmentid,
                'datayear':datayear,
                'datamonth':datamonth
            };
            AppConnector.requestPjaxPost(postData).then(
                function(data){
                    progressIndicatorElement.progressIndicator({
                        'mode' : 'hide'
                    });
                    $('#bartable').html(data);
                    $("#flaltt1").smartFloat();
                }
            )
        });
    },

    registerEvents : function(){
        this._super();
        this.entryconfim();
        this.getdatetime();
        this.loading();
        //this.departmentchange();

    }
});