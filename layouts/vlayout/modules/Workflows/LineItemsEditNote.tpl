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
{if empty($NOTECONTENT) eq false}
    <table class="table table-bordered blockContainer showInlineTable detailview-table">
    <thead>
   <tr><th class="blockHeader" colspan="4"><img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;"><img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">&nbsp;&nbsp;工单信息</th></tr></thead>
   <tbody>
   <tr><td class="fieldLabel medium">
   <label class="muted pull-right marginRight10px">工单数据</label></td>
   <td class="fieldValue medium" colspan="3"><div class="row-fluid"><span class="span10">
   <textarea class="span11" style="height:400px;" name="notecontent">
   {$NOTECONTENT}
   </textarea>
   </span></div></td></tr></tbody></table>
   <br/>
   {/if}
-->*}
{strip}
<input type="hidden" value="{$ISCONTRACT}" id="iscontract" />
{*stee 2015-05-13添加是否包产品*}
<input type="hidden" value="{$ISCONTENT}" id="iscontent" />

 {/strip}