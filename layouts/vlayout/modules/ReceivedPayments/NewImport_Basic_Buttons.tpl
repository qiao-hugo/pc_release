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

<button type="button" id="newImportButton"  class="btn btn-success" ><strong>{'LBL_IMPORT'|@vtranslate:$MODULE}</strong></button>
&nbsp;&nbsp;
<a name="cancel" class="cursorPointer cancelLink" value="{'LBL_CANCEL'|@vtranslate:$MODULE}" onclick="location.href='index.php?module={$FOR_MODULE}&view=List'">
		{'LBL_CANCEL'|@vtranslate:$MODULE}
</a>