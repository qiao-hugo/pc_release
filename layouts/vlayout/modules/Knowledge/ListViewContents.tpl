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


    <div id="pagehtml">
<input type="hidden" id="view" value="{$VIEW}" />
<input type="hidden" id="pageStartRange" value="" />
<input type="hidden" id="pageEndRange" value="" />
<input type="hidden" id="previousPageExist" value="" />
<input type="hidden" id="nextPageExist" value="" />
<input type="hidden" id="alphabetSearchKey" value= "" />
<input type="hidden" id="Operator" value="{$OPERATOR}" />
<input type="hidden" id="alphabetValue" value="{$ALPHABET_VALUE}" />
<input type="hidden" id="totalCount" value="{$PAGE_COUNT}" />

<input type='hidden' value="{$PAGE_NUMBER}" id='pageNumber'>
<input type='hidden' value="{$PAGING_MODEL->getPageLimit()}" id='pageLimit'>
<input type="hidden" value="{$LISTVIEW_ENTIRES_COUNT}" id="noOfEntries">

{assign var = ALPHABETS_LABEL value = vtranslate('LBL_ALPHABETS', 'Vtiger')}
{assign var = ALPHABETS value = ','|explode:$ALPHABETS_LABEL}
	
{*assign var =IS_PROTECTED value=$MODULE_MODEL->isprotected() *}


<div class="listViewEntriesDiv contents-bottomscroll" style="overflow:auto;">
	<div class="bottomscroll-div" >
	<input type="hidden" value="{$ORDER_BY}" id="orderBy">
	<input type="hidden" value="{$SORT_ORDER}" id="sortOrder">

	{assign var=WIDTHTYPE value=$CURRENT_USER_MODEL->get('rowheight')}
	<div class="container-fluid" style="padding-left:0;">
		<div class="row-fluid">
		
			<div class="span10">
				<table class="table listViewEntriesTable" style="word-break:break-all; word-wrap:break-word;">
					<thead>
						<tr class="listViewHeaders">
							{foreach key=KEY item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
							<th nowrap data-field="">
								{vtranslate($KEY, $MODULE)}
							</th>
							{/foreach}
							<th nowrap style="width:90px">操作</th>
						</tr>
					</thead>
					
					{foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=listview}
		<tr class="listViewEntries"  data-id='{$LISTVIEW_ENTRY['id']}' data-recordUrl='index.php?module={$MODULE}&view=Detail&record={$LISTVIEW_ENTRY['id']}' id="{$MODULE}_listView_row_{$smarty.foreach.listview.index+1}">

			{foreach key=fkey item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS name=fieldview}

			<td class="listViewEntryValue"  nowrap style="white-space: {if $LISTVIEW_HEADER['columnname'] eq 'knowledgetop' && $LISTVIEW_ENTRY[$LISTVIEW_HEADER['columnname']] eq '是'}nowrap{else}inherit{/if};">
                {if $LISTVIEW_HEADER['columnname'] eq 'knowledgetitle'}
					<a class="btn-link" href='index.php?module={$MODULE}&view=Detail&record={$LISTVIEW_ENTRY['id']}' target="_block">{vtranslate( $LISTVIEW_ENTRY[$LISTVIEW_HEADER['columnname']],$MODULE)}</a>
				{elseif $LISTVIEW_HEADER['columnname'] eq 'isrole'}
					{$MODULE_MODEL->displayRole($LISTVIEW_ENTRY['isrole'])}
				{else}
					{uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)}
				{/if}

            </td>


			{/foreach}
			<td class="listViewEntryValue" >
                {if $LISTVIEW_HEADER@last}

                    <div  style="width:90px">
						{if $IS_MANAGER and $PUBLIC}
							<a  class="onTheShelf" data-record="{$LISTVIEW_ENTRY['id']}"><i title="上架" class="icon-circle-arrow-up alignMiddle"></i></a>&nbsp;
						{else}
							<a  href="index.php?module={$MODULE}&view=Detail&record={$LISTVIEW_ENTRY['id']}"><i title="详细信息" class="icon-th-list alignMiddle"></i></a>&nbsp;&nbsp;
							{if $IS_MODULE_EDITABLE}
								<a href="index.php?module={$MODULE}&view=Edit&record={$LISTVIEW_ENTRY['id']}"><i title="{vtranslate('LBL_EDIT', $MODULE)}" class="icon-pencil alignMiddle"></i></a>&nbsp;&nbsp;
							{/if}
							{if $IS_MODULE_DELETABLE}
								<a class="deleteRecordButton"><i title="{vtranslate('LBL_DELETE', $MODULE)}" class="icon-trash alignMiddle"></i></a>&nbsp;
							{/if}
							{if $IS_MANAGER and !$PUBLIC and in_array($LISTVIEW_ENTRY['knowledgecolumns'],array('idcbusinessprocess','Salessystem','NewList','Salesmanagementsystem','News'))}
								<a class="offTheShelf" data-record="{$LISTVIEW_ENTRY['id']}"><i title="下架" class="icon-circle-arrow-down alignMiddle"></i></a>
							{/if}
						{/if}
                    </div>
                {/if}
            </td>		
		</tr>
		{/foreach}
		</table>
			</div>
		    <div class="span2">
	            <div class="accordion" id="accordion-284164">
	            	{foreach item=CATEGORYL from=$CATEGORY name=cate}
					<div class="accordion-group">
	                
	                   
						<div class="accordion-heading" style="background-color: #0065a6;">
							 <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion-284164" href="#accordion-element-455981{$smarty.foreach.cate.index}" style="color:#fff;">{vtranslate($CATEGORYL['info']['knowledgecolumns'], $MODULE)}</a>
						</div>
	                   	{if !empty($CATEGORYL['child'])}
						<div id="accordion-element-455981{$smarty.foreach.cate.index}" class="accordion-body in collapse"  style="background-color:#fff;">
							<div class="accordion-inner">
							
	                            {foreach item=CATEGORYSON from=$CATEGORYL['child']}
	                            	<p class="text-info" id="{$CATEGORYSON['info']['knowledgecolumns']}" ><i class="icon-hand-right"></i><a  href="/index.php?module=Knowledge&view=List&filter={$CATEGORYSON['info']['knowledgecolumns']}" >{vtranslate($CATEGORYSON['info']['knowledgecolumns'], $MODULE)}</a></p>
	                            	{if !empty($CATEGORYSON['child'])}
		                            	{foreach item=CATEGORYSONS from=$CATEGORYSON['child']}
		                            		<p class="text-info" id="{$CATEGORYSONS['info']['knowledgecolumns']}" style="margin-left:40px;"><i class="icon-hand-right"></i><a  href="/index.php?module=Knowledge&view=List&filter={$CATEGORYSONS['info']['knowledgecolumns']}" >{vtranslate($CATEGORYSONS['info']['knowledgecolumns'], $MODULE)}</a></p>
		                            	{/foreach}
	                            	{/if}
	                            {/foreach}
	                        
						 	</div>
						</div>
	                  	{/if}
	              
	                       
					</div>
					 {/foreach}
				</div>
				
			</div>
		</div>
	</div>

</div>
</div>
    </div>

{/strip}
