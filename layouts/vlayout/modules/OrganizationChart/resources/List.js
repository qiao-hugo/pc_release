/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("OrganizationChart_List_Js",{},{
    //初始化
    loading:function(){
        this.getData();
    },
    getData:function(){
        var thisInstance=this;
        var userid=$('#user_editView_fieldName_dropDown').val();
        postData= {
            'module': app.getModuleName(),
            'action': 'selectAjax',
            'mode': 'getUserData',
            'userid':userid
        };
        var progressIndicatorElement = jQuery.progressIndicator({
            'message' : '正在处理,请耐心等待哟',
            'position' : 'html',
            'blockInfo' : {'enabled' : true}
        });
        AppConnector.request(postData).then(
            function(data){
                progressIndicatorElement.progressIndicator({
                    'mode' : 'hide'
                });

                if(data.data.length>0) {

                    var showlist = $("<ul id='org' style='display:none'></ul>");
                    showall(data.data, showlist);
                    $("#jOrgChart").append(showlist);
                    $("#org").jOrgChart( {
                        chartElement : '#jOrgChart',//指定在某个dom生成jorgchart
                        dragAndDrop : false //设置是否可拖动
                    });
                    function showall(menu_list, parent){
                        $.each(menu_list, function(index, val) {
                            if(val.childrens.length > 0){

                                var li = $("<li></li>");
                                li.append("<div style='overflow: hidden;width: 100%;height:100%;cursor:pointer;'>("+val.childrens.length+")&nbsp;"+val.name+"</div>").append("<ul></ul>").appendTo(parent);
                                //递归显示
                                showall(val.childrens, $(li).children().eq(1));
                            }else{
                                $("<li></li>").append("<div  style='overflow: hidden;width: 100%;height: 100%;'>"+val.name+"</div>").appendTo(parent);
                            }
                        });
                    }
                } else {

                }
            },
            function(error,err){

            }
        );
    },

    getrefreshvisiting:function(){
        var thisInstance=this;
        $('table').on('click','#postrefresh',function(){
            var userid=$('#user_editView_fieldName_dropDown').val();
            postData= {
                'module': app.getModuleName(),
                'action': 'selectAjax',
                'mode': 'getRefreshUserData',
                'userid': userid
            };
            var progressIndicatorElement = jQuery.progressIndicator({
                'message' : '数据量比较大,请耐心等待哟',
                'position' : 'html',
                'blockInfo' : {'enabled' : true}
            });
            AppConnector.request(postData).then(
                function(data){
                    progressIndicatorElement.progressIndicator({
                        'mode' : 'hide'
                    });
                    if(data.success) {
                        var  params = {text : app.vtranslate(),
                            title : app.vtranslate(data.result.msg)};
                        Vtiger_Helper_Js.showPnotify(params);

                    }
                }
            )
        });
    },
    submitconfim:function(){
        var thisInstance=this;
        $('table').on('click','#PostQuery',function() {
            $('#jOrgChart').html('');
            thisInstance.getData();
        });
    },
    registerEvents : function(){
        this._super();

        this.submitconfim();
        this.loading();
        this.getrefreshvisiting();

    }
});