/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("Medium_Edit_Js",{},{
	ckEditorInstance:'',
	ckEInstance:'',
	rowSequenceHolder : false,
	registerReferenceSelectionEvent : function(container) {
		this._super(container);
		var thisInstance = this;
		//wangbin 2015-1-13 修改之前拜访单关联列表,input获取name值有所变化.
	},

	additem:function(classname){
        var numd=$('.'+classname).length+1;
        if(numd>20){return;}/*超过20个不允许添加*/
        var nowdnum=$('.'+classname).last().data('num');
        if(nowdnum!=undefined){
            numd=nowdnum+1;
        }
        //var addname=d;
        var extend=insertdata[classname].replace(/\[\]|replaceyes/g,'['+numd+']');
        extend=extend.replace(/yesreplace/g,numd);
        $('#insert'+classname).before(extend);
        $('#cadsrecentmaintenancetime'+numd).datepicker({
            format: "yyyy-mm-dd",
            language:  'zh-CN',
            autoclose: true,
            pickerPosition: "bottom-left",
            showMeridian: 0
        });
	},
    //添加厂商信息
    addItemEvent:function(){
        var thisInstance = this;
        $('#addfirmpolicy').on('click',function(){
            thisInstance.additem('firmpolicy');
        });
        $('#addcadsname').on('click',function(){
            thisInstance.additem('cadsname');
        });
    },
    //删除信息
    deleteitem:function(){
        $('#EditView').on('click','.delbutton',function(){
            var message='确定要删除吗？';
            var msg={
                'message':message
            };
            var thisstance=$(this);
            var category=thisstance.data('category');
            Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){
                thisstance.parents('.'+category).remove();
            },function(error, err) {});
        });
    },
	/**
		pop two calander.
	*/
	registerBasicEvents:function(container){
		this._super(container);
		this.registerReferenceSelectionEvent(container);
		this.addItemEvent();
		this.deleteitem();
	    //$("#VisitingOrder_editView_fieldName_enddate").val(endtime);
	}
});




















