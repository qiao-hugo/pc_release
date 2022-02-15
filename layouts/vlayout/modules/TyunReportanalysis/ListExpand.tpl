{strip}
    <script type="text/javascript" src="/libraries/media/jquery.dataTables.js"></script>
    <script type="text/javascript" src="/libraries/echarts/echarts.js"></script>
    <script type="text/javascript" src="/libraries/dateUtil.js"></script>
    <script type="text/javascript">
        var searchConditionRowNum = 1;
        var limitedFieldCount = 5;
        var templateFieldNumber = '999999';
        var searchParamsPreFix = 'TyunReportQuery';
        var dateFormatError = '请输入正确的日期格式。例如，2009-10-8 或 -7。';
        var field_owner_operator = '<select id=\"TyunReportQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"TyunReport[operator999999]\"><option value=\"=\" selected=\"selected\">等于</option></select>';
        var field_datetime_operator = '<select id=\"TyunReportQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"TyunReport[operator999999]\"><option value=\"LIKE\"  selected=\"selected\">等于</option><option value=\"&gt;=\"   selected=\"selected\">大于等于</option><option value=\"&lt;=\">小于等于</option></select>';
        var field_owner_value = '<select id="TyunReportQuery_value999999" name="TyunReport_owner[value999999]" style="width:300px;">{foreach key=index item=value from=$USERDEPARTMENT}<option value="{$value.id}">{$value.last_name}</option>{/foreach}</select>';
        var field_datetime_value = '<div id="TyunReportQuery_value999999"><input id="TyunReportQuery_start_value999999" class="span9 dateField form_datetime" style="width: 120px;margin-right: 10px;" name="TyunReport_start[value999999]" size="16" type="text" value="" >到<input id="TyunReportQuery_end_value999999" style="width: 120px;margin-left: 10px;" class="span9 dateField form_datetime" name="TyunReport_end[value999999]" size="16" type="text" value="" ></div>';
    </script>
    <style>
        .report_date_on{
            background: #2196F3;
            line-height: 30px;
            padding: 0 10px;
            cursor: pointer;
            color: #fff;
            border-radius: 5px;
            height: 30px;
        }
        .dateSelect{
            padding: 8px 0px;
            margin: 0 0 20px;
            list-style: none;
            display: inline-flex;
            font-size: 16px;
        }
        .dateSelect li{
            line-height: 30px;
            margin-right: 20px;
        }
        .div_stat_index input{
            width: 15px;
            height: 15px;
            margin-bottom: 6px;
        }
        .lbl_dim_title{
            height: 30px;
            line-height: 30px;
            padding-right: 10px;
        }
        .div_report_user_hide{
            display: none;
        }
    </style>
