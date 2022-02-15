{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*{foreach item=IMAGE_INFO from=$IMAGE_DETAILS}<div class='span2'>{if !empty($IMAGE_INFO.path) && !empty({$IMAGE_INFO.orgname})}<img src="{$IMAGE_INFO.path}_{$IMAGE_INFO.orgname}">{/if}</div>{/foreach}
 ********************************************************************************/
-->*}
{strip}


{assign var = ALPHABETS_LABEL value = vtranslate('LBL_ALPHABETS', 'Vtiger')}
{assign var = ALPHABETS value = ','|explode:$ALPHABETS_LABEL}


<div>
	<input type="hidden" value="{$ORDER_BY}" id="orderBy">
	<input type="hidden" value="{$SORT_ORDER}" id="sortOrder">
    <input type="hidden" id="listViewEntriesCount" value="{$LISTVIEW_ENTIRES_COUNT}" />
    <input type="hidden" id="pageStartRange" value="{$PAGING_MODEL->getRecordStartRange()}" />
    <input type="hidden" id="pageEndRange" value="{$PAGING_MODEL->getRecordEndRange()}" />
    <input type="hidden" id="previousPageExist" value="{$PAGING_MODEL->isPrevPageExists()}" />
    <input type="hidden" id="nextPageExist" value="{$PAGING_MODEL->isNextPageExists()}" />
    <input type="hidden" id="pageNumberValue" value= "{$PAGE_NUMBER}"/>
    <input type="hidden" id="pageLimitValue" value= "{$PAGING_MODEL->getPageLimit()}" />
    <input type="hidden" id="numberOfEntries" value= "{$LISTVIEW_ENTIRES_COUNT}" />
    <input type="hidden" id="alphabetSearchKey" value= "user_name" />
    <input type="hidden" id="Operator" value="{$OPERATOR}" />
    <input type="hidden" id="alphabetValue" value="{$ALPHABET_VALUE}" />
    <input type="hidden" id="totalCount" value="{$PAGE_COUNT}" />
    <input type='hidden' value="{$PAGE_NUMBER}" id='pageNumber'>
    <input type='hidden' value="{$PAGING_MODEL->getPageLimit()}" id='pageLimit'>
    <input type="hidden" value="{$LISTVIEW_COUNT}" id="noOfEntries">
    <div class="listViewEntriesDiv contents-bottomscroll" style="overflow:auto;">
	{assign var=WIDTHTYPE value=narrow}
	<table class="table listViewEntriesTable">
		<thead>
			<tr class="listViewHeaders">
				{foreach item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
						<th nowrap class="{$WIDTHTYPE}"><a href="javascript:void(0);" class="listViewHeaderValues">{vtranslate($LISTVIEW_HEADER->get('label'), $MODULE)}&nbsp;&nbsp;</a>
						</th>
				{/foreach}
				<th nowrap class="{$WIDTHTYPE}"><a class="listViewHeaderValues">操作</a></th>
			</tr>
		</thead>
		{foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=listview}
		<tr class="listViewEntries">
			{foreach item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
			{assign var=LISTVIEW_HEADERNAME value=$LISTVIEW_HEADER->get('name')}
			<input type="hidden" name="deleteActionUrl" value="{$LISTVIEW_ENTRY->getDeleteUrl()}">
				{if $LISTVIEW_HEADER->getName() eq 'last_name'}
					<td class="listViewEntryValue {$WIDTHTYPE}" style="min-width:50px;"><a href="{$LISTVIEW_ENTRY->getDetailViewUrl()}">{$LISTVIEW_ENTRY->get('last_name')}</a></td>
				{else}
					<td class="{$WIDTHTYPE}" nowrap>{if ($LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME) eq Null)||$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME) eq 'null'}-{else}{$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME)}{/if}	</td>
				{/if}	
			{/foreach}
			<td class="{$WIDTHTYPE}" >
			<div class="pull-right actions">
						<span class="actionImages">
							{if $IS_MODULE_EDITABLE && $LISTVIEW_ENTRY->get('status') eq 'Active'}
								<a id="{$MODULE}_LISTVIEW_ROW_{$LISTVIEW_ENTRY->getId()}_EDIT" href='{$LISTVIEW_ENTRY->getEditViewUrl()}'><i title="{vtranslate('LBL_EDIT', $MODULE)}" class="icon-pencil alignMiddle"></i></a>&nbsp;
							{/if}
							{if $IS_MODULE_DELETABLE && $LISTVIEW_ENTRY->getId() != $USER_MODEL->getId() && $LISTVIEW_ENTRY->get('status') eq 'Active'}
								<a id="{$MODULE}_LISTVIEW_ROW_{$LISTVIEW_ENTRY->getId()}_DELETE" class="deleteRecordButton"><i title="{vtranslate('LBL_DELETE', $MODULE)}" class="icon-trash alignMiddle"></i></a>
							{/if}
						</span>
					</div>
			</td>
		</tr>
		{/foreach}
	</table>

	{if $LISTVIEW_ENTIRES_COUNT eq '0'}
		<table class="emptyRecordsDiv">
			<tbody>
				<tr>
					<td>
						{assign var=SINGLE_MODULE value="SINGLE_$MODULE"}
						{vtranslate('LBL_NO')} {vtranslate($MODULE, $MODULE)} {vtranslate('LBL_FOUND')}.<!--{if $IS_MODULE_EDITABLE} {vtranslate('LBL_CREATE')} <a href="{$MODULE_MODEL->getCreateRecordUrl()}">{vtranslate($SINGLE_MODULE, $MODULE)}</a>-->{/if}
					</td>
				</tr>
			</tbody>
		</table>
	{/if}
    </div>
</div>
{/strip}