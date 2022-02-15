<?php /* Smarty version Smarty-3.1.7, created on 2022-02-15 11:32:13
         compiled from "D:\phpstudy_pro\WWW\pc_release\includes\runtime/../../layouts/vlayout\modules\Workflows\ListViewHeader.tpl" */ ?>
<?php /*%%SmartyHeaderCode:8565620b1ebd06f9a8-35633596%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c5275135d6c55e5bcd8cab7c76e14fb0509cc7ec' => 
    array (
      0 => 'D:\\phpstudy_pro\\WWW\\pc_release\\includes\\runtime/../../layouts/vlayout\\modules\\Workflows\\ListViewHeader.tpl',
      1 => 1604739553,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '8565620b1ebd06f9a8-35633596',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'LISTVIEW_LINKS' => 0,
    'MODULE' => 0,
    'LISTVIEW_BASICACTION' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_620b1ebd0d8cf',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_620b1ebd0d8cf')) {function content_620b1ebd0d8cf($_smarty_tpl) {?>
<div class="listViewPageDiv"><?php if (!isset($_GET['filter'])){?><div class="listViewTopMenuDiv noprint"><div class="listViewActionsDiv row-fluid"><span class="btn-toolbar span4"><span class="<?php if (empty($_GET['public'])==false){?>hide<?php }?>"><?php  $_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['LISTVIEW_LINKS']->value['LISTVIEWBASIC']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->key => $_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value){
$_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->_loop = true;
?><span class="btn-group"><button id="<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
_listView_basicAction_<?php echo Vtiger_Util_Helper::replaceSpaceWithUnderScores($_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value->getLabel());?>
" class="btn addButton" <?php if (stripos($_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value->getUrl(),'javascript:')===0){?> onclick='<?php echo substr($_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value->getUrl(),strlen("javascript:"));?>
;'<?php }else{ ?> onclick='window.location.href="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value->getUrl();?>
"'<?php }?>><i class="icon-plus icon-white"></i>&nbsp;<strong><?php echo vtranslate($_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value->getLabel(),$_smarty_tpl->tpl_vars['MODULE']->value);?>
</strong></button></span><?php } ?></span></span><span class="btn-toolbar span4"><span class="customFilterMainSpan btn-group"></span></span></div></div><?php }?><div><?php if (!isset($_GET['filter'])){?><form method="post"  name="SearchBug" id="SearchBug"><input type="hidden" value="1" id="queryaction" name="queryaction"><input type="hidden" value="" id="queryTitle" name="queryTitle"><input type="hidden" value="0" id="saveQuery" name="saveQuery"><input type="hidden" value="0" id="reset" name="reset"><input type="hidden" value="" id="showField" name="showField"><div id="SearchBlankCover" style="background-color: #F0F0F0;"><table id="searchtable" style="margin:auto"><tbody><tr><td colspan="7"><center><input type="button"value="提交查询" id="PostQuery" name="PostQuery" class="btn"><input type="button" onclick="setSearchConditionOrder();$('#save_query_dialog').dialog('open'); return false;"value="保存查询" id="SaveQuery" name="SaveQuery" class="btn hide"><input type="button" onclick="location.reload();"value="重置查询" class="btn"></center></td></tr></tbody></table><?php echo $_smarty_tpl->getSubTemplate (vtemplate_path('SearchJS.tpl'), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('MODULE'=>$_smarty_tpl->tpl_vars['MODULE']->value), 0);?>
</form></div><?php echo $_smarty_tpl->getSubTemplate (vtemplate_path('DefaultListFields.tpl'), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('MODULE'=>$_smarty_tpl->tpl_vars['MODULE']->value,'ISDEFAULT'=>true,'ISPAGE'=>true), 0);?>
<?php }?><div class="listViewContentDiv" id="listViewContents"><?php }} ?>