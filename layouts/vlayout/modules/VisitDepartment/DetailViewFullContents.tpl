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
	{include file='DetailViewBlockView.tpl'|@vtemplate_path:$MODULE_NAME RECORD_STRUCTURE=$RECORD_STRUCTURE MODULE_NAME=$MODULE_NAME}
	<div class="widgetContainer_workflows" data-url="module=VisitDepartment&amp;view=Detail&amp;record={$RECORD->getId()}&amp;mode=getVisitDImprovement&amp;page=1&amp;limit=5" data-name="SalesorderWorkflowStages">
		<div class="widget_contents span12">

		</div>
	</div>
	{include file='LineItemsDetail.tpl'|@vtemplate_path:$MODULE_NAME RECORD_STRUCTURE=$RECORD_STRUCTURE MODULE_NAME=$MODULE_NAME}
{/strip}