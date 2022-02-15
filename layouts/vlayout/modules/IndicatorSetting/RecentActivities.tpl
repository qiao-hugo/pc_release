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
    <div class="recentActivitiesContainer">
        <div>
            {if !empty($RECENT_ACTIVITIES)}
                <ul class="unstyled">
                    {foreach key=KEG item=RECENT_ACTIVITY from=$RECENT_ACTIVITIES}
                        <div class="bs-callout bs-callout-warning">
                                <li>
                                    <div>
                                        <span><strong>{$RECENT_ACTIVITY['last_name']}<br/></strong> {$RECENT_ACTIVITY['detail_info']}</span>
                                        <span class="pull-right"><p class="muted"><small title="">{$RECENT_ACTIVITY['createdtime']}</small></p></span>
                                    </div>
                                </li>
                        </div>
                    {/foreach}
                </ul>
            {else}
                <div class="bs-callout bs-callout-warning">
                    <p class="textAlignCenter">{vtranslate('LBL_NO_RECENT_UPDATES')}</p>
                </div>
            {/if}
        </div>
        {if $PAGING_MODEL->isNextPageExists()}
            <div class="row-fluid">
                <div class="pull-right">
                    <a href="javascript:void(0)" class="moreRecentUpdates">{vtranslate('LBL_MORE',$MODULE_NAME)}..</a>
                </div>
            </div>
        {/if}
        <span class="clearfix"></span>
    </div>
{/strip}