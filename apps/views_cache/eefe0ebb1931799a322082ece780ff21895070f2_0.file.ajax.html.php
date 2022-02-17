<?php
/* Smarty version 3.1.28-dev/79, created on 2018-02-24 16:35:44
  from "/data/httpd/vtigerCRM/apps/views/ContractsAgreement/ajax.html" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.28-dev/79',
  'unifunc' => 'content_5a9123e0e283d5_29783395',
  'file_dependency' => 
  array (
    'eefe0ebb1931799a322082ece780ff21895070f2' => 
    array (
      0 => '/data/httpd/vtigerCRM/apps/views/ContractsAgreement/ajax.html',
      1 => 1515739088,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a9123e0e283d5_29783395 ($_smarty_tpl) {
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
    <a data-ajax="false" data-transition="slide" href="/index.php?module=ServiceContracts&action=one&id=<?php echo $_smarty_tpl->tpl_vars['value']->value['servicecontractsid'];?>
" class="ui-btn ui-btn-icon-right ui-icon-carat-r">
        <img  style="top:10px;height:80px;width:80px;border: 1px solid #eee;border-radius:80px;overflow: hidden;" src="<?php if (isset($_smarty_tpl->tpl_vars['USERIMGS']->value[$_smarty_tpl->tpl_vars['IMGMD']->value])) {
echo $_smarty_tpl->tpl_vars['USERIMGS']->value[$_smarty_tpl->tpl_vars['IMGMD']->value];
} else { ?>../../static/img/trueland.png<?php }?>">
        <h2><?php echo $_smarty_tpl->tpl_vars['value']->value['contract_no'];?>
 &nbsp;</h2>
        <p>领: <?php echo $_smarty_tpl->tpl_vars['value']->value['smownerid'];?>
 &nbsp;&nbsp; <?php echo $_smarty_tpl->tpl_vars['value']->value['receivedate'];?>
</p>
        <?php if ($_smarty_tpl->tpl_vars['receiveid']->value) {?>
            <p>提: <?php echo $_smarty_tpl->tpl_vars['value']->value['receiveid'];?>
 <?php echo $_smarty_tpl->tpl_vars['value']->value['returndate'];?>
</p>
        <?php }?>
        <p>待审核节点:【<?php echo $_smarty_tpl->tpl_vars['value']->value['workflowsnode'];?>
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
