{*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************}
{strip}
<div class="container-fluid">
	<div class="widget_header row-fluid">
		<div class="span8">
			<h3>{vtranslate($MODULE, $QUALIFIED_MODULE)}</h3>
	    </div>
	</div>
	<hr>
	<div class="clearfix treeView">
		<ul>
			{foreach from=$COMPANY_LIST item=item}
				<li style="list-style: none;">
					<div class="toolbar-handle">
						<a href="javascript:;" class="btn draggable droppable ui-draggable ui-droppable">{$item['companyfullname']}</a>
					</div>
				</li>
			{/foreach}
		</ul>
	</div>
</div>
{/strip}