<div style="margin-right:20px;margin-top:10px;">
    <div class="row-fluid" id="c" style="width:100%;">
        <div style="border-right:1px #ccc solid;">
            <table class="table">
                <tr>
                    <td style="border-right: 1px solid #ddd;padding-right: 20px;width: 80px;"><label class="pull-right" style="font-weight: bold;">统计条件</label></td>
                    {*<td style="width: 250px;">
                        <div class="pull-left div_stat_index" style=" padding-left: 20px;">
                            <label class="pull-left" style="margin-right: 20px;">
                                <input type="radio" name="radio_stat_date_index" value="1" checked>按签单日期
                            </label>
                            <label class="pull-left" style="margin-right: 20px;">
                                <input type="radio" name="radio_stat_date_index" value="2" >按创建日期
                            </label>
                        </div>
                    </td>*}
                    <td colspan="3">
                        <div id="SearchBlankCover" style="background-color: #F0F0F0;">
                            <table id="searchtable" style="margin:auto">
                                <tbody>
                                <tr class="SearchConditionRow" id="SearchConditionRow0" data-index="0" style="height:22px;">
                                    {*<td>
                                        <input type="hidden" value="" name="TyunReportQuery[leftParenthesesName0]"
                                               id="TyunReportQuery_leftParenthesesName0">
                                    </td>*}
                                    <td>
                                        <select id="TyunReportQuery_field0" style="width:100%;color:#878787;" name="TyunReportQuery[field0]">
                                            <option value="vtiger_departments.departmentname" data="signdempart" fieldtype="owner" selected="selected">
                                                部门
                                            </option>
                                        </select>
                                    </td>
                                    {*<td>
                                        <select id="TyunReportQuery_operator0" style="width:100%;color:#878787;"
                                                name="TyunReportQuery[operator0]">
                                            <option value="=" selected="selected">
                                                等于
                                            </option>
                                        </select>
                                    </td>*}
                                    <td>
                                        {*{assign var =Department value=getDepartment()}
                                        <select id="TyunReportQuery_value0" name="department" style="width: 300px;">
                                            {foreach item="departname" key="departmentid" from=$Department}
                                                <option value="{$departmentid}">{$departname}</option>
                                            {/foreach}
                                        </select>*}
                                        {assign var=arr value=['H3']}
                                        <select id="department_editView_fieldName_dropDown" class="chzn-select referenceModulesList streched" multiple style="width:500px;">
                                            {foreach key=index item=value from=$DEPARTMENTUSER}
                                                <option value="{$index}" {if in_array($index,$arr)} selected{/if}>{str_replace(array('|','—'),array('',''),$value)}</option>
                                            {/foreach}
                                        </select>
                                    </td>
                                   {* <td>
                                        <input type="hidden" value="" name="TyunReportQuery[rightParenthesesName0]"
                                               id="TyunReportQuery_rightParenthesesName0">
                                    </td>*}
                                    <td>
                                        <select id="TyunReportQuery_andor0" style="width:65px;color:#878787;" name="TyunReportQuery[andor0]">
                                            <option value="And" selected="selected">
                                                并且
                                            </option>
                                        </select>
                                    </td>
                                    <td>
                                        <a class="add_report_search_button" href="javascript:addSearchField(0);">
                                            <img src="layouts/vlayout/skins/softed/images/add_search.gif">
                                        </a>
                                    </td>
                                </tr>

                                </tbody>
                            </table>

                            </form>
                        </div>
                        {*<div class="pull-left" style="margin-right:20px;height: 44px;">
                            <ul class="dateSelect">
                                <li class="btn-link report_date_on">今天</li>
                                <li class="btn-link">昨天</li>
                                <li class="btn-link">本周</li>
                                <li class="btn-link">上周</li>
                                <li class="btn-link">本月</li>
                                <li class="btn-link">上月</li>
                                <li class="btn-link">最近30天</li>
                                <li class="btn-link">今年</li>
                                <li class="btn-link">去年</li>
                                <li class="btn-link">
                                    <label class="pull-left timecode" style="width:100px;"><input class="span12 dateField"type="text"  id="start_date" value="" readonly></label>
                                    <label class="pull-left timecode" style="margin:5px 10px 0;">到</label>
                                    <label class="pull-left timecode" style="width:100px;"><input class="span12 dateField"  type="text" name="end_date" data-date-format="yyyy-mm-dd" id="end_date" value="" readonly></label>
                                </li>
                            </ul>
                        </div>*}
                    </td>
                </tr>
                <tr>
                    <td style="border-right: 1px solid #ddd;padding-right: 20px;"><label class="pull-right" style="font-weight: bold;">统计维度</label></td>
                    <td colspan="3">
                        <div class="pull-left div_stat_index" style=" padding-left: 20px;">
                            <label class="pull-left" style="margin-right: 20px;">
                                <input type="radio" name="radio_stat_dim" value="1" checked>按部门
                            </label>
                            <label class="pull-left" style="margin-right: 20px;">
                                <input type="radio" name="radio_stat_dim" value="2" >按负责人
                            </label>
                        </div>
                    </td>
                    {*<td style="width: 550px;"><div class="pull-left">
                            <label class="pull-left lbl_dim_title">部门</label>
                            <select id="department_editView_fieldName_dropDown" class="chzn-select referenceModulesList streched" multiple style="width:500px;">
                                {foreach key=index item=value from=$DEPARTMENTUSER}
                                    <option value="{$index}" {if in_array($index,$arr)} selected{/if}>{str_replace(array('|','—'),array('',''),$value)}</option>
                                {/foreach}
                            </select></div>
                    </td>
                    <td>
                        <div class="pull-left div_report_user_hide" id="div_user_dim">
                            <label class="pull-left lbl_dim_title">负责人</label>
                            <label class="pull-left">
                                <select id="user_editView_fieldName_dropDown" class="chzn-select referenceModulesList streched" multiple style="width:300px;">
                                    {foreach key=index item=value from=$USERDEPARTMENT}
                                        <option value="{$value.id}">{$value.last_name}</option>
                                    {/foreach}
                                </select></label>
                        </div>
                    </td>*}

                </tr>
                {*<tr>
                    <td style="border-right: 1px solid #ddd;padding-right: 20px;"><label class="pull-right" style="font-weight: bold;">统计指标</label></td>
                    <td colspan="4"><div class="pull-left div_stat_index" style="padding-left: 20px;">
                            <label class="pull-left" style="margin-right: 20px;">
                                <input type="radio" name="radio_stat_index" value="1" checked>客户数量
                            </label>
                            <label class="pull-left" style="margin-right: 20px;">
                                <input  type="radio" name="radio_stat_index" value="2" >合同金额
                            </label>
                            <label class="pull-left" style="margin-right: 20px;">
                                <input  type="radio" name="radio_stat_index" value="3" >回款金额
                            </label>
                            <label class="pull-left" style="margin-right: 20px;">
                                <input type="radio" name="radio_stat_index" value="4" >未回款金额
                            </label>
                            <label class="pull-left" style="margin-right: 20px;">
                                <input type="radio" name="radio_stat_index" value="5" >开票金额
                            </label>
                            <label class="pull-left" style="margin-right: 20px;">
                                <input type="radio" name="radio_stat_index" value="6" >未开票金额
                            </label>
                            *}{*<label class="pull-left" style="margin-right: 20px;">
                                <input type="radio" name="radio_stat_index" value="7" >拜访单数
                            </label>*}{*
                        </div>
                    </td>
                </tr>*}
                <tr>
                    <td style="border-right: 1px solid #ddd;padding-right: 20px;"><label class="pull-right" style="font-weight: bold;">显示方式</label></td>
                    <td colspan="4">
                        <div class="pull-left div_stat_index" style="padding-left: 20px;">
                            <label class="pull-left" style="margin-right: 20px;">
                                <input type="radio" name="radio_stat_type" value="1" checked>按日
                            </label>
                            {* <label class="pull-left" style="margin-right: 20px;">
                                 <input  type="radio" name="radio_stat_type" value="2" >按周
                             </label>*}
                            <label class="pull-left" style="margin-right: 20px;">
                                <input  type="radio" name="radio_stat_type" value="3" >按月
                            </label>
                            {*<label class="pull-left" style="margin-right: 20px;">
                                <input  type="radio" name="radio_stat_type" value="4" >按年
                            </label>*}
                            <label class="pull-left" style="margin-right: 20px;">
                                <input  type="radio" name="radio_stat_type" value="5" >按版本
                            </label>
                        </div>
                    </td>
                </tr>
                <tr id="tr_stat_type">
                    <td style="border-right: 1px solid #ddd;padding-right: 20px;"><label class="pull-right" style="font-weight: bold;">统计方式</label></td>
                    <td colspan="4">
                        <div class="pull-left div_stat_index" style="padding-left: 20px;">
                            <label class="pull-left" style="margin-right: 20px;">
                                <input type="radio" name="radio_stat_date_type" value="1" checked>按签单日期
                            </label>
                            <label class="pull-left" style="margin-right: 20px;">
                                <input  type="radio" name="radio_stat_date_type" value="2" >按回款日期
                            </label>

                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="5"><label style="text-align:center"><input type="button" value="提交查询" id="PostQuery" name="PostQuery" class="btn"></label></td>
                </tr>
            </table>
        </div>
        <div style="border:1px solid #ccc;margin:0 auto 20px;">
            <table class="table">
                <tr>
                    {*<td style="border-top:0px;width: 130px;"><label class="pull-right" style="font-weight: bold;">统计指标</label></td>*}
                    <td style="border-top:0px;padding-top: 10px;padding-left: 55px;"><div class="pull-left div_stat_index" style="padding-left: 20px;">
                            <label class="pull-left" style="margin-right: 20px;">
                                <input type="radio" name="radio_stat_index" value="1" checked>客户数量
                            </label>
                            <label class="pull-left" style="margin-right: 20px;">
                                <input  type="radio" name="radio_stat_index" value="2" >合同金额
                            </label>
                            <label class="pull-left" style="margin-right: 20px;">
                                <input  type="radio" name="radio_stat_index" value="3" >回款金额
                            </label>
                            <label class="pull-left" style="margin-right: 20px;">
                                <input type="radio" name="radio_stat_index" value="4" >未回款金额
                            </label>
                            <label class="pull-left" style="margin-right: 20px;">
                                <input type="radio" name="radio_stat_index" value="5" >开票金额
                            </label>
                            <label class="pull-left" style="margin-right: 20px;">
                                <input type="radio" name="radio_stat_index" value="6" >未开票金额
                            </label>
                            {*<label class="pull-left" style="margin-right: 20px;">
                                <input type="radio" name="radio_stat_index" value="7" >拜访单数
                            </label>*}
                        </div>
                    </td>
                </tr>
            </table>
            <div id="bartablea" style="height:400px;"></div></div>

        <div id="tyun_list_data" style="display:none">
            <div id="msg" style="height:20px;margin:0 auto;border:1px solid #ccc;border-bottom: none;padding-top:20px;"></div>
            <div id="tyun_etail_data" style="border:1px solid #ccc;margin:0 auto 40px;border-top:none;"></div>
        </div>
        {*<div style="border:1px solid #ccc;margin:0 auto 20px;padding-right:20px;"><div id="bartableb" style="height:400px;"></div></div>*}

       {* <div id="listdatab" style="display:none">
            <div id="msgb" style="height:20px;margin:0 auto;border:1px solid #ccc;border-bottom: none;padding-top:20px;"></div>
            <div id="detailtableb" style="border:1px solid #ccc;margin:0 auto 40px;border-top:none;"></div>
        </div>
        <div style="border:1px solid #ccc;margin:0 auto 20px;padding-right:20px;"><div id="bartablec" style="height:400px;"></div></div>

        <div id="listdatac" style="display:none">
            <div id="msgc" style="height:20px;margin:0 auto;border:1px solid #ccc;border-bottom: none;padding-top:20px;"></div>
            <div id="detailtablec" style="border:1px solid #ccc;margin:0 auto 40px;border-top:none;"></div>
        </div>
        <div style="border:1px solid #ccc;margin:0 auto 20px;padding-right:20px;"><div id="bartabled" style="height:400px;"></div></div>

        <div id="listdatad" style="display:none">
            <div id="msgd" style="height:20px;margin:0 auto;border:1px solid #ccc;border-bottom: none;padding-top:20px;"></div>
            <div id="detailtabled" style="border:1px solid #ccc;margin:0 auto 40px;border-top:none;"></div>
        </div>*}

    </div>
