{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
-->*}
{strip}
<div class="container-fluid">

	<div class="row-fluid">
		<div class="span12">
			<div class="accordion" id="accordion-31884">
				<div class="accordion-group">
					<div class="accordion-heading">
						<div>
							<h4 style="margin-left:20px;display:inline">未写工作总结人员</h4>
							<div style="display:inline;padding:5px 0;">
								<div class="input-append date" id="datetimepicker" data-date="" data-date-format="yyyy-mm-dd" style="margin:0 20px 0 200px">
   								<input class="span9 dateField" size="16" type="text" name="nowritetime" value="{date("Y-m-d",strtotime("-1 day"))}" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" readonly>
   								<span class="add-on"><i class="icon-th"></i></span>
								</div>
								<button id="btnnowrite">查  看</button>
						</div>
						<div style="clear:both"></div> 
					</div>
					<div style="width-height:100px;overflow:auto">
						<div id="accordion-element-416238" class="accordion-body">
							<div class="accordion-inner">
							<table class="hide listViewEntriesTable">
							<thead>
							<tr><th></th></tr>
							</thead>
							</table>
								<table class="table table-bordered table-hover" id="nowritename">
                                    {if !empty($REPORT)}
									<tr class="success">
										<td>　姓名</td>
									</tr>
									
									<tbody>

                                        {foreach item=SIGNRECORD  from=$REPORT}
                                            <tr>
                                                <td>{$SIGNRECORD['last_name']}</td>

                                            </tr>

                                        {/foreach}

									</tbody>
                                    {else}
                                        <tr class="success">
                                            <td>　没有记录</td>
                                        </tr>
                                    {/if}
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
{literal} 
$(function(){
	$('.loadinImg').attr('src','');
	$('#datetimepicker').datetimepicker({
        language:'zh-CN',
        weekStart:1,
        todayBtn:1,
        autoclose:1,
        todayHighlight:1,
        startView:2,
        minView:2,
        forceParse:0
    /*
        format: 'yyyy-mm-dd',
        language: 'zh-CN',
        pickDate: true,
        pickTime: true,
        hourStep: 1,
        minuteStep: 15,
        secondStep: 30,
        inputMask: true,
        autoclose:1
        */
    });
    $('#btnnowrite').click(function(){
    	var nowdate=$('input[name="nowritetime"]').val();
    	$.ajax({url:'/index.php?module=WorkSummarize&action=ChangeAjax&nowdate='+nowdate,
    	success: function(data){
    		var newstd='<tr class="success"><td>　姓名</td></tr>';
    		if(data!=0){
    			var newdata=eval(data);
    			
    			$.each(newdata,function(index,value){
    				newstd+='<tr><td>'+value.last_name+'</td></tr>';
    			})
    			
    			$('#nowritename').html('');
    			$('#nowritename').html(newstd);
    			
    		}else{
  			$('#nowritename').html('');
    		$('#nowritename').html(newstd);
  		}
  		}
    	
    	});
    });
   
});
{/literal} 
</script>
	
{/strip}
