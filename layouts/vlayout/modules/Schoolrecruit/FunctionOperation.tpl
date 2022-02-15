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
<table style="width:100%;text-align:right;">
    <tbody>
    <tr>
    <td>
    {*{if $EXAMINE_STATUS eq 'pass' && $FOLLOW_STATUS eq 'notfollow'}
 	<button id="btnFollow" type="button" class="btn stagesubmit btn-primary">跟进</button>
 	&nbsp;
 	{/if}*}
 	{if $EXAMINE_STATUS eq 'unaudited'}
 	<button id="btnAudit" type="button" class="btn stagesubmit btn-primary">通过</button>
 	&nbsp;
 	<button id="btnReject" type="button" class="btn stagereset btn-primary">拒绝</button>
 	{/if}
    </td></tr>
    </tbody>
    </table>
{/strip}