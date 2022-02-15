{strip}
    <script type="text/javascript" src="/libraries/jquery/vtflowdesign/js/jquery.jsPlumb-1.7.2-min.js"></script>
    <script type="text/javascript" src="/libraries/jquery/vtflowdesign/js/jquery.flowdesign.v3.js"></script>
    <script type="text/javascript" src="/libraries/jquery/vtflowdesign/js/jquery.contextmenu.r2.js"></script>
    <style>
        .mini-layout {
            position: relative;
        }

        .process-step {
            position: absolute;
            cursor: move;
            min-width: 50px;
            line-height: 28px
        }

        .process-step span {
            cursor: pointer;
        }
    </style>
    <div class="contentsDiv mini-layout" id="flowdesign_canvas"></div>
    {*右键属性*}
    <input type="hidden" value="{$FLOWID}" id="source_record">
    <div id="processMenu" style="display:none;">
        <ul>
            <li id="setAttribute"><i class="icon-cog"></i>&nbsp;<span class="_label">属性</span></li>
            <li id="pmForm"><i class="icon-th"></i>&nbsp;<span class="_label">任务设置</span></li>
            {*<li id="pmJudge" class="hide"><i class="icon-share-alt"></i>&nbsp;<span class="_label">转出条件</span></li>*}
            {*<li id="pmSetting" class="hide"><i class=" icon-wrench"></i>&nbsp;<span class="_label">样式</span></li>*}
            <li id="pmDelete"><i class="icon-trash"></i>&nbsp;<span class="_label">删除</span></li>

        </ul>
    </div>
    <div id="canvasMenu" style="display:none;">
        <ul>
            <li id="taskSave"><i class="icon-ok"></i>&nbsp;<span class="_label">保存设计</span></li>
            <li id="addProcess"><i class="icon-plus"></i>&nbsp;<span class="_label">添加步骤</span></li>
            <li id="vtRefresh"><i class="icon-refresh"></i>&nbsp;<span class="_label">刷新 F5</span></li>
        </ul>
    </div>

    <script type="text/javascript">
        var the_flow_id = '{$FLOWID}';    //流程id
        /*步骤数据*/
        {literal}
        var processData = {
            {/literal}{if !empty($MODULE_MODLE)}
            "total": {$MODULE_MODLE|count}, "list": [

                {foreach item=MODEL from=$MODULE_MODLE}

                {literal} {{/literal}
                    "id": "{$MODEL['autoworkflowtaskid']}",
                    "flow_id": "{$MODEL['autoworkflowid']}",
                    "process_name": "{$MODEL['autoworkflowtaskname']}",
                    "process_to": "{$MODEL['process_to']}",
                    "process_from": '',
                    "icon": "icon-ok",
                    "style": "{$MODEL['style']}",
                    "action": 'label label-info',
                    "info": ''
                {literal} }{/literal}{if $MODEL@last}{else},{/if}
                {/foreach}
            ]{/if}
            {literal}
        };
        $(function () {
            /*创建流程设计器*/
            var _canvas = $("#flowdesign_canvas").vtFlowdesign({
                "processData": processData,					//数据初始化
                canvasMenus: {								//绑定空白右键
                    "addProcess": function (t) {
                        var mLeft = $("#jqContextMenu").css("left"), mTop = $("#jqContextMenu").css("top");//将鼠标位置传递到后台
                        var msg = {};
                        var html = $('<div></div>');
                        html.html('<form class="form-horizontal" id="addprocess"><div class="control-group"><label class="control-label" for="autoworkflowtaskname">节点名称</label><div class="controls"><input class="validate[required]" name="autoworkflowtaskname" type="text" id="autoworkflowtaskname" placeholder=""></div></div></form>');
                        msg['form'] = 'addprocess';
                        msg['title'] = '添加节点';
                        msg['message'] = html.html();

                        Vtiger_Helper_Js.showPubDialogBox(msg).then(function (e) {
                            if (e != 'ok') {
                                return false;
                            }
                            var params = getparams();
                            params['mode'] = 'addProcess';
                            params['autoworkflowid'] = getworkflowid();
                            params['autoworkflowtaskname'] = $('#autoworkflowtaskname').val();
                            params['top'] = mTop;
                            params['left'] = mLeft;
                            var d = {};
                            d.data = params;
                            d.type = 'GET';
                            AppConnector.request(d).then(
                                    function (data) {
                                        if (data.success == true) {
                                            if (!_canvas.addProcess(data.result)) {
                                                alert('添加失败');
                                            }
                                        }
                                    }
                            );
                        });
                    },
                    "taskSave": function (t) {
                        var processInfo = _canvas.getProcessInfo();//连接信息
                        var params = getparams(), d = {};
                        params['mode'] = 'saveAutoworkflowTask';
                        params['autoworkflowid'] = getworkflowid();
                        params['data'] = processInfo;
                        d.url = 'index.php';
                        d.data = params;
                        d.type = 'POST';
                        AppConnector.requestPjaxPost(d).then(function (data) {
                            data=eval("("+data+")");
                            if(data.result==1){
                                Vtiger_Helper_Js.showMessage('保存成功');
                            }else{
                                Vtiger_Helper_Js.showMessage('保存失败');
                            }
                        });
                    },
                    //刷新
                    "vtRefresh": function (t) {
                        location.reload();
                    }
                },
                //节点单击右键
                processMenus: {
                    "pmDelete": function (t) {
                        var msg = {};
                        msg['title'] = '删除步骤节点';
                        msg['message'] = '确定要删除改步骤节点';

                        Vtiger_Helper_Js.showPubDialogBox(msg).then(function (e) {
                            var activeId = _canvas.getActiveId();//右键当前的ID
                            var processInfo = _canvas.getProcessInfo();//连接信息
							//$("#window"+activeId).remove();                           
                             location.reload();
                        });
                    },
                    //设置节点，右键点击属性时触发。
                    "setAttribute": function (t) {
                        var activeId = _canvas.getActiveId();//右键当前的ID
                        var params = getparams();
                        params['mode'] = 'setAttribute';
                        params['autoworkflowtaskid'] = activeId;

                        var d = {};
                        d.url = 'index.php';
                        d.data = params;
                        d.type = 'GET';
                        AppConnector.requestPjaxPost(d).then(function (data) {
                            var msg = {
                                title: '编辑当前节点',
                                message: data,
                                form: 'setattribute'
                            };
                            Vtiger_Helper_Js.showPubDialogBox(msg).then(function (e) {
                                if (e) {
                                    params = $('#setattribute').serialize();
                                    d.url = 'index.php';
                                    d.data = params;
                                    d.type = 'POST';
                                    AppConnector.requestPjaxPost(d).then(function (data) {

                                        if (data.success == true) {
                                            Vtiger_Helper_Js.showMessage('设置成功');
											location.reload();
                                        }
                                    });
                                }
                            });
                        });

                    },
                    "pmForm": function (t) {
                        var activeId = _canvas.getActiveId();//右键当前的ID
                        var params = getparams();
                        params['mode'] = 'setAutoworkflowTask';
                        params['autoworkflowtaskid'] = activeId;
                        var d = {};
                        d.data = params;
                        d.type = 'GET';
                        AppConnector.requestPjaxPost(d).then(function (data) {
                            var msg = {
                                title: '编辑当前节点任务',
                                message: data,
                                form: 'setattribute',
                                width:'800px'
                            };
                            Vtiger_Helper_Js.showPubDialogBox(msg).then(function (e) {
                                //mode 'saveAutoworkflowTaskdetail'; action = TaskAjax
                                var params = $('#setautoworkflowtask').serialize();  
                                AppConnector.request(params).then(function (data) {
                                    Vtiger_Helper_Js.showNotify('设置成功');
                                    location.reload();
                                });
                            });
                        });
                    },
                    "pmJudge": function (t) {
                        var activeId = _canvas.getActiveId();//右键当前的ID
                        var url = "/Flowdesign/attribute/op/judge/id/" + activeId + ".html";
                        ajaxModal(url, function () {
                            //alert('加载完成执行')
                        });
                    },
                    "pmSetting": function (t) {
                        var activeId = _canvas.getActiveId();//右键当前的ID
                        var url = "/Flowdesign/attribute/op/style/id/" + activeId + ".html";
                        ajaxModal(url, function () {
                            //alert('加载完成执行')
                        });
                    }
                }
                , fnRepeat: function () {
                    //alert("步骤连接重复1");//可使用 jquery ui 或其它方式提示
                    mAlert("步骤连接重复了，请重新连接");

                }
                , fnClick: function () {
                    var activeId = _canvas.getActiveId();
                    mAlert("查看步骤信息 " + activeId);
                }
                , fnDbClick: function () {
                    //和 pmAttribute 一样
                    var activeId = _canvas.getActiveId();//右键当前的ID
                    var url = "/Flowdesign/attribute/id/" + activeId + ".html";
                    ajaxModal(url, function () {
                        //alert('加载完成执行')
                    });
                }
            });


            /*保存*/
            $("#vtiger_save").bind('click', function () {
                var processInfo = _canvas.getProcessInfo();//连接信息
                var params = getparams(), d = {};
                params['mode'] = 'saveAutoworkflowTask';
                params['autoworkflowid'] = getworkflowid();
                params['data'] = processInfo;
                d.url = 'index.php';
                d.data = params;
                d.type = 'POST';
                AppConnector.requestPjaxPost(d).then(function (data) {

                });
            });
            /*清除*/
            $("#vtiger_clear").bind('click', function () {
                if (_canvas.clear()) {
                    //alert("清空连接成功");
                    mAlert("清空连接成功，你可以重新连接");
                } else {
                    //alert("清空连接失败");
                    mAlert("清空连接失败");
                }
            });


        });
        function getparams() {
            var params = {};
            params['action'] = 'TaskAjax';
            params['module'] = 'AutoWorkflows';
            params['parent'] = 'Settings';
            return params;
        }
        function getworkflowid() {
            return the_flow_id;
        }
        {/literal}
    </script>
{/strip}