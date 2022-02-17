/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("IdcRecords_List_Js",{},{

    /**
     * 必须用live，用on时，分页情况下，不加载；
     */
	registerOnclickEvent:function(){
        //全选
        $("#checkall").live('click',function(){
            $(":checkbox").each(function(){
                this.checked = true;
            });
        });
        //全不选
        $("#checkallNo").live('click',function(){
            $(":checkbox").each(function(){
                this.checked = false;
            })
        });
        //反选
        $("#check_revsern").live('click',function(){
            $(":checkbox").each(function(){
                if (this.checked) {
                    this.checked = false;
                }
                else {
                    this.checked = true;
                }
            });
        });

        //编辑 点击状态显示，设置指定相对位置
        $('#ListviewEdit').live('click',function(){

            var butLeft = $("#ListviewEdit").offset().left-32; //坐标x轴
            //var butTop = $("#ListviewEdit").offset().top;
            var ListliF =  $(".ListViewItem").find('.ListliF').length;
            $('#stateF').toggle().css("position","relative").css("left",butLeft).css("bottom",parseInt(ListliF+1)*32-ListliF); //坐标y轴
        })
        //鼠标移入父级节点，指定孩子相对位置
        $('.ListliF').live({
            mouseenter: function() {
                var butLeft = $("#ListviewEdit").offset().left-62;  //X轴
                //指定从上往下显示
               //$(this).children("ul").show(500).css("position","relative").css("left",butLeft).css("bottom",30);
                //指定从下往上显示
                $(this).children("ul").show(500).css("position","absolute").css("left",butLeft).css("bottom",0);
                $(this).children().children("span").show(); //右三角
            },
            mouseleave: function() {
                $(this).children("ul").hide();
                $(this).children().children("span").hide();//右三角
            }
        })

        //移入移除更换背景色
         $('.ListViewItem li').live({
             mouseenter: function () {
                 $(this).css("background", "#e7f4fe");
             },
             mouseleave: function () {
                 $(this).css("background", "#f5f5f5");
             }
         })



      /*  $('.ListViewItem li').hover(
            function(){
                var _this = $(this);
                $(this).animate({
                    //"height" : "100px"
                },600,function(){
                    _this.css({"background" : "#e7f4fe"});

                });
            },function(){
                var _this = $(this);
                $(this).animate({
                    //"height" : "80px"
                },600,function(){
                    _this.css({"background" : "#f5f5f5"});

                });
            }
        )*/

	},
    //点击提交
    registerStateOnclickEvent : function () {
        $('#stateF li ul li').live('click',function(){
            var _this =$(this);
            var stateName = encodeURIComponent(_this.parent().prev().text());   //父级值
            var stateValue =encodeURIComponent(_this .text());  //请求异常处理，对字符进行编码
            //获取页面上所有选中的值
            var record ='';
            $(':checkbox').each(function(){
                if($(this).attr("checked")){
                    record +=$(this).val()+ ',';
                }
            });

            //ajax请求提交后台
            var thisInstance = this;
            if(record!=''){
                var params = {
                    "module": "IdcRecords",
                    "action": "ListAjax",
                    "recordid": record,
                    "stateValue":stateValue,
                    "stateName":stateName
                }
             /*   var urlParams = 'module=IdcRecords&action=ListAjax';
                var params = {
                    'type' : 'GET',
                    'dataType': 'html',

                    'data' : urlParams+'&recordid='+record+'&stateValue='+stateValue
                };*/

               AppConnector.request(params).then(
                function(data) {
                    if (data.success == true) {
                        var jumppage=parseInt($('.active').children('a').text());
                        $('#jumppage').val(jumppage);
                        $('#jumppage').trigger('change');//用该事件实现异步刷新事件
                    }
                })
            }
        })
    },


    registerEvents : function(){
        this._super();
        this.registerOnclickEvent();
        this.registerStateOnclickEvent();
    }

});