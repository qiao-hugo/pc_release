<?php /* Smarty version Smarty-3.1.7, created on 2022-02-16 10:36:39
         compiled from "D:\phpstudy_pro\WWW\pc_release\includes\runtime/../../layouts/vlayout\modules\Settings\Roles\RoleTree.tpl" */ ?>
<?php /*%%SmartyHeaderCode:7428620c6337968f54-08548288%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b358edf0ffb24730d64daa649e486ad09c0a917a' => 
    array (
      0 => 'D:\\phpstudy_pro\\WWW\\pc_release\\includes\\runtime/../../layouts/vlayout\\modules\\Settings\\Roles\\RoleTree.tpl',
      1 => 1575293397,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '7428620c6337968f54-08548288',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'ROLE' => 0,
    'CHILD_ROLE' => 0,
    'GETCATEGORYARR' => 0,
    'QUALIFIED_MODULE' => 0,
    'SOURCE_ROLE' => 0,
    'SOURCE_ROLE_SUBPATTERN' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_620c63379e11c',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_620c63379e11c')) {function content_620c63379e11c($_smarty_tpl) {?>
<ul><?php $_smarty_tpl->tpl_vars["GETCATEGORYARR"] = new Smarty_variable(array(), null, 0);?><?php  $_smarty_tpl->tpl_vars['CHILD_ROLE'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['CHILD_ROLE']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['ROLE']->value->getChildren(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['CHILD_ROLE']->key => $_smarty_tpl->tpl_vars['CHILD_ROLE']->value){
$_smarty_tpl->tpl_vars['CHILD_ROLE']->_loop = true;
?><?php if ($_smarty_tpl->tpl_vars['CHILD_ROLE']->value->getDepth()==1&&!in_array($_smarty_tpl->tpl_vars['CHILD_ROLE']->value->getcategory(),$_smarty_tpl->tpl_vars['GETCATEGORYARR']->value)){?><?php $_smarty_tpl->createLocalArrayVariable('GETCATEGORYARR', null, 0);
$_smarty_tpl->tpl_vars['GETCATEGORYARR']->value[] = $_smarty_tpl->tpl_vars['CHILD_ROLE']->value->getcategory();?><li data-role="<?php echo $_smarty_tpl->tpl_vars['CHILD_ROLE']->value->getParentRoleString();?>
"><img src="/layouts/vlayout/images/right_triangle.png" class="icon-eye-open-right open<?php echo $_smarty_tpl->tpl_vars['CHILD_ROLE']->value->getcategory();?>
" title="显示" style="cursor:pointer;display:none;max-width:5%" data-id="<?php echo $_smarty_tpl->tpl_vars['CHILD_ROLE']->value->getcategory();?>
"><img src="/layouts/vlayout/images/lower_triangle.png" class="icon-eye-close-lower close<?php echo $_smarty_tpl->tpl_vars['CHILD_ROLE']->value->getcategory();?>
" title="隐藏" style="cursor:pointer;max-width:5%" data-id="<?php echo $_smarty_tpl->tpl_vars['CHILD_ROLE']->value->getcategory();?>
"><?php echo vtranslate($_smarty_tpl->tpl_vars['CHILD_ROLE']->value->getcategory(),"Settings:Roles");?>
<div <?php if ($_REQUEST['view']!='Popup'){?>class="toolbar-handle"<?php }?>><?php if ($_REQUEST['view']!='Popup'){?><div class="toolbar">&nbsp;<a href="<?php echo $_smarty_tpl->tpl_vars['CHILD_ROLE']->value->getCreateChildUrl();?>
"  data-url="<?php echo $_smarty_tpl->tpl_vars['CHILD_ROLE']->value->getCreateChildUrl();?>
" title="<?php echo vtranslate('LBL_ADD_RECORD',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
"><span class="icon-plus-sign"></span></a></div><?php }?></div></li><?php }?><li data-role="<?php echo $_smarty_tpl->tpl_vars['CHILD_ROLE']->value->getParentRoleString();?>
" data-roleid="<?php echo $_smarty_tpl->tpl_vars['CHILD_ROLE']->value->getId();?>
" <?php if ($_smarty_tpl->tpl_vars['CHILD_ROLE']->value->getDepth()==1){?>style="padding-left:25px;"<?php }?> class="<?php echo $_smarty_tpl->tpl_vars['CHILD_ROLE']->value->getcategory();?>
"><div <?php if ($_REQUEST['view']!='Popup'){?>class="toolbar-handle"<?php }?>><?php if ($_REQUEST['type']=='Transfer'){?><?php $_smarty_tpl->tpl_vars["SOURCE_ROLE_SUBPATTERN"] = new Smarty_variable(('::').($_smarty_tpl->tpl_vars['SOURCE_ROLE']->value->getId()), null, 0);?><?php if (strpos($_smarty_tpl->tpl_vars['CHILD_ROLE']->value->getParentRoleString(),$_smarty_tpl->tpl_vars['SOURCE_ROLE_SUBPATTERN']->value)!==false){?><?php echo $_smarty_tpl->tpl_vars['CHILD_ROLE']->value->getName();?>
<?php }else{ ?><a href="<?php echo $_smarty_tpl->tpl_vars['CHILD_ROLE']->value->getEditViewUrl();?>
" data-url="<?php echo $_smarty_tpl->tpl_vars['CHILD_ROLE']->value->getEditViewUrl();?>
" class="btn roleEle" rel="tooltip" ><?php echo $_smarty_tpl->tpl_vars['CHILD_ROLE']->value->getName();?>
</a><?php }?><?php }else{ ?><a href="<?php echo $_smarty_tpl->tpl_vars['CHILD_ROLE']->value->getEditViewUrl();?>
" data-url="<?php echo $_smarty_tpl->tpl_vars['CHILD_ROLE']->value->getEditViewUrl();?>
" class="btn draggable droppable" rel="tooltip" title="<?php echo vtranslate('LBL_CLICK_TO_EDIT_OR_DRAG_TO_MOVE',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
"><?php echo $_smarty_tpl->tpl_vars['CHILD_ROLE']->value->getName();?>
</a><?php }?><?php if ($_REQUEST['view']!='Popup'){?><div class="toolbar">&nbsp;<a href="<?php echo $_smarty_tpl->tpl_vars['CHILD_ROLE']->value->getCreateChildUrl();?>
" data-url="<?php echo $_smarty_tpl->tpl_vars['CHILD_ROLE']->value->getCreateChildUrl();?>
" title="<?php echo vtranslate('LBL_ADD_RECORD',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
"><span class="icon-plus-sign"></span></a>&nbsp;<a data-id="<?php echo $_smarty_tpl->tpl_vars['CHILD_ROLE']->value->getId();?>
" href="javascript:;" data-url="<?php echo $_smarty_tpl->tpl_vars['CHILD_ROLE']->value->getDeleteActionUrl();?>
" data-action="modal" title="<?php echo vtranslate('LBL_DELETE',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
"><span class="icon-trash"></span></a></div><?php }?></div><?php $_smarty_tpl->tpl_vars["ROLE"] = new Smarty_variable($_smarty_tpl->tpl_vars['CHILD_ROLE']->value, null, 0);?><?php echo $_smarty_tpl->getSubTemplate (vtemplate_path("RoleTree.tpl","Settings:Roles"), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
</li><?php } ?></ul><?php }} ?>