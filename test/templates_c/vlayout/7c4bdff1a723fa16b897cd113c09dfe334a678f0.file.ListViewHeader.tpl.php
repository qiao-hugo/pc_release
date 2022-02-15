<?php /* Smarty version Smarty-3.1.7, created on 2022-02-15 16:39:08
         compiled from "D:\phpstudy_pro\WWW\pc_release\includes\runtime/../../layouts/vlayout\modules\ReceivedPayments\ListViewHeader.tpl" */ ?>
<?php /*%%SmartyHeaderCode:30128620b66ac35fbc6-08590278%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7c4bdff1a723fa16b897cd113c09dfe334a678f0' => 
    array (
      0 => 'D:\\phpstudy_pro\\WWW\\pc_release\\includes\\runtime/../../layouts/vlayout\\modules\\ReceivedPayments\\ListViewHeader.tpl',
      1 => 1631777271,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '30128620b66ac35fbc6-08590278',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'LISTVIEW_LINKS' => 0,
    'MODULE' => 0,
    'LISTVIEW_BASICACTION' => 0,
    'Department' => 0,
    'departmentid' => 0,
    'departname' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_620b66ac4de8c',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_620b66ac4de8c')) {function content_620b66ac4de8c($_smarty_tpl) {?>
<div class="listViewPageDiv"><div class="listViewTopMenuDiv noprint"><div class="listViewActionsDiv row-fluid"><span class="btn-toolbar span4"><span style="display:none;"><select id="customFilter" style="display:none;"></select></span><span class="<?php if (empty($_GET['public'])==false){?>hide<?php }?>"><?php  $_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['LISTVIEW_LINKS']->value['LISTVIEWBASIC']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->key => $_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value){
$_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->_loop = true;
?><span class="btn-group"><button id="<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
_listView_basicAction_<?php echo Vtiger_Util_Helper::replaceSpaceWithUnderScores($_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value->getLabel());?>
" class="btn addButton" <?php if (stripos($_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value->getUrl(),'javascript:')===0){?> onclick='<?php echo substr($_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value->getUrl(),strlen("javascript:"));?>
;'<?php }else{ ?> onclick='window.location.href="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value->getUrl();?>
"'<?php }?>><i class="icon-plus icon-white"></i>&nbsp;<strong><?php echo vtranslate($_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value->getLabel(),$_smarty_tpl->tpl_vars['MODULE']->value);?>
</strong></button></span><?php } ?></span></span><span class="btn-toolbar span4"><span class="customFilterMainSpan btn-group"></span></span></div></div><?php if (empty($_GET['public'])){?><div> <form method="post"  name="SearchBug" id="SearchBug"><input type="hidden" value="1" id="queryaction" name="queryaction"><input type="hidden" value="" id="queryTitle" name="queryTitle"><input type="hidden" value="0" id="saveQuery" name="saveQuery"><input type="hidden" value="0" id="reset" name="reset"><input type="hidden" value="" id="showField" name="showField"><div id="SearchBlankCover" style="background-color: #F0F0F0;"><table id="searchtable" style="margin:auto"><tbody><tr class="SearchConditionRow" id="SearchConditionRow0" style="height:22px;"><td><input type="hidden" value="" name="BugFreeQuery[leftParenthesesName0]"id="BugFreeQuery_leftParenthesesName0"></td><td><select id="BugFreeQuery_field0" style="width:100%;color:#878787;" name="BugFreeQuery[field0]"><option value="department" selected="selected">部门</option></select></td><td><select id="BugFreeQuery_operator0" style="width:100%;color:#878787;"name="BugFreeQuery[operator0]"><option value="UNDER" selected="selected">等于</option></select></td><td><?php $_smarty_tpl->tpl_vars['Department'] = new Smarty_variable(getDepartment(), null, 0);?><select id="DepartFilter" name="department"><?php  $_smarty_tpl->tpl_vars["departname"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["departname"]->_loop = false;
 $_smarty_tpl->tpl_vars["departmentid"] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['Department']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["departname"]->key => $_smarty_tpl->tpl_vars["departname"]->value){
$_smarty_tpl->tpl_vars["departname"]->_loop = true;
 $_smarty_tpl->tpl_vars["departmentid"]->value = $_smarty_tpl->tpl_vars["departname"]->key;
?><option value="<?php echo $_smarty_tpl->tpl_vars['departmentid']->value;?>
" ><?php echo $_smarty_tpl->tpl_vars['departname']->value;?>
</option><?php } ?></select></td><td><input type="hidden" value="" name="BugFreeQuery[rightParenthesesName0]"id="BugFreeQuery_rightParenthesesName0"></td><td><select id="BugFreeQuery_andor0" style="width:65px;color:#878787;" name="BugFreeQuery[andor0]"><option value="And" selected="selected">并且</option></select></td><td><a class="add_search_button" href="javascript:addSearchField(0);"><img src="layouts/vlayout/skins/softed/images/add_search.gif"></a></td></tr><tr><td colspan="7"><center><input type="button"value="提交查询" id="PostQuery" name="PostQuery" class="btn"><input type="button" onclick="setSearchConditionOrder();$('#save_query_dialog').dialog('open'); return false;"value="保存查询" id="SaveQuery" name="SaveQuery" class="btn hide"><input type="button" onclick="location.reload();"value="重置查询" class="btn"></center></td></tr></tbody></table><?php echo $_smarty_tpl->getSubTemplate (vtemplate_path('SearchJS.tpl','ReceivedPayments'), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('MODULE'=>$_smarty_tpl->tpl_vars['MODULE']->value), 0);?>
</form></div></div><?php echo $_smarty_tpl->getSubTemplate (vtemplate_path('DefaultListFields.tpl','ReceivedPayments'), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('MODULE'=>$_smarty_tpl->tpl_vars['MODULE']->value,'ISDEFAULT'=>true,'ISPAGE'=>true), 0);?>
<?php }else{ ?><?php }?><div class="listViewContentDiv" id="listViewContents"><?php }} ?>