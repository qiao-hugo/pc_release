/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("AutoTask_Detail_Js",{
    clicksubmit :function(field, rules, i, options){
        return false;
    }
},{
    inputcustommail:function(){
        /*$("body").on("focus","#custom_rece",function(){
            var input = $("#custom_rece");
            $(document).keydown(function(e) {
                if ( e.which == 32)
                    input.val(input.val()+"##");
            })
        })*/
    },

    //验证邮箱为空的情况
    checkemptyemail:function(){
        $("body").on('change',"#memberReceive",function(){
            var params = $('#showaudit').serialize();
            activeId = $("input[name='taskid']").val();
            the_flow_id = $("input[name='flowid']");
            params+="&autoflowtaskid="+activeId+"&autoflowid="+the_flow_id+"&test=true";

            AppConnector.request(params).then(function (data) {
                $("#test").val(data.result['type']);
                if(data.result['type'] !==0){
                    var emptyemail = "以下人员没有邮箱请自行添加: ";
                    if(data.result['type'] == 1 ||data.result['type'] == 3) {
                        $.each(data.result['receive'], function (i, item) {
                            emptyemail += "<" + item + ">";
                        });
                    }
                    if(data.result['type'] == 2 ||data.result['type'] == 3) {
                        $.each(data.result['copy'],function(i,item){
                            emptyemail +="<"+item+">";
                        });
                    }
                    $("#emptytixing").text(emptyemail);
                }
            })
        });

        $("body").on('change',"#memberCopy",function(){
            var params = $('#showaudit').serialize();
            activeId = $("input[name='taskid']").val();
            the_flow_id = $("input[name='flowid']");
            params+="&autoflowtaskid="+activeId+"&autoflowid="+the_flow_id+"&test=true";

            AppConnector.request(params).then(function (data) {
                $("#test").val(data.result['type']);
                if(data.result['type'] !==0){
                    var emptyemail = "以下人员没有邮箱请自行添加: ";
                    if(data.result['type'] == 1 ||data.result['type'] == 3) {
                        $.each(data.result['receive'], function (i, item) {
                            emptyemail += "<" + item + ">";
                        });
                    }
                    if(data.result['type'] == 2 ||data.result['type'] == 3) {
                        $.each(data.result['copy'],function(i,item){
                            emptyemail +="<"+item+">";
                        });
                    }
                    $("#emptytixing").text(emptyemail);
                }
            })
        });    },

	registerEvents : function(){
		this._super();
        if($("#clickid").val()){
            var id = '#'+$("#clickid").val()+".label-1";
            $(id).trigger('click');
        }
       if($('#email_ID').length>0){
           var ckEditorInstance = new Vtiger_CkEditor_Js();
           ckEditorInstance.loadCkEditor('email_ID');
       }
        this.inputcustommail();
        this.checkemptyemail();
        //this.clicksubmit();
    }
})