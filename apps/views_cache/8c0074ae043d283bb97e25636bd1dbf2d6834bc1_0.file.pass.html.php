<?php
/* Smarty version 3.1.28-dev/79, created on 2018-02-09 10:21:47
  from "/data/httpd/vtigerCRM/apps/views/VisitingOrder/pass.html" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.28-dev/79',
  'unifunc' => 'content_5a7d05bbda6c94_70323806',
  'file_dependency' => 
  array (
    '8c0074ae043d283bb97e25636bd1dbf2d6834bc1' => 
    array (
      0 => '/data/httpd/vtigerCRM/apps/views/VisitingOrder/pass.html',
      1 => 1516969159,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.html' => 1,
    'file:footer.html' => 1,
  ),
),false)) {
function content_5a7d05bbda6c94_70323806 ($_smarty_tpl) {
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
		
		<?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

        <title>待审核提醒</title>
</head>

<body>
<div class="container-fluid w fix">
        <div class="row">
           
            <div class="to-do-audit">
                <ul>
                    <?php if ($_smarty_tpl->tpl_vars['sum']->value > 0) {?>
                    	<?php
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
    					<li>
                            <div class="list"><?php echo $_smarty_tpl->tpl_vars['value']->value['startdate'];?>
</div>
                            <div class="text">
                                <h5><a href="/index.php?module=VisitingOrder&action=detail&record=<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
" class="fl">
                                    <?php echo $_smarty_tpl->tpl_vars['value']->value['related_to'];?>
</a> <?php echo $_smarty_tpl->tpl_vars['value']->value['contacts'];?>
</h5>
                                <div class="fix">
                                    <div class="fl">提单人：<?php echo $_smarty_tpl->tpl_vars['value']->value['extractid'];?>
</div>
                                    
                                    <div class="fl">拜访目的：<?php echo $_smarty_tpl->tpl_vars['value']->value['purpose'];?>
</div>
                                </div>
                            </div>
                        </li>
    					<?php
$_smarty_tpl->tpl_vars['value'] = $__foreach_value_0_saved_local_item;
}
} else {
?>
    							没有拜访单 
    					<?php
}
if ($__foreach_value_0_saved_item) {
$_smarty_tpl->tpl_vars['value'] = $__foreach_value_0_saved_item;
}
?>
                    <?php } else { ?>
                        无审核内容
                    <?php }?>
                </ul>
            </div>
            <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

        </div>

</div>


</body>
</html><?php }
}
