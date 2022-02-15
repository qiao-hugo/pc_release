/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("EmployeeAbility_List_Js",{},{
	registerEvents : function(){
        this.registerPageNavigationEventsK();
        this.fixedTable();
        $('#listViewContentTable').fixedHeaderTable({ altClass: 'odd', footer: true, cloneHeadToFoot: false, fixedColumns: 6 });
        this.showRejectHistory();
        this._super();
        this.tableFix();
        this.tableFixByAjax();

    },
    fixedTable:function(){

    },
    tableFix:function(){

    },
    tableFixByAjax:function(){

    },
    showRejectHistory:function(){
        jQuery('.reject').on('mouseover',function(e){
            console.log(1233333);
            var thisInstance = this;
            var rejector = $(this).attr("data-rejector");
          var rejectreason = $(this).attr('data-rejectreason');
          var rejectnum = $(this).attr('data-rejectnum');
          if(rejector && rejectreason){
              showRejectHistory(e,rejector,rejectreason,rejectnum);
              return;
          }

          var record = $(this).data("employeeabilityid");
          var fieldname = $(this).data("rejectcolumn");
            postData= {
                'module': 'EmployeeAbility',
                'action': 'ChangeAjax',
                'mode': 'rejectHistory',
                'record':record,
                'fieldname':fieldname,
            };
            var progressIndicatorElement = jQuery.progressIndicator({
                'message' : '正在处理,请耐心等待哟',
                'position' : 'html',
                'blockInfo' : {'enabled' : true}
            });
            AppConnector.request(postData).then(
                function(data) {
                    progressIndicatorElement.progressIndicator({
                        'mode': 'hide'
                    });
                    if (data.success) {
                        console.log(data);
                        $(thisInstance).attr("data-rejector",data.result.rejector);
                        $(thisInstance).attr("data-rejectreason",data.result.rejectreason);
                        $(thisInstance).attr("data-rejectnum",data.result.rejectnum);
                        showRejectHistory(e,data.result.rejector,data.result.rejectreason,data.result.rejectnum);
                    }
                }
            )

      });

        jQuery('.reject').on('mouseout',function(e){
            $("#div_toop").remove();
        });
        jQuery('.reject').on('mousemove',function(e){
            $("#div_toop")
                .css({
                    "top": (e.pageY + 10) + "px",
                    "position": "absolute",
                    "left": (e.pageX + 20) + "px",
                });
        });
    },
    registerPageNavigationEventsK : function(){

        var thisInstance = this;
        var pageLimit = jQuery('#pageLimit').val();
        var noOfEntries = jQuery('#noOfEntries').val();
        var orderBy = jQuery('#orderBy').val();
        var sortOrder = jQuery("#sortOrder").val();
        var cvId = thisInstance.getCurrentCvId();

        if($('.pagination-demo').length<1){
            return;
        }
        var pageNumber = jQuery('#pageNumber').val(); //当前页码
        var totalCount = jQuery('#totalCount').val(); // 总页数
        //默认绑定分页
        $('.pagination-demo').twbsPagination({
            total:noOfEntries,  //总记录数
            totalPages: totalCount,
            visiblePages: 6,
            first: '首页',
            prev: '上页',
            next: '下页',
            last: '末页',
            startPage:1,
            onPageClick: function (event, p){
                getpage(p,'#listViewContents',false);

            }
        });
        //列表页面通用获取html方法
        function getpage(p,id,pagereset){
            var aDeferred = jQuery.Deferred();
            $('#pagecount').val(p);
            var loadingMessage = jQuery('.listViewLoadingMsg').text();
            var progressIndicatorElement = jQuery.progressIndicator({
                'message' : loadingMessage,
                'position' : 'html',
                'blockInfo' : {
                    'enabled' : true
                }
            });
            //$('.pagination-demo').css({'display':'none'});
            //console.log(p);

            var searchParamsPreFix = 'BugFreeQuery';
            var rowOrder = "";
            var $searchRows = $("tr[id^=SearchConditionRow]");
            $searchRows.each(function(){
                rowOrder += $(this).attr("id")+",";
            });

            eval("$('#"+searchParamsPreFix+"_QueryRowOrder')").attr("value",rowOrder);
            var limit = $('#limit').val();
            var o = {};
            var a = $('#SearchBug').serializeArray();
            $.each(a, function() {
                if (o[this.name] !== undefined) {
                    if (!o[this.name].push) {
                        o[this.name] = [o[this.name]];
                    }
                    o[this.name].push(this.value || '');
                } else {
                    o[this.name] = this.value || '';
                }
            });
            var form=JSON.stringify(o);

            var urlParams = {"page":p,"BugFreeQuery":form,"limit":limit};

            var defaultParams = thisInstance.getDefaultParams();
            urlParams = jQuery.extend(defaultParams,urlParams);
            //搜索提交查询后调用到这里
            //console.log(urlParams);
            AppConnector.requestPjaxPost(urlParams).then(
                function(data){

                    progressIndicatorElement.progressIndicator({
                        'mode' : 'hide'
                    })
                    //$('.pagination-demo').css({'display':''});
                    if(pagereset){
                        //键入条转页的时候会用到？？？
                        $('#pagination').html('<ul class="pagination-demo"></ul>');
                    }

                    jQuery(id).html('');
                    jQuery(id).html(data);
                    noOfEntries = jQuery('#noOfEntries').val();
                    totalCount = jQuery('#totalCount').val();
                    pageNumber = jQuery('#pageNumber').val();
                    app.setContentsHeight();//2015-4-15 young.yang #8720 分页请求重新调整页面高度
                    app.tabletrodd();  //隔行换色

                    err = $("#sortOrder").val();
                    ett = $("#orderBy").val();
                    if(ett){
                        imgclass = "";
                        if(err == "ASC"){
                            //imgclass = "icon-chevron-up icon-white";
                            imgclass="layouts/vlayout/skins/images/sort_up.png"
                        }else{
                            //imgclass = "icon-chevron-down icon-white"
                            imgclass="layouts/vlayout/skins/images/sort_down.png"
                        }
                        //$("#listViewContents").find("th[data-field= "+ett+"]").children("img").attr("class",imgclass);
                        //$("#listViewContents").find("th").prepend('<img src="layouts/vlayout/skins/images/sort_all.png">');
                        $("#listViewContents").find("th[data-field='"+ett+"']").children("img").attr("src",imgclass);
                    }
                    console.log('xadada1111');
                    $('#listViewContentTable').fixedHeaderTable({ altClass: 'odd', footer: true, cloneHeadToFoot: false, fixedColumns: 6 });
                    aDeferred.resolve(data);
                    thisInstance.showRejectHistory();
                },
                function(textStatus, errorThrown){
                    aDeferred.reject(textStatus, errorThrown);
                    progressIndicatorElement.progressIndicator({
                        'mode' : 'hide'
                    });
                    //aDeferred.resolve(false);
                }
            );
            $('#jumppage').val(''); //调整之后清空跳转
            return aDeferred.promise();
        }
        //列表搜索
        $('#PostQuery').on('click',function(){
            getpage(1,'#listViewContents',true).then(
                function(data){
                    noOfEntries = jQuery('#noOfEntries').val();
                    $('.pagination-demo').twbsPagination({
                        total:noOfEntries,
                        totalPages: totalCount,
                        visiblePages: 6,
                        first: '首页',
                        prev: '上页',
                        next: '下页',
                        last: '末页',
                        startPage:1,
                        onPageClick: function (event, p){
                            getpage(p,'#listViewContents',false);
                            //document.SearchBug.submit();
                        }
                    });
                });
            //thisInstance.registerPageNavigationEventsK();
            //return false;
        });
        var is_advances = jQuery('#is_advances').val();
        if(is_advances == 2){
            getpage(1,'#listViewContents',true).then(
                function(data){
                    noOfEntries = jQuery('#noOfEntries').val();
                    $('.pagination-demo').twbsPagination({
                        total:noOfEntries,
                        totalPages: totalCount,
                        visiblePages: 6,
                        first: '首页',
                        prev: '上页',
                        next: '下页',
                        last: '末页',
                        startPage:1,
                        onPageClick: function (event, p){
                            getpage(p,'#listViewContents',false);
                            //document.SearchBug.submit();
                        }
                    });
                });
        }
        //后台用户检索
        $('#userSearchButton').on('click',function(){
            getpage(1,'#listViewContents',true).then(
                function(data){
                    $('.pagination-demo').twbsPagination({
                        total:noOfEntries,
                        totalPages: totalCount,
                        visiblePages: 6,
                        first: '首页',
                        prev: '上页',
                        next: '下页',
                        last: '末页',
                        startPage:1,
                        onPageClick: function (event, p){
                            getpage(p,'#listViewContents',false);
                            //document.SearchBug.submit();
                        }
                    });
                },function(){});
        });

        //wangbin 2015-7-1 添加表头排序
        $("#listViewContents").find("th").live("click",function(){
            $("#orderBy").val($(this).attr('data-field')); //排序字段

            if($("#sortOrder").val() == "" || $("#sortOrder").val() == "ASC"){
                $("#sortOrder").val("DESC");
            }else{
                $("#sortOrder").val("ASC");
            }
            tmp = parseInt(jQuery('#pageNumber').val());
            getpage(tmp,'#listViewContents',true).then(
                function(data){
                    $('.pagination-demo').twbsPagination({
                        total:noOfEntries,
                        totalPages: totalCount,
                        visiblePages: 6,
                        first: '首页',
                        prev: '上页',
                        next: '下页',
                        last: '末页',
                        startPage:tmp,
                        onPageClick: function (event, p){
                            getpage(p,'#listViewContents',false);
                        }
                    });
                });
        });
        //点击排序
        /*$('.listViewEntriesTable .listViewHeaders th').on('click',function(){
            //alert($(this).data('field'));
            //$('.listViewEntriesTable .listViewHeaders th').find('i').remove();
            var iclass='icon-arrow-down';
            var defsort="DESC";
            //console.log($(this).find('i').hasClass(iclass));console.log(iclass);
            if($(this).find('i').length>0){ //如果为当前字段
                if($(this).find('i').hasClass('icon-arrow-up')){
                    iclass='icon-arrow-down';
                    defsort='DESC';
                }else{
                    iclass='icon-arrow-up';
                    defsort='ASC';
                }
            }else{ //非排序过的字段，先清除标记
                $('.listViewEntriesTable .listViewHeaders th').find('i').remove();
            }
            $(this).html($(this).text()+'<i class="'+iclass+'"></i>');
            //设置隐藏指
            jQuery('#orderBy').val($(this).data('field'));
            jQuery("#sortOrder").val(defsort);
            jQuery('#PostQuery').trigger('click');

        });*/
        //弹出框检索
        jQuery('#popupSearchButton').on('click',function(e) {
            getpage(1,'#popupContents',true).then(
                function(data){
                    $('.pagination-demo').twbsPagination({
                        total:noOfEntries,
                        totalPages: totalCount,
                        visiblePages: 6,
                        first: '首页',
                        prev: '上页',
                        next: '下页',
                        last: '末页',
                        startPage:1,
                        onPageClick: function (event, p){

                            getpage(p,'#popupContents',false);
                            //document.SearchBug.submit();
                        }
                    });
                });
        });
        //跳转页
        $('#jumppage').on('change',function(){
            var jumppage=parseInt($(this).val());//$('#jumppage').val();
            //var totalCount=$('#totalCount').val();
            if(jumppage<=totalCount){
                // console.log(jumppage);
                //$('#pagination').html('<ul class="pagination-demo"></ul>');
                getpage(jumppage,'#listViewContents',true).then(
                    function(data){

                        $('.pagination-demo').twbsPagination({
                            total:noOfEntries,
                            totalPages: totalCount,
                            visiblePages: 6,
                            first: '首页',
                            prev: '上页',
                            next: '下页',
                            last: '末页',
                            startPage:jumppage,
                            onPageClick: function (event, p){

                                getpage(p,'#listViewContents',false);
                                //document.SearchBug.submit();
                            }
                        });
                    });
            }else{
                var params = {
                    title : 'ERROR',
                    text: '输入有误请重新输入',
                    animation: 'show',
                    type: 'error'
                };
                Vtiger_Helper_Js.showPnotify(params);
            }
            $('#jumppage').val('');
        });
        //每页多少条
        $('#limit').on('change',function(){
            //$('#pagination').html('<ul class="pagination-demo"></ul>');

            getpage(1,'#listViewContents',true).then(
                function(data){
                    $('.pagination-demo').twbsPagination({
                        total:noOfEntries,
                        totalPages: totalCount,
                        visiblePages: 6,
                        first: '首页',
                        prev: '上页',
                        next: '下页',
                        last: '末页',
                        startPage:1,
                        onPageClick: function (event, p){
                            getpage(p,'#listViewContents',false);
                            //document.SearchBug.submit();
                        }
                    });
                });
        });
        //2015-04-28 young 列表页面绑定回车时间
        $(document).bind('keypress',function(event){
            if(event.keyCode == "13")
            {
                event.preventDefault();
                if($('#jumppage').is(":focus")) {

                    $('#jumppage').trigger("change");
                }
                if($('#PostQuery').length>0 && !$('#jumppage').is(":focus")){
                    $('#PostQuery').trigger("click");
                }

            }
        });
    },

});

function showRejectHistory(e,rejector,rejectreason,rejectnum) {
    $("#div_toop").remove();
    var div_toop = '';
    div_toop += ' <div id="div_toop" style="width: 300px;word-wrap: break-word;word-break: break-all">';
    div_toop += '<span>第'+rejectnum+'次驳回</span><br>';
    div_toop += '<span>驳回人:'+rejector+'</span><br>';
    div_toop += '<span>驳回原因:'+rejectreason+'</span><br>';
    div_toop += '</div>';

    $("body").append(div_toop);
    $("#div_toop")
        .css({
            "top": (e.pageY + 10) + "px",
            "position": "absolute",
            "left": (e.pageX + 20) + "px",
        }).show("fast");
}

