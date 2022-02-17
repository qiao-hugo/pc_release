<?php /* Smarty version Smarty-3.1.7, created on 2022-02-16 10:13:04
         compiled from "D:\phpstudy_pro\WWW\pc_release\includes\runtime/../../layouts/vlayout\modules\Settings\Profiles\ListViewHeader.tpl" */ ?>
<?php /*%%SmartyHeaderCode:24661620c5db0661b53-35663355%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8f3389c7b48400a98913d68022088f2db9453913' => 
    array (
      0 => 'D:\\phpstudy_pro\\WWW\\pc_release\\includes\\runtime/../../layouts/vlayout\\modules\\Settings\\Profiles\\ListViewHeader.tpl',
      1 => 1564496341,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '24661620c5db0661b53-35663355',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'MODULE' => 0,
    'QUALIFIED_MODULE' => 0,
    'LISTVIEW_LINKS' => 0,
    'LISTVIEW_BASICACTION' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_620c5db069bba',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_620c5db069bba')) {function content_620c5db069bba($_smarty_tpl) {?>
<div class="container-fluid"><div class="widget_header row-fluid"><h3><?php echo vtranslate($_smarty_tpl->tpl_vars['MODULE']->value,$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</h3></div><hr><div class="row-fluid"><span class="span6 btn-toolbar"><?php  $_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['LISTVIEW_LINKS']->value['LISTVIEWBASIC']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->key => $_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value){
$_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->_loop = true;
?><button class="btn addButton" <?php if (stripos($_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value->getUrl(),'javascript:')===0){?> onclick='<?php echo substr($_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value->getUrl(),strlen("javascript:"));?>
;'<?php }else{ ?> onclick='window.location.href="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value->getUrl();?>
"' <?php }?>><i class="icon-plus icon-white"></i>&nbsp;<strong><?php echo vtranslate('LBL_ADD_RECORD',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</strong></button><?php } ?></span></div><div id="showSearch1" ><div class="control-group margin0px"><select name="searchtype" id="searchtype" style="width:120px;"><option value="profilename">配置分组名称</option></select><input type="text" placeholder="分组名称" id="searchvalue"><span class="paddingLeft10px cursorPointer help-inline" id="userSearchButton"><img src="layouts/vlayout/skins/softed/images/search.png" alt="搜索按钮" title="搜索按钮"></span><span><span class="pull-right">&nbsp;<input type="text" name="jumppage" value="" id="jumppage" class="input-small" style="width: 50px;" placeholder="跳转">&nbsp;</span><span class="pagination pull-right" id="pagination"><ul class="pagination-demo"></ul></span></div></div><div class="clearfix"></div><div class="listViewContentDiv" id="listViewContents"><?php }} ?>