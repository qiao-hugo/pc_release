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
<table width="100%" cellspacing="0" cellpadding="2">
	<tr>
		<td><strong>{'LBL_IMPORT_STEP_1'|@vtranslate:$MODULE}:</strong></td>
		<td class="big">{'LBL_IMPORT_STEP_1_DESCRIPTION'|@vtranslate:$MODULE}</td>
		<td>&nbsp;<a href="./回款人工分类批量导入模板模板.xlsx" target="_blank">模板下载</a></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td data-import-upload-size="{$IMPORT_UPLOAD_SIZE}">
			<input type="button" id="uploadImport" value="批量导入" title="文件名请勿包含空格" style="display: none;">
			<span id="fileName"><span>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>支持的文件类型：.XLSX</td>
	</tr>
</table>