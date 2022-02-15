<?php
/* Smarty version 3.1.28-dev/79, created on 2018-01-28 18:56:41
  from "/data/httpd/vtigerCRM/apps/views/VisitingOrder/ajax_my_account_list.html" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.28-dev/79',
  'unifunc' => 'content_5a6dac69a54ec0_38445776',
  'file_dependency' => 
  array (
    'c24cd0093ddf2a97b2bcda167f220d57e5c0a29b' => 
    array (
      0 => '/data/httpd/vtigerCRM/apps/views/VisitingOrder/ajax_my_account_list.html',
      1 => 1516969158,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a6dac69a54ec0_38445776 ($_smarty_tpl) {
$_from = $_smarty_tpl->tpl_vars['list']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_value_0_saved_item = isset($_smarty_tpl->tpl_vars['value']) ? $_smarty_tpl->tpl_vars['value'] : false;
$_smarty_tpl->tpl_vars['value'] = new Smarty_Variable();
$__foreach_value_0_total = $_smarty_tpl->smarty->ext->_foreach->count($_from);
if ($__foreach_value_0_total) {
foreach ($_from as $_smarty_tpl->tpl_vars['value']->value) {
$__foreach_value_0_saved_local_item = $_smarty_tpl->tpl_vars['value'];
?>
                    	<li class="fix">
                            <a href="#" class="fl">
                                <div class="list"><?php echo $_smarty_tpl->tpl_vars['value']->value['accountname'];?>
</div>
                            </a>
                             <div class="fr right" data-toggle="modal" data-target="#myModal2" onclick='opendl(<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
,<?php echo $_smarty_tpl->tpl_vars['value']->value['accountid'];?>
)'>+</div>
                        </li>
						<?php
$_smarty_tpl->tpl_vars['value'] = $__foreach_value_0_saved_local_item;
}
} else {
?>
						<?php
}
if ($__foreach_value_0_saved_item) {
$_smarty_tpl->tpl_vars['value'] = $__foreach_value_0_saved_item;
}
}
}
