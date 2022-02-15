{*<!--<a href="?module=SalesorderProjectTasksrel&view=Edit&salesorderid={$RECORD}" class="btn btn-primary" target="_black">工单任务生成</a>
 	&nbsp;-->*}
{strip}
<style>
.n_hauto { height:20px;overflow:hidden;display: block;padding-left:4px;}
</style>
    <table class="table table-bordered ">
        <thead>
        <th colspan="1" class="detailViewBlockHeader">{vtranslate('LBL_WORKSTAGES_INFO','SalesOrder')}{if isset($WORKFLOWSNAME)}---{$WORKFLOWSNAME}{/if}
        </th>
        <th colspan="2" class="detailViewBlockHeader" style="text-align:right">
            <div class="form-inline">
                {if $ISROLE and $STAGERECORDID}

                    {*<!-- 2015年3月31日 星期二 注释
                    <select style="width:80px;" name="schedule" class="schedule">
                        <option value="0">0%</option>
                        <option value="20">20%</option>
                        <option value="50">50%</option>
                        <option value="80">80%</option>
                    </select>
                   -->*}

                    {if !empty($DATA)}
                        {*<a href="?module={$DATA['module']}&view=Edit&record={$DATA['record']}" class="btn btn-primary" target="_black">数据</a>*}
                        <input name="datamodule" id="datamodule" type="hidden" disabled value="{$DATA['module']}"/>
                        <input name="datamodulerecord" id="datamodulerecord" type="hidden" disabled  value="{$DATA['record']}"/>
                    {/if}
                    &nbsp;

                    <div class="btn-group">
                    <button type="button" class="btn stagesubmit">审核</button>
                    </div>
                {/if}{*暂时隐藏  下期 By Joe @20150508*}
                <!--
                &nbsp;
                <button class="btn stagereset" data-name="SalesOrder" type="button" data-url="index.php" id="rejectbutton">打回</button>
                &nbsp;
                <button class="btn stagereset" data-name="SalesOrder" type="button" data-url="index.php" id="remarkbutton">备注</button>
                &nbsp;
               <button class="btn stagereset" data-name="SalesOrder" type="button" data-url="index.php" id="SalesorderProjectTasksrel">工单项目任务</button>-->
                &nbsp;
            </div>

        </th>
        </thead>
        <tbody>
        <tr>
            <td colspan="3">
                <div style="padding:5px;">
                    {assign var=SCHEDULE value='0'}
                    {assign var=actiontime value='-'}
                    <ul class="nav nav-pills">
                        {foreach key=index item=vals from=$STAGES}
                            <li style="float: none;vertical-align: middle;display: inline-block;">
                                {foreach from=$vals item=val}
                                    <span class="label {if $val['isaction'] eq 2} label-inverse {elseif $val['check'] eq 1} label-success {elseif $val["isaction"] eq 1} label-info{/if} "
                                          title="{$val['actiontime']}">{$val["workflowstagesname"]}</span>
                                    {if $val['isaction'] eq 1 }
                                        {assign var=SCHEDULE value=$val['schedule']}

                                        {if $val['check'] eq 1}
                                            <input id="stagerecordid" type="hidden"
                                                   value="{$val['salesorderworkflowstagesid']}"/>
                                            <input id="stagerecordname" type="hidden"
                                                   value="{$val['workflowstagesname']}"/>
                                            {assign var=actiontime value=$val["actiontime"]}
                                        {/if}
                                    {/if}
                                    <br>
                                {/foreach}
                            </li>
                            {if $vals@last }
                            {else}
                                <li style="float: none;vertical-align: middle;display: inline-block;">
                                    <i class="icon-arrow-right" style=""></i></li>
                            {/if}
                        {/foreach}
                    </ul>
                </div>
                <script>
                    $(function () {
                        $('.schedule').val({$SCHEDULE});
                        $('.schedule option').each(function () {
                            if ($(this).val() <={$SCHEDULE}) {
                                $(this).attr('disabled', 'disabled');
                            }
                        });
                    });
                </script>
                <!-- 2015年3月31日 星期二 注释
    	<div class="progress progress-info">
	  		<div class="bar" data-schedule="{$SCHEDULE}" style="width: {$SCHEDULE}%">{$SCHEDULE}%</div>
		</div>
    -->
            </td>
        </tr>
        <tr>
            <td colspan="3">
                图例说明：<span class="label  label-inverse" title="">已经审核的节点</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span class="label label-success" title="">正在审核(有权限)的节点</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span class="label label-info" title="">正在审核(无权限)的节点</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span class="label" title="">即将审核的节点</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </td>
        </tr>
        </tbody>
    </table>
    <!-- wangbin 注释 打回到指定节点-->
    {*<div id="test">
    {if $ISROLE and $STAGERECORDID}
               <table class="table table-bordered">
                       <form action="" method="post">
                           <input id="backstagerecordname" type="hidden" value="{$STAGERECORDNAME}"/>
                        <input id="backstagerecordeid" type="hidden" value="{$STAGERECORDID}"/>
                        <tr>
                            <td colspan="2"><textarea rows="3" id="rejectreason" style="width:100%" placeholder="请输入打回原因"></textarea></td>
                            <td>
                            <select id="chooseback">
                                        <option stagename>--请选择打回节点--</option>
                                    {foreach key=index item=val from=$STAGES}
                                          {if $val['isaction'] eq 2}
                                          <option value={$val['salesorderworkflowstagesid']} stagename={$val["workflowstagesname"]}>{$val["workflowstagesname"]}</option>
                                          {/if}
                                      {/foreach}
                            </select>
                            </td>
                            <td><button type="button" class="btn btn-large btn-primary" id="realstagereset">打回</button></td>
                        </tr>
                    </form>
            </table>
            {/if}
            <table class="table table-bordered" >
                        <thead><tr><th class="detailViewBlockHeader">操作人</th>
                        <th class="detailViewBlockHeader">从节点打回</th>
                        <th class="detailViewBlockHeader">至节点</th>
                        <th  class="detailViewBlockHeader">原因</th>
                        <th class="detailViewBlockHeader">日期</th></tr></thead>
                    {foreach key=index item=value from=$SALESORDERHISTORY}
                    <tr>
                        <td>{$value["last_name"]}</td>
                        <td>{$value["rejectnameto"]}</td>
                        <td>{$value["rejectname"]}</td>
                        <td>{$value["reject"]}</td>
                        <td>{$value["rejecttime"]}</td>
                    </tr>
                    {/foreach}
            </table>
    </div>*}

        <table class="table table-bordered mergeTables detailview-table ">
            <thead>
            <tr>
                <th class="detailViewBlockHeader" >
                    <img class="cursorPointer alignMiddle blockToggle  hide  "
                         src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="jd61"
                         style="display: none;">
                    <img class="cursorPointer alignMiddle blockToggle "
                         src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="jd61">
                    节点名称
                </th>
                <th class="detailViewBlockHeader" >状态</th>
                <th class="detailViewBlockHeader" >激活时间</th>
                <th class="detailViewBlockHeader" >角色</th>
                <th class="detailViewBlockHeader" >产品负责人</th>
                <th class="detailViewBlockHeader" >负责人</th>
                <th class="detailViewBlockHeader" >审核人</th>
                <th class="detailViewBlockHeader" >审核时间</th>
                <th class="detailViewBlockHeader" >日期</th>
            </tr>
            </thead>
            {foreach key=index item=value from=$WORKFLOWSSTAGELIST name=listview}
                <tr">
                    <td >{$value["workflowstagesname"]}</td>
                    <td >
                    {$value["actionstatus"]}
                    {*{if $value["isaction"] eq 2}
                    <button class="btn btn-link resetaction" data-id="{$value["salesorderworkflowstagesid"]}" data-name="{$value["workflowstagesname"]}" type="button">激活</button>
                    {/if}*}
                    </td>
                    <td >{$value["actiontime"]}</td>
                    <td >{$value["isrole"]}</td>
                    <td ><a><i class="icon-hand-right pull-left hide" style="margin-top:4px">&nbsp;</i>
					<span class="n_hauto" data-trigger="hover" data-content="{$value["productid"]}">{$value["productid"]}</span></a></td>
                    <td ><a><i class="icon-hand-right pull-left hide" style="margin-top:4px">&nbsp;</i>
					<span class="n_hauto {if $canChangeAuditor}auditorid{else}noauditorid{/if}" data-workflowstagesname="{$value['workflowstagesname']}" data-salesorderworkflowstagesid="{$value['salesorderworkflowstagesid']}" data-trigger="hover" data-content="{$value["higherid"]}">{$value["higherid"]}</span></a></td>
                <td >{$value["auditorid"]}</td>
                    <td >{$value["auditortime"]}</td>
                    <td >{$value["createdtime"]}</td>
                </tr>
            {/foreach}
        </table>

