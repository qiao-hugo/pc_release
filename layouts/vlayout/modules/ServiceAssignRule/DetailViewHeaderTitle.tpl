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
	<span class="span10 margin0px">
		<span class="row-fluid">
			<span class="recordLabel font-x-x-large textOverflowEllipsis span pushDown" title="{$RECORD->getName()}">
				{if ($RECORD->get('assigntype') eq 'productby')}
				客服分配规则(按产品分配)- {$RECORD->getDisplayValue('productid',$RECORD->getName())}
				{else}
				客服分配规则(按客户分配)
				{/if}
			</span>
		</span>
	</span>
{/strip}