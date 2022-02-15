{strip}
  <div class="model" id="quickmodel"  tabindex='0'>
        <div class="modal-header contentsBackground">
        回复
        </div>
        
       <form class="form-horizontal recordEditView" id="QuickCreate" name="QuickCreate" method="post" action="#">
        <input type="hidden" name="modcommentsid" id="modcommentsid" value="{$Modcommentsid}" />
        <input type="hidden" name="id" id="id" value="{$RECORD}" />
        <div class="modal-body tabbable" style="padding:0px">
        <div class="tab-content" style="height:300px;">
        	<div class="tab-content overflowVisible">
        	<table class="massEditTable table">
        	
				{if empty($COMMENT) eq false}
				
				<tr><td colspan="2">回复人：{$COMMENT['createdbyer']} 回复时间：{$COMMENT['createdtime']}</td></tr>
				<tr><td width="100">回复内容(<font color=red>*</font>)</td><td align="left">
				<textarea style="width:500px;height:80px;" class='modcommenthistory' data-validation-engine="validate[required,maxSize[500]]" >{$COMMENT['modcommenthistory']}</textarea></td></tr>
				{else}
					<tr><td colspan="2">回复人：{$CURRENTUSER->last_name} 回复时间：{php}echo date('Y-m-d H:i:s',time());{/php}</td></tr>
					<tr><td width="100">回复内容(<font color=red>*</font>)</td><td align="left">
							<textarea class='modcommenthistory' style="width:500px;height:80px;" data-validation-engine="validate[required,maxSize[500]]" ></textarea></td></tr>

				{/if}
				{*<tr><td colspan="2">修改人：*}
				{*{if empty($COMMENT['modifiedbyer'])}{$CURRENTUSER->last_name}{else}{$COMMENT['modifiedbyer']}{/if}*}
				{*{$CURRENTUSER->last_name} 修改时间：{if empty($COMMENT['modifiedtime'])}{php}echo date('Y-m-d H:i:s',time());{/php}{else}{$COMMENT['modifiedtime']}{/if}</td></tr>*}
        		
        		{*{else}*}
        		{*<tr><td colspan="2">评论人：{$CURRENTUSER->last_name} 评论时间：{php}echo date('Y-m-d H:i:s',time());{/php}</td></tr>*}
				{*<tr><td width="100">评论内容(<font color=red>*</font>)</td><td align="left">*}
				{*<textarea class='modcommenthistory' style="width:500px;height:80px;" data-validation-engine="validate[required,maxSize[500]]" ></textarea></td></tr>*}

        		{*{/if}*}
				{*{if $ACCOUNTINTENTIONALITY neq ''}*}
					{*<tr>*}
						{*<td>意向度评(<font color="red">*</font>)：</td>*}
						{*<td>*}
							{*<select class="accountintentionality2" name="accountintentionality2">*}
								{*<option value="">请选择一个选项</option>*}
								{*{foreach key=index item=COMMENTtype from=$ACCOUNTINTENTIONALITY}*}
									{*<option value="{$COMMENTtype}">{vtranslate($COMMENTtype, 'ModComments')}</option>*}
								{*{/foreach}*}
							{*</select>*}
						{*</td>*}
					{*</tr>*}

				{*{/if}*}
        		</table>
        	</div>
        </div>
        
        </div>
    	<div class="modal-footer">
    	
    	<a class="cancelLink cancelLinkContainer pull-right" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL', 'Vtiger')}</a>
    	<button class="btn btn-success subcommentsbut"  type="submit"  ><strong>{vtranslate('LBL_SAVE', 'Vtiger')}</strong></button>
    	
		</div>
		</form>
   </div>
   
<script>
	$('.subcommentsbut').live('click',function(event){
		jQuery('#QuickCreate').validationEngine('attach', {
			bindMethod: 'live'
		});
		
		$(this).addClass('disabled');

		
		if(jQuery('#QuickCreate').validationEngine('validate')){
			event.preventDefault();
			$('.cancelLink').trigger('click');

			var params = {
				'module' : 'ModComments',
				'action' : 'SubSave',
				'src_record' : $('#modcommentsid').val(), 
				'type' : 'POST',
				'modcommenthistory':$('.modcommenthistory').val(),
				'modifiedcause':$('.modifiedcause').val(),
				'edit':$('#id').val(),

			};
			var p={
				mode:"hide"
			};
			var pp={};
			AppConnector.request(params).then(function(data){
				
				if(data.success==true){
				    console.log(data);
					pp={
					type:'success',text:'成功'
					};
					
				}else{
					pp={
					type:'error',text:'失败'+data.result
					};
					Vtiger_Helper_Js.showMessage(pp);
					return;
				}
				
			},function(){}).then(function(){
							if($('div').hasClass('widgetContainer_0')){
								var widgetList = jQuery('.widgetContainer_0');
								var vdjs=new Vtiger_Detail_Js;
								vdjs.loadWidget(widgetList);
							}else{
								$('li[data-label-key="ModComments"]').trigger("click");
							}
							
			},function(){});
			
			return false;
		}
		
		
	
		return false;
	});
	
</script>					
{/strip}