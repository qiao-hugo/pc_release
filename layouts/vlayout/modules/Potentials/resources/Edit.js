/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("Potentials_Edit_Js",{},{
    ckEditorInstance:'',
    ckEInstance:'',
    rowSequenceHolder : false,
    registerReferenceSelectionEvent : function(container) {
        this._super(container);
        var thisInstance = this;
        //wangbin 2015-1-13 修改之前拜访单关联列表,input获取name值有所变化.
        jQuery('input[name="related_to"]', container).on(Vtiger_Edit_Js.referenceSelectionEvent, function (e,data){accountlist(data['source_module']);});
        function accountlist(sourcemodule){
            //没有找到通过数据库来添加readyonly属性动态给它添加一个
            $('#Potentials_editView_fieldName_contact_id').attr("readonly","readonly");
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
        params['module'] = 'Potentials';
        params['action'] = 'SaveAjax';
        params['mode'] = 'autofillpotentials';
        AppConnector.request(params).then(
            function(data){

                if(data.success==true){
//						address=data.result[0].address;
//						if(address !==null){
//							re=new RegExp("#","g");
//							var	address=address.replace(re,"");
//							$('#Potentials_editView_fieldName_destination').val(address);					
//						}

                    contact=data.result;
                    $("select#contactorselect").remove();
                    if(data.result.length!==0){
                        if(contact.length==1){
                            $('#Potentials_editView_fieldName_contact_id').val(contact[0].name);
                        }else{

                            var str="";
                            $.each(contact,function(n,value){
                                str += "<option value="+value.name+'>'+value.name+"</option>";
                            })

                            newstr = "<select id='contactorselect'>"+str+"</select>"
                            $('#Potentials_editView_fieldName_contact_id').closest('span').append(newstr);
                            $("#contactorselect").on('change',function(){
                                $('#Potentials_editView_fieldName_contact_id').val($(this).val());
                            })
                            //$("#contactorselect option:first").attr('selected','selected');
                            $('#Potentials_editView_fieldName_contact_id').val($("#contactorselect option:first").val());
                            ;}
                    }
                }
            })
    },
    //wangbin 添加销售机会关联可能性;
    sales_stage:function (){
        var vale = '';
        $("select[name='sales_stage']").on('change',function(){
            if($(this).val()=="The imminent signing cooperation"){
                vale = "80";
            }else if($(this).val()=="Solve doubts stage"){
                vale = "40";
            }else if($(this).val()=="Clear the requirements phase"){
                vale = "60";
            }else if($(this).val()=="The customer contact stage"){
                vale = "20";
            }else{
                vale = "0";
            };
            $('#Potentials_editView_fieldName_probability').val(vale);
        })
    },
    formatNumber:function(_this){
        _this.val(_this.val().replace(/,/g,''));//只能输入数字小数点
        _this.val(_this.val().replace(/[^0-9.]/g,''));//只能输入数字小数点
        _this.val(_this.val().replace(/^0\d{1,}/g,''));//不能以0打头的整数
        _this.val(_this.val().replace(/^\./g,''));//不能以小数点打头
        _this.val(_this.val().replace(/[\d]{13,}/g,''));//超出长度全部清空
        _this.val(_this.val().replace(/\.\d{3,}$/g,''));//小数点后两位如果超过两位则将小数后的所有数清空包含小数点
        _this.val(_this.val().replace(/\.\d*\.$/g,''));//小数点后两位如果超过两位则将小数后的所有数清空包含小数点
    },
    inputnumberchange : function(){
        var thisInstance=this;
        $('#EditView').on("keyup",'.checknumber',function(){
            thisInstance.formatNumber($(this));
            var arr=$(this).val().split('.');//只有一个小数点
            if(arr.length>2){
                if(arr[1]==''){
                    $(this).val(arr[0]);
                }else{
                    $(this).val(arr[0]+'.'+arr[1]);
                }
            }
        }).on("blur",'.checknumber',function(){  //CTR+V事件处理
            $(this).trigger("keyup");
        }).on('paste','.checknumber',function(e){
            $(this).trigger("keyup");
        }); //CSS设置输入法不可用

    },
    /**
     * 新增销售明细  以及对其的 删除某条操作
     * @param container
     */
    addOrDelDetailInfo:function(container){
        /**
         * 如果是编辑 则 此时start
         * @type {*|jQuery}
         */

            // 编辑时初化日期插件
        var lengthDetail =  $("input[name='countDetail']").val();
        if(lengthDetail>0){
            var i=0;
            for (i=0;i< lengthDetail ;i++){
                var  start=$("#budgetlockstart"+i);
                var  end =$("#budgetlockend"+i);
                initDateTime(start,end);
            }
        }else{
            var  start=$("#budgetlockstart0");
            var  end =$("#budgetlockend0");
            initDateTime(start,end);
        }
        //isannuallypay 是否按年付费设置隐藏域内容
        $('body').on('click',".addfallinto",function(){
            //获取添加的 detailInfo 个数
            var detailNumber=parseInt($("#detatilNumber").val())+1;
            //更新添加按钮 为删除按钮操作
            var insertInfo = appendDetailInfo.replace("icon-plus","icon-trash");
            insertInfo = insertInfo.replace("addfallinto","delDetailInfo");
            insertInfo = insertInfo.replace("detailNumbers",detailNumber);
            //确定添加的日期插件的id  开始和结束日期
            var budgetlockstartId = "budgetlockstart"+detailNumber;
            var budgetlockendId   = "budgetlockend"+detailNumber;
            // 添加对应的日期插件id
            insertInfo = insertInfo.replace("budgetlockstart",budgetlockstartId);
            insertInfo = insertInfo.replace("budgetlockend",budgetlockendId);
            $('#insertbefore').before(insertInfo);
            //初始化新增的日期插件
            var  start=$("#budgetlockstart"+detailNumber);
            var  end =$("#budgetlockend"+detailNumber);
            initDateTime(start,end);
            $("#detatilNumber").val(detailNumber);
            $('.chzn-select').chosen();
        });
        // isannuallypay 是否按年付费设置隐藏域内容
        $('body').on('click',".isannuallypay",function(){
            if($(this).is(':checked')){
                $(this).next().val(1);
            }else{
                $(this).next().val(0);
            }
        });
        //新增点击事件
        $('#addfallinto').on('click',function(){
            // 获取添加的 detailInfo 个数
            var detailNumber=parseInt($("#detatilNumber").val())+1;
            //更新添加按钮 为删除按钮操作
            var insertInfo = appendDetailInfo.replace("icon-plus","icon-trash");
            insertInfo = insertInfo.replace("addfallinto","delDetailInfo");
            insertInfo = insertInfo.replace("detailNumbers",detailNumber);
            //确定添加的日期插件的id  开始和结束日期
            var budgetlockstartId = "budgetlockstart"+detailNumber;
            var budgetlockendId   = "budgetlockend"+detailNumber;
            // 添加对应的日期插件id
            insertInfo = insertInfo.replace("budgetlockstart",budgetlockstartId);
            insertInfo = insertInfo.replace("budgetlockend",budgetlockendId);
            $('#insertbefore').before(insertInfo);
            //初始化新增的日期插件
            var  start=$("#budgetlockstart"+detailNumber);
            var  end =$("#budgetlockend"+detailNumber);
            initDateTime(start,end);
            $("#detatilNumber").val(detailNumber);
            console.log("what");

        });
        //删除点击事件
        $('body').on('click',".delDetailInfo",function(){
            var potentialdetailid= $(this).attr("data-potentialdetailid");
            var message='确定要删除吗？';
            var msg={
                'message':message
            };
            var thisstance=$(this);
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                // 说明是更新
                if(potentialdetailid>0){
                    var params = {
                        'module': 'Potentials',
                        'action': 'BasicAjax',
                        'potentialdetailid': potentialdetailid,
                        'mode': 'delPotentialDetailOne'
                    };
                    AppConnector.request(params).then(
                        function (data) {
                            if(data.success){
                                thisstance.parents('.plusTableContent').remove();
                            }
                        }
                    )
                }else{
                    thisstance.parents('.plusTableContent').remove();
                }
            },function(error, err) {});
        });
        // 是开始时间判断 日期大小
        function startTimeCtr(){

            var startTime = $(this).val();
            console.log(startTime);
            var endTime = $(this).parents(".input-append").next().find("input").val();
            if(startTime < endTime && endTime!=''){
            } else if(endTime!='') {
                $(this).val("");
                var message='开始日期应小于结束日期！';
                var msg={
                    'message':message
                };
                Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                },function(error, err) {});
            }
        }

        // 是结束日期判断日期大小
        function endTimeCtr(){
            var endTime = $(this).val();
            var startTime = $(this).parents(".input-append").prev().find("input").val();
            if(startTime<endTime && startTime!=''){
            }else if(startTime!='') {
                $(this).val("");
                var message='结束日期应大于开始日期！';
                var msg={
                    'message':message
                };
                Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                },function(error, err) {});
            }
        }

        // 初始化日期插件
        function initDateTime(start,end){
            start.datetimepicker({
                format: "yyyy-mm-dd",
                language:  'zh-CN',
                autoclose: true,
                //todayBtn: true,
                pickerPosition: "bottom-right",
                showMeridian: 0,
                //endDate:new Date(),
                weekStart:1,
                todayHighlight:1,
                startView:2,
                minView:2,
                forceParse:0,
                onSelect:startTimeCtr,
            }).on('changeDate',startTimeCtr);
            end.datetimepicker({
                format: "yyyy-mm-dd",
                language:  'zh-CN',
                autoclose: true,
                //todayBtn: true,
                pickerPosition: "bottom-right",
                showMeridian: 0,
                //endDate:new Date(),
                weekStart:1,
                todayHighlight:1,
                startView:2,
                minView:2,
                forceParse:0,
                onSelect:endTimeCtr,
            }).on('changeDate',endTimeCtr);

        }

    },
    registerBasicEvents:function(container){
        this._super(container);
        this.registerReferenceSelectionEvent(container);
        $('#Potentials_editView_fieldName_contact_id').attr("readonly","readonly");
        this.loadWidgetNote($('input[name="related_to"]').val());
        this.sales_stage();
        this.addOrDelDetailInfo(container);
        this.inputnumberchange();
    },
});




















