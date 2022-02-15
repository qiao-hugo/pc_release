/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("PurchaseInvoice_Edit_Js",{},{
	
	registerReferenceSelectionEvent: function (container) {
        var thisInstance = this;

        //2015年4月24日 星期五 根据合同的客户负责人选择默认合同提单人 wangbin
        jQuery('input[name="suppliercontractsid"]', container).on(Vtiger_Edit_Js.referenceSelectionEvent, function (e, data) {
            relatedchange();
        });

        function relatedchange() {
            var sparams = {
                'module': 'PurchaseInvoice',
                'action': 'BasicAjax',
                'record': $('input[name="suppliercontractsid"]').val(),
                'mode': 'getvendorid'
            };
            AppConnector.request(sparams).then(
                function (datas) {
                    if (datas.success == true) {
                    	$('input[name=vendorid]').val(datas.result.vendorid);
                        $('input[name=vendorid_display]').val(datas.result.vendorname);
                    }
                }
            )
        }
    },
    // 
    amountofmoneyextend : function () {
        var thisInstance = this;
        $(document).on('blur','input[name="amountofmoney"]',function(){
            thisInstance.formatNumber($(this));
            thisInstance.makeTaxmoney();
        });
        $('select[name=purchasetaxrate]').change(function() {
            thisInstance.makeTaxmoney();
        });
    },

    makeTaxmoney : function () {
        var thisInstance = this;
        var amountofmoney = $('input[name="amountofmoney"]').val();
        if(parseFloat(amountofmoney)>0){
            var purchasetaxrate = $('select[name=purchasetaxrate]').val();
            var a = {'6%': 0.06, '17%': 0.17}; 
            if (a[purchasetaxrate]) {
                // 10000/1.06*0.06
                var taxmoney =  thisInstance.accMul( thisInstance.accDiv(amountofmoney, 1+ a[purchasetaxrate]), a[purchasetaxrate]);
                taxmoney = thisInstance.toDecimal(taxmoney);
                $('input[name=taxmoney]').val(taxmoney);
            }
        }
    },

    //保留两位小数  
    //功能：将浮点数四舍五入，取小数点后2位 
    toDecimal : function(x) { 
      var f = parseFloat(x); 
      if (isNaN(f)) { 
        return 0; 
      } 
      f = Math.round(x*100)/100; 
      return f; 
    }, 

    //格式化输入只能转入数字或小数保留两位
    formatNumber:function(_this){
        _this.val(_this.val().replace(/,/g,''));//只能输入数字小数点
        _this.val(_this.val().replace(/[^0-9.]/g,''));//只能输入数字小数点
        _this.val(_this.val().replace(/^0\d{1,}/g,''));//不能以0打头的整数
        _this.val(_this.val().replace(/^\./g,''));//不能以小数点打头
        _this.val(_this.val().replace(/[\d]{13,}/g,''));//超出长度全部清空
        _this.val(_this.val().replace(/\.\d{3,}$/g,''));//小数点后两位如果超过两位则将小数后的所有数清空包含小数点
    },

    /**
     * 除法运算相除JS问题
     * @param arg1除数
     * @param arg2被除数
     * @returns {number}
     */
    accDiv:function(arg1,arg2){
        var t1=0,t2=0,r1,r2;
        try{t1=arg1.toString().split(".")[1].length}catch(e){}
        try{t2=arg2.toString().split(".")[1].length}catch(e){}
        with(Math){
            r1=Number(arg1.toString().replace(".",""))
            r2=Number(arg2.toString().replace(".",""))
            return (r1/r2)*pow(10,t2-t1);
        }
    },

    //乘法运算解决Js相乘的问题
    accMul:function(arg1,arg2){
        var m=0,s1=arg1.toString(),s2=arg2.toString();
        try{m+=s1.split(".")[1].length}catch(e){}
        try{m+=s2.split(".")[1].length}catch(e){}
        return Number(s1.replace(".",""))*Number(s2.replace(".",""))/Math.pow(10,m)
    },

    //加法运算,解决JS浮点数问题
    accAdd:function (arg1,arg2){
        var r1,r2,m;
        try{r1=arg1.toString().split(".")[1].length}catch(e){r1=0}
        try{r2=arg2.toString().split(".")[1].length}catch(e){r2=0}
        m=Math.pow(10,Math.max(r1,r2))
        var s=(arg1*m+arg2*m)/m;
        if(isNaN(s)){
            s=0;
        }
        return s;
    },

	init : function() {
		$('#PurchaseInvoice_editView_fieldName_issubmit').attr('checked', 'checked').attr('disabled', 'disabled');
	},

	registerEvents: function(){
		this._super();
		this.init();
        this.amountofmoneyextend();
		this.registerReferenceSelectionEvent();
	}
});


