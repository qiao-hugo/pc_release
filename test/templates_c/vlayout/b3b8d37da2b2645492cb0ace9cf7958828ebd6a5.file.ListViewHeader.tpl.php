<?php /* Smarty version Smarty-3.1.7, created on 2022-02-16 10:36:44
         compiled from "D:\phpstudy_pro\WWW\pc_release\includes\runtime/../../layouts/vlayout\modules\WorkFlowCheck\ListViewHeader.tpl" */ ?>
<?php /*%%SmartyHeaderCode:24746620c633c7b7ca9-55633028%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b3b8d37da2b2645492cb0ace9cf7958828ebd6a5' => 
    array (
      0 => 'D:\\phpstudy_pro\\WWW\\pc_release\\includes\\runtime/../../layouts/vlayout\\modules\\WorkFlowCheck\\ListViewHeader.tpl',
      1 => 1566458164,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '24746620c633c7b7ca9-55633028',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'MODULE' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_620c633c81b28',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_620c633c81b28')) {function content_620c633c81b28($_smarty_tpl) {?>
<div class="listViewPageDiv"><div class="listViewTopMenuDiv noprint hide"></div><div> <form method="post"  name="SearchBug" id="SearchBug"><input type="hidden" value="1" id="queryaction" name="queryaction"><input type="hidden" value="" id="queryTitle" name="queryTitle"><input type="hidden" value="0" id="saveQuery" name="saveQuery"><input type="hidden" value="0" id="reset" name="reset"><input type="hidden" value="" id="showField" name="showField"><div id="SearchBlankCover" style="background-color: #F0F0F0;"><table id="searchtable" style="margin:auto"><tbody><tr class="SearchConditionRow" id="SearchConditionRow0" style="height:22px;"><td><input type="hidden" value="" name="BugFreeQuery[leftParenthesesName0]"id="BugFreeQuery_leftParenthesesName0"></td><td><select id="BugFreeQuery_field0" style="width:100%;color:#878787;" name="BugFreeQuery[field0]"><option value="department" selected="selected">编号</option></select></td><td><select id="BugFreeQuery_operator0" style="width:100%;color:#878787;"name="BugFreeQuery[operator0]"><option value="LIKE" selected="selected">包含</option></select></td><td><input type="text" value="" id="DepartFilter" name="salesorder_nono"></td><td><input type="hidden" value="" name="BugFreeQuery[rightParenthesesName0]"id="BugFreeQuery_rightParenthesesName0"></td><td><select id="BugFreeQuery_andor0" style="width:65px;color:#878787;" name="BugFreeQuery[andor0]"><option value="And" selected="selected">并且</option></select></td><td><a class="add_search_button" href="javascript:addSearchField(0);"><img src="layouts/vlayout/skins/softed/images/add_search.gif"></a></td></tr><tr><td colspan="7"><center><input type="button"value="提交查询" id="PostQuery" name="PostQuery" class="btn"><input type="button" onclick="setSearchConditionOrder();$('#save_query_dialog').dialog('open'); return false;"value="保存查询" id="SaveQuery" name="SaveQuery" class="btn hide"><input type="button" onclick="location.reload();"value="重置查询" class="btn"></center></td></tr></tbody></table><?php echo $_smarty_tpl->getSubTemplate (vtemplate_path('SearchJS.tpl'), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('MODULE'=>$_smarty_tpl->tpl_vars['MODULE']->value), 0);?>
</form></div><?php echo $_smarty_tpl->getSubTemplate (vtemplate_path('DefaultListFields.tpl'), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('MODULE'=>$_smarty_tpl->tpl_vars['MODULE']->value,'ISDEFAULT'=>true,'ISPAGE'=>true), 0);?>
<div class="listViewContentDiv" id="listViewContents"><?php }} ?>