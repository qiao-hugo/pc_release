<?php /* Smarty version Smarty-3.1.7, created on 2022-02-15 10:21:51
         compiled from "D:\phpstudy_pro\WWW\pc_release\includes\runtime/../../layouts/vlayout\modules\Vtiger\SearchJS.tpl" */ ?>
<?php /*%%SmartyHeaderCode:14919620b0e3f2573a2-30402501%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'fd59874c70ced663b0de59d4fb149da20a25ea5f' => 
    array (
      0 => 'D:\\phpstudy_pro\\WWW\\pc_release\\includes\\runtime/../../layouts/vlayout\\modules\\Vtiger\\SearchJS.tpl',
      1 => 1631190584,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '14919620b0e3f2573a2-30402501',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'SEARCHRECORD_STRUCTURE' => 0,
    'FIELD_MODEL' => 0,
    'FIELD_NAME' => 0,
    'FIELD_INFO' => 0,
    'PICKLIST' => 0,
    'pick_list' => 0,
    'USERSLIST' => 0,
    'SOURCE_MODULE' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_620b0e3f2c1d0',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_620b0e3f2c1d0')) {function content_620b0e3f2c1d0($_smarty_tpl) {?>
<input type="hidden" value="" name="BugFreeQuery[queryRowOrder]" id="BugFreeQuery_QueryRowOrder"><script type="text/javascript">var limitedFieldCount = 8;var templateFieldNumber = '999999';var searchParamsPreFix = 'BugFreeQuery';var infoType = 'bug';var dateFormatError = '请输入正确的日期格式。例如，2009-10-8 或 -7。';var field_department_type='string';var isUserDepartment = false;<?php  $_smarty_tpl->tpl_vars['FIELD_MODEL'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['FIELD_MODEL']->_loop = false;
 $_smarty_tpl->tpl_vars['FIELD_NAME'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['SEARCHRECORD_STRUCTURE']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['FIELD_MODEL']->key => $_smarty_tpl->tpl_vars['FIELD_MODEL']->value){
$_smarty_tpl->tpl_vars['FIELD_MODEL']->_loop = true;
 $_smarty_tpl->tpl_vars['FIELD_NAME']->value = $_smarty_tpl->tpl_vars['FIELD_MODEL']->key;
?><?php $_smarty_tpl->tpl_vars['FIELD_INFO'] = new Smarty_variable($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getFieldInfo(), null, 0);?><?php $_smarty_tpl->tpl_vars['MODULE_MODEL'] = new Smarty_variable($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getModule(), null, 0);?>var field_<?php echo trim($_smarty_tpl->tpl_vars['FIELD_NAME']->value);?>
_type='<?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getFieldDataType();?>
';<?php if ($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getFieldDataType()=='picklist'){?>var field_<?php echo trim($_smarty_tpl->tpl_vars['FIELD_NAME']->value);?>
_value = '<select id=\"BugFreeQuery_value999999\" style=\"width:100%\" class=\"chzn-select\" name=\"BugFreeQuery[value999999]\"><option value=\"\">选择一个选项</option><?php  $_smarty_tpl->tpl_vars['pick_list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['pick_list']->_loop = false;
 $_smarty_tpl->tpl_vars['PICKLIST'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['FIELD_INFO']->value['picklistvalues']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['pick_list']->key => $_smarty_tpl->tpl_vars['pick_list']->value){
$_smarty_tpl->tpl_vars['pick_list']->_loop = true;
 $_smarty_tpl->tpl_vars['PICKLIST']->value = $_smarty_tpl->tpl_vars['pick_list']->key;
?><option value=\"<?php echo $_smarty_tpl->tpl_vars['PICKLIST']->value;?>
\"><?php echo $_smarty_tpl->tpl_vars['pick_list']->value;?>
</option><?php } ?></select>';<?php }elseif($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getFieldDataType()=='owner'){?><?php $_smarty_tpl->tpl_vars['USERSLIST'] = new Smarty_variable($_smarty_tpl->tpl_vars['FIELD_INFO']->value['picklistvalues']['用户'], null, 0);?>var field_<?php echo trim($_smarty_tpl->tpl_vars['FIELD_NAME']->value);?>
_value = '<select id=\"BugFreeQuery_value999999\" style=\"width:100%;\" class=\"chzn-select\" name=\"BugFreeQuery[value999999]\"><option value=\"\">选择一个选项</option><?php  $_smarty_tpl->tpl_vars['pick_list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['pick_list']->_loop = false;
 $_smarty_tpl->tpl_vars['PICKLIST'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['USERSLIST']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['pick_list']->key => $_smarty_tpl->tpl_vars['pick_list']->value){
$_smarty_tpl->tpl_vars['pick_list']->_loop = true;
 $_smarty_tpl->tpl_vars['PICKLIST']->value = $_smarty_tpl->tpl_vars['pick_list']->key;
?><option value=\"<?php echo $_smarty_tpl->tpl_vars['PICKLIST']->value;?>
\"><?php echo $_smarty_tpl->tpl_vars['pick_list']->value;?>
</option><?php } ?></select>';<?php }elseif($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getFieldDataType()=='boolean'){?>var field_<?php echo trim($_smarty_tpl->tpl_vars['FIELD_NAME']->value);?>
_value = '<select id=\"BugFreeQuery_value999999\" style=\"width:100%;\" class=\"chzn-select\" name=\"BugFreeQuery[value999999]\"><option value=\"\">选择一个选项</option><option value=\"0\">否</option><option value=\"1\">是</option></select>';<?php }elseif(($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getFieldDataType()=='userDepartment')&&($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('name')=='authordepartment')){?>isUserDepartment = true;var field_<?php echo trim($_smarty_tpl->tpl_vars['FIELD_NAME']->value);?>
_value = '<select id=\"BugFreeQuery_value999999\" style=\"width:100%\" class=\"chzn-select\" name=\"BugFreeQuery[value999999]\"><option value=\"\">选择一个选项</option><?php  $_smarty_tpl->tpl_vars['pick_list'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['pick_list']->_loop = false;
 $_smarty_tpl->tpl_vars['PICKLIST'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['FIELD_INFO']->value['picklistvalues']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['pick_list']->key => $_smarty_tpl->tpl_vars['pick_list']->value){
$_smarty_tpl->tpl_vars['pick_list']->_loop = true;
 $_smarty_tpl->tpl_vars['PICKLIST']->value = $_smarty_tpl->tpl_vars['pick_list']->key;
?><option value=\"<?php echo $_smarty_tpl->tpl_vars['PICKLIST']->value;?>
\"><?php echo $_smarty_tpl->tpl_vars['pick_list']->value;?>
</option><?php } ?></select>';<?php }else{ ?>var field_<?php echo trim($_smarty_tpl->tpl_vars['FIELD_NAME']->value);?>
_value = '<input type=\"text\"  size=\"16\" value=\"\" id=\"BugFreeQuery_value999999\" name=\"BugFreeQuery[value999999]\" autocomplete=\"off\">';<?php }?><?php } ?>var field_integer_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"=\"   selected=\"selected\">等于</option><option value=\"&gt;=\" >大于等于</option><option value=\"&lt;=\">小于等于</option><option value=\"IN\">包含</option></select>';var field_datetime_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"LIKE\"  selected=\"selected\">包含</option><option value=\"&gt;=\"   selected=\"selected\">大于等于</option><option value=\"&lt;=\">小于等于</option></select>';var field_date_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"LIKE\"  selected=\"selected\">包含</option><option value=\"&gt;=\"   selected=\"selected\">大于等于</option><option value=\"&lt;=\">小于等于</option></select>';var field_string_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"LIKE\"  selected=\"selected\">包含</option></select>';var field_owner_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"=\" selected=\"selected\">等于</option></select>';var field_reference_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"LIKE\"  selected=\"selected\">包含</option><option value=\"=\">等于</option></select>';var field_mulreference_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"=\" selected=\"selected\">包含</option></select>';var field_picklist_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"=\"   selected=\"selected\">等于</option></select>';var field_boolean_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"=\"   selected=\"selected\">等于</option></select>';var field_currency_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"=\"   selected=\"selected\">等于</option><option value=\"&gt;=\">大于等于</option><option value=\"&lt;=\">小于等于</option></select>';var field_Area_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"LIKE\"  selected=\"selected\">包含</option></select>';var field_email_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"LIKE\"  selected=\"selected\">包含</option><option value=\"=\">等于</option></select>';var field_url_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"LIKE\"  selected=\"selected\">包含</option></select>';if(isUserDepartment){var field_userDepartment_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"=\"   selected=\"selected\">等于</option></select>';}else{var field_userDepartment_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"LIKE\"  selected=\"selected\">包含</option></select>';}var field_FileUpload_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"LIKE\"  selected=\"selected\">包含</option></select>';var field_phone_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"LIKE\"  selected=\"selected\">包含</option></select>';var field_other_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"LIKE\"  selected=\"selected\">包含</option></select>';var field_product_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"LIKE\"  selected=\"selected\">包含</option></select>';var field_text_operator = '<select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"LIKE\"  selected=\"selected\">包含</option></select>';var searchConditionTmp = '<tr class=\"SearchConditionRow\" id=\"SearchConditionRow999999\"><td><select id=\"BugFreeQuery_leftParenthesesName999999\" onchange=\"validateParentheses()\" style=\"width:48px;\" name=\"BugFreeQuery[leftParenthesesName999999]\"><option value=\"\" selected=\"selected\"></option><option value=\"(\">(</option></select></td><td><select id=\"BugFreeQuery_field999999\" onchange=\"updateQueryRow(999999);\" name=\"BugFreeQuery[field999999]\"><?php  $_smarty_tpl->tpl_vars['FIELD_MODEL'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['FIELD_MODEL']->_loop = false;
 $_smarty_tpl->tpl_vars['FIELD_NAME'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['SEARCHRECORD_STRUCTURE']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['FIELD_MODEL']->key => $_smarty_tpl->tpl_vars['FIELD_MODEL']->value){
$_smarty_tpl->tpl_vars['FIELD_MODEL']->_loop = true;
 $_smarty_tpl->tpl_vars['FIELD_NAME']->value = $_smarty_tpl->tpl_vars['FIELD_MODEL']->key;
?><option value=\"<?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('column');?>
##<?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('uitype');?>
##<?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('id');?>
##<?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getFieldDataType();?>
\" data=\"<?php echo $_smarty_tpl->tpl_vars['FIELD_NAME']->value;?>
\"><?php echo vtranslate($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('label'),$_smarty_tpl->tpl_vars['SOURCE_MODULE']->value);?>
</option><?php } ?></select></td><td><select id=\"BugFreeQuery_operator999999\" style=\"width:100%\" onchange=\"updateQueryValue(999999,true);\" name=\"BugFreeQuery[operator999999]\"><option value=\"LIKE\"   selected=\"selected\">包含</option></select></td><td><input size=\"16\" id=\"BugFreeQuery_value999999\" type=\"text\" value=\"\" name=\"BugFreeQuery[value999999]\" autocomplete=\"off\" /></td><td><select id=\"BugFreeQuery_rightParenthesesName999999\" onchange=\"validateParentheses()\" style=\"width:48px;\" name=\"BugFreeQuery[rightParenthesesName999999]\"><option value=\"\" selected=\"selected\"></option><option value=\")\">)</option></select></td><td><select id=\"BugFreeQuery_andor999999\" style=\"width:65px;\" name=\"BugFreeQuery[andor999999]\"><option value=\"And\">并且</option><option value=\"Or\">或者</option></select></td><td><a class=\"add_search_button\"  href=\"javascript:addSearchField(999999);\"><img src=\"layouts/vlayout/skins/softed/images/add_search.gif\"/></a>&nbsp;&nbsp;<a class=\"cancel_search_button\"  href=\"javascript:removeSearchField(999999);\"><img src=\"layouts/vlayout/skins/softed/images/cancel_search.gif\"/></a></td></tr>';</script>
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
        function updateQueryValue(index,isKeepOldValue)
        {
            var $fieldName = $('#'+searchParamsPreFix+'_field'+index).find("option:selected").attr('data');
            var $operatorValue = $('#'+searchParamsPreFix+'_operator'+index).val();
            var $oldFieldValue = $('#'+searchParamsPreFix+'_value'+index).val();
            /*if($("#SearchConditionRow"+index+' .chzn-container').length) {
                $("#SearchConditionRow"+index+' .chzn-container').remove();
            }*/
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

            //if(fieldType == 'owner'||fieldType == 'picklist'){ //2015-05-06 young 判断下拉
               $(".chzn-select").chosen();
               // $('.chzn-container').css({'margin-top':'-6px'});
            //}
            if(fieldType == 'datetime'||fieldType == 'date'){
                //console.log(1111);
                $('#'+searchParamsPreFix+'_value'+index).replaceWith('<input class="span9 dateField form_datetime" id="BugFreeQuery_value'+index+'" name="BugFreeQuery[value'+index+']" size="16" type="text" name="nowritetime" value=""  >');
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
            //markQueryTitle();
            searchConditionRowNum = 1;
            addSearchField(0);
            //updateQueryRow(searchConditionRowNum,true);

        })
    </script>
<?php }} ?>