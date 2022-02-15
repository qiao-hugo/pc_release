/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("ProductProvider_Edit_Js",{},{
	ckEditorInstance:'',
	ckEInstance:'',
	rowSequenceHolder : false,
    proudctList:{},
    seletedIndexValue:0,
    seletedValue:0,
    registerRecordPreSaveEvent : function(form) {
        var editViewForm = this.getForm();
        var thisInstance = this;
        if(typeof form == 'undefined') {
            form = this.getForm();
        }
        editViewForm.on(Vtiger_Edit_Js.recordPreSave,function(e,data){
            // 不同的票据类型 判断是否为空
            var servicestartdate = $('input[name=servicestartdate]').val();
            var serviceenddate = $('input[name=serviceenddate]').val();
            /*if ((new Date(servicestartdate.replace(/-/g,'\/')))>(new Date(serviceenddate.replace(/-/g,'\/')))) {
				var  params = {text : app.vtranslate(),title : app.vtranslate('产品服务有效开始日期不能大于账户有效结束日期')};
				Vtiger_Helper_Js.showPnotify(params);
				e.preventDefault();
				return false;
            }*/

           	var productid=$('input[name="productid"]').val();
           	var vendorid=$('input[name="vendorid"]').val();
           	var idaccount=$('input[name="idaccount"]').val();
			var module = app.getModuleName();
			var postData={};
			postData.data = {
				"module": module,
				"action": "ChangeAjax",
				'mode': 'checkIdAndProductProvider',
				'record': $('input[name=record]').val(),
				"productid": productid,
				"vendorid": vendorid,
				"idaccount":idaccount
			}
			postData.async=false;
			var ajaxflag=false;
			var Message = app.vtranslate('正在验证...');
			var progressIndicatorElement = jQuery.progressIndicator({
				'message' : Message,
				'position' : 'html',
				'blockInfo' : {'enabled' : true}
			});
			AppConnector.request(postData).then(
				function(data){
					progressIndicatorElement.progressIndicator({'mode' : 'hide'});
					if(data.success) {
                        if(data.result.flag){
                            Vtiger_Helper_Js.showMessage({type:'error',text:'账号ID存在,不充许添加!'});
                            ajaxflag=true;
                        }
					}
				},
				function(error,err){

				}
			);
            if(ajaxflag){
                e.preventDefault();
                return false;
            }
        });
    },

    registerReferenceSelectionEvent : function(container) {
        this._super(container);
        var thisInstance = this;
        //wangbin 2015-1-13 修改之前拜访单关联列表,input获取name值有所变化.
        jQuery('input[name="productid"]', container).on(Vtiger_Edit_Js.referenceSelectionEvent, function (e,data){accountlist(data['source_module']);});
        function accountlist(sourcemodule){
            if(sourcemodule=='Products'){
                var productid=$('input[name="productid"]');
                thisInstance.loadWidgetNote(productid.val(),1);
            }
        }
    },
    loadWidgetNote : function(id,flag){
        var thisInstance=this;
        var params={};
        params['productid'] =id ;                  //公司的id
        params['module'] = 'ProductProvider';
        params['action'] = 'ChangeAjax';
        params['mode'] = 'getVendorInfo';
        $('#inserttable').empty();
        if(flag==1){
            thisInstance.clearVendorData();
        }
        AppConnector.request(params).then(
            function(data){
                if(data.success==true){
                    if(data.result.countnum>0){
                        if(flag==1){
                            thisInstance.setVendorData(data.result.data[0]);
                        }
                        if(data.result.countnum>1){
                            thisInstance.proudctList=data.result.data;
                            var str='<table id="vdatatable" class="table table-bordered blockContainer newinvoicerayment_tab detailview-table">'+
                                '<thead><tr><td><span style="color:red;">点选更改产合同供应商</span> </td><td><label class="muted">供应商名称</label></td>'+
                                '<td><label class="muted"><span class="redColor">*</span> 供应商返点</label></td><td><label class="muted"><span class="redColor">*</span>采购合同</label></td><td><label class="muted"><span class="redColor">*</span>返点类型</label></td></tr></thead><tbody>';
                            $.each(thisInstance.proudctList,function(key,value){
                                str+='<tr class="selectvendor" data-key="'+key+'" style="cursor:pointer;" title="双击选择"><td><input type="radio" name="readioselect" class="radioselect radios'+key+'" value="1"/></td><td><div class="row-fluid"><span class="span10">'+value.vendorname+'</span></div></td>'+
                                    '<td><div class="row-fluid"><span class="span10">'+value.rebate+'</span></div></td>'+
                                    '<td><div class="row-fluid"><span class="span10">'+value.contract_no+'</span></div></td>'+
                                    '<td><div class="row-fluid"><span class="span10">'+value.rebatetypename+'</span></div></td></tr>';
                            });
                            str+='</tbody></table>';
                            $('#inserttable').append(str);
                            var table = jQuery('#vdatatable').DataTable({
                                language: {"sProcessing":   "处理中...",	"sLengthMenu":   "显示 _MENU_ 项结果","sZeroRecords":  "没有匹配结果","sInfo":         "显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",	"sInfoEmpty":    "显示第 0 至 0 项结果，共 0 项","sInfoFiltered": "(由 _MAX_ 项结果过滤)","sInfoPostFix":  "","sSearch":"当前页快速检索:","sUrl":"","sEmptyTable":     "表中数据为空","sLoadingRecords": "载入中...",
                                    "sInfoThousands":  ",",	"oPaginate": {"sFirst":"首页","sPrevious": "上页","sNext":"下页","sLast":"末页"},
                                    "oAria": {"sSortAscending":  ": 以升序排列此列","sSortDescending": ": 以降序排列此列"}},
                                scrollY:"300px",
                                sScrollX:"disabled",
                                aLengthMenu: [ 10, 20, 50, 100, ],
                                fnDrawCallback:function(){

                                }
                            });
                        }
                    }

                }
            });
    },
    selectKeyChange:function(){
        var thisInstance=this;
        $('#EditView').on('click','.selectvendor',function(){
            var key=$(this).data("key");
            var message = app.vtranslate('确定选用该供应商吗?');
            Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
                function(e) {
                    $('.radioselect').removeAttr('checked');
                    $('.radios'+key).attr('checked','checked');
                    thisInstance.setVendorData(thisInstance.proudctList[key]);
                });
        });
    },
    instanceLoading:function(){
        var thisInstance=this;
        $('input[name="vendorid_display"]').prev('span').remove();
        $('input[name="vendorid_display"]').next('span').remove();
        $('input[name="vendorid_display"]').attr('readonly','readonly');
        $('input[name="suppliercontractsid_display"]').prev('span').remove();
        $('input[name="suppliercontractsid_display"]').next('span').remove();
        $('input[name="suppliercontractsid_display"]').attr('readonly','readonly');
        /*$('input[name="supplierrebate"]').attr('readonly','readonly');*/
        var recordid=$('input[name="record"]').val();
        if(recordid>0){
            var productid=$('input[name="productid"]').val();
            thisInstance.loadWidgetNote(productid,0);
        }
        $('#EditView').on('click','.chzn-container',function(){
            var thisId=$(this).attr('id');
            var thisIdSelect=thisId.split('_');
            thisInstance.seletedIndexValue=$('#'+thisIdSelect[0])[0].selectedIndex;
            thisInstance.seletedValue=$('#'+thisIdSelect[0]).val();
        });
        $('#EditView').on('change','select[name="rebatetype"]',function(){
            this.selectedIndex=thisInstance.seletedIndexValue;
            $(this).trigger('liszt:updated');
            var thisid=$(this).attr('id');
            $('#'+thisid+'_chzn').find('.chzn-drop').remove();
    });
    },
    setVendorData:function(Obj){
        console.log(Obj);
        $('input[name="vendorid"]').val(Obj.vendorid);
        $('input[name="vendorid_display"]').val(Obj.vendorname);
        $('input[name="suppliercontractsid"]').val(Obj.suppliercontractsid);
        $('input[name="suppliercontractsid_display"]').val(Obj.contract_no);
        $('input[name="supplierrebate"]').val(Obj.rebate);
        $('input[name="servicestartdate"]').val(Obj.effectdate);
        $('input[name="serviceenddate"]').val(Obj.enddate);
        $('select[name="rebatetype"]').val(Obj.rebatetype);
        $('select[name="rebatetype"]').trigger('liszt:updated');
    },
    clearVendorData:function(){
        $('input[name="vendorid"]').val('');
        $('input[name="vendorid_display"]').val('');
        $('input[name="suppliercontractsid"]').val('');
        $('input[name="suppliercontractsid_display"]').val('');
        $('input[name="supplierrebate"]').val('');
        $('input[name="servicestartdate"]').val('');
        $('input[name="serviceenddate"]').val('');
    },
    appendAccountDetail:function(){
	    $('body').on('blur','.idaccount',function () {
	        var thisTr=$(this).closest("tr");
            var idaccount=$(this).val();
            if(idaccount.length>0){
            }else{
                Vtiger_Helper_Js.showMessage({type:'error',text:'当前ID账号不允许为空!!'});
                return false;
            }
            var i=0;
            console.log(123);
            $("input[name='idaccount[]']").each(function () {
                  if($(this).val()==idaccount){
                      i=i+1;
                  }
            });
            if(i>1){
                Vtiger_Helper_Js.showMessage({type:'error',text:'当前ID,账号重复不允许添加!!'});
                return false;
            }
            var module = app.getModuleName();
            var postData={};
            postData.data = {
                "module": module,
                "action": "ChangeAjax",
                'mode': 'checkRepate',
                'record': $('input[name=record]').val(),
                "idaccount":idaccount
            }
            postData.async=false;
            AppConnector.request(postData).then(
                function(data){
                    if(data.success) {
                        if(data.result.message){
                            Vtiger_Helper_Js.showMessage({type:'error',text:data.result.message});
                            thisTr.find("#idaccount").val('');
                        }
                    }
                },
                function(error,err){
                }
            );

        });

	    /*$("input[name='idaccount[]']").each(function () {
            
        });*/
	    // 添加
        $("#appendAccountDetail").click(function () {
             $('#accountDetail').append(appendAccountDetail);
        });
        $('body').on('keyup','.idaccount,.accountzh',function () {
              var thisStr=$(this).closest("tr");
              var oldidaccount=thisStr.data("idaccount");
              var oldaccountzh=thisStr.data("accountzh");
              var nowidaccount=thisStr.find("#idaccount").val();
              var nowaccountzh=thisStr.find("#accountzh").val();
              console.log(oldidaccount);
              console.log(oldaccountzh);
              console.log(nowidaccount);
              console.log(nowaccountzh);
              if( (oldidaccount!=nowidaccount || oldaccountzh!=nowaccountzh) && oldidaccount!=undefined && oldaccountzh!=undefined){
                  thisStr.find("#updateStatus").val(1);
              }else{
                  thisStr.find("#updateStatus").val(0);
              }
        })
        // 删除
        $("#accountDetail").on('click','.deleteRecordButton',function () {
            var thisTr=$(this).closest("tr");

            var id=thisTr.data("id");
            if(id){
                var module=app.getModuleName();
                var postData= {
                    "module": module,
                    "action": "ChangeAjax",
                    'mode': 'deleteDetailOne',
                    'record': $('input[name=record]').val(),
                    "id": id
                }
                var Message = app.vtranslate('正在处理中...');
                var progressIndicatorElement = jQuery.progressIndicator({
                    'message' : Message,
                    'position' : 'html',
                    'blockInfo' : {'enabled' : true}
                });
                AppConnector.request(postData).then(
                    function(data){
                        progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                        console.log(data);
                        if(data.success) {
                            thisTr.remove();
                        }else{
                            Vtiger_Helper_Js.showMessage({type:'error',text:'删除失败!'});
                        }
                    },
                    function(error,err){

                    }
                );
            }else{
                thisTr.remove();
            }
        });
    },
	/**
		pop two calander.
	*/
	registerBasicEvents:function(container){
		this._super(container);
		this.registerRecordPreSaveEvent(container);
        this.registerReferenceSelectionEvent(container);
        this.selectKeyChange();
        this.instanceLoading();
        this.appendAccountDetail();
	}
});




















