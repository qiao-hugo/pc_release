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
		<span >

			<button class="btn addButton">
				<i class="icon-plus icon-white"></i>&nbsp;
				<strong>{vtranslate('LBL_ADD_RECORD', $QUALIFIED_MODULE)}</strong>
			</button>
			<input type="hidden" id="role" value='{$ROLE}'>
			<input type="hidden" id="department" value='{$DEPARTMENT}'>

		</span>
		<span class="span12 btn-toolbar" style="margin-left:0;">
		<div id="showSearch1" style="font-size: 12px;" >
			<div class="control-group margin0px">
				<select name="searchtype" class="chzn-select" id="searchtype" style="width:200px;">
					<option value="0">请选择部门</option>
					{foreach item=DEPARTMENTDATA key=KEYS from=$DEPARTMENTDATAS}
						<option value="{$KEYS}">{$DEPARTMENTDATA}</option>
					{/foreach}
				</select>
				<span class="paddingLeft10px cursorPointer help-inline" id="userSearchButton"><img src="layouts/vlayout/skins/softed/images/search.png" alt="搜索按钮" title="搜索按钮"></span>

				<span>
						<span class="pull-right">&nbsp;<input type="text" name="jumppage" value="" id="jumppage" class="input-small" style="width: 50px;" placeholder="跳转">&nbsp;</span>
							<span class="pagination pull-right" id="pagination">
								<ul class="pagination-demo">

								</ul>
							</span>
			</div>
		</div>
		</span>
	</div>

	<div class="clearfix"></div>
	<div class="listViewContentDiv" id="listViewContents">
{/strip}