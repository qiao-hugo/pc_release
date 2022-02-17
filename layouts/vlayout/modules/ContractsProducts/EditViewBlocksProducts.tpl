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
    <link href="libraries/icheck/blue.css" rel="stylesheet">
    <script src="libraries/icheck/icheck.min.js"></script>
    <script>
        $(document).ready(function(){
            $('.entryCheckBox').iCheck({
                checkboxClass: 'icheckbox_minimal-blue'
            });
        });
    </script>
    <table class="table table-bordered blockContainer showInlineTable">
        <tbody>
        <tr><th class="blockHeader" colspan="4">产品信息</th></tr>
        <tr>
            <td class="fieldLabel medium"><label class="muted pull-right marginRight10px">产品类型</label></td>
            <td class="fieldValue medium" colspan="3">
                <div class="row-fluid">
                    {foreach from=$RECORD_PRODUCT_LIST item=value key=key}
                        <div style="line-height: 30px;float: left; float: left; width: 290px; border: 1px solid  rgba(57, 15, 40, 0.18); margin: 2px;">
                            <label class="checkbox"><input type="checkbox" value="{$value['productid']}" class="entryCheckBox" name="relproductid[]" {if in_array($value['productid'],$RECORD_PRODUCT_ID)}checked{/if}>{$value['productname']}</label>
                        </div>
                    {/foreach}
                </div>
            </td>

        </tr>

        </tbody>
    </table>

{/strip}