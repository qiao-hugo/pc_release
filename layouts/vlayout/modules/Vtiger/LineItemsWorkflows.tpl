{strip}
<table class="table table-bordered ">
    <thead>
    <th colspan="1" class="detailViewBlockHeader">{vtranslate('LBL_WORKSTAGES_INFO','SalesOrder')}{if isset($WORKFLOWSNAME)}---{$WORKFLOWSNAME}{/if}
    </th><th colspan="2" class="detailViewBlockHeader" style="text-align:right">
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
	<a href="?module={$DATA['module']}&view=Edit&record={$DATA['record']}" class="btn btn-primary" target="_black">数据</a>
	{/if}
 	&nbsp;
 	<button type="button" class="btn stagesubmit btn-primary">完成</button>
 	{/if}
 	&nbsp;
 	<button class="btn stagereset" data-name="SalesOrder" type="button" data-url="index.php" id="rejectbutton">打回</button>
 	&nbsp;
 	<button class="btn stagereset" data-name="SalesOrder" type="button" data-url="index.php" id="remarkbutton">备注</button>
 	</div>

    </th></thead>
    <tbody>
    <tr>
    <td colspan="3">
    <div style="padding:5px;">
    {assign var=SCHEDULE value='0'}
    {assign var=actiontime value='-'}
    {foreach key=index item=val from=$STAGES}

    <span class="label {if $val['isaction'] eq 2} label-inverse {elseif $val['check'] eq 1} label-success {elseif $val["isaction"] eq 1} label-info{/if} " title="{$val['actiontime']}">{$val["workflowstagesname"]}</span>
	{if $val['isaction'] eq 1 }
    {assign var=SCHEDULE value=$val['schedule']}
    {if $val['check'] eq 1}
	    <input id="stagerecordid" type="hidden" value="{$val['salesorderworkflowstagesid']}" />
	    <input id="stagerecordname" type="hidden" value="{$val['workflowstagesname']}" />
    {assign var=actiontime value=$val["actiontime"]}
	{/if}
    {/if}
    {if $index lt ($STAGESCOUNT-1)}
    <i class="icon-arrow-right" style=""></i>
    {/if}
    {/foreach}
    </div>
    <script>
    $(function(){
    	$('.schedule').val({$SCHEDULE});
    	$('.schedule option').each(function(){
    		if($(this).val()<={$SCHEDULE}){
    			$(this).attr('disabled','disabled');
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
        <tr><td colspan="3">
    	图例说明：<span class="label  label-inverse"  title="">已经审核的节点</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    	<span class="label label-success"  title="">正在审核(有权限)的节点</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  	 	<span class="label label-info"  title="">正在审核(无权限)的节点</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
   		<span class="label"  title="">即将审核的节点</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    </td></tr>
    </tbody>
</table>

{*<!-- wangbin 注释 打回到指定节点 <div id="test" style="display:block;">
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
</div>-->*}
<div id="test" style="display:block;">
{if $ISROLE and $STAGERECORDID}
   		<table class="table table-bordered">
		   		<form action="" method="post">
		   			<input id="backstagerecordname" type="hidden" value="{$STAGERECORDNAME}"/>
		    		<input id="backstagerecordeid" type="hidden" value="{$STAGERECORDID}"/>
		    		<tr>
		    			<td><textarea  id="rejectreason" style="width:100%" placeholder="请输入打回原因"></textarea></td>
		    			<td><button type="button" class="btn btn-large btn-primary pull-right" id="realstagereset">打回</button></td>
		    		</tr>
		    	</form>
		</table>
		{/if}
		<table class="table table-bordered" >
    				<thead><tr><th class="detailViewBlockHeader">操作人</th>
    				<th class="detailViewBlockHeader">从节点打回</th>
    				<th  class="detailViewBlockHeader">原因</th>
    				<th class="detailViewBlockHeader">日期</th></tr></thead>
    			{foreach key=index item=value from=$SALESORDERHISTORY}
    			<tr>
    				<td>{$value["last_name"]}</td>
    				<td>{$value["rejectnameto"]}</td>
    				<td>{$value["reject"]}</td>
    				<td>{$value["rejecttime"]}</td>
    			</tr>
    			{/foreach}
   	 </table>
</div>
<div id="remarkdiv" style="display:block;">
	{if $ISROLE and $STAGERECORDID}
	<table class="table table-bordered">
		<tr><td><textarea  id="remarkvalue"  placeholder="请输入备注内容" style="width:100%"></textarea></td>
		<td><button type="button" class="btn btn-large btn-primary pull-right" id="realremarkbutton">添加备注</button></td></tr>
	</table>
	{/if}
	<table class="table table-bordered table-condensed">
    				<thead><tr>
    				<th class="detailViewBlockHeader">操作人</th>
    				<th class="detailViewBlockHeader">备注节点</th>
    				<th  class="detailViewBlockHeader">原因</th>
    				<th class="detailViewBlockHeader">创建日期</th>
    				<th class="detailViewBlockHeader">修改时间</th>
    				<th class="detailViewBlockHeader">操作</th></tr></thead>
    			{foreach key=index item=value from=$REMARKLIST}
    			<tr>
    				<td><input type="hidden" value="{$value['salesorderhistoryid']}" class="remarkid">{$value["last_name"]}</td>
    				<td>{$value["rejectnameto"]}</td>
    				<td>{$value["reject"]}</td>
    				<td>{$value["rejecttime"]}</td>
    				<td>{$value["modifytime"]}</td>
    				<td id="editremark">{if $USER eq $value["rejectid"]}<button class="btn btn-primary" type="button" data-toggle="modal" data-target="#myModal">修改</button>{else}<button class="btn btn-primary" disabled>修改</button>{/if}</td>
    			</tr>
    			{/foreach}
   	 </table>
</div>
<!-- Button to trigger modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="z-index:1000006">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">修改备注信息</h3>
  </div>
  <div class="modal-body">
    <p><textarea id="editremarkval" placeholder="键入内容修改备注" style="width:100%"></textarea></p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
    <button id="realeditremark" class="btn btn-primary" data-dismiss="modal" aria-hidden="true" >确认修改</button>
  </div>
</div>
{/strip}