</div>
<table id="tbl_copy_condition" style="display: none">
    <tbody>
    <tr class="SearchConditionRow" id="SearchConditionRow999999" data-index="999999">
        {*<td>
            <select id="TyunReportQuery_leftParenthesesName999999" onchange="validateParentheses()" style="width:48px;" name="TyunReportQuery[leftParenthesesName999999]">
                <option value="" selected="selected"></option>
                <option value="(">(</option></select>
        </td>*}
        <td>
            <select id="TyunReportQuery_field999999" onchange="updateQueryRow(999999);" name="TyunReportQuery[field999999]">
                <option value="vtiger_servicecontracts.signdate" data="signdate" fieldtype="datetime">签单日期</option>
                <option value="vtiger_crmentity.smownerid" data="smownerid" fieldtype="owner">负责人</option>
                <option value="vtiger_receivedpayments.reality_date" data="realitydate" fieldtype="datetime">回款日期</option>
            </select></td>
        {*<td>
            <select id="TyunReportQuery_operator999999" style="width:100%" onchange="updateQueryValue(999999,true);" name="TyunReportQuery[operator999999]">
                <option value="=" selected="selected">等于</option></select>
        </td>*}
        <td>
            <div id="TyunReportQuery_value999999">
                <input id="TyunReportQuery_start_value999999" class="span9 dateField form_datetime" style="width: 120px;margin-right: 10px;" name="TyunReport_start[value999999]" size="16" type="text" value="" >到<input id="TyunReportQuery_end_value999999" style="width: 120px;margin-left: 10px;" class="span9 dateField form_datetime" name="TyunReport_end[value999999]" size="16" type="text" value="" ></div>

           {* <select id="TyunReportQuery_value999999" name="TyunReport_owner[value999999]" style="width:300px;">
                {foreach key=index item=value from=$USERDEPARTMENT}
                    <option value="{$value.id}">{$value.last_name}</option>
                {/foreach}
            </select>*}
        </td>
        {*<td>
            <select id="TyunReportQuery_rightParenthesesName999999" onchange="validateParentheses()" style="width:48px;" name="TyunReportQuery[rightParenthesesName999999]">
                <option value="" selected="selected"></option>
                <option value=")">)</option></select>
        </td>*}
        <td>
            <select id="TyunReportQuery_andor999999" style="width:65px;" name="TyunReportQuery[andor999999]">
                <option value="And">并且</option>
               {* <option value="Or">或者</option>*}
            </select>
        </td>
        <td>
            <a class="add_search_button" href="javascript:addSearchField(999999);">
                <img src="layouts/vlayout/skins/softed/images/add_search.gif" /></a>&nbsp;&nbsp;
            <a class="cancel_search_button" href="javascript:removeSearchField(999999);">
                <img src="layouts/vlayout/skins/softed/images/cancel_search.gif" /></a></td>
        </tr>
    </tbody>
