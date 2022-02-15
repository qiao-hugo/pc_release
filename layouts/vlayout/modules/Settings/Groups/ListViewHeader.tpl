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
	<div class="widget_header row-fluid">
		<h3>{vtranslate($MODULE, $QUALIFIED_MODULE)}</h3>
	</div>
	<hr>
	<div class="row-fluid">
		<span class="span6 btn-toolbar">
			{foreach item=LISTVIEW_BASICACTION from=$LISTVIEW_LINKS['LISTVIEWBASIC']}
			<button class="btn addButton" {if stripos($LISTVIEW_BASICACTION->getUrl(), 'javascript:')===0} onclick='{$LISTVIEW_BASICACTION->getUrl()|substr:strlen("javascript:")};'
					{else} onclick='window.location.href="{$LISTVIEW_BASICACTION->getUrl()}"' {/if}>
				<i class="icon-plus icon-white"></i>&nbsp;
				<strong>{vtranslate('LBL_ADD_RECORD', $QUALIFIED_MODULE)}</strong>
			</button>
			{/foreach}
		</span>
	</div>
	<div id="showSearch1" >
		<div class="control-group margin0px">
			<select name="searchtype" id="searchtype" style="width:120px;">
				<option value="groupname">组名</option>
				<option value="username">用户</option>
			</select>
			<input type="text" placeholder="分组名称" id="searchvalue">
			<span class="paddingLeft10px cursorPointer help-inline" id="userSearchButton">
				<img src="layouts/vlayout/skins/softed/images/search.png" alt="搜索按钮" title="搜索按钮"></span>

			<span>
	<span class="pull-right">&nbsp;<input type="text" name="jumppage" value="" id="jumppage" class="input-small" style="width: 50px;" placeholder="跳转">&nbsp;</span>
		<span class="pagination pull-right" id="pagination">
			<ul class="pagination-demo">

            </ul>
		</span>
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="listViewContentDiv" id="listViewContents">
{/strip}