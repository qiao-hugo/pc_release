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
	<div class="listViewPageDiv">{if !isset($smarty.get.report)}
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
        {if !isset($smarty.get.report)}
        <form method="post"  name="SearchBug" id="SearchBug">
            <input type="hidden" value="1" id="queryaction" name="queryaction">
            <input type="hidden" value="" id="queryTitle" name="queryTitle">
            <input type="hidden" value="0" id="saveQuery" name="saveQuery">
            <input type="hidden" value="0" id="reset" name="reset">
            <input type="hidden" value="" id="showField" name="showField">
            <div id="SearchBlankCover" style="background-color: #F0F0F0;">

                <table id="searchtable" style="margin:auto">

                    <tbody>

                    {*<tr class="SearchConditionRow" id="SearchConditionRow0" style="height:22px;">
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
                    </tr>*}
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
    <div class="row" style="padding: 10px 20px 0 20px;">
    <span class="span4">
    {if $LISTVIEW_LINKS['LISTVIEWBASIC']|@count gt 0}
        {foreach item=LISTVIEW_BASICACTION from=$LISTVIEW_LINKS['LISTVIEWBASIC']}
            <span class="btn-group">
					<button id="{$MODULE}_listView_basicAction_{Vtiger_Util_Helper::replaceSpaceWithUnderScores($LISTVIEW_BASICACTION->getLabel())}" class="btn addButton" {if stripos($LISTVIEW_BASICACTION->getUrl(), 'javascript:')===0} onclick='{$LISTVIEW_BASICACTION->getUrl()|substr:strlen("javascript:")};'{else} onclick='window.location.href="{$LISTVIEW_BASICACTION->getUrl()}"'{/if}><i class="icon-plus icon-white"></i>&nbsp;<strong>{vtranslate($LISTVIEW_BASICACTION->getLabel(), $MODULE)}</strong></button>
					</span>
        {/foreach}
    {/if}
        <span style="display:none;">
 &nbsp;<input  onclick="Vtiger_List_Js.showUserFieldEdit('index.php?module={$MODULE}&view=FieldAjax');" class="btn" type="button" value="列表自定义" ></span>
        </span>
       <span class="span8" style="text-align: right">  <span>
           &nbsp;
            <select id="limit" name="limit" style="width: 80px;margin-top: -13px;"><option value="10">10</option><option value="20" selected="selected">20</option><option value="50">50</option><option value="100">100</option></select>&nbsp;
        </span>
    <span class="pagination" style="" id="pagination">
        <ul class="pagination-demo">

        </ul>
    </span>
    <span>&nbsp;<input type="text" name="jumppage" value="" id="jumppage" class="input-small" style="width: 50px;margin-top: -13px;" placeholder="跳转">&nbsp;
</span>

    </span>
    </div>
{/if}
	<div class="listViewContentDiv" id="listViewContents">
{/strip}