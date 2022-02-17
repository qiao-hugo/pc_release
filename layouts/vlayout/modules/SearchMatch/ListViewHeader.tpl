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
	<div class="listViewPageDiv">
        <div>
            <form method="post"  name="SearchBug" id="SearchBug">
                <input type="hidden" value="1" id="queryaction" name="queryaction">
                <input type="hidden" value="" id="queryTitle" name="queryTitle">
                <input type="hidden" value="0" id="saveQuery" name="saveQuery">
                <input type="hidden" value="0" id="reset" name="reset">
                <input type="hidden" value="" id="showField" name="showField">
                <div id="SearchBlankCover" style="background-color: #F0F0F0;">
                    <table id="searchtable" style="margin:auto">
                        <tbody>
                            <tr class="SearchConditionRow" id="SearchConditionRow0" style="height:22px;">
                                <td>
                                    <select id="BugFreeQuery_field0" style="width:100%;color:#878787;" name="BugFreeQuery[field0]">
                                        <option value="paymentchannel" selected="selected">支付方式</option>
                                    </select>
                                </td>
                                <td>
                                    <select id="BugFreeQuery_operator0" style="width:100%;color:#878787;" name="BugFreeQuery[operator0]">
                                        <option value="UNDER" selected="selected">等于</option>
                                    </select>
                                </td>
                                <td>
                                    <select id="ChannelFilter" name="paymentchannel">
                                        <option value="" ></option>
                                        <option value="对公转账" >对公转账</option>
                                        <option value="支付宝转账" >支付宝转账</option>
                                        <option value="扫码" >扫码</option>
                                    </select>
                                </td>
                                <td>
                                    <select id="BugFreeQuery_andor0" style="width:65px;color:#878787;" name="BugFreeQuery[andor0]">
                                        <option value="And" selected="selected">并且</option>
                                    </select>
                                </td>
                            </tr>
                            <tr class="SearchConditionRow" id="SearchConditionRow1" style="height:22px;">
                                <td>
                                    <select id="BugFreeQuery_field1" style="width:100%;color:#878787;" name="BugFreeQuery[field1]">
                                        <option value="reality_date" selected="selected">入账时间</option>
                                    </select>
                                </td>
                                <td>
                                    <select id="BugFreeQuery_operator1" style="width:100%;color:#878787;" name="BugFreeQuery[operator1]">
                                        <option value="UNDER" selected="selected">等于</option>
                                    </select>
                                </td>
                                <td>
                                    <input class="span9 dateField form_datetime" value="" autocomplete="off" id="realityDateFilter" name="reality_date" size="16" type="text">
                                </td>
                                <td>
                                    <select id="BugFreeQuery_andor1" style="width:65px;color:#878787;" name="BugFreeQuery[andor1]">
                                        <option value="And" selected="selected">并且</option>
                                    </select>
                                </td>
                            </tr>
                            <tr class="SearchConditionRow" id="SearchConditionRow2" style="height:22px;">
                                <td>
                                    <select id="BugFreeQuery_field2" style="width:100%;color:#878787;" name="BugFreeQuery[field2]">
                                        <option value="paytitle" selected="selected">回款抬头</option>
                                    </select>
                                </td>
                                <td>
                                    <select id="BugFreeQuery_operator2" style="width:100%;color:#878787;" name="BugFreeQuery[operator2]">
                                        <option value="UNDER" selected="selected">等于</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" size="16" value="" id="paytitleFilter" name="paytitle" autocomplete="off">                                </td>
                                <td>
                                    <select id="BugFreeQuery_andor2" style="width:65px;color:#878787;" name="BugFreeQuery[andor2]">
                                        <option value="And" selected="selected">并且</option>
                                    </select>
                                </td>
                            </tr>
                            <tr class="SearchConditionRow" id="SearchConditionRow3" style="height:22px;">
                                <td>
                                    <select id="BugFreeQuery_field3" style="width:100%;color:#878787;" name="BugFreeQuery[field3]">
                                        <option value="paymentcode" selected="selected">交易单号</option>
                                    </select>
                                </td>
                                <td>
                                    <select id="BugFreeQuery_operator3" style="width:100%;color:#878787;" name="BugFreeQuery[operator3]">
                                        <option value="UNDER" selected="selected">等于</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" size="16" value="" id="paymentcodeFilter" name="paymentcode" autocomplete="off">                                </td>
                                <td>
                                    <select id="BugFreeQuery_andor3" style="width:65px;color:#878787;" name="BugFreeQuery[andor3]">
                                        <option value="And" selected="selected">并且</option>
                                    </select>
                                </td>
                            </tr>
                            <tr class="SearchConditionRow" id="SearchConditionRow4" style="height:22px;">
                                <td>
                                    <select id="BugFreeQuery_field4" style="width:100%;color:#878787;" name="BugFreeQuery[field4]">
                                        <option value="standardmoney" selected="selected">回款原币金额</option>
                                    </select>
                                </td>
                                <td>
                                    <select id="BugFreeQuery_operator4" style="width:100%;color:#878787;" name="BugFreeQuery[operator4]">
                                        <option value="UNDER" selected="selected">等于</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" size="16" value="" id="standardmoneyFilter" name="standardmoney" autocomplete="off">                                </td>
                                <td>
                            </tr>
                            <tr>
                                <td colspan="7">
                                    <center>
                                        <input type="button"
                                               value="提交查询" id="PostQuery" name="PostQuery" class="btn">
                                        <input type="button" onclick="setSearchConditionOrder();$('#save_query_dialog').dialog('open'); return false;"
                                               value="保存查询" id="SaveQuery" name="SaveQuery" class="btn hide">
                                        <input type="button" onclick="location.reload();"
                                               value="重置查询" class="btn">
                                    </center>
                                </td>
                            </tr>
                        </tbody>
                    </table>
            </form>
        </div>
    </div>
    {include file='../SearchMatch/DefaultListFields.tpl'|@vtemplate_path MODULE=$MODULE ISDEFAULT=true ISPAGE=true}
	<div class="listViewContentDiv" id="listViewContents">
    <script src="/libraries/jquery/chosen/chosen.jquery.min.js"></script>
    <script src="/libraries/jquery/datetimepicker/bootstrap-datetimepicker.min.js"></script>
{/strip}
<script>
    $(function(){
        $("#realityDateFilter").datetimepicker({
            format: "yyyy-mm-dd",
            language:  'zh-CN',
            autoclose: true,
            todayBtn: true,
            pickerPosition: "bottom-right",
            showMeridian: 0,
            endDate:new Date(),
            weekStart:1,
            todayHighlight:1,
            startView:2,
            minView:2,
            forceParse:0
        });

        //支付渠道变更搜索框变红
        $("#ChannelFilter").change(function () {
            $("#searchtable").find('input').each(function () {
                this.style.borderColor='#ccc';
            });
            if($(this).val()=='对公转账'){
                $("#realityDateFilter").css("border-color",'red');
                $("#paytitleFilter").css("border-color",'red');
                $("#standardmoneyFilter").css("border-color",'red');
            }else if($(this).val()=='支付宝转账'){
                $("#paytitleFilter").css("border-color",'red');
                $("#paymentcodeFilter").css("border-color",'red');
            }else if($(this).val()=='扫码'){
                $("#paymentcodeFilter").css("border-color",'red');
            }
        });
    });

</script>