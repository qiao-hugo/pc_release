Vtiger_Edit_Js("Guarantee_Edit_Js",{ },{
    rowSequenceHolder: false,
    Cangumoney:10001,
	onchecksalesorderid:function(){
        var thisinstance=this;
        $('input[name="salesorderid"]').on('blur',function(){
            var instancethis=$(this);
            var salesorderno=$(this).val();
            if(salesorderno ==''){
                return ;
            }
            var params={
                "module":"Guarantee",
                "action":"BasicAjax",
                "mode":"getGuarantee",
                "salelesorder_no":salesorderno
            };
            thisinstance.rowSequenceHolder=false;
            thisinstance.Cangumoney=10001;
            $('input[name="submitflag"]').val('');
            AppConnector.request(params).then(function(data){
                if(data.result.flag=='yes'){
                    var guaranteetotal='可担保总金额:<font color="red">'+data.result.guaranteetotal+'</font><br>';//可担保总金额
                    var Guarantecurrentpay='已担保的总金额:<font color="red">'+data.result.Guarantecurrentpay+'</font><br>';//已担保的总金额
                    var receiveprice='该工单对应可用回款总额:<font color="red">'+data.result.receiveprice+'</font><br>';//已担保的总金额
                    var realprice='该工单对应总成本:<font color="red">'+data.result.realprice+'</font><br>';//总成本
                    var salesorderguarante='该工单已担保总金额:<font color="red">'+data.result.salesorderguarante+'</font><br>';//总成本
                    var Canguarantee=data.result.guaranteetotal-data.result.Guarantecurrentpay;
                    var Canguaranteedifference=data.result.realprice-data.result.receiveprice-data.result.salesorderguarante;
                    Canguaranteedifference=Canguaranteedifference>0?Canguaranteedifference:0;
                    var Canguaranteeamount='<font color="red">该工单需要提保的金额:'+Canguaranteedifference+'</font><br>';//可担保的金额
                    Canguaranteemoney=Canguarantee>0?(Canguaranteedifference>0?(Canguarantee-Canguaranteedifference>=0?Canguaranteedifference:Canguarantee):0):0;
                    instancethis.attr('data-title','注意');instancethis.attr('data-content',guaranteetotal+Guarantecurrentpay+receiveprice+realprice+salesorderguarante+Canguaranteeamount);instancethis.popover('show');
                    if(Canguarantee<=0){
                        var progressIndicatorElement = jQuery.progressIndicator({
                            'message' : '您没有担保金额无法进行操作,请关闭该窗口',
                            'position' : 'html',
                            'blockInfo' : {'enabled' : true}
                        });
                        $('input[name="total"]').attr('readonly','readonly');
                    }
                    if(Canguaranteemoney==0){
                        Vtiger_Helper_Js.showMessage({type:'error',text:'无法担保或不需要担保'});
                        //alert('无法担保或者不需要担保');
                        //$('input[name="total"]').attr('readonly','readonly');
                    }else{
                        $('input[name="total"]').val(Canguaranteemoney);
                        //$('input[name="total"]').attr('readonly','readonly');
                        thisinstance.rowSequenceHolder=true;
                        thisinstance.Cangumoney=Canguaranteemoney;
                        if($('input[name="submitflag"]').length){
                            $('input[name="submitflag"]').val('yes');
                        }else{
                            $('form').append('<input type="hidden" name="submitflag" value="yes">');
                        }

                    }
                }else if(data.result.flag=='no'){
                    instancethis.attr('data-title','注意');instancethis.attr('data-content','<font color="red">'+data.result.msg+'</font>');instancethis.popover('show');
                    /*var progressIndicatorElement = jQuery.progressIndicator({
                        'message' : '您没有担保金额无法进行操作,请关闭该窗口',
                        'position' : 'html',
                        'blockInfo' : {'enabled' : true}
                    });*/
                    //$('input[name="total"]').attr('readonly','readonly');
                }
            });

        });
    },
    /**
     * 格式化数字允许输入的位数
     * @param _this
     */
    checkremarkprice:function(_this){
        _this.val(_this.val().replace(/[^0-9.]/g,''));//只能输入数字小数点
        _this.val(_this.val().replace(/^0\d{1,}/g,''));//不能以0打头的整数
        _this.val(_this.val().replace(/^\./g,''));//不能以小数点打头
        _this.val(_this.val().replace(/[\d]{13,}/g,''));//超出长度全部清空
        _this.val(_this.val().replace(/\.\d{3,}$/g,''));//小数点后两位如果超过两位则将小数后的所有数清空包含小数点
    },
    //格式化输入只能转入数字或小数保留两位
    inputnumberchange : function(){
        var thisInstance=this;
        $('form').on('keyup','input[name="total"],',function(){
            thisInstance.checkremarkprice($(this));
            var arr=$(this).val().split('.');//只有一个小数点

            if(arr.length>2){
                if(arr[1]==''){
                    $(this).val(arr[0]);
                }else{
                    $(this).val(arr[0]+'.'+arr[1]);
                }
            }
            $(this).popover('destroy');
            if($(this).val()>thisInstance.Cangumoney){
                $(this).attr('data-title','注意');$(this).attr('data-content','担保过大');$(this).popover('show');
                $(this).val('')
            }
        }).on('blur','input[name="total"]',function(){  //失焦事件
            thisInstance.checkremarkprice($(this));
            var arr=$(this).val().split('.');//只有一个小数点当小数后没有数字时清除小数点
            if(arr.length>2){
                if(arr[1]==''){
                    $(this).val(arr[0]);
                }else{
                    $(this).val(arr[0]+'.'+arr[1]);
                }
            }else if(arr.length==2){
                //小数点后没有数字的则将小数点删除
                if(arr[1]==''){
                    $(this).val(arr[0]);
                }
            }
            $(this).popover('destroy');
            if($(this).val()>thisInstance.Cangumoney){
                $(this).attr('data-title','注意');$(this).attr('data-content','担保过大');$(this).popover('show');
                $(this).val('')
            }

        }).css("ime-mode", "disabled"); //CSS设置输入法不可用
    },
    registerResultEvent: function (form) {
        var thisInstance = this;
        if (typeof form == 'undefined') {
            form = this.getForm();
        }
        form.on(Vtiger_Edit_Js.recordPreSave, function (e, data) {
            if(!thisInstance.rowSequenceHolder||thisInstance.Cangumoney==10001||$('input[name="total"]').val()==0||$('input[name="total"]').val()>thisInstance.Cangumoney){
                Vtiger_Helper_Js.showMessage({type:'error',text:'无法提交请检查'});
                e.preventDefault();
            }
        })
    },
	registerBasicEvents : function(container) {
		this._super(container);
        this.onchecksalesorderid();
        this.registerResultEvent(container);
        this.inputnumberchange();
	}
});