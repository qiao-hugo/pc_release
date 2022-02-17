{*<!--
/*********************************************************************************

*<td>
	是否符合Tsite{$LINE_ITEM_DETAIL["Tsite"]}是否符合Tsite新动力{$LINE_ITEM_DETAIL["TsiteNew"]}</br>
</td>
********************************************************************************/
-->*}
<table class="table table-bordered blockContainer detailview-table" id="lineItemNotv">
    <thead>
    <tr>
        <th colspan="11" class="blockHeader">
            <img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;">
            <img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">
            &nbsp;&nbsp;当月新增40%客户 总计  <span class="label label-warning">{$DETAILLIST['foutnotv']|count}</span>
        </th>
    </tr>

    </thead>
    <tbody>
    <tr>
        <td nowrap>序号</td>
        <td nowrap>日期</td>
        <td nowrap>负责人</td>
        <td nowrap>客户名称</td>
        <td nowrap>来源</td>
        <td nowrap>姓名</td>
        <td nowrap>手机</td>
        <td nowrap>职位</td>
        <td nowrap>邀约拜访时间</td>
        <td nowrap>经理回访截止时间</td>
        <td nowrap>最新拜访跟进内容</td>

    </tr>
    {foreach item=FOURNOTV from=$DETAILLIST['foutnotv'] name="fournotv" key="keyvalue"}
        <tr>
            <td>{$smarty.foreach.fournotv.index+1}</td>
            <td>{$FOURNOTV['dailydatetime']}</td>
            <td>{$FOURNOTV['username']}</td>
            <td>{$FOURNOTV['accountname']}</td>
            <td>{$FOURNOTV['leadsource']}</td>
            <td>{$FOURNOTV['linkname']}</td>
            <td>{$FOURNOTV['mobile']}</td>
            <td>{$FOURNOTV['title']}</td>
            <td nowrap>{$FOURNOTV['startdatetime']}</td>
            <td>{$FOURNOTV['mangereturntime']}</td>
            <td>{$FOURNOTV['commentcontent']}</td>
        </tr>
    {/foreach}


    </tbody>
</table>
<br />

<!--近期可成交的客户-->

<table class="table table-bordered blockContainer detailview-table" id="lineItemCanDeal">
    <thead>
    <tr>
        <th colspan="12" class="blockHeader">
            <img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;">
            <img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">
            &nbsp;&nbsp;当月可成交的客户 总计  <span class="label label-warning">{$DETAILLIST['candeal']|count}</span> 已成交 <span class="label label-success">{$DETAILLIST['candealcontract']}</span>  已放弃 <span class="label label-a_exception">{$DETAILLIST['candealdelete']}</span>
        </th>
    </tr>

    </thead>
    <tbody>
    <tr>
        <td nowrap>序号</td>
        <td nowrap>日期</td>
        <td nowrap>负责人</td>
        <td nowrap>客户名称</td>
        <td nowrap>姓名</td>
        <td nowrap>手机</td>
        <td nowrap>职位</td>
        <td nowrap>客户情况</td>
        <td nowrap>产品</td>
        <td nowrap>报价</td>
        <td nowrap>首付款</td>
        <td nowrap>已签合同</td>
    </tr>
    {foreach item=CANDEAL from=$DETAILLIST['candeal'] name="candealn"}
        <tr>
            <td{$CANDEAL['datacolor']}>{$smarty.foreach.candealn.index+1}</td>
            <td{$CANDEAL['datacolor']}>{$CANDEAL['dailydatetime']}</td>
            <td{$CANDEAL['datacolor']}>{$CANDEAL['username']}</td>
            <td{$CANDEAL['datacolor']}>{$CANDEAL['accountname']}</td>
            <td{$CANDEAL['datacolor']}>{$CANDEAL['contactname']}</td>
            <td{$CANDEAL['datacolor']}>{$CANDEAL['mobile']}</td>
            <td{$CANDEAL['datacolor']}>{$CANDEAL['title']}</td>
            <td{$CANDEAL['datacolor']}>{$CANDEAL['accountcontent']}</td>
            <td{$CANDEAL['datacolor']}>{$CANDEAL['productname']}</td>
            <td{$CANDEAL['datacolor']}>{$CANDEAL['quote']}</td>
            <td{$CANDEAL['datacolor']}>{$CANDEAL['firstpayment']}</td>
            <td{$CANDEAL['datacolor']}>{$CANDEAL['issigncontract']}</td>

        </tr>
    {/foreach}
    </tbody>
