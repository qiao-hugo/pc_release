{strip}
  <div class="model" id="quickmodel"  tabindex='0'>
        <div class="modal-header contentsBackground">
        快速视图 新增\编辑 评论
        </div>
        
       <form class="form-horizontal recordEditView" id="QuickCreate" name="QuickCreate" method="post" action="#">
        <input type="hidden" name="modcommentsid" id="modcommentsid" value="{$Modcommentsid}" />
        <input type="hidden" name="id" id="id" value="{$RECORD}" />
        <div class="modal-body tabbable" style="padding:0px">
        <div class="tab-content" style="height:300px;">
        	<div class="tab-content overflowVisible">
        	<table class="massEditTable table">
        	
				{if empty($COMMENT) eq false}
				
				<tr><td colspan="2">评论人：{$COMMENT['createdbyer']} 评论时间：{$COMMENT['createdtime']}</td></tr>
				<tr><td width="100">评论内容(<font color=red>*</font>)</td><td align="left">
				<textarea style="width:500px;height:80px;" class='modcommenthistory' data-validation-engine="validate[required,maxSize[500]]" >{$COMMENT['modcommenthistory']}</textarea></td></tr>
				<tr><td colspan="2">修改人：
				{if empty($COMMENT['modifiedbyer'])}{$CURRENTUSER->last_name}{else}{$COMMENT['modifiedbyer']}{/if}
				{$CURRENTUSER->last_name} 修改时间：{if empty($COMMENT['modifiedtime'])}{php}echo date('Y-m-d H:i:s',time());{/php}{else}{$COMMENT['modifiedtime']}{/if}</td></tr>
        		
        		{else}
        		<tr><td colspan="2">评论人：{$CURRENTUSER->last_name} 评论时间：{php}echo date('Y-m-d H:i:s',time());{/php}</td></tr>
				<tr><td width="100">评论内容(<font color=red>*</font>)</td><td align="left">
				<textarea class='modcommenthistory' style="width:500px;height:80px;" data-validation-engine="validate[required,maxSize[500]]" ></textarea></td></tr>

        		{/if}
				{if $ACCOUNTINTENTIONALITY neq ''}
					<tr>
						<td>意向度评(<font color="red">*</font>)：</td>
						<td>
							<select class="accountintentionality2" name="accountintentionality2">
								<option value="">请选择一个选项</option>
								{foreach key=index item=COMMENTtype from=$ACCOUNTINTENTIONALITY}
									<option value="{$COMMENTtype}">{vtranslate($COMMENTtype, 'ModComments')}</option>
								{/foreach}
							</select>
						</td>
					</tr>

				{/if}
        		</table>
        	</div>
        </div>
        
        </div>
    	<div class="modal-footer">
    	
    	<a class="cancelLink cancelLinkContainer pull-right" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL', 'Vtiger')}</a>
    	<button class="btn btn-success subcommentsbut"  type="button"  ><strong>{vtranslate('LBL_SAVE', 'Vtiger')}</strong></button>
    	
		</div>
		</form>
   </div>
   
<script>
	var num=0;
	$('.subcommentsbut').live('click',function(event){
		num++;
		$(this).addClass('disabled');
        accountintentionality = $(".accountintentionality2").val();
		{if $ACCOUNTINTENTIONALITY neq ''}
        if(!accountintentionality){
            pp={
                type:'error',text:'意向评估度必填'
            };
            Vtiger_Helper_Js.showMessage(pp);
            event.preventDefault();
            return;
        }
		{/if}
		jQuery('#QuickCreate').validationEngine('attach', {
			bindMethod: 'live'
		});

		if(jQuery('#QuickCreate').validationEngine('validate')){
			event.preventDefault();
			$('.cancelLink').trigger('click');
			if(num!=1){
				return false;
			}
			var params = {
				'module' : 'ModComments',
				'action' : 'SubSave',
				'src_record' : $('#modcommentsid').val(), 
				'type' : 'POST',
				'modcommenthistory':$('.modcommenthistory').val(),
				'modifiedcause':$('.modifiedcause').val(),
				'edit':$('#id').val(),
				'accountintentionality':accountintentionality,
				
			};
			var p={
				mode:"hide"
			};
			var pp={};
			AppConnector.request(params).then(function(data){
				if(data.success==true){
				    console.log(data);
				    if(data.result.accountcategory && accountintentionality!='zeropercentage'){
				        console.log(1111);
						alert("意向度大于0%，当前客户为"+data.result.accountcategory+"客户，领取到正常保护区后客户才会进入意向客户池")
					}
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