<?php /* Smarty version Smarty-3.1.7, created on 2022-02-14 13:53:35
         compiled from "D:\phpstudy_pro\WWW\pc_release\includes\runtime/../../layouts/vlayout\modules\Accounts\ListViewHeader.tpl" */ ?>
<?php /*%%SmartyHeaderCode:198216209ee5f2c1cb7-09814494%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'be918724badbf47217b8ae795fa113eb93ac99ea' => 
    array (
      0 => 'D:\\phpstudy_pro\\WWW\\pc_release\\includes\\runtime/../../layouts/vlayout\\modules\\Accounts\\ListViewHeader.tpl',
      1 => 1622524018,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '198216209ee5f2c1cb7-09814494',
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
  'unifunc' => 'content_6209ee5f33ba4',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_6209ee5f33ba4')) {function content_6209ee5f33ba4($_smarty_tpl) {?>
        <?php if ($_GET['filter']=='overt'){?><link href="libraries/icheck/orange.css" rel="stylesheet"><script src="libraries/icheck/icheck.min.js"></script><?php }?><?php if ($_GET['filter']=='temporary'){?><link href="libraries/icheck/orange.css" rel="stylesheet"><script src="libraries/icheck/icheck.min.js"></script><?php }?><div class="listViewPageDiv"><?php if (!isset($_GET['report'])){?><div class="listViewTopMenuDiv noprint"><div class="listViewActionsDiv row-fluid hide"><span class="btn-toolbar span4"><span style="display:none;"></span><span class="<?php if (empty($_GET['public'])==false){?>hide<?php }?>"><span class="btn-group listViewMassActions"></span><?php  $_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['LISTVIEW_LINKS']->value['LISTVIEWBASIC']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->key => $_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value){
$_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->_loop = true;
?><span class="btn-group"><button id="<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
_listView_basicAction_<?php echo Vtiger_Util_Helper::replaceSpaceWithUnderScores($_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value->getLabel());?>
" class="btn addButton" <?php if (stripos($_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value->getUrl(),'javascript:')===0){?> onclick='<?php echo substr($_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value->getUrl(),strlen("javascript:"));?>
;'<?php }else{ ?> onclick='window.location.href="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value->getUrl();?>
"'<?php }?>><i class="icon-plus icon-white"></i>&nbsp;<strong><?php echo vtranslate($_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value->getLabel(),$_smarty_tpl->tpl_vars['MODULE']->value);?>
</strong></button></span><?php } ?></span></span><span class="btn-toolbar span4"><span class="customFilterMainSpan btn-group"></span></span></div></div><?php }?><div><?php if (!isset($_GET['report'])){?><form method="post"  name="SearchBug" id="SearchBug"><input type="hidden" value="1" id="queryaction" name="queryaction"><input type="hidden" value="" id="queryTitle" name="queryTitle"><input type="hidden" value="0" id="saveQuery" name="saveQuery"><input type="hidden" value="0" id="reset" name="reset"><input type="hidden" value="" id="showField" name="showField"><div id="SearchBlankCover" style="background-color: #F0F0F0;"><table id="searchtable" style="margin:auto"><tbody><tr class="SearchConditionRow" id="SearchConditionRow0" style="height:22px;"><td><input type="hidden" value="" name="BugFreeQuery[leftParenthesesName0]"id="BugFreeQuery_leftParenthesesName0"></td><td><select id="BugFreeQuery_field0" style="width:100%;color:#878787;" name="BugFreeQuery[field0]"><option value="department" selected="selected">部门</option></select></td><td><select id="BugFreeQuery_operator0" style="width:100%;color:#878787;"name="BugFreeQuery[operator0]"><option value="UNDER" selected="selected">等于</option></select></td><td><?php $_smarty_tpl->tpl_vars['Department'] = new Smarty_variable(getDepartmentNameAndParentName(), null, 0);?><select id="DepartFilter" name="department"  class="chzn-select"><?php  $_smarty_tpl->tpl_vars["departname"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["departname"]->_loop = false;
 $_smarty_tpl->tpl_vars["departmentid"] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['Department']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["departname"]->key => $_smarty_tpl->tpl_vars["departname"]->value){
$_smarty_tpl->tpl_vars["departname"]->_loop = true;
 $_smarty_tpl->tpl_vars["departmentid"]->value = $_smarty_tpl->tpl_vars["departname"]->key;
?><option value="<?php echo $_smarty_tpl->tpl_vars['departmentid']->value;?>
" title="<?php echo $_smarty_tpl->tpl_vars['departname']->value['parentdepartmentname'];?>
"><?php echo $_smarty_tpl->tpl_vars['departname']->value['departmentname'];?>
</option><?php } ?></select></td><td><input type="hidden" value="" name="BugFreeQuery[rightParenthesesName0]"id="BugFreeQuery_rightParenthesesName0"></td><td><select id="BugFreeQuery_andor0" style="width:65px;color:#878787;" name="BugFreeQuery[andor0]"><option value="And" selected="selected">并且</option></select></td><td><a class="add_search_button" href="javascript:addSearchField(0);"><img src="layouts/vlayout/skins/softed/images/add_search.gif"></a></td></tr><tr><td colspan="7"><center><input type="button"value="提交查询" id="PostQuery" name="PostQuery" class="btn"><input type="button" onclick="setSearchConditionOrder();$('#save_query_dialog').dialog('open'); return false;"value="保存查询" id="SaveQuery" name="SaveQuery" class="btn hide"><input type="button" onclick="location.reload();"value="重置查询" class="btn"></center></td></tr></tbody></table><?php echo $_smarty_tpl->getSubTemplate (vtemplate_path('SearchJS.tpl',$_smarty_tpl->tpl_vars['MODULE']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('MODULE'=>$_smarty_tpl->tpl_vars['MODULE']->value), 0);?>
</form></div><?php echo $_smarty_tpl->getSubTemplate (vtemplate_path('DefaultListFields.tpl'), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('MODULE'=>$_smarty_tpl->tpl_vars['MODULE']->value,'ISDEFAULT'=>true,'ISPAGE'=>true), 0);?>
<?php }?><div class="listViewContentDiv" id="listViewContents"><?php }} ?>