</table>
<br />

<!--每日成交客户-->
<table class="table table-bordered blockContainer detailview-table" id="lineItemDayDeal">
    <thead>
    <tr>
        <th colspan="18" class="blockHeader">
            <img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;">
            <img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">
            &nbsp;&nbsp;当月收款客户&nbsp;&nbsp;当月有效业绩&nbsp;<span class="label label-b_actioning">{$DETAILLIST['daydealarrivalamount']}</span>  总计 <span class="label label-warning">{$DETAILLIST['daydeal']|count}</span>
        </th>
    </tr>


    </thead>
    <tbody>
    <tr>
        <td nowrap>序号</td>
        <td nowrap>日期</td>
        <td nowrap>负责人</td>
        <td nowrap>客户名称</td>
        <td nowrap>成交业务</td>
        <td nowrap>市场价</td>
        <td nowrap>成交金额</td>
        <td nowrap>是否全款</td>
        <td nowrap>到款性质</td>
        <td nowrap>收款</td>
        <td nowrap>拜访次数</td>
        <td nowrap>老客户</td>
        <td nowrap>行业</td>
        <td nowrap>拜访对象</td>
        <td nowrap>有陪访</td>
        <td nowrap>陪访者</td>
        <td nowrap>折扣</td>
        <td nowrap>到账业绩</td>
    </tr>
    {foreach item=DAYDEAL from=$DETAILLIST['daydeal'] name="daydealn"}
        <tr>
            <td>{$smarty.foreach.daydealn.index+1}</td>
            <td>{$DAYDEAL['dailydatetime']}</td>
            <td>{$DAYDEAL['username']}</td>
            <td>{$DAYDEAL['accountname']}</td>
            <td>{$DAYDEAL['productname']}</td>
            <td>{$DAYDEAL['marketprice']}</td>
            <td>{$DAYDEAL['dealamount']}</td>
            <td>{$DAYDEAL['allamount']}</td>
            <td>{$DAYDEAL['paymentnature']}</td>
            <td>{$DAYDEAL['firstpayment']}</td>
            <td>{$DAYDEAL['visitingordercount']}</td>
            <td>{$DAYDEAL['oldcustomers']}</td>
            <td>{$DAYDEAL['industry']}</td>
            <td>{$DAYDEAL['visitingobj']}</td>
            <td>{$DAYDEAL['isvisitor']}</td>
            <td>{$DAYDEAL['withvisitor']}</td>
            <td nowrap>{$DAYDEAL['discount']}</td>
            <td>{$DAYDEAL['arrivalamount']}</td>

        </tr>
    {/foreach}


    </tbody>
</table>
<br />
{*
<!--次日拜访情况-->
<table class="table table-bordered blockContainer detailview-table" id="lineItemNextDayVisit">
    <thead>
    <tr>
        <th colspan="10" class="blockHeader">
            <img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;">
            <img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">
            &nbsp;&nbsp;当月拜访情况
        </th>
    </tr>


    </thead>
    <tbody>
    <tr>
        <td nowrap>日期</td>
        <td nowrap>负责人</td>
        <td nowrap>客户名称</td>
        <td nowrap>姓名</td>
        <td nowrap>是否老板</td>
        <td nowrap>第几次拜访</td>
        <td nowrap>拜访说明</td>
        <td nowrap>有陪访</td>
        <td nowrap>陪访者</td>
    </tr>
    {foreach item=NEXTDAYVISIT from=$DETAILLIST['nextdayvisit']}
        <tr>
            <td>{$NEXTDAYVISIT['dailydatetime']}</td>
            <td>{$NEXTDAYVISIT['username']}</td>
            <td>{$NEXTDAYVISIT['accountname']}</td>
            <td>{$NEXTDAYVISIT['contacts']}</td>
            <td>{$NEXTDAYVISIT['title']}</td>
            <td>{$NEXTDAYVISIT['visitingordernum']}</td>
            <td>{$NEXTDAYVISIT['purpose']}</td>
            <td>{$NEXTDAYVISIT['isvisitor']}</td>
            <td nowrap>{$NEXTDAYVISIT['withvisitor']}</td>

        </tr>
    {/foreach}


    </tbody>
</table>
*}