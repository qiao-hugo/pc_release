/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("IronAccount_Edit_Js",{},{
    //加载业务类型
    ckEditorInstance:'',
    ckEInstance:'',
    rowSequenceHolder : false,
    registerReferenceSelectionEvent : function(container) {

        var thisInstance = this;
        jQuery('input[name="accountid"]', container).on(Vtiger_Edit_Js.referenceSelectionEvent, function (e,data){accountlist(data['source_module']);});
        function accountlist(sourcemodule){

            if(sourcemodule=='Accounts'){
                var Accountid=$('input[name="accountid"]');
                if(Accountid.val().length>0){
                    thisInstance.loadWidgetNote(Accountid.val());
                }

            }
        }
    },

    loadWidgetNote : function(Accountid){
        var accountid=Accountid;
        var params={};
        params['related_toID'] = accountid ;                  //公司的id
        params['module'] = 'IronAccount';
        params['action'] = 'ChangeAjax';
        var selectname='';//拼接字符串使用
        var departmentid='';
        var serviceid='';
        AppConnector.request(params).then(
            function(data){
                if(data.success==true){
                    var productsLth = data.result.product_list;
                    var arrproduct =[];
                    if(productsLth.length != 0){
                        $(productsLth).each(function(i,val){
                            arrproduct[i] = val.productid;
                        })
                        $('select[name="servicetype[]"] option').each(function(){
                            if($.inArray($(this).val(),arrproduct)!=-1){
                                $(this).attr('selected','selected');
                            }
                        })
                    }
                    //$('.chzn-select').chosen();
                    $(".chzn-select").trigger("liszt:updated");

                    if(data.result.departmentid == null){
                        departmentid = '无';
                    }else{
                        departmentid = data.result.departmentid;
                    }
                    if(data.result.serviceid == null){
                        serviceid = '无';
                    }else{
                        serviceid = data.result.serviceid;
                    }
                    selectname+='<br/><table class="table table-bordered blockContainer showInlineTable">';
                    selectname+='<tr><th class="blockHeader" colspan="4">客户信息</th></tr>';
                    selectname+='<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">所属部门</label></td><td class="fieldLabel medium"><span class="span10">'+departmentid+'</span></td><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">跟进客服</label></td><td class="fieldLabel medium"><span class="span10">'+serviceid+'</span></td></tr>';


                   /*selectname+='<tr><td class="fieldLabel medium"><label class="muted pull-right marginRight10px">业务类型</label></td><td class="fieldLabel medium">';
                    var productsLth = data.result.product_list;
                    if(productsLth.length != 0){
                        for( i = 0 ; i<productsLth.length;i++){
                            selectname+='&nbsp;<span><input type="hidden" name="productid[]" value="'+productsLth[i].productid+'"/>'+ productsLth[i].productname+'</span>;&nbsp;';
                        }
                    }else{
                        selectname+='&nbsp;<span><input type="hidden" name="productid[]" value="" />无</span>';
                    }
                    selectname+='</td></tr>';*/
                    selectname+='</table>';
                    $("#content_iron").html('');
                    $("#content_iron").append(selectname);
                }
            })

    },


    /**
     * 客服跟进进来时，自动读取客户，获取客户id
     */
    loadAccountid : function(){
        var thisInstance = this;
        var Hidden_Accountid=$('input[name="hidden_accountid"]');
        var Hidden_AccountName=$('input[name="hidden_accountname"]');
        if(Hidden_Accountid.val().length>0){
            $('input[name="accountid"]').val(Hidden_Accountid.val());
            $('input[name="accountid_display"]').val(Hidden_AccountName.val());
            thisInstance.loadWidgetNote(Hidden_Accountid.val());
        }
    },


    registerEvents: function(container){
        this._super(container);
        this.registerReferenceSelectionEvent(container);
        this.loadAccountid();
    }
});


