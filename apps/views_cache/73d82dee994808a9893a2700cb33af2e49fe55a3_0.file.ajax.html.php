<?php
/* Smarty version 3.1.28-dev/79, created on 2017-12-20 09:50:18
  from "/data/httpd/vtigerCRM/apps/views/RefillApplication/ajax.html" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.28-dev/79',
  'unifunc' => 'content_5a39c1da1eabb0_82038601',
  'file_dependency' => 
  array (
    '73d82dee994808a9893a2700cb33af2e49fe55a3' => 
    array (
      0 => '/data/httpd/vtigerCRM/apps/views/RefillApplication/ajax.html',
      1 => 1490929008,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a39c1da1eabb0_82038601 ($_smarty_tpl) {
if (!empty($_smarty_tpl->tpl_vars['list']->value)) {
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
    <?php $_smarty_tpl->tpl_vars['IMGMD'] = new Smarty_Variable(md5($_smarty_tpl->tpl_vars['value']->value['email']), null);
$_smarty_tpl->ext->_updateScope->updateScope($_smarty_tpl, 'IMGMD', 0);?>
    <a data-ajax="false" data-transition="slide" href="/index.php?module=RefillApplication&action=one&id=<?php echo $_smarty_tpl->tpl_vars['value']->value['refillapplicationid'];?>
" class="ui-btn ui-btn-icon-right ui-icon-carat-r">
        <img src="<?php if (isset($_smarty_tpl->tpl_vars['USERIMGS']->value[$_smarty_tpl->tpl_vars['IMGMD']->value])) {
echo $_smarty_tpl->tpl_vars['USERIMGS']->value[$_smarty_tpl->tpl_vars['IMGMD']->value];
} else { ?>../../static/img/trueland.png<?php }?>">
        <h2><?php echo $_smarty_tpl->tpl_vars['value']->value['accountid'];?>
</h2><p>【<?php echo $_smarty_tpl->tpl_vars['value']->value['servicecontractsid'];?>
】</p>
    </a></li>
<?php
$_smarty_tpl->tpl_vars['value'] = $__foreach_wlist_0_saved_local_item;
}
}
if ($__foreach_wlist_0_saved_item) {
$_smarty_tpl->tpl_vars['value'] = $__foreach_wlist_0_saved_item;
}
}
echo '<script'; ?>
 type="text/javascript">
$(function(){
	$('#loading').attr("data-totalnum",<?php echo $_smarty_tpl->tpl_vars['totalnum']->value;?>
);
});
<?php echo '</script'; ?>
>

<?php }
}
