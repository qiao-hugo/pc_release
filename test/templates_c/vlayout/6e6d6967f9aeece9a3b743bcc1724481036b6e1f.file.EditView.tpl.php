<?php /* Smarty version Smarty-3.1.7, created on 2022-02-15 11:41:23
         compiled from "D:\phpstudy_pro\WWW\pc_release\includes\runtime/../../layouts/vlayout\modules\ServiceContracts\EditView.tpl" */ ?>
<?php /*%%SmartyHeaderCode:410620b20e3618457-45578276%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6e6d6967f9aeece9a3b743bcc1724481036b6e1f' => 
    array (
      0 => 'D:\\phpstudy_pro\\WWW\\pc_release\\includes\\runtime/../../layouts/vlayout\\modules\\ServiceContracts\\EditView.tpl',
      1 => 1630399635,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '410620b20e3618457-45578276',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'is_collate' => 0,
    'MODULE' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_620b20e365b06',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_620b20e365b06')) {function content_620b20e365b06($_smarty_tpl) {?>
<?php if ($_smarty_tpl->tpl_vars['is_collate']->value){?>
<?php echo $_smarty_tpl->getSubTemplate (vtemplate_path("CollateEditViewBlocks.tpl",$_smarty_tpl->tpl_vars['MODULE']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php }else{ ?>
<?php echo $_smarty_tpl->getSubTemplate (vtemplate_path("EditViewBlocks.tpl",$_smarty_tpl->tpl_vars['MODULE']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php }?>
<?php echo $_smarty_tpl->getSubTemplate (vtemplate_path("EditViewActions.tpl",$_smarty_tpl->tpl_vars['MODULE']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<?php }} ?>