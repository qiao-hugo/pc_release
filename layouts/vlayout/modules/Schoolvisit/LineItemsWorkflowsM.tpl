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
<input id="recordid" type="hidden" value="{$RECORD->getId()}" />
<div class="widgetContainer_workflows" data-url="module={$ModuleName}&amp;view=Detail&amp;record={$RECORD->getId()}&amp;mode=getWorkflows&amp;page=1&amp;limit=5" data-name="SalesorderWorkflowStages">
    <div class="widget_contents">
    
	</div>
</div>

</br>
{/strip}