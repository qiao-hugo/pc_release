{strip}
{*var_dump($DATA)*}
	<input type="hidden" id="isedit" value="{$ISEDIT}">
  	{if $ISEDIT}
        <form class=" form-horizontal"  id="showaudit">
                <input type="hidden" id="test" value="{$USER_MAIL['type']['0']}">
	            <input type="hidden" value="submitaudit" name="mode">
	            <input type="hidden" value="BasicAjax" name="action">
	            <input type="hidden" value="AutoTask" name="module">
	           	<input type="hidden" value="{$CRMID}" name="crmid">
	           	<input type="hidden"  value="{$BASEDATA['0']}" name="taskid">
	           	<input type="hidden" value="{$BASEDATA['1']}" name="flowid">

	           	{assign var=ALL_ACTIVEUSER_LIST value=$USER_MODEL->getAccessibleUsers(54)}
	<ul id="myTab" class="nav nav-tabs">
		<li class="active"><a href="#auditors" data-toggle="tab">审核</a></li>
		<li class="" disabled="disabled"><a href="#mail" data-toggle="tab">邮件</a></li>
	</ul>
	<div id="myTabContent" class="tab-content">
		<div class="tab-pane fade active in" id="auditors">
			 <div class="control-group">
	                <label class="control-label" for="inputPassword">备注</label>
	                <div class="controls">
	                    <textarea rows="3" id="inputPassword" name="taskremark">{$DATA['taskremark']}</textarea>
	                </div>
	            </div>
	           <div class="control-group">
		           <div class="controls">
		           		<label class="checkbox"><input type="checkbox" name="pauseaudit" {if $DATA['pauseaudit'] eq 1 }checked {/if}>暂时不提交审核</label>
		           </div>
	           </div>
		</div>
		<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx -->
		<div class="tab-pane fade" id="mail">
			<table class="table table-condensed">
				<tr><td><input type="checkbox" {if $ISMAIL eq "on"}checked{/if} name="mail[issendit]">&nbsp;&nbsp;&nbsp;发送邮件</td>
                    <td colspan="3" id="emptytixing">
                        {$EMPTYEMAIL}
                    </td>
                </tr>
				<tr>
					<td>主题</td>
					<td colspan="1">
						<input type="text"  name="mail[subject]" value = "{$MAILTEMPLI['subject']}">
					</td>
                    <td>
                        {strip}
                            <div class="fileUploadContainer" xmlns="http://www.w3.org/1999/html">
                                <div class="upload">
                                    <div style="display:inline-block;width:70px;height:30px;overflow: hidden;vertical-align: middle;"  title="文件名请勿包含空格"><div style="margin-top:-2px;">文件名请勿</div><div style="margin-top:-5px;">包含空格</div></div>
                                    <input type="button" id="uploadButton" value="上传"  title="文件名请勿包含空格" />
                                    <div style="display:inline-block" id="fileall">
                                        {if !empty($FIELD_VALUE[0])}
                                            {foreach item=NEWFILD from=$FIELD_VALUE}
                                                {assign var=NFIELD_VALUE value=explode('##',$NEWFILD)}
                                                <span class="label file{$NFIELD_VALUE[1]}" style="margin-left:5px;">{$NFIELD_VALUE[0]}&nbsp;<b class="deletefile" data-class="file{$NFIELD_VALUE[1]}" data-id="{$NFIELD_VALUE[1]}" title="删除文件" style="display:inline-block;width:12px;height:12px;line-height:12px;text-align:center">x</b>&nbsp;</span>
                                                <input class="ke-input-text file{$NFIELD_VALUE[1]}" type="hidden" name="file[{$NFIELD_VALUE[1]}]" data-id="{$NFIELD_VALUE[1]}" id="file" value="{$NFIELD_VALUE[0]}" readonly="readonly" />
                                                <input class="file{$NFIELD_VALUE[1]}" type="hidden" name="attachmentsid[{$NFIELD_VALUE[1]}]" value="{$NFIELD_VALUE[1]}">
                                            {/foreach}
                                        {else}
                                            <input class="ke-input-text filedelete" type="hidden" name="file" id="file" value="" readonly="readonly" />
                                            <input class="filedelete" type="hidden" name="attachmentsid" value="">
                                        {/if}
                                    </div>
                                </div>
                            </div>
                        {/strip}
                    </td>
				</tr>
				<tr>
					<td>收件人</td>
					<td>
						<select id="memberReceive" class="row-fluid members chzn-select "  multiple="true" name="mail[receiveids][]" data-placeholder="请选择收件人"  style="width:300px";>
							{foreach key=DEPARTMENTNAME item=DEPARTMENTNAME_LIST from=$ALL_ACTIVEUSER_LIST}
		                		<optgroup label="{if $DEPARTMENTNAME eq ''} 用户{else} {$DEPARTMENTNAME}{/if}">
				    	            {foreach key=OWNER_ID item=OWNER_NAME from=$DEPARTMENTNAME_LIST}
									{assign var="KEYID" value='Users:'|cat:$OWNER_ID}
										<option value="{$OWNER_ID}" data-picklistvalue= '{$OWNER_NAME}' {if  in_array($OWNER_ID,$RECEID)}selected{/if}>{$OWNER_NAME}</option>
									{/foreach}
								</optgroup>
							{/foreach}
                                <option value = "acc##{$ACCOUNTID}"{if $MAIL_ACC_RECEIVE}selected{/if}>客户首要联系人</option>
                        </select>
                    </td>
                    <td><input type="text"  id= "custom_rece" placeholder="收件邮箱,邮箱之间用##隔开" name="mail[custom_rece]" value="" data-validation-engine="validate[]"></td>
                </tr>
                <tr>
					<td>抄送人</td>
					<td>
						<select id="memberCopy" class="row-fluid members chzn-select "  multiple="true" name="mail[copyids][]" data-placeholder="请选择抄送人"  style="width:300px";>
							{foreach key=DEPARTMENTNAME item=DEPARTMENTNAME_LIST from=$ALL_ACTIVEUSER_LIST}
		                		<optgroup label="{if $DEPARTMENTNAME eq ''} 用户{else} {$DEPARTMENTNAME}{/if}">
				    	            {foreach key=OWNER_ID item=OWNER_NAME from=$DEPARTMENTNAME_LIST}
									{assign var="KEYID" value='Users:'|cat:$OWNER_ID}
										<option value="{$OWNER_ID}" data-picklistvalue= '{$OWNER_NAME}' {if  in_array($OWNER_ID,$COPYID)}selected{/if}>{$OWNER_NAME}</option>
									{/foreach}
								</optgroup>
							{/foreach}
                            <option value = "acc##{$ACCOUNTID}"{if $MAIL_ACC_COPY}selected{/if}>客户首要联系人</option>
						</select>
					</td>
                    <td><input type="text"  id= "custom_copy" placeholder="抄送邮箱,邮箱之间用##隔开" name="mail[custom_copy]" value="" data-validation-engine="validate[funcCall[]]"></td>
				</tr>
                {if $DATA['relationmodule'] eq 'Salesorder' &&$DATA['isv1'] neq '1'}<tr><td colspan="3" align="center" style="color: red">该邮件为V1默认版本，非V1版本的请将其他版本直接替代现有模板，word内容复制即可</td></tr>{/if}
                <tr><td>邮件内容</td><td colspan="2"><textarea id="mailcontex" name="mail[mailcontext]">{$MAILTEMPLI['body']}</textarea></td></tr>
            </table>
        </div>
    </div>
            </form>
    {else}
        <div class="row">
	        <form class=" form-horizontal" id="showauditss">
	            <div class="control-group">
	                <label class="control-label">备注</label>
	                <div class="controls">
	                    <textarea rows="3" id="inputPasswords" name="taskremark">没有权限访问，稍后完善</textarea>
	                </div>
	            </div>
	           <div class="control-group">
		           <div class="controls">
		           		<label class="checkbox" readonly="readonly"><input type="checkbox" name="pauseaudit" readonly="readonly">暂时不提交审核</label>
		           </div>
	           </div>
	        </form>
	    </div>
    {/if}
    <script>
    $(document).ready(function(){
    		new(AutoTask_Detailaudit_Js);
    	});
    </script>
{/strip}