</table>
{literal}
    <script type="text/javascript">
        function addSearchField(fieldRowNum)
        {
            var searchConditionTmp = $("#tbl_copy_condition tbody").html();
            var $newSearchRow = replaceTemplateWithIndex(searchConditionTmp,searchConditionRowNum);

            //if(fieldRowNum==0){
                <!--修改自定义的搜索框排序在最后一个的tr之前插入-->
             //   $("#searchtable tbody tr:last").before($($newSearchRow));

                //$('#'+searchParamsPreFix+'_leftParenthesesName'+fieldRowNum).attr('type')='hidden';
            //}else{
                $("#SearchConditionRow"+fieldRowNum).after($($newSearchRow));
            //}

            //$("#TyunReportQuery_value" + searchConditionRowNum).chosen();

            var $rowNum = $(".SearchConditionRow").length;
            if($rowNum >=limitedFieldCount )
            {
                $('.add_search_button').hide();
            }
            if($rowNum >1 )
            {
                $('.cancel_search_button').show();
            }
            if($rowNum <=1 ){

                $('.cancel_search_button').hide();
            }

            updateQueryValue(searchConditionRowNum);
            //updateQueryRow(searchConditionRowNum,false);
            //setSearchHeight();
            searchConditionRowNum++;

        }
        function validateDateFormat()
        {
            var $fieldArr = $("select[id*="+searchParamsPreFix+"_field]");
            var resultStr = '';
            $fieldArr.each(function(){
                var $selectedValue = $(this).attr('data');
                eval('var fieldType='+'field_'+$selectedValue+'_type');
                var fieldId = $(this).attr('id');
                var filedPrefix = searchParamsPreFix+'_field';
                var indexNum = fieldId.substr(filedPrefix.length,fieldId.length);
                if('datetime' == fieldType)
                {
                    var dateValue = $('#'+searchParamsPreFix+'_value'+indexNum).val();
                    if('' != dateValue)
                    {
                        if(!isDateNumber(dateValue))
                        {
                            resultStr = 'failed';
                            return false;
                        }
                    }
                }
            });
            return resultStr;
        }
        function submitSearchForm()
        {
            //alert("submitSearchForm");
            if('' != validateDateFormat())
            {
                alert(dateFormatError);
                return false;
            }
            //document.SearchBug.submit();
        }
        function setSearchConditionOrder()
        {
            var rowOrder = "";
            var $searchRows = $("tr[id^=SearchConditionRow]");
            $searchRows.each(function(){
                rowOrder += $(this).attr("id")+",";
            });
            $("#"+searchParamsPreFix+"_QueryRowOrder").attr("value",rowOrder);
        }
        function updateQueryOperator(index){
            var fieldType = $('#'+searchParamsPreFix+'_field'+index).find("option:selected").attr('fieldtype');
            eval('var fieldOperatorSelect='+'field_'+fieldType+'_operator');
            var $operatorValue = replaceTemplateWithIndex(fieldOperatorSelect,index);
            $('#'+searchParamsPreFix+'_operator'+index).replaceWith($operatorValue);
        }

        /*function updateQueryOperator(index,isKeepOldValue)
        {
            var $oldOperatorValue = $('#'+searchParamsPreFix+'_operator'+index).val();
            var $fieldName = $('#'+searchParamsPreFix+'_field'+index).find("option:selected").attr('data');

            var fieldType = $('#'+searchParamsPreFix+'_field'+index).find("option:selected").attr('fieldtype');
            //eval('var fieldType='+'field_'+$fieldName+'_type');
            var $fieldName = $('#'+searchParamsPreFix+'_field'+index).find("option:selected").attr('data');
            /!* 先隐藏掉，放开产品负责人之后打开
            if(app.getModuleName()=='ServiceMaintenance'&&$fieldName=='ownerid'){
                fieldType='mulreference';
            }*!/

            eval('var fieldOperatorSelect='+'field_'+fieldType+'_operator');

            var $operatorValue = replaceTemplateWithIndex(fieldOperatorSelect,index);
            $('#'+searchParamsPreFix+'_operator'+index).replaceWith($operatorValue);

            if(true == isKeepOldValue)
            {
                $('#'+searchParamsPreFix+'_operator'+index).attr('value',$oldOperatorValue);
            }
        }*/
        function removeSearchField(fieldRowNum)
        {
            $("#SearchConditionRow"+fieldRowNum).remove();
            if($("#SearchConditionRow"+fieldRowNum+' .chzn-container').length) {
                $("#SearchConditionRow"+fieldRowNum+' .chzn-container').remove();
            }
            validateParentheses();
            var $rowNum = $(".SearchConditionRow").length;
            if($rowNum <2 )
            {
                $('.cancel_search_button').hide();
            }
            if($rowNum <limitedFieldCount)
            {
                $('.add_search_button').show();
            }
            setSearchHeight();
        }
        function replaceTemplateWithIndex($templateStr,$index)
        {
            raRegExp = new RegExp(templateFieldNumber,"g");
            return $templateStr.replace(raRegExp,$index);
        }

        function setSearchHeight()
        {
            $height = $(window).height();
            $topheight = $('#SearchBlankCover').height();
            $('#indexmain').css('height',$height-63+'px');
            $('#SearchResultDiv').css('height',$height-$topheight-94+'px');
            $('#expandindex').show();
            //$('.chzn-container').css({'margin-top':'-6px'});
        }
        function validateParentheses()
        {
            var stack = new Array();
            var $parenthesesArr = $("select[id*=ParenthesesName]");
            $parenthesesArr.css('color','black');
            $parenthesesArr.each(function(){
                var $selectedValue = $(this).find("option:selected").text();
                $selectedValue = jQuery.trim($selectedValue);
                if('' != $selectedValue)
                {
                    if(stack.length == 0)
                    {
                        stack.push($(this));
                    }
                    else
                    {
                        if('(' == $selectedValue)
                        {
                            stack.push($(this));
                        }
                        else
                        {
                            var $preObj = stack.pop();
                            if('(' != $preObj.find("option:selected").text())
                            {
                                stack.push($preObj);
                                stack.push($(this));
                            }
                        }
                    }

                }
            });
            if(stack.length>0)
            {
                $("#SaveQuery").attr("disabled","disabled");
                $("#SaveQuery").css('color','grey');
                $("#SaveQuery").css('cursor','default');

                $("#PostQuery").attr("disabled","disabled");
                $("#PostQuery").css('color','grey');
                $("#PostQuery").css('cursor','default');
                $("#PostQuery").attr("title","请补全括号");

            }
            else
            {
                $("#PostQuery").removeAttr("disabled");
                $("#PostQuery").css('color','#000000');
                $("#PostQuery").css('cursor','pointer');
                $("#SaveQuery").removeAttr("disabled");
                $("#SaveQuery").css('color','#000000');
                $("#SaveQuery").css('cursor','pointer');
                $("#PostQuery").attr("title","点击查询");
            }
            for(var i=0;i<stack.length;i++)
            {
                stack[i].css('color','red');
            }

        }
        function updateQueryValue(index)
        {
            var $fieldType = $('#'+searchParamsPreFix+'_field'+index).find("option:selected").attr('fieldtype');
            if($("#SearchConditionRow"+index+' .chzn-container').length) {
                $("#SearchConditionRow"+index+' .chzn-container').remove();
            }
            eval('var fieldValueSelect='+'field_'+$fieldType+'_value;');
            var $newValue = replaceTemplateWithIndex(fieldValueSelect,index);
            $('#'+searchParamsPreFix+'_value'+index).replaceWith($newValue);

            if($fieldType == 'datetime'||$fieldType == 'date'){

                //$('#'+searchParamsPreFix+'_value'+index).replaceWith('<input class="span9 dateField form_datetime" id="TyunReportQuery_value'+index+'" name="TyunReportQuery[value'+index+']" size="16" type="text" name="nowritetime" value=""  >');
                $(".form_datetime").datepicker({format: 'yyyy-mm-dd',autoclose:true,language:  'zh-CN'});
                $("#TyunReportQuery_start_value"+ index).val(getMonthStartDate());
                $("#TyunReportQuery_end_value"+ index).val(getMonthEndDate());
            }
            if($fieldType == 'owner'){
                $("#TyunReportQuery_value" + index).chosen();
            }
        }
       /* function updateQueryValue(index,isKeepOldValue)
        {
            var $fieldName = $('#'+searchParamsPreFix+'_field'+index).find("option:selected").attr('data');
            var $fieldType = $('#'+searchParamsPreFix+'_field'+index).find("option:selected").attr('fieldtype');
            var $operatorValue = $('#'+searchParamsPreFix+'_operator'+index).val();
            var $oldFieldValue = $('#'+searchParamsPreFix+'_value'+index).val();
            if($("#SearchConditionRow"+index+' .chzn-container').length) {
                $("#SearchConditionRow"+index+' .chzn-container').remove();
            }
            if(('severity' == $fieldName || 'priority' == $fieldName) && 'IN' == $operatorValue)
            {
                $('#'+searchParamsPreFix+'_value'+index).replaceWith('<input type="text" size="16" value="" id="TyunReportQuery_value'+index+'" name="TyunReportQuery[value'+index+']">');
                if(true == isKeepOldValue)
                {
                    $('#'+searchParamsPreFix+'_value'+index).attr('value',$oldFieldValue);
                }
                return;
            }

            eval('var fieldValueSelect='+'field_'+$fieldType+'_value;');
            var $newValue = replaceTemplateWithIndex(fieldValueSelect,index);
            $('#'+searchParamsPreFix+'_value'+index).replaceWith($newValue);
            eval('var fieldType='+'field_'+$fieldType+'_type');

            //if(fieldType == 'owner'||fieldType == 'picklist'){ //2015-05-06 young 判断下拉
            $(".chzn-select").chosen();
            // $('.chzn-container').css({'margin-top':'-6px'});
            //}
            if(fieldType == 'datetime'||fieldType == 'date'){
                //console.log(1111);
                $('#'+searchParamsPreFix+'_value'+index).replaceWith('<input class="span9 dateField form_datetime" id="TyunReportQuery_value'+index+'" name="TyunReportQuery[value'+index+']" size="16" type="text" name="nowritetime" value=""  >');
                $(".form_datetime").datepicker({format: 'yyyy-mm-dd',autoclose:true,language:  'zh-CN'});
            }

            //搜索是在这里在这里完成完成自动联想功能的。
            /!*if(fieldType == 'reference'||fieldType == 'product')
            {
                $("#"+searchParamsPreFix+"_value"+index+"").autocomplete({
                    source: "index.php?module=Home&src_module="+app.getModuleName()+"&field="+$fieldName+"&action=BasicAjax&mode=getList&record=",
                    minLength: 2,
                    select: function( event, ui ) {
                        console.log( ui.item ?
                        "Selected: " + ui.item.value + " aka " + ui.item.id :
                        "Nothing selected, input was " + this.value );
                    }
                });;
            }*!/


            if(true == isKeepOldValue)
            {
                $('#'+searchParamsPreFix+'_value'+index).attr('value',$oldFieldValue);
            }

        }*/
        function updateQueryRow(index,isKeepOldValue)
        {
            //updateQueryOperator(index);
            updateQueryValue(index);
        }
        $(function(){
            //markQueryTitle();
            //searchConditionRowNum = 1;
            addSearchField(0);
            //updateQueryRow(searchConditionRowNum,true);
        })
    </script>{/literal}
{/strip}