<br>
        <table class="table table-bordered equalSplit detailview-table ">
            <thead>
            	{if $ISROLE and $STAGERECORDID}
	            <tr>
	                <td colspan="3">
	                    <textarea class="row-fluid" required="required" id="rejectreason" placeholder="请输入打回原因"></textarea>
	                </td>
	                <td>
	                    <div class="pull-right"><button type="button" class="btn btn-warning" id="realstagereset">打回 </button></div>
	            	</td>
	            </tr>
            	{/if}
            <tr>
                <th class="detailViewBlockHeader" nowrap>
                    <img class="cursorPointer alignMiddle blockToggle hide" src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="61" style="display: none;">
                    <img class="cursorPointer alignMiddle blockToggle" src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="61">操作人
                </th>
                <th class="detailViewBlockHeader" nowrap>从节点打回</th>
                <th class="detailViewBlockHeader" nowrap>原因</th>
                <th class="detailViewBlockHeader" nowrap>日期</th>
            </tr>
            </thead>
            {foreach key=index item=value from=$SALESORDERHISTORY}
                <tr>
                    <td nowrap>{$value["last_name"]}</td>
                    <td nowrap>{$value["rejectnameto"]}</td>
                    <td>{$value["reject"]}</td>
                    <td nowrap>{$value["rejecttime"]}</td>
                </tr>
            {/foreach}
        </table>
        {assign var=USERDEPARTMENT value=getDepartmentUser('H55')}
        <table class="table table-bordered equalSplit detailview-table ">
            <thead>
            {if ($ISROLE and $STAGERECORDID) || in_array($USER,$USERDEPARTMENT)}
           	<tr class="hide realremarkbutton" nowrap>
            	<td colspan="3"><textarea class="row-fluid remark" id="remarkvalue" required="required" placeholder="请输入备注信息" name="description" ></textarea></td>
            	<td colspan="2"><div class="pull-right"><button type="button" id="realremarkbutton" class="btn btn-success"><strong>保存</strong></button><a class="cancelLink" type="reset" onclick="$('.realremarkbutton').hide();">取消</a></div></td>
            </tr>
            {/if}
			<tr>
                <th class="detailViewBlockHeader" nowrap>
                    <img class="cursorPointer alignMiddle blockToggle hide" src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="61" style="display: none;">
                    <img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="61">操作人</th>
                <!--<div class="btn-group"><button type="button" class="btn stagesubmit">审核</button></div><th class="detailViewBlockHeader" nowrap>备注节点</th>-->
                <th class="detailViewBlockHeader" width="60%">备注原因</th>
                <th class="detailViewBlockHeader" nowrap>创建日期</th>
                <th class="detailViewBlockHeader"nowrap>修改时间</th>
                <th class="detailViewBlockHeader" nowrap>
                {if ($ISROLE and $STAGERECORDID) or in_array($USER,$USERDEPARTMENT)}<div class="pull-right"><button type="button" onclick="$('.realremarkbutton').show();$('#remarkvalue').focus();" class="btn btn-info">添加备注</button>&nbsp;</div>{/if}
                </th>
            </tr>
            </thead>
            {foreach key=index item=value from=$REMARKLIST}
                <tr>
                    <td nowrap><input type="hidden" value="{$value['salesorderhistoryid']}"class="remarkid">
                    	{$value["last_name"]}
                    </td>
                    {*<td nowrap width="10%">{$value["rejectname"]}</td>*}
                    <td width="60%">{$value["reject"]}</td>
                    <td nowrap >{$value["rejecttime"]}</td>
                    <td nowrap>{$value["modifytime"]}</td>
                    <td id="editremark" nowrap width="10%">
                    	{if $USER eq $value["rejectid"] && false}
                    	<a style="color:#00743e;" data-toggle="modal" data-target="#myModal" class="cursorPointer"><i class="icon-edit"></i>编辑</a>
                    	{/if}
                    </td>
                </tr>
            {/foreach}
        </table>

    <div id="projectselectdiv" style="display:none;">
        <select id="projectselect">
            <option>--请选择项目模版--</option>
            {foreach item=projectname from=$PROJECTNAME}
                <option value="{$projectname['0']}">{$projectname['1']}</option>
            {/foreach}
        </select>
        <button type="button" id="realSalesorderProjectTasksrel">工单任务生成</button>
    </div>
    <div id="protaskform" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true" style="z-index:1000006">准备测试数据。。。
    </div>
    <!-- Button to trigger modal -->
    <div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true" style="z-index:1000006">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel">修改备注信息</h3>
        </div>
        <div class="modal-body">
            <p><textarea id="editremarkval" placeholder="键入内容修改备注" style="width:100%"></textarea></p>
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
            <button id="realeditremark" class="btn btn-primary" data-dismiss="modal" aria-hidden="true">确认修改</button>
        </div>
    </div>
	<script type="text/javascript">
		$('.n_hauto').popover();
		$('img[data-id="jd61"]').trigger('click');
		$('.icon-hand-right').each(function(){
			if($(this).next('span').html().indexOf('br')>-1){
				$(this).show();
			}
		})
</script>
{/strip}
