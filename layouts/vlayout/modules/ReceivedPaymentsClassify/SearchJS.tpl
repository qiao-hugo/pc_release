{strip}

    <input type="hidden" value="" name="BugFreeQuery[queryRowOrder]" id="BugFreeQuery_QueryRowOrder">
    <script type="text/javascript">
        var limitedFieldCount = 8;
        var templateFieldNumber = '999999';
        var searchParamsPreFix = 'BugFreeQuery';
        var infoType = 'bug';
        var dateFormatError = '请输入正确的日期格式。例如，2009-10-8 或 -7。';
        var field_department_type='string';

        {foreach key=FIELD_NAME item=FIELD_MODEL from=$SEARCHRECORD_STRUCTURE}
        {assign var=FIELD_INFO value=$FIELD_MODEL->getFieldInfo()}
        {assign var=MODULE_MODEL value=$FIELD_MODEL->getModule()}
        var field_{$FIELD_NAME|trim}_type='{$FIELD_MODEL->getFieldDataType()}';
        {if  $FIELD_MODEL->getFieldDataType() eq 'picklist' }
        {if in_array($FIELD_NAME, ['first_collate_status', 'last_collate_status'])}
        var field_{$FIELD_NAME|trim}_value = '<select id=\"BugFreeQuery_value999999\" style=\"width:100%\" class=\"chzn-select\" name=\"BugFreeQuery[value999999]\"><option value=\"\">选择一个选项</option><option value="fit">符合</option><option value="unfit">不符合</option></select>';
        {elseif in_array($FIELD_NAME, ['systemclassfication', 'artificialclassfication'])}
        var field_{$FIELD_NAME|trim}_value = '<select id=\"BugFreeQuery_value999999\" style=\"width:100%\" class=\"chzn-select\" name=\"BugFreeQuery[value999999]\"><option value=\"\">选择一个选项</option><option value="0">空</option>{foreach $classficationList as $pkey=>$classfication}<optgroup label="{$pkey}">{foreach $classfication as $key=>$value}<option value="{$key}">{$value}</option>{/foreach}</optgroup>{/foreach}</select>';
        {else}
        var field_{$FIELD_NAME|trim}_value = '<select id=\"BugFreeQuery_value999999\" style=\"width:100%\" class=\"chzn-select\" name=\"BugFreeQuery[value999999]\"><option value=\"\">选择一个选项</option>{foreach key=PICKLIST item=pick_list from=$FIELD_INFO['picklistvalues']}<option value=\"{$PICKLIST}\">{$pick_list}</option>{/foreach}</select>';
        {/if}
        {elseif $FIELD_MODEL->getFieldDataType() eq 'owner'}
        {assign var=USERSLIST value=$FIELD_INFO['picklistvalues']['用户']}
        var field_{$FIELD_NAME|trim}_value = '<select id=\"BugFreeQuery_value999999\" style=\"width:100%;\" class=\"chzn-select\" name=\"BugFreeQuery[value999999]\"><option value=\"\">选择一个选项</option>{foreach key=PICKLIST item=pick_list from=$USERSLIST}<option value=\"{$PICKLIST}\">{$pick_list}</option>{/foreach}</select>';
        {elseif $FIELD_MODEL->getFieldDataType() eq 'boolean'}
        var field_{$FIELD_NAME|trim}_value = '<select id=\"BugFreeQuery_value999999\" style=\"width:100%;\" class=\"chzn-select\" name=\"BugFreeQuery[value999999]\"><option value=\"\">选择一个选项</option><option value=\"0\">否</option><option value=\"1\">是</option></select>';
        {else}
        var field_{$FIELD_NAME|trim}_value = '<input type=\"text\"  size=\"16\" value=\"\" id=\"BugFreeQuery_value999999\" name=\"BugFreeQuery[value999999]\" autocomplete=\"off\">';
        {/if}
        {/foreach}

        var field_integer_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"=\"   selected=\"selected\">等于</option><option value=\"&gt;=\" >大于等于</option><option value=\"&lt;=\">小于等于</option><option value=\"IN\">包含</option></select>';
        var field_datetime_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"LIKE\"  selected=\"selected\">包含</option><option value=\"&gt;=\"   selected=\"selected\">大于等于</option><option value=\"&lt;=\">小于等于</option></select>';
        var field_date_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"LIKE\"  selected=\"selected\">包含</option><option value=\"&gt;=\"   selected=\"selected\">大于等于</option><option value=\"&lt;=\">小于等于</option></select>';

        var field_string_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"LIKE\"  selected=\"selected\">包含</option></select>';
        var field_owner_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"=\" selected=\"selected\">等于</option></select>';
        var field_reference_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"LIKE\"  selected=\"selected\">包含</option><option value=\"=\">等于</option></select>';
        var field_mulreference_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"=\" selected=\"selected\">包含</option></select>';
        var field_picklist_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"=\"   selected=\"selected\">等于</option></select>';

        var field_boolean_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"=\"   selected=\"selected\">等于</option></select>';
        var field_currency_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"=\"   selected=\"selected\">等于</option><option value=\"&gt;=\">大于等于</option><option value=\"&lt;=\">小于等于</option></select>';

        var field_Area_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"LIKE\"  selected=\"selected\">包含</option></select>';
        var field_email_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"LIKE\"  selected=\"selected\">包含</option><option value=\"=\">等于</option></select>';
        var field_url_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"LIKE\"  selected=\"selected\">包含</option></select>';
        var field_userDepartment_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"LIKE\"  selected=\"selected\">包含</option></select>';
        var field_FileUpload_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"LIKE\"  selected=\"selected\">包含</option></select>';
        var field_phone_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"LIKE\"  selected=\"selected\">包含</option></select>';
        var field_other_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"LIKE\"  selected=\"selected\">包含</option></select>';
        var field_product_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"LIKE\"  selected=\"selected\">包含</option></select>';
        var field_text_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"LIKE\"  selected=\"selected\">包含</option></select>';
        var field_negativenumber_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"=\"   selected=\"selected\">等于</option><option value=\"&gt;=\">大于等于</option><option value=\"&lt;=\">小于等于</option></select>';

        var searchConditionTmp = '<tr class=\"SearchConditionRow\" id=\"SearchConditionRow999999\"><td><select id=\"BugFreeQuery_leftParenthesesName999999\" onchange=\"validateParentheses()\" style=\"width:48px;\" name=\"BugFreeQuery[leftParenthesesName999999]\"><option value=\"\" selected=\"selected\"></option><option value=\"(\">(</option></select></td><td><select id=\"BugFreeQuery_field999999\" onchange=\"updateQueryRow(999999);\" name=\"BugFreeQuery[field999999]\">{foreach key=FIELD_NAME item=FIELD_MODEL from=$SEARCHRECORD_STRUCTURE}<option value=\"{$FIELD_MODEL->get('column')}##{$FIELD_MODEL->get('uitype')}##{$FIELD_MODEL->get('id')}##{$FIELD_MODEL->getFieldDataType()}\" data=\"{$FIELD_NAME}\">{vtranslate($FIELD_MODEL->get('label'), $SOURCE_MODULE)}</option>{/foreach}</select></td><td><select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"LIKE\"   selected=\"selected\">包含</option></select></td><td><input size=\"16\" id=\"BugFreeQuery_value999999\" type=\"text\" value=\"\" name=\"BugFreeQuery[value999999]\" autocomplete=\"off\" /></td><td><select id=\"BugFreeQuery_rightParenthesesName999999\" onchange=\"validateParentheses()\" style=\"width:48px;\" name=\"BugFreeQuery[rightParenthesesName999999]\"><option value=\"\" selected=\"selected\"></option><option value=\")\">)</option></select></td><td><select id=\"BugFreeQuery_andor999999\" style=\"width:65px;\" name=\"BugFreeQuery[andor999999]\"><option value=\"And\">并且</option><option value=\"Or\">或者</option></select></td><td><a class=\"add_search_button\"  href=\"javascript:addSearchField(999999);\"><img src=\"layouts/vlayout/skins/softed/images/add_search.gif\"/></a>&nbsp;&nbsp;<a class=\"cancel_search_button\"  href=\"javascript:removeSearchField(999999);\"><img src=\"layouts/vlayout/skins/softed/images/cancel_search.gif\"/></a></td></tr>';
    </script>
{literal}
    <script type="text/javascript">
        function addSearchField(fieldRowNum)
        {
            var $newSearchRow = replaceTemplateWithIndex(searchConditionTmp,searchConditionRowNum);

            if(fieldRowNum==0){
				<!--修改自定义的搜索框排序在最后一个的tr之前插入-->
                $("#searchtable tbody tr:last").before($($newSearchRow));
                $('#BugFreeQuery_field0').chosen();
                //$('#'+searchParamsPreFix+'_leftParenthesesName'+fieldRowNum).attr('type')='hidden';
            }else{
                $("#SearchConditionRow"+fieldRowNum).after($($newSearchRow));
            }

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

            updateQueryRow(searchConditionRowNum,false);
            setSearchHeight();
            $('#BugFreeQuery_field'+searchConditionRowNum).chosen();
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
        function updateQueryOperator(index,isKeepOldValue)
        {
            var $oldOperatorValue = $('#'+searchParamsPreFix+'_operator'+index).val();
            var $fieldName = $('#'+searchParamsPreFix+'_field'+index).find("option:selected").attr('data');
            eval('var fieldType='+'field_'+$fieldName+'_type');
            var $fieldName = $('#'+searchParamsPreFix+'_field'+index).find("option:selected").attr('data');

            eval('var fieldOperatorSelect='+'field_'+fieldType+'_operator');

            var $operatorValue = replaceTemplateWithIndex(fieldOperatorSelect,index);
            $('#'+searchParamsPreFix+'_operator'+index).replaceWith($operatorValue);

            if(true == isKeepOldValue)
            {
                $('#'+searchParamsPreFix+'_operator'+index).attr('value',$oldOperatorValue);
            }
        }
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
        function updateQueryValue(index,isKeepOldValue)
        {
            var $fieldName = $('#'+searchParamsPreFix+'_field'+index).find("option:selected").attr('data');
            var $operatorValue = $('#'+searchParamsPreFix+'_operator'+index).val();
            var $oldFieldValue = $('#'+searchParamsPreFix+'_value'+index).val();

            if($("#BugFreeQuery_value"+index+'_chzn').length){
                $("#BugFreeQuery_value"+index+'_chzn').remove();
            }
            if(('severity' == $fieldName || 'priority' == $fieldName) && 'IN' == $operatorValue)
            {
                $('#'+searchParamsPreFix+'_value'+index).replaceWith('<input type="text" size="16" value="" id="BugFreeQuery_value'+index+'" name="BugFreeQuery[value'+index+']">');
                if(true == isKeepOldValue)
                {
                    $('#'+searchParamsPreFix+'_value'+index).attr('value',$oldFieldValue);
                }
                return;
            }

            eval('var fieldValueSelect='+'field_'+$fieldName+'_value;');
            var $newValue = replaceTemplateWithIndex(fieldValueSelect,index);
            $('#'+searchParamsPreFix+'_value'+index).replaceWith($newValue);
            eval('var fieldType='+'field_'+$fieldName+'_type');
            $(".chzn-select").chosen();
            if(fieldType == 'datetime'||fieldType == 'date'){

                $('#'+searchParamsPreFix+'_value'+index).replaceWith('<input class="span9 dateField form_datetime" id="BugFreeQuery_value'+index+'" name="BugFreeQuery[value'+index+']" autocomplete="off" size="16" type="text" name="nowritetime" value=""  >');
                $(".form_datetime").datepicker({format: 'yyyy-mm-dd',autoclose:true,language:  'zh-CN',todayHighlight:true});
            }

            //搜索是在这里在这里完成完成自动联想功能的。
            /*if(fieldType == 'reference'||fieldType == 'product')
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
            }*/

            if(true == isKeepOldValue)
            {
                $('#'+searchParamsPreFix+'_value'+index).attr('value',$oldFieldValue);
            }
        }
        function updateQueryRow(index,isKeepOldValue)
        {
            updateQueryOperator(index,isKeepOldValue);
            updateQueryValue(index,isKeepOldValue);
        }
        $(function(){
            searchConditionRowNum = 1;
            addSearchField(0);
        })
    </script>{/literal}
{/strip}
