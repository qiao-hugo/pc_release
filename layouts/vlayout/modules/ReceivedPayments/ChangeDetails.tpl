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
<a class="pull-right btn btn-primary" href="index.php?module=ReceivedPayments&view=List&public=ExportCD&record={$record}">导出</a>
<div class="recentActivitiesContainer" style="margin-top:50px">
	<div>
		{if !empty($RECENT_ACTIVITIES)}
			<ul class="unstyled">
				{foreach item=RECENT_ACTIVITY from=$RECENT_ACTIVITIES}
					<div class="bs-callout bs-callout-warning">
						<li>
							<div class='font-x-small updateInfoContainer'>
								<i>变更人</i> :&nbsp;
								<b>{$RECENT_ACTIVITY['last_name']}</b>&nbsp;<b style="color:#ff0000" >{$RECENT_ACTIVITY['changetype']}</b>合同号&nbsp;
								<b><a target="_blank" href="index.php?module=ServiceContracts&view=Detail&record={$RECENT_ACTIVITY['servicecontractsid']}">{$RECENT_ACTIVITY['contract_no']}</a></b>
								<span class="pull-right"><p class="muted"><small>{$RECENT_ACTIVITY['changetime']}</small></p></span>
							</div>
						</li>
					</div>
				{/foreach}
			</ul>
		{else}
			<div class="bs-callout bs-callout-warning">
				<p class="textAlignCenter">无匹配或解绑合同</p>
			</div>
		{/if}
	</div>
	<span class="clearfix"></span>
</div>
{/strip}