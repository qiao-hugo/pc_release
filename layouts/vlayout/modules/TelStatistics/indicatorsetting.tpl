{strip}
<div class="listViewPageDiv">{if !isset($smarty.get.public)}
        <div class="listViewTopMenuDiv noprint">
            <div class="listViewActionsDiv row-fluid">
				<span class="btn-toolbar span4">

				<span class="{if empty($smarty.get.public) eq false}hide{/if}">

					{foreach item=LISTVIEW_BASICACTION from=$LISTVIEW_LINKS['LISTVIEWBASIC']}
                        <span class="btn-group">
							<button id="{$MODULE}_listView_basicAction_{Vtiger_Util_Helper::replaceSpaceWithUnderScores($LISTVIEW_BASICACTION->getLabel())}" class="btn addButton" {if stripos($LISTVIEW_BASICACTION->getUrl(), 'javascript:')===0} onclick='{$LISTVIEW_BASICACTION->getUrl()|substr:strlen("javascript:")};'{else} onclick='window.location.href="{$LISTVIEW_BASICACTION->getUrl()}"'{/if}><i class="icon-plus icon-white"></i>&nbsp;<strong>{vtranslate($LISTVIEW_BASICACTION->getLabel(), $MODULE)}</strong></button>
						</span>
                    {/foreach}</span>
				</span>
                <span class="btn-toolbar span4">
				<span class="customFilterMainSpan btn-group">

				</span>
			</span>
            </div>
        </div>
    {/if}


    <div>
        <form method="post"  name="SearchBug" id="SearchBug">
            <input type="hidden" value="1" id="queryaction" name="queryaction">
            <input type="hidden" value="" id="queryTitle" name="queryTitle">
            <input type="hidden" value="0" id="saveQuery" name="saveQuery">
            <input type="hidden" value="0" id="reset" name="reset">
            <input type="hidden" value="" id="showField" name="showField">
            <div id="SearchBlankCover" style="background-color: #F0F0F0;">

                <table id="searchtable" style="margin:auto">

                    <tbody>

                    <tr class="SearchConditionRow" id="SearchConditionRow0" style="height:22px;">
                        <td>
                            <input type="hidden" value="" name="BugFreeQuery[leftParenthesesName0]"
                                   id="BugFreeQuery_leftParenthesesName0">
                        </td>
                        <td>
                            <select id="BugFreeQuery_field0" style="width:100%;color:#878787;" name="BugFreeQuery[field0]">
                                <option value="department" selected="selected">
                                    部门
                                </option>
                            </select>
                        </td>
                        <td>
                            <select id="BugFreeQuery_operator0" style="width:100%;color:#878787;"
                                    name="BugFreeQuery[operator0]">
                                <option value="UNDER" selected="selected">
                                    等于
                                </option>
                            </select>
                        </td>
                        <td>


                            {assign var =Department value=getDepartment()}
                            <select id="DepartFilter" name="department">
                                {foreach item="departname" key="departmentid" from=$Department}
                                    <option value="{$departmentid}" >{$departname}</option>

                                {/foreach}
                            </select>
                        </td>
                        <td>
                            <input type="hidden" value="" name="BugFreeQuery[rightParenthesesName0]"
                                   id="BugFreeQuery_rightParenthesesName0">
                        </td>
                        <td>
                            <select id="BugFreeQuery_andor0" style="width:65px;color:#878787;" name="BugFreeQuery[andor0]">
                                <option value="And" selected="selected">
                                    并且
                                </option>
                            </select>
                        </td>
                        <td>
                            <a class="add_search_button" href="javascript:addSearchField(0);">
                                <img src="layouts/vlayout/skins/softed/images/add_search.gif">
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="7">
                            <center>
                                <input type="button"
                                       value="提交查询" id="PostQuery" name="PostQuery" class="btn">
                                <input type="button" onclick="setSearchConditionOrder();$('#save_query_dialog').dialog('open'); return false;"
                                       value="保存查询" id="SaveQuery" name="SaveQuery" class="btn hide">
                                <input type="button" onclick="location.reload();"
                                       value="重置查询" class="btn">
                            </center>
                        </td>
                    </tr>
                    </tbody>
                </table>

                {include file='SearchJS.tpl'|@vtemplate_path MODULE=$MODULE}
        </form>
    </div>
    {include file='DefaultListFields.tpl'|@vtemplate_path MODULE=$MODULE ISDEFAULT=true ISPAGE=true}
    <div class="listViewContentDiv" id="listViewContents">

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




<div class="listViewEntriesDiv contents-bottomscroll" style="overflow:auto;">
	<div class="bottomscroll-div" >
	<input type="hidden" value="{$ORDER_BY}" id="orderBy">
	<input type="hidden" value="{$SORT_ORDER}" id="sortOrder">

	{assign var=WIDTHTYPE value=$CURRENT_USER_MODEL->get('rowheight')}
	<table class="table listViewEntriesTable">
		<thead>
			<tr class="listViewHeaders">
				{if $LISTVIEW_FIELDS}
					{foreach key=KEY item=LISTVIEW_HEADER from=$LISTVIEW_FIELDS}
				<th nowrap data-field="{$LISTVIEW_HEADERS[$KEY]['columnname']}">
					{vtranslate($KEY, $MODULE)}
				</th>
				{/foreach}
				{else}
				{foreach key=KEY item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
				<th nowrap data-field="{$LISTVIEW_HEADER['columnname']}">
					{vtranslate($KEY, $MODULE)}
				</th>
				{/foreach}
				{/if}
				<th nowrap style="width:90px">操作</th>
			</tr>
		</thead>
		{foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=listview}
		<tr class="listViewEntries1"  data-id='{$LISTVIEW_ENTRY['id']}' data-recordUrl='index.php?module={$MODULE}&view=Detail&record={$LISTVIEW_ENTRY['id']}' id="{$MODULE}_listView_row_{$smarty.foreach.listview.index+1}">
            {foreach key=fkey item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS name=fieldview}
                <td class="listViewEntryValue {$fkey}"  nowrap>
                    {uitypeformat($LISTVIEW_HEADER,$LISTVIEW_ENTRY,$MODULE)}
                </td>
			{/foreach}

            <td class="listViewEntryValue" >
                {if $LISTVIEW_HEADER@last}
					{if $IS_MODULE_EDITABLE}
						<i title="{vtranslate('LBL_EDIT', $MODULE)}" class="icon-pencil alignMiddle changeUserData" style="cursor:pointer;" data-id="{$LISTVIEW_ENTRY['id']}"></i>
					{/if}
                {/if}
            </td>
		</tr>
		{/foreach}

	</table>

</div>
</div>
    </div>
{/strip}