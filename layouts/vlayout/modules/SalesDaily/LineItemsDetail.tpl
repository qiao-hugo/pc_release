{*<!--
/*********************************************************************************

*<td>
	是否符合Tsite{$LINE_ITEM_DETAIL["Tsite"]}是否符合Tsite新动力{$LINE_ITEM_DETAIL["TsiteNew"]}</br>
</td>
********************************************************************************/
-->*}
<!--客户统计 -->
<table class="table table-bordered blockContainer detailview-table">
    <thead>
    <tr>
        <th colspan="12">
            <img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;">
            <img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">
            &nbsp;&nbsp;客户统计
        </th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td style="text-align: right">今日提单拜访数（个）</td>
        <td style="text-align: left"><input type="text" name="todayvisitnum" value="{$ACCOUNTSTATICTICS['todayvisitnum']}" readonly></td>
        <td style="text-align: right">总电话量（个）</td>
        <td style="text-align: left"><input type="text" name="total_telnumber" value="{$ACCOUNTSTATICTICS['total_telnumber']}" readonly></td>
    </tr>
    <tr>
        <td style="text-align: right">电话量（个）</td>
        <td style="text-align: left"><input type="text" name="telnumber" value="{$ACCOUNTSTATICTICS['telnumber']}" readonly></td>
        <td style="text-align: right">接通率（%）</td>
        <td style="text-align: left"><input type="text" name="tel_connect_rate" value="{$ACCOUNTSTATICTICS['tel_connect_rate']}" readonly></td>
    </tr>
    <tr>
        <td style="text-align: right">目前微信总人数（个）</td>
        <td style="text-align: left"><input type="number" name="wxnumber" value="{$ACCOUNTSTATICTICS['wxnumber']}" readonly></td>
        <td style="text-align: right">今日新增微信（个）</td>
        <td style="text-align: left"><input type="number" name="wxnewlyaddnumber" value="{$ACCOUNTSTATICTICS['wxnewlyaddnumber']}" readonly></td>
    </tr>
    <tr>
        <td style="text-align: right">本周微信人数（个）</td>
        <td style="text-align: left"><input type="number" name="wxnumberweek" value="{$ACCOUNTSTATICTICS['wxnumberweek']}" readonly></td>
        <td style="text-align: right">相比上周增长微信数（个）</td>
        <td style="text-align: left"><input type="text" name="wxnumberweekaddnumber" value="{$ACCOUNTSTATICTICS['wxnumberweekaddnumber']}" readonly></td>
    </tr>
    <tr>
        <td style="text-align: right">本月微信人数（个）</td>
        <td style="text-align: left"><input type="number" name="wxnumbermonth" value="{$ACCOUNTSTATICTICS['wxnumbermonth']}" readonly></td>
        <td style="text-align: right">相比上月增长微信数（个）</td>
        <td style="text-align: left"><input type="text" name="wxnumbermonthaddnumber" value="{$ACCOUNTSTATICTICS['wxnumbermonthaddnumber']}" readonly></td>
    </tr>
    </tbody>
</table>

<table class="table table-bordered blockContainer detailview-table" id="lineItemNotv">
    <thead>
    <tr>
        <th colspan="9" class="blockHeader">
            <img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;">
            <img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">
            &nbsp;&nbsp;每日新增40%客户
        </th>
    </tr>

    </thead>
    <tbody>
    <tr>
        <td nowrap>客户名称</td>
        <td nowrap>来源</td>
        <td nowrap>姓名</td>
        <td nowrap>手机</td>
        <td nowrap>职位</td>
        <td nowrap>邀约拜访时间</td>
        <td nowrap>经理回访截止时间</td>
        <td nowrap>最新拜访跟进内容</td>
        <td></td>
    </tr>
    {foreach item=FOURNOTV from=$DETAILLIST['foutnotv']}
        <tr>
            <td>{$FOURNOTV['accountname']}</td>
            <td>{$FOURNOTV['leadsource']}</td>
            <td>{$FOURNOTV['linkname']}</td>
            <td>{$FOURNOTV['mobile']}</td>
            <td>{$FOURNOTV['title']}</td>
            <td nowrap>{$FOURNOTV['startdatetime']}</td>
            <td>{$FOURNOTV['mangereturnendtime']}</td>
            <td>{$FOURNOTV['commentcontent']}</td>
            <td nowrap>{if empty($FOURNOTV['mangerid']) && $CURRENT_USER_MODEL->get('id') eq $REPORTID}<a class="btn-link editmanger" title="回访添加" data-id="{$FOURNOTV['salesdailyfournotvid']}"><i class="icon-edit"></i>编辑</a>{/if}</td>

        </tr>
    {/foreach}


    </tbody>
</table>
<br />

<!--近期可成交的客户-->

<table class="table listViewEntriesTable blockContainer detailview-table" id="lineItemCanDeal">
    <thead>
    <tr>
        <th colspan="9" class="blockHeader">
            <img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;">
            <img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">
            &nbsp;&nbsp;近期可成交的客户
        </th>
    </tr>

    </thead>
    <tbody>
    <tr>
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
    {foreach item=CANDEAL from=$DETAILLIST['candeal']}
        <tr>
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
        <th colspan="15" class="blockHeader">
            <img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;">
            <img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">
            &nbsp;&nbsp;每日收款客户
        </th>
    </tr>


    </thead>
    <tbody>
    <tr>
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
    {foreach item=DAYDEAL from=$DETAILLIST['daydeal']}
        <tr>
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

<!--次日拜访情况-->
<table class="table table-bordered blockContainer detailview-table" id="lineItemNextDayVisit">
    <thead>
    <tr>
        <th colspan="8" class="blockHeader">
            <img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;">
            <img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">
            &nbsp;&nbsp;次日拜访情况
        </th>
    </tr>


    </thead>
    <tbody>
    <tr>
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
<link href='libraries/fullcalendar/fullcalendar.css' rel='stylesheet' />
<link href='libraries/fullcalendar/fullcalendar.print.css' rel='stylesheet' media='print' />
<script type="text/javascript" src="libraries/fullcalendar/fullcalendar.min.js" ></script>

<script>

    var currentmonth={$CURRENTMONTHSA};
</script>
