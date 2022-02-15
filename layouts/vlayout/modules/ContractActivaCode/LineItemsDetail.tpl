{*<!--
/*********************************************************************************

*<td>
	<b>是否符合Tsite</b>{$LINE_ITEM_DETAIL["Tsite"]}<b>是否符合Tsite新动力</b>{$LINE_ITEM_DETAIL["TsiteNew"]}</br>
</td>
********************************************************************************/
-->*}
<br>
<table class="table table-bordered blockContainer lineItemTable tableproduct detailview-table">
    <thead>
    <tr>
        <th colspan="8">
            <img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;">
            <img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">
            &nbsp;&nbsp;发票明细
        </th>
    </tr>
    </thead>
    <tbody>
    <tr id="insertinvoice">
        <td><b>发票代码</b></td>
        <td><b>发票号码</b></td>
        <td><b>开票日期</b></td>
        <td><b>发票金额</b></td>
    </tr>

        {if !empty($INVOICELIST)}
            {foreach key=row_no item=data from=$INVOICELIST}
                <tr class="removetr"><td>{$data.invoice_noextend}</td><td>{$data.invoicecodeextend}</td><td>{$data.billingtimeextend}</td><td>{$data.totalandtaxextend}</td></tr>
            {/foreach}
        {/if}

    </tbody>
</table>
<br>
<table class="table table-bordered blockContainer lineItemTable tableproduct detailview-table">
    <thead>
    <tr>
        <th colspan="8">
            <img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;">
            <img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">
            &nbsp;&nbsp;回款明细
        </th>
    </tr>
    </thead>
    <tbody>
    <tr id="insertreceivepay">
        <td><b>公司账号</b></td>
        <td><b>汇款抬头</b></td>
        <td><b>入账日期</b></td>
        <td><b>匹配日期</b></td>
        <td><b>原币金额</b></td>
        <td><b>汇率</b></td>
        <td><b>金额</b></td>
    </tr>

        {if !empty($RECEIVEPAYMENTSLIST)}
            {foreach key=row_no item=data from=$RECEIVEPAYMENTSLIST['receivedpaymentlist']}
                <tr class="removetr">
                    <td>{$data.owncompany}</td>
                    <td>{$data.paytitle}</td>
                    <td>{$data.reality_date}</td>
                    <td>{$data.matchdate}</td>
                    <td>{$data.standardmoney}</td>
                    <td>{$data.exchangerate}</td>
                    <td>{$data.unit_price}</td>

                </tr>
                <tr class="removetr">
                    <td></td>
                    <td></td>
                    <td colspan="5">
                <table class="table table-bordered blockContainer lineItemTable tableproduct detailview-table">
                    <thead>
                    <tr id="insertreceivepay">
                        <th><b>业绩所属人</b></th>
                        <th><b>分成比</b></th>
                        <th><b>金额</b></th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach key=row_no item=data1 from=$RECEIVEPAYMENTSLIST['achievementallotdata'][$data['receivedpaymentsid']]}
                        <tr class="removetr">
                            <td>{$data1.receivedpaymentownid}</td>
                            <td>{$data1.scalling}%</td>
                            <td>{$data1.businessunit}</td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
                </td>
                </tr>
            {/foreach}
        {/if}

    </tbody>
</table>