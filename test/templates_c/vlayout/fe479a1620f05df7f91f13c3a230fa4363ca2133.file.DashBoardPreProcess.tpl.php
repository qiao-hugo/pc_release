<?php /* Smarty version Smarty-3.1.7, created on 2022-02-14 11:16:43
         compiled from "D:\phpstudy_pro\WWW\pc_release\includes\runtime/../../layouts/vlayout\modules\Home\dashboards\DashBoardPreProcess.tpl" */ ?>
<?php /*%%SmartyHeaderCode:185006209c99b8f8e21-41397975%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'fe479a1620f05df7f91f13c3a230fa4363ca2133' => 
    array (
      0 => 'D:\\phpstudy_pro\\WWW\\pc_release\\includes\\runtime/../../layouts/vlayout\\modules\\Home\\dashboards\\DashBoardPreProcess.tpl',
      1 => 1636426941,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '185006209c99b8f8e21-41397975',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'MODULE' => 0,
    'MENULISTS' => 0,
    'MENU' => 0,
    'SUBMENU' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_6209c99b95540',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_6209c99b95540')) {function content_6209c99b95540($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate (vtemplate_path("HeaderV2.tpl",$_smarty_tpl->tpl_vars['MODULE']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php echo $_smarty_tpl->getSubTemplate (vtemplate_path("BasicHeaderV2.tpl",$_smarty_tpl->tpl_vars['MODULE']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<div class="main-con"><!-- 左侧菜单栏 --><div class="layer19 flex-col"><?php  $_smarty_tpl->tpl_vars['MENU'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['MENU']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['MENULISTS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['MENU']->key => $_smarty_tpl->tpl_vars['MENU']->value){
$_smarty_tpl->tpl_vars['MENU']->_loop = true;
?><div class="bd flex-row"><img class="menu-icon" src="libraries/v2/img/<?php echo $_smarty_tpl->tpl_vars['MENU']->value['icon'];?>
"/><span class="menu-txt"><?php echo $_smarty_tpl->tpl_vars['MENU']->value['name'];?>
</span><i class="arrow-right arrow flex-col"></i><div class="menu-list"><div class="menu-main-con flex-col"><div class="menu-item"><?php  $_smarty_tpl->tpl_vars['SUBMENU'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['SUBMENU']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['MENU']->value['children']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['SUBMENU']->key => $_smarty_tpl->tpl_vars['SUBMENU']->value){
$_smarty_tpl->tpl_vars['SUBMENU']->_loop = true;
?><a href="<?php echo $_smarty_tpl->tpl_vars['SUBMENU']->value['url'];?>
"><?php echo $_smarty_tpl->tpl_vars['SUBMENU']->value['name'];?>
</a><?php } ?></div></div></div></div><?php } ?></div><?php }} ?>