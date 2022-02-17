/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("IdcRecords_Edit_Js",{},{
    ckEditorInstance:'',
    ckEInstance:'',
    rowSequenceHolder : false,
    registerReferenceSelectionEvent : function(container) {
        this._super(container);
        var thisInstance = this;
        /*jQuery('input[name="related_to"]', container).on(Vtiger_Edit_Js.referenceSelectionEvent, function (e,data){accountlist(data['source_module']);});
            function accountlist(sourcemodule){
                if(sourcemodule=='Accounts'){
                    var Accountid=$('input[name="related_to"]');
                    var Hidden_salesorder_no=$('input[name="hidden_salesorder_no"]');
                    if(Accountid.val().length>0){
                        thisInstance.loadWidgetNote(Accountid.val(),Hidden_salesorder_no.val());
                    }
                }
        }*/
        jQuery('input[name="salesorder_no"]', container).on(Vtiger_Edit_Js.referenceSelectionEvent, function (e,data){salesorderchange(data);});
        function salesorderchange(data){
           if(data['source_module'] == 'SalesOrder' && data['record'].length>0){
               thisInstance.loadRelated_assigned_user(data['record']);
           }
        }
    },
    /*wangbin 2015年9月18日 修改工单时，自动关联负责人跟客户
    */

    loadRelated_assigned_user : function(salesorderid){
        var params = {};
        params['salesorderid'] = salesorderid;
        params['module'] = 'IdcRecords';
        params['action'] = 'ChangeAjax';
        params['mode'] = 'get_salesorder_relate';
        AppConnector.request(params).then(
            function(data){
                if(data.success==true && data.result){
                    //console.log(data.result);
                    $('select[name="assigned_user_id"]').val(data.result.smcreatorid);
                    $('select[name="assigned_user_id"]').trigger('liszt:updated');
                    $('input[name="related_to"]').val(data.result.crmid);
                    $('input[name="related_to_display"]').val(data.result.label);
                }
            })
    },

    relation_salesorder : function(){
      var record = $("input[name='record']").val();
      var salesorderid = $("input[name='salesorder_no']").val();
      if(record == "" && salesorderid){
          this.loadRelated_assigned_user(salesorderid);
      }
    },

    /**
     * 点击或自动加载 获取负责人及工单编号
     * @param id 公司的id
     * @param hidden_salesorder_no 编辑状态下，隐藏的工单编号
     */
    loadWidgetNote : function(id,hidden_salesorder_no){
        var accountid=id;
        var params={};
        params['accountid'] = accountid ;                  //公司的id
        params['module'] = 'IdcRecords';
        params['action'] = 'ChangeAjax';
        params['mode'] = 'autoIdcRecordsaccount';
        AppConnector.request(params).then(
            function(data){
                if(data.success==true){
                   var smcreatorid=data.result[0].smcreatorid;   //获取负责人
                    if(smcreatorid !== null){
                        $('select[name="assigned_user_id"] option').each(function(){
                            if($(this).val() == smcreatorid){
                               smcreatorid = $(this).val();
                            }
                        });
                        $('select[name="assigned_user_id"]').val(smcreatorid).attr("selected",true);  //设置选中的值
                        var selVal=$('select[name="assigned_user_id"]').find('option:selected').text();
                        $('select[name="assigned_user_id"]').next().find('span').text(selVal);         //页面显示选中的值
                    }
                   /* //console.log(data);
                    var SalesorderNo=data.result[1];          //获取工单
                    var Selsalesorder_no='<td class="fieldLabel medium"><select class="chzn-select" name="salesorder_no" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"> <option value="">选择一个选项</option>';
                    if (data.result[1] == null || data.result[1].length == 0) {
                        Selsalesorder_no+='</select></td>';
                        $('select[name="salesorder_no"]').closest("td").replaceWith(Selsalesorder_no);
                        $('.chzn-select').chosen();
                    }else{
                        var option='';
                        $.each(SalesorderNo,function(i,value){
                            if(hidden_salesorder_no == value.salesorder_no){
                                hidden_salesorder_noValue = value.salesorder_no;
                            }
                            option+='<option value='+value.salesorder_no+'>'+value.salesorder_no+'</optio>';
                        });
                        Selsalesorder_no+=option+'</select></td>';
                        $('select[name="salesorder_no"]').closest("td").replaceWith(Selsalesorder_no);
                        $('.chzn-select').chosen();

                        $('select[name="salesorder_no"]').val(hidden_salesorder_noValue).attr("selected",true);  //设置选中的值
                        var selValsalesorder_no=$('select[name="salesorder_no"]').find('option:selected').text();
                        $('select[name="salesorder_no"]').next().find('span').text(selValsalesorder_no);         //页面显示选中的值

                    }*/

                }
            })
    },
    /**
     * 编辑情况下，自动读取工单，获取客户id
     */
    loadAccountid : function(){
        var thisInstance = this;
        var Accountid=$('input[name="related_to"]');
        var Hidden_salesorder_no=$('input[name="hidden_salesorder_no"]');
        if(Accountid.val().length>0){
            thisInstance.loadWidgetNote(Accountid.val(),Hidden_salesorder_no.val());
           // console.log(Hidden_salesorder_no.val());
        }
    },

    /**
     * 自定义备案信息
     * 类型读取：国内国外
     */
    registerTypeEvents : function () {
        jQuery('select[name="idctype"]', this.getForm()).on('change', function(e){
            var idctypeElement = jQuery(e.currentTarget);
            if (idctypeElement.val() == 'china'){
                jQuery('#blockContainer_LBL_IDCRECORDS_INFORMATION').removeClass('hide').removeClass('tableadv');
            }else{
                jQuery('#blockContainer_LBL_IDCRECORDS_INFORMATION').addClass('hide').addClass('tableadv');
            }
        });
    },
    /**
     * 自定义多行文本框 域名
     */
    //registerTextareaName : function () {
    //    $('textarea[name="domainname"]').removeClass('span11');
    //    $('textarea[name="domainname"]').addClass('span6').height(80);
    //
    //},
    /**
     * 自动加载工单编号下拉
     */

    //2015年9月18日 wangbin 注释,客服任务包关联idc节点相关修改
  /*  LoadSalesorderNo :function () {
        var Selsalesorder_no='<td class="fieldLabel medium">' +
            '<select class="chzn-select" name="salesorder_no" data-validation-engine="validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]">' +
            ' <option value="">选择一个选项</option></select></td>';
        $('input[name="salesorder_no"]').closest("td").replaceWith(Selsalesorder_no);
        $('.chzn-select').chosen();
    },*/

    /**
     pop two calander.
     */


    registerBasicEvents:function(container){
        this._super(container);
        this.registerReferenceSelectionEvent(container);
        this.loadAccountid();
        this.registerTypeEvents();
        this.relation_salesorder();
       // this.LoadSalesorderNo();
        //this.registerTextareaName();

        //$("#VisitingOrder_editView_fieldName_enddate").val(endtime);
    }
});




















