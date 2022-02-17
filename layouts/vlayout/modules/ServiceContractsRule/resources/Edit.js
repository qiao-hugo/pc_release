Vtiger_Edit_Js("ServiceContractsRule_Edit_Js",{ },{
    spellnumber:function(){
        var mydate = new Date();
        var Y=mydate.getFullYear();
        var M=mydate.getMonth()+1;
        if(M<10){
            M="0"+M;
        }
        var D=mydate.getDate();
        if(D<10){
            D="0"+D;
        }
        $("input[name='prefix'],select[name='interval_code'],input[name='year_code']").on('change',function(){
             var prefix=$('input[name="prefix"]').val();
             var interval_code=$('select[name="interval_code"]').val();
             var company_code=$('select[name="company_code"]').val();
             var products_code=$('select[name="products_code"]').val();
             var year_code=$('input[name="year_code"]').is(":checked");
             var month_code=$('input[name="month_code"]').is(":checked");
             var day_code=$('input[name="day_code"]').is(":checked");
             var number = $("input[name='number']").validationEngine('validate');

            var specil=prefix+interval_code+company_code+products_code;
            if(year_code){specil+=Y;}
            if(month_code){specil+=M;}
            if(day_code){specil+=D;}
            console.log(specil);
        })
    },
	registerBasicEvents : function(container) {
        this._super(container);
        this.spellnumber();
	}
});