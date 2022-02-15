{strip}
<form class=" form-horizontal" id="setautoworkflowtask">
        	<input type="hidden" value="{$RECORD}" name="autoworkflowtaskid">
            <input type="hidden" value="saveAutoworkflowTaskdetail" name="mode">
            <input type="hidden" value="TaskAjax" name="action">
            <input type="hidden" value="AutoWorkflows" name="module">
            <input type="hidden" value="Settings" name="parent">
			{assign var=ALL_ACTIVEUSER_LIST value=$USER_MODEL->getAccessibleUsers(54)}
                <ul id="myTab" class="nav nav-tabs">
                    <li class="active"><a href="#mail" data-toggle="tab">邮件</a></li>
                    <li class=""><a href="#interface" data-toggle="tab">接口</a></li>
                    <li class=""><a href="#customfun" data-toggle="tab">自定义函数</a></li>
                </ul>
                <div id="myTabContent" class="tab-content">
                   <!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx -->
                    <div class="tab-pane fade active in" id="mail">
                       <table class="table table-condensed">
							<tr class="row">
								<td class="">
								</td>
								<td class="">
								<label class="checkbox"><input type="checkbox" name="mail[ismail]" {if $DATA->ismail eq 'on'}checked{/if}> 是否邮件</label>
								</td>
								<td class="">
									<label>邮件模版</label>
								</td>
								<td class="">
									<select class="chzn-select" name="mail[templates]">
									 {foreach from=$ALLMAIL key=AMAIL_KEY item=AMAIL_VALUE}
									 	<option value="{$AMAIL_VALUE['templateid']}" {if $DATA->templates eq $AMAIL_VALUE['templateid']} selected {/if} > {$AMAIL_VALUE['templatename']}</option>
									 {/foreach}
									 </select>
								</td>
							</tr>
							<tr class="row">
								<td class="">收件人分组</td>
								<td class="">
									 <select name="mail[mailreiveby]" class="chzn-select" id="mailreiveby">
			                         <option value="0" {if $DATA->mailreiveby eq '0'} selected {/if} >按人员</option>
			                         <option value="1" {if $DATA->mailreiveby eq '1'} selected {/if} >按组别</option>
			                         <option value="2" {if $DATA->mailreiveby eq '2'} selected {/if} >按角色</option> 
			                         <option value="3" {if $DATA->mailreiveby eq '3'} selected {/if} >客户首要联系人</option>
                    				 </select>
								</td>
								<td class="">抄送人分组</td>
								<td class="">
									<select name="mail[mailcopyby]" class="chzn-select" id="mailcopyby">
			                         <option value="0" {if $DATA->mailcopyby eq '0'} selected {/if} >按人员</option>
			                         <option value="1" {if $DATA->mailcopyby eq '1'} selected {/if} >按组别</option>
			                         <option value="2" {if $DATA->mailcopyby eq '2'} selected {/if} >按角色</option>
			                         <option value="3" {if $DATA->mailcopyby eq '3'} selected {/if}>客户首要联系人</option>
                    				 </select>
								</td>
							</tr>
							<tr class="row">
								<td class="">收件人</td>
								<td class="">
	<!-- 收件人员分配  -->
            <div id="mailreceivebyperson" class="{if $DATA->mailreiveby neq '0'} hide {/if}">
					<select id="memberList" class="row-fluid members chzn-select "  multiple="true" name="mail[receiveids][]" data-placeholder="请选择收件人"  style="width:225px";>
						{foreach key=DEPARTMENTNAME item=DEPARTMENTNAME_LIST from=$ALL_ACTIVEUSER_LIST}
	                		<optgroup label="{if $DEPARTMENTNAME eq ''} 用户{else} {$DEPARTMENTNAME}{/if}">
			    	            {foreach key=OWNER_ID item=OWNER_NAME from=$DEPARTMENTNAME_LIST}
								{assign var="KEYID" value='Users:'|cat:$OWNER_ID}				               
									<option value="{$OWNER_ID}" data-picklistvalue= '{$OWNER_NAME}' {if $DATA->mailreiveby eq '0' && in_array($OWNER_ID,$DATA->receiveids)}selected{/if}>
				                    	{$OWNER_NAME}
				                    </option>
								{/foreach}
							</optgroup>
						{/foreach}
					</select>
            </div>
             <!-- 收件人组别分配 -->
                   <div id="mailreceivebygroup" class="{if $DATA->mailreiveby neq '1'} hide {/if}">
	                  <select class="chzn-select"  name="mail[receiveids][]" data-placeholder="请选择收件组" multiple="true">
			             {foreach from=$GROUPUSER_MODEL key=GROUP_LABEL item=ALL_GROUP_MEMBERS}
	                         <optgroup label="{vtranslate($GROUP_LABEL, $QUALIFIED_MODULE)}">
	                           {foreach from=$ALL_GROUP_MEMBERS item=MEMBER}
	                               <option value="{$MEMBER->getId()}" {if $DATA->mailreiveby eq '1' && in_array($MEMBER->getId(),$DATA->receiveids)}selected{/if}>
	                            	 {$MEMBER->getName()}
	                         	  </option>
	                      	  {/foreach}
	                  		</optgroup>
	                     {/foreach}
	                 </select>
                  </div>
    <!-- 收件人角色分配  -->
                   <div id="mailreceivebyrole" class="{if $DATA->mailreiveby neq '2'} hide {/if}">
	                  <select class="chzn-select"  name="mail[receiveids][]" data-placeholder="请选择收件角色" multiple="true">
			             {foreach from=$ROLE_MODEL key=ROLE_KEY item=ROLE_VALUE}
	                        <option value="{$ROLE_VALUE->get('roleid')}" {if $DATA->mailreiveby eq '2' && in_array($ROLE_VALUE->get('roleid'),$DATA->receiveids)}selected{/if}>
	                         	{$ROLE_VALUE->get("rolename")}
	                       	</option>
	                     {/foreach}
	                 </select>
	               </div>
     <!-- end -->
								</td>
								<td class="">抄送人</td>
								<td class="">
	<!-- 抄送人分配  -->
            <div id="mailcopybyperson" class="{if $DATA->mailcopyby neq '0'} hide {/if}" >
					<select id="memberLists"   class="row-fluid members chzn-select " multiple="true" name="mail[copyids][]" data-placeholder="请选择超送人" style="width:225px";>
						{foreach key=DEPARTMENTNAME item=DEPARTMENTNAME_LIST from=$ALL_ACTIVEUSER_LIST}
	                		<optgroup label="{if $DEPARTMENTNAME eq ''} 用户{else} {$DEPARTMENTNAME}{/if}">
			    	            {foreach key=OWNER_ID item=OWNER_NAME from=$DEPARTMENTNAME_LIST}
								{assign var="KEYID" value='Users:'|cat:$OWNER_ID}				               
									<option value="{$OWNER_ID}"  {if $DATA->mailcopyby eq 0  && in_array($OWNER_ID,$DATA->copyids)}selected{/if}>
				                    	{$OWNER_NAME}
				                    </option>
								{/foreach}
							</optgroup>
						{/foreach}
					</select>
            </div>
               <!-- 抄送人组别员分配 -->
                   <div id="mailcopybygroup" class="{if $DATA->mailcopyby neq '1'} hide {/if}">
	                  <select class="chzn-select "  name="mail[copyids][]" data-placeholder="请选择抄送组" multiple="true">
			             {foreach from=$GROUPUSER_MODEL key=GROUP_LABEL item=ALL_GROUP_MEMBERS}
	                         <optgroup label="{vtranslate($GROUP_LABEL, $QUALIFIED_MODULE)}">
	                           {foreach from=$ALL_GROUP_MEMBERS item=MEMBER}
	                               <option value="{$MEMBER->getId()}" {if $DATA->mailcopyby eq '1' && in_array($MEMBER->getId(),$DATA->copyids)}selected{/if}>
	                            	 {$MEMBER->getName()}
	                         	  </option>
	                      	  {/foreach}
	                  		</optgroup>
	                     {/foreach}
	                 </select>
                  </div>
    <!-- 抄送角色分配  -->
                   <div id="mailcopybyrole" class="{if $DATA->mailcopyby neq '2'} hide {/if}">
	                  <select class="chzn-select"   name="mail[copyids][] data-placeholder="请选择抄送角色" multiple="true">
			             {foreach from=$ROLE_MODEL key=ROLE_KEY item=ROLE_VALUE}
	                        <option value="{$ROLE_VALUE->get('roleid')}" {if $DATA->mailcopyby eq '2' && in_array($ROLE_VALUE->get('roleid'),$DATA->copyids)}selected{/if}>
	                         	{$ROLE_VALUE->get("rolename")}
	                       	</option>
	                     {/foreach}
	                 </select>
	               </div>
     <!-- end -->
								</td>
							</tr>
							<tr class="row">
								<td class="">自定义收件人</td>
								<td class="">
				  <div id="mailcusomreceivebyperson">
					<select id="mailcusomreceivebyperson" class="row-fluid members chzn-select " multiple="true" name="mail[cusomreceiveids][]" data-placeholder="自定义收件人" data-validation-engine="validate[required]" style="width:225px";>
						{foreach key=DEPARTMENTNAME item=DEPARTMENTNAME_LIST from=$ALL_ACTIVEUSER_LIST}
	                		<optgroup label="{if $DEPARTMENTNAME eq ''} 用户{else} {$DEPARTMENTNAME}{/if}">
			    	            {foreach key=OWNER_ID item=OWNER_NAME from=$DEPARTMENTNAME_LIST}
								{assign var="KEYID" value='Users:'|cat:$OWNER_ID}				               
									<option value="{$OWNER_ID}" data-picklistvalue= '{$OWNER_NAME}' {if in_array($OWNER_ID,$DATA->cusomreceiveids)}selected{/if}>
				                    	{$OWNER_NAME}
				                    </option>
								{/foreach}
							</optgroup>
						{/foreach}
					</select>
            </div>
								</td>
								<td class="">自定义抄送人</td>
								<td class="">
			<div id="mailcusomcopybyperson">
				<select id="mailcusoncopybyperson" class="row-fluid members chzn-select " multiple="true" name="mail[cusomdopyids][]" data-placeholder="自定义抄送人" data-validation-engine="validate[required]" style="width:225px";>
					{foreach key=DEPARTMENTNAME item=DEPARTMENTNAME_LIST from=$ALL_ACTIVEUSER_LIST}
                		<optgroup label="{if $DEPARTMENTNAME eq ''} 用户{else} {$DEPARTMENTNAME}{/if}">
		    	            {foreach key=OWNER_ID item=OWNER_NAME from=$DEPARTMENTNAME_LIST}
							{assign var="KEYID" value='Users:'|cat:$OWNER_ID}				               
								<option value="{$OWNER_ID}" data-picklistvalue= '{$OWNER_NAME}' {if in_array($OWNER_ID,$DATA->cusomdopyids)}selected{/if}>
			                    	{$OWNER_NAME}
			                    </option>
							{/foreach}
						</optgroup>
					{/foreach}
					</select>
            </div>
								</td>
							</tr>
					   </table>
                    </div>
                      <!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx -->
                    <div class="tab-pane fade" id="interface">
                     	接口
                    </div>
                      <!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx -->
                    <div class="tab-pane fade" id="customfun" >
						 <table class="table table-condensed" >
							<tr><td>前置函数：</td><td><input type="text" value="{$FUNC->pre}" name="func[pre]"></td></tr>
							<tr><td>中置函数：</td><td><input type="text" value="{$FUNC->middle}" name="func[middle]"></td></tr>
							<tr><td>后置函数：</td><td><input type="text" value="{$FUNC->after}" name="func[after]"></td></tr>
						 </table>
				   </div>
      </div>
</form>
{literal}
<script>
	new (Settings_AutoWorkflows_EditTask_Js); // 测试有效
</script>
{/literal}
{/strip}