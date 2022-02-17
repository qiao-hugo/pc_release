
{strip}
<input type='hidden' id='pageNumber' value="{$PAGE_NUMBER}">
<input type='hidden' id='pageLimit' value="{$PAGING_MODEL->getPageLimit()}">
<input type="hidden" id="noOfEntries" value="{$LISTVIEW_COUNT}">
<input type="hidden" id="pageStartRange" value="{$PAGING_MODEL->getRecordStartRange()}" />
<input type="hidden" id="pageEndRange" value="{$PAGING_MODEL->getRecordEndRange()}" />
<input type="hidden" id="previousPageExist" value="{$PAGING_MODEL->isPrevPageExists()}" />
<input type="hidden" id="nextPageExist" value="{$PAGING_MODEL->isNextPageExists()}" />
<input type="hidden" id="totalCount" value="{$PAGE_COUNT}" />
<div class="popupEntriesDiv" id="listViewContents">
	<input type="hidden" value="{$ORDER_BY}" id="orderBy">
	<input type="hidden" value="{$SORT_ORDER}" id="sortOrder">
	{if $SOURCE_MODULE eq "Emails"}
		<input type="hidden" value="Vtiger_EmailsRelatedModule_Popup_Js" id="popUpClassName"/>
	{/if}
	{assign var=WIDTHTYPE value=$CURRENT_USER_MODEL->get('rowheight')}
	{assign var=SRCMODULE value=$smarty.get.src_module}
	<input type="hidden" value="{$SRCMODULE}" id="srcmodule">
{if $SRCMODULE neq 'ServiceContracts' && $SRCMODULE neq 'SalesOrder'}
    <table class="table table-bordered listViewEntriesTable">
        <thead>
        <tr class="listViewHeaders">
            {if $MULTI_SELECT}
                <td class="{$WIDTHTYPE}">
                    <input type="checkbox"   class="selectAllInCurrentPage" />
                </td>
            {/if}
            {foreach key=KEY item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
                <th class="{$WIDTHTYPE}">
                    <a href="javascript:void(0);" class="listViewHeaderValues"  data-columnname="{$LISTVIEW_HEADER}">{vtranslate($KEY, $MODULE)}
                    </a>
                </th>
            {/foreach}
        </tr>
        </thead>

        {foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=popupListView}
            <tr class="listViewEntries" data-id="{$LISTVIEW_ENTRY['id']}" data-name="{$LISTVIEW_ENTRY['productname']}"  data-info='{ZEND_JSON::encode($LISTVIEW_ENTRY)}'
                id="{$MODULE}_popUpListView_row_{$smarty.foreach.popupListView.index+1}">
                {if $MULTI_SELECT}
                    <td class="{$WIDTHTYPE}">
                        <input class="entryCheckBox" type="checkbox" />
                    </td>
                {/if}
                {foreach key=KEY item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}

                    <td class="listViewEntryValue {$WIDTHTYPE}">

                        {$LISTVIEW_ENTRY[$LISTVIEW_HEADER]}

                    </td>
                {/foreach}
            </tr>
        {/foreach}


    </table>
{else}
<link href="libraries/icheck/blue.css" rel="stylesheet">
<script src="libraries/icheck/icheck.min.js"></script>
<script>
$(document).ready(function(){
  $('.entryCheckBox').iCheck({
    checkboxClass: 'icheckbox_minimal-blue'
  });
	var srcmodule=$('#srcmodule').val();
	if(srcmodule=='SalesOrder'){
		var selectedid=$('input[name="productid"]',window.opener.document).val();
		if(selectedid){
			selectedid=selectedid.split(',');
			$('.entryCheckBox').each(function(index,checkBoxElement){
				var row = $(checkBoxElement).closest('.listViewEntries');
				var id = row.data('id').toString();
				if($.inArray(id,selectedid)!=-1){
					$(checkBoxElement).iCheck('check');
				}
			});	 
		}	
	}
});
</script>
 <table class="table table-bordered listViewEntriesTable"><tr><td>
     {foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=popupListView}
		<div class="span4" style="margin:2px;padding:2px;border: 1px solid #ddd;border-radius: 4px;"
             id="{$MODULE}_popUpListView_row_{$smarty.foreach.popupListView.index+1}">
            {foreach key=KEY item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
			    <div class="listViewEntries {$WIDTHTYPE}" data-id="{$LISTVIEW_ENTRY['id']}" data-name='{$LISTVIEW_ENTRY['productname']}' data-info='{ZEND_JSON::encode($LISTVIEW_ENTRY)}'>
			    <label class="checkbox">{if $MULTI_SELECT}<input class="entryCheckBox" type="checkbox" />{/if}
                    {$LISTVIEW_ENTRY[$LISTVIEW_HEADER]}
				</label>
			    </div>
			{/foreach}
		</div>
		{/foreach}</td></tr>
</table>
{/if}
	<!--added this div for Temporarily -->
{if $LISTVIEW_ENTIRES_COUNT eq '0'}
	<div class="row-fluid">
		<div class="emptyRecordsDiv">{vtranslate('LBL_NO', $MODULE)} {vtranslate($MODULE, $MODULE)} {vtranslate('LBL_FOUND', $MODULE)}.</div>
	</div>
{/if}
</div>
{/strip}
