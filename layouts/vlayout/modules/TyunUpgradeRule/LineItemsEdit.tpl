
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
    <!--
    All final details are stored in the first element in the array with the index name as final_details
    so we will get that array, parse that array and fill the details
    -->
    <link href="libraries/icheck/orange.css" rel="stylesheet">
    <script src="libraries/icheck/icheck.min.js"></script>
        <table class="table table-bordered blockContainer showInlineTable  detailview-table Duplicates" data-num="{$row_no+1}">
            <thead>
            <tr>
                <th class="blockHeader" colspan="4">
                    <img class="cursorPointer alignMiddle blockToggle  hide" src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;">
                    <img class="cursorPointer alignMiddle blockToggle" src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">
                    &nbsp;&nbsp;产品明细&nbsp;&nbsp;

                </th>
            </tr>
            </thead>
            <tr>
                {foreach key=row_no item=data from=$PRODUCTDATA name=PRODUCTLIST}
                <td class="fieldLabel medium">
                    <label class="muted pull-right marginRight10px">
                        &nbsp;
                    </label>
                </td>
                <td class="fieldValue medium"  >
                    <label style="width:50%;height:100%;display: inline-block;"><input type="checkbox" class="entryCheckBox" name="productdlist[]" value="{$data['productid']}"{if $RECORD_ID && in_array($data['productid'],$PRODUCTSELECT)} checked{/if}>{vtranslate($data['productid'],'TyunUpgradeRule')}</label>
                </td>
                {if ($row_no+1)%2 eq 0 && !$PRODUCTLIST@last}
                </tr></tr>
                {/if}
                {if $PRODUCTLIST@last}
                    </tr>
                {/if}

                {/foreach}



            </tbody>
        </table>
        <br>
    <script type="text/javascript">
        $(function(){
            $('.entryCheckBox').iCheck({
                checkboxClass: 'icheckbox_minimal-orange'
            });
        });

    </script>




{/strip}