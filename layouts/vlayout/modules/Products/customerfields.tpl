{assign var = FORMID value = 0}
{if $FORMCONTENT}
{$FORMID=$FORMCONTENT['formid']}
{/if}
<div class="container" style="padding-bottom:150px;">
  <div class="row clearfix">
	<div class="control-group">
    <label class="control-label" for="formid">模版列表</label>
    <div class="controls">
      <select class="chzn-select chzn-done" name="formid" id="formid"  >
		<option>请选择模版</option>
		{foreach item=FORM from=$FORMLIST}
			<option value="{$FORM['formid']}"  {if $FORMID eq $FORM['formid'] }selected{/if}>{$FORM['form_name']}</option>
		{/foreach} 
	  </select>
    </div>
  </div>
	
		<div class="control-group" id="form_show">
		{if $FORMCONTENT}
			<label class="control-label">{$FORMCONTENT['form_name']}</label>
			<div class="controls">
			{htmlspecialchars_decode($FORMCONTENT['content_data'])}
			</div>
		{/if}
		</div>
	
    
   </div> 
</div>