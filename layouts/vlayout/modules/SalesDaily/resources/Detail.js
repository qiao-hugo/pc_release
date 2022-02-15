Vtiger_Detail_Js('SalesDaily_Detail_Js',{
},{

    /**
     * young.yang 2015-05-21
     * 覆盖父级的审核按钮动作，主要是为了指定审核客服使用
     **/
    mangerReturnVisiting:function(){
        var instanceThis=this
        $('.editmanger').on('click',function(){
            var id=$(this).data('id'); //获取审核节点名称
            var msg={
                'message':'<h3>请填写回访信息</h3>',
                "width":'600px'
            };

            instanceThis.showConfirmationBox(msg).then(function(e){
                var params={'module' : 'SalesDaily',
                            'action' :	'BasicAjax',
                            'record' :	$('#recordId').val(),
                            'id':id,
                            'date':$('#changedate').val(),
                            'content':$('#mangercontent').val(),
                            'mode'   : 	'updateMContent'
                            };

                AppConnector.request(params).then(function(data){
                    window.location.reload();
                });


            });
            $('.modal-content .modal-body').append('<table class="table table-bordered equalSplit detailview-table"><tr><td style="text-align:right">日期</td><td><input type="text" id="changedate" readonly></td></tr><tr><td style="text-align:right">内容</td><td><textarea id="mangercontent"></textarea></td></tr></table>');

            $('#changedate').datepicker({
                format: "yyyy-mm-dd",
                language:  'zh-CN',
                autoclose: true,
                todayHighlight:true,
                pickerPosition: "bottom-left",
                //startDate:enddate,
                endDate:new Date()
            });

        });


    },
    checkedform:function(){

        $('#changedate').popover('destroy');
        if(''==$('#changedate').val()){
            $('#changedate').focus();
            $('#changedate').attr('data-content','<font color="red">必填项不能为空</font>');
            $('#changedate').popover("show");
            $('.popover-content').css({"color":"red","fontSize":"12px"});
            $('.popover').css('z-index',1000010);
            setTimeout("$('#changedate').popover('destroy')",2000);
            return false;
        }
        $('#mangercontent').popover('destroy');
        if(''==$('#mangercontent').val()){
            $('#mangercontent').focus();
            $('#mangercontent').attr('data-content','<font color="red">必填项不能为空</font>');
            $('#mangercontent').popover("show");
            $('.popover-content').css({"color":"red","fontSize":"12px"});
            $('.popover').css('z-index',1000010);
            setTimeout("$('#changedate').popover('destroy')",2000);
            return false;
        }
        return true;

    },
    showConfirmationBox : function(data){
        var thisstance=this;
        var aDeferred = jQuery.Deferred();
        var width='800px';
        if(typeof  data['width'] != "undefined"){
            width=data['width'];
        }
        var bootBoxModal = bootbox.confirm({message:data['message'],width:width, callback:function(result) {
            if(result){
                if(thisstance.checkedform()){
                    aDeferred.resolve();
                }else{
                    return false;
                }

            } else{
                aDeferred.reject();
            }
            }, buttons: { cancel: {
            label: '取消',
            className: 'btn'
             },
            confirm: {
                label: '确认',
                className: 'btn-success'
            }
        }});
        bootBoxModal.on('hidden',function(e){
            if(jQuery('#globalmodal').length > 0) {
                jQuery('body').addClass('modal-open');
            }
        })
        return aDeferred.promise();
    },
    showFullSalesdaily:function(){
        $('#SalesDaily_detailView_basicAction_LBL_ALL').on('click',function(){
                    var id=$(this).data('id'); //获取审核节点名称
                    var msg={
                        'message':'<div style="overflow:auto;"><div id="calendar"></div><div> ',
                        "width":'800px'
                    };

                    Vtiger_Helper_Js.showConfirmationBox(msg).then(function(e){



                    });
                    $('.modal-body').css({"max-height":"560px"});
                    $('#calendar').fullCalendar({
                        header:false,
                        monthNames: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
                        monthNamesShort: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
                        dayNames: ["周日", "周一", "周二", "周三", "周四", "周五", "周六"],
                        dayNamesShort: ["周日", "周一", "周二", "周三", "周四", "周五", "周六"],
                        today: ["今天"],
                        firstDay: 1,
                        buttonText: {
                            today: '本月',
                            month: '月',
                            week: '周',
                            day: '日',
                            prev: '上一月',
                            next: '下一月'
                        },
                        events: currentmonth
                    });





                });
    },
 
    
	registerEvents: function(){
		this._super();
		this.mangerReturnVisiting();
		this.showFullSalesdaily();
	}
})