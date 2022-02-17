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
    {if !empty($MODULEID)}
        {*<a target="_blank" href="index.php?module={$MODULENAME}&view=Detail&record={$MODULEID}">详细</a>*}
        &nbsp;&nbsp;
    {/if}
    	
    {if $ALERT_STATUS neq 'finish'}
	 	<button id="btnFinish" type="button" class="btn btn-primary">完成</button>
	 	&nbsp;
 	{/if}
    </td></tr>
    </tbody>
    </table>
{/strip}