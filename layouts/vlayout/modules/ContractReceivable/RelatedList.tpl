{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
{if $HEADER_FIELD@last}
</td>
{if $IS_EDITABLE}
<a href='{$RELATED_RECORD->getEditViewUrl()}&relationOperation=true&sourceModule={$PARENT_RECORD->get('record_module')}&sourceRecord={$PARENT_RECORD->get('record_id')}'><i title="{vtranslate('LBL_EDIT', $MODULE)}" class="icon-pencil alignMiddle"></i></a>{/if}{if $IS_DELETABLE}<a class="relationDelete"><i title="{vtranslate('LBL_DELETE', $MODULE)}" class="icon-trash alignMiddle"></i></a>{/if} 
{/if}
********************************************************************************/
-->*}
{strip}
    <table  class="table table-bordered blockContainer showInlineTable" style="margin-top: 20px;text-align: center;margin-bottom: 0px">
        <tr style="background-color: #f1f1f1;font-size:14px;color:#333;">
            <td colspan="7" style="font-weight: bold;color: black">合同回款明细</td>
        </tr>
        <tr>
            <th>汇款抬头</th>
            <th>原币金额</th>
            <th>金额</th>
            <th>回款类型</th>
            <th>入账日期</th>
            <th>公司账号</th>
        </tr>
        {foreach from=$CONTRACTS key=KEY item=$ITEM }
            <tr>
                <td>{$CONTRACTS[$KEY]['paytitle']}</td>
                <td>{$CONTRACTS[$KEY]['standardmoney']}</td>
                <td>{$CONTRACTS[$KEY]['unit_price']}</td>
                <td>{vtranslate($CONTRACTS[$KEY]['receivedstatus'],'ReceivedPayments')}</td>
                <td>{$CONTRACTS[$KEY]['reality_date']}</td>
                <td>{$CONTRACTS[$KEY]['owncompany']}</td>
            </tr>
        {/foreach}
        <tr style="color: red;">
            <td>合计</td>
            <td>{$TOTALSTANDARDMONEY}</td>
            <td>{$UNITPRICE}</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>

    </table>
{/strip}
