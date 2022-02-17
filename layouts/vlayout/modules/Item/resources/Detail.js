/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("Item_Detail_Js",{

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

    AddRStatement:function(){
        var thisInstance=this;
        $('body').on('click','#newworkflow',function(){
            var soncate=$(this).data("soncate");
            var parentcate=$(this).data("parentcate");
            var soncateid=$(this).data("soncateid");
            var params = {
                'module': 'Item',
                'action': 'ChangeAjax',
                'mode': 'newItemWorkFlow',
                "recordid": $('#recordId').val()
            };
            var Message = "正在处理中,请稍等...";
            var progressIndicatorElement = jQuery.progressIndicator({
                'message' : Message,
                'position' : 'html',
                'blockInfo' : {'enabled' : true}
            });
            AppConnector.request(params).then(
                function (data) {
                    progressIndicatorElement.progressIndicator({
                        'mode' : 'hide'
                    });
                    if(data.success){

                        var message = '<h4>添加工作流!</h4><hr>';
                        var msg = {
                            'message': message,
                            'width': '800px',
                            "action":function(){
                                var departmentid=$("select[name='departmentid']").val();
                                if(departmentid==null){
                                    Vtiger_Helper_Js.showMessage({type:'error',text:"部门必选！"});
                                    return false;
                                }
                                var falg=false;
                                var params={};
                                params.data = {
                                    'module': 'Item',
                                    'action': 'ChangeAjax',
                                    'mode': 'checkCreateFilterWorkFlow',
                                    "recordid": $('#recordId').val(),
                                    'departmentid':departmentid

                                };

                                params.async=false;
                                var Message = "正在验证数据,请稍等...";
                                var progressIndicatorElement = jQuery.progressIndicator({
                                    'message' : Message,
                                    'position' : 'html',
                                    'blockInfo' : {'enabled' : true}
                                });
                                $('.blockOverlay').css({"z-index": 1000004});
                                $('.blockPage').css({"z-index": 1000005});
                                AppConnector.request(params).then(
                                    function (data) {
                                        progressIndicatorElement.progressIndicator({
                                            'mode' : 'hide'
                                        });
                                        if(data.success){
                                            if(data.result.falg){
                                                Vtiger_Helper_Js.showMessage({type:'error',text:"<hr>如下部门对应的工作流已添加，无需重复添加<br><h5>"+data.result.data+"</h5>"});
                                            }else{
                                                falg=true;
                                            }
                                        }else{

                                        }
                                    },
                                    function () {

                                    }
                                );
                                return falg;
                            }
                        };

                        Vtiger_Helper_Js.showConfirmationBox(msg).then(function (e) {
                            var workflowstageids=[];
                            $(".draggable-element").each(function(k,v){
                                workflowstageids.push($(v).attr("data-value"))
                            });

                            var companycodes=[];
                            var companynames=[];
                            $("input[name='companycode']").each(function (k, v) {
                                if($(v).is(":checked")){
                                    companycodes.push($(v).val());
                                    companynames.push($(v).attr("data-value"));
                                }
                            });
                            var departmentid=$("select[name='departmentid']").val();
                            var params = {
                                'module': 'Item',
                                'action': 'ChangeAjax',
                                'mode': 'batchCreateFilterWorkFlow',
                                "recordid": $('#recordId').val(),
                                'workflowstageids':workflowstageids,
                                'companynames':companynames,
                                'companycodes':companycodes,
                                'departmentid':departmentid,
                                'ceocheck':$("input[name='ceocheck']:checked").val()
                            };
                            var Message = "数据添加中,请稍等...";
                            var progressIndicatorElement = jQuery.progressIndicator({
                                'message' : Message,
                                'position' : 'html',
                                'blockInfo' : {'enabled' : true}
                            });
                            $('.blockOverlay').css({"z-index": 1000004});
                            $('.blockPage').css({"z-index": 1000005});
                            AppConnector.request(params).then(
                                function (data) {
                                    progressIndicatorElement.progressIndicator({
                                        'mode' : 'hide'
                                    });
                                    window.location.reload(true);
                                    if(data.success){
                                        window.location.reload(true);
                                    }else{
                                        Vtiger_Helper_Js.showMessage({type:'error',text:data.msg});
                                    }
                                },
                                function () {
                                    window.location.reload();
                                }
                            );


                        }, function (error, err) {});

                        var invoicecompanystr='<select id="departmentid" class="chzn-select referenceModulesList streched" name="departmentid" multiple style="width:590px;">';
                        $.each(data.invoicecompanys, function (key, value) {
                            //console.log(value.invoicecompany);
                            //invoicecompanystr += '<label style="float: left;margin-left: 30px;"><input data-value="'+value.invoicecompany+'" type="checkbox" null="" name="companycode" value="'+value.companycode+'"><span>'+value.invoicecompany+'</span></label>';
                            invoicecompanystr+='<option value="'+key+'">'+value+'</option>';
                        });
                        invoicecompanystr+=' </select>'

                        var workflowstagesstr='';
                        $.each(data.workflowstages, function (key, value) {
                            console.log(value.workflowstagesid);
                            workflowstagesstr += '<label style="float: left;margin-left: 30px;"><input type="checkbox" null="" data-stagename="'+value.workflowstagesname+'" name="workflowstages" value="'+value.workflowstagesid+'"><span><a target="_blank" href="index.php?module=WorkflowStages&view=Detail&record='+value.workflowstagesid+'">'+value.workflowstagesname+'</a></span></label>';
                        });


                        var str = '';
                        var strr="<div class='customclass' style='height:500px;overflow: auto'><form name=\"insertcomment\" id=\"formcomment\">\n" +
                            "\t<div id=\"insertcomment\" style='margin-right:5px;'>\n" +
                            "\t\t<table class=\"table table-bordered blockContainer Duplicates showInlineTable  detailview-table\" data-num=\"1\" id=\"comments1\">\n" +
                            "\t\t\t<tbody>\n" +
                            "\t\t\t\t<tr>\n" +
                            "\t\t\t\t\t<td class=\"fieldLabel\">\n" +
                            "\t\t\t\t\t\t<label class=\"muted pull-right marginRight10px\"><span class=\"redColor\">*</span>项目大小类:</label>\n" +
                            "\t\t\t\t\t</td>\n" +
                            "\t\t\t\t\t<td class=\"fieldValue\" colspan=\"3\">\n" +
                            "\t\t\t\t\t\t<input style='margin-left: 30px;' type=\"text\" id=\"parentcate\" name=\"parentcate\" value='"+parentcate+'-'+soncate+"' readonly>\n" +
                            "<input type='hidden' name='soncateid' value='"+soncateid+"'>"+
                            "\t\t\t\t\t</td>\n" +
                            "\t\t\t\t</tr>\n" +
                            "\t\t\t\t<tr>\n" +
                            "\t\t\t\t\t<td class=\"fieldLabel\">\n" +
                            "\t\t\t\t\t\t<label class=\"muted pull-right marginRight10px\"><span class=\"redColor\">*</span>适用部门:</label>\n" +
                            "\t\t\t\t\t</td>\n" +
                            "\t\t\t\t\t<td class=\"fieldValue\" colspan=\"3\">\n" +
                            "\t\t\t\t\t\t<div class=\"row-fluid\">\n" +
                            invoicecompanystr+
                            "\t\t\t\t\t\t</div>\n" +
                            "\t\t\t\t\t</td>\n" +
                            "\t\t\t\t</tr>\n" +
                            "\t\t\t\t<tr>\n" +
                            "\t\t\t\t\t<td class=\"fieldLabel\">\n" +
                            "\t\t\t\t\t\t<label class=\"muted pull-right marginRight10px\"><span class=\"redColor\">*</span>工作流:</label>\n" +
                            "\t\t\t\t\t</td>\n" +
                            "\t\t\t\t\t<td class=\"fieldValue\" colspan=\"3\">\n" +
                            "\t\t\t\t\t\t<div class=\"row-fluid\" style='height: 200px;'>\n" +
                            workflowstagesstr+
                            "\t\t\t\t\t\t</div>\n" +
                            "\t\t\t\t\t</td>\n" +
                            "\t\t\t\t</tr>\n" +
                            "\t\t\t\t<tr>\n" +
                            "\t\t\t\t\t<td class=\"fieldLabel\">\n" +
                            "\t\t\t\t\t\t<label class=\"muted pull-right marginRight10px\"><span class=\"redColor\">*</span>排序工作流:</label>\n" +
                            "\t\t\t\t\t</td>\n" +
                            "\t\t\t\t\t<td class=\"fieldValue\" colspan=\"3\">\n" +
                            "\t\t\t\t\t\t<div class=\"row-fluid\">\n" +
                            "\t\t\t\t\t\t\t  <section class=\"showcase showcase-1\">\n" +
                            "\t\t\t\t\t\t\t  <div id=\"elements-container\">\n" +
                            // "\t\t\t\t\t\t\t\t<div class=\"draggable-element d-1\">Drag 1222222222222222222</div>\n" +
                            // "\t\t\t\t\t\t\t\t<div class=\"draggable-element d-2\">Drag 2333333333333333</div>\n" +
                            // "\t\t\t\t\t\t\t\t<div class=\"draggable-element d-3\">Drag 322222222222222</div>\n" +
                            // "\t\t\t\t\t\t\t\t<div class=\"draggable-element d-4\">Drag 4333333333</div>\n" +
                            "\t\t\t\t\t\t\t  </div>\n" +
                            "\t\t\t\t\t\t\t</section>\n" +
                            "\t\t\t\t\t\t</div>\n" +
                            "\t\t\t\t\t</td>\n" +
                            "\t\t\t\t</tr>\n" +
                            "\t\t\t</tbody>\n" +
                            "\t\t</table>\n" +
                            "\t</div>\n" +
                            "</form></div>";
                        $('.modal-content .modal-body').append(strr);
                        $('.draggable-element').arrangeable();
                        $('#departmentid').chosen();
                        $('li').arrangeable({dragSelector: '.drag-area'});
                        $('#paymentdate').datepicker({
                            format: "yyyy-mm-dd",
                            language:  'zh-CN',
                            autoclose: true,
                            todayBtn: true,
                            orientation: "top left"
                        });
                        $("input[name='workflowstages']").on("click",function (k, v) {
                            var draglen = $(".draggable-element").length;
                            var workflowstagesval = $(this).val();
                            console.log(workflowstagesval);
                            if($(this).is(":checked")){
                                draglen = 1+draglen;
                                stagename = $(this).data("stagename");
                                $("#elements-container").append('<div data-value="'+workflowstagesval+'" class="draggable-element d-'+draglen+' drag-'+workflowstagesval+'">'+stagename+'</div>');
                            }else{
                                $(".drag-"+workflowstagesval).remove();
                            }
                            $('.draggable-element').arrangeable();

                            $('li').arrangeable({dragSelector: '.drag-area'});
                        });
                    }else{
                        Vtiger_Helper_Js.showMessage({type:'error',text:data.result.msg});
                    }
                },
                function () {
                    window.location.reload();
                }
            );
        });
    },

    updateRecordWorkFlow:function(){
        var thisInstance=this;
        $(".updateRecordButton").on("click",function () {
            var soncate=$(this).data("soncate");
            var parentcate=$(this).data("parentcate");
            var soncateid=$(this).data("soncateid");
            var ceocheck =$(this).data("ceocheck");
            var companycode =$(this).data("companycode");
            var departmentid =$(this).data("departmentid");
            var filterworkflowstageid =$(this).data("filterworkflowstageid");
            var workflowstagesid =$('.workflowstagesid'+filterworkflowstageid);
            console.log(companycode);
            workflowstagesidarr=[];
            workflowstagesnamearr=[];
            $(workflowstagesid).each(function (k, v) {
                workflowstagesidarr.push($(v).attr("data-workflowstagesid"))
                workflowstagesnamearr.push($(v).attr("data-workflowstagesname"))
            });
            $(workflowstagesidarr).each(function (k, v) {
                console.log(v);
                var draglen = $(".draggable-element").length;
                var workflowstagesval = v;
                console.log(workflowstagesval);

                draglen = 1+draglen;
                stagename = workflowstagesnamearr[k];
                $("#elements-container").append('<div data-value="'+workflowstagesval+'" class="draggable-element d-'+draglen+' drag-'+workflowstagesval+'">'+stagename+'</div>');

                $('.draggable-element').arrangeable();

                $('li').arrangeable({dragSelector: '.drag-area'});
            });
            var params = {
                'module': 'Item',
                'action': 'ChangeAjax',
                'mode': 'newItemWorkFlow',
                "recordid": $('#recordId').val()
            };
            var Message = "正在处理中,请稍等...";
            var progressIndicatorElement = jQuery.progressIndicator({
                'message' : Message,
                'position' : 'html',
                'blockInfo' : {'enabled' : true}
            });
            AppConnector.request(params).then(
                function (data) {
                    progressIndicatorElement.progressIndicator({
                        'mode' : 'hide'
                    });
                    if(data.success){
                        console.log(data);
                        var message = '<h4>修改工作流!</h4><hr>';
                        var msg = {
                            'message': message,
                            'width': '800px',
                            "action":function(){
                                var departmentid=$("select[name='departmentid']").val();
                                if(departmentid==null){
                                    Vtiger_Helper_Js.showMessage({type:'error',text:"部门必选！"});
                                    return false;
                                }
                                var falg=false;
                                var params={};
                                params.data = {
                                    'module': 'Item',
                                    'action': 'ChangeAjax',
                                    'mode': 'checkCreateFilterWorkFlow',
                                    "recordid": $('#recordId').val(),
                                    "filterworkflowstageid":filterworkflowstageid,
                                    'departmentid':departmentid
                                };
                                params.async=false;
                                var Message = "正在验证数据,请稍等...";
                                var progressIndicatorElement = jQuery.progressIndicator({
                                    'message' : Message,
                                    'position' : 'html',
                                    'blockInfo' : {'enabled' : true}
                                });
                                $('.blockOverlay').css({"z-index": 1000004});
                                $('.blockPage').css({"z-index": 1000005});
                                AppConnector.request(params).then(
                                    function (data) {
                                        progressIndicatorElement.progressIndicator({
                                            'mode' : 'hide'
                                        });
                                        if(data.success){
                                            if(data.result.falg){
                                                Vtiger_Helper_Js.showMessage({type:'error',text:"<hr>如下部门对应的工作流已添加，无需重复添加<br><h5>"+data.result.data+"</h5>"});
                                            }else{
                                                falg=true;
                                            }
                                        }else{
                                        }
                                    },
                                    function () {
                                    }
                                );
                                return falg;
                            }
                        };

                        Vtiger_Helper_Js.showConfirmationBox(msg).then(function (e) {
                            var workflowstageids=[];
                            $(".draggable-element").each(function(k,v){
                                console.log($(v).attr("data-value"));
                                workflowstageids.push($(v).attr("data-value"))
                            });

                            var companycodes=[];
                            var companynames=[];
                            $("input[name='companycode']").each(function (k, v) {
                                if($(v).is(":checked")){
                                    companycodes.push($(v).val());
                                    companynames.push($(v).attr("data-value"));
                                }
                            });


                            var params = {
                                'module': 'Item',
                                'action': 'ChangeAjax',
                                'mode': 'updateFilterWorkFlow',
                                // "recordid": $('#recordId').val(),
                                'filterworkflowstageid':filterworkflowstageid,
                                'workflowstageids':workflowstageids,
                                'departmentid':$("select[name='departmentid']").val(),
                                // 'companynames':companynames,
                                // 'companycodes':companycodes,
                                 'ceocheck':$("input[name='ceocheck']:checked").val()
                            };
                            AppConnector.request(params).then(
                                function (data) {
                                    if(data.success){
                                        window.location.reload(true);
                                    }else{
                                        Vtiger_Helper_Js.showMessage({type:'error',text:data.msg});
                                    }
                                },
                                function () {
                                    window.location.reload();
                                }
                            );


                        }, function (error, err) {});

                        /*var invoicecompanystr='';
                        $.each(data.invoicecompanys, function (key, value) {
                            console.log(value.invoicecompany);
                            if(value.companycode==companycode){
                                invoicecompanystr += '<label style="float: left;margin-left: 30px;"><input checked data-value="'+value.invoicecompany+'" type="checkbox" null="" name="companycode" value="'+value.companycode+'"><span>'+value.invoicecompany+'</span></label>';
                            }else{
                                invoicecompanystr += '<label style="float: left;margin-left: 30px;"><input disabled data-value="'+value.invoicecompany+'" type="checkbox" null="" name="companycode" value="'+value.companycode+'"><span>'+value.invoicecompany+'</span></label>';
                            }
                        });*/
                        var departmentids=departmentid.split(',');
                        var invoicecompanystr='<select id="departmentid" class="chzn-select referenceModulesList streched" name="departmentid" style="width:590px;" multiple>';
                        $.each(data.invoicecompanys, function (key, value) {
                            //console.log(value.invoicecompany);
                            //invoicecompanystr += '<label style="float: left;margin-left: 30px;"><input data-value="'+value.invoicecompany+'" type="checkbox" null="" name="companycode" value="'+value.companycode+'"><span>'+value.invoicecompany+'</span></label>';
                            invoicecompanystr+='<option value="'+key+'" '+($.inArray(key,departmentids)!=-1?' selected':'')+'>'+value+'</option>';
                        });
                        invoicecompanystr+=' </select>'
                        var workflowstagesstr='';
                        $.each(data.workflowstages, function (key, value) {
                            console.log(value.workflowstagesid);
                            if(workflowstagesidarr.indexOf(value.workflowstagesid)>-1){
                                workflowstagesstr += '<label style="float: left;margin-left: 30px;"><input type="checkbox" null="" checked  data-stagename="'+value.workflowstagesname+'" name="workflowstages" value="'+value.workflowstagesid+'"><span><a target="_blank" href="index.php?module=WorkflowStages&view=Detail&record='+value.workflowstagesid+'">'+value.workflowstagesname+'</a></span></label>';
                            }else{
                                workflowstagesstr += '<label style="float: left;margin-left: 30px;"><input type="checkbox" null=""  data-stagename="'+value.workflowstagesname+'" name="workflowstages" value="'+value.workflowstagesid+'"><span><a target="_blank" href="index.php?module=WorkflowStages&view=Detail&record='+value.workflowstagesid+'">'+value.workflowstagesname+'</a></span></label>';
                            }
                        });
                        /*ceocheckstr='';
                        if(ceocheck){
                            ceocheckstr +=                  "\t\t\t\t\t\t<input style='margin-left: 30px;' type=\"radio\" disabled name=\"ceocheck\"  value='0'>否\n" +
                                "\t\t\t\t\t\t<input type=\"radio\" name=\"ceocheck\" checked value='1'>是\n" ;
                        }else{
                            ceocheckstr +=                  "\t\t\t\t\t\t<input style='margin-left: 30px;' checked type=\"radio\" name=\"ceocheck\" checked value='0'>否\n" +
                                "\t\t\t\t\t\t<input type=\"radio\" disabled name=\"ceocheck\" value='1'>是\n" ;
                        }*/

                        var str = '';
                        var strr="<div class='customclass' style='height:500px;overflow: auto'><form name=\"insertcomment\" id=\"formcomment\">\n" +
                            "\t<div id=\"insertcomment\" >\n" +
                            "\t\t<table class=\"table table-bordered blockContainer Duplicates showInlineTable  detailview-table\" data-num=\"1\" id=\"comments1\">\n" +
                            "\t\t\t<tbody>\n" +
                            "\t\t\t\t<tr>\n" +
                            "\t\t\t\t\t<td class=\"fieldLabel\">\n" +
                            "\t\t\t\t\t\t<label class=\"muted pull-right marginRight10px\"><span class=\"redColor\">*</span>项目大小类:</label>\n" +
                            "\t\t\t\t\t</td>\n" +
                            "\t\t\t\t\t<td class=\"fieldValue\" colspan=\"3\">\n" +
                            "\t\t\t\t\t\t<input style='margin-left: 30px;' type=\"text\" id=\"parentcate\" name=\"parentcate\" value='"+parentcate+'-'+soncate+"' readonly>\n" +
                            "<input type='hidden' name='soncateid' value='"+soncateid+"'>"+
                            "\t\t\t\t\t</td>\n" +
                            "\t\t\t\t</tr>\n" +
                            "\t\t\t\t<tr>\n" +
                            "\t\t\t\t\t<td class=\"fieldLabel\">\n" +
                            "\t\t\t\t\t\t<label class=\"muted pull-right marginRight10px\"><span class=\"redColor\">*</span>适用部门:</label>\n" +
                            "\t\t\t\t\t</td>\n" +
                            "\t\t\t\t\t<td class=\"fieldValue\" colspan=\"3\">\n" +
                            "\t\t\t\t\t\t<div class=\"row-fluid\">\n" +
                            invoicecompanystr+
                            "\t\t\t\t\t\t</div>\n" +
                            "\t\t\t\t\t</td>\n" +
                            "\t\t\t\t</tr>\n" +
                            "\t\t\t\t<tr>\n" +
                            "\t\t\t\t\t<td class=\"fieldLabel\">\n" +
                            "\t\t\t\t\t\t<label class=\"muted pull-right marginRight10px\"><span class=\"redColor\">*</span>工作流:</label>\n" +
                            "\t\t\t\t\t</td>\n" +
                            "\t\t\t\t\t<td class=\"fieldValue\" colspan=\"3\">\n" +
                            "\t\t\t\t\t\t<div class=\"row-fluid\" style='height: 200px;'>\n" +
                            workflowstagesstr+
                            "\t\t\t\t\t\t</div>\n" +
                            "\t\t\t\t\t</td>\n" +
                            "\t\t\t\t</tr>\n" +
                            "\t\t\t\t<tr>\n" +
                            "\t\t\t\t\t<td class=\"fieldLabel\">\n" +
                            "\t\t\t\t\t\t<label class=\"muted pull-right marginRight10px\"><span class=\"redColor\">*</span>排序工作流:</label>\n" +
                            "\t\t\t\t\t</td>\n" +
                            "\t\t\t\t\t<td class=\"fieldValue\" colspan=\"3\">\n" +
                            "\t\t\t\t\t\t<div class=\"row-fluid\">\n" +
                            "\t\t\t\t\t\t\t  <section class=\"showcase showcase-1\">\n" +
                            "\t\t\t\t\t\t\t  <div id=\"elements-container\">\n" +
                            // "\t\t\t\t\t\t\t\t<div class=\"draggable-element d-1\">Drag 1222222222222222222</div>\n" +
                            // "\t\t\t\t\t\t\t\t<div class=\"draggable-element d-2\">Drag 2333333333333333</div>\n" +
                            // "\t\t\t\t\t\t\t\t<div class=\"draggable-element d-3\">Drag 322222222222222</div>\n" +
                            // "\t\t\t\t\t\t\t\t<div class=\"draggable-element d-4\">Drag 4333333333</div>\n" +
                            "\t\t\t\t\t\t\t  </div>\n" +
                            "\t\t\t\t\t\t\t</section>\n" +
                            "\t\t\t\t\t\t</div>\n" +
                            "\t\t\t\t\t</td>\n" +
                            "\t\t\t\t</tr>\n" +
                            "\t\t\t</tbody>\n" +
                            "\t\t</table>\n" +
                            "\t</div>\n" +
                            "</form></div>";
                        $('.modal-content .modal-body').append(strr);

                        $(workflowstagesidarr).each(function (k, v) {
                            console.log(v);
                            var draglen = $(".draggable-element").length;
                            var workflowstagesval = v;
                            console.log(workflowstagesval);

                            draglen = 1+draglen;
                            stagename = workflowstagesnamearr[k];
                            $("#elements-container").append('<div data-value="'+workflowstagesval+'" class="draggable-element d-'+draglen+' drag-'+workflowstagesval+'">'+stagename+'</div>');

                            $('.draggable-element').arrangeable();

                            $('li').arrangeable({dragSelector: '.drag-area'});
                        });


                        $('.draggable-element').arrangeable();
                        $('#departmentid').chosen();
                        $('li').arrangeable({dragSelector: '.drag-area'});
                        $('#paymentdate').datepicker({
                            format: "yyyy-mm-dd",
                            language:  'zh-CN',
                            autoclose: true,
                            todayBtn: true,
                            orientation: "top left"
                        });
                        $("input[name='workflowstages']").on("click",function (k, v) {
                            var draglen = $(".draggable-element").length;
                            var workflowstagesval = $(this).val();
                            console.log(workflowstagesval);
                            if($(this).is(":checked")){
                                draglen = 1+draglen;
                                stagename = $(this).data("stagename");
                                $("#elements-container").append('<div data-value="'+workflowstagesval+'" class="draggable-element d-'+draglen+' drag-'+workflowstagesval+'">'+stagename+'</div>');
                            }else{
                                $(".drag-"+workflowstagesval).remove();
                            }
                            $('.draggable-element').arrangeable();

                            $('li').arrangeable({dragSelector: '.drag-area'});
                        });
                    }else{
                        Vtiger_Helper_Js.showMessage({type:'error',text:data.result.msg});
                    }
                },
                function () {
                    window.location.reload();
                }
            );
        })
    },
    deleteRecordWorkFlow:function(){
        $('body').on('click','.deleteRecordButton',function(){
            var filterworkflowstageid=$(this).data('soncateworkflowid');
            var msg = {
                'message': '<h4>确定要删除工作流？</h4><hr>',
                'width': '800px',
            };
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function (e) {
                var params = {
                    'module': 'Item',
                    'action': 'ChangeAjax',
                    'mode': 'deleteRecordWorkFlow',
                    'filterworkflowstageid':filterworkflowstageid
                };
                AppConnector.request(params).then(
                    function (data) {
                        if(data.success){
                            window.location.reload(true);
                        }else{
                            Vtiger_Helper_Js.showMessage({type:'error',text:data.msg});
                        }
                    },
                    function () {
                        window.location.reload();
                    }
                );
            })
        })
    },
    registerEvents:function(){
        this._super();
        this.addComment();
        this.AddRStatement();
        this.updateRecordWorkFlow();
        this.deleteRecordWorkFlow();
    }
});
