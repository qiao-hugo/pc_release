{strip}
<div class="row-fluid " id="c">
	<table class="table table-bordered table-hover">
		<thead>
			<tr>
				<td colspan=1>部门人员表</td>
				<td>
      			{*<label class="checkbox"></label><input type="checkbox" id="bosss"> *}
                        部门人员在职信息
    </td>
			</tr>
		</thead>

		<tbody>
			{foreach item=USER key = key from=$SMALLUSER}
                {if $key eq '客户服务部'} {continue} {/if}
				<tr>
					<td nowrap> {$key}</td>

					<td class="naughty">
					{foreach item=realdata from =$USER }
                    {if $realdata['department'] eq '客户服务部'} {break} {/if}
					<span class="span2" style="margin-left:0px;"><span class="{if strpos($realdata['role'],'商务总监')!==false}label label-success {/if}
										{if strpos($realdata['role'],'商务经理')!==false}label label-warning {/if}
										{if strpos($realdata['role'], '商务主管')!==false}label label-important{/if}
										 {if strpos($realdata['role'],'商务助理')!==false}label label-info{/if}
										 {*if $realdata['status'] eq '[离职]'}hide leave{/if *}" >{$realdata['name']}-{$realdata['role']}{*-{$realdata['status']*}</span>
						</span>
						{/foreach}
					</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
</div>
<br>
<br>
<br>
<div class="row-fluid " id="d">
	<table class="table table-bordered blockContainer showInlineTable  detailview-table">
		<thead>
			<tr>
				<td colspan="4" style="text-align: center;"><font style="font-weight:bold;font-size:16px;">部门每月在职人员表统计</font></td>
			</tr>
		</thead>
		<tbody>
        <tr>
            <td>部门</td>
            <td>月份</td>
            <td>人数</td>
            <td>部门人员</td>
        </tr>
    {foreach item=USERS from=$SMALLUSERMONTH}
        {foreach item=REALDATAS key=KEYS from =$USERS name=foo}
            {if $KEYS neq 'departmentname'}
            <tr>
                {if $smarty.foreach.foo.index eq 1}<td rowspan="{count($USERS)-1}"> {$USERS['departmentname']}</td>{/if}
                <td>
                    <span class="label label-warning ">{$KEYS}</span>
                </td>
                <td>
                    <span class="label label-success ">{count($REALDATAS)}</span>
                </td>
                <td class="naughty">
                    {foreach item=VALUES from=$REALDATAS}
                       {$VALUES}
                    {/foreach}
                </td>
            </tr>
            {/if}
        {/foreach}
    {/foreach}
		</tbody>
	</table>
</div>
{*<script>
	$("#bosss").click(function(){
		$('.leave').toggle();
	});
</script>*}
{/strip}
