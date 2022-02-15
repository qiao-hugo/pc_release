(function ($) {
    var defaults = {
        processData: {},//步骤节点数据
        isdraggable:true,
        isdelete:true,
        fnRepeat: function () {
            alert("步骤连接重复");
        },
        fnClick: function () {
            alert("单击");
        },
        fnDbClick: function () {
            alert("双击");
        },
        canvasMenus: {
            "one": function (t) {
                alert('画面右键')
            }
        },
        processMenus: {
            "one": function (t) {
                alert('步骤右键')
            }
        },
        /*右键菜单样式*/
        menuStyle: {
            border: '1px solid #5a6377',
            minWidth: '150px',
            padding: '5px 0'
        },
        itemStyle: {
            fontFamily: 'verdana',
            color: '#333',
            border: '0',
            /*borderLeft:'5px solid #fff',*/
            padding: '5px 40px 5px 20px'
        },
        itemHoverStyle: {
            border: '0',
            /*borderLeft:'5px solid #49afcd',*/
            color: '#fff',
            backgroundColor: '#5a6377'
        },
        mtAfterDrop: function (params) {
            //alert('连接成功后调用');
            //alert("连接："+params.sourceId +" -> "+ params.targetId);
        },
        //默认接线路的绘画样式
        connectorPaintStyle: {
            lineWidth: 2,
            strokeStyle: "#49afcd",
            joinstyle: "round"
        },
        //默认鼠标经过样式
        connectorHoverStyle: {
            lineWidth: 2,
            strokeStyle: "#da4f49"
        }
		

    };

	//初始化节点
    var initEndPoints = function () {
        //源节点
        $(".process-flag").each(function (i, e) {
            var p = $(e).parent();
            jsPlumb.makeSource($(e), {
                parent: p,
                anchor: "Continuous",
                endpoint: ["Dot", {radius: 1}],				//连接点
                connector: [ "Flowchart", { gap: 0, cornerRadius: 5, alwaysRespectStubs: true } ],	//连接线
				//connector :'StateMachine',
				//connector: [ "Bezier", {curviness:60}],
                connectorStyle: defaults.connectorPaintStyle,
                hoverPaintStyle: defaults.connectorHoverStyle,

            });
        });
        //目标节点
        if($('.process-step').length>0&&defaults.isdraggable){
            jsPlumb.makeTarget(jsPlumb.getSelector(".process-step"), {
                dropOptions: {hoverClass: "hover", activeClass: "active"},
                anchor: "Continuous",
                maxConnections: -1,
                endpoint: ["Dot", {radius: 1}],
                paintStyle: {fillStyle: "#ec912a", radius: 1},
                hoverPaintStyle: this.connectorHoverStyle,
                beforeDrop: function (params) {                         //拖动之前判断是否允许随便放
                    var from = $('#' + params.sourceId).attr('process_id');
                    var target = $('#' + params.targetId).attr('process_id');

                    //重复的连接不允许，反向的连接不允许，自己连接不允许
                    if (params.sourceId == params.targetId) return false;
                    if($('#vtiger_process_info').find('.node_'+from+'_'+target).length>0){
                        return false;
                    }else if($('#vtiger_process_info').find('.node_'+target+'_'+from).length>0) {
                        return false;
                    }
                    return true;
                }
            });
        }

    }

    //设置隐藏域保存关系信息
    var aConnections = [];
    var setConnections = function (conn, remove) {
        if (!remove) aConnections.push(conn);
        else {
            var idx = -1;
            for (var i = 0; i < aConnections.length; i++) {
                if (aConnections[i] == conn) {
                    idx = i;
                    break;
                }
            }
            if (idx != -1) aConnections.splice(idx, 1);
        }
        if (aConnections.length > 0) {
            var s = "";
            for (var j = 0; j < aConnections.length; j++) {
                var from = $('#' + aConnections[j].sourceId).attr('process_id');
                var target = $('#' + aConnections[j].targetId).attr('process_id');
                //s = s + "<input type='hidden' class='node_"+ form +"_"+ target +"' value=\"" + from + "," + target + "\">";
                s = s + "<input type='hidden' class=\"node_"+ from +"_"+ target +"\" value=\"" + from + "," + target + "\">";
            }
            $('#vtiger_process_info').html(s);
        } else {
            $('#vtiger_process_info').html('');
        }
        jsPlumb.repaintEverything();//重画
    };

    
    $.fn.vtFlowdesign = function (options) {
        var _canvas = $(this);
        //加入隐藏值
        _canvas.append('<input type="hidden" id="vtiger_active_id" value="0"/>');
        _canvas.append('<div id="vtiger_process_info"></div>');

        //初始化默认参数
        $.each(options, function (i, val) {
            if (typeof val == 'object' && defaults[i])
                $.extend(defaults[i], val);
            else
                defaults[i] = val;
        });
        //定义右键默认设置
        var contextmenu = {
            bindings: defaults.canvasMenus,
            menuStyle: defaults.menuStyle,
            itemStyle: defaults.itemStyle,
            itemHoverStyle: defaults.itemHoverStyle
        }
        $(this).contextMenu('canvasMenu', contextmenu);
        //1.导入默认的配置
        jsPlumb.importDefaults({
            DragOptions: {cursor: 'pointer'},
            EndpointStyle: {fillStyle: '#225588'},
            Endpoint: ["Dot", {radius: 1}],
            ConnectionOverlays: [
                ["Arrow", {location: -1,width:6,length:8}],
                ["Label", {
                    location: 0.1,
                    id: "label",
                    cssClass: "aLabel"
                }]
            ],
            Anchor: 'Continuous',
            ConnectorZIndex: 5,
            HoverPaintStyle: defaults.connectorHoverStyle
        });
        //ie9以下，用VML画图，兼容画布
        if ($.browser.msie && $.browser.version < '9.0') {
            jsPlumb.setRenderMode(jsPlumb.VML);
        } else { //其他浏览器用SVG
            jsPlumb.setRenderMode(jsPlumb.SVG);
        }

        //2.初始化原步骤
        var lastProcessId = 0;
        var processData = defaults.processData;
        if (processData.list) {
            $.each(processData.list, function (i, row) {
                var nodeDiv = document.createElement('div');
                var nodeId = "window" + row.id, badge = 'badge-inverse', icon = 'icon-ok';
                /*if (lastProcessId == 0)//第一步
                {
                    badge = 'badge-info';
                    icon = 'icon-play';
                }*/
                if (row.icon) {
                    icon = row.icon;
                }
				//扩展，当前节点是否活动可以修改这里
                $(nodeDiv).attr("id", nodeId)
                    .attr("style", row.style)
                    .attr("process_to", row.process_to)
                    .attr("process_id", row.id)
                    .addClass("process-step "+row.action)
                    .html('<span class="process-flag"><i class="' + icon + ' icon-white"></i></span>' + row.process_name)
                    .mousedown(function (e) {
                        if (e.which == 3) { //右键绑定
                            _canvas.find('#vtiger_active_id').val(row.id);
                            contextmenu.bindings = defaults.processMenus
                            $(this).contextMenu('processMenu', contextmenu);
                        }
                    });
                _canvas.append(nodeDiv);
                //索引变量
                lastProcessId = row.id;
            });//each
        }

        var timeout = null;
        //点击或双击事件,这里进行了一个单击事件延迟，因为同时绑定了双击事件
        $(".process-step").live('click', function () {
            //激活
            _canvas.find('#vtiger_active_id').val($(this).attr("process_id"))
            clearTimeout(timeout);
            var obj = this;
            timeout = setTimeout(defaults.fnClick, 300);
        });
        //3.初始化每个节点的样式
        initEndPoints();
        //4.初始化事件
        if(defaults.isdelete){
            //使之可拖动
            jsPlumb.draggable(jsPlumb.getSelector(".process-step"));

            //绑定添加连接操作。画线-input text值  拒绝重复连接
            jsPlumb.bind("connection", function (info,event) {
                setConnections(info.connection)
            });
            //绑定删除connection事件
            jsPlumb.bind("connectionDetached", function (info,event) {
                setConnections(info.connection, true);
            });
            //绑定删除确认操作
            jsPlumb.bind("click", function (c) {
                if (confirm("你确定取消连接吗?"))
                    jsPlumb.detach(c);
            });
        }

        //目标节点，判断是否存在重复的连接
        //if($('.process-step').length>0){

        //}
        //初始化连接线
        var _canvas_design = function () {
            //连接关联的步骤
            $('.process-step').each(function (i) {
                var sourceId = $(this).attr('process_id');
                //var nodeId = "window"+id;
                var prcsto = $(this).attr('process_to');
                var toArr = prcsto.split(",");
                var processData = defaults.processData;
                $.each(toArr, function (j, targetId) {

                    if (targetId != '' && targetId != 0) {
                        //检查 source 和 target是否存在
                        var is_source = false, is_target = false;
                        $.each(processData.list, function (i, row) {
                            if (row.id == sourceId) {
                                is_source = true;
                            } else if (row.id == targetId) {
                                is_target = true;
                            }
                            if (is_source && is_target)
                                return true;
                        });

                        if (is_source && is_target) {
                            jsPlumb.connect({
                                source: "window" + sourceId,
                                target: "window" + targetId
                            });
                            return;
                        }
                    }
                })
            });
        }//_canvas_design end reset
        _canvas_design();
		//外部调用接口
        var vtFlowdesign = {
            //动态增加节点
            addProcess: function (row) {

                if (row.id <= 0) {
                    return false;
                }
                var nodeDiv = document.createElement('div');
                var nodeId = "window" + row.id, badge = 'badge-inverse', icon = 'icon-ok';

                if (row.icon) {
                    icon = row.icon;
                }
                $(nodeDiv).attr("id", nodeId)
                    .attr("style", row.style)
                    .attr("process_to", row.process_to)
                    .attr("process_id", row.id)
                    .addClass("process-step label label-info")
                    .html('<span class="process-flag"><i class="' + icon + ' icon-white"></i></span>' + row.process_name)
                    .mousedown(function (e) {
                        if (e.which == 3) { //右键绑定
                            _canvas.find('#vtiger_active_id').val(row.id);
                            contextmenu.bindings = defaults.processMenus
                            $(this).contextMenu('processMenu', contextmenu);
                        }
                    });

                _canvas.append(nodeDiv);
                //使之可拖动
                if(defaults.isdraggable){
                    jsPlumb.draggable(jsPlumb.getSelector(".process-step"));
                }
                initEndPoints();				//初始化
                return true;

            },
			//删除
            delProcess: function (activeId) {
                if (activeId <= 0) return false;

                $("#window" + activeId).remove();
                return true;
            },
            //获取当前活动的节点
            getActiveId: function () {
                return _canvas.find("#vtiger_active_id").val();
            },
            //获取连接信息
            getProcessInfo: function () {
                try {
                    //连接关系
                    var aProcessData = {};
                    $("#vtiger_process_info input[type=hidden]").each(function (i) {
                        var processVal = $(this).val().split(",");
                        if (processVal.length == 2) {
                            if (!aProcessData[processVal[0]]) {
                                aProcessData[processVal[0]] = {"top": 0, "left": 0, "process_to": [],"process_from":[]};
                            }
                            if (!aProcessData[processVal[1]]) {
                                aProcessData[processVal[1]] = {"top": 0, "left": 0, "process_to": [],"process_from":[]};
                            }
                            aProcessData[processVal[0]]["process_to"].push(processVal[1]);
                            aProcessData[processVal[1]]["process_from"].push(processVal[0]);
                        }
                    })
                    //位置
                    _canvas.find("div.process-step").each(function (i) { //生成Json字符串，发送到服务器解析
                        if ($(this).attr('id')) {
                            var pId = $(this).attr('process_id');
                            var pLeft = parseInt($(this).css('left'));
                            var pTop = parseInt($(this).css('top'));
                            if (!aProcessData[pId]) {
                                aProcessData[pId] = {"top": 0, "left": 0, "process_to": [],"process_from":[]};
                            }
                            aProcessData[pId]["top"] = pTop;
                            aProcessData[pId]["left"] = pLeft;

                        }
                    })
                    return JSON.stringify(aProcessData);
                } catch (e) {
                    return '';
                }
            },
			//清理
            clear: function () {
                try {

                    jsPlumb.detachEveryConnection();
                    jsPlumb.deleteEveryEndpoint();
                    $('#vtiger_process_info').html('');
                    jsPlumb.repaintEverything();
                    return true;
                } catch (e) {
                    return false;
                }
            },
			//刷新
			refresh: function () {
                try {
                    this.clear();
                    _canvas_design();
                    return true;
                } catch (e) {
                    return false;
                }
            }
        };
        return vtFlowdesign;


    }//$.fn
})(jQuery);