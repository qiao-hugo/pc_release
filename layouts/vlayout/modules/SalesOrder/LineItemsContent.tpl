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

	{if $row_no eq 0}
	{assign var="0" value="$data.productid"}
	{/if}
	<td>
		<i class="icon-trash deleteRow cursorPointer" title="{vtranslate('LBL_DELETE',$MODULE)}"></i>
		&nbsp;<a><img src="{vimage_path('drag.png')}" border="0" title="{vtranslate('LBL_DRAG',$MODULE)}"/></a>
		<input type="hidden" class="rowNumber" value="{$row_no}" />
	</td>
	<td>
		<div>
			<input type="text" id="{$productName}" name="{$productName}" value="{$data.productname}" class="productName {if $row_no neq 0} autoComplete {/if}" placeholder="{vtranslate('LBL_TYPE_SEARCH',$MODULE)}" data-validation-engine="validate[required]" {if !empty($data.productname)} disabled="disabled" {/if}/>
			<input type="hidden" name="data[{$data.productid}][productid]" value="{$data.productid}" class="selectedModuleId"/>
			{if $row_no eq 0}
				<img class="lineItemPopup cursorPointer alignMiddle" data-popup="ProductsPopup" title="{vtranslate('Products',$MODULE)}" data-module-name="Products" data-field-name="productid" src="{vimage_path('Products.png')}"/>
			{else}
				<img class="lineItemPopup cursorPointer alignMiddle" data-popup="ProductsPopup" data-module-name="Products" title="{vtranslate('Products',$MODULE)}" data-field-name="productid" src="{vimage_path('Products.png')}"/>
			{/if}
		</div>
		<div class="subInformation">
		<input type="hidden" name="data[{$data.productid}][productcomboid]" value="{$data.productcomboid}" />
		{if empty($data['productcomboname']) eq false}
		<span class="subProductsContainer">
		<p>所属套餐：
		{$data.productcomboname}</p>
		</span>
		{/if}
		</div>
	</td>
	<td>
		<div align="left" class="productTotal">{if $data.realprice}{$data.realprice}{else}0.00{/if}</div>
		</td>
	<td>
			<textarea name="data[{$data.productid}][comment]"  class="lineItemCommentBox">{$data.notecontent}</textarea>
		
		
	</td>