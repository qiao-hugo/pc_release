{if !empty($FORMINFO)}
<div>
	<table class="table table-bordered table-striped blockContainer showInlineTable ">
		<thead>
			<tr>
				<th class="blockHeader"   >
					{$FORMINFO['form_name']}
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
			
			<td>{htmlspecialchars_decode($FORMINFO['content_data'])}</td>
			
			
			</tr>
		</tbody>
	</table>		
</div>
{/if}