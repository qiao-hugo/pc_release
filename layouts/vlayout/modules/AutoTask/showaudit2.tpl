{strip}
{*var_dump($DATA)*}
	<input type="hidden" id="isedit" value="{$ISEDIT}">
  	{if $ISEDIT}
	    <div class="row">
	        <form class=" form-horizontal" id="showaudit">
	            <input type="hidden" value="submitaudit" name="mode">
	            <input type="hidden" value="BasicAjax" name="action">
	            <input type="hidden" value="AutoTask" name="module">
	           	<input type="hidden" value="{$CRMID}" name="crmid">
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
	        </form>
	    </div>
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
{/strip}