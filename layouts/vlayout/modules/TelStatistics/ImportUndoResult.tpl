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
<div class="contentsDiv span10 marginLeftZero">
	<input type="hidden" name="module" value="{$FOR_MODULE}" />
	<table style="width:80%;margin-left:auto;margin-right:auto;margin-top:10px;" cellpadding="10" class="searchUIBasic well">
		<tr>
			<td class="font-x-large" align="left" colspan="2">
				<strong>{'LBL_IMPORT'|@vtranslate:$MODULE} {$FOR_MODULE|@vtranslate:$FOR_MODULE} - {'LBL_UNDO_RESULT'|@vtranslate:$MODULE}</strong>
			</td>
		</tr>
		{if $ERROR_MESSAGE neq ''}
		<tr>
			<td class="style1" align="left" colspan="2">
				{$ERROR_MESSAGE}
			</td>
		</tr>
		{/if}
		<tr>
			<td colspan="2" valign="top">
				<table cellpadding="10" cellspacing="0" align="center" class="dvtSelectedCell thickBorder importContents">
					<tr>
						<td>{'LBL_TOTAL_RECORDS'|@vtranslate:$MODULE}</td>
						<td width="10%">:</td>
						<td width="10%">{$TOTAL_RECORDS}</td>
					</tr>
					<tr>
						<td>{'LBL_NUMBER_OF_RECORDS_DELETED'|@vtranslate:$MODULE}</td>
						<td width="10%">:</td>
						<td width="10%">{$DELETED_RECORDS_COUNT}</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td align="right" colspan="2">
			{include file='Import_Done_Buttons.tpl'|@vtemplate_path:'TelStatistics'}
			</td>
		</tr>
	</table>
</div>
{/strip}