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
{foreach key=row_no item=data from=$RELATED_PRODUCTS}
     <tr class="lineItemRow" name="{if empty($data['productcomboid']) eq false}productcombo{$data.productcomboid}{/if}">
            {include file="LineItemsContent.tpl"|@vtemplate_path:'SalesOrder' row_no=$row_no data=$data}
     </tr> 
{/foreach}
{/strip}