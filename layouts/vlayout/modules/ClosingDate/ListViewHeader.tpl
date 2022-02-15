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
        {if !isset($smarty.get.report) && $smarty.get.public neq 'selectPage' }
    </div>
    {include file='DefaultListFields.tpl'|@vtemplate_path:$MODULE MODULE=$MODULE ISDEFAULT=true ISPAGE=true}
{/if}
	<div class="listViewContentDiv" id="listViewContents">
{/strip}