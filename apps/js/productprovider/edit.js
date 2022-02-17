$(function(){
    var $loadingToast = $('#loadingToast');
    $('.search_list').click(function(){
        var dataname=$(this).data('name');
        var dataname_display=$('input[name="'+dataname+'_display"]').val();
        if(dataname_display!=''){
            $.ajax({
                url: '/index.php?module=AccountPlatform&action=searchAccount&company='+dataname_display+'&field='+dataname,
                type: 'GET',
                dataType: 'json',
                beforeSend:function() {
                    $loadingToast.show();
                },
                success: function (data) {
                    $loadingToast.hide();
                    if (data && data.length > 0) {

                        var oli=$('#'+dataname+'_downcontent');
                        oli.empty();
                        for (var i = 0;i<data.length; i++) {
                            var item2=data[i];
                            var nArr = item2.value;
                            oli.append('<a class="weui-cell weui-cell_access accountlist" data-field="'+dataname+'" data-id="'+item2.id+'" data-name="'+item2.value+'" href="javascript:;">'+
                                '<div class="weui-cell__bd weui-cell_primary">'+
                                '<p>'+item2.value+'</p>'+
                                '</div>'+
                                '<span class="weui-cell__ft"></span>'+
                                '</a>');
                        }
                        $('#'+dataname+'_downlist').show();
                    }else{
                        $('input[name="'+dataname+'_display"]').val('');
                    }
                }
            });
        }
    });
    $('.weui-cells_form').on('click','.accountlist',function(e){
        var datafield=$(this).data('field');
        $('input[name="'+datafield+'"]').val($(this).data('id'));
        $('input[name="'+datafield+'_display"]').val($(this).data('name'));
        $('#'+datafield+'_downlist').hide();
        if(datafield=='productid'){
            getproudctvendor($(this).data('id'))
        }

    });
    function getproudctvendor(id){
        $.ajax({
            url: '/index.php?module=AccountPlatform&action=getVendorInfo&productid='+id,
            type: 'GET',
            dataType: 'json',
            beforeSend:function() {
                $loadingToast.show();
            },
            success: function (data) {
                $loadingToast.hide();
                $('.vendors').remove();
                if (data.countnum > 0) {
                    var datas=data.data;
                    for (var i = 0;i<datas.length; i++) {
                        var item2=datas[i];
                        if(i ==0){
                            updateInput(item2);
                        }
                        var rebatetypename=item2.rebatetypename=='CashBack'?'返现':'返货';
                        var str='<div class="weui-panel weui-panel_access vendors" data-json=\''+JSON.stringify(item2)+'\'>\
                            <div class="page__bd page__bd_spacing">\
                            <a href="javascript:;" class="weui-btn weui-btn_primary">选用</a>\
                            </div>\
                            <div class="weui-panel__hd" style="padding:0">\
                            </div>\
                            <div class="weui-panel__bd">\
                            <div class="weui-media-box weui-media-box_small-appmsg">\
                            <div class="weui-cells">\
                            <a class="weui-cell weui-cell_access" href="javascript:;">\
                            <div class="weui-cell__bd weui-cell_primary">\
                            <label class="weui-form-preview__label">采购合同编号</label>\
                            <span class="weui-form-preview__value" style="text-align: right;">'+item2.contract_no+'</span>\
                            </div>\
                            </a>\
                            <a class="weui-cell weui-cell_access" href="javascript:;">\
                            <div class="weui-cell__bd weui-cell_primary">\
                            <label class="weui-form-preview__label">供应商</label>\
                            <span class="weui-form-preview__value" style="text-align: right;">'+item2.vendorname+'</span>\
                            </div>\
                            </a>\
                            <a class="weui-cell weui-cell_access" href="javascript:;">\
                            <div class="weui-cell__bd weui-cell_primary">\
                            <label class="weui-form-preview__label">供应商返点类型</label>\
                            <span class="weui-form-preview__value" style="text-align: right;">'+rebatetypename+'</span>\
                            </div>\
                            </a>\
                            <a class="weui-cell weui-cell_access" href="javascript:;">\
                            <div class="weui-cell__bd weui-cell_primary">\
                            <label class="weui-form-preview__label">供应商返点</label>\
                            <span class="weui-form-preview__value" style="text-align: right;">'+item2.rebate+'</span>\
                            </div>\
                            </a>\
                            </div>\
                            </div>\
                            </div>\
                            <div class="weui-panel__ft">\
                            <a href="javascript:void(0);" class="weui-cell weui-cell_access weui-cell_link">\
                            <div class="weui-cell__bd"></div>\
                            </a>\
                            </div>\
                            </div>';
                        $('#beforevendor').before(str);
                    }
                }
            }
        });
    }
    $('.weui-cells_form').on('click','.vendors',function(e){
        var data=$(this).data('json');
        Tips.confirm({
            content: '确定要选该信息吗?',
            define: '确定',
            cancel: '取消',
            before: function(){
            },
            after: function(b){
                if(b){
                    updateInput(data);
                    $('input[name="idaccount"]').focus();
                }
            }
        });

    });
    $('.docancel').click(function(){
        Tips.confirm({
            content: '确定要取消吗?',
            define: '确定',
            cancel: '取消',
            before: function(){
            },
            after: function(b){
                if(b){
                    window.location.href='/index.php?module=ProductProvider&action=index';
                }
            }
        });

    });
    $('.doconfirm').click(function(){
        Tips.confirm({
            content: '确定要保存吗?',
            define: '确定',
            cancel: '取消',
            before: function(){
            },
            after: function(b){
                if(b){
                    var flag=checkForm();
                    if(flag){
                        $.ajax({
                            url: '/index.php?module=ProductProvider&action=doadd',
                            data:$('#EditView').serializeArray(),
                            type: 'POST',
                            dataType: 'json',
                            beforeSend:function() {
                                $loadingToast.show();
                            },
                            success: function (data) {
                                $loadingToast.hide();
                                if(data.success){
                                    if($("input[name='record']").val()>0){
                                        window.location.href='/index.php?module=ProductProvider&action=index';
                                    }else{
                                        Tips.confirm({
                                            content: '保存成功,确定要继续添加吗?',
                                            define: '确定',
                                            cancel: '下次再吧',
                                            before: function(){
                                            },
                                            after: function(b){
                                                if(!b){
                                                    window.location.href='/index.php?module=ProductProvider&action=index';
                                                }else{
                                                    window.location.reload();
                                                }
                                            }
                                        });
                                    }
                                }else{
                                    Tips.alert({
                                        content: data.msg
                                    });
                                }
                            }
                        });
                    }
                }
            }
        });

    });
    function updateInput(data){
        $('input[name="servicestartdate"]').val(data.effectdate);
        $('input[name="serviceenddate"]').val(data.enddate);
        $('input[name="supplierrebate"]').val(data.rebate);
        $('input[name="vendorid_display"]').val(data.vendorname);
        $('input[name="vendorid"]').val(data.vendorid);
        $('input[name="suppliercontractsid_display"]').val(data.contract_no);
        $('input[name="suppliercontractsid"]').val(data.suppliercontractsid);
        $('select[name="rebatetype"]').val(data.rebatetype);
    }
    function checkForm(){
        var flag=true;
        do{
            var value=$('input[name="accountid"]')
            if(value.val()<=0){
                flag=false;
                break;
            }
            var value=$('input[name="vendorid"]')
            if(value.val()<=0){
                flag=false;
                break;
            }
            var value=$('input[name="productid"]')
            if(value.val()<=0){
                flag=false;
                break;
            }
            var value=$('input[name="suppliercontractsid"]')
            if(value.val()<=0){
                flag=false;
                break;
            }
            var value=$('input[name="supplierrebate"]')
            if(value.val()==''){
                flag=false;
                break;
            }
            var value=$('input[name="accountrebate"]')
            if(value.val()==''){
                flag=false;
                break;
            }
            var value=$('input[name="idaccount"]')
            if(value.val()==''){
                flag=false;
                break;
            }
        }while(0);
        if(!flag){
            Tips.alert({
                content: '必填项'+$(value).data('msg')+'不能为空!'
            });
            $(value).focus();
        }
        return flag;
    }
});


