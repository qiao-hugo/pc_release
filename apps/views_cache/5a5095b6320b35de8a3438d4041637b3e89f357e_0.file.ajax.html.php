<?php
/* Smarty version 3.1.28-dev/79, created on 2017-12-19 23:15:34
  from "/data/httpd/vtigerCRM/apps/views/SalesDaily/ajax.html" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.28-dev/79',
  'unifunc' => 'content_5a392d16162ce2_62087102',
  'file_dependency' => 
  array (
    '5a5095b6320b35de8a3438d4041637b3e89f357e' => 
    array (
      0 => '/data/httpd/vtigerCRM/apps/views/SalesDaily/ajax.html',
      1 => 1486367357,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a392d16162ce2_62087102 ($_smarty_tpl) {
$_from = $_smarty_tpl->tpl_vars['list']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_wlist_0_saved_item = isset($_smarty_tpl->tpl_vars['value']) ? $_smarty_tpl->tpl_vars['value'] : false;
$_smarty_tpl->tpl_vars['value'] = new Smarty_Variable();
$__foreach_wlist_0_total = $_smarty_tpl->smarty->ext->_foreach->count($_from);
if ($__foreach_wlist_0_total) {
foreach ($_from as $_smarty_tpl->tpl_vars['value']->value) {
$__foreach_wlist_0_saved_local_item = $_smarty_tpl->tpl_vars['value'];
?>


<li>
                        <a href="index.php?module=SalesDaily&action=one&id=<?php echo $_smarty_tpl->tpl_vars['value']->value['salesdailybasicid'];?>
" class="ui-btn ui-btn-icon-right ui-icon-carat-r">
                        <span style="font-size:12px;">
                            <?php echo $_smarty_tpl->tpl_vars['value']->value['smownerid'];?>
&nbsp;&nbsp;<?php echo $_smarty_tpl->tpl_vars['value']->value['dailydatetime'];?>
</span>
                        </a>
                    </li>

<?php
$_smarty_tpl->tpl_vars['value'] = $__foreach_wlist_0_saved_local_item;
}
}
if ($__foreach_wlist_0_saved_item) {
$_smarty_tpl->tpl_vars['value'] = $__foreach_wlist_0_saved_item;
}
?>

<!-- <li>
    <a href="#">
    <span style="font-size:12px;">
        <?php echo $_smarty_tpl->tpl_vars['value']->value['smownerid'];?>
&nbsp;&nbsp;<?php echo $_smarty_tpl->tpl_vars['value']->value['dailydatetime'];?>
</span>
    </a>
</li> --><?php }
}
