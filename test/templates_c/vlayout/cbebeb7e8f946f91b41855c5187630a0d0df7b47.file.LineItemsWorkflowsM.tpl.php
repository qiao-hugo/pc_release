<?php /* Smarty version Smarty-3.1.7, created on 2022-02-15 16:27:47
         compiled from "D:\phpstudy_pro\WWW\pc_release\includes\runtime/../../layouts/vlayout\modules\Vtiger\LineItemsWorkflowsM.tpl" */ ?>
<?php /*%%SmartyHeaderCode:23225620b6403d1c603-14160155%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'cbebeb7e8f946f91b41855c5187630a0d0df7b47' => 
    array (
      0 => 'D:\\phpstudy_pro\\WWW\\pc_release\\includes\\runtime/../../layouts/vlayout\\modules\\Vtiger\\LineItemsWorkflowsM.tpl',
      1 => 1523874473,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '23225620b6403d1c603-14160155',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'RECORD' => 0,
    'ModuleName' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_620b6403db1a6',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_620b6403db1a6')) {function content_620b6403db1a6($_smarty_tpl) {?>
<input id="recordid" type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['RECORD']->value->getId();?>
" /><div class="widgetContainer_workflows" data-url="module=<?php echo $_smarty_tpl->tpl_vars['ModuleName']->value;?>
&amp;view=Detail&amp;record=<?php echo $_smarty_tpl->tpl_vars['RECORD']->value->getId();?>
&amp;mode=getWorkflows&amp;page=1&amp;limit=5" data-name="SalesorderWorkflowStages"><div class="widget_contents"></div></div></br><?php }} ?>