{strip}
    <div class="row">
        <form class=" form-horizontal" id="setattribute">
            <input type="hidden" value="{$RECORD}" name="autoworkflowtaskid">
            <input type="hidden" value="saveAttribute" name="mode">
            <input type="hidden" value="TaskAjax" name="action">
            <input type="hidden" value="AutoWorkflows" name="module">
            <input type="hidden" value="Settings" name="parent">
            {assign var=ROLEDETAILS value='##'|explode:$DATA['autodetails']}
            <div class="control-group">
                <label class="control-label" for="autoworkflowtaskname">节点名称</label>
                <div class="controls">
                    <input type="text" id="autoworkflowtaskname" value="{$DATA['autoworkflowtaskname']}" placeholder="" name="data[autoworkflowtaskname]">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="autodatetype">执行日期</label>

                <div class="controls">
                	<span>
                		<select name="data[autodatetype]" id="autodatetype" class="span2 chzn-select">
                        <option value="0" {if $DATA['autodatetype'] eq '0'} selected {/if}>上个节点创建时间</option>
                        <option value="1" {if $DATA['autodatetype'] eq '1'} selected {/if}>上个节点完成时间</option>
                        <option value="2" {if $DATA['autodatetype'] eq '2'} selected {/if}>上个节点激活时间</option>
                    	</select>
                	</span>
                    &nbsp;
                    <div class="input-prepend input-append">
                        <span class="add-on">+</span>
                        <input name="data[autoaddandsubday]" value="{$DATA['autoaddandsubday']}" id="autoaddandsubday" type="number" style="width:50px;">
                        <span class="add-on">天</span>
                    </div>
                 </div>

            </div>
            <div class="control-group">
                <label class="control-label" for="autostatus">默认状态</label>

                <div class="controls">
                    <select name="data[autostatus]"  class="chzn-select" id="autostatus">
                        <option value="0"  {if $DATA['autostatus'] eq '0'} selected {/if}>未开始</option>
                        <option value="1"  {if $DATA['autostatus'] eq '1'} selected {/if}>激活</option>
                        <option value="2"  {if $DATA['autostatus'] eq '2'} selected {/if}>结束</option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label " for="inputPassword">执行人分配</label>
                <div class="controls">
                    <select name="data[autorole]" class="chzn-select" id="autorole">
                         <option value="0" {if $DATA['autorole'] eq '0'} selected {/if}>按人员</option>
                         <option value="1" {if $DATA['autorole'] eq '1'} selected {/if}>按组别</option>
                         <option value="2" {if $DATA['autorole'] eq '2'} selected {/if}>按角色</option>
                    </select>
                </div>
            </div>
    <!-- 人员分配  -->
            <div class="control-group {if $DATA['autorole'] neq '0'} hide {/if} " id="allotbyperson">
				<label class="control-label" for="inputPassword">人员分配</label>
                <div class="controls">						
                	{assign var=ALL_ACTIVEUSER_LIST value=$USER_MODEL->getAccessibleUsers(54)}
					<select id="memberList" class="row-fluid members chzn-select {if $DATA['autorole'] neq '0'}disabled{/if} " multiple="true" name="data[autodetails][]" data-placeholder="人员信息" data-validation-engine="validate[required]" style="width:225px";>
						{foreach key=DEPARTMENTNAME item=DEPARTMENTNAME_LIST from=$ALL_ACTIVEUSER_LIST}
	                		<optgroup label="{if $DEPARTMENTNAME eq ''} 用户{else} {$DEPARTMENTNAME}{/if}">
			    	            {foreach key=OWNER_ID item=OWNER_NAME from=$DEPARTMENTNAME_LIST}
								{assign var="KEYID" value='Users:'|cat:$OWNER_ID}				               
									<option value="{$OWNER_ID}" data-picklistvalue= '{$OWNER_NAME}' {if $DATA['autorole'] eq '0' && in_array($OWNER_ID,$ROLEDETAILS)}selected{/if}>
				                    	{$OWNER_NAME}
				                    </option>
								{/foreach}
								</optgroup>
							{/foreach}
						</select>
				</div>			
            </div>
    <!-- 组别分配 -->
             <div class="control-group {if $DATA['autorole'] neq '1' } hide {/if} " id="allotbygroup">
                  <label class="control-label" for="inputPassword">组别分配</label>
                   <div class="controls">
	                  <select class="chzn-select {if $DATA['autorole'] neq '1'} disabled='disabled' {/if}"   name="data[autodetails][]" multiple="true">
			             {foreach from=$GROUPUSER_MODEL key=GROUP_LABEL item=ALL_GROUP_MEMBERS}
	                         <optgroup label="{vtranslate($GROUP_LABEL, $QUALIFIED_MODULE)}">
	                           {foreach from=$ALL_GROUP_MEMBERS item=MEMBER}
	                               <option value="{$MEMBER->getId()}" {if $DATA['autorole'] eq '1' && in_array($MEMBER->getId(),$ROLEDETAILS)}selected{/if}>
	                            	 {$MEMBER->getName()}
	                         	  </option>
	                      	  {/foreach}
	                  		</optgroup>
	                     {/foreach}
	                 </select>
                  </div>
             </div>
    <!-- 角色分配  -->
                  <div class="control-group {if $DATA['autorole'] neq '2'} hide {/if}"  id="allotbyrole">
                  <label class="control-label" for="inputPassword">角色分配</label>
                   <div class="controls">
	                  <select class="chzn-select {if $DATA['autorole'] neq '2'} disabled {/if} " name="data[autodetails][]" multiple="true">
			             {foreach from=$ROLE_MODEL key=ROLE_KEY item=ROLE_VALUE}
	                        <option value="{$ROLE_VALUE->get('roleid')}" {if $DATA['autorole'] eq '2' && in_array($ROLE_VALUE->get('roleid'),$ROLEDETAILS)}selected{/if}>
	                         	{$ROLE_VALUE->get("rolename")}
	                       	</option>
	                     {/foreach}
	                 </select>
                  </div>
                  </div>
                   <div class="control-group">
                       <div class="controls">
                           <label class="checkbox">
                               <input type="checkbox" name="data[isnotice]" {if $DATA['isnotice'] eq '1'} checked {/if} > 是否通知执行人员
                           </label>
                       </div>
                   </div>
                   <div class="control-group">
                       <label class="control-label" for="inputPassword">通知内容</label>
                       <div class="controls">
                           <textarea rows="3" id="inputPassword" name="data[noticecontext]">{$DATA['noticecontext']}</textarea>
                       </div>
                   </div>
        </form>
    </div>
{literal}
<script>
/* $(function(){
	console.log($('#autoaddandsubday').val());
	$(".chzn-select").select2();}); */
	console.log(new(Settings_AutoWorkflows_EditTask_Js)); // 测试有效
</script>
{/literal}
{/strip}