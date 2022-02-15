{*<!--
/*********************************************************************************
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
* {foreach key=FIELD_NAME item=FIELD_MODEL from=$SEARCHRECORD_STRUCTURE}
     {if $FIELD_MODEL->getFieldDataType() eq 'owner'}
     	{var_dump($FIELD_MODEL->getFieldInfo())}
     {/if}
    {/foreach}
    
     var searchConditionTmp = '<tr class=\"SearchConditionRow\" id=\"SearchConditionRow999999\"><td><select id=\"BugFreeQuery_field999999\" onchange=\"updateQueryRow(999999);\" name=\"BugFreeQuery[field999999]\">{foreach key=FIELD_NAME item=FIELD_MODEL from=$SEARCHRECORD_STRUCTURE}<option value=\"{$FIELD_MODEL->get('column')}##{$FIELD_MODEL->get('uitype')}##{$FIELD_MODEL->get('id')}##{$FIELD_MODEL->getFieldDataType()}\" data=\"{$FIELD_NAME}\">{vtranslate($FIELD_MODEL->get('label'), $SOURCE_MODULE)}</option>{/foreach}</select></td><td><select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"LIKE\"   selected=\"selected\">包含</option></select></td><td><input size=\"16\" id=\"BugFreeQuery_value999999\" type=\"text\" value=\"\" name=\"BugFreeQuery[value999999]\" /></td><td><a class=\"add_search_button\"  href=\"javascript:addSearchField(999999);\"><img src=\"layouts/vlayout/skins/softed/images/add_search.gif\"/></a>&nbsp;&nbsp;<a class=\"cancel_search_button\"  href=\"javascript:removeSearchField(999999);\"><img src=\"layouts/vlayout/skins/softed/images/cancel_search.gif\"/></a></td></tr>';
********************************************************************************/
-->*}
{strip}
    <input type="hidden" value="" name="BugFreeQuery[queryRowOrder]" id="BugFreeQuery_QueryRowOrder">
    <script type="text/javascript">
        var limitedFieldCount = 8;
        var templateFieldNumber = '999999';
        var searchParamsPreFix = 'BugFreeQuery';
        var infoType = 'bug';
        var dateFormatError = '请输入正确的日期格式。例如，2009-10-8 或 -7。';


        {foreach key=FIELD_NAME item=FIELD_MODEL from=$SEARCHRECORD_STRUCTURE}
        {assign var=FIELD_INFO value=$FIELD_MODEL->getFieldInfo()}
        {assign var=MODULE_MODEL value=$FIELD_MODEL->getModule()}
        var field_{$FIELD_NAME|trim}_type= '{$FIELD_MODEL->getFieldDataType()}';
        {if  $FIELD_MODEL->getFieldDataType() eq 'picklist' }

        var field_{$FIELD_NAME|trim}_value = '<select id=\"BugFreeQuery_value999999\" style=\"width:100%\" class=\"chzn-select\" name=\"BugFreeQuery[value999999]\"><option value=\"\">选择一个选项</option>{foreach key=PICKLIST item=pick_list from=$FIELD_INFO['picklistvalues']}<option value=\"{$PICKLIST}\">{$pick_list}</option>{/foreach}</select>';
        {elseif $FIELD_MODEL->getFieldDataType() eq 'owner'}
        {assign var=USERSLIST value=$FIELD_INFO['picklistvalues']['label']}
        var field_{$FIELD_NAME|trim}_value = '<select id=\"BugFreeQuery_value999999\" style=\"width:100%;\" class=\"chzn-select\" name=\"BugFreeQuery[value999999]\"><option value=\"\">选择一个选项</option>{foreach key=PICKLIST item=pick_list from=$USERSLIST}<option value=\"{$PICKLIST}\">{$pick_list}</option>{/foreach}</select>';
        {elseif $FIELD_MODEL->getFieldDataType() eq 'boolean'}
        var field_{$FIELD_NAME|trim}_value = '<select id=\"BugFreeQuery_value999999\" style=\"width:100%;\" class=\"chzn-select\" name=\"BugFreeQuery[value999999]\"><option value=\"\">选择一个选项</option><option value=\"0\">否</option><option value=\"1\">是</option></select>';
        {else}
        var field_{$FIELD_NAME|trim}_value = '<input type=\"text\"  size=\"16\" value=\"\" id=\"BugFreeQuery_value999999\" name=\"BugFreeQuery[value999999]\">';
        {/if}

        {/foreach}

        	var field_integer_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"EQ\"   selected=\"selected\">等于</option><option value=\"GTQ\" >大于等于</option><option value=\"&lt;=\">小于等于</option><option value=\"IN\">包含</option></select>';
            var field_datetime_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"LIKE\"  selected=\"selected\">包含</option><option value=\"GTQ\"   selected=\"selected\">大于等于</option><option value=\"&lt;=\">小于等于</option></select>';
            var field_date_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"LIKE\"  selected=\"selected\">包含</option><option value=\"GTQ\"   selected=\"selected\">大于等于</option><option value=\"&lt;=\">小于等于</option></select>';

            var field_string_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"LIKE\"  selected=\"selected\">包含</option></select>';
            var field_owner_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"EQ\" selected=\"selected\">等于</option></select>';
            var field_reference_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"LIKE\"  selected=\"selected\">包含</option><option value=\"EQ\">等于</option></select>';
            var field_mulreference_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"=\" selected=\"selected\">包含</option></select>';
            var field_picklist_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"EQ\"   selected=\"selected\">等于</option></select>';

            var field_boolean_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"EQ\"   selected=\"selected\">等于</option></select>';
            var field_currency_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"GTQ\" selected=\"selected\">大于等于</option><option value=\"&lt;=\">小于等于</option></select>';

            var field_Area_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"LIKE\"  selected=\"selected\">包含</option></select>';
            var field_email_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"LIKE\"  selected=\"selected\">包含</option><option value=\"EQ\">等于</option></select>';
            var field_url_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"LIKE\"  selected=\"selected\">包含</option></select>';
            var field_userDepartment_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"LIKE\"  selected=\"selected\">包含</option></select>';
            var field_FileUpload_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"LIKE\"  selected=\"selected\">包含</option></select>';
            var field_phone_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"LIKE\"  selected=\"selected\">包含</option></select>';
            var field_other_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"LIKE\"  selected=\"selected\">包含</option></select>';
            var field_product_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"LIKE\"  selected=\"selected\">包含</option></select>';
            var field_text_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"LIKE\"  selected=\"selected\">包含</option></select>';

        var searchConditionTmp = '<tr class=\"SearchConditionRow\" id=\"SearchConditionRow999999\"><td><select id=\"BugFreeQuery_field999999\" onchange=\"updateQueryRow(999999);\" name=\"BugFreeQuery[field999999]\">{foreach key=FIELD_NAME item=FIELD_MODEL from=$SEARCHRECORD_STRUCTURE}<option value=\"{$FIELD_MODEL->get('column')}##{$FIELD_MODEL->get('uitype')}##{$FIELD_MODEL->get('id')}##{$FIELD_MODEL->getFieldDataType()}\" data=\"{$FIELD_NAME}\">{vtranslate($FIELD_MODEL->get('label'), $SOURCE_MODULE)}</option>{/foreach}</select></td><td><select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"LIKE\"   selected=\"selected\">包含</option></select></td><td><input size=\"16\" id=\"BugFreeQuery_value999999\" type=\"text\" value=\"\" name=\"BugFreeQuery[value999999]\" /></td><td><a class=\"add_search_button\"  href=\"javascript:addSearchField(999999);\"><img src=\"layouts/vlayout/skins/softed/images/add_search.gif\"/></a>&nbsp;&nbsp;<a class=\"cancel_search_button\"  href=\"javascript:removeSearchField(999999);\"><img src=\"layouts/vlayout/skins/softed/images/cancel_search.gif\"/></a></td></tr>';
    </script>
{literal}
    <script type="text/javascript">
   		//添加并行条件
        function addSearchField(fieldRowNum){
            var $newSearchRow = replaceTemplateWithIndex(searchConditionTmp,searchConditionRowNum);
            if(fieldRowNum==0){
				<!--wangbin修改自定义的搜索框排序在最后一个的tr之前插入-->
                $("#searchtable tbody").html($($newSearchRow));
            }else{
                //$("#SearchConditionRow"+fieldRowNum).after($($newSearchRow));
                //wangbin 添加
                var left_key = $("#BugFreeQuery_field1").val();
                var left_value = $("#BugFreeQuery_field1 option:selected").text();

                var middle_key = $("#BugFreeQuery_operator1").val();
                var middle_value = $("#BugFreeQuery_operator1 option:selected").text();
				
               var right_key = $("#BugFreeQuery_value1").val();
               var right_value = "";
               if($("#BugFreeQuery_value1")[0].tagName == "SELECT"){
            	   var right_value = $("#BugFreeQuery_value1 option:selected").text();
               }else{
            	   right_value = right_key;
               }
              // var sjson = '{'+left_key+':"'+left_value+'",'+middle_value+':"'+middle_value+'",'+right_key+':"'+right_value+'"}';
              var $i = $("#searchtable tr").length-1;
              var sjson = '{"'+left_key+'":"'+left_value+'","'+middle_value+'":"'+middle_value+'","'+right_key+'":"'+right_value+'"}'; 
             // console.log(sjson);
               var afterstr = '<tr><td><input type="hidden" name="submitcondition['+$i+']" value=\''+sjson+'\'>'+left_value+'</td><td>'+middle_value+'</td><td>'+right_value+'</td><td><a class="removetr" href="script:void(0)"><img src="layouts/vlayout/skins/softed/images/cancel_search.gif"/></a></td></tr>';
                $("#SearchConditionRow"+fieldRowNum).after(afterstr);
            }

            var $rowNum = $(".SearchConditionRow").length;
            if($rowNum >=limitedFieldCount){$('.add_search_button').hide();}
            $('.cancel_search_button').hide();
            //if($rowNum >1 ){$('.cancel_search_button').show();}
            //if($rowNum <=1 ){$('.cancel_search_button').hide();}

            updateQueryRow(searchConditionRowNum,false);
            setSearchHeight();
            //searchConditionRowNum++;  去掉添加动态的下拉框这个方法报错了,先注释一下啊 应该不需要了;
        }
   		
   		//wangbin wangbin 删除当前tr
   		$('body').on('click','.removetr',function(){$(this).closest('tr').remove()});

   		function removetr(){

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
            /* 先隐藏掉，放开产品负责人之后打开
            if(app.getModuleName()=='ServiceMaintenance'&&$fieldName=='ownerid'){
                fieldType='mulreference';
            }*/

            eval('var fieldOperatorSelect='+'field_'+fieldType+'_operator');

            var $operatorValue = replaceTemplateWithIndex(fieldOperatorSelect,index);
            $('#'+searchParamsPreFix+'_operator'+index).replaceWith($operatorValue);

            if(true == isKeepOldValue)
            {
                $('#'+searchParamsPreFix+'_operator'+index).attr('value',$oldOperatorValue);
            }
        }
        function removeSearchField(fieldRowNum){}
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
            $('.chzn-container').css({'margin-top':'-6px'});
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
            if($("#SearchConditionRow"+index+' .chzn-container').length) {
                $("#SearchConditionRow"+index+' .chzn-container').remove();
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

            //if(fieldType == 'owner'||fieldType == 'picklist'){ //2015-05-06 young 判断下拉
               $(".chzn-select").chosen();
                $('.chzn-container').css({'margin-top':'-6px'});
            //}
            if(fieldType == 'datetime'||fieldType == 'date'){
                //console.log(1111);
                $('#'+searchParamsPreFix+'_value'+index).replaceWith('<input class="span9 dateField form_datetime" id="BugFreeQuery_value'+index+'" name="BugFreeQuery[value'+index+']" size="16" type="text" name="nowritetime" value=""  >');
                $(".form_datetime").datepicker({format: 'yyyy-mm-dd',autoclose:true,language:  'zh-CN'});
            }

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
