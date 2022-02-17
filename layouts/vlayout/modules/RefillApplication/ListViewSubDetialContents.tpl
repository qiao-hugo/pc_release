{*<!--
/**********
**修改字段为workflowsnode时换行
 *
 * 
 * 
*
 *************/
-->*}
{strip}
		{foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=listview}
		<tr class="listViewEntries{$LISTVIEW_ENTRY['id']}"  data-id='{$LISTVIEW_ENTRY['id']}'>
			<td nowrap></td>
			<td nowrap></td>
			<td nowrap></td>
            {foreach key=fkey item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS name=fieldview}
                <td class="listViewEntryValue"  nowrap style="background-color:#cae7fb;">
                    {uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,'RefillApplication')}
                </td>
            {/foreach}
            <td class="listViewEntryValue" >
            </td>
		</tr>
		{/foreach}
{/strip}
