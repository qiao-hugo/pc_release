
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
    {assign var=CANDEALACCOUTPRODUCR value=$RECORD->getCandealAccounts()}
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
        <td style="text-align: left"><input type="text" name="todayvisitnum" value="" readonly></td>
        <td style="text-align: right">总电话量（个）</td>
        <td style="text-align: left"><input type="text" name="total_telnumber" value="" readonly></td>
    </tr>
    <tr>
        <td style="text-align: right">电话量（个）</td>
        <td style="text-align: left"><input type="text" name="telnumber" value="" readonly></td>
        <td style="text-align: right">接通率（%）</td>
        <td style="text-align: left"><input type="text" name="tel_connect_rate" value="" readonly></td>
    </tr>
    <tr>
        <td style="text-align: right"><span class="redColor">*</span>目前微信总人数（个）</td>
        <td style="text-align: left"><input type="number" name="wxnumber" value=""></td>
        <td style="text-align: right"><span class="redColor">*</span>今日新增微信（个）</td>
        <td style="text-align: left"><input type="number" name="wxnewlyaddnumber" value=""></td>
    </tr>
    <tr>
        <td style="text-align: right"><span class="redColor">*</span>本周微信人数（个）</td>
        <td style="text-align: left"><input type="number" name="wxnumberweek" value="" ></td>
        <td style="text-align: right"><span class="redColor">*</span>相比上周增长微信数（个）</td>
        <td style="text-align: left"><input type="text" name="wxnumberweekaddnumber" value="" readonly><input type="hidden" name="wxnumberlastweeknumber" value="" readonly></td>
    </tr>
    <tr>
        <td style="text-align: right"><span class="redColor">*</span>本月微信人数（个）</td>
        <td style="text-align: left"><input type="number" name="wxnumbermonth" value="" ></td>
        <td style="text-align: right"><span class="redColor">*</span>相比上月增长微信数（个）</td>
        <td style="text-align: left"><input type="text" name="wxnumbermonthaddnumber" value="" readonly><input type="hidden" name="wxnumberlastmonthnumber" value="" readonly></td>
    </tr>
    </tbody>
    </table>

    <!--新增40%的客户-->
    <table class="table table-bordered blockContainer detailview-table" id="lineItemNotv">
        <thead>
        <tr>
            <th colspan="9">
	            <img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;">
	            <img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">
	            &nbsp;&nbsp;{vtranslate('LBL_ADD_FORP_NOTV', $MODULE)}
	        </th> 
        </tr>
        <tr>
            <th style="background-color:#fff;"><b>{vtranslate('LBL_ACCOUNTNAME',$MODULE)}</b></th>
            <th style="background-color:#fff;"><b>{vtranslate('LBL_Lead_Source',$MODULE)}</b></th>
            <th style="background-color:#fff;"><b>{vtranslate('LBL_CONTACTSNAME',$MODULE)}</b></th>
            <th style="background-color:#fff;"><b>{vtranslate('LBL_PHONE',$MODULE)}</b></th>
            <th style="background-color:#fff;"><b>{vtranslate('LBL_TITLE',$MODULE)}</b></th>
            <th style="background-color:#fff;"><b>{vtranslate('LBL_STARTDATE',$MODULE)}</b></th>
            <th style="background-color:#fff;"><b>{vtranslate('LBL_Manager_return_enddate',$MODULE)}</b></th>
            <th style="background-color:#fff;"><b>{vtranslate('LBL_Manager_followup_content',$MODULE)}</b></th>
            <th style="text-align: right;margin-right: 10px;background-color:#fff;">{if empty($RECORD_ID)}<b><button class="btn btn-small" type="button" id="refreshvisitingorder" style="margin-right:5px;"><i class="icon-refresh" title="更新"></i></button><button class="btn btn-small" type="button" id="addvisitingorder"><i class="icon-plus" title="添加"></i></button></b>{/if}</th>
        </tr>
        </thead>
        <tbody>
        {foreach item=FOURNOTV from=$EDITLIST['foutnotv']}
            <tr>
                <td><input type="hidden" name="editfnvaccount[{$FOURNOTV['salesdailyfournotvid']}]" value="{$FOURNOTV['salesdailyfournotvid']}"/>{$FOURNOTV['accountname']}</td>
                <td><input type="hidden" name="editfnvaccountisupdate[{$FOURNOTV['salesdailyfournotvid']}]" value="0" id="editfnvaccountisupdate{$FOURNOTV['salesdailyfournotvid']}"/>{$FOURNOTV['leadsource']}</td>
                <td>{$FOURNOTV['linkname']}</td>
                <td>{$FOURNOTV['mobile']}</td>
                <td>{$FOURNOTV['title']}</td>
                <td nowrap>{$FOURNOTV['startdatetime']}</td>
                <td>{$FOURNOTV['mangereturnendtime']}</td>
                <td>{$FOURNOTV['commentcontent']}</td>
                <td nowrap>{*<a class="displayRecordButton" data-id="editfnvaccountisupdate{$FOURNOTV['salesdailyfournotvid']}" style="cursor:pointer;"><i title="删除" class="icon-trash alignMiddle"></i></a>*}</td>

            </tr>
        {/foreach}
        

        </tbody>
    </table>
    <br />

    <!--近期可成交的客户-->

    <table class="table table-bordered blockContainer detailview-table" id="lineItemCanDeal">
        <thead>
        <tr>
            <th colspan="10">
                <img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;">
                <img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">
                &nbsp;&nbsp;{vtranslate('LBL_ADD_CAN_DEAL', $MODULE)}
            </th>
        </tr>
        <tr>
            <th style="background-color:#fff;" nowrap><b>客户名称</b></th>
            <th style="background-color:#fff;" nowrap><b>姓名</b></th>
            <th style="background-color:#fff;" nowrap><b>手机</b></th>
            <th style="background-color:#fff;" nowrap><b>职位</b></th>
            <th style="background-color:#fff;" nowrap><span class="redColor">*</span><b>客户情况</b></th>
            <th style="background-color:#fff;" nowrap><span class="redColor">*</span><b>产品</b></th>
            <th style="background-color:#fff;" nowrap><span class="redColor">*</span><b>报价</b></th>
            <th style="background-color:#fff;" nowrap><span class="redColor">*</span><b>首付款</b></th>
            <th style="background-color:#fff;" nowrap><b>已签合同</b></th>
            <th style="text-align: right;margin-right: 10px;background-color:#fff;" nowrap>{if empty($RECORD_ID)}<b><button class="btn btn-small" type="button" id="refreshcandeal" style="margin-right:5px;display:none;"><i class="icon-refresh" title="更新"></i></button>{/if}<button class="btn btn-small" type="button" id="addCanDeal"><i class="icon-plus" title="添加"></i></button></b></th>
        </tr>

        </thead>
        <tbody>
        {if empty($RECORD_ID)}
        {foreach item=CANDEAL from=$EDITLIST['candeal']}
            <tr class="candealaccount{$CANDEAL['accountid']}">
                <td nowrap><input type="hidden" name="prevcandealrecordid[{$CANDEAL['salesdailycandealid']}]" value="{$CANDEAL['salesdailycandealid']}"/>{$CANDEAL['accountname']}</td>
                <td nowrap><input type="hidden" name="prevcandealdeleted[{$CANDEAL['salesdailycandealid']}]" value="0" id="candealdeleted{$CANDEAL['salesdailycandealid']}"/>{$CANDEAL['contactname']}</td>
                <td nowrap>{$CANDEAL['mobile']}</td>
                <td nowrap>{$CANDEAL['title']}</td>
                <td nowrap>{$CANDEAL['accountcontent']}</td>
                <td nowrap>{$CANDEAL['productname']}</td>
                <td nowrap>{$CANDEAL['quote']}</td>
                <td nowrap>{$CANDEAL['firstpayment']}</td>
                <td nowrap><label style="display:inline-block;"><input type="radio" data-id="{$CANDEAL['salesdailycandealid']}" class="ncandealissigncontract" name="prevcandealissigncontract[{$CANDEAL['salesdailycandealid']}]" value="1">是</label><label style="display:inline-block;"><input type="radio" data-id="{$CANDEAL['salesdailycandealid']}" name="prevcandealissigncontract[{$CANDEAL['salesdailycandealid']}]" value="0" checked>否</label></td>
                <td nowrap><a class="displayRecordButton" data-id="candealdeleted{$CANDEAL['salesdailycandealid']}" style="cursor:pointer;"><i title="删除" class="icon-trash alignMiddle"></i></a></td>

            </tr>
        {/foreach}
        {else}
            {foreach item=CANDEAL from=$EDITLIST['candeal']}
                <tr>
                    <td><input type="hidden" name="editcandeal[{$CANDEAL['salesdailycandealid']}]" value="{$CANDEAL['salesdailycandealid']}" />{$CANDEAL['accountname']}</td>
                    <td><input type="hidden" name="editcandealdeleted[{$CANDEAL['salesdailycandealid']}]" value="0" id="candealdeleted{$CANDEAL['salesdailycandealid']}"/>{$CANDEAL['contactname']}</td>
                    <td>{$CANDEAL['mobile']}</td>
                    <td>{$CANDEAL['title']}</td>
                    <td><input type="text" name="editcandealaccountcontent[{$CANDEAL['salesdailycandealid']}]" value="{$CANDEAL['accountcontent']}" style="width:100px;" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"></td>
                    <td><input type="text" name="editcandealproduct[{$CANDEAL['salesdailycandealid']}]" value="{$CANDEAL['productname']}" style="width:100px;" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"></td>
                    <td><input type="text" name="editcandealquote[{$CANDEAL['salesdailycandealid']}]" value="{$CANDEAL['quote']}" style="width:100px;"  data-validation-engine="validate[required,min[1],funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"></td>
                    <td><input type="text" name="editcandealfirstpayment[{$CANDEAL['salesdailycandealid']}]" value="{$CANDEAL['firstpayment']}" style="width:100px;"  data-validation-engine="validate[required,min[1],funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"></td>
                    <td><label style="display:inline-block;"><input type="radio" data-id="{$CANDEAL['salesdailycandealid']}" name="editcandealissigncontract[{$CANDEAL['salesdailycandealid']}]" value="1" {if $CANDEAL['issigncontract'] eq 1}checked{/if}>是</label><label style="display:inline-block;"><input type="radio" data-id="{$CANDEAL['salesdailycandealid']}" name="editcandealissigncontract[{$CANDEAL['salesdailycandealid']}]" value="0" {if $CANDEAL['issigncontract'] eq 0}checked{/if}>否</label></td>
                    <td><a class="displayRecordButton" data-id="candealdeleted{$CANDEAL['salesdailycandealid']}" style="cursor:pointer;"><i title="删除" class="icon-trash alignMiddle"></i></a></td>

                </tr>
            {/foreach}
        {/if}

        </tbody>
    </table>
    <br />

    <!--每日成交客户-->
    <table class="table table-bordered blockContainer detailview-table" id="lineItemDayDeal">
        <thead>
        <tr>
            <th colspan="16">
                <img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;">
                <img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">
                &nbsp;&nbsp;每日收款客户<span class="label label-a_exception">(当日匹配的回款)</span>
            </th>
        </tr>
        <tr>
            <th style="background-color:#fff;" nowrap><b>客户名称</b></th>
            <th style="background-color:#fff;" nowrap><span class="redColor">*</span><b>成交业务</b></th>
            <th style="background-color:#fff;" nowrap><span class="redColor">*</span><b>市场价</b></th>
            <th style="background-color:#fff;" nowrap><span class="redColor">*</span><b>成交金额</b></th>
            <th style="background-color:#fff;" nowrap><span class="redColor">*</span><b>是否全款</b></th>
            <th style="background-color:#fff;" nowrap><span class="redColor">*</span><b>到款性质</b></th>
            <th style="background-color:#fff;" nowrap><span class="redColor">*</span><b>收款</b></th>
            <th style="background-color:#fff;" nowrap><b>拜访次数</b></th>
            <th style="background-color:#fff;" nowrap><b>老客户</b></th>
            <th style="background-color:#fff;" nowrap><b>行业</b></th>
            <th style="background-color:#fff;" nowrap><b>拜访对象</b></th>
            <th style="background-color:#fff;" nowrap><b>有陪访</b></th>
            <th style="background-color:#fff;" nowrap><b>陪访者</b></th>
            <th style="background-color:#fff;" nowrap><b>折扣</b></td>
            <th style="background-color:#fff;" nowrap><b>到账业绩</b></th>
            <th style="text-align: right;margin-right: 10px;background-color:#fff;" nowrap><b>{if empty($RECORD_ID)}<button class="btn btn-small" type="button" id="addDayDeal"><i class="icon-refresh" title="添加"></i></button>{/if}</b></th>
        </tr>

        </thead>
        <tbody>
        {if !empty($RECORD_ID)}
        {foreach item=DAYDEAL from=$EDITLIST['daydeal']}
            <tr>
                <td><input type="hidden" name="editdaydeal[{$DAYDEAL['salesadailydaydealid']}]" value="{$DAYDEAL['salesadailydaydealid']}" />{$DAYDEAL['accountname']}</td>
                <td class="daydealproduct"><input type="hidden" name="editdaydealisupdate[{$DAYDEAL['salesadailydaydealid']}]" id="editdaydealisupdate{$DAYDEAL['salesadailydaydealid']}" value="0" />
                    <input type="hidden" name="editdaydealproduct[{$DAYDEAL['salesadailydaydealid']}]" value="{$DAYDEAL['productid']}">{$DAYDEAL['productname']}</td>
                <td><input type="hidden" name="editdealstepprice[{$DAYDEAL['salesadailydaydealid']}]" data-id="{$DAYDEAL['salesadailydaydealid']}" class="daydealstepprice{$DAYDEAL['salesadailydaydealid']}" value="{$DAYDEAL['costprice']}"><input type="hidden" name="editdaydealmarketprice[{$DAYDEAL['salesadailydaydealid']}]" data-id="{$DAYDEAL['salesadailydaydealid']}" class="checkdiscount daydealmarketprice{$DAYDEAL['salesadailydaydealid']}" value="{$DAYDEAL['marketprice']}" style="width:80px;" >{$DAYDEAL['marketprice']}</td>
                <td><input type="hidden" name="editdaydealdealamount[{$DAYDEAL['salesadailydaydealid']}]" data-id="{$DAYDEAL['salesadailydaydealid']}" class="checkdiscount daydealdealamount{$DAYDEAL['salesadailydaydealid']}" value="{$DAYDEAL['dealamount']}" style="width:80px;" >{$DAYDEAL['dealamount']}</td>
                <td><label style="display:inline-block;"><input type="radio" data-id="{$DAYDEAL['salesadailydaydealid']}" class="daydealallamount{$DAYDEAL['salesadailydaydealid']}" name="editdaydealallamount[{$DAYDEAL['salesadailydaydealid']}]" value="1" {if $DAYDEAL['allamount'] eq 1}checked{/if}>是</label><label style="display:inline-block;"><input type="radio" name="editdaydealallamount[{$DAYDEAL['salesadailydaydealid']}]" value="0" {if $DAYDEAL['allamount'] eq 0}checked{/if}>否</label></td>
                <td class="datdealpaymentnature" nowrap=""><label style="display:inline-block;"><input type="radio" data-id="{$DAYDEAL['salesadailydaydealid']}" class="daydealpaymentnature{$DAYDEAL['salesadailydaydealid']}" name="editdaypaymentnature[{$DAYDEAL['salesadailydaydealid']}]" value="firstpaymentnature" {if $DAYDEAL['paymentnature'] eq 'firstpaymentnature'}checked{/if}>首付款</label><label style="display:inline-block;"><input type="radio" name="editdaypaymentnature[{$DAYDEAL['salesadailydaydealid']}]" value="lastpaymentnature" {if $DAYDEAL['paymentnature'] eq 'lastpaymentnature'}checked{/if}>尾款</label></td>
                <td><input type="hidden" name="editdaydealfirstpayment[{$DAYDEAL['salesadailydaydealid']}]" data-id="{$DAYDEAL['salesadailydaydealid']}" class="checkdiscount daydealfirstpayment{$DAYDEAL['salesadailydaydealid']}" value="{$DAYDEAL['firstpayment']}" style="width:80px;">{$DAYDEAL['firstpayment']}</td>
                <td>{$DAYDEAL['visitingordercount']}</td>
                <td>{$DAYDEAL['oldcustomers']}</td>
                <td>{$DAYDEAL['industry']}</td>
                <td>{$DAYDEAL['visitingobj']}</td>
                <td>{$DAYDEAL['isvisitor']}</td>
                <td>{$DAYDEAL['withvisitor']}</td>
                <td class="daydealdiscount" nowrap>{$DAYDEAL['discount']}</td>
                <td class="daydealarrivalamount"><input type="hidden" name="editdaydealarrivalamount[{$DAYDEAL['salesadailydaydealid']}]" data-id="{$DAYDEAL['salesadailydaydealid']}" class="daydealarrivalamount{$DAYDEAL['salesadailydaydealid']}" value="{$DAYDEAL['arrivalamount']}" style="width:80px;"><span class="daydealarrivalamount{$DAYDEAL['salesadailydaydealid']}text">{$DAYDEAL['arrivalamount']}</span></td>
                <td>{*<a class="displayRecordButton" data-id="editdaydealisupdate{$DAYDEAL['salesadailydaydealid']}" style="cursor:pointer;"><i title="删除" class="icon-trash alignMiddle"></i></a>*}</td>
            </tr>
        {/foreach}
        {/if}

        </tbody>
    </table>
    <br />

    <!--次日拜访情况-->
    <table class="table table-bordered blockContainer detailview-table" id="lineItemNextDayVisit">
        <thead>
        <tr>
            <th colspan="9">
                <img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/softed/images/arrowRight.png" data-mode="hide" data-id="141" style="display: none;">
                <img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/softed/images/arrowDown.png" data-mode="show" data-id="141" style="display: inline;">
                &nbsp;&nbsp;次日拜访情况
            </th>
        </tr>
        <tr>
            <th style="background-color:#fff;"><b>客户名称</b></th>
            <th style="background-color:#fff;"><b>姓名</b></th>
            <th style="background-color:#fff;"><b>是否老板</b></th>
            <th style="background-color:#fff;"><b>第几次拜访</b></th>
            <th style="background-color:#fff;"><b>拜访说明</b></th>
            <th style="background-color:#fff;"><b>有陪访</b></th>
            <th style="background-color:#fff;"><b>陪访者</b></th>
            <th style="background-color:#fff;"><b>是否审核</b></th>
            <th style="text-align: right;margin-right: 10px;background-color:#fff;">{if empty($RECORD_ID)}<b><button class="btn btn-small" type="button" id="refreshnextdayvisit" style="margin-right:5px;"><i class="icon-refresh" title="更新"></i></button></b>{/if}</th>
        </tr>

        </thead>
        <tbody>
        {foreach item=NEXTDAYVISIT from=$EDITLIST['nextdayvisit']}
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
<script>

    var accountinfo='<select class="chzn-select accountmsg ###" style="width:120px;"><option value="">--请选择--</option>{foreach key=CACCOUNTID item=CACCOUNTNAME from=$CANDEALACCOUTPRODUCR['account']}<option value="{$CACCOUNTID}">{$CACCOUNTNAME}</option>{/foreach}</select>';
    var productinfo='<select class="chzn-select productmsg ###" style="width:120px;" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"><option value>--请选择--</option>{foreach key=CACCOUNTID item=CACCOUNTNAME from=$CANDEALACCOUTPRODUCR['product']}<option value="{$CACCOUNTID}" data-price="{$CACCOUNTNAME['marketprice']}" data-stepprice="{$CACCOUNTNAME['performance']}">{$CACCOUNTNAME['name']}</option>{/foreach}</select>';
    var changedate='{date("Y-m-d")}';
</script>


{/strip}