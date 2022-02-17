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
        .label{
            border:1px solid #ccc;color:#000;
        }
        .label span{
            color:#000;
        }
        .label-0{
        	/*background: #549BA3;*/
            background: #F89406;
            /*border:1px solid #ff2222;*/
            -moz-border-radius: 3px;      /* Gecko browsers */
            -webkit-border-radius: 3px;   /* Webkit browsers */
            border-radius:3px;            /* W3C syntax */
        }.label-1{
        	/*background: #008AD9;*/

             background: #468847;
             /*border:1px solid #006600;*/
             -moz-border-radius: 3px;      /* Gecko browsers */
             -webkit-border-radius: 3px;   /* Webkit browsers */
             border-radius:3px;            /* W3C syntax */
         }.label-2{
         	  /*background: #6178DA;*/
                               background: #B94A48;
             /* border:1px solid #3300aa;*/
              -moz-border-radius: 3px;      /* Gecko browsers */
              -webkit-border-radius: 3px;   /* Webkit browsers */
              border-radius:3px;            /* W3C syntax */
          }
        .icon-ok {
            background-color: #ff2222;
        }
        .process-step span {
            cursor: pointer;
        }
    </style>
    <div>
        <div class="label label-0 " style="color:#fff;left:36px;top:108px;">还未审核</div>
        <div class="label label-1 " style="color:#fff;left:36px;top:108px;">正在审核</div>
        <div class="label label-2 " style="color:#fff;left:36px;top:108px;">已经审核</div>
    </div>
    <input type="hidden" id="clickid" value="{$CLICKID}">
    <input type="hidden" id="row_type">
    <input type="hidden" id="crmid" value="{$CRMID}">
    <span id="customspan"></span>
    <div class="contentsDiv mini-layout" id="flowdesign_canvas" style="min-height:800px;background-color: #cccccc">
    </div>

    <script type="text/javascript">
        var safeinput = '<input type="hidden" name="'+csrfMagicName+'" value="'+csrfMagicToken+'">';
        $("#customspan").html(safeinput);

        var the_flow_id = '{$AUTOFOLOID}';    //流程id
        /*步骤数据*/
        {literal}
        var processData = {
		{/literal}
{if !empty($MODULE_MODLE)}
        "total": {$MODULE_MODLE|count}, "list":{literal}{{/literal}
    {foreach item=MODEL from=$MODULE_MODLE}
    "{$MODEL['autoworkflowtaskid']}"
    {literal}:{{/literal}
        "id": "{$MODEL['autoworkflowtaskid']}",
        "flow_id": "{$MODEL['autoworkflowid']}",
        "process_name": "{$MODEL['autoworkflowtaskname']}",
        "process_to": "{$MODEL['process_to']}",
        "process_from": "{$MODEL['process_from']}",
        "icon": "{$MODEL['icon']}",
        "style": "{$MODEL['style']}",                 
        "action": "label label-{if $MODEL['process_from'] eq ''&& $MODEL['isaction'] eq '0'}1{else}{$MODEL['isaction']}{/if}",  
        "info": '',
        "status":'{$MODEL['isaction']}'
        {literal} }{/literal}{if $MODEL@last}{else},{/if}
    {/foreach}
    	{literal}}{/literal}
       {/if}
{literal}
        };
        $(function () {
            /*创建流程设计器*/
            var _canvas = $("#flowdesign_canvas").vtFlowdesign({
                "processData": processData,					//数据初始化
                isdelete:false,
                isdraggable:false,
                fnClick: function () {
                    var activeId = _canvas.getActiveId();
                   // console.log(processData);
					var actionstatus = processData.list[activeId]['status'] //0 未开始 1激活 2 结束
					var title = processData.list[activeId]['process_name'];
					var from = processData.list[activeId]['process_from'];
					var to = processData.list[activeId]['process_to'];
					var ispermission = processData.list[activeId]['icon'];
					if((actionstatus !== "2" && from=="")||actionstatus=="1"){
						if(ispermission == "icon-ok"){
							//wangbin 2015年7月21日 加载 审核弹出框;
			                   var params = {};
			                   params['mode'] = 'getAudittpl';
			                   params['action'] = 'BasicAjax';
			                   params['module'] = 'AutoTask';
			                   params['autoworkflowtaskid'] = activeId;
			                   params['crmid'] = $("#crmid").val();
			                   var d = {};
			                   d.data = params;
			                   d.type = 'GET';
			                   AppConnector.requestPjaxPost(d).then(function (data) {
			                       var msg = {
			                           title: "审核<<"+title+">> 节点",
			                           width:"1000px",
			                           modal:false,
			                           message: data,
			                           form: 'showaudit',
			                       };
			                        Vtiger_Helper_Js.showPubDialogBox1(msg).then(function (e) {
			                           //mode 'saveAutoworkflowTaskdetail'; action = TaskAjax
                                        var params = $('#showaudit').serialize();
			                          	   params+="&autoflowtaskid="+activeId+"&autoflowid="+the_flow_id;
			                           AppConnector.request(params).then(function (data) {
			                               //Vtiger_Helper_Js.showNotify('设置成功');
			                               location.reload();
			                           });
			                       });
			                   });
						}else if(ispermission =="icon-warning-sign"){
							alert("你没有权限审核当前节点");
						}
						
					}else if(actionstatus == 0 && from !==""){
						alert("当前节点未开始");
					}else if(actionstatus == 2){
						alert("当前节点已经结束");
					}
                    
                  //end
                  
                }
            });
        });
        function getworkflowid() {
            return the_flow_id;
        }

{/literal}
    </script>
{